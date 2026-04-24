<?php
class M_Performance {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private function emptyOverallStatsObject(int $playerId): stdClass
    {
        $stats = new stdClass();
        $stats->PlayerID = $playerId;
        $stats->MatchesPlayed = 0;
        $stats->TotalRuns = 0;
        $stats->HighestScore = 0;
        $stats->TotalWickets = 0;
        $stats->BattingAverage = 0;
        $stats->BowlingAverage = 0;
        $stats->StrikeRate = 0;
        $stats->EconomyRate = 0;
        $stats->Centuries = 0;
        $stats->HalfCenturies = 0;
        $stats->FiveWickets = 0;
        $stats->FourWickets = 0;
        $stats->BestBowling = 'N/A';

        // Legacy aliases expected by some views/controllers
        $stats->BattingAvg = 0;
        $stats->BowlingAvg = 0;
        $stats->Wickets = 0;

        return $stats;
    }

    private function deleteStoredOverallStats(int $playerId): void
    {
        try {
            $this->db->query('DELETE FROM playeroverallstats WHERE PlayerID = :player_id');
            $this->db->bind(':player_id', $playerId);
            $this->db->execute();
        } catch (Throwable $e) {
            error_log('Failed to delete playeroverallstats for PlayerID=' . $playerId . ' error=' . $e->getMessage());
        }
    }

    // Get overall stats for a player
    public function getOverallStats($playerId) {
        $playerId = (int)$playerId;

        if ($playerId <= 0) {
            return $this->emptyOverallStatsObject(0);
        }

        // If the player has no VERIFIED performance records, they should not have a stored overallstats row.
        if (!$this->hasAnyVerifiedPerformanceRecords($playerId)) {
            $this->deleteStoredOverallStats($playerId);
            return $this->emptyOverallStatsObject($playerId);
        }

        // Try to get from playeroverallstats table first
        $this->db->query('SELECT * FROM playeroverallstats WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', $playerId);
        $stats = $this->db->single();
        
        // If no stats found in overall table, calculate from match history
        if (!$stats) {
            return $this->calculateOverallStats($playerId) ?: $this->emptyOverallStatsObject($playerId);
        }

        // If the row exists but is still empty,
        // In that case, refresh from playermatchperformance.
        $matchesPlayed = (int)($stats->MatchesPlayed ?? 0);
        if ($matchesPlayed === 0 && $this->hasAnyVerifiedPerformanceRecords($playerId)) {
            $recalculated = $this->calculateOverallStats($playerId);
            return $recalculated ?: $this->emptyOverallStatsObject($playerId);
        }

        return $stats;
    }

    // Get the stored overall stats row without recalculating from match history.
    public function getStoredOverallStats($playerId) {
        $this->db->query('SELECT * FROM playeroverallstats WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', $playerId);
        return $this->db->single();
    }

    private function hasAnyVerifiedPerformanceRecords(int $playerId): bool
    {
        $playerId = (int)$playerId;
        if ($playerId <= 0) {
            return false;
        }

        // If verification columns don't exist, we can't enforce verified-only semantics.
        // In that case, fall back to "any record" behavior.
        $hasVerificationColumns = $this->checkVerificationColumns();
        if ($hasVerificationColumns) {
            $this->db->query('SELECT COUNT(*) AS cnt
                FROM playermatchperformance
                WHERE PlayerID = :player_id
                  AND VerifiedStatus = "verified"');
        } else {
            $this->db->query('SELECT COUNT(*) AS cnt
                FROM playermatchperformance
                WHERE PlayerID = :player_id');
        }

        $this->db->bind(':player_id', $playerId);
        $row = $this->db->single();
        return (int)($row->cnt ?? 0) > 0;
    }
    

    // Calculate overall stats from match history and persist into playeroverallstats
    private function calculateOverallStats($playerId) {
        $playerId = (int)$playerId;

        // Enforce: only players with at least one VERIFIED performance record can have overallstats.
        if (!$this->hasAnyVerifiedPerformanceRecords($playerId)) {
            $this->deleteStoredOverallStats($playerId);
            return false;
        }

        // Overall stats are derived ONLY from verified match performance records.
        $hasVerificationColumns = $this->checkVerificationColumns();
        $whereVerified = $hasVerificationColumns ? ' AND VerifiedStatus = "verified"' : '';

        $this->db->query('SELECT 
            COUNT(*) as MatchesPlayed,
            COALESCE(SUM(RunsScored), 0) as TotalRuns,
            COALESCE(SUM(BallsFaced), 0) as TotalBalls,
            COALESCE(MAX(RunsScored), 0) as HighestScore,
            COALESCE(SUM(WicketsTaken), 0) as Wickets,
            COALESCE(SUM(OversBowled), 0) as TotalOvers,
            COALESCE(SUM(RunsConceded), 0) as TotalRunsConceded,
            COALESCE(SUM(CASE WHEN RunsScored >= 100 THEN 1 ELSE 0 END), 0) as Centuries,
            COALESCE(SUM(CASE WHEN RunsScored >= 50 AND RunsScored < 100 THEN 1 ELSE 0 END), 0) as HalfCenturies,
            COALESCE(SUM(CASE WHEN WicketsTaken >= 5 THEN 1 ELSE 0 END), 0) as FiveWickets,
            COALESCE(SUM(CASE WHEN WicketsTaken >= 4 THEN 1 ELSE 0 END), 0) as FourWickets
            FROM playermatchperformance
            WHERE PlayerID = :player_id' . $whereVerified);
        $this->db->bind(':player_id', $playerId);
        $result = $this->db->single();

        // Get best bowling figures separately
        $this->db->query('SELECT CONCAT(WicketsTaken, \'/\', RunsConceded) as BestBowling 
            FROM playermatchperformance 
            WHERE PlayerID = :player_id' . $whereVerified . '
            ORDER BY WicketsTaken DESC, RunsConceded ASC 
            LIMIT 1');
        $this->db->bind(':player_id', $playerId);
        $bestBowlingResult = $this->db->single();
        $bestBowling = $bestBowlingResult ? $bestBowlingResult->BestBowling : 'N/A';

        if (!$result) {
            return false;
        }

        $matchesPlayed = (int)($result->MatchesPlayed ?? 0);
        if ($matchesPlayed <= 0) {
            // No verified matches => no overallstats row.
            $this->deleteStoredOverallStats($playerId);
            return false;
        }
        $totalRuns = (int)($result->TotalRuns ?? 0);
        $totalBalls = (float)($result->TotalBalls ?? 0);
        $highestScore = (int)($result->HighestScore ?? 0);
        $totalWickets = (int)($result->Wickets ?? 0);
        $totalOvers = (float)($result->TotalOvers ?? 0);
        $totalRunsConceded = (int)($result->TotalRunsConceded ?? 0);
        $centuries = (int)($result->Centuries ?? 0);
        $halfCenturies = (int)($result->HalfCenturies ?? 0);
        $fiveWickets = (int)($result->FiveWickets ?? 0);
        $fourWickets = (int)($result->FourWickets ?? 0);

        $battingAvg = $matchesPlayed > 0 ? round($totalRuns / $matchesPlayed, 2) : 0;
        $strikeRate = $totalBalls > 0 ? round(($totalRuns / $totalBalls) * 100, 2) : 0;
        $bowlingAvg = $totalWickets > 0 ? round($totalRunsConceded / $totalWickets, 2) : 0;
        $economyRate = $totalOvers > 0 ? round($totalRunsConceded / $totalOvers, 2) : 0;

        // Persist computed stats (works whether row is pre-created or not).
        $this->db->query('INSERT INTO playeroverallstats
            (PlayerID, MatchesPlayed, TotalRuns, TotalWickets, HighestScore,
             BattingAverage, BowlingAverage, StrikeRate, EconomyRate, Centuries, HalfCenturies, FiveWickets, FourWickets, BestBowling)
            VALUES
            (:player_id, :matches_played, :total_runs, :total_wickets, :highest_score,
             :batting_avg, :bowling_avg, :strike_rate, :economy_rate, :centuries, :half_centuries, :five_wickets, :four_wickets, :best_bowling)
            ON DUPLICATE KEY UPDATE
                MatchesPlayed = VALUES(MatchesPlayed),
                TotalRuns = VALUES(TotalRuns),
                TotalWickets = VALUES(TotalWickets),
                HighestScore = VALUES(HighestScore),
                BattingAverage = VALUES(BattingAverage),
                BowlingAverage = VALUES(BowlingAverage),
                StrikeRate = VALUES(StrikeRate),
                EconomyRate = VALUES(EconomyRate),
                Centuries = VALUES(Centuries),
                HalfCenturies = VALUES(HalfCenturies),
                FiveWickets = VALUES(FiveWickets),
                FourWickets = VALUES(FourWickets),
                BestBowling = VALUES(BestBowling)');

        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':matches_played', $matchesPlayed);
        $this->db->bind(':total_runs', $totalRuns);
        $this->db->bind(':total_wickets', $totalWickets);
        $this->db->bind(':highest_score', $highestScore);
        $this->db->bind(':batting_avg', $battingAvg);
        $this->db->bind(':bowling_avg', $bowlingAvg);
        $this->db->bind(':strike_rate', $strikeRate);
        $this->db->bind(':economy_rate', $economyRate);
        $this->db->bind(':centuries', $centuries);
        $this->db->bind(':half_centuries', $halfCenturies);
        $this->db->bind(':five_wickets', $fiveWickets);
        $this->db->bind(':four_wickets', $fourWickets);
        $this->db->bind(':best_bowling', $bestBowling);

        $persisted = $this->db->execute();
        if (!$persisted) {
            error_log('Failed to persist playeroverallstats for PlayerID=' . $playerId . ' error=' . print_r($this->db->getError(), true));
        }

        // Return object with both legacy and schema-accurate property names (for existing controller code).
        $stats = new stdClass();
        $stats->PlayerID = $playerId;
        $stats->MatchesPlayed = $matchesPlayed;
        $stats->TotalRuns = $totalRuns;
        $stats->HighestScore = $highestScore;
        $stats->TotalWickets = $totalWickets;
        $stats->BattingAverage = $battingAvg;
        $stats->BowlingAverage = $bowlingAvg;
        $stats->StrikeRate = $strikeRate;
        $stats->EconomyRate = $economyRate;

        // Legacy aliases
        $stats->BattingAvg = $battingAvg;
        $stats->BowlingAvg = $bowlingAvg;
        $stats->Wickets = $totalWickets;

        return $stats;
    }






    // Get match history for a player
    public function getMatchHistory($playerId, $limit = 20) {
        $this->db->query('SELECT pmp.*, cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, 
            cm.Name AS MatchName, cm.MarginValue, cm.MarginType, cm.OurRuns, cm.OurWickets,
            cm.OpponentRuns, cm.OpponentWickets, cm.IsDLS, cm.SummaryNotes,
            cm.OurRuns AS OurScore, cm.OpponentRuns AS OpponentScore, t.Name AS TournamentName
            FROM playermatchperformance pmp 
            JOIN crimatch cm ON pmp.MatchID = cm.MatchID 
            JOIN tournament t ON cm.TournamentID = t.TournamentID 
            WHERE pmp.PlayerID = :player_id and pmp.VerifiedStatus = "verified"
            ORDER BY cm.Date DESC LIMIT :limit');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get batting stats (verified matches only) formatted for display
    public function getBattingStatsForPlayer($playerId, $limit = 10) {
        $matches = $this->getMatchHistory($playerId, $limit);
        $battingData = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                if ($match->RunsScored > 0 || $match->BallsFaced > 0) {
                    $strikeRate = $match->BallsFaced > 0 ?
                        round(($match->RunsScored / $match->BallsFaced) * 100, 2) : 0;
                    $battingData[] = [
                        'match_id'    => $match->MatchID,
                        'match_date'  => $match->Date,
                        'opponent'    => $match->OpponentTeam,
                        'tournament'  => $match->TournamentName,
                        'runs'        => $match->RunsScored,
                        'balls'       => $match->BallsFaced,
                        'strike_rate' => $strikeRate,
                        'result'      => $match->Result,
                        'venue'       => $match->Venue
                    ];
                }
            }
        }
        return $battingData;
    }

    // Get bowling stats (verified matches only) formatted for display
    public function getBowlingStatsForPlayer($playerId, $limit = 10) {
        $matches = $this->getMatchHistory($playerId, $limit);
        $bowlingData = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                if ($match->WicketsTaken > 0 || $match->OversBowled > 0) {
                    $economy = $match->OversBowled > 0 ?
                        round($match->RunsConceded / $match->OversBowled, 2) : 0;
                    $bowlingData[] = [
                        'match_id'      => $match->MatchID,
                        'match_date'    => $match->Date,
                        'opponent'      => $match->OpponentTeam,
                        'tournament'    => $match->TournamentName,
                        'wickets'       => $match->WicketsTaken,
                        'overs'         => $match->OversBowled,
                        'runs_conceded' => $match->RunsConceded,
                        'economy'       => $economy,
                        'result'        => $match->Result,
                        'venue'         => $match->Venue
                    ];
                }
            }
        }
        return $bowlingData;
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

    /*// Get performance updates/assessments from coaches
    public function getPerformanceUpdates($playerId, $limit = 10) {
        $this->db->query('SELECT pu.*, CONCAT(u.FirstName, \' \', u.LastName) AS CoachName, s.Name AS SessionName
            FROM performanceupdate pu
            JOIN user u ON pu.CoachID = u.UserID
            LEFT JOIN session s ON pu.SessionID = s.SessionID
            WHERE pu.PlayerID = :player_id AND pu.Status = "approved"
            ORDER BY pu.UpdateDate DESC LIMIT :limit');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }*/

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
        // First, check if VerifiedStatus column exists (for backward compatibility)
        $hasVerificationColumns = $this->checkVerificationColumns();
        
        if ($hasVerificationColumns) {
            // Use new schema with verification columns
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
            $this->db->bind(':verified_status', 'pending');
            $this->db->bind(':added_by', $data['added_by']);
        } else {
            // Use old schema without verification columns (fallback)
            $this->db->query('INSERT INTO playermatchperformance 
                (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled, 
                 RunsConceded, Catches, Stumpings, Rating) 
                VALUES (:match_id, :player_id, :runs, :balls, :wickets, :overs, 
                        :runs_conceded, :catches, :stumpings, :rating)');
            
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
        }
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Add performance statistics by a coach on behalf of a player (auto-verified)
    public function addCoachPerformanceStatistics($data) {
        $hasVerificationColumns = $this->checkVerificationColumns();

        if ($hasVerificationColumns) {
            $this->db->query('INSERT INTO playermatchperformance
                (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled,
                 RunsConceded, Catches, Stumpings, Rating, VerifiedStatus, AddedBy, VerifiedBy, VerifiedAt)
                VALUES (:match_id, :player_id, :runs, :balls, :wickets, :overs,
                        :runs_conceded, :catches, :stumpings, :rating, "verified", :added_by, :verified_by, NOW())');

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
            $this->db->bind(':added_by', $data['added_by']);
            $this->db->bind(':verified_by', $data['added_by']);
        } else {
            $this->db->query('INSERT INTO playermatchperformance
                (MatchID, PlayerID, RunsScored, BallsFaced, WicketsTaken, OversBowled,
                 RunsConceded, Catches, Stumpings, Rating)
                VALUES (:match_id, :player_id, :runs, :balls, :wickets, :overs,
                        :runs_conceded, :catches, :stumpings, :rating)');

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
        }

        if (!$this->db->execute()) {
            return false;
        }

        $performanceId = $this->db->lastInsertId();

        if ($performanceId) {
            $this->calculateOverallStats((int)$data['player_id']);

            $this->db->query('SELECT cm.TournamentID FROM crimatch cm WHERE cm.MatchID = :match_id');
            $this->db->bind(':match_id', $data['match_id']);
            $matchRecord = $this->db->single();
            $tournamentId = (int)($matchRecord->TournamentID ?? 0);
            if ($tournamentId > 0) {
                try {
                    require_once APPROOT . '/models/M_TournamentResult.php';
                    $resultModel = new M_TournamentResult();
                    $resultModel->recalculatePlayerTournamentStatsFromPerformance($tournamentId, true);
                    $resultModel->updateResultAwardsFromPerformance($tournamentId, true);
                } catch (Throwable $e) {
                    error_log('Tournament stats recalculation failed after coach add performance: ' . $e->getMessage());
                }
            }
        }

        return $performanceId;
    }
    
    // Check if verification columns exist in the database
    private function checkVerificationColumns() {
        try {
            $this->db->query("SHOW COLUMNS FROM playermatchperformance LIKE 'VerifiedStatus'");
            $result = $this->db->single();
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Get performance statistics with verification status
    public function getPerformanceStatistics($playerId, $includeUnverified = true) {
        $whereClause = $includeUnverified ? 
            'WHERE pmp.PlayerID = :player_id' : 
            'WHERE pmp.PlayerID = :player_id AND pmp.VerifiedStatus = "verified"';
        
        $this->db->query("SELECT pmp.*, 
            cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.Name AS MatchName,
            cm.MarginValue, cm.MarginType, cm.OurRuns, cm.OurWickets, cm.OpponentRuns, cm.OpponentWickets,
            cm.IsDLS, cm.SummaryNotes, cm.OurRuns AS OurScore, cm.OpponentRuns AS OpponentScore,
            t.Name AS TournamentName,
            CONCAT(u1.FirstName, ' ', u1.LastName) AS AddedByName,
            CONCAT(u2.FirstName, ' ', u2.LastName) AS VerifiedByName,
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

    // Coach view: get match-wise performance records including pending/rejected
    public function getCoachPerformanceDetails($playerId, $limit = 50) {
        $this->db->query("SELECT pmp.*,
            cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.Name AS MatchName,
            cm.MarginValue, cm.MarginType, cm.OurRuns, cm.OurWickets, cm.OpponentRuns, cm.OpponentWickets,
            cm.IsDLS, cm.SummaryNotes, cm.OurRuns AS OurScore, cm.OpponentRuns AS OpponentScore,
            t.Name AS TournamentName,
            CONCAT(u1.FirstName, ' ', u1.LastName) AS AddedByName,
            CONCAT(u2.FirstName, ' ', u2.LastName) AS VerifiedByName
            FROM playermatchperformance pmp
            LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID
            LEFT JOIN tournament t ON cm.TournamentID = t.TournamentID
            LEFT JOIN user u1 ON pmp.AddedBy = u1.UserID
            LEFT JOIN user u2 ON pmp.VerifiedBy = u2.UserID
            WHERE pmp.PlayerID = :player_id
            ORDER BY cm.Date DESC, pmp.CreatedAt DESC
            LIMIT :limit");
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get pending (unverified) performance statistics for review
    public function getPendingPerformanceStatistics($playerId = null) {
        if ($playerId) {
            $this->db->query('SELECT pmp.*, 
                cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.Name AS MatchName,
                cm.MarginValue, cm.MarginType, cm.OurRuns, cm.OurWickets, cm.OpponentRuns, cm.OpponentWickets,
                cm.IsDLS, cm.SummaryNotes,
                t.Name AS TournamentName,
                CONCAT(u.FirstName, \' \', u.LastName) AS PlayerName,
                CONCAT(u1.FirstName, \' \', u1.LastName) AS AddedByName
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
                cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.Name AS MatchName,
                cm.MarginValue, cm.MarginType, cm.OurRuns, cm.OurWickets, cm.OpponentRuns, cm.OpponentWickets,
                cm.IsDLS, cm.SummaryNotes,
                t.Name AS TournamentName,
                CONCAT(u.FirstName, \' \', u.LastName) AS PlayerName,
                CONCAT(u1.FirstName, \' \', u1.LastName) AS AddedByName
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
    // Get PlayerID + TournamentID + old verification status first (TournamentID via the match)
    $this->db->query('SELECT pmp.PlayerID, cm.TournamentID, pmp.VerifiedStatus AS OldVerifiedStatus
        FROM playermatchperformance pmp
        LEFT JOIN crimatch cm ON cm.MatchID = pmp.MatchID
        WHERE pmp.PerformanceID = :performance_id');
    $this->db->bind(':performance_id', $performanceId);
    $record = $this->db->single();
    
    if (!$record) {
        return false;
    }
    
    // Update verification status
    $this->db->query('UPDATE playermatchperformance 
        SET VerifiedStatus = :status, 
            VerifiedBy = :verified_by, 
            VerifiedAt = NOW() 
        WHERE PerformanceID = :performance_id');
    
    $this->db->bind(':status', $status);
    $this->db->bind(':verified_by', $verifiedBy);
    $this->db->bind(':performance_id', $performanceId);
    
    if (!$this->db->execute()) {
        return false;
    }
    
    // Keep playeroverallstats consistent with verified-only rule.
    // - If verified: recalc from verified performances.
    // - If un-verified (e.g. rejected after being verified): recalc again so totals go DOWN.
    $oldStatus = (string)($record->OldVerifiedStatus ?? '');
    if ($status === 'verified' || $oldStatus === 'verified') {
        $this->calculateOverallStats((int)$record->PlayerID);
    } else if (!$this->hasAnyVerifiedPerformanceRecords((int)$record->PlayerID)) {
        $this->deleteStoredOverallStats((int)$record->PlayerID);
    }

    // Recalculate tournament stats from match performance (keeps playertournamentstats and awards in sync)
    $tournamentId = (int)($record->TournamentID ?? 0);
    if ($tournamentId > 0) {
        try {
            require_once APPROOT . '/models/M_TournamentResult.php';
            $resultModel = new M_TournamentResult();
            $resultModel->recalculatePlayerTournamentStatsFromPerformance($tournamentId, true);
            $resultModel->updateResultAwardsFromPerformance($tournamentId, true);
        } catch (Throwable $e) {
            error_log('Tournament stats recalculation failed after verification: ' . $e->getMessage());
        }
    }
    
    return true;
}

    // Get available matches (for dropdown when adding performance)
    public function getAvailableMatches($limit = 50) {
        $this->db->query('SELECT cm.MatchID, cm.Name AS MatchName, cm.Date, cm.Venue, cm.OpponentTeam, 
            t.Name AS TournamentName, cm.Result, cm.MarginValue, cm.MarginType,
            cm.OurRuns, cm.OurWickets, cm.OpponentRuns, cm.OpponentWickets, cm.IsDLS, cm.SummaryNotes
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
            cm.Date, cm.Venue, cm.OpponentTeam, cm.Result, cm.Name AS MatchName,
            cm.MarginValue, cm.MarginType, cm.OurRuns, cm.OurWickets, cm.OpponentRuns, cm.OpponentWickets,
            cm.IsDLS, cm.SummaryNotes, cm.OurRuns AS OurScore, cm.OpponentRuns AS OpponentScore,
            t.Name AS TournamentName, t.TournamentID,
            CONCAT(u1.FirstName, \' \', u1.LastName) AS AddedByName,
            CONCAT(u2.FirstName, \' \', u2.LastName) AS VerifiedByName
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
