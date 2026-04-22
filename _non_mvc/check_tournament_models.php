<?php
// Quick model smoke test — DELETE this file after testing
define('APPROOT', dirname(dirname(dirname(__FILE__))) . '/app');
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cricket_academy');

require_once APPROOT . '/libraries/Database.php';
require_once APPROOT . '/models/M_Tournament.php';
require_once APPROOT . '/models/M_TournamentJoinRequest.php';
require_once APPROOT . '/models/M_TrainerTournamentRecommendation.php';
require_once APPROOT . '/models/M_TournamentResult.php';

$results = [];

// Test 1: createTournament
$m = new M_Tournament();
$id = $m->createTournament([
    'name' => 'Smoke Test Tournament ' . date('His'),
    'age_group' => 'Under 16',
    'format' => 'T20',
    'tdate' => '2026-12-01',
    'registration_deadline' => '2026-11-01',
    'max_players' => 15,
    'location' => 'Colombo',
    'prize_pool' => 10000,
    'created_by' => 1,
]);
$results[] = ['test' => 'createTournament', 'pass' => $id > 0, 'detail' => "New TournamentID: $id"];

// Test 2: getTournamentById
$t = $m->getTournamentById($id);
$results[] = ['test' => 'getTournamentById', 'pass' => $t && $t->Status === 'created' && $t->IsTeamAnnounced == 0, 'detail' => "Status={$t->Status}, IsTeamAnnounced={$t->IsTeamAnnounced}"];

// Test 3: updateStatus
$m->updateStatus($id, 'registration_open');
$t2 = $m->getTournamentById($id);
$results[] = ['test' => 'updateStatus → registration_open', 'pass' => $t2->Status === 'registration_open', 'detail' => "Status={$t2->Status}"];

// Test 4: join request duplicate guard
$jr = new M_TournamentJoinRequest();
$jr->createRequest($id, 6, 'First request');
$blocked = false;
try {
    $jr->createRequest($id, 6, 'Duplicate');
} catch (Exception $e) {
    $blocked = true;
}
if (!$blocked) $blocked = $jr->hasExistingRequest($id, 6);
$results[] = ['test' => 'JoinRequest duplicate blocked', 'pass' => $blocked, 'detail' => 'Duplicate correctly blocked'];

// Test 5: rejectAllPending cascade
$jr->createRequest($id, 7, 'Second player');
$jr->rejectAllPending($id, 1);
$count = $jr->getCountByTournament($id, 'pending');
$results[] = ['test' => 'rejectAllPending', 'pass' => $count === 0, 'detail' => "Pending requests remaining: $count"];

// Test 6: trainer recommendation + duplicate guard
$ttr = new M_TrainerTournamentRecommendation();
$r1 = $ttr->addRecommendation(4, $id, 6, ['role' => 'Batsman', 'reason' => 'Fast runner']);
$r2 = $ttr->addRecommendation(4, $id, 6, ['role' => 'Batsman', 'reason' => 'Again']);
$results[] = ['test' => 'TrainerRec: first ok, duplicate blocked', 'pass' => $r1['success'] === true && $r2['success'] === false, 'detail' => "R1={$r1['success']}, R2={$r2['success']}: {$r2['message']}"];

// Test 7: addPlayerToTeam + isInTeam
$m->addPlayerToTeam(['tournament_id' => $id, 'player_id' => 6, 'role' => 'Batsman', 'selected_by' => 9]);
$in  = $m->isInTeam($id, 6);
$out = $m->isInTeam($id, 9999);
$results[] = ['test' => 'addPlayerToTeam + isInTeam', 'pass' => $in && !$out, 'detail' => "PlayerID 6 in team: " . ($in ? 'yes' : 'no') . ", PlayerID 9999: " . ($out ? 'yes' : 'no')];

// Test 8: saveResult upsert
$m->updateStatus($id, 'ongoing');
$res = new M_TournamentResult();
$res->saveResult(['tournament_id' => $id, 'position' => '1st', 'opponent_in_final' => 'Kandy', 'won_by' => '8 wickets', 'entered_by' => 1]);
$res->saveResult(['tournament_id' => $id, 'position' => '2nd', 'entered_by' => 1]); // upsert
$row = $res->getResult($id);
$results[] = ['test' => 'saveResult upsert', 'pass' => $row && $row->Position === '2nd', 'detail' => "Position={$row->Position}"];

// Test 9: announceTeam
$m->announceTeam($id);
$t3 = $m->getTournamentById($id);
$results[] = ['test' => 'announceTeam', 'pass' => $t3->IsTeamAnnounced == 1 && $t3->Status === 'team_announced', 'detail' => "IsTeamAnnounced={$t3->IsTeamAnnounced}, Status={$t3->Status}"];

// Cleanup
$db = new Database();
$db->query('DELETE FROM tournament_result WHERE TournamentID=:id'); $db->bind(':id', $id); $db->execute();
$db->query('DELETE FROM tournamentplayer WHERE TournamentID=:id'); $db->bind(':id', $id); $db->execute();
$db->query('DELETE FROM trainer_tournament_recommendations WHERE TournamentID=:id'); $db->bind(':id', $id); $db->execute();
$db->query('DELETE FROM tournament_join_request WHERE TournamentID=:id'); $db->bind(':id', $id); $db->execute();
$db->query('DELETE FROM tournament WHERE TournamentID=:id'); $db->bind(':id', $id); $db->execute();

// Output
$pass = array_filter($results, fn($r) => $r['pass']);
$fail = array_filter($results, fn($r) => !$r['pass']);
echo "<h2>Tournament Model Tests: " . count($pass) . "/" . count($results) . " passed</h2>";
echo "<style>table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:6px 10px}
.pass{background:#d4edda}.fail{background:#f8d7da}</style>";
echo "<table><tr><th>#</th><th>Test</th><th>Result</th><th>Detail</th></tr>";
foreach ($results as $i => $r) {
    $cls = $r['pass'] ? 'pass' : 'fail';
    $icon = $r['pass'] ? '✅' : '❌';
    echo "<tr class='$cls'><td>" . ($i+1) . "</td><td>{$r['test']}</td><td>$icon</td><td>{$r['detail']}</td></tr>";
}
echo "</table>";
if ($fail) { echo "<p style='color:red'>⚠️ " . count($fail) . " test(s) failed.</p>"; }
else { echo "<p style='color:green'>All tests passed. Safe to delete this file.</p>"; }
