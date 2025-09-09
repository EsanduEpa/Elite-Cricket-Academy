<?php
/**
 * Booking Controller
 * Handles booking management for the Cricket Academy
 */

class Bookings extends Controller {
    
    public function __construct() {
        // Initialize any required models
        // $this->bookingModel = $this->model('M_Bookings');
        // $this->userModel = $this->model('M_Users');
    }
    
    /**
     * Player booking page
     */
    public function index() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('login');
        }
        
        // Check if user is a player
        if ($_SESSION['user_role'] !== 'player') {
            redirect('login');
        }
        
        // Sample data for development
        $data = [
            'title' => 'My Bookings',
            'player' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'membership_level' => 'Premium'
            ],
            'upcomingBookings' => [
                [
                    'id' => 1,
                    'type' => 'Physio Session',
                    'date' => date('Y-m-d', strtotime('+1 day')),
                    'time' => '2:00 PM - 3:00 PM',
                    'practitioner' => 'Dr. Sarah Wilson',
                    'reason' => 'Injury Recovery',
                    'status' => 'confirmed'
                ],
                [
                    'id' => 2,
                    'type' => 'Fitness Assessment',
                    'date' => date('Y-m-d', strtotime('+3 days')),
                    'time' => '10:00 AM - 11:30 AM',
                    'practitioner' => 'Coach Mike Johnson',
                    'reason' => 'Monthly Check',
                    'status' => 'confirmed'
                ]
            ]
        ];
        
        $this->view('player/bookings', $data);
    }
    
    /**
     * Create new booking (AJAX)
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate input
            $data = [
                'service' => trim($_POST['service']),
                'date' => trim($_POST['date']),
                'time' => trim($_POST['time']),
                'reason' => trim($_POST['reason']),
                'notes' => trim($_POST['notes']),
                'urgency' => trim($_POST['urgency'])
            ];
            
            // Basic validation
            if (empty($data['service']) || empty($data['date']) || empty($data['time']) || empty($data['reason'])) {
                echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
                return;
            }
            
            // Here you would save to database
            // $result = $this->bookingModel->createBooking($data);
            
            // For now, simulate success
            echo json_encode([
                'success' => true,
                'message' => 'Booking created successfully! You will receive a confirmation email shortly.',
                'booking_id' => rand(1000, 9999)
            ]);
        }
    }
    
    /**
     * Cancel booking
     */
    public function cancel($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate booking ownership and timing (24-hour rule)
            // $booking = $this->bookingModel->getBookingById($id);
            
            // For now, simulate success
            echo json_encode([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);
        }
    }
    
    /**
     * Reschedule booking
     */
    public function reschedule($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'booking_id' => $id,
                'new_date' => trim($_POST['date']),
                'new_time' => trim($_POST['time'])
            ];
            
            // Here you would update the booking in database
            // $result = $this->bookingModel->rescheduleBooking($data);
            
            echo json_encode([
                'success' => true,
                'message' => 'Booking rescheduled successfully'
            ]);
        }
    }
    
    /**
     * Get available time slots for a date
     */
    public function getAvailableSlots() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $date = trim($_POST['date']);
            $serviceType = trim($_POST['service_type']);
            
            // Sample available slots
            $availableSlots = [
                '9:00 AM',
                '10:30 AM', 
                '2:00 PM',
                '3:30 PM',
                '4:00 PM'
            ];
            
            // Here you would query database for actual availability
            // $slots = $this->bookingModel->getAvailableSlots($date, $serviceType);
            
            echo json_encode([
                'success' => true,
                'slots' => $availableSlots
            ]);
        }
    }
    
    /**
     * Trainer/Admin booking management
     */
    public function manage() {
        // Check if user is trainer/admin
        if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['trainer', 'admin'])) {
            redirect('login');
        }
        
        // This would typically be part of the trainer dashboard
        // Redirect to trainer dashboard bookings section
        redirect('trainer#bookings');
    }
    
    /**
     * Approve booking (Trainer/Admin only)
     */
    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check permissions
            if (!in_array($_SESSION['user_role'], ['trainer', 'admin'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            
            // Here you would update booking status
            // $result = $this->bookingModel->approveBooking($id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Booking approved successfully'
            ]);
        }
    }
    
    /**
     * Reject booking (Trainer/Admin only)
     */
    public function reject($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check permissions
            if (!in_array($_SESSION['user_role'], ['trainer', 'admin'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            
            $reason = trim($_POST['reason'] ?? '');
            
            // Here you would update booking status and send notification
            // $result = $this->bookingModel->rejectBooking($id, $reason);
            
            echo json_encode([
                'success' => true,
                'message' => 'Booking rejected'
            ]);
        }
    }
    
    /**
     * Complete session (Trainer only)
     */
    public function complete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check permissions
            if ($_SESSION['user_role'] !== 'trainer') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            
            $data = [
                'booking_id' => $id,
                'session_summary' => trim($_POST['summary']),
                'findings' => trim($_POST['findings']),
                'recommendations' => trim($_POST['recommendations']),
                'followup' => trim($_POST['followup']),
                'rating' => trim($_POST['rating']),
                'notify_player' => isset($_POST['notify_player'])
            ];
            
            // Here you would save session notes and update status
            // $result = $this->bookingModel->completeSession($data);
            
            echo json_encode([
                'success' => true,
                'message' => 'Session completed and notes saved successfully'
            ]);
        }
    }
    
    /**
     * Add available time slot (Trainer/Admin only)
     */
    public function addSlot() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check permissions
            if (!in_array($_SESSION['user_role'], ['trainer', 'admin'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            
            $data = [
                'date' => trim($_POST['date']),
                'start_time' => trim($_POST['startTime']),
                'end_time' => trim($_POST['endTime']),
                'service_type' => trim($_POST['type']),
                'practitioner' => trim($_POST['practitioner']),
                'location' => trim($_POST['location']),
                'notes' => trim($_POST['notes']),
                'recurring' => isset($_POST['recurring'])
            ];
            
            // Validate required fields
            if (empty($data['date']) || empty($data['start_time']) || empty($data['end_time'])) {
                echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
                return;
            }
            
            // Here you would save the time slot to database
            // $result = $this->bookingModel->addTimeSlot($data);
            
            echo json_encode([
                'success' => true,
                'message' => 'Time slot added successfully'
            ]);
        }
    }
}
?>
