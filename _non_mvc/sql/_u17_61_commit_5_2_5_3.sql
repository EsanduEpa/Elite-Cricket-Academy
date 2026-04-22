-- Commit runner: applies 5.2 + 5.3 and COMMITs.
-- Database: cricket_academy

START TRANSACTION;

SET @TournamentID := 61;
SET @SelectedBy := 14;

-- 5.2 Insert missing tournamentplayer rows for ALL U17-assigned players
INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT
  @TournamentID,
  u17.PlayerID,
  'Elite U17',
  'Batsman' AS RoleInTeam,          -- placeholder
  @SelectedBy,
  'confirmed',
  NOW()
FROM (
  SELECT DISTINCT PlayerID
  FROM player_skill_coach_assignment
  WHERE AgeGroup='Under 17'
) u17
WHERE NOT EXISTS (
  SELECT 1
  FROM tournamentplayer tp
  WHERE tp.TournamentID=@TournamentID
    AND tp.PlayerID=u17.PlayerID
);

-- 5.3 Ensure every tournamentplayer has a playermatchperformance row for matches 29/30/31 (zeros by default)
INSERT INTO playermatchperformance
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating)
SELECT
  cm.MatchID,
  tp.PlayerID,
  0,0,0,0.0,0,0,0,0.0
FROM crimatch cm
JOIN tournamentplayer tp ON tp.TournamentID = cm.TournamentID
WHERE cm.TournamentID = @TournamentID
  AND cm.MatchID IN (29,30,31)
  AND NOT EXISTS (
    SELECT 1
    FROM playermatchperformance p
    WHERE p.MatchID = cm.MatchID
      AND p.PlayerID = tp.PlayerID
  );

COMMIT;

-- Quick summary after commit
SELECT
  (SELECT COUNT(*) FROM tournamentplayer WHERE TournamentID=61) AS TournamentPlayerRows,
  (SELECT COUNT(*) FROM playermatchperformance WHERE MatchID IN (29,30,31)) AS PerformanceRows_29_30_31;
