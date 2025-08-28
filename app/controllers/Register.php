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
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

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
            }

            if(empty($data['school'])) {
                $data['school_err'] = 'Please enter your school/institution';
            }

            if(empty($data['username'])) {
                $data['username_err'] = 'Please choose a username';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter a password';
            } elseif(strlen($data['password']) < 8) {
                $data['password_err'] = 'Password must be at least 8 characters long';
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
                if($this->userModel->register($data)) {
                    flash('register_success', 'Registration successful! Welcome to Elite Cricket Academy.');
                    redirect('login');
                } else {
                    die('Something went wrong');
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
}
?> 