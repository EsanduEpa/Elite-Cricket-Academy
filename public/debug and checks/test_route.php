<?php
// Test the actual player/addMedicalRecord route
session_start();

echo "<h2>Player addMedicalRecord Route Test</h2>";

// Simulate POST data
$_POST = [
    'injury_details' => 'Test injury from route test',
    'diagnosis' => 'Test diagnosis from route test',
    'treatment_given' => 'Test treatment',
    'recovery_status' => 'ongoing',
    'reported_date' => date('Y-m-d')
];

$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h3>Simulated POST Data:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Load the necessary files
require_once '../app/bootloader.php';

// Check if we can access the Player controller
try {
    $playerController = new Player();
    echo "<p style='color: green;'>✅ Player controller loaded successfully</p>";
    
    // Check if the method exists
    if (method_exists($playerController, 'addMedicalRecord')) {
        echo "<p style='color: green;'>✅ addMedicalRecord method exists</p>";
        
        // Try to call the method
        echo "<h3>Calling addMedicalRecord method...</h3>";
        
        // Capture any output
        ob_start();
        try {
            $playerController->addMedicalRecord();
            $output = ob_get_contents();
            echo "<p style='color: green;'>✅ Method executed successfully</p>";
            if ($output) {
                echo "<h4>Method Output:</h4>";
                echo "<pre>" . htmlspecialchars($output) . "</pre>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Method execution failed: " . $e->getMessage() . "</p>";
        }
        ob_end_clean();
        
    } else {
        echo "<p style='color: red;'>❌ addMedicalRecord method does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading Player controller: " . $e->getMessage() . "</p>";
}

// Check the current database state
echo "<h3>Database State Check</h3>";
try {
    require_once '../app/libraries/Database.php';
    $db = new Database();
    $db->query("SELECT COUNT(*) as count FROM PlayerMedicalRecord");
    $count = $db->single();
    echo "<p>Total records in PlayerMedicalRecord table: " . ($count ? $count->count : 0) . "</p>";
    
    if ($count && $count->count > 0) {
        $db->query("SELECT * FROM PlayerMedicalRecord ORDER BY CreatedAt DESC LIMIT 1");
        $lastRecord = $db->single();
        echo "<h4>Last Record Added:</h4>";
        echo "<pre>";
        print_r($lastRecord);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database check error: " . $e->getMessage() . "</p>";
}
?>