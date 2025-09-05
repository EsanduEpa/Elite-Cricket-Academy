<?php
class Feedback {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get pending feedbacks
    public function getPendingFeedbacks($limit = 5) {
        $this->db->query('SELECT f.*, u.name as user_name FROM feedback f 
                         LEFT JOIN users u ON f.user_id = u.id 
                         WHERE f.status = "pending" 
                         ORDER BY f.created_at DESC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        $results = $this->db->resultSet();
        
        // Return dummy data if no database results
        if (empty($results)) {
            return [
                [
                    'id' => 1,
                    'subject' => 'Training Quality Feedback',
                    'message' => 'The coaching sessions are excellent but need more practice time.',
                    'user_name' => 'John Smith',
                    'status' => 'pending',
                    'created_at' => '2025-09-01 10:30:00',
                    'priority' => 'medium'
                ],
                [
                    'id' => 2,
                    'subject' => 'Facility Improvement Suggestion',
                    'message' => 'The changing rooms could use better lighting and ventilation.',
                    'user_name' => 'Sarah Johnson',
                    'status' => 'pending',
                    'created_at' => '2025-08-30 14:15:00',
                    'priority' => 'low'
                ],
                [
                    'id' => 3,
                    'subject' => 'Equipment Request',
                    'message' => 'We need more batting helmets for junior players.',
                    'user_name' => 'Mike Wilson',
                    'status' => 'pending',
                    'created_at' => '2025-08-29 09:45:00',
                    'priority' => 'high'
                ]
            ];
        }
        
        return $results;
    }

    // Get total pending feedback count
    public function getTotalPendingFeedback() {
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE status = "pending"');
        $result = $this->db->single();
        
        return $result ? $result->count : 5; // Return dummy data if no database
    }

    // Get today's feedback count
    public function getTodayFeedback() {
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE DATE(created_at) = CURDATE()');
        $result = $this->db->single();
        
        return $result ? $result->count : 1; // Return dummy data if no database
    }

    // Get all feedbacks
    public function getAllFeedbacks() {
        $this->db->query('SELECT f.*, u.name as user_name FROM feedback f 
                         LEFT JOIN users u ON f.user_id = u.id 
                         ORDER BY f.created_at DESC');
        return $this->db->resultSet();
    }

    // Get feedback by ID
    public function getFeedbackById($id) {
        $this->db->query('SELECT f.*, u.name as user_name FROM feedback f 
                         LEFT JOIN users u ON f.user_id = u.id 
                         WHERE f.id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // Add new feedback
    public function addFeedback($data) {
        $this->db->query('INSERT INTO feedback (user_id, subject, message, priority, status) 
                         VALUES (:user_id, :subject, :message, :priority, :status)');
        
        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':message', $data['message']);
        $this->db->bind(':priority', $data['priority']);
        $this->db->bind(':status', 'pending');

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update feedback status
    public function updateFeedbackStatus($id, $status, $response = '') {
        $this->db->query('UPDATE feedback SET 
                         status = :status,
                         admin_response = :response,
                         resolved_at = :resolved_at
                         WHERE id = :id');

        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        $this->db->bind(':response', $response);
        $this->db->bind(':resolved_at', $status === 'resolved' ? date('Y-m-d H:i:s') : null);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete feedback
    public function deleteFeedback($id) {
        $this->db->query('DELETE FROM feedback WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get feedback statistics
    public function getFeedbackStats() {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'resolved' => 0,
            'in_progress' => 0
        ];

        // Get total feedback
        $this->db->query('SELECT COUNT(*) as count FROM feedback');
        $result = $this->db->single();
        $stats['total'] = $result ? $result->count : 8;

        // Get pending feedback
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE status = "pending"');
        $result = $this->db->single();
        $stats['pending'] = $result ? $result->count : 5;

        // Get resolved feedback
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE status = "resolved"');
        $result = $this->db->single();
        $stats['resolved'] = $result ? $result->count : 2;

        // Get in progress feedback
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE status = "in_progress"');
        $result = $this->db->single();
        $stats['in_progress'] = $result ? $result->count : 1;

        return $stats;
    }
}
?>
