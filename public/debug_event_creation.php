<?php
// Debug script to test event creation POST data
session_start();

// Simulate logged in admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'Admin';

echo "<h2>Event Creation Debug</h2>";
echo "<hr>";

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>✓ POST Request Received</h3>";
    echo "<pre>";
    echo "POST Data:\n";
    print_r($_POST);
    echo "</pre>";
    
    // Test the actual creation process
    require_once '../app/bootloader.php';
    
    echo "<h3>Testing Event Creation...</h3>";
    
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
    // Merge date and time fields
    $startDateTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
    $endDateTime = $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00';
    
    $registrationStart = !empty($_POST['registration_start']) ? 
        str_replace('T', ' ', $_POST['registration_start']) . ':00' : null;
    $registrationEnd = !empty($_POST['registration_end']) ? 
        str_replace('T', ' ', $_POST['registration_end']) . ':00' : null;
    
    $eventData = [
        'name' => trim($_POST['event_name']),
        'type' => $_POST['event_type'],
        'category' => $_POST['event_category'],
        'description' => trim($_POST['event_description']),
        'start_date' => $startDateTime,
        'end_date' => $endDateTime,
        'location' => trim($_POST['event_venue']),
        'organized_by' => $_SESSION['user_id'],
        'status' => isset($_POST['event_status']) ? $_POST['event_status'] : 'upcoming',
        'max_participants' => !empty($_POST['max_participants']) ? intval($_POST['max_participants']) : null,
        'registration_fee' => !empty($_POST['registration_fee']) ? floatval($_POST['registration_fee']) : 0.00,
        'registration_start' => $registrationStart,
        'registration_end' => $registrationEnd,
        'primary_contact' => !empty($_POST['primary_contact']) ? trim($_POST['primary_contact']) : null,
        'contact_email' => !empty($_POST['contact_email']) ? trim($_POST['contact_email']) : null,
        'contact_phone' => !empty($_POST['contact_phone']) ? trim($_POST['contact_phone']) : null,
        'secondary_contact' => !empty($_POST['secondary_contact']) ? trim($_POST['secondary_contact']) : null,
        'secondary_email' => !empty($_POST['secondary_email']) ? trim($_POST['secondary_email']) : null,
        'secondary_phone' => !empty($_POST['secondary_phone']) ? trim($_POST['secondary_phone']) : null,
        'event_coordinator' => !empty($_POST['event_coordinator']) ? intval($_POST['event_coordinator']) : null,
        'special_requirements' => !empty($_POST['special_requirements']) ? trim($_POST['special_requirements']) : null
    ];
    
    echo "<h4>Processed Event Data:</h4>";
    echo "<pre>";
    print_r($eventData);
    echo "</pre>";
    
    // Try to create the event
    try {
        $eventModel = new Event();
        $result = $eventModel->createEvent($eventData);
        
        if ($result) {
            echo "<h3 style='color: green;'>✓ Event Created Successfully!</h3>";
            
            // Get the latest event
            $db = new Database();
            $db->query("SELECT * FROM Event ORDER BY EventID DESC LIMIT 1");
            $event = $db->single();
            
            echo "<h4>Created Event:</h4>";
            echo "<pre>";
            print_r($event);
            echo "</pre>";
        } else {
            echo "<h3 style='color: red;'>✗ Failed to Create Event</h3>";
        }
    } catch (Exception $e) {
        echo "<h3 style='color: red;'>✗ Error: " . $e->getMessage() . "</h3>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} else {
    echo "<h3>No POST Data</h3>";
    echo "<p>This script should receive POST data from the event wizard form.</p>";
    echo "<p>Form action should be: <code>/admin/create_event</code></p>";
}
?>
