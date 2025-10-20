<?php
// Direct test of injury reports
require_once '../app/bootloader.php';

// Set up session
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test Trainer';
$_SESSION['user_type'] = 'Trainer';
$_SESSION['user_role'] = 'Trainer';

echo "<h1>Direct Injury Reports Test</h1>";

try {
    // Create trainer instance
    $trainer = new Trainer();
    
    echo "<h2>Calling injuryReports method...</h2>";
    
    // Call the method directly
    $trainer->injuryReports();
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "<h3>Error occurred:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>