<?php
// Test if WorkoutPlan table exists and show its structure
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cricket_academy', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>WorkoutPlan Table Structure</h2>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'WorkoutPlan'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color: green;'>✓ WorkoutPlan table exists</p>";
        
        // Show table structure
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $stmt = $pdo->query("DESCRIBE WorkoutPlan");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show any existing data
        echo "<h3>Existing Records</h3>";
        $stmt = $pdo->query("SELECT * FROM WorkoutPlan");
        $count = $stmt->rowCount();
        echo "<p>Total records: {$count}</p>";
        
        if ($count > 0) {
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>PlanID</th><th>TrainerID</th><th>workoutname</th><th>frequency</th><th>Duration</th><th>CreatedDate</th></tr>";
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
        
    } else {
        echo "<p style='color: red;'>✗ WorkoutPlan table does NOT exist</p>";
        echo "<p>You need to create it first using the SQL schema.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>
