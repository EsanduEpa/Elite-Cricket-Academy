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
}
?>