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
        string  $payStatus     = 'not_required'
    ): bool|string {

        // ── Gate checks (always enforced) ──
        // Load TemplateID for entitlement check
        $this->db->query(
            'SELECT so.OccurrenceDate, so.TemplateID
             FROM slot_occurrence so WHERE so.OccurrenceID = :oid'
        );
        $this->db->bind(':oid', $occurrenceId, PDO::PARAM_INT);
        $occ = $this->db->single();
        if (!$occ) return 'not_found';

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

        // ── Insert ──
        $this->db->query(
            'INSERT INTO slot_booking
             (OccurrenceID, PlayerID, BookingSource, SubscriptionID, Status,
              AmountCharged, PaymentStatus, PaymentMethod, BookedBy)
             VALUES (:oid, :pid, :src, :sub, \'confirmed\',
                     :amt, :pstat, :pmeth, :by)'
        );
        $this->db->bind(':oid',   $occurrenceId,  PDO::PARAM_INT);
        $this->db->bind(':pid',   $playerId,       PDO::PARAM_INT);
        $this->db->bind(':src',   $source);
        $this->db->bind(':sub',   $subscriptionId);
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

    // =========================================================
    // PLAYER'S OWN BOOKINGS
    // =========================================================

    public function getPlayerBookings(int $playerId): array {
        $this->db->query(
            'SELECT sb.BookingID, sb.Status, sb.BookingSource,
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
