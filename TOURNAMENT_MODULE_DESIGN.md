# Tournament Module Design

**Version:** 1.0  
**Date:** 2025  
**Status:** Planning — not yet implemented  
**Author:** GitHub Copilot  
**Related documents:** SLOTS_MODULE_DESIGN.md, SESSIONS_MODULE_GUIDE.md

---

## Table of Contents

1. [Problem Statement](#1-problem-statement)
2. [How This Module Connects to Every Role](#2-how-this-module-connects-to-every-role)
3. [Tournament Lifecycle State Machine](#3-tournament-lifecycle-state-machine)
4. [Real-Life Scenario Walkthrough](#4-real-life-scenario-walkthrough)
5. [Database Changes — Existing Tables to Alter](#5-database-changes--existing-tables-to-alter)
6. [New Tables to Create](#6-new-tables-to-create)
7. [Migration Sequence](#7-migration-sequence)
8. [Controllers Needed](#8-controllers-needed)
9. [Views Needed](#9-views-needed)
10. [Models Needed](#10-models-needed)
11. [Head Coach Workflow](#11-head-coach-workflow)
12. [URL Reference](#12-url-reference)
13. [Role Permissions Table](#13-role-permissions-table)
14. [Key Business Rules](#14-key-business-rules)
15. [Implementation Checklist](#15-implementation-checklist)

---

## 1. Problem Statement

### What Exists Today

The database already has a `tournament` table and a `tournamentplayer` table. The `coachprofile` table has an `IsHeadCoach` flag. There is a partially-built `M_CoachTournamentRecommendation` model and five methods in `Coach.php` that handle recommendation display and saving. There is also a SQL migration file (`add_coach_tournament_recommendations.sql`) that has not yet been run.

Despite this foundation, the current state has six critical gaps:

### Gap 1 — No Admin management for tournaments

The `Admin.php` controller has full CRUD for **events** (create_event, edit_event, delete_event, calendar). It has **zero** tournament management functions. A tournament can only enter the database by raw SQL insert. There is no way for the admin to create a tournament through the UI.

### Gap 2 — Players can only see but not act

`Player.php::tournaments()` shows a merged list of events and tournaments — all read-only. Players have no mechanism to express interest in a tournament. There is no `tournament_join_request` table.

### Gap 3 — Trainer recommendations don't exist

`M_CoachTournamentRecommendation` handles coach recommendations only. The `coach_tournament_recommendations` table enforces `CoachID` (FK to `coachprofile`). Trainers who observe players during fitness sessions have no route to recommend players for tournaments.

### Gap 4 — Head Coach has no interface

`coachprofile.IsHeadCoach = 1` exists in the schema but is referenced nowhere in the PHP codebase: no controller check, no dedicated view, no workflow. The head coach is supposed to finalize team selection but there is no mechanism to do so.

### Gap 5 — The `tournament` table is too sparse

The current `tournament` schema has: `TournamentID, Name, tdate, Location, CreatedBy, Status, PrizePool`. It is missing: age group, format (T20/ODI/Test), description, registration deadline, max players, whether the team has been publicly announced, and result summary. Without these fields, the module cannot distinguish a U15 T20 cup from an open batting trial.

### Gap 6 — No results entry or announcement

`playertournamentstats` exists to record per-player statistics but there is no tournament-level result table (final standings, opponent, winner). There is also no "team announced" flag visible to players, so there is no moment when the team selection becomes public.

### Summary of What Must Be Built

| Area | Action |
|---|---|
| `tournament` table | ALTER — add 7 new columns |
| `coach_tournament_recommendations` | CREATE — run existing migration SQL |
| `trainer_tournament_recommendations` | CREATE — new table (mirrors coach version) |
| `tournament_join_request` | CREATE — new table for player interest |
| `tournament_result` | CREATE — new table for final standings |
| Admin tournament CRUD | BUILD — new controller methods + views |
| Player join request flow | BUILD — new controller + view |
| Trainer recommendation flow | BUILD — new controller methods + view |
| Head coach finalization interface | BUILD — head-coach-gated view in Coach.php |
| Team announcement visibility | BUILD — public team page for all roles |
| Results entry | BUILD — head coach or admin can enter results |

---

## 2. How This Module Connects to Every Role

### Admin (Priya)

Priya is the system operator. She creates and manages all tournaments and events through the admin panel. For tournaments, her responsibilities are:

- **Create** a tournament with all mandatory fields (name, age group, format, dates, registration deadline, max players)
- **Open and close** registration so the system knows when join requests are accepted
- **Review** player join requests and approve or reject them
- **Monitor** the recommendation pipeline: see all coach and trainer recommendations for a tournament
- **Publish the team** once the head coach has finalized selection (flip `IsTeamAnnounced = 1`)
- **Enter results** after the tournament or delegate this to the head coach
- **Cancel** tournaments with a reason

Priya already manages **events** (holiday camps, lectures, seminars) through the existing admin event CRUD. The tournament module adds a parallel workflow to her panel — tournaments require a selection process; events use simple enrollment.

### Head Coach (Coach Ravi, IsHeadCoach = 1)

Ravi sees the full picture. He is the only staff member who can **finalize** the team roster. His workflow:

- View **all recommendations** submitted for a tournament (from all coaches + all trainers)
- View **all join requests** from players (see who requested, who was recommended, who has neither)
- **Select players** from the combined pool and assign them to the team (`tournamentplayer`)
- **Announce the team** — marks the tournament roster as ready for public viewing
- **Enter or edit results** after the tournament ends
- He also uses the standard coach recommendation flow for his own assigned players

Ravi's panel has a gate: methods that involve finalization check `coachprofile.IsHeadCoach = 1` for the logged-in coach. If a coach does not have that flag, those menu items are hidden and the routes redirect with an error.

### Regular Coach (Coach Sarath, IsHeadCoach = 0)

Sarath sees tournaments and has one form of agency:

- Browse upcoming tournaments (with their age group, format, deadline)
- **Recommend players** from his assigned roster (players linked to him via `playercoachassignment`)
- Submit a recommendation with a role, reason, and comments
- Track the status of his submitted recommendations (pending / approved by head coach / rejected)
- Cannot finalize the team — the finalization UI is hidden

### Trainer (Trainer Nimal)

Nimal works closely with players during fitness sessions. He has direct insight into physical conditioning, endurance, and injury history. His route:

- Browse upcoming tournaments
- **Recommend players** from the full player pool (trainers are not bound to `playercoachassignment` — they work with all players)
- Submit a recommendation with a fitness-focused reason and comments
- Track his submitted recommendations
- Cannot view other trainers' or coaches' recommendation reasons (only his own)
- Cannot finalize the team

> **Why trainers reference the full player pool?** Trainers are assigned to programs (via `slot_template_staff`) but not to individual players via `playercoachassignment`. Restricting trainer recommendations to that table would silently block all trainer recommendations. Trainers should be able to recommend any active player they have observed.

### Player (Ashan)

Ashan logs in and sees the tournaments page:

- Browse all tournaments with status `upcoming` or `registration_open`
- View tournament details: format, age group, location, prize pool, deadline
- **Submit a join request** for tournaments with status `registration_open`
- Track the status of his join request: `pending`, `approved`, `rejected`
- Once the team is announced (`IsTeamAnnounced = 1`), see the full team list
- See his own performance stats if he participated in a completed tournament

Players do **not** see who recommended them, who submitted join requests alongside them, or the internal coach/trainer deliberation. They see only tournament metadata and their personal status.

---

## 3. Tournament Lifecycle State Machine

A tournament moves through exactly six states. Each transition has a defined actor and a defined set of tables that change.

```
created
   │
   │  Admin opens registration
   ▼
registration_open
   │
   │  Admin closes registration (deadline passes or manually closed)
   ▼
registration_closed
   │
   │  Head coach finalizes team + Admin publishes
   ▼
team_announced
   │
   │  Tournament start date reached / Admin sets ongoing
   ▼
ongoing
   │
   │  Head coach / Admin enters results
   ▼
completed
```

A `cancelled` escape hatch is available from any state before `ongoing` (and admin can still cancel an ongoing tournament with a reason). A cancelled tournament does not have results entered.

### State Transition Rules

| From | To | Actor | What Changes |
|---|---|---|---|
| (new) | `created` | Admin | Row inserted in `tournament`; status = `created` |
| `created` | `registration_open` | Admin | `tournament.Status` = `registration_open`; players can now submit join requests |
| `registration_open` | `registration_closed` | Admin | `tournament.Status` = `registration_closed`; join request form hidden from players |
| `registration_closed` | `team_announced` | Head Coach + Admin | Head coach finalizes `tournamentplayer`; Admin sets `IsTeamAnnounced = 1` and `Status = team_announced` |
| `team_announced` | `ongoing` | Admin | `tournament.Status` = `ongoing`; team is locked (no further edits to `tournamentplayer`) |
| `ongoing` | `completed` | Head Coach or Admin | Results entered in `tournament_result`; stats entered in `playertournamentstats`; status = `completed` |
| Any (pre-ongoing) | `cancelled` | Admin | `tournament.Status` = `cancelled`; `tournament.CancelReason` set; all pending join requests auto-rejected |

---

## 4. Real-Life Scenario Walkthrough

> This walkthrough follows the complete lifecycle of the **"Sri Lanka U16 District T20 Cup 2025"** from the admin's desk to the live results page.

---

#### Week 1, Monday — Admin creates the tournament

Priya logs into the admin panel and opens **Tournaments → Create Tournament**. She fills in:
- Name: `Sri Lanka U16 District T20 Cup 2025`
- Age Group: `Under 16`
- Format: `T20`
- Location: `Colombo Cricket Ground`
- Date: `2025-09-20`
- Registration Deadline: `2025-08-31`
- Max Players: `18`
- Prize Pool: `Rs. 50,000`
- Description: "Annual district T20 competition. Squad of 15 playing members + 3 reserves."

The system inserts a row with `Status = 'created'`. No players can see a join request button yet.

> **The extended `tournament` table is needed here.**
> Without `AgeGroup`, Priya cannot separate this U16 tournament from a U19 one. Without `Format`, the academy has no record of whether this is a T20 or 50-over competition. Without `MaxPlayers`, the system has no capacity limit and the head coach might finalize a squad of 30. Without `RegistrationDeadline`, there is no automatic cutoff — the admin must manually remember to close registration. Without `Is TeamAnnounced`, players will never know if a team has been confirmed.

---

#### Week 1, Thursday — Admin opens registration

Priya changes the status to `registration_open`. The tournament now appears on the player portal with an **"Apply to Join"** button visible to all players who are 16 or under.

---

#### Weeks 2–5 — Players submit join requests

Player Ashan (under-16) browses the tournament page and clicks **"Apply to Join"**. He writes a short message: *"I have been playing U15 district cricket for 2 seasons and would like to be considered for the Under 16 squad."*

The system inserts a row in `tournament_join_request`:
```
TournamentID = 12, PlayerID = 34, Message = '...', Status = 'pending', RequestedAt = now()
```

By the deadline, 37 players have submitted join requests. The admin dashboard shows: **37 join requests pending review**.

> **`tournament_join_request` is needed here.**
> Without this table, players have no way to formally signal interest. Coaches would have no knowledge of who volunteered. The head coach would be selecting from only the players his colleagues happen to remember. Join requests create a documented, auditable intake pool.

---

#### Weeks 2–5 — Coaches submit recommendations

Coach Sarath reviews his assigned players (linked via `playercoachassignment`). He believes Ashan should be in the squad. He opens **Tournaments → Recommend Players**, selects the tournament, selects Ashan, and fills the form:
- Recommended Role: `Batsman`
- Reason: `Excellent T20 strike rate — 138.5 in the last inter-school series`
- Comments: `Should open the innings. Works well under pressure.`

The system inserts a row in `coach_tournament_recommendations`:
```
CoachID = 5, TournamentID = 12, PlayerID = 34, RecommendedRole = 'Batsman',
Reason = '...', Status = 'pending'
```

> **`coach_tournament_recommendations` is needed here.**
> Without it, Sarath's professional judgment is stored nowhere. The head coach selects players with no record of who endorsed whom or why. If the team performs poorly, there is no audit trail of what information the head coach was given. The recommendation table is the structured evidence that supports every selection decision.

---

#### Weeks 2–5 — Trainer submits a fitness recommendation

Trainer Nimal has been working with Player Kasun in fitness sessions. He knows Kasun's sprint endurance and agility are exceptional. He opens **Tournaments → Recommend Players** and submits:
- Player: Kasun
- Reason: `Top-tier sprint recovery time. Best VO2 max scores in the Under 16 group.`
- Comments: `Ideal as a running between the wickets asset. Recommend playing him at 5 or 6.`

The system inserts a row in `trainer_tournament_recommendations`:
```
TrainerID = 8, TournamentID = 12, PlayerID = 67, RecommendedRole = 'Batsman',
Reason = '...', Status = 'pending'
```

> **`trainer_tournament_recommendations` is needed here — separate from the coach table.**
> Trainers are not in `coachprofile`. A `CoachID` foreign key in a shared recommendations table would exclude them. A separate table preserves the FK integrity without compromising the coach-only constraint on `coach_tournament_recommendations`. The head coach can then query both tables with a UNION and see the full picture.

---

#### Week 6 — Admin closes registration

Priya sets status to `registration_closed`. The "Apply to Join" button disappears from the player portal. Existing requests remain visible to the head coach.

---

#### Week 7 — Head coach finalizes the team

Coach Ravi (IsHeadCoach = 1) opens **Tournaments → Finalize Team**. He sees three panels:

1. **Join Requests** — 37 players, their message, and how many coaches/trainers recommended them
2. **Coach Recommendations** — every recommendation from every coach, with reason and role
3. **Trainer Recommendations** — every recommendation from every trainer, with reason and role

He selects 15 players for the playing squad and 3 as reserves. For each player he assigns a `RoleInTeam` (Captain, Batsman, Bowler, etc.). The system writes rows to `tournamentplayer`:

```
TournamentID = 12, PlayerID = 34, Team = 'A', RoleInTeam = 'Batsman', SelectedBy = Ravi's CoachID
```

> **The head coach gate is needed here.**
> Without `coachprofile.IsHeadCoach = 1` as an access control, any coach could finalize (or overwrite) the team. The head coach is the designated selector. All other coaches can only submit recommendations.

---

#### Week 8 — Admin announces the team

Ravi signals to Priya that selection is complete. Priya opens the tournament in the admin panel and clicks **"Publish Team"**, which sets:
```
tournament.IsTeamAnnounced = 1
tournament.Status = 'team_announced'
```

Immediately, any user who opens the tournament detail page sees the full squad list with roles. Players who were not selected can see they were not included. Coach recommendations and trainer reasons remain internal — players never see them.

> **`IsTeamAnnounced` is needed here.**
> Without this flag, there is no clean moment at which the team becomes public. Without it, the system would have to infer team visibility from status, which creates race conditions (what if status is `ongoing` but the head coach hasn't confirmed the squad yet?). The flag is a single-bit, explicit commitment.

---

#### Week 9 — Tournament runs

Status is set to `ongoing`. The team roster is locked. No new players can be added to `tournamentplayer`. Players, coaches, trainers, and admins can all see the squad.

---

#### After the tournament — Results and stats entered

Ravi opens **Tournaments → Enter Results**. He fills in:
- Academy team position: `1st` (winners)
- Final opponent: `Kandy District`
- Won by: `8 wickets`
- Man of the Tournament: Ashan (PlayerID = 34)

He also fills per-player stats for each of the 15 playing members: runs, wickets, matches.

The system inserts a row in `tournament_result` and updates rows in `playertournamentstats`.

Status is set to `completed`. Everyone can now see the result and each player's personal stats on their profile.

> **`tournament_result` is needed here.**
> `playertournamentstats` covers individual performance but says nothing about the team outcome. Who did the academy beat? Did they win? There is no place to record "won by 8 wickets in the final against Kandy District". That information is academy-level evidence of competitive success — crucial for sponsor reports, committee reviews, and player development records.

---

### The Whole Tournament, Summarised

| Stage | Actor | Tables Touched |
|---|---|---|
| Create tournament | Admin | `tournament` (INSERT) |
| Open registration | Admin | `tournament.Status` update |
| Player applies | Player | `tournament_join_request` (INSERT) |
| Coach recommends player | Coach | `coach_tournament_recommendations` (INSERT) |
| Trainer recommends player | Trainer | `trainer_tournament_recommendations` (INSERT) |
| Close registration | Admin | `tournament.Status` update |
| Head coach reviews all inputs | Head Coach | reads `tournament_join_request`, `coach_tournament_recommendations`, `trainer_tournament_recommendations` |
| Head coach finalizes squad | Head Coach | `tournamentplayer` (INSERT/DELETE) |
| Admin announces team | Admin | `tournament.IsTeamAnnounced=1`, `tournament.Status` update |
| Tournament runs | All (read-only) | `tournament`, `tournamentplayer` |
| Results entered | Head Coach / Admin | `tournament_result` (INSERT), `playertournamentstats` (INSERT/UPDATE) |
| Status → completed | Admin | `tournament.Status` update |

No step is redundant. Each step requires a table that does not already exist in the schema.

---

## 5. Database Changes — Existing Tables to Alter

### 5.1 `tournament` — Add Seven Columns

The existing table has only six columns. Seven additional columns are needed:

```sql
ALTER TABLE `tournament`
  ADD COLUMN `AgeGroup`             VARCHAR(50)   DEFAULT NULL
    COMMENT 'Under 13, Under 15, Under 16, Under 19, Open'
    AFTER `Name`,

  ADD COLUMN `Format`               ENUM('T20','ODI','Test','Other') DEFAULT 'T20'
    COMMENT 'Match format for the tournament'
    AFTER `AgeGroup`,

  ADD COLUMN `Description`          TEXT          DEFAULT NULL
    COMMENT 'Full description shown to players'
    AFTER `Format`,

  ADD COLUMN `RegistrationDeadline` DATE          DEFAULT NULL
    COMMENT 'Last date for player join requests'
    AFTER `tdate`,

  ADD COLUMN `MaxPlayers`           SMALLINT(6)   DEFAULT NULL
    COMMENT 'Maximum squad size the head coach can confirm'
    AFTER `RegistrationDeadline`,

  ADD COLUMN `IsTeamAnnounced`      TINYINT(1)    NOT NULL DEFAULT 0
    COMMENT '1 = team roster is visible to all users'
    AFTER `MaxPlayers`,

  ADD COLUMN `CancelReason`         TEXT          DEFAULT NULL
    COMMENT 'Required when Status = cancelled'
    AFTER `Status`;
```

**Existing columns retained as-is:**

| Column | Type | Notes |
|---|---|---|
| `TournamentID` | INT AUTO_INCREMENT | PK |
| `Name` | VARCHAR(100) | Tournament display name |
| `tdate` | DATE | Tournament date |
| `Location` | VARCHAR(255) | Venue |
| `CreatedBy` | INT | FK → user |
| `Status` | ENUM | Currently: upcoming/ongoing/completed/cancelled — **must expand** |
| `PrizePool` | DECIMAL | Optional prize pool |

**Status ENUM expansion:**

```sql
ALTER TABLE `tournament`
  MODIFY COLUMN `Status`
    ENUM('created','registration_open','registration_closed','team_announced','ongoing','completed','cancelled')
    NOT NULL DEFAULT 'created';
```

> Existing rows with `Status = 'upcoming'` must be migrated: `UPDATE tournament SET Status = 'created' WHERE Status = 'upcoming';` — run before the MODIFY.

---

### 5.2 `tournamentplayer` — Add Selection Status Column

Currently the table tracks who is in the team but not whether their selection is a draft or confirmed. Adding a status column separates the head coach's working draft from the final published squad.

```sql
ALTER TABLE `tournamentplayer`
  ADD COLUMN `SelectionStatus` ENUM('draft','confirmed') NOT NULL DEFAULT 'draft'
    COMMENT 'draft = working selection; confirmed = published in announced team'
    AFTER `SelectedBy`,

  ADD COLUMN `SelectedAt` DATETIME DEFAULT CURRENT_TIMESTAMP
    COMMENT 'When the head coach added this player to the squad'
    AFTER `SelectionStatus`;
```

---

## 6. New Tables to Create

### 6.1 `coach_tournament_recommendations` — Coach Recommendations

> **This SQL is already in `add_coach_tournament_recommendations.sql` and must be run first.**

```sql
CREATE TABLE IF NOT EXISTS `coach_tournament_recommendations` (
  `RecommendationID` INT(11)       NOT NULL AUTO_INCREMENT,
  `CoachID`          INT(11)       NOT NULL,  -- FK → coachprofile
  `TournamentID`     INT(11)       NOT NULL,  -- FK → tournament
  `PlayerID`         INT(11)       NOT NULL,  -- FK → user (Role = Player)
  `RecommendedRole`  VARCHAR(100)  DEFAULT NULL,
  `Reason`           TEXT          DEFAULT NULL,
  `Comments`         TEXT          DEFAULT NULL,
  `Status`           ENUM('pending','reviewed','confirmed','rejected') NOT NULL DEFAULT 'pending',
  `AdminFeedback`    TEXT          DEFAULT NULL,
  `ReviewedBy`       INT(11)       DEFAULT NULL,  -- FK → user (Head Coach or Admin)
  `DateRecommended`  DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `DateReviewed`     DATETIME      DEFAULT NULL,

  PRIMARY KEY (`RecommendationID`),
  UNIQUE KEY `uq_coach_rec_tournament_player` (`TournamentID`, `PlayerID`, `CoachID`),
  -- One recommendation per coach per player per tournament

  KEY `idx_ctr_coach`      (`CoachID`),
  KEY `idx_ctr_tournament` (`TournamentID`),
  KEY `idx_ctr_player`     (`PlayerID`),
  KEY `idx_ctr_status`     (`Status`),

  CONSTRAINT `fk_ctr_coach`      FOREIGN KEY (`CoachID`)      REFERENCES `coachprofile`(`CoachID`),
  CONSTRAINT `fk_ctr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament`(`TournamentID`),
  CONSTRAINT `fk_ctr_player`     FOREIGN KEY (`PlayerID`)     REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Coach recommendations for player selection in a tournament.';
```

---

### 6.2 `trainer_tournament_recommendations` — Trainer Recommendations

Structurally identical to the coach table but references `trainerprofile` instead of `coachprofile`.

```sql
CREATE TABLE IF NOT EXISTS `trainer_tournament_recommendations` (
  `RecommendationID` INT(11)       NOT NULL AUTO_INCREMENT,
  `TrainerID`        INT(11)       NOT NULL,  -- FK → trainerprofile
  `TournamentID`     INT(11)       NOT NULL,  -- FK → tournament
  `PlayerID`         INT(11)       NOT NULL,  -- FK → user (Role = Player)
  `RecommendedRole`  VARCHAR(100)  DEFAULT NULL,
  `Reason`           TEXT          DEFAULT NULL,
  `Comments`         TEXT          DEFAULT NULL,
  `Status`           ENUM('pending','reviewed','confirmed','rejected') NOT NULL DEFAULT 'pending',
  `ReviewedBy`       INT(11)       DEFAULT NULL,  -- FK → user (Head Coach or Admin)
  `DateRecommended`  DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `DateReviewed`     DATETIME      DEFAULT NULL,

  PRIMARY KEY (`RecommendationID`),
  UNIQUE KEY `uq_trainer_rec_tournament_player` (`TournamentID`, `PlayerID`, `TrainerID`),

  KEY `idx_ttr_trainer`    (`TrainerID`),
  KEY `idx_ttr_tournament` (`TournamentID`),
  KEY `idx_ttr_player`     (`PlayerID`),
  KEY `idx_ttr_status`     (`Status`),

  CONSTRAINT `fk_ttr_trainer`    FOREIGN KEY (`TrainerID`)    REFERENCES `trainerprofile`(`TrainerID`),
  CONSTRAINT `fk_ttr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament`(`TournamentID`),
  CONSTRAINT `fk_ttr_player`     FOREIGN KEY (`PlayerID`)     REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Trainer recommendations for player selection in a tournament.';
```

---

### 6.3 `tournament_join_request` — Player Interest

```sql
CREATE TABLE IF NOT EXISTS `tournament_join_request` (
  `RequestID`    INT(11)   NOT NULL AUTO_INCREMENT,
  `TournamentID` INT(11)   NOT NULL,  -- FK → tournament
  `PlayerID`     INT(11)   NOT NULL,  -- FK → user (Role = Player)
  `Message`      TEXT      DEFAULT NULL,
  `Status`       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ReviewedBy`   INT(11)   DEFAULT NULL,  -- FK → user (Admin or Head Coach)
  `ReviewNotes`  TEXT      DEFAULT NULL,
  `RequestedAt`  DATETIME  DEFAULT CURRENT_TIMESTAMP,
  `ReviewedAt`   DATETIME  DEFAULT NULL,

  PRIMARY KEY (`RequestID`),
  UNIQUE KEY `uq_join_req_tournament_player` (`TournamentID`, `PlayerID`),
  -- One request per player per tournament

  KEY `idx_tjr_tournament` (`TournamentID`),
  KEY `idx_tjr_player`     (`PlayerID`),
  KEY `idx_tjr_status`     (`Status`),

  CONSTRAINT `fk_tjr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament`(`TournamentID`),
  CONSTRAINT `fk_tjr_player`     FOREIGN KEY (`PlayerID`)     REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Player self-nominations to be considered for tournament squad selection.';
```

**Join request business rules:**
- Only allowed when `tournament.Status = 'registration_open'`
- One row per `(TournamentID, PlayerID)` — enforced by UNIQUE KEY
- Rejection does not prevent coach or trainer from still recommending the player
- Approval does not guarantee squad selection — it merely advances the player to the head coach's review pool
- When tournament is cancelled, a batch UPDATE sets all pending requests to `rejected`

---

### 6.4 `tournament_result` — Tournament-Level Outcome

```sql
CREATE TABLE IF NOT EXISTS `tournament_result` (
  `ResultID`          INT(11)       NOT NULL AUTO_INCREMENT,
  `TournamentID`      INT(11)       NOT NULL UNIQUE,  -- one result row per tournament
  `Position`          ENUM('1st','2nd','3rd','Finalist','Group Stage','DNS')
                      NOT NULL DEFAULT 'DNS',
  `OpponentInFinal`   VARCHAR(255)  DEFAULT NULL,
  `MatchFormat`       ENUM('T20','ODI','Test','Other') DEFAULT NULL,
  `WonBy`             VARCHAR(100)  DEFAULT NULL,
  -- E.g. "8 wickets", "52 runs", "DLS method", etc. Free text.

  `ManOfTournament`   INT(11)       DEFAULT NULL,  -- FK → user (PlayerID)
  `SummaryNotes`      TEXT          DEFAULT NULL,
  `AnnouncedAt`       DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `EnteredBy`         INT(11)       NOT NULL,       -- FK → user (Head Coach or Admin)
  `UpdatedAt`         DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`ResultID`),
  KEY `idx_tr_tournament` (`TournamentID`),
  CONSTRAINT `fk_tr_tournament`  FOREIGN KEY (`TournamentID`) REFERENCES `tournament`(`TournamentID`),
  CONSTRAINT `fk_tr_man_of_tour` FOREIGN KEY (`ManOfTournament`) REFERENCES `user`(`UserID`),
  CONSTRAINT `fk_tr_enteredby`   FOREIGN KEY (`EnteredBy`)    REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Academy team outcome for a completed tournament.';
```

---

## 7. Migration Sequence

Run these DDL statements **in order**. Each step depends on the previous.

```
Step 1: UPDATE tournament SET Status='created' WHERE Status='upcoming';
         (migrate old status value before the MODIFY)

Step 2: ALTER TABLE tournament MODIFY COLUMN Status ENUM(...);
         (expand the status enum)

Step 3: ALTER TABLE tournament ADD COLUMN AgeGroup ... (the 7-column ALTER from §5.1)

Step 4: ALTER TABLE tournamentplayer ADD COLUMN SelectionStatus ... (from §5.2)

Step 5: Run add_coach_tournament_recommendations.sql
         (creates coach_tournament_recommendations; file already exists at project root)

Step 6: CREATE TABLE trainer_tournament_recommendations (from §6.2)

Step 7: CREATE TABLE tournament_join_request (from §6.3)

Step 8: CREATE TABLE tournament_result (from §6.4)
```

> **Verify trainerprofile FK:** Step 6 adds `FOREIGN KEY (TrainerID) REFERENCES trainerprofile(TrainerID)`. Confirm `trainerprofile` exists and has the `TrainerID` PK before running Step 6.

---

## 8. Controllers Needed

The module spans four existing controllers. No new controller class is needed. All new methods are added to the appropriate existing controller.

### 8.1 Admin.php — Tournament Management (new methods)

| Method | HTTP | Purpose |
|---|---|---|
| `tournaments()` | GET | List all tournaments with status badges |
| `create_tournament()` | GET/POST | Show form / save new tournament |
| `edit_tournament($id)` | GET/POST | Edit tournament details (pre-ongoing only) |
| `cancel_tournament($id)` | POST | Set status=cancelled, require reason, batch-reject join requests |
| `tournament_detail($id)` | GET | View all join requests + all recommendations for a tournament |
| `update_tournament_status($id)` | POST | Advance status (created→registration_open→registration_closed→team_announced→ongoing→completed) |
| `approve_join_request($id)` | POST | Set tournament_join_request.Status = approved |
| `reject_join_request($id)` | POST | Set tournament_join_request.Status = rejected |
| `publish_team($tournamentId)` | POST | Set IsTeamAnnounced=1, Status=team_announced |
| `enter_results($tournamentId)` | GET/POST | Show / save tournament_result + playertournamentstats |

### 8.2 Coach.php — Enhancements (new/updated methods)

| Method | HTTP | Purpose |
|---|---|---|
| `tournaments()` | GET | **UPDATE** — load from `tournament` table (not Event model); show join request status if head coach |
| `tournament_detail($id)` | GET | Tournament info + own recommendation for this tournament |
| `recommend_players($id)` | GET | Form to select player + fill recommendation (existing, connect to new table) |
| `save_recommendation()` | POST | Save to `coach_tournament_recommendations` (existing — verify table exists first) |
| `finalize_team($id)` | GET | **HEAD COACH ONLY** — full review: join requests + all recommendations |
| `save_team_selection($id)` | POST | **HEAD COACH ONLY** — write to `tournamentplayer`, set SelectionStatus=confirmed |
| `enter_results($id)` | GET/POST | **HEAD COACH ONLY** — enter `tournament_result` + `playertournamentstats` |

### 8.3 Trainer.php — Recommendation Flow (new methods)

| Method | HTTP | Purpose |
|---|---|---|
| `tournaments()` | GET | **UPDATE** — load from `tournament` table; show own recommendation status per tournament |
| `recommend_players($id)` | GET | Form to select player + fill recommendation |
| `save_recommendation()` | POST | Save to `trainer_tournament_recommendations` |
| `my_recommendations()` | GET | List all trainer's past recommendations with status |

### 8.4 Player.php — Join Request Flow (new/updated methods)

| Method | HTTP | Purpose |
|---|---|---|
| `tournaments()` | GET | **UPDATE** — list from `tournament` table; show join request status and team if announced |
| `tournament_detail($id)` | GET | Full tournament page: info, team (if announced), own stats (if completed) |
| `join_tournament($id)` | GET/POST | Show form / submit join request to `tournament_join_request` |
| `cancel_join_request($id)` | POST | Withdraw pending join request (only while status=registration_open) |

---

## 9. Views Needed

### 9.1 Admin Views

```
app/views/admin/tournaments/
├── index.php          — Tournament list table with status badges and action buttons
├── create.php         — Create tournament form
├── edit.php           — Edit tournament form (same fields, pre-populated)
├── detail.php         — Single tournament: join requests panel + recommendations panel + team panel
└── results.php        — Enter / edit tournament_result form + per-player stats entry
```

**`index.php` columns:** Name, AgeGroup, Format, Date, Status, Registrations count, Team Announced (Y/N), Actions (Edit / View / Cancel / Status Change)

**`detail.php` panels:**
1. Tournament summary (header)
2. Join Requests tab: table of all players who requested, their message, status (approve/reject buttons)
3. Coach Recommendations tab: all coaches' recommendations with role, reason, status
4. Trainer Recommendations tab: all trainers' recommendations with role, reason, status
5. Team tab: current `tournamentplayer` rows; "Publish Team" button (when head coach has confirmed selections)

### 9.2 Coach Views

```
app/views/coach/tournaments/
├── index.php          — Tournament list + own recommendation status + head-coach-only link
├── detail.php         — Tournament info + own submitted recommendation
├── recommend.php      — Player recommendation form (player dropdown + role + reason + comments)
└── finalize.php       — HEAD COACH ONLY: 3-panel review page (join requests, coach recs, trainer recs) + squad builder
```

**`finalize.php`** is the most complex view. It contains:
- Left panel: filtering / search across all inputs (join requests + recommendations + unrequested players)
- Right panel: current draft squad (drag-and-drop or checkbox-select up to MaxPlayers)
- Role assignment: dropdown per player (Captain / Vice-Captain / WK / Batsman / Bowler / All-Rounder)
- Save Draft button (sets SelectionStatus=draft)
- Confirm Squad button (sets SelectionStatus=confirmed; signals to admin team is ready to publish)

### 9.3 Trainer Views

```
app/views/trainer/tournaments/
├── index.php          — Tournament list + own recommendation status per tournament
├── recommend.php      — Player recommendation form (full player list, not restricted to assignments)
└── my_recommendations.php — History of trainer's submitted recommendations
```

### 9.4 Player Views

```
app/views/player/tournaments/
├── index.php          — Tournament list with status, deadline, join request status badge
├── detail.php         — Tournament info, team (if IsTeamAnnounced=1), own stats (if completed)
└── join.php           — Join request form: message text area + submit button
```

**`index.php` for players:** Each tournament card shows:
- Name, Age Group, Format, Date, Location
- Status badge (Registration Open / Closed / Team Announced / Ongoing / Completed)
- "Apply to Join" button only when Status=registration_open and player has no existing request
- Join request status badge (Pending / Approved / Not Selected) if they applied
- "View Team" button when IsTeamAnnounced=1

---

## 10. Models Needed

### 10.1 M_Tournament — Core Tournament Model

```
app/models/M_Tournament.php
```

| Method | Returns | Description |
|---|---|---|
| `getTournaments($status = null)` | array | All tournaments, optionally filtered by status |
| `getTournamentById($id)` | object | Single tournament with all columns |
| `createTournament($data)` | int | INSERT; returns new TournamentID |
| `updateTournament($id, $data)` | bool | UPDATE tournament row |
| `updateStatus($id, $status, $reason = null)` | bool | Change lifecycle status |
| `announceTeam($id)` | bool | Set IsTeamAnnounced=1 + Status=team_announced |
| `getTeam($tournamentId)` | array | All `tournamentplayer` rows with player name and role |
| `addPlayerToTeam($data)` | bool | INSERT into tournamentplayer |
| `removePlayerFromTeam($tournamentId, $playerId)` | bool | DELETE from tournamentplayer |
| `confirmTeam($tournamentId, $headCoachId)` | bool | Set all draft rows to confirmed |
| `isInTeam($tournamentId, $playerId)` | bool | Check if player is already selected |

### 10.2 M_TournamentJoinRequest — Player Request Model

```
app/models/M_TournamentJoinRequest.php
```

| Method | Returns | Description |
|---|---|---|
| `createRequest($tournamentId, $playerId, $message)` | int | INSERT join request |
| `getRequestsByTournament($tournamentId)` | array | All requests for one tournament |
| `getRequestByPlayer($tournamentId, $playerId)` | object\|null | Single player's request for one tournament |
| `updateStatus($requestId, $status, $reviewedBy, $notes)` | bool | Approve or reject |
| `cancelByPlayer($requestId, $playerId)` | bool | Player withdraws (own request only) |
| `rejectAllPending($tournamentId)` | bool | Batch reject on tournament cancel |
| `hasExistingRequest($tournamentId, $playerId)` | bool | Duplicate guard |

### 10.3 M_TrainerTournamentRecommendation — Trainer Recommendation Model

```
app/models/M_TrainerTournamentRecommendation.php
```

| Method | Returns | Description |
|---|---|---|
| `addRecommendation($data)` | int | INSERT; checks duplicate via UNIQUE KEY |
| `getRecommendationsByTrainer($trainerId)` | array | All trainer's recommendations |
| `getRecommendationsByTournament($tournamentId)` | array | All recommendations for a tournament (head coach view) |
| `updateStatus($recId, $status, $reviewedBy)` | bool | Head coach marks reviewed/confirmed/rejected |
| `checkDuplicate($tournamentId, $playerId, $trainerId)` | bool | Guard before INSERT |

### 10.4 M_TournamentResult — Results Model

```
app/models/M_TournamentResult.php
```

| Method | Returns | Description |
|---|---|---|
| `getResult($tournamentId)` | object\|null | Single result row |
| `saveResult($data)` | bool | INSERT or UPDATE (upsert) |
| `getPlayerStats($tournamentId, $playerId)` | object\|null | From playertournamentstats |
| `savePlayerStats($data)` | bool | INSERT or UPDATE per-player stats |
| `getAllStatsForTournament($tournamentId)` | array | All player stats rows |

> **Existing model to update:** `M_CoachTournamentRecommendation` needs one new method: `getRecommendationsByTournament($tournamentId)` — returns all coach recommendations for a tournament (for the head coach finalization view). The existing method `getRecommendationsByCoach()` only returns one coach's own records.

---

## 11. Head Coach Workflow

The head coach workflow is the most sensitive part of the system. It requires specific access control and a specific data view. This section documents the flow in full.

### Access Control Gate

In `Coach.php`, any method restricted to the head coach must begin with this check:

```php
// Head coach gate
$coachProfile = $this->model->getCoachProfile($_SESSION['user_id']);
if (!$coachProfile || !$coachProfile->IsHeadCoach) {
    $_SESSION['error'] = 'Only the Head Coach can access this section.';
    redirect('coach/tournaments');
}
```

This check runs **in addition to** the normal `requireAuth(['Coach'])` at the controller level. It is not enough to be a coach — the coach must have `IsHeadCoach = 1`.

### Setting the Head Coach Flag

Currently all coaches have `IsHeadCoach = 0`. The admin must be able to designate a head coach:

**In `Admin.php`** (method `coaches()` or `edit_coach()`): Add a toggle to set/unset `coachprofile.IsHeadCoach`. Only one coach should be head coach at a time. The recommendation is to use a business rule (not a DB constraint) that shows a warning if `UPDATE coachprofile SET IsHeadCoach=0 WHERE IsHeadCoach=1` would affect more than one row, then sets the new one.

### Head Coach Finalization View (`finalize.php`)

The `finalize_team($tournamentId)` method in `Coach.php` assembles:

```php
$data['tournament']       = $this->M_Tournament->getTournamentById($tournamentId);
$data['joinRequests']     = $this->M_TournamentJoinRequest->getRequestsByTournament($tournamentId);
$data['coachRecs']        = $this->M_CoachTournamentRecommendation->getRecommendationsByTournament($tournamentId);
$data['trainerRecs']      = $this->M_TrainerTournamentRecommendation->getRecommendationsByTournament($tournamentId);
$data['currentSquad']     = $this->M_Tournament->getTeam($tournamentId);
$data['maxPlayers']       = $data['tournament']->MaxPlayers;
```

The view shows three input panels side by side (or as tabs on mobile). The head coach selects players for the squad using checkboxes. The view prevents selecting more than `MaxPlayers` players using JavaScript and also enforces this in the server-side `save_team_selection()` method.

### Summary View for All Rankings

The finalize view should include a "recommendation count" column for each player so the head coach can see at a glance which players were endorsed by multiple coaches and trainers:

```sql
SELECT
  u.UserID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  COUNT(DISTINCT ctr.RecommendationID) AS CoachRecs,
  COUNT(DISTINCT ttr.RecommendationID) AS TrainerRecs,
  (SELECT COUNT(*) FROM tournament_join_request tjr
   WHERE tjr.TournamentID = :tid AND tjr.PlayerID = u.UserID) AS SelfNominated
FROM user u
LEFT JOIN coach_tournament_recommendations ctr
  ON ctr.PlayerID = u.UserID AND ctr.TournamentID = :tid
LEFT JOIN trainer_tournament_recommendations ttr
  ON ttr.PlayerID = u.UserID AND ttr.TournamentID = :tid
WHERE u.Role = 'Player'
  AND (ctr.RecommendationID IS NOT NULL OR ttr.RecommendationID IS NOT NULL
       OR EXISTS (SELECT 1 FROM tournament_join_request tjr2
                  WHERE tjr2.TournamentID = :tid AND tjr2.PlayerID = u.UserID))
GROUP BY u.UserID
ORDER BY (CoachRecs + TrainerRecs) DESC, SelfNominated DESC;
```

This query surfaces the players who attracted the most combined endorsement.

---

## 12. URL Reference

### Admin Routes

| URL | Method | Action |
|---|---|---|
| `/admin/tournaments` | GET | Tournament list |
| `/admin/create_tournament` | GET | Create form |
| `/admin/create_tournament` | POST | Save new tournament |
| `/admin/edit_tournament/{id}` | GET | Edit form |
| `/admin/edit_tournament/{id}` | POST | Save edits |
| `/admin/cancel_tournament/{id}` | POST | Cancel + batch reject join requests |
| `/admin/update_tournament_status/{id}` | POST | Advance status |
| `/admin/tournament_detail/{id}` | GET | Full detail with all panels |
| `/admin/approve_join_request/{id}` | POST | Approve one join request |
| `/admin/reject_join_request/{id}` | POST | Reject one join request |
| `/admin/publish_team/{tournamentId}` | POST | Set IsTeamAnnounced=1 |
| `/admin/enter_results/{tournamentId}` | GET | Results form |
| `/admin/enter_results/{tournamentId}` | POST | Save results |

### Coach Routes

| URL | Method | Action |
|---|---|---|
| `/coach/tournaments` | GET | Tournament list |
| `/coach/tournament_detail/{id}` | GET | Tournament info + own recommendation |
| `/coach/recommend_players/{id}` | GET | Recommendation form |
| `/coach/save_recommendation` | POST | Submit recommendation |
| `/coach/finalize_team/{id}` | GET | Head coach finalization view |
| `/coach/save_team_selection/{id}` | POST | Head coach saves squad |
| `/coach/enter_results/{id}` | GET | Head coach results entry form |
| `/coach/enter_results/{id}` | POST | Save results |

### Trainer Routes

| URL | Method | Action |
|---|---|---|
| `/trainer/tournaments` | GET | Tournament list with recommendation status |
| `/trainer/recommend_players/{id}` | GET | Recommendation form |
| `/trainer/save_recommendation` | POST | Submit recommendation |
| `/trainer/my_recommendations` | GET | History of own recommendations |

### Player Routes

| URL | Method | Action |
|---|---|---|
| `/player/tournaments` | GET | Tournament list with join status |
| `/player/tournament_detail/{id}` | GET | Detail page: info, team, own stats |
| `/player/join_tournament/{id}` | GET | Join request form |
| `/player/join_tournament/{id}` | POST | Submit join request |
| `/player/cancel_join_request/{id}` | POST | Withdraw pending request |

---

## 13. Role Permissions Table

| Feature | Admin | Head Coach | Coach | Trainer | Player |
|---|---|---|---|---|---|
| Create tournament | ✅ | — | — | — | — |
| Edit tournament (pre-ongoing) | ✅ | — | — | — | — |
| Cancel tournament | ✅ | — | — | — | — |
| Advance tournament status | ✅ | — | — | — | — |
| View tournament list | ✅ | ✅ | ✅ | ✅ | ✅ |
| View tournament detail | ✅ | ✅ | ✅ | ✅ | ✅ |
| Submit join request | — | — | — | — | ✅ (when open) |
| Cancel own join request | — | — | — | — | ✅ (pending only) |
| Approve / reject join request | ✅ | ✅ | — | — | — |
| Submit coach recommendation | — | ✅ | ✅ | — | — |
| Submit trainer recommendation | — | — | — | ✅ | — |
| View all recommendations (full) | ✅ | ✅ | — | — | — |
| View own recommendations only | — | — | ✅ | ✅ | — |
| Finalize squad (tournamentplayer) | — | ✅ | — | — | — |
| Publish team (IsTeamAnnounced=1) | ✅ | — | — | — | — |
| View team after announcement | ✅ | ✅ | ✅ | ✅ | ✅ |
| Enter tournament results | ✅ | ✅ | — | — | — |
| View own performance stats | — | — | — | — | ✅ |
| View all player stats | ✅ | ✅ | ✅ | ✅ | — |
| Set IsHeadCoach flag | ✅ | — | — | — | — |

---

## 14. Key Business Rules

**Rule 1: Join requests only while registration is open.**
`tournament_join_request` INSERT is only allowed when `tournament.Status = 'registration_open'`. The controller must validate this before any INSERT. The DB does not enforce this.

**Rule 2: One join request per player per tournament.**
Enforced by UNIQUE KEY `(TournamentID, PlayerID)` on `tournament_join_request`. The model's `hasExistingRequest()` method provides a user-friendly check before the INSERT so the controller can show "You have already applied" instead of a DB error.

**Rule 3: One recommendation per coach per player per tournament.**
Enforced by UNIQUE KEY `(TournamentID, PlayerID, CoachID)` on `coach_tournament_recommendations` and `(TournamentID, PlayerID, TrainerID)` on `trainer_tournament_recommendations`. Models must call `checkDuplicate()` before `addRecommendation()`.

**Rule 4: Coach recommendations are restricted to assigned players.**
`M_CoachTournamentRecommendation::addRecommendation()` validates that a `playercoachassignment` row exists for `(CoachID, PlayerID)`. A coach cannot recommend a player assigned to a different coach. This validation is already in the existing model — do not remove it.

**Rule 5: Trainer recommendations are NOT restricted to assigned players.**
Trainers work with all players in fitness sessions. `trainer_tournament_recommendations` has no assignment gate. Any active player can be recommended by any trainer.

**Rule 6: Only the head coach can finalize the squad.**
`Coach.php::finalize_team()` and `save_team_selection()` must check `coachprofile.IsHeadCoach = 1` before executing. If the check fails, redirect with an error message.

**Rule 7: Squad size cannot exceed MaxPlayers.**
`save_team_selection()` must count the selected players and reject the submission if `count($selectedPlayers) > $tournament->MaxPlayers`. Both server-side and client-side (JavaScript) enforcement is required. Server-side is mandatory; client-side is a UX improvement only.

**Rule 8: Team is locked when ongoing.**
Once `tournament.Status = 'ongoing'`, no INSERT, UPDATE, or DELETE on `tournamentplayer` is allowed. The finalization UI becomes read-only. Any attempt to modify the squad via direct POST must be rejected by the controller.

**Rule 9: Results can only be entered for ongoing or completed tournaments.**
`enter_results()` must check that `tournament.Status IN ('ongoing', 'completed')` before rendering the form. This prevents the head coach from entering results for a tournament that has not started.

**Rule 10: Cancellation cascades to join requests.**
When admin cancels a tournament, `tournament_join_request` rows with `Status = 'pending'` must be updated to `'rejected'` in the same transaction. Players who had pending requests should not see an indefinitely-pending status for a cancelled tournament.

---

## 15. Implementation Checklist

This checklist is intended for use during the implementation phase. Items are ordered by dependency.

### Phase 1 — Database (no PHP yet)
- [ ] Run Step 1: `UPDATE tournament SET Status='created' WHERE Status='upcoming'`
- [ ] Run Step 2: `ALTER TABLE tournament MODIFY COLUMN Status ENUM(...)` (expanded)
- [ ] Run Step 3: `ALTER TABLE tournament ADD COLUMN ...` (7 columns from §5.1)
- [ ] Run Step 4: `ALTER TABLE tournamentplayer ADD COLUMN SelectionStatus, SelectedAt`
- [ ] Run Step 5: Execute `add_coach_tournament_recommendations.sql`
- [ ] Run Step 6: Create `trainer_tournament_recommendations`
- [ ] Run Step 7: Create `tournament_join_request`
- [ ] Run Step 8: Create `tournament_result`
- [ ] Verify all foreign keys resolve correctly (especially `trainerprofile.TrainerID`)
- [ ] Set one coach as head coach for testing: `UPDATE coachprofile SET IsHeadCoach=1 WHERE CoachID=X`

### Phase 2 — Models
- [ ] Create `M_Tournament.php` with all 12 methods from §10.1
- [ ] Create `M_TournamentJoinRequest.php` with all 7 methods from §10.2
- [ ] Create `M_TrainerTournamentRecommendation.php` with all 5 methods from §10.3
- [ ] Create `M_TournamentResult.php` with all 4 methods from §10.4
- [ ] Add `getRecommendationsByTournament($tournamentId)` to existing `M_CoachTournamentRecommendation.php`

### Phase 3 — Admin Controller + Views
- [ ] Add 10 new methods to `Admin.php` (§8.1)
- [ ] Create `app/views/admin/tournaments/index.php`
- [ ] Create `app/views/admin/tournaments/create.php`
- [ ] Create `app/views/admin/tournaments/edit.php`
- [ ] Create `app/views/admin/tournaments/detail.php` (3 tabbed panels)
- [ ] Create `app/views/admin/tournaments/results.php`
- [ ] Add "Tournaments" nav link to admin sidebar

### Phase 4 — Coach Controller + Views
- [ ] Update `Coach.php::tournaments()` to load from `tournament` table
- [ ] Add `finalize_team()`, `save_team_selection()`, `enter_results()` with head coach gate
- [ ] Create `app/views/coach/tournaments/index.php`
- [ ] Create `app/views/coach/tournaments/detail.php`
- [ ] Create `app/views/coach/tournaments/recommend.php`
- [ ] Create `app/views/coach/tournaments/finalize.php` (head coach only)
- [ ] Add head coach nav badge to coach sidebar (only visible when IsHeadCoach=1)

### Phase 5 — Trainer Controller + Views
- [ ] Update `Trainer.php::tournaments()` to load from `tournament` table
- [ ] Add `recommend_players()`, `save_recommendation()`, `my_recommendations()` to `Trainer.php`
- [ ] Create `app/views/trainer/tournaments/index.php`
- [ ] Create `app/views/trainer/tournaments/recommend.php`
- [ ] Create `app/views/trainer/tournaments/my_recommendations.php`

### Phase 6 — Player Controller + Views
- [ ] Update `Player.php::tournaments()` to load from `tournament` table
- [ ] Add `tournament_detail()`, `join_tournament()`, `cancel_join_request()` to `Player.php`
- [ ] Create `app/views/player/tournaments/index.php`
- [ ] Create `app/views/player/tournaments/detail.php`
- [ ] Create `app/views/player/tournaments/join.php`

### Phase 7 — Integration and Business Rules
- [ ] Verify admin head coach toggle (set/unset IsHeadCoach from admin coach management)
- [ ] Test full lifecycle: create → open → player joins → coach recommends → trainer recommends → close → head coach finalizes → admin publishes → results entered → completed
- [ ] Test cancellation cascade (pending join requests auto-rejected)
- [ ] Test MaxPlayers enforcement (client + server)
- [ ] Test team lock when status = ongoing
- [ ] Test head coach gate: confirm non-head-coach cannot access finalize routes
- [ ] Test coach assignment gate: coach cannot recommend player not in their assigned list
- [ ] Test trainer open access: trainer can recommend any active player

---

*End of TOURNAMENT_MODULE_DESIGN.md*
