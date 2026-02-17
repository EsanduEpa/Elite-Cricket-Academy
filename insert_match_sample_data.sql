-- ================================================
-- Sample Match Data Insertion for crimatch table
-- ================================================
-- This file contains sample cricket match records for testing
-- Date: February 17, 2026
-- ================================================

-- Safe deletion (optional - uncomment if you want to reset)
-- DELETE FROM crimatch WHERE MatchID BETWEEN 1 AND 20;

-- ================================================
-- INSERT SAMPLE MATCHES
-- ================================================

INSERT INTO crimatch (MatchID, TournamentID, name, Date, Venue, OpponentTeam, Result, OurScore, OpponentScore)
VALUES 
-- Tournament 1: Elite League Matches
(1, 1, 'Elite League - Round 1', '2026-01-15', 'Main Cricket Ground', 'Thunder Warriors', 'win', '185/8 (20 overs)', '178/10 (19.4 overs)'),
(2, 1, 'Elite League - Round 2', '2026-01-22', 'City Sports Complex', 'Royal Knights', 'loss', '142/10 (18.2 overs)', '145/6 (20 overs)'),
(3, 1, 'Elite League - Round 3', '2026-01-29', 'Main Cricket Ground', 'Falcon Strikers', 'win', '198/5 (20 overs)', '192/8 (20 overs)'),
(4, 1, 'Elite League - Semi Final', '2026-02-05', 'Stadium Ground', 'Phoenix Blazers', 'win', '167/7 (20 overs)', '160/9 (20 overs)'),
(5, 1, 'Elite League - Final', '2026-02-12', 'Championship Stadium', 'Cobra Kings', 'loss', '155/10 (19.1 overs)', '158/6 (19.5 overs)'),

-- Tournament 2: Championship Series Matches
(6, 2, 'Championship - Group A', '2026-01-18', 'Green Park Ground', 'Eagle Warriors', 'win', '205/6 (20 overs)', '198/9 (20 overs)'),
(7, 2, 'Championship - Group B', '2026-01-25', 'Oval Cricket Ground', 'Lions Pride', 'win', '178/5 (18 overs)', '175/10 (17.4 overs)'),
(8, 2, 'Championship - Quarter Final', '2026-02-01', 'Memorial Ground', 'Dragon Fire', 'win', '189/7 (20 overs)', '182/8 (20 overs)'),
(9, 2, 'Championship - Semi Final', '2026-02-08', 'City Sports Complex', 'Viper Squad', 'loss', '165/10 (19.2 overs)', '168/7 (19.5 overs)'),

-- Tournament 3: Local Cup Matches
(10, 3, 'Local Cup - Round 1', '2026-01-20', 'Community Ground', 'Blue Tigers', 'win', '156/6 (20 overs)', '148/10 (18.3 overs)'),
(11, 3, 'Local Cup - Round 2', '2026-01-27', 'Town Cricket Field', 'Red Panthers', 'win', '172/5 (20 overs)', '168/9 (20 overs)'),
(12, 3, 'Local Cup - Final', '2026-02-03', 'Main Cricket Ground', 'Golden Hawks', 'win', '195/4 (20 overs)', '188/8 (20 overs)'),

-- Practice/Friendly Matches
(13, 1, 'Practice Match - Inter Squad', '2026-02-06', 'Practice Ground', 'Team Alpha (Internal)', 'win', '142/8 (20 overs)', '138/10 (19.5 overs)'),
(14, 1, 'Practice Match - Against Academy B', '2026-02-09', 'Training Facility', 'Academy B Team', 'win', '168/6 (20 overs)', '162/9 (20 overs)'),
(15, 2, 'Friendly - Weekend Match', '2026-02-13', 'Green Park Ground', 'Suburban Stars', 'draw', '175/8 (20 overs)', '175/7 (20 overs)'),

-- Upcoming/Pending Matches
(16, 1, 'Elite League - Round 4', '2026-02-19', 'Main Cricket Ground', 'Storm Chasers', 'pending', NULL, NULL),
(17, 1, 'Elite League - Round 5', '2026-02-22', 'Stadium Ground', 'Thunder Warriors', 'pending', NULL, NULL),
(18, 2, 'Championship - Round 3', '2026-02-25', 'City Sports Complex', 'Falcon Strikers', 'pending', NULL, NULL),
(19, 3, 'Local Cup - Semi Final', '2026-02-28', 'Memorial Ground', 'Silver Sharks', 'pending', NULL, NULL),
(20, 3, 'Local Cup - Final', '2026-03-05', 'Championship Stadium', 'TBD', 'pending', NULL, NULL);

-- ================================================
-- Verify the insertion
-- ================================================

SELECT 
    COUNT(*) as TotalMatches,
    SUM(CASE WHEN Result = 'win' THEN 1 ELSE 0 END) as Wins,
    SUM(CASE WHEN Result = 'loss' THEN 1 ELSE 0 END) as Losses,
    SUM(CASE WHEN Result = 'draw' THEN 1 ELSE 0 END) as Draws,
    SUM(CASE WHEN Result = 'pending' THEN 1 ELSE 0 END) as UpcomingMatches
FROM crimatch;

-- Display recent matches
SELECT 
    MatchID,
    name as MatchName,
    Date,
    Venue,
    OpponentTeam,
    Result,
    OurScore,
    OpponentScore
FROM crimatch
ORDER BY Date DESC
LIMIT 10;

-- ================================================
-- END OF SAMPLE DATA
-- ================================================
