<?php
// Test login credentials validation
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';
require_once '../app/models/M_Users.php';

echo "<h2>Login Credentials Testing</h2>";

try {
    $userModel = new M_Users();
    
    // Show available users for testing
    echo "<h3>Available Users for Testing:</h3>";
    $db = new Database();
    $db->query('SELECT UserID, Name, Email, Username, Role FROM User LIMIT 10');
    $users = $db->resultSet();
    
    if($users) {
        echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
        echo "<tr><th>UserID</th><th>Name</th><th>Email</th><th>Username</th><th>Role</th></tr>";
        foreach($users as $user) {
            echo "<tr>";
            echo "<td>" . $user->UserID . "</td>";
            echo "<td>" . $user->Name . "</td>";
            echo "<td>" . $user->Email . "</td>";
            echo "<td>" . $user->Username . "</td>";
            echo "<td>" . $user->Role . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test with the first user
        $testUser = $users[0];
        echo "<h3>Testing Login Method:</h3>";
        echo "<p><strong>Testing with:</strong> " . $testUser->Email . "</p>";
        echo "<p><em>Note: This test uses a known password. For actual login, use the login form.</em></p>";
        
        // Test different scenarios
        echo "<h4>Test Results:</h4>";
        
        // Test 1: Invalid email
        $result1 = $userModel->login('nonexistent@example.com', 'password123');
        echo "<p>❌ Invalid email test: " . ($result1 ? "FAILED (should be false)" : "PASSED (correctly returned false)") . "</p>";
        
        // Test 2: Valid email, wrong password  
        $result2 = $userModel->login($testUser->Email, 'wrongpassword');
        echo "<p>❌ Wrong password test: " . ($result2 ? "FAILED (should be false)" : "PASSED (correctly returned false)") . "</p>";
        
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border: 1px solid #ffeaa7;'>";
        echo "<p><strong>🔐 Password Note:</strong> To test successful login, you need to know the actual password for a user.</p>";
        echo "<p>Try using the login form at: <a href='" . URLROOT . "/login'>Login Page</a></p>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ No users found in database!</p>";
        echo "<p>Create some users first using the <a href='" . URLROOT . "/register'>Registration Form</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='" . URLROOT . "/login'>← Go to Login Form</a></p>";
echo "<p><a href='" . URLROOT . "/register'>Go to Registration →</a></p>";
?>
