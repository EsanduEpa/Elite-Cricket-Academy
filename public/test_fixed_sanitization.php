<?php
// Test the fixed form submission logic
$_POST = [
    'trainer_id' => '10',
    'workoutname' => 'Fixed Test Workout',
    'frequency' => 'Daily',
    'duration' => '30',
    'videolink' => 'https://youtube.com/test',
    'intensity' => 'High',
    'notsuitablefor' => 'People with injuries',
    'benefits' => 'Builds strength'
];

echo "=== Testing Fixed Sanitization ===\n\n";
echo "Original POST Data:\n";
print_r($_POST);
echo "\n";

// Use the NEW sanitization method (like the fixed controller)
$data = [
    'trainer_id' => 10,
    'workoutname' => isset($_POST['workoutname']) ? trim(htmlspecialchars($_POST['workoutname'], ENT_QUOTES, 'UTF-8')) : '',
    'frequency' => isset($_POST['frequency']) ? htmlspecialchars($_POST['frequency'], ENT_QUOTES, 'UTF-8') : '',
    'duration' => isset($_POST['duration']) ? (int)$_POST['duration'] : 0,
    'videolink' => !empty($_POST['videolink']) ? trim(htmlspecialchars($_POST['videolink'], ENT_QUOTES, 'UTF-8')) : null,
    'intensity' => !empty($_POST['intensity']) ? htmlspecialchars($_POST['intensity'], ENT_QUOTES, 'UTF-8') : 'Moderate',
    'notsuitablefor' => !empty($_POST['notsuitablefor']) ? trim(htmlspecialchars($_POST['notsuitablefor'], ENT_QUOTES, 'UTF-8')) : null,
    'benefits' => !empty($_POST['benefits']) ? trim(htmlspecialchars($_POST['benefits'], ENT_QUOTES, 'UTF-8')) : null
];

echo "Sanitized Data Array:\n";
print_r($data);
echo "\n";

// Check validation
if (empty($data['workoutname']) || empty($data['frequency']) || empty($data['duration'])) {
    echo "✗ Validation FAILED\n";
} else {
    echo "✓ Validation PASSED\n\n";
    
    // Test actual insert
    require_once '../app/bootloader.php';
    require_once '../app/models/M_Trainer.php';
    
    $trainerModel = new M_Trainer();
    
    try {
        $result = $trainerModel->addWorkoutPlan($data);
        if ($result) {
            echo "✓ SUCCESS! Workout plan added\n";
            
            // Retrieve and display
            $plans = $trainerModel->getWorkoutPlans(10);
            foreach ($plans as $plan) {
                if ($plan->workoutname === 'Fixed Test Workout') {
                    echo "\nAdded Plan Details:\n";
                    echo "  ID: {$plan->PlanID}\n";
                    echo "  Name: {$plan->workoutname}\n";
                    echo "  Frequency: {$plan->frequency}\n";
                    echo "  Duration: {$plan->Duration} days\n";
                    echo "  VideoLink: {$plan->VideoLink}\n";
                    echo "  Intensity: {$plan->Intensity}\n";
                    echo "  NotSuitableFor: {$plan->NotSuitableFor}\n";
                    echo "  Benefits: {$plan->Benefits}\n";
                    
                    // Clean up
                    $trainerModel->deleteWorkoutPlan($plan->PlanID, 10);
                    echo "\n✓ Test data cleaned up\n";
                    break;
                }
            }
        } else {
            echo "✗ FAILED\n";
        }
    } catch (Exception $e) {
        echo "✗ ERROR: {$e->getMessage()}\n";
    }
}
?>
