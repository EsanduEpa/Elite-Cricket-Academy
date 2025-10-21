<?php
class M_WorkoutPlan {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get all workout plans created by a trainer
    public function getWorkoutPlansByTrainer($trainerId) {
        $this->db->query('SELECT 
            wp.*,
            u.name as player_name,
            u.email as player_email
            FROM WorkoutPlan wp 
            LEFT JOIN User u ON wp.PlayerID = u.UserID 
            WHERE wp.TrainerID = :trainer_id 
            ORDER BY wp.CreatedDate DESC');
        
        $this->db->bind(':trainer_id', $trainerId);
        
        return $this->db->resultSet();
    }
    
    // Add new workout plan
    public function addWorkoutPlan($data) {
        $this->db->query('INSERT INTO WorkoutPlan (
            TrainerID, 
            PlayerID, 
            VideoUrl, 
            WorkoutDetails, 
            Frequency, 
            Duration, 
            CreatedDate, 
            Status
        ) VALUES (
            :trainer_id, 
            :player_id, 
            :video_url, 
            :workout_details, 
            :frequency, 
            :duration, 
            CURDATE(), 
            :status
        )');
        
        // Bind values
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':video_url', $data['video_url']);
        $this->db->bind(':workout_details', $data['workout_details']);
        $this->db->bind(':frequency', $data['frequency']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Update workout plan
    public function updateWorkoutPlan($planId, $data) {
        $this->db->query('UPDATE WorkoutPlan SET 
            PlayerID = :player_id,
            VideoUrl = :video_url,
            WorkoutDetails = :workout_details,
            Frequency = :frequency,
            Duration = :duration,
            Status = :status
            WHERE PlanID = :plan_id');
        
        // Bind values
        $this->db->bind(':plan_id', $planId);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':video_url', $data['video_url']);
        $this->db->bind(':workout_details', $data['workout_details']);
        $this->db->bind(':frequency', $data['frequency']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Delete workout plan
    public function deleteWorkoutPlan($planId) {
        $this->db->query('DELETE FROM WorkoutPlan WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->execute();
    }
    
    // Get single workout plan
    public function getWorkoutPlan($planId) {
        $this->db->query('SELECT 
            wp.*,
            u.name as player_name,
            u.email as player_email
            FROM WorkoutPlan wp 
            LEFT JOIN User u ON wp.PlayerID = u.UserID 
            WHERE wp.PlanID = :plan_id');
        
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->single();
    }
    
    // Get all active players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT UserID, name, email FROM User WHERE Role = "Player" AND Status = "active" ORDER BY name');
        
        return $this->db->resultSet();
    }
}
?>