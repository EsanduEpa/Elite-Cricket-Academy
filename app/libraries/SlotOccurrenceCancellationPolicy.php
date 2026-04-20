<?php

class SlotOccurrenceCancellationPolicy {
    /**
     * Mirrors the admin occurrence cancellation rules.
     *
     * Returns:
     * - true if cancellable
     * - string error message otherwise
     */
    public static function canCancel(object $occurrence): bool|string {
        $date = trim((string) ($occurrence->OccurrenceDate ?? ''));
        $startTime = trim((string) ($occurrence->StartTime ?? ''));

        if ($date === '' || $startTime === '') {
            return 'Cannot cancel: occurrence time is missing.';
        }

        try {
            $occDateTime = new DateTime($date . ' ' . $startTime, new DateTimeZone('UTC'));
            $now = new DateTime('now', new DateTimeZone('UTC'));
        } catch (Throwable $e) {
            return 'Cannot cancel: invalid occurrence time.';
        }

        $timeUntilOcc = $now->diff($occDateTime);
        $slotType = strtolower((string) ($occurrence->SlotType ?? ''));

        // Check 48-hour rule for program sessions
        if ($slotType === 'program') {
            if ($timeUntilOcc->invert === 1) {
                return 'Cannot cancel a past occurrence.';
            }

            $hoursRemaining = $timeUntilOcc->h + ($timeUntilOcc->days * 24);
            if ($hoursRemaining < 48) {
                return 'Cannot cancel within 48 hours of the scheduled time.';
            }
        } elseif ($slotType === 'private' || $slotType === 'facility_only') {
            // For private or facility-only, can cancel only if no bookings
            if ((int) ($occurrence->BookingCount ?? 0) > 0) {
                return 'Cannot cancel: this occurrence has bookings. Contact affected players first.';
            }
        }

        return true;
    }
}
