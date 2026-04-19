<?php

/**
 * Shared notification API controller.
 *
 * The navbar notification dropdown calls these methods through JavaScript.
 * All methods return JSON because the dropdown updates without reloading the page.
 *
 * Important viva point:
 * This controller does not decide when notifications are created. Other controllers
 * and services create them. This controller only reads/updates/deletes notifications
 * for the currently logged-in user.
 */
class Notifications extends Controller
{
    public function list()
    {
        // Return the latest notifications and unread count for the logged-in user.
        // Called by public/js/notifications.js when the bell dropdown opens/refreshes.
        $this->requireLoggedInJson();

        // UserID comes from the session, not from GET/POST, so users cannot request
        // another user's notifications by changing a browser parameter.
        $userId = (int) $_SESSION['user_id'];
        $notificationModel = $this->model('M_Notification');
        $notifications = $notificationModel->getForUser($userId, 10);

        // The model returns database objects. formatNotification() converts them
        // into frontend-friendly arrays before json_encode().
        $this->json([
            'success' => true,
            'unread_count' => $notificationModel->countUnread($userId),
            'notifications' => array_map([$this, 'formatNotification'], $notifications),
        ]);
    }

    public function mark_read()
    {
        // If notification_id is passed, mark one item read.
        // If not, mark all notifications read for this user.
        // This supports both "click one notification" and "mark all read" UI actions.
        $this->requireLoggedInJson();

        $userId = (int) $_SESSION['user_id'];
        $notificationId = (int) ($_POST['notification_id'] ?? 0);
        $notificationModel = $this->model('M_Notification');

        if ($notificationId > 0) {
            $notificationModel->markRead($notificationId, $userId);
        } else {
            $notificationModel->markAllRead($userId);
        }

        $this->json([
            'success' => true,
            'unread_count' => $notificationModel->countUnread($userId),
        ]);
    }

    public function delete()
    {
        // Delete only when the selected notification belongs to the logged-in user.
        // The model also checks UserID in the DELETE query as an extra safety layer.
        $this->requireLoggedInJson();

        $notificationId = (int) ($_POST['notification_id'] ?? 0);
        if ($notificationId <= 0) {
            $this->json([
                'success' => false,
                'message' => 'Invalid notification selected.',
            ], 400);
        }

        $userId = (int) $_SESSION['user_id'];
        $notificationModel = $this->model('M_Notification');
        $notificationModel->deleteForUser($notificationId, $userId);

        $this->json([
            'success' => true,
            'unread_count' => $notificationModel->countUnread($userId),
        ]);
    }

    public function clear()
    {
        // Remove all notification rows for the current user.
        // Useful when the dropdown has a "clear all" action.
        $this->requireLoggedInJson();

        $userId = (int) $_SESSION['user_id'];
        $notificationModel = $this->model('M_Notification');
        $notificationModel->clearForUser($userId);

        $this->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    private function requireLoggedInJson(): void
    {
        // This endpoint is used by fetch(), so it sends JSON errors instead of redirecting.
        // Without this, an expired session could return a full HTML page to JavaScript.
        if (!function_exists('isLoggedIn') || !isLoggedIn()) {
            $this->json([
                'success' => false,
                'message' => 'Please log in to view notifications.',
            ], 401);
        }
    }

    private function formatNotification(object $notification): array
    {
        // Convert the database object into a safe, predictable structure for JavaScript.
        // Casting values protects the frontend from nulls/unexpected database types.
        $createdAt = $notification->CreatedAt ?? null;

        return [
            'id' => (int) ($notification->NotificationID ?? 0),
            'type' => (string) ($notification->Type ?? 'info'),
            'title' => (string) ($notification->Title ?? 'Notification'),
            'message' => (string) ($notification->Message ?? ''),
            'action_url' => (string) ($notification->ActionUrl ?? ''),
            'is_read' => (bool) ($notification->IsRead ?? false),
            'created_at' => $createdAt,
            // "time" is a display label; "created_at" is kept for exact timestamp use.
            'time' => $this->relativeTime($createdAt),
        ];
    }

    private function relativeTime(?string $createdAt): string
    {
        // Convert database timestamps into friendly labels such as "5 min ago".
        // This keeps display formatting on the server so the dropdown JS stays simple.
        if (empty($createdAt)) {
            return 'Just now';
        }

        try {
            $created = new DateTime($createdAt);
            $now = new DateTime();
        } catch (Exception $e) {
            return 'Recently';
        }

        $seconds = max(0, $now->getTimestamp() - $created->getTimestamp());
        if ($seconds < 60) {
            return 'Just now';
        }

        $minutes = (int) floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' min ago';
        }

        $hours = (int) floor($minutes / 60);
        if ($hours < 24) {
            return $hours . ' hr ago';
        }

        $days = (int) floor($hours / 24);
        if ($days < 7) {
            return $days . ' day' . ($days === 1 ? '' : 's') . ' ago';
        }

        return $created->format('M j, Y');
    }

    private function json(array $payload, int $statusCode = 200): void
    {
        // Small helper to keep every API response consistent.
        // exit is important so no extra PHP/HTML output is appended after JSON.
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
