<?php
class M_SlotStaff {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // =========================================================
    // MY OCCURRENCES — sessions this staff member is assigned to
    // =========================================================

    /**
     * All occurrences where $userId is the assigned staff (coach or trainer),
     * within the given date range.
     *
     * Sources checked (both, deduped by OccurrenceID):
     *   1. slot_template_staff  — recurring template assignment
     *   2. slot_occurrence_staff_override — per-date override / self-created private sessions
     */
    public function getMyOccurrences(int $userId, string $role, string $from, string $to): array {
        $this->db->query(
            'SELECT so.OccurrenceID, so.OccurrenceDate, so.Status, so.Notes,
                    so.MaxParticipants AS OccMax,
                    COALESCE(st.TemplateName, \'Private Session\') AS SessionName,
                    COALESCE(st.SlotType, \'private\') AS SlotType,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    (SELECT COUNT(*) FROM slot_booking sb
                     WHERE sb.OccurrenceID = so.OccurrenceID
                       AND sb.Status != \'cancelled\'
                    ) AS BookingCount,
                    COALESCE(so.MaxParticipants, st.MaxParticipants, 10) AS MaxSlots
             FROM slot_occurrence so
             LEFT JOIN slot_template  st ON st.TemplateID = so.TemplateID
             JOIN  slot_time_band tb ON tb.SlotID      = so.SlotID
             LEFT JOIN facility    f  ON f.FacilityID   = so.FacilityID
             WHERE so.OccurrenceDate BETWEEN :from AND :to
               AND (
                   EXISTS (
                       SELECT 1 FROM slot_template_staff ts
                       WHERE ts.TemplateID = so.TemplateID
                         AND ts.UserID     = :uid1
                   )
                   OR
                   EXISTS (
                       SELECT 1 FROM slot_occurrence_staff_override ov
                       WHERE ov.OccurrenceID = so.OccurrenceID
                         AND ov.UserID       = :uid2
                   )
               )
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        $this->db->bind(':from', $from);
        $this->db->bind(':to',   $to);
        $this->db->bind(':uid1', $userId, PDO::PARAM_INT);
        $this->db->bind(':uid2', $userId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // =========================================================
    // OCCURRENCE DETAIL — with ownership check
    // =========================================================

    /**
     * Returns the full occurrence row only if $userId is assigned to it.
     * Returns null if not found OR if the staff member is not assigned.
     */
    public function getOccurrenceDetail(int $occId, int $userId): ?object {
        $this->db->query(
            'SELECT so.OccurrenceID, so.OccurrenceDate, so.Status,
                    so.CancelReason, so.Notes, so.GeneratedBy,
                    so.MaxParticipants AS OccMax,
                    COALESCE(st.TemplateName, \'Private Session\') AS SessionName,
                    COALESCE(st.SlotType, \'private\') AS SlotType,
                    COALESCE(st.StaffType, \'coach\') AS StaffType,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    COALESCE(so.MaxParticipants, st.MaxParticipants, 10) AS MaxSlots,
                    (SELECT COUNT(*) FROM slot_booking sb
                     WHERE sb.OccurrenceID = so.OccurrenceID
                       AND sb.Status != \'cancelled\'
                    ) AS BookingCount
             FROM slot_occurrence so
             LEFT JOIN slot_template  st ON st.TemplateID = so.TemplateID
             JOIN  slot_time_band tb ON tb.SlotID      = so.SlotID
             LEFT JOIN facility    f  ON f.FacilityID   = so.FacilityID
             WHERE so.OccurrenceID = :oid
               AND (
                   EXISTS (
                       SELECT 1 FROM slot_template_staff ts
                       WHERE ts.TemplateID = so.TemplateID
                         AND ts.UserID     = :uid1
                   )
                   OR
                   EXISTS (
                       SELECT 1 FROM slot_occurrence_staff_override ov
                       WHERE ov.OccurrenceID = so.OccurrenceID
                         AND ov.UserID       = :uid2
                   )
               )'
        );
        $this->db->bind(':oid',  $occId,  PDO::PARAM_INT);
        $this->db->bind(':uid1', $userId, PDO::PARAM_INT);
        $this->db->bind(':uid2', $userId, PDO::PARAM_INT);
        $row = $this->db->single();
        return $row ?: null;
    }

    // =========================================================
    // BOOKINGS FOR AN OCCURRENCE (read-only, no payment data)
    // =========================================================

    public function getBookingsForOccurrence(int $occId): array {
        $this->db->query(
            'SELECT sb.BookingID, sb.Status, sb.CreatedAt,
                    u.Name AS PlayerName, u.Email AS PlayerEmail
             FROM slot_booking sb
             JOIN user u ON u.UserID = sb.PlayerID
             WHERE sb.OccurrenceID = :oid
             ORDER BY sb.CreatedAt'
        );
        $this->db->bind(':oid', $occId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // =========================================================
    // CANCEL OCCURRENCE (staff can only cancel their own)
    // =========================================================

    /**
     * Cancel an occurrence that belongs to this staff member.
     *
     * Returns:
     *   true              — success
     *   'not_found'       — OccurrenceID doesn't exist
     *   'not_assigned'    — this staff member is not assigned to it
     *   'already_cancelled' — already cancelled
     */
    public function cancelOccurrence(int $occId, string $reason, int $userId): bool|string {
        // Check it exists and fetch status
        $this->db->query(
            'SELECT OccurrenceID, Status FROM slot_occurrence WHERE OccurrenceID = :oid'
        );
        $this->db->bind(':oid', $occId, PDO::PARAM_INT);
        $occ = $this->db->single();
        if (!$occ) return 'not_found';

        if ($occ->Status === 'cancelled') return 'already_cancelled';

        // Verify ownership
        $detail = $this->getOccurrenceDetail($occId, $userId);
        if (!$detail) return 'not_assigned';

        $this->db->query(
            "UPDATE slot_occurrence
             SET Status = 'cancelled', CancelReason = :reason
             WHERE OccurrenceID = :oid"
        );
        $this->db->bind(':reason', $reason);
        $this->db->bind(':oid',    $occId, PDO::PARAM_INT);
        $ok = $this->db->execute();

        if ($ok) {
            $this->_auditLog('occurrence', $occId, 'cancel', 'Status', 'scheduled', 'cancelled', $reason, $userId);
            $this->_activityLog($userId, 'cancel_occurrence', "Staff cancelled occurrence #{$occId}: {$reason}");
        }
        return $ok ? true : 'error';
    }

    // =========================================================
    // CREATE PRIVATE (AD-HOC) SESSION
    // =========================================================

    /**
     * Creates a one-off occurrence with no template, then self-assigns the
     * creating staff member via slot_occurrence_staff_override so that
     * getMyOccurrences() picks it up immediately.
     *
     * Returns new OccurrenceID (int > 0) or a string error code:
     *   'duplicate' — facility + time + date already taken
     *   'error'     — unexpected DB failure
     */
    public function createPrivateSession(array $d, int $userId, string $staffType): int|string {
        $this->db->query(
            "INSERT INTO slot_occurrence
             (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, Notes, GeneratedBy)
             VALUES (NULL, :slotid, :date, :fid, 'scheduled', :max, :notes, :gen)"
        );
        $this->db->bind(':slotid', (int) $d['SlotID'],        PDO::PARAM_INT);
        $this->db->bind(':date',   $d['OccurrenceDate']);
        $this->db->bind(':fid',    isset($d['FacilityID']) && $d['FacilityID'] !== ''
                                   ? (int) $d['FacilityID'] : null);
        $this->db->bind(':max',    isset($d['MaxParticipants']) && $d['MaxParticipants'] !== ''
                                   ? (int) $d['MaxParticipants'] : 10, PDO::PARAM_INT);
        $this->db->bind(':notes',  $d['Notes'] ?? null);
        $this->db->bind(':gen',    $userId, PDO::PARAM_INT);

        try {
            $ok = $this->db->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return 'duplicate';
            return 'error';
        }

        if (!$ok) {
            $err = $this->db->getError();
            if ($err && $err[0] === '23000') return 'duplicate';
            return 'error';
        }

        $newId = (int) $this->db->lastInsertId();
        if ($newId <= 0) return 'error';

        // Self-assign as lead staff for this occurrence
        $this->db->query(
            "INSERT INTO slot_occurrence_staff_override
             (OccurrenceID, UserID, StaffType, StaffRole, OverridesUserID, OverrideReason)
             VALUES (:oid, :uid, :type, 'lead', NULL, 'Private session created by staff')"
        );
        $this->db->bind(':oid',  $newId,     PDO::PARAM_INT);
        $this->db->bind(':uid',  $userId,    PDO::PARAM_INT);
        $this->db->bind(':type', $staffType);
        $this->db->execute();

        $this->_auditLog('occurrence', $newId, 'create', null, null, 'scheduled',
                         'Private session created by staff UserID=' . $userId, $userId);
        $this->_activityLog($userId, 'create_private_session',
                            "Staff created private session #{$newId} on {$d['OccurrenceDate']}");

        return $newId;
    }

    // =========================================================
    // SUPPORTING LOOKUPS
    // =========================================================

    public function getFacilities(): array {
        $this->db->query('SELECT FacilityID, Name FROM facility ORDER BY Name');
        return $this->db->resultSet();
    }

    public function getActiveTimeBands(): array {
        $this->db->query('SELECT * FROM slot_time_band WHERE IsActive = 1 ORDER BY SlotID');
        return $this->db->resultSet();
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
