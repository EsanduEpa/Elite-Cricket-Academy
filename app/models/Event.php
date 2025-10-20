<?php
class Event {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get upcoming events from database
    public function getUpcomingEvents($limit = 10) {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type,
            Description as description,
            Location as location,
            Status,
            Category,
            EndDate,
            RegistrationFee,
            MaxParticipants,
            PrimaryContact,
            ContactEmail,
            ContactPhone
        FROM Event 
        WHERE Status IN ("upcoming", "registration_open") 
            AND StartDate >= NOW()
        ORDER BY StartDate ASC 
        LIMIT :limit');
        
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event; // Convert stdClass to array
            // Add lowercase event_type with underscores for frontend compatibility
            $eventArray['event_type'] = (isset($eventArray['Type']) && !is_null($eventArray['Type']) && $eventArray['Type'] !== '')
                ? strtolower(str_replace(' ', '_', $eventArray['Type'])) 
                : '';
            $formatted[] = $eventArray;
        }
        
        return $formatted;
    }

    // Get past events from database
    public function getPastEvents($limit = 10) {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type,
            Description as description,
            Location as location,
            Status,
            Category,
            EndDate,
            RegistrationFee,
            MaxParticipants,
            PrimaryContact,
            ContactEmail,
            ContactPhone
        FROM Event 
        WHERE (Status = "completed" OR EndDate < NOW())
        ORDER BY StartDate DESC 
        LIMIT :limit');
        
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event; // Convert stdClass to array
            $eventArray['event_type'] = (isset($eventArray['Type']) && !is_null($eventArray['Type']) && $eventArray['Type'] !== '')
                ? strtolower(str_replace(' ', '_', $eventArray['Type']))
                : '';
            // Ensure Status is set to completed for past events
            if (isset($eventArray['Status']) && $eventArray['Status'] != 'cancelled') {
                $eventArray['Status'] = 'completed';
            }
            $formatted[] = $eventArray;
        }
        
        return $formatted;
    }

    // Get recent events (for dashboard)
    public function getRecentEvents($limit = 5) {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type,
            Description as description,
            Location as location,
            Status
        FROM Event 
        ORDER BY EventID DESC 
        LIMIT :limit');
        
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event; // Convert stdClass to array
            $eventArray['event_type'] = isset($eventArray['Type']) && $eventArray['Type']
                ? strtolower(str_replace(' ', '_', $eventArray['Type']))
                : '';
            $formatted[] = $eventArray;
        }
        
        return $formatted;
    }

    // Get today's active events
    public function getTodayActiveEvents() {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type,
            Description as description,
            Location as location,
            Status
        FROM Event 
        WHERE DATE(StartDate) = CURDATE()
            AND Status IN ("upcoming", "registration_open", "ongoing")
        ORDER BY StartDate ASC');
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event;
            $eventArray['event_type'] = (isset($eventArray['Type']) && !is_null($eventArray['Type']) && $eventArray['Type'] !== '')
                ? strtolower(str_replace(' ', '_', $eventArray['Type']))
                : '';
            $formatted[] = $eventArray;
        }
        
        return $formatted;
    }

    // Get all events
    public function getAllEvents() {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type,
            Description as description,
            Location as location,
            Status
        FROM Event 
        ORDER BY StartDate DESC');
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and add event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event;
            $eventArray['event_type'] = (isset($eventArray['Type']) && !is_null($eventArray['Type']) && $eventArray['Type'] !== '')
                ? strtolower(str_replace(' ', '_', $eventArray['Type']))
                : '';
            $formatted[] = $eventArray;
        }
        
        return $formatted;
    }

    // Get events for calendar (all future events)
    public function getCalendarEvents() {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as start,
            EndDate as end,
            Type,
            Description,
            Location,
            Status,
            Category
        FROM Event 
        WHERE StartDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY StartDate ASC');
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays
        $formatted = [];
        foreach ($results as $event) {
            $formatted[] = (array)$event;
        }
        
        return $formatted;
    }

    // Get event by ID from database
    public function getEventById($id) {
        error_log("===getEventById() called with ID: $id===");
        
        $this->db->query('SELECT 
            EventID,
            Name,
            Type,
            Category,
            Description,
            StartDate,
            EndDate,
            Location,
            Status,
            RegistrationStart,
            RegistrationEnd,
            MaxParticipants,
            RegistrationFee,
            PrimaryContact,
            ContactEmail,
            ContactPhone
        FROM Event 
        WHERE EventID = :id');
        
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        
        if (!$result) {
            error_log("Event not found for ID: $id");
            return null;
        }
        
        // Convert stdClass to clean associative array using JSON encode/decode
        $eventArray = json_decode(json_encode($result), true);
        error_log("getEventById() keys: " . implode(', ', array_keys($eventArray)));
        
        return $eventArray;
    }

    // Update event in database
    public function updateEvent($data) {
        // DEBUG: Log incoming data
        error_log("=== updateEvent() called ===");
        error_log("Data received: " . print_r($data, true));
        
        $this->db->query('UPDATE Event SET
            Name = :name,
            Type = :type,
            Category = :category,
            Description = :description,
            StartDate = :start_date,
            EndDate = :end_date,
            Location = :location,
            Status = :status,
            MaxParticipants = :max_participants,
            RegistrationFee = :registration_fee,
            RegistrationStart = :registration_start,
            RegistrationEnd = :registration_end,
            PrimaryContact = :primary_contact,
            ContactEmail = :contact_email,
            ContactPhone = :contact_phone
        WHERE EventID = :id');

        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':max_participants', $data['max_participants']);
        $this->db->bind(':registration_fee', $data['registration_fee']);
        $this->db->bind(':registration_start', $data['registration_start']);
        $this->db->bind(':registration_end', $data['registration_end']);
        $this->db->bind(':primary_contact', $data['primary_contact']);
        $this->db->bind(':contact_email', $data['contact_email']);
        $this->db->bind(':contact_phone', $data['contact_phone']);

        // DEBUG: Log bound parameters
        error_log("Bound values:");
        error_log("  ID: " . $data['id']);
        error_log("  Name: " . $data['name']);
        error_log("  Start Date: " . $data['start_date']);
        error_log("  End Date: " . $data['end_date']);

        // Execute
        try {
            if ($this->db->execute()) {
                error_log("✅ Event updated successfully - EventID: " . $data['id']);
                return true;
            } else {
                error_log("❌ Event update failed - execute() returned false");
                // Try to get PDO error info
                if (method_exists($this->db, 'getError')) {
                    error_log("Database error: " . $this->db->getError());
                }
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Event update exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // Delete event from database
    public function deleteEvent($id) {
        $this->db->query('DELETE FROM Event WHERE EventID = :id');
        $this->db->bind(':id', $id);

        try {
            if ($this->db->execute()) {
                error_log("Event deleted successfully");
                return true;
            } else {
                error_log("Event deletion failed");
                return false;
            }
        } catch (Exception $e) {
            error_log("Event deletion error: " . $e->getMessage());
            return false;
        }
    }

    // Get total events count from database
    public function getTotalEvents() {
        $this->db->query('SELECT COUNT(*) as total FROM Event');
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    // Create new event - REAL DATABASE VERSION
    public function createEvent($data) {
        // Prepare the SQL statement with all fields from the updated Event table
        $this->db->query('INSERT INTO Event (
            Name, 
            Type, 
            Category, 
            Description, 
            StartDate, 
            EndDate, 
            Location, 
            Status, 
            MaxParticipants,
            RegistrationFee,
            RegistrationStart,
            RegistrationEnd,
            PrimaryContact,
            ContactEmail,
            ContactPhone
        ) VALUES (
            :name,
            :type,
            :category,
            :description,
            :start_date,
            :end_date,
            :location,
            :status,
            :max_participants,
            :registration_fee,
            :registration_start,
            :registration_end,
            :primary_contact,
            :contact_email,
            :contact_phone
        )');

        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':max_participants', $data['max_participants']);
        $this->db->bind(':registration_fee', $data['registration_fee']);
        $this->db->bind(':registration_start', $data['registration_start']);
        $this->db->bind(':registration_end', $data['registration_end']);
        $this->db->bind(':primary_contact', $data['primary_contact']);
        $this->db->bind(':contact_email', $data['contact_email']);
        $this->db->bind(':contact_phone', $data['contact_phone']);

        // Execute
        try {
            if ($this->db->execute()) {
                error_log("Event created successfully");
                return true;
            } else {
                error_log("Event creation failed - execute returned false");
                return false;
            }
        } catch (Exception $e) {
            error_log("Event creation error: " . $e->getMessage());
            return false;
        }
    }
}

