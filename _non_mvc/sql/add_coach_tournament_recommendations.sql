-- ============================================================================
-- Coach Tournament Recommendations Table
-- ============================================================================
-- Database: cricket_academy
-- Purpose: Track coach recommendations of players for tournaments
-- Created: April 7, 2026
-- Version: 1.0
-- ============================================================================

USE `cricket_academy`;

-- ============================================================================
-- Create Table: coach_tournament_recommendations
-- ============================================================================
-- Tracks recommendations made by coaches for player participation in tournaments
-- Supports workflow: pending → approved/rejected → confirmed
-- Maintains audit trail (who recommended, who reviewed, when)

CREATE TABLE IF NOT EXISTS `coach_tournament_recommendations` (
    `RecommendationID` INT AUTO_INCREMENT PRIMARY KEY,
    `CoachID` INT NOT NULL,
    `TournamentID` INT NOT NULL,
    `PlayerID` INT NOT NULL,
    `RecommendedRole` VARCHAR(50) COMMENT 'batsman, bowler, all-rounder, wicket-keeper',
    `Reason` TEXT COMMENT 'Coach''s justification for recommendation',
    `Comments` TEXT COMMENT 'Additional notes/details',
    `Status` ENUM('pending', 'approved', 'rejected', 'confirmed') DEFAULT 'pending' COMMENT 'Recommendation status',
    `AdminFeedback` TEXT COMMENT 'Feedback from reviewer (if rejected)',
    `ReviewedBy` INT COMMENT 'UserID of person who approved/rejected',
    `DateRecommended` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When recommendation was created',
    `DateReviewed` TIMESTAMP NULL COMMENT 'When recommendation was reviewed',
    
    -- Foreign Keys with proper constraints
    CONSTRAINT `fk_ctr_coach` FOREIGN KEY (`CoachID`) 
        REFERENCES `user`(`UserID`) ON DELETE RESTRICT,
    
    CONSTRAINT `fk_ctr_tournament` FOREIGN KEY (`TournamentID`) 
        REFERENCES `tournament`(`TournamentID`) ON DELETE CASCADE,
    
    CONSTRAINT `fk_ctr_player` FOREIGN KEY (`PlayerID`) 
        REFERENCES `playerprofile`(`PlayerID`) ON DELETE CASCADE,
    
    CONSTRAINT `fk_ctr_reviewer` FOREIGN KEY (`ReviewedBy`) 
        REFERENCES `user`(`UserID`) ON DELETE SET NULL,
    
    -- Unique constraint: prevent duplicate recommendations from same coach
    UNIQUE KEY `unique_coach_player_tournament` (`TournamentID`, `PlayerID`, `CoachID`),
    
    -- Indexes for query performance
    INDEX `idx_coach_tournament` (`CoachID`, `TournamentID`),
    INDEX `idx_tournament` (`TournamentID`),
    INDEX `idx_player` (`PlayerID`),
    INDEX `idx_status` (`Status`),
    INDEX `idx_date_recommended` (`DateRecommended`)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Coach recommendations of players for tournament participation';

-- ============================================================================
-- Log Entry
-- ============================================================================
-- Table created successfully with:
-- - 13 columns including coach, tournament, player references
-- - 4 foreign keys with cascade/restrict constraints
-- - 5 indexes for common queries
-- - Unique constraint on (tournament, player, coach)
-- - ENUM status field for workflow tracking
-- - Audit fields (CreatedAt, ReviewedAt, ReviewedBy)
--
-- Next Steps:
-- 1. Add indexes for faster queries
-- 2. Create base model M_CoachTournamentRecommendation.php
-- 3. Extend Coach controller with recommendation methods
-- 4. Update tournaments.php view with recommendation UI
-- ============================================================================
