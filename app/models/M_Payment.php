<?php
class M_Payment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function ensureInitialPendingMembershipPayment(int $playerId): bool {
        $subscription = $this->getPlayerSubscription($playerId);
        if (!$subscription || empty($subscription->SubscriptionID)) {
            return false;
        }

        $planName = strtolower(trim((string)($subscription->PlanName ?? '')));
        if ($planName === 'facility_only' || (float)($subscription->SubscriptionFee ?? 0) <= 0) {
            return false;
        }

        $this->db->query('SELECT COUNT(*) AS payment_count
            FROM subscriptionpayment
            WHERE SubscriptionID = :subscription_id');
        $this->db->bind(':subscription_id', (int)$subscription->SubscriptionID, PDO::PARAM_INT);
        $existing = $this->db->single();

        if ($existing && (int)($existing->payment_count ?? 0) > 0) {
            return false;
        }

        $dueDate = $this->calculateInitialMembershipDueDate((string)($subscription->StartDate ?? date('Y-m-d')));

        $this->db->query('INSERT INTO subscriptionpayment
            (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, Notes)
            VALUES (:subscription_id, NULL, :amount, :payment_method, :status, :due_date, :notes)');
        $this->db->bind(':subscription_id', (int)$subscription->SubscriptionID, PDO::PARAM_INT);
        $this->db->bind(':amount', number_format((float)($subscription->SubscriptionFee ?? 0), 2, '.', ''), PDO::PARAM_STR);
        $this->db->bind(':payment_method', 'online', PDO::PARAM_STR);
        $this->db->bind(':status', 'pending', PDO::PARAM_STR);
        $this->db->bind(':due_date', $dueDate, PDO::PARAM_STR);
        $this->db->bind(':notes', 'Membership is pending. Pay to experience the whole academy services.', PDO::PARAM_STR);

        return $this->db->execute();
    }

    private function calculateInitialMembershipDueDate(string $startDate): string {
        try {
            $subscriptionStart = new DateTime($startDate);
        } catch (Exception $e) {
            $subscriptionStart = new DateTime();
        }

        $dueDate = clone $subscriptionStart;
        if ((int)$dueDate->format('j') > 14) {
            $dueDate->modify('first day of next month');
        }

        $dueDate->setDate(
            (int)$dueDate->format('Y'),
            (int)$dueDate->format('m'),
            14
        );

        return $dueDate->format('Y-m-d');
    }

    // Get payment history for a player
    public function getPaymentHistory($playerId) {
        $this->db->query('SELECT sp.PaymentID, sp.PaymentDate, sp.Amount, sp.PaymentMethod, 
            sp.Status, sp.DueDate, sp.Notes, sp.PaymentReference, sp.Gateway, 0.00 AS LateFee, mp.PlanName
            FROM subscriptionpayment sp 
            JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID 
            JOIN membershipplan mp ON ps.PlanID = mp.PlanID 
            WHERE ps.PlayerID = :player_id 
            ORDER BY sp.PaymentDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get upcoming/pending payments for a player
    public function getUpcomingPayments($playerId) {
        $this->db->query('SELECT sp.PaymentID, sp.DueDate, sp.Amount, sp.Status, sp.PaymentMethod, sp.Notes, mp.PlanName
            FROM subscriptionpayment sp 
            JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID 
            JOIN membershipplan mp ON ps.PlanID = mp.PlanID 
            WHERE ps.PlayerID = :player_id AND sp.Status = "pending" 
            ORDER BY sp.DueDate ASC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get active subscription for a player
    public function getPlayerSubscription($playerId) {
        $this->db->query('SELECT ps.SubscriptionID, ps.PlayerID, ps.PlanID, ps.StartDate, ps.EndDate,
            ps.Status, ps.MonthlyFee AS SubscriptionFee, ps.PaymentDay, ps.AutoRenewal,
            mp.PlanName, mp.Description, mp.MonthlyFee AS PlanFee, 
            mp.SessionsPerWeek, mp.PrivateSessionsIncluded, mp.FacilityAccessIncluded
            FROM playersubscription ps 
            JOIN membershipplan mp ON ps.PlanID = mp.PlanID 
            WHERE ps.PlayerID = :player_id AND ps.Status = "active"
            ORDER BY ps.StartDate DESC LIMIT 1');
        $this->db->bind(':player_id', $playerId);
        return $this->db->single();
    }

    // Get all membership plans
    public function getAllPlans() {
        $this->db->query('SELECT * FROM membershipplan WHERE Status = "active" ORDER BY MonthlyFee ASC');
        return $this->db->resultSet();
    }

    // Get payment summary/stats for a player
    public function getPaymentSummary($playerId) {
        $this->db->query('SELECT 
            COUNT(CASE WHEN sp.Status = "completed" THEN 1 END) as total_paid,
            COUNT(CASE WHEN sp.Status = "pending" THEN 1 END) as total_pending,
            COALESCE(SUM(CASE WHEN sp.Status = "completed" THEN sp.Amount ELSE 0 END), 0) as total_amount_paid,
            COALESCE(SUM(CASE WHEN sp.Status = "pending" THEN sp.Amount ELSE 0 END), 0) as total_amount_due
            FROM subscriptionpayment sp 
            JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID 
            WHERE ps.PlayerID = :player_id');
        $this->db->bind(':player_id', $playerId);
        return $this->db->single();
    }

    // ==================== SESSION PAYMENTS ====================

    /**
     * Record a session payment (pending until confirmed)
     */
    public function createSessionPayment(array $data): int|false {
        $this->db->query('INSERT INTO sessionpayment
            (EnrollmentID, PlayerID, SessionID, Amount, PaymentMethod, Status, PaidAt)
            VALUES (:eid, :pid, :sid, :amount, :method, :status, :paid_at)');
        $this->db->bind(':eid',     (int)$data['enrollment_id'], PDO::PARAM_INT);
        $this->db->bind(':pid',     (int)$data['player_id'],     PDO::PARAM_INT);
        $this->db->bind(':sid',     (int)$data['session_id'],    PDO::PARAM_INT);
        $this->db->bind(':amount',  $data['amount'],             PDO::PARAM_STR);
        $this->db->bind(':method',  $data['payment_method'] ?? 'online', PDO::PARAM_STR);
        $this->db->bind(':status',  $data['status'] ?? 'completed', PDO::PARAM_STR);
        $this->db->bind(':paid_at', $data['paid_at'] ?? date('Y-m-d H:i:s'), PDO::PARAM_STR);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Mark a session payment as refunded
     */
    public function refundSessionPayment(int $enrollmentId): bool {
        $this->db->query("UPDATE sessionpayment
            SET Status = 'refunded', RefundedAt = NOW()
            WHERE EnrollmentID = :eid AND Status = 'completed'");
        $this->db->bind(':eid', $enrollmentId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Get session payments for a player
     */
    public function getSessionPaymentsForPlayer(int $playerId): array {
        $this->db->query('SELECT sp.*, s.Name AS session_name, s.Date AS session_date,
            s.PricePerSession
            FROM sessionpayment sp
            JOIN session s ON sp.SessionID = s.SessionID
            WHERE sp.PlayerID = :pid
            ORDER BY sp.CreatedAt DESC');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get equipment rental payments due for player
    public function getRentalPaymentsDue($playerId) {
        $this->db->query('SELECT e.Name AS item, DATE(er.EndDate) AS due_date, er.TotalCost AS fee
            FROM equipmentrental er 
            JOIN equipment e ON er.EquipmentID = e.EquipmentID 
            WHERE er.PlayerID = :player_id AND er.Status = "active"
            ORDER BY er.EndDate ASC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }
}
