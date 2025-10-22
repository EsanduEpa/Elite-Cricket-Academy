<?php

class M_Achievement {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all achievements for a specific player
    public function getAchievementsByPlayer($playerId) {
        try {
            $this->db->query('SELECT * FROM Achievements 
                             WHERE PlayerID = :player_id 
                             ORDER BY Date DESC');
            
            $this->db->bind(':player_id', $playerId);
            $result = $this->db->resultSet();
            
            // Log for debugging
            error_log("Achievement query executed for player ID: $playerId, Results count: " . count($result));
            
            return $result;
        } catch (Exception $e) {
            error_log("Achievement model error: " . $e->getMessage());
            throw $e; // Re-throw to let controller handle
        }
    }

    // Get single achievement by ID
    public function getAchievementById($achievementId) {
        $this->db->query('SELECT * FROM Achievements WHERE AchievementID = :achievement_id');
        $this->db->bind(':achievement_id', $achievementId);
        return $this->db->single();
    }

    // Add new achievement
    public function addAchievement($data) {
        $this->db->query('INSERT INTO Achievements (
            PlayerID, 
            Date, 
            MatchName, 
            Tournament, 
            Achievement, 
            VerifiedStatus,
            CreatedAt
        ) VALUES (
            :player_id, 
            :date, 
            :match_name, 
            :tournament, 
            :achievement, 
            :verified_status,
            NOW()
        )');
        
        // Bind values
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':match_name', $data['match_name']);
        $this->db->bind(':tournament', $data['tournament']);
        $this->db->bind(':achievement', $data['achievement']);
        $this->db->bind(':verified_status', $data['verified_status'] ?? 'pending');

        // Execute
        try {
            if($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                error_log("Database execution failed during achievement creation");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during achievement creation: " . $e->getMessage());
            return false;
        }
    }

    // Update achievement
    public function updateAchievement($data) {
        $this->db->query('UPDATE Achievements SET 
                         Date = :date,
                         MatchName = :match_name,
                         Tournament = :tournament,
                         Achievement = :achievement,
                         VerifiedStatus = :verified_status,
                         UpdatedAt = NOW()
                         WHERE AchievementID = :achievement_id AND PlayerID = :player_id');
        
        // Bind values
        $this->db->bind(':achievement_id', $data['achievement_id']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':match_name', $data['match_name']);
        $this->db->bind(':tournament', $data['tournament']);
        $this->db->bind(':achievement', $data['achievement']);
        $this->db->bind(':verified_status', $data['verified_status']);

        try {
            if($this->db->execute()) {
                return true;
            } else {
                error_log("Database execution failed during achievement update");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during achievement update: " . $e->getMessage());
            return false;
        }
    }

    // Delete achievement
    public function deleteAchievement($achievementId, $playerId) {
        try {
            $this->db->query('DELETE FROM Achievements 
                             WHERE AchievementID = :achievement_id AND PlayerID = :player_id');
            
            $this->db->bind(':achievement_id', $achievementId);
            $this->db->bind(':player_id', $playerId);

            $result = $this->db->execute();
            
            // Log for debugging
            error_log("Achievement deletion attempted for ID: $achievementId, Player: $playerId, Success: " . ($result ? 'true' : 'false'));
            
            return $result;
        } catch (Exception $e) {
            error_log("Achievement deletion error: " . $e->getMessage());
            return false;
        }
    }

    // Update verification status
    public function updateVerificationStatus($achievementId, $status) {
        $this->db->query('UPDATE Achievements SET 
                         VerifiedStatus = :status,
                         UpdatedAt = NOW()
                         WHERE AchievementID = :achievement_id');
        
        $this->db->bind(':achievement_id', $achievementId);
        $this->db->bind(':status', $status);

        try {
            if($this->db->execute()) {
                return true;
            } else {
                error_log("Database execution failed during verification status update");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during verification status update: " . $e->getMessage());
            return false;
        }
    }

    // Get achievements count by status
    public function getAchievementsCountByStatus($playerId, $status) {
        $this->db->query('SELECT COUNT(*) as count FROM Achievements 
                         WHERE PlayerID = :player_id AND VerifiedStatus = :status');
        
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':status', $status);
        
        $result = $this->db->single();
        return $result ? $result->count : 0;
    }

    // Get recent achievements
    public function getRecentAchievements($playerId, $limit = 5) {
        $this->db->query('SELECT * FROM Achievements 
                         WHERE PlayerID = :player_id 
                         ORDER BY Date DESC 
                         LIMIT :limit');
        
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    // Get achievements by tournament
    public function getAchievementsByTournament($playerId, $tournament) {
        $this->db->query('SELECT * FROM Achievements 
                         WHERE PlayerID = :player_id AND Tournament = :tournament 
                         ORDER BY Date DESC');
        
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':tournament', $tournament);
        
        return $this->db->resultSet();
    }

    // Search achievements
    public function searchAchievements($playerId, $searchTerm) {
        $this->db->query('SELECT * FROM Achievements 
                         WHERE PlayerID = :player_id 
                         AND (Achievement LIKE :search_term 
                              OR Tournament LIKE :search_term 
                              OR MatchName LIKE :search_term)
                         ORDER BY Date DESC');
        
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':search_term', '%' . $searchTerm . '%');
        
        return $this->db->resultSet();
    }

    // Get all unique tournaments for a player
    public function getPlayerTournaments($playerId) {
        $this->db->query('SELECT DISTINCT Tournament FROM Achievements 
                         WHERE PlayerID = :player_id 
                         ORDER BY Tournament ASC');
        
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get achievements statistics
    public function getAchievementStats($playerId) {
        $this->db->query('SELECT 
                            COUNT(*) as total_achievements,
                            COUNT(CASE WHEN VerifiedStatus = "verified" THEN 1 END) as verified_count,
                            COUNT(CASE WHEN VerifiedStatus = "pending" THEN 1 END) as pending_count,
                            COUNT(CASE WHEN VerifiedStatus = "rejected" THEN 1 END) as rejected_count,
                            COUNT(DISTINCT Tournament) as tournaments_participated,
                            MAX(Date) as latest_achievement_date
                         FROM Achievements 
                         WHERE PlayerID = :player_id');
        
        $this->db->bind(':player_id', $playerId);
        return $this->db->single();
    }
}
?>