<?php
// Debug script to test workout plan retrieval
session_start();

// Include necessary files
require_once 'app/config/config.php';
require_once 'app/libraries/Database.php';
require_once 'app/models/M_Trainer.php';

// Set a test trainer ID that matches existing data
$_SESSION['user_id'] = 10;

// Initialize model
$trainer = new M_Trainer();

// Test the getWorkoutPlans method
echo "<h2>Testing Workout Plans Retrieval</h2>";
$workoutPlans = $trainer->getWorkoutPlans();

echo "<h3>Raw workout plans data:</h3>";
var_dump($workoutPlans);

echo "<h3>Number of plans found:</h3>";
echo count($workoutPlans);

echo "<h3>Formatted output:</h3>";
if (!empty($workoutPlans)) {
    echo "<table border='1'>";
    echo "<tr><th>Plan ID</th><th>Workout Name</th><th>Frequency</th><th>Duration</th><th>Created Date</th></tr>";
    foreach ($workoutPlans as $plan) {
        echo "<tr>";
        echo "<td>" . $plan->PlanID . "</td>";
        echo "<td>" . $plan->workoutname . "</td>";
        echo "<td>" . $plan->frequency . "</td>";
        echo "<td>" . $plan->Duration . "</td>";
        echo "<td>" . $plan->CreatedDate . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No workout plans found.";
}

// Test direct database query
echo "<h3>Direct Database Test:</h3>";
$db = new Database();
$db->query('SELECT * FROM WorkoutPlan LIMIT 5');
$directResults = $db->resultSet();
echo "Direct query results: ";
var_dump($directResults);
?>