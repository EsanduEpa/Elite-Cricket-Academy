-- ================================================================
-- Elite Cricket Academy — Slot / Session / Booking / Facility Module
-- File: create_slot_tables.sql
-- Run AFTER cricket_academy (10).sql is already loaded
-- All CREATE statements use IF NOT EXISTS — safe on fresh install
-- Run once per environment; harmless to re-run
--
-- NOTE (table name clarity):
--   time_slot                → slot_time_band
--   slot_occurrence_coach     → slot_occurrence_staff_override
--   slot_change_log           → slot_audit_log
-- If you already ran an older version of this script, rename the tables first:
--   RENAME TABLE `time_slot` TO `slot_time_band`;
--   RENAME TABLE `slot_occurrence_coach` TO `slot_occurrence_staff_override`;
--   RENAME TABLE `slot_change_log` TO `slot_audit_log`;
-- ================================================================

-- ----------------------------------------------------------------
-- Step 0: Seed the Trainer Room into the existing facility table
-- FacilityID 6 is the fixed location for all trainer-led sessions.
-- Using a real FacilityID lets the UNIQUE constraint on slot_occurrence
-- prevent double-booking of the trainer room — no special-case code needed.
-- ----------------------------------------------------------------
INSERT IGNORE INTO `facility`
    (`FacilityID`, `Name`, `Location`, `Capacity`, `AvailabilityStatus`, `HourlyRate`)
VALUES
    (6, 'Trainer Room', 'Main Building — Ground Floor', 1, 'Available', 0.00);
-- Capacity = 1  : one trainer session at a time
-- HourlyRate = 0: players are billed via session fee, not room hire


-- ================================================================
-- 1. slot_time_band — Master Fixed Time Bands
-- 7 bands covering 09:00-22:00 in 2-hour steps (last band is 1h)
-- RULE: Never DELETE a row — set IsActive = 0 instead
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_time_band` (
  `SlotID`           TINYINT(4)   NOT NULL AUTO_INCREMENT,
  `SlotLabel`        VARCHAR(50)  NOT NULL COMMENT '"09:00 AM – 11:00 AM" — used in dropdowns',
  `StartTime`        TIME         NOT NULL,
  `EndTime`          TIME         NOT NULL,
  `DurationMinutes`  SMALLINT(6)  NOT NULL DEFAULT 120,
  `IsActive`         TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`SlotID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Fixed time bands. Toggle IsActive only — never DELETE rows.';

INSERT IGNORE INTO `slot_time_band`
    (`SlotID`, `SlotLabel`, `StartTime`, `EndTime`, `DurationMinutes`)
VALUES
    (1, '09:00 AM – 11:00 AM', '09:00:00', '11:00:00', 120),
    (2, '11:00 AM – 01:00 PM', '11:00:00', '13:00:00', 120),
    (3, '01:00 PM – 03:00 PM', '13:00:00', '15:00:00', 120),
    (4, '03:00 PM – 05:00 PM', '15:00:00', '17:00:00', 120),
    (5, '05:00 PM – 07:00 PM', '17:00:00', '19:00:00', 120),
    (6, '07:00 PM – 09:00 PM', '19:00:00', '21:00:00', 120),
    (7, '09:00 PM – 10:00 PM', '21:00:00', '22:00:00',  60);


-- ================================================================
-- 2. slot_template — Program / Session Definition
-- One row = one type of recurring session offering.
-- Admin creates; players never touch this table directly.
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_template` (
  `TemplateID`          INT(11)       NOT NULL AUTO_INCREMENT,
  `TemplateName`        VARCHAR(255)  NOT NULL,

  `SlotType`            ENUM('program','private','facility_only') NOT NULL
    COMMENT 'program=group subscription session | private=1:1 on request | facility_only=no staff',

  `StaffType`           ENUM('coach','trainer','none') NOT NULL DEFAULT 'coach'
    COMMENT 'Determines post-session log prompt: coachingsession vs trainerappointment vs none',

  `SlotID`              TINYINT(4)    NOT NULL            COMMENT 'FK → slot_time_band',
  `DayOfWeek`           TINYINT(1)    DEFAULT NULL        COMMENT '1=Mon…7=Sun; NULL=no fixed day',
  `FacilityID`          INT(11)       DEFAULT NULL        COMMENT 'FK → facility; NULL=assigned per occurrence',

  `AgeGroup`            VARCHAR(50)   DEFAULT NULL        COMMENT '"Under 15", "Under 19", "Under 21", "Open"',
  `Category`            VARCHAR(100)  DEFAULT NULL        COMMENT '"Batting","Bowling","Fielding","Fitness"',
  `Description`         TEXT          DEFAULT NULL,

  `MaxParticipants`     INT(11)       NOT NULL DEFAULT 10,
  `PricePerSession`     DECIMAL(10,2) NOT NULL DEFAULT 0.00
    COMMENT '0.00 = subscription-covered; >0 = direct charge',

  `RequiredPlanFeature` VARCHAR(50) DEFAULT NULL
    COMMENT 'NULL = open to all; supports legacy feature rules and plan:ID values checked at booking time',

  `IsActive`            TINYINT(1)    NOT NULL DEFAULT 1,

  `CreatedBy`           INT(11)       NOT NULL            COMMENT 'FK → user (Admin)',
  `CreatedAt`           DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt`           DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`TemplateID`),
  KEY `idx_st_slot`       (`SlotID`),
  KEY `idx_st_facility`   (`FacilityID`),
  KEY `idx_st_type`       (`SlotType`),
  KEY `idx_st_stafftype`  (`StaffType`),
  CONSTRAINT `fk_st_slot`     FOREIGN KEY (`SlotID`)     REFERENCES `slot_time_band`(`SlotID`),
  CONSTRAINT `fk_st_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility`(`FacilityID`),
  CONSTRAINT `fk_st_creator`  FOREIGN KEY (`CreatedBy`)  REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master definition of recurring programs and bookable slot offerings.';


-- ================================================================
-- 3. slot_template_staff — Multi-Staff Assignments Per Template
-- Supports coaches AND trainers co-assigned to the same program.
-- RULE: user.Role must match StaffType — validated in application.
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_template_staff` (
  `ID`          INT(11)  NOT NULL AUTO_INCREMENT,
  `TemplateID`  INT(11)  NOT NULL  COMMENT 'FK → slot_template',
  `UserID`      INT(11)  NOT NULL  COMMENT 'FK → user (Role = Coach OR Trainer)',
  `StaffType`   ENUM('coach','trainer') NOT NULL,
  `StaffRole`   ENUM('lead','assistant') NOT NULL DEFAULT 'lead',
  `AssignedBy`  INT(11)  NOT NULL  COMMENT 'FK → user (Admin)',
  `AssignedAt`  DATETIME DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_tstaff_template_user` (`TemplateID`, `UserID`),
  CONSTRAINT `fk_tstaff_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template`(`TemplateID`) ON DELETE CASCADE,
  CONSTRAINT `fk_tstaff_user`     FOREIGN KEY (`UserID`)     REFERENCES `user`(`UserID`),
  CONSTRAINT `fk_tstaff_assigner` FOREIGN KEY (`AssignedBy`) REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Coaches and trainers assigned to a template. UNIQUE(TemplateID, UserID) prevents duplicates.';


-- ================================================================
-- 4. slot_occurrence — Actual Calendar Instances
-- One row = one real session on one specific date.
-- Critical UNIQUE KEY: prevents double-booking a facility on the same day/time.
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_occurrence` (
  `OccurrenceID`    INT(11)    NOT NULL AUTO_INCREMENT,
  `TemplateID`      INT(11)    DEFAULT NULL  COMMENT 'NULL = ad-hoc (not from a template)',
  `SlotID`          TINYINT(4) NOT NULL      COMMENT 'FK → slot_time_band',
  `OccurrenceDate`  DATE       NOT NULL,
  `FacilityID`      INT(11)    DEFAULT NULL  COMMENT 'Overrides template facility for this date',
  `LegacySessionID` INT(11)    DEFAULT NULL  COMMENT 'Migration bridge only → old session.SessionID; NULL for all new rows',

  `Status`          ENUM('scheduled','active','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `CancelReason`    TEXT       DEFAULT NULL,
  `MaxParticipants` INT(11)    DEFAULT NULL  COMMENT 'NULL = inherit from slot_template.MaxParticipants',
  `Notes`           TEXT       DEFAULT NULL,

  `GeneratedBy`     INT(11)    DEFAULT NULL  COMMENT 'Admin UserID who ran batch generation',
  `CreatedAt`       DATETIME   DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`OccurrenceID`),

  -- Hard double-booking lock at the database level.
  -- Two sessions cannot share the same facility, time band, and date.
  -- This covers the Trainer Room (FacilityID=6) as well as all other facilities.
  UNIQUE KEY `uq_occ_facility_slot_date` (`FacilityID`, `SlotID`, `OccurrenceDate`),

  KEY `idx_occ_template`  (`TemplateID`),
  KEY `idx_occ_date`      (`OccurrenceDate`),
  KEY `idx_occ_status`    (`Status`),
  CONSTRAINT `fk_occ_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template`(`TemplateID`),
  CONSTRAINT `fk_occ_slot`     FOREIGN KEY (`SlotID`)     REFERENCES `slot_time_band`(`SlotID`),
  CONSTRAINT `fk_occ_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility`(`FacilityID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='One row per actual session on one calendar date. Generated from template or created ad-hoc.';


-- ================================================================
-- 5. slot_occurrence_staff_override — Per-Date Staff Overrides
-- Used when a coach or trainer is substituted for a single date only.
-- Rows here TAKE PRECEDENCE over slot_template_staff for that occurrence.
-- If no rows exist for an OccurrenceID, fall back to slot_template_staff.
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_occurrence_staff_override` (
  `ID`              INT(11)      NOT NULL AUTO_INCREMENT,
  `OccurrenceID`    INT(11)      NOT NULL  COMMENT 'FK → slot_occurrence',
  `UserID`          INT(11)      NOT NULL  COMMENT 'FK → user (substitute coach or trainer)',
  `StaffType`       ENUM('coach','trainer') NOT NULL,
  `StaffRole`       ENUM('lead','assistant','substitute') NOT NULL DEFAULT 'substitute',
  `OverridesUserID` INT(11)      DEFAULT NULL  COMMENT 'UserID of the person being replaced this date',
  `OverrideReason`  VARCHAR(255) DEFAULT NULL  COMMENT '"Sick leave", "Emergency", "Training camp", etc.',

  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_soso_occ_user` (`OccurrenceID`, `UserID`),
  CONSTRAINT `fk_soso_occurrence` FOREIGN KEY (`OccurrenceID`) REFERENCES `slot_occurrence`(`OccurrenceID`) ON DELETE CASCADE,
  CONSTRAINT `fk_soso_user`       FOREIGN KEY (`UserID`)       REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='One-day staff substitution. Does not affect the template or any other occurrence date.';


-- ================================================================
-- 6. slot_booking — Unified Player Bookings
-- Covers all three booking types: program enrollment, private session,
-- and facility-only self-booking.
-- UNIQUE(OccurrenceID, PlayerID) prevents a player booking the same session twice.
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_booking` (
  `BookingID`          INT(11)       NOT NULL AUTO_INCREMENT,
  `OccurrenceID`       INT(11)       NOT NULL  COMMENT 'FK → slot_occurrence',
  `PlayerID`           INT(11)       NOT NULL  COMMENT 'FK → user (Role = Player)',

  `BookingSource`      ENUM('self','admin','shop_employee') NOT NULL DEFAULT 'self'
    COMMENT 'self=player portal | admin=management console | shop_employee=counter',

  `SubscriptionID`     INT(11)       DEFAULT NULL
    COMMENT 'FK → playersubscription; NULL for direct-pay bookings (facility_only, private)',

  `MedicalClearedBy`   INT(11)       DEFAULT NULL
    COMMENT 'FK → user (Admin); set when admin overrides an active-injury flag. NULL = no flag triggered.',

  `Status`             ENUM('pending','confirmed','cancelled','attended','missed') NOT NULL DEFAULT 'pending',
  `AmountCharged`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `PaymentStatus`      ENUM('not_required','pending','paid','refunded') NOT NULL DEFAULT 'not_required',
  `PaymentMethod`      ENUM('cash','card','online') DEFAULT NULL,
  `PaidAt`             DATETIME      DEFAULT NULL,

  `BookedBy`           INT(11)       DEFAULT NULL  COMMENT 'FK → user; NULL if self-booked',
  `CancelledBy`        INT(11)       DEFAULT NULL  COMMENT 'FK → user',
  `CancelledAt`        DATETIME      DEFAULT NULL,
  `CancelReason`       TEXT          DEFAULT NULL,

  `CreatedAt`          DATETIME      DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt`          DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`BookingID`),
  UNIQUE KEY `uq_booking_player_occurrence` (`OccurrenceID`, `PlayerID`),

  KEY `idx_sb_player`     (`PlayerID`),
  KEY `idx_sb_status`     (`Status`),
  KEY `idx_sb_payment`    (`PaymentStatus`),
  KEY `idx_sb_source`     (`BookingSource`),
  CONSTRAINT `fk_sb_occurrence`   FOREIGN KEY (`OccurrenceID`)   REFERENCES `slot_occurrence`(`OccurrenceID`),
  CONSTRAINT `fk_sb_player`       FOREIGN KEY (`PlayerID`)       REFERENCES `user`(`UserID`),
  CONSTRAINT `fk_sb_subscription` FOREIGN KEY (`SubscriptionID`) REFERENCES `playersubscription`(`SubscriptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Unified booking table. One row links one player to one occurrence.';


-- ================================================================
-- 7. slot_audit_log — Full Audit Trail
-- APPEND-ONLY. No UPDATE or DELETE ever.
-- The application must have no code path that modifies existing rows.
-- ================================================================
CREATE TABLE IF NOT EXISTS `slot_audit_log` (
  `LogID`        INT(11)      NOT NULL AUTO_INCREMENT,
  `EntityType`   ENUM('template','occurrence','booking','staff_assignment') NOT NULL
    COMMENT 'Which table was changed',
  `EntityID`     INT(11)      NOT NULL  COMMENT 'PK value of the changed row',
  `Action`       ENUM('create','update','cancel','delete','override') NOT NULL,
  `ChangedField` VARCHAR(100) DEFAULT NULL  COMMENT 'Column that changed, e.g. "Status", "FacilityID"',
  `OldValue`     TEXT         DEFAULT NULL,
  `NewValue`     TEXT         DEFAULT NULL,
  `Reason`       TEXT         DEFAULT NULL  COMMENT 'Justification entered by the user',
  `ChangedBy`    INT(11)      NOT NULL  COMMENT 'FK → user',
  `ChangedAt`    DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `IPAddress`    VARCHAR(45)  DEFAULT NULL,

  PRIMARY KEY (`LogID`),
  KEY `idx_scl_entity`  (`EntityType`, `EntityID`),
  KEY `idx_scl_changer` (`ChangedBy`),
  KEY `idx_scl_date`    (`ChangedAt`),
  CONSTRAINT `fk_scl_changer` FOREIGN KEY (`ChangedBy`) REFERENCES `user`(`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Append-only audit trail. Never UPDATE or DELETE rows in this table.';


-- ================================================================
-- Step 9: Migration Bridge
-- Copy existing session rows into slot_occurrence using the closest
-- matching slot_time_band. LegacySessionID preserves the link so
-- existing coachingsession, sessionenrollment, and sessionpayment
-- records remain queryable with no data loss.
-- Skip rows already migrated (WHERE NOT EXISTS guard).
-- ================================================================
INSERT INTO `slot_occurrence`
    ( TemplateID, SlotID, OccurrenceDate, FacilityID,
      Status, MaxParticipants, LegacySessionID, GeneratedBy )
SELECT
    NULL,
    -- Map old free-text StartTime to the nearest slot_time_band
    -- COALESCE defaults to band 1 (09:00 AM) for sessions before 09:00
    COALESCE(
        ( SELECT ts.SlotID
          FROM slot_time_band ts
          WHERE ts.StartTime <= s.StartTime
          ORDER BY ts.StartTime DESC
          LIMIT 1 ),
        1
    ),
    s.`Date`,
    NULL,   -- FacilityID unknown from old freetext; admin fills post-migration
    CASE s.`Status`
        WHEN 'active'    THEN 'active'
        WHEN 'completed' THEN 'completed'
        WHEN 'cancelled' THEN 'cancelled'
        ELSE 'scheduled'
    END,
    s.`MaxParticipants`,
    s.`SessionID`,  -- LegacySessionID bridge
    1               -- system/admin UserID
FROM `session` s
WHERE NOT EXISTS (
    SELECT 1 FROM `slot_occurrence` o
    WHERE o.`LegacySessionID` = s.`SessionID`
);

-- ================================================================
-- Done. Verify with:
--   SELECT COUNT(*) FROM slot_time_band;      -- expect 7
--   SELECT COUNT(*) FROM slot_occurrence;     -- expect >= old session count
--   SHOW CREATE TABLE slot_occurrence;        -- check UNIQUE KEY
-- ================================================================
