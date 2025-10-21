<?php
session_start();
session_unset();
session_destroy();
echo "✅ Session cleared! Please refresh the page and try adding staff again.";
?>
