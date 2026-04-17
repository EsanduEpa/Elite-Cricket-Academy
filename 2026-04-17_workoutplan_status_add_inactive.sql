-- ============================================================
-- Fix workoutplan.Status to support 'inactive'
-- Date: 2026-04-17
-- Database: cricket_academy
--
-- Symptom:
-- - Selecting status = 'inactive' results in blank Status in DB and UI.
-- Cause:
-- - workoutplan.Status ENUM does not include 'inactive'. MySQL stores
--   invalid ENUM assignments as empty string ('') when not in strict mode.
-- ============================================================

-- 1) Expand the Status ENUM to include 'inactive'
-- Keep existing lifecycle values used by the app: active/inactive/draft/archived
ALTER TABLE `workoutplan`
  MODIFY COLUMN `Status` ENUM('active','inactive','draft','archived') NOT NULL DEFAULT 'active';

-- 2) Backfill invalid/empty values (usually created by earlier attempts
--    to save 'inactive' before the ENUM supported it)
UPDATE `workoutplan`
SET `Status` = 'inactive'
WHERE `Status` = '' OR `Status` IS NULL;
