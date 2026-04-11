-- Elite Cricket Academy
-- Nutrition CRUD DB Migration
-- Date: 2026-04-08
--
-- Purpose
--   Ensure the Nutrition module can CREATE / READ / UPDATE / DELETE nutrition plans reliably.
--   This script:
--     1) Ensures required tables exist (`nutritionplan`, `nutritionplan_player`).
--     2) Adds missing columns used by the PHP code: PlayerID, Status, Notes.
--     3) Ensures keys/indexes exist (PK + helpful indexes).
--     4) Backfills `nutritionplan.PlayerID` from `nutritionplan_player` (first assigned player).
--     5) (Optional, only on case-sensitive MySQL) creates compatibility VIEWS with the
--        exact casing used in the PHP code: `NutritionPlan`, `User`, `PlayerProfile`, `TrainerProfile`.
--
-- How to run (phpMyAdmin)
--   1) Select your database
--   2) Import this file
--
-- How to run (CLI)
--   mysql -u root -p your_db_name < db_changes_nutrition_crud.sql

SET FOREIGN_KEY_CHECKS = 0;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_elite_nutrition_crud_migrate $$
CREATE PROCEDURE sp_elite_nutrition_crud_migrate()
BEGIN
	DECLARE lctn INT DEFAULT @@lower_case_table_names;

	/*
		1) Ensure base tables exist
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.tables
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
	) THEN
		CREATE TABLE `nutritionplan` (
			`PlanID` int(11) NOT NULL AUTO_INCREMENT,
			`TrainerID` int(11) NOT NULL,
			`PlayerID` int(11) DEFAULT NULL,
			`nutritionPlanName` varchar(255) DEFAULT NULL,
			`DietDetails` text NOT NULL,
			`Notes` text DEFAULT NULL,
			`Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
			`Status` enum('active','inactive') NOT NULL DEFAULT 'active',
			`CreatedDate` date DEFAULT curdate()
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.tables
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan_player'
	) THEN
		CREATE TABLE `nutritionplan_player` (
			`PlanID` int(11) NOT NULL,
			`PlayerID` int(11) NOT NULL,
			`AssignedDate` date DEFAULT curdate()
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	END IF;

	/*
		2) Add missing columns (safe / idempotent)
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND column_name = 'PlayerID'
	) THEN
		ALTER TABLE `nutritionplan`
			ADD COLUMN `PlayerID` int(11) DEFAULT NULL AFTER `TrainerID`;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND column_name = 'Notes'
	) THEN
		ALTER TABLE `nutritionplan`
			ADD COLUMN `Notes` text DEFAULT NULL AFTER `DietDetails`;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND column_name = 'Status'
	) THEN
		ALTER TABLE `nutritionplan`
			ADD COLUMN `Status` enum('active','inactive') NOT NULL DEFAULT 'active' AFTER `Duration`;
	END IF;

	/*
		3) Ensure primary key + auto increment
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND constraint_type = 'PRIMARY KEY'
	) THEN
		ALTER TABLE `nutritionplan`
			ADD PRIMARY KEY (`PlanID`);
	END IF;

	-- Harmless if already AUTO_INCREMENT
	ALTER TABLE `nutritionplan`
		MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT;

	/*
		4) Ensure indexes used by app queries
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.statistics
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND index_name = 'idx_trainer_nutrition'
	) THEN
		ALTER TABLE `nutritionplan`
			ADD KEY `idx_trainer_nutrition` (`TrainerID`);
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.statistics
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND index_name = 'idx_nutrition_player'
	) THEN
		ALTER TABLE `nutritionplan`
			ADD KEY `idx_nutrition_player` (`PlayerID`);
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'nutritionplan_player'
			AND constraint_type = 'PRIMARY KEY'
	) THEN
		ALTER TABLE `nutritionplan_player`
			ADD PRIMARY KEY (`PlanID`, `PlayerID`);
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.statistics
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan_player'
			AND index_name = 'PlayerID'
	) THEN
		ALTER TABLE `nutritionplan_player`
			ADD KEY `PlayerID` (`PlayerID`);
	END IF;

	/*
		5) Backfill single PlayerID column from assignment table (helps older queries)
	*/
	IF EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan'
			AND column_name = 'PlayerID'
	) AND EXISTS (
		SELECT 1
		FROM information_schema.tables
		WHERE table_schema = DATABASE()
			AND table_name = 'nutritionplan_player'
	) THEN
		UPDATE `nutritionplan` np
		JOIN (
			SELECT `PlanID`, MIN(`PlayerID`) AS `FirstPlayerID`
			FROM `nutritionplan_player`
			GROUP BY `PlanID`
		) x ON x.`PlanID` = np.`PlanID`
		SET np.`PlayerID` = x.`FirstPlayerID`
		WHERE np.`PlayerID` IS NULL;
	END IF;

	/*
		5b) Backfill required profile rows for FK constraints
				- nutritionplan.TrainerID references trainerprofile.TrainerID
				- nutritionplan_player.PlayerID references playerprofile.PlayerID
				If these rows are missing, INSERT/ASSIGN will fail and the UI shows a generic error.
	*/
	IF EXISTS (
		SELECT 1 FROM information_schema.tables
		WHERE table_schema = DATABASE() AND table_name = 'user'
	) AND EXISTS (
		SELECT 1 FROM information_schema.tables
		WHERE table_schema = DATABASE() AND table_name = 'trainerprofile'
	) THEN
		INSERT INTO `trainerprofile` (`TrainerID`)
		SELECT u.`UserID`
		FROM `user` u
		LEFT JOIN `trainerprofile` tp ON tp.`TrainerID` = u.`UserID`
		WHERE u.`Role` = 'Trainer'
			AND tp.`TrainerID` IS NULL;
	END IF;

	IF EXISTS (
		SELECT 1 FROM information_schema.tables
		WHERE table_schema = DATABASE() AND table_name = 'user'
	) AND EXISTS (
		SELECT 1 FROM information_schema.tables
		WHERE table_schema = DATABASE() AND table_name = 'playerprofile'
	) THEN
		INSERT INTO `playerprofile` (`PlayerID`)
		SELECT u.`UserID`
		FROM `user` u
		LEFT JOIN `playerprofile` pp ON pp.`PlayerID` = u.`UserID`
		WHERE u.`Role` = 'Player'
			AND u.`Status` = 'active'
			AND pp.`PlayerID` IS NULL;
	END IF;

	/*
		6) Case-sensitivity compatibility (Linux/MySQL lower_case_table_names = 0)
			 - Many PHP queries use `NutritionPlan`, `User`, `PlayerProfile`, `TrainerProfile`.
			 - Your schema uses lowercase `nutritionplan`, `user`, `playerprofile`, `trainerprofile`.
			 - On case-sensitive setups, that mismatch breaks CRUD.
			 - On Windows/macOS default MySQL configs (lower_case_table_names != 0), DO NOT create
				 these views because names collide.
	*/
	IF lctn = 0 THEN

		-- NutritionPlan view
		IF EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'nutritionplan'
				AND table_type = 'BASE TABLE'
		) AND NOT EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'NutritionPlan'
		) THEN
			SET @sql_np_view = CONCAT(
				'CREATE VIEW `NutritionPlan` AS ',
				'SELECT `PlanID`, `TrainerID`, `PlayerID`, `nutritionPlanName`, `DietDetails`, `Notes`, `Duration`, `Status`, `CreatedDate` ',
				'FROM `nutritionplan`'
			);
			PREPARE stmt FROM @sql_np_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;

		-- User view
		IF EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'user'
				AND table_type = 'BASE TABLE'
		) AND NOT EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'User'
		) THEN
			SET @sql_user_view = 'CREATE VIEW `User` AS SELECT * FROM `user`';
			PREPARE stmt FROM @sql_user_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;

		-- PlayerProfile view
		IF EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'playerprofile'
				AND table_type = 'BASE TABLE'
		) AND NOT EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'PlayerProfile'
		) THEN
			SET @sql_pp_view = 'CREATE VIEW `PlayerProfile` AS SELECT * FROM `playerprofile`';
			PREPARE stmt FROM @sql_pp_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;

		-- TrainerProfile view
		IF EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'trainerprofile'
				AND table_type = 'BASE TABLE'
		) AND NOT EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'TrainerProfile'
		) THEN
			SET @sql_tp_view = 'CREATE VIEW `TrainerProfile` AS SELECT * FROM `trainerprofile`';
			PREPARE stmt FROM @sql_tp_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;

	END IF;

END $$

DELIMITER ;

CALL sp_elite_nutrition_crud_migrate();
DROP PROCEDURE IF EXISTS sp_elite_nutrition_crud_migrate;

SET FOREIGN_KEY_CHECKS = 1;
