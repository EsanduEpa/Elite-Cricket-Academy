<?php
/**
 * Sends player reminder emails for slot bookings starting in about 3 hours.
 *
 * Usage:
 *   php scripts/send_player_session_reminders.php
 *   php scripts/send_player_session_reminders.php --dry-run
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require_once dirname(__DIR__) . '/app/bootloader.php';
require_once APPROOT . '/libraries/Mailer.php';
require_once APPROOT . '/models/M_SlotPlayer.php';
require_once APPROOT . '/models/M_Email.php';

$dryRun = in_array('--dry-run', $argv, true);
$slotModel = new M_SlotPlayer();
$emailModel = new M_Email();
$bookings = $slotModel->getPlayerBookingsDueForReminder(175, 185);

echo 'Player session reminder scan started at ' . date('Y-m-d H:i:s') . "\n";
echo 'Mode: ' . ($dryRun ? 'dry-run' : 'send') . "\n";
echo 'Bookings found: ' . count($bookings) . "\n";

$sentCount = 0;
$skippedCount = 0;
$failedCount = 0;

foreach ($bookings as $booking) {
    $bookingId = (int)($booking->BookingID ?? 0);
    $playerId = (int)($booking->PlayerID ?? 0);
    $recipientEmail = trim((string)($booking->PlayerEmail ?? ''));
    $playerName = trim((string)($booking->PlayerName ?? 'Player'));
    $playerName = $playerName !== '' ? $playerName : 'Player';

    if ($bookingId <= 0 || $playerId <= 0) {
        $skippedCount++;
        echo "SKIP: Invalid booking row.\n";
        continue;
    }

    if ($emailModel->hasSessionReminderBeenSent($bookingId)) {
        $skippedCount++;
        echo "SKIP: Booking #{$bookingId} already has a sent reminder.\n";
        continue;
    }

    $subject = "Session Reminder - Booking #{$bookingId}";

    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        $failedCount++;
        echo "FAIL: Booking #{$bookingId} has invalid player email: {$recipientEmail}\n";
        $emailModel->logSessionReminder(
            $playerId,
            $recipientEmail,
            $bookingId,
            $subject,
            false,
            'Skipped because player email is missing or invalid.'
        );
        continue;
    }

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
    $safeBooking = htmlspecialchars((string)$bookingId, ENT_QUOTES, 'UTF-8');
    $safeSession = htmlspecialchars($sessionName, ENT_QUOTES, 'UTF-8');
    $safeFacility = htmlspecialchars($facilityName, ENT_QUOTES, 'UTF-8');
    $safeDate = htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8');
    $safeTime = htmlspecialchars(trim($displayStart . ' - ' . $displayEnd, ' -'), ENT_QUOTES, 'UTF-8');
    $safeSlot = htmlspecialchars($slotLabel, ENT_QUOTES, 'UTF-8');
    $safeStaff = htmlspecialchars($staffNames !== '' ? $staffNames : 'Academy staff', ENT_QUOTES, 'UTF-8');

    $htmlBody = '
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

    echo "BOOKING #{$bookingId}: {$recipientEmail} {$displayDate} {$displayStart}\n";

    if ($dryRun) {
        $skippedCount++;
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
        $sentCount++;
        echo "SENT: Booking #{$bookingId}\n";
    } else {
        $failedCount++;
        echo "FAIL: Booking #{$bookingId}\n";
    }
}

echo "Summary: sent={$sentCount}, skipped={$skippedCount}, failed={$failedCount}\n";
exit($failedCount > 0 ? 1 : 0);
