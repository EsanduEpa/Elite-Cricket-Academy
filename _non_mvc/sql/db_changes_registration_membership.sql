-- ================================================================
-- DB CHANGES: Registration + Membership Plan Integration
-- Date: 2026-04-09
-- Scope: Adds membership plan selection to player registration
--        and auto-creates a playersubscription on sign-up.
-- ================================================================

-- ----------------------------------------------------------------
-- 1.  No table structure changes required.
--     The existing tables are used as-is:
--
--   membershipplan  (PlanID, PlanName, Description, MonthlyFee,
--                    SessionsPerWeek, PrivateSessionsIncluded,
--                    FacilityAccessIncluded, Status)
--
--   playersubscription  (SubscriptionID, PlayerID, PlanID,
--                         StartDate, EndDate, Status, MonthlyFee,
--                         PaymentDay, AutoRenewal)
--
--   playerprofile   (PlayerID, ..., SubscriptionType, ...)
-- ----------------------------------------------------------------


-- ----------------------------------------------------------------
-- 2.  Application-level changes (no DDL needed):
--
--   a) M_Users model  — three new methods added:
--        getActiveMembershipPlans()
--            SELECT * FROM membershipplan WHERE Status = 'active'
--            ORDER BY MonthlyFee ASC
--
--        getMembershipPlanById($planId)
--            SELECT * FROM membershipplan
--            WHERE PlanID = :plan_id AND Status = 'active'
--
--        createPlayerSubscription($playerId, $planId, $monthlyFee)
--            INSERT INTO playersubscription
--              (PlayerID, PlanID, StartDate, Status,
--               MonthlyFee, PaymentDay, AutoRenewal)
--            VALUES
--              (:player_id, :plan_id, CURDATE(), 'active',
--               :monthly_fee, 1, 1)
--
--   b) Register controller — on successful registration:
--        1. Validates selected PlanID against membershipplan
--        2. Calls createPlayerSubscription() with the plan's
--           MonthlyFee read from the DB (never trusted from POST)
--
--   c) Registration form  — new "Membership Plan" dropdown
--        populated from membershipplan WHERE Status = 'active'
-- ----------------------------------------------------------------


-- ----------------------------------------------------------------
-- 3.  Verify active plans exist (run to check, not to modify)
-- ----------------------------------------------------------------
-- SELECT PlanID, PlanName, MonthlyFee, Status
-- FROM membershipplan
-- WHERE Status = 'active'
-- ORDER BY MonthlyFee ASC;


-- ----------------------------------------------------------------
-- 4.  Optional: ensure all three seed plans are present
--     (safe to run; INSERT IGNORE skips duplicates)
-- ----------------------------------------------------------------
INSERT IGNORE INTO `membershipplan`
    (`PlanID`, `PlanName`, `Description`, `MonthlyFee`,
     `SessionsPerWeek`, `PrivateSessionsIncluded`,
     `FacilityAccessIncluded`, `Status`)
VALUES
    (1, 'general', 'Group sessions only',          4500.00, 2, 0, 0, 'active'),
    (2, 'private', 'Private sessions only',         7000.00, 2, 2, 1, 'active'),
    (3, 'pro',     'Group + private sessions',      10000.00, 4, 2, 1, 'active');


-- ----------------------------------------------------------------
-- 5.  Index hint — already covered by the existing primary key on
--     membershipplan(PlanID).  The status+fee index below is
--     useful if the table grows large.
-- ----------------------------------------------------------------
-- CREATE INDEX IF NOT EXISTS idx_membershipplan_status_fee
--     ON membershipplan (Status, MonthlyFee);
-- (MariaDB 10.1+ / MySQL 8.0+ supports IF NOT EXISTS)
-- For older versions use the optimisation script instead:
--   db_changes_subscription_payments_optimization.sql
