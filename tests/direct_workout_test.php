<!DOCTYPE html>
<html>
<head>
    <title>Workout Plan Direct Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #4A90E2; border-bottom: 2px solid #4A90E2; padding-bottom: 10px; }
        .success { color: #2ed573; background: #e8f8f0; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: #ff6b6b; background: #ffe5e5; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #4A90E2; background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #4A90E2; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
        .btn { background: #4A90E2; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #357ABD; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🏋️ Workout Plan Database Test</h2>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=cricket_academy', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✓ Database connection successful</div>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'WorkoutPlan'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<div class='error'>❌ WorkoutPlan table does NOT exist. Creating it now...</div>";
        
        // Create table without foreign key
        $createSQL = "CREATE TABLE WorkoutPlan (
            PlanID INT AUTO_INCREMENT PRIMARY KEY,
            TrainerID INT NOT NULL,
            workoutname VARCHAR(255),
            frequency ENUM('Daily', 'Weekly', 'Bi-weekly'),
            Duration INT COMMENT 'Duration in minutes',
            CreatedDate DATE DEFAULT (CURRENT_DATE),
            INDEX idx_trainer_workout (TrainerID)
        ) ENGINE=InnoDB COMMENT='Customized workout plans for players'";
        
        $pdo->exec($createSQL);
        echo "<div class='success'>✓ WorkoutPlan table created successfully!</div>";
    } else {
        echo "<div class='success'>✓ WorkoutPlan table exists</div>";
    }
    
    // Show current table structure
    echo "<h3>Table Structure:</h3>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $stmt = $pdo->query("DESCRIBE WorkoutPlan");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td><strong>{$row['Field']}</strong></td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test INSERT
    echo "<h3>Testing INSERT Operation:</h3>";
    
    $testData = [
        'trainer_id' => 10,
        'workoutname' => 'Test Cardio Workout ' . date('H:i:s'),
        'frequency' => 'Daily',
        'duration' => 60
    ];
    
    echo "<div class='info'><strong>Test Data:</strong><br>";
    echo "Trainer ID: {$testData['trainer_id']}<br>";
    echo "Workout Name: {$testData['workoutname']}<br>";
    echo "Frequency: {$testData['frequency']}<br>";
    echo "Duration: {$testData['duration']} minutes</div>";
    
    $stmt = $pdo->prepare("INSERT INTO WorkoutPlan (TrainerID, workoutname, frequency, Duration) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([
        $testData['trainer_id'],
        $testData['workoutname'],
        $testData['frequency'],
        $testData['duration']
    ]);
    
    if ($result) {
        $lastId = $pdo->lastInsertId();
        echo "<div class='success'>✓ INSERT successful! New PlanID: {$lastId}</div>";
    } else {
        echo "<div class='error'>❌ INSERT failed</div>";
    }
    
    // Show all records
    echo "<h3>All Workout Plans:</h3>";
    $stmt = $pdo->query("SELECT * FROM WorkoutPlan ORDER BY CreatedDate DESC LIMIT 20");
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo "<table>";
        echo "<tr><th>PlanID</th><th>TrainerID</th><th>Workout Name</th><th>Frequency</th><th>Duration (min)</th><th>Created Date</th></tr>";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><strong>#{$row['PlanID']}</strong></td>";
            echo "<td>{$row['TrainerID']}</td>";
            echo "<td>{$row['workoutname']}</td>";
            echo "<td><span style='background: #e3f2fd; padding: 4px 8px; border-radius: 4px;'>{$row['frequency']}</span></td>";
            echo "<td>{$row['Duration']} mins</td>";
            echo "<td>{$row['CreatedDate']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div class='info'>Total Records: {$count}</div>";
    } else {
        echo "<div class='info'>No workout plans found yet.</div>";
    }
    
    // Test DELETE (remove test record)
    if (isset($lastId)) {
        $stmt = $pdo->prepare("DELETE FROM WorkoutPlan WHERE PlanID = ?");
        $stmt->execute([$lastId]);
        echo "<div class='info'>ℹ️ Test record (PlanID: {$lastId}) has been deleted for cleanup</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<strong>Database Error:</strong><br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "Code: " . $e->getCode();
    echo "</div>";
}
?>

        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h4>Next Steps:</h4>
            <ol>
                <li>If the table was created, go to: <a href="../trainer/workout" style="color: #4A90E2;">Trainer Workout Page</a></li>
                <li>Try adding a workout plan through the actual interface</li>
                <li>Check error logs in: <code>C:\xampp\php\logs\php_error_log</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
