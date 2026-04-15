<?php

class M_TournamentResult
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getResult($tournamentId)
    {
        $this->db->query(
            'SELECT tr.*, 
                    CONCAT(u.FirstName, \' \', u.LastName) AS ManName,
                    CONCAT(b.FirstName, \' \', b.LastName) AS BestBatsmanName,
                    CONCAT(w.FirstName, \' \', w.LastName) AS BestBowlerName
             FROM tournament_result tr
             LEFT JOIN user u ON u.UserID = tr.ManOfTournament
             LEFT JOIN user b ON b.UserID = tr.BestBatsman
             LEFT JOIN user w ON w.UserID = tr.BestBowler
             WHERE tr.TournamentID = :tid'
        );
        $this->db->bind(':tid', $tournamentId);
        return $this->db->single();
    }

    // INSERT or UPDATE (upsert via ON DUPLICATE KEY)
    public function saveResult($data)
    {
        $this->db->query(
            'INSERT INTO tournament_result
                         (TournamentID, Position, TotalMatchesPlayed, TotalWins, TotalLosses, ManOfTournament, BestBatsman, BestBowler, SummaryNotes, EnteredBy)
                         VALUES (:tid, :position, :matches, :wins, :losses, :man, :batsman, :bowler, :notes, :enteredby)
             ON DUPLICATE KEY UPDATE
               Position = :position,
                             TotalMatchesPlayed = :matches,
                             TotalWins = :wins,
                             TotalLosses = :losses,
               ManOfTournament = :man,
                             BestBatsman = :batsman,
                             BestBowler = :bowler,
               SummaryNotes = :notes,
               EnteredBy = :enteredby,
               UpdatedAt = NOW()'
        );
        $this->db->bind(':tid',       $data['tournament_id']);
        $this->db->bind(':position',  $data['position']);
                $this->db->bind(':matches',   $data['total_matches_played'] ?? 0);
                $this->db->bind(':wins',      $data['total_wins'] ?? 0);
                $this->db->bind(':losses',    $data['total_losses'] ?? 0);
        $this->db->bind(':man',       $data['man_of_tournament'] ?? null);
                $this->db->bind(':batsman',   $data['best_batsman'] ?? null);
                $this->db->bind(':bowler',    $data['best_bowler'] ?? null);
        $this->db->bind(':notes',     $data['summary_notes'] ?? null);
        $this->db->bind(':enteredby', $data['entered_by']);
        return $this->db->execute();
    }

    public function getPlayerStats($tournamentId, $playerId)
    {
        $this->db->query(
            'SELECT * FROM playertournamentstats
             WHERE TournamentID = :tid AND PlayerID = :pid'
        );
        $this->db->bind(':tid', $tournamentId);
        $this->db->bind(':pid', $playerId);
        return $this->db->single();
    }

    // INSERT or UPDATE player stats
    public function savePlayerStats($data)
    {
        $this->db->query(
            'INSERT INTO playertournamentstats
             (TournamentID, PlayerID, MatchesPlayed, TotalRuns, TotalWickets,
              BattingAverage, BowlingAverage, StrikeRate, EconomyRate)
             VALUES (:tid, :pid, :matches, :runs, :wickets, :batavg, :bowlavg, :sr, :er)
             ON DUPLICATE KEY UPDATE
               MatchesPlayed = :matches,
               TotalRuns = :runs,
               TotalWickets = :wickets,
               BattingAverage = :batavg,
               BowlingAverage = :bowlavg,
               StrikeRate = :sr,
               EconomyRate = :er'
        );
        $this->db->bind(':tid',     $data['tournament_id']);
        $this->db->bind(':pid',     $data['player_id']);
        $this->db->bind(':matches', $data['matches_played'] ?? 0);
        $this->db->bind(':runs',    $data['total_runs'] ?? 0);
        $this->db->bind(':wickets', $data['total_wickets'] ?? 0);
        $this->db->bind(':batavg',  $data['batting_average'] ?? null);
        $this->db->bind(':bowlavg', $data['bowling_average'] ?? null);
        $this->db->bind(':sr',      $data['strike_rate'] ?? null);
        $this->db->bind(':er',      $data['economy_rate'] ?? null);
        return $this->db->execute();
    }

    public function getAllStatsForTournament($tournamentId)
    {
        $this->db->query(
            'SELECT pts.*, CONCAT(u.FirstName, \' \', u.LastName) AS Name, tp.RoleInTeam
             FROM playertournamentstats pts
             JOIN user u ON u.UserID = pts.PlayerID
             LEFT JOIN tournamentplayer tp ON tp.TournamentID = pts.TournamentID AND tp.PlayerID = pts.PlayerID
             WHERE pts.TournamentID = :tid
             ORDER BY pts.TotalRuns DESC'
        );
        $this->db->bind(':tid', $tournamentId);
        return $this->db->resultset();
    }

    public function hasResult($tournamentId)
    {
        $this->db->query('SELECT COUNT(*) AS cnt FROM tournament_result WHERE TournamentID = :tid');
        $this->db->bind(':tid', $tournamentId);
        $row = $this->db->single();
        return $row && $row->cnt > 0;
    }
}
