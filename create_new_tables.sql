-- ================================================================
-- NEW TABLES FOR CRICKET ACADEMY
-- 1. playerhealthvitals  - Weight, Height, HR, Body Fat, Lung Capacity
-- 2. playervaccination   - Vaccine name, dose, dates
-- 3. paymentmethod       - Saved cards/bank accounts
-- 4. conversation + message - Chat messages between users
-- 5. leaverequest        - Multi-day leave from training
-- ================================================================

-- ----------------------------------------------------------------
-- 1. PLAYER HEALTH VITALS
-- Tracks periodic health measurements for players
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `playerhealthvitals` (
    `VitalID` INT(11) NOT NULL AUTO_INCREMENT,
    `PlayerID` INT(11) NOT NULL,
    `RecordedDate` DATE NOT NULL,
    `Weight` DECIMAL(5,2) DEFAULT NULL COMMENT 'Weight in kg',
    `WeightStatus` ENUM('Underweight','Normal','Overweight','Obese') DEFAULT 'Normal',
    `Height` DECIMAL(5,2) DEFAULT NULL COMMENT 'Height in cm',
    `HeightStatus` ENUM('Below Avg','Normal','Above Avg','Tall') DEFAULT 'Normal',
    `RestingHeartRate` INT(11) DEFAULT NULL COMMENT 'Resting heart rate in bpm',
    `HeartRateStatus` ENUM('Excellent','Good','Normal','Above Normal','High') DEFAULT 'Normal',
    `BodyFatPercentage` DECIMAL(4,1) DEFAULT NULL COMMENT 'Body fat %',
    `BodyFatStatus` ENUM('Essential','Athletic','Fitness','Average','Above Average') DEFAULT 'Athletic',
    `LungCapacity` DECIMAL(3,1) DEFAULT NULL COMMENT 'Lung capacity in Litres',
    `LungCapacityStatus` ENUM('Below Avg','Average','Above Avg','Excellent') DEFAULT 'Above Avg',
    `BMI` DECIMAL(4,1) DEFAULT NULL COMMENT 'Body Mass Index (auto-calculated or manual)',
    `Notes` TEXT DEFAULT NULL,
    `RecordedBy` INT(11) DEFAULT NULL COMMENT 'Coach or Trainer who recorded',
    `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`VitalID`),
    KEY `idx_player_vitals` (`PlayerID`),
    KEY `idx_vital_date` (`RecordedDate`),
    CONSTRAINT `playerhealthvitals_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `playerhealthvitals_ibfk_2` FOREIGN KEY (`RecordedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Periodic health vitals tracking for players';

-- ----------------------------------------------------------------
-- 2. PLAYER VACCINATION
-- Vaccination records for each player
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `playervaccination` (
    `VaccinationID` INT(11) NOT NULL AUTO_INCREMENT,
    `PlayerID` INT(11) NOT NULL,
    `VaccineName` VARCHAR(255) NOT NULL COMMENT 'e.g., Hepatitis B, Tetanus, COVID-19',
    `DoseNumber` INT(11) DEFAULT 1 COMMENT 'Dose number (1st, 2nd, booster)',
    `Status` ENUM('Completed','Due','Overdue','Scheduled','Exempted') DEFAULT 'Completed',
    `DateAdministered` DATE DEFAULT NULL COMMENT 'Date vaccine was given',
    `NextDueDate` DATE DEFAULT NULL COMMENT 'Date next dose is due',
    `AdministeredBy` VARCHAR(255) DEFAULT NULL COMMENT 'Doctor/clinic name',
    `BatchNumber` VARCHAR(100) DEFAULT NULL,
    `Notes` TEXT DEFAULT NULL,
    `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    `UpdatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (`VaccinationID`),
    KEY `idx_player_vaccine` (`PlayerID`),
    KEY `idx_vaccine_status` (`Status`),
    KEY `idx_next_due` (`NextDueDate`),
    CONSTRAINT `playervaccination_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player vaccination records and schedule tracking';

-- ----------------------------------------------------------------
-- 3. PAYMENT METHOD
-- Saved payment cards/bank accounts for players
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `paymentmethod` (
    `MethodID` INT(11) NOT NULL AUTO_INCREMENT,
    `UserID` INT(11) NOT NULL,
    `MethodType` ENUM('visa','mastercard','amex','bank','dialog_pay','mobitel_pay','frimi','lankaqr') NOT NULL COMMENT 'Payment method type',
    `DisplayName` VARCHAR(255) NOT NULL COMMENT 'e.g., Visa ending 4242',
    `CardLastFour` VARCHAR(4) DEFAULT NULL COMMENT 'Last 4 digits of card',
    `ExpiryDate` VARCHAR(10) DEFAULT NULL COMMENT 'MM/YY format',
    `BankName` VARCHAR(255) DEFAULT NULL COMMENT 'For bank transfers',
    `AccountLastFour` VARCHAR(4) DEFAULT NULL COMMENT 'Last 4 digits of account',
    `IsDefault` TINYINT(1) DEFAULT 0 COMMENT 'Default payment method flag',
    `Icon` VARCHAR(50) DEFAULT 'credit-card' COMMENT 'FontAwesome icon name',
    `Color` VARCHAR(10) DEFAULT '#4A90E2' COMMENT 'Display color hex',
    `Status` ENUM('active','expired','removed') DEFAULT 'active',
    `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    `UpdatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (`MethodID`),
    KEY `idx_user_methods` (`UserID`),
    KEY `idx_method_status` (`Status`),
    CONSTRAINT `paymentmethod_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Saved payment methods for users';

-- ----------------------------------------------------------------
-- 4a. CONVERSATION
-- Chat conversations between two or more users
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `conversation` (
    `ConversationID` INT(11) NOT NULL AUTO_INCREMENT,
    `Title` VARCHAR(255) DEFAULT NULL COMMENT 'Optional conversation title for group chats',
    `Type` ENUM('direct','group','announcement') DEFAULT 'direct',
    `CreatedBy` INT(11) NOT NULL,
    `LastMessageAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`ConversationID`),
    KEY `idx_conv_created_by` (`CreatedBy`),
    KEY `idx_conv_last_msg` (`LastMessageAt`),
    CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chat conversations between users';

-- ----------------------------------------------------------------
-- 4b. CONVERSATION PARTICIPANT
-- Users participating in a conversation
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `conversationparticipant` (
    `ParticipantID` INT(11) NOT NULL AUTO_INCREMENT,
    `ConversationID` INT(11) NOT NULL,
    `UserID` INT(11) NOT NULL,
    `LastReadAt` DATETIME DEFAULT NULL COMMENT 'When user last read messages',
    `IsOnline` TINYINT(1) DEFAULT 0,
    `JoinedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`ParticipantID`),
    UNIQUE KEY `uk_conv_user` (`ConversationID`, `UserID`),
    KEY `idx_participant_user` (`UserID`),
    CONSTRAINT `convparticipant_ibfk_1` FOREIGN KEY (`ConversationID`) REFERENCES `conversation` (`ConversationID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `convparticipant_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Users participating in each conversation';

-- ----------------------------------------------------------------
-- 4c. MESSAGE
-- Individual messages within a conversation
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `message` (
    `MessageID` INT(11) NOT NULL AUTO_INCREMENT,
    `ConversationID` INT(11) NOT NULL,
    `SenderID` INT(11) NOT NULL,
    `Content` TEXT NOT NULL,
    `MessageType` ENUM('text','image','file','announcement') DEFAULT 'text',
    `IsEdited` TINYINT(1) DEFAULT 0,
    `IsDeleted` TINYINT(1) DEFAULT 0,
    `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    `UpdatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (`MessageID`),
    KEY `idx_msg_conversation` (`ConversationID`),
    KEY `idx_msg_sender` (`SenderID`),
    KEY `idx_msg_created` (`CreatedAt`),
    CONSTRAINT `message_ibfk_1` FOREIGN KEY (`ConversationID`) REFERENCES `conversation` (`ConversationID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `message_ibfk_2` FOREIGN KEY (`SenderID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chat messages within conversations';

-- ----------------------------------------------------------------
-- 5. LEAVE REQUEST
-- Multi-day leave requests from players
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leaverequest` (
    `LeaveID` INT(11) NOT NULL AUTO_INCREMENT,
    `PlayerID` INT(11) NOT NULL,
    `CoachID` INT(11) DEFAULT NULL COMMENT 'Assigned coach who reviews the request',
    `Title` VARCHAR(255) NOT NULL,
    `LeaveType` ENUM('medical','personal','family','academic','tournament','other') NOT NULL DEFAULT 'personal',
    `StartDate` DATE NOT NULL,
    `EndDate` DATE NOT NULL,
    `TotalDays` INT(11) NOT NULL,
    `Reason` TEXT NOT NULL,
    `MedicalCertificate` VARCHAR(500) DEFAULT NULL COMMENT 'File path to uploaded certificate',
    `Status` ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    `IsUrgent` TINYINT(1) DEFAULT 0,
    `ReviewedBy` INT(11) DEFAULT NULL COMMENT 'Coach/Admin who reviewed',
    `ReviewedAt` DATETIME DEFAULT NULL,
    `ReviewNotes` TEXT DEFAULT NULL COMMENT 'Approval/rejection notes',
    `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP(),
    `UpdatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (`LeaveID`),
    KEY `idx_leave_player` (`PlayerID`),
    KEY `idx_leave_coach` (`CoachID`),
    KEY `idx_leave_status` (`Status`),
    KEY `idx_leave_dates` (`StartDate`, `EndDate`),
    CONSTRAINT `leaverequest_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `leaverequest_ibfk_2` FOREIGN KEY (`CoachID`) REFERENCES `coachprofile` (`CoachID`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `leaverequest_ibfk_3` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player leave requests for multi-day absences from training';


-- ================================================================
-- SAMPLE DATA (Sri Lankan themed)
-- ================================================================

-- ----------------------------------------------------------------
-- 1. PLAYER HEALTH VITALS SAMPLE DATA
-- Players: 6, 7, 15, 16, 18, 20
-- RecordedBy: Coaches (3,11,12,17,23) or Trainers (4,10)
-- ----------------------------------------------------------------
INSERT INTO `playerhealthvitals` (`PlayerID`, `RecordedDate`, `Weight`, `WeightStatus`, `Height`, `HeightStatus`, `RestingHeartRate`, `HeartRateStatus`, `BodyFatPercentage`, `BodyFatStatus`, `LungCapacity`, `LungCapacityStatus`, `BMI`, `Notes`, `RecordedBy`) VALUES
-- Player 6 (Esandu) - multiple records over time
(6, '2025-12-01', 72.50, 'Normal', 175.00, 'Normal', 68, 'Excellent', 12.5, 'Athletic', 4.2, 'Above Avg', 23.7, 'Good overall fitness. Maintain current routine.', 4),
(6, '2026-01-15', 73.00, 'Normal', 175.00, 'Normal', 66, 'Excellent', 12.0, 'Athletic', 4.3, 'Above Avg', 23.8, 'Slight improvement in cardiovascular fitness.', 4),
(6, '2026-02-10', 72.80, 'Normal', 175.00, 'Normal', 65, 'Excellent', 11.8, 'Athletic', 4.4, 'Excellent', 23.8, 'Peak form. Ready for upcoming tournaments.', 10),

-- Player 7 (Vijini)
(7, '2025-12-01', 58.00, 'Normal', 162.00, 'Normal', 72, 'Good', 18.0, 'Fitness', 3.5, 'Average', 22.1, 'Healthy vitals. Focus on endurance training.', 4),
(7, '2026-01-15', 57.50, 'Normal', 162.00, 'Normal', 70, 'Good', 17.5, 'Fitness', 3.6, 'Average', 21.9, 'Improved heart rate from cardio program.', 10),
(7, '2026-02-10', 57.80, 'Normal', 162.00, 'Normal', 69, 'Excellent', 17.2, 'Fitness', 3.7, 'Above Avg', 22.0, 'Good progress in lung capacity.', 4),

-- Player 15 (Swairi)
(15, '2025-12-05', 65.00, 'Normal', 168.00, 'Normal', 74, 'Good', 15.0, 'Fitness', 3.8, 'Above Avg', 23.0, 'Good baseline measurements.', 4),
(15, '2026-02-05', 64.50, 'Normal', 168.50, 'Normal', 71, 'Good', 14.5, 'Athletic', 3.9, 'Above Avg', 22.7, 'Improved body composition.', 10),

-- Player 16 (esandu)
(16, '2025-12-10', 78.00, 'Normal', 180.00, 'Above Avg', 70, 'Good', 13.0, 'Athletic', 4.5, 'Excellent', 24.1, 'Tall build, good for fast bowling.', 4),
(16, '2026-02-01', 77.50, 'Normal', 180.00, 'Above Avg', 67, 'Excellent', 12.5, 'Athletic', 4.6, 'Excellent', 23.9, 'Excellent cardio improvement.', 4),

-- Player 18 (vijini liyanamana)
(18, '2026-01-10', 55.00, 'Normal', 158.00, 'Normal', 75, 'Normal', 20.0, 'Average', 3.3, 'Average', 22.0, 'Average fitness. Needs more cardio work.', 10),
(18, '2026-02-10', 54.50, 'Normal', 158.00, 'Normal', 73, 'Good', 19.5, 'Fitness', 3.4, 'Average', 21.8, 'Slight improvement with new training plan.', 10),

-- Player 20 (esandu yapa)
(20, '2025-12-15', 70.00, 'Normal', 172.00, 'Normal', 72, 'Good', 14.0, 'Athletic', 4.0, 'Above Avg', 23.7, 'Good fitness level for batsman.', 4),
(20, '2026-02-08', 69.50, 'Normal', 172.00, 'Normal', 69, 'Excellent', 13.5, 'Athletic', 4.1, 'Above Avg', 23.5, 'Improved conditioning. Keep it up.', 4);

-- ----------------------------------------------------------------
-- 2. PLAYER VACCINATION SAMPLE DATA
-- ----------------------------------------------------------------
INSERT INTO `playervaccination` (`PlayerID`, `VaccineName`, `DoseNumber`, `Status`, `DateAdministered`, `NextDueDate`, `AdministeredBy`, `BatchNumber`, `Notes`) VALUES
-- Player 6 (Esandu)
(6, 'Hepatitis B', 1, 'Completed', '2024-03-15', NULL, 'Dr. Perera - Colombo General Hospital', 'HB-2024-0315', '1st dose completed as part of academy enrollment'),
(6, 'Hepatitis B', 2, 'Completed', '2024-04-15', NULL, 'Dr. Perera - Colombo General Hospital', 'HB-2024-0415', '2nd dose completed'),
(6, 'Hepatitis B', 3, 'Completed', '2024-09-15', NULL, 'Dr. Perera - Colombo General Hospital', 'HB-2024-0915', '3rd dose completed. Fully vaccinated.'),
(6, 'Tetanus (Td)', 1, 'Completed', '2024-06-20', '2034-06-20', 'Dr. Silva - Lanka Hospitals', 'TD-2024-0620', 'Booster valid for 10 years'),
(6, 'COVID-19 (Pfizer)', 3, 'Completed', '2024-01-10', NULL, 'MOH Colombo', 'PF-2024-0110', 'Booster dose administered'),
(6, 'Influenza', 1, 'Due', '2025-03-01', '2026-03-01', 'Dr. Perera - Colombo General Hospital', 'FLU-2025-0301', 'Annual flu vaccine due'),

-- Player 7 (Vijini)
(7, 'Hepatitis B', 3, 'Completed', '2024-10-01', NULL, 'Dr. Fernando - Nawaloka Hospital', 'HB-2024-1001', 'All 3 doses completed'),
(7, 'Tetanus (Td)', 1, 'Completed', '2023-08-15', '2033-08-15', 'Dr. Fernando - Nawaloka Hospital', 'TD-2023-0815', 'Valid until 2033'),
(7, 'COVID-19 (Sinopharm)', 2, 'Completed', '2023-05-20', NULL, 'MOH Kandy', 'SP-2023-0520', 'Fully vaccinated'),
(7, 'Influenza', 1, 'Completed', '2025-11-15', '2026-11-15', 'Dr. Fernando - Nawaloka Hospital', 'FLU-2025-1115', 'Annual flu vaccine done'),

-- Player 15 (Swairi)
(15, 'Hepatitis B', 2, 'Completed', '2024-07-10', '2025-01-10', 'Dr. Rajapaksa - Asiri Hospital', 'HB-2024-0710', '2nd dose done'),
(15, 'Hepatitis B', 3, 'Overdue', NULL, '2025-01-10', NULL, NULL, '3rd dose overdue. Schedule immediately.'),
(15, 'Tetanus (Td)', 1, 'Completed', '2024-02-28', '2034-02-28', 'Dr. Rajapaksa - Asiri Hospital', 'TD-2024-0228', 'Booster administered'),
(15, 'COVID-19 (AstraZeneca)', 2, 'Completed', '2023-09-10', NULL, 'MOH Galle', 'AZ-2023-0910', 'Fully vaccinated'),

-- Player 16 (esandu)  
(16, 'Hepatitis B', 3, 'Completed', '2024-11-20', NULL, 'Dr. Wickramasinghe - Durdans Hospital', 'HB-2024-1120', 'All doses completed'),
(16, 'Tetanus (Td)', 1, 'Completed', '2024-05-10', '2034-05-10', 'Dr. Wickramasinghe - Durdans Hospital', 'TD-2024-0510', 'Valid for 10 years'),
(16, 'Influenza', 1, 'Scheduled', NULL, '2026-03-15', NULL, NULL, 'Scheduled for next month'),

-- Player 18 (vijini liyanamana)
(18, 'Hepatitis B', 1, 'Completed', '2025-06-01', '2025-07-01', 'Dr. de Silva - National Hospital', 'HB-2025-0601', '1st dose done'),
(18, 'Hepatitis B', 2, 'Due', NULL, '2025-07-01', NULL, NULL, '2nd dose due'),
(18, 'Tetanus (Td)', 1, 'Completed', '2025-01-15', '2035-01-15', 'Dr. de Silva - National Hospital', 'TD-2025-0115', 'Booster done'),

-- Player 20 (esandu yapa)
(20, 'Hepatitis B', 3, 'Completed', '2024-08-30', NULL, 'Dr. Jayawardena - Lanka Hospitals', 'HB-2024-0830', 'Fully vaccinated'),
(20, 'Tetanus (Td)', 1, 'Completed', '2024-04-01', '2034-04-01', 'Dr. Jayawardena - Lanka Hospitals', 'TD-2024-0401', 'Valid until 2034'),
(20, 'COVID-19 (Pfizer)', 3, 'Completed', '2024-02-15', NULL, 'MOH Matara', 'PF-2024-0215', 'Booster completed'),
(20, 'Influenza', 1, 'Completed', '2025-10-20', '2026-10-20', 'Dr. Jayawardena - Lanka Hospitals', 'FLU-2025-1020', 'Annual flu vaccine done');

-- ----------------------------------------------------------------
-- 3. PAYMENT METHOD SAMPLE DATA
-- Sri Lankan banks and payment methods
-- ----------------------------------------------------------------
INSERT INTO `paymentmethod` (`UserID`, `MethodType`, `DisplayName`, `CardLastFour`, `ExpiryDate`, `BankName`, `AccountLastFour`, `IsDefault`, `Icon`, `Color`, `Status`) VALUES
-- Player 6 (Esandu)
(6, 'visa', 'Visa ending 4242', '4242', '12/27', 'Commercial Bank of Ceylon', NULL, 1, 'credit-card', '#1A1F71', 'active'),
(6, 'bank', 'BOC Savings Account', NULL, NULL, 'Bank of Ceylon', '8891', 0, 'university', '#003366', 'active'),
(6, 'lankaqr', 'LankaQR Payment', NULL, NULL, NULL, NULL, 0, 'qrcode', '#E84C3D', 'active'),

-- Player 7 (Vijini)
(7, 'mastercard', 'Mastercard ending 5678', '5678', '08/27', 'Hatton National Bank', NULL, 1, 'credit-card', '#EB001B', 'active'),
(7, 'bank', 'HNB Current Account', NULL, NULL, 'Hatton National Bank', '3345', 0, 'university', '#00539F', 'active'),

-- Player 15 (Swairi)
(15, 'visa', 'Visa ending 9012', '9012', '03/28', 'Sampath Bank', NULL, 1, 'credit-card', '#1A1F71', 'active'),
(15, 'dialog_pay', 'Dialog Pay', NULL, NULL, NULL, NULL, 0, 'mobile-alt', '#E4002B', 'active'),

-- Player 16 (esandu)
(16, 'visa', 'Visa ending 3456', '3456', '06/27', 'Peoples Bank', NULL, 1, 'credit-card', '#1A1F71', 'active'),
(16, 'mastercard', 'Mastercard ending 7890', '7890', '11/26', 'Nations Trust Bank', NULL, 0, 'credit-card', '#EB001B', 'active'),
(16, 'bank', 'NTB Savings Account', NULL, NULL, 'Nations Trust Bank', '5567', 0, 'university', '#005A9C', 'active'),

-- Player 18 (vijini liyanamana)
(18, 'visa', 'Visa ending 2341', '2341', '09/27', 'Commercial Bank of Ceylon', NULL, 1, 'credit-card', '#1A1F71', 'active'),

-- Player 20 (esandu yapa)
(20, 'mastercard', 'Mastercard ending 6789', '6789', '01/28', 'DFCC Bank', NULL, 1, 'credit-card', '#EB001B', 'active'),
(20, 'frimi', 'FriMi Wallet', NULL, NULL, NULL, NULL, 0, 'wallet', '#6C3EC1', 'active'),

-- Coach 3 (Coach Sarath) - coaches may also have payment methods
(3, 'visa', 'Visa ending 1122', '1122', '05/28', 'Seylan Bank', NULL, 1, 'credit-card', '#1A1F71', 'active'),

-- Admin 1
(1, 'bank', 'Academy Main Account', NULL, NULL, 'Bank of Ceylon', '0001', 1, 'university', '#003366', 'active');

-- ----------------------------------------------------------------
-- 4. CONVERSATION & MESSAGE SAMPLE DATA
-- ----------------------------------------------------------------

-- Conversations
INSERT INTO `conversation` (`ConversationID`, `Title`, `Type`, `CreatedBy`, `LastMessageAt`) VALUES
(1, NULL, 'direct', 3, '2026-02-17 09:15:00'),       -- Coach Sarath <-> Player Esandu
(2, NULL, 'direct', 3, '2026-02-16 16:30:00'),       -- Coach Sarath <-> Player Vijini
(3, NULL, 'direct', 11, '2026-02-17 08:45:00'),      -- Coach Dharshana <-> Player Swairi
(4, NULL, 'direct', 3, '2026-02-15 14:20:00'),       -- Coach Sarath <-> Trainer Hasitha
(5, 'U19 Team Announcements', 'group', 3, '2026-02-17 07:00:00'),  -- Group: U19 Team
(6, NULL, 'direct', 6, '2026-02-16 20:00:00'),       -- Player Esandu <-> Trainer Hasitha
(7, NULL, 'direct', 12, '2026-02-15 11:30:00'),      -- Coach Kumara <-> Player esandu(16)
(8, 'Academy Updates', 'announcement', 1, '2026-02-14 10:00:00');  -- Admin announcement

-- Conversation Participants
INSERT INTO `conversationparticipant` (`ConversationID`, `UserID`, `LastReadAt`, `IsOnline`) VALUES
-- Conv 1: Coach Sarath (3) <-> Player Esandu (6)
(1, 3, '2026-02-17 09:15:00', 1),
(1, 6, '2026-02-17 09:10:00', 1),
-- Conv 2: Coach Sarath (3) <-> Player Vijini (7)
(2, 3, '2026-02-16 16:30:00', 1),
(2, 7, '2026-02-16 16:25:00', 0),
-- Conv 3: Coach Dharshana (11) <-> Player Swairi (15)
(3, 11, '2026-02-17 08:45:00', 1),
(3, 15, '2026-02-17 08:40:00', 0),
-- Conv 4: Coach Sarath (3) <-> Trainer Hasitha (4)
(4, 3, '2026-02-15 14:20:00', 1),
(4, 4, '2026-02-15 14:15:00', 0),
-- Conv 5: U19 Team Group - Coach Sarath + players 6,7,15,16
(5, 3, '2026-02-17 07:00:00', 1),
(5, 6, '2026-02-17 06:55:00', 1),
(5, 7, NULL, 0),
(5, 15, '2026-02-17 06:50:00', 0),
(5, 16, NULL, 0),
-- Conv 6: Player Esandu (6) <-> Trainer Hasitha (4)
(6, 6, '2026-02-16 20:00:00', 1),
(6, 4, '2026-02-16 19:55:00', 0),
-- Conv 7: Coach Kumara (12) <-> Player esandu (16)
(7, 12, '2026-02-15 11:30:00', 0),
(7, 16, '2026-02-15 11:25:00', 0),
-- Conv 8: Academy Updates (Admin announcement) - all users see it
(8, 1, '2026-02-14 10:00:00', 0),
(8, 3, '2026-02-14 10:05:00', 1),
(8, 6, NULL, 1),
(8, 7, NULL, 0);

-- Messages
INSERT INTO `message` (`ConversationID`, `SenderID`, `Content`, `MessageType`, `CreatedAt`) VALUES
-- Conv 1: Coach Sarath <-> Player Esandu
(1, 3, 'Esandu, your batting form has improved a lot in the last few sessions. Keep working on the cover drive.', 'text', '2026-02-16 15:00:00'),
(1, 6, 'Thank you coach! I have been practicing the drills you showed me. Should I focus more on front foot or back foot?', 'text', '2026-02-16 15:05:00'),
(1, 3, 'Focus on front foot for now. We will work on back foot play next week during the evening session.', 'text', '2026-02-16 15:10:00'),
(1, 6, 'Understood coach. I will be there for the session on Monday.', 'text', '2026-02-16 15:15:00'),
(1, 3, 'Good. Also remember to bring your new bat for the net session tomorrow at 3pm.', 'text', '2026-02-17 09:00:00'),
(1, 6, 'Will do! See you tomorrow coach.', 'text', '2026-02-17 09:15:00'),

-- Conv 2: Coach Sarath <-> Player Vijini
(2, 7, 'Coach, I am having trouble with my off-spin. The ball is not turning enough.', 'text', '2026-02-16 14:00:00'),
(2, 3, 'Vijini, try to use more wrist action. I noticed your grip is too tight. Relax your fingers and let the ball roll off.', 'text', '2026-02-16 14:10:00'),
(2, 7, 'I will try that. Can we schedule an extra practice session this week?', 'text', '2026-02-16 16:00:00'),
(2, 3, 'Sure. How about Wednesday 4-5pm? I have a free slot then.', 'text', '2026-02-16 16:20:00'),
(2, 7, 'That works perfectly. Thank you coach!', 'text', '2026-02-16 16:30:00'),

-- Conv 3: Coach Dharshana <-> Player Swairi
(3, 15, 'Coach Dharshana, my knee has been hurting after yesterday batting practice. Should I rest?', 'text', '2026-02-17 08:00:00'),
(3, 11, 'Swairi, please rest today and apply ice. If it continues tomorrow, visit Dr. Perera at the academy clinic.', 'text', '2026-02-17 08:15:00'),
(3, 15, 'Okay coach. Should I still attend the theory session?', 'text', '2026-02-17 08:30:00'),
(3, 11, 'Yes, you can attend theory. Just skip the physical training for today.', 'text', '2026-02-17 08:45:00'),

-- Conv 4: Coach Sarath <-> Trainer Hasitha
(4, 3, 'Hasitha, can you prepare a new strength program for Esandu? He needs to work on upper body for fast bowling.', 'text', '2026-02-15 13:00:00'),
(4, 4, 'Sure Coach. I will design a 4-week progressive program. Shall I include plyometrics as well?', 'text', '2026-02-15 13:30:00'),
(4, 3, 'Yes, plyometrics would be great. Focus on shoulder stability and core strength.', 'text', '2026-02-15 14:00:00'),
(4, 4, 'Got it. I will have the plan ready by Monday. Will send it for your review.', 'text', '2026-02-15 14:20:00'),

-- Conv 5: U19 Team Group
(5, 3, 'Attention all U19 players: Inter-academy tournament practice starts next Monday. Report at 6am sharp at the main ground.', 'text', '2026-02-16 18:00:00'),
(5, 6, 'Will be there coach!', 'text', '2026-02-16 18:10:00'),
(5, 15, 'I will be there too. Should we bring our own kits?', 'text', '2026-02-16 18:15:00'),
(5, 3, 'Yes, bring your full kit including whites. We will do a practice match format.', 'text', '2026-02-17 07:00:00'),

-- Conv 6: Player Esandu <-> Trainer Hasitha
(6, 6, 'Trainer, I am feeling some tightness in my hamstring after today workout.', 'text', '2026-02-16 19:00:00'),
(6, 4, 'Do some gentle stretching and foam rolling tonight. We will adjust your program tomorrow. Make sure to hydrate well.', 'text', '2026-02-16 19:30:00'),
(6, 6, 'Thank you! Will do the stretches you taught me last week.', 'text', '2026-02-16 20:00:00'),

-- Conv 7: Coach Kumara <-> Player esandu(16)
(7, 12, 'Your match analysis report is ready. Come see me during lunch break tomorrow.', 'text', '2026-02-15 11:00:00'),
(7, 16, 'Thank you coach. I will be there at 12:30.', 'text', '2026-02-15 11:30:00'),

-- Conv 8: Academy Announcement
(8, 1, 'Important: Academy fee structure has been updated for 2026. Please check the notice board or contact the office for details. Early bird discount available for renewals before March 31st.', 'announcement', '2026-02-14 10:00:00');

-- ----------------------------------------------------------------
-- 5. LEAVE REQUEST SAMPLE DATA
-- ----------------------------------------------------------------
INSERT INTO `leaverequest` (`PlayerID`, `CoachID`, `Title`, `LeaveType`, `StartDate`, `EndDate`, `TotalDays`, `Reason`, `MedicalCertificate`, `Status`, `IsUrgent`, `ReviewedBy`, `ReviewedAt`, `ReviewNotes`) VALUES
-- Approved medical leave
(6, 9, 'Medical Leave - Dengue Recovery', 'medical', '2026-01-05', '2026-01-12', 7, 'Diagnosed with dengue fever. Doctor recommended 7 days rest. Will provide medical certificate.', 'uploads/medical_receipts/dengue_cert_esandu.pdf', 'approved', 1, 3, '2026-01-05 10:00:00', 'Get well soon. Rest fully and return only when cleared by doctor.'),

-- Approved personal leave
(7, 9, 'Family Wedding in Kandy', 'family', '2026-01-20', '2026-01-22', 3, 'Sister wedding ceremony in Kandy. Need to travel and attend the ceremony.', NULL, 'approved', 0, 3, '2026-01-18 14:00:00', 'Approved. Enjoy the wedding. Make up practice sessions next week.'),

-- Pending academic leave
(15, 11, 'O/L Examination Preparation', 'academic', '2026-03-01', '2026-03-14', 14, 'Need to prepare for upcoming O/L examinations. Important for academic future.', NULL, 'pending', 0, NULL, NULL, NULL),

-- Rejected leave (too close to tournament)
(16, 12, 'Personal Trip to Jaffna', 'personal', '2026-02-25', '2026-02-28', 4, 'Family visiting relatives in Jaffna during school break.', NULL, 'rejected', 0, 12, '2026-02-18 09:00:00', 'Cannot approve during inter-academy tournament period. Please reschedule to after March 5th.'),

-- Approved tournament leave (playing for school)
(6, 9, 'School Cricket Tournament - Royal-Thomian', 'tournament', '2026-02-20', '2026-02-22', 3, 'Selected to play for school team in annual Big Match. Will return to academy training after.', NULL, 'approved', 0, 3, '2026-02-15 16:00:00', 'Congratulations on selection! Best of luck. Report back on Feb 23rd.'),

-- Pending medical leave
(18, 11, 'Ankle Sprain Recovery', 'medical', '2026-02-16', '2026-02-20', 5, 'Twisted ankle during practice. Physiotherapist recommended 5 days off from physical activity.', 'uploads/medical_receipts/ankle_report_vijini.pdf', 'pending', 1, NULL, NULL, NULL),

-- Cancelled leave
(20, 12, 'Personal Leave', 'personal', '2026-02-10', '2026-02-11', 2, 'Personal matters to attend to.', NULL, 'cancelled', 0, NULL, NULL, NULL),

-- Approved leave - completed
(7, 9, 'Vesak Holiday Family Trip', 'family', '2025-05-12', '2025-05-14', 3, 'Family trip to Anuradhapura for Vesak celebrations.', NULL, 'approved', 0, 3, '2025-05-10 11:00:00', 'Approved. Have a blessed Vesak.');
