<?php
// Simple debug script to test profile data loading
session_start();

// Set up basic includes
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';

// Set session data for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_role'] = 'Player';

// Test the database query directly
try {
    $db = new Database();
    
    echo "<h2>Testing Profile Data Loading</h2>";
    echo "<p>Session User ID: " . ($_SESSION['user_id'] ?? 'Not Set') . "</p>";
    echo "<p>Session User Name: " . ($_SESSION['user_name'] ?? 'Not Set') . "</p>";
    echo "<p>Session User Role: " . ($_SESSION['user_role'] ?? 'Not Set') . "</p>";
    
    // Test getUserWithProfile query
    $userId = $_SESSION['user_id'];
    $db->query('SELECT u.*, 
                        pp.BattingStyle, pp.BowlingStyle, pp.JerseyNumber, 
                        pp.SubscriptionType, pp.SchoolInstitution, pp.EmergencyContactName, 
                        pp.EmergencyContactPhone, pp.ParentGuardianName, pp.ParentGuardianPhone,
                        cp.Specialization as CoachSpecialization, cp.Experience as CoachExperience, 
                        cp.Certifications as CoachCertifications, cp.IsHeadCoach,
                        tp.Experience as TrainerExperience, tp.Certifications as TrainerCertifications,
                        sep.Department as ShopDepartment, sep.HireDate as ShopHireDate,
                        ap.Section as AdminSection, ap.Department as AdminDepartment, 
                        ap.AccessLevel as AdminAccessLevel, ap.HireDate as AdminHireDate
                 FROM User u 
                 LEFT JOIN PlayerProfile pp ON u.UserID = pp.PlayerID 
                 LEFT JOIN CoachProfile cp ON u.UserID = cp.CoachID
                 LEFT JOIN TrainerProfile tp ON u.UserID = tp.TrainerID
                 LEFT JOIN ShopEmployeeProfile sep ON u.UserID = sep.ShopEmployeeID
                 LEFT JOIN AdminProfile ap ON u.UserID = ap.AdminID
                 WHERE u.UserID = :user_id');
    
    $db->bind(':user_id', $userId);
    $result = $db->single();
    
    echo "<h3>Query Result:</h3>";
    if ($result) {
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>Query returned FALSE - no user found or database error</p>";
        
        // Try a simpler query
        echo "<h3>Trying Basic User Query:</h3>";
        $db->query('SELECT * FROM User WHERE UserID = :user_id');
        $db->bind(':user_id', $userId);
        $basicResult = $db->single();
        
        if ($basicResult) {
            echo "<pre>";
            print_r($basicResult);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>Basic query also failed - user ID " . $userId . " does not exist</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>