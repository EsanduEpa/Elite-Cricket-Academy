USE cricket_academy;

-- ================================================================
-- User Table Name Split
-- File: add_user_first_last_name_columns.sql
--
-- Purpose:
--   Replace the single Name column with FirstName and LastName fields.
--
-- Behavior:
--   1. Existing values in `Name` are preserved automatically when the
--      column is renamed to `FirstName`.
--   2. New `LastName` values start as NULL.
--   3. This script does not attempt to split existing full names into
--      first and last names, because that would be unreliable.
-- ================================================================

ALTER TABLE `user`
    CHANGE COLUMN `Name` `FirstName` VARCHAR(255) NOT NULL,
    ADD COLUMN `LastName` VARCHAR(255) NULL AFTER `FirstName`;


-- ================================================================
-- Optional verification
-- ================================================================

-- SHOW COLUMNS FROM `user`;
-- SELECT UserID, FirstName, LastName FROM `user` LIMIT 20;

ALTER TABLE productorder
ADD COLUMN IsShipped TINYINT(1) NOT NULL DEFAULT 0 AFTER OrderDate,
ADD COLUMN ShippedAt DATETIME NULL DEFAULT NULL AFTER IsShipped;

-- ================================================================
-- File: db_changes_facility_only_shipping_and_backfill.sql
-- Purpose: Consolidate today's DB changes into one script
-- Changes included:
--   1) Add facility_only membership plan
--   2) Backfill missing initial pending subscription payments
--   3) Add product shipping columns to productorder
--   4) Support membership-plan based required plans in slot templates
--   5) Remove unused recurrence date fields from slot templates
-- ================================================================

START TRANSACTION;

-- ----------------------------------------------------------------
-- 1) Add the facility_only membership plan if it does not exist
-- ----------------------------------------------------------------
INSERT INTO membershipplan
    (PlanName, Description, MonthlyFee, SessionsPerWeek, PrivateSessionsIncluded, FacilityAccessIncluded, Status)
SELECT
    'facility_only',
    'Facility access without a coach. No recurring monthly membership fee. Users pay the facility booking price per booking.',
    0.00,
    0,
    0,
    1,
    'active'
WHERE NOT EXISTS (
    SELECT 1
    FROM membershipplan
    WHERE PlanName = 'facility_only'
);

-- ----------------------------------------------------------------
-- 2) Backfill the initial pending subscription payment for active
--    subscriptions that currently have no rows in subscriptionpayment
-- ----------------------------------------------------------------
INSERT INTO subscriptionpayment
    (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, Notes)
SELECT
    ps.SubscriptionID,
    NULL AS PaymentDate,
    ps.MonthlyFee AS Amount,
    'online' AS PaymentMethod,
    'pending' AS Status,
    CASE
        WHEN DAY(ps.StartDate) > 14
            THEN DATE_ADD(DATE_ADD(LAST_DAY(ps.StartDate), INTERVAL 1 DAY), INTERVAL 13 DAY)
        ELSE DATE_ADD(DATE_SUB(ps.StartDate, INTERVAL DAY(ps.StartDate) - 1 DAY), INTERVAL 13 DAY)
    END AS DueDate,
    'Membership is pending. Pay to experience the whole academy services.' AS Notes
FROM playersubscription ps
JOIN membershipplan mp ON mp.PlanID = ps.PlanID
WHERE ps.Status = 'active'
  AND LOWER(mp.PlanName) <> 'facility_only'
  AND ps.MonthlyFee > 0
  AND NOT EXISTS (
      SELECT 1
      FROM subscriptionpayment sp
      WHERE sp.SubscriptionID = ps.SubscriptionID
  );

-- ----------------------------------------------------------------
-- 3) Add shipping columns to productorder
-- ----------------------------------------------------------------
ALTER TABLE productorder
    ADD COLUMN IF NOT EXISTS IsShipped TINYINT(1) NOT NULL DEFAULT 0 AFTER OrderDate,
    ADD COLUMN IF NOT EXISTS ShippedAt DATETIME NULL DEFAULT NULL AFTER IsShipped;

-- ----------------------------------------------------------------
-- 4) Update slot templates to store exact required membership plans
--    Admin forms now submit values like "plan:3".
--    Legacy "none" values are converted to NULL so old rows do not break.
-- ----------------------------------------------------------------
ALTER TABLE slot_template
    MODIFY COLUMN RequiredPlanFeature VARCHAR(50) NULL
    COMMENT 'NULL = open to all; supports legacy feature rules and plan:ID values checked at booking time';

UPDATE slot_template
SET RequiredPlanFeature = NULL
WHERE RequiredPlanFeature = 'none';

-- Optional follow-up after every existing template has been assigned a real plan:
-- ALTER TABLE slot_template
--     MODIFY COLUMN RequiredPlanFeature VARCHAR(50) NOT NULL
--     COMMENT 'Stores required membership plan values like plan:3';

-- ----------------------------------------------------------------
-- 5) Template recurrence dates are no longer collected on the form.
--    Generation now always uses the explicit from/to range in the
--    occurrence generation form, so these legacy columns can be removed.
-- ----------------------------------------------------------------
ALTER TABLE slot_template
    DROP COLUMN RecurrenceStart,
    DROP COLUMN RecurrenceEnd;

COMMIT;

-- ----------------------------------------------------------------
-- Optional verification queries
-- ----------------------------------------------------------------
-- SELECT * FROM membershipplan WHERE PlanName = 'facility_only';
-- SELECT SubscriptionID, Amount, Status, DueDate, Notes FROM subscriptionpayment ORDER BY PaymentID DESC;
-- DESCRIBE productorder;
-- DESCRIBE slot_template;
-- SELECT TemplateID, TemplateName, RequiredPlanFeature FROM slot_template ORDER BY TemplateID DESC;