<?php
// Simple upload test handler
// This will help identify if the issue is with file upload or the controller logic

echo "<h2>Upload Test Result</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $file = $_FILES['test_image'];
    
    echo "<h3>File Information:</h3>";
    echo "Name: " . $file['name'] . "<br>";
    echo "Type: " . $file['type'] . "<br>";
    echo "Size: " . $file['size'] . " bytes<br>";
    echo "Temp Name: " . $file['tmp_name'] . "<br>";
    echo "Error Code: " . $file['error'] . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        echo "✅ File uploaded to temporary location<br><br>";
        
        // Check file type
        $actualType = mime_content_type($file['tmp_name']);
        echo "Actual MIME type: " . $actualType . "<br>";
        
        // Define paths
        define('APPROOT', dirname(dirname(__FILE__)) . '/app');
        $projectRoot = dirname(APPROOT);
        $uploadDir = $projectRoot . '/public/uploads/profile_images/';
        
        // Create directory if needed
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFileName = 'test_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $newFileName;
        
        echo "Upload path: " . $uploadPath . "<br><br>";
        
        // Try to move file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo "✅ <strong>SUCCESS!</strong> File moved to: " . $uploadPath . "<br>";
            echo "File size on disk: " . filesize($uploadPath) . " bytes<br>";
            echo "File permissions: " . substr(sprintf('%o', fileperms($uploadPath)), -4) . "<br>";
            
            // Show the image if it's an image
            if (in_array($actualType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                $webPath = '/Elite/public/uploads/profile_images/' . $newFileName;
                echo "<br><h3>Uploaded Image:</h3>";
                echo '<img src="' . $webPath . '" style="max-width: 300px; border: 2px solid #4A90E2;"><br>';
            }
            
            echo "<br><a href='test_upload_config.php'>Back to Config Test</a>";
        } else {
            echo "❌ <strong>FAILED</strong> to move uploaded file<br>";
            echo "This usually means a permissions issue<br>";
            echo "<br><a href='test_upload_config.php'>Back to Config Test</a>";
        }
    } else {
        echo "❌ Upload error occurred: ";
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temp directory',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
            UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
        ];
        echo $errors[$file['error']] ?? 'Unknown error';
        echo "<br><br><a href='test_upload_config.php'>Back to Config Test</a>";
    }
} else {
    echo "No file uploaded<br>";
    echo "<a href='test_upload_config.php'>Back to Config Test</a>";
}
?>
