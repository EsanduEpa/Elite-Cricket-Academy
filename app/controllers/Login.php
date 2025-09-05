<?php

class Login extends Controller {
    public function index() {
        $this->view('v_login');
    }
    
    public function logout() {
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
}
?>