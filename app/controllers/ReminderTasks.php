<?php

/**
 * Browser heartbeat controller for reminder jobs.
 *
 * This project cannot rely on cron on every machine, so public/js/reminder-heartbeat.js
 * calls this endpoint hourly while a logged-in user is using the system.
 *
 * Viva explanation:
 * Normally, reminder emails/notifications should be sent by a server cron job.
 * For this demo, the browser acts like a "heartbeat" trigger. When someone is
 * using the app, JavaScript calls this controller, and this controller asks the
 * reminder services to send only reminders that are due.
 */
class ReminderTasks extends Controller
{
    public function player_session_reminders()
    {
        // The frontend expects JSON, not a rendered PHP view.
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            // No logged-in session means there is no safe user context for this request.
            // Also avoids running background work for anonymous visitors.
            echo json_encode([
                'success' => false,
                'message' => 'Reminder heartbeat skipped because no user is logged in.',
            ]);
            exit;
        }

        // These files are loaded here because this controller is only a thin trigger.
        // The actual business rules are inside the service classes.
        require_once APPROOT . '/models/M_SlotPlayer.php';
        require_once APPROOT . '/models/M_Email.php';
        require_once APPROOT . '/libraries/PlayerSessionReminderService.php';
        require_once APPROOT . '/libraries/TournamentNotificationService.php';

        try {
            // false means "do not force"; each service checks whether reminders are due
            // and avoids duplicate emails/notifications using database logs/keys.
            // PlayerSessionReminderService handles session reminders before sessions.
            $summary = PlayerSessionReminderService::sendDueReminders(false);

            // TournamentNotificationService handles tournament reminders and selected-player alerts.
            $tournamentSummary = TournamentNotificationService::sendDueReminders(false);
            echo json_encode([
                'success' => true,
                'summary' => $summary,
                'tournament_summary' => $tournamentSummary,
            ]);
        } catch (Throwable $e) {
            // Reminder failures are logged but should not crash the whole page.
            error_log('Reminder heartbeat failed: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Reminder heartbeat failed. Check PHP error log.',
            ]);
        }

        exit;
    }
}
