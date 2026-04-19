-- 2026-04-18 Cleanup
-- Keep playeroverallstats containing ONLY players with at least one VERIFIED playermatchperformance.
--
-- This matches the application rule:
-- - If a player has 0 verified playermatchperformance records, they should NOT have a row in playeroverallstats.
--
-- Safe to re-run.

START TRANSACTION;

-- Count before
SELECT COUNT(*) AS overallstats_rows_before
FROM playeroverallstats;

-- Delete overallstats for players with zero verified performance rows
DELETE pos
FROM playeroverallstats pos
LEFT JOIN (
  SELECT DISTINCT PlayerID
  FROM playermatchperformance
  WHERE VerifiedStatus = 'verified'
) v ON v.PlayerID = pos.PlayerID
WHERE v.PlayerID IS NULL;

-- Count after
SELECT COUNT(*) AS overallstats_rows_after
FROM playeroverallstats;

COMMIT;
