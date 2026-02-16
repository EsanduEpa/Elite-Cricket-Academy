-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 16, 2026 at 06:56 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
(18, 17, 'account_created', 'New Coach account created', NULL, NULL, '2025-10-22 13:55:27'),
(19, 18, 'account_created', 'New Player account created', NULL, NULL, '2025-10-22 21:19:54'),
(20, 18, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 21:19:54'),
(22, 20, 'account_created', 'New Player account created', NULL, NULL, '2025-10-23 10:08:20'),
(23, 20, 'account_created', 'New player account registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-23 10:08:20'),
(26, 1, 'Event Created', 'Created new event: Mu2 (Match)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 10:08:22'),
(27, 1, 'Event Updated', 'Updated event: Mu2456 (Match)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 10:15:57'),
(30, 23, 'account_created', 'New Coach account created', NULL, NULL, '2026-02-10 15:40:17'),
(31, 1, 'Staff Created', 'Added new staff member: Esandu Epa (Coach)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 15:40:17'),
(41, 1, 'Player Created', 'Added new player: Esandu Epa', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 16:52:54'),
(42, 1, 'Player Deleted', 'Deleted player: Esandu Epa (ID: 22)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 17:18:18'),
(43, 1, 'Player Deleted', 'Deleted player: Esandu Epa (ID: 32)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 17:22:06'),
(44, 1, 'Player Deleted', 'Deleted player: Esandu Epa (ID: 21)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 17:22:18'),
(45, 1, 'Event Created', 'Created new event: ABC23 (Training Camp)', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 17:32:05');

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
  `Reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='One-on-one appointments between coaches and players';

-- --------------------------------------------------------

--
-- Stand-in structure for view `coachingeffectiveness`
-- (See below for the actual view)
--
CREATE TABLE `coachingeffectiveness` (
`CoachID` int(11)
,`CoachName` varchar(255)
,`Specialization` enum('Batting','Bowling','All-rounder','Wicket-keeping')
,`PlayersAssigned` bigint(21)
,`PerformanceUpdatesGiven` bigint(21)
,`SessionsLogged` bigint(21)
,`AvgSessionRating` decimal(14,4)
,`AvgTechnicalRatingGiven` decimal(14,4)
,`SessionsAttended` bigint(21)
,`SessionsMissed` bigint(21)
);

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
CREATE TABLE `coachplayerpermissions` (
`CoachID` int(11)
,`CoachName` varchar(255)
,`IsHeadCoach` tinyint(1)
,`Role` enum('Admin','ShopEmployee','Coach','Trainer','Player')
,`PlayerID` int(11)
,`PlayerName` varchar(255)
,`AssignmentStatus` enum('active','inactive','completed')
,`AssignmentType` enum('regular','private','both')
,`AssignedDate` datetime
,`PermissionLevel` varchar(22)
);

-- --------------------------------------------------------

--
-- Table structure for table `coachprofile`
--

CREATE TABLE `coachprofile` (
  `CoachID` int(11) NOT NULL,
  `Specialization` enum('Batting','Bowling','All-rounder','Wicket-keeping') DEFAULT NULL,
  `Experience` int(11) DEFAULT NULL COMMENT 'Years of experience',
  `Certifications` text DEFAULT NULL,
  `IsHeadCoach` tinyint(1) DEFAULT 0 COMMENT 'Head coach flag - only one per academy with enhanced permissions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Coach-specific profile with head coach promotion capabilities';

--
-- Dumping data for table `coachprofile`
--

INSERT INTO `coachprofile` (`CoachID`, `Specialization`, `Experience`, `Certifications`, `IsHeadCoach`) VALUES
(9, 'Batting', 5, NULL, 0),
(11, 'Bowling', 10, NULL, 0),
(12, 'Bowling', 7, NULL, 0),
(17, 'Bowling', 0, NULL, 0),
(23, NULL, NULL, NULL, 0);

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
  `Date` date NOT NULL,
  `Venue` varchar(255) DEFAULT NULL,
  `OpponentTeam` varchar(255) NOT NULL,
  `Result` enum('win','loss','draw','no-result','pending') DEFAULT 'pending',
  `OurScore` varchar(50) DEFAULT NULL,
  `OpponentScore` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual matches within tournaments';

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
(17, 17, 'v3@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 13:55:27'),
(18, 18, 'jini.a.lm2@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 21:19:54'),
(19, NULL, 'coach@cricketacademy.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-22 21:30:39'),
(20, 20, 'esanduepa0225@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2025-10-23 10:08:20'),
(21, NULL, 'esanduepa022555@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-01-17 09:19:41'),
(22, NULL, 'esanduepa0225777@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-03 14:11:50'),
(23, 23, 'esanduepa0225111@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 15:40:17'),
(24, NULL, 'esanduepa0225hvj@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:37:16'),
(25, NULL, 'esanduepa0225hvj@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:41:20'),
(26, NULL, 'esanduepa0225gugkgf@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:41:52'),
(27, NULL, 'esanduepa0225gugkgf@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:42:07'),
(28, NULL, 'esanduepa02258888@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:44:35'),
(29, NULL, 'esanduepa02256677@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:45:43'),
(30, NULL, 'esanduepa0225787@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:48:37'),
(31, NULL, 'esanduepa0225787@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:49:02'),
(32, NULL, 'esanduepa0225787@gmail.com', 'Welcome to Cricket Academy', 'welcome', 'queued', NULL, NULL, '2026-02-10 16:52:54');

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
  `EqCondition` enum('new','good','fair','poor') DEFAULT 'good'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cricket equipment available for rental or purchase';

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`EquipmentID`, `Name`, `Description`, `Category`, `AvailabilityStatus`, `RentalPrice`, `PurchasePrice`, `Stock`, `EqCondition`) VALUES
(1, 'Cricket Bat - Professional', 'High-quality willow cricket bat', 'Batting', 'rented', 25.00, 150.00, 0, 'new'),
(2, 'Cricket Ball - Leather', 'Professional leather cricket ball', 'Bowling', 'rented', 5.00, 15.00, 0, 'new'),
(3, 'Helmet - Professional', 'Safety helmet with grill', 'Protective', 'rented', 15.00, 80.00, 0, 'new'),
(4, 'Batting Pads', 'Professional batting pads', 'Protective', 'available', 20.00, 100.00, 0, 'good'),
(5, 'Wicket Keeping Gloves', 'Professional WK gloves', 'Protective', 'available', 18.00, 90.00, 0, 'good'),
(6, 'Cricket Bat - Professional', 'High-quality willow cricket bat', 'Batting', 'available', 25.00, 150.00, 0, 'new'),
(7, 'Cricket Ball - Leather', 'Professional leather cricket ball', 'Bowling', 'available', 5.00, 15.00, 0, 'new'),
(8, 'Helmet - Professional', 'Safety helmet with grill', 'Protective', 'available', 15.00, 80.00, 0, 'new'),
(9, 'Batting Pads', 'Professional batting pads', 'Protective', 'available', 20.00, 100.00, 0, 'good'),
(10, 'Wicket Keeping Gloves', 'Professional WK gloves', 'Protective', 'available', 18.00, 90.00, 0, 'good'),
(11, 'Professional Cricket Bat', 'Premium English willow bat for professional players', 'Batting', 'available', 25.00, 150.00, 0, ''),
(12, 'Complete Training Kit', 'Includes cones, stumps, practice balls, and agility equipment', 'Training', 'available', 20.00, 100.00, 0, 'good'),
(13, 'Wicket Keeping Set', 'Professional wicket keeping gloves and pads', 'Protective', 'available', 22.00, 80.00, 0, ''),
(14, 'Junior Cricket Set', 'Complete cricket set designed for junior players', 'Batting', 'available', 18.00, 75.00, 0, 'good');

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
(1, 1, 7, '2026-01-15', '2026-01-17 18:47:36', '2026-01-17 23:47:36', 'active', 500.00, 7, NULL, NULL, 0.00),
(2, 2, 16, '2026-01-16', '2026-01-17 19:47:36', '2026-01-18 00:47:36', 'active', 300.00, 7, NULL, NULL, 0.00),
(3, 3, 20, '2026-01-17', '2026-01-17 20:17:36', '2026-01-18 02:47:36', 'active', 450.00, 7, NULL, NULL, 0.00),
(4, 4, 7, '2026-01-12', '2026-01-15 20:47:36', '2026-01-16 20:47:36', 'returned', 500.00, 7, NULL, NULL, 0.00),
(5, 5, 16, '2026-01-10', '2026-01-12 20:47:36', '2026-01-14 20:47:36', 'returned', 300.00, 7, NULL, NULL, 0.00);

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
(4, 'elite summer training', 'Training Camp', 'junior', NULL, '2025-10-31 07:00:00', '2025-10-31 17:00:00', 'main ground', 'upcoming', '2025-10-22 16:41:00', '2025-10-23 15:41:00', 'esandu', 'esandu@gmail.com', '0987654321', NULL, NULL),
(5, 'summer camp', 'Training Camp', 'junior', NULL, '2025-10-30 20:59:00', '2025-10-31 03:04:00', 'main ground two', 'upcoming', '2025-10-23 20:00:00', '2025-10-24 20:02:00', 'esandu', 'e@gmail.com', '0987654321', 10000, 1000000.00),
(7, 'Music Fest', 'Trial', 'academy', 'Relaxation', '2025-10-23 01:04:00', '2025-10-23 01:35:00', 'Ground', 'ongoing', '2025-10-23 01:03:00', '2025-10-23 01:04:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', 100, NULL),
(8, 'Music Fest23', 'Seminar', 'senior', 'Relaxation', '2025-10-23 04:05:00', '2025-10-23 07:05:00', 'Ground', 'upcoming', '2025-10-23 01:05:00', '2025-10-23 01:10:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', NULL, NULL),
(9, 'Music Fest12356', 'Training Camp', 'youth', 'good', '2025-10-24 10:34:00', '2025-10-28 15:33:00', '2Ground', 'upcoming', '2025-10-23 10:35:00', '2025-10-23 10:39:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', 11, 1.00),
(10, 'Mu3', 'Training Camp', 'senior', 'dcvfrverv', '2025-10-28 00:49:00', '2025-10-29 00:52:00', 'Jwddd', 'upcoming', '2025-10-27 00:47:00', '2025-10-27 00:50:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', 7, 0.04),
(11, 'Mu55555910', 'Training Camp', 'junior', 'ycjjfufu', '2026-01-18 09:51:00', '2026-01-28 09:51:00', 'Ground', 'upcoming', '2026-01-17 09:53:00', '2026-01-18 09:51:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', NULL, 65.00),
(12, 'Mu2456', 'Match', 'senior', 'wevrvev', '2026-01-17 14:08:00', '2026-01-18 10:11:00', 'Ground', 'upcoming', '2026-01-17 10:08:00', '2026-01-17 10:11:00', 'Esandu Epa', 'esanduepa0225@gmail.com', '+94774267307', NULL, NULL),
(13, 'ABC23', 'Training Camp', 'senior', 'Good', '2026-02-11 17:31:00', '2026-02-19 17:34:00', 'Ground', 'upcoming', '2026-02-10 17:31:00', '2026-02-10 17:33:00', 'Esandu', 'esanduepa0225@gmail.com', '+94774267307', NULL, NULL);

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
  `Name` varchar(255) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Capacity` int(11) NOT NULL,
  `AvailabilityStatus` enum('available','occupied','maintenance') DEFAULT 'available',
  `HourlyRate` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cricket facilities like nets, bowling machines, grounds';

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`FacilityID`, `Name`, `Location`, `Capacity`, `AvailabilityStatus`, `HourlyRate`) VALUES
(1, 'Practice Net 1', 'North Ground', 6, 'available', 50.00),
(2, 'Practice Net 2', 'North Ground', 6, 'available', 50.00),
(3, 'Bowling Machine', 'Training Center', 1, 'available', 75.00),
(4, 'Main Ground', 'Center Field', 22, 'available', 200.00),
(5, 'Indoor Training Hall', 'Building A', 20, 'available', 100.00),
(6, 'Practice Net 1', 'North Ground', 6, 'available', 50.00),
(7, 'Practice Net 2', 'North Ground', 6, 'available', 50.00),
(8, 'Bowling Machine', 'Training Center', 1, 'available', 75.00),
(9, 'Main Ground', 'Center Field', 22, 'available', 200.00),
(10, 'Indoor Training Hall', 'Building A', 20, 'available', 100.00);

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
(11, 2, 3, 'The training sessions have been excellent. Coach really knows how to motivate the team.', 5, 'coach', '2026-02-05 18:12:38', 'resolved'),
(12, 6, 3, 'Coach Sarath explains techniques very clearly. Batting improved significantly.', 5, 'coach', '2026-02-11 13:33:48', 'pending'),
(13, 7, 3, 'Great group sessions, could use more individual feedback during practice.', 4, 'coach', '2026-02-11 13:33:48', 'pending'),
(14, 15, 3, 'The bowling drills are very effective. Would like more advanced sessions.', 4, 'coach', '2026-02-11 13:33:48', 'reviewed'),
(15, 18, 3, 'Excellent coaching style. Very motivating and patient with beginners.', 5, 'coach', '2026-02-11 13:33:48', 'resolved');

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
  `Status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Different membership plans with varying benefits';

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
(17, 17, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-22 13:55:27'),
(18, 18, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-22 21:19:54'),
(20, 20, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2025-10-23 10:08:20'),
(23, 23, 'welcome', 'Welcome to Cricket Academy', 'Your account has been created successfully. Please complete your profile setup.', NULL, NULL, 0, NULL, '2026-02-10 15:40:17'),
(33, 3, 'session', 'New Session Booking', 'Player Esandu has enrolled in your batting session', NULL, NULL, 0, NULL, '2026-02-11 12:33:48'),
(34, 3, 'injury', 'Injury Report Filed', 'Swairi reported a minor knee strain during practice', NULL, NULL, 0, NULL, '2026-02-11 10:33:48'),
(35, 3, 'event', 'Tournament Update', 'Junior Championship registration deadline extended to March 1', NULL, NULL, 0, NULL, '2026-02-10 13:33:48'),
(36, 3, 'player', 'Player Achievement', 'Vijini achieved Man of the Match in elite league', NULL, NULL, 1, NULL, '2026-02-09 13:33:48'),
(37, 3, 'session', 'Session Feedback', 'Please provide feedback for your completed sessions this week', NULL, NULL, 1, NULL, '2026-02-08 13:33:48'),
(38, 3, 'system', 'Profile Update Reminder', 'Please update your coaching certifications for the new term', NULL, NULL, 1, NULL, '2026-02-06 13:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `nutritionplan`
--

CREATE TABLE `nutritionplan` (
  `PlanID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `nutritionPlanName` varchar(255) DEFAULT NULL,
  `DietDetails` text NOT NULL,
  `Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customized nutrition plans for players';

-- --------------------------------------------------------

--
-- Table structure for table `nutritionplan_player`
--

CREATE TABLE `nutritionplan_player` (
  `PlanID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AssignedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
DELIMITER $$
CREATE TRIGGER `tr_update_overall_stats` AFTER UPDATE ON `performanceupdate` FOR EACH ROW BEGIN
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
,`AvgTechnicalRating` decimal(14,4)
,`AvgFitnessRating` decimal(14,4)
,`AvgAttitudeRating` decimal(14,4)
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
(1, 6, 3, 'regular', '2026-02-11 13:33:48', 'active', 'Regular training group'),
(2, 7, 3, 'regular', '2026-02-11 13:33:48', 'active', 'Regular training group'),
(3, 15, 3, 'private', '2026-02-11 13:33:48', 'active', 'Private batting sessions'),
(4, 16, 3, 'regular', '2026-02-11 13:33:48', 'active', 'Regular training group'),
(5, 18, 3, 'both', '2026-02-11 13:33:48', 'active', 'Private and group sessions'),
(6, 20, 3, 'regular', '2026-02-11 13:33:48', 'active', 'Regular training group');

-- --------------------------------------------------------

--
-- Stand-in structure for view `playerdevelopmenttracking`
-- (See below for the actual view)
--
CREATE TABLE `playerdevelopmenttracking` (
`PlayerID` int(11)
,`PlayerName` varchar(255)
,`Age` int(5)
,`BattingStyle` enum('Right-handed','Left-handed','Switch-hitter')
,`BowlingStyle` enum('Fast','Medium','Spin','Off-spin','Leg-spin','None')
,`TotalRuns` int(11)
,`TotalWickets` int(11)
,`BattingAverage` decimal(6,2)
,`BowlingAverage` decimal(6,2)
,`SessionsAttended` bigint(21)
,`PerformanceUpdatesReceived` bigint(21)
,`AvgSessionRating` decimal(14,4)
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
  `Rating` decimal(3,1) DEFAULT 0.0 COMMENT 'Performance rating out of 10'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual player performance in matches';

-- --------------------------------------------------------

--
-- Table structure for table `playermedicalrecord`
--

CREATE TABLE `playermedicalrecord` (
  `RecordID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `InjuryDetails` text DEFAULT NULL,
  `Diagnosis` text DEFAULT NULL,
  `TreatmentGiven` text DEFAULT NULL,
  `RecoveryStatus` enum('recovering','recovered','chronic','ongoing') DEFAULT 'ongoing',
  `InjuryDate` date NOT NULL,
  `HappenedAtAcademy` enum('yes','no') DEFAULT 'no' COMMENT 'Did the injury occur at the academy?',
  `RestDaysNeeded` int(11) DEFAULT 0 COMMENT 'Estimated rest days required for recovery',
  `DiagnosisReceiptURL` varchar(255) DEFAULT NULL COMMENT 'Path or URL of uploaded diagnosis receipt image/file',
  `ReportedDate` date NOT NULL,
  `ReportedBy` int(11) DEFAULT NULL COMMENT 'Doctor, trainer, or player who reported',
  `verifyStatus` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Medical records and injury tracking';

--
-- Dumping data for table `playermedicalrecord`
--

INSERT INTO `playermedicalrecord` (`RecordID`, `PlayerID`, `InjuryDetails`, `Diagnosis`, `TreatmentGiven`, `RecoveryStatus`, `InjuryDate`, `HappenedAtAcademy`, `RestDaysNeeded`, `DiagnosisReceiptURL`, `ReportedDate`, `ReportedBy`, `verifyStatus`) VALUES
(4, 15, 'fell on the staircase', 'Torn tissue', 'iced, rest for 2 days', 'recovering', '2025-10-22', 'no', 0, NULL, '2025-10-19', 15, 'pending'),
(8, 16, 'knee cap fracture', 'fracture in the knee cap', 'oriental medicine', 'recovered', '2025-10-22', 'no', 0, NULL, '2025-10-22', 16, 'pending'),
(10, 15, 'knee injury', 'thinner thigh muscles has caused the limb bones to be contact with the knee cap, making painful to walk.', 'exercises and calcium pills', 'ongoing', '2025-10-08', 'no', 3, 'uploads/medical_receipts/receipt_15_1761138553.png', '2025-10-22', 15, 'rejected'),
(11, 15, 'skull fracture', 'skull fracture', 'stitches', 'recovered', '2025-10-15', 'no', 2, 'uploads/medical_receipts/receipt_15_1761147506.pdf', '2025-10-22', 15, 'pending'),
(12, 18, 'back pain for 3 days', 'displaced disk', 'physio therapy', 'recovered', '2025-10-15', 'no', 23, 'uploads/medical_receipts/receipt_18_1761148482.png', '2025-10-22', 18, 'rejected'),
(13, 15, 'Yes well', 'no no no', '', 'recovered', '2025-10-22', 'yes', 5, NULL, '2025-10-23', 15, 'pending');

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
  `LastUpdatedBy` int(11) DEFAULT NULL COMMENT 'Coach who last updated the stats'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Overall career statistics for each player with coach tracking';

--
-- Dumping data for table `playeroverallstats`
--

INSERT INTO `playeroverallstats` (`StatsID`, `PlayerID`, `MatchesPlayed`, `TotalRuns`, `TotalWickets`, `HighestScore`, `BattingAverage`, `BowlingAverage`, `StrikeRate`, `EconomyRate`, `LastUpdated`, `LastUpdatedBy`) VALUES
(1, 7, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-18 16:08:42', NULL),
(2, 15, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-19 21:30:13', NULL),
(3, 16, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-22 13:22:54', NULL),
(4, 18, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-22 21:19:54', NULL),
(5, 20, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2025-10-23 10:08:20', NULL),
(18, 6, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, '2026-02-11 14:15:36', NULL);

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
(6, 'Left-handed', 'Spin', 45, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Right-handed', 'Fast', 78, 'basic', 'gamage', '0986123456', 'gamage .S', '0986123456', 'Elite Cricket Academy', 'none', 'none', 'Social Media'),
(16, '', '', NULL, 'basic', 'waa', '', '', '', 'SLIIT', '', '', ''),
(18, 'Right-handed', 'Medium', 21, 'basic', '', '', '', '', 'SLIIT', '', '', ''),
(20, NULL, NULL, NULL, 'basic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
  `AutoRenewal` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player membership subscriptions for regular group sessions';

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
(9, 'sssssss', 'sss', 'Protective', 'www', 99999999.99, 2, 'active', 'd', 22.000, '222', '2025-10-22 13:16:14', NULL, 'uploads/shop_product/product_9_1761119174.png'),
(12, 'Essa2', 'aa', 'Protective', '111', 1.00, 5, 'active', NULL, 11.000, '2 X 3 X 6', '2025-10-23 02:17:53', NULL, NULL),
(13, 'essa2', 'gofojasiocilaebg', 'Batting', 'aaa', 90.00, 3, 'active', NULL, 23.000, '2 X 3 X 6', '2025-10-23 10:45:17', NULL, NULL);

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

INSERT INTO `productorder` (`OrderID`, `PlayerID`, `OrderDate`, `TotalAmount`, `PaymentMethod`, `Status`, `ProcessedBy`, `ShippingAddress`, `OrderNotes`) VALUES
(11, 7, '2026-01-12 20:47:36', 4500.00, 'card', 'completed', 7, NULL, 'First order - Cricket bat and gloves'),
(12, 16, '2026-01-14 20:47:36', 2800.00, 'cash', 'completed', 7, NULL, 'Protective gear purchase'),
(13, 7, '2026-01-15 20:47:36', 6200.00, 'online', 'processing', 7, NULL, 'Complete cricket kit'),
(14, 20, '2026-01-16 20:47:36', 1500.00, 'card', 'pending', NULL, NULL, 'Cricket balls order');

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

--
-- Dumping data for table `productorderitem`
--

INSERT INTO `productorderitem` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`, `UnitPrice`, `SubTotal`) VALUES
(8, 11, 13, 2, 2000.00, 4000.00),
(9, 11, 9, 1, 500.00, 500.00),
(10, 12, 9, 3, 800.00, 2400.00),
(11, 12, 12, 2, 200.00, 400.00),
(12, 13, 13, 3, 2000.00, 6000.00),
(13, 14, 12, 5, 300.00, 1500.00);

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

--
-- Dumping data for table `productreview`
--

INSERT INTO `productreview` (`ReviewID`, `ProductID`, `PlayerID`, `OrderID`, `Rating`, `ReviewTitle`, `ReviewText`, `Pros`, `Cons`, `WouldRecommend`, `VerifiedPurchase`, `ReviewDate`, `Status`, `ModeratedBy`, `ModerationNotes`, `HelpfulVotes`) VALUES
(5, 13, 7, NULL, 5, NULL, 'Excellent quality cricket bat, very happy with the purchase!', NULL, NULL, 1, 1, '2026-01-14 20:47:36', 'pending', NULL, NULL, 0),
(6, 9, 16, NULL, 4, NULL, 'Good protective gear, fits well.', NULL, NULL, 1, 1, '2026-01-15 20:47:36', 'pending', NULL, NULL, 0),
(7, 12, 20, NULL, 4, NULL, 'Cricket balls are of good quality for practice.', NULL, NULL, 1, 1, '2026-01-16 20:47:36', 'pending', NULL, NULL, 0),
(8, 9, 7, NULL, 5, NULL, 'Great value for money!', NULL, NULL, 1, 1, '2026-01-13 20:47:36', 'approved', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `SessionID` int(11) NOT NULL,
  `SessionType` enum('Coaching','Physical Training') NOT NULL,
  `SessionMode` enum('Group','Private') DEFAULT 'Group',
  `CoachOrTrainerID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Status` enum('active','cancelled','completed') DEFAULT 'active',
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
(9, 'Coaching', 'Group', 3, 'Morning Batting Practice', '2026-02-11', '09:00:00', '11:00:00', 'Main Ground', 'active', 15, 500.00, 1),
(10, 'Coaching', 'Private', 3, 'Advanced Bowling Technique', '2026-02-11', '14:00:00', '15:30:00', 'Indoor Net 1', 'active', 3, 1500.00, 0),
(11, 'Coaching', 'Group', 3, 'Fielding Drills', '2026-02-12', '08:00:00', '10:00:00', 'Practice Ground', 'active', 20, 500.00, 1),
(12, 'Coaching', 'Private', 3, 'Spin Bowling Masterclass', '2026-02-13', '10:00:00', '11:30:00', 'Indoor Net 2', 'active', 5, 2000.00, 0),
(13, 'Coaching', 'Group', 3, 'Match Simulation', '2026-02-14', '07:00:00', '12:00:00', 'Main Ground', 'active', 22, 750.00, 0),
(14, 'Coaching', 'Group', 3, 'Weekend Fitness and Cricket', '2026-02-16', '06:00:00', '08:00:00', 'Fitness Center', 'active', 25, 500.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `SessionAttendance`
--

CREATE TABLE `SessionAttendance` (
  `AttendanceID` int(11) NOT NULL,
  `EnrollmentID` int(11) NOT NULL,
  `AttendanceStatus` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `AttendanceNotes` text DEFAULT NULL,
  `MarkedBy` int(11) DEFAULT NULL,
  `MarkedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessionenrollment`
--

CREATE TABLE `sessionenrollment` (
  `EnrollmentID` int(11) NOT NULL,
  `SessionID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `EnrollmentDate` datetime DEFAULT current_timestamp(),
  `Status` enum('enrolled','attended','missed','cancelled') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Player enrollment in training sessions';

--
-- Dumping data for table `sessionenrollment`
--

INSERT INTO `sessionenrollment` (`EnrollmentID`, `SessionID`, `PlayerID`, `EnrollmentDate`, `Status`) VALUES
(24, 9, 6, '2026-02-11 14:15:36', 'enrolled'),
(25, 9, 7, '2026-02-11 14:15:36', 'enrolled'),
(26, 9, 15, '2026-02-11 14:15:36', 'enrolled'),
(27, 9, 16, '2026-02-11 14:15:36', 'enrolled'),
(28, 9, 18, '2026-02-11 14:15:36', 'attended'),
(29, 10, 6, '2026-02-11 14:15:36', 'enrolled'),
(30, 10, 15, '2026-02-11 14:15:36', 'enrolled'),
(31, 11, 7, '2026-02-11 14:15:36', 'enrolled'),
(32, 11, 16, '2026-02-11 14:15:36', 'enrolled'),
(33, 11, 18, '2026-02-11 14:15:36', 'enrolled'),
(34, 11, 20, '2026-02-11 14:15:36', 'enrolled'),
(35, 12, 6, '2026-02-11 14:15:36', 'enrolled'),
(36, 12, 15, '2026-02-11 14:15:36', 'enrolled'),
(37, 13, 6, '2026-02-11 14:15:36', 'enrolled'),
(38, 13, 7, '2026-02-11 14:15:36', 'enrolled'),
(39, 13, 15, '2026-02-11 14:15:36', 'enrolled'),
(40, 13, 16, '2026-02-11 14:15:36', 'enrolled'),
(41, 13, 18, '2026-02-11 14:15:36', 'enrolled'),
(42, 13, 20, '2026-02-11 14:15:36', 'enrolled'),
(43, 14, 7, '2026-02-11 14:15:36', 'enrolled'),
(44, 14, 16, '2026-02-11 14:15:36', 'enrolled'),
(45, 14, 18, '2026-02-11 14:15:36', 'enrolled'),
(46, 14, 20, '2026-02-11 14:15:36', 'enrolled');

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
(5, 'General', '2024-01-15'),
(7, 'General', '2024-01-01'),
(9, 'Equipment', '2024-03-01');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptionpayment`
--

CREATE TABLE `subscriptionpayment` (
  `PaymentID` int(11) NOT NULL,
  `SubscriptionID` int(11) NOT NULL,
  `PaymentDate` date NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('cash','card','bank_transfer','online') NOT NULL,
  `Status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `DueDate` date NOT NULL,
  `LateFee` decimal(10,2) DEFAULT 0.00,
  `ProcessedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Monthly subscription payments';

-- --------------------------------------------------------

--
-- Table structure for table `supplementplan`
--

CREATE TABLE `supplementplan` (
  `PlanID` int(11) NOT NULL,
  `TrainerID` int(11) NOT NULL,
  `SupplementPlanName` varchar(255) DEFAULT NULL,
  `SupplementDetails` text NOT NULL,
  `Dosage` varchar(255) DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL COMMENT 'Duration in days',
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Supplement recommendations for players';

-- --------------------------------------------------------

--
-- Table structure for table `supplement_player`
--

CREATE TABLE `supplement_player` (
  `PlanID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AssignedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `TournamentID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `tdate` date NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `CreatedBy` int(11) NOT NULL,
  `Status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `PrizePool` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cricket tournaments';

-- --------------------------------------------------------

--
-- Table structure for table `tournamentplayer`
--

CREATE TABLE `tournamentplayer` (
  `TournamentID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `Team` varchar(100) DEFAULT 'Academy Team',
  `RoleInTeam` enum('Captain','Vice-Captain','Wicket-Keeper','Batsman','Bowler','All-Rounder') DEFAULT NULL,
  `SelectedBy` int(11) DEFAULT NULL COMMENT 'Head coach who selected player'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Players selected for tournaments';

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
  `Reason` text DEFAULT NULL
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
(10, NULL, NULL);

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
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
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

INSERT INTO `user` (`UserID`, `Name`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `PasswordChangeDeadline`, `LastLoginAt`, `LoginAttempts`, `AccountLockedUntil`, `CreatedBy`, `Notes`, `ProfileImage`) VALUES
(1, 'Admin User', '1990-01-01', '+1234567891', 'admin@cricketacademy.com', 'Academy Headquarters', '', 'Admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:02:42', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(2, 'Academy manager', '1990-01-01', '+1234567890', 'manager@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Admin', 'Manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(3, 'Coach Sarath', '1990-01-01', '+1234567890', 'coach001@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Coach', 'coach001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(4, 'Trainer Hasitha', '1990-01-01', '+1234567890', 'trainer001@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Trainer', 'trainer001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(5, 'Shop Muditha', '1990-01-01', '+1234567890', 'shop001@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'ShopEmployee', 'shop001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(6, 'Player Esandu', '1990-01-01', '+1234567890', 'esandu@eliteca.com', 'Elite CA', 'Elite Cricket Academy', 'Player', 'esandu001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 13:10:39', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(7, 'Vijini', '1990-01-01', '+1234567890', 'vijini@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Player', 'avijini001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:08:42', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(8, 'admin2', '1990-01-01', '+1234567890', 'admin2@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Admin', 'admin2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:10:01', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_8_1761147214.png'),
(9, 'coach Kasun', '1990-01-01', '+1234567890', 'coach002@celiteca.com', 'Academy Address', 'Elite Cricket Academy', 'ShopEmployee', 'coach002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:12:47', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(10, 'Trainer subanu', '1990-01-01', '+1234567890', 'trainer002@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Trainer', 'trainer002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 16:13:52', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(11, 'coach Dharshana', '1990-01-01', '+1234567890', 'coach004@eliteca.com', 'Academy Headquarters', NULL, 'Coach', 'coach004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-10-18 23:38:52', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_11_1761114831.png'),
(12, 'Kumara Darmasena', '1980-01-01', '0771234567', 'kumara.darmasena@eliteacademy.com', NULL, NULL, 'Coach', 'kdarmasena', '$2y$10$.UJUjUwSObzJmzTGsJcL8.VfyJZ1f88FhX6Hl6RWbKyXCQf/lViPq', '2025-10-18 23:44:38', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(13, 'Ben Carter', '1980-01-01', '0771234567', 'ben.carter@eliteacademy.com', NULL, NULL, 'Coach', 'bcarter', '$2y$10$vlnExCdpdUaOrioO1BYegu2OEhqSUwfwAMGM.o4hK46NL9GkaEPBe', '2025-10-18 23:44:38', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(14, 'Kumar Sangakkara', '1980-01-01', '0771234567', 'kumar.sangakkara@eliteacademy.com', NULL, NULL, 'Coach', 'ksangakkara', '$2y$10$.UW163sEsWJ8e9NT/wnfi.6mf.cG0asQqhzBd5aZo9b12avkfEJX2', '2025-10-18 23:44:38', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(15, 'Swairi', '1990-01-01', '+1234567890', 'swairi@eliteca.com', 'Academy Address', 'Elite Cricket Academy', 'Player', 'Swairi', '$2y$10$JkEuF9xPMokzILpUV/fZ7OU.jYspHBkBOv3U6Deprgci1BjamM4uO', '2025-10-19 21:30:13', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_15_1761129401.jpeg'),
(16, 'esandu', '2003-06-04', '0123456789', 'e@g.com', '193/55,nugegoda', 'SLIIT', 'Player', 'esandu', '$2y$10$wzIcyBsyqcjG0KLOe5MUA.pPr33Vv704h2N27KW7a3IS3VupuzQ0m', '2025-10-22 13:22:54', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(17, 'Kumara Dharmasena', '1800-09-01', '0774267567', 'v3@gmail.com', '', '', 'Coach', 'kumarad', '$2y$10$fCpoynnqCDiRcRVJ3t.ltOVWh9T8rb6pWT8CKL7UQjCti9xkUpGcm', '2025-10-22 13:55:27', 'active', 0, NULL, NULL, 0, NULL, 8, NULL, 'uploads/profile_images/profile_17_1761123053.png'),
(18, 'vijini liyanamana', '2002-07-10', '0123456789', 'jini.a.lm2@gmail.com', '193/55,nugegoda', 'SLIIT', 'Player', 'vijinia', '$2y$10$r6Oe1uymncsattPzIRvxVu7gT8Cdf28rbc0YSYDfilYpEwxiwQr1q', '2025-10-22 21:19:54', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, 'uploads/profile_images/profile_18_1761148566.png'),
(20, 'esandu yapa', '2000-03-05', '0774267307', 'esanduepa0225@gmail.com', 'Deiyandarawatta, Panvila', 'RCG', 'Player', 'esandu12', '$2y$10$U1rmUEsrO85uogg0/U7HbeRYM.WSGgZNumnBy8o0ba535z3S/oqTm', '2025-10-23 10:08:20', 'active', 0, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(23, 'Esandu Epa', '1999-02-25', '+94774267307', 'esanduepa0225111@gmail.com', 'Deiyandarawatta, Panvila', 'Example', 'Coach', 'user23', '$2y$10$zI1ErUZVCQA065uLPSFEs.Dq4eBYv0gTNgzXaZHd7upXElzL9oL5y', '2026-02-10 15:40:17', 'active', 0, NULL, NULL, 0, NULL, 1, NULL, NULL);

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
  `NotSuitableFor` text DEFAULT NULL COMMENT 'Conditions or individuals for whom this workout is not recommended',
  `Benefits` text DEFAULT NULL COMMENT 'Key benefits of performing this workout',
  `CreatedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customized workout plans for players';

--
-- Dumping data for table `workoutplan`
--

INSERT INTO `workoutplan` (`PlanID`, `TrainerID`, `workoutname`, `frequency`, `Duration`, `VideoLink`, `Intensity`, `NotSuitableFor`, `Benefits`, `CreatedDate`) VALUES
(2, 10, 'cardio yuyiuj', 'Bi-weekly', 60, NULL, 'Moderate', NULL, NULL, '2025-10-21'),
(24, 10, 'Manual Test Workout', 'Daily', 60, NULL, 'Moderate', NULL, NULL, '2025-10-22'),
(25, 10, 'Manual Test Workout 20', 'Daily', 60, NULL, 'Moderate', NULL, NULL, '2025-10-22'),
(26, 10, 'aa22', 'Weekly', 20, NULL, 'High', 'good', 'good', '2025-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `workoutplan_player`
--

CREATE TABLE `workoutplan_player` (
  `PlanID` int(11) NOT NULL,
  `PlayerID` int(11) NOT NULL,
  `AssignedDate` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `coachingeffectiveness`
--
DROP TABLE IF EXISTS `coachingeffectiveness`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `coachingeffectiveness`  AS SELECT `cp`.`CoachID` AS `CoachID`, `u_coach`.`Name` AS `CoachName`, `cp`.`Specialization` AS `Specialization`, count(distinct `pca`.`PlayerID`) AS `PlayersAssigned`, count(`pu`.`UpdateID`) AS `PerformanceUpdatesGiven`, count(`cs`.`SessionLogID`) AS `SessionsLogged`, avg(`cs`.`SessionRating`) AS `AvgSessionRating`, avg(case when `pu`.`Status` = 'approved' then `pu`.`TechnicalRating` end) AS `AvgTechnicalRatingGiven`, count(case when `cs`.`AttendanceStatus` = 'present' then 1 end) AS `SessionsAttended`, count(case when `cs`.`AttendanceStatus` in ('absent','late') then 1 end) AS `SessionsMissed` FROM ((((`coachprofile` `cp` join `user` `u_coach` on(`cp`.`CoachID` = `u_coach`.`UserID`)) left join `playercoachassignment` `pca` on(`cp`.`CoachID` = `pca`.`CoachID` and `pca`.`Status` = 'active')) left join `performanceupdate` `pu` on(`cp`.`CoachID` = `pu`.`CoachID`)) left join `coachingsession` `cs` on(`cp`.`CoachID` = `cs`.`CoachID`)) WHERE `u_coach`.`Status` = 'active' GROUP BY `cp`.`CoachID`, `u_coach`.`Name`, `cp`.`Specialization` ;

-- --------------------------------------------------------

--
-- Structure for view `coachplayerpermissions`
--
DROP TABLE IF EXISTS `coachplayerpermissions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `coachplayerpermissions`  AS SELECT `cp`.`CoachID` AS `CoachID`, `u_coach`.`Name` AS `CoachName`, `cp`.`IsHeadCoach` AS `IsHeadCoach`, `u_coach`.`Role` AS `Role`, `pp`.`PlayerID` AS `PlayerID`, `u_player`.`Name` AS `PlayerName`, `pca`.`Status` AS `AssignmentStatus`, `pca`.`AssignmentType` AS `AssignmentType`, `pca`.`AssignedDate` AS `AssignedDate`, CASE WHEN `u_coach`.`Role` = 'Admin' THEN 'Full Access' WHEN `cp`.`IsHeadCoach` = 1 THEN 'Head Coach Access' WHEN `pca`.`Status` = 'active' THEN 'Assigned Player Access' ELSE 'No Access' END AS `PermissionLevel` FROM ((((`user` `u_coach` join `coachprofile` `cp` on(`u_coach`.`UserID` = `cp`.`CoachID`)) left join `playercoachassignment` `pca` on(`cp`.`CoachID` = `pca`.`CoachID`)) left join `playerprofile` `pp` on(`pca`.`PlayerID` = `pp`.`PlayerID`)) left join `user` `u_player` on(`pp`.`PlayerID` = `u_player`.`UserID`)) WHERE `u_coach`.`Status` = 'active' ;

-- --------------------------------------------------------

--
-- Structure for view `performanceupdatesummary`
--
DROP TABLE IF EXISTS `performanceupdatesummary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `performanceupdatesummary`  AS SELECT `pp`.`PlayerID` AS `PlayerID`, `u`.`Name` AS `PlayerName`, count(`pu`.`UpdateID`) AS `TotalUpdates`, count(case when `pu`.`Status` = 'approved' then 1 end) AS `ApprovedUpdates`, count(case when `pu`.`Status` = 'pending_approval' then 1 end) AS `PendingUpdates`, max(`pu`.`UpdateDate`) AS `LastUpdateDate`, avg(`pu`.`TechnicalRating`) AS `AvgTechnicalRating`, avg(`pu`.`FitnessRating`) AS `AvgFitnessRating`, avg(`pu`.`AttitudeRating`) AS `AvgAttitudeRating` FROM ((`playerprofile` `pp` join `user` `u` on(`pp`.`PlayerID` = `u`.`UserID`)) left join `performanceupdate` `pu` on(`pp`.`PlayerID` = `pu`.`PlayerID`)) GROUP BY `pp`.`PlayerID`, `u`.`Name` ;

-- --------------------------------------------------------

--
-- Structure for view `playerdevelopmenttracking`
--
DROP TABLE IF EXISTS `playerdevelopmenttracking`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `playerdevelopmenttracking`  AS SELECT `pp`.`PlayerID` AS `PlayerID`, `u_player`.`Name` AS `PlayerName`, year(curdate()) - year(`u_player`.`DateOfBirth`) AS `Age`, `pp`.`BattingStyle` AS `BattingStyle`, `pp`.`BowlingStyle` AS `BowlingStyle`, `pos`.`TotalRuns` AS `TotalRuns`, `pos`.`TotalWickets` AS `TotalWickets`, `pos`.`BattingAverage` AS `BattingAverage`, `pos`.`BowlingAverage` AS `BowlingAverage`, count(distinct `s`.`SessionID`) AS `SessionsAttended`, count(distinct `pu`.`UpdateID`) AS `PerformanceUpdatesReceived`, avg(`cs`.`SessionRating`) AS `AvgSessionRating`, max(`pu`.`UpdateDate`) AS `LastPerformanceUpdate`, `u_coach`.`Name` AS `CurrentCoach` FROM ((((((((`playerprofile` `pp` join `user` `u_player` on(`pp`.`PlayerID` = `u_player`.`UserID`)) join `playeroverallstats` `pos` on(`pp`.`PlayerID` = `pos`.`PlayerID`)) left join `sessionenrollment` `se` on(`pp`.`PlayerID` = `se`.`PlayerID` and `se`.`Status` = 'attended')) left join `session` `s` on(`se`.`SessionID` = `s`.`SessionID`)) left join `performanceupdate` `pu` on(`pp`.`PlayerID` = `pu`.`PlayerID`)) left join `coachingsession` `cs` on(`pp`.`PlayerID` = `cs`.`PlayerID`)) left join `playercoachassignment` `pca` on(`pp`.`PlayerID` = `pca`.`PlayerID` and `pca`.`Status` = 'active')) left join `user` `u_coach` on(`pca`.`CoachID` = `u_coach`.`UserID`)) WHERE `u_player`.`Status` = 'active' GROUP BY `pp`.`PlayerID`, `u_player`.`Name`, `pp`.`BattingStyle`, `pp`.`BowlingStyle`, `pos`.`TotalRuns`, `pos`.`TotalWickets`, `pos`.`BattingAverage`, `pos`.`BowlingAverage`, `u_coach`.`Name` ;

-- --------------------------------------------------------

--
-- Structure for view `v_active_players`
--
DROP TABLE IF EXISTS `v_active_players`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_players`  AS SELECT `u`.`UserID` AS `UserID`, `u`.`Name` AS `Name`, `u`.`Email` AS `Email`, `u`.`PhoneNumber` AS `PhoneNumber`, `u`.`DateOfBirth` AS `DateOfBirth`, `pp`.`BattingStyle` AS `BattingStyle`, `pp`.`BowlingStyle` AS `BowlingStyle`, `pp`.`JerseyNumber` AS `JerseyNumber`, `pos`.`MatchesPlayed` AS `MatchesPlayed`, `pos`.`TotalRuns` AS `TotalRuns`, `pos`.`TotalWickets` AS `TotalWickets`, `pos`.`BattingAverage` AS `BattingAverage` FROM ((`user` `u` join `playerprofile` `pp` on(`u`.`UserID` = `pp`.`PlayerID`)) join `playeroverallstats` `pos` on(`pp`.`PlayerID` = `pos`.`PlayerID`)) WHERE `u`.`Status` = 'active' ;

-- --------------------------------------------------------

--
-- Structure for view `v_facility_availability`
--
DROP TABLE IF EXISTS `v_facility_availability`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_facility_availability`  AS SELECT `f`.`FacilityID` AS `FacilityID`, `f`.`Name` AS `Name`, `f`.`Location` AS `Location`, `f`.`Capacity` AS `Capacity`, `f`.`HourlyRate` AS `HourlyRate`, `f`.`AvailabilityStatus` AS `AvailabilityStatus`, count(`fb`.`FacilityBookingID`) AS `TodayBookings` FROM (`facility` `f` left join `facilitybooking` `fb` on(`f`.`FacilityID` = `fb`.`FacilityID` and `fb`.`BookingDate` = curdate() and `fb`.`Status` = 'confirmed')) GROUP BY `f`.`FacilityID`, `f`.`Name`, `f`.`Location`, `f`.`Capacity`, `f`.`HourlyRate`, `f`.`AvailabilityStatus` ;

-- --------------------------------------------------------

--
-- Structure for view `v_upcoming_sessions`
--
DROP TABLE IF EXISTS `v_upcoming_sessions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_upcoming_sessions`  AS SELECT `s`.`SessionID` AS `SessionID`, `s`.`Name` AS `Name`, `s`.`SessionType` AS `SessionType`, `s`.`Date` AS `Date`, `s`.`StartTime` AS `StartTime`, `s`.`EndTime` AS `EndTime`, `s`.`Location` AS `Location`, `u`.`Name` AS `InstructorName`, count(`se`.`EnrollmentID`) AS `EnrolledCount`, `s`.`MaxParticipants` AS `MaxParticipants` FROM ((`session` `s` join `user` `u` on(`s`.`CoachOrTrainerID` = `u`.`UserID`)) left join `sessionenrollment` `se` on(`s`.`SessionID` = `se`.`SessionID` and `se`.`Status` = 'enrolled')) WHERE `s`.`Status` = 'active' AND `s`.`Date` >= curdate() GROUP BY `s`.`SessionID`, `s`.`Name`, `s`.`SessionType`, `s`.`Date`, `s`.`StartTime`, `s`.`EndTime`, `s`.`Location`, `u`.`Name`, `s`.`MaxParticipants` ;

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
  ADD PRIMARY KEY (`PlanID`);

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
  ADD KEY `idx_trainer_nutrition` (`TrainerID`);

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
  ADD KEY `idx_rating` (`Rating`);

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
  ADD KEY `PlanID` (`PlanID`);

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
-- Indexes for table `SessionAttendance`
--
ALTER TABLE `SessionAttendance`
  ADD PRIMARY KEY (`AttendanceID`),
  ADD UNIQUE KEY `unique_enrollment` (`EnrollmentID`);

--
-- Indexes for table `sessionenrollment`
--
ALTER TABLE `sessionenrollment`
  ADD PRIMARY KEY (`EnrollmentID`),
  ADD UNIQUE KEY `unique_enrollment` (`SessionID`,`PlayerID`),
  ADD KEY `idx_player_enrollment` (`PlayerID`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `shopemployeeprofile`
--
ALTER TABLE `shopemployeeprofile`
  ADD PRIMARY KEY (`ShopEmployeeID`);

--
-- Indexes for table `subscriptionpayment`
--
ALTER TABLE `subscriptionpayment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `SubscriptionID` (`SubscriptionID`),
  ADD KEY `ProcessedBy` (`ProcessedBy`);

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
  ADD PRIMARY KEY (`TournamentID`,`PlayerID`),
  ADD KEY `SelectedBy` (`SelectedBy`),
  ADD KEY `idx_player_tournament` (`PlayerID`),
  ADD KEY `idx_role` (`RoleInTeam`);

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
  ADD KEY `idx_trainer_workout` (`TrainerID`);

--
-- Indexes for table `workoutplan_player`
--
ALTER TABLE `workoutplan_player`
  ADD PRIMARY KEY (`PlanID`,`PlayerID`),
  ADD KEY `PlayerID` (`PlayerID`);

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
  MODIFY `ActivityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `coachappointment`
--
ALTER TABLE `coachappointment`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `MatchID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emaillog`
--
ALTER TABLE `emaillog`
  MODIFY `EmailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `livenotification`
--
ALTER TABLE `livenotification`
  MODIFY `LiveNotificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membershipplan`
--
ALTER TABLE `membershipplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `nutritionplan`
--
ALTER TABLE `nutritionplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `PerformanceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playermedicalrecord`
--
ALTER TABLE `playermedicalrecord`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `playeroverallstats`
--
ALTER TABLE `playeroverallstats`
  MODIFY `StatsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `playersubscription`
--
ALTER TABLE `playersubscription`
  MODIFY `SubscriptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `productcart`
--
ALTER TABLE `productcart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productorder`
--
ALTER TABLE `productorder`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `SessionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `SessionAttendance`
--
ALTER TABLE `SessionAttendance`
  MODIFY `AttendanceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessionenrollment`
--
ALTER TABLE `sessionenrollment`
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `subscriptionpayment`
--
ALTER TABLE `subscriptionpayment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplementplan`
--
ALTER TABLE `supplementplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `TournamentID` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `workoutplan`
--
ALTER TABLE `workoutplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
-- Constraints for table `nutritionplan`
--
ALTER TABLE `nutritionplan`
  ADD CONSTRAINT `nutritionplan_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `SessionAttendance`
--
ALTER TABLE `SessionAttendance`
  ADD CONSTRAINT `sessionattendance_ibfk_1` FOREIGN KEY (`EnrollmentID`) REFERENCES `sessionenrollment` (`EnrollmentID`) ON DELETE CASCADE;

--
-- Constraints for table `sessionenrollment`
--
ALTER TABLE `sessionenrollment`
  ADD CONSTRAINT `sessionenrollment_ibfk_1` FOREIGN KEY (`SessionID`) REFERENCES `session` (`SessionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sessionenrollment_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shopemployeeprofile`
--
ALTER TABLE `shopemployeeprofile`
  ADD CONSTRAINT `shopemployeeprofile_ibfk_1` FOREIGN KEY (`ShopEmployeeID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `tournamentplayer_ibfk_1` FOREIGN KEY (`TournamentID`) REFERENCES `tournament` (`TournamentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tournamentplayer_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tournamentplayer_ibfk_3` FOREIGN KEY (`SelectedBy`) REFERENCES `coachprofile` (`CoachID`) ON DELETE SET NULL ON UPDATE CASCADE;

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
  ADD CONSTRAINT `workoutplan_ibfk_1` FOREIGN KEY (`TrainerID`) REFERENCES `trainerprofile` (`TrainerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workoutplan_player`
--
ALTER TABLE `workoutplan_player`
  ADD CONSTRAINT `workoutplan_player_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `workoutplan` (`PlanID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workoutplan_player_ibfk_2` FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile` (`PlayerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
