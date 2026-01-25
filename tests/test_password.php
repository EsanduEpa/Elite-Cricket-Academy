<?php
// Test password verification
$password = 'password123';
$hash_from_db = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Testing password verification:\n";
echo "Password: " . $password . "\n";
echo "Hash: " . $hash_from_db . "\n\n";

if (password_verify($password, $hash_from_db)) {
    echo "✅ Password verification SUCCESSFUL!\n";
} else {
    echo "❌ Password verification FAILED!\n";
    echo "\nGenerating new hash for 'password123':\n";
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    echo $new_hash . "\n";
}

// Also test creating a new hash
echo "\n\n--- Creating fresh hash for 'password123' ---\n";
$fresh_hash = password_hash('password123', PASSWORD_DEFAULT);
echo "New hash: " . $fresh_hash . "\n";

if (password_verify('password123', $fresh_hash)) {
    echo "✅ New hash verification works!\n";
}
?>
