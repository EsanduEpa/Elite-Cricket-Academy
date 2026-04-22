<?php
/**
 * Test script to verify nutrition plan update works with the fix
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/config.php';
require 'config/database.php';
require 'app/models/M_NutritionPlan.php';

echo "=== Testing Nutrition Plan Update Fix ===\n\n";

$model = new M_NutritionPlan();

// Get a nutrition plan to test
$db = new Database();
$db->query('SELECT PlanID, TrainerID, PlayerID, TemplateID FROM NutritionPlan ORDER BY PlanID LIMIT 1');
$testPlan = $db->single();

if (!$testPlan) {
    echo "ERROR: No nutrition plans found in database to test\n";
    exit(1);
}

echo "Test Plan ID: " . $testPlan->PlanID . "\n";
echo "Current TemplateID: " . ($testPlan->TemplateID ?? 'NULL') . "\n";
echo "Current PlayerID: " . ($testPlan->PlayerID ?? 'NULL') . "\n\n";

// Prepare update data (simulating form submission)
$updateData = [
    'plan_id' => (int)$testPlan->PlanID,
    'player_id' => (int)($testPlan->PlayerID ?? 1),
    'player_ids' => [(int)($testPlan->PlayerID ?? 1)],  // Include player IDs
    'plan_name' => 'Updated Test Plan',
    'template_id' => 1,  // Use first template
    'protein_percentage' => '45.00',
    'carbohydrate_percentage' => '35.00',
    'fat_percentage' => '20.00',
    'recommended_calories' => 2400,
    'description' => 'Updated test description',
    'diet_details' => 'Updated diet details',
    'duration' => 30,
    'status' => 'active',
    'created_date' => date('Y-m-d'),
    'notes' => 'Test notes'
];

echo "Updating plan with data:\n";
echo "  template_id: " . $updateData['template_id'] . "\n";
echo "  plan_name: " . $updateData['plan_name'] . "\n";
echo "  protein_percentage: " . $updateData['protein_percentage'] . "\n";
echo "  carbohydrate_percentage: " . $updateData['carbohydrate_percentage'] . "\n";
echo "  fat_percentage: " . $updateData['fat_percentage'] . "\n";
echo "  recommended_calories: " . $updateData['recommended_calories'] . "\n";
echo "  player_ids: " . implode(', ', $updateData['player_ids']) . "\n\n";

$result = $model->updatePlan($updateData);
echo "Update Result: " . ($result ? "✓ SUCCESS" : "✗ FAILED") . "\n\n";

if ($result) {
    // Verify the update
    echo "Verifying updated data in database:\n";
    $db->query('SELECT 
        PlanID,
        TemplateID, 
        PlanName, 
        nutritionPlanName,
        ProteinPercentage, 
        CarbohydratePercentage, 
        FatPercentage, 
        RecommendedCalories,
        Description,
        DietDetails,
        Duration,
        Status
        FROM NutritionPlan 
        WHERE PlanID = :plan_id');
    $db->bind(':plan_id', $testPlan->PlanID);
    $updated = $db->single();
    
    if ($updated) {
        $checks = [
            'TemplateID' => [1, $updated->TemplateID ?? null],
            'ProteinPercentage' => ['45.00', $updated->ProteinPercentage ?? null],
            'CarbohydratePercentage' => ['35.00', $updated->CarbohydratePercentage ?? null],
            'FatPercentage' => ['20.00', $updated->FatPercentage ?? null],
            'RecommendedCalories' => [2400, $updated->RecommendedCalories ?? null],
        ];
        
        $allCorrect = true;
        foreach ($checks as $field => $values) {
            [$expected, $actual] = $values;
            $match = (string)$expected === (string)$actual;
            echo "  $field: " . ($match ? "✓" : "✗") . " (Expected: $expected, Got: $actual)\n";
            if (!$match) $allCorrect = false;
        }
        
        echo "\nOverall Result: " . ($allCorrect ? "✓ ALL CHECKS PASSED" : "✗ SOME CHECKS FAILED") . "\n";
    } else {
        echo "  ✗ Could not retrieve updated plan\n";
    }
    
    // Check player assignments
    if ($model->tableExists('nutritionplan_player')) {
        echo "\nChecking player assignments:\n";
        $db->query('SELECT COUNT(*) as cnt FROM nutritionplan_player WHERE PlanID = :plan_id');
        $db->bind(':plan_id', $testPlan->PlanID);
        $result = $db->single();
        echo "  Assigned players: " . ($result->cnt ?? 0) . "\n";
    }
} else {
    echo "Update failed - check database for errors\n";
}

echo "\n=== Test Complete ===\n";
?>
