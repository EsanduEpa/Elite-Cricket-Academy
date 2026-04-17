<?php

class ReminderTasks extends Controller
{
    public function player_session_reminders()
    {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode([
                'success' => false,
                'message' => 'Reminder heartbeat skipped because no user is logged in.',
            ]);
            exit;
        }

        require_once APPROOT . '/models/M_SlotPlayer.php';
        require_once APPROOT . '/models/M_Email.php';
        require_once APPROOT . '/libraries/PlayerSessionReminderService.php';

        try {
            $summary = PlayerSessionReminderService::sendDueReminders(false);
            echo json_encode([
                'success' => true,
                'summary' => $summary,
            ]);
        } catch (Throwable $e) {
            error_log('Reminder heartbeat failed: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Reminder heartbeat failed. Check PHP error log.',
            ]);
        }

        exit;
    }
}
