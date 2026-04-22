-- ============================================================
-- SESSION BOOKING SYSTEM — DATABASE MIGRATION
-- Elite Cricket Academy
-- Date: 2026-04-03
-- Run this script on your local `cricket_academy` database
-- to sync with the session booking system changes.
-- ============================================================


-- ------------------------------------------------------------
-- 1. session table — make CoachOrTrainerID nullable
--    (admin can now create empty slots with no coach assigned)
-- ------------------------------------------------------------
ALTER TABLE `session`
  MODIFY COLUMN `CoachOrTrainerID` INT(11) NULL DEFAULT NULL;


-- ------------------------------------------------------------
-- 2. session table — add 'open' status
--    'open'   = admin created, not yet claimed by coach/trainer
--    'active' = claimed by coach/trainer, open for player booking
-- ------------------------------------------------------------
ALTER TABLE `session`
  MODIFY COLUMN `Status` ENUM('open','active','cancelled','completed') DEFAULT 'open';


-- ------------------------------------------------------------
-- 3. New table: sessiondetails
--    Stores extra details for a session (facility, cancel reason)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessiondetails` (
    DetailID       INT(11) NOT NULL AUTO_INCREMENT,
    SessionID      INT(11) NOT NULL,
    FacilityType   VARCHAR(100) NULL,
    FacilityNumber VARCHAR(50)  NULL,
    CancelReason   TEXT         NULL,
    PRIMARY KEY (DetailID),
    KEY idx_session (SessionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 4. New table: sessionattendance
--    Tracks per-enrollment attendance marked by coach/trainer
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionattendance` (
    AttendanceID     INT(11) NOT NULL AUTO_INCREMENT,
    EnrollmentID     INT(11) NOT NULL,
    AttendanceStatus ENUM('present','absent','late') NOT NULL DEFAULT 'present',
    AttendanceNotes  TEXT NULL,
    MarkedBy         INT(11) NULL,
    MarkedAt         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (AttendanceID),
    KEY idx_enrollment (EnrollmentID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 5. sessionenrollment — add UpdatedAt
--    Required to track when a cancellation was made
--    (used in the monthly cancellation count rule: max 3/month)
-- ------------------------------------------------------------
ALTER TABLE `sessionenrollment`
  ADD COLUMN `UpdatedAt` DATETIME NULL DEFAULT NULL;


-- ------------------------------------------------------------
-- 6. coachappointment — add UpdatedAt
--    Same reason as above, for coach appointment cancellations
-- ------------------------------------------------------------
ALTER TABLE `coachappointment`
  ADD COLUMN `UpdatedAt` DATETIME NULL DEFAULT NULL;


-- ------------------------------------------------------------
-- 7. trainerappointment — add UpdatedAt
--    Same reason as above, for trainer appointment cancellations
-- ------------------------------------------------------------
ALTER TABLE `trainerappointment`
  ADD COLUMN `UpdatedAt` DATETIME NULL DEFAULT NULL;


-- ------------------------------------------------------------
-- 8. New table: sessionpayment
--    Records payment when a player books a paid session
--    (PricePerSession > 0). Also tracks refunds on cancellation.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionpayment` (
    PaymentID     INT(11)       NOT NULL AUTO_INCREMENT,
    EnrollmentID  INT(11)       NOT NULL,
    PlayerID      INT(11)       NOT NULL,
    SessionID     INT(11)       NOT NULL,
    Amount        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PaymentMethod ENUM('cash','card','online') NOT NULL DEFAULT 'online',
    Status        ENUM('pending','completed','refunded') NOT NULL DEFAULT 'pending',
    PaidAt        DATETIME NULL DEFAULT NULL,
    RefundedAt    DATETIME NULL DEFAULT NULL,
    CreatedAt     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (PaymentID),
    KEY idx_enrollment (EnrollmentID),
    KEY idx_player     (PlayerID),
    KEY idx_session    (SessionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- END OF MIGRATION
-- ============================================================
