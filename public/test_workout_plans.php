<?php
require_once '../app/bootloader.php';

$db = new Database();

// Check total count
$db->query('SELECT COUNT(*) as count FROM WorkoutPlan');
$result = $db->single();
echo "Total WorkoutPlan count: " . $result->count . "<br><br>";

// Get all workout plans with trainers
$db->query('SELECT 
    wp.PlanID,
    wp.TrainerID,
    wp.workoutname,
    wp.frequency,
    wp.Duration,
    wp.durationdays,
    wp.VideoLink,
    wp.Intensity,
    wp.NotSuitableFor,
    wp.Benefits,
    wp.CreatedDate,
    u.name as trainer_name,
    u.email as trainer_email
FROM WorkoutPlan wp 
LEFT JOIN users u ON wp.TrainerID = u.user_id
ORDER BY wp.CreatedDate DESC');

$plans = $db->resultSet();

echo "Plans fetched: " . count($plans) . "<br><br>";

if (!empty($plans)) {
    echo "<pre>";
    print_r($plans);
    echo "</pre>";
} else {
    echo "No workout plans found!<br>";
    
    // Check if users table has trainers
    $db->query('SELECT user_id, name, role FROM users WHERE role = "Trainer"');
    $trainers = $db->resultSet();
    echo "<br>Trainers in database: " . count($trainers) . "<br>";
    if (!empty($trainers)) {
        echo "<pre>";
        print_r($trainers);
        echo "</pre>";
    }
}
