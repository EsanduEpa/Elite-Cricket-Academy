# Slots Module — Comprehensive Design Document (v2)
**Elite Cricket Academy**
**Date:** April 6, 2026
**Scope:** Sessions / Slots system redesign — full cross-system revision
**Based on schema:** `cricket_academy (10).sql`
**Revision reason:** v1 covered only coach sessions. This version covers all 5 roles, trainer-led sessions, subscription entitlement gating, medical clearance flags, the shop employee counter-booking workflow, and a full audit trail.

---

## 1. Problem Statement

The current `session` table has structural limitations that prevent the system from operating safely and fully:

| Problem | Current Table | Impact |
|---|---|---|
| One coach per session | `session.CoachOrTrainerID` — single column | Cannot record two coaches on one program |
| No recurrence pattern | `session.IsRecurring` is a boolean; `Date` is a single day | Cancelling one Monday cancels the whole program |
| Coaches and Trainers share one column | `session.CoachOrTrainerID` | No type distinction between coaching and physical training session |
| Facility is freetext | `sessiondetails.FacilityType` varchar | Not linked to `facility` table; no double-booking prevention |
| No fixed time bands | Arbitrary `StartTime`/`EndTime` on every row | Inconsistent data; impossible to query "all 3pm sessions" reliably |
| No subscription gate | Nothing links `session` to `playersubscription` or `membershipplan` | Expired-subscription players can attend for free undetected |
| No medical safety check | Nothing links sessions to `playermedicalrecord` | Injured players can book facilities or sessions with no warning |
| Facility-only bookings disconnected | Separate `facilitybooking` table with no shared conflict constraint | "Is this facility free right now?" requires two separate queries |
| No audit trail | No change log table | Cannot see who cancelled a session, changed a coach, or altered a booking |
| ShopEmployee role not integrated | `facilitybooking.BookedBy` exists but has no equivalent in the session system | Counter bookings are invisible to coaching records |
| Trainer sessions not properly typed | `session.SessionType = 'Physical Training'` exists but trainers are grouped with coaches | No way to distinguish physical training slots from coaching slots |

---

## 2. How This Module Connects to Every Role

This module is the operational heart of the academy. Every role touches it daily. A design flaw here causes cascading failures across finance, medical safety, performance tracking, and user experience.

### 2.1 Admin
Owns the entire slot configuration. The only role that can create templates, assign staff, generate calendar occurrences, and resolve conflicts.

**Reads:** Everything. Full calendar view, all bookings, payment status, attendance summary, audit logs.
**Writes:** Templates, staff assignments, occurrence generation, occurrence cancellation, player enrollment in programs, conflict override.

**Critical safety connections:**
- Must check `playermedicalrecord.RestDaysNeeded` before enrolling a player with an active injury
- Must validate `playersubscription.Status = active` before enrolling a player in any paid or subscription program
- Cancelling an occurrence must auto-insert rows into `notification` for all booked players
- All admin slot actions must be written to `activitylog` and `slot_audit_log`

---

### 2.2 Coach
Sees their own sessions. Cannot create templates or manage facilities.

**Reads:** Upcoming occurrences they are assigned to (via `slot_template_staff` or `slot_occurrence_staff_override`), the enrolled player list for each occurrence (`slot_booking`).
**Writes:** `coachingsession` (performance notes per player per session — the existing detailed log).

**Critical connections:**
- A coach can **only** see the player list for occurrences they are assigned to. They cannot view another coach's private session player details — enforced through the existing `ValidateCoachPlayerPermission` stored procedure.
- Coaches with `coachprofile.IsHeadCoach = 1` can see all sessions across all coaches.
- After a session completes, the coach fills in `coachingsession` performance notes for each player. These notes feed `performanceupdate` → `playeroverallstats`.

---

### 2.3 Trainer
Runs **Physical Training** sessions. Separate staff type from coaches. Currently lumped together in one column — the new design separates them clearly.

**Reads:** Upcoming physical training occurrences they are assigned to, enrolled player list.
**Writes:** Trainers do **not** write `coachingsession` — they use `trainerappointment`, `nutritionplan`, and `supplementplan` for player-specific records.

**Critical connections:**
- A template with `StaffType = trainer` can only be assigned to users with `Role = Trainer`.
- Trainers work with `playertrainerassignment` the same way coaches work with `playercoachassignment`. Slot visibility must match those assignments.
- Physical training slots and coaching slots **can** co-exist in the same facility in the same time band only if the facility's `Capacity` can accommodate both headcounts. The application checks this before generating occurrences; the DB UNIQUE constraint prevents two occurrences of the same type from being put in the same slot+facility+date.

---

### 2.4 Player
The consumer of the slot system. Their experience and safety depend entirely on its correctness.

**Reads:** Available facility-only slots, available private coaching slots, own enrolled program sessions, booking history, cancellation status.
**Writes:** `slot_booking` (self-bookings only for `facility_only` and `private` slot types).

**Critical rules — non-negotiable:**

1. **Subscription gate:** Before any program enrollment, confirm `playersubscription.Status = active` and verify the player's `membershipplan` covers the slot type (`SessionsPerWeek`, `PrivateSessionsIncluded`, `FacilityAccessIncluded`).
2. **Medical flag:** Before confirming any booking, query `playermedicalrecord` for records where `RecoveryStatus IN ('ongoing','recovering')` AND the player's `InjuryDate + RestDaysNeeded >= CURDATE()`. If found, show a warning and require admin override stored in `MedicalClearedBy`.
3. **Capacity check:** Booking count for an occurrence must be less than `MaxParticipants` before accepting a new booking.
4. **No duplicate booking:** DB UNIQUE constraint on `(OccurrenceID, PlayerID)` in `slot_booking`.
5. **Cancellation window:** Players may cancel self-bookings up to 24 hours before the session. After that, the cancellation is recorded but no refund is issued automatically.

---

### 2.5 ShopEmployee
The **counter operator**. Processes walk-in players who want to book a facility or pay for a session at the front desk. Does not configure slots; consumes availability data.

**Reads:** All upcoming occurrences with available capacity (to tell a walk-in what is free), a player's existing bookings, facility availability.
**Writes:** `slot_booking` with `BookingSource = shop_employee` on behalf of players. Records `PaymentMethod = cash` at the counter and sets `PaymentStatus = paid`.

**Critical connections:**
- ShopEmployee **cannot** enroll players in subscription-covered program sessions — that is Admin only.
- ShopEmployee **can** book `facility_only` and `private` slots (walk-in counter booking).
- ShopEmployee must see the medical flag warning but **cannot** override it — they must direct the player to the admin.
- All counter bookings are logged to `activitylog`.

---

## 3. Why So Many Tables? — A Real-Life Scenario

> Follow one Monday through the life of the academy to see exactly why each table exists and what breaks without it.

---

### The Story: Monday, April 13, 2026

---

#### 7:30 AM — Admin sets up the academy's weekly schedule (done once at season start)

Admin Priya creates **"U15 Batting Practice — every Monday, 3 PM to 5 PM, Main Ground"**.

> **`slot_time_band` is needed here.**
> Priya picks from a dropdown: *"03:00 PM – 05:00 PM"*. That dropdown is powered by `slot_time_band`. Without it every form uses different spellings — "3pm", "15:00", "3:00 PM" — and date-range queries break silently.

> **`slot_template` is needed here.**
> Priya is declaring that *this program exists every Monday*, not just today. Without the template, she would have to create 52 individual records for every Monday of the year. One typo would move a session to the wrong facility. The template is the single source of truth.

---

#### 7:45 AM — Admin assigns a coach AND a trainer to the U15 program

The program needs Coach Sarath as lead cricket coach and Trainer Nimal running the warm-up block.

> **`slot_template_staff` is needed here (not just for coaches).**
> `session.CoachOrTrainerID` holds exactly one person. You cannot put both Sarath and Nimal in one column. `slot_template_staff` gives each person their own row: Sarath = `StaffType=coach, StaffRole=lead`, Nimal = `StaffType=trainer, StaffRole=assistant`. Without this separation, the academy cannot record that a coach and a trainer co-run a session — a reality at every organised program.

---

#### 8:00 AM — Admin generates April's calendar

Priya clicks *"Generate occurrences for April 2026"*. The system reads `DayOfWeek = 1 (Monday)` and creates four rows: Apr 6, 13, 20, 27.

> **`slot_occurrence` is needed here.**
> The template says "every Monday" but the calendar needs real dates. `slot_occurrence` converts the rule into rows the timetable can display and players can book. The template is the repeating alarm setting — `slot_occurrence` is each individual alarm that rings. Without it, "cancel April 13 due to a holiday" would delete the entire Monday program for the rest of the year.

---

#### 8:30 AM — Admin enrolls 14 U15 players. The system checks subscriptions first.

Before enrolling, the system checks `playersubscription.Status = active` and `membershipplan.SessionsPerWeek >= 2`. Player Ashan passes. Player Bimal's subscription expired — the system blocks his enrollment and notifies Priya.

> **`slot_booking` gated by `playersubscription` is needed here.**
> Without this check, any player whose membership lapsed in January is still attending every Monday in April for free. The academy loses revenue and has no record of the gap. With the gate, the system catches it immediately.

---

#### 9:00 AM — Coach Ravi (assistant) calls in sick for April 13 only

Priya arranges Coach Dilshan as substitute just for April 13.

> **`slot_occurrence_staff_override` is needed here.**
> If Priya edited `slot_template_staff` to replace Ravi with Dilshan, every future Monday would also lose Ravi permanently. `slot_occurrence_staff_override` lets her record: *"For April 13 only, Dilshan replaces Ravi."* April 20, 27, and beyond still show Ravi as per the template. Without this table there is no way to handle a one-day substitution without corrupting the whole season.

---

#### 9:30 AM — Player Swairi's booking is flagged for an active injury

Swairi had a wrist fracture reported April 3 with `RestDaysNeeded = 14`. On April 13 she is still within her rest window. When admin tries to confirm her booking, the system reads `playermedicalrecord` and shows: *"Swairi has an active injury. 4 rest days remaining."* Admin defers her booking.

> **The medical check on `slot_booking` is a safety-critical rule.**
> Without it, an injured player physically shows up to a batting session and worsens her injury on academy premises — creating a legal liability. The `playermedicalrecord` table already has `RestDaysNeeded` and `InjuryDate`. The booking process must read this before confirming.

---

#### 11:30 AM — Chamara self-books Practice Net 1 for 1–3 PM

Chamara logs into the player portal. The system checks his `membershipplan.FacilityAccessIncluded = 0` (he is on the `general` basic plan). His booking is blocked with: *"Your plan does not include facility self-booking. Upgrade to Private or Pro."*

> **`membershipplan` integration on `slot_template.RequiredPlanFeature` is needed here.**
> Without this gate, a player on the cheapest plan can book every net every day, and the academy gives away facility time it should be charging a premium for.

---

#### 2:00 PM — Walk-in Dinesh arrives at the counter wanting the Bowling Machine for 3–5 PM

Shop Employee Kasun checks the system: slot for FacilityID=3 (Bowling Machine), SlotID=4, April 13 — available. Kasun creates a `slot_booking` with `BookingSource = shop_employee`, takes Rs.150 cash, sets `PaymentStatus = paid`.

> **`slot_booking.BookingSource` matters for accountability.**
> If a dispute arises about whether Dinesh paid, the admin can trace the transaction to Kasun's counter operation. Three different source values — `self`, `admin`, `shop_employee` — make revenue reconciliation possible without ambiguity.

---

#### 3:00 PM — U15 session runs. Coach Sarath adds performance notes after.

For each booked player, the system prompts Sarath to fill `coachingsession` notes (skills worked on, areas for improvement, homework). These notes are linked back to the occurrence via `OccurrenceID`, feeding the entire performance tracking chain.

> **Slot data feeds the performance development chain.**
> `slot_occurrence` → `slot_booking` → `coachingsession` → `performanceupdate` → `playeroverallstats`. The slot system is the entry point of the entire player development pipeline. If the slot system is unreliable, every downstream report is unreliable too.

---

#### 5:30 PM — Admin cancels the April 20 occurrence (school holiday)

Priya cancels it with reason "School holiday". The system auto-inserts a `notification` for all 14 enrolled players and writes a row to `slot_audit_log`: who changed it, when, from what status to what status.

> **`slot_audit_log` is needed for accountability.**
> Next month when Admin Ravi asks "why was April 20 cancelled?", `activitylog` gives the action and `slot_audit_log` gives the before/after state of every changed field. Without it, there is no recoverable evidence of what happened.

---

### The Whole Monday, Summarised

| Time | What Happened | Tables Involved |
|---|---|---|
| Season start | Admin defines "U15 Batting, Mon 3–5pm, Main Ground" | `slot_time_band`, `slot_template` |
| Season start | Coach + Trainer assigned | `slot_template_staff` |
| 8:00 AM | 4 Monday occurrences generated for April | `slot_occurrence` |
| 8:30 AM | 13 players enrolled (sub validated); 1 blocked (sub expired) | `slot_booking`, `playersubscription`, `membershipplan` |
| 9:00 AM | Ravi replaced by Dilshan for April 13 only | `slot_occurrence_staff_override` |
| 9:30 AM | Swairi's booking flagged — active wrist injury | `slot_booking`, `playermedicalrecord` |
| 11:30 AM | Chamara blocked — plan has no facility access | `slot_occurrence`, `slot_booking`, `membershipplan` |
| 2:00 PM | Walk-in Dinesh books Bowling Machine at counter | `slot_occurrence`, `slot_booking` (shop_employee) |
| 3:00 PM | Coach adds performance notes for players | `coachingsession` (linked via OccurrenceID) |
| 5:30 PM | April 20 cancelled; players notified; change logged | `slot_occurrence`, `notification`, `activitylog`, `slot_audit_log` |

Every table was touched by normal academy operations on one ordinary Monday. None is redundant.

---

## 4. Table Definitions

### 4.1 `slot_time_band` — Master Time Bands

```sql
CREATE TABLE `slot_time_band` (
  `SlotID`           TINYINT(4)  NOT NULL AUTO_INCREMENT,
  `SlotLabel`        VARCHAR(50) NOT NULL,   -- "09:00 AM – 11:00 AM"
  `StartTime`        TIME        NOT NULL,
  `EndTime`          TIME        NOT NULL,
  `DurationMinutes`  SMALLINT(6) NOT NULL DEFAULT 120,
  `IsActive`         TINYINT(1)  NOT NULL DEFAULT 1,
  PRIMARY KEY (`SlotID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master fixed time bands. Admin toggles IsActive only — never deletes rows.';

INSERT INTO `slot_time_band` (`SlotLabel`, `StartTime`, `EndTime`, `DurationMinutes`) VALUES
('09:00 AM – 11:00 AM', '09:00:00', '11:00:00', 120),
('11:00 AM – 01:00 PM', '11:00:00', '13:00:00', 120),
('01:00 PM – 03:00 PM', '13:00:00', '15:00:00', 120),
('03:00 PM – 05:00 PM', '15:00:00', '17:00:00', 120),
('05:00 PM – 07:00 PM', '17:00:00', '19:00:00', 120),
('07:00 PM – 09:00 PM', '19:00:00', '21:00:00', 120),
('09:00 PM – 10:00 PM', '21:00:00', '22:00:00',  60);
```

**Safety rule:** Never `DELETE` a row. Existing bookings reference `SlotID`. Set `IsActive = 0` instead.

---

### 4.2 `slot_template` — Program / Session Definition

```sql
CREATE TABLE `slot_template` (
  `TemplateID`          INT(11)       NOT NULL AUTO_INCREMENT,
  `TemplateName`        VARCHAR(255)  NOT NULL,

  `SlotType`            ENUM('program','private','facility_only') NOT NULL,
  -- program       = recurring group (subscription-covered; admin enrolls players)
  -- private       = one staff + one player, booked on request
  -- facility_only = no staff; player or shop employee books independently

  `StaffType`           ENUM('coach','trainer','none') NOT NULL DEFAULT 'coach',
  -- coach   = coaching session (prompts coachingsession log after session)
  -- trainer = physical training session (prompts trainerappointment notes)
  -- none    = facility_only, no staff

  `SlotID`              TINYINT(4)    NOT NULL,       -- FK → slot_time_band
  `DayOfWeek`           TINYINT(1)    DEFAULT NULL,   -- 1=Mon…7=Sun; NULL = ad-hoc / no fixed day
  `FacilityID`          INT(11)       DEFAULT NULL,   -- FK → facility; NULL = assigned per occurrence

  `AgeGroup`            VARCHAR(50)   DEFAULT NULL,   -- "Under 15", "Under 19", "Open"
  `Category`            VARCHAR(100)  DEFAULT NULL,   -- "Batting","Bowling","Fielding","Fitness"
  `Description`         TEXT          DEFAULT NULL,

  `MaxParticipants`     INT(11)       NOT NULL DEFAULT 10,
  `PricePerSession`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  -- 0.00 = covered by subscription (program type)
  -- > 0   = direct charge (private / facility_only)

  `RequiredPlanFeature` ENUM('none','sessions','private_sessions','facility_access')
                        NOT NULL DEFAULT 'none',
  -- Checked against membershipplan before allowing enrollment or booking:
  -- 'sessions'          → plan.SessionsPerWeek > 0
  -- 'private_sessions'  → plan.PrivateSessionsIncluded > 0
  -- 'facility_access'   → plan.FacilityAccessIncluded = 1
  -- 'none'              → any plan (or no active subscription) is allowed

  `RecurrenceStart`     DATE          NOT NULL,
  `RecurrenceEnd`       DATE          DEFAULT NULL,   -- NULL = open-ended
  `IsActive`            TINYINT(1)    NOT NULL DEFAULT 1,

  `CreatedBy`           INT(11)       NOT NULL,       -- FK → user (Admin)
  `CreatedAt`           DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt`           DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`TemplateID`),
  KEY `idx_st_slot`       (`SlotID`),
  KEY `idx_st_facility`   (`FacilityID`),
  KEY `idx_st_type`       (`SlotType`),
  KEY `idx_st_stafftype`  (`StaffType`),
  CONSTRAINT `fk_st_slot`     FOREIGN KEY (`SlotID`)     REFERENCES `slot_time_band`(`SlotID`),
  CONSTRAINT `fk_st_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility`(`FacilityID`),
  CONSTRAINT `fk_st_creator`  FOREIGN KEY (`CreatedBy`)  REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master definition of recurring programs and bookable slot offerings.';
```

**Example templates:**

| TemplateName | SlotType | StaffType | DayOfWeek | Band | AgeGroup | RequiredPlanFeature | FacilityID |
|---|---|---|---|---|---|---|---|
| U15 Batting Practice | program | coach | Monday | 3–5 PM | Under 15 | sessions | 4 (Main Ground) |
| Morning Fitness Circuit | program | trainer | Tuesday | 9–11 AM | Open | sessions | **6 (Trainer Room)** |
| Afternoon Strength & Conditioning | program | trainer | Thursday | 3–5 PM | Open | sessions | **6 (Trainer Room)** |
| Private Batting Session | private | coach | — | Any | Open | private_sessions | 2 (Practice Net 2) |
| Net Self-Practice | facility_only | none | — | Any | Open | facility_access | 1 (Practice Net 1) |
| Bowling Machine Hire | facility_only | none | — | Any | Open | facility_access | 3 (Bowling Machine) |

> **Trainer Room note:** Trainers always use FacilityID 6 (Trainer Room). This is not bookable by players directly (no `facility_only` template is ever created for it). Because a real FacilityID is used, the UNIQUE KEY `(FacilityID, SlotID, OccurrenceDate)` on `slot_occurrence` prevents two trainer sessions from being scheduled at the same time — which could not happen if `FacilityID` were NULL.

---

### 4.3 `slot_template_staff` — Multi-Staff Assignments

> **Changed from v1 design:** Renamed from `slot_template_coach` to `slot_template_staff` to accommodate both coaches and trainers co-assigned to the same program.

```sql
CREATE TABLE `slot_template_staff` (
  `ID`          INT(11)  NOT NULL AUTO_INCREMENT,
  `TemplateID`  INT(11)  NOT NULL,   -- FK → slot_template
  `UserID`      INT(11)  NOT NULL,   -- FK → user (Role = Coach OR Trainer)
  `StaffType`   ENUM('coach','trainer') NOT NULL,
  `StaffRole`   ENUM('lead','assistant') NOT NULL DEFAULT 'lead',
  `AssignedBy`  INT(11)  NOT NULL,   -- FK → user (Admin)
  `AssignedAt`  DATETIME DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_tstaff_template_user` (`TemplateID`, `UserID`),
  CONSTRAINT `fk_tstaff_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template`(`TemplateID`) ON DELETE CASCADE,
  CONSTRAINT `fk_tstaff_user`     FOREIGN KEY (`UserID`)     REFERENCES `user`(`UserID`),
  CONSTRAINT `fk_tstaff_assigner` FOREIGN KEY (`AssignedBy`) REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Staff (coaches and trainers) assigned to a slot template. Supports mixed staff types per program.';
```

**Application rule:** When saving a row, the application must verify that `user.Role` matches `StaffType` (`coach` → Role must be `Coach` or `Admin`; `trainer` → Role must be `Trainer`).

---

### 4.4 `slot_occurrence` — Actual Calendar Instances

```sql
CREATE TABLE `slot_occurrence` (
  `OccurrenceID`    INT(11)    NOT NULL AUTO_INCREMENT,
  `TemplateID`      INT(11)    DEFAULT NULL,      -- NULL = one-off; not from a template
  `SlotID`          TINYINT(4) NOT NULL,
  `OccurrenceDate`  DATE       NOT NULL,
  `FacilityID`      INT(11)    DEFAULT NULL,      -- overrides template facility for this date
  `LegacySessionID` INT(11)    DEFAULT NULL,      -- migration bridge → session.SessionID

  `Status`          ENUM('scheduled','active','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `CancelReason`    TEXT       DEFAULT NULL,
  `MaxParticipants` INT(11)    DEFAULT NULL,      -- NULL = inherit from slot_template
  `Notes`           TEXT       DEFAULT NULL,

  `GeneratedBy`     INT(11)    DEFAULT NULL,      -- FK → user (Admin who ran batch generation)
  `CreatedAt`       DATETIME   DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`OccurrenceID`),
  UNIQUE KEY `uq_occ_facility_slot_date` (`FacilityID`, `SlotID`, `OccurrenceDate`),
  -- Hard DB-level constraint: one session per facility per time band per day

  KEY `idx_occ_template` (`TemplateID`),
  KEY `idx_occ_date`     (`OccurrenceDate`),
  KEY `idx_occ_status`   (`Status`),
  CONSTRAINT `fk_occ_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template`(`TemplateID`),
  CONSTRAINT `fk_occ_slot`     FOREIGN KEY (`SlotID`)     REFERENCES `slot_time_band`(`SlotID`),
  CONSTRAINT `fk_occ_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility`(`FacilityID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='One row per actual session on one calendar date. Generated from templates or created ad-hoc.';
```

**`LegacySessionID`** is a migration bridge only. It stores the old `session.SessionID` for rows migrated from the old table, so existing `coachingsession`, `sessionenrollment`, and `sessionpayment` records that reference it remain queryable. Must be `NULL` for all new occurrences.

---

### 4.5 `slot_occurrence_staff_override` — Per-Date Staff Overrides

```sql
CREATE TABLE `slot_occurrence_staff_override` (
  `ID`              INT(11)      NOT NULL AUTO_INCREMENT,
  `OccurrenceID`    INT(11)      NOT NULL,
  `UserID`          INT(11)      NOT NULL,   -- FK → user (Coach or Trainer substitute)
  `StaffType`       ENUM('coach','trainer') NOT NULL,
  `StaffRole`       ENUM('lead','assistant','substitute') NOT NULL DEFAULT 'substitute',
  `OverridesUserID` INT(11)      DEFAULT NULL, -- UserID of the person being replaced
  `OverrideReason`  VARCHAR(255) DEFAULT NULL, -- "Sick leave", "Emergency", etc.

  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_oc_occ_user` (`OccurrenceID`, `UserID`),
  CONSTRAINT `fk_oc_occurrence` FOREIGN KEY (`OccurrenceID`) REFERENCES `slot_occurrence`(`OccurrenceID`) ON DELETE CASCADE,
  CONSTRAINT `fk_oc_user`       FOREIGN KEY (`UserID`)       REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='One-day staff override. If rows exist for an OccurrenceID, they take precedence over slot_template_staff.';
```

**Fallback rule (application):** When fetching staff for an occurrence, first check `slot_occurrence_staff_override`. If empty, fall back to `slot_template_staff`. A one-day override is additive — you never need to remove template assignments to handle a single substitution.

---

### 4.6 `slot_booking` — Unified Player Bookings

```sql
CREATE TABLE `slot_booking` (
  `BookingID`          INT(11)       NOT NULL AUTO_INCREMENT,
  `OccurrenceID`       INT(11)       NOT NULL,
  `PlayerID`           INT(11)       NOT NULL,   -- FK → user (Role = Player)

  `BookingSource`      ENUM('self','admin','shop_employee') NOT NULL DEFAULT 'self',
  `SubscriptionID`     INT(11)       DEFAULT NULL,
  -- FK → playersubscription: populated when booking is covered by subscription.
  -- NULL for facility_only paid bookings and private session direct charges.

  `MedicalClearedBy`   INT(11)       DEFAULT NULL,
  -- FK → user (Admin UserID): set when an admin overrides a medical flag.
  -- NULL means no medical flag was triggered.

  `Status`             ENUM('pending','confirmed','cancelled','attended','missed') NOT NULL DEFAULT 'pending',
  `AmountCharged`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `PaymentStatus`      ENUM('not_required','pending','paid','refunded') NOT NULL DEFAULT 'not_required',
  `PaymentMethod`      ENUM('cash','card','online') DEFAULT NULL,
  `PaidAt`             DATETIME      DEFAULT NULL,

  `BookedBy`           INT(11)       DEFAULT NULL,   -- FK → user; NULL if self-booked
  `CancelledBy`        INT(11)       DEFAULT NULL,
  `CancelledAt`        DATETIME      DEFAULT NULL,
  `CancelReason`       TEXT          DEFAULT NULL,

  `CreatedAt`          DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt`          DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`BookingID`),
  UNIQUE KEY `uq_booking_player_occurrence` (`OccurrenceID`, `PlayerID`),

  KEY `idx_sb_player`     (`PlayerID`),
  KEY `idx_sb_status`     (`Status`),
  KEY `idx_sb_payment`    (`PaymentStatus`),
  KEY `idx_sb_source`     (`BookingSource`),
  CONSTRAINT `fk_sb_occurrence`   FOREIGN KEY (`OccurrenceID`)   REFERENCES `slot_occurrence`(`OccurrenceID`),
  CONSTRAINT `fk_sb_player`       FOREIGN KEY (`PlayerID`)       REFERENCES `user`(`UserID`),
  CONSTRAINT `fk_sb_subscription` FOREIGN KEY (`SubscriptionID`) REFERENCES `playersubscription`(`SubscriptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Unified player booking. Covers group programs, private sessions, and facility-only hires.';
```

---

### 4.7 `slot_audit_log` — Full Audit Trail

Every create, edit, and cancel operation on the slot system must be permanently recorded. This table is the accountability layer.

```sql
CREATE TABLE `slot_audit_log` (
  `LogID`        INT(11)      NOT NULL AUTO_INCREMENT,
  `EntityType`   ENUM('template','occurrence','booking','staff_assignment') NOT NULL,
  `EntityID`     INT(11)      NOT NULL,   -- PK of the changed record
  `Action`       ENUM('create','update','cancel','delete','override') NOT NULL,
  `ChangedField` VARCHAR(100) DEFAULT NULL, -- e.g. 'Status', 'FacilityID', 'CoachID'
  `OldValue`     TEXT         DEFAULT NULL,
  `NewValue`     TEXT         DEFAULT NULL,
  `Reason`       TEXT         DEFAULT NULL, -- justification entered by the user
  `ChangedBy`    INT(11)      NOT NULL,    -- FK → user
  `ChangedAt`    DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `IPAddress`    VARCHAR(45)  DEFAULT NULL,

  PRIMARY KEY (`LogID`),
  KEY `idx_scl_entity`  (`EntityType`, `EntityID`),
  KEY `idx_scl_changer` (`ChangedBy`),
  KEY `idx_scl_date`    (`ChangedAt`),
  CONSTRAINT `fk_scl_changer` FOREIGN KEY (`ChangedBy`) REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Append-only audit trail of all changes to slot system records. Never UPDATE or DELETE rows here.';
```

**Rule:** Append-only. No UPDATE or DELETE ever. The application must have no UI control and no controller code path that modifies existing rows in this table.

---

## 5. Cross-System Integration Map

```
slot_occurrence
    │
    ├──► coachingsession.SessionID
    │    (coach fills performance notes per player after marking attendance)
    │         └──► performanceupdate.SessionID
    │                   └──► playeroverallstats
    │                        (via existing tr_update_overall_stats trigger)
    │
    ├──► slot_booking
    │         ├── validates: playersubscription + membershipplan (RequiredPlanFeature)
    │         ├── validates: playermedicalrecord (active injury / RestDaysNeeded)
    │         ├── triggers:  notification (booking confirmed / cancelled)
    │         ├── triggers:  livenotification (same-day events)
    │         └── recorded:  slot_audit_log + activitylog
    │
slot_template
    ├──► facility  (reserves FacilityID for recurring programs)
    └──► membershipplan (RequiredPlanFeature matches FacilityAccessIncluded /
                         SessionsPerWeek / PrivateSessionsIncluded)
```

---

## 6. Automated Notifications

All notifications use the existing `notification` and `livenotification` tables.

| Trigger Event | Who Gets Notified | Table | Urgency |
|---|---|---|---|
| New occurrence generated for a program | All enrolled players | `notification` | Low |
| Occurrence cancelled | All booked players | `notification` + `livenotification` | High |
| Booking confirmed | That player | `notification` | Medium |
| Booking cancelled by admin / shop | That player | `notification` + `livenotification` | High |
| Staff substituted on an occurrence | Original + substitute staff | `notification` | Medium |
| Booking blocked — subscription expired | Admin | `notification` | High |
| Booking flagged — active injury | Admin | `notification` + `livenotification` | High |
| Payment not confirmed 24h before session | Player + shop employee | `notification` | Medium |

---

## 7. Safety, Permission, and Conflict Rules

### 7.1 Database-Level (Cannot be bypassed)

| Rule | Mechanism |
|---|---|
| One session per facility per time band per day | `UNIQUE KEY (FacilityID, SlotID, OccurrenceDate)` on `slot_occurrence` — applies to Trainer Room (FacilityID 6) exactly like all other facilities |
| One booking per player per occurrence | `UNIQUE KEY (OccurrenceID, PlayerID)` on `slot_booking` |
| One staff per template | `UNIQUE KEY (TemplateID, UserID)` on `slot_template_staff` |
| One staff override per person per occurrence | `UNIQUE KEY (OccurrenceID, UserID)` on `slot_occurrence_staff_override` |
| Referential integrity | `ON DELETE CASCADE` where child records are meaningless without the parent (e.g., `slot_occurrence_staff_override` without its occurrence) |

### 7.2 Application-Level (PHP controller checks before every DB write)

| Rule | Where to Check | Action on Failure |
|---|---|---|
| Player has active subscription | `slot_booking` service, pre-insert | Block + alert admin |
| Player's plan covers this slot type | `RequiredPlanFeature` vs `membershipplan` | Block + show upgrade prompt to player |
| Player has no active injury within rest window | `playermedicalrecord` query | Show warning; require admin to set `MedicalClearedBy` |
| Booking count < MaxParticipants | COUNT(slot_booking) for OccurrenceID | Return "session is full" |
| Coach/trainer only sees their assigned sessions | Check `slot_template_staff.UserID = logged_in_user` | Return empty list / 403 |
| ShopEmployee cannot create templates or occurrences | Role check in controller constructor | Return 403 |
| ShopEmployee cannot override medical flags | Role check | Show "contact admin" message |
| Player cancellation within 24h window only | `OccurrenceDate - CURDATE() >= 1` | Block with "cancellation window closed" |
| Cancellation after window: no auto-refund | PaymentStatus stays `paid`; reason logged | Admin must manually issue refund |
| `slot_audit_log` is append-only | Application never calls UPDATE/DELETE on this table | No delete button in any admin UI for this table |
| `slot_time_band` rows never deleted | Application only calls `UPDATE IsActive = 0` | No delete UI for slot_time_band |

### 7.3 Role Access Matrix

| Action | Admin | Coach | Trainer | Player | ShopEmployee |
|---|---|---|---|---|---|
| Create / edit `slot_template` | ✅ | ❌ | ❌ | ❌ | ❌ |
| Assign staff to template | ✅ | ❌ | ❌ | ❌ | ❌ |
| Generate occurrences | ✅ | ❌ | ❌ | ❌ | ❌ |
| Cancel any occurrence | ✅ | ❌ | ❌ | ❌ | ❌ |
| Override staff for one date | ✅ | ❌ | ❌ | ❌ | ❌ |
| Enroll player in program | ✅ | ❌ | ❌ | ❌ | ❌ |
| Counter-book facility / private slot | ✅ | ❌ | ❌ | ❌ | ✅ |
| Self-book facility / private slot | ❌ | ❌ | ❌ | ✅ | ❌ |
| Cancel own booking (within window) | ❌ | ❌ | ❌ | ✅ | ❌ |
| Cancel any booking | ✅ | ❌ | ❌ | ❌ | ❌ |
| View all occurrences | ✅ | ❌ | ❌ | ❌ | ✅ (availability only) |
| View own schedule | — | ✅ | ✅ | ✅ | — |
| Override medical flag | ✅ | ❌ | ❌ | ❌ | ❌ |
| Read `slot_audit_log` | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 8. End-to-End Flows

### Flow A: Recurring Group Program (U15 Batting — Every Monday 3–5pm)

```
Admin → Create slot_template
        SlotType=program, StaffType=coach, DayOfWeek=1 (Mon), SlotID=4 (3–5pm),
        FacilityID=4 (Main Ground), AgeGroup="Under 15", RequiredPlanFeature=sessions

Admin → Assign staff (slot_template_staff)
        Coach Sarath  = coach / lead
        Trainer Nimal = trainer / assistant

Admin → Generate occurrences for April
        → inserts slot_occurrence for Apr 6, 13, 20, 27

Admin → Enroll players
        For each player:
          1. Check playersubscription.Status = active
          2. Check membershipplan.SessionsPerWeek > 0
          3. Check playermedicalrecord for active injury
        → inserts slot_booking (SubscriptionID = player's sub, AmountCharged = 0)
        → notification sent to each enrolled player

Coach → Fill coachingsession notes for players after session
```

### Flow B: Private Coaching Session

```
Admin → Create slot_template
        SlotType=private, StaffType=coach, DayOfWeek=NULL,
        FacilityID=2 (Practice Net 2), MaxParticipants=1,
        PricePerSession=1500, RequiredPlanFeature=private_sessions

Admin → Assign one coach (lead) via slot_template_staff

Player → Requests session for a specific date + time band

Admin → Confirm no existing occurrence: check UNIQUE (FacilityID, SlotID, date)
Admin → Create slot_occurrence for the requested date
Admin → Check medical flag; if flagged → set MedicalClearedBy = admin UserID
Admin → Create slot_booking
        BookingSource=admin, AmountCharged=1500, PaymentStatus=pending
        → notification sent to player

Player → Pays → PaymentStatus=paid, PaidAt=now
Coach  → Session completed → add coachingsession notes
All    → slot_audit_log entry written for every state transition
```

### Flow C: Facility-Only (Player Self-Books a Net)

```
Admin → Create slot_template
        SlotType=facility_only, StaffType=none, FacilityID=1 (Practice Net 1),
        MaxParticipants=2, PricePerSession=100, RequiredPlanFeature=facility_access

Admin → Generate weekly slot_occurrence rows for the next month

Player → Visits "Book a Facility" page
         System checks:
           1. playersubscription.Status = active
           2. membershipplan.FacilityAccessIncluded = 1
           3. playermedicalrecord: no active injury
           4. Occurrence booking_count < MaxParticipants
         Shows only occurrences passing all checks

Player → Books → slot_booking (BookingSource=self, AmountCharged=100, PaymentStatus=pending)
         DB UNIQUE KEY prevents any double-booking at the database level
Player → Pays online → PaymentStatus=paid
```

### Flow D: Walk-in Counter Booking (ShopEmployee)

```
Walk-in → arrives at front desk: wants Bowling Machine (3–5pm today)

ShopEmployee → Checks slot_occurrence: FacilityID=3, SlotID=4, today — available
ShopEmployee → Looks up player account; checks medical flag
               (If flag: shows warning, tells player to see admin)
ShopEmployee → Creates slot_booking:
               BookingSource=shop_employee, BookedBy=ShopEmployee UserID,
               AmountCharged=150, PaymentMethod=cash, PaymentStatus=paid
               → activitylog entry created
               → notification sent to player
```

### Flow E: Ad-Hoc Session (No Template)

```
Special camp day / makeup session / tournament warm-up:

Admin → Create slot_occurrence directly (TemplateID = NULL)
Admin → Assign staff via slot_occurrence_staff_override directly
Admin → Enroll players via slot_booking (subscription + medical checks still apply)
Admin → slot_audit_log: Action=create, EntityType=occurrence, Reason="Special camp"
```

---

## 9. Admin UI Pages

| Page | Route | Role | Purpose |
|---|---|---|---|
| Manage Time Bands | `/admin/slots/timeslots` | Admin | Toggle IsActive only. No delete. |
| Manage Templates | `/admin/slots/templates` | Admin | Full CRUD; includes StaffType + RequiredPlanFeature |
| Assign Staff to Template | `/admin/slots/templates/{id}/staff` | Admin | Add coaches and trainers; set type + role |
| Generate Occurrences | `/admin/slots/generate` | Admin | Template + date range → batch insert |
| Calendar View | `/admin/slots/calendar` | Admin | Week/month grid; colour by slot type and staff type |
| Edit Occurrence | `/admin/slots/occurrences/{id}` | Admin | Change facility, cancel with reason, substitute staff |
| Manage Bookings | `/admin/slots/bookings` | Admin | View / confirm / cancel; clear medical flags |
| Change Log | `/admin/slots/changelog` | Admin | Read-only view of `slot_audit_log` |
| Coach Schedule | `/coach/slots` | Coach | Weekly view of assigned occurrences |
| Mark Attendance (Coach) | `/coach/slots/{id}/attendance` | Coach | Present/absent/late → link to coachingsession notes |
| Trainer Schedule | `/trainer/slots` | Trainer | Weekly view of assigned physical training occurrences |
| Mark Attendance (Trainer) | `/trainer/slots/{id}/attendance` | Trainer | Present/absent/late for fitness sessions |
| Browse Slots | `/player/slots` | Player | Available facility and private slots (gated by plan + medical) |
| My Bookings | `/player/slots/bookings` | Player | Upcoming + past; cancel within 24h window |
| Counter Booking | `/shop/slots/book` | ShopEmployee | Walk-in facility and private bookings at the counter |

---

## 10. Implementation Phases

### Phase 1 — Database (2 days)
- [ ] Write `create_slot_tables.sql` with all 8 tables
- [ ] Seed `slot_time_band` with 7 bands
- [ ] Write migration script: copy `session` rows → `slot_occurrence` (populate `LegacySessionID`)
- [ ] Verify no FK conflicts with existing tables

### Phase 2 — Admin Templates & Staff Assignment (2–3 days)
- [ ] Template form with `StaffType` and `RequiredPlanFeature` selectors
- [ ] Staff assignment: role-aware dropdown (Coaches if StaffType=coach, Trainers if trainer)
- [ ] Application-level validation that assigned `user.Role` matches `StaffType`
- [ ] Toggle IsActive; no delete button for templates or time bands

### Phase 3 — Occurrence Generation & Calendar (2 days)
- [ ] Occurrence generator: template + date range → inserts for matching DayOfWeek
- [ ] Handle DB `1062 Duplicate entry` from UNIQUE constraint gracefully ("facility already booked that slot on that day")
- [ ] Admin calendar view: grid by date + facility, colour-coded by type
- [ ] Occurrence edit: cancel with reason, substitute staff, log to `slot_audit_log`

### Phase 4 — Subscription Gate & Medical Flag (2 days)
- [ ] `SlotBookingService::validateEntitlement(playerID, templateID)` — checks `playersubscription` + `membershipplan.RequiredPlanFeature`
- [ ] `SlotBookingService::checkMedicalFlag(playerID, occurrenceDate)` — queries `playermedicalrecord`
- [ ] Admin override form for medical flag (sets `MedicalClearedBy`)
- [ ] Structured error codes so UI shows correct message per failure type

### Phase 5 — Player Booking & Payment (2–3 days)
- [ ] Player browse page with all four gates applied
- [ ] Self-booking form + payment flow
- [ ] 24-hour cancellation window enforcement
- [ ] ShopEmployee counter-booking page with medical flag warning

### Phase 6 — Notifications & Audit (1–2 days)
- [ ] `slot_audit_log` writes on every state change with IP address logging
- [ ] Notification inserts for all events listed in Section 6

### Phase 7 — Reporting (1–2 days)
- [ ] Facility utilisation: occurrences per facility per month, percentage occupied
- [ ] Revenue report: `slot_booking.AmountCharged` by `BookingSource` and by `SlotType`
- [ ] Coach and trainer workload: sessions led vs assisted per month

---

## 11. SQL Migration Outline

### Before running: seed the Trainer Room into `facility`

```sql
-- Add the Trainer Room as a facility row so trainer occurrences
-- participate in the UNIQUE (FacilityID, SlotID, OccurrenceDate) constraint.
-- Run this ONCE; skip if the row already exists.
INSERT IGNORE INTO `facility`
    (`FacilityID`, `Name`, `Location`, `Capacity`, `AvailabilityStatus`, `HourlyRate`)
VALUES
    (6, 'Trainer Room', 'Main Building — Ground Floor', 1, 'Available', 0.00);
-- Capacity = 1 means one trainer session at a time (one trainer uses the room).
-- HourlyRate = 0 because players are never charged separately for trainer room use;
-- the session fee applies instead.
-- AvailabilityStatus is managed by the slot_occurrence UNIQUE constraint, not manually.
```

File: `create_slot_tables.sql`

```sql
-- ============================================================
-- Elite Cricket Academy — Slot Module Migration
-- Run after cricket_academy (10).sql is already loaded
-- Uses IF NOT EXISTS: safe to run on a fresh install
-- ============================================================

CREATE TABLE IF NOT EXISTS `slot_time_band` ( /* see Section 4.1 */ );
INSERT IGNORE INTO `slot_time_band` ( /* 7 bands */ );

CREATE TABLE IF NOT EXISTS `slot_template`       ( /* Section 4.2 */ );
CREATE TABLE IF NOT EXISTS `slot_template_staff` ( /* Section 4.3 */ );
CREATE TABLE IF NOT EXISTS `slot_occurrence`     ( /* Section 4.4 */ );
CREATE TABLE IF NOT EXISTS `slot_occurrence_staff_override` ( /* Section 4.5 */ );
CREATE TABLE IF NOT EXISTS `slot_booking`        ( /* Section 4.6 */ );
CREATE TABLE IF NOT EXISTS `slot_audit_log`      ( /* Section 4.7 */ );

-- Migration bridge: map existing session rows into slot_occurrence
INSERT INTO `slot_occurrence`
    (TemplateID, SlotID, OccurrenceDate, FacilityID, Status,
     MaxParticipants, LegacySessionID, GeneratedBy)
SELECT
    NULL,
    (SELECT SlotID FROM slot_time_band
     WHERE StartTime <= s.StartTime ORDER BY StartTime DESC LIMIT 1),
    s.Date,
    NULL,           -- FacilityID unknown from old freetext; admin fills post-migration
    CASE s.Status
        WHEN 'active'    THEN 'active'
        WHEN 'completed' THEN 'completed'
        WHEN 'cancelled' THEN 'cancelled'
        ELSE 'scheduled'
    END,
    s.MaxParticipants,
    s.SessionID,    -- LegacySessionID bridge
    1               -- system admin UserID = 1
FROM `session` s
WHERE NOT EXISTS (
    SELECT 1 FROM slot_occurrence o WHERE o.LegacySessionID = s.SessionID
);
```

---

## 12. Design Decisions Log

| Decision | Reason |
|---|---|
| `slot_template_staff` instead of `slot_template_coach` | Trainers co-run programs alongside coaches. The original table name excluded an entire staff role. |
| `StaffType` column on `slot_template` | Tells the system what post-session log to prompt (coachingsession vs trainerappointment), and what assignment table to validate against. |
| `RequiredPlanFeature` on `slot_template` | Centralises entitlement logic in the template. Admin who creates the template sets what membership is needed. Booking code just reads one field — no hardcoded plan IDs in controllers. |
| `SubscriptionID` on `slot_booking` | Proves which subscription covered a group booking. Finance team can reconcile. Support can answer "why wasn't this player charged?" |
| `MedicalClearedBy` on `slot_booking` | Records the admin who overrode a medical flag. Creates legal accountability if a cleared-to-play player re-injures during the session. |
| `LegacySessionID` on `slot_occurrence` | Non-destructive migration. Old session data remains queryable. Old `coachingsession`, `sessionenrollment`, and `sessionpayment` rows retain their references. |
| `slot_audit_log` append-only | An audit log that can be edited is not an audit log. Integrity requires immutability. |
| DB UNIQUE on `(FacilityID, SlotID, OccurrenceDate)` | Double-booking prevention at the database survives race conditions, bugs, and direct DB access. Application-level checks alone do not. |
| Never DELETE `slot_time_band` rows | Every booking references `SlotID`. A delete would cascade-break FK constraints or leave orphaned records. `IsActive = 0` is the only allowed operation. |
| Unified `slot_booking` for all types | Facility utilisation, attendance, and revenue reports only need one table. With the old setup (`facilitybooking`, `sessionenrollment`, `sessionpayment` all separate) a single occupancy query required three-way joins with no shared conflict constraint. |
| Trainer Room added to `facility` table instead of a separate trainer table | Trainers are not associated with player-bookable facilities, but they do have a fixed physical location (the Trainer Room). Making it a `facility` row means the DB UNIQUE constraint `(FacilityID, SlotID, OccurrenceDate)` covers trainer double-booking prevention automatically — no special case in code. Splitting trainers into a separate occurrence table would require duplicating all booking, attendance, and audit logic for zero scheduling benefit, since the mechanics are identical. |

---

## 13. DBML Diagram

Paste the block below into **[dbdiagram.io](https://dbdiagram.io)** to generate a visual ER diagram.

```dbml
// Elite Cricket Academy — Slot / Session / Booking / Facility Module
// https://dbdiagram.io

// ── NEW MODULE TABLES ─────────────────────────────────────────────

Table slot_time_band {
  SlotID          tinyint   [pk, increment, note: 'Never DELETE — set IsActive=0']
  SlotLabel       varchar(50) [not null, note: '"09:00 AM – 11:00 AM"']
  StartTime       time      [not null]
  EndTime         time      [not null]
  DurationMinutes smallint  [not null, default: 120]
  IsActive        tinyint   [not null, default: 1]
}

Table slot_template {
  TemplateID          int     [pk, increment]
  TemplateName        varchar(255) [not null]
  SlotType            varchar(20) [not null, note: 'program | private | facility_only']
  StaffType           varchar(10) [not null, note: 'coach | trainer | none']
  SlotID              tinyint [not null, ref: > slot_time_band.SlotID]
  DayOfWeek           tinyint [note: '1=Mon…7=Sun; NULL=ad-hoc']
  FacilityID          int     [ref: > facility.FacilityID]
  AgeGroup            varchar(50)
  Category            varchar(100)
  Description         text
  MaxParticipants     int     [not null, default: 10]
  PricePerSession     decimal [not null, default: 0.00, note: '0=subscription-covered']
  RequiredPlanFeature varchar(30) [not null, note: 'none|sessions|private_sessions|facility_access']
  RecurrenceStart     date    [not null]
  RecurrenceEnd       date    [note: 'NULL = open-ended']
  IsActive            tinyint [not null, default: 1]
  CreatedBy           int     [not null, ref: > user.UserID]
  CreatedAt           datetime
  UpdatedAt           datetime
}

Table slot_template_staff {
  ID          int     [pk, increment]
  TemplateID  int     [not null, ref: > slot_template.TemplateID]
  UserID      int     [not null, ref: > user.UserID, note: 'Role = Coach OR Trainer']
  StaffType   varchar(10) [not null, note: 'coach | trainer']
  StaffRole   varchar(15) [not null, note: 'lead | assistant']
  AssignedBy  int     [not null, ref: > user.UserID]
  AssignedAt  datetime

  indexes {
    (TemplateID, UserID) [unique, name: 'uq_tstaff_template_user']
  }
}

Table slot_occurrence {
  OccurrenceID    int     [pk, increment]
  TemplateID      int     [ref: > slot_template.TemplateID, note: 'NULL = ad-hoc']
  SlotID          tinyint [not null, ref: > slot_time_band.SlotID]
  OccurrenceDate  date    [not null]
  FacilityID      int     [ref: > facility.FacilityID]
  LegacySessionID int     [note: 'Migration bridge only — NULL for all new rows']
  Status          varchar(15) [not null, note: 'scheduled|active|cancelled|completed']
  CancelReason    text
  MaxParticipants int     [note: 'NULL = inherit from slot_template']
  Notes           text
  GeneratedBy     int     [ref: > user.UserID]
  CreatedAt       datetime

  indexes {
    (FacilityID, SlotID, OccurrenceDate) [unique, name: 'uq_occ_facility_slot_date',
      note: 'Hard double-booking lock — covers all facilities including Trainer Room']
  }
}

Table slot_occurrence_staff_override {
  ID              int     [pk, increment]
  OccurrenceID    int     [not null, ref: > slot_occurrence.OccurrenceID]
  UserID          int     [not null, ref: > user.UserID]
  StaffType       varchar(10) [not null, note: 'coach | trainer']
  StaffRole       varchar(15) [not null, note: 'lead|assistant|substitute']
  OverridesUserID int     [ref: > user.UserID, note: 'Person being replaced this date']
  OverrideReason  varchar(255)

  indexes {
    (OccurrenceID, UserID) [unique, name: 'uq_soso_occ_user']
  }
}

Table slot_booking {
  BookingID       int     [pk, increment]
  OccurrenceID    int     [not null, ref: > slot_occurrence.OccurrenceID]
  PlayerID        int     [not null, ref: > user.UserID]
  BookingSource   varchar(15) [not null, note: 'self | admin | shop_employee']
  SubscriptionID  int     [ref: > playersubscription.SubscriptionID,
                           note: 'NULL for direct-pay bookings']
  MedicalClearedBy int    [ref: > user.UserID,
                            note: 'Admin who overrode injury flag; NULL = no flag triggered']
  Status          varchar(15) [not null, note: 'pending|confirmed|cancelled|attended|missed']
  AmountCharged   decimal [not null, default: 0.00]
  PaymentStatus   varchar(15) [not null, note: 'not_required|pending|paid|refunded']
  PaymentMethod   varchar(10) [note: 'cash|card|online']
  PaidAt          datetime
  BookedBy        int     [ref: > user.UserID, note: 'NULL if self-booked']
  CancelledBy     int     [ref: > user.UserID]
  CancelledAt     datetime
  CancelReason    text
  CreatedAt       datetime
  UpdatedAt       datetime

  indexes {
    (OccurrenceID, PlayerID) [unique, name: 'uq_booking_player_occurrence']
  }
}

Table slot_audit_log {
  LogID         int     [pk, increment]
  EntityType    varchar(20) [not null, note: 'template|occurrence|booking|staff_assignment']
  EntityID      int     [not null, note: 'PK of the changed row']
  Action        varchar(15) [not null, note: 'create|update|cancel|delete|override']
  ChangedField  varchar(100)
  OldValue      text
  NewValue      text
  Reason        text
  ChangedBy     int     [not null, ref: > user.UserID]
  ChangedAt     datetime
  IPAddress     varchar(45)

  Note: 'APPEND-ONLY. No UPDATE or DELETE ever.'
}

// ── REFERENCED EXISTING TABLES (abbreviated) ──────────────────────

Table facility {
  FacilityID        int     [pk, increment]
  Name              varchar(100) [not null]
  Location          varchar(255)
  Capacity          int
  AvailabilityStatus varchar(20)
  HourlyRate        decimal

  Note: 'Existing table. FacilityID 6 = Trainer Room (seed in create_slot_tables.sql).'
}

Table user {
  UserID     int     [pk, increment]
  FirstName  varchar(50)
  LastName   varchar(50)
  Email      varchar(100)
  Role       varchar(15) [note: 'Admin|Coach|Trainer|Player|ShopEmployee']
  IsActive   tinyint

  Note: 'Existing table — abbreviated for clarity.'
}

Table playersubscription {
  SubscriptionID  int     [pk, increment]
  PlayerID        int     [not null, ref: > user.UserID]
  PlanID          int     [not null, ref: > membershipplan.PlanID]
  StartDate       date    [not null]
  EndDate         date
  Status          varchar(15) [note: 'active|suspended|cancelled|expired']
  MonthlyFee      decimal

  Note: 'Existing table. Checked before every program enrollment.'
}

Table membershipplan {
  PlanID                  int     [pk, increment]
  PlanName                varchar(100)
  SessionsPerWeek         int
  PrivateSessionsIncluded int
  FacilityAccessIncluded  tinyint
  MonthlyFee              decimal

  Note: 'Existing table. 3 plans: general(4500) / private(7000) / pro(10000).'
}

Table playermedicalrecord {
  RecordID        int     [pk, increment]
  PlayerID        int     [not null, ref: > user.UserID]
  InjuryDate      date
  RecoveryStatus  varchar(25) [note: 'ongoing|recovering|fully_recovered|chronic_condition']
  RestDaysNeeded  int

  Note: 'Existing table. Checked before confirming any slot_booking.'
}

Table coachingsession {
  SessionLogID      int   [pk, increment]
  SessionID         int   [note: 'Will link to slot_occurrence.OccurrenceID post-migration']
  PlayerID          int   [ref: > user.UserID]
  CoachID           int   [ref: > user.UserID]
  AttendanceStatus  varchar(15)
  PerformanceNotes  text
  SkillsWorkedOn    text
  SessionRating     tinyint

  Note: 'Existing table. Fed from slot_booking after session completes.'
}
```

---

## 14. Booking Journey Scenario — Facility → Slot → Booking

This is a compact trace of one booking from start to finish, showing exactly which table row is read or written at each step. Use this as a test script checklist when building the booking controller.

---

### Setup (done once by Admin — season start)

| Step | Action | Table Written |
|---|---|---|
| 1 | Admin creates "Net Self-Practice" template: `SlotType=facility_only`, `StaffType=none`, `FacilityID=1` (Practice Net 1), `SlotID=3` (1–3 PM), `RequiredPlanFeature=facility_access`, `PricePerSession=100` | `slot_template` (1 row) |
| 2 | Admin generates occurrences for April: every day, slot_time_band 3, FacilityID 1 | `slot_occurrence` (30 rows) |
| 3 | DB UNIQUE `(FacilityID=1, SlotID=3, OccurrenceDate)` enforced — no day can be double-entered | — |

---

### Player Books a Net (April 13, 1–3 PM)

| Step | Check | Table Read | Pass / Fail Action |
|---|---|---|---|
| 4 | Player logs in (`PlayerID=12`) | `user` | — |
| 5 | Subscription active? | `playersubscription` WHERE `PlayerID=12 AND Status='active'` | Fail → show "Renew subscription" |
| 6 | Plan covers facility_only? | `membershipplan` WHERE `FacilityAccessIncluded=1` | Fail → show "Upgrade to Private or Pro plan" |
| 7 | Active injury within rest window? | `playermedicalrecord` WHERE `PlayerID=12 AND RecoveryStatus IN ('ongoing','recovering') AND ADDDATE(InjuryDate, RestDaysNeeded) >= '2026-04-13'` | Fail → show warning, block booking, notify admin |
| 8 | Occurrence available? | `slot_occurrence` WHERE `OccurrenceDate='2026-04-13' AND SlotID=3 AND FacilityID=1 AND Status='scheduled'` | Fail → "No slot available that day" |
| 9 | Under capacity? | COUNT(`slot_booking`) for that OccurrenceID < `MaxParticipants` | Fail → "Session is full" |
| 10 | Already booked? | `slot_booking` WHERE `OccurrenceID=X AND PlayerID=12` | Fail → "Already booked" (UNIQUE key would also catch this) |
| **11** | **All checks pass → INSERT booking** | **`slot_booking`** | `BookingSource=self`, `SubscriptionID=NULL` (facility_only = direct pay), `AmountCharged=100`, `PaymentStatus=pending` |
| 12 | Write audit entry | `slot_audit_log` | `EntityType=booking`, `Action=create`, `ChangedBy=12` |
| 13 | Notify player | `notification` | "Your net booking for April 13, 1–3 PM is confirmed. Payment pending." |

---

### Player Pays

| Step | Action | Table Written |
|---|---|---|
| 14 | Player completes online payment | `slot_booking` UPDATE: `PaymentStatus=paid`, `PaymentMethod=online`, `PaidAt=now()` |
| 15 | Audit | `slot_audit_log`: `ChangedField=PaymentStatus`, `OldValue=pending`, `NewValue=paid` |

---

### What Each Table Held After This One Booking

```
facility            → FacilityID=1 (Practice Net 1) — unchanged, just referenced
slot_time_band      → SlotID=3 (01:00 PM – 03:00 PM) — unchanged, just referenced
slot_template       → 1 row: Net Self-Practice definition
slot_occurrence     → 1 row for Apr 13: OccurrenceID=X, FacilityID=1, SlotID=3
slot_booking        → 1 row: PlayerID=12, OccurrenceID=X, paid
slot_audit_log      → 2 rows: create booking / pay
notification        → 1 row: booking confirmed
playersubscription  → read-only, unchanged
membershipplan      → read-only, unchanged
playermedicalrecord → read-only, unchanged (no flag this time)
```

Fifteen steps. Three table writes. Three pre-write safety checks. One booking.

---

*Document prepared for the Elite Cricket Academy development team.*
*All designs reference the live schema `cricket_academy (10).sql` as of April 6, 2026.*
*SQL migration file: `create_slot_tables.sql`*
