# Sessions Module — Complete Guide

This document covers every aspect of how the slot-based sessions system works: database design, business rules, role responsibilities, booking flow, and admin management.

---

## Table of Contents

1. [Overview](#1-overview)
2. [Database Schema](#2-database-schema)
3. [Session Types](#3-session-types)
4. [Time Bands](#4-time-bands)
5. [Templates](#5-templates)
6. [Occurrences](#6-occurrences)
7. [Staff Assignment](#7-staff-assignment)
8. [Booking Flow — Player](#8-booking-flow--player)
9. [Gate Checks](#9-gate-checks)
10. [Private Sessions (Staff-Created)](#10-private-sessions-staff-created)
11. [Admin Management](#11-admin-management)
12. [Staff View (Coach / Trainer)](#12-staff-view-coach--trainer)
13. [Audit & Activity Logging](#13-audit--activity-logging)
14. [URL Reference](#14-url-reference)
15. [Role Permissions Summary](#15-role-permissions-summary)

---

## 1. Overview

The sessions module replaces the old ad-hoc booking system with a structured, template-driven slot system.

```
Time Band  →  Template  →  Occurrences  →  Bookings
(when)        (what)         (each date)     (who attends)
```

**Core idea:**
- An **Admin** creates a **Template** (e.g. "U15 Batting Practice, every Monday, 11 AM–1 PM").
- An **Admin** generates **Occurrences** from that template — one row per date.
- A **Player** books an occurrence. Three gate checks fire: entitlement, medical, and capacity.
- A **Coach** or **Trainer** views their assigned occurrences on a weekly calendar and can create their own one-off **Private Sessions**.

---

## 2. Database Schema

### 2.1 `slot_time_band`

Defines the available time windows. All sessions must use one of these.

| Column | Type | Notes |
|---|---|---|
| `SlotID` | tinyint PK | Auto-increment |
| `SlotLabel` | varchar(50) | Display text e.g. `11:00 AM – 01:00 PM` |
| `StartTime` / `EndTime` | time | Used in views |
| `DurationMinutes` | smallint | Default 120 |
| `IsActive` | tinyint(1) | 0 = hidden from all dropdowns |

**Current time bands:**

| SlotID | Label | Active |
|---|---|---|
| 1 | 09:00 AM – 11:00 AM | No (disabled) |
| 2 | 11:00 AM – 01:00 PM | Yes |
| 3 | 01:00 PM – 03:00 PM | Yes |
| 4 | 03:00 PM – 05:00 PM | Yes |
| 5 | 05:00 PM – 07:00 PM | Yes |
| 6 | 07:00 PM – 09:00 PM | Yes |
| 7 | 09:00 PM – 10:00 PM | Yes |

Time bands cannot be deleted; they are toggled active/inactive.

---

### 2.2 `slot_template`

Defines a recurring session program. Think of it as a course definition.

| Column | Type | Notes |
|---|---|---|
| `TemplateID` | int PK | |
| `TemplateName` | varchar(255) | e.g. "U15 Batting Practice" |
| `SlotType` | enum | `program`, `private`, `facility_only` |
| `StaffType` | enum | `coach`, `trainer`, `none` |
| `SlotID` | FK → slot_time_band | The time window |
| `DayOfWeek` | tinyint | 1=Mon…7=Sun; NULL = no fixed day |
| `FacilityID` | FK → facility | Default facility; can be overridden per occurrence |
| `AgeGroup` | varchar | e.g. "Under 15", "Open" |
| `Category` | varchar | e.g. "Batting", "Fitness" |
| `MaxParticipants` | int | Default cap; occurrence can override |
| `PricePerSession` | decimal | 0.00 = subscription-covered |
| `RequiredPlanFeature` | enum | `none`, `sessions`, `private_sessions`, `facility_access` |
| `RecurrenceStart` | date | Season start |
| `RecurrenceEnd` | date | NULL = open-ended |
| `IsActive` | tinyint(1) | Inactive templates are hidden from the player booking page |

**Important constraints:**
- Deactivating a template (`IsActive = 0`) immediately hides all its future occurrences from players.
- Editing a template does **not** retroactively change occurrences already generated — occurrences are independent rows.

---

### 2.3 `slot_occurrence`

One row per actual session date. This is what players book.

| Column | Type | Notes |
|---|---|---|
| `OccurrenceID` | int PK | |
| `TemplateID` | int FK | NULL for private/ad-hoc sessions |
| `SlotID` | tinyint FK | Copied from template at generation time |
| `OccurrenceDate` | date | The actual date |
| `FacilityID` | int FK | May override the template's facility |
| `Status` | enum | `scheduled`, `active`, `cancelled`, `completed` |
| `CancelReason` | text | Populated on cancellation |
| `MaxParticipants` | int | NULL = inherit from template |
| `Notes` | text | Optional notes shown to players |
| `GeneratedBy` | int FK → user | Admin or coach/trainer who created the row |

**Unique constraint:** `UNIQUE(FacilityID, SlotID, OccurrenceDate)` — prevents double-booking a facility for the same time slot on the same date.

---

### 2.4 `slot_template_staff`

Permanent assignment of a coach or trainer to a template (applies to all occurrences).

| Column | Type | Notes |
|---|---|---|
| `ID` | int PK | |
| `TemplateID` | FK → slot_template | |
| `UserID` | FK → user | Must have Role=Coach or Role=Trainer |
| `StaffType` | enum | `coach` / `trainer` — must match template's StaffType |
| `StaffRole` | enum | `lead` / `assistant` |
| `AssignedBy` | FK → user | Admin who assigned |

**Unique constraint:** `UNIQUE(TemplateID, UserID)` — a staff member can only be assigned once per template.

---

### 2.5 `slot_occurrence_staff_override`

Per-date staff override — covers substitutions and self-created private sessions.

| Column | Type | Notes |
|---|---|---|
| `ID` | int PK | |
| `OccurrenceID` | FK → slot_occurrence | |
| `UserID` | FK → user | The substitute or creator |
| `StaffType` | enum | `coach` / `trainer` |
| `StaffRole` | enum | `lead`, `assistant`, `substitute` |
| `OverridesUserID` | FK → user (nullable) | Who is being replaced; NULL for self-created private sessions |
| `OverrideReason` | varchar | e.g. "Sick leave", "Private session created by staff" |

**How staff visibility works:**
- `getMyOccurrences` runs a UNION: it finds occurrences where the staff member appears in either `slot_template_staff` OR `slot_occurrence_staff_override`.
- When displaying staff names on the admin calendar, override rows take priority over template rows.

---

### 2.6 `slot_booking`

One row per player per occurrence.

| Column | Type | Notes |
|---|---|---|
| `BookingID` | int PK | |
| `OccurrenceID` | FK → slot_occurrence | |
| `PlayerID` | FK → user | |
| `BookingSource` | enum | `self`, `admin`, `shop_employee` |
| `SubscriptionID` | FK → playersubscription (nullable) | NULL for direct-pay bookings |
| `MedicalClearedBy` | FK → user (nullable) | Admin who manually cleared an injury flag |
| `Status` | enum | `pending`, `confirmed`, `cancelled`, `attended`, `missed` |
| `AmountCharged` | decimal | 0.00 for subscription-covered sessions |
| `PaymentStatus` | enum | `not_required`, `pending`, `paid`, `refunded` |
| `PaymentMethod` | enum | `cash`, `card`, `online` |
| `CancelledBy` / `CancelledAt` / `CancelReason` | | Populated on cancellation |

**Unique constraint:** `UNIQUE(OccurrenceID, PlayerID)` — a player can only hold one booking per occurrence (prevents duplicate bookings after cancellation without using the re-book path).

---

### 2.7 `slot_audit_log`

Immutable change log for every action on the slot system.

| Column | Notes |
|---|---|
| `EntityType` | `occurrence`, `booking`, `template`, `time_band` |
| `EntityID` | ID of the affected row |
| `Action` | `create`, `cancel`, `update`, `override` |
| `ChangedField` / `OldValue` / `NewValue` | What changed |
| `Reason` | Free-text reason |
| `ChangedBy` | FK → user |
| `IPAddress` | Recorded for all writes |

---

## 3. Session Types

The `SlotType` field on `slot_template` controls how the system treats a session.

| Type | Description | Staff Required | Subscription Check |
|---|---|---|---|
| `program` | Coach/trainer-led group session (e.g. batting practice) | Yes | Yes — based on `RequiredPlanFeature` |
| `private` | One-to-one or small group session | Yes | Yes — `private_sessions` feature required |
| `facility_only` | Player books the facility/net only, no staff involved | No (StaffType = `none`) | Yes — `facility_access` feature |
| *(private session — no template)* | Ad-hoc session created by staff from the Staffslots module | Yes (auto-assigned) | **No** — always open to all players |

---

## 4. Time Bands

Time bands are the building blocks. Every template and every occurrence must reference a time band.

**Admin actions:**
- View all bands at `/adminslots` → Time Bands tab
- Toggle active/inactive — deactivating a band hides it from new template/occurrence creation, but existing occurrence rows are unaffected

**Rule:** A time band should only be deactivated if no active templates reference it and no future occurrences use it.

---

## 5. Templates

Templates define the *programme* — what session it is, when it recurs, who delivers it.

**Creating a template (`/adminslots/template/create`):**
1. Name, SlotType, StaffType
2. Time band (any active SlotID)
3. Day of week (1–7) or leave blank for no fixed day
4. Facility (optional at template level; can be set per occurrence)
5. Age group, Category, Description
6. Max participants, Price per session
7. Required plan feature — controls who can book
8. Recurrence start / end dates

**After creating a template, nothing is visible to players until occurrences are generated.**

---

## 6. Occurrences

An occurrence is a **single bookable instance** of a session on a specific date.

### 6.1 Batch Generation (from template)

Admin runs "Generate Occurrences" (`/adminslots/generate`):
1. Picks a template, a from-date, and a to-date
2. System loops over every day in the range
3. If `DayOfWeek` matches (or template has no fixed day), inserts a `slot_occurrence` row
4. `UNIQUE(FacilityID, SlotID, OccurrenceDate)` silently skips any date already generated
5. Returns a summary: `N inserted, N skipped`

### 6.2 Ad-Hoc Occurrence (Admin)

Admin creates a single occurrence without a template (`/adminslots/adhoc`):
- `TemplateID = NULL`
- Must specify: time band, date, facility, max participants, notes
- Immediately visible to players on the booking page

### 6.3 Private Session (Staff-Created)

Coach or trainer creates a one-off occurrence from their Staffslots panel (`/staffslots/private_session`):
- `TemplateID = NULL`
- System auto-inserts a `slot_occurrence_staff_override` row so the session appears on the creator's calendar
- Immediately visible to players — no subscription check applies
- Duplicate detection: `UNIQUE(FacilityID, SlotID, OccurrenceDate)` — shows "already booked" error if clash

### 6.4 Occurrence Status

| Status | Meaning |
|---|---|
| `scheduled` | Upcoming, open for booking |
| `active` | In progress (not currently auto-set) |
| `cancelled` | Cancelled by admin or assigned staff; no new bookings possible |
| `completed` | Past session (not currently auto-set) |

Players can only see and book occurrences with status `scheduled` or `active`.

---

## 7. Staff Assignment

### Template-level (permanent)

Admin assigns coaches/trainers to a template:
- `StaffType` on the assignment must match the template's `StaffType`
- User's `Role` in the `user` table must also match (`Coach`/`Trainer`)
- Admin and Coach roles are both accepted when assigning type=`coach`
- Stored in `slot_template_staff`; applies to every occurrence generated from that template

### Occurrence-level (one-date override)

Admin can substitute a staff member for a single date:
- Stored in `slot_occurrence_staff_override`
- `OverridesUserID` records who is being replaced
- The override takes precedence when displaying staff names on the admin calendar

### Staff visibility rule

When looking up who is running an occurrence:
1. **Check `slot_occurrence_staff_override`** — if any rows exist, use those exclusively
2. **Otherwise fall back to `slot_template_staff`** for the template

---

## 8. Booking Flow — Player

**Entry point:** `/playerslots/available`

The player sees a list of all upcoming, non-cancelled occurrences (including private sessions). Each card shows:
- Session name (or "Private Session" for template-less rows)
- Date, time band, facility
- Spots taken / max
- Type badge (Program / Private / Facility Only)
- A Book button (if not blocked) or a greyed-out state with a reason

**Booking POST:** `/playerslots/book`

1. Receives `occurrence_id` via POST
2. Runs all three gate checks (see Section 9)
3. On pass: inserts `slot_booking` with `Status = 'confirmed'`, writes audit and activity logs, redirects to My Bookings with success flash
4. On fail: sets `$_SESSION['slot_error']` with a human-readable message, redirects back to Available sessions

**My Bookings:** `/playerslots/bookings`

Shows two tables — Upcoming (future confirmed) and Past (past or cancelled). Each row has a Cancel button for upcoming bookings.

**Cancellation:** `/playerslots/cancel`

- Player cannot cancel within 24 hours of the session date
- Sets `Status = 'cancelled'`, records `CancelledBy`, `CancelledAt`, `CancelReason = 'Player cancelled'`

---

## 9. Gate Checks

Three checks always run before a booking is accepted. All three are static methods on `SlotBookingService` (`app/libraries/SlotBookingService.php`).

### 9.1 Entitlement Check — `validateEntitlement(playerId, templateId)`

**Skipped for:** private/ad-hoc sessions (`TemplateID = NULL`) and shop_employee bookings.

1. Load `RequiredPlanFeature` from the template
2. If `none` → pass (anyone can book)
3. Find player's active `playersubscription` joined to `membershipplan`
4. No active subscription → `no_subscription` error
5. Check the relevant plan column:

| RequiredPlanFeature | Plan column checked |
|---|---|
| `sessions` | `SessionsPerWeek > 0` |
| `private_sessions` | `PrivateSessionsIncluded > 0` |
| `facility_access` | `FacilityAccessIncluded = 1` |

6. Mismatch → `plan_mismatch` error

### 9.2 Medical Flag — `checkMedicalFlag(playerId, occurrenceDate)`

1. Queries `playermedicalrecord` for any record where:
   - `RecoveryStatus IN ('ongoing', 'recovering')`
   - `DATE_ADD(InjuryDate, INTERVAL RestDaysNeeded DAY) >= occurrenceDate`
2. If found → `active_injury` error with rest days remaining
3. Admin can override a medical flag per booking via the manage-bookings panel (sets `MedicalClearedBy`)

### 9.3 Capacity Check — `checkCapacity(occurrenceId)`

1. Counts confirmed/pending bookings for the occurrence
2. If `OccMax` is set on the occurrence, uses that; otherwise uses `TplMax` from the template; otherwise unlimited
3. `Booked >= max` → `full` error; otherwise returns `spots_left`

**Error code → message mapping (player-facing):**

| Code | Message shown to player |
|---|---|
| `duplicate` | You have already booked this session. |
| `full` | This session is fully booked. |
| `active_injury` | You have an active medical flag. Please see the admin before booking. |
| `no_subscription` | You need an active subscription to book this session. |
| `plan_mismatch` | Your current plan does not include this session type. |
| `not_found` | Session not found. |
| `window_closed` | Bookings for this session are closed. |

---

## 10. Private Sessions (Staff-Created)

Coaches and trainers can create one-off sessions that are not linked to any template.

**Form:** `/staffslots/private_session`

| Field | Required | Notes |
|---|---|---|
| Date | Yes | Cannot be in the past |
| Time Band | Yes | Any active `slot_time_band` |
| Facility | Yes | Any facility |
| Max Participants | No | Defaults to 10 |
| Notes | No | Shown to players on the booking page |

**On submit:**
1. Validates required fields and date
2. Inserts `slot_occurrence` with `TemplateID = NULL`, `Status = 'scheduled'`, `GeneratedBy = userId`
3. Immediately inserts `slot_occurrence_staff_override` row with `StaffRole = 'lead'` and `OverrideReason = 'Private session created by staff'` — this is what makes the session appear on the creator's calendar
4. Redirects to the occurrence detail page
5. `UNIQUE(FacilityID, SlotID, OccurrenceDate)` prevents double-booking; shows "already booked" error

**Visibility:**
- Appears on the creating staff member's Staffslots calendar immediately
- Appears on the player Available Sessions page immediately (no subscription check)

---

## 11. Admin Management

All admin slot routes are under `/adminslots`.

### 11.1 Admin Slots Dashboard (`/adminslots`)

Tabbed interface:
- **Time Bands** — list, toggle active/inactive
- **Templates** — list, create, edit, toggle active/inactive
- **Occurrences Calendar** — week view of all occurrences
- **Generate** — bulk-generate occurrences from a template

### 11.2 Template Management

| Action | Route | Notes |
|---|---|---|
| Create | `POST /adminslots/template/create` | |
| Edit | `POST /adminslots/template/edit/{id}` | Does not change existing occurrences |
| Toggle active | `POST /adminslots/template/toggle/{id}` | Hides/shows all future occurrences |
| Assign staff | `POST /adminslots/template/assign_staff/{id}` | Adds to slot_template_staff |
| Remove staff | `POST /adminslots/template/remove_staff/{id}` | Deletes from slot_template_staff |

### 11.3 Occurrence Management

| Action | Route | Notes |
|---|---|---|
| View detail | `/adminslots/occurrence/{id}` | Shows bookings, staff, status |
| Cancel occurrence | `POST /adminslots/occurrence/{id}` action=cancel | Sets Status=cancelled, writes audit log |
| Substitute staff | `POST /adminslots/occurrence/{id}` action=substitute | Adds override row |
| Manage bookings | Shows all bookings with medical-clear option | |
| Ad-hoc create | `/adminslots/adhoc` | No template needed |

### 11.4 Occurrence Generation

`/adminslots/generate`
1. Select template
2. Select date range
3. System loops, inserts rows, skips duplicates
4. Reports: "X generated, Y already existed"

**Important:** Generation respects `DayOfWeek`. If a template has `DayOfWeek = 1` (Monday) and you generate for a week, only the Monday in that range gets an occurrence.

---

## 12. Staff View (Coach / Trainer)

All staff routes are under `/staffslots`.

### 12.1 Weekly Calendar (`/staffslots/calendar[/{date}]`)

- Defaults to the current week (Mon–Sun)
- Prev/Next navigation by passing a date: `/staffslots/calendar/2026-04-20`
- Shows only occurrences where the logged-in user is assigned (via template or override)
- Colour coding:
  - **Blue** — program session (from a template)
  - **Green** — private session (no template)
  - **Grey strikethrough** — cancelled
- Summary: total sessions this week + total players booked

### 12.2 Occurrence Detail (`/staffslots/occurrence/{id}`)

- Shows session info: date, time, facility, bookings/max, notes, cancel reason
- Attendee list: player name, email, booking status, booked-at time (read-only; no payment data exposed)
- Cancel panel shown only when status is `scheduled` or `active`
- Cancellation requires a reason; confirmation dialog in JS
- **Ownership guard:** if the logged-in staff member is not in `slot_template_staff` OR `slot_occurrence_staff_override` for this occurrence, they are redirected back to the calendar (cannot view or cancel)

### 12.3 Private Session Creation (`/staffslots/private_session`)

See Section 10.

### 12.4 Staff Role Identification

The controller reads `$_SESSION['user_role']`:
- `'Coach'` → `$staffType = 'coach'`
- `'Trainer'` → `$staffType = 'trainer'`

`$staffType` is stored as lowercase because it matches the `slot_occurrence_staff_override.StaffType` enum (`'coach'`/`'trainer'`). If the session contains an unexpected role (e.g. a stale Admin session during development), it safely defaults to `'coach'`.

---

## 13. Audit & Activity Logging

Every write operation writes to two logs:

### `slot_audit_log`
- Fine-grained field-level changes
- `EntityType + EntityID` identifies the record
- `OldValue` / `NewValue` record exactly what changed
- Cannot be modified; append-only

### `activitylog`
- Human-readable summary per action
- Used by the admin activity dashboard
- Example: `"Generated 5 occurrence(s) for template #1 (U15 Batting Practice) from 2026-04-07 to 2026-04-13"`

**Actions that are logged:**

| Action | Logged to |
|---|---|
| Generate occurrences | Both |
| Create ad-hoc / private occurrence | Both |
| Cancel occurrence (admin or staff) | Both |
| Assign / remove template staff | Audit only |
| Staff substitute override | Audit only |
| Player booking created | Both |
| Player booking cancelled | Both |
| Medical flag cleared | Both |

---

## 14. URL Reference

### Player
| URL | Description |
|---|---|
| `/playerslots` | Redirects to available |
| `/playerslots/available` | Browse and book sessions |
| `POST /playerslots/book` | Submit a booking |
| `/playerslots/bookings` | My bookings (upcoming + past) |
| `POST /playerslots/cancel` | Cancel a booking |

### Coach / Trainer
| URL | Description |
|---|---|
| `/staffslots` | Redirects to calendar |
| `/staffslots/calendar` | Weekly calendar (current week) |
| `/staffslots/calendar/{YYYY-MM-DD}` | Weekly calendar for the week containing that date |
| `/staffslots/occurrence/{id}` | Session detail + attendee list + cancel |
| `/staffslots/private_session` | Create a private session (GET=form, POST=submit) |

### Admin
| URL | Description |
|---|---|
| `/adminslots` | Main dashboard (tabbed) |
| `/adminslots/template/create` | Create template |
| `/adminslots/template/edit/{id}` | Edit template |
| `/adminslots/template/toggle/{id}` | Toggle active/inactive |
| `/adminslots/template/assign_staff/{id}` | Assign staff to template |
| `/adminslots/template/remove_staff/{id}` | Remove staff from template |
| `/adminslots/occurrence/{id}` | Occurrence detail + manage |
| `/adminslots/generate` | Bulk-generate occurrences |
| `/adminslots/adhoc` | Create ad-hoc occurrence |

---

## 15. Role Permissions Summary

| Action | Player | Coach | Trainer | Admin |
|---|---|---|---|---|
| Browse available sessions | ✅ | — | — | — |
| Book a session | ✅ | — | — | — |
| Cancel own booking (>24h) | ✅ | — | — | — |
| View weekly calendar (own) | — | ✅ | ✅ | — |
| View occurrence attendee list | — | ✅ (own) | ✅ (own) | ✅ (all) |
| Cancel occurrence (own) | — | ✅ (own) | ✅ (own) | ✅ (all) |
| Create private session | — | ✅ | ✅ | — |
| Create ad-hoc occurrence | — | — | — | ✅ |
| Create / edit templates | — | — | — | ✅ |
| Generate occurrences | — | — | — | ✅ |
| Assign / substitute staff | — | — | — | ✅ |
| Toggle time band active | — | — | — | ✅ |
| Clear medical flag | — | — | — | ✅ |
| View audit log | — | — | — | ✅ |

---

## Key Business Rules (Quick Reference)

1. **No template = no bookable sessions.** Occurrences must be generated from a template, or created manually as ad-hoc/private sessions.
2. **Deactivating a template hides all its future occurrences from players** but does not delete them.
3. **`UNIQUE(FacilityID, SlotID, OccurrenceDate)`** — a facility cannot host two different sessions in the same time band on the same day.
4. **Private / ad-hoc sessions bypass subscription checks.** Any player can book them regardless of plan.
5. **Medical flags block bookings.** Only an admin can clear the flag per booking via `MedicalClearedBy`.
6. **Staff ownership guard.** A coach/trainer can only view and cancel occurrences they are assigned to. This is enforced at the model layer — not just the view.
7. **Cancellations are soft deletes.** Status is set to `cancelled`; the row is never deleted from `slot_occurrence` or `slot_booking`.
8. **Player self-cancellation window is 24 hours.** Attempting to cancel within 24 hours of the session date returns a `window_closed` error.
9. **Duplicate booking is prevented at the DB level.** `UNIQUE(OccurrenceID, PlayerID)` on `slot_booking` means even if a player somehow makes two requests simultaneously, only one will succeed.
10. **Staff type must match.** When assigning staff to a template, `StaffType` on the assignment must match the template's `StaffType` field. Coaches assigned to a trainer-type template are rejected and vice versa.
