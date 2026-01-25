-- =====================================================
-- Create Event Table - Elite Cricket Academy
-- =====================================================
-- This is the complete Event table structure currently in use
-- Based on working CRUD implementation
-- Date: 2025-10-20
-- =====================================================

DROP TABLE IF EXISTS Event;

CREATE TABLE Event (
    -- Primary Key
    EventID INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Basic Event Information
    Name VARCHAR(255) NOT NULL,
    Type ENUM('Training Camp', 'Workshop', 'Seminar', 'Competition', 'Tournament', 'Match', 'Other') DEFAULT 'Other',
    Category VARCHAR(100) NULL COMMENT 'Event category (e.g., U15, U19, Senior)',
    Description TEXT NULL,
    
    -- Date & Time Information
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    
    -- Location
    Location VARCHAR(255) NULL,
    
    -- Status
    Status ENUM('upcoming', 'registration_open', 'registration_closed', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    
    -- Registration Information
    RegistrationStart DATETIME NULL,
    RegistrationEnd DATETIME NULL,
    MaxParticipants INT NULL,
    RegistrationFee DECIMAL(10,2) NULL DEFAULT 0.00,
    
    -- Contact Information
    PrimaryContact VARCHAR(255) NULL,
    ContactEmail VARCHAR(255) NULL,
    ContactPhone VARCHAR(20) NULL,
    
    -- Timestamps
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_start_date (StartDate),
    INDEX idx_end_date (EndDate),
    INDEX idx_status (Status),
    INDEX idx_type (Type),
    INDEX idx_category (Category)
    
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Events, tournaments, training camps, and competitions';

-- =====================================================
-- Sample Data (Optional - Remove if not needed)
-- =====================================================

INSERT INTO Event (
    Name,
    Type,
    Category,
    Description,
    StartDate,
    EndDate,
    Location,
    Status,
    RegistrationStart,
    RegistrationEnd,
    MaxParticipants,
    RegistrationFee,
    PrimaryContact,
    ContactEmail,
    ContactPhone
) VALUES 
(
    'U19 Cricket Championship 2025',
    'Tournament',
    'U19',
    'Annual Under-19 Cricket Championship featuring top academies from across the region.',
    '2025-11-15 09:00:00',
    '2025-11-17 18:00:00',
    'Elite Cricket Academy Main Ground',
    'registration_open',
    '2025-10-20 00:00:00',
    '2025-11-10 23:59:59',
    16,
    2500.00,
    'John Smith',
    'tournaments@elitecricket.com',
    '+94771234567'
),
(
    'Advanced Bowling Workshop',
    'Workshop',
    'Senior',
    'Intensive 2-day workshop focused on advanced bowling techniques with international coaches.',
    '2025-10-28 08:30:00',
    '2025-10-29 17:00:00',
    'Indoor Training Center',
    'registration_open',
    '2025-10-20 00:00:00',
    '2025-10-26 23:59:59',
    25,
    1500.00,
    'Sarah Johnson',
    'workshops@elitecricket.com',
    '+94777654321'
),
(
    'Junior Cricket Training Camp',
    'Training Camp',
    'U15',
    'Week-long training camp for junior players focusing on fundamental skills.',
    '2025-12-01 08:00:00',
    '2025-12-05 16:00:00',
    'Elite Cricket Academy',
    'upcoming',
    '2025-11-01 00:00:00',
    '2025-11-25 23:59:59',
    30,
    3500.00,
    'Mike Anderson',
    'training@elitecricket.com',
    '+94771122334'
);

-- =====================================================
-- Verify Table Creation
-- =====================================================

-- Check table structure
DESCRIBE Event;

-- Check sample data (if inserted)
SELECT 
    EventID,
    Name,
    Type,
    Category,
    StartDate,
    Status
FROM Event
ORDER BY StartDate;
