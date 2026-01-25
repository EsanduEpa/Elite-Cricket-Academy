<?php
session_start();

// Check current session
echo "<h2>Current Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Fix the coach_id
$_SESSION['user_id'] = 4; // The actual coach UserID from your database
$_SESSION['username'] = 'coach';
$_SESSION['role'] = 'Coach';

echo "<h2>✅ Session Fixed!</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Now try creating a session again.</h3>";
echo "<a href='/Elite/app/views/coach/dashboard.php'>Go to Coach Dashboard</a>";
?>
