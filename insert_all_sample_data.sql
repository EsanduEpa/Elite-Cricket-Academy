-- =====================================================
-- ELITE CRICKET ACADEMY - COMPLETE SAMPLE DATA
-- Sri Lanka themed data for all empty tables
-- Run this AFTER cricket_academy (6).sql
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- =====================================================
-- 1. MEMBERSHIP PLANS
-- =====================================================
INSERT INTO `membershipplan` (`PlanID`, `PlanName`, `Description`, `MonthlyFee`, `SessionsPerWeek`, `PrivateSessionsIncluded`, `FacilityAccessIncluded`, `Status`) VALUES
(1, 'Basic Plan', 'Group coaching sessions with access to practice nets', 5000.00, 3, 0, 0, 'active'),
(2, 'Standard Plan', 'Group coaching plus facility access and fitness training', 8500.00, 4, 1, 1, 'active'),
(3, 'Premium Plan', 'Unlimited group sessions, private coaching, full facility access', 15000.00, 6, 3, 1, 'active'),
(4, 'Junior Plan', 'Special plan for under-13 players with weekend sessions', 3500.00, 2, 0, 0, 'active');

-- =====================================================
-- 2. PLAYER SUBSCRIPTIONS
-- =====================================================
INSERT INTO `playersubscription` (`SubscriptionID`, `PlayerID`, `PlanID`, `StartDate`, `EndDate`, `Status`, `MonthlyFee`, `PaymentDay`, `AutoRenewal`) VALUES
(1, 6, 3, '2025-09-01', '2026-08-31', 'active', 15000.00, 1, 1),
(2, 7, 2, '2025-10-01', '2026-09-30', 'active', 8500.00, 1, 1),
(3, 15, 1, '2025-10-01', '2026-09-30', 'active', 5000.00, 15, 1),
(4, 16, 2, '2025-11-01', '2026-10-31', 'active', 8500.00, 1, 0),
(5, 18, 3, '2025-10-01', '2026-09-30', 'active', 15000.00, 1, 1),
(6, 20, 1, '2025-11-01', '2026-10-31', 'active', 5000.00, 1, 1);

-- =====================================================
-- 3. SUBSCRIPTION PAYMENTS (last 6 months)
-- =====================================================
INSERT INTO `subscriptionpayment` (`PaymentID`, `SubscriptionID`, `PaymentDate`, `Amount`, `PaymentMethod`, `Status`, `DueDate`, `LateFee`, `ProcessedBy`) VALUES
-- Player 6 (Esandu) - Premium 15000
(1, 1, '2025-09-01', 15000.00, 'card', 'completed', '2025-09-01', 0.00, 5),
(2, 1, '2025-10-01', 15000.00, 'card', 'completed', '2025-10-01', 0.00, 5),
(3, 1, '2025-11-01', 15000.00, 'online', 'completed', '2025-11-01', 0.00, NULL),
(4, 1, '2025-12-01', 15000.00, 'card', 'completed', '2025-12-01', 0.00, 5),
(5, 1, '2026-01-01', 15000.00, 'online', 'completed', '2026-01-01', 0.00, NULL),
(6, 1, '2026-02-01', 15000.00, 'card', 'completed', '2026-02-01', 0.00, 5),
-- Player 7 (Vijini) - Standard 8500
(7, 2, '2025-10-01', 8500.00, 'cash', 'completed', '2025-10-01', 0.00, 5),
(8, 2, '2025-11-01', 8500.00, 'cash', 'completed', '2025-11-01', 0.00, 5),
(9, 2, '2025-12-01', 8500.00, 'card', 'completed', '2025-12-01', 0.00, 5),
(10, 2, '2026-01-01', 8500.00, 'card', 'completed', '2026-01-01', 0.00, 5),
(11, 2, '2026-02-01', 8500.00, 'online', 'completed', '2026-02-01', 0.00, NULL),
-- Player 15 (Swairi) - Basic 5000
(12, 3, '2025-10-15', 5000.00, 'cash', 'completed', '2025-10-15', 0.00, 5),
(13, 3, '2025-11-15', 5000.00, 'cash', 'completed', '2025-11-15', 0.00, 5),
(14, 3, '2025-12-15', 5000.00, 'bank_transfer', 'completed', '2025-12-15', 0.00, NULL),
(15, 3, '2026-01-15', 5000.00, 'cash', 'completed', '2026-01-15', 0.00, 5),
(16, 3, '2026-02-15', 5000.00, 'pending', 'pending', '2026-02-15', 0.00, NULL),
-- Player 16 (esandu) - Standard 8500
(17, 4, '2025-11-01', 8500.00, 'card', 'completed', '2025-11-01', 0.00, 5),
(18, 4, '2025-12-01', 8500.00, 'card', 'completed', '2025-12-01', 0.00, 5),
(19, 4, '2026-01-01', 8500.00, 'online', 'completed', '2026-01-01', 0.00, NULL),
(20, 4, '2026-02-01', 8500.00, 'card', 'completed', '2026-02-01', 0.00, 5),
-- Player 18 (vijini liyanamana) - Premium 15000
(21, 5, '2025-10-01', 15000.00, 'card', 'completed', '2025-10-01', 0.00, 5),
(22, 5, '2025-11-01', 15000.00, 'online', 'completed', '2025-11-01', 0.00, NULL),
(23, 5, '2025-12-01', 15000.00, 'card', 'completed', '2025-12-01', 0.00, 5),
(24, 5, '2026-01-01', 15000.00, 'card', 'completed', '2026-01-01', 0.00, 5),
(25, 5, '2026-02-01', 15000.00, 'online', 'completed', '2026-02-01', 0.00, NULL),
-- Player 20 (esandu yapa) - Basic 5000
(26, 6, '2025-11-01', 5000.00, 'cash', 'completed', '2025-11-01', 0.00, 5),
(27, 6, '2025-12-01', 5000.00, 'cash', 'completed', '2025-12-01', 0.00, 5),
(28, 6, '2026-01-01', 5000.00, 'bank_transfer', 'completed', '2026-01-01', 0.00, NULL),
(29, 6, '2026-02-01', 5000.00, 'cash', 'completed', '2026-02-01', 0.00, 5);

-- =====================================================
-- 4. TOURNAMENTS
-- =====================================================
INSERT INTO `tournament` (`TournamentID`, `Name`, `tdate`, `Location`, `CreatedBy`, `Status`, `PrizePool`) VALUES
(1, 'Elite Junior Championship 2026', '2026-03-15', 'R. Premadasa Stadium, Colombo', 1, 'upcoming', 250000.00),
(2, 'Western Province U-19 Tournament', '2026-02-25', 'SSC Ground, Colombo', 1, 'upcoming', 150000.00),
(3, 'Inter-Academy Cricket League', '2026-01-20', 'Sinhalese Sports Club Ground', 1, 'ongoing', 200000.00),
(4, 'Southern Province Youth Cup', '2025-12-10', 'Galle International Stadium', 1, 'completed', 100000.00),
(5, 'Colombo Schools Cricket Festival', '2025-11-05', 'P. Sara Oval, Colombo', 1, 'completed', 75000.00);

-- =====================================================
-- 5. TOURNAMENT PLAYERS
-- =====================================================
INSERT INTO `tournamentplayer` (`TournamentID`, `PlayerID`, `Team`, `RoleInTeam`, `SelectedBy`) VALUES
(1, 6, 'Elite Academy A', 'All-Rounder', 9),
(1, 7, 'Elite Academy A', 'Batsman', 9),
(1, 15, 'Elite Academy A', 'Bowler', 9),
(1, 18, 'Elite Academy A', 'Batsman', 9),
(2, 6, 'Elite Academy', 'Captain', 9),
(2, 15, 'Elite Academy', 'Bowler', 9),
(2, 16, 'Elite Academy', 'Wicket-Keeper', 9),
(2, 20, 'Elite Academy', 'All-Rounder', 9),
(3, 6, 'Elite Academy', 'Captain', 9),
(3, 7, 'Elite Academy', 'Batsman', 9),
(3, 15, 'Elite Academy', 'Bowler', 9),
(3, 16, 'Elite Academy', 'Batsman', 9),
(3, 18, 'Elite Academy', 'Vice-Captain', 9),
(3, 20, 'Elite Academy', 'All-Rounder', 9),
(4, 6, 'Elite Academy', 'Captain', 9),
(4, 7, 'Elite Academy', 'Batsman', 9),
(4, 15, 'Elite Academy', 'Bowler', 9),
(4, 18, 'Elite Academy', 'Batsman', 9),
(5, 7, 'Elite Academy', 'Batsman', 9),
(5, 15, 'Elite Academy', 'Bowler', 9),
(5, 16, 'Elite Academy', 'Wicket-Keeper', 9);

-- =====================================================
-- 6. MATCHES (crimatch)
-- =====================================================
INSERT INTO `crimatch` (`MatchID`, `TournamentID`, `Date`, `Venue`, `OpponentTeam`, `Result`, `OurScore`, `OpponentScore`) VALUES
(1, 4, '2025-12-10', 'Galle International Stadium', 'Galle Cricket Academy', 'win', '185/6', '142/10'),
(2, 4, '2025-12-12', 'Galle International Stadium', 'Matara Sports Club', 'win', '210/4', '178/10'),
(3, 4, '2025-12-14', 'Galle International Stadium', 'Southern Stars Academy', 'loss', '145/10', '148/3'),
(4, 5, '2025-11-05', 'P. Sara Oval', 'Royal College', 'win', '198/7', '165/10'),
(5, 5, '2025-11-07', 'P. Sara Oval', 'S. Thomas College', 'draw', '220/8', '218/9'),
(6, 5, '2025-11-09', 'P. Sara Oval', 'Ananda College', 'win', '175/5', '120/10'),
(7, 3, '2026-01-20', 'SSC Ground', 'Colombo Cricket Club', 'win', '195/6', '160/10'),
(8, 3, '2026-01-25', 'SSC Ground', 'Nondescripts CC', 'win', '225/3', '180/10'),
(9, 3, '2026-02-01', 'SSC Ground', 'Tamil Union CC', 'loss', '155/10', '158/4'),
(10, 3, '2026-02-10', 'SSC Ground', 'Bloomfield CC', 'pending', NULL, NULL);

-- =====================================================
-- 7. PLAYER MATCH PERFORMANCE
-- =====================================================
INSERT INTO `playermatchperformance` (`PerformanceID`, `MatchID`, `PlayerID`, `RunsScored`, `BallsFaced`, `WicketsTaken`, `OversBowled`, `RunsConceded`, `Catches`, `Stumpings`, `Rating`) VALUES
-- Match 1: vs Galle Cricket Academy (win)
(1, 1, 6, 65, 48, 2, 4.0, 28, 1, 0, 8.5),
(2, 1, 7, 42, 35, 0, 0.0, 0, 2, 0, 7.0),
(3, 1, 15, 12, 18, 4, 8.0, 32, 0, 0, 8.0),
(4, 1, 18, 38, 30, 0, 0.0, 0, 1, 0, 7.5),
-- Match 2: vs Matara Sports Club (win)
(5, 2, 6, 85, 62, 1, 3.0, 22, 2, 0, 9.0),
(6, 2, 7, 55, 40, 0, 0.0, 0, 1, 0, 8.0),
(7, 2, 15, 8, 12, 3, 7.0, 38, 1, 0, 7.5),
(8, 2, 18, 28, 25, 0, 0.0, 0, 0, 0, 6.5),
-- Match 4: vs Royal College (win)
(9, 4, 7, 72, 55, 0, 0.0, 0, 1, 0, 8.5),
(10, 4, 15, 15, 20, 5, 9.0, 35, 0, 0, 9.0),
(11, 4, 16, 35, 28, 0, 0.0, 0, 3, 2, 7.5),
-- Match 7: vs Colombo Cricket Club (win)
(12, 7, 6, 78, 56, 1, 2.0, 15, 1, 0, 9.0),
(13, 7, 7, 45, 38, 0, 0.0, 0, 2, 0, 7.5),
(14, 7, 15, 5, 8, 4, 8.0, 28, 0, 0, 8.5),
(15, 7, 16, 22, 18, 0, 0.0, 0, 1, 0, 6.5),
(16, 7, 18, 30, 22, 0, 0.0, 0, 1, 0, 7.0),
(17, 7, 20, 10, 15, 2, 5.0, 32, 0, 0, 6.5),
-- Match 8: vs Nondescripts CC (win)
(18, 8, 6, 92, 68, 0, 1.0, 8, 1, 0, 9.5),
(19, 8, 7, 68, 50, 0, 0.0, 0, 0, 0, 8.5),
(20, 8, 15, 3, 5, 3, 6.0, 25, 1, 0, 7.5),
(21, 8, 18, 40, 32, 0, 0.0, 0, 2, 0, 7.5),
-- Match 9: vs Tamil Union CC (loss)
(22, 9, 6, 35, 30, 1, 4.0, 35, 0, 0, 6.0),
(23, 9, 7, 28, 25, 0, 0.0, 0, 1, 0, 5.5),
(24, 9, 15, 10, 15, 2, 8.0, 42, 0, 0, 5.0),
(25, 9, 18, 45, 38, 0, 0.0, 0, 0, 0, 7.0);

-- =====================================================
-- 8. UPDATE PLAYER OVERALL STATS (with real totals)
-- =====================================================
UPDATE `playeroverallstats` SET
    `MatchesPlayed` = 7, `TotalRuns` = 355, `TotalWickets` = 5,
    `HighestScore` = 92, `BattingAverage` = 50.71, `BowlingAverage` = 21.60,
    `StrikeRate` = 121.58, `EconomyRate` = 5.40, `LastUpdatedBy` = 3
WHERE `PlayerID` = 6;

UPDATE `playeroverallstats` SET
    `MatchesPlayed` = 7, `TotalRuns` = 310, `TotalWickets` = 0,
    `HighestScore` = 72, `BattingAverage` = 44.29, `BowlingAverage` = 0.00,
    `StrikeRate` = 112.73, `EconomyRate` = 0.00, `LastUpdatedBy` = 3
WHERE `PlayerID` = 7;

UPDATE `playeroverallstats` SET
    `MatchesPlayed` = 7, `TotalRuns` = 53, `TotalWickets` = 21,
    `HighestScore` = 15, `BattingAverage` = 7.57, `BowlingAverage` = 14.29,
    `StrikeRate` = 68.83, `EconomyRate` = 3.91, `LastUpdatedBy` = 3
WHERE `PlayerID` = 15;

UPDATE `playeroverallstats` SET
    `MatchesPlayed` = 4, `TotalRuns` = 57, `TotalWickets` = 0,
    `HighestScore` = 35, `BattingAverage` = 14.25, `BowlingAverage` = 0.00,
    `StrikeRate` = 86.36, `EconomyRate` = 0.00, `LastUpdatedBy` = 3
WHERE `PlayerID` = 16;

UPDATE `playeroverallstats` SET
    `MatchesPlayed` = 6, `TotalRuns` = 181, `TotalWickets` = 0,
    `HighestScore` = 45, `BattingAverage` = 30.17, `BowlingAverage` = 0.00,
    `StrikeRate` = 109.70, `EconomyRate` = 0.00, `LastUpdatedBy` = 3
WHERE `PlayerID` = 18;

UPDATE `playeroverallstats` SET
    `MatchesPlayed` = 2, `TotalRuns` = 10, `TotalWickets` = 2,
    `HighestScore` = 10, `BattingAverage` = 5.00, `BowlingAverage` = 16.00,
    `StrikeRate` = 66.67, `EconomyRate` = 6.40, `LastUpdatedBy` = 3
WHERE `PlayerID` = 20;

-- =====================================================
-- 9. PERFORMANCE UPDATES (coach assessments)
-- =====================================================
INSERT INTO `performanceupdate` (`UpdateID`, `PlayerID`, `CoachID`, `SessionID`, `UpdateType`, `RunsScored`, `BallsFaced`, `Fours`, `Sixes`, `WicketsTaken`, `OversBowled`, `RunsConceded`, `CatchesTaken`, `TechnicalRating`, `FitnessRating`, `AttitudeRating`, `Comments`, `UpdateDate`, `Status`, `ApprovedBy`, `ApprovedAt`) VALUES
-- Player 6 (Esandu) - monthly assessments
(1, 6, 3, 9, 'training_assessment', 45, 32, 5, 2, 1, 3.0, 18, 2, 8, 7, 9, 'Excellent batting form. Cover drives are improving. Needs more focus on bowling accuracy.', '2025-09-15 10:00:00', 'approved', 3, '2025-09-15 10:00:00'),
(2, 6, 3, 9, 'training_assessment', 52, 38, 7, 1, 2, 4.0, 22, 1, 8, 8, 9, 'Consistent performance. Good work ethic. Ready for inter-academy tournament.', '2025-10-15 10:00:00', 'approved', 3, '2025-10-15 10:00:00'),
(3, 6, 3, 9, 'skill_evaluation', 60, 42, 8, 2, 1, 3.0, 20, 2, 9, 8, 9, 'Outstanding progress in all departments. Leadership qualities emerging.', '2025-11-15 10:00:00', 'approved', 3, '2025-11-15 10:00:00'),
(4, 6, 3, 9, 'training_assessment', 48, 35, 6, 1, 0, 2.0, 15, 1, 8, 7, 8, 'Solid training month. Minor dip in bowling but batting remains strong.', '2025-12-15 10:00:00', 'approved', 3, '2025-12-15 10:00:00'),
(5, 6, 3, 9, 'training_assessment', 55, 40, 7, 2, 2, 4.0, 25, 2, 9, 8, 9, 'Exceptional month. Leading the team well in inter-academy league.', '2026-01-15 10:00:00', 'approved', 3, '2026-01-15 10:00:00'),
(6, 6, 3, 9, 'fitness_test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 9, 9, 'Peak fitness level. Agility test score improved by 15%.', '2026-02-10 10:00:00', 'approved', 3, '2026-02-10 10:00:00'),
-- Player 7 (Vijini) - monthly assessments
(7, 7, 3, 9, 'training_assessment', 38, 30, 4, 1, 0, 0.0, 0, 1, 7, 6, 8, 'Good batting technique, needs to work on playing pace bowling.', '2025-10-15 10:00:00', 'approved', 3, '2025-10-15 10:00:00'),
(8, 7, 3, 9, 'training_assessment', 42, 35, 5, 1, 0, 0.0, 0, 2, 7, 7, 8, 'Improving steadily. Footwork against spin is much better now.', '2025-11-15 10:00:00', 'approved', 3, '2025-11-15 10:00:00'),
(9, 7, 3, 9, 'skill_evaluation', 50, 38, 6, 2, 0, 0.0, 0, 1, 8, 7, 9, 'Breakthrough month. Scored maiden fifty in tournament match.', '2025-12-15 10:00:00', 'approved', 3, '2025-12-15 10:00:00'),
(10, 7, 3, 9, 'training_assessment', 48, 36, 5, 1, 0, 0.0, 0, 2, 8, 7, 8, 'Consistent performer. Should be considered for opening position.', '2026-01-15 10:00:00', 'approved', 3, '2026-01-15 10:00:00'),
-- Player 15 (Swairi) - monthly assessments
(11, 15, 3, 10, 'training_assessment', 8, 12, 1, 0, 3, 6.0, 22, 0, 7, 7, 8, 'Excellent bowling. Good line and length. Batting needs significant improvement.', '2025-10-15 10:00:00', 'approved', 3, '2025-10-15 10:00:00'),
(12, 15, 3, 10, 'training_assessment', 12, 15, 2, 0, 4, 8.0, 30, 1, 8, 7, 8, 'Best bowling figures this month. Yorker execution is outstanding.', '2025-11-15 10:00:00', 'approved', 3, '2025-11-15 10:00:00'),
(13, 15, 3, 10, 'skill_evaluation', 10, 14, 1, 0, 5, 9.0, 28, 0, 9, 8, 9, '5-wicket haul in tournament! Becoming our premier fast bowler.', '2025-12-15 10:00:00', 'approved', 3, '2025-12-15 10:00:00'),
(14, 15, 3, 10, 'training_assessment', 5, 8, 0, 0, 3, 7.0, 25, 0, 8, 7, 8, 'Consistent bowling performance. Working on slower ball variations.', '2026-01-15 10:00:00', 'approved', 3, '2026-01-15 10:00:00'),
-- Player 18 (vijini liyanamana)
(15, 18, 3, 9, 'training_assessment', 35, 28, 4, 1, 0, 0.0, 0, 1, 7, 6, 8, 'Good technique. Needs to work on concentration for longer innings.', '2025-11-15 10:00:00', 'approved', 3, '2025-11-15 10:00:00'),
(16, 18, 3, 9, 'training_assessment', 40, 32, 5, 1, 0, 0.0, 0, 2, 7, 7, 8, 'Improved shot selection. Pull shot is becoming a strength.', '2025-12-15 10:00:00', 'approved', 3, '2025-12-15 10:00:00'),
(17, 18, 3, 9, 'training_assessment', 45, 35, 6, 1, 0, 0.0, 0, 1, 8, 7, 9, 'Excellent progress. One of our most improved players this quarter.', '2026-01-15 10:00:00', 'approved', 3, '2026-01-15 10:00:00');

-- =====================================================
-- 10. COACH APPOINTMENTS
-- =====================================================
INSERT INTO `coachappointment` (`AppointmentID`, `CoachID`, `PlayerID`, `AppointmentDate`, `StartTime`, `EndTime`, `Status`, `Reason`) VALUES
(1, 9, 6, '2026-02-18', '09:00:00', '10:00:00', 'scheduled', 'Performance review and tournament preparation discussion'),
(2, 9, 15, '2026-02-18', '10:30:00', '11:30:00', 'scheduled', 'Bowling technique analysis and improvement plan'),
(3, 9, 7, '2026-02-19', '09:00:00', '10:00:00', 'scheduled', 'Batting strategy for upcoming Western Province tournament'),
(4, 9, 18, '2026-02-19', '14:00:00', '15:00:00', 'scheduled', 'Monthly progress review'),
(5, 9, 6, '2026-02-10', '09:00:00', '10:00:00', 'completed', 'Inter-academy league team selection discussion'),
(6, 9, 15, '2026-02-05', '10:00:00', '11:00:00', 'completed', 'Review fast bowling action after video analysis'),
(7, 9, 7, '2026-02-03', '14:00:00', '15:00:00', 'completed', 'Discussed pace bowling techniques and fielding improvement'),
(8, 9, 16, '2026-02-20', '09:00:00', '10:00:00', 'scheduled', 'Wicket-keeping technique session'),
(9, 9, 20, '2026-02-20', '10:30:00', '11:30:00', 'scheduled', 'All-round performance discussion'),
(10, 9, 18, '2026-01-28', '09:00:00', '10:00:00', 'cancelled', 'Player requested reschedule due to school exam');

-- =====================================================
-- 11. TRAINER APPOINTMENTS
-- =====================================================
INSERT INTO `trainerappointment` (`AppointmentID`, `TrainerID`, `PlayerID`, `AppointmentDate`, `StartTime`, `EndTime`, `Status`, `Reason`) VALUES
(1, 10, 6, '2026-02-17', '06:00:00', '07:30:00', 'scheduled', 'Strength training assessment and new program design'),
(2, 10, 15, '2026-02-17', '08:00:00', '09:00:00', 'scheduled', 'Fast bowler fitness program - endurance focus'),
(3, 10, 7, '2026-02-18', '06:00:00', '07:00:00', 'scheduled', 'Flexibility and agility training'),
(4, 10, 18, '2026-02-18', '07:30:00', '08:30:00', 'scheduled', 'Core strength program review'),
(5, 10, 6, '2026-02-10', '06:00:00', '07:30:00', 'completed', 'Monthly fitness test - excellent results'),
(6, 10, 15, '2026-02-10', '08:00:00', '09:00:00', 'completed', 'Bowling-specific strength training'),
(7, 10, 16, '2026-02-19', '06:00:00', '07:00:00', 'scheduled', 'Wicket-keeper agility and reflex training'),
(8, 10, 20, '2026-02-19', '07:30:00', '08:30:00', 'scheduled', 'General fitness assessment');

-- =====================================================
-- 12. FACILITY BOOKINGS
-- =====================================================
INSERT INTO `facilitybooking` (`FacilityBookingID`, `FacilityID`, `PlayerID`, `BookingDate`, `StartTime`, `EndTime`, `Status`, `TotalCost`, `BookedBy`) VALUES
(1, 1, 6, '2026-02-17', '09:00:00', '11:00:00', 'confirmed', 100.00, 5),
(2, 3, 15, '2026-02-17', '14:00:00', '15:00:00', 'confirmed', 75.00, 5),
(3, 4, 6, '2026-02-18', '08:00:00', '10:00:00', 'confirmed', 400.00, 5),
(4, 5, 7, '2026-02-18', '16:00:00', '18:00:00', 'confirmed', 200.00, 5),
(5, 1, 18, '2026-02-19', '09:00:00', '11:00:00', 'confirmed', 100.00, 5),
(6, 2, 16, '2026-02-19', '14:00:00', '16:00:00', 'confirmed', 100.00, 5),
(7, 3, 6, '2026-02-16', '10:00:00', '11:00:00', 'completed', 75.00, 5),
(8, 1, 15, '2026-02-16', '14:00:00', '16:00:00', 'completed', 100.00, 5),
(9, 5, 20, '2026-02-20', '09:00:00', '11:00:00', 'confirmed', 200.00, 5),
(10, 4, 7, '2026-02-15', '08:00:00', '12:00:00', 'completed', 800.00, 5);

-- =====================================================
-- 13. COACHING SESSIONS (session logs per player)
-- =====================================================
INSERT INTO `coachingsession` (`SessionLogID`, `SessionID`, `PlayerID`, `CoachID`, `AttendanceStatus`, `PerformanceNotes`, `SkillsWorkedOn`, `AreasForImprovement`, `HomeworkAssigned`, `SessionRating`, `CreatedAt`) VALUES
(1, 9, 6, 3, 'present', 'Excellent batting display in net session. Cover drive was pristine.', 'Cover Drive, Straight Drive, Defence', 'Playing short balls, Footwork against spin', 'Practice 50 cover drives daily against throw-downs', 9, '2026-02-11 11:00:00'),
(2, 9, 7, 3, 'present', 'Good session. Working well on front foot play.', 'Front Foot Defence, On Drive', 'Back foot play, Running between wickets', 'Shadow batting 30 mins focusing on back foot', 7, '2026-02-11 11:00:00'),
(3, 9, 15, 3, 'present', 'Bowling with great rhythm. Yorker accuracy at 70%.', 'Yorker, Bouncer, Out-swinger', 'Slower ball variations, Death bowling', 'Run-up drills and 20 yorker practice deliveries', 8, '2026-02-11 11:00:00'),
(4, 9, 18, 3, 'present', 'Showed improvement in shot selection. Patient innings in practice match.', 'Shot Selection, Defensive Technique', 'Playing pace bowling, Concentration', 'Watch video analysis of Sangakkara defensive technique', 7, '2026-02-11 11:00:00'),
(5, 9, 16, 3, 'late', 'Arrived 15 mins late. Once started, performed well in fielding drills.', 'Fielding, Throwing, Ground Fielding', 'Punctuality, Catching in slips', 'Catching practice with tennis ball against wall - 100 catches', 6, '2026-02-11 11:00:00'),
(6, 10, 6, 3, 'present', 'Private session focused on advanced techniques. Excellent receptiveness.', 'Reverse Sweep, Switch Hit, Sweep', 'Consistency with unorthodox shots', 'Practice sweep shot in front of mirror for technique', 9, '2026-02-11 15:30:00'),
(7, 10, 15, 3, 'present', 'Worked on bowling variations. Good progress on leg cutter.', 'Leg Cutter, Off Cutter, Slower Ball', 'Maintaining pace while varying delivery', 'Grip exercises and 30 slower ball practice deliveries', 8, '2026-02-11 15:30:00'),
(8, 11, 7, 3, 'present', 'Fielding session. Good ground fielding but catching needs work.', 'Ground Fielding, Direct Hits', 'High catches, Slip catching', 'Reflex ball drills 20 mins daily', 7, '2026-02-12 10:00:00'),
(9, 11, 16, 3, 'present', 'Good effort in fielding drills. Throwing accuracy improved.', 'Throwing, Intercepting, Diving', 'Throwing from deep, Relay throws', 'Target throwing practice - 50 throws daily', 7, '2026-02-12 10:00:00'),
(10, 11, 18, 3, 'absent', 'Absent - reported sick (flu).', NULL, NULL, NULL, NULL, '2026-02-12 10:00:00'),
(11, 11, 20, 3, 'present', 'Promising all-round fielding skills. Keen to improve.', 'All-round Fielding, Catching', 'Consistency, Game awareness', 'Watch fielding highlights and practice sliding stops', 7, '2026-02-12 10:00:00');

-- =====================================================
-- 14. SESSION ATTENDANCE
-- =====================================================
INSERT INTO `SessionAttendance` (`AttendanceID`, `EnrollmentID`, `AttendanceStatus`, `AttendanceNotes`, `MarkedBy`, `MarkedAt`) VALUES
(1, 24, 'present', 'On time, active participation', 3, '2026-02-11 09:05:00'),
(2, 25, 'present', 'Good attendance', 3, '2026-02-11 09:05:00'),
(3, 26, 'present', 'On time', 3, '2026-02-11 09:05:00'),
(4, 27, 'late', 'Arrived 15 minutes late', 3, '2026-02-11 09:20:00'),
(5, 28, 'present', 'Excellent participation', 3, '2026-02-11 09:05:00'),
(6, 29, 'present', 'Good focus', 3, '2026-02-11 14:05:00'),
(7, 30, 'present', 'On time', 3, '2026-02-11 14:05:00'),
(8, 31, 'present', 'Active participation in fielding drills', 3, '2026-02-12 08:05:00'),
(9, 32, 'present', 'Good effort', 3, '2026-02-12 08:05:00'),
(10, 33, 'absent', 'Reported sick - flu', 3, '2026-02-12 08:05:00'),
(11, 34, 'present', 'On time, enthusiastic', 3, '2026-02-12 08:05:00');

-- =====================================================
-- 15. EVENT ENROLLMENTS
-- =====================================================
INSERT INTO `eventenrollment` (`EnrollmentID`, `EventID`, `PlayerID`, `EnrollmentDate`, `Status`) VALUES
(1, 4, 6, '2025-10-22 16:50:00', 'enrolled'),
(2, 4, 7, '2025-10-22 17:00:00', 'enrolled'),
(3, 4, 15, '2025-10-22 17:10:00', 'enrolled'),
(4, 5, 7, '2025-10-23 09:00:00', 'enrolled'),
(5, 5, 16, '2025-10-23 09:15:00', 'enrolled'),
(6, 11, 6, '2026-01-17 10:00:00', 'enrolled'),
(7, 11, 18, '2026-01-17 10:30:00', 'enrolled'),
(8, 13, 6, '2026-02-10 18:00:00', 'enrolled'),
(9, 13, 7, '2026-02-10 18:15:00', 'enrolled'),
(10, 13, 15, '2026-02-10 18:30:00', 'enrolled'),
(11, 13, 20, '2026-02-10 18:45:00', 'enrolled');

-- =====================================================
-- 16. NUTRITION PLANS
-- =====================================================
INSERT INTO `nutritionplan` (`PlanID`, `TrainerID`, `nutritionPlanName`, `DietDetails`, `Duration`, `CreatedDate`) VALUES
(1, 10, 'Batsman Power Diet', 'High-carb diet for sustained energy during long innings.\n\nBreakfast: String hoppers with coconut sambol, 2 eggs, king coconut water\nMid-morning: Banana and handful of cashews\nLunch: Rice with dhal, fish curry, gotukola salad, papadam\nEvening snack: Thambili (king coconut) and protein bar\nDinner: Chicken breast with steamed vegetables, small portion rice\nBefore bed: Warm milk with kithul treacle', 90, '2026-01-15'),
(2, 10, 'Fast Bowler Endurance Plan', 'High-protein recovery diet for fast bowlers.\n\nBreakfast: Oats with sliced banana and honey, 3 egg whites\nMid-morning: Greek yogurt with granola\nLunch: Brown rice, grilled chicken, lentil curry, green salad\nPost-training: Whey protein shake with banana\nDinner: Fish ambul thiyal, steamed vegetables, small rice portion\nBefore bed: Casein protein with warm water', 90, '2026-01-15'),
(3, 10, 'Junior Player Growth Diet', 'Balanced diet for growing young cricketers.\n\nBreakfast: Milk rice with lunu miris, fresh fruit juice\nMid-morning: Fruit salad (papaya, banana, mango)\nLunch: Rice with chicken curry, parippu, mallung, curd\nEvening snack: Sandwich with cheese and vegetables\nDinner: Kottu or fried rice with vegetables and egg\nBefore bed: Glass of warm milk', 60, '2026-01-20');

-- =====================================================
-- 17. NUTRITION PLAN ASSIGNMENTS
-- =====================================================
INSERT INTO `nutritionplan_player` (`PlanID`, `PlayerID`, `AssignedDate`) VALUES
(1, 6, '2026-01-15'),
(1, 7, '2026-01-15'),
(1, 18, '2026-01-20'),
(2, 15, '2026-01-15'),
(3, 16, '2026-01-20'),
(3, 20, '2026-01-20');

-- =====================================================
-- 18. SUPPLEMENT PLANS
-- =====================================================
INSERT INTO `supplementplan` (`PlanID`, `TrainerID`, `SupplementPlanName`, `SupplementDetails`, `Dosage`, `Duration`, `CreatedDate`) VALUES
(1, 10, 'Whey Protein Isolate', 'Post-workout protein supplement for muscle recovery. Take within 30 minutes after training. Mix with water or milk.', '25g (1 scoop) after each training session', 90, '2026-01-10'),
(2, 10, 'Creatine Monohydrate', 'Strength and power supplement. Helps with explosive movements in batting and bowling.', '5g daily with water, take with breakfast', 60, '2026-01-10'),
(3, 10, 'Multivitamin Complex', 'Daily vitamin and mineral supplement to fill nutritional gaps.', '1 tablet daily with breakfast', 180, '2026-01-10'),
(4, 10, 'BCAA Recovery', 'Branch chain amino acids for muscle recovery during intense training periods.', '10g mixed in water, sip during training', 90, '2026-01-15'),
(5, 10, 'Calcium + Vitamin D', 'Bone health supplement, especially important for fast bowlers.', '1 tablet (500mg Ca + 400IU D3) daily', 120, '2026-01-20');

-- =====================================================
-- 19. SUPPLEMENT ASSIGNMENTS
-- =====================================================
INSERT INTO `supplement_player` (`PlanID`, `PlayerID`, `AssignedDate`) VALUES
(1, 6, '2026-01-10'),
(1, 15, '2026-01-10'),
(1, 18, '2026-01-15'),
(2, 6, '2026-01-10'),
(2, 15, '2026-01-10'),
(3, 6, '2026-01-10'),
(3, 7, '2026-01-10'),
(3, 15, '2026-01-10'),
(3, 18, '2026-01-15'),
(3, 16, '2026-01-20'),
(3, 20, '2026-01-20'),
(4, 6, '2026-01-15'),
(4, 15, '2026-01-15'),
(5, 15, '2026-01-20');

-- =====================================================
-- 20. WORKOUT PLAN ASSIGNMENTS
-- =====================================================
INSERT INTO `workoutplan_player` (`PlanID`, `PlayerID`, `AssignedDate`) VALUES
(2, 6, '2025-10-21'),
(2, 7, '2025-10-21'),
(24, 15, '2025-10-22'),
(24, 18, '2025-10-22'),
(25, 6, '2025-10-22'),
(25, 16, '2025-10-22'),
(26, 15, '2025-10-23'),
(26, 20, '2025-10-23');

-- =====================================================
-- 21. PLAYER TRAINER ASSIGNMENTS
-- =====================================================
INSERT INTO `playertrainerassignment` (`AssignmentID`, `PlayerID`, `TrainerID`, `AssignedDate`, `Status`) VALUES
(1, 6, 4, '2025-10-01 00:00:00', 'active'),
(2, 7, 4, '2025-10-01 00:00:00', 'active'),
(3, 15, 4, '2025-10-01 00:00:00', 'active'),
(4, 16, 10, '2025-10-15 00:00:00', 'active'),
(5, 18, 4, '2025-10-15 00:00:00', 'active'),
(6, 20, 10, '2025-11-01 00:00:00', 'active');

-- =====================================================
-- 22. EQUIPMENT REVIEWS
-- =====================================================
INSERT INTO `equipmentreview` (`ReviewID`, `EquipmentID`, `PlayerID`, `RentalID`, `Rating`, `ReviewTitle`, `ReviewText`, `ConditionRating`, `UsabilityRating`, `WouldRecommend`, `ReviewDate`, `Status`) VALUES
(1, 1, 7, 1, 5, 'Excellent Professional Bat', 'Top quality English willow bat. Great balance and pick-up weight. Perfect for competitive matches.', 5, 5, 1, '2026-01-18 10:00:00', 'approved'),
(2, 2, 16, 2, 4, 'Good Practice Balls', 'Leather cricket balls are well made. Seam holds up well during practice sessions.', 4, 4, 1, '2026-01-19 14:00:00', 'approved'),
(3, 3, 20, 3, 4, 'Safe and Comfortable Helmet', 'The helmet fits well and provides good protection. Visor could be slightly better quality.', 4, 4, 1, '2026-01-20 09:00:00', 'pending');

-- =====================================================
-- 23. COACH REVIEWS
-- =====================================================
INSERT INTO `coachreview` (`ReviewID`, `CoachID`, `PlayerID`, `AppointmentID`, `TeachingQuality`, `Communication`, `Punctuality`, `Knowledge`, `OverallRating`, `ReviewTitle`, `ReviewText`, `WouldRecommend`, `ReviewDate`, `Status`) VALUES
(1, 9, 6, 5, 5, 5, 5, 5, 5.0, 'Outstanding Coach', 'Coach Sarath is incredibly knowledgeable about batting techniques. His analysis of my game helped me improve my average significantly.', 1, '2026-02-11 10:00:00', 'approved'),
(2, 9, 7, 7, 4, 5, 5, 5, 4.8, 'Very Helpful Sessions', 'Great communication and always on time. Explains complex techniques in simple terms. Highly recommended.', 1, '2026-02-05 14:00:00', 'approved'),
(3, 9, 15, 6, 5, 4, 5, 5, 4.8, 'Best Bowling Coach', 'Completely transformed my bowling action. My pace increased and accuracy improved dramatically under his guidance.', 1, '2026-02-06 11:00:00', 'approved');

-- =====================================================
-- 24. PLAYER TOURNAMENT STATS
-- =====================================================
INSERT INTO `playertournamentstats` (`TournamentStatsID`, `TournamentID`, `PlayerID`, `MatchesPlayed`, `TotalRuns`, `TotalWickets`, `BattingAverage`, `BowlingAverage`, `StrikeRate`, `EconomyRate`) VALUES
(1, 4, 6, 3, 150, 3, 50.00, 23.33, 125.00, 5.83),
(2, 4, 7, 3, 97, 0, 32.33, 0.00, 110.23, 0.00),
(3, 4, 15, 3, 20, 7, 6.67, 14.29, 66.67, 4.00),
(4, 4, 18, 3, 66, 0, 22.00, 0.00, 100.00, 0.00),
(5, 5, 7, 3, 72, 0, 24.00, 0.00, 112.50, 0.00),
(6, 5, 15, 3, 15, 5, 5.00, 14.00, 60.00, 3.89),
(7, 5, 16, 3, 35, 0, 11.67, 0.00, 87.50, 0.00),
(8, 3, 6, 4, 205, 2, 51.25, 29.00, 118.50, 5.00),
(9, 3, 7, 4, 141, 0, 35.25, 0.00, 115.57, 0.00),
(10, 3, 15, 4, 18, 9, 4.50, 11.67, 56.25, 3.75),
(11, 3, 16, 4, 22, 0, 5.50, 0.00, 75.86, 0.00),
(12, 3, 18, 4, 115, 0, 28.75, 0.00, 106.48, 0.00),
(13, 3, 20, 2, 10, 2, 5.00, 16.00, 66.67, 6.40);

COMMIT;
