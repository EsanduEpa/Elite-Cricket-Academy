<?php
// Test registration system with School field
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';
require_once '../app/models/M_Users.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Registration System Test with School Field</h2>";

try {
    // Initialize the user model
    $userModel = new M_Users();
    
    // Test data
    $testData = [
        'fullName' => 'Test Student',
        'dateOfBirth' => '2005-05-15',
        'address' => '123 Test Street, Test City',
        'school' => 'Test High School',
        'email' => 'test@example.com',
        'contactNumber' => '+1234567890',
        'username' => 'testuser123',
        'password' => password_hash('password123', PASSWORD_DEFAULT)
    ];
    
    echo "<h3>Test Data:</h3>";
    echo "<pre>";
    foreach($testData as $key => $value) {
        if($key !== 'password') {
            echo "$key: $value\n";
        } else {
            echo "$key: [HASHED]\n";
        }
    }
    echo "</pre>";
    
    echo "<h3>Testing Database Connection...</h3>";
    $db = new Database();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Check if user already exists
    if($userModel->findUserByEmail($testData['email'])) {
        echo "<p style='color: orange;'>⚠️ Test user already exists. Skipping registration.</p>";
    } else {
        // Test registration
        echo "<h3>Testing Registration...</h3>";
        $result = $userModel->register($testData);
        
        if($result) {
            echo "<p style='color: green;'>✅ Registration successful!</p>";
            
            // Verify the user was created with all fields including School
            $createdUser = $userModel->findUserByEmail($testData['email']);
            if($createdUser) {
                echo "<h3>Created User Details:</h3>";
                echo "<pre>";
                echo "UserID: " . ($createdUser->UserID ?? 'N/A') . "\n";
                echo "Name: " . ($createdUser->Name ?? 'N/A') . "\n";
                echo "Email: " . ($createdUser->Email ?? 'N/A') . "\n";
                echo "School: " . ($createdUser->School ?? 'N/A') . "\n";
                echo "Role: " . ($createdUser->Role ?? 'N/A') . "\n";
                echo "Status: " . ($createdUser->Status ?? 'N/A') . "\n";
                echo "DateJoined: " . ($createdUser->DateJoined ?? 'N/A') . "\n";
                echo "</pre>";
            }
        } else {
            echo "<p style='color: red;'>❌ Registration failed!</p>";
        }
    }
    
    // Test login functionality
    echo "<h3>Testing Login...</h3>";
    $loginUser = $userModel->login($testData['email'], 'password123');
    
    if($loginUser) {
        echo "<p style='color: green;'>✅ Login successful!</p>";
        echo "<p>Logged in user: " . ($loginUser->Name ?? 'N/A') . " (" . ($loginUser->Email ?? 'N/A') . ")</p>";
    } else {
        echo "<p style='color: red;'>❌ Login failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><hr>";
echo "<p><a href='test_db.php'>← Back to Database Test</a></p>";
echo "<p><a href='" . URLROOT . "/register'>Go to Registration Form →</a></p>";
?>
