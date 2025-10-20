<?php

class Pages extends Controller {
    private $pagesModel;

    public function __construct() {
        $this->pagesModel = $this->model('M_Pages'); // Your model class
    }

    public function index() {
        // Load home page view
        $this->view('v_home');
    }

    public function logout() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Destroy all session data
        session_unset();
        session_destroy();
        
        // Redirect to home page
        header('Location: /Elite/');
        exit();
    }

    public function about() {
        // Fetch users from the database using the model
        $users = $this->pagesModel->getUsers();

        $data = [
            'users' => $users
        ];

        // Pass data to the view
        $this->view('v_about', $data);
    }
}
?>
