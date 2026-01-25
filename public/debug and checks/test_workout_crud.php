<?php
// Test complete CRUD operations for workout plan
require_once '../app/bootloader.php';
require_once '../app/models/M_Trainer.php';

echo "=== Testing M_Trainer CRUD Operations ===\n\n";

$trainerModel = new M_Trainer();

// Test 1: CREATE (Add)
echo "TEST 1: ADD Workout Plan\n";
$testData = [
    'trainer_id' => 10,
    'workoutname' => 'PHP Test Workout',
    'frequency' => 'Weekly',
    'duration' => 45,
    'videolink' => 'https://youtube.com/watch?v=test',
    'intensity' => 'High',
    'notsuitablefor' => 'People with knee injuries',
    'benefits' => 'Builds strength and endurance'
];

try {
    $result = $trainerModel->addWorkoutPlan($testData);
    if ($result) {
        echo "✓ Add workout plan: SUCCESS\n";
    } else {
        echo "✗ Add workout plan: FAILED\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: READ (Get all plans)
echo "\nTEST 2: GET Workout Plans\n";
try {
    $plans = $trainerModel->getWorkoutPlans(10);
    echo "✓ Retrieved " . count($plans) . " workout plans\n";
    
    // Find our test plan
    $testPlanId = null;
    foreach ($plans as $plan) {
        if ($plan->workoutname === 'PHP Test Workout') {
            $testPlanId = $plan->PlanID;
            echo "✓ Found test plan with ID: $testPlanId\n";
            echo "  - Name: {$plan->workoutname}\n";
            echo "  - Frequency: {$plan->frequency}\n";
            echo "  - Duration: {$plan->Duration} days\n";
            echo "  - VideoLink: {$plan->VideoLink}\n";
            echo "  - Intensity: {$plan->Intensity}\n";
            echo "  - NotSuitableFor: {$plan->NotSuitableFor}\n";
            echo "  - Benefits: {$plan->Benefits}\n";
            break;
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: UPDATE
if ($testPlanId) {
    echo "\nTEST 3: UPDATE Workout Plan\n";
    $updateData = [
        'plan_id' => $testPlanId,
        'trainer_id' => 10,
        'workoutname' => 'PHP Test Workout UPDATED',
        'frequency' => 'Daily',
        'duration' => 60,
        'videolink' => 'https://youtube.com/watch?v=updated',
        'intensity' => 'Low',
        'notsuitablefor' => 'Beginners',
        'benefits' => 'Updated benefits text'
    ];
    
    try {
        $result = $trainerModel->updateWorkoutPlan($updateData);
        if ($result) {
            echo "✓ Update workout plan: SUCCESS\n";
            
            // Verify the update
            $updatedPlan = $trainerModel->getWorkoutPlanById($testPlanId);
            if ($updatedPlan) {
                echo "✓ Verified update:\n";
                echo "  - Name: {$updatedPlan->workoutname}\n";
                echo "  - Frequency: {$updatedPlan->frequency}\n";
                echo "  - Duration: {$updatedPlan->Duration} days\n";
                echo "  - Intensity: {$updatedPlan->Intensity}\n";
            }
        } else {
            echo "✗ Update workout plan: FAILED\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    
    // Test 4: DELETE
    echo "\nTEST 4: DELETE Workout Plan\n";
    try {
        $result = $trainerModel->deleteWorkoutPlan($testPlanId, 10);
        if ($result) {
            echo "✓ Delete workout plan: SUCCESS\n";
            
            // Verify deletion
            $deletedPlan = $trainerModel->getWorkoutPlanById($testPlanId);
            if (!$deletedPlan) {
                echo "✓ Verified deletion: Plan no longer exists\n";
            } else {
                echo "✗ Plan still exists after deletion\n";
            }
        } else {
            echo "✗ Delete workout plan: FAILED\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n✗ Could not find test plan for UPDATE and DELETE tests\n";
}

echo "\n=== CRUD Tests Complete ===\n";
?>
