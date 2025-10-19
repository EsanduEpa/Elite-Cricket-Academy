<?php
// Test script to check event data retrieval
require_once 'app/bootloader.php';

// Create Event model instance
require_once 'app/models/Event.php';
$eventModel = new Event();

echo "<h1>Event Data Debug</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } pre { background: #f5f5f5; padding: 10px; border-radius: 5px; } h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }</style>";

// Test 1: Get Upcoming Events
echo "<h2>1. Upcoming Events</h2>";
$upcomingEvents = $eventModel->getUpcomingEvents(10);
echo "<p><strong>Count:</strong> " . count($upcomingEvents) . "</p>";
echo "<pre>" . print_r($upcomingEvents, true) . "</pre>";

// Test 2: Get Past Events
echo "<h2>2. Past Events</h2>";
$pastEvents = $eventModel->getPastEvents(10);
echo "<p><strong>Count:</strong> " . count($pastEvents) . "</p>";
echo "<pre>" . print_r($pastEvents, true) . "</pre>";

// Test 3: Get Calendar Events
echo "<h2>3. Calendar Events (JSON format)</h2>";
$calendarEvents = $eventModel->getCalendarEvents();
echo "<p><strong>Count:</strong> " . count($calendarEvents) . "</p>";
echo "<pre>" . print_r($calendarEvents, true) . "</pre>";

// Test 4: Direct Database Query
echo "<h2>4. Direct Database Query</h2>";
require_once 'app/libraries/Database.php';
$db = new Database();
$db->query('SELECT 
    EventID as id,
    Name as title,
    StartDate as event_date,
    Type as event_type,
    Description as description,
    Location as location,
    Status as status
FROM Event 
WHERE StartDate >= NOW() 
ORDER BY StartDate ASC 
LIMIT 10');

$directResults = $db->resultSet();
echo "<p><strong>Count:</strong> " . count($directResults) . "</p>";
echo "<pre>" . print_r($directResults, true) . "</pre>";

// Test 5: Check current time
echo "<h2>5. Time Check</h2>";
echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Database NOW():</strong> ";
$db->query('SELECT NOW() as current_time');
$timeResult = $db->single();
echo $timeResult->current_time . "</p>";

// Test 6: All events in database
echo "<h2>6. All Events in Database</h2>";
$db->query('SELECT EventID, Name, StartDate, Status FROM Event ORDER BY StartDate');
$allEvents = $db->resultSet();
echo "<p><strong>Total Events:</strong> " . count($allEvents) . "</p>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Start Date</th><th>Status</th><th>Is Future?</th></tr>";
foreach ($allEvents as $event) {
    $isFuture = (strtotime($event->StartDate) > time()) ? '✅ YES' : '❌ NO';
    echo "<tr>";
    echo "<td>{$event->EventID}</td>";
    echo "<td>{$event->Name}</td>";
    echo "<td>{$event->StartDate}</td>";
    echo "<td>{$event->Status}</td>";
    echo "<td>{$isFuture}</td>";
    echo "</tr>";
}
echo "</table>";
?>
