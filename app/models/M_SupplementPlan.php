<?php
class M_SupplementPlan {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get all supplement plans created by a trainer
    public function getSupplementPlansByTrainer($trainerId) {
        $this->db->query('SELECT 
            sp.*,
            u.name as player_name,
            u.email as player_email
            FROM SupplementPlan sp 
            LEFT JOIN User u ON sp.PlayerID = u.UserID 
            WHERE sp.TrainerID = :trainer_id 
            ORDER BY sp.CreatedDate DESC');
        
        $this->db->bind(':trainer_id', $trainerId);
        
        return $this->db->resultSet();
    }
    
    // Add new supplement plan
    public function addSupplementPlan($data) {
        $this->db->query('INSERT INTO SupplementPlan (
            TrainerID, 
            PlayerID, 
            SupplementDetails, 
            Dosage, 
            Duration, 
            CreatedDate, 
            Status
        ) VALUES (
            :trainer_id, 
            :player_id, 
            :supplement_details, 
            :dosage, 
            :duration, 
            CURDATE(), 
            :status
        )');
        
        // Bind values
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':supplement_details', $data['supplement_details']);
        $this->db->bind(':dosage', $data['dosage']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Update supplement plan
    public function updateSupplementPlan($planId, $data) {
        $this->db->query('UPDATE SupplementPlan SET 
            PlayerID = :player_id,
            SupplementDetails = :supplement_details,
            Dosage = :dosage,
            Duration = :duration,
            Status = :status
            WHERE PlanID = :plan_id');
        
        // Bind values
        $this->db->bind(':plan_id', $planId);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':supplement_details', $data['supplement_details']);
        $this->db->bind(':dosage', $data['dosage']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Delete supplement plan
    public function deleteSupplementPlan($planId) {
        $this->db->query('DELETE FROM SupplementPlan WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->execute();
    }
    
    // Get single supplement plan
    public function getSupplementPlan($planId) {
        $this->db->query('SELECT 
            sp.*,
            u.name as player_name,
            u.email as player_email
            FROM SupplementPlan sp 
            LEFT JOIN User u ON sp.PlayerID = u.UserID 
            WHERE sp.PlanID = :plan_id');
        
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->single();
    }
    
    // Get all active players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT UserID, name, email FROM User WHERE Role = "Player" AND Status = "active" ORDER BY name');
        
        return $this->db->resultSet();
    }

    // Get supplement plans assigned to a player
    public function getSupplementPlansByPlayer($playerId) {
        $this->db->query('SELECT sp.*, u.Name AS trainer_name 
            FROM supplementplan sp 
            JOIN supplement_player spp ON sp.PlanID = spp.PlanID
            JOIN user u ON sp.TrainerID = u.UserID 
            WHERE spp.PlayerID = :player_id 
            ORDER BY sp.CreatedDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }
}
?>