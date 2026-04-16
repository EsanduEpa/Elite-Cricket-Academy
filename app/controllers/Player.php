<?php
class Player extends Controller {
    
    private $userModel;
    private $medicalModel;
    private $achievementModel;
    private $productModel;
    private $trainerModel;
    private $slotPlayerModel;
    
    public function __construct() {
        // PayHere server-to-server callbacks cannot carry a player browser session.
        $urlParts = isset($_GET['url']) ? explode('/', trim((string)$_GET['url'], '/')) : [];
        $currentMethod = $urlParts[1] ?? 'index';
        if ($currentMethod !== 'payhere_notify') {
            requireAuth(['Player']);
        }

        // Enable database for achievement functionality
        $this->userModel = $this->model('M_Users');
        // $this->medicalModel = $this->model('M_Medical');
        $this->achievementModel = $this->model('M_Achievement');
        $this->productModel = $this->model('M_Product');
        $this->trainerModel = $this->model('M_Trainer');
        $this->slotPlayerModel = $this->model('M_SlotPlayer');
        require_once APPROOT . '/libraries/SlotBookingService.php';
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
        $coachSessions = [];
        if (isset($playerData['id'])) {
            $coachSessions = $this->slotPlayerModel->getUpcomingCoachSessions((int)$playerData['id']);
        }

        $data = [
            'title' => 'Player Dashboard - ' . $playerData['name'],
            'player' => $playerData,
            'todaySchedule' => $this->getTodaySchedule(),
            'upcomingSchedule' => $this->getUpcomingSchedule(),
            'upcomingBookings' => $this->getUpcomingBookings(),
            'calendarEvents' => $this->buildDashboardCalendarEvents(),
            'rentalsDue' => $this->getRentalsDue(),
            'paymentsDue' => $this->getPaymentsDue(),
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
            'calendarEvents' => $this->buildDashboardCalendarEvents(),
            'rentalsDue' => $this->getRentalsDue(),
            'paymentsDue' => $this->getPaymentsDue(),
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
        $playerId     = (int)$_SESSION['user_id'];
        $data = [
            'title'            => 'Training Schedule',
            'player'           => $this->getPlayerData(),
            'todaySessions'    => $this->getTodaySchedule(),
            'upcomingSessions' => $this->slotPlayerModel->getUpcomingScheduleSessions($playerId),
        ];
        $this->view('player/training', $data);
    }

    // Calendar View
    public function calendar() {
        $this->requireLogin();
        $playerId     = (int)$_SESSION['user_id'];
        $bookings     = $this->slotPlayerModel->getUpcomingBookingFeed($playerId);

        // Format for FullCalendar JSON
        $events = [];
        $typeColors = [
            'coach'   => '#4A90E2',
            'trainer' => '#27ae60',
            'session' => '#9b59b6',
            'program' => '#7c3aed',
            'facility'=> '#e67e22',
        ];
        foreach ($bookings as $b) {
            $color = $typeColors[$b->booking_type] ?? '#7f8c8d';
            $title = $b->reason ?: ucfirst($b->booking_type) . ' Session';
            if (!empty($b->practitioner_name)) {
                $title .= ' — ' . $b->practitioner_name;
            }
            $events[] = [
                'id'    => $b->booking_type . '_' . $b->id,
                'title' => $title,
                'start' => $b->date . 'T' . $b->StartTime,
                'end'   => $b->date . 'T' . $b->EndTime,
                'color' => $color,
                'extendedProps' => [
                    'type'   => $b->booking_type,
                    'status' => $b->Status,
                ],
            ];
        }
        $data = [
            'title'  => 'My Session Calendar',
            'player' => $this->getPlayerData(),
            'events' => $events,
        ];
        $this->view('player/calendar', $data);
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
                $uploadDir = APPROOT . '/public/uploads/medical_receipts/';
                
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
                'body_area' => trim($_POST['body_area'] ?? ''),
                'diagnosis' => trim($_POST['diagnosis'] ?? ''),
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
            if (empty($data['body_area'])) {
                $errors[] = 'Body area is required';
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
            
            // Validate body area - must be a valid ENUM value
            $validBodyAreas = ['Head/Face', 'Neck', 'Shoulder', 'Arm/Elbow', 'Hand/Wrist',
                               'Chest/Back', 'Hip/Groin', 'Thigh', 'Knee', 'Lower Leg', 'Ankle/Foot'];
            if (!in_array($data['body_area'], $validBodyAreas)) {
                $errors[] = 'Please select a valid body area';
            }

            // Validate diagnosis - must be a valid ENUM value
            $validDiagnoses = ['Sprain', 'Strain', 'Fracture', 'Dislocation',
                               'Concussion', 'Tear', 'Laceration', 'Overuse/Inflammation', 'Illness'];
            if (!in_array($data['diagnosis'], $validDiagnoses)) {
                $errors[] = 'Please select a valid diagnosis';
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
                'recovery_status' => $recoveryStatus,
                'reported_date' => $existingRecord->ReportedDate
            ];

            // Validate recovery status
            $validStatuses = ['ongoing', 'recovering', 'fully_recovered', 'chronic_condition'];
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

    // Full update of a medical record (only allowed when verifyStatus is pending)
    public function fullUpdateMedicalRecord() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('player/medical');
            return;
        }

        if (!isset($this->medicalModel)) {
            $this->medicalModel = $this->model('M_Medical');
        }

        $playerData = $this->getPlayerData();
        $playerId   = $playerData['id'];
        $recordId   = intval($_POST['record_id'] ?? 0);

        $existingRecord = $this->medicalModel->getMedicalRecord($recordId);

        if (!$existingRecord || $existingRecord->PlayerID != $playerId) {
            flash('medical_message', 'Record not found or access denied', 'alert alert-danger');
            redirect('player/medical');
            return;
        }

        // Only allow full edit when the record is still pending
        if (strtolower($existingRecord->verifyStatus ?? 'pending') !== 'pending') {
            flash('medical_message', 'This record has already been reviewed and cannot be fully edited', 'alert alert-danger');
            redirect('player/medical');
            return;
        }

        $validStatuses = ['ongoing', 'recovering', 'fully_recovered', 'chronic_condition'];
        $recoveryStatus = $_POST['recovery_status'] ?? '';
        if (!in_array($recoveryStatus, $validStatuses)) {
            flash('medical_message', 'Invalid recovery status selected', 'alert alert-danger');
            redirect('player/medical');
            return;
        }

        $updateData = [
            'body_area'           => trim($_POST['body_area']          ?? ''),
            'diagnosis'           => trim($_POST['diagnosis']           ?? ''),
            'treatment_given'     => trim($_POST['treatment_given']     ?? ''),
            'recovery_status'     => $recoveryStatus,
            'injury_date'         => $_POST['injury_date']              ?? '',
            'happened_at_academy' => $_POST['happened_at_academy']      ?? 'no',
            'rest_days_needed'    => intval($_POST['rest_days_needed']  ?? 0),
            'reported_date'       => $_POST['reported_date']            ?? date('Y-m-d'),
        ];

        if (empty($updateData['body_area']) || empty($updateData['diagnosis'])) {
            flash('medical_message', 'Body area and diagnosis are required', 'alert alert-danger');
            redirect('player/medical');
            return;
        }

        $success = $this->medicalModel->fullUpdateMedicalRecord($recordId, $updateData);

        if ($success) {
            flash('medical_message', 'Medical record updated successfully');
        } else {
            flash('medical_message', 'Failed to update medical record', 'alert alert-danger');
        }
        redirect('player/medical');
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
    
    // Payment History
    public function payments() {
        $recentPayments = $this->formatRecentSubscriptionPayments($this->getMonthlyFees());
        $upcomingPayments = $this->formatUpcomingSubscriptionPayments($this->getUpcomingPayments());

        $data = [
            'title' => 'Payment History',
            'player' => $this->getPlayerData(),
            'monthlyFees' => $this->getMonthlyFees(),
            'eventFees' => $this->getEventFees(),
            'upcomingPayments' => $this->getUpcomingPayments(),
            'recent_payments' => $recentPayments,
            'upcoming_payments' => $upcomingPayments,
            'payment_methods' => $this->getPaymentMethodsList(),
        ];
        $this->view('player/payments', $data);
    }
    
    // Tournaments
    public function tournaments() {
        requireAuth(['Player']);
        $M_Tournament  = $this->model('M_Tournament');
        $M_JoinRequest = $this->model('M_TournamentJoinRequest');
        $playerId      = $_SESSION['user_id'];
        $playerData    = $this->getPlayerData();

        $tournaments = $M_Tournament->getPublicTournaments();
        $myRequests  = [];
        $eligibility = [];
        foreach ($tournaments as $t) {
            $req = $M_JoinRequest->getRequestByPlayer($t->TournamentID, $playerId);
            if ($req) {
                $myRequests[$t->TournamentID] = $req;
            }

            $eligibility[$t->TournamentID] = $this->getTournamentEligibility($playerData, $t);
        }

        $data = [
            'title'       => 'Tournaments',
            'player'      => $playerData,
            'tournaments' => $tournaments,
            'my_requests' => $myRequests,
            'eligibility' => $eligibility,
        ];
        $this->view('player/tournaments/index', $data);
    }

    public function tournament_detail($id) {
        requireAuth(['Player']);
        $id            = (int)$id;
        $M_Tournament  = $this->model('M_Tournament');
        $M_JoinRequest = $this->model('M_TournamentJoinRequest');
        $playerId      = $_SESSION['user_id'];
        $playerData    = $this->getPlayerData();

        $tournament = $M_Tournament->getTournamentById($id);
        if (!$tournament) {
            flash('tournament_message', 'Tournament not found.', 'alert alert-danger');
            redirect('player/tournaments');
        }

        $data = [
            'title'      => $tournament->Name,
            'player'     => $playerData,
            'tournament' => $tournament,
            'team'       => $tournament->IsTeamAnnounced ? $M_Tournament->getTeam($id) : [],
            'my_request' => $M_JoinRequest->getRequestByPlayer($id, $playerId),
            'eligibility'=> $this->getTournamentEligibility($playerData, $tournament),
        ];
        $this->view('player/tournaments/detail', $data);
    }

    public function join_tournament($id) {
        requireAuth(['Player']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('player/tournaments');
        }
        $id            = (int)$id;
        $M_Tournament  = $this->model('M_Tournament');
        $M_JoinRequest = $this->model('M_TournamentJoinRequest');
        $playerId      = $_SESSION['user_id'];
        $playerData    = $this->getPlayerData();

        $tournament = $M_Tournament->getTournamentById($id);
        if (!$tournament) {
            flash('tournament_message', 'Tournament not found.', 'alert alert-danger');
            redirect('player/tournaments');
        }

        if ($tournament->Status !== 'registration_open') {
            flash('tournament_message', 'Registration is not currently open for this tournament.', 'alert alert-warning');
            redirect('player/tournament_detail/' . $id);
        }

        $eligibility = $this->getTournamentEligibility($playerData, $tournament);
        if (!$eligibility['eligible']) {
            flash('tournament_message', $eligibility['message'], 'alert alert-danger');
            redirect('player/tournament_detail/' . $id);
        }

        if ($M_JoinRequest->hasExistingRequest($id, $playerId)) {
            flash('tournament_message', 'You have already submitted a join request for this tournament.', 'alert alert-warning');
            redirect('player/tournament_detail/' . $id);
        }

        $message = trim($_POST['message'] ?? '');
        $M_JoinRequest->createRequest($id, $playerId, $message);
        flash('tournament_message', 'Your join request has been submitted successfully.', 'alert alert-success');
        redirect('player/tournament_detail/' . $id);
    }

    public function cancel_join_request($id) {
        requireAuth(['Player']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('player/tournaments');
        }
        $requestId     = (int)$id;
        $M_JoinRequest = $this->model('M_TournamentJoinRequest');
        $M_JoinRequest->cancelByPlayer($requestId, $_SESSION['user_id']);
        flash('tournament_message', 'Your join request has been cancelled.', 'alert alert-info');
        redirect('player/tournaments');
    }

    private function getTournamentEligibility(array $playerData, $tournament): array {
        $playerAge = $this->getPlayerAge($playerData['date_of_birth'] ?? '');
        $tournamentAgeGroup = trim((string)($tournament->AgeGroup ?? 'Open'));

        if (empty($playerData['date_of_birth']) || $playerAge === null) {
            return [
                'eligible' => false,
                'message' => 'Your date of birth is missing or invalid. Update your profile before applying for age-group tournaments.',
                'player_age' => null,
                'player_age_group' => 'Unknown',
                'tournament_age_group' => $tournamentAgeGroup,
            ];
        }

        $playerAgeGroup = $this->userModel->getAgeGroupForDateOfBirth((string)$playerData['date_of_birth']);

        if ($tournamentAgeGroup === '' || strcasecmp($tournamentAgeGroup, 'Open') === 0) {
            return [
                'eligible' => true,
                'message' => 'You are eligible for this open tournament.',
                'player_age' => $playerAge,
                'player_age_group' => $playerAgeGroup,
                'tournament_age_group' => 'Open',
            ];
        }

        if (preg_match('/under\s*(\d+)/i', $tournamentAgeGroup, $matches)) {
            $ageLimit = (int)$matches[1];
            $eligible = $playerAge !== null && $playerAge < $ageLimit;

            return [
                'eligible' => $eligible,
                'message' => $eligible
                    ? 'Eligible: your age matches the ' . $tournamentAgeGroup . ' requirement.'
                    : 'Not eligible: this tournament is for ' . $tournamentAgeGroup . ' players, and you do not meet that age requirement.',
                'player_age' => $playerAge,
                'player_age_group' => $playerAgeGroup,
                'tournament_age_group' => $tournamentAgeGroup,
            ];
        }

        $eligible = strcasecmp($playerAgeGroup, $tournamentAgeGroup) === 0;

        return [
            'eligible' => $eligible,
            'message' => $eligible
                ? 'Eligible: your age group matches this tournament.'
                : 'Not eligible: your age group does not match this tournament.',
            'player_age' => $playerAge,
            'player_age_group' => $playerAgeGroup,
            'tournament_age_group' => $tournamentAgeGroup,
        ];
    }

    private function getPlayerAge(string $dateOfBirth): ?int {
        if ($dateOfBirth === '') {
            return null;
        }

        try {
            $birthDate = new DateTime($dateOfBirth);
            return (new DateTime())->diff($birthDate)->y;
        } catch (Exception $e) {
            return null;
        }
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

    public function confirm_rental() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('player/rentals');
        }

        $playerData = $this->getPlayerData();
        $playerId = (int)($playerData['id'] ?? 0);
        $equipmentId = (int)($_POST['equipment_id'] ?? 0);
        $startDate = trim((string)($_POST['start_date'] ?? ''));
        $duration = (int)($_POST['duration'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $pickupMethod = trim((string)($_POST['pickup_method'] ?? 'pickup'));
        $allowedDurations = [1, 3, 7, 14, 30];

        if ($playerId <= 0 || $equipmentId <= 0) {
            flash('rental_message', 'Invalid rental request. Please choose equipment again.', 'alert alert-danger');
            redirect('player/rentals');
        }

        if (!in_array($duration, $allowedDurations, true)) {
            flash('rental_message', 'Invalid rental duration selected.', 'alert alert-danger');
            redirect('player/rentals');
        }

        if ($quantity < 1) {
            flash('rental_message', 'Please select at least one item.', 'alert alert-danger');
            redirect('player/rentals');
        }

        if (!in_array($pickupMethod, ['pickup', 'delivery'], true)) {
            flash('rental_message', 'Invalid pickup method selected.', 'alert alert-danger');
            redirect('player/rentals');
        }

        if ($startDate === '') {
            flash('rental_message', 'Please choose a valid rental start date.', 'alert alert-danger');
            redirect('player/rentals');
        }

        try {
            $start = new DateTime($startDate);
            $today = new DateTime('today');
            if ($start < $today) {
                flash('rental_message', 'Please choose today or a future date for your rental.', 'alert alert-danger');
                redirect('player/rentals');
            }
        } catch (Exception $e) {
            flash('rental_message', 'Please choose a valid rental start date.', 'alert alert-danger');
            redirect('player/rentals');
        }

        $shopModel = $this->model('M_Shop');
        $equipment = $shopModel->getEquipmentById($equipmentId);
        if (!$equipment) {
            flash('rental_message', 'Selected equipment was not found. Please choose another item.', 'alert alert-danger');
            redirect('player/rentals');
        }

        $availableStock = (int)($equipment->Stock ?? 0);
        $availability = strtolower((string)($equipment->AvailabilityStatus ?? ''));
        if ($availability !== 'available' || $availableStock < $quantity) {
            flash('rental_message', 'Only ' . max(0, $availableStock) . ' item(s) are available now.', 'alert alert-danger');
            redirect('player/rentals');
        }

        $totalCost = $shopModel->calculateRentalTotal(
            (float)($equipment->RentalPrice ?? 0),
            $duration,
            $quantity,
            $pickupMethod
        );
        $amount = number_format($totalCost, 2, '.', '');
        if ((float)$amount <= 0) {
            flash('rental_message', 'This equipment has no valid rental price. Please contact the academy.', 'alert alert-danger');
            redirect('player/rentals');
        }

        require_once APPROOT . '/libraries/PayHere.php';

        $currency = 'LKR';
        $orderId = 'ELITE-RENT-' . $playerId . '-' . $equipmentId . '-' . time();
        $itemsLabel = 'Equipment Rental - ' . (string)($equipment->Name ?? 'Equipment');
        $nameParts = explode(' ', trim($playerData['name'] ?? 'Player'), 2);

        $_SESSION['payhere_pending_order'] = $orderId;
        unset($_SESSION['payhere_pending_shop_payment']);
        unset($_SESSION['payhere_pending_facility_booking']);
        unset($_SESSION['payhere_pending_subscription_payment']);
        $_SESSION['payhere_pending_rental_payment'] = [
            'order_id' => $orderId,
            'equipment_id' => $equipmentId,
            'player_id' => $playerId,
            'start_date' => $startDate,
            'duration_days' => $duration,
            'quantity' => $quantity,
            'pickup_method' => $pickupMethod,
            'amount' => $amount,
            'currency' => $currency,
            'equipment_name' => (string)($equipment->Name ?? 'Equipment'),
        ];

        $data = [
            'title' => 'Redirecting to PayHere...',
            'player' => $playerData,
            'gateway' => [
                'merchant_id' => PayHere::MERCHANT_ID,
                'gateway_url' => PayHere::GATEWAY_URL,
                'order_id' => $orderId,
                'amount' => $amount,
                'currency' => $currency,
                'items' => $itemsLabel,
                'hash' => PayHere::buildHash($orderId, $amount, $currency),
                'return_url' => URLROOT . '/player/payhere_return?payment_type=rental',
                'cancel_url' => URLROOT . '/player/payhere_cancel?payment_type=rental',
                'notify_url' => URLROOT . '/player/payhere_notify',
                'first_name' => $nameParts[0] ?? 'Player',
                'last_name' => $nameParts[1] ?? '',
                'email' => $playerData['email'],
                'phone' => $playerData['phone'] ?: '0000000000',
                'address' => $playerData['address'] ?: 'N/A',
                'city' => 'Colombo',
                'country' => 'Sri Lanka',
            ],
        ];

        $this->view('player/payhere_gateway', $data);
    }

    // Facilities
    public function facilities() {
        $this->requireLogin();
        $data = [
            'title' => 'Facility Booking',
            'player' => $this->getPlayerData(),
            'facilities' => $this->getAvailableFacilities(),
            'myBookings' => $this->getMyFacilityBookings(),
            'facilityStats' => $this->getFacilityStats()
        ];
        $this->view('player/facilities', $data);
    }

    // AJAX: Book a facility slot
    public function book_facility() {
        $this->requireLogin();
        ob_start();
        header('Content-Type: application/json');

        $playerId   = (int)$_SESSION['user_id'];
        $facilityId = (int)($_POST['facility_id'] ?? 0);
        $date       = trim($_POST['date'] ?? '');
        $startTime  = trim($_POST['start_time'] ?? '');
        $duration   = (int)($_POST['duration'] ?? 0);  // hours

        if (!$facilityId || !$date || !$startTime || $duration < 1) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            ob_end_flush(); exit;
        }

        // Must be a future date
        if ($date < date('Y-m-d')) {
            echo json_encode(['success' => false, 'message' => 'Cannot book a past date']);
            ob_end_flush(); exit;
        }

        // Operating hours: 06:00 – 21:00
        $endTime = date('H:i:s', strtotime($startTime) + $duration * 3600);
        if ($startTime < '06:00:00' || $endTime > '21:00:00') {
            echo json_encode(['success' => false, 'message' => 'Booking must be within operating hours (6 AM – 9 PM)']);
            ob_end_flush(); exit;
        }

        // Max 2 hours per day per facility per player
        $hoursAlready = $this->slotPlayerModel->getPlayerDailyFacilityHours($playerId, $facilityId, $date);
        if (($hoursAlready + $duration) > 2) {
            $remaining = max(0, 2 - $hoursAlready);
            echo json_encode(['success' => false, 'message' => 'Maximum 2 hours per facility per day. You have ' . $remaining . 'h remaining today.']);
            ob_end_flush(); exit;
        }

        // No double-booking of the same facility slot
        if ($this->slotPlayerModel->facilityHasTimeConflict($facilityId, $date, $startTime, $endTime)) {
            echo json_encode(['success' => false, 'message' => 'This facility is already booked for the selected time slot. Please choose a different time.']);
            ob_end_flush(); exit;
        }

        // Calculate cost
        $shopModel = $this->model('M_Shop');
        $facility  = $shopModel->getFacilityById($facilityId);
        $hourlyRate = (float)($facility->HourlyRate ?? 0);
        $totalCost  = $hourlyRate * $duration;

        $id = $this->slotPlayerModel->bookFacility([
            'facility_id' => $facilityId,
            'player_id'   => $playerId,
            'date'        => $date,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'total_cost'  => $totalCost,
        ]);

        if ($id) {
            echo json_encode([
                'success'    => true,
                'message'    => 'Facility booked successfully!',
                'booking_id' => $id,
                'total_cost' => 'Rs. ' . number_format($totalCost, 2),
            ]);
        } elseif ($id === 'time_conflict') {
            echo json_encode(['success' => false, 'message' => 'You already have another booking at the same date and time.']);
        } elseif ($id === 'weekly_facility_limit') {
            echo json_encode(['success' => false, 'message' => 'You can book facilities at most 6 times per week.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
        }
        ob_end_flush(); exit;
    }

    // AJAX: Return unavailable time slots for a facility on a date
    public function facility_times() {
        $this->requireLogin();
        ob_start();
        header('Content-Type: application/json');

        $facilityId = (int)($_GET['facility_id'] ?? 0);
        $date       = trim($_GET['date'] ?? '');

        if (!$facilityId || !$date) {
            echo json_encode([]);
            ob_end_flush(); exit;
        }

        $rows = $this->slotPlayerModel->getUnavailableTimes($facilityId, $date);
        $result = [];
        foreach ($rows as $r) {
            $result[] = ['start' => $r->StartTime, 'end' => $r->EndTime];
        }
        echo json_encode($result);
        ob_end_flush(); exit;
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

    // Shopping Cart
    public function cart() {
        // Generate a nonce for cart → PayHere POST anti-CSRF
        if (empty($_SESSION['payhere_nonce'])) {
            $_SESSION['payhere_nonce'] = bin2hex(random_bytes(16));
        }
        $data = [
            'title'  => 'Shopping Cart',
            'player' => $this->getPlayerData(),
        ];
        $this->view('player/cart', $data);
    }

    // Checkout (formerly payment page)
    public function checkout() {
        $data = [
            'title' => 'Checkout',
            'player' => $this->getPlayerData()
        ];
        $this->view('player/checkout', $data);
    }

    // ── PayHere integration ────────────────────────────────

    /** POST /player/payhere_checkout — generate hash and auto-submit to PayHere */
    public function payhere_checkout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('player/cart');
        }

        require_once APPROOT . '/libraries/PayHere.php';

        $playerData = $this->getPlayerData();

        $orderId  = 'ELITE-' . $playerData['id'] . '-' . time();
        $currency = 'LKR';
        $amount   = number_format((float)($_POST['cart_total'] ?? 0), 2, '.', '');

        if ((float)$amount <= 0) {
            $_SESSION['cart_error'] = 'Invalid cart total. Please try again.';
            redirect('player/cart');
        }

        // Summarise items for the PayHere "items" field
        $rawItems  = [];
        $decoded   = json_decode($_POST['cart_items'] ?? '[]', true);
        if (is_array($decoded)) {
            foreach ($decoded as $it) {
                $name = htmlspecialchars($it['Name'] ?? $it['name'] ?? 'Item', ENT_QUOTES);
                $qty  = (int)($it['quantity'] ?? 1);
                $rawItems[] = "{$name} x{$qty}";
            }
        }
        $itemsLabel = $rawItems ? implode(', ', $rawItems) : 'Cricket Academy Purchase';

        $nameParts = explode(' ', trim($playerData['name']), 2);

        $_SESSION['payhere_pending_order'] = $orderId;
        unset($_SESSION['payhere_pending_facility_booking']);
        unset($_SESSION['payhere_pending_subscription_payment']);
        unset($_SESSION['payhere_pending_rental_payment']);
        $_SESSION['payhere_pending_shop_payment'] = [
            'order_id' => $orderId,
            'amount'   => $amount,
            'currency' => $currency,
            'items'    => $itemsLabel,
            'player_id' => (int)($playerData['id'] ?? 0),
            'recipient_email' => (string)($playerData['email'] ?? ''),
            'recipient_name' => (string)($playerData['name'] ?? 'Player'),
        ];
        $_SESSION['payhere_nonce']         = bin2hex(random_bytes(16));

        $data = [
            'title'   => 'Redirecting to PayHere...',
            'player'  => $playerData,
            'gateway' => [
                'merchant_id' => PayHere::MERCHANT_ID,
                'gateway_url' => PayHere::GATEWAY_URL,
                'order_id'    => $orderId,
                'amount'      => $amount,
                'currency'    => $currency,
                'items'       => $itemsLabel,
                'hash'        => PayHere::buildHash($orderId, $amount, $currency),
                'return_url'  => URLROOT . '/player/payhere_return?payment_type=shop',
                'cancel_url'  => URLROOT . '/player/payhere_cancel?payment_type=shop',
                'notify_url'  => URLROOT . '/player/payhere_notify',
                'first_name'  => $nameParts[0] ?? 'Player',
                'last_name'   => $nameParts[1] ?? '',
                'email'       => $playerData['email'],
                'phone'       => $playerData['phone'] ?: '0000000000',
                'address'     => $playerData['address'] ?: 'N/A',
                'city'        => 'Colombo',
                'country'     => 'Sri Lanka',
            ],
        ];
        $this->view('player/payhere_gateway', $data);
    }

    /** POST /player/facility_payhere_checkout — generate hash and auto-submit to PayHere */
    public function facility_payhere_checkout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/facilities');
        }

        require_once APPROOT . '/libraries/PayHere.php';

        $playerData = $this->getPlayerData();
        $playerId   = (int)($playerData['id'] ?? 0);
        $occurrenceId = (int)($_POST['occurrence_id'] ?? 0);

        if ($playerId <= 0 || $occurrenceId <= 0) {
            $_SESSION['slot_error'] = 'Invalid slot selected. Please try again.';
            redirect('playerslots/facilities');
        }

        $occurrence = null;
        foreach ($this->slotPlayerModel->getFacilityOccurrences($playerId) as $row) {
            if ((int)($row->OccurrenceID ?? 0) === $occurrenceId) {
                $occurrence = $row;
                break;
            }
        }

        if (!$occurrence || !empty($occurrence->blocked)) {
            $_SESSION['slot_error'] = 'Selected slot is no longer available. Please choose another one.';
            redirect('playerslots/facilities');
        }

        $amount   = number_format((float)($occurrence->PricePerSession ?? 0), 2, '.', '');
        $currency = 'LKR';

        if ((float)$amount <= 0) {
            $_SESSION['slot_error'] = 'This facility slot has no configured payment amount. Please contact admin to set the price before booking.';
            redirect('playerslots/facilities');
        }

        $nameParts = explode(' ', trim($playerData['name'] ?? 'Player'), 2);
        $orderId    = 'ELITE-FAC-' . $playerId . '-' . $occurrenceId . '-' . time();

        $_SESSION['payhere_pending_order'] = $orderId;
        unset($_SESSION['payhere_pending_shop_payment']);
        unset($_SESSION['payhere_pending_rental_payment']);
        $_SESSION['payhere_pending_facility_booking'] = [
            'order_id'      => $orderId,
            'occurrence_id'  => $occurrenceId,
            'player_id'     => $playerId,
            'amount'        => (float)$amount,
        ];

        $itemsLabel = trim((string)($occurrence->FacilityName ?? 'Facility Booking'));
        if (!empty($occurrence->SlotLabel)) {
            $itemsLabel .= ' - ' . $occurrence->SlotLabel;
        }

        $data = [
            'title'   => 'Redirecting to PayHere...',
            'player'  => $playerData,
            'gateway' => [
                'merchant_id' => PayHere::MERCHANT_ID,
                'gateway_url' => PayHere::GATEWAY_URL,
                'order_id'    => $orderId,
                'amount'      => $amount,
                'currency'    => $currency,
                'items'       => $itemsLabel,
                'hash'        => PayHere::buildHash($orderId, $amount, $currency),
                'return_url'  => URLROOT . '/player/payhere_return',
                'cancel_url'  => URLROOT . '/player/payhere_cancel',
                'notify_url'  => URLROOT . '/player/payhere_notify',
                'first_name'  => $nameParts[0] ?? 'Player',
                'last_name'   => $nameParts[1] ?? '',
                'email'       => $playerData['email'],
                'phone'       => $playerData['phone'] ?: '0000000000',
                'address'     => $playerData['address'] ?: 'N/A',
                'city'        => 'Colombo',
                'country'     => 'Sri Lanka',
            ],
        ];

        $this->view('player/payhere_gateway', $data);
    }

    /** POST /player/subscription_payhere_checkout — pay pending membership fee */
    public function subscription_payhere_checkout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('player/payments');
        }

        require_once APPROOT . '/libraries/PayHere.php';

        $playerData = $this->getPlayerData();
        $playerId = (int)($playerData['id'] ?? 0);
        $paymentId = (int)($_POST['payment_id'] ?? 0);

        if ($playerId <= 0 || $paymentId <= 0) {
            $_SESSION['payment_error'] = 'Invalid payment selected. Please try again.';
            redirect('player/payments');
        }

        $paymentModel = $this->model('M_Payment');
        $payment = $paymentModel->getPendingSubscriptionPaymentForPlayer($paymentId, $playerId);

        if (!$payment) {
            $_SESSION['payment_error'] = 'This payment is not available or has already been completed.';
            redirect('player/payments');
        }

        $amount = number_format((float)($payment->Amount ?? 0), 2, '.', '');
        if ((float)$amount <= 0) {
            $_SESSION['payment_error'] = 'Invalid subscription payment amount. Please contact admin.';
            redirect('player/payments');
        }

        $currency = 'LKR';
        $orderId = 'ELITE-SUB-' . $playerId . '-' . $paymentId . '-' . time();
        $itemsLabel = ucfirst((string)($payment->PlanName ?? 'Membership')) . ' Membership Fee';
        $nameParts = explode(' ', trim($playerData['name'] ?? 'Player'), 2);

        $_SESSION['payhere_pending_order'] = $orderId;
        unset($_SESSION['payhere_pending_shop_payment']);
        unset($_SESSION['payhere_pending_facility_booking']);
        unset($_SESSION['payhere_pending_rental_payment']);
        $_SESSION['payhere_pending_subscription_payment'] = [
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'player_id' => $playerId,
            'amount' => $amount,
            'currency' => $currency,
            'plan_name' => (string)($payment->PlanName ?? 'Membership'),
            'recipient_email' => (string)($payment->PlayerEmail ?? ($playerData['email'] ?? '')),
            'recipient_name' => (string)($payment->PlayerName ?? ($playerData['name'] ?? 'Player')),
        ];

        $data = [
            'title' => 'Redirecting to PayHere...',
            'player' => $playerData,
            'gateway' => [
                'merchant_id' => PayHere::MERCHANT_ID,
                'gateway_url' => PayHere::GATEWAY_URL,
                'order_id' => $orderId,
                'amount' => $amount,
                'currency' => $currency,
                'items' => $itemsLabel,
                'hash' => PayHere::buildHash($orderId, $amount, $currency),
                'return_url' => URLROOT . '/player/payhere_return?payment_type=subscription',
                'cancel_url' => URLROOT . '/player/payhere_cancel?payment_type=subscription',
                'notify_url' => URLROOT . '/player/payhere_notify',
                'first_name' => $nameParts[0] ?? 'Player',
                'last_name' => $nameParts[1] ?? '',
                'email' => $playerData['email'],
                'phone' => $playerData['phone'] ?: '0000000000',
                'address' => $playerData['address'] ?: 'N/A',
                'city' => 'Colombo',
                'country' => 'Sri Lanka',
            ],
        ];

        $this->view('player/payhere_gateway', $data);
    }

    /** GET /player/payhere_return — PayHere browser redirect on payment success */
    public function payhere_return() {
        $playerData = $this->getPlayerData();
        $orderId    = htmlspecialchars($_GET['order_id'] ?? '');
        $paymentType = strtolower(trim((string)($_GET['payment_type'] ?? '')));
        $message    = 'Your order has been placed successfully.';
        $primaryUrl = URLROOT . '/player/shopping';
        $primaryLabel = 'Continue Shopping';
        $secondaryUrl = URLROOT . '/player';
        $secondaryLabel = 'Dashboard';

        if ($paymentType === 'rental') {
            $primaryUrl = URLROOT . '/player/rentals';
            $primaryLabel = 'View Rentals';
            $secondaryUrl = URLROOT . '/player/shopping';
            $secondaryLabel = 'Back to Shop';
            $message = 'Your rental payment was successful.';
        } elseif ($paymentType === 'subscription') {
            $primaryUrl = URLROOT . '/player/payments';
            $primaryLabel = 'View Payments';
            $secondaryUrl = URLROOT . '/player';
            $secondaryLabel = 'Dashboard';
            $message = 'Your membership payment was successful.';
        } elseif ($paymentType === 'shop') {
            $primaryUrl = URLROOT . '/player/shopping';
            $primaryLabel = 'Continue Shopping';
            $secondaryUrl = URLROOT . '/player/cart';
            $secondaryLabel = 'Back to Cart';
        }

        $pendingFacilityBooking = $_SESSION['payhere_pending_facility_booking'] ?? null;
        if (is_array($pendingFacilityBooking) && !empty($pendingFacilityBooking['order_id'])
            && ($orderId === '' || $pendingFacilityBooking['order_id'] === $orderId)) {
            $occurrenceId = (int)($pendingFacilityBooking['occurrence_id'] ?? 0);
            $amount       = max(0.0, (float)($pendingFacilityBooking['amount'] ?? 0));
            $primaryUrl = URLROOT . '/playerslots/bookings';
            $primaryLabel = 'View My Bookings';
            $secondaryUrl = URLROOT . '/playerslots/facilities';
            $secondaryLabel = 'Back to Facilities';

            if ($occurrenceId > 0) {
                $bookingResult = $this->slotPlayerModel->createBooking(
                    $occurrenceId,
                    (int)$playerData['id'],
                    'self',
                    (int)$playerData['id'],
                    null,
                    $amount,
                    'payhere',
                    'paid',
                    1
                );

                if ($bookingResult === true || $bookingResult === 'duplicate') {
                    $message = 'Your facility booking has been confirmed successfully.';
                    $this->sendPaymentSuccessEmailForUser(
                        (int)$playerData['id'],
                        (string)$pendingFacilityBooking['order_id'],
                        (string)$amount,
                        'LKR',
                        'Facility Booking Payment',
                        'Facility booking confirmed successfully.'
                    );
                } else {
                    $message = 'Your payment was received, but the booking could not be finalized. Please contact support.';
                }
            }

            unset($_SESSION['payhere_pending_facility_booking']);
            unset($_SESSION['payhere_pending_shop_payment']);
            unset($_SESSION['payhere_pending_subscription_payment']);
            unset($_SESSION['payhere_pending_rental_payment']);
            unset($_SESSION['payhere_pending_order']);
        } else {
            $pendingSubscriptionPayment = $_SESSION['payhere_pending_subscription_payment'] ?? null;
            if (is_array($pendingSubscriptionPayment) && !empty($pendingSubscriptionPayment['order_id'])) {
                $subscriptionOrderId = (string)$pendingSubscriptionPayment['order_id'];
                if ($orderId === '' || $orderId === $subscriptionOrderId || $paymentType === 'subscription') {
                    $orderId = $subscriptionOrderId;
                    $primaryUrl = URLROOT . '/player/payments';
                    $primaryLabel = 'View Payments';
                    $secondaryUrl = URLROOT . '/player';
                    $secondaryLabel = 'Dashboard';
                    $paymentCompleted = $this->completeSubscriptionPaymentAndSendEmail(
                        (int)($pendingSubscriptionPayment['payment_id'] ?? 0),
                        (int)($pendingSubscriptionPayment['player_id'] ?? 0),
                        $subscriptionOrderId,
                        null,
                        (string)$subscriptionOrderId
                    );

                    if ($paymentCompleted) {
                        $message = 'Your membership payment has been completed successfully.';
                    } else {
                        $message = 'Your payment was received, but the membership record could not be updated. Please contact support.';
                    }

                    unset($_SESSION['payhere_pending_subscription_payment']);
                    unset($_SESSION['payhere_pending_order']);
                }
            } else {
                $pendingRentalPayment = $_SESSION['payhere_pending_rental_payment'] ?? null;
                if (is_array($pendingRentalPayment) && !empty($pendingRentalPayment['order_id'])) {
                    $rentalOrderId = (string)$pendingRentalPayment['order_id'];
                    if ($orderId === '' || $orderId === $rentalOrderId) {
                        $orderId = $rentalOrderId;
                        $primaryUrl = URLROOT . '/player/rentals';
                        $primaryLabel = 'View Rentals';
                        $secondaryUrl = URLROOT . '/player/shopping';
                        $secondaryLabel = 'Back to Shop';

                        $rentalResult = $this->completeRentalPaymentAndSendEmail($pendingRentalPayment, $playerData);
                        if (!empty($rentalResult['success'])) {
                            $message = 'Your rental payment was successful and the equipment rental is confirmed.';
                        } else {
                            $message = $rentalResult['message'] ?? 'Your payment was received, but the rental could not be finalized. Please contact support.';
                        }

                        unset($_SESSION['payhere_pending_rental_payment']);
                        unset($_SESSION['payhere_pending_order']);
                    }
                } else {
                    $pendingShopPayment = $_SESSION['payhere_pending_shop_payment'] ?? null;
                    if (is_array($pendingShopPayment) && !empty($pendingShopPayment['order_id'])) {
                    $shopOrderId = (string)$pendingShopPayment['order_id'];
                    if ($orderId === '' || $orderId === $shopOrderId || $paymentType === 'shop') {
                        $orderId = $shopOrderId;
                        $emailSent = $this->sendPendingShopPaymentSuccessEmail($pendingShopPayment);

                        if (!$emailSent) {
                            error_log("Shop payment success email failed on return for order $shopOrderId");
                        }

                        unset($_SESSION['payhere_pending_shop_payment']);
                        unset($_SESSION['payhere_pending_order']);
                    }
                    }
                }
            }
        }

        $data = [
            'title'    => 'Payment Successful',
            'player'   => $playerData,
            'order_id' => $orderId,
            'message'  => $message,
            'primary_url' => $primaryUrl,
            'primary_label' => $primaryLabel,
            'secondary_url' => $secondaryUrl,
            'secondary_label' => $secondaryLabel,
        ];
        $this->view('player/payhere_return', $data);
    }

    /** GET /player/payhere_cancel — PayHere browser redirect when user cancels */
    public function payhere_cancel() {
        $paymentType = strtolower(trim((string)($_GET['payment_type'] ?? '')));
        $pendingFacilityBooking = $_SESSION['payhere_pending_facility_booking'] ?? null;
        $pendingSubscriptionPayment = $_SESSION['payhere_pending_subscription_payment'] ?? null;
        $pendingShopPayment = $_SESSION['payhere_pending_shop_payment'] ?? null;
        $pendingRentalPayment = $_SESSION['payhere_pending_rental_payment'] ?? null;

        unset($_SESSION['payhere_pending_facility_booking']);
        unset($_SESSION['payhere_pending_shop_payment']);
        unset($_SESSION['payhere_pending_subscription_payment']);
        unset($_SESSION['payhere_pending_rental_payment']);
        unset($_SESSION['payhere_pending_order']);

        if (is_array($pendingSubscriptionPayment) || $paymentType === 'subscription') {
            $_SESSION['payment_error'] = 'Payment was cancelled. You can try the subscription payment again.';
            redirect('player/payments');
        }

        if (is_array($pendingFacilityBooking)) {
            $_SESSION['slot_error'] = 'Payment was cancelled. Please choose the facility slot again.';
            redirect('playerslots/facilities');
        }

        if (is_array($pendingRentalPayment) || $paymentType === 'rental') {
            flash('rental_message', 'Rental payment was cancelled. You can try again from the rentals page.', 'alert alert-warning');
            redirect('player/rentals');
        }

        if (is_array($pendingShopPayment) || $paymentType === 'shop') {
            $_SESSION['cart_error'] = 'Payment was cancelled. Please try again from the shop.';
            redirect('player/shopping');
        }

        $data = [
            'title'  => 'Payment Cancelled',
            'player' => $this->getPlayerData(),
            'primary_url' => URLROOT . '/player',
            'primary_label' => 'Dashboard',
            'secondary_url' => URLROOT . '/player/shopping',
            'secondary_label' => 'Continue Shopping',
        ];
        $this->view('player/payhere_cancel', $data);
    }

    /** POST /player/payhere_notify — Server-to-server webhook from PayHere */
    public function payhere_notify() {
        require_once APPROOT . '/libraries/PayHere.php';
        require_once APPROOT . '/libraries/Mailer.php';

        $logFile = APPROOT . '/../payhere_notify_log.txt';
        $orderId     = $_POST['order_id']       ?? '';
        $amount      = $_POST['payhere_amount'] ?? '';
        $currency    = $_POST['payhere_currency'] ?? '';
        $statusCode  = $_POST['status_code']   ?? '';

        if (PayHere::verifyNotify($_POST)) {
            PayHere::log($logFile, "SUCCESS order=$orderId amount=$amount $currency");
            if ($this->isShopProductPaymentOrder($orderId)) {
                $emailSent = $this->sendShopPaymentSuccessEmail($orderId, $amount, $currency);
                PayHere::log($logFile, 'SHOP_PAYMENT_EMAIL ' . ($emailSent ? 'SENT' : 'FAILED') . " order=$orderId");
            } elseif ($this->isSubscriptionPaymentOrder($orderId)) {
                $ids = $this->getSubscriptionPaymentIdsFromOrderId($orderId);
                $completed = $this->completeSubscriptionPaymentAndSendEmail(
                    $ids['payment_id'],
                    $ids['player_id'],
                    $orderId,
                    $_POST['payment_id'] ?? ($_POST['payhere_payment_id'] ?? null),
                    $orderId
                );
                PayHere::log($logFile, 'SUBSCRIPTION_PAYMENT ' . ($completed ? 'COMPLETED' : 'FAILED') . " order=$orderId");
            } elseif ($this->isRentalPaymentOrder($orderId)) {
                PayHere::log($logFile, "RENTAL_PAYMENT_SUCCESS order=$orderId browser_return_required_for_local_demo");
            }
        } else {
            PayHere::log($logFile, "UNVERIFIED/FAILED status=$statusCode order=$orderId");
        }

        http_response_code(200);
        echo 'OK';
        exit;
    }

    private function isShopProductPaymentOrder(string $orderId): bool {
        return preg_match('/^ELITE-\d+-\d+$/', $orderId) === 1;
    }

    private function getPlayerIdFromShopOrderId(string $orderId): int {
        if (preg_match('/^ELITE-(\d+)-\d+$/', $orderId, $matches) !== 1) {
            return 0;
        }
        return (int)$matches[1];
    }

    private function isSubscriptionPaymentOrder(string $orderId): bool {
        return preg_match('/^ELITE-SUB-\d+-\d+-\d+$/', $orderId) === 1;
    }

    private function isRentalPaymentOrder(string $orderId): bool {
        return preg_match('/^ELITE-RENT-\d+-\d+-\d+$/', $orderId) === 1;
    }

    private function getSubscriptionPaymentIdsFromOrderId(string $orderId): array {
        if (preg_match('/^ELITE-SUB-(\d+)-(\d+)-\d+$/', $orderId, $matches) !== 1) {
            return ['player_id' => 0, 'payment_id' => 0];
        }

        return [
            'player_id' => (int)$matches[1],
            'payment_id' => (int)$matches[2],
        ];
    }

    private function completeSubscriptionPaymentAndSendEmail(
        int $paymentId,
        int $playerId,
        string $orderId,
        ?string $gatewayPaymentId = null,
        ?string $reference = null
    ): bool {
        if ($paymentId <= 0 || $playerId <= 0) {
            return false;
        }

        $paymentModel = $this->model('M_Payment');
        $payment = $paymentModel->getPendingSubscriptionPaymentForPlayer($paymentId, $playerId)
            ?: $paymentModel->getSubscriptionPaymentByGatewayOrderId($orderId);

        if (!$payment) {
            return false;
        }

        $updated = $paymentModel->markSubscriptionPaymentCompleted($paymentId, $orderId, $gatewayPaymentId, $reference);
        if (!$updated) {
            return false;
        }

        $payment = $paymentModel->getSubscriptionPaymentByGatewayOrderId($orderId) ?: $payment;

        require_once APPROOT . '/libraries/PaymentEmailService.php';
        PaymentEmailService::sendSuccessEmail(
            $playerId,
            (string)($payment->PlayerEmail ?? ''),
            (string)($payment->PlayerName ?? 'Player'),
            $orderId,
            (string)($payment->Amount ?? '0.00'),
            'LKR',
            'Membership Payment',
            ucfirst((string)($payment->PlanName ?? 'Membership')) . ' membership fee paid successfully.'
        );

        return true;
    }

    private function sendPaymentSuccessEmailForUser(
        int $playerId,
        string $orderId,
        string $amount,
        string $currency,
        string $paymentTitle,
        string $paymentDescription
    ): bool {
        $player = $this->userModel->getUserById($playerId);
        if (!$player) {
            return false;
        }

        require_once APPROOT . '/libraries/PaymentEmailService.php';
        return PaymentEmailService::sendSuccessEmail(
            $playerId,
            (string)($player->Email ?? ''),
            trim((string)($player->Name ?? ($player->FirstName ?? 'Player'))),
            $orderId,
            $amount,
            $currency,
            $paymentTitle,
            $paymentDescription
        );
    }

    private function sendPendingShopPaymentSuccessEmail(array $pendingShopPayment): bool {
        $orderId = (string)($pendingShopPayment['order_id'] ?? '');
        if ($orderId === '') {
            return false;
        }

        $playerId = (int)($pendingShopPayment['player_id'] ?? $this->getPlayerIdFromShopOrderId($orderId));
        $recipientEmail = (string)($pendingShopPayment['recipient_email'] ?? '');
        $recipientName = (string)($pendingShopPayment['recipient_name'] ?? 'Player');

        if ($recipientEmail === '') {
            return $this->sendShopPaymentSuccessEmail(
                $orderId,
                (string)($pendingShopPayment['amount'] ?? '0.00'),
                (string)($pendingShopPayment['currency'] ?? 'LKR')
            );
        }

        require_once APPROOT . '/libraries/PaymentEmailService.php';
        return PaymentEmailService::sendSuccessEmail(
            $playerId > 0 ? $playerId : null,
            $recipientEmail,
            $recipientName,
            $orderId,
            (string)($pendingShopPayment['amount'] ?? '0.00'),
            (string)($pendingShopPayment['currency'] ?? 'LKR'),
            'Shop Product Payment',
            'Shop product payment received successfully.'
        );
    }

    private function sendShopPaymentSuccessEmail(string $orderId, string $amount, string $currency): bool {
        $playerId = $this->getPlayerIdFromShopOrderId($orderId);
        if ($playerId <= 0) {
            error_log("Payment email skipped: could not parse player ID from order $orderId");
            return false;
        }
        return $this->sendPaymentSuccessEmailForUser(
            $playerId,
            $orderId,
            $amount,
            $currency,
            'Shop Product Payment',
            'Shop product payment received successfully.'
        );
    }

    private function sendRentalConfirmationEmail(array $playerData, array $rentalDetails): bool {
        require_once APPROOT . '/libraries/RentalEmailService.php';

        try {
            return RentalEmailService::sendConfirmationEmail(
                (int)($playerData['id'] ?? 0),
                (string)($playerData['email'] ?? ''),
                (string)($playerData['name'] ?? 'Player'),
                $rentalDetails
            );
        } catch (Throwable $e) {
            error_log('Rental confirmation email failed: ' . $e->getMessage());
            return false;
        }
    }

    private function completeRentalPaymentAndSendEmail(array $pendingRentalPayment, array $playerData): array {
        $playerId = (int)($pendingRentalPayment['player_id'] ?? 0);
        if ($playerId <= 0 || $playerId !== (int)($playerData['id'] ?? 0)) {
            return ['success' => false, 'message' => 'Your payment was received, but the rental player details did not match. Please contact support.'];
        }

        $shopModel = $this->model('M_Shop');
        $result = $shopModel->createAutoConfirmedRental([
            'equipment_id' => (int)($pendingRentalPayment['equipment_id'] ?? 0),
            'player_id' => $playerId,
            'start_date' => (string)($pendingRentalPayment['start_date'] ?? ''),
            'duration_days' => (int)($pendingRentalPayment['duration_days'] ?? 0),
            'quantity' => (int)($pendingRentalPayment['quantity'] ?? 0),
            'pickup_method' => (string)($pendingRentalPayment['pickup_method'] ?? 'pickup'),
            'processed_by' => 5,
        ]);

        if (empty($result['success'])) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Your payment was received, but the rental could not be finalized. Please contact support.',
            ];
        }

        $equipment = $result['equipment'] ?? null;
        $pickupMethod = (string)($pendingRentalPayment['pickup_method'] ?? 'pickup');
        $this->sendRentalConfirmationEmail(
            $playerData,
            [
                'rental_ids' => $result['rental_ids'] ?? [],
                'equipment_name' => $equipment->Name ?? ($pendingRentalPayment['equipment_name'] ?? 'Equipment'),
                'quantity' => $result['quantity'] ?? ($pendingRentalPayment['quantity'] ?? 1),
                'start_time' => $result['start_time'] ?? '',
                'end_time' => $result['end_time'] ?? '',
                'pickup_label' => $pickupMethod === 'delivery' ? 'Home delivery' : 'Academy pickup',
                'total_cost' => $result['total_cost'] ?? ($pendingRentalPayment['amount'] ?? 0),
            ]
        );

        return $result;
    }

    // Backwards-compatible route for older links
    public function payment() {
        $this->checkout();
    }
    
    // AJAX: Get notifications for the logged-in player
    public function notifications() {
        $this->requireLogin();
        ob_start();
        header('Content-Type: application/json');
        $userId = (int)$_SESSION['user_id'];
        $notifModel = $this->model('M_Notification');
        echo json_encode([
            'notifications' => $notifModel->getForUser($userId, 15),
            'unread_count'  => $notifModel->countUnread($userId),
        ]);
        ob_end_flush(); exit;
    }

    // AJAX: Mark notification(s) read
    public function mark_notifications_read() {
        $this->requireLogin();
        ob_start();
        header('Content-Type: application/json');
        $userId = (int)$_SESSION['user_id'];
        $notifModel = $this->model('M_Notification');
        $id = (int)($_POST['notification_id'] ?? 0);
        if ($id) {
            $notifModel->markRead($id, $userId);
        } else {
            $notifModel->markAllRead($userId);
        }
        echo json_encode(['success' => true, 'unread_count' => $notifModel->countUnread($userId)]);
        ob_end_flush(); exit;
    }

    // Session Calendar
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

        if (!$stats) {
            return [
                'batting_avg' => 0,
                'strike_rate' => 0,
                'total_runs' => 0,
                'total_wickets' => 0,
                'bowling_avg' => 0,
                'economy_rate' => 0,
                'matches_played' => 0,
                'wins' => 0
            ];
        }

        return [
            'batting_avg' => $stats->BattingAverage ?? 0,
            'strike_rate' => $stats->StrikeRate ?? 0,
            'total_runs' => $stats->TotalRuns ?? 0,
            'total_wickets' => $stats->TotalWickets ?? 0,
            'bowling_avg' => $stats->BowlingAverage ?? 0,
            'economy_rate' => $stats->EconomyRate ?? 0,
            'matches_played' => $stats->MatchesPlayed ?? 0,
            'wins' => $stats->Wins ?? 0
        ];
    }
    
    private function getTodaySchedule() {
        $playerId = $_SESSION['user_id'] ?? 6;
        return $this->slotPlayerModel->getTodaySchedule($playerId);
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
        return $this->slotPlayerModel->getUpcomingBookingFeed($playerId);
    }

    private function buildDashboardCalendarEvents(): array {
        $events = [];

        foreach ($this->getUpcomingBookings() as $booking) {
            $start = $booking->StartTime ?? '00:00:00';
            $end = $booking->EndTime ?? $start;
            $duration = '';

            if (!empty($start) && !empty($end)) {
                $duration = date('g:i A', strtotime($start)) . ' - ' . date('g:i A', strtotime($end));
            }

            $events[] = [
                'date' => $booking->date,
                'time' => $start,
                'type' => $booking->booking_type,
                'title' => $booking->reason ?: ucfirst($booking->booking_type),
                'location' => $booking->location ?? ($booking->practitioner_name ?? 'Academy'),
                'duration' => $duration,
            ];
        }

        return $events;
    }
    
    private function getRentalsDue() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        return $paymentModel->getRentalPaymentsDue($playerId);
    }

    private function getPaymentModelWithInitializedMembershipPayments() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->model('M_Payment');
        $paymentModel->ensureInitialPendingMembershipPayment($playerId);

        return $paymentModel;
    }
    
    private function getPaymentsDue() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->getPaymentModelWithInitializedMembershipPayments();
        $payments = $paymentModel->getUpcomingPayments($playerId);

        return array_map(function ($payment) {
            return [
                'type' => 'Membership Pending',
                'amount' => 'Rs. ' . number_format((float)($payment->Amount ?? 0), 2),
                'due_date' => $payment->DueDate ?? date('Y-m-d'),
                'message' => !empty($payment->Notes)
                    ? $payment->Notes
                    : 'Pay to experience the whole academy services.',
            ];
        }, $payments);
    }
    
    private function getTrainingSessions() {
        $playerId = $_SESSION['user_id'] ?? 6;
        return $this->slotPlayerModel->getUpcomingScheduleSessions($playerId);
    }
    
    private function getCoachSessions() {
        $playerId = $_SESSION['user_id'] ?? 6;
        return $this->slotPlayerModel->getUpcomingCoachSessions($playerId);
    }
    
    private function getFacilityReservations() {
        $playerId = $_SESSION['user_id'] ?? 6;
        return $this->slotPlayerModel->getFacilityBookingsForPlayer($playerId);
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
    
    private function getMonthlyFees() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $paymentModel = $this->getPaymentModelWithInitializedMembershipPayments();
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
        $paymentModel = $this->getPaymentModelWithInitializedMembershipPayments();
        return $paymentModel->getUpcomingPayments($playerId);
    }

    private function formatRecentSubscriptionPayments(array $payments): array {
        $recentPayments = [];

        foreach ($payments as $payment) {
            if (($payment->Status ?? '') === 'pending' || empty($payment->PaymentDate)) {
                continue;
            }

            $recentPayments[] = [
                'date' => $payment->PaymentDate,
                'description' => ucfirst((string)($payment->PlanName ?? 'Membership')) . ' Membership Fee',
                'details' => !empty($payment->PaymentReference)
                    ? 'Reference: ' . $payment->PaymentReference
                    : 'Subscription payment recorded successfully.',
                'amount' => (float)($payment->Amount ?? 0),
                'method_type' => ($payment->PaymentMethod ?? '') === 'bank_transfer' ? 'bank' : 'card',
                'method_label' => $this->formatPaymentMethodLabel((string)($payment->PaymentMethod ?? 'online')),
                'status' => ucfirst((string)($payment->Status ?? 'completed')),
                'status_class' => ($payment->Status ?? 'completed') === 'completed' ? 'paid' : strtolower((string)$payment->Status),
            ];
        }

        return $recentPayments;
    }

    private function formatUpcomingSubscriptionPayments(array $payments): array {
        $upcomingPayments = [];

        foreach ($payments as $payment) {
            $upcomingPayments[] = [
                'payment_id' => (int)($payment->PaymentID ?? 0),
                'due_date' => $payment->DueDate ?? date('Y-m-d'),
                'description' => 'Membership Pending',
                'details' => !empty($payment->Notes)
                    ? $payment->Notes
                    : 'Pay to experience the whole academy services.',
                'amount' => (float)($payment->Amount ?? 0),
                'method_type' => ($payment->PaymentMethod ?? '') === 'bank_transfer' ? 'bank' : 'card',
                'method_label' => $this->formatPaymentMethodLabel((string)($payment->PaymentMethod ?? 'online')),
                'status' => 'Pending',
                'status_class' => 'pending',
            ];
        }

        return $upcomingPayments;
    }

    private function formatPaymentMethodLabel(string $paymentMethod): string {
        return match ($paymentMethod) {
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'card' => 'Card',
            'online' => 'Online Payment',
            default => ucfirst(str_replace('_', ' ', $paymentMethod ?: 'online')),
        };
    }

    private function getPaymentMethodsList(): array {
        return [
            [
                'icon' => 'credit-card',
                'color' => '#4A90E2',
                'name' => 'PayHere Online Payment',
                'details' => 'Use online payments to settle your pending membership fee and unlock full academy services.',
            ],
            [
                'icon' => 'money-bill-wave',
                'color' => '#16A34A',
                'name' => 'Counter Payment',
                'details' => 'You can also complete membership payments at the academy counter through the shop staff.',
            ],
        ];
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
        return $this->slotPlayerModel->getFacilityBookingsForPlayer($playerId);
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
                'FirstName' => '',
                'LastName' => '',
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
            'FirstName', 'LastName', 'Email', 'PhoneNumber', 'Address'
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
                'firstName' => trim($_POST['firstName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number']),
                'address' => trim($_POST['address']),
                'school' => trim($_POST['school']),
                'role' => $_SESSION['user_role'], // Keep current role
                'status' => 'active' // Keep active
            ];
            
            // Validate data
            $errors = [];
            if (empty($userData['firstName'])) {
                $errors[] = 'First name is required';
            }
            if (empty($userData['lastName'])) {
                $errors[] = 'Last name is required';
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
                    $_SESSION['user_name'] = trim($userData['firstName'] . ' ' . $userData['lastName']);
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
                'firstName' => trim($_POST['firstName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number']),
                'address' => trim($_POST['address']),
                'school' => trim($_POST['school']),
                'role' => $_SESSION['user_role'], // Keep current role
                'status' => 'active' // Keep active
            ];
            
            // Validate data
            $errors = [];
            if (empty($userData['firstName'])) {
                $errors[] = 'First name is required';
            }
            if (empty($userData['lastName'])) {
                $errors[] = 'Last name is required';
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
                    $_SESSION['user_name'] = trim($userData['firstName'] . ' ' . $userData['lastName']);
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
