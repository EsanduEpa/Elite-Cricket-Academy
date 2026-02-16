<?php
class M_NutritionPlan {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get all nutrition plans created by a trainer
    public function getNutritionPlansByTrainer($trainerId) {
        $this->db->query('SELECT 
            np.*,
            u.name as player_name,
            u.email as player_email
            FROM NutritionPlan np 
            LEFT JOIN User u ON np.PlayerID = u.UserID 
            WHERE np.TrainerID = :trainer_id 
            ORDER BY np.CreatedDate DESC');
        
        $this->db->bind(':trainer_id', $trainerId);
        
        return $this->db->resultSet();
    }
    
    // Add new nutrition plan
    public function addNutritionPlan($data) {
        $this->db->query('INSERT INTO NutritionPlan (
            TrainerID, 
            PlayerID, 
            DietDetails, 
            Duration, 
            CreatedDate, 
            Status
        ) VALUES (
            :trainer_id, 
            :player_id, 
            :diet_details, 
            :duration, 
            CURDATE(), 
            :status
        )');
        
        // Bind values
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':diet_details', $data['diet_details']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Update nutrition plan
    public function updateNutritionPlan($planId, $data) {
        $this->db->query('UPDATE NutritionPlan SET 
            PlayerID = :player_id,
            DietDetails = :diet_details,
            Duration = :duration,
            Status = :status
            WHERE PlanID = :plan_id');
        
        // Bind values
        $this->db->bind(':plan_id', $planId);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':diet_details', $data['diet_details']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Delete nutrition plan
    public function deleteNutritionPlan($planId) {
        $this->db->query('DELETE FROM NutritionPlan WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->execute();
    }
    
    // Get single nutrition plan
    public function getNutritionPlan($planId) {
        $this->db->query('SELECT 
            np.*,
            u.name as player_name,
            u.email as player_email
            FROM NutritionPlan np 
            LEFT JOIN User u ON np.PlayerID = u.UserID 
            WHERE np.PlanID = :plan_id');
        
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->single();
    }
    
    // Get all active players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT UserID, name, email FROM User WHERE Role = "Player" AND Status = "active" ORDER BY name');
        
        return $this->db->resultSet();
    }

    // Get nutrition plans assigned to a player
    public function getNutritionPlansByPlayer($playerId) {
        $this->db->query('SELECT np.*, u.Name AS trainer_name 
            FROM nutritionplan np 
            JOIN nutritionplan_player npp ON np.PlanID = npp.PlanID
            JOIN user u ON np.TrainerID = u.UserID 
            WHERE npp.PlayerID = :player_id 
            ORDER BY np.CreatedDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }
}
?>