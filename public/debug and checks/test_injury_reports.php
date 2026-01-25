<?php
// Test injury reports functionality
require_once '../app/bootloader.php';

// Set session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test Trainer';
$_SESSION['user_type'] = 'Trainer';
$_SESSION['user_role'] = 'Trainer';

echo "<h2>Testing Injury Reports</h2>";

try {
    // Test database connection
    $db = new Database();
    echo "✅ Database connection successful<br>";
    
    // Test M_Medical model
    $medicalModel = new M_Medical();
    echo "✅ M_Medical model loaded<br>";
    
    // Test getAllMedicalRecords method
    $records = $medicalModel->getAllMedicalRecords();
    echo "✅ getAllMedicalRecords method executed<br>";
    echo "📊 Found " . count($records) . " medical records<br>";
    
    if (!empty($records)) {
        echo "<h3>Sample Records:</h3>";
        foreach (array_slice($records, 0, 3) as $record) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<strong>Record ID:</strong> " . $record->RecordID . "<br>";
            echo "<strong>Player:</strong> " . ($record->player_name ?? 'Unknown') . "<br>";
            echo "<strong>Injury:</strong> " . $record->InjuryDetails . "<br>";
            echo "<strong>Status:</strong> " . $record->RecoveryStatus . "<br>";
            echo "<strong>Date:</strong> " . $record->ReportedDate . "<br>";
            echo "</div>";
        }
    }
    
    // Test the trainer controller
    echo "<br><h3>Testing Trainer Controller:</h3>";
    $trainer = new Trainer();
    echo "✅ Trainer controller loaded<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

echo "<br><a href='index.php/trainer/injuryReports'>🔗 Try Injury Reports Page</a>";
?>