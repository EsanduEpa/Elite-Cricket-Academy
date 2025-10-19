<?php
// Debug login process step by step
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';
require_once '../app/models/M_Users.php';

echo "<h2>Login Debug Tool</h2>";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    echo "<h3>🔍 Debugging Login Process</h3>";
    echo "<p><strong>Input:</strong></p>";
    echo "<ul>";
    echo "<li>Email/Username: " . htmlspecialchars($email) . "</li>";
    echo "<li>Password: " . str_repeat('*', strlen($password)) . " (" . strlen($password) . " chars)</li>";
    echo "</ul>";
    
    try {
        $userModel = new M_Users();
        $db = new Database();
        
        // Step 1: Check if user exists
        echo "<h4>Step 1: Looking for user in database...</h4>";
        $db->query('SELECT * FROM User WHERE Email = :email OR Username = :email');
        $db->bind(':email', $email);
        $user = $db->single();
        
        if($user) {
            echo "<p style='color: green;'>✅ User found!</p>";
            echo "<ul>";
            echo "<li>UserID: " . $user->UserID . "</li>";
            echo "<li>Name: " . $user->Name . "</li>";
            echo "<li>Email: " . $user->Email . "</li>";
            echo "<li>Username: " . $user->Username . "</li>";
            echo "<li>Role: " . $user->Role . "</li>";
            echo "<li>Status: " . $user->Status . "</li>";
            echo "</ul>";
            
            // Step 2: Check password
            echo "<h4>Step 2: Verifying password...</h4>";
            echo "<p>Stored password hash: " . substr($user->PasswordHash, 0, 20) . "...</p>";
            
            if(password_verify($password, $user->PasswordHash)) {
                echo "<p style='color: green;'>✅ Password verification successful!</p>";
                
                // Step 3: Test the actual login method
                echo "<h4>Step 3: Testing login method...</h4>";
                $loginResult = $userModel->login($email, $password);
                
                if($loginResult) {
                    echo "<p style='color: green;'>✅ Login method returned user object!</p>";
                    echo "<p><strong>Login would redirect to:</strong> ";
                    switch($loginResult->Role) {
                        case 'Admin':
                            echo "admin/dashboard";
                            break;
                        case 'Coach':
                            echo "coach/dashboard";
                            break;
                        case 'Trainer':
                            echo "trainer/dashboard";
                            break;
                        case 'ShopEmployee':
                            echo "shop/dashboard";
                            break;
                        case 'Player':
                        default:
                            echo "player/dashboard";
                            break;
                    }
                    echo "</p>";
                } else {
                    echo "<p style='color: red;'>❌ Login method returned false (unexpected!)</p>";
                }
                
            } else {
                echo "<p style='color: red;'>❌ Password verification failed!</p>";
                echo "<p>The entered password does not match the stored hash.</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ No user found with that email/username!</p>";
            
            // Show available users for reference
            echo "<h4>Available users in database:</h4>";
            $db->query('SELECT UserID, Name, Email, Username FROM User LIMIT 5');
            $users = $db->resultSet();
            
            if($users) {
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>UserID</th><th>Name</th><th>Email</th><th>Username</th></tr>";
                foreach($users as $u) {
                    echo "<tr>";
                    echo "<td>" . $u->UserID . "</td>";
                    echo "<td>" . $u->Name . "</td>";
                    echo "<td>" . $u->Email . "</td>";
                    echo "<td>" . $u->Username . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
    echo "<hr>";
}
?>

<h3>🧪 Test Login Credentials</h3>
<form method="POST" style="max-width: 400px;">
    <div style="margin-bottom: 15px;">
        <label for="email">Email or Username:</label><br>
        <input type="text" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" style="width: 100%; padding: 8px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" style="width: 100%; padding: 8px;" required>
    </div>
    
    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer;">
        Debug Login Process
    </button>
</form>

<hr>
<p><a href="<?php echo URLROOT; ?>/login">← Back to Login Page</a></p>
<p><a href="<?php echo URLROOT; ?>/register">Register New User →</a></p>