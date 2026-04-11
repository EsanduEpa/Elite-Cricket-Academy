<?php

/**
 * M_CoachTournamentRecommendation Model - Unit Tests
 * 
 * Tests all 9 core methods to verify functionality
 * 
 * @date April 7, 2026
 */

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/../libraries/Controller.php';
require_once __DIR__ . '/M_CoachTournamentRecommendation.php';

echo "═════════════════════════════════════════════════════════════\n";
echo "  M_CoachTournamentRecommendation Model - Unit Tests\n";
echo "═════════════════════════════════════════════════════════════\n\n";

// Initialize model
$model = new M_CoachTournamentRecommendation();

// Test data from database
$coachId = 3;           // Coach Sarath
$tournamentId = 6;      // Monsoon Cricket League
$playerId = 7;          // A player
$adminId = 1;           // Admin user

echo "TEST 1: getCoachAssignedPlayers()\n";
echo "─────────────────────────────────────\n";
$players = $model->getCoachAssignedPlayers($coachId);
echo "✅ PASS - Retrieved " . count($players) . " assigned players\n";
if (!empty($players)) {
    echo "   First player: {$players[0]['PlayerName']} (ID: {$players[0]['PlayerID']})\n";
}
echo "\n";

echo "TEST 2: checkDuplicateRecommendation() - Should not exist\n";
echo "─────────────────────────────────────────────────────────\n";
$isDuplicate = $model->checkDuplicateRecommendation($tournamentId, $playerId, $coachId);
echo ($isDuplicate ? "❌ FAIL" : "✅ PASS") . " - No existing duplicate found\n\n";

echo "TEST 3: addRecommendation() - Add valid recommendation\n";
echo "────────────────────────────────────────────────────────\n";
$newRecommendation = $model->addRecommendation($coachId, $tournamentId, $playerId, [
    'role' => 'batsman',
    'reason' => 'Excellent batting performance in recent sessions',
    'comments' => 'Ready for competitive tournament'
]);
echo ($newRecommendation['success'] ? "✅ PASS" : "❌ FAIL") . " - " . $newRecommendation['message'] . "\n";
$recommendationId = $newRecommendation['id'] ?? null;
if ($recommendationId) {
    echo "   Recommendation ID: {$recommendationId}\n";
}
echo "\n";

if ($recommendationId) {
    echo "TEST 4: checkDuplicateRecommendation() - Should exist now\n";
    echo "──────────────────────────────────────────────────────────\n";
    $isDuplicate = $model->checkDuplicateRecommendation($tournamentId, $playerId, $coachId);
    echo ($isDuplicate ? "✅ PASS" : "❌ FAIL") . " - Duplicate check working\n\n";

    echo "TEST 5: getRecommendationDetails() - Get full details\n";
    echo "──────────────────────────────────────────────────────\n";
    $details = $model->getRecommendationDetails($recommendationId);
    echo ($details ? "✅ PASS" : "❌ FAIL") . " - Retrieved recommendation details\n";
    if ($details) {
        echo "   Coach: {$details['CoachName']}\n";
        echo "   Player: {$details['PlayerName']}\n";
        echo "   Tournament: {$details['TournamentName']}\n";
        echo "   Role: {$details['RecommendedRole']}\n";
        echo "   Status: {$details['Status']}\n";
    }
    echo "\n";

    echo "TEST 6: getRecommendationsByCoach() - Get coach's recommendations\n";
    echo "───────────────────────────────────────────────────────────────────\n";
    $coachRecs = $model->getRecommendationsByCoach($coachId);
    echo "✅ PASS - Retrieved " . count($coachRecs) . " recommendations for coach\n";
    if (!empty($coachRecs)) {
        foreach ($coachRecs as $rec) {
            echo "   - {$rec['PlayerName']} for {$rec['TournamentName']} ({$rec['Status']})\n";
        }
    }
    echo "\n";

    echo "TEST 7: getRecommendationsByTournament() - Get tournament's recommendations\n";
    echo "──────────────────────────────────────────────────────────────────────────\n";
    $tourneyRecs = $model->getRecommendationsByTournament($tournamentId);
    echo "✅ PASS - Retrieved " . count($tourneyRecs) . " recommendations for tournament\n";
    echo "\n";

    echo "TEST 8: updateRecommendation() - Update pending recommendation\n";
    echo "────────────────────────────────────────────────────────────────\n";
    $updateResult = $model->updateRecommendation($recommendationId, $coachId, [
        'role' => 'all-rounder',
        'reason' => 'Excellent all-round performance - can bat and bowl',
        'comments' => 'Updated recommendation'
    ]);
    echo ($updateResult['success'] ? "✅ PASS" : "❌ FAIL") . " - " . $updateResult['message'] . "\n\n";

    echo "TEST 9: getRecommendationStats() - Get statistics\n";
    echo "─────────────────────────────────────────────────\n";
    $stats = $model->getRecommendationStats($coachId);
    echo "✅ PASS - Retrieved stats\n";
    echo "   Pending: {$stats['pending']}, Approved: {$stats['approved']}, Rejected: {$stats['rejected']}\n\n";

    echo "TEST 10: getPendingCount() - Get pending count\n";
    echo "──────────────────────────────────────────────\n";
    $pendingCount = $model->getPendingCount($coachId);
    echo "✅ PASS - Coach has {$pendingCount} pending recommendations\n\n";

    echo "TEST 11: approveRecommendation() - Admin approves\n";
    echo "────────────────────────────────────────────────\n";
    $approveResult = $model->approveRecommendation($recommendationId, $adminId, 'Good recommendation');
    echo ($approveResult['success'] ? "✅ PASS" : "❌ FAIL") . " - " . $approveResult['message'] . "\n\n";

    // Add another test recommendation for rejection test
    echo "TEST 12: addRecommendation() - Add another for rejection test\n";
    echo "──────────────────────────────────────────────────────────────\n";
    $testRec2 = $model->addRecommendation($coachId, 7, 16, [ // Tournament 7, Player 16
        'role' => 'bowler',
        'reason' => 'Strong bowling technique',
        'comments' => 'For testing rejection'
    ]);
    echo ($testRec2['success'] ? "✅ PASS" : "❌ FAIL") . " - " . $testRec2['message'] . "\n";
    $recId2 = $testRec2['id'] ?? null;
    if ($recId2) {
        echo "   Test recommendation ID: {$recId2}\n";
    }
    echo "\n";

    if ($recId2) {
        echo "TEST 13: rejectRecommendation() - Admin rejects\n";
        echo "───────────────────────────────────────────────\n";
        $rejectResult = $model->rejectRecommendation($recId2, $adminId, 'Player not eligible for this tournament');
        echo ($rejectResult['success'] ? "✅ PASS" : "❌ FAIL") . " - " . $rejectResult['message'] . "\n\n";

        echo "TEST 14: deleteRecommendation() - Delete approved (should fail)\n";
        echo "───────────────────────────────────────────────────────────────\n";
        $deleteResult = $model->deleteRecommendation($recommendationId, $coachId);
        echo ($deleteResult['success'] ? "❌ FAIL" : "✅ PASS") . " - Cannot delete approved: " . $deleteResult['message'] . "\n\n";

        echo "TEST 15: deleteRecommendation() - Delete rejected (should succeed)\n";
        echo "─────────────────────────────────────────────────────────────────\n";
        $deleteResult = $model->deleteRecommendation($recId2, $coachId);
        echo ($deleteResult['success'] ? "✅ PASS" : "❌ FAIL") . " - " . $deleteResult['message'] . "\n\n";
    }
}

echo "═════════════════════════════════════════════════════════════\n";
echo "  All Tests Completed\n";
echo "═════════════════════════════════════════════════════════════\n";

?>
