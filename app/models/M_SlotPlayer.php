<?php
class M_SlotPlayer {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private function normalizePlanName(string $planName): string {
        $planName = strtolower(trim($planName));

        return match ($planName) {
            'private_only' => 'private',
            default => $planName,
        };
    }

    private function getActivePlanName(int $playerId): string {
        $this->db->query(
            'SELECT LOWER(TRIM(mp.PlanName)) AS PlanName
             FROM playersubscription ps
             JOIN membershipplan mp ON mp.PlanID = ps.PlanID
             WHERE ps.PlayerID = :pid
               AND ps.Status = \'active\'
             ORDER BY ps.SubscriptionID DESC
             LIMIT 1'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $subscription = $this->db->single();

        return !empty($subscription) ? $this->normalizePlanName((string)($subscription->PlanName ?? '')) : '';
    }

    public function getActivePlanKey(int $playerId): string {
        return $this->getActivePlanName($playerId);
    }

    public function getPlayerModuleSessions(int $playerId): array {
        $visibleSlotTypes = $this->getVisibleSlotTypes($playerId);
        $slotTypeList = "'" . implode("','", $visibleSlotTypes) . "'";

        $this->db->query(
            "SELECT sb.BookingID, sb.Status, sb.BookingSource, sb.ParticipantCount,
                    sb.AmountCharged, sb.PaymentStatus, sb.PaymentMethod, sb.CreatedAt,
                    so.OccurrenceID, so.OccurrenceDate, so.Status AS OccurrenceStatus,
                    so.OccurrenceDate AS Date,
                    so.OccurrenceDate AS date,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    COALESCE(st.TemplateName, 'Session') AS TemplateName,
                    COALESCE(st.SlotType, 'program') AS SlotType,
                    COALESCE(st.StaffType, '') AS StaffType,
                    COALESCE(st.PricePerSession, 0) AS PricePerSession,
                    COALESCE(f.Name, 'Academy') AS FacilityName,
                    IF(
                        EXISTS (
                            SELECT 1 FROM slot_occurrence_staff_override ov0
                            WHERE ov0.OccurrenceID = so.OccurrenceID
                        ),
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, ' ', u.LastName) ORDER BY u.FirstName SEPARATOR ', ')
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, ' ', u.LastName) ORDER BY u.FirstName SEPARATOR ', ')
                         FROM slot_template_staff ts
                         JOIN user u ON u.UserID = ts.UserID
                         WHERE ts.TemplateID = so.TemplateID)
                    ) AS StaffNames
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE sb.PlayerID = :pid
               AND COALESCE(st.SlotType, 'program') IN (" . $slotTypeList . ")
             ORDER BY so.OccurrenceDate ASC, tb.StartTime ASC"
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();

        $this->appendFacilityBookingsToSchedule($rows, $playerId);

        $coachSessionSlotTypes = $this->getCoachSessionSlotTypes($playerId);
        if (!empty($coachSessionSlotTypes)) {
            foreach ($this->getAssignedCoachOccurrences($playerId, $coachSessionSlotTypes) as $assignedSession) {
                $rows[] = (object) [
                    'BookingID' => null,
                    'Status' => 'scheduled',
                    'BookingSource' => 'system',
                    'ParticipantCount' => null,
                    'AmountCharged' => 0,
                    'PaymentStatus' => 'not_required',
                    'PaymentMethod' => null,
                    'CreatedAt' => null,
                    'OccurrenceID' => $assignedSession->OccurrenceID,
                    'OccurrenceDate' => $assignedSession->OccurrenceDate,
                    'Date' => $assignedSession->OccurrenceDate,
                    'date' => $assignedSession->OccurrenceDate,
                    'OccurrenceStatus' => 'scheduled',
                    'SlotLabel' => $assignedSession->SlotLabel,
                    'StartTime' => $assignedSession->StartTime,
                    'EndTime' => $assignedSession->EndTime,
                    'TemplateName' => $assignedSession->TemplateName,
                    'SlotType' => $assignedSession->SlotType ?? 'program',
                    'StaffType' => $assignedSession->StaffType ?? 'coach',
                    'PricePerSession' => $assignedSession->PricePerSession ?? 0,
                    'FacilityName' => $assignedSession->FacilityName ?? 'Academy',
                    'StaffNames' => $assignedSession->CoachName ?? null,
                ];
            }
        }

        $rows = $this->dedupeScheduleRows($rows);
        $this->sortScheduleRows($rows);

        return $rows;
    }

    public function getAllPrivateCoachOccurrences(int $playerId): array {
        if (!$this->canSeePrivateSessions($playerId)) {
            return [];
        }

        $this->db->query(
            'SELECT DISTINCT so.OccurrenceID, so.OccurrenceDate, so.MaxParticipants AS OccMax,
                    so.Notes,
                    st.TemplateID,
                    COALESCE(st.TemplateName, "Private Session") AS TemplateName,
                    COALESCE(st.SlotType, "private") AS SlotType,
                    COALESCE(st.StaffType, "coach") AS StaffType,
                    COALESCE(st.PricePerSession, 0) AS PricePerSession,
                    st.RequiredPlanFeature,
                    st.MaxParticipants AS TplMax,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.FacilityID, f.Name AS FacilityName,
                    CONCAT(c.FirstName, " ", c.LastName) AS CoachName,
                    EXISTS (
                        SELECT 1 FROM slot_booking sb2
                        WHERE sb2.OccurrenceID = so.OccurrenceID
                          AND sb2.PlayerID = :pid_exists
                          AND sb2.Status != "cancelled"
                    ) AS AlreadyBooked
             FROM slot_occurrence so
             JOIN slot_template st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             LEFT JOIN slot_template_staff ts ON ts.TemplateID = st.TemplateID
             LEFT JOIN user c ON c.UserID = ts.UserID
             WHERE so.Status IN ("scheduled", "active")
               AND so.OccurrenceDate >= CURDATE()
               AND st.IsActive = 1
               AND COALESCE(st.SlotType, "private") = "private"
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        $this->db->bind(':pid_exists', $playerId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();

        foreach ($rows as $row) {
            $row->blocked = false;
            $row->blockReason = null;
            $row->SessionMode = 'Private';

            if (!empty($row->AlreadyBooked)) {
                $row->blocked = true;
                $row->blockReason = 'already_booked';
            }

            if (!empty($row->TemplateID)) {
                $ent = SlotBookingService::validateEntitlement($playerId, (int) $row->TemplateID);
                if (!$ent['ok']) {
                    $row->blocked = true;
                    $row->blockReason = $ent['code'];
                    continue;
                }
            }

            $med = SlotBookingService::checkMedicalFlag($playerId, $row->OccurrenceDate);
            if (!$med['ok']) {
                $row->blocked = true;
                $row->blockReason = $med['code'];
                continue;
            }

            $cap = SlotBookingService::checkCapacity($row->OccurrenceID);
            if (!$cap['ok']) {
                $row->blocked = true;
                $row->blockReason = $cap['code'];
                continue;
            }

            if ($this->playerHasTimeConflict(
                $playerId,
                (string) $row->OccurrenceDate,
                (string) ($row->StartTime ?? '00:00:00'),
                (string) ($row->EndTime ?? '00:00:00')
            )) {
                $row->blocked = true;
                $row->blockReason = 'time_conflict';
                continue;
            }

            if ($this->getActivePlanName($playerId) === 'private'
                && $this->getWeeklySlotBookingCount($playerId, 'private') >= 2) {
                $row->blocked = true;
                $row->blockReason = 'weekly_private_limit';
                continue;
            }

            $row->spotsLeft = $cap['spots_left'];
        }

        return $rows;
    }

    private function canSeeProgramSessions(int $playerId): bool {
        return in_array($this->getActivePlanKey($playerId), ['general', 'pro'], true);
    }

    private function canSeePrivateSessions(int $playerId): bool {
        return in_array($this->getActivePlanKey($playerId), ['private', 'pro'], true);
    }

    private function canSeeFacilityBookings(int $playerId): bool {
        return in_array($this->getActivePlanKey($playerId), ['general', 'private', 'pro', 'facility_only'], true);
    }

    public function canAccessCoachSessions(int $playerId): bool {
        return $this->getActivePlanKey($playerId) !== 'facility_only';
    }

    private function getCoachSessionSlotTypes(int $playerId): array {
        return match ($this->getActivePlanKey($playerId)) {
            'general' => ['program'],
            'private' => ['private'],
            'pro' => ['program', 'private'],
            default => [],
        };
    }

    private function getVisibleSlotTypes(int $playerId): array {
        return match ($this->getActivePlanName($playerId)) {
            'general' => ['program', 'facility_only'],
            'private' => ['private', 'facility_only'],
            'pro' => ['program', 'private', 'facility_only'],
            'facility_only' => ['facility_only'],
            default => ['program', 'private', 'facility_only'],
        };
    }

    private function getWeeklySlotBookingCount(int $playerId, string $slotType): int {
        $this->db->query(
            'SELECT COUNT(*) AS cnt
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             WHERE sb.PlayerID = :pid
               AND sb.Status != \'cancelled\'
               AND YEARWEEK(so.OccurrenceDate, 1) = YEARWEEK(CURDATE(), 1)
               AND COALESCE(st.SlotType, \'program\') = :slot_type'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':slot_type', $slotType, PDO::PARAM_STR);
        $row = $this->db->single();

        return (int)($row->cnt ?? 0);
    }

    private function getWeeklyFacilityBookingCount(int $playerId): int {
        $this->db->query(
            'SELECT (
                COALESCE((SELECT COUNT(*)
                 FROM facilitybooking fb
                 WHERE fb.PlayerID = :pid
                   AND fb.Status != \'cancelled\'
                   AND YEARWEEK(fb.BookingDate, 1) = YEARWEEK(CURDATE(), 1)), 0)
                +
                COALESCE((SELECT COUNT(*)
                 FROM slot_booking sb
                 JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
                 LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
                 WHERE sb.PlayerID = :pid2
                   AND sb.Status != \'cancelled\'
                   AND YEARWEEK(so.OccurrenceDate, 1) = YEARWEEK(CURDATE(), 1)
                   AND COALESCE(st.SlotType, \'program\') = \'facility_only\'), 0)
             ) AS cnt'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':pid2', $playerId, PDO::PARAM_INT);
        $row = $this->db->single();

        return (int)($row->cnt ?? 0);
    }

    public function playerHasTimeConflict(int $playerId, string $date, string $startTime, string $endTime): bool {
        $this->db->query(
            'SELECT COUNT(*) AS cnt
             FROM (
                 SELECT 1 AS conflict_row
                 FROM slot_booking sb
                 JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
                 JOIN slot_time_band tb ON tb.SlotID = so.SlotID
                 WHERE sb.PlayerID = :pid1
                   AND sb.Status != "cancelled"
                   AND so.Status != "cancelled"
                   AND TIMESTAMP(so.OccurrenceDate, tb.StartTime) < :request_end
                   AND TIMESTAMP(so.OccurrenceDate, tb.EndTime) > :request_start
                 UNION ALL
                 SELECT 1 AS conflict_row
                 FROM facilitybooking fb
                 WHERE fb.PlayerID = :pid2
                   AND fb.Status != "cancelled"
                   AND TIMESTAMP(fb.BookingDate, fb.StartTime) < :request_end
                   AND TIMESTAMP(fb.BookingDate, fb.EndTime) > :request_start
             ) AS conflicts'
        );
        $this->db->bind(':pid1', $playerId, PDO::PARAM_INT);
        $this->db->bind(':pid2', $playerId, PDO::PARAM_INT);
        $this->db->bind(':request_start', $date . ' ' . $startTime, PDO::PARAM_STR);
        $this->db->bind(':request_end', $date . ' ' . $endTime, PDO::PARAM_STR);
        $row = $this->db->single();

        return (int)($row->cnt ?? 0) > 0;
    }

    private function mapFacilityBookingToScheduleRow(object $booking, string $bookingType = 'facility'): object {
        return (object) [
            'BookingID' => $booking->BookingID ?? null,
            'OccurrenceID' => null,
            'OccurrenceDate' => $booking->BookingDate ?? $booking->date ?? null,
            'Date' => $booking->BookingDate ?? $booking->date ?? null,
            'date' => $booking->BookingDate ?? $booking->date ?? null,
            'StartTime' => $booking->StartTime ?? null,
            'EndTime' => $booking->EndTime ?? null,
            'activity' => $booking->facility_name ?? $booking->FacilityName ?? 'Facility Booking',
            'location' => $booking->Location ?? $booking->facility_name ?? 'Academy',
            'coach' => null,
            'Name' => $booking->facility_name ?? 'Facility Booking',
            'Location' => $booking->Location ?? $booking->facility_name ?? 'Academy',
            'SessionType' => 'Facility',
            'SessionMode' => 'Facility',
            'reason' => $booking->facility_name ?? 'Facility Booking',
            'practitioner_name' => null,
            'booking_type' => $bookingType,
            'Status' => $booking->Status ?? 'confirmed',
            'BookingSource' => 'facility_booking',
            'SlotLabel' => $booking->facility_name ?? 'Facility Booking',
            'TemplateName' => $booking->facility_name ?? 'Facility Booking',
            'FacilityName' => $booking->facility_name ?? 'Academy',
            'SlotType' => 'facility_only',
            'StaffType' => '',
            'PricePerSession' => $booking->TotalCost ?? 0,
            'StaffNames' => null,
        ];
    }

    private function appendFacilityBookingsToSchedule(array &$rows, int $playerId, ?string $fromDate = null, ?string $toDate = null): void {
        if (!$this->canSeeFacilityBookings($playerId)) {
            return;
        }

        $sql = 'SELECT fb.BookingID, fb.PlayerID, fb.BookingDate, fb.StartTime, fb.EndTime, fb.Status,
                       f.Name AS facility_name, f.Location
                FROM facilitybooking fb
                JOIN facility f ON f.FacilityID = fb.FacilityID
                WHERE fb.PlayerID = :pid
                  AND fb.Status != \'cancelled\'';

        if ($fromDate !== null) {
            $sql .= ' AND fb.BookingDate >= :from_date';
        }
        if ($toDate !== null) {
            $sql .= ' AND fb.BookingDate <= :to_date';
        }

        $sql .= ' ORDER BY fb.BookingDate ASC, fb.StartTime ASC';

        $this->db->query($sql);
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        if ($fromDate !== null) {
            $this->db->bind(':from_date', $fromDate, PDO::PARAM_STR);
        }
        if ($toDate !== null) {
            $this->db->bind(':to_date', $toDate, PDO::PARAM_STR);
        }

        foreach ($this->db->resultSet() as $booking) {
            $rows[] = $this->mapFacilityBookingToScheduleRow($booking);
        }
    }

    private function sortScheduleRows(array &$rows): void {
        usort($rows, function ($left, $right) {
            $leftDate = $left->date ?? $left->Date ?? '';
            $rightDate = $right->date ?? $right->Date ?? '';
            $leftTime = $left->StartTime ?? '';
            $rightTime = $right->StartTime ?? '';
            return strcmp($leftDate . ' ' . $leftTime, $rightDate . ' ' . $rightTime);
        });
    }

    private function dedupeScheduleRows(array $rows): array {
        $deduped = [];

        foreach ($rows as $row) {
            $occurrenceId = (int) ($row->OccurrenceID ?? 0);
            $bookingId = (int) ($row->BookingID ?? $row->id ?? 0);
            $date = (string) ($row->date ?? $row->Date ?? '');
            $startTime = (string) ($row->StartTime ?? '');
            $key = $occurrenceId > 0
                ? 'occ:' . $occurrenceId
                : 'row:' . $bookingId . ':' . $date . ':' . $startTime . ':' . (string) ($row->booking_type ?? '');

            if (!isset($deduped[$key])) {
                $deduped[$key] = $row;
                continue;
            }

            $existing = $deduped[$key];
            $existingHasBooking = !empty($existing->BookingID) || !empty($existing->id);
            $incomingHasBooking = !empty($row->BookingID) || !empty($row->id);

            if ($incomingHasBooking && !$existingHasBooking) {
                $deduped[$key] = $row;
            }
        }

        return array_values($deduped);
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
        $visibleSlotTypes = $this->getVisibleSlotTypes($playerId);
        $slotTypeList = "'" . implode("','", $visibleSlotTypes) . "'";

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
                    f.FacilityID, f.Name AS FacilityName,
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
                             AND (so.TemplateID IS NULL OR st.SlotType IN (\'program\', \'facility_only\'))
                             AND COALESCE(st.SlotType, \'program\') IN (' . $slotTypeList . ')
             GROUP BY so.OccurrenceID
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        $this->db->bind(':pid_exists', $playerId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();

        // Run gate checks and annotate each row
        foreach ($rows as $row) {
            if (($row->SlotType ?? '') === 'program' && !empty($row->TemplateID)) {
                $eligibleCount = SlotBookingService::getEligiblePlayerCountForTemplate((int) $row->TemplateID);
                $row->EligiblePlayerCount = $eligibleCount;
                $row->TplMax = $eligibleCount;

                $row->blocked     = true;
                $row->blockReason = 'assigned_program';
                $row->spotsLeft   = null;
                continue;
            }

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

    public function getAssignedCoachOccurrences(int $playerId, ?array $slotTypes = null): array {
        $slotTypes = $slotTypes ?? $this->getCoachSessionSlotTypes($playerId);

        if (empty($slotTypes)) {
            return [];
        }

        $slotTypeList = "'" . implode("','", array_map([$this, 'normalizePlanName'], $slotTypes)) . "'";

        $this->db->query(
            'SELECT DISTINCT so.OccurrenceID, so.OccurrenceDate, so.MaxParticipants AS OccMax,
                    so.Notes,
                    st.TemplateID,
                    COALESCE(st.TemplateName, "Group Session") AS TemplateName,
                    COALESCE(st.SlotType, "program") AS SlotType,
                    COALESCE(st.StaffType, "coach") AS StaffType,
                    COALESCE(st.PricePerSession, 0) AS PricePerSession,
                    st.RequiredPlanFeature,
                    st.MaxParticipants AS TplMax,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    f.Name AS FacilityName,
                    CONCAT(c.FirstName, " ", c.LastName) AS CoachName,
                                        psca.AgeGroup AS PlayerAgeGroup,
                                        EXISTS (
                                                SELECT 1 FROM slot_booking sb2
                                                WHERE sb2.OccurrenceID = so.OccurrenceID
                                                    AND sb2.PlayerID = :pid_exists
                                                    AND sb2.Status != "cancelled"
                                        ) AS AlreadyBooked
             FROM slot_occurrence so
             JOIN slot_template st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             JOIN slot_template_staff ts ON ts.TemplateID = st.TemplateID
             JOIN user c ON c.UserID = ts.UserID
             JOIN player_skill_coach_assignment psca
               ON psca.PlayerID = :pid
              AND psca.CoachID = c.UserID
             WHERE so.Status IN ("scheduled", "active")
               AND so.OccurrenceDate >= CURDATE()
               AND st.IsActive = 1
               AND COALESCE(st.SlotType, "program") IN (' . $slotTypeList . ')
               AND LOWER(TRIM(psca.AgeGroup)) = LOWER(TRIM(COALESCE(st.AgeGroup, "")))
               AND LOWER(TRIM(psca.CoachingType)) = LOWER(TRIM(COALESCE(st.Category, "")))
             ORDER BY so.OccurrenceDate, tb.StartTime'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':pid_exists', $playerId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();

        foreach ($rows as $row) {
            $slotType = strtolower((string) ($row->SlotType ?? 'program'));

            if ($slotType === 'program') {
                $row->blocked = true;
                $row->blockReason = 'assigned_program';
                $row->spotsLeft = null;
                $row->SessionMode = 'Group';
                continue;
            }

            $row->SessionMode = 'Private';

            if (!empty($row->AlreadyBooked)) {
                $row->blocked = true;
                $row->blockReason = 'already_booked';
                continue;
            }

            if (!empty($row->TemplateID)) {
                $ent = SlotBookingService::validateEntitlement($playerId, (int) $row->TemplateID);
                if (!$ent['ok']) {
                    $row->blocked = true;
                    $row->blockReason = $ent['code'];
                    continue;
                }
            }

            $med = SlotBookingService::checkMedicalFlag($playerId, $row->OccurrenceDate);
            if (!$med['ok']) {
                $row->blocked = true;
                $row->blockReason = $med['code'];
                continue;
            }

            $cap = SlotBookingService::checkCapacity($row->OccurrenceID);
            if (!$cap['ok']) {
                $row->blocked = true;
                $row->blockReason = $cap['code'];
                continue;
            }

            if ($this->playerHasTimeConflict(
                $playerId,
                (string) $row->OccurrenceDate,
                (string) ($row->StartTime ?? '00:00:00'),
                (string) ($row->EndTime ?? '00:00:00')
            )) {
                $row->blocked = true;
                $row->blockReason = 'time_conflict';
                continue;
            }

            if ($slotType === 'private'
                && $this->getActivePlanName($playerId) === 'private'
                && $this->getWeeklySlotBookingCount($playerId, 'private') >= 2) {
                $row->blocked = true;
                $row->blockReason = 'weekly_private_limit';
                continue;
            }

            $row->blocked = false;
            $row->blockReason = null;
            $row->spotsLeft = $cap['spots_left'];
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
                  tb.StartTime, tb.EndTime,
                  COALESCE(st.SlotType, \'private\') AS SlotType
               FROM slot_occurrence so
               LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
               JOIN slot_time_band tb ON tb.SlotID = so.SlotID
               WHERE so.OccurrenceID = :oid'
        );
        $this->db->bind(':oid', $occurrenceId, PDO::PARAM_INT);
        $occ = $this->db->single();
        if (!$occ) return 'not_found';

        if ($source === 'self' && $occ->SlotType === 'program') {
            return 'not_found';
        }

        $planName = $this->getActivePlanName($playerId);

        if ($occ->SlotType === 'program' && !in_array($planName, ['general', 'pro'], true)) {
            return 'plan_mismatch';
        }

        if ($occ->SlotType === 'private' && !in_array($planName, ['private', 'pro'], true)) {
            return 'plan_mismatch';
        }

        if ($occ->SlotType === 'facility_only' && !in_array($planName, ['general', 'private', 'pro', 'facility_only'], true)) {
            return 'plan_mismatch';
        }

        if ($this->playerHasTimeConflict(
            $playerId,
            (string) $occ->OccurrenceDate,
            (string) ($occ->StartTime ?? '00:00:00'),
            (string) ($occ->EndTime ?? '00:00:00')
        )) {
            return 'time_conflict';
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

        if ($occ->SlotType === 'private' && $planName === 'private' && $this->getWeeklySlotBookingCount($playerId, 'private') >= 2) {
            return 'weekly_private_limit';
        }

        if ($occ->SlotType === 'facility_only' && $this->getWeeklyFacilityBookingCount($playerId) >= 3) {
            return 'weekly_facility_limit';
        }

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
        $visibleSlotTypes = $this->getVisibleSlotTypes($playerId);
        $slotTypeList = "'" . implode("','", $visibleSlotTypes) . "'";

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
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR \', \')
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR \', \')
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
                             AND COALESCE(st.SlotType, \'program\') IN (' . $slotTypeList . ')
             ORDER BY tb.StartTime ASC'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
                $rows = $this->db->resultSet();

                $this->appendFacilityBookingsToSchedule($rows, $playerId, date('Y-m-d'), date('Y-m-d'));
                $this->sortScheduleRows($rows);

                return $rows;
    }

    public function getUpcomingScheduleSessions(int $playerId): array {
                $visibleSlotTypes = $this->getVisibleSlotTypes($playerId);
                $slotTypeList = "'" . implode("','", $visibleSlotTypes) . "'";

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
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR \', \')
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR \', \')
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
               AND COALESCE(st.SlotType, \'program\') IN (' . $slotTypeList . ')
             ORDER BY so.OccurrenceDate ASC, tb.StartTime ASC'
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $sessions = $this->db->resultSet();

        $this->appendFacilityBookingsToSchedule($sessions, $playerId, date('Y-m-d', strtotime('+1 day')));

        $coachSessionSlotTypes = $this->getCoachSessionSlotTypes($playerId);
        if (!empty($coachSessionSlotTypes)) {
            foreach ($this->getAssignedCoachOccurrences($playerId, $coachSessionSlotTypes) as $assignedSession) {
                $sessionType = strtolower((string) ($assignedSession->SlotType ?? 'program')) === 'private'
                    ? 'Private Session'
                    : 'Assigned Program';
                $sessionMode = strtolower((string) ($assignedSession->SlotType ?? 'program')) === 'private'
                    ? '1:1'
                    : 'Group';

                $sessions[] = (object) [
                    'BookingID' => null,
                    'OccurrenceID' => $assignedSession->OccurrenceID,
                    'Date' => $assignedSession->OccurrenceDate,
                    'date' => $assignedSession->OccurrenceDate,
                    'StartTime' => $assignedSession->StartTime,
                    'EndTime' => $assignedSession->EndTime,
                    'Name' => $assignedSession->TemplateName,
                    'Location' => $assignedSession->FacilityName ?? 'Academy',
                    'SessionType' => $sessionType,
                    'SessionMode' => $sessionMode,
                    'CoachName' => $assignedSession->CoachName ?? null,
                    'BookingSource' => 'system',
                    'Status' => 'scheduled',
                    'booking_type' => $sessionMode === '1:1' ? 'coach' : 'program',
                ];
            }
        }

        $sessions = $this->dedupeScheduleRows($sessions);
        $this->sortScheduleRows($sessions);

        return $sessions;
    }

    public function getUpcomingCoachSessions(int $playerId): array {
        return $this->getAssignedCoachOccurrences($playerId, $this->getCoachSessionSlotTypes($playerId));
    }

    public function getUpcomingBookingFeed(int $playerId): array {
        $visibleSlotTypes = $this->getVisibleSlotTypes($playerId);
        $slotTypeList = "'" . implode("','", $visibleSlotTypes) . "'";

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
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR \', \')
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR \', \')
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
                             AND COALESCE(st.SlotType, \'program\') IN (' . $slotTypeList . ')
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

        $this->appendFacilityBookingsToSchedule($rows, $playerId, date('Y-m-d'));

        $coachSessionSlotTypes = $this->getCoachSessionSlotTypes($playerId);
        if (!empty($coachSessionSlotTypes)) {
            foreach ($this->getAssignedCoachOccurrences($playerId, $coachSessionSlotTypes) as $assignedSession) {
                $slotType = strtolower((string) ($assignedSession->SlotType ?? 'program'));

                $rows[] = (object) [
                    'id' => 'assigned_' . $assignedSession->OccurrenceID,
                    'raw_type' => $slotType,
                    'date' => $assignedSession->OccurrenceDate,
                    'StartTime' => $assignedSession->StartTime,
                    'EndTime' => $assignedSession->EndTime,
                    'Status' => 'scheduled',
                    'reason' => $assignedSession->TemplateName,
                    'practitioner_name' => $assignedSession->CoachName ?? null,
                    'location' => $assignedSession->FacilityName ?? 'Academy',
                    'booking_type' => $slotType === 'private' ? 'coach' : 'program',
                ];
            }
        }

        $rows = $this->dedupeScheduleRows($rows);
        $this->sortScheduleRows($rows);

        return $rows;
    }

    // =========================================================
    // PLAYER'S OWN BOOKINGS
    // =========================================================

    public function getPlayerBookings(int $playerId): array {
        $visibleSlotTypes = $this->getVisibleSlotTypes($playerId);
        $slotTypeList = "'" . implode("','", $visibleSlotTypes) . "'";

        $this->db->query("SELECT sb.BookingID, sb.Status, sb.BookingSource, sb.ParticipantCount,
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
                             AND COALESCE(st.SlotType, 'program') IN (" . $slotTypeList . ")
                         ORDER BY so.OccurrenceDate DESC");
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getPlayerBookingsDueForReminder(int $windowStartMinutes = 55, int $windowEndMinutes = 65): array {
        $windowStartMinutes = max(0, $windowStartMinutes);
        $windowEndMinutes = max($windowStartMinutes, $windowEndMinutes);

        $this->db->query(
            'SELECT sb.BookingID, sb.PlayerID, sb.Status, sb.BookingSource,
                    u.Email AS PlayerEmail,
                    TRIM(CONCAT_WS(" ", u.FirstName, u.LastName)) AS PlayerName,
                    so.OccurrenceID, so.OccurrenceDate,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    COALESCE(st.TemplateName, "Session") AS TemplateName,
                    COALESCE(st.SlotType, "private") AS SlotType,
                    COALESCE(f.Name, "Academy") AS FacilityName,
                    IF(
                        EXISTS (
                            SELECT 1 FROM slot_occurrence_staff_override ov0
                            WHERE ov0.OccurrenceID = so.OccurrenceID
                        ),
                        (SELECT GROUP_CONCAT(CONCAT(staff.FirstName, " ", staff.LastName) ORDER BY staff.FirstName SEPARATOR ", ")
                         FROM slot_occurrence_staff_override ov
                         JOIN user staff ON staff.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(staff.FirstName, " ", staff.LastName) ORDER BY staff.FirstName SEPARATOR ", ")
                         FROM slot_template_staff ts
                         JOIN user staff ON staff.UserID = ts.UserID
                         WHERE ts.TemplateID = so.TemplateID)
                    ) AS StaffNames
             FROM slot_booking sb
             JOIN user u ON u.UserID = sb.PlayerID
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE sb.Status = "confirmed"
               AND so.Status IN ("scheduled", "active")
               AND TIMESTAMP(so.OccurrenceDate, tb.StartTime)
                   BETWEEN DATE_ADD(NOW(), INTERVAL :window_start MINUTE)
                   AND DATE_ADD(NOW(), INTERVAL :window_end MINUTE)
             ORDER BY so.OccurrenceDate ASC, tb.StartTime ASC, sb.BookingID ASC'
        );
        $this->db->bind(':window_start', $windowStartMinutes, PDO::PARAM_INT);
        $this->db->bind(':window_end', $windowEndMinutes, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getOccurrenceNotificationDetails(int $occurrenceId): object|false {
        $this->db->query(
            'SELECT so.OccurrenceID, so.OccurrenceDate,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    COALESCE(st.TemplateName, "Session") AS TemplateName,
                    COALESCE(st.SlotType, "private") AS SlotType,
                    COALESCE(f.Name, "Academy") AS FacilityName,
                    IF(
                        EXISTS (
                            SELECT 1 FROM slot_occurrence_staff_override ov0
                            WHERE ov0.OccurrenceID = so.OccurrenceID
                        ),
                        (SELECT GROUP_CONCAT(CONCAT(staff.FirstName, " ", staff.LastName) ORDER BY staff.FirstName SEPARATOR ", ")
                         FROM slot_occurrence_staff_override ov
                         JOIN user staff ON staff.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(staff.FirstName, " ", staff.LastName) ORDER BY staff.FirstName SEPARATOR ", ")
                         FROM slot_template_staff ts
                         JOIN user staff ON staff.UserID = ts.UserID
                         WHERE ts.TemplateID = so.TemplateID)
                    ) AS StaffNames
             FROM slot_occurrence so
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             WHERE so.OccurrenceID = :oid
             LIMIT 1'
        );
        $this->db->bind(':oid', $occurrenceId, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getOccurrenceStaffUserIds(int $occurrenceId): array {
        $this->db->query(
            'SELECT DISTINCT staff.UserID
             FROM slot_occurrence so
             JOIN user staff ON (
                staff.UserID IN (
                    SELECT ov.UserID
                    FROM slot_occurrence_staff_override ov
                    WHERE ov.OccurrenceID = so.OccurrenceID
                )
                OR (
                    NOT EXISTS (
                        SELECT 1
                        FROM slot_occurrence_staff_override ov0
                        WHERE ov0.OccurrenceID = so.OccurrenceID
                    )
                    AND staff.UserID IN (
                        SELECT ts.UserID
                        FROM slot_template_staff ts
                        WHERE ts.TemplateID = so.TemplateID
                    )
                )
             )
             WHERE so.OccurrenceID = :oid'
        );
        $this->db->bind(':oid', $occurrenceId, PDO::PARAM_INT);
        return array_map(static function ($row) {
            return (int)($row->UserID ?? 0);
        }, $this->db->resultSet());
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
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR ", ")
                         FROM slot_occurrence_staff_override ov
                         JOIN user u ON u.UserID = ov.UserID
                         WHERE ov.OccurrenceID = so.OccurrenceID),
                        (SELECT GROUP_CONCAT(CONCAT(u.FirstName, \' \', u.LastName) ORDER BY u.FirstName SEPARATOR ", ")
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

    public function bookFacility(array $data): int|string|false {
        $playerId = (int)($data['player_id'] ?? 0);
        if ($playerId > 0 && $this->getWeeklyFacilityBookingCount($playerId) >= 3) {
            return 'weekly_facility_limit';
        }

        if ($playerId > 0 && $this->playerHasTimeConflict(
            $playerId,
            (string)($data['date'] ?? ''),
            (string)($data['start_time'] ?? '00:00:00'),
            (string)($data['end_time'] ?? '00:00:00')
        )) {
            return 'time_conflict';
        }

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
                    so.OccurrenceDate,
                    tb.StartTime,
                    COALESCE(st.SlotType, "program") AS SlotType
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             WHERE sb.BookingID = :bid'
        );
        $this->db->bind(':bid', $bookingId, PDO::PARAM_INT);
        $row = $this->db->single();

        if (!$row)                        return 'not_found';
        if ((int)$row->PlayerID !== $playerId) return 'forbidden';
        if ($row->Status === 'cancelled') return 'already_cancelled';

        $hoursUntil = (strtotime($row->OccurrenceDate . ' ' . ($row->StartTime ?? '00:00:00')) - time()) / 3600;
        $windowHours = ($this->getActivePlanName($playerId) === 'private') ? 48 : 24;
        if ($hoursUntil < $windowHours)   return 'window_closed';

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
     * Returns upcoming facility_only occurrences with optional filters.
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
            "st.SlotType   = 'facility_only'",
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

            if ($this->playerHasTimeConflict(
                $playerId,
                (string) $row->OccurrenceDate,
                (string) ($row->StartTime ?? '00:00:00'),
                (string) ($row->EndTime ?? '00:00:00')
            )) {
                $row->blocked     = true;
                $row->blockReason = 'time_conflict';
                continue;
            }

            if ($this->getWeeklyFacilityBookingCount($playerId) >= 3) {
                $row->blocked     = true;
                $row->blockReason = 'weekly_facility_limit';
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

    public function getFacilityOnlyBookingsForCounter(int $daysBack = 7, int $daysForward = 14): array {
        $daysBack = max(0, $daysBack);
        $daysForward = max(0, $daysForward);

        $this->db->query(
            'SELECT sb.BookingID, sb.Status, sb.BookingSource, sb.CreatedAt,
                    so.OccurrenceID, so.OccurrenceDate,
                    tb.SlotLabel, tb.StartTime, tb.EndTime,
                    st.TemplateName, st.SlotType,
                    f.FacilityID, f.Name AS FacilityName,
                    CONCAT(u.FirstName, \' \', u.LastName) AS PlayerName, u.Email AS PlayerEmail
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_template st ON st.TemplateID = so.TemplateID
             JOIN slot_time_band tb ON tb.SlotID = so.SlotID
             LEFT JOIN facility f ON f.FacilityID = so.FacilityID
             JOIN user u ON u.UserID = sb.PlayerID
             WHERE st.SlotType = \'facility_only\'
               AND so.Status != \'cancelled\'
               AND so.OccurrenceDate BETWEEN DATE_SUB(CURDATE(), INTERVAL :daysBack DAY)
                                         AND DATE_ADD(CURDATE(), INTERVAL :daysForward DAY)
             ORDER BY so.OccurrenceDate DESC, tb.StartTime DESC, sb.CreatedAt DESC'
        );
        $this->db->bind(':daysBack', $daysBack, PDO::PARAM_INT);
        $this->db->bind(':daysForward', $daysForward, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function updateFacilityBookingStatus(int $bookingId, string $status, int $shopEmployeeId): bool|string {
        $status = strtolower(trim($status));
        $statusMap = [
            'confirmed' => 'confirmed',
            'completed' => 'attended',
            'attended' => 'attended',
            'not_attended' => 'missed',
            'missed' => 'missed',
        ];

        if (!isset($statusMap[$status])) {
            return 'invalid_status';
        }

        $this->db->query(
            'SELECT sb.BookingID, sb.Status AS OldStatus, sb.OccurrenceID,
                    so.Status AS OccurrenceStatus,
                    st.SlotType
             FROM slot_booking sb
             JOIN slot_occurrence so ON so.OccurrenceID = sb.OccurrenceID
             JOIN slot_template st ON st.TemplateID = so.TemplateID
             WHERE sb.BookingID = :bid'
        );
        $this->db->bind(':bid', $bookingId, PDO::PARAM_INT);
        $row = $this->db->single();

        if (!$row) {
            return 'not_found';
        }

        if (($row->SlotType ?? '') !== 'facility_only') {
            return 'not_allowed';
        }

        if (($row->OccurrenceStatus ?? '') === 'cancelled' || ($row->OldStatus ?? '') === 'cancelled') {
            return 'locked';
        }

        $normalizedStatus = $statusMap[$status];

        $this->db->query(
            'UPDATE slot_booking
             SET Status = :status, UpdatedAt = NOW()
             WHERE BookingID = :bid'
        );
        $this->db->bind(':status', $normalizedStatus);
        $this->db->bind(':bid', $bookingId, PDO::PARAM_INT);
        $ok = $this->db->execute();

        if (!$ok) {
            return 'error';
        }

        $this->_auditLog('booking', $bookingId, 'update', 'Status', $row->OldStatus, $normalizedStatus,
            'Facility booking status updated by shop employee', $shopEmployeeId);
        $this->_activityLog($shopEmployeeId, 'update_facility_booking_status',
            "Updated facility booking #{$bookingId} status to {$normalizedStatus}");

        return true;
    }

    public function searchPlayers(string $term): array {
        $this->db->query(
            'SELECT UserID, CONCAT(FirstName, \' \', LastName) AS Name, Email, PhoneNumber
             FROM user
             WHERE Role = \'Player\'
               AND Status = \'active\'
               AND (CONCAT(FirstName, \' \', LastName) LIKE :t OR Email LIKE :t2 OR UserID = :id)
             LIMIT 20'
        );
        $like = '%' . $term . '%';
        $this->db->bind(':t',  $like);
        $this->db->bind(':t2', $like);
        $this->db->bind(':id', is_numeric($term) ? (int)$term : 0, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    private function _auditLog(
        string $entityType,
        int $entityId,
        string $action,
        ?string $changedField,
        ?string $oldValue,
        ?string $newValue,
        ?string $reason,
        int $userId
    ): void {
        $this->db->query(
            'INSERT INTO slot_audit_log
             (EntityType, EntityID, Action, ChangedField, OldValue, NewValue, Reason, ChangedBy, IPAddress)
             VALUES (:et, :eid, :act, :cf, :ov, :nv, :reason, :uid, :ip)'
        );
        $this->db->bind(':et', $entityType);
        $this->db->bind(':eid', $entityId, PDO::PARAM_INT);
        $this->db->bind(':act', $action);
        $this->db->bind(':cf', $changedField);
        $this->db->bind(':ov', $oldValue);
        $this->db->bind(':nv', $newValue);
        $this->db->bind(':reason', $reason);
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();
    }

    private function _activityLog(int $userId, string $action, string $description): void {
        $this->db->query(
            'INSERT INTO activitylog (UserID, Action, Description, IPAddress)
             VALUES (:uid, :action, :desc, :ip)'
        );
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':action', $action);
        $this->db->bind(':desc', $description);
        $this->db->bind(':ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->execute();
    }
}
