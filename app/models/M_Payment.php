<?php
class M_Payment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get payment history for a player
    public function getPaymentHistory($playerId) {
        $this->db->query('SELECT sp.PaymentID, sp.PaymentDate, sp.Amount, sp.PaymentMethod, 
            sp.Status, sp.DueDate, sp.LateFee, mp.PlanName
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
        $this->db->query('SELECT sp.PaymentID, sp.DueDate, sp.Amount, sp.Status, mp.PlanName
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
