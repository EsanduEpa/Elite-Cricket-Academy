<?php
// Clear opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache cleared!\n";
} else {
    echo "Opcache not available\n";
}

// Also clear file stat cache
clearstatcache(true);
echo "File stat cache cleared!\n";

echo "Now try: http://localhost/Elite/public/test_events.php\n";
?>
