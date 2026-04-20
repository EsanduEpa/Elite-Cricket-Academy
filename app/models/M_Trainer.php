<?php
class M_Trainer {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get trainer information
    public function getTrainerById($id) {
        $this->db->query('SELECT * FROM trainers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get all trainers
    public function getTrainers() {
        $this->db->query('SELECT * FROM trainers ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Schedule Management
    public function getSchedules($trainer_id) {
        $this->db->query('SELECT s.*, p.name as player_name 
                         FROM training_sessions s 
                         LEFT JOIN players p ON s.player_id = p.id 
                         WHERE s.trainer_id = :trainer_id 
                         ORDER BY s.session_date ASC, s.session_time ASC');
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    public function getSessionsForCalendar($trainer_id) {
        $this->db->query('SELECT s.*, p.name as player_name,
                         CASE 
                             WHEN s.session_type = "group" THEN "group-session"
                             WHEN s.session_type = "private" THEN "private-session"
                             WHEN s.session_type = "tournament" THEN "tournament"
                             ELSE "other"
                         END as event_type
                         FROM training_sessions s 
                         LEFT JOIN players p ON s.player_id = p.id 
                         WHERE s.trainer_id = :trainer_id 
                         AND s.session_date >= CURDATE()
                         ORDER BY s.session_date ASC');
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    public function addSession($data) {
        $this->db->query('INSERT INTO training_sessions 
                         (trainer_id, session_type, session_date, session_time, duration, player_id, description, status) 
                         VALUES (:trainer_id, :session_type, :date, :time, :duration, :player_id, :description, "scheduled")');
        
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':session_type', $data['session_type']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':time', $data['time']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':description', $data['description']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function updateSession($data) {
        $this->db->query('UPDATE training_sessions 
                         SET session_type = :session_type, session_date = :date, session_time = :time, 
                             duration = :duration, player_id = :player_id, description = :description, status = :status
                         WHERE id = :session_id AND trainer_id = :trainer_id');
        
        $this->db->bind(':session_id', $data['session_id']);
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':session_type', $data['session_type']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':time', $data['time']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    public function deleteSession($session_id, $trainer_id) {
        $this->db->query('DELETE FROM training_sessions WHERE id = :session_id AND trainer_id = :trainer_id');
        $this->db->bind(':session_id', $session_id);
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->execute();
    }

    // Booking Management
    public function getBookings($trainer_id) {
        $this->db->query('SELECT b.*, p.name as player_name, s.name as service_name
                         FROM bookings b 
                         JOIN players p ON b.player_id = p.id
                         LEFT JOIN services s ON b.service_id = s.id
                         WHERE b.trainer_id = :trainer_id 
                         ORDER BY b.booking_date DESC');
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    public function updateBookingStatus($booking_id, $status, $trainer_id) {
        $this->db->query('UPDATE bookings SET status = :status WHERE id = :booking_id AND trainer_id = :trainer_id');
        $this->db->bind(':booking_id', $booking_id);
        $this->db->bind(':status', $status);
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->execute();
    }

    // Tournament Management
    public function getTournaments() {
        $this->db->query('SELECT * FROM tournaments WHERE tournament_date >= CURDATE() ORDER BY tournament_date ASC');
        return $this->db->resultSet();
    }

    public function getTournamentParticipants($tournament_id) {
        $this->db->query('SELECT tp.*, p.name as player_name 
                         FROM tournament_participants tp 
                         JOIN players p ON tp.player_id = p.id 
                         WHERE tp.tournament_id = :tournament_id');
        $this->db->bind(':tournament_id', $tournament_id);
        return $this->db->resultSet();
    }

    // Nutrition Management
    public function getNutritionPlans() {
        $this->db->query('SELECT np.*, p.name as player_name 
                         FROM nutrition_plans np 
                         LEFT JOIN players p ON np.player_id = p.id 
                         ORDER BY np.created_at DESC');
        return $this->db->resultSet();
    }

    public function getSupplements() {
        $this->db->query('SELECT * FROM supplements ORDER BY name ASC');
        return $this->db->resultSet();
    }

    public function addNutritionPlan($data) {
        $this->db->query('INSERT INTO nutrition_plans 
                         (player_id, trainer_id, plan_name, description, meal_plan, supplements, notes) 
                         VALUES (:player_id, :trainer_id, :plan_name, :description, :meal_plan, :supplements, :notes)');
        
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':plan_name', $data['plan_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':meal_plan', $data['meal_plan']);
        $this->db->bind(':supplements', $data['supplements']);
        $this->db->bind(':notes', $data['notes']);

        return $this->db->execute();
    }

    /**
     * Returns own active/draft plans + other trainers' active plans (read-only).
     * Each row has is_own (1/0) and trainer_name so the view can badge them.
     * Archived plans are excluded from the visible library.
     */
    public function getWorkoutPlansWithVisibility($trainer_id) {
        $this->db->query('
            SELECT
                wp.PlanID,
                wp.TrainerID,
                wp.workoutname,
                wp.frequency,
                wp.Duration,
                wp.VideoLink,
                wp.Intensity,
                wp.NotSuitableFor,
                wp.Benefits,
                wp.Status,
                wp.CreatedDate,
                CONCAT(u.FirstName, \' \', u.LastName) AS trainer_name,
                IF(wp.TrainerID = :trainer_id, 1, 0) AS is_own,
                COUNT(wpp.PlayerID) AS assigned_count
            FROM workoutplan wp
            JOIN `user` u ON wp.TrainerID = u.UserID
            LEFT JOIN workoutplan_player wpp
                ON wp.PlanID = wpp.PlanID AND wpp.Status = \'active\'
            WHERE wp.Status != \'archived\'
            GROUP BY wp.PlanID
            ORDER BY is_own DESC, wp.CreatedDate DESC
        ');
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    /**
     * Get all players assigned to a specific plan (with assignment lifecycle fields).
     */
    public function getAssignedPlayersForPlan($plan_id) {
        $this->db->query('
            SELECT
                pp.PlayerID,
                CONCAT(u.FirstName, \' \', u.LastName) AS player_name,
                u.Email         AS player_email,
                wpp.AssignedBy,
                wpp.AssignedDate,
                wpp.EndDate,
                wpp.Status      AS assignment_status,
                CONCAT(assigned_t.FirstName, \' \', assigned_t.LastName) AS assigned_by_name
            FROM workoutplan_player wpp
            JOIN playerprofile pp  ON wpp.PlayerID  = pp.PlayerID
            JOIN `user` u          ON pp.PlayerID   = u.UserID
            LEFT JOIN `user` assigned_t ON wpp.AssignedBy = assigned_t.UserID
            WHERE wpp.PlanID = :plan_id
            ORDER BY wpp.AssignedDate DESC
        ');
        $this->db->bind(':plan_id', $plan_id);
        return $this->db->resultSet();
    }

    /**
     * Assign a workout plan to a player.
     * Returns 'duplicate' if the player already has an active assignment for this plan,
     * true on success, false on DB failure.
     */
    public function assignPlanToPlayer($data) {
        // Block duplicate active assignment
        $this->db->query('
            SELECT 1 FROM workoutplan_player
            WHERE PlanID = :plan_id AND PlayerID = :player_id AND Status = \'active\'
        ');
        $this->db->bind(':plan_id',   $data['plan_id']);
        $this->db->bind(':player_id', $data['player_id']);
        if ($this->db->single()) {
            return 'duplicate';
        }

        $this->db->query('
            INSERT INTO workoutplan_player
                (PlanID, PlayerID, AssignedDate, AssignedBy, Status, EndDate)
            VALUES
                (:plan_id, :player_id, CURDATE(), :assigned_by, \'active\', :end_date)
            ON DUPLICATE KEY UPDATE
                AssignedBy   = VALUES(AssignedBy),
                AssignedDate = CURDATE(),
                Status       = \'active\',
                EndDate      = VALUES(EndDate)
        ');
        $this->db->bind(':plan_id',     $data['plan_id']);
        $this->db->bind(':player_id',   $data['player_id']);
        $this->db->bind(':assigned_by', $data['assigned_by']);
        $this->db->bind(':end_date',    !empty($data['end_date']) ? $data['end_date'] : null);
        return $this->db->execute() ? true : false;
    }

    /**
     * Unassign (delete) a plan from a player.
     * Only the trainer who originally assigned it may remove it.
     */
    public function unassignPlanFromPlayer($plan_id, $player_id, $trainer_id) {
        $this->db->query('
            DELETE FROM workoutplan_player
            WHERE PlanID = :plan_id AND PlayerID = :player_id AND AssignedBy = :trainer_id
        ');
        $this->db->bind(':plan_id',    $plan_id);
        $this->db->bind(':player_id',  $player_id);
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->execute();
    }

    /**
     * Update the status of a player's assignment (active / completed / paused).
     * Only the assigning trainer may change the status.
     */
    public function updateAssignmentStatus($plan_id, $player_id, $trainer_id, $status) {
        $allowed = ['active', 'completed', 'paused'];
        if (!in_array($status, $allowed)) return false;

        $this->db->query('
            UPDATE workoutplan_player
            SET Status = :status
            WHERE PlanID = :plan_id AND PlayerID = :player_id AND AssignedBy = :trainer_id
        ');
        $this->db->bind(':status',     $status);
        $this->db->bind(':plan_id',    $plan_id);
        $this->db->bind(':player_id',  $player_id);
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->execute();
    }

    public function getExercises() {
        $this->db->query('SELECT * FROM exercises ORDER BY category ASC, name ASC');
        return $this->db->resultSet();
    }

    public function addWorkoutPlan($data) {
        $this->db->query('INSERT INTO workoutplan (TrainerID, workoutname, frequency, Duration, VideoLink, Intensity, NotSuitableFor, Benefits, Status)
            VALUES (:trainer_id, :workoutname, :frequency, :duration, :videolink, :intensity, :notsuitablefor, :benefits, :status)');

        $this->db->bind(':trainer_id',     $data['trainer_id']);
        $this->db->bind(':workoutname',    $data['workoutname']);
        $this->db->bind(':frequency',      $data['frequency']);
        $this->db->bind(':duration',       $data['duration']);
        $this->db->bind(':videolink',      $data['videolink']);
        $this->db->bind(':intensity',      $data['intensity']);
        $this->db->bind(':notsuitablefor', $data['notsuitablefor']);
        $this->db->bind(':benefits',       $data['benefits']);
        $this->db->bind(':status',         $data['status'] ?? 'active');

        return $this->db->execute();
    }

    // Update workout plan
    public function updateWorkoutPlan($data) {
        $this->db->query('UPDATE workoutplan 
            SET workoutname = :workoutname, 
                frequency = :frequency, 
                Duration = :duration,
                VideoLink = :videolink,
                Intensity = :intensity,
                NotSuitableFor = :notsuitablefor,
                Benefits = :benefits,
                Status = :status
            WHERE PlanID = :plan_id AND TrainerID = :trainer_id');
        
        $this->db->bind(':plan_id', $data['plan_id']);
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':workoutname', $data['workoutname']);
        $this->db->bind(':frequency', $data['frequency']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':videolink', $data['videolink']);
        $this->db->bind(':intensity', $data['intensity']);
        $this->db->bind(':notsuitablefor', $data['notsuitablefor']);
        $this->db->bind(':benefits', $data['benefits']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        
        return $this->db->execute();
    }

    // Delete workout plan
    public function deleteWorkoutPlan($plan_id, $trainer_id) {
        $this->db->query('DELETE FROM workoutplan 
            WHERE PlanID = :plan_id AND TrainerID = :trainer_id');
        
        $this->db->bind(':plan_id', $plan_id);
        $this->db->bind(':trainer_id', $trainer_id);
        
        return $this->db->execute();
    }

    // Get single workout plan by ID
    public function getWorkoutPlanById($plan_id) {
        $this->db->query('SELECT *, Duration AS durationdays FROM workoutplan WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', $plan_id);
        
        return $this->db->single();
    }

    // Get all players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT pp.PlayerID, CONCAT(u.FirstName, \' \', u.LastName) AS Name FROM playerprofile pp JOIN `user` u ON pp.PlayerID = u.UserID ORDER BY u.FirstName');
        return $this->db->resultSet();
    }

    // Medical Records
    public function getMedicalRecords($trainer_id) {
        $this->db->query('SELECT mr.*, p.name as player_name 
                         FROM medical_records mr 
                         JOIN players p ON mr.player_id = p.id 
                         WHERE mr.trainer_id = :trainer_id OR mr.is_public = 1
                         ORDER BY mr.record_date DESC');
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    public function addMedicalRecord($data) {
        $this->db->query('INSERT INTO medical_records 
                         (player_id, trainer_id, record_type, description, recommendations, record_date, is_public) 
                         VALUES (:player_id, :trainer_id, :record_type, :description, :recommendations, :record_date, :is_public)');
        
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':record_type', $data['record_type']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':recommendations', $data['recommendations']);
        $this->db->bind(':record_date', $data['record_date']);
        $this->db->bind(':is_public', $data['is_public']);

        return $this->db->execute();
    }

    public function updateMedicalRecord($data) {
        $this->db->query('UPDATE medical_records 
                         SET record_type = :record_type, description = :description, 
                             recommendations = :recommendations, record_date = :record_date, is_public = :is_public
                         WHERE id = :record_id AND trainer_id = :trainer_id');
        
        $this->db->bind(':record_id', $data['record_id']);
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':record_type', $data['record_type']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':recommendations', $data['recommendations']);
        $this->db->bind(':record_date', $data['record_date']);
        $this->db->bind(':is_public', $data['is_public']);

        return $this->db->execute();
    }

    // Player Management
    public function getPlayersForTrainer($trainer_id) {
        $this->db->query('SELECT DISTINCT p.* 
                         FROM players p 
                         LEFT JOIN training_sessions ts ON p.id = ts.player_id 
                         WHERE ts.trainer_id = :trainer_id OR p.assigned_trainer = :trainer_id
                         ORDER BY p.name ASC');
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    // Dashboard Statistics
    public function getDashboardStats($trainer_id) {
        $stats = [];
        
        // Today's sessions
        $this->db->query('SELECT COUNT(*) as count FROM training_sessions 
                         WHERE trainer_id = :trainer_id AND session_date = CURDATE()');
        $this->db->bind(':trainer_id', $trainer_id);
        $stats['today_sessions'] = $this->db->single()->count;
        
        // Active players
        $this->db->query('SELECT COUNT(DISTINCT player_id) as count FROM training_sessions 
                         WHERE trainer_id = :trainer_id AND session_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
        $this->db->bind(':trainer_id', $trainer_id);
        $stats['active_players'] = $this->db->single()->count;
        
        // Private sessions today
        $this->db->query('SELECT COUNT(*) as count FROM training_sessions 
                         WHERE trainer_id = :trainer_id AND session_date = CURDATE() AND session_type = "private"');
        $this->db->bind(':trainer_id', $trainer_id);
        $stats['private_sessions'] = $this->db->single()->count;
        
        // Upcoming tournaments
        $this->db->query('SELECT COUNT(*) as count FROM tournaments WHERE tournament_date >= CURDATE()');
        $stats['upcoming_tournaments'] = $this->db->single()->count;
        
        return $stats;
    }

    // Get workout plans assigned to a player (includes assignment lifecycle fields)
    public function getWorkoutPlansByPlayer($playerId) {
        $this->db->query('
            SELECT
                wp.*,
                CONCAT(u.FirstName, \' \', u.LastName) AS trainer_name,
                wpp.PlayerID   AS assigned_player_id,
                wpp.AssignedDate,
                wpp.EndDate,
                wpp.Status      AS assignment_status,
                CONCAT(ab.FirstName, \' \', ab.LastName) AS assigned_by_name
            FROM workoutplan wp
            JOIN workoutplan_player wpp ON wp.PlanID = wpp.PlanID
            JOIN `user` u               ON wp.TrainerID = u.UserID
            LEFT JOIN `user` ab          ON wpp.AssignedBy = ab.UserID
            WHERE wpp.PlayerID = :player_id
            ORDER BY
                FIELD(wpp.Status, \'active\', \'paused\', \'completed\'),
                wpp.AssignedDate DESC
        ');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get trainer bookings (appointments) from the trainerappointment table  
    public function getTrainerAppointments($trainerId) {
        $this->db->query('SELECT ta.*, CONCAT(u.FirstName, \' \', u.LastName) AS player_name, u.Email AS player_email,
            pp.BattingStyle, pp.BowlingStyle, pp.PlayingRole
            FROM trainerappointment ta
            JOIN user u ON ta.PlayerID = u.UserID
            LEFT JOIN playerprofile pp ON ta.PlayerID = pp.PlayerID
            WHERE ta.TrainerID = :trainer_id
            ORDER BY ta.AppointmentDate DESC, ta.StartTime ASC');
        $this->db->bind(':trainer_id', $trainerId);
        return $this->db->resultSet();
    }
}
