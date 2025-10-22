<?php
// Quick diagnostic - shows last 50 lines of PHP error log
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Error Log Viewer</h2>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;} h2{color:#4A90E2;} pre{background:#2d2d2d;padding:20px;border-radius:5px;border-left:4px solid #ff6b6b;overflow-x:auto;} .error{color:#ff6b6b;} .warning{color:#ffa500;} .info{color:#4A90E2;} .success{color:#2ed573;}</style>";

$logLocations = [
    'C:\\xampp\\php\\logs\\php_error_log',
    'C:\\xampp\\apache\\logs\\error.log',
    dirname(dirname(__DIR__)) . '\\php_error_log',
    ini_get('error_log')
];

echo "<h3>Searching for error logs...</h3>";

foreach ($logLocations as $logFile) {
    if (file_exists($logFile)) {
        echo "<div class='success'>✓ Found: {$logFile}</div>";
        echo "<h3>Last 100 lines:</h3>";
        
        $lines = file($logFile);
        $lastLines = array_slice($lines, -100);
        
        echo "<pre>";
        foreach ($lastLines as $line) {
            // Highlight different types of messages
            if (stripos($line, 'workout') !== false || stripos($line, 'WorkoutPlan') !== false) {
                echo "<span class='error'>" . htmlspecialchars($line) . "</span>";
            } elseif (stripos($line, 'error') !== false) {
                echo "<span class='warning'>" . htmlspecialchars($line) . "</span>";
            } elseif (stripos($line, 'success') !== false) {
                echo "<span class='success'>" . htmlspecialchars($line) . "</span>";
            } else {
                echo htmlspecialchars($line);
            }
        }
        echo "</pre>";
        
    } else {
        echo "<div class='info'>⊘ Not found: {$logFile}</div>";
    }
}

echo "<hr>";
echo "<h3>PHP Configuration:</h3>";
echo "<pre>";
echo "error_log setting: " . ini_get('error_log') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "</pre>";
?>
