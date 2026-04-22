# Understanding Your Current System - Visual Guide

## 🏗️ SYSTEM ARCHITECTURE EXPLAINED

### 1. How Your MVC Works

```
┌─────────────────────────────────────────────────────────────┐
│                         USER                                │
│              (Visits: elite.com/player/profile)             │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   public/index.php                          │
│  • Entry point for ALL requests                             │
│  • Loads bootloader.php                                     │
│  • Creates Core object                                      │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                app/libraries/Core.php                       │
│  • Parses URL: player/profile → [player, profile]          │
│  • Loads Controller: Player.php                             │
│  • Calls Method: profile()                                  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              app/controllers/Player.php                     │
│  • Receives request                                         │
│  • Loads Model: M_Users                                     │
│  • Calls model methods to get data                          │
│  • Passes data to View                                      │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                app/models/M_Users.php                       │
│  • Connects to database                                     │
│  • Executes SQL queries                                     │
│  • Returns data to Controller                               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│             app/libraries/Database.php                      │
│  • PDO connection to MySQL                                  │
│  • Executes prepared statements                             │
│  • Returns results                                          │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   MySQL Database                            │
│  • cricket_academy database                                 │
│  • Tables: User, PlayerProfile, etc.                        │
└────────────────────────┬────────────────────────────────────┘
                         │
                    DATA RETURNS
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│           app/views/player/profile.php                      │
│  • Receives data from Controller                            │
│  • Displays HTML with data                                  │
│  • Shown to USER                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 FILE STRUCTURE EXPLAINED

```
Elite/
│
├── public/                     # PUBLIC FOLDER (Web accessible)
│   ├── index.php              # → Entry point for all requests
│   ├── css/                   # → All stylesheets
│   ├── js/                    # → JavaScript files
│   │   ├── login.js          # → Login page functionality
│   │   ├── register.js       # → Registration validation
│   │   └── player/           # → Player-specific JS
│   └── uploads/               # → User uploaded files
│
├── app/                        # APPLICATION FOLDER (NOT web accessible)
│   │
│   ├── bootloader.php         # → Loads all required files
│   │
│   ├── config/                # → Configuration
│   │   └── config.php        # → DB credentials, constants
│   │
│   ├── libraries/             # → Core system classes
│   │   ├── Core.php          # → Router (URL handler)
│   │   ├── Controller.php    # → Base controller class
│   │   └── Database.php      # → Database connection class
│   │
│   ├── controllers/           # → Handle business logic
│   │   ├── Login.php         # → Authentication
│   │   ├── Player.php        # → Player operations
│   │   ├── Coach.php         # → Coach operations
│   │   ├── Admin.php         # → Admin operations
│   │   ├── Trainer.php       # → Trainer operations
│   │   └── Shop.php          # → Shop operations
│   │
│   ├── models/                # → Database interactions
│   │   ├── M_Users.php       # → User data operations
│   │   ├── M_Session.php     # → Session data
│   │   ├── M_Shop.php        # → Shop data
│   │   ├── M_Trainer.php     # → Trainer data
│   │   └── Event.php         # → Event data
│   │
│   ├── views/                 # → HTML templates
│   │   ├── v_login.php       # → Login page
│   │   ├── v_register.php    # → Registration page
│   │   ├── admin/            # → Admin views
│   │   ├── coach/            # → Coach views
│   │   ├── player/           # → Player views
│   │   ├── trainer/          # → Trainer views
│   │   └── shop/             # → Shop views
│   │
│   └── helpers/               # → Utility functions
│       └── session_helper.php # → Session management
│
└── SQL files/                  # → Database setup
    └── cricket_academy_schema.sql
```

---

## 🔄 EXAMPLE: Player Views Their Profile

### Step-by-Step Flow:

**1. Player clicks "Profile" in navbar**
```javascript
// Browser sends request to:
https://elite.local/player/profile
```

**2. index.php receives the request**
```php
// public/index.php
require_once '../app/bootloader.php';
$ini = new Core();  // This starts everything
```

**3. Core.php parses the URL**
```php
// app/libraries/Core.php
$url = ['player', 'profile'];  // Extracted from URL

// Loads Player controller
require_once '../app/controllers/Player.php';
$controller = new Player();

// Calls profile method
$controller->profile();
```

**4. Player Controller processes request**
```php
// app/controllers/Player.php
class Player extends Controller {
    public function profile() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('login');
        }
        
        // Load the User model
        $userModel = $this->model('M_Users');
        
        // Get player data from database
        $player = $userModel->getUserById($_SESSION['user_id']);
        
        // Get player's performance
        $performanceModel = $this->model('M_Performance');
        $stats = $performanceModel->getPlayerStats($_SESSION['user_id']);
        
        // Prepare data for view
        $data = [
            'player' => $player,
            'stats' => $stats
        ];
        
        // Load the view and pass data
        $this->view('player/profile', $data);
    }
}
```

**5. Model fetches data from database**
```php
// app/models/M_Users.php
class M_Users {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getUserById($id) {
        $this->db->query('SELECT * FROM User WHERE UserID = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();  // Returns one row
    }
}
```

**6. Database executes query**
```php
// app/libraries/Database.php
class Database {
    public function query($sql) {
        $this->statement = $this->dbh->prepare($sql);
    }
    
    public function bind($param, $value) {
        $this->statement->bindValue($param, $value);
    }
    
    public function single() {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }
}
```

**7. View displays the data**
```php
// app/views/player/profile.php
<div class="profile-container">
    <h1>Welcome, <?php echo $data['player']->FirstName; ?>!</h1>
    <p>Email: <?php echo $data['player']->Email; ?></p>
    
    <div class="stats">
        <h2>Your Statistics</h2>
        <p>Batting Average: <?php echo $data['stats']->batting_avg; ?></p>
        <p>Wickets: <?php echo $data['stats']->wickets; ?></p>
    </div>
</div>
```

---

## 🎯 WHAT EACH TEAM MEMBER DID

### 1. **Player Module (L.M.V.A.Liyanamana)**
**What's Done:**
- ✅ Player dashboard UI
- ✅ Medical records CRUD
- ✅ Achievement management

**Files Created:**
- `app/views/player/*.php` - All player views
- `app/controllers/Player.php` - Player controller (partial)
- Medical records functionality in controllers

### 2. **Admin Module (E. H. Epa)**
**What's Done:**
- ✅ Admin dashboard UI
- ✅ Event management CRUD
- ✅ User management views

**Files Created:**
- `app/views/admin/*.php` - All admin views
- `app/controllers/Admin.php` - Admin controller
- `app/models/Event.php` - Event model

### 3. **Trainer Module (V.L.Wickramaarachchi)**
**What's Done:**
- ✅ Trainer dashboard UI
- ✅ Workout plans CRUD
- ✅ Nutrition management views

**Files Created:**
- `app/views/trainer/*.php` - All trainer views
- `app/controllers/Trainer.php` - Trainer controller
- `app/models/M_Trainer.php` - Trainer model

### 4. **Shop Module (B.K.M.M.Nandipala)**
**What's Done:**
- ✅ Shop interface UI
- ✅ Product management CRUD
- ✅ Shop dashboard

**Files Created:**
- `app/views/shop/*.php` - All shop views
- `app/controllers/Shop.php` - Shop controller
- `app/models/M_Shop.php` - Shop model

### 5. **Coach Module (G.I.Sumanasekara)**
**What's Done:**
- ✅ Coach dashboard UI
- ✅ Session management CRUD
- ✅ Player management views

**Files Created:**
- `app/views/coach/*.php` - All coach views
- `app/controllers/Coach.php` - Coach controller
- `app/models/M_Session.php` - Session model

---

## 🧩 HOW TO ADD A NEW FEATURE

Let's say you want to add "Player can view their bookings"

### Step 1: Create/Update Model
```php
// app/models/M_Bookings.php (NEW FILE)
class M_Bookings {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getPlayerBookings($player_id) {
        $this->db->query('
            SELECT b.*, c.FirstName as CoachName 
            FROM Bookings b 
            JOIN User c ON b.CoachID = c.UserID 
            WHERE b.PlayerID = :player_id
        ');
        $this->db->bind(':player_id', $player_id);
        return $this->db->resultSet();
    }
}
```

### Step 2: Add Controller Method
```php
// app/controllers/Player.php (EDIT EXISTING FILE)
class Player extends Controller {
    
    // ... existing methods ...
    
    public function bookings() {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('login');
        }
        
        // Load model
        $bookingModel = $this->model('M_Bookings');
        
        // Get data
        $bookings = $bookingModel->getPlayerBookings($_SESSION['user_id']);
        
        // Prepare data
        $data = [
            'bookings' => $bookings,
            'title' => 'My Bookings'
        ];
        
        // Load view
        $this->view('player/bookings', $data);
    }
}
```

### Step 3: Create View
```php
// app/views/player/bookings.php (EDIT EXISTING FILE)
<div class="bookings-container">
    <h1><?php echo $data['title']; ?></h1>
    
    <?php if (!empty($data['bookings'])): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Coach</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['bookings'] as $booking): ?>
                <tr>
                    <td><?php echo $booking->Date; ?></td>
                    <td><?php echo $booking->Time; ?></td>
                    <td><?php echo $booking->CoachName; ?></td>
                    <td><?php echo $booking->Status; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>
```

### Step 4: Add JavaScript (if needed)
```javascript
// public/js/player/bookings.js (EDIT EXISTING FILE)
document.addEventListener('DOMContentLoaded', function() {
    // Add interactivity
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            cancelBooking(bookingId);
        });
    });
});

function cancelBooking(bookingId) {
    // Send AJAX request to cancel
    fetch('/bookings/cancel/' + bookingId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
```

### Step 5: Test
1. Navigate to: `https://elite.local/player/bookings`
2. Check if bookings display
3. Test cancel functionality
4. Verify database updates

---

## 🐛 COMMON ISSUES & SOLUTIONS

### Issue 1: "Page not found" error
**Cause:** Controller or method doesn't exist  
**Solution:** 
1. Check filename matches URL (case-sensitive)
2. Verify method exists in controller
3. Check .htaccess configuration

### Issue 2: "Call to undefined method"
**Cause:** Model method doesn't exist  
**Solution:** 
1. Check model file exists in `app/models/`
2. Verify method name spelling
3. Ensure model is loaded in controller

### Issue 3: Database query returns nothing
**Cause:** SQL error or wrong table/column names  
**Solution:**
1. Check table and column names in database
2. Use var_dump() to debug query results
3. Check error logs

### Issue 4: "Undefined variable" in view
**Cause:** Data not passed from controller  
**Solution:**
1. Verify data is in $data array in controller
2. Check array key names match view usage
3. Use isset() checks in view

---

## 📝 QUICK REFERENCE

### Database Connection (already configured)
```php
// In config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cricket_academy');
```

### Session Helper Functions
```php
isLoggedIn()  // Check if user is authenticated
redirect($page)  // Redirect to another page
```

### Common PDO Methods
```php
$this->db->query($sql);  // Prepare SQL
$this->db->bind($param, $value);  // Bind parameter
$this->db->execute();  // Execute query
$this->db->single();  // Get one row
$this->db->resultSet();  // Get all rows
$this->db->rowCount();  // Get row count
```

---

## 🎯 FOR YOUR VIVA

### Be Ready to Explain:

1. **Why MVC?**
   - Separation of concerns
   - Easier maintenance
   - Team collaboration
   - Code reusability

2. **Your Role:**
   - What module you built
   - Challenges you faced
   - How you solved problems
   - What you learned

3. **Database Design:**
   - Why certain tables exist
   - Relationships between tables
   - Normalization decisions

4. **Security:**
   - How you prevent SQL injection (PDO prepared statements)
   - Session management
   - Password hashing
   - Input validation

---

Good luck with your development! 🚀
