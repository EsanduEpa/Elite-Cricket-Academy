<?php
class Player extends Controller {
    
    private $userModel;
    
    public function __construct() {
        // Check authentication for all player pages
        requireAuth(['Player']);
        // Database disabled for UI testing
        // $this->userModel = $this->model('M_Users');
    }
    
    // Main dashboard
    public function index() {
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
        $data = [
            'title' => 'Training Schedule',
            'player' => $this->getPlayerData(),
            'trainingSessions' => $this->getTrainingSessions()
        ];
        $this->view('player/training', $data);
    }
    
    // Shopping and Rental Info
    public function shopping() {
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
        $data = [
            'title' => 'Payment History',
            'player' => $this->getPlayerData(),
            'monthlyFees' => $this->getMonthlyFees(),
            'eventFees' => $this->getEventFees(),
            'upcomingPayments' => $this->getUpcomingPayments()
        ];
        $this->view('player/payments', $data);
    }
    
    // Tournaments
    public function tournaments() {
        $data = [
            'title' => 'Tournaments',
            'player' => $this->getPlayerData(),
            'upcomingTournaments' => $this->getUpcomingTournaments(),
            'enrolledTournaments' => $this->getEnrolledTournaments(),
            'completedTournaments' => $this->getCompletedTournaments(),
            'tournamentStats' => $this->getTournamentStats()
        ];
        $this->view('player/tournaments', $data);
    }

    // Equipment Rentals
    public function rentals() {
        $data = [
            'title' => 'Equipment Rentals',
            'player' => $this->getPlayerData(),
            'rentals' => $this->getRentalEquipment(),
            'myRentals' => $this->getMyRentals(),
            'rentalStats' => $this->getRentalStats()
        ];
        $this->view('player/rentals', $data);
    }

    // Facilities
    public function facilities() {
        $data = [
            'title' => 'Facility Booking',
            'player' => $this->getPlayerData(),
            'facilities' => $this->getAvailableFacilities(),
            'myBookings' => $this->getMyFacilityBookings(),
            'facilityStats' => $this->getFacilityStats()
        ];
        $this->view('player/facilities', $data);
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
    
    // Tournament helper methods
    private function getUpcomingTournaments() {
        return [
            [
                'id' => 1,
                'name' => 'Elite Premier League 2025',
                'type' => 'Championship',
                'start_date' => '2025-10-15',
                'end_date' => '2025-11-15',
                'location' => 'Elite Cricket Academy',
                'prize_pool' => 'LKR 500,000',
                'teams' => 16,
                'entry_fee' => 'LKR 2,500',
                'registration_deadline' => '2025-10-01',
                'status' => 'open'
            ],
            [
                'id' => 2,
                'name' => 'Autumn Cup 2025',
                'type' => 'Tournament',
                'start_date' => '2025-09-20',
                'end_date' => '2025-09-25',
                'location' => 'Central Ground',
                'prize_pool' => 'LKR 150,000',
                'teams' => 8,
                'entry_fee' => 'LKR 1,500',
                'registration_deadline' => '2025-09-15',
                'status' => 'open'
            ]
        ];
    }
    
    private function getEnrolledTournaments() {
        return [
            [
                'id' => 3,
                'name' => 'Youth Championship 2025',
                'type' => 'Championship',
                'start_date' => '2025-09-30',
                'end_date' => '2025-10-10',
                'location' => 'Youth Cricket Complex',
                'team' => 'Elite Warriors',
                'status' => 'enrolled',
                'matches_played' => 2,
                'matches_won' => 1,
                'next_match' => '2025-09-18 at 2:00 PM'
            ]
        ];
    }
    
    private function getCompletedTournaments() {
        return [
            [
                'id' => 4,
                'name' => 'Summer Championship 2025',
                'type' => 'Championship',
                'completed_date' => '2025-08-15',
                'location' => 'Elite Cricket Academy',
                'team' => 'Elite Warriors',
                'position' => '2nd Place',
                'matches_played' => 8,
                'matches_won' => 6,
                'prize_amount' => 'LKR 75,000'
            ],
            [
                'id' => 5,
                'name' => 'Spring Tournament 2025',
                'type' => 'Tournament',
                'completed_date' => '2025-05-20',
                'location' => 'Central Ground',
                'team' => 'Elite Warriors',
                'position' => '1st Place',
                'matches_played' => 6,
                'matches_won' => 6,
                'prize_amount' => 'LKR 100,000'
            ]
        ];
    }
    
    private function getTournamentStats() {
        return [
            'total_tournaments' => 8,
            'tournaments_won' => 3,
            'tournaments_runner_up' => 2,
            'total_matches' => 45,
            'matches_won' => 32,
            'win_percentage' => 71.1,
            'total_prize_money' => 'LKR 275,000'
        ];
    }

    // Shopping System Methods
    private function getAvailableProducts() {
        return [
            [
                'id' => 1,
                'name' => 'Professional Cricket Bat',
                'description' => 'Premium English Willow cricket bat with professional finish',
                'price' => 18500.00,
                'category' => 'Bats',
                'image' => 'cricket-bat-pro.jpg',
                'rating' => 4.8,
                'reviews' => 25,
                'stock' => 15,
                'brand' => 'Elite Sports',
                'features' => ['English Willow', 'Professional Grade', 'Free Grip Tape'],
                'discount' => 15
            ],
            [
                'id' => 2,
                'name' => 'Protective Gear Set',
                'description' => 'Complete protection set including helmet, pads, and gloves',
                'price' => 12500.00,
                'category' => 'Protection',
                'image' => 'protective-gear-set.jpg',
                'rating' => 4.6,
                'reviews' => 18,
                'stock' => 8,
                'brand' => 'SafeGuard',
                'features' => ['Complete Set', 'Professional Grade', 'Lightweight'],
                'discount' => 10
            ],
            [
                'id' => 3,
                'name' => 'Cricket Ball Set (6 pieces)',
                'description' => 'Professional leather cricket balls for training and matches',
                'price' => 2500.00,
                'category' => 'Balls',
                'image' => 'cricket-balls.jpg',
                'rating' => 4.7,
                'reviews' => 32,
                'stock' => 25,
                'brand' => 'Elite Sports',
                'features' => ['Leather Construction', 'Match Quality', '6 Piece Set'],
                'discount' => 0
            ],
            [
                'id' => 4,
                'name' => 'Elite Training Kit',
                'description' => 'Complete training kit with jersey, shorts, and accessories',
                'price' => 4500.00,
                'category' => 'Apparel',
                'image' => 'training-kit.jpg',
                'rating' => 4.5,
                'reviews' => 15,
                'stock' => 20,
                'brand' => 'Elite Academy',
                'features' => ['Moisture Wicking', 'Breathable Fabric', 'Academy Logo'],
                'discount' => 5
            ]
        ];
    }

    private function getRentalEquipment() {
        return [
            [
                'id' => 1,
                'name' => 'Professional Cricket Bat',
                'description' => 'Premium bat available for short-term rental',
                'rental_price_daily' => 500.00,
                'rental_price_weekly' => 2500.00,
                'category' => 'Bats',
                'image' => 'rental-bat.jpg',
                'available_quantity' => 5,
                'condition' => 'Excellent'
            ],
            [
                'id' => 2,
                'name' => 'Bowling Machine',
                'description' => 'Automatic bowling machine for practice sessions',
                'rental_price_daily' => 1500.00,
                'rental_price_weekly' => 8000.00,
                'category' => 'Training Equipment',
                'image' => 'bowling-machine.jpg',
                'available_quantity' => 2,
                'condition' => 'Good'
            ],
            [
                'id' => 3,
                'name' => 'Practice Net Setup',
                'description' => 'Portable cricket practice net for individual training',
                'rental_price_daily' => 800.00,
                'rental_price_weekly' => 4000.00,
                'category' => 'Training Equipment',
                'image' => 'practice-net.jpg',
                'available_quantity' => 3,
                'condition' => 'Good'
            ]
        ];
    }

    private function getAvailableFacilities() {
        return [
            [
                'id' => 1,
                'name' => 'Main Cricket Ground',
                'description' => 'Full-size cricket ground with pavilion facilities',
                'hourly_rate' => 2500.00,
                'capacity' => 22,
                'amenities' => ['Pavilion', 'Changing Rooms', 'Scoreboard', 'Lighting'],
                'image' => 'main-ground.jpg',
                'booking_slots' => [
                    '06:00-08:00', '08:00-10:00', '10:00-12:00', 
                    '14:00-16:00', '16:00-18:00', '18:00-20:00'
                ]
            ],
            [
                'id' => 2,
                'name' => 'Practice Ground A',
                'description' => 'Medium-size practice ground perfect for training sessions',
                'hourly_rate' => 1500.00,
                'capacity' => 15,
                'amenities' => ['Practice Nets', 'Equipment Storage', 'Water Facility'],
                'image' => 'practice-ground-a.jpg',
                'booking_slots' => [
                    '06:00-08:00', '08:00-10:00', '10:00-12:00', 
                    '14:00-16:00', '16:00-18:00', '18:00-20:00'
                ]
            ],
            [
                'id' => 3,
                'name' => 'Indoor Training Hall',
                'description' => 'Climate-controlled indoor facility for year-round training',
                'hourly_rate' => 2000.00,
                'capacity' => 12,
                'amenities' => ['Air Conditioning', 'Artificial Turf', 'Video Analysis'],
                'image' => 'indoor-hall.jpg',
                'booking_slots' => [
                    '06:00-08:00', '08:00-10:00', '10:00-12:00', 
                    '14:00-16:00', '16:00-18:00', '18:00-20:00'
                ]
            ]
        ];
    }

    private function getMyRentals() {
        return [
            [
                'id' => 1,
                'equipment_name' => 'Professional Cricket Bat',
                'rental_start' => '2025-10-15',
                'rental_end' => '2025-10-22',
                'daily_rate' => 500.00,
                'total_cost' => 3500.00,
                'status' => 'Active',
                'condition_on_rental' => 'Excellent'
            ],
            [
                'id' => 2,
                'equipment_name' => 'Practice Net Setup',
                'rental_start' => '2025-10-10',
                'rental_end' => '2025-10-17',
                'daily_rate' => 800.00,
                'total_cost' => 5600.00,
                'status' => 'Returned',
                'condition_on_return' => 'Good'
            ]
        ];
    }

    // New Shopping Related Methods
    public function productDetails($productId) {
        $products = $this->getAvailableProducts();
        $product = null;
        
        foreach ($products as $p) {
            if ($p['id'] == $productId) {
                $product = $p;
                break;
            }
        }
        
        if (!$product) {
            redirect('player/shopping');
        }
        
        $data = [
            'title' => $product['name'],
            'player' => $this->getPlayerData(),
            'product' => $product,
            'relatedProducts' => array_slice($products, 0, 3)
        ];
        
        $this->view('player/product-details', $data);
    }

    public function facilityBooking() {
        $data = [
            'title' => 'Facility Booking',
            'player' => $this->getPlayerData(),
            'facilities' => $this->getAvailableFacilities()
        ];
        
        $this->view('player/facility-booking', $data);
    }

    public function cart() {
        $data = [
            'title' => 'Shopping Cart',
            'player' => $this->getPlayerData(),
            'cartItems' => $this->getCartItems()
        ];
        
        $this->view('player/cart', $data);
    }

    public function checkout() {
        $data = [
            'title' => 'Checkout',
            'player' => $this->getPlayerData(),
            'cartItems' => $this->getCartItems(),
            'cartTotal' => $this->getCartTotal()
        ];
        
        $this->view('player/checkout', $data);
    }

    private function getCartItems() {
        // Mock cart data - in real implementation, this would come from session/database
        return [
            [
                'id' => 1,
                'name' => 'Professional Cricket Bat',
                'price' => 18500.00,
                'quantity' => 1,
                'image' => 'cricket-bat-pro.jpg',
                'discount' => 15
            ],
            [
                'id' => 2,
                'name' => 'Protective Gear Set',
                'price' => 12500.00,
                'quantity' => 1,
                'image' => 'protective-gear-set.jpg',
                'discount' => 10
            ]
        ];
    }

    private function getCartTotal() {
        $items = $this->getCartItems();
        $subtotal = 0;
        
        foreach ($items as $item) {
            $discountedPrice = $item['price'] * (1 - $item['discount'] / 100);
            $subtotal += $discountedPrice * $item['quantity'];
        }
        
        $memberDiscount = $subtotal * 0.05; // 5% member discount
        $total = $subtotal - $memberDiscount;
        
        return [
            'subtotal' => $subtotal,
            'member_discount' => $memberDiscount,
            'total' => $total
        ];
    }

    private function getRentalStats() {
        return [
            'total_equipment' => 25,
            'active_rentals' => 3,
            'total_spent' => 450,
            'member_discount' => '15%'
        ];
    }

    private function getMyFacilityBookings() {
        return [
            [
                'id' => 1,
                'facility' => 'Indoor Practice Nets',
                'date' => '2024-01-25',
                'time' => '10:00 AM',
                'duration' => '2 hours',
                'status' => 'confirmed',
                'total' => 70
            ],
            [
                'id' => 2,
                'facility' => 'Main Cricket Ground',
                'date' => '2024-01-28',
                'time' => '2:00 PM',
                'duration' => '4 hours',
                'status' => 'pending',
                'total' => 200
            ]
        ];
    }

    private function getFacilityStats() {
        return [
            'total_facilities' => 8,
            'hours_available' => 16,
            'max_capacity' => 50,
            'rating' => '5★'
        ];
    }
}
?>

