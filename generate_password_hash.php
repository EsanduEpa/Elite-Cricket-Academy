<?php
// Generate proper password hash for 'password123'
$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: password123\n";
echo "Hash: " . $hash . "\n\n";

// Generate SQL to update all users
echo "-- SQL to update all test users with correct password hash\n";
echo "USE cricket_academy;\n\n";
echo "UPDATE User SET PasswordHash = '" . $hash . "' WHERE Username IN ('admin', 'trainer', 'coach', 'shopkeeper', 'player');\n\n";
echo "-- Verify update\n";
echo "SELECT Username, Email, Role FROM User WHERE Username IN ('admin', 'trainer', 'coach', 'shopkeeper', 'player');\n";
?>
