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

    // Workout Management
    public function getWorkoutPlans($trainer_id = null) {
        if (!$trainer_id) {
            $trainer_id = $_SESSION['user_id'] ?? 1;
        }
        
        $this->db->query('SELECT 
            wp.PlanID,
            wp.TrainerID,
            wp.workoutname,
            wp.frequency,
            wp.Duration,
            wp.Duration AS durationdays,
            wp.VideoLink,
            wp.Intensity,
            wp.NotSuitableFor,
            wp.Benefits,
            wp.CreatedDate
        FROM WorkoutPlan wp 
        WHERE wp.TrainerID = :trainer_id
        ORDER BY wp.CreatedDate DESC');
        
        $this->db->bind(':trainer_id', $trainer_id);
        return $this->db->resultSet();
    }

    public function getExercises() {
        $this->db->query('SELECT * FROM exercises ORDER BY category ASC, name ASC');
        return $this->db->resultSet();
    }

    public function addWorkoutPlan($data) {
        try {
            error_log("=== M_Trainer::addWorkoutPlan() ===");
            error_log("Data received: " . print_r($data, true));
            
            $this->db->query('INSERT INTO WorkoutPlan (TrainerID, workoutname, frequency, Duration, VideoLink, Intensity, NotSuitableFor, Benefits) 
                VALUES (:trainer_id, :workoutname, :frequency, :duration, :videolink, :intensity, :notsuitablefor, :benefits)');
            
            $this->db->bind(':trainer_id', $data['trainer_id']);
            $this->db->bind(':workoutname', $data['workoutname']);
            $this->db->bind(':frequency', $data['frequency']);
            $this->db->bind(':duration', $data['duration']);
            $this->db->bind(':videolink', $data['videolink']);
            $this->db->bind(':intensity', $data['intensity']);
            $this->db->bind(':notsuitablefor', $data['notsuitablefor']);
            $this->db->bind(':benefits', $data['benefits']);
            
            error_log("Query prepared, executing...");
            $result = $this->db->execute();
            error_log("Execute result: " . ($result ? 'TRUE' : 'FALSE'));
            
            return $result;
        } catch (PDOException $e) {
            error_log("PDO Exception in addWorkoutPlan: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        } catch (Exception $e) {
            error_log("Exception in addWorkoutPlan: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    // Update workout plan
    public function updateWorkoutPlan($data) {
        $this->db->query('UPDATE WorkoutPlan 
            SET workoutname = :workoutname, 
                frequency = :frequency, 
                Duration = :duration,
                VideoLink = :videolink,
                Intensity = :intensity,
                NotSuitableFor = :notsuitablefor,
                Benefits = :benefits
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
        
        return $this->db->execute();
    }

    // Delete workout plan
    public function deleteWorkoutPlan($plan_id, $trainer_id) {
        $this->db->query('DELETE FROM WorkoutPlan 
            WHERE PlanID = :plan_id AND TrainerID = :trainer_id');
        
        $this->db->bind(':plan_id', $plan_id);
        $this->db->bind(':trainer_id', $trainer_id);
        
        return $this->db->execute();
    }

    // Get single workout plan by ID
    public function getWorkoutPlanById($plan_id) {
    $this->db->query('SELECT *, Duration AS durationdays FROM WorkoutPlan WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', $plan_id);
        
        return $this->db->single();
    }

    // Get all workout plans with trainer information for players to view
    public function getAllWorkoutPlansWithTrainers() {
        $this->db->query('SELECT 
            wp.PlanID,
            wp.TrainerID,
            wp.workoutname,
            wp.frequency,
            wp.Duration,
            wp.Duration AS durationdays,
            wp.VideoLink,
            wp.Intensity,
            wp.NotSuitableFor,
            wp.Benefits,
            wp.CreatedDate,
            u.name as trainer_name,
            u.email as trainer_email
        FROM WorkoutPlan wp 
        LEFT JOIN users u ON wp.TrainerID = u.user_id
        ORDER BY wp.CreatedDate DESC');
        
        return $this->db->resultSet();
    }

    // Get all players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT PlayerID, Name FROM PlayerProfile ORDER BY Name');
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
}
