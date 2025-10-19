<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flash message helper
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            if(!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if(!empty($_SESSION[$name. '_class'])) {
                unset($_SESSION[$name. '_class']);
            }

            $_SESSION[$name] = $message;
            $_SESSION[$name. '_class'] = $class;
        }
        elseif(empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';
            echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name. '_class']);
        }
    }
}

function isLoggedIn() {
    if(isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

function redirect($page) {
    header('location: ' . URLROOT . '/' . $page);
    exit();
}

function hasRole($role) {
    if(isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'] === $role;
    }
    return false;
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'Guest';
}

// Check if user is logged in and has correct role
function requireAuth($allowedRoles = []) {
    // 🔧 DEVELOPMENT MODE: Bypass authentication
    if (defined('DEV_MODE') && DEV_MODE === true) {
        // Set up mock session for development if not already set
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            // Create mock session based on the required role
            $_SESSION['user_id'] = 999;
            $_SESSION['user_name'] = 'Dev User';
            $_SESSION['user_email'] = 'dev@test.com';
            
            // Set role based on what's being accessed
            if (!empty($allowedRoles)) {
                $_SESSION['user_role'] = $allowedRoles[0]; // Use first allowed role
            } else {
                $_SESSION['user_role'] = 'Admin'; // Default to Admin in dev mode
            }
        }
        return; // Skip authentication in dev mode
    }
    
    // 🔒 PRODUCTION MODE: Normal authentication
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        flash('login_required', 'Please login to access this page', 'alert alert-danger');
        redirect('login');
        exit();
    }
    
    // Check if role is allowed (if roles specified)
    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
        // Redirect to their own dashboard
        redirectToDashboard();
        exit();
    }
}

// Redirect user to their appropriate dashboard
function redirectToDashboard() {
    if (!isset($_SESSION['user_role'])) {
        redirect('login');
        return;
    }
    
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
?> 