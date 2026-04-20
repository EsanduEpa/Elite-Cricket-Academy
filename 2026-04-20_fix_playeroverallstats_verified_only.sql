-- 2026-04-20 Fix
-- Enforce rule: playeroverallstats should exist ONLY for players
-- with at least one VERIFIED playermatchperformance record.
--
-- This script:
-- 1) Drops the trigger that pre-creates blank overall stats rows (source of 0 rows)
-- 2) Cleans invalid overall stats rows
-- 3) Rebuilds overall stats from VERIFIED performance rows only
--
-- Safe to re-run.

START TRANSACTION;

-- 1) Stop creating blank (zero) overall stats rows for every new player
DROP TRIGGER IF EXISTS tr_create_player_stats;

-- 2) Remove rows for players with no verified performance OR empty rows
DELETE pos
FROM playeroverallstats pos
LEFT JOIN (
  SELECT PlayerID
  FROM playermatchperformance
  WHERE VerifiedStatus = 'verified'
  GROUP BY PlayerID
) v ON v.PlayerID = pos.PlayerID
WHERE v.PlayerID IS NULL
   OR COALESCE(pos.MatchesPlayed, 0) = 0;

-- 3) Rebuild from verified-only records
INSERT INTO playeroverallstats
(
  PlayerID,
  MatchesPlayed,
  TotalRuns,
  TotalWickets,
  HighestScore,
  BattingAverage,
  BowlingAverage,
  StrikeRate,
  EconomyRate,
  Centuries,
  HalfCenturies,
  FiveWickets,
  FourWickets,
  BestBowling
)
SELECT
  agg.PlayerID,
  agg.MatchesPlayed,
  agg.TotalRuns,
  agg.TotalWickets,
  agg.HighestScore,
  ROUND(agg.TotalRuns / NULLIF(agg.MatchesPlayed, 0), 2) AS BattingAverage,
  CASE
    WHEN agg.TotalWickets > 0 THEN ROUND(agg.TotalRunsConceded / agg.TotalWickets, 2)
    ELSE 0
  END AS BowlingAverage,
  CASE
    WHEN agg.TotalBalls > 0 THEN ROUND((agg.TotalRuns / agg.TotalBalls) * 100, 2)
    ELSE 0
  END AS StrikeRate,
  CASE
    WHEN agg.TotalOvers > 0 THEN ROUND(agg.TotalRunsConceded / agg.TotalOvers, 2)
    ELSE 0
  END AS EconomyRate,
  agg.Centuries,
  agg.HalfCenturies,
  agg.FiveWickets,
  agg.FourWickets,
  COALESCE(best.BestBowling, 'N/A') AS BestBowling
FROM (
  SELECT
    PlayerID,
    COUNT(*) AS MatchesPlayed,
    COALESCE(SUM(RunsScored), 0) AS TotalRuns,
    COALESCE(SUM(WicketsTaken), 0) AS TotalWickets,
    COALESCE(MAX(RunsScored), 0) AS HighestScore,
    COALESCE(SUM(BallsFaced), 0) AS TotalBalls,
    COALESCE(SUM(OversBowled), 0) AS TotalOvers,
    COALESCE(SUM(RunsConceded), 0) AS TotalRunsConceded,
    COALESCE(SUM(CASE WHEN RunsScored >= 100 THEN 1 ELSE 0 END), 0) AS Centuries,
    COALESCE(SUM(CASE WHEN RunsScored >= 50 AND RunsScored < 100 THEN 1 ELSE 0 END), 0) AS HalfCenturies,
    COALESCE(SUM(CASE WHEN WicketsTaken >= 5 THEN 1 ELSE 0 END), 0) AS FiveWickets,
    COALESCE(SUM(CASE WHEN WicketsTaken >= 4 THEN 1 ELSE 0 END), 0) AS FourWickets
  FROM playermatchperformance
  WHERE VerifiedStatus = 'verified'
  GROUP BY PlayerID
) agg
LEFT JOIN (
  SELECT
    bw.PlayerID,
    CONCAT(bw.BestWickets, '/', br.BestRunsConceded) AS BestBowling
  FROM (
    SELECT PlayerID, MAX(COALESCE(WicketsTaken, 0)) AS BestWickets
    FROM playermatchperformance
    WHERE VerifiedStatus = 'verified'
    GROUP BY PlayerID
  ) bw
  JOIN (
    SELECT PlayerID, WicketsTaken, MIN(COALESCE(RunsConceded, 0)) AS BestRunsConceded
    FROM playermatchperformance
    WHERE VerifiedStatus = 'verified'
    GROUP BY PlayerID, WicketsTaken
  ) br
    ON br.PlayerID = bw.PlayerID
   AND br.WicketsTaken = bw.BestWickets
) best
  ON best.PlayerID = agg.PlayerID
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
