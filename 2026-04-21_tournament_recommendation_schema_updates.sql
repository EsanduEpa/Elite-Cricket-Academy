-- 2026-04-21 Tournament recommendation schema updates
-- Applies requested alterations:
-- 1) coach_tournament_recommendations: strict role enum + Captaincy + WicketKeeper
-- 2) trainer_tournament_recommendations: drop old columns + add FitnessRecommended (Yes/No)
--
-- Notes:
-- - This script includes normalization steps so existing free-text RecommendedRole values
--   are mapped into the new structure before converting to a strict ENUM.
-- - Some statements are conditional (via INFORMATION_SCHEMA + prepared statements)
--   to reduce "duplicate column" / "unknown column" failures across environments.

-- ------------------------------------------------------------
-- coach_tournament_recommendations
-- ------------------------------------------------------------

-- Add Captaincy column if missing
SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `coach_tournament_recommendations`\n  ADD COLUMN `Captaincy` ENUM(\'captain\', \'vice captain\', \'team member\') NOT NULL DEFAULT \'team member\' AFTER `RecommendedRole`',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'coach_tournament_recommendations'
      AND COLUMN_NAME = 'Captaincy'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add WicketKeeper column if missing
SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `coach_tournament_recommendations`\n  ADD COLUMN `WicketKeeper` ENUM(\'yes\', \'no\') NOT NULL DEFAULT \'no\' AFTER `Captaincy`',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'coach_tournament_recommendations'
      AND COLUMN_NAME = 'WicketKeeper'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Normalize existing RecommendedRole values into the new columns
-- Captaincy mapping
UPDATE `coach_tournament_recommendations`
SET `Captaincy` = CASE
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('captain') THEN 'captain'
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('vice captain', 'vice-captain', 'vice_captain', 'vicecaptain') THEN 'vice captain'
    ELSE `Captaincy`
END;

-- WicketKeeper mapping
UPDATE `coach_tournament_recommendations`
SET `WicketKeeper` = CASE
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('wicket keeper', 'wicket-keeper', 'wicket_keeper', 'keeper') THEN 'yes'
    ELSE `WicketKeeper`
END;

-- Role normalization: map free text to the new strict enum values (or NULL)
UPDATE `coach_tournament_recommendations`
SET `RecommendedRole` = CASE
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('bowler') THEN 'bowler'
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('batsman', 'batter') THEN 'batsman'
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('allrounder', 'all-rounder', 'all rounder', 'all_rounder') THEN 'allrounder'
    WHEN LOWER(TRIM(`RecommendedRole`)) IN ('captain', 'vice captain', 'vice-captain', 'vice_captain', 'vicecaptain', 'wicket keeper', 'wicket-keeper', 'wicket_keeper', 'keeper') THEN NULL
    WHEN TRIM(COALESCE(`RecommendedRole`, '')) = '' THEN NULL
    ELSE NULL
END;

-- Change RecommendedRole to a strict ENUM
ALTER TABLE `coach_tournament_recommendations`
  MODIFY COLUMN `RecommendedRole` ENUM('bowler', 'batsman', 'allrounder') DEFAULT NULL;


-- ------------------------------------------------------------
-- trainer_tournament_recommendations
-- ------------------------------------------------------------

-- Drop RecommendedRole if it exists
SET @sql := (
    SELECT IF(
        COUNT(*) > 0,
        'ALTER TABLE `trainer_tournament_recommendations` DROP COLUMN `RecommendedRole`',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainer_tournament_recommendations'
      AND COLUMN_NAME = 'RecommendedRole'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Drop Reason if it exists
SET @sql := (
    SELECT IF(
        COUNT(*) > 0,
        'ALTER TABLE `trainer_tournament_recommendations` DROP COLUMN `Reason`',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainer_tournament_recommendations'
      AND COLUMN_NAME = 'Reason'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add FitnessRecommended if missing
SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `trainer_tournament_recommendations`\n  ADD COLUMN `FitnessRecommended` ENUM(\'Yes\', \'No\') NOT NULL DEFAULT \'No\' AFTER `PlayerID`',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainer_tournament_recommendations'
      AND COLUMN_NAME = 'FitnessRecommended'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
