-- Add missing columns to WorkoutPlan table
-- Run this SQL to update the database structure for the new workout plan features

ALTER TABLE `workoutplan` 
ADD COLUMN `VideoLink` VARCHAR(500) DEFAULT NULL COMMENT 'Link to workout video' AFTER `Duration`,
ADD COLUMN `Intensity` ENUM('Light', 'Moderate', 'High', 'Extreme') DEFAULT 'Moderate' COMMENT 'Workout intensity level' AFTER `VideoLink`,
ADD COLUMN `NotSuitableFor` TEXT DEFAULT NULL COMMENT 'Conditions/injuries not suitable for this workout' AFTER `Intensity`,
ADD COLUMN `Benefits` TEXT DEFAULT NULL COMMENT 'Benefits of this workout plan' AFTER `NotSuitableFor`;

-- Optional: Add an index for better query performance
CREATE INDEX `idx_trainer_workout` ON `workoutplan`(`TrainerID`, `CreatedDate`);

-- Note: The column 'durationdays' referenced in code should map to existing 'Duration' column
-- Duration is already in days, so no new column needed for that
