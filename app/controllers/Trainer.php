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
        $data = [
            'title' => 'Trainer Dashboard',
            'trainer_name' => $_SESSION['user_name'] ?? 'Trainer',
            'upcoming_sessions' => $this->getUpcomingSessions(),
            'today_stats' => $this->getTodayStats()
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
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        $data = [
            'title' => 'Bookings Management',
            'bookings' => [] // $this->trainerModel->getBookings($_SESSION['user_id'])
        ];

        $this->view('trainer/bookings', $data);
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
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        $data = [
            'title' => 'Nutrition & Supplements',
            'nutrition_plans' => [], // $this->trainerModel->getNutritionPlans(),
            'supplements' => [] // $this->trainerModel->getSupplements()
        ];

        $this->view('trainer/nutrition', $data);
    }

    public function workout() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        $data = [
            'title' => 'Workout Recommendations',
            'workout_plans' => [], // $this->trainerModel->getWorkoutPlans(),
            'exercises' => [] // $this->trainerModel->getExercises()
        ];

        $this->view('trainer/workout', $data);
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
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Validate input
        if (!isset($_POST['record_id']) || !isset($_POST['verify_status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $recordId = intval($_POST['record_id']);
        $verifyStatus = trim($_POST['verify_status']);
        $verifyComments = trim($_POST['verify_comments'] ?? '');

        // Validate verify status
        $allowedStatuses = ['pending', 'verified', 'rejected'];
        if (!in_array($verifyStatus, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid verification status']);
            return;
        }

        try {
            // Initialize medical model
            $medicalModel = $this->model('M_Medical');
            
            // Update verify status
            $result = $medicalModel->updateVerifyStatus($recordId, $verifyStatus, $verifyComments);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Verification status updated successfully',
                    'record_id' => $recordId,
                    'new_status' => $verifyStatus
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update verification status']);
            }
        } catch (Exception $e) {
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

    public function addSession() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'trainer') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'trainer_id' => $_SESSION['user_id'],
                'session_type' => $_POST['session_type'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'duration' => $_POST['duration'],
                'player_id' => $_POST['player_id'] ?? null,
                'description' => $_POST['description'] ?? ''
            ];

            if ($this->trainerModel->addSession($data)) {
                echo json_encode(['success' => true, 'message' => 'Session added successfully']);
            } else {
                echo json_encode(['error' => 'Failed to add session']);
            }
        }
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
    private function getUpcomingSessions() {
        // Get upcoming sessions for today
        return [
            [
                'time' => '09:00 AM - 11:00 AM',
                'title' => 'Youth Cricket Program',
                'players' => '15 Players',
                'location' => 'Field A',
                'type' => 'group'
            ],
            [
                'time' => '11:30 AM - 12:30 PM',
                'title' => 'Kumara Silva',
                'description' => 'Fitness Assessment',
                'type' => 'private'
            ],
            [
                'time' => '02:00 PM - 04:00 PM',
                'title' => 'Advanced Training',
                'players' => '12 Players',
                'location' => 'Indoor Nets',
                'type' => 'group'
            ]
        ];
    }

    private function getTodayStats() {
        return [
            'total_sessions' => 8,
            'active_players' => 24,
            'private_sessions' => 3,
            'upcoming_tournaments' => 2
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
}
