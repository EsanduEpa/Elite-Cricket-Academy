-- 2026-04-17 Fix-up
-- Align existing tournament records with lifecycle validations:
-- - If tournament date already passed and there is no activity, cancel it.
-- - If tournament is too close (<50 days) and has no matches/squad/results, reschedule it.
-- - If tournament is marked ongoing before its date, reset to registration_open.
--
-- Assumptions:
-- - Safe to reschedule only when MatchCount=0, SquadCount=0, ResultCount=0.
-- - Uses CURDATE() as "today".

START TRANSACTION;

-- Cancel past-dated tournaments that have no activity
UPDATE tournament t
SET
  t.Status = 'cancelled',
  t.CancelReason = 'Auto-fix: tournament date passed with no recorded activity.',
  t.IsTeamAnnounced = 0
WHERE t.TournamentID IN (7, 9, 10)
  AND t.tdate < CURDATE()
  AND t.Status IN ('created','registration_open','registration_closed','team_announced')
  AND (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournamentplayer tp WHERE tp.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_join_request tjr WHERE tjr.TournamentID = t.TournamentID) = 0;

-- Reschedule tournaments that violate the "create at least 50 days ahead" expectation
-- (only when there is no match/squad/result activity)
UPDATE tournament t
SET
  t.tdate = DATE_ADD(CURDATE(), INTERVAL 50 DAY),
  t.RegistrationDeadline = DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 50 DAY), INTERVAL 1 MONTH)
WHERE t.TournamentID = 60
  AND DATEDIFF(t.tdate, CURDATE()) BETWEEN 0 AND 49
  AND t.Status IN ('created','registration_open','registration_closed','team_announced')
  AND (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournamentplayer tp WHERE tp.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID) = 0;

UPDATE tournament t
SET
  t.tdate = DATE_ADD(CURDATE(), INTERVAL 51 DAY),
  t.RegistrationDeadline = DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 51 DAY), INTERVAL 1 MONTH)
WHERE t.TournamentID = 8
  AND DATEDIFF(t.tdate, CURDATE()) BETWEEN 0 AND 49
  AND t.Status IN ('created','registration_open','registration_closed','team_announced')
  AND (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournamentplayer tp WHERE tp.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID) = 0;

UPDATE tournament t
SET
  t.tdate = DATE_ADD(CURDATE(), INTERVAL 52 DAY),
  t.RegistrationDeadline = DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 52 DAY), INTERVAL 1 MONTH)
WHERE t.TournamentID = 34
  AND DATEDIFF(t.tdate, CURDATE()) BETWEEN 0 AND 49
  AND t.Status IN ('created','registration_open','registration_closed','team_announced')
  AND (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournamentplayer tp WHERE tp.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_join_request tjr WHERE tjr.TournamentID = t.TournamentID) = 0;

-- Fix tournament marked ongoing too early (no activity indicates status is incorrect)
UPDATE tournament t
SET
  t.Status = 'registration_open',
  t.IsTeamAnnounced = 0,
  t.CancelReason = NULL
WHERE t.TournamentID = 6
  AND t.Status = 'ongoing'
  AND t.tdate > CURDATE()
  AND (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournamentplayer tp WHERE tp.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_result tr WHERE tr.TournamentID = t.TournamentID) = 0
  AND (SELECT COUNT(*) FROM tournament_join_request tjr WHERE tjr.TournamentID = t.TournamentID) = 0;

COMMIT;
