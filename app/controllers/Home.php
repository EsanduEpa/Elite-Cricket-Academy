<?php

class Home extends Controller {
    
    public function __construct() {
        // Constructor can be empty or used for initialization
    }

    public function index() {
        // Initialize data array
        $data = [
            'users' => [] // Empty array to prevent foreach error
        ];
        
        // Load the home view with data
        $this->view('v_home', $data);
    }
}
?>
