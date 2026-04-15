<?php

class M_Email
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function hasPaymentConfirmationBeenSent(string $orderId): bool
    {
        try {
            $this->db->query("SELECT EmailID FROM emaillog
                WHERE EmailType = 'notification'
                  AND Subject LIKE :subject
                  AND Status = 'sent'
                LIMIT 1");
            $this->db->bind(':subject', '%' . $orderId . '%');
            return (bool) $this->db->single();
        } catch (Exception $e) {
            error_log('Email log duplicate check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function logPaymentConfirmation(
        int $userId,
        string $recipientEmail,
        string $subject,
        bool $sent,
        ?string $errorMessage = null
    ): bool {
        try {
            $this->db->query("INSERT INTO emaillog
                (UserID, RecipientEmail, Subject, EmailType, Status, SentAt, ErrorMessage)
                VALUES (:user_id, :recipient_email, :subject, 'notification', :status, :sent_at, :error_message)");
            $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
            $this->db->bind(':recipient_email', $recipientEmail);
            $this->db->bind(':subject', $subject);
            $this->db->bind(':status', $sent ? 'sent' : 'failed');
            $this->db->bind(':sent_at', $sent ? date('Y-m-d H:i:s') : null);
            $this->db->bind(':error_message', $errorMessage);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Email log insert failed: ' . $e->getMessage());
            return false;
        }
    }

    public function hasSessionReminderBeenSent(int $bookingId): bool
    {
        try {
            $this->db->query("SELECT EmailID FROM emaillog
                WHERE EmailType = 'notification'
                  AND Subject LIKE :subject
                  AND Status = 'sent'
                LIMIT 1");
            $this->db->bind(':subject', '%Booking #' . $bookingId . '%');
            return (bool) $this->db->single();
        } catch (Exception $e) {
            error_log('Session reminder duplicate check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function logSessionReminder(
        int $userId,
        string $recipientEmail,
        int $bookingId,
        string $subject,
        bool $sent,
        ?string $errorMessage = null
    ): bool {
        try {
            $this->db->query("INSERT INTO emaillog
                (UserID, RecipientEmail, Subject, EmailType, Status, SentAt, ErrorMessage)
                VALUES (:user_id, :recipient_email, :subject, 'notification', :status, :sent_at, :error_message)");
            $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
            $this->db->bind(':recipient_email', $recipientEmail);
            $this->db->bind(':subject', $subject);
            $this->db->bind(':status', $sent ? 'sent' : 'failed');
            $this->db->bind(':sent_at', $sent ? date('Y-m-d H:i:s') : null);
            $this->db->bind(':error_message', $errorMessage);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Session reminder log insert failed for booking #' . $bookingId . ': ' . $e->getMessage());
            return false;
        }
    }
}
