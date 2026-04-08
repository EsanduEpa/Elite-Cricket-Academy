<?php
class M_SlotAdmin {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // =========================================================
    // TIME BANDS
    // =========================================================

    public function getTimeBands(): array {
        $this->db->query('SELECT * FROM slot_time_band ORDER BY SlotID');
        return $this->db->resultSet();
    }

    public function toggleTimeBand(int $id): bool {
        $this->db->query('UPDATE slot_time_band SET IsActive = IF(IsActive=1,0,1) WHERE SlotID = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // =========================================================
    // TEMPLATES
    // =========================================================

    public function getTemplates(): array {
        $this->db->query(
            'SELECT t.*, tb.SlotLabel, f.Name AS FacilityName
             FROM slot_template t
             LEFT JOIN slot_time_band tb ON tb.SlotID = t.SlotID
             LEFT JOIN facility f ON f.FacilityID = t.FacilityID
             ORDER BY t.TemplateID DESC'
        );
        return $this->db->resultSet();
    }

    public function getTemplateById(int $id): ?object {
        $this->db->query(
            'SELECT t.*, tb.SlotLabel, f.Name AS FacilityName
             FROM slot_template t
             LEFT JOIN slot_time_band tb ON tb.SlotID = t.SlotID
             LEFT JOIN facility f ON f.FacilityID = t.FacilityID
             WHERE t.TemplateID = :id'
        );
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $row = $this->db->single();
        return $row ?: null;
    }

    public function createTemplate(array $d): int {
        $this->db->query(
            'INSERT INTO slot_template
             (TemplateName, SlotType, StaffType, SlotID, DayOfWeek, FacilityID,
              AgeGroup, Category, Description, MaxParticipants, PricePerSession,
              RequiredPlanFeature, RecurrenceStart, RecurrenceEnd, IsActive, CreatedBy)
             VALUES
             (:name, :stype, :stafftype, :slotid, :dow, :fid,
              :age, :cat, :desc, :max, :price,
              :rpf, :rstart, :rend, 1, :createdby)'
        );
        $this->db->bind(':name',      $d['TemplateName']);
        $this->db->bind(':stype',     $d['SlotType']);
        $this->db->bind(':stafftype', $d['StaffType']);
        $this->db->bind(':slotid',    (int)$d['SlotID'], PDO::PARAM_INT);
        $this->db->bind(':dow',       isset($d['DayOfWeek']) && $d['DayOfWeek'] !== '' ? (int)$d['DayOfWeek'] : null);
        $this->db->bind(':fid',       isset($d['FacilityID']) && $d['FacilityID'] !== '' ? (int)$d['FacilityID'] : null);
        $this->db->bind(':age',       $d['AgeGroup'] ?? null);
        $this->db->bind(':cat',       $d['Category'] ?? null);
        $this->db->bind(':desc',      $d['Description'] ?? null);
        $this->db->bind(':max',       (int)($d['MaxParticipants'] ?? 10), PDO::PARAM_INT);
        $this->db->bind(':price',     (float)($d['PricePerSession'] ?? 0));
        $this->db->bind(':rpf',       $d['RequiredPlanFeature'] ?? 'none');
        $this->db->bind(':rstart',    $d['RecurrenceStart']);
        $this->db->bind(':rend',      isset($d['RecurrenceEnd']) && $d['RecurrenceEnd'] !== '' ? $d['RecurrenceEnd'] : null);
        $this->db->bind(':createdby', (int)$d['CreatedBy'], PDO::PARAM_INT);
        $this->db->execute();
        return (int)$this->db->lastInsertId();
    }

    public function updateTemplate(int $id, array $d): bool {
        $this->db->query(
            'UPDATE slot_template SET
             TemplateName=:name, SlotType=:stype, StaffType=:stafftype, SlotID=:slotid,
             DayOfWeek=:dow, FacilityID=:fid, AgeGroup=:age, Category=:cat, Description=:desc,
             MaxParticipants=:max, PricePerSession=:price, RequiredPlanFeature=:rpf,
             RecurrenceStart=:rstart, RecurrenceEnd=:rend
             WHERE TemplateID=:id'
        );
        $this->db->bind(':name',      $d['TemplateName']);
        $this->db->bind(':stype',     $d['SlotType']);
        $this->db->bind(':stafftype', $d['StaffType']);
        $this->db->bind(':slotid',    (int)$d['SlotID'], PDO::PARAM_INT);
        $this->db->bind(':dow',       isset($d['DayOfWeek']) && $d['DayOfWeek'] !== '' ? (int)$d['DayOfWeek'] : null);
        $this->db->bind(':fid',       isset($d['FacilityID']) && $d['FacilityID'] !== '' ? (int)$d['FacilityID'] : null);
        $this->db->bind(':age',       $d['AgeGroup'] ?? null);
        $this->db->bind(':cat',       $d['Category'] ?? null);
        $this->db->bind(':desc',      $d['Description'] ?? null);
        $this->db->bind(':max',       (int)($d['MaxParticipants'] ?? 10), PDO::PARAM_INT);
        $this->db->bind(':price',     (float)($d['PricePerSession'] ?? 0));
        $this->db->bind(':rpf',       $d['RequiredPlanFeature'] ?? 'none');
        $this->db->bind(':rstart',    $d['RecurrenceStart']);
        $this->db->bind(':rend',      isset($d['RecurrenceEnd']) && $d['RecurrenceEnd'] !== '' ? $d['RecurrenceEnd'] : null);
        $this->db->bind(':id',        $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function toggleTemplate(int $id): bool {
        $this->db->query('UPDATE slot_template SET IsActive = IF(IsActive=1,0,1) WHERE TemplateID = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // =========================================================
    // STAFF ASSIGNMENTS
    // =========================================================

    public function getStaffForTemplate(int $templateId): array {
        $this->db->query(
            'SELECT ts.*, u.Name AS UserName, u.Role AS UserRole
             FROM slot_template_staff ts
             JOIN user u ON u.UserID = ts.UserID
             WHERE ts.TemplateID = :tid
             ORDER BY ts.StaffRole'
        );
        $this->db->bind(':tid', $templateId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Assign staff to a template.
     * Returns true on success, 'role_mismatch' if user.Role doesn't match StaffType,
     * 'type_mismatch' if the staff type doesn't match the template's StaffType,
     * 'duplicate' if already assigned.
     */
    public function assignStaff(int $templateId, int $userId, string $type, string $role, int $adminId) {
        // Validate submitted type matches the template's StaffType
        $this->db->query('SELECT StaffType FROM slot_template WHERE TemplateID = :tid');
        $this->db->bind(':tid', $templateId, PDO::PARAM_INT);
        $template = $this->db->single();
        if (!$template) return 'not_found';
        if ($template->StaffType !== 'none' && $type !== $template->StaffType) {
            return 'type_mismatch';
        }

        // Validate user.Role matches the submitted type
        $this->db->query('SELECT Role FROM user WHERE UserID = :uid');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $user = $this->db->single();
        if (!$user) return 'not_found';

        $userRole = strtolower($user->Role);
        if ($type === 'coach'   && !in_array($userRole, ['coach', 'admin'])) return 'role_mismatch';
        if ($type === 'trainer' && $userRole !== 'trainer')                  return 'role_mismatch';

        $this->db->query(
            'INSERT INTO slot_template_staff (TemplateID, UserID, StaffType, StaffRole, AssignedBy)
             VALUES (:tid, :uid, :type, :role, :admin)'
        );
        $this->db->bind(':tid',   $templateId, PDO::PARAM_INT);
        $this->db->bind(':uid',   $userId,     PDO::PARAM_INT);
        $this->db->bind(':type',  $type);
        $this->db->bind(':role',  $role);
        $this->db->bind(':admin', $adminId,    PDO::PARAM_INT);
        try {
            $this->db->execute();
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return 'duplicate';
            throw $e;
        }
    }

    public function removeStaff(int $id): bool {
        $this->db->query('DELETE FROM slot_template_staff WHERE ID = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // =========================================================
    // STAFF DROPDOWNS
    // =========================================================

    public function getAvailableCoaches(): array {
        $this->db->query(
            "SELECT UserID, Name FROM user WHERE LOWER(Role) IN ('coach','admin') AND Status='Active' ORDER BY Name"
        );
        return $this->db->resultSet();
    }

    public function getAvailableTrainers(): array {
        $this->db->query(
            "SELECT UserID, Name FROM user WHERE LOWER(Role) = 'trainer' AND Status='Active' ORDER BY Name"
        );
        return $this->db->resultSet();
    }

    public function getFacilities(): array {
        $this->db->query('SELECT FacilityID, Name FROM facility ORDER BY Name');
        return $this->db->resultSet();
    }

    public function getActiveTimeBands(): array {
        $this->db->query('SELECT * FROM slot_time_band WHERE IsActive = 1 ORDER BY SlotID');
        return $this->db->resultSet();
    }
}
