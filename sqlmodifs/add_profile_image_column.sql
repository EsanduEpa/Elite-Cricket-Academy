-- Add ProfileImage column to User table
-- This migration adds a column to store the profile image path for users

USE cricket_academy;

-- Add ProfileImage column if it doesn't exist
ALTER TABLE User 
ADD COLUMN IF NOT EXISTS ProfileImage VARCHAR(255) DEFAULT NULL 
COMMENT 'Relative path to user profile image (e.g., uploads/profile_images/profile_1_123456789.jpg)';

-- Add index for faster queries (optional but recommended)
CREATE INDEX IF NOT EXISTS idx_profile_image ON User(ProfileImage);

-- Display success message
SELECT 'ProfileImage column added successfully!' AS Status;

-- Show the updated table structure
DESCRIBE User;
