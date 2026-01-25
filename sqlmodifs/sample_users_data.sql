-- ============================================================================
-- SAMPLE USER DATA FOR AUTHENTICATION TESTING
-- ============================================================================
-- Database: cricket_academy
-- Purpose: Insert sample users for testing authentication system
-- Includes: 1 Admin, 1 Trainer, 1 Coach, 1 ShopEmployee, 1 Player
-- Password for all users: "password123"
-- ============================================================================

USE cricket_academy;

-- Clear existing test data (optional - comment out if you want to keep existing data)
-- DELETE FROM PlayerProfile WHERE PlayerID IN (SELECT UserID FROM User WHERE Email LIKE '%@test.com');
-- DELETE FROM CoachProfile WHERE CoachID IN (SELECT UserID FROM User WHERE Email LIKE '%@test.com');
-- DELETE FROM TrainerProfile WHERE TrainerID IN (SELECT UserID FROM User WHERE Email LIKE '%@test.com');
-- DELETE FROM ShopEmployeeProfile WHERE ShopEmployeeID IN (SELECT UserID FROM User WHERE Email LIKE '%@test.com');
-- DELETE FROM User WHERE Email LIKE '%@test.com';

-- ============================================================================
-- INSERT SAMPLE USERS
-- Password: "password123" (hashed using bcrypt)
-- Hash generated with: password_hash('password123', PASSWORD_DEFAULT)
-- ============================================================================

-- 1. ADMIN USER
INSERT INTO User (
    Name, 
    DateOfBirth, 
    PhoneNumber, 
    Email, 
    Address, 
    School, 
    Role, 
    Username, 
    PasswordHash, 
    DateJoined, 
    Status
) VALUES (
    'Admin User',
    '1985-01-15',
    '+94771234567',
    'admin@test.com',
    '123 Admin Street, Colombo 03',
    NULL,
    'Admin',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    NOW(),
    'active'
);

-- 2. TRAINER USER
INSERT INTO User (
    Name, 
    DateOfBirth, 
    PhoneNumber, 
    Email, 
    Address, 
    School, 
    Role, 
    Username, 
    PasswordHash, 
    DateJoined, 
    Status
) VALUES (
    'Mike Trainer',
    '1988-03-20',
    '+94772345678',
    'trainer@test.com',
    '456 Fitness Lane, Kandy',
    NULL,
    'Trainer',
    'trainer',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    NOW(),
    'active'
);

-- Get the TrainerID for profile insertion
SET @trainer_id = LAST_INSERT_ID();

-- Insert Trainer Profile
INSERT INTO TrainerProfile (
    TrainerID,
    Experience,
    Certifications
) VALUES (
    @trainer_id,
    8,
    'Certified Sports Trainer, Level 3 Fitness Coach, First Aid Certified'
);

-- 3. COACH USER
INSERT INTO User (
    Name, 
    DateOfBirth, 
    PhoneNumber, 
    Email, 
    Address, 
    School, 
    Role, 
    Username, 
    PasswordHash, 
    DateJoined, 
    Status
) VALUES (
    'Sarah Coach',
    '1982-07-10',
    '+94773456789',
    'coach@test.com',
    '789 Cricket Avenue, Galle',
    NULL,
    'Coach',
    'coach',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    NOW(),
    'active'
);

-- Get the CoachID for profile insertion
SET @coach_id = LAST_INSERT_ID();

-- Insert Coach Profile
INSERT INTO CoachProfile (
    CoachID,
    Specialization,
    Experience,
    Certifications,
    IsHeadCoach
) VALUES (
    @coach_id,
    'All-rounder',
    12,
    'Level 3 ECB Coach, ICC Certified Coach, Advanced Coaching Diploma',
    FALSE
);

-- 4. SHOP EMPLOYEE USER
INSERT INTO User (
    Name, 
    DateOfBirth, 
    PhoneNumber, 
    Email, 
    Address, 
    School, 
    Role, 
    Username, 
    PasswordHash, 
    DateJoined, 
    Status
) VALUES (
    'John Shopkeeper',
    '1990-11-25',
    '+94774567890',
    'shop@test.com',
    '321 Commerce Road, Negombo',
    NULL,
    'ShopEmployee',
    'shopkeeper',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    NOW(),
    'active'
);

-- Get the ShopEmployeeID for profile insertion
SET @shop_id = LAST_INSERT_ID();

-- Insert Shop Employee Profile
INSERT INTO ShopEmployeeProfile (
    ShopEmployeeID,
    Department,
    HireDate
) VALUES (
    @shop_id,
    'Equipment',
    '2023-01-15'
);

-- 5. PLAYER USER
INSERT INTO User (
    Name, 
    DateOfBirth, 
    PhoneNumber, 
    Email, 
    Address, 
    School, 
    Role, 
    Username, 
    PasswordHash, 
    DateJoined, 
    Status
) VALUES (
    'Tom Player',
    '2005-05-18',
    '+94775678901',
    'player@test.com',
    '555 Player Street, Matara',
    'Royal College Colombo',
    'Player',
    'player',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    NOW(),
    'active'
);

-- Get the PlayerID for profile insertion
SET @player_id = LAST_INSERT_ID();

-- Insert Player Profile
INSERT INTO PlayerProfile (
    PlayerID,
    BattingStyle,
    BowlingStyle,
    JerseyNumber,
    SubscriptionType,
    EmergencyContactName,
    EmergencyContactPhone,
    ParentGuardianName,
    ParentGuardianPhone,
    SchoolInstitution,
    PreviousExperience,
    MedicalConditions
) VALUES (
    @player_id,
    'Right-handed',
    'Medium',
    15,
    'premium',
    'Mary Player',
    '+94776789012',
    'Robert Player',
    '+94776789012',
    'Royal College Colombo',
    'School cricket team captain, District U19 player',
    'None'
);

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Display all test users
SELECT 
    UserID,
    Name,
    Email,
    Username,
    Role,
    Status,
    DateJoined
FROM User 
WHERE Email LIKE '%@test.com'
ORDER BY Role, Name;

-- ============================================================================
-- LOGIN CREDENTIALS SUMMARY
-- ============================================================================
-- 
-- Admin Login:
--   Email/Username: admin@test.com OR admin
--   Password: password123
--   Dashboard: /admin/dashboard
--
-- Trainer Login:
--   Email/Username: trainer@test.com OR trainer
--   Password: password123
--   Dashboard: /trainer/dashboard
--
-- Coach Login:
--   Email/Username: coach@test.com OR coach
--   Password: password123
--   Dashboard: /coach/dashboard
--
-- Shop Employee Login:
--   Email/Username: shop@test.com OR shopkeeper
--   Password: password123
--   Dashboard: /shop/dashboard
--
-- Player Login:
--   Email/Username: player@test.com OR player
--   Password: password123
--   Dashboard: /player/dashboard
--
-- ============================================================================
