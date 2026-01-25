<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix WorkoutPlan Table</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #4A90E2; border-bottom: 3px solid #4A90E2; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; color: #e83e8c; }
        .btn { background: #4A90E2; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #357ABD; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #4A90E2; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border: 1px solid #dee2e6; }
        tr:nth-child(even) { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔧 WorkoutPlan Table Setup & Repair</h2>

<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cricket_academy', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✓ Database connection successful</div>";
    
    // Check MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<div class='info'><strong>MySQL Version:</strong> {$version}</div>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'WorkoutPlan'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<div class='warning'>⚠ WorkoutPlan table already exists. Dropping it to recreate...</div>";
        $pdo->exec("DROP TABLE WorkoutPlan");
        echo "<div class='info'>Table dropped</div>";
    }
    
    echo "<h3>Creating WorkoutPlan Table...</h3>";
    
    // Create table with compatible syntax for all MySQL versions
    $createSQL = "CREATE TABLE WorkoutPlan (
        PlanID INT AUTO_INCREMENT PRIMARY KEY,
        TrainerID INT NOT NULL,
        workoutname VARCHAR(255),
        frequency ENUM('Daily', 'Weekly', 'Bi-weekly') DEFAULT 'Daily',
        Duration INT DEFAULT 60 COMMENT 'Duration in minutes',
        CreatedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_trainer_workout (TrainerID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Workout plans for trainers'";
    
    echo "<div class='info'><strong>SQL Command:</strong><pre>" . htmlspecialchars($createSQL) . "</pre></div>";
    
    $pdo->exec($createSQL);
    
    echo "<div class='success'>✓✓✓ WorkoutPlan table created successfully!</div>";
    
    // Verify table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE WorkoutPlan");
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td><strong>{$row['Field']}</strong></td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>" . ($row['Default'] ?? '<em>NULL</em>') . "</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test INSERT
    echo "<h3>Testing INSERT...</h3>";
    $testSQL = "INSERT INTO WorkoutPlan (TrainerID, workoutname, frequency, Duration) 
                VALUES (10, 'Test Strength Training', 'Daily', 60)";
    
    echo "<div class='info'><strong>Test SQL:</strong><pre>" . htmlspecialchars($testSQL) . "</pre></div>";
    
    $pdo->exec($testSQL);
    $lastId = $pdo->lastInsertId();
    
    echo "<div class='success'>✓ Test insert successful! PlanID: {$lastId}</div>";
    
    // Verify the insert
    $stmt = $pdo->query("SELECT * FROM WorkoutPlan WHERE PlanID = {$lastId}");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<div class='info'><strong>Inserted Record:</strong><pre>" . print_r($row, true) . "</pre></div>";
    
    // Clean up test record
    $pdo->exec("DELETE FROM WorkoutPlan WHERE PlanID = {$lastId}");
    echo "<div class='info'>Test record deleted (cleanup)</div>";
    
    echo "<div class='success' style='margin-top: 30px; font-size: 18px;'>";
    echo "🎉 <strong>SUCCESS!</strong> The WorkoutPlan table is now ready to use!";
    echo "</div>";
    
    echo "<div style='margin-top: 20px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to the <a href='test_workout_insert.php' class='btn' style='margin: 0 10px;'>MVC Insert Test</a></li>";
    echo "<li>Or try the actual page: <a href='../trainer/workout' class='btn' style='margin: 0 10px;'>Trainer Workout Page</a></li>";
    echo "</ol>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Database Error</h3>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Code:</strong> " . $e->getCode() . "<br>";
    echo "<strong>Trace:</strong><pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "<h4>Common Issues:</h4>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Verify database 'cricket_academy' exists</li>";
    echo "<li>Check if you have permissions to create tables</li>";
    echo "</ul>";
    echo "</div>";
}
?>
    </div>
</body>
</html>
