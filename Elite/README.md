### Step 1: Create the Login Controller

1. **Create a new file** named `Login.php` in the `app/controllers` directory.

```php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/Elite/app/controllers/Login.php
<?php

class Login extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('M_Users');
    }

    public function index() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            redirect('dashboard');
        }

        // Check for POST request
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => ''
            ];

            // Validate
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter your username';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter your password';
            }

            // Check for errors
            if(empty($data['username_err']) && empty($data['password_err'])) {
                // Check and authenticate user
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if($loggedInUser) {
                    // Create session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('v_login', $data);
                }
            } else {
                // Load view with errors
                $this->view('v_login', $data);
            }
        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('v_login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        redirect('dashboard');
    }
}
?>
```

### Step 2: Create the Login View

2. **Create a new file** named `v_login.php` in the `app/views` directory.

```php
<!-- filepath: /Applications/XAMPP/xamppfiles/htdocs/Elite/app/views/v_login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="<?php echo URLROOT; ?>/login" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo $data['username']; ?>">
                <span><?php echo $data['username_err']; ?></span>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password">
                <span><?php echo $data['password_err']; ?></span>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
        </form>
        <p>Don't have an account? <a href="<?php echo URLROOT; ?>/register">Register here</a></p>
    </div>
</body>
</html>
```

### Step 3: Update the Header

3. **Locate the header file** where the login button is defined (this could be in a layout file or a specific view file).

4. **Add a link to the login page** in the header. For example:

```php
<!-- Example header code -->
<nav>
    <ul>
        <li><a href="<?php echo URLROOT; ?>/home">Home</a></li>
        <li><a href="<?php echo URLROOT; ?>/register">Register</a></li>
        <li><a href="<?php echo URLROOT; ?>/login">Login</a></li> <!-- Link to the login page -->
    </ul>
</nav>
```

### Step 4: Update the Routes

5. **Ensure your routing is set up** to handle the login page. If you are using a front controller, make sure that the URL `/login` points to the `Login` controller's `index` method.

### Step 5: Test the Login Functionality

6. **Run your application** and navigate to the login page using the link in the header. Test the login functionality to ensure everything works as expected.

### Conclusion

You have now created a new login page, linked it to the header, and set up the necessary controller and view files. Make sure to adjust any paths or URLs according to your application's structure.