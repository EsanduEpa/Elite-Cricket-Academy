<?php
// Simple PHP test file to verify XAMPP is working
echo "<h1>Elite Cricket Academy - System Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Test if our constants are defined
if (file_exists(__DIR__ . '/app/config/config.php')) {
    require_once __DIR__ . '/app/config/config.php';
    echo "<h2>Configuration Test</h2>";
    echo "<p>✅ Config file loaded successfully</p>";
    echo "<p>Database Name: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</p>";
    echo "<p>URL Root: " . (defined('URLROOT') ? URLROOT : 'Not defined') . "</p>";
    echo "<p>App Root: " . (defined('APPROOT') ? APPROOT : 'Not defined') . "</p>";
} else {
    echo "<p>❌ Config file not found</p>";
}

echo "<hr>";
echo "<h2>Available Test URLs</h2>";
echo "<ul>";
echo "<li><a href='" . (defined('URLROOT') ? URLROOT : '/Elite') . "/player/test'>Player Dashboard (UI Test)</a></li>";
echo "<li><a href='" . (defined('URLROOT') ? URLROOT : '/Elite') . "/player/index'>Player Dashboard (With Auth)</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>File Structure Check</h2>";
$files_to_check = [
    'app/controllers/Player.php',
    'app/views/player/dashboard.php',
    'public/css/player/dashboard.css',
    'public/js/player/dashboard.js'
];

foreach ($files_to_check as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<p>✅ " . $file . "</p>";
    } else {
        echo "<p>❌ " . $file . " (missing)</p>";
    }
}
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 800px; 
    margin: 50px auto; 
    padding: 20px;
    background: #f5f5f5;
}
h1, h2 { color: #2c3e50; }
p { margin: 10px 0; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #3498db; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
