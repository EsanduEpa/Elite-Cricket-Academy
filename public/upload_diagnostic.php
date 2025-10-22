<?php
// Medical Receipt Upload Diagnostic
echo "<h1>Medical Receipt Upload Diagnostics</h1>";

// Check PHP Upload Settings
echo "<h2>PHP Upload Configuration</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$uploadMaxFilesize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
$maxFileUploads = ini_get('max_file_uploads');
$fileUploads = ini_get('file_uploads');

echo "<tr><td>file_uploads</td><td>$fileUploads</td><td>" . ($fileUploads ? '✅' : '❌') . "</td></tr>";
echo "<tr><td>upload_max_filesize</td><td>$uploadMaxFilesize</td><td>✅</td></tr>";
echo "<tr><td>post_max_size</td><td>$postMaxSize</td><td>✅</td></tr>";
echo "<tr><td>max_file_uploads</td><td>$maxFileUploads</td><td>✅</td></tr>";
echo "</table>";

// Check Directory Permissions
echo "<h2>Directory Permissions</h2>";
$uploadDir = 'uploads/medical_receipts/';
$fullPath = __DIR__ . '/' . $uploadDir;

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Check</th><th>Path</th><th>Status</th></tr>";
echo "<tr><td>Directory Exists</td><td>$fullPath</td><td>" . (is_dir($fullPath) ? '✅ Yes' : '❌ No') . "</td></tr>";
echo "<tr><td>Is Writable</td><td>$fullPath</td><td>" . (is_writable($fullPath) ? '✅ Yes' : '❌ No') . "</td></tr>";
echo "<tr><td>Is Readable</td><td>$fullPath</td><td>" . (is_readable($fullPath) ? '✅ Yes' : '❌ No') . "</td></tr>";

if (is_dir($fullPath)) {
    $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
    echo "<tr><td>Permissions</td><td>$perms</td><td>" . ($perms >= '0755' ? '✅' : '⚠️') . "</td></tr>";
}
echo "</table>";

// Check Files in Directory
echo "<h2>Files in Upload Directory</h2>";
if (is_dir($fullPath)) {
    $files = scandir($fullPath);
    if (count($files) > 2) { // . and ..
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $fullPath . $file;
                $size = filesize($filePath);
                echo "<li>$file (" . number_format($size) . " bytes) - <a href='uploads/medical_receipts/$file' target='_blank'>View</a></li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ No files found in directory</p>";
    }
} else {
    echo "<p>❌ Directory does not exist</p>";
}

// Test File Upload Form
echo "<h2>Test File Upload</h2>";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Result:</h3>";
    echo "<pre>";
    print_r($_FILES['test_file']);
    echo "</pre>";
    
    if ($_FILES['test_file']['error'] == 0) {
        $targetPath = $fullPath . $_FILES['test_file']['name'];
        if (move_uploaded_file($_FILES['test_file']['tmp_name'], $targetPath)) {
            echo "<p style='color: green;'>✅ File uploaded successfully to: $targetPath</p>";
            echo "<p><a href='uploads/medical_receipts/{$_FILES['test_file']['name']}' target='_blank'>View Uploaded File</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to move uploaded file</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Upload error code: {$_FILES['test_file']['error']}</p>";
    }
}

echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file' required>";
echo "<button type='submit'>Upload Test File</button>";
echo "</form>";

echo "<h2>Database Record Check</h2>";
// Connect to database
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cricket_academy');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $stmt = $pdo->query("SELECT RecordID, PlayerID, DiagnosisReceiptURL FROM PlayerMedicalRecord WHERE DiagnosisReceiptURL IS NOT NULL ORDER BY RecordID DESC LIMIT 5");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($records) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>RecordID</th><th>PlayerID</th><th>Receipt URL</th><th>File Exists?</th><th>Link</th></tr>";
        foreach ($records as $record) {
            $filePath = __DIR__ . '/' . $record['DiagnosisReceiptURL'];
            $exists = file_exists($filePath);
            echo "<tr>";
            echo "<td>{$record['RecordID']}</td>";
            echo "<td>{$record['PlayerID']}</td>";
            echo "<td>{$record['DiagnosisReceiptURL']}</td>";
            echo "<td>" . ($exists ? '✅ Yes' : '❌ No') . "</td>";
            echo "<td><a href='{$record['DiagnosisReceiptURL']}' target='_blank'>Test Link</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records with receipts found</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; margin: 10px 0; }
    th { background: #4A90E2; color: white; }
    h2 { margin-top: 30px; color: #333; }
</style>
