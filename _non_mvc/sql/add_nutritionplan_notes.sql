-- Add Notes column to NutritionPlan so trainers can customise predefined plans per player/group.
-- Run this against your database once.

ALTER TABLE NutritionPlan
  ADD COLUMN Notes TEXT NULL AFTER DietDetails;
