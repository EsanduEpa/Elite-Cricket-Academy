<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Workout Plan Insert Test with MVC</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .error{color:red;background:#ffe5e5;padding:10px;margin:10px 0;border-radius:5px;} .success{color:green;background:#e5ffe5;padding:10px;margin:10px 0;border-radius:5px;} .info{color:blue;background:#e5f2ff;padding:10px;margin:10px 0;border-radius:5px;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow:auto;}</style>";

try {
    // Bootstrap the application
    require_once '../app/bootloader.php';
    
    echo "<div class='success'>✓ Bootloader loaded successfully</div>";
    
    // Set up session
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 10;
        $_SESSION['user_name'] = 'Test Trainer';
        $_SESSION['user_role'] = 'Trainer';
    }
    
    echo "<div class='info'>Session User ID: " . $_SESSION['user_id'] . "</div>";
    
    // Load necessary files
    require_once '../app/libraries/Database.php';
    require_once '../app/models/M_Trainer.php';
    
    echo "<div class='success'>✓ Required files loaded</div>";
    
    // Create model instance
    $trainerModel = new M_Trainer();
    
    echo "<div class='success'>✓ M_Trainer model instantiated</div>";
    
    // Test data
    $testData = [
        'trainer_id' => 10,
        'workoutname' => 'Test Workout ' . date('H:i:s'),
        'frequency' => 'Daily',
        'duration' => 60
    ];
    
    echo "<div class='info'><strong>Test Data:</strong>";
    echo "<pre>" . print_r($testData, true) . "</pre></div>";
    
    // First, check if table exists
    echo "<h3>Checking Database Table...</h3>";
    $pdo = new PDO('mysql:host=localhost;dbname=cricket_academy', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'WorkoutPlan'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<div class='error'>❌ WorkoutPlan table does NOT exist!</div>";
        echo "<div class='info'>Creating table...</div>";
        
        $createSQL = "CREATE TABLE WorkoutPlan (
            PlanID INT AUTO_INCREMENT PRIMARY KEY,
            TrainerID INT NOT NULL,
            workoutname VARCHAR(255),
            frequency ENUM('Daily', 'Weekly', 'Bi-weekly'),
            Duration INT COMMENT 'Duration in minutes',
            CreatedDate DATE DEFAULT (CURRENT_DATE),
            INDEX idx_trainer_workout (TrainerID)
        ) ENGINE=InnoDB";
        
        $pdo->exec($createSQL);
        echo "<div class='success'>✓ Table created!</div>";
    } else {
        echo "<div class='success'>✓ WorkoutPlan table exists</div>";
    }
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE WorkoutPlan");
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr style='background:#4A90E2;color:white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
    }
    echo "</table>";
    
    // Try the insert using the model
    echo "<h3>Testing Insert via M_Trainer Model...</h3>";
    
    $result = $trainerModel->addWorkoutPlan($testData);
    
    if ($result) {
        echo "<div class='success'>✓✓✓ SUCCESS! Workout plan added via model!</div>";
        
        // Fetch and display
        $plans = $trainerModel->getWorkoutPlans(10);
        echo "<h3>Current Workout Plans:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        echo "<tr style='background:#4A90E2;color:white;'><th>PlanID</th><th>TrainerID</th><th>Workout Name</th><th>Frequency</th><th>Duration</th><th>Created</th></tr>";
        foreach ($plans as $plan) {
            echo "<tr>";
            echo "<td>{$plan->PlanID}</td>";
            echo "<td>{$plan->TrainerID}</td>";
            echo "<td>{$plan->workoutname}</td>";
            echo "<td>{$plan->frequency}</td>";
            echo "<td>{$plan->Duration} mins</td>";
            echo "<td>{$plan->CreatedDate}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>❌ FAILED! Model returned FALSE</div>";
        echo "<div class='info'>Check the error log for details</div>";
    }
    
    // Show error log location
    echo "<h3>Check Error Logs:</h3>";
    echo "<div class='info'>";
    echo "PHP Error Log: <code>C:\\xampp\\php\\logs\\php_error_log</code><br>";
    echo "Apache Error Log: <code>C:\\xampp\\apache\\logs\\error.log</code>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<strong>PDO Exception:</strong><br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "Code: " . $e->getCode() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>Exception:</strong><br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>

<div style="margin-top:30px; padding:20px; background:#f0f0f0; border-radius:5px;">
    <h3>What to do if it fails:</h3>
    <ol>
        <li>Check if the table was created properly</li>
        <li>Verify database connection settings in <code>app/config/config.php</code></li>
        <li>Check error logs at the locations shown above</li>
        <li>Make sure TrainerID 10 exists in your database (or change it in the test data)</li>
    </ol>
</div>
