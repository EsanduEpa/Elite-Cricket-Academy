-- 2026-04-19
-- DEMO/DUMMY DATA SEED
-- Random-ish match performance values for matches 29/30/31.
--
-- WARNING:
-- This FABRICATES performance data to make match tallies work.
-- Use only for demo/testing.
--
-- What it does:
-- 1) Backs up existing playermatchperformance rows for MatchID IN (29,30,31)
--    into playermatchperformance_backup_20260418.
-- 2) Overwrites playermatchperformance batting/bowling fields for all 11 players
--    per match so that:
--      - SUM(RunsScored) == crimatch.OurRuns
--      - SUM(WicketsTaken) == crimatch.OpponentWickets
--      - SUM(RunsConceded for bowlers) == crimatch.OpponentRuns
--      - OversBowled totals 20.0 across 5 bowlers (T20-like)
-- 3) Marks all these performance rows as VerifiedStatus='verified'.

START TRANSACTION;

-- -----------------------------------------------------------------------------
-- Backup current values (safe to re-run; creates additional backup rows)
-- -----------------------------------------------------------------------------
INSERT INTO playermatchperformance_backup_20260418
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating)
SELECT
  MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating
FROM playermatchperformance
WHERE MatchID IN (29,30,31);

-- -----------------------------------------------------------------------------
-- Match 29 (OurRuns=210, OpponentRuns=185, OpponentWickets=10)
-- Batting totals: 210 off 120 balls
-- Bowling totals: 20.0 overs, 185 runs conceded, 10 wickets
-- Bowlers: 15, 43, 42, 44, 68 (4 overs each)
-- -----------------------------------------------------------------------------
UPDATE playermatchperformance
SET RunsScored=60, BallsFaced=34, WicketsTaken=2, OversBowled=4.0, RunsConceded=35, Catches=0, Stumpings=0, Rating=7.5
WHERE MatchID=29 AND PlayerID=15;

UPDATE playermatchperformance
SET RunsScored=30, BallsFaced=20, WicketsTaken=2, OversBowled=4.0, RunsConceded=38, Catches=0, Stumpings=0, Rating=6.8
WHERE MatchID=29 AND PlayerID=43;

UPDATE playermatchperformance
SET RunsScored=25, BallsFaced=17, WicketsTaken=2, OversBowled=4.0, RunsConceded=40, Catches=0, Stumpings=0, Rating=6.6
WHERE MatchID=29 AND PlayerID=42;

UPDATE playermatchperformance
SET RunsScored=20, BallsFaced=14, WicketsTaken=2, OversBowled=4.0, RunsConceded=36, Catches=0, Stumpings=0, Rating=6.4
WHERE MatchID=29 AND PlayerID=44;

UPDATE playermatchperformance
SET RunsScored=18, BallsFaced=9, WicketsTaken=2, OversBowled=4.0, RunsConceded=36, Catches=0, Stumpings=0, Rating=6.3
WHERE MatchID=29 AND PlayerID=68;

UPDATE playermatchperformance
SET RunsScored=15, BallsFaced=8, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.8
WHERE MatchID=29 AND PlayerID=45;

UPDATE playermatchperformance
SET RunsScored=12, BallsFaced=6, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.6
WHERE MatchID=29 AND PlayerID=46;

UPDATE playermatchperformance
SET RunsScored=10, BallsFaced=4, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.4
WHERE MatchID=29 AND PlayerID=71;

UPDATE playermatchperformance
SET RunsScored=8, BallsFaced=3, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.2
WHERE MatchID=29 AND PlayerID=70;

UPDATE playermatchperformance
SET RunsScored=7, BallsFaced=2, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.1
WHERE MatchID=29 AND PlayerID=69;

UPDATE playermatchperformance
SET RunsScored=5, BallsFaced=3, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.0
WHERE MatchID=29 AND PlayerID=67;

-- -----------------------------------------------------------------------------
-- Match 30 (OurRuns=198, OpponentRuns=199, OpponentWickets=7)
-- Batting totals: 198 off 120 balls
-- Bowling totals: 20.0 overs, 199 runs conceded, 7 wickets
-- Bowlers: 15, 43, 42, 44, 68 (4 overs each)
-- -----------------------------------------------------------------------------
UPDATE playermatchperformance
SET RunsScored=45, BallsFaced=30, WicketsTaken=2, OversBowled=4.0, RunsConceded=42, Catches=0, Stumpings=0, Rating=7.1
WHERE MatchID=30 AND PlayerID=15;

UPDATE playermatchperformance
SET RunsScored=35, BallsFaced=22, WicketsTaken=2, OversBowled=4.0, RunsConceded=41, Catches=0, Stumpings=0, Rating=6.9
WHERE MatchID=30 AND PlayerID=43;

UPDATE playermatchperformance
SET RunsScored=28, BallsFaced=18, WicketsTaken=1, OversBowled=4.0, RunsConceded=38, Catches=0, Stumpings=0, Rating=6.5
WHERE MatchID=30 AND PlayerID=42;

UPDATE playermatchperformance
SET RunsScored=25, BallsFaced=17, WicketsTaken=1, OversBowled=4.0, RunsConceded=40, Catches=0, Stumpings=0, Rating=6.4
WHERE MatchID=30 AND PlayerID=44;

UPDATE playermatchperformance
SET RunsScored=12, BallsFaced=7, WicketsTaken=1, OversBowled=4.0, RunsConceded=38, Catches=0, Stumpings=0, Rating=6.2
WHERE MatchID=30 AND PlayerID=68;

UPDATE playermatchperformance
SET RunsScored=18, BallsFaced=12, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.8
WHERE MatchID=30 AND PlayerID=45;

UPDATE playermatchperformance
SET RunsScored=10, BallsFaced=5, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.5
WHERE MatchID=30 AND PlayerID=46;

UPDATE playermatchperformance
SET RunsScored=8, BallsFaced=4, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.3
WHERE MatchID=30 AND PlayerID=71;

UPDATE playermatchperformance
SET RunsScored=7, BallsFaced=3, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.2
WHERE MatchID=30 AND PlayerID=70;

UPDATE playermatchperformance
SET RunsScored=5, BallsFaced=1, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.0
WHERE MatchID=30 AND PlayerID=67;

UPDATE playermatchperformance
SET RunsScored=5, BallsFaced=1, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.0
WHERE MatchID=30 AND PlayerID=69;

-- -----------------------------------------------------------------------------
-- Match 31 (OurRuns=175, OpponentRuns=174, OpponentWickets=10)
-- Batting totals: 175 off 120 balls
-- Bowling totals: 20.0 overs, 174 runs conceded, 10 wickets
-- Bowlers: 15, 43, 42, 44, 68 (4 overs each)
-- -----------------------------------------------------------------------------
UPDATE playermatchperformance
SET RunsScored=50, BallsFaced=33, WicketsTaken=3, OversBowled=4.0, RunsConceded=34, Catches=0, Stumpings=0, Rating=7.4
WHERE MatchID=31 AND PlayerID=15;

UPDATE playermatchperformance
SET RunsScored=25, BallsFaced=19, WicketsTaken=2, OversBowled=4.0, RunsConceded=36, Catches=0, Stumpings=0, Rating=6.7
WHERE MatchID=31 AND PlayerID=43;

UPDATE playermatchperformance
SET RunsScored=22, BallsFaced=18, WicketsTaken=2, OversBowled=4.0, RunsConceded=33, Catches=0, Stumpings=0, Rating=6.6
WHERE MatchID=31 AND PlayerID=42;

UPDATE playermatchperformance
SET RunsScored=20, BallsFaced=16, WicketsTaken=2, OversBowled=4.0, RunsConceded=35, Catches=0, Stumpings=0, Rating=6.5
WHERE MatchID=31 AND PlayerID=44;

UPDATE playermatchperformance
SET RunsScored=15, BallsFaced=9, WicketsTaken=1, OversBowled=4.0, RunsConceded=36, Catches=0, Stumpings=0, Rating=6.3
WHERE MatchID=31 AND PlayerID=68;

UPDATE playermatchperformance
SET RunsScored=18, BallsFaced=13, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.9
WHERE MatchID=31 AND PlayerID=45;

UPDATE playermatchperformance
SET RunsScored=10, BallsFaced=5, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.5
WHERE MatchID=31 AND PlayerID=46;

UPDATE playermatchperformance
SET RunsScored=6, BallsFaced=3, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.2
WHERE MatchID=31 AND PlayerID=71;

UPDATE playermatchperformance
SET RunsScored=4, BallsFaced=2, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.1
WHERE MatchID=31 AND PlayerID=70;

UPDATE playermatchperformance
SET RunsScored=3, BallsFaced=1, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.0
WHERE MatchID=31 AND PlayerID=67;

UPDATE playermatchperformance
SET RunsScored=2, BallsFaced=1, WicketsTaken=0, OversBowled=0.0, RunsConceded=0, Catches=0, Stumpings=0, Rating=5.0
WHERE MatchID=31 AND PlayerID=69;

-- Mark as verified so verified-only queries and derived stats can include them
UPDATE playermatchperformance
SET VerifiedStatus='verified', VerifiedBy=NULL, VerifiedAt=NOW()
WHERE MatchID IN (29,30,31);

COMMIT;

-- -----------------------------------------------------------------------------
-- Validation: per-match tallies should now match exactly
-- -----------------------------------------------------------------------------
SELECT
  cm.MatchID,
  cm.Name AS MatchName,
  cm.OurRuns,
  SUM(pmp.RunsScored) AS SumPlayerRuns,
  (cm.OurRuns - SUM(pmp.RunsScored)) AS RunsDiff,
  cm.OpponentRuns,
  SUM(pmp.RunsConceded) AS SumPlayerRunsConceded,
  (cm.OpponentRuns - SUM(pmp.RunsConceded)) AS OppRunsDiff,
  cm.OpponentWickets,
  SUM(pmp.WicketsTaken) AS SumPlayerWickets,
  (cm.OpponentWickets - SUM(pmp.WicketsTaken)) AS WicketsDiff,
  SUM(pmp.OversBowled) AS SumOvers
FROM crimatch cm
JOIN playermatchperformance pmp ON pmp.MatchID = cm.MatchID
WHERE cm.MatchID IN (29,30,31)
GROUP BY cm.MatchID, cm.Name, cm.OurRuns, cm.OpponentRuns, cm.OpponentWickets
ORDER BY cm.MatchID;
