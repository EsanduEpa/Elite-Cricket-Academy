<?php
// Simulate form submission to test the controller
session_start();
$_SESSION['user_id'] = 10;
$_SESSION['username'] = 'Test Trainer';
$_SESSION['user_type'] = 'trainer';

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'trainer_id' => '10',
    'workoutname' => 'Form Test Workout',
    'frequency' => 'Daily',
    'duration' => '30',
    'videolink' => '',
    'intensity' => 'Moderate',
    'notsuitablefor' => '',
    'benefits' => ''
];

echo "=== Simulating Form Submission ===\n\n";
echo "POST Data:\n";
print_r($_POST);
echo "\n";

// Load the application
define('APPROOT', dirname(__FILE__, 2) . '/app');
define('URLROOT', 'http://localhost/Elite');
define('SITENAME', 'Elite Cricket Academy');

require_once APPROOT . '/bootloader.php';
require_once APPROOT . '/models/M_Trainer.php';

echo "=== Testing Controller Logic ===\n\n";

$trainerModel = new M_Trainer();

// Sanitize POST data (same as controller)
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

echo "Sanitized POST Data:\n";
print_r($_POST);
echo "\n";

$data = [
    'trainer_id' => $_SESSION['user_id'] ?? 10,
    'workoutname' => trim($_POST['workoutname'] ?? ''),
    'frequency' => $_POST['frequency'] ?? '',
    'duration' => (int)($_POST['duration'] ?? 0),
    'videolink' => !empty($_POST['videolink']) ? trim($_POST['videolink']) : null,
    'intensity' => !empty($_POST['intensity']) ? $_POST['intensity'] : 'Moderate',
    'notsuitablefor' => !empty($_POST['notsuitablefor']) ? trim($_POST['notsuitablefor']) : null,
    'benefits' => !empty($_POST['benefits']) ? trim($_POST['benefits']) : null
];

echo "Processed Data Array:\n";
print_r($data);
echo "\n";

// Validate data
if (empty($data['workoutname']) || empty($data['frequency']) || empty($data['duration'])) {
    $missing = [];
    if (empty($data['workoutname'])) $missing[] = 'workoutname';
    if (empty($data['frequency'])) $missing[] = 'frequency';
    if (empty($data['duration'])) $missing[] = 'duration';
    echo "✗ Validation failed. Missing: " . implode(', ', $missing) . "\n";
} else if ($data['duration'] < 1 || $data['duration'] > 365) {
    echo "✗ Duration validation failed: {$data['duration']}\n";
} else {
    echo "✓ Validation passed\n\n";
    
    // Try to add workout plan
    echo "Attempting to insert...\n";
    try {
        $result = $trainerModel->addWorkoutPlan($data);
        if ($result) {
            echo "✓ SUCCESS! Workout plan added\n";
            
            // Retrieve it
            $plans = $trainerModel->getWorkoutPlans(10);
            foreach ($plans as $plan) {
                if ($plan->workoutname === 'Form Test Workout') {
                    echo "\n✓ Retrieved added plan:\n";
                    print_r($plan);
                    
                    // Clean up
                    $trainerModel->deleteWorkoutPlan($plan->PlanID, 10);
                    echo "\n✓ Test data cleaned up\n";
                    break;
                }
            }
        } else {
            echo "✗ FAILED - Model returned false\n";
        }
    } catch (Exception $e) {
        echo "✗ EXCEPTION: {$e->getMessage()}\n";
        echo "Stack trace:\n";
        echo $e->getTraceAsString();
    }
}
?>
