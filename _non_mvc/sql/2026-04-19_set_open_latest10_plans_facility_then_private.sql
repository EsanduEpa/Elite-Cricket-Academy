-- 2026-04-19
-- Set the latest 10 OPEN age-group players' active membership plan:
-- - Latest 5 -> facility_only
-- - Next 5   -> private
--
-- Definition of "latest": ORDER BY user.DateJoined DESC, user.UserID DESC
-- Safe to re-run.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_open_latest10;
CREATE TEMPORARY TABLE tmp_open_latest10 (
  rn INT NOT NULL,
  PlayerID INT NOT NULL,
  DateJoined DATETIME NULL,
  PRIMARY KEY (PlayerID)
);

SET @rn := 0;
INSERT INTO tmp_open_latest10 (rn, PlayerID, DateJoined)
SELECT (@rn := @rn + 1) AS rn,
       x.PlayerID,
       x.DateJoined
FROM (
  SELECT DISTINCT u.UserID AS PlayerID,
         u.DateJoined
  FROM user u
  JOIN player_skill_coach_assignment ps
    ON ps.PlayerID = u.UserID
   AND ps.AgeGroup = 'Open'
  WHERE u.Role = 'Player'
  ORDER BY u.DateJoined DESC, u.UserID DESC
  LIMIT 10
) x;

-- Preview targets (before)
SELECT
  t.rn,
  t.PlayerID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  t.DateJoined,
  mp.PlanName AS CurrentPlan,
  ps.SubscriptionID,
  ps.StartDate,
  ps.MonthlyFee AS CurrentMonthlyFee,
  CASE WHEN t.rn <= 5 THEN 'facility_only' ELSE 'private' END AS TargetPlan
FROM tmp_open_latest10 t
JOIN user u ON u.UserID = t.PlayerID
LEFT JOIN playersubscription ps ON ps.PlayerID = t.PlayerID AND ps.Status = 'active'
LEFT JOIN membershipplan mp ON mp.PlanID = ps.PlanID
ORDER BY t.rn;

-- Apply updates (active subscriptions only)
UPDATE playersubscription ps
JOIN tmp_open_latest10 t ON t.PlayerID = ps.PlayerID
JOIN membershipplan mp_fac ON mp_fac.PlanName = 'facility_only'
JOIN membershipplan mp_priv ON mp_priv.PlanName = 'private'
SET
  ps.PlanID = CASE WHEN t.rn <= 5 THEN mp_fac.PlanID ELSE mp_priv.PlanID END,
  ps.MonthlyFee = CASE WHEN t.rn <= 5 THEN mp_fac.MonthlyFee ELSE mp_priv.MonthlyFee END,
  ps.UpdatedAt = NOW()
WHERE ps.Status = 'active';

-- Create missing active subscriptions (if any)
INSERT INTO playersubscription
  (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal, CreatedAt, UpdatedAt)
SELECT
  t.PlayerID,
  CASE WHEN t.rn <= 5 THEN mp_fac.PlanID ELSE mp_priv.PlanID END AS PlanID,
  CURDATE() AS StartDate,
  NULL AS EndDate,
  'active' AS Status,
  CASE WHEN t.rn <= 5 THEN mp_fac.MonthlyFee ELSE mp_priv.MonthlyFee END AS MonthlyFee,
  1 AS PaymentDay,
  1 AS AutoRenewal,
  NOW() AS CreatedAt,
  NOW() AS UpdatedAt
FROM tmp_open_latest10 t
LEFT JOIN playersubscription ps ON ps.PlayerID = t.PlayerID AND ps.Status = 'active'
JOIN membershipplan mp_fac ON mp_fac.PlanName = 'facility_only'
JOIN membershipplan mp_priv ON mp_priv.PlanName = 'private'
WHERE ps.SubscriptionID IS NULL;

-- Preview targets (after)
SELECT
  t.rn,
  t.PlayerID,
  CONCAT(u.FirstName, ' ', u.LastName) AS PlayerName,
  mp.PlanName AS ActivePlan,
  ps.SubscriptionID,
  ps.StartDate,
  ps.MonthlyFee AS MonthlyFee
FROM tmp_open_latest10 t
JOIN user u ON u.UserID = t.PlayerID
LEFT JOIN playersubscription ps ON ps.PlayerID = t.PlayerID AND ps.Status = 'active'
LEFT JOIN membershipplan mp ON mp.PlanID = ps.PlanID
ORDER BY t.rn;

COMMIT;
