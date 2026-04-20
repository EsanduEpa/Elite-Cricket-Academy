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
            CONCAT(u.FirstName, \' \', u.LastName) as reported_by_name,
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
            ReportedBy,
            Dr_reference

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
            :reported_by,
            :dr_reference
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
        $this->db->bind(':dr_reference', $data['dr_reference']);
        
        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Update medical record (recovery status only — for verified records)
    public function updateMedicalRecord($recordId, $data) {
        $this->db->query('UPDATE PlayerMedicalRecord SET
            RecoveryStatus = :recovery_status,
            ReportedDate = :reported_date
            WHERE RecordID = :record_id');

        $this->db->bind(':record_id', $recordId);
        $this->db->bind(':recovery_status', $data['recovery_status']);
        $this->db->bind(':reported_date', $data['reported_date']);
        
        return $this->db->execute();
    }

    // Full update of medical record (all editable fields — for pending records)
    public function fullUpdateMedicalRecord($recordId, $data) {
        $this->db->query('UPDATE PlayerMedicalRecord SET
            bodyarea           = :body_area,
            Diagnosis          = :diagnosis,
            TreatmentGiven     = :treatment_given,
            RecoveryStatus     = :recovery_status,
            InjuryDate         = :injury_date,
            HappenedAtAcademy  = :happened_at_academy,
            RestDaysNeeded     = :rest_days_needed,
            ReportedDate       = :reported_date,
            Dr_reference       = :dr_reference
            WHERE RecordID = :record_id');

        $this->db->bind(':record_id',           $recordId);
        $this->db->bind(':body_area',           $data['body_area']);
        $this->db->bind(':diagnosis',           $data['diagnosis']);
        $this->db->bind(':treatment_given',     $data['treatment_given']);
        $this->db->bind(':recovery_status',     $data['recovery_status']);
        $this->db->bind(':injury_date',         $data['injury_date']);
        $this->db->bind(':happened_at_academy', $data['happened_at_academy']);
        $this->db->bind(':rest_days_needed',    $data['rest_days_needed']);
        $this->db->bind(':reported_date',       $data['reported_date']);
        $this->db->bind(':dr_reference',       $data['dr_reference']);

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
            CONCAT(u.FirstName, \' \', u.LastName) as reported_by_name,
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
            CONCAT(u.FirstName, \' \', u.LastName) as reported_by_name,
            u.role as reported_by_role,
            CONCAT(p.FirstName, \' \', p.LastName) as player_name,
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
            CONCAT(u.FirstName, \' \', u.LastName) as PlayerName,
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

        // DB enum is ('pending','verified','rejected') — write 'verified' directly.
        $verifyStatus = strtolower(trim((string)$verifyStatus));
        // Accept 'approved' as alias in case old callers still send it.
        if ($verifyStatus === 'approved') {
            $verifyStatus = 'verified';
        }

        // Only allow writing actual DB enum values to avoid MySQL coercing to ''.
        if (!in_array($verifyStatus, ['pending', 'verified', 'rejected'], true)) {
            error_log("ERROR: Invalid verifyStatus value for DB: $verifyStatus");
            return false;
        }
 
        // First check if record exists (avoid depending on verify-status column name)
        $this->db->query('SELECT RecordID FROM PlayerMedicalRecord WHERE RecordID = :record_id');
        $this->db->bind(':record_id', $recordId, PDO::PARAM_INT);
        $existing = $this->db->single();

        if (!$existing) {
            error_log("ERROR: Record ID $recordId not found in database");
            return false;
        }

        // Some DB versions use verifyStatus, others use VerifiedStatus.
        $statusColumn = $this->resolveMedicalVerifyStatusColumn();
        if ($statusColumn === null) {
            // Default to the original column name; we'll try a fallback if it fails.
            $statusColumn = 'verifyStatus';
        }

        $result = $this->executeVerifyStatusUpdate($statusColumn, $recordId, $verifyStatus);

        // If update failed and we might be on the other column variant, try the alternative.
        if (!$result) {
            $fallbackColumn = ($statusColumn === 'verifyStatus') ? 'VerifiedStatus' : 'verifyStatus';
            $result = $this->executeVerifyStatusUpdate($fallbackColumn, $recordId, $verifyStatus);
        }

        return $result;
    }

    private function resolveMedicalVerifyStatusColumn() {
        try {
            $this->db->query("SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND LOWER(TABLE_NAME) = 'playermedicalrecord'
                  AND COLUMN_NAME IN ('verifyStatus', 'VerifiedStatus')");

            $rows = $this->db->resultSet();
            $columns = [];
            foreach ($rows as $row) {
                if (isset($row->COLUMN_NAME)) {
                    $columns[] = (string)$row->COLUMN_NAME;
                }
            }

            if (in_array('verifyStatus', $columns, true)) {
                return 'verifyStatus';
            }
            if (in_array('VerifiedStatus', $columns, true)) {
                return 'VerifiedStatus';
            }
        } catch (Throwable $e) {
            // Ignore schema introspection issues; caller will fallback.
        }

        return null;
    }

    private function executeVerifyStatusUpdate($statusColumn, $recordId, $verifyStatus) {
        error_log("Updating PlayerMedicalRecord.$statusColumn for RecordID=$recordId");

        $this->db->query("UPDATE PlayerMedicalRecord SET $statusColumn = :verify_status WHERE RecordID = :record_id");
        $this->db->bind(':record_id', $recordId, PDO::PARAM_INT);
        $this->db->bind(':verify_status', $verifyStatus, PDO::PARAM_STR);

        $result = $this->db->execute();
        error_log("Execute result ($statusColumn): " . ($result ? 'SUCCESS' : 'FAILED'));
        error_log("Rows affected ($statusColumn): " . $this->db->rowCount());

        return $result;
    }
}
?>