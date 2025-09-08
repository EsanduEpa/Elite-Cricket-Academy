<?php
class Event {
    private $db;

    public function __construct() {
        // Initialize database but don't use it for interface demo
        $this->db = new Database();
    }

    // Get upcoming events - INTERFACE DEMO VERSION (No Database)
    public function getUpcomingEvents($limit = 5) {
        // Return sample data only - no database calls
        $sampleEvents = [
            [
                'id' => 1,
                'title' => 'Junior Cricket Championship',
                'event_date' => '2025-09-15',
                'event_type' => 'tournament',
                'description' => 'Annual junior cricket championship for under-16 players',
                'location' => 'Main Cricket Ground',
                'status' => 'upcoming'
            ],
            [
                'id' => 2,
                'title' => 'Advanced Batting Workshop',
                'event_date' => '2025-09-12',
                'event_type' => 'training',
                'description' => 'Specialized batting techniques workshop by senior coach',
                'location' => 'Practice Nets Area',
                'status' => 'upcoming'
            ],
            [
                'id' => 3,
                'title' => 'Inter-Academy Friendly Match',
                'event_date' => '2025-09-18',
                'event_type' => 'match',
                'description' => 'Friendly match against City Sports Academy',
                'location' => 'Stadium Ground',
                'status' => 'upcoming'
            ],
            [
                'id' => 4,
                'title' => 'Fielding Skills Training',
                'event_date' => '2025-09-20',
                'event_type' => 'training',
                'description' => 'Intensive fielding practice session',
                'location' => 'Training Ground',
                'status' => 'upcoming'
            ],
            [
                'id' => 5,
                'title' => 'Weekly Cricket Tournament',
                'event_date' => '2025-09-25',
                'event_type' => 'tournament',
                'description' => 'Weekly tournament for all skill levels',
                'location' => 'Main Ground',
                'status' => 'upcoming'
            ],
            [
                'id' => 6,
                'title' => 'Bowling Masterclass',
                'event_date' => '2025-09-28',
                'event_type' => 'workshop',
                'description' => 'Professional bowling techniques masterclass',
                'location' => 'Indoor Training Center',
                'status' => 'upcoming'
            ]
        ];
        
        // Return limited results based on the limit parameter
        return array_slice($sampleEvents, 0, $limit);
    }

    // Get today's active events - INTERFACE DEMO VERSION (No Database)
    public function getTodayActiveEvents() {
        // Return sample data for today's events
        return [
            [
                'id' => 2,
                'title' => 'Morning Training Session',
                'event_date' => '2025-09-06',
                'event_type' => 'training',
                'description' => 'Daily morning training for academy players',
                'location' => 'Practice Nets',
                'status' => 'active'
            ]
        ];
    }

    // Get all events - INTERFACE DEMO VERSION (No Database)
    public function getAllEvents() {
        return array_merge($this->getUpcomingEvents(50), $this->getPastEvents(50));
    }

    // Add event - INTERFACE DEMO VERSION (No Database)
    public function addEvent($data) {
        // Simulate successful event creation
        return true;
    }

    // Get event by ID - INTERFACE DEMO VERSION (No Database)
    public function getEventById($id) {
        // Return sample event data based on ID
        $sampleEvents = [
            1 => [
                'id' => 1,
                'title' => 'Junior Cricket Championship',
                'event_date' => '2025-09-15',
                'event_type' => 'tournament',
                'description' => 'Annual junior cricket championship for under-16 players',
                'location' => 'Main Cricket Ground',
                'status' => 'upcoming'
            ],
            2 => [
                'id' => 2,
                'title' => 'Advanced Batting Workshop',
                'event_date' => '2025-09-12',
                'event_type' => 'training',
                'description' => 'Specialized batting techniques workshop by senior coach',
                'location' => 'Practice Nets Area',
                'status' => 'upcoming'
            ]
        ];
        
        return isset($sampleEvents[$id]) ? $sampleEvents[$id] : $sampleEvents[1];
    }

    // Update event - INTERFACE DEMO VERSION (No Database)
    public function updateEvent($data) {
        // Simulate successful update
        return true;
    }

    // Delete event - INTERFACE DEMO VERSION (No Database)
    public function deleteEvent($id) {
        // Simulate successful deletion
        return true;
    }

    // Get total events count - INTERFACE DEMO VERSION (No Database)
    public function getTotalEvents() {
        return 45; // Sample total count
    }

    // Get past events - INTERFACE DEMO VERSION (No Database)
    public function getPastEvents($limit = 10) {
        // Return sample past events
        $samplePastEvents = [
            [
                'id' => 11,
                'title' => 'Summer Cricket Camp 2025',
                'event_date' => '2025-08-15',
                'event_type' => 'training',
                'description' => 'Intensive summer training camp for young cricketers',
                'location' => 'Academy Grounds',
                'status' => 'completed'
            ],
            [
                'id' => 12,
                'title' => 'Regional Championship Final',
                'event_date' => '2025-07-28',
                'event_type' => 'tournament',
                'description' => 'Regional championship final match',
                'location' => 'Central Stadium',
                'status' => 'completed'
            ],
            [
                'id' => 13,
                'title' => 'Bowling Workshop',
                'event_date' => '2025-07-20',
                'event_type' => 'training',
                'description' => 'Advanced bowling techniques workshop',
                'location' => 'Practice Nets',
                'status' => 'completed'
            ],
            [
                'id' => 14,
                'title' => 'Youth Cricket Festival',
                'event_date' => '2025-07-10',
                'event_type' => 'tournament',
                'description' => 'Annual youth cricket festival with multiple teams',
                'location' => 'Sports Complex',
                'status' => 'completed'
            ],
            [
                'id' => 15,
                'title' => 'Wicket Keeping Clinic',
                'event_date' => '2025-06-25',
                'event_type' => 'workshop',
                'description' => 'Specialized wicket keeping skills clinic',
                'location' => 'Indoor Training Center',
                'status' => 'completed'
            ]
        ];
        
        return array_slice($samplePastEvents, 0, $limit);
    }

    // Get recent events for dashboard - INTERFACE DEMO VERSION (No Database)
    public function getRecentEvents($limit = 5) {
        // Return mix of recent upcoming and past events
        $recentEvents = [
            [
                'id' => 1,
                'title' => 'Junior Cricket Championship',
                'event_date' => '2025-09-15',
                'event_type' => 'tournament',
                'description' => 'Annual junior cricket championship',
                'location' => 'Main Ground',
                'status' => 'upcoming'
            ],
            [
                'id' => 2,
                'title' => 'Advanced Batting Workshop',
                'event_date' => '2025-09-12',
                'event_type' => 'training',
                'description' => 'Specialized batting techniques workshop',
                'location' => 'Practice Nets',
                'status' => 'upcoming'
            ],
            [
                'id' => 11,
                'title' => 'Summer Cricket Camp 2025',
                'event_date' => '2025-08-15',
                'event_type' => 'training',
                'description' => 'Intensive summer training camp',
                'location' => 'Academy Grounds',
                'status' => 'completed'
            ]
        ];
        
        return array_slice($recentEvents, 0, $limit);
    }

    // Create new event (interface method) - INTERFACE DEMO VERSION (No Database)
    public function createEvent($data) {
        // Simulate successful creation
        return true;
    }

    // Get calendar events (formatted for calendar display) - INTERFACE DEMO VERSION (No Database)
    public function getCalendarEvents() {
        $upcoming = $this->getUpcomingEvents(20);
        $past = $this->getPastEvents(20);
        $allEvents = array_merge($upcoming, $past);
        
        $calendarEvents = [];
        foreach ($allEvents as $event) {
            $calendarEvents[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['event_date'],
                'className' => 'event-' . $event['event_type'],
                'extendedProps' => [
                    'description' => $event['description'],
                    'location' => $event['location'],
                    'type' => $event['event_type']
                ]
            ];
        }
        
        return $calendarEvents;
    }
}
?>
