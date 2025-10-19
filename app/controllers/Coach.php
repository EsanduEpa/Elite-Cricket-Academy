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
}
?>
