<?php

class M_TournamentJoinRequest
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createRequest($tournamentId, $playerId, $message)
    {
        $this->db->query(
            'INSERT INTO tournament_join_request (TournamentID, PlayerID, Message)
             VALUES (:tid, :pid, :msg)'
        );
        $this->db->bind(':tid', $tournamentId);
        $this->db->bind(':pid', $playerId);
        $this->db->bind(':msg', $message);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function getRequestsByTournament($tournamentId)
    {
        $this->db->query(
            'SELECT tjr.*, CONCAT(u.FirstName, \' \', u.LastName) AS Name, u.Email,
                    u.ProfileImage,
                    COUNT(DISTINCT ctr.RecommendationID) AS CoachRecs,
                    COUNT(DISTINCT ttr.RecommendationID) AS TrainerRecs
             FROM tournament_join_request tjr
             JOIN user u ON u.UserID = tjr.PlayerID
             LEFT JOIN coach_tournament_recommendations ctr
                    ON ctr.PlayerID = tjr.PlayerID AND ctr.TournamentID = tjr.TournamentID
             LEFT JOIN trainer_tournament_recommendations ttr
                    ON ttr.PlayerID = tjr.PlayerID AND ttr.TournamentID = tjr.TournamentID
             WHERE tjr.TournamentID = :tid
             GROUP BY tjr.RequestID
             ORDER BY tjr.RequestedAt DESC'
        );
        $this->db->bind(':tid', $tournamentId);
        return $this->db->resultset();
    }

    public function getRequestByPlayer($tournamentId, $playerId)
    {
        $this->db->query(
            'SELECT * FROM tournament_join_request
             WHERE TournamentID = :tid AND PlayerID = :pid'
        );
        $this->db->bind(':tid', $tournamentId);
        $this->db->bind(':pid', $playerId);
        return $this->db->single();
    }

    public function updateStatus($requestId, $status, $reviewedBy, $notes = null)
    {
        $this->db->query(
            'UPDATE tournament_join_request
             SET Status = :status, ReviewedBy = :reviewedby, ReviewNotes = :notes, ReviewedAt = NOW()
             WHERE RequestID = :id'
        );
        $this->db->bind(':status',     $status);
        $this->db->bind(':reviewedby', $reviewedBy);
        $this->db->bind(':notes',      $notes);
        $this->db->bind(':id',         $requestId);
        return $this->db->execute();
    }

    // Player withdraws their own pending request
    public function cancelByPlayer($requestId, $playerId)
    {
        $this->db->query(
            'DELETE FROM tournament_join_request
             WHERE RequestID = :id AND PlayerID = :pid AND Status = "pending"'
        );
        $this->db->bind(':id',  $requestId);
        $this->db->bind(':pid', $playerId);
        return $this->db->execute();
    }

    // Batch-reject all pending requests when a tournament is cancelled
    public function rejectAllPending($tournamentId, $reviewedBy)
    {
        $this->db->query(
            'UPDATE tournament_join_request
             SET Status = "rejected", ReviewedBy = :reviewedby, ReviewNotes = "Tournament cancelled", ReviewedAt = NOW()
             WHERE TournamentID = :tid AND Status = "pending"'
        );
        $this->db->bind(':reviewedby', $reviewedBy);
        $this->db->bind(':tid',        $tournamentId);
        return $this->db->execute();
    }

    public function hasExistingRequest($tournamentId, $playerId)
    {
        $this->db->query(
            'SELECT COUNT(*) AS cnt FROM tournament_join_request
             WHERE TournamentID = :tid AND PlayerID = :pid'
        );
        $this->db->bind(':tid', $tournamentId);
        $this->db->bind(':pid', $playerId);
        $row = $this->db->single();
        return $row && $row->cnt > 0;
    }

    public function getRequestById($requestId)
    {
        $this->db->query('SELECT * FROM tournament_join_request WHERE RequestID = :id');
        $this->db->bind(':id', $requestId);
        return $this->db->single();
    }

    public function getCountByTournament($tournamentId, $status = null)
    {
        if ($status) {
            $this->db->query(
                'SELECT COUNT(*) AS cnt FROM tournament_join_request WHERE TournamentID = :tid AND Status = :status'
            );
            $this->db->bind(':status', $status);
        } else {
            $this->db->query(
                'SELECT COUNT(*) AS cnt FROM tournament_join_request WHERE TournamentID = :tid'
            );
        }
        $this->db->bind(':tid', $tournamentId);
        $row = $this->db->single();
        return $row ? (int)$row->cnt : 0;
    }
}
