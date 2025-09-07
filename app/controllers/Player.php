<?php
class Player extends Controller {
    
    private $userModel;
    
    public function __construct() {
        // Database disabled for UI testing
        // $this->userModel = $this->model('M_Users');
    }
    
    private function requireLogin() {
        // TEMPORARILY DISABLED FOR TESTING - REMOVE THIS COMMENT IN PRODUCTION
        return true;
        
        if (!function_exists('isLoggedIn')) {
            require_once APPROOT . '/helpers/session_helper.php';
        }
        if (!isLoggedIn()) {
            redirect('login');
            exit;
        }
    }
    
    // Main dashboard
    public function index() {
        $this->requireLogin();
        $playerData = $this->getPlayerData();
        
        $data = [
            'title' => 'Player Dashboard - ' . $playerData['name'],
            'player' => $playerData,
            'todaySchedule' => $this->getTodaySchedule(),
            'upcomingSchedule' => $this->getUpcomingSchedule(),
            'upcomingBookings' => $this->getUpcomingBookings(),
            'rentalsDue' => $this->getRentalsDue(),
            'paymentsDue' => $this->getPaymentsDue(),
            'performanceStats' => $this->getPerformanceStats()
        ];
        
        $this->view('player/dashboard', $data);
    }
    
    // Test method without login requirement
    public function test() {
        // Direct UI test without any auth or database
        $playerData = $this->getPlayerData();
        
        $data = [
            'title' => 'Player Dashboard - ' . $playerData['name'],
            'player' => $playerData,
            'todaySchedule' => $this->getTodaySchedule(),
            'upcomingSchedule' => $this->getUpcomingSchedule(),
            'upcomingBookings' => $this->getUpcomingBookings(),
            'rentalsDue' => $this->getRentalsDue(),
            'paymentsDue' => $this->getPaymentsDue(),
            'performanceStats' => $this->getPerformanceStats()
        ];
        
        $this->view('player/dashboard', $data);
    }
    
    // Debug method to test routing
    public function debug() {
        $this->view('player/debug');
    }
    
    // Training Schedule
    public function training() {
        $this->requireLogin();
        $data = [
            'title' => 'Training Schedule',
            'player' => $this->getPlayerData(),
            'trainingSessions' => $this->getTrainingSessions()
        ];
        $this->view('player/training', $data);
    }
    
    // Shopping and Rental Info
    public function shopping() {
        $this->requireLogin();
        $data = [
            'title' => 'Shopping & Rentals',
            'player' => $this->getPlayerData(),
            'products' => $this->getAvailableProducts(),
            'rentals' => $this->getRentalEquipment(),
            'myRentals' => $this->getMyRentals()
        ];
        $this->view('player/shopping', $data);
    }
    
    // My Bookings
    public function bookings() {
        $this->requireLogin();
        $data = [
            'title' => 'My Bookings',
            'player' => $this->getPlayerData(),
            'coachSessions' => $this->getCoachSessions(),
            'facilityReservations' => $this->getFacilityReservations(),
            'upcomingBookings' => $this->getUpcomingBookings()
        ];
        $this->view('player/bookings', $data);
    }
    
    // Medical Records
    public function medical() {
        $this->requireLogin();
        $data = [
            'title' => 'Medical Records',
            'player' => $this->getPlayerData(),
            'medicalHistory' => $this->getMedicalHistory(),
            'vaccinations' => $this->getVaccinations(),
            'injuries' => $this->getInjuries()
        ];
        $this->view('player/medical', $data);
    }
    
    // Performance History
    public function performance() {
        $this->requireLogin();
        $data = [
            'title' => 'Performance History',
            'player' => $this->getPlayerData(),
            'practiceMatches' => $this->getPracticeMatches(),
            'tournaments' => $this->getTournaments(),
            'performanceStats' => $this->getDetailedPerformanceStats()
        ];
        $this->view('player/performance', $data);
    }
    
    // Achievements
    public function achievements() {
        $this->requireLogin();
        $data = [
            'title' => 'Achievements',
            'player' => $this->getPlayerData(),
            'awards' => $this->getAwards(),
            'records' => $this->getRecords(),
            'certificates' => $this->getCertificates()
        ];
        $this->view('player/achievements', $data);
    }
    
    // Payment History
    public function payments() {
        $this->requireLogin();
        $data = [
            'title' => 'Payment History',
            'player' => $this->getPlayerData(),
            'monthlyFees' => $this->getMonthlyFees(),
            'eventFees' => $this->getEventFees(),
            'upcomingPayments' => $this->getUpcomingPayments()
        ];
        $this->view('player/payments', $data);
    }
    
    // Helper methods with demo data
    private function getPlayerData() {
        // In real implementation, get from database using session user ID
        return [
            'id' => 12345,
            'name' => 'Ethan Carter',
            'email' => 'ethan.carter@email.com',
            'phone' => '+1-555-123-4567',
            'roles' => 'Right-handed Batsman | Right-arm Fast Bowler',
            'profile_picture' => 'default-profile.jpg',
            'date_of_birth' => '1999-01-15',
            'address' => '123 Willow Creek Rd, Anytown, USA',
            'membership_level' => 'Premium',
            'joined_date' => '2023-06-15'
        ];
    }
    
    private function getPerformanceStats() {
        return [
            'batting_avg' => 45.80,
            'strike_rate' => 128.50,
            'total_runs' => 5250,
            'total_wickets' => 250,
            'bowling_avg' => 18.20,
            'economy_rate' => 4.85,
            'matches_played' => 148,
            'wins' => 95
        ];
    }
    
    private function getTodaySchedule() {
        // Get today's day of week (0 = Sunday, 1 = Monday, etc.)
        $today = date('w');
        $todayName = date('l');
        
        // Create dynamic schedule based on current day
        $schedules = [
            0 => [], // Sunday - Rest day
            1 => [  // Monday
                ['time' => '07:00 AM', 'activity' => 'Morning Practice', 'location' => 'Main Ground', 'coach' => 'Coach Wilson'],
                ['time' => '04:00 PM', 'activity' => 'Fitness Session', 'location' => 'Gym', 'coach' => 'Trainer Mike']
            ],
            2 => [  // Tuesday
                ['time' => '10:00 AM', 'activity' => 'Team Meeting', 'location' => 'Conference Room', 'coach' => 'Head Coach'],
                ['time' => '02:00 PM', 'activity' => 'Bowling Practice', 'location' => 'Net 2', 'coach' => 'Coach Sarah']
            ],
            3 => [  // Wednesday
                ['time' => '09:00 AM', 'activity' => 'Net Practice', 'location' => 'Net 1', 'coach' => 'Coach Wilson'],
                ['time' => '02:00 PM', 'activity' => 'Strategy Review', 'location' => 'Video Room', 'coach' => 'Analyst John']
            ],
            4 => [  // Thursday
                ['time' => '02:00 PM', 'activity' => 'Match vs Central CC', 'location' => 'Away Ground', 'coach' => 'Team Manager']
            ],
            5 => [  // Friday
                ['time' => '08:00 AM', 'activity' => 'Bowling Practice', 'location' => 'Net 3', 'coach' => 'Coach Sarah'],
                ['time' => '03:00 PM', 'activity' => 'Skills Assessment', 'location' => 'Main Ground', 'coach' => 'Head Coach']
            ],
            6 => [  // Saturday
                ['time' => '10:00 AM', 'activity' => 'Weekend Tournament', 'location' => 'Tournament Ground', 'coach' => 'Team Manager'],
                ['time' => '02:30 PM', 'activity' => 'Team Selection', 'location' => 'Clubhouse', 'coach' => 'Selection Committee']
            ]
        ];
        
        return $schedules[$today] ?? [];
    }
    
    private function getUpcomingSchedule() {
        // Generate upcoming schedule for next 7 days
        $schedule = [];
        $events = [
            'Match vs Thunder Hawks' => ['location' => 'Away Ground', 'time' => '10:00 AM'],
            'Team Practice' => ['location' => 'Practice Ground', 'time' => '02:00 PM'],
            'Fitness Assessment' => ['location' => 'Gym', 'time' => '09:00 AM'],
            'Inter-Club Match' => ['location' => 'Main Ground', 'time' => '01:00 PM'],
            'Batting Clinic' => ['location' => 'Net 1', 'time' => '09:00 AM'],
            'Physical Fitness Test' => ['location' => 'Gym', 'time' => '06:00 AM'],
            'Championship Quarter Final' => ['location' => 'Stadium', 'time' => '02:30 PM']
        ];
        
        $eventKeys = array_keys($events);
        for($i = 1; $i <= 7; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            $eventIndex = ($i - 1) % count($eventKeys);
            $eventName = $eventKeys[$eventIndex];
            $eventDetails = $events[$eventName];
            
            $schedule[] = [
                'date' => $date,
                'time' => $eventDetails['time'],
                'activity' => $eventName,
                'location' => $eventDetails['location']
            ];
        }
        
        return $schedule;
    }
    
    private function getUpcomingBookings() {
        return [
            ['date' => '2025-09-08', 'time' => '03:00 PM', 'type' => 'Private Coaching', 'coach' => 'Coach Anderson', 'duration' => '60 min'],
            ['date' => '2025-09-10', 'time' => '01:00 PM', 'type' => 'Net Practice', 'facility' => 'Batting Net 2', 'duration' => '90 min']
        ];
    }
    
    private function getRentalsDue() {
        return [
            ['item' => 'Cricket Bat (Premium)', 'due_date' => '2025-09-08', 'fee' => 'LKR 1000.00'],
            ['item' => 'Protective Gear Set', 'due_date' => '2025-09-15', 'fee' => 'LKR 2000.00']
        ];
    }
    
    private function getPaymentsDue() {
        return [
            ['type' => 'Monthly Membership', 'amount' => 'LKR 4500.00', 'due_date' => '2025-09-30'],
            ['type' => 'Tournament Fee', 'amount' => 'LKR 1000.00', 'due_date' => '2025-09-20']
        ];
    }
    
    private function getTrainingSessions() {
        return [
            ['date' => '2025-09-07', 'time' => '09:00 AM', 'type' => 'Batting', 'coach' => 'Coach Wilson', 'location' => 'Net 1'],
            ['date' => '2025-09-07', 'time' => '11:00 AM', 'type' => 'Fitness', 'coach' => 'Trainer Mike', 'location' => 'Gym'],
            ['date' => '2025-09-08', 'time' => '02:00 PM', 'type' => 'Bowling', 'coach' => 'Coach Sarah', 'location' => 'Net 3']
        ];
    }
    
    private function getAvailableProducts() {
        return [
            ['name' => 'Cricket Bat Pro', 'price' => 'LKR 15,000.00', 'category' => 'Equipment'],
            ['name' => 'Protective Gear Set', 'price' => 'LKR 10,000.00', 'category' => 'Safety'],
            ['name' => 'Training Jersey', 'price' => 'LKR 2,000.00', 'category' => 'Apparel']
        ];
    }
    
    private function getRentalEquipment() {
        return [
            ['name' => 'Premium Cricket Bat', 'daily_rate' => 'LKR 25,000.00', 'available' => true],
            ['name' => 'Bowling Machine Access', 'hourly_rate' => 'LKR 1,500.00', 'available' => true],
            ['name' => 'Video Analysis Equipment', 'session_rate' => 'LKR 1000.00', 'available' => false]
        ];
    }
    
    private function getMyRentals() {
        return [
            ['item' => 'Cricket Bat Premium', 'start_date' => '2025-09-01', 'return_date' => '2025-09-08', 'status' => 'Active'],
            ['item' => 'Protective Gear', 'start_date' => '2025-08-15', 'return_date' => '2025-09-15', 'status' => 'Active']
        ];
    }
    
    private function getCoachSessions() {
        return [
            ['date' => '2025-09-08', 'coach' => 'Coach Anderson', 'type' => 'Batting Technique', 'duration' => '60 min', 'status' => 'Confirmed'],
            ['date' => '2025-09-12', 'coach' => 'Coach Williams', 'type' => 'Bowling Analysis', 'duration' => '90 min', 'status' => 'Pending']
        ];
    }
    
    private function getFacilityReservations() {
        return [
            ['date' => '2025-09-10', 'facility' => 'Batting Net 2', 'time' => '01:00 PM', 'duration' => '90 min', 'status' => 'Confirmed'],
            ['date' => '2025-09-14', 'facility' => 'Bowling Machine', 'time' => '03:00 PM', 'duration' => '60 min', 'status' => 'Confirmed']
        ];
    }
    
    private function getMedicalHistory() {
        return [
            ['date' => '2025-08-15', 'type' => 'Physical Examination', 'doctor' => 'Dr. Smith', 'notes' => 'All clear, excellent fitness'],
            ['date' => '2025-07-20', 'type' => 'Injury Assessment', 'doctor' => 'Dr. Johnson', 'notes' => 'Minor strain, fully recovered']
        ];
    }
    
    private function getVaccinations() {
        return [
            ['vaccine' => 'Tetanus', 'date' => '2024-12-15', 'next_due' => '2029-12-15'],
            ['vaccine' => 'Hepatitis B', 'date' => '2024-06-10', 'next_due' => '2029-06-10']
        ];
    }
    
    private function getInjuries() {
        return [
            ['date' => '2025-07-15', 'type' => 'Muscle Strain', 'location' => 'Right Shoulder', 'status' => 'Fully Recovered'],
            ['date' => '2025-05-10', 'type' => 'Minor Sprain', 'location' => 'Left Ankle', 'status' => 'Fully Recovered']
        ];
    }
    
    private function getPracticeMatches() {
        return [
            ['date' => '2025-08-25', 'opponent' => 'Thunder Hawks', 'runs' => 85, 'wickets' => 2, 'result' => 'Win'],
            ['date' => '2025-08-18', 'opponent' => 'Lightning Bolts', 'runs' => 42, 'wickets' => 1, 'result' => 'Loss'],
            ['date' => '2025-08-12', 'opponent' => 'Storm Eagles', 'runs' => 67, 'wickets' => 3, 'result' => 'Win']
        ];
    }
    
    private function getTournaments() {
        return [
            ['name' => 'Summer Championship 2025', 'position' => '2nd Place', 'runs' => 340, 'wickets' => 12],
            ['name' => 'Elite Cup 2025', 'position' => '1st Place', 'runs' => 280, 'wickets' => 8],
            ['name' => 'Regional Trophy 2024', 'position' => '3rd Place', 'runs' => 195, 'wickets' => 6]
        ];
    }
    
    private function getDetailedPerformanceStats() {
        return [
            'batting' => [
                'total_runs' => 5250,
                'average' => 45.80,
                'strike_rate' => 128.50,
                'centuries' => 12,
                'half_centuries' => 28,
                'highest_score' => 156
            ],
            'bowling' => [
                'total_wickets' => 250,
                'average' => 18.20,
                'economy_rate' => 4.85,
                'best_figures' => '6/25',
                'five_wickets' => 15,
                'four_wickets' => 22
            ]
        ];
    }
    
    private function getAwards() {
        return [
            ['title' => 'Player of the Tournament', 'event' => 'Elite Cup 2025', 'date' => '2025-08-30'],
            ['title' => 'Best Bowler Award', 'event' => 'Summer Championship 2025', 'date' => '2025-07-20'],
            ['title' => 'Most Improved Player', 'event' => 'Academy Awards 2024', 'date' => '2024-12-15']
        ];
    }
    
    private function getRecords() {
        return [
            ['record' => 'Highest Individual Score', 'value' => '156 runs', 'match' => 'vs Thunder Hawks', 'date' => '2025-06-15'],
            ['record' => 'Best Bowling Figures', 'value' => '6/25', 'match' => 'vs Storm Eagles', 'date' => '2025-05-20'],
            ['record' => 'Most Runs in Tournament', 'value' => '340 runs', 'tournament' => 'Summer Championship 2025', 'date' => '2025-07-30']
        ];
    }
    
    private function getCertificates() {
        return [
            ['title' => 'Level 3 Coaching Certificate', 'issued_by' => 'Cricket Board', 'date' => '2025-03-15'],
            ['title' => 'Sports First Aid Certificate', 'issued_by' => 'Health Council', 'date' => '2024-11-20'],
            ['title' => 'Fitness Training Certificate', 'issued_by' => 'Fitness Academy', 'date' => '2024-09-10']
        ];
    }
    
    private function getMonthlyFees() {
        return [
            ['month' => 'August 2025', 'amount' => 'LKR 4,500.00', 'paid_date' => '2025-08-01', 'status' => 'Paid'],
            ['month' => 'July 2025', 'amount' => 'LKR 4,500.00', 'paid_date' => '2025-07-01', 'status' => 'Paid'],
            ['month' => 'June 2025', 'amount' => 'LKR 4,500.00', 'paid_date' => '2025-06-01', 'status' => 'Paid']
        ];
    }
    
    private function getEventFees() {
        return [
            ['event' => 'Elite Cup 2025', 'amount' => 'LKR 1,000.00', 'paid_date' => '2025-08-15', 'status' => 'Paid'],
            ['event' => 'Summer Championship 2025', 'amount' => 'LKR 1,000.00', 'paid_date' => '2025-07-10', 'status' => 'Paid'],
            ['event' => 'Autumn Tournament 2025', 'amount' => 'LKR 1,000.00', 'due_date' => '2025-09-20', 'status' => 'Due']
        ];
    }
    
    private function getUpcomingPayments() {
        return [
            ['type' => 'Monthly Membership', 'amount' => 'LKR 4,500.00', 'due_date' => '2025-09-30'],
            ['type' => 'Autumn Tournament Fee', 'amount' => 'LKR 1,000.00', 'due_date' => '2025-09-20'],
            ['type' => 'Equipment Rental', 'amount' => 'LKR 3,000.00', 'due_date' => '2025-09-15']
        ];
    }
}
?>
