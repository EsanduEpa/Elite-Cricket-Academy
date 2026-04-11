-- ================================================
-- Add Verification Columns to playermatchperformance
-- ================================================
-- This migration adds verification workflow columns
-- Run this after the table is created
-- Date: February 18, 2026
-- ================================================

-- Step 1: Add new columns for verification workflow
ALTER TABLE playermatchperformance
  ADD COLUMN VerifiedStatus ENUM('pending', 'verified', 'rejected') DEFAULT 'pending' 
    COMMENT 'Verification status of performance statistics' AFTER Rating,
  ADD COLUMN AddedBy INT NULL 
    COMMENT 'User who added this performance record' AFTER VerifiedStatus,
  ADD COLUMN VerifiedBy INT NULL 
    COMMENT 'Coach/Admin who verified this record' AFTER AddedBy,
  ADD COLUMN VerifiedAt DATETIME NULL 
    COMMENT 'When the record was verified' AFTER VerifiedBy,
  ADD COLUMN CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP 
    COMMENT 'When the record was created' AFTER VerifiedAt,
  ADD COLUMN UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
    COMMENT 'When the record was last updated' AFTER CreatedAt;

-- Step 2: Add indexes for performance optimization
ALTER TABLE playermatchperformance
  ADD INDEX idx_verified_status (VerifiedStatus),
  ADD INDEX idx_added_by (AddedBy),
  ADD INDEX idx_verified_by (VerifiedBy);

-- Step 3: Add foreign key constraints
-- Note: These reference the 'user' table. If your user table has a different name,
-- update the table name accordingly (e.g., 'users', 'playerprofile', etc.)
ALTER TABLE playermatchperformance
  ADD CONSTRAINT fk_performance_added_by 
    FOREIGN KEY (AddedBy) REFERENCES user(UserID) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_performance_verified_by 
    FOREIGN KEY (VerifiedBy) REFERENCES user(UserID) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- ================================================
-- Verification Query
-- ================================================
-- Run this to verify the changes were applied successfully

DESCRIBE playermatchperformance;

-- Expected output should include these new columns:
-- VerifiedStatus  | enum('pending','verified','rejected')
-- AddedBy         | int(11)
-- VerifiedBy      | int(11)
-- VerifiedAt      | datetime
-- CreatedAt       | datetime
-- UpdatedAt       | datetime
