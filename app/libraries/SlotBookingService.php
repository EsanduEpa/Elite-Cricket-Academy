<?php
/**
 * SlotBookingService
 *
 * Gate-checks run before any booking is created or confirmed.
 * All methods are static and instantiate Database internally.
 * They return an associative array: ['ok' => bool, ...]
 */
class SlotBookingService {

    // =========================================================
    // ENTITLEMENT CHECK — Does the player's plan cover this slot?
    // =========================================================

    /**
     * Returns ['ok' => true, 'subscription_id' => N]
     *      or ['ok' => false, 'code' => '...', 'message' => '...']
     *
     * Codes: no_subscription | plan_mismatch
     */
    public static function validateEntitlement(int $playerId, int $templateId): array {
        $db = new Database();

        // 1. Load the template's required plan feature
        $db->query('SELECT RequiredPlanFeature FROM slot_template WHERE TemplateID = :tid');
        $db->bind(':tid', $templateId, PDO::PARAM_INT);
        $template = $db->single();

        if (!$template) {
            return ['ok' => false, 'code' => 'template_not_found', 'message' => 'Session template not found.'];
        }

        $requiredPlan = trim((string)($template->RequiredPlanFeature ?? ''));

        // 2. Legacy open access templates remain bookable until they are updated.
        if ($requiredPlan === '' || $requiredPlan === 'none') {
            return ['ok' => true, 'subscription_id' => null];
        }

        // 3. Find player's active subscription
        $db->query(
            'SELECT ps.SubscriptionID, ps.PlanID,
                    mp.SessionsPerWeek, mp.PrivateSessionsIncluded, mp.FacilityAccessIncluded
             FROM playersubscription ps
             JOIN membershipplan mp ON mp.PlanID = ps.PlanID
             WHERE ps.PlayerID = :pid
               AND ps.Status   = \'active\'
             ORDER BY ps.SubscriptionID DESC
             LIMIT 1'
        );
        $db->bind(':pid', $playerId, PDO::PARAM_INT);
        $sub = $db->single();

        if (!$sub) {
            return [
                'ok'      => false,
                'code'    => 'no_subscription',
                'message' => 'You do not have an active subscription. Please subscribe to a plan to book sessions.',
            ];
        }

        // 4. New templates require an exact membership plan. Older templates may
        // still carry feature-based legacy values until they are edited.
        if (preg_match('/^plan:(\d+)$/', $requiredPlan, $matches) === 1) {
            $requiredPlanId = (int)$matches[1];

            if ((int)$sub->PlanID !== $requiredPlanId) {
                $db->query('SELECT PlanName FROM membershipplan WHERE PlanID = :plan_id LIMIT 1');
                $db->bind(':plan_id', $requiredPlanId, PDO::PARAM_INT);
                $plan = $db->single();

                return [
                    'ok'      => false,
                    'code'    => 'plan_mismatch',
                    'message' => 'This session requires the ' . ($plan->PlanName ?? 'selected membership plan') . ' plan. Please switch or upgrade your subscription.',
                ];
            }

            return ['ok' => true, 'subscription_id' => (int) $sub->SubscriptionID];
        }

        $feature = $requiredPlan;
        $covered = false;

        if ($feature === 'sessions'          && $sub->SessionsPerWeek         > 0) $covered = true;
        if ($feature === 'private_sessions'  && $sub->PrivateSessionsIncluded > 0) $covered = true;
        if ($feature === 'facility_access'   && $sub->FacilityAccessIncluded  == 1) $covered = true;

        if (!$covered) {
            $labels = [
                'sessions'         => 'group sessions',
                'private_sessions' => 'private sessions',
                'facility_access'  => 'facility access',
            ];
            return [
                'ok'      => false,
                'code'    => 'plan_mismatch',
                'message' => 'Your current plan does not include ' . ($labels[$feature] ?? $feature) . '. Please upgrade your subscription.',
            ];
        }

        return ['ok' => true, 'subscription_id' => (int) $sub->SubscriptionID];
    }

    // =========================================================
    // MEDICAL FLAG — Is the player cleared to train?
    // =========================================================

    /**
     * Returns ['ok' => true]
     *      or ['ok' => false, 'code' => 'active_injury', 'rest_days' => N,
     *                         'injury_note' => '...', 'message' => '...']
     */
    public static function checkMedicalFlag(int $playerId, string $occurrenceDate): array {
        $db = new Database();

        // Find any active injury whose rest window covers occurrenceDate
        $db->query(
            'SELECT RecordID, InjuryDetails, RestDaysNeeded,
                    DATE_ADD(InjuryDate, INTERVAL RestDaysNeeded DAY) AS ClearDate
             FROM playermedicalrecord
             WHERE PlayerID        = :pid
               AND RecoveryStatus IN (\'ongoing\', \'recovering\')
               AND DATE_ADD(InjuryDate, INTERVAL RestDaysNeeded DAY) >= :odate
             ORDER BY ClearDate DESC
             LIMIT 1'
        );
        $db->bind(':pid',   $playerId,      PDO::PARAM_INT);
        $db->bind(':odate', $occurrenceDate);
        $record = $db->single();

        if (!$record) {
            return ['ok' => true];
        }

        $restDaysLeft = (int) ceil(
            (strtotime($record->ClearDate) - strtotime($occurrenceDate)) / 86400
        );

        return [
            'ok'          => false,
            'code'        => 'active_injury',
            'rest_days'   => max(0, $restDaysLeft),
            'injury_note' => $record->InjuryDetails,
            'message'     => 'This player has an active injury and requires ' . max(0, $restDaysLeft) . ' more rest day(s) before training.',
        ];
    }

    // =========================================================
    // CAPACITY CHECK — Is there room in the occurrence?
    // =========================================================

    /**
     * For facility_only and private occurrences, one confirmed booking reserves the slot.
     * MaxParticipants represents the group size allowed within that single booking,
     * not the number of separate bookings allowed for the same occurrence.
     *
     * Returns ['ok' => true,  'spots_left' => N, 'group_capacity' => N|null]
     *      or ['ok' => false, 'code' => 'full', 'message' => '...']
     */
    public static function checkCapacity(int $occurrenceId): array {
        $db = new Database();

        $db->query(
            'SELECT so.MaxParticipants  AS OccMax,
                    st.MaxParticipants  AS TplMax,
                    st.SlotType,
                    COUNT(sb.BookingID) AS Booked
             FROM slot_occurrence so
             LEFT JOIN slot_template st ON st.TemplateID = so.TemplateID
             LEFT JOIN slot_booking  sb ON sb.OccurrenceID = so.OccurrenceID
                    AND sb.Status NOT IN (\'cancelled\')
             WHERE so.OccurrenceID = :oid
             GROUP BY so.OccurrenceID, so.MaxParticipants, st.MaxParticipants, st.SlotType'
        );
        $db->bind(':oid', $occurrenceId, PDO::PARAM_INT);
        $row = $db->single();

        if (!$row) {
            return ['ok' => false, 'code' => 'not_found', 'message' => 'Occurrence not found.'];
        }

        // Capacity is only enforced for facility_only and private slot types
        if (!in_array($row->SlotType, ['facility_only', 'private'], true)) {
            return ['ok' => true, 'spots_left' => null];
        }

        // Occurrence-level override takes priority; fall back to template value.
        // This value is the allowed headcount inside a single reservation.
        $max    = $row->OccMax ?? $row->TplMax ?? null;
        $booked = (int) $row->Booked;

        if ($booked >= 1) {
            return [
                'ok'      => false,
                'code'    => 'full',
                'message' => 'This slot is already reserved.',
            ];
        }

        return [
            'ok'             => true,
            'spots_left'     => 1,
            'group_capacity' => $max !== null ? (int) $max : null,
        ];
    }
}
