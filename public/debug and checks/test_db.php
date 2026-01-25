<?php
// Test database connection
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';

echo "<h2>Database Connection Test</h2>";

try {
    $db = new Database();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    echo "<p>Database: " . DB_NAME . "</p>";
    echo "<p>Host: " . DB_HOST . "</p>";
    
    // Test if User table exists
    $db->query("SHOW TABLES LIKE 'User'");
    $result = $db->single();
    
    if ($result) {
        echo "<p style='color: green;'>✅ User table exists!</p>";
        
        // Count existing users
        $db->query("SELECT COUNT(*) as count FROM User");
        $count = $db->single();
        echo "<p>Current users in database: " . $count->count . "</p>";
    } else {
        echo "<p style='color: red;'>❌ User table does NOT exist!</p>";
        echo "<p>Please run the SQL script provided to create the User table.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>