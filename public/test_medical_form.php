<?php
// Simple form submission test
session_start();

// Load the config and necessary files
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';
require_once '../app/helpers/session_helper.php';

echo "<h2>Medical Form Debug Test</h2>";

// Process form if submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test database connection
    try {
        $db = new Database();
        echo "<p style='color: green;'>✅ Database connected</p>";
        
        // Check if table exists
        $db->query("SHOW TABLES LIKE 'PlayerMedicalRecord'");
        $tableExists = $db->single();
        
        if (!$tableExists) {
            echo "<p style='color: red;'>❌ Table doesn't exist, creating it...</p>";
            
            $createSQL = "CREATE TABLE PlayerMedicalRecord (
                RecordID INT AUTO_INCREMENT PRIMARY KEY,
                PlayerID INT NOT NULL,
                InjuryDetails TEXT NOT NULL,
                Diagnosis TEXT NOT NULL,
                TreatmentGiven TEXT,
                RecoveryStatus ENUM('ongoing', 'recovering', 'recovered', 'chronic') NOT NULL,
                ReportedDate DATE NOT NULL,
                ReportedBy INT NOT NULL,
                CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $db->query($createSQL);
            if ($db->execute()) {
                echo "<p style='color: green;'>✅ Table created successfully!</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create table</p>";
            }
        } else {
            echo "<p style='color: green;'>✅ Table exists</p>";
        }
        
        // Try to insert the data
        if (!empty($_POST['injury_details']) && !empty($_POST['diagnosis'])) {
            echo "<h3>Attempting to insert data...</h3>";
            
            $db->query('INSERT INTO PlayerMedicalRecord (
                PlayerID, 
                InjuryDetails, 
                Diagnosis, 
                TreatmentGiven, 
                RecoveryStatus, 
                ReportedDate, 
                ReportedBy
            ) VALUES (
                :player_id, 
                :injury_details, 
                :diagnosis, 
                :treatment_given, 
                :recovery_status, 
                :reported_date, 
                :reported_by
            )');
            
            $db->bind(':player_id', $_SESSION['user_id'] ?? 1); // Use session user ID or fallback
            $db->bind(':injury_details', $_POST['injury_details']);
            $db->bind(':diagnosis', $_POST['diagnosis']);
            $db->bind(':treatment_given', $_POST['treatment_given'] ?? '');
            $db->bind(':recovery_status', $_POST['recovery_status']);
            $db->bind(':reported_date', $_POST['reported_date']);
            $db->bind(':reported_by', $_SESSION['user_id'] ?? 1); // Use session user ID or fallback
            
            if ($db->execute()) {
                $lastId = $db->lastInsertId();
                echo "<p style='color: green;'>✅ Record inserted successfully! ID: " . $lastId . "</p>";
                
                // Show the inserted record
                $db->query("SELECT * FROM PlayerMedicalRecord WHERE RecordID = :id");
                $db->bind(':id', $lastId);
                $record = $db->single();
                
                echo "<h4>Inserted Record:</h4>";
                echo "<pre>";
                print_r($record);
                echo "</pre>";
                
            } else {
                echo "<p style='color: red;'>❌ Failed to insert record</p>";
                echo "<p>Check database permissions and table structure.</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Show current records
try {
    $db = new Database();
    $db->query("SELECT COUNT(*) as count FROM PlayerMedicalRecord");
    $count = $db->single();
    echo "<h3>Current Records in Database: " . ($count ? $count->count : 0) . "</h3>";
    
    if ($count && $count->count > 0) {
        $db->query("SELECT * FROM PlayerMedicalRecord ORDER BY CreatedAt DESC LIMIT 5");
        $records = $db->resultSet();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>PlayerID</th><th>Injury</th><th>Diagnosis</th><th>Status</th><th>Date</th></tr>";
        foreach ($records as $record) {
            echo "<tr>";
            echo "<td>" . $record->RecordID . "</td>";
            echo "<td>" . $record->PlayerID . "</td>";
            echo "<td>" . substr($record->InjuryDetails, 0, 30) . "...</td>";
            echo "<td>" . substr($record->Diagnosis, 0, 30) . "...</td>";
            echo "<td>" . $record->RecoveryStatus . "</td>";
            echo "<td>" . $record->ReportedDate . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking records: " . $e->getMessage() . "</p>";
}
?>

<hr>
<h3>Test Form</h3>
<form method="POST">
    <p>
        <label>Injury Details (required):</label><br>
        <textarea name="injury_details" rows="3" cols="50" required>Test injury from debug form</textarea>
    </p>
    <p>
        <label>Diagnosis (required):</label><br>
        <textarea name="diagnosis" rows="2" cols="50" required>Test diagnosis</textarea>
    </p>
    <p>
        <label>Treatment Given:</label><br>
        <textarea name="treatment_given" rows="2" cols="50">Test treatment</textarea>
    </p>
    <p>
        <label>Recovery Status:</label><br>
        <select name="recovery_status" required>
            <option value="ongoing">Ongoing</option>
            <option value="recovering">Recovering</option>
            <option value="recovered">Recovered</option>
            <option value="chronic">Chronic</option>
        </select>
    </p>
    <p>
        <label>Date:</label><br>
        <input type="date" name="reported_date" value="<?php echo date('Y-m-d'); ?>" required>
    </p>
    <p>
        <input type="submit" value="Submit Test Record">
    </p>
</form>

<hr>
<p><a href="<?php echo URLROOT; ?>/player/medical">← Back to Player Medical Page</a></p>