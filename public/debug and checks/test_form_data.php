<?php
// Simple test to see what data is being sent to login controller
echo "<h2>Login Form Data Test</h2>";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>✅ POST Request Received!</h3>";
    echo "<p><strong>Raw POST data:</strong></p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<p><strong>Filtered POST data:</strong></p>";
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    echo "<p><strong>Processed values:</strong></p>";
    echo "<ul>";
    echo "<li>Email: " . htmlspecialchars($email) . "</li>";
    echo "<li>Password: " . str_repeat('*', strlen($password)) . " (" . strlen($password) . " chars)</li>";
    echo "</ul>";
    
    if(!empty($email) && !empty($password)) {
        echo "<p style='color: green;'>✅ Both fields have values - ready for login validation!</p>";
        echo "<p><a href='" . URLROOT . "/login'>← Back to Login Form</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Missing email or password!</p>";
    }
} else {
    echo "<p>No POST data received. Submit the login form to see the data.</p>";
}

echo "<hr>";
echo "<h3>Quick Login Test Form</h3>";
?>

<form method="POST" style="max-width: 400px;">
    <div style="margin-bottom: 15px;">
        <label for="email">Email or Username:</label><br>
        <input type="text" id="email" name="email" style="width: 100%; padding: 8px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" style="width: 100%; padding: 8px;" required>
    </div>
    
    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer;">
        Test Form Data
    </button>
</form>

<hr>
<p><a href="<?php echo URLROOT; ?>/login">← Go to Actual Login Form</a></p>