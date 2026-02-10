-- Insert sample subscription data for testing finance page

-- First create some playersubscriptions if they don't exist
-- (Assuming PlanID 1 exists in subscriptionplan table)
INSERT IGNORE INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
VALUES 
(7, 1, '2025-11-01', '2026-11-01', 'active', 5000.00, 1, 1),
(16, 1, '2025-12-01', '2026-12-01', 'active', 5000.00, 1, 1),
(20, 1, '2026-01-01', '2027-01-01', 'active', 5000.00, 1, 1);

-- Now insert sample subscription payments
INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, LateFee, ProcessedBy)
SELECT 
    ps.SubscriptionID,
    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 60) DAY) as PaymentDate,
    ps.MonthlyFee as Amount,
    CASE FLOOR(RAND() * 4)
        WHEN 0 THEN 'cash'
        WHEN 1 THEN 'card'
        WHEN 2 THEN 'bank_transfer'
        ELSE 'online'
    END as PaymentMethod,
    CASE 
        WHEN RAND() > 0.8 THEN 'pending'
        ELSE 'completed'
    END as Status,
    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 60) DAY) as DueDate,
    0.00 as LateFee,
    1 as ProcessedBy  -- Admin user
FROM playersubscription ps
WHERE ps.Status = 'active'
LIMIT 10;
