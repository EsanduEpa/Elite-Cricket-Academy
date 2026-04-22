-- 2026-04-18 Recalc
-- TournamentID=61: rebuild `playertournamentstats` from `playermatchperformance`
-- and update tournament_result BestBatsman/BestBowler to match current data.
--
-- Idempotent: deletes and rebuilds stats for this tournament; awards are set from aggregates.

START TRANSACTION;

SET @TournamentID := 61;

-- Sanity: tournament
SELECT TournamentID, Name, AgeGroup, tdate, Status
FROM tournament
WHERE TournamentID = @TournamentID;

-- -----------------------------------------------------------------------------
-- 1) Rebuild playertournamentstats
-- -----------------------------------------------------------------------------
DELETE FROM playertournamentstats WHERE TournamentID = @TournamentID;

INSERT INTO playertournamentstats
  (TournamentID, PlayerID, MatchesPlayed, TotalRuns, TotalWickets, BattingAverage, BowlingAverage, StrikeRate, EconomyRate)
SELECT
  @TournamentID AS TournamentID,
  tp.PlayerID,
  COUNT(DISTINCT pmp.MatchID) AS MatchesPlayed,
  SUM(COALESCE(pmp.RunsScored,0)) AS TotalRuns,
  SUM(COALESCE(pmp.WicketsTaken,0)) AS TotalWickets,
  CASE
    WHEN COUNT(DISTINCT pmp.MatchID) = 0 THEN 0.00
    ELSE ROUND(SUM(COALESCE(pmp.RunsScored,0)) / COUNT(DISTINCT pmp.MatchID), 2)
  END AS BattingAverage,
  CASE
    WHEN SUM(COALESCE(pmp.WicketsTaken,0)) = 0 THEN 0.00
    ELSE ROUND(SUM(COALESCE(pmp.RunsConceded,0)) / SUM(COALESCE(pmp.WicketsTaken,0)), 2)
  END AS BowlingAverage,
  CASE
    WHEN SUM(COALESCE(pmp.BallsFaced,0)) = 0 THEN 0.00
    ELSE ROUND((SUM(COALESCE(pmp.RunsScored,0)) / SUM(COALESCE(pmp.BallsFaced,0))) * 100, 2)
  END AS StrikeRate,
  CASE
    WHEN SUM(COALESCE(pmp.OversBowled,0)) = 0 THEN 0.00
    ELSE ROUND(SUM(COALESCE(pmp.RunsConceded,0)) / SUM(COALESCE(pmp.OversBowled,0)), 2)
  END AS EconomyRate
FROM tournamentplayer tp
JOIN crimatch cm ON cm.TournamentID = tp.TournamentID
JOIN playermatchperformance pmp ON pmp.MatchID = cm.MatchID AND pmp.PlayerID = tp.PlayerID
WHERE tp.TournamentID = @TournamentID
GROUP BY tp.PlayerID;

-- -----------------------------------------------------------------------------
-- 2) Update awards in tournament_result based on aggregates
-- -----------------------------------------------------------------------------
UPDATE tournament_result tr
SET
  tr.BestBatsman = (
    SELECT a.PlayerID
    FROM (
      SELECT pmp.PlayerID, SUM(COALESCE(pmp.RunsScored,0)) AS TotalRuns
      FROM playermatchperformance pmp
      JOIN crimatch cm ON cm.MatchID = pmp.MatchID
      JOIN tournamentplayer tp ON tp.TournamentID = cm.TournamentID AND tp.PlayerID = pmp.PlayerID
      WHERE cm.TournamentID = @TournamentID
      GROUP BY pmp.PlayerID
      ORDER BY TotalRuns DESC, pmp.PlayerID ASC
      LIMIT 1
    ) a
  ),
  tr.BestBowler = (
    SELECT b.PlayerID
    FROM (
      SELECT pmp.PlayerID, SUM(COALESCE(pmp.WicketsTaken,0)) AS TotalWickets
      FROM playermatchperformance pmp
      JOIN crimatch cm ON cm.MatchID = pmp.MatchID
      JOIN tournamentplayer tp ON tp.TournamentID = cm.TournamentID AND tp.PlayerID = pmp.PlayerID
      WHERE cm.TournamentID = @TournamentID
      GROUP BY pmp.PlayerID
      ORDER BY TotalWickets DESC, pmp.PlayerID ASC
      LIMIT 1
    ) b
  )
WHERE tr.TournamentID = @TournamentID;

-- -----------------------------------------------------------------------------
-- 3) Verification
-- -----------------------------------------------------------------------------
SELECT
  (SELECT COUNT(*) FROM playertournamentstats WHERE TournamentID=@TournamentID) AS stats_rows,
  (SELECT COUNT(DISTINCT PlayerID) FROM tournamentplayer WHERE TournamentID=@TournamentID) AS squad_players;

SELECT TournamentID, ManOfTournament, BestBatsman, BestBowler
FROM tournament_result
WHERE TournamentID = @TournamentID;

COMMIT;
