<?php

/**
 * LOGIN CONTROLLER
 * 
 * Purpose: Handle user authentication (login/logout) for the cricket academy
 * Responsibilities:
 *   1. Display login form
 *   2. Validate credentials against database
 *   3. Create user session upon successful login
 *   4. Redirect users to appropriate dashboard based on role
 *   5. Handle logout and session destruction
 */
class Login extends Controller {
    // Store reference to User model for database operations
    private $userModel;

    /**
     * CONSTRUCTOR - Runs when Login controller is instantiated
     * Loads the M_Users model for authentication queries
     */
    public function __construct() {
        $this->userModel = $this->model('M_Users');
    }

    /**
     * INDEX METHOD - Main login handler
     * Handles both GET (show form) and POST (process login) requests
     */
    public function index() {
        // ========== AUTHENTICATION SYSTEM ==========
        
        // STEP 1: ENSURE SESSION IS STARTED
        // Sessions are needed to store logged-in user information
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
        
        // STEP 3: DETERMINE REQUEST TYPE
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // === POST REQUEST - PROCESS LOGIN ATTEMPT ===
            
            // STEP 4: SANITIZE USER INPUT
            // Prevent XSS attacks by cleaning all POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // STEP 5: PREPARE DATA ARRAY
            // Store credentials and error messages
            $data = [
                // User inputs (supports both email and username)
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                // Error message placeholders
                'email_err' => '',
                'password_err' => ''
            ];

            // STEP 6: BASIC INPUT VALIDATION
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
                    // === STEP 9: AUTHENTICATION SUCCESSFUL ===
                    // Credentials are valid - create user session

                    $assignmentRefresh = null;
                    if (($loggedInUser->Role ?? '') === 'Player') {
                        $assignmentRefresh = $this->userModel->refreshPlayerAssignmentsOnLogin((int)$loggedInUser->UserID);

                        if (!empty($assignmentRefresh['updated'])) {
                            $notificationModel = $this->model('M_Notification');
                            $assignmentLabels = [];

                            foreach (($assignmentRefresh['assignments'] ?? []) as $assignment) {
                                $assignmentLabels[] = ucfirst((string)$assignment->CoachingType) . ': ' . (string)$assignment->CoachName;
                            }

                            $messageParts = [];
                            if (!empty($assignmentRefresh['ageGroupChanged']) && !empty($assignmentRefresh['ageGroup'])) {
                                $messageParts[] = 'Your age group is now ' . $assignmentRefresh['ageGroup'] . '.';
                            }

                            if (!empty($assignmentRefresh['assignmentsChanged'])) {
                                if (!empty($assignmentLabels)) {
                                    $messageParts[] = 'Your coach assignments were refreshed: ' . implode(', ', $assignmentLabels) . '.';
                                } else {
                                    $messageParts[] = 'Your coach assignments were refreshed.';
                                }
                            }

                            if (!empty($messageParts)) {
                                $notificationModel->create(
                                    (int)$loggedInUser->UserID,
                                    'coach_assignment',
                                    'Age Group / Coach Assignment Updated',
                                    implode(' ', $messageParts),
                                    URLROOT . '/player/dashboard'
                                );
                            }
                        }
                    }
                    
                    // Ensure session is started (safety check)
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // STEP 10: STORE USER DATA IN SESSION
                    // This data persists across pages until logout
                    // $_SESSION is a PHP superglobal accessible anywhere
                    $_SESSION['user_id'] = $loggedInUser->UserID;        // Unique identifier
                    $_SESSION['user_email'] = $loggedInUser->Email;      // User's email
                    $_SESSION['user_name'] = $loggedInUser->Name;        // Display name
                    $_SESSION['user_role'] = $loggedInUser->Role;        // For access control
                    $_SESSION['last_activity'] = time();                 // Inactivity timeout clock
                    
                    // STEP 11: ROLE-BASED REDIRECT
                    // Different user roles access different dashboards
                    // This implements Role-Based Access Control (RBAC)
                    // Important: do not echo output before redirect(), because redirects use HTTP headers.
                    switch($loggedInUser->Role) {
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
                } else {
                    // === AUTHENTICATION FAILED ===
                    // Credentials are invalid (wrong email/username or password)
                    // SECURITY NOTE: We don't specify which field is wrong to prevent
                    // attackers from knowing if an email/username exists in the system
                    $data['password_err'] = 'Invalid email/username or password';
                }
            }

            // STEP 13: RELOAD LOGIN FORM WITH ERRORS
            // If validation failed or authentication failed, show form again
            $this->view('v_login', $data);
        } else {
            // === GET REQUEST - DISPLAY LOGIN FORM ===
            // User is visiting login page for the first time
            
            // Initialize empty data array
            $data = [
                'email' => '',          // Empty email field
                'password' => '',       // Empty password field
                'email_err' => '',      // No errors yet
                'password_err' => ''    // No errors yet
            ];

            // LOAD LOGIN VIEW
            // Display the login form (v_login.php)
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

    /**
     * LOGOUT METHOD - Terminate user session
     * 
     * Purpose: Securely log out the user and clean up their session
     * Steps:
     *   1. Start session (to access session data)
     *   2. Clear all session variables
     *   3. Destroy the session entirely
     *   4. Redirect to login page
     */
    public function logout() {
        // STEP 1: ENSURE SESSION IS STARTED
        // We need an active session to destroy it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        destroyUserSession();
        redirect('');
    }
    
    // Forgot Password
    public function forgot_password() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'username_err' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Validation
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            if(empty($data['new_password'])) {
                $data['password_err'] = 'Please enter new password';
            } elseif(strlen($data['new_password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            if($data['new_password'] != $data['confirm_password']) {
                $data['password_err'] = 'Passwords do not match';
            }

            // Check if no errors
            if(empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err'])) {
                // Verify user exists with matching username and email
                $user = $this->userModel->getUserByUsernameAndEmail($data['username'], $data['email']);
                
                if($user) {
                    // Hash new password
                    $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
                    
                    // Update password
                    if($this->userModel->updatePassword($user->UserID, $hashedPassword)) {
                        // Success
                        flash('login_message', 'Password reset successful! You can now login with your new password.', 'alert alert-success');
                        redirect('login');
                    } else {
                        flash('login_message', 'Something went wrong. Please try again.', 'alert alert-danger');
                        redirect('login');
                    }
                } else {
                    // User not found or credentials don't match
                    flash('login_message', 'Invalid username or email. Please check your credentials.', 'alert alert-danger');
                    redirect('login');
                }
            } else {
                // Show errors
                $errorMessages = array_filter([$data['username_err'], $data['email_err'], $data['password_err']]);
                flash('login_message', implode('<br>', $errorMessages), 'alert alert-danger');
                redirect('login');
            }
        } else {
            redirect('login');
        }
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
