-- 2026-04-20: Add doctor referral flag to player medical records.
-- Supports both `PlayerMedicalRecord` and `playermedicalrecord` (case varies by server settings).

SET @medical_table := (
    SELECT TABLE_NAME
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
      AND LOWER(table_name) = 'playermedicalrecord'
    LIMIT 1
);

SET @add_col_stmt := IF(
    @medical_table IS NULL,
    'SELECT "WARN: playermedicalrecord table not found in current schema" AS info;',
    CONCAT(
        'ALTER TABLE `', @medical_table, '` ',
        'ADD COLUMN IF NOT EXISTS `Dr_reference` ENUM(\'yes\',\'no\') NOT NULL DEFAULT \'no\''
    )
);

PREPARE stmt FROM @add_col_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
