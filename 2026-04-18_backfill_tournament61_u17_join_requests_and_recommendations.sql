-- 2026-04-18 Backfill
-- Ensure TournamentID=61 (U17) has join-request and coach-recommendation records
-- for the accurate U17 squad players currently present in `tournamentplayer`.
--
-- This script is idempotent: it inserts ONLY missing records.

START TRANSACTION;

SET @TournamentID := 61;

-- Seed actors (existing in `user` table)
SET @AdminUserID := 1;
SET @HeadCoachUserID := 14;
SET @CoachUserID := 3;

-- Sanity: confirm tournament
SELECT TournamentID, Name, AgeGroup, tdate, Status
FROM tournament
WHERE TournamentID = @TournamentID;

-- -----------------------------------------------------------------------------
-- 1) Backfill tournament_join_request (approved)
-- Unique key: (TournamentID, PlayerID)
-- -----------------------------------------------------------------------------
INSERT INTO tournament_join_request
  (TournamentID, PlayerID, Message, Status, ReviewedBy, ReviewNotes, RequestedAt, ReviewedAt)
SELECT
  tp.TournamentID,
  tp.PlayerID,
  'Auto-backfill: U17 squad member (system).',
  'approved',
  @AdminUserID,
  'Auto-approved: player is in confirmed tournament squad.',
  COALESCE(tp.SelectedAt, NOW()),
  NOW()
FROM tournamentplayer tp
WHERE tp.TournamentID = @TournamentID
  AND NOT EXISTS (
    SELECT 1
    FROM tournament_join_request tjr
    WHERE tjr.TournamentID = tp.TournamentID
      AND tjr.PlayerID = tp.PlayerID
  );

-- -----------------------------------------------------------------------------
-- 2) Backfill coach_tournament_recommendations (approved)
-- Unique key: (CoachID, TournamentID, PlayerID)
-- -----------------------------------------------------------------------------
INSERT INTO coach_tournament_recommendations
  (CoachID, TournamentID, PlayerID, RecommendedRole, Reason, Comments, Status, ReviewedBy, ReviewFeedback, DateRecommended, DateReviewed)
SELECT
  @CoachUserID,
  tp.TournamentID,
  tp.PlayerID,
  COALESCE(tp.RoleInTeam, 'Player'),
  'Auto-backfill: player is in confirmed squad for this tournament.',
  CONCAT('Backfilled from tournamentplayer on ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')),
  'approved',
  @HeadCoachUserID,
  'Auto-approved (backfill).',
  COALESCE(tp.SelectedAt, NOW()),
  NOW()
FROM tournamentplayer tp
WHERE tp.TournamentID = @TournamentID
  AND NOT EXISTS (
    SELECT 1
    FROM coach_tournament_recommendations ctr
    WHERE ctr.CoachID = @CoachUserID
      AND ctr.TournamentID = tp.TournamentID
      AND ctr.PlayerID = tp.PlayerID
  );

-- -----------------------------------------------------------------------------
-- 3) Verification counts
-- -----------------------------------------------------------------------------
SELECT
  (SELECT COUNT(DISTINCT PlayerID) FROM tournamentplayer WHERE TournamentID=@TournamentID) AS squad_players,
  (SELECT COUNT(DISTINCT PlayerID) FROM tournament_join_request WHERE TournamentID=@TournamentID) AS join_request_players,
  (SELECT COUNT(DISTINCT PlayerID) FROM coach_tournament_recommendations WHERE TournamentID=@TournamentID AND CoachID=@CoachUserID) AS recommendation_players;

COMMIT;
