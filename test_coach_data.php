<?php
require_once 'app/config/config.php';
require_once 'app/libraries/Database.php';

// Test Medical Records Query
echo "<h2>Testing Medical Records Query</h2>";
$db = new Database();
$db->query('SELECT 
    pmr.*,
    u.Name as PlayerName,
    u.ContactNumber as PlayerContact,
    u.Email as PlayerEmail
    FROM PlayerMedicalRecord pmr 
    LEFT JOIN User u ON pmr.PlayerID = u.UserID
    WHERE u.Role = "player"
    ORDER BY pmr.InjuryDate DESC, pmr.ReportedDate DESC');
$medicalRecords = $db->resultSet();
echo "<pre>";
echo "Medical Records Count: " . count($medicalRecords) . "\n\n";
print_r($medicalRecords);
echo "</pre>";

echo "<hr>";

// Test Achievements Query
echo "<h2>Testing Achievements Query</h2>";
$db->query('SELECT 
    a.*,
    u.Name as PlayerName,
    u.ContactNumber as PlayerContact,
    u.Email as PlayerEmail
    FROM Achievements a
    LEFT JOIN User u ON a.PlayerID = u.UserID
    WHERE u.Role = "player"
    ORDER BY a.Date DESC, a.CreatedAt DESC');
$achievements = $db->resultSet();
echo "<pre>";
echo "Achievements Count: " . count($achievements) . "\n\n";
print_r($achievements);
echo "</pre>";
?>
