<?php

class Profile extends Controller {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
    }

    // Main profile page - redirects to role-specific profile
    public function index() {
        $role = $_SESSION['user_role'] ?? 'Player';
        
        switch($role) {
            case 'Player':
                redirect('player/profile');
                break;
            case 'Coach':
                redirect('coach/profile');
                break;
            case 'Trainer':
                redirect('trainer/profile');
                break;
            case 'Admin':
                redirect('admin/profile');
                break;
            case 'ShopEmployee':
                redirect('shop/profile');
                break;
            default:
                redirect('login');
        }
    }

    // Generic profile view for any role
    public function view() {
        $userModel = $this->model('M_Users');
        $userId = $_SESSION['user_id'];
        $userProfile = $userModel->getUserWithProfile($userId);
        
        $data = [
            'title' => 'My Profile',
            'user' => $userProfile
        ];
        
        $this->view('common/profile', $data);
    }

    // Generic profile update
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'];
            
            // Update basic user info
            $userData = [
                'user_id' => $userId,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number']),
                'address' => trim($_POST['address']),
                'school' => trim($_POST['school']),
                'role' => $_SESSION['user_role'], // Keep current role
                'status' => 'active' // Keep active
            ];
            
            // Validate data
            $errors = [];
            if (empty($userData['name'])) {
                $errors[] = 'Name is required';
            }
            if (empty($userData['email'])) {
                $errors[] = 'Email is required';
            }
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            
            if (empty($errors)) {
                if ($userModel->updateUser($userData)) {
                    // Update session data
                    $_SESSION['user_name'] = $userData['name'];
                    $_SESSION['user_email'] = $userData['email'];
                    
                    flash('profile_message', 'Profile updated successfully');
                } else {
                    flash('profile_message', 'Failed to update profile', 'alert alert-danger');
                }
            } else {
                flash('profile_message', implode('<br>', $errors), 'alert alert-danger');
            }
        }
        
        // Redirect back to role-specific profile
        $role = $_SESSION['user_role'] ?? 'Player';
        redirect(strtolower($role) . '/profile');
    }

    // Generic account deactivation
    public function deactivate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'];
            
            if ($userModel->suspendUser($userId, 9999)) { // Long suspension = deactivation
                // Clear session and redirect to login
                session_destroy();
                flash('login_message', 'Your account has been deactivated successfully');
                redirect('login');
            } else {
                flash('profile_message', 'Failed to deactivate account', 'alert alert-danger');
                // Redirect back to role-specific profile
                $role = $_SESSION['user_role'] ?? 'Player';
                redirect(strtolower($role) . '/profile');
            }
        } else {
            // Redirect back to role-specific profile
            $role = $_SESSION['user_role'] ?? 'Player';
            redirect(strtolower($role) . '/profile');
        }
    }
}
?>