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
}
