<?php
class Profile extends Controller {
    
    public function __construct() {
        // COMMENTED OUT MODEL INITIALIZATION FOR UI TESTING
        // Initialize any required models
        // $this->userModel = $this->model('M_Users');
    }
    
    private function requireLogin() {
        if (!function_exists('isLoggedIn')) {
            require_once APPROOT . '/helpers/session_helper.php';
        }
        if (!isLoggedIn()) {
            redirect('login');
            exit;
        }
    }
    
    public function index($playerId = null) {
        $this->requireLogin();
        if (!$playerId) {
            $playerId = 12345;
        }
        $playerData = $this->getDemoPlayerData($playerId);
        $data = [
            'title' => 'Player Profile - ' . $playerData['name'],
            'player' => $playerData
        ];
        $this->view('v_profile', $data);
    }

    public function rentals() { $this->requireLogin(); $this->renderSubPage('v_profile_rentals', 'Past Rentals'); }
    public function orders() { $this->requireLogin(); $this->renderSubPage('v_profile_orders', 'Product Orders'); }
    public function medical() { $this->requireLogin(); $this->renderSubPage('v_profile_medical', 'Medical History'); }
    public function achievements() { $this->requireLogin(); $this->renderSubPage('v_profile_achievements', 'Achievements'); }
    public function notes() { $this->requireLogin(); $this->renderSubPage('v_profile_notes', "Trainer & Coach Notes"); }
    public function reservations() { $this->requireLogin(); $this->renderSubPage('v_profile_reservations', 'Facility Reservations'); }

    private function renderSubPage($viewName, $title) {
        $playerData = $this->getDemoPlayerData(12345);
        $data = [ 'title' => 'Profile - ' . $title, 'player' => $playerData, 'pageTitle' => $title ];
        $this->view($viewName, $data);
    }
    
    private function getDemoPlayerData($playerId) {
        return [
            'id' => $playerId,
            'name' => 'Ethan Carter',
            'roles' => 'Right-handed Batsman | Right-arm Fast Bowler',
            'profile_picture' => 'default-profile.jpg',
            'date_of_birth' => 'January 15, 1999',
            'contact' => '+1-555-123-4567',
            'address' => '123 Willow Creek Rd, Anytown, USA',
            'statistics' => [ 'runs' => 5250, 'wickets' => 250, 'average' => 45.00 ],
            'recent_performances' => [],
            'upcoming_matches' => []
        ];
    }
}
?>
