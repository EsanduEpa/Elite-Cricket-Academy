<?php

class M_CriMatch
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getMatchesByTournament(int $tournamentId): array
    {
        $this->db->query(
            'SELECT cm.*
             FROM crimatch cm
             WHERE cm.TournamentID = :tid
             ORDER BY cm.Date DESC, cm.MatchID DESC'
        );
        $this->db->bind(':tid', $tournamentId);
        return $this->db->resultset();
    }

    public function createMatch(array $data)
    {
        $this->db->query(
            'INSERT INTO crimatch
                (TournamentID, Name, Date, Venue, OpponentTeam, Result,
                 MarginValue, MarginType, OurRuns, OurWickets, OpponentRuns, OpponentWickets,
                 IsDLS, SummaryNotes)
             VALUES
                (:tournament_id, :name, :match_date, :venue, :opponent_team, :result,
                 :margin_value, :margin_type, :our_runs, :our_wickets, :opp_runs, :opp_wickets,
                 :is_dls, :summary_notes)'
        );

        $this->db->bind(':tournament_id', (int)$data['tournament_id']);
        $this->db->bind(':name', (string)$data['name']);
        $this->db->bind(':match_date', (string)$data['match_date']);
        $this->db->bind(':venue', $data['venue'] !== null ? (string)$data['venue'] : null);
        $this->db->bind(':opponent_team', (string)$data['opponent_team']);
        $this->db->bind(':result', (string)($data['result'] ?? 'pending'));

        $this->db->bind(':margin_value', $data['margin_value'] !== null ? (int)$data['margin_value'] : null);
        $this->db->bind(':margin_type', $data['margin_type'] !== null ? (string)$data['margin_type'] : null);

        $this->db->bind(':our_runs', $data['our_runs'] !== null ? (int)$data['our_runs'] : null);
        $this->db->bind(':our_wickets', $data['our_wickets'] !== null ? (int)$data['our_wickets'] : null);
        $this->db->bind(':opp_runs', $data['opponent_runs'] !== null ? (int)$data['opponent_runs'] : null);
        $this->db->bind(':opp_wickets', $data['opponent_wickets'] !== null ? (int)$data['opponent_wickets'] : null);

        $this->db->bind(':is_dls', !empty($data['is_dls']) ? 1 : 0);
        $this->db->bind(':summary_notes', $data['summary_notes'] !== null ? (string)$data['summary_notes'] : null);

        $ok = $this->db->execute();
        return $ok ? $this->db->lastInsertId() : false;
    }
}
