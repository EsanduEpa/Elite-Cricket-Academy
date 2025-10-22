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

        // Initialize nutrition plan model
        $nutritionModel = $this->model('M_NutritionPlan');
        
        // Get trainer's nutrition plans
        $nutritionPlans = $nutritionModel->getNutritionPlansByTrainer($_SESSION['user_id']);
        $players = $nutritionModel->getAllPlayers();

        $data = [
            'title' => 'Nutrition Plans',
            'nutrition_plans' => $nutritionPlans,
            'players' => $players
        ];

        $this->view('trainer/nutrition', $data);
    }

    public function workout() {
        // Temporary bypass for development
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 10; // Changed to match existing data
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }

        // Initialize trainer model
        $trainerModel = $this->model('M_Trainer');
        
        // Get trainer's workout plans
        $workoutPlans = $trainerModel->getWorkoutPlans();
        $players = $trainerModel->getAllPlayers();

        $data = [
            'title' => 'Workout Plans',
            'workout_plans' => $workoutPlans,
            'players' => $players
        ];

        $this->view('trainer/workout', $data);
    }

    // Add workout plan
    public function addWorkoutPlan() {
        // Ensure session has a valid trainer ID
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 10;
            $_SESSION['username'] = 'John Trainer';
            $_SESSION['user_type'] = 'trainer';
        }
        
        // Log the request
        error_log("=== ADD WORKOUT PLAN REQUEST ===");
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Log raw POST data
            error_log("Raw POST data: " . print_r($_POST, true));
            
            // Initialize trainer model
            $trainerModel = $this->model('M_Trainer');
            
            // Sanitize and prepare data (only the fields that exist in WorkoutPlan table)
            $data = [
                'trainer_id' => $_SESSION['user_id'] ?? 10,
                'workoutname' => isset($_POST['workoutname']) ? trim(htmlspecialchars($_POST['workoutname'], ENT_QUOTES, 'UTF-8')) : '',
                'frequency' => isset($_POST['frequency']) ? htmlspecialchars($_POST['frequency'], ENT_QUOTES, 'UTF-8') : '',
                'duration' => isset($_POST['duration']) ? (int)$_POST['duration'] : 0
            ];
            
            error_log("Processed data: " . print_r($data, true));
            
            // Validate data
            if (empty($data['workoutname']) || empty($data['frequency']) || empty($data['duration'])) {
                error_log("Validation failed - Missing required fields");
                flash('workout_message', 'Please fill in all required fields (Workout Name, Frequency, Duration)', 'alert alert-danger');
            } else if ($data['duration'] < 15 || $data['duration'] > 180) {
                error_log("Validation failed - Duration out of range: " . $data['duration']);
                flash('workout_message', 'Duration must be between 15 and 180 minutes', 'alert alert-danger');
            } else if (!in_array($data['frequency'], ['Daily', 'Weekly', 'Bi-weekly'])) {
                error_log("Validation failed - Invalid frequency: " . $data['frequency']);
                flash('workout_message', 'Invalid frequency selected', 'alert alert-danger');
            } else {
                error_log("Validation passed - Attempting to add workout plan");
                
                // Add workout plan
                try {
                    $result = $trainerModel->addWorkoutPlan($data);
                    error_log("Model result: " . ($result ? 'TRUE' : 'FALSE'));
                    
                    if ($result) {
                        error_log("SUCCESS - Workout plan added");
                        flash('workout_message', 'Workout plan added successfully!', 'alert alert-success');
                    } else {
                        error_log("FAILED - Model returned false");
                        flash('workout_message', 'Failed to add workout plan. Database error occurred.', 'alert alert-danger');
                    }
                } catch (PDOException $e) {
                    error_log("PDO Exception: " . $e->getMessage());
                    flash('workout_message', 'Database error: ' . $e->getMessage(), 'alert alert-danger');
                } catch (Exception $e) {
                    error_log("General Exception: " . $e->getMessage());
                    flash('workout_message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
                }
            }
        } else {
            error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            flash('workout_message', 'Invalid request method', 'alert alert-danger');
        }
        
        error_log("Redirecting to trainer/workout");
        redirect('trainer/workout');
    }

    // Update workout plan
    public function updateWorkoutPlan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize trainer model
            $trainerModel = $this->model('M_Trainer');
            
            $data = [
                'plan_id' => isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0,
                'trainer_id' => $_SESSION['user_id'] ?? 10,
                'workoutname' => isset($_POST['workoutname']) ? trim(htmlspecialchars($_POST['workoutname'], ENT_QUOTES, 'UTF-8')) : '',
                'frequency' => isset($_POST['frequency']) ? htmlspecialchars($_POST['frequency'], ENT_QUOTES, 'UTF-8') : '',
                'duration' => isset($_POST['duration']) ? (int)$_POST['duration'] : 0
            ];
            
            // Validate data
            if (empty($data['workoutname']) || empty($data['frequency']) || empty($data['duration'])) {
                flash('workout_message', 'Please fill in all required fields', 'alert alert-danger');
            } else if ($data['duration'] < 15 || $data['duration'] > 180) {
                flash('workout_message', 'Duration must be between 15 and 180 minutes', 'alert alert-danger');
            } else if (!in_array($data['frequency'], ['Daily', 'Weekly', 'Bi-weekly'])) {
                flash('workout_message', 'Invalid frequency selected', 'alert alert-danger');
            } else {
                // Update workout plan
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
            $plan_id = (int)$_POST['plan_id'];
            $trainer_id = $_SESSION['user_id'] ?? 10; // Use 10 as default for testing
            
            if ($trainerModel->deleteWorkoutPlan($plan_id, $trainer_id)) {
                flash('workout_message', 'Workout plan deleted successfully', 'alert alert-success');
            } else {
                flash('workout_message', 'Failed to delete workout plan', 'alert alert-danger');
            }
        }
        
        redirect('trainer/workout');
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
}
