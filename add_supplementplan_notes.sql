-- Add Notes and Status columns to SupplementPlan so trainers can customise predefined supplement plans per player/group.
-- Run this against your database once.

ALTER TABLE SupplementPlan
  ADD COLUMN Notes TEXT NULL AFTER SupplementDetails;

ALTER TABLE SupplementPlan
  ADD COLUMN Status ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER Duration;
