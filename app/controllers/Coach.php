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
        // Hardcoded sample data for coach dashboard demonstration
        $data = [
            'title' => 'Coach Dashboard - Elite Cricket Academy',
            'coachName' => 'Michael Johnson',
            'coachType' => 'Batting Coach', // or 'Bowling Coach'
            'coachId' => 'COACH_001',
            'totalSessions' => 15,
            'todaySessions' => 3,
            'privateSessions' => 2,
            'normalSessions' => 1,
            'upcomingBookings' => [
                [
                    'id' => 1,
                    'player_name' => 'Alex Smith',
                    'session_type' => 'private',
                    'date' => '2025-09-06',
                    'time' => '09:00',
                    'duration' => '2 hours',
                    'facility' => 'Practice Net 1',
                    'equipment' => 'Bowling Machine, Side Wickets'
                ],
                [
                    'id' => 2,
                    'player_name' => 'Sarah Wilson',
                    'session_type' => 'private',
                    'date' => '2025-09-06',
                    'time' => '11:30',
                    'duration' => '2 hours',
                    'facility' => 'Practice Net 2',
                    'equipment' => 'Batting Tee, Cones'
                ],
                [
                    'id' => 3,
                    'player_name' => 'Group Training (U-16)',
                    'session_type' => 'normal',
                    'date' => '2025-09-06',
                    'time' => '15:00',
                    'duration' => '2 hours',
                    'facility' => 'Main Ground',
                    'equipment' => 'Bowling Machine, Wickets'
                ],
                [
                    'id' => 4,
                    'player_name' => 'Emma Davis',
                    'session_type' => 'private',
                    'date' => '2025-09-07',
                    'time' => '08:00',
                    'duration' => '2 hours',
                    'facility' => 'Practice Net 3',
                    'equipment' => 'Side Wickets, Bowling Machine'
                ],
                [
                    'id' => 5,
                    'player_name' => 'Junior Squad Training',
                    'session_type' => 'normal',
                    'date' => '2025-09-07',
                    'time' => '16:00',
                    'duration' => '2 hours',
                    'facility' => 'Main Ground',
                    'equipment' => 'Full Training Setup'
                ]
            ],
            'weeklySchedule' => [
                [
                    'day' => 'Monday',
                    'sessions' => [
                        ['time' => '09:00-11:00', 'type' => 'Private - John Doe', 'facility' => 'Net 1'],
                        ['time' => '15:00-17:00', 'type' => 'Group Training', 'facility' => 'Main Ground']
                    ]
                ],
                [
                    'day' => 'Tuesday',
                    'sessions' => [
                        ['time' => '08:00-10:00', 'type' => 'Private - Lisa Chen', 'facility' => 'Net 2'],
                        ['time' => '11:00-13:00', 'type' => 'Private - Mark Wilson', 'facility' => 'Net 1'],
                        ['time' => '16:00-18:00', 'type' => 'Squad Training', 'facility' => 'Main Ground']
                    ]
                ],
                [
                    'day' => 'Wednesday',
                    'sessions' => [
                        ['time' => '09:30-11:30', 'type' => 'Private - Emma Taylor', 'facility' => 'Net 3'],
                        ['time' => '14:00-16:00', 'type' => 'Technique Session', 'facility' => 'Indoor Nets']
                    ]
                ]
            ],
            'playerProfiles' => [
                [
                    'id' => 1,
                    'name' => 'Alex Smith',
                    'age' => 16,
                    'position' => 'Batsman',
                    'sessions_completed' => 24,
                    'performance_rating' => 8.5,
                    'last_session' => '2025-09-04'
                ],
                [
                    'id' => 2,
                    'name' => 'Sarah Wilson',
                    'age' => 14,
                    'position' => 'All-rounder',
                    'sessions_completed' => 18,
                    'performance_rating' => 7.8,
                    'last_session' => '2025-09-03'
                ],
                [
                    'id' => 3,
                    'name' => 'Emma Davis',
                    'age' => 15,
                    'position' => 'Bowler',
                    'sessions_completed' => 31,
                    'performance_rating' => 9.1,
                    'last_session' => '2025-09-05'
                ]
            ],
            'tournaments' => [
                [
                    'id' => 1,
                    'name' => 'Junior Championship 2025',
                    'date' => '2025-09-15',
                    'players_selected' => ['Alex Smith', 'Emma Davis'],
                    'status' => 'upcoming'
                ],
                [
                    'id' => 2,
                    'name' => 'Regional Tournament',
                    'date' => '2025-09-25',
                    'players_selected' => ['Sarah Wilson'],
                    'status' => 'upcoming'
                ]
            ],
            'recommendations' => [
                [
                    'player_name' => 'Alex Smith',
                    'recommendation' => 'Focus on improving footwork for off-side shots',
                    'priority' => 'high',
                    'date' => '2025-09-05'
                ],
                [
                    'player_name' => 'Sarah Wilson',
                    'recommendation' => 'Continue working on bowling accuracy',
                    'priority' => 'medium',
                    'date' => '2025-09-04'
                ]
            ],
            'medicalRecords' => [
                [
                    'player_name' => 'Alex Smith',
                    'condition' => 'Minor knee strain',
                    'status' => 'Under observation',
                    'restrictions' => 'No heavy running for 1 week',
                    'date' => '2025-09-02'
                ],
                [
                    'player_name' => 'Emma Davis',
                    'condition' => 'Fitness evaluation',
                    'status' => 'Cleared for all activities',
                    'restrictions' => 'None',
                    'date' => '2025-08-30'
                ]
            ]
        ];
        
        $this->view('coach/dashboard', $data);
    }
    
    public function schedules() {
        $data = [
            'title' => 'Training Schedules - Coach Dashboard',
            'schedules' => []
        ];
        $this->view('coach/schedules', $data);
    }
    
    public function bookings() {
        $data = [
            'title' => 'Session Bookings - Coach Dashboard',
            'bookings' => []
        ];
        $this->view('coach/bookings', $data);
    }
    
    public function tournaments() {
        $data = [
            'title' => 'Tournaments - Coach Dashboard',
            'tournaments' => []
        ];
        $this->view('coach/tournaments', $data);
    }
    
    public function players() {
        $data = [
            'title' => 'Player Management - Coach Dashboard',
            'players' => []
        ];
        $this->view('coach/players', $data);
    }
    
    public function recommendations() {
        $data = [
            'title' => 'Player Recommendations - Coach Dashboard',
            'recommendations' => []
        ];
        $this->view('coach/recommendations', $data);
    }
    
    public function medical() {
        $data = [
            'title' => 'Medical Records - Coach Dashboard',
            'records' => []
        ];
        $this->view('coach/medical', $data);
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
        $data = [
            'title' => 'Session & Schedule Management - Elite Cricket Academy',
            'coachName' => $_SESSION['user_name'] ?? 'Coach',
            'coachId' => $_SESSION['user_id'] ?? 1
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
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                
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
                    'recurrence_end' => trim($_POST['recurrence_end'] ?? null),
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
            $session['participants'] = $participants;
            
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
            $color = $this->getSessionColor($session['SessionType']);
            
            $events[] = [
                'id' => $session['SessionID'],
                'title' => $session['Title'],
                'start' => $session['SessionDate'] . 'T' . $session['StartTime'],
                'end' => $session['SessionDate'] . 'T' . $session['EndTime'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => $session['SessionType'],
                    'facility' => $session['FacilityType'] . ' ' . $session['FacilityNumber'],
                    'status' => $session['Status'],
                    'participants' => $session['ParticipantCount'] ?? 0,
                    'maxParticipants' => $session['MaxParticipants']
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
        $coachId = $_SESSION['user_id'] ?? 1;
        $filters = [
            'type' => $_GET['type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'dateFrom' => $_GET['dateFrom'] ?? '',
            'dateTo' => $_GET['dateTo'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        $sessionModel = $this->model('M_Session');
        $sessions = $sessionModel->getSessionsByCoach($coachId, $filters);
        
        echo json_encode([
            'success' => true,
            'sessions' => $sessions
        ]);
    }

    // Update Session
    public function edit_session($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'session_id' => $id,
                'session_type' => trim($_POST['session_type'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'facility_type' => trim($_POST['facility_type'] ?? ''),
                'facility_number' => intval($_POST['facility_number'] ?? 0),
                'session_date' => trim($_POST['session_date'] ?? ''),
                'start_time' => trim($_POST['start_time'] ?? ''),
                'end_time' => trim($_POST['end_time'] ?? ''),
                'max_participants' => intval($_POST['max_participants'] ?? 0),
                'status' => trim($_POST['status'] ?? 'scheduled')
            ];
            
            $sessionModel = $this->model('M_Session');
            
            if ($sessionModel->updateSession($id, $data)) {
                // Update participants if changed
                if (isset($_POST['selected_players'])) {
                    $sessionModel->updateSessionParticipants($id, $_POST['selected_players']);
                }
                
                // Send update notifications
                $this->sendSessionNotifications($id, 'updated');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Session updated successfully'
                ]);
            } else {
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
            $newDate = trim($_POST['new_date'] ?? '');
            $newStartTime = trim($_POST['new_start_time'] ?? '');
            $newEndTime = trim($_POST['new_end_time'] ?? '');
            
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
                    'notes' => $data['notes'] ?? ''
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
    public function delete_session($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionModel = $this->model('M_Session');
            
            if ($sessionModel->deleteSession($id)) {
                // Send deletion notifications
                $this->sendSessionNotifications($id, 'deleted');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Session deleted successfully'
                ]);
            } else {
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
        
        // Prepare notification message
        $messages = [
            'created' => "New session '{$session['Title']}' has been scheduled for {$session['SessionDate']} at {$session['StartTime']}",
            'updated' => "Session '{$session['Title']}' has been updated. Please check the details.",
            'cancelled' => "Session '{$session['Title']}' scheduled for {$session['SessionDate']} has been cancelled." . ($reason ? " Reason: $reason" : ""),
            'rescheduled' => "Session '{$session['Title']}' has been rescheduled to {$session['SessionDate']} at {$session['StartTime']}",
            'deleted' => "Session '{$session['Title']}' has been removed from the schedule."
        ];
        
        $message = $messages[$action] ?? 'Session notification';
        
        // Send notifications to each participant
        // TODO: Implement actual notification system (email, SMS, in-app)
        // For now, just log the notification
        foreach ($participants as $participant) {
            error_log("Notification to Player {$participant['PlayerID']}: $message");
            // Future: Send email, SMS, or create in-app notification
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
}
?>
