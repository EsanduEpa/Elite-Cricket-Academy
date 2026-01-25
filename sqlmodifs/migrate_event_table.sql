-- =============================================================================
-- EVENT TABLE MIGRATION SCRIPT
-- =============================================================================
-- Purpose: Update Event table from original schema to new enhanced structure
-- Date: October 21, 2025
-- Instructions: Run this script on the cricket_academy database
-- =============================================================================

USE cricket_academy;

-- Step 1: Drop the old Event table (will also drop foreign keys)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Event;
SET FOREIGN_KEY_CHECKS = 1;

-- Step 2: Create the new enhanced Event table
CREATE TABLE Event (
    EventID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    
    -- Enhanced Type field with more options
    Type ENUM(
        'Training Camp', 
        'Workshop', 
        'Seminar', 
        'Competition', 
        'Tournament', 
        'Match', 
        'Trial', 
        'Meeting', 
        'Other'
    ) DEFAULT NULL,
    
    -- NEW: Category field for event classification
    Category ENUM(
        'junior', 
        'senior', 
        'youth', 
        'professional', 
        'recreational', 
        'academy'
    ) DEFAULT NULL,
    
    Description TEXT,
    
    -- Changed from DATE to DATETIME for better scheduling
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    
    Location VARCHAR(255),
    
    -- Enhanced Status field with more states
    Status ENUM(
        'upcoming', 
        'registration_open', 
        'registration_closed', 
        'ongoing', 
        'completed', 
        'cancelled'
    ) DEFAULT 'upcoming',
    
    -- NEW: Registration management fields
    RegistrationStart DATETIME DEFAULT NULL,
    RegistrationEnd DATETIME DEFAULT NULL,
    
    -- NEW: Contact information fields
    PrimaryContact VARCHAR(255) DEFAULT NULL,
    ContactEmail VARCHAR(255) DEFAULT NULL,
    ContactPhone VARCHAR(20) DEFAULT NULL,
    
    -- NEW: Participant management
    MaxParticipants INT DEFAULT NULL,
    RegistrationFee DECIMAL(10,2) DEFAULT 0.00,
    
    -- Indexes for performance
    INDEX idx_start_date (StartDate),
    INDEX idx_status (Status),
    INDEX idx_category (Category),
    INDEX idx_registration_start (RegistrationStart),
    INDEX idx_registration_end (RegistrationEnd),
    INDEX idx_contact_email (ContactEmail)
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci 
COMMENT='Academy events and training camps';

-- Step 3: Recreate EventEnrollment table with proper foreign key
DROP TABLE IF EXISTS EventEnrollment;

CREATE TABLE EventEnrollment (
    EnrollmentID INT AUTO_INCREMENT PRIMARY KEY,
    EventID INT NOT NULL,
    PlayerID INT NOT NULL,
    EnrollmentDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('enrolled', 'attended', 'cancelled') DEFAULT 'enrolled',
    
    FOREIGN KEY (EventID) REFERENCES Event(EventID) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_event_enrollment (EventID, PlayerID),
    INDEX idx_player_enrollment (PlayerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Player enrollment in events';

-- =============================================================================
-- SUMMARY OF CHANGES
-- =============================================================================

/*
CHANGES FROM ORIGINAL SCHEMA:

1. REMOVED FIELDS:
   - OrganizedBy (INT, Foreign Key to User table)

2. MODIFIED FIELDS:
   - StartDate: Changed from DATE to DATETIME
   - EndDate: Changed from DATE to DATETIME
   - Type: Added more options (Tournament, Match, Trial, Meeting)
   - Status: Added more states (registration_open, registration_closed)

3. NEW FIELDS ADDED:
   - Category: ENUM for event classification (junior/senior/youth/professional/recreational/academy)
   - RegistrationStart: DATETIME for registration open date
   - RegistrationEnd: DATETIME for registration close date
   - PrimaryContact: VARCHAR(255) for main contact person
   - ContactEmail: VARCHAR(255) for contact email
   - ContactPhone: VARCHAR(20) for contact phone number
   - MaxParticipants: INT for participant limit
   - RegistrationFee: DECIMAL(10,2) for event registration fee

4. NEW INDEXES ADDED:
   - idx_category
   - idx_registration_start
   - idx_registration_end
   - idx_contact_email
*/

-- =============================================================================
-- VERIFICATION QUERY
-- =============================================================================

-- Run this to verify the new structure
SHOW CREATE TABLE Event\G

-- Run this to check if table is empty (should be 0 initially)
SELECT COUNT(*) AS TotalEvents FROM Event;

-- =============================================================================
-- SCRIPT COMPLETE
-- =============================================================================

SELECT 'Event table migration completed successfully!' AS Status;
SELECT 'You can now use the enhanced Event table structure.' AS Message;
