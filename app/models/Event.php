<?php
class Event {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get upcoming events
    public function getUpcomingEvents($limit = 5) {
        $this->db->query('SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        $results = $this->db->resultSet();
        
        // Return dummy data if no database results
        if (empty($results)) {
            return [
                [
                    'id' => 1,
                    'title' => 'Junior Cricket Tournament',
                    'event_date' => '2025-09-15',
                    'event_type' => 'tournament',
                    'description' => 'Annual junior cricket championship',
                    'location' => 'Main Ground'
                ],
                [
                    'id' => 2,
                    'title' => 'Batting Coaching Session',
                    'event_date' => '2025-09-10',
                    'event_type' => 'training',
                    'description' => 'Advanced batting techniques workshop',
                    'location' => 'Practice Nets'
                ],
                [
                    'id' => 3,
                    'title' => 'Inter-Academy Match',
                    'event_date' => '2025-09-20',
                    'event_type' => 'match',
                    'description' => 'Friendly match with City Cricket Academy',
                    'location' => 'Stadium Ground'
                ]
            ];
        }
        
        return $results;
    }

    // Get today's active events
    public function getTodayActiveEvents() {
        $this->db->query('SELECT COUNT(*) as count FROM events WHERE DATE(event_date) = CURDATE()');
        $result = $this->db->single();
        
        return $result ? $result->count : 2; // Return dummy data if no database
    }

    // Get all events
    public function getAllEvents() {
        $this->db->query('SELECT * FROM events ORDER BY event_date DESC');
        return $this->db->resultSet();
    }

    // Add new event
    public function addEvent($data) {
        $this->db->query('INSERT INTO events (title, description, event_date, event_type, location, created_by) 
                         VALUES (:title, :description, :event_date, :event_type, :location, :created_by)');
        
        // Bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':event_date', $data['event_date']);
        $this->db->bind(':event_type', $data['event_type']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':created_by', $data['created_by']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get event by ID
    public function getEventById($id) {
        $this->db->query('SELECT * FROM events WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Update event
    public function updateEvent($data) {
        $this->db->query('UPDATE events SET 
                         title = :title,
                         description = :description,
                         event_date = :event_date,
                         event_type = :event_type,
                         location = :location
                         WHERE id = :id');

        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':event_date', $data['event_date']);
        $this->db->bind(':event_type', $data['event_type']);
        $this->db->bind(':location', $data['location']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete event
    public function deleteEvent($id) {
        $this->db->query('DELETE FROM events WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get total events count
    public function getTotalEvents() {
        $this->db->query('SELECT COUNT(*) as count FROM events');
        $result = $this->db->single();
        
        return $result ? $result->count : 5; // Return dummy data if no database
    }
}
?>
