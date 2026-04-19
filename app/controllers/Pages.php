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
        destroyUserSession();
        redirect('');
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
