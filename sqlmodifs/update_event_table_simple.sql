-- Update Event Table for Wizard Compatibility
USE cricket_academy;

-- Modify existing columns
ALTER TABLE Event MODIFY COLUMN StartDate DATETIME NOT NULL;
ALTER TABLE Event MODIFY COLUMN EndDate DATETIME NOT NULL;

ALTER TABLE Event MODIFY COLUMN Type ENUM('Training Camp','Workshop','Seminar','Competition','Tournament','Match','Trial','Meeting','Other');

ALTER TABLE Event MODIFY COLUMN Status ENUM('upcoming','registration_open','registration_closed','ongoing','completed','cancelled') DEFAULT 'upcoming';

-- Add new columns
ALTER TABLE Event ADD COLUMN Category ENUM('junior','senior','youth','professional','recreational','academy') AFTER Type;

ALTER TABLE Event ADD COLUMN RegistrationFee DECIMAL(10,2) DEFAULT 0.00 AFTER MaxParticipants;

ALTER TABLE Event ADD COLUMN RegistrationStart DATETIME NULL AFTER Status;

ALTER TABLE Event ADD COLUMN RegistrationEnd DATETIME NULL AFTER RegistrationStart;

ALTER TABLE Event ADD COLUMN PrimaryContact VARCHAR(255) NULL AFTER RegistrationEnd;

ALTER TABLE Event ADD COLUMN ContactEmail VARCHAR(255) NULL AFTER PrimaryContact;

ALTER TABLE Event ADD COLUMN ContactPhone VARCHAR(20) NULL AFTER ContactEmail;

ALTER TABLE Event ADD COLUMN SecondaryContact VARCHAR(255) NULL AFTER ContactPhone;

ALTER TABLE Event ADD COLUMN SecondaryEmail VARCHAR(255) NULL AFTER SecondaryContact;

ALTER TABLE Event ADD COLUMN SecondaryPhone VARCHAR(20) NULL AFTER SecondaryEmail;

ALTER TABLE Event ADD COLUMN EventCoordinator INT NULL AFTER SecondaryPhone;

ALTER TABLE Event ADD COLUMN SpecialRequirements TEXT NULL AFTER EventCoordinator;

-- Add foreign key
ALTER TABLE Event ADD CONSTRAINT fk_event_coordinator FOREIGN KEY (EventCoordinator) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add indexes
ALTER TABLE Event ADD INDEX idx_category (Category);
ALTER TABLE Event ADD INDEX idx_registration_start (RegistrationStart);
ALTER TABLE Event ADD INDEX idx_registration_end (RegistrationEnd);
ALTER TABLE Event ADD INDEX idx_contact_email (ContactEmail);
ALTER TABLE Event ADD INDEX idx_event_coordinator (EventCoordinator);

-- Verify
SELECT '✅ Event table updated successfully!' as Status;
DESCRIBE Event;
