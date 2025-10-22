<?php
require_once '../app/bootloader.php';

$db = new Database();

echo "=== Checking Trainer Constraint ===\n\n";

// Check if TrainerID 10 exists in trainerprofile
$db->query('SELECT * FROM trainerprofile WHERE TrainerID = 10');
$trainer = $db->single();

if ($trainer) {
    echo "✓ TrainerID 10 EXISTS in trainerprofile table\n";
    print_r($trainer);
} else {
    echo "✗ TrainerID 10 NOT FOUND in trainerprofile table\n";
}

echo "\n=== All Trainers in trainerprofile ===\n";
$db->query('SELECT tp.TrainerID, u.Name, u.Role FROM trainerprofile tp LEFT JOIN User u ON tp.TrainerID = u.UserID');
$trainers = $db->resultSet();
if (!empty($trainers)) {
    foreach ($trainers as $t) {
        echo "TrainerID: {$t->TrainerID}, Name: {$t->Name}, Role: {$t->Role}\n";
    }
} else {
    echo "No trainers found\n";
}

echo "\n=== Testing INSERT ===\n";
try {
    $db->query('INSERT INTO workoutplan (TrainerID, workoutname, frequency, Duration, VideoLink, Intensity, NotSuitableFor, Benefits) 
        VALUES (10, "Test Workout", "Daily", 30, NULL, "Moderate", NULL, NULL)');
    
    if ($db->execute()) {
        echo "✓ INSERT successful!\n";
        // Get the inserted record
        $db->query('SELECT * FROM workoutplan WHERE workoutname = "Test Workout"');
        $result = $db->single();
        print_r($result);
        
        // Clean up test data
        $db->query('DELETE FROM workoutplan WHERE workoutname = "Test Workout"');
        $db->execute();
        echo "\n✓ Test data cleaned up\n";
    } else {
        echo "✗ INSERT failed\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
