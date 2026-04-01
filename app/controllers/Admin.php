<?php
class Admin extends Controller {
    public function __construct() {
        // Debug using error_log (doesn't contaminate output)
        error_log("Admin controller constructor called - Method will be: " . ($_GET['url'] ?? 'none'));
        
        // Check authentication for all admin pages
        requireAuth(['Admin']);
    }
    
    public function index() {
        // Redirect to dashboard by default
        redirect('admin/dashboard');
    }
    
    public function dashboard() {
        // === ADMIN DASHBOARD - REAL DATABASE DATA ===
        
        // STEP 1: AUTO-UPDATE EVENT STATUSES
        // Updates events to "completed" if end date has passed
        // This keeps event data accurate without manual intervention
        $eventModel = $this->model('Event');
        $eventModel->updateEventStatuses();
        
        // STEP 2: LOAD DATA MODELS
        // Each model handles specific database queries for its domain
        $userModel = $this->model('M_Users');      // User-related queries
        $feedbackModel = $this->model('Feedback'); // Feedback queries
        $financeModel = $this->model('Finance');   // Finance/revenue queries
        
        // STEP 3: FETCH USER STATISTICS FROM DATABASE
        // Query the User table to get real counts for each role
        // Role values: 'Admin', 'Coach', 'Player', 'Trainer', 'ShopEmployee'
        $totalUsers = $userModel->getTotalUsers();                    // COUNT all users
        $totalCoaches = $userModel->getTotalUsersByType('Coach');     // COUNT WHERE Role = 'Coach'
        $totalPlayers = $userModel->getTotalUsersByType('Player');    // COUNT WHERE Role = 'Player'
        $totalTrainers = $userModel->getTotalUsersByType('Trainer');  // COUNT WHERE Role = 'Trainer'
        $totalAdmins = $userModel->getTotalUsersByType('Admin');      // COUNT WHERE Role = 'Admin'
        $totalShopEmployees = $userModel->getTotalUsersByType('ShopEmployee'); // COUNT WHERE Role = 'ShopEmployee'
        
        // CALCULATE TOTAL STAFF: All users except Players
        // Staff = Admin + Coach + Trainer + ShopEmployee
        $totalStaff = $totalAdmins + $totalCoaches + $totalTrainers + $totalShopEmployees;
        
        // STEP 4: FETCH UPCOMING EVENTS FROM DATABASE
        // Get next 4 upcoming events ordered by start date
        // This replaces hardcoded event data with real database records
        $upcomingEvents = $eventModel->getUpcomingEvents(4);
        
        // STEP 5: FETCH RECENT ACTIVITIES FROM DATABASE
        // Get last 20 activities from ActivityLog table
        // Includes user actions like registrations, logins, profile updates
        $recentActivities = $userModel->getRecentActivities(20);
        
        // STEP 6: FETCH PENDING FEEDBACK FROM DATABASE
        // Get feedback items that need admin attention
        // Status = 'pending' means not yet reviewed by admin
        $pendingFeedback = $feedbackModel->getPendingFeedbacks(5);
        
        // STEP 7: PREPARE DATA ARRAY FOR VIEW
        // All data now comes from database queries above
        $data = [
            'title' => 'Academy Management Dashboard - Elite Cricket Academy',
            
            // USER STATISTICS (from database)
            'totalUsers' => $totalUsers,       // Total registered users
            'totalCoaches' => $totalCoaches,   // Users with Coach role
            'totalPlayers' => $totalPlayers,   // Users with Player role
            'totalTrainers' => $totalTrainers, // Users with Trainer role
            'totalStaff' => $totalStaff,       // Users with ShopEmployee role
            
            // UPCOMING EVENTS (from Event table)
            // Returns array of event objects with: id, title, event_date, Type, Description, Location
            'upcomingEvents' => $upcomingEvents,
            
            // RECENT ACTIVITIES (from ActivityLog table)
            // Returns array of activity objects with: ActivityID, Action, Description, Timestamp, user_name
            'recentActivities' => $recentActivities,
            
            // PENDING FEEDBACK (from Feedback table)
            // Returns array of feedback objects with: FeedbackID, Content, Rating, Status, user_name
            'pendingFeedback' => $pendingFeedback,
            
            // FEEDBACK STATISTICS
            // Count of total pending feedback items needing review
            'totalPendingFeedback' => count($pendingFeedback),
            
            // TODAY'S STATISTICS (from database)
            // Real-time data for current day activity monitoring
            'todayStats' => [
                // New user registrations today (WHERE DATE(DateJoined) = CURDATE())
                'newRegistrations' => $userModel->getTodayRegistrations(),
                
                // Events happening today (WHERE DATE(StartDate) = CURDATE())
                'activeEvents' => count($eventModel->getTodayActiveEvents()),
                
                // Feedback received today
                'feedbackReceived' => $feedbackModel->getTodayFeedback()
            ],
            
            // MONTHLY REVENUE from Finance model
            'monthlyRevenue' => $financeModel->getMonthlyRevenue()
        ];
        
        // STEP 8: LOAD DASHBOARD VIEW
        // Pass all database data to the view template
        // View file: app/views/admin/dashboard.php
        $this->view('admin/dashboard', $data);
    }
    
    // AJAX endpoint for refreshing dashboard data
    public function refresh() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $userModel = $this->model('M_Users');
            $feedbackModel = $this->model('Feedback');
            $financeModel = $this->model('Finance');
            
            $refreshData = [
                'totalUsers' => $userModel->getTotalUsers(),
                'totalPendingFeedback' => $feedbackModel->getTotalPendingFeedback(),
                'todayStats' => [
                    'newRegistrations' => $userModel->getTodayRegistrations(),
                    'activeEvents' => count($this->model('Event')->getTodayActiveEvents()),
                    'feedbackReceived' => $feedbackModel->getTodayFeedback()
                ],
                'monthlyRevenue' => $financeModel->getMonthlyRevenue()
            ];
            
            echo json_encode(['success' => true, 'data' => $refreshData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Staff Management
    public function staff() {
        // Get user model
        $userModel = $this->model('M_Users');
        
        // Get staff members from database
        $staffMembers = $userModel->getStaffMembers();
        $staffStats = $userModel->getStaffStats();
        
        $data = [
            'title' => 'Staff Management - Elite Cricket Academy',
            'staff_members' => $staffMembers,
            'staff_stats' => $staffStats
        ];
        
        $this->view('admin/staff', $data);
    }

    // Events and Tournaments Management
    public function events() {
        try {
            $eventModel = $this->model('Event');
            
            // Auto-update event statuses before displaying
            $eventModel->updateEventStatuses();
            
            // Get events from database
            $upcomingEvents = $eventModel->getUpcomingEvents(10);
            $pastEvents = $eventModel->getPastEvents(10);
            
            // Add delete permission info to each event
            foreach ($upcomingEvents as &$event) {
                $event['can_delete'] = false; // Upcoming events cannot be deleted
            }
            
            foreach ($pastEvents as &$event) {
                $event['can_delete'] = $eventModel->canDeleteEvent($event['id']);
            }
            
            // Calculate stats from database
            $totalEvents = $eventModel->getTotalEvents();
            $upcomingCount = count($eventModel->getUpcomingEvents(1000)); // Get all upcoming
            
            // Get event type counts
            $db = new Database();
            $db->query('SELECT Type, COUNT(*) as count FROM Event GROUP BY Type');
            $typeCounts = $db->resultSet();
            
            $tournaments = 0;
            $trainingSessions = 0;
            $matches = 0;
            
            foreach ($typeCounts as $typeCount) {
                $type = $typeCount->Type;
                $count = (int)$typeCount->count;
                
                if ($type == 'Tournament') {
                    $tournaments = $count;
                } elseif ($type == 'Training Camp') {
                    $trainingSessions = $count;
                } elseif ($type == 'Match') {
                    $matches = $count;
                }
            }
            
            $data = [
                'title' => 'Events & Tournaments - Elite Cricket Academy',
                'upcomingEvents' => $upcomingEvents,
                'pastEvents' => $pastEvents,
                'recentEvents' => $eventModel->getRecentEvents(5),
                'eventStats' => [
                    'totalEvents' => $totalEvents,
                    'upcomingCount' => $upcomingCount,
                    'pastCount' => $totalEvents - $upcomingCount,
                    'tournaments' => $tournaments,
                    'trainingSessions' => $trainingSessions,
                    'matches' => $matches
                ]
            ];
        } catch (Exception $e) {
            error_log("Error loading events: " . $e->getMessage());
            // Fallback to empty data if there's an error
            $data = [
                'title' => 'Events & Tournaments - Elite Cricket Academy',
                'upcomingEvents' => [],
                'pastEvents' => [],
                'recentEvents' => [],
                'eventStats' => [
                    'totalEvents' => 0,
                    'upcomingCount' => 0,
                    'pastCount' => 0,
                    'tournaments' => 0,
                    'trainingSessions' => 0,
                    'matches' => 0
                ]
            ];
        }
        
        $this->view('admin/events', $data);
    }

    public function create_event() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Debug: Log that we received POST data
            error_log("======================================");
            error_log("Event creation POST received at: " . date('Y-m-d H:i:s'));
            error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'));
            error_log("POST data count: " . count($_POST));
            error_log("POST data: " . print_r($_POST, true));
            
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                error_log("ERROR: User not logged in!");
                flash('event_message', '⚠️ You must be logged in to create events', 'alert alert-danger');
                redirect('login');
                return;
            }
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Validate required POST fields exist
            $requiredFields = ['event_name', 'event_type', 'event_category', 'event_venue', 
                              'start_date', 'start_time', 'end_date', 'end_time',
                              'primary_contact', 'contact_email', 'contact_phone'];
            
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                error_log("ERROR: Missing required fields: " . implode(', ', $missingFields));
                flash('event_message', '⚠️ Missing required fields: ' . implode(', ', $missingFields), 'alert alert-danger');
                redirect('admin/create_event');
                return;
            }
            
            // Merge date and time fields into DATETIME format
            $startDateTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
            $endDateTime = $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00';
            
            error_log("Start DateTime: " . $startDateTime);
            error_log("End DateTime: " . $endDateTime);
            
            // Handle optional datetime fields
            $registrationStart = !empty($_POST['registration_start']) ? 
                str_replace('T', ' ', $_POST['registration_start']) . ':00' : null;
            $registrationEnd = !empty($_POST['registration_end']) ? 
                str_replace('T', ' ', $_POST['registration_end']) . ':00' : null;
            
            // Prepare data array for the model
            $eventData = [
                'name' => trim($_POST['event_name']),
                'type' => $_POST['event_type'],
                'category' => $_POST['event_category'],
                'description' => !empty($_POST['event_description']) ? trim($_POST['event_description']) : null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'location' => trim($_POST['event_venue']),
                'status' => isset($_POST['event_status']) ? $_POST['event_status'] : 'upcoming',
                'max_participants' => !empty($_POST['max_participants']) ? intval($_POST['max_participants']) : null,
                'registration_fee' => !empty($_POST['registration_fee']) ? floatval($_POST['registration_fee']) : null,
                'registration_start' => $registrationStart,
                'registration_end' => $registrationEnd,
                'primary_contact' => !empty($_POST['primary_contact']) ? trim($_POST['primary_contact']) : null,
                'contact_email' => !empty($_POST['contact_email']) ? trim($_POST['contact_email']) : null,
                'contact_phone' => !empty($_POST['contact_phone']) ? trim($_POST['contact_phone']) : null
            ];
            
            // Validate required fields
            if (empty($eventData['name']) || empty($eventData['type']) || empty($eventData['category']) || 
                empty($eventData['location']) || empty($eventData['start_date']) || empty($eventData['end_date'])) {
                flash('event_message', '⚠️ Please fill all required fields (Name, Type, Category, Venue, Start Date/Time, End Date/Time)', 'alert alert-danger');
                redirect('admin/create_event');
                return;
            }
            
            // Create event with error handling
            try {
                $eventModel = $this->model('Event');
                $result = $eventModel->createEvent($eventData);
                
                if ($result) {
                    // LOG ACTIVITY: Event created
                    $userModel = $this->model('M_Users');
                    $userModel->logActivity(
                        $_SESSION['user_id'],
                        'Event Created',
                        'Created new event: ' . $eventData['name'] . ' (' . $eventData['type'] . ')',
                        $_SERVER['REMOTE_ADDR'] ?? null,
                        $_SERVER['HTTP_USER_AGENT'] ?? null
                    );
                    
                    flash('event_message', '✅ Event "' . $eventData['name'] . '" created successfully!', 'alert alert-success');
                    redirect('admin/events');
                } else {
                    flash('event_message', '❌ Failed to create event. Please check the form data and try again.', 'alert alert-danger');
                    redirect('admin/create_event');
                }
            } catch (Exception $e) {
                error_log("Event creation exception: " . $e->getMessage());
                flash('event_message', '❌ Database error: ' . $e->getMessage(), 'alert alert-danger');
                redirect('admin/create_event');
            }
        } else {
            // Load coaches and trainers for the event coordinator dropdown
            $userModel = $this->model('M_Users');
            
            $data = [
                'title' => 'Create New Event - Elite Cricket Academy',
                'coordinators' => [] // You can load staff members here if needed
            ];
            $this->view('admin/create_event', $data);
        }
    }

    public function edit_event_form($id) {
        // This method loads event data and displays it in the wizard on the events page
        $eventModel = $this->model('Event');
        $event = $eventModel->getEventById($id);
        
        if (!$event) {
            flash('event_message', '❌ Event not found', 'alert alert-danger');
            redirect('admin/events');
            return;
        }
        
        // Get all events for the tables
        $upcomingEvents = $eventModel->getUpcomingEvents(10);
        $pastEvents = $eventModel->getPastEvents(10);
        
        // Calculate stats
        $totalEvents = $eventModel->getTotalEvents();
        $upcomingCount = count($eventModel->getUpcomingEvents(1000));
        
        // Get event type counts
        $db = new Database();
        $db->query('SELECT Type, COUNT(*) as count FROM Event GROUP BY Type');
        $typeCounts = $db->resultSet();
        
        $tournaments = 0;
        $trainingSessions = 0;
        $matches = 0;
        
        foreach ($typeCounts as $typeCount) {
            $type = $typeCount->Type;
            $count = (int)$typeCount->count;
            
            if ($type == 'Tournament') {
                $tournaments = $count;
            } elseif ($type == 'Training Camp') {
                $trainingSessions = $count;
            } elseif ($type == 'Match') {
                $matches = $count;
            }
        }
        
        $data = [
            'title' => 'Edit Event - Elite Cricket Academy',
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'recentEvents' => $eventModel->getRecentEvents(5),
            'eventStats' => [
                'totalEvents' => $totalEvents,
                'upcomingCount' => $upcomingCount,
                'pastCount' => $totalEvents - $upcomingCount,
                'tournaments' => $tournaments,
                'trainingSessions' => $trainingSessions,
                'matches' => $matches
            ],
            'editEvent' => $event, // Event data to populate the wizard
            'editMode' => true // Flag to trigger wizard opening
        ];
        
        $this->view('admin/events', $data);
    }

    public function edit_event($id) {
        // Debug using error_log only (no HTML output)
        error_log("Admin::edit_event() called with ID: $id");
        
        $eventModel = $this->model('Event');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // DEBUG: Log POST data
            error_log("=== Edit Event POST received ===");
            error_log("POST data: " . print_r($_POST, true));
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Combine date and time fields - FIX: Use correct field names from form
            // Form sends: StartDate_date, StartTime, EndDate_date, EndTime
            $startDateTime = $_POST['StartDate_date'] . ' ' . $_POST['StartTime'] . ':00';
            $endDateTime = $_POST['EndDate_date'] . ' ' . $_POST['EndTime'] . ':00';
            
            error_log("Start DateTime: " . $startDateTime);
            error_log("End DateTime: " . $endDateTime);
            
            // Prepare event data with exact column names
            $eventData = [
                'id' => $id,
                'name' => trim($_POST['Name']),
                'type' => $_POST['Type'],
                'category' => $_POST['Category'] ?? null,
                'description' => !empty($_POST['Description']) ? trim($_POST['Description']) : null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'location' => !empty($_POST['Location']) ? trim($_POST['Location']) : null,
                'status' => $_POST['Status'] ?? 'upcoming',
                'max_participants' => !empty($_POST['MaxParticipants']) ? intval($_POST['MaxParticipants']) : null,
                'registration_fee' => !empty($_POST['RegistrationFee']) ? floatval($_POST['RegistrationFee']) : null,
                'registration_start' => !empty($_POST['RegistrationStart']) ? $_POST['RegistrationStart'] . ':00' : null,
                'registration_end' => !empty($_POST['RegistrationEnd']) ? $_POST['RegistrationEnd'] . ':00' : null,
                'primary_contact' => !empty($_POST['PrimaryContact']) ? trim($_POST['PrimaryContact']) : null,
                'contact_email' => !empty($_POST['ContactEmail']) ? trim($_POST['ContactEmail']) : null,
                'contact_phone' => !empty($_POST['ContactPhone']) ? trim($_POST['ContactPhone']) : null
            ];
            
            error_log("Event data prepared for update: " . print_r($eventData, true));
            
            // Update event
            $result = $eventModel->updateEvent($eventData);
            
            if ($result) {
                // LOG ACTIVITY: Event updated
                $userModel = $this->model('M_Users');
                $userModel->logActivity(
                    $_SESSION['user_id'],
                    'Event Updated',
                    'Updated event: ' . $eventData['name'] . ' (' . $eventData['type'] . ')',
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                flash('event_message', '✅ Event updated successfully!', 'alert alert-success');
                redirect('admin/events');
            } else {
                flash('event_message', '❌ Failed to update event. Please try again.', 'alert alert-danger');
                redirect('admin/edit_event/' . $id);
            }
        } else {
            // GET request - Display edit form
            $event = $eventModel->getEventById($id);
            
            if (!$event) {
                flash('event_message', '❌ Event not found', 'alert alert-danger');
                redirect('admin/events');
                return;
            }
            
            $data = [
                'title' => 'Edit Event - Elite Cricket Academy',
                'event' => $event
            ];
            $this->view('admin/edit_event', $data);
        }
    }

    public function delete_event($id) {
        $eventModel = $this->model('Event');
        
        // Get event details before deletion for activity log
        $event = $eventModel->getEventById($id);
        $eventName = $event ? $event->Name : 'Event #' . $id;
        
        // Check if event can be deleted (6 months after end date)
        if (!$eventModel->canDeleteEvent($id)) {
            flash('event_message', 'This event cannot be deleted yet. Events can only be deleted 6 months after they have ended.', 'alert alert-warning');
            redirect('admin/events');
            return;
        }
        
        $result = $eventModel->deleteEvent($id);
        
        if ($result) {
            // LOG ACTIVITY: Event deleted
            $userModel = $this->model('M_Users');
            $userModel->logActivity(
                $_SESSION['user_id'],
                'Event Deleted',
                'Deleted event: ' . $eventName,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
            
            flash('event_message', 'Event deleted successfully');
        } else {
            flash('event_message', 'Something went wrong', 'alert alert-danger');
        }
        
        redirect('admin/events');
    }

    public function get_calendar_events() {
        header('Content-Type: application/json');
        $eventModel = $this->model('Event');
        $events = $eventModel->getCalendarEvents();
        echo json_encode($events);
    }

    public function get_event($id) {
        header('Content-Type: application/json');
        $eventModel = $this->model('Event');
        $event = $eventModel->getEventById($id);
        
        if ($event) {
            // Add proper field mapping for the frontend
            $event['EventName'] = $event['Name'] ?? '';
            $event['EventType'] = $event['Type'] ?? '';
            $event['EventDate'] = $event['StartDate'] ?? '';
            $event['EventTime'] = isset($event['StartDate']) ? date('H:i', strtotime($event['StartDate'])) : '';
            $event['RegistrationDeadline'] = $event['RegistrationEnd'] ?? '';
            
            echo json_encode([
                'success' => true,
                'event' => $event
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Event not found'
            ]);
        }
    }

    public function update_event($id) {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $eventModel = $this->model('Event');
            
            // Prepare data for update - only allow certain fields to be edited
            $data = [
                'EventID' => $id,
                'EventName' => trim($_POST['event_name']),
                'Description' => trim($_POST['description']),
                'Location' => trim($_POST['location']),
                'EventDate' => $_POST['event_date'],
                'EventTime' => $_POST['event_time'],
                'RegistrationDeadline' => $_POST['registration_deadline'],
                'MaxParticipants' => $_POST['max_participants']
            ];
            
            // Call model method to update
            if ($eventModel->updateEvent($data)) {
                // LOG ACTIVITY: Event updated
                $userModel = $this->model('M_Users');
                $logResult = $userModel->logActivity(
                    $_SESSION['user_id'],
                    'Event Updated',
                    'Updated event: ' . $data['EventName'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                // Debug logging
                error_log("Activity logged for event update: " . ($logResult ? 'SUCCESS' : 'FAILED'));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Event updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update event'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
    }

    public function finance() {
        // Load Finance model for real database data
        $financeModel = $this->model('Finance');
        
        // Get revenue statistics
        $stats = $financeModel->getRevenueStats();
        $revenueCategories = $financeModel->getRevenueByCategory();
        $recentTransactions = $financeModel->getRecentTransactions(15);
        $monthlyData = $financeModel->getMonthlyData(12);
        $topSources = $financeModel->getTopRevenueSources(5);
        
        $data = [
            'title' => 'Finance Management - Elite Cricket Academy',
            'totalRevenue' => $stats['total'],
            'monthlyRevenue' => $stats['monthly'],
            'dailyAverage' => $stats['daily_average'],
            'growthRate' => $stats['growth_rate'],
            'pendingCount' => $stats['pending_count'],
            'revenueCategories' => $revenueCategories,
            'recentTransactions' => $recentTransactions,
            'monthlyData' => $monthlyData,
            'topSources' => $topSources
        ];
        
        $this->view('admin/finance', $data);
    }

    // Feedback Monitoring
    public function feedback() {
        $feedbackModel = $this->model('Feedback');
        
        // Get all feedbacks from database
        $allFeedbacks = $feedbackModel->getAllFeedbacks();
        
        // Separate by status (database has: pending, reviewed, resolved)
        $pendingFeedbacks = array_filter($allFeedbacks, function($f) {
            return isset($f['status']) && $f['status'] === 'pending';
        });
        
        $reviewedFeedbacks = array_filter($allFeedbacks, function($f) {
            return isset($f['status']) && $f['status'] === 'reviewed';
        });
        
        $resolvedFeedbacks = array_filter($allFeedbacks, function($f) {
            return isset($f['status']) && $f['status'] === 'resolved';
        });
        
        $data = [
            'title' => 'Feedback Monitoring - Elite Cricket Academy',
            'allFeedbacks' => $allFeedbacks,
            'pendingFeedbacks' => array_values($pendingFeedbacks),
            'reviewedFeedbacks' => array_values($reviewedFeedbacks),
            'resolvedFeedbacks' => array_values($resolvedFeedbacks),
            'feedbackStats' => [
                'total' => count($allFeedbacks),
                'pending' => count($pendingFeedbacks),
                'reviewed' => count($reviewedFeedbacks),
                'resolved' => count($resolvedFeedbacks),
                'highPriority' => count(array_filter($allFeedbacks, function($f) {
                    return isset($f['priority']) && $f['priority'] === 'high';
                })),
                'todayCount' => count(array_filter($allFeedbacks, function($f) {
                    return isset($f['created_at']) && date('Y-m-d', strtotime($f['created_at'])) === date('Y-m-d');
                }))
            ]
        ];
        
        $this->view('admin/feedback', $data);
    }

    // Update feedback status via AJAX
    public function updateFeedbackStatus() {
        header('Content-Type: application/json');
        
        if ($_POST && isset($_POST['feedback_id']) && isset($_POST['status'])) {
            $feedbackModel = $this->model('Feedback');
            $response = isset($_POST['response']) ? $_POST['response'] : '';
            
            $result = $feedbackModel->updateFeedbackStatus(
                $_POST['feedback_id'],
                $_POST['status'],
                $response
            );
            
            if ($result) {
                // LOG ACTIVITY: Feedback status updated
                $userModel = $this->model('M_Users');
                $userModel->logActivity(
                    $_SESSION['user_id'],
                    'Feedback Updated',
                    'Updated feedback #' . $_POST['feedback_id'] . ' status to: ' . $_POST['status'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
            }
            
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }

    // Delete feedback via AJAX
    public function deleteFeedback() {
        header('Content-Type: application/json');
        
        if ($_POST && isset($_POST['feedback_id'])) {
            $feedbackModel = $this->model('Feedback');
            $result = $feedbackModel->deleteFeedback($_POST['feedback_id']);
            
            if ($result) {
                // LOG ACTIVITY: Feedback deleted
                $userModel = $this->model('M_Users');
                $userModel->logActivity(
                    $_SESSION['user_id'],
                    'Feedback Deleted',
                    'Deleted feedback #' . $_POST['feedback_id'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
            }
            
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }

    // Add new staff member
    public function add_staff() {
        // CRITICAL: Start output buffering FIRST to catch any PHP errors/warnings
        ob_start();
        
        // Disable error display for this endpoint (log only)
        ini_set('display_errors', 0);
        
        try {
            // Set JSON header
            header('Content-Type: application/json; charset=utf-8');
            
            // Only accept POST requests
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method. POST required.');
            }
            
            // Log incoming data for debugging
            error_log("=== Add Staff Request ===");
            error_log("POST data: " . print_r($_POST, true));
            error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
            
            // Sanitize POST data (using FILTER_SANITIZE_FULL_SPECIAL_CHARS instead of deprecated FILTER_SANITIZE_STRING)
            $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if ($postData === null || $postData === false) {
                throw new Exception('Invalid form data received');
            }
            
            // Validate required fields
            $requiredFields = ['fullName', 'dateOfBirth', 'phone', 'email', 'address', 'username', 'role'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($postData[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                ob_end_clean(); // Clear buffer
                echo json_encode([
                    'status' => 'error',
                    'success' => false, 
                    'message' => 'Missing required fields: ' . implode(', ', $missingFields)
                ]);
                return;
            }
            
            // Validate date of birth
            if (!empty($postData['dateOfBirth'])) {
                $dob = new DateTime($postData['dateOfBirth']);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                
                if ($age < 16) {
                    ob_end_clean();
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Staff member must be at least 16 years old'
                    ]);
                    return;
                } elseif ($age > 100) {
                    ob_end_clean();
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Please enter a valid date of birth'
                    ]);
                    return;
                } elseif ($dob > $today) {
                    ob_end_clean();
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Date of birth cannot be in the future'
                    ]);
                    return;
                }
            }
            
            // Load user model
            $userModel = $this->model('M_Users');
            
            // Check if username already exists
            if ($userModel->findUserByUsername($postData['username'])) {
                ob_end_clean(); // Clear buffer
                echo json_encode([
                    'status' => 'error',
                    'success' => false, 
                    'message' => 'Username already exists. Please choose a different username.'
                ]);
                return;
            }
            
            // Check if email already exists
            if ($userModel->findUserByEmail($postData['email'])) {
                ob_end_clean(); // Clear buffer
                echo json_encode([
                    'status' => 'error',
                    'success' => false, 
                    'message' => 'Email already exists. Please use a different email.'
                ]);
                return;
            }
            
            // Hash the default password
            $defaultPassword = 'staff123456';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            // Get CreatedBy - check if user_id exists in User table, otherwise set to NULL
            $createdBy = null;
            if (isset($_SESSION['user_id'])) {
                $userModel = $this->model('M_Users');
                $existingUser = $userModel->getUserById($_SESSION['user_id']);
                if ($existingUser) {
                    $createdBy = $_SESSION['user_id'];
                }
            }
            
            // Prepare staff data
            $staffData = [
                'fullName' => trim($postData['fullName']),
                'dateOfBirth' => $postData['dateOfBirth'],
                'phone' => trim($postData['phone']),
                'email' => trim($postData['email']),
                'address' => trim($postData['address']),
                'school' => !empty($postData['school']) ? trim($postData['school']) : null,
                'role' => $postData['role'],
                'username' => trim($postData['username']),
                'passwordHash' => $hashedPassword,
                'status' => 'active',
                'createdBy' => $createdBy,  // Use validated createdBy
                'notes' => !empty($postData['notes']) ? trim($postData['notes']) : null
            ];
            
            // Create staff member
            $userId = $userModel->createStaff($staffData);
            
            if ($userId) {
                // LOG ACTIVITY: Staff member created
                $userModel->logActivity(
                    $_SESSION['user_id'] ?? 0,
                    'Staff Created',
                    'Added new staff member: ' . $staffData['fullName'] . ' (' . $staffData['role'] . ')',
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                error_log("✅ Staff member created successfully with ID: $userId");
                ob_end_clean(); // Clear any accumulated output
                echo json_encode([
                    'status' => 'success',
                    'success' => true, 
                    'message' => 'Staff member added successfully! Default password: staff123456',
                    'userId' => $userId,
                    'data' => [
                        'id' => $userId,
                        'username' => $postData['username'],
                        'name' => $postData['fullName'],
                        'role' => $postData['role']
                    ]
                ]);
            } else {
                throw new Exception('Failed to create staff member in database');
            }
            
        } catch (Exception $e) {
            // Log the full error
            error_log("❌ Staff creation error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Clear any output buffer
            ob_end_clean();
            
            // Return clean JSON error
            echo json_encode([
                'status' => 'error',
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        // Ensure we exit cleanly
        exit;
    }

    public function add_player() {
        // Start output buffering to catch any PHP errors/warnings
        ob_start();
        
        // Disable error display for this endpoint (log only)
        ini_set('display_errors', 0);
        
        try {
            // Set JSON header
            header('Content-Type: application/json; charset=utf-8');
            
            // Only accept POST requests
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method. POST required.');
            }
            
            // Get JSON input
            $jsonInput = file_get_contents('php://input');
            $postData = json_decode($jsonInput, true);
            
            if ($postData === null) {
                // Fallback to POST data if JSON parsing fails
                $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            
            // Log incoming data for debugging
            error_log("=== Add Player Request ===");
            error_log("POST data: " . print_r($postData, true));
            
            if ($postData === null || $postData === false) {
                throw new Exception('Invalid form data received');
            }
            
            // Validate required fields
            $requiredFields = ['fullName', 'dateOfBirth', 'phone', 'email', 'username', 'subscriptionType'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($postData[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                ob_end_clean();
                echo json_encode([
                    'status' => 'error',
                    'success' => false, 
                    'message' => 'Missing required fields: ' . implode(', ', $missingFields)
                ]);
                return;
            }
            
            // Validate date of birth
            if (!empty($postData['dateOfBirth'])) {
                $dob = new DateTime($postData['dateOfBirth']);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                
                if ($age < 5) {
                    ob_end_clean();
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Player must be at least 5 years old'
                    ]);
                    return;
                } elseif ($age > 100) {
                    ob_end_clean();
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Please enter a valid date of birth'
                    ]);
                    return;
                } elseif ($dob > $today) {
                    ob_end_clean();
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Date of birth cannot be in the future'
                    ]);
                    return;
                }
            }
            
            // Load user model
            $userModel = $this->model('M_Users');
            
            // Check if username already exists
            if ($userModel->findUserByUsername($postData['username'])) {
                ob_end_clean();
                echo json_encode([
                    'status' => 'error',
                    'success' => false, 
                    'message' => 'Username already exists. Please choose a different username.'
                ]);
                return;
            }
            
            // Check if email already exists
            if ($userModel->findUserByEmail($postData['email'])) {
                ob_end_clean();
                echo json_encode([
                    'status' => 'error',
                    'success' => false, 
                    'message' => 'Email already exists. Please use a different email.'
                ]);
                return;
            }
            
            // Hash the default password
            $defaultPassword = 'player123456';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            // Get CreatedBy
            $createdBy = null;
            if (isset($_SESSION['user_id'])) {
                $existingUser = $userModel->getUserById($_SESSION['user_id']);
                if ($existingUser) {
                    $createdBy = $_SESSION['user_id'];
                }
            }
            
            // Prepare player data
            $playerData = [
                'fullName' => trim($postData['fullName']),
                'dateOfBirth' => $postData['dateOfBirth'],
                'phone' => trim($postData['phone']),
                'email' => trim($postData['email']),
                'address' => !empty($postData['address']) ? trim($postData['address']) : null,
                'jerseyNumber' => !empty($postData['jerseyNumber']) ? intval($postData['jerseyNumber']) : null,
                'battingStyle' => !empty($postData['battingStyle']) ? trim($postData['battingStyle']) : null,
                'bowlingStyle' => !empty($postData['bowlingStyle']) ? trim($postData['bowlingStyle']) : null,
                'subscriptionType' => $postData['subscriptionType'],
                'username' => trim($postData['username']),
                'passwordHash' => $hashedPassword,
                'status' => 'active',
                'createdBy' => $createdBy
            ];
            
            // Create player
            $userId = $userModel->createPlayer($playerData);
            
            if ($userId) {
                // LOG ACTIVITY: Player created
                $userModel->logActivity(
                    $_SESSION['user_id'] ?? 0,
                    'Player Created',
                    'Added new player: ' . $playerData['fullName'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                error_log("✅ Player created successfully with ID: $userId");
                ob_end_clean();
                echo json_encode([
                    'status' => 'success',
                    'success' => true, 
                    'message' => 'Player added successfully! Default password: player123456',
                    'userId' => $userId,
                    'data' => [
                        'id' => $userId,
                        'username' => $postData['username'],
                        'name' => $postData['fullName']
                    ]
                ]);
            } else {
                throw new Exception('Failed to create player in database');
            }
            
        } catch (Exception $e) {
            // Log the full error
            error_log("❌ Player creation error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Clear any output buffer
            ob_end_clean();
            
            // Return clean JSON error
            echo json_encode([
                'status' => 'error',
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        // Ensure we exit cleanly
        exit;
    }

    public function update_player() {
        // Set up fatal error handler
        register_shutdown_function(function() {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                error_log("FATAL ERROR in update_player: " . print_r($error, true));
                if (ob_get_level()) ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Server error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']
                ]);
            }
        });
        
        // Disable any error display and use error_log instead
        @ini_set('display_errors', '0');
        error_reporting(E_ALL);
        
        // Start output buffering FIRST - before anything else
        while (ob_get_level()) ob_end_clean();
        ob_start();
        
        // Set JSON header immediately
        header('Content-Type: application/json');
        
        error_log("update_player method called");
        
        // Check authentication for AJAX request
        if (!isLoggedIn() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
            error_log("Authentication failed for update_player");
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            // Only allow POST requests
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Get JSON input
            $json = file_get_contents('php://input');
            error_log("Raw JSON input: " . $json);
            
            $postData = json_decode($json, true);
            
            // Fallback to $_POST if JSON is empty
            if (empty($postData)) {
                $postData = $_POST;
            }
            
            error_log("Update player request data: " . print_r($postData, true));
            
            // Validate required fields
            $required = ['playerId', 'fullName', 'email', 'phone', 'subscriptionType', 'status'];
            foreach ($required as $field) {
                if (empty($postData[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
            
            error_log("Validation passed, loading model...");
            
            // Load user model
            $userModel = $this->model('M_Users');
            error_log("Model loaded successfully");
            
            // Check if email is already used by another player
            if (!empty($postData['email'])) {
                $existingUser = $userModel->getUserByEmail($postData['email']);
                if ($existingUser && $existingUser->UserID != $postData['playerId']) {
                    throw new Exception('Email is already registered to another user');
                }
            }
            
            error_log("Email check passed, preparing data...");
            
            // Prepare update data
            $playerData = [
                'playerId' => $postData['playerId'],
                'fullName' => trim($postData['fullName']),
                'email' => trim($postData['email']),
                'phone' => trim($postData['phone']),
                'address' => trim($postData['address'] ?? ''),
                'jerseyNumber' => !empty($postData['jerseyNumber']) ? intval($postData['jerseyNumber']) : null,
                'battingStyle' => !empty($postData['battingStyle']) ? $postData['battingStyle'] : null,
                'bowlingStyle' => !empty($postData['bowlingStyle']) ? $postData['bowlingStyle'] : null,
                'subscriptionType' => $postData['subscriptionType'],
                'status' => $postData['status']
            ];
            
            error_log("Data prepared, calling updatePlayer...");
            
            // Update player
            $result = $userModel->updatePlayer($playerData);
            
            error_log("updatePlayer returned: " . ($result ? 'true' : 'false'));
            
            if ($result) {
                // LOG ACTIVITY: Player updated
                $userModel->logActivity(
                    $_SESSION['user_id'] ?? 0,
                    'Player Updated',
                    'Updated player: ' . $playerData['fullName'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                error_log("✅ Player updated successfully: " . $playerData['playerId']);
                
                // Clear buffer and output JSON
                $output = ob_get_clean();
                if (!empty($output)) {
                    error_log("WARNING: Unexpected output before JSON: " . $output);
                }
                
                echo json_encode([
                    'status' => 'success',
                    'success' => true, 
                    'message' => 'Player updated successfully!',
                    'data' => [
                        'id' => $playerData['playerId'],
                        'name' => $playerData['fullName']
                    ]
                ]);
            } else {
                throw new Exception('Failed to update player in database');
            }
            
        } catch (Exception $e) {
            error_log("❌ Player update error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Clear any buffered content
            if (ob_get_level()) {
                $output = ob_get_clean();
                if (!empty($output)) {
                    error_log("Buffered output during error: " . $output);
                }
            }
            
            echo json_encode([
                'status' => 'error',
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
        
        exit;
    }

    public function delete_player() {
        // Set up fatal error handler
        register_shutdown_function(function() {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                error_log("FATAL ERROR in delete_player: " . print_r($error, true));
                if (ob_get_level()) ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Server error: ' . $error['message']
                ]);
            }
        });
        
        // Disable any error display
        @ini_set('display_errors', '0');
        error_reporting(E_ALL);
        
        // Start output buffering
        while (ob_get_level()) ob_end_clean();
        ob_start();
        
        // Set JSON header
        header('Content-Type: application/json');
        
        error_log("delete_player method called");
        
        // Check authentication
        if (!isLoggedIn() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
            error_log("Authentication failed for delete_player");
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            // Only allow POST requests
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Get JSON input
            $json = file_get_contents('php://input');
            error_log("Delete player raw JSON: " . $json);
            
            $postData = json_decode($json, true);
            
            if (empty($postData) || empty($postData['playerId'])) {
                throw new Exception('Missing player ID');
            }
            
            $playerId = $postData['playerId'];
            error_log("Deleting player ID: " . $playerId);
            
            // Load user model
            $userModel = $this->model('M_Users');
            
            // Get player info before deletion for logging
            $player = $userModel->getUserById($playerId);
            if (!$player) {
                throw new Exception('Player not found');
            }
            
            // Check if user is actually a player
            if ($player->Role !== 'Player') {
                throw new Exception('This user is not a player');
            }
            
            error_log("Deleting player: " . $player->Name);
            
            // Delete player (will delete from both User and PlayerProfile tables)
            $result = $userModel->deleteUser($playerId);
            
            if ($result) {
                // LOG ACTIVITY: Player deleted
                $userModel->logActivity(
                    $_SESSION['user_id'] ?? 0,
                    'Player Deleted',
                    'Deleted player: ' . $player->Name . ' (ID: ' . $playerId . ')',
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                error_log("✅ Player deleted successfully: " . $playerId);
                
                $output = ob_get_clean();
                echo json_encode([
                    'status' => 'success',
                    'success' => true,
                    'message' => 'Player deleted successfully!'
                ]);
            } else {
                throw new Exception('Failed to delete player from database');
            }
            
        } catch (Exception $e) {
            error_log("❌ Player deletion error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            if (ob_get_level()) {
                $output = ob_get_clean();
                if (!empty($output)) {
                    error_log("Buffered output during error: " . $output);
                }
            }
            
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        
        exit;
    }

    // Player Management
    public function players() {
        // Fetch all players with their profile information
        $userModel = $this->model('M_Users');
        $players = $userModel->getAllPlayersWithProfile();
        $playerStats = $userModel->getPlayerStats();
        
        $data = [
            'title' => 'Player Management - Elite Cricket Academy',
            'players' => $players,
            'stats' => $playerStats
        ];
        
        $this->view('admin/players', $data);
    }

    // Player Statistics
    public function player_statistics($playerId = null) {
        // In production, fetch real player data by ID
        $data = [
            'title' => 'Player Statistics - Elite Cricket Academy',
            'playerId' => $playerId
        ];
        
        $this->view('admin/player_statistics', $data);
    }

    // ==================== SESSION SLOT MANAGEMENT ====================

    public function session_slots() {
        $sessionModel = $this->model('M_Session');
        $data = [
            'title'        => 'Session Slot Management - Elite Cricket Academy',
            'slots'        => $sessionModel->getAllSessions(['status' => 'open']),
            'recentActive' => $sessionModel->getAllSessions(['status' => 'active']),
            'openCount'    => $sessionModel->getOpenSlotsCount(),
        ];
        $this->view('admin/session_slots', $data);
    }

    public function create_session_slot() {
        ob_start();
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'POST required']);
                exit;
            }
            $sessionType = filter_input(INPUT_POST, 'session_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $sessionMode = filter_input(INPUT_POST, 'session_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $name        = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $date        = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $startTime   = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $endTime     = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $location    = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $maxPart     = filter_input(INPUT_POST, 'max_participants', FILTER_VALIDATE_INT) ?: 10;
            $price       = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT) ?: 0.00;
            $isRecurring = isset($_POST['is_recurring']) ? 1 : 0;

            if (!in_array($sessionType, ['Coaching', 'Physical Training'])) {
                ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Invalid session type']); exit;
            }
            if (!$name || !$date || !$startTime || !$endTime) {
                ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Name, date, start and end time are required']); exit;
            }
            if (strtotime($date) < strtotime(date('Y-m-d'))) {
                ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Date must be today or in the future']); exit;
            }
            if (strtotime($endTime) <= strtotime($startTime)) {
                ob_end_clean(); echo json_encode(['success' => false, 'message' => 'End time must be after start time']); exit;
            }

            $sessionModel = $this->model('M_Session');
            $slotId = $sessionModel->createAdminSlot([
                'session_type'    => $sessionType,
                'session_mode'    => $sessionMode ?? 'Group',
                'name'            => $name,
                'date'            => $date,
                'start_time'      => $startTime,
                'end_time'        => $endTime,
                'location'        => $location ?? '',
                'max_participants'=> $maxPart,
                'price'           => $price,
                'is_recurring'    => $isRecurring,
            ]);

            if ($slotId) {
                $userModel = $this->model('M_Users');
                $userModel->logActivity($_SESSION['user_id'], 'CREATE_SESSION_SLOT',
                    "Created session slot ID $slotId: $name on $date",
                    $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
                ob_end_clean();
                echo json_encode(['success' => true, 'slotId' => $slotId, 'message' => 'Session slot created successfully']);
            } else {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to create session slot']);
            }
        } catch (Exception $e) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function delete_slot($id = null) {
        header('Content-Type: application/json');
        $id = intval($id ?? $_POST['slot_id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid slot ID']);
            exit;
        }
        $sessionModel = $this->model('M_Session');
        // Only allow deleting open (unclaimed) slots
        if ($sessionModel->isSlotClaimed($id)) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete a slot that has already been claimed']);
            exit;
        }
        $result = $sessionModel->cancelAdminSlot($id);
        if ($result) {
            $userModel = $this->model('M_Users');
            $userModel->logActivity($_SESSION['user_id'], 'DELETE_SESSION_SLOT',
                "Deleted session slot ID $id",
                $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
        }
        echo json_encode(['success' => $result, 'message' => $result ? 'Slot deleted' : 'Failed to delete slot']);
        exit;
    }

    // Reports
    public function reports() {
        // Get filter parameters
        $filters = [
            'status' => $_GET['status'] ?? 'all',
            'subscription' => $_GET['subscription'] ?? 'all',
            'batting' => $_GET['batting'] ?? 'all',
            'bowling' => $_GET['bowling'] ?? 'all',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Get players with filters
        $userModel = $this->model('M_Users');
        $players = $userModel->getPlayersForReport($filters);
        
        $data = [
            'title' => 'Player Reports - Elite Cricket Academy',
            'players' => $players,
            'filters' => $filters,
            'totalPlayers' => count($players)
        ];
        
        $this->view('admin/reports', $data);
    }

    // Generate Event Summary Report
    public function generate_event_report() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $period = $_POST['period'] ?? 'month';
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            
            // Calculate date range based on period
            if ($period == 'custom' && $startDate && $endDate) {
                $dateFrom = $startDate;
                $dateTo = $endDate;
            } else {
                switch($period) {
                    case 'week':
                        $dateFrom = date('Y-m-d', strtotime('-7 days'));
                        break;
                    case 'quarter':
                        $dateFrom = date('Y-m-d', strtotime('-3 months'));
                        break;
                    case 'year':
                        $dateFrom = date('Y-m-d', strtotime('-1 year'));
                        break;
                    case 'month':
                    default:
                        $dateFrom = date('Y-m-d', strtotime('-1 month'));
                        break;
                }
                $dateTo = date('Y-m-d');
            }
            
            // Get event data from database
            $eventModel = $this->model('Event');
            $db = new Database();
            
            // Get event summary statistics
            $db->query('SELECT 
                COUNT(*) as total_events,
                SUM(CASE WHEN Status = "completed" THEN 1 ELSE 0 END) as completed_events,
                SUM(CASE WHEN Status = "upcoming" THEN 1 ELSE 0 END) as upcoming_events,
                SUM(CASE WHEN Status = "cancelled" THEN 1 ELSE 0 END) as cancelled_events,
                SUM(CASE WHEN Type = "Tournament" THEN 1 ELSE 0 END) as tournaments,
                SUM(CASE WHEN Type = "Training Camp" THEN 1 ELSE 0 END) as training_camps,
                SUM(CASE WHEN Type = "Match" THEN 1 ELSE 0 END) as matches
            FROM Event 
            WHERE StartDate >= :start_date AND StartDate <= :end_date');
            
            $db->bind(':start_date', $dateFrom);
            $db->bind(':end_date', $dateTo);
            $summary = $db->single();
            
            // Get detailed event list
            $db->query('SELECT 
                EventID,
                Name,
                Type,
                StartDate,
                EndDate,
                Location,
                Status,
                MaxParticipants
            FROM Event 
            WHERE StartDate >= :start_date AND StartDate <= :end_date
            ORDER BY StartDate DESC');
            
            $db->bind(':start_date', $dateFrom);
            $db->bind(':end_date', $dateTo);
            $events = $db->resultSet();
            
            // Get event participation statistics
            $db->query('SELECT 
                e.Name as event_name,
                e.Type as event_type,
                e.StartDate,
                COUNT(ep.PlayerID) as participant_count,
                e.MaxParticipants as max_participants
            FROM Event e
            LEFT JOIN EventParticipation ep ON e.EventID = ep.EventID
            WHERE e.StartDate >= :start_date AND e.StartDate <= :end_date
            GROUP BY e.EventID
            ORDER BY e.StartDate DESC');
            
            $db->bind(':start_date', $dateFrom);
            $db->bind(':end_date', $dateTo);
            $participation = $db->resultSet();
            
            // Prepare report data
            $reportData = [
                'success' => true,
                'report_type' => 'Event Summary Report',
                'period' => $period,
                'date_range' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ],
                'summary' => [
                    'total_events' => (int)$summary->total_events,
                    'completed_events' => (int)$summary->completed_events,
                    'upcoming_events' => (int)$summary->upcoming_events,
                    'cancelled_events' => (int)$summary->cancelled_events,
                    'tournaments' => (int)$summary->tournaments,
                    'training_camps' => (int)$summary->training_camps,
                    'matches' => (int)$summary->matches
                ],
                'events' => $events,
                'participation' => $participation,
                'generated_at' => date('Y-m-d H:i:s')
            ];
            
            echo json_encode($reportData);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Profile Management
    public function profile() {
        // Get comprehensive user profile data
        $userModel = $this->model('M_Users');
        $userId = $_SESSION['user_id'] ?? 1;
        $userProfile = $userModel->getUserWithProfile($userId);
        
        $data = [
            'title' => 'My Profile - Admin',
            'user' => $userProfile
        ];
        
        $this->view('admin/profile', $data);
    }

    // Update Profile
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data (using FILTER_SANITIZE_FULL_SPECIAL_CHARS instead of deprecated FILTER_SANITIZE_STRING)
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'] ?? 1;
            
            // Get current user data to preserve role and status
            $currentUser = $userModel->getUserWithProfile($userId);
            
            // Update basic user info
            $userData = [
                'user_id' => $userId,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number'] ?? $_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'school' => trim($_POST['school'] ?? ''),
                'role' => $currentUser->Role, // Preserve role
                'status' => $currentUser->Status ?? 'active' // Preserve status
            ];
            
            if ($userModel->updateUser($userData)) {
                // Update session name if changed
                $_SESSION['user_name'] = $userData['name'];
                
                flash('profile_message', 'Profile updated successfully!', 'alert alert-success');
            } else {
                flash('profile_message', 'Failed to update profile', 'alert alert-danger');
            }
            
            redirect('admin/profile');
        }
    }

    // Upload/Update Profile Image
    public function uploadProfileImage() {
        // Set JSON response header
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            $maxFileSize = 2 * 1024 * 1024;
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
            $userId = $_SESSION['user_id'] ?? 1;
            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            
            // Set upload directory
            $projectRoot = dirname(APPROOT);
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
                // Save relative path to database
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
            $userId = $_SESSION['user_id'] ?? 1;
            
            try {
                $userModel = $this->model('M_Users');
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

    // Update Staff Member
    public function update_staff() {
        // Set JSON response header
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get JSON data from request body
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Invalid data format']);
                return;
            }
            
            $staffId = $data['staffId'] ?? null;
            
            if (!$staffId) {
                echo json_encode(['success' => false, 'message' => 'Staff ID is required']);
                return;
            }
            
            try {
                $userModel = $this->model('M_Users');
                
                // Check if staff member exists
                $staff = $userModel->getUserById($staffId);
                
                if (!$staff) {
                    echo json_encode(['success' => false, 'message' => 'Staff member not found']);
                    return;
                }
                
                // Prepare update data
                $updateData = [
                    'user_id' => $staffId,
                    'first_name' => $data['firstName'] ?? '',
                    'last_name' => $data['lastName'] ?? '',
                    'email' => $data['email'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'role' => $data['role'] ?? '',
                    'status' => $data['status'] ?? '',
                    'address' => $data['address'] ?? ''
                ];
                
                // Validate required fields
                if (empty($updateData['first_name']) || empty($updateData['last_name']) || 
                    empty($updateData['email']) || empty($updateData['phone'])) {
                    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
                    return;
                }
                
                // Update the staff member
                if ($userModel->updateStaff($updateData)) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Staff member updated successfully'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update staff member']);
                }
                
            } catch (Exception $e) {
                error_log("Error updating staff: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Delete Staff Member
    public function delete_staff($staffId = null) {
        // Set JSON response header
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get staff ID from URL parameter or POST data
            if (!$staffId && isset($_POST['staff_id'])) {
                $staffId = $_POST['staff_id'];
            }
            
            if (!$staffId) {
                echo json_encode(['success' => false, 'message' => 'Staff ID is required']);
                return;
            }
            
            try {
                $userModel = $this->model('M_Users');
                
                // Check if staff member exists and is not an admin
                $staff = $userModel->getUserById($staffId);
                
                if (!$staff) {
                    echo json_encode(['success' => false, 'message' => 'Staff member not found']);
                    return;
                }
                
                // Prevent deleting admin accounts
                if ($staff->Role === 'Admin') {
                    echo json_encode(['success' => false, 'message' => 'Cannot delete admin accounts']);
                    return;
                }
                
                // Prevent self-deletion
                if ($staffId == $_SESSION['user_id']) {
                    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
                    return;
                }
                
                // Delete the staff member
                if ($userModel->deleteUser($staffId)) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Staff member deleted successfully'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete staff member']);
                }
                
            } catch (Exception $e) {
                error_log("Error deleting staff: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
}
?>