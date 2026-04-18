<?php
class Coach extends Controller {
    public function __construct() {
        // Check authentication for all coach pages
        requireAuth(['Coach']);
    }
    
    public function index() {
        // Redirect to dashboard by default
        redirect('coach/dashboard');
    }
    
    public function dashboard() {
        $coachId = $_SESSION['user_id'];
        $slotStaffModel = $this->model('M_SlotStaff');

        $getOccurrenceStatusMeta = static function($status): array {
            $statusKey = strtolower(trim((string) ($status ?? 'scheduled')));
            $statusKey = $statusKey !== '' ? $statusKey : 'scheduled';

            return [
                'key' => $statusKey,
                'label' => ucfirst(str_replace('_', ' ', $statusKey)),
                'class' => $statusKey,
            ];
        };

        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $dashboardOccurrences = $slotStaffModel->getMyOccurrences($coachId, 'coach', $today, $nextWeek);
        $dashboardOccurrences = array_values(array_filter($dashboardOccurrences, function($occurrence) {
            return strtolower((string)($occurrence->Status ?? '')) !== 'cancelled';
        }));

        $upcomingBookings = [];
        foreach ($dashboardOccurrences as $occurrence) {
            $slotType = strtolower((string)($occurrence->SlotType ?? 'program'));
            $sessionType = $slotType === 'private' ? 'private' : 'normal';
            $attendanceAvailable = $sessionType === 'normal' && $this->hasOccurrenceEnded($occurrence);

            if ($sessionType === 'private') {
                $participants = $slotStaffModel->getBookingsForOccurrence((int)$occurrence->OccurrenceID);
                $participantNames = array_map(function($booking) {
                    return $booking->PlayerName;
                }, $participants);
                $participantSummary = !empty($participantNames) ? implode(', ', $participantNames) : 'No participants yet';
                $currentParticipants = count($participants);
            } else {
                $participants = $slotStaffModel->getGroupParticipantsForOccurrence((int)$occurrence->OccurrenceID);
                $participantCount = count($participants);
                $participantSummary = $participantCount > 0
                    ? $participantCount . ' eligible players'
                    : 'No eligible players';
                $currentParticipants = $participantCount;
            }

            $upcomingBookings[] = [
                'id' => (int)$occurrence->OccurrenceID,
                'player_name' => $participantSummary,
                'session_name' => $occurrence->SessionName ?? 'Slot Session',
                'session_type' => $sessionType,
                'date' => $occurrence->OccurrenceDate,
                'time' => !empty($occurrence->StartTime) ? date('H:i', strtotime($occurrence->StartTime)) : 'TBD',
                'start_time' => $occurrence->StartTime ?? '',
                'end_time' => $occurrence->EndTime ?? '',
                'duration' => $this->calculateDuration($occurrence->StartTime ?? '', $occurrence->EndTime ?? ''),
                'facility' => $occurrence->FacilityName ?? 'TBA',
                'equipment' => '',
                'max_participants' => (int)($occurrence->MaxSlots ?? $occurrence->OccMax ?? 0),
                'current_participants' => $currentParticipants,
                'status' => $occurrence->Status ?? 'scheduled',
                'status_meta' => $getOccurrenceStatusMeta($occurrence->Status ?? 'scheduled'),
                'attendance_enabled' => $attendanceAvailable,
                'attendance_label' => $sessionType === 'normal' ? 'Eligible Players' : 'View Bookings',
                'attendance_note' => $attendanceAvailable ? 'Mark attendance' : 'Available after session ends',
                'price' => 0,
            ];
        }

        $pastFrom = date('Y-m-d', strtotime('-30 days'));
        $pastTo = date('Y-m-d', strtotime('-1 day'));
        $pastSessions = $this->getCoachSlotSessions($coachId, $pastFrom, $pastTo, true);
        $pastSessions = array_values(array_filter($pastSessions, function($session) {
            $status = strtolower((string) ($session->Status ?? ''));
            $sessionDate = (string) ($session->Date ?? '');
            return in_array($status, ['completed', 'cancelled'], true) || ($sessionDate !== '' && $sessionDate < date('Y-m-d'));
        }));
        $pastSessions = array_reverse($pastSessions);

        $todaySessionsCount = count(array_filter($upcomingBookings, function($booking) use ($today) {
            return ($booking['date'] ?? '') === $today;
        }));
        $privateSessions = count(array_filter($upcomingBookings, function($booking) {
            return ($booking['session_type'] ?? '') === 'private';
        }));
        $groupSessions = count(array_filter($upcomingBookings, function($booking) {
            return ($booking['session_type'] ?? '') === 'normal';
        }));
        $totalSessions = count($upcomingBookings);
        
        $data = [
            'title' => 'Coach Dashboard - Elite Cricket Academy',
            'coachName' => $_SESSION['user_name'] ?? 'Coach',
            'coachType' => 'Coach',
            'coachId' => 'COACH_' . str_pad($coachId, 3, '0', STR_PAD_LEFT),
            'totalSessions' => $totalSessions,
            'todaySessions' => $todaySessionsCount,
            'privateSessions' => $privateSessions,
            'normalSessions' => $groupSessions,
            'upcomingBookings' => array_slice($upcomingBookings, 0, 5),
            'pastSessions' => array_slice($pastSessions, 0, 8),
            'allSessions' => [],
        ];
        
        $this->view('coach/dashboard', $data);
    }
    
    public function schedules() {
        redirect('coach/sessions');
    }
    
    public function bookings() {
        redirect('coach/sessions');
    }
    
    public function tournaments() {
        $M_Tournament = $this->model('M_Tournament');
        $data['tournaments'] = $M_Tournament->getPublicTournaments();
        $data['is_head_coach'] = $this->_isHeadCoach();
        $this->view('coach/tournaments/index', $data);
    }
    
    public function players() {
        $coachId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        
        // Get players assigned to this coach from DB
        $players = $userModel->getCoachAssignedPlayers($coachId);

        $ageGroupsMap = [];
        foreach ($players as $player) {
            $rawAgeGroups = array_filter(array_map('trim', explode(',', (string)($player->AssignmentAgeGroups ?? ''))));

            if (empty($rawAgeGroups)) {
                $ageGroupsMap['open'] = 'Open';
                continue;
            }

            foreach ($rawAgeGroups as $ageGroup) {
                $ageGroupsMap[strtolower($ageGroup)] = $ageGroup;
            }
        }

        $ageGroups = array_values($ageGroupsMap);
        sort($ageGroups, SORT_NATURAL | SORT_FLAG_CASE);

        $coachProfiles = $userModel->getAllCoachProfiles();
        $coachProfile = null;
        foreach ($coachProfiles as $profile) {
            if ((int)($profile->coach_id ?? 0) === (int)$coachId) {
                $coachProfile = $profile;
                break;
            }
        }

        $coachAssignments = [];
        foreach ($userModel->getCoachSkillAgeGroupAssignments() as $assignment) {
            if ((int)($assignment->CoachID ?? 0) !== (int)$coachId) {
                continue;
            }

            $ageGroups = array_values(array_filter(array_map('trim', explode(',', (string)($assignment->AgeGroups ?? '')))));
            $coachAssignments[] = [
                'skill' => strtolower((string)($assignment->CoachingType ?? '')),
                'skill_label' => ucwords(str_replace(['_', '-'], ' ', (string)($assignment->CoachingType ?? ''))),
                'age_groups' => $ageGroups,
            ];
        }

        if (empty($coachAssignments) && !empty($coachProfile->specialization)) {
            $coachAssignments[] = [
                'skill' => strtolower((string)$coachProfile->specialization),
                'skill_label' => (string)$coachProfile->specialization,
                'age_groups' => []
            ];
        }
        
        // Fetch achievements from database
        $achievementModel = $this->model('M_Achievement');
        $achievements = $achievementModel->getAllAchievementsWithPlayerInfo();
        
        // Calculate stats
        $totalPlayers = count($players);
        $activePlayers = count(array_filter($players, function($p) { return $p->Status == 'active'; }));
        $inactivePlayers = $totalPlayers - $activePlayers;
        
        $data = [
            'title' => 'Player Management - Coach Dashboard',
            'players' => $players,
            'achievements' => $achievements,
            'coachProfile' => $coachProfile,
            'coachAssignments' => $coachAssignments,
            'ageGroups' => $ageGroups,
            'totalPlayers' => $totalPlayers,
            'activePlayers' => $activePlayers,
            'inactivePlayers' => $inactivePlayers
        ];
        $this->view('coach/players', $data);
    }

    public function performance() {
        $coachId = (int)($_SESSION['user_id'] ?? 0);
        $userModel = $this->model('M_Users');
        $performanceModel = $this->model('M_Performance');

        $players = $userModel->getCoachAssignedPlayers($coachId);

        $ageGroupsMap = [];
        foreach ($players as $player) {
            $rawAgeGroups = array_filter(array_map('trim', explode(',', (string)($player->AssignmentAgeGroups ?? ''))));
            if (empty($rawAgeGroups)) {
                $ageGroupsMap['open'] = 'Open';
                continue;
            }
            foreach ($rawAgeGroups as $ageGroup) {
                $ageGroupsMap[strtolower($ageGroup)] = $ageGroup;
            }
        }

        $ageGroups = array_values($ageGroupsMap);
        sort($ageGroups, SORT_NATURAL | SORT_FLAG_CASE);

        $overallStatsByPlayerId = [];
        foreach ($players as $player) {
            $playerId = (int)($player->PlayerID ?? 0);
            if ($playerId <= 0) {
                continue;
            }
            $overallStatsByPlayerId[$playerId] = $performanceModel->getOverallStats($playerId);
        }

        $data = [
            'title' => 'Performance - Coach Dashboard',
            'players' => $players,
            'overallStatsByPlayerId' => $overallStatsByPlayerId,
            'ageGroups' => $ageGroups,
        ];

        $this->view('coach/performance', $data);
    }

    public function performance_details($playerId = null) {
        $coachId = (int)($_SESSION['user_id'] ?? 0);
        $playerId = (int)($playerId ?? 0);

        if ($playerId <= 0) {
            redirect('coach/performance');
            return;
        }

        $userModel = $this->model('M_Users');
        $performanceModel = $this->model('M_Performance');

        $assignedPlayers = $userModel->getCoachAssignedPlayers($coachId);
        $selectedPlayer = null;
        foreach ($assignedPlayers as $player) {
            if ((int)($player->PlayerID ?? 0) === $playerId) {
                $selectedPlayer = $player;
                break;
            }
        }

        if (!$selectedPlayer) {
            redirect('coach/performance');
            return;
        }

        $overall = $performanceModel->getOverallStats($playerId);
        $matchHistory = $performanceModel->getMatchHistory($playerId, 50);

        $data = [
            'title' => 'Performance Details - Coach Dashboard',
            'player' => $selectedPlayer,
            'overall' => $overall,
            'matchHistory' => $matchHistory,
        ];

        $this->view('coach/performance_details', $data);
    }
    
    public function recommendations() {
        redirect('coach/players');
    }
    
    public function medical() {
        redirect('coach/health');
    }
    
    public function health() {
        // Fetch real medical records from database
        $medicalModel = $this->model('M_Medical');
        $medicalRecords = $medicalModel->getAllMedicalRecordsWithPlayerInfo();
        
        // Calculate stats from real data
        $totalRecords = count($medicalRecords);
        $recoveredCount = 0;
        $injuredCount = 0;
        $severeCount = 0;
        $pendingCount = 0;
        
        foreach ($medicalRecords as $record) {
            $status = strtolower($record->RecoveryStatus ?? '');
            if ($status === 'recovered') {
                $recoveredCount++;
            } else {
                $injuredCount++;
            }
            if ($status === 'chronic' || (isset($record->RestDaysNeeded) && $record->RestDaysNeeded > 14)) {
                $severeCount++;
            }
            $verifyStatus = strtolower($record->verifyStatus ?? 'pending');
            if ($verifyStatus === 'pending') {
                $pendingCount++;
            }
        }

        $data = [
            'title' => 'Health & Injury Monitoring - Elite Cricket Academy',
            'medicalRecords' => $medicalRecords,
            'injuredCount' => $injuredCount,
            'recoveredCount' => $recoveredCount,
            'severeCount' => $severeCount,
            'pendingCount' => $pendingCount,
            'healthChartData' => [
                'healthData' => [
                    $recoveredCount,
                    max(0, $injuredCount - $severeCount),
                    $severeCount,
                ],
            ],
        ];
        $this->view('coach/health', $data);
    }
    
    public function events() {
        // Get event model
        $eventModel = $this->model('Event');
        
        // Get upcoming and past events
        $upcomingEvents = $eventModel->getUpcomingEvents(50);
        $pastEvents = $eventModel->getPastEvents(50);
        
        $data = [
            'title' => 'Events & Tournaments - Coach Dashboard',
            'upcoming_events' => $upcomingEvents,
            'past_events' => $pastEvents
        ];
        
        $this->view('coach/events', $data);
    }
    
    // Update verification status for medical records
    public function updateVerifyStatus() {
        header('Content-Type: application/json');
        error_log("=== Coach updateVerifyStatus called ===");
        
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
    
    public function communication() {
        $coachId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        
        // Get feedback received by this coach
        $feedbacks = $userModel->getFeedbackForUser($coachId);
        
        $data = [
            'title' => 'Communication & Feedback - Elite Cricket Academy',
            'feedbacks' => $feedbacks
        ];
        $this->view('coach/communication', $data);
    }
    
    public function reports() {
        $coachId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        $medicalModel = $this->model('M_Medical');
        
        // Get real stats
        $players = $userModel->getPlayersAssignedToCoach($coachId);
        $allSessions = $this->getCoachSlotSessions(
            $coachId,
            date('Y-m-d', strtotime('-365 days')),
            date('Y-m-d', strtotime('+365 days'))
        );
        $medicalRecords = $medicalModel->getAllMedicalRecordsWithPlayerInfo();
        
        $totalPlayers = count($players);
        $totalSessions = count($allSessions);
        $completedSessions = count(array_filter($allSessions, function($s) { return ($s->Status ?? '') === 'completed'; }));
        $activeSessions = count(array_filter($allSessions, function($s) { return ($s->Status ?? '') === 'active'; }));
        
        // Session type breakdown
        $privateSessions = count(array_filter($allSessions, function($s) { return ($s->SessionMode ?? '') === 'Private'; }));
        $groupSessions = count(array_filter($allSessions, function($s) { return ($s->SessionMode ?? '') === 'Group'; }));
        
        // Medical stats
        $totalMedical = count($medicalRecords);
        $recoveredCount = count(array_filter($medicalRecords, function($m) { 
            return isset($m->RecoveryStatus) && strtolower($m->RecoveryStatus) == 'recovered'; 
        }));
        
        $data = [
            'title' => 'Reports & Analytics - Elite Cricket Academy',
            'totalPlayers' => $totalPlayers,
            'totalSessions' => $totalSessions,
            'completedSessions' => $completedSessions,
            'activeSessions' => $activeSessions,
            'privateSessions' => $privateSessions,
            'groupSessions' => $groupSessions,
            'totalMedical' => $totalMedical,
            'recoveredCount' => $recoveredCount,
            'players' => $players,
            'sessions' => $allSessions
        ];
        $this->view('coach/reports', $data);
    }
    
    public function requests() {
        $coachId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        
        // Get feedback/requests for this coach
        $feedbacks = $userModel->getFeedbackForUser($coachId);
        
        $pendingCount = count(array_filter($feedbacks, function($f) { return $f->Status == 'pending'; }));
        $reviewedCount = count(array_filter($feedbacks, function($f) { return $f->Status == 'reviewed'; }));
        $resolvedCount = count(array_filter($feedbacks, function($f) { return $f->Status == 'resolved'; }));
        
        $data = [
            'title' => 'Requests & Approvals - Elite Cricket Academy',
            'feedbacks' => $feedbacks,
            'pendingCount' => $pendingCount,
            'reviewedCount' => $reviewedCount,
            'resolvedCount' => $resolvedCount
        ];
        $this->view('coach/requests', $data);
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
        
        $this->view('coach/profile', $data);
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
                'role' => $_SESSION['user_role'] ?? 'Coach',
                'status' => 'active'
            ];
            
            // Update coach-specific fields if provided
            $coachData = [
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
                if ($userModel->updateUser($userData) && $userModel->updateCoachProfile($coachData)) {
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
        
        redirect('coach/profile');
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
                redirect('coach/profile');
            }
        } else {
            redirect('coach/profile');
        }
    }

    // ==================== SESSION MANAGEMENT ====================
    
    // Display Sessions & Schedule Management Page
    public function sessions() {
        redirect('staffslots/calendar');
    }

    // Create New Session (POST from wizard)
    public function create_session() {
        $this->respondLegacySessionRedirect(
            'Legacy coach session creation has been retired. Use My Slot Sessions to create slot-based sessions.',
            'staffslots/private_session'
        );
    }

    // Get Session by ID (JSON)
    public function get_session($id) {
        $slotStaffModel = $this->model('M_SlotStaff');
        $occurrence = $slotStaffModel->getOccurrenceDetail((int) $id, (int) ($_SESSION['user_id'] ?? 0));

        if (!$occurrence) {
            echo json_encode([
                'success' => false,
                'message' => 'Session not found'
            ]);
            return;
        }

        $bookings = $slotStaffModel->getBookingsForOccurrence((int) $id);
        $session = $this->mapCoachOccurrenceToSession($occurrence, $bookings);
        $session->participants = $session->players;

        echo json_encode([
            'success' => true,
            'session' => $session
        ]);
    }

    // Get Calendar Sessions (JSON for FullCalendar)
    public function get_calendar_sessions() {
        $coachId = $_SESSION['user_id'] ?? 1;
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        $from = $start ?: date('Y-m-d', strtotime('-30 days'));
        $to = $end ?: date('Y-m-d', strtotime('+90 days'));
        $sessions = $this->getCoachSlotSessions($coachId, $from, $to);
        $events = $this->buildCoachCalendarEvents($sessions);

        echo json_encode($events);
    }

    // Get Session Statistics (JSON)
    public function get_session_stats() {
        $coachId = $_SESSION['user_id'] ?? 1;
        $sessions = $this->getCoachSlotSessions(
            $coachId,
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d', strtotime('+90 days'))
        );
        $stats = $this->buildCoachSessionStats($sessions);
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    // Get Filtered Sessions List (JSON)
    public function get_sessions_list() {
        header('Content-Type: application/json');
        
        $coachId = $_SESSION['user_id'] ?? null;
        
        // Debug logging
        error_log('=== GET SESSIONS LIST DEBUG ===');
        error_log('Session user_id: ' . ($coachId ?? 'NOT SET'));
        error_log('Session data: ' . print_r($_SESSION, true));
        
        if (!$coachId) {
            echo json_encode([
                'success' => false,
                'message' => 'User not logged in',
                'sessions' => []
            ]);
            return;
        }
        
        $filters = [
            'type' => $_GET['type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'dateFrom' => $_GET['dateFrom'] ?? '',
            'dateTo' => $_GET['dateTo'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        error_log('Filters: ' . print_r($filters, true));
        
        $scope = $_GET['scope'] ?? '';
        $from = $filters['dateFrom'] ?: date('Y-m-d', strtotime('-30 days'));
        $to = $filters['dateTo'] ?: date('Y-m-d', strtotime('+180 days'));
        $sessions = $this->getCoachSlotSessions($coachId, $from, $to, $scope === 'all');
        $sessions = $this->applyCoachSessionFilters($sessions, $filters);
        
        error_log('Found ' . count($sessions) . ' session(s) for coach ID: ' . $coachId);
        error_log('=== GET SESSIONS LIST END ===');
        
        echo json_encode([
            'success' => true,
            'sessions' => $sessions,
            'coachId' => $coachId,
            'count' => count($sessions)
        ]);
    }

    public function occurrence($id = null) {
        if (!$id) {
            redirect('coach/dashboard');
        }

        $slotStaffModel = $this->model('M_SlotStaff');
        $coachId = (int) ($_SESSION['user_id'] ?? 0);
        $occurrence = $slotStaffModel->getOccurrenceDetail((int) $id, $coachId);

        if (!$occurrence) {
            redirect('coach/dashboard');
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action_cancel'])) {
                $reason = trim((string) ($_POST['cancel_reason'] ?? ''));

                if ($reason === '') {
                    $error = 'A cancellation reason is required.';
                } else {
                    $result = $slotStaffModel->cancelOccurrence((int) $id, $reason, $coachId);

                    if ($result === true) {
                        $success = 'Session cancelled successfully.';
                        $occurrence = $slotStaffModel->getOccurrenceDetail((int) $id, $coachId);
                    } elseif ($result === 'already_cancelled') {
                        $error = 'This session is already cancelled.';
                    } elseif ($result === 'not_assigned') {
                        $error = 'You are not assigned to this session.';
                    } else {
                        $error = 'Could not cancel the session. Please try again.';
                    }
                }
            } elseif (isset($_POST['action_update_occurrence_status'])) {
                $status = trim((string) ($_POST['occurrence_status'] ?? ''));
                $reason = trim((string) ($_POST['occurrence_status_reason'] ?? ''));
                $result = $slotStaffModel->updateOccurrenceStatus((int) $id, $status, $coachId, $reason);

                if ($result === true) {
                    $success = 'Session occurrence status updated successfully.';
                    $occurrence = $slotStaffModel->getOccurrenceDetail((int) $id, $coachId);
                } elseif ($result === 'invalid_status') {
                    $error = 'That occurrence status is not allowed.';
                } elseif ($result === 'not_past') {
                    $error = 'Occurrence status can only be updated after the session has ended.';
                } elseif ($result === 'reason_required') {
                    $error = 'Please provide a reason when marking a session as cancelled.';
                } elseif ($result === 'not_assigned') {
                    $error = 'You are not assigned to this session.';
                } else {
                    $error = 'Could not update the occurrence status. Please try again.';
                }
            } elseif (isset($_POST['action_update_booking'])) {
                $bookingId = (int) ($_POST['booking_id'] ?? 0);
                $status = trim((string) ($_POST['booking_status'] ?? ''));

                if ($bookingId <= 0 || $status === '') {
                    $error = 'Please choose a valid booking status.';
                } else {
                    $result = $slotStaffModel->markAttendance($bookingId, $status, $coachId);

                    if ($result === true) {
                        $success = 'Booking status updated successfully.';
                    } elseif ($result === 'not_assigned') {
                        $error = 'You can only update bookings for your own slot sessions.';
                    } elseif ($result === 'not_found') {
                        $error = 'The selected booking could not be found.';
                    } elseif ($result === 'invalid_status') {
                        $error = 'That booking status is not allowed.';
                    } else {
                        $error = 'Could not update the booking status. Please try again.';
                    }
                }
            }
        }

        $data = [
            'title' => 'Coach Session Detail',
            'role' => 'Coach',
            'occurrence' => $occurrence,
            'bookings' => $slotStaffModel->getBookingsForOccurrence((int) $id),
            'error' => $error,
            'success' => $success,
        ];

        $this->view('staff/slots/occurrence', $data);
    }

    // Update Session
    public function edit_session($id = null) {
        $this->respondLegacySessionRedirect(
            'Legacy coach session editing has been retired. Manage slot sessions from the session detail page.',
            $id ? 'coach/occurrence/' . (int) $id : 'coach/dashboard'
        );
    }

    // Cancel Session
    public function cancel_session($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $reason = trim($_POST['reason'] ?? 'No reason provided');

            $slotStaffModel = $this->model('M_SlotStaff');
            $result = $slotStaffModel->cancelOccurrence((int) $id, $reason, (int) ($_SESSION['user_id'] ?? 0));

            if ($result === true) {
                // Send cancellation notifications
                $this->sendSessionNotifications($id, 'cancelled', $reason);

                echo json_encode([
                    'success' => true,
                    'message' => 'Session cancelled successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to cancel session'
                ]);
            }
        } else {
            redirect('coach/sessions');
        }
    }

    // Reschedule Session
    public function reschedule_session($id) {
        $this->respondLegacySessionRedirect(
            'Legacy coach rescheduling has been retired. Reschedule slot sessions from the slot calendar workflow.',
            $id ? 'staffslots/occurrence/' . (int) $id : 'staffslots/calendar'
        );
    }

    // Mark Attendance
    public function mark_attendance() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId = intval($_POST['session_id'] ?? 0);
            $attendanceData = $_POST['attendance'] ?? [];
            
            if (empty($sessionId) || empty($attendanceData)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid attendance data'
                ]);
                return;
            }
            
            $slotStaffModel = $this->model('M_SlotStaff');
            $bookings = $slotStaffModel->getBookingsForOccurrence($sessionId);
            $bookingsByPlayerId = [];
            foreach ($bookings as $booking) {
                $bookingsByPlayerId[(int) ($booking->PlayerID ?? 0)] = $booking;
            }
            $success = true;
            
            foreach ($attendanceData as $playerId => $data) {
                $booking = $bookingsByPlayerId[(int) $playerId] ?? null;
                if (!$booking) {
                    $success = false;
                    continue;
                }

                $result = $slotStaffModel->markAttendance(
                    (int) $booking->BookingID,
                    (string) ($data['status'] ?? ''),
                    (int) ($_SESSION['user_id'] ?? 1)
                );

                if ($result !== true) {
                    $success = false;
                }
            }
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Attendance marked successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to mark some attendance records'
                ]);
            }
        } else {
            redirect('coach/sessions');
        }
    }

    // Get Session Attendance (JSON)
    public function get_session_attendance($sessionId) {
        $slotStaffModel = $this->model('M_SlotStaff');
        $attendance = array_map(function($booking) {
            return (object) [
                'BookingID' => (int) ($booking->BookingID ?? 0),
                'PlayerID' => (int) ($booking->PlayerID ?? 0),
                'Name' => (string) ($booking->PlayerName ?? ''),
                'AttendanceStatus' => (string) ($booking->Status ?? 'confirmed'),
                'CreatedAt' => $booking->CreatedAt ?? null,
            ];
        }, $slotStaffModel->getBookingsForOccurrence((int) $sessionId));
        
        echo json_encode([
            'success' => true,
            'attendance' => $attendance
        ]);
    }

    public function get_session_roster($sessionId) {
        header('Content-Type: application/json');

        $occurrenceId = (int) $sessionId;
        if ($occurrenceId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid session id']);
            return;
        }

        $slotStaffModel = $this->model('M_SlotStaff');
        $occurrence = $slotStaffModel->getOccurrenceDetail($occurrenceId, (int) ($_SESSION['user_id'] ?? 0));
        if (!$occurrence) {
            echo json_encode(['success' => false, 'message' => 'Session not found']);
            return;
        }

        if (!$this->hasOccurrenceEnded($occurrence)) {
            echo json_encode(['success' => false, 'message' => 'Attendance roster becomes available after the session ends.']);
            return;
        }

        $roster = $slotStaffModel->getAttendanceRosterForOccurrence($occurrenceId);
        echo json_encode([
            'success' => true,
            'session' => [
                'id' => $occurrenceId,
                'name' => (string) ($occurrence->SessionName ?? 'Session'),
                'type' => (string) ($occurrence->SlotType ?? 'program'),
            ],
            'players' => $roster,
        ]);
    }

    public function save_session_attendance() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $occurrenceId = (int) ($_POST['session_id'] ?? 0);
        $presentIds = array_map('intval', $_POST['attendance_present'] ?? []);

        if ($occurrenceId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid session id']);
            return;
        }

        $slotStaffModel = $this->model('M_SlotStaff');
        $result = $slotStaffModel->saveAttendanceRoster($occurrenceId, $presentIds, (int) ($_SESSION['user_id'] ?? 0));

        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Attendance saved successfully']);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => is_string($result) ? $result : 'Failed to save attendance',
        ]);
    }

    private function hasOccurrenceEnded(object $occurrence): bool {
        $date = trim((string) ($occurrence->OccurrenceDate ?? ''));
        $endTime = trim((string) ($occurrence->EndTime ?? ''));

        if ($date === '' || $endTime === '') {
            return false;
        }

        $endTimestamp = strtotime($date . ' ' . $endTime);
        if ($endTimestamp === false) {
            return false;
        }

        return $endTimestamp <= time();
    }

    // Delete Session
    public function delete_session($id = null) {
        $this->respondLegacySessionRedirect(
            'Legacy coach deletion has been retired. Cancel slot sessions from the slot calendar instead.',
            $id ? 'staffslots/occurrence/' . (int) $id : 'staffslots/calendar'
        );
    }

    // Helper: Send Session Notifications
    private function sendSessionNotifications($sessionId, $action, $reason = '') {
        $slotStaffModel = $this->model('M_SlotStaff');
        $session = $slotStaffModel->getOccurrenceDetail((int) $sessionId, (int) ($_SESSION['user_id'] ?? 0));
        $participants = $slotStaffModel->getBookingsForOccurrence((int) $sessionId);
        
        if (!$session || empty($participants)) {
            return false;
        }
        
        $title = $session->SessionName ?? 'Session';
        $date = $session->OccurrenceDate ?? '';
        $time = $session->StartTime ?? '';
        
        // Prepare notification message
        $messages = [
            'created' => "New session '{$title}' has been scheduled for {$date} at {$time}",
            'updated' => "Session '{$title}' has been updated. Please check the details.",
            'cancelled' => "Session '{$title}' scheduled for {$date} has been cancelled." . ($reason ? " Reason: $reason" : ""),
            'rescheduled' => "Session '{$title}' has been rescheduled to {$date} at {$time}",
            'deleted' => "Session '{$title}' has been removed from the schedule."
        ];
        
        $message = $messages[$action] ?? 'Session notification';
        
        // Log notifications for each participant
        foreach ($participants as $participant) {
            $pid = $participant->PlayerID ?? ($participant['PlayerID'] ?? 'unknown');
            error_log("Notification to Player {$pid}: $message");
        }
        
        return true;
    }

    private function getCoachSlotSessions(int $coachId, string $from, string $to, bool $includeAll = false): array {
        $slotStaffModel = $this->model('M_SlotStaff');
        $role = $includeAll ? 'Coach' : 'coach';
        $occurrences = $slotStaffModel->getMyOccurrences($coachId, $role, $from, $to);
        $sessions = [];

        foreach ($occurrences as $occurrence) {
            $bookings = $slotStaffModel->getBookingsForOccurrence((int) $occurrence->OccurrenceID);
            $sessions[] = $this->mapCoachOccurrenceToSession($occurrence, $bookings);
        }

        return $sessions;
    }

    private function mapCoachOccurrenceToSession(object $occurrence, array $bookings): object {
        $session = new stdClass();
        $session->SessionID = (int) ($occurrence->OccurrenceID ?? 0);
        $session->Name = (string) ($occurrence->SessionName ?? 'Session');
        $session->Title = $session->Name;
        $session->SessionType = $this->formatCoachSlotType((string) ($occurrence->SlotType ?? 'program'));
        $session->SessionMode = in_array(($occurrence->SlotType ?? ''), ['private', 'facility_only'], true) ? 'Private' : 'Group';
        $session->Date = (string) ($occurrence->OccurrenceDate ?? '');
        $session->StartTime = (string) ($occurrence->StartTime ?? '00:00:00');
        $session->EndTime = (string) ($occurrence->EndTime ?? '00:00:00');
        $session->Location = (string) ($occurrence->FacilityName ?? $occurrence->SlotLabel ?? 'Academy');
        $session->Facility = $session->Location;
        $session->MaxParticipants = (int) ($occurrence->MaxSlots ?? 0);
        $session->ParticipantCount = count(array_filter($bookings, fn($booking) => strtolower((string) ($booking->Status ?? '')) !== 'cancelled'));
        $session->Status = $this->normalizeCoachOccurrenceStatus($occurrence, $bookings);
        $session->Description = (string) ($occurrence->Notes ?? '');
        $session->players = array_values(array_map(function($booking) {
            return (object) [
                'PlayerID' => (int) ($booking->PlayerID ?? 0),
                'Name' => (string) ($booking->PlayerName ?? ''),
                'AttendanceStatus' => (string) ($booking->Status ?? 'confirmed'),
            ];
        }, array_filter($bookings, fn($booking) => strtolower((string) ($booking->Status ?? '')) !== 'cancelled')));

        return $session;
    }

    private function normalizeCoachOccurrenceStatus(object $occurrence, array $bookings): string {
        $occurrenceStatus = strtolower((string) ($occurrence->Status ?? 'scheduled'));
        if ($occurrenceStatus === 'cancelled') {
            return 'cancelled';
        }
        if ($occurrenceStatus === 'completed') {
            return 'completed';
        }

        $today = date('Y-m-d');
        $nonCancelled = array_filter($bookings, fn($booking) => strtolower((string) ($booking->Status ?? '')) !== 'cancelled');
        $finalized = array_filter($nonCancelled, fn($booking) => in_array(strtolower((string) ($booking->Status ?? '')), ['attended', 'missed'], true));

        if (($occurrence->OccurrenceDate ?? '') < $today && !empty($nonCancelled) && count($finalized) === count($nonCancelled)) {
            return 'completed';
        }

        if (($occurrence->OccurrenceDate ?? '') > $today) {
            return 'scheduled';
        }

        return 'active';
    }

    private function formatCoachSlotType(string $slotType): string {
        return match (strtolower($slotType)) {
            'private' => 'Private Session',
            'facility_only' => 'Facility Booking',
            default => 'Program Session',
        };
    }

    private function buildCoachCalendarEvents(array $sessions): array {
        $events = [];
        foreach ($sessions as $session) {
            $color = $this->getSessionColor($session->SessionType ?? '');
            $events[] = [
                'id' => $session->SessionID,
                'title' => $session->Name,
                'start' => ($session->Date ?? '') . 'T' . ($session->StartTime ?? '00:00:00'),
                'end' => ($session->Date ?? '') . 'T' . ($session->EndTime ?? '00:00:00'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => $session->SessionType,
                    'location' => $session->Location ?? '',
                    'status' => $session->Status ?? 'scheduled',
                    'participants' => $session->ParticipantCount ?? 0,
                    'maxParticipants' => $session->MaxParticipants ?? 0,
                ],
            ];
        }

        return $events;
    }

    private function buildCoachSessionStats(array $sessions): array {
        $today = date('Y-m-d');
        $weekEnd = date('Y-m-d', strtotime('+6 days'));
        $attendanceStatuses = 0;
        $attendedStatuses = 0;

        foreach ($sessions as $session) {
            foreach ($session->players ?? [] as $player) {
                $attendanceStatus = strtolower((string) ($player->AttendanceStatus ?? ''));
                if (in_array($attendanceStatus, ['attended', 'missed'], true)) {
                    $attendanceStatuses++;
                    if ($attendanceStatus === 'attended') {
                        $attendedStatuses++;
                    }
                }
            }
        }

        return [
            'today' => count(array_filter($sessions, fn($session) => ($session->Date ?? '') === $today && ($session->Status ?? '') !== 'cancelled')),
            'thisWeek' => count(array_filter($sessions, fn($session) => ($session->Date ?? '') >= $today && ($session->Date ?? '') <= $weekEnd && ($session->Status ?? '') !== 'cancelled')),
            'total' => count(array_filter($sessions, fn($session) => ($session->Status ?? '') !== 'cancelled')),
            'attendance' => $attendanceStatuses > 0 ? (int) round(($attendedStatuses / $attendanceStatuses) * 100) : 0,
        ];
    }

    private function applyCoachSessionFilters(array $sessions, array $filters): array {
        return array_values(array_filter($sessions, function($session) use ($filters) {
            if (!empty($filters['type']) && stripos((string) ($session->SessionType ?? ''), (string) $filters['type']) === false) {
                return false;
            }
            if (!empty($filters['status']) && strtolower((string) ($session->Status ?? '')) !== strtolower((string) $filters['status'])) {
                return false;
            }
            if (!empty($filters['dateFrom']) && (string) ($session->Date ?? '') < (string) $filters['dateFrom']) {
                return false;
            }
            if (!empty($filters['dateTo']) && (string) ($session->Date ?? '') > (string) $filters['dateTo']) {
                return false;
            }
            if (!empty($filters['search'])) {
                $needle = strtolower((string) $filters['search']);
                $haystack = strtolower(
                    (string) ($session->Name ?? '') . ' ' .
                    (string) ($session->Location ?? '') . ' ' .
                    (string) ($session->SessionType ?? '')
                );
                if (strpos($haystack, $needle) === false) {
                    return false;
                }
            }
            return true;
        }));
    }

    private function respondLegacySessionRedirect(string $message, string $redirectPath): void {
        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
        $isJsonRequest = strpos($accept, 'application/json') !== false
            || strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

        if ($isJsonRequest || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $message,
                'redirect' => URLROOT . '/' . ltrim($redirectPath, '/'),
            ]);
            return;
        }

        flash('session_message', $message, 'alert alert-info');
        redirect($redirectPath);
    }

    // Helper: Get Session Color by Type
    private function getSessionColor($type) {
        $colors = [
            'Batting' => '#4A90E2',
            'Bowling' => '#50C878',
            'Strategy' => '#9B59B6',
            'Fielding' => '#F39C12',
            'Fitness' => '#E74C3C'
        ];
        
        return $colors[$type] ?? '#4A90E2';
    }
    
    // Helper: Calculate duration between two times
    private function calculateDuration($startTime, $endTime) {
        if (empty($startTime) || empty($endTime)) {
            return 'Time not set';
        }

        $start = strtotime($startTime);
        $end = strtotime($endTime);
        if ($start === false || $end === false || $end <= $start) {
            return 'Time not set';
        }
        $diff = $end - $start;
        
        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);
        
        if ($hours > 0 && $minutes > 0) {
            return "$hours hours $minutes mins";
        } elseif ($hours > 0) {
            return "$hours hours";
        } else {
            return "$minutes mins";
        }
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

    // ==================== TOURNAMENT RECOMMENDATIONS ====================

    /**
     * Display all tournament recommendations for current coach
     * GET /coach/tournament-recommendations
     */
    public function tournament_recommendations() {
        $coachId = $_SESSION['user_id'];
        $recommendationModel = $this->model('M_CoachTournamentRecommendation');
        
        try {
            // Get all recommendations for this coach
            $recommendations = $recommendationModel->getRecommendationsByCoach($coachId);
            
            // Get statistics
            $stats = $recommendationModel->getRecommendationStats($coachId);
            
            // Get pending count
            $pendingCount = $recommendationModel->getPendingCount($coachId);
            
            $data = [
                'title' => 'Tournament Recommendations - Coach Dashboard',
                'coachId' => $coachId,
                'recommendations' => $recommendations,
                'stats' => $stats,
                'pendingCount' => $pendingCount
            ];
            
            $this->view('coach/tournaments/tournament-recommendations', $data);
        } catch (Exception $e) {
            error_log('Error in tournament_recommendations: ' . $e->getMessage());
            redirect('coach/tournaments');
        }
    }

    private function getCoachAssignedRecommendationPlayers(int $coachId): array {
        $userModel = $this->model('M_Users');
        $players = $userModel->getCoachAssignedPlayers($coachId);

        $filteredPlayers = [];
        foreach ($players as $player) {
            if ((int)($player->UserID ?? 0) <= 0 || strtolower((string)($player->Status ?? '')) !== 'active') {
                continue;
            }

            $playerAgeGroup = $userModel->getAgeGroupForDateOfBirth((string)($player->DateOfBirth ?? ''));
            $player->PlayerAgeGroup = $playerAgeGroup;
            $filteredPlayers[] = $player;
        }

        return $filteredPlayers;
    }

    private function getCoachEligibleTournaments(int $coachId): array {
        $userModel = $this->model('M_Users');
        $tournamentModel = $this->model('M_Tournament');

        $coachAssignments = $userModel->getCoachSkillAgeGroupAssignments();
        $eligibleAgeGroups = [];
        foreach ($coachAssignments as $assignment) {
            if ((int)($assignment->CoachID ?? 0) !== $coachId) {
                continue;
            }

            $ageGroups = array_filter(array_map('trim', explode(',', (string)($assignment->AgeGroups ?? ''))));
            foreach ($ageGroups as $ageGroup) {
                $eligibleAgeGroups[strtolower($ageGroup)] = true;
            }
        }

        if (empty($eligibleAgeGroups)) {
            return [];
        }

        $tournaments = $tournamentModel->getPublicTournaments();
        return array_values(array_filter($tournaments, function($tournament) use ($eligibleAgeGroups) {
            $status = strtolower((string)($tournament->Status ?? ''));
            if (in_array($status, ['completed', 'cancelled'], true)) {
                return false;
            }

            $tournamentAgeGroup = strtolower(trim((string)($tournament->AgeGroup ?? '')));
            if ($tournamentAgeGroup === '') {
                return false;
            }

            return isset($eligibleAgeGroups[$tournamentAgeGroup]) || isset($eligibleAgeGroups['open']);
        }));
    }

    /**
     * Get form data for recommending players to a tournament
     * GET /coach/recommend-players/{tournamentId}
     */
    public function recommend_players($tournamentId = null) {
        if (!$tournamentId) { redirect('coach/tournaments'); return; }

        $tournamentModel  = $this->model('M_Tournament');
        $joinRequestModel = $this->model('M_TournamentJoinRequest');
        $coachRecModel    = $this->model('M_CoachTournamentRecommendation');
        $performanceModel = $this->model('M_Performance');

        $tournament = $tournamentModel->getTournamentById($tournamentId);
        if (!$tournament) { redirect('coach/tournaments'); return; }

        $data['tournament']   = $tournament;
        $data['selected_player_id'] = isset($_GET['playerId']) ? (int)$_GET['playerId'] : null;
        $data['players']      = $joinRequestModel->getRequestsByTournament($tournamentId);
        $data['my_recs']      = $coachRecModel->getRecommendationsByCoach($_SESSION['user_id'], ['tournamentId' => $tournamentId]);
        $data['is_head_coach'] = $this->_isHeadCoach();

        foreach ($data['players'] as $player) {
            $playerId = (int)($player->PlayerID ?? 0);
            $player->PerformanceSummary = $this->buildTournamentRecommendationPerformanceSummary($performanceModel, $playerId);
            $player->PerformanceMatches = $this->buildTournamentRecommendationPerformanceMatches($performanceModel, $playerId);
        }

        $this->view('coach/tournaments/recommend', $data);
    }

    private function buildTournamentRecommendationPerformanceSummary($performanceModel, int $playerId): array {
        if ($playerId <= 0) {
            return [
                'overall' => null,
                'latest_match' => null,
            ];
        }

        $overall = $performanceModel->getStoredOverallStats($playerId);
        $matchRecords = $performanceModel->getPerformanceStatistics($playerId, false);
        $latestMatch = !empty($matchRecords) ? $matchRecords[0] : null;

        return [
            'overall' => $overall,
            'latest_match' => $latestMatch,
        ];
    }

    private function buildTournamentRecommendationPerformanceMatches($performanceModel, int $playerId): array {
        if ($playerId <= 0) {
            return [];
        }

        $matches = $performanceModel->getPerformanceStatistics($playerId, true);
        if (empty($matches)) {
            return [];
        }

        return array_slice($matches, 0, 5);
    }

    /**
     * Save a new tournament recommendation
     * POST /coach/save_recommendation
     */
    public function save_recommendation() {
        $isJsonRequest = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isJsonRequest) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'POST method required']);
                return;
            }
            redirect('coach/tournaments');
            return;
        }

        $coachId = $_SESSION['user_id'];
        $input = $isJsonRequest ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;

        $tournamentId    = intval($input['tournamentId'] ?? 0);
        $playerId        = intval($input['playerId'] ?? 0);
        $recommendedRole = trim($input['recommendedRole'] ?? '');
        $reason          = trim($input['reason'] ?? '');
        $comments        = trim($input['comments'] ?? '');

        $formBack    = 'coach/recommend_players/' . $tournamentId;
        $detailPage  = 'coach/tournament_detail/' . $tournamentId;

        $eligibility = $this->validateCoachRecommendationEligibility($coachId, $tournamentId, $playerId);
        if (!$eligibility['success']) {
            if ($isJsonRequest) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $eligibility['message']]);
                return;
            }
            $_SESSION['error'] = $eligibility['message'];
            redirect('coach/tournament-recommendations');
            return;
        }

        if (!$tournamentId || !$playerId || empty($recommendedRole) || empty($reason)) {
            if ($isJsonRequest) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Player, Role, and Reason are all required.']);
                return;
            }
            $_SESSION['error'] = 'Player, Role, and Reason are all required.';
            redirect($formBack);
            return;
        }

        $recommendationModel = $this->model('M_CoachTournamentRecommendation');

        $result = $recommendationModel->addRecommendation($coachId, $tournamentId, $playerId, [
            'role'     => $recommendedRole,
            'reason'   => $reason,
            'comments' => $comments,
        ]);

        if ($result['success']) {
            if ($isJsonRequest) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Recommendation submitted successfully.']);
                return;
            }
            $_SESSION['success'] = 'Recommendation submitted successfully.';
            redirect($detailPage);
        } else {
            if ($isJsonRequest) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Failed to save recommendation.']);
                return;
            }
            $_SESSION['error'] = $result['message'] ?? 'Failed to save recommendation.';
            redirect($formBack);
        }
    }

    /**
     * Get a recommendation as JSON for edit forms.
     * GET /coach/recommendation-details/{recommendationId}
     */
    public function recommendation_details($recommendationId = null) {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'GET method required']);
            return;
        }

        if (!$recommendationId) {
            echo json_encode(['success' => false, 'message' => 'Recommendation ID is required']);
            return;
        }

        $coachId = $_SESSION['user_id'];
        $recommendationModel = $this->model('M_CoachTournamentRecommendation');
        $recommendation = $recommendationModel->getRecommendationDetails($recommendationId);

        if (!$recommendation) {
            echo json_encode(['success' => false, 'message' => 'Recommendation not found']);
            return;
        }

        if ((int)$recommendation->CoachID !== (int)$coachId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        echo json_encode(['success' => true, 'recommendation' => $recommendation]);
    }

    /**
     * Update an existing recommendation
     * PUT /coach/update-recommendation/{recommendationId}
     */
    public function update_recommendation($recommendationId = null) {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode(['success' => false, 'message' => 'POST or PUT method required']);
            return;
        }
        
        if (!$recommendationId) {
            echo json_encode(['success' => false, 'message' => 'Recommendation ID is required']);
            return;
        }
        
        $coachId = $_SESSION['user_id'];
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $recommendedRole = trim($input['recommendedRole'] ?? '');
        $reason = trim($input['reason'] ?? '');
        $comments = trim($input['comments'] ?? '');
        
        if (empty($recommendedRole)) {
            echo json_encode(['success' => false, 'message' => 'Role is required']);
            return;
        }

        $recommendationModel = $this->model('M_CoachTournamentRecommendation');
        
        try {
            // Get recommendation to verify ownership
            $recommendation = $recommendationModel->getRecommendationDetails($recommendationId);
            if (!$recommendation) {
                echo json_encode(['success' => false, 'message' => 'Recommendation not found']);
                return;
            }
            
            if ($recommendation->CoachID != $coachId) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You can only edit your own recommendations']);
                return;
            }
            
            if ($recommendation->Status !== 'pending') {
                echo json_encode(['success' => false, 'message' => 'Can only edit pending recommendations']);
                return;
            }

            $eligibility = $this->validateCoachRecommendationEligibility($coachId, (int)$recommendation->TournamentID, (int)$recommendation->PlayerID);
            if (!$eligibility['success']) {
                echo json_encode(['success' => false, 'message' => $eligibility['message']]);
                return;
            }
            
            // Update recommendation
            $updateData = [
                'role' => $recommendedRole,
                'reason' => $reason,
                'comments' => $comments
            ];

            $result = $recommendationModel->updateRecommendation($recommendationId, $coachId, $updateData);

            if (!empty($result['success'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recommendation updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to update recommendation'
                ]);
            }
        } catch (Exception $e) {
            error_log('Error in update_recommendation: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function validateCoachRecommendationEligibility(int $coachId, int $tournamentId, int $playerId, bool $allowRecommendationIdPlayerLookup = false): array {
        $userModel = $this->model('M_Users');
        $tournamentModel = $this->model('M_Tournament');

        $tournament = $tournamentModel->getTournamentById($tournamentId);
        if (!$tournament) {
            return ['success' => false, 'message' => 'Tournament not found'];
        }

        $playerAssignments = $userModel->getCoachAssignedPlayers($coachId);
        $playerAssigned = false;
        $playerAgeGroup = null;
        foreach ($playerAssignments as $assignment) {
            if ((int)($assignment->PlayerID ?? 0) === $playerId || (int)($assignment->UserID ?? 0) === $playerId) {
                $playerAssigned = true;
                $playerAgeGroup = (string)($assignment->PlayerAgeGroup ?? $userModel->getAgeGroupForDateOfBirth((string)($assignment->DateOfBirth ?? '')));
                break;
            }
        }

        if (!$playerAssigned) {
            return ['success' => false, 'message' => 'You can only recommend players currently assigned to you'];
        }

        $coachAssignments = $userModel->getCoachSkillAgeGroupAssignments();
        $eligibleAgeGroups = [];
        foreach ($coachAssignments as $assignment) {
            if ((int)($assignment->CoachID ?? 0) !== $coachId) {
                continue;
            }

            foreach (array_filter(array_map('trim', explode(',', (string)($assignment->AgeGroups ?? '')))) as $ageGroup) {
                $eligibleAgeGroups[strtolower($ageGroup)] = true;
            }
        }

        $tournamentAgeGroup = strtolower(trim((string)($tournament->AgeGroup ?? '')));
        if ($tournamentAgeGroup === '' || (!isset($eligibleAgeGroups[$tournamentAgeGroup]) && !isset($eligibleAgeGroups['open']))) {
            return ['success' => false, 'message' => 'You can only recommend for tournaments in your assigned age groups'];
        }

        $playerAgeGroup = strtolower(trim((string)$playerAgeGroup));
        if ($playerAgeGroup !== '' && $playerAgeGroup !== 'open' && $tournamentAgeGroup !== 'open' && $playerAgeGroup !== $tournamentAgeGroup) {
            return ['success' => false, 'message' => 'You can only recommend players who match the selected tournament age group'];
        }

        return ['success' => true];
    }

    /**
     * Delete a recommendation
     * DELETE /coach/delete-recommendation/{recommendationId}
     */
    public function delete_recommendation($recommendationId = null) {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode(['success' => false, 'message' => 'POST or DELETE method required']);
            return;
        }
        
        if (!$recommendationId) {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $recommendationId = $input['recommendationId'] ?? null;
        }
        
        if (!$recommendationId) {
            echo json_encode(['success' => false, 'message' => 'Recommendation ID is required']);
            return;
        }
        
        $coachId = $_SESSION['user_id'];
        $recommendationModel = $this->model('M_CoachTournamentRecommendation');
        
        try {
            // Get recommendation to verify ownership
            $recommendation = $recommendationModel->getRecommendationDetails($recommendationId);
            if (!$recommendation) {
                echo json_encode(['success' => false, 'message' => 'Recommendation not found']);
                return;
            }
            
            if ($recommendation->CoachID != $coachId) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You can only delete your own recommendations']);
                return;
            }
            
            if ($recommendation->Status !== 'pending') {
                echo json_encode(['success' => false, 'message' => 'Can only delete pending recommendations']);
                return;
            }
            
            // Delete recommendation
            $result = $recommendationModel->deleteRecommendation($recommendationId, $coachId);

            if (!empty($result['success'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recommendation deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to delete recommendation'
                ]);
            }
        } catch (Exception $e) {
            error_log('Error in delete_recommendation: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get tournament details with status
     * GET /coach/tournament/{tournamentId}/details
     */
    public function get_tournament_details($tournamentId = null) {
        header('Content-Type: application/json');
        
        if (!$tournamentId) {
            echo json_encode(['success' => false, 'message' => 'Tournament ID is required']);
            return;
        }
        
        $eventModel = $this->model('Event');
        
        try {
            $tournament = $eventModel->getEventById($tournamentId);
            
            if (!$tournament) {
                echo json_encode(['success' => false, 'message' => 'Tournament not found']);
                return;
            }
            
            // Check if tournament is still accepting recommendations
            $status = 'accepting';
            if (isset($tournament->Status)) {
                $tourStatus = strtolower($tournament->Status ?? '');
                if ($tourStatus === 'completed' || $tourStatus === 'cancelled') {
                    $status = 'closed';
                } elseif ($tourStatus === 'in_progress') {
                    $status = 'in-progress';
                }
            }
            
            echo json_encode([
                'success' => true,
                'tournament' => $tournament,
                'status' => $status
            ]);
        } catch (Exception $e) {
            error_log('Error in get_tournament_details: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all players assigned to current coach
     * GET /coach/assigned-players
     */
    public function get_assigned_players() {
        header('Content-Type: application/json');
        
        $coachId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        
        try {
            $players = $userModel->getCoachAssignedPlayers($coachId);
            
            if (empty($players)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'No players assigned',
                    'players' => [],
                    'count' => 0
                ]);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'players' => $players,
                'count' => count($players)
            ]);
        } catch (Exception $e) {
            error_log('Error in get_assigned_players: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ─── NEW TOURNAMENT MODULE METHODS ───────────────────────────────────────

    private function _isHeadCoach()
    {
        $coachId = $_SESSION['user_id'];
        $db = new Database();
        $db->query('SELECT IsHeadCoach FROM coachprofile WHERE CoachID = :id');
        $db->bind(':id', $coachId);
        $row = $db->single();
        return $row && $row->IsHeadCoach == 1;
    }

    private function getAvailableTournamentStatuses($tournament)
    {
        $transitions = [
            'created'             => ['registration_open'],
            'registration_open'   => ['registration_closed'],
            'registration_closed' => $tournament->IsTeamAnnounced ? ['ongoing'] : ['team_announced', 'ongoing'],
            'team_announced'      => ['ongoing'],
            'ongoing'             => ['completed'],
        ];

        $availableStatuses = [];
        $queue = $transitions[$tournament->Status] ?? [];

        while (!empty($queue)) {
            $status = array_shift($queue);
            if (in_array($status, $availableStatuses, true)) {
                continue;
            }

            $availableStatuses[] = $status;

            foreach ($transitions[$status] ?? [] as $nextStatus) {
                if (!in_array($nextStatus, $availableStatuses, true)) {
                    $queue[] = $nextStatus;
                }
            }
        }

        return $availableStatuses;
    }

    public function tournament_detail($id = null)
    {
        if (!$id) { redirect('coach/tournaments'); return; }

        $M_Tournament  = $this->model('M_Tournament');
        $M_JoinRequest = $this->model('M_TournamentJoinRequest');
        $M_CoachRec    = $this->model('M_CoachTournamentRecommendation');
        $M_Result      = $this->model('M_TournamentResult');

        $tournament = $M_Tournament->getTournamentById($id);
        if (!$tournament) { redirect('coach/tournaments'); return; }

        $data['tournament']    = $tournament;
        $data['team']          = $M_Tournament->getTeam($id);
        $data['join_requests'] = $M_JoinRequest->getRequestsByTournament($id);
        $data['my_recs']       = $M_CoachRec->getRecommendationsByTournament($id);
        $data['result']        = $M_Result->getResult($id);
        $data['is_head_coach'] = $this->_isHeadCoach();
        $data['status_options'] = $data['is_head_coach'] ? $this->getAvailableTournamentStatuses($tournament) : [];

        $this->view('coach/tournaments/detail', $data);
    }

    public function update_tournament_status($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('coach/tournaments'); return; }

        if (!$this->_isHeadCoach()) {
            $_SESSION['error'] = 'Only the head coach can update tournament status.';
            redirect('coach/tournament_detail/' . $id);
            return;
        }

        $M_Tournament = $this->model('M_Tournament');
        $tournament = $M_Tournament->getTournamentById($id);
        if (!$tournament) {
            $_SESSION['error'] = 'Tournament not found.';
            redirect('coach/tournaments');
            return;
        }

        $allowed = $this->getAvailableTournamentStatuses($tournament);
        $newStatus = trim($_POST['status'] ?? '');

        if ($newStatus === '' || !in_array($newStatus, $allowed, true)) {
            $_SESSION['error'] = 'Invalid status selection.';
            redirect('coach/tournament_detail/' . $id);
            return;
        }

        // Enforce lifecycle timing rules (relative to tournament date)
        try {
            $tournamentDate = !empty($tournament->tdate) ? new DateTime((string)$tournament->tdate) : null;
            $today = new DateTime('today');
            $daysUntil = $tournamentDate ? (int)$today->diff($tournamentDate)->format('%r%a') : null;

            if ($daysUntil !== null) {
                if ($newStatus === 'registration_closed' && $daysUntil < 30) {
                    $_SESSION['error'] = 'Registrations must be closed at least 1 month before the tournament date.';
                    redirect('coach/tournament_detail/' . $id);
                    return;
                }

                if ($newStatus === 'team_announced' && $daysUntil < 14) {
                    $_SESSION['error'] = 'Squad must be announced at least 2 weeks before the tournament date.';
                    redirect('coach/tournament_detail/' . $id);
                    return;
                }

                if ($newStatus === 'ongoing' && $daysUntil > 0) {
                    $_SESSION['error'] = 'Tournament can only be marked as ongoing on or after the tournament date.';
                    redirect('coach/tournament_detail/' . $id);
                    return;
                }

                if ($newStatus === 'completed' && $daysUntil > 0) {
                    $_SESSION['error'] = 'Tournament can only be marked as completed on or after the tournament date.';
                    redirect('coach/tournament_detail/' . $id);
                    return;
                }
            }
        } catch (Exception $e) {
            // If date parsing fails, do not block status updates.
        }

        if ($newStatus === 'team_announced') {
            $M_Tournament->announceTeam($id);
            $this->notifyTournamentTeamSelection((int)$id);
            $_SESSION['success'] = 'Team announced successfully.';
        } else {
            $M_Tournament->updateStatus($id, $newStatus);
            $_SESSION['success'] = 'Tournament status updated to ' . str_replace('_', ' ', $newStatus) . '.';
        }

        redirect('coach/tournament_detail/' . $id);
    }

    public function finalize_team($id = null)
    {
        if (!$id) { redirect('coach/tournaments'); return; }
        if (!$this->_isHeadCoach()) {
            $_SESSION['error'] = 'Only the head coach can finalize the squad.';
            redirect('coach/tournament_detail/' . $id);
            return;
        }

        $M_Tournament = $this->model('M_Tournament');
        $tournament   = $M_Tournament->getTournamentById($id);
        if (!$tournament) { redirect('coach/tournaments'); return; }

        if (!in_array($tournament->Status, ['registration_open', 'registration_closed', 'created'])) {
            $_SESSION['error'] = 'Squad can only be finalized while the tournament is not yet ongoing.';
            redirect('coach/tournament_detail/' . $id);
            return;
        }

        $M_JoinRequest = $this->model('M_TournamentJoinRequest');
        $M_CoachRec    = $this->model('M_CoachTournamentRecommendation');
        $M_TrainerRec  = $this->model('M_TrainerTournamentRecommendation');

        $data['tournament']    = $tournament;
        $data['join_requests'] = $M_JoinRequest->getRequestsByTournament($id);
        $data['coach_recs']    = $M_CoachRec->getRecommendationsByTournament($id);
        $data['trainer_recs']  = $M_TrainerRec->getRecommendationsByTournament($id);
        $data['team']          = $M_Tournament->getTeam($id);

        $this->view('coach/tournaments/finalize', $data);
    }

    public function save_team_selection($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('coach/tournaments'); return; }
        if (!$this->_isHeadCoach()) {
            $_SESSION['error'] = 'Only the head coach can select the squad.';
            redirect('coach/tournament_detail/' . $id);
            return;
        }

        $M_Tournament = $this->model('M_Tournament');
        $tournament   = $M_Tournament->getTournamentById($id);
        if (!$tournament) { redirect('coach/tournaments'); return; }

        // Clear existing draft and rebuild
        $M_Tournament->clearTeamDraft($id);

        $selected = $_POST['selected'] ?? [];   // array of player IDs
        $roles    = $_POST['roles'] ?? [];       // player_id => role string

        foreach ($selected as $playerId) {
            $playerId = (int)$playerId;
            if (!$playerId) continue;
            $M_Tournament->addPlayerToTeam([
                'tournament_id' => $id,
                'player_id'     => $playerId,
                'role'          => $roles[$playerId] ?? null,
                'selected_by'   => $_SESSION['user_id'],
            ]);
        }

        // If "confirm" button pressed, lock the squad
        if (!empty($_POST['confirm'])) {
            // Enforce: squad must be announced at least 2 weeks before tournament date
            try {
                if (!empty($tournament->tdate)) {
                    $tournamentDate = new DateTime((string)$tournament->tdate);
                    $today = new DateTime('today');
                    $daysUntil = (int)$today->diff($tournamentDate)->format('%r%a');
                    if ($daysUntil < 14) {
                        $_SESSION['error'] = 'Squad cannot be announced within 2 weeks of the tournament date.';
                        $_SESSION['success'] = 'Squad draft saved.';
                        redirect('coach/tournament_detail/' . $id);
                        return;
                    }
                }
            } catch (Exception $e) {
                // If date parsing fails, proceed.
            }

            $M_Tournament->confirmTeam($id, $_SESSION['user_id']);
            $M_Tournament->announceTeam($id);
            $this->notifyTournamentTeamSelection((int)$id);
            $_SESSION['success'] = 'Squad confirmed and announced successfully.';
        } else {
            $_SESSION['success'] = 'Squad draft saved.';
        }

        redirect('coach/tournament_detail/' . $id);
    }

    private function notifyTournamentTeamSelection(int $tournamentId): void
    {
        if ($tournamentId <= 0) {
            return;
        }

        try {
            require_once APPROOT . '/libraries/TournamentNotificationService.php';
            TournamentNotificationService::notifyTeamSelection($tournamentId);
        } catch (Throwable $e) {
            error_log('Coach tournament selection notification failed: ' . $e->getMessage());
        }
    }
}
?>
