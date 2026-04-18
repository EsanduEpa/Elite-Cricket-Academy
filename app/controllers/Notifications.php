<?php

class Notifications extends Controller
{
    public function list()
    {
        $this->requireLoggedInJson();

        $userId = (int) $_SESSION['user_id'];
        $notificationModel = $this->model('M_Notification');
        $notifications = $notificationModel->getForUser($userId, 10);

        $this->json([
            'success' => true,
            'unread_count' => $notificationModel->countUnread($userId),
            'notifications' => array_map([$this, 'formatNotification'], $notifications),
        ]);
    }

    public function mark_read()
    {
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

    private function requireLoggedInJson(): void
    {
        if (!function_exists('isLoggedIn') || !isLoggedIn()) {
            $this->json([
                'success' => false,
                'message' => 'Please log in to view notifications.',
            ], 401);
        }
    }

    private function formatNotification(object $notification): array
    {
        $createdAt = $notification->CreatedAt ?? null;

        return [
            'id' => (int) ($notification->NotificationID ?? 0),
            'type' => (string) ($notification->Type ?? 'info'),
            'title' => (string) ($notification->Title ?? 'Notification'),
            'message' => (string) ($notification->Message ?? ''),
            'action_url' => (string) ($notification->ActionUrl ?? ''),
            'is_read' => (bool) ($notification->IsRead ?? false),
            'created_at' => $createdAt,
            'time' => $this->relativeTime($createdAt),
        ];
    }

    private function relativeTime(?string $createdAt): string
    {
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
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
