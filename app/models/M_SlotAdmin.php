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

    // =========================================================
    // OCCURRENCE GENERATION
    // =========================================================

    public function getActiveTemplates(): array {
        $this->db->query(
            'SELECT t.*, tb.SlotLabel FROM slot_template t
             LEFT JOIN slot_time_band tb ON tb.SlotID = t.SlotID
             WHERE t.IsActive = 1 ORDER BY t.TemplateName'
        );
        return $this->db->resultSet();
    }

    /**
     * Generate slot_occurrence rows for every matching weekday in the range.
     * Skips silently on UNIQUE KEY conflicts (duplicate).
     * Returns ['inserted'=>N, 'skipped'=>N, 'skipped_dates'=>[...]]
     */
    public function generateOccurrences(int $templateId, string $from, string $to, int $adminId): array {
        $template = $this->getTemplateById($templateId);
        if (!$template) {
            return ['inserted' => 0, 'skipped' => 0, 'skipped_dates' => [], 'error' => 'Template not found'];
        }

        $inserted     = 0;
        $skipped      = 0;
        $skippedDates = [];
        $current      = strtotime($from);
        $end          = strtotime($to);

        while ($current <= $end) {
            $dow  = (int) date('N', $current); // 1=Mon … 7=Sun
            $date = date('Y-m-d', $current);

            $match = ($template->DayOfWeek === null)
                   || ((int) $template->DayOfWeek === $dow);

            if ($match) {
                $this->db->query(
                    'INSERT INTO slot_occurrence
                     (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy)
                     VALUES (:tid, :slotid, :date, :fid, \'scheduled\', :admin)'
                );
                $this->db->bind(':tid',   $templateId,                              PDO::PARAM_INT);
                $this->db->bind(':slotid', (int) $template->SlotID,                PDO::PARAM_INT);
                $this->db->bind(':date',  $date);
                $this->db->bind(':fid',   $template->FacilityID !== null ? (int) $template->FacilityID : null);
                $this->db->bind(':admin', $adminId,                                 PDO::PARAM_INT);

                $ok = $this->db->execute();

                if ($ok) {
                    $newId = (int) $this->db->lastInsertId();
                    $this->_auditLog('occurrence', $newId, 'create', null, null, 'scheduled', null, $adminId);
                    $inserted++;
                } else {
                    $err = $this->db->getError();
                    if ($err[0] === '23000') {
                        $skippedDates[] = $date;
                        $skipped++;
                    }
                }
            }
            $current = strtotime('+1 day', $current);
        }

        if ($inserted > 0) {
            $this->_activityLog(
                $adminId,
                'generate_occurrences',
                "Generated {$inserted} occurrence(s) for template #{$templateId} ({$template->TemplateName}) from {$from} to {$to}"
            );
        }

        return ['inserted' => $inserted, 'skipped' => $skipped, 'skipped_dates' => $skippedDates];
    }

    /**
     * Occurrences in a date range with full detail for the calendar view.
     * Staff fallback: overrides take priority over template staff.
     */
    public function getOccurrencesForCalendar(string $from, string $to): array {
        $this->db->query(
            'SELECT so.*,
                    st.TemplateName, st.SlotType, st.StaffType,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    IF(
                        EXISTS (
                            SELECT 1 FROM slot_occurrence_staff_override ov0
                            WHERE ov0.OccurrenceID = so.OccurrenceID
                        ),
                        (SELECT GROUP_CONCAT(u.Name ORDER BY u.Name SEPARATOR \', \')
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(u.Name ORDER BY u.Name SEPARATOR \', \')
                         FROM slot_template_staff ts
                         JOIN user u ON u.UserID = ts.UserID
                         WHERE ts.TemplateID = so.TemplateID)
                    ) AS StaffNames,
                    (SELECT COUNT(*) FROM slot_booking sb
                     WHERE sb.OccurrenceID = so.OccurrenceID AND sb.Status != \'cancelled\'
                    ) AS BookingCount
             FROM slot_occurrence so
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN slot_time_band tb ON tb.SlotID    = so.SlotID
             LEFT JOIN facility f         ON f.FacilityID = so.FacilityID
             WHERE so.OccurrenceDate BETWEEN :from AND :to
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        $this->db->bind(':from', $from);
        $this->db->bind(':to',   $to);
        return $this->db->resultSet();
    }

    public function getOccurrenceById(int $id): ?object {
        $this->db->query(
            'SELECT so.*,
                    st.TemplateName, st.SlotType, st.StaffType,
                    st.MaxParticipants AS TemplateMaxParticipants,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    (SELECT COUNT(*) FROM slot_booking sb
                     WHERE sb.OccurrenceID = so.OccurrenceID AND sb.Status != \'cancelled\'
                    ) AS BookingCount
             FROM slot_occurrence so
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN slot_time_band tb ON tb.SlotID    = so.SlotID
             LEFT JOIN facility f         ON f.FacilityID = so.FacilityID
             WHERE so.OccurrenceID = :id'
        );
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $row = $this->db->single();
        return $row ?: null;
    }

    /**
     * Effective staff for one occurrence.
     * Overrides take priority; falls back to template staff if no overrides exist.
     */
    public function getStaffForOccurrence(int $occurrenceId, ?int $templateId): array {
        $this->db->query(
            'SELECT COUNT(*) AS cnt FROM slot_occurrence_staff_override WHERE OccurrenceID = :id'
        );
        $this->db->bind(':id', $occurrenceId, PDO::PARAM_INT);
        $row = $this->db->single();

        if ($row && (int) $row->cnt > 0) {
            $this->db->query(
                'SELECT ov.ID, ov.UserID, ov.StaffType, ov.StaffRole,
                        ov.OverridesUserID, ov.OverrideReason,
                        u.Name AS UserName, u.Role AS UserRole,
                        \'override\' AS Source
                 FROM slot_occurrence_staff_override ov
                 JOIN user u ON u.UserID = ov.UserID
                 WHERE ov.OccurrenceID = :id'
            );
            $this->db->bind(':id', $occurrenceId, PDO::PARAM_INT);
            return $this->db->resultSet();
        }

        if (!$templateId) return [];

        $this->db->query(
            'SELECT ts.ID, ts.UserID, ts.StaffType, ts.StaffRole,
                    NULL AS OverridesUserID, NULL AS OverrideReason,
                    u.Name AS UserName, u.Role AS UserRole,
                    \'template\' AS Source
             FROM slot_template_staff ts
             JOIN user u ON u.UserID = ts.UserID
             WHERE ts.TemplateID = :tid
             ORDER BY ts.StaffRole'
        );
        $this->db->bind(':tid', $templateId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function cancelOccurrence(int $id, string $reason, int $adminId): bool {
        $this->db->query(
            'UPDATE slot_occurrence SET Status=\'cancelled\', CancelReason=:reason WHERE OccurrenceID=:id'
        );
        $this->db->bind(':reason', $reason);
        $this->db->bind(':id',     $id, PDO::PARAM_INT);
        $ok = $this->db->execute();
        if ($ok) {
            $this->_auditLog('occurrence', $id, 'cancel', 'Status', 'scheduled', 'cancelled', $reason, $adminId);
            $this->_activityLog($adminId, 'cancel_occurrence', "Cancelled occurrence #{$id}: {$reason}");
        }
        return $ok;
    }

    public function substituteStaff(
        int $occurrenceId, int $userId, string $type, string $role,
        int $replacesId, string $reason, int $adminId
    ): bool {
        $this->db->query(
            'INSERT INTO slot_occurrence_staff_override
             (OccurrenceID, UserID, StaffType, StaffRole, OverridesUserID, OverrideReason)
             VALUES (:oid, :uid, :type, :role, :replaces, :reason)'
        );
        $this->db->bind(':oid',     $occurrenceId,              PDO::PARAM_INT);
        $this->db->bind(':uid',     $userId,                    PDO::PARAM_INT);
        $this->db->bind(':type',    $type);
        $this->db->bind(':role',    $role);
        $this->db->bind(':replaces', $replacesId > 0 ? $replacesId : null);
        $this->db->bind(':reason',  $reason);
        $ok = $this->db->execute();
        if ($ok) {
            $this->_auditLog(
                'occurrence', $occurrenceId, 'override', 'StaffID',
                (string) $replacesId, (string) $userId, $reason, $adminId
            );
        }
        return $ok;
    }

    /**
     * Create a one-off occurrence with no template (TemplateID = NULL).
     * Returns new OccurrenceID or 0 on failure (e.g. duplicate).
     */
    public function createAdHocOccurrence(array $d, int $adminId): int {
        $this->db->query(
            'INSERT INTO slot_occurrence
             (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, Notes, GeneratedBy)
             VALUES (NULL, :slotid, :date, :fid, \'scheduled\', :max, :notes, :admin)'
        );
        $this->db->bind(':slotid', (int) $d['SlotID'], PDO::PARAM_INT);
        $this->db->bind(':date',   $d['OccurrenceDate']);
        $this->db->bind(':fid',    isset($d['FacilityID']) && $d['FacilityID'] !== '' ? (int) $d['FacilityID'] : null);
        $this->db->bind(':max',    isset($d['MaxParticipants']) && $d['MaxParticipants'] !== '' ? (int) $d['MaxParticipants'] : null);
        $this->db->bind(':notes',  $d['Notes'] ?? null);
        $this->db->bind(':admin',  $adminId, PDO::PARAM_INT);

        $ok = $this->db->execute();
        if (!$ok) return 0;

        $newId = (int) $this->db->lastInsertId();
        if ($newId > 0) {
            $this->_auditLog('occurrence', $newId, 'create', null, null, 'scheduled', 'Ad-hoc occurrence', $adminId);
            $this->_activityLog($adminId, 'create_adhoc_occurrence',
                "Created ad-hoc occurrence #{$newId} on {$d['OccurrenceDate']}");
        }
        return $newId;
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function _auditLog(
        string  $entityType, int    $entityId, string  $action,
        ?string $changedField, ?string $oldValue, ?string $newValue,
        ?string $reason, int $userId
    ): void {
        $this->db->query(
            'INSERT INTO slot_audit_log
             (EntityType, EntityID, Action, ChangedField, OldValue, NewValue, Reason, ChangedBy, IPAddress)
             VALUES (:et, :eid, :act, :cf, :ov, :nv, :reason, :uid, :ip)'
        );
        $this->db->bind(':et',     $entityType);
        $this->db->bind(':eid',    $entityId,    PDO::PARAM_INT);
        $this->db->bind(':act',    $action);
        $this->db->bind(':cf',     $changedField);
        $this->db->bind(':ov',     $oldValue);
        $this->db->bind(':nv',     $newValue);
        $this->db->bind(':reason', $reason);
        $this->db->bind(':uid',    $userId,      PDO::PARAM_INT);
        $this->db->bind(':ip',     $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();
    }

    private function _activityLog(int $userId, string $action, string $description): void {
        $this->db->query(
            'INSERT INTO activitylog (UserID, Action, Description, IPAddress)
             VALUES (:uid, :action, :desc, :ip)'
        );
        $this->db->bind(':uid',    $userId, PDO::PARAM_INT);
        $this->db->bind(':action', $action);
        $this->db->bind(':desc',   $description);
        $this->db->bind(':ip',     $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();
    }
}
