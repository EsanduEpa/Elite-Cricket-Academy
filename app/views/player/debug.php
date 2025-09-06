<?php
// Debug page to test if Player controller is working
echo "<h1>Player Controller Debug</h1>";
echo "<h2>Testing Player Dashboard System</h2>";

echo "<div style='font-family: Arial; padding: 20px; background: #f8f9fa;'>";

echo "<h3>Available Player Pages:</h3>";
echo "<ul>";
echo "<li><a href='" . URLROOT . "/player/test' target='_blank'>Dashboard (Test Mode)</a></li>";
echo "<li><a href='" . URLROOT . "/player/training' target='_blank'>Training Schedule</a></li>";
echo "<li><a href='" . URLROOT . "/player/performance' target='_blank'>Performance</a></li>";
echo "<li><a href='" . URLROOT . "/player/payments' target='_blank'>Payments</a></li>";
echo "</ul>";

echo "<h3>System Info:</h3>";
echo "<p><strong>URLROOT:</strong> " . (defined('URLROOT') ? URLROOT : 'Not defined') . "</p>";
echo "<p><strong>APPROOT:</strong> " . (defined('APPROOT') ? APPROOT : 'Not defined') . "</p>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

echo "<h3>Player Dashboard Preview:</h3>";
echo "<p>If the links above work, you'll see:</p>";
echo "<ul>";
echo "<li>✅ Modern sidebar navigation</li>";
echo "<li>✅ Performance statistics cards</li>";
echo "<li>✅ Today's schedule section</li>";
echo "<li>✅ Responsive design</li>";
echo "<li>✅ Beautiful animations</li>";
echo "</ul>";

echo "</div>";
?>
