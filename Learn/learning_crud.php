<?php
/*
╔══════════════════════════════════════════════════════════════════╗
║          LEARNING FILE — PHP + HTML + CSS + JavaScript          ║
║          Topic  : CRUD (Create, Read, Update, Delete)           ║
║          Table  : learning_names  (only used for this demo)     ║
║          Setup  : Run on XAMPP  →  visit localhost/Elite/       ║
║                   learning_crud.php  in your browser            ║
╚══════════════════════════════════════════════════════════════════╝

  HOW THIS FILE WORKS
  ───────────────────
  1. PHP runs on the SERVER (your computer via XAMPP) before the
     page is sent to the browser.  The browser never sees PHP code.
  2. HTML defines the structure of the page (forms, tables, buttons).
  3. CSS (inside <style>) makes it look nice.
  4. JavaScript (inside <script>) adds browser-side behaviour.
  5. MySQL stores the data permanently in a database table.

  HOW TO USE
  ──────────
  • Open http://localhost/Elite/learning_crud.php in your browser.
  • Type a name and click Add Name  → INSERT
  • See the list below                → SELECT (Read)
  • Click Edit on a row              → UPDATE
  • Click Delete on a row            → DELETE
*/


/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   STEP 1 — CONNECT TO THE DATABASE
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   Before doing anything we need to open a "channel" to MySQL.
   Think of this like calling a phone number before talking.

   mysqli = "MySQL Improved" — PHP's built-in class for talking to MySQL.
   new mysqli(...) creates a connection object and stores it in $conn.
   We call methods on $conn  (e.g.  $conn->query(...))  to run SQL.
*/

$host   = '127.0.0.1';       // where MySQL is running
                              // 127.0.0.1 = "this same computer" (localhost)

$user   = 'root';             // MySQL username (XAMPP default is 'root')

$pass   = '';                 // MySQL password (XAMPP default is empty '')

$dbname = 'cricket_academy';  // which database to use
                              // a database is a collection of tables
                              // (like a folder full of spreadsheets)

// Open the connection using the four values above.
// If anything is wrong (wrong password, MySQL not running) $conn
// will contain an error instead of a working connection.
$conn = new mysqli($host, $user, $pass, $dbname);

// Check if the connection failed.
// $conn->connect_error is NULL when everything is OK,
// and contains an error message string when something went wrong.
if ($conn->connect_error) {
    // die() stops the whole PHP script immediately and prints the message.
    // We show a red error so the user knows what's wrong.
    die('<p style="color:red">Connection failed: ' . $conn->connect_error . '</p>');
}
// If we reach this point the connection is working — carry on.


/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   STEP 2 — CREATE THE TABLE (only if it does not exist yet)
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   A table is like a spreadsheet with fixed columns.
   "IF NOT EXISTS" means: only create it the very first time;
   on every visit after that this line does nothing.

   Table design for learning_names:
   ┌────┬──────────────┬─────────────────────┐
   │ id │     name     │      created_at      │
   ├────┼──────────────┼─────────────────────┤
   │  1 │ Kasun Perera │ 2025-01-10 09:30:00 │
   │  2 │ Nuwan Silva  │ 2025-01-10 09:31:45 │
   └────┴──────────────┴─────────────────────┘

   Column explanations:
   • id         INT AUTO_INCREMENT PRIMARY KEY
                → a unique number (1, 2, 3 …) MySQL adds automatically.
                  "PRIMARY KEY" means no two rows can share the same id.
   • name       VARCHAR(100) NOT NULL
                → text up to 100 characters.  NOT NULL = cannot be empty.
   • created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                → date+time of insertion.  MySQL fills it in automatically.
*/
$conn->query("
    CREATE TABLE IF NOT EXISTS learning_names (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");


/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   STEP 3 — DECLARE WORKING VARIABLES
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   These three variables are used throughout the rest of the script.
   We set them to safe default values before any logic runs.
*/

$message  = '';    // Stores a message shown to the user after an action
                   // e.g.  "✅ Name added!"  or  "⚠️ Cannot be empty."
                   // Empty string '' = nothing to show yet.

$editId   = null;  // When the user clicks Edit on a row this holds that
                   // row's id number so we know which row to update.
                   // null = not in edit mode yet.

$editName = '';    // The current name of the row being edited.
                   // Pre-filled into the edit form so the user can see
                   // what they are changing.


/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   STEP 4 — HANDLE POST REQUESTS  (Insert & Update)
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   When a <form method="POST"> is submitted the browser sends data
   in the request BODY (not visible in the URL).
   PHP automatically puts that data into the $_POST superglobal array.

   $_SERVER['REQUEST_METHOD'] tells us HOW the page was requested:
   • 'GET'  → user just opened the page / clicked a link
   • 'POST' → user submitted a form

   We only run this block when a form was submitted (POST).
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── INSERT (Create) ───────────────────────────────────────
       Triggered when the "Add a Name" form is submitted.
       The form has a hidden field: <input type="hidden" name="action" value="insert">
       So $_POST['action'] will equal 'insert'.

       isset() checks if a key exists in the array before reading it,
       preventing PHP "undefined index" warnings.
    */
    if (isset($_POST['action']) && $_POST['action'] === 'insert') {

        // Read the name the user typed.
        // trim() removes any accidental spaces from the start and end.
        // e.g.  "  Kasun  "  →  "Kasun"
        $name = trim($_POST['name']);

        // Only insert if the name is not empty after trimming.
        if ($name !== '') {

            /* PREPARED STATEMENT — the safe way to run SQL
               ─────────────────────────────────────────────
               Step 1: prepare() sends the SQL template to MySQL.
                       The ? is a PLACEHOLDER — not the real value yet.
                       MySQL now knows the shape of the query but has
                       not run it yet.
               Step 2: bind_param() replaces ? with the actual value.
                       "s" means the value is a String.
                       MySQL treats this as DATA, never as SQL code,
                       which prevents SQL Injection attacks.
               Step 3: execute() actually runs the query.
               Step 4: close() frees up memory.
            */
            $stmt = $conn->prepare("INSERT INTO learning_names (name) VALUES (?)");
            //                                                            ↑
            //                                       placeholder for $name

            $stmt->bind_param("s", $name);
            //                 ↑    ↑
            //    "s" = string  |    the PHP variable to plug in

            $stmt->execute();   // run the INSERT
            $stmt->close();     // always close when done

            // htmlspecialchars() converts  <  >  &  "  '  to safe HTML
            // so if someone typed  <script>  it shows as text, not code.
            $message = '✅ Name "<strong>' . htmlspecialchars($name) . '</strong>" added!';

        } else {
            // The field was empty — tell the user.
            $message = '⚠️ Name cannot be empty.';
        }
    }

    /* ── UPDATE ────────────────────────────────────────────────
       Triggered when the "Edit Name" form is submitted.
       That form has:
         <input type="hidden" name="action" value="update">
         <input type="hidden" name="id"     value="[the row id]">
    */
    if (isset($_POST['action']) && $_POST['action'] === 'update') {

        // (int) is a TYPE CAST — forces the value to be a whole number.
        // If someone tampers with the hidden field and sends "5; DROP TABLE"
        // it becomes just  5  — safe.
        $id   = (int) $_POST['id'];

        $name = trim($_POST['name']);   // new name the user typed

        if ($name !== '') {

            // Prepared statement with TWO placeholders.
            // bind_param("si", ...) means:
            //   first ?  → "s" (string) = $name
            //   second ? → "i" (integer) = $id
            // The order of the letters must match the order of the ? marks.
            $stmt = $conn->prepare("UPDATE learning_names SET name = ? WHERE id = ?");
            //                                                        ↑           ↑
            //                                               new name     row to update

            $stmt->bind_param("si", $name, $id);
            //                  ↑↑   ↑      ↑
            //          s=string |    |      integer $id
            //          i=int ───┘    └── string $name

            $stmt->execute();
            $stmt->close();

            $message = '✅ Name updated to "<strong>' . htmlspecialchars($name) . '</strong>"!';

        } else {
            $message = '⚠️ Name cannot be empty.';
        }
    }

} // end if POST


/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   STEP 5 — HANDLE GET ACTIONS  (Delete & Load Edit)
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   GET actions are triggered by clicking ordinary links.
   The action and id are passed in the URL:
     ?action=delete&id=3   → delete row 3
     ?action=edit&id=3     → load the edit form for row 3

   PHP puts URL parameters into the $_GET superglobal array.
*/
if (isset($_GET['action'])) {

    /* ── DELETE ────────────────────────────────────────────────
       The Delete button in the table is a plain <a href> link.
       Before following the link, JavaScript asks for confirmation
       (see the JS section below).  If the user clicks OK the link
       is followed → this URL is requested → PHP deletes the row.
    */
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {

        $id   = (int) $_GET['id'];   // safe integer cast (same reason as above)

        $stmt = $conn->prepare("DELETE FROM learning_names WHERE id = ?");
        // Always use WHERE with DELETE — without it every single row
        // in the table would be deleted.

        $stmt->bind_param("i", $id);  // "i" = integer
        $stmt->execute();
        $stmt->close();

        $message = '🗑️ Name deleted.';
    }

    /* ── LOAD EDIT FORM ────────────────────────────────────────
       When the user clicks Edit we need to:
       1. Read the current name from the database (so we can
          pre-fill the edit form).
       2. Set $editId and $editName so the HTML below knows to
          show the Edit form instead of the Insert form.
    */
    if ($_GET['action'] === 'edit' && isset($_GET['id'])) {

        $id  = (int) $_GET['id'];

        // Simple query — we already sanitised $id with (int) so
        // putting it directly in the string is safe here.
        $res = $conn->query("SELECT * FROM learning_names WHERE id = $id");

        // fetch_assoc() returns one row as an associative array:
        // ['id' => 3, 'name' => 'Kasun', 'created_at' => '...']
        // Returns null if no row was found.
        $row = $res->fetch_assoc();

        if ($row) {
            // Store in our working variables — the HTML below reads these.
            $editId   = $row['id'];
            $editName = $row['name'];
        }
    }

} // end if GET


/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   STEP 6 — READ  (SELECT all rows)
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   This SELECT runs on EVERY page load (after any insert/update/delete
   has already happened) so the table always shows fresh data.

   ORDER BY id DESC = newest row first (highest id at the top).
   Change DESC to ASC if you want oldest first.
*/
$result = $conn->query("SELECT * FROM learning_names ORDER BY id DESC");
// $result is a result-set object.
// $result->num_rows  = how many rows were returned.
// $result->fetch_assoc()  = get the next row as an array.

?>
<!DOCTYPE html>
<!--
  Everything below this line is HTML that gets sent to the browser.
  PHP code can still appear inside HTML using  [?php ?]  or  [?= ?]  tags.
  [?= $variable ?]  is just a shorthand for  [?php echo $variable; ?]
-->
<html lang="en">
<head>
    <!--
      <head> contains page settings and resources.
      The browser does NOT display <head> content on screen.
    -->
    <meta charset="UTF-8">
    <!-- charset="UTF-8" tells the browser how characters are encoded.
         Without this, special characters like ✅ 🗑️ might look broken. -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Makes the page look correct on phones/tablets.
         Without this, mobile browsers zoom out and make text tiny. -->

    <title>PHP CRUD — Learning Demo</title>
    <!-- Text shown in the browser tab -->


<!-- ╔═══════════════════════════════════════════════════════════╗
     ║                    CSS SECTION                           ║
     ║   Everything inside <style>...</style> controls the      ║
     ║   visual appearance of the page.                        ║
     ║   Try changing any colour or number and refresh!        ║
     ╚═══════════════════════════════════════════════════════════╝ -->
<style>

    /*
      CSS RULE FORMAT:
      ────────────────
        selector { property: value; }

      selector   → WHICH element(s) to style
      property   → WHAT to change (colour, size, spacing …)
      value      → the new value

      SELECTOR TYPES:
        *           all elements (universal)
        body        the <body> tag specifically
        .card       any element with class="card"
        #msg        the one element with id="msg"
        tr:hover    a <tr> when the mouse is over it
        input:focus the <input> that currently has the cursor
    */


    /* ── UNIVERSAL RESET ─────────────────────────────────────
       Applied to EVERY element on the page (that is what * means).
       This clears browser default margins/padding so we start
       with a clean slate.
    */
    * {
        box-sizing: border-box;
        /* box-sizing: border-box  means:
           when you set  width: 200px  that 200px INCLUDES padding and
           border.  Without this, padding would ADD to the width and
           mess up layouts.  Always include this in real projects. */

        margin: 0;    /* remove default margin around elements */
        padding: 0;   /* remove default padding inside elements */
    }

    /* ── BODY — the whole page ───────────────────────────────
       EXPERIMENT: change the background colour!
    */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        /* font-family lists fonts in order of preference.
           The browser picks the first one it has installed.
           'sans-serif' at the end is a generic fallback that always works. */

        background: #f0f4f8;  /* ← CHANGE THIS: try  #000  or  #ffeeba  */
        color: #333;           /* text colour for the whole page */
        padding: 30px 20px;    /* top/bottom = 30px,  left/right = 20px */
    }

    /* ── PAGE TITLE <h1> ─────────────────────────────────────
       EXPERIMENT: change the title colour!
    */
    h1 {
        text-align: center;    /* centre the text horizontally */
        color: #1a73e8;        /* ← CHANGE THIS: try  red  or  #e91e63  */
        margin-bottom: 6px;    /* space below the title */
        font-size: 2rem;       /* rem = "relative to root font size"
                                  1rem ≈ 16px,  2rem ≈ 32px */
    }

    /* ── SUBTITLE PARAGRAPH ──────────────────────────────────*/
    .subtitle {
        text-align: center;
        color: #888;
        margin-bottom: 30px;
        font-size: 0.9rem;     /* 90% of the base font size = slightly smaller */
    }

    /* ── CONTAINER — centres the content block ───────────────
       max-width + margin: auto = classic CSS centring trick.
       The content will be at most 750px wide and centred on screen.
    */
    .container {
        max-width: 750px;   /* ← CHANGE THIS: try 400px or 1000px */
        margin: 0 auto;     /* 0 top/bottom margin, auto left/right = centred */
    }

    /* ── CARD — the white boxes ──────────────────────────────
       Each major section (form, table) lives inside a .card div.
       EXPERIMENT: change background to see coloured cards!
    */
    .card {
        background: #ffffff;  /* ← CHANGE THIS: try  #fff3cd  (yellow)  */
        border-radius: 10px;  /* rounds the corners; try 0 for sharp corners */
        padding: 25px 30px;   /* breathing room inside the card */
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        /* box-shadow: x-offset  y-offset  blur  colour(with opacity)
           This creates a subtle drop shadow under each card.
           Try:  0 0 20px rgba(0,0,0,0.3)  for a stronger glow.   */
        margin-bottom: 25px;  /* gap between cards */
    }

    /* ── CARD HEADING <h2> ───────────────────────────────────
       The coloured left border is a visual design trick:
       set border-left to a thick solid line.
    */
    .card h2 {
        font-size: 1.1rem;
        margin-bottom: 16px;
        color: #444;
        border-left: 4px solid #1a73e8;  /* ← CHANGE THIS colour */
        padding-left: 10px;              /* text does not sit on the border */
    }

    /* ── LABEL (text above the input) ───────────────────────*/
    label {
        display: block;        /* makes the label sit on its own line */
        font-size: 0.85rem;
        font-weight: 600;      /* bold */
        margin-bottom: 5px;
        color: #555;
    }

    /* ── TEXT INPUT FIELD ────────────────────────────────────*/
    input[type="text"] {
        /*  input[type="text"]  targets only inputs whose type="text"
            (not checkboxes, radio buttons, etc.)  */
        width: 100%;            /* stretch to fill the card */
        padding: 10px 14px;
        border: 1px solid #ccd0d5;
        border-radius: 6px;
        font-size: 1rem;
        outline: none;          /* removes the ugly blue browser outline on focus
                                   (we draw our own focus ring below) */
        transition: border 0.2s;
        /* transition makes the border-color change animate smoothly
           instead of snapping instantly.  0.2s = 0.2 seconds. */
    }

    /* ── INPUT FOCUS STATE (user has clicked into the field) ─*/
    input[type="text"]:focus {
        border-color: #1a73e8;  /* ← CHANGE THIS focus colour */
        box-shadow: 0 0 0 3px rgba(26,115,232,0.15);
        /* A soft glow effect — the 0.15 at the end is 15% opacity */
    }

    /* ── BASE BUTTON STYLE ───────────────────────────────────
       .btn styles ALL buttons.  Each colour variant (primary,
       danger, etc.) then adds ONLY the background colour on top.
       This keeps the code DRY (Don't Repeat Yourself).
    */
    .btn {
        display: inline-block;
        /* display:inline-block lets us set padding on an <a> tag —
           by default <a> is inline and does not respond to padding. */
        padding: 9px 20px;
        border: none;          /* remove default browser button border */
        border-radius: 6px;
        cursor: pointer;       /* mouse shows a hand when hovering */
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none; /* remove underline from <a> links styled as buttons */
        transition: background 0.2s, transform 0.1s;
        /* two transitions: background and a slight shrink on click */
        margin-top: 12px;
    }

    /* When the button is actively being clicked it shrinks slightly
       giving a satisfying "press" feel */
    .btn:active { transform: scale(0.97); }

    /* ── COLOUR VARIANTS ─────────────────────────────────────
       EXPERIMENT: change any of these hex colours!
    */
    .btn-primary  { background: #1a73e8; color: white; }   /* Add button    */
    .btn-success  { background: #34a853; color: white; }   /* Save button   */
    .btn-danger   { background: #ea4335; color: white;     /* Delete button */
                    padding: 5px 12px; font-size: 0.8rem; margin-top: 0; }
    .btn-warning  { background: #fbbc04; color: #333;      /* Edit button   */
                    padding: 5px 12px; font-size: 0.8rem; margin-top: 0; }
    .btn-cancel   { background: #9e9e9e; color: white; margin-left: 8px; }

    /* Hover = when mouse is over the button */
    .btn-primary:hover { background: #1558b0; }
    .btn-success:hover { background: #278a41; }
    .btn-danger:hover  { background: #c62828; }
    .btn-warning:hover { background: #f0a500; }
    .btn-cancel:hover  { background: #757575; }

    /* ── FEEDBACK MESSAGE ────────────────────────────────────
       Shown at the top after every action (insert, update, delete).
       EXPERIMENT: change background/border/color to restyle it.
    */
    .message {
        background: #e8f5e9;      /* light green */
        border: 1px solid #c8e6c9;
        color: #2e7d32;
        padding: 10px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    /* ── TABLE ───────────────────────────────────────────────*/
    table {
        width: 100%;
        border-collapse: collapse;
        /* border-collapse:collapse removes the double-border you get
           when cells sit next to each other by default. */
        font-size: 0.9rem;
    }

    /* Table header row */
    thead th {
        background: #1a73e8;  /* ← CHANGE THIS: try  #2e7d32  (dark green) */
        color: white;
        padding: 10px 14px;
        text-align: left;
    }

    /* Zebra-stripe every second row for readability */
    tbody tr:nth-child(even) {
        background: #f8f9fa;  /* ← CHANGE THIS */
    }

    /* Highlight row when mouse hovers over it */
    tbody tr:hover {
        background: #e8f0fe;  /* ← CHANGE THIS */
    }

    /* Table data cells */
    td {
        padding: 10px 14px;
        border-bottom: 1px solid #e0e0e0;  /* thin line under each row */
    }

    /* Action buttons inside a cell sit side by side */
    .actions { display: flex; gap: 6px; }
    /* display:flex puts children in a row.
       gap:6px adds 6px of space between them. */

    /* Text shown when the table is empty */
    .empty {
        text-align: center;
        color: #aaa;
        padding: 20px 0;
        font-style: italic;
    }

    /* Tiny grey hint text below each card */
    .legend {
        font-size: 0.78rem;
        color: #999;
        margin-top: 8px;
    }

</style>
<!-- ╔═══════════════════════════════════════════════════════════╗
     ║                   END CSS SECTION                        ║
     ╚═══════════════════════════════════════════════════════════╝ -->

</head>
<body>
<!--
  Everything inside <body>...</body> is visible on screen.
  The browser reads it top to bottom and draws each element.
-->

<div class="container">
<!--
  <div> is a generic container with no visual meaning on its own.
  class="container" links it to the .container CSS rule above
  which centres and limits the width.
-->

    <h1>PHP CRUD Demo</h1>
    <p class="subtitle">
        One file — PHP + HTML + CSS + JS &nbsp;|&nbsp; Table: <code>learning_names</code>
        <!-- &nbsp; = a non-breaking space (wider than a normal space) -->
        <!-- <code> displays text in a monospace font (like a terminal) -->
    </p>


    <!-- ▸▸ FEEDBACK MESSAGE ◂◂
         PHP wrote either a success or error message into $message.
         [?php if ($message): ?]  is the same as  if ($message !== '').
         We only render the <div> if there is actually a message.
         id="msg" lets JavaScript find this element by name.
    -->
    <?php if ($message): ?>
        <div class="message" id="msg"><?= $message ?></div>
        <!--
          [?= $message ?]  is shorthand for  [?php echo $message; ?]
          It outputs the PHP variable directly into the HTML.
        -->
    <?php endif; ?>


    <!-- ╔═══════════════════════════════════════════════════════╗
         ║   INSERT FORM  — shown when NOT in edit mode         ║
         ║   PHP condition: if $editId is null (no edit active) ║
         ╚═══════════════════════════════════════════════════════╝ -->
    <?php if (!$editId): ?>
    <!--
      !$editId  means "if $editId is falsy" = null or 0 or ''
      When the user is NOT editing, $editId = null → show this form.
      When the user IS  editing, $editId = some number → skip this form.
    -->
    <div class="card">
        <h2>➕ Add a Name</h2>

        <form method="POST" action="">
        <!--
          method="POST"   → data goes in the request BODY (hidden from URL)
          action=""       → submit to the same page (this file)

          When the user clicks the submit button, the browser packs
          all the input values into a POST request and sends it to
          this same URL.  PHP at the TOP of this file reads $_POST
          and does the insert BEFORE the page HTML is built.
        -->

            <!--
              HIDDEN FIELD — invisible to the user but sent with the form.
              PHP reads  $_POST['action']  to know which action to take.
              Without this, PHP would not know whether it was an Insert
              or an Update that was submitted.
            -->
            <input type="hidden" name="action" value="insert">

            <label for="name">Name</label>
            <!--
              for="name" links this label to the input with id="name".
              Clicking the label moves the cursor into the input — good UX.
            -->

            <input type="text"
                   id="name"
                   name="name"
                   placeholder="e.g. Kasun Perera"
                   required>
            <!--
              id="name"         → used by the <label for="name"> above
              name="name"       → the KEY in $_POST['name'] that PHP reads
              placeholder       → grey hint text shown when the field is empty
              required          → browser blocks submission if the field is empty
                                  (no PHP is involved — browser checks it first)
            -->

            <button type="submit" class="btn btn-primary">Add Name</button>
            <!--
              type="submit"  → clicking this button submits the whole form.
              class          → applies CSS styles defined in the <style> block.
            -->
        </form>

        <p class="legend">
            This form sends a <strong>POST</strong> request →
            PHP inserts a row into <code>learning_names</code>.
        </p>
    </div>
    <?php endif; ?>


    <!-- ╔═══════════════════════════════════════════════════════╗
         ║   UPDATE FORM  — shown ONLY when in edit mode        ║
         ║   PHP condition: if $editId has a value              ║
         ╚═══════════════════════════════════════════════════════╝ -->
    <?php if ($editId): ?>
    <!--
      This form is the opposite: only visible when $editId is set,
      meaning the user clicked an Edit button in the table below.
      PHP already loaded $editId and $editName from the database.
    -->
    <div class="card">
        <h2>✏️ Edit Name (ID: <?= $editId ?>)</h2>
        <!--
          [?= $editId ?]  prints the id number so the user can see
          which row they are editing.
        -->

        <form method="POST" action="">

            <!-- Tell PHP this is an update, not an insert -->
            <input type="hidden" name="action" value="update">

            <!-- Pass the row id back to PHP so it knows WHICH row to update -->
            <input type="hidden" name="id" value="<?= $editId ?>">

            <label for="name">New Name</label>

            <input type="text"
                   id="name"
                   name="name"
                   value="<?= htmlspecialchars($editName) ?>"
                   required>
            <!--
              value="..."  pre-fills the input with the current name.
              The user sees what they are changing and can edit it.
              htmlspecialchars() makes sure if the name contains < or >
              it does not break the HTML attribute.
            -->

            <button type="submit" class="btn btn-success">Save Changes</button>

            <!-- Cancel = go back to the plain page with no ?action in the URL -->
            <a href="learning_crud.php" class="btn btn-cancel">Cancel</a>
        </form>

        <p class="legend">
            This form sends a <strong>POST</strong> request →
            PHP runs UPDATE on row id = <?= $editId ?>.
        </p>
    </div>
    <?php endif; ?>


    <!-- ╔═══════════════════════════════════════════════════════╗
         ║   READ TABLE  — always visible                       ║
         ║   Displays every row from the SELECT query above     ║
         ╚═══════════════════════════════════════════════════════╝ -->
    <div class="card">
        <h2>📋 All Names</h2>

        <?php if ($result && $result->num_rows > 0): ?>
        <!--
          $result->num_rows  = the number of rows returned by SELECT.
          If it is > 0 there is data to show → draw the table.
          Otherwise fall through to the "empty" message.
        -->

        <table>
            <thead>
                <!-- Header row — describes each column -->
                <tr>
                    <th>#</th>           <!-- row id from the database -->
                    <th>Name</th>
                    <th>Added At</th>    <!-- created_at column -->
                    <th>Actions</th>     <!-- Edit and Delete buttons -->
                </tr>
            </thead>
            <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>
            <!--
              fetch_assoc() fetches ONE row at a time as an array:
              $row = ['id' => 1, 'name' => 'Kasun', 'created_at' => '...']
              The while loop keeps going until fetch_assoc() returns null
              (no more rows left in $result).
              Each iteration draws one <tr> for one database row.
            -->

                <tr>
                    <!-- The database id number -->
                    <td><?= $row['id'] ?></td>

                    <!-- The name — htmlspecialchars prevents XSS -->
                    <td id="name-<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['name']) ?>
                    </td>
                    <!--
                      id="name-1", id="name-2" etc. are unique identifiers
                      JavaScript could use these to find a specific cell.
                    -->

                    <!-- The timestamp MySQL stored automatically -->
                    <td><?= $row['created_at'] ?></td>

                    <!-- Edit and Delete buttons -->
                    <td class="actions">

                        <!-- ─── EDIT BUTTON ───────────────────────────────
                             A plain link styled to look like a button.
                             Clicking it adds  ?action=edit&id=X  to the URL.
                             PHP at the top reads $_GET and loads the
                             current name into $editId/$editName.
                             The page then refreshes showing the Update form.
                        -->
                        <a href="?action=edit&id=<?= $row['id'] ?>"
                           class="btn btn-warning">
                            Edit
                        </a>

                        <!-- ─── DELETE BUTTON ─────────────────────────────
                             Also a plain link styled as a button.
                             onclick="return confirmDelete(...)"  calls the
                             JavaScript function below BEFORE following the link.
                             If the user clicks Cancel → function returns false
                             → "return false" stops the link from being followed
                             → nothing is deleted.
                             If the user clicks OK → function returns true
                             → the link is followed → PHP deletes the row.
                        -->
                        <a href="?action=delete&id=<?= $row['id'] ?>"
                           class="btn btn-danger"
                           onclick="return confirmDelete(
                               <?= $row['id'] ?>,
                               '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>'
                           )">
                           Delete
                        </a>
                        <!--
                          ENT_QUOTES makes htmlspecialchars also escape
                          single quotes ' — important here because the
                          name is inside a JavaScript string with single quotes.
                        -->
                    </td>
                </tr>

            <?php endwhile; ?>
            </tbody>
        </table>

        <?php else: ?>
            <!-- Shown when the table is completely empty -->
            <p class="empty">No names yet — add one above!</p>
        <?php endif; ?>

        <p class="legend">
            PHP loops over the result set from
            <code>SELECT * FROM learning_names</code>.
        </p>
    </div>


    <!-- ╔═══════════════════════════════════════════════════════╗
         ║   TECH LEGEND — static info table                    ║
         ╚═══════════════════════════════════════════════════════╝ -->
    <div class="card">
        <h2>🛠 Technologies at a Glance</h2>
        <table>
            <thead>
                <tr><th>Layer</th><th>What it does in this file</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>HTML</strong></td>
                    <td>Structure — forms, table, buttons, headings</td></tr>
                <tr><td><strong>CSS</strong></td>
                    <td>Styling — colours, layout, hover &amp; focus effects</td></tr>
                <tr><td><strong>JavaScript</strong></td>
                    <td>Interactivity — delete confirmation, auto-hide message, row highlight</td></tr>
                <tr><td><strong>PHP</strong></td>
                    <td>Server logic — runs CRUD queries via MySQLi prepared statements</td></tr>
                <tr><td><strong>MySQL</strong></td>
                    <td>Database — permanently stores rows in <code>learning_names</code></td></tr>
            </tbody>
        </table>
    </div>

</div><!-- /container -->


<!-- ╔═══════════════════════════════════════════════════════════╗
     ║                 JAVASCRIPT SECTION                       ║
     ║  Everything inside <script>...</script> runs in the      ║
     ║  BROWSER after the HTML has loaded.                      ║
     ║  PHP has already finished by the time JS runs.           ║
     ╚═══════════════════════════════════════════════════════════╝ -->
<script>
/*
  JavaScript runs CLIENT-SIDE (in the browser).
  PHP runs SERVER-SIDE (on your computer via XAMPP).
  They do not run at the same time — PHP runs first and builds
  the HTML;  then the browser receives the HTML and runs the JS.
*/


// ── FEATURE 1: DELETE CONFIRMATION ────────────────────────────
/*
  This function is called by  onclick="return confirmDelete(...)"
  on each Delete button in the table.

  How onclick works:
    1. User clicks the Delete link.
    2. Browser calls  confirmDelete(id, name)  BEFORE following the link.
    3. confirm() shows a pop-up dialog with OK and Cancel.
    4. If user clicks OK     → confirm() returns true  → function returns true
       → "return true"  in onclick  → the link IS followed → PHP deletes.
    5. If user clicks Cancel → confirm() returns false → function returns false
       → "return false" in onclick  → the link is NOT followed → nothing deleted.
*/
function confirmDelete(id, name) {
    // confirm() is built into every browser.
    // It shows a modal dialog and returns true (OK) or false (Cancel).
    return confirm('Are you sure you want to delete "' + name + '"?');
    //              ↑ EXPERIMENT: change this message text
}


// ── FEATURE 2: AUTO-HIDE FEEDBACK MESSAGE ─────────────────────
/*
  After an Insert / Update / Delete, PHP sets $message and PHP
  outputs  <div class="message" id="msg">...</div>  in the HTML.
  This JS finds that element and fades it out after a delay.

  The DOM (Document Object Model) is the browser's tree of all
  HTML elements.  JS can read and change the DOM in real time.
*/

// document.getElementById('msg')  searches the entire DOM for
// the element whose  id="msg".  Returns null if not found.
const msg = document.getElementById('msg');

if (msg) {
    // msg exists → there was an action message to show.

    // setTimeout(function, delay_in_ms)
    // Waits 'delay' milliseconds then calls the function ONCE.
    // 4000ms = 4 seconds.  EXPERIMENT: try 1000 (1 second).
    setTimeout(function () {

        // Start a CSS transition on the opacity property.
        // This tells the browser: "when opacity changes, animate it
        // over 0.8 seconds instead of snapping instantly."
        msg.style.transition = 'opacity 0.8s';   // ← change fade speed

        // Setting opacity to 0 makes the element invisible.
        // The transition above makes it fade smoothly.
        msg.style.opacity = '0';

        // After the fade animation finishes (800ms later)
        // completely remove the element from the DOM so it
        // no longer takes up space on the page.
        setTimeout(() => msg.remove(), 800);
        //          ↑ arrow function = shorter way to write  function() { msg.remove(); }

    }, 4000);   // ← EXPERIMENT: change 4000 to 1000 for a 1-second delay
}


// ── FEATURE 3: HIGHLIGHT NEWLY ADDED ROW ──────────────────────
/*
  When a name is inserted, PHP outputs a message containing "added".
  We check for that word, then give the first table row (the newest,
  because we ORDER BY id DESC) a yellow highlight that fades away.
*/

if (msg && msg.textContent.includes('added')) {
    // msg.textContent  is the plain text inside the message element
    // (without HTML tags).  .includes('added') returns true/false.

    // document.querySelector(selector)  finds the FIRST element
    // matching a CSS selector.  'tbody tr' = first <tr> inside <tbody>.
    const firstRow = document.querySelector('tbody tr');

    if (firstRow) {
        firstRow.style.background = '#fff9c4';   // ← yellow highlight colour
        firstRow.style.transition = 'background 2s';
        // After 2 seconds, remove the background (let CSS take over again)
        setTimeout(() => firstRow.style.background = '', 2000);
        //                                                   ↑ empty string resets style
    }
}

</script>
<!-- ╔═══════════════════════════════════════════════════════════╗
     ║               END JAVASCRIPT SECTION                     ║
     ╚═══════════════════════════════════════════════════════════╝ -->

</body>
</html>
<?php
// Close the database connection — releases the resource back to MySQL.
// PHP would close it automatically at the end of the script anyway,
// but it is good practice to close it explicitly.
$conn->close();
?>
