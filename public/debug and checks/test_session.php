<?php
session_start();

echo "<h2>User Session Debug</h2>";

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ User is logged in</p>";
    echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
    echo "<p><strong>User Role:</strong> " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";
    echo "<p><strong>User Name:</strong> " . ($_SESSION['user_name'] ?? $_SESSION['name'] ?? 'Not set') . "</p>";
} else {
    echo "<p style='color: red;'>❌ No user is logged in</p>";
    echo "<p>You need to login first to use the medical records system.</p>";
    echo "<p><a href='" . (defined('URLROOT') ? URLROOT : '/Elite/public') . "/login'>Go to Login Page</a></p>";
}

// Test database connection and get user data if logged in
if (isset($_SESSION['user_id'])) {
    try {
        require_once '../app/config/config.php';
        require_once '../app/libraries/Database.php';
        
        $db = new Database();
        $db->query("SELECT * FROM User WHERE UserID = :user_id");
        $db->bind(':user_id', $_SESSION['user_id']);
        $user = $db->single();
        
        if ($user) {
            echo "<h3>User Database Record:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            foreach ($user as $key => $value) {
                echo "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ User record not found in database</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='" . (defined('URLROOT') ? URLROOT : '/Elite/public') . "/player/medical'>← Back to Medical Records</a></p>";
?>