-- Remove 'Under 21' age group and map existing data to 'Open'
-- Open age group now represents players aged 19 and above.
--
-- This script is written to be resilient: it only updates tables/columns
-- that exist in the current database.

SET @db := DATABASE();

-- Helper pattern: conditionally run UPDATE if column exists.

-- player_skill_coach_assignment.AgeGroup
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'player_skill_coach_assignment' AND COLUMN_NAME = 'AgeGroup';
SET @sql := IF(@col_exists > 0,
  "UPDATE player_skill_coach_assignment SET AgeGroup='Open' WHERE AgeGroup='Under 21'",
  "SELECT 'skip: player_skill_coach_assignment.AgeGroup not found' AS info"
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- coach_skill_age_group_assignment.AgeGroup
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'coach_skill_age_group_assignment' AND COLUMN_NAME = 'AgeGroup';
SET @sql := IF(@col_exists > 0,
  "UPDATE coach_skill_age_group_assignment SET AgeGroup='Open' WHERE AgeGroup='Under 21'",
  "SELECT 'skip: coach_skill_age_group_assignment.AgeGroup not found' AS info"
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- slot_template.AgeGroup
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'slot_template' AND COLUMN_NAME = 'AgeGroup';
SET @sql := IF(@col_exists > 0,
  "UPDATE slot_template SET AgeGroup='Open' WHERE AgeGroup='Under 21'",
  "SELECT 'skip: slot_template.AgeGroup not found' AS info"
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- slot_template_player_assignment.AgeGroup
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'slot_template_player_assignment' AND COLUMN_NAME = 'AgeGroup';
SET @sql := IF(@col_exists > 0,
  "UPDATE slot_template_player_assignment SET AgeGroup='Open' WHERE AgeGroup='Under 21'",
  "SELECT 'skip: slot_template_player_assignment.AgeGroup not found' AS info"
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tournament.AgeGroup
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'tournament' AND COLUMN_NAME = 'AgeGroup';
SET @sql := IF(@col_exists > 0,
  "UPDATE tournament SET AgeGroup='Open' WHERE AgeGroup='Under 21'",
  "SELECT 'skip: tournament.AgeGroup not found' AS info"
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
