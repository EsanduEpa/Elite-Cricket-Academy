# Multi-Coach Assignment And Recurring Program Auto-Allocation

This document captures the full change set required to support:

1. Assigning one player to up to three different coaches at the same time:
   - one batting coach
   - one bowling coach
   - one fielding coach
2. Making those assignments age-group-aware.
3. Automatically assigning recurring group program templates to the correct students so players do not need to book those sessions manually.

No SQL in this document has been executed.

## Current Problem

The current system uses `playercoachassignment` as a generic single-assignment model. That structure is too broad for the new requirement because it does not cleanly enforce:

- one batting coach per player
- one bowling coach per player
- one fielding coach per player
- age-group-based coach eligibility
- automatic recurring program allocation from coach/student assignment rules

The slot module already has useful fields on `slot_template`:

- `SlotType`
- `AgeGroup`
- `Category`

Those fields can be used to drive program auto-allocation.

## Recommended Design

Keep the existing `playercoachassignment` table for legacy/general usage.

Add three new structures:

1. `coach_skill_age_group_assignment`
   Purpose: define which coach can handle which coaching type for which age group.

2. `player_skill_coach_assignment`
   Purpose: store the current active batting/bowling/fielding coach per player.

3. `slot_template_player_assignment`
   Purpose: explicitly map recurring `program` templates to the players who should receive them automatically.

Also extend `slot_booking.BookingSource` to include `system` so recurring program enrollments can be distinguished from manual bookings.

## Age Group Rule

Use player age from `user.DateOfBirth` and convert it into an age group in application logic or SQL.

Suggested mapping:

- `Under 11` => age 5 to 10
- `Under 13` => age 11 to 12
- `Under 15` => age 13 to 14
- `Under 17` => age 15 to 16
- `Under 19` => age 17 to 18
- `Open` => age 19+

If your academy already uses a different age-group vocabulary, keep the same labels in both coach assignment and slot templates.

## SQL Changes

### 1. Coach skill and age-group capability table

```sql
CREATE TABLE IF NOT EXISTS coach_skill_age_group_assignment (
    CoachSkillGroupID INT(11) NOT NULL AUTO_INCREMENT,
    CoachID           INT(11) NOT NULL,
    CoachingType      ENUM('batting','bowling','fielding') NOT NULL,
    AgeGroup          VARCHAR(50) NOT NULL,
    PriorityRank      INT(11) NOT NULL DEFAULT 1,
    IsActive          TINYINT(1) NOT NULL DEFAULT 1,
    Notes             TEXT DEFAULT NULL,
    AssignedBy        INT(11) DEFAULT NULL,
    CreatedAt         DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt         DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (CoachSkillGroupID),
    UNIQUE KEY uq_csg_coach_skill_age (CoachID, CoachingType, AgeGroup),
    KEY idx_csg_skill_age (CoachingType, AgeGroup, IsActive),
    CONSTRAINT fk_csg_coach FOREIGN KEY (CoachID) REFERENCES coachprofile (CoachID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_csg_assigned_by FOREIGN KEY (AssignedBy) REFERENCES user (UserID) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Seed coach capability rows from existing coachprofile data

This gives you a starting point from `coachprofile.Specialization`. It uses `Open` as the initial age group, and admins can refine the rows later.

```sql
INSERT IGNORE INTO coach_skill_age_group_assignment
    (CoachID, CoachingType, AgeGroup, PriorityRank, IsActive, Notes)
SELECT cp.CoachID, 'batting', 'Open', 1, 1, 'Seeded from coachprofile specialization'
FROM coachprofile cp
WHERE cp.Specialization IN ('Batting', 'All-rounder');

INSERT IGNORE INTO coach_skill_age_group_assignment
    (CoachID, CoachingType, AgeGroup, PriorityRank, IsActive, Notes)
SELECT cp.CoachID, 'bowling', 'Open', 1, 1, 'Seeded from coachprofile specialization'
FROM coachprofile cp
WHERE cp.Specialization IN ('Bowling', 'All-rounder');

INSERT IGNORE INTO coach_skill_age_group_assignment
    (CoachID, CoachingType, AgeGroup, PriorityRank, IsActive, Notes)
SELECT cp.CoachID, 'fielding', 'Open', 2, 1, 'Seeded from all-rounder specialization; refine manually'
FROM coachprofile cp
WHERE cp.Specialization = 'All-rounder';
```

### 3. Player to skill-coach assignment table

This table stores the current live assignment only. The composite primary key enforces exactly one active coach per player per coaching type.

```sql
CREATE TABLE IF NOT EXISTS player_skill_coach_assignment (
    PlayerID          INT(11) NOT NULL,
    CoachingType      ENUM('batting','bowling','fielding') NOT NULL,
    CoachID           INT(11) NOT NULL,
    AgeGroup          VARCHAR(50) NOT NULL,
    AssignmentSource  ENUM('auto_registration','admin_manual','system_refresh') NOT NULL DEFAULT 'auto_registration',
    AssignedBy        INT(11) DEFAULT NULL,
    AssignedAt        DATETIME DEFAULT CURRENT_TIMESTAMP,
    Notes             TEXT DEFAULT NULL,
    PRIMARY KEY (PlayerID, CoachingType),
    KEY idx_psca_coach (CoachID),
    KEY idx_psca_age_group (AgeGroup),
    CONSTRAINT fk_psca_player FOREIGN KEY (PlayerID) REFERENCES user (UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_psca_coach FOREIGN KEY (CoachID) REFERENCES coachprofile (CoachID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_psca_assigned_by FOREIGN KEY (AssignedBy) REFERENCES user (UserID) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. Recurring program assignment table

This table tracks which players are entitled to which recurring `program` templates.

```sql
CREATE TABLE IF NOT EXISTS slot_template_player_assignment (
    TemplateID         INT(11) NOT NULL,
    PlayerID           INT(11) NOT NULL,
    CoachingType       ENUM('batting','bowling','fielding') DEFAULT NULL,
    AgeGroup           VARCHAR(50) DEFAULT NULL,
    AssignmentSource   ENUM('auto_plan','admin_manual','system_refresh') NOT NULL DEFAULT 'auto_plan',
    AssignedBy         INT(11) DEFAULT NULL,
    AssignedAt         DATETIME DEFAULT CURRENT_TIMESTAMP,
    IsActive           TINYINT(1) NOT NULL DEFAULT 1,
    Notes              TEXT DEFAULT NULL,
    PRIMARY KEY (TemplateID, PlayerID),
    KEY idx_stpa_player (PlayerID, IsActive),
    CONSTRAINT fk_stpa_template FOREIGN KEY (TemplateID) REFERENCES slot_template (TemplateID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_stpa_player FOREIGN KEY (PlayerID) REFERENCES user (UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_stpa_assigned_by FOREIGN KEY (AssignedBy) REFERENCES user (UserID) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Extend slot booking source for automatic recurring enrollments

```sql
ALTER TABLE slot_booking
MODIFY BookingSource ENUM('self','admin','shop_employee','system') NOT NULL DEFAULT 'self';
```

## Coach Selection Query

This query returns the best coach for one player, one skill, and one age group.

Selection order:

1. matching coaching type
2. matching age group or `Open`
3. active coach only
4. lowest current assigned load first
5. highest `PriorityRank`
6. highest experience

```sql
SELECT
    csg.CoachID,
    u.Name,
    cp.Specialization,
    cp.Experience,
    csg.CoachingType,
    csg.AgeGroup,
    csg.PriorityRank,
    COUNT(psca.PlayerID) AS CurrentLoad
FROM coach_skill_age_group_assignment csg
JOIN coachprofile cp
    ON cp.CoachID = csg.CoachID
JOIN user u
    ON u.UserID = csg.CoachID
LEFT JOIN player_skill_coach_assignment psca
    ON psca.CoachID = csg.CoachID
   AND psca.CoachingType = csg.CoachingType
WHERE csg.IsActive = 1
  AND u.Status = 'active'
  AND csg.CoachingType = :coaching_type
  AND csg.AgeGroup IN (:player_age_group, 'Open')
GROUP BY csg.CoachID, u.Name, cp.Specialization, cp.Experience, csg.CoachingType, csg.AgeGroup, csg.PriorityRank
ORDER BY
    CASE WHEN csg.AgeGroup = :player_age_group THEN 0 ELSE 1 END,
    CurrentLoad ASC,
    csg.PriorityRank ASC,
    cp.Experience DESC,
    u.Name ASC
LIMIT 1;
```

Note: in PHP you should bind either one query for the exact age group and another fallback query for `Open`, or build the SQL with separate placeholders because PDO cannot bind an array directly to `IN (...)`.

## Registration-Time Auto Assignment Flow

Apply this only when the selected membership plan is `Basic` or `Pro`.

If your database uses different plan names, replace the plan-name check with the correct values. Another valid option is to drive this from plan features instead of the name.

### SQL to clear and rebuild a player's skill assignments

```sql
DELETE FROM player_skill_coach_assignment
WHERE PlayerID = :player_id;
```

Then insert one row each for batting, bowling, and fielding using the coach-selection query above.

Example insert shape:

```sql
INSERT INTO player_skill_coach_assignment
    (PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, Notes)
VALUES
    (:player_id, :coaching_type, :coach_id, :age_group, 'auto_registration', NULL, 'Assigned automatically during registration');
```

## Recurring Group Program Auto-Allocation

The slot system already has:

- `slot_template.SlotType`
- `slot_template.AgeGroup`
- `slot_template.Category`

Use those fields to determine which recurring group templates belong to each player.

### Rule

For each player skill assignment:

- only match templates where `SlotType = 'program'`
- match `Category` to the coaching type
  - batting coach -> batting programs
  - bowling coach -> bowling programs
  - fielding coach -> fielding programs
- match `AgeGroup` to the player's current age group, or allow `Open`
- only include active templates

### Query to refresh program-template assignments for one player

```sql
DELETE FROM slot_template_player_assignment
WHERE PlayerID = :player_id
  AND AssignmentSource IN ('auto_plan', 'system_refresh');

INSERT INTO slot_template_player_assignment
    (TemplateID, PlayerID, CoachingType, AgeGroup, AssignmentSource, AssignedBy, Notes)
SELECT
    st.TemplateID,
    psca.PlayerID,
    psca.CoachingType,
    psca.AgeGroup,
    'auto_plan',
    NULL,
    'Auto-assigned from player skill coach assignment and age group'
FROM player_skill_coach_assignment psca
JOIN slot_template st
    ON st.SlotType = 'program'
   AND st.IsActive = 1
   AND LOWER(COALESCE(st.Category, '')) = psca.CoachingType
   AND COALESCE(st.AgeGroup, 'Open') IN (psca.AgeGroup, 'Open')
WHERE psca.PlayerID = :player_id;
```

## Automatically enroll assigned players into generated occurrences

Once a recurring occurrence is created, the system should automatically create `slot_booking` rows for all players assigned to that template.

### Query for occurrence-time auto enrollment

```sql
INSERT INTO slot_booking
    (OccurrenceID, PlayerID, BookingSource, SubscriptionID, Status, AmountCharged, PaymentStatus, PaymentMethod, BookedBy)
SELECT
    so.OccurrenceID,
    stpa.PlayerID,
    'system',
    ps.SubscriptionID,
    'confirmed',
    0.00,
    'not_required',
    NULL,
    NULL
FROM slot_occurrence so
JOIN slot_template_player_assignment stpa
    ON stpa.TemplateID = so.TemplateID
   AND stpa.IsActive = 1
LEFT JOIN playersubscription ps
    ON ps.PlayerID = stpa.PlayerID
   AND ps.Status = 'active'
LEFT JOIN slot_booking sb
    ON sb.OccurrenceID = so.OccurrenceID
   AND sb.PlayerID = stpa.PlayerID
WHERE so.OccurrenceID = :occurrence_id
  AND sb.BookingID IS NULL;
```

This keeps recurring program enrollments out of the manual booking flow.

## Player Visibility Rule

This is the required player-facing behavior:

- recurring `program` templates must **not** appear on the `Book Sessions` page
- players must **not** manually book recurring group programs
- once the system assigns the player to the right recurring programs, those sessions must appear in the player's:
    - schedule tables
    - training/schedule pages
    - calendar views
    - booking/history lists where appropriate

In short:

- `facility_only` and `private` remain opt-in bookable items
- `program` becomes assignment-driven, not booking-driven from the player's side

## PHP Changes Required

### 1. Registration flow

File: `app/controllers/Register.php`

After `createPlayerSubscription(...)` succeeds:

- determine whether the selected plan is `Basic` or `Pro`
- derive the player's age group from `dateOfBirth`
- call a new model method to:
  - assign batting coach
  - assign bowling coach
  - assign fielding coach
  - populate `slot_template_player_assignment`

Recommended new call:

```php
$this->userModel->autoAssignSkillCoachesAndPrograms($userId, (int)$data['membershipPlan']);
```

### 2. User model

File: `app/models/M_Users.php`

Add methods:

- `getAgeGroupForDateOfBirth(string $dob): string`
- `planRequiresCoachAllocation(int $planId): bool`
- `getBestCoachForSkill(string $ageGroup, string $coachingType): ?object`
- `replacePlayerSkillCoachAssignments(int $playerId, string $ageGroup, ?int $assignedBy = null): bool`
- `refreshPlayerProgramTemplateAssignments(int $playerId): bool`
- `autoAssignSkillCoachesAndPrograms(int $playerId, int $planId): bool`

### 3. Coach dashboard / assigned-player queries

Files currently tied to `playercoachassignment`:

- `app/models/M_Users.php`
- `app/models/M_CoachTournamentRecommendation.php`
- `app/controllers/Coach.php`

These reads should be updated so a coach sees players assigned to them through `player_skill_coach_assignment` too.

Recommended behavior:

- coach dashboards list players where `player_skill_coach_assignment.CoachID = current coach`
- display the skill label beside the player assignment
- if legacy `playercoachassignment` must remain visible, union both sources and label them clearly

### 4. Slot generation hook

File: `app/models/M_SlotAdmin.php`

Method: `generateOccurrences(...)`

After each successful `slot_occurrence` insert:

- if the template is a `program`
- insert missing `slot_booking` rows using the auto-enrollment query above

Recommended helper:

```php
$this->autoEnrollAssignedPlayersForOccurrence($newId);
```

### 5. Player slot browsing

File: `app/models/M_SlotPlayer.php`

Program templates that are auto-assigned should not behave like opt-in bookable sessions anymore.

Recommended behavior:

- `facility_only` and `private` stay manually bookable
- `program` occurrences must be excluded from `getAvailableOccurrences()` so they do not show on the `Book Sessions` page
- `program` occurrences should appear in the player's own assigned schedule because they are system-enrolled into `slot_booking`
- `getPlayerBookings()` should continue to return them, but the UI should label them as auto-assigned when `BookingSource = 'system'`

### 6. Player schedule and calendar integration

Files:

- `app/controllers/Player.php`
- any underlying `M_Session` queries currently feeding training and calendar views

Current player calendar/training pages are still driven by `M_Session`, while the new slot module stores assigned recurring programs in `slot_occurrence` and `slot_booking`.

So this feature needs one integration step:

- update the player's training schedule data source so slot-based recurring program bookings are included in the schedule tables
- update the player's calendar data source so slot-based recurring program bookings are included in the calendar event list
- those rows should be read from `slot_booking` + `slot_occurrence` for `BookingSource = 'system'` and `SlotType = 'program'`

Recommended rule for those views:

- show assigned recurring programs as scheduled sessions
- do not show a `Book Now` action for them
- optionally show a badge such as `Assigned Program` or `Auto-Enrolled`

## Suggested Transaction Order

When a qualifying player registers:

1. create user
2. create player profile
3. create subscription
4. derive age group
5. create batting/bowling/fielding coach assignments
6. create recurring template assignments
7. insert notifications to the assigned coaches and player

Wrap steps 3 to 6 in one DB transaction so registration does not produce half-finished assignments.

## Optional Notification Inserts

```sql
INSERT INTO notification (UserID, Type, Title, Message)
VALUES
(:player_id, 'coach_assignment', 'Coaches Assigned', 'Your batting, bowling, and fielding coaches have been assigned based on your age group and membership plan');
```

```sql
INSERT INTO notification (UserID, Type, Title, Message)
VALUES
(:coach_id, 'player_assignment', 'New Player Assigned', 'A player has been assigned to you for a coaching skill and age group');
```

## Important Notes

1. `coachprofile.Specialization` alone is not enough for the new requirement because it has no age-group dimension and no dedicated fielding specialization.
2. The new `coach_skill_age_group_assignment` table solves that cleanly without breaking current coach profiles.
3. `player_skill_coach_assignment` should be treated as the authoritative live assignment table for this feature.
4. `slot_template_player_assignment` is what removes the need for manual booking of recurring group templates.
5. The existing `playercoachassignment` table can stay in place for backward compatibility until the rest of the app is migrated.

## Files Impacted

- `app/controllers/Register.php`
- `app/models/M_Users.php`
- `app/models/M_SlotAdmin.php`
- `app/models/M_SlotPlayer.php`
- `app/controllers/Coach.php`
- `app/models/M_CoachTournamentRecommendation.php`
- `create_slot_tables.sql` or a new dedicated SQL migration file

## Recommended Rollout

1. Create the new tables.
2. Seed coach capability rows from `coachprofile`.
3. Add the new user-model assignment methods.
4. Update registration to call the new assignment flow.
5. Update slot occurrence generation to auto-enroll assigned players.
6. Remove recurring `program` templates from the `Book Sessions` page.
7. Update player schedule and calendar queries so system-enrolled recurring programs appear there correctly.
8. Update coach-facing queries to use the new assignment table.
