<?php
// Direct test of event creation logic
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Direct Event Creation Test</h1>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;}
.success{color:green;}.error{color:red;}.info{color:blue;}</style>";

// Start session
session_start();

// Set fake admin session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'Admin';

echo "<div class='info'><h2>1. Session Check</h2>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
echo "User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "</div>";

// Load framework
require_once '../app/bootloader.php';

echo "<div class='info'><h2>2. Framework Loaded</h2>";
echo "✓ Bootloader loaded successfully</div>";

// Simulate POST data
$_POST = [
    'event_name' => 'Direct Test Event ' . date('H:i:s'),
    'event_type' => 'Match',
    'event_category' => 'senior',
    'event_venue' => 'Test Ground',
    'event_description' => 'This is a direct test to verify event creation works',
    'start_date' => '2025-11-01',
    'start_time' => '10:00',
    'end_date' => '2025-11-01',
    'end_time' => '18:00',
    'max_participants' => '50',
    'registration_fee' => '500',
    'event_status' => 'upcoming'
];

echo "<div class='info'><h2>3. POST Data Prepared</h2>";
echo "<pre>" . print_r($_POST, true) . "</pre></div>";

// Process data like controller does
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$startDateTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
$endDateTime = $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00';

$eventData = [
    'name' => trim($_POST['event_name']),
    'type' => $_POST['event_type'],
    'category' => $_POST['event_category'],
    'description' => trim($_POST['event_description']),
    'start_date' => $startDateTime,
    'end_date' => $endDateTime,
    'location' => trim($_POST['event_venue']),
    'organized_by' => $_SESSION['user_id'],
    'status' => $_POST['event_status'] ?? 'upcoming',
    'max_participants' => !empty($_POST['max_participants']) ? intval($_POST['max_participants']) : null,
    'registration_fee' => !empty($_POST['registration_fee']) ? floatval($_POST['registration_fee']) : 0.00,
    'registration_start' => null,
    'registration_end' => null,
    'primary_contact' => null,
    'contact_email' => null,
    'contact_phone' => null,
    'secondary_contact' => null,
    'secondary_email' => null,
    'secondary_phone' => null,
    'event_coordinator' => null,
    'special_requirements' => null
];

echo "<div class='info'><h2>4. Event Data Processed</h2>";
echo "<pre>" . print_r($eventData, true) . "</pre></div>";

// Validate required fields
if (empty($eventData['name']) || empty($eventData['type']) || empty($eventData['category']) || 
    empty($eventData['location']) || empty($eventData['start_date']) || empty($eventData['end_date'])) {
    echo "<div class='error'><h2>5. Validation Failed</h2>";
    echo "Missing required fields!</div>";
    exit;
}

echo "<div class='success'><h2>5. Validation Passed</h2>✓ All required fields present</div>";

// Try to create event
echo "<div class='info'><h2>6. Creating Event...</h2>";

try {
    $eventModel = new Event();
    echo "✓ Event model instantiated<br>";
    
    $result = $eventModel->createEvent($eventData);
    
    if ($result) {
        echo "<div class='success'><h2>7. SUCCESS!</h2>";
        echo "✓ Event created successfully!<br>";
        echo "Event name: <strong>" . $eventData['name'] . "</strong></div>";
        
        // Verify in database
        $db = new Database();
        $db->query("SELECT * FROM Event ORDER BY EventID DESC LIMIT 1");
        $latestEvent = $db->single();
        
        if ($latestEvent) {
            echo "<div class='success'><h2>8. Database Verification</h2>";
            echo "<pre>" . print_r($latestEvent, true) . "</pre></div>";
        }
    } else {
        echo "<div class='error'><h2>7. FAILED</h2>";
        echo "✗ createEvent() returned false</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'><h2>7. EXCEPTION</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
}

echo "<hr><h2>Test Complete</h2>";
echo "<p><a href='http://localhost/Elite/admin/events'>View Events Page</a></p>";
echo "<p><a href='http://localhost/Elite/admin/create_event'>Create Event Wizard</a></p>";
?>
