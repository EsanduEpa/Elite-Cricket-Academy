-- ============================================================================
-- UPDATE EVENT TABLE TO MATCH ADD EVENT WIZARD
-- ============================================================================
-- Database: cricket_academy
-- Purpose: Expand Event table to store all data collected by the wizard
-- Date: October 19, 2025
-- ============================================================================

USE cricket_academy;

-- ============================================================================
-- STEP 1: MODIFY EXISTING COLUMNS
-- ============================================================================

-- Change StartDate from DATE to DATETIME (to store both date and time)
ALTER TABLE Event
MODIFY COLUMN StartDate DATETIME NOT NULL
COMMENT 'Event start date and time';

-- Change EndDate from DATE to DATETIME (to store both date and time)
ALTER TABLE Event
MODIFY COLUMN EndDate DATETIME NOT NULL
COMMENT 'Event end date and time';

-- Update Type ENUM to match wizard options
ALTER TABLE Event
MODIFY COLUMN Type ENUM(
    'Training Camp',
    'Workshop', 
    'Seminar', 
    'Competition',
    'Tournament',
    'Match',
    'Trial',
    'Meeting',
    'Other'
) COMMENT 'Type of event';

-- Update Status ENUM to include registration states
ALTER TABLE Event
MODIFY COLUMN Status ENUM(
    'upcoming',
    'registration_open',
    'registration_closed',
    'ongoing',
    'completed',
    'cancelled'
) DEFAULT 'upcoming'
COMMENT 'Current status of the event';

-- ============================================================================
-- STEP 2: ADD NEW COLUMNS
-- ============================================================================

-- Add Category field (from wizard)
ALTER TABLE Event
ADD COLUMN Category ENUM(
    'junior',
    'senior',
    'youth',
    'professional',
    'recreational',
    'academy'
) AFTER Type;

-- Add Registration Fee (from wizard)
ALTER TABLE Event
ADD COLUMN RegistrationFee DECIMAL(10,2) DEFAULT 0.00 AFTER MaxParticipants
COMMENT 'Registration fee in LKR (0.00 for free events)';

-- Add Registration Start DateTime (from wizard)
ALTER TABLE Event
ADD COLUMN RegistrationStart DATETIME NULL AFTER Status
COMMENT 'When registration opens for the event';

-- Add Registration End DateTime (from wizard)
ALTER TABLE Event
ADD COLUMN RegistrationEnd DATETIME NULL AFTER RegistrationStart
COMMENT 'When registration closes for the event';

-- Add Primary Contact Person (from wizard)
ALTER TABLE Event
ADD COLUMN PrimaryContact VARCHAR(255) NOT NULL AFTER RegistrationEnd
COMMENT 'Name of primary contact person/organizer';

-- Add Contact Email (from wizard)
ALTER TABLE Event
ADD COLUMN ContactEmail VARCHAR(255) NOT NULL AFTER PrimaryContact
COMMENT 'Primary contact email address';

-- Add Contact Phone (from wizard)
ALTER TABLE Event
ADD COLUMN ContactPhone VARCHAR(20) NOT NULL AFTER ContactEmail
COMMENT 'Primary contact phone number';

-- Add Secondary Contact Person (from wizard)
ALTER TABLE Event
ADD COLUMN SecondaryContact VARCHAR(255) NULL AFTER ContactPhone
COMMENT 'Name of secondary/backup contact person';

-- Add Secondary Email (from wizard)
ALTER TABLE Event
ADD COLUMN SecondaryEmail VARCHAR(255) NULL AFTER SecondaryContact
COMMENT 'Secondary contact email address';

-- Add Secondary Phone (from wizard)
ALTER TABLE Event
ADD COLUMN SecondaryPhone VARCHAR(20) NULL AFTER SecondaryEmail
COMMENT 'Secondary contact phone number';

-- Add Event Coordinator (from wizard)
ALTER TABLE Event
ADD COLUMN EventCoordinator INT NULL AFTER SecondaryPhone
COMMENT 'User ID of assigned event coordinator';

-- Add Special Requirements (from wizard)
ALTER TABLE Event
ADD COLUMN SpecialRequirements TEXT NULL AFTER EventCoordinator
COMMENT 'Special arrangements, notes, or requirements';

-- ============================================================================
-- STEP 3: ADD FOREIGN KEY CONSTRAINTS
-- ============================================================================

-- Add foreign key for EventCoordinator
ALTER TABLE Event
ADD CONSTRAINT fk_event_coordinator
FOREIGN KEY (EventCoordinator) REFERENCES User(UserID)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- ============================================================================
-- STEP 4: ADD INDEXES FOR PERFORMANCE
-- ============================================================================

-- Add index on Category for filtering
ALTER TABLE Event
ADD INDEX idx_category (Category);

-- Add index on RegistrationStart for queries
ALTER TABLE Event
ADD INDEX idx_registration_start (RegistrationStart);

-- Add index on RegistrationEnd for queries
ALTER TABLE Event
ADD INDEX idx_registration_end (RegistrationEnd);

-- Add index on ContactEmail for lookups
ALTER TABLE Event
ADD INDEX idx_contact_email (ContactEmail);

-- Add index on EventCoordinator for joins
ALTER TABLE Event
ADD INDEX idx_event_coordinator (EventCoordinator);

-- ============================================================================
-- STEP 5: VERIFY CHANGES
-- ============================================================================

-- Display updated table structure
DESCRIBE Event;

-- Display indexes
SHOW INDEXES FROM Event;

-- Count existing events
SELECT COUNT(*) as TotalEvents FROM Event;

-- ============================================================================
-- UPDATED EVENT TABLE STRUCTURE
-- ============================================================================
/*
Event (
    EventID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Type ENUM('Training Camp', 'Workshop', 'Seminar', 'Competition', 
              'Tournament', 'Match', 'Trial', 'Meeting', 'Other'),
    Category ENUM('junior', 'senior', 'youth', 'professional', 
                  'recreational', 'academy'),
    Description TEXT,
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    Location VARCHAR(255),
    OrganizedBy INT NOT NULL,
    Status ENUM('upcoming', 'registration_open', 'registration_closed',
                'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    MaxParticipants INT,
    RegistrationFee DECIMAL(10,2) DEFAULT 0.00,
    RegistrationStart DATETIME NULL,
    RegistrationEnd DATETIME NULL,
    PrimaryContact VARCHAR(255) NOT NULL,
    ContactEmail VARCHAR(255) NOT NULL,
    ContactPhone VARCHAR(20) NOT NULL,
    SecondaryContact VARCHAR(255) NULL,
    SecondaryEmail VARCHAR(255) NULL,
    SecondaryPhone VARCHAR(20) NULL,
    EventCoordinator INT NULL,
    SpecialRequirements TEXT NULL,
    
    FOREIGN KEY (OrganizedBy) REFERENCES User(UserID) ON DELETE CASCADE,
    FOREIGN KEY (EventCoordinator) REFERENCES User(UserID) ON DELETE SET NULL,
    
    INDEX idx_start_date (StartDate),
    INDEX idx_status (Status),
    INDEX idx_organizer (OrganizedBy),
    INDEX idx_category (Category),
    INDEX idx_registration_start (RegistrationStart),
    INDEX idx_registration_end (RegistrationEnd),
    INDEX idx_contact_email (ContactEmail),
    INDEX idx_event_coordinator (EventCoordinator)
);
*/

-- ============================================================================
-- FIELD MAPPING: WIZARD FORM → DATABASE
-- ============================================================================
/*
Wizard Field Name      → Database Column      → Type/Notes
─────────────────────────────────────────────────────────────────────
event_name             → Name                 → VARCHAR(255) NOT NULL
event_type             → Type                 → ENUM (updated values)
event_category         → Category             → ENUM (NEW)
event_description      → Description          → TEXT
event_venue            → Location             → VARCHAR(255)
max_participants       → MaxParticipants      → INT
registration_fee       → RegistrationFee      → DECIMAL(10,2) (NEW)
start_date + start_time→ StartDate            → DATETIME (merge both)
end_date + end_time    → EndDate              → DATETIME (merge both)
registration_start     → RegistrationStart    → DATETIME (NEW)
registration_end       → RegistrationEnd      → DATETIME (NEW)
event_status           → Status               → ENUM (updated values)
primary_contact        → PrimaryContact       → VARCHAR(255) (NEW)
contact_email          → ContactEmail         → VARCHAR(255) (NEW)
contact_phone          → ContactPhone         → VARCHAR(20) (NEW)
secondary_contact      → SecondaryContact     → VARCHAR(255) (NEW)
secondary_email        → SecondaryEmail       → VARCHAR(255) (NEW)
secondary_phone        → SecondaryPhone       → VARCHAR(20) (NEW)
event_coordinator      → EventCoordinator     → INT FK to User (NEW)
special_requirements   → SpecialRequirements  → TEXT (NEW)
[SESSION user_id]      → OrganizedBy          → INT FK to User (auto)
*/

-- ============================================================================
-- SAMPLE INSERT QUERY (After Changes)
-- ============================================================================
/*
INSERT INTO Event (
    Name,
    Type,
    Category,
    Description,
    StartDate,
    EndDate,
    Location,
    OrganizedBy,
    Status,
    MaxParticipants,
    RegistrationFee,
    RegistrationStart,
    RegistrationEnd,
    PrimaryContact,
    ContactEmail,
    ContactPhone,
    SecondaryContact,
    SecondaryEmail,
    SecondaryPhone,
    EventCoordinator,
    SpecialRequirements
) VALUES (
    'Annual Cricket Championship',                    -- Name
    'Tournament',                                     -- Type
    'senior',                                         -- Category
    'Annual championship for senior players...',      -- Description
    '2025-11-15 09:00:00',                           -- StartDate (date + time)
    '2025-11-15 18:00:00',                           -- EndDate (date + time)
    'Main Cricket Ground, Elite Academy',             -- Location
    1,                                                -- OrganizedBy (admin user)
    'registration_open',                              -- Status
    100,                                              -- MaxParticipants
    500.00,                                           -- RegistrationFee
    '2025-10-20 00:00:00',                           -- RegistrationStart
    '2025-11-10 23:59:59',                           -- RegistrationEnd
    'John Doe',                                       -- PrimaryContact
    'john.doe@elitecricket.com',                     -- ContactEmail
    '+94771234567',                                   -- ContactPhone
    'Jane Smith',                                     -- SecondaryContact
    'jane.smith@elitecricket.com',                   -- SecondaryEmail
    '+94772345678',                                   -- SecondaryPhone
    2,                                                -- EventCoordinator (coach user)
    'Bring your own equipment. Lunch provided.'       -- SpecialRequirements
);
*/

-- ============================================================================
-- ROLLBACK SCRIPT (In case you need to undo changes)
-- ============================================================================
/*
-- WARNING: This will remove all new columns and data in them!

ALTER TABLE Event DROP FOREIGN KEY fk_event_coordinator;
ALTER TABLE Event DROP INDEX idx_category;
ALTER TABLE Event DROP INDEX idx_registration_start;
ALTER TABLE Event DROP INDEX idx_registration_end;
ALTER TABLE Event DROP INDEX idx_contact_email;
ALTER TABLE Event DROP INDEX idx_event_coordinator;

ALTER TABLE Event DROP COLUMN SpecialRequirements;
ALTER TABLE Event DROP COLUMN EventCoordinator;
ALTER TABLE Event DROP COLUMN SecondaryPhone;
ALTER TABLE Event DROP COLUMN SecondaryEmail;
ALTER TABLE Event DROP COLUMN SecondaryContact;
ALTER TABLE Event DROP COLUMN ContactPhone;
ALTER TABLE Event DROP COLUMN ContactEmail;
ALTER TABLE Event DROP COLUMN PrimaryContact;
ALTER TABLE Event DROP COLUMN RegistrationEnd;
ALTER TABLE Event DROP COLUMN RegistrationStart;
ALTER TABLE Event DROP COLUMN RegistrationFee;
ALTER TABLE Event DROP COLUMN Category;

ALTER TABLE Event MODIFY COLUMN StartDate DATE NOT NULL;
ALTER TABLE Event MODIFY COLUMN EndDate DATE NOT NULL;
ALTER TABLE Event MODIFY COLUMN Type ENUM('Training Camp', 'Workshop', 'Seminar', 'Competition', 'Other');
ALTER TABLE Event MODIFY COLUMN Status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming';
*/

-- ============================================================================
-- COMPLETION MESSAGE
-- ============================================================================

SELECT 
    '✅ Event table successfully updated!' as Message,
    'All wizard fields now have corresponding database columns' as Status,
    'You can now save all 22 fields from the wizard' as Note;

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
