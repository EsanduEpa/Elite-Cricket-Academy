-- ============================================================================
-- UPDATE SAMPLE USER PASSWORDS FOR AUTHENTICATION TESTING
-- ============================================================================
-- Database: cricket_academy
-- Purpose: Update existing user passwords to "password123" for testing
-- ============================================================================

USE cricket_academy;

-- Update password for all test users
-- Password: "password123" (hashed using bcrypt)
-- Hash generated with: password_hash('password123', PASSWORD_DEFAULT)
-- VERIFIED WORKING HASH

UPDATE User 
SET PasswordHash = '$2y$12$3HITtikJ8yCvx2LkoG4UuOq1RlTPu7un6.TYAIn8V5WrBVJll5wFK'
WHERE Username IN ('admin', 'trainer', 'coach', 'shopkeeper', 'player');

-- Display updated users
SELECT 
    UserID,
    Name,
    Email,
    Username,
    Role,
    Status
FROM User 
WHERE Username IN ('admin', 'trainer', 'coach', 'shopkeeper', 'player')
ORDER BY Role, Name;

-- ============================================================================
-- LOGIN CREDENTIALS SUMMARY
-- ============================================================================
-- 
-- Admin Login:
--   Email/Username: admin
--   Password: password123
--
-- Trainer Login:
--   Email/Username: trainer
--   Password: password123
--
-- Coach Login:
--   Email/Username: coach
--   Password: password123
--
-- Shop Employee Login:
--   Email/Username: shopkeeper
--   Password: password123
--
-- Player Login:
--   Email/Username: player
--   Password: password123
--
-- ============================================================================
