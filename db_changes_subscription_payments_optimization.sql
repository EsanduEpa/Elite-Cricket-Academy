-- Subscription payments optimization (membershipplan / playersubscription / subscriptionpayment)
-- Target: MySQL 8+ or MariaDB 10.2+ (generated columns supported).
-- Run once, after importing the base schema.

START TRANSACTION;

-- ---------------------------------------------------------------------------
-- 1) membershipplan
-- ---------------------------------------------------------------------------
-- Query patterns in app/models/M_Payment.php:
--   SELECT * FROM membershipplan WHERE Status='active' ORDER BY MonthlyFee ASC

ALTER TABLE membershipplan
  ADD COLUMN CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE INDEX idx_membershipplan_status_fee ON membershipplan (Status, MonthlyFee);

-- ---------------------------------------------------------------------------
-- 2) playersubscription
-- ---------------------------------------------------------------------------
-- Query patterns in app/models/M_Payment.php:
--   WHERE PlayerID=? AND Status='active' ORDER BY StartDate DESC LIMIT 1

ALTER TABLE playersubscription
  ADD COLUMN CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN StatusChangedAt DATETIME NULL DEFAULT NULL,
  ADD COLUMN CancelledAt DATETIME NULL DEFAULT NULL,
  ADD COLUMN CancelReason VARCHAR(255) NULL DEFAULT NULL;

CREATE INDEX idx_playersubscription_player_status_start
  ON playersubscription (PlayerID, Status, StartDate);

CREATE INDEX idx_playersubscription_plan_status
  ON playersubscription (PlanID, Status);

-- Optional (recommended) integrity rule:
-- Ensure a player cannot have more than one ACTIVE subscription at a time.
-- This uses a generated column that is NULL for non-active rows, so multiple
-- historical (expired/cancelled/etc) rows are allowed.
--
-- If your MySQL/MariaDB version does not support STORED generated columns,
-- comment these 2 statements out.
--

-- ---------------------------------------------------------------------------
-- 3) subscriptionpayment
-- ---------------------------------------------------------------------------
-- Key goals:
-- - represent billing period explicitly (for "one payment per month")
-- - make pending payments realistic (PaymentDate can be NULL)
-- - support online gateway reconciliation (reference IDs)
-- - add audit timestamps
-- - add indexes aligned to existing queries in M_Payment.php + Finance.php

ALTER TABLE subscriptionpayment
  MODIFY COLUMN PaymentDate DATE NULL,
  ADD COLUMN BillingMonth DATE
    GENERATED ALWAYS AS (DATE_SUB(DueDate, INTERVAL (DAYOFMONTH(DueDate) - 1) DAY)) STORED
    COMMENT 'First day of billing month derived from DueDate' AFTER SubscriptionID,
  ADD COLUMN PaymentReference VARCHAR(100) NULL DEFAULT NULL AFTER Status,
  ADD COLUMN Gateway ENUM('manual','payhere') NOT NULL DEFAULT 'manual' AFTER PaymentReference,
  ADD COLUMN GatewayOrderId VARCHAR(50) NULL DEFAULT NULL AFTER Gateway,
  ADD COLUMN GatewayPaymentId VARCHAR(50) NULL DEFAULT NULL AFTER GatewayOrderId,
  ADD COLUMN Notes TEXT NULL DEFAULT NULL AFTER ProcessedBy,
  ADD COLUMN CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN PaidAt DATETIME NULL DEFAULT NULL,
  ADD COLUMN FailedAt DATETIME NULL DEFAULT NULL,
  ADD COLUMN RefundedAt DATETIME NULL DEFAULT NULL;

-- Backfill lifecycle timestamps for existing rows.
UPDATE subscriptionpayment
SET
  PaidAt = CASE
    WHEN Status = 'completed' AND PaymentDate IS NOT NULL THEN CONCAT(PaymentDate, ' 00:00:00')
    ELSE NULL
  END,
  FailedAt = CASE
    WHEN Status = 'failed' AND PaymentDate IS NOT NULL THEN CONCAT(PaymentDate, ' 00:00:00')
    ELSE NULL
  END,
  RefundedAt = CASE
    WHEN Status = 'refunded' AND PaymentDate IS NOT NULL THEN CONCAT(PaymentDate, ' 00:00:00')
    ELSE NULL
  END;

-- Prevent duplicate monthly charges per subscription.
CREATE UNIQUE INDEX uk_subscriptionpayment_subscription_month
  ON subscriptionpayment (SubscriptionID, BillingMonth);

-- Performance indexes for common filters/sorts.
CREATE INDEX idx_subscriptionpayment_status_due
  ON subscriptionpayment (Status, DueDate);

CREATE INDEX idx_subscriptionpayment_sub_status_due
  ON subscriptionpayment (SubscriptionID, Status, DueDate);

CREATE INDEX idx_subscriptionpayment_status_paymentdate
  ON subscriptionpayment (Status, PaymentDate);

CREATE INDEX idx_subscriptionpayment_gateway_order
  ON subscriptionpayment (Gateway, GatewayOrderId);

COMMIT;
