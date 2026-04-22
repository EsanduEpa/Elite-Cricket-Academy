<?php
/**
 * Tournament Module — Scenario-Based Integration Tests
 *
 * 4 scenarios covering the full tournament lifecycle:
 *   1. Player submits a join request
 *   2. Trainer recommends a player
 *   3. Admin approves a join request
 *   4. Head coach finalises the squad
 *
 * Run from browser: http://localhost/Elite/test_tournament_scenarios.php
 * Run from CLI:     php test_tournament_scenarios.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ── Bootstrap ────────────────────────────────────────────────────────────────
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/libraries/Database.php';
require_once __DIR__ . '/app/models/M_Tournament.php';
require_once __DIR__ . '/app/models/M_TournamentJoinRequest.php';
require_once __DIR__ . '/app/models/M_TrainerTournamentRecommendation.php';

// ── Test fixtures (real rows in cricket_academy DB) ──────────────────────────
define('TOURNAMENT_ID', 6);   // Monsoon Cricket League — Status: created
define('PLAYER_ID',     6);   // esandu001  (UserID = PlayerID)
define('TRAINER_ID',    4);   // trainer001 (UserID)
define('COACH_ID',      3);   // coach001   (head coach, UserID)
define('ADMIN_ID',      1);   // admin

// ── Helpers ──────────────────────────────────────────────────────────────────
$passed = 0;
$failed = 0;

function pass(string $msg): void {
    global $passed;
    $passed++;
    echo "    ✅ PASS  $msg\n";
}

function fail(string $msg, string $detail = ''): void {
    global $failed;
    $failed++;
    echo "    ❌ FAIL  $msg" . ($detail ? " — $detail" : '') . "\n";
}

function assert_true($expr, string $label, string $detail = ''): void {
    $expr ? pass($label) : fail($label, $detail);
}

function assert_false($expr, string $label, string $detail = ''): void {
    (!$expr) ? pass($label) : fail($label, $detail);
}

function assert_equals($actual, $expected, string $label): void {
    $actual == $expected
        ? pass("$label (got: $actual)")
        : fail("$label", "expected '$expected', got '$actual'");
}

function section(string $title): void {
    $line = str_repeat('─', 60);
    echo "\n$line\n  $title\n$line\n";
}

function teardown_join_request(int $tid, int $pid): void {
    $db = new Database();
    $db->query('DELETE FROM tournament_join_request WHERE TournamentID = :t AND PlayerID = :p');
    $db->bind(':t', $tid);
    $db->bind(':p', $pid);
    $db->execute();
}

function teardown_trainer_rec(int $tid, int $pid, int $trainer): void {
    $db = new Database();
    $db->query(
        'DELETE FROM trainer_tournament_recommendations
         WHERE TournamentID = :t AND PlayerID = :p AND TrainerID = :tr'
    );
    $db->bind(':t',  $tid);
    $db->bind(':p',  $pid);
    $db->bind(':tr', $trainer);
    $db->execute();
}

function teardown_team_member(int $tid, int $pid): void {
    $db = new Database();
    $db->query('DELETE FROM tournamentplayer WHERE TournamentID = :t AND PlayerID = :p');
    $db->bind(':t', $tid);
    $db->bind(':p', $pid);
    $db->execute();
}

// ── Print header ─────────────────────────────────────────────────────────────
echo "═══════════════════════════════════════════════════════════════\n";
echo "   Tournament Module — Scenario Integration Tests\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "   Tournament : #" . TOURNAMENT_ID . " (Monsoon Cricket League)\n";
echo "   Player     : UserID=" . PLAYER_ID . "  (esandu001)\n";
echo "   Trainer    : UserID=" . TRAINER_ID . "  (trainer001)\n";
echo "   Head Coach : UserID=" . COACH_ID   . "  (coach001)\n";
echo "   Admin      : UserID=" . ADMIN_ID   . "\n";

// Ensure clean state before starting
teardown_join_request(TOURNAMENT_ID, PLAYER_ID);
teardown_trainer_rec(TOURNAMENT_ID, PLAYER_ID, TRAINER_ID);
teardown_team_member(TOURNAMENT_ID, PLAYER_ID);

// ════════════════════════════════════════════════════════════════════════════
// SCENARIO 1 — Player submits a join request
// ════════════════════════════════════════════════════════════════════════════
section('SCENARIO 1 — Player submits a join request');

$M_Tournament  = new M_Tournament();
$M_JoinRequest = new M_TournamentJoinRequest();

// S1-T1: Tournament must exist and be queryable
$tournament = $M_Tournament->getTournamentById(TOURNAMENT_ID);
assert_true($tournament !== false && !empty($tournament->TournamentID),
    'Tournament #' . TOURNAMENT_ID . ' exists in DB');

// S1-T2: No existing request before submission
$existsBefore = $M_JoinRequest->hasExistingRequest(TOURNAMENT_ID, PLAYER_ID);
assert_false($existsBefore, 'No prior join request exists for this player');

// S1-T3: Submit the join request
$requestId = $M_JoinRequest->createRequest(TOURNAMENT_ID, PLAYER_ID, 'I am ready for this tournament.');
assert_true($requestId > 0, 'createRequest() returns a valid new RequestID', "got: $requestId");

// S1-T4: Duplicate guard — second submission must be blocked
$existsAfter = $M_JoinRequest->hasExistingRequest(TOURNAMENT_ID, PLAYER_ID);
assert_true($existsAfter, 'hasExistingRequest() detects the submitted request');

// S1-T5: getRequestByPlayer returns the correct record
$req = $M_JoinRequest->getRequestByPlayer(TOURNAMENT_ID, PLAYER_ID);
assert_true($req !== false, 'getRequestByPlayer() finds the request');
assert_equals($req->Status  ?? '', 'pending',               'Initial status is "pending"');
assert_equals((int)($req->PlayerID ?? 0), PLAYER_ID,        'PlayerID matches');
assert_equals((int)($req->TournamentID ?? 0), TOURNAMENT_ID,'TournamentID matches');

echo "\n  → Scenario 1 complete. Request #$requestId is pending.\n";

// ════════════════════════════════════════════════════════════════════════════
// SCENARIO 2 — Trainer recommends the player for the tournament
// ════════════════════════════════════════════════════════════════════════════
section('SCENARIO 2 — Trainer recommends a player');

$M_TTR = new M_TrainerTournamentRecommendation();

// S2-T1: getAllActivePlayers returns at least one player
$players = $M_TTR->getAllActivePlayers();
assert_true(count($players) > 0, 'getAllActivePlayers() returns at least 1 player');

// S2-T2: No duplicate exists yet
$dupBefore = $M_TTR->checkDuplicate(TOURNAMENT_ID, PLAYER_ID, TRAINER_ID);
assert_false($dupBefore, 'No existing recommendation before submission');

// S2-T3: Submit recommendation
$result = $M_TTR->addRecommendation(TRAINER_ID, TOURNAMENT_ID, PLAYER_ID, [
    'role'     => 'Batsman',
    'reason'   => 'Consistent top scorer in net sessions',
    'comments' => 'Excellent technique against pace',
]);
assert_true($result['success'], 'addRecommendation() succeeds', $result['message'] ?? '');

// S2-T4: Duplicate is now detected
$dupAfter = $M_TTR->checkDuplicate(TOURNAMENT_ID, PLAYER_ID, TRAINER_ID);
assert_true($dupAfter, 'checkDuplicate() blocks a second recommendation for the same player');

// S2-T5: Second call returns an error message, not success
$duplicate = $M_TTR->addRecommendation(TRAINER_ID, TOURNAMENT_ID, PLAYER_ID, [
    'role' => 'Bowler', 'reason' => 'trying again',
]);
assert_false($duplicate['success'], 'Duplicate addRecommendation() returns success=false');

// S2-T6: getRecommendationsByTrainer lists the new rec
$trainerRecs = $M_TTR->getRecommendationsByTrainer(TRAINER_ID);
$found = false;
foreach ($trainerRecs as $r) {
    if ((int)$r->TournamentID === TOURNAMENT_ID && (int)$r->PlayerID === PLAYER_ID) {
        $found = true;
        break;
    }
}
assert_true($found, 'getRecommendationsByTrainer() includes the submitted recommendation');

echo "\n  → Scenario 2 complete. Player recommended as Batsman.\n";

// ════════════════════════════════════════════════════════════════════════════
// SCENARIO 3 — Admin approves the join request
// ════════════════════════════════════════════════════════════════════════════
section('SCENARIO 3 — Admin approves the join request');

// S3-T1: getRequestsByTournament lists the player's request
$requests = $M_JoinRequest->getRequestsByTournament(TOURNAMENT_ID);
assert_true(count($requests) > 0, 'getRequestsByTournament() returns at least 1 request');

$targetReq = null;
foreach ($requests as $r) {
    if ((int)$r->PlayerID === PLAYER_ID) {
        $targetReq = $r;
        break;
    }
}
assert_true($targetReq !== null, 'Player\'s request appears in tournament request list');

// S3-T2: Approve the request
if ($targetReq) {
    $updated = $M_JoinRequest->updateStatus(
        $targetReq->RequestID,
        'approved',
        ADMIN_ID,
        'Player meets age and skill requirements.'
    );
    assert_true($updated, 'updateStatus() executes without error');

    // S3-T3: Verify new status persisted
    $reqAfter = $M_JoinRequest->getRequestByPlayer(TOURNAMENT_ID, PLAYER_ID);
    assert_equals($reqAfter->Status ?? '', 'approved', 'Status persisted as "approved" in DB');

    // S3-T4: ReviewNotes are stored
    assert_equals(
        $reqAfter->ReviewNotes ?? '',
        'Player meets age and skill requirements.',
        'ReviewNotes persisted correctly'
    );
} else {
    fail('updateStatus() — skipped (request not found in list)');
    fail('Status persisted as "approved" — skipped');
    fail('ReviewNotes persisted — skipped');
}

echo "\n  → Scenario 3 complete. Join request approved by admin.\n";

// ════════════════════════════════════════════════════════════════════════════
// SCENARIO 4 — Head coach finalises the squad
// ════════════════════════════════════════════════════════════════════════════
section('SCENARIO 4 — Head coach finalises the squad');

// S4-T1: addPlayerToTeam — add player to draft
$addResult = $M_Tournament->addPlayerToTeam([
    'tournament_id' => TOURNAMENT_ID,
    'player_id'     => PLAYER_ID,
    'team'          => 'Academy Team',
    'role'          => 'Batsman',
    'selected_by'   => COACH_ID,
]);
assert_true($addResult, 'addPlayerToTeam() inserts player into draft squad');

// S4-T2: isInTeam confirms membership
$inTeam = $M_Tournament->isInTeam(TOURNAMENT_ID, PLAYER_ID);
assert_true($inTeam, 'isInTeam() returns true after adding player');

// S4-T3: getTeam lists the added player
$team = $M_Tournament->getTeam(TOURNAMENT_ID);
$teamMember = null;
foreach ($team as $m) {
    if ((int)$m->PlayerID === PLAYER_ID) {
        $teamMember = $m;
        break;
    }
}
assert_true($teamMember !== null, 'getTeam() includes the newly added player');
assert_equals($teamMember->RoleInTeam ?? '', 'Batsman', 'RoleInTeam stored correctly');

// S4-T4: confirmTeam + announceTeam sets IsTeamAnnounced = 1
$confirmed = $M_Tournament->confirmTeam(TOURNAMENT_ID, COACH_ID);
assert_true($confirmed, 'confirmTeam() executes without error');
$announced = $M_Tournament->announceTeam(TOURNAMENT_ID);
assert_true($announced, 'announceTeam() executes without error');

$refreshed = $M_Tournament->getTournamentById(TOURNAMENT_ID);
assert_equals((int)($refreshed->IsTeamAnnounced ?? 0), 1, 'IsTeamAnnounced = 1 after confirmTeam()');

// S4-T5: clearTeamDraft removes draft entries and resets IsTeamAnnounced
$cleared = $M_Tournament->clearTeamDraft(TOURNAMENT_ID);
assert_true($cleared, 'clearTeamDraft() executes without error');

$afterClear = $M_Tournament->getTournamentById(TOURNAMENT_ID);
assert_equals((int)($afterClear->IsTeamAnnounced ?? 1), 0, 'IsTeamAnnounced reset to 0 after clearTeamDraft()');

$teamAfterClear = $M_Tournament->getTeam(TOURNAMENT_ID);
$stillPresent = false;
foreach ($teamAfterClear as $m) {
    if ((int)$m->PlayerID === PLAYER_ID) { $stillPresent = true; break; }
}
assert_false($stillPresent, 'Player removed from squad after clearTeamDraft()');

echo "\n  → Scenario 4 complete. Squad finalised and draft cleared.\n";

// ── Teardown ─────────────────────────────────────────────────────────────────
teardown_join_request(TOURNAMENT_ID, PLAYER_ID);
teardown_trainer_rec(TOURNAMENT_ID, PLAYER_ID, TRAINER_ID);
teardown_team_member(TOURNAMENT_ID, PLAYER_ID);

// ── Summary ──────────────────────────────────────────────────────────────────
$total = $passed + $failed;
echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
printf("   Results: %d/%d passed", $passed, $total);
echo ($failed === 0 ? "  🎉 All tests passed!\n" : "  ⚠️  $failed test(s) failed.\n");
echo "═══════════════════════════════════════════════════════════════\n";
