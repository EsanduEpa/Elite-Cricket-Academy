-- Enforce max players per age group by deleting the newest players in any group that exceeds the cap.
-- Age group rules:
--   Under 11: age < 11
--   Under 13: age < 13
--   Under 15: age < 15
--   Under 17: age < 17
--   Under 19: age < 19
--   Open:     age >= 19 (or missing DOB)
-- Ordering rule for deletion: newest first by user.DateJoined, then UserID.
--
-- IMPORTANT: This is destructive. It deletes users and (via cascades) related rows.
-- It also deletes/updates rows in tables that have RESTRICT foreign keys to user(UserID).

SET @cap := 25;

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_players_to_delete;
CREATE TEMPORARY TABLE tmp_players_to_delete (
  UserID INT(11) NOT NULL PRIMARY KEY
) ENGINE=MEMORY;

INSERT INTO tmp_players_to_delete (UserID)
WITH players AS (
    SELECT
        u.UserID,
        u.DateJoined,
        CASE
            WHEN u.DateOfBirth IS NULL THEN 'Open'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
            ELSE 'Open'
        END AS AgeGroup
    FROM user u
    JOIN playerprofile p ON p.PlayerID = u.UserID
    WHERE u.Role = 'Player'
), ranked AS (
    SELECT
        UserID,
        AgeGroup,
        DateJoined,
        ROW_NUMBER() OVER (PARTITION BY AgeGroup ORDER BY DateJoined DESC, UserID DESC) AS rn_newest,
        COUNT(*) OVER (PARTITION BY AgeGroup) AS grp_cnt
    FROM players
)
SELECT UserID
FROM ranked
WHERE grp_cnt > @cap
  AND rn_newest <= (grp_cnt - @cap);

-- Preview summary: how many will be deleted per age group?
SELECT
  AgeGroup,
  COUNT(*) AS to_delete
FROM (
  SELECT
      u.UserID,
      CASE
          WHEN u.DateOfBirth IS NULL THEN 'Open'
          WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
          WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
          WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
          WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
          WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
          ELSE 'Open'
      END AS AgeGroup
  FROM tmp_players_to_delete t
  JOIN user u ON u.UserID = t.UserID
) x
GROUP BY AgeGroup
ORDER BY FIELD(AgeGroup,'Under 11','Under 13','Under 15','Under 17','Under 19','Open');

-- Preview details (for audit)
SELECT
  u.UserID,
  TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
  u.Email,
  u.DateOfBirth,
  u.DateJoined,
  CASE
      WHEN u.DateOfBirth IS NULL THEN 'Open'
      WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
      WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
      WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
      WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
      WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
      ELSE 'Open'
  END AS AgeGroup
FROM tmp_players_to_delete t
JOIN user u ON u.UserID = t.UserID
ORDER BY u.DateJoined DESC, u.UserID DESC;

-- Clean up RESTRICT references (nullable columns can be set to NULL)
UPDATE tournament_result
SET BestBatsman = NULL
WHERE BestBatsman IN (SELECT UserID FROM tmp_players_to_delete);

UPDATE tournament_result
SET BestBowler = NULL
WHERE BestBowler IN (SELECT UserID FROM tmp_players_to_delete);

UPDATE tournament_result
SET ManOfTournament = NULL
WHERE ManOfTournament IN (SELECT UserID FROM tmp_players_to_delete);

UPDATE tournamentplayer
SET SelectedBy = NULL
WHERE SelectedBy IN (SELECT UserID FROM tmp_players_to_delete);

UPDATE tournament_join_request
SET ReviewedBy = NULL
WHERE ReviewedBy IN (SELECT UserID FROM tmp_players_to_delete);

UPDATE trainer_tournament_recommendations
SET ReviewedBy = NULL
WHERE ReviewedBy IN (SELECT UserID FROM tmp_players_to_delete);

-- Clean up RESTRICT references (non-null columns require DELETE)
DELETE FROM coach_tournament_recommendations
WHERE PlayerID IN (SELECT UserID FROM tmp_players_to_delete)
   OR CoachID  IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM trainer_tournament_recommendations
WHERE PlayerID IN (SELECT UserID FROM tmp_players_to_delete)
   OR TrainerID IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM slot_booking
WHERE PlayerID IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM tournament_join_request
WHERE PlayerID IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM tournamentplayer
WHERE PlayerID IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM slot_audit_log
WHERE ChangedBy IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM slot_occurrence_staff_override
WHERE UserID IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM slot_template_staff
WHERE UserID IN (SELECT UserID FROM tmp_players_to_delete)
   OR AssignedBy IN (SELECT UserID FROM tmp_players_to_delete);

DELETE FROM slot_template
WHERE CreatedBy IN (SELECT UserID FROM tmp_players_to_delete);

-- Finally delete the player users; CASCADE/SET NULL will handle most other tables.
DELETE FROM user
WHERE UserID IN (SELECT UserID FROM tmp_players_to_delete)
  AND Role = 'Player';

COMMIT;

-- Post-check: counts per age group after trimming
SELECT AgeGroup, COUNT(*) AS player_count
FROM (
    SELECT
        u.UserID,
        CASE
            WHEN u.DateOfBirth IS NULL THEN 'Open'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
            ELSE 'Open'
        END AS AgeGroup
    FROM user u
    JOIN playerprofile p ON p.PlayerID = u.UserID
    WHERE u.Role='Player'
) x
GROUP BY AgeGroup
ORDER BY FIELD(AgeGroup,'Under 11','Under 13','Under 15','Under 17','Under 19','Open');
