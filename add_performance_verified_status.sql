-- Add VerifiedStatus column to PlayerMatchPerformance table
-- This allows tracking whether performance statistics are verified by coaches

ALTER TABLE playermatchperformance
  ADD COLUMN VerifiedStatus ENUM('pending', 'verified', 'rejected') DEFAULT 'pending' 
    COMMENT 'Verification status of performance statistics' AFTER Rating,
  ADD COLUMN VerifiedBy INT NULL 
    COMMENT 'Coach/Admin who verified this record' AFTER AddedBy,
  ADD COLUMN VerifiedAt DATETIME NULL 
    COMMENT 'When the record was verified' AFTER VerifiedBy,
  ADD COLUMN CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP AFTER VerifiedAt,
  ADD COLUMN UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER CreatedAt,
  ADD INDEX idx_verified_status (VerifiedStatus),
  ADD INDEX idx_added_by (AddedBy);

-- Add foreign key constraints
ALTER TABLE playermatchperformance
  ADD CONSTRAINT fk_performance_added_by FOREIGN KEY (AddedBy) REFERENCES user(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_performance_verified_by FOREIGN KEY (VerifiedBy) REFERENCES user(UserID) ON DELETE SET NULL ON UPDATE CASCADE;
