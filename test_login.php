<?php
// Test login functionality
require_once 'app/config/config.php';
require_once 'app/libraries/Database.php';
require_once 'app/models/M_Users.php';

$userModel = new M_Users();

echo "Testing login for admin user...\n\n";

// Test login
$username = 'admin';
$password = 'password123';

$user = $userModel->login($username, $password);

if ($user) {
    echo "✅ LOGIN SUCCESSFUL!\n\n";
    echo "User ID: " . $user->UserID . "\n";
    echo "Name: " . $user->Name . "\n";
    echo "Email: " . $user->Email . "\n";
    echo "Username: " . $user->Username . "\n";
    echo "Role: " . $user->Role . "\n";
    echo "Status: " . $user->Status . "\n";
} else {
    echo "❌ LOGIN FAILED!\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password . "\n";
}

// Test all users
echo "\n\n--- Testing all user logins ---\n\n";

$testUsers = [
    ['username' => 'admin', 'password' => 'password123'],
    ['username' => 'trainer', 'password' => 'password123'],
    ['username' => 'coach', 'password' => 'password123'],
    ['username' => 'shopkeeper', 'password' => 'password123'],
    ['username' => 'player', 'password' => 'password123']
];

foreach ($testUsers as $testUser) {
    $result = $userModel->login($testUser['username'], $testUser['password']);
    if ($result) {
        echo "✅ " . $testUser['username'] . " - LOGIN OK (Role: " . $result->Role . ")\n";
    } else {
        echo "❌ " . $testUser['username'] . " - LOGIN FAILED\n";
    }
}
?>
