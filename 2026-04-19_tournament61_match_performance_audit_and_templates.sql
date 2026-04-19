-- 2026-04-19
-- Tournament 61 Match Performance Audit + Update Templates
--
-- Purpose:
-- 1) Show whether match totals (crimatch.OurRuns / crimatch.OpponentWickets) tally
--    with player-entered rows in playermatchperformance.
-- 2) List which player rows look "not filled" yet (all zeros) so they can be completed.
-- 3) Generate ready-to-edit UPDATE statement templates per player per match.
--
-- Notes:
-- - Team runs will often be higher than sum(batsman runs) due to extras.
-- - Team wickets lost (OpponentWickets) will not always equal sum(bowler wickets)
--   due to run outs / retired hurt / etc. But very large gaps usually mean missing entries.
--
-- Scope: TournamentID = 61

-- -----------------------------------------------------------------------------
-- A) Per-match reconciliation (ALL performance rows)
-- -----------------------------------------------------------------------------
SELECT
  cm.MatchID,
  cm.Date,
  cm.Name AS MatchName,
  cm.OurRuns,
  COALESCE(SUM(pmp.RunsScored), 0) AS SumPlayerRuns,
  (cm.OurRuns - COALESCE(SUM(pmp.RunsScored), 0)) AS RunsDiff,
  cm.OpponentWickets,
  COALESCE(SUM(pmp.WicketsTaken), 0) AS SumPlayerWickets,
  (cm.OpponentWickets - COALESCE(SUM(pmp.WicketsTaken), 0)) AS WicketsDiff,
  COUNT(pmp.PlayerID) AS PerfRows,
  SUM(pmp.RunsScored > 0) AS PlayersWithRuns,
  SUM(pmp.WicketsTaken > 0) AS PlayersWithWickets
FROM crimatch cm
LEFT JOIN playermatchperformance pmp
  ON pmp.MatchID = cm.MatchID
WHERE cm.TournamentID = 61
GROUP BY cm.MatchID, cm.Date, cm.Name, cm.OurRuns, cm.OpponentWickets
ORDER BY cm.Date, cm.MatchID;

-- -----------------------------------------------------------------------------
-- B) Find "not filled" rows (all key stats are zero)
-- -----------------------------------------------------------------------------
SELECT
  pmp.MatchID,
  cm.Name AS MatchName,
  pmp.PlayerID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  pmp.VerifiedStatus,
  pmp.RunsScored,
  pmp.BallsFaced,
  pmp.WicketsTaken,
  pmp.OversBowled,
  pmp.RunsConceded
FROM playermatchperformance pmp
JOIN crimatch cm ON cm.MatchID = pmp.MatchID
LEFT JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID = 61
  AND COALESCE(pmp.RunsScored, 0) = 0
  AND COALESCE(pmp.BallsFaced, 0) = 0
  AND COALESCE(pmp.WicketsTaken, 0) = 0
  AND COALESCE(pmp.OversBowled, 0) = 0
  AND COALESCE(pmp.RunsConceded, 0) = 0
ORDER BY pmp.MatchID, PlayerName;

-- -----------------------------------------------------------------------------
-- C) UPDATE templates (edit the ??? values, then run the output)
-- -----------------------------------------------------------------------------
-- This generates one UPDATE statement per player row.
-- Edit the placeholders (???), copy the statements, and run them.
SELECT
  CONCAT(
    'UPDATE playermatchperformance SET ',
    'RunsScored=???',
    ', BallsFaced=???',
    ', WicketsTaken=???',
    ', OversBowled=???',
    ', RunsConceded=???',
    ', Catches=???',
    ', Stumpings=???',
    ', Rating=???',
    ' WHERE MatchID=', pmp.MatchID,
    ' AND PlayerID=', pmp.PlayerID,
    '; -- ', CONCAT(u.FirstName, ' ', u.LastName), ' / ', cm.Name,
    ' / status=', pmp.VerifiedStatus
  ) AS UpdateTemplate
FROM playermatchperformance pmp
JOIN crimatch cm ON cm.MatchID = pmp.MatchID
LEFT JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID = 61
ORDER BY pmp.MatchID, u.FirstName, u.LastName;

-- -----------------------------------------------------------------------------
-- D) Re-run reconciliation using VERIFIED only
-- -----------------------------------------------------------------------------
SELECT
  cm.MatchID,
  cm.Date,
  cm.Name AS MatchName,
  cm.OurRuns,
  COALESCE(SUM(CASE WHEN pmp.VerifiedStatus='verified' THEN pmp.RunsScored ELSE 0 END), 0) AS SumVerifiedPlayerRuns,
  (cm.OurRuns - COALESCE(SUM(CASE WHEN pmp.VerifiedStatus='verified' THEN pmp.RunsScored ELSE 0 END), 0)) AS RunsDiff_Verified,
  cm.OpponentWickets,
  COALESCE(SUM(CASE WHEN pmp.VerifiedStatus='verified' THEN pmp.WicketsTaken ELSE 0 END), 0) AS SumVerifiedPlayerWickets,
  (cm.OpponentWickets - COALESCE(SUM(CASE WHEN pmp.VerifiedStatus='verified' THEN pmp.WicketsTaken ELSE 0 END), 0)) AS WicketsDiff_Verified,
  SUM(pmp.VerifiedStatus='verified') AS VerifiedRows,
  COUNT(pmp.PlayerID) AS TotalRows
FROM crimatch cm
LEFT JOIN playermatchperformance pmp
  ON pmp.MatchID = cm.MatchID
WHERE cm.TournamentID = 61
GROUP BY cm.MatchID, cm.Date, cm.Name, cm.OurRuns, cm.OpponentWickets
ORDER BY cm.Date, cm.MatchID;

-- -----------------------------------------------------------------------------
-- E) Data-quality flags (helps you find partially-filled rows)
-- -----------------------------------------------------------------------------

-- E1) Batting inconsistencies (runs but 0 balls)
SELECT
  pmp.MatchID,
  cm.Name AS MatchName,
  pmp.PlayerID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  pmp.VerifiedStatus,
  pmp.RunsScored,
  pmp.BallsFaced
FROM playermatchperformance pmp
JOIN crimatch cm ON cm.MatchID = pmp.MatchID
LEFT JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID = 61
  AND COALESCE(pmp.RunsScored, 0) > 0
  AND COALESCE(pmp.BallsFaced, 0) = 0
ORDER BY pmp.MatchID, PlayerName;

-- E2) Bowling inconsistencies (wickets but 0 overs OR wickets but 0 runs conceded)
SELECT
  pmp.MatchID,
  cm.Name AS MatchName,
  pmp.PlayerID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  pmp.VerifiedStatus,
  pmp.WicketsTaken,
  pmp.OversBowled,
  pmp.RunsConceded
FROM playermatchperformance pmp
JOIN crimatch cm ON cm.MatchID = pmp.MatchID
LEFT JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID = 61
  AND COALESCE(pmp.WicketsTaken, 0) > 0
  AND (
    COALESCE(pmp.OversBowled, 0) = 0
    OR COALESCE(pmp.RunsConceded, 0) = 0
  )
ORDER BY pmp.MatchID, PlayerName;
