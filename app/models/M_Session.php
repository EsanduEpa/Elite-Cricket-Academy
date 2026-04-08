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
    // Ensure a playerprofile row exists — SessionEnrollment.PlayerID FKs to playerprofile
    $this->db->query('INSERT IGNORE INTO playerprofile (PlayerID) VALUES (:pid)');
    $this->db->bind(':pid', (int)$playerId, PDO::PARAM_INT);
    $this->db->execute();

    $this->db->query('INSERT INTO SessionEnrollment (SessionID, PlayerID, Status)
                      VALUES (:session_id, :player_id, "enrolled")
                      ON DUPLICATE KEY UPDATE Status = "enrolled"');
    $this->db->bind(':session_id', (int)$sessionId, PDO::PARAM_INT);
    $this->db->bind(':player_id',  (int)$playerId,  PDO::PARAM_INT);
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
                sd.FacilityType,
                sd.FacilityNumber,
               (SELECT COUNT(*) FROM SessionEnrollment WHERE SessionID = s.SessionID AND Status != "cancelled") AS ParticipantCount
           FROM Session s
            LEFT JOIN SessionDetails sd ON s.SessionID = sd.SessionID
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
    $this->db->query('UPDATE Session SET Status = "cancelled" WHERE SessionID = :id');
    $this->db->bind(':id', $id);

    if (!$this->db->execute()) {
        return false;
    }

    // Store cancel reason in SessionDetails (table now exists)
    $this->db->query('INSERT INTO sessiondetails (SessionID, CancelReason)
        VALUES (:id, :reason)
        ON DUPLICATE KEY UPDATE CancelReason = :reason2');
    $this->db->bind(':id', $id);
    $this->db->bind(':reason', $reason);
    $this->db->bind(':reason2', $reason);
    $this->db->execute();

    return true;
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

    // Get all upcoming bookings for a player (coach + trainer appointments + facility bookings)
    public function getUpcomingBookingsForPlayer($playerId) {
        $this->db->query("
            SELECT 'coach' AS booking_type, ca.AppointmentID AS id, 
                ca.AppointmentDate AS date, ca.StartTime, ca.EndTime, 
                ca.Status, ca.Reason AS reason, u.Name AS practitioner_name
            FROM coachappointment ca 
            JOIN user u ON ca.CoachID = u.UserID 
            WHERE ca.PlayerID = :pid1 AND ca.AppointmentDate >= CURDATE() AND ca.Status != 'cancelled'
            UNION ALL
            SELECT 'trainer', ta.AppointmentID, 
                ta.AppointmentDate, ta.StartTime, ta.EndTime, 
                ta.Status, ta.Reason, u.Name
            FROM trainerappointment ta 
            JOIN user u ON ta.TrainerID = u.UserID 
            WHERE ta.PlayerID = :pid2 AND ta.AppointmentDate >= CURDATE() AND ta.Status != 'cancelled'
            UNION ALL
            SELECT 'facility', fb.FacilityBookingID, 
                fb.BookingDate, fb.StartTime, fb.EndTime, 
                fb.Status, f.Name, f.Name
            FROM facilitybooking fb 
            JOIN facility f ON fb.FacilityID = f.FacilityID 
            WHERE fb.PlayerID = :pid3 AND fb.BookingDate >= CURDATE() AND fb.Status != 'cancelled'
            UNION ALL
            SELECT 'session' AS booking_type, se.EnrollmentID AS id,
                s.Date AS date, s.StartTime, s.EndTime,
                se.Status, s.Name AS reason, u.Name AS practitioner_name
            FROM sessionenrollment se
            JOIN session s ON se.SessionID = s.SessionID
            LEFT JOIN user u ON s.CoachOrTrainerID = u.UserID
            WHERE se.PlayerID = :pid4 AND s.Date >= CURDATE()
              AND se.Status = 'enrolled' AND s.Status = 'active'
            ORDER BY date ASC, StartTime ASC
        ");
        $this->db->bind(':pid1', $playerId);
        $this->db->bind(':pid2', $playerId);
        $this->db->bind(':pid3', $playerId);
        $this->db->bind(':pid4', $playerId);
        return $this->db->resultSet();
    }

    // Get booking history for a player
    public function getBookingHistoryForPlayer($playerId) {
        $this->db->query("
            SELECT 'coach' AS booking_type, ca.AppointmentID AS id, 
                ca.AppointmentDate AS date, ca.StartTime, ca.EndTime, 
                ca.Status, ca.Reason AS reason, u.Name AS practitioner_name
            FROM coachappointment ca 
            JOIN user u ON ca.CoachID = u.UserID 
            WHERE ca.PlayerID = :pid1
            UNION ALL
            SELECT 'trainer', ta.AppointmentID, 
                ta.AppointmentDate, ta.StartTime, ta.EndTime, 
                ta.Status, ta.Reason, u.Name
            FROM trainerappointment ta 
            JOIN user u ON ta.TrainerID = u.UserID 
            WHERE ta.PlayerID = :pid2
            UNION ALL
            SELECT 'facility', fb.FacilityBookingID, 
                fb.BookingDate, fb.StartTime, fb.EndTime, 
                fb.Status, f.Name, f.Name
            FROM facilitybooking fb 
            JOIN facility f ON fb.FacilityID = f.FacilityID 
            WHERE fb.PlayerID = :pid3
            UNION ALL
            SELECT 'session' AS booking_type, se.EnrollmentID AS id,
                s.Date AS date, s.StartTime, s.EndTime,
                se.Status, s.Name AS reason, u.Name AS practitioner_name
            FROM sessionenrollment se
            JOIN session s ON se.SessionID = s.SessionID
            LEFT JOIN user u ON s.CoachOrTrainerID = u.UserID
            WHERE se.PlayerID = :pid4
            ORDER BY date DESC
        ");
        $this->db->bind(':pid1', $playerId);
        $this->db->bind(':pid2', $playerId);
        $this->db->bind(':pid3', $playerId);
        $this->db->bind(':pid4', $playerId);
        return $this->db->resultSet();
    }

    // Get facility bookings for a player
    public function getFacilityBookingsForPlayer($playerId) {
        $this->db->query('SELECT fb.*, f.Name AS facility_name, f.Location, f.HourlyRate
            FROM facilitybooking fb 
            JOIN facility f ON fb.FacilityID = f.FacilityID 
            WHERE fb.PlayerID = :player_id 
            ORDER BY fb.BookingDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    // Get available facilities and their bookings for a date
    public function getUnavailableTimes($facilityId, $date) {
        $this->db->query('SELECT StartTime, EndTime FROM facilitybooking
            WHERE FacilityID = :fid AND BookingDate = :date AND Status != "cancelled"
            ORDER BY StartTime ASC');
        $this->db->bind(':fid', $facilityId);
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    /**
     * Total hours a player has booked for a given facility on a given date (non-cancelled)
     */
    public function getPlayerDailyFacilityHours(int $playerId, int $facilityId, string $date): float {
        $this->db->query('SELECT SUM(TIMESTAMPDIFF(MINUTE, StartTime, EndTime)) as total_minutes
            FROM facilitybooking
            WHERE PlayerID = :pid AND FacilityID = :fid AND BookingDate = :date AND Status != "cancelled"');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':fid', $facilityId, PDO::PARAM_INT);
        $this->db->bind(':date', $date, PDO::PARAM_STR);
        $result = $this->db->single();
        return round((float)($result->total_minutes ?? 0) / 60, 2);
    }

    /**
     * Insert a confirmed facility booking
     */
    public function bookFacility(array $data): int|false {
        $this->db->query('INSERT INTO facilitybooking
            (FacilityID, PlayerID, BookingDate, StartTime, EndTime, Status, TotalCost, BookedBy)
            VALUES (:fid, :pid, :date, :start, :end, "confirmed", :cost, :booked_by)');
        $this->db->bind(':fid',       (int)$data['facility_id'], PDO::PARAM_INT);
        $this->db->bind(':pid',       (int)$data['player_id'],   PDO::PARAM_INT);
        $this->db->bind(':date',      $data['date'],             PDO::PARAM_STR);
        $this->db->bind(':start',     $data['start_time'],       PDO::PARAM_STR);
        $this->db->bind(':end',       $data['end_time'],         PDO::PARAM_STR);
        $this->db->bind(':cost',      $data['total_cost'],       PDO::PARAM_STR);
        $this->db->bind(':booked_by', (int)$data['player_id'],   PDO::PARAM_INT);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Check if the facility is already booked for an overlapping time slot on that date
     */
    public function facilityHasTimeConflict(int $facilityId, string $date, string $startTime, string $endTime): bool {
        $this->db->query('SELECT COUNT(*) as cnt FROM facilitybooking
            WHERE FacilityID = :fid AND BookingDate = :date AND Status != "cancelled"
              AND StartTime < :end AND EndTime > :start');
        $this->db->bind(':fid',   $facilityId, PDO::PARAM_INT);
        $this->db->bind(':date',  $date,        PDO::PARAM_STR);
        $this->db->bind(':start', $startTime,   PDO::PARAM_STR);
        $this->db->bind(':end',   $endTime,     PDO::PARAM_STR);
        $result = $this->db->single();
        return (int)($result->cnt ?? 0) > 0;
    }

    // Get today's schedule for a player (sessions + appointments)
    public function getTodayScheduleForPlayer($playerId) {
        $this->db->query('SELECT s.SessionID, s.Name as activity, s.Date, s.StartTime as start_time, 
            s.EndTime as end_time, s.Location as location, s.SessionType as type, u.Name as coach
            FROM Session s
            JOIN SessionEnrollment se ON s.SessionID = se.SessionID
            LEFT JOIN User u ON s.CoachOrTrainerID = u.UserID
            WHERE se.PlayerID = :pid AND s.Date = CURDATE() AND s.Status != "cancelled"
            ORDER BY s.StartTime ASC');
        $this->db->bind(':pid', $playerId);
        return $this->db->resultSet();
    }

    // Get available coach sessions for booking
    public function getAvailableCoachSessions($date = null) {
        $sql = 'SELECT s.SessionID as slot_id, s.CoachOrTrainerID as coach_id,
                u.Name as coach_name,
                cp.Specialization as coach_specialization,
                s.Date as date, s.StartTime as start_time,
                s.EndTime as end_time, s.SessionType as session_type, s.SessionMode as session_mode,
                COALESCE(NULLIF(s.MaxParticipants, 0), 20) as max_participants,
                (SELECT COUNT(*) FROM SessionEnrollment se2
                 WHERE se2.SessionID = s.SessionID AND se2.Status != "cancelled") as current_bookings,
                s.Location as location, s.Name as description,
                u.ProfileImage as coach_image, s.PricePerSession as price
            FROM `Session` s
            JOIN User u ON u.UserID = s.CoachOrTrainerID
            LEFT JOIN coachprofile cp ON cp.CoachID = s.CoachOrTrainerID
            WHERE s.Status = "active"
              AND s.Date >= CURDATE()
              AND s.CoachOrTrainerID IS NOT NULL
              AND (
                  s.SessionType = "Coaching"
                  OR LOWER(u.Role) = "coach"
                  OR EXISTS (SELECT 1 FROM coachprofile cp2 WHERE cp2.CoachID = s.CoachOrTrainerID)
              )';
        if ($date) {
            $sql .= ' AND s.Date = :date';
        }
        $sql .= ' HAVING current_bookings < max_participants ORDER BY s.Date ASC, s.StartTime ASC';
        $this->db->query($sql);
        if ($date) $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    // Get available trainer sessions for booking
    public function getAvailableTrainerSessions($date = null) {
        $sql = 'SELECT s.SessionID as slot_id,
                s.CoachOrTrainerID as trainer_id,
                u.Name as trainer_name,
                s.Date as date,
                s.StartTime as start_time,
                s.EndTime as end_time,
                s.SessionType as session_type,
                s.SessionMode as session_mode,
                COALESCE(NULLIF(s.MaxParticipants, 0), 20) as max_participants,
                (SELECT COUNT(*) FROM SessionEnrollment se2
                 WHERE se2.SessionID = s.SessionID AND se2.Status != "cancelled") as current_bookings,
                s.Location as location,
                s.Name as description,
                u.ProfileImage as trainer_image,
                s.PricePerSession as price
            FROM `Session` s
            JOIN User u ON u.UserID = s.CoachOrTrainerID
            WHERE s.Status = "active"
              AND s.Date >= CURDATE()
              AND s.CoachOrTrainerID IS NOT NULL
              AND (
                  s.SessionType = "Physical Training"
                  OR LOWER(u.Role) = "trainer"
                  OR EXISTS (SELECT 1 FROM trainerprofile tp WHERE tp.TrainerID = s.CoachOrTrainerID)
              )';
        if ($date) {
            $sql .= ' AND s.Date = :date';
        }
        $sql .= ' HAVING current_bookings < max_participants ORDER BY s.Date ASC, s.StartTime ASC';
        $this->db->query($sql);
        if ($date) $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    // Book a coach appointment
    public function bookCoachAppointment($data) {
        $this->db->query('INSERT INTO coachappointment (PlayerID, CoachID, AppointmentDate, StartTime, EndTime, Status, Reason)
            VALUES (:pid, :cid, :date, :start, :end, :status, :reason)');
        $this->db->bind(':pid', $data['player_id']);
        $this->db->bind(':cid', $data['coach_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':start', $data['start_time']);
        $this->db->bind(':end', $data['end_time']);
        $this->db->bind(':status', $data['status'] ?? 'scheduled');
        $this->db->bind(':reason', $data['notes'] ?? '');
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Cancel an appointment
    public function cancelAppointment($appointmentId, $playerId) {
        $this->db->query('UPDATE coachappointment SET Status = "Cancelled" 
            WHERE AppointmentID = :id AND PlayerID = :pid');
        $this->db->bind(':id', $appointmentId);
        $this->db->bind(':pid', $playerId);
        return $this->db->execute();
    }

    // Get session types available
    public function getSessionTypes() {
        $this->db->query('SELECT DISTINCT SessionType FROM Session WHERE Status = "active" ORDER BY SessionType');
        return $this->db->resultSet();
    }

    // ==================== TRAINER BOOKING SESSION ====================

    /**
     * Add a new trainer-created booking session.
     * Maps the "Add Session" form fields to the Session table.
     *
     * @param array $data {
     *   trainer_id  int,
     *   title       string  (Session Title),
     *   client_name string  (embedded in Name as "Title — Client: Name"),
     *   date        string  YYYY-MM-DD,
     *   start_time  string  HH:MM:SS,
     *   end_time    string  HH:MM:SS,
     *   location    string,
     *   description string  (stored as part of Name; no DB column),
     *   status      string  'active'|'completed'
     * }
     * @return int|false New SessionID or false on failure
     */

    /* create session is duplicated*/
    public function addTrainerBookingSession($data) {
        // Build a rich Name: "Title — Client: John Smith"
        $name = trim($data['title']);
        if (!empty($data['client_name'])) {
            $name .= ' — Client: ' . trim($data['client_name']);
        }

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
            :trainer_id,
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

        $this->db->bind(':session_type', 'Physical Training', PDO::PARAM_STR);
        $this->db->bind(':session_mode', 'Individual',        PDO::PARAM_STR);
        $this->db->bind(':trainer_id',   (int)$data['trainer_id'], PDO::PARAM_INT);
        $this->db->bind(':name',         $name,               PDO::PARAM_STR);
        $this->db->bind(':date',         $data['date'],       PDO::PARAM_STR);
        $this->db->bind(':start_time',   $data['start_time'], PDO::PARAM_STR);
        $this->db->bind(':end_time',     $data['end_time'],   PDO::PARAM_STR);
        $this->db->bind(':location',     $data['location'],   PDO::PARAM_STR);
        $this->db->bind(':status',       $data['status'],     PDO::PARAM_STR);
        $this->db->bind(':max_participants', 1,               PDO::PARAM_INT);
        $this->db->bind(':price',        '0.00',              PDO::PARAM_STR);
        $this->db->bind(':is_recurring', 0,                   PDO::PARAM_INT);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // ==================== CANCEL & RESCHEDULE ====================

    /**
     * Count how many cancellations a player has made this calendar month
     * across session enrollments + coach appointments + trainer appointments
     */
    public function getPlayerMonthlyCancellationCount(int $playerId): int {
        $month = date('Y-m');

        $this->db->query("SELECT COUNT(*) as cnt FROM sessionenrollment
            WHERE PlayerID = :pid AND Status = 'cancelled'
              AND DATE_FORMAT(UpdatedAt, '%Y-%m') = :month");
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':month', $month, PDO::PARAM_STR);
        $r1 = $this->db->single();

        $this->db->query("SELECT COUNT(*) as cnt FROM coachappointment
            WHERE PlayerID = :pid AND Status = 'cancelled'
              AND DATE_FORMAT(UpdatedAt, '%Y-%m') = :month");
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':month', $month, PDO::PARAM_STR);
        $r2 = $this->db->single();

        $this->db->query("SELECT COUNT(*) as cnt FROM trainerappointment
            WHERE PlayerID = :pid AND Status = 'cancelled'
              AND DATE_FORMAT(UpdatedAt, '%Y-%m') = :month");
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':month', $month, PDO::PARAM_STR);
        $r3 = $this->db->single();

        return (int)($r1->cnt ?? 0) + (int)($r2->cnt ?? 0) + (int)($r3->cnt ?? 0);
    }

    /**
     * Cancel a session enrollment — only if it belongs to the player
     */
    public function cancelSessionEnrollment(int $enrollmentId, int $playerId): bool {
        $this->db->query("UPDATE sessionenrollment SET Status = 'cancelled', UpdatedAt = NOW()
            WHERE EnrollmentID = :id AND PlayerID = :pid AND Status = 'enrolled'");
        $this->db->bind(':id', $enrollmentId, PDO::PARAM_INT);
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Cancel a coach appointment — only if it belongs to the player
     */
    public function cancelCoachAppointment(int $appointmentId, int $playerId): bool {
        $this->db->query("UPDATE coachappointment SET Status = 'cancelled', UpdatedAt = NOW()
            WHERE AppointmentID = :id AND PlayerID = :pid AND Status != 'cancelled'");
        $this->db->bind(':id', $appointmentId, PDO::PARAM_INT);
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Cancel a trainer appointment — only if it belongs to the player
     */
    public function cancelTrainerAppointment(int $appointmentId, int $playerId): bool {
        $this->db->query("UPDATE trainerappointment SET Status = 'cancelled', UpdatedAt = NOW()
            WHERE AppointmentID = :id AND PlayerID = :pid AND Status != 'cancelled'");
        $this->db->bind(':id', $appointmentId, PDO::PARAM_INT);
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Get session/appointment date+time for the 24-hour rule check.
     * Returns an object with ->date and ->start_time, or false.
     */
    public function getBookingDateTime(int $id, string $type): mixed {
        if ($type === 'session') {
            $this->db->query('SELECT s.Date as date, s.StartTime as start_time
                FROM sessionenrollment se JOIN session s ON se.SessionID = s.SessionID
                WHERE se.EnrollmentID = :id');
        } elseif ($type === 'coach') {
            $this->db->query('SELECT AppointmentDate as date, StartTime as start_time
                FROM coachappointment WHERE AppointmentID = :id');
        } elseif ($type === 'trainer') {
            $this->db->query('SELECT AppointmentDate as date, StartTime as start_time
                FROM trainerappointment WHERE AppointmentID = :id');
        } else {
            return false;
        }
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /** Get enrollment with its session data (for notification on cancel) */
    public function getEnrollmentWithSession(int $enrollmentId): mixed {
        $this->db->query('SELECT se.*, s.Date, s.StartTime, s.CoachOrTrainerID
            FROM sessionenrollment se JOIN session s ON se.SessionID = s.SessionID
            WHERE se.EnrollmentID = :id');
        $this->db->bind(':id', $enrollmentId, PDO::PARAM_INT);
        return $this->db->single();
    }

    /** Get a coach appointment by ID */
    public function getCoachAppointmentById(int $id): mixed {
        $this->db->query('SELECT * FROM coachappointment WHERE AppointmentID = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /** Get a trainer appointment by ID */
    public function getTrainerAppointmentById(int $id): mixed {
        $this->db->query('SELECT * FROM trainerappointment WHERE AppointmentID = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    // ==================== BOOKING RULES ====================

    /**
     * Count how many coach OR trainer bookings a player has on a given date.
     * Covers both group session enrollments and 1-on-1 appointments.
     * @param string $type  'coach' | 'trainer'
     */
    public function playerDailyBookingCount(int $playerId, string $date, string $type): int {
        if ($type === 'coach') {
            $this->db->query('SELECT COUNT(*) as cnt
                FROM sessionenrollment se
                JOIN session s ON se.SessionID = s.SessionID
                WHERE se.PlayerID = :pid AND s.Date = :date
                  AND s.SessionType = "Coaching"
                  AND se.Status = "enrolled" AND s.Status = "active"');
            $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
            $this->db->bind(':date', $date, PDO::PARAM_STR);
            $r1 = $this->db->single();

            $this->db->query('SELECT COUNT(*) as cnt FROM coachappointment
                WHERE PlayerID = :pid AND AppointmentDate = :date AND Status != "cancelled"');
            $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
            $this->db->bind(':date', $date, PDO::PARAM_STR);
            $r2 = $this->db->single();

            return (int)($r1->cnt ?? 0) + (int)($r2->cnt ?? 0);
        } else {
            $this->db->query('SELECT COUNT(*) as cnt
                FROM sessionenrollment se
                JOIN session s ON se.SessionID = s.SessionID
                WHERE se.PlayerID = :pid AND s.Date = :date
                  AND s.SessionType = "Physical Training"
                  AND se.Status = "enrolled" AND s.Status = "active"');
            $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
            $this->db->bind(':date', $date, PDO::PARAM_STR);
            $r1 = $this->db->single();

            $this->db->query('SELECT COUNT(*) as cnt FROM trainerappointment
                WHERE PlayerID = :pid AND AppointmentDate = :date AND Status != "cancelled"');
            $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
            $this->db->bind(':date', $date, PDO::PARAM_STR);
            $r2 = $this->db->single();

            return (int)($r1->cnt ?? 0) + (int)($r2->cnt ?? 0);
        }
    }

    /**
     * Check if a player has any booking that overlaps a given date + time window.
     * Checks group session enrollments, coach appointments, and trainer appointments.
     */
    public function playerHasTimeOverlap(int $playerId, string $date, string $startTime, string $endTime): bool {
        $this->db->query('SELECT COUNT(*) as cnt
            FROM sessionenrollment se
            JOIN session s ON se.SessionID = s.SessionID
            WHERE se.PlayerID = :pid AND s.Date = :date
              AND se.Status = "enrolled" AND s.Status = "active"
              AND s.StartTime < :end AND s.EndTime > :start');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':date', $date, PDO::PARAM_STR);
        $this->db->bind(':start', $startTime, PDO::PARAM_STR);
        $this->db->bind(':end', $endTime, PDO::PARAM_STR);
        $r = $this->db->single();
        if ((int)($r->cnt ?? 0) > 0) return true;

        $this->db->query('SELECT COUNT(*) as cnt FROM coachappointment
            WHERE PlayerID = :pid AND AppointmentDate = :date AND Status != "cancelled"
              AND StartTime < :end AND EndTime > :start');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':date', $date, PDO::PARAM_STR);
        $this->db->bind(':start', $startTime, PDO::PARAM_STR);
        $this->db->bind(':end', $endTime, PDO::PARAM_STR);
        $r = $this->db->single();
        if ((int)($r->cnt ?? 0) > 0) return true;

        $this->db->query('SELECT COUNT(*) as cnt FROM trainerappointment
            WHERE PlayerID = :pid AND AppointmentDate = :date AND Status != "cancelled"
              AND StartTime < :end AND EndTime > :start');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $this->db->bind(':date', $date, PDO::PARAM_STR);
        $this->db->bind(':start', $startTime, PDO::PARAM_STR);
        $this->db->bind(':end', $endTime, PDO::PARAM_STR);
        $r = $this->db->single();
        return (int)($r->cnt ?? 0) > 0;
    }

    /**
     * Get today's facility bookings with facility and player details
     */
    public function getTodaysFacilityBookings() {
        $today = date('Y-m-d');
        $this->db->query('SELECT fb.*, f.Name as facility_name, f.Location, f.HourlyRate,
            u.Name as player_name, u.email as player_email
            FROM facilitybooking fb
            JOIN facility f ON fb.FacilityID = f.FacilityID
            LEFT JOIN user u ON fb.PlayerID = u.UserID
            WHERE DATE(fb.BookingDate) = :date
            ORDER BY fb.StartTime ASC');
        $this->db->bind(':date', $today, PDO::PARAM_STR);
        return $this->db->resultSet();
    }
}
?>