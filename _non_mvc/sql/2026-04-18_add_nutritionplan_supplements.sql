-- Add optional supplements guidance column to nutrition plans.
-- Trainers should only fill this when supplement recommendations are necessary.

DROP PROCEDURE IF EXISTS sp_add_nutritionplan_supplements;
DELIMITER $$
CREATE PROCEDURE sp_add_nutritionplan_supplements()
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
    ) AND NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nutritionplan'
          AND column_name = 'Supplements'
    ) THEN
        ALTER TABLE nutritionplan
            ADD COLUMN Supplements TEXT NULL AFTER DietDetails;
    END IF;
END $$
DELIMITER ;

CALL sp_add_nutritionplan_supplements();
DROP PROCEDURE IF EXISTS sp_add_nutritionplan_supplements;
