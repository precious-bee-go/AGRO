<?php
namespace Models;

use Core\Model;

class Notification extends Model
{
    /**
     * Get unread notifications for a user
     */
    public function getUnread($userId)
    {
        $sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
        return $this->query($sql, [$userId], "i");
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead($userId)
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
        return $this->query($sql, [$userId], "i");
    }

    /**
     * Create notification
     */
    public function create($userId, $title, $message, $type = 'system')
    {
        $sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)";
        return $this->query($sql, [$userId, $title, $message, $type], "isss");
    }
}
?>