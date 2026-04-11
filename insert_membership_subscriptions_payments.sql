-- Seed data: Membership plans + player subscriptions + subscription payments
-- Uses actual schema tables: membershipplan, playersubscription, subscriptionpayment
-- Safe to re-run: inserts are guarded with WHERE NOT EXISTS.

START TRANSACTION;

-- NOTE:
-- This script assumes you already have these rows in membershipplan:
-- (1, 'general', 4500.00), (2, 'private', 7000.00), (3, 'pro', 10000.00)
-- It will only insert into playersubscription and subscriptionpayment.

-- 2) Player subscriptions
-- Known PlayerIDs from cricket_academy_schema.sql dump: 7, 15, 16, 18
-- Note: MonthlyFee is stored on playersubscription as a price snapshot.

INSERT INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
SELECT 7,
   1,
       '2025-11-01',
       NULL,
       'active',
   4500.00,
     14,
       1
WHERE NOT EXISTS (
    SELECT 1 FROM playersubscription
    WHERE PlayerID = 7
  AND PlanID = 1
      AND Status IN ('active','suspended')
);

INSERT INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
SELECT 15,
   3,
       '2025-12-01',
       NULL,
       'active',
   10000.00,
     14,
       1
WHERE NOT EXISTS (
    SELECT 1 FROM playersubscription
    WHERE PlayerID = 15
  AND PlanID = 3
      AND Status IN ('active','suspended')
);

INSERT INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
SELECT 16,
   1,
       '2026-01-01',
       NULL,
       'suspended',
   4500.00,
     14,
       0
WHERE NOT EXISTS (
    SELECT 1 FROM playersubscription
    WHERE PlayerID = 16
  AND PlanID = 1
      AND Status IN ('active','suspended')
);

INSERT INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
SELECT 18,
   2,
       '2025-10-01',
       '2025-12-31',
       'expired',
   7000.00,
     14,
       0
WHERE NOT EXISTS (
    SELECT 1 FROM playersubscription
    WHERE PlayerID = 18
  AND PlanID = 2
      AND StartDate = '2025-10-01'
);

-- 3) Subscription payments
-- ProcessedBy: take any existing shopemployeeprofile row, else NULL.
-- Avoid duplicates by keeping one payment row per subscription per month.

-- Player 7 (general): Jan + Feb paid, Mar pending
INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, ProcessedBy)
SELECT
    (SELECT SubscriptionID FROM playersubscription
     WHERE PlayerID = 7
       AND PlanID = 1
     ORDER BY SubscriptionID DESC LIMIT 1),
    '2026-01-01',
    4500.00,
    'card',
    'completed',
    '2026-01-14',
    (SELECT ShopEmployeeID FROM shopemployeeprofile ORDER BY ShopEmployeeID LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = (SELECT SubscriptionID FROM playersubscription
                               WHERE PlayerID = 7
                                 AND PlanID = 1
                               ORDER BY SubscriptionID DESC LIMIT 1)
      AND sp.DueDate = '2026-01-14'
);

INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, ProcessedBy)
SELECT
    (SELECT SubscriptionID FROM playersubscription
     WHERE PlayerID = 7
       AND PlanID = 1
     ORDER BY SubscriptionID DESC LIMIT 1),
    '2026-02-01',
    4500.00,
    'cash',
    'completed',
    '2026-02-14',
    (SELECT ShopEmployeeID FROM shopemployeeprofile ORDER BY ShopEmployeeID LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = (SELECT SubscriptionID FROM playersubscription
                               WHERE PlayerID = 7
                                 AND PlanID = 1
                               ORDER BY SubscriptionID DESC LIMIT 1)
      AND sp.DueDate = '2026-02-14'
);

INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, ProcessedBy)
SELECT
    (SELECT SubscriptionID FROM playersubscription
     WHERE PlayerID = 7
       AND PlanID = 1
     ORDER BY SubscriptionID DESC LIMIT 1),
    NULL,
    4500.00,
    'online',
    'pending',
    '2026-03-14',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = (SELECT SubscriptionID FROM playersubscription
                               WHERE PlayerID = 7
                                 AND PlanID = 1
                               ORDER BY SubscriptionID DESC LIMIT 1)
      AND sp.DueDate = '2026-03-14'
);

-- Player 15 (pro): payment after due date (no late fee policy)
INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, ProcessedBy)
SELECT
    (SELECT SubscriptionID FROM playersubscription
     WHERE PlayerID = 15
       AND PlanID = 3
     ORDER BY SubscriptionID DESC LIMIT 1),
    '2026-01-10',
    10000.00,
    'bank_transfer',
    'completed',
    '2026-01-14',
    (SELECT ShopEmployeeID FROM shopemployeeprofile ORDER BY ShopEmployeeID LIMIT 1)
WHERE NOT EXISTS (
    SELECT 1 FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = (SELECT SubscriptionID FROM playersubscription
                               WHERE PlayerID = 15
                                 AND PlanID = 3
                               ORDER BY SubscriptionID DESC LIMIT 1)
      AND sp.DueDate = '2026-01-14'
);

-- Player 16 (general): failed payment example
INSERT INTO subscriptionpayment (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, ProcessedBy)
SELECT
    (SELECT SubscriptionID FROM playersubscription
     WHERE PlayerID = 16
       AND PlanID = 1
     ORDER BY SubscriptionID DESC LIMIT 1),
    NULL,
    4500.00,
    'card',
    'failed',
    '2026-02-14',
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = (SELECT SubscriptionID FROM playersubscription
                               WHERE PlayerID = 16
                                 AND PlanID = 1
                               ORDER BY SubscriptionID DESC LIMIT 1)
      AND sp.DueDate = '2026-02-14'
);

COMMIT;
