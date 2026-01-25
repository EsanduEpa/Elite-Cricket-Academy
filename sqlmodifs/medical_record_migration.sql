-- =====================================================
-- Medical Record System Update - Database Migration
-- Date: October 22, 2025
-- Description: Add new columns to PlayerMedicalRecord table
-- =====================================================

-- Add new columns to PlayerMedicalRecord table
ALTER TABLE PlayerMedicalRecord
ADD COLUMN InjuryDate DATE NOT NULL COMMENT 'Date when the injury occurred' AFTER RecoveryStatus,
ADD COLUMN HappenedAtAcademy ENUM('yes', 'no') DEFAULT 'no' COMMENT 'Did the injury occur at the academy?' AFTER InjuryDate,
ADD COLUMN RestDaysNeeded INT DEFAULT 0 COMMENT 'Estimated rest days required for recovery' AFTER HappenedAtAcademy,
ADD COLUMN DiagnosisReceiptURL VARCHAR(255) COMMENT 'Path or URL of uploaded diagnosis receipt image/file' AFTER RestDaysNeeded;

-- =====================================================
-- Data Migration for Existing Records
-- =====================================================
-- For existing records, set InjuryDate = ReportedDate as a default
-- You may want to manually adjust these dates if more accurate information is available

UPDATE PlayerMedicalRecord 
SET InjuryDate = ReportedDate 
WHERE InjuryDate IS NULL OR InjuryDate = '0000-00-00';

-- =====================================================
-- Verify Migration
-- =====================================================
-- Check the updated table structure
-- DESCRIBE PlayerMedicalRecord;

-- Check sample data
-- SELECT RecordID, PlayerID, InjuryDate, HappenedAtAcademy, RestDaysNeeded, DiagnosisReceiptURL, ReportedDate 
-- FROM PlayerMedicalRecord 
-- LIMIT 5;

-- =====================================================
-- Rollback Script (if needed)
-- =====================================================
/*
-- Use this to rollback the changes if needed
ALTER TABLE PlayerMedicalRecord
DROP COLUMN InjuryDate,
DROP COLUMN HappenedAtAcademy,
DROP COLUMN RestDaysNeeded,
DROP COLUMN DiagnosisReceiptURL;
*/

-- =====================================================
-- Add Indexes for Performance (Recommended)
-- =====================================================
-- Add index on InjuryDate for faster sorting and filtering
ALTER TABLE PlayerMedicalRecord 
ADD INDEX idx_injury_date (InjuryDate);

-- Add index on HappenedAtAcademy for filtering academy injuries
ALTER TABLE PlayerMedicalRecord 
ADD INDEX idx_happened_at_academy (HappenedAtAcademy);

-- Add composite index for common queries
ALTER TABLE PlayerMedicalRecord 
ADD INDEX idx_player_injury_date (PlayerID, InjuryDate);

-- =====================================================
-- Complete Table Structure After Migration
-- =====================================================
/*
Expected Structure:

TABLE PlayerMedicalRecord (
    RecordID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    InjuryDetails TEXT NOT NULL,
    Diagnosis TEXT,
    TreatmentGiven TEXT,
    RecoveryStatus ENUM('recovering', 'recovered', 'chronic', 'ongoing') DEFAULT 'ongoing',
    InjuryDate DATE NOT NULL,
    HappenedAtAcademy ENUM('yes', 'no') DEFAULT 'no',
    RestDaysNeeded INT DEFAULT 0,
    DiagnosisReceiptURL VARCHAR(255),
    ReportedDate DATE NOT NULL,
    ReportedBy INT,
    verifyStatus ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    
    FOREIGN KEY (PlayerID) REFERENCES User(UserID),
    FOREIGN KEY (ReportedBy) REFERENCES User(UserID),
    
    INDEX idx_injury_date (InjuryDate),
    INDEX idx_happened_at_academy (HappenedAtAcademy),
    INDEX idx_player_injury_date (PlayerID, InjuryDate)
);
*/
