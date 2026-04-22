# 2026-04-18 db changes — U17 tournament integrity (assignment-based)

This document contains the “tournament thingy” SQL rewritten so **U17 players are identified ONLY from `player_skill_coach_assignment`** (`AgeGroup='Under 17'`).

Everything here is **queries/scripts only** (don’t run unless you choose to).

---

## A) Define the U17 player set (from assignment table)

```sql
-- U17 players are those with at least one assignment row tagged Under 17
WITH u17 AS (
  SELECT DISTINCT psca.PlayerID
  FROM player_skill_coach_assignment psca
  WHERE psca.AgeGroup = 'Under 17'
)
SELECT u.UserID AS PlayerID, u.Name
FROM u17
JOIN user u ON u.UserID = u17.PlayerID
WHERE u.Role='Player' AND u.Status='active'
ORDER BY u.UserID;

-- NOTE (cricket_academy schema): `user` has FirstName/LastName, not `Name`.
-- If you get an error on `u.Name`, replace it with:
--   CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName
```

---

## 1) U17 players must exist in `tournamentplayer` for completed U17 tournaments

### 1.1 List completed Under-17 tournaments

```sql
SELECT t.TournamentID, t.Name, t.AgeGroup, t.Status, t.tdate
FROM tournament t
WHERE t.AgeGroup='Under 17' AND t.Status='completed'
ORDER BY t.tdate, t.TournamentID;
```

### 1.2 Show which U17 players are missing from `tournamentplayer` (for completed U17 tournaments)

```sql
WITH u17 AS (
  SELECT DISTINCT PlayerID
  FROM player_skill_coach_assignment
  WHERE AgeGroup='Under 17'
),
u17_completed_tournaments AS (
  SELECT TournamentID
  FROM tournament
  WHERE AgeGroup='Under 17' AND Status='completed'
)
SELECT
  t.TournamentID,
  t.Name AS TournamentName,
  u17.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName
FROM u17_completed_tournaments x
JOIN tournament t ON t.TournamentID = x.TournamentID
JOIN u17
JOIN user u ON u.UserID = u17.PlayerID
LEFT JOIN tournamentplayer tp
  ON tp.TournamentID = t.TournamentID
 AND tp.PlayerID     = u17.PlayerID
WHERE tp.PlayerID IS NULL
ORDER BY t.TournamentID, u17.PlayerID;
```

### 1.3 Pull the actual `tournamentplayer` rows for U17 players (for those completed U17 tournaments)

```sql
WITH u17 AS (
  SELECT DISTINCT PlayerID
  FROM player_skill_coach_assignment
  WHERE AgeGroup='Under 17'
)
SELECT
  tp.TournamentID,
  tp.TournamentPlayerID,
  tp.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
  tp.Team,
  tp.RoleInTeam,
  tp.SelectionStatus,
  tp.SelectedAt
FROM tournamentplayer tp
JOIN u17 ON u17.PlayerID = tp.PlayerID
JOIN tournament t ON t.TournamentID = tp.TournamentID
JOIN user u ON u.UserID = tp.PlayerID
WHERE t.AgeGroup='Under 17'
  AND t.Status='completed'
ORDER BY tp.TournamentID, tp.PlayerID;
```

---

## 2) If they’re in `tournamentplayer`, they must have `playermatchperformance` for that tournament’s matches

### 2.1 Missing performance rows (player is in `tournamentplayer`, but no row in `playermatchperformance` for a match)

```sql
WITH u17 AS (
  SELECT DISTINCT PlayerID
  FROM player_skill_coach_assignment
  WHERE AgeGroup='Under 17'
)
SELECT
  cm.TournamentID,
  cm.MatchID,
  cm.Name AS MatchName,
  tp.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName
FROM tournament t
JOIN crimatch cm
  ON cm.TournamentID = t.TournamentID
JOIN tournamentplayer tp
  ON tp.TournamentID = t.TournamentID
JOIN u17
  ON u17.PlayerID = tp.PlayerID
JOIN user u
  ON u.UserID = tp.PlayerID
LEFT JOIN playermatchperformance pmp
  ON pmp.MatchID  = cm.MatchID
 AND pmp.PlayerID = tp.PlayerID
WHERE t.AgeGroup='Under 17'
  AND t.Status='completed'
  AND pmp.PlayerID IS NULL
ORDER BY cm.TournamentID, cm.MatchID, tp.PlayerID;
```

### 2.2 Pull all performance rows for tournament 61 (example)

```sql
SET @TournamentID := 61;

SELECT
  cm.MatchID,
  cm.Name AS MatchName,
  cm.Date,
  pmp.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
  pmp.RunsScored,
  pmp.BallsFaced,
  pmp.WicketsTaken,
  pmp.OversBowled,
  pmp.RunsConceded,
  pmp.Catches,
  pmp.Stumpings,
  pmp.Rating
  -- If your DB has verification columns, you can add:
  -- ,pmp.VerifiedStatus, pmp.AddedBy, pmp.VerifiedBy, pmp.VerifiedAt
FROM crimatch cm
JOIN playermatchperformance pmp ON pmp.MatchID = cm.MatchID
JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID = @TournamentID
ORDER BY cm.MatchID, pmp.PlayerID;
```

---

## 3) Match-tally validations (MatchID=29 example)

You said:
- `crimatch.OurRuns = 210` and `OurWickets = 8` ⇒ you expect “8 batsman scores”
- `crimatch.OpponentWickets = 10` ⇒ our bowlers took 10 wickets
- wicket-takers must have role “Bowler” in `tournamentplayer`

### 3.1 Per-match totals: sum of runs and wickets vs `crimatch`

```sql
SET @MatchID := 29;

SELECT
  cm.MatchID,
  cm.TournamentID,
  cm.Name,
  cm.OurRuns,
  COALESCE(SUM(pmp.RunsScored),0) AS SumRunsScored,
  (COALESCE(SUM(pmp.RunsScored),0) - cm.OurRuns) AS RunsDiff,

  cm.OpponentWickets,
  COALESCE(SUM(pmp.WicketsTaken),0) AS SumWicketsTaken,
  (COALESCE(SUM(pmp.WicketsTaken),0) - cm.OpponentWickets) AS WicketsDiff,

  cm.OurWickets,
  COALESCE(SUM(CASE WHEN pmp.RunsScored > 0 THEN 1 ELSE 0 END),0) AS PlayersWithRuns,
  COALESCE(SUM(CASE WHEN pmp.BallsFaced > 0 THEN 1 ELSE 0 END),0) AS PlayersWhoBatted
FROM crimatch cm
LEFT JOIN playermatchperformance pmp
  ON pmp.MatchID = cm.MatchID
WHERE cm.MatchID = @MatchID
GROUP BY cm.MatchID;
```

### 3.2 “8 batsman scores should be there” check (flags mismatch)

```sql
SET @MatchID := 29;

SELECT
  cm.MatchID,
  cm.OurWickets,
  SUM(CASE WHEN pmp.RunsScored > 0 THEN 1 ELSE 0 END) AS PlayersWithRuns,
  (SUM(CASE WHEN pmp.RunsScored > 0 THEN 1 ELSE 0 END) - cm.OurWickets) AS DiffPlayersWithRunsMinusOurWickets
FROM crimatch cm
LEFT JOIN playermatchperformance pmp ON pmp.MatchID = cm.MatchID
WHERE cm.MatchID = @MatchID
GROUP BY cm.MatchID;
```

### 3.3 Wicket-takers must be Bowler (find violations)

```sql
SET @MatchID := 29;

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
WHERE pmp.MatchID = @MatchID
  AND pmp.WicketsTaken > 0
  AND (tp.RoleInTeam IS NULL OR tp.RoleInTeam <> 'Bowler')
ORDER BY pmp.WicketsTaken DESC, pmp.PlayerID;
```

---

## 4) “Give the mentioned entries for U17 players” (tournament 61 + matches 29/30/31)

This uses U17 from `player_skill_coach_assignment` and returns the rows you care about.

```sql
SET @TournamentID := 61;

WITH u17 AS (
  SELECT DISTINCT PlayerID
  FROM player_skill_coach_assignment
  WHERE AgeGroup='Under 17'
)
-- A) U17 players in tournamentplayer for this tournament
SELECT
  tp.TournamentID,
  tp.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
  tp.Team,
  tp.RoleInTeam,
  tp.SelectionStatus,
  tp.SelectedAt
FROM tournamentplayer tp
JOIN u17 ON u17.PlayerID = tp.PlayerID
JOIN user u ON u.UserID = tp.PlayerID
WHERE tp.TournamentID = @TournamentID
ORDER BY tp.PlayerID;

-- B) U17 players’ performances for the 3 matches
SELECT
  cm.MatchID,
  cm.Name AS MatchName,
  cm.Date,
  pmp.PlayerID,
  CONCAT_WS(' ', u.FirstName, u.LastName) AS PlayerName,
  pmp.RunsScored, pmp.BallsFaced,
  pmp.WicketsTaken, pmp.OversBowled, pmp.RunsConceded,
  pmp.Catches, pmp.Stumpings,
  pmp.Rating
FROM crimatch cm
JOIN playermatchperformance pmp ON pmp.MatchID = cm.MatchID
JOIN u17 ON u17.PlayerID = pmp.PlayerID
JOIN user u ON u.UserID = pmp.PlayerID
WHERE cm.TournamentID = @TournamentID
  AND cm.MatchID IN (29,30,31)
ORDER BY cm.MatchID, pmp.PlayerID;
```

---

## 5) Optional: drop + rebuild missing rows (framework)

### 5.2 + 5.3 Run together (insert missing tournamentplayer, then create missing performance rows)

```sql
-- DO NOT RUN unless you want to:
--  (a) force “every U17-assigned player” into tournamentplayer for tournament 61, AND
--  (b) create missing playermatchperformance rows (zeros) for matches 29/30/31 for that tournament roster.
-- NOTE: This uses a placeholder RoleInTeam. Adjust RoleInTeam properly after insert.
-- NOTE: This does NOT delete existing playermatchperformance rows. If you want a clean rebuild, run 5.1 first.

START TRANSACTION;

SET @TournamentID := 61;
SET @SelectedBy := 14; -- head coach user id (change if needed)

-- (5.2) Insert missing tournamentplayer rows for ALL U17-assigned players
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

-- (5.3) Ensure every tournamentplayer has a playermatchperformance row for matches 29/30/31 (zeros by default)
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
```

### 5.1 Drop only tournament 61 performance rows for matches 29/30/31

```sql
-- DO NOT RUN unless you want to delete those match performance rows
START TRANSACTION;

DELETE FROM playermatchperformance
WHERE MatchID IN (29,30,31);

COMMIT;
```

### 5.2 Insert missing `tournamentplayer` rows for ALL U17 players into tournament 61 (if that’s truly your rule)

```sql
-- DO NOT RUN unless you want to force “every U17-assigned player” into tournamentplayer for tournament 61
-- NOTE: This sets a placeholder RoleInTeam. Adjust RoleInTeam properly after insert.

START TRANSACTION;

SET @TournamentID := 61;
SET @SelectedBy := 14; -- head coach user id (change if needed)

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

COMMIT;
```

### 5.3 Ensure every tournamentplayer has a `playermatchperformance` row for matches 29/30/31 (zeros by default)

```sql
-- DO NOT RUN unless you want to (re)create missing performance rows with zero stats

START TRANSACTION;

SET @TournamentID := 61;

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
```

---

If you tell me the exact 11-player squad you expect for tournament 61 (or confirm that tournamentplayer already contains the correct squad), I can generate a deterministic “auto-fill match 29” script that:
- creates exactly 8 scoring batsmen (per your rule),
- makes `SUM(RunsScored)=210`,
- makes `SUM(WicketsTaken)=10`,
- assigns those wickets only to `RoleInTeam='Bowler'`.
