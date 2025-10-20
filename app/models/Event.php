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
            Type as event_type,
            Description as description,
            Location as location,
            Status as status,
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
        LIMIT ' . (int)$limit);
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event; // Convert stdClass to array
            $eventArray['event_type'] = strtolower(str_replace(' ', '_', $eventArray['event_type']));
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
            Type as event_type,
            Description as description,
            Location as location,
            Status as status,
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
        LIMIT ' . (int)$limit);
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event; // Convert stdClass to array
            $eventArray['event_type'] = strtolower(str_replace(' ', '_', $eventArray['event_type']));
            // Ensure status is set to completed for past events
            if ($eventArray['status'] != 'cancelled') {
                $eventArray['status'] = 'completed';
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
            Type as event_type,
            Description as description,
            Location as location,
            Status as status
        FROM Event 
        ORDER BY EventID DESC 
        LIMIT ' . (int)$limit);
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays and format event_type
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event; // Convert stdClass to array
            $eventArray['event_type'] = strtolower(str_replace(' ', '_', $eventArray['event_type']));
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
            Type as event_type,
            Description as description,
            Location as location,
            Status as status
        FROM Event 
        WHERE DATE(StartDate) = CURDATE()
            AND Status IN ("upcoming", "registration_open", "ongoing")
        ORDER BY StartDate ASC');
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays
        $formatted = [];
        foreach ($results as $event) {
            $formatted[] = (array)$event;
        }
        
        return $formatted;
    }

    // Get all events
    public function getAllEvents() {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type as event_type,
            Description as description,
            Location as location,
            Status as status
        FROM Event 
        ORDER BY StartDate DESC');
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays
        $formatted = [];
        foreach ($results as $event) {
            $formatted[] = (array)$event;
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
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            EndDate,
            Type as event_type,
            Category,
            Description as description,
            Location as location,
            Status as status,
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
        
        return $result ? (array)$result : null;
    }

    // Update event in database
    public function updateEvent($data) {
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

        // Execute
        try {
            if ($this->db->execute()) {
                error_log("Event updated successfully");
                return true;
            } else {
                error_log("Event update failed");
                return false;
            }
        } catch (Exception $e) {
            error_log("Event update error: " . $e->getMessage());
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

    // Create new event in database
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
