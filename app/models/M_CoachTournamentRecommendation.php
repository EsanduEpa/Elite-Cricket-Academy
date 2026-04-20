<?php

/**
 * M_CoachTournamentRecommendation Model
 * 
 * Handles all database operations for coach tournament recommendations
 * Manages the coach_tournament_recommendations table
 * 
 * @author Elite Academy
 * @version 1.0
 * @date April 7, 2026
 */

class M_CoachTournamentRecommendation
{
    private $db;

    /**
     * Constructor - Initialize database connection
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Add a new recommendation
     * 
     * @param int $coachId - ID of the coach making the recommendation
     * @param int $tournamentId - ID of the tournament
     * @param int $playerId - ID of the player being recommended
     * @param array $data - Array containing: role, reason, comments
     * @return array - ['success' => bool, 'id' => int, 'message' => string]
     */
    public function addRecommendation($coachId, $tournamentId, $playerId, $data)
    {
        try {
            // Validate inputs
            if (empty($coachId) || empty($tournamentId) || empty($playerId)) {
                return ['success' => false, 'message' => 'Missing required IDs'];
            }

            if (empty($data['role']) || empty($data['reason'])) {
                return ['success' => false, 'message' => 'Role and reason are required'];
            }

            // Verify tournament exists and is not completed/cancelled
            $this->db->query("
                SELECT TournamentID, Status FROM tournament 
                WHERE TournamentID = :tournamentId
            ");
            $this->db->bind(':tournamentId', $tournamentId);
            $tournament = $this->db->single();
            
            if (!$tournament) {
                return ['success' => false, 'message' => 'Tournament not found'];
            }

            if ($tournament->Status === 'completed' || $tournament->Status === 'cancelled') {
                return ['success' => false, 'message' => 'Cannot recommend players for completed/cancelled tournaments'];
            }

            // Check for duplicate recommendation
            $duplicate = $this->checkDuplicateRecommendation($tournamentId, $playerId, $coachId);
            if ($duplicate) {
                return ['success' => false, 'message' => 'You have already recommended this player for this tournament'];
            }

            // Insert recommendation
            $this->db->query("
                INSERT INTO coach_tournament_recommendations 
                (CoachID, TournamentID, PlayerID, RecommendedRole, Captaincy, WicketKeeper, Reason, Comments, Status, DateRecommended)
                VALUES (:coachId, :tournamentId, :playerId, :role, :captaincy, :wicketKeeper, :reason, :comments, 'pending', NOW())
            ");

            $this->db->bind(':coachId', $coachId);
            $this->db->bind(':tournamentId', $tournamentId);
            $this->db->bind(':playerId', $playerId);
            $this->db->bind(':role', $data['role']);
            $this->db->bind(':captaincy', $data['captaincy'] ?? 'team member');
            $this->db->bind(':wicketKeeper', $data['wicketKeeper'] ?? 'no');
            $this->db->bind(':reason', $data['reason']);
            $this->db->bind(':comments', $data['comments'] ?? null);

            if ($this->db->execute()) {
                $id = $this->db->lastInsertId();
                return ['success' => true, 'id' => $id, 'message' => 'Recommendation added successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to add recommendation'];
            }

        } catch (Exception $e) {
            error_log('Error in addRecommendation: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Get all recommendations by coach
     * 
     * @param int $coachId - ID of the coach
     * @param array $filters - Optional filters: ['status' => 'pending', 'tournamentId' => 5]
     * @return array - Array of recommendations with related data
     */
    public function getRecommendationsByCoach($coachId, $filters = [])
    {
        try {
            $query = "
                SELECT 
                    ctr.*,
                    COALESCE(NULLIF(TRIM(CONCAT_WS(' ', u_coach.FirstName, u_coach.LastName)), ''), CONCAT('Coach #', ctr.CoachID)) as CoachName,
                    t.Name as TournamentName,
                    t.tdate as TournamentDate,
                    t.Location as TournamentLocation,
                    ctr.PlayerID as PlayerID,
                    COALESCE(NULLIF(TRIM(CONCAT_WS(' ', up.FirstName, up.LastName)), ''), CONCAT('Player #', ctr.PlayerID)) as PlayerName,
                    p.BattingStyle,
                    p.BowlingStyle,
                    COALESCE(NULLIF(TRIM(CONCAT_WS(' ', u_reviewer.FirstName, u_reviewer.LastName)), ''), '') as ReviewedByName
                FROM coach_tournament_recommendations ctr
                LEFT JOIN user u_coach ON ctr.CoachID = u_coach.UserID
                JOIN tournament t ON ctr.TournamentID = t.TournamentID
                LEFT JOIN playerprofile p ON ctr.PlayerID = p.PlayerID
                LEFT JOIN user up ON ctr.PlayerID = up.UserID
                LEFT JOIN user u_reviewer ON ctr.ReviewedBy = u_reviewer.UserID
                WHERE ctr.CoachID = :coachId
            ";

            // Add filters
            if (!empty($filters['status'])) {
                $query .= " AND ctr.Status = :status";
            }
            if (!empty($filters['tournamentId'])) {
                $query .= " AND ctr.TournamentID = :tournamentId";
            }

            $query .= " ORDER BY ctr.DateRecommended DESC";

            $this->db->query($query);
            $this->db->bind(':coachId', $coachId);

            if (!empty($filters['status'])) {
                $this->db->bind(':status', $filters['status']);
            }
            if (!empty($filters['tournamentId'])) {
                $this->db->bind(':tournamentId', $filters['tournamentId']);
            }

            return $this->db->resultSet();

        } catch (Exception $e) {
            error_log('Error in getRecommendationsByCoach: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all recommendations for a tournament
     * 
     * @param int $tournamentId - ID of the tournament
     * @param array $filters - Optional filters: ['status' => 'pending']
     * @return array - Array of recommendations with related data
     */
    public function getRecommendationsByTournament($tournamentId, $filters = [])
    {
        try {
            $query = "
                SELECT 
                    ctr.*,
                    COALESCE(NULLIF(TRIM(CONCAT_WS(' ', u_coach.FirstName, u_coach.LastName)), ''), CONCAT('Coach #', ctr.CoachID)) as CoachName,
                    ctr.CoachID as CoachID,
                    t.Name as TournamentName,
                    t.tdate as TournamentDate,
                    ctr.PlayerID,
                    COALESCE(NULLIF(TRIM(CONCAT_WS(' ', up.FirstName, up.LastName)), ''), CONCAT('Player #', ctr.PlayerID)) as PlayerName,
                    p.BattingStyle,
                    p.BowlingStyle,
                    COALESCE(NULLIF(TRIM(CONCAT_WS(' ', u_reviewer.FirstName, u_reviewer.LastName)), ''), '') as ReviewedByName
                FROM coach_tournament_recommendations ctr
                LEFT JOIN user u_coach ON ctr.CoachID = u_coach.UserID
                JOIN tournament t ON ctr.TournamentID = t.TournamentID
                LEFT JOIN playerprofile p ON ctr.PlayerID = p.PlayerID
                LEFT JOIN user up ON ctr.PlayerID = up.UserID
                LEFT JOIN user u_reviewer ON ctr.ReviewedBy = u_reviewer.UserID
                WHERE ctr.TournamentID = :tournamentId
            ";

            if (!empty($filters['status'])) {
                $query .= " AND ctr.Status = :status";
            }

            $query .= " ORDER BY ctr.DateRecommended DESC";

            $this->db->query($query);
            $this->db->bind(':tournamentId', $tournamentId);

            if (!empty($filters['status'])) {
                $this->db->bind(':status', $filters['status']);
            }

            return $this->db->resultSet();

        } catch (Exception $e) {
            error_log('Error in getRecommendationsByTournament: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update a recommendation (only if pending)
     * 
     * @param int $recommendationId - ID of the recommendation
     * @param int $coachId - ID of the coach (for authorization)
     * @param array $data - Fields to update: role, reason, comments
     * @return array - ['success' => bool, 'message' => string]
     */
    public function updateRecommendation($recommendationId, $coachId, $data)
    {
        try {
            // Get current recommendation
            $this->db->query("
                SELECT * FROM coach_tournament_recommendations 
                WHERE RecommendationID = :id
            ");
            $this->db->bind(':id', $recommendationId);
            $recommendation = $this->db->single();

            if (!$recommendation) {
                return ['success' => false, 'message' => 'Recommendation not found'];
            }

            // Verify coach owns this recommendation
            if ((int)$recommendation->CoachID !== (int)$coachId) {
                return ['success' => false, 'message' => 'Unauthorized - you cannot edit this recommendation'];
            }

            // Can only edit pending recommendations
            if ((string)$recommendation->Status !== 'pending') {
                return ['success' => false, 'message' => 'Can only edit pending recommendations'];
            }

            // Update the recommendation
            $this->db->query("
                UPDATE coach_tournament_recommendations 
                SET RecommendedRole = :role,
                    Captaincy = :captaincy,
                    WicketKeeper = :wicketKeeper,
                    Reason = :reason,
                    Comments = :comments
                WHERE RecommendationID = :id
            ");

            $this->db->bind(':id', $recommendationId);
            $this->db->bind(':role', $data['role'] ?? $recommendation->RecommendedRole);
            $this->db->bind(':captaincy', $data['captaincy'] ?? ($recommendation->Captaincy ?? 'team member'));
            $this->db->bind(':wicketKeeper', $data['wicketKeeper'] ?? ($recommendation->WicketKeeper ?? 'no'));
            $this->db->bind(':reason', $data['reason'] ?? $recommendation->Reason);
            $this->db->bind(':comments', $data['comments'] ?? $recommendation->Comments);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Recommendation updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update recommendation'];
            }

        } catch (Exception $e) {
            error_log('Error in updateRecommendation: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Delete a recommendation (only if pending)
     * 
     * @param int $recommendationId - ID of the recommendation
     * @param int $coachId - ID of the coach (for authorization)
     * @return array - ['success' => bool, 'message' => string]
     */
    public function deleteRecommendation($recommendationId, $coachId)
    {
        try {
            // Get current recommendation
            $this->db->query("
                SELECT * FROM coach_tournament_recommendations 
                WHERE RecommendationID = :id
            ");
            $this->db->bind(':id', $recommendationId);
            $recommendation = $this->db->single();

            if (!$recommendation) {
                return ['success' => false, 'message' => 'Recommendation not found'];
            }

            // Verify coach owns this recommendation
            if ((int)$recommendation->CoachID !== (int)$coachId) {
                return ['success' => false, 'message' => 'Unauthorized - you cannot delete this recommendation'];
            }

            // Can only delete pending recommendations
            if ((string)$recommendation->Status !== 'pending') {
                return ['success' => false, 'message' => 'Can only delete pending recommendations'];
            }

            // Delete the recommendation
            $this->db->query("
                DELETE FROM coach_tournament_recommendations 
                WHERE RecommendationID = :id
            ");
            $this->db->bind(':id', $recommendationId);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Recommendation deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete recommendation'];
            }

        } catch (Exception $e) {
            error_log('Error in deleteRecommendation: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Approve a recommendation (admin only)
     * 
     * @param int $recommendationId - ID of the recommendation
     * @param int $adminId - ID of the admin approving
     * @param string $feedback - Optional feedback/notes
     * @return array - ['success' => bool, 'message' => string]
     */
    public function approveRecommendation($recommendationId, $adminId, $feedback = '')
    {
        try {
            // Get current recommendation
            $this->db->query("
                SELECT * FROM coach_tournament_recommendations 
                WHERE RecommendationID = :id
            ");
            $this->db->bind(':id', $recommendationId);
            $recommendation = $this->db->single();

            if (!$recommendation) {
                return ['success' => false, 'message' => 'Recommendation not found'];
            }

            if ((string)$recommendation->Status !== 'pending') {
                return ['success' => false, 'message' => 'Only pending recommendations can be approved'];
            }

            // Approve the recommendation
            $this->db->query("
                UPDATE coach_tournament_recommendations 
                SET Status = 'approved',
                    ReviewedBy = :adminId,
                    DateReviewed = NOW(),
                    AdminFeedback = :feedback
                WHERE RecommendationID = :id
            ");

            $this->db->bind(':id', $recommendationId);
            $this->db->bind(':adminId', $adminId);
            $this->db->bind(':feedback', $feedback ?? null);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Recommendation approved successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to approve recommendation'];
            }

        } catch (Exception $e) {
            error_log('Error in approveRecommendation: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Reject a recommendation with feedback (admin only)
     * 
     * @param int $recommendationId - ID of the recommendation
     * @param int $adminId - ID of the admin rejecting
     * @param string $feedback - Reason for rejection
     * @return array - ['success' => bool, 'message' => string]
     */
    public function rejectRecommendation($recommendationId, $adminId, $feedback = '')
    {
        try {
            // Get current recommendation
            $this->db->query("
                SELECT * FROM coach_tournament_recommendations 
                WHERE RecommendationID = :id
            ");
            $this->db->bind(':id', $recommendationId);
            $recommendation = $this->db->single();

            if (!$recommendation) {
                return ['success' => false, 'message' => 'Recommendation not found'];
            }

            if ((string)$recommendation->Status !== 'pending') {
                return ['success' => false, 'message' => 'Only pending recommendations can be rejected'];
            }

            if (empty($feedback)) {
                return ['success' => false, 'message' => 'Feedback is required when rejecting'];
            }

            // Reject the recommendation
            $this->db->query("
                UPDATE coach_tournament_recommendations 
                SET Status = 'rejected',
                    ReviewedBy = :adminId,
                    DateReviewed = NOW(),
                    AdminFeedback = :feedback
                WHERE RecommendationID = :id
            ");

            $this->db->bind(':id', $recommendationId);
            $this->db->bind(':adminId', $adminId);
            $this->db->bind(':feedback', $feedback);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Recommendation rejected successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to reject recommendation'];
            }

        } catch (Exception $e) {
            error_log('Error in rejectRecommendation: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    // Head coach/admin updates recommendation status for a tournament (only pending)
    public function updateStatusForTournament($recommendationId, $tournamentId, $status, $reviewedBy, $feedback = null)
    {
        $recommendationId = (int)$recommendationId;
        $tournamentId = (int)$tournamentId;
        $reviewedBy = (int)$reviewedBy;
        $status = strtolower(trim((string)$status));

        if ($recommendationId <= 0 || $tournamentId <= 0 || $reviewedBy <= 0) {
            return false;
        }

        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }

        $this->db->query(
            'UPDATE coach_tournament_recommendations
             SET Status = :status,
                 ReviewedBy = :reviewer,
                 DateReviewed = NOW(),
                 AdminFeedback = :feedback
             WHERE RecommendationID = :id
               AND TournamentID = :tid
               AND Status = "pending"'
        );
        $this->db->bind(':status', $status);
        $this->db->bind(':reviewer', $reviewedBy);
        $this->db->bind(':feedback', $feedback !== '' ? $feedback : null);
        $this->db->bind(':id', $recommendationId);
        $this->db->bind(':tid', $tournamentId);

        $ok = $this->db->execute();
        if (!$ok) {
            return false;
        }

        return $this->db->rowCount() > 0;
    }

    /**
     * Check if a duplicate recommendation exists
     * 
     * @param int $tournamentId - ID of the tournament
     * @param int $playerId - ID of the player
     * @param int $coachId - ID of the coach
     * @return bool - True if duplicate exists, false otherwise
     */
    public function checkDuplicateRecommendation($tournamentId, $playerId, $coachId)
    {
        try {
            $this->db->query("
                SELECT COUNT(*) as count FROM coach_tournament_recommendations 
                WHERE TournamentID = :tournamentId 
                AND PlayerID = :playerId 
                AND CoachID = :coachId
            ");

            $this->db->bind(':tournamentId', $tournamentId);
            $this->db->bind(':playerId', $playerId);
            $this->db->bind(':coachId', $coachId);

            $result = $this->db->resultSet();
            return !empty($result) && $result[0]->count > 0;

        } catch (Exception $e) {
            error_log('Error in checkDuplicateRecommendation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all players assigned to a coach
     * 
     * @param int $coachId - ID of the coach
     * @return array - Array of assigned players with stats
     */
    public function getCoachAssignedPlayers($coachId)
    {
        try {
            $this->db->query("
                SELECT 
                    p.PlayerID,
                    CONCAT(u.FirstName, ' ', u.LastName) as PlayerName,
                    u.UserID,
                    p.BattingStyle,
                    p.BowlingStyle,
                    pca.AssignmentType,
                    pca.Status as AssignmentStatus,
                    (SELECT COUNT(*) FROM playertournamentstats WHERE PlayerID = p.PlayerID) as TournamentCount
                FROM playercoachassignment pca
                JOIN playerprofile p ON pca.PlayerID = p.PlayerID
                JOIN user u ON p.PlayerID = u.UserID
                WHERE pca.CoachID = :coachId 
                AND pca.Status = 'active'
                AND u.Status = 'active'
                ORDER BY u.FirstName ASC
            ");

            $this->db->bind(':coachId', $coachId);
            return $this->db->resultSet();

        } catch (Exception $e) {
            error_log('Error in getCoachAssignedPlayers: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recommendation details with all related data
     * 
     * @param int $recommendationId - ID of the recommendation
     * @return array - Detailed recommendation data
     */
    public function getRecommendationDetails($recommendationId)
    {
        try {
            $this->db->query("
                SELECT 
                    ctr.*,
                    u_coach.Name as CoachName,
                    u_coach.Email as CoachEmail,
                    t.Name as TournamentName,
                    t.tdate as TournamentDate,
                    t.Location as TournamentLocation,
                    t.Status as TournamentStatus,
                    ctr.PlayerID,
                    COALESCE(up.Name, CONCAT('Player #', ctr.PlayerID)) as PlayerName,
                    up.Email as PlayerEmail,
                    p.BattingStyle,
                    p.BowlingStyle,
                    u_reviewer.Name as ReviewedByName
                FROM coach_tournament_recommendations ctr
                JOIN user u_coach ON ctr.CoachID = u_coach.UserID
                JOIN tournament t ON ctr.TournamentID = t.TournamentID
                LEFT JOIN playerprofile p ON ctr.PlayerID = p.PlayerID
                LEFT JOIN user up ON ctr.PlayerID = up.UserID
                LEFT JOIN user u_reviewer ON ctr.ReviewedBy = u_reviewer.UserID
                WHERE ctr.RecommendationID = :id
            ");

            $this->db->bind(':id', $recommendationId);
            return $this->db->single();

        } catch (Exception $e) {
            error_log('Error in getRecommendationDetails: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get statistics for recommendations
     * 
     * @param int $coachId - Optional: get stats for specific coach
     * @return array - Statistics with counts by status
     */
    public function getRecommendationStats($coachId = null)
    {
        try {
            $query = "
                SELECT 
                    Status,
                    COUNT(*) as count
                FROM coach_tournament_recommendations
            ";

            if (!empty($coachId)) {
                $query .= " WHERE CoachID = :coachId";
            }

            $query .= " GROUP BY Status";

            $this->db->query($query);

            if (!empty($coachId)) {
                $this->db->bind(':coachId', $coachId);
            }

            $results = $this->db->resultSet();
            
            // Format results
            $stats = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'confirmed' => 0, 'total' => 0];
            foreach ($results as $row) {
                $statusKey = strtolower((string)($row->Status ?? ''));
                if ($statusKey !== '') {
                    $stats[$statusKey] = (int)$row->count;
                    $stats['total'] += (int)$row->count;
                }
            }

            return $stats;

        } catch (Exception $e) {
            error_log('Error in getRecommendationStats: ' . $e->getMessage());
            return ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'confirmed' => 0, 'total' => 0];
        }
    }

    /**
     * Get pending recommendations count for coach
     * 
     * @param int $coachId - ID of the coach
     * @return int - Count of pending recommendations
     */
    public function getPendingCount($coachId)
    {
        try {
            $this->db->query("
                SELECT COUNT(*) as count FROM coach_tournament_recommendations 
                WHERE CoachID = :coachId AND Status = 'pending'
            ");
            $this->db->bind(':coachId', $coachId);
            $result = $this->db->single();
            return $result->count ?? 0;

        } catch (Exception $e) {
            error_log('Error in getPendingCount: ' . $e->getMessage());
            return 0;
        }
    }
}

?>
