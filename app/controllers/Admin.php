<?php
class Admin extends Controller {
    public function __construct() {
        // Simple constructor for interface-only dashboard
    }
    
    public function index() {
        // Redirect to dashboard by default
        redirect('admin/dashboard');
    }
    
    public function dashboard() {
        // Hardcoded sample data for interface demonstration
        $data = [
            'title' => 'Academy Management Dashboard - Elite Cricket Academy',
            'totalUsers' => 47,
            'totalCoaches' => 8,
            'totalPlayers' => 32,
            'totalTrainers' => 5,
            'totalStaff' => 2,
            'upcomingEvents' => [
                [
                    'id' => 1,
                    'title' => 'Junior Cricket Championship',
                    'event_date' => '2025-09-15',
                    'event_type' => 'tournament',
                    'description' => 'Annual junior cricket championship for under-16 players',
                    'location' => 'Main Cricket Ground'
                ],
                [
                    'id' => 2,
                    'title' => 'Advanced Batting Workshop',
                    'event_date' => '2025-09-12',
                    'event_type' => 'training',
                    'description' => 'Specialized batting techniques workshop by senior coach',
                    'location' => 'Practice Nets Area'
                ],
                [
                    'id' => 3,
                    'title' => 'Inter-Academy Friendly Match',
                    'event_date' => '2025-09-18',
                    'event_type' => 'match',
                    'description' => 'Friendly match against City Sports Academy',
                    'location' => 'Stadium Ground'
                ],
                [
                    'id' => 4,
                    'title' => 'Fielding Skills Training',
                    'event_date' => '2025-09-20',
                    'event_type' => 'training',
                    'description' => 'Intensive fielding and wicket-keeping session',
                    'location' => 'Training Field B'
                ]
            ],
            'recentActivities' => [
                [
                    'id' => 1,
                    'action' => 'New player registered',
                    'user_name' => 'Sarah Thompson',
                    'timestamp' => '2 hours ago',
                    'type' => 'registration',
                    'details' => 'Junior player joined the academy program'
                ],
                [
                    'id' => 2,
                    'action' => 'Training session completed',
                    'user_name' => 'Coach Michael Smith',
                    'timestamp' => '4 hours ago',
                    'type' => 'training',
                    'details' => 'Bowling technique workshop for senior players'
                ],
                [
                    'id' => 3,
                    'action' => 'Event scheduled',
                    'user_name' => 'Admin User',
                    'timestamp' => '6 hours ago',
                    'type' => 'event',
                    'details' => 'Junior Cricket Championship added to calendar'
                ],
                [
                    'id' => 4,
                    'action' => 'Feedback submitted',
                    'user_name' => 'Emily Rodriguez',
                    'timestamp' => '1 day ago',
                    'type' => 'feedback',
                    'details' => 'Training facility improvement suggestions'
                ],
                [
                    'id' => 5,
                    'action' => 'Coach profile updated',
                    'user_name' => 'David Wilson',
                    'timestamp' => '1 day ago',
                    'type' => 'profile',
                    'details' => 'Coaching credentials and certifications updated'
                ],
                [
                    'id' => 6,
                    'action' => 'Equipment inventory updated',
                    'user_name' => 'Staff Manager',
                    'timestamp' => '2 days ago',
                    'type' => 'inventory',
                    'details' => 'New cricket gear added to inventory'
                ]
            ],
            'pendingFeedback' => [
                [
                    'id' => 1,
                    'subject' => 'Training Schedule Improvement',
                    'message' => 'Could we have more evening training slots for working parents?',
                    'user_name' => 'Jennifer Martinez',
                    'status' => 'pending',
                    'created_at' => '2025-09-01 14:30:00',
                    'priority' => 'medium'
                ],
                [
                    'id' => 2,
                    'subject' => 'Equipment Quality Concern',
                    'message' => 'Some of the batting helmets need replacement for safety.',
                    'user_name' => 'Robert Chen',
                    'status' => 'pending',
                    'created_at' => '2025-08-31 09:15:00',
                    'priority' => 'high'
                ],
                [
                    'id' => 3,
                    'subject' => 'Ground Maintenance',
                    'message' => 'The practice pitch needs better drainage after recent rains.',
                    'user_name' => 'Amanda Foster',
                    'status' => 'pending',
                    'created_at' => '2025-08-30 16:45:00',
                    'priority' => 'high'
                ],
                [
                    'id' => 4,
                    'subject' => 'Coaching Appreciation',
                    'message' => 'Excellent work by Coach Smith with the junior team!',
                    'user_name' => 'Mark Johnson',
                    'status' => 'pending',
                    'created_at' => '2025-08-29 11:20:00',
                    'priority' => 'low'
                ],
                [
                    'id' => 5,
                    'subject' => 'Tournament Preparation',
                    'message' => 'Need more practice matches before the championship.',
                    'user_name' => 'Lisa Williams',
                    'status' => 'pending',
                    'created_at' => '2025-08-28 13:10:00',
                    'priority' => 'medium'
                ]
            ],
            'totalPendingFeedback' => 5,
            'todayStats' => [
                'newRegistrations' => 3,
                'activeEvents' => 2,
                'feedbackReceived' => 1
            ]
        ];
        
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
        $data = [
            'title' => 'Staff Management - Elite Cricket Academy'
        ];
        
        $this->view('admin/staff', $data);
    }

    // Events and Tournaments Management
    public function events() {
        $eventModel = $this->model('Event');
        
        $data = [
            'title' => 'Events & Tournaments - Elite Cricket Academy',
            'upcomingEvents' => $eventModel->getUpcomingEvents(10),
            'pastEvents' => $eventModel->getPastEvents(10),
            'recentEvents' => $eventModel->getRecentEvents(5),
            'eventStats' => [
                'totalEvents' => 45,
                'upcomingCount' => 8,
                'pastCount' => 37,
                'tournaments' => 12,
                'trainingSessions' => 25,
                'matches' => 8
            ]
        ];
        
        $this->view('admin/events', $data);
    }

    public function create_event() {
        if ($_POST) {
            // Handle event creation
            $eventModel = $this->model('Event');
            $result = $eventModel->createEvent($_POST);
            
            if ($result) {
                flash('event_message', 'Event created successfully');
                redirect('admin/events');
            } else {
                flash('event_message', 'Something went wrong', 'alert alert-danger');
                redirect('admin/events');
            }
        } else {
            $data = [
                'title' => 'Create New Event - Elite Cricket Academy'
            ];
            $this->view('admin/create_event', $data);
        }
    }

    public function edit_event($id) {
        $eventModel = $this->model('Event');
        
        if ($_POST) {
            // Handle event update
            $result = $eventModel->updateEvent($id, $_POST);
            
            if ($result) {
                flash('event_message', 'Event updated successfully');
                redirect('admin/events');
            } else {
                flash('event_message', 'Something went wrong', 'alert alert-danger');
                redirect('admin/events');
            }
        } else {
            $data = [
                'title' => 'Edit Event - Elite Cricket Academy',
                'event' => $eventModel->getEventById($id)
            ];
            $this->view('admin/edit_event', $data);
        }
    }

    public function delete_event($id) {
        $eventModel = $this->model('Event');
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
        echo json_encode($event);
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
}
?>