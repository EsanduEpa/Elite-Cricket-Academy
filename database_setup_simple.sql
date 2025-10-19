-- Simple User table creation script for testing
-- This matches the User table structure with School field

DROP TABLE IF EXISTS User;

CREATE TABLE User (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    DateOfBirth DATE NOT NULL,
    PhoneNumber VARCHAR(20),
    Email VARCHAR(255) UNIQUE NOT NULL,
    Address TEXT,
    School VARCHAR(255) COMMENT 'School or educational institution',
    Role ENUM('Admin', 'ShopEmployee', 'Coach', 'Trainer', 'Player') NOT NULL,
    Username VARCHAR(100) UNIQUE NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    DateJoined DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive') DEFAULT 'active',
    
    -- Enhanced fields for security and user management
    RequiresPasswordChange BOOLEAN DEFAULT FALSE COMMENT 'Force password change on first login',
    PasswordChangeDeadline DATETIME NULL COMMENT 'Deadline for mandatory password change',
    LastLoginAt DATETIME NULL COMMENT 'Track last successful login',
    LoginAttempts INT DEFAULT 0 COMMENT 'Failed login attempts counter',
    AccountLockedUntil DATETIME NULL COMMENT 'Account lock expiry time',
    CreatedBy INT NULL COMMENT 'Admin who created this user',
    Notes TEXT COMMENT 'Admin notes about the user',
    
    FOREIGN KEY (CreatedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_email (Email),
    INDEX idx_username (Username),
    INDEX idx_role (Role),
    INDEX idx_status (Status),
    INDEX idx_password_deadline (PasswordChangeDeadline),
    INDEX idx_last_login (LastLoginAt),
    INDEX idx_created_by (CreatedBy)
) ENGINE=InnoDB COMMENT='Core user table with enhanced security and role-based access control';

-- Insert a test admin user
INSERT INTO User (Name, DateOfBirth, PhoneNumber, Email, Address, School, Role, Username, PasswordHash, DateJoined, Status) 
VALUES (
    'Admin User', 
    '1990-01-01', 
    '+1234567890', 
    'admin@cricketacademy.com', 
    'Academy Address', 
    'Elite Cricket Academy', 
    'Admin', 
    'admin', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password is 'password'
    NOW(), 
    'active'
);

SELECT 'User table created successfully with School field!' as message;