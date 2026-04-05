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
            ORDER BY pmr.InjuryDate DESC, pmr.ReportedDate DESC');
        
        $this->db->bind(':player_id', $playerId);
        
        return $this->db->resultSet();
    }
    
    // Add new medical record
    public function addMedicalRecord($data) {
        $this->db->query('INSERT INTO PlayerMedicalRecord (
            PlayerID,
            bodyarea,
            Diagnosis,
            TreatmentGiven,
            RecoveryStatus,
            InjuryDate,
            HappenedAtAcademy,
            RestDaysNeeded,
            DiagnosisReceiptURL,
            ReportedDate,
            ReportedBy
        ) VALUES (
            :player_id,
            :body_area,
            :diagnosis,
            :treatment_given,
            :recovery_status,
            :injury_date,
            :happened_at_academy,
            :rest_days_needed,
            :diagnosis_receipt_url,
            :reported_date,
            :reported_by
        )');

        // Bind values
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':body_area', $data['body_area']);
        $this->db->bind(':diagnosis', $data['diagnosis']);
        $this->db->bind(':treatment_given', $data['treatment_given']);
        $this->db->bind(':recovery_status', $data['recovery_status']);
        $this->db->bind(':injury_date', $data['injury_date']);
        $this->db->bind(':happened_at_academy', $data['happened_at_academy']);
        $this->db->bind(':rest_days_needed', $data['rest_days_needed']);
        $this->db->bind(':diagnosis_receipt_url', $data['diagnosis_receipt_url']);
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
            RecoveryStatus = :recovery_status,
            ReportedDate = :reported_date
            WHERE RecordID = :record_id');

        // Bind values
        $this->db->bind(':record_id', $recordId);
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
    
    // Get all medical records for trainers (all players)
    public function getAllMedicalRecords() {
        $this->db->query('SELECT 
            pmr.*,
            u.name as reported_by_name,
            u.role as reported_by_role,
            p.name as player_name,
            p.UserID as player_user_id
            FROM PlayerMedicalRecord pmr 
            LEFT JOIN User u ON pmr.ReportedBy = u.UserID 
            LEFT JOIN User p ON pmr.PlayerID = p.UserID
            ORDER BY pmr.InjuryDate DESC, pmr.ReportedDate DESC');
        
        return $this->db->resultSet();
    }
    
    // Get all medical records with player information for coach view
    public function getAllMedicalRecordsWithPlayerInfo() {
        $this->db->query('SELECT 
            pmr.*,
            u.Name as PlayerName,
            u.PhoneNumber as PlayerContact,
            u.Email as PlayerEmail
            FROM PlayerMedicalRecord pmr 
            LEFT JOIN user u ON pmr.PlayerID = u.UserID
            WHERE u.Role = "Player"
            ORDER BY pmr.InjuryDate DESC, pmr.ReportedDate DESC');
        
        return $this->db->resultSet();
    }
    
    // Update verification status for a medical record
    public function updateVerifyStatus($recordId, $verifyStatus, $comments = '') {
        error_log("=== M_Medical::updateVerifyStatus called ===");
        error_log("Record ID: $recordId, Status: $verifyStatus, Comments: $comments");
        
        // First check if record exists
        $this->db->query('SELECT RecordID, verifyStatus FROM PlayerMedicalRecord WHERE RecordID = :record_id');
        $this->db->bind(':record_id', $recordId, PDO::PARAM_INT);
        $existing = $this->db->single();
        
        if (!$existing) {
            error_log("ERROR: Record ID $recordId not found in database");
            return false;
        }
        
        error_log("Record found. Current status: " . $existing->verifyStatus);
        
        // Now update the record
        $this->db->query('UPDATE PlayerMedicalRecord SET 
            verifyStatus = :verify_status
            WHERE RecordID = :record_id');
        
        // Bind values
        $this->db->bind(':record_id', $recordId, PDO::PARAM_INT);
        $this->db->bind(':verify_status', $verifyStatus, PDO::PARAM_STR);
        
        error_log("SQL query prepared and parameters bound");
        
        // Execute
        $result = $this->db->execute();
        error_log("Execute result: " . ($result ? 'SUCCESS' : 'FAILED'));
        
        // Check how many rows were affected
        $rowCount = $this->db->rowCount();
        error_log("Rows affected: $rowCount");
        
        return $result;
    }
}
?>