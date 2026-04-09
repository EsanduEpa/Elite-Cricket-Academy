<?php

class M_TrainerTournamentRecommendation
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Add a new trainer recommendation
    // Trainers can recommend any active player (not restricted to assignment)
    public function addRecommendation($trainerId, $tournamentId, $playerId, $data)
    {
        if ($this->checkDuplicate($tournamentId, $playerId, $trainerId)) {
            return ['success' => false, 'message' => 'You have already recommended this player for this tournament.'];
        }

        try {
            $this->db->query(
                'INSERT INTO trainer_tournament_recommendations
                 (TrainerID, TournamentID, PlayerID, RecommendedRole, Reason, Comments)
                 VALUES (:trainer, :tid, :pid, :role, :reason, :comments)'
            );
            $this->db->bind(':trainer',  $trainerId);
            $this->db->bind(':tid',      $tournamentId);
            $this->db->bind(':pid',      $playerId);
            $this->db->bind(':role',     $data['role'] ?? null);
            $this->db->bind(':reason',   $data['reason'] ?? null);
            $this->db->bind(':comments', $data['comments'] ?? null);
            $this->db->execute();
            return ['success' => true, 'id' => $this->db->lastInsertId(), 'message' => 'Recommendation submitted successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to save recommendation.'];
        }
    }

    // Get all recommendations submitted by this trainer
    public function getRecommendationsByTrainer($trainerId)
    {
        $this->db->query(
            'SELECT ttr.*,
                    t.Name AS TournamentName, t.tdate, t.Status AS TournamentStatus, t.Format,
                    u.FirstName, u.LastName, u.Email
             FROM trainer_tournament_recommendations ttr
             JOIN tournament t ON t.TournamentID = ttr.TournamentID
             JOIN user u ON u.UserID = ttr.PlayerID
             WHERE ttr.TrainerID = :trainer
             ORDER BY ttr.DateRecommended DESC'
        );
        $this->db->bind(':trainer', $trainerId);
        return $this->db->resultset();
    }

    // Get all recommendations for a tournament (used by head coach finalization view)
    public function getRecommendationsByTournament($tournamentId)
    {
        $this->db->query(
            'SELECT ttr.*,
                    u_player.Name AS PlayerName,
                    u_trainer.Name AS TrainerName
             FROM trainer_tournament_recommendations ttr
             JOIN user u_player ON u_player.UserID = ttr.PlayerID
             JOIN user u_trainer ON u_trainer.UserID = ttr.TrainerID
             WHERE ttr.TournamentID = :tid
             ORDER BY ttr.DateRecommended DESC'
        );
        $this->db->bind(':tid', $tournamentId);
        return $this->db->resultset();
    }

    // Head coach updates recommendation status (reviewed/confirmed/rejected)
    public function updateStatus($recId, $status, $reviewedBy)
    {
        $this->db->query(
            'UPDATE trainer_tournament_recommendations
             SET Status = :status, ReviewedBy = :reviewer, DateReviewed = NOW()
             WHERE RecommendationID = :id'
        );
        $this->db->bind(':status',   $status);
        $this->db->bind(':reviewer', $reviewedBy);
        $this->db->bind(':id',       $recId);
        return $this->db->execute();
    }

    public function checkDuplicate($tournamentId, $playerId, $trainerId)
    {
        $this->db->query(
            'SELECT COUNT(*) AS cnt FROM trainer_tournament_recommendations
             WHERE TournamentID = :tid AND PlayerID = :pid AND TrainerID = :trainer'
        );
        $this->db->bind(':tid',     $tournamentId);
        $this->db->bind(':pid',     $playerId);
        $this->db->bind(':trainer', $trainerId);
        $row = $this->db->single();
        return $row && $row->cnt > 0;
    }

    public function getCountByTournament($tournamentId)
    {
        $this->db->query(
            'SELECT COUNT(*) AS cnt FROM trainer_tournament_recommendations WHERE TournamentID = :tid'
        );
        $this->db->bind(':tid', $tournamentId);
        $row = $this->db->single();
        return $row ? (int)$row->cnt : 0;
    }

    // All active players available for trainer to recommend
    public function getAllActivePlayers()
    {
        $this->db->query(
            'SELECT u.UserID, u.FirstName, u.LastName, u.Email, pp.ProfileImage
             FROM user u
             JOIN playerprofile pp ON pp.PlayerID = u.UserID
             WHERE u.Role = "Player"
             ORDER BY u.FirstName, u.LastName'
        );
        return $this->db->resultset();
    }
}
