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

    public function createOnceForUsers(
        array $userIds,
        string $key,
        string $type,
        string $title,
        string $message,
        string $actionUrl = ''
    ): int {
        $created = 0;
        foreach (array_unique(array_map('intval', $userIds)) as $userId) {
            if ($userId <= 0) {
                continue;
            }

            if ($this->createOnceForOrder($userId, $key, $type, $title, $message, $actionUrl) !== false) {
                $created++;
            }
        }

        return $created;
    }

    public function createOnceForRoles(
        array $roles,
        string $key,
        string $type,
        string $title,
        string $message,
        string $actionUrl = ''
    ): int {
        $userIds = $this->getUserIdsByRoles($roles);
        return $this->createOnceForUsers($userIds, $key, $type, $title, $message, $actionUrl);
    }

    public function deleteForUser(int $notificationId, int $userId): bool {
        $this->db->query('DELETE FROM notification WHERE NotificationID = :id AND UserID = :uid');
        $this->db->bind(':id', $notificationId, PDO::PARAM_INT);
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function clearForUser(int $userId): bool {
        $this->db->query('DELETE FROM notification WHERE UserID = :uid');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function getUserIdsByRoles(array $roles): array {
        $roles = array_values(array_unique(array_filter(array_map('strval', $roles))));
        if (!$roles) {
            return [];
        }

        $placeholders = [];
        foreach ($roles as $index => $role) {
            $placeholders[] = ':role_' . $index;
        }

        $this->db->query('SELECT UserID FROM user WHERE Role IN (' . implode(',', $placeholders) . ')');
        foreach ($roles as $index => $role) {
            $this->db->bind(':role_' . $index, $role, PDO::PARAM_STR);
        }

        return array_map(static function ($row) {
            return (int)($row->UserID ?? 0);
        }, $this->db->resultSet());
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
