<?php
class Player extends Controller {
    
    private $userModel;
    private $medicalModel;
    
    public function __construct() {
        // Check authentication for all player pages
        requireAuth(['Player']);
        // Database disabled for UI testing
        // $this->userModel = $this->model('M_Users');
        // $this->medicalModel = $this->model('M_Medical');
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

    // Coach Booking System
    public function coachbooking() {
        $this->requireLogin();
        
        // Handle AJAX requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCoachBookingAjax();
            return;
        }
        
        $data = [
            'title' => 'Book Coach Session',
            'player' => $this->getPlayerData(),
            'availableSlots' => $this->getAvailableCoachSlots(),
            'coaches' => $this->getAllCoaches(),
            'sessionTypes' => $this->getSessionTypes()
        ];
        $this->view('player/coach-booking', $data);
    }

    // Handle AJAX requests for coach booking
    private function handleCoachBookingAjax() {
        header('Content-Type: application/json');
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'get_available_slots':
                    $filters = [
                        'coach_id' => $_POST['coach_id'] ?? null,
                        'date' => $_POST['date'] ?? null,
                        'session_type' => $_POST['session_type'] ?? null,
                        'time_range' => $_POST['time_range'] ?? null
                    ];
                    echo json_encode($this->getFilteredAvailableSlots($filters));
                    break;
                    
                case 'book_session':
                    $result = $this->bookCoachSession(
                        $_POST['slot_id'],
                        $_SESSION['user_id'],
                        $_POST['special_requests'] ?? ''
                    );
                    echo json_encode($result);
                    break;
                    
                case 'process_payment':
                    $result = $this->processBookingPayment(
                        $_POST['booking_id'],
                        $_POST['payment_method'],
                        $_POST['payment_details'] ?? []
                    );
                    echo json_encode($result);
                    break;
                    
                case 'cancel_booking':
                    $result = $this->cancelBooking($_POST['booking_id']);
                    echo json_encode($result);
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No action specified']);
        }
        exit;
    }
    
    // Medical Records
    public function medical() {
        $this->requireLogin();
        
        // Initialize medical model
        if (!isset($this->medicalModel)) {
            $this->medicalModel = $this->model('M_Medical');
        }
        
        // Get current logged-in player data
        $playerData = $this->getPlayerData();
        $playerId = $playerData['id'];
        
        // Get medical records from database
        $medicalRecords = $this->medicalModel->getMedicalRecords($playerId);
        
        $data = [
            'title' => 'Medical Records',
            'player' => $playerData,
            'medicalRecords' => $medicalRecords,
            'medicalHistory' => $this->getMedicalHistory(),
            'vaccinations' => $this->getVaccinations(),
            'injuries' => $this->getInjuries()
        ];
        $this->view('player/medical', $data);
    }
    
    // Add medical record
    public function addMedicalRecord() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize medical model
            if (!isset($this->medicalModel)) {
                $this->medicalModel = $this->model('M_Medical');
            }
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get current logged-in player data
            $playerData = $this->getPlayerData();
            $playerId = $playerData['id'];
            $reportedBy = $playerData['id']; // Player reporting their own record
            
            $data = [
                'player_id' => $playerId,
                'injury_details' => trim($_POST['injury_details']),
                'diagnosis' => trim($_POST['diagnosis']),
                'treatment_given' => trim($_POST['treatment_given']),
                'recovery_status' => $_POST['recovery_status'],
                'reported_date' => $_POST['reported_date'],
                'reported_by' => $reportedBy
            ];
            
            // Validate data
            $errors = [];
            if (empty($data['injury_details'])) {
                $errors[] = 'Injury details are required';
            }
            if (empty($data['diagnosis'])) {
                $errors[] = 'Diagnosis is required';
            }
            if (empty($data['reported_date'])) {
                $errors[] = 'Date is required';
            }
            
            if (empty($errors)) {
                // Add record to database
                $recordId = $this->medicalModel->addMedicalRecord($data);
                
                if ($recordId) {
                    flash('medical_message', 'Medical record added successfully');
                    redirect('player/medical');
                } else {
                    flash('medical_message', 'Something went wrong', 'alert alert-danger');
                    redirect('player/medical');
                }
            } else {
                flash('medical_message', implode('<br>', $errors), 'alert alert-danger');
                redirect('player/medical');
            }
        } else {
            redirect('player/medical');
        }
    }

    // Update medical record recovery status
    public function updateMedicalRecord() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize medical model
            if (!isset($this->medicalModel)) {
                $this->medicalModel = $this->model('M_Medical');
            }
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get current logged-in player data
            $playerData = $this->getPlayerData();
            $playerId = $playerData['id'];
            
            $recordId = $_POST['record_id'];
            $recoveryStatus = $_POST['recovery_status'];
            
            // First, verify this record belongs to the current player
            $existingRecord = $this->medicalModel->getMedicalRecord($recordId);
            
            if (!$existingRecord || $existingRecord->PlayerID != $playerId) {
                flash('medical_message', 'Record not found or access denied', 'alert alert-danger');
                redirect('player/medical');
                return;
            }
            
            // Prepare update data
            $updateData = [
                'injury_details' => $existingRecord->InjuryDetails,
                'diagnosis' => $existingRecord->Diagnosis,
                'treatment_given' => $existingRecord->TreatmentGiven,
                'recovery_status' => $recoveryStatus,
                'reported_date' => $existingRecord->ReportedDate
            ];
            
            // Validate recovery status
            $validStatuses = ['ongoing', 'recovering', 'recovered', 'chronic'];
            if (empty($recoveryStatus) || !in_array($recoveryStatus, $validStatuses)) {
                flash('medical_message', 'Invalid recovery status selected', 'alert alert-danger');
                redirect('player/medical');
                return;
            }
            
            // Update record in database
            $success = $this->medicalModel->updateMedicalRecord($recordId, $updateData);
            
            if ($success) {
                flash('medical_message', 'Recovery status updated successfully');
                redirect('player/medical');
            } else {
                flash('medical_message', 'Failed to update recovery status', 'alert alert-danger');
                redirect('player/medical');
            }
        } else {
            redirect('player/medical');
        }
    }

    // Delete medical record (only if verify status is rejected)
    public function deleteMedicalRecord() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Validate input
        if (!isset($_POST['record_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing record ID']);
            return;
        }

        $recordId = intval($_POST['record_id']);
        
        try {
            // Initialize medical model
            if (!isset($this->medicalModel)) {
                $this->medicalModel = $this->model('M_Medical');
            }
            
            // Get current logged-in player data
            $playerData = $this->getPlayerData();
            $playerId = $playerData['id'];
            
            // First, verify this record belongs to the current player
            $existingRecord = $this->medicalModel->getMedicalRecord($recordId);
            
            if (!$existingRecord) {
                echo json_encode(['success' => false, 'message' => 'Medical record not found']);
                return;
            }
            
            if ($existingRecord->PlayerID != $playerId) {
                echo json_encode(['success' => false, 'message' => 'Access denied - record does not belong to current player']);
                return;
            }
            
            // Check if verify status is 'rejected' - only then allow deletion
            $verifyStatus = strtolower($existingRecord->verifyStatus ?? 'pending');
            if ($verifyStatus !== 'rejected') {
                echo json_encode(['success' => false, 'message' => 'Medical record can only be deleted if verification status is "rejected"']);
                return;
            }
            
            // Delete the record
            $success = $this->medicalModel->deleteMedicalRecord($recordId);
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Medical record deleted successfully',
                    'record_id' => $recordId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete medical record']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
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

    // Trainer Plans (Note: URL uses hyphen but method uses camelCase due to Core.php routing)
    public function trainerplans() {
        $this->requireLogin();
        $data = [
            'title' => 'Trainer Plans',
            'player' => $this->getPlayerData(),
            'workoutPlans' => $this->getGeneralWorkoutPlans(),
            'nutritionGuides' => $this->getGeneralNutritionGuides(),
            'supplementInfo' => $this->getGeneralSupplementInfo()
        ];
        $this->view('player/trainer-plans', $data);
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

    // Coach Sessions Page
    public function coach_sessions() {
        $this->requireLogin();
        $data = [
            'title' => 'Coach Sessions',
            'player' => $this->getPlayerData(),
            'coaches' => $this->getAllCoaches(),
            'availableSlots' => $this->getAvailableCoachSlots(),
            'sessionTypes' => $this->getSessionTypes()
        ];
        $this->view('player/coach_sessions', $data);
    }

    // Trainer Sessions Page
    public function trainer_sessions() {
        $this->requireLogin();
        $data = [
            'title' => 'Trainer Sessions',
            'player' => $this->getPlayerData(),
            'trainers' => $this->getAllTrainers(),
            'availableTrainerSlots' => $this->getAvailableTrainerSlots(),
            'sessionTypes' => $this->getTrainerSessionTypes()
        ];
        $this->view('player/trainer_sessions', $data);
    }
    
    // Helper methods with demo data
    private function getPlayerData() {
        // Get actual user data from session and database
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get user data from database
        $userModel = $this->model('M_Users');
        $user = $userModel->getUserById($userId);
        
        if ($user) {
            return [
                'id' => $user->UserID,
                'name' => $user->Name,
                'email' => $user->Email,
                'phone' => $user->PhoneNumber ?? '+1-555-123-4567',
                'roles' => 'Right-handed Batsman | Right-arm Fast Bowler', // Could be enhanced with profile data
                'profile_picture' => 'default-profile.jpg', // Could be enhanced with profile data
                'date_of_birth' => $user->DateOfBirth ?? '1999-01-15',
                'address' => $user->Address ?? '123 Willow Creek Rd, Anytown, USA',
                'membership_level' => 'Standard', // Could be enhanced with profile data
                'joined_date' => $user->DateJoined ?? '2023-06-15'
            ];
        } else {
            // Fallback if user not found
            return [
                'id' => $userId,
                'name' => 'Player',
                'email' => 'player@email.com',
                'phone' => '+1-555-123-4567',
                'roles' => 'Cricket Player',
                'profile_picture' => 'default-profile.jpg',
                'date_of_birth' => '1999-01-15',
                'address' => 'Cricket Academy',
                'membership_level' => 'Standard',
                'joined_date' => date('Y-m-d')
            ];
        }
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

    // Trainer Plans Data Methods
    private function getGeneralWorkoutPlans() {
        return [
            [
                'id' => 1,
                'title' => 'Beginner Cricket Fitness',
                'description' => 'Foundation fitness program for new cricket players',
                'level' => 'Beginner',
                'duration' => 4,
                'frequency' => '3x per week',
                'category' => 'Cricket-Specific',
                'trainer_name' => 'Coach Johnson',
                'trainer_specialization' => 'Physical Training',
                'created_date' => '2025-10-01',
                'view_count' => 45,
                'details' => [
                    'Week 1-2: Basic cardio (20 min) + bodyweight exercises',
                    'Week 3-4: Progressive strength training + cricket movements'
                ]
            ],
            [
                'id' => 2,
                'title' => 'Intermediate Strength Training',
                'description' => 'Progressive strength building for developing players',
                'level' => 'Intermediate',
                'duration' => 6,
                'frequency' => '4x per week',
                'category' => 'Strength',
                'trainer_name' => 'Coach Anderson',
                'trainer_specialization' => 'Strength & Conditioning',
                'created_date' => '2025-09-28',
                'view_count' => 62,
                'details' => [
                    'Focus on compound movements',
                    'Progressive overload principles',
                    'Cricket-specific strength patterns'
                ]
            ],
            [
                'id' => 3,
                'title' => 'Advanced Cricket Performance',
                'description' => 'Elite training for competitive cricket players',
                'level' => 'Advanced',
                'duration' => 8,
                'frequency' => '5x per week',
                'category' => 'Cricket-Specific',
                'trainer_name' => 'Coach Williams',
                'trainer_specialization' => 'Performance Coach',
                'created_date' => '2025-09-25',
                'view_count' => 38,
                'details' => [
                    'High-intensity training',
                    'Power and agility development',
                    'Match-specific conditioning'
                ]
            ],
            [
                'id' => 4,
                'title' => 'Cardio Enhancement Program',
                'description' => 'Improve cardiovascular endurance for cricket',
                'level' => 'Intermediate',
                'duration' => 5,
                'frequency' => '4x per week',
                'category' => 'Cardio',
                'trainer_name' => 'Trainer Mike',
                'trainer_specialization' => 'Cardio Specialist',
                'created_date' => '2025-09-20',
                'view_count' => 29,
                'details' => [
                    'HIIT training protocols',
                    'Cricket-specific endurance',
                    'Recovery optimization'
                ]
            ],
            [
                'id' => 5,
                'title' => 'Flexibility & Mobility',
                'description' => 'Enhance flexibility and prevent injuries',
                'level' => 'Beginner',
                'duration' => 3,
                'frequency' => 'Daily',
                'category' => 'Flexibility',
                'trainer_name' => 'Coach Sarah',
                'trainer_specialization' => 'Physiotherapy',
                'created_date' => '2025-09-15',
                'view_count' => 71,
                'details' => [
                    'Dynamic warm-up routines',
                    'Static stretching protocols',
                    'Injury prevention exercises'
                ]
            ]
        ];
    }

    private function getGeneralNutritionGuides() {
        return [
            [
                'id' => 1,
                'title' => 'Sports Nutrition Basics',
                'description' => 'Essential nutrition fundamentals for athletes',
                'category' => 'General Health',
                'target_audience' => 'All',
                'trainer_name' => 'Nutritionist Sarah',
                'trainer_specialization' => 'Sports Nutrition',
                'created_date' => '2025-10-05',
                'view_count' => 89,
                'content' => [
                    'Understanding macronutrients',
                    'Meal timing for performance',
                    'Hydration strategies',
                    'Recovery nutrition'
                ]
            ],
            [
                'id' => 2,
                'title' => 'Pre & Post Training Nutrition',
                'description' => 'Optimize your nutrition around training sessions',
                'category' => 'Performance',
                'target_audience' => 'Intermediate',
                'trainer_name' => 'Nutritionist David',
                'trainer_specialization' => 'Performance Nutrition',
                'created_date' => '2025-10-02',
                'view_count' => 67,
                'content' => [
                    'Pre-workout meal planning',
                    'During-workout nutrition',
                    'Post-workout recovery meals',
                    'Timing optimization'
                ]
            ],
            [
                'id' => 3,
                'title' => 'Hydration Guidelines',
                'description' => 'Complete guide to proper hydration for athletes',
                'category' => 'Hydration',
                'target_audience' => 'All',
                'trainer_name' => 'Dr. Thompson',
                'trainer_specialization' => 'Sports Medicine',
                'created_date' => '2025-09-30',
                'view_count' => 54,
                'content' => [
                    'Daily water intake recommendations',
                    'Electrolyte balance',
                    'Climate-based hydration',
                    'Hydration monitoring'
                ]
            ],
            [
                'id' => 4,
                'title' => 'Weight Management for Athletes',
                'description' => 'Healthy approaches to weight management in sports',
                'category' => 'Weight Management',
                'target_audience' => 'Advanced',
                'trainer_name' => 'Nutritionist Sarah',
                'trainer_specialization' => 'Sports Nutrition',
                'created_date' => '2025-09-28',
                'view_count' => 42,
                'content' => [
                    'Healthy weight loss strategies',
                    'Muscle mass maintenance',
                    'Performance considerations',
                    'Long-term sustainability'
                ]
            ]
        ];
    }

    private function getGeneralSupplementInfo() {
        return [
            [
                'id' => 1,
                'supplement_name' => 'Whey Protein',
                'description' => 'High-quality complete protein for muscle recovery',
                'benefits' => 'Supports muscle protein synthesis, aids recovery, convenient protein source',
                'recommended_dosage' => '20-30g post-workout',
                'usage' => 'Mix with water or milk, consume within 30 minutes post-workout',
                'safety_rating' => 'Very Safe',
                'category' => 'Protein',
                'trainer_name' => 'Coach Johnson',
                'trainer_specialization' => 'Physical Training',
                'created_date' => '2025-10-08',
                'view_count' => 76
            ],
            [
                'id' => 2,
                'supplement_name' => 'Creatine Monohydrate',
                'description' => 'Well-researched supplement for power and strength',
                'benefits' => 'Increases power output, enhances high-intensity performance, supports muscle growth',
                'recommended_dosage' => '3-5g daily',
                'usage' => 'Take consistently daily, timing not critical',
                'safety_rating' => 'Very Safe',
                'category' => 'Performance',
                'trainer_name' => 'Coach Anderson',
                'trainer_specialization' => 'Strength & Conditioning',
                'created_date' => '2025-10-06',
                'view_count' => 65
            ],
            [
                'id' => 3,
                'supplement_name' => 'Multivitamin',
                'description' => 'Comprehensive vitamin and mineral supplement',
                'benefits' => 'Nutritional insurance, immune system support, general health maintenance',
                'recommended_dosage' => '1 tablet daily with breakfast',
                'usage' => 'Take with food to improve absorption',
                'safety_rating' => 'Very Safe',
                'category' => 'Vitamins',
                'trainer_name' => 'Nutritionist Sarah',
                'trainer_specialization' => 'Sports Nutrition',
                'created_date' => '2025-10-04',
                'view_count' => 58
            ],
            [
                'id' => 4,
                'supplement_name' => 'Beta-Alanine',
                'description' => 'Amino acid that helps buffer muscle acidity',
                'benefits' => 'Improves muscular endurance, delays fatigue, enhances training capacity',
                'recommended_dosage' => '3-5g daily in divided doses',
                'usage' => 'Split into 2-3 doses throughout the day with meals',
                'safety_rating' => 'Generally Safe',
                'category' => 'Performance',
                'trainer_name' => 'Coach Williams',
                'trainer_specialization' => 'Performance Coach',
                'created_date' => '2025-10-01',
                'view_count' => 43
            ],
            [
                'id' => 5,
                'supplement_name' => 'Fish Oil (Omega-3)',
                'description' => 'Essential fatty acids for health and recovery',
                'benefits' => 'Reduces inflammation, supports heart health, aids recovery',
                'recommended_dosage' => '1-2g daily with meals',
                'usage' => 'Take with food to improve absorption and reduce fishy aftertaste',
                'safety_rating' => 'Very Safe',
                'category' => 'Recovery',
                'trainer_name' => 'Dr. Thompson',
                'trainer_specialization' => 'Sports Medicine',
                'created_date' => '2025-09-29',
                'view_count' => 39
            ],
            [
                'id' => 6,
                'supplement_name' => 'Caffeine',
                'description' => 'Natural stimulant for energy and focus',
                'benefits' => 'Increases alertness, improves focus, enhances endurance performance',
                'recommended_dosage' => '100-200mg before training',
                'usage' => '30-45 minutes before exercise, avoid late in the day',
                'safety_rating' => 'Use With Caution',
                'category' => 'Pre-Workout',
                'trainer_name' => 'Trainer Mike',
                'trainer_specialization' => 'Cardio Specialist',
                'created_date' => '2025-09-27',
                'view_count' => 52
            ]
        ];
    }
    
    // =============================================================================
    // COACH BOOKING SYSTEM METHODS
    // =============================================================================
    
    // Get all available coach slots
    private function getAvailableCoachSlots() {
        // In real implementation, this would query the database
        return [
            [
                'slot_id' => 1,
                'coach_id' => 1,
                'coach_name' => 'Coach Johnson',
                'coach_specialization' => 'Batting Specialist',
                'coach_rating' => 4.8,
                'coach_image' => 'coach1.jpg',
                'date' => '2025-10-20',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'session_type' => 'Individual',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2500.00,
                'location' => 'Indoor Net 1',
                'description' => 'Individual batting technique session',
                'status' => 'available'
            ],
            [
                'slot_id' => 2,
                'coach_id' => 1,
                'coach_name' => 'Coach Johnson',
                'coach_specialization' => 'Batting Specialist',
                'coach_rating' => 4.8,
                'coach_image' => 'coach1.jpg',
                'date' => '2025-10-20',
                'start_time' => '10:30:00',
                'end_time' => '11:30:00',
                'session_type' => 'Individual',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2500.00,
                'location' => 'Indoor Net 1',
                'description' => 'Advanced batting techniques',
                'status' => 'available'
            ],
            [
                'slot_id' => 3,
                'coach_id' => 1,
                'coach_name' => 'Coach Johnson',
                'coach_specialization' => 'Batting Specialist',
                'coach_rating' => 4.8,
                'coach_image' => 'coach1.jpg',
                'date' => '2025-10-20',
                'start_time' => '14:00:00',
                'end_time' => '15:30:00',
                'session_type' => 'Group',
                'max_participants' => 3,
                'current_bookings' => 1,
                'price_per_person' => 1500.00,
                'location' => 'Practice Ground A',
                'description' => 'Group batting practice',
                'status' => 'available'
            ],
            [
                'slot_id' => 4,
                'coach_id' => 2,
                'coach_name' => 'Coach Anderson',
                'coach_specialization' => 'Bowling Specialist',
                'coach_rating' => 4.9,
                'coach_image' => 'coach2.jpg',
                'date' => '2025-10-20',
                'start_time' => '09:30:00',
                'end_time' => '10:30:00',
                'session_type' => 'Individual',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2800.00,
                'location' => 'Practice Ground B',
                'description' => 'Individual bowling coaching',
                'status' => 'available'
            ],
            [
                'slot_id' => 5,
                'coach_id' => 2,
                'coach_name' => 'Coach Anderson',
                'coach_specialization' => 'Bowling Specialist',
                'coach_rating' => 4.9,
                'coach_image' => 'coach2.jpg',
                'date' => '2025-10-20',
                'start_time' => '11:00:00',
                'end_time' => '12:30:00',
                'session_type' => 'Group',
                'max_participants' => 4,
                'current_bookings' => 2,
                'price_per_person' => 1800.00,
                'location' => 'Main Ground',
                'description' => 'Group bowling practice',
                'status' => 'available'
            ],
            [
                'slot_id' => 6,
                'coach_id' => 3,
                'coach_name' => 'Coach Williams',
                'coach_specialization' => 'Fitness Specialist',
                'coach_rating' => 4.7,
                'coach_image' => 'coach3.jpg',
                'date' => '2025-10-20',
                'start_time' => '06:00:00',
                'end_time' => '07:00:00',
                'session_type' => 'Fitness',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2000.00,
                'location' => 'Gym',
                'description' => 'Personal fitness training',
                'status' => 'available'
            ],
            [
                'slot_id' => 7,
                'coach_id' => 3,
                'coach_name' => 'Coach Williams',
                'coach_specialization' => 'Fitness Specialist',
                'coach_rating' => 4.7,
                'coach_image' => 'coach3.jpg',
                'date' => '2025-10-20',
                'start_time' => '18:00:00',
                'end_time' => '19:00:00',
                'session_type' => 'Group',
                'max_participants' => 6,
                'current_bookings' => 3,
                'price_per_person' => 1200.00,
                'location' => 'Gym',
                'description' => 'Group fitness class',
                'status' => 'available'
            ],
            [
                'slot_id' => 8,
                'coach_id' => 1,
                'coach_name' => 'Coach Johnson',
                'coach_specialization' => 'Batting Specialist',
                'coach_rating' => 4.8,
                'coach_image' => 'coach1.jpg',
                'date' => '2025-10-21',
                'start_time' => '08:00:00',
                'end_time' => '09:00:00',
                'session_type' => 'Individual',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2500.00,
                'location' => 'Indoor Net 1',
                'description' => 'Early morning batting session',
                'status' => 'available'
            ]
        ];
    }
    
    // Book a coach session
    private function bookCoachSession($slotId, $playerId, $specialRequests = '') {
        try {
            $slots = $this->getAvailableCoachSlots();
            $slot = null;
            
            foreach ($slots as $s) {
                if ($s['slot_id'] == $slotId) {
                    $slot = $s;
                    break;
                }
            }
            
            if (!$slot) {
                return ['success' => false, 'message' => 'Session slot not found'];
            }
            
            if ($slot['current_bookings'] >= $slot['max_participants']) {
                return ['success' => false, 'message' => 'Session is fully booked'];
            }
            
            // Create booking (mock)
            $bookingId = rand(1000, 9999);
            $totalAmount = $slot['price_per_person'];
            
            return [
                'success' => true,
                'booking_id' => $bookingId,
                'total_amount' => $totalAmount,
                'session_details' => $slot,
                'payment_required' => true,
                'message' => 'Booking created successfully. Please complete payment to confirm.'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()];
        }
    }
    
    // Process booking payment
    private function processBookingPayment($bookingId, $paymentMethod, $paymentDetails = []) {
        try {
            // Mock payment processing - 90% success rate for demo
            $success = rand(1, 10) > 1;
            
            if ($success) {
                $transactionId = 'TXN_' . date('Ymd') . '_' . str_pad($bookingId, 6, '0', STR_PAD_LEFT);
                
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'message' => 'Payment processed successfully. Your session is confirmed!',
                    'booking_confirmed' => true
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment failed. Please try again or use a different payment method.'
                ];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Payment processing failed: ' . $e->getMessage()];
        }
    }
    
    // Get filtered available slots based on criteria
    private function getFilteredAvailableSlots($filters = []) {
        $slots = $this->getAvailableCoachSlots();
        
        if (empty($filters)) {
            return $slots;
        }
        
        $filtered = array_filter($slots, function($slot) use ($filters) {
            // Filter by coach
            if (!empty($filters['coach_id']) && $slot['coach_id'] != $filters['coach_id']) {
                return false;
            }
            
            // Filter by date
            if (!empty($filters['date']) && $slot['date'] != $filters['date']) {
                return false;
            }
            
            // Filter by session type
            if (!empty($filters['session_type']) && $slot['session_type'] != $filters['session_type']) {
                return false;
            }
            
            // Filter by time range
            if (!empty($filters['start_time']) && $slot['start_time'] < $filters['start_time']) {
                return false;
            }
            
            if (!empty($filters['end_time']) && $slot['end_time'] > $filters['end_time']) {
                return false;
            }
            
            // Filter by availability
            if (!empty($filters['available_only']) && $slot['current_bookings'] >= $slot['max_participants']) {
                return false;
            }
            
            return true;
        });
        
        return array_values($filtered);
    }
    
    // Get all coaches with their specializations
    private function getAllCoaches() {
        // In real implementation, this would query the coaches table
        return [
            [
                'coach_id' => 1,
                'name' => 'Coach Johnson',
                'specialization' => 'Batting Specialist',
                'rating' => 4.8,
                'image' => 'coach1.jpg',
                'experience_years' => 8,
                'price_range' => '₹2,000 - ₹3,000',
                'availability_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'bio' => 'Expert batting coach with 8 years of experience. Specializes in technique improvement and match preparation.'
            ],
            [
                'coach_id' => 2,
                'name' => 'Coach Anderson',
                'specialization' => 'Bowling Specialist',
                'rating' => 4.9,
                'image' => 'coach2.jpg',
                'experience_years' => 10,
                'price_range' => '₹2,500 - ₹3,500',
                'availability_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'bio' => 'Former professional bowler with 10+ years of coaching experience. Specializes in pace and swing bowling techniques.'
            ],
            [
                'coach_id' => 3,
                'name' => 'Coach Williams',
                'specialization' => 'Fitness Specialist',
                'rating' => 4.7,
                'image' => 'coach3.jpg',
                'experience_years' => 6,
                'price_range' => '₹1,500 - ₹2,500',
                'availability_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'bio' => 'Certified fitness trainer specializing in cricket-specific conditioning and injury prevention.'
            ]
        ];
    }
    
    // Cancel booking
    private function cancelBooking($bookingId, $playerId, $reason = '') {
        try {
            // Mock cancellation logic
            $cancellationFee = 0;
            $refundAmount = 0;
            
            // Determine cancellation policy based on time until session
            $timeUntilSession = rand(1, 48); // Mock hours until session
            
            if ($timeUntilSession >= 24) {
                $refundAmount = 100; // Full refund
                $cancellationFee = 0;
            } elseif ($timeUntilSession >= 12) {
                $refundAmount = 75; // 75% refund
                $cancellationFee = 25;
            } elseif ($timeUntilSession >= 2) {
                $refundAmount = 50; // 50% refund
                $cancellationFee = 50;
            } else {
                $refundAmount = 0; // No refund
                $cancellationFee = 100;
            }
            
            return [
                'success' => true,
                'refund_percentage' => $refundAmount,
                'cancellation_fee_percentage' => $cancellationFee,
                'message' => 'Booking cancelled successfully. Refund will be processed within 3-5 business days.',
                'cancellation_policy' => [
                    '24+ hours: 100% refund',
                    '12-24 hours: 75% refund',
                    '2-12 hours: 50% refund',
                    'Less than 2 hours: No refund'
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()];
        }
    }
    
    // Get player's booking history
    private function getPlayerBookingHistory($playerId) {
        // Mock booking history
        return [
            [
                'booking_id' => 1001,
                'coach_name' => 'Coach Johnson',
                'session_type' => 'Individual',
                'date' => '2025-10-15',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'status' => 'completed',
                'amount_paid' => 2500.00,
                'rating_given' => 5,
                'feedback' => 'Excellent session on batting technique'
            ],
            [
                'booking_id' => 1002,
                'coach_name' => 'Coach Anderson',
                'session_type' => 'Group',
                'date' => '2025-10-18',
                'start_time' => '14:00:00',
                'end_time' => '15:30:00',
                'status' => 'confirmed',
                'amount_paid' => 1800.00,
                'rating_given' => null,
                'feedback' => null
            ],
            [
                'booking_id' => 1003,
                'coach_name' => 'Coach Williams',
                'session_type' => 'Fitness',
                'date' => '2025-10-12',
                'start_time' => '06:00:00',
                'end_time' => '07:00:00',
                'status' => 'cancelled',
                'amount_paid' => 2000.00,
                'refund_amount' => 1500.00,
                'rating_given' => null,
                'feedback' => null
            ]
        ];
    }

    // Get session types
    private function getSessionTypes() {
        return [
            'Individual' => 'One-on-one coaching session',
            'Group' => 'Small group training (2-4 players)',
            'Fitness' => 'Physical conditioning session',
            'Match Preparation' => 'Pre-match coaching and strategy'
        ];
    }

    // Get all trainers
    private function getAllTrainers() {
        return [
            [
                'trainer_id' => 1,
                'name' => 'Trainer Mike Thompson',
                'specialization' => 'Strength & Conditioning',
                'rating' => 4.9,
                'image' => 'trainer1.jpg',
                'experience_years' => 12,
                'price_range' => '₹1,500 - ₹2,500',
                'availability_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'bio' => 'Certified strength and conditioning specialist with 12+ years experience in cricket fitness training.'
            ],
            [
                'trainer_id' => 2,
                'name' => 'Trainer Sarah Williams',
                'specialization' => 'Physiotherapy & Injury Prevention',
                'rating' => 4.8,
                'image' => 'trainer2.jpg',
                'experience_years' => 8,
                'price_range' => '₹2,000 - ₹3,000',
                'availability_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'bio' => 'Licensed physiotherapist specializing in sports injury prevention and rehabilitation for cricket players.'
            ],
            [
                'trainer_id' => 3,
                'name' => 'Trainer David Kumar',
                'specialization' => 'Nutrition & Wellness',
                'rating' => 4.7,
                'image' => 'trainer3.jpg',
                'experience_years' => 6,
                'price_range' => '₹1,200 - ₹2,000',
                'availability_days' => ['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'bio' => 'Sports nutritionist and wellness coach focusing on cricket-specific dietary planning and mental wellness.'
            ]
        ];
    }

    // Get available trainer slots
    private function getAvailableTrainerSlots() {
        return [
            [
                'slot_id' => 1,
                'trainer_id' => 1,
                'trainer_name' => 'Trainer Mike Thompson',
                'trainer_specialization' => 'Strength & Conditioning',
                'trainer_rating' => 4.9,
                'trainer_image' => 'trainer1.jpg',
                'date' => '2025-10-20',
                'start_time' => '06:00:00',
                'end_time' => '07:00:00',
                'session_type' => 'Individual Fitness',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2000.00,
                'location' => 'Fitness Center',
                'description' => 'Personal strength and conditioning session',
                'status' => 'available'
            ],
            [
                'slot_id' => 2,
                'trainer_id' => 1,
                'trainer_name' => 'Trainer Mike Thompson',
                'trainer_specialization' => 'Strength & Conditioning',
                'trainer_rating' => 4.9,
                'trainer_image' => 'trainer1.jpg',
                'date' => '2025-10-20',
                'start_time' => '07:30:00',
                'end_time' => '08:30:00',
                'session_type' => 'Group Fitness',
                'max_participants' => 4,
                'current_bookings' => 2,
                'price_per_person' => 1500.00,
                'location' => 'Fitness Center',
                'description' => 'Group strength training session',
                'status' => 'available'
            ],
            [
                'slot_id' => 3,
                'trainer_id' => 2,
                'trainer_name' => 'Trainer Sarah Williams',
                'trainer_specialization' => 'Physiotherapy & Injury Prevention',
                'trainer_rating' => 4.8,
                'trainer_image' => 'trainer2.jpg',
                'date' => '2025-10-20',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'session_type' => 'Injury Assessment',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 2500.00,
                'location' => 'Physiotherapy Room',
                'description' => 'Individual injury prevention assessment',
                'status' => 'available'
            ],
            [
                'slot_id' => 4,
                'trainer_id' => 3,
                'trainer_name' => 'Trainer David Kumar',
                'trainer_specialization' => 'Nutrition & Wellness',
                'trainer_rating' => 4.7,
                'trainer_image' => 'trainer3.jpg',
                'date' => '2025-10-20',
                'start_time' => '11:00:00',
                'end_time' => '12:00:00',
                'session_type' => 'Nutrition Consultation',
                'max_participants' => 1,
                'current_bookings' => 0,
                'price_per_person' => 1800.00,
                'location' => 'Consultation Room',
                'description' => 'Personal nutrition and wellness consultation',
                'status' => 'available'
            ]
        ];
    }

    // Profile Management
    public function profile() {
        $this->requireLogin();
        
        // Get comprehensive user profile data
        $userModel = $this->model('M_Users');
        $userId = $_SESSION['user_id'] ?? 1; // Fallback to user ID 1 if session not set
        $userProfile = $userModel->getUserWithProfile($userId);
        
        // If getUserWithProfile fails, try getting basic user data
        if (!$userProfile) {
            $userProfile = $userModel->getUserById($userId);
        }
        
        // If still no user data, create a basic user object
        if (!$userProfile) {
            $userProfile = (object) [
                'UserID' => $userId,
                'Name' => $_SESSION['user_name'] ?? 'Unknown User',
                'Email' => $_SESSION['user_email'] ?? '',
                'PhoneNumber' => '',
                'Address' => '',
                'School' => '',
                'Role' => $_SESSION['user_role'] ?? 'Player',
                'Status' => 'active'
            ];
        }
        
        // Check if profile is complete (has essential details filled)
        $isProfileComplete = $this->isProfileComplete($userProfile);
        
        $data = [
            'title' => $isProfileComplete ? 'My Profile' : 'Add Profile Details',
            'user' => $userProfile,
            'player' => $this->getPlayerData(),
            'isProfileComplete' => $isProfileComplete,
            'formMode' => $isProfileComplete ? 'update' : 'add'
        ];
        
        $this->view('player/profile', $data);
    }

    // Check if profile has essential details filled
    private function isProfileComplete($userProfile) {
        // Check if essential player profile fields are filled
        $essentialFields = [
            'Name', 'Email', 'PhoneNumber', 'Address'
        ];
        
        foreach ($essentialFields as $field) {
            if (empty($userProfile->$field)) {
                return false;
            }
        }
        
        // For players, also check if cricket-specific details are filled
        if (isset($userProfile->Role) && $userProfile->Role === 'Player') {
            if (empty($userProfile->BattingStyle) && empty($userProfile->BowlingStyle)) {
                return false;
            }
        }
        
        return true;
    }

    // Add Profile Details (for first-time profile setup)
    public function addProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->requireLogin();
            
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
            if (empty($userData['phone_number'])) {
                $errors[] = 'Phone number is required';
            }
            if (empty($userData['address'])) {
                $errors[] = 'Address is required';
            }
            
            if (empty($errors)) {
                $updateSuccess = false;
                
                // Update basic user info
                if ($userModel->updateUser($userData)) {
                    // Update session data
                    $_SESSION['user_name'] = $userData['name'];
                    $_SESSION['user_email'] = $userData['email'];
                    
                    // Add role-specific profile data
                    $userRole = $_SESSION['user_role'] ?? 'Player';
                    
                    if ($userRole === 'Player') {
                        $playerData = [
                            'user_id' => $userId,
                            'batting_style' => trim($_POST['batting_style'] ?? ''),
                            'bowling_style' => trim($_POST['bowling_style'] ?? ''),
                            'jersey_number' => !empty($_POST['jersey_number']) ? intval($_POST['jersey_number']) : null,
                            'subscription_type' => trim($_POST['subscription_type'] ?? 'basic'),
                            'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
                            'emergency_contact_phone' => trim($_POST['emergency_contact_phone'] ?? ''),
                            'parent_guardian_name' => trim($_POST['parent_guardian_name'] ?? ''),
                            'parent_guardian_phone' => trim($_POST['parent_guardian_phone'] ?? ''),
                            'school_institution' => trim($_POST['school_institution'] ?? ''),
                            'previous_experience' => trim($_POST['previous_experience'] ?? ''),
                            'medical_conditions' => trim($_POST['medical_conditions'] ?? ''),
                            'how_heard_about_us' => trim($_POST['how_heard_about_us'] ?? '')
                        ];
                        $updateSuccess = $userModel->updatePlayerProfile($playerData);
                    }
                    
                    if ($updateSuccess) {
                        flash('profile_message', 'Profile details added successfully! Welcome to Elite Cricket Academy!', 'alert alert-success');
                    } else {
                        flash('profile_message', 'Profile updated, but some additional details may not have been saved', 'alert alert-warning');
                    }
                } else {
                    flash('profile_message', 'Failed to add profile details', 'alert alert-danger');
                }
            } else {
                flash('profile_message', implode('<br>', $errors), 'alert alert-danger');
            }
        }
        
        redirect('player/profile');
    }

    // Update Profile
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->requireLogin();
            
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
                $updateSuccess = false;
                
                // Update basic user info
                if ($userModel->updateUser($userData)) {
                    // Update session data
                    $_SESSION['user_name'] = $userData['name'];
                    $_SESSION['user_email'] = $userData['email'];
                    
                    // Update role-specific profile data
                    $userRole = $_SESSION['user_role'] ?? 'Player';
                    
                    switch ($userRole) {
                        case 'Player':
                            $playerData = [
                                'user_id' => $userId,
                                'batting_style' => trim($_POST['batting_style'] ?? ''),
                                'bowling_style' => trim($_POST['bowling_style'] ?? ''),
                                'jersey_number' => !empty($_POST['jersey_number']) ? intval($_POST['jersey_number']) : null,
                                'subscription_type' => trim($_POST['subscription_type'] ?? 'basic'),
                                'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
                                'emergency_contact_phone' => trim($_POST['emergency_contact_phone'] ?? ''),
                                'parent_guardian_name' => trim($_POST['parent_guardian_name'] ?? ''),
                                'parent_guardian_phone' => trim($_POST['parent_guardian_phone'] ?? ''),
                                'school_institution' => trim($_POST['school_institution'] ?? ''),
                                'previous_experience' => trim($_POST['previous_experience'] ?? ''),
                                'medical_conditions' => trim($_POST['medical_conditions'] ?? ''),
                                'how_heard_about_us' => trim($_POST['how_heard_about_us'] ?? '')
                            ];
                            $updateSuccess = $userModel->updatePlayerProfile($playerData);
                            break;
                            
                        case 'Coach':
                            $coachData = [
                                'user_id' => $userId,
                                'specialization' => trim($_POST['specialization'] ?? ''),
                                'experience' => !empty($_POST['experience']) ? intval($_POST['experience']) : 0,
                                'certifications' => trim($_POST['certifications'] ?? '')
                            ];
                            $updateSuccess = $userModel->updateCoachProfile($coachData);
                            break;
                            
                        case 'Trainer':
                            $trainerData = [
                                'user_id' => $userId,
                                'experience' => !empty($_POST['experience']) ? intval($_POST['experience']) : 0,
                                'certifications' => trim($_POST['certifications'] ?? '')
                            ];
                            $updateSuccess = $userModel->updateTrainerProfile($trainerData);
                            break;
                            
                        case 'ShopEmployee':
                            $shopData = [
                                'user_id' => $userId,
                                'department' => trim($_POST['department'] ?? 'General')
                            ];
                            $updateSuccess = $userModel->updateShopEmployeeProfile($shopData);
                            break;
                            
                        case 'Admin':
                            $adminData = [
                                'user_id' => $userId,
                                'admin_level' => trim($_POST['admin_level'] ?? 'system_admin'),
                                'department' => trim($_POST['department'] ?? 'General'),
                                'security_clearance' => trim($_POST['security_clearance'] ?? 'Level1')
                            ];
                            $updateSuccess = $userModel->updateAdminProfile($adminData);
                            break;
                            
                        default:
                            $updateSuccess = true; // No additional profile to update
                            break;
                    }
                    
                    if ($updateSuccess) {
                        flash('profile_message', 'Profile updated successfully');
                    } else {
                        flash('profile_message', 'Profile updated, but some role-specific data may not have been saved', 'alert alert-warning');
                    }
                } else {
                    flash('profile_message', 'Failed to update profile', 'alert alert-danger');
                }
            } else {
                flash('profile_message', implode('<br>', $errors), 'alert alert-danger');
            }
        }
        
        redirect('player/profile');
    }



    // Deactivate Account
    public function deactivateAccount() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->requireLogin();
            
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'];
            
            if ($userModel->suspendUser($userId, 9999)) { // Long suspension = deactivation
                // Clear session and redirect to login
                session_destroy();
                flash('login_message', 'Your account has been deactivated successfully');
                redirect('login');
            } else {
                flash('profile_message', 'Failed to deactivate account', 'alert alert-danger');
                redirect('player/profile');
            }
        } else {
            redirect('player/profile');
        }
    }

    // Get trainer session types
    private function getTrainerSessionTypes() {
        return [
            'Individual Fitness' => 'Personal fitness training session',
            'Group Fitness' => 'Small group fitness training (2-4 players)',
            'Injury Assessment' => 'Individual injury prevention consultation',
            'Nutrition Consultation' => 'Personal nutrition and dietary planning',
            'Rehabilitation' => 'Injury recovery and rehabilitation session'
        ];
    }
}
?>

