# Elite Cricket Academy — Beginner Learning Report

**Level:** Beginner  
**Goal:** Understand how this PHP web application is built, how the code is organized, and how to safely make simple changes  
**Prerequisite:** Basic awareness of HTML and the idea that servers run code — no PHP experience needed

---

## Table of Contents

1. [What Is This Project?](#1-what-is-this-project)
2. [What Is PHP?](#2-what-is-php)
3. [What Is MVC?](#3-what-is-mvc)
4. [How the App Starts](#4-how-the-app-starts)
5. [How URLs Work in This App](#5-how-urls-work-in-this-app)
6. [Controllers — The Traffic Manager](#6-controllers--the-traffic-manager)
7. [Models — The Data Layer](#7-models--the-data-layer)
8. [Views — The Display Layer](#8-views--the-display-layer)
9. [The Database Class — Talking to MySQL](#9-the-database-class--talking-to-mysql)
10. [Sessions and Helpers](#10-sessions-and-helpers)
11. [Constants — App-Wide Settings](#11-constants--app-wide-settings)
12. [Your First Edit — A Guided Exercise](#12-your-first-edit--a-guided-exercise)

---

## 1. What Is This Project?

**Elite Cricket Academy** is a website for managing a cricket training academy. It lets players register, book training slots, buy equipment from a shop, and track their performance. Coaches and admins can manage schedules, memberships, and reports.

The whole website is written in **PHP** and runs on a **local server** using XAMPP. All the data (users, bookings, products) is stored in a **MySQL database** called `cricket_academy`.

Here is the big picture of the folder structure:

```
Elite/
├── public/         ← The only folder visible to the browser
│   ├── index.php   ← Entry point: EVERY page request starts here
│   ├── css/        ← Stylesheets
│   └── js/         ← JavaScript files
└── app/            ← All the PHP logic (hidden from browser)
    ├── config/     ← App settings (database credentials, URLs)
    ├── controllers/ ← Handle requests and decisions
    ├── models/      ← Talk to the database
    ├── views/       ← HTML templates shown to the user
    ├── libraries/   ← Core framework classes
    └── helpers/     ← Small utility functions
```

> **Key rule:** Visitors can only access files inside `public/`. Everything in `app/` is protected and runs only on the server.

---

## 2. What Is PHP?

PHP is a **server-side scripting language**. This means the code runs on the web server (your XAMPP machine), not inside the visitor's browser.

Here is the difference:

```
Visitor's Browser                    Your Server (XAMPP)
─────────────────                    ───────────────────
Types: localhost/Elite/player/cart
                              ──►    PHP runs, queries database,
                                     builds an HTML page
                              ◄──    Sends finished HTML to browser
Browser shows the HTML page
(PHP code is NEVER sent to browser)
```

PHP code lives inside `<?php ... ?>` tags:

```php
<?php
// This is a comment
$name = "Elite Cricket Academy";   // Variable (starts with $)
echo $name;                         // Prints: Elite Cricket Academy
?>
```

---

## 3. What Is MVC?

MVC stands for **Model – View – Controller**. It is a way of organizing code so that each part has one clear job.

```
User visits a URL
       │
       ▼
  CONTROLLER          ← Decides what to do (the "brain")
  Player.php          
       │
       ├──► MODEL     ← Fetches data from the database
       │    M_Shop.php
       │         │
       │         ▼
       │    Database result (list of products)
       │
       └──► VIEW      ← Builds the HTML the user sees
            cart.php
```

Think of it like a restaurant:
- **Controller** = The waiter — takes your order, passes it to the kitchen, brings you the result
- **Model** = The chef — does the actual work (cooking = querying the database)
- **View** = The plate presentation — what the customer actually sees

---

## 4. How the App Starts

Every single page visit — no matter what URL the user types — begins at one file:

### `public/index.php`

```php
<?php
require_once '../app/bootloader.php';
$ini = new Core();
```

That is the entire file. Just two lines of real code. It does two things:
1. Loads `bootloader.php` which sets up everything
2. Creates a `new Core()` object that handles routing

### `app/bootloader.php`

```php
<?php
require_once 'config/config.php';
require_once 'libraries/Core.php';
require_once 'libraries/Database.php';
require_once 'libraries/Controller.php';
require_once 'helpers/session_helper.php';
```

`require_once` means "load this file one time." If PHP did not find one of these files, the whole app would stop and show an error. The bootloader loads files in this specific order because later files depend on earlier ones (e.g., `Database.php` needs the credentials from `config.php`).

### The startup chain, visualized:

```
Browser request
      │
      ▼
public/index.php            (2 lines)
      │
      ├── app/bootloader.php (loads all libraries)
      │       ├── config/config.php      (DB credentials, URLROOT)
      │       ├── libraries/Core.php     (URL router)
      │       ├── libraries/Database.php (PDO wrapper)
      │       ├── libraries/Controller.php (base controller)
      │       └── helpers/session_helper.php (login helpers)
      │
      └── new Core()         (runs the router)
```

---

## 5. How URLs Work in This App

### The `.htaccess` trick

The file `public/.htaccess` tells Apache web server to route ALL requests through `index.php`:

```
http://localhost/Elite/player/cart
                ↓ .htaccess rewrites this to ↓
http://localhost/Elite/index.php?url=player/cart
```

So the URL `player/cart` becomes the query string `?url=player/cart`.

### How `Core.php` reads the URL

```php
public function getURL() {
    if(isset($_GET['url'])) {
        $url = rtrim($_GET['url'], '/');          // Remove trailing slash
        $url = filter_var($url, FILTER_SANITIZE_URL); // Block bad characters
        $url = explode('/', $url);                // Split by /
        return $url;
    }
}
```

For the URL `player/cart`, `explode('/', ...)` produces:

```php
$url = ['player', 'cart'];
//      [0]        [1]
//   controller   method
```

### How Core decides which controller and method to run

```php
public function __construct() {
    $url = $this->getURL();

    // Step 1: Find the controller
    // url[0] = 'player' → looks for app/controllers/Player.php
    if($url && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
        $this->currentController = ucwords($url[0]); // 'Player'
        unset($url[0]);
    }

    // Step 2: Load and instantiate the controller
    require_once '../app/controllers/' . $this->currentController . '.php';
    $this->currentController = new $this->currentController; // new Player()

    // Step 3: Find the method
    // url[1] = 'cart' → calls Player::cart()
    if(isset($url[1])) {
        if(method_exists($this->currentController, $url[1])) {
            $this->currentMethod = $url[1]; // 'cart'
        }
    }

    // Step 4: Call the method
    call_user_func_array([$this->currentController, $this->currentMethod], $this->param);
}
```

### URL examples

| URL typed by user | Controller file | Method called |
|---|---|---|
| `/Elite/` | `Home.php` | `index()` |
| `/Elite/player/cart` | `Player.php` | `cart()` |
| `/Elite/login` | `Login.php` | `index()` |
| `/Elite/admin/dashboard` | `Admin.php` | `dashboard()` |
| `/Elite/shop/product/42` | `Shop.php` | `product('42')` |

> If a controller or method is not found, the default is `Home::index()`.

---

## 6. Controllers — The Traffic Manager

All controllers live in `app/controllers/`. Every controller **extends Controller** (the base class).

### What the base `Controller.php` provides

```php
class Controller {
    // Load a Model
    public function model($model) {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    // Load a View
    public function view($view, $data = []) {
        if(file_exists(APPROOT . '/views/' . $view . '.php')) {
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
}
```

- `$this->model('M_Shop')` → loads `app/models/M_Shop.php` and returns a new `M_Shop` object
- `$this->view('v_home', $data)` → loads `app/views/v_home.php` and passes `$data` to it

### A real controller: `Home.php`

```php
class Home extends Controller {

    // Models are stored as properties
    private $eventModel;
    private $userModel;
    private $shopModel;
    private $feedbackModel;

    // Constructor runs first, before any method
    public function __construct() {
        // Load all models this controller needs
        $this->eventModel    = $this->model('M_Events');
        $this->userModel     = $this->model('M_Users');
        $this->shopModel     = $this->model('M_Shop');
        $this->feedbackModel = $this->model('M_Feedback');
    }

    // Handles: GET /Elite/
    public function index() {
        // 1. Collect data from models
        $data = [
            'programs'   => $this->getHomePrograms(),
            'homeStats'  => $this->userModel->getHomeStats(),
            'events'     => $this->eventModel->getUpcomingEvents(3),
            'products'   => $this->shopModel->getFeaturedProducts(3),
            'feedback'   => $this->feedbackModel->getApprovedFeedback(3),
        ];

        // 2. Send data to the view
        $this->view('v_home', $data);
    }
}
```

**Pattern to remember:**
1. Constructor loads models
2. Method collects data into `$data` array
3. Method calls `$this->view('viewname', $data)`

### How Login.php handles a form submission

```php
class Login extends Controller {
    public function index() {
        // Already logged in? Send them to their dashboard
        if(isLoggedIn()) {
            $role = $_SESSION['user_role'];
            if($role === 'admin') redirect('admin/dashboard');
            if($role === 'player') redirect('player/dashboard');
            // ... etc
        }

        // GET request → show the empty login form
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->view('v_login');
        }

        // POST request → process the submitted form
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $user = $this->userModel->getUserByUsername($username);

            if($user && password_verify($password, $user->Password)) {
                // Store user info in session
                $_SESSION['user_id']   = $user->UserID;
                $_SESSION['user_role'] = $user->Role;
                $_SESSION['username']  = $user->Username;

                redirect('player/dashboard');
            } else {
                flash('login_error', 'Invalid credentials', 'alert-danger');
                $this->view('v_login');
            }
        }
    }
}
```

> **Key concept:** `$_SERVER['REQUEST_METHOD']` tells you whether the browser is loading a page (`GET`) or submitting a form (`POST`).

---

## 7. Models — The Data Layer

Models live in `app/models/`. Their only job is to **talk to the database**. No HTML, no session logic — just SQL queries.

### A real model method: `M_Users.php`

```php
class M_Users {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Connect to MySQL
    }

    public function getUserByUsername($username) {
        $this->db->query('SELECT * FROM User WHERE Username = :username');
        $this->db->bind(':username', $username);
        return $this->db->single(); // Returns one row as stdClass object
    }

    public function register($data) {
        $this->db->query(
            'INSERT INTO User (FullName, Email, Username, Password)
             VALUES (:name, :email, :username, :password)'
        );
        $this->db->bind(':name',     $data['fullName']);
        $this->db->bind(':email',    $data['email']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']); // Already hashed by controller
        return $this->db->execute();
    }
}
```

> **Why use `:username` instead of putting the value directly in the SQL?**  
> This is called a **prepared statement**. It prevents **SQL injection** — a common attack where someone types SQL code into a form to manipulate your database. Never write `"WHERE Username = '$username'"`.

---

## 8. Views — The Display Layer

Views live in `app/views/`. They are mostly HTML files with PHP mixed in to display dynamic data.

### How data gets to a view

**In the controller:**
```php
$data = [
    'programs'  => [...],  // array of program objects
    'homeStats' => [...],
];
$this->view('v_home', $data);
```

**Inside `Controller.php`'s `view()` method:**
```php
public function view($view, $data = []) {
    require_once APPROOT . '/views/' . $view . '.php';
    // $data is now available inside the view file
}
```

**In the view file (`v_home.php`):**
```php
<?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<!-- Display a count from $data -->
<p>
    <?php echo (int)($data['homeStats']['program_count'] ?? 0); ?> active programs
</p>

<!-- Loop through an array -->
<?php foreach ($data['programs'] as $program): ?>
    <div class="program-card">
        <h3><?php echo htmlspecialchars($program['name']); ?></h3>
        <p><?php echo htmlspecialchars($program['description']); ?></p>
        <p><strong>Monthly Fee:</strong> Rs. <?php echo number_format($program['monthly_fee'], 2); ?></p>
    </div>
<?php endforeach; ?>
```

### Safety functions used in views

| Function | Purpose | Example |
|---|---|---|
| `htmlspecialchars()` | Prevents XSS attacks by escaping `<`, `>`, `"` | `htmlspecialchars($user->Name)` |
| `(int)` | Forces a value to be a whole number | `(int)$data['count']` |
| `number_format()` | Formats a number with commas and decimals | `number_format(1999.5, 2)` → `1,999.50` |
| `?? 0` | "If this is null or missing, use 0 instead" | `$data['count'] ?? 0` |

> **Always use `htmlspecialchars()` when echoing anything that came from a user or the database.** Skipping this is a security risk.

---

## 9. The Database Class — Talking to MySQL

The file `app/libraries/Database.php` is a wrapper around PHP's built-in **PDO** (PHP Data Objects) system. You never call PDO directly — you always use this class.

### How the connection is made

```php
class Database {
    private $dbh;    // Database Handle

    public function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $this->dbh = new PDO($dsn, DB_USER, DB_PASS);
        $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }
}
```

`PDO::FETCH_OBJ` means results come back as **objects**, not arrays. So you write `$user->Username`, NOT `$user['Username']`.

### The four steps to run a query

```php
// STEP 1: Write the SQL with placeholders
$this->db->query('SELECT * FROM User WHERE UserID = :id');

// STEP 2: Bind actual values to placeholders
$this->db->bind(':id', 5);

// STEP 3a: Get one row
$user = $this->db->single();

// STEP 3b: Get all rows
$users = $this->db->resultSet();

// STEP 3c: Insert/Update/Delete (no result)
$success = $this->db->execute();
```

### Accessing the result

```php
$user = $this->db->single();

// Correct: use -> (object notation)
echo $user->Username;
echo $user->Email;

// WRONG: do not use array notation
// echo $user['Username'];  // This causes an error
```

When you get multiple rows (`resultSet()`), you get an **array of objects**:

```php
$products = $this->db->resultSet();

foreach ($products as $product) {
    echo $product->ProductName;    // object notation
    echo $product->Price;
}
```

---

## 10. Sessions and Helpers

### What is a session?

A **session** stores information about the current user across multiple page visits. Without it, the server would forget who you are every time you click a link.

PHP stores session data in a `$_SESSION` superglobal (a special built-in variable):

```php
// When the user logs in:
$_SESSION['user_id']   = $user->UserID;   // e.g., 7
$_SESSION['user_role'] = $user->Role;     // e.g., 'player'
$_SESSION['username']  = $user->Username; // e.g., 'john_doe'

// On any later page:
echo $_SESSION['username'];  // Prints: john_doe
```

### Helper functions in `session_helper.php`

These functions are available everywhere in the app because `bootloader.php` loads the file before anything else runs.

**`isLoggedIn()`** — Check if someone is logged in:
```php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Usage:
if(!isLoggedIn()) {
    redirect('login');  // Send them to the login page
}
```

**`hasRole($role)`** — Check what type of user this is:
```php
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Usage:
if(hasRole('admin')) {
    // Show admin-only content
}
```

**`redirect($page)`** — Send the user to another page:
```php
function redirect($page) {
    header('location: ' . URLROOT . '/' . $page);
    exit();
}

// Usage:
redirect('player/dashboard');
// Same as going to: http://localhost/Elite/player/dashboard
```

**`flash($name, $message, $class)`** — Show a one-time notification message:
```php
// In the controller — store the message:
flash('login_error', 'Wrong username or password', 'alert-danger');
redirect('login');

// In the view — display and automatically delete it:
flash('login_error');
```

The message only appears once. After it is displayed, it disappears automatically.

---

## 11. Constants — App-Wide Settings

Constants are defined in `app/config/config.php` and are available everywhere in the app:

```php
define('DB_HOST', '127.0.0.1');       // MySQL server address
define('DB_USER', 'root');             // MySQL username
define('DB_PASS', '');                 // MySQL password (empty for XAMPP)
define('DB_NAME', 'cricket_academy'); // Database name

define('APPROOT', dirname(dirname(__FILE__))); // Full path to /app folder
define('URLROOT', 'http://localhost/Elite');   // Base URL of the website
define('SITENAME', 'Elite-Cricket-Academy');

define('DEV_MODE', true); // ⚠️ MUST be false in production!
```

### How constants are used

```php
// In views — building links:
echo '<a href="' . URLROOT . '/player/cart">View Cart</a>';
// Outputs: <a href="http://localhost/Elite/player/cart">View Cart</a>

// In libraries — loading files:
require_once APPROOT . '/models/M_Users.php';
// Outputs path: /Applications/XAMPP/.../Elite/app/models/M_Users.php
```

> **Never hardcode `http://localhost/Elite` directly in your code.** Always use `URLROOT`. This way, if the app moves to a different server, you only change one line in `config.php`.

---

## 12. Your First Edit — A Guided Exercise

Let's make a simple, safe change so you can see how everything connects. We will update the "About" section text on the home page.

### Task: Add a sentence to the About section

**File to edit:** `app/views/v_home.php`

**Step 1:** Open the file and find the About section (around line 15):

```php
<section class="about">
    <h2>About Elite Cricket Academy</h2>
    <p>Elite Cricket Academy is dedicated to nurture the next generation...</p>
    ...
</section>
```

**Step 2:** Add one new paragraph before the closing `</section>`:

```php
    <p>We are always looking for dedicated players who want to improve their game.</p>
</section>
```

**Step 3:** Visit `http://localhost/Elite/` in your browser — you should see your new text.

**What happened? The full request lifecycle:**

```
1. Browser:    GET http://localhost/Elite/
2. .htaccess:  Rewrites to public/index.php?url=
3. index.php:  Loads bootloader, creates new Core()
4. Core.php:   url is empty → default controller=Home, method=index
5. Home.php:   index() builds $data array
6. Home.php:   calls $this->view('v_home', $data)
7. v_home.php: renders HTML including YOUR new paragraph
8. Browser:    displays the finished page
```

---

### Try It Yourself Checklist

After reading this guide, test your understanding:

- [ ] What file does every request start at?
- [ ] What does `Core.php` read to decide which controller to use?
- [ ] If someone visits `/Elite/shop/product/5`, which controller file and method are called?
- [ ] Why do we write `:username` in SQL instead of the variable directly?
- [ ] What is the difference between `single()` and `resultSet()`?
- [ ] What does `htmlspecialchars()` protect against?
- [ ] How do you check if a user is logged in?
- [ ] What does `DEV_MODE = true` do and why is it dangerous in production?

---

## Summary Table

| Concept | File(s) | In Plain English |
|---|---|---|
| Entry point | `public/index.php` | Every request starts here |
| App setup | `app/bootloader.php` | Loads all necessary files at startup |
| URL routing | `app/libraries/Core.php` | Reads the URL, picks the right controller |
| Base controller | `app/libraries/Controller.php` | Provides `model()` and `view()` methods |
| Database access | `app/libraries/Database.php` | Run SQL queries safely |
| Session/auth helpers | `app/helpers/session_helper.php` | `isLoggedIn()`, `redirect()`, `flash()` |
| App settings | `app/config/config.php` | DB credentials, `URLROOT`, `DEV_MODE` |
| Your controllers | `app/controllers/*.php` | Handle each page's logic |
| Your models | `app/models/*.php` | SQL queries for one area of the app |
| Your views | `app/views/*.php` | HTML templates shown to the user |

---

*Next: See the **Intermediate Learning Report** for deeper topics — models in detail, form handling, role-based access, and the slot booking system.*
