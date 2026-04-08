# Slots Module — Full Implementation Plan (Enhanced)
**Elite Cricket Academy**
**Last updated:** 2026-04-08
**Status file:** Keep this file open. Mark each checkbox as you complete it.

---

## Quick Reference — Phase Status

| Phase | Description | Status |
|-------|-------------|--------|
| 1 | Database — create tables + migrate old sessions | ✅ DONE |
| 2 | Admin — Time Bands + Templates + Staff Assignment | ✅ DONE |
| 3 | Admin — Occurrence Generation + Calendar | ⬜ TODO |
| 4 | Service Layer — Subscription Gate + Medical Flag | ⬜ TODO |
| 5 | Player + ShopEmployee Booking | ⬜ TODO |
| 6 | Coach + Trainer Schedule Views + Attendance | ⬜ TODO |
| 7 | Notifications + Audit Trail | ⬜ TODO |
| 8 | Reporting + Changelog Page | ⬜ TODO |

---

## Enhancements Added vs Original Plan

The following items were in the design document but missing from the original plan.
All are now included in the phases below:

1. **Coach + Trainer schedule views and attendance marking** (new Phase 6)
   — `slot_occurrence → slot_booking → coachingsession → performanceupdate → playeroverallstats` chain depends on this
2. **`activitylog` writes** alongside `slot_audit_log` (added to Phase 7)
3. **`livenotification` inserts** for high-urgency events (added to Phase 7)
4. **Ad-hoc occurrence creation** (TemplateID = NULL) for special camps/makeup sessions (added to Phase 3)
5. **`/adminslots/changelog`** standalone read-only page for `slot_audit_log` (added to Phase 8)
6. **`slot_booking` status flow** — `pending → confirmed` two-step for admin-enrolled players (noted in Phase 5)

---

---

# PHASE 1 — Database ✅ DONE

## What Was Built

- All 7 tables created via `create_slot_tables.sql` (project root)
- 7 time bands seeded into `slot_time_band`
- All 18 existing `Session` rows migrated into `slot_occurrence` with `LegacySessionID` bridge
- FacilityID 6 (Trainer Room) seeded into `facility` table

## Files
```
create_slot_tables.sql    (project root)
```

---

## Phase 1 — Testing Checklist ✅

Open phpMyAdmin or run these in terminal: `mysql -u root -p cricket_academy`

### T1.1 — Tables exist
```sql
SHOW TABLES LIKE 'slot_%';
```
**Expected:** 7 rows — `slot_audit_log`, `slot_booking`, `slot_occurrence`,
`slot_occurrence_staff_override`, `slot_template`, `slot_template_staff`, `slot_time_band`

### T1.2 — 7 time bands seeded
```sql
SELECT SlotID, SlotLabel, StartTime, EndTime, IsActive FROM slot_time_band ORDER BY SlotID;
```
**Expected:** 7 rows, SlotID 1–7, all IsActive = 1

### T1.3 — Old sessions migrated
```sql
SELECT COUNT(*) AS old_sessions FROM session;
SELECT COUNT(*) AS migrated     FROM slot_occurrence WHERE LegacySessionID IS NOT NULL;
```
**Expected:** Both counts are equal

### T1.4 — Double-booking constraint on `slot_occurrence`
```sql
SHOW CREATE TABLE slot_occurrence\G
```
**Expected:** Contains `UNIQUE KEY uq_occ_facility_slot_date (FacilityID, SlotID, OccurrenceDate)`

### T1.5 — No-duplicate-booking constraint on `slot_booking`
```sql
SHOW CREATE TABLE slot_booking\G
```
**Expected:** Contains `UNIQUE KEY uq_booking_player_occurrence (OccurrenceID, PlayerID)`

### T1.6 — Trainer Room seeded
```sql
SELECT FacilityID, Name, Location, Capacity FROM facility WHERE FacilityID = 6;
```
**Expected:** One row — "Trainer Room", Capacity=1

### T1.7 — Audit log is append-only (structural check)
```sql
SHOW CREATE TABLE slot_audit_log\G
```
**Expected:** `LogID AUTO_INCREMENT PRIMARY KEY`, no UPDATE/DELETE triggers — just an INSERT-only structure
(Enforcement is at the application level — no controller or model ever runs UPDATE/DELETE on this table)

---

---

# PHASE 2 — Admin: Time Bands + Templates + Staff Assignment ✅ DONE

## What Was Built

| File | Purpose |
|------|---------|
| `app/controllers/Adminslots.php` | Controller for all admin slot management |
| `app/models/M_SlotAdmin.php` | All DB reads/writes for admin slot operations |
| `app/views/admin/slots/timeslots.php` | Toggle time bands active/inactive |
| `app/views/admin/slots/templates.php` | List all templates |
| `app/views/admin/slots/template_form.php` | Create/edit template form |
| `app/views/admin/slots/staff.php` | Assign coaches or trainers to a template |

### Bug fixed
`public/css/admin/admin-dashboard.css` — `.dashboard-header::before` overlay blocked all clicks.
Fixed: added `position:relative; overflow:hidden` to `.dashboard-header` and `pointer-events:none` to `::before`.

---

## Phase 2 — Testing Checklist ✅

### T2.1 — Time bands page loads
```
Visit: http://localhost/Elite/adminslots/timeslots
```
**Expected:** Table with 7 rows, each showing SlotLabel + Active/Inactive badge + toggle button.
No JavaScript errors in browser console.

### T2.2 — Toggle a time band
Click "Deactivate" on band 7 (09:00 PM – 10:00 PM).
```sql
SELECT SlotID, SlotLabel, IsActive FROM slot_time_band WHERE SlotID = 7;
```
**Expected:** IsActive = 0. Page reloads, badge shows "Inactive", button now says "Activate".
Click again → IsActive flips back to 1.

### T2.3 — No delete button exists
Inspect the timeslots page HTML — there must be **no** delete button or link for any time band row.

### T2.4 — Templates page loads (empty state)
```
Visit: http://localhost/Elite/adminslots/templates
```
**Expected:** "No templates yet" message + "Create Template" button visible.

### T2.5 — Create a template
Fill the form:
- Name: `U15 Batting Practice`
- Slot Type: `Program`
- Staff Type: `Coach`
- Day of Week: `Monday`
- Time Band: `03:00 PM – 05:00 PM`
- Facility: any
- Max Participants: `14`
- Required Plan Feature: `Sessions`
- Recurrence Start: `2026-04-01`

Click Save. **Expected:** Redirect to the staff assignment page for the new template.

```sql
SELECT TemplateID, TemplateName, SlotType, StaffType, DayOfWeek, RequiredPlanFeature
FROM slot_template ORDER BY TemplateID DESC LIMIT 1;
```
**Expected:** Row matches what you entered.

### T2.6 — Staff type locked on staff page
```
Visit: http://localhost/Elite/adminslots/staff/{new_template_id}
```
**Expected:** "Staff Type" field shows "Coach" as read-only. Cannot change it.

### T2.7 — Assign a coach
Select a coach user from the dropdown, role Lead. Click Assign.
```sql
SELECT ts.*, u.Name, u.Role, t.StaffType AS TemplateType
FROM slot_template_staff ts
JOIN user u ON u.UserID = ts.UserID
JOIN slot_template t ON t.TemplateID = ts.TemplateID
WHERE ts.TemplateID = (SELECT MAX(TemplateID) FROM slot_template);
```
**Expected:** One row. `ts.StaffType = 'coach'` and `TemplateType = 'coach'`.

### T2.8 — Blocked: assign a trainer to a coach template
Select a Trainer user from the dropdown, change the hidden type if visible. Submit.
**Expected:** Error message — "Cannot assign: this template requires a Coach."
No row inserted in `slot_template_staff`.

### T2.9 — Remove staff assignment
Click Remove next to the assigned coach.
**Expected:** Row deleted from `slot_template_staff`. Page reloads showing empty staff list.

### T2.10 — Toggle template active/inactive
On the templates list page, toggle the template.
```sql
SELECT TemplateID, TemplateName, IsActive FROM slot_template ORDER BY TemplateID DESC LIMIT 1;
```
**Expected:** IsActive flips. Toggle again → flips back.

### T2.11 — Role-protection: non-admin cannot access
Log out and log in as a Player or Coach.
```
Visit: http://localhost/Elite/adminslots/timeslots
```
**Expected:** Redirect to login or 403 page. Never shows the admin content.

---

---

# PHASE 3 — Admin: Occurrence Generation + Calendar ⬜ TODO

## Files to Create

| File | Purpose |
|------|---------|
| `app/views/admin/slots/generate.php` | Form: pick template + date range → submit |
| `app/views/admin/slots/calendar.php` | JS-rendered weekly/monthly occurrence grid |
| `app/views/admin/slots/occurrence.php` | Detail: cancel, substitute staff |

## Methods to Add to `M_SlotAdmin.php`

### `generateOccurrences(int $templateId, string $from, string $to): array`
```php
// 1. Load template (DayOfWeek, SlotID, FacilityID, MaxParticipants, TemplateID)
// 2. Loop each date between $from and $to inclusive
// 3. If date('N', strtotime($date)) == $template->DayOfWeek → attempt INSERT
//    INSERT INTO slot_occurrence
//      (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy)
//      VALUES (:tid, :slotid, :date, :fid, 'scheduled', :adminId)
//    Catch PDOException SQLSTATE '23000' → add to $skipped list, continue loop
// 4. Return ['inserted' => $n, 'skipped' => $n, 'skipped_dates' => [...]]
```

### `getOccurrencesForCalendar(string $from, string $to): array`
```php
// SELECT so.*, st.TemplateName, st.SlotType, st.StaffType,
//        tb.SlotLabel, tb.StartTime, tb.EndTime,
//        f.Name AS FacilityName,
//        GROUP_CONCAT(DISTINCT u.Name ORDER BY u.Name SEPARATOR ', ') AS StaffNames
// FROM slot_occurrence so
// LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
// LEFT JOIN slot_time_band tb ON tb.SlotID = so.SlotID
// LEFT JOIN facility f ON f.FacilityID = so.FacilityID
// LEFT JOIN slot_occurrence_staff_override ov ON ov.OccurrenceID = so.OccurrenceID
// LEFT JOIN slot_template_staff ts ON ts.TemplateID = so.TemplateID
//            AND NOT EXISTS (SELECT 1 FROM slot_occurrence_staff_override
//                            WHERE OccurrenceID = so.OccurrenceID)
// LEFT JOIN user u ON u.UserID = COALESCE(ov.UserID, ts.UserID)
// WHERE so.OccurrenceDate BETWEEN :from AND :to
// GROUP BY so.OccurrenceID
// ORDER BY so.OccurrenceDate, tb.StartTime
```

### `getOccurrenceById(int $id): ?object`
```php
// Same joins as calendar query but WHERE so.OccurrenceID = :id
```

### `cancelOccurrence(int $id, string $reason, int $adminId): bool`
```php
// 1. UPDATE slot_occurrence SET Status='cancelled', CancelReason=:reason WHERE OccurrenceID=:id
// 2. INSERT INTO slot_audit_log (EntityType='occurrence', EntityID=:id,
//      Action='cancel', ChangedField='Status', OldValue='scheduled', NewValue='cancelled',
//      Reason=:reason, ChangedBy=:adminId, IPAddress=$_SERVER['REMOTE_ADDR'])
// 3. INSERT INTO activitylog (UserID=:adminId, Action='Cancelled occurrence #'.$id, ...)
// Returns true on success
```

### `substituteStaff(int $occurrenceId, int $userId, string $type, string $role, int $replacesId, string $reason, int $adminId): bool`
```php
// 1. INSERT INTO slot_occurrence_staff_override
//      (OccurrenceID, UserID, StaffType, StaffRole, OverridesUserID, OverrideReason)
// 2. INSERT INTO slot_audit_log (EntityType='occurrence', EntityID=:occurrenceId,
//      Action='override', ChangedField='StaffID', OldValue=:replacesId, NewValue=:userId,
//      Reason=:reason, ChangedBy=:adminId, IPAddress=$_SERVER['REMOTE_ADDR'])
// Returns true on success
```

### `createAdHocOccurrence(array $d, int $adminId): int`
```php
// For special camps / makeup sessions with no template (TemplateID = NULL)
// INSERT INTO slot_occurrence (TemplateID=NULL, SlotID, OccurrenceDate, FacilityID,
//   Status='scheduled', MaxParticipants, Notes, GeneratedBy=:adminId)
// INSERT INTO slot_audit_log (EntityType='occurrence', EntityID=new_id,
//   Action='create', Reason='Ad-hoc', ChangedBy=:adminId)
// Returns new OccurrenceID
```

## New Controller Methods in `Adminslots.php`

### `generate()`
```php
public function generate() {
    // GET:  show form with template dropdown (active templates only)
    // POST: call $model->generateOccurrences($templateId, $from, $to)
    //       show result: "Inserted: N  Skipped (already existed): N"
    //       Skipped dates listed so admin knows exactly which ones conflicted
}
```

### `calendar()`
```php
public function calendar() {
    // GET: default to current week (Mon–Sun)
    //      Accept ?from=YYYY-MM-DD&to=YYYY-MM-DD query params for navigation
    //      Pass occurrences as PHP array to view; view renders JS calendar grid
    //      Colour coding: program=blue, private=yellow, facility_only=green, cancelled=grey
}
```

### `occurrence($id = null)`
```php
public function occurrence($id = null) {
    // GET:  show occurrence detail + staff list + booking count
    // POST cancel:     call cancelOccurrence(), redirect back
    // POST substitute: call substituteStaff(), redirect back
    // POST adhoc:      show form for TemplateID=NULL occurrence creation
}
```

### `adhoc()`
```php
public function adhoc() {
    // GET:  show form for ad-hoc occurrence (no template required)
    // POST: call createAdHocOccurrence(), redirect to occurrence detail
}
```

## Sub-nav Links to Add

Add these links to the header actions section in `timeslots.php` and `templates.php`:
- **Generate Occurrences** → `/adminslots/generate`
- **Calendar** → `/adminslots/calendar`
- **Ad-hoc Session** → `/adminslots/adhoc`

## Business Rules
- `slot_occurrence.FacilityID` inherited from `slot_template.FacilityID` when generating
- `slot_occurrence.MaxParticipants` = NULL on generation (inherited from template at booking time)
- DB UNIQUE KEY `(FacilityID, SlotID, OccurrenceDate)` prevents double-booking — catch PDO `23000`, add to skipped list
- Staff fallback: when showing staff for an occurrence, check `slot_occurrence_staff_override` first; if empty, fall back to `slot_template_staff`
- `LegacySessionID` must be NULL for all newly generated occurrences

---

## Phase 3 — Testing Checklist

### T3.1 — Generate occurrences for April 2026
Prerequisites: Template "U15 Batting Practice" exists with DayOfWeek=1 (Monday)
```
Visit: http://localhost/Elite/adminslots/generate
Select: U15 Batting Practice, From: 2026-04-01, To: 2026-04-30
Submit
```
**Expected:** Message "Inserted: 4  Skipped: 0"

```sql
SELECT OccurrenceID, OccurrenceDate, Status, LegacySessionID
FROM slot_occurrence
WHERE TemplateID = (SELECT TemplateID FROM slot_template WHERE TemplateName='U15 Batting Practice')
ORDER BY OccurrenceDate;
```
**Expected:** 4 rows — Apr 6, Apr 13, Apr 20, Apr 27. All Status='scheduled'. LegacySessionID = NULL.

### T3.2 — Idempotent generation (no duplicates on re-run)
Submit the same form again (same template, same date range).
**Expected:** Message "Inserted: 0  Skipped: 4" — no new rows in DB.

```sql
SELECT COUNT(*) FROM slot_occurrence
WHERE TemplateID = (SELECT TemplateID FROM slot_template WHERE TemplateName='U15 Batting Practice');
```
**Expected:** Still 4 rows.

### T3.3 — Calendar view shows the occurrences
```
Visit: http://localhost/Elite/adminslots/calendar?from=2026-04-06&to=2026-04-12
```
**Expected:** Apr 6 shows "U15 Batting Practice — 03:00 PM – 05:00 PM" in blue.
Other days in the range show empty.

### T3.4 — Cancel an occurrence
```
Visit: http://localhost/Elite/adminslots/occurrence/{apr_20_occurrence_id}
Fill: Cancel reason "School holiday". Submit.
```
```sql
SELECT OccurrenceID, Status, CancelReason
FROM slot_occurrence WHERE OccurrenceDate = '2026-04-20';
```
**Expected:** Status = 'cancelled', CancelReason = 'School holiday'

```sql
SELECT EntityType, EntityID, Action, Reason, ChangedBy
FROM slot_audit_log WHERE EntityType='occurrence'
ORDER BY LogID DESC LIMIT 3;
```
**Expected:** Row with Action='cancel', Reason='School holiday'

### T3.5 — Substitute staff for one date
On the occurrence detail page for Apr 13, add a staff substitute.
Select a different coach, role=substitute, reason="Ravi sick".
```sql
SELECT ov.*, u.Name AS SubName
FROM slot_occurrence_staff_override ov
JOIN user u ON u.UserID = ov.UserID
WHERE ov.OccurrenceID = {apr_13_id};
```
**Expected:** One row with OverrideReason='Ravi sick'

Verify the original template staff assignment is unchanged:
```sql
SELECT * FROM slot_template_staff WHERE TemplateID = {template_id};
```
**Expected:** Original coach still assigned. No modification.

### T3.6 — Ad-hoc occurrence (no template)
```
Visit: http://localhost/Elite/adminslots/adhoc
Fill: Facility=Practice Net 1, Time Band=11:00 AM – 01:00 PM,
      Date=2026-04-15 (Wednesday — not covered by any template), Notes="Special camp"
Submit
```
```sql
SELECT OccurrenceID, TemplateID, OccurrenceDate, Notes FROM slot_occurrence
WHERE OccurrenceDate = '2026-04-15';
```
**Expected:** One row with TemplateID = NULL, Notes = 'Special camp'

### T3.7 — Double-booking prevention
Try to generate a second template occurrence on the same Facility+Band+Date that already has an occurrence.
**Expected:** That date shows in the "Skipped" list. DB unchanged (still 1 row for that Facility+Band+Date).

---

---

# PHASE 4 — Service Layer: Subscription Gate + Medical Flag ⬜ TODO

## File to Create

### `app/libraries/SlotBookingService.php`

```php
<?php
class SlotBookingService {

    /**
     * Check player has active subscription covering this slot type.
     * Returns: ['ok'=>true]
     * or:      ['ok'=>false, 'code'=>'no_subscription'|'expired'|'plan_mismatch', 'message'=>'...']
     */
    public static function validateEntitlement(int $playerId, int $templateId): array {
        // 1. Load slot_template.RequiredPlanFeature
        // 2. If RequiredPlanFeature = 'none' → return ['ok'=>true]
        // 3. Load playersubscription WHERE PlayerID=:pid AND Status='active'
        //    → If none found: return ['ok'=>false, 'code'=>'no_subscription']
        // 4. Load membershipplan for that subscription
        // 5. Check feature map:
        //    'sessions'         → plan.SessionsPerWeek > 0
        //    'private_sessions' → plan.PrivateSessionsIncluded > 0
        //    'facility_access'  → plan.FacilityAccessIncluded = 1
        //    Fail → return ['ok'=>false, 'code'=>'plan_mismatch', 'message'=>'Your plan does not include ...']
        // 6. return ['ok'=>true, 'subscription_id'=>$sub->SubscriptionID]
    }

    /**
     * Check player has no active injury within rest window.
     * Returns: ['ok'=>true]
     * or:      ['ok'=>false, 'code'=>'active_injury', 'rest_days'=>N, 'injury_note'=>'...', 'message'=>'...']
     */
    public static function checkMedicalFlag(int $playerId, string $occurrenceDate): array {
        // SELECT * FROM playermedicalrecord
        // WHERE PlayerID = :pid
        //   AND RecoveryStatus IN ('ongoing','recovering')
        //   AND DATE_ADD(InjuryDate, INTERVAL RestDaysNeeded DAY) >= :occurrenceDate
        // If row found:
        //   $daysLeft = (strtotime(date_add result) - strtotime($occurrenceDate)) / 86400
        //   return ['ok'=>false, 'code'=>'active_injury', 'rest_days'=>$daysLeft,
        //           'injury_note'=>$record->InjuryNote, 'message'=>'Player has active injury...']
        // return ['ok'=>true]
    }

    /**
     * Check occurrence is not full.
     * MaxParticipants = occurrence.MaxParticipants ?? template.MaxParticipants
     * Returns: ['ok'=>true, 'spots_left'=>N]
     * or:      ['ok'=>false, 'code'=>'full', 'message'=>'Session is full']
     */
    public static function checkCapacity(int $occurrenceId): array {
        // SELECT so.MaxParticipants AS OccMax, st.MaxParticipants AS TplMax,
        //        COUNT(sb.BookingID) AS Booked
        // FROM slot_occurrence so
        // JOIN slot_template st ON st.TemplateID = so.TemplateID
        // LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID
        //   AND sb.Status NOT IN ('cancelled')
        // WHERE so.OccurrenceID = :id
        // $max = $row->OccMax ?? $row->TplMax;
        // if ($row->Booked >= $max) return ['ok'=>false, 'code'=>'full', ...]
        // return ['ok'=>true, 'spots_left' => $max - $row->Booked]
    }
}
```

## New Method in `Adminslots.php`

### `clearMedical($bookingId = null)`
```php
public function clearMedical($bookingId = null) {
    // POST only — Admin sets slot_booking.MedicalClearedBy = session user_id
    // 1. UPDATE slot_booking SET MedicalClearedBy = :adminId WHERE BookingID = :bid
    // 2. INSERT INTO slot_audit_log (EntityType='booking', EntityID=:bid,
    //      Action='update', ChangedField='MedicalClearedBy', NewValue=:adminId,
    //      ChangedBy=:adminId)
    // 3. redirect back to occurrence page
}
```

---

## Phase 4 — Testing Checklist

### T4.1 — Create a manual test script
Create `test_slot_service.php` in project root for direct testing:
```php
<?php
define('BASEPATH', true);
require_once 'app/config/config.php';
require_once 'app/libraries/Database.php';
require_once 'app/libraries/SlotBookingService.php';

echo "<h2>Test 1: Player with no subscription</h2>";
$r = SlotBookingService::validateEntitlement(999, 1); // non-existent player
var_dump($r);
// Expected: ['ok'=>false, 'code'=>'no_subscription']

echo "<h2>Test 2: Player with active subscription, wrong plan</h2>";
// Use a real player ID who has an active subscription on a plan with SessionsPerWeek=0
$r = SlotBookingService::validateEntitlement($playerIdBasicPlan, $templateIdRequiringSession);
var_dump($r);
// Expected: ['ok'=>false, 'code'=>'plan_mismatch']

echo "<h2>Test 3: Player with active injury</h2>";
// Use a real player who has a playermedicalrecord with RecoveryStatus='ongoing'
$r = SlotBookingService::checkMedicalFlag($injuredPlayerId, '2026-04-13');
var_dump($r);
// Expected: ['ok'=>false, 'code'=>'active_injury', 'rest_days'=>N]

echo "<h2>Test 4: Full occurrence</h2>";
// Create an occurrence with MaxParticipants=1, add one booking manually in DB
$r = SlotBookingService::checkCapacity($fullOccurrenceId);
var_dump($r);
// Expected: ['ok'=>false, 'code'=>'full']

echo "<h2>Test 5: All checks pass</h2>";
// Player with active matching subscription, no injury, occurrence has space
$r1 = SlotBookingService::validateEntitlement($goodPlayerId, $goodTemplateId);
$r2 = SlotBookingService::checkMedicalFlag($goodPlayerId, '2026-04-20');
$r3 = SlotBookingService::checkCapacity($openOccurrenceId);
var_dump($r1, $r2, $r3);
// Expected: all ['ok'=>true]
```
```
Visit: http://localhost/Elite/test_slot_service.php
```
**Delete this file before going to production.**

### T4.2 — RequiredPlanFeature = 'none' always passes
```php
$r = SlotBookingService::validateEntitlement($anyPlayerId, $templateIdWithNone);
```
**Expected:** `['ok'=>true]` regardless of player's subscription status.

### T4.3 — Medical flag: player outside rest window passes
Use a player with `InjuryDate + RestDaysNeeded < CURDATE()` (injury recovered).
```php
$r = SlotBookingService::checkMedicalFlag($recoveredPlayerId, '2026-04-20');
```
**Expected:** `['ok'=>true]`

### T4.4 — Admin can clear medical flag
```
Visit: http://localhost/Elite/adminslots/occurrence/{id_with_flagged_booking}
Click "Clear Medical Flag" on the flagged booking row.
```
```sql
SELECT BookingID, MedicalClearedBy, Status
FROM slot_booking WHERE BookingID = {flagged_booking_id};
```
**Expected:** `MedicalClearedBy` = admin's UserID (not NULL).

```sql
SELECT Action, ChangedField, ChangedBy FROM slot_audit_log
WHERE EntityType='booking' AND EntityID = {flagged_booking_id}
ORDER BY LogID DESC LIMIT 1;
```
**Expected:** Action='update', ChangedField='MedicalClearedBy'

### T4.5 — ShopEmployee cannot clear medical flag
Log in as ShopEmployee.
**Expected:** The "Clear Medical Flag" button is not visible or the route returns a redirect.

---

---

# PHASE 5 — Player + ShopEmployee Booking ⬜ TODO

## Files to Create

| File | Purpose |
|------|---------|
| `app/models/M_SlotPlayer.php` | Player-side DB operations |
| `app/controllers/Playerslots.php` | Player booking controller |
| `app/controllers/Shopemployee.php` | Counter booking controller |
| `app/views/player/slots.php` | Available sessions grid |
| `app/views/player/slot_bookings.php` | My bookings: upcoming + past + cancel |
| `app/views/shopemployee/counter_booking.php` | Walk-in booking interface |

---

## `M_SlotPlayer.php`

### `getAvailableOccurrences(int $playerId): array`
```php
// SELECT so.OccurrenceID, so.OccurrenceDate, so.MaxParticipants,
//        st.TemplateName, st.SlotType, st.StaffType, st.TemplateID,
//        st.PricePerSession, st.RequiredPlanFeature,
//        tb.SlotLabel, tb.StartTime, tb.EndTime,
//        f.Name AS FacilityName,
//        COUNT(sb.BookingID) AS BookedCount
// FROM slot_occurrence so
// JOIN slot_template st ON st.TemplateID = so.TemplateID
// JOIN slot_time_band tb ON tb.SlotID = so.SlotID
// LEFT JOIN facility f ON f.FacilityID = so.FacilityID
// LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID
//           AND sb.Status NOT IN ('cancelled')
// WHERE so.Status IN ('scheduled','active')
//   AND so.OccurrenceDate >= CURDATE()
//   AND st.IsActive = 1
// GROUP BY so.OccurrenceID
// ORDER BY so.OccurrenceDate, tb.StartTime
//
// After fetch, filter in PHP using SlotBookingService:
// foreach ($rows as $key => $r) {
//   $ent = SlotBookingService::validateEntitlement($playerId, $r->TemplateID);
//   $med = SlotBookingService::checkMedicalFlag($playerId, $r->OccurrenceDate);
//   $cap = SlotBookingService::checkCapacity($r->OccurrenceID);
//   $r->blocked = !($ent['ok'] && $med['ok'] && $cap['ok']);
//   $r->blockReason = !$ent['ok'] ? $ent['code'] : (!$med['ok'] ? $med['code'] : $cap['code']);
//   // Keep the row in list but mark it blocked — UI shows reason
// }
```

### `createBooking(int $occurrenceId, int $playerId, string $source, int $bookedBy = null, int $subscriptionId = null, float $amount = 0, string $payMethod = null, string $payStatus = 'not_required'): bool|string`
```php
// Run all 3 service checks before INSERT (MANDATORY):
//   $ent = SlotBookingService::validateEntitlement(...)
//   $med = SlotBookingService::checkMedicalFlag(...)
//   $cap = SlotBookingService::checkCapacity(...)
// If any fails → return error code string
//
// INSERT INTO slot_booking
//   (OccurrenceID, PlayerID, BookingSource, SubscriptionID, Status,
//    AmountCharged, PaymentStatus, PaymentMethod, BookedBy)
// Catch PDOException SQLSTATE '23000' → return 'duplicate'
// On success → INSERT slot_audit_log + INSERT notification + INSERT activitylog
// return true
```

### `getPlayerBookings(int $playerId): array`
```php
// SELECT sb.*, so.OccurrenceDate, tb.SlotLabel, tb.StartTime,
//        st.TemplateName, st.SlotType, f.Name AS FacilityName
// FROM slot_booking sb
// JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
// JOIN slot_time_band tb ON tb.SlotID = so.SlotID
// JOIN slot_template st ON st.TemplateID = so.TemplateID
// LEFT JOIN facility f ON f.FacilityID = so.FacilityID
// WHERE sb.PlayerID = :pid
// ORDER BY so.OccurrenceDate DESC
```

### `cancelBooking(int $bookingId, int $playerId): bool|string`
```php
// 1. Load booking — verify PlayerID = :playerId (security: player cannot cancel another's booking)
// 2. Load occurrence date
// 3. if (OccurrenceDate - CURDATE() < 1 day) return 'window_closed'
// 4. UPDATE slot_booking SET Status='cancelled', CancelledBy=:playerId,
//       CancelledAt=NOW(), CancelReason='Player cancelled'
//    WHERE BookingID=:id AND PlayerID=:playerId
// 5. INSERT slot_audit_log, INSERT notification, INSERT activitylog
// return true
```

---

## `Playerslots.php`

```php
class Playerslots extends Controller {
    public function __construct() {
        requireAuth(['Player']);  // blocks all other roles
    }

    public function index()    { redirect('playerslots/available'); }
    public function available() { /* GET: show getAvailableOccurrences grid */ }
    public function book()      { /* POST: createBooking — all 3 checks enforced in model */ }
    public function bookings()  { /* GET: show getPlayerBookings */ }
    public function cancel()    { /* POST: cancelBooking */ }
}
```

---

## `Shopemployee.php`

```php
class Shopemployee extends Controller {
    public function __construct() {
        requireAuth(['ShopEmployee']);
    }

    public function index()        { redirect('shopemployee/counter'); }
    public function counter()      { /* GET: show available facility_only and private slots */ }
    public function searchplayer() { /* POST: AJAX − search player by name/ID for lookup */ }
    public function book()         {
        // POST: createBooking with:
        //   BookingSource = 'shop_employee'
        //   BookedBy      = $_SESSION['user_id']
        //   PaymentMethod = 'cash'
        //   PaymentStatus = 'paid'
        //
        // If medical flag: show warning and "Direct player to Admin" message.
        // ShopEmployee CANNOT call clearMedical — booking is blocked.
        //
        // ShopEmployee can only book SlotType IN ('facility_only','private')
        // Program slots are Admin-only enrollment
    }
}
```

---

### Booking status flow (important for program slots)
- **Self-booking (Player):** `pending` → confirm immediately if all checks pass → `confirmed`
- **Counter-booking (ShopEmployee):** directly `confirmed` (cash paid on the spot)
- **Admin-enrolled (programs):** `pending` → Admin reviews → `confirmed`

---

## Phase 5 — Testing Checklist

### T5.1 — Player with expired subscription sees no sessions
Log in as a player whose subscription has expired.
```
Visit: http://localhost/Elite/playerslots/available
```
**Expected:** 0 bookable sessions shown. "Subscription required" banner visible.

### T5.2 — Player with facility_access plan can see facility slots
Log in as a player with `membershipplan.FacilityAccessIncluded = 1`.
**Expected:** `facility_only` slot occurrences visible. `program` and `private` slots are blocked/greyed.

### T5.3 — Successful self-booking
Book an available facility slot.
```sql
SELECT BookingID, OccurrenceID, PlayerID, BookingSource, Status, PaymentStatus
FROM slot_booking ORDER BY BookingID DESC LIMIT 1;
```
**Expected:** BookingSource='self', Status='confirmed', PaymentStatus as per template price.

### T5.4 — Duplicate booking blocked
Attempt to book the same occurrence again.
**Expected:** "Already booked" error message. No new row in `slot_booking`.

### T5.5 — Full session blocked
Manually set MaxParticipants=1 on an occurrence. Book it once. Attempt to book again with a different player.
**Expected:** "Session is full" error.

### T5.6 — Cancel within 24-hour window
Book a session for a date >= 2 days from now. Visit My Bookings → click Cancel.
```sql
SELECT Status, CancelledBy, CancelledAt FROM slot_booking WHERE BookingID = {id};
```
**Expected:** Status='cancelled'

### T5.7 — Cancel blocked after window
Book a session for tomorrow (less than 24h away). Attempt to cancel.
**Expected:** "Cancellation window is closed" error. Status unchanged.

### T5.8 — ShopEmployee counter booking
Log in as ShopEmployee.
```
Visit: http://localhost/Elite/shopemployee/counter
Search for player, select a facility slot, submit.
```
```sql
SELECT BookingID, BookingSource, BookedBy, PaymentMethod, PaymentStatus
FROM slot_booking ORDER BY BookingID DESC LIMIT 1;
```
**Expected:** BookingSource='shop_employee', PaymentMethod='cash', PaymentStatus='paid'

### T5.9 — ShopEmployee blocked from program slots
Attempt to book a `SlotType='program'` occurrence from the counter.
**Expected:** Error or slot not shown in the counter interface.

### T5.10 — ShopEmployee cannot override medical flag
Book for a player with an active injury as ShopEmployee.
**Expected:** Warning shown: "This player has an active injury. Direct player to Admin to clear flag." Booking blocked.

### T5.11 — ShopEmployee cannot access admin routes
```
Visit: http://localhost/Elite/adminslots/templates  (while logged in as ShopEmployee)
```
**Expected:** Redirect to login or access-denied page.

### T5.12 — Player cannot access ShopEmployee routes
```
Visit: http://localhost/Elite/shopemployee/counter  (while logged in as Player)
```
**Expected:** Redirect to login or access-denied page.

---

---

# PHASE 6 — Coach + Trainer Schedule Views + Attendance ⬜ TODO

## Files to Create

| File | Purpose |
|------|---------|
| `app/controllers/Coachslots.php` | Coach schedule + attendance controller |
| `app/controllers/Trainerslots.php` | Trainer schedule + attendance controller |
| `app/models/M_SlotStaff.php` | Shared DB operations for coach/trainer slot views |
| `app/views/coach/slots/schedule.php` | Coach weekly schedule view |
| `app/views/coach/slots/attendance.php` | Mark attendance + link to coachingsession notes |
| `app/views/trainer/slots/schedule.php` | Trainer weekly schedule view |
| `app/views/trainer/slots/attendance.php` | Mark attendance for fitness sessions |

---

## `M_SlotStaff.php`

### `getOccurrencesForStaff(int $userId, string $from, string $to): array`
```php
// Return occurrences where this staff member is assigned:
// Priority 1 — slot_occurrence_staff_override WHERE UserID = :uid
// Priority 2 — slot_template_staff WHERE UserID = :uid (if no override exists for that occurrence)
//
// SELECT so.OccurrenceID, so.OccurrenceDate, so.Status,
//        st.TemplateName, st.SlotType, st.StaffType, st.TemplateID,
//        tb.SlotLabel, tb.StartTime, tb.EndTime,
//        f.Name AS FacilityName,
//        COUNT(sb.BookingID) AS PlayerCount
// FROM slot_occurrence so
// JOIN slot_template st ON st.TemplateID = so.TemplateID
// JOIN slot_time_band tb ON tb.SlotID = so.SlotID
// LEFT JOIN facility f ON f.FacilityID = so.FacilityID
// LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID AND sb.Status != 'cancelled'
// WHERE so.OccurrenceDate BETWEEN :from AND :to
//   AND so.Status != 'cancelled'
//   AND (
//     EXISTS (SELECT 1 FROM slot_occurrence_staff_override ov
//             WHERE ov.OccurrenceID = so.OccurrenceID AND ov.UserID = :uid)
//     OR (
//       EXISTS (SELECT 1 FROM slot_template_staff ts
//               WHERE ts.TemplateID = so.TemplateID AND ts.UserID = :uid)
//       AND NOT EXISTS (SELECT 1 FROM slot_occurrence_staff_override ov2
//                       WHERE ov2.OccurrenceID = so.OccurrenceID)
//     )
//   )
// GROUP BY so.OccurrenceID
// ORDER BY so.OccurrenceDate, tb.StartTime
```

### `getEnrolledPlayersForOccurrence(int $occurrenceId): array`
```php
// SELECT sb.BookingID, sb.Status AS BookingStatus, sb.MedicalClearedBy,
//        u.UserID, u.Name AS PlayerName, u.Email,
//        cs.CoachingSessionID, cs.SkillsWorkedOn, cs.AreasForImprovement
// FROM slot_booking sb
// JOIN user u ON u.UserID = sb.PlayerID
// LEFT JOIN coachingsession cs ON cs.SessionID = :occurrenceId AND cs.PlayerID = sb.PlayerID
// WHERE sb.OccurrenceID = :occurrenceId AND sb.Status != 'cancelled'
// ORDER BY u.Name
```

### `markAttendance(int $bookingId, string $status, int $staffId): bool`
```php
// $status IN ('attended','missed')
// UPDATE slot_booking SET Status = :status WHERE BookingID = :id
// INSERT slot_audit_log (EntityType='booking', EntityID=:id, Action='update',
//   ChangedField='Status', NewValue=:status, ChangedBy=:staffId)
```

---

## `Coachslots.php`

```php
class Coachslots extends Controller {
    public function __construct() {
        requireAuth(['Coach', 'Admin']);
    }

    public function index()       { redirect('coachslots/schedule'); }
    public function schedule()    { /* GET: weekly view of assigned occurrences */ }
    public function attendance($occurrenceId = null) {
        // GET:  list enrolled players for this occurrence
        // POST: mark each player attended/missed
        //       After marking, prompt coach to fill coachingsession notes for each player
        //       Only works if the logged-in coach is assigned to this occurrence
        //       (via slot_template_staff OR slot_occurrence_staff_override)
        //       Head coach (isHeadCoach=1) can view all
    }
}
```

---

## `Trainerslots.php`

```php
class Trainerslots extends Controller {
    public function __construct() {
        requireAuth(['Trainer']);
    }

    public function index()       { redirect('trainerslots/schedule'); }
    public function schedule()    { /* GET: weekly view of assigned physical training occurrences */ }
    public function attendance($occurrenceId = null) {
        // GET:  list enrolled players for this occurrence
        // POST: mark each player attended/missed
        //       No coachingsession prompt — trainer notes go to trainerappointment
    }
}
```

---

## Business Rules
- A coach/trainer can only view the player list for occurrences they are assigned to.
  Check `slot_template_staff.UserID = logged_in_user_id` OR `slot_occurrence_staff_override.UserID = logged_in_user_id`.
- Head coach (`coachprofile.IsHeadCoach = 1`) overrides this — can see all occurrences across all coaches.
- Marking attendance writes to `slot_booking.Status` (attended/missed) — this is NOT a new table.
- After marking attended, the view prompts the coach to navigate to the existing `coachingsession` form for that occurrence. The link between them is `coachingsession.SessionID = slot_occurrence.OccurrenceID` (or LegacySessionID for migrated rows).

---

## Phase 6 — Testing Checklist

### T6.1 — Coach sees only their assigned occurrences
Log in as a coach who is assigned to "U15 Batting Practice" but not to another template.
```
Visit: http://localhost/Elite/coachslots/schedule
```
**Expected:** Only "U15 Batting Practice" occurrences appear. No other templates' sessions visible.

### T6.2 — Head coach sees all occurrences
```sql
UPDATE coachprofile SET IsHeadCoach = 1 WHERE UserID = {head_coach_id};
```
Log in as that coach.
**Expected:** All occurrences across all templates visible in the schedule.

### T6.3 — Coach cannot see another coach's private session player list
As non-head coach, attempt to visit attendance page for an occurrence assigned to a different coach.
**Expected:** Redirect or "Access denied" message.

### T6.4 — Mark attendance
```
Visit: http://localhost/Elite/coachslots/attendance/{occurrence_id}
Mark player A as 'attended', player B as 'missed'. Submit.
```
```sql
SELECT sb.BookingID, sb.Status, u.Name
FROM slot_booking sb JOIN user u ON u.UserID = sb.PlayerID
WHERE sb.OccurrenceID = {occurrence_id};
```
**Expected:** Player A Status='attended', Player B Status='missed'

### T6.5 — Trainer role is separate from coach
Log in as a Trainer.
```
Visit: http://localhost/Elite/coachslots/schedule
```
**Expected:** Access denied / redirect.

```
Visit: http://localhost/Elite/trainerslots/schedule
```
**Expected:** Shows only physical training occurrences assigned to this trainer.

### T6.6 — Attendance feeds coachingsession prompt
After marking attendance on an occurrence, verify a link/button to fill `coachingsession` notes is shown for each attended player.
Check that the link points to the existing coach performance notes form with the correct occurrence/session ID pre-filled.

---

---

# PHASE 7 — Notifications + Audit Trail ⬜ TODO

## File to Create

### `app/libraries/SlotAuditService.php`

```php
<?php
class SlotAuditService {

    /**
     * Append-only. NEVER called with UPDATE or DELETE.
     * Call after every state change in every controller and model.
     */
    public static function log(
        string  $entityType,    // 'template'|'occurrence'|'booking'|'staff_assignment'
        int     $entityId,
        string  $action,        // 'create'|'update'|'cancel'|'delete'|'override'
        int     $userId,
        string  $changedField = null,
        string  $oldValue     = null,
        string  $newValue     = null,
        string  $reason       = null,
        string  $ip           = null
    ): void {
        // INSERT INTO slot_audit_log (EntityType, EntityID, Action, ChangedField,
        //   OldValue, NewValue, Reason, ChangedBy, ChangedAt, IPAddress)
        // VALUES (...)
        // NO UPDATE. NO DELETE. EVER.
        $db = new Database();
        $db->query('INSERT INTO slot_audit_log
            (EntityType, EntityID, Action, ChangedField, OldValue, NewValue, Reason, ChangedBy, IPAddress)
            VALUES (:et, :eid, :act, :cf, :ov, :nv, :reason, :uid, :ip)');
        $db->bind(':et',     $entityType);
        $db->bind(':eid',    $entityId,   PDO::PARAM_INT);
        $db->bind(':act',    $action);
        $db->bind(':cf',     $changedField);
        $db->bind(':ov',     $oldValue);
        $db->bind(':nv',     $newValue);
        $db->bind(':reason', $reason);
        $db->bind(':uid',    $userId,     PDO::PARAM_INT);
        $db->bind(':ip',     $ip ?? ($_SERVER['REMOTE_ADDR'] ?? null));
        $db->execute();
    }

    /**
     * Insert a notification row.
     * $urgency: 'low'|'medium'|'high'
     * High urgency also inserts into livenotification.
     */
    public static function notify(
        int    $toUserId,
        string $message,
        string $urgency = 'medium',
        string $relatedType = null,
        int    $relatedId   = null
    ): void {
        $db = new Database();
        // INSERT INTO notification (UserID, Message, IsRead, CreatedAt)
        $db->query('INSERT INTO notification (UserID, Message, IsRead, CreatedAt)
                    VALUES (:uid, :msg, 0, NOW())');
        $db->bind(':uid', $toUserId, PDO::PARAM_INT);
        $db->bind(':msg', $message);
        $db->execute();

        if ($urgency === 'high') {
            // INSERT INTO livenotification (UserID, Message, CreatedAt)
            $db->query('INSERT INTO livenotification (UserID, Message, CreatedAt)
                        VALUES (:uid, :msg, NOW())');
            $db->bind(':uid', $toUserId, PDO::PARAM_INT);
            $db->bind(':msg', $message);
            $db->execute();
        }
    }
}
```

---

## Where to Call `SlotAuditService::log()`

Refactor each model method to use `SlotAuditService` instead of inline INSERT statements
(inline inserts in Phase 3 and 4 models are acceptable for initial build — consolidate here).

| Event | EntityType | Action | Notes |
|-------|-----------|--------|-------|
| Template created | template | create | |
| Template edited | template | update | One call per changed field |
| Staff assigned | staff_assignment | create | |
| Staff removed | staff_assignment | delete | |
| Occurrence generated | occurrence | create | |
| Occurrence cancelled | occurrence | cancel | |
| Staff substituted | occurrence | override | |
| Booking created | booking | create | |
| Booking cancelled | booking | cancel | |
| Medical flag cleared | booking | update | ChangedField='MedicalClearedBy' |
| Attendance marked | booking | update | ChangedField='Status' |

---

## Where to Call `SlotAuditService::notify()`

| Event | Who | Urgency | Also livenotification? |
|-------|-----|---------|------------------------|
| Booking confirmed | That player | medium | No |
| Occurrence cancelled | ALL booked players on that occurrence | high | Yes |
| Booking cancelled by admin/shop | That player | high | Yes |
| Staff substituted | Original + substitute staff | medium | No |
| Booking blocked (sub expired) | Admin | high | Yes |
| Medical flag triggered | Admin | high | Yes |
| Payment not confirmed 24h before session | Player + ShopEmployee | medium | No |

---

## `activitylog` writes (missed from original plan)

Every slot action must also write to the existing `activitylog` table so non-slot admin reports still show slot activity.

```php
// Pattern: write to activitylog alongside slot_audit_log
$db->query('INSERT INTO activitylog (UserID, Action, Details, IPAddress, CreatedAt)
            VALUES (:uid, :action, :details, :ip, NOW())');
```

Write to `activitylog` for:
- Occurrence generated (bulk)
- Occurrence cancelled
- Booking created (any source)
- Booking cancelled
- Medical flag cleared

---

## Phase 7 — Testing Checklist

### T7.1 — Audit log written on occurrence cancel
Cancel an occurrence.
```sql
SELECT EntityType, EntityID, Action, OldValue, NewValue, Reason, ChangedBy, IPAddress
FROM slot_audit_log
WHERE EntityType='occurrence' ORDER BY LogID DESC LIMIT 5;
```
**Expected:** Row with Action='cancel', OldValue='scheduled', NewValue='cancelled', IPAddress filled.

### T7.2 — Audit log is append-only
Check that no controller or model file contains UPDATE or DELETE SQL targeting `slot_audit_log`.
```bash
grep -r "slot_audit_log" /Applications/XAMPP/xamppfiles/htdocs/Elite/app/
```
**Expected:** Every match is an INSERT statement. Zero UPDATE or DELETE matches.

### T7.3 — Notification sent on booking confirmed
Create a booking for a player.
```sql
SELECT UserID, Message, IsRead, CreatedAt
FROM notification
WHERE UserID = {player_id}
ORDER BY NotificationID DESC LIMIT 3;
```
**Expected:** A "booking confirmed" notification exists.

### T7.4 — livenotification on occurrence cancel
Cancel an occurrence that has booked players.
```sql
SELECT UserID, Message FROM livenotification
WHERE UserID IN (SELECT PlayerID FROM slot_booking WHERE OccurrenceID = {cancelled_id})
ORDER BY ID DESC LIMIT 5;
```
**Expected:** One livenotification per booked player.

### T7.5 — Admin notified on medical flag
Attempt a booking for a player with an active injury.
```sql
SELECT UserID, Message FROM notification
WHERE Message LIKE '%injury%' OR Message LIKE '%medical%'
ORDER BY NotificationID DESC LIMIT 3;
```
**Expected:** Admin receives notification.

### T7.6 — activitylog receives slot actions
Cancel an occurrence.
```sql
SELECT UserID, Action, Details, CreatedAt FROM activitylog
ORDER BY LogID DESC LIMIT 5;
```
**Expected:** Row with action related to "slot" or "occurrence cancelled".

### T7.7 — No delete button exists anywhere for audit log
Inspect all admin views — there must be zero delete or edit buttons on any `slot_audit_log` display.
The changelog page is read-only (no forms, no POST routes for it).

---

---

# PHASE 8 — Reporting + Changelog Page ⬜ TODO

## Files to Create

| File | Purpose |
|------|---------|
| `app/views/admin/slots/reports.php` | Three report sections on one page |
| `app/views/admin/slots/changelog.php` | Read-only `slot_audit_log` viewer |

## Methods to Add to `M_SlotAdmin.php`

### `getFacilityUtilisation(int $month, int $year): array`
```php
// Count occurrences per facility for the month
// Compare against possible slots = days_in_month * COUNT(active time bands)
// SELECT f.FacilityID, f.Name AS FacilityName,
//        COUNT(so.OccurrenceID) AS SessionsHeld,
//        DAY(LAST_DAY(:yearmonth)) * (SELECT COUNT(*) FROM slot_time_band WHERE IsActive=1) AS PossibleSlots,
//        ROUND(COUNT(so.OccurrenceID) /
//              (DAY(LAST_DAY(:yearmonth)) * (SELECT COUNT(*) FROM slot_time_band WHERE IsActive=1)) * 100, 1)
//        AS UtilisationPct
// FROM facility f
// LEFT JOIN slot_occurrence so ON so.FacilityID = f.FacilityID
//   AND MONTH(so.OccurrenceDate) = :month AND YEAR(so.OccurrenceDate) = :year
//   AND so.Status != 'cancelled'
// GROUP BY f.FacilityID
// ORDER BY SessionsHeld DESC
```

### `getRevenueReport(string $from, string $to): array`
```php
// SELECT sb.BookingSource, st.SlotType,
//        SUM(sb.AmountCharged) AS TotalRevenue,
//        COUNT(sb.BookingID) AS TotalBookings,
//        SUM(CASE WHEN sb.PaymentStatus='paid' THEN sb.AmountCharged ELSE 0 END) AS PaidRevenue
// FROM slot_booking sb
// JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
// JOIN slot_template st  ON st.TemplateID = so.TemplateID
// WHERE sb.PaymentStatus IN ('paid','pending')
//   AND sb.CreatedAt BETWEEN :from AND :to
//   AND sb.Status != 'cancelled'
// GROUP BY sb.BookingSource, st.SlotType
// ORDER BY TotalRevenue DESC
```

### `getStaffWorkload(int $month, int $year): array`
```php
// Count distinct occurrences per UserID from slot_template_staff
// (override assignments checked too)
// SELECT u.UserID, u.Name, u.Role,
//        ts.StaffRole,
//        COUNT(DISTINCT so.OccurrenceID) AS SessionsTotal
// FROM slot_template_staff ts
// JOIN user u ON u.UserID = ts.UserID
// JOIN slot_occurrence so ON so.TemplateID = ts.TemplateID
//   AND MONTH(so.OccurrenceDate) = :month AND YEAR(so.OccurrenceDate) = :year
//   AND so.Status != 'cancelled'
//   AND NOT EXISTS (
//     SELECT 1 FROM slot_occurrence_staff_override ov
//     WHERE ov.OccurrenceID = so.OccurrenceID AND ov.OverridesUserID = ts.UserID
//   )
// GROUP BY u.UserID, ts.StaffRole
// ORDER BY SessionsTotal DESC
```

### `getAuditLog(int $page = 1, int $perPage = 50, string $entityType = null): array`
```php
// SELECT al.*, u.Name AS ChangedByName
// FROM slot_audit_log al JOIN user u ON u.UserID = al.ChangedBy
// WHERE (:et IS NULL OR al.EntityType = :et)
// ORDER BY al.LogID DESC
// LIMIT :offset, :limit
```

## New Routes in `Adminslots.php`

### `reports()`
```php
public function reports() {
    // GET: default month = current month
    //      Accept ?month=&year= and ?from=&to= query params
    //      Pass all 3 report arrays to view
}
```

### `changelog()`
```php
public function changelog() {
    // GET only — no POST handling
    // Read-only paged view of slot_audit_log
    // Filter by EntityType (dropdown) and date range
    // NO edit, NO delete, NO form submissions except filter
}
```

## Sub-nav to Add
Add "Reports" and "Change Log" links to the existing slot management sub-nav on all slot admin pages.

---

## Phase 8 — Testing Checklist

### T8.1 — Facility utilisation report
Generate 10 occurrences across 2 facilities for current month.
```
Visit: http://localhost/Elite/adminslots/reports
Select current month + year.
```
**Expected:** Table shows both facilities with non-zero SessionsHeld and UtilisationPct > 0.

### T8.2 — Revenue report splits correctly
Create bookings with different BookingSource values and AmountCharged > 0. Set PaymentStatus='paid'.
Select a date range that covers those bookings.
**Expected:** Report splits by BookingSource and SlotType. `SUM(AmountCharged)` matches DB sum:
```sql
SELECT BookingSource, SUM(AmountCharged) FROM slot_booking
WHERE PaymentStatus='paid' AND Status != 'cancelled' GROUP BY BookingSource;
```

### T8.3 — Staff workload shows correct session counts
Assign 2 coaches to different templates. Generate 3 occurrences each.
**Expected:** Both coaches appear in workload report. Each shows 3 sessions.

### T8.4 — Changelog page is read-only
```
Visit: http://localhost/Elite/adminslots/changelog
```
**Expected:**
- Paginated log entries showing EntityType, Action, ChangedBy, ChangedAt
- No edit button, no delete button, no form other than filter
- Shows entries from Phase 3, 4, 5, 6, 7 actions

### T8.5 — Changelog filter by entity type
Use the filter dropdown to show only `booking` entries.
**Expected:** Only rows with EntityType='booking' visible.

### T8.6 — Non-admin cannot access changelog
Log in as Player or ShopEmployee.
```
Visit: http://localhost/Elite/adminslots/changelog
```
**Expected:** Redirect to login or access denied.

---

---

# Non-Negotiable Code Rules (All Phases)

These apply to every file in every phase. Violating any of these is a defect, not a choice.

| # | Rule | Where Enforced |
|---|------|----------------|
| 1 | `slot_audit_log` is append-only. Zero UPDATE or DELETE on it, ever. | Every model + controller |
| 2 | `slot_time_band` rows are never deleted. Only `UPDATE IsActive = 0`. | Adminslots controller + view |
| 3 | Every booking INSERT must call all 3 `SlotBookingService` checks first. | `M_SlotPlayer::createBooking()` |
| 4 | SQLSTATE `23000` on `slot_occurrence` caught → "facility already booked that slot on that day". | `generateOccurrences()` |
| 5 | `slot_template_staff.StaffType` must match `slot_template.StaffType`. | `M_SlotAdmin::assignStaff()` |
| 6 | `user.Role` must match `staff_type` on staff assignment. | `M_SlotAdmin::assignStaff()` |
| 7 | ShopEmployee controller blocks all template/occurrence/medical routes. | `Shopemployee::__construct()` |
| 8 | Medical override only by Admin via `MedicalClearedBy` field. | `Adminslots::clearMedical()` |
| 9 | Player cancellation blocked if `OccurrenceDate - CURDATE() < 1 day`. | `M_SlotPlayer::cancelBooking()` |
| 10 | ShopEmployee can only book `facility_only` and `private` slot types. | `Shopemployee::book()` |
| 11 | High-urgency events write to both `notification` AND `livenotification`. | `SlotAuditService::notify()` |
| 12 | All slot actions also write to `activitylog`. | All create/cancel/override paths |
| 13 | `LegacySessionID` must be NULL for all newly generated/created occurrences. | `generateOccurrences()` + `createAdHocOccurrence()` |

---

# Files Reference — Complete List

## Already Created (Phases 1–2)
```
create_slot_tables.sql
app/controllers/Adminslots.php
app/models/M_SlotAdmin.php
app/views/admin/slots/timeslots.php
app/views/admin/slots/templates.php
app/views/admin/slots/template_form.php
app/views/admin/slots/staff.php
public/css/admin/admin-dashboard.css  (bug fix applied)
```

## To Create in Phase 3
```
app/views/admin/slots/generate.php
app/views/admin/slots/calendar.php
app/views/admin/slots/occurrence.php
  — Methods added to M_SlotAdmin.php:
     generateOccurrences()
     getOccurrencesForCalendar()
     getOccurrenceById()
     cancelOccurrence()
     substituteStaff()
     createAdHocOccurrence()
  — Methods added to Adminslots.php:
     generate()
     calendar()
     occurrence()
     adhoc()
```

## To Create in Phase 4
```
app/libraries/SlotBookingService.php
  — Method added to Adminslots.php:
     clearMedical()
```

## To Create in Phase 5
```
app/models/M_SlotPlayer.php
app/controllers/Playerslots.php
app/controllers/Shopemployee.php
app/views/player/slots.php
app/views/player/slot_bookings.php
app/views/shopemployee/counter_booking.php
```

## To Create in Phase 6
```
app/models/M_SlotStaff.php
app/controllers/Coachslots.php
app/controllers/Trainerslots.php
app/views/coach/slots/schedule.php
app/views/coach/slots/attendance.php
app/views/trainer/slots/schedule.php
app/views/trainer/slots/attendance.php
```

## To Create in Phase 7
```
app/libraries/SlotAuditService.php
  — Refactor inline slot_audit_log INSERTs from Phase 3-6 to use SlotAuditService::log()
  — Add SlotAuditService::notify() calls for all events
  — Add activitylog writes to all action paths
```

## To Create in Phase 8
```
app/views/admin/slots/reports.php
app/views/admin/slots/changelog.php
  — Methods added to M_SlotAdmin.php:
     getFacilityUtilisation()
     getRevenueReport()
     getStaffWorkload()
     getAuditLog()
  — Methods added to Adminslots.php:
     reports()
     changelog()
```
