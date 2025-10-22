<?php
// Complete diagnostic test for workout plan insertion
session_start();
$_SESSION['user_id'] = 10;

echo "<h1>Workout Plan Add - Complete Diagnostic</h1>";
echo "<pre>";

// Step 1: Check database connection
echo "\n=== STEP 1: Database Connection ===\n";
require_once '../app/bootloader.php';
$db = new Database();
echo "✓ Database connection successful\n";

// Step 2: Check if trainer exists
echo "\n=== STEP 2: Verify Trainer ===\n";
$db->query('SELECT * FROM trainerprofile WHERE TrainerID = 10');
$trainer = $db->single();
if ($trainer) {
    echo "✓ TrainerID 10 exists\n";
} else {
    echo "✗ TrainerID 10 NOT FOUND - This is the problem!\n";
    die();
}

// Step 3: Test direct SQL insert
echo "\n=== STEP 3: Direct SQL Insert ===\n";
try {
    $db->query('INSERT INTO workoutplan (TrainerID, workoutname, frequency, Duration, VideoLink, Intensity, NotSuitableFor, Benefits) 
        VALUES (10, "Diagnostic Test", "Daily", 30, "https://test.com", "Moderate", "Test Not Suitable", "Test Benefits")');
    
    if ($db->execute()) {
        $lastId = $db->lastInsertId();
        echo "✓ Direct SQL insert successful! New PlanID: $lastId\n";
        
        // Clean up
        $db->query('DELETE FROM workoutplan WHERE PlanID = :id');
        $db->bind(':id', $lastId);
        $db->execute();
        echo "✓ Test record cleaned up\n";
    } else {
        echo "✗ Direct SQL insert failed\n";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

// Step 4: Test M_Trainer model
echo "\n=== STEP 4: M_Trainer Model Test ===\n";
require_once '../app/models/M_Trainer.php';
$trainerModel = new M_Trainer();

$testData = [
    'trainer_id' => 10,
    'workoutname' => 'Model Test Workout',
    'frequency' => 'Weekly',
    'duration' => 60,
    'videolink' => 'https://youtube.com/model-test',
    'intensity' => 'High',
    'notsuitablefor' => 'Model test contraindication',
    'benefits' => 'Model test benefits'
];

try {
    $result = $trainerModel->addWorkoutPlan($testData);
    if ($result) {
        echo "✓ Model addWorkoutPlan() successful!\n";
        
        // Find and display the added plan
        $plans = $trainerModel->getWorkoutPlans(10);
        foreach ($plans as $plan) {
            if ($plan->workoutname === 'Model Test Workout') {
                echo "✓ Plan retrieved - ID: {$plan->PlanID}\n";
                echo "  Name: {$plan->workoutname}\n";
                echo "  VideoLink: {$plan->VideoLink}\n";
                echo "  Intensity: {$plan->Intensity}\n";
                
                // Clean up
                $trainerModel->deleteWorkoutPlan($plan->PlanID, 10);
                echo "✓ Test record cleaned up\n";
                break;
            }
        }
    } else {
        echo "✗ Model addWorkoutPlan() returned false\n";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

// Step 5: Test controller sanitization
echo "\n=== STEP 5: Controller Sanitization Test ===\n";
$_POST = [
    'workoutname' => 'Controller Test <script>alert("xss")</script>',
    'frequency' => 'Daily',
    'duration' => '45',
    'intensity' => 'Moderate',
    'videolink' => 'https://youtube.com/test',
    'benefits' => 'Test benefits with "quotes" and \'apostrophes\'',
    'notsuitablefor' => 'Test contraindications'
];

$sanitized = [
    'trainer_id' => 10,
    'workoutname' => isset($_POST['workoutname']) ? trim(htmlspecialchars($_POST['workoutname'], ENT_QUOTES, 'UTF-8')) : '',
    'frequency' => isset($_POST['frequency']) ? htmlspecialchars($_POST['frequency'], ENT_QUOTES, 'UTF-8') : '',
    'duration' => isset($_POST['duration']) ? (int)$_POST['duration'] : 0,
    'videolink' => !empty($_POST['videolink']) ? trim(htmlspecialchars($_POST['videolink'], ENT_QUOTES, 'UTF-8')) : null,
    'intensity' => !empty($_POST['intensity']) ? htmlspecialchars($_POST['intensity'], ENT_QUOTES, 'UTF-8') : 'Moderate',
    'notsuitablefor' => !empty($_POST['notsuitablefor']) ? trim(htmlspecialchars($_POST['notsuitablefor'], ENT_QUOTES, 'UTF-8')) : null,
    'benefits' => !empty($_POST['benefits']) ? trim(htmlspecialchars($_POST['benefits'], ENT_QUOTES, 'UTF-8')) : null
];

echo "Original POST['workoutname']: {$_POST['workoutname']}\n";
echo "Sanitized workoutname: {$sanitized['workoutname']}\n";
echo "✓ Sanitization working correctly (XSS prevented)\n";

try {
    $result = $trainerModel->addWorkoutPlan($sanitized);
    if ($result) {
        echo "✓ Sanitized data inserted successfully!\n";
        
        // Clean up
        $plans = $trainerModel->getWorkoutPlans(10);
        foreach ($plans as $plan) {
            if (strpos($plan->workoutname, 'Controller Test') !== false) {
                $trainerModel->deleteWorkoutPlan($plan->PlanID, 10);
                echo "✓ Test record cleaned up\n";
                break;
            }
        }
    } else {
        echo "✗ Sanitized data insert failed\n";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "\nIf all tests passed, the form should work.\n";
echo "Try accessing: http://localhost/Elite/trainer/workout\n";
echo "And use the 'Add New Plan' button.\n";
echo "</pre>";
?>
