<?php
// Test session creation directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once '../app/bootloader.php';

// Start session
session_start();

// Set a test coach ID
$_SESSION['user_id'] = 1; // Change this to a valid coach ID
$_SESSION['role'] = 'Coach';

// Create test data
$testData = [
    'SessionType' => 'Coaching',
    'SessionMode' => 'Private',
    'Name' => 'Test Session ' . date('Y-m-d H:i:s'),
    'Date' => '2025-10-25',
    'StartTime' => '09:00',
    'EndTime' => '11:00',
    'Location' => 'Test Location',
    'MaxParticipants' => 10,
    'PricePerSession' => 50.00,
    'IsRecurring' => true
];

echo "<h2>Testing Session Creation</h2>";
echo "<h3>Input Data:</h3>";
echo "<pre>" . print_r($testData, true) . "</pre>";

// Map to model format
$mappedData = [
    'coach_id' => $_SESSION['user_id'],
    'session_type' => $testData['SessionType'],
    'session_mode' => $testData['SessionMode'],
    'title' => $testData['Name'],
    'session_date' => $testData['Date'],
    'start_time' => $testData['StartTime'],
    'end_time' => $testData['EndTime'],
    'location' => $testData['Location'],
    'max_participants' => $testData['MaxParticipants'],
    'price' => $testData['PricePerSession'],
    'is_recurring' => $testData['IsRecurring'],
    'facility_type' => '',
    'facility_number' => 0,
    'recurrence_pattern' => 'None',
    'recurrence_end' => null
];

echo "<h3>Mapped Data for Model:</h3>";
echo "<pre>" . print_r($mappedData, true) . "</pre>";

// Load database
$db = new Database();

// Test database connection
try {
    echo "<h3>Testing Database Connection...</h3>";
    $db->query("SELECT 1");
    echo "✅ Database connection successful<br>";
    
    // Check if Session table exists
    echo "<h3>Checking Session Table...</h3>";
    $db->query("DESCRIBE Session");
    $db->execute();
    echo "✅ Session table exists<br>";
    
    // Load model
    echo "<h3>Loading M_Session Model...</h3>";
    require_once '../app/models/M_Session.php';
    $sessionModel = new M_Session();
    echo "✅ Model loaded<br>";
    
    // Try to create session
    echo "<h3>Creating Session...</h3>";
    $sessionId = $sessionModel->createSession($mappedData);
    
    if ($sessionId) {
        echo "✅ <strong>SUCCESS!</strong> Session created with ID: " . $sessionId . "<br>";
        
        // Verify in database
        $db->query("SELECT * FROM Session WHERE SessionID = :id");
        $db->bind(':id', $sessionId);
        $result = $db->single();
        
        echo "<h3>Session Data from Database:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    } else {
        echo "❌ <strong>FAILED!</strong> Could not create session<br>";
        echo "<h3>Checking for errors...</h3>";
        
        // Try to get PDO error info
        $db->query("SELECT * FROM Session ORDER BY SessionID DESC LIMIT 1");
        $lastSession = $db->single();
        echo "<h4>Last session in database:</h4>";
        echo "<pre>" . print_r($lastSession, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='/coach/sessions'>← Back to Sessions Page</a></p>";
?>
