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
            redirect('');
        }

        $trainerId = (int)$_SESSION['user_id'];
        $sessions = $this->getTrainerSlotSessions(
            $trainerId,
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d', strtotime('+60 days'))
        );

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
        $data = [
            'title' => 'Schedules Management',
            'schedules' => [] // $this->trainerModel->getSchedules($_SESSION['user_id'])
        ];

        $this->view('trainer/schedules', $data);
    }

    public function bookings() {
        redirect('staffslots/calendar');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Add Session  — GET shows form, POST processes it  (/trainer/addSession)
    // ──────────────────────────────────────────────────────────────────────────
    public function addSession() {
        redirect('staffslots/private_session');
    }

    public function tournaments() {
        requireAuth(['Trainer']);
        $M_Tournament = $this->model('M_Tournament');
        $data = [
            'title'       => 'Tournaments',
            'tournaments' => $M_Tournament->getPublicTournaments(),
        ];
        $this->view('trainer/tournaments/index', $data);
    }

    public function tournament_detail($id) {
        requireAuth(['Trainer']);
        $id = (int)$id;
        $M_Tournament = $this->model('M_Tournament');
        $M_TTR        = $this->model('M_TrainerTournamentRecommendation');

        $tournament = $M_Tournament->getTournamentById($id);
        if (!$tournament) {
            flash('tournament_message', 'Tournament not found.', 'alert alert-danger');
            redirect('trainer/tournaments');
        }

        $showTeam = in_array(strtolower((string)($tournament->Status ?? '')), ['team_announced', 'ongoing', 'completed'], true)
            || !empty($tournament->IsTeamAnnounced);

        $trainerId = $_SESSION['user_id'];
        $data = [
            'title'      => $tournament->Name,
            'tournament' => $tournament,
            'team'       => $showTeam ? $M_Tournament->getTeam($id) : [],
            'my_recs'    => $M_TTR->getRecommendationsByTrainer($trainerId),
        ];
        // Filter my_recs to this tournament only
        $data['my_recs_for_tournament'] = array_filter($data['my_recs'], function($r) use ($id) {
            return (int)$r->TournamentID === $id;
        });

        $this->view('trainer/tournaments/detail', $data);
    }

    public function recommend_player($id) {
        requireAuth(['Trainer']);
        $id           = (int)$id;
        $M_Tournament = $this->model('M_Tournament');
        $M_TTR        = $this->model('M_TrainerTournamentRecommendation');

        $tournament = $M_Tournament->getTournamentById($id);
        if (!$tournament) {
            flash('tournament_message', 'Tournament not found.', 'alert alert-danger');
            redirect('trainer/tournaments');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trainerId = $_SESSION['user_id'];
            $playerId  = (int)$_POST['player_id'];
            $fitnessRecommended = trim((string)($_POST['fitness_recommended'] ?? 'No'));
            $fitnessRecommended = strcasecmp($fitnessRecommended, 'Yes') === 0 ? 'Yes' : 'No';
            $result    = $M_TTR->addRecommendation($trainerId, $id, $playerId, [
                'fitness_recommended' => $fitnessRecommended,
                'comments' => trim($_POST['comments'] ?? ''),
            ]);
            if ($result['success']) {
                flash('tournament_message', $result['message'], 'alert alert-success');
            } else {
                flash('tournament_message', $result['message'], 'alert alert-danger');
            }
            redirect('trainer/tournament_detail/' . $id);
        }

        $data = [
            'title'      => 'Recommend Player – ' . $tournament->Name,
            'tournament' => $tournament,
            'players'    => $this->model('M_TournamentJoinRequest')->getRequestsByTournament($id),
        ];
        $this->view('trainer/tournaments/recommend', $data);
    }

    public function my_recommendations() {
        requireAuth(['Trainer']);
        $M_TTR = $this->model('M_TrainerTournamentRecommendation');
        $data  = [
            'title' => 'My Tournament Recommendations',
            'recs'  => $M_TTR->getRecommendationsByTrainer($_SESSION['user_id']),
        ];
        $this->view('trainer/tournaments/my_recommendations', $data);
    }

    public function nutrition() {
        // Use the dedicated Nutrition controller pages for all nutrition CRUD.
        // The legacy trainer/nutrition view contains placeholder modal logic.
        redirect('nutrition');
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
                'status'         => isset($_POST['status'])           ? trim(strtolower($_POST['status'])) : 'active',
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
            } else if (!in_array($data['status'], ['active', 'inactive'], true)) {
                flash('workout_message', 'Invalid workout status selected', 'alert alert-danger');
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
        $this->supplementsModuleRemoved();
        return;
    }

    private function supplementsModuleRemoved(): void {
        flash('nutrition_message', 'Supplements module has been removed.', 'alert alert-warning');
        redirect('trainer/nutrition');
    }

    public function medical() {
        $data = [
            'title' => 'Medical Records',
            'medical_records' => [] // $this->trainerModel->getMedicalRecords($_SESSION['user_id'])
        ];

        $this->view('trainer/medical', $data);
    }

    public function injuryReports() {
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

        // Validate verify status (UI uses 'verified'; DB enum uses 'approved')
        $allowedStatuses = ['pending', 'verified', 'approved', 'rejected'];
        if (!in_array($verifyStatus, $allowedStatuses, true)) {
            error_log("Error: Invalid verification status: $verifyStatus");
            echo json_encode(['success' => false, 'message' => 'Invalid verification status']);
            return;
        }

        $verifyStatus = strtolower($verifyStatus);
        $verifyStatusDb = ($verifyStatus === 'verified') ? 'approved' : $verifyStatus;
        $verifyStatusForUi = ($verifyStatusDb === 'approved') ? 'verified' : $verifyStatusDb;

        try {
            // Initialize medical model
            $medicalModel = $this->model('M_Medical');
            error_log("Medical model initialized");
            
            // Update verify status
            $result = $medicalModel->updateVerifyStatus($recordId, $verifyStatusDb, $verifyComments);
            error_log("Update result: " . ($result ? 'true' : 'false'));
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Verification status updated successfully',
                    'record_id' => $recordId,
                    'new_status' => $verifyStatusForUi
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
        $sessions = $this->getTrainerSlotSessions(
            $trainerId,
            date('Y-m-d'),
            date('Y-m-d', strtotime('+60 days'))
        );
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

    private function getTrainerSlotSessions(int $trainerId, string $from, string $to): array {
        $slotStaffModel = $this->model('M_SlotStaff');
        $occurrences = $slotStaffModel->getMyOccurrences($trainerId, 'trainer', $from, $to);
        $sessions = [];

        foreach ($occurrences as $occurrence) {
            $bookings = $slotStaffModel->getBookingsForOccurrence((int) $occurrence->OccurrenceID);
            $sessions[] = $this->mapSlotOccurrenceToTrainerSession($occurrence, $bookings);
        }

        usort($sessions, fn($a, $b) => strcmp(($a->Date ?? '') . ($a->StartTime ?? ''), ($b->Date ?? '') . ($b->StartTime ?? '')));
        return $sessions;
    }

    private function mapSlotOccurrenceToTrainerSession(object $occurrence, array $bookings): object {
        $session = new stdClass();
        $session->SessionID = (int) ($occurrence->OccurrenceID ?? 0);
        $session->Name = (string) ($occurrence->SessionName ?? 'Session');
        $session->Date = (string) ($occurrence->OccurrenceDate ?? '');
        $session->StartTime = (string) ($occurrence->StartTime ?? '00:00:00');
        $session->EndTime = (string) ($occurrence->EndTime ?? '00:00:00');
        $session->Location = (string) ($occurrence->FacilityName ?? $occurrence->SlotLabel ?? 'Academy');
        $session->SessionType = $this->formatTrainerSlotType((string) ($occurrence->SlotType ?? 'program'));
        $session->SessionMode = in_array(($occurrence->SlotType ?? ''), ['private', 'facility_only'], true) ? 'Individual' : 'Group';
        $session->MaxParticipants = $occurrence->MaxSlots ?? null;
        $session->ParticipantCount = count(array_filter($bookings, fn($booking) => strtolower((string) ($booking->Status ?? '')) !== 'cancelled'));
        $session->Status = $this->normalizeTrainerOccurrenceStatus($occurrence, $bookings);
        $session->players = array_values(array_map(function($booking) {
            return (object) [
                'PlayerID' => (int) ($booking->PlayerID ?? 0),
                'Name' => (string) ($booking->PlayerName ?? ''),
                'Status' => (string) ($booking->Status ?? 'confirmed'),
            ];
        }, array_filter($bookings, fn($booking) => strtolower((string) ($booking->Status ?? '')) !== 'cancelled')));

        return $session;
    }

    private function normalizeTrainerOccurrenceStatus(object $occurrence, array $bookings): string {
        $occurrenceStatus = strtolower((string) ($occurrence->Status ?? 'scheduled'));
        if ($occurrenceStatus === 'cancelled') {
            return 'cancelled';
        }
        if ($occurrenceStatus === 'completed') {
            return 'completed';
        }

        $today = date('Y-m-d');
        $finalized = array_filter($bookings, fn($booking) => in_array(strtolower((string) ($booking->Status ?? '')), ['attended', 'missed'], true));
        $nonCancelled = array_filter($bookings, fn($booking) => strtolower((string) ($booking->Status ?? '')) !== 'cancelled');

        if (($occurrence->OccurrenceDate ?? '') < $today && !empty($nonCancelled) && count($finalized) === count($nonCancelled)) {
            return 'completed';
        }

        if (($occurrence->OccurrenceDate ?? '') > $today) {
            return 'upcoming';
        }

        return 'active';
    }

    private function formatTrainerSlotType(string $slotType): string {
        return match (strtolower($slotType)) {
            'private' => 'Private Training',
            'facility_only' => 'Facility Booking',
            default => 'Program Session',
        };
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
                'firstName' => trim($_POST['firstName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number'] ?? $_POST['phone'] ?? ''),
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
                    $_SESSION['user_name'] = trim($userData['firstName'] . ' ' . $userData['lastName']);
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
                destroyUserSession();
                redirect('');
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
        $this->supplementsModuleRemoved();
        return;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize model
            $supplementModel = $this->model('M_SupplementPlan');
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            [$errors, $data] = $this->validateSupplementPlan($_POST, $supplementModel);

            if (!empty($errors)) {
                $_SESSION['supplement_form_errors'] = $errors;
                $_SESSION['supplement_form_old'] = $data;
                $firstError = reset($errors) ?: 'Please review the highlighted supplement fields.';
                flash('supplement_message', $firstError, 'alert alert-danger');
                redirect('trainer/supplements');
                return;
            }

            $data['trainer_id'] = $_SESSION['user_id'];

            if (($data['assignment_mode'] ?? 'individual') === 'group') {
                $data['player_ids'] = $supplementModel->getPlayerIdsByGroup($data['player_group'] ?? '');
                if (empty($data['player_ids'])) {
                    $_SESSION['supplement_form_errors'] = ['player_group' => 'No players were found for the selected group.'];
                    $_SESSION['supplement_form_old'] = $data;
                    flash('supplement_message', 'No players were found for the selected group.', 'alert alert-danger');
                    redirect('trainer/supplements');
                    return;
                }
            }

            if ($supplementModel->createPlan($data)) {
                flash('supplement_message', 'Supplement plan added successfully');
            } else {
                flash('supplement_message', 'Failed to add supplement plan', 'alert alert-danger');
            }
        }
        
        redirect('trainer/supplements');
    }

    // Update supplement plan
    public function updateSupplementPlan($id) {
        $this->supplementsModuleRemoved();
        return;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $supplementModel = $this->model('M_SupplementPlan');
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            [$errors, $data] = $this->validateSupplementPlan($_POST, $supplementModel);

            if (!empty($errors)) {
                $_SESSION['supplement_form_errors'] = $errors;
                $_SESSION['supplement_form_old'] = $data;
                $firstError = reset($errors) ?: 'Please review the highlighted supplement fields.';
                flash('supplement_message', $firstError, 'alert alert-danger');
                redirect('trainer/supplements');
                return;
            }

            $existingPlan = $supplementModel->getPlanById((int)$id, $_SESSION['user_id']);
            if (!$existingPlan) {
                flash('supplement_message', 'Plan not found or access denied.', 'alert alert-danger');
                redirect('trainer/supplements');
                return;
            }

            if (($data['assignment_mode'] ?? 'individual') === 'group') {
                $data['player_ids'] = $supplementModel->getPlayerIdsByGroup($data['player_group'] ?? '');
                if (empty($data['player_ids'])) {
                    $_SESSION['supplement_form_errors'] = ['player_group' => 'No players were found for the selected group.'];
                    $_SESSION['supplement_form_old'] = $data;
                    flash('supplement_message', 'No players were found for the selected group.', 'alert alert-danger');
                    redirect('trainer/supplements');
                    return;
                }
            }

            $data['plan_id'] = (int)$id;

            if ($supplementModel->updatePlan($data)) {
                flash('supplement_message', 'Supplement plan updated successfully');
            } else {
                flash('supplement_message', 'Failed to update supplement plan', 'alert alert-danger');
            }
        }

        redirect('trainer/supplements');
    }

    // Delete supplement plan
    public function deleteSupplementPlan($id) {
        $this->supplementsModuleRemoved();
        return;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('trainer/supplements');
            return;
        }

        $supplementModel = $this->model('M_SupplementPlan');
        if ($supplementModel->deletePlan((int)$id, $_SESSION['user_id'])) {
            flash('supplement_message', 'Supplement plan deleted successfully');
        } else {
            flash('supplement_message', 'Delete failed or plan not found.', 'alert alert-danger');
        }

        redirect('trainer/supplements');
    }

    private function getSupplementPlanOptions(): array {
        return [
            'Strength & Muscle Gain Plan',
            'Endurance & Performance Plan',
            'Recovery Plan',
            'General Health Plan',
            'Hydration Support Plan',
            'Injury Recovery Plan',
        ];
    }

    private function getSupplementPlanLibrary(): array {
        return [
            'Strength & Muscle Gain Plan' => [
                'category' => 'strength',
                'dosage' => 'Whey Protein: 25g post-workout; Creatine: 5g daily; Multivitamin: 1 tablet daily',
                'details' => "Goal: Support lean muscle gain and strength development.\n\nIncludes:\n- Whey Protein\n- Creatine\n- Multivitamins\n\nGuidelines:\n- Take protein after training.\n- Creatine should be taken daily, even on rest days.\n- Multivitamin with breakfast.",
            ],
            'Endurance & Performance Plan' => [
                'category' => 'performance',
                'dosage' => 'Electrolytes during sessions; BCAAs during long sessions; Energy gels optional',
                'details' => "Goal: Improve endurance and maintain intensity across long training blocks and match play.\n\nIncludes:\n- Electrolytes\n- BCAAs\n- Energy gels (optional)\n\nGuidelines:\n- Use electrolytes during heat or heavy sweat loss.\n- BCAAs can be used during prolonged sessions.\n- Energy gels are optional for match-day fuel.",
            ],
            'Recovery Plan' => [
                'category' => 'recovery',
                'dosage' => 'Protein: 20-30g post-session; Omega-3 daily; Magnesium at night',
                'details' => "Goal: Improve recovery between sessions and reduce muscle fatigue.\n\nIncludes:\n- Protein\n- Omega-3\n- Magnesium\n\nGuidelines:\n- Take protein shortly after exercise.\n- Use omega-3 with meals.\n- Magnesium is best taken in the evening.",
            ],
            'General Health Plan' => [
                'category' => 'health',
                'dosage' => 'Multivitamin: 1 tablet daily; Vitamin D as prescribed; Fish Oil with meals',
                'details' => "Goal: Maintain general wellness and fill common micronutrient gaps.\n\nIncludes:\n- Multivitamins\n- Vitamin D\n- Fish Oil\n\nGuidelines:\n- Keep the routine simple and consistent.\n- Take with meals to reduce stomach upset.",
            ],
            'Hydration Support Plan' => [
                'category' => 'hydration',
                'dosage' => 'Electrolytes only: before, during, and after sessions as needed',
                'details' => "Goal: Prevent dehydration and maintain performance in hot conditions.\n\nIncludes:\n- Electrolytes only\n\nGuidelines:\n- Use before intense outdoor sessions.\n- Rehydrate during and after training.",
            ],
            'Injury Recovery Plan' => [
                'category' => 'injury',
                'dosage' => 'Collagen daily; Vitamin C with food; Omega-3 with meals',
                'details' => "Goal: Support tissue repair and rehabilitation after injury.\n\nIncludes:\n- Collagen\n- Vitamin C\n- Omega-3\n\nGuidelines:\n- Use consistently during rehab.\n- Combine with physiotherapy and rest recommendations.",
            ],
        ];
    }

    private function validateSupplementPlan(array $post, $supplementModel): array {
        $library = $this->getSupplementPlanLibrary();
        $defaultPlanName = array_key_first($library) ?: '';
        $planName = trim(htmlspecialchars($post['supplement_plan_name'] ?? '', ENT_QUOTES, 'UTF-8'));
        if ($planName === '' && $defaultPlanName !== '') {
            $planName = $defaultPlanName;
        }
        if ($planName !== '' && !array_key_exists($planName, $library) && $defaultPlanName !== '') {
            $planName = $defaultPlanName;
        }
        $assignmentMode = trim(strtolower($post['assignment_mode'] ?? 'individual'));
        if (!in_array($assignmentMode, ['individual', 'group'], true)) {
            $assignmentMode = 'individual';
        }

        $rawPlayerIds = $post['player_ids'] ?? [];
        if (!is_array($rawPlayerIds)) {
            $rawPlayerIds = [];
        }
        $playerIds = [];
        foreach ($rawPlayerIds as $playerId) {
            $playerId = (int)$playerId;
            if ($playerId > 0) {
                $playerIds[] = $playerId;
            }
        }
        if (empty($playerIds) && !empty($post['player_id'])) {
            $playerIds[] = (int)$post['player_id'];
        }

        $playerIds = array_values(array_unique($playerIds));
        $playerGroup = trim($post['player_group'] ?? '');
        $notes = trim(htmlspecialchars($post['notes'] ?? '', ENT_QUOTES, 'UTF-8'));
        $details = trim(htmlspecialchars($post['supplement_details'] ?? '', ENT_QUOTES, 'UTF-8'));
        $dosage = trim(htmlspecialchars($post['dosage'] ?? '', ENT_QUOTES, 'UTF-8'));
        $duration = trim($post['duration'] ?? '');
        $status = trim(strtolower($post['status'] ?? 'active'));
        $createdDate = trim($post['created_date'] ?? '');

        $errors = [];

        if ($assignmentMode === 'group') {
            if ($playerGroup === '') {
                $errors['player_group'] = 'Please select a player group.';
            }
        } elseif (empty($playerIds)) {
            $errors['player_ids'] = 'Please select at least one player.';
        }

        if ($details === '' && isset($library[$planName])) {
            $details = $library[$planName]['details'];
        }

        if ($dosage === '' && isset($library[$planName])) {
            $dosage = $library[$planName]['dosage'];
        }

        // Dosage is template-driven in the UI; keep it optional if a template has no dosage.
        if ($dosage === '') {
            $dosage = null;
        }

        if ($details === '' && $planName !== '') {
            $details = 'Supplement template: ' . $planName;
        }

        if ($notes !== '' && strlen($notes) > 1000) {
            $errors['notes'] = 'Notes must be 1000 characters or fewer.';
        }

        if ($duration === '') {
            $errors['duration'] = 'Duration is required.';
        } elseif (!ctype_digit($duration) || (int)$duration <= 0) {
            $errors['duration'] = 'Duration must be a whole number greater than 0.';
        }

        if ($createdDate === '') {
            $errors['created_date'] = 'Created date is required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $createdDate)) {
            $errors['created_date'] = 'Invalid date format.';
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        $fields = [
            'supplement_plan_name' => $planName,
            'assignment_mode' => $assignmentMode,
            'player_ids' => $playerIds,
            'player_group' => $playerGroup,
            'supplement_details' => $details,
            'dosage' => $dosage,
            'notes' => $notes,
            'duration' => $duration,
            'status' => $status,
            'created_date' => $createdDate,
        ];

        $fields['player_id'] = !empty($playerIds) ? (int)$playerIds[0] : 0;

        return [$errors, $fields];
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

}
