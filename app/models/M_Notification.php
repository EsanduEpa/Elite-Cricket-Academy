<?php
/**
 * Notification model.
 *
 * Stores in-app notifications in the notification table. Controllers and services
 * use this model to create alerts for payments, bookings, tournaments, stock, etc.
 *
 * In MVC terms:
 * - Controllers decide WHEN a notification should be created.
 * - This model decides HOW notification data is saved/read from MySQL.
 * - public/js/notifications.js later calls Notifications controller to display these rows.
 */
class M_Notification {
    private $db;

    public function __construct() {
        // Every model creates its own Database wrapper so it can run prepared SQL queries.
        $this->db = new Database();
    }

    /**
     * Create a notification for one user.
     *
     * Inputs:
     * - $userId: the receiver's UserID from the user table.
     * - $type: category used by the frontend to choose icon/color (payment, session, stock, etc.).
     * - $title/$message: text shown inside the dropdown.
     * - $actionUrl: optional link opened when the user clicks the notification.
     *
     * Output: inserted NotificationID, or false if insert fails.
     */
    public function create(int $userId, string $type, string $title, string $message, string $actionUrl = ''): int|false {
        // New notifications start unread with IsRead = 0.
        $this->db->query('INSERT INTO notification (UserID, Type, Title, Message, ActionUrl, IsRead)
            VALUES (:uid, :type, :title, :msg, :url, 0)');

        // Values are bound separately from SQL to prevent SQL injection.
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
     * The order_id is stored in JSON Data so repeated PayHere callbacks
     * do not create duplicate notifications.
     *
     * This method is also reused for non-payment unique events by passing a unique key,
     * for example "slot-booked-12-55" for player 12 and occurrence 55.
     */
    public function createOnceForOrder(
        int $userId,
        string $orderId,
        string $type,
        string $title,
        string $message,
        string $actionUrl = ''
    ): int|false {
        // Invalid user IDs or empty keys cannot be used for duplicate detection.
        if ($userId <= 0 || $orderId === '') {
            return false;
        }

        // Stop here if a notification with this same unique key already exists.
        if ($this->existsForOrder($userId, $orderId)) {
            return false;
        }

        // Data stores extra metadata that is not shown directly but is useful for lookup.
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
        // JSON_EXTRACT reads Data->order_id from the notification row.
        // This query answers: "Has this user already received this exact event?"
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
        // Used when one event should notify several known users.
        // Example: when a player books a session, notify every staff member attached to that slot.
        $created = 0;

        // array_unique avoids sending two notifications to the same user if duplicate IDs are passed.
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
        // Used for admin/shop/staff-wide alerts without hardcoding user IDs.
        // Example: low stock alert should go to all users with Shop or ShopEmployee role.
        $userIds = $this->getUserIdsByRoles($roles);
        return $this->createOnceForUsers($userIds, $key, $type, $title, $message, $actionUrl);
    }

    public function deleteForUser(int $notificationId, int $userId): bool {
        // UserID condition prevents one user from deleting another user's notification.
        // This is an ownership check at database level.
        $this->db->query('DELETE FROM notification WHERE NotificationID = :id AND UserID = :uid');
        $this->db->bind(':id', $notificationId, PDO::PARAM_INT);
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function clearForUser(int $userId): bool {
        // Clear all notifications for the current dropdown user.
        // It does not affect other users because the WHERE clause filters by UserID.
        $this->db->query('DELETE FROM notification WHERE UserID = :uid');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function getUserIdsByRoles(array $roles): array {
        // Build dynamic placeholders safely for the SQL IN (...) role list.
        // We cannot bind an array directly to PDO, so each role gets its own placeholder.
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
            // Example placeholders: :role_0 = Admin, :role_1 = Coach.
            $this->db->bind(':role_' . $index, $role, PDO::PARAM_STR);
        }

        // Convert database row objects into a simple integer array.
        return array_map(static function ($row) {
            return (int)($row->UserID ?? 0);
        }, $this->db->resultSet());
    }

    /**
     * Get notifications for the dropdown.
     *
     * The navbar only needs a small recent list, so $limit keeps the query light.
     * Newest notifications appear first because users expect recent alerts at the top.
     */
    public function getForUser(int $userId, int $limit = 20): array {
        $this->db->query('SELECT * FROM notification WHERE UserID = :uid
            ORDER BY CreatedAt DESC LIMIT :lim');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':lim', $limit,  PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Count unread notifications for the red badge near the bell icon.
     */
    public function countUnread(int $userId): int {
        $this->db->query('SELECT COUNT(*) as cnt FROM notification WHERE UserID = :uid AND IsRead = 0');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $result = $this->db->single();
        return (int)($result->cnt ?? 0);
    }

    /**
     * Mark a single notification as read.
     *
     * ReadAt stores the time the user saw/cleared the item.
     */
    public function markRead(int $notificationId, int $userId): bool {
        $this->db->query('UPDATE notification SET IsRead = 1, ReadAt = NOW()
            WHERE NotificationID = :nid AND UserID = :uid');
        $this->db->bind(':nid', $notificationId, PDO::PARAM_INT);
        $this->db->bind(':uid', $userId,         PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Mark all notifications as read for a user.
     *
     * Used when the user clicks "mark all read" or when the dropdown wants
     * to remove the unread badge after the user has seen all items.
     */
    public function markAllRead(int $userId): bool {
        $this->db->query('UPDATE notification SET IsRead = 1, ReadAt = NOW()
            WHERE UserID = :uid AND IsRead = 0');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }
}
