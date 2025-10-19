<?php
// Quick URL test
require_once '../app/config/config.php';

echo "<h2>URL Configuration Test</h2>";
echo "<p><strong>URLROOT:</strong> " . URLROOT . "</p>";
echo "<p><strong>Current URL should be:</strong> http://localhost/Elite/url_test.php</p>";

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='" . URLROOT . "/admin/dashboard'>Admin Dashboard</a> - Should be: http://localhost/Elite/admin/dashboard</li>";
echo "<li><a href='" . URLROOT . "/coach/dashboard'>Coach Dashboard</a> - Should be: http://localhost/Elite/coach/dashboard</li>";
echo "<li><a href='" . URLROOT . "/player/dashboard'>Player Dashboard</a> - Should be: http://localhost/Elite/player/dashboard</li>";
echo "<li><a href='" . URLROOT . "/login'>Login Page</a> - Should be: http://localhost/Elite/login</li>";
echo "</ul>";

echo "<h3>Debug Info:</h3>";
echo "<p>Server: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
?>