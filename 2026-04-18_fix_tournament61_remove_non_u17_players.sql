-- 2026-04-18 Fix
-- TournamentID=61 is an Under-17 tournament, but 5 non-U17 (AgeGroup='Open') adult players
-- were seeded into the squad. This script removes ONLY those players from tournament 61
-- and deletes their tournament-61 scoped related rows.
--
-- Target players (non-U17 in squad for TournamentID=61): 6, 7, 16, 18, 20
--
-- Safe to re-run (idempotent): DELETE statements are scoped and will simply affect 0 rows if already cleaned.

START TRANSACTION;

SET @TournamentID := 61;

-- Safety: ensure we are operating on the expected U17 tournament
SELECT
  t.TournamentID,
  t.Name,
  t.AgeGroup,
  t.tdate,
  t.Status
FROM tournament t
WHERE t.TournamentID = @TournamentID;

-- Before: show squad composition vs U17 assignment
SELECT
  COUNT(*) AS squad_total,
  SUM(has_u17 = 1) AS squad_u17,
  SUM(has_u17 = 0) AS squad_not_u17
FROM (
  SELECT
    tp.PlayerID,
    MAX(psca.AgeGroup = 'Under 17') AS has_u17
  FROM tournamentplayer tp
  LEFT JOIN player_skill_coach_assignment psca
    ON psca.PlayerID = tp.PlayerID
  WHERE tp.TournamentID = @TournamentID
  GROUP BY tp.PlayerID
) x;

-- Before: list the players we are about to remove (with DOB-based age on tournament date)
SELECT
  tp.PlayerID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  u.DateOfBirth,
  TIMESTAMPDIFF(YEAR, u.DateOfBirth, t.tdate) AS AgeOnTournamentDate,
  GROUP_CONCAT(DISTINCT psca.AgeGroup ORDER BY psca.AgeGroup SEPARATOR ', ') AS AgeGroupsInAssignment
FROM tournamentplayer tp
JOIN tournament t ON t.TournamentID = tp.TournamentID
JOIN user u ON u.UserID = tp.PlayerID
LEFT JOIN player_skill_coach_assignment psca ON psca.PlayerID = tp.PlayerID
WHERE tp.TournamentID = @TournamentID
  AND tp.PlayerID IN (6, 7, 16, 18, 20)
GROUP BY tp.PlayerID, u.FirstName, u.LastName, u.DateOfBirth, t.tdate
ORDER BY PlayerName;

-- -----------------------------------------------------------------------------
-- Deletes (order chosen to avoid FK issues if present)
-- -----------------------------------------------------------------------------

-- A) Remove match performances for tournament 61 matches for these players
DELETE pmp
FROM playermatchperformance pmp
JOIN crimatch cm ON cm.MatchID = pmp.MatchID
WHERE cm.TournamentID = @TournamentID
  AND pmp.PlayerID IN (6, 7, 16, 18, 20);

-- B) Remove tournament stats rows for these players
DELETE pts
FROM playertournamentstats pts
WHERE pts.TournamentID = @TournamentID
  AND pts.PlayerID IN (6, 7, 16, 18, 20);

-- C) Remove squad rows
DELETE tp
FROM tournamentplayer tp
WHERE tp.TournamentID = @TournamentID
  AND tp.PlayerID IN (6, 7, 16, 18, 20);

-- D) Remove join requests (seeded approved requests)
DELETE tjr
FROM tournament_join_request tjr
WHERE tjr.TournamentID = @TournamentID
  AND tjr.PlayerID IN (6, 7, 16, 18, 20);

-- E) Remove coach recommendations (seeded approvals)
DELETE ctr
FROM coach_tournament_recommendations ctr
WHERE ctr.TournamentID = @TournamentID
  AND ctr.PlayerID IN (6, 7, 16, 18, 20);

-- After: confirm squad is now only U17-assigned players (per assignment table)
SELECT
  COUNT(*) AS squad_total,
  SUM(has_u17 = 1) AS squad_u17,
  SUM(has_u17 = 0) AS squad_not_u17
FROM (
  SELECT
    tp.PlayerID,
    MAX(psca.AgeGroup = 'Under 17') AS has_u17
  FROM tournamentplayer tp
  LEFT JOIN player_skill_coach_assignment psca
    ON psca.PlayerID = tp.PlayerID
  WHERE tp.TournamentID = @TournamentID
  GROUP BY tp.PlayerID
) x;

-- After: make sure none of the removed players remain in the squad
SELECT
  COUNT(*) AS remaining_rows_for_removed_players
FROM tournamentplayer tp
WHERE tp.TournamentID = @TournamentID
  AND tp.PlayerID IN (6, 7, 16, 18, 20);

COMMIT;
