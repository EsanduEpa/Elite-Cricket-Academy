-- =============================================================================
-- TOURNAMENT MODULE MIGRATION FOR EXISTING ELITE DATABASE
-- Target DB: existing schema from cricket_academy (14).sql
-- Generated: 10 April 2026
--
-- Notes:
-- 1. Take a backup before running this script.
-- 2. This script keeps ID columns as int(11) to match the current user table.
-- 3. Existing foreign keys on player/coach profile tables are moved to user.UserID
--    because profile IDs mirror user IDs in this schema.
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. Normalize existing data so enum changes do not fail
-- -----------------------------------------------------------------------------
UPDATE `tournament`
SET `Status` = 'created'
WHERE `Status` IS NULL OR `Status` = '';

UPDATE `coach_tournament_recommendations`
SET `Status` = 'approved'
WHERE `Status` = 'confirmed';


-- -----------------------------------------------------------------------------
-- 2. Align tournament table with required definition
-- -----------------------------------------------------------------------------
ALTER TABLE `tournament`
  MODIFY COLUMN `Name` VARCHAR(200) NOT NULL,
  MODIFY COLUMN `AgeGroup` VARCHAR(50) DEFAULT NULL,
  MODIFY COLUMN `Format` ENUM('T20','ODI','Test','Other') DEFAULT 'T20',
  MODIFY COLUMN `Description` TEXT DEFAULT NULL,
  MODIFY COLUMN `tdate` DATE NOT NULL COMMENT 'Tournament date',
  MODIFY COLUMN `RegistrationDeadline` DATE DEFAULT NULL,
  MODIFY COLUMN `MaxPlayers` TINYINT UNSIGNED DEFAULT NULL,
  MODIFY COLUMN `Location` VARCHAR(200) DEFAULT NULL,
  MODIFY COLUMN `CreatedBy` INT(11) NOT NULL COMMENT 'FK -> user.UserID (admin)',
  MODIFY COLUMN `Status` ENUM(
    'created',
    'registration_open',
    'registration_closed',
    'team_announced',
    'ongoing',
    'completed',
    'cancelled'
  ) NOT NULL DEFAULT 'created',
  MODIFY COLUMN `IsTeamAnnounced` TINYINT(1) NOT NULL DEFAULT 0,
  MODIFY COLUMN `CancelReason` TEXT DEFAULT NULL,
  MODIFY COLUMN `CreatedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY COLUMN `UpdatedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  MODIFY COLUMN `PrizePool` DECIMAL(10,2) DEFAULT 0.00;


-- -----------------------------------------------------------------------------
-- 3. Create tournament_join_request if missing
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tournament_join_request` (
  `RequestID` INT(11) NOT NULL AUTO_INCREMENT,
  `TournamentID` INT(11) NOT NULL,
  `PlayerID` INT(11) NOT NULL COMMENT 'FK -> user.UserID',
  `Message` TEXT DEFAULT NULL,
  `Status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ReviewedBy` INT(11) DEFAULT NULL COMMENT 'FK -> user.UserID (admin/coach)',
  `ReviewNotes` TEXT DEFAULT NULL,
  `RequestedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ReviewedAt` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`RequestID`),
  UNIQUE KEY `uq_join_request` (`TournamentID`, `PlayerID`),
  KEY `idx_tjr_player` (`PlayerID`),
  KEY `idx_tjr_status` (`Status`),
  KEY `idx_tjr_reviewer` (`ReviewedBy`),
  CONSTRAINT `fk_tjr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE,
  CONSTRAINT `fk_tjr_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  CONSTRAINT `fk_tjr_reviewer` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- 4. Upgrade coach_tournament_recommendations
-- -----------------------------------------------------------------------------
ALTER TABLE `coach_tournament_recommendations`
  DROP FOREIGN KEY `fk_ctr_player`;

ALTER TABLE `coach_tournament_recommendations`
  DROP INDEX `unique_coach_player_tournament`;

ALTER TABLE `coach_tournament_recommendations`
  CHANGE COLUMN `AdminFeedback` `ReviewFeedback` TEXT DEFAULT NULL COMMENT 'Feedback from reviewer (if rejected)',
  MODIFY COLUMN `CoachID` INT(11) NOT NULL COMMENT 'FK -> user.UserID',
  MODIFY COLUMN `TournamentID` INT(11) NOT NULL,
  MODIFY COLUMN `PlayerID` INT(11) NOT NULL COMMENT 'FK -> user.UserID',
  MODIFY COLUMN `RecommendedRole` VARCHAR(100) DEFAULT NULL,
  MODIFY COLUMN `Reason` TEXT DEFAULT NULL,
  MODIFY COLUMN `Comments` TEXT DEFAULT NULL,
  MODIFY COLUMN `Status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  MODIFY COLUMN `ReviewedBy` INT(11) DEFAULT NULL COMMENT 'FK -> user.UserID (admin)',
  MODIFY COLUMN `DateRecommended` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY COLUMN `DateReviewed` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `coach_tournament_recommendations`
  ADD UNIQUE KEY `uq_coach_rec` (`CoachID`, `TournamentID`, `PlayerID`),
  ADD CONSTRAINT `fk_ctr_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`);


-- -----------------------------------------------------------------------------
-- 5. Create trainer_tournament_recommendations if missing
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trainer_tournament_recommendations` (
  `RecommendationID` INT(11) NOT NULL AUTO_INCREMENT,
  `TrainerID` INT(11) NOT NULL COMMENT 'FK -> user.UserID',
  `TournamentID` INT(11) NOT NULL,
  `PlayerID` INT(11) NOT NULL COMMENT 'FK -> user.UserID',
  `RecommendedRole` VARCHAR(100) DEFAULT NULL,
  `Reason` TEXT DEFAULT NULL,
  `Comments` TEXT DEFAULT NULL,
  `Status` ENUM('pending','reviewed','confirmed','rejected') NOT NULL DEFAULT 'pending',
  `ReviewedBy` INT(11) DEFAULT NULL COMMENT 'FK -> user.UserID (head coach)',
  `DateRecommended` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DateReviewed` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`RecommendationID`),
  UNIQUE KEY `uq_trainer_rec` (`TrainerID`, `TournamentID`, `PlayerID`),
  KEY `idx_ttr_player` (`PlayerID`),
  KEY `idx_ttr_tournament` (`TournamentID`),
  KEY `idx_ttr_reviewed_by` (`ReviewedBy`),
  KEY `idx_ttr_status` (`Status`),
  CONSTRAINT `fk_ttr_trainer` FOREIGN KEY (`TrainerID`) REFERENCES `user` (`UserID`),
  CONSTRAINT `fk_ttr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE,
  CONSTRAINT `fk_ttr_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  CONSTRAINT `fk_ttr_reviewer` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- 6. Upgrade tournamentplayer to selected squad structure
-- -----------------------------------------------------------------------------
ALTER TABLE `tournamentplayer`
  DROP FOREIGN KEY `tournamentplayer_ibfk_1`,
  DROP FOREIGN KEY `tournamentplayer_ibfk_2`,
  DROP FOREIGN KEY `tournamentplayer_ibfk_3`;

ALTER TABLE `tournamentplayer`
  DROP PRIMARY KEY,
  ADD COLUMN `TournamentPlayerID` INT(11) NOT NULL AUTO_INCREMENT FIRST,
  ADD PRIMARY KEY (`TournamentPlayerID`),
  ADD UNIQUE KEY `uq_tournament_player` (`TournamentID`, `PlayerID`);

ALTER TABLE `tournamentplayer`
  MODIFY COLUMN `TournamentID` INT(11) NOT NULL,
  MODIFY COLUMN `PlayerID` INT(11) NOT NULL COMMENT 'FK -> user.UserID',
  MODIFY COLUMN `Team` VARCHAR(100) DEFAULT 'Academy Team',
  MODIFY COLUMN `RoleInTeam` VARCHAR(100) DEFAULT NULL,
  MODIFY COLUMN `SelectedBy` INT(11) DEFAULT NULL COMMENT 'FK -> user.UserID (head coach)',
  ADD COLUMN `SelectionStatus` ENUM('draft','confirmed') NOT NULL DEFAULT 'draft' AFTER `SelectedBy`,
  ADD COLUMN `SelectedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `SelectionStatus`;

ALTER TABLE `tournamentplayer`
  ADD CONSTRAINT `fk_tp_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tp_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_tp_selected` FOREIGN KEY (`SelectedBy`) REFERENCES `user` (`UserID`);


-- -----------------------------------------------------------------------------
-- 7. Optional verification queries
-- -----------------------------------------------------------------------------
-- SHOW CREATE TABLE `tournament`;
-- SHOW CREATE TABLE `tournament_join_request`;
-- SHOW CREATE TABLE `coach_tournament_recommendations`;
-- SHOW CREATE TABLE `trainer_tournament_recommendations`;
-- SHOW CREATE TABLE `tournamentplayer`;

SET FOREIGN_KEY_CHECKS = 1;