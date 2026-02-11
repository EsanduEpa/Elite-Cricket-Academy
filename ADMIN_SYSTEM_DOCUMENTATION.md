# ADMIN SECTION - TECHNICAL DOCUMENTATION
## Elite Cricket Academy Management System

**Prepared for:** Technical Viva / Demonstration  
**Date:** February 10, 2026  
**Architecture:** MVC (Model-View-Controller) Pattern  
**Database:** MySQL with PDO

---

## TABLE OF CONTENTS
1. [System Architecture](#system-architecture)
2. [File Structure](#file-structure)
3. [Authentication & Security](#authentication-security)
4. [Database Tables](#database-tables)
5. [Admin Features](#admin-features)
6. [Code Flow Examples](#code-flow-examples)

---

## 1. SYSTEM ARCHITECTURE

### MVC Pattern Implementation

```
┌─────────────┐      ┌──────────────┐      ┌─────────────┐
│   Browser   │─────▶│  Controller  │─────▶│    Model    │
│  (Client)   │      │   (Logic)    │      │  (Database) │
└─────────────┘      └──────────────┘      └─────────────┘
       ▲                     │                      │
       │                     ▼                      │
       │              ┌──────────────┐             │
       └──────────────│     View     │◀────────────┘
                      │  (Template)  │
                      └──────────────┘
```

**Flow:**
1. User visits `/admin/dashboard`
2. **Router** (`Core.php`) calls `Admin` controller → `dashboard()` method
3. **Controller** loads models: `M_Users`, `Event`, `Feedback`
4. **Models** query database using PDO
5. **Controller** passes data to **View**
6. **View** renders HTML with PHP
7. Browser receives complete page

---

## 2. FILE STRUCTURE

### A. Controller Layer
**File:** `/app/controllers/Admin.php` (1893 lines)

**Key Methods:**
```php
public function __construct()      // Authentication check for all methods
public function dashboard()        // Main admin panel with statistics
public function staff()           // Staff management (CRUD)
public function players()         // Player management (CRUD)
public function events()          // Event calendar & management
public function feedback()        // Feedback monitoring system
public function finance()         // Revenue & financial reports
public function profile()         // Admin profile management
```

### B. Model Layer
**Files:**
- `/app/models/M_Users.php` (1312 lines) - User operations
- `/app/models/Event.php` - Event CRUD operations
- `/app/models/Feedback.php` (225 lines) - Feedback queries
- `/app/models/Finance.php` (310 lines) - Revenue calculations

### C. View Layer
**Directory:** `/app/views/admin/`
```
dashboard.php           - Main admin dashboard
staff.php              - Staff listing & management
players.php            - Player listing & management
events.php             - Event calendar with FullCalendar.js
create_event.php       - Event creation form
edit_event.php         - Event editing form
feedback.php           - Feedback table & status management
finance.php            - Revenue overview & transactions
profile.php            - Admin profile settings
reports.php            - Player reports with filters
player_statistics.php  - Individual player performance
```

### D. Assets
```
/public/css/admin/     - Admin-specific stylesheets
/public/js/admin/      - Admin JavaScript files
/public/uploads/       - User-uploaded files (profile images, receipts)
```

---

## 3. AUTHENTICATION & SECURITY

### A. Session-Based Authentication

**File:** `/app/helpers/session_helper.php`

```php
function requireAuth($allowedRoles = []) {
    // Check if session exists
    if (!isset($_SESSION['user_id'])) {
        redirect('login');  // Not logged in
    }
    
    // Check role authorization
    if (!empty($allowedRoles)) {
        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            redirect('login');  // Wrong role
        }
    }
}
```

**Usage in Admin Controller:**
```php
public function __construct() {
    requireAuth(['Admin']);  // Only Admin role allowed
}
```

**Session Variables:**
- `$_SESSION['user_id']` - UserID from database
- `$_SESSION['user_name']` - Display name
- `$_SESSION['user_role']` - Admin/Coach/Player/Trainer/ShopEmployee
- `$_SESSION['user_email']` - Email address

### B. SQL Injection Prevention

**Uses PDO Prepared Statements:**
```php
// BAD (vulnerable):
$sql = "SELECT * FROM User WHERE email = '$email'";

// GOOD (secure):
$this->db->query("SELECT * FROM User WHERE email = :email");
$this->db->bind(':email', $email);
$result = $this->db->single();
```

### C. AJAX Request Validation

```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}
```

---

## 4. DATABASE TABLES

### A. User Table
```sql
Table: User
Columns:
- UserID (INT, PRIMARY KEY, AUTO_INCREMENT)
- Name (VARCHAR)
- Email (VARCHAR, UNIQUE)
- PhoneNumber (VARCHAR)
- PasswordHash (VARCHAR) -- bcrypt hashed
- Role (ENUM: 'Admin', 'Coach', 'Player', 'Trainer', 'ShopEmployee')
- Status (ENUM: 'active', 'inactive')
- DateOfBirth (DATE)
- Address (TEXT)
- School (VARCHAR)
- DateJoined (TIMESTAMP)
```

### B. PlayerProfile Table
```sql
Table: playerprofile
Columns:
- PlayerID (INT, PRIMARY KEY, FOREIGN KEY → User.UserID)
- BattingStyle (ENUM: 'Right-handed', 'Left-handed')
- BowlingStyle (ENUM: 'Fast', 'Medium', 'Spin')
- SubscriptionType (ENUM: 'basic', 'premium', 'private_only')
- JerseyNumber (INT)
- CoachID (INT, FOREIGN KEY → User.UserID)
- TrainerID (INT, FOREIGN KEY → User.UserID)
```

### C. Feedback Table
```sql
Table: feedback
Columns:
- FeedbackID (INT, PRIMARY KEY)
- FromUserID (INT, FOREIGN KEY → User.UserID)
- ToUserID (INT, NULLABLE) -- specific coach/trainer
- Content (TEXT)
- Rating (INT, 1-5)
- Category (ENUM: 'coach', 'trainer', 'facility', 'equipment', 'shop', 'general')
- Status (ENUM: 'pending', 'reviewed', 'resolved')
- CreatedDate (TIMESTAMP)
```

### D. Event Table
```sql
Table: Event
Columns:
- EventID (INT, PRIMARY KEY)
- Title (VARCHAR)
- Description (TEXT)
- Type (ENUM: 'tournament', 'training', 'match', 'meeting')
- StartDate (DATE)
- EndDate (DATE)
- Location (VARCHAR)
- Status (ENUM: 'upcoming', 'ongoing', 'completed')
- CreatedBy (INT, FOREIGN KEY → User.UserID)
```

### E. Finance Tables
```sql
Table: productorder
- OrderID (PRIMARY KEY)
- PlayerID (FOREIGN KEY → User.UserID)
- TotalAmount (DECIMAL)
- PaymentMethod (ENUM: 'Cash', 'Card', 'BankTransfer')
- Status (ENUM: 'pending', 'completed', 'cancelled')
- OrderDate (TIMESTAMP)

Table: subscriptionpayment
- PaymentID (PRIMARY KEY)
- SubscriptionID (FOREIGN KEY → Subscription)
- Amount (DECIMAL)
- PaymentMethod (ENUM: 'Cash', 'Card', 'BankTransfer')
- Status (ENUM: 'pending', 'completed', 'failed')
- PaymentDate (TIMESTAMP)
```

---

## 5. ADMIN FEATURES

### A. DASHBOARD
**URL:** `http://localhost/Elite/admin/dashboard`  
**Controller Method:** `Admin::dashboard()`  
**View:** `/app/views/admin/dashboard.php`

**Purpose:** Central hub showing academy overview

**Data Sources:**
```php
// 1. USER STATISTICS
$totalUsers = $userModel->getTotalUsers();
$totalCoaches = $userModel->getTotalUsersByType('Coach');
$totalPlayers = $userModel->getTotalUsersByType('Player');
$totalTrainers = $userModel->getTotalUsersByType('Trainer');
$totalStaff = $totalAdmins + $totalCoaches + $totalTrainers + $totalShopEmployees;

// 2. UPCOMING EVENTS (next 4)
$upcomingEvents = $eventModel->getUpcomingEvents(4);

// 3. RECENT ACTIVITIES (last 20)
$recentActivities = $userModel->getRecentActivities(20);

// 4. PENDING FEEDBACK (needs attention)
$pendingFeedback = $feedbackModel->getPendingFeedbacks(5);

// 5. TODAY'S STATISTICS
$todayStats['newRegistrations'] = $userModel->getTodayRegistrations();
$todayStats['activeEvents'] = count($eventModel->getTodayActiveEvents());
```

**Key SQL Query Example:**
```php
// File: app/models/M_Users.php
public function getTotalUsersByType($role) {
    $this->db->query('SELECT COUNT(*) as total 
                      FROM User 
                      WHERE Role = :role');
    $this->db->bind(':role', $role);
    $row = $this->db->single();
    return $row->total;
}
```

---

### B. STAFF MANAGEMENT
**URL:** `http://localhost/Elite/admin/staff`  
**Controller Method:** `Admin::staff()`  
**View:** `/app/views/admin/staff.php`

**Features:**
1. **List All Staff** (Coaches, Trainers, Shop Employees)
2. **Add New Staff** with role selection
3. **Edit Staff** details
4. **Delete Staff** accounts
5. **Search & Filter** by role

**ADD STAFF Flow:**
```php
// 1. Form Submission (staff.php)
<form id="addStaffForm">
    <input name="name" required>
    <input name="email" required>
    <select name="role">
        <option value="Coach">Coach</option>
        <option value="Trainer">Trainer</option>
        <option value="ShopEmployee">Shop Employee</option>
    </select>
    <input name="password" required>
</form>

// 2. Controller receives POST (Admin.php)
public function add_staff() {
    // Validate input
    if(empty($_POST['name']) || empty($_POST['email'])) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing']);
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Prepare data
    $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'role' => $_POST['role'],
        'password' => $hashedPassword,
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'status' => 'active'
    ];
    
    // Call model to insert
    $userId = $userModel->registerUser($data);
    
    if($userId) {
        echo json_encode(['success' => true, 'message' => 'Staff added']);
    }
}

// 3. Model executes SQL (M_Users.php)
public function registerUser($data) {
    $this->db->query('INSERT INTO User 
        (Name, Email, PasswordHash, Role, PhoneNumber, Address, Status, DateJoined) 
        VALUES (:name, :email, :password, :role, :phone, :address, :status, NOW())');
    
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', $data['password']);
    $this->db->bind(':role', $data['role']);
    $this->db->bind(':phone', $data['phone']);
    $this->db->bind(':address', $data['address']);
    $this->db->bind(':status', $data['status']);
    
    if($this->db->execute()) {
        return $this->db->lastInsertId();  // Return new UserID
    }
    return false;
}
```

---

### C. PLAYER MANAGEMENT
**URL:** `http://localhost/Elite/admin/players`  
**Controller Method:** `Admin::players()`  
**View:** `/app/views/admin/players.php`

**Features:**
1. **List All Players** with profile details
2. **Add New Player** with cricket profile (batting, bowling, subscription)
3. **Assign Coach & Trainer**
4. **View Player Statistics** (link to detailed performance page)
5. **Update Player Info**
6. **Delete Player** account

**Special: Two-Table Insert**
```php
// When adding a player, we insert into TWO tables:

// STEP 1: Insert into User table
$userId = $userModel->registerUser([
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'role' => 'Player',  // Fixed role
    'password' => $hashedPassword
]);

// STEP 2: Insert into playerprofile table
if($userId) {
    $profileData = [
        'player_id' => $userId,
        'batting_style' => $_POST['batting_style'],
        'bowling_style' => $_POST['bowling_style'],
        'subscription_type' => $_POST['subscription_type'],
        'jersey_number' => $_POST['jersey_number'],
        'coach_id' => $_POST['coach_id'] ?? null,
        'trainer_id' => $_POST['trainer_id'] ?? null
    ];
    
    $userModel->createPlayerProfile($profileData);
}
```

**Model Method (M_Users.php):**
```php
public function createPlayerProfile($data) {
    $this->db->query('INSERT INTO playerprofile 
        (PlayerID, BattingStyle, BowlingStyle, SubscriptionType, 
         JerseyNumber, CoachID, TrainerID) 
        VALUES (:player_id, :batting, :bowling, :subscription, 
                :jersey, :coach, :trainer)');
    
    $this->db->bind(':player_id', $data['player_id']);
    $this->db->bind(':batting', $data['batting_style']);
    $this->db->bind(':bowling', $data['bowling_style']);
    $this->db->bind(':subscription', $data['subscription_type']);
    $this->db->bind(':jersey', $data['jersey_number']);
    $this->db->bind(':coach', $data['coach_id']);
    $this->db->bind(':trainer', $data['trainer_id']);
    
    return $this->db->execute();
}
```

---

### D. EVENT MANAGEMENT
**URL:** `http://localhost/Elite/admin/events`  
**Controller Method:** `Admin::events()`  
**View:** `/app/views/admin/events.php`  
**Library:** FullCalendar.js (interactive calendar)

**Features:**
1. **Calendar View** - Visual event display
2. **Create Event** - Tournament, Training, Match, Meeting
3. **Edit Event** - Update details
4. **Delete Event** - Remove from calendar
5. **Auto Status Update** - Events become "completed" after end date

**Calendar Integration:**
```javascript
// File: /public/js/admin/events.js

// Fetch events via AJAX
calendar.addEventSource({
    url: '/Elite/admin/get_calendar_events',
    method: 'GET',
    success: function(events) {
        // events = [{id, title, start, end, type}]
    }
});
```

**Controller AJAX Endpoint:**
```php
// File: app/controllers/Admin.php
public function get_calendar_events() {
    $eventModel = $this->model('Event');
    $events = $eventModel->getAllEventsForCalendar();
    
    // Format for FullCalendar
    $calendarEvents = array_map(function($event) {
        return [
            'id' => $event->EventID,
            'title' => $event->Title,
            'start' => $event->StartDate,
            'end' => $event->EndDate,
            'backgroundColor' => $this->getEventColor($event->Type),
            'type' => $event->Type
        ];
    }, $events);
    
    echo json_encode($calendarEvents);
}
```

**Auto Status Update:**
```php
// File: app/models/Event.php
public function updateEventStatuses() {
    // Mark past events as completed
    $this->db->query("UPDATE Event 
                      SET Status = 'completed' 
                      WHERE EndDate < CURDATE() 
                      AND Status != 'completed'");
    return $this->db->execute();
}
```

---

### E. FEEDBACK MANAGEMENT
**URL:** `http://localhost/Elite/admin/feedback`  
**Controller Method:** `Admin::feedback()`  
**View:** `/app/views/admin/feedback.php`

**Purpose:** Monitor and respond to user feedback

**Features:**
1. **View All Feedback** in table format
2. **Filter by Category** (coach, trainer, facility, equipment, shop, general)
3. **Update Status** (pending → reviewed → resolved)
4. **Delete Feedback**
5. **Rating Display** (1-5 stars)

**Feedback Display Query:**
```php
// File: app/models/Feedback.php
public function getAllFeedbacks() {
    $this->db->query('SELECT 
        f.FeedbackID,
        f.Content,
        f.Category,
        f.Rating,
        f.Status,
        f.CreatedDate,
        u1.Name as FromUserName,
        u2.Name as ToUserName
    FROM feedback f
    LEFT JOIN User u1 ON f.FromUserID = u1.UserID
    LEFT JOIN User u2 ON f.ToUserID = u2.UserID
    ORDER BY f.CreatedDate DESC');
    
    return $this->db->resultSet();
}
```

**AJAX Status Update:**
```javascript
// File: /public/js/admin/feedback.js
function updateFeedbackStatus(feedbackId, newStatus) {
    fetch('/Elite/admin/updateFeedbackStatus', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            feedback_id: feedbackId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Update UI
            location.reload();
        }
    });
}
```

**Controller Handler:**
```php
// File: app/controllers/Admin.php
public function updateFeedbackStatus() {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false]);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $feedbackId = $input['feedback_id'];
    $status = $input['status'];
    
    $feedbackModel = $this->model('Feedback');
    $result = $feedbackModel->updateFeedbackStatus($feedbackId, $status);
    
    echo json_encode(['success' => $result, 'message' => 'Status updated']);
}
```

---

### F. FINANCE MANAGEMENT
**URL:** `http://localhost/Elite/admin/finance`  
**Controller Method:** `Admin::finance()`  
**View:** `/app/views/admin/finance.php`  
**Model:** `/app/models/Finance.php`

**Purpose:** Track academy revenue and transactions

**Features:**
1. **Total Revenue** - All-time completed transactions
2. **Monthly Revenue** - Current month earnings
3. **Revenue Categories** - Shop sales vs Membership fees
4. **Recent Transactions** - Last 10 orders/payments
5. **Top Revenue Sources** - Best selling products
6. **Growth Rate** - Month-over-month percentage

**Revenue Calculation:**
```php
// File: app/models/Finance.php
public function getTotalRevenue() {
    // Product orders (Shop sales)
    $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                      FROM productorder 
                      WHERE Status = 'completed'");
    $productRevenue = $this->db->single()->revenue;
    
    // Subscription payments (Membership fees)
    $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                      FROM subscriptionpayment 
                      WHERE Status = 'completed'");
    $subscriptionRevenue = $this->db->single()->revenue;
    
    return floatval($productRevenue) + floatval($subscriptionRevenue);
}
```

**Revenue by Category:**
```php
public function getRevenueByCategory() {
    $totalRevenue = $this->getTotalRevenue();
    
    // Shop sales total
    $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                      FROM productorder 
                      WHERE Status = 'completed'");
    $shopSales = $this->db->single()->revenue;
    
    // Membership fees total
    $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                      FROM subscriptionpayment 
                      WHERE Status = 'completed'");
    $membershipFees = $this->db->single()->revenue;
    
    return [
        [
            'category' => 'Shop Sales',
            'amount' => $shopSales,
            'percentage' => $totalRevenue > 0 ? ($shopSales / $totalRevenue) * 100 : 0
        ],
        [
            'category' => 'Membership Fees',
            'amount' => $membershipFees,
            'percentage' => $totalRevenue > 0 ? ($membershipFees / $totalRevenue) * 100 : 0
        ]
    ];
}
```

**Recent Transactions (Combined Query):**
```php
public function getRecentTransactions($limit = 10) {
    // UNION of productorder and subscriptionpayment
    $this->db->query("
        (SELECT 
            CONCAT('ORD-', OrderID) as id,
            'Shop Purchase' as type,
            TotalAmount as amount,
            PaymentMethod as method,
            Status as status,
            OrderDate as date
        FROM productorder
        ORDER BY OrderDate DESC
        LIMIT :limit1)
        
        UNION ALL
        
        (SELECT 
            CONCAT('SUB-', PaymentID) as id,
            'Subscription Payment' as type,
            Amount as amount,
            PaymentMethod as method,
            Status as status,
            PaymentDate as date
        FROM subscriptionpayment
        ORDER BY PaymentDate DESC
        LIMIT :limit2)
        
        ORDER BY date DESC
        LIMIT :limit3
    ");
    
    $this->db->bind(':limit1', $limit, PDO::PARAM_INT);
    $this->db->bind(':limit2', $limit, PDO::PARAM_INT);
    $this->db->bind(':limit3', $limit, PDO::PARAM_INT);
    
    return $this->db->resultSet();
}
```

---

### G. REPORTS & FILTERING
**URL:** `http://localhost/Elite/admin/reports`  
**Controller Method:** `Admin::reports()`  
**View:** `/app/views/admin/reports.php`

**Purpose:** Generate filtered player reports

**Filter Options:**
1. **Status:** Active / Inactive
2. **Subscription:** Basic / Premium / Private Only
3. **Batting Style:** Right-handed / Left-handed
4. **Bowling Style:** Fast / Medium / Spin
5. **Search:** Name or Email

**Dynamic Query Building:**
```php
// File: app/models/M_Users.php
public function getPlayersForReport($filters = []) {
    // Base query with JOIN
    $sql = "SELECT 
        u.UserID,
        u.Name,
        u.Email,
        u.PhoneNumber,
        u.DateOfBirth,
        u.Status,
        TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) as Age,
        pp.BattingStyle,
        pp.BowlingStyle,
        pp.SubscriptionType,
        pp.JerseyNumber
    FROM User u
    LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
    WHERE u.Role = 'Player'";
    
    $params = [];
    
    // Add filters dynamically
    if(!empty($filters['status']) && $filters['status'] !== 'all') {
        $sql .= " AND u.Status = :status";
        $params[':status'] = $filters['status'];
    }
    
    if(!empty($filters['subscription']) && $filters['subscription'] !== 'all') {
        $sql .= " AND pp.SubscriptionType = :subscription";
        $params[':subscription'] = $filters['subscription'];
    }
    
    if(!empty($filters['batting']) && $filters['batting'] !== 'all') {
        $sql .= " AND pp.BattingStyle = :batting";
        $params[':batting'] = $filters['batting'];
    }
    
    if(!empty($filters['bowling']) && $filters['bowling'] !== 'all') {
        $sql .= " AND pp.BowlingStyle = :bowling";
        $params[':bowling'] = $filters['bowling'];
    }
    
    if(!empty($filters['search'])) {
        $sql .= " AND (u.Name LIKE :search OR u.Email LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    $sql .= " ORDER BY u.Name ASC";
    
    $this->db->query($sql);
    
    // Bind all parameters
    foreach($params as $key => $value) {
        $this->db->bind($key, $value);
    }
    
    return $this->db->resultSet();
}
```

**Controller Usage:**
```php
public function reports() {
    $userModel = $this->model('M_Users');
    
    // Get filter values from URL
    $filters = [
        'status' => $_GET['status'] ?? 'all',
        'subscription' => $_GET['subscription'] ?? 'all',
        'batting' => $_GET['batting'] ?? 'all',
        'bowling' => $_GET['bowling'] ?? 'all',
        'search' => $_GET['search'] ?? ''
    ];
    
    // Get filtered players
    $players = $userModel->getPlayersForReport($filters);
    
    $data = [
        'players' => $players,
        'filters' => $filters,
        'totalPlayers' => count($players)
    ];
    
    $this->view('admin/reports', $data);
}
```

---

## 6. CODE FLOW EXAMPLES

### Example 1: User Login to Dashboard

```
1. USER ENTERS CREDENTIALS
   URL: /Elite/login
   Form: email + password

2. LOGIN CONTROLLER (Login.php)
   ├─ Validate input (not empty)
   ├─ Call M_Users->getUserByEmail($email)
   ├─ Verify password: password_verify($input, $hash)
   └─ If valid:
      ├─ Store in session: $_SESSION['user_id'], $_SESSION['user_role']
      └─ Redirect based on role:
         └─ if Role == 'Admin' → redirect('admin/dashboard')

3. ADMIN CONTROLLER (Admin.php)
   ├─ __construct() checks requireAuth(['Admin'])
   └─ dashboard() method:
      ├─ Load models: M_Users, Event, Feedback
      ├─ Query database for statistics
      └─ Pass data to view

4. DASHBOARD VIEW (dashboard.php)
   ├─ Include header (sidebar, navigation)
   ├─ Display statistics: <?php echo $data['totalUsers']; ?>
   ├─ Loop through events: <?php foreach($data['upcomingEvents'] as $event): ?>
   └─ Include footer

5. BROWSER RENDERS
   User sees complete dashboard with real data
```

### Example 2: Adding a New Coach (AJAX)

```
1. ADMIN CLICKS "Add Staff"
   Modal opens with form

2. FORM SUBMISSION (JavaScript)
   File: /public/js/admin/staff-management.js
   
   document.getElementById('addStaffForm').addEventListener('submit', (e) => {
       e.preventDefault();
       
       const formData = new FormData(e.target);
       
       fetch('/Elite/admin/add_staff', {
           method: 'POST',
           body: formData
       })
       .then(response => response.json())
       .then(data => {
           if(data.success) {
               alert('Staff added successfully');
               location.reload();
           } else {
               alert('Error: ' + data.message);
           }
       });
   });

3. ADMIN CONTROLLER (Admin.php)
   public function add_staff() {
       // Validate
       if(empty($_POST['name']) || empty($_POST['email'])) {
           echo json_encode(['success' => false, 'message' => 'Missing fields']);
           return;
       }
       
       // Hash password
       $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
       
       // Prepare data
       $data = [
           'name' => $_POST['name'],
           'email' => $_POST['email'],
           'role' => $_POST['role'],  // 'Coach'
           'password' => $password,
           'phone' => $_POST['phone'],
           'status' => 'active'
       ];
       
       // Call model
       $userModel = $this->model('M_Users');
       $userId = $userModel->registerUser($data);
       
       if($userId) {
           // Create role-specific profile if Coach
           if($_POST['role'] === 'Coach') {
               $userModel->createCoachProfile($userId);
           }
           
           echo json_encode(['success' => true, 'message' => 'Coach added']);
       } else {
           echo json_encode(['success' => false, 'message' => 'Database error']);
       }
   }

4. USER MODEL (M_Users.php)
   public function registerUser($data) {
       $this->db->query('INSERT INTO User 
           (Name, Email, PasswordHash, Role, PhoneNumber, Status, DateJoined) 
           VALUES (:name, :email, :password, :role, :phone, :status, NOW())');
       
       // Bind parameters (prevents SQL injection)
       $this->db->bind(':name', $data['name']);
       $this->db->bind(':email', $data['email']);
       $this->db->bind(':password', $data['password']);
       $this->db->bind(':role', $data['role']);
       $this->db->bind(':phone', $data['phone']);
       $this->db->bind(':status', $data['status']);
       
       // Execute query
       if($this->db->execute()) {
           return $this->db->lastInsertId();  // Return new UserID
       }
       
       return false;
   }

5. DATABASE LAYER (Database.php)
   Uses PDO prepared statements:
   
   public function execute() {
       return $this->stmt->execute();
   }
   
   public function lastInsertId() {
       return $this->conn->lastInsertId();
   }

6. RESPONSE TO BROWSER
   JSON: {"success": true, "message": "Coach added"}
   
   JavaScript receives response → shows success message → reloads page
   
7. PAGE RELOAD
   Staff table now shows new coach with UserID, Name, Email, Role
```

### Example 3: Event Status Auto-Update

```
1. ADMIN VISITS DASHBOARD
   URL: /Elite/admin/dashboard

2. CONTROLLER CONSTRUCTOR
   public function __construct() {
       requireAuth(['Admin']);  // Check authorization
   }

3. DASHBOARD METHOD (FIRST LINE)
   public function dashboard() {
       // Auto-update event statuses
       $eventModel = $this->model('Event');
       $eventModel->updateEventStatuses();  // ← This runs BEFORE dashboard loads
       
       // Continue with dashboard data...
   }

4. EVENT MODEL (Event.php)
   public function updateEventStatuses() {
       // SQL: Mark past events as completed
       $this->db->query("UPDATE Event 
                         SET Status = 'completed' 
                         WHERE EndDate < CURDATE() 
                         AND Status != 'completed'");
       
       return $this->db->execute();
   }

5. DATABASE EXECUTION
   Example: If today is 2026-02-10
   
   UPDATE Event 
   SET Status = 'completed' 
   WHERE EndDate < '2026-02-10'  -- Events ended before today
   AND Status != 'completed';    -- Only update if not already completed
   
   Result: 5 rows affected (5 past events updated)

6. DASHBOARD LOADS
   Calendar now shows correct event statuses
   Past events appear in gray (completed)
   Future events appear in color (upcoming)
```

---

## 7. KEY CODING CONCEPTS FOR VIVA

### A. MVC Pattern Benefits

**Question:** Why use MVC architecture?

**Answer:**
1. **Separation of Concerns**
   - Model: Database logic only
   - Controller: Business logic (validation, flow control)
   - View: Presentation (HTML/CSS)

2. **Reusability**
   - Same model method used by multiple controllers
   - Example: `M_Users->getTotalUsers()` used in dashboard, reports, staff page

3. **Maintainability**
   - Change database query without touching HTML
   - Change UI design without touching database code

4. **Team Collaboration**
   - Frontend developer works on views
   - Backend developer works on models/controllers
   - No conflicts

### B. Security Measures

**Question:** How do you prevent SQL injection?

**Answer:**
We use **PDO Prepared Statements** with parameter binding:

```php
// VULNERABLE CODE (never use):
$sql = "SELECT * FROM User WHERE UserID = " . $_GET['id'];
// Attacker can inject: ?id=1 OR 1=1

// SECURE CODE (what we use):
$this->db->query("SELECT * FROM User WHERE UserID = :id");
$this->db->bind(':id', $_GET['id'], PDO::PARAM_INT);
// PDO escapes the value, prevents injection
```

**Question:** How do you handle password security?

**Answer:**
We use **bcrypt hashing** with PHP's password functions:

```php
// Registration:
$hash = password_hash($password, PASSWORD_DEFAULT);
// Stores: $2y$10$abc123... (60-character bcrypt hash)

// Login verification:
if(password_verify($inputPassword, $storedHash)) {
    // Password correct
}
```

Benefits:
- **One-way encryption** (cannot reverse to get password)
- **Salt included** (same password = different hashes)
- **Computational cost** (slow brute-force attacks)

### C. AJAX vs Traditional Form Submission

**Question:** Why use AJAX for some operations?

**Answer:**

**Traditional Form:**
```php
<form action="/admin/add_staff" method="POST">
    <!-- fields -->
    <button type="submit">Add</button>
</form>

// Result: Entire page reloads, user loses scroll position
```

**AJAX Approach:**
```javascript
fetch('/admin/add_staff', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if(data.success) {
        // Update only the staff table, no full page reload
        appendNewStaffRow(data.staff);
        closeModal();
    }
});
```

Benefits:
- **Better UX** - No page reload, feels faster
- **Partial updates** - Update only changed content
- **Real-time feedback** - Show loading spinners, error messages
- **Less bandwidth** - Transfer JSON instead of full HTML

### D. Database Relationships

**Question:** Explain the relationship between User and PlayerProfile tables.

**Answer:**

**One-to-One Relationship:**
```
User (Parent)              PlayerProfile (Child)
├─ UserID (PK)  ──────────▶ PlayerID (PK, FK)
├─ Name                     ├─ BattingStyle
├─ Email                    ├─ BowlingStyle
├─ Role = 'Player'          └─ SubscriptionType
└─ ...
```

**Why separate tables?**
1. **Not all users are players** - Admins, Coaches don't need cricket stats
2. **Normalization** - Avoid NULL columns in User table
3. **Flexibility** - Easy to add player-specific fields

**JOIN Query:**
```sql
SELECT 
    u.Name,
    u.Email,
    pp.BattingStyle,
    pp.BowlingStyle
FROM User u
LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
WHERE u.Role = 'Player';
```

**Foreign Key Constraint:**
```sql
ALTER TABLE playerprofile
ADD CONSTRAINT FK_PlayerProfile_User
FOREIGN KEY (PlayerID) REFERENCES User(UserID)
ON DELETE CASCADE;  -- If user deleted, profile also deleted
```

### E. Session Management

**Question:** How does the system remember logged-in users?

**Answer:**

**Session Flow:**
```
1. USER LOGS IN
   ├─ Verify credentials
   └─ Store in PHP session:
      $_SESSION['user_id'] = 15;
      $_SESSION['user_role'] = 'Admin';
      $_SESSION['user_name'] = 'John Doe';

2. BROWSER RECEIVES COOKIE
   Set-Cookie: PHPSESSID=abc123xyz789
   (Browser stores this cookie)

3. SUBSEQUENT REQUESTS
   Browser sends: Cookie: PHPSESSID=abc123xyz789
   ├─ PHP loads session data from server
   └─ $_SESSION array is populated

4. AUTHORIZATION CHECK (every page)
   requireAuth(['Admin']);
   ├─ Checks if $_SESSION['user_id'] exists
   ├─ Checks if $_SESSION['user_role'] == 'Admin'
   └─ If not → redirect to login
```

**Session Security:**
```php
// File: app/helpers/session_helper.php

session_start([
    'cookie_lifetime' => 0,           // Expires when browser closes
    'cookie_httponly' => true,        // Cannot access via JavaScript
    'cookie_secure' => true,          // HTTPS only (production)
    'use_strict_mode' => true         // Reject uninitialized session IDs
]);

// Regenerate ID on login (prevent session fixation)
session_regenerate_id(true);
```

---

## 8. TESTING SCENARIOS FOR VIVA

### Test Case 1: Add New Player

**Steps:**
1. Login as Admin
2. Navigate to `/admin/players`
3. Click "Add Player" button
4. Fill form:
   - Name: Test Player
   - Email: test@player.com
   - Password: Test123!
   - Batting: Right-handed
   - Bowling: Fast
   - Subscription: Premium
5. Click Submit

**Expected Result:**
- New row appears in players table
- Database has 2 new records:
  - `User` table: New user with Role='Player'
  - `playerprofile` table: New profile with cricket details
- Success message shown
- Player can login with credentials

**Verification Queries:**
```sql
-- Check User table
SELECT * FROM User WHERE Email = 'test@player.com';
-- Should return: UserID, Name, Email, Role='Player', Status='active'

-- Check PlayerProfile table (use UserID from above)
SELECT * FROM playerprofile WHERE PlayerID = [UserID];
-- Should return: BattingStyle='Right-handed', BowlingStyle='Fast'
```

### Test Case 2: Filter Reports

**Steps:**
1. Navigate to `/admin/reports`
2. Set filters:
   - Status: Active
   - Subscription: Premium
   - Batting: Right-handed
3. Click "Apply Filters"

**Expected Result:**
- URL becomes: `/admin/reports?status=active&subscription=premium&batting=Right-handed`
- Table shows only players matching ALL filters
- Player count updates: "8 Players" (example)

**SQL Generated:**
```sql
SELECT u.UserID, u.Name, pp.BattingStyle, pp.SubscriptionType
FROM User u
LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
WHERE u.Role = 'Player'
  AND u.Status = 'active'
  AND pp.SubscriptionType = 'premium'
  AND pp.BattingStyle = 'Right-handed'
ORDER BY u.Name ASC;
```

### Test Case 3: Finance Calculation

**Scenario:** Test revenue calculation accuracy

**Database State:**
```sql
-- Product Orders
INSERT INTO productorder VALUES 
(1, 10, 5000.00, 'Card', 'completed', '2026-02-01'),
(2, 11, 3000.00, 'Cash', 'completed', '2026-02-05'),
(3, 12, 2000.00, 'Card', 'pending', '2026-02-10');

-- Subscription Payments
INSERT INTO subscriptionpayment VALUES
(1, 1, 2500.00, 'Card', 'completed', '2026-02-03'),
(2, 2, 2500.00, 'Cash', 'completed', '2026-02-08');
```

**Expected Calculations:**
```
Total Revenue = (5000 + 3000 + 2500 + 2500) = LKR 13,000
                (Excludes pending order of 2000)

Revenue by Category:
- Shop Sales: 5000 + 3000 = 8000 (61.5%)
- Membership Fees: 2500 + 2500 = 5000 (38.5%)

Recent Transactions (5):
1. ORD-3 - LKR 2,000 - pending (2026-02-10)
2. SUB-2 - LKR 2,500 - completed (2026-02-08)
3. ORD-2 - LKR 3,000 - completed (2026-02-05)
4. SUB-1 - LKR 2,500 - completed (2026-02-03)
5. ORD-1 - LKR 5,000 - completed (2026-02-01)
```

**Visit:** `/admin/finance`  
**Verify:** Dashboard shows correct calculations

---

## 9. COMMON VIVA QUESTIONS & ANSWERS

### Q1: What happens when Admin controller is instantiated?

**Answer:**
```php
public function __construct() {
    requireAuth(['Admin']);
}
```

1. PHP calls `session_start()` (via helpers)
2. Checks if `$_SESSION['user_id']` exists
3. Checks if `$_SESSION['user_role']` is 'Admin'
4. If both true: Continue to requested method
5. If false: Redirect to login page

This runs **before** any other method (`dashboard()`, `staff()`, etc.)

### Q2: How do you prevent duplicate emails during registration?

**Answer:**
```php
// Database constraint:
ALTER TABLE User ADD UNIQUE INDEX idx_email (Email);

// Model validation:
public function emailExists($email) {
    $this->db->query('SELECT UserID FROM User WHERE Email = :email');
    $this->db->bind(':email', $email);
    $this->db->single();
    return $this->db->rowCount() > 0;
}

// Controller check:
if($userModel->emailExists($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    return;
}
```

### Q3: Explain the difference between GET and POST in your system.

**Answer:**

**GET Requests (Reading data):**
```php
// URL: /admin/dashboard
// Use: Displaying pages, filtering reports
// Data: In URL query string (?status=active)
// Idempotent: Multiple requests = same result

Examples:
- /admin/dashboard (show dashboard)
- /admin/reports?status=active (filtered report)
- /admin/get_event/5 (fetch event details)
```

**POST Requests (Modifying data):**
```php
// URL: /admin/add_staff
// Use: Creating, updating, deleting records
// Data: In request body (not visible in URL)
// Not idempotent: Each request creates new record

Examples:
- POST /admin/add_staff (create new staff)
- POST /admin/update_player (modify player data)
- POST /admin/delete_event (remove event)
```

### Q4: What is the purpose of the Database.php library?

**Answer:**

**File:** `/app/libraries/Database.php`

**Purpose:** Wrapper around PDO for cleaner syntax

**Without Database class:**
```php
$pdo = new PDO("mysql:host=localhost;dbname=academy", "root", "");
$stmt = $pdo->prepare("SELECT * FROM User WHERE UserID = :id");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_OBJ);
```

**With Database class:**
```php
$this->db->query("SELECT * FROM User WHERE UserID = :id");
$this->db->bind(':id', $userId);
$result = $this->db->single();
```

**Methods:**
```php
query($sql)           // Prepare SQL statement
bind($param, $value)  // Bind parameter (auto-detects type)
execute()            // Execute query
resultSet()          // Return multiple rows as array
single()             // Return single row as object
rowCount()           // Return number of affected rows
lastInsertId()       // Return auto-increment ID after INSERT
```

### Q5: How would you add a new admin feature (e.g., "Reports")?

**Answer:**

**Step 1: Create Model Method**
```php
// File: app/models/M_Users.php
public function getPlayerReport() {
    $this->db->query('SELECT u.Name, pp.BattingStyle 
                      FROM User u 
                      JOIN playerprofile pp ON u.UserID = pp.PlayerID 
                      WHERE u.Role = "Player"');
    return $this->db->resultSet();
}
```

**Step 2: Create Controller Method**
```php
// File: app/controllers/Admin.php
public function reports() {
    $userModel = $this->model('M_Users');
    $players = $userModel->getPlayerReport();
    
    $data = [
        'title' => 'Player Reports',
        'players' => $players
    ];
    
    $this->view('admin/reports', $data);
}
```

**Step 3: Create View**
```php
// File: app/views/admin/reports.php
<?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<h1><?php echo $data['title']; ?></h1>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Batting Style</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data['players'] as $player): ?>
        <tr>
            <td><?php echo $player->Name; ?></td>
            <td><?php echo $player->BattingStyle; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
```

**Step 4: Add Navigation Link**
```php
// File: app/views/inc/components/header.php (admin sidebar)
<li class="nav-item">
    <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
        <i class="fas fa-file-alt"></i>
        <span>Reports</span>
    </a>
</li>
```

**Step 5: Test**
- Visit: `http://localhost/Elite/admin/reports`
- Should display player list

---

## 10. DATABASE ER DIAGRAM

```
┌─────────────────────┐
│       User          │
├─────────────────────┤
│ UserID (PK)         │◀─────┐
│ Name                │      │
│ Email (UNIQUE)      │      │
│ PasswordHash        │      │
│ Role (ENUM)         │      │
│ Status              │      │
│ PhoneNumber         │      │
│ DateOfBirth         │      │
│ DateJoined          │      │
└─────────────────────┘      │
          │                  │
          │ 1                │ N
          │                  │
          ▼                  │
┌─────────────────────┐      │
│   PlayerProfile     │      │
├─────────────────────┤      │
│ PlayerID (PK, FK)   │──────┘
│ BattingStyle        │
│ BowlingStyle        │
│ SubscriptionType    │
│ JerseyNumber        │
│ CoachID (FK)        │──────┐
│ TrainerID (FK)      │      │
└─────────────────────┘      │
                             │
          ┌──────────────────┘
          │
          ▼
┌─────────────────────┐
│     Feedback        │
├─────────────────────┤
│ FeedbackID (PK)     │
│ FromUserID (FK)     │──────▶ User.UserID
│ ToUserID (FK)       │──────▶ User.UserID
│ Content             │
│ Rating              │
│ Category            │
│ Status              │
│ CreatedDate         │
└─────────────────────┘

┌─────────────────────┐
│       Event         │
├─────────────────────┤
│ EventID (PK)        │
│ Title               │
│ Description         │
│ Type                │
│ StartDate           │
│ EndDate             │
│ Location            │
│ Status              │
│ CreatedBy (FK)      │──────▶ User.UserID
└─────────────────────┘

┌──────────────────────┐
│   ProductOrder       │
├──────────────────────┤
│ OrderID (PK)         │
│ PlayerID (FK)        │──────▶ User.UserID
│ TotalAmount          │
│ PaymentMethod        │
│ Status               │
│ OrderDate            │
└──────────────────────┘

┌──────────────────────┐
│ SubscriptionPayment  │
├──────────────────────┤
│ PaymentID (PK)       │
│ SubscriptionID (FK)  │
│ Amount               │
│ PaymentMethod        │
│ Status               │
│ PaymentDate          │
└──────────────────────┘
```

---

## 11. SUMMARY - ADMIN SYSTEM FEATURES

| Feature | URL | Controller | Model | View | Database Tables |
|---------|-----|------------|-------|------|----------------|
| **Dashboard** | `/admin/dashboard` | `Admin::dashboard()` | M_Users, Event, Feedback | dashboard.php | User, Event, Feedback |
| **Staff Management** | `/admin/staff` | `Admin::staff()` | M_Users | staff.php | User |
| **Player Management** | `/admin/players` | `Admin::players()` | M_Users | players.php | User, playerprofile |
| **Event Management** | `/admin/events` | `Admin::events()` | Event | events.php | Event |
| **Feedback** | `/admin/feedback` | `Admin::feedback()` | Feedback | feedback.php | feedback, User |
| **Finance** | `/admin/finance` | `Admin::finance()` | Finance | finance.php | productorder, subscriptionpayment |
| **Reports** | `/admin/reports` | `Admin::reports()` | M_Users | reports.php | User, playerprofile |
| **Profile** | `/admin/profile` | `Admin::profile()` | M_Users | profile.php | User |

---

## 12. KEY TAKEAWAYS FOR VIVA

1. **MVC Architecture**
   - Models handle database
   - Controllers handle logic
   - Views handle presentation

2. **Security**
   - Session-based authentication
   - PDO prepared statements prevent SQL injection
   - Bcrypt password hashing
   - CSRF protection with session validation

3. **Database Design**
   - Normalized tables (no redundancy)
   - Foreign keys maintain referential integrity
   - ENUM types for fixed values (status, role)
   - Indexes on frequently queried columns

4. **User Experience**
   - AJAX for seamless interactions
   - Real-time validation
   - Modal forms (no page reloads)
   - Responsive design (mobile-friendly)

5. **Data Integrity**
   - Auto-update event statuses
   - Transaction logging in ActivityLog
   - Cascade deletes (remove profile when user deleted)
   - Default values for optional fields

6. **Code Quality**
   - DRY principle (Don't Repeat Yourself)
   - Single Responsibility (each method does one thing)
   - Comments explaining "why", not "what"
   - Consistent naming conventions

---

## END OF DOCUMENTATION

**Good luck with your viva!** 🎓

Remember:
- Understand the flow: Browser → Controller → Model → Database → View → Browser
- Know WHY we use each pattern, not just HOW
- Be able to trace code execution for any feature
- Explain security measures confidently
- Show how data moves through the MVC layers
