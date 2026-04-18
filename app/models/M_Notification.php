<?php
class M_Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create a notification for a user
     */
    public function create(int $userId, string $type, string $title, string $message, string $actionUrl = ''): int|false {
        $this->db->query('INSERT INTO notification (UserID, Type, Title, Message, ActionUrl, IsRead)
            VALUES (:uid, :type, :title, :msg, :url, 0)');
        $this->db->bind(':uid',   $userId,     PDO::PARAM_INT);
        $this->db->bind(':type',  $type,       PDO::PARAM_STR);
        $this->db->bind(':title', $title,      PDO::PARAM_STR);
        $this->db->bind(':msg',   $message,    PDO::PARAM_STR);
        $this->db->bind(':url',   $actionUrl,  PDO::PARAM_STR);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Create a notification once for a specific PayHere/order reference.
     */
    public function createOnceForOrder(
        int $userId,
        string $orderId,
        string $type,
        string $title,
        string $message,
        string $actionUrl = ''
    ): int|false {
        if ($userId <= 0 || $orderId === '') {
            return false;
        }

        if ($this->existsForOrder($userId, $orderId)) {
            return false;
        }

        $data = json_encode(['order_id' => $orderId], JSON_UNESCAPED_SLASHES);
        $this->db->query('INSERT INTO notification (UserID, Type, Title, Message, Data, ActionUrl, IsRead)
            VALUES (:uid, :type, :title, :msg, :data, :url, 0)');
        $this->db->bind(':uid',   $userId,    PDO::PARAM_INT);
        $this->db->bind(':type',  $type,      PDO::PARAM_STR);
        $this->db->bind(':title', $title,     PDO::PARAM_STR);
        $this->db->bind(':msg',   $message,   PDO::PARAM_STR);
        $this->db->bind(':data',  $data,      PDO::PARAM_STR);
        $this->db->bind(':url',   $actionUrl, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    public function existsForOrder(int $userId, string $orderId): bool {
        $this->db->query("SELECT NotificationID FROM notification
            WHERE UserID = :uid
              AND JSON_UNQUOTE(JSON_EXTRACT(Data, '$.order_id')) = :order_id
            LIMIT 1");
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':order_id', $orderId, PDO::PARAM_STR);
        return (bool)$this->db->single();
    }

    /**
     * Get all notifications for a user (newest first)
     */
    public function getForUser(int $userId, int $limit = 20): array {
        $this->db->query('SELECT * FROM notification WHERE UserID = :uid
            ORDER BY CreatedAt DESC LIMIT :lim');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':lim', $limit,  PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Count unread notifications for a user
     */
    public function countUnread(int $userId): int {
        $this->db->query('SELECT COUNT(*) as cnt FROM notification WHERE UserID = :uid AND IsRead = 0');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $result = $this->db->single();
        return (int)($result->cnt ?? 0);
    }

    /**
     * Mark a single notification as read
     */
    public function markRead(int $notificationId, int $userId): bool {
        $this->db->query('UPDATE notification SET IsRead = 1, ReadAt = NOW()
            WHERE NotificationID = :nid AND UserID = :uid');
        $this->db->bind(':nid', $notificationId, PDO::PARAM_INT);
        $this->db->bind(':uid', $userId,         PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllRead(int $userId): bool {
        $this->db->query('UPDATE notification SET IsRead = 1, ReadAt = NOW()
            WHERE UserID = :uid AND IsRead = 0');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }
}
