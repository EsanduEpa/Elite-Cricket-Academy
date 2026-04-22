-- Insert sample subscription data for testing finance page

-- First create some playersubscriptions if they don't exist
-- (Assuming PlanID 1 exists in subscriptionplan table)
INSERT IGNORE INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
VALUES 
(7, 1, '2025-11-01', '2026-11-01', 'active', 5000.00, 14, 1),
(16, 1, '2025-12-01', '2026-12-01', 'active', 5000.00, 14, 1),
(20, 1, '2026-01-01', '2027-01-01', 'active', 5000.00, 14, 1);

-- Now insert sample subscription payments
INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, ProcessedBy)
SELECT
    t.SubscriptionID,
    CASE WHEN t.Status = 'pending' THEN NULL ELSE t.AnyDate END AS PaymentDate,
    t.Amount,
    t.PaymentMethod,
    t.Status,
    DATE_ADD(
        DATE_SUB(t.AnyDate, INTERVAL (DAYOFMONTH(t.AnyDate) - 1) DAY),
        INTERVAL 13 DAY
    ) AS DueDate,
    t.ProcessedBy
FROM (
    SELECT
        ps.SubscriptionID,
        DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 60) DAY) AS AnyDate,
        ps.MonthlyFee AS Amount,
        CASE FLOOR(RAND() * 4)
            WHEN 0 THEN 'cash'
            WHEN 1 THEN 'card'
            WHEN 2 THEN 'bank_transfer'
            ELSE 'online'
        END AS PaymentMethod,
        CASE
            WHEN RAND() > 0.8 THEN 'pending'
            ELSE 'completed'
        END AS Status,
        1 AS ProcessedBy  -- Admin user
    FROM playersubscription ps
    WHERE ps.Status = 'active'
    LIMIT 10
) t;
