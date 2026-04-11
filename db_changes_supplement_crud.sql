-- Elite Cricket Academy
-- Supplement CRUD DB Migration
-- Date: 2026-04-09
--
-- Purpose
--   Ensure the Supplement module can CREATE / READ / UPDATE / DELETE plans reliably.
--   This script:
--     1) Ensures the base tables exist (`supplementplan`, `supplement_player`).
--     2) Adds missing columns used by the PHP code: PlayerID, Notes, Status.
--     3) Ensures keys/indexes exist for trainer and player lookups.
--     4) Backfills trainer/player profile rows needed by foreign keys.
--     5) Creates compatibility views on case-sensitive MySQL installs.
--
-- How to run (phpMyAdmin)
--   1) Select your database
--   2) Import this file
--
-- How to run (CLI)
--   mysql -u root -p your_db_name < db_changes_supplement_crud.sql

SET FOREIGN_KEY_CHECKS = 0;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_elite_supplement_crud_migrate $$
CREATE PROCEDURE sp_elite_supplement_crud_migrate()
BEGIN
	DECLARE lctn INT DEFAULT @@lower_case_table_names;

	/*
		1) Ensure base tables exist
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.tables
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
	) THEN
		CREATE TABLE `supplementplan` (
			`PlanID` int(11) NOT NULL AUTO_INCREMENT,
			`TrainerID` int(11) NOT NULL,
			`PlayerID` int(11) DEFAULT NULL,
			`SupplementPlanName` varchar(255) DEFAULT NULL,
			`SupplementDetails` text NOT NULL,
			`Notes` text DEFAULT NULL,
			`Dosage` varchar(255) DEFAULT NULL,
			`Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
			`Status` enum('active','inactive') NOT NULL DEFAULT 'active',
			`CreatedDate` date DEFAULT curdate(),
			PRIMARY KEY (`PlanID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Supplement recommendations for players';
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.tables
		WHERE table_schema = DATABASE()
			AND table_name = 'supplement_player'
	) THEN
		CREATE TABLE `supplement_player` (
			`PlanID` int(11) NOT NULL,
			`PlayerID` int(11) NOT NULL,
			`AssignedDate` date DEFAULT curdate()
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	END IF;

	/*
		2) Add missing columns used by the app
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND column_name = 'PlayerID'
	) THEN
		ALTER TABLE `supplementplan`
			ADD COLUMN `PlayerID` int(11) DEFAULT NULL AFTER `TrainerID`;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND column_name = 'Notes'
	) THEN
		ALTER TABLE `supplementplan`
			ADD COLUMN `Notes` text DEFAULT NULL AFTER `SupplementDetails`;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND column_name = 'Status'
	) THEN
		ALTER TABLE `supplementplan`
			ADD COLUMN `Status` enum('active','inactive') NOT NULL DEFAULT 'active' AFTER `Duration`;
	END IF;

	/*
		3) Ensure primary key + auto increment
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND constraint_type = 'PRIMARY KEY'
	) THEN
		ALTER TABLE `supplementplan`
			ADD PRIMARY KEY (`PlanID`);
	END IF;

	ALTER TABLE `supplementplan`
		MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT;

	/*
		4) Ensure indexes used by app queries
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.statistics
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND index_name = 'idx_trainer_supplement'
	) THEN
		ALTER TABLE `supplementplan`
			ADD KEY `idx_trainer_supplement` (`TrainerID`);
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.statistics
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND index_name = 'idx_supplement_player'
	) THEN
		ALTER TABLE `supplementplan`
			ADD KEY `idx_supplement_player` (`PlayerID`);
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'supplement_player'
			AND constraint_type = 'PRIMARY KEY'
	) THEN
		ALTER TABLE `supplement_player`
			ADD PRIMARY KEY (`PlanID`, `PlayerID`);
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.statistics
		WHERE table_schema = DATABASE()
			AND table_name = 'supplement_player'
			AND index_name = 'PlayerID'
	) THEN
		ALTER TABLE `supplement_player`
			ADD KEY `PlayerID` (`PlayerID`);
	END IF;

	/*
		5) Ensure foreign keys exist
	*/
	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND constraint_name = 'supplementplan_ibfk_1'
	) AND EXISTS (
		SELECT 1 FROM information_schema.tables
		WHERE table_schema = DATABASE() AND table_name = 'trainerprofile'
	) THEN
		ALTER TABLE `supplementplan`
			ADD CONSTRAINT `supplementplan_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'supplement_player'
			AND constraint_name = 'supplement_player_ibfk_1'
	) THEN
		ALTER TABLE `supplement_player`
			ADD CONSTRAINT `supplement_player_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `supplementplan` (`PlanID`) ON DELETE CASCADE ON UPDATE CASCADE;
	END IF;

	IF NOT EXISTS (
		SELECT 1
		FROM information_schema.table_constraints
		WHERE constraint_schema = DATABASE()
			AND table_name = 'supplement_player'
			AND constraint_name = 'supplement_player_ibfk_2'
	) AND EXISTS (
		SELECT 1 FROM information_schema.tables
		WHERE table_schema = DATABASE() AND table_name = 'playerprofile'
	) THEN
		ALTER TABLE `supplement_player`
			ADD CONSTRAINT `supplement_player_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;
	END IF;

	/*
		6) Backfill profile rows required by FK constraints
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
		7) Backfill the optional PlayerID column from assignments
	*/
	IF EXISTS (
		SELECT 1
		FROM information_schema.columns
		WHERE table_schema = DATABASE()
			AND table_name = 'supplementplan'
			AND column_name = 'PlayerID'
	) AND EXISTS (
		SELECT 1
		FROM information_schema.tables
		WHERE table_schema = DATABASE()
			AND table_name = 'supplement_player'
	) THEN
		UPDATE `supplementplan` sp
		JOIN (
			SELECT `PlanID`, MIN(`PlayerID`) AS `FirstPlayerID`
			FROM `supplement_player`
			GROUP BY `PlanID`
		) x ON x.`PlanID` = sp.`PlanID`
		SET sp.`PlayerID` = x.`FirstPlayerID`
		WHERE sp.`PlayerID` IS NULL;
	END IF;

	/*
		8) Case-sensitivity compatibility views for Linux installs
	*/
	IF lctn = 0 THEN
		IF EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'supplementplan'
				AND table_type = 'BASE TABLE'
		) AND NOT EXISTS (
			SELECT 1 FROM information_schema.tables
			WHERE table_schema = DATABASE()
				AND BINARY table_name = 'SupplementPlan'
		) THEN
			SET @sql_supplement_plan_view = 'CREATE VIEW `SupplementPlan` AS SELECT * FROM `supplementplan`';
			PREPARE stmt FROM @sql_supplement_plan_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;

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
			SET @sql_player_profile_view = 'CREATE VIEW `PlayerProfile` AS SELECT * FROM `playerprofile`';
			PREPARE stmt FROM @sql_player_profile_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;

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
			SET @sql_trainer_profile_view = 'CREATE VIEW `TrainerProfile` AS SELECT * FROM `trainerprofile`';
			PREPARE stmt FROM @sql_trainer_profile_view;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;
	END IF;
END $$

DELIMITER ;

CALL sp_elite_supplement_crud_migrate();

DROP PROCEDURE IF EXISTS sp_elite_supplement_crud_migrate;

SET FOREIGN_KEY_CHECKS = 1;