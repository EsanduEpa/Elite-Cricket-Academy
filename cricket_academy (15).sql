-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 05:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cricket_academy`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AutoAssignCoach` (IN `p_player_id` INT, OUT `p_assigned_coach_id` INT, OUT `p_assignment_result` VARCHAR(255))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidateCoachPlayerPermission` (IN `p_coach_id` INT, IN `p_player_id` INT, OUT `p_has_permission` BOOLEAN, OUT `p_permission_type` VARCHAR(50))   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `AchievementID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'Foreign key to identify which player earned it',
  `Date` date NOT NULL,
  `MatchName` varchar(255) NOT NULL,
  `Tournament` varchar(255) NOT NULL,
  `Achievement` text NOT NULL,
  `VerifiedStatus` enum('pending','verified','rejected') DEFAULT 'pending',
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`AchievementID`, `PlayerID`, `Date`, `MatchName`, `Tournament`, `Achievement`, `VerifiedStatus`, `CreatedAt`, `UpdatedAt`) VALUES
(3, 15, '2025-10-22', 'team snakes vs home team', 'elite league', 'man of the match', 'verified', '2025-10-22 21:09:22', '2025-10-23 10:22:01'),
(4, 18, '2025-10-22', 'team snakes', 'elite league', 'man of the match', 'pending', '2025-10-22 21:22:01', '2025-10-22 21:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `activitylog`
--

CREATE TABLE `activitylog` (
  `ActivityID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Action` varchar(100) NOT NULL COMMENT 'Action type: account_created, login, coach_assignment, etc.',
  `Description` text DEFAULT NULL COMMENT 'Detailed description of the activity',
  `IPAddress` varchar(45) DEFAULT NULL COMMENT 'User IP address for security tracking',
  `UserAgent` text DEFAULT NULL COMMENT 'Browser/device information',
  `Timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comprehensive activity logging and audit trail system';

--
-- Dumping data for table `activitylog`
--

INSERT INTO `activitylog` (`ActivityID`, `UserID`, `Action`, `Description`, `IPAddress`, `UserAgent`, `Timestamp`) VALUES
(1, 1, 'account_created', 'New Admin account created', NULL, NULL, '2025-10-18 13:02:42'),
(2, 2, 'account_created', 'New Admin account created', NULL, NULL, '2025-10-18 13:10:39'),
(3, 3, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-18 13:10:39'),
(4, 4, 'account_created', 'New Trainer account created', NULL, NULL, '2025-10-18 13:10:39'),
(5, 5, 'account_created', 'New ShopEmployee account created', NULL, NULL, '2025-10-18 13:10:39'),
(6, 6, 'account_created', 'New Player account created', NULL, NULL, '2025-10-18 13:10:39'),
(7, 7, 'account_created', 'New Player account created', NULL, NULL, '2025-10-18 16:08:42'),
(8, 8, 'account_created', 'New Admin account created', NULL, NULL, '2025-10-18 16:10:01'),
(9, 9, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-18 16:12:47'),
(10, 10, 'account_created', 'New Trainer account created', NULL, NULL, '2025-10-18 16:13:52'),
(11, 11, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-18 23:38:52'),
(12, 12, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-18 23:44:38'),
(13, 13, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-18 23:44:38'),
(14, 14, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-18 23:44:38'),
(15, 15, 'account_created', 'New Player account created', NULL, NULL, '2025-10-19 21:30:13'),
(16, 16, 'account_created', 'New Player account created', NULL, NULL, '2025-10-22 13:22:54'),
(17, 16, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 13:22:54'),
(19, 18, 'account_created', 'New Player account created', NULL, NULL, '2025-10-22 21:19:54'),
(20, 18, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 21:19:54'),
(21, 19, 'account_created', 'New Trainer account created', NULL, NULL, '2025-10-22 21:30:39'),
(22, 20, 'account_created', 'New Player account created', NULL, NULL, '2025-10-23 10:08:20'),
(23, 20, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-23 10:08:20'),
(24, 21, 'account_created', 'New Player account created', NULL, NULL, '2026-01-17 09:19:41'),
(25, 21, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 09:19:41'),
(26, 1, 'Event Created', 'Created new event: Mu2 (Match)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 10:08:22'),
(27, 1, 'Event Updated', 'Updated event: Mu2456 (Match)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 10:15:57'),
(28, 22, 'account_created', 'New Player account created', NULL, NULL, '2026-02-03 14:11:50'),
(29, 22, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-03 14:11:50'),
(31, 6, 'Staff Created', 'Added new staff member: shalitha (Trainer)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:44:43'),
(33, 6, 'Staff Created', 'Added new staff member: hjxbhzvculo (Trainer)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:48:55'),
(34, 25, 'account_created', 'New Player account created', NULL, NULL, '2026-02-17 13:57:36'),
(35, 6, 'Player Created', 'Added new player: shalitha', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:57:36'),
(36, 3, 'Event Updated', 'Updated event: Mu2456 (Match)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 14:35:40'),
(37, 15, 'stats_updated_by_coach', 'Overall stats updated by coach ID: 0 based on performance ID: 22', NULL, NULL, '2026-03-29 01:04:09'),
(38, 15, 'stats_updated_by_coach', 'Overall stats updated by coach ID: 0 based on performance ID: 23', NULL, NULL, '2026-03-29 03:02:04'),
(39, 25, 'stats_updated_by_coach', 'Overall stats updated by coach ID: 0 based on performance ID: 4', NULL, NULL, '2026-03-29 09:08:27'),
(40, 8, 'generate_occurrences', 'Generated 61 occurrence(s) for template #1 (u13 balling ) from 2026-04-09 to 2027-06-09', '::1', NULL, '2026-04-09 12:02:45'),
(41, 1, 'generate_occurrences', 'Generated 44 occurrence(s) for template #3 (drfty) from 2027-12-09 to 2028-10-09', '::1', NULL, '2026-04-09 13:05:15'),
(42, 3, 'create_private_session', 'Staff created private session #184 on 2026-04-10', '::1', NULL, '2026-04-09 15:05:49'),
(43, 6, 'slot_booking', 'Booked occurrence #184 for player #6 (source: self)', '::1', NULL, '2026-04-09 15:15:28'),
(44, 15, 'slot_booking', 'Booked occurrence #186 for player #15 (source: self)', '::1', NULL, '2026-04-09 22:53:07'),
(45, 15, 'slot_booking', 'Booked occurrence #199 for player #15 (source: self)', '::1', NULL, '2026-04-09 22:57:52'),
(46, 15, 'slot_booking', 'Booked occurrence #193 for player #15 (source: self)', '::1', NULL, '2026-04-10 07:05:53'),
(47, 26, 'account_created', 'New Player account created', NULL, NULL, '2026-04-10 08:06:39'),
(48, 26, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 08:06:39'),
(49, 27, 'account_created', 'New Coach account created', NULL, NULL, '2026-04-10 19:32:16'),
(50, 8, 'Staff Created', 'Added new staff member: venuk Wickrama (Coach)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 19:32:16'),
(51, 3, 'update_booking_status', 'Updated booking #1 status to missed', '::1', NULL, '2026-04-11 23:46:42'),
(52, 3, 'update_booking_status', 'Updated booking #1 status to missed', '::1', NULL, '2026-04-12 09:52:15'),
(53, 3, 'update_booking_status', 'Updated booking #1 status to missed', '::1', NULL, '2026-04-12 09:55:11'),
(54, 4, 'update_facility_booking_status', 'Updated facility booking #6 status to attended', '::1', NULL, '2026-04-12 10:10:49'),
(55, 3, 'update_booking_status', 'Updated booking #1 status to missed', '::1', NULL, '2026-04-12 10:50:42'),
(56, 28, 'account_created', 'New Player account created', NULL, NULL, '2026-04-12 15:25:28'),
(57, 28, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-12 15:25:28'),
(58, 29, 'account_created', 'New Player account created', NULL, NULL, '2026-04-12 15:49:26'),
(59, 29, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-12 15:49:26'),
(60, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #1', '::1', NULL, '2026-04-13 01:00:44'),
(61, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #2', '::1', NULL, '2026-04-13 01:00:44'),
(62, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #3', '::1', NULL, '2026-04-13 01:00:44'),
(63, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #4', '::1', NULL, '2026-04-13 01:00:44'),
(64, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #5', '::1', NULL, '2026-04-13 01:00:44'),
(65, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #6', '::1', NULL, '2026-04-13 01:00:44'),
(66, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #7', '::1', NULL, '2026-04-13 01:00:44'),
(67, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #8', '::1', NULL, '2026-04-13 01:00:44'),
(68, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #9', '::1', NULL, '2026-04-13 01:00:44'),
(69, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #10', '::1', NULL, '2026-04-13 01:00:44'),
(70, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #11', '::1', NULL, '2026-04-13 01:00:44'),
(71, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #12', '::1', NULL, '2026-04-13 01:00:44'),
(72, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #13', '::1', NULL, '2026-04-13 01:00:44'),
(73, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #14', '::1', NULL, '2026-04-13 01:00:44'),
(74, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #15', '::1', NULL, '2026-04-13 01:00:44'),
(75, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #16', '::1', NULL, '2026-04-13 01:00:44'),
(76, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #17', '::1', NULL, '2026-04-13 01:00:44'),
(77, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #18', '::1', NULL, '2026-04-13 01:00:44'),
(78, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #19', '::1', NULL, '2026-04-13 01:00:44'),
(79, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #20', '::1', NULL, '2026-04-13 01:00:44'),
(80, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #21', '::1', NULL, '2026-04-13 01:00:44'),
(81, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #22', '::1', NULL, '2026-04-13 01:00:44'),
(82, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #23', '::1', NULL, '2026-04-13 01:00:44'),
(83, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #24', '::1', NULL, '2026-04-13 01:00:44'),
(84, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #25', '::1', NULL, '2026-04-13 01:00:44'),
(85, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #26', '::1', NULL, '2026-04-13 01:00:44'),
(86, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #27', '::1', NULL, '2026-04-13 01:00:44'),
(87, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #28', '::1', NULL, '2026-04-13 01:00:44'),
(88, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #29', '::1', NULL, '2026-04-13 01:00:44'),
(89, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #30', '::1', NULL, '2026-04-13 01:00:44'),
(90, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #31', '::1', NULL, '2026-04-13 01:00:44'),
(91, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #32', '::1', NULL, '2026-04-13 01:00:44'),
(92, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #33', '::1', NULL, '2026-04-13 01:00:44'),
(93, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #34', '::1', NULL, '2026-04-13 01:00:44'),
(94, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #35', '::1', NULL, '2026-04-13 01:00:44'),
(95, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #36', '::1', NULL, '2026-04-13 01:00:44'),
(96, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #37', '::1', NULL, '2026-04-13 01:00:44'),
(97, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #38', '::1', NULL, '2026-04-13 01:00:44'),
(98, 1, 'generate_occurrences', 'Generated 38 occurrence(s) for template #1 (U15 batting) from 2026-04-13 to 2026-12-28', '::1', NULL, '2026-04-13 01:00:44'),
(99, 1, 'generate_occurrences', 'Generated 38 occurrence(s) for template #4 (Practice net 2 facility ) from 2026-04-15 to 2026-12-30', '::1', NULL, '2026-04-13 09:07:27'),
(100, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #77', '::1', NULL, '2026-04-13 09:22:14'),
(101, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #78', '::1', NULL, '2026-04-13 09:22:14'),
(102, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #79', '::1', NULL, '2026-04-13 09:22:15'),
(103, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #80', '::1', NULL, '2026-04-13 09:22:15'),
(104, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #81', '::1', NULL, '2026-04-13 09:22:15'),
(105, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #82', '::1', NULL, '2026-04-13 09:22:15'),
(106, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #83', '::1', NULL, '2026-04-13 09:22:15'),
(107, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #84', '::1', NULL, '2026-04-13 09:22:15'),
(108, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #85', '::1', NULL, '2026-04-13 09:22:15'),
(109, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #86', '::1', NULL, '2026-04-13 09:22:15'),
(110, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #87', '::1', NULL, '2026-04-13 09:22:15'),
(111, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #88', '::1', NULL, '2026-04-13 09:22:15'),
(112, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #89', '::1', NULL, '2026-04-13 09:22:15'),
(113, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #90', '::1', NULL, '2026-04-13 09:22:15'),
(114, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #91', '::1', NULL, '2026-04-13 09:22:15'),
(115, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #92', '::1', NULL, '2026-04-13 09:22:15'),
(116, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #93', '::1', NULL, '2026-04-13 09:22:15'),
(117, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #94', '::1', NULL, '2026-04-13 09:22:15'),
(118, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #95', '::1', NULL, '2026-04-13 09:22:15'),
(119, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #96', '::1', NULL, '2026-04-13 09:22:15'),
(120, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #97', '::1', NULL, '2026-04-13 09:22:15'),
(121, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #98', '::1', NULL, '2026-04-13 09:22:15'),
(122, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #99', '::1', NULL, '2026-04-13 09:22:15'),
(123, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #100', '::1', NULL, '2026-04-13 09:22:15'),
(124, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #101', '::1', NULL, '2026-04-13 09:22:15'),
(125, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #102', '::1', NULL, '2026-04-13 09:22:15'),
(126, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #103', '::1', NULL, '2026-04-13 09:22:15'),
(127, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #104', '::1', NULL, '2026-04-13 09:22:15'),
(128, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #105', '::1', NULL, '2026-04-13 09:22:15'),
(129, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #106', '::1', NULL, '2026-04-13 09:22:15'),
(130, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #107', '::1', NULL, '2026-04-13 09:22:15'),
(131, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #108', '::1', NULL, '2026-04-13 09:22:15'),
(132, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #109', '::1', NULL, '2026-04-13 09:22:15'),
(133, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #110', '::1', NULL, '2026-04-13 09:22:15'),
(134, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #111', '::1', NULL, '2026-04-13 09:22:15'),
(135, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #112', '::1', NULL, '2026-04-13 09:22:15'),
(136, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #113', '::1', NULL, '2026-04-13 09:22:15'),
(137, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #114', '::1', NULL, '2026-04-13 09:22:15'),
(138, 1, 'generate_occurrences', 'Generated 38 occurrence(s) for template #2 (U15 bowling) from 2026-04-13 to 2026-12-28', '::1', NULL, '2026-04-13 09:22:15'),
(139, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #115', '::1', NULL, '2026-04-13 15:29:55'),
(140, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #116', '::1', NULL, '2026-04-13 15:29:55'),
(141, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #117', '::1', NULL, '2026-04-13 15:29:55'),
(142, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #118', '::1', NULL, '2026-04-13 15:29:55'),
(143, 1, 'generate_occurrences', 'Generated 4 occurrence(s) for template #6 (Test Program Session Flow) from 2025-07-07 to 2025-07-28', '::1', NULL, '2026-04-13 15:29:55'),
(144, 1, 'generate_occurrences', 'Generated 38 occurrence(s) for template #28 (Balling machine -F/O) from 2026-04-16 to 2026-12-31', '::1', NULL, '2026-04-13 22:07:44'),
(145, 1, 'generate_occurrences', 'Generated 37 occurrence(s) for template #31 (Balling machine -F/O) from 2026-04-19 to 2026-12-27', '::1', NULL, '2026-04-13 22:08:35'),
(146, 1, 'generate_occurrences', 'Generated 37 occurrence(s) for template #29 (practice net 2 -F/O) from 2026-04-17 to 2026-12-25', '::1', NULL, '2026-04-13 22:10:53'),
(147, 1, 'generate_occurrences', 'Generated 2 occurrence(s) for template #30 (practice net 1 -F/O) from 2026-04-09 to 2026-04-22', '::1', NULL, '2026-04-13 22:16:54'),
(148, 1, 'generate_occurrences', 'Generated 3 occurrence(s) for template #27 (practice net 1 -F/O) from 2026-04-14 to 2026-04-30', '::1', NULL, '2026-04-13 22:17:51'),
(149, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #1', '::1', NULL, '2026-04-14 01:23:15'),
(150, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #2', '::1', NULL, '2026-04-14 01:23:15'),
(151, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #3', '::1', NULL, '2026-04-14 01:23:15'),
(152, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #4', '::1', NULL, '2026-04-14 01:23:15'),
(153, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #5', '::1', NULL, '2026-04-14 01:23:15'),
(154, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #6', '::1', NULL, '2026-04-14 01:23:15'),
(155, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #7', '::1', NULL, '2026-04-14 01:23:15'),
(156, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #8', '::1', NULL, '2026-04-14 01:23:15'),
(157, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #9', '::1', NULL, '2026-04-14 01:23:15'),
(158, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #10', '::1', NULL, '2026-04-14 01:23:15'),
(159, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #11', '::1', NULL, '2026-04-14 01:23:15'),
(160, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #12', '::1', NULL, '2026-04-14 01:23:15'),
(161, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #13', '::1', NULL, '2026-04-14 01:23:15'),
(162, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #14', '::1', NULL, '2026-04-14 01:23:15'),
(163, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #15', '::1', NULL, '2026-04-14 01:23:15'),
(164, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #16', '::1', NULL, '2026-04-14 01:23:15'),
(165, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #17', '::1', NULL, '2026-04-14 01:23:15'),
(166, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #18', '::1', NULL, '2026-04-14 01:23:15'),
(167, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #19', '::1', NULL, '2026-04-14 01:23:15'),
(168, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #20', '::1', NULL, '2026-04-14 01:23:15'),
(169, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #21', '::1', NULL, '2026-04-14 01:23:15'),
(170, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #22', '::1', NULL, '2026-04-14 01:23:15'),
(171, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #23', '::1', NULL, '2026-04-14 01:23:15'),
(172, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #24', '::1', NULL, '2026-04-14 01:23:15'),
(173, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #25', '::1', NULL, '2026-04-14 01:23:15'),
(174, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #26', '::1', NULL, '2026-04-14 01:23:15'),
(175, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #27', '::1', NULL, '2026-04-14 01:23:15'),
(176, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #28', '::1', NULL, '2026-04-14 01:23:15'),
(177, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #29', '::1', NULL, '2026-04-14 01:23:15'),
(178, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #30', '::1', NULL, '2026-04-14 01:23:15'),
(179, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #31', '::1', NULL, '2026-04-14 01:23:15'),
(180, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #32', '::1', NULL, '2026-04-14 01:23:15'),
(181, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #33', '::1', NULL, '2026-04-14 01:23:15'),
(182, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #34', '::1', NULL, '2026-04-14 01:23:16'),
(183, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #35', '::1', NULL, '2026-04-14 01:23:16'),
(184, 1, 'slot_auto_enroll', 'Auto-enrolled assigned players for occurrence #36', '::1', NULL, '2026-04-14 01:23:16'),
(185, 1, 'generate_occurrences', 'Generated 36 occurrence(s) for template #1 (U15 batting) from 2026-04-20 to 2026-12-27', '::1', NULL, '2026-04-14 01:23:16'),
(186, 1, 'generate_occurrences', 'Generated 38 occurrence(s) for template #18 (Bowling Machine Wed 8-10) from 2026-04-15 to 2026-12-30', '::1', NULL, '2026-04-14 10:30:52'),
(187, 29, 'slot_booking', 'Booked occurrence #37 for player #29 (source: self)', '::1', NULL, '2026-04-14 10:42:26'),
(188, 29, 'slot_booking', 'Booked occurrence #37 for player #29 (source: self)', '::1', NULL, '2026-04-14 11:49:18'),
(189, 30, 'account_created', 'New Coach account created', NULL, NULL, '2026-04-14 13:13:04'),
(190, 31, 'account_created', 'New Coach account created', NULL, NULL, '2026-04-14 13:13:04'),
(191, 32, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:49'),
(192, 33, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:49'),
(193, 34, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(194, 35, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(195, 36, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(196, 37, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(197, 38, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(198, 39, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(199, 40, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(200, 41, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(201, 42, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(202, 43, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(203, 44, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(204, 45, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(205, 46, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(206, 47, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(207, 48, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(208, 49, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(209, 50, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(210, 51, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(211, 52, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(212, 53, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(213, 54, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(214, 55, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(215, 56, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 15:13:50'),
(216, 1, 'generate_occurrences', 'Generated 37 occurrence(s) for template #91 (Coach Ruwan - Sunday 1:1 Bowling Machine) from 2026-04-19 to 2026-12-27', '::1', NULL, '2026-04-14 15:53:06'),
(217, 57, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(218, 58, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(219, 59, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(220, 60, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(221, 61, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(222, 62, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(223, 63, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(224, 64, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(225, 65, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(226, 66, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(227, 67, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(228, 68, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(229, 69, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(230, 70, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(231, 71, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(232, 72, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(233, 73, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(234, 74, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(235, 75, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(236, 76, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(237, 77, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(238, 78, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(239, 79, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(240, 80, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(241, 81, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(242, 82, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(243, 83, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(244, 84, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(245, 85, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(246, 86, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(247, 87, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(248, 88, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(249, 89, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(250, 90, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(251, 91, 'account_created', 'New Player account created', NULL, NULL, '2026-04-14 20:06:22'),
(252, 3, 'create_private_session_request', 'Staff requested private session #1 on 2026-04-16', '::1', NULL, '2026-04-14 21:50:02'),
(253, 3, 'create_private_session_request', 'Staff requested private session #2 on 2026-04-23', '::1', NULL, '2026-04-14 21:50:22'),
(254, 8, 'private_session_request_approved', 'Reviewed private session request #2 as approved and created occurrence #2159', '::1', NULL, '2026-04-14 22:55:26'),
(255, 121, 'account_created', 'New Coach account created', NULL, NULL, '2026-04-15 00:12:15');

-- --------------------------------------------------------

--
-- Table structure for table `adminprofile`
--

CREATE TABLE `adminprofile` (
  `AdminID` int(11) NOT NULL,
  `Section` enum('Academics','Sports','Finance','Operations','General') DEFAULT NULL,
  `HireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coachappointment`
--

CREATE TABLE `coachappointment` (
  `AppointmentID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AppointmentDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Status` enum('scheduled','completed','cancelled','rescheduled') DEFAULT 'scheduled',
  `Reason` text DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='One-on-one appointments between coaches and players';

--
-- Dumping data for table `coachappointment`
--

INSERT INTO `coachappointment` (`AppointmentID`, `CoachID`, `PlayerID`, `AppointmentDate`, `StartTime`, `EndTime`, `Status`, `Reason`, `UpdatedAt`) VALUES
(1, 9, 15, '2026-04-08', '08:00:00', '09:00:00', 'scheduled', 'Batting technique improvement', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `coachingeffectiveness`
-- (See below for the actual view)
--


-- --------------------------------------------------------

--
-- Table structure for table `coachingsession`
--

CREATE TABLE `coachingsession` (
  `SessionLogID` int(11) NOT NULL,
  `SessionID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL,
  `AttendanceStatus` enum('present','absent','late','partial') NOT NULL,
  `PerformanceNotes` text DEFAULT NULL COMMENT 'Coach notes about player performance in this session',
  `SkillsWorkedOn` text DEFAULT NULL COMMENT 'Specific skills practiced',
  `AreasForImprovement` text DEFAULT NULL COMMENT 'Areas identified for improvement',
  `HomeworkAssigned` text DEFAULT NULL COMMENT 'Practice tasks assigned for home',
  `SessionRating` int(11) DEFAULT NULL COMMENT 'Overall session rating 1-10',
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detailed coaching session logs and player performance tracking per session';

--
-- Triggers `coachingsession`
--
DELIMITER $$
CREATE TRIGGER `tr_coaching_session_logging` AFTER INSERT ON `coachingsession` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `coachplayerpermissions`
-- (See below for the actual view)
--


-- --------------------------------------------------------

--
-- Table structure for table `coachprofile`
--

CREATE TABLE `coachprofile` (
  `CoachID` int(11) NOT NULL,
  `Specialization` enum('Batting','Bowling','Fielding') DEFAULT NULL,
  `Experience` int(11) DEFAULT NULL COMMENT 'Years of experience',
  `Certifications` text DEFAULT NULL,
  `IsHeadCoach` tinyint(1) DEFAULT 0 COMMENT 'Head coach flag - only one per academy with enhanced permissions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Coach-specific profile with head coach promotion capabilities';

--
-- Dumping data for table `coachprofile`
--

INSERT INTO `coachprofile` (`CoachID`, `Specialization`, `Experience`, `Certifications`, `IsHeadCoach`) VALUES
(3, 'Fielding', 0, NULL, 0),
(9, 'Batting', 5, NULL, 0),
(11, 'Bowling', 10, NULL, 0),
(12, 'Bowling', 7, NULL, 0),
(27, 'Batting', 2, NULL, 0),
(30, 'Batting', 8, 'Level 2 Cricket Coaching', 1),
(31, 'Bowling', 6, 'Fast Bowling Specialist', 0),
(121, 'Fielding', 7, 'Fielding Specialist Level 2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `coachreview`
--

CREATE TABLE `coachreview` (
  `ReviewID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AppointmentID` int(11) DEFAULT NULL COMMENT 'Related appointment if applicable',
  `TeachingQuality` int(11) NOT NULL COMMENT 'Rating 1-5 for teaching quality',
  `Communication` int(11) NOT NULL COMMENT 'Rating 1-5 for communication skills',
  `Punctuality` int(11) NOT NULL COMMENT 'Rating 1-5 for punctuality',
  `Knowledge` int(11) NOT NULL COMMENT 'Rating 1-5 for cricket knowledge',
  `OverallRating` decimal(3,1) DEFAULT NULL COMMENT 'Calculated average of all ratings',
  `ReviewTitle` varchar(255) DEFAULT NULL,
  `ReviewText` text DEFAULT NULL,
  `WouldRecommend` tinyint(1) DEFAULT 1,
  `ReviewDate` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected','flagged') DEFAULT 'approved',
  `CoachResponse` text DEFAULT NULL COMMENT 'Coach response to the review',
  `ResponseDate` datetime DEFAULT NULL COMMENT 'When coach responded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coach_skill_age_group_assignment`
--

CREATE TABLE `coach_skill_age_group_assignment` (
  `CoachSkillGroupID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL,
  `CoachingType` enum('batting','bowling','fielding') NOT NULL,
  `AgeGroup` varchar(50) NOT NULL,
  `PriorityRank` int(11) NOT NULL DEFAULT 1,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `Notes` text DEFAULT NULL,
  `AssignedBy` int(11) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coach_skill_age_group_assignment`
--

INSERT INTO `coach_skill_age_group_assignment` (`CoachSkillGroupID`, `CoachID`, `CoachingType`, `AgeGroup`, `PriorityRank`, `IsActive`, `Notes`, `AssignedBy`, `CreatedAt`, `UpdatedAt`) VALUES
(26, 11, 'bowling', 'Under 13', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 13:22:47', '2026-04-14 13:22:47'),
(27, 11, 'bowling', 'Under 15', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 13:22:47', '2026-04-14 13:22:47'),
(33, 27, 'batting', 'Under 17', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:36:29', '2026-04-14 14:36:29'),
(34, 27, 'batting', 'Under 19', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:36:29', '2026-04-14 14:36:29'),
(36, 30, 'batting', 'Under 11', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:37:05', '2026-04-14 14:37:05'),
(37, 30, 'batting', 'Open', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:37:05', '2026-04-14 14:37:05'),
(38, 12, 'bowling', 'Under 11', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:37:29', '2026-04-14 14:37:29'),
(39, 12, 'bowling', 'Open', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:37:29', '2026-04-14 14:37:29'),
(40, 9, 'batting', 'Under 13', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:41:41', '2026-04-14 14:41:41'),
(41, 9, 'batting', 'Under 15', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:41:41', '2026-04-14 14:41:41'),
(42, 9, 'batting', 'Under 21', 3, 1, 'Assigned by admin from staff management page', 1, '2026-04-14 14:41:41', '2026-04-14 14:41:41'),
(43, 3, 'fielding', 'Under 15', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:10:25', '2026-04-15 00:10:25'),
(44, 3, 'fielding', 'Under 17', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:10:25', '2026-04-15 00:10:25'),
(45, 3, 'fielding', 'Under 19', 3, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:10:25', '2026-04-15 00:10:25'),
(46, 3, 'fielding', 'Under 21', 4, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:10:25', '2026-04-15 00:10:25'),
(50, 121, 'fielding', 'Under 11', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:12:47', '2026-04-15 00:12:47'),
(51, 121, 'fielding', 'Under 13', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:12:47', '2026-04-15 00:12:47'),
(52, 121, 'fielding', 'Open', 3, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 00:12:47', '2026-04-15 00:12:47'),
(53, 31, 'bowling', 'Under 17', 1, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 01:15:26', '2026-04-15 01:15:26'),
(54, 31, 'bowling', 'Under 19', 2, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 01:15:26', '2026-04-15 01:15:26'),
(55, 31, 'bowling', 'Under 21', 3, 1, 'Assigned by admin from staff management page', 1, '2026-04-15 01:15:26', '2026-04-15 01:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `coach_tournament_recommendations`
--

CREATE TABLE `coach_tournament_recommendations` (
  `RecommendationID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL COMMENT 'FK -> user.UserID',
  `TournamentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'FK -> user.UserID',
  `RecommendedRole` varchar(100) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `Comments` text DEFAULT NULL,
  `Status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ReviewFeedback` text DEFAULT NULL COMMENT 'Feedback from reviewer (if rejected)',
  `ReviewedBy` int(11) DEFAULT NULL COMMENT 'FK -> user.UserID (admin)',
  `DateRecommended` timestamp NOT NULL DEFAULT current_timestamp(),
  `DateReviewed` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Coach recommendations of players for tournament participation';

--
-- Dumping data for table `coach_tournament_recommendations`
--

INSERT INTO `coach_tournament_recommendations` (`RecommendationID`, `CoachID`, `TournamentID`, `PlayerID`, `RecommendedRole`, `Reason`, `Comments`, `Status`, `ReviewFeedback`, `ReviewedBy`, `DateRecommended`, `DateReviewed`) VALUES
(1, 3, 6, 15, 'Batsman', 'nm', '', 'pending', NULL, NULL, '2026-04-10 13:15:07', NULL),
(2, 3, 8, 15, 'Batsman', 'best scores so far', '', 'pending', NULL, NULL, '2026-04-10 16:12:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contactmessage`
--

CREATE TABLE `contactmessage` (
  `ContactID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL COMMENT 'nullable for guest visitors',
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL COMMENT 'Optional contact number',
  `Subject` varchar(255) DEFAULT NULL,
  `Message` text NOT NULL,
  `MessageType` enum('General Inquiry','Admission','Facility Booking','Complaint','Suggestion','Technical Support') DEFAULT 'General Inquiry',
  `SubmittedDate` datetime DEFAULT current_timestamp(),
  `Status` enum('new','read','responded','closed') DEFAULT 'new',
  `RespondedBy` int(11) DEFAULT NULL COMMENT 'Staff member who responded',
  `ResponseMessage` text DEFAULT NULL COMMENT 'Staff response to the inquiry',
  `ResponseDate` datetime DEFAULT NULL COMMENT 'When response was sent',
  `Priority` enum('low','medium','high','urgent') DEFAULT 'medium'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contact form submissions from website visitors and registered users';

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `ContactID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL COMMENT 'Nullable for guest visitors',
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL COMMENT 'Optional contact number',
  `Subject` varchar(255) DEFAULT NULL,
  `Message` text NOT NULL,
  `MessageType` enum('General Inquiry','Admission','Facility Booking','Complaint','Suggestion','Technical Support') DEFAULT 'General Inquiry',
  `SubmittedDate` datetime DEFAULT current_timestamp(),
  `Status` enum('new','read','responded','closed') DEFAULT 'new',
  `RespondedBy` int(11) DEFAULT NULL COMMENT 'Staff member who responded',
  `ResponseMessage` text DEFAULT NULL COMMENT 'Staff response to the inquiry',
  `ResponseDate` datetime DEFAULT NULL COMMENT 'When response was sent',
  `Priority` enum('low','medium','high','urgent') DEFAULT 'medium'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contact form submissions and inquiries - Essential for website functionality';

-- --------------------------------------------------------

--
-- Table structure for table `crimatch`
--

CREATE TABLE `crimatch` (
  `MatchID` int(11) NOT NULL,
  `TournamentID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `Venue` varchar(255) DEFAULT NULL,
  `OpponentTeam` varchar(255) NOT NULL,
  `Result` enum('win','loss','draw','no-result','pending') DEFAULT 'pending',
  `OurScore` varchar(50) DEFAULT NULL,
  `OpponentScore` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual matches within tournaments';

--
-- Dumping data for table `crimatch`
--

INSERT INTO `crimatch` (`MatchID`, `TournamentID`, `name`, `Date`, `Venue`, `OpponentTeam`, `Result`, `OurScore`, `OpponentScore`) VALUES
(1, 7, 'Elite League - Round 1', '2026-01-15', 'Main Cricket Ground', 'Thunder Warriors', 'win', '185/8 (20 overs)', '178/10 (19.4 overs)'),
(2, 7, 'Elite League - Round 2', '2026-01-22', 'City Sports Complex', 'Royal Knights', 'loss', '142/10 (18.2 overs)', '145/6 (20 overs)'),
(3, 7, 'Elite League - Round 3', '2026-01-29', 'Main Cricket Ground', 'Falcon Strikers', 'win', '198/5 (20 overs)', '192/8 (20 overs)'),
(4, 7, 'Elite League - Semi Final', '2026-02-05', 'Stadium Ground', 'Phoenix Blazers', 'win', '167/7 (20 overs)', '160/9 (20 overs)'),
(5, 7, 'Elite League - Final', '2026-02-12', 'Championship Stadium', 'Cobra Kings', 'loss', '155/10 (19.1 overs)', '158/6 (19.5 overs)'),
(6, 8, 'Championship - Group A', '2026-01-18', 'Green Park Ground', 'Eagle Warriors', 'win', '205/6 (20 overs)', '198/9 (20 overs)'),
(7, 8, 'Championship - Group B', '2026-01-25', 'Oval Cricket Ground', 'Lions Pride', 'win', '178/5 (18 overs)', '175/10 (17.4 overs)'),
(8, 8, 'Championship - Quarter Final', '2026-02-01', 'Memorial Ground', 'Dragon Fire', 'win', '189/7 (20 overs)', '182/8 (20 overs)'),
(9, 8, 'Championship - Semi Final', '2026-02-08', 'City Sports Complex', 'Viper Squad', 'loss', '165/10 (19.2 overs)', '168/7 (19.5 overs)'),
(10, 9, 'Local Cup - Round 1', '2026-01-20', 'Community Ground', 'Blue Tigers', 'win', '156/6 (20 overs)', '148/10 (18.3 overs)'),
(11, 9, 'Local Cup - Round 2', '2026-01-27', 'Town Cricket Field', 'Red Panthers', 'win', '172/5 (20 overs)', '168/9 (20 overs)'),
(12, 9, 'Local Cup - Final', '2026-02-03', 'Main Cricket Ground', 'Golden Hawks', 'win', '195/4 (20 overs)', '188/8 (20 overs)'),
(13, 10, 'Elite League - Round 4', '2026-02-19', 'Main Cricket Ground', 'Storm Chasers', 'loss', NULL, NULL),
(14, 10, 'Elite League - Round 5', '2026-02-22', 'Stadium Ground', 'Thunder Warriors', 'win', NULL, NULL),
(15, 10, 'Championship - Round 3', '2026-02-25', 'City Sports Complex', 'Falcon Strikers', 'pending', NULL, NULL),
(16, 10, 'Local Cup - Semi Final', '2026-02-28', 'Memorial Ground', 'Silver Sharks', 'pending', NULL, NULL),
(17, 10, 'Local Cup - Final', '2026-03-05', 'Championship Stadium', 'TBD', 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `emaillog`
--

CREATE TABLE `emaillog` (
  `EmailID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL COMMENT 'Can be null for system emails',
  `RecipientEmail` varchar(255) NOT NULL,
  `Subject` varchar(255) NOT NULL,
  `EmailType` enum('welcome','password_reset','notification','suspension','promotion') NOT NULL,
  `Status` enum('queued','sent','failed','bounced') DEFAULT 'queued',
  `SentAt` datetime DEFAULT NULL,
  `ErrorMessage` text DEFAULT NULL COMMENT 'Error details for failed emails',
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email tracking and delivery status monitoring';

--
-- Dumping data for table `emaillog`
--

INSERT INTO `emaillog` (`EmailID`, `UserID`, `RecipientEmail`, `Subject`, `EmailType`, `Status`, `SentAt`, `ErrorMessage`, `CreatedAt`) VALUES
(1, 1, 'admin@cricketacademy.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 13:02:42'),
(2, 2, 'manager@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 13:10:39'),
(3, 3, 'coach001@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 13:10:39'),
(4, 4, 'trainer001@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 13:10:39'),
(5, 5, 'shop001@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 13:10:39'),
(6, 6, 'esandu@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 13:10:39'),
(7, 7, 'vijini@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 16:08:42'),
(8, 8, 'admin2@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 16:10:01'),
(9, 9, 'coach002@celiteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 16:12:47'),
(10, 10, 'trainer002@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 16:13:52'),
(11, 11, 'coach004@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 23:38:52'),
(12, 12, 'kumara.darmasena@eliteacademy.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 23:44:38'),
(13, 13, 'ben.carter@eliteacademy.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 23:44:38'),
(14, 14, 'kumar.sangakkara@eliteacademy.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-18 23:44:38'),
(15, 15, 'swairi@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-19 21:30:13'),
(16, 16, 'e@g.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 13:22:54'),
(17, NULL, 'v3@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 13:55:27'),
(18, 18, 'jini.a.lm2@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 21:19:54'),
(19, 19, 'coach@cricketacademy.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 21:30:39'),
(20, 20, 'esanduepa0225@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-23 10:08:20'),
(21, 21, 'esanduepa022555@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-01-17 09:19:41'),
(22, 22, 'esanduepa0225777@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-03 14:11:50'),
(23, NULL, 'shalitha@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-17 13:44:43'),
(24, NULL, 'jin@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-17 13:48:55'),
(25, 25, 'jina.lm2@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-17 13:57:36'),
(26, 26, 'vijinialm2417@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-10 08:06:39'),
(27, 27, 'venukw@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-10 19:32:16'),
(28, 28, 'samitha@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-12 15:25:28'),
(29, 29, 'thilan@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-12 15:49:26'),
(30, 30, 'niroshan.coach@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 13:13:04'),
(31, 31, 'ruwan.coach@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 13:13:04'),
(32, 32, 'ayaan.u13_01@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:49'),
(33, 33, 'mihin.u13_02@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:49'),
(34, 34, 'ravin.u13_03@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(35, 35, 'sadev.u13_04@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(36, 36, 'nethuka.u13_05@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(37, 37, 'kavindu.u15_01@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(38, 38, 'dulneth.u15_02@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(39, 39, 'hasitha.u15_03@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(40, 40, 'chamith.u15_04@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(41, 41, 'imeth.u15_05@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(42, 42, 'pasindu.u17_01@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(43, 43, 'nethran.u17_02@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(44, 44, 'ashen.u17_03@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(45, 45, 'tharindu.u17_04@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(46, 46, 'dineth.u17_05@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(47, 47, 'hirun.u19_01@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(48, 48, 'kethmi.u19_02@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(49, 49, 'malith.u19_03@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(50, 50, 'sanjana.u19_04@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(51, 51, 'ruvin.u19_05@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(52, 52, 'sahan.u21_01@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(53, 53, 'pasan.u21_02@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(54, 54, 'nisal.u21_03@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(55, 55, 'dulaj.u21_04@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(56, 56, 'prabath.u21_05@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 15:13:50'),
(57, 57, 'aarav.u13_06@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(58, 58, 'rehan.u13_07@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(59, 59, 'ishan.u13_08@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(60, 60, 'navin.u13_09@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(61, 61, 'senuth.u13_10@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(62, 62, 'yuvan.u15_06@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(63, 63, 'lakshan.u15_07@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(64, 64, 'dasun.u15_08@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(65, 65, 'omith.u15_09@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(66, 66, 'thivin.u15_10@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(67, 67, 'madusha.u17_06@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(68, 68, 'asela.u17_07@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(69, 69, 'ravisha.u17_08@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(70, 70, 'kusal.u17_09@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(71, 71, 'dinura.u17_10@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(72, 72, 'chathura.u19_06@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(73, 73, 'shalitha.u19_07@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(74, 74, 'praveen.u19_08@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(75, 75, 'sachin.u19_09@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(76, 76, 'hiranya.u19_10@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(77, 77, 'naveen.u21_06@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(78, 78, 'isuru.u21_07@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(79, 79, 'malsha.u21_08@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(80, 80, 'heshan.u21_09@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(81, 81, 'vihan.u21_10@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(82, 82, 'duleesha.open_01@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(83, 83, 'sanka.open_02@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(84, 84, 'chanuka.open_03@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(85, 85, 'isuru.open_04@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(86, 86, 'akila.open_05@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(87, 87, 'raveen.open_06@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(88, 88, 'tharush.open_07@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(89, 89, 'kasun.open_08@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(90, 90, 'dinesh.open_09@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(91, 91, 'pasan.open_10@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-14 20:06:22'),
(92, 121, 'tharuka.fielding@eliteca.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-04-15 00:12:15');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `EquipmentID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Category` enum('Batting','Bowling','Protective','Training','Other') DEFAULT NULL,
  `AvailabilityStatus` enum('available','rented','maintenance','damaged') DEFAULT 'available',
  `RentalPrice` decimal(10,2) NOT NULL,
  `PurchasePrice` decimal(10,2) DEFAULT NULL,
  `Stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Quantity of equipment items available in inventory',
  `EqCondition` enum('new','good','fair','poor') DEFAULT 'good',
  `equipmentImage` varchar(255) DEFAULT NULL COMMENT 'Relative path to product image (e.g., uploads/shop_product/product_1_123456789.jpg)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cricket equipment available for rental or purchase';

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`EquipmentID`, `Name`, `Description`, `Category`, `AvailabilityStatus`, `RentalPrice`, `PurchasePrice`, `Stock`, `EqCondition`, `equipmentImage`) VALUES
(1, 'Cricket Bat - Professional', 'High-quality willow cricket bat', 'Batting', 'rented', 55.00, 150.00, 0, 'new', 'uploads/shop_product/product_9_1761119174.png'),
(2, 'Cricket Ball - Leather', 'Professional leather cricket ball', 'Bowling', 'rented', 35.00, 15.00, 10, 'new', 'uploads/shop_product/product_9_1761119174.png'),
(3, 'Helmet - Professional', 'Safety helmet with grill', 'Protective', 'rented', 45.00, 80.00, 0, 'new', 'uploads/shop_product/product_9_1761119174.png'),
(4, 'Batting Pads', 'Professional batting pads', 'Protective', 'available', 50.00, 100.00, 0, 'good', 'uploads/shop_product/product_9_1761119174.png'),
(5, 'Wicket Keeping Gloves', 'Professional WK gloves', 'Protective', 'available', 48.00, 90.00, 0, 'good', 'uploads/shop_product/product_9_1761119174.png'),
(6, 'Cricket Bat - Professional', 'High-quality willow cricket bat', 'Batting', 'available', 55.00, 150.00, 0, 'new', 'uploads/shop_product/product_9_1761119174.png'),
(7, 'Cricket Ball - Leather', 'Professional leather cricket ball', 'Bowling', 'available', 35.00, 15.00, 0, 'new', 'uploads/shop_product/product_9_1761119174.png'),
(8, 'Helmet - Professional', 'Safety helmet with grill', 'Protective', 'available', 45.00, 80.00, 0, 'new', 'uploads/shop_product/product_9_1761119174.png'),
(9, 'Batting Pads', 'Professional batting pads', 'Protective', 'available', 50.00, 100.00, 0, 'good', 'uploads/shop_product/product_9_1761119174.png'),
(10, 'Wicket Keeping Gloves', 'Professional WK gloves', 'Protective', 'available', 48.00, 90.00, 0, 'good', 'uploads/shop_product/product_9_1761119174.png'),
(11, 'Professional Cricket Bat', 'Premium English willow bat for professional players', 'Batting', 'available', 55.00, 150.00, 0, '', NULL),
(12, 'Complete Training Kit', 'Includes cones, stumps, practice balls, and agility equipment', 'Training', 'available', 50.00, 100.00, 0, 'good', NULL),
(13, 'Wicket Keeping Set', 'Professional wicket keeping gloves and pads', 'Protective', 'available', 52.00, 80.00, 0, '', NULL),
(14, 'Junior Cricket Set', 'Complete cricket set designed for junior players', 'Batting', 'available', 48.00, 75.00, 0, 'good', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipmentcart`
--

CREATE TABLE `equipmentcart` (
  `CartID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `EquipmentID` int(11) NOT NULL,
  `RequestedStartTime` datetime NOT NULL,
  `RequestedEndTime` datetime NOT NULL,
  `Status` enum('pending','confirmed','expired') DEFAULT 'pending',
  `AddedDate` datetime DEFAULT current_timestamp(),
  `ExpiresAt` datetime DEFAULT (current_timestamp() + interval 30 minute) COMMENT 'Reservation expires after 30 minutes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Equipment reservation cart for players to reserve equipment before rental';

-- --------------------------------------------------------

--
-- Table structure for table `equipmentrental`
--

CREATE TABLE `equipmentrental` (
  `RentalID` int(11) NOT NULL,
  `EquipmentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `RentalDate` date NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `Status` enum('active','returned','overdue','cancelled') DEFAULT 'active',
  `TotalCost` decimal(10,2) DEFAULT NULL,
  `ProcessedBy` int(11) NOT NULL,
  `ReturnInspectedBy` int(11) DEFAULT NULL COMMENT 'Employee who inspected returned equipment',
  `ReturnNotes` text DEFAULT NULL COMMENT 'Notes about equipment condition on return',
  `LateFee` decimal(10,2) DEFAULT 0.00 COMMENT 'Late return fee if applicable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Equipment rentals processed by shop employees';

--
-- Dumping data for table `equipmentrental`
--

INSERT INTO `equipmentrental` (`RentalID`, `EquipmentID`, `PlayerID`, `RentalDate`, `StartTime`, `EndTime`, `Status`, `TotalCost`, `ProcessedBy`, `ReturnInspectedBy`, `ReturnNotes`, `LateFee`) VALUES
(1, 1, 7, '2026-01-15', '2026-01-17 18:47:36', '2026-01-17 23:47:36', 'active', 500.00, 5, NULL, NULL, 0.00),
(2, 2, 15, '2026-01-16', '2026-01-17 19:47:36', '2026-01-18 00:47:36', 'active', 300.00, 5, NULL, NULL, 0.00),
(3, 3, 20, '2026-01-17', '2026-01-17 20:17:36', '2026-01-18 02:47:36', 'active', 450.00, 5, NULL, NULL, 0.00),
(4, 4, 7, '2026-01-12', '2026-01-15 20:47:36', '2026-01-16 20:47:36', 'returned', 500.00, 5, NULL, NULL, 0.00),
(5, 5, 15, '2026-01-10', '2026-01-12 20:47:36', '2026-01-14 20:47:36', 'returned', 300.00, 5, NULL, NULL, 0.00);

--
-- Triggers `equipmentrental`
--
DELIMITER $$
CREATE TRIGGER `tr_equipment_rental_status` AFTER INSERT ON `equipmentrental` FOR EACH ROW BEGIN
    IF NEW.Status = 'active' THEN
        UPDATE Equipment SET AvailabilityStatus = 'rented' WHERE EquipmentID = NEW.EquipmentID;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_equipment_return_status` AFTER UPDATE ON `equipmentrental` FOR EACH ROW BEGIN
    IF NEW.Status = 'returned' AND OLD.Status = 'active' THEN
        UPDATE Equipment SET AvailabilityStatus = 'available' WHERE EquipmentID = NEW.EquipmentID;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `equipmentreview`
--

CREATE TABLE `equipmentreview` (
  `ReviewID` int(11) NOT NULL,
  `EquipmentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `RentalID` int(11) NOT NULL COMMENT 'Reference to equipment rental',
  `Rating` int(11) NOT NULL COMMENT 'Rating from 1 to 5 stars',
  `ReviewTitle` varchar(255) DEFAULT NULL,
  `ReviewText` text DEFAULT NULL,
  `ConditionRating` int(11) NOT NULL COMMENT 'Equipment condition rating 1-5',
  `UsabilityRating` int(11) NOT NULL COMMENT 'How easy it was to use 1-5',
  `WouldRecommend` tinyint(1) DEFAULT 1,
  `ReviewDate` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected','flagged') DEFAULT 'pending',
  `ModeratedBy` int(11) DEFAULT NULL COMMENT 'Staff who moderated the review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `EventID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` enum('Training Camp','Workshop','Seminar','Competition','Tournament','Match','Trial','Meeting','Other') DEFAULT NULL,
  `Category` enum('junior','senior','youth','professional','recreational','academy') DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `StartDate` datetime NOT NULL,
  `EndDate` datetime NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Status` enum('upcoming','registration_open','registration_closed','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `RegistrationStart` datetime DEFAULT NULL,
  `RegistrationEnd` datetime DEFAULT NULL,
  `PrimaryContact` varchar(255) DEFAULT NULL,
  `ContactEmail` varchar(255) DEFAULT NULL,
  `ContactPhone` varchar(20) DEFAULT NULL,
  `MaxParticipants` int(11) DEFAULT NULL,
  `RegistrationFee` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Academy events and training camps';

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`EventID`, `Name`, `Type`, `Category`, `Description`, `StartDate`, `EndDate`, `Location`, `Status`, `RegistrationStart`, `RegistrationEnd`, `PrimaryContact`, `ContactEmail`, `ContactPhone`, `MaxParticipants`, `RegistrationFee`) VALUES
(4, 'elite summer training', 'Training Camp', 'junior', NULL, '2025-10-31 07:00:00', '2025-10-31 17:00:00', 'main ground', 'completed', '2025-10-22 16:41:00', '2025-10-23 15:41:00', 'esandu', 'esandu@gmail.com', '0987654321', NULL, NULL),
(5, 'summer camp', 'Training Camp', 'junior', NULL, '2025-10-30 20:59:00', '2025-10-31 03:04:00', 'main ground two', 'completed', '2025-10-23 20:00:00', '2025-10-24 20:02:00', 'esandu', 'e@gmail.com', '0987654321', 10000, 1000000.00),
(7, 'Music Fest', 'Trial', 'academy', 'Relaxation', '2025-10-23 01:04:00', '2025-10-23 01:35:00', 'Ground', 'completed', '2025-10-23 01:03:00', '2025-10-23 01:04:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', 100, NULL),
(8, 'Music Fest23', 'Seminar', 'senior', 'Relaxation', '2025-10-23 04:05:00', '2025-10-23 07:05:00', 'Ground', 'completed', '2025-10-23 01:05:00', '2025-10-23 01:10:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', NULL, NULL),
(9, 'Music Fest12356', 'Training Camp', 'youth', 'good', '2025-10-24 10:34:00', '2025-10-28 15:33:00', '2Ground', 'completed', '2025-10-23 10:35:00', '2025-10-23 10:39:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', 11, 1.00),
(10, 'Mu3', 'Training Camp', 'senior', 'dcvfrverv', '2025-10-28 00:49:00', '2025-10-29 00:52:00', 'Jwddd', 'completed', '2025-10-27 00:47:00', '2025-10-27 00:50:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', 7, 0.04),
(11, 'Mu55555910', 'Training Camp', 'junior', 'ycjjfufu', '2026-01-18 09:51:00', '2026-01-28 09:51:00', 'Ground', 'completed', '2026-01-17 09:53:00', '2026-01-18 09:51:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', NULL, 65.00),
(12, 'Mu2456', 'Match', 'senior', 'wevrvev', '2026-04-17 14:08:00', '2026-05-18 10:11:00', 'Ground', 'completed', '2026-06-17 10:08:00', '2026-07-17 10:11:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eventenrollment`
--

CREATE TABLE `eventenrollment` (
  `EnrollmentID` int(11) NOT NULL,
  `EventID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `EnrollmentDate` datetime DEFAULT current_timestamp(),
  `Status` enum('enrolled','attended','cancelled') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player enrollment in events';

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `FacilityID` int(11) NOT NULL,
  `f_code` varchar(20) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Capacity` int(11) NOT NULL,
  `AvailabilityStatus` enum('available','occupied','maintenance') DEFAULT 'available',
  `HourlyRate` decimal(10,2) DEFAULT 0.00,
  `facilityImage` varchar(255) DEFAULT NULL COMMENT 'Relative path to product image (e.g., uploads/shop_product/product_1_123456789.jpg)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cricket facilities like nets, bowling machines, grounds';

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`FacilityID`, `f_code`, `Name`, `Location`, `Capacity`, `AvailabilityStatus`, `HourlyRate`, `facilityImage`) VALUES
(1, 'F-PN1', 'Practice Net 1', 'North Ground', 6, 'available', 2500.00, NULL),
(2, 'F-PN2', 'Practice Net 2', 'North Ground', 6, 'available', 2500.00, NULL),
(3, 'F-BM1', 'Bowling Machine', 'Training Center', 3, 'available', 2500.00, NULL),
(4, 'F-MG1', 'Main Ground', 'Center Field', 22, 'available', 10000.00, NULL),
(6, 'F-TR1', 'Trainer Room', 'Main Building — Ground Floor', 1, 'available', 1500.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `facilitybooking`
--

CREATE TABLE `facilitybooking` (
  `FacilityBookingID` int(11) NOT NULL,
  `FacilityID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `BookingDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `TotalCost` decimal(10,2) DEFAULT NULL,
  `BookedBy` int(11) DEFAULT NULL COMMENT 'Shop employee who processed booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Facility bookings managed by shop employees';

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `FromUserID` int(11) NOT NULL,
  `ToUserID` int(11) NOT NULL,
  `Content` text NOT NULL,
  `Rating` int(11) DEFAULT NULL COMMENT 'Rating from 1 to 5',
  `Category` enum('coach','trainer','facility','equipment','shop','general') DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','reviewed','resolved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`FeedbackID`, `FromUserID`, `ToUserID`, `Content`, `Rating`, `Category`, `CreatedDate`, `Status`) VALUES
(1, 6, 3, 'Coach Sarath explains techniques very clearly. Batting improved significantly.', 5, 'coach', '2026-02-13 12:19:31', 'pending'),
(2, 7, 3, 'Great group sessions, could use more individual feedback during practice.', 4, 'coach', '2026-02-13 12:19:31', 'pending'),
(3, 15, 3, 'The bowling drills are very effective. Would like more advanced sessions.', 4, 'coach', '2026-02-13 12:19:31', 'reviewed'),
(4, 18, 3, 'Excellent coaching style. Very motivating and patient with beginners.', 5, 'coach', '2026-02-13 12:19:31', 'resolved');

-- --------------------------------------------------------

--
-- Table structure for table `livenotification`
--

CREATE TABLE `livenotification` (
  `LiveNotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `ExpiresAt` datetime NOT NULL COMMENT 'Auto-cleanup expired notifications'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Real-time notifications for immediate user updates and alerts';

-- --------------------------------------------------------

--
-- Table structure for table `membershipplan`
--

CREATE TABLE `membershipplan` (
  `PlanID` int(11) NOT NULL,
  `PlanName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `MonthlyFee` decimal(10,2) NOT NULL,
  `SessionsPerWeek` int(11) DEFAULT 2,
  `PrivateSessionsIncluded` int(11) DEFAULT 0,
  `FacilityAccessIncluded` tinyint(1) DEFAULT 0,
  `Status` enum('active','inactive') DEFAULT 'active',
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Different membership plans with varying benefits';

--
-- Dumping data for table `membershipplan`
--

INSERT INTO `membershipplan` (`PlanID`, `PlanName`, `Description`, `MonthlyFee`, `SessionsPerWeek`, `PrivateSessionsIncluded`, `FacilityAccessIncluded`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'general', 'group only', 4500.00, 3, 0, 0, 'active', '2026-04-07 22:16:21', '2026-04-14 19:33:18'),
(2, 'private', 'private only', 7000.00, 2, 2, 1, 'active', '2026-04-07 22:16:21', '2026-04-07 22:16:21'),
(3, 'pro', 'both', 10000.00, 5, 2, 1, 'active', '2026-04-07 22:16:21', '2026-04-14 19:34:10'),
(4, 'facility_only', 'Facility access without a coach. No recurring monthly membership fee. Users pay the facility booking price per booking.', 0.00, 0, 0, 1, 'active', '2026-04-12 22:20:41', '2026-04-12 22:20:41');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `NotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Type` varchar(50) NOT NULL COMMENT 'notification type: new_player_registration, coach_assignment, etc.',
  `Title` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `Data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional notification data as JSON' CHECK (json_valid(`Data`)),
  `ActionUrl` varchar(500) DEFAULT NULL COMMENT 'URL for notification action button',
  `IsRead` tinyint(1) DEFAULT 0,
  `ReadAt` datetime DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comprehensive notification system for all user communications';

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`NotificationID`, `UserID`, `Type`, `Title`, `Message`, `Data`, `ActionUrl`, `IsRead`, `ReadAt`, `CreatedAt`) VALUES
(1, 1, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 13:02:42'),
(2, 2, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 13:10:39'),
(3, 3, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 13:10:39'),
(4, 4, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 13:10:39'),
(5, 5, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 13:10:39'),
(6, 6, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 13:10:39'),
(7, 7, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 16:08:42'),
(8, 8, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 16:10:01'),
(9, 9, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 16:12:47'),
(10, 10, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 16:13:52'),
(11, 11, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 23:38:52'),
(12, 12, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 23:44:38'),
(13, 13, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 23:44:38'),
(14, 14, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-18 23:44:38'),
(15, 15, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-19 21:30:13'),
(16, 16, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-22 13:22:54'),
(18, 18, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-22 21:19:54'),
(19, 19, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-22 21:30:39'),
(20, 20, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-23 10:08:20'),
(21, 21, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-01-17 09:19:41'),
(22, 22, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-02-03 14:11:50'),
(23, 3, 'session', 'New Session Booking', 'Player Esandu has enrolled in your batting session', NULL, NULL, 0, NULL, '2026-02-13 11:19:31'),
(24, 3, 'injury', 'Injury Report Filed', 'Swairi reported a minor knee strain during practice', NULL, NULL, 0, NULL, '2026-02-13 09:19:31'),
(25, 3, 'event', 'Tournament Update', 'Junior Championship registration deadline extended to March 1', NULL, NULL, 0, NULL, '2026-02-12 12:19:31'),
(26, 3, 'player', 'Player Achievement', 'Vijini achieved Man of the Match in elite league', NULL, NULL, 1, NULL, '2026-02-11 12:19:31'),
(27, 3, 'session', 'Session Feedback', 'Please provide feedback for your completed sessions this week', NULL, NULL, 1, NULL, '2026-02-10 12:19:31'),
(28, 3, 'system', 'Profile Update Reminder', 'Please update your coaching certifications for the new term', NULL, NULL, 1, NULL, '2026-02-08 12:19:31'),
(31, 25, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-02-17 13:57:36'),
(32, 15, 'stats_updated', 'Performance Statistics Updated', 'Your overall performance statistics have been updated based on your verified match performance.', NULL, NULL, 0, NULL, '2026-03-29 01:04:09'),
(33, 15, 'stats_updated', 'Performance Statistics Updated', 'Your overall performance statistics have been updated based on your verified match performance.', NULL, NULL, 0, NULL, '2026-03-29 03:02:04'),
(34, 25, 'stats_updated', 'Performance Statistics Updated', 'Your overall performance statistics have been updated based on your verified match performance.', NULL, NULL, 0, NULL, '2026-03-29 09:08:27'),
(35, 26, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-10 08:06:39'),
(36, 15, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your age group is now Under 17. Your coach assignments were refreshed: Batting: coach Kasun, Bowling: Kumara Dharmasena.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-10 18:22:44'),
(37, 27, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-10 19:32:16'),
(38, 28, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-12 15:25:28'),
(39, 29, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-12 15:49:26'),
(40, 29, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your coach assignments were refreshed: Bowling: venuk Wickrama.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-14 10:31:10'),
(41, 15, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your coach assignments were refreshed.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-14 11:49:54'),
(42, 30, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 13:13:04'),
(43, 31, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 13:13:04'),
(44, 15, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your age group is now Under 17. Your coach assignments were refreshed: Fielding: Coach Ruwan.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-14 13:18:50'),
(45, 32, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:49'),
(46, 33, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:49'),
(47, 34, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(48, 35, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(49, 36, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(50, 37, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(51, 38, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(52, 39, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(53, 40, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(54, 41, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(55, 42, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(56, 43, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(57, 44, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(58, 45, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(59, 46, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(60, 47, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(61, 48, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(62, 49, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(63, 50, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(64, 51, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(65, 52, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(66, 53, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(67, 54, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(68, 55, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(69, 56, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 15:13:50'),
(70, 15, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your coach assignments were refreshed: Batting: venuk Wickrama, Bowling: Coach Ruwan, Fielding: Coach Sarath.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-14 15:39:59'),
(71, 29, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your coach assignments were refreshed: Batting: coach Kasun, Bowling: coach Dharshana.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-14 15:43:03'),
(72, 57, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(73, 58, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(74, 59, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(75, 60, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(76, 61, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(77, 62, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(78, 63, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(79, 64, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(80, 65, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(81, 66, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(82, 67, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(83, 68, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(84, 69, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(85, 70, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(86, 71, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(87, 72, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(88, 73, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(89, 74, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(90, 75, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(91, 76, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(92, 77, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(93, 78, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(94, 79, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(95, 80, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(96, 81, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(97, 82, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(98, 83, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(99, 84, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(100, 85, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(101, 86, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(102, 87, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(103, 88, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(104, 89, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(105, 90, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(106, 91, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-14 20:06:22'),
(107, 68, 'coach_assignment', 'Age Group / Coach Assignment Updated', 'Your coach assignments were refreshed: Batting: venuk Wickrama, Bowling: Coach Ruwan, Fielding: Coach Sarath.', NULL, 'http://localhost/Elite/player/dashboard', 0, NULL, '2026-04-14 21:25:18'),
(108, 121, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-04-15 00:12:15');

-- --------------------------------------------------------

--
-- Table structure for table `nutritionplan`
--

CREATE TABLE `nutritionplan` (
  `PlanID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `PlayerID` int(11) DEFAULT NULL,
  `nutritionPlanName` varchar(255) DEFAULT NULL,
  `DietDetails` text NOT NULL,
  `Notes` text DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customized nutrition plans for players';

--
-- Dumping data for table `nutritionplan`
--

INSERT INTO `nutritionplan` (`PlanID`, `TrainerID`, `PlayerID`, `nutritionPlanName`, `DietDetails`, `Notes`, `Duration`, `Status`, `CreatedDate`) VALUES
(4, 4, 16, 'High Carb (Match Preparation) Plan', 'Goal: Maximise energy availability for match days.\n\nGuidelines:\n- Increase carbs 24–48h pre-match (rice, pasta, potatoes, fruit).\n- Keep protein moderate; keep fats lower close to match time.\n- Hydrate consistently; include electrolytes if sweating heavily.\n\nTiming:\n- Pre-match meal (3–4h): high carb + moderate protein.\n- Top-up snack (60–90 min): easily digested carbs.', 'Allergic to carbs', 60, 'active', '2026-04-08'),
(6, 4, 18, 'Balanced Diet Plan', 'Goal: Everyday performance for training days.\n\nGuidelines:\n- Plate method: 1/2 vegetables, 1/4 protein, 1/4 carbs.\n- 2–3 fruit servings daily.\n- Hydrate and limit sugary drinks.\n\nTiming:\n- Spread meals evenly across the day.\n- Include a recovery snack after intense sessions.', 'no', 40, 'active', '2026-04-09'),
(9, 8, 16, 'Balanced Diet Plan', 'Goal: Everyday performance for training days.\n\nGuidelines:\n- Plate method: 1/2 vegetables, 1/4 protein, 1/4 carbs.\n- 2–3 fruit servings daily.\n- Hydrate and limit sugary drinks.\n\nTiming:\n- Spread meals evenly across the day.\n- Include a recovery snack after intense sessions.', 'gfds', 20, 'active', '2026-04-10'),
(10, 4, 26, 'High Carb (Match Preparation) Plan', 'Goal: Maximise energy availability for match days.\n\nGuidelines:\n- Increase carbs 24–48h pre-match (rice, pasta, potatoes, fruit).\n- Keep protein moderate; keep fats lower close to match time.\n- Hydrate consistently; include electrolytes if sweating heavily.\n\nTiming:\n- Pre-match meal (3–4h): high carb + moderate protein.\n- Top-up snack (60–90 min): easily digested carbs.', '', 20, 'active', '2026-04-10'),
(11, 4, 25, 'Recovery Plan', 'Goal: Support recovery after matches or during injury rehab.\n\nGuidelines:\n- Higher protein + micronutrients (iron, calcium, vitamin D).\n- Anti-inflammatory foods (omega-3 sources, colourful vegetables).\n- Prioritise sleep-supportive routine and hydration.\n\nTiming:\n- Recovery meal within 60–90 min post-match/training.', '', 30, 'active', '2026-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `nutritionplan_player`
--

CREATE TABLE `nutritionplan_player` (
  `PlanID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AssignedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nutritionplan_player`
--

INSERT INTO `nutritionplan_player` (`PlanID`, `PlayerID`, `AssignedDate`) VALUES
(9, 16, '2026-04-10'),
(9, 25, '2026-04-10'),
(10, 26, '2026-04-10'),
(11, 25, '2026-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `performanceupdate`
--

CREATE TABLE `performanceupdate` (
  `UpdateID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL COMMENT 'Coach making the update',
  `SessionID` int(11) DEFAULT NULL COMMENT 'Related training session if applicable',
  `UpdateType` enum('match_performance','training_assessment','skill_evaluation','fitness_test') NOT NULL,
  `RunsScored` int(11) DEFAULT NULL,
  `BallsFaced` int(11) DEFAULT NULL,
  `Fours` int(11) DEFAULT NULL,
  `Sixes` int(11) DEFAULT NULL,
  `WicketsTaken` int(11) DEFAULT NULL,
  `OversBowled` decimal(4,1) DEFAULT NULL,
  `RunsConceded` int(11) DEFAULT NULL,
  `CatchesTaken` int(11) DEFAULT NULL,
  `TechnicalRating` int(11) DEFAULT NULL COMMENT 'Rating 1-10',
  `FitnessRating` int(11) DEFAULT NULL COMMENT 'Rating 1-10',
  `AttitudeRating` int(11) DEFAULT NULL COMMENT 'Rating 1-10',
  `Comments` text DEFAULT NULL COMMENT 'Coach comments and observations',
  `UpdateDate` datetime DEFAULT current_timestamp(),
  `Status` enum('pending_approval','approved','rejected') DEFAULT 'approved',
  `ApprovedBy` int(11) DEFAULT NULL COMMENT 'Head coach or admin who approved',
  `ApprovedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual performance updates and assessments by coaches with full audit trail';

--
-- Triggers `performanceupdate`
--
DELIMITER $$
CREATE TRIGGER `tr_performance_update_logging` AFTER INSERT ON `performanceupdate` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_performance_update_validation` BEFORE INSERT ON `performanceupdate` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `performanceupdatesummary`
-- (See below for the actual view)
--
CREATE TABLE `performanceupdatesummary` (
`PlayerID` int(11)
,`PlayerName` varchar(255)
,`TotalUpdates` bigint(21)
,`ApprovedUpdates` bigint(21)
,`PendingUpdates` bigint(21)
,`LastUpdateDate` datetime
,`AvgTechnicalRating` double
,`AvgFitnessRating` double
,`AvgAttitudeRating` double
);

-- --------------------------------------------------------

--
-- Table structure for table `playercoach`
--

CREATE TABLE `playercoach` (
  `PlayerCoachID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `CoachID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` enum('active','completed','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Many-to-many relationship between players and coaches';

-- --------------------------------------------------------

--
-- Table structure for table `playercoachassignment`
--

CREATE TABLE `playercoachassignment` (
  `AssignmentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'References User table for players',
  `CoachID` int(11) NOT NULL COMMENT 'References User table for coaches',
  `AssignmentType` enum('regular','private','both') DEFAULT 'regular',
  `AssignedDate` datetime DEFAULT current_timestamp(),
  `Status` enum('active','inactive','completed') DEFAULT 'active',
  `Notes` text DEFAULT NULL COMMENT 'Admin notes about the assignment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player-Coach assignment tracking with automatic age-based suggestions';

--
-- Dumping data for table `playercoachassignment`
--

INSERT INTO `playercoachassignment` (`AssignmentID`, `PlayerID`, `CoachID`, `AssignmentType`, `AssignedDate`, `Status`, `Notes`) VALUES
(1, 6, 1, 'regular', '2026-02-13 12:19:31', 'active', 'Regular training group'),
(2, 7, 3, 'regular', '2026-02-13 12:19:31', 'active', 'Regular training group'),
(3, 15, 3, 'private', '2026-02-13 12:19:31', 'active', 'Private batting sessions'),
(4, 16, 3, 'regular', '2026-02-13 12:19:31', 'active', 'Regular training group'),
(5, 18, 3, 'both', '2026-02-13 12:19:31', 'active', 'Private and group sessions'),
(6, 20, 3, 'regular', '2026-02-13 12:19:31', 'active', 'Regular training group');

-- --------------------------------------------------------

--
-- Stand-in structure for view `playerdevelopmenttracking`
-- (See below for the actual view)
--
CREATE TABLE `playerdevelopmenttracking` (
`PlayerID` int(11)
,`PlayerName` varchar(255)
,`Age` int(11)
,`BattingStyle` enum('Right-handed','Left-handed','Switch-hitter')
,`BowlingStyle` enum('Fast','Medium','Spin','Off-spin','Leg-spin','None')
,`TotalRuns` int(11)
,`TotalWickets` int(11)
,`BattingAverage` decimal(6,2)
,`BowlingAverage` decimal(6,2)
,`SessionsAttended` bigint(21)
,`PerformanceUpdatesReceived` bigint(21)
,`AvgSessionRating` double
,`LastPerformanceUpdate` datetime
,`CurrentCoach` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `playermatchperformance`
--

CREATE TABLE `playermatchperformance` (
  `PerformanceID` int(11) NOT NULL,
  `MatchID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `RunsScored` int(11) DEFAULT 0,
  `BallsFaced` int(11) DEFAULT 0,
  `WicketsTaken` int(11) DEFAULT 0,
  `OversBowled` decimal(4,1) DEFAULT 0.0,
  `RunsConceded` int(11) DEFAULT 0,
  `Catches` int(11) DEFAULT 0,
  `Stumpings` int(11) DEFAULT 0,
  `Rating` decimal(3,1) DEFAULT 0.0 COMMENT 'Performance rating out of 10',
  `VerifiedStatus` enum('pending','verified','rejected') DEFAULT 'pending' COMMENT 'Verification status of performance statistics',
  `AddedBy` int(11) DEFAULT NULL COMMENT 'User who added this performance record',
  `VerifiedBy` int(11) DEFAULT NULL COMMENT 'Coach/Admin who verified this record',
  `VerifiedAt` datetime DEFAULT NULL COMMENT 'When the record was verified',
  `CreatedAt` datetime DEFAULT current_timestamp() COMMENT 'When the record was created',
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'When the record was last updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual player performance in matches';

--
-- Dumping data for table `playermatchperformance`
--

INSERT INTO `playermatchperformance` (`PerformanceID`, `MatchID`, `PlayerID`, `RunsScored`, `BallsFaced`, `WicketsTaken`, `OversBowled`, `RunsConceded`, `Catches`, `Stumpings`, `Rating`, `VerifiedStatus`, `AddedBy`, `VerifiedBy`, `VerifiedAt`, `CreatedAt`, `UpdatedAt`) VALUES
(4, 4, 25, 56, 39, 0, 0.0, 0, 0, 0, 0.0, 'verified', 25, NULL, NULL, '2026-02-18 21:03:21', '2026-03-29 09:08:27'),
(5, 8, 25, 10, 3, 4, 5.0, 30, 1, 0, 0.0, 'pending', 25, NULL, NULL, '2026-02-18 21:03:58', '2026-02-18 21:03:58'),
(6, 7, 25, 0, 0, 0, 0.0, 0, 0, 0, 0.0, 'pending', 25, NULL, NULL, '2026-02-18 21:24:09', '2026-02-18 21:24:09'),
(13, 15, 25, 45, 0, 0, 0.0, 0, 0, 0, 0.0, 'pending', 25, NULL, NULL, '2026-02-25 09:33:14', '2026-02-25 09:33:14'),
(15, 3, 7, 51, 15, 1, 3.0, 25, 0, 0, 0.0, 'pending', 7, NULL, NULL, '2026-02-25 09:51:14', '2026-03-29 09:00:58'),
(17, 7, 15, 5, 10, 1, 3.0, 21, 0, 0, 0.0, 'verified', 15, NULL, NULL, '2026-02-25 09:53:43', '2026-03-28 23:32:05'),
(21, 15, 15, 7, 8, 0, 0.0, 0, 0, 0, 0.0, 'verified', 15, NULL, NULL, '2026-03-29 00:37:35', '2026-03-29 00:37:58'),
(22, 14, 15, 0, 150, 0, 0.0, 0, 0, 0, 0.0, 'verified', 15, NULL, NULL, '2026-03-29 00:42:24', '2026-03-29 01:04:09'),
(23, 9, 15, 50, 21, 1, 2.0, 15, 0, 0, 0.0, 'pending', 15, NULL, NULL, '2026-03-29 03:01:41', '2026-04-10 21:39:02'),
(24, 11, 15, 45, 29, 0, 0.0, 0, 0, 0, 0.0, 'pending', 15, NULL, NULL, '2026-04-02 06:13:40', '2026-04-02 06:13:40'),
(26, 10, 15, 23, 15, 1, 2.0, 15, 0, 0, 0.0, 'pending', 15, NULL, NULL, '2026-04-10 06:45:50', '2026-04-10 06:45:50');

--
-- Triggers `playermatchperformance`
--
DELIMITER $$
CREATE TRIGGER `tr_update_overall_stats` AFTER UPDATE ON `playermatchperformance` FOR EACH ROW BEGIN
    -- Only run once when record transitions to verified
    IF NEW.VerifiedStatus = 'verified' AND OLD.VerifiedStatus <> 'verified' THEN

        UPDATE PlayerOverallStats
        SET
            MatchesPlayed = MatchesPlayed + 1,
            TotalRuns = TotalRuns + COALESCE(NEW.RunsScored, 0),
            TotalWickets = TotalWickets + COALESCE(NEW.WicketsTaken, 0),
            HighestScore = GREATEST(HighestScore, COALESCE(NEW.RunsScored, 0)),
            BattingAverage = CASE
                WHEN (MatchesPlayed + 1) > 0
                    THEN ROUND((TotalRuns + COALESCE(NEW.RunsScored, 0)) / (MatchesPlayed + 1), 2)
                ELSE 0
            END,
            BowlingAverage = CASE
                WHEN (TotalWickets + COALESCE(NEW.WicketsTaken, 0)) > 0
                    THEN ROUND((TotalRuns + COALESCE(NEW.RunsScored, 0)) / (TotalWickets + COALESCE(NEW.WicketsTaken, 0)), 2)
                ELSE 0
            END,
            StrikeRate = CASE
                WHEN (COALESCE(NEW.BallsFaced, 0)) > 0
                    THEN ROUND((COALESCE(NEW.RunsScored, 0) / COALESCE(NEW.BallsFaced, 0)) * 100, 2)
                ELSE StrikeRate
            END,
            EconomyRate = CASE
                WHEN COALESCE(NEW.OversBowled, 0) > 0
                    THEN ROUND(COALESCE(NEW.RunsConceded, 0) / COALESCE(NEW.OversBowled, 0), 2)
                ELSE EconomyRate
            END,
            LastUpdatedBy = NEW.VerifiedBy,
            LastUpdated = NOW()
        WHERE PlayerID = NEW.PlayerID;

        INSERT INTO ActivityLog (UserID, Action, Description)
        VALUES (
            NEW.PlayerID,
            'stats_updated_by_coach',
            CONCAT(
                'Overall stats updated by coach ID: ',
                COALESCE(NEW.VerifiedBy, 0),
                ' based on performance ID: ',
                NEW.PerformanceID
            )
        );

        INSERT INTO Notification (UserID, Type, Title, Message)
        VALUES (
            NEW.PlayerID,
            'stats_updated',
            'Performance Statistics Updated',
            'Your overall performance statistics have been updated based on your verified match performance.'
        );

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `playermedicalrecord`
--

CREATE TABLE `playermedicalrecord` (
  `RecordID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `Diagnosis` enum('Sprain','Strain','Fracture','Dislocation','Concussion','Tear','Laceration','Overuse/Inflammation','Illness') DEFAULT NULL,
  `TreatmentGiven` enum('RICE Procedure','First Aid/Wound Care','Physiotherapy','Medication','Referral to Specialist','Surgery','Observation') DEFAULT NULL,
  `RecoveryStatus` enum('ongoing','recovering','fully_recovered','chronic_condition') DEFAULT 'ongoing',
  `InjuryDate` date NOT NULL,
  `HappenedAtAcademy` enum('yes','no') DEFAULT 'no' COMMENT 'Did the injury occur at the academy?',
  `RestDaysNeeded` int(11) DEFAULT 0 COMMENT 'Estimated rest days required for recovery',
  `DiagnosisReceiptURL` varchar(255) DEFAULT NULL COMMENT 'Path or URL of uploaded diagnosis receipt image/file',
  `ReportedDate` date NOT NULL,
  `ReportedBy` int(11) DEFAULT NULL COMMENT 'Doctor, trainer, or player who reported',
  `verifyStatus` enum('pending','approved','rejected') DEFAULT 'pending',
  `bodyarea` enum('Head/Face','Neck','Shoulder','Arm/Elbow','Hand/Wrist','Chest/Back','Hip/Groin','Thigh','Knee','Lower Leg','Ankle/Foot') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Medical records and injury tracking';

--
-- Dumping data for table `playermedicalrecord`
--

INSERT INTO `playermedicalrecord` (`RecordID`, `PlayerID`, `Diagnosis`, `TreatmentGiven`, `RecoveryStatus`, `InjuryDate`, `HappenedAtAcademy`, `RestDaysNeeded`, `DiagnosisReceiptURL`, `ReportedDate`, `ReportedBy`, `verifyStatus`, `bodyarea`) VALUES
(15, 15, '', '', 'recovering', '2026-02-04', 'no', 0, 'uploads/medical_receipts/receipt_15_1771999461.pdf', '2026-02-25', 15, 'pending', NULL),
(17, 15, '', '', 'ongoing', '2026-04-01', 'no', 7, 'uploads/medical_receipts/receipt_15_1775066224.pdf', '2026-04-01', 15, 'rejected', NULL),
(18, 15, 'Fracture', 'Referral to Specialist', 'ongoing', '2026-04-03', 'no', 14, 'uploads/medical_receipts/receipt_15_1775190513.pdf', '2026-04-03', 15, '', 'Hand/Wrist'),
(19, 15, 'Dislocation', 'Physiotherapy', 'ongoing', '2026-04-05', 'no', 3, 'uploads/medical_receipts/receipt_15_1775408052.png', '2026-04-05', 15, '', 'Shoulder');

-- --------------------------------------------------------

--
-- Table structure for table `playeroverallstats`
--

CREATE TABLE `playeroverallstats` (
  `StatsID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `MatchesPlayed` int(11) DEFAULT 0,
  `TotalRuns` int(11) DEFAULT 0,
  `TotalWickets` int(11) DEFAULT 0,
  `HighestScore` int(11) DEFAULT 0,
  `BattingAverage` decimal(6,2) DEFAULT 0.00,
  `BowlingAverage` decimal(6,2) DEFAULT 0.00,
  `StrikeRate` decimal(6,2) DEFAULT 0.00,
  `EconomyRate` decimal(4,2) DEFAULT 0.00,
  `LastUpdated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `LastUpdatedBy` int(11) DEFAULT NULL COMMENT 'Coach who last updated the stats',
  `Centuries` int(11) DEFAULT 0,
  `HalfCenturies` int(11) DEFAULT 0,
  `FiveWickets` int(11) DEFAULT 0,
  `FourWickets` int(11) DEFAULT 0,
  `BestBowling` varchar(20) DEFAULT NULL COMMENT 'Best bowling figures (e.g., 5/24)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Overall career statistics for each player with coach tracking';

--
-- Dumping data for table `playeroverallstats`
--

INSERT INTO `playeroverallstats` (`StatsID`, `PlayerID`, `MatchesPlayed`, `TotalRuns`, `TotalWickets`, `HighestScore`, `BattingAverage`, `BowlingAverage`, `StrikeRate`, `EconomyRate`, `LastUpdated`, `LastUpdatedBy`, `Centuries`, `HalfCenturies`, `FiveWickets`, `FourWickets`, `BestBowling`) VALUES
(1, 7, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-18 16:08:42', NULL, 0, 0, 0, 0, NULL),
(2, 15, 4, 32, 2, 20, 10.40, 17.33, 95.24, 7.50, '2026-03-29 03:02:04', NULL, 0, 0, 0, 0, NULL),
(3, 16, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-22 13:22:54', NULL, 0, 0, 0, 0, NULL),
(4, 18, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-22 21:19:54', NULL, 0, 0, 0, 0, NULL),
(5, 20, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-23 10:08:20', NULL, 0, 0, 0, 0, NULL),
(6, 21, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-01-17 09:19:41', NULL, 0, 0, 0, 0, NULL),
(7, 22, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-02-03 14:11:50', NULL, 0, 0, 0, 0, NULL),
(9, 25, 1, 56, 0, 56, 56.00, 0.00, 143.59, 0.00, '2026-03-29 09:08:27', NULL, 0, 0, 0, 0, NULL),
(30, 26, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-10 08:06:39', NULL, 0, 0, 0, 0, NULL),
(32, 28, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-12 15:25:28', NULL, 0, 0, 0, 0, NULL),
(33, 29, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-12 15:49:26', NULL, 0, 0, 0, 0, NULL),
(34, 32, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:49', NULL, 0, 0, 0, 0, NULL),
(35, 33, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:49', NULL, 0, 0, 0, 0, NULL),
(36, 34, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(37, 35, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(38, 36, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(39, 37, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(40, 38, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(41, 39, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(42, 40, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(43, 41, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(44, 42, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(45, 43, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(46, 44, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(47, 45, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(48, 46, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(49, 47, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(50, 48, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(51, 49, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(52, 50, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(53, 51, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(54, 52, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(55, 53, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(56, 54, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(57, 55, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(58, 56, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 15:13:50', NULL, 0, 0, 0, 0, NULL),
(59, 57, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(60, 58, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(61, 59, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(62, 60, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(63, 61, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(64, 62, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(65, 63, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(66, 64, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(67, 65, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(68, 66, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(69, 67, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(70, 68, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(71, 69, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(72, 70, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(73, 71, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(74, 72, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(75, 73, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(76, 74, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(77, 75, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(78, 76, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(79, 77, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(80, 78, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(81, 79, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(82, 80, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(83, 81, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(84, 82, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(85, 83, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(86, 84, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(87, 85, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(88, 86, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(89, 87, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(90, 88, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(91, 89, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(92, 90, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL),
(93, 91, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-04-14 20:06:22', NULL, 0, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `playerprofile`
--

CREATE TABLE `playerprofile` (
  `PlayerID` int(11) NOT NULL,
  `BattingStyle` enum('Right-handed','Left-handed','Switch-hitter') DEFAULT NULL,
  `BowlingStyle` enum('Fast','Medium','Spin','Off-spin','Leg-spin','None') DEFAULT NULL,
  `JerseyNumber` int(11) DEFAULT NULL,
  `SubscriptionType` enum('basic','premium','private_only') DEFAULT 'basic',
  `EmergencyContactName` varchar(255) DEFAULT NULL COMMENT 'Emergency contact person',
  `EmergencyContactPhone` varchar(20) DEFAULT NULL COMMENT 'Emergency contact phone',
  `ParentGuardianName` varchar(255) DEFAULT NULL COMMENT 'Parent or guardian name',
  `ParentGuardianPhone` varchar(20) DEFAULT NULL COMMENT 'Parent or guardian phone',
  `SchoolInstitution` varchar(255) DEFAULT NULL COMMENT 'Educational institution',
  `PreviousExperience` text DEFAULT NULL COMMENT 'Previous cricket experience',
  `MedicalConditions` text DEFAULT NULL COMMENT 'Medical conditions or allergies',
  `HowHeardAboutUs` varchar(100) DEFAULT NULL COMMENT 'Marketing tracking field'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Enhanced player profile with comprehensive information for better management';

--
-- Dumping data for table `playerprofile`
--

INSERT INTO `playerprofile` (`PlayerID`, `BattingStyle`, `BowlingStyle`, `JerseyNumber`, `SubscriptionType`, `EmergencyContactName`, `EmergencyContactPhone`, `ParentGuardianName`, `ParentGuardianPhone`, `SchoolInstitution`, `PreviousExperience`, `MedicalConditions`, `HowHeardAboutUs`) VALUES
(7, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Right-handed', 'Fast', 78, 'private_only', 'gamage', '0986123456', 'gamage .S', '0986123456', 'Elite Cricket Academy', 'none', 'none', 'Social Media'),
(16, '', '', NULL, 'basic', 'waa', '', '', '', 'SLIIT', '', '', ''),
(18, 'Right-handed', 'Medium', 21, 'basic', '', '', '', '', 'SLIIT', '', '', ''),
(20, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Left-handed', 'Leg-spin', NULL, 'basic', '', '', '', '', 'RCG', '', '', ''),
(22, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, NULL, NULL, 45, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(58, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(59, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(60, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(61, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(62, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(63, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(64, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(65, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(66, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(67, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(68, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(69, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(70, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(71, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(72, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(73, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(74, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(75, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(76, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(77, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(78, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(79, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(80, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(81, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(82, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(83, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(84, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(85, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(86, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(87, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(88, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(89, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(90, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL),
(91, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, 'Elite Cricket Academy', NULL, NULL, NULL);

--
-- Triggers `playerprofile`
--
DELIMITER $$
CREATE TRIGGER `tr_create_player_stats` AFTER INSERT ON `playerprofile` FOR EACH ROW BEGIN
    INSERT INTO PlayerOverallStats (PlayerID) VALUES (NEW.PlayerID);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `playersubscription`
--

CREATE TABLE `playersubscription` (
  `SubscriptionID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `PlanID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` enum('active','suspended','cancelled','expired') DEFAULT 'active',
  `MonthlyFee` decimal(10,2) NOT NULL,
  `PaymentDay` int(11) DEFAULT 1 COMMENT 'Day of month when payment is due',
  `AutoRenewal` tinyint(1) DEFAULT 1,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `StatusChangedAt` datetime DEFAULT NULL,
  `CancelledAt` datetime DEFAULT NULL,
  `CancelReason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player membership subscriptions for regular group sessions';

--
-- Dumping data for table `playersubscription`
--

INSERT INTO `playersubscription` (`SubscriptionID`, `PlayerID`, `PlanID`, `StartDate`, `EndDate`, `Status`, `MonthlyFee`, `PaymentDay`, `AutoRenewal`, `CreatedAt`, `UpdatedAt`, `StatusChangedAt`, `CancelledAt`, `CancelReason`) VALUES
(1, 15, 2, '2026-01-01', '2026-12-31', 'active', 7000.00, 1, 1, '2026-04-07 22:16:21', '2026-04-07 22:16:21', NULL, NULL, NULL),
(3, 20, 3, '2026-04-01', '2027-04-01', 'active', 10000.00, 1, 1, '2026-04-09 20:30:25', '2026-04-09 20:30:25', NULL, NULL, NULL),
(4, 7, 1, '2026-04-01', '2027-04-01', 'active', 4500.00, 1, 1, '2026-04-09 20:41:22', '2026-04-09 20:41:22', NULL, NULL, NULL),
(6, 26, 1, '2026-04-10', NULL, 'active', 4500.00, 1, 1, '2026-04-10 08:06:39', '2026-04-10 08:06:39', NULL, NULL, NULL),
(7, 28, 1, '2026-04-12', NULL, 'active', 4500.00, 1, 1, '2026-04-12 15:25:28', '2026-04-12 15:25:28', NULL, NULL, NULL),
(8, 29, 1, '2026-04-12', NULL, 'active', 4500.00, 1, 1, '2026-04-12 15:49:26', '2026-04-12 15:49:26', NULL, NULL, NULL),
(9, 32, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(10, 33, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(11, 36, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(12, 34, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(13, 35, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(14, 40, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(15, 38, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(16, 39, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(17, 41, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(18, 37, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(19, 44, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(20, 46, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(21, 43, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(22, 42, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(23, 45, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(24, 47, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(25, 48, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(26, 49, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(27, 51, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(28, 50, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(29, 55, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(30, 54, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(31, 53, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(32, 56, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(33, 52, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 15:13:50', '2026-04-14 15:13:50', NULL, NULL, NULL),
(40, 57, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(41, 58, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(42, 59, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(43, 60, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(44, 61, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(45, 62, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(46, 63, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(47, 64, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(48, 65, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(49, 66, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(50, 67, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(51, 68, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(52, 69, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(53, 70, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(54, 71, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(55, 72, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(56, 73, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(57, 74, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(58, 75, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(59, 76, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(60, 77, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(61, 78, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(62, 79, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(63, 80, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(64, 81, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(65, 82, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(66, 83, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(67, 84, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(68, 85, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(69, 86, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(70, 87, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(71, 88, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(72, 89, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(73, 90, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL),
(74, 91, 1, '2026-04-14', NULL, 'active', 4500.00, 14, 1, '2026-04-14 20:10:07', '2026-04-14 20:10:07', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `playertournamentstats`
--

CREATE TABLE `playertournamentstats` (
  `TournamentStatsID` int(11) NOT NULL,
  `TournamentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `MatchesPlayed` int(11) DEFAULT 0,
  `TotalRuns` int(11) DEFAULT 0,
  `TotalWickets` int(11) DEFAULT 0,
  `BattingAverage` decimal(6,2) DEFAULT 0.00,
  `BowlingAverage` decimal(6,2) DEFAULT 0.00,
  `StrikeRate` decimal(6,2) DEFAULT 0.00,
  `EconomyRate` decimal(4,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player statistics aggregated by tournament';

-- --------------------------------------------------------

--
-- Table structure for table `playertrainer`
--

CREATE TABLE `playertrainer` (
  `PlayerTrainerID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` enum('active','completed','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Many-to-many relationship between players and trainers';

-- --------------------------------------------------------

--
-- Table structure for table `playertrainerassignment`
--

CREATE TABLE `playertrainerassignment` (
  `AssignmentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'References User table for players',
  `TrainerID` int(11) NOT NULL COMMENT 'References User table for trainers',
  `AssignedDate` datetime DEFAULT current_timestamp(),
  `Status` enum('active','inactive','completed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player-Trainer assignment tracking for physical training';

-- --------------------------------------------------------

--
-- Table structure for table `player_skill_coach_assignment`
--

CREATE TABLE `player_skill_coach_assignment` (
  `PlayerID` int(11) NOT NULL,
  `CoachingType` enum('batting','bowling','fielding') NOT NULL,
  `CoachID` int(11) NOT NULL,
  `AgeGroup` varchar(50) NOT NULL,
  `AssignmentSource` enum('auto_registration','admin_manual','system_refresh') NOT NULL DEFAULT 'auto_registration',
  `AssignedBy` int(11) DEFAULT NULL,
  `AssignedAt` datetime DEFAULT current_timestamp(),
  `Notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `player_skill_coach_assignment`
--

INSERT INTO `player_skill_coach_assignment` (`PlayerID`, `CoachingType`, `CoachID`, `AgeGroup`, `AssignmentSource`, `AssignedBy`, `AssignedAt`, `Notes`) VALUES
(15, 'batting', 27, 'Under 17', 'auto_registration', NULL, '2026-04-14 15:39:59', 'Assigned automatically during registration'),
(15, 'bowling', 31, 'Under 17', 'auto_registration', NULL, '2026-04-14 15:39:59', 'Assigned automatically during registration'),
(15, 'fielding', 3, 'Under 17', 'auto_registration', NULL, '2026-04-14 15:39:59', 'Assigned automatically during registration'),
(26, 'batting', 9, 'Under 13', 'auto_registration', NULL, '2026-04-10 08:42:34', 'Assigned automatically during registration'),
(26, 'bowling', 11, 'Under 13', 'auto_registration', NULL, '2026-04-10 08:42:34', 'Assigned automatically during registration'),
(28, 'batting', 9, 'Under 19', 'auto_registration', NULL, '2026-04-12 15:25:28', 'Assigned automatically during registration'),
(28, 'bowling', 12, 'Under 19', 'auto_registration', NULL, '2026-04-12 15:25:28', 'Assigned automatically during registration'),
(29, 'batting', 9, 'Under 13', 'auto_registration', NULL, '2026-04-14 15:43:03', 'Assigned automatically during registration'),
(29, 'bowling', 11, 'Under 13', 'auto_registration', NULL, '2026-04-14 15:43:03', 'Assigned automatically during registration'),
(32, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 13 batting group'),
(33, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 13 batting group'),
(34, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 13 batting group'),
(35, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 13 batting group'),
(36, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 13 batting group'),
(37, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 15 batting group'),
(38, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 15 batting group'),
(39, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 15 batting group'),
(40, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 15 batting group'),
(41, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 15 batting group'),
(42, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 17 bowling group'),
(43, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 17 bowling group'),
(44, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 17 bowling group'),
(45, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 17 bowling group'),
(46, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 17 bowling group'),
(47, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 19 bowling group'),
(48, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 19 bowling group'),
(49, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 19 bowling group'),
(50, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 19 bowling group'),
(51, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 19 bowling group'),
(52, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 21 fielding group'),
(53, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 21 fielding group'),
(54, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 21 fielding group'),
(55, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 21 fielding group'),
(56, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 15:39:15', 'Seed assignment - Under 21 fielding group'),
(57, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 13 batting group'),
(58, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 13 batting group'),
(59, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 13 batting group'),
(60, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 13 batting group'),
(61, 'batting', 30, 'Under 13', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 13 batting group'),
(62, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 15 batting group'),
(63, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 15 batting group'),
(64, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 15 batting group'),
(65, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 15 batting group'),
(66, 'batting', 30, 'Under 15', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 15 batting group'),
(67, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 17 bowling group'),
(68, 'batting', 27, 'Under 17', 'auto_registration', NULL, '2026-04-14 22:54:37', 'Assigned automatically during registration'),
(68, 'bowling', 31, 'Under 17', 'auto_registration', NULL, '2026-04-14 22:54:37', 'Assigned automatically during registration'),
(68, 'fielding', 3, 'Under 17', 'auto_registration', NULL, '2026-04-14 22:54:37', 'Assigned automatically during registration'),
(69, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 17 bowling group'),
(70, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 17 bowling group'),
(71, 'bowling', 31, 'Under 17', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 17 bowling group'),
(72, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 19 bowling group'),
(73, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 19 bowling group'),
(74, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 19 bowling group'),
(75, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 19 bowling group'),
(76, 'bowling', 31, 'Under 19', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 19 bowling group'),
(77, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 21 fielding group'),
(78, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 21 fielding group'),
(79, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 21 fielding group'),
(80, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 21 fielding group'),
(81, 'fielding', 31, 'Under 21', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Under 21 fielding group'),
(82, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(83, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(84, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(85, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(86, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(87, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(88, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(89, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(90, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group'),
(91, 'fielding', 31, 'Open', 'admin_manual', 1, '2026-04-14 20:22:23', 'Seed assignment - Open fielding group');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Category` enum('Batting','Bowling','Protective','Training','Merchandise','Accessories','Other') DEFAULT NULL,
  `Brand` varchar(100) DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `StockQuantity` int(11) DEFAULT 0,
  `Status` enum('active','discontinued','out_of_stock') DEFAULT 'active',
  `SKU` varchar(100) DEFAULT NULL COMMENT 'Stock Keeping Unit',
  `Weight` decimal(8,3) DEFAULT NULL COMMENT 'Product weight in kg',
  `Dimensions` varchar(100) DEFAULT NULL COMMENT 'Length x Width x Height',
  `AddedDate` datetime DEFAULT current_timestamp(),
  `UpdatedBy` int(11) DEFAULT NULL COMMENT 'Shop employee who last updated',
  `ProductImage` varchar(255) DEFAULT NULL COMMENT 'Relative path to product image (e.g., uploads/shop_product/product_1_123456789.jpg)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Shop products available for purchase - cricket gear, accessories, merchandise';

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `Name`, `Description`, `Category`, `Brand`, `Price`, `StockQuantity`, `Status`, `SKU`, `Weight`, `Dimensions`, `AddedDate`, `UpdatedBy`, `ProductImage`) VALUES
(14, 'Elite Pro English Willow Bat', 'Hand-selected English willow bat designed for balanced pickup and strong stroke play.', 'Batting', 'Elite', 12500.00, 18, 'active', 'BAT-ELITE-001', 1.180, '85 x 11 x 7 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_8_1761117247.jpg'),
(15, 'Performance Batting Gloves (Pair)', 'Breathable batting gloves with reinforced palm and flexible finger protection.', 'Batting', 'Elite', 1900.00, 40, 'active', 'GLV-ELITE-002', 0.260, '30 x 15 x 8 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(16, 'Cricket Helmet with Steel Grill', 'Lightweight protective helmet with adjustable strap and impact-absorbing inner padding.', 'Protective', 'Elite', 4200.00, 12, 'active', 'HLMT-ELITE-003', 0.820, '35 x 25 x 25 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(17, 'Leather Cricket Ball (Red) - Match', 'Premium stitched leather ball suitable for match and club level play.', 'Bowling', 'Elite', 650.00, 120, 'active', 'BALL-ELITE-004', 0.160, '8 x 8 x 8 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(18, 'Training Cones Set (20 pcs)', 'Set of 20 durable cones for fielding drills, agility training, and boundary marking.', 'Training', 'Elite', 850.00, 55, 'active', 'TRN-ELITE-005', 0.900, '32 x 32 x 20 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(19, 'Kit Bag (Wheel) - Large', 'Spacious kit bag with wheels, reinforced base, and multiple compartments.', 'Accessories', 'Elite', 5600.00, 9, 'active', 'BAG-ELITE-006', 3.800, '95 x 40 x 38 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(20, 'Academy Training Jersey', 'Moisture-wicking jersey ideal for training sessions and warm-ups.', 'Merchandise', 'Elite', 1450.00, 30, 'active', 'MERCH-ELITE-007', 0.220, '35 x 25 x 2 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(21, 'Arm Guard (Batting)', 'Comfort-fit arm guard with padded protection for confident front-foot play.', 'Protective', 'Elite', 1100.00, 25, 'active', 'PRT-ELITE-008', 0.180, '30 x 12 x 6 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(22, 'Thigh Pad Set (Pair)', 'Padded thigh guards for batting protection. Includes both left and right pads.', 'Protective', 'Elite', 1750.00, 22, 'active', 'PRT-ELITE-009', 0.420, '28 x 20 x 10 cm', '2026-04-01 08:22:59', NULL, 'uploads/shop_product/product_9_1761119174.png');

-- --------------------------------------------------------

--
-- Table structure for table `productcart`
--

CREATE TABLE `productcart` (
  `CartID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `AddedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Shopping cart for players to add products before purchase';

-- --------------------------------------------------------

--
-- Table structure for table `productorder`
--

CREATE TABLE `productorder` (
  `OrderID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `OrderDate` datetime DEFAULT current_timestamp(),
  `IsShipped` tinyint(1) NOT NULL DEFAULT 0,
  `ShippedAt` datetime DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('cash','card','bank_transfer','online') NOT NULL,
  `Status` enum('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
  `ProcessedBy` int(11) DEFAULT NULL COMMENT 'Shop employee who processed the order',
  `ShippingAddress` text DEFAULT NULL COMMENT 'Delivery address if different from user address',
  `OrderNotes` text DEFAULT NULL COMMENT 'Special instructions or notes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Purchase orders for products bought from the shop';

--
-- Dumping data for table `productorder`
--

INSERT INTO `productorder` (`OrderID`, `PlayerID`, `OrderDate`, `IsShipped`, `ShippedAt`, `TotalAmount`, `PaymentMethod`, `Status`, `ProcessedBy`, `ShippingAddress`, `OrderNotes`) VALUES
(11, 7, '2026-01-12 20:47:36', 0, NULL, 4500.00, 'card', 'completed', 5, NULL, 'First order - Cricket bat and gloves'),
(12, 16, '2026-01-14 20:47:36', 0, NULL, 2800.00, 'cash', 'completed', 5, NULL, 'Protective gear purchase'),
(13, 7, '2026-01-15 20:47:36', 0, NULL, 6200.00, 'online', 'processing', 5, NULL, 'Complete cricket kit'),
(14, 20, '2026-01-16 20:47:36', 0, NULL, 1500.00, 'card', 'pending', NULL, NULL, 'Cricket balls order');

-- --------------------------------------------------------

--
-- Table structure for table `productorderitem`
--

CREATE TABLE `productorderitem` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `SubTotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual items within each product order';

-- --------------------------------------------------------

--
-- Table structure for table `productreview`
--

CREATE TABLE `productreview` (
  `ReviewID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL COMMENT 'Reference to purchase order if bought',
  `Rating` int(11) NOT NULL COMMENT 'Rating from 1 to 5 stars',
  `ReviewTitle` varchar(255) DEFAULT NULL,
  `ReviewText` text DEFAULT NULL,
  `Pros` text DEFAULT NULL COMMENT 'What the player liked about the product',
  `Cons` text DEFAULT NULL COMMENT 'What could be improved',
  `WouldRecommend` tinyint(1) DEFAULT 1,
  `VerifiedPurchase` tinyint(1) DEFAULT 0 COMMENT 'True if player actually bought/rented the product',
  `ReviewDate` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected','flagged') DEFAULT 'pending',
  `ModeratedBy` int(11) DEFAULT NULL COMMENT 'Staff who moderated the review',
  `ModerationNotes` text DEFAULT NULL COMMENT 'Reason for approval/rejection',
  `HelpfulVotes` int(11) DEFAULT 0 COMMENT 'Number of users who found this review helpful'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `SessionID` int(11) NOT NULL,
  `SessionType` enum('Coaching','Physical Training') NOT NULL,
  `SessionMode` enum('Group','Private') DEFAULT 'Group',
  `CoachOrTrainerID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Status` enum('open','active','cancelled','completed') DEFAULT 'open',
  `MaxParticipants` int(11) DEFAULT 10,
  `PricePerSession` decimal(10,2) DEFAULT 0.00 COMMENT 'Cost for private sessions, 0 for monthly subscription group sessions',
  `IsRecurring` tinyint(1) DEFAULT 1 COMMENT 'True for regular group sessions, false for one-time sessions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Training sessions conducted by coaches or trainers with enhanced features';

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`SessionID`, `SessionType`, `SessionMode`, `CoachOrTrainerID`, `Name`, `Date`, `StartTime`, `EndTime`, `Location`, `Status`, `MaxParticipants`, `PricePerSession`, `IsRecurring`) VALUES
(4, 'Coaching', 'Group', 1, 'u15 batting2', '2025-10-23', '10:29:00', '15:30:00', 'ground', 'active', 10, 0.00, 1),
(7, 'Coaching', 'Group', 3, 'ABC2', '2025-10-28', '11:07:00', '11:46:00', 'Ground2', 'active', 40, 0.00, 1),
(8, 'Coaching', 'Group', 3, 'ABC', '2025-10-28', '00:49:00', '05:47:00', 'Ground', 'active', 10, 0.00, 1),
(9, 'Coaching', 'Group', 11, 'under13 batting', '2026-02-10', '15:30:00', '16:30:00', 'main ground', 'active', 10, 0.00, 1),
(10, 'Coaching', 'Group', 3, 'Morning Batting Practice', '2026-02-13', '09:00:00', '11:00:00', 'Main Ground', 'active', 15, 500.00, 1),
(11, 'Coaching', 'Private', 3, 'Advanced Bowling Technique', '2026-02-13', '14:00:00', '15:30:00', 'Indoor Net 1', 'active', 3, 1500.00, 0),
(12, 'Coaching', 'Group', 3, 'Fielding Drills', '2026-02-14', '08:00:00', '10:00:00', 'Practice Ground', 'active', 20, 500.00, 1),
(13, 'Coaching', 'Private', 3, 'Spin Bowling Masterclass', '2026-02-28', '10:00:00', '11:30:00', 'Indoor Net 2', 'active', 5, 2000.00, 0),
(14, 'Coaching', 'Group', 3, 'Match Simulation', '2026-02-16', '07:00:00', '12:00:00', 'Main Ground', 'active', 22, 750.00, 0),
(15, 'Coaching', 'Group', 3, 'Weekend Fitness and Cricket', '2026-02-18', '06:00:00', '08:00:00', 'Fitness Center', 'active', 25, 500.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shopemployeeprofile`
--

CREATE TABLE `shopemployeeprofile` (
  `ShopEmployeeID` int(11) NOT NULL,
  `Department` enum('Equipment','Facility','General') DEFAULT 'General',
  `HireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Shop employee managing products, rentals, and facility bookings';

--
-- Dumping data for table `shopemployeeprofile`
--

INSERT INTO `shopemployeeprofile` (`ShopEmployeeID`, `Department`, `HireDate`) VALUES
(5, 'General', '2024-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `slot_audit_log`
--

CREATE TABLE `slot_audit_log` (
  `LogID` int(11) NOT NULL,
  `EntityType` enum('template','occurrence','booking','staff_assignment') NOT NULL COMMENT 'Which table was changed',
  `EntityID` int(11) NOT NULL COMMENT 'PK value of the changed row',
  `Action` enum('create','update','cancel','delete','override') NOT NULL,
  `ChangedField` varchar(100) DEFAULT NULL COMMENT 'Column that changed, e.g. "Status", "FacilityID"',
  `OldValue` text DEFAULT NULL,
  `NewValue` text DEFAULT NULL,
  `Reason` text DEFAULT NULL COMMENT 'Justification entered by the user',
  `ChangedBy` int(11) NOT NULL COMMENT 'FK → user',
  `ChangedAt` datetime DEFAULT current_timestamp(),
  `IPAddress` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Append-only audit trail. Never UPDATE or DELETE rows in this table.';

--
-- Dumping data for table `slot_audit_log`
--

INSERT INTO `slot_audit_log` (`LogID`, `EntityType`, `EntityID`, `Action`, `ChangedField`, `OldValue`, `NewValue`, `Reason`, `ChangedBy`, `ChangedAt`, `IPAddress`) VALUES
(1, 'occurrence', 1, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(2, 'occurrence', 2, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(3, 'occurrence', 3, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(4, 'occurrence', 4, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(5, 'occurrence', 5, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(6, 'occurrence', 6, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(7, 'occurrence', 7, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(8, 'occurrence', 8, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(9, 'occurrence', 9, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(10, 'occurrence', 10, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(11, 'occurrence', 11, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(12, 'occurrence', 12, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(13, 'occurrence', 13, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(14, 'occurrence', 14, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(15, 'occurrence', 15, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(16, 'occurrence', 16, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(17, 'occurrence', 17, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(18, 'occurrence', 18, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(19, 'occurrence', 19, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(20, 'occurrence', 20, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(21, 'occurrence', 21, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(22, 'occurrence', 22, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(23, 'occurrence', 23, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(24, 'occurrence', 24, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(25, 'occurrence', 25, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(26, 'occurrence', 26, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(27, 'occurrence', 27, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(28, 'occurrence', 28, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(29, 'occurrence', 29, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(30, 'occurrence', 30, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(31, 'occurrence', 31, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(32, 'occurrence', 32, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(33, 'occurrence', 33, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:15', '::1'),
(34, 'occurrence', 34, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:16', '::1'),
(35, 'occurrence', 35, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:16', '::1'),
(36, 'occurrence', 36, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 01:23:16', '::1'),
(37, 'occurrence', 37, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(38, 'occurrence', 38, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(39, 'occurrence', 39, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(40, 'occurrence', 40, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(41, 'occurrence', 41, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(42, 'occurrence', 42, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(43, 'occurrence', 43, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(44, 'occurrence', 44, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(45, 'occurrence', 45, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(46, 'occurrence', 46, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(47, 'occurrence', 47, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(48, 'occurrence', 48, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(49, 'occurrence', 49, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(50, 'occurrence', 50, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(51, 'occurrence', 51, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(52, 'occurrence', 52, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(53, 'occurrence', 53, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(54, 'occurrence', 54, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(55, 'occurrence', 55, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(56, 'occurrence', 56, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(57, 'occurrence', 57, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(58, 'occurrence', 58, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(59, 'occurrence', 59, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(60, 'occurrence', 60, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(61, 'occurrence', 61, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(62, 'occurrence', 62, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(63, 'occurrence', 63, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(64, 'occurrence', 64, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(65, 'occurrence', 65, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(66, 'occurrence', 66, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(67, 'occurrence', 67, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(68, 'occurrence', 68, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(69, 'occurrence', 69, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(70, 'occurrence', 70, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(71, 'occurrence', 71, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(72, 'occurrence', 72, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(73, 'occurrence', 73, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(74, 'occurrence', 74, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 10:30:52', '::1'),
(75, 'booking', 1, 'create', NULL, NULL, 'confirmed', 'self', 29, '2026-04-14 10:42:26', '::1'),
(76, 'booking', 2, 'create', NULL, NULL, 'confirmed', 'self', 29, '2026-04-14 11:49:18', '::1'),
(77, 'occurrence', 75, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(78, 'occurrence', 76, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(79, 'occurrence', 77, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(80, 'occurrence', 78, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(81, 'occurrence', 79, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(82, 'occurrence', 80, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(83, 'occurrence', 81, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(84, 'occurrence', 82, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(85, 'occurrence', 83, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(86, 'occurrence', 84, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(87, 'occurrence', 85, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(88, 'occurrence', 86, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(89, 'occurrence', 87, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(90, 'occurrence', 88, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(91, 'occurrence', 89, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(92, 'occurrence', 90, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(93, 'occurrence', 91, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(94, 'occurrence', 92, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(95, 'occurrence', 93, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(96, 'occurrence', 94, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(97, 'occurrence', 95, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(98, 'occurrence', 96, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(99, 'occurrence', 97, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(100, 'occurrence', 98, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(101, 'occurrence', 99, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(102, 'occurrence', 100, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(103, 'occurrence', 101, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(104, 'occurrence', 102, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(105, 'occurrence', 103, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(106, 'occurrence', 104, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(107, 'occurrence', 105, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(108, 'occurrence', 106, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(109, 'occurrence', 107, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(110, 'occurrence', 108, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(111, 'occurrence', 109, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(112, 'occurrence', 110, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1'),
(113, 'occurrence', 111, 'create', NULL, NULL, 'scheduled', NULL, 1, '2026-04-14 15:53:06', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `slot_booking`
--

CREATE TABLE `slot_booking` (
  `BookingID` int(11) NOT NULL,
  `OccurrenceID` int(11) NOT NULL COMMENT 'FK → slot_occurrence',
  `PlayerID` int(11) NOT NULL COMMENT 'FK → user (Role = Player)',
  `BookingSource` enum('self','admin','shop_employee','system') NOT NULL DEFAULT 'self' COMMENT 'self=player portal | admin=management console | shop_employee=counter | system=automatic program allocation',
  `SubscriptionID` int(11) DEFAULT NULL COMMENT 'FK → playersubscription; NULL for direct-pay bookings (facility_only, private)',
  `MedicalClearedBy` int(11) DEFAULT NULL COMMENT 'FK → user (Admin); set when admin overrides an active-injury flag. NULL = no flag triggered.',
  `Status` enum('pending','confirmed','cancelled','attended','missed') NOT NULL DEFAULT 'pending',
  `ParticipantCount` int(11) NOT NULL DEFAULT 1 COMMENT 'How many people will use the reserved slot under this one booking, including the player.',
  `AmountCharged` decimal(10,2) NOT NULL DEFAULT 0.00,
  `PaymentStatus` enum('not_required','pending','paid','refunded') NOT NULL DEFAULT 'not_required',
  `PaymentMethod` enum('cash','card','online') DEFAULT NULL,
  `PaidAt` datetime DEFAULT NULL,
  `BookedBy` int(11) DEFAULT NULL COMMENT 'FK → user; NULL if self-booked',
  `CancelledBy` int(11) DEFAULT NULL COMMENT 'FK → user',
  `CancelledAt` datetime DEFAULT NULL,
  `CancelReason` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified booking table. One row links one player to one occurrence.';

--
-- Dumping data for table `slot_booking`
--

INSERT INTO `slot_booking` (`BookingID`, `OccurrenceID`, `PlayerID`, `BookingSource`, `SubscriptionID`, `MedicalClearedBy`, `Status`, `ParticipantCount`, `AmountCharged`, `PaymentStatus`, `PaymentMethod`, `PaidAt`, `BookedBy`, `CancelledBy`, `CancelledAt`, `CancelReason`, `CreatedAt`, `UpdatedAt`) VALUES
(2, 37, 29, 'self', 8, NULL, 'confirmed', 1, 2500.00, 'paid', '', NULL, 29, NULL, NULL, NULL, '2026-04-14 11:49:18', '2026-04-14 11:49:18');

-- --------------------------------------------------------

--
-- Table structure for table `slot_occurrence`
--

CREATE TABLE `slot_occurrence` (
  `OccurrenceID` int(11) NOT NULL,
  `TemplateID` int(11) DEFAULT NULL COMMENT 'NULL = ad-hoc (not from a template)',
  `SlotID` tinyint(4) NOT NULL COMMENT 'FK → slot_time_band',
  `OccurrenceDate` date NOT NULL,
  `FacilityID` int(11) DEFAULT NULL COMMENT 'Overrides template facility for this date',
  `LegacySessionID` int(11) DEFAULT NULL COMMENT 'Migration bridge only → old session.SessionID; NULL for all new rows',
  `Status` enum('scheduled','active','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `CancelReason` text DEFAULT NULL,
  `MaxParticipants` int(11) DEFAULT NULL COMMENT 'NULL = inherit from slot_template.MaxParticipants',
  `Notes` text DEFAULT NULL,
  `GeneratedBy` int(11) DEFAULT NULL COMMENT 'Admin UserID who ran batch generation',
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='One row per actual session on one calendar date. Generated from template or created ad-hoc.';

--
-- Dumping data for table `slot_occurrence`
--

INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(37, 18, 6, '2026-04-15', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(38, 18, 6, '2026-04-22', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(39, 18, 6, '2026-04-29', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(40, 18, 6, '2026-05-06', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(41, 18, 6, '2026-05-13', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(42, 18, 6, '2026-05-20', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(43, 18, 6, '2026-05-27', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(44, 18, 6, '2026-06-03', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(45, 18, 6, '2026-06-10', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(46, 18, 6, '2026-06-17', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(47, 18, 6, '2026-06-24', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(48, 18, 6, '2026-07-01', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(49, 18, 6, '2026-07-08', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(50, 18, 6, '2026-07-15', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(51, 18, 6, '2026-07-22', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(52, 18, 6, '2026-07-29', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(53, 18, 6, '2026-08-05', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(54, 18, 6, '2026-08-12', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(55, 18, 6, '2026-08-19', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(56, 18, 6, '2026-08-26', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(57, 18, 6, '2026-09-02', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(58, 18, 6, '2026-09-09', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(59, 18, 6, '2026-09-16', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(60, 18, 6, '2026-09-23', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(61, 18, 6, '2026-09-30', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(62, 18, 6, '2026-10-07', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(63, 18, 6, '2026-10-14', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(64, 18, 6, '2026-10-21', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(65, 18, 6, '2026-10-28', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(66, 18, 6, '2026-11-04', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(67, 18, 6, '2026-11-11', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(68, 18, 6, '2026-11-18', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(69, 18, 6, '2026-11-25', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(70, 18, 6, '2026-12-02', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(71, 18, 6, '2026-12-09', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(72, 18, 6, '2026-12-16', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(73, 18, 6, '2026-12-23', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(74, 18, 6, '2026-12-30', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 10:30:52'),
(75, 91, 5, '2026-04-19', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(76, 91, 5, '2026-04-26', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(77, 91, 5, '2026-05-03', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(78, 91, 5, '2026-05-10', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(79, 91, 5, '2026-05-17', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(80, 91, 5, '2026-05-24', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(81, 91, 5, '2026-05-31', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(82, 91, 5, '2026-06-07', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(83, 91, 5, '2026-06-14', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(84, 91, 5, '2026-06-21', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(85, 91, 5, '2026-06-28', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(86, 91, 5, '2026-07-05', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(87, 91, 5, '2026-07-12', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(88, 91, 5, '2026-07-19', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(89, 91, 5, '2026-07-26', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(90, 91, 5, '2026-08-02', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(91, 91, 5, '2026-08-09', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(92, 91, 5, '2026-08-16', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(93, 91, 5, '2026-08-23', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(94, 91, 5, '2026-08-30', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(95, 91, 5, '2026-09-06', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(96, 91, 5, '2026-09-13', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(97, 91, 5, '2026-09-20', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(98, 91, 5, '2026-09-27', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(99, 91, 5, '2026-10-04', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(100, 91, 5, '2026-10-11', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(101, 91, 5, '2026-10-18', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(102, 91, 5, '2026-10-25', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(103, 91, 5, '2026-11-01', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(104, 91, 5, '2026-11-08', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(105, 91, 5, '2026-11-15', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(106, 91, 5, '2026-11-22', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(107, 91, 5, '2026-11-29', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(108, 91, 5, '2026-12-06', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(109, 91, 5, '2026-12-13', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(110, 91, 5, '2026-12-20', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(111, 91, 5, '2026-12-27', 3, NULL, 'scheduled', NULL, NULL, NULL, 1, '2026-04-14 15:53:06'),
(114, 2, 6, '2026-04-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(115, 2, 6, '2026-04-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(116, 2, 6, '2026-04-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(117, 2, 6, '2026-05-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(118, 2, 6, '2026-05-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(119, 2, 6, '2026-05-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(120, 2, 6, '2026-05-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(121, 2, 6, '2026-06-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(122, 2, 6, '2026-06-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(123, 2, 6, '2026-06-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(124, 2, 6, '2026-06-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(125, 2, 6, '2026-06-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(126, 2, 6, '2026-07-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(127, 2, 6, '2026-07-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(128, 2, 6, '2026-07-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(129, 2, 6, '2026-07-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(130, 2, 6, '2026-08-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(131, 2, 6, '2026-08-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(132, 2, 6, '2026-08-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(133, 2, 6, '2026-08-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(134, 2, 6, '2026-08-31', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(135, 2, 6, '2026-09-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(136, 2, 6, '2026-09-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(137, 2, 6, '2026-09-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(138, 2, 6, '2026-09-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(139, 2, 6, '2026-10-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(140, 2, 6, '2026-10-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(141, 2, 6, '2026-10-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(142, 2, 6, '2026-10-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(143, 2, 6, '2026-11-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(144, 2, 6, '2026-11-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(145, 2, 6, '2026-11-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(146, 2, 6, '2026-11-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(147, 2, 6, '2026-11-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(148, 2, 6, '2026-12-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(149, 2, 6, '2026-12-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(150, 2, 6, '2026-12-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(151, 2, 6, '2026-12-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Mon 8-10', 1, '2026-04-14 16:02:09'),
(152, 3, 6, '2026-04-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(153, 3, 6, '2026-04-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(154, 3, 6, '2026-04-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(155, 3, 6, '2026-05-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(156, 3, 6, '2026-05-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(157, 3, 6, '2026-05-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(158, 3, 6, '2026-05-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(159, 3, 6, '2026-06-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(160, 3, 6, '2026-06-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(161, 3, 6, '2026-06-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(162, 3, 6, '2026-06-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(163, 3, 6, '2026-06-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(164, 3, 6, '2026-07-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(165, 3, 6, '2026-07-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(166, 3, 6, '2026-07-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(167, 3, 6, '2026-07-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(168, 3, 6, '2026-08-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(169, 3, 6, '2026-08-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(170, 3, 6, '2026-08-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(171, 3, 6, '2026-08-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(172, 3, 6, '2026-09-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(173, 3, 6, '2026-09-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(174, 3, 6, '2026-09-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(175, 3, 6, '2026-09-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(176, 3, 6, '2026-09-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(177, 3, 6, '2026-10-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(178, 3, 6, '2026-10-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(179, 3, 6, '2026-10-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(180, 3, 6, '2026-10-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(181, 3, 6, '2026-11-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(182, 3, 6, '2026-11-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(183, 3, 6, '2026-11-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(184, 3, 6, '2026-11-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(185, 3, 6, '2026-12-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(186, 3, 6, '2026-12-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(187, 3, 6, '2026-12-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(188, 3, 6, '2026-12-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(189, 3, 6, '2026-12-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Tue 8-10', 1, '2026-04-14 16:02:09'),
(190, 4, 6, '2026-04-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(191, 4, 6, '2026-04-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(192, 4, 6, '2026-04-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(193, 4, 6, '2026-05-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(194, 4, 6, '2026-05-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(195, 4, 6, '2026-05-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(196, 4, 6, '2026-05-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(197, 4, 6, '2026-06-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(198, 4, 6, '2026-06-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(199, 4, 6, '2026-06-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(200, 4, 6, '2026-06-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(201, 4, 6, '2026-07-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(202, 4, 6, '2026-07-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(203, 4, 6, '2026-07-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(204, 4, 6, '2026-07-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(205, 4, 6, '2026-07-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(206, 4, 6, '2026-08-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(207, 4, 6, '2026-08-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(208, 4, 6, '2026-08-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(209, 4, 6, '2026-08-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(210, 4, 6, '2026-09-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(211, 4, 6, '2026-09-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(212, 4, 6, '2026-09-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(213, 4, 6, '2026-09-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(214, 4, 6, '2026-09-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(215, 4, 6, '2026-10-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(216, 4, 6, '2026-10-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(217, 4, 6, '2026-10-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(218, 4, 6, '2026-10-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(219, 4, 6, '2026-11-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(220, 4, 6, '2026-11-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(221, 4, 6, '2026-11-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(222, 4, 6, '2026-11-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(223, 4, 6, '2026-12-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(224, 4, 6, '2026-12-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(225, 4, 6, '2026-12-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(226, 4, 6, '2026-12-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(227, 4, 6, '2026-12-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Wed 8-10', 1, '2026-04-14 16:02:09'),
(228, 5, 6, '2026-04-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(229, 5, 6, '2026-04-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(230, 5, 6, '2026-04-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(231, 5, 6, '2026-05-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(232, 5, 6, '2026-05-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(233, 5, 6, '2026-05-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(234, 5, 6, '2026-05-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(235, 5, 6, '2026-06-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(236, 5, 6, '2026-06-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(237, 5, 6, '2026-06-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(238, 5, 6, '2026-06-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(239, 5, 6, '2026-07-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(240, 5, 6, '2026-07-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(241, 5, 6, '2026-07-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(242, 5, 6, '2026-07-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(243, 5, 6, '2026-07-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(244, 5, 6, '2026-08-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(245, 5, 6, '2026-08-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(246, 5, 6, '2026-08-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(247, 5, 6, '2026-08-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(248, 5, 6, '2026-09-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(249, 5, 6, '2026-09-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(250, 5, 6, '2026-09-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(251, 5, 6, '2026-09-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(252, 5, 6, '2026-10-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(253, 5, 6, '2026-10-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(254, 5, 6, '2026-10-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(255, 5, 6, '2026-10-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(256, 5, 6, '2026-10-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(257, 5, 6, '2026-11-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(258, 5, 6, '2026-11-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(259, 5, 6, '2026-11-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(260, 5, 6, '2026-11-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(261, 5, 6, '2026-12-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(262, 5, 6, '2026-12-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(263, 5, 6, '2026-12-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(264, 5, 6, '2026-12-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(265, 5, 6, '2026-12-31', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Thu 8-10', 1, '2026-04-14 16:02:09'),
(266, 6, 6, '2026-04-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(267, 6, 6, '2026-04-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(268, 6, 6, '2026-05-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(269, 6, 6, '2026-05-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(270, 6, 6, '2026-05-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(271, 6, 6, '2026-05-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(272, 6, 6, '2026-05-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(273, 6, 6, '2026-06-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(274, 6, 6, '2026-06-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(275, 6, 6, '2026-06-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(276, 6, 6, '2026-06-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(277, 6, 6, '2026-07-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(278, 6, 6, '2026-07-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(279, 6, 6, '2026-07-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(280, 6, 6, '2026-07-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(281, 6, 6, '2026-07-31', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(282, 6, 6, '2026-08-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(283, 6, 6, '2026-08-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(284, 6, 6, '2026-08-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(285, 6, 6, '2026-08-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(286, 6, 6, '2026-09-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(287, 6, 6, '2026-09-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(288, 6, 6, '2026-09-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(289, 6, 6, '2026-09-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(290, 6, 6, '2026-10-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(291, 6, 6, '2026-10-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(292, 6, 6, '2026-10-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(293, 6, 6, '2026-10-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(294, 6, 6, '2026-10-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(295, 6, 6, '2026-11-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(296, 6, 6, '2026-11-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(297, 6, 6, '2026-11-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(298, 6, 6, '2026-11-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(299, 6, 6, '2026-12-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(300, 6, 6, '2026-12-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(301, 6, 6, '2026-12-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(302, 6, 6, '2026-12-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Fri 8-10', 1, '2026-04-14 16:02:09'),
(303, 7, 6, '2026-04-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(304, 7, 6, '2026-04-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(305, 7, 6, '2026-05-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(306, 7, 6, '2026-05-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(307, 7, 6, '2026-05-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(308, 7, 6, '2026-05-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(309, 7, 6, '2026-05-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(310, 7, 6, '2026-06-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(311, 7, 6, '2026-06-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(312, 7, 6, '2026-06-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(313, 7, 6, '2026-06-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(314, 7, 6, '2026-07-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(315, 7, 6, '2026-07-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(316, 7, 6, '2026-07-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(317, 7, 6, '2026-07-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(318, 7, 6, '2026-08-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(319, 7, 6, '2026-08-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(320, 7, 6, '2026-08-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(321, 7, 6, '2026-08-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(322, 7, 6, '2026-08-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(323, 7, 6, '2026-09-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(324, 7, 6, '2026-09-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(325, 7, 6, '2026-09-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(326, 7, 6, '2026-09-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(327, 7, 6, '2026-10-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(328, 7, 6, '2026-10-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(329, 7, 6, '2026-10-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(330, 7, 6, '2026-10-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(331, 7, 6, '2026-10-31', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(332, 7, 6, '2026-11-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(333, 7, 6, '2026-11-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(334, 7, 6, '2026-11-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(335, 7, 6, '2026-11-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(336, 7, 6, '2026-12-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(337, 7, 6, '2026-12-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(338, 7, 6, '2026-12-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(339, 7, 6, '2026-12-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sat 8-10', 1, '2026-04-14 16:02:09'),
(340, 8, 6, '2026-04-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(341, 8, 6, '2026-04-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(342, 8, 6, '2026-05-03', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(343, 8, 6, '2026-05-10', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(344, 8, 6, '2026-05-17', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(345, 8, 6, '2026-05-24', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(346, 8, 6, '2026-05-31', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(347, 8, 6, '2026-06-07', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(348, 8, 6, '2026-06-14', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(349, 8, 6, '2026-06-21', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(350, 8, 6, '2026-06-28', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(351, 8, 6, '2026-07-05', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(352, 8, 6, '2026-07-12', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(353, 8, 6, '2026-07-19', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(354, 8, 6, '2026-07-26', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(355, 8, 6, '2026-08-02', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(356, 8, 6, '2026-08-09', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(357, 8, 6, '2026-08-16', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(358, 8, 6, '2026-08-23', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(359, 8, 6, '2026-08-30', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(360, 8, 6, '2026-09-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(361, 8, 6, '2026-09-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(362, 8, 6, '2026-09-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(363, 8, 6, '2026-09-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(364, 8, 6, '2026-10-04', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(365, 8, 6, '2026-10-11', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(366, 8, 6, '2026-10-18', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(367, 8, 6, '2026-10-25', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(368, 8, 6, '2026-11-01', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(369, 8, 6, '2026-11-08', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(370, 8, 6, '2026-11-15', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(371, 8, 6, '2026-11-22', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(372, 8, 6, '2026-11-29', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(373, 8, 6, '2026-12-06', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(374, 8, 6, '2026-12-13', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(375, 8, 6, '2026-12-20', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(376, 8, 6, '2026-12-27', 1, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 1 Sun 8-10', 1, '2026-04-14 16:02:09'),
(377, 9, 6, '2026-04-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(378, 9, 6, '2026-04-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(379, 9, 6, '2026-04-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(380, 9, 6, '2026-05-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(381, 9, 6, '2026-05-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(382, 9, 6, '2026-05-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(383, 9, 6, '2026-05-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(384, 9, 6, '2026-06-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(385, 9, 6, '2026-06-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(386, 9, 6, '2026-06-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(387, 9, 6, '2026-06-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(388, 9, 6, '2026-06-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(389, 9, 6, '2026-07-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(390, 9, 6, '2026-07-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(391, 9, 6, '2026-07-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(392, 9, 6, '2026-07-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(393, 9, 6, '2026-08-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(394, 9, 6, '2026-08-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(395, 9, 6, '2026-08-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(396, 9, 6, '2026-08-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(397, 9, 6, '2026-08-31', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(398, 9, 6, '2026-09-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(399, 9, 6, '2026-09-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(400, 9, 6, '2026-09-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(401, 9, 6, '2026-09-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(402, 9, 6, '2026-10-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(403, 9, 6, '2026-10-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(404, 9, 6, '2026-10-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(405, 9, 6, '2026-10-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(406, 9, 6, '2026-11-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(407, 9, 6, '2026-11-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(408, 9, 6, '2026-11-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(409, 9, 6, '2026-11-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(410, 9, 6, '2026-11-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(411, 9, 6, '2026-12-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(412, 9, 6, '2026-12-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(413, 9, 6, '2026-12-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(414, 9, 6, '2026-12-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Mon 8-10', 1, '2026-04-14 16:02:09'),
(415, 10, 6, '2026-04-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(416, 10, 6, '2026-04-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(417, 10, 6, '2026-04-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(418, 10, 6, '2026-05-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(419, 10, 6, '2026-05-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(420, 10, 6, '2026-05-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(421, 10, 6, '2026-05-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(422, 10, 6, '2026-06-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(423, 10, 6, '2026-06-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(424, 10, 6, '2026-06-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(425, 10, 6, '2026-06-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(426, 10, 6, '2026-06-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(427, 10, 6, '2026-07-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(428, 10, 6, '2026-07-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(429, 10, 6, '2026-07-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(430, 10, 6, '2026-07-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(431, 10, 6, '2026-08-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09');
INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(432, 10, 6, '2026-08-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(433, 10, 6, '2026-08-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(434, 10, 6, '2026-08-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(435, 10, 6, '2026-09-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(436, 10, 6, '2026-09-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(437, 10, 6, '2026-09-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(438, 10, 6, '2026-09-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(439, 10, 6, '2026-09-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(440, 10, 6, '2026-10-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(441, 10, 6, '2026-10-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(442, 10, 6, '2026-10-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(443, 10, 6, '2026-10-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(444, 10, 6, '2026-11-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(445, 10, 6, '2026-11-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(446, 10, 6, '2026-11-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(447, 10, 6, '2026-11-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(448, 10, 6, '2026-12-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(449, 10, 6, '2026-12-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(450, 10, 6, '2026-12-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(451, 10, 6, '2026-12-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(452, 10, 6, '2026-12-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Tue 8-10', 1, '2026-04-14 16:02:09'),
(453, 11, 6, '2026-04-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(454, 11, 6, '2026-04-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(455, 11, 6, '2026-04-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(456, 11, 6, '2026-05-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(457, 11, 6, '2026-05-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(458, 11, 6, '2026-05-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(459, 11, 6, '2026-05-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(460, 11, 6, '2026-06-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(461, 11, 6, '2026-06-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(462, 11, 6, '2026-06-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(463, 11, 6, '2026-06-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(464, 11, 6, '2026-07-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(465, 11, 6, '2026-07-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(466, 11, 6, '2026-07-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(467, 11, 6, '2026-07-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(468, 11, 6, '2026-07-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(469, 11, 6, '2026-08-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(470, 11, 6, '2026-08-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(471, 11, 6, '2026-08-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(472, 11, 6, '2026-08-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(473, 11, 6, '2026-09-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(474, 11, 6, '2026-09-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(475, 11, 6, '2026-09-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(476, 11, 6, '2026-09-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(477, 11, 6, '2026-09-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(478, 11, 6, '2026-10-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(479, 11, 6, '2026-10-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(480, 11, 6, '2026-10-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(481, 11, 6, '2026-10-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(482, 11, 6, '2026-11-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(483, 11, 6, '2026-11-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(484, 11, 6, '2026-11-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(485, 11, 6, '2026-11-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(486, 11, 6, '2026-12-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(487, 11, 6, '2026-12-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(488, 11, 6, '2026-12-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(489, 11, 6, '2026-12-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(490, 11, 6, '2026-12-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Wed 8-10', 1, '2026-04-14 16:02:09'),
(491, 12, 6, '2026-04-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(492, 12, 6, '2026-04-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(493, 12, 6, '2026-04-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(494, 12, 6, '2026-05-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(495, 12, 6, '2026-05-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(496, 12, 6, '2026-05-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(497, 12, 6, '2026-05-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(498, 12, 6, '2026-06-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(499, 12, 6, '2026-06-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(500, 12, 6, '2026-06-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(501, 12, 6, '2026-06-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(502, 12, 6, '2026-07-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(503, 12, 6, '2026-07-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(504, 12, 6, '2026-07-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(505, 12, 6, '2026-07-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(506, 12, 6, '2026-07-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(507, 12, 6, '2026-08-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(508, 12, 6, '2026-08-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(509, 12, 6, '2026-08-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(510, 12, 6, '2026-08-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(511, 12, 6, '2026-09-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(512, 12, 6, '2026-09-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(513, 12, 6, '2026-09-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(514, 12, 6, '2026-09-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(515, 12, 6, '2026-10-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(516, 12, 6, '2026-10-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(517, 12, 6, '2026-10-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(518, 12, 6, '2026-10-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(519, 12, 6, '2026-10-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(520, 12, 6, '2026-11-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(521, 12, 6, '2026-11-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(522, 12, 6, '2026-11-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(523, 12, 6, '2026-11-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(524, 12, 6, '2026-12-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(525, 12, 6, '2026-12-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(526, 12, 6, '2026-12-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(527, 12, 6, '2026-12-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(528, 12, 6, '2026-12-31', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Thu 8-10', 1, '2026-04-14 16:02:09'),
(529, 13, 6, '2026-04-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(530, 13, 6, '2026-04-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(531, 13, 6, '2026-05-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(532, 13, 6, '2026-05-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(533, 13, 6, '2026-05-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(534, 13, 6, '2026-05-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(535, 13, 6, '2026-05-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(536, 13, 6, '2026-06-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(537, 13, 6, '2026-06-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(538, 13, 6, '2026-06-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(539, 13, 6, '2026-06-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(540, 13, 6, '2026-07-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(541, 13, 6, '2026-07-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(542, 13, 6, '2026-07-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(543, 13, 6, '2026-07-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(544, 13, 6, '2026-07-31', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(545, 13, 6, '2026-08-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(546, 13, 6, '2026-08-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(547, 13, 6, '2026-08-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(548, 13, 6, '2026-08-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(549, 13, 6, '2026-09-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(550, 13, 6, '2026-09-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(551, 13, 6, '2026-09-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(552, 13, 6, '2026-09-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(553, 13, 6, '2026-10-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(554, 13, 6, '2026-10-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(555, 13, 6, '2026-10-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(556, 13, 6, '2026-10-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(557, 13, 6, '2026-10-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(558, 13, 6, '2026-11-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(559, 13, 6, '2026-11-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(560, 13, 6, '2026-11-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(561, 13, 6, '2026-11-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(562, 13, 6, '2026-12-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(563, 13, 6, '2026-12-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(564, 13, 6, '2026-12-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(565, 13, 6, '2026-12-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Fri 8-10', 1, '2026-04-14 16:02:09'),
(566, 14, 6, '2026-04-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(567, 14, 6, '2026-04-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(568, 14, 6, '2026-05-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(569, 14, 6, '2026-05-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(570, 14, 6, '2026-05-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(571, 14, 6, '2026-05-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(572, 14, 6, '2026-05-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(573, 14, 6, '2026-06-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(574, 14, 6, '2026-06-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(575, 14, 6, '2026-06-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(576, 14, 6, '2026-06-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(577, 14, 6, '2026-07-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(578, 14, 6, '2026-07-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(579, 14, 6, '2026-07-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(580, 14, 6, '2026-07-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(581, 14, 6, '2026-08-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(582, 14, 6, '2026-08-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(583, 14, 6, '2026-08-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(584, 14, 6, '2026-08-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(585, 14, 6, '2026-08-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(586, 14, 6, '2026-09-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(587, 14, 6, '2026-09-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(588, 14, 6, '2026-09-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(589, 14, 6, '2026-09-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(590, 14, 6, '2026-10-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(591, 14, 6, '2026-10-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(592, 14, 6, '2026-10-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(593, 14, 6, '2026-10-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(594, 14, 6, '2026-10-31', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(595, 14, 6, '2026-11-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(596, 14, 6, '2026-11-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(597, 14, 6, '2026-11-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(598, 14, 6, '2026-11-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(599, 14, 6, '2026-12-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(600, 14, 6, '2026-12-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(601, 14, 6, '2026-12-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(602, 14, 6, '2026-12-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sat 8-10', 1, '2026-04-14 16:02:09'),
(603, 15, 6, '2026-04-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(604, 15, 6, '2026-04-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(605, 15, 6, '2026-05-03', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(606, 15, 6, '2026-05-10', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(607, 15, 6, '2026-05-17', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(608, 15, 6, '2026-05-24', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(609, 15, 6, '2026-05-31', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(610, 15, 6, '2026-06-07', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(611, 15, 6, '2026-06-14', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(612, 15, 6, '2026-06-21', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(613, 15, 6, '2026-06-28', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(614, 15, 6, '2026-07-05', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(615, 15, 6, '2026-07-12', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(616, 15, 6, '2026-07-19', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(617, 15, 6, '2026-07-26', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(618, 15, 6, '2026-08-02', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(619, 15, 6, '2026-08-09', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(620, 15, 6, '2026-08-16', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(621, 15, 6, '2026-08-23', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(622, 15, 6, '2026-08-30', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(623, 15, 6, '2026-09-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(624, 15, 6, '2026-09-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(625, 15, 6, '2026-09-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(626, 15, 6, '2026-09-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(627, 15, 6, '2026-10-04', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(628, 15, 6, '2026-10-11', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(629, 15, 6, '2026-10-18', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(630, 15, 6, '2026-10-25', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(631, 15, 6, '2026-11-01', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(632, 15, 6, '2026-11-08', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(633, 15, 6, '2026-11-15', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(634, 15, 6, '2026-11-22', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(635, 15, 6, '2026-11-29', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(636, 15, 6, '2026-12-06', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(637, 15, 6, '2026-12-13', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(638, 15, 6, '2026-12-20', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(639, 15, 6, '2026-12-27', 2, NULL, 'scheduled', NULL, 10, 'Generated from template: Practice Net 2 Sun 8-10', 1, '2026-04-14 16:02:09'),
(640, 16, 6, '2026-04-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(641, 16, 6, '2026-04-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(642, 16, 6, '2026-04-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(643, 16, 6, '2026-05-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(644, 16, 6, '2026-05-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(645, 16, 6, '2026-05-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(646, 16, 6, '2026-05-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(647, 16, 6, '2026-06-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(648, 16, 6, '2026-06-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(649, 16, 6, '2026-06-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(650, 16, 6, '2026-06-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(651, 16, 6, '2026-06-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(652, 16, 6, '2026-07-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(653, 16, 6, '2026-07-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(654, 16, 6, '2026-07-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(655, 16, 6, '2026-07-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(656, 16, 6, '2026-08-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(657, 16, 6, '2026-08-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(658, 16, 6, '2026-08-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(659, 16, 6, '2026-08-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(660, 16, 6, '2026-08-31', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(661, 16, 6, '2026-09-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(662, 16, 6, '2026-09-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(663, 16, 6, '2026-09-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(664, 16, 6, '2026-09-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(665, 16, 6, '2026-10-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(666, 16, 6, '2026-10-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(667, 16, 6, '2026-10-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(668, 16, 6, '2026-10-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(669, 16, 6, '2026-11-02', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(670, 16, 6, '2026-11-09', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(671, 16, 6, '2026-11-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(672, 16, 6, '2026-11-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(673, 16, 6, '2026-11-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(674, 16, 6, '2026-12-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(675, 16, 6, '2026-12-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(676, 16, 6, '2026-12-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(677, 16, 6, '2026-12-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Mon 8-10', 1, '2026-04-14 16:02:09'),
(678, 17, 6, '2026-04-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(679, 17, 6, '2026-04-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(680, 17, 6, '2026-04-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(681, 17, 6, '2026-05-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(682, 17, 6, '2026-05-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(683, 17, 6, '2026-05-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(684, 17, 6, '2026-05-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(685, 17, 6, '2026-06-02', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(686, 17, 6, '2026-06-09', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(687, 17, 6, '2026-06-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(688, 17, 6, '2026-06-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(689, 17, 6, '2026-06-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(690, 17, 6, '2026-07-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(691, 17, 6, '2026-07-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(692, 17, 6, '2026-07-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(693, 17, 6, '2026-07-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(694, 17, 6, '2026-08-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(695, 17, 6, '2026-08-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(696, 17, 6, '2026-08-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(697, 17, 6, '2026-08-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(698, 17, 6, '2026-09-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(699, 17, 6, '2026-09-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(700, 17, 6, '2026-09-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(701, 17, 6, '2026-09-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(702, 17, 6, '2026-09-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(703, 17, 6, '2026-10-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(704, 17, 6, '2026-10-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(705, 17, 6, '2026-10-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(706, 17, 6, '2026-10-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(707, 17, 6, '2026-11-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(708, 17, 6, '2026-11-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(709, 17, 6, '2026-11-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(710, 17, 6, '2026-11-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(711, 17, 6, '2026-12-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(712, 17, 6, '2026-12-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(713, 17, 6, '2026-12-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(714, 17, 6, '2026-12-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(715, 17, 6, '2026-12-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Tue 8-10', 1, '2026-04-14 16:02:09'),
(716, 19, 6, '2026-04-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(717, 19, 6, '2026-04-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(718, 19, 6, '2026-04-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(719, 19, 6, '2026-05-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(720, 19, 6, '2026-05-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(721, 19, 6, '2026-05-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(722, 19, 6, '2026-05-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(723, 19, 6, '2026-06-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(724, 19, 6, '2026-06-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(725, 19, 6, '2026-06-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(726, 19, 6, '2026-06-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(727, 19, 6, '2026-07-02', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(728, 19, 6, '2026-07-09', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(729, 19, 6, '2026-07-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(730, 19, 6, '2026-07-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(731, 19, 6, '2026-07-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(732, 19, 6, '2026-08-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(733, 19, 6, '2026-08-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(734, 19, 6, '2026-08-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(735, 19, 6, '2026-08-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(736, 19, 6, '2026-09-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(737, 19, 6, '2026-09-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(738, 19, 6, '2026-09-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(739, 19, 6, '2026-09-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(740, 19, 6, '2026-10-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(741, 19, 6, '2026-10-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(742, 19, 6, '2026-10-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(743, 19, 6, '2026-10-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(744, 19, 6, '2026-10-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(745, 19, 6, '2026-11-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(746, 19, 6, '2026-11-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(747, 19, 6, '2026-11-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(748, 19, 6, '2026-11-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(749, 19, 6, '2026-12-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(750, 19, 6, '2026-12-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(751, 19, 6, '2026-12-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(752, 19, 6, '2026-12-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(753, 19, 6, '2026-12-31', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Thu 8-10', 1, '2026-04-14 16:02:09'),
(754, 20, 6, '2026-04-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(755, 20, 6, '2026-04-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(756, 20, 6, '2026-05-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(757, 20, 6, '2026-05-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(758, 20, 6, '2026-05-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(759, 20, 6, '2026-05-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(760, 20, 6, '2026-05-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(761, 20, 6, '2026-06-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(762, 20, 6, '2026-06-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(763, 20, 6, '2026-06-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(764, 20, 6, '2026-06-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(765, 20, 6, '2026-07-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(766, 20, 6, '2026-07-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(767, 20, 6, '2026-07-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(768, 20, 6, '2026-07-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(769, 20, 6, '2026-07-31', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(770, 20, 6, '2026-08-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(771, 20, 6, '2026-08-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(772, 20, 6, '2026-08-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(773, 20, 6, '2026-08-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(774, 20, 6, '2026-09-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(775, 20, 6, '2026-09-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(776, 20, 6, '2026-09-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(777, 20, 6, '2026-09-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(778, 20, 6, '2026-10-02', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(779, 20, 6, '2026-10-09', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(780, 20, 6, '2026-10-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(781, 20, 6, '2026-10-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(782, 20, 6, '2026-10-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(783, 20, 6, '2026-11-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(784, 20, 6, '2026-11-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(785, 20, 6, '2026-11-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(786, 20, 6, '2026-11-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(787, 20, 6, '2026-12-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(788, 20, 6, '2026-12-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(789, 20, 6, '2026-12-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(790, 20, 6, '2026-12-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Fri 8-10', 1, '2026-04-14 16:02:09'),
(791, 21, 6, '2026-04-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(792, 21, 6, '2026-04-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(793, 21, 6, '2026-05-02', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(794, 21, 6, '2026-05-09', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(795, 21, 6, '2026-05-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09');
INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(796, 21, 6, '2026-05-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(797, 21, 6, '2026-05-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(798, 21, 6, '2026-06-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(799, 21, 6, '2026-06-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(800, 21, 6, '2026-06-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(801, 21, 6, '2026-06-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(802, 21, 6, '2026-07-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(803, 21, 6, '2026-07-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(804, 21, 6, '2026-07-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(805, 21, 6, '2026-07-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(806, 21, 6, '2026-08-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(807, 21, 6, '2026-08-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(808, 21, 6, '2026-08-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(809, 21, 6, '2026-08-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(810, 21, 6, '2026-08-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(811, 21, 6, '2026-09-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(812, 21, 6, '2026-09-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(813, 21, 6, '2026-09-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(814, 21, 6, '2026-09-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(815, 21, 6, '2026-10-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(816, 21, 6, '2026-10-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(817, 21, 6, '2026-10-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(818, 21, 6, '2026-10-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(819, 21, 6, '2026-10-31', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(820, 21, 6, '2026-11-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(821, 21, 6, '2026-11-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(822, 21, 6, '2026-11-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(823, 21, 6, '2026-11-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(824, 21, 6, '2026-12-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(825, 21, 6, '2026-12-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(826, 21, 6, '2026-12-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(827, 21, 6, '2026-12-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sat 8-10', 1, '2026-04-14 16:02:09'),
(828, 22, 6, '2026-04-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(829, 22, 6, '2026-04-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(830, 22, 6, '2026-05-03', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(831, 22, 6, '2026-05-10', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(832, 22, 6, '2026-05-17', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(833, 22, 6, '2026-05-24', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(834, 22, 6, '2026-05-31', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(835, 22, 6, '2026-06-07', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(836, 22, 6, '2026-06-14', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(837, 22, 6, '2026-06-21', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(838, 22, 6, '2026-06-28', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(839, 22, 6, '2026-07-05', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(840, 22, 6, '2026-07-12', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(841, 22, 6, '2026-07-19', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(842, 22, 6, '2026-07-26', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(843, 22, 6, '2026-08-02', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(844, 22, 6, '2026-08-09', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(845, 22, 6, '2026-08-16', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(846, 22, 6, '2026-08-23', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(847, 22, 6, '2026-08-30', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(848, 22, 6, '2026-09-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(849, 22, 6, '2026-09-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(850, 22, 6, '2026-09-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(851, 22, 6, '2026-09-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(852, 22, 6, '2026-10-04', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(853, 22, 6, '2026-10-11', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(854, 22, 6, '2026-10-18', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(855, 22, 6, '2026-10-25', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(856, 22, 6, '2026-11-01', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(857, 22, 6, '2026-11-08', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(858, 22, 6, '2026-11-15', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(859, 22, 6, '2026-11-22', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(860, 22, 6, '2026-11-29', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(861, 22, 6, '2026-12-06', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(862, 22, 6, '2026-12-13', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(863, 22, 6, '2026-12-20', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(864, 22, 6, '2026-12-27', 3, NULL, 'scheduled', NULL, 10, 'Generated from template: Bowling Machine Sun 8-10', 1, '2026-04-14 16:02:09'),
(903, 26, 5, '2026-04-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(904, 26, 5, '2026-04-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(905, 26, 5, '2026-04-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(906, 26, 5, '2026-05-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(907, 26, 5, '2026-05-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(908, 26, 5, '2026-05-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(909, 26, 5, '2026-05-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(910, 26, 5, '2026-06-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(911, 26, 5, '2026-06-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(912, 26, 5, '2026-06-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(913, 26, 5, '2026-06-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(914, 26, 5, '2026-06-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(915, 26, 5, '2026-07-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(916, 26, 5, '2026-07-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(917, 26, 5, '2026-07-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(918, 26, 5, '2026-07-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(919, 26, 5, '2026-08-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(920, 26, 5, '2026-08-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(921, 26, 5, '2026-08-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(922, 26, 5, '2026-08-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(923, 26, 5, '2026-08-31', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(924, 26, 5, '2026-09-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(925, 26, 5, '2026-09-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(926, 26, 5, '2026-09-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(927, 26, 5, '2026-09-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(928, 26, 5, '2026-10-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(929, 26, 5, '2026-10-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(930, 26, 5, '2026-10-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(931, 26, 5, '2026-10-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(932, 26, 5, '2026-11-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(933, 26, 5, '2026-11-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(934, 26, 5, '2026-11-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(935, 26, 5, '2026-11-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(936, 26, 5, '2026-11-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(937, 26, 5, '2026-12-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(938, 26, 5, '2026-12-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(939, 26, 5, '2026-12-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(940, 26, 5, '2026-12-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Monday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(941, 29, 5, '2026-04-13', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(942, 29, 5, '2026-04-20', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(943, 29, 5, '2026-04-27', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(944, 29, 5, '2026-05-04', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(945, 29, 5, '2026-05-11', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(946, 29, 5, '2026-05-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(947, 29, 5, '2026-05-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(948, 29, 5, '2026-06-01', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(949, 29, 5, '2026-06-08', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(950, 29, 5, '2026-06-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(951, 29, 5, '2026-06-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(952, 29, 5, '2026-06-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(953, 29, 5, '2026-07-06', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(954, 29, 5, '2026-07-13', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(955, 29, 5, '2026-07-20', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(956, 29, 5, '2026-07-27', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(957, 29, 5, '2026-08-03', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(958, 29, 5, '2026-08-10', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(959, 29, 5, '2026-08-17', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(960, 29, 5, '2026-08-24', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(961, 29, 5, '2026-08-31', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(962, 29, 5, '2026-09-07', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(963, 29, 5, '2026-09-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(964, 29, 5, '2026-09-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(965, 29, 5, '2026-09-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(966, 29, 5, '2026-10-05', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(967, 29, 5, '2026-10-12', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(968, 29, 5, '2026-10-19', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(969, 29, 5, '2026-10-26', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(970, 29, 5, '2026-11-02', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(971, 29, 5, '2026-11-09', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(972, 29, 5, '2026-11-16', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(973, 29, 5, '2026-11-23', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(974, 29, 5, '2026-11-30', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(975, 29, 5, '2026-12-07', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(976, 29, 5, '2026-12-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(977, 29, 5, '2026-12-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(978, 29, 5, '2026-12-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Monday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1017, 33, 5, '2026-04-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1018, 33, 5, '2026-04-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1019, 33, 5, '2026-04-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1020, 33, 5, '2026-05-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1021, 33, 5, '2026-05-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1022, 33, 5, '2026-05-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1023, 33, 5, '2026-05-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1024, 33, 5, '2026-06-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1025, 33, 5, '2026-06-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1026, 33, 5, '2026-06-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1027, 33, 5, '2026-06-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1028, 33, 5, '2026-06-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1029, 33, 5, '2026-07-07', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1030, 33, 5, '2026-07-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1031, 33, 5, '2026-07-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1032, 33, 5, '2026-07-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1033, 33, 5, '2026-08-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1034, 33, 5, '2026-08-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1035, 33, 5, '2026-08-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1036, 33, 5, '2026-08-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1037, 33, 5, '2026-09-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1038, 33, 5, '2026-09-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1039, 33, 5, '2026-09-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1040, 33, 5, '2026-09-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1041, 33, 5, '2026-09-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1042, 33, 5, '2026-10-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1043, 33, 5, '2026-10-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1044, 33, 5, '2026-10-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1045, 33, 5, '2026-10-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1046, 33, 5, '2026-11-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1047, 33, 5, '2026-11-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1048, 33, 5, '2026-11-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1049, 33, 5, '2026-11-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1050, 33, 5, '2026-12-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1051, 33, 5, '2026-12-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1052, 33, 5, '2026-12-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1053, 33, 5, '2026-12-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1054, 33, 5, '2026-12-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1055, 36, 5, '2026-04-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1056, 36, 5, '2026-04-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1057, 36, 5, '2026-04-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1058, 36, 5, '2026-05-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1059, 36, 5, '2026-05-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1060, 36, 5, '2026-05-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1061, 36, 5, '2026-05-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1062, 36, 5, '2026-06-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1063, 36, 5, '2026-06-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1064, 36, 5, '2026-06-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1065, 36, 5, '2026-06-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1066, 36, 5, '2026-06-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1067, 36, 5, '2026-07-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1068, 36, 5, '2026-07-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1069, 36, 5, '2026-07-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1070, 36, 5, '2026-07-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1071, 36, 5, '2026-08-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1072, 36, 5, '2026-08-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1073, 36, 5, '2026-08-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1074, 36, 5, '2026-08-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1075, 36, 5, '2026-09-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1076, 36, 5, '2026-09-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1077, 36, 5, '2026-09-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1078, 36, 5, '2026-09-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1079, 36, 5, '2026-09-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1080, 36, 5, '2026-10-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1081, 36, 5, '2026-10-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1082, 36, 5, '2026-10-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1083, 36, 5, '2026-10-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1084, 36, 5, '2026-11-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1085, 36, 5, '2026-11-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1086, 36, 5, '2026-11-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1087, 36, 5, '2026-11-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1088, 36, 5, '2026-12-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1089, 36, 5, '2026-12-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1090, 36, 5, '2026-12-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1091, 36, 5, '2026-12-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1092, 36, 5, '2026-12-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Tuesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1093, 39, 5, '2026-04-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1094, 39, 5, '2026-04-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1095, 39, 5, '2026-04-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1096, 39, 5, '2026-05-05', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1097, 39, 5, '2026-05-12', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1098, 39, 5, '2026-05-19', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1099, 39, 5, '2026-05-26', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1100, 39, 5, '2026-06-02', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1101, 39, 5, '2026-06-09', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1102, 39, 5, '2026-06-16', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1103, 39, 5, '2026-06-23', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1104, 39, 5, '2026-06-30', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1105, 39, 5, '2026-07-07', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1106, 39, 5, '2026-07-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1107, 39, 5, '2026-07-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1108, 39, 5, '2026-07-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1109, 39, 5, '2026-08-04', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1110, 39, 5, '2026-08-11', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1111, 39, 5, '2026-08-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1112, 39, 5, '2026-08-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1113, 39, 5, '2026-09-01', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1114, 39, 5, '2026-09-08', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1115, 39, 5, '2026-09-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1116, 39, 5, '2026-09-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1117, 39, 5, '2026-09-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1118, 39, 5, '2026-10-06', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1119, 39, 5, '2026-10-13', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1120, 39, 5, '2026-10-20', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1121, 39, 5, '2026-10-27', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1122, 39, 5, '2026-11-03', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1123, 39, 5, '2026-11-10', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1124, 39, 5, '2026-11-17', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1125, 39, 5, '2026-11-24', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1126, 39, 5, '2026-12-01', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1127, 39, 5, '2026-12-08', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1128, 39, 5, '2026-12-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1129, 39, 5, '2026-12-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1130, 39, 5, '2026-12-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Tuesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1169, 43, 5, '2026-04-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1170, 43, 5, '2026-04-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1171, 43, 5, '2026-04-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1172, 43, 5, '2026-05-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1173, 43, 5, '2026-05-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1174, 43, 5, '2026-05-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1175, 43, 5, '2026-05-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1176, 43, 5, '2026-06-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1177, 43, 5, '2026-06-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1178, 43, 5, '2026-06-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1179, 43, 5, '2026-06-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1180, 43, 5, '2026-07-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1181, 43, 5, '2026-07-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1182, 43, 5, '2026-07-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1183, 43, 5, '2026-07-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1184, 43, 5, '2026-07-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1185, 43, 5, '2026-08-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1186, 43, 5, '2026-08-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1187, 43, 5, '2026-08-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1188, 43, 5, '2026-08-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1189, 43, 5, '2026-09-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1190, 43, 5, '2026-09-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1191, 43, 5, '2026-09-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1192, 43, 5, '2026-09-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1193, 43, 5, '2026-09-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1194, 43, 5, '2026-10-07', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1195, 43, 5, '2026-10-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1196, 43, 5, '2026-10-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1197, 43, 5, '2026-10-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1198, 43, 5, '2026-11-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1199, 43, 5, '2026-11-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1200, 43, 5, '2026-11-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1201, 43, 5, '2026-11-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1202, 43, 5, '2026-12-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1203, 43, 5, '2026-12-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1204, 43, 5, '2026-12-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1205, 43, 5, '2026-12-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1206, 43, 5, '2026-12-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1207, 46, 5, '2026-04-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1208, 46, 5, '2026-04-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1209, 46, 5, '2026-04-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1210, 46, 5, '2026-05-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1211, 46, 5, '2026-05-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1212, 46, 5, '2026-05-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1213, 46, 5, '2026-05-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1214, 46, 5, '2026-06-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1215, 46, 5, '2026-06-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1216, 46, 5, '2026-06-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1217, 46, 5, '2026-06-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1218, 46, 5, '2026-07-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1219, 46, 5, '2026-07-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1220, 46, 5, '2026-07-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1221, 46, 5, '2026-07-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1222, 46, 5, '2026-07-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1223, 46, 5, '2026-08-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1224, 46, 5, '2026-08-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1225, 46, 5, '2026-08-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1226, 46, 5, '2026-08-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1227, 46, 5, '2026-09-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1228, 46, 5, '2026-09-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1229, 46, 5, '2026-09-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1230, 46, 5, '2026-09-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1231, 46, 5, '2026-09-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1232, 46, 5, '2026-10-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1233, 46, 5, '2026-10-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1234, 46, 5, '2026-10-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1235, 46, 5, '2026-10-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1236, 46, 5, '2026-11-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1237, 46, 5, '2026-11-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1238, 46, 5, '2026-11-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1239, 46, 5, '2026-11-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09');
INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(1240, 46, 5, '2026-12-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1241, 46, 5, '2026-12-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1242, 46, 5, '2026-12-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1243, 46, 5, '2026-12-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1244, 46, 5, '2026-12-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Wednesday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1245, 49, 5, '2026-04-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1246, 49, 5, '2026-04-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1247, 49, 5, '2026-04-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1248, 49, 5, '2026-05-06', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1249, 49, 5, '2026-05-13', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1250, 49, 5, '2026-05-20', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1251, 49, 5, '2026-05-27', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1252, 49, 5, '2026-06-03', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1253, 49, 5, '2026-06-10', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1254, 49, 5, '2026-06-17', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1255, 49, 5, '2026-06-24', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1256, 49, 5, '2026-07-01', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1257, 49, 5, '2026-07-08', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1258, 49, 5, '2026-07-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1259, 49, 5, '2026-07-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1260, 49, 5, '2026-07-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1261, 49, 5, '2026-08-05', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1262, 49, 5, '2026-08-12', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1263, 49, 5, '2026-08-19', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1264, 49, 5, '2026-08-26', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1265, 49, 5, '2026-09-02', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1266, 49, 5, '2026-09-09', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1267, 49, 5, '2026-09-16', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1268, 49, 5, '2026-09-23', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1269, 49, 5, '2026-09-30', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1270, 49, 5, '2026-10-07', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1271, 49, 5, '2026-10-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1272, 49, 5, '2026-10-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1273, 49, 5, '2026-10-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1274, 49, 5, '2026-11-04', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1275, 49, 5, '2026-11-11', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1276, 49, 5, '2026-11-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1277, 49, 5, '2026-11-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1278, 49, 5, '2026-12-02', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1279, 49, 5, '2026-12-09', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1280, 49, 5, '2026-12-16', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1281, 49, 5, '2026-12-23', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1282, 49, 5, '2026-12-30', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Wednesday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1321, 53, 5, '2026-04-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1322, 53, 5, '2026-04-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1323, 53, 5, '2026-04-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1324, 53, 5, '2026-05-07', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1325, 53, 5, '2026-05-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1326, 53, 5, '2026-05-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1327, 53, 5, '2026-05-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1328, 53, 5, '2026-06-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1329, 53, 5, '2026-06-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1330, 53, 5, '2026-06-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1331, 53, 5, '2026-06-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1332, 53, 5, '2026-07-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1333, 53, 5, '2026-07-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1334, 53, 5, '2026-07-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1335, 53, 5, '2026-07-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1336, 53, 5, '2026-07-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1337, 53, 5, '2026-08-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1338, 53, 5, '2026-08-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1339, 53, 5, '2026-08-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1340, 53, 5, '2026-08-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1341, 53, 5, '2026-09-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1342, 53, 5, '2026-09-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1343, 53, 5, '2026-09-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1344, 53, 5, '2026-09-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1345, 53, 5, '2026-10-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1346, 53, 5, '2026-10-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1347, 53, 5, '2026-10-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1348, 53, 5, '2026-10-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1349, 53, 5, '2026-10-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1350, 53, 5, '2026-11-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1351, 53, 5, '2026-11-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1352, 53, 5, '2026-11-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1353, 53, 5, '2026-11-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1354, 53, 5, '2026-12-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1355, 53, 5, '2026-12-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1356, 53, 5, '2026-12-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1357, 53, 5, '2026-12-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1358, 53, 5, '2026-12-31', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1359, 56, 5, '2026-04-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1360, 56, 5, '2026-04-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1361, 56, 5, '2026-04-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1362, 56, 5, '2026-05-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1363, 56, 5, '2026-05-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1364, 56, 5, '2026-05-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1365, 56, 5, '2026-05-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1366, 56, 5, '2026-06-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1367, 56, 5, '2026-06-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1368, 56, 5, '2026-06-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1369, 56, 5, '2026-06-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1370, 56, 5, '2026-07-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1371, 56, 5, '2026-07-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1372, 56, 5, '2026-07-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1373, 56, 5, '2026-07-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1374, 56, 5, '2026-07-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1375, 56, 5, '2026-08-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1376, 56, 5, '2026-08-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1377, 56, 5, '2026-08-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1378, 56, 5, '2026-08-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1379, 56, 5, '2026-09-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1380, 56, 5, '2026-09-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1381, 56, 5, '2026-09-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1382, 56, 5, '2026-09-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1383, 56, 5, '2026-10-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1384, 56, 5, '2026-10-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1385, 56, 5, '2026-10-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1386, 56, 5, '2026-10-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1387, 56, 5, '2026-10-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1388, 56, 5, '2026-11-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1389, 56, 5, '2026-11-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1390, 56, 5, '2026-11-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1391, 56, 5, '2026-11-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1392, 56, 5, '2026-12-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1393, 56, 5, '2026-12-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1394, 56, 5, '2026-12-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1395, 56, 5, '2026-12-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1396, 56, 5, '2026-12-31', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Thursday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1473, 63, 5, '2026-04-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1474, 63, 5, '2026-04-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1475, 63, 5, '2026-05-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1476, 63, 5, '2026-05-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1477, 63, 5, '2026-05-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1478, 63, 5, '2026-05-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1479, 63, 5, '2026-05-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1480, 63, 5, '2026-06-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1481, 63, 5, '2026-06-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1482, 63, 5, '2026-06-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1483, 63, 5, '2026-06-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1484, 63, 5, '2026-07-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1485, 63, 5, '2026-07-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1486, 63, 5, '2026-07-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1487, 63, 5, '2026-07-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1488, 63, 5, '2026-07-31', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1489, 63, 5, '2026-08-07', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1490, 63, 5, '2026-08-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1491, 63, 5, '2026-08-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1492, 63, 5, '2026-08-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1493, 63, 5, '2026-09-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1494, 63, 5, '2026-09-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1495, 63, 5, '2026-09-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1496, 63, 5, '2026-09-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1497, 63, 5, '2026-10-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1498, 63, 5, '2026-10-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1499, 63, 5, '2026-10-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1500, 63, 5, '2026-10-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1501, 63, 5, '2026-10-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1502, 63, 5, '2026-11-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1503, 63, 5, '2026-11-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1504, 63, 5, '2026-11-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1505, 63, 5, '2026-11-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1506, 63, 5, '2026-12-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1507, 63, 5, '2026-12-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1508, 63, 5, '2026-12-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1509, 63, 5, '2026-12-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1510, 66, 5, '2026-04-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1511, 66, 5, '2026-04-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1512, 66, 5, '2026-05-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1513, 66, 5, '2026-05-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1514, 66, 5, '2026-05-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1515, 66, 5, '2026-05-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1516, 66, 5, '2026-05-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1517, 66, 5, '2026-06-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1518, 66, 5, '2026-06-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1519, 66, 5, '2026-06-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1520, 66, 5, '2026-06-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1521, 66, 5, '2026-07-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1522, 66, 5, '2026-07-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1523, 66, 5, '2026-07-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1524, 66, 5, '2026-07-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1525, 66, 5, '2026-07-31', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1526, 66, 5, '2026-08-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1527, 66, 5, '2026-08-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1528, 66, 5, '2026-08-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1529, 66, 5, '2026-08-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1530, 66, 5, '2026-09-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1531, 66, 5, '2026-09-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1532, 66, 5, '2026-09-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1533, 66, 5, '2026-09-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1534, 66, 5, '2026-10-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1535, 66, 5, '2026-10-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1536, 66, 5, '2026-10-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1537, 66, 5, '2026-10-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1538, 66, 5, '2026-10-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1539, 66, 5, '2026-11-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1540, 66, 5, '2026-11-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1541, 66, 5, '2026-11-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1542, 66, 5, '2026-11-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1543, 66, 5, '2026-12-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1544, 66, 5, '2026-12-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1545, 66, 5, '2026-12-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1546, 66, 5, '2026-12-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Friday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1547, 69, 5, '2026-04-17', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1548, 69, 5, '2026-04-24', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1549, 69, 5, '2026-05-01', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1550, 69, 5, '2026-05-08', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1551, 69, 5, '2026-05-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1552, 69, 5, '2026-05-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1553, 69, 5, '2026-05-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1554, 69, 5, '2026-06-05', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1555, 69, 5, '2026-06-12', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1556, 69, 5, '2026-06-19', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1557, 69, 5, '2026-06-26', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1558, 69, 5, '2026-07-03', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1559, 69, 5, '2026-07-10', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1560, 69, 5, '2026-07-17', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1561, 69, 5, '2026-07-24', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1562, 69, 5, '2026-07-31', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1563, 69, 5, '2026-08-07', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1564, 69, 5, '2026-08-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1565, 69, 5, '2026-08-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1566, 69, 5, '2026-08-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1567, 69, 5, '2026-09-04', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1568, 69, 5, '2026-09-11', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1569, 69, 5, '2026-09-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1570, 69, 5, '2026-09-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1571, 69, 5, '2026-10-02', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1572, 69, 5, '2026-10-09', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1573, 69, 5, '2026-10-16', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1574, 69, 5, '2026-10-23', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1575, 69, 5, '2026-10-30', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1576, 69, 5, '2026-11-06', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1577, 69, 5, '2026-11-13', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1578, 69, 5, '2026-11-20', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1579, 69, 5, '2026-11-27', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1580, 69, 5, '2026-12-04', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1581, 69, 5, '2026-12-11', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1582, 69, 5, '2026-12-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1583, 69, 5, '2026-12-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Friday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1621, 73, 5, '2026-04-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1622, 73, 5, '2026-04-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1623, 73, 5, '2026-05-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1624, 73, 5, '2026-05-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1625, 73, 5, '2026-05-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1626, 73, 5, '2026-05-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1627, 73, 5, '2026-05-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1628, 73, 5, '2026-06-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1629, 73, 5, '2026-06-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1630, 73, 5, '2026-06-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1631, 73, 5, '2026-06-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1632, 73, 5, '2026-07-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1633, 73, 5, '2026-07-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1634, 73, 5, '2026-07-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1635, 73, 5, '2026-07-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1636, 73, 5, '2026-08-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1637, 73, 5, '2026-08-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1638, 73, 5, '2026-08-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1639, 73, 5, '2026-08-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1640, 73, 5, '2026-08-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1641, 73, 5, '2026-09-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1642, 73, 5, '2026-09-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1643, 73, 5, '2026-09-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1644, 73, 5, '2026-09-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1645, 73, 5, '2026-10-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1646, 73, 5, '2026-10-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1647, 73, 5, '2026-10-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1648, 73, 5, '2026-10-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1649, 73, 5, '2026-10-31', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1650, 73, 5, '2026-11-07', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1651, 73, 5, '2026-11-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1652, 73, 5, '2026-11-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1653, 73, 5, '2026-11-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1654, 73, 5, '2026-12-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1655, 73, 5, '2026-12-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1656, 73, 5, '2026-12-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1657, 73, 5, '2026-12-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1658, 76, 5, '2026-04-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1659, 76, 5, '2026-04-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1660, 76, 5, '2026-05-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1661, 76, 5, '2026-05-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1662, 76, 5, '2026-05-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1663, 76, 5, '2026-05-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1664, 76, 5, '2026-05-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1665, 76, 5, '2026-06-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1666, 76, 5, '2026-06-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1667, 76, 5, '2026-06-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1668, 76, 5, '2026-06-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1669, 76, 5, '2026-07-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1670, 76, 5, '2026-07-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1671, 76, 5, '2026-07-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1672, 76, 5, '2026-07-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1673, 76, 5, '2026-08-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1674, 76, 5, '2026-08-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1675, 76, 5, '2026-08-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1676, 76, 5, '2026-08-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1677, 76, 5, '2026-08-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1678, 76, 5, '2026-09-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1679, 76, 5, '2026-09-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1680, 76, 5, '2026-09-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1681, 76, 5, '2026-09-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1682, 76, 5, '2026-10-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1683, 76, 5, '2026-10-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1684, 76, 5, '2026-10-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1685, 76, 5, '2026-10-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1686, 76, 5, '2026-10-31', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1687, 76, 5, '2026-11-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1688, 76, 5, '2026-11-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1689, 76, 5, '2026-11-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1690, 76, 5, '2026-11-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1691, 76, 5, '2026-12-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1692, 76, 5, '2026-12-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1693, 76, 5, '2026-12-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1694, 76, 5, '2026-12-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Saturday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1695, 79, 5, '2026-04-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1696, 79, 5, '2026-04-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1697, 79, 5, '2026-05-02', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1698, 79, 5, '2026-05-09', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1699, 79, 5, '2026-05-16', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1700, 79, 5, '2026-05-23', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1701, 79, 5, '2026-05-30', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1702, 79, 5, '2026-06-06', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1703, 79, 5, '2026-06-13', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1704, 79, 5, '2026-06-20', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1705, 79, 5, '2026-06-27', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1706, 79, 5, '2026-07-04', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1707, 79, 5, '2026-07-11', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1708, 79, 5, '2026-07-18', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1709, 79, 5, '2026-07-25', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1710, 79, 5, '2026-08-01', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1711, 79, 5, '2026-08-08', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09');
INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(1712, 79, 5, '2026-08-15', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1713, 79, 5, '2026-08-22', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1714, 79, 5, '2026-08-29', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1715, 79, 5, '2026-09-05', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1716, 79, 5, '2026-09-12', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1717, 79, 5, '2026-09-19', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1718, 79, 5, '2026-09-26', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1719, 79, 5, '2026-10-03', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1720, 79, 5, '2026-10-10', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1721, 79, 5, '2026-10-17', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1722, 79, 5, '2026-10-24', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1723, 79, 5, '2026-10-31', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1724, 79, 5, '2026-11-07', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1725, 79, 5, '2026-11-14', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1726, 79, 5, '2026-11-21', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1727, 79, 5, '2026-11-28', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1728, 79, 5, '2026-12-05', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1729, 79, 5, '2026-12-12', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1730, 79, 5, '2026-12-19', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1731, 79, 5, '2026-12-26', 3, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Dharshana - Saturday 1:1 Bowling Machine', 1, '2026-04-14 16:02:09'),
(1769, 83, 5, '2026-04-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1770, 83, 5, '2026-04-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1771, 83, 5, '2026-05-03', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1772, 83, 5, '2026-05-10', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1773, 83, 5, '2026-05-17', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1774, 83, 5, '2026-05-24', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1775, 83, 5, '2026-05-31', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1776, 83, 5, '2026-06-07', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1777, 83, 5, '2026-06-14', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1778, 83, 5, '2026-06-21', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1779, 83, 5, '2026-06-28', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1780, 83, 5, '2026-07-05', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1781, 83, 5, '2026-07-12', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1782, 83, 5, '2026-07-19', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1783, 83, 5, '2026-07-26', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1784, 83, 5, '2026-08-02', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1785, 83, 5, '2026-08-09', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1786, 83, 5, '2026-08-16', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1787, 83, 5, '2026-08-23', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1788, 83, 5, '2026-08-30', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1789, 83, 5, '2026-09-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1790, 83, 5, '2026-09-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1791, 83, 5, '2026-09-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1792, 83, 5, '2026-09-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1793, 83, 5, '2026-10-04', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1794, 83, 5, '2026-10-11', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1795, 83, 5, '2026-10-18', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1796, 83, 5, '2026-10-25', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1797, 83, 5, '2026-11-01', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1798, 83, 5, '2026-11-08', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1799, 83, 5, '2026-11-15', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1800, 83, 5, '2026-11-22', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1801, 83, 5, '2026-11-29', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1802, 83, 5, '2026-12-06', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1803, 83, 5, '2026-12-13', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1804, 83, 5, '2026-12-20', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1805, 83, 5, '2026-12-27', 1, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 1', 1, '2026-04-14 16:02:09'),
(1806, 86, 5, '2026-04-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1807, 86, 5, '2026-04-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1808, 86, 5, '2026-05-03', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1809, 86, 5, '2026-05-10', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1810, 86, 5, '2026-05-17', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1811, 86, 5, '2026-05-24', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1812, 86, 5, '2026-05-31', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1813, 86, 5, '2026-06-07', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1814, 86, 5, '2026-06-14', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1815, 86, 5, '2026-06-21', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1816, 86, 5, '2026-06-28', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1817, 86, 5, '2026-07-05', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1818, 86, 5, '2026-07-12', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1819, 86, 5, '2026-07-19', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1820, 86, 5, '2026-07-26', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1821, 86, 5, '2026-08-02', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1822, 86, 5, '2026-08-09', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1823, 86, 5, '2026-08-16', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1824, 86, 5, '2026-08-23', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1825, 86, 5, '2026-08-30', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1826, 86, 5, '2026-09-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1827, 86, 5, '2026-09-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1828, 86, 5, '2026-09-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1829, 86, 5, '2026-09-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1830, 86, 5, '2026-10-04', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1831, 86, 5, '2026-10-11', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1832, 86, 5, '2026-10-18', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1833, 86, 5, '2026-10-25', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1834, 86, 5, '2026-11-01', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1835, 86, 5, '2026-11-08', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1836, 86, 5, '2026-11-15', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1837, 86, 5, '2026-11-22', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1838, 86, 5, '2026-11-29', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1839, 86, 5, '2026-12-06', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1840, 86, 5, '2026-12-13', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1841, 86, 5, '2026-12-20', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(1842, 86, 5, '2026-12-27', 2, NULL, 'scheduled', NULL, 1, 'Generated from template: coach Kasun - Sunday 1:1 Practice Net 2', 1, '2026-04-14 16:02:09'),
(2159, NULL, 2, '2026-04-23', 4, NULL, 'scheduled', NULL, 10, 'Approved private session request', 8, '2026-04-14 22:55:26'),
(2160, 181, 4, '2026-04-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2161, 182, 4, '2026-04-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2162, 183, 3, '2026-04-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2163, 184, 3, '2026-04-14', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2164, 185, 3, '2026-04-16', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2165, 186, 4, '2026-04-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2166, 187, 4, '2026-04-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2167, 188, 3, '2026-04-16', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2168, 189, 3, '2026-04-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2169, 190, 3, '2026-04-17', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2170, 191, 4, '2026-04-13', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2171, 192, 3, '2026-04-15', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2172, 193, 4, '2026-04-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2173, 195, 4, '2026-04-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2174, 196, 1, '2026-04-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2175, 197, 4, '2026-04-14', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2176, 198, 4, '2026-04-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2177, 199, 5, '2026-04-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2178, 200, 5, '2026-04-16', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2179, 201, 1, '2026-04-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2180, 181, 4, '2026-04-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2181, 182, 4, '2026-04-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2182, 183, 3, '2026-04-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2183, 184, 3, '2026-04-21', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2184, 185, 3, '2026-04-23', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2185, 186, 4, '2026-04-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2186, 187, 4, '2026-04-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2187, 188, 3, '2026-04-23', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2188, 189, 3, '2026-04-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2189, 190, 3, '2026-04-24', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2190, 191, 4, '2026-04-20', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2191, 192, 3, '2026-04-22', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2192, 193, 4, '2026-04-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2193, 195, 4, '2026-04-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2194, 196, 1, '2026-04-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2195, 197, 4, '2026-04-21', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2196, 198, 4, '2026-04-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2197, 199, 5, '2026-04-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2198, 200, 5, '2026-04-23', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2199, 201, 1, '2026-04-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2200, 181, 4, '2026-04-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2201, 182, 4, '2026-04-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2202, 183, 3, '2026-05-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2203, 184, 3, '2026-04-28', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2204, 185, 3, '2026-04-30', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2205, 186, 4, '2026-05-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2206, 187, 4, '2026-04-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2207, 188, 3, '2026-04-30', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2208, 189, 3, '2026-04-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2209, 190, 3, '2026-05-01', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2210, 191, 4, '2026-04-27', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2211, 192, 3, '2026-04-29', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2212, 193, 4, '2026-05-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2213, 195, 4, '2026-04-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2214, 196, 1, '2026-05-02', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2215, 197, 4, '2026-04-28', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2216, 198, 4, '2026-04-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2217, 199, 5, '2026-04-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2218, 200, 5, '2026-04-30', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2219, 201, 1, '2026-05-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2220, 181, 4, '2026-05-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2221, 182, 4, '2026-05-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2222, 183, 3, '2026-05-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2223, 184, 3, '2026-05-05', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2224, 185, 3, '2026-05-07', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2225, 186, 4, '2026-05-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2226, 187, 4, '2026-05-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2227, 188, 3, '2026-05-07', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2228, 189, 3, '2026-05-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2229, 190, 3, '2026-05-08', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2230, 191, 4, '2026-05-04', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2231, 192, 3, '2026-05-06', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2232, 193, 4, '2026-05-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2233, 195, 4, '2026-05-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2234, 196, 1, '2026-05-09', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2235, 197, 4, '2026-05-05', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2236, 198, 4, '2026-05-06', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2237, 199, 5, '2026-05-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2238, 200, 5, '2026-05-07', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2239, 201, 1, '2026-05-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2240, 181, 4, '2026-05-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2241, 182, 4, '2026-05-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2242, 183, 3, '2026-05-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2243, 184, 3, '2026-05-12', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2244, 185, 3, '2026-05-14', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2245, 186, 4, '2026-05-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2246, 187, 4, '2026-05-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2247, 188, 3, '2026-05-14', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2248, 189, 3, '2026-05-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2249, 190, 3, '2026-05-15', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2250, 191, 4, '2026-05-11', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2251, 192, 3, '2026-05-13', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2252, 193, 4, '2026-05-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2253, 195, 4, '2026-05-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2254, 196, 1, '2026-05-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2255, 197, 4, '2026-05-12', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2256, 198, 4, '2026-05-13', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2257, 199, 5, '2026-05-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2258, 200, 5, '2026-05-14', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2259, 201, 1, '2026-05-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2260, 181, 4, '2026-05-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2261, 182, 4, '2026-05-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2262, 183, 3, '2026-05-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2263, 184, 3, '2026-05-19', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2264, 185, 3, '2026-05-21', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2265, 186, 4, '2026-05-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2266, 187, 4, '2026-05-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2267, 188, 3, '2026-05-21', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2268, 189, 3, '2026-05-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2269, 190, 3, '2026-05-22', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2270, 191, 4, '2026-05-18', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2271, 192, 3, '2026-05-20', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2272, 193, 4, '2026-05-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2273, 195, 4, '2026-05-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2274, 196, 1, '2026-05-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2275, 197, 4, '2026-05-19', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2276, 198, 4, '2026-05-20', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2277, 199, 5, '2026-05-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2278, 200, 5, '2026-05-21', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2279, 201, 1, '2026-05-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2280, 181, 4, '2026-05-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2281, 182, 4, '2026-05-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2282, 183, 3, '2026-05-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2283, 184, 3, '2026-05-26', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2284, 185, 3, '2026-05-28', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2285, 186, 4, '2026-05-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2286, 187, 4, '2026-05-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2287, 188, 3, '2026-05-28', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2288, 189, 3, '2026-05-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2289, 190, 3, '2026-05-29', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2290, 191, 4, '2026-05-25', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2291, 192, 3, '2026-05-27', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2292, 193, 4, '2026-05-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2293, 195, 4, '2026-05-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2294, 196, 1, '2026-05-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2295, 197, 4, '2026-05-26', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2296, 198, 4, '2026-05-27', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2297, 199, 5, '2026-05-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2298, 200, 5, '2026-05-28', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2299, 201, 1, '2026-05-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2300, 181, 4, '2026-06-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2301, 182, 4, '2026-06-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2302, 183, 3, '2026-06-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2303, 184, 3, '2026-06-02', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2304, 185, 3, '2026-06-04', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2305, 186, 4, '2026-06-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2306, 187, 4, '2026-06-02', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2307, 188, 3, '2026-06-04', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2308, 189, 3, '2026-06-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2309, 190, 3, '2026-06-05', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2310, 191, 4, '2026-06-01', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2311, 192, 3, '2026-06-03', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2312, 193, 4, '2026-06-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2313, 195, 4, '2026-06-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2314, 196, 1, '2026-06-06', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2315, 197, 4, '2026-06-02', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2316, 198, 4, '2026-06-03', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2317, 199, 5, '2026-06-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2318, 200, 5, '2026-06-04', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2319, 201, 1, '2026-06-06', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2320, 181, 4, '2026-06-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2321, 182, 4, '2026-06-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2322, 183, 3, '2026-06-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2323, 184, 3, '2026-06-09', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2324, 185, 3, '2026-06-11', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2325, 186, 4, '2026-06-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2326, 187, 4, '2026-06-09', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2327, 188, 3, '2026-06-11', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2328, 189, 3, '2026-06-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2329, 190, 3, '2026-06-12', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2330, 191, 4, '2026-06-08', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2331, 192, 3, '2026-06-10', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2332, 193, 4, '2026-06-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2333, 195, 4, '2026-06-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2334, 196, 1, '2026-06-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2335, 197, 4, '2026-06-09', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2336, 198, 4, '2026-06-10', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2337, 199, 5, '2026-06-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2338, 200, 5, '2026-06-11', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2339, 201, 1, '2026-06-13', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2340, 181, 4, '2026-06-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2341, 182, 4, '2026-06-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2342, 183, 3, '2026-06-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2343, 184, 3, '2026-06-16', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2344, 185, 3, '2026-06-18', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2345, 186, 4, '2026-06-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2346, 187, 4, '2026-06-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2347, 188, 3, '2026-06-18', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2348, 189, 3, '2026-06-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2349, 190, 3, '2026-06-19', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2350, 191, 4, '2026-06-15', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2351, 192, 3, '2026-06-17', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2352, 193, 4, '2026-06-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2353, 195, 4, '2026-06-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2354, 196, 1, '2026-06-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2355, 197, 4, '2026-06-16', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2356, 198, 4, '2026-06-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2357, 199, 5, '2026-06-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2358, 200, 5, '2026-06-18', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2359, 201, 1, '2026-06-20', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2360, 181, 4, '2026-06-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2361, 182, 4, '2026-06-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2362, 183, 3, '2026-06-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2363, 184, 3, '2026-06-23', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2364, 185, 3, '2026-06-25', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2365, 186, 4, '2026-06-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2366, 187, 4, '2026-06-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2367, 188, 3, '2026-06-25', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2368, 189, 3, '2026-06-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2369, 190, 3, '2026-06-26', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2370, 191, 4, '2026-06-22', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2371, 192, 3, '2026-06-24', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2372, 193, 4, '2026-06-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2373, 195, 4, '2026-06-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2374, 196, 1, '2026-06-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2375, 197, 4, '2026-06-23', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2376, 198, 4, '2026-06-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2377, 199, 5, '2026-06-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2378, 200, 5, '2026-06-25', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2379, 201, 1, '2026-06-27', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2380, 181, 4, '2026-06-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2381, 182, 4, '2026-07-02', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2382, 183, 3, '2026-07-03', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2383, 184, 3, '2026-06-30', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2384, 185, 3, '2026-07-02', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2385, 186, 4, '2026-07-03', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2386, 187, 4, '2026-06-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2387, 188, 3, '2026-07-02', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2388, 189, 3, '2026-06-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2389, 190, 3, '2026-07-03', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2390, 191, 4, '2026-06-29', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23');
INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(2391, 192, 3, '2026-07-01', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2392, 193, 4, '2026-07-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2393, 195, 4, '2026-06-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2394, 196, 1, '2026-07-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2395, 197, 4, '2026-06-30', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2396, 198, 4, '2026-07-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2397, 199, 5, '2026-06-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2398, 200, 5, '2026-07-02', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2399, 201, 1, '2026-07-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2400, 181, 4, '2026-07-06', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2401, 182, 4, '2026-07-09', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2402, 183, 3, '2026-07-10', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2403, 184, 3, '2026-07-07', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2404, 185, 3, '2026-07-09', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2405, 186, 4, '2026-07-10', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2406, 187, 4, '2026-07-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2407, 188, 3, '2026-07-09', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2408, 189, 3, '2026-07-07', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2409, 190, 3, '2026-07-10', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2410, 191, 4, '2026-07-06', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2411, 192, 3, '2026-07-08', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2412, 193, 4, '2026-07-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2413, 195, 4, '2026-07-07', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2414, 196, 1, '2026-07-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2415, 197, 4, '2026-07-07', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2416, 198, 4, '2026-07-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2417, 199, 5, '2026-07-06', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2418, 200, 5, '2026-07-09', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2419, 201, 1, '2026-07-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2420, 181, 4, '2026-07-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2421, 182, 4, '2026-07-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2422, 183, 3, '2026-07-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2423, 184, 3, '2026-07-14', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2424, 185, 3, '2026-07-16', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2425, 186, 4, '2026-07-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2426, 187, 4, '2026-07-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2427, 188, 3, '2026-07-16', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2428, 189, 3, '2026-07-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2429, 190, 3, '2026-07-17', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2430, 191, 4, '2026-07-13', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2431, 192, 3, '2026-07-15', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2432, 193, 4, '2026-07-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2433, 195, 4, '2026-07-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2434, 196, 1, '2026-07-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2435, 197, 4, '2026-07-14', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2436, 198, 4, '2026-07-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2437, 199, 5, '2026-07-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2438, 200, 5, '2026-07-16', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2439, 201, 1, '2026-07-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2440, 181, 4, '2026-07-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2441, 182, 4, '2026-07-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2442, 183, 3, '2026-07-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2443, 184, 3, '2026-07-21', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2444, 185, 3, '2026-07-23', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2445, 186, 4, '2026-07-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2446, 187, 4, '2026-07-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2447, 188, 3, '2026-07-23', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2448, 189, 3, '2026-07-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2449, 190, 3, '2026-07-24', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2450, 191, 4, '2026-07-20', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2451, 192, 3, '2026-07-22', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2452, 193, 4, '2026-07-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2453, 195, 4, '2026-07-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2454, 196, 1, '2026-07-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2455, 197, 4, '2026-07-21', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2456, 198, 4, '2026-07-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2457, 199, 5, '2026-07-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2458, 200, 5, '2026-07-23', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2459, 201, 1, '2026-07-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2460, 181, 4, '2026-07-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2461, 182, 4, '2026-07-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2462, 183, 3, '2026-07-31', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2463, 184, 3, '2026-07-28', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2464, 185, 3, '2026-07-30', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2465, 186, 4, '2026-07-31', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2466, 187, 4, '2026-07-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2467, 188, 3, '2026-07-30', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2468, 189, 3, '2026-07-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2469, 190, 3, '2026-07-31', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2470, 191, 4, '2026-07-27', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2471, 192, 3, '2026-07-29', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2472, 193, 4, '2026-07-31', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2473, 195, 4, '2026-07-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2474, 196, 1, '2026-08-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2475, 197, 4, '2026-07-28', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2476, 198, 4, '2026-07-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2477, 199, 5, '2026-07-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2478, 200, 5, '2026-07-30', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2479, 201, 1, '2026-08-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2480, 181, 4, '2026-08-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2481, 182, 4, '2026-08-06', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2482, 183, 3, '2026-08-07', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2483, 184, 3, '2026-08-04', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2484, 185, 3, '2026-08-06', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2485, 186, 4, '2026-08-07', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2486, 187, 4, '2026-08-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2487, 188, 3, '2026-08-06', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2488, 189, 3, '2026-08-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2489, 190, 3, '2026-08-07', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2490, 191, 4, '2026-08-03', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2491, 192, 3, '2026-08-05', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2492, 193, 4, '2026-08-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2493, 195, 4, '2026-08-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2494, 196, 1, '2026-08-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2495, 197, 4, '2026-08-04', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2496, 198, 4, '2026-08-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2497, 199, 5, '2026-08-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2498, 200, 5, '2026-08-06', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2499, 201, 1, '2026-08-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2500, 181, 4, '2026-08-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2501, 182, 4, '2026-08-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2502, 183, 3, '2026-08-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2503, 184, 3, '2026-08-11', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2504, 185, 3, '2026-08-13', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2505, 186, 4, '2026-08-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2506, 187, 4, '2026-08-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2507, 188, 3, '2026-08-13', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2508, 189, 3, '2026-08-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2509, 190, 3, '2026-08-14', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2510, 191, 4, '2026-08-10', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2511, 192, 3, '2026-08-12', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2512, 193, 4, '2026-08-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2513, 195, 4, '2026-08-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2514, 196, 1, '2026-08-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2515, 197, 4, '2026-08-11', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2516, 198, 4, '2026-08-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2517, 199, 5, '2026-08-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2518, 200, 5, '2026-08-13', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2519, 201, 1, '2026-08-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2520, 181, 4, '2026-08-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2521, 182, 4, '2026-08-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2522, 183, 3, '2026-08-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2523, 184, 3, '2026-08-18', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2524, 185, 3, '2026-08-20', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2525, 186, 4, '2026-08-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2526, 187, 4, '2026-08-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2527, 188, 3, '2026-08-20', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2528, 189, 3, '2026-08-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2529, 190, 3, '2026-08-21', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2530, 191, 4, '2026-08-17', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2531, 192, 3, '2026-08-19', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2532, 193, 4, '2026-08-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2533, 195, 4, '2026-08-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2534, 196, 1, '2026-08-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2535, 197, 4, '2026-08-18', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2536, 198, 4, '2026-08-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2537, 199, 5, '2026-08-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2538, 200, 5, '2026-08-20', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2539, 201, 1, '2026-08-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2540, 181, 4, '2026-08-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2541, 182, 4, '2026-08-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2542, 183, 3, '2026-08-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2543, 184, 3, '2026-08-25', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2544, 185, 3, '2026-08-27', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2545, 186, 4, '2026-08-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2546, 187, 4, '2026-08-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2547, 188, 3, '2026-08-27', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2548, 189, 3, '2026-08-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2549, 190, 3, '2026-08-28', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2550, 191, 4, '2026-08-24', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2551, 192, 3, '2026-08-26', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2552, 193, 4, '2026-08-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2553, 195, 4, '2026-08-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2554, 196, 1, '2026-08-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2555, 197, 4, '2026-08-25', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2556, 198, 4, '2026-08-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2557, 199, 5, '2026-08-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2558, 200, 5, '2026-08-27', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2559, 201, 1, '2026-08-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2560, 181, 4, '2026-08-31', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2561, 182, 4, '2026-09-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2562, 183, 3, '2026-09-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2563, 184, 3, '2026-09-01', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2564, 185, 3, '2026-09-03', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2565, 186, 4, '2026-09-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2566, 187, 4, '2026-09-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2567, 188, 3, '2026-09-03', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2568, 189, 3, '2026-09-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2569, 190, 3, '2026-09-04', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2570, 191, 4, '2026-08-31', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2571, 192, 3, '2026-09-02', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2572, 193, 4, '2026-09-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2573, 195, 4, '2026-09-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2574, 196, 1, '2026-09-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2575, 197, 4, '2026-09-01', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2576, 198, 4, '2026-09-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2577, 199, 5, '2026-08-31', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2578, 200, 5, '2026-09-03', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2579, 201, 1, '2026-09-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2580, 181, 4, '2026-09-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2581, 182, 4, '2026-09-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2582, 183, 3, '2026-09-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2583, 184, 3, '2026-09-08', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2584, 185, 3, '2026-09-10', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2585, 186, 4, '2026-09-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2586, 187, 4, '2026-09-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2587, 188, 3, '2026-09-10', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2588, 189, 3, '2026-09-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2589, 190, 3, '2026-09-11', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2590, 191, 4, '2026-09-07', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2591, 192, 3, '2026-09-09', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2592, 193, 4, '2026-09-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2593, 195, 4, '2026-09-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2594, 196, 1, '2026-09-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2595, 197, 4, '2026-09-08', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2596, 198, 4, '2026-09-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2597, 199, 5, '2026-09-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2598, 200, 5, '2026-09-10', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2599, 201, 1, '2026-09-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2600, 181, 4, '2026-09-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2601, 182, 4, '2026-09-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2602, 183, 3, '2026-09-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2603, 184, 3, '2026-09-15', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2604, 185, 3, '2026-09-17', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2605, 186, 4, '2026-09-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2606, 187, 4, '2026-09-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2607, 188, 3, '2026-09-17', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2608, 189, 3, '2026-09-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2609, 190, 3, '2026-09-18', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2610, 191, 4, '2026-09-14', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2611, 192, 3, '2026-09-16', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2612, 193, 4, '2026-09-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2613, 195, 4, '2026-09-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2614, 196, 1, '2026-09-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2615, 197, 4, '2026-09-15', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2616, 198, 4, '2026-09-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2617, 199, 5, '2026-09-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2618, 200, 5, '2026-09-17', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2619, 201, 1, '2026-09-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2620, 181, 4, '2026-09-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2621, 182, 4, '2026-09-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2622, 183, 3, '2026-09-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2623, 184, 3, '2026-09-22', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2624, 185, 3, '2026-09-24', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2625, 186, 4, '2026-09-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2626, 187, 4, '2026-09-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2627, 188, 3, '2026-09-24', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2628, 189, 3, '2026-09-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2629, 190, 3, '2026-09-25', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2630, 191, 4, '2026-09-21', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2631, 192, 3, '2026-09-23', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2632, 193, 4, '2026-09-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2633, 195, 4, '2026-09-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2634, 196, 1, '2026-09-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2635, 197, 4, '2026-09-22', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2636, 198, 4, '2026-09-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2637, 199, 5, '2026-09-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2638, 200, 5, '2026-09-24', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2639, 201, 1, '2026-09-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2640, 181, 4, '2026-09-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2641, 182, 4, '2026-10-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2642, 183, 3, '2026-10-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2643, 184, 3, '2026-09-29', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2644, 185, 3, '2026-10-01', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2645, 186, 4, '2026-10-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2646, 187, 4, '2026-09-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2647, 188, 3, '2026-10-01', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2648, 189, 3, '2026-09-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2649, 190, 3, '2026-10-02', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2650, 191, 4, '2026-09-28', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2651, 192, 3, '2026-09-30', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2652, 193, 4, '2026-10-02', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2653, 195, 4, '2026-09-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2654, 196, 1, '2026-10-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2655, 197, 4, '2026-09-29', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2656, 198, 4, '2026-09-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2657, 199, 5, '2026-09-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2658, 200, 5, '2026-10-01', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2659, 201, 1, '2026-10-03', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2660, 181, 4, '2026-10-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2661, 182, 4, '2026-10-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2662, 183, 3, '2026-10-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2663, 184, 3, '2026-10-06', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2664, 185, 3, '2026-10-08', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2665, 186, 4, '2026-10-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2666, 187, 4, '2026-10-06', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2667, 188, 3, '2026-10-08', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2668, 189, 3, '2026-10-06', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2669, 190, 3, '2026-10-09', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2670, 191, 4, '2026-10-05', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2671, 192, 3, '2026-10-07', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2672, 193, 4, '2026-10-09', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2673, 195, 4, '2026-10-06', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2674, 196, 1, '2026-10-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2675, 197, 4, '2026-10-06', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2676, 198, 4, '2026-10-07', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2677, 199, 5, '2026-10-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2678, 200, 5, '2026-10-08', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2679, 201, 1, '2026-10-10', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2680, 181, 4, '2026-10-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2681, 182, 4, '2026-10-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2682, 183, 3, '2026-10-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2683, 184, 3, '2026-10-13', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2684, 185, 3, '2026-10-15', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2685, 186, 4, '2026-10-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2686, 187, 4, '2026-10-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2687, 188, 3, '2026-10-15', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2688, 189, 3, '2026-10-13', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2689, 190, 3, '2026-10-16', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2690, 191, 4, '2026-10-12', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2691, 192, 3, '2026-10-14', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2692, 193, 4, '2026-10-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2693, 195, 4, '2026-10-13', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2694, 196, 1, '2026-10-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2695, 197, 4, '2026-10-13', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2696, 198, 4, '2026-10-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2697, 199, 5, '2026-10-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2698, 200, 5, '2026-10-15', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2699, 201, 1, '2026-10-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2700, 181, 4, '2026-10-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2701, 182, 4, '2026-10-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2702, 183, 3, '2026-10-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2703, 184, 3, '2026-10-20', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2704, 185, 3, '2026-10-22', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2705, 186, 4, '2026-10-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2706, 187, 4, '2026-10-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2707, 188, 3, '2026-10-22', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2708, 189, 3, '2026-10-20', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2709, 190, 3, '2026-10-23', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2710, 191, 4, '2026-10-19', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2711, 192, 3, '2026-10-21', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2712, 193, 4, '2026-10-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2713, 195, 4, '2026-10-20', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2714, 196, 1, '2026-10-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2715, 197, 4, '2026-10-20', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2716, 198, 4, '2026-10-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2717, 199, 5, '2026-10-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23');
INSERT INTO `slot_occurrence` (`OccurrenceID`, `TemplateID`, `SlotID`, `OccurrenceDate`, `FacilityID`, `LegacySessionID`, `Status`, `CancelReason`, `MaxParticipants`, `Notes`, `GeneratedBy`, `CreatedAt`) VALUES
(2718, 200, 5, '2026-10-22', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2719, 201, 1, '2026-10-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2720, 181, 4, '2026-10-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2721, 182, 4, '2026-10-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2722, 183, 3, '2026-10-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2723, 184, 3, '2026-10-27', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2724, 185, 3, '2026-10-29', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2725, 186, 4, '2026-10-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2726, 187, 4, '2026-10-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2727, 188, 3, '2026-10-29', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2728, 189, 3, '2026-10-27', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2729, 190, 3, '2026-10-30', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2730, 191, 4, '2026-10-26', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2731, 192, 3, '2026-10-28', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2732, 193, 4, '2026-10-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2733, 195, 4, '2026-10-27', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2734, 196, 1, '2026-10-31', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2735, 197, 4, '2026-10-27', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2736, 198, 4, '2026-10-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2737, 199, 5, '2026-10-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2738, 200, 5, '2026-10-29', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2739, 201, 1, '2026-10-31', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2740, 181, 4, '2026-11-02', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2741, 182, 4, '2026-11-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2742, 183, 3, '2026-11-06', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2743, 184, 3, '2026-11-03', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2744, 185, 3, '2026-11-05', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2745, 186, 4, '2026-11-06', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2746, 187, 4, '2026-11-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2747, 188, 3, '2026-11-05', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2748, 189, 3, '2026-11-03', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2749, 190, 3, '2026-11-06', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2750, 191, 4, '2026-11-02', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2751, 192, 3, '2026-11-04', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2752, 193, 4, '2026-11-06', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2753, 195, 4, '2026-11-03', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2754, 196, 1, '2026-11-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2755, 197, 4, '2026-11-03', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2756, 198, 4, '2026-11-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2757, 199, 5, '2026-11-02', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2758, 200, 5, '2026-11-05', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2759, 201, 1, '2026-11-07', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2760, 181, 4, '2026-11-09', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2761, 182, 4, '2026-11-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2762, 183, 3, '2026-11-13', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2763, 184, 3, '2026-11-10', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2764, 185, 3, '2026-11-12', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2765, 186, 4, '2026-11-13', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2766, 187, 4, '2026-11-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2767, 188, 3, '2026-11-12', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2768, 189, 3, '2026-11-10', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2769, 190, 3, '2026-11-13', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2770, 191, 4, '2026-11-09', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2771, 192, 3, '2026-11-11', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2772, 193, 4, '2026-11-13', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2773, 195, 4, '2026-11-10', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2774, 196, 1, '2026-11-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2775, 197, 4, '2026-11-10', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2776, 198, 4, '2026-11-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2777, 199, 5, '2026-11-09', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2778, 200, 5, '2026-11-12', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2779, 201, 1, '2026-11-14', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2780, 181, 4, '2026-11-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2781, 182, 4, '2026-11-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2782, 183, 3, '2026-11-20', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2783, 184, 3, '2026-11-17', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2784, 185, 3, '2026-11-19', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2785, 186, 4, '2026-11-20', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2786, 187, 4, '2026-11-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2787, 188, 3, '2026-11-19', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2788, 189, 3, '2026-11-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2789, 190, 3, '2026-11-20', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2790, 191, 4, '2026-11-16', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2791, 192, 3, '2026-11-18', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2792, 193, 4, '2026-11-20', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2793, 195, 4, '2026-11-17', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2794, 196, 1, '2026-11-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2795, 197, 4, '2026-11-17', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2796, 198, 4, '2026-11-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2797, 199, 5, '2026-11-16', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2798, 200, 5, '2026-11-19', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2799, 201, 1, '2026-11-21', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2800, 181, 4, '2026-11-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2801, 182, 4, '2026-11-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2802, 183, 3, '2026-11-27', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2803, 184, 3, '2026-11-24', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2804, 185, 3, '2026-11-26', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2805, 186, 4, '2026-11-27', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2806, 187, 4, '2026-11-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2807, 188, 3, '2026-11-26', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2808, 189, 3, '2026-11-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2809, 190, 3, '2026-11-27', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2810, 191, 4, '2026-11-23', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2811, 192, 3, '2026-11-25', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2812, 193, 4, '2026-11-27', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2813, 195, 4, '2026-11-24', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2814, 196, 1, '2026-11-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2815, 197, 4, '2026-11-24', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2816, 198, 4, '2026-11-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2817, 199, 5, '2026-11-23', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2818, 200, 5, '2026-11-26', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2819, 201, 1, '2026-11-28', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2820, 181, 4, '2026-11-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2821, 182, 4, '2026-12-03', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2822, 183, 3, '2026-12-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2823, 184, 3, '2026-12-01', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2824, 185, 3, '2026-12-03', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2825, 186, 4, '2026-12-04', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2826, 187, 4, '2026-12-01', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2827, 188, 3, '2026-12-03', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2828, 189, 3, '2026-12-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2829, 190, 3, '2026-12-04', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2830, 191, 4, '2026-11-30', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2831, 192, 3, '2026-12-02', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2832, 193, 4, '2026-12-04', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2833, 195, 4, '2026-12-01', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2834, 196, 1, '2026-12-05', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2835, 197, 4, '2026-12-01', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2836, 198, 4, '2026-12-02', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2837, 199, 5, '2026-11-30', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2838, 200, 5, '2026-12-03', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2839, 201, 1, '2026-12-05', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2840, 181, 4, '2026-12-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2841, 182, 4, '2026-12-10', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2842, 183, 3, '2026-12-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2843, 184, 3, '2026-12-08', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2844, 185, 3, '2026-12-10', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2845, 186, 4, '2026-12-11', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2846, 187, 4, '2026-12-08', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2847, 188, 3, '2026-12-10', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2848, 189, 3, '2026-12-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2849, 190, 3, '2026-12-11', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2850, 191, 4, '2026-12-07', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2851, 192, 3, '2026-12-09', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2852, 193, 4, '2026-12-11', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2853, 195, 4, '2026-12-08', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2854, 196, 1, '2026-12-12', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2855, 197, 4, '2026-12-08', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2856, 198, 4, '2026-12-09', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2857, 199, 5, '2026-12-07', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2858, 200, 5, '2026-12-10', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2859, 201, 1, '2026-12-12', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2860, 181, 4, '2026-12-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2861, 182, 4, '2026-12-17', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2862, 183, 3, '2026-12-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2863, 184, 3, '2026-12-15', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2864, 185, 3, '2026-12-17', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2865, 186, 4, '2026-12-18', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2866, 187, 4, '2026-12-15', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2867, 188, 3, '2026-12-17', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2868, 189, 3, '2026-12-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2869, 190, 3, '2026-12-18', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2870, 191, 4, '2026-12-14', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2871, 192, 3, '2026-12-16', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2872, 193, 4, '2026-12-18', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2873, 195, 4, '2026-12-15', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2874, 196, 1, '2026-12-19', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2875, 197, 4, '2026-12-15', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2876, 198, 4, '2026-12-16', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2877, 199, 5, '2026-12-14', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2878, 200, 5, '2026-12-17', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2879, 201, 1, '2026-12-19', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2880, 181, 4, '2026-12-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2881, 182, 4, '2026-12-24', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2882, 183, 3, '2026-12-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2883, 184, 3, '2026-12-22', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2884, 185, 3, '2026-12-24', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2885, 186, 4, '2026-12-25', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 13 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2886, 187, 4, '2026-12-22', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2887, 188, 3, '2026-12-24', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2888, 189, 3, '2026-12-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2889, 190, 3, '2026-12-25', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2890, 191, 4, '2026-12-21', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2891, 192, 3, '2026-12-23', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2892, 193, 4, '2026-12-25', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2893, 195, 4, '2026-12-22', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2894, 196, 1, '2026-12-26', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2895, 197, 4, '2026-12-22', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2896, 198, 4, '2026-12-23', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2897, 199, 5, '2026-12-21', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2898, 200, 5, '2026-12-24', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2899, 201, 1, '2026-12-26', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2900, 181, 4, '2026-12-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2901, 182, 4, '2026-12-31', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 11 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2902, 184, 3, '2026-12-29', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2903, 185, 3, '2026-12-31', 1, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 13 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2904, 187, 4, '2026-12-29', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Batting Group - Practice Net 1', 1, '2026-04-15 01:55:23'),
(2905, 188, 3, '2026-12-31', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2906, 189, 3, '2026-12-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 15 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2907, 191, 4, '2026-12-28', 3, NULL, 'scheduled', NULL, 11, 'Generated from template: Under 17 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2908, 192, 3, '2026-12-30', 4, NULL, 'scheduled', NULL, 2, 'Generated from template: Under 17 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2909, 195, 4, '2026-12-29', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 19 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2910, 197, 4, '2026-12-29', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23'),
(2911, 198, 4, '2026-12-30', 4, NULL, 'scheduled', NULL, 0, 'Generated from template: Under 21 Fielding Group - Main Ground', 1, '2026-04-15 01:55:23'),
(2912, 199, 5, '2026-12-28', 1, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Batting Group - Practice Net 2', 1, '2026-04-15 01:55:23'),
(2913, 200, 5, '2026-12-31', 3, NULL, 'scheduled', NULL, 0, 'Generated from template: Open Bowling Group - Bowling Machine', 1, '2026-04-15 01:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `slot_occurrence_staff_override`
--

CREATE TABLE `slot_occurrence_staff_override` (
  `ID` int(11) NOT NULL,
  `OccurrenceID` int(11) NOT NULL COMMENT 'FK → slot_occurrence',
  `UserID` int(11) NOT NULL COMMENT 'FK → user (substitute coach or trainer)',
  `StaffType` enum('coach','trainer') NOT NULL,
  `StaffRole` enum('lead','assistant','substitute') NOT NULL DEFAULT 'substitute',
  `OverridesUserID` int(11) DEFAULT NULL COMMENT 'UserID of the person being replaced this date',
  `OverrideReason` varchar(255) DEFAULT NULL COMMENT '"Sick leave", "Emergency", "Training camp", etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='One-day staff substitution. Does not affect the template or any other occurrence date.';

--
-- Dumping data for table `slot_occurrence_staff_override`
--

INSERT INTO `slot_occurrence_staff_override` (`ID`, `OccurrenceID`, `UserID`, `StaffType`, `StaffRole`, `OverridesUserID`, `OverrideReason`) VALUES
(1, 2159, 3, 'coach', 'lead', NULL, 'Approved from private session request #2');

-- --------------------------------------------------------

--
-- Table structure for table `slot_private_session_request`
--

CREATE TABLE `slot_private_session_request` (
  `RequestID` int(11) NOT NULL,
  `RequesterUserID` int(11) NOT NULL,
  `StaffType` enum('coach','trainer') NOT NULL,
  `SlotID` tinyint(4) NOT NULL,
  `RequestedDate` date NOT NULL,
  `FacilityID` int(11) DEFAULT NULL,
  `MaxParticipants` int(11) DEFAULT 10,
  `Notes` text DEFAULT NULL,
  `Status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `ReviewNotes` text DEFAULT NULL,
  `ReviewedBy` int(11) DEFAULT NULL,
  `ReviewedAt` datetime DEFAULT NULL,
  `ApprovedOccurrenceID` int(11) DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_private_session_request`
--

INSERT INTO `slot_private_session_request` (`RequestID`, `RequesterUserID`, `StaffType`, `SlotID`, `RequestedDate`, `FacilityID`, `MaxParticipants`, `Notes`, `Status`, `ReviewNotes`, `ReviewedBy`, `ReviewedAt`, `ApprovedOccurrenceID`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 3, 'coach', 3, '2026-04-16', 4, 10, '', 'pending', NULL, NULL, NULL, NULL, '2026-04-14 21:50:02', '2026-04-14 21:50:02'),
(2, 3, 'coach', 2, '2026-04-23', 4, 10, '', 'approved', NULL, 8, '2026-04-14 22:55:26', 2159, '2026-04-14 21:50:22', '2026-04-14 22:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `slot_template`
--

CREATE TABLE `slot_template` (
  `TemplateID` int(11) NOT NULL,
  `TemplateName` varchar(255) NOT NULL,
  `temp_code` varchar(50) DEFAULT NULL,
  `SlotType` enum('program','private','facility_only') NOT NULL COMMENT 'program=group subscription session | private=1:1 on request | facility_only=no staff',
  `StaffType` enum('coach','trainer','none') NOT NULL DEFAULT 'coach' COMMENT 'Determines post-session log prompt: coachingsession vs trainerappointment vs none',
  `SlotID` tinyint(4) NOT NULL COMMENT 'FK → slot_time_band',
  `DayOfWeek` tinyint(1) DEFAULT NULL COMMENT '1=Mon…7=Sun; NULL=no fixed day',
  `FacilityID` int(11) DEFAULT NULL COMMENT 'FK → facility; NULL=assigned per occurrence',
  `AgeGroup` varchar(50) DEFAULT NULL COMMENT '"Under 15", "Under 19", "Open"',
  `Category` varchar(100) DEFAULT NULL COMMENT '"Batting","Bowling","Fielding","Fitness"',
  `Description` text DEFAULT NULL,
  `MaxParticipants` int(11) DEFAULT NULL COMMENT 'NULL = no fixed capacity; program sessions use eligible player assignments instead',
  `PricePerSession` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '0.00 = subscription-covered; >0 = direct charge',
  `RequiredPlanFeature` varchar(50) DEFAULT NULL COMMENT 'NULL = open to all; supports legacy feature rules and plan:ID values checked at booking time',
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedBy` int(11) NOT NULL COMMENT 'FK → user (Admin)',
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master definition of recurring programs and bookable slot offerings.';

--
-- Dumping data for table `slot_template`
--

INSERT INTO `slot_template` (`TemplateID`, `TemplateName`, `temp_code`, `SlotType`, `StaffType`, `SlotID`, `DayOfWeek`, `FacilityID`, `AgeGroup`, `Category`, `Description`, `MaxParticipants`, `PricePerSession`, `RequiredPlanFeature`, `IsActive`, `CreatedBy`, `CreatedAt`, `UpdatedAt`) VALUES
(2, 'Practice Net 1 Mon 8-10', 'F-PN1-MON-6', 'facility_only', 'none', 6, 1, 1, NULL, 'Practice Net', 'Practice Net 1 Mon 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(3, 'Practice Net 1 Tue 8-10', 'F-PN1-TUE-6', 'facility_only', 'none', 6, 2, 1, NULL, 'Practice Net', 'Practice Net 1 Tue 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(4, 'Practice Net 1 Wed 8-10', 'F-PN1-WED-6', 'facility_only', 'none', 6, 3, 1, NULL, 'Practice Net', 'Practice Net 1 Wed 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(5, 'Practice Net 1 Thu 8-10', 'F-PN1-THU-6', 'facility_only', 'none', 6, 4, 1, NULL, 'Practice Net', 'Practice Net 1 Thu 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(6, 'Practice Net 1 Fri 8-10', 'F-PN1-FRI-6', 'facility_only', 'none', 6, 5, 1, NULL, 'Practice Net', 'Practice Net 1 Fri 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(7, 'Practice Net 1 Sat 8-10', 'F-PN1-SAT-6', 'facility_only', 'none', 6, 6, 1, NULL, 'Practice Net', 'Practice Net 1 Sat 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(8, 'Practice Net 1 Sun 8-10', 'F-PN1-SUN-6', 'facility_only', 'none', 6, 7, 1, NULL, 'Practice Net', 'Practice Net 1 Sun 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(9, 'Practice Net 2 Mon 8-10', 'F-PN2-MON-6', 'facility_only', 'none', 6, 1, 2, NULL, 'Practice Net', 'Practice Net 2 Mon 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(10, 'Practice Net 2 Tue 8-10', 'F-PN2-TUE-6', 'facility_only', 'none', 6, 2, 2, NULL, 'Practice Net', 'Practice Net 2 Tue 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(11, 'Practice Net 2 Wed 8-10', 'F-PN2-WED-6', 'facility_only', 'none', 6, 3, 2, NULL, 'Practice Net', 'Practice Net 2 Wed 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(12, 'Practice Net 2 Thu 8-10', 'F-PN2-THU-6', 'facility_only', 'none', 6, 4, 2, NULL, 'Practice Net', 'Practice Net 2 Thu 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(13, 'Practice Net 2 Fri 8-10', 'F-PN2-FRI-6', 'facility_only', 'none', 6, 5, 2, NULL, 'Practice Net', 'Practice Net 2 Fri 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(14, 'Practice Net 2 Sat 8-10', 'F-PN2-SAT-6', 'facility_only', 'none', 6, 6, 2, NULL, 'Practice Net', 'Practice Net 2 Sat 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(15, 'Practice Net 2 Sun 8-10', 'F-PN2-SUN-6', 'facility_only', 'none', 6, 7, 2, NULL, 'Practice Net', 'Practice Net 2 Sun 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(16, 'Bowling Machine Mon 8-10', 'F-BM-MON-6', 'facility_only', 'none', 6, 1, 3, NULL, 'Bowling Machine', 'Bowling Machine Mon 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(17, 'Bowling Machine Tue 8-10', 'F-BM-TUE-6', 'facility_only', 'none', 6, 2, 3, NULL, 'Bowling Machine', 'Bowling Machine Tue 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(18, 'Bowling Machine Wed 8-10', 'F-BM-WED-6', 'facility_only', 'none', 6, 3, 3, NULL, 'Bowling Machine', 'Bowling Machine Wed 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(19, 'Bowling Machine Thu 8-10', 'F-BM-THU-6', 'facility_only', 'none', 6, 4, 3, NULL, 'Bowling Machine', 'Bowling Machine Thu 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(20, 'Bowling Machine Fri 8-10', 'F-BM-FRI-6', 'facility_only', 'none', 6, 5, 3, NULL, 'Bowling Machine', 'Bowling Machine Fri 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(21, 'Bowling Machine Sat 8-10', 'F-BM-SAT-6', 'facility_only', 'none', 6, 6, 3, NULL, 'Bowling Machine', 'Bowling Machine Sat 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(22, 'Bowling Machine Sun 8-10', 'F-BM-SUN-6', 'facility_only', 'none', 6, 7, 3, NULL, 'Bowling Machine', 'Bowling Machine Sun 8-10', 10, 2500.00, 'facility_access', 1, 1, '2026-04-14 10:23:52', '2026-04-14 11:47:00'),
(26, 'coach Kasun - Monday 1:1 Practice Net 2', 'PVT-BAT-MON-S5-F2-C9', 'private', 'coach', 5, 1, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(27, 'venuk Wickrama - Monday 1:1 Practice Net 2', 'PVT-BAT-MON-S5-F2-C27', 'private', 'coach', 5, 1, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(28, 'Coach Niroshan - Monday 1:1 Practice Net 2', 'PVT-BAT-MON-S5-F2-C30', 'private', 'coach', 5, 1, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(29, 'coach Dharshana - Monday 1:1 Bowling Machine', 'PVT-BOWL-MON-S5-F3-C11', 'private', 'coach', 5, 1, 3, 'Under 13', 'Bowling', '1-on-1 Bowling coaching with coach Dharshana', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(30, 'Kumara Darmasena - Monday 1:1 Bowling Machine', 'PVT-BOWL-MON-S5-F3-C12', 'private', 'coach', 5, 1, 3, 'Under 11', 'Bowling', '1-on-1 Bowling coaching with Kumara Darmasena', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(31, 'Coach Ruwan - Monday 1:1 Bowling Machine', 'PVT-BOWL-MON-S5-F3-C31', 'private', 'coach', 5, 1, 3, 'Under 17', 'Bowling', '1-on-1 Bowling coaching with Coach Ruwan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(33, 'coach Kasun - Tuesday 1:1 Practice Net 1', 'PVT-BAT-TUE-S5-F1-C9', 'private', 'coach', 5, 2, 1, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(34, 'venuk Wickrama - Tuesday 1:1 Practice Net 1', 'PVT-BAT-TUE-S5-F1-C27', 'private', 'coach', 5, 2, 1, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(35, 'Coach Niroshan - Tuesday 1:1 Practice Net 1', 'PVT-BAT-TUE-S5-F1-C30', 'private', 'coach', 5, 2, 1, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(36, 'coach Kasun - Tuesday 1:1 Practice Net 2', 'PVT-BAT-TUE-S5-F2-C9', 'private', 'coach', 5, 2, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(37, 'venuk Wickrama - Tuesday 1:1 Practice Net 2', 'PVT-BAT-TUE-S5-F2-C27', 'private', 'coach', 5, 2, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(38, 'Coach Niroshan - Tuesday 1:1 Practice Net 2', 'PVT-BAT-TUE-S5-F2-C30', 'private', 'coach', 5, 2, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(39, 'coach Dharshana - Tuesday 1:1 Bowling Machine', 'PVT-BOWL-TUE-S5-F3-C11', 'private', 'coach', 5, 2, 3, 'Under 13', 'Bowling', '1-on-1 Bowling coaching with coach Dharshana', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(40, 'Kumara Darmasena - Tuesday 1:1 Bowling Machine', 'PVT-BOWL-TUE-S5-F3-C12', 'private', 'coach', 5, 2, 3, 'Under 11', 'Bowling', '1-on-1 Bowling coaching with Kumara Darmasena', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(41, 'Coach Ruwan - Tuesday 1:1 Bowling Machine', 'PVT-BOWL-TUE-S5-F3-C31', 'private', 'coach', 5, 2, 3, 'Under 17', 'Bowling', '1-on-1 Bowling coaching with Coach Ruwan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(43, 'coach Kasun - Wednesday 1:1 Practice Net 1', 'PVT-BAT-WED-S5-F1-C9', 'private', 'coach', 5, 3, 1, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(44, 'venuk Wickrama - Wednesday 1:1 Practice Net 1', 'PVT-BAT-WED-S5-F1-C27', 'private', 'coach', 5, 3, 1, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(45, 'Coach Niroshan - Wednesday 1:1 Practice Net 1', 'PVT-BAT-WED-S5-F1-C30', 'private', 'coach', 5, 3, 1, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(46, 'coach Kasun - Wednesday 1:1 Practice Net 2', 'PVT-BAT-WED-S5-F2-C9', 'private', 'coach', 5, 3, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(47, 'venuk Wickrama - Wednesday 1:1 Practice Net 2', 'PVT-BAT-WED-S5-F2-C27', 'private', 'coach', 5, 3, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(48, 'Coach Niroshan - Wednesday 1:1 Practice Net 2', 'PVT-BAT-WED-S5-F2-C30', 'private', 'coach', 5, 3, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(49, 'coach Dharshana - Wednesday 1:1 Bowling Machine', 'PVT-BOWL-WED-S5-F3-C11', 'private', 'coach', 5, 3, 3, 'Under 13', 'Bowling', '1-on-1 Bowling coaching with coach Dharshana', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(50, 'Kumara Darmasena - Wednesday 1:1 Bowling Machine', 'PVT-BOWL-WED-S5-F3-C12', 'private', 'coach', 5, 3, 3, 'Under 11', 'Bowling', '1-on-1 Bowling coaching with Kumara Darmasena', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(51, 'Coach Ruwan - Wednesday 1:1 Bowling Machine', 'PVT-BOWL-WED-S5-F3-C31', 'private', 'coach', 5, 3, 3, 'Under 17', 'Bowling', '1-on-1 Bowling coaching with Coach Ruwan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(53, 'coach Kasun - Thursday 1:1 Practice Net 1', 'PVT-BAT-THU-S5-F1-C9', 'private', 'coach', 5, 4, 1, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(54, 'venuk Wickrama - Thursday 1:1 Practice Net 1', 'PVT-BAT-THU-S5-F1-C27', 'private', 'coach', 5, 4, 1, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(55, 'Coach Niroshan - Thursday 1:1 Practice Net 1', 'PVT-BAT-THU-S5-F1-C30', 'private', 'coach', 5, 4, 1, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(56, 'coach Kasun - Thursday 1:1 Practice Net 2', 'PVT-BAT-THU-S5-F2-C9', 'private', 'coach', 5, 4, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(57, 'venuk Wickrama - Thursday 1:1 Practice Net 2', 'PVT-BAT-THU-S5-F2-C27', 'private', 'coach', 5, 4, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(58, 'Coach Niroshan - Thursday 1:1 Practice Net 2', 'PVT-BAT-THU-S5-F2-C30', 'private', 'coach', 5, 4, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(63, 'coach Kasun - Friday 1:1 Practice Net 1', 'PVT-BAT-FRI-S5-F1-C9', 'private', 'coach', 5, 5, 1, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(64, 'venuk Wickrama - Friday 1:1 Practice Net 1', 'PVT-BAT-FRI-S5-F1-C27', 'private', 'coach', 5, 5, 1, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(65, 'Coach Niroshan - Friday 1:1 Practice Net 1', 'PVT-BAT-FRI-S5-F1-C30', 'private', 'coach', 5, 5, 1, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(66, 'coach Kasun - Friday 1:1 Practice Net 2', 'PVT-BAT-FRI-S5-F2-C9', 'private', 'coach', 5, 5, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(67, 'venuk Wickrama - Friday 1:1 Practice Net 2', 'PVT-BAT-FRI-S5-F2-C27', 'private', 'coach', 5, 5, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(68, 'Coach Niroshan - Friday 1:1 Practice Net 2', 'PVT-BAT-FRI-S5-F2-C30', 'private', 'coach', 5, 5, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(69, 'coach Dharshana - Friday 1:1 Bowling Machine', 'PVT-BOWL-FRI-S5-F3-C11', 'private', 'coach', 5, 5, 3, 'Under 13', 'Bowling', '1-on-1 Bowling coaching with coach Dharshana', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(70, 'Kumara Darmasena - Friday 1:1 Bowling Machine', 'PVT-BOWL-FRI-S5-F3-C12', 'private', 'coach', 5, 5, 3, 'Under 11', 'Bowling', '1-on-1 Bowling coaching with Kumara Darmasena', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(71, 'Coach Ruwan - Friday 1:1 Bowling Machine', 'PVT-BOWL-FRI-S5-F3-C31', 'private', 'coach', 5, 5, 3, 'Under 17', 'Bowling', '1-on-1 Bowling coaching with Coach Ruwan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(73, 'coach Kasun - Saturday 1:1 Practice Net 1', 'PVT-BAT-SAT-S5-F1-C9', 'private', 'coach', 5, 6, 1, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(74, 'venuk Wickrama - Saturday 1:1 Practice Net 1', 'PVT-BAT-SAT-S5-F1-C27', 'private', 'coach', 5, 6, 1, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(75, 'Coach Niroshan - Saturday 1:1 Practice Net 1', 'PVT-BAT-SAT-S5-F1-C30', 'private', 'coach', 5, 6, 1, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(76, 'coach Kasun - Saturday 1:1 Practice Net 2', 'PVT-BAT-SAT-S5-F2-C9', 'private', 'coach', 5, 6, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(77, 'venuk Wickrama - Saturday 1:1 Practice Net 2', 'PVT-BAT-SAT-S5-F2-C27', 'private', 'coach', 5, 6, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(78, 'Coach Niroshan - Saturday 1:1 Practice Net 2', 'PVT-BAT-SAT-S5-F2-C30', 'private', 'coach', 5, 6, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(79, 'coach Dharshana - Saturday 1:1 Bowling Machine', 'PVT-BOWL-SAT-S5-F3-C11', 'private', 'coach', 5, 6, 3, 'Under 13', 'Bowling', '1-on-1 Bowling coaching with coach Dharshana', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(80, 'Kumara Darmasena - Saturday 1:1 Bowling Machine', 'PVT-BOWL-SAT-S5-F3-C12', 'private', 'coach', 5, 6, 3, 'Under 11', 'Bowling', '1-on-1 Bowling coaching with Kumara Darmasena', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(81, 'Coach Ruwan - Saturday 1:1 Bowling Machine', 'PVT-BOWL-SAT-S5-F3-C31', 'private', 'coach', 5, 6, 3, 'Under 17', 'Bowling', '1-on-1 Bowling coaching with Coach Ruwan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(83, 'coach Kasun - Sunday 1:1 Practice Net 1', 'PVT-BAT-SUN-S5-F1-C9', 'private', 'coach', 5, 7, 1, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(84, 'venuk Wickrama - Sunday 1:1 Practice Net 1', 'PVT-BAT-SUN-S5-F1-C27', 'private', 'coach', 5, 7, 1, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(85, 'Coach Niroshan - Sunday 1:1 Practice Net 1', 'PVT-BAT-SUN-S5-F1-C30', 'private', 'coach', 5, 7, 1, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(86, 'coach Kasun - Sunday 1:1 Practice Net 2', 'PVT-BAT-SUN-S5-F2-C9', 'private', 'coach', 5, 7, 2, 'Under 13', 'Batting', '1-on-1 Batting coaching with coach Kasun', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(87, 'venuk Wickrama - Sunday 1:1 Practice Net 2', 'PVT-BAT-SUN-S5-F2-C27', 'private', 'coach', 5, 7, 2, 'Under 17', 'Batting', '1-on-1 Batting coaching with venuk Wickrama', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(88, 'Coach Niroshan - Sunday 1:1 Practice Net 2', 'PVT-BAT-SUN-S5-F2-C30', 'private', 'coach', 5, 7, 2, 'Under 11', 'Batting', '1-on-1 Batting coaching with Coach Niroshan', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(89, 'coach Dharshana - Sunday 1:1 Bowling Machine', 'PVT-BOWL-SUN-S5-F3-C11', 'private', 'coach', 5, 7, 3, 'Under 13', 'Bowling', '1-on-1 Bowling coaching with coach Dharshana', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(90, 'Kumara Darmasena - Sunday 1:1 Bowling Machine', 'PVT-BOWL-SUN-S5-F3-C12', 'private', 'coach', 5, 7, 3, 'Under 11', 'Bowling', '1-on-1 Bowling coaching with Kumara Darmasena', 1, 2500.00, 'private_sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:52:27'),
(91, 'Coach Ruwan - Sunday 1:1 Bowling Machine', 'PVT-BOWL-SUN-S5-F3-C31', 'private', 'coach', 5, 7, 3, 'Under 17', 'Bowling', '1-on-1 Bowling coaching with Coach Ruwan', NULL, 0.00, 'sessions', 1, 1, '2026-04-14 15:24:06', '2026-04-14 15:44:11'),
(181, 'Under 11 Batting Group - Practice Net 1', 'U11-BAT-PN1', 'program', 'coach', 4, 1, 1, 'Under 11', 'batting', 'Weekly Under 11 batting group session at Practice Net 1', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(182, 'Under 11 Bowling Group - Bowling Machine', 'U11-BOWL-BM', 'program', 'coach', 4, 4, 1, 'Under 11', 'bowling', 'Weekly Under 11 bowling group session at Practice Net 1', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(183, 'Under 11 Fielding Group - Main Ground', 'U11-FLD-MG', 'program', 'coach', 3, 5, 4, 'Under 11', 'fielding', 'Weekly Under 11 fielding group session at Main Ground', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(184, 'Under 13 Batting Group - Practice Net 1', 'U13-BAT-PN1', 'program', 'coach', 3, 2, 1, 'Under 13', 'batting', 'Weekly Under 13 batting group session at Practice Net 1', 2, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(185, 'Under 13 Bowling Group - Bowling Machine', 'U13-BOWL-PN1', 'program', 'coach', 3, 4, 1, 'Under 13', 'bowling', 'Weekly Under 13 bowling group session at Practice Net 1', 2, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(186, 'Under 13 Fielding Group - Main Ground', 'U13-FLD-MG', 'program', 'coach', 4, 5, 4, 'Under 13', 'fielding', 'Weekly Under 13 fielding group session at Main Ground', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(187, 'Under 15 Batting Group - Practice Net 1', 'U15-BAT-PN2', 'program', 'coach', 4, 2, 1, 'Under 15', 'batting', 'Weekly Under 15 batting group session at Practice Net 1', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(188, 'Under 15 Bowling Group - Bowling Machine', 'U15-BOWL-BM', 'program', 'coach', 3, 4, 3, 'Under 15', 'bowling', 'Weekly Under 15 bowling group session at Bowling Machine', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(189, 'Under 15 Fielding Group - Main Ground', 'U15-FLD-MG', 'program', 'coach', 3, 2, 4, 'Under 15', 'fielding', 'Weekly Under 15 fielding group session at Main Ground', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(190, 'Under 17 Batting Group - Practice Net 1', 'U17-BAT-PN2', 'program', 'coach', 3, 5, 1, 'Under 17', 'batting', 'Weekly Under 17 batting group session at Practice Net 1', 2, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(191, 'Under 17 Bowling Group - Bowling Machine', 'U17-BOWL-BM', 'program', 'coach', 4, 1, 3, 'Under 17', 'bowling', 'Weekly Under 17 bowling group session at Bowling Machine', 11, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(192, 'Under 17 Fielding Group - Main Ground', 'U17-FLD-MG', 'program', 'coach', 3, 3, 4, 'Under 17', 'fielding', 'Weekly Under 17 fielding group session at Main Ground', 2, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(193, 'Under 19 Batting Group - Practice Net 1', 'U19-BAT-PN2', 'program', 'coach', 4, 5, 1, 'Under 19', 'batting', 'Weekly Under 19 batting group session at Practice Net 1', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(194, 'Under 19 Bowling Group - Bowling Machine', 'U19-BOWL-BM', 'program', 'coach', 4, 1, 3, 'Under 19', 'bowling', 'Weekly Under 19 bowling group session at Bowling Machine', 10, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(195, 'Under 19 Fielding Group - Main Ground', 'U19-FLD-MG', 'program', 'coach', 4, 2, 4, 'Under 19', 'fielding', 'Weekly Under 19 fielding group session at Main Ground', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(196, 'Under 21 Batting Group - Practice Net 1', 'U21-BAT-PN1', 'program', 'coach', 1, 6, 1, 'Under 21', 'batting', 'Weekly Under 21 batting group session at Practice Net 1', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(197, 'Under 21 Bowling Group - Bowling Machine', 'U21-BOWL-BM', 'program', 'coach', 4, 2, 3, 'Under 21', 'bowling', 'Weekly Under 21 bowling group session at Bowling Machine', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(198, 'Under 21 Fielding Group - Main Ground', 'U21-FLD-MG', 'program', 'coach', 4, 3, 4, 'Under 21', 'fielding', 'Weekly Under 21 fielding group session at Main Ground', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(199, 'Open Batting Group - Practice Net 2', 'OPEN-BAT-PN2', 'program', 'coach', 5, 1, 1, 'Open', 'batting', 'Weekly Open batting group session at Practice Net 1', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(200, 'Open Bowling Group - Bowling Machine', 'OPEN-BOWL-BM', 'program', 'coach', 5, 4, 3, 'Open', 'bowling', 'Weekly Open bowling group session at Bowling Machine', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23'),
(201, 'Open Fielding Group - Main Ground', 'OPEN-FLD-MG', 'program', 'coach', 1, 6, 4, 'Open', 'fielding', 'Weekly Open fielding group session at Main Ground', 0, 0.00, 'sessions', 1, 1, '2026-04-15 01:55:23', '2026-04-15 01:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `slot_template_player_assignment`
--

CREATE TABLE `slot_template_player_assignment` (
  `TemplateID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `CoachingType` enum('batting','bowling','fielding') DEFAULT NULL,
  `AgeGroup` varchar(50) DEFAULT NULL,
  `AssignmentSource` enum('auto_plan','admin_manual','system_refresh') NOT NULL DEFAULT 'auto_plan',
  `AssignedBy` int(11) DEFAULT NULL,
  `AssignedAt` datetime DEFAULT current_timestamp(),
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `Notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slot_template_staff`
--

CREATE TABLE `slot_template_staff` (
  `ID` int(11) NOT NULL,
  `TemplateID` int(11) NOT NULL COMMENT 'FK → slot_template',
  `UserID` int(11) NOT NULL COMMENT 'FK → user (Role = Coach OR Trainer)',
  `StaffType` enum('coach','trainer') NOT NULL,
  `StaffRole` enum('lead','assistant') NOT NULL DEFAULT 'lead',
  `AssignedBy` int(11) NOT NULL COMMENT 'FK → user (Admin)',
  `AssignedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Coaches and trainers assigned to a template. UNIQUE(TemplateID, UserID) prevents duplicates.';

--
-- Dumping data for table `slot_template_staff`
--

INSERT INTO `slot_template_staff` (`ID`, `TemplateID`, `UserID`, `StaffType`, `StaffRole`, `AssignedBy`, `AssignedAt`) VALUES
(4, 26, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(5, 27, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(6, 28, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(7, 29, 11, 'coach', '', 1, '2026-04-14 15:52:27'),
(8, 30, 12, 'coach', '', 1, '2026-04-14 15:52:27'),
(9, 31, 31, 'coach', '', 1, '2026-04-14 15:52:27'),
(11, 33, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(12, 34, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(13, 35, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(14, 36, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(15, 37, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(16, 38, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(17, 39, 11, 'coach', '', 1, '2026-04-14 15:52:27'),
(18, 40, 12, 'coach', '', 1, '2026-04-14 15:52:27'),
(19, 41, 31, 'coach', '', 1, '2026-04-14 15:52:27'),
(21, 43, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(22, 44, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(23, 45, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(24, 46, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(25, 47, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(26, 48, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(27, 49, 11, 'coach', '', 1, '2026-04-14 15:52:27'),
(28, 50, 12, 'coach', '', 1, '2026-04-14 15:52:27'),
(29, 51, 31, 'coach', '', 1, '2026-04-14 15:52:27'),
(31, 53, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(32, 54, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(33, 55, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(34, 56, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(35, 57, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(36, 58, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(41, 63, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(42, 64, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(43, 65, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(44, 66, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(45, 67, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(46, 68, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(47, 69, 11, 'coach', '', 1, '2026-04-14 15:52:27'),
(48, 70, 12, 'coach', '', 1, '2026-04-14 15:52:27'),
(49, 71, 31, 'coach', '', 1, '2026-04-14 15:52:27'),
(51, 73, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(52, 74, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(53, 75, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(54, 76, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(55, 77, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(56, 78, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(57, 79, 11, 'coach', '', 1, '2026-04-14 15:52:27'),
(58, 80, 12, 'coach', '', 1, '2026-04-14 15:52:27'),
(59, 81, 31, 'coach', '', 1, '2026-04-14 15:52:27'),
(61, 83, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(62, 84, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(63, 85, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(64, 86, 9, 'coach', '', 1, '2026-04-14 15:52:27'),
(65, 87, 27, 'coach', '', 1, '2026-04-14 15:52:27'),
(66, 88, 30, 'coach', '', 1, '2026-04-14 15:52:27'),
(67, 89, 11, 'coach', '', 1, '2026-04-14 15:52:27'),
(68, 90, 12, 'coach', '', 1, '2026-04-14 15:52:27'),
(69, 91, 31, 'coach', '', 1, '2026-04-14 15:52:27'),
(128, 181, 30, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(129, 182, 12, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(130, 183, 121, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(131, 184, 9, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(132, 185, 11, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(133, 186, 121, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(134, 187, 9, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(135, 188, 11, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(136, 189, 3, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(137, 190, 27, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(138, 191, 31, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(139, 192, 3, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(140, 193, 27, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(141, 194, 31, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(142, 195, 3, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(143, 196, 9, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(144, 197, 31, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(145, 198, 3, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(146, 199, 30, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(147, 200, 12, 'coach', 'lead', 1, '2026-04-15 01:55:23'),
(148, 201, 121, 'coach', 'lead', 1, '2026-04-15 01:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `slot_time_band`
--

CREATE TABLE `slot_time_band` (
  `SlotID` tinyint(4) NOT NULL,
  `SlotLabel` varchar(50) NOT NULL COMMENT '"09:00 AM – 11:00 AM" — used in dropdowns',
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `DurationMinutes` smallint(6) NOT NULL DEFAULT 120,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fixed time bands. Toggle IsActive only — never DELETE rows.';

--
-- Dumping data for table `slot_time_band`
--

INSERT INTO `slot_time_band` (`SlotID`, `SlotLabel`, `StartTime`, `EndTime`, `DurationMinutes`, `IsActive`) VALUES
(1, '09:00 AM – 11:00 AM', '09:00:00', '11:00:00', 120, 1),
(2, '11:00 AM – 01:00 PM', '11:00:00', '13:00:00', 120, 1),
(3, '02:00 PM – 04:00 PM', '14:00:00', '16:00:00', 120, 1),
(4, '04:00 PM – 06:00 PM', '16:00:00', '18:00:00', 120, 1),
(5, '06:00 PM – 08:00 PM', '18:00:00', '20:00:00', 120, 1),
(6, '08:00 PM – 10:00 PM', '20:00:00', '22:00:00', 120, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptionpayment`
--

CREATE TABLE `subscriptionpayment` (
  `PaymentID` int(11) NOT NULL,
  `SubscriptionID` int(11) NOT NULL,
  `BillingMonth` date GENERATED ALWAYS AS (`DueDate` - interval dayofmonth(`DueDate`) - 1 day) STORED COMMENT 'First day of billing month derived from DueDate',
  `PaymentDate` date DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('cash','card','bank_transfer','online') NOT NULL,
  `Status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `PaymentReference` varchar(100) DEFAULT NULL,
  `Gateway` enum('manual','payhere') NOT NULL DEFAULT 'manual',
  `GatewayOrderId` varchar(50) DEFAULT NULL,
  `GatewayPaymentId` varchar(50) DEFAULT NULL,
  `DueDate` date NOT NULL,
  `ProcessedBy` int(11) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `PaidAt` datetime DEFAULT NULL,
  `FailedAt` datetime DEFAULT NULL,
  `RefundedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Monthly subscription payments';

--
-- Dumping data for table `subscriptionpayment`
--

INSERT INTO `subscriptionpayment` (`PaymentID`, `SubscriptionID`, `PaymentDate`, `Amount`, `PaymentMethod`, `Status`, `PaymentReference`, `Gateway`, `GatewayOrderId`, `GatewayPaymentId`, `DueDate`, `ProcessedBy`, `Notes`, `CreatedAt`, `UpdatedAt`, `PaidAt`, `FailedAt`, `RefundedAt`) VALUES
(4, 8, NULL, 4500.00, 'online', 'pending', NULL, 'manual', NULL, NULL, '2026-04-14', NULL, 'Membership is pending. Pay to experience the whole academy services.', '2026-04-12 19:21:49', '2026-04-12 19:21:49', NULL, NULL, NULL),
(5, 1, NULL, 7000.00, 'online', 'pending', NULL, 'manual', NULL, NULL, '2026-01-14', NULL, 'Membership is pending. Pay to experience the whole academy services.', '2026-04-12 21:04:19', '2026-04-12 21:04:19', NULL, NULL, NULL),
(6, 3, NULL, 10000.00, 'online', 'pending', NULL, 'manual', NULL, NULL, '2026-04-14', NULL, 'Membership is pending. Pay to experience the whole academy services.', '2026-04-12 21:04:19', '2026-04-12 21:04:19', NULL, NULL, NULL),
(7, 4, NULL, 4500.00, 'online', 'pending', NULL, 'manual', NULL, NULL, '2026-04-14', NULL, 'Membership is pending. Pay to experience the whole academy services.', '2026-04-12 21:04:19', '2026-04-12 21:04:19', NULL, NULL, NULL),
(8, 6, NULL, 4500.00, 'online', 'pending', NULL, 'manual', NULL, NULL, '2026-04-14', NULL, 'Membership is pending. Pay to experience the whole academy services.', '2026-04-12 21:04:19', '2026-04-12 21:04:19', NULL, NULL, NULL),
(9, 7, NULL, 4500.00, 'online', 'pending', NULL, 'manual', NULL, NULL, '2026-04-14', NULL, 'Membership is pending. Pay to experience the whole academy services.', '2026-04-12 21:04:19', '2026-04-12 21:04:19', NULL, NULL, NULL),
(14, 9, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_ayaan01', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(15, 10, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_mihin02', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(16, 11, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_nethuka05', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(17, 12, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_ravin03', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(18, 13, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_sadev04', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(19, 14, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_chamith04', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(20, 15, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_dulneth02', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(21, 16, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_hasitha03', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(22, 17, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_imeth05', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(23, 18, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_kavindu01', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(24, 19, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_ashen03', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(25, 20, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_dineth05', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(26, 21, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_nethran02', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(27, 22, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_pasindu01', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(28, 23, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_tharindu04', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(29, 24, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_hirun01', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(30, 25, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_kethmi02', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(31, 26, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_malith03', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(32, 27, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_ruvin05', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(33, 28, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_sanjana04', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(34, 29, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_dulaj04', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(35, 30, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_nisal03', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(36, 31, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_pasan02', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(37, 32, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_prabath05', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(38, 33, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_sahan01', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(39, 40, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_06', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(40, 41, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_07', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(41, 42, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_08', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(42, 43, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_09', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(43, 44, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u13_10', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(44, 45, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_06', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(45, 46, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_07', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(46, 47, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_08', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(47, 48, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_09', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(48, 49, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u15_10', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(49, 50, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_06', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(50, 51, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_07', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(51, 52, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_08', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(52, 53, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_09', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(53, 54, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u17_10', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(54, 55, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_06', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(55, 56, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_07', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(56, 57, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_08', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(57, 58, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_09', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(58, 59, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u19_10', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(59, 60, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_06', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(60, 61, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_07', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(61, 62, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_08', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(62, 63, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_09', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(63, 64, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-u21_10', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(64, 65, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_01', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(65, 66, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_02', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(66, 67, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_03', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(67, 68, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_04', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(68, 69, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_05', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(69, 70, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_06', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(70, 71, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_07', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(71, 72, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_08', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(72, 73, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_09', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL),
(73, 74, '2026-04-14', 4500.00, 'online', 'completed', 'APR2026-uopen_10', 'manual', NULL, NULL, '2026-04-14', 5, 'Seed payment - April 2026 general membership fee', '2026-04-14 20:19:26', '2026-04-14 20:19:26', '2026-04-14 20:19:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplementplan`
--

CREATE TABLE `supplementplan` (
  `PlanID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `SupplementPlanName` varchar(255) DEFAULT NULL,
  `SupplementDetails` text NOT NULL,
  `Notes` text DEFAULT NULL,
  `Dosage` varchar(255) DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Supplement recommendations for players';

--
-- Dumping data for table `supplementplan`
--

INSERT INTO `supplementplan` (`PlanID`, `TrainerID`, `SupplementPlanName`, `SupplementDetails`, `Notes`, `Dosage`, `Duration`, `Status`, `CreatedDate`) VALUES
(1, 4, 'Strength & Muscle Gain Plan', 'Goal: Support lean muscle gain and strength development.\r\n\r\nIncludes:\r\n- Whey Protein\r\n- Creatine\r\n- Multivitamins\r\n\r\nGuidelines:\r\n- Take protein after training.\r\n- Creatine should be taken daily, even on rest days.\r\n- Multivitamin with breakfast.', '', 'Whey Protein: 25g post-workout; Creatine: 5g daily; Multivitamin: 1 tablet daily', 20, 'active', '2026-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `supplement_player`
--

CREATE TABLE `supplement_player` (
  `PlanID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AssignedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplement_player`
--

INSERT INTO `supplement_player` (`PlanID`, `PlayerID`, `AssignedDate`) VALUES
(1, 15, '2026-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `TournamentID` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `AgeGroup` varchar(50) DEFAULT NULL,
  `Format` enum('T20','ODI','Test','Other') DEFAULT 'T20',
  `Description` text DEFAULT NULL,
  `tdate` date NOT NULL COMMENT 'Tournament date',
  `RegistrationDeadline` date DEFAULT NULL,
  `MaxPlayers` tinyint(3) UNSIGNED DEFAULT NULL,
  `Location` varchar(200) DEFAULT NULL,
  `CreatedBy` int(11) NOT NULL COMMENT 'FK -> user.UserID (admin)',
  `Status` enum('created','registration_open','registration_closed','team_announced','ongoing','completed','cancelled') NOT NULL DEFAULT 'created',
  `IsTeamAnnounced` tinyint(1) NOT NULL DEFAULT 0,
  `CancelReason` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `PrizePool` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cricket tournaments';

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`TournamentID`, `Name`, `AgeGroup`, `Format`, `Description`, `tdate`, `RegistrationDeadline`, `MaxPlayers`, `Location`, `CreatedBy`, `Status`, `IsTeamAnnounced`, `CancelReason`, `CreatedAt`, `UpdatedAt`, `PrizePool`) VALUES
(6, 'Monsoon Cricket League', 'Under 13', 'T20', '', '2026-06-15', '2026-05-15', 20, 'Riverside Cricket Ground', 1, 'ongoing', 1, NULL, '2026-04-10 11:01:59', '2026-04-10 14:35:29', 30000.00),
(7, 'Premier Division T20', NULL, 'T20', NULL, '2026-04-01', NULL, NULL, 'Central Stadium', 1, 'created', 0, NULL, '2026-04-10 11:01:59', '2026-04-10 12:27:28', 80000.00),
(8, 'Academy Invitational Cup', NULL, 'T20', NULL, '2026-05-10', NULL, NULL, 'Training Complex A', 1, 'registration_open', 0, NULL, '2026-04-10 11:01:59', '2026-04-10 15:58:43', 20000.00),
(9, 'District Cricket Championship', NULL, 'T20', NULL, '2026-02-28', NULL, NULL, 'District Sports Arena', 1, 'created', 0, NULL, '2026-04-10 11:01:59', '2026-04-10 12:27:28', 40000.00),
(10, 'Veterans Cricket Tournament', NULL, 'T20', NULL, '2026-03-15', NULL, NULL, 'Memorial Ground', 1, 'created', 0, NULL, '2026-04-10 11:01:59', '2026-04-10 12:27:28', 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tournamentplayer`
--

CREATE TABLE `tournamentplayer` (
  `TournamentPlayerID` int(11) NOT NULL,
  `TournamentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'FK -> user.UserID',
  `Team` varchar(100) DEFAULT 'Academy Team',
  `RoleInTeam` varchar(100) DEFAULT NULL,
  `SelectedBy` int(11) DEFAULT NULL COMMENT 'FK -> user.UserID (head coach)',
  `SelectionStatus` enum('draft','confirmed') NOT NULL DEFAULT 'draft',
  `SelectedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Players selected for tournaments';

-- --------------------------------------------------------

--
-- Table structure for table `tournament_join_request`
--

CREATE TABLE `tournament_join_request` (
  `RequestID` int(11) NOT NULL,
  `TournamentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'FK -> user.UserID',
  `Message` text DEFAULT NULL,
  `Status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ReviewedBy` int(11) DEFAULT NULL COMMENT 'FK -> user.UserID (admin/coach)',
  `ReviewNotes` text DEFAULT NULL,
  `RequestedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `ReviewedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_join_request`
--

INSERT INTO `tournament_join_request` (`RequestID`, `TournamentID`, `PlayerID`, `Message`, `Status`, `ReviewedBy`, `ReviewNotes`, `RequestedAt`, `ReviewedAt`) VALUES
(2, 8, 15, '', 'pending', NULL, NULL, '2026-04-10 16:01:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trainerappointment`
--

CREATE TABLE `trainerappointment` (
  `AppointmentID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AppointmentDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Status` enum('scheduled','completed','cancelled','rescheduled') DEFAULT 'scheduled',
  `Reason` text DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='One-on-one appointments between trainers and players';

-- --------------------------------------------------------

--
-- Table structure for table `trainerprofile`
--

CREATE TABLE `trainerprofile` (
  `TrainerID` int(11) NOT NULL,
  `Experience` int(11) DEFAULT NULL COMMENT 'Years of experience',
  `Certifications` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Physical trainer profile information';

--
-- Dumping data for table `trainerprofile`
--

INSERT INTO `trainerprofile` (`TrainerID`, `Experience`, `Certifications`) VALUES
(4, NULL, NULL),
(10, NULL, NULL),
(19, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trainerreview`
--

CREATE TABLE `trainerreview` (
  `ReviewID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `ProgramEffectiveness` int(11) NOT NULL COMMENT 'Rating 1-5 for program effectiveness',
  `MotivationSkills` int(11) NOT NULL COMMENT 'Rating 1-5 for motivation',
  `Professionalism` int(11) NOT NULL COMMENT 'Rating 1-5 for professionalism',
  `Knowledge` int(11) NOT NULL COMMENT 'Rating 1-5 for fitness knowledge',
  `OverallRating` decimal(3,1) DEFAULT NULL COMMENT 'Calculated average of all ratings',
  `ReviewTitle` varchar(255) DEFAULT NULL,
  `ReviewText` text DEFAULT NULL,
  `WouldRecommend` tinyint(1) DEFAULT 1,
  `ReviewDate` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected','flagged') DEFAULT 'approved',
  `TrainerResponse` text DEFAULT NULL COMMENT 'Trainer response to the review',
  `ResponseDate` datetime DEFAULT NULL COMMENT 'When trainer responded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainer_tournament_recommendations`
--

CREATE TABLE `trainer_tournament_recommendations` (
  `RecommendationID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL COMMENT 'FK -> user.UserID',
  `TournamentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL COMMENT 'FK -> user.UserID',
  `RecommendedRole` varchar(100) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `Comments` text DEFAULT NULL,
  `Status` enum('pending','reviewed','confirmed','rejected') NOT NULL DEFAULT 'pending',
  `ReviewedBy` int(11) DEFAULT NULL COMMENT 'FK -> user.UserID (head coach)',
  `DateRecommended` timestamp NOT NULL DEFAULT current_timestamp(),
  `DateReviewed` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL DEFAULT '',
  `DateOfBirth` date NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` text DEFAULT NULL,
  `School` varchar(255) DEFAULT NULL COMMENT 'School or educational institution',
  `Role` enum('Admin','ShopEmployee','Coach','Trainer','Player') NOT NULL,
  `Username` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `DateJoined` datetime DEFAULT current_timestamp(),
  `Status` enum('active','inactive') DEFAULT 'active',
  `RequiresPasswordChange` tinyint(1) DEFAULT 0 COMMENT 'Force password change on first login',
  `PasswordChangeDeadline` datetime DEFAULT NULL COMMENT 'Deadline for mandatory password change',
  `LastLoginAt` datetime DEFAULT NULL COMMENT 'Track last successful login',
  `LoginAttempts` int(11) DEFAULT 0 COMMENT 'Failed login attempts counter',
  `AccountLockedUntil` datetime DEFAULT NULL COMMENT 'Account lock expiry time',
  `CreatedBy` int(11) DEFAULT NULL COMMENT 'Admin who created this user',
  `Notes` text DEFAULT NULL COMMENT 'Admin notes about the user',
  `ProfileImage` varchar(255) DEFAULT NULL COMMENT 'Relative path to user profile image (e.g., uploads/profile_images/profile_1_123456789.jpg)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Core user table with enhanced security and role-based access control';

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `PasswordChangeDeadline`, `LastLoginAt`, `LoginAttempts`, `AccountLockedUntil`, `CreatedBy`, `Notes`, `ProfileImage`) VALUES
(1, 'Sarath ', 'Kothalawala', '1970-01-20', '+1234567890', 'admin@cricketacademy.com', 'Academy Headquarters', 'Visakha Vidyalaya', 'Admin', 'admin', '$2y$10$uxNNNmvcYhBxqIzjZb6VmeUAkRRRSGq0ikEsabLFexzDPhNpGyLSq', '2025-10-18 13:02:42', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_1_1771315014.png'),
(2, 'Academy manager', '', '1990-01-01', '+1234567890', 'manager@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Admin', 'Manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(3, 'Coach Sarath', '', '1990-01-01', '+1234567890', 'coach001@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Coach', 'coach001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(4, 'Trainer Hasitha', '', '1990-01-01', '+1234567890', 'trainer001@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Trainer', 'trainer001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(5, 'Shop Muditha', '', '1990-01-01', '+1234567890', 'shop001@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'ShopEmployee', 'shop001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(6, 'Player Esandu', '', '1990-01-01', '+1234567890', 'esandu@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Player', 'esandu001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(7, 'Vijini', '', '1990-01-01', '+1234567890', 'vijini@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Player', 'avijini001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:08:42', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(8, 'admin2', '', '1990-01-01', '+1234567890', 'admin2@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Admin', 'admin2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:10:01', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_8_1761147214.png'),
(9, 'coach Kasun', '', '1990-01-01', '+1234567890', 'coach002@celiteca.com', 'Academy Address', 'Elite Cricket Academy', 'Coach', 'coach002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:12:47', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(10, 'Trainer subanu', '', '1990-01-01', '+1234567890', 'trainer002@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Trainer', 'trainer002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:13:52', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(11, 'coach Dharshana', '', '1990-01-01', '+1234567890', 'coach004@eliteca.com', 'Academy Headquarters', NULL, 'Coach', 'coach004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 23:38:52', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_11_1761114831.png'),
(12, 'Kumara Darmasena', '', '1980-01-01', '0771234567', 'kumara.darmasena@eliteacademy.com', NULL, NULL, 'Coach', 'kdarmasena', '$2y$10$.UJUjUwSObzJmzTGsJcL8.VfyJZ1f88FhX6Hl6RWbKyXCQf/lViPq', '2025-10-18 23:44:38', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(13, 'Ben Carter', '', '1980-01-01', '0771234567', 'ben.carter@eliteacademy.com', NULL, NULL, 'Coach', 'bcarter', '$2y$10$vlnExCdpdUaOrioO1BYegu2OEhqSUwfwAMGM.o4hK46NL9GkaEPBe', '2025-10-18 23:44:38', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(14, 'Kumar Sangakkara', '', '1980-01-01', '0771234567', 'kumar.sangakkara@eliteacademy.com', NULL, NULL, 'Coach', 'ksangakkara', '$2y$10$.UW163sEsWJ8e9NT/wnfi.6mf.cG0asQqhzBd5aZo9b12avkfEJX2', '2025-10-18 23:44:38', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(15, 'Swairi', '', '2010-01-01', '+1234567890', 'swairi@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Player', 'Swairi', '$2y$10$GRyoc1IyfcpNy9dB.g0cHeVZRgf4EiX6igVL3Y3KcKm.06fzVi.Py', '2025-10-19 21:30:13', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_15_1761129401.jpeg'),
(16, 'esandu', '', '2003-06-04', '0123456789', 'e@g.com', '193/55,nugegoda', 'SLIIT', 'Player', 'esandu', '$2y$10$wzIcyBsyqcjG0KLOe5MUA.pPr33Vv704h2N27KW7a3IS3VupuzQ0m', '2025-10-22 13:22:54', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(18, 'vijini liyanamana', '', '2002-07-10', '0123456789', 'jini.a.lm2@gmail.com', '193/55,nugegoda', 'SLIIT', 'Player', 'vijinia', '$2y$10$r6Oe1uymncsattPzIRvxVu7gT8Cdf28rbc0YSYDfilYpEwxiwQr1q', '2025-10-22 21:19:54', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_18_1761148566.png'),
(19, 'Kumara Dharmasena', '', '2002-06-22', '+94755748105', 'coach@cricketacademy.com', '123/5 , kottawa', 'Elite Cricket Academy', 'Trainer', 'kumar2', '$2y$10$xJcohxTmXRnX3TacRRfvs..U7uu9nDueeGVe02LvLkT3VOTpR9pVS', '2025-10-22 21:30:39', 'active', 0, NULL, NULL, 0, NULL, 8, NULL, NULL),
(20, 'esandu yapa', '', '2000-03-05', '0774267307', 'esanduepa0225@gmail.com', 'Deiyandarawatta, Panvila', 'RCG', 'Player', 'esandu12', '$2y$10$U1rmUEsrO85uogg0/U7HbeRYM.WSGgZNumnBy8o0ba535z3S/oqTm', '2025-10-23 10:08:20', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(21, 'Esandu Epa', '', '2003-02-25', '+94774267307', 'esanduepa022555@gmail.com', 'Deiyandarawatta, Panvila', 'RCG', 'Player', 'essa1234', '$2y$10$LX1eoz9bIOx.FhApFOFMBOGNrPQ61pQ6hqaMNmb.wDX/0N.gv4c3.', '2026-01-17 09:19:41', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(22, 'Esandu Epa', '', '2003-02-25', '+94774267307', 'esanduepa0225777@gmail.com', 'Deiyandarawatta, Panvila', 'RCG', 'Player', 'essa234', '$2y$10$QFq5Z39i8dbGcHpyqSHMDO9NfgNxENwEfHdxv8lBFr3Pj6aCgAwa2', '2026-02-03 14:11:50', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(25, 'shalitha', '', '2003-02-17', '0765948665', 'jina.lm2@gmail.com', 'marar', NULL, 'Player', 'mara', '$2y$10$qv2qb6moG.vnEkrCJJihceu4.zWy5oohEZ0zdddAeWhiVaELa9bFK', '2026-02-17 13:57:36', 'active', 0, NULL, NULL, 0, NULL, 6, NULL, NULL),
(26, 'Vijini Lr', '', '2014-02-02', '0714565321', 'vijinialm2417@gmail.com', '12/1 , kaduwela, colombo', 'lyceum', 'Player', 'vijinialm', '$2y$10$hvrH05rzkrrrA4AHOIa1nO6RuUtFQ8aHOjiymyUjdM4zZ4Rt5esby', '2026-04-10 08:06:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(27, 'venuk Wickrama', '', '2003-07-10', '0745861236', 'venukw@gmail.com', 'kottawa', NULL, 'Coach', 'venukw', '$2y$10$3sItSkvk.7G7MT7TN.ADXu.jaMaNjqhBstey3OcMatNyYuwcPF2J2', '2026-04-10 19:32:16', 'active', 0, NULL, NULL, 0, NULL, 8, NULL, NULL),
(28, 'Samitha', 'Rathnayaka', '2009-02-14', '0178057303', 'samitha@gmail.com', '12/B , Kottawa, pannipitiya', 'Royal College', 'Player', 'samitha', '$2y$10$7v3m.44dIUSOtsKNXr/IWuFcVBGEhNPTGtJTLI1QcKtbqVwHJmUGi', '2026-04-12 15:25:28', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(29, 'thilan', 'jayasinghe', '2014-05-14', '0758956123', 'thilan@gmail.com', '34/A, pagoda, Nugegoda', 'Ananda College', 'Player', 'thilan', '$2y$10$9EdQi0vn.AMkFpu.wFIzguHRBkEPle19vjbDxZ/QhMH6PEYm6qD2a', '2026-04-12 15:49:26', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(30, 'Coach', 'Niroshan', '1988-05-14', '0771112201', 'niroshan.coach@eliteca.com', 'Academy HQ', 'Elite Cricket Academy', 'Coach', 'coach201', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 13:13:04', 'active', 0, NULL, NULL, 0, NULL, 1, 'Sample batting coach', NULL),
(31, 'Coach', 'Ruwan', '1990-11-02', '0771112202', 'ruwan.coach@eliteca.com', 'Academy HQ', 'Elite Cricket Academy', 'Coach', 'coach202', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 13:13:04', 'active', 0, NULL, NULL, 0, NULL, 1, 'Sample bowling coach', NULL),
(32, 'Ayaan', 'Perera', '2014-02-14', '0772001001', 'ayaan.u13_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_ayaan01', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:49', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(33, 'Mihin', 'Silva', '2014-09-20', '0772001002', 'mihin.u13_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_mihin02', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:49', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(34, 'Ravin', 'Fernando', '2015-03-10', '0772001003', 'ravin.u13_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_ravin03', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(35, 'Sadev', 'Jayasinghe', '2013-12-30', '0772001004', 'sadev.u13_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_sadev04', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(36, 'Nethuka', 'Weerasinghe', '2014-06-01', '0772001005', 'nethuka.u13_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_nethuka05', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(37, 'Kavindu', 'Perera', '2011-06-01', '0772001011', 'kavindu.u15_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_kavindu01', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(38, 'Dulneth', 'Silva', '2012-02-14', '0772001012', 'dulneth.u15_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_dulneth02', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(39, 'Hasitha', 'Fernando', '2011-09-20', '0772001013', 'hasitha.u15_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_hasitha03', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(40, 'Chamith', 'Jayasinghe', '2012-03-10', '0772001014', 'chamith.u15_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_chamith04', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(41, 'Imeth', 'Weerasinghe', '2011-12-30', '0772001015', 'imeth.u15_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_imeth05', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(42, 'Pasindu', 'Perera', '2009-06-01', '0772001021', 'pasindu.u17_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_pasindu01', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(43, 'Nethran', 'Silva', '2010-02-14', '0772001022', 'nethran.u17_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_nethran02', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(44, 'Ashen', 'Fernando', '2009-09-20', '0772001023', 'ashen.u17_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_ashen03', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(45, 'Tharindu', 'Jayasinghe', '2010-03-10', '0772001024', 'tharindu.u17_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_tharindu04', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(46, 'Dineth', 'Weerasinghe', '2011-01-05', '0772001025', 'dineth.u17_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_dineth05', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(47, 'Hirun', 'Perera', '2007-06-01', '0772001031', 'hirun.u19_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_hirun01', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(48, 'Kethmi', 'Silva', '2008-02-14', '0772001032', 'kethmi.u19_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_kethmi02', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(49, 'Malith', 'Fernando', '2007-09-20', '0772001033', 'malith.u19_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_malith03', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(50, 'Sanjana', 'Jayasinghe', '2008-03-10', '0772001034', 'sanjana.u19_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_sanjana04', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(51, 'Ruvin', 'Weerasinghe', '2009-01-05', '0772001035', 'ruvin.u19_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_ruvin05', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(52, 'Sahan', 'Perera', '2005-06-01', '0772001041', 'sahan.u21_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_sahan01', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(53, 'Pasan', 'Silva', '2006-02-14', '0772001042', 'pasan.u21_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_pasan02', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(54, 'Nisal', 'Fernando', '2005-09-20', '0772001043', 'nisal.u21_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_nisal03', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(55, 'Dulaj', 'Jayasinghe', '2006-03-10', '0772001044', 'dulaj.u21_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_dulaj04', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(56, 'Prabath', 'Weerasinghe', '2007-01-05', '0772001045', 'prabath.u21_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_prabath05', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 15:13:50', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(57, 'Aarav', 'Jayasuriya', '2015-04-18', '0772001101', 'aarav.u13_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_06', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(58, 'Rehan', 'Perera', '2014-11-22', '0772001102', 'rehan.u13_07@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_07', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(59, 'Ishan', 'Silva', '2015-02-09', '0772001103', 'ishan.u13_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_08', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(60, 'Navin', 'Fernando', '2014-07-15', '0772001104', 'navin.u13_09@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_09', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(61, 'Senuth', 'Jayasinghe', '2015-01-28', '0772001105', 'senuth.u13_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_10', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 13', NULL),
(62, 'Yuvan', 'Perera', '2012-04-20', '0772001111', 'yuvan.u15_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_06', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(63, 'Lakshan', 'Silva', '2011-11-12', '0772001112', 'lakshan.u15_07@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_07', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(64, 'Dasun', 'Fernando', '2012-08-03', '0772001113', 'dasun.u15_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_08', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(65, 'Omith', 'Jayasinghe', '2011-05-19', '0772001114', 'omith.u15_09@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_09', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(66, 'Thivin', 'Weerasinghe', '2012-01-30', '0772001115', 'thivin.u15_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_10', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 15', NULL),
(67, 'Madusha', 'Perera', '2010-04-17', '0772001121', 'madusha.u17_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_06', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(68, 'Asela', 'Silva', '2009-11-09', '0772001122', 'asela.u17_07@eliteca.com', '12/B , Kottawa, pannipitiya', 'Elite Cricket Academy', 'Player', 'Asela', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(69, 'Ravisha', 'Fernando', '2010-07-24', '0772001123', 'ravisha.u17_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_08', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(70, 'Kusal', 'Jayasinghe', '2009-05-14', '0772001124', 'kusal.u17_09@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_09', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(71, 'Dinura', 'Weerasinghe', '2010-02-28', '0772001125', 'dinura.u17_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_10', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 17', NULL),
(72, 'Chathura', 'Perera', '2008-04-21', '0772001131', 'chathura.u19_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_06', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(73, 'Shalitha', 'Silva', '2007-10-11', '0772001132', 'shalitha.u19_07@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_07', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(74, 'Praveen', 'Fernando', '2008-06-30', '0772001133', 'praveen.u19_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_08', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(75, 'Sachin', 'Jayasinghe', '2007-03-17', '0772001134', 'sachin.u19_09@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_09', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(76, 'Hiranya', 'Weerasinghe', '2008-01-25', '0772001135', 'hiranya.u19_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_10', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 19', NULL),
(77, 'Naveen', 'Perera', '2006-04-22', '0772001141', 'naveen.u21_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_06', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(78, 'Isuru', 'Silva', '2005-12-09', '0772001142', 'isuru.u21_07@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_07', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(79, 'Malsha', 'Fernando', '2006-09-13', '0772001143', 'malsha.u21_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_08', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(80, 'Heshan', 'Jayasinghe', '2005-05-26', '0772001144', 'heshan.u21_09@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_09', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(81, 'Vihan', 'Weerasinghe', '2006-02-14', '0772001145', 'vihan.u21_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_10', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Under 21', NULL),
(82, 'Duleesha', 'Perera', '2004-04-20', '0772001151', 'duleesha.open_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_01', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(83, 'Sanka', 'Silva', '2003-10-11', '0772001152', 'sanka.open_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_02', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(84, 'Chanuka', 'Fernando', '2002-06-30', '0772001153', 'chanuka.open_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_03', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(85, 'Isuru', 'Jayasinghe', '2001-03-17', '0772001154', 'isuru.open_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_04', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(86, 'Akila', 'Weerasinghe', '2000-01-25', '0772001155', 'akila.open_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_05', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(87, 'Raveen', 'Perera', '2004-11-04', '0772001156', 'raveen.open_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_06', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(88, 'Tharush', 'Silva', '2003-08-19', '0772001157', 'tharush.open_07@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_07', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(89, 'Kasun', 'Fernando', '2002-12-08', '0772001158', 'kasun.open_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_08', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(90, 'Dinesh', 'Jayasinghe', '2001-05-02', '0772001159', 'dinesh.open_09@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_09', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(91, 'Pasan', 'Weerasinghe', '2000-09-16', '0772001160', 'pasan.open_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'uopen_10', '', '2026-04-14 20:06:22', 'active', 0, NULL, NULL, 0, NULL, 1, 'Seed player - Open', NULL),
(121, 'Coach', 'Tharuka', '1991-08-14', '0771112203', 'tharuka.fielding@eliteca.com', 'Academy HQ', 'Elite Cricket Academy', 'Coach', 'coach203', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', '2026-04-15 00:12:15', 'active', 0, NULL, NULL, 0, NULL, 1, 'Fielding coach for Under 11, Under 13, and Open', NULL);

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `tr_create_role_profile` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    -- Create appropriate profile based on role
    CASE NEW.Role
        WHEN 'Player' THEN
            INSERT INTO PlayerProfile (PlayerID, SubscriptionType) 
            VALUES (NEW.UserID, 'basic');
        WHEN 'Coach' THEN
            INSERT INTO CoachProfile (CoachID, IsHeadCoach) 
            VALUES (NEW.UserID, FALSE);
        WHEN 'Trainer' THEN
            INSERT INTO TrainerProfile (TrainerID) 
            VALUES (NEW.UserID);
        WHEN 'ShopEmployee' THEN
            INSERT INTO ShopEmployeeProfile (ShopEmployeeID, HireDate) 
            VALUES (NEW.UserID, CURDATE());
        ELSE 
            BEGIN END; -- Admin or other roles don't need profiles
    END CASE;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_user_account_creation` AFTER INSERT ON `user` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `userpermissions`
--

CREATE TABLE `userpermissions` (
  `UserID` int(11) NOT NULL,
  `Permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Role-based permissions stored as JSON array' CHECK (json_valid(`Permissions`)),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Auto-updated on permission changes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dynamic role-based permissions storage for enhanced access control';

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_players`
-- (See below for the actual view)
--
CREATE TABLE `v_active_players` (
`UserID` int(11)
,`Name` varchar(255)
,`Email` varchar(255)
,`PhoneNumber` varchar(20)
,`DateOfBirth` date
,`BattingStyle` enum('Right-handed','Left-handed','Switch-hitter')
,`BowlingStyle` enum('Fast','Medium','Spin','Off-spin','Leg-spin','None')
,`JerseyNumber` int(11)
,`MatchesPlayed` int(11)
,`TotalRuns` int(11)
,`TotalWickets` int(11)
,`BattingAverage` decimal(6,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_facility_availability`
-- (See below for the actual view)
--
CREATE TABLE `v_facility_availability` (
`FacilityID` int(11)
,`Name` varchar(255)
,`Location` varchar(255)
,`Capacity` int(11)
,`HourlyRate` decimal(10,2)
,`AvailabilityStatus` enum('available','occupied','maintenance')
,`TodayBookings` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_upcoming_sessions`
-- (See below for the actual view)
--
CREATE TABLE `v_upcoming_sessions` (
`SessionID` int(11)
,`Name` varchar(255)
,`SessionType` enum('Coaching','Physical Training')
,`Date` date
,`StartTime` time
,`EndTime` time
,`Location` varchar(255)
,`InstructorName` varchar(255)
,`EnrolledCount` bigint(21)
,`MaxParticipants` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `workoutplan`
--

CREATE TABLE `workoutplan` (
  `PlanID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `workoutname` varchar(255) DEFAULT NULL,
  `frequency` enum('Daily','Weekly','Bi-weekly') DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
  `VideoLink` varchar(255) DEFAULT NULL COMMENT 'Link to workout demonstration video',
  `Intensity` enum('Low','Moderate','High') DEFAULT 'Moderate' COMMENT 'Workout intensity level',
  `NotSuitableFor` enum('None (General)','Post-Surgery','Active Lower Back Pain','Knee Injuries','Shoulder Instability','Acute Ankle Sprain','Heart Conditions','Concussion Protocol') NOT NULL DEFAULT 'None (General)',
  `Benefits` text DEFAULT NULL COMMENT 'Key benefits of performing this workout',
  `Status` enum('active','draft','archived') NOT NULL DEFAULT 'active',
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customized workout plans for players';

--
-- Dumping data for table `workoutplan`
--

INSERT INTO `workoutplan` (`PlanID`, `TrainerID`, `workoutname`, `frequency`, `Duration`, `VideoLink`, `Intensity`, `NotSuitableFor`, `Benefits`, `Status`, `CreatedDate`) VALUES
(2, 10, 'cardio yuyiuj', 'Bi-weekly', 60, NULL, 'Moderate', 'None (General)', NULL, 'active', '2025-10-21'),
(24, 10, 'Manual Test Workout', 'Daily', 60, NULL, 'Moderate', 'None (General)', NULL, 'active', '2025-10-22'),
(25, 10, 'Manual Test Workout 20', 'Daily', 60, NULL, 'Moderate', 'None (General)', NULL, 'active', '2025-10-22'),
(26, 10, 'aa22', 'Weekly', 20, NULL, 'High', 'None (General)', 'good', 'active', '2025-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `workoutplan_player`
--

CREATE TABLE `workoutplan_player` (
  `PlanID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AssignedDate` date DEFAULT curdate(),
  `AssignedBy` int(11) DEFAULT NULL,
  `Status` enum('active','completed','paused') NOT NULL DEFAULT 'active',
  `EndDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `coachingeffectiveness`
--
DROP TABLE IF EXISTS `coachingeffectiveness`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `coachingeffectiveness`  AS SELECT `cp`.`CoachID` AS `CoachID`, CONCAT(`u_coach`.`FirstName`, ' ', `u_coach`.`LastName`) AS `CoachName`, `cp`.`Specialization` AS `Specialization`, count(distinct `pca`.`PlayerID`) AS `PlayersAssigned`, count(`pu`.`UpdateID`) AS `PerformanceUpdatesGiven`, count(`cs`.`SessionLogID`) AS `SessionsLogged`, avg(`cs`.`SessionRating`) AS `AvgSessionRating`, avg(case when `pu`.`Status` = 'approved' then `pu`.`TechnicalRating` end) AS `AvgTechnicalRatingGiven`, count(case when `cs`.`AttendanceStatus` = 'present' then 1 end) AS `SessionsAttended`, count(case when `cs`.`AttendanceStatus` in ('absent','late') then 1 end) AS `SessionsMissed` FROM ((((`coachprofile` `cp` join `user` `u_coach` on(`cp`.`CoachID` = `u_coach`.`UserID`)) left join `playercoachassignment` `pca` on(`cp`.`CoachID` = `pca`.`CoachID` and `pca`.`Status` = 'active')) left join `performanceupdate` `pu` on(`cp`.`CoachID` = `pu`.`CoachID`)) left join `coachingsession` `cs` on(`cp`.`CoachID` = `cs`.`CoachID`)) WHERE `u_coach`.`Status` = 'active' GROUP BY `cp`.`CoachID`, CONCAT(`u_coach`.`FirstName`, ' ', `u_coach`.`LastName`), `cp`.`Specialization` ;

-- --------------------------------------------------------

--
-- Structure for view `coachplayerpermissions`
--
DROP TABLE IF EXISTS `coachplayerpermissions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `coachplayerpermissions`  AS SELECT `cp`.`CoachID` AS `CoachID`, CONCAT(`u_coach`.`FirstName`, ' ', `u_coach`.`LastName`) AS `CoachName`, `cp`.`IsHeadCoach` AS `IsHeadCoach`, `u_coach`.`Role` AS `Role`, `pp`.`PlayerID` AS `PlayerID`, CONCAT(`u_player`.`FirstName`, ' ', `u_player`.`LastName`) AS `PlayerName`, `pca`.`Status` AS `AssignmentStatus`, `pca`.`AssignmentType` AS `AssignmentType`, `pca`.`AssignedDate` AS `AssignedDate`, CASE WHEN `u_coach`.`Role` = 'Admin' THEN 'Full Access' WHEN `cp`.`IsHeadCoach` = 1 THEN 'Head Coach Access' WHEN `pca`.`Status` = 'active' THEN 'Assigned Player Access' ELSE 'No Access' END AS `PermissionLevel` FROM ((((`user` `u_coach` join `coachprofile` `cp` on(`u_coach`.`UserID` = `cp`.`CoachID`)) left join `playercoachassignment` `pca` on(`cp`.`CoachID` = `pca`.`CoachID`)) left join `playerprofile` `pp` on(`pca`.`PlayerID` = `pp`.`PlayerID`)) left join `user` `u_player` on(`pp`.`PlayerID` = `u_player`.`UserID`)) WHERE `u_coach`.`Status` = 'active' ;

-- --------------------------------------------------------

--
-- Structure for view `performanceupdatesummary`
--
DROP TABLE IF EXISTS `performanceupdatesummary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `performanceupdatesummary`  AS SELECT `pp`.`PlayerID` AS `PlayerID`, CONCAT(`u`.`FirstName`, ' ', `u`.`LastName`) AS `PlayerName`, count(`pu`.`UpdateID`) AS `TotalUpdates`, count(case when `pu`.`Status` = 'approved' then 1 end) AS `ApprovedUpdates`, count(case when `pu`.`Status` = 'pending_approval' then 1 end) AS `PendingUpdates`, max(`pu`.`UpdateDate`) AS `LastUpdateDate`, avg(`pu`.`TechnicalRating`) AS `AvgTechnicalRating`, avg(`pu`.`FitnessRating`) AS `AvgFitnessRating`, avg(`pu`.`AttitudeRating`) AS `AvgAttitudeRating` FROM ((`playerprofile` `pp` join `user` `u` on(`pp`.`PlayerID` = `u`.`UserID`)) left join `performanceupdate` `pu` on(`pp`.`PlayerID` = `pu`.`PlayerID`)) GROUP BY `pp`.`PlayerID`, CONCAT(`u`.`FirstName`, ' ', `u`.`LastName`) ;

-- --------------------------------------------------------

--
-- View `playerdevelopmenttracking` removed (depends on missing sessionenrollment table)
--
DROP TABLE IF EXISTS `playerdevelopmenttracking`;

-- --------------------------------------------------------

--
-- Structure for view `v_active_players`
--
DROP TABLE IF EXISTS `v_active_players`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_players`  AS SELECT `u`.`UserID` AS `UserID`, CONCAT(`u`.`FirstName`, ' ', `u`.`LastName`) AS `Name`, `u`.`Email` AS `Email`, `u`.`PhoneNumber` AS `PhoneNumber`, `u`.`DateOfBirth` AS `DateOfBirth`, `pp`.`BattingStyle` AS `BattingStyle`, `pp`.`BowlingStyle` AS `BowlingStyle`, `pp`.`JerseyNumber` AS `JerseyNumber`, `pos`.`MatchesPlayed` AS `MatchesPlayed`, `pos`.`TotalRuns` AS `TotalRuns`, `pos`.`TotalWickets` AS `TotalWickets`, `pos`.`BattingAverage` AS `BattingAverage` FROM ((`user` `u` join `playerprofile` `pp` on(`u`.`UserID` = `pp`.`PlayerID`)) join `playeroverallstats` `pos` on(`pp`.`PlayerID` = `pos`.`PlayerID`)) WHERE `u`.`Status` = 'active' ;

-- --------------------------------------------------------

--
-- Structure for view `v_facility_availability`
--
DROP TABLE IF EXISTS `v_facility_availability`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_facility_availability`  AS SELECT `f`.`FacilityID` AS `FacilityID`, `f`.`Name` AS `Name`, `f`.`Location` AS `Location`, `f`.`Capacity` AS `Capacity`, `f`.`HourlyRate` AS `HourlyRate`, `f`.`AvailabilityStatus` AS `AvailabilityStatus`, count(`fb`.`FacilityBookingID`) AS `TodayBookings` FROM (`facility` `f` left join `facilitybooking` `fb` on(`f`.`FacilityID` = `fb`.`FacilityID` and `fb`.`BookingDate` = curdate() and `fb`.`Status` = 'confirmed')) GROUP BY `f`.`FacilityID`, `f`.`Name`, `f`.`Location`, `f`.`Capacity`, `f`.`HourlyRate`, `f`.`AvailabilityStatus` ;

-- --------------------------------------------------------

--
-- View `v_upcoming_sessions` removed (depends on missing sessionenrollment table)
--
DROP TABLE IF EXISTS `v_upcoming_sessions`;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`AchievementID`),
  ADD KEY `PlayerID` (`PlayerID`),
  ADD KEY `idx_tournament` (`Tournament`),
  ADD KEY `idx_verified` (`VerifiedStatus`),
  ADD KEY `idx_date` (`Date`);

--
-- Indexes for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD PRIMARY KEY (`ActivityID`),
  ADD KEY `idx_user_activity` (`UserID`),
  ADD KEY `idx_action` (`Action`),
  ADD KEY `idx_timestamp` (`Timestamp`);

--
-- Indexes for table `adminprofile`
--
ALTER TABLE `adminprofile`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `coachappointment`
--
ALTER TABLE `coachappointment`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `idx_coach_date` (`CoachID`,`AppointmentDate`),
  ADD KEY `idx_player_date` (`PlayerID`,`AppointmentDate`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `coachingsession`
--
ALTER TABLE `coachingsession`
  ADD PRIMARY KEY (`SessionLogID`),
  ADD KEY `idx_coaching_session` (`SessionID`),
  ADD KEY `idx_coaching_player` (`PlayerID`),
  ADD KEY `idx_coaching_coach` (`CoachID`),
  ADD KEY `idx_coaching_attendance` (`AttendanceStatus`);

--
-- Indexes for table `coachprofile`
--
ALTER TABLE `coachprofile`
  ADD PRIMARY KEY (`CoachID`),
  ADD KEY `idx_is_head_coach` (`IsHeadCoach`);

--
-- Indexes for table `coachreview`
--
ALTER TABLE `coachreview`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `idx_coach_review` (`CoachID`),
  ADD KEY `idx_player_review` (`PlayerID`),
  ADD KEY `idx_overall_rating` (`OverallRating`),
  ADD KEY `idx_review_date` (`ReviewDate`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `coach_skill_age_group_assignment`
--
ALTER TABLE `coach_skill_age_group_assignment`
  ADD PRIMARY KEY (`CoachSkillGroupID`),
  ADD UNIQUE KEY `uq_csg_coach_skill_age` (`CoachID`,`CoachingType`,`AgeGroup`),
  ADD KEY `idx_csg_skill_age` (`CoachingType`,`AgeGroup`,`IsActive`),
  ADD KEY `fk_csg_assigned_by` (`AssignedBy`);

--
-- Indexes for table `coach_tournament_recommendations`
--
ALTER TABLE `coach_tournament_recommendations`
  ADD PRIMARY KEY (`RecommendationID`),
  ADD UNIQUE KEY `uq_coach_rec` (`CoachID`,`TournamentID`,`PlayerID`),
  ADD KEY `fk_ctr_reviewer` (`ReviewedBy`),
  ADD KEY `idx_coach_tournament` (`CoachID`,`TournamentID`),
  ADD KEY `idx_tournament` (`TournamentID`),
  ADD KEY `idx_player` (`PlayerID`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_date_recommended` (`DateRecommended`);

--
-- Indexes for table `contactmessage`
--
ALTER TABLE `contactmessage`
  ADD PRIMARY KEY (`ContactID`),
  ADD KEY `RespondedBy` (`RespondedBy`),
  ADD KEY `idx_contact_user` (`UserID`),
  ADD KEY `idx_contact_status` (`Status`),
  ADD KEY `idx_contact_type` (`MessageType`),
  ADD KEY `idx_contact_priority` (`Priority`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`ContactID`),
  ADD KEY `RespondedBy` (`RespondedBy`),
  ADD KEY `idx_contact_user` (`UserID`),
  ADD KEY `idx_contact_status` (`Status`),
  ADD KEY `idx_contact_type` (`MessageType`),
  ADD KEY `idx_contact_priority` (`Priority`),
  ADD KEY `idx_contact_date` (`SubmittedDate`);

--
-- Indexes for table `crimatch`
--
ALTER TABLE `crimatch`
  ADD PRIMARY KEY (`MatchID`),
  ADD KEY `idx_tournament_match` (`TournamentID`),
  ADD KEY `idx_date` (`Date`),
  ADD KEY `idx_result` (`Result`);

--
-- Indexes for table `emaillog`
--
ALTER TABLE `emaillog`
  ADD PRIMARY KEY (`EmailID`),
  ADD KEY `idx_user_email` (`UserID`),
  ADD KEY `idx_email_type` (`EmailType`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_sent_at` (`SentAt`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`EquipmentID`),
  ADD KEY `idx_availability` (`AvailabilityStatus`),
  ADD KEY `idx_category` (`Category`),
  ADD KEY `idx_name` (`Name`);

--
-- Indexes for table `equipmentcart`
--
ALTER TABLE `equipmentcart`
  ADD PRIMARY KEY (`CartID`),
  ADD UNIQUE KEY `unique_equipment_reservation` (`PlayerID`,`EquipmentID`,`RequestedStartTime`),
  ADD KEY `idx_player_equipment_cart` (`PlayerID`),
  ADD KEY `idx_equipment_availability` (`EquipmentID`,`RequestedStartTime`,`RequestedEndTime`),
  ADD KEY `idx_expires_at` (`ExpiresAt`);

--
-- Indexes for table `equipmentrental`
--
ALTER TABLE `equipmentrental`
  ADD PRIMARY KEY (`RentalID`),
  ADD KEY `ProcessedBy` (`ProcessedBy`),
  ADD KEY `ReturnInspectedBy` (`ReturnInspectedBy`),
  ADD KEY `idx_rental_date` (`RentalDate`),
  ADD KEY `idx_equipment_rental` (`EquipmentID`),
  ADD KEY `idx_player_rental` (`PlayerID`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `equipmentreview`
--
ALTER TABLE `equipmentreview`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `ModeratedBy` (`ModeratedBy`),
  ADD KEY `idx_equipment_review` (`EquipmentID`),
  ADD KEY `idx_player_review` (`PlayerID`),
  ADD KEY `idx_rental_review` (`RentalID`),
  ADD KEY `idx_rating` (`Rating`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `idx_start_date` (`StartDate`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_category` (`Category`),
  ADD KEY `idx_registration_start` (`RegistrationStart`),
  ADD KEY `idx_registration_end` (`RegistrationEnd`),
  ADD KEY `idx_contact_email` (`ContactEmail`);

--
-- Indexes for table `eventenrollment`
--
ALTER TABLE `eventenrollment`
  ADD PRIMARY KEY (`EnrollmentID`),
  ADD UNIQUE KEY `unique_event_enrollment` (`EventID`,`PlayerID`),
  ADD KEY `idx_player_enrollment` (`PlayerID`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `facility`
--
ALTER TABLE `facility`
  ADD PRIMARY KEY (`FacilityID`),
  ADD UNIQUE KEY `uq_facility_f_code` (`f_code`),
  ADD KEY `idx_availability` (`AvailabilityStatus`),
  ADD KEY `idx_name` (`Name`);

--
-- Indexes for table `facilitybooking`
--
ALTER TABLE `facilitybooking`
  ADD PRIMARY KEY (`FacilityBookingID`),
  ADD KEY `BookedBy` (`BookedBy`),
  ADD KEY `idx_booking_date` (`BookingDate`),
  ADD KEY `idx_facility_date` (`FacilityID`,`BookingDate`),
  ADD KEY `idx_player_booking` (`PlayerID`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `idx_from_user` (`FromUserID`),
  ADD KEY `idx_to_user` (`ToUserID`),
  ADD KEY `idx_rating` (`Rating`),
  ADD KEY `idx_category` (`Category`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `livenotification`
--
ALTER TABLE `livenotification`
  ADD PRIMARY KEY (`LiveNotificationID`),
  ADD KEY `idx_live_user` (`UserID`),
  ADD KEY `idx_live_expires` (`ExpiresAt`);

--
-- Indexes for table `membershipplan`
--
ALTER TABLE `membershipplan`
  ADD PRIMARY KEY (`PlanID`),
  ADD KEY `idx_membershipplan_status_fee` (`Status`,`MonthlyFee`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `idx_user_notifications` (`UserID`),
  ADD KEY `idx_notification_type` (`Type`),
  ADD KEY `idx_notification_read` (`IsRead`),
  ADD KEY `idx_notification_created` (`CreatedAt`);

--
-- Indexes for table `nutritionplan`
--
ALTER TABLE `nutritionplan`
  ADD PRIMARY KEY (`PlanID`),
  ADD KEY `idx_trainer_nutrition` (`TrainerID`),
  ADD KEY `idx_nutrition_player` (`PlayerID`);

--
-- Indexes for table `nutritionplan_player`
--
ALTER TABLE `nutritionplan_player`
  ADD PRIMARY KEY (`PlanID`,`PlayerID`),
  ADD KEY `PlayerID` (`PlayerID`);

--
-- Indexes for table `performanceupdate`
--
ALTER TABLE `performanceupdate`
  ADD PRIMARY KEY (`UpdateID`),
  ADD KEY `SessionID` (`SessionID`),
  ADD KEY `ApprovedBy` (`ApprovedBy`),
  ADD KEY `idx_perf_player` (`PlayerID`),
  ADD KEY `idx_perf_coach` (`CoachID`),
  ADD KEY `idx_perf_date` (`UpdateDate`),
  ADD KEY `idx_perf_status` (`Status`),
  ADD KEY `idx_player_coach_perf` (`PlayerID`,`CoachID`);

--
-- Indexes for table `playercoach`
--
ALTER TABLE `playercoach`
  ADD PRIMARY KEY (`PlayerCoachID`),
  ADD KEY `CoachID` (`CoachID`),
  ADD KEY `idx_player_coach` (`PlayerID`,`CoachID`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `playercoachassignment`
--
ALTER TABLE `playercoachassignment`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD UNIQUE KEY `unique_active_assignment` (`PlayerID`,`CoachID`,`Status`),
  ADD KEY `CoachID` (`CoachID`),
  ADD KEY `idx_player_coach` (`PlayerID`,`CoachID`),
  ADD KEY `idx_assignment_status` (`Status`);

--
-- Indexes for table `playermatchperformance`
--
ALTER TABLE `playermatchperformance`
  ADD PRIMARY KEY (`PerformanceID`),
  ADD UNIQUE KEY `unique_match_performance` (`MatchID`,`PlayerID`),
  ADD KEY `idx_player_performance` (`PlayerID`),
  ADD KEY `idx_rating` (`Rating`),
  ADD KEY `idx_verified_status` (`VerifiedStatus`),
  ADD KEY `idx_added_by` (`AddedBy`),
  ADD KEY `idx_verified_by` (`VerifiedBy`);

--
-- Indexes for table `playermedicalrecord`
--
ALTER TABLE `playermedicalrecord`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `ReportedBy` (`ReportedBy`),
  ADD KEY `idx_player_medical` (`PlayerID`),
  ADD KEY `idx_reported_date` (`ReportedDate`),
  ADD KEY `idx_recovery_status` (`RecoveryStatus`);

--
-- Indexes for table `playeroverallstats`
--
ALTER TABLE `playeroverallstats`
  ADD PRIMARY KEY (`StatsID`),
  ADD UNIQUE KEY `PlayerID` (`PlayerID`),
  ADD KEY `idx_batting_avg` (`BattingAverage`),
  ADD KEY `idx_bowling_avg` (`BowlingAverage`),
  ADD KEY `idx_last_updated_by` (`LastUpdatedBy`);

--
-- Indexes for table `playerprofile`
--
ALTER TABLE `playerprofile`
  ADD PRIMARY KEY (`PlayerID`),
  ADD UNIQUE KEY `JerseyNumber` (`JerseyNumber`),
  ADD KEY `idx_jersey` (`JerseyNumber`);

--
-- Indexes for table `playersubscription`
--
ALTER TABLE `playersubscription`
  ADD PRIMARY KEY (`SubscriptionID`),
  ADD KEY `PlayerID` (`PlayerID`),
  ADD KEY `PlanID` (`PlanID`),
  ADD KEY `idx_playersubscription_player_status_start` (`PlayerID`,`Status`,`StartDate`),
  ADD KEY `idx_playersubscription_plan_status` (`PlanID`,`Status`);

--
-- Indexes for table `playertournamentstats`
--
ALTER TABLE `playertournamentstats`
  ADD PRIMARY KEY (`TournamentStatsID`),
  ADD UNIQUE KEY `unique_tournament_stats` (`TournamentID`,`PlayerID`),
  ADD KEY `idx_player_tournament_stats` (`PlayerID`);

--
-- Indexes for table `playertrainer`
--
ALTER TABLE `playertrainer`
  ADD PRIMARY KEY (`PlayerTrainerID`),
  ADD KEY `TrainerID` (`TrainerID`),
  ADD KEY `idx_player_trainer` (`PlayerID`,`TrainerID`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `playertrainerassignment`
--
ALTER TABLE `playertrainerassignment`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD KEY `TrainerID` (`TrainerID`),
  ADD KEY `idx_player_trainer` (`PlayerID`,`TrainerID`);

--
-- Indexes for table `player_skill_coach_assignment`
--
ALTER TABLE `player_skill_coach_assignment`
  ADD PRIMARY KEY (`PlayerID`,`CoachingType`),
  ADD KEY `idx_psca_coach` (`CoachID`),
  ADD KEY `idx_psca_age_group` (`AgeGroup`),
  ADD KEY `fk_psca_assigned_by` (`AssignedBy`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD UNIQUE KEY `SKU` (`SKU`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `idx_category` (`Category`),
  ADD KEY `idx_brand` (`Brand`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_price` (`Price`),
  ADD KEY `idx_stock` (`StockQuantity`),
  ADD KEY `idx_product_image` (`ProductImage`);

--
-- Indexes for table `productcart`
--
ALTER TABLE `productcart`
  ADD PRIMARY KEY (`CartID`),
  ADD UNIQUE KEY `unique_cart_item` (`PlayerID`,`ProductID`),
  ADD KEY `ProductID` (`ProductID`),
  ADD KEY `idx_player_cart` (`PlayerID`);

--
-- Indexes for table `productorder`
--
ALTER TABLE `productorder`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `ProcessedBy` (`ProcessedBy`),
  ADD KEY `idx_player_order` (`PlayerID`),
  ADD KEY `idx_order_date` (`OrderDate`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `productorderitem`
--
ALTER TABLE `productorderitem`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `idx_order_items` (`OrderID`),
  ADD KEY `idx_product_sales` (`ProductID`);

--
-- Indexes for table `productreview`
--
ALTER TABLE `productreview`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ModeratedBy` (`ModeratedBy`),
  ADD KEY `idx_product_review` (`ProductID`),
  ADD KEY `idx_player_review` (`PlayerID`),
  ADD KEY `idx_rating` (`Rating`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_review_date` (`ReviewDate`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`SessionID`),
  ADD KEY `idx_date` (`Date`),
  ADD KEY `idx_datetime_range` (`Date`,`StartTime`,`EndTime`),
  ADD KEY `idx_coach_trainer` (`CoachOrTrainerID`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_session_mode` (`SessionMode`);

--
-- Indexes for table `shopemployeeprofile`
--
ALTER TABLE `shopemployeeprofile`
  ADD PRIMARY KEY (`ShopEmployeeID`);

--
-- Indexes for table `slot_audit_log`
--
ALTER TABLE `slot_audit_log`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `idx_scl_entity` (`EntityType`,`EntityID`),
  ADD KEY `idx_scl_changer` (`ChangedBy`),
  ADD KEY `idx_scl_date` (`ChangedAt`);

--
-- Indexes for table `slot_booking`
--
ALTER TABLE `slot_booking`
  ADD PRIMARY KEY (`BookingID`),
  ADD UNIQUE KEY `uq_booking_player_occurrence` (`OccurrenceID`,`PlayerID`),
  ADD KEY `idx_sb_player` (`PlayerID`),
  ADD KEY `idx_sb_status` (`Status`),
  ADD KEY `idx_sb_payment` (`PaymentStatus`),
  ADD KEY `idx_sb_source` (`BookingSource`),
  ADD KEY `fk_sb_subscription` (`SubscriptionID`);

--
-- Indexes for table `slot_occurrence`
--
ALTER TABLE `slot_occurrence`
  ADD PRIMARY KEY (`OccurrenceID`),
  ADD UNIQUE KEY `uq_occ_facility_slot_date` (`FacilityID`,`SlotID`,`OccurrenceDate`),
  ADD KEY `idx_occ_template` (`TemplateID`),
  ADD KEY `idx_occ_date` (`OccurrenceDate`),
  ADD KEY `idx_occ_status` (`Status`),
  ADD KEY `fk_occ_slot` (`SlotID`);

--
-- Indexes for table `slot_occurrence_staff_override`
--
ALTER TABLE `slot_occurrence_staff_override`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `uq_soso_occ_user` (`OccurrenceID`,`UserID`),
  ADD KEY `fk_soso_user` (`UserID`);

--
-- Indexes for table `slot_private_session_request`
--
ALTER TABLE `slot_private_session_request`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `idx_private_session_request_status` (`Status`),
  ADD KEY `idx_private_session_request_date` (`RequestedDate`),
  ADD KEY `idx_private_session_request_requester` (`RequesterUserID`),
  ADD KEY `idx_private_session_request_facility` (`FacilityID`),
  ADD KEY `idx_private_session_request_slot` (`SlotID`),
  ADD KEY `idx_private_session_request_reviewed_by` (`ReviewedBy`),
  ADD KEY `idx_private_session_request_approved_occurrence` (`ApprovedOccurrenceID`);

--
-- Indexes for table `slot_template`
--
ALTER TABLE `slot_template`
  ADD PRIMARY KEY (`TemplateID`),
  ADD KEY `idx_st_slot` (`SlotID`),
  ADD KEY `idx_st_facility` (`FacilityID`),
  ADD KEY `idx_st_type` (`SlotType`),
  ADD KEY `idx_st_stafftype` (`StaffType`),
  ADD KEY `fk_st_creator` (`CreatedBy`);

--
-- Indexes for table `slot_template_player_assignment`
--
ALTER TABLE `slot_template_player_assignment`
  ADD PRIMARY KEY (`TemplateID`,`PlayerID`),
  ADD KEY `idx_stpa_player` (`PlayerID`,`IsActive`),
  ADD KEY `fk_stpa_assigned_by` (`AssignedBy`);

--
-- Indexes for table `slot_template_staff`
--
ALTER TABLE `slot_template_staff`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `uq_tstaff_template_user` (`TemplateID`,`UserID`),
  ADD KEY `fk_tstaff_user` (`UserID`),
  ADD KEY `fk_tstaff_assigner` (`AssignedBy`);

--
-- Indexes for table `slot_time_band`
--
ALTER TABLE `slot_time_band`
  ADD PRIMARY KEY (`SlotID`);

--
-- Indexes for table `subscriptionpayment`
--
ALTER TABLE `subscriptionpayment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD UNIQUE KEY `uk_subscriptionpayment_subscription_month` (`SubscriptionID`,`BillingMonth`),
  ADD KEY `SubscriptionID` (`SubscriptionID`),
  ADD KEY `ProcessedBy` (`ProcessedBy`),
  ADD KEY `idx_subscriptionpayment_status_due` (`Status`,`DueDate`),
  ADD KEY `idx_subscriptionpayment_sub_status_due` (`SubscriptionID`,`Status`,`DueDate`),
  ADD KEY `idx_subscriptionpayment_status_paymentdate` (`Status`,`PaymentDate`),
  ADD KEY `idx_subscriptionpayment_gateway_order` (`Gateway`,`GatewayOrderId`);

--
-- Indexes for table `supplementplan`
--
ALTER TABLE `supplementplan`
  ADD PRIMARY KEY (`PlanID`),
  ADD KEY `idx_trainer_supplement` (`TrainerID`);

--
-- Indexes for table `supplement_player`
--
ALTER TABLE `supplement_player`
  ADD PRIMARY KEY (`PlanID`,`PlayerID`),
  ADD KEY `PlayerID` (`PlayerID`);

--
-- Indexes for table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`TournamentID`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_creator` (`CreatedBy`);

--
-- Indexes for table `tournamentplayer`
--
ALTER TABLE `tournamentplayer`
  ADD PRIMARY KEY (`TournamentPlayerID`),
  ADD UNIQUE KEY `uq_tournament_player` (`TournamentID`,`PlayerID`),
  ADD KEY `SelectedBy` (`SelectedBy`),
  ADD KEY `idx_player_tournament` (`PlayerID`),
  ADD KEY `idx_role` (`RoleInTeam`);

--
-- Indexes for table `tournament_join_request`
--
ALTER TABLE `tournament_join_request`
  ADD PRIMARY KEY (`RequestID`),
  ADD UNIQUE KEY `uq_join_request` (`TournamentID`,`PlayerID`),
  ADD KEY `idx_tjr_player` (`PlayerID`),
  ADD KEY `idx_tjr_status` (`Status`),
  ADD KEY `idx_tjr_reviewer` (`ReviewedBy`);

--
-- Indexes for table `trainerappointment`
--
ALTER TABLE `trainerappointment`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `idx_trainer_date` (`TrainerID`,`AppointmentDate`),
  ADD KEY `idx_player_date` (`PlayerID`,`AppointmentDate`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `trainerprofile`
--
ALTER TABLE `trainerprofile`
  ADD PRIMARY KEY (`TrainerID`);

--
-- Indexes for table `trainerreview`
--
ALTER TABLE `trainerreview`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `idx_trainer_review` (`TrainerID`),
  ADD KEY `idx_player_review` (`PlayerID`),
  ADD KEY `idx_overall_rating` (`OverallRating`),
  ADD KEY `idx_review_date` (`ReviewDate`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `trainer_tournament_recommendations`
--
ALTER TABLE `trainer_tournament_recommendations`
  ADD PRIMARY KEY (`RecommendationID`),
  ADD UNIQUE KEY `uq_trainer_rec` (`TrainerID`,`TournamentID`,`PlayerID`),
  ADD KEY `idx_ttr_player` (`PlayerID`),
  ADD KEY `idx_ttr_tournament` (`TournamentID`),
  ADD KEY `idx_ttr_reviewed_by` (`ReviewedBy`),
  ADD KEY `idx_ttr_status` (`Status`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `idx_email` (`Email`),
  ADD KEY `idx_username` (`Username`),
  ADD KEY `idx_role` (`Role`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_password_deadline` (`PasswordChangeDeadline`),
  ADD KEY `idx_last_login` (`LastLoginAt`),
  ADD KEY `idx_created_by` (`CreatedBy`),
  ADD KEY `idx_profile_image` (`ProfileImage`);

--
-- Indexes for table `userpermissions`
--
ALTER TABLE `userpermissions`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `workoutplan`
--
ALTER TABLE `workoutplan`
  ADD PRIMARY KEY (`PlanID`),
  ADD KEY `idx_trainer_workout` (`TrainerID`),
  ADD KEY `idx_wp_trainer_status` (`TrainerID`,`Status`);

--
-- Indexes for table `workoutplan_player`
--
ALTER TABLE `workoutplan_player`
  ADD PRIMARY KEY (`PlanID`,`PlayerID`),
  ADD KEY `PlayerID` (`PlayerID`),
  ADD KEY `idx_wpp_player_status` (`PlayerID`,`Status`),
  ADD KEY `idx_wpp_plan_status` (`PlanID`,`Status`),
  ADD KEY `idx_wpp_assigned_by` (`AssignedBy`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `AchievementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `activitylog`
--
ALTER TABLE `activitylog`
  MODIFY `ActivityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT for table `coachappointment`
--
ALTER TABLE `coachappointment`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coachingsession`
--
ALTER TABLE `coachingsession`
  MODIFY `SessionLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coachreview`
--
ALTER TABLE `coachreview`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coach_skill_age_group_assignment`
--
ALTER TABLE `coach_skill_age_group_assignment`
  MODIFY `CoachSkillGroupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `coach_tournament_recommendations`
--
ALTER TABLE `coach_tournament_recommendations`
  MODIFY `RecommendationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contactmessage`
--
ALTER TABLE `contactmessage`
  MODIFY `ContactID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `ContactID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crimatch`
--
ALTER TABLE `crimatch`
  MODIFY `MatchID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `emaillog`
--
ALTER TABLE `emaillog`
  MODIFY `EmailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `EquipmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `equipmentcart`
--
ALTER TABLE `equipmentcart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipmentrental`
--
ALTER TABLE `equipmentrental`
  MODIFY `RentalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `equipmentreview`
--
ALTER TABLE `equipmentreview`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `eventenrollment`
--
ALTER TABLE `eventenrollment`
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facility`
--
ALTER TABLE `facility`
  MODIFY `FacilityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `facilitybooking`
--
ALTER TABLE `facilitybooking`
  MODIFY `FacilityBookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `livenotification`
--
ALTER TABLE `livenotification`
  MODIFY `LiveNotificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membershipplan`
--
ALTER TABLE `membershipplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `nutritionplan`
--
ALTER TABLE `nutritionplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `performanceupdate`
--
ALTER TABLE `performanceupdate`
  MODIFY `UpdateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playercoach`
--
ALTER TABLE `playercoach`
  MODIFY `PlayerCoachID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playercoachassignment`
--
ALTER TABLE `playercoachassignment`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `playermatchperformance`
--
ALTER TABLE `playermatchperformance`
  MODIFY `PerformanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `playermedicalrecord`
--
ALTER TABLE `playermedicalrecord`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `playeroverallstats`
--
ALTER TABLE `playeroverallstats`
  MODIFY `StatsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `playersubscription`
--
ALTER TABLE `playersubscription`
  MODIFY `SubscriptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `playertournamentstats`
--
ALTER TABLE `playertournamentstats`
  MODIFY `TournamentStatsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playertrainer`
--
ALTER TABLE `playertrainer`
  MODIFY `PlayerTrainerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playertrainerassignment`
--
ALTER TABLE `playertrainerassignment`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `productcart`
--
ALTER TABLE `productcart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productorder`
--
ALTER TABLE `productorder`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `productorderitem`
--
ALTER TABLE `productorderitem`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `productreview`
--
ALTER TABLE `productreview`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `SessionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `slot_audit_log`
--
ALTER TABLE `slot_audit_log`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `slot_booking`
--
ALTER TABLE `slot_booking`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slot_occurrence`
--
ALTER TABLE `slot_occurrence`
  MODIFY `OccurrenceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2914;

--
-- AUTO_INCREMENT for table `slot_occurrence_staff_override`
--
ALTER TABLE `slot_occurrence_staff_override`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `slot_private_session_request`
--
ALTER TABLE `slot_private_session_request`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slot_template`
--
ALTER TABLE `slot_template`
  MODIFY `TemplateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `slot_template_staff`
--
ALTER TABLE `slot_template_staff`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `slot_time_band`
--
ALTER TABLE `slot_time_band`
  MODIFY `SlotID` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscriptionpayment`
--
ALTER TABLE `subscriptionpayment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `supplementplan`
--
ALTER TABLE `supplementplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `TournamentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tournamentplayer`
--
ALTER TABLE `tournamentplayer`
  MODIFY `TournamentPlayerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tournament_join_request`
--
ALTER TABLE `tournament_join_request`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trainerappointment`
--
ALTER TABLE `trainerappointment`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainerreview`
--
ALTER TABLE `trainerreview`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainer_tournament_recommendations`
--
ALTER TABLE `trainer_tournament_recommendations`
  MODIFY `RecommendationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `workoutplan`
--
ALTER TABLE `workoutplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD CONSTRAINT `activitylog_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `adminprofile`
--
ALTER TABLE `adminprofile`
  ADD CONSTRAINT `adminprofile_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coachappointment`
--
ALTER TABLE `coachappointment`
  ADD CONSTRAINT `coachappointment_ibfk_1` FOREIGN KEY (`CoachID`) REFERENCES `coachprofile` (`CoachID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coachappointment_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coachingsession`
--
ALTER TABLE `coachingsession`
  ADD CONSTRAINT `coachingsession_ibfk_1` FOREIGN KEY (`SessionID`) REFERENCES `session` (`SessionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coachingsession_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coachingsession_ibfk_3` FOREIGN KEY (`CoachID`) REFERENCES `user` (`UserID`) ON UPDATE CASCADE;

--
-- Constraints for table `coachprofile`
--
ALTER TABLE `coachprofile`
  ADD CONSTRAINT `coachprofile_ibfk_1` FOREIGN KEY (`CoachID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coachreview`
--
ALTER TABLE `coachreview`
  ADD CONSTRAINT `coachreview_ibfk_1` FOREIGN KEY (`CoachID`) REFERENCES `coachprofile` (`CoachID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coachreview_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coach_skill_age_group_assignment`
--
ALTER TABLE `coach_skill_age_group_assignment`
  ADD CONSTRAINT `fk_csg_assigned_by` FOREIGN KEY (`AssignedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_csg_coach` FOREIGN KEY (`CoachID`) REFERENCES `coachprofile` (`CoachID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coach_tournament_recommendations`
--
ALTER TABLE `coach_tournament_recommendations`
  ADD CONSTRAINT `fk_ctr_coach` FOREIGN KEY (`CoachID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_ctr_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_ctr_reviewer` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ctr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE;

--
-- Constraints for table `contactmessage`
--
ALTER TABLE `contactmessage`
  ADD CONSTRAINT `contactmessage_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `contactmessage_ibfk_2` FOREIGN KEY (`RespondedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `contactus`
--
ALTER TABLE `contactus`
  ADD CONSTRAINT `contactus_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `contactus_ibfk_2` FOREIGN KEY (`RespondedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `crimatch`
--
ALTER TABLE `crimatch`
  ADD CONSTRAINT `crimatch_ibfk_1` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emaillog`
--
ALTER TABLE `emaillog`
  ADD CONSTRAINT `emaillog_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `equipmentcart`
--
ALTER TABLE `equipmentcart`
  ADD CONSTRAINT `equipmentcart_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentcart_ibfk_2` FOREIGN KEY (`EquipmentID`) REFERENCES `equipment` (`EquipmentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `equipmentrental`
--
ALTER TABLE `equipmentrental`
  ADD CONSTRAINT `equipmentrental_ibfk_1` FOREIGN KEY (`EquipmentID`) REFERENCES `equipment` (`EquipmentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentrental_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentrental_ibfk_3` FOREIGN KEY (`ProcessedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentrental_ibfk_4` FOREIGN KEY (`ReturnInspectedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `equipmentreview`
--
ALTER TABLE `equipmentreview`
  ADD CONSTRAINT `equipmentreview_ibfk_1` FOREIGN KEY (`EquipmentID`) REFERENCES `equipment` (`EquipmentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentreview_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentreview_ibfk_3` FOREIGN KEY (`RentalID`) REFERENCES `equipmentrental` (`RentalID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipmentreview_ibfk_4` FOREIGN KEY (`ModeratedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `eventenrollment`
--
ALTER TABLE `eventenrollment`
  ADD CONSTRAINT `eventenrollment_ibfk_1` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventenrollment_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facilitybooking`
--
ALTER TABLE `facilitybooking`
  ADD CONSTRAINT `facilitybooking_ibfk_1` FOREIGN KEY (`FacilityID`) REFERENCES `facility` (`FacilityID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facilitybooking_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facilitybooking_ibfk_3` FOREIGN KEY (`BookedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`FromUserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`ToUserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `livenotification`
--
ALTER TABLE `livenotification`
  ADD CONSTRAINT `livenotification_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nutritionplan_player`
--
ALTER TABLE `nutritionplan_player`
  ADD CONSTRAINT `nutritionplan_player_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `nutritionplan` (`PlanID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nutritionplan_player_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `performanceupdate`
--
ALTER TABLE `performanceupdate`
  ADD CONSTRAINT `performanceupdate_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `performanceupdate_ibfk_2` FOREIGN KEY (`CoachID`) REFERENCES `user` (`UserID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `performanceupdate_ibfk_3` FOREIGN KEY (`SessionID`) REFERENCES `session` (`SessionID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `performanceupdate_ibfk_4` FOREIGN KEY (`ApprovedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `playercoach`
--
ALTER TABLE `playercoach`
  ADD CONSTRAINT `playercoach_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playercoach_ibfk_2` FOREIGN KEY (`CoachID`) REFERENCES `coachprofile` (`CoachID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playercoachassignment`
--
ALTER TABLE `playercoachassignment`
  ADD CONSTRAINT `playercoachassignment_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playercoachassignment_ibfk_2` FOREIGN KEY (`CoachID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playermatchperformance`
--
ALTER TABLE `playermatchperformance`
  ADD CONSTRAINT `fk_performance_added_by` FOREIGN KEY (`AddedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_performance_verified_by` FOREIGN KEY (`VerifiedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `playermatchperformance_ibfk_1` FOREIGN KEY (`MatchID`) REFERENCES `crimatch` (`MatchID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playermatchperformance_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playermedicalrecord`
--
ALTER TABLE `playermedicalrecord`
  ADD CONSTRAINT `playermedicalrecord_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playermedicalrecord_ibfk_2` FOREIGN KEY (`ReportedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `playeroverallstats`
--
ALTER TABLE `playeroverallstats`
  ADD CONSTRAINT `playeroverallstats_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playeroverallstats_ibfk_2` FOREIGN KEY (`LastUpdatedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `playerprofile`
--
ALTER TABLE `playerprofile`
  ADD CONSTRAINT `playerprofile_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playersubscription`
--
ALTER TABLE `playersubscription`
  ADD CONSTRAINT `playersubscription_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playersubscription_ibfk_2` FOREIGN KEY (`PlanID`) REFERENCES `membershipplan` (`PlanID`) ON UPDATE CASCADE;

--
-- Constraints for table `playertournamentstats`
--
ALTER TABLE `playertournamentstats`
  ADD CONSTRAINT `playertournamentstats_ibfk_1` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playertournamentstats_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playertrainer`
--
ALTER TABLE `playertrainer`
  ADD CONSTRAINT `playertrainer_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playertrainer_ibfk_2` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playertrainerassignment`
--
ALTER TABLE `playertrainerassignment`
  ADD CONSTRAINT `playertrainerassignment_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playertrainerassignment_ibfk_2` FOREIGN KEY (`TrainerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `player_skill_coach_assignment`
--
ALTER TABLE `player_skill_coach_assignment`
  ADD CONSTRAINT `fk_psca_assigned_by` FOREIGN KEY (`AssignedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_psca_coach` FOREIGN KEY (`CoachID`) REFERENCES `coachprofile` (`CoachID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_psca_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`UpdatedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `productcart`
--
ALTER TABLE `productcart`
  ADD CONSTRAINT `productcart_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productcart_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `productorder`
--
ALTER TABLE `productorder`
  ADD CONSTRAINT `productorder_ibfk_1` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productorder_ibfk_2` FOREIGN KEY (`ProcessedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `productorderitem`
--
ALTER TABLE `productorderitem`
  ADD CONSTRAINT `productorderitem_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `productorder` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productorderitem_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `productreview`
--
ALTER TABLE `productreview`
  ADD CONSTRAINT `productreview_ibfk_1` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productreview_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productreview_ibfk_3` FOREIGN KEY (`OrderID`) REFERENCES `productorder` (`OrderID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `productreview_ibfk_4` FOREIGN KEY (`ModeratedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`CoachOrTrainerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shopemployeeprofile`
--
ALTER TABLE `shopemployeeprofile`
  ADD CONSTRAINT `shopemployeeprofile_ibfk_1` FOREIGN KEY (`ShopEmployeeID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `slot_audit_log`
--
ALTER TABLE `slot_audit_log`
  ADD CONSTRAINT `fk_scl_changer` FOREIGN KEY (`ChangedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `slot_booking`
--
ALTER TABLE `slot_booking`
  ADD CONSTRAINT `fk_sb_occurrence` FOREIGN KEY (`OccurrenceID`) REFERENCES `slot_occurrence` (`OccurrenceID`),
  ADD CONSTRAINT `fk_sb_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_sb_subscription` FOREIGN KEY (`SubscriptionID`) REFERENCES `playersubscription` (`SubscriptionID`);

--
-- Constraints for table `slot_occurrence`
--
ALTER TABLE `slot_occurrence`
  ADD CONSTRAINT `fk_occ_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility` (`FacilityID`),
  ADD CONSTRAINT `fk_occ_slot` FOREIGN KEY (`SlotID`) REFERENCES `slot_time_band` (`SlotID`),
  ADD CONSTRAINT `fk_occ_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template` (`TemplateID`);

--
-- Constraints for table `slot_occurrence_staff_override`
--
ALTER TABLE `slot_occurrence_staff_override`
  ADD CONSTRAINT `fk_soso_occurrence` FOREIGN KEY (`OccurrenceID`) REFERENCES `slot_occurrence` (`OccurrenceID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_soso_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `slot_private_session_request`
--
ALTER TABLE `slot_private_session_request`
  ADD CONSTRAINT `fk_private_session_request_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility` (`FacilityID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_private_session_request_occurrence` FOREIGN KEY (`ApprovedOccurrenceID`) REFERENCES `slot_occurrence` (`OccurrenceID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_private_session_request_requester` FOREIGN KEY (`RequesterUserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_private_session_request_reviewed_by` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_private_session_request_slot` FOREIGN KEY (`SlotID`) REFERENCES `slot_time_band` (`SlotID`) ON UPDATE CASCADE;

--
-- Constraints for table `slot_template`
--
ALTER TABLE `slot_template`
  ADD CONSTRAINT `fk_st_creator` FOREIGN KEY (`CreatedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_st_facility` FOREIGN KEY (`FacilityID`) REFERENCES `facility` (`FacilityID`),
  ADD CONSTRAINT `fk_st_slot` FOREIGN KEY (`SlotID`) REFERENCES `slot_time_band` (`SlotID`);

--
-- Constraints for table `slot_template_player_assignment`
--
ALTER TABLE `slot_template_player_assignment`
  ADD CONSTRAINT `fk_stpa_assigned_by` FOREIGN KEY (`AssignedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stpa_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stpa_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template` (`TemplateID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `slot_template_staff`
--
ALTER TABLE `slot_template_staff`
  ADD CONSTRAINT `fk_tstaff_assigner` FOREIGN KEY (`AssignedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_tstaff_template` FOREIGN KEY (`TemplateID`) REFERENCES `slot_template` (`TemplateID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tstaff_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `subscriptionpayment`
--
ALTER TABLE `subscriptionpayment`
  ADD CONSTRAINT `subscriptionpayment_ibfk_1` FOREIGN KEY (`SubscriptionID`) REFERENCES `playersubscription` (`SubscriptionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subscriptionpayment_ibfk_2` FOREIGN KEY (`ProcessedBy`) REFERENCES `shopemployeeprofile` (`ShopEmployeeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `supplementplan`
--
ALTER TABLE `supplementplan`
  ADD CONSTRAINT `supplementplan_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplement_player`
--
ALTER TABLE `supplement_player`
  ADD CONSTRAINT `supplement_player_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `supplementplan` (`PlanID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplement_player_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournament`
--
ALTER TABLE `tournament`
  ADD CONSTRAINT `tournament_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournamentplayer`
--
ALTER TABLE `tournamentplayer`
  ADD CONSTRAINT `fk_tp_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_tp_selected` FOREIGN KEY (`SelectedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_tp_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE;

--
-- Constraints for table `tournament_join_request`
--
ALTER TABLE `tournament_join_request`
  ADD CONSTRAINT `fk_tjr_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_tjr_reviewer` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_tjr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE;

--
-- Constraints for table `trainerappointment`
--
ALTER TABLE `trainerappointment`
  ADD CONSTRAINT `trainerappointment_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trainerappointment_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trainerprofile`
--
ALTER TABLE `trainerprofile`
  ADD CONSTRAINT `trainerprofile_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trainerreview`
--
ALTER TABLE `trainerreview`
  ADD CONSTRAINT `trainerreview_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trainerreview_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trainer_tournament_recommendations`
--
ALTER TABLE `trainer_tournament_recommendations`
  ADD CONSTRAINT `fk_ttr_player` FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_ttr_reviewer` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_ttr_tournament` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ttr_trainer` FOREIGN KEY (`TrainerID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `userpermissions`
--
ALTER TABLE `userpermissions`
  ADD CONSTRAINT `userpermissions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workoutplan`
--
ALTER TABLE `workoutplan`
  ADD CONSTRAINT `fk_workoutplan_user` FOREIGN KEY (`TrainerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workoutplan_player`
--
ALTER TABLE `workoutplan_player`
  ADD CONSTRAINT `fk2_wpp_assigned_by` FOREIGN KEY (`AssignedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `workoutplan_player_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `workoutplan` (`PlanID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workoutplan_player_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
