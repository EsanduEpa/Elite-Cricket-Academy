-- Safe runner: applies 5.2 + 5.3, validates match tallies, then ROLLBACKs.
-- Database: cricket_academy
-- Assumes: TournamentID=61, matches 29/30/31.

-- Make output easier to read
\! echo "===== BEGIN U17 61: 5.2+5.3 + VALIDATIONS (ROLLBACK) ====="

START TRANSACTION;

SET @TournamentID := 61;
SET @SelectedBy := 14;

\! echo "----- Snapshot BEFORE -----"
SELECT
  (SELECT COUNT(DISTINCT PlayerID)
   FROM player_skill_coach_assignment
   WHERE AgeGroup='Under 17') AS U17_AssignedPlayers,
  (SELECT COUNT(*)
   FROM tournamentplayer
   WHERE TournamentID=@TournamentID) AS TournamentPlayerRows_Before,
  (SELECT COUNT(*)
   FROM playermatchperformance
   WHERE MatchID IN (29,30,31)) AS PerformanceRows_29_30_31_Before;

-- Track what was already there
DROP TEMPORARY TABLE IF EXISTS tmp_tp_before;
CREATE TEMPORARY TABLE tmp_tp_before (
  PlayerID INT PRIMARY KEY
) ENGINE=MEMORY
AS
SELECT tp.PlayerID
FROM tournamentplayer tp
WHERE tp.TournamentID=@TournamentID;

DROP TEMPORARY TABLE IF EXISTS tmp_pmp_before;
CREATE TEMPORARY TABLE tmp_pmp_before (
  MatchID INT NOT NULL,
  PlayerID INT NOT NULL,
  PRIMARY KEY (MatchID, PlayerID)
) ENGINE=MEMORY
AS
SELECT p.MatchID, p.PlayerID
FROM playermatchperformance p
WHERE p.MatchID IN (29,30,31);

\! echo "----- Step 5.2: Insert missing tournamentplayer rows -----"
INSERT INTO tournamentplayer
  (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
SELECT
  @TournamentID,
  u17.PlayerID,
  'Elite U17',
  'Batsman' AS RoleInTeam,
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

SELECT ROW_COUNT() AS InsertedTournamentPlayerRows;

\! echo "New tournamentplayer rows inserted (this run):"
SELECT tp.TournamentPlayerID,
       tp.PlayerID,
       CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
       tp.Team,
       tp.RoleInTeam,
       tp.SelectedBy,
       tp.SelectionStatus,
       tp.SelectedAt
FROM tournamentplayer tp
JOIN user u ON u.UserID = tp.PlayerID
LEFT JOIN tmp_tp_before b ON b.PlayerID = tp.PlayerID
WHERE tp.TournamentID=@TournamentID
  AND b.PlayerID IS NULL
ORDER BY tp.PlayerID;

\! echo "----- Step 5.3: Insert missing playermatchperformance rows (zeros) -----"
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

SELECT ROW_COUNT() AS InsertedPerformanceRows;

\! echo "New playermatchperformance rows inserted (this run):"
SELECT p.MatchID,
       p.PlayerID,
       CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
       p.RunsScored, p.BallsFaced, p.WicketsTaken, p.OversBowled, p.RunsConceded, p.Catches, p.Stumpings, p.Rating
FROM playermatchperformance p
JOIN user u ON u.UserID = p.PlayerID
LEFT JOIN tmp_pmp_before b ON b.MatchID=p.MatchID AND b.PlayerID=p.PlayerID
WHERE p.MatchID IN (29,30,31)
  AND b.PlayerID IS NULL
ORDER BY p.MatchID, p.PlayerID;

\! echo "----- VALIDATION 1: Per-match totals vs crimatch (matches 29/30/31) -----"
SELECT
  cm.MatchID,
  cm.Name,
  cm.OurRuns,
  COALESCE(SUM(pmp.RunsScored),0) AS SumRunsScored,
  (COALESCE(SUM(pmp.RunsScored),0) - cm.OurRuns) AS RunsDiff,

  cm.OpponentWickets,
  COALESCE(SUM(pmp.WicketsTaken),0) AS SumWicketsTaken,
  (COALESCE(SUM(pmp.WicketsTaken),0) - cm.OpponentWickets) AS WicketsDiff,

  cm.OurWickets,
  COALESCE(SUM(CASE WHEN pmp.RunsScored > 0 THEN 1 ELSE 0 END),0) AS PlayersWithRuns,
  (COALESCE(SUM(CASE WHEN pmp.RunsScored > 0 THEN 1 ELSE 0 END),0) - cm.OurWickets) AS DiffPlayersWithRunsMinusOurWickets
FROM crimatch cm
LEFT JOIN playermatchperformance pmp
  ON pmp.MatchID = cm.MatchID
WHERE cm.TournamentID = @TournamentID
  AND cm.MatchID IN (29,30,31)
GROUP BY cm.MatchID
ORDER BY cm.MatchID;

\! echo "----- VALIDATION 2: Wicket-takers must be Bowler (matches 29/30/31) -----"
SELECT
  pmp.MatchID,
  pmp.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
  tp.RoleInTeam,
  pmp.WicketsTaken
FROM playermatchperformance pmp
JOIN crimatch cm ON cm.MatchID = pmp.MatchID
LEFT JOIN tournamentplayer tp
  ON tp.TournamentID = cm.TournamentID
 AND tp.PlayerID     = pmp.PlayerID
JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID=@TournamentID
  AND pmp.MatchID IN (29,30,31)
  AND pmp.WicketsTaken > 0
  AND (tp.RoleInTeam IS NULL OR tp.RoleInTeam <> 'Bowler')
ORDER BY pmp.MatchID, pmp.WicketsTaken DESC, pmp.PlayerID;

\! echo "----- Snapshot AFTER (still inside transaction) -----"
SELECT
  (SELECT COUNT(*)
   FROM tournamentplayer
   WHERE TournamentID=@TournamentID) AS TournamentPlayerRows_After,
  (SELECT COUNT(*)
   FROM playermatchperformance
   WHERE MatchID IN (29,30,31)) AS PerformanceRows_29_30_31_After;

\! echo "----- ROLLBACK (no changes persisted) -----"
ROLLBACK;

\! echo "===== END (ROLLED BACK) ====="
