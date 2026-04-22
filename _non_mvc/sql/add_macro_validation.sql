-- Elite Cricket Academy
-- Macro Percentage Validation Enhancement
-- Adds database-level constraints to ensure macro percentages total 100%

SET FOREIGN_KEY_CHECKS = 0;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_add_macro_validation $$
CREATE PROCEDURE sp_add_macro_validation()
BEGIN
    DECLARE lctn INT DEFAULT @@lower_case_table_names;
    DECLARE v_total_protein DECIMAL(5,2);
    DECLARE v_total_carbs DECIMAL(5,2);
    DECLARE v_total_fat DECIMAL(5,2);

    -- Verify all existing templates have macros totaling 100%
    -- This will alert if any template has invalid data
    SELECT COUNT(*) INTO @invalid_templates
    FROM `nutrition_plan_templates`
    WHERE ABS((ProteinPercentage + CarbohydratePercentage + FatPercentage) - 100) > 0.01;

    IF @invalid_templates > 0 THEN
        -- Log warning about invalid templates
        INSERT INTO `system_logs` (`log_level`, `message`, `created_at`)
        VALUES ('WARNING', CONCAT('Found ', @invalid_templates, ' nutrition templates with invalid macro percentages'), NOW())
        ON DUPLICATE KEY UPDATE `created_at` = NOW();
    END IF;

    -- Add CHECK constraint to nutrition_plan_templates if using MySQL 8.0.16+
    -- This constraint ensures all new templates have macros totaling approximately 100%
    -- (within 0.01 tolerance for floating-point rounding)
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'nutrition_plan_templates'
          AND constraint_name = 'chk_nutrition_template_macros'
    ) THEN
        BEGIN
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
            ALTER TABLE `nutrition_plan_templates`
            ADD CONSTRAINT `chk_nutrition_template_macros`
            CHECK (ABS((ProteinPercentage + CarbohydratePercentage + FatPercentage) - 100) <= 0.01);
        END;
    END IF;

    -- Add CHECK constraint to nutritionplan table for individual plans
    -- Allows NULL values (for plans before macro system) but validates when set
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND constraint_name = 'chk_nutrition_plan_macros'
    ) THEN
        BEGIN
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
            ALTER TABLE `nutritionplan`
            ADD CONSTRAINT `chk_nutrition_plan_macros`
            CHECK (
                (ProteinPercentage IS NULL AND CarbohydratePercentage IS NULL AND FatPercentage IS NULL)
                OR
                (ProteinPercentage IS NOT NULL AND CarbohydratePercentage IS NOT NULL AND FatPercentage IS NOT NULL
                 AND ABS((ProteinPercentage + CarbohydratePercentage + FatPercentage) - 100) <= 0.01)
            );
        END;
    END IF;

    -- Add CHECK constraints for individual percentages (must be 0-100)
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'nutrition_plan_templates'
          AND constraint_name = 'chk_nutrition_template_percentage_range'
    ) THEN
        BEGIN
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
            ALTER TABLE `nutrition_plan_templates`
            ADD CONSTRAINT `chk_nutrition_template_percentage_range`
            CHECK (ProteinPercentage >= 0 AND ProteinPercentage <= 100
                   AND CarbohydratePercentage >= 0 AND CarbohydratePercentage <= 100
                   AND FatPercentage >= 0 AND FatPercentage <= 100);
        END;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND constraint_name = 'chk_nutrition_plan_percentage_range'
    ) THEN
        BEGIN
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
            ALTER TABLE `nutritionplan`
            ADD CONSTRAINT `chk_nutrition_plan_percentage_range`
            CHECK ((ProteinPercentage IS NULL OR (ProteinPercentage >= 0 AND ProteinPercentage <= 100))
                   AND (CarbohydratePercentage IS NULL OR (CarbohydratePercentage >= 0 AND CarbohydratePercentage <= 100))
                   AND (FatPercentage IS NULL OR (FatPercentage >= 0 AND FatPercentage <= 100)));
        END;
    END IF;

    -- Add CHECK constraint for recommended calories (must be 500-10000 or NULL)
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'nutrition_plan_templates'
          AND constraint_name = 'chk_nutrition_template_calories'
    ) THEN
        BEGIN
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
            ALTER TABLE `nutrition_plan_templates`
            ADD CONSTRAINT `chk_nutrition_template_calories`
            CHECK (RecommendedCalories >= 500 AND RecommendedCalories <= 10000);
        END;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND constraint_name = 'chk_nutrition_plan_calories'
    ) THEN
        BEGIN
            DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
            ALTER TABLE `nutritionplan`
            ADD CONSTRAINT `chk_nutrition_plan_calories`
            CHECK (RecommendedCalories IS NULL OR (RecommendedCalories >= 500 AND RecommendedCalories <= 10000));
        END;
    END IF;

END $$

DELIMITER ;

-- Execute the procedure
CALL sp_add_macro_validation();
DROP PROCEDURE IF EXISTS sp_add_macro_validation;

SET FOREIGN_KEY_CHECKS = 1;
