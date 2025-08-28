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
                // Check and log in user
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
        $_SESSION['user_name'] = $user->username;
        redirect('dashboard');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('login');
    }
}
?>