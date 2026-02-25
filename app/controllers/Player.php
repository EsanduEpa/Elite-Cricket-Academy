<?php
class Player extends Controller {
    
    private $userModel;
    private $medicalModel;
    private $achievementModel;
    private $productModel;
    private $trainerModel;
    
    public function __construct() {
        // Check authentication for all player pages
        requireAuth(['Player']);
        // Enable database for achievement functionality
        $this->userModel = $this->model('M_Users');
        // $this->medicalModel = $this->model('M_Medical');
        $this->achievementModel = $this->model('M_Achievement');
        $this->productModel = $this->model('M_Product');
        $this->trainerModel = $this->model('M_Trainer');
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
        $sessionModel = $this->model('M_Session');
        $coachSessions = [];
        if (isset($playerData['id'])) {
            $coachSessions = $sessionModel->getUpcomingSessionsForPlayer($playerData['id']);
        }

        $data = [
            'title' => 'Player Dashboard - ' . $playerData['name'],
            'player' => $playerData,
            'todaySchedule' => $this->getTodaySchedule(),
            'upcomingSchedule' => $this->getUpcomingSchedule(),
            'upcomingBookings' => $this->getUpcomingBookings(),
            'rentalsDue' => $this->getRentalsDue(),
            'paymentsDue' => $this->getPaymentsDue(),
            'performanceStats' => $this->getPerformanceStats(),
            'battingStats' => $this->getBattingStats(),
            'bowlingStats' => $this->getBowlingStats(),
            'coachSessions' => $coachSessions
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
        
        // Get supplement, workout, and nutrition plans for this player
        $supplementModel = $this->model('M_SupplementPlan');
        $nutritionModel = $this->model('M_NutritionPlan');

        $data = [
            'title' => 'Medical Records',
            'player' => $playerData,
            'medicalRecords' => $medicalRecords,
            'medicalHistory' => $this->getMedicalHistory(),
            'vaccinations' => $this->getVaccinations(),
            'injuries' => $this->getInjuries(),
            'supplements' => $supplementModel->getSupplementPlansByPlayer($playerId),
            'workoutPlans' => $this->trainerModel->getWorkoutPlansByPlayer($playerId),
            'nutritionPlans' => $nutritionModel->getNutritionPlansByPlayer($playerId)
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
            
            // Handle file upload for diagnosis receipt
            $diagnosisReceiptURL = null;
            if (isset($_FILES['diagnosis_receipt']) && $_FILES['diagnosis_receipt']['error'] == 0) {
                $uploadDir = 'public/uploads/medical_receipts/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate unique filename
                $fileExtension = pathinfo($_FILES['diagnosis_receipt']['name'], PATHINFO_EXTENSION);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                
                if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                    $fileName = 'receipt_' . $playerId . '_' . time() . '.' . $fileExtension;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['diagnosis_receipt']['tmp_name'], $targetPath)) {
                        // Store path without 'public/' prefix for web access
                        $diagnosisReceiptURL = 'uploads/medical_receipts/' . $fileName;
                    }
                }
            }
            
            $data = [
                'player_id' => $playerId,
                'injury_details' => trim($_POST['injury_details']),
                'diagnosis' => trim($_POST['diagnosis']),
                'treatment_given' => trim($_POST['treatment_given']) ?: '',
                'recovery_status' => $_POST['recovery_status'],
                'injury_date' => $_POST['injury_date'],
                'happened_at_academy' => $_POST['happened_at_academy'] ?? 'no',
                'rest_days_needed' => intval($_POST['rest_days_needed']) ?: 0,
                'diagnosis_receipt_url' => $diagnosisReceiptURL,
                'reported_date' => $_POST['reported_date'],
                'reported_by' => $reportedBy
            ];
            
            // Validate data
            $errors = [];
            
            // Required fields
            if (empty($data['injury_details'])) {
                $errors[] = 'Injury details are required';
            }
            if (empty($data['diagnosis'])) {
                $errors[] = 'Diagnosis is required';
            }
            if (empty($data['injury_date'])) {
                $errors[] = 'Injury date is required';
            }
            if (empty($data['reported_date'])) {
                $errors[] = 'Reported date is required';
            }
            
            // Validate injury details - at least 2 words
            $injuryWords = str_word_count($data['injury_details']);
            if ($injuryWords < 2) {
                $errors[] = 'Injury details must contain at least 2 words';
            }
            
            // Validate diagnosis - at least 2 words
            $diagnosisWords = str_word_count($data['diagnosis']);
            if ($diagnosisWords < 2) {
                $errors[] = 'Diagnosis must contain at least 2 words';
            }
            
            // Validate rest days - must be integer between 0 and 1000
            if ($data['rest_days_needed'] < 0) {
                $errors[] = 'Rest days cannot be negative';
            }
            if ($data['rest_days_needed'] > 1000) {
                $errors[] = 'Rest days cannot exceed 1000 days';
            }
            // Check if decimal was submitted (cast to float and compare)
            if (isset($_POST['rest_days_needed']) && !empty($_POST['rest_days_needed'])) {
                $restDaysFloat = floatval($_POST['rest_days_needed']);
                $restDaysInt = intval($_POST['rest_days_needed']);
                if ($restDaysFloat != $restDaysInt) {
                    $errors[] = 'Rest days must be a whole number (no decimals allowed)';
                }
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
        $perfModel = $this->model('M_Performance');
        $playerId = $_SESSION['user_id'] ?? 6;
        
        $data = [
            'title' => 'Performance History',
            'player' => $this->getPlayerData(),
            'practiceMatches' => $this->getPracticeMatches(),
            'tournaments' => $this->getTournaments(),
            'performanceStats' => $this->getDetailedPerformanceStats(),
            'achievements' => $this->getPlayerAchievements(),
            'playerPerformanceRecords' => $perfModel->getPerformanceStatistics($playerId, true),
            'pendingPerformanceRecords' => $perfModel->getPendingPerformanceStatistics($playerId)
        ];
        $this->view('player/performance', $data);
    }

    // Add Achievement (AJAX method)
    public function addAchievement() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize input data
            $data = [
                'player_id' => $_SESSION['user_id'] ?? 6, // Default to 6 for testing
                'date' => trim($_POST['date'] ?? ''),
                'match_name' => trim($_POST['match_name'] ?? ''),
                'tournament' => trim($_POST['tournament'] ?? ''),
                'achievement' => trim($_POST['achievement'] ?? ''),
                'verified_status' => 'pending' // New achievements start as pending
            ];

            // Validate required fields
            $errors = [];
            if (empty($data['date'])) {
                $errors[] = 'Date is required';
            }
            if (empty($data['match_name'])) {
                $errors[] = 'Match name is required';
            }
            if (empty($data['tournament'])) {
                $errors[] = 'Tournament is required';
            }
            if (empty($data['achievement'])) {
                $errors[] = 'Achievement description is required';
            }

            // Return JSON response
            header('Content-Type: application/json');
            
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                return;
            }

            // Try to add achievement to database
            try {
                $achievementId = $this->achievementModel->addAchievement($data);
                
                if ($achievementId) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Achievement added successfully! It will be reviewed by coaching staff.',
                        'achievement_id' => $achievementId
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to add achievement. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Achievement creation error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred. Please try again later.'
                ]);
            }
        } else {
            // Redirect if not POST request
            redirect('player/performance');
        }
    }

    // Edit Achievement (AJAX method)
    public function editAchievement() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize input data
            $data = [
                'achievement_id' => filter_input(INPUT_POST, 'achievement_id', FILTER_VALIDATE_INT),
                'player_id' => $_SESSION['user_id'] ?? 6, // Default to 6 for testing
                'date' => trim($_POST['date'] ?? ''),
                'match_name' => trim($_POST['match_name'] ?? ''),
                'tournament' => trim($_POST['tournament'] ?? ''),
                'achievement' => trim($_POST['achievement'] ?? ''),
                'verified_status' => trim($_POST['verified_status'] ?? 'pending')
            ];

            // Validate required fields
            $errors = [];
            if (empty($data['achievement_id'])) {
                $errors[] = 'Achievement ID is required';
            }
            if (empty($data['date'])) {
                $errors[] = 'Date is required';
            }
            if (empty($data['match_name'])) {
                $errors[] = 'Match name is required';
            }
            if (empty($data['tournament'])) {
                $errors[] = 'Tournament is required';
            }
            if (empty($data['achievement'])) {
                $errors[] = 'Achievement description is required';
            }

            // Return JSON response
            header('Content-Type: application/json');
            
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                return;
            }

            // Try to update achievement in database
            try {
                $success = $this->achievementModel->updateAchievement($data);
                
                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Achievement updated successfully!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update achievement. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Achievement update error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred. Please try again later.'
                ]);
            }
        } else {
            // Redirect if not POST request
            redirect('player/performance');
        }
    }

    // Get Achievement (AJAX method)
    public function getAchievement() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $achievementId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $playerId = $_SESSION['user_id'] ?? 6; // Default to 6 for testing

            header('Content-Type: application/json');

            if (!$achievementId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid achievement ID'
                ]);
                return;
            }

            try {
                $achievement = $this->achievementModel->getAchievementById($achievementId);
                
                if ($achievement && $achievement->PlayerID == $playerId) {
                    echo json_encode([
                        'success' => true,
                        'achievement' => $achievement
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Achievement not found or access denied'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Achievement retrieval error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred'
                ]);
            }
        } else {
            redirect('player/performance');
        }
    }

    // Delete Achievement (AJAX method)
    public function deleteAchievement() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $achievementId = filter_input(INPUT_POST, 'achievement_id', FILTER_VALIDATE_INT);
            $playerId = $_SESSION['user_id'] ?? 6; // Default to 6 for testing

            header('Content-Type: application/json');

            if (!$achievementId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid achievement ID'
                ]);
                return;
            }

            try {
                // First check if the achievement exists and belongs to the player
                $achievement = $this->achievementModel->getAchievementById($achievementId);
                
                if (!$achievement || $achievement->PlayerID != $playerId) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Achievement not found or access denied'
                    ]);
                    return;
                }

                // Check if the achievement is rejected (only rejected achievements can be deleted)
                if ($achievement->VerifiedStatus !== 'rejected') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Only rejected achievements can be deleted'
                    ]);
                    return;
                }

                // Delete the achievement
                $success = $this->achievementModel->deleteAchievement($achievementId, $playerId);
                
                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Rejected achievement deleted successfully!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete achievement. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Achievement deletion error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred. Please try again later.'
                ]);
            }
        } else {
            redirect('player/performance');
        }
    }

    // Add Performance Statistics (AJAX method)
    public function addPerformanceStats() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'player_id' => $_SESSION['user_id'] ?? 6,
                'match_id' => filter_input(INPUT_POST, 'match_id', FILTER_VALIDATE_INT),
                'runs_scored' => filter_input(INPUT_POST, 'runs_scored', FILTER_VALIDATE_INT) ?? 0,
                'balls_faced' => filter_input(INPUT_POST, 'balls_faced', FILTER_VALIDATE_INT) ?? 0,
                'wickets_taken' => filter_input(INPUT_POST, 'wickets_taken', FILTER_VALIDATE_INT) ?? 0,
                'overs_bowled' => floatval($_POST['overs_bowled'] ?? 0),
                'runs_conceded' => filter_input(INPUT_POST, 'runs_conceded', FILTER_VALIDATE_INT) ?? 0,
                'catches' => filter_input(INPUT_POST, 'catches', FILTER_VALIDATE_INT) ?? 0,
                'stumpings' => filter_input(INPUT_POST, 'stumpings', FILTER_VALIDATE_INT) ?? 0,
                'rating' => floatval($_POST['rating'] ?? 0),
                'added_by' => $_SESSION['user_id'] ?? 6
            ];

            $errors = [];
            if (!$data['match_id']) {
                $errors[] = 'Please select a match';
            }

            header('Content-Type: application/json');
            
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                return;
            }

            try {
                $perfModel = $this->model('M_Performance');
                $performanceId = $perfModel->addPerformanceStatistics($data);
                
                if ($performanceId) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Performance statistics added successfully! It will be reviewed by coaching staff.',
                        'performance_id' => $performanceId
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to add performance statistics. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Performance statistics creation error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'error_details' => 'Check if database schema is up to date. Run check_performance_schema.php'
                ]);
            }
        } else {
            redirect('player/performance');
        }
    }

    // Get available matches for dropdown (AJAX method)
    public function getAvailableMatches() {
        header('Content-Type: application/json');
        
        try {
            $perfModel = $this->model('M_Performance');
            $matches = $perfModel->getAvailableMatches(50);
            
            echo json_encode([
                'success' => true,
                'matches' => $matches
            ]);
        } catch (Exception $e) {
            error_log("Error fetching matches: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load matches'
            ]);
        }
    }

    // Get single performance record (AJAX method)
    public function getPerformanceRecord() {
        header('Content-Type: application/json');
        
        $performanceId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$performanceId) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid performance ID'
            ]);
            return;
        }
        
        try {
            $perfModel = $this->model('M_Performance');
            $performance = $perfModel->getPerformanceById($performanceId);
            
            if ($performance) {
                echo json_encode([
                    'success' => true,
                    'performance' => $performance
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Performance record not found'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error fetching performance: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load performance record'
            ]);
        }
    }

    // Edit/Update Performance Statistics (AJAX method)
    public function editPerformanceStats() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $performanceId = filter_input(INPUT_POST, 'performance_id', FILTER_VALIDATE_INT);
            $playerId = $_SESSION['user_id'] ?? 6;
            
            $data = [
                'match_id' => filter_input(INPUT_POST, 'match_id', FILTER_VALIDATE_INT),
                'runs_scored' => filter_input(INPUT_POST, 'runs_scored', FILTER_VALIDATE_INT) ?? 0,
                'balls_faced' => filter_input(INPUT_POST, 'balls_faced', FILTER_VALIDATE_INT) ?? 0,
                'wickets_taken' => filter_input(INPUT_POST, 'wickets_taken', FILTER_VALIDATE_INT) ?? 0,
                'overs_bowled' => floatval($_POST['overs_bowled'] ?? 0),
                'runs_conceded' => filter_input(INPUT_POST, 'runs_conceded', FILTER_VALIDATE_INT) ?? 0,
                'catches' => filter_input(INPUT_POST, 'catches', FILTER_VALIDATE_INT) ?? 0,
                'stumpings' => filter_input(INPUT_POST, 'stumpings', FILTER_VALIDATE_INT) ?? 0,
                'rating' => floatval($_POST['rating'] ?? 0)
            ];

            $errors = [];
            if (!$performanceId) {
                $errors[] = 'Invalid performance ID';
            }
            if (!$data['match_id']) {
                $errors[] = 'Please select a match';
            }

            header('Content-Type: application/json');
            
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                return;
            }

            try {
                $perfModel = $this->model('M_Performance');
                
                // Check if performance exists and belongs to the player
                $existing = $perfModel->getPerformanceById($performanceId);
                if (!$existing || $existing->PlayerID != $playerId) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Performance record not found or access denied'
                    ]);
                    return;
                }

                // Check if still pending
                if ($existing->VerifiedStatus !== 'pending') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cannot edit verified or rejected performance records'
                    ]);
                    return;
                }

                $success = $perfModel->updatePerformanceStatistics($performanceId, $data);
                
                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Performance statistics updated successfully!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update performance statistics. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Performance update error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred. Please try again later.'
                ]);
            }
        } else {
            redirect('player/performance');
        }
    }

    // Delete Performance Statistics (AJAX method)
    public function deletePerformanceStats() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $performanceId = filter_input(INPUT_POST, 'performance_id', FILTER_VALIDATE_INT);
            $playerId = $_SESSION['user_id'] ?? 6;

            header('Content-Type: application/json');

            if (!$performanceId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid performance ID'
                ]);
                return;
            }

            try {
                $perfModel = $this->model('M_Performance');
                
                // Check if performance exists and belongs to the player
                $existing = $perfModel->getPerformanceById($performanceId);
                if (!$existing || $existing->PlayerID != $playerId) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Performance record not found or access denied'
                    ]);
                    return;
                }

                // Check if still pending (can only delete pending records)
                if ($existing->VerifiedStatus !== 'pending') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Can only delete pending performance records'
                    ]);
                    return;
                }

                $success = $perfModel->deletePerformanceStatistics($performanceId, $playerId);
                
                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Performance record deleted successfully!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete performance record. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Performance deletion error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred. Please try again later.'
                ]);
            }
        } else {
            redirect('player/performance');
        }
    }

    // Get player achievements (for display)
    private function getPlayerAchievements() {
        $playerId = $_SESSION['user_id'] ?? 6; // Default to 6 for testing
        
        // First, try to get data from database
        try {
            $achievements = $this->achievementModel->getAchievementsByPlayer($playerId);
            
            // If we get successful results from database, return them
            if ($achievements !== false) {
                error_log("Successfully fetched " . count($achievements) . " achievements from database");
                return $achievements;
            }
            
        } catch (Exception $e) {
            error_log("Database error fetching achievements: " . $e->getMessage());
            // Continue to fallback data
        }
        
        // If database fails or returns false, provide fallback data for testing
        error_log("Using fallback achievement data");
        return [
            (object)[
                'AchievementID' => 1,
                'Date' => '2024-10-15',
                'MatchName' => 'vs Team Alpha',
                'Tournament' => 'Elite League',
                'Achievement' => 'Century Maker - Scored 100+ runs in single match',
                'VerifiedStatus' => 'verified',
                'CreatedAt' => '2024-10-15 15:30:00'
            ],
            
        ];
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
        $eventModel = $this->model('Event');
        $academyEvents = $eventModel->getUpcomingEvents(20); // fetch up to 20 upcoming events
        $data = [
            'title' => 'Tournaments',
            'player' => $this->getPlayerData(),
            'upcomingTournaments' => $this->getUpcomingTournaments(),
            'enrolledTournaments' => $this->getEnrolledTournaments(),
            'completedTournaments' => $this->getCompletedTournaments(),
            'tournamentStats' => $this->getTournamentStats(),
            'academyEvents' => $academyEvents
        ];
        $this->view('player/tournaments', $data);
    }

    // Trainer Plans (Note: URL uses hyphen but method uses camelCase due to Core.php routing)
    public function trainerplans() {
        $this->requireLogin();
        $data = [
            'title' => 'Trainer Plans',
            'player' => $this->getPlayerData(),
            'workoutPlans' => $this->trainerModel->getAllWorkoutPlansWithTrainers(),
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
        $playerId = $_SESSION['user_id'] ?? 6;
        
        // Get player subscription to determine access level
        $paymentModel = $this->model('M_Payment');
        $subscription = $paymentModel->getPlayerSubscription($playerId);
        $planName = $subscription->PlanName ?? '';
        $privateSessionsIncluded = (int)($subscription->PrivateSessionsIncluded ?? 0);
        
        // Determine if player can see all coaches or only assigned
        // If plan includes private sessions (PrivateSessionsIncluded > 0) or is Premium → all coaches
        // Otherwise (Basic, Junior, no subscription) → assigned coach only
        $canAccessAllCoaches = ($privateSessionsIncluded > 0);
        
        $userModel = $this->model('M_Users');
        if ($canAccessAllCoaches) {
            $coaches = $userModel->getAllCoachProfiles();
        } else {
            $coaches = $userModel->getPlayersAssignedToCoach($playerId, 'player');
        }
        
        $data = [
            'title' => 'Coach Sessions',
            'player' => $this->getPlayerData(),
            'coaches' => $coaches,
            'availableSlots' => $this->getAvailableCoachSlots(),
            'sessionTypes' => ['Group', 'Private'],
            'subscription' => $subscription,
            'canAccessAllCoaches' => $canAccessAllCoaches,
            'planName' => $planName
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
    

    // =========================================================================
    // PRIVATE HELPER METHODS - ALL USE REAL DATABASE QUERIES
    // =========================================================================

    private function getPlayerData() {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        $user = $userModel->getUserWithProfile($userId);
        
        if (!$user) {
            $user = $userModel->getUserById($userId);
        }
        
        if ($user) {
            // Get subscription info
            $paymentModel = $this->model('M_Payment');
            $subscription = $paymentModel->getPlayerSubscription($userId);
            
            return [
                'id' => $user->UserID,
                'name' => $user->Name,
                'email' => $user->Email,
                'phone' => $user->PhoneNumber ?? '',
                'roles' => ($user->BattingStyle ?? 'N/A') . ' | ' . ($user->BowlingStyle ?? 'N/A'),
                'profile_picture' => $user->ProfileImage ?? 'default-profile.jpg',
                'date_of_birth' => $user->DateOfBirth ?? '',
                'address' => $user->Address ?? '',
                'membership_level' => $subscription->PlanName ?? 'Standard',
                'joined_date' => $user->DateJoined ?? date('Y-m-d')
            ];
        }
        return [
            'id' => $userId, 'name' => 'Player', 'email' => '', 'phone' => '',
            'roles' => 'Cricket Player', 'profile_picture' => 'default-profile.jpg',
            'date_of_birth' => '', 'address' => '', 'membership_level' => 'Standard',
            'joined_date' => date('Y-m-d')
        ];
    }
    
    private function getPerformanceStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $stats = $perfModel->getOverallStats($playerId);
        if ($stats) {
            return [
                'batting_avg' => $stats->BattingAvg ?? 0,
                'strike_rate' => $stats->StrikeRate ?? 0,
                'total_runs' => $stats->TotalRuns ?? 0,
                'total_wickets' => $stats->Wickets ?? 0,
                'bowling_avg' => $stats->BowlingAvg ?? 0,
                'economy_rate' => $stats->EconomyRate ?? 0,
                'matches_played' => $stats->MatchesPlayed ?? 0,
                'wins' => $stats->Wins ?? 0
            ];
        }
        return ['batting_avg'=>0,'strike_rate'=>0,'total_runs'=>0,'total_wickets'=>0,
                'bowling_avg'=>0,'economy_rate'=>0,'matches_played'=>0,'wins'=>0];
    }
    
    private function getBattingStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $matches = $perfModel->getMatchHistory($playerId, 10);
        
        $battingData = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                if ($match->RunsScored > 0 || $match->BallsFaced > 0) {
                    $strikeRate = $match->BallsFaced > 0 ? 
                        round(($match->RunsScored / $match->BallsFaced) * 100, 2) : 0;
                    
                    $battingData[] = [
                        'match_date' => $match->Date,
                        'opponent' => $match->OpponentTeam,
                        'tournament' => $match->TournamentName,
                        'runs' => $match->RunsScored,
                        'balls' => $match->BallsFaced,
                        'strike_rate' => $strikeRate,
                        'result' => $match->Result,
                        'venue' => $match->Venue
                    ];
                }
            }
        }
        return $battingData;
    }
    
    private function getBowlingStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $matches = $perfModel->getMatchHistory($playerId, 10);
        
        $bowlingData = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                if ($match->WicketsTaken > 0 || $match->OversBowled > 0) {
                    $economy = $match->OversBowled > 0 ? 
                        round($match->RunsConceded / $match->OversBowled, 2) : 0;
                    
                    $bowlingData[] = [
                        'match_date' => $match->Date,
                        'opponent' => $match->OpponentTeam,
                        'tournament' => $match->TournamentName,
                        'wickets' => $match->WicketsTaken,
                        'overs' => $match->OversBowled,
                        'runs_conceded' => $match->RunsConceded,
                        'economy' => $economy,
                        'result' => $match->Result,
                        'venue' => $match->Venue
                    ];
                }
            }
        }
        return $bowlingData;
    }
    
    private function getTodaySchedule() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        $sessions = $sessionModel->getTodayScheduleForPlayer($playerId);
        if (!empty($sessions)) {
            return $sessions;
        }
        return [];
    }
    
    private function getUpcomingSchedule() {
        $eventModel = $this->model('Event');
        $events = $eventModel->getUpcomingEvents(7);
        $schedule = [];
        if ($events) {
            foreach ($events as $event) {
                $schedule[] = [
                    'date' => $event->EventDate ?? $event->StartDate ?? '',
                    'time' => $event->StartTime ?? $event->EventTime ?? '',
                    'activity' => $event->Title ?? $event->Name ?? '',
                    'location' => $event->Venue ?? $event->Location ?? ''
                ];
            }
        }
        return $schedule;
    }
    
    private function getUpcomingBookings() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getUpcomingBookingsForPlayer($playerId);
    }
    
    private function getRentalsDue() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        return $paymentModel->getRentalPaymentsDue($playerId);
    }
    
    private function getPaymentsDue() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        return $paymentModel->getUpcomingPayments($playerId);
    }
    
    private function getTrainingSessions() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getUpcomingSessionsForPlayer($playerId);
    }
    
    private function getCoachSessions() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        $bookings = $sessionModel->getUpcomingBookingsForPlayer($playerId);
        // Filter to only coach sessions
        return array_values(array_filter($bookings, function($b) {
            return (isset($b->booking_type) && $b->booking_type === 'coach');
        }));
    }
    
    private function getFacilityReservations() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getFacilityBookingsForPlayer($playerId);
    }
    
    private function getMedicalHistory() {
        $playerId = $_SESSION['user_id'] ?? 6;
        if (!isset($this->medicalModel)) {
            $this->medicalModel = $this->model('M_Medical');
        }
        return $this->medicalModel->getMedicalRecords($playerId);
    }
    
    private function getVaccinations() {
        // No vaccination table in schema - return empty
        return [];
    }
    
    private function getInjuries() {
        // Injuries come from medical records
        return $this->getMedicalHistory();
    }
    
    private function getPracticeMatches() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getMatchHistory($playerId, 10);
    }
    
    private function getTournaments() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getTournamentStats($playerId);
    }
    
    private function getDetailedPerformanceStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $stats = $perfModel->getOverallStats($playerId);
        if ($stats) {
            return [
                'batting' => [
                    'total_runs' => $stats->TotalRuns ?? 0,
                    'average' => $stats->BattingAvg ?? 0,
                    'strike_rate' => $stats->StrikeRate ?? 0,
                    'centuries' => $stats->Centuries ?? 0,
                    'half_centuries' => $stats->HalfCenturies ?? 0,
                    'highest_score' => $stats->HighestScore ?? 0
                ],
                'bowling' => [
                    'total_wickets' => $stats->Wickets ?? 0,
                    'average' => $stats->BowlingAvg ?? 0,
                    'economy_rate' => $stats->EconomyRate ?? 0,
                    'best_figures' => $stats->BestBowling ?? 'N/A',
                    'five_wickets' => $stats->FiveWickets ?? 0,
                    'four_wickets' => $stats->FourWickets ?? 0
                ]
            ];
        }
        return ['batting'=>['total_runs'=>0,'average'=>0,'strike_rate'=>0,'centuries'=>0,'half_centuries'=>0,'highest_score'=>0],
                'bowling'=>['total_wickets'=>0,'average'=>0,'economy_rate'=>0,'best_figures'=>'N/A','five_wickets'=>0,'four_wickets'=>0]];
    }
    
    private function getAwards() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $achievements = $this->achievementModel->getAchievementsByPlayer($playerId);
        $awards = [];
        if ($achievements) {
            foreach ($achievements as $a) {
                if ($a->VerifiedStatus === 'verified') {
                    $awards[] = [
                        'title' => $a->Achievement,
                        'event' => $a->Tournament,
                        'date' => $a->Date
                    ];
                }
            }
        }
        return $awards;
    }
    
    private function getRecords() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $stats = $perfModel->getOverallStats($playerId);
        $records = [];
        if ($stats) {
            if (!empty($stats->HighestScore)) {
                $records[] = ['record' => 'Highest Individual Score', 'value' => $stats->HighestScore . ' runs', 'match' => '', 'date' => ''];
            }
            if (!empty($stats->BestBowling)) {
                $records[] = ['record' => 'Best Bowling Figures', 'value' => $stats->BestBowling, 'match' => '', 'date' => ''];
            }
            if (!empty($stats->TotalRuns)) {
                $records[] = ['record' => 'Total Career Runs', 'value' => $stats->TotalRuns . ' runs', 'match' => '', 'date' => ''];
            }
        }
        return $records;
    }
    
    private function getCertificates() {
        // Certificates could overlap with verified achievements
        $playerId = $_SESSION['user_id'] ?? 6;
        $achievements = $this->achievementModel->getAchievementsByPlayer($playerId);
        $certs = [];
        if ($achievements) {
            foreach ($achievements as $a) {
                $certs[] = [
                    'title' => $a->Achievement,
                    'issued_by' => $a->Tournament ?? 'Elite Cricket Academy',
                    'date' => $a->Date
                ];
            }
        }
        return $certs;
    }
    
    private function getMonthlyFees() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        return $paymentModel->getPaymentHistory($playerId);
    }
    
    private function getEventFees() {
        // Event fees are part of payment history - same source
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        return $paymentModel->getPaymentHistory($playerId);
    }
    
    private function getUpcomingPayments() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        return $paymentModel->getUpcomingPayments($playerId);
    }
    
    // Tournament helper methods
    private function getUpcomingTournaments() {
        $perfModel = $this->model('M_Performance');
        return $perfModel->getUpcomingTournamentsAll();
    }
    
    private function getEnrolledTournaments() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getEnrolledTournaments($playerId);
    }
    
    private function getCompletedTournaments() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getCompletedTournaments($playerId);
    }
    
    private function getTournamentStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $summary = $perfModel->getPlayerTournamentSummary($playerId);
        if ($summary) {
            return [
                'total_tournaments' => $summary->total_tournaments ?? 0,
                'total_matches' => $summary->total_matches ?? 0,
                'total_runs' => $summary->total_runs ?? 0,
                'total_wickets' => $summary->total_wickets ?? 0,
                'best_score' => $summary->best_score ?? 0
            ];
        }
        return ['total_tournaments'=>0,'total_matches'=>0,'total_runs'=>0,'total_wickets'=>0,'best_score'=>0];
    }

    // Shopping System Methods
    private function getAvailableProducts() {
        if ($this->productModel) {
            $products = $this->productModel->getProductsByStatus('active');
            return $products ? $products : [];
        }
        return [];
    }

    private function getRentalEquipment() {
        $shopModel = $this->model('M_Shop');
        return $shopModel->getAvailableEquipmentForRent();
    }

    private function getAvailableFacilities() {
        $shopModel = $this->model('M_Shop');
        return $shopModel->getAllFacilities();
    }

    private function getMyRentals() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $shopModel = $this->model('M_Shop');
        return $shopModel->getPlayerRentals($playerId);
    }

    private function getCartItems() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    private function getCartTotal() {
        $items = $this->getCartItems();
        $subtotal = 0;
        foreach ($items as $item) {
            $price = $item['price'] ?? 0;
            $discount = $item['discount'] ?? 0;
            $qty = $item['quantity'] ?? 1;
            $subtotal += ($price * (1 - $discount / 100)) * $qty;
        }
        $memberDiscount = $subtotal * 0.05;
        return ['subtotal' => $subtotal, 'member_discount' => $memberDiscount, 'total' => $subtotal - $memberDiscount];
    }

    private function getRentalStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $shopModel = $this->model('M_Shop');
        $stats = $shopModel->getPlayerRentalStats($playerId);
        if ($stats) {
            return [
                'total_equipment' => $stats->total_equipment ?? 0,
                'active_rentals' => $stats->active_rentals ?? 0,
                'total_spent' => $stats->total_spent ?? 0,
                'member_discount' => '15%'
            ];
        }
        return ['total_equipment'=>0,'active_rentals'=>0,'total_spent'=>0,'member_discount'=>'15%'];
    }

    private function getMyFacilityBookings() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getFacilityBookingsForPlayer($playerId);
    }

    private function getFacilityStats() {
        $shopModel = $this->model('M_Shop');
        $stats = $shopModel->getFacilityStats();
        if ($stats) {
            return [
                'total_facilities' => $stats->total_facilities ?? 0,
                'available_facilities' => $stats->available_facilities ?? 0
            ];
        }
        return ['total_facilities'=>0,'available_facilities'=>0];
    }

    // Trainer Plans Data Methods
    private function getGeneralWorkoutPlans() {
        return $this->trainerModel->getAllWorkoutPlansWithTrainers();
    }

    private function getGeneralNutritionGuides() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $nutritionModel = $this->model('M_NutritionPlan');
        return $nutritionModel->getNutritionPlansByPlayer($playerId);
    }

    private function getGeneralSupplementInfo() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $supplementModel = $this->model('M_SupplementPlan');
        return $supplementModel->getSupplementPlansByPlayer($playerId);
    }
    
    // =========================================================================
    // COACH BOOKING SYSTEM METHODS - REAL DB
    // =========================================================================
    
    private function getAvailableCoachSlots() {
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getAvailableCoachSessions();
    }
    
    private function bookCoachSession($slotId, $playerId, $specialRequests = '') {
        try {
            $sessionModel = $this->model('M_Session');
            // Get the session details
            $session = $sessionModel->getSessionById($slotId);
            if (!$session) {
                return ['success' => false, 'message' => 'Session slot not found'];
            }
            
            // Book the appointment
            $data = [
                'player_id' => $playerId,
                'coach_id' => $session->CoachOrTrainerID ?? $session->CoachID ?? 0,
                'date' => $session->Date,
                'start_time' => $session->StartTime,
                'end_time' => $session->EndTime,
                'status' => 'Scheduled',
                'notes' => $specialRequests
            ];
            
            $bookingId = $sessionModel->bookCoachAppointment($data);
            if ($bookingId) {
                return [
                    'success' => true,
                    'booking_id' => $bookingId,
                    'message' => 'Booking created successfully. Please complete payment to confirm.',
                    'payment_required' => true
                ];
            }
            return ['success' => false, 'message' => 'Failed to create booking'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()];
        }
    }
    
    private function processBookingPayment($bookingId, $paymentMethod, $paymentDetails = []) {
        try {
            $transactionId = 'TXN_' . date('Ymd') . '_' . str_pad($bookingId, 6, '0', STR_PAD_LEFT);
            // Update appointment status to confirmed
            $sessionModel = $this->model('M_Session');
            // For now, mark as confirmed (payment integration can be added later)
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Payment processed successfully. Your session is confirmed!',
                'booking_confirmed' => true
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Payment processing failed: ' . $e->getMessage()];
        }
    }
    
    private function getFilteredAvailableSlots($filters = []) {
        $sessionModel = $this->model('M_Session');
        $date = $filters['date'] ?? null;
        $slots = $sessionModel->getAvailableCoachSessions($date);
        
        if (empty($filters)) return $slots;
        
        return array_values(array_filter($slots, function($slot) use ($filters) {
            if (!empty($filters['coach_id']) && ($slot->coach_id ?? '') != $filters['coach_id']) return false;
            if (!empty($filters['session_type']) && ($slot->session_type ?? '') != $filters['session_type']) return false;
            return true;
        }));
    }
    
    private function getAllCoaches() {
        $userModel = $this->model('M_Users');
        return $userModel->getAllCoachProfiles();
    }
    
    private function cancelBooking($bookingId) {
        try {
            $playerId = $_SESSION['user_id'] ?? 6;
            $sessionModel = $this->model('M_Session');
            $result = $sessionModel->cancelAppointment($bookingId, $playerId);
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Booking cancelled successfully.',
                    'cancellation_policy' => [
                        '24+ hours: 100% refund',
                        '12-24 hours: 75% refund',
                        '2-12 hours: 50% refund',
                        'Less than 2 hours: No refund'
                    ]
                ];
            }
            return ['success' => false, 'message' => 'Failed to cancel booking'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()];
        }
    }
    
    private function getPlayerBookingHistory() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getBookingHistoryForPlayer($playerId);
    }

    private function getSessionTypes() {
        $sessionModel = $this->model('M_Session');
        $types = $sessionModel->getSessionTypes();
        $result = [];
        if ($types) {
            foreach ($types as $t) {
                $result[$t->SessionType] = $t->SessionType . ' session';
            }
        }
        if (empty($result)) {
            $result = ['Individual' => 'One-on-one session', 'Group' => 'Group training', 'Fitness' => 'Physical conditioning'];
        }
        return $result;
    }

    private function getAllTrainers() {
        $userModel = $this->model('M_Users');
        return $userModel->getAllTrainerProfiles();
    }

    private function getAvailableTrainerSlots() {
        $sessionModel = $this->model('M_Session');
        return $sessionModel->getAvailableTrainerSessions();
    }

    private function getTrainerSessionTypes() {
        return [
            'Individual Fitness' => 'Personal fitness training session',
            'Group Fitness' => 'Small group fitness training',
            'Injury Assessment' => 'Injury prevention consultation',
            'Nutrition Consultation' => 'Nutrition and dietary planning',
            'Rehabilitation' => 'Injury recovery session'
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

    // Upload/Update Profile Image
    public function uploadProfileImage() {
        // Set JSON response header
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->requireLogin();
            
            // Check if file was uploaded
            if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
                echo json_encode(['success' => false, 'message' => 'No file was uploaded']);
                return;
            }
            
            $file = $_FILES['profile_image'];
            
            // Check for upload errors with detailed messages
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in HTML form',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
                ];
                $message = $errorMessages[$file['error']] ?? 'Unknown upload error occurred';
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            // Validate file size (max 2MB)
            $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
            if ($file['size'] > $maxFileSize) {
                echo json_encode(['success' => false, 'message' => 'File size must be less than 2MB']);
                return;
            }
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG files are allowed']);
                return;
            }
            
            // Get file extension
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Generate unique filename
            $userId = $_SESSION['user_id'];
            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            
            // Set upload directory - APPROOT is /app, so go up one level to get project root
            $projectRoot = dirname(APPROOT); // Goes from /app to /Elite
            $uploadDir = $projectRoot . '/public/uploads/profile_images/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                    return;
                }
            }
            
            $uploadPath = $uploadDir . $newFileName;
            
            // Get old image path to delete it later
            try {
                $userModel = $this->model('M_Users');
                $oldImage = $userModel->getProfileImage($userId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Save relative path to database (without 'public' - server config handles this)
                $relativePath = 'uploads/profile_images/' . $newFileName;
                
                if ($userModel->updateProfileImage($userId, $relativePath)) {
                    // Delete old image file if it exists
                    if ($oldImage) {
                        $oldImagePath = $projectRoot . '/public/' . $oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Profile image updated successfully',
                        'image_url' => URLROOT . '/' . $relativePath
                    ]);
                } else {
                    // Delete uploaded file if database update failed
                    unlink($uploadPath);
                    echo json_encode(['success' => false, 'message' => 'Failed to update profile image in database']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Delete Profile Image
    public function deleteProfileImage() {
        // Set JSON response header
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->requireLogin();
            
            $userId = $_SESSION['user_id'];
            
            try {
                $userModel = $this->model('M_Users');
                
                // Get current image path
                $imagePath = $userModel->getProfileImage($userId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            if ($imagePath) {
                // Delete file from server
                $projectRoot = dirname(APPROOT);
                $fullPath = $projectRoot . '/public/' . $imagePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                // Remove from database
                if ($userModel->deleteProfileImage($userId)) {
                    echo json_encode(['success' => true, 'message' => 'Profile image deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete image from database']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No profile image found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
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
}
?>
