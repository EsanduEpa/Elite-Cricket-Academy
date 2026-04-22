-- 2026-04-17 Seed Data
-- U17 Completed Tournament (end-to-end records)
--
-- Creates (idempotent):
-- - tournament (completed)
-- - tournament_join_request (approved)
-- - coach_tournament_recommendations (approved)
-- - tournamentplayer (confirmed squad)
-- - crimatch (3 matches)
-- - playermatchperformance (verified performance for each match)
-- - tournament_result
-- - playeroverallstats (recalculated for affected players)
--
-- Safe to re-run: uses deterministic lookups + NOT EXISTS / ON DUPLICATE KEY.

START TRANSACTION;

SET @SeedTournamentName := 'U17 April Completed Seed Tournament';
SET @SeedTournamentDate := '2026-04-10';
SET @SeedAgeGroup := 'Under 17';

-- Actors (must exist in `user` table)
SET @AdminUserID := 1;
SET @HeadCoachUserID := 14;
SET @CoachUserID := 3;

-- Squad (must exist and be Role='Player')
SET @P1 := 15; -- REQUIRED: include this player in each match
SET @P2 := 6;
SET @P3 := 7;
SET @P4 := 16;
SET @P5 := 18;
SET @P6 := 20;

-- -----------------------------------------------------------------------------
-- 1) Tournament (completed)
-- -----------------------------------------------------------------------------
SET @TournamentID := (
  SELECT t.TournamentID
  FROM tournament t
  WHERE t.Name = @SeedTournamentName AND t.tdate = @SeedTournamentDate
  LIMIT 1
);

INSERT INTO tournament
  (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status, IsTeamAnnounced, CancelReason)
SELECT
  @SeedTournamentName,
  @SeedAgeGroup,
  'ODI',
  'Seeded completed Under-17 tournament with matches, squad, results, and performance records.',
  @SeedTournamentDate,
  DATE_SUB(@SeedTournamentDate, INTERVAL 1 MONTH),
  18,
  'Elite Ground 1',
  10000.00,
  @AdminUserID,
  'completed',
  1,
  NULL
WHERE @TournamentID IS NULL;

SET @TournamentID := COALESCE(@TournamentID, LAST_INSERT_ID());

-- -----------------------------------------------------------------------------
-- 2) Join requests (approved) + coach recommendations (approved)
-- -----------------------------------------------------------------------------
INSERT INTO tournament_join_request
  (TournamentID, PlayerID, Message, Status, ReviewedBy, ReviewNotes, RequestedAt, ReviewedAt)
VALUES
  (@TournamentID, @P1, 'Request to join (seed).', 'approved', @AdminUserID, 'Approved (seed).', '2026-03-05 09:00:00', '2026-03-06 10:00:00'),
  (@TournamentID, @P2, 'Request to join (seed).', 'approved', @AdminUserID, 'Approved (seed).', '2026-03-05 09:05:00', '2026-03-06 10:05:00'),
  (@TournamentID, @P3, 'Request to join (seed).', 'approved', @AdminUserID, 'Approved (seed).', '2026-03-05 09:10:00', '2026-03-06 10:10:00'),
  (@TournamentID, @P4, 'Request to join (seed).', 'approved', @AdminUserID, 'Approved (seed).', '2026-03-05 09:15:00', '2026-03-06 10:15:00'),
  (@TournamentID, @P5, 'Request to join (seed).', 'approved', @AdminUserID, 'Approved (seed).', '2026-03-05 09:20:00', '2026-03-06 10:20:00'),
  (@TournamentID, @P6, 'Request to join (seed).', 'approved', @AdminUserID, 'Approved (seed).', '2026-03-05 09:25:00', '2026-03-06 10:25:00')
ON DUPLICATE KEY UPDATE
  Status = VALUES(Status),
  ReviewedBy = VALUES(ReviewedBy),
  ReviewNotes = VALUES(ReviewNotes),
  ReviewedAt = VALUES(ReviewedAt);

INSERT INTO coach_tournament_recommendations
  (CoachID, TournamentID, PlayerID, RecommendedRole, Reason, Comments, Status, ReviewedBy, ReviewFeedback, DateRecommended, DateReviewed)
VALUES
  (@CoachUserID, @TournamentID, @P1, 'All-Rounder', 'Strong recent performance (seed).', 'Include in squad (seed).', 'approved', @HeadCoachUserID, 'Approved (seed).', '2026-03-08 11:00:00', '2026-03-09 12:00:00'),
  (@CoachUserID, @TournamentID, @P2, 'Batsman',     'Consistent opener (seed).',        'Include in squad (seed).', 'approved', @HeadCoachUserID, 'Approved (seed).', '2026-03-08 11:05:00', '2026-03-09 12:05:00'),
  (@CoachUserID, @TournamentID, @P3, 'Bowler',      'Wicket-taking bowler (seed).',     'Include in squad (seed).', 'approved', @HeadCoachUserID, 'Approved (seed).', '2026-03-08 11:10:00', '2026-03-09 12:10:00'),
  (@CoachUserID, @TournamentID, @P4, 'Wicket-Keeper','Safe hands (seed).',              'Include in squad (seed).', 'approved', @HeadCoachUserID, 'Approved (seed).', '2026-03-08 11:15:00', '2026-03-09 12:15:00'),
  (@CoachUserID, @TournamentID, @P5, 'Batsman',     'Middle-order stability (seed).',   'Include in squad (seed).', 'approved', @HeadCoachUserID, 'Approved (seed).', '2026-03-08 11:20:00', '2026-03-09 12:20:00'),
  (@CoachUserID, @TournamentID, @P6, 'Bowler',      'Economical spells (seed).',        'Include in squad (seed).', 'approved', @HeadCoachUserID, 'Approved (seed).', '2026-03-08 11:25:00', '2026-03-09 12:25:00')
ON DUPLICATE KEY UPDATE
  RecommendedRole = VALUES(RecommendedRole),
  Reason = VALUES(Reason),
  Comments = VALUES(Comments),
  Status = VALUES(Status),
  ReviewedBy = VALUES(ReviewedBy),
  ReviewFeedback = VALUES(ReviewFeedback),
  DateReviewed = VALUES(DateReviewed);

-- -----------------------------------------------------------------------------
-- 3) Squad (tournamentplayer) — confirmed, announced >= 2 weeks before tdate
-- -----------------------------------------------------------------------------
INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT @TournamentID, @P1, 'Elite U17', 'Captain',       @HeadCoachUserID, 'confirmed', '2026-03-27 18:00:00'
WHERE NOT EXISTS (SELECT 1 FROM tournamentplayer WHERE TournamentID=@TournamentID AND PlayerID=@P1);

INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT @TournamentID, @P2, 'Elite U17', 'Batsman',       @HeadCoachUserID, 'confirmed', '2026-03-27 18:00:00'
WHERE NOT EXISTS (SELECT 1 FROM tournamentplayer WHERE TournamentID=@TournamentID AND PlayerID=@P2);

INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT @TournamentID, @P3, 'Elite U17', 'Bowler',        @HeadCoachUserID, 'confirmed', '2026-03-27 18:00:00'
WHERE NOT EXISTS (SELECT 1 FROM tournamentplayer WHERE TournamentID=@TournamentID AND PlayerID=@P3);

INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT @TournamentID, @P4, 'Elite U17', 'Wicket-Keeper', @HeadCoachUserID, 'confirmed', '2026-03-27 18:00:00'
WHERE NOT EXISTS (SELECT 1 FROM tournamentplayer WHERE TournamentID=@TournamentID AND PlayerID=@P4);

INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT @TournamentID, @P5, 'Elite U17', 'Batsman',       @HeadCoachUserID, 'confirmed', '2026-03-27 18:00:00'
WHERE NOT EXISTS (SELECT 1 FROM tournamentplayer WHERE TournamentID=@TournamentID AND PlayerID=@P5);

INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT @TournamentID, @P6, 'Elite U17', 'Bowler',        @HeadCoachUserID, 'confirmed', '2026-03-27 18:00:00'
WHERE NOT EXISTS (SELECT 1 FROM tournamentplayer WHERE TournamentID=@TournamentID AND PlayerID=@P6);

-- -----------------------------------------------------------------------------
-- 4) Matches (crimatch) — one tournament has multiple matches
-- -----------------------------------------------------------------------------
-- Match 1
SET @M1Name := 'U17 Seed Match 1';
SET @M1Date := '2026-04-09';
SET @Match1ID := (
  SELECT cm.MatchID FROM crimatch cm
  WHERE cm.TournamentID=@TournamentID AND cm.Name=@M1Name AND cm.Date=@M1Date
  LIMIT 1
);

INSERT INTO crimatch
  (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT
  @TournamentID, @M1Name, @M1Date,
  'Elite Ground 1',
  'Colombo Juniors U17',
  'win',
  25, 'runs',
  210, 8,
  185, 10,
  0,
  'Won by 25 runs (seed).'
WHERE @Match1ID IS NULL;

SET @Match1ID := COALESCE(@Match1ID, LAST_INSERT_ID());

-- Match 2
SET @M2Name := 'U17 Seed Match 2';
SET @M2Date := '2026-04-10';
SET @Match2ID := (
  SELECT cm.MatchID FROM crimatch cm
  WHERE cm.TournamentID=@TournamentID AND cm.Name=@M2Name AND cm.Date=@M2Date
  LIMIT 1
);

INSERT INTO crimatch
  (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT
  @TournamentID, @M2Name, @M2Date,
  'Elite Ground 1',
  'Kandy Juniors U17',
  'loss',
  3, 'wickets',
  198, 10,
  199, 7,
  0,
  'Lost by 3 wickets (seed).'
WHERE @Match2ID IS NULL;

SET @Match2ID := COALESCE(@Match2ID, LAST_INSERT_ID());

-- Match 3 (Final)
SET @M3Name := 'U17 Seed Final';
SET @M3Date := '2026-04-10';
SET @Match3ID := (
  SELECT cm.MatchID FROM crimatch cm
  WHERE cm.TournamentID=@TournamentID AND cm.Name=@M3Name AND cm.Date=@M3Date
  LIMIT 1
);

INSERT INTO crimatch
  (TournamentID, Name, Date, Venue, OpponentTeam, Result, MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets, IsDLS, SummaryNotes)
SELECT
  @TournamentID, @M3Name, @M3Date,
  'Elite Ground 1',
  'Galle Juniors U17',
  'win',
  2, 'wickets',
  175, 9,
  174, 10,
  0,
  'Won final by 2 wickets (seed).'
WHERE @Match3ID IS NULL;

SET @Match3ID := COALESCE(@Match3ID, LAST_INSERT_ID());

-- -----------------------------------------------------------------------------
-- 5) Ensure playerprofile rows exist for squad players
--    (FK requirement: playermatchperformance.PlayerID -> playerprofile.PlayerID)
-- -----------------------------------------------------------------------------
INSERT INTO playerprofile (PlayerID)
SELECT @P1
WHERE NOT EXISTS (SELECT 1 FROM playerprofile WHERE PlayerID = @P1);

INSERT INTO playerprofile (PlayerID)
SELECT @P2
WHERE NOT EXISTS (SELECT 1 FROM playerprofile WHERE PlayerID = @P2);

INSERT INTO playerprofile (PlayerID)
SELECT @P3
WHERE NOT EXISTS (SELECT 1 FROM playerprofile WHERE PlayerID = @P3);

INSERT INTO playerprofile (PlayerID)
SELECT @P4
WHERE NOT EXISTS (SELECT 1 FROM playerprofile WHERE PlayerID = @P4);

INSERT INTO playerprofile (PlayerID)
SELECT @P5
WHERE NOT EXISTS (SELECT 1 FROM playerprofile WHERE PlayerID = @P5);

INSERT INTO playerprofile (PlayerID)
SELECT @P6
WHERE NOT EXISTS (SELECT 1 FROM playerprofile WHERE PlayerID = @P6);

-- -----------------------------------------------------------------------------
-- 6) Match performances (playermatchperformance)
--    NOTE: coaches/player dashboards use VerifiedStatus='verified' for history.
-- -----------------------------------------------------------------------------
-- Match 1: ensure PlayerID=15 exists
INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match1ID, @P1, 45, 38, 2, 5.0, 28, 1, 0, 8.6,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:00:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match1ID AND PlayerID=@P1);

INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match1ID, @P2, 62, 55, 0, 0.0, 0, 0, 0, 7.8,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:00:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match1ID AND PlayerID=@P2);

INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match1ID, @P3, 10, 12, 3, 7.0, 35, 0, 0, 7.6,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:00:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match1ID AND PlayerID=@P3);

-- Match 2
INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match2ID, @P1, 12, 18, 1, 6.0, 40, 0, 0, 5.9,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:05:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match2ID AND PlayerID=@P1);

INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match2ID, @P4, 25, 22, 0, 0.0, 0, 1, 1, 7.2,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:05:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match2ID AND PlayerID=@P4);

INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match2ID, @P6, 5, 9, 2, 8.0, 34, 0, 0, 7.1,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:05:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match2ID AND PlayerID=@P6);

-- Match 3 (Final)
INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match3ID, @P1, 67, 60, 2, 4.0, 18, 1, 0, 9.1,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:10:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match3ID AND PlayerID=@P1);

INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match3ID, @P5, 44, 39, 0, 0.0, 0, 0, 0, 7.4,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:10:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match3ID AND PlayerID=@P5);

INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating,
   VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
SELECT
  @Match3ID, @P3, 8, 10, 4, 6.0, 27, 0, 0, 8.3,
  'verified', @CoachUserID, @AdminUserID, '2026-04-11 10:10:00'
WHERE NOT EXISTS (SELECT 1 FROM playermatchperformance WHERE MatchID=@Match3ID AND PlayerID=@P3);

-- -----------------------------------------------------------------------------
-- 7) Tournament result
-- -----------------------------------------------------------------------------
INSERT INTO tournament_result
  (TournamentID, Position, TotalMatchesPlayed, TotalWins, TotalLosses, ManOfTournament, BestBatsman, BestBowler, SummaryNotes, EnteredBy)
VALUES
  (@TournamentID, 'champions', 3, 2, 1, @P1, @P1, @P3,
   'Seed result: champions with 3 matches played; includes full squad + verified performances.',
   @AdminUserID)
ON DUPLICATE KEY UPDATE
  Position = VALUES(Position),
  TotalMatchesPlayed = VALUES(TotalMatchesPlayed),
  TotalWins = VALUES(TotalWins),
  TotalLosses = VALUES(TotalLosses),
  ManOfTournament = VALUES(ManOfTournament),
  BestBatsman = VALUES(BestBatsman),
  BestBowler = VALUES(BestBowler),
  SummaryNotes = VALUES(SummaryNotes),
  EnteredBy = VALUES(EnteredBy);

-- -----------------------------------------------------------------------------
-- 8) Recalculate playeroverallstats for affected players
--    Mirrors app/models/M_Performance.php::calculateOverallStats()
-- -----------------------------------------------------------------------------
INSERT INTO playeroverallstats
  (PlayerID, MatchesPlayed, TotalRuns, TotalWickets, HighestScore,
   BattingAverage, BowlingAverage, StrikeRate, EconomyRate,
   Centuries, HalfCenturies, FiveWickets, FourWickets, BestBowling)
SELECT
  p.PlayerID,
  p.MatchesPlayed,
  p.TotalRuns,
  p.Wickets,
  p.HighestScore,
  CASE WHEN p.MatchesPlayed > 0 THEN ROUND(p.TotalRuns / p.MatchesPlayed, 2) ELSE 0 END AS BattingAverage,
  CASE WHEN p.Wickets > 0 THEN ROUND(p.TotalRunsConceded / p.Wickets, 2) ELSE 0 END AS BowlingAverage,
  CASE WHEN p.TotalBalls > 0 THEN ROUND((p.TotalRuns / p.TotalBalls) * 100, 2) ELSE 0 END AS StrikeRate,
  CASE WHEN p.TotalOvers > 0 THEN ROUND(p.TotalRunsConceded / p.TotalOvers, 2) ELSE 0 END AS EconomyRate,
  p.Centuries,
  p.HalfCenturies,
  p.FiveWickets,
  p.FourWickets,
  p.BestBowling
FROM (
  SELECT
    pmp.PlayerID,
    COUNT(*) AS MatchesPlayed,
    COALESCE(SUM(pmp.RunsScored), 0) AS TotalRuns,
    COALESCE(SUM(pmp.BallsFaced), 0) AS TotalBalls,
    COALESCE(MAX(pmp.RunsScored), 0) AS HighestScore,
    COALESCE(SUM(pmp.WicketsTaken), 0) AS Wickets,
    COALESCE(SUM(pmp.OversBowled), 0) AS TotalOvers,
    COALESCE(SUM(pmp.RunsConceded), 0) AS TotalRunsConceded,
    COALESCE(SUM(CASE WHEN pmp.RunsScored >= 100 THEN 1 ELSE 0 END), 0) AS Centuries,
    COALESCE(SUM(CASE WHEN pmp.RunsScored >= 50 AND pmp.RunsScored < 100 THEN 1 ELSE 0 END), 0) AS HalfCenturies,
    COALESCE(SUM(CASE WHEN pmp.WicketsTaken >= 5 THEN 1 ELSE 0 END), 0) AS FiveWickets,
    COALESCE(SUM(CASE WHEN pmp.WicketsTaken >= 4 THEN 1 ELSE 0 END), 0) AS FourWickets,
    SUBSTRING_INDEX(
      GROUP_CONCAT(CONCAT(pmp.WicketsTaken, '/', pmp.RunsConceded) ORDER BY pmp.WicketsTaken DESC, pmp.RunsConceded ASC SEPARATOR ','),
      ',',
      1
    ) AS BestBowling
  FROM playermatchperformance pmp
  WHERE pmp.PlayerID IN (@P1, @P2, @P3, @P4, @P5, @P6)
  GROUP BY pmp.PlayerID
) p
ON DUPLICATE KEY UPDATE
  MatchesPlayed = VALUES(MatchesPlayed),
  TotalRuns = VALUES(TotalRuns),
  TotalWickets = VALUES(TotalWickets),
  HighestScore = VALUES(HighestScore),
  BattingAverage = VALUES(BattingAverage),
  BowlingAverage = VALUES(BowlingAverage),
  StrikeRate = VALUES(StrikeRate),
  EconomyRate = VALUES(EconomyRate),
  Centuries = VALUES(Centuries),
  HalfCenturies = VALUES(HalfCenturies),
  FiveWickets = VALUES(FiveWickets),
  FourWickets = VALUES(FourWickets),
  BestBowling = VALUES(BestBowling);

COMMIT;
