START TRANSACTION;

SET @supplements_col_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'nutritionplan'
      AND COLUMN_NAME = 'Supplements'
);

SET @sql := IF(
    @supplements_col_exists = 0,
    'ALTER TABLE nutritionplan ADD COLUMN Supplements TEXT NULL AFTER Notes',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
