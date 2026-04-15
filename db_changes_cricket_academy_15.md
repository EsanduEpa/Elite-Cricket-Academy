# Database Changes — cricket_academy (15).sql

**Date:** 2026-04-15  
**Applied by:** Claude Code  
**File modified:** `cricket_academy (15).sql`  
**Database:** `cricket_academy`

---

## Summary

Three categories of fixes were made to `cricket_academy (15).sql` before importing, followed by cleanup of unused tables in the live database.

---

## Fix 1 — Empty Stand-in CREATE TABLE Statements

### Problem
phpMyAdmin exports views using a temporary "stand-in" `CREATE TABLE` as a placeholder. Four of these were exported with empty column lists `()`, which is invalid SQL and causes:

```
#1064 - You have an error in your SQL syntax ... near ')' at line 8
A symbol name was expected! (near ")" at position 202)
```

### Affected Views
| View | Fix |
|---|---|
| `performanceupdatesummary` | Added 9 columns |
| `playerdevelopmenttracking` | Added 14 columns |
| `v_active_players` | Added 12 columns |
| `v_upcoming_sessions` | Added 10 columns |

### What Was Done
Each empty `CREATE TABLE \`viewname\` ();` was replaced with the correct column definitions derived from the actual `SELECT` columns in the view definition further down in the file.

**Example fix:**
```sql
-- BEFORE (broken)
CREATE TABLE `performanceupdatesummary` (
);

-- AFTER (fixed)
CREATE TABLE `performanceupdatesummary` (
`PlayerID` int(11)
,`PlayerName` varchar(255)
,`TotalUpdates` bigint(21)
,`ApprovedUpdates` bigint(21)
,`PendingUpdates` bigint(21)
,`LastUpdateDate` datetime
,`AvgTechnicalRating` double
,`AvgFitnessRating` double
,`AvgAttitudeRating` double
);
```

---

## Fix 2 — Unknown Column `Name` in User Table

### Problem
All view definitions referenced a `Name` column on the `user` table (as `u.Name`, `u_coach.Name`, `u_player.Name`). The `user` table has no `Name` column — it has `FirstName` and `LastName`. This caused:

```
#1054 - Unknown column 'u_coach.Name' in 'field list'
```

### Affected Views
| View | Aliases Fixed |
|---|---|
| `coachingeffectiveness` | `u_coach.Name` in SELECT and GROUP BY |
| `coachplayerpermissions` | `u_coach.Name`, `u_player.Name` in SELECT |
| `performanceupdatesummary` | `u.Name` in SELECT and GROUP BY |
| `playerdevelopmenttracking` | `u_player.Name`, `u_coach.Name` in SELECT and GROUP BY |
| `v_active_players` | `u.Name` in SELECT |
| `v_upcoming_sessions` | `u.Name` in SELECT and GROUP BY |

### What Was Done
Every `alias`.`Name` reference to the `user` table was replaced with:
```sql
CONCAT(`alias`.`FirstName`, ' ', `alias`.`LastName`)
```

**Example fix:**
```sql
-- BEFORE (broken)
`u_coach`.`Name` AS `CoachName`
...
GROUP BY `cp`.`CoachID`, `u_coach`.`Name`, `cp`.`Specialization`

-- AFTER (fixed)
CONCAT(`u_coach`.`FirstName`, ' ', `u_coach`.`LastName`) AS `CoachName`
...
GROUP BY `cp`.`CoachID`, CONCAT(`u_coach`.`FirstName`, ' ', `u_coach`.`LastName`), `cp`.`Specialization`
```

> **Note:** `f.Name` in `v_facility_availability` was left untouched — it references the `facility` table which does have a `Name` column.

---

## Fix 3 — Views Referencing Missing Table `sessionenrollment`

### Problem
The table `sessionenrollment` was accidentally omitted from the dump entirely. Two views that join against it caused the import to fail:

```
ERROR 1146 (42S02) at line 5693: Table 'cricket_academy.sessionenrollment' doesn't exist
```

### Affected Views
- `playerdevelopmenttracking`
- `v_upcoming_sessions`

### What Was Done
Both views were **removed** from the SQL file (neither is referenced anywhere in the PHP codebase). The `DROP TABLE IF EXISTS` statements were kept so the stand-in tables are properly cleaned up on import.

---

## Database Import

After the above fixes, the old database was dropped and the corrected file was imported:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e \
  "DROP DATABASE IF EXISTS cricket_academy; \
   CREATE DATABASE cricket_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

/Applications/XAMPP/xamppfiles/bin/mysql -u root cricket_academy \
  < "cricket_academy (15).sql"
```

---

## Unused Table Cleanup

After import, 19 tables were identified as unreferenced in any PHP file and dropped from the live database.

### Dropped Tables
| Table | Reason |
|---|---|
| `adminprofile` | Not referenced in any PHP file |
| `coachappointment` | Not referenced in any PHP file |
| `coachingsession` | Not referenced in any PHP file |
| `coachreview` | Not referenced in any PHP file |
| `contactmessage` | Not referenced in any PHP file |
| `contactus` | Not referenced in any PHP file |
| `emaillog` | Not referenced in any PHP file |
| `equipmentcart` | Not referenced in any PHP file |
| `equipmentreview` | Not referenced in any PHP file |
| `eventenrollment` | Not referenced in any PHP file |
| `livenotification` | Not referenced in any PHP file |
| `playercoach` | Not referenced in any PHP file (superseded by `playercoachassignment`) |
| `playertrainer` | Not referenced in any PHP file |
| `playertrainerassignment` | Not referenced in any PHP file |
| `productcart` | Not referenced in any PHP file |
| `slot_audit_log` | Not referenced in any PHP file |
| `slot_private_session_request` | Not referenced in any PHP file |
| `trainerreview` | Not referenced in any PHP file |
| `userpermissions` | Not referenced in any PHP file |

### Drop Command Used
```sql
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS adminprofile;
DROP TABLE IF EXISTS coachappointment;
DROP TABLE IF EXISTS coachingsession;
DROP TABLE IF EXISTS coachreview;
DROP TABLE IF EXISTS contactmessage;
DROP TABLE IF EXISTS contactus;
DROP TABLE IF EXISTS emaillog;
DROP TABLE IF EXISTS equipmentcart;
DROP TABLE IF EXISTS equipmentreview;
DROP TABLE IF EXISTS eventenrollment;
DROP TABLE IF EXISTS livenotification;
DROP TABLE IF EXISTS playercoach;
DROP TABLE IF EXISTS playertrainer;
DROP TABLE IF EXISTS playertrainerassignment;
DROP TABLE IF EXISTS productcart;
DROP TABLE IF EXISTS slot_audit_log;
DROP TABLE IF EXISTS slot_private_session_request;
DROP TABLE IF EXISTS trainerreview;
DROP TABLE IF EXISTS userpermissions;
SET FOREIGN_KEY_CHECKS = 1;
```

---

## Final Database State

**50 active tables + 4 views** remain after cleanup.

### Active Views
| View | Status |
|---|---|
| `coachingeffectiveness` | Kept |
| `coachplayerpermissions` | Kept |
| `performanceupdatesummary` | Kept |
| `v_active_players` | Kept |
| `v_facility_availability` | Kept (unchanged — no Name column issue) |

### Tables Retained (50)
All tables confirmed referenced in the PHP codebase:
`achievements`, `activitylog`, `coach_skill_age_group_assignment`, `coach_tournament_recommendations`, `coachprofile`, `crimatch`, `equipment`, `equipmentrental`, `event`, `facility`, `facilitybooking`, `feedback`, `membershipplan`, `notification`, `nutritionplan`, `nutritionplan_player`, `performanceupdate`, `player_skill_coach_assignment`, `playercoachassignment`, `playermatchperformance`, `playermedicalrecord`, `playeroverallstats`, `playerprofile`, `playersubscription`, `playertournamentstats`, `product`, `productorder`, `productorderitem`, `productreview`, `session`, `shopemployeeprofile`, `slot_booking`, `slot_occurrence`, `slot_occurrence_staff_override`, `slot_template`, `slot_template_player_assignment`, `slot_template_staff`, `slot_time_band`, `subscriptionpayment`, `supplement_player`, `supplementplan`, `tournament`, `tournament_join_request`, `tournamentplayer`, `trainer_tournament_recommendations`, `trainerappointment`, `trainerprofile`, `user`, `workoutplan`, `workoutplan_player`
