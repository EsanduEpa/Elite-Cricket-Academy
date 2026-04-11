<?php
class M_SlotPlayer {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // =========================================================
    // AVAILABLE OCCURRENCES
    // =========================================================

    /**
     * Returns all upcoming bookable occurrences.
     * Each row gets $row->blocked (bool) and $row->blockReason (string|null)
     * set in PHP after the gate checks so the view can grey blocked rows.
     */
    public function getAvailableOccurrences(int $playerId): array {
        $this->db->query(
            'SELECT so.OccurrenceID, so.OccurrenceDate, so.MaxParticipants AS OccMax,
                    so.Notes,
                    st.TemplateID,
                    COALESCE(st.TemplateName, \'Private Session\') AS TemplateName,
                    COALESCE(st.SlotType, \'private\') AS SlotType,
                    COALESCE(st.StaffType, \'coach\') AS StaffType,
                    COALESCE(st.PricePerSession, 0) AS PricePerSession,
                    st.RequiredPlanFeature,
                    st.MaxParticipants AS TplMax,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    COUNT(sb.BookingID) AS BookedCount,
                    EXISTS (
                        SELECT 1 FROM slot_booking sb2
                        WHERE sb2.OccurrenceID = so.OccurrenceID
                          AND sb2.PlayerID     = :pid_exists
                          AND sb2.Status      != \'cancelled\'
                    ) AS AlreadyBooked
             FROM slot_occurrence so
             LEFT JOIN slot_template  st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band tb ON tb.SlotID      = so.SlotID
             LEFT JOIN facility   f  ON f.FacilityID  = so.FacilityID
             LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID
                    AND sb.Status != \'cancelled\'
             WHERE so.Status IN (\'scheduled\', \'active\')
               AND so.OccurrenceDate >= CURDATE()
               AND (st.IsActive = 1 OR so.TemplateID IS NULL)
                             AND (so.TemplateID IS NULL OR st.SlotType IN (\'private\', \'facility_only\'))
             GROUP BY so.OccurrenceID
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        $this->db->bind(':pid_exists', $playerId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();

        // Run gate checks and annotate each row
        foreach ($rows as $row) {
            if ($row->AlreadyBooked) {
                $row->blocked     = true;
                $row->blockReason = 'already_booked';
                continue;
            }

            // Private sessions (no template) skip subscription entitlement check
            if ($row->TemplateID !== null) {
                $ent = SlotBookingService::validateEntitlement($playerId, (int) $row->TemplateID);
                if (!$ent['ok']) {
                    $row->blocked     = true;
                    $row->blockReason = $ent['code'];
                    continue;
                }
            }

            $med = SlotBookingService::checkMedicalFlag($playerId, $row->OccurrenceDate);
            if (!$med['ok']) {
                $row->blocked     = true;
                $row->blockReason = $med['code'];
                continue;
            }

            $cap = SlotBookingService::checkCapacity($row->OccurrenceID);
            if (!$cap['ok']) {
                $row->blocked     = true;
                $row->blockReason = $cap['code'];
                continue;
            }

            $row->blocked     = false;
            $row->blockReason = null;
            $row->spotsLeft   = $cap['spots_left'];
        }

        return $rows;
    }

    // =========================================================
    // CREATE BOOKING
    // =========================================================

    /**
     * Runs all 3 gate checks then INSERTs a slot_booking row.
     *
     * Returns true on success or a string error code on failure:
     *   no_subscription | plan_mismatch | active_injury | full | duplicate | not_found | error
     */
    public function createBooking(
        int     $occurrenceId,
        int     $playerId,
        string  $source        = 'self',
        int     $bookedBy      = 0,
        ?int    $subscriptionId = null,
        float   $amount        = 0.0,
        ?string $payMethod     = null,
        string  $payStatus     = 'not_required',
        int     $participantCount = 1
    ): bool|string {

        // ── Gate checks (always enforced) ──
        // Load TemplateID for entitlement check
        $this->db->query(
              'SELECT so.OccurrenceDate, so.TemplateID,
                    COALESCE(st.SlotType, \'private\') AS SlotType
               FROM slot_occurrence so
               LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
               WHERE so.OccurrenceID = :oid'
        );
        $this->db->bind(':oid', $occurrenceId, PDO::PARAM_INT);
        $occ = $this->db->single();
        if (!$occ) return 'not_found';

        if ($source === 'self' && $occ->SlotType === 'program') {
            return 'not_found';
        }

        // ShopEmployee counter bookings and private sessions skip subscription check
        if ($source !== 'shop_employee' && $occ->TemplateID !== null) {
            $ent = SlotBookingService::validateEntitlement($playerId, (int)$occ->TemplateID);
            if (!$ent['ok']) return $ent['code'];
            if ($subscriptionId === null && isset($ent['subscription_id'])) {
                $subscriptionId = $ent['subscription_id'];
            }
        }

        $med = SlotBookingService::checkMedicalFlag($playerId, $occ->OccurrenceDate);
        if (!$med['ok']) return $med['code'];

        $cap = SlotBookingService::checkCapacity($occurrenceId);
        if (!$cap['ok']) return $cap['code'];

        $participantCount = max(1, $participantCount);

        if (in_array($occ->SlotType, ['facility_only', 'private'], true)
            && isset($cap['group_capacity'])
            && $cap['group_capacity'] !== null
            && $participantCount > (int)$cap['group_capacity']) {
            return 'full';
        }

        // ── Insert ──
        $this->db->query(
            'INSERT INTO slot_booking
             (OccurrenceID, PlayerID, BookingSource, SubscriptionID, Status, ParticipantCount,
              AmountCharged, PaymentStatus, PaymentMethod, BookedBy)
             VALUES (:oid, :pid, :src, :sub, \'confirmed\', :pcount,
                     :amt, :pstat, :pmeth, :by)'
        );
        $this->db->bind(':oid',   $occurrenceId,  PDO::PARAM_INT);
        $this->db->bind(':pid',   $playerId,       PDO::PARAM_INT);
        $this->db->bind(':src',   $source);
        $this->db->bind(':sub',   $subscriptionId);
        $this->db->bind(':pcount',$participantCount, PDO::PARAM_INT);
        $this->db->bind(':amt',   $amount);
        $this->db->bind(':pstat', $payStatus);
        $this->db->bind(':pmeth', $payMethod);
        $this->db->bind(':by',    $bookedBy ?: null);

        $ok = $this->db->execute();

        if (!$ok) {
            $err = $this->db->getError();
            if ($err && $err[0] === '23000') return 'duplicate';
            return 'error';
        }

        $bookingId = $this->db->lastInsertId();

        // ── Audit log ──
        $this->db->query(
            'INSERT INTO slot_audit_log
             (EntityType, EntityID, Action, ChangedField, NewValue, Reason, ChangedBy, IPAddress)
             VALUES (\'booking\', :bid, \'create\', NULL, \'confirmed\', :src, :by, :ip)'
        );
        $this->db->bind(':bid', $bookingId, PDO::PARAM_INT);
        $this->db->bind(':src', $source);
        $this->db->bind(':by',  $bookedBy ?: $playerId, PDO::PARAM_INT);
        $this->db->bind(':ip',  $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();

        // ── Activity log ──
        $actor = $bookedBy ?: $playerId;
        $this->db->query(
            'INSERT INTO activitylog (UserID, Action, Description, IPAddress)
             VALUES (:uid, \'slot_booking\', :desc, :ip)'
        );
        $this->db->bind(':uid',  $actor, PDO::PARAM_INT);
        $this->db->bind(':desc', "Booked occurrence #{$occurrenceId} for player #{$playerId} (source: {$source})");
        $this->db->bind(':ip',   $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();

        return true;
    }

    public function getTodaySchedule(int $playerId): array {
        $this->db->query(
            'SELECT sb.BookingID,
                    so.OccurrenceID,
                    so.OccurrenceDate AS Date,
                    tb.StartTime,
                    tb.EndTime,
                    COALESCE(st.TemplateName, \'Session\') AS activity,
                    COALESCE(f.Name, \'Academy\') AS location,
                    COALESCE(st.SlotType, \'program\') AS type,
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
                    ) AS coach,
                    sb.Status
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE sb.PlayerID = :pid
               AND sb.Status != \'cancelled\'
               AND so.Status != \'cancelled\'
               AND so.OccurrenceDate = CURDATE()
             ORDER BY tb.StartTime ASC'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getUpcomingScheduleSessions(int $playerId): array {
        $this->db->query(
            'SELECT sb.BookingID,
                    so.OccurrenceID,
                    so.OccurrenceDate AS Date,
                    tb.StartTime,
                    tb.EndTime,
                    COALESCE(st.TemplateName, \'Session\') AS Name,
                    COALESCE(f.Name, \'Academy\') AS Location,
                    CASE
                        WHEN COALESCE(st.SlotType, \'program\') = \'facility_only\' THEN \'Facility Booking\'
                        WHEN COALESCE(st.SlotType, \'program\') = \'private\' THEN \'Private Session\'
                        ELSE \'Assigned Program\'
                    END AS SessionType,
                    CASE
                        WHEN COALESCE(st.SlotType, \'program\') = \'facility_only\' THEN \'Facility\'
                        WHEN COALESCE(st.SlotType, \'program\') = \'private\' THEN \'1:1\'
                        ELSE \'Group\'
                    END AS SessionMode,
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
                    ) AS CoachName,
                    sb.BookingSource,
                    sb.Status
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE sb.PlayerID = :pid
               AND sb.Status != \'cancelled\'
               AND so.Status != \'cancelled\'
               AND so.OccurrenceDate > CURDATE()
             ORDER BY so.OccurrenceDate ASC, tb.StartTime ASC'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getUpcomingCoachSessions(int $playerId): array {
        $sessions = $this->getUpcomingScheduleSessions($playerId);

        return array_values(array_filter($sessions, function ($session) {
            return ($session->SessionType ?? '') === 'Assigned Program'
                || ($session->SessionType ?? '') === 'Private Session';
        }));
    }

    public function getUpcomingBookingFeed(int $playerId): array {
        $this->db->query(
            'SELECT sb.BookingID AS id,
                    COALESCE(st.SlotType, \'program\') AS raw_type,
                    so.OccurrenceDate AS date,
                    tb.StartTime,
                    tb.EndTime,
                    sb.Status,
                    COALESCE(st.TemplateName, \'Session\') AS reason,
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
                    ) AS practitioner_name,
                    COALESCE(f.Name, \'Academy\') AS location,
                    sb.BookingSource
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE sb.PlayerID = :pid
               AND sb.Status != \'cancelled\'
               AND so.Status != \'cancelled\'
               AND so.OccurrenceDate >= CURDATE()
             ORDER BY so.OccurrenceDate ASC, tb.StartTime ASC'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();

        foreach ($rows as $row) {
            if ($row->raw_type === 'facility_only') {
                $row->booking_type = 'facility';
            } elseif ($row->raw_type === 'private') {
                $row->booking_type = 'coach';
            } elseif (($row->BookingSource ?? '') === 'system') {
                $row->booking_type = 'program';
            } else {
                $row->booking_type = 'session';
            }
        }

        return $rows;
    }

    // =========================================================
    // PLAYER'S OWN BOOKINGS
    // =========================================================

    public function getPlayerBookings(int $playerId): array {
        $this->db->query(
                'SELECT sb.BookingID, sb.Status, sb.BookingSource, sb.ParticipantCount,
                    sb.AmountCharged, sb.PaymentStatus, sb.MedicalClearedBy,
                    sb.CancelReason, sb.CreatedAt,
                    so.OccurrenceID, so.OccurrenceDate, so.Status AS OccurrenceStatus,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    st.TemplateName, st.SlotType,
                    f.Name AS FacilityName
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band  tb ON tb.SlotID        = so.SlotID
             LEFT JOIN slot_template  st ON st.TemplateID  = so.TemplateID
             LEFT JOIN facility        f  ON f.FacilityID   = so.FacilityID
             WHERE sb.PlayerID = :pid
             ORDER BY so.OccurrenceDate DESC'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getAssignedProgramBookings(int $playerId, ?string $fromDate = null): array {
        $sql =
                'SELECT sb.BookingID, sb.Status, sb.BookingSource, sb.ParticipantCount,
                    so.OccurrenceID, so.OccurrenceDate, so.Status AS OccurrenceStatus,
                    st.TemplateName, st.SlotType, st.Category, st.AgeGroup,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    IF(
                        EXISTS (
                            SELECT 1 FROM slot_occurrence_staff_override ov0
                            WHERE ov0.OccurrenceID = so.OccurrenceID
                        ),
                        (SELECT GROUP_CONCAT(u.Name ORDER BY u.Name SEPARATOR ", ")
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(u.Name ORDER BY u.Name SEPARATOR ", ")
                         FROM slot_template_staff ts
                         JOIN user u ON u.UserID = ts.UserID
                         WHERE ts.TemplateID = so.TemplateID)
                    ) AS StaffNames
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_template st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE sb.PlayerID = :pid
               AND sb.BookingSource = \'system\'
               AND st.SlotType = \'program\'
               AND sb.Status != \'cancelled\'';

        if ($fromDate !== null && $fromDate !== '') {
            $sql .= ' AND so.OccurrenceDate >= :from';
        }

        $sql .= ' ORDER BY so.OccurrenceDate ASC, tb.StartTime ASC';

        $this->db->query($sql);
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        if ($fromDate !== null && $fromDate !== '') {
            $this->db->bind(':from', $fromDate);
        }

        return $this->db->resultSet();
    }

    public function getUnavailableTimes(int $facilityId, string $date): array {
        $this->db->query('SELECT StartTime, EndTime FROM facilitybooking
            WHERE FacilityID = :fid AND BookingDate = :date AND Status != "cancelled"
            ORDER BY StartTime ASC');
        $this->db->bind(':fid', $facilityId);
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    public function getPlayerDailyFacilityHours(int $playerId, int $facilityId, string $date): float {
        $this->db->query('SELECT SUM(TIMESTAMPDIFF(MINUTE, StartTime, EndTime)) as total_minutes
            FROM facilitybooking
            WHERE PlayerID = :pid AND FacilityID = :fid AND BookingDate = :date AND Status != "cancelled"');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':fid', $facilityId, PDO::PARAM_INT);
        $this->db->bind(':date', $date, PDO::PARAM_STR);
        $result = $this->db->single();
        return round((float)($result->total_minutes ?? 0) / 60, 2);
    }

    public function bookFacility(array $data): int|false {
        $this->db->query('INSERT INTO facilitybooking
            (FacilityID, PlayerID, BookingDate, StartTime, EndTime, Status, TotalCost, BookedBy)
            VALUES (:fid, :pid, :date, :start, :end, "confirmed", :cost, :booked_by)');
        $this->db->bind(':fid',       (int)$data['facility_id'], PDO::PARAM_INT);
        $this->db->bind(':pid',       (int)$data['player_id'],   PDO::PARAM_INT);
        $this->db->bind(':date',      $data['date'],             PDO::PARAM_STR);
        $this->db->bind(':start',     $data['start_time'],       PDO::PARAM_STR);
        $this->db->bind(':end',       $data['end_time'],         PDO::PARAM_STR);
        $this->db->bind(':cost',      $data['total_cost'],       PDO::PARAM_STR);
        $this->db->bind(':booked_by', (int)$data['player_id'],   PDO::PARAM_INT);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function facilityHasTimeConflict(int $facilityId, string $date, string $startTime, string $endTime): bool {
        $this->db->query('SELECT COUNT(*) as cnt FROM facilitybooking
            WHERE FacilityID = :fid AND BookingDate = :date AND Status != "cancelled"
              AND StartTime < :end AND EndTime > :start');
        $this->db->bind(':fid',   $facilityId, PDO::PARAM_INT);
        $this->db->bind(':date',  $date, PDO::PARAM_STR);
        $this->db->bind(':start', $startTime, PDO::PARAM_STR);
        $this->db->bind(':end',   $endTime, PDO::PARAM_STR);
        $result = $this->db->single();
        return (int)($result->cnt ?? 0) > 0;
    }

    public function getFacilityBookingsForPlayer(int $playerId): array {
        $this->db->query('SELECT fb.*, f.Name AS facility_name, f.Location, f.HourlyRate
            FROM facilitybooking fb
            JOIN facility f ON fb.FacilityID = f.FacilityID
            WHERE fb.PlayerID = :player_id
            ORDER BY fb.BookingDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // =========================================================
    // CANCEL BOOKING
    // =========================================================

    /**
     * Returns true, 'not_found', 'forbidden', 'window_closed', or 'already_cancelled'.
     */
    public function cancelBooking(int $bookingId, int $playerId): bool|string {
        // Load booking + occurrence date
        $this->db->query(
            'SELECT sb.BookingID, sb.PlayerID, sb.Status,
                    so.OccurrenceDate
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             WHERE sb.BookingID = :bid'
        );
        $this->db->bind(':bid', $bookingId, PDO::PARAM_INT);
        $row = $this->db->single();

        if (!$row)                        return 'not_found';
        if ((int)$row->PlayerID !== $playerId) return 'forbidden';
        if ($row->Status === 'cancelled') return 'already_cancelled';

        // 24-hour cancellation window
        $hoursUntil = (strtotime($row->OccurrenceDate) - time()) / 3600;
        if ($hoursUntil < 24)             return 'window_closed';

        $this->db->query(
            'UPDATE slot_booking
             SET Status=\'cancelled\', CancelledBy=:pid, CancelledAt=NOW(),
                 CancelReason=\'Player cancelled\'
             WHERE BookingID=:bid AND PlayerID=:pid2'
        );
        $this->db->bind(':pid',  $playerId,  PDO::PARAM_INT);
        $this->db->bind(':bid',  $bookingId, PDO::PARAM_INT);
        $this->db->bind(':pid2', $playerId,  PDO::PARAM_INT);
        $ok = $this->db->execute();
        if (!$ok) return 'error';

        // Audit log
        $this->db->query(
            'INSERT INTO slot_audit_log
             (EntityType, EntityID, Action, ChangedField, OldValue, NewValue,
              Reason, ChangedBy, IPAddress)
             VALUES (\'booking\',:bid,\'cancel\',\'Status\',\'confirmed\',\'cancelled\',
                     \'Player cancelled\',:pid,:ip)'
        );
        $this->db->bind(':bid', $bookingId, PDO::PARAM_INT);
        $this->db->bind(':pid', $playerId,  PDO::PARAM_INT);
        $this->db->bind(':ip',  $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();

        return true;
    }

    // =========================================================
    // FACILITY LISTING & AVAILABILITY
    // =========================================================

    public function getAllFacilities(): array {
        $this->db->query(
            'SELECT FacilityID, Name, Location, Capacity, HourlyRate,
                    AvailabilityStatus, facilityImage
             FROM facility
             ORDER BY FacilityID'
        );
        return $this->db->resultSet();
    }

    public function getTimeBands(): array {
        $this->db->query(
            'SELECT SlotID, SlotLabel, StartTime, EndTime
             FROM slot_time_band
             WHERE IsActive = 1
             ORDER BY StartTime'
        );
        return $this->db->resultSet();
    }

    /**
     * Returns upcoming facility_only / private occurrences with optional filters.
     * Pass 0 / '' to skip a filter dimension.
     * Each row is annotated with ->blocked (bool) and ->blockReason (string|null).
     */
    public function getFacilityOccurrences(
        int    $playerId,
        int    $facilityId = 0,
        string $date       = '',
        int    $slotId     = 0
    ): array {

        $conditions = [
            "so.Status     IN ('scheduled','active')",
            "so.OccurrenceDate >= CURDATE()",
            "st.IsActive   = 1",
            "st.SlotType   IN ('facility_only','private')",
        ];
        if ($facilityId > 0) $conditions[] = 'so.FacilityID = :fid';
        if ($date !== '')    $conditions[] = 'so.OccurrenceDate = :date';
        if ($slotId  > 0)    $conditions[] = 'tb.SlotID = :slotid';
        $where = implode(' AND ', $conditions);

        $this->db->query(
            "SELECT so.OccurrenceID, so.OccurrenceDate, so.MaxParticipants AS OccMax, so.Notes,
                    st.TemplateID, st.TemplateName, st.SlotType, st.PricePerSession,
                    st.RequiredPlanFeature, st.MaxParticipants AS TplMax,
                    tb.SlotID, tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.FacilityID, f.Name AS FacilityName, f.Location AS FacilityLocation,
                    f.HourlyRate, f.Capacity AS FacilityCapacity, f.facilityImage,
                    COUNT(sb.BookingID) AS BookedCount,
                    EXISTS (
                        SELECT 1 FROM slot_booking sb2
                        WHERE sb2.OccurrenceID = so.OccurrenceID
                          AND sb2.PlayerID     = :pid_exists
                          AND sb2.Status      != 'cancelled'
                    ) AS AlreadyBooked
             FROM slot_occurrence so
             JOIN slot_template   st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band  tb ON tb.SlotID     = so.SlotID
             JOIN facility         f ON f.FacilityID  = so.FacilityID
             LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID
                    AND sb.Status != 'cancelled'
             WHERE {$where}
             GROUP BY so.OccurrenceID
             ORDER BY so.OccurrenceDate, tb.StartTime"
        );
        $this->db->bind(':pid_exists', $playerId, PDO::PARAM_INT);
        if ($facilityId > 0) $this->db->bind(':fid',    $facilityId, PDO::PARAM_INT);
        if ($date !== '')    $this->db->bind(':date',   $date);
        if ($slotId  > 0)    $this->db->bind(':slotid', $slotId,     PDO::PARAM_INT);

        $rows = $this->db->resultSet();

        foreach ($rows as $row) {
            if ($row->AlreadyBooked) {
                $row->blocked     = true;
                $row->blockReason = 'already_booked';
                continue;
            }

            $ent = SlotBookingService::validateEntitlement($playerId, (int) $row->TemplateID);
            if (!$ent['ok']) {
                $row->blocked     = true;
                $row->blockReason = $ent['code'];
                continue;
            }

            $med = SlotBookingService::checkMedicalFlag($playerId, $row->OccurrenceDate);
            if (!$med['ok']) {
                $row->blocked     = true;
                $row->blockReason = $med['code'];
                continue;
            }

            $cap = SlotBookingService::checkCapacity($row->OccurrenceID);
            if (!$cap['ok']) {
                $row->blocked     = true;
                $row->blockReason = $cap['code'];
                continue;
            }

            $row->blocked     = false;
            $row->blockReason = null;
            $row->spotsLeft   = $cap['spots_left'];
        }

        return $rows;
    }

    // =========================================================
    // SHOP EMPLOYEE — available counter slots
    // =========================================================

    /**
     * Only facility_only and private slots for counter booking.
     * Returns raw rows without gate checks (ShopEmployee handles medical warning separately).
     */
    public function getCounterSlots(): array {
        $this->db->query(
            'SELECT so.OccurrenceID, so.OccurrenceDate, so.MaxParticipants AS OccMax,
                    st.TemplateName, st.SlotType, st.PricePerSession,
                    st.MaxParticipants AS TplMax,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    COUNT(sb.BookingID) AS BookedCount
             FROM slot_occurrence so
             JOIN slot_template  st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band tb ON tb.SlotID      = so.SlotID
             LEFT JOIN facility   f  ON f.FacilityID  = so.FacilityID
             LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID
                    AND sb.Status != \'cancelled\'
             WHERE so.Status IN (\'scheduled\', \'active\')
               AND so.OccurrenceDate >= CURDATE()
               AND st.IsActive = 1
               AND st.SlotType IN (\'facility_only\', \'private\')
             GROUP BY so.OccurrenceID
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        return $this->db->resultSet();
    }

    public function searchPlayers(string $term): array {
        $this->db->query(
            'SELECT UserID, Name, Email, PhoneNumber
             FROM user
             WHERE Role = \'Player\'
               AND Status = \'active\'
               AND (Name LIKE :t OR Email LIKE :t2 OR UserID = :id)
             LIMIT 20'
        );
        $like = '%' . $term . '%';
        $this->db->bind(':t',  $like);
        $this->db->bind(':t2', $like);
        $this->db->bind(':id', is_numeric($term) ? (int)$term : 0, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
