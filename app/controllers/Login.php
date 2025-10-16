<?php

class Login extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('M_Users');
    }

    public function index() {
        // ========== SIMPLIFIED LOGIN - CREDENTIAL CHECKING WITH ROLE-BASED REDIRECT ==========
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            // Redirect to appropriate dashboard based on stored role
            switch($_SESSION['user_role']) {
                case 'Admin':
                    redirect('admin/dashboard');
                    break;
                case 'Coach':
                    redirect('coach/dashboard');
                    break;
                case 'Trainer':
                    redirect('trainer/dashboard');
                    break;
                case 'ShopEmployee':
                    redirect('shop/dashboard');
                    break;
                case 'Player':
                default:
                    redirect('player/dashboard');
                    break;
            }
        }
        
        // Check for POST request
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // Basic validation
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email or username';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check credentials if no validation errors
            if(empty($data['email_err']) && empty($data['password_err'])) {
                
                // Attempt to validate credentials
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                
                if($loggedInUser) {
                    // ✅ CREDENTIALS MATCH - Create session and redirect based on role
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    $_SESSION['user_id'] = $loggedInUser->UserID;
                    $_SESSION['user_email'] = $loggedInUser->Email;
                    $_SESSION['user_name'] = $loggedInUser->Name;
                    $_SESSION['user_role'] = $loggedInUser->Role;
                    
                    // DEBUG: Show what's happening (remove this after testing)
                    echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 20px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
                    echo "<h3>✅ Login Successful - Debug Info</h3>";
                    echo "<p><strong>User:</strong> " . $loggedInUser->Name . "</p>";
                    echo "<p><strong>Role:</strong> " . $loggedInUser->Role . "</p>";
                    echo "<p><strong>Should redirect to:</strong> ";
                    
                    // Redirect based on user role
                    switch($loggedInUser->Role) {
                        case 'Admin':
                            echo "admin/dashboard";
                            echo "</p>";
                            echo "<p><a href='" . URLROOT . "/admin/dashboard'>Click here if redirect doesn't work →</a></p>";
                            echo "</div>";
                            redirect('admin/dashboard');
                            break;
                        case 'Coach':
                            echo "coach/dashboard";
                            echo "</p>";
                            echo "<p><a href='" . URLROOT . "/coach/dashboard'>Click here if redirect doesn't work →</a></p>";
                            echo "</div>";
                            redirect('coach/dashboard');
                            break;
                        case 'Trainer':
                            echo "trainer/dashboard";
                            echo "</p>";
                            echo "<p><a href='" . URLROOT . "/trainer/dashboard'>Click here if redirect doesn't work →</a></p>";
                            echo "</div>";
                            redirect('trainer/dashboard');
                            break;
                        case 'ShopEmployee':
                            echo "shop/dashboard";
                            echo "</p>";
                            echo "<p><a href='" . URLROOT . "/shop/dashboard'>Click here if redirect doesn't work →</a></p>";
                            echo "</div>";
                            redirect('shop/dashboard');
                            break;
                        case 'Player':
                        default:
                            echo "player/dashboard";
                            echo "</p>";
                            echo "<p><a href='" . URLROOT . "/player/dashboard'>Click here if redirect doesn't work →</a></p>";
                            echo "</div>";
                            redirect('player/dashboard');
                            break;
                    }
                } else {
                    // ❌ CREDENTIALS DON'T MATCH
                    $data['password_err'] = 'Invalid email/username or password';
                }
            }

            // Load view with errors
            $this->view('v_login', $data);
        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('v_login', $data);
        }

        /* ========== COMMENTED OUT: FULL LOGIN SYSTEM ==========
        
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            redirect('player/dashboard');
        }

        // Check for POST request
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // Validation
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email or username';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for errors
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Check if account is locked
                if($this->userModel->isAccountLocked($data['email'])) {
                    $data['email_err'] = 'Account is temporarily locked due to multiple failed login attempts. Please try again later.';
                } else {
                    // Attempt login
                    $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                    
                    if($loggedInUser) {
                        // Update last login and reset login attempts
                        $this->userModel->updateLastLogin($loggedInUser->UserID);
                        
                        // Log successful login
                        $this->userModel->logActivity(
                            $loggedInUser->UserID,
                            'login',
                            'User logged in successfully',
                            $_SERVER['REMOTE_ADDR'] ?? null,
                            $_SERVER['HTTP_USER_AGENT'] ?? null
                        );
                        
                        // Create session
                        $this->createUserSession($loggedInUser);
                    } else {
                        // Increment login attempts
                        $this->userModel->incrementLoginAttempts($data['email']);
                        $data['password_err'] = 'Password incorrect';
                    }
                }
            }

            // Load view with errors
            $this->view('v_login', $data);
        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('v_login', $data);
        }
        
        ========== END COMMENTED SECTION ========== */
    }

    // Simple logout method
    public function logout() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        session_unset();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        redirect('login');
    }
    
    /* ========== COMMENTED OUT: ADVANCED SESSION MANAGEMENT ==========
    
    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->UserID;
        $_SESSION['user_email'] = $user->Email;
        $_SESSION['user_name'] = $user->Name;
        $_SESSION['user_role'] = $user->Role;
        
        // Redirect based on user role
        switch($user->Role) {
            case 'Admin':
                redirect('admin/dashboard');
                break;
            case 'Coach':
                redirect('coach/dashboard');
                break;
            case 'Trainer':
                redirect('trainer/dashboard');
                break;
            case 'ShopEmployee':
                redirect('shop/dashboard');
                break;
            default:
                redirect('player/dashboard');
        }
    }
    
    // Advanced logout with activity logging
    public function logout_advanced() {
        // Log logout activity if user is logged in
        if(isset($_SESSION['user_id'])) {
            $this->userModel->logActivity(
                $_SESSION['user_id'],
                'logout',
                'User logged out',
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        }
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        session_unset();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to home page
        redirect('');
    }
    
    ========== END COMMENTED ADVANCED SESSION MANAGEMENT ========== */
}
?>