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
}
?>
?>