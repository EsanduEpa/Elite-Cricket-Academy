<?php
require_once 'app/bootloader.php';

// Create database connection
$db = new Database();

// Test query
$db->query('SELECT 
    EventID as id,
    Name as title,
    StartDate as event_date,
    Type as event_type,
    Category,
    Description as description,
    Location as location,
    Status as status
FROM Event 
WHERE StartDate >= NOW() 
AND Status IN ("upcoming", "scheduled")
ORDER BY StartDate ASC 
LIMIT 10');

$results = $db->resultSet();

echo "Query Results:\n";
echo "Count: " . count($results) . "\n\n";

if ($results) {
    foreach ($results as $event) {
        print_r($event);
        echo "\n---\n";
    }
} else {
    echo "No results or error\n";
}
