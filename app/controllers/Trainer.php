<?php
class Trainer extends Controller {
    public function __construct() {
        // Check authentication for all trainer pages
        requireAuth(['Trainer']);
        // $this->trainerModel = $this->model('M_Trainer');
        // Comment out model for development to avoid database dependencies
    }

    public function index() {
        // Redirect to dashboard
        redirect('trainer/dashboard');
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        $trainerId = (int)$_SESSION['user_id'];
        $sessionModel = $this->model('M_Session');
        $sessions = $sessionModel->getSessionsByCoach($trainerId);

        foreach ($sessions as $session) {
            $session->players = $sessionModel->getSessionParticipants($session->SessionID);
        }

        $today = date('Y-m-d');
        $todaySessions = array_values(array_filter($sessions, fn($s) => ($s->Date ?? '') === $today));

        $recentSessions = $sessions;
        usort($recentSessions, fn($a, $b) => strcmp(($b->Date ?? '') . ($b->StartTime ?? ''), ($a->Date ?? '') . ($a->StartTime ?? '')));
        $recentSessions = array_slice($recentSessions, 0, 6);

        $data = [
            'title' => 'Trainer Dashboard',
            'trainer_name' => $_SESSION['user_name'] ?? 'Trainer',
            'sessions' => $sessions,
            'today_sessions' => $todaySessions,
            'recent_sessions' => $recentSessions,
            'upcoming_sessions' => $this->getUpcomingSessions($trainerId),
            'today_stats' => $this->getTodayStats($sessions)
        ];

        $this->view('trainer/dashboard', $data);
    }

    public function schedules() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        $data = [
            'title' => 'Schedules Management',
            'schedules' => [] // $this->trainerModel->getSchedules($_SESSION['user_id'])
        ];

        $this->view('trainer/schedules', $data);
    }

    public function bookings() {
        $trainerId    = $_SESSION['user_id'];
        $sessionModel = $this->model('M_Session');
        $sessions     = $sessionModel->getSessionsByCoach($trainerId);

        foreach ($sessions as $session) {
            $session->players = $sessionModel->getSessionParticipants($session->SessionID);
        }

        $data = [
            'title'    => 'Schedule & Bookings',
            'sessions' => $sessions,
        ];
        $this->view('trainer/bookings', $data);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Add Session  — GET shows form, POST processes it  (/trainer/addSession)
    // ──────────────────────────────────────────────────────────────────────────
    public function addSession() {
        // Ensure logged-in trainer
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        // ── GET: render the Add Session form page ─────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Pull back any errors/old values from a previous failed submission
            $errors  = $_SESSION['add_session_errors'] ?? [];
            $oldData = $_SESSION['add_session_data']   ?? [];
            unset($_SESSION['add_session_errors'], $_SESSION['add_session_data']);

            $data = [
                'title'   => 'Add Session',
                'errors'  => $errors,
                'oldData' => $oldData,
            ];
            $this->view('trainer/add_session', $data);
            return;
        }

        // ── Allowed option lists (whitelist) ──────────────────────────────────
        $validTimeSlots = [
            '06:00-07:00', '07:00-08:00', '07:30-08:30', '08:00-09:00',
            '09:00-10:00', '10:00-11:00', '11:00-12:00', '12:00-13:00',
            '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00',
            '17:00-18:00', '18:00-19:00',
        ];
        $validLocations = [
            'Gym A - Weight Room',
            'Cardio Zone - Fitness Center',
            'Yoga Studio - Recovery Room',
            'Field Area - Training Ground',
            'Indoor Court - Sports Hall',
            'Cricket Ground - Main Oval',
            'Swimming Pool - Aquatic Center',
            'Conference Room - Meeting Room',
        ];

        // ── Sanitise inputs ───────────────────────────────────────────────────
        $title       = trim(htmlspecialchars($_POST['session_title']       ?? '', ENT_QUOTES, 'UTF-8'));
        $clientName  = trim(htmlspecialchars($_POST['session_client']      ?? '', ENT_QUOTES, 'UTF-8'));
        $date        = trim($_POST['session_date']       ?? '');
        $timeSlot    = trim($_POST['session_time_slot']  ?? '');
        $location    = trim(htmlspecialchars($_POST['session_location']    ?? '', ENT_QUOTES, 'UTF-8'));
        $description = trim(htmlspecialchars($_POST['session_description'] ?? '', ENT_QUOTES, 'UTF-8'));
        $status      = trim($_POST['session_status']     ?? '');

        // ── Server-side validation ────────────────────────────────────────────
        $errors = [];

        // Session Title
        if (empty($title)) {
            $errors['session_title'] = 'Session title is required.';
        } elseif (strlen($title) < 3 || strlen($title) > 100) {
            $errors['session_title'] = 'Title must be between 3 and 100 characters.';
        }

        // Client Name
        if (empty($clientName)) {
            $errors['session_client'] = 'Client name is required.';
        } elseif (!preg_match("/^[a-zA-Z\s'\-\.]+$/u", $clientName)) {
            $errors['session_client'] = 'Client name must contain letters only.';
        } elseif (strlen($clientName) > 100) {
            $errors['session_client'] = 'Client name must not exceed 100 characters.';
        }

        // Date — required & not in the past
        if (empty($date)) {
            $errors['session_date'] = 'Session date is required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors['session_date'] = 'Invalid date format.';
        } else {
            $today  = new DateTime('today');
            $chosen = DateTime::createFromFormat('Y-m-d', $date);
            if (!$chosen || $chosen < $today) {
                $errors['session_date'] = 'Date cannot be in the past.';
            }
        }

        // Time Slot — must be from predefined list
        if (empty($timeSlot)) {
            $errors['session_time_slot'] = 'Please select a time slot.';
        } elseif (!in_array($timeSlot, $validTimeSlots, true)) {
            $errors['session_time_slot'] = 'Invalid time slot selected.';
        }

        // Location — must be from predefined list
        if (empty($location)) {
            $errors['session_location'] = 'Please select a location.';
        } elseif (!in_array($location, $validLocations, true)) {
            $errors['session_location'] = 'Invalid location selected.';
        }

        // Description
        if (empty($description)) {
            $errors['session_description'] = 'Description is required.';
        } elseif (strlen($description) < 10) {
            $errors['session_description'] = 'Description must be at least 10 characters.';
        } elseif (strlen($description) > 1000) {
            $errors['session_description'] = 'Description must not exceed 1000 characters.';
        }

        // Status
        if (empty($status) || !in_array($status, ['active', 'upcoming', 'planned', 'completed'], true)) {
            $errors['session_status'] = 'Please select a status.';
        }

        // ── If validation failed, bounce back to the form with errors ───────────
        if (!empty($errors)) {
            $_SESSION['add_session_errors'] = $errors;
            $_SESSION['add_session_data']   = [
                'session_title'       => $title,
                'session_client'      => $clientName,
                'session_date'        => $date,
                'session_time_slot'   => $timeSlot,
                'session_location'    => $location,
                'session_description' => $description,
                'session_status'      => $status,
            ];
            redirect('trainer/addSession');
        }

        // ── Parse time slot into start/end times ──────────────────────────────
        // Format: "HH:MM-HH:MM"  e.g. "06:00-07:00"
        list($startHHMM, $endHHMM) = explode('-', $timeSlot);
        $startTime = $startHHMM . ':00';  // "06:00:00"
        $endTime   = $endHHMM   . ':00';  // "07:00:00"

        // Map UI status to DB status (planned/upcoming → active in DB)
        $dbStatus = ($status === 'completed') ? 'completed' : 'active';

        // ── Save to database ──────────────────────────────────────────────────
        $sessionModel = $this->model('M_Session');
        $sessionId = $sessionModel->addTrainerBookingSession([
            'trainer_id'  => $_SESSION['user_id'],
            'title'       => $title,
            'client_name' => $clientName,
            'date'        => $date,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'location'    => $location,
            'description' => $description,
            'status'      => $dbStatus,
        ]);

        if ($sessionId) {
            $_SESSION['flash_message'] = 'Session "' . $title . '" added successfully!';
            $_SESSION['flash_type']    = 'success';
        } else {
            $_SESSION['flash_message'] = 'Failed to add session. Please try again.';
            $_SESSION['flash_type']    = 'error';
        }

        redirect('trainer/bookings');
    }

    public function tournaments() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        $data = [
            'title' => 'Tournaments',
            'tournaments' => [] // $this->trainerModel->getTournaments()
        ];

        $this->view('trainer/tournaments', $data);
    }

    public function nutrition() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 10;
            $_SESSION['username'] = 'Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        // Initialize nutrition plan model
        $nutritionModel = $this->model('M_NutritionPlan');
        
        // Get trainer's nutrition plans (with assignment counts/names)
        $nutritionPlans = $nutritionModel->getAllPlans($_SESSION['user_id']);

        $data = [
            'title' => 'Nutrition Plans',
            'nutrition_plans' => $nutritionPlans,
        ];

        $this->view('trainer/nutrition', $data);
    }

    public function workout() {
        $trainerModel = $this->model('M_Trainer');
        $trainer_id   = $_SESSION['user_id'];

        // Own plans + read-only view of other trainers' active plans
        $workoutPlans = $trainerModel->getWorkoutPlansWithVisibility($trainer_id);
        $players      = $trainerModel->getAllPlayers();

        $data = [
            'title'        => 'Workout Plans',
            'workout_plans' => $workoutPlans,
            'players'      => $players
        ];

        $this->view('trainer/workout', $data);
    }

    // Assign an existing workout plan to a player (POST, JSON response)
    public function assignPlanToPlayer() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $trainerModel = $this->model('M_Trainer');
        $trainer_id   = (int)$_SESSION['user_id'];

        $plan_id    = isset($_POST['plan_id'])   ? (int)$_POST['plan_id']   : 0;
        $player_id  = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
        $end_date   = !empty($_POST['end_date']) && strtotime($_POST['end_date'])
                        ? date('Y-m-d', strtotime($_POST['end_date']))
                        : null;

        if (!$plan_id || !$player_id) {
            echo json_encode(['success' => false, 'message' => 'Plan and player are required']);
            return;
        }

        // Verify plan exists and is active/draft (not archived)
        $plan = $trainerModel->getWorkoutPlanById($plan_id);
        if (!$plan || $plan->Status === 'archived') {
            echo json_encode(['success' => false, 'message' => 'Plan not found or is archived']);
            return;
        }

        $result = $trainerModel->assignPlanToPlayer([
            'plan_id'     => $plan_id,
            'player_id'   => $player_id,
            'assigned_by' => $trainer_id,
            'end_date'    => $end_date
        ]);

        if ($result === 'duplicate') {
            echo json_encode(['success' => false, 'message' => 'This player already has an active assignment for this plan']);
        } elseif ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Plan assigned successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error while assigning plan']);
        }
    }

    // Unassign a workout plan from a player (POST, JSON response)
    public function unassignPlanFromPlayer() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $trainerModel = $this->model('M_Trainer');
        $trainer_id   = (int)$_SESSION['user_id'];
        $plan_id      = isset($_POST['plan_id'])   ? (int)$_POST['plan_id']   : 0;
        $player_id    = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;

        if (!$plan_id || !$player_id) {
            echo json_encode(['success' => false, 'message' => 'Plan and player are required']);
            return;
        }

        if ($trainerModel->unassignPlanFromPlayer($plan_id, $player_id, $trainer_id)) {
            echo json_encode(['success' => true, 'message' => 'Assignment removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not remove assignment. You may not have permission (only the assigning trainer can unassign).']);
        }
    }

    // Update the status of an assignment (active / completed / paused) — POST, JSON
    public function updateAssignmentStatus() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $trainerModel = $this->model('M_Trainer');
        $trainer_id   = (int)$_SESSION['user_id'];
        $plan_id      = isset($_POST['plan_id'])   ? (int)$_POST['plan_id']   : 0;
        $player_id    = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
        $status       = isset($_POST['status'])    ? trim($_POST['status'])    : '';

        if (!$plan_id || !$player_id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        if ($trainerModel->updateAssignmentStatus($plan_id, $player_id, $trainer_id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Assignment status updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not update status. You may not have permission.']);
        }
    }

    // Add workout plan
    public function addWorkoutPlan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $trainerModel = $this->model('M_Trainer');

            $data = [
                'trainer_id'     => (int)$_SESSION['user_id'],
                'workoutname'    => isset($_POST['workoutname'])     ? trim($_POST['workoutname'])    : '',
                'frequency'      => isset($_POST['frequency'])       ? trim($_POST['frequency'])      : '',
                'duration'       => isset($_POST['duration'])        ? (int)$_POST['duration']        : 0,
                'videolink'      => !empty($_POST['videolink'])      ? trim($_POST['videolink'])       : null,
                'intensity'      => !empty($_POST['intensity'])      ? trim($_POST['intensity'])       : 'Moderate',
                'notsuitablefor' => !empty($_POST['notsuitablefor']) ? trim($_POST['notsuitablefor'])  : 'None (General)',
                'benefits'       => !empty($_POST['benefits'])       ? trim($_POST['benefits'])        : null,
            ];

            if (empty($data['workoutname']) || empty($data['frequency']) || empty($data['duration'])) {
                flash('workout_message', 'Please fill in all required fields (Workout Name, Frequency, Duration)', 'alert alert-danger');
            } else if (strlen($data['workoutname']) < 3 || strlen($data['workoutname']) > 255) {
                flash('workout_message', 'Workout name must be between 3 and 255 characters', 'alert alert-danger');
            } else if ($data['duration'] < 15 || $data['duration'] > 180) {
                flash('workout_message', 'Duration must be between 15 and 180 minutes', 'alert alert-danger');
            } else if (!in_array($data['frequency'], ['Daily', 'Weekly', 'Bi-weekly'])) {
                flash('workout_message', 'Invalid frequency selected', 'alert alert-danger');
            } else if (!empty($data['intensity']) && !in_array($data['intensity'], ['Low', 'Moderate', 'High'])) {
                flash('workout_message', 'Invalid intensity level selected', 'alert alert-danger');
            } else if (!empty($data['videolink']) && !filter_var($data['videolink'], FILTER_VALIDATE_URL)) {
                flash('workout_message', 'Please provide a valid URL for the video link', 'alert alert-danger');
            } else if (!empty($data['benefits']) && strlen($data['benefits']) > 1000) {
                flash('workout_message', 'Benefits description must not exceed 1000 characters', 'alert alert-danger');
            } else if (!in_array($data['notsuitablefor'], [
                'None (General)', 'Post-Surgery', 'Active Lower Back Pain', 'Knee Injuries',
                'Shoulder Instability', 'Acute Ankle Sprain', 'Heart Conditions', 'Concussion Protocol'
            ])) {
                flash('workout_message', 'Invalid contraindication selected', 'alert alert-danger');
            } else {
                if ($trainerModel->addWorkoutPlan($data)) {
                    flash('workout_message', 'Workout plan added successfully!', 'alert alert-success');
                } else {
                    flash('workout_message', 'Failed to add workout plan. Please try again.', 'alert alert-danger');
                }
            }
        }

        redirect('trainer/workout');
    }

    // Update workout plan
    public function updateWorkoutPlan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $trainerModel = $this->model('M_Trainer');

            $data = [
                'plan_id'        => isset($_POST['plan_id'])         ? (int)$_POST['plan_id']         : 0,
                'trainer_id'     => (int)$_SESSION['user_id'],
                'workoutname'    => isset($_POST['workoutname'])      ? trim($_POST['workoutname'])     : '',
                'frequency'      => isset($_POST['frequency'])        ? trim($_POST['frequency'])       : '',
                'duration'       => isset($_POST['duration'])         ? (int)$_POST['duration']         : 0,
                'videolink'      => !empty($_POST['videolink'])       ? trim($_POST['videolink'])        : null,
                'intensity'      => !empty($_POST['intensity'])       ? trim($_POST['intensity'])        : 'Moderate',
                'notsuitablefor' => !empty($_POST['notsuitablefor'])  ? trim($_POST['notsuitablefor'])   : 'None (General)',
                'benefits'       => !empty($_POST['benefits'])        ? trim($_POST['benefits'])         : null,
            ];

            if (empty($data['workoutname']) || empty($data['frequency']) || empty($data['duration'])) {
                flash('workout_message', 'Please fill in all required fields', 'alert alert-danger');
            } else if (strlen($data['workoutname']) < 3 || strlen($data['workoutname']) > 255) {
                flash('workout_message', 'Workout name must be between 3 and 255 characters', 'alert alert-danger');
            } else if ($data['duration'] < 15 || $data['duration'] > 180) {
                flash('workout_message', 'Duration must be between 15 and 180 minutes', 'alert alert-danger');
            } else if (!in_array($data['frequency'], ['Daily', 'Weekly', 'Bi-weekly'])) {
                flash('workout_message', 'Invalid frequency selected', 'alert alert-danger');
            } else if (!empty($data['intensity']) && !in_array($data['intensity'], ['Low', 'Moderate', 'High'])) {
                flash('workout_message', 'Invalid intensity level selected', 'alert alert-danger');
            } else if (!empty($data['videolink']) && !filter_var($data['videolink'], FILTER_VALIDATE_URL)) {
                flash('workout_message', 'Please provide a valid URL for the video link', 'alert alert-danger');
            } else if (!empty($data['benefits']) && strlen($data['benefits']) > 1000) {
                flash('workout_message', 'Benefits description must not exceed 1000 characters', 'alert alert-danger');
            } else if (!in_array($data['notsuitablefor'], [
                'None (General)', 'Post-Surgery', 'Active Lower Back Pain', 'Knee Injuries',
                'Shoulder Instability', 'Acute Ankle Sprain', 'Heart Conditions', 'Concussion Protocol'
            ])) {
                flash('workout_message', 'Invalid contraindication selected', 'alert alert-danger');
            } else {
                if ($trainerModel->updateWorkoutPlan($data)) {
                    flash('workout_message', 'Workout plan updated successfully', 'alert alert-success');
                } else {
                    flash('workout_message', 'Failed to update workout plan. Please try again.', 'alert alert-danger');
                }
            }
        }

        redirect('trainer/workout');
    }

    // Delete workout plan
    public function deleteWorkoutPlan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $trainerModel = $this->model('M_Trainer');
            $plan_id    = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
            $trainer_id = (int)$_SESSION['user_id'];
            
            if ($trainerModel->deleteWorkoutPlan($plan_id, $trainer_id)) {
                flash('workout_message', 'Workout plan deleted successfully', 'alert alert-success');
            } else {
                flash('workout_message', 'Failed to delete workout plan', 'alert alert-danger');
            }
        }
        
        redirect('trainer/workout');
    }

    // AJAX: Get players assigned to a plan (GET, JSON)
    public function getAssignedPlayers() {
        header('Content-Type: application/json');
        $plan_id    = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
        $trainer_id = (int)$_SESSION['user_id'];

        if (!$plan_id) {
            echo json_encode(['success' => false, 'players' => []]);
            return;
        }

        $trainerModel = $this->model('M_Trainer');
        $players      = $trainerModel->getAssignedPlayersForPlan($plan_id);

        $result = [];
        foreach ($players as $p) {
            $row = (array)$p;
            $row['can_manage'] = ((int)($p->AssignedBy ?? 0) === $trainer_id);
            $result[] = $row;
        }

        echo json_encode(['success' => true, 'players' => $result]);
    }

    public function supplements() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        // Initialize supplement plan model
        $supplementModel = $this->model('M_SupplementPlan');
        
        // Get trainer's supplement plans
        $supplementPlans = $supplementModel->getSupplementPlansByTrainer($_SESSION['user_id']);
        $players = $supplementModel->getAllPlayers();

        $data = [
            'title' => 'Supplement Plans',
            'supplement_plans' => $supplementPlans,
            'players' => $players
        ];

        $this->view('trainer/supplements', $data);
    }

    public function medical() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        $data = [
            'title' => 'Medical Records',
            'medical_records' => [] // $this->trainerModel->getMedicalRecords($_SESSION['user_id'])
        ];

        $this->view('trainer/medical', $data);
    }

    public function injuryReports() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        // Initialize medical model to get all medical records
        $medicalModel = $this->model('M_Medical');
        
        // Get all medical records from PlayerMedicalRecord table
        $medicalRecords = $medicalModel->getAllMedicalRecords();

        $data = [
            'title' => 'Injury Reports - All Players',
            'medical_records' => $medicalRecords
        ];

        $this->view('trainer/injury-reports', $data);
    }

    // URL-friendly method name for injury_reports route (underscore version)
    public function injury_reports() {
        return $this->injuryReports();
    }

    // Update verification status for medical records
    public function updateVerifyStatus() {
        header('Content-Type: application/json');
        error_log("=== updateVerifyStatus called ===");
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Error: Invalid request method");
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        error_log("POST data: " . print_r($_POST, true));

        // Validate input
        if (!isset($_POST['record_id']) || !isset($_POST['verify_status'])) {
            error_log("Error: Missing required fields");
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $recordId = intval($_POST['record_id']);
        $verifyStatus = trim($_POST['verify_status']);
        $verifyComments = trim($_POST['verify_comments'] ?? '');

        error_log("Record ID: $recordId, Status: $verifyStatus");

        // Validate verify status
        $allowedStatuses = ['pending', 'verified', 'rejected'];
        if (!in_array($verifyStatus, $allowedStatuses)) {
            error_log("Error: Invalid verification status: $verifyStatus");
            echo json_encode(['success' => false, 'message' => 'Invalid verification status']);
            return;
        }

        try {
            // Initialize medical model
            $medicalModel = $this->model('M_Medical');
            error_log("Medical model initialized");
            
            // Update verify status
            $result = $medicalModel->updateVerifyStatus($recordId, $verifyStatus, $verifyComments);
            error_log("Update result: " . ($result ? 'true' : 'false'));
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Verification status updated successfully',
                    'record_id' => $recordId,
                    'new_status' => $verifyStatus
                ]);
            } else {
                error_log("Error: Database execute returned false");
                echo json_encode(['success' => false, 'message' => 'Failed to update verification status. Database update returned false.']);
            }
        } catch (Exception $e) {
            error_log("Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // API methods for AJAX requests
    public function getSessionsData() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'trainer') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $sessions = $this->trainerModel->getSessionsForCalendar($_SESSION['user_id']);
        echo json_encode($sessions);
    }

    public function updateSession() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'trainer') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'session_id' => $_POST['session_id'],
                'trainer_id' => $_SESSION['user_id'],
                'session_type' => $_POST['session_type'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'duration' => $_POST['duration'],
                'player_id' => $_POST['player_id'] ?? null,
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'scheduled'
            ];

            if ($this->trainerModel->updateSession($data)) {
                echo json_encode(['success' => true, 'message' => 'Session updated successfully']);
            } else {
                echo json_encode(['error' => 'Failed to update session']);
            }
        }
    }

    public function deleteSession() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'trainer') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $session_id = $_POST['session_id'];
            
            if ($this->trainerModel->deleteSession($session_id, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Session deleted successfully']);
            } else {
                echo json_encode(['error' => 'Failed to delete session']);
            }
        }
    }

    // Helper methods
    private function getUpcomingSessions(int $trainerId): array {
        $sessionModel = $this->model('M_Session');
        $sessions = $sessionModel->getSessionsByCoach($trainerId);
        $today = date('Y-m-d');

        $upcoming = array_values(array_filter($sessions, function($s) use ($today) {
            return ($s->Date ?? '') >= $today && strtolower($s->Status ?? '') !== 'cancelled';
        }));

        usort($upcoming, fn($a, $b) => strcmp(($a->Date ?? '') . ($a->StartTime ?? ''), ($b->Date ?? '') . ($b->StartTime ?? '')));
        return $upcoming;
    }

    private function getTodayStats(array $sessions): array {
        $today = date('Y-m-d');
        $todaySessions = array_values(array_filter($sessions, fn($s) => ($s->Date ?? '') === $today));

        $completedCount = count(array_filter($sessions, fn($s) => strtolower($s->Status ?? '') === 'completed'));
        $nonCancelledCount = count(array_filter($sessions, fn($s) => strtolower($s->Status ?? '') !== 'cancelled'));

        $uniquePlayers = [];
        $privateSessions = 0;
        foreach ($sessions as $session) {
            if (strtolower($session->SessionMode ?? '') === 'individual') {
                $privateSessions++;
            }

            if (!empty($session->players)) {
                foreach ($session->players as $player) {
                    if (isset($player->PlayerID)) {
                        $uniquePlayers[(int)$player->PlayerID] = true;
                    }
                }
            }
        }

        return [
            'total_sessions' => count($todaySessions),
            'active_players' => count($uniquePlayers),
            'private_sessions' => $privateSessions,
            'completion_rate' => $nonCancelledCount > 0 ? (int)round(($completedCount / $nonCancelledCount) * 100) : 0,
            'completed_sessions' => $completedCount,
            'upcoming_sessions' => count(array_filter($sessions, fn($s) => ($s->Date ?? '') > $today)),
        ];
    }

    // Profile Management
    public function profile() {
        // Get comprehensive user profile data
        $userModel = $this->model('M_Users');
        $userId = $_SESSION['user_id'] ?? 1;
        $userProfile = $userModel->getUserWithProfile($userId);
        
        $data = [
            'title' => 'My Profile',
            'user' => $userProfile
        ];
        
        $this->view('trainer/profile', $data);
    }

    // Update Profile
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'] ?? 1;
            
            // Update basic user info
            $userData = [
                'user_id' => $userId,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number']),
                'address' => trim($_POST['address']),
                'school' => trim($_POST['school']),
                'role' => $_SESSION['user_role'] ?? 'Trainer',
                'status' => 'active'
            ];
            
            // Update trainer-specific fields if provided
            $trainerData = [
                'user_id' => $userId,
                'certification' => trim($_POST['certification'] ?? ''),
                'experience_years' => intval($_POST['experience_years'] ?? 0),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0)
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
            
            // Validate date of birth if provided
            if (!empty($_POST['dateOfBirth'])) {
                $dob = new DateTime($_POST['dateOfBirth']);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                
                if ($age < 16) {
                    $errors[] = 'You must be at least 16 years old';
                } elseif ($age > 100) {
                    $errors[] = 'Please enter a valid date of birth';
                } elseif ($dob > $today) {
                    $errors[] = 'Date of birth cannot be in the future';
                } else {
                    // Add valid date of birth to userData
                    $userData['date_of_birth'] = $_POST['dateOfBirth'];
                }
            }
            
            if (empty($errors)) {
                if ($userModel->updateUser($userData) && $userModel->updateTrainerProfile($trainerData)) {
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
        
        redirect('trainer/profile');
    }

    // Deactivate Account
    public function deactivateAccount() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'] ?? 1;
            
            if ($userModel->suspendUser($userId, 9999)) {
                // Clear session and redirect to login
                session_destroy();
                flash('login_message', 'Your account has been deactivated successfully');
                redirect('login');
            } else {
                flash('profile_message', 'Failed to deactivate account', 'alert alert-danger');
                redirect('trainer/profile');
            }
        } else {
            redirect('trainer/profile');
        }
    }

    // Add nutrition plan
    public function addNutritionPlan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize model
            $nutritionModel = $this->model('M_NutritionPlan');
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'trainer_id' => $_SESSION['user_id'],
                'player_id' => $_POST['player_id'],
                'diet_details' => $_POST['diet_details'],
                'duration' => $_POST['duration'],
                'status' => 'active'
            ];
            
            // Validate data
            if (empty($data['player_id']) || empty($data['diet_details']) || empty($data['duration'])) {
                flash('nutrition_message', 'All fields are required', 'alert alert-danger');
            } else {
                // Add nutrition plan
                if ($nutritionModel->addNutritionPlan($data)) {
                    flash('nutrition_message', 'Nutrition plan added successfully');
                } else {
                    flash('nutrition_message', 'Failed to add nutrition plan', 'alert alert-danger');
                }
            }
        }
        
        redirect('trainer/nutrition');
    }

    // Add supplement plan
    public function addSupplementPlan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize model
            $supplementModel = $this->model('M_SupplementPlan');
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'trainer_id' => $_SESSION['user_id'],
                'player_id' => $_POST['player_id'],
                'supplement_details' => $_POST['supplement_details'],
                'dosage' => $_POST['dosage'],
                'duration' => $_POST['duration'],
                'status' => 'active'
            ];
            
            // Validate data
            if (empty($data['player_id']) || empty($data['supplement_details']) || empty($data['dosage']) || empty($data['duration'])) {
                flash('supplement_message', 'All fields are required', 'alert alert-danger');
            } else {
                // Add supplement plan
                if ($supplementModel->addSupplementPlan($data)) {
                    flash('supplement_message', 'Supplement plan added successfully');
                } else {
                    flash('supplement_message', 'Failed to add supplement plan', 'alert alert-danger');
                }
            }
        }
        
        redirect('trainer/supplements');
    }

    // Upload/Update Profile Image
    public function uploadProfileImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
                echo json_encode(['success' => false, 'message' => 'No file was uploaded']);
                return;
            }
            
            $file = $_FILES['profile_image'];
            
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
            
            $maxFileSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxFileSize) {
                echo json_encode(['success' => false, 'message' => 'File size must be less than 2MB']);
                return;
            }
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG files are allowed']);
                return;
            }
            
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $userId = $_SESSION['user_id'] ?? 1;
            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            
            $projectRoot = dirname(APPROOT);
            $uploadDir = $projectRoot . '/public/uploads/profile_images/';
            
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                    return;
                }
            }
            
            $uploadPath = $uploadDir . $newFileName;
            
            try {
                $userModel = $this->model('M_Users');
                $oldImage = $userModel->getProfileImage($userId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $relativePath = 'uploads/profile_images/' . $newFileName;
                
                if ($userModel->updateProfileImage($userId, $relativePath)) {
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
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'] ?? 1;
            
            try {
                $userModel = $this->model('M_Users');
                $imagePath = $userModel->getProfileImage($userId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            if ($imagePath) {
                $projectRoot = dirname(APPROOT);
                $fullPath = $projectRoot . '/public/' . $imagePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
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

    // ==================== AVAILABLE SLOTS ====================

    public function available_slots() {
        $sessionModel = $this->model('M_Session');
        $data = [
            'title'     => 'Available Slots - Trainer Panel',
            'trainerId' => $_SESSION['user_id'],
            'slots'     => $sessionModel->getOpenSlots(['type' => 'Physical Training']),
        ];
        $this->view('trainer/available_slots', $data);
    }

    public function claim_slot() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST required']);
            exit;
        }
        $sessionId = intval($_POST['session_id'] ?? 0);
        if (!$sessionId) {
            echo json_encode(['success' => false, 'message' => 'Invalid session ID']);
            exit;
        }
        $sessionModel = $this->model('M_Session');
        if ($sessionModel->isSlotClaimed($sessionId)) {
            echo json_encode(['success' => false, 'message' => 'This slot has already been claimed']);
            exit;
        }
        $result = $sessionModel->claimSlot($sessionId, $_SESSION['user_id']);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Slot claimed successfully' : 'Failed to claim slot'
        ]);
        exit;
    }
}
