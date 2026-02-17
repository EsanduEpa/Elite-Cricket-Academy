<?php
class M_Performance {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get overall stats for a player
    public function getOverallStats($playerId) {
        $this->db->query('SELECT * FROM playeroverallstats WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', $playerId);
        return $this->db->single();
    }

    // Get match history for a player
    public function getMatchHistory($playerId, $limit = 20) {
        $this->db->query('SELECT pmp.*, cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, 
            cm.OurScore, cm.OpponentScore, t.Name AS TournamentName
            FROM playermatchperformance pmp 
            JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
            JOIN tournament t ON cm.TournamentID = t.TournamentID 
            WHERE pmp.PlayerID = :player_id 
            ORDER BY cm.Date DESC LIMIT :limit');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get tournament stats for a player
    public function getTournamentStats($playerId) {
        $this->db->query('SELECT pts.*, t.Name AS TournamentName, t.tdate, t.Location, t.Status AS TournamentStatus
            FROM playertournamentstats pts 
            JOIN tournament t ON pts.TournamentID = t.TournamentID 
            WHERE pts.PlayerID = :player_id 
            ORDER BY t.tdate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get recent performance (last N matches)
    public function getRecentPerformance($playerId, $limit = 5) {
        $this->db->query('SELECT pmp.*, cm.Date, cm.OpponentTeam, cm.Result, cm.Venue
            FROM playermatchperformance pmp 
            JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
            WHERE pmp.PlayerID = :player_id 
            ORDER BY cm.Date DESC LIMIT :limit');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get performance updates/assessments from coaches
    public function getPerformanceUpdates($playerId, $limit = 10) {
        $this->db->query('SELECT pu.*, u.Name AS CoachName, s.Name AS SessionName
            FROM performanceupdate pu
            JOIN user u ON pu.CoachID = u.UserID
            LEFT JOIN session s ON pu.SessionID = s.SessionID
            WHERE pu.PlayerID = :player_id AND pu.Status = "approved"
            ORDER BY pu.UpdateDate DESC LIMIT :limit');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get all tournaments a player is participating in
    public function getPlayerTournaments($playerId) {
        $this->db->query('SELECT t.*, tp.Team, tp.RoleInTeam
            FROM tournament t
            JOIN tournamentplayer tp ON t.TournamentID = tp.TournamentID
            WHERE tp.PlayerID = :player_id
            ORDER BY t.tdate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get all tournaments (for coach/admin views)
    public function getAllTournaments() {
        $this->db->query('SELECT t.*, 
            (SELECT COUNT(*) FROM tournamentplayer WHERE TournamentID = t.TournamentID) AS PlayerCount,
            (SELECT COUNT(*) FROM crimatch WHERE TournamentID = t.TournamentID) AS MatchCount
            FROM tournament t ORDER BY t.tdate DESC');
        return $this->db->resultSet();
    }

    // Get all matches with details
    public function getAllMatches($tournamentId = null) {
        if ($tournamentId) {
            $this->db->query('SELECT cm.*, t.Name AS TournamentName
                FROM crimatch cm JOIN tournament t ON cm.TournamentID = t.TournamentID
                WHERE cm.TournamentID = :tid ORDER BY cm.Date DESC');
            $this->db->bind(':tid', $tournamentId);
        } else {
            $this->db->query('SELECT cm.*, t.Name AS TournamentName
                FROM crimatch cm JOIN tournament t ON cm.TournamentID = t.TournamentID
                ORDER BY cm.Date DESC');
        }
        return $this->db->resultSet();
    }

    // Get upcoming tournaments (status = Upcoming or Registration Open)
    public function getUpcomingTournamentsAll() {
        $this->db->query('SELECT t.*, 
            (SELECT COUNT(*) FROM tournamentplayer tp WHERE tp.TournamentID = t.TournamentID) as registered_players
            FROM tournament t
            WHERE t.Status IN ("Upcoming", "Registration Open")
            ORDER BY t.StartDate ASC');
        return $this->db->resultSet();
    }

    // Get enrolled tournaments for a player
    public function getEnrolledTournaments($playerId) {
        $this->db->query('SELECT t.*, tp.TeamName, tp.RegistrationDate,
            (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) as total_matches
            FROM tournament t
            JOIN tournamentplayer tp ON t.TournamentID = tp.TournamentID
            WHERE tp.PlayerID = :pid AND t.Status IN ("Upcoming", "Ongoing", "Registration Open")
            ORDER BY t.StartDate ASC');
        $this->db->bind(':pid', $playerId);
        return $this->db->resultSet();
    }

    // Get completed tournaments for a player
    public function getCompletedTournaments($playerId) {
        $this->db->query('SELECT t.*, tp.TeamName, tp.RegistrationDate,
            pts.MatchesPlayed, pts.RunsScored, pts.WicketsTaken, pts.HighestScore,
            (SELECT COUNT(*) FROM crimatch cm WHERE cm.TournamentID = t.TournamentID) as total_matches
            FROM tournament t
            JOIN tournamentplayer tp ON t.TournamentID = tp.TournamentID
            LEFT JOIN playertournamentstats pts ON pts.TournamentID = t.TournamentID AND pts.PlayerID = :pid2
            WHERE tp.PlayerID = :pid AND t.Status = "Completed"
            ORDER BY t.EndDate DESC');
        $this->db->bind(':pid', $playerId);
        $this->db->bind(':pid2', $playerId);
        return $this->db->resultSet();
    }

    // Get player tournament summary stats
    public function getPlayerTournamentSummary($playerId) {
        $this->db->query('SELECT 
            COUNT(DISTINCT tp.TournamentID) as total_tournaments,
            COALESCE(SUM(pts.MatchesPlayed), 0) as total_matches,
            COALESCE(SUM(pts.RunsScored), 0) as total_runs,
            COALESCE(SUM(pts.WicketsTaken), 0) as total_wickets,
            COALESCE(MAX(pts.HighestScore), 0) as best_score
            FROM tournamentplayer tp
            LEFT JOIN playertournamentstats pts ON tp.TournamentID = pts.TournamentID AND tp.PlayerID = pts.PlayerID
            WHERE tp.PlayerID = :pid');
        $this->db->bind(':pid', $playerId);
        return $this->db->single();
    }

    // Add new performance statistics (by player)
    public function addPerformanceStatistics($data) {
        $this->db->query('INSERT INTO playermatchperformance 
            (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, 
             RunsConceded, Catches, Stumpings, Rating, VerifiedStatus, AddedBy) 
            VALUES (:match_id, :player_id, :runs, :balls, :wickets, :overs, 
                    :runs_conceded, :catches, :stumpings, :rating, :verified_status, :added_by)');
        
        $this->db->bind(':match_id', $data['match_id']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':runs', $data['runs_scored']);
        $this->db->bind(':balls', $data['balls_faced']);
        $this->db->bind(':wickets', $data['wickets_taken']);
        $this->db->bind(':overs', $data['overs_bowled']);
        $this->db->bind(':runs_conceded', $data['runs_conceded']);
        $this->db->bind(':catches', $data['catches']);
        $this->db->bind(':stumpings', $data['stumpings']);
        $this->db->bind(':rating', $data['rating'] ?? 0);
        $this->db->bind(':verified_status', 'pending'); // Default to pending
        $this->db->bind(':added_by', $data['added_by']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Get performance statistics with verification status
    public function getPerformanceStatistics($playerId, $includeUnverified = true) {
        $whereClause = $includeUnverified ? 
            'WHERE pmp.PlayerID = :player_id' : 
            'WHERE pmp.PlayerID = :player_id AND pmp.VerifiedStatus = "verified"';
        
        $this->db->query("SELECT pmp.*, 
            cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.OurScore, cm.OpponentScore,
            t.Name AS TournamentName,
            u1.Name AS AddedByName,
            u2.Name AS VerifiedByName,
            pmp.VerifiedStatus,
            pmp.VerifiedAt
            FROM playermatchperformance pmp 
            LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
            LEFT JOIN tournament t ON cm.TournamentID = t.TournamentID 
            LEFT JOIN user u1 ON pmp.AddedBy = u1.UserID
            LEFT JOIN user u2 ON pmp.VerifiedBy = u2.UserID
            $whereClause 
            ORDER BY cm.Date DESC, pmp.CreatedAt DESC");
        
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get pending (unverified) performance statistics for review
    public function getPendingPerformanceStatistics($playerId = null) {
        if ($playerId) {
            $this->db->query('SELECT pmp.*, 
                cm.Date, cm.Venue, cm.OpponentTeam, cm.Result,
                t.Name AS TournamentName,
                u.Name AS PlayerName,
                u1.Name AS AddedByName
                FROM playermatchperformance pmp 
                LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
                LEFT JOIN tournament t ON cm.TournamentID = t.TournamentID 
                JOIN user u ON pmp.PlayerID = u.UserID
                LEFT JOIN user u1 ON pmp.AddedBy = u1.UserID
                WHERE pmp.PlayerID = :player_id AND pmp.VerifiedStatus = "pending"
                ORDER BY pmp.CreatedAt DESC');
            $this->db->bind(':player_id', $playerId);
        } else {
            // For coaches/admins to see all pending performance records
            $this->db->query('SELECT pmp.*, 
                cm.Date, cm.Venue, cm.OpponentTeam, cm.Result,
                t.Name AS TournamentName,
                u.Name AS PlayerName,
                u1.Name AS AddedByName
                FROM playermatchperformance pmp 
                LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
                LEFT JOIN tournament t ON cm.TournamentID = t.TournamentID 
                JOIN user u ON pmp.PlayerID = u.UserID
                LEFT JOIN user u1 ON pmp.AddedBy = u1.UserID
                WHERE pmp.VerifiedStatus = "pending"
                ORDER BY pmp.CreatedAt DESC');
        }
        return $this->db->resultSet();
    }

    // Verify/Reject performance statistics (for coaches/admins)
    public function updatePerformanceVerification($performanceId, $status, $verifiedBy) {
        $this->db->query('UPDATE playermatchperformance 
            SET VerifiedStatus = :status, 
                VerifiedBy = :verified_by, 
                VerifiedAt = NOW() 
            WHERE PerformanceID = :performance_id');
        
        $this->db->bind(':status', $status);
        $this->db->bind(':verified_by', $verifiedBy);
        $this->db->bind(':performance_id', $performanceId);
        
        return $this->db->execute();
    }

    // Get available matches (for dropdown when adding performance)
    public function getAvailableMatches($limit = 50) {
        $this->db->query('SELECT cm.MatchID, cm.Date, cm.Venue, cm.OpponentTeam, 
            t.Name AS TournamentName, cm.Result, cm.OurScore, cm.OpponentScore
            FROM crimatch cm 
            JOIN tournament t ON cm.TournamentID = t.TournamentID 
            ORDER BY cm.Date DESC 
            LIMIT :limit');
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get single performance record by ID
    public function getPerformanceById($performanceId) {
        $this->db->query('SELECT pmp.*, 
            cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.OurScore, cm.OpponentScore,
            t.Name AS TournamentName, t.TournamentID,
            u1.Name AS AddedByName,
            u2.Name AS VerifiedByName
            FROM playermatchperformance pmp 
            LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
            LEFT JOIN tournament t ON cm.TournamentID = t.TournamentID 
            LEFT JOIN user u1 ON pmp.AddedBy = u1.UserID
            LEFT JOIN user u2 ON pmp.VerifiedBy = u2.UserID
            WHERE pmp.PerformanceID = :performance_id');
        $this->db->bind(':performance_id', $performanceId);
        return $this->db->single();
    }

    // Update performance statistics
    public function updatePerformanceStatistics($performanceId, $data) {
        $this->db->query('UPDATE playermatchperformance 
            SET MatchID = :match_id,
                RunsScored = :runs,
                BallsFaced = :balls,
                WicketsTaken = :wickets,
                OversBowled = :overs,
                RunsConceded = :runs_conceded,
                Catches = :catches,
                Stumpings = :stumpings,
                Rating = :rating
            WHERE PerformanceID = :performance_id 
            AND VerifiedStatus = "pending"');
        
        $this->db->bind(':performance_id', $performanceId);
        $this->db->bind(':match_id', $data['match_id']);
        $this->db->bind(':runs', $data['runs_scored']);
        $this->db->bind(':balls', $data['balls_faced']);
        $this->db->bind(':wickets', $data['wickets_taken']);
        $this->db->bind(':overs', $data['overs_bowled']);
        $this->db->bind(':runs_conceded', $data['runs_conceded']);
        $this->db->bind(':catches', $data['catches']);
        $this->db->bind(':stumpings', $data['stumpings']);
        $this->db->bind(':rating', $data['rating'] ?? 0);
        
        return $this->db->execute();
    }

    // Delete performance statistics (only if pending)
    public function deletePerformanceStatistics($performanceId, $playerId) {
        $this->db->query('DELETE FROM playermatchperformance 
            WHERE PerformanceID = :performance_id 
            AND PlayerID = :player_id 
            AND VerifiedStatus = "pending"');
        
        $this->db->bind(':performance_id', $performanceId);
        $this->db->bind(':player_id', $playerId);
        
        return $this->db->execute();
    }
}
