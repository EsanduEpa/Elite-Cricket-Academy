-- ============================================
-- PROFILE IMAGE COLUMN - DATABASE MIGRATION
-- ============================================
-- Run this SQL script in phpMyAdmin or MySQL command line
-- Database: cricket_academy
-- Table: User
-- Action: Add ProfileImage column

-- ============================================
-- OPTION 1: Simple Add Column (Recommended)
-- ============================================

USE cricket_academy;

ALTER TABLE User 
ADD COLUMN ProfileImage VARCHAR(255) DEFAULT NULL
COMMENT 'Path to user profile image';

-- ============================================
-- OPTION 2: Add with Position (After specific column)
-- ============================================

-- USE cricket_academy;
-- 
-- ALTER TABLE User 
-- ADD COLUMN ProfileImage VARCHAR(255) DEFAULT NULL
-- COMMENT 'Path to user profile image'
-- AFTER Email;

-- ============================================
-- OPTION 3: Check if column exists first (MySQL 5.7+)
-- ============================================

-- USE cricket_academy;
-- 
-- SET @dbname = DATABASE();
-- SET @tablename = 'User';
-- SET @columnname = 'ProfileImage';
-- SET @preparedStatement = (SELECT IF(
--   (
--     SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
--     WHERE
--       (table_name = @tablename)
--       AND (table_schema = @dbname)
--       AND (column_name = @columnname)
--   ) > 0,
--   'SELECT 1',
--   CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) DEFAULT NULL')
-- ));
-- PREPARE alterIfNotExists FROM @preparedStatement;
-- EXECUTE alterIfNotExists;
-- DEALLOCATE PREPARE alterIfNotExists;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if column was added successfully
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'cricket_academy'
  AND TABLE_NAME = 'User'
  AND COLUMN_NAME = 'ProfileImage';

-- Show all columns in User table
DESCRIBE User;

-- Count existing profile images
SELECT 
    COUNT(*) as total_users,
    COUNT(ProfileImage) as users_with_image,
    COUNT(*) - COUNT(ProfileImage) as users_without_image
FROM User;

-- ============================================
-- ROLLBACK (If you want to remove the column)
-- ============================================

-- USE cricket_academy;
-- ALTER TABLE User DROP COLUMN ProfileImage;

-- ============================================
-- SAMPLE DATA (For testing purposes)
-- ============================================

-- Update a test user with sample profile image
-- UPDATE User 
-- SET ProfileImage = 'uploads/profile_images/profile_1_1234567890.jpg'
-- WHERE UserID = 1;

-- ============================================
-- SUCCESS!
-- ============================================
-- Column ProfileImage has been added to the User table
-- You can now start uploading profile images!
