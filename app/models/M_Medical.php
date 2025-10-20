<?php
class M_Medical {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get all medical records for a player
    public function getMedicalRecords($playerId) {
        $this->db->query('SELECT 
            pmr.*,
            u.name as reported_by_name,
            u.role as reported_by_role
            FROM PlayerMedicalRecord pmr 
            LEFT JOIN User u ON pmr.ReportedBy = u.UserID 
            WHERE pmr.PlayerID = :player_id 
            ORDER BY pmr.ReportedDate DESC');
        
        $this->db->bind(':player_id', $playerId);
        
        return $this->db->resultSet();
    }
    
    // Add new medical record
    public function addMedicalRecord($data) {
        $this->db->query('INSERT INTO PlayerMedicalRecord (
            PlayerID, 
            InjuryDetails, 
            Diagnosis, 
            TreatmentGiven, 
            RecoveryStatus, 
            ReportedDate, 
            ReportedBy
        ) VALUES (
            :player_id, 
            :injury_details, 
            :diagnosis, 
            :treatment_given, 
            :recovery_status, 
            :reported_date, 
            :reported_by
        )');
        
        // Bind values
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':injury_details', $data['injury_details']);
        $this->db->bind(':diagnosis', $data['diagnosis']);
        $this->db->bind(':treatment_given', $data['treatment_given']);
        $this->db->bind(':recovery_status', $data['recovery_status']);
        $this->db->bind(':reported_date', $data['reported_date']);
        $this->db->bind(':reported_by', $data['reported_by']);
        
        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Update medical record
    public function updateMedicalRecord($recordId, $data) {
        $this->db->query('UPDATE PlayerMedicalRecord SET 
            InjuryDetails = :injury_details,
            Diagnosis = :diagnosis,
            TreatmentGiven = :treatment_given,
            RecoveryStatus = :recovery_status,
            ReportedDate = :reported_date
            WHERE RecordID = :record_id');
        
        // Bind values
        $this->db->bind(':record_id', $recordId);
        $this->db->bind(':injury_details', $data['injury_details']);
        $this->db->bind(':diagnosis', $data['diagnosis']);
        $this->db->bind(':treatment_given', $data['treatment_given']);
        $this->db->bind(':recovery_status', $data['recovery_status']);
        $this->db->bind(':reported_date', $data['reported_date']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Delete medical record
    public function deleteMedicalRecord($recordId) {
        $this->db->query('DELETE FROM PlayerMedicalRecord WHERE RecordID = :record_id');
        $this->db->bind(':record_id', $recordId);
        
        return $this->db->execute();
    }
    
    // Get single medical record
    public function getMedicalRecord($recordId) {
        $this->db->query('SELECT 
            pmr.*,
            u.name as reported_by_name,
            u.role as reported_by_role
            FROM PlayerMedicalRecord pmr 
            LEFT JOIN User u ON pmr.ReportedBy = u.UserID 
            WHERE pmr.RecordID = :record_id');
        
        $this->db->bind(':record_id', $recordId);
        
        return $this->db->single();
    }
}
?>