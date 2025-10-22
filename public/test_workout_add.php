<?php
// Test workout plan addition
require_once '../app/bootloader.php';

// Simulate session for trainer
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 10; // Test trainer ID
    $_SESSION['user_role'] = 'trainer';
}

echo "<h2>Workout Plan Add Test</h2>";

// Test data
$testData = [
    'workoutname' => 'Test Strength Training',
    'frequency' => 'Daily',
    'duration' => 60 // minutes
];

echo "<h3>Test Data:</h3>";
echo "<pre>";
print_r($testData);
echo "</pre>";

// Try to add via controller
try {
    // Simulate POST request
    $_POST['workoutname'] = $testData['workoutname'];
    $_POST['frequency'] = $testData['frequency'];
    $_POST['duration'] = $testData['duration'];
    
    // Call controller method
    require_once '../app/libraries/Database.php';
    require_once '../app/models/M_Trainer.php';
    
    $trainerModel = new M_Trainer();
    
    $data = [
        'trainer_id' => $_SESSION['user_id'],
        'workoutname' => htmlspecialchars(trim($_POST['workoutname'])),
        'frequency' => htmlspecialchars(trim($_POST['frequency'])),
        'duration' => intval($_POST['duration'])
    ];
    
    echo "<h3>Data to insert:</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    $result = $trainerModel->addWorkoutPlan($data);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Workout plan added successfully!</p>";
        
        // Fetch the added plans
        $plans = $trainerModel->getWorkoutPlans($_SESSION['user_id']);
        echo "<h3>Current Workout Plans:</h3>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>PlanID</th><th>TrainerID</th><th>Workout Name</th><th>Frequency</th><th>Duration</th><th>Created Date</th></tr>";
        foreach ($plans as $plan) {
            echo "<tr>";
            echo "<td>{$plan->PlanID}</td>";
            echo "<td>{$plan->TrainerID}</td>";
            echo "<td>{$plan->workoutname}</td>";
            echo "<td>{$plan->frequency}</td>";
            echo "<td>{$plan->Duration}</td>";
            echo "<td>{$plan->CreatedDate}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ Failed to add workout plan</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
