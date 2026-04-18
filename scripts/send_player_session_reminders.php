<?php
/**
 * Sends player reminder emails for slot bookings starting in about 2-3 hours.
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
require_once APPROOT . '/models/M_SlotPlayer.php';
require_once APPROOT . '/models/M_Email.php';
require_once APPROOT . '/libraries/PlayerSessionReminderService.php';
require_once APPROOT . '/libraries/TournamentNotificationService.php';

$dryRun = in_array('--dry-run', $argv, true);
$summary = PlayerSessionReminderService::sendDueReminders($dryRun);
$tournamentSummary = TournamentNotificationService::sendDueReminders($dryRun);

echo 'Player session reminder scan started at ' . date('Y-m-d H:i:s') . "\n";
echo 'Mode: ' . ($dryRun ? 'dry-run' : 'send') . "\n";
echo 'Bookings found: ' . $summary['found'] . "\n";

foreach ($summary['items'] as $item) {
    $bookingId = $item['booking_id'] ?? 0;
    $status = strtoupper((string)($item['status'] ?? 'unknown'));
    $reason = isset($item['reason']) ? ' (' . $item['reason'] . ')' : '';
    echo "{$status}: Booking #{$bookingId}{$reason}\n";
}

echo "Summary: sent={$summary['sent']}, skipped={$summary['skipped']}, failed={$summary['failed']}\n";
echo 'Tournament reminder tournaments found: ' . $tournamentSummary['found'] . "\n";
echo "Tournament summary: sent={$tournamentSummary['sent']}, skipped={$tournamentSummary['skipped']}, failed={$tournamentSummary['failed']}\n";
exit(($summary['failed'] > 0 || $tournamentSummary['failed'] > 0) ? 1 : 0);
