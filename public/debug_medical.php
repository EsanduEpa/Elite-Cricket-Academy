<?php
// Debug medical records system
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';

echo "<h2>Medical Records Debug</h2>";

try {
    $db = new Database();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Check if PlayerMedicalRecord table exists
    echo "<h3>1. Table Structure Check</h3>";
    $db->query("SHOW TABLES LIKE 'PlayerMedicalRecord'");
    $result = $db->single();
    
    if ($result) {
        echo "<p style='color: green;'>✅ PlayerMedicalRecord table exists!</p>";
        
        // Show table structure
        echo "<h4>Table Structure:</h4>";
        $db->query("DESCRIBE PlayerMedicalRecord");
        $columns = $db->resultSet();
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . $col->Field . "</td>";
            echo "<td>" . $col->Type . "</td>";
            echo "<td>" . $col->Null . "</td>";
            echo "<td>" . $col->Key . "</td>";
            echo "<td>" . $col->Default . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count existing records
        $db->query("SELECT COUNT(*) as count FROM PlayerMedicalRecord");
        $count = $db->single();
        echo "<p>Current medical records in database: <strong>" . $count->count . "</strong></p>";
        
        // Show all records if any exist
        if ($count->count > 0) {
            echo "<h4>Existing Records:</h4>";
            $db->query("SELECT * FROM PlayerMedicalRecord ORDER BY ReportedDate DESC");
            $records = $db->resultSet();
            
            echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
            echo "<tr><th>ID</th><th>PlayerID</th><th>Injury Details</th><th>Diagnosis</th><th>Recovery Status</th><th>Date</th></tr>";
            foreach ($records as $record) {
                echo "<tr>";
                echo "<td>" . $record->RecordID . "</td>";
                echo "<td>" . $record->PlayerID . "</td>";
                echo "<td>" . substr($record->InjuryDetails, 0, 50) . "...</td>";
                echo "<td>" . substr($record->Diagnosis, 0, 30) . "...</td>";
                echo "<td>" . $record->RecoveryStatus . "</td>";
                echo "<td>" . $record->ReportedDate . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ PlayerMedicalRecord table does NOT exist!</p>";
        echo "<p>Creating table now...</p>";
        
        // Create the table
        $createTableSQL = "
        CREATE TABLE PlayerMedicalRecord (
            RecordID INT AUTO_INCREMENT PRIMARY KEY,
            PlayerID INT NOT NULL,
            InjuryDetails TEXT NOT NULL,
            Diagnosis TEXT NOT NULL,
            TreatmentGiven TEXT,
            RecoveryStatus ENUM('ongoing', 'recovering', 'recovered', 'chronic') NOT NULL,
            ReportedDate DATE NOT NULL,
            ReportedBy INT NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (PlayerID) REFERENCES User(UserID),
            FOREIGN KEY (ReportedBy) REFERENCES User(UserID)
        )";
        
        $db->query($createTableSQL);
        if ($db->execute()) {
            echo "<p style='color: green;'>✅ PlayerMedicalRecord table created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create table</p>";
        }
    }
    
    // Check User table for PlayerID
    echo "<h3>2. User Table Check</h3>";
    $db->query("SELECT UserID, name, role FROM User WHERE role = 'player' LIMIT 5");
    $users = $db->resultSet();
    
    if ($users) {
        echo "<p style='color: green;'>✅ Found player users:</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
        echo "<tr><th>UserID</th><th>Name</th><th>Role</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user->UserID . "</td>";
            echo "<td>" . $user->name . "</td>";
            echo "<td>" . $user->role . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No player users found in database</p>";
    }
    
    // Test model loading
    echo "<h3>3. Model Test</h3>";
    require_once '../app/models/M_Medical.php';
    
    try {
        $medicalModel = new M_Medical();
        echo "<p style='color: green;'>✅ M_Medical model loaded successfully!</p>";
        
        // Test getting records for current logged-in user
        session_start();
        $testPlayerId = $_SESSION['user_id'] ?? 1; // Use session user ID or fallback to 1
        $records = $medicalModel->getMedicalRecords($testPlayerId);
        echo "<p>Records for Player ID " . $testPlayerId . ": " . count($records) . " records</p>";
        
        if ($_SESSION['user_id'] ?? false) {
            echo "<p style='color: green;'>✅ User is logged in with ID: " . $_SESSION['user_id'] . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No user logged in (using fallback ID 1)</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error loading M_Medical model: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Check POST data if this is a form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>4. POST Data Received</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}

echo "<hr>";
echo "<h3>Test Form</h3>";
echo "<form method='POST'>";
echo "<p>Injury Details: <input type='text' name='injury_details' value='Test injury'></p>";
echo "<p>Diagnosis: <input type='text' name='diagnosis' value='Test diagnosis'></p>";
echo "<p>Treatment: <input type='text' name='treatment_given' value='Test treatment'></p>";
echo "<p>Recovery Status: <select name='recovery_status'>";
echo "<option value='ongoing'>Ongoing</option>";
echo "<option value='recovering'>Recovering</option>";
echo "<option value='recovered'>Recovered</option>";
echo "<option value='chronic'>Chronic</option>";
echo "</select></p>";
echo "<p>Date: <input type='date' name='reported_date' value='" . date('Y-m-d') . "'></p>";
echo "<p><input type='submit' value='Test Submit'></p>";
echo "</form>";
?>