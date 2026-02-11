<?php
class M_Session {
    /**
     * Get upcoming sessions for a player (enrolled, active, future)
     * @param int $playerId
     * @return array
     */
    public function getUpcomingSessionsForPlayer($playerId) {
        $this->db->query('SELECT 
                s.SessionID,
                s.Name,
                s.Date,
                s.StartTime,
                s.EndTime,
                s.Location,
                s.SessionType,
                s.SessionMode,
                u.Name AS CoachName
            FROM Session s
            JOIN SessionEnrollment se ON s.SessionID = se.SessionID
            LEFT JOIN User u ON s.CoachOrTrainerID = u.UserID
            WHERE se.PlayerID = :player_id
              AND se.Status = "enrolled"
              AND s.Status = "active"
              AND s.Date >= CURDATE()
            ORDER BY s.Date ASC, s.StartTime ASC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // ==================== CREATE ====================
    
    /**
     * Create a new session using existing schema
     * @param array $data Session data
     * @return int|false Session ID on success, false on failure
     */
    public function createSession($data) {
        // Enhanced logging for debugging
        error_log('=== SESSION CREATION DEBUG START ===');
        error_log('Input data: ' . print_r($data, true));
        
        // Insert into main Session table with backticks for reserved keywords
        $this->db->query('INSERT INTO `Session` (
            `SessionType`,
            `SessionMode`,
            `CoachOrTrainerID`,
            `Name`,
            `Date`,
            `StartTime`,
            `EndTime`,
            `Location`,
            `Status`,
            `MaxParticipants`,
            `PricePerSession`,
            `IsRecurring`
        ) VALUES (
            :session_type,
            :session_mode,
            :coach_id,
            :name,
            :date,
            :start_time,
            :end_time,
            :location,
            :status,
            :max_participants,
            :price,
            :is_recurring
        )');

        // Validate and map session_type to SessionType ENUM
        $sessionType = ($data['session_type'] == 'Coaching') ? 'Coaching' : 'Physical Training';
        error_log('Mapped SessionType: ' . $sessionType);
        
        // Bind all parameters with explicit types
        $this->db->bind(':session_type', $sessionType, PDO::PARAM_STR);
        $this->db->bind(':session_mode', $data['session_mode'] ?? 'Group', PDO::PARAM_STR);
        $this->db->bind(':coach_id', (int)$data['coach_id'], PDO::PARAM_INT);
        $this->db->bind(':name', $data['title'], PDO::PARAM_STR);
        $this->db->bind(':date', $data['session_date'], PDO::PARAM_STR);
        $this->db->bind(':start_time', $data['start_time'], PDO::PARAM_STR);
        $this->db->bind(':end_time', $data['end_time'], PDO::PARAM_STR);
        $this->db->bind(':location', $data['location'] ?? '', PDO::PARAM_STR);
        $this->db->bind(':status', 'active', PDO::PARAM_STR);
        $this->db->bind(':max_participants', (int)($data['max_participants'] ?? 10), PDO::PARAM_INT);
        $this->db->bind(':price', (float)($data['price'] ?? 0.00), PDO::PARAM_STR); // Use STR for DECIMAL
        
        // Convert boolean to integer for MySQL BOOLEAN (TINYINT)
        $isRecurring = filter_var($data['is_recurring'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->db->bind(':is_recurring', $isRecurring ? 1 : 0, PDO::PARAM_INT);
        
        error_log('All parameters bound successfully');

        // Execute with error handling
        if ($this->db->execute()) {
            $sessionId = $this->db->lastInsertId();
            error_log('✅ SUCCESS! Session created with ID: ' . $sessionId);
            error_log('=== SESSION CREATION DEBUG END ===');
            
            return $sessionId;
        } else {
            // Log detailed error information
            error_log('❌ FAILED! Database execute() returned false');
            
            // Try to get PDO error info (if your Database class exposes it)
            if (method_exists($this->db, 'getError')) {
                error_log('PDO Error: ' . print_r($this->db->getError(), true));
            }
            
            error_log('=== SESSION CREATION DEBUG END ===');
            return false;
        }
    }

    /**
     * Add a player to a session
     * @param int $sessionId
     * @param int $playerId
     * @return bool
     */
    public function addPlayerToSession($sessionId, $playerId) {
        $this->db->query('INSERT INTO SessionEnrollment (SessionID, PlayerID, Status) 
                         VALUES (:session_id, :player_id, "enrolled")
                         ON DUPLICATE KEY UPDATE Status = "enrolled"');
        
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':player_id', $playerId);
        
        return $this->db->execute();
    }

    // ==================== READ ====================
    
    /**
     * Get session by ID
     * @param int $id Session ID
     * @return object|false Session object or false
     */
    public function getSessionById($id) {
        $this->db->query('SELECT 
                s.*,
                u.Name AS CoachName
            FROM `Session` s
            LEFT JOIN User u ON s.CoachOrTrainerID = u.UserID
            WHERE s.SessionID = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    /**
     * Get sessions by coach with optional filters
     * @param int $coachId
     * @param array $filters
     * @return array
     */
    public function getSessionsByCoach($coachId, $filters = []) {
        error_log('M_Session::getSessionsByCoach called with coach_id: ' . $coachId);
        
        $query = 'SELECT 
                s.SessionID,
                s.SessionType,
                s.SessionMode,
                s.CoachOrTrainerID,
                s.Name,
                s.`Date`,
                s.StartTime,
                s.EndTime,
                s.Location,
                s.`Status`,
                s.MaxParticipants,
                s.PricePerSession,
                s.IsRecurring,
                (SELECT COUNT(*) FROM SessionEnrollment WHERE SessionID = s.SessionID AND `Status` != "cancelled") AS ParticipantCount
            FROM `Session` s
            WHERE s.CoachOrTrainerID = :coach_id';
        
        // Add filters
        if (!empty($filters['type'])) {
            $query .= ' AND s.SessionType = :type';
        }
        if (!empty($filters['status'])) {
            $query .= ' AND s.`Status` = :status';
        }
        if (!empty($filters['dateFrom'])) {
            $query .= ' AND s.`Date` >= :date_from';
        }
        if (!empty($filters['dateTo'])) {
            $query .= ' AND s.`Date` <= :date_to';
        }
        if (!empty($filters['search'])) {
            $query .= ' AND (s.Name LIKE :search OR s.Location LIKE :search)';
        }
        
        $query .= ' ORDER BY s.`Date` DESC, s.StartTime DESC';
        
        error_log('Query: ' . $query);
        
        $this->db->query($query);
        
        // Bind parameters
        $this->db->bind(':coach_id', $coachId);
        if (!empty($filters['type'])) {
            $this->db->bind(':type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        if (!empty($filters['dateFrom'])) {
            $this->db->bind(':date_from', $filters['dateFrom']);
        }
        if (!empty($filters['dateTo'])) {
            $this->db->bind(':date_to', $filters['dateTo']);
        }
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        
        $result = $this->db->resultSet();
        error_log('Query returned ' . count($result) . ' rows');
        
        return $result;
    }

    /**
     * Get calendar sessions for custom calendar
     * @param int $coachId
     * @param string $start Start date
     * @param string $end End date
     * @return array
     */
    public function getCalendarSessions($coachId, $start = null, $end = null) {
        $query = 'SELECT 
                s.*,
                (SELECT COUNT(*) FROM SessionEnrollment WHERE SessionID = s.SessionID AND Status != "cancelled") AS ParticipantCount
            FROM Session s
            WHERE s.CoachOrTrainerID = :coach_id
            AND s.Status != "cancelled"';
        
        if ($start) {
            $query .= ' AND s.Date >= :start';
        }
        if ($end) {
            $query .= ' AND s.Date <= :end';
        }
        
        $query .= ' ORDER BY s.Date, s.StartTime';
        
        $this->db->query($query);
        $this->db->bind(':coach_id', $coachId);
        
        if ($start) {
            $this->db->bind(':start', $start);
        }
        if ($end) {
            $this->db->bind(':end', $end);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Get all sessions (no coach filter) with optional filters
     * @param array $filters
     * @return array
     */
    public function getAllSessions($filters = []) {
        $query = 'SELECT 
                s.SessionID,
                s.SessionType,
                s.SessionMode,
                s.CoachOrTrainerID,
                s.Name,
                s.`Date`,
                s.StartTime,
                s.EndTime,
                s.Location,
                s.`Status`,
                s.MaxParticipants,
                s.PricePerSession,
                s.IsRecurring,
                (SELECT COUNT(*) FROM SessionEnrollment WHERE SessionID = s.SessionID AND `Status` != "cancelled") AS ParticipantCount
            FROM `Session` s
            WHERE 1=1';

        if (!empty($filters['type'])) {
            $query .= ' AND s.SessionType = :type';
        }
        if (!empty($filters['status'])) {
            $query .= ' AND s.`Status` = :status';
        }
        if (!empty($filters['dateFrom'])) {
            $query .= ' AND s.`Date` >= :date_from';
        }
        if (!empty($filters['dateTo'])) {
            $query .= ' AND s.`Date` <= :date_to';
        }
        if (!empty($filters['search'])) {
            $query .= ' AND (s.Name LIKE :search OR s.Location LIKE :search)';
        }

        $query .= ' ORDER BY s.`Date` DESC, s.StartTime DESC';

        $this->db->query($query);

        // Bind filters
        if (!empty($filters['type'])) {
            $this->db->bind(':type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        if (!empty($filters['dateFrom'])) {
            $this->db->bind(':date_from', $filters['dateFrom']);
        }
        if (!empty($filters['dateTo'])) {
            $this->db->bind(':date_to', $filters['dateTo']);
        }
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }

        $result = $this->db->resultSet();
        error_log('getAllSessions returned ' . count($result) . ' rows');
        return $result;
    }

    /**
     * Get session participants
     * @param int $sessionId
     * @return array
     */
    public function getSessionParticipants($sessionId) {
        $this->db->query('SELECT 
                se.EnrollmentID,
                se.PlayerID,
                se.Status,
                u.Name AS PlayerName,
                u.Email,
                u.PhoneNumber
            FROM SessionEnrollment se
            JOIN User u ON se.PlayerID = u.UserID
            WHERE se.SessionID = :session_id');
        
        $this->db->bind(':session_id', $sessionId);
        
        return $this->db->resultSet();
    }

    /**
     * Get session attendance records
     * @param int $sessionId
     * @return array
     */
    public function getSessionAttendance($sessionId) {
        $this->db->query('SELECT 
                sa.AttendanceID,
                sa.EnrollmentID,
                sa.AttendanceStatus,
                sa.AttendanceNotes,
                sa.MarkedAt,
                se.PlayerID,
                u.Name AS PlayerName
            FROM SessionAttendance sa
            JOIN SessionEnrollment se ON sa.EnrollmentID = se.EnrollmentID
            JOIN User u ON se.PlayerID = u.UserID
            WHERE se.SessionID = :session_id
            ORDER BY u.Name');
        
        $this->db->bind(':session_id', $sessionId);
        
        return $this->db->resultSet();
    }

    // ==================== STATISTICS ====================
    
    /**
     * Get today's sessions count
     * @param int $coachId
     * @return int
     */
    public function getTodaySessions($coachId) {
        $this->db->query('SELECT COUNT(*) as count 
                         FROM Session 
                         WHERE CoachOrTrainerID = :coach_id 
                         AND Date = CURDATE()
                         AND Status = "active"');
        
        $this->db->bind(':coach_id', $coachId);
        $result = $this->db->single();
        
        return $result->count ?? 0;
    }

    /**
     * Get this week's sessions count
     * @param int $coachId
     * @return int
     */
    public function getThisWeekSessions($coachId) {
        $this->db->query('SELECT COUNT(*) as count 
                         FROM Session 
                         WHERE CoachOrTrainerID = :coach_id 
                         AND YEARWEEK(Date, 1) = YEARWEEK(CURDATE(), 1)
                         AND Status = "active"');
        
        $this->db->bind(':coach_id', $coachId);
        $result = $this->db->single();
        
        return $result->count ?? 0;
    }

    /**
     * Get total sessions count
     * @param int $coachId
     * @return int
     */
    public function getTotalSessions($coachId) {
        $this->db->query('SELECT COUNT(*) as count 
                         FROM Session 
                         WHERE CoachOrTrainerID = :coach_id 
                         AND Status != "cancelled"');
        
        $this->db->bind(':coach_id', $coachId);
        $result = $this->db->single();
        
        return $result->count ?? 0;
    }

    /**
     * Get average attendance rate
     * @param int $coachId
     * @return float
     */
    public function getAverageAttendance($coachId) {
        $this->db->query('SELECT 
                AVG(
                    (SELECT COUNT(*) FROM SessionAttendance sa 
                     JOIN SessionEnrollment se ON sa.EnrollmentID = se.EnrollmentID
                     WHERE se.SessionID = s.SessionID AND sa.AttendanceStatus = "present") * 100.0 /
                    NULLIF((SELECT COUNT(*) FROM SessionEnrollment se2 
                            WHERE se2.SessionID = s.SessionID AND se2.Status != "cancelled"), 0)
                ) as avg_attendance
            FROM Session s
            WHERE s.CoachOrTrainerID = :coach_id
            AND s.Status = "completed"
            AND EXISTS (SELECT 1 FROM SessionEnrollment se3 WHERE se3.SessionID = s.SessionID)');
        
        $this->db->bind(':coach_id', $coachId);
        $result = $this->db->single();
        
        return round($result->avg_attendance ?? 85.0, 1);
    }

    // ==================== UPDATE ====================
    
    /**
     * Update session
     * @param int $id Session ID
     * @param array $data Updated data
     * @return bool
     */
    public function updateSession($id, $data) {
        error_log('=== SESSION UPDATE DEBUG START ===');
        error_log('Updating session ID: ' . $id);
        error_log('Update data: ' . print_r($data, true));
        
        $this->db->query('UPDATE `Session` SET
            SessionType = :session_type,
            SessionMode = :session_mode,
            Name = :name,
            `Date` = :date,
            StartTime = :start_time,
            EndTime = :end_time,
            Location = :location,
            MaxParticipants = :max_participants,
            PricePerSession = :price,
            IsRecurring = :is_recurring,
            `Status` = :status
            WHERE SessionID = :id');

        $this->db->bind(':id', (int)$id, PDO::PARAM_INT);
        $this->db->bind(':session_type', $data['session_type'], PDO::PARAM_STR);
        $this->db->bind(':session_mode', $data['session_mode'] ?? 'Group', PDO::PARAM_STR);
        $this->db->bind(':name', $data['title'], PDO::PARAM_STR);
        $this->db->bind(':date', $data['session_date'], PDO::PARAM_STR);
        $this->db->bind(':start_time', $data['start_time'], PDO::PARAM_STR);
        $this->db->bind(':end_time', $data['end_time'], PDO::PARAM_STR);
        $this->db->bind(':location', $data['location'] ?? '', PDO::PARAM_STR);
        $this->db->bind(':max_participants', (int)($data['max_participants'] ?? 10), PDO::PARAM_INT);
        $this->db->bind(':price', (float)($data['price'] ?? 0.00), PDO::PARAM_STR);
        $this->db->bind(':status', $data['status'] ?? 'active', PDO::PARAM_STR);
        
        // Convert boolean to integer
        $isRecurring = filter_var($data['is_recurring'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->db->bind(':is_recurring', $isRecurring ? 1 : 0, PDO::PARAM_INT);

        if ($this->db->execute()) {
            error_log('✅ Session updated successfully');
            error_log('=== SESSION UPDATE DEBUG END ===');
            return true;
        } else {
            error_log('❌ Failed to update session');
            if (method_exists($this->db, 'getError')) {
                error_log('PDO Error: ' . print_r($this->db->getError(), true));
            }
            error_log('=== SESSION UPDATE DEBUG END ===');
            return false;
        }
    }

    /**
     * Cancel session
     * @param int $id Session ID
     * @param string $reason Cancellation reason
     * @return bool
     */
    public function cancelSession($id, $reason = '') {
        $this->db->query('UPDATE Session SET
            Status = "cancelled"
            WHERE SessionID = :id');

        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            return true;
        }
        
        return false;
    }

    /**
     * Reschedule session
     * @param int $id Session ID
     * @param array $data New date/time data
     * @return bool
     */
    public function rescheduleSession($id, $data) {
        $this->db->query('UPDATE Session SET
            Date = :date,
            StartTime = :start_time,
            EndTime = :end_time
            WHERE SessionID = :id');

        $this->db->bind(':id', $id);
        $this->db->bind(':date', $data['session_date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);

        return $this->db->execute();
    }

    /**
     * Update session participants
     * @param int $sessionId
     * @param array $playerIds Array of player IDs
     * @return bool
     */
    public function updateSessionParticipants($sessionId, $playerIds) {
        // Cancel all current enrollments
        $this->db->query('UPDATE SessionEnrollment SET Status = "cancelled" WHERE SessionID = :session_id');
        $this->db->bind(':session_id', $sessionId);
        $this->db->execute();
        
        // Add new participants
        if (!empty($playerIds)) {
            foreach ($playerIds as $playerId) {
                $this->addPlayerToSession($sessionId, $playerId);
            }
        }
        
        return true;
    }

    /**
     * Mark attendance for a player in a session
     * @param array $data Attendance data
     * @return bool
     */
    public function markAttendance($data) {
        // Get enrollment ID
        $this->db->query('SELECT EnrollmentID FROM SessionEnrollment 
                         WHERE SessionID = :session_id AND PlayerID = :player_id');
        $this->db->bind(':session_id', $data['session_id']);
        $this->db->bind(':player_id', $data['player_id']);
        
        $enrollment = $this->db->single();
        
        if (!$enrollment) {
            return false;
        }
        
        // Insert or update attendance
        $this->db->query('INSERT INTO SessionAttendance (
            EnrollmentID,
            AttendanceStatus,
            AttendanceNotes,
            MarkedBy
        ) VALUES (
            :enrollment_id,
            :status,
            :notes,
            :marked_by
        ) ON DUPLICATE KEY UPDATE
            AttendanceStatus = :status,
            AttendanceNotes = :notes,
            MarkedBy = :marked_by,
            MarkedAt = CURRENT_TIMESTAMP');

        $this->db->bind(':enrollment_id', $enrollment->EnrollmentID);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes'] ?? '');
        $this->db->bind(':marked_by', $data['marked_by']);

        return $this->db->execute();
    }

    // ==================== DELETE ====================
    
    /**
     * Delete session
     * @param int $id Session ID
     * @return bool
     */
    public function deleteSession($id) {
        error_log('=== SESSION DELETION DEBUG START ===');
        error_log('Deleting session ID: ' . $id);
        
        // Delete from Session table (CASCADE will handle SessionParticipant)
        $this->db->query('DELETE FROM `Session` WHERE SessionID = :id');
        $this->db->bind(':id', (int)$id, PDO::PARAM_INT);

        if ($this->db->execute()) {
            error_log('✅ Session deleted successfully');
            error_log('=== SESSION DELETION DEBUG END ===');
            return true;
        } else {
            error_log('❌ Failed to delete session');
            if (method_exists($this->db, 'getError')) {
                error_log('PDO Error: ' . print_r($this->db->getError(), true));
            }
            error_log('=== SESSION DELETION DEBUG END ===');
            return false;
        }
    }

    /**
     * Check for session conflicts
     * @param int $coachId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int $excludeSessionId Optional session ID to exclude from check
     * @return bool True if conflict exists
     */
    public function hasConflict($coachId, $date, $startTime, $endTime, $excludeSessionId = null) {
        $query = 'SELECT COUNT(*) as count
            FROM Session
            WHERE CoachOrTrainerID = :coach_id
            AND Date = :date
            AND Status != "cancelled"
            AND (
                (StartTime < :end_time AND EndTime > :start_time)
            )';
        
        if ($excludeSessionId) {
            $query .= ' AND SessionID != :exclude_id';
        }
        
        $this->db->query($query);
        $this->db->bind(':coach_id', $coachId);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        if ($excludeSessionId) {
            $this->db->bind(':exclude_id', $excludeSessionId);
        }
        
        $result = $this->db->single();
        return $result->count > 0;
    }
}
?>