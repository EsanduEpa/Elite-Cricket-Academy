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
        $sessionModel = $this->model('M_Session');
        
        // Get coach's sessions
        $allSessions = $sessionModel->getSessionsByCoach($coachId);
        
        // Filter today's sessions
        $today = date('Y-m-d');
        $todaySessions = array_filter($allSessions, function($session) use ($today) {
            return $session->Date == $today && $session->Status == 'active';
        });
        
        // Filter upcoming sessions (next 7 days)
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $upcomingSessions = array_filter($allSessions, function($session) use ($today, $nextWeek) {
            return $session->Date >= $today && $session->Date <= $nextWeek && $session->Status == 'active';
        });
        
        // Sort upcoming sessions by date and time
        usort($upcomingSessions, function($a, $b) {
            $dateCompare = strcmp($a->Date, $b->Date);
            if ($dateCompare !== 0) return $dateCompare;
            return strcmp($a->StartTime, $b->StartTime);
        });
        
        // Format upcoming bookings for the view
        $upcomingBookings = array_map(function($session) use ($sessionModel) {
            $participants = $sessionModel->getSessionParticipants($session->SessionID);
            $participantNames = array_map(function($p) { return $p->PlayerName; }, $participants);
            
            return [
                'id' => $session->SessionID,
                'player_name' => !empty($participantNames) ? implode(', ', $participantNames) : 'No participants yet',
                'session_name' => $session->Name,
                'session_type' => strtolower($session->SessionMode),
                'date' => $session->Date,
                'time' => date('H:i', strtotime($session->StartTime)),
                'start_time' => $session->StartTime,
                'end_time' => $session->EndTime,
                'duration' => $this->calculateDuration($session->StartTime, $session->EndTime),
                'facility' => $session->Location ?? 'TBA',
                'equipment' => '', // No equipment field in current schema
                'max_participants' => $session->MaxParticipants,
                'current_participants' => count($participants),
                'price' => $session->PricePerSession
            ];
        }, array_slice($upcomingSessions, 0, 5));
        
        // Calculate statistics
        $totalSessions = count($allSessions);
        $todaySessionsCount = count($todaySessions);
        $privateSessions = count(array_filter($allSessions, function($s) { return $s->SessionMode == 'Private'; }));
        $groupSessions = count(array_filter($allSessions, function($s) { return $s->SessionMode == 'Group'; }));
        
        $data = [
            'title' => 'Coach Dashboard - Elite Cricket Academy',
            'coachName' => $_SESSION['user_name'] ?? 'Coach',
            'coachType' => 'Coach',
            'coachId' => 'COACH_' . str_pad($coachId, 3, '0', STR_PAD_LEFT),
            'totalSessions' => $totalSessions,
            'todaySessions' => $todaySessionsCount,
            'privateSessions' => $privateSessions,
            'normalSessions' => $groupSessions,
            'upcomingBookings' => $upcomingBookings,
            'allSessions' => $allSessions,
            'weeklySchedule' => $this->generateWeeklySchedule($upcomingSessions),
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
        $players = $userModel->getPlayersAssignedToCoach($coachId);
        
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
            'totalPlayers' => $totalPlayers,
            'activePlayers' => $activePlayers,
            'inactivePlayers' => $inactivePlayers
        ];
        $this->view('coach/players', $data);
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
            'pendingCount' => $pendingCount
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
    
    public function notifications() {
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        
        // Get real notifications from database
        $notifications = $userModel->getNotificationsByUser($userId);
        $unreadCount = $userModel->getUnreadNotificationCount($userId);
        
        $data = [
            'title' => 'Notifications - Elite Cricket Academy',
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ];
        $this->view('coach/notifications', $data);
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
        $sessionModel = $this->model('M_Session');
        $medicalModel = $this->model('M_Medical');
        
        // Get real stats
        $players = $userModel->getPlayersAssignedToCoach($coachId);
        $allSessions = $sessionModel->getSessionsByCoach($coachId);
        $medicalRecords = $medicalModel->getAllMedicalRecordsWithPlayerInfo();
        
        $totalPlayers = count($players);
        $totalSessions = count($allSessions);
        $completedSessions = count(array_filter($allSessions, function($s) { return $s->Status == 'completed'; }));
        $activeSessions = count(array_filter($allSessions, function($s) { return $s->Status == 'active'; }));
        
        // Session type breakdown
        $privateSessions = count(array_filter($allSessions, function($s) { return $s->SessionMode == 'Private'; }));
        $groupSessions = count(array_filter($allSessions, function($s) { return $s->SessionMode == 'Group'; }));
        
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
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number']),
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
                if ($userModel->updateUser($userData) && $userModel->updateCoachProfile($coachData)) {
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
        $coachId      = $_SESSION['user_id'];
        $sessionModel = $this->model('M_Session');
        $sessions     = $sessionModel->getSessionsByCoach($coachId);

        foreach ($sessions as $session) {
            $session->players = $sessionModel->getSessionParticipants($session->SessionID);
        }

        $data = [
            'title'     => 'My Sessions',
            'coachName' => $_SESSION['user_name'] ?? 'Coach',
            'coachId'   => $coachId,
            'sessions'  => $sessions,
        ];
        $this->view('coach/sessions', $data);
    }

    // Create New Session (POST from wizard)
    public function create_session() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get JSON input from wizard
            $input = json_decode(file_get_contents('php://input'), true);
            
            // If JSON input exists (from wizard), use it; otherwise fallback to POST
            if ($input) {
                // Map wizard fields to model expected format
                $data = [
                    'coach_id' => $_SESSION['user_id'] ?? 1,
                    'session_type' => trim($input['SessionType'] ?? ''),
                    'session_mode' => trim($input['SessionMode'] ?? 'Group'),
                    'title' => trim($input['Name'] ?? ''),
                    'session_date' => trim($input['Date'] ?? ''),
                    'start_time' => trim($input['StartTime'] ?? ''),
                    'end_time' => trim($input['EndTime'] ?? ''),
                    'location' => trim($input['Location'] ?? ''),
                    'max_participants' => intval($input['MaxParticipants'] ?? 10),
                    'price' => floatval($input['PricePerSession'] ?? 0.00),
                    'is_recurring' => filter_var($input['IsRecurring'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'facility_type' => '',
                    'facility_number' => 0,
                    'recurrence_pattern' => 'None',
                    'recurrence_end' => null,
                    'selected_players' => []
                ];
            } else {
                // Legacy POST format
                $data = [
                    'coach_id' => $_SESSION['user_id'] ?? 1,
                    'session_type' => trim($_POST['session_type'] ?? ''),
                    'session_mode' => trim($_POST['session_mode'] ?? 'Group'),
                    'title' => trim($_POST['title'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'facility_type' => trim($_POST['facility_type'] ?? ''),
                    'facility_number' => intval($_POST['facility_number'] ?? 0),
                    'session_date' => trim($_POST['session_date'] ?? ''),
                    'start_time' => trim($_POST['start_time'] ?? ''),
                    'end_time' => trim($_POST['end_time'] ?? ''),
                    'max_participants' => intval($_POST['max_participants'] ?? 0),
                    'price' => floatval($_POST['price'] ?? 0.00),
                    'is_recurring' => filter_var($_POST['is_recurring'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'recurrence_pattern' => trim($_POST['recurrence_pattern'] ?? 'None'),
                    'recurrence_end' => trim($_POST['recurrence_end'] ?? ''),
                    'selected_players' => $_POST['selected_players'] ?? []
                ];
            }
            
            // Validate required fields
            if (empty($data['session_type']) || empty($data['title']) || 
                empty($data['session_date']) || empty($data['start_time']) || empty($data['end_time'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ]);
                return;
            }
            
            // Load model
            $sessionModel = $this->model('M_Session');
            
            // Create session
            try {
                $sessionId = $sessionModel->createSession($data);
                
                if ($sessionId) {
                    // Add players to session if any selected
                    if (!empty($data['selected_players'])) {
                        foreach ($data['selected_players'] as $playerId) {
                            $sessionModel->addPlayerToSession($sessionId, $playerId);
                        }
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Session created successfully',
                        'sessionId' => $sessionId
                    ]);
                } else {
                    // Get database error if available
                    error_log('Session creation failed. Data: ' . print_r($data, true));
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create session in database'
                    ]);
                }
            } catch (Exception $e) {
                error_log('Session creation exception: ' . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            redirect('coach/sessions');
        }
    }

    // Get Session by ID (JSON)
    public function get_session($id) {
        $sessionModel = $this->model('M_Session');
        $session = $sessionModel->getSessionById($id);
        
        if ($session) {
            // Get participants
            $participants = $sessionModel->getSessionParticipants($id);
            $session->participants = $participants;
            
            echo json_encode([
                'success' => true,
                'session' => $session
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Session not found'
            ]);
        }
    }

    // Get Calendar Sessions (JSON for FullCalendar)
    public function get_calendar_sessions() {
        $coachId = $_SESSION['user_id'] ?? 1;
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        
        $sessionModel = $this->model('M_Session');
        $sessions = $sessionModel->getCalendarSessions($coachId, $start, $end);
        
        // Format for FullCalendar
        $events = [];
        foreach ($sessions as $session) {
            $color = $this->getSessionColor($session->SessionType ?? '');
            
            $events[] = [
                'id' => $session->SessionID,
                'title' => $session->Name,
                'start' => $session->Date . 'T' . $session->StartTime,
                'end' => $session->Date . 'T' . $session->EndTime,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => $session->SessionType,
                    'location' => $session->Location ?? '',
                    'status' => $session->Status,
                    'participants' => $session->ParticipantCount ?? 0,
                    'maxParticipants' => $session->MaxParticipants
                ]
            ];
        }
        
        echo json_encode($events);
    }

    // Get Session Statistics (JSON)
    public function get_session_stats() {
        $coachId = $_SESSION['user_id'] ?? 1;
        $sessionModel = $this->model('M_Session');
        
        $stats = [
            'today' => $sessionModel->getTodaySessions($coachId),
            'thisWeek' => $sessionModel->getThisWeekSessions($coachId),
            'total' => $sessionModel->getTotalSessions($coachId),
            'attendance' => $sessionModel->getAverageAttendance($coachId)
        ];
        
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
        
        $sessionModel = $this->model('M_Session');

        // If scope=all is requested and user is authorized (coach), return all sessions
        $scope = $_GET['scope'] ?? '';
        if ($scope === 'all') {
            error_log('get_sessions_list: returning ALL sessions (scope=all)');
            $sessions = $sessionModel->getAllSessions($filters);
        } else {
            $sessions = $sessionModel->getSessionsByCoach($coachId, $filters);
        }
        
        error_log('Found ' . count($sessions) . ' session(s) for coach ID: ' . $coachId);
        error_log('=== GET SESSIONS LIST END ===');
        
        echo json_encode([
            'success' => true,
            'sessions' => $sessions,
            'coachId' => $coachId,
            'count' => count($sessions)
        ]);
    }

    // Update Session
    public function edit_session($id = null) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Session ID is required'
                ]);
                return;
            }
            
            error_log('=== EDIT SESSION DEBUG START ===');
            error_log('Session ID: ' . $id);
            error_log('Input data: ' . print_r($input, true));
            
            // Map input data to model format
            $data = [
                'session_type' => trim($input['SessionType'] ?? ''),
                'session_mode' => trim($input['SessionMode'] ?? 'Group'),
                'title' => trim($input['Name'] ?? ''),
                'session_date' => trim($input['Date'] ?? ''),
                'start_time' => trim($input['StartTime'] ?? ''),
                'end_time' => trim($input['EndTime'] ?? ''),
                'location' => trim($input['Location'] ?? ''),
                'max_participants' => intval($input['MaxParticipants'] ?? 10),
                'price' => floatval($input['PricePerSession'] ?? 0.00),
                'is_recurring' => filter_var($input['IsRecurring'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'status' => 'active'
            ];
            
            // Validate required fields
            if (empty($data['session_type']) || empty($data['title']) || 
                empty($data['session_date']) || empty($data['start_time']) || empty($data['end_time'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ]);
                error_log('=== EDIT SESSION DEBUG END (validation failed) ===');
                return;
            }
            
            $sessionModel = $this->model('M_Session');
            
            // Verify session exists and belongs to this coach
            $session = $sessionModel->getSessionById($id);
            if (!$session) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Session not found'
                ]);
                error_log('=== EDIT SESSION DEBUG END (not found) ===');
                return;
            }
            
            if ($session->CoachOrTrainerID != $_SESSION['user_id']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: You can only edit your own sessions'
                ]);
                error_log('=== EDIT SESSION DEBUG END (unauthorized) ===');
                return;
            }
            
            if ($sessionModel->updateSession($id, $data)) {
                error_log('✅ Session updated successfully');
                error_log('=== EDIT SESSION DEBUG END ===');
                echo json_encode([
                    'success' => true,
                    'message' => 'Session updated successfully',
                    'sessionId' => $id
                ]);
            } else {
                error_log('❌ Failed to update session');
                error_log('=== EDIT SESSION DEBUG END ===');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update session'
                ]);
            }
        } else {
            redirect('coach/sessions');
        }
    }

    // Cancel Session
    public function cancel_session($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $reason = trim($_POST['reason'] ?? 'No reason provided');
            
            $sessionModel = $this->model('M_Session');
            
            if ($sessionModel->cancelSession($id, $reason)) {
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Support both JSON and form POST
            $input = json_decode(file_get_contents('php://input'), true);
            $newDate = trim($input['new_date'] ?? $input['date'] ?? $_POST['new_date'] ?? '');
            $newStartTime = trim($input['new_start_time'] ?? $input['startTime'] ?? $_POST['new_start_time'] ?? '');
            $newEndTime = trim($input['new_end_time'] ?? $input['endTime'] ?? $_POST['new_end_time'] ?? '');
            
            if (empty($newDate) || empty($newStartTime) || empty($newEndTime)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please provide new date and time'
                ]);
                return;
            }
            
            $sessionModel = $this->model('M_Session');
            
            $data = [
                'session_date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime
            ];
            
            if ($sessionModel->rescheduleSession($id, $data)) {
                // Send reschedule notifications
                $this->sendSessionNotifications($id, 'rescheduled');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Session rescheduled successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to reschedule session'
                ]);
            }
        } else {
            redirect('coach/sessions');
        }
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
            
            $sessionModel = $this->model('M_Session');
            $success = true;
            
            foreach ($attendanceData as $playerId => $data) {
                $result = $sessionModel->markAttendance([
                    'session_id' => $sessionId,
                    'player_id' => $playerId,
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? '',
                    'marked_by' => $_SESSION['user_id'] ?? 1
                ]);
                
                if (!$result) {
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
        $sessionModel = $this->model('M_Session');
        $attendance = $sessionModel->getSessionAttendance($sessionId);
        
        echo json_encode([
            'success' => true,
            'attendance' => $attendance
        ]);
    }

    // Delete Session
    public function delete_session($id = null) {
        // Handle both POST with ID in URL and JSON with ID in body
        if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            header('Content-Type: application/json');
            
            error_log('=== DELETE SESSION DEBUG START ===');
            error_log('Session ID to delete: ' . $id);
            error_log('User ID from session: ' . ($_SESSION['user_id'] ?? 'NOT SET'));
            
            // Get ID from URL parameter or JSON body
            if (!$id) {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['sessionId'] ?? null;
                error_log('ID from JSON body: ' . $id);
            }
            
            if (!$id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Session ID is required'
                ]);
                return;
            }
            
            $sessionModel = $this->model('M_Session');
            
            // Verify session exists and belongs to this coach
            $session = $sessionModel->getSessionById($id);
            if (!$session) {
                error_log('Session not found: ' . $id);
                echo json_encode([
                    'success' => false,
                    'message' => 'Session not found'
                ]);
                return;
            }
            
            error_log('Session owner: ' . $session->CoachOrTrainerID);
            
            // Check if coach owns this session - use object notation
            if ($session->CoachOrTrainerID != $_SESSION['user_id']) {
                error_log('Unauthorized delete attempt');
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: You can only delete your own sessions'
                ]);
                return;
            }
            
            // Validate if session can be deleted (must be in the past)
            $sessionDateTime = new DateTime($session->Date . ' ' . $session->EndTime);
            $now = new DateTime();
            
            if ($sessionDateTime > $now) {
                error_log('Cannot delete future session');
                echo json_encode([
                    'success' => false,
                    'message' => 'Cannot delete upcoming sessions. Only past sessions can be deleted.'
                ]);
                return;
            }
            
            if ($sessionModel->deleteSession($id)) {
                error_log('✅ Session deleted successfully: ' . $id);
                error_log('=== DELETE SESSION DEBUG END ===');
                echo json_encode([
                    'success' => true,
                    'message' => 'Session deleted successfully'
                ]);
            } else {
                error_log('❌ Failed to delete session: ' . $id);
                error_log('=== DELETE SESSION DEBUG END ===');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete session'
                ]);
            }
        } else {
            redirect('coach/sessions');
        }
    }

    // Helper: Send Session Notifications
    private function sendSessionNotifications($sessionId, $action, $reason = '') {
        $sessionModel = $this->model('M_Session');
        $session = $sessionModel->getSessionById($sessionId);
        $participants = $sessionModel->getSessionParticipants($sessionId);
        
        if (!$session || empty($participants)) {
            return false;
        }
        
        $title = $session->Name ?? 'Session';
        $date = $session->Date ?? '';
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
        $start = strtotime($startTime);
        $end = strtotime($endTime);
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
    
    // Helper: Generate weekly schedule from sessions
    private function generateWeeklySchedule($sessions) {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $schedule = [];
        
        foreach ($days as $day) {
            $daySchedule = [
                'day' => $day,
                'sessions' => []
            ];
            
            foreach ($sessions as $session) {
                $sessionDayOfWeek = date('l', strtotime($session->Date));
                
                if ($sessionDayOfWeek === $day) {
                    $startTime = date('H:i', strtotime($session->StartTime));
                    $endTime = date('H:i', strtotime($session->EndTime));
                    
                    $daySchedule['sessions'][] = [
                        'time' => "$startTime-$endTime",
                        'type' => $session->SessionMode . ' - ' . $session->Name,
                        'facility' => $session->Location
                    ];
                }
            }
            
            if (!empty($daySchedule['sessions'])) {
                $schedule[] = $daySchedule;
            }
        }
        
        return $schedule;
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

    // Mark notification as read
    public function markNotificationRead($id = null) {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $id) {
            $userId = $_SESSION['user_id'] ?? 1;
            $userModel = $this->model('M_Users');
            if ($userModel->markNotificationRead($id, $userId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to mark as read']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }

    // Mark all notifications as read
    public function markAllNotificationsRead() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'] ?? 1;
            $userModel = $this->model('M_Users');
            if ($userModel->markAllNotificationsRead($userId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to mark all as read']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }

    // Delete notification
    public function deleteNotification($id = null) {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $id) {
            $userId = $_SESSION['user_id'] ?? 1;
            $userModel = $this->model('M_Users');
            if ($userModel->deleteNotification($id, $userId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
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
            
            $this->view('coach/tournament-recommendations', $data);
        } catch (Exception $e) {
            error_log('Error in tournament_recommendations: ' . $e->getMessage());
            redirect('coach/tournaments');
        }
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

        $tournament = $tournamentModel->getTournamentById($tournamentId);
        if (!$tournament) { redirect('coach/tournaments'); return; }

        $data['tournament']   = $tournament;
        $data['players']      = $joinRequestModel->getRequestsByTournament($tournamentId);
        $data['my_recs']      = $coachRecModel->getRecommendationsByCoach($_SESSION['user_id'], ['tournamentId' => $tournamentId]);
        $data['is_head_coach'] = $this->_isHeadCoach();

        $this->view('coach/tournaments/recommend', $data);
    }

    /**
     * Save a new tournament recommendation
     * POST /coach/save_recommendation
     */
    public function save_recommendation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('coach/tournaments');
            return;
        }

        $coachId = $_SESSION['user_id'];

        $tournamentId    = intval($_POST['tournamentId'] ?? 0);
        $playerId        = intval($_POST['playerId'] ?? 0);
        $recommendedRole = trim($_POST['recommendedRole'] ?? '');
        $reason          = trim($_POST['reason'] ?? '');
        $comments        = trim($_POST['comments'] ?? '');

        $formBack    = 'coach/recommend_players/' . $tournamentId;
        $detailPage  = 'coach/tournament_detail/' . $tournamentId;

        if (!$tournamentId || !$playerId || empty($recommendedRole) || empty($reason)) {
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
            $_SESSION['success'] = 'Recommendation submitted successfully.';
            redirect($detailPage);
        } else {
            $_SESSION['error'] = $result['message'] ?? 'Failed to save recommendation.';
            redirect($formBack);
        }
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
            
            // Update recommendation
            $updateData = [
                'recommended_role' => $recommendedRole,
                'reason' => $reason,
                'comments' => $comments
            ];
            
            if ($recommendationModel->updateRecommendation($recommendationId, $updateData)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recommendation updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update recommendation'
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
            if ($recommendationModel->deleteRecommendation($recommendationId)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recommendation deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete recommendation'
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
        $data['my_recs']       = $M_CoachRec->getRecommendationsByCoach($_SESSION['user_id'], ['tournamentId' => $id]);
        $data['result']        = $M_Result->getResult($id);
        $data['is_head_coach'] = $this->_isHeadCoach();

        $this->view('coach/tournaments/detail', $data);
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
            $M_Tournament->confirmTeam($id, $_SESSION['user_id']);
            $M_Tournament->announceTeam($id);
            $_SESSION['success'] = 'Squad confirmed and announced successfully.';
        } else {
            $_SESSION['success'] = 'Squad draft saved.';
        }

        redirect('coach/tournament_detail/' . $id);
    }
}
?>
