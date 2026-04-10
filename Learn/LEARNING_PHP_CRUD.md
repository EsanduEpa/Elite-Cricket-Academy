# PHP CRUD — Full Learning Report
**File:** `learning_crud.php`  
**Table:** `learning_names` (auto-created on first visit)  
**Purpose:** One self-contained file demonstrating every technology used in this project — PHP, HTML, CSS, JavaScript, and MySQL — so you can change any part and immediately see what happens.

---

## Table of Contents
1. [What is CRUD?](#1-what-is-crud)
2. [How the File is Structured](#2-how-the-file-is-structured)
3. [Technology 1 — MySQL (The Database)](#3-technology-1--mysql-the-database)
4. [Technology 2 — PHP (The Server Brain)](#4-technology-2--php-the-server-brain)
5. [Technology 3 — HTML (The Page Skeleton)](#5-technology-3--html-the-page-skeleton)
6. [Technology 4 — CSS (The Look)](#6-technology-4--css-the-look)
7. [Technology 5 — JavaScript (The Browser Behaviour)](#7-technology-5--javascript-the-browser-behaviour)
8. [The Full Request → Response Journey](#8-the-full-request--response-journey)
9. [Security Concepts Used](#9-security-concepts-used)
10. [Quick Experiments — Change These and See What Happens](#10-quick-experiments--change-these-and-see-what-happens)
11. [Glossary](#11-glossary)

---

## 1. What is CRUD?

CRUD is an acronym for the four fundamental database operations:

| Letter | Operation | SQL Command | What it Does |
|--------|-----------|-------------|--------------|
| **C**  | Create    | `INSERT`    | Add a new row |
| **R**  | Read      | `SELECT`    | Fetch rows and display them |
| **U**  | Update    | `UPDATE`    | Change an existing row |
| **D**  | Delete    | `DELETE`    | Remove a row permanently |

Every web application that stores data (social media, e-commerce, this cricket academy) is built on these four operations.

---

## 2. How the File is Structured

The file is divided into clear zones, top to bottom:

```
learning_crud.php
│
├── PHP ZONE (top)       — server-side logic runs BEFORE the page is sent
│   ├── Database connection
│   ├── Auto-create table
│   ├── Handle POST (Insert / Update)
│   ├── Handle GET  (Delete / Load Edit)
│   └── SELECT all rows (Read)
│
├── HTML HEAD
│   └── CSS ZONE         — all styling lives inside <style> tags
│
└── HTML BODY
    ├── Feedback message (PHP output)
    ├── Insert form      (HTML form → POST)
    ├── Update form      (HTML form → POST, only shown when editing)
    ├── Read table       (PHP loop over SELECT results)
    ├── Tech legend      (static HTML table)
    └── JS ZONE          — all JavaScript lives inside <script> tags
```

This "all-in-one" structure is intentional for learning. In a real project the CSS goes in `.css` files and the JS in `.js` files, but having them all visible in one file makes it easy to see how they connect.

---

## 3. Technology 1 — MySQL (The Database)

### What MySQL Is
MySQL is a **relational database** — it stores data in tables (like Excel spreadsheets), and you talk to it using a language called **SQL** (Structured Query Language).

### The Table Structure

```sql
CREATE TABLE IF NOT EXISTS learning_names (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Column | Type | Meaning |
|--------|------|---------|
| `id` | INT AUTO_INCREMENT | A unique number that increases by 1 for every new row. MySQL manages this automatically. |
| `name` | VARCHAR(100) | A text value, up to 100 characters. |
| `created_at` | TIMESTAMP | The exact date and time the row was inserted. MySQL fills this in automatically. |

`PRIMARY KEY` means `id` is the unique identifier — no two rows can share the same `id`.

### The Four SQL Queries Used

**INSERT — Create**
```sql
INSERT INTO learning_names (name) VALUES ('Kasun')
```
Adds a new row. We only supply `name`; MySQL fills in `id` and `created_at` automatically.

**SELECT — Read**
```sql
SELECT * FROM learning_names ORDER BY id DESC
```
Fetches all rows. `ORDER BY id DESC` means newest row first.

**UPDATE — Update**
```sql
UPDATE learning_names SET name = 'Nuwan' WHERE id = 3
```
Changes the `name` column **only** for the row where `id = 3`. Without `WHERE` this would change every single row — always use `WHERE` with UPDATE and DELETE.

**DELETE — Delete**
```sql
DELETE FROM learning_names WHERE id = 3
```
Removes the row where `id = 3`.

---

## 4. Technology 2 — PHP (The Server Brain)

### What PHP Is
PHP is a **server-side scripting language**. The user's browser never sees PHP code — the PHP engine on the server runs the code, produces plain HTML, and sends that HTML to the browser.

```
Browser                     Server
  │                            │
  │── GET /learning_crud.php ──▶│
  │                            │  PHP runs:
  │                            │  - connects to MySQL
  │                            │  - runs SELECT
  │                            │  - builds HTML string
  │◀── sends finished HTML ────│
  │                            │
```

### Connecting to the Database

```php
$conn = new mysqli('127.0.0.1', 'root', '', 'cricket_academy');
```

`mysqli` is a PHP class ("MySQLi" = MySQL Improved). The four arguments are:
1. Host — where MySQL is running (`127.0.0.1` = this same computer)
2. Username
3. Password
4. Database name

### Detecting What Action the User Wants

PHP uses two **superglobals** — arrays automatically filled by PHP:

| Superglobal | Filled when | Used for |
|-------------|-------------|---------|
| `$_POST` | A form is submitted with `method="POST"` | Insert, Update (send data without showing it in the URL) |
| `$_GET` | The URL has `?key=value` parameters | Delete, load Edit form (simple navigation) |

```php
// Example: user clicked Delete on row id=5
// URL becomes: learning_crud.php?action=delete&id=5
$_GET['action'] === 'delete'   // true
$_GET['id']     === '5'        // true
```

### Prepared Statements (Security)

Instead of putting user input directly into SQL:
```php
// DANGEROUS — never do this:
$conn->query("INSERT INTO learning_names (name) VALUES ('$name')");
```

We use a **prepared statement**:
```php
$stmt = $conn->prepare("INSERT INTO learning_names (name) VALUES (?)");
$stmt->bind_param("s", $name);   // "s" = string type
$stmt->execute();
```

The `?` is a placeholder. `bind_param` fills it in safely, preventing **SQL Injection** attacks (explained in Section 9).

### PHP Inside HTML (Templating)

PHP can be mixed into HTML using `<?php ... ?>` and `<?= ... ?>` tags:

```php
<?= $variable ?>          // short for <?php echo $variable; ?>

<?php if ($editId): ?>
    <p>Editing row <?= $editId ?></p>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
    <tr><td><?= $row['name'] ?></td></tr>
<?php endwhile; ?>
```

`fetch_assoc()` returns one row from a SELECT result as an associative array (keys = column names). When there are no more rows it returns `null` and the while loop stops.

---

## 5. Technology 3 — HTML (The Page Skeleton)

### What HTML Is
HTML (HyperText Markup Language) defines the **structure and content** of the page using **tags**. Tags are instructions to the browser wrapped in `< >`.

### Key HTML Elements Used

#### `<form>` — Sending Data to the Server
```html
<form method="POST" action="">
    <input type="hidden" name="action" value="insert">
    <input type="text"   name="name"   required>
    <button type="submit">Add Name</button>
</form>
```

| Attribute | What it controls |
|-----------|-----------------|
| `method="POST"` | Data is sent in the request body (not visible in URL) |
| `method="GET"` | Data appears in the URL as `?key=value` |
| `action=""` | Empty = submit to the same page (this file handles itself) |
| `type="hidden"` | An invisible field — used to pass `action=insert` to PHP |
| `required` | Browser will not submit if this field is empty |

#### `<table>` — Displaying Rows
```html
<table>
    <thead>          <!-- header row(s) -->
        <tr><th>ID</th><th>Name</th></tr>
    </thead>
    <tbody>          <!-- data rows -->
        <tr><td>1</td><td>Kasun</td></tr>
    </tbody>
</table>
```

`<tr>` = table row, `<th>` = table header cell (bold), `<td>` = table data cell.

#### `<a>` — Hyperlinks for Delete / Edit
```html
<a href="?action=delete&id=5" class="btn btn-danger">Delete</a>
```
Clicking this link navigates to the same page with `?action=delete&id=5` in the URL. PHP reads `$_GET` and performs the deletion.

---

## 6. Technology 4 — CSS (The Look)

### What CSS Is
CSS (Cascading Style Sheets) controls the **visual appearance** — colours, fonts, sizes, spacing, layout. CSS rules follow this pattern:

```css
selector {
    property: value;
}
```

### How CSS is Loaded Here
All CSS is inside a `<style>` tag in the `<head>`. This is called **internal CSS**. The comment blocks show you exactly where to make changes:

```css
/* ── CHANGE THESE COLOURS ──────────────── */
body          { background: #f0f4f8; }   /* page background   */
h1            { color: #1a73e8;      }   /* title colour      */
.btn-primary  { background: #1a73e8; }   /* primary button    */
.btn-danger   { background: #ea4335; }   /* delete button     */
```

### Selectors Explained

| Selector | Example | Targets |
|----------|---------|---------|
| Tag | `table { }` | Every `<table>` element |
| Class | `.btn { }` | Every element with `class="btn"` |
| ID | `#msg { }` | The one element with `id="msg"` |
| Pseudo-class | `tr:hover { }` | A `<tr>` when the mouse hovers over it |
| Nth-child | `tr:nth-child(even) { }` | Every even table row (zebra stripes) |

### The Box Model
Every HTML element is a box:

```
┌─────────────────────────────┐
│          MARGIN             │  ← space outside the border
│  ┌───────────────────────┐  │
│  │        BORDER         │  │  ← the visible border line
│  │  ┌─────────────────┐  │  │
│  │  │    PADDING      │  │  │  ← space inside the border
│  │  │  ┌───────────┐  │  │  │
│  │  │  │  CONTENT  │  │  │  │  ← the actual text/image
│  │  │  └───────────┘  │  │  │
│  │  └─────────────────┘  │  │
│  └───────────────────────┘  │
└─────────────────────────────┘
```

`box-sizing: border-box` (set on `*`) makes `width` include padding and border — it is the modern standard.

### CSS Transitions
```css
.btn { transition: background 0.2s; }
```
When `background` changes (on hover), instead of snapping instantly it animates over 0.2 seconds. Change `0.2s` to `1s` to see a slow colour fade.

---

## 7. Technology 5 — JavaScript (The Browser Behaviour)

### What JavaScript Is
JavaScript (JS) runs **inside the browser**, after the page has loaded. PHP runs on the server; JS runs on the client. They serve different purposes.

### The Three JS Features in This File

#### 1. Delete Confirmation (`confirmDelete`)
```javascript
function confirmDelete(id, name) {
    return confirm('Are you sure you want to delete "' + name + '"?');
}
```

- `confirm()` is a built-in browser function that shows a pop-up with OK / Cancel.
- Returns `true` (OK) or `false` (Cancel).
- The HTML `onclick="return confirmDelete(...)"` will follow the link only if the function returns `true`.
- If the user cancels, `return false` stops the browser from navigating to the delete URL.

#### 2. Auto-hide Message
```javascript
const msg = document.getElementById('msg');
if (msg) {
    setTimeout(function () {
        msg.style.transition = 'opacity 0.8s';
        msg.style.opacity    = '0';
        setTimeout(() => msg.remove(), 800);
    }, 4000);
}
```

- `document.getElementById('msg')` finds the element with `id="msg"` (the feedback message).
- `setTimeout(fn, 4000)` calls `fn` after 4000 milliseconds (4 seconds).
- `msg.style.opacity = '0'` fades the element out (CSS transition handles the animation).
- The second `setTimeout` removes the element from the DOM after the fade completes.

#### 3. Highlight New Row
```javascript
if (msg && msg.textContent.includes('added')) {
    const firstRow = document.querySelector('tbody tr');
    if (firstRow) {
        firstRow.style.background = '#fff9c4';
        firstRow.style.transition = 'background 2s';
        setTimeout(() => firstRow.style.background = '', 2000);
    }
}
```

- `document.querySelector('tbody tr')` finds the first `<tr>` inside `<tbody>`.
- Because rows are ordered `DESC`, the newest row is first.
- Sets a yellow background, then fades it away after 2 seconds.

### The DOM (Document Object Model)
The browser turns HTML into a tree of **objects** called the DOM. JavaScript can read and change that tree:

```
document
└── html
    ├── head
    └── body
        ├── div.container
        │   ├── h1
        │   ├── div.card  (form)
        │   └── div.card  (table)
        │       └── table
        │           └── tbody
        │               ├── tr  ← querySelector('tbody tr') finds this
        │               └── tr
        └── script
```

---

## 8. The Full Request → Response Journey

Here is what happens from the moment you click "Add Name" to the moment you see the updated table:

```
1. You type "Kasun" in the input and click Add Name
   └─ Browser builds a POST request:
      POST /Elite/learning_crud.php
      Body: action=insert&name=Kasun

2. XAMPP receives the request
   └─ Apache web server passes it to the PHP engine

3. PHP starts executing from the top of the file
   ├─ Connects to MySQL on 127.0.0.1
   ├─ Creates the table if missing
   ├─ Detects $_SERVER['REQUEST_METHOD'] === 'POST'
   ├─ Reads $_POST['action'] === 'insert'
   ├─ Reads $_POST['name'] === 'Kasun'
   ├─ Runs: INSERT INTO learning_names (name) VALUES ('Kasun')
   └─ Sets $message = '✅ Name "Kasun" added!'

4. PHP runs SELECT * FROM learning_names ORDER BY id DESC
   └─ MySQL returns all rows including the new one

5. PHP processes the HTML template
   ├─ Outputs the <style> block (CSS)
   ├─ Outputs the message div with $message
   ├─ Outputs the Insert form ($editId is null)
   ├─ Loops over $result and outputs a <tr> for each row
   └─ Outputs the <script> block (JS)

6. PHP sends the finished HTML back to the browser

7. Browser renders the HTML
   ├─ Applies CSS → page looks styled
   └─ Runs JavaScript → message will auto-hide after 4 seconds
```

---

## 9. Security Concepts Used

### SQL Injection Prevention
If you put user input directly into SQL:
```php
// DANGEROUS:
$conn->query("SELECT * FROM users WHERE name = '$name'");
```
A user could type `' OR '1'='1` as their name and break the query.

**Prepared statements** fix this by keeping data and SQL code completely separate:
```php
$stmt = $conn->prepare("INSERT INTO learning_names (name) VALUES (?)");
$stmt->bind_param("s", $name);  // MySQL treats this as data, never as SQL code
$stmt->execute();
```

### XSS (Cross-Site Scripting) Prevention
If a user stores `<script>alert('hacked')</script>` as a name, and you display it raw, it will execute in every visitor's browser.

`htmlspecialchars()` converts dangerous characters to safe HTML entities:
```php
echo htmlspecialchars($row['name']);
// <script>alert('hacked')</script>
// becomes:
// &lt;script&gt;alert(&#039;hacked&#039;)&lt;/script&gt;
// The browser displays it as text, never executes it.
```

### Type Casting
```php
$id = (int) $_GET['id'];
```
`(int)` forces the value to be a whole number. If someone passes `id=5;DROP TABLE learning_names--` it becomes `5`, neutralising the attack.

---

## 10. Quick Experiments — Change These and See What Happens

These are safe changes — nothing will break permanently.

| # | What to Change | Where in the File | Expected Result |
|---|---------------|-------------------|-----------------|
| 1 | `background: #f0f4f8` → `background: #000` | CSS, `body` rule | Page turns black |
| 2 | `color: #1a73e8` → `color: red` | CSS, `h1` rule | Title turns red |
| 3 | `background: #1a73e8` → `background: green` | CSS, `.btn-primary` rule | Add button turns green |
| 4 | `4000` → `1000` | JS, setTimeout delay | Message disappears in 1 second |
| 5 | `background: #1a73e8` → `background: purple` | CSS, `thead th` rule | Table header turns purple |
| 6 | `ORDER BY id DESC` → `ORDER BY id ASC` | PHP, SELECT query | Oldest name shows first |
| 7 | `'Are you sure...'` → `'Really delete?'` | JS, confirm() message | Delete pop-up text changes |
| 8 | `border-radius: 10px` → `border-radius: 0` | CSS, `.card` rule | Cards become sharp rectangles |
| 9 | `font-family: 'Segoe UI'` → `font-family: monospace` | CSS, `body` rule | All text becomes monospace |
| 10 | `transition: background 0.2s` → `transition: background 2s` | CSS, `.btn` rule | Button hover colour changes very slowly |

---

## 11. Glossary

| Term | Plain English |
|------|---------------|
| **PHP** | A programming language that runs on the server and generates HTML |
| **MySQL** | A database program that stores data in tables |
| **SQL** | The language used to communicate with MySQL (SELECT, INSERT, UPDATE, DELETE) |
| **HTML** | The markup language that defines the structure of a web page |
| **CSS** | The styling language that controls colours, fonts, layout |
| **JavaScript** | A programming language that runs in the browser and adds interactivity |
| **CRUD** | Create, Read, Update, Delete — the four basic database operations |
| **GET request** | A browser request where data is visible in the URL |
| **POST request** | A browser request where data is sent in the body (not in the URL) |
| **Prepared statement** | A secure way to run SQL that keeps user input separate from the query |
| **htmlspecialchars** | A PHP function that converts `<` and `>` to safe text so scripts can't inject |
| **DOM** | The browser's internal tree representation of the HTML page, which JS can read and modify |
| **Superglobal** | A PHP variable (`$_POST`, `$_GET`) automatically available everywhere |
| **VARCHAR** | A MySQL data type for text with a maximum length |
| **AUTO_INCREMENT** | A MySQL feature that automatically increases the `id` by 1 for each new row |
| **TIMESTAMP** | A MySQL data type storing date and time |
| **Primary Key** | A column whose value is unique for every row — used to identify a specific row |
| **`fetch_assoc()`** | A PHP function that gets one row from a query result as a key→value array |
| **Box model** | The CSS concept that every element is a box with content, padding, border, and margin |
| **Transition** | A CSS feature that animates property changes smoothly over time |
| **`setTimeout`** | A JavaScript function that runs a function after a given delay in milliseconds |
| **XSS** | Cross-Site Scripting — an attack where malicious scripts are injected into a page |
| **SQL Injection** | An attack where malicious SQL code is inserted through user input |
