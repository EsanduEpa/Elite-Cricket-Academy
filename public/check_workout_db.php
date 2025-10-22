<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Workout Plan Database Check</h2>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=cricket_academy', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'WorkoutPlan'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<h3 style='color: red;'>❌ WorkoutPlan table does NOT exist!</h3>";
        echo "<p>Creating the table now...</p>";
        
        // Create the table WITHOUT foreign key constraint (to avoid dependency issues)
        $createSQL = "CREATE TABLE WorkoutPlan (
            PlanID INT AUTO_INCREMENT PRIMARY KEY,
            TrainerID INT NOT NULL,
            workoutname varchar(255),
            frequency ENUM('Daily', 'Weekly', 'Bi-weekly'),
            Duration INT COMMENT 'Duration in minutes',
            CreatedDate DATE DEFAULT (CURRENT_DATE),
            INDEX idx_trainer_workout (TrainerID)
        ) ENGINE=InnoDB COMMENT='Customized workout plans for players'";
        
        try {
            $pdo->exec($createSQL);
            echo "<p style='color: green;'>✓ WorkoutPlan table created successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Failed to create table: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<h3 style='color: green;'>✓ WorkoutPlan table exists</h3>";
    }
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $stmt = $pdo->query("DESCRIBE WorkoutPlan");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count existing records
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM WorkoutPlan");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<h3>Existing Records: {$count}</h3>";
    
    if ($count > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>PlanID</th><th>TrainerID</th><th>Workout Name</th><th>Frequency</th><th>Duration</th><th>Created Date</th></tr>";
        
        $stmt = $pdo->query("SELECT * FROM WorkoutPlan ORDER BY CreatedDate DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['PlanID']}</td>";
            echo "<td>{$row['TrainerID']}</td>";
            echo "<td>{$row['workoutname']}</td>";
            echo "<td>{$row['frequency']}</td>";
            echo "<td>{$row['Duration']}</td>";
            echo "<td>{$row['CreatedDate']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test insert
    echo "<h3>Testing INSERT operation...</h3>";
    try {
        $testInsert = $pdo->prepare("INSERT INTO WorkoutPlan (TrainerID, workoutname, frequency, Duration) VALUES (?, ?, ?, ?)");
        $result = $testInsert->execute([10, 'Test Workout Plan', 'Daily', 60]);
        
        if ($result) {
            $lastId = $pdo->lastInsertId();
            echo "<p style='color: green;'>✓ Test insert successful! New PlanID: {$lastId}</p>";
            
            // Delete test record
            $pdo->exec("DELETE FROM WorkoutPlan WHERE PlanID = {$lastId}");
            echo "<p style='color: blue;'>ℹ Test record deleted</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Insert test failed: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Database Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Error Code: " . $e->getCode() . "</p>";
}
?>
