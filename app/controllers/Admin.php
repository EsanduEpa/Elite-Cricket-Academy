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
        // Get last 6 activities from ActivityLog table
        // Includes user actions like registrations, logins, profile updates
        $recentActivities = $userModel->getRecentActivities(6);
        
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
                
                // Feedback received today - will need to add this method if needed
                'feedbackReceived' => 0  // Placeholder - can add getFeedbackToday() method
            ],
            
            // MONTHLY REVENUE (Placeholder until Finance model is created)
            // TODO: Create Finance model with getMonthlyRevenue() method
            // This should query Order/Transaction table and SUM payments for current month
            'monthlyRevenue' => 0  // Will show "RS 0" until Finance model implemented
        ];
        
        // STEP 8: LOAD DASHBOARD VIEW
        // Pass all database data to the view template
        // View file: app/views/admin/dashboard.php
        $this->view('admin/dashboard', $data);
    }
    
    // AJAX endpoint for refreshing dashboard data (interface demo only)
    public function refresh() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            // Simulate refreshed data with slight variations
            $refreshData = [
                'totalUsers' => 48, // Simulated increase
                'totalPendingFeedback' => 4, // Simulated decrease
                'todayStats' => [
                    'newRegistrations' => 4, // Simulated increase
                    'activeEvents' => 2,
                    'feedbackReceived' => 2 // Simulated increase
                ]
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
        
        // Check if event can be deleted (6 months after end date)
        if (!$eventModel->canDeleteEvent($id)) {
            flash('event_message', 'This event cannot be deleted yet. Events can only be deleted 6 months after they have ended.', 'alert alert-warning');
            redirect('admin/events');
            return;
        }
        
        $result = $eventModel->deleteEvent($id);
        
        if ($result) {
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
        // Sample finance data for interface demonstration
        $data = [
            'title' => 'Finance Management - Elite Cricket Academy',
            'totalRevenue' => 2450000, // LKR
            'monthlyRevenue' => 350000, // LKR
            'yearlyRevenue' => 2450000, // LKR
            'revenueCategories' => [
                'shop_sales' => [
                    'amount' => 650000,
                    'percentage' => 26.5,
                    'monthly' => 85000,
                    'growth' => 12.5
                ],
                'equipment_rental' => [
                    'amount' => 420000,
                    'percentage' => 17.1,
                    'monthly' => 55000,
                    'growth' => 8.3
                ],
                'facility_rental' => [
                    'amount' => 580000,
                    'percentage' => 23.7,
                    'monthly' => 75000,
                    'growth' => 15.2
                ],
                'membership_fees' => [
                    'amount' => 800000,
                    'percentage' => 32.7,
                    'monthly' => 135000,
                    'growth' => 10.8
                ]
            ],
            'recentPayments' => [
                [
                    'id' => 'PAY001',
                    'type' => 'Membership Fee',
                    'customer' => 'John Doe',
                    'amount' => 25000,
                    'date' => '2025-09-16',
                    'status' => 'completed',
                    'method' => 'Card'
                ],
                [
                    'id' => 'PAY002',
                    'type' => 'Equipment Purchase',
                    'customer' => 'Sarah Wilson',
                    'amount' => 15750,
                    'date' => '2025-09-16',
                    'status' => 'completed',
                    'method' => 'Cash'
                ],
                [
                    'id' => 'PAY003',
                    'type' => 'Ground Rental',
                    'customer' => 'City Sports Club',
                    'amount' => 45000,
                    'date' => '2025-09-15',
                    'status' => 'completed',
                    'method' => 'Bank Transfer'
                ],
                [
                    'id' => 'PAY004',
                    'type' => 'Equipment Rental',
                    'customer' => 'Mike Johnson',
                    'amount' => 8500,
                    'date' => '2025-09-15',
                    'status' => 'pending',
                    'method' => 'Card'
                ],
                [
                    'id' => 'PAY005',
                    'type' => 'Membership Fee',
                    'customer' => 'Emily Rodriguez',
                    'amount' => 30000,
                    'date' => '2025-09-14',
                    'status' => 'completed',
                    'method' => 'Online'
                ],
                [
                    'id' => 'PAY006',
                    'type' => 'Bowling Machine Rental',
                    'customer' => 'Elite Academy Branch',
                    'amount' => 12000,
                    'date' => '2025-09-14',
                    'status' => 'completed',
                    'method' => 'Cash'
                ],
                [
                    'id' => 'PAY007',
                    'type' => 'Cricket Bat Purchase',
                    'customer' => 'David Silva',
                    'amount' => 22500,
                    'date' => '2025-09-13',
                    'status' => 'completed',
                    'method' => 'Card'
                ],
                [
                    'id' => 'PAY008',
                    'type' => 'Net Practice Rental',
                    'customer' => 'Youth Cricket Team',
                    'amount' => 6000,
                    'date' => '2025-09-13',
                    'status' => 'completed',
                    'method' => 'Cash'
                ]
            ],
            'monthlyData' => [
                'January' => 185000,
                'February' => 220000,
                'March' => 195000,
                'April' => 240000,
                'May' => 285000,
                'June' => 310000,
                'July' => 295000,
                'August' => 265000,
                'September' => 350000
            ],
            'topSellingItems' => [
                [
                    'item' => 'Cricket Bats',
                    'quantity' => 45,
                    'revenue' => 180000,
                    'category' => 'shop_sales'
                ],
                [
                    'item' => 'Ground Rental',
                    'quantity' => 28,
                    'revenue' => 420000,
                    'category' => 'facility_rental'
                ],
                [
                    'item' => 'Annual Membership',
                    'quantity' => 35,
                    'revenue' => 525000,
                    'category' => 'membership_fees'
                ],
                [
                    'item' => 'Equipment Rental',
                    'quantity' => 120,
                    'revenue' => 240000,
                    'category' => 'equipment_rental'
                ],
                [
                    'item' => 'Protective Gear',
                    'quantity' => 38,
                    'revenue' => 152000,
                    'category' => 'shop_sales'
                ]
            ]
        ];
        
        $this->view('admin/finance', $data);
    }

    // Feedback Monitoring
    public function feedback() {
        $feedbackModel = $this->model('Feedback');
        
        // Get all feedbacks with different statuses
        $allFeedbacks = $feedbackModel->getAllFeedbacks();
        
        // Separate by status
        $pendingFeedbacks = array_filter($allFeedbacks, function($f) {
            return isset($f['status']) && $f['status'] === 'pending';
        });
        
        $inProgressFeedbacks = array_filter($allFeedbacks, function($f) {
            return isset($f['status']) && $f['status'] === 'in_progress';
        });
        
        $resolvedFeedbacks = array_filter($allFeedbacks, function($f) {
            return isset($f['status']) && $f['status'] === 'resolved';
        });
        
        // Add dummy data if no database results
        if (empty($allFeedbacks)) {
            $allFeedbacks = [
                [
                    'id' => 1,
                    'subject' => 'Training Quality Feedback',
                    'message' => 'The coaching sessions are excellent but need more practice time. The coaches are very supportive.',
                    'user_name' => 'John Smith',
                    'status' => 'pending',
                    'priority' => 'medium',
                    'category' => 'training',
                    'created_at' => '2025-10-18 10:30:00',
                    'user_email' => 'john.smith@email.com'
                ],
                [
                    'id' => 2,
                    'subject' => 'Facility Improvement Suggestion',
                    'message' => 'The changing rooms could use better lighting and ventilation. Also, more benches would be helpful.',
                    'user_name' => 'Sarah Johnson',
                    'status' => 'in_progress',
                    'priority' => 'low',
                    'category' => 'facilities',
                    'created_at' => '2025-10-17 14:15:00',
                    'user_email' => 'sarah.j@email.com'
                ],
                [
                    'id' => 3,
                    'subject' => 'Equipment Request',
                    'message' => 'We need more batting helmets for junior players. Current stock is insufficient for the growing number of students.',
                    'user_name' => 'Mike Wilson',
                    'status' => 'pending',
                    'priority' => 'high',
                    'category' => 'equipment',
                    'created_at' => '2025-10-17 09:45:00',
                    'user_email' => 'mike.w@email.com'
                ],
                [
                    'id' => 4,
                    'subject' => 'Schedule Conflict Issue',
                    'message' => 'There is a scheduling conflict between junior and senior training sessions on weekends.',
                    'user_name' => 'Emily Brown',
                    'status' => 'resolved',
                    'priority' => 'high',
                    'category' => 'scheduling',
                    'created_at' => '2025-10-15 16:20:00',
                    'resolved_at' => '2025-10-16 10:00:00',
                    'admin_response' => 'Schedule has been adjusted. Junior sessions now at 8 AM, seniors at 10 AM.',
                    'user_email' => 'emily.b@email.com'
                ],
                [
                    'id' => 5,
                    'subject' => 'Payment System Feedback',
                    'message' => 'The online payment system is great but could use more payment options like digital wallets.',
                    'user_name' => 'David Lee',
                    'status' => 'pending',
                    'priority' => 'medium',
                    'category' => 'system',
                    'created_at' => '2025-10-14 11:30:00',
                    'user_email' => 'david.lee@email.com'
                ],
                [
                    'id' => 6,
                    'subject' => 'Coach Performance Appreciation',
                    'message' => 'Coach Williams has been exceptional in improving my batting technique. Highly appreciate the dedication.',
                    'user_name' => 'Lisa Anderson',
                    'status' => 'resolved',
                    'priority' => 'low',
                    'category' => 'appreciation',
                    'created_at' => '2025-10-13 13:45:00',
                    'resolved_at' => '2025-10-14 09:00:00',
                    'admin_response' => 'Thank you for your positive feedback. We have shared it with Coach Williams.',
                    'user_email' => 'lisa.a@email.com'
                ],
                [
                    'id' => 7,
                    'subject' => 'Tournament Organization Query',
                    'message' => 'When will the registration open for the upcoming junior cricket championship?',
                    'user_name' => 'Robert Martinez',
                    'status' => 'in_progress',
                    'priority' => 'medium',
                    'category' => 'events',
                    'created_at' => '2025-10-12 15:00:00',
                    'user_email' => 'robert.m@email.com'
                ],
                [
                    'id' => 8,
                    'subject' => 'Parking Space Concern',
                    'message' => 'Limited parking space during peak hours. Parents have difficulty finding parking spots.',
                    'user_name' => 'Jennifer White',
                    'status' => 'pending',
                    'priority' => 'high',
                    'category' => 'facilities',
                    'created_at' => '2025-10-11 08:30:00',
                    'user_email' => 'jennifer.w@email.com'
                ],
                [
                    'id' => 9,
                    'subject' => 'Cafeteria Menu Suggestion',
                    'message' => 'It would be great to have more healthy food options in the cafeteria menu.',
                    'user_name' => 'Chris Taylor',
                    'status' => 'resolved',
                    'priority' => 'low',
                    'category' => 'services',
                    'created_at' => '2025-10-10 12:15:00',
                    'resolved_at' => '2025-10-11 14:30:00',
                    'admin_response' => 'New healthy menu items have been added. Check the updated menu board.',
                    'user_email' => 'chris.t@email.com'
                ],
                [
                    'id' => 10,
                    'subject' => 'Medical Facility Inquiry',
                    'message' => 'Is there a sports physiotherapist available on-site for injury consultations?',
                    'user_name' => 'Amanda Clark',
                    'status' => 'pending',
                    'priority' => 'medium',
                    'category' => 'medical',
                    'created_at' => '2025-10-09 10:00:00',
                    'user_email' => 'amanda.c@email.com'
                ],
                [
                    'id' => 11,
                    'subject' => 'Equipment Maintenance Issue',
                    'message' => 'Some of the bowling machines in Practice Net 2 are not working properly.',
                    'user_name' => 'Kevin Brown',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'category' => 'equipment',
                    'created_at' => '2025-10-08 14:45:00',
                    'user_email' => 'kevin.b@email.com'
                ],
                [
                    'id' => 12,
                    'subject' => 'Membership Benefits Query',
                    'message' => 'What additional benefits are included in the premium membership package?',
                    'user_name' => 'Rachel Green',
                    'status' => 'resolved',
                    'priority' => 'low',
                    'category' => 'membership',
                    'created_at' => '2025-10-07 11:20:00',
                    'resolved_at' => '2025-10-08 09:15:00',
                    'admin_response' => 'Premium membership includes priority booking, free equipment rental, and personalized training sessions.',
                    'user_email' => 'rachel.g@email.com'
                ]
            ];
            
            $pendingFeedbacks = array_filter($allFeedbacks, function($f) {
                return $f['status'] === 'pending';
            });
            
            $inProgressFeedbacks = array_filter($allFeedbacks, function($f) {
                return $f['status'] === 'in_progress';
            });
            
            $resolvedFeedbacks = array_filter($allFeedbacks, function($f) {
                return $f['status'] === 'resolved';
            });
        }
        
        $data = [
            'title' => 'Feedback Monitoring - Elite Cricket Academy',
            'allFeedbacks' => $allFeedbacks,
            'pendingFeedbacks' => array_values($pendingFeedbacks),
            'inProgressFeedbacks' => array_values($inProgressFeedbacks),
            'resolvedFeedbacks' => array_values($resolvedFeedbacks),
            'feedbackStats' => [
                'total' => count($allFeedbacks),
                'pending' => count($pendingFeedbacks),
                'inProgress' => count($inProgressFeedbacks),
                'resolved' => count($resolvedFeedbacks),
                'highPriority' => count(array_filter($allFeedbacks, function($f) {
                    return isset($f['priority']) && $f['priority'] === 'high';
                })),
                'todayCount' => count(array_filter($allFeedbacks, function($f) {
                    return isset($f['created_at']) && date('Y-m-d', strtotime($f['created_at'])) === date('Y-m-d');
                })),
                'avgResponseTime' => '4.2 hours',
                'satisfactionRate' => 92
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

    // Reports
    public function reports() {
        $data = [
            'title' => 'Reports - Elite Cricket Academy'
        ];
        
        $this->view('admin/reports', $data);
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
            
            // Update basic user info
            $userData = [
                'user_id' => $userId,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'address' => trim($_POST['address'] ?? ''),
                'date_of_birth' => $_POST['dateOfBirth'] ?? null
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
}
?>