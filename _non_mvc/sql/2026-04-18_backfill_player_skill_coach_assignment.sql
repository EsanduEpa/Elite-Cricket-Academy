-- Backfill player_skill_coach_assignment for any player who has a playerprofile
-- Ensures each (PlayerID, CoachingType) exists for coaching types: batting/bowling/fielding.
-- Coach selection matches the application logic:
-- - Prefer exact age group mapping, else fall back to Open
-- - Prefer lower current load, then PriorityRank, then coach experience
--
-- Safe to run multiple times.

INSERT IGNORE INTO player_skill_coach_assignment
    (PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, Notes)
SELECT
    src.PlayerID,
    src.CoachingType,
    src.CoachID,
    src.AgeGroup,
    'system_refresh' AS AssignmentSource,
    NULL AS AssignedBy,
    'Backfill: ensure all player profiles have coach assignments' AS Notes
FROM (
    SELECT
        p.PlayerID,
        ct.CoachingType,
        CASE
            WHEN u.DateOfBirth IS NULL THEN 'Open'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
            ELSE 'Open'
        END AS AgeGroup,
        (
            SELECT csg.CoachID
            FROM coach_skill_age_group_assignment csg
            JOIN coachprofile cp ON cp.CoachID = csg.CoachID
            JOIN user cu ON cu.UserID = csg.CoachID
            LEFT JOIN player_skill_coach_assignment psca
                   ON psca.CoachID = csg.CoachID
                  AND psca.CoachingType = csg.CoachingType
            WHERE csg.IsActive = 1
              AND cu.Status = 'active'
              AND csg.CoachingType = ct.CoachingType
              AND csg.AgeGroup IN (
                CASE
                    WHEN u.DateOfBirth IS NULL THEN 'Open'
                    WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
                    WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
                    WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
                    WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
                    WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
                    ELSE 'Open'
                END,
                'Open'
              )
            GROUP BY csg.CoachID,
                     csg.AgeGroup,
                     csg.PriorityRank,
                     cp.Experience,
                     cu.FirstName,
                     cu.LastName
            ORDER BY
                CASE
                    WHEN csg.AgeGroup = (
                        CASE
                            WHEN u.DateOfBirth IS NULL THEN 'Open'
                            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
                            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 13 THEN 'Under 13'
                            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 15 THEN 'Under 15'
                            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 17 THEN 'Under 17'
                            WHEN TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < 19 THEN 'Under 19'
                            ELSE 'Open'
                        END
                    ) THEN 0
                    ELSE 1
                END,
                COUNT(psca.PlayerID) ASC,
                csg.PriorityRank ASC,
                cp.Experience DESC,
                cu.FirstName ASC,
                cu.LastName ASC
            LIMIT 1
        ) AS CoachID
    FROM playerprofile p
    JOIN user u
      ON u.UserID = p.PlayerID
     AND u.Role = 'Player'
    CROSS JOIN (
        SELECT 'batting' AS CoachingType
        UNION ALL SELECT 'bowling'
        UNION ALL SELECT 'fielding'
    ) ct
    LEFT JOIN player_skill_coach_assignment existing
           ON existing.PlayerID = p.PlayerID
          AND existing.CoachingType = ct.CoachingType
    WHERE existing.PlayerID IS NULL
) src
WHERE src.CoachID IS NOT NULL;

-- Verification: any remaining missing coaching-type rows?
SELECT
    ct.CoachingType,
    COUNT(*) AS missing_rows
FROM playerprofile p
JOIN user u ON u.UserID = p.PlayerID AND u.Role='Player'
CROSS JOIN (
    SELECT 'batting' AS CoachingType
    UNION ALL SELECT 'bowling'
    UNION ALL SELECT 'fielding'
) ct
LEFT JOIN player_skill_coach_assignment psca
       ON psca.PlayerID = p.PlayerID
      AND psca.CoachingType = ct.CoachingType
WHERE psca.PlayerID IS NULL
GROUP BY ct.CoachingType
ORDER BY ct.CoachingType;
