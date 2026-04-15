-- 2026-04-15 DB Changes
-- Purpose: Store per-occurence attendance for group sessions shown on the coach dashboard.
-- Checked players are marked present; unchecked players are stored as absent.
-- Coach player list actions were moved into the table header to match the admin table pattern.
-- No schema change was required for that UI update.

CREATE TABLE IF NOT EXISTS `slot_occurrence_attendance` (
  `AttendanceID` INT(11) NOT NULL AUTO_INCREMENT,
  `OccurrenceID` INT(11) NOT NULL,
  `PlayerID` INT(11) NOT NULL,
  `AttendanceStatus` ENUM('present','absent') NOT NULL DEFAULT 'absent',
  `MarkedBy` INT(11) DEFAULT NULL,
  `MarkedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AttendanceID`),
  UNIQUE KEY `uq_slot_occurrence_attendance` (`OccurrenceID`, `PlayerID`),
  KEY `idx_slot_occurrence_attendance_occurrence` (`OccurrenceID`),
  KEY `idx_slot_occurrence_attendance_player` (`PlayerID`),
  KEY `idx_slot_occurrence_attendance_marked_by` (`MarkedBy`),
  CONSTRAINT `fk_slot_occurrence_attendance_occurrence`
    FOREIGN KEY (`OccurrenceID`) REFERENCES `slot_occurrence` (`OccurrenceID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_slot_occurrence_attendance_player`
    FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_slot_occurrence_attendance_marked_by`
    FOREIGN KEY (`MarkedBy`) REFERENCES `user` (`UserID`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- tournament_result
-- Final tournament result summary with supported position values.
-- -----------------------------------------------------------------------------
ALTER TABLE crimatch
  CHANGE COLUMN `name` `Name` VARCHAR(100) NOT NULL COMMENT 'e.g. Semi-Final 1 or League Match 5',
  MODIFY COLUMN `Result` ENUM('win', 'loss', 'tie', 'draw', 'no-result', 'abandoned', 'pending') DEFAULT 'pending',
  ADD COLUMN `MarginValue` INT UNSIGNED DEFAULT NULL COMMENT 'The number part: e.g. 5' AFTER `Result`,
  ADD COLUMN `MarginType` ENUM('runs', 'wickets', 'super over', 'DLS', 'boundaries', 'forfeit') DEFAULT NULL AFTER `MarginValue`,
  ADD COLUMN `OurRuns` SMALLINT UNSIGNED DEFAULT NULL AFTER `MarginType`,
  ADD COLUMN `OurWickets` TINYINT UNSIGNED DEFAULT NULL AFTER `OurRuns`,
  ADD COLUMN `OpponentRuns` SMALLINT UNSIGNED DEFAULT NULL AFTER `OurWickets`,
  ADD COLUMN `OpponentWickets` TINYINT UNSIGNED DEFAULT NULL AFTER `OpponentRuns`,
  ADD COLUMN `IsDLS` TINYINT(1) DEFAULT 0 COMMENT '1 if Duckworth-Lewis was applied' AFTER `OpponentWickets`,
  ADD COLUMN `SummaryNotes` TEXT DEFAULT NULL AFTER `IsDLS`,
  ADD COLUMN `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `SummaryNotes`,
  ADD COLUMN `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `CreatedAt`;

  ALTER TABLE crimatch
  DROP COLUMN `OurScore`,
  DROP COLUMN `OpponentScore`;



CREATE TABLE IF NOT EXISTS tournament_result (
    ResultID            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TournamentID        INT(11) NOT NULL,
    
    -- Overall Achievement
    Position            ENUM('champions', '1st runners up', '2nd runners up', '3rd runners up', 'super 8', 'super 16', 'group stage') NOT NULL,
    
    -- Performance Summary (Aggregates)
    TotalMatchesPlayed  TINYINT UNSIGNED DEFAULT 0,
    TotalWins           TINYINT UNSIGNED DEFAULT 0,
    TotalLosses          TINYINT UNSIGNED DEFAULT 0,
    
    -- Awards (Linking to your User table)
    ManOfTournament     INT(11) DEFAULT NULL COMMENT 'FK → user.UserID',
    BestBatsman         INT(11) DEFAULT NULL COMMENT 'FK → user.UserID',
    BestBowler          INT(11) DEFAULT NULL COMMENT 'FK → user.UserID',
    
    -- The "Final Chapter"
    SummaryNotes        TEXT DEFAULT NULL COMMENT 'E.g. "Historical run to the final, lost in a thriller."',
    
    EnteredBy           INT(11) NOT NULL,
    CreatedAt           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_result_tournament (TournamentID),
    CONSTRAINT fk_tr_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_tr_man        FOREIGN KEY (ManOfTournament) REFERENCES user (UserID),
    CONSTRAINT fk_tr_batsman    FOREIGN KEY (BestBatsman)     REFERENCES user (UserID),
    CONSTRAINT fk_tr_bowler     FOREIGN KEY (BestBowler)      REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================================
-- Full age-group/status matrix
-- Purpose:
--   Ensure each age group has tournaments in every status category.
--   Match rows are only added for the completed tournaments.
-- ================================================================

-- Under 11
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Created Cup', 'Under 11', 'T20', 'Under 11 tournament in created state.', '2026-06-15', '2026-06-10', 14, 'Main Ground', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Registration Open Cup', 'Under 11', 'T20', 'Under 11 tournament with registration open.', '2026-06-16', '2026-06-11', 14, 'Main Ground', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Registration Closed Cup', 'Under 11', 'T20', 'Under 11 tournament with registration closed.', '2026-06-17', '2026-06-12', 14, 'Main Ground', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Squad Announced Cup', 'Under 11', 'T20', 'Under 11 tournament with team announced.', '2026-06-18', '2026-06-13', 14, 'Main Ground', 5000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Ongoing Cup', 'Under 11', 'T20', 'Under 11 tournament currently ongoing.', '2026-06-19', '2026-06-14', 14, 'Main Ground', 5000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Completed Cup', 'Under 11', 'T20', 'Under 11 completed tournament.', '2026-06-20', '2026-06-15', 14, 'Main Ground', 10000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-06-19', 'Main Ground', 'North Warriors', 'win', 7, 'runs', 182, 5, 175, 9, 0, 'Completed Under 11 tournament league match.'
FROM tournament t
WHERE t.Name = 'Under 11 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-06-19' AND m.Venue = 'Main Ground'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-06-20', 'Main Ground', 'North Warriors', 'win', 5, 'wickets', 176, 5, 175, 9, 0, 'Completed Under 11 tournament final.'
FROM tournament t
WHERE t.Name = 'Under 11 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-06-20' AND m.Venue = 'Main Ground'
  )
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 11 Cancelled Cup', 'Under 11', 'T10', 'Under 11 cancelled tournament.', '2026-03-21', '2026-04-21', 14, 'Main Ground', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 11 Cancelled Cup')
LIMIT 1;

-- Under 13
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Created Cup', 'Under 13', 'T20', 'Under 13 tournament in created state.', '2026-06-22', '2026-06-17', 16, 'North Ground', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Registration Open Cup', 'Under 13', 'T20', 'Under 13 tournament with registration open.', '2026-06-23', '2026-06-18', 16, 'North Ground', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Registration Closed Cup', 'Under 13', 'T20', 'Under 13 tournament with registration closed.', '2026-06-24', '2026-06-19', 16, 'North Ground', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Squad Announced Cup', 'Under 13', 'T20', 'Under 13 tournament with team announced.', '2026-06-25', '2026-06-20', 16, 'North Ground', 8000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Ongoing Cup', 'Under 13', 'T20', 'Under 13 tournament currently ongoing.', '2026-06-26', '2026-06-21', 16, 'North Ground', 8000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Completed Cup', 'Under 13', 'T20', 'Under 13 completed tournament.', '2026-06-27', '2026-06-22', 16, 'North Ground', 12000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-06-26', 'North Ground', 'District XI', 'loss', 4, 'wickets', 154, 8, 158, 6, 0, 'Completed Under 13 tournament league match.'
FROM tournament t
WHERE t.Name = 'Under 13 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-06-26' AND m.Venue = 'North Ground'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-06-27', 'North Ground', 'District XI', 'loss', 4, 'wickets', 154, 8, 158, 6, 0, 'Completed Under 13 tournament final.'
FROM tournament t
WHERE t.Name = 'Under 13 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-06-27' AND m.Venue = 'North Ground'
  )
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 13 Cancelled Cup', 'Under 13', 'T20', 'Under 13 cancelled tournament.', '2026-06-28', '2026-06-23', 16, 'North Ground', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 13 Cancelled Cup')
LIMIT 1;

-- Under 15
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Created Cup', 'Under 15', 'T20', 'Under 15 tournament in created state.', '2026-07-01', '2026-06-26', 18, 'City Stadium', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Registration Open Cup', 'Under 15', 'T20', 'Under 15 tournament with registration open.', '2026-07-02', '2026-06-27', 18, 'City Stadium', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Registration Closed Cup', 'Under 15', 'T20', 'Under 15 tournament with registration closed.', '2026-07-03', '2026-06-28', 18, 'City Stadium', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Squad Announced Cup', 'Under 15', 'T20', 'Under 15 tournament with team announced.', '2026-07-04', '2026-06-29', 18, 'City Stadium', 10000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Ongoing Cup', 'Under 15', 'T20', 'Under 15 tournament currently ongoing.', '2026-07-05', '2026-06-30', 18, 'City Stadium', 10000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Completed Cup', 'Under 15', 'T20', 'Under 15 completed tournament.', '2026-07-06', '2026-07-01', 18, 'City Stadium', 15000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-07-05', 'City Stadium', 'Metro Titans', 'draw', 0, NULL, 171, 7, 171, 9, 0, 'Completed Under 15 tournament league match.'
FROM tournament t
WHERE t.Name = 'Under 15 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-07-05' AND m.Venue = 'City Stadium'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-07-06', 'City Stadium', 'Metro Titans', 'draw', 0, NULL, 171, 7, 171, 9, 0, 'Completed Under 15 tournament final.'
FROM tournament t
WHERE t.Name = 'Under 15 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-07-06' AND m.Venue = 'City Stadium'
  )
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 15 Cancelled Cup', 'Under 15', 'T20', 'Under 15 cancelled tournament.', '2026-07-07', '2026-07-02', 18, 'City Stadium', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 15 Cancelled Cup')
LIMIT 1;

-- Under 17
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Created Cup', 'Under 17', 'ODI', 'Under 17 tournament in created state.', '2026-07-08', '2026-07-03', 18, 'Academy Oval', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Registration Open Cup', 'Under 17', 'ODI', 'Under 17 tournament with registration open.', '2026-07-09', '2026-07-04', 18, 'Academy Oval', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Registration Closed Cup', 'Under 17', 'ODI', 'Under 17 tournament with registration closed.', '2026-07-10', '2026-07-05', 18, 'Academy Oval', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Squad Announced Cup', 'Under 17', 'ODI', 'Under 17 tournament with team announced.', '2026-07-11', '2026-07-06', 18, 'Academy Oval', 12000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Ongoing Cup', 'Under 17', 'ODI', 'Under 17 tournament currently ongoing.', '2026-07-12', '2026-07-07', 18, 'Academy Oval', 12000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Completed Cup', 'Under 17', 'ODI', 'Under 17 completed tournament.', '2026-07-13', '2026-07-08', 18, 'Academy Oval', 20000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-07-12', 'Academy Oval', 'County Rockets', 'win', 11, 'runs', 201, 6, 190, 10, 0, 'Completed Under 17 tournament league match.'
FROM tournament t
WHERE t.Name = 'Under 17 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-07-12' AND m.Venue = 'Academy Oval'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-07-13', 'Academy Oval', 'County Rockets', 'win', 11, 'runs', 201, 6, 190, 10, 0, 'Completed Under 17 tournament final.'
FROM tournament t
WHERE t.Name = 'Under 17 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-07-13' AND m.Venue = 'Academy Oval'
  )
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 17 Cancelled Cup', 'Under 17', 'ODI', 'Under 17 cancelled tournament.', '2026-07-14', '2026-07-09', 18, 'Academy Oval', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 17 Cancelled Cup')
LIMIT 1;

-- Under 19
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Created Cup', 'Under 19', 'T20', 'Under 19 tournament in created state.', '2026-07-15', '2026-07-10', 20, 'National Ground', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Registration Open Cup', 'Under 19', 'T20', 'Under 19 tournament with registration open.', '2026-07-16', '2026-07-11', 20, 'National Ground', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Registration Closed Cup', 'Under 19', 'T20', 'Under 19 tournament with registration closed.', '2026-07-17', '2026-07-12', 20, 'National Ground', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Squad Announced Cup', 'Under 19', 'T20', 'Under 19 tournament with team announced.', '2026-07-18', '2026-07-13', 20, 'National Ground', 15000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Ongoing Cup', 'Under 19', 'T20', 'Under 19 tournament currently ongoing.', '2026-07-19', '2026-07-14', 20, 'National Ground', 15000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Completed Cup', 'Under 19', 'T20', 'Under 19 completed tournament.', '2026-07-20', '2026-07-15', 20, 'National Ground', 25000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-07-19', 'National Ground', 'Elite Select XI', 'no-result', 0, NULL, 167, 8, 167, 6, 0, 'Completed Under 19 tournament league match.'
FROM tournament t
WHERE t.Name = 'Under 19 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-07-19' AND m.Venue = 'National Ground'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-07-20', 'National Ground', 'Elite Select XI', 'draw', 0, NULL, 167, 8, 167, 6, 0, 'Completed Under 19 tournament final.'
FROM tournament t
WHERE t.Name = 'Under 19 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-07-20' AND m.Venue = 'National Ground'
  )
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 19 Cancelled Cup', 'Under 19', 'T20', 'Under 19 cancelled tournament.', '2026-07-21', '2026-07-16', 20, 'National Ground', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 19 Cancelled Cup')
LIMIT 1;

-- Open
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Created Cup', 'Open', 'T20', 'Open age-group tournament in created state.', '2026-07-22', '2026-07-17', 22, 'Elite Ground 1', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Registration Open Cup', 'Open', 'T20', 'Open age-group tournament with registration open.', '2026-07-23', '2026-07-18', 22, 'Elite Ground 1', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Registration Closed Cup', 'Open', 'T20', 'Open age-group tournament with registration closed.', '2026-07-24', '2026-07-19', 22, 'Elite Ground 1', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Squad Announced Cup', 'Open', 'T20', 'Open age-group tournament with team announced.', '2026-07-25', '2026-07-20', 22, 'Elite Ground 1', 10000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Ongoing Cup', 'Open', 'T20', 'Open age-group tournament currently ongoing.', '2026-07-26', '2026-07-21', 22, 'Elite Ground 1', 10000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Completed Cup', 'Open', 'T20', 'Open age-group completed tournament.', '2026-07-27', '2026-07-22', 22, 'Elite Ground 1', 20000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-07-26', 'Elite Ground 1', 'Weather Watchers', 'draw', 0, NULL, 189, 7, 189, 8, 0, 'Completed Open tournament league match.'
FROM tournament t
WHERE t.Name = 'Open Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-07-26' AND m.Venue = 'Elite Ground 1'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-07-27', 'Elite Ground 1', 'Weather Watchers', 'draw', 0, NULL, 189, 7, 189, 8, 0, 'Completed Open tournament final.'
FROM tournament t
WHERE t.Name = 'Open Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-07-27' AND m.Venue = 'Elite Ground 1'
  )
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Open Cancelled Cup', 'Open', 'T20', 'Open age-group cancelled tournament.', '2026-07-28', '2026-07-23', 22, 'Elite Ground 1', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Open Cancelled Cup')
LIMIT 1;

-- Under 21
INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Created Cup', 'Under 21', 'T20', 'Under 21 tournament in created state.', '2026-07-29', '2026-07-24', 22, 'Elite Ground 2', 0.00, u.UserID, 'created', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Created Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Registration Open Cup', 'Under 21', 'T20', 'Under 21 tournament with registration open.', '2026-07-30', '2026-07-25', 22, 'Elite Ground 2', 0.00, u.UserID, 'registration_open', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Registration Open Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Registration Closed Cup', 'Under 21', 'T20', 'Under 21 tournament with registration closed.', '2026-07-31', '2026-07-26', 22, 'Elite Ground 2', 0.00, u.UserID, 'registration_closed', 0, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Registration Closed Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Squad Announced Cup', 'Under 21', 'T20', 'Under 21 tournament with team announced.', '2026-08-01', '2026-07-27', 22, 'Elite Ground 2', 12000.00, u.UserID, 'team_announced', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Squad Announced Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Ongoing Cup', 'Under 21', 'T20', 'Under 21 tournament currently ongoing.', '2026-08-02', '2026-07-28', 22, 'Elite Ground 2', 12000.00, u.UserID, 'ongoing', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Ongoing Cup')
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Completed Cup', 'Under 21', 'T20', 'Under 21 completed tournament.', '2026-08-03', '2026-07-29', 22, 'Elite Ground 2', 25000.00, u.UserID, 'completed', 1, NULL
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Completed Cup')
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'League Match 1', '2026-08-02', 'Elite Ground 2', 'Young Titans', 'win', 8, 'runs', 194, 6, 186, 9, 0, 'Completed Under 21 tournament league match.'
FROM tournament t
WHERE t.Name = 'Under 21 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'League Match 1' AND m.Date = '2026-08-02' AND m.Venue = 'Elite Ground 2'
  )
LIMIT 1;

INSERT INTO crimatch (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT t.TournamentID, 'Final', '2026-08-03', 'Elite Ground 2', 'Young Titans', 'win', 3, 'wickets', 187, 7, 186, 9, 0, 'Completed Under 21 tournament final.'
FROM tournament t
WHERE t.Name = 'Under 21 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (
    SELECT 1 FROM crimatch m
    WHERE m.TournamentID = t.TournamentID AND m.Name = 'Final' AND m.Date = '2026-08-03' AND m.Venue = 'Elite Ground 2'
  )
LIMIT 1;

-- Cleanup malformed match row from the Open completed tournament import.
DELETE FROM crimatch
WHERE TournamentID = (
    SELECT TournamentID
    FROM tournament
    WHERE Name = 'Open Completed Cup'
    LIMIT 1
)
  AND (Name IS NULL OR Name = '')
  AND Date = '0000-00-00';

-- Completed tournament results
INSERT INTO tournament_result (TournamentID, Position, TotalMatchesPlayed, TotalWins, TotalLosses, ManOfTournament, BestBatsman, BestBowler, SummaryNotes, EnteredBy)
SELECT t.TournamentID, '1st runners up', 2, 0, 0, NULL, NULL, NULL, 'Open Completed Cup result summary.', u.UserID
FROM tournament t
JOIN user u ON u.Role = 'Admin' AND u.Status = 'active'
WHERE t.Name = 'Open Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (SELECT 1 FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID)
LIMIT 1;

INSERT INTO tournament_result (TournamentID, Position, TotalMatchesPlayed, TotalWins, TotalLosses, ManOfTournament, BestBatsman, BestBowler, SummaryNotes, EnteredBy)
SELECT t.TournamentID, 'champions', 1, 1, 0, NULL, NULL, NULL, 'Under 21 Completed Cup result summary.', u.UserID
FROM tournament t
JOIN user u ON u.Role = 'Admin' AND u.Status = 'active'
WHERE t.Name = 'Under 21 Completed Cup' AND t.Status = 'completed'
  AND NOT EXISTS (SELECT 1 FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID)
LIMIT 1;

INSERT INTO tournament (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT 'Under 21 Cancelled Cup', 'Under 21', 'T20', 'Under 21 cancelled tournament.', '2026-08-04', '2026-07-30', 22, 'Elite Ground 2', 0.00, u.UserID, 'cancelled', 0, 'Weather disruption'
FROM user u WHERE u.Role = 'Admin' AND u.Status = 'active'
  AND NOT EXISTS (SELECT 1 FROM tournament t WHERE t.Name = 'Under 21 Cancelled Cup')
LIMIT 1;

-- Player performance data for completed tournaments
-- Open Completed Cup (players in the Open age group)
INSERT INTO playermatchperformance
(MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating, VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT m.MatchID, s.PlayerID, s.RunsScored, s.BallsFaced, s.WicketsTaken, s.OversBowled, s.RunsConceded, s.Catches, s.Stumpings, s.Rating,
       'verified', u.UserID, u.UserID, NOW()
FROM crimatch m
JOIN tournament t ON t.TournamentID = m.TournamentID
JOIN (
    SELECT 16 AS PlayerID, 42 AS RunsScored, 31 AS BallsFaced, 0 AS WicketsTaken, 0.0 AS OversBowled, 0 AS RunsConceded, 1 AS Catches, 0 AS Stumpings, 8.5 AS Rating
    UNION ALL
    SELECT 18, 18, 22, 1, 4.0, 24, 0, 0, 7.8
    UNION ALL
    SELECT 20, 9, 12, 2, 4.0, 19, 0, 0, 8.9
) s
JOIN user u ON u.UserID = s.PlayerID
WHERE m.MatchID = 25
  AND t.Status = 'completed'
  AND TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) >= 21
  AND NOT EXISTS (
      SELECT 1
      FROM playermatchperformance p
      WHERE p.MatchID = m.MatchID AND p.PlayerID = s.PlayerID
  )
LIMIT 3;

INSERT INTO playermatchperformance
(MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating, VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT m.MatchID, s.PlayerID, s.RunsScored, s.BallsFaced, s.WicketsTaken, s.OversBowled, s.RunsConceded, s.Catches, s.Stumpings, s.Rating,
       'verified', u.UserID, u.UserID, NOW()
FROM crimatch m
JOIN tournament t ON t.TournamentID = m.TournamentID
JOIN (
    SELECT 21 AS PlayerID, 55 AS RunsScored, 38 AS BallsFaced, 0 AS WicketsTaken, 0.0 AS OversBowled, 0 AS RunsConceded, 1 AS Catches, 0 AS Stumpings, 8.7 AS Rating
    UNION ALL
    SELECT 22, 24, 20, 1, 4.0, 21, 1, 0, 8.0
    UNION ALL
    SELECT 25, 17, 16, 1, 4.0, 18, 0, 0, 7.6
) s
JOIN user u ON u.UserID = s.PlayerID
WHERE m.MatchID = 26
  AND t.Status = 'completed'
  AND TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) >= 21
  AND NOT EXISTS (
      SELECT 1
      FROM playermatchperformance p
      WHERE p.MatchID = m.MatchID AND p.PlayerID = s.PlayerID
  )
LIMIT 3;

-- Under 21 Completed Cup (players in the Under 21 age group)
INSERT INTO playermatchperformance
(MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating, VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT m.MatchID, s.PlayerID, s.RunsScored, s.BallsFaced, s.WicketsTaken, s.OversBowled, s.RunsConceded, s.Catches, s.Stumpings, s.Rating,
       'verified', u.UserID, u.UserID, NOW()
FROM crimatch m
JOIN tournament t ON t.TournamentID = m.TournamentID
JOIN (
    SELECT 52 AS PlayerID, 61 AS RunsScored, 44 AS BallsFaced, 0 AS WicketsTaken, 0.0 AS OversBowled, 0 AS RunsConceded, 1 AS Catches, 0 AS Stumpings, 9.1 AS Rating
    UNION ALL
    SELECT 53, 15, 18, 2, 4.0, 17, 1, 0, 8.3
    UNION ALL
    SELECT 54, 34, 29, 1, 3.0, 22, 0, 0, 8.0
) s
JOIN user u ON u.UserID = s.PlayerID
WHERE m.MatchID = 24
  AND t.Status = 'completed'
  AND TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) >= 19
  AND TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 21
  AND NOT EXISTS (
      SELECT 1
      FROM playermatchperformance p
      WHERE p.MatchID = m.MatchID AND p.PlayerID = s.PlayerID
  )
LIMIT 3;
