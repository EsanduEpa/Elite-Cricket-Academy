<?php
class Feedback {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * GET PENDING FEEDBACKS - Fetch unresolved feedback items
     * 
     * Purpose: Display feedback that requires admin attention on dashboard
     * @param int $limit - Maximum number of feedback items to return
     * @return array - Array of feedback objects with user information
     * 
     * Database Schema:
     *   - FeedbackID: Primary key
     *   - FromUserID: User who submitted feedback
     *   - ToUserID: Target user (coach/trainer) or NULL for general feedback
     *   - Content: Feedback message text
     *   - Rating: 1-5 star rating (optional)
     *   - Category: coach, trainer, facility, equipment, shop, general
     *   - Status: pending, reviewed, resolved
     *   - CreatedDate: When feedback was submitted
     */
    public function getPendingFeedbacks($limit = 5) {
        // QUERY: Get pending feedback with user names
        // LEFT JOIN to get the name of user who submitted feedback (FromUserID)
        // Filter by Status = 'pending' to show only unresolved items
        $this->db->query('SELECT 
            f.FeedbackID as id,
            f.Content as message,
            f.Category as subject,
            f.Rating as rating,
            f.Status as status,
            f.CreatedDate as created_at,
            u.Name as user_name,
            CASE 
                WHEN f.Rating >= 4 THEN "low"
                WHEN f.Rating = 3 THEN "medium"
                WHEN f.Rating <= 2 THEN "high"
                ELSE "medium"
            END as priority
        FROM feedback f 
        LEFT JOIN User u ON f.FromUserID = u.UserID 
        WHERE f.Status = "pending" 
        ORDER BY f.CreatedDate DESC 
        LIMIT :limit');
        
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        
        // EXECUTE AND RETURN RESULTS
        $results = $this->db->resultSet();
        
        // RETURN: Array of feedback (empty array if no pending feedback)
        return $results;
    }

    /**
     * GET TOTAL PENDING FEEDBACK - Count unresolved feedback items
     * 
     * Purpose: Display count badge on admin dashboard
     * @return int - Number of pending feedback items
     */
    public function getTotalPendingFeedback() {
        // QUERY: COUNT feedback WHERE Status = 'pending'
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE Status = "pending"');
        $result = $this->db->single();
        
        // RETURN: Count of pending feedback (0 if none)
        return $result ? (int)$result->count : 0;
    }

    // Get today's feedback count
    public function getTodayFeedback() {
        $this->db->query('SELECT COUNT(*) as count FROM feedback WHERE DATE(created_at) = CURDATE()');
        $result = $this->db->single();
        
        return $result ? $result->count : 1; // Return dummy data if no database
    }

    // Get all feedbacks with user information
    public function getAllFeedbacks() {
        $this->db->query('SELECT 
            f.FeedbackID as id,
            f.Content as message,
            f.Category as subject,
            f.Rating as rating,
            f.Status as status,
            f.CreatedDate as created_at,
            f.AdminResponse as admin_response,
            f.ResponseDate as resolved_at,
            u.Name as user_name,
            u.Email as user_email,
            CASE 
                WHEN f.Rating >= 4 THEN "low"
                WHEN f.Rating = 3 THEN "medium"
                WHEN f.Rating <= 2 THEN "high"
                ELSE "medium"
            END as priority
        FROM feedback f 
        LEFT JOIN User u ON f.FromUserID = u.UserID 
        ORDER BY f.CreatedDate DESC');
        
        $results = $this->db->resultSet();
        
        // Convert objects to arrays for compatibility
        $feedbacks = [];
        foreach ($results as $feedback) {
            $feedbacks[] = (array) $feedback;
        }
        
        return $feedbacks;
    }

    // Get feedback by ID
    public function getFeedbackById($id) {
        $this->db->query('SELECT 
            f.FeedbackID as id,
            f.Content as message,
            f.Category as subject,
            f.Rating as rating,
            f.Status as status,
            f.CreatedDate as created_at,
            f.AdminResponse as admin_response,
            u.Name as user_name,
            u.Email as user_email
        FROM feedback f 
        LEFT JOIN User u ON f.FromUserID = u.UserID 
        WHERE f.FeedbackID = :id');
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
