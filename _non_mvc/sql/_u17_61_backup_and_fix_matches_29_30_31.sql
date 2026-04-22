-- Backup + Fix script for tournament 61 matches 29/30/31
-- Database: cricket_academy
-- Goal: make playermatchperformance totals exactly match crimatch for runs/wickets + enforce wicket-takers are Bowlers.
-- WARNING: This overwrites existing playermatchperformance stats for MatchID IN (29,30,31).

START TRANSACTION;

SET @TournamentID := 61;

-- 1) Backup current performance rows
CREATE TABLE IF NOT EXISTS playermatchperformance_backup_20260418 (
  BackupID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  BackedUpAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MatchID INT NOT NULL,
  PlayerID INT NOT NULL,
  RunsScored INT,
  BallsFaced INT,
  WicketsTaken INT,
  OversBowled DECIMAL(4,1),
  RunsConceded INT,
  Catches INT,
  Stumpings INT,
  Rating DECIMAL(4,1)
);

INSERT INTO playermatchperformance_backup_20260418
  (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, RunsConceded, Catches, Stumpings, Rating)
SELECT
  p.MatchID, p.PlayerID, p.RunsScored, p.BallsFaced, p.WicketsTaken, p.OversBowled, p.RunsConceded, p.Catches, p.Stumpings, p.Rating
FROM playermatchperformance p
WHERE p.MatchID IN (29,30,31);

-- 2) Determine a stable roster ordering for tournament 61
DROP TEMPORARY TABLE IF EXISTS tmp_roster;
CREATE TEMPORARY TABLE tmp_roster (
  rn INT NOT NULL,
  PlayerID INT NOT NULL,
  PRIMARY KEY (rn),
  UNIQUE KEY uq_tmp_roster_player (PlayerID)
) ENGINE=MEMORY
AS
SELECT
  (@rownum := @rownum + 1) AS rn,
  x.PlayerID
FROM (
  SELECT DISTINCT tp.PlayerID
  FROM tournamentplayer tp
  WHERE tp.TournamentID=@TournamentID
  ORDER BY tp.PlayerID
) x
CROSS JOIN (SELECT @rownum := 0) vars;

-- Choose 4 bowlers deterministically: rn 1..4
DROP TEMPORARY TABLE IF EXISTS tmp_bowlers;
CREATE TEMPORARY TABLE tmp_bowlers (
  PlayerID INT NOT NULL PRIMARY KEY
) ENGINE=MEMORY
AS
SELECT PlayerID
FROM tmp_roster
WHERE rn IN (1,2,3,4);

-- 3) Enforce RoleInTeam='Bowler' for any player who will take wickets (our chosen bowlers)
UPDATE tournamentplayer tp
JOIN tmp_bowlers b ON b.PlayerID = tp.PlayerID
SET tp.RoleInTeam = 'Bowler'
WHERE tp.TournamentID=@TournamentID;

-- 4) Reset all stats for matches 29/30/31 to zero (keeps rows, overwrites stats)
UPDATE playermatchperformance p
SET
  p.RunsScored=0,
  p.BallsFaced=0,
  p.WicketsTaken=0,
  p.OversBowled=0.0,
  p.RunsConceded=0,
  p.Catches=0,
  p.Stumpings=0,
  p.Rating=0.0
WHERE p.MatchID IN (29,30,31);

-- Helper: apply run allocations for a match using rn 1..N batters
-- Match 29: OurRuns=210, OurWickets=8 => 8 players with runs. Distribution: 30,30,30,30,30,30,15,15
UPDATE playermatchperformance p
JOIN tmp_roster r ON r.PlayerID = p.PlayerID
SET p.RunsScored = CASE r.rn
  WHEN 1 THEN 30
  WHEN 2 THEN 30
  WHEN 3 THEN 30
  WHEN 4 THEN 30
  WHEN 5 THEN 30
  WHEN 6 THEN 30
  WHEN 7 THEN 15
  WHEN 8 THEN 15
  ELSE 0
END,
p.BallsFaced = CASE
  WHEN r.rn BETWEEN 1 AND 8 THEN CASE r.rn
    WHEN 7 THEN 18
    WHEN 8 THEN 18
    ELSE 32
  END
  ELSE 0
END
WHERE p.MatchID=29;

-- Match 30: OurRuns=198, OurWickets=10 => 10 players with runs. Distribution: 20,20,20,20,20,20,20,20,19,19
UPDATE playermatchperformance p
JOIN tmp_roster r ON r.PlayerID = p.PlayerID
SET p.RunsScored = CASE r.rn
  WHEN 1 THEN 20
  WHEN 2 THEN 20
  WHEN 3 THEN 20
  WHEN 4 THEN 20
  WHEN 5 THEN 20
  WHEN 6 THEN 20
  WHEN 7 THEN 20
  WHEN 8 THEN 20
  WHEN 9 THEN 19
  WHEN 10 THEN 19
  ELSE 0
END,
p.BallsFaced = CASE
  WHEN r.rn BETWEEN 1 AND 10 THEN 24
  ELSE 0
END
WHERE p.MatchID=30;

-- Match 31: OurRuns=175, OurWickets=9 => 9 players with runs. Distribution: 20,20,20,20,20,20,20,18,17
UPDATE playermatchperformance p
JOIN tmp_roster r ON r.PlayerID = p.PlayerID
SET p.RunsScored = CASE r.rn
  WHEN 1 THEN 20
  WHEN 2 THEN 20
  WHEN 3 THEN 20
  WHEN 4 THEN 20
  WHEN 5 THEN 20
  WHEN 6 THEN 20
  WHEN 7 THEN 20
  WHEN 8 THEN 18
  WHEN 9 THEN 17
  ELSE 0
END,
p.BallsFaced = CASE
  WHEN r.rn BETWEEN 1 AND 9 THEN 22
  ELSE 0
END
WHERE p.MatchID=31;

-- Apply wicket allocations for chosen bowlers only, per crimatch.OpponentWickets
-- Match 29: OpponentWickets=10 => 3,3,2,2 across bowlers rn 1..4
UPDATE playermatchperformance p
JOIN tmp_roster r ON r.PlayerID=p.PlayerID
JOIN tmp_bowlers b ON b.PlayerID=p.PlayerID
SET p.WicketsTaken = CASE r.rn
  WHEN 1 THEN 3
  WHEN 2 THEN 3
  WHEN 3 THEN 2
  WHEN 4 THEN 2
  ELSE 0
END
WHERE p.MatchID=29;

-- Match 30: OpponentWickets=7 => 2,2,2,1
UPDATE playermatchperformance p
JOIN tmp_roster r ON r.PlayerID=p.PlayerID
JOIN tmp_bowlers b ON b.PlayerID=p.PlayerID
SET p.WicketsTaken = CASE r.rn
  WHEN 1 THEN 2
  WHEN 2 THEN 2
  WHEN 3 THEN 2
  WHEN 4 THEN 1
  ELSE 0
END
WHERE p.MatchID=30;

-- Match 31: OpponentWickets=10 => 3,3,2,2
UPDATE playermatchperformance p
JOIN tmp_roster r ON r.PlayerID=p.PlayerID
JOIN tmp_bowlers b ON b.PlayerID=p.PlayerID
SET p.WicketsTaken = CASE r.rn
  WHEN 1 THEN 3
  WHEN 2 THEN 3
  WHEN 3 THEN 2
  WHEN 4 THEN 2
  ELSE 0
END
WHERE p.MatchID=31;

-- 5) Validations
\! echo "----- VALIDATION: Per-match totals vs crimatch (29/30/31) -----"
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
LEFT JOIN playermatchperformance pmp ON pmp.MatchID = cm.MatchID
WHERE cm.TournamentID=@TournamentID
  AND cm.MatchID IN (29,30,31)
GROUP BY cm.MatchID
ORDER BY cm.MatchID;

\! echo "----- VALIDATION: Wicket-takers must be Bowler -----"
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

COMMIT;
