<?php
// Debug role-based redirects
require_once '../app/config/config.php';
require_once '../app/helpers/session_helper.php';

echo "<h2>Role-Based Redirect Testing</h2>";

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h3>Current Session Data:</h3>";
if(!empty($_SESSION)) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "<p>No session data found.</p>";
}

if(isset($_POST['test_role'])) {
    $role = $_POST['test_role'];
    
    // Set up test session
    $_SESSION['user_id'] = 999;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_email'] = 'test@example.com';
    $_SESSION['user_role'] = $role;
    
    echo "<h3>✅ Test Session Created for Role: $role</h3>";
    echo "<p>Now testing redirect...</p>";
    
    // Test the redirect logic
    echo "<div style='background: #e7f3ff; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3;'>";
    echo "<p><strong>Redirect Test for Role: $role</strong></p>";
    
    switch($role) {
        case 'Admin':
            echo "<p>Should redirect to: <a href='" . URLROOT . "/admin/dashboard' target='_blank'>" . URLROOT . "/admin/dashboard</a></p>";
            break;
        case 'Coach':
            echo "<p>Should redirect to: <a href='" . URLROOT . "/coach/dashboard' target='_blank'>" . URLROOT . "/coach/dashboard</a></p>";
            break;
        case 'Trainer':
            echo "<p>Should redirect to: <a href='" . URLROOT . "/trainer/dashboard' target='_blank'>" . URLROOT . "/trainer/dashboard</a></p>";
            break;
        case 'ShopEmployee':
            echo "<p>Should redirect to: <a href='" . URLROOT . "/shop/dashboard' target='_blank'>" . URLROOT . "/shop/dashboard</a></p>";
            break;
        case 'Player':
        default:
            echo "<p>Should redirect to: <a href='" . URLROOT . "/player/dashboard' target='_blank'>" . URLROOT . "/player/dashboard</a></p>";
            break;
    }
    
    echo "<p><strong>Click the link above to test if the dashboard loads correctly.</strong></p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<p><a href='test_dashboards.php'>← Refresh Test Page</a></p>";
}
?>

<h3>🧪 Test Different Role Redirects</h3>
<p>Select a role to test the redirect and dashboard access:</p>

<form method="POST" style="margin: 20px 0;">
    <div style="margin-bottom: 15px;">
        <label for="test_role">Select Role to Test:</label><br>
        <select id="test_role" name="test_role" style="width: 200px; padding: 8px;" required>
            <option value="">Choose a role...</option>
            <option value="Admin">Admin</option>
            <option value="Coach">Coach</option>
            <option value="Trainer">Trainer</option>
            <option value="ShopEmployee">Shop Employee</option>
            <option value="Player">Player</option>
        </select>
    </div>
    
    <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">
        Test Role Redirect
    </button>
</form>

<hr>

<h3>📋 Available Dashboard URLs</h3>
<ul>
    <li><strong>Admin:</strong> <a href="<?php echo URLROOT; ?>/admin/dashboard" target="_blank"><?php echo URLROOT; ?>/admin/dashboard</a></li>
    <li><strong>Coach:</strong> <a href="<?php echo URLROOT; ?>/coach/dashboard" target="_blank"><?php echo URLROOT; ?>/coach/dashboard</a></li>
    <li><strong>Trainer:</strong> <a href="<?php echo URLROOT; ?>/trainer/dashboard" target="_blank"><?php echo URLROOT; ?>/trainer/dashboard</a></li>
    <li><strong>Player:</strong> <a href="<?php echo URLROOT; ?>/player/dashboard" target="_blank"><?php echo URLROOT; ?>/player/dashboard</a></li>
</ul>

<p><em>Try clicking these links directly to see which dashboards work and which don't.</em></p>

<hr>
<p><a href="<?php echo URLROOT; ?>/login">← Back to Login</a></p>
