-- =============================================================================
-- CRICKET ACADEMY MANAGEMENT SYSTEM - DATABASE SCHEMA
-- =============================================================================
-- Generated: September 6, 2025
-- Database: MySQL 8.0+
-- Description: Complete database schema for Cricket Academy Management System
-- =============================================================================

DROP DATABASE IF EXISTS cricket_academy;
CREATE DATABASE cricket_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cricket_academy;

-- =============================================================================
-- SECTION 1: CORE USER MANAGEMENT
-- =============================================================================

-- Main user table with enhanced security and role-based access control
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

-- Enhanced player profile with comprehensive information for better management
CREATE TABLE PlayerProfile (
    PlayerID INT PRIMARY KEY,
    BattingStyle ENUM('Right-handed', 'Left-handed', 'Switch-hitter'),
    BowlingStyle ENUM('Fast', 'Medium', 'Spin', 'Off-spin', 'Leg-spin', 'None'),
    JerseyNumber INT UNIQUE,
    
    -- Enhanced fields for comprehensive player management
    SubscriptionType ENUM('basic', 'premium', 'private_only') DEFAULT 'basic',
    EmergencyContactName VARCHAR(255) COMMENT 'Emergency contact person',
    EmergencyContactPhone VARCHAR(20) COMMENT 'Emergency contact phone',
    ParentGuardianName VARCHAR(255) COMMENT 'Parent or guardian name',
    ParentGuardianPhone VARCHAR(20) COMMENT 'Parent or guardian phone',
    SchoolInstitution VARCHAR(255) COMMENT 'Educational institution',
    PreviousExperience TEXT COMMENT 'Previous cricket experience',
    MedicalConditions TEXT COMMENT 'Medical conditions or allergies',
    HowHeardAboutUs VARCHAR(100) COMMENT 'Marketing tracking field',
    
    FOREIGN KEY (PlayerID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_jersey (JerseyNumber)
) ENGINE=InnoDB COMMENT='Enhanced player profile with comprehensive information for better management';

-- Coach-specific profile with head coach promotion capabilities
CREATE TABLE CoachProfile (
    CoachID INT PRIMARY KEY,
    Specialization ENUM('Batting', 'Bowling', 'All-rounder', 'Wicket-keeping'),
    Experience INT COMMENT 'Years of experience',
    Certifications TEXT,
    IsHeadCoach BOOLEAN DEFAULT FALSE COMMENT 'Head coach flag - only one per academy with enhanced permissions',
    
    FOREIGN KEY (CoachID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_is_head_coach (IsHeadCoach)
) ENGINE=InnoDB COMMENT='Coach-specific profile with head coach promotion capabilities';

-- Physical trainer profile information
CREATE TABLE TrainerProfile (
    TrainerID INT PRIMARY KEY,
    Experience INT COMMENT 'Years of experience',
    Certifications TEXT,
    
    FOREIGN KEY (TrainerID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Physical trainer profile information';

-- Shop employee managing products, rentals, and facility bookings
CREATE TABLE ShopEmployeeProfile (
    ShopEmployeeID INT PRIMARY KEY,
    Department ENUM('Equipment', 'Facility', 'General') DEFAULT 'General',
    HireDate DATE NOT NULL,
    
    FOREIGN KEY (ShopEmployeeID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Shop employee managing products, rentals, and facility bookings';

-- Admin-specific profile with enhanced permissions and security tracking
CREATE TABLE AdminProfile (
    AdminID INT PRIMARY KEY,
    AdminLevel ENUM('super_admin', 'system_admin', 'content_admin', 'finance_admin') DEFAULT 'system_admin' COMMENT 'Admin access level hierarchy',
    Department ENUM('Management', 'Operations', 'Finance', 'Technical', 'General') DEFAULT 'General' COMMENT 'Administrative department',
    AccessPermissions JSON COMMENT 'JSON object storing specific permission flags',
    LastLoginIP VARCHAR(45) COMMENT 'Last login IP address for security tracking',
    LoginAttempts INT DEFAULT 0 COMMENT 'Failed login attempt counter',
    AccountLocked BOOLEAN DEFAULT FALSE COMMENT 'Account lock status for security',
    LockoutExpiry DATETIME NULL COMMENT 'When account lockout expires',
    TwoFactorEnabled BOOLEAN DEFAULT FALSE COMMENT 'Two-factor authentication status',
    SecurityClearance ENUM('Level1', 'Level2', 'Level3', 'Level4') DEFAULT 'Level1' COMMENT 'Security clearance level',
    HireDate DATE NOT NULL COMMENT 'Admin hire/appointment date',
    CreatedBy INT NULL COMMENT 'Admin who created this account',
    LastPasswordChange DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Last password change timestamp',
    SessionTimeout INT DEFAULT 30 COMMENT 'Session timeout in minutes',
    
    FOREIGN KEY (AdminID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (CreatedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_admin_level (AdminLevel),
    INDEX idx_department (Department),
    INDEX idx_security_clearance (SecurityClearance),
    INDEX idx_account_status (AccountLocked, LockoutExpiry)
) ENGINE=InnoDB COMMENT='Admin-specific profile with enhanced permissions and security tracking';
-- =============================================================================
-- SECTION 2: ENHANCED USER MANAGEMENT AND ASSIGNMENT SYSTEM  
-- =============================================================================

-- Player-Coach assignment tracking with automatic age-based suggestions
CREATE TABLE PlayerCoachAssignment (
    AssignmentID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL COMMENT 'References User table for players',
    CoachID INT NOT NULL COMMENT 'References User table for coaches',
    AssignmentType ENUM('regular', 'private', 'both') DEFAULT 'regular',
    AssignedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive', 'completed') DEFAULT 'active',
    Notes TEXT COMMENT 'Admin notes about the assignment',
    
    FOREIGN KEY (PlayerID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (CoachID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_player_coach (PlayerID, CoachID),
    INDEX idx_assignment_status (Status),
    UNIQUE KEY unique_active_assignment (PlayerID, CoachID, Status)
) ENGINE=InnoDB COMMENT='Player-Coach assignment tracking with automatic age-based suggestions';

-- Player-Trainer assignment tracking for physical training
CREATE TABLE PlayerTrainerAssignment (
    AssignmentID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL COMMENT 'References User table for players',
    TrainerID INT NOT NULL COMMENT 'References User table for trainers',
    AssignedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive', 'completed') DEFAULT 'active',
    
    FOREIGN KEY (PlayerID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (TrainerID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_player_trainer (PlayerID, TrainerID)
) ENGINE=InnoDB COMMENT='Player-Trainer assignment tracking for physical training';

-- Dynamic role-based permissions storage for enhanced access control
CREATE TABLE UserPermissions (
    UserID INT PRIMARY KEY,
    Permissions JSON COMMENT 'Role-based permissions stored as JSON array',
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Auto-updated on permission changes',
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Dynamic role-based permissions storage for enhanced access control';

-- Comprehensive notification system for all user communications
CREATE TABLE Notification (
    NotificationID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Type VARCHAR(50) NOT NULL COMMENT 'notification type: new_player_registration, coach_assignment, etc.',
    Title VARCHAR(255) NOT NULL,
    Message TEXT NOT NULL,
    Data JSON NULL COMMENT 'Additional notification data as JSON',
    ActionUrl VARCHAR(500) NULL COMMENT 'URL for notification action button',
    IsRead BOOLEAN DEFAULT FALSE,
    ReadAt DATETIME NULL,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_notifications (UserID),
    INDEX idx_notification_type (Type),
    INDEX idx_notification_read (IsRead),
    INDEX idx_notification_created (CreatedAt)
) ENGINE=InnoDB COMMENT='Comprehensive notification system for all user communications';

-- Real-time notifications for immediate user updates and alerts
CREATE TABLE LiveNotification (
    LiveNotificationID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Type VARCHAR(50) NOT NULL,
    Title VARCHAR(255) NOT NULL,
    Message TEXT NOT NULL,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    ExpiresAt DATETIME NOT NULL COMMENT 'Auto-cleanup expired notifications',
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_live_user (UserID),
    INDEX idx_live_expires (ExpiresAt)
) ENGINE=InnoDB COMMENT='Real-time notifications for immediate user updates and alerts';

-- Comprehensive activity logging and audit trail system
CREATE TABLE ActivityLog (
    ActivityID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Action VARCHAR(100) NOT NULL COMMENT 'Action type: account_created, login, coach_assignment, etc.',
    Description TEXT COMMENT 'Detailed description of the activity',
    IPAddress VARCHAR(45) COMMENT 'User IP address for security tracking',
    UserAgent TEXT COMMENT 'Browser/device information',
    Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_activity (UserID),
    INDEX idx_action (Action),
    INDEX idx_timestamp (Timestamp)
) ENGINE=InnoDB COMMENT='Comprehensive activity logging and audit trail system';

-- Email tracking and delivery status monitoring
CREATE TABLE EmailLog (
    EmailID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NULL COMMENT 'Can be null for system emails',
    RecipientEmail VARCHAR(255) NOT NULL,
    Subject VARCHAR(255) NOT NULL,
    EmailType ENUM('welcome', 'password_reset', 'notification', 'suspension', 'promotion') NOT NULL,
    Status ENUM('queued', 'sent', 'failed', 'bounced') DEFAULT 'queued',
    SentAt DATETIME NULL,
    ErrorMessage TEXT NULL COMMENT 'Error details for failed emails',
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_user_email (UserID),
    INDEX idx_email_type (EmailType),
    INDEX idx_status (Status),
    INDEX idx_sent_at (SentAt)
) ENGINE=InnoDB COMMENT='Email tracking and delivery status monitoring';

-- =============================================================================
-- SECTION 3: MEMBERSHIP AND SUBSCRIPTION MANAGEMENT
-- =============================================================================

-- Different membership plans with varying benefits
CREATE TABLE MembershipPlan (
    PlanID INT AUTO_INCREMENT PRIMARY KEY,
    PlanName VARCHAR(255) NOT NULL,
    Description TEXT,
    MonthlyFee DECIMAL(10,2) NOT NULL,
    SessionsPerWeek INT DEFAULT 2,
    PrivateSessionsIncluded INT DEFAULT 0,
    FacilityAccessIncluded BOOLEAN DEFAULT FALSE,
    Status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB COMMENT='Different membership plans with varying benefits';

-- Player membership subscriptions for regular group sessions
CREATE TABLE PlayerSubscription (
    SubscriptionID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    PlanID INT NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE,
    Status ENUM('active', 'suspended', 'cancelled', 'expired') DEFAULT 'active',
    MonthlyFee DECIMAL(10,2) NOT NULL,
    PaymentDay INT DEFAULT 1 COMMENT 'Day of month when payment is due',
    AutoRenewal BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlanID) REFERENCES MembershipPlan(PlanID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Player membership subscriptions for regular group sessions';

-- Monthly subscription payments
CREATE TABLE SubscriptionPayment (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    SubscriptionID INT NOT NULL,
    PaymentDate DATE NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentMethod ENUM('cash', 'card', 'bank_transfer', 'online') NOT NULL,
    Status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    DueDate DATE NOT NULL,
    LateFee DECIMAL(10,2) DEFAULT 0.00,
    ProcessedBy INT,
    
    FOREIGN KEY (SubscriptionID) REFERENCES PlayerSubscription(SubscriptionID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ProcessedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Monthly subscription payments';

-- =============================================================================
-- SECTION 4: PLAYER-COACH/TRAINER RELATIONSHIPS
-- =============================================================================

-- Many-to-many relationship between players and coaches
CREATE TABLE PlayerCoach (
    PlayerCoachID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    CoachID INT NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE,
    Status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (CoachID) REFERENCES CoachProfile(CoachID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_player_coach (PlayerID, CoachID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Many-to-many relationship between players and coaches';

-- Many-to-many relationship between players and trainers
CREATE TABLE PlayerTrainer (
    PlayerTrainerID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    TrainerID INT NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE,
    Status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (TrainerID) REFERENCES TrainerProfile(TrainerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_player_trainer (PlayerID, TrainerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Many-to-many relationship between players and trainers';

-- =============================================================================
-- SECTION 5: SESSION MANAGEMENT
-- =============================================================================

-- Training sessions conducted by coaches or trainers with enhanced features
CREATE TABLE Session (
    SessionID INT AUTO_INCREMENT PRIMARY KEY,
    SessionType ENUM('Coaching', 'Physical Training') NOT NULL,
    SessionMode ENUM('Group', 'Private') DEFAULT 'Group',
    CoachOrTrainerID INT NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Date DATE NOT NULL,
    StartTime TIME NOT NULL,
    EndTime TIME NOT NULL,
    Location VARCHAR(255),
    Status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    MaxParticipants INT DEFAULT 10,
    PricePerSession DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cost for private sessions, 0 for monthly subscription group sessions',
    IsRecurring BOOLEAN DEFAULT TRUE COMMENT 'True for regular group sessions, false for one-time sessions',
    
    FOREIGN KEY (CoachOrTrainerID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_date (Date),
    INDEX idx_datetime_range (Date, StartTime, EndTime),
    INDEX idx_coach_trainer (CoachOrTrainerID),
    INDEX idx_status (Status),
    INDEX idx_session_mode (SessionMode)
) ENGINE=InnoDB COMMENT='Training sessions conducted by coaches or trainers with enhanced features';

-- Player enrollment in training sessions
CREATE TABLE SessionEnrollment (
    EnrollmentID INT AUTO_INCREMENT PRIMARY KEY,
    SessionID INT NOT NULL,
    PlayerID INT NOT NULL,
    EnrollmentDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('enrolled', 'attended', 'missed', 'cancelled') DEFAULT 'enrolled',
    
    FOREIGN KEY (SessionID) REFERENCES Session(SessionID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_enrollment (SessionID, PlayerID),
    INDEX idx_player_enrollment (PlayerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Player enrollment in training sessions';

-- =============================================================================
-- SECTION 4: APPOINTMENT SYSTEM
-- =============================================================================

-- One-on-one appointments between coaches and players
CREATE TABLE CoachAppointment (
    AppointmentID INT AUTO_INCREMENT PRIMARY KEY,
    CoachID INT NOT NULL,
    PlayerID INT NOT NULL,
    AppointmentDate DATE NOT NULL,
    StartTime TIME NOT NULL,
    EndTime TIME NOT NULL,
    Status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    Reason TEXT,
    
    FOREIGN KEY (CoachID) REFERENCES CoachProfile(CoachID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_coach_date (CoachID, AppointmentDate),
    INDEX idx_player_date (PlayerID, AppointmentDate),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='One-on-one appointments between coaches and players';

-- One-on-one appointments between trainers and players
CREATE TABLE TrainerAppointment (
    AppointmentID INT AUTO_INCREMENT PRIMARY KEY,
    TrainerID INT NOT NULL,
    PlayerID INT NOT NULL,
    AppointmentDate DATE NOT NULL,
    StartTime TIME NOT NULL,
    EndTime TIME NOT NULL,
    Status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    Reason TEXT,
    
    FOREIGN KEY (TrainerID) REFERENCES TrainerProfile(TrainerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_trainer_date (TrainerID, AppointmentDate),
    INDEX idx_player_date (PlayerID, AppointmentDate),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='One-on-one appointments between trainers and players';

-- =============================================================================
-- SECTION 5: FACILITY MANAGEMENT
-- =============================================================================

-- Cricket facilities like nets, bowling machines, grounds
CREATE TABLE Facility (
    FacilityID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Location VARCHAR(255),
    Capacity INT NOT NULL,
    AvailabilityStatus ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    HourlyRate DECIMAL(10,2) DEFAULT 0.00,
    
    INDEX idx_availability (AvailabilityStatus),
    INDEX idx_name (Name)
) ENGINE=InnoDB COMMENT='Cricket facilities like nets, bowling machines, grounds';

-- Facility bookings managed by shop employees
CREATE TABLE FacilityBooking (
    FacilityBookingID INT AUTO_INCREMENT PRIMARY KEY,
    FacilityID INT NOT NULL,
    PlayerID INT NOT NULL,
    BookingDate DATE NOT NULL,
    StartTime TIME NOT NULL,
    EndTime TIME NOT NULL,
    Status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    TotalCost DECIMAL(10,2),
    BookedBy INT COMMENT 'Shop employee who processed booking',
    
    FOREIGN KEY (FacilityID) REFERENCES Facility(FacilityID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (BookedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_booking_date (BookingDate),
    INDEX idx_facility_date (FacilityID, BookingDate),
    INDEX idx_player_booking (PlayerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Facility bookings managed by shop employees';

-- =============================================================================
-- SECTION 6: EQUIPMENT MANAGEMENT
-- =============================================================================

-- Cricket equipment available for rental or purchase
CREATE TABLE Equipment (
    EquipmentID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Category ENUM('Batting', 'Bowling', 'Protective', 'Training', 'Other'),
    AvailabilityStatus ENUM('available', 'rented', 'maintenance', 'damaged') DEFAULT 'available',
    RentalPrice DECIMAL(10,2) NOT NULL,
    PurchasePrice DECIMAL(10,2),
    EqCondition ENUM('new', 'good', 'fair', 'poor') DEFAULT 'good',

    INDEX idx_availability (AvailabilityStatus),
    INDEX idx_category (Category),
    INDEX idx_name (Name)
) ENGINE=InnoDB COMMENT='Cricket equipment available for rental or purchase';

-- Equipment rentals processed by shop employees
CREATE TABLE EquipmentRental (
    RentalID INT AUTO_INCREMENT PRIMARY KEY,
    EquipmentID INT NOT NULL,
    PlayerID INT NOT NULL,
    RentalDate DATE NOT NULL,
    StartTime DATETIME NOT NULL,
    EndTime DATETIME NOT NULL,
    Status ENUM('active', 'returned', 'overdue', 'cancelled') DEFAULT 'active',
    TotalCost DECIMAL(10,2),
    ProcessedBy INT NOT NULL,
    ReturnInspectedBy INT NULL COMMENT 'Employee who inspected returned equipment',
    ReturnNotes TEXT COMMENT 'Notes about equipment condition on return',
    LateFee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Late return fee if applicable',
    
    FOREIGN KEY (EquipmentID) REFERENCES Equipment(EquipmentID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ProcessedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ReturnInspectedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_rental_date (RentalDate),
    INDEX idx_equipment_rental (EquipmentID),
    INDEX idx_player_rental (PlayerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Equipment rentals processed by shop employees';

-- Shop products available for purchase (cricket gear, accessories, merchandise)
CREATE TABLE Product (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Category ENUM('Batting', 'Bowling', 'Protective', 'Training', 'Merchandise', 'Accessories', 'Other'),
    Brand VARCHAR(100),
    Price DECIMAL(10,2) NOT NULL,
    StockQuantity INT DEFAULT 0,
    Status ENUM('active', 'discontinued', 'out_of_stock') DEFAULT 'active',
    SKU VARCHAR(100) UNIQUE COMMENT 'Stock Keeping Unit',
    Weight DECIMAL(8,3) COMMENT 'Product weight in kg',
    Dimensions VARCHAR(100) COMMENT 'Length x Width x Height',
     ImageURL VARCHAR(255) COMMENT 'Path or URL of product image', 
    AddedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedBy INT NULL COMMENT 'Shop employee who last updated',
    
    FOREIGN KEY (UpdatedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_category (Category),
    INDEX idx_brand (Brand),
    INDEX idx_status (Status),
    INDEX idx_price (Price),
    INDEX idx_stock (StockQuantity)
) ENGINE=InnoDB COMMENT='Shop products available for purchase - cricket gear, accessories, merchandise';

-- Shopping cart for players to add products before purchase
CREATE TABLE ProductCart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    ProductID INT NOT NULL,
    Quantity INT NOT NULL DEFAULT 1,
    AddedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_cart_item (PlayerID, ProductID),
    INDEX idx_player_cart (PlayerID)
) ENGINE=InnoDB COMMENT='Shopping cart for players to add products before purchase';

-- Equipment reservation cart for players to reserve equipment before rental
CREATE TABLE EquipmentCart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    EquipmentID INT NOT NULL,
    RequestedStartTime DATETIME NOT NULL,
    RequestedEndTime DATETIME NOT NULL,
    Status ENUM('pending', 'confirmed', 'expired') DEFAULT 'pending',
    AddedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    ExpiresAt DATETIME DEFAULT (DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 30 MINUTE)) COMMENT 'Reservation expires after 30 minutes',
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (EquipmentID) REFERENCES Equipment(EquipmentID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_equipment_reservation (PlayerID, EquipmentID, RequestedStartTime),
    INDEX idx_player_equipment_cart (PlayerID),
    INDEX idx_equipment_availability (EquipmentID, RequestedStartTime, RequestedEndTime),
    INDEX idx_expires_at (ExpiresAt)
) ENGINE=InnoDB COMMENT='Equipment reservation cart for players to reserve equipment before rental';

-- Purchase orders for products bought from the shop
CREATE TABLE ProductOrder (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    OrderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    TotalAmount DECIMAL(10,2) NOT NULL,
    PaymentMethod ENUM('cash', 'card', 'bank_transfer', 'online') NOT NULL,
    Status ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
    ProcessedBy INT NULL COMMENT 'Shop employee who processed the order',
    ShippingAddress TEXT COMMENT 'Delivery address if different from user address',
    OrderNotes TEXT COMMENT 'Special instructions or notes',
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ProcessedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_player_order (PlayerID),
    INDEX idx_order_date (OrderDate),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Purchase orders for products bought from the shop';

-- Individual items within each product order
CREATE TABLE ProductOrderItem (
    OrderItemID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    ProductID INT NOT NULL,
    Quantity INT NOT NULL,
    UnitPrice DECIMAL(10,2) NOT NULL,
    SubTotal DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (OrderID) REFERENCES ProductOrder(OrderID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_order_items (OrderID),
    INDEX idx_product_sales (ProductID)
) ENGINE=InnoDB COMMENT='Individual items within each product order';

-- =============================================================================
-- SECTION 7: EVENT AND TOURNAMENT MANAGEMENT
-- =============================================================================

-- Academy events and training camps
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


-- Cricket tournaments
CREATE TABLE Tournament (
    TournamentID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Location VARCHAR(255),
    EventDate DATE NOT NULL,
    CreatedBy INT NOT NULL,
    Status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    PrizePool DECIMAL(12,2) DEFAULT 0.00,
    
    FOREIGN KEY (CreatedBy) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_event_date (EventDate),
    INDEX idx_status (Status),
    INDEX idx_creator (CreatedBy)
) ENGINE=InnoDB COMMENT='Cricket tournaments';

-- Individual matches within tournaments
CREATE TABLE CriMatch (
    MatchID INT AUTO_INCREMENT PRIMARY KEY,
    TournamentID INT NOT NULL,
    Date DATE NOT NULL,
    Venue VARCHAR(255),
    OpponentTeam VARCHAR(255) NOT NULL,
    Result ENUM('win', 'loss', 'draw', 'no-result', 'pending') DEFAULT 'pending',
    OurScore VARCHAR(50),
    OpponentScore VARCHAR(50),
    
    FOREIGN KEY (TournamentID) REFERENCES Tournament(TournamentID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_tournament_match (TournamentID),
    INDEX idx_date (Date),
    INDEX idx_result (Result)
) ENGINE=InnoDB COMMENT='Individual matches within tournaments';

-- Players selected for tournaments
CREATE TABLE TournamentPlayer (
    TournamentID INT NOT NULL,
    PlayerID INT NOT NULL,
    Team VARCHAR(100) DEFAULT 'Academy Team',
    RoleInTeam ENUM('Captain', 'Vice-Captain', 'Wicket-Keeper', 'Batsman', 'Bowler', 'All-Rounder'),
    SelectedBy INT COMMENT 'Head coach who selected player',
    
    PRIMARY KEY (TournamentID, PlayerID),
    FOREIGN KEY (TournamentID) REFERENCES Tournament(TournamentID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (SelectedBy) REFERENCES CoachProfile(CoachID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_player_tournament (PlayerID),
    INDEX idx_role (RoleInTeam)
) ENGINE=InnoDB COMMENT='Players selected for tournaments';

-- =============================================================================
-- SECTION 8: PERFORMANCE TRACKING
-- =============================================================================



CREATE TABLE Achievements (
    AchievementID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL COMMENT 'Foreign key to identify which player earned it',
    Date DATE NOT NULL,
    MatchName VARCHAR(255) NOT NULL,
    Tournament VARCHAR(255) NOT NULL,
    Achievement TEXT NOT NULL,
    VerifiedStatus ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',

    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID)
        ON DELETE CASCADE ON UPDATE CASCADE,

    INDEX idx_tournament (Tournament),
    INDEX idx_verified (VerifiedStatus),
    INDEX idx_date (Date)
);


-- Individual player performance in matches
CREATE TABLE PlayerMatchPerformance (
    PerformanceID INT AUTO_INCREMENT PRIMARY KEY,
    MatchID INT NOT NULL,
    PlayerID INT NOT NULL,
    RunsScored INT DEFAULT 0,
    BallsFaced INT DEFAULT 0,
    WicketsTaken INT DEFAULT 0,
    OversBowled DECIMAL(4,1) DEFAULT 0.0,
    RunsConceded INT DEFAULT 0,
    Catches INT DEFAULT 0,
    Stumpings INT DEFAULT 0,
    Rating DECIMAL(3,1) DEFAULT 0.0 COMMENT 'Performance rating out of 10',
    
    FOREIGN KEY (MatchID) REFERENCES CriMatch(MatchID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_match_performance (MatchID, PlayerID),
    INDEX idx_player_performance (PlayerID),
    INDEX idx_rating (Rating)
) ENGINE=InnoDB COMMENT='Individual player performance in matches';

-- Player statistics aggregated by tournament
CREATE TABLE PlayerTournamentStats (
    TournamentStatsID INT AUTO_INCREMENT PRIMARY KEY,
    TournamentID INT NOT NULL,
    PlayerID INT NOT NULL,
    MatchesPlayed INT DEFAULT 0,
    TotalRuns INT DEFAULT 0,
    TotalWickets INT DEFAULT 0,
    BattingAverage DECIMAL(6,2) DEFAULT 0.00,
    BowlingAverage DECIMAL(6,2) DEFAULT 0.00,
    StrikeRate DECIMAL(6,2) DEFAULT 0.00,
    EconomyRate DECIMAL(4,2) DEFAULT 0.00,
    
    FOREIGN KEY (TournamentID) REFERENCES Tournament(TournamentID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_tournament_stats (TournamentID, PlayerID),
    INDEX idx_player_tournament_stats (PlayerID)
) ENGINE=InnoDB COMMENT='Player statistics aggregated by tournament';

-- Overall career statistics for each player with coach tracking
CREATE TABLE PlayerOverallStats (
    StatsID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL UNIQUE,
    MatchesPlayed INT DEFAULT 0,
    TotalRuns INT DEFAULT 0,
    TotalWickets INT DEFAULT 0,
    HighestScore INT DEFAULT 0,
    BattingAverage DECIMAL(6,2) DEFAULT 0.00,
    BowlingAverage DECIMAL(6,2) DEFAULT 0.00,
    StrikeRate DECIMAL(6,2) DEFAULT 0.00,
    EconomyRate DECIMAL(4,2) DEFAULT 0.00,
    LastUpdated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    LastUpdatedBy INT NULL COMMENT 'Coach who last updated the stats',
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (LastUpdatedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_batting_avg (BattingAverage),
    INDEX idx_bowling_avg (BowlingAverage),
    INDEX idx_last_updated_by (LastUpdatedBy)
) ENGINE=InnoDB COMMENT='Overall career statistics for each player with coach tracking';

-- Individual performance updates and assessments by coaches with full audit trail
CREATE TABLE PerformanceUpdate (
    UpdateID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    CoachID INT NOT NULL COMMENT 'Coach making the update',
    SessionID INT NULL COMMENT 'Related training session if applicable',
    UpdateType ENUM('match_performance', 'training_assessment', 'skill_evaluation', 'fitness_test') NOT NULL,
    
    -- Performance data fields
    RunsScored INT NULL,
    BallsFaced INT NULL,
    Fours INT NULL,
    Sixes INT NULL,
    WicketsTaken INT NULL,
    OversBowled DECIMAL(4,1) NULL,
    RunsConceded INT NULL,
    CatchesTaken INT NULL,
    
    -- Assessment fields
    TechnicalRating INT NULL COMMENT 'Rating 1-10',
    FitnessRating INT NULL COMMENT 'Rating 1-10',
    AttitudeRating INT NULL COMMENT 'Rating 1-10',
    
    Comments TEXT COMMENT 'Coach comments and observations',
    UpdateDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending_approval', 'approved', 'rejected') DEFAULT 'approved',
    ApprovedBy INT NULL COMMENT 'Head coach or admin who approved',
    ApprovedAt DATETIME NULL,
    
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (CoachID) REFERENCES User(UserID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (SessionID) REFERENCES Session(SessionID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (ApprovedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_perf_player (PlayerID),
    INDEX idx_perf_coach (CoachID),
    INDEX idx_perf_date (UpdateDate),
    INDEX idx_perf_status (Status),
    INDEX idx_player_coach_perf (PlayerID, CoachID)
) ENGINE=InnoDB COMMENT='Individual performance updates and assessments by coaches with full audit trail';

-- Detailed coaching session logs and player performance tracking per session
CREATE TABLE CoachingSession (
    SessionLogID INT AUTO_INCREMENT PRIMARY KEY,
    SessionID INT NOT NULL,
    PlayerID INT NOT NULL,
    CoachID INT NOT NULL,
    AttendanceStatus ENUM('present', 'absent', 'late', 'partial') NOT NULL,
    PerformanceNotes TEXT COMMENT 'Coach notes about player performance in this session',
    SkillsWorkedOn TEXT COMMENT 'Specific skills practiced',
    AreasForImprovement TEXT COMMENT 'Areas identified for improvement',
    HomeworkAssigned TEXT COMMENT 'Practice tasks assigned for home',
    SessionRating INT COMMENT 'Overall session rating 1-10',
    
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (SessionID) REFERENCES Session(SessionID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (CoachID) REFERENCES User(UserID) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_coaching_session (SessionID),
    INDEX idx_coaching_player (PlayerID),
    INDEX idx_coaching_coach (CoachID),
    INDEX idx_coaching_attendance (AttendanceStatus)
) ENGINE=InnoDB COMMENT='Detailed coaching session logs and player performance tracking per session';

-- =============================================================================
-- SECTION 9: TRAINING PLANS
-- =============================================================================

-- Customized nutrition plans for players
CREATE TABLE NutritionPlan (
    PlanID INT AUTO_INCREMENT PRIMARY KEY,
    TrainerID INT NOT NULL,
   
    DietDetails TEXT NOT NULL,
    Duration INT COMMENT 'Duration in days',
    CreatedDate DATE DEFAULT (CURRENT_DATE),
    Status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    
    FOREIGN KEY (TrainerID) REFERENCES TrainerProfile(TrainerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_trainer_nutrition (TrainerID),
    INDEX idx_player_nutrition (PlayerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Customized nutrition plans for players';
CREATE TABLE NutritionPlan_Player (
    PlanID INT NOT NULL,
    PlayerID INT NOT NULL,
    AssignedDate DATE DEFAULT (CURRENT_DATE),
    PRIMARY KEY (PlanID, PlayerID),
    FOREIGN KEY (PlanID) REFERENCES NutritionPlan(PlanID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID)
        ON DELETE CASCADE ON UPDATE CASCADE
);
-- Customized workout plans for players
CREATE TABLE WorkoutPlan (
    PlanID INT AUTO_INCREMENT PRIMARY KEY,
    TrainerID INT NOT NULL,
    workoutname varchar(255),
    frequency ENUM('Daily', 'Weekly', 'Bi-weekly'),
    Duration INT COMMENT 'Duration in days',
    CreatedDate DATE DEFAULT (CURRENT_DATE),
    
    FOREIGN KEY (TrainerID) REFERENCES TrainerProfile(TrainerID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_trainer_workout (TrainerID)
) ENGINE=InnoDB COMMENT='Customized workout plans for players';

CREATE TABLE WorkoutPlan_Player (
    PlanID INT NOT NULL,
    PlayerID INT NOT NULL,
    AssignedDate DATE DEFAULT (CURRENT_DATE),
    PRIMARY KEY (PlanID, PlayerID),
    FOREIGN KEY (PlanID) REFERENCES WorkoutPlan(PlanID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID)
        ON DELETE CASCADE ON UPDATE CASCADE
);
-- Supplement recommendations for players
CREATE TABLE SupplementPlan (
    PlanID INT AUTO_INCREMENT PRIMARY KEY,
    TrainerID INT NOT NULL,
   
    SupplementDetails TEXT NOT NULL,
    Dosage VARCHAR(255),
    Duration INT COMMENT 'Duration in days',
    CreatedDate DATE DEFAULT (CURRENT_DATE),
    Status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    
    FOREIGN KEY (TrainerID) REFERENCES TrainerProfile(TrainerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_trainer_supplement (TrainerID),
    INDEX idx_player_supplement (PlayerID),
    INDEX idx_status (Status)
) ENGINE=InnoDB COMMENT='Supplement recommendations for players';
CREATE TABLE Supplement_Player (
    PlanID INT NOT NULL,
    PlayerID INT NOT NULL,
    AssignedDate DATE DEFAULT (CURRENT_DATE),
    PRIMARY KEY (PlanID, PlayerID),
    FOREIGN KEY (PlanID) REFERENCES SupplementPlan(PlanID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID)
        ON DELETE CASCADE ON UPDATE CASCADE
);
-- =============================================================================
-- SECTION 10: MEDICAL RECORDS
-- =============================================================================

-- Medical records and injury tracking
CREATE TABLE PlayerMedicalRecord (
    RecordID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    InjuryDetails TEXT NOT NULL,
    Diagnosis TEXT,
    TreatmentGiven TEXT,
    RecoveryStatus ENUM('recovering', 'recovered', 'chronic', 'ongoing') DEFAULT 'ongoing',
    
    -- 🆕 New Columns
    InjuryDate DATE NOT NULL COMMENT 'Date when the injury occurred',
    HappenedAtAcademy ENUM('yes', 'no') DEFAULT 'no' COMMENT 'Did the injury occur at the academy?',
    RestDaysNeeded INT DEFAULT 0 COMMENT 'Estimated rest days required for recovery',
    DiagnosisReceiptURL VARCHAR(255) COMMENT 'Path or URL of uploaded diagnosis receipt image/file',
    
    ReportedDate DATE NOT NULL COMMENT 'Date when report was created',
    ReportedBy INT COMMENT 'Doctor, trainer, or player who reported',
    verifyStatus ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
     FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ReportedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_player_medical (PlayerID),
    INDEX idx_reported_date (ReportedDate),
    INDEX idx_recovery_status (RecoveryStatus)
) ENGINE=InnoDB COMMENT='Medical records and injury tracking';

-- =============================================================================
-- SECTION 11: ENHANCED COMMUNICATION SYSTEM (Additional Tables)
-- =============================================================================

-- Contact form submissions from website visitors and registered users
CREATE TABLE ContactMessage (
    ContactID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NULL COMMENT 'nullable for guest visitors',
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    PhoneNumber VARCHAR(20) COMMENT 'Optional contact number',
    Subject VARCHAR(255),
    Message TEXT NOT NULL,
    MessageType ENUM('General Inquiry', 'Admission', 'Facility Booking', 'Complaint', 'Suggestion', 'Technical Support') DEFAULT 'General Inquiry',
    SubmittedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('new', 'read', 'responded', 'closed') DEFAULT 'new',
    RespondedBy INT NULL COMMENT 'Staff member who responded',
    ResponseMessage TEXT COMMENT 'Staff response to the inquiry',
    ResponseDate DATETIME COMMENT 'When response was sent',
    Priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (RespondedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_contact_user (UserID),
    INDEX idx_contact_status (Status),
    INDEX idx_contact_type (MessageType),
    INDEX idx_contact_priority (Priority)
) ENGINE=InnoDB COMMENT='Contact form submissions from website visitors and registered users';

-- Contact form submissions and inquiries (Essential for website functionality)
CREATE TABLE ContactUs (
    ContactID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NULL COMMENT 'Nullable for guest visitors',
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    PhoneNumber VARCHAR(20) COMMENT 'Optional contact number',
    Subject VARCHAR(255),
    Message TEXT NOT NULL,
    MessageType ENUM('General Inquiry', 'Admission', 'Facility Booking', 'Complaint', 'Suggestion', 'Technical Support') DEFAULT 'General Inquiry',
    SubmittedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('new', 'read', 'responded', 'closed') DEFAULT 'new',
    RespondedBy INT NULL COMMENT 'Staff member who responded',
    ResponseMessage TEXT COMMENT 'Staff response to the inquiry',
    ResponseDate DATETIME COMMENT 'When response was sent',
    Priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (RespondedBy) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_contact_user (UserID),
    INDEX idx_contact_status (Status),
    INDEX idx_contact_type (MessageType),
    INDEX idx_contact_priority (Priority),
    INDEX idx_contact_date (SubmittedDate)
) ENGINE=InnoDB COMMENT='Contact form submissions and inquiries - Essential for website functionality';

-- Feedback and ratings between users
CREATE TABLE Feedback (
    FeedbackID INT AUTO_INCREMENT PRIMARY KEY,
    FromUserID INT NOT NULL,
    ToUserID INT NOT NULL,
    Content TEXT NOT NULL,
    Rating INT COMMENT 'Rating from 1 to 5',
    Category ENUM('coach', 'trainer', 'facility', 'equipment', 'shop', 'general'),
    CreatedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    
    FOREIGN KEY (FromUserID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ToUserID) REFERENCES User(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_from_user (FromUserID),
    INDEX idx_to_user (ToUserID),
    INDEX idx_rating (Rating),
    INDEX idx_category (Category),
    INDEX idx_status (Status),
    CHECK (Rating >= 1 AND Rating <= 5)
) ENGINE=InnoDB COMMENT='Feedback and ratings between users';

-- =============================================================================
-- SECTION 12: RATING AND REVIEW SYSTEM
-- =============================================================================

-- Player reviews and ratings for shop products
CREATE TABLE ProductReview (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    ProductID INT NOT NULL,
    PlayerID INT NOT NULL,
    OrderID INT NULL COMMENT 'Reference to purchase order if bought',
    Rating INT NOT NULL COMMENT 'Rating from 1 to 5 stars',
    ReviewTitle VARCHAR(255),
    ReviewText TEXT,
    Pros TEXT COMMENT 'What the player liked about the product',
    Cons TEXT COMMENT 'What could be improved',
    WouldRecommend BOOLEAN DEFAULT TRUE,
    VerifiedPurchase BOOLEAN DEFAULT FALSE COMMENT 'True if player actually bought/rented the product',
    ReviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'approved', 'rejected', 'flagged') DEFAULT 'pending',
    ModeratedBy INT NULL COMMENT 'Staff who moderated the review',
    ModerationNotes TEXT COMMENT 'Reason for approval/rejection',
    HelpfulVotes INT DEFAULT 0 COMMENT 'Number of users who found this review helpful',
    
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (OrderID) REFERENCES ProductOrder(OrderID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (ModeratedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_product_review (ProductID),
    INDEX idx_player_review (PlayerID),
    INDEX idx_rating (Rating),
    INDEX idx_status (Status),
    INDEX idx_review_date (ReviewDate),
    CHECK (Rating >= 1 AND Rating <= 5)
) ENGINE=InnoDB COMMENT='Player reviews and ratings for shop products';

-- Player reviews and ratings for rented equipment
CREATE TABLE EquipmentReview (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    EquipmentID INT NOT NULL,
    PlayerID INT NOT NULL,
    RentalID INT NOT NULL COMMENT 'Reference to equipment rental',
    Rating INT NOT NULL COMMENT 'Rating from 1 to 5 stars',
    ReviewTitle VARCHAR(255),
    ReviewText TEXT,
    ConditionRating INT NOT NULL COMMENT 'Equipment condition rating 1-5',
    UsabilityRating INT NOT NULL COMMENT 'How easy it was to use 1-5',
    WouldRecommend BOOLEAN DEFAULT TRUE,
    ReviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'approved', 'rejected', 'flagged') DEFAULT 'pending',
    ModeratedBy INT NULL COMMENT 'Staff who moderated the review',
    
    FOREIGN KEY (EquipmentID) REFERENCES Equipment(EquipmentID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (RentalID) REFERENCES EquipmentRental(RentalID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ModeratedBy) REFERENCES ShopEmployeeProfile(ShopEmployeeID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_equipment_review (EquipmentID),
    INDEX idx_player_review (PlayerID),
    INDEX idx_rental_review (RentalID),
    INDEX idx_rating (Rating),
    INDEX idx_status (Status),
    CHECK (Rating >= 1 AND Rating <= 5),
    CHECK (ConditionRating >= 1 AND ConditionRating <= 5),
    CHECK (UsabilityRating >= 1 AND UsabilityRating <= 5)
) ENGINE=InnoDB COMMENT='Player reviews and ratings for rented equipment';

-- Player reviews and ratings for coaches
CREATE TABLE CoachReview (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    CoachID INT NOT NULL,
    PlayerID INT NOT NULL,
    AppointmentID INT NULL COMMENT 'Related appointment if applicable',
    TeachingQuality INT NOT NULL COMMENT 'Rating 1-5 for teaching quality',
    Communication INT NOT NULL COMMENT 'Rating 1-5 for communication skills',
    Punctuality INT NOT NULL COMMENT 'Rating 1-5 for punctuality',
    Knowledge INT NOT NULL COMMENT 'Rating 1-5 for cricket knowledge',
    OverallRating DECIMAL(3,1) COMMENT 'Calculated average of all ratings',
    ReviewTitle VARCHAR(255),
    ReviewText TEXT,
    WouldRecommend BOOLEAN DEFAULT TRUE,
    ReviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'approved', 'rejected', 'flagged') DEFAULT 'approved',
    CoachResponse TEXT COMMENT 'Coach response to the review',
    ResponseDate DATETIME COMMENT 'When coach responded',
    
    FOREIGN KEY (CoachID) REFERENCES CoachProfile(CoachID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_coach_review (CoachID),
    INDEX idx_player_review (PlayerID),
    INDEX idx_overall_rating (OverallRating),
    INDEX idx_review_date (ReviewDate),
    INDEX idx_status (Status),
    CHECK (TeachingQuality >= 1 AND TeachingQuality <= 5 
                                      AND Communication >= 1 AND Communication <= 5 
                                      AND Punctuality >= 1 AND Punctuality <= 5 
                                      AND Knowledge >= 1 AND Knowledge <= 5)
) ENGINE=InnoDB COMMENT='Player reviews and ratings for coaches';

-- Player reviews and ratings for trainers
CREATE TABLE TrainerReview (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    TrainerID INT NOT NULL,
    PlayerID INT NOT NULL,
    ProgramEffectiveness INT NOT NULL COMMENT 'Rating 1-5 for program effectiveness',
    MotivationSkills INT NOT NULL COMMENT 'Rating 1-5 for motivation',
    Professionalism INT NOT NULL COMMENT 'Rating 1-5 for professionalism',
    Knowledge INT NOT NULL COMMENT 'Rating 1-5 for fitness knowledge',
    OverallRating DECIMAL(3,1) COMMENT 'Calculated average of all ratings',
    ReviewTitle VARCHAR(255),
    ReviewText TEXT,
    WouldRecommend BOOLEAN DEFAULT TRUE,
    ReviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'approved', 'rejected', 'flagged') DEFAULT 'approved',
    TrainerResponse TEXT COMMENT 'Trainer response to the review',
    ResponseDate DATETIME COMMENT 'When trainer responded',
    
    FOREIGN KEY (TrainerID) REFERENCES TrainerProfile(TrainerID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES PlayerProfile(PlayerID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_trainer_review (TrainerID),
    INDEX idx_player_review (PlayerID),
    INDEX idx_overall_rating (OverallRating),
    INDEX idx_review_date (ReviewDate),
    INDEX idx_status (Status),
    CHECK (ProgramEffectiveness >= 1 AND ProgramEffectiveness <= 5 
                                        AND MotivationSkills >= 1 AND MotivationSkills <= 5 
                                        AND Professionalism >= 1 AND Professionalism <= 5 
                                        AND Knowledge >= 1 AND Knowledge <= 5)
) ENGINE=InnoDB COMMENT='Player reviews and ratings for trainers';

-- =============================================================================
-- SECTION 14: ENHANCED TRIGGERS AND STORED PROCEDURES
-- =============================================================================

-- Trigger to automatically create PlayerOverallStats when PlayerProfile is created
DELIMITER //
CREATE TRIGGER tr_create_player_stats 
AFTER INSERT ON PlayerProfile 
FOR EACH ROW
BEGIN
    INSERT INTO PlayerOverallStats (PlayerID) VALUES (NEW.PlayerID);
END//
DELIMITER ;

-- Note: ProductCart and EquipmentCart are created on-demand when players add items/reservations

-- Enhanced trigger for user account creation with security setup
DELIMITER //
CREATE TRIGGER tr_user_account_creation 
AFTER INSERT ON User 
FOR EACH ROW
BEGIN
    -- Log account creation
    INSERT INTO ActivityLog (UserID, Action, Description) 
    VALUES (NEW.UserID, 'account_created', CONCAT('New ', NEW.Role, ' account created'));
    
    -- Send welcome notification
    INSERT INTO Notification (UserID, Type, Title, Message) 
    VALUES (NEW.UserID, 'welcome', 'Welcome to Cricket Academy', 
            'Your account has been created successfully. Please complete your profile setup.');
    
    -- Queue welcome email
    INSERT INTO EmailLog (UserID, RecipientEmail, Subject, EmailType) 
    VALUES (NEW.UserID, NEW.Email, 'Welcome to Cricket Academy', 'welcome');
END//
DELIMITER ;

-- Trigger for performance update validation and processing
DELIMITER //
CREATE TRIGGER tr_performance_update_validation
BEFORE INSERT ON PerformanceUpdate
FOR EACH ROW
BEGIN
    DECLARE coach_role VARCHAR(50);
    DECLARE is_assigned INT DEFAULT 0;
    DECLARE is_head_coach BOOLEAN DEFAULT FALSE;
    
    -- Check if user is a coach
    SELECT Role INTO coach_role FROM User WHERE UserID = NEW.CoachID;
    IF coach_role NOT IN ('Coach', 'Admin') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Only coaches and admins can update performance stats';
    END IF;
    
    -- Check if coach is assigned to player (unless admin or head coach)
    IF coach_role = 'Coach' THEN
        SELECT COUNT(*), COALESCE(MAX(cp.IsHeadCoach), FALSE) 
        INTO is_assigned, is_head_coach
        FROM PlayerCoachAssignment pca
        LEFT JOIN CoachProfile cp ON pca.CoachID = cp.CoachID
        WHERE pca.PlayerID = NEW.PlayerID AND pca.CoachID = NEW.CoachID AND pca.Status = 'active';
        
        IF is_assigned = 0 AND NOT is_head_coach THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Coach is not assigned to this player and cannot update performance stats';
        END IF;
        
        -- Auto-approve for head coaches and admins
        IF is_head_coach OR coach_role = 'Admin' THEN
            SET NEW.Status = 'approved';
            SET NEW.ApprovedBy = NEW.CoachID;
            SET NEW.ApprovedAt = NOW();
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger for performance update logging
DELIMITER //
CREATE TRIGGER tr_performance_update_logging
AFTER INSERT ON PerformanceUpdate
FOR EACH ROW
BEGIN
    -- Log the performance update
    INSERT INTO ActivityLog (UserID, Action, Description) 
    VALUES (NEW.PlayerID, 'performance_update_created', 
            CONCAT('Performance update created by coach ID: ', NEW.CoachID, ' - Type: ', NEW.UpdateType));
    
    -- Notify player about performance update
    INSERT INTO Notification (UserID, Type, Title, Message) 
    VALUES (NEW.PlayerID, 'performance_update', 'Performance Update from Coach',
            'Your coach has updated your performance stats. Check your dashboard for details.');
    
    -- Create live notification for immediate visibility
    INSERT INTO LiveNotification (UserID, Type, Title, Message, ExpiresAt) 
    VALUES (NEW.PlayerID, 'performance_update', 'New Performance Update',
            'Your coach has updated your performance stats.', DATE_ADD(NOW(), INTERVAL 48 HOUR));
END//
DELIMITER ;

-- Trigger to update overall stats when performance update is approved
DELIMITER //
CREATE TRIGGER tr_update_overall_stats
AFTER UPDATE ON PerformanceUpdate
FOR EACH ROW
BEGIN
    IF NEW.Status = 'approved' AND OLD.Status != 'approved' THEN
        -- Update overall statistics
        UPDATE PlayerOverallStats 
        SET 
            TotalRuns = TotalRuns + COALESCE(NEW.RunsScored, 0),
            TotalWickets = TotalWickets + COALESCE(NEW.WicketsTaken, 0),
            HighestScore = GREATEST(HighestScore, COALESCE(NEW.RunsScored, 0)),
            LastUpdatedBy = NEW.CoachID
        WHERE PlayerID = NEW.PlayerID;
        
        -- Log stats update
        INSERT INTO ActivityLog (UserID, Action, Description) 
        VALUES (NEW.PlayerID, 'stats_updated_by_coach', 
                CONCAT('Overall stats updated by coach ID: ', NEW.CoachID, ' based on performance update ID: ', NEW.UpdateID));
        
        -- Notify player about stats update
        INSERT INTO Notification (UserID, Type, Title, Message) 
        VALUES (NEW.PlayerID, 'stats_updated', 'Performance Statistics Updated',
                'Your overall performance statistics have been updated based on recent coaching assessments.');
    END IF;
END//
DELIMITER ;

-- Trigger for coaching session logging
DELIMITER //
CREATE TRIGGER tr_coaching_session_logging
AFTER INSERT ON CoachingSession
FOR EACH ROW
BEGIN
    -- Log coaching session activity
    INSERT INTO ActivityLog (UserID, Action, Description) 
    VALUES (NEW.PlayerID, 'coaching_session_logged', 
            CONCAT('Coaching session attendance: ', NEW.AttendanceStatus, ' - Coach ID: ', NEW.CoachID, ' - Rating: ', COALESCE(NEW.SessionRating, 'N/A')));
    
    -- Update player stats timestamp if present
    IF NEW.AttendanceStatus = 'present' THEN
        UPDATE PlayerOverallStats 
        SET LastUpdated = NOW(), LastUpdatedBy = NEW.CoachID
        WHERE PlayerID = NEW.PlayerID;
    END IF;
    
    -- Notify about poor attendance
    IF NEW.AttendanceStatus IN ('absent', 'late') THEN
        INSERT INTO Notification (UserID, Type, Title, Message) 
        VALUES (NEW.PlayerID, 'attendance_alert', 'Attendance Notice',
                CONCAT('You were marked as ', NEW.AttendanceStatus, ' for your coaching session. Please ensure regular attendance.'));
    END IF;
END//
DELIMITER ;


-- Enhanced trigger for automatic role-based profile creation
DELIMITER //
CREATE TRIGGER tr_create_role_based_profile 
AFTER INSERT ON User 
FOR EACH ROW
BEGIN
    -- Create profile based on user role
    CASE NEW.Role
        WHEN 'player' THEN
            INSERT INTO PlayerProfile (
                PlayerID, 
                SubscriptionType, 
                BattingStyle, 
                BowlingStyle
            ) VALUES (
                NEW.UserID, 
                'basic', 
                NULL, 
                NULL
            );
            
        WHEN 'coach' THEN
            INSERT INTO CoachProfile (
                CoachID, 
                Specialization, 
                Experience, 
                IsHeadCoach
            ) VALUES (
                NEW.UserID, 
                NULL, 
                0, 
                FALSE
            );
            
        WHEN 'trainer' THEN
            INSERT INTO TrainerProfile (
                TrainerID, 
                Experience
            ) VALUES (
                NEW.UserID, 
                0
            );
            
        WHEN 'shop' THEN
            INSERT INTO ShopEmployeeProfile (
                ShopEmployeeID, 
                Department, 
                HireDate
            ) VALUES (
                NEW.UserID, 
                'General', 
                CURDATE()
            );
            
        -- Admin role doesn't need a separate profile table
        WHEN 'admin' THEN
            -- Log admin account creation for security
            INSERT INTO ActivityLog (UserID, Action, Description) 
            VALUES (NEW.UserID, 'admin_account_created', 'New admin account created - requires verification');
            
        ELSE
            -- Log unknown role for debugging
            INSERT INTO ActivityLog (UserID, Action, Description) 
            VALUES (NEW.UserID, 'unknown_role_registered', CONCAT('Unknown role registered: ', NEW.Role));
    END CASE;
    
    -- Log account creation for all users
    INSERT INTO ActivityLog (UserID, Action, Description) 
    VALUES (NEW.UserID, 'account_created', CONCAT('New ', NEW.Role, ' account created'));
    
    -- Send welcome notification for all users
    INSERT INTO Notification (UserID, Type, Title, Message) 
    VALUES (NEW.UserID, 'welcome', 'Welcome to Elite Cricket Academy', 
            'Your account has been created successfully. Please complete your profile setup.');
            
    -- Queue welcome email for all users
    INSERT INTO EmailLog (UserID, RecipientEmail, Subject, EmailType) 
    VALUES (NEW.UserID, NEW.Email, 'Welcome to Elite Cricket Academy', 'welcome');
END//
DELIMITER ;

-- Stored procedure to validate coach-player permissions
DELIMITER //
CREATE PROCEDURE ValidateCoachPlayerPermission(
    IN p_coach_id INT,
    IN p_player_id INT,
    OUT p_has_permission BOOLEAN,
    OUT p_permission_type VARCHAR(50)
)
BEGIN
    DECLARE coach_role VARCHAR(50);
    DECLARE is_head_coach BOOLEAN DEFAULT FALSE;
    DECLARE assignment_count INT DEFAULT 0;
    
    -- Get coach role
    SELECT Role INTO coach_role FROM User WHERE UserID = p_coach_id;
    
    -- Check if admin
    IF coach_role = 'Admin' THEN
        SET p_has_permission = TRUE;
        SET p_permission_type = 'Admin Access';
    ELSE
        -- Check if head coach
        SELECT IsHeadCoach INTO is_head_coach FROM CoachProfile WHERE CoachID = p_coach_id;
        
        IF is_head_coach THEN
            SET p_has_permission = TRUE;
            SET p_permission_type = 'Head Coach Access';
        ELSE
            -- Check assignment
            SELECT COUNT(*) INTO assignment_count 
            FROM PlayerCoachAssignment 
            WHERE CoachID = p_coach_id AND PlayerID = p_player_id AND Status = 'active';
            
            IF assignment_count > 0 THEN
                SET p_has_permission = TRUE;
                SET p_permission_type = 'Assigned Coach Access';
            ELSE
                SET p_has_permission = FALSE;
                SET p_permission_type = 'No Permission';
            END IF;
        END IF;
    END IF;
END//
DELIMITER ;

-- Stored procedure for automatic coach assignment based on age and specialization
DELIMITER //
CREATE PROCEDURE AutoAssignCoach(
    IN p_player_id INT,
    OUT p_assigned_coach_id INT,
    OUT p_assignment_result VARCHAR(255)
)
BEGIN
    DECLARE player_age INT;
    DECLARE player_batting_style VARCHAR(50);
    DECLARE player_bowling_style VARCHAR(50);
    DECLARE suggested_coach_id INT;
    
    -- Calculate player age
    SELECT YEAR(CURDATE()) - YEAR(DateOfBirth) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(DateOfBirth, '%m%d')) 
    INTO player_age
    FROM User u JOIN PlayerProfile p ON u.UserID = p.PlayerID
    WHERE p.PlayerID = p_player_id;
    
    -- Get player playing styles
    SELECT BattingStyle, BowlingStyle INTO player_batting_style, player_bowling_style
    FROM PlayerProfile WHERE PlayerID = p_player_id;
    
    -- Find best coach match based on age and specialization
    SELECT c.CoachID INTO suggested_coach_id
    FROM CoachProfile c
    JOIN User u ON c.CoachID = u.UserID
    WHERE u.Status = 'active'
    AND (
        (player_age <= 12 AND c.Specialization IN ('All-rounder', 'Batting')) OR
        (player_age BETWEEN 13 AND 16 AND c.Specialization IN ('All-rounder', 'Batting', 'Bowling')) OR
        (player_age >= 17)
    )
    ORDER BY 
        CASE 
            WHEN c.Specialization = 'All-rounder' THEN 1
            WHEN (player_bowling_style != 'None' AND c.Specialization = 'Bowling') THEN 2
            WHEN c.Specialization = 'Batting' THEN 3
            ELSE 4
        END,
        c.Experience DESC
    LIMIT 1;
    
    -- Create assignment if coach found
    IF suggested_coach_id IS NOT NULL THEN
        INSERT INTO PlayerCoachAssignment (PlayerID, CoachID, AssignmentType, Notes)
        VALUES (p_player_id, suggested_coach_id, 'regular', 'Auto-assigned based on age and playing style');
        
        SET p_assigned_coach_id = suggested_coach_id;
        SET p_assignment_result = 'Coach assigned successfully based on age and specialization';
        
        -- Send notification to player
        INSERT INTO Notification (UserID, Type, Title, Message)
        VALUES (p_player_id, 'coach_assignment', 'Coach Assigned', 
                CONCAT('You have been assigned to a coach based on your profile. Check your dashboard for details.'));
        
        -- Send notification to coach
        INSERT INTO Notification (UserID, Type, Title, Message)
        VALUES (suggested_coach_id, 'player_assignment', 'New Player Assigned', 
                CONCAT('A new player has been assigned to you. Please review their profile and plan initial sessions.'));
    ELSE
        SET p_assigned_coach_id = NULL;
        SET p_assignment_result = 'No suitable coach available for assignment';
    END IF;
END//
DELIMITER ;

-- Trigger to update equipment availability when rented
DELIMITER //
CREATE TRIGGER tr_equipment_rental_status 
AFTER INSERT ON EquipmentRental 
FOR EACH ROW
BEGIN
    IF NEW.Status = 'active' THEN
        UPDATE Equipment SET AvailabilityStatus = 'rented' WHERE EquipmentID = NEW.EquipmentID;
    END IF;
END//

CREATE TRIGGER tr_equipment_return_status 
AFTER UPDATE ON EquipmentRental 
FOR EACH ROW
BEGIN
    IF NEW.Status = 'returned' AND OLD.Status = 'active' THEN
        UPDATE Equipment SET AvailabilityStatus = 'available' WHERE EquipmentID = NEW.EquipmentID;
    END IF;
END//
DELIMITER ;

-- =============================================================================
-- SECTION 14: INITIAL DATA SETUP
-- =============================================================================

-- Insert default admin user
INSERT INTO User (Name, DateOfBirth, Email, Role, Username, PasswordHash, PhoneNumber, Address) 
VALUES 
('Admin User', '1990-01-01', 'admin@cricketacademy.com', 'Admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', 'Academy Headquarters');

-- Insert sample facilities
INSERT INTO Facility (Name, Location, Capacity, HourlyRate) VALUES
('Practice Net 1', 'North Ground', 6, 50.00),
('Practice Net 2', 'North Ground', 6, 50.00),
('Bowling Machine', 'Training Center', 1, 75.00),
('Main Ground', 'Center Field', 22, 200.00),
('Indoor Training Hall', 'Building A', 20, 100.00);

-- Insert sample equipment
INSERT INTO Equipment (Name, Description, Category, RentalPrice, PurchasePrice, EqCondition) VALUES
('Cricket Bat - Professional', 'High-quality willow cricket bat', 'Batting', 25.00, 150.00, 'new'),
('Cricket Ball - Leather', 'Professional leather cricket ball', 'Bowling', 5.00, 15.00, 'new'),
('Helmet - Professional', 'Safety helmet with grill', 'Protective', 15.00, 80.00, 'new'),
('Batting Pads', 'Professional batting pads', 'Protective', 20.00, 100.00, 'good'),
('Wicket Keeping Gloves', 'Professional WK gloves', 'Protective', 18.00, 90.00, 'good');

-- =============================================================================
-- SECTION 15: VIEWS FOR COMMON QUERIES AND PERFORMANCE MANAGEMENT
-- =============================================================================

-- View for active players with their profiles
CREATE VIEW v_active_players AS
SELECT 
    u.UserID, u.Name, u.Email, u.PhoneNumber, u.DateOfBirth,
    pp.BattingStyle, pp.BowlingStyle, pp.JerseyNumber,
    pos.MatchesPlayed, pos.TotalRuns, pos.TotalWickets, pos.BattingAverage
FROM User u
JOIN PlayerProfile pp ON u.UserID = pp.PlayerID
JOIN PlayerOverallStats pos ON pp.PlayerID = pos.PlayerID
WHERE u.Status = 'active';

-- View for upcoming sessions with enrollment count
CREATE VIEW v_upcoming_sessions AS
SELECT 
    s.SessionID, s.Name, s.SessionType, s.Date, s.StartTime, s.EndTime, s.Location,
    u.Name as InstructorName,
    COUNT(se.EnrollmentID) as EnrolledCount, s.MaxParticipants
FROM Session s
JOIN User u ON s.CoachOrTrainerID = u.UserID
LEFT JOIN SessionEnrollment se ON s.SessionID = se.SessionID AND se.Status = 'enrolled'
WHERE s.Status = 'active' AND s.Date >= CURDATE()
GROUP BY s.SessionID, s.Name, s.SessionType, s.Date, s.StartTime, s.EndTime, s.Location, u.Name, s.MaxParticipants;

-- View for facility availability
CREATE VIEW v_facility_availability AS
SELECT 
    f.FacilityID, f.Name, f.Location, f.Capacity, f.HourlyRate,
    f.AvailabilityStatus,
    COUNT(fb.FacilityBookingID) as TodayBookings
FROM Facility f
LEFT JOIN FacilityBooking fb ON f.FacilityID = fb.FacilityID 
    AND fb.BookingDate = CURDATE() 
    AND fb.Status = 'confirmed'
GROUP BY f.FacilityID, f.Name, f.Location, f.Capacity, f.HourlyRate, f.AvailabilityStatus;

-- View for coach-player permission management
CREATE VIEW CoachPlayerPermissions AS
SELECT 
    cp.CoachID,
    u_coach.Name AS CoachName,
    cp.IsHeadCoach,
    u_coach.Role,
    pp.PlayerID,
    u_player.Name AS PlayerName,
    pca.Status AS AssignmentStatus,
    pca.AssignmentType,
    pca.AssignedDate,
    CASE 
        WHEN u_coach.Role = 'Admin' THEN 'Full Access'
        WHEN cp.IsHeadCoach = TRUE THEN 'Head Coach Access'
        WHEN pca.Status = 'active' THEN 'Assigned Player Access'
        ELSE 'No Access'
    END AS PermissionLevel
FROM User u_coach
JOIN CoachProfile cp ON u_coach.UserID = cp.CoachID
LEFT JOIN PlayerCoachAssignment pca ON cp.CoachID = pca.CoachID
LEFT JOIN PlayerProfile pp ON pca.PlayerID = pp.PlayerID
LEFT JOIN User u_player ON pp.PlayerID = u_player.UserID
WHERE u_coach.Status = 'active';

-- View for performance update summary
CREATE VIEW PerformanceUpdateSummary AS
SELECT 
    pp.PlayerID,
    u.Name AS PlayerName,
    COUNT(pu.UpdateID) AS TotalUpdates,
    COUNT(CASE WHEN pu.Status = 'approved' THEN 1 END) AS ApprovedUpdates,
    COUNT(CASE WHEN pu.Status = 'pending_approval' THEN 1 END) AS PendingUpdates,
    MAX(pu.UpdateDate) AS LastUpdateDate,
    AVG(pu.TechnicalRating) AS AvgTechnicalRating,
    AVG(pu.FitnessRating) AS AvgFitnessRating,
    AVG(pu.AttitudeRating) AS AvgAttitudeRating
FROM PlayerProfile pp
JOIN User u ON pp.PlayerID = u.UserID
LEFT JOIN PerformanceUpdate pu ON pp.PlayerID = pu.PlayerID
GROUP BY pp.PlayerID, u.Name;

-- View for coaching effectiveness analysis  
CREATE VIEW CoachingEffectiveness AS
SELECT 
    cp.CoachID,
    u_coach.Name AS CoachName,
    cp.Specialization,
    COUNT(DISTINCT pca.PlayerID) AS PlayersAssigned,
    COUNT(pu.UpdateID) AS PerformanceUpdatesGiven,
    COUNT(cs.SessionLogID) AS SessionsLogged,
    AVG(cs.SessionRating) AS AvgSessionRating,
    AVG(CASE WHEN pu.Status = 'approved' THEN pu.TechnicalRating END) AS AvgTechnicalRatingGiven,
    COUNT(CASE WHEN cs.AttendanceStatus = 'present' THEN 1 END) AS SessionsAttended,
    COUNT(CASE WHEN cs.AttendanceStatus IN ('absent', 'late') THEN 1 END) AS SessionsMissed
FROM CoachProfile cp
JOIN User u_coach ON cp.CoachID = u_coach.UserID
LEFT JOIN PlayerCoachAssignment pca ON cp.CoachID = pca.CoachID AND pca.Status = 'active'
LEFT JOIN PerformanceUpdate pu ON cp.CoachID = pu.CoachID
LEFT JOIN CoachingSession cs ON cp.CoachID = cs.CoachID
WHERE u_coach.Status = 'active'
GROUP BY cp.CoachID, u_coach.Name, cp.Specialization;

-- View for player development tracking
CREATE VIEW PlayerDevelopmentTracking AS
SELECT 
    pp.PlayerID,
    u_player.Name AS PlayerName,
    YEAR(CURDATE()) - YEAR(u_player.DateOfBirth) AS Age,
    pp.BattingStyle,
    pp.BowlingStyle,
    pos.TotalRuns,
    pos.TotalWickets,
    pos.BattingAverage,
    pos.BowlingAverage,
    COUNT(DISTINCT s.SessionID) AS SessionsAttended,
    COUNT(DISTINCT pu.UpdateID) AS PerformanceUpdatesReceived,
    AVG(cs.SessionRating) AS AvgSessionRating,
    MAX(pu.UpdateDate) AS LastPerformanceUpdate,
    u_coach.Name AS CurrentCoach
FROM PlayerProfile pp
JOIN User u_player ON pp.PlayerID = u_player.UserID
JOIN PlayerOverallStats pos ON pp.PlayerID = pos.PlayerID
LEFT JOIN SessionEnrollment se ON pp.PlayerID = se.PlayerID AND se.Status = 'attended'
LEFT JOIN Session s ON se.SessionID = s.SessionID
LEFT JOIN PerformanceUpdate pu ON pp.PlayerID = pu.PlayerID
LEFT JOIN CoachingSession cs ON pp.PlayerID = cs.PlayerID
LEFT JOIN PlayerCoachAssignment pca ON pp.PlayerID = pca.PlayerID AND pca.Status = 'active'
LEFT JOIN User u_coach ON pca.CoachID = u_coach.UserID
WHERE u_player.Status = 'active'
GROUP BY pp.PlayerID, u_player.Name, pp.BattingStyle, pp.BowlingStyle, pos.TotalRuns, pos.TotalWickets, pos.BattingAverage, pos.BowlingAverage, u_coach.Name;

-- =============================================================================
-- DATABASE SCHEMA CREATION COMPLETE
-- =============================================================================

SELECT 'Cricket Academy Management System database schema created successfully!' as Status;
