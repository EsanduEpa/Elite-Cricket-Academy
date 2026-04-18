<?php

class M_Tournament
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Returns all tournaments, optionally filtered by status
    public function getTournaments($status = null)
    {
        if ($status) {
            $this->db->query(
                'SELECT t.*, CONCAT(u.FirstName, \' \', u.LastName) AS CreatedByName
                 FROM tournament t
                 LEFT JOIN user u ON u.UserID = t.CreatedBy
                 WHERE t.Status = :status
                 ORDER BY t.tdate DESC'
            );
            $this->db->bind(':status', $status);
        } else {
            $this->db->query(
                'SELECT t.*, CONCAT(u.FirstName, \' \', u.LastName) AS CreatedByName
                 FROM tournament t
                 LEFT JOIN user u ON u.UserID = t.CreatedBy
                 ORDER BY t.tdate DESC'
            );
        }
        return $this->db->resultset();
    }

    // Returns tournaments visible to players/staff (not cancelled, not created)
    public function getPublicTournaments()
    {
        $this->db->query(
            "SELECT t.* 
             FROM tournament t
             WHERE t.Status NOT IN ('cancelled')
             ORDER BY t.tdate DESC"
        );
        return $this->db->resultset();
    }

    public function getTournamentsForCalendar(string $from, string $to): array
    {
        $this->db->query(
            'SELECT t.TournamentID,
                    t.Name,
                    t.AgeGroup,
                    t.Format,
                    t.Description,
                    t.tdate,
                    t.Location,
                    t.Status,
                    t.PrizePool
             FROM tournament t
             WHERE t.tdate BETWEEN :from AND :to
             ORDER BY t.tdate ASC, t.Name ASC'
        );
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);

        return $this->db->resultset();
    }

    public function getUpcomingReminderTournaments(string $date): array
    {
        $this->db->query(
            "SELECT t.*
             FROM tournament t
             WHERE DATE(t.tdate) = :date
               AND t.Status NOT IN ('cancelled', 'completed', 'created')
             ORDER BY t.tdate ASC, t.Name ASC"
        );
        $this->db->bind(':date', $date);
        return $this->db->resultset();
    }

    public function getTournamentById($id)
    {
        $this->db->query(
            'SELECT t.*, CONCAT(u.FirstName, \' \', u.LastName) AS CreatedByName
             FROM tournament t
             LEFT JOIN user u ON u.UserID = t.CreatedBy
             WHERE t.TournamentID = :id'
        );
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createTournament($data)
    {
        $this->db->query(
            'INSERT INTO tournament 
             (Name, AgeGroup, Format, Description, tdate, RegistrationDeadline, MaxPlayers, Location, PrizePool, CreatedBy, Status)
             VALUES
             (:name, :agegroup, :format, :desc, :tdate, :regdeadline, :maxplayers, :location, :prizepool, :createdby, :status)'
        );
        $this->db->bind(':name',        $data['name']);
        $this->db->bind(':agegroup',    $data['age_group'] ?? null);
        $this->db->bind(':format',      $data['format'] ?? 'T20');
        $this->db->bind(':desc',        $data['description'] ?? null);
        $this->db->bind(':tdate',       $data['tdate']);
        $this->db->bind(':regdeadline', $data['registration_deadline'] ?? null);
        $this->db->bind(':maxplayers',  $data['max_players'] ?? null);
        $this->db->bind(':location',    $data['location'] ?? null);
        $this->db->bind(':prizepool',   $data['prize_pool'] ?? 0);
        $this->db->bind(':createdby',   $data['created_by']);
        $this->db->bind(':status',      $data['status'] ?? 'created');
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function updateTournament($id, $data)
    {
        $this->db->query(
            'UPDATE tournament SET
             Name = :name,
             AgeGroup = :agegroup,
             Format = :format,
             Description = :desc,
             tdate = :tdate,
             RegistrationDeadline = :regdeadline,
             MaxPlayers = :maxplayers,
             Location = :location,
             PrizePool = :prizepool
             WHERE TournamentID = :id'
        );
        $this->db->bind(':name',        $data['name']);
        $this->db->bind(':agegroup',    $data['age_group'] ?? null);
        $this->db->bind(':format',      $data['format'] ?? 'T20');
        $this->db->bind(':desc',        $data['description'] ?? null);
        $this->db->bind(':tdate',       $data['tdate']);
        $this->db->bind(':regdeadline', $data['registration_deadline'] ?? null);
        $this->db->bind(':maxplayers',  $data['max_players'] ?? null);
        $this->db->bind(':location',    $data['location'] ?? null);
        $this->db->bind(':prizepool',   $data['prize_pool'] ?? 0);
        $this->db->bind(':id',          $id);
        return $this->db->execute();
    }

    public function updateStatus($id, $status, $reason = null)
    {
        $this->db->query(
            'UPDATE tournament SET Status = :status, CancelReason = :reason WHERE TournamentID = :id'
        );
        $this->db->bind(':status', $status);
        $this->db->bind(':reason', $reason);
        $this->db->bind(':id',     $id);
        return $this->db->execute();
    }

    public function announceTeam($id)
    {
        $this->db->query(
            'UPDATE tournament SET IsTeamAnnounced = 1, Status = "team_announced" WHERE TournamentID = :id'
        );
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Returns all tournamentplayer rows with player name and role
    public function getTeam($tournamentId)
    {
        $this->db->query(
            'SELECT tp.*, CONCAT(u.FirstName, \' \', u.LastName) AS Name, u.Email,
                    u.ProfileImage
             FROM tournamentplayer tp
             JOIN user u ON u.UserID = tp.PlayerID
             WHERE tp.TournamentID = :tid
             ORDER BY FIELD(tp.RoleInTeam,"Captain","Vice-Captain","Wicket-Keeper","Batsman","Bowler","All-Rounder")'
        );
        $this->db->bind(':tid', $tournamentId);
        return $this->db->resultset();
    }

    // Add a player to the draft squad
    public function addPlayerToTeam($data)
    {
        $this->db->query(
            'INSERT INTO tournamentplayer (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy, SelectionStatus, SelectedAt)
             VALUES (:tid, :pid, :team, :role, :selectedby, "draft", NOW())
             ON DUPLICATE KEY UPDATE RoleInTeam = :role, SelectedBy = :selectedby, SelectionStatus = "draft", SelectedAt = NOW()'
        );
        $this->db->bind(':tid',        $data['tournament_id']);
        $this->db->bind(':pid',        $data['player_id']);
        $this->db->bind(':team',       $data['team'] ?? 'Academy Team');
        $this->db->bind(':role',       $data['role'] ?? null);
        $this->db->bind(':selectedby', $data['selected_by']);
        return $this->db->execute();
    }

    public function removePlayerFromTeam($tournamentId, $playerId)
    {
        $this->db->query(
            'DELETE FROM tournamentplayer WHERE TournamentID = :tid AND PlayerID = :pid'
        );
        $this->db->bind(':tid', $tournamentId);
        $this->db->bind(':pid', $playerId);
        return $this->db->execute();
    }

    // Confirm all draft rows — called by head coach after finalization
    public function confirmTeam($tournamentId, $headCoachUserId)
    {
        $this->db->query(
            'UPDATE tournamentplayer SET SelectionStatus = "confirmed", SelectedBy = :coach
             WHERE TournamentID = :tid AND SelectionStatus = "draft"'
        );
        $this->db->bind(':coach', $headCoachUserId);
        $this->db->bind(':tid',   $tournamentId);
        return $this->db->execute();
    }

    public function clearTeamDraft($tournamentId)
    {
        $this->db->query('DELETE FROM tournamentplayer WHERE TournamentID = :tid');
        $this->db->bind(':tid', $tournamentId);
        $this->db->execute();
        // Also reset announced flag so the team is hidden until re-confirmed
        $this->db->query('UPDATE tournament SET IsTeamAnnounced = 0 WHERE TournamentID = :tid');
        $this->db->bind(':tid', $tournamentId);
        return $this->db->execute();
    }

    public function isInTeam($tournamentId, $playerId)
    {
        $this->db->query(
            'SELECT COUNT(*) AS cnt FROM tournamentplayer WHERE TournamentID = :tid AND PlayerID = :pid'
        );
        $this->db->bind(':tid', $tournamentId);
        $this->db->bind(':pid', $playerId);
        $row = $this->db->single();
        return $row && $row->cnt > 0;
    }

    // Summary: join request count + coach/trainer recommendation counts per player
    public function getPlayerSelectionSummary($tournamentId)
    {
        $this->db->query(
            'SELECT u.UserID, CONCAT(u.FirstName, \' \', u.LastName) AS PlayerName,
                    u.Email,
                    u.ProfileImage,
                    COUNT(DISTINCT ctr.RecommendationID) AS CoachRecs,
                    COUNT(DISTINCT ttr.RecommendationID) AS TrainerRecs,
                    MAX(CASE WHEN tjr.RequestID IS NOT NULL THEN 1 ELSE 0 END) AS SelfNominated,
                    MAX(tjr.Status) AS JoinRequestStatus,
                    MAX(CASE WHEN tp.PlayerID IS NOT NULL THEN 1 ELSE 0 END) AS AlreadySelected
             FROM user u
             LEFT JOIN coach_tournament_recommendations ctr
                    ON ctr.PlayerID = u.UserID AND ctr.TournamentID = :tid1
             LEFT JOIN trainer_tournament_recommendations ttr
                    ON ttr.PlayerID = u.UserID AND ttr.TournamentID = :tid2
             LEFT JOIN tournament_join_request tjr
                    ON tjr.PlayerID = u.UserID AND tjr.TournamentID = :tid3
             LEFT JOIN tournamentplayer tp
                    ON tp.PlayerID = u.UserID AND tp.TournamentID = :tid4
             WHERE u.Role = "Player"
               AND (ctr.RecommendationID IS NOT NULL
                    OR ttr.RecommendationID IS NOT NULL
                    OR tjr.RequestID IS NOT NULL)
             GROUP BY u.UserID
             ORDER BY (COUNT(DISTINCT ctr.RecommendationID) + COUNT(DISTINCT ttr.RecommendationID)) DESC,
                      SelfNominated DESC'
        );
        $this->db->bind(':tid1', $tournamentId);
        $this->db->bind(':tid2', $tournamentId);
        $this->db->bind(':tid3', $tournamentId);
        $this->db->bind(':tid4', $tournamentId);
        return $this->db->resultset();
    }

    // Count join requests for a tournament
    public function getJoinRequestCount($tournamentId)
    {
        $this->db->query(
            'SELECT COUNT(*) AS cnt FROM tournament_join_request WHERE TournamentID = :tid AND Status = "pending"'
        );
        $this->db->bind(':tid', $tournamentId);
        $row = $this->db->single();
        return $row ? (int)$row->cnt : 0;
    }
}
