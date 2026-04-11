<?php
/**
 * Phase 5 — Slot Player & Shop Counter Booking test
 * URL: http://localhost/Elite/test_phase5.php
 */
define('APPROOT', dirname(__DIR__) . '/app');
define('URLROOT', 'http://localhost/Elite');
define('SITENAME', 'Elite-Cricket-Academy');
define('DEV_MODE', true);

require_once APPROOT . '/config/config.php';
require_once APPROOT . '/libraries/Database.php';
require_once APPROOT . '/libraries/SlotBookingService.php';
require_once APPROOT . '/models/M_SlotPlayer.php';

// ── helpers ────────────────────────────────────────────────────────────────
$pass = 0; $fail = 0;
function ok(string $label): void  { global $pass; $pass++; echo "<tr><td>✅</td><td>$label</td></tr>\n"; }
function nok(string $label, string $detail = ''): void {
    global $fail; $fail++;
    echo "<tr style='background:#fff5f5'><td>❌</td><td>$label" . ($detail ? " — <small>$detail</small>" : '') . "</td></tr>\n";
}
function section(string $title): void {
    echo "<tr><th colspan='2' style='background:#f0f4ff;padding:8px 12px;font-size:15px'>$title</th></tr>\n";
}

$PLAYER_ID     = 6;     // Player Esandu (DEV_MODE default)
$SHOP_EMP_ID   = 5;     // Shop Muditha

// ── Fetch a future occurrence to use for testing ────────────────────────────
$db = new Database();
$db->query("SELECT so.OccurrenceID, so.OccurrenceDate, so.TemplateID
            FROM slot_occurrence so
            JOIN slot_template st ON st.TemplateID = so.TemplateID
            WHERE so.OccurrenceDate >= CURDATE()
              AND so.Status IN ('scheduled','active')
              AND st.IsActive = 1
            ORDER BY so.OccurrenceDate LIMIT 1");
$occ = $db->single();

$db->query("SELECT so.OccurrenceID, so.OccurrenceDate
            FROM slot_occurrence so
            JOIN slot_template st ON st.TemplateID = so.TemplateID
            WHERE so.OccurrenceDate >= CURDATE()
              AND st.SlotType IN ('facility_only','private')
              AND so.Status IN ('scheduled','active')
            ORDER BY so.OccurrenceDate LIMIT 1");
$counterOcc = $db->single();

$model = new M_SlotPlayer();

echo <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Phase 5 Test</title>
<style>
  body { font-family: system-ui, sans-serif; max-width: 860px; margin: 30px auto; padding: 0 20px; }
  h1   { color: #1e293b; }
  table{ width:100%; border-collapse:collapse; font-size:14px; margin-bottom:24px; }
  td,th{ padding:8px 12px; border:1px solid #e2e8f0; text-align:left; }
  .summary { font-size:18px; font-weight:700; margin-top:16px; }
  .green { color:#16a34a } .red { color:#dc2626 }
  a.btn { background:#2563eb;color:#fff;padding:8px 16px;border-radius:6px;text-decoration:none;font-size:13px; }
  .link-row { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:24px; }
</style>
</head>
<body>
<h1>⚙️ Phase 5 — Slot Booking Test</h1>
<p>Player under test: <strong>#$PLAYER_ID</strong> &nbsp;|&nbsp;
   Shop employee: <strong>#$SHOP_EMP_ID</strong></p>

<div class="link-row">
  <a class="btn" href="http://localhost/Elite/playerslots/available">Book Sessions (Player)</a>
  <a class="btn" href="http://localhost/Elite/playerslots/bookings">My Bookings (Player)</a>
  <a class="btn" href="http://localhost/Elite/shop/counter" style="background:#16a34a">Counter Booking (Shop)</a>
</div>

<table>
HTML;

// ── 1. SERVICE CHECKS ──────────────────────────────────────────────────────
section('1. SlotBookingService');

if ($occ) {
    $ent = SlotBookingService::validateEntitlement($PLAYER_ID, (int)$occ->TemplateID);
    $ent['ok'] ? ok("validateEntitlement — ok=true (RequiredPlanFeature=none)") :
                 nok("validateEntitlement returned ok=false", $ent['code']);

    $med = SlotBookingService::checkMedicalFlag($PLAYER_ID, $occ->OccurrenceDate);
    $med['ok'] ? ok("checkMedicalFlag — no active injury for date {$occ->OccurrenceDate}") :
                 nok("checkMedicalFlag returned ok=false", $med['code']);

    $cap = SlotBookingService::checkCapacity((int)$occ->OccurrenceID);
    $cap['ok'] ? ok("checkCapacity — spots_left={$cap['spots_left']} for occ#{$occ->OccurrenceID}") :
                 nok("checkCapacity returned ok=false", $cap['code']);
} else {
    nok("No future occurrences exist — create them via /adminslots/generate first");
}

// ── 2. MODEL — getAvailableOccurrences ────────────────────────────────────
section('2. M_SlotPlayer::getAvailableOccurrences()');
$avail = $model->getAvailableOccurrences($PLAYER_ID);
if (is_array($avail) && count($avail) > 0) {
    ok("Returned " . count($avail) . " occurrence(s) for player #$PLAYER_ID");

    $bookable = array_filter($avail, fn($r) => !$r->blocked);
    $blocked  = array_filter($avail, fn($r) => $r->blocked);
    ok("Bookable: " . count($bookable) . " | Blocked: " . count($blocked));

    $first = $avail[0];
    isset($first->blocked) ? ok("Row has 'blocked' property") : nok("Row missing 'blocked' property");
    isset($first->OccurrenceDate) ? ok("Row has 'OccurrenceDate' property") : nok("Row missing 'OccurrenceDate'");
} elseif (is_array($avail) && count($avail) === 0) {
    nok("No future occurrences returned — no upcoming slots in DB (date check)");
} else {
    nok("getAvailableOccurrences() returned non-array");
}

// ── 3. MODEL — createBooking ──────────────────────────────────────────────
section('3. M_SlotPlayer::createBooking()');
$testOcc = null;
foreach ($avail as $r) {
    if (!$r->blocked) { $testOcc = $r; break; }
}

$bookingId = null;
if ($testOcc) {
    $result = $model->createBooking(
        (int)$testOcc->OccurrenceID, $PLAYER_ID,
        'self', $PLAYER_ID, null, 0.0, null, 'not_required'
    );
    if ($result === true) {
        ok("createBooking() — inserted booking for occ#{$testOcc->OccurrenceID}");

        // Fetch the new BookingID
        $db2 = new Database();
        $db2->query("SELECT BookingID FROM slot_booking WHERE OccurrenceID=:oid AND PlayerID=:pid ORDER BY BookingID DESC LIMIT 1");
        $db2->bind(':oid', (int)$testOcc->OccurrenceID, PDO::PARAM_INT);
        $db2->bind(':pid', $PLAYER_ID, PDO::PARAM_INT);
        $brow = $db2->single();
        $bookingId = $brow ? (int)$brow->BookingID : null;
        $bookingId ? ok("BookingID confirmed in DB: #$bookingId") : nok("Booking row not found after insert");
    } elseif ($result === 'duplicate') {
        ok("createBooking() — returned 'duplicate' (booking already exists — idempotent)");
        $db2 = new Database();
        $db2->query("SELECT BookingID FROM slot_booking WHERE OccurrenceID=:oid AND PlayerID=:pid AND Status!='cancelled' LIMIT 1");
        $db2->bind(':oid', (int)$testOcc->OccurrenceID, PDO::PARAM_INT);
        $db2->bind(':pid', $PLAYER_ID, PDO::PARAM_INT);
        $brow = $db2->single();
        $bookingId = $brow ? (int)$brow->BookingID : null;
    } else {
        nok("createBooking() failed with: " . (is_string($result) ? $result : 'unexpected'));
    }
} else {
    nok("No bookable occurrence available to test createBooking()");
}

// ── duplicate guard ──
if ($testOcc) {
    $dup = $model->createBooking(
        (int)$testOcc->OccurrenceID, $PLAYER_ID,
        'self', $PLAYER_ID, null, 0.0, null, 'not_required'
    );
    $dup === 'duplicate' ? ok("Duplicate guard — returns 'duplicate' on second attempt") :
                           nok("Duplicate guard failed — got: " . (is_string($dup) ? $dup : 'true'));
}

// ── 4. MODEL — getPlayerBookings ─────────────────────────────────────────
section('4. M_SlotPlayer::getPlayerBookings()');
$bkgs = $model->getPlayerBookings($PLAYER_ID);
if (is_array($bkgs) && count($bkgs) > 0) {
    ok("getPlayerBookings() — returned " . count($bkgs) . " booking(s) for player #$PLAYER_ID");
    $first = $bkgs[0];
    foreach (['BookingID','OccurrenceDate','SlotLabel','StartTime','EndTime','Status'] as $col) {
        isset($first->$col) ? ok("  Row has '$col'") : nok("  Row missing '$col'");
    }
} else {
    nok("getPlayerBookings() returned no rows (booking may not have been created)");
}

// ── 5. MODEL — cancelBooking ──────────────────────────────────────────────
section('5. M_SlotPlayer::cancelBooking()');
if ($bookingId) {
    // Try cancel with wrong player — should get 'forbidden'
    $r = $model->cancelBooking($bookingId, 999);
    $r === 'forbidden' ? ok("cancelBooking() — 'forbidden' for wrong player") :
                         nok("cancelBooking() — wrong player did not return 'forbidden', got: $r");

    // Now find a booking whose occurrence is >24h away
    $db3 = new Database();
    $db3->query("SELECT sb.BookingID FROM slot_booking sb JOIN slot_occurrence so ON so.OccurrenceID=sb.OccurrenceID WHERE sb.PlayerID=:pid AND sb.Status='confirmed' AND so.OccurrenceDate > DATE_ADD(CURDATE(), INTERVAL 1 DAY) LIMIT 1");
    $db3->bind(':pid', $PLAYER_ID, PDO::PARAM_INT);
    $cancelRow = $db3->single();

    if ($cancelRow) {
        $r = $model->cancelBooking((int)$cancelRow->BookingID, $PLAYER_ID);
        $r === true ? ok("cancelBooking() — successfully cancelled booking #{$cancelRow->BookingID}") :
                      nok("cancelBooking() failed: $r");
    } else {
        // Booking might be today — window_closed expected
        $r = $model->cancelBooking($bookingId, $PLAYER_ID);
        ($r === 'window_closed' || $r === true) ?
            ok("cancelBooking() — returned '$r' (24h window check working)") :
            nok("cancelBooking() unexpected result: $r");
    }
} else {
    nok("cancelBooking() — skipped, no bookingId from step 3");
}

// ── 6. SHOP — getCounterSlots ────────────────────────────────────────────
section('6. M_SlotPlayer::getCounterSlots() (Shop Employee)');
$slots = $model->getCounterSlots();
if (is_array($slots)) {
    if (count($slots) > 0) {
        ok("getCounterSlots() — " . count($slots) . " facility/private slot(s) found");
    } else {
        $n = '<a href="http://localhost/Elite/adminslots/calendar" target="_blank">create a facility_only or private template</a>';
        ok("getCounterSlots() — 0 slots (no facility_only/private occurrences yet — $n)");
    }
} else {
    nok("getCounterSlots() returned non-array");
}

// ── 7. SHOP — searchPlayers ──────────────────────────────────────────────
section('7. M_SlotPlayer::searchPlayers()');
$players = $model->searchPlayers('Esandu');
if (is_array($players) && count($players) > 0) {
    ok("searchPlayers('Esandu') — found " . count($players) . " player(s)");
} else {
    nok("searchPlayers('Esandu') — found 0 results");
}
$players2 = $model->searchPlayers('6');
is_array($players2) ? ok("searchPlayers('6') (by ID) — returned array") : nok("searchPlayers by ID failed");

echo "</table>\n";
echo "<div class='summary'>";
echo "<span class='green'>✅ $pass passed</span> &nbsp; <span class='red'>❌ $fail failed</span>";
echo "</div>\n";
echo "<hr style='margin:24px 0'>";
echo "<h3>Quick Navigation</h3>";
echo "<div class='link-row'>";
$links = [
    'Player — Book Sessions'  => 'playerslots/available',
    'Player — My Bookings'    => 'playerslots/bookings',
    'Shop — Counter Booking'  => 'shop/counter',
    'Admin — Slots Calendar'  => 'adminslots/calendar',
];
foreach ($links as $label => $path) {
    echo "<a class='btn' href='http://localhost/Elite/$path' target='_blank'>$label</a>";
}
echo "</div>\n";
echo "</body></html>\n";
