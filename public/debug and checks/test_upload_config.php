<?php
// Simple test file to check upload configuration and directory permissions
// Access via: http://localhost/Elite/public/test_upload_config.php

echo "<h2>Profile Image Upload - Configuration Test</h2>";

// Test 1: Check APPROOT constant
echo "<h3>1. Check Paths</h3>";
define('APPROOT', dirname(dirname(__FILE__)) . '/app');
$projectRoot = dirname(APPROOT);
echo "APPROOT: " . APPROOT . "<br>";
echo "Project Root: " . $projectRoot . "<br>";
echo "Upload Directory: " . $projectRoot . '/public/uploads/profile_images/' . "<br>";

// Test 2: Check if directory exists
echo "<h3>2. Directory Status</h3>";
$uploadDir = $projectRoot . '/public/uploads/profile_images/';
if (is_dir($uploadDir)) {
    echo "✅ Upload directory EXISTS<br>";
} else {
    echo "❌ Upload directory DOES NOT EXIST<br>";
    echo "Attempting to create...<br>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "✅ Directory created successfully<br>";
    } else {
        echo "❌ Failed to create directory<br>";
    }
}

// Test 3: Check permissions
echo "<h3>3. Directory Permissions</h3>";
if (is_writable($uploadDir)) {
    echo "✅ Directory is WRITABLE<br>";
} else {
    echo "❌ Directory is NOT WRITABLE<br>";
    echo "Current permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "<br>";
}

// Test 4: Check PHP upload settings
echo "<h3>4. PHP Upload Settings</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";

// Test 5: Test file creation
echo "<h3>5. File Write Test</h3>";
$testFile = $uploadDir . 'test_' . time() . '.txt';
if (file_put_contents($testFile, 'test content')) {
    echo "✅ Can write files to directory<br>";
    unlink($testFile); // Clean up
    echo "✅ Test file cleaned up<br>";
} else {
    echo "❌ Cannot write files to directory<br>";
}

// Test 6: Check session
echo "<h3>6. Session Test</h3>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "✅ Session user_id exists: " . $_SESSION['user_id'] . "<br>";
} else {
    echo "⚠️ No session user_id found<br>";
    echo "Setting test session...<br>";
    $_SESSION['user_id'] = 1;
    echo "✅ Test session set (user_id = 1)<br>";
}

echo "<h3>7. Simple Upload Test Form</h3>";
echo '<form action="test_simple_upload.php" method="POST" enctype="multipart/form-data">';
echo '<input type="file" name="test_image" accept="image/*"><br><br>';
echo '<input type="submit" value="Test Upload">';
echo '</form>';

echo "<hr>";
echo "<p><strong>Status Summary:</strong></p>";
echo "<p>If all tests pass (✅), the upload feature should work correctly.</p>";
echo "<p>If you see any failures (❌), fix those issues first.</p>";
?>
