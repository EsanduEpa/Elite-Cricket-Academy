-- ===================================================================
-- SESSION MANAGEMENT SYSTEM - USING EXISTING SCHEMA
-- ===================================================================
-- Description: The Session table already exists in cricket_academy_schema.sql
-- This file adds additional supporting tables for enhanced functionality
-- Created: 2025-10-21
-- Author: Elite Cricket Academy Development Team
-- ===================================================================

USE cricket_academy;

-- Note: The main Session table already exists in the schema with these columns:
-- SessionID, SessionType ('Coaching', 'Physical Training'), SessionMode ('Group', 'Private'),
-- CoachOrTrainerID, Name, Date, StartTime, EndTime, Location, Status, MaxParticipants,
-- PricePerSession, IsRecurring

-- Note: SessionEnrollment table also exists with:
-- EnrollmentID, SessionID, PlayerID, EnrollmentDate, Status

-- ===================================================================
-- ADDITIONAL TABLES FOR SESSION MANAGEMENT
-- ===================================================================

-- Extended session details for coach-specific features
CREATE TABLE IF NOT EXISTS SessionDetails (
    SessionID INT PRIMARY KEY,
    FacilityType VARCHAR(50) COMMENT 'Net, Indoor, Ground, Gym',
    FacilityNumber INT COMMENT 'Facility identifier',
    RecurrencePattern VARCHAR(50) DEFAULT 'None' COMMENT 'None, Daily, Weekly, Monthly',
    RecurrenceEnd DATE COMMENT 'End date for recurring sessions',
    CreatedBy INT NOT NULL COMMENT 'Coach who created the session',
    CancelReason TEXT COMMENT 'Reason for cancellation if status is cancelled',
    
    FOREIGN KEY (SessionID) REFERENCES Session(SessionID) ON DELETE CASCADE,
    FOREIGN KEY (CreatedBy) REFERENCES User(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Extended details for training sessions';

-- Session attendance tracking (enhanced version of existing enrollment)
CREATE TABLE IF NOT EXISTS SessionAttendance (
    AttendanceID INT PRIMARY KEY AUTO_INCREMENT,
    EnrollmentID INT NOT NULL COMMENT 'Links to SessionEnrollment',
    AttendanceStatus ENUM('present', 'absent', 'late', 'excused') NOT NULL,
    AttendanceNotes TEXT COMMENT 'Coach notes about attendance',
    MarkedBy INT NOT NULL COMMENT 'Coach who marked attendance',
    MarkedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (EnrollmentID) REFERENCES SessionEnrollment(EnrollmentID) ON DELETE CASCADE,
    FOREIGN KEY (MarkedBy) REFERENCES User(UserID) ON DELETE CASCADE,
    
    UNIQUE KEY unique_attendance (EnrollmentID),
    INDEX idx_status (AttendanceStatus),
    INDEX idx_marked_at (MarkedAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Detailed attendance tracking for sessions';

-- ===================================================================
-- SAMPLE DATA FOR TESTING
-- ===================================================================

-- Insert some sample coaching sessions (adjust coach IDs as needed)
INSERT INTO Session (SessionType, SessionMode, CoachOrTrainerID, Name, Date, StartTime, EndTime, Location, Status, MaxParticipants, IsRecurring) VALUES
('Coaching', 'Group', 1, 'Advanced Batting Techniques', '2025-10-25', '09:00:00', '11:00:00', 'Practice Net 1', 'active', 8, TRUE),
('Coaching', 'Group', 1, 'Beginner Batting Fundamentals', '2025-10-25', '14:00:00', '16:00:00', 'Practice Net 2', 'active', 10, TRUE),
('Physical Training', 'Group', 2, 'Strength and Conditioning', '2025-10-26', '07:00:00', '09:00:00', 'Gym', 'active', 12, TRUE),
('Coaching', 'Group', 1, 'Match Strategy Workshop', '2025-10-27', '10:00:00', '12:00:00', 'Indoor Training Hall', 'active', 15, FALSE),
('Coaching', 'Private', 1, 'One-on-One Batting Session', '2025-10-28', '15:00:00', '16:00:00', 'Practice Net 1', 'active', 1, FALSE);

-- Add session details for the coaching sessions
INSERT INTO SessionDetails (SessionID, FacilityType, FacilityNumber, RecurrencePattern, CreatedBy) VALUES
(1, 'Net', 1, 'Weekly', 1),
(2, 'Net', 2, 'Weekly', 1),
(3, 'Gym', 1, 'Weekly', 2),
(4, 'Indoor', 1, 'None', 1),
(5, 'Net', 1, 'None', 1);

-- ===================================================================
-- VERIFICATION QUERIES
-- ===================================================================

SELECT 'Session management tables setup complete!' as Status;

-- Show created tables
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'cricket_academy'
AND TABLE_NAME IN ('Session', 'SessionEnrollment', 'SessionDetails', 'SessionAttendance')
ORDER BY TABLE_NAME;

-- ===================================================================
-- SAMPLE DATA FOR TESTING
-- ===================================================================

-- Insert sample sessions (assuming CoachID 1 and 2 exist)
INSERT INTO Session (CoachID, SessionType, Title, Description, FacilityType, FacilityNumber, SessionDate, StartTime, EndTime, MaxParticipants, Status) VALUES
(1, 'Batting', 'Advanced Batting Techniques', 'Focus on power hitting and timing', 'Net', 1, '2025-01-22', '09:00:00', '11:00:00', 8, 'scheduled'),
(1, 'Batting', 'Beginner Batting Fundamentals', 'Basic stance, grip, and straight drive', 'Net', 2, '2025-01-22', '14:00:00', '16:00:00', 10, 'scheduled'),
(1, 'Strategy', 'Match Situation Analysis', 'Understanding game situations and decision making', 'Indoor', 1, '2025-01-23', '10:00:00', '12:00:00', 15, 'scheduled'),
(2, 'Bowling', 'Fast Bowling Workshop', 'Pace, line, and length control', 'Ground', 1, '2025-01-24', '08:00:00', '10:00:00', 6, 'scheduled'),
(2, 'Bowling', 'Spin Bowling Masterclass', 'Variations and flight control', 'Net', 3, '2025-01-24', '15:00:00', '17:00:00', 8, 'scheduled'),
(1, 'Fielding', 'Fielding Drills', 'Catching, throwing, and ground fielding', 'Ground', 2, '2025-01-25', '09:00:00', '11:00:00', 12, 'scheduled'),
(2, 'Fitness', 'Strength and Conditioning', 'Build power and endurance', 'Gym', 1, '2025-01-26', '07:00:00', '09:00:00', 10, 'scheduled'),
(1, 'Batting', 'Power Hitting Session', 'Six hitting techniques', 'Ground', 1, '2025-01-20', '09:00:00', '11:00:00', 8, 'completed'),
(2, 'Bowling', 'Yorker Practice', 'Death bowling skills', 'Net', 1, '2025-01-19', '14:00:00', '16:00:00', 6, 'completed');

-- Insert sample participants (assuming PlayerIDs 1-5 exist)
INSERT INTO SessionParticipants (SessionID, PlayerID) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),
(2, 2), (2, 3), (2, 5),
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5),
(4, 1), (4, 3), (4, 5),
(5, 2), (5, 4),
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5),
(7, 1), (7, 2), (7, 3),
(8, 1), (8, 2), (8, 3), (8, 4),
(9, 1), (9, 3), (9, 5);

-- Insert sample attendance for completed sessions
INSERT INTO SessionAttendance (SessionID, PlayerID, Status, Notes) VALUES
(8, 1, 'present', 'Excellent performance'),
(8, 2, 'present', 'Good improvement'),
(8, 3, 'late', 'Arrived 15 minutes late'),
(8, 4, 'present', 'Very focused'),
(9, 1, 'present', 'Great yorker execution'),
(9, 3, 'absent', 'Medical appointment'),
(9, 5, 'present', 'Needs more practice');

-- ===================================================================
-- USEFUL QUERIES FOR SESSION MANAGEMENT
-- ===================================================================

-- View all upcoming sessions with participant count
/*
SELECT 
    s.SessionID,
    s.Title,
    s.SessionType,
    s.SessionDate,
    s.StartTime,
    s.EndTime,
    s.FacilityType,
    s.FacilityNumber,
    s.Status,
    CONCAT(u.FirstName, ' ', u.LastName) AS CoachName,
    COUNT(sp.PlayerID) AS ParticipantCount,
    s.MaxParticipants
FROM Session s
LEFT JOIN Coach c ON s.CoachID = c.CoachID
LEFT JOIN User u ON c.UserID = u.UserID
LEFT JOIN SessionParticipants sp ON s.SessionID = sp.SessionID
WHERE s.SessionDate >= CURDATE()
AND s.Status = 'scheduled'
GROUP BY s.SessionID
ORDER BY s.SessionDate, s.StartTime;
*/

-- View coach's weekly schedule
/*
SELECT 
    s.SessionID,
    s.Title,
    s.SessionType,
    DATE_FORMAT(s.SessionDate, '%W, %M %d, %Y') AS FormattedDate,
    TIME_FORMAT(s.StartTime, '%h:%i %p') AS StartTime,
    TIME_FORMAT(s.EndTime, '%h:%i %p') AS EndTime,
    CONCAT(s.FacilityType, ' ', s.FacilityNumber) AS Facility,
    s.Status,
    COUNT(sp.PlayerID) AS Participants
FROM Session s
LEFT JOIN SessionParticipants sp ON s.SessionID = sp.SessionID
WHERE s.CoachID = 1
AND YEARWEEK(s.SessionDate, 1) = YEARWEEK(CURDATE(), 1)
GROUP BY s.SessionID
ORDER BY s.SessionDate, s.StartTime;
*/

-- View attendance report for a session
/*
SELECT 
    s.Title,
    s.SessionDate,
    CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
    sa.Status,
    sa.Notes,
    sa.MarkedAt
FROM SessionAttendance sa
JOIN Session s ON sa.SessionID = s.SessionID
JOIN Player p ON sa.PlayerID = p.PlayerID
JOIN User u ON p.UserID = u.UserID
WHERE sa.SessionID = 8
ORDER BY sa.Status, u.FirstName;
*/

-- Calculate coach's average attendance rate
/*
SELECT 
    CONCAT(u.FirstName, ' ', u.LastName) AS CoachName,
    COUNT(DISTINCT s.SessionID) AS TotalSessions,
    COUNT(DISTINCT CASE WHEN s.Status = 'completed' THEN s.SessionID END) AS CompletedSessions,
    ROUND(AVG(
        CASE WHEN sa.SessionID IS NOT NULL THEN
            (SELECT COUNT(*) FROM SessionAttendance WHERE SessionID = s.SessionID AND Status = 'present') * 100.0 /
            (SELECT COUNT(*) FROM SessionParticipants WHERE SessionID = s.SessionID)
        END
    ), 1) AS AvgAttendanceRate
FROM Coach c
JOIN User u ON c.UserID = u.UserID
LEFT JOIN Session s ON c.CoachID = s.CoachID
LEFT JOIN SessionAttendance sa ON s.SessionID = sa.SessionID
WHERE c.CoachID = 1
GROUP BY c.CoachID;
*/

-- Find session conflicts for a coach
/*
SELECT 
    s1.SessionID,
    s1.Title,
    s1.SessionDate,
    s1.StartTime,
    s1.EndTime,
    'CONFLICT WITH' AS ConflictIndicator,
    s2.SessionID AS ConflictingSessionID,
    s2.Title AS ConflictingTitle,
    s2.StartTime AS ConflictingStart,
    s2.EndTime AS ConflictingEnd
FROM Session s1
JOIN Session s2 ON s1.CoachID = s2.CoachID 
    AND s1.SessionDate = s2.SessionDate
    AND s1.SessionID != s2.SessionID
    AND s1.StartTime < s2.EndTime 
    AND s1.EndTime > s2.StartTime
WHERE s1.CoachID = 1
AND s1.Status != 'cancelled'
AND s2.Status != 'cancelled'
ORDER BY s1.SessionDate, s1.StartTime;
*/

-- ===================================================================
-- VERIFICATION QUERIES
-- ===================================================================

-- Check if tables were created successfully
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME,
    TABLE_COMMENT
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'cricket_academy'
AND TABLE_NAME IN ('Session', 'SessionParticipants', 'SessionAttendance', 'SessionNotification')
ORDER BY TABLE_NAME;

-- Display table structures
SHOW CREATE TABLE Session;
SHOW CREATE TABLE SessionParticipants;
SHOW CREATE TABLE SessionAttendance;
SHOW CREATE TABLE SessionNotification;

-- Count records in each table
SELECT 'Session' AS TableName, COUNT(*) AS RecordCount FROM Session
UNION ALL
SELECT 'SessionParticipants', COUNT(*) FROM SessionParticipants
UNION ALL
SELECT 'SessionAttendance', COUNT(*) FROM SessionAttendance
UNION ALL
SELECT 'SessionNotification', COUNT(*) FROM SessionNotification;

-- ===================================================================
-- MAINTENANCE QUERIES
-- ===================================================================

-- Delete old cancelled sessions (older than 90 days)
/*
DELETE FROM Session 
WHERE Status = 'cancelled' 
AND SessionDate < DATE_SUB(CURDATE(), INTERVAL 90 DAY);
*/

-- Archive completed sessions (move to archive table if needed)
/*
CREATE TABLE IF NOT EXISTS Session_Archive LIKE Session;
INSERT INTO Session_Archive SELECT * FROM Session WHERE Status = 'completed' AND SessionDate < DATE_SUB(CURDATE(), INTERVAL 180 DAY);
DELETE FROM Session WHERE Status = 'completed' AND SessionDate < DATE_SUB(CURDATE(), INTERVAL 180 DAY);
*/

-- ===================================================================
-- END OF SCHEMA
-- ===================================================================
