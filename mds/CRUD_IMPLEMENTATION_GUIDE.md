# CRUD Implementation Guide - Elite Cricket Academy MVC Framework

**Version:** 1.0  
**Date:** October 20, 2025  
**Based on:** Events CRUD System (Fully Tested & Working)  
**Framework:** Custom PHP MVC (XAMPP, MySQL)  

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture Pattern](#architecture-pattern)
3. [File Structure](#file-structure)
4. [Step-by-Step Implementation](#step-by-step-implementation)
5. [Common Problems & Solutions](#common-problems--solutions)
6. [Best Practices](#best-practices)
7. [Testing Checklist](#testing-checklist)
8. [Code Templates](#code-templates)

---

## 🎯 Overview

This guide documents the complete CRUD (Create, Read, Update, Delete) implementation pattern used successfully in the Events system. Follow this exact pattern for implementing any new CRUD functionality.

### What This Guide Covers

✅ Complete MVC implementation pattern  
✅ Database integration (PDO)  
✅ Form handling (GET/POST)  
✅ Data validation  
✅ Error handling  
✅ Flash messages  
✅ Redirects and routing  
✅ Frontend integration  
✅ Common pitfalls and solutions  

---

## 🏗️ Architecture Pattern

### MVC Structure

```
┌─────────────────────────────────────────────────────────────┐
│                         USER BROWSER                         │
└───────────────────────────┬─────────────────────────────────┘
                            │
                    HTTP Request (GET/POST)
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│  ROUTER (Core.php)                                           │
│  - Parses URL: /controller/method/param                     │
│  - Instantiates Controller                                   │
└───────────────────────────┬─────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│  CONTROLLER (Admin.php, Coach.php, etc.)                    │
│  - Handles request logic                                     │
│  - Validates input                                           │
│  - Calls Model methods                                       │
│  - Prepares data for View                                    │
└───────────────────────────┬─────────────────────────────────┘
                            │
                    ┌───────┴────────┐
                    ▼                ▼
    ┌───────────────────┐  ┌────────────────────┐
    │  MODEL            │  │  VIEW              │
    │  (Event.php)      │  │  (events.php)      │
    │  - Database ops   │  │  - HTML rendering  │
    │  - Business logic │  │  - Display data    │
    │  - Data fetch     │  │  - Forms           │
    └─────────┬─────────┘  └────────────────────┘
              │
              ▼
    ┌─────────────────────┐
    │  DATABASE (MySQL)   │
    │  - cricket_academy  │
    │  - Event table      │
    └─────────────────────┘
```

---

## 📁 File Structure

For any CRUD entity (e.g., Events, Players, Coaches), you need:

```
Elite/
├── app/
│   ├── controllers/
│   │   └── Admin.php                    # Controller with CRUD methods
│   │
│   ├── models/
│   │   └── Event.php                    # Model with database operations
│   │
│   └── views/
│       └── admin/
│           ├── events.php               # List view (Read All)
│           ├── edit_event.php           # Edit form (Update)
│           └── create_event.php         # Create form (optional if using modal)
│
├── public/
│   ├── js/
│   │   └── admin/
│   │       ├── events.js                # JavaScript functionality
│   │       └── create-event-wizard.js   # Modal/wizard logic
│   │
│   └── css/
│       └── admin/
│           └── events.css               # Styling
│
└── cricket_academy (database)
    └── Event (table)                    # Database table
```

---

## 🚀 Step-by-Step Implementation

### PHASE 1: Database Setup

#### Step 1.1: Create Database Table

**File:** Create SQL file (e.g., `create_events_table.sql`)

```sql
CREATE TABLE Event (
    EventID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    Type VARCHAR(100),
    Category VARCHAR(50),
    Description TEXT,
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    Location VARCHAR(255),
    Status ENUM('upcoming', 'registration_open', 'registration_closed', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    RegistrationStart DATETIME,
    RegistrationEnd DATETIME,
    MaxParticipants INT,
    RegistrationFee DECIMAL(10,2),
    PrimaryContact VARCHAR(255),
    ContactEmail VARCHAR(255),
    ContactPhone VARCHAR(20),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Key Points:**
- ✅ Use singular table names (Event, not Events)
- ✅ Always include `ID` field (auto-increment)
- ✅ Use appropriate data types
- ✅ Add `CreatedAt`/`UpdatedAt` for tracking
- ✅ Use ENUM for fixed-value fields
- ✅ Set proper default values

---

### PHASE 2: Model Implementation

#### Step 2.1: Create Model File

**File:** `app/models/Event.php`

```php
<?php
class Event {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // ==================== READ OPERATIONS ====================
    
    /**
     * Get all events (with optional filtering)
     */
    public function getAllEvents($limit = null) {
        $sql = 'SELECT 
            EventID,
            Name,
            Type,
            Category,
            Description,
            StartDate,
            EndDate,
            Location,
            Status
        FROM Event 
        ORDER BY StartDate DESC';
        
        if ($limit) {
            $sql .= ' LIMIT :limit';
        }
        
        $this->db->query($sql);
        
        if ($limit) {
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($limit = 10) {
        $this->db->query('SELECT 
            EventID as id,
            Name as title,
            StartDate as event_date,
            Type,
            Description as description,
            Location as location,
            Status
        FROM Event 
        WHERE Status IN ("upcoming", "registration_open") 
            AND StartDate >= NOW()
        ORDER BY StartDate ASC 
        LIMIT :limit');
        
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $results = $this->db->resultSet();
        
        // Format for frontend
        $formatted = [];
        foreach ($results as $event) {
            $eventArray = (array)$event;
            $eventArray['event_type'] = isset($eventArray['Type']) 
                ? strtolower(str_replace(' ', '_', $eventArray['Type'])) 
                : '';
            $formatted[] = $eventArray;
        }
        
        return $formatted;
    }

    /**
     * Get single event by ID
     */
    public function getEventById($id) {
        error_log("getEventById() called with ID: $id");
        
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
        
        // Convert stdClass to associative array
        $eventArray = json_decode(json_encode($result), true);
        error_log("Event fetched successfully: " . $eventArray['Name']);
        
        return $eventArray;
    }

    // ==================== CREATE OPERATION ====================
    
    /**
     * Create new event
     */
    public function createEvent($data) {
        error_log("=== createEvent() called ===");
        error_log("Data: " . print_r($data, true));
        
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

        // Bind all values
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
                error_log("✅ Event created successfully");
                return true;
            } else {
                error_log("❌ Event creation failed");
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Event creation exception: " . $e->getMessage());
            return false;
        }
    }

    // ==================== UPDATE OPERATION ====================
    
    /**
     * Update existing event
     */
    public function updateEvent($data) {
        error_log("=== updateEvent() called ===");
        error_log("Data: " . print_r($data, true));
        
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

        // Bind all values
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
                error_log("✅ Event updated successfully - ID: " . $data['id']);
                return true;
            } else {
                error_log("❌ Event update failed");
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Event update exception: " . $e->getMessage());
            return false;
        }
    }

    // ==================== DELETE OPERATION ====================
    
    /**
     * Delete event
     */
    public function deleteEvent($id) {
        error_log("deleteEvent() called with ID: $id");
        
        $this->db->query('DELETE FROM Event WHERE EventID = :id');
        $this->db->bind(':id', $id);

        try {
            if ($this->db->execute()) {
                error_log("✅ Event deleted successfully");
                return true;
            } else {
                error_log("❌ Event deletion failed");
                return false;
            }
        } catch (Exception $e) {
            error_log("❌ Event deletion exception: " . $e->getMessage());
            return false;
        }
    }
}
```

**Key Points:**
- ✅ Use prepared statements (PDO) - prevents SQL injection
- ✅ Always use `error_log()` for debugging, never `echo`
- ✅ Return boolean for success/failure
- ✅ Use try-catch for database operations
- ✅ Convert stdClass to array for consistency
- ✅ Add descriptive comments for each method

---

### PHASE 3: Controller Implementation

#### Step 3.1: Create Controller Methods

**File:** `app/controllers/Admin.php`

```php
<?php
class Admin extends Controller {
    
    public function __construct() {
        error_log("Admin controller constructor called");
        // Check authentication for all admin pages
        requireAuth(['Admin']);
    }
    
    // ==================== READ (List All) ====================
    
    public function events() {
        $eventModel = $this->model('Event');
        
        // Fetch data
        $upcomingEvents = $eventModel->getUpcomingEvents(10);
        $pastEvents = $eventModel->getPastEvents(10);
        
        // Get statistics
        $eventStats = [
            'totalEvents' => $eventModel->getTotalEventsCount(),
            'upcomingCount' => count($upcomingEvents),
            'tournaments' => $eventModel->getTournamentsCount()
        ];
        
        // Prepare data for view
        $data = [
            'title' => 'Events Management - Elite Cricket Academy',
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'eventStats' => $eventStats
        ];
        
        // Load view
        $this->view('admin/events', $data);
    }
    
    // ==================== CREATE ====================
    
    public function create_event() {
        $eventModel = $this->model('Event');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Combine date and time fields
            $startDateTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
            $endDateTime = $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00';
            
            // Prepare data array
            $eventData = [
                'name' => trim($_POST['event_name']),
                'type' => $_POST['event_type'],
                'category' => $_POST['event_category'] ?? null,
                'description' => !empty($_POST['event_description']) ? trim($_POST['event_description']) : null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'location' => !empty($_POST['event_venue']) ? trim($_POST['event_venue']) : null,
                'status' => $_POST['event_status'] ?? 'upcoming',
                'max_participants' => !empty($_POST['max_participants']) ? intval($_POST['max_participants']) : null,
                'registration_fee' => !empty($_POST['registration_fee']) ? floatval($_POST['registration_fee']) : null,
                'registration_start' => !empty($_POST['registration_start']) ? $_POST['registration_start'] . ':00' : null,
                'registration_end' => !empty($_POST['registration_end']) ? $_POST['registration_end'] . ':00' : null,
                'primary_contact' => !empty($_POST['primary_contact']) ? trim($_POST['primary_contact']) : null,
                'contact_email' => !empty($_POST['contact_email']) ? trim($_POST['contact_email']) : null,
                'contact_phone' => !empty($_POST['contact_phone']) ? trim($_POST['contact_phone']) : null
            ];
            
            // Validate required fields
            if (empty($eventData['name']) || empty($eventData['type'])) {
                flash('event_message', '❌ Please fill all required fields', 'alert alert-danger');
                redirect('admin/events');
                return;
            }
            
            // Create event
            try {
                $result = $eventModel->createEvent($eventData);
                
                if ($result) {
                    flash('event_message', '✅ Event created successfully!', 'alert alert-success');
                    redirect('admin/events');
                } else {
                    flash('event_message', '❌ Failed to create event', 'alert alert-danger');
                    redirect('admin/events');
                }
            } catch (Exception $e) {
                error_log("Event creation exception: " . $e->getMessage());
                flash('event_message', '❌ Database error: ' . $e->getMessage(), 'alert alert-danger');
                redirect('admin/events');
            }
        } else {
            // GET request - show create form
            $data = [
                'title' => 'Create New Event - Elite Cricket Academy'
            ];
            $this->view('admin/create_event', $data);
        }
    }
    
    // ==================== READ (Single Item) ====================
    
    public function view_event($id) {
        $eventModel = $this->model('Event');
        $event = $eventModel->getEventById($id);
        
        if (!$event) {
            flash('event_message', '❌ Event not found', 'alert alert-danger');
            redirect('admin/events');
            return;
        }
        
        $data = [
            'title' => 'Event Details - ' . $event['Name'],
            'event' => $event
        ];
        
        $this->view('admin/view_event', $data);
    }
    
    // ==================== UPDATE ====================
    
    public function edit_event($id) {
        error_log("Admin::edit_event() called with ID: $id");
        
        $eventModel = $this->model('Event');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Combine date and time fields
            $startDateTime = $_POST['StartDate_date'] . ' ' . $_POST['StartTime'] . ':00';
            $endDateTime = $_POST['EndDate_date'] . ' ' . $_POST['EndTime'] . ':00';
            
            // Prepare event data
            $eventData = [
                'id' => $id,
                'name' => trim($_POST['Name']),
                'type' => $_POST['Type'],
                'category' => $_POST['Category'] ?? null,
                'description' => !empty($_POST['Description']) ? trim($_POST['Description']) : null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'location' => !empty($_POST['Location']) ? trim($_POST['Location']) : null,
                'status' => $_POST['Status'] ?? 'upcoming',
                'max_participants' => !empty($_POST['MaxParticipants']) ? intval($_POST['MaxParticipants']) : null,
                'registration_fee' => !empty($_POST['RegistrationFee']) ? floatval($_POST['RegistrationFee']) : null,
                'registration_start' => !empty($_POST['RegistrationStart']) ? $_POST['RegistrationStart'] . ':00' : null,
                'registration_end' => !empty($_POST['RegistrationEnd']) ? $_POST['RegistrationEnd'] . ':00' : null,
                'primary_contact' => !empty($_POST['PrimaryContact']) ? trim($_POST['PrimaryContact']) : null,
                'contact_email' => !empty($_POST['ContactEmail']) ? trim($_POST['ContactEmail']) : null,
                'contact_phone' => !empty($_POST['ContactPhone']) ? trim($_POST['ContactPhone']) : null
            ];
            
            // Update event
            $result = $eventModel->updateEvent($eventData);
            
            if ($result) {
                flash('event_message', '✅ Event updated successfully!', 'alert alert-success');
                redirect('admin/events');
            } else {
                flash('event_message', '❌ Failed to update event', 'alert alert-danger');
                redirect('admin/edit_event/' . $id);
            }
        } else {
            // GET request - Display edit form
            $event = $eventModel->getEventById($id);
            
            if (!$event) {
                flash('event_message', '❌ Event not found', 'alert alert-danger');
                redirect('admin/events');
                return;
            }
            
            $data = [
                'title' => 'Edit Event - Elite Cricket Academy',
                'event' => $event
            ];
            
            $this->view('admin/edit_event', $data);
        }
    }
    
    // ==================== DELETE ====================
    
    public function delete_event($id) {
        error_log("delete_event() called with ID: $id");
        
        $eventModel = $this->model('Event');
        $result = $eventModel->deleteEvent($id);
        
        if ($result) {
            flash('event_message', '✅ Event deleted successfully', 'alert alert-success');
        } else {
            flash('event_message', '❌ Failed to delete event', 'alert alert-danger');
        }
        
        redirect('admin/events');
    }
}
```

**Key Points:**
- ✅ Handle GET and POST in same method
- ✅ Always sanitize input: `filter_input_array()`
- ✅ Validate required fields before processing
- ✅ Use flash messages for user feedback
- ✅ Always redirect after POST (PRG pattern)
- ✅ Check if entity exists before update/delete
- ✅ Use try-catch for error handling
- ✅ Log all operations with `error_log()`

---

### PHASE 4: View Implementation

#### Step 4.1: List View (Read All)

**File:** `app/views/admin/events.php`

```php
<?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<div class="container">
    <h1>Events Management</h1>
    
    <?php flash('event_message'); ?>
    
    <div class="actions">
        <a href="<?php echo URLROOT; ?>/admin/create_event" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Event
        </a>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Date</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['upcomingEvents'] as $event): ?>
            <tr>
                <td><?php echo $event['id']; ?></td>
                <td><?php echo htmlspecialchars($event['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($event['Type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo date('Y-m-d', strtotime($event['event_date'])); ?></td>
                <td><?php echo htmlspecialchars($event['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($event['Status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <button onclick="editEvent(<?php echo $event['id']; ?>)" class="btn-edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteEvent(<?php echo $event['id']; ?>)" class="btn-delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editEvent(id) {
    window.location.href = `${window.location.origin}/Elite/admin/edit_event/${id}`;
}

function deleteEvent(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        window.location.href = `${window.location.origin}/Elite/admin/delete_event/${id}`;
    }
}
</script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
```

#### Step 4.2: Edit View (Update)

**File:** `app/views/admin/edit_event.php`

```php
<?php
// Debug using error_log instead of HTML comments
if (isset($data['event'])) {
    error_log("Edit Event View - Event ID: " . $data['event']['EventID']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        
        <?php flash('event_message'); ?>
        
        <?php if (!empty($data['event'])): ?>
            <?php 
                $event = $data['event'];
                
                // Format dates for form inputs
                $startDate = !empty($event['StartDate']) ? date('Y-m-d', strtotime($event['StartDate'])) : '';
                $startTime = !empty($event['StartDate']) ? date('H:i', strtotime($event['StartDate'])) : '';
                $endDate = !empty($event['EndDate']) ? date('Y-m-d', strtotime($event['EndDate'])) : '';
                $endTime = !empty($event['EndDate']) ? date('H:i', strtotime($event['EndDate'])) : '';
            ?>
            
            <form action="<?= URLROOT; ?>/admin/edit_event/<?= $event['EventID']; ?>" method="POST">
                
                <div class="form-group">
                    <label>Event ID</label>
                    <input type="text" value="<?= $event['EventID']; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Event Name <span class="required">*</span></label>
                    <input type="text" name="Name" value="<?= htmlspecialchars($event['Name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Event Type</label>
                    <select name="Type">
                        <option value="Tournament" <?= ($event['Type'] == 'Tournament') ? 'selected' : ''; ?>>Tournament</option>
                        <option value="Training Camp" <?= ($event['Type'] == 'Training Camp') ? 'selected' : ''; ?>>Training Camp</option>
                        <option value="Match" <?= ($event['Type'] == 'Match') ? 'selected' : ''; ?>>Match</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="StartDate_date" value="<?= $startDate; ?>">
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="StartTime" value="<?= $startTime; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="EndDate_date" value="<?= $endDate; ?>">
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="EndTime" value="<?= $endTime; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="Location" value="<?= htmlspecialchars($event['Location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="Description" rows="4"><?= htmlspecialchars($event['Description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="Status">
                        <option value="upcoming" <?= ($event['Status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="ongoing" <?= ($event['Status'] == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="completed" <?= ($event['Status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Event</button>
                    <a href="<?= URLROOT; ?>/admin/events" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <p>Event not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
```

**Key Points:**
- ✅ Always use `htmlspecialchars($var ?? '', ENT_QUOTES, 'UTF-8')` for PHP 8.2+
- ✅ Format dates properly for input fields
- ✅ Split datetime fields (date + time separately)
- ✅ Pre-select dropdown options
- ✅ Show flash messages
- ✅ Use error_log() for debugging, not HTML comments

---

### PHASE 5: JavaScript Integration

**File:** `public/js/admin/events.js`

```javascript
// Edit event - redirect to edit page
function editEvent(eventId) {
    if (!eventId) {
        alert("⚠️ Invalid event ID");
        return;
    }
    window.location.href = `${window.location.origin}/Elite/admin/edit_event/${eventId}`;
}

// Delete event with confirmation
function deleteEvent(eventId) {
    if (!eventId) {
        alert("⚠️ Invalid event ID");
        return;
    }
    
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        window.location.href = `${window.location.origin}/Elite/admin/delete_event/${eventId}`;
    }
}

// View event details
function viewEvent(eventId) {
    window.location.href = `${window.location.origin}/Elite/admin/view_event/${eventId}`;
}
```

**Key Points:**
- ✅ Use proper URL construction
- ✅ Validate parameters before redirect
- ✅ Use redirects, not AJAX for CRUD (simpler, more reliable)
- ✅ Add confirmation for destructive actions

---

## ⚠️ Common Problems & Solutions

### Problem 1: PDO Returns Lowercase Keys

**Symptom:**
```php
// Database column: EventID
// PDO returns: eventid or event_id
$event['EventID']; // ❌ Undefined
```

**Solution:** Set PDO case attribute in Database.php

```php
// app/libraries/Database.php
public function __construct() {
    // ...
    $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
    
    // CRITICAL: Preserve column name casing
    $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    
    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
}
```

---

### Problem 2: HTML Debug Output Breaks JSON/Redirects

**Symptom:**
```
SyntaxError: Unexpected token '<', " <!-- DEBUG"... is not valid JSON
```

**Cause:**
```php
public function __construct() {
    echo "<!-- DEBUG: Constructor called -->"; // ❌ WRONG
}
```

**Solution:**
```php
public function __construct() {
    error_log("Constructor called"); // ✅ CORRECT
}
```

**Rule:** NEVER use `echo`, `var_dump()`, or `print_r()` in:
- Controllers
- Models
- Constructors
- Any method that might output JSON or redirect

---

### Problem 3: PHP 8.2 htmlspecialchars() Warnings

**Symptom:**
```
htmlspecialchars(): Passing null to parameter #1
```

**Cause:**
```php
<?php echo htmlspecialchars($event['Name']); ?> // ❌ Breaks if null
```

**Solution:**
```php
<?php echo htmlspecialchars($event['Name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> // ✅ Safe
```

---

### Problem 4: DateTime Format Mismatches

**Symptom:**
```
Invalid datetime format: 2025-10-20 14:30
```

**Cause:** MySQL expects `YYYY-MM-DD HH:MM:SS` but code sends `YYYY-MM-DD HH:MM`

**Solution:**
```php
// In controller
$startDateTime = $_POST['StartDate_date'] . ' ' . $_POST['StartTime'] . ':00'; // ✅ Add seconds

// In view - split datetime for input fields
$startDate = date('Y-m-d', strtotime($event['StartDate'])); // Date only
$startTime = date('H:i', strtotime($event['StartDate']));   // Time only
```

---

### Problem 5: Field Name Mismatches

**Symptom:**
```
Undefined index: StartDate
```

**Cause:**
```php
// Form sends: StartDate_date
// Controller expects: StartDate
$_POST['StartDate']; // ❌ Doesn't exist
```

**Solution:** Match field names exactly
```php
// Form
<input name="StartDate_date">
<input name="StartTime">

// Controller
$date = $_POST['StartDate_date']; // ✅ Matches
$time = $_POST['StartTime'];      // ✅ Matches
```

---

### Problem 6: Redirect After POST Not Working

**Symptom:** Form submits but stays on same page

**Cause:**
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $model->create($data);
    // Missing redirect
}
```

**Solution:** Always redirect after POST (PRG pattern)
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $model->create($data);
    
    if ($result) {
        flash('message', 'Success!', 'alert alert-success');
        redirect('admin/entity'); // ✅ Always redirect
    } else {
        flash('message', 'Failed!', 'alert alert-danger');
        redirect('admin/create_entity'); // ✅ Redirect on error too
    }
}
```

---

### Problem 7: SQL Injection Vulnerability

**Symptom:** Security vulnerability

**Cause:**
```php
// ❌ DANGEROUS - Never do this
$this->db->query("SELECT * FROM Event WHERE EventID = $id");
```

**Solution:** Always use prepared statements
```php
// ✅ SAFE - Always do this
$this->db->query("SELECT * FROM Event WHERE EventID = :id");
$this->db->bind(':id', $id);
$result = $this->db->single();
```

---

### Problem 8: Null Values Breaking Updates

**Symptom:** Optional fields cause errors

**Cause:**
```php
$data = [
    'max_participants' => $_POST['MaxParticipants'] // ❌ Empty string becomes 0
];
```

**Solution:** Handle nulls properly
```php
$data = [
    'max_participants' => !empty($_POST['MaxParticipants']) 
        ? intval($_POST['MaxParticipants']) 
        : null // ✅ Null for empty optional fields
];
```

---

## ✅ Best Practices Checklist

### Database Layer (Model)
- [ ] Use prepared statements with PDO
- [ ] Bind all parameters with correct types
- [ ] Return boolean for success/failure
- [ ] Use try-catch for error handling
- [ ] Log operations with `error_log()`
- [ ] Convert stdClass to array for consistency
- [ ] Handle null values properly
- [ ] Add docblock comments for methods

### Controller Layer
- [ ] Check authentication in constructor
- [ ] Sanitize all input: `filter_input_array()`
- [ ] Validate required fields
- [ ] Handle GET and POST separately
- [ ] Always redirect after POST (PRG pattern)
- [ ] Check entity exists before update/delete
- [ ] Use flash messages for feedback
- [ ] Log operations with `error_log()`
- [ ] Never use `echo` or `var_dump()`

### View Layer
- [ ] Use `htmlspecialchars($var ?? '', ENT_QUOTES, 'UTF-8')`
- [ ] Format dates properly for display/input
- [ ] Show flash messages
- [ ] Pre-select dropdown options for edit
- [ ] Split datetime fields (date + time)
- [ ] Use error_log() for debug, not HTML comments
- [ ] Include CSRF protection (if implemented)
- [ ] Validate on client-side (optional but recommended)

### JavaScript
- [ ] Validate eventId before action
- [ ] Use proper URL construction
- [ ] Add confirmation for destructive actions
- [ ] Use redirects for CRUD (not AJAX for simplicity)
- [ ] Handle errors gracefully

### Security
- [ ] Use prepared statements (prevent SQL injection)
- [ ] Sanitize input (prevent XSS)
- [ ] Validate data types
- [ ] Check authentication/authorization
- [ ] Use HTTPS in production
- [ ] Add CSRF tokens (recommended)
- [ ] Limit file upload sizes/types
- [ ] Use password hashing for sensitive data

---

## 🧪 Testing Checklist

### CREATE Testing
- [ ] Submit with all fields filled
- [ ] Submit with only required fields
- [ ] Submit with invalid data types
- [ ] Submit with SQL injection attempts
- [ ] Submit with XSS attempts
- [ ] Check database record created
- [ ] Verify flash message displays
- [ ] Verify redirect works

### READ Testing
- [ ] View list with multiple records
- [ ] View list with zero records
- [ ] View single record
- [ ] View non-existent record
- [ ] Test pagination (if implemented)
- [ ] Test filtering (if implemented)
- [ ] Test sorting (if implemented)
- [ ] Check all data displays correctly

### UPDATE Testing
- [ ] Edit with all fields modified
- [ ] Edit with only one field modified
- [ ] Edit with invalid data
- [ ] Edit non-existent record
- [ ] Submit without changes
- [ ] Check database record updated
- [ ] Verify flash message displays
- [ ] Verify redirect works
- [ ] Check datetime fields update correctly

### DELETE Testing
- [ ] Delete existing record
- [ ] Delete non-existent record
- [ ] Cancel delete confirmation
- [ ] Check database record removed
- [ ] Verify flash message displays
- [ ] Verify redirect works
- [ ] Check foreign key constraints (if any)

### Error Handling
- [ ] Database connection failure
- [ ] Invalid parameters
- [ ] Missing required fields
- [ ] Network timeout
- [ ] Permission denied
- [ ] Duplicate entries (if unique constraint)

---

## 📝 Code Templates

### Quick Start Template

```php
// ============================================
// MODEL: app/models/YourEntity.php
// ============================================
<?php
class YourEntity {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // READ ALL
    public function getAll() {
        $this->db->query('SELECT * FROM your_table ORDER BY id DESC');
        return $this->db->resultSet();
    }
    
    // READ ONE
    public function getById($id) {
        $this->db->query('SELECT * FROM your_table WHERE id = :id');
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? json_decode(json_encode($result), true) : null;
    }
    
    // CREATE
    public function create($data) {
        $this->db->query('INSERT INTO your_table (name, description) VALUES (:name, :description)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        
        try {
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Create error: " . $e->getMessage());
            return false;
        }
    }
    
    // UPDATE
    public function update($data) {
        $this->db->query('UPDATE your_table SET name = :name, description = :description WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        
        try {
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }
    
    // DELETE
    public function delete($id) {
        $this->db->query('DELETE FROM your_table WHERE id = :id');
        $this->db->bind(':id', $id);
        
        try {
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }
}

// ============================================
// CONTROLLER: app/controllers/Admin.php
// ============================================

// LIST ALL
public function your_entities() {
    $model = $this->model('YourEntity');
    $data = [
        'title' => 'Manage Entities',
        'entities' => $model->getAll()
    ];
    $this->view('admin/your_entities', $data);
}

// CREATE
public function create_entity() {
    $model = $this->model('YourEntity');
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        $data = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        if (empty($data['name'])) {
            flash('entity_message', 'Name is required', 'alert alert-danger');
            redirect('admin/create_entity');
            return;
        }
        
        $result = $model->create($data);
        
        if ($result) {
            flash('entity_message', 'Created successfully!', 'alert alert-success');
            redirect('admin/your_entities');
        } else {
            flash('entity_message', 'Failed to create', 'alert alert-danger');
            redirect('admin/create_entity');
        }
    } else {
        $data = ['title' => 'Create Entity'];
        $this->view('admin/create_entity', $data);
    }
}

// EDIT
public function edit_entity($id) {
    $model = $this->model('YourEntity');
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        $data = [
            'id' => $id,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        $result = $model->update($data);
        
        if ($result) {
            flash('entity_message', 'Updated successfully!', 'alert alert-success');
            redirect('admin/your_entities');
        } else {
            flash('entity_message', 'Failed to update', 'alert alert-danger');
            redirect('admin/edit_entity/' . $id);
        }
    } else {
        $entity = $model->getById($id);
        
        if (!$entity) {
            flash('entity_message', 'Entity not found', 'alert alert-danger');
            redirect('admin/your_entities');
            return;
        }
        
        $data = [
            'title' => 'Edit Entity',
            'entity' => $entity
        ];
        $this->view('admin/edit_entity', $data);
    }
}

// DELETE
public function delete_entity($id) {
    $model = $this->model('YourEntity');
    $result = $model->delete($id);
    
    if ($result) {
        flash('entity_message', 'Deleted successfully', 'alert alert-success');
    } else {
        flash('entity_message', 'Failed to delete', 'alert alert-danger');
    }
    
    redirect('admin/your_entities');
}
```

---

## 🎓 Implementation Workflow

### Step-by-Step Process for New CRUD

1. **Plan (30 minutes)**
   - Define database table structure
   - List all fields and types
   - Identify required vs optional fields
   - Plan relationships with other tables

2. **Database (1 hour)**
   - Create SQL migration file
   - Run migration
   - Verify table structure
   - Insert test data

3. **Model (2 hours)**
   - Create model file
   - Implement getAll() method
   - Implement getById() method
   - Implement create() method
   - Implement update() method
   - Implement delete() method
   - Test each method

4. **Controller (2 hours)**
   - Create list method
   - Create create method (GET/POST)
   - Create edit method (GET/POST)
   - Create delete method
   - Add validation
   - Add error handling

5. **Views (3 hours)**
   - Create list view
   - Create create form view
   - Create edit form view
   - Add styling
   - Add JavaScript interactions

6. **Testing (2 hours)**
   - Test create with valid data
   - Test create with invalid data
   - Test update
   - Test delete
   - Test edge cases
   - Fix bugs

7. **Polish (1 hour)**
   - Clean up debug code
   - Add comments
   - Optimize queries
   - Document

**Total Estimated Time: 11 hours for complete CRUD**

---

## 📚 Reference Links

### Internal Documentation
- `EDIT_EVENT_IMPLEMENTATION.md` - Complete edit event guide
- `FIX_DEBUG_OUTPUT_CONTAMINATION.md` - Debug output issues
- `FIX_EDIT_EVENT_JSON_ERROR.md` - JSON parsing fixes
- `EVENT_SYSTEM_CODE_AUDIT.md` - Code quality audit
- `CLEANUP_COMPLETED.md` - Cleanup documentation

### PHP Resources
- [PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [PHP 8.2 Changes](https://www.php.net/releases/8.2/en.php)
- [htmlspecialchars() Documentation](https://www.php.net/htmlspecialchars)

### Best Practices
- [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [OWASP XSS Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PRG Pattern](https://en.wikipedia.org/wiki/Post/Redirect/Get)

---

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-10-20 | Initial guide based on working Events CRUD |

---

## 📞 Support

If you encounter issues while implementing CRUD:

1. **Check this guide** - Most problems are documented
2. **Review working example** - Events system implementation
3. **Check error logs** - `error_log` statements will help
4. **Test incrementally** - Build and test one method at a time
5. **Use git** - Commit working code before making changes

---

**Remember:** This guide is based on a fully working, tested Events CRUD system. Follow it exactly for consistent, reliable CRUD implementations across your application.

**Next time you need CRUD:** Read this document, use the templates, follow the checklist. You'll save hours of debugging! 🚀
