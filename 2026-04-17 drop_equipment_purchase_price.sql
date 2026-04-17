-- Drop PurchasePrice from equipment
-- Run this on the target database (e.g., via phpMyAdmin / MySQL client)

ALTER TABLE `equipment`
  DROP COLUMN `PurchasePrice`;
