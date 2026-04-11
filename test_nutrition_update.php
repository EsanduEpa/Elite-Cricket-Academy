<?php
// Quick test to verify nutrition plan update is working
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/config.php';
require 'config/database.php';

echo "=== Testing Nutrition Plan Update ===\n\n";

// 1. Check if columns exist
echo "1. Checking NutritionPlan table structure...\n";
$db = new Database();
$db->query('SHOW COLUMNS FROM NutritionPlan');
$columns = $db->resultSet();
$columnNames = array_map(fn($col) => $col->Field, $columns);

echo "Columns found: " . count($columnNames) . "\n";
$requiredCols = ['TemplateID', 'ProteinPercentage', 'CarbohydratePercentage', 'FatPercentage', 'RecommendedCalories', 'Description'];
foreach ($requiredCols as $col) {
    $found = in_array($col, $columnNames);
    echo "  - $col: " . ($found ? "✓" : "✗ MISSING") . "\n";
}

// 2. Check nutrition_plan_templates table
echo "\n2. Checking nutrition_plan_templates table...\n";
$db->query('SHOW TABLES LIKE "nutrition_plan_templates"');
$table = $db->single();
echo "  Table exists: " . ($table ? "✓" : "✗ MISSING") . "\n";

if ($table) {
    $db->query('SELECT COUNT(*) as cnt FROM nutrition_plan_templates');
    $result = $db->single();
    echo "  Templates in DB: " . ($result->cnt ?? 0) . "\n";
}

// 3. Get a sample nutrition plan
echo "\n3. Checking existing nutrition plans...\n";
$db->query('SELECT PlanID, TrainerID, TemplateID, PlanName, nutritionPlanName, ProteinPercentage, CarbohydratePercentage, FatPercentage, RecommendedCalories FROM NutritionPlan LIMIT 1');
$plan = $db->single();
if ($plan) {
    echo "Sample plan found:\n";
    echo "  PlanID: " . ($plan->PlanID ?? 'NULL') . "\n";
    echo "  TemplateID: " . ($plan->TemplateID ?? 'NULL') . "\n";
    echo "  PlanName/nutritionPlanName: " . ($plan->PlanName ?? $plan->nutritionPlanName ?? 'NULL') . "\n";
    echo "  ProteinPercentage: " . ($plan->ProteinPercentage ?? 'NULL') . "\n";
    echo "  CarbohydratePercentage: " . ($plan->CarbohydratePercentage ?? 'NULL') . "\n";
    echo "  FatPercentage: " . ($plan->FatPercentage ?? 'NULL') . "\n";
    echo "  RecommendedCalories: " . ($plan->RecommendedCalories ?? 'NULL') . "\n";
} else {
    echo "  No nutrition plans found in database\n";
}

// 4. Test the model's updatePlan method
echo "\n4. Testing M_NutritionPlan::updatePlan()...\n";
require 'app/models/M_NutritionPlan.php';
$model = new M_NutritionPlan();

// Try to get a plan and test update
$db->query('SELECT PlanID, TrainerID FROM NutritionPlan LIMIT 1');
$testPlan = $db->single();

if ($testPlan) {
    echo "  Plan ID to test: " . $testPlan->PlanID . "\n";
    
    $testData = [
        'plan_id' => $testPlan->PlanID,
        'player_id' => 1,
        'plan_name' => 'Test Updated Plan',
        'template_id' => 1,
        'protein_percentage' => '45.00',
        'carbohydrate_percentage' => '35.00',
        'fat_percentage' => '20.00',
        'recommended_calories' => 2400,
        'description' => 'Test description from update',
        'diet_details' => 'Test diet details',
        'duration' => 30,
        'status' => 'active',
        'created_date' => date('Y-m-d'),
        'notes' => 'Test notes'
    ];
    
    echo "\n  Attempting update with data:\n";
    foreach ($testData as $k => $v) {
        echo "    $k: $v\n";
    }
    
    $result = $model->updatePlan($testData);
    echo "\n  Update result: " . ($result ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    
    if ($result) {
        // Verify the update
        $db->query('SELECT TemplateID, ProteinPercentage, CarbohydratePercentage, FatPercentage, RecommendedCalories, Description FROM NutritionPlan WHERE PlanID = :plan_id');
        $db->bind(':plan_id', $testPlan->PlanID);
        $updated = $db->single();
        
        echo "\n  Verification:\n";
        echo "    TemplateID: " . ($updated->TemplateID ?? 'NULL') . " (expected: 1)\n";
        echo "    ProteinPercentage: " . ($updated->ProteinPercentage ?? 'NULL') . " (expected: 45.00)\n";
        echo "    CarbohydratePercentage: " . ($updated->CarbohydratePercentage ?? 'NULL') . " (expected: 35.00)\n";
        echo "    FatPercentage: " . ($updated->FatPercentage ?? 'NULL') . " (expected: 20.00)\n";
        echo "    RecommendedCalories: " . ($updated->RecommendedCalories ?? 'NULL') . " (expected: 2400)\n";
        echo "    Description: " . ($updated->Description ?? 'NULL') . "\n";
    }
} else {
    echo "  No test plan available\n";
}

echo "\n=== Test Complete ===\n";
?>
