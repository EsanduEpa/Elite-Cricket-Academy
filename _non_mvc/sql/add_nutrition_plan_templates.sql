-- Elite Cricket Academy
-- Nutrition template migration
-- Adds reusable nutrition templates and structured nutrition plan fields.

SET FOREIGN_KEY_CHECKS = 0;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_elite_nutrition_template_migrate $$
CREATE PROCEDURE sp_elite_nutrition_template_migrate()
BEGIN
    DECLARE lctn INT DEFAULT @@lower_case_table_names;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = 'nutrition_plan_templates'
    ) THEN
        CREATE TABLE `nutrition_plan_templates` (
            `TemplateID` int(11) NOT NULL AUTO_INCREMENT,
            `PlanName` varchar(150) NOT NULL,
            `ProteinPercentage` decimal(5,2) NOT NULL,
            `CarbohydratePercentage` decimal(5,2) NOT NULL,
            `FatPercentage` decimal(5,2) NOT NULL,
            `RecommendedCalories` int(11) NOT NULL,
            `Description` text DEFAULT NULL,
            `SortOrder` int(11) NOT NULL DEFAULT 0,
            `IsActive` tinyint(1) NOT NULL DEFAULT 1,
            `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
            `UpdatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`TemplateID`),
            UNIQUE KEY `uq_nutrition_template_name` (`PlanName`),
            KEY `idx_nutrition_template_active` (`IsActive`, `SortOrder`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'nutrition_plan_templates'
          AND index_name = 'uq_nutrition_template_name'
    ) THEN
        ALTER TABLE `nutrition_plan_templates`
            ADD UNIQUE KEY `uq_nutrition_template_name` (`PlanName`);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'nutrition_plan_templates'
          AND index_name = 'idx_nutrition_template_active'
    ) THEN
        ALTER TABLE `nutrition_plan_templates`
            ADD KEY `idx_nutrition_template_active` (`IsActive`, `SortOrder`);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'TemplateID'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD COLUMN `TemplateID` int(11) DEFAULT NULL AFTER `TrainerID`;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'ProteinPercentage'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD COLUMN `ProteinPercentage` decimal(5,2) DEFAULT NULL AFTER `DietDetails`;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'CarbohydratePercentage'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD COLUMN `CarbohydratePercentage` decimal(5,2) DEFAULT NULL AFTER `ProteinPercentage`;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'FatPercentage'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD COLUMN `FatPercentage` decimal(5,2) DEFAULT NULL AFTER `CarbohydratePercentage`;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'RecommendedCalories'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD COLUMN `RecommendedCalories` int(11) DEFAULT NULL AFTER `FatPercentage`;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'Description'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD COLUMN `Description` text DEFAULT NULL AFTER `RecommendedCalories`;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND index_name = 'idx_nutrition_template'
    ) THEN
        ALTER TABLE `nutritionplan`
            ADD KEY `idx_nutrition_template` (`TemplateID`);
    END IF;

    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'nutritionPlanName'
    ) THEN
        UPDATE `nutritionplan` np
        INNER JOIN `nutrition_plan_templates` t
            ON t.`PlanName` = np.`nutritionPlanName`
        SET np.`TemplateID` = t.`TemplateID`
        WHERE np.`TemplateID` IS NULL;
    END IF;

    INSERT INTO `nutrition_plan_templates`
        (`PlanName`, `ProteinPercentage`, `CarbohydratePercentage`, `FatPercentage`, `RecommendedCalories`, `Description`, `SortOrder`, `IsActive`)
    VALUES
        ('High Protein', 45, 35, 20, 2400, 'Supports muscle repair and strength development with a protein-forward meal balance.', 1, 1),
        ('Low Carb', 40, 25, 35, 2200, 'Reduces carbohydrate load while keeping protein high for satiety and recovery.', 2, 1),
        ('Balanced Diet', 30, 40, 30, 2300, 'A balanced everyday plan for training consistency and general performance.', 3, 1),
        ('Weight Loss / Lean', 40, 30, 30, 1900, 'Uses controlled calories with higher protein to protect lean mass.', 4, 1),
        ('Recovery', 35, 45, 20, 2500, 'Prioritises glycogen replenishment and recovery nutrition after training or matches.', 5, 1),
        ('Hydration & Light Nutrition', 25, 50, 25, 2000, 'Keeps meals light and easy to digest while maintaining hydration and energy.', 6, 1)
    ON DUPLICATE KEY UPDATE
        `ProteinPercentage` = VALUES(`ProteinPercentage`),
        `CarbohydratePercentage` = VALUES(`CarbohydratePercentage`),
        `FatPercentage` = VALUES(`FatPercentage`),
        `RecommendedCalories` = VALUES(`RecommendedCalories`),
        `Description` = VALUES(`Description`),
        `SortOrder` = VALUES(`SortOrder`),
        `IsActive` = VALUES(`IsActive`),
        `UpdatedAt` = CURRENT_TIMESTAMP;

    IF lctn = 0 THEN
        CREATE OR REPLACE VIEW `NutritionPlan` AS
            SELECT
                `PlanID`,
                `TrainerID`,
                `TemplateID`,
                `PlayerID`,
                `nutritionPlanName`,
                `DietDetails`,
                `ProteinPercentage`,
                `CarbohydratePercentage`,
                `FatPercentage`,
                `RecommendedCalories`,
                `Description`,
                `Notes`,
                `Duration`,
                `Status`,
                `CreatedDate`
            FROM `nutritionplan`;
    END IF;
END $$

DELIMITER ;

CALL sp_elite_nutrition_template_migrate();
DROP PROCEDURE IF EXISTS sp_elite_nutrition_template_migrate;

SET FOREIGN_KEY_CHECKS = 1;
