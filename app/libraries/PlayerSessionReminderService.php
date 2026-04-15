<?php

require_once APPROOT . '/libraries/Mailer.php';

class PlayerSessionReminderService
{
    public static function sendDueReminders(bool $dryRun = false): array
    {
        $slotModel = new M_SlotPlayer();
        $emailModel = new M_Email();
        $bookings = $slotModel->getPlayerBookingsDueForReminder(120, 180);

        $summary = [
            'found' => count($bookings),
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
            'dry_run' => $dryRun,
            'items' => [],
        ];

        foreach ($bookings as $booking) {
            $bookingId = (int)($booking->BookingID ?? 0);
            $playerId = (int)($booking->PlayerID ?? 0);
            $recipientEmail = trim((string)($booking->PlayerEmail ?? ''));
            $playerName = trim((string)($booking->PlayerName ?? 'Player'));
            $playerName = $playerName !== '' ? $playerName : 'Player';

            if ($bookingId <= 0 || $playerId <= 0) {
                $summary['skipped']++;
                $summary['items'][] = ['booking_id' => $bookingId, 'status' => 'skipped', 'reason' => 'invalid_row'];
                continue;
            }

            if ($emailModel->hasSessionReminderBeenSent($bookingId)) {
                $summary['skipped']++;
                $summary['items'][] = ['booking_id' => $bookingId, 'status' => 'skipped', 'reason' => 'already_sent'];
                continue;
            }

            $subject = "Session Reminder - Booking #{$bookingId}";

            if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                $summary['failed']++;
                $emailModel->logSessionReminder(
                    $playerId,
                    $recipientEmail,
                    $bookingId,
                    $subject,
                    false,
                    'Skipped because player email is missing or invalid.'
                );
                $summary['items'][] = ['booking_id' => $bookingId, 'status' => 'failed', 'reason' => 'invalid_email'];
                continue;
            }

            $htmlBody = self::buildReminderEmail($booking, $playerName);

            if ($dryRun) {
                $summary['skipped']++;
                $summary['items'][] = ['booking_id' => $bookingId, 'status' => 'dry_run'];
                continue;
            }

            try {
                $sent = Mailer::send($recipientEmail, $subject, $htmlBody, $playerName);
            } catch (Throwable $e) {
                error_log('Player session reminder unexpected failure: ' . $e->getMessage());
                $sent = false;
            }

            $emailModel->logSessionReminder(
                $playerId,
                $recipientEmail,
                $bookingId,
                $subject,
                $sent,
                $sent ? null : 'SMTP send failed or recipient mailbox was unavailable. Check PHP error log for Mailer details.'
            );

            if ($sent) {
                $summary['sent']++;
                $summary['items'][] = ['booking_id' => $bookingId, 'status' => 'sent'];
            } else {
                $summary['failed']++;
                $summary['items'][] = ['booking_id' => $bookingId, 'status' => 'failed', 'reason' => 'smtp_failed'];
            }
        }

        return $summary;
    }

    private static function buildReminderEmail(object $booking, string $playerName): string
    {
        $sessionDate = (string)($booking->OccurrenceDate ?? '');
        $startTime = (string)($booking->StartTime ?? '');
        $endTime = (string)($booking->EndTime ?? '');
        $sessionName = (string)($booking->TemplateName ?? 'Session');
        $facilityName = (string)($booking->FacilityName ?? 'Academy');
        $slotLabel = (string)($booking->SlotLabel ?? '');
        $staffNames = trim((string)($booking->StaffNames ?? ''));

        $displayDate = $sessionDate ? date('D, d M Y', strtotime($sessionDate)) : 'Today';
        $displayStart = $startTime ? date('g:i A', strtotime($startTime)) : '';
        $displayEnd = $endTime ? date('g:i A', strtotime($endTime)) : '';

        $safeName = htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8');
        $safeBooking = htmlspecialchars((string)($booking->BookingID ?? ''), ENT_QUOTES, 'UTF-8');
        $safeSession = htmlspecialchars($sessionName, ENT_QUOTES, 'UTF-8');
        $safeFacility = htmlspecialchars($facilityName, ENT_QUOTES, 'UTF-8');
        $safeDate = htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8');
        $safeTime = htmlspecialchars(trim($displayStart . ' - ' . $displayEnd, ' -'), ENT_QUOTES, 'UTF-8');
        $safeSlot = htmlspecialchars($slotLabel, ENT_QUOTES, 'UTF-8');
        $safeStaff = htmlspecialchars($staffNames !== '' ? $staffNames : 'Academy staff', ENT_QUOTES, 'UTF-8');

        return '
            <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color: #0f766e; margin-bottom: 8px;">Session Reminder</h2>
                <p>Hi ' . $safeName . ',</p>
                <p>This is a reminder that your Elite Cricket Academy session starts in about 3 hours.</p>
                <table style="border-collapse: collapse; margin: 16px 0; width: 100%; max-width: 560px;">
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Booking</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">#' . $safeBooking . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Session</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeSession . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Date</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeDate . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Time</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeTime . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Slot</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeSlot . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Facility</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeFacility . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Staff</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeStaff . '</td>
                    </tr>
                </table>
                <p>Please arrive 10 minutes early.</p>
                <p style="margin-top: 24px;">Regards,<br>Elite Cricket Academy</p>
            </div>';
    }
}
