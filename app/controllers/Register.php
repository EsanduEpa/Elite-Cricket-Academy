<?php

class Register extends Controller {
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
            // Process form - sanitize input data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'fullName' => trim($_POST['fullName']),
                'dateOfBirth' => trim($_POST['dateOfBirth']),
                'address' => trim($_POST['address']),
                'email' => trim($_POST['email']),
                'contactNumber' => trim($_POST['contactNumber']),
                'school' => trim($_POST['school']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'confirmPassword' => trim($_POST['confirmPassword']),
                'fullName_err' => '',
                'dateOfBirth_err' => '',
                'address_err' => '',
                'email_err' => '',
                'contactNumber_err' => '',
                'school_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirmPassword_err' => ''
            ];

            // Validation
            if(empty($data['fullName'])) {
                $data['fullName_err'] = 'Please enter your full name';
            }

            if(empty($data['dateOfBirth'])) {
                $data['dateOfBirth_err'] = 'Please enter your date of birth';
            } else {
                // Validate date of birth
                $dob = new DateTime($data['dateOfBirth']);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                
                if($age < 5) {
                    $data['dateOfBirth_err'] = 'You must be at least 5 years old to register';
                } elseif($age > 100) {
                    $data['dateOfBirth_err'] = 'Please enter a valid date of birth';
                } elseif($dob > $today) {
                    $data['dateOfBirth_err'] = 'Date of birth cannot be in the future';
                }
            }

            if(empty($data['address'])) {
                $data['address_err'] = 'Please enter your address';
            }

            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter your email';
            } elseif($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email is already taken';
            }

            if(empty($data['contactNumber'])) {
                $data['contactNumber_err'] = 'Please enter your contact number';
            } else {
                // Validate phone number format (Sri Lankan format: 10 digits starting with 0)
                $phone = preg_replace('/[^0-9]/', '', $data['contactNumber']);
                if(strlen($phone) < 10) {
                    $data['contactNumber_err'] = 'Contact number must be at least 10 digits';
                } elseif(!preg_match('/^[0-9+\-\s()]+$/', $data['contactNumber'])) {
                    $data['contactNumber_err'] = 'Please enter a valid phone number';
                }
            }

            if(empty($data['school'])) {
                $data['school_err'] = 'Please enter your school/institution';
            }

            if(empty($data['username'])) {
                $data['username_err'] = 'Please choose a username';
            } elseif($this->userModel->findUserByUsername($data['username'])) {
                $data['username_err'] = 'Username is already taken';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter a password';
            } elseif(strlen($data['password']) < 8) {
                $data['password_err'] = 'Password must be at least 8 characters long';
            } else {
                // Enhanced password validation
                $passwordValidation = $this->validatePassword($data['password']);
                if($passwordValidation !== true) {
                    $data['password_err'] = $passwordValidation;
                }
            }

            if(empty($data['confirmPassword'])) {
                $data['confirmPassword_err'] = 'Please confirm your password';
            } elseif($data['password'] !== $data['confirmPassword']) {
                $data['confirmPassword_err'] = 'Passwords do not match';
            }

            // Check for errors
            if(empty($data['fullName_err']) && empty($data['dateOfBirth_err']) && 
               empty($data['address_err']) && empty($data['email_err']) && 
               empty($data['contactNumber_err']) && empty($data['school_err']) && 
               empty($data['username_err']) && empty($data['password_err']) && 
               empty($data['confirmPassword_err'])) {
                
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                
                // Register user
                if($userId = $this->userModel->register($data)) {
                    // Try to create player profile with additional information
                    try {
                        $this->userModel->createPlayerProfile($userId, [
                            'school' => $data['school']
                        ]);
                    } catch (Exception $e) {
                        // Log error but continue - profile can be created later
                        error_log("Player profile creation failed: " . $e->getMessage());
                    }
                    
                    // Try to log the registration activity
                    try {
                        $this->userModel->logActivity(
                            $userId, 
                            'account_created', 
                            'New player account registered',
                            $_SERVER['REMOTE_ADDR'] ?? null,
                            $_SERVER['HTTP_USER_AGENT'] ?? null
                        );
                    } catch (Exception $e) {
                        // Log error but continue - activity logging is not critical
                        error_log("Activity logging failed: " . $e->getMessage());
                    }
                    
                    // Always redirect after successful user registration
                    flash('register_success', 'Registration successful! Welcome to Elite Cricket Academy.');
                    redirect('login');
                } else {
                    die('Something went wrong during registration');
                }
            } else {
                            // Load view with errors
            $this->view('v_register', $data);
            }
        } else {
            // Init data
            $data = [
                'fullName' => '',
                'dateOfBirth' => '',
                'address' => '',
                'email' => '',
                'contactNumber' => '',
                'school' => '',
                'username' => '',
                'password' => '',
                'confirmPassword' => '',
                'fullName_err' => '',
                'dateOfBirth_err' => '',
                'address_err' => '',
                'email_err' => '',
                'contactNumber_err' => '',
                'school_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirmPassword_err' => ''
            ];

            // Load view
            $this->view('v_register', $data);
        }
    }

    /**
     * Enhanced password validation
     * Checks for minimum length, uppercase, lowercase, number, and special character
     */
    private function validatePassword($password) {
        if(strlen($password) < 8) {
            return 'Password must be at least 8 characters long';
        }
        
        if(!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter';
        }
        
        if(!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter';
        }
        
        if(!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one number';
        }
        
        if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            return 'Password must contain at least one special character (!@#$%^&*(),.?":{}|<>)';
        }
        
        return true; // Password is valid
    }
}
?> 