-- ================================================
-- Complete Tournament and Match Data Insertion
-- Simple Format with INSERT VALUES
-- ================================================
-- Run this script to populate both tournament and crimatch tables
-- Date: February 17, 2026
-- ================================================

-- ================================================
-- STEP 1: INSERT TOURNAMENTS (Parent Table)
-- ================================================

INSERT INTO tournament (TournamentID, Name, tdate, Location, CreatedBy, Status, PrizePool)
VALUES 
(1, 'Elite League Championship 2026', '2026-01-15', 'Main Cricket Stadium, City Center', 1, 'ongoing', 50000.00),
(2, 'Inter-Academy Championship Series', '2026-01-18', 'National Sports Complex', 1, 'ongoing', 75000.00),
(3, 'Local Cricket Cup 2026', '2026-01-20', 'Community Cricket Ground', 1, 'completed', 15000.00),
(4, 'Summer Cricket Festival 2026', '2026-03-10', 'Green Valley Sports Arena', 1, 'upcoming', 100000.00),
(5, 'Junior Cricket Excellence Cup', '2026-02-20', 'Youth Cricket Academy Grounds', 1, 'upcoming', 25000.00);

-- ================================================
-- STEP 2: INSERT MATCHES (Child Table)
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
-- VERIFICATION QUERIES
-- ================================================

-- Display tournament summary
SELECT 
    COUNT(*) as TotalTournaments,
    SUM(CASE WHEN Status = 'upcoming' THEN 1 ELSE 0 END) as UpcomingTournaments,
    SUM(CASE WHEN Status = 'ongoing' THEN 1 ELSE 0 END) as OngoingTournaments,
    SUM(CASE WHEN Status = 'completed' THEN 1 ELSE 0 END) as CompletedTournaments,
    SUM(PrizePool) as TotalPrizePool
FROM tournament;

-- Display match summary
SELECT 
    COUNT(*) as TotalMatches,
    SUM(CASE WHEN Result = 'win' THEN 1 ELSE 0 END) as Wins,
    SUM(CASE WHEN Result = 'loss' THEN 1 ELSE 0 END) as Losses,
    SUM(CASE WHEN Result = 'draw' THEN 1 ELSE 0 END) as Draws,
    SUM(CASE WHEN Result = 'pending' THEN 1 ELSE 0 END) as PendingMatches
FROM crimatch;

-- Display relationships
SELECT 
    t.TournamentID,
    t.Name as TournamentName,
    t.Status as TournamentStatus,
    COUNT(m.MatchID) as TotalMatches,
    SUM(CASE WHEN m.Result = 'win' THEN 1 ELSE 0 END) as Wins,
    SUM(CASE WHEN m.Result = 'loss' THEN 1 ELSE 0 END) as Losses,
    SUM(CASE WHEN m.Result = 'pending' THEN 1 ELSE 0 END) as UpcomingMatches
FROM tournament t
LEFT JOIN crimatch m ON t.TournamentID = m.TournamentID
GROUP BY t.TournamentID, t.Name, t.Status
ORDER BY t.tdate DESC;

-- ================================================
-- END OF SCRIPT
-- ================================================
