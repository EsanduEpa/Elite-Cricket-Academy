# 🏗️ Elite Cricket Academy - Registration Process Architecture

## 📋 **MVC Architecture Overview**

The Elite Cricket Academy uses a **Model-View-Controller (MVC)** pattern with additional layers for routing, database abstraction, and session management.

---

## 🔄 **Complete Registration Flow**

### **1. 🌐 Request Initiation**
```
User Browser → http://localhost/Elite/register
```

**What happens:**
- User clicks "Enroll Now" or navigates to `/register`
- Browser sends HTTP GET/POST request to web server
- Apache/XAMPP routes request to `public/index.php`

---

### **2. 🚀 Application Bootstrap**
**File:** `public/index.php`
```php
<?php
require_once '../app/bootloader.php';
$ini = new Core();
?>
```

**Process:**
1. **Loads bootloader** - Includes all necessary files
2. **Instantiates Core** - Starts the routing system

---

### **3. 📦 Bootloader Initialization**
**File:** `app/bootloader.php`
```php
require_once 'config/config.php';        // Database config
require_once 'libraries/Core.php';       // Router
require_once 'libraries/Database.php';   // DB abstraction
require_once 'libraries/Controller.php'; // Base controller
require_once 'helpers/session_helper.php'; // Session management
```

**What's loaded:**
- ✅ **Configuration** (database credentials)
- ✅ **Core router** (URL parsing)
- ✅ **Database layer** (PDO wrapper)
- ✅ **Controller base class**
- ✅ **Session helpers** (flash messages, redirects)

---

### **4. 🎯 URL Routing & Controller Loading**
**File:** `app/libraries/Core.php`

**URL Pattern:** `/controller/method/params`

For `/register`:
```php
// URL breakdown: ['register']
$this->currentController = 'Register';  // Default method: 'index'
```

**Process:**
1. **Parses URL** → Identifies controller: `Register`
2. **Checks if controller exists** → `app/controllers/Register.php`
3. **Loads controller file**
4. **Instantiates controller** → `new Register()`
5. **Calls method** → `index()` (default)

---

### **5. 🎮 Controller Initialization**
**File:** `app/controllers/Register.php`

```php
class Register extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('M_Users');
    }
```

**Process:**
1. **Extends base Controller** class
2. **Loads M_Users model** via `$this->model()` helper
3. **Ready to handle requests**

---

### **6A. 👀 GET Request (Display Form)**

When user first visits `/register`:

```php
public function index() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle form submission
    } else {
        // Display registration form
        $data = [
            'fullName' => '',
            'email' => '',
            // ... all fields empty
            'fullName_err' => '',
            'email_err' => '',
            // ... all errors empty
        ];
        
        $this->view('v_register', $data);
    }
}
```

**Process:**
1. **Checks request method** → GET
2. **Initializes empty data array**
3. **Calls view helper** → `$this->view('v_register', $data)`

---

### **6B. 📝 View Rendering**
**File:** `app/views/v_register.php`

```php
<form method="POST" action="">
    <input type="text" name="fullName" value="<?php echo $data['fullName']; ?>">
    <div class="error-message <?php echo (!empty($data['fullName_err'])) ? 'show' : ''; ?>">
        <?php echo $data['fullName_err']; ?>
    </div>
</form>
```

**Process:**
1. **Renders HTML form** with PHP data integration
2. **Shows/hides error messages** based on data
3. **Populates form fields** with previous values (on validation errors)
4. **Sends HTML to browser**

---

### **7. 📤 POST Request (Form Submission)**

When user submits the form:

```php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form submission
}
```

**Process Flow:**

#### **7A. Data Sanitization**
```php
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$data = [
    'fullName' => trim($_POST['fullName']),
    'email' => trim($_POST['email']),
    // ... collect all form fields
];
```

#### **7B. Server-Side Validation**
```php
// Validate each field
if(empty($data['fullName'])) {
    $data['fullName_err'] = 'Please enter your full name';
}

if(empty($data['email'])) {
    $data['email_err'] = 'Please enter your email';
} elseif($this->userModel->findUserByEmail($data['email'])) {
    $data['email_err'] = 'Email is already taken';
}
```

**Validation includes:**
- ✅ **Required field checks**
- ✅ **Email uniqueness** (database query)
- ✅ **Username uniqueness** (database query)
- ✅ **Password strength**
- ✅ **Password confirmation match**

---

### **8. 🗄️ Model Layer (Database Operations)**

#### **8A. Email/Username Uniqueness Check**
**File:** `app/models/M_Users.php`

```php
public function findUserByEmail($email) {
    $this->db->query('SELECT * FROM user WHERE email = :email');
    $this->db->bind(':email', $email);
    $row = $this->db->single();
    
    return $this->db->rowCount() > 0;
}
```

**Database Layer Process:**
1. **Prepares SQL statement** (prevents SQL injection)
2. **Binds parameters** safely
3. **Executes query**
4. **Returns boolean result**

#### **8B. User Registration (Insert)**
If validation passes:

```php
// Hash password
$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

// Register user
if($this->userModel->register($data)) {
    flash('register_success', 'Registration successful!');
    redirect('login');
}
```

**Database Insert Process:**
```php
public function register($data) {
    $this->db->query('INSERT INTO user (full_name, date_of_birth, address, email, contact_number, school, username, password, created_at) VALUES(:full_name, :date_of_birth, :address, :email, :contact_number, :school, :username, :password, NOW())');
    
    // Bind all values
    $this->db->bind(':full_name', $data['fullName']);
    $this->db->bind(':email', $data['email']);
    // ... bind all fields
    
    return $this->db->execute();
}
```

---

### **9. 🗃️ Database Layer Abstraction**
**File:** `app/libraries/Database.php`

```php
class Database {
    private $dbh;  // PDO connection
    private $statement;
    
    public function __construct() {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $this->dbh = new PDO($dsn, DB_USER, DB_PASS);
    }
    
    public function query($sql) {
        $this->statement = $this->dbh->prepare($sql);
    }
    
    public function bind($param, $value, $type = null) {
        $this->statement->bindValue($param, $value, $type);
    }
    
    public function execute() {
        return $this->statement->execute();
    }
}
```

**Database Security Features:**
- ✅ **Prepared statements** (SQL injection prevention)
- ✅ **Parameter binding**
- ✅ **PDO abstraction**
- ✅ **Error handling**

---

### **10. 📬 Response Handling**

#### **10A. Success Path**
```php
if($this->userModel->register($data)) {
    flash('register_success', 'Registration successful!');
    redirect('login');
}
```

**Process:**
1. **Sets flash message** in session
2. **Redirects to login** page
3. **Flash message displays** on login page
4. **Session message cleared** after display

#### **10B. Validation Error Path**
```php
} else {
    // Load view with errors
    $this->view('v_register', $data);
}
```

**Process:**
1. **Re-renders registration form**
2. **Shows validation errors**
3. **Retains user input** in form fields
4. **User can correct and resubmit**

---

## 🏗️ **Architecture Components**

### **📁 File Structure**
```
Elite/
├── public/
│   ├── index.php           # Entry point
│   ├── css/register.css    # Styling
│   └── js/register.js      # Client validation
├── app/
│   ├── bootloader.php      # Load all components
│   ├── config/
│   │   └── config.php      # Database config
│   ├── controllers/
│   │   └── Register.php    # Registration logic
│   ├── models/
│   │   └── M_Users.php     # User database operations
│   ├── views/
│   │   └── v_register.php  # Registration form HTML
│   ├── libraries/
│   │   ├── Core.php        # Router
│   │   ├── Database.php    # DB abstraction
│   │   └── Controller.php  # Base controller
│   └── helpers/
│       └── session_helper.php # Flash messages, redirects
```

### **🔗 Component Interactions**

```
Browser Request
    ↓
index.php (Entry Point)
    ↓
bootloader.php (Load Components)
    ↓
Core.php (Router)
    ↓
Register.php (Controller)
    ↓
M_Users.php (Model)
    ↓
Database.php (DB Layer)
    ↓
MySQL Database
    ↓
Response to Browser
```

### **🛡️ Security Layers**

1. **Input Sanitization** - `filter_input_array()`
2. **Data Validation** - Server-side checks
3. **SQL Injection Prevention** - Prepared statements
4. **Password Security** - `password_hash()` with bcrypt
5. **Session Security** - Secure session handling
6. **CSRF Protection** - Form tokens (can be added)

### **⚡ Performance Features**

- **Single Entry Point** - All requests through index.php
- **Lazy Loading** - Models loaded only when needed
- **Connection Pooling** - PDO persistent connections
- **Prepared Statements** - Query compilation caching

---

## 🎯 **Key Benefits of This Architecture**

✅ **Separation of Concerns** - Logic, data, presentation separated  
✅ **Reusability** - Controllers and models can be reused  
✅ **Security** - Multiple layers of protection  
✅ **Maintainability** - Clear file organization  
✅ **Scalability** - Easy to add new features  
✅ **Testability** - Components can be tested individually  

This MVC architecture provides a robust, secure, and maintainable foundation for the Elite Cricket Academy application! 🏏