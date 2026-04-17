<?php

/**
 * USER MODEL (M_Users)
 * 
 * Purpose: Handle all database operations related to users
 * Responsibilities:
 *   1. User registration (INSERT new users)
 *   2. User authentication (SELECT and verify credentials)
 *   3. User profile management (UPDATE user data)
 *   4. User queries (SELECT user information)
 *   5. Activity logging and security features
 * 
 * This is the DATA LAYER in MVC architecture
 * - No business logic here, only database queries
 * - Controllers call these methods to interact with database
 * - Uses PDO (PHP Data Objects) for secure database access
 */
class M_Users {
    // Database connection object
    private $db;
    private $lastErrorMessage = '';

    private function buildFullName(?string $firstName, ?string $lastName): string {
        return trim(implode(' ', array_filter([
            trim((string)$firstName),
            trim((string)$lastName)
        ], static fn($part) => $part !== '')));
    }

    private function splitFullName(?string $fullName): array {
        $fullName = trim((string)$fullName);

        if ($fullName === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $fullName, 2);
        return [$parts[0] ?? '', $parts[1] ?? ''];
    }

    private function extractNameParts(array $data): array {
        $firstName = trim((string)($data['firstName'] ?? $data['first_name'] ?? ''));
        $lastName = trim((string)($data['lastName'] ?? $data['last_name'] ?? ''));

        if ($firstName === '' && $lastName === '') {
            [$firstName, $lastName] = $this->splitFullName($data['fullName'] ?? $data['name'] ?? '');
        }

        return [$firstName, $lastName, $this->buildFullName($firstName, $lastName)];
    }

    /**
     * CONSTRUCTOR - Initialize database connection
     * Runs automatically when model is instantiated
     */
    public function __construct() {
        // Create new Database instance (handles PDO connection)
        $this->db = new Database();
    }

    /**
     * REGISTER METHOD - Create new user account in database
     * 
     * Purpose: Insert a new user record into the User table
     * 
     * @param array $data - User registration data from controller:
     *   - fullName: User's full name
     *   - dateOfBirth: Birth date (YYYY-MM-DD format)
     *   - address: Physical address
     *   - school: School/institution name
     *   - email: Unique email address
     *   - contactNumber: Phone number
     *   - username: Unique username
     *   - password: ALREADY HASHED password (controller handles hashing)
     * 
     * @return int|false - Returns new UserID if successful, false if failed
     * 
     * Database Interaction:
     *   - Inserts into 'User' table
     *   - Triggers auto-creation of role-specific profile (PlayerProfile)
     *   - Triggers welcome notification and email
     */
    public function register($data) {
        $this->lastErrorMessage = '';
        [$firstName, $lastName] = $this->extractNameParts($data);

        // STEP 1: PREPARE SQL INSERT QUERY
        // Uses placeholders (:name) to prevent SQL injection
        // INSERT INTO table (columns) VALUES (placeholders)
        $this->db->query('INSERT INTO User (
            FirstName,
            LastName,
            DateOfBirth,
            Address,
            School,
            Email,
            PhoneNumber,
            Role,
            Username,
            PasswordHash,
            DateJoined
        ) VALUES (
            :first_name,
            :last_name,
            :date_of_birth, 
            :address, 
            :school, 
            :email, 
            :phone_number, 
            :role, 
            :username, 
            :password_hash, 
            NOW()
        )');
        
        // STEP 2: BIND VALUES TO PLACEHOLDERS
        // This prevents SQL injection by separating data from SQL structure
        // PDO handles proper escaping and type conversion
        $this->db->bind(':first_name', $firstName);
        $this->db->bind(':last_name', $lastName !== '' ? $lastName : null);
        $this->db->bind(':date_of_birth', $data['dateOfBirth']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':school', $data['school']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone_number', $data['contactNumber']);
        $this->db->bind(':role', 'Player'); // Default role for registration
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password_hash', $data['password']);

        // Execute
        try {
            if($this->db->execute()) {
                // Return the new user ID
                return $this->db->lastInsertId();
            } else {
                $errorInfo = $this->db->getError();
                $this->lastErrorMessage = $errorInfo[2] ?? 'Unknown registration database error';
                error_log("Database execution failed during user registration: " . $this->lastErrorMessage);
                return false;
            }
        } catch (Exception $e) {
            $this->lastErrorMessage = $e->getMessage();
            error_log("Database error during registration: " . $e->getMessage());
            return false;
        }
    }

    public function getLastErrorMessage() {
        return $this->lastErrorMessage;
    }

    /**
     * LOGIN METHOD - Authenticate user credentials
     * 
     * Purpose: Verify user's email/username and password against database
     * 
     * @param string $email - Email address OR username (supports both)
     * @param string $password - Plain-text password (NOT hashed yet)
     * 
     * @return object|false - Returns user object if valid, false if invalid
     * 
     * Security Features:
     *   - Uses password_verify() to check hashed passwords
     *   - Never compares plain-text passwords
     *   - Supports both email and username login
     * 
     * How Password Verification Works:
     *   1. Query database for user by email OR username
     *   2. Get the stored password hash from database
     *   3. Use password_verify() to compare plain password with hash
     *   4. Return user data if match, false if no match
     */
    public function login($email, $password) {
        // STEP 1: PREPARE SELECT QUERY
        // Search for user by email OR username (flexible login)
        // SELECT * gets all user columns (UserID, Name, Email, Role, etc.)
        $this->db->query("SELECT User.*, TRIM(CONCAT_WS(CHAR(32), FirstName, LastName)) AS Name FROM User WHERE Email = :email OR Username = :email");
        
        // STEP 2: BIND THE EMAIL/USERNAME
        // Same placeholder used twice (email OR username)
        // User can login with either their email or username
        $this->db->bind(':email', $email);

        // STEP 3: EXECUTE QUERY AND GET RESULT
        // single() returns one row as an object, or false if no match
        $row = $this->db->single();

        // STEP 4: VERIFY PASSWORD IF USER EXISTS
        if($row) {
            // User found in database - now verify password
            
            // Get the hashed password from database
            $hashed_password = $row->PasswordHash;
            
            // STEP 5: VERIFY PASSWORD
            // password_verify() compares plain password with hashed password
            // This is SECURE - it uses the same algorithm that created the hash
            // Returns true if passwords match, false if they don't
            if(password_verify($password, $hashed_password)) {
                // PASSWORD MATCHES - Return complete user object
                // Contains: UserID, Name, Email, Role, etc.
                return $row;
            }
        }

        // USER NOT FOUND OR PASSWORD WRONG
        // Return false (controller will show "Invalid credentials" message)
        return false;
    }

    /**
     * FIND USER BY EMAIL - Check if email exists in database
     * 
     * Purpose: Validate email uniqueness during registration
     * 
     * @param string $email - Email address to search for
     * @return bool - Returns true if email exists, false if available
     * 
     * Used during registration to prevent duplicate email addresses
     * Controller checks this before allowing registration
     */
    public function findUserByEmail($email) {
        // STEP 1: QUERY DATABASE FOR EMAIL
        $this->db->query('SELECT * FROM User WHERE Email = :email');
        $this->db->bind(':email', $email);

        // STEP 2: EXECUTE QUERY
        $row = $this->db->single();

        // STEP 3: CHECK IF EMAIL WAS FOUND
        // rowCount() returns number of rows found (1 if exists, 0 if not)
        if($this->db->rowCount() > 0) {
            return true;  // Email already exists in database
        } else {
            return false; // Email is available
        }
    }

    public function getUserByEmail($email) {
        $this->db->query("SELECT User.*, TRIM(CONCAT_WS(CHAR(32), FirstName, LastName)) AS Name FROM User WHERE Email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * FIND USER BY USERNAME - Check if username exists in database
     * 
     * Purpose: Validate username uniqueness during registration
     * 
     * @param string $username - Username to search for
     * @return bool - Returns true if username exists, false if available
     * 
     * Used during registration to prevent duplicate usernames
     * Works exactly like findUserByEmail() but for usernames
     */
    public function findUserByUsername($username) {
        // STEP 1: QUERY DATABASE FOR USERNAME
        $this->db->query('SELECT * FROM User WHERE Username = :username');
        $this->db->bind(':username', $username);

        // STEP 2: EXECUTE QUERY
        $row = $this->db->single();

        // STEP 3: CHECK IF USERNAME WAS FOUND
        // rowCount() returns number of matching rows
        if($this->db->rowCount() > 0) {
            return true;  // Username already taken
        } else {
            return false; // Username is available
        }
    }

    /**
     * GET USER BY ID - Retrieve user information by UserID
     * 
     * Purpose: Fetch complete user record from database
     * 
     * @param int $id - UserID to search for
     * @return object|null - User object if found, null if not found
     * 
     * Used to get user details for profile pages, admin views, etc.
     * Returns all user columns as an object
     */
    public function getUserById($id) {
        // STEP 1: QUERY FOR USER BY ID
        $this->db->query("SELECT User.*, TRIM(CONCAT_WS(CHAR(32), FirstName, LastName)) AS Name FROM User WHERE UserID = :id");
        $this->db->bind(':id', $id);

        // STEP 2: RETURN USER OBJECT
        // single() returns one row as object, or null if not found
        return $this->db->single();
    }

    // Get total users count
    /**
     * GET TOTAL USERS - Count all registered users
     * 
     * Purpose: Get total count of users in the system
     * @return int - Total number of users in database
     */
    public function getTotalUsers() {
        // QUERY: COUNT all rows in User table
        $this->db->query('SELECT COUNT(*) as count FROM User');
        $result = $this->db->single();
        
        // RETURN: Total user count (returns 0 if table is empty)
        return $result ? (int)$result->count : 0;
    }

    /**
     * GET TOTAL USERS BY TYPE - Count users by their role
     * 
     * Purpose: Get count of users for specific role (Coach, Player, Trainer, ShopEmployee)
     * @param string $type - Role name (case-sensitive: 'Coach', 'Player', 'Trainer', 'ShopEmployee')
     * @return int - Count of users with specified role
     * 
     * Example: getTotalUsersByType('Coach') returns number of coaches
     */
    public function getTotalUsersByType($type) {
        // QUERY: COUNT users WHERE Role matches the specified type
        $this->db->query('SELECT COUNT(*) as count FROM User WHERE Role = :type');
        $this->db->bind(':type', $type);
        $result = $this->db->single();
        
        // RETURN: Count for this role (returns 0 if none found)
        return $result ? (int)$result->count : 0;
    }

    /**
     * GET TODAY'S REGISTRATIONS - Count new users registered today
     * 
     * Purpose: Track daily registration activity for monitoring growth
     * @return int - Number of users who registered today
     * 
     * Uses CURDATE() to compare with registration date
     */
    public function getTodayRegistrations() {
        // QUERY: COUNT users WHERE registration date = today's date
        // DATE(DateJoined) extracts just the date part (ignoring time)
        // CURDATE() returns current date in 'YYYY-MM-DD' format
        $this->db->query('SELECT COUNT(*) as count FROM User WHERE DATE(DateJoined) = CURDATE()');
        $result = $this->db->single();
        
        // RETURN: Today's registration count (returns 0 if no registrations today)
        return $result ? (int)$result->count : 0;
    }

    /**
     * GET RECENT ACTIVITIES - Fetch latest user activities from log
     * 
     * Purpose: Display recent system activities on admin dashboard
     * @param int $limit - Maximum number of activities to return (default: 10)
     * @return array - Array of activity objects with user information
     * 
     * Returns activities like: account_created, login, profile_updated, etc.
     * Useful for monitoring system usage and user behavior
     */
    public function getRecentActivities($limit = 10) {
        // QUERY: Get activities with user names via LEFT JOIN
        // activitylog.UserID -> User.UserID to get user name
        // ORDER BY Timestamp DESC gets newest activities first
        $this->db->query('SELECT 
            al.ActivityID as id,
            al.Action as action,
            al.Description as details,
            al.Timestamp as timestamp,
            al.Action as type,
            TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as user_name 
        FROM activitylog al 
        LEFT JOIN User u ON al.UserID = u.UserID 
        ORDER BY al.Timestamp DESC 
        LIMIT :limit');
        
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        
        // EXECUTE QUERY
        $results = $this->db->resultSet();
        
        // FORMAT TIMESTAMPS
        // Convert database timestamps to relative time (e.g., "2 hours ago")
        foreach ($results as &$activity) {
            if (isset($activity->timestamp)) {
                $activity->timestamp = $this->timeAgo($activity->timestamp);
            }
        }
        
        // RETURN: Array of activities (empty array if no activities found)
        return $results;
    }
    
    /**
     * TIME AGO - Convert timestamp to relative time string
     * 
     * Purpose: Display user-friendly relative time ("2 hours ago" instead of timestamp)
     * @param string $datetime - Database timestamp
     * @return string - Relative time string
     */
    private function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;
        
        if ($difference < 60) {
            return 'Just now';
        } elseif ($difference < 3600) {
            $minutes = floor($difference / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($difference < 86400) {
            $hours = floor($difference / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($difference < 604800) {
            $days = floor($difference / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $timestamp);
        }
    }

    // Get all users with pagination
    public function getAllUsers($offset = 0, $limit = 20) {
        $this->db->query("SELECT User.*, TRIM(CONCAT_WS(CHAR(32), FirstName, LastName)) AS Name FROM User ORDER BY DateJoined DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }

    // Update user profile
    public function updateUser($data) {
        [$firstName, $lastName] = $this->extractNameParts($data);
        $this->db->query('UPDATE User SET 
                         FirstName = :first_name,
                         LastName = :last_name,
                         Email = :email,
                         PhoneNumber = :phone_number,
                         Address = :address,
                         School = :school,
                         Role = :role,
                         Status = :status
                         WHERE UserID = :user_id');

        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':first_name', $firstName);
        $this->db->bind(':last_name', $lastName !== '' ? $lastName : null);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone_number', $data['phone_number']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':school', $data['school']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Suspend user (using AccountLockedUntil field from schema)
    public function suspendUser($id, $duration) {
        $lock_until = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
        
        $this->db->query('UPDATE User SET 
                         Status = "inactive",
                         AccountLockedUntil = :locked_until
                         WHERE UserID = :id');

        $this->db->bind(':id', $id);
        $this->db->bind(':locked_until', $lock_until);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Activate user
    public function activateUser($id) {
        $this->db->query('UPDATE User SET 
                         Status = "active",
                         AccountLockedUntil = NULL,
                         LoginAttempts = 0
                         WHERE UserID = :id');

        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update last login timestamp
    public function updateLastLogin($userId) {
        $this->db->query('UPDATE User SET 
                         LastLoginAt = NOW(),
                         LoginAttempts = 0
                         WHERE UserID = :user_id');
        
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    // Increment login attempts
    public function incrementLoginAttempts($email) {
        $this->db->query('UPDATE User SET 
                         LoginAttempts = LoginAttempts + 1
                         WHERE Email = :email OR Username = :email');
        
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    // Check if account is locked
    public function isAccountLocked($email) {
        $this->db->query('SELECT AccountLockedUntil, LoginAttempts FROM User 
                         WHERE (Email = :email OR Username = :email) 
                         AND Status = "active"');
        
        $this->db->bind(':email', $email);
        $result = $this->db->single();
        
        if ($result) {
            // Check if account is temporarily locked
            if ($result->AccountLockedUntil && strtotime($result->AccountLockedUntil) > time()) {
                return true;
            }
            
            // Check if too many login attempts
            if ($result->LoginAttempts >= 5) {
                // Lock account for 30 minutes after 5 failed attempts
                $this->lockAccount($email, 30);
                return true;
            }
        }
        
        return false;
    }

    // Lock account temporarily
    public function lockAccount($email, $minutes = 30) {
        $lock_until = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        
        $this->db->query('UPDATE User SET 
                         AccountLockedUntil = :locked_until
                         WHERE Email = :email OR Username = :email');
        
        $this->db->bind(':email', $email);
        $this->db->bind(':locked_until', $lock_until);
        
        return $this->db->execute();
    }

    // Create activity log entry
    public function logActivity($userId, $action, $description, $ipAddress = null, $userAgent = null) {
        $this->db->query('INSERT INTO activitylog (UserID, Action, Description, IPAddress, UserAgent) 
                         VALUES (:user_id, :action, :description, :ip_address, :user_agent)');
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':action', $action);
        $this->db->bind(':description', $description);
        $this->db->bind(':ip_address', $ipAddress);
        $this->db->bind(':user_agent', $userAgent);
        
        return $this->db->execute();
    }

    // Get all active membership plans
    public function getActiveMembershipPlans() {
        $this->db->query('SELECT * FROM membershipplan WHERE Status = :status ORDER BY MonthlyFee ASC');
        $this->db->bind(':status', 'active');
        return $this->db->resultSet();
    }

    // Get a single membership plan by ID
    public function getMembershipPlanById($planId) {
        $this->db->query('SELECT * FROM membershipplan WHERE PlanID = :plan_id AND Status = :status');
        $this->db->bind(':plan_id', (int)$planId, PDO::PARAM_INT);
        $this->db->bind(':status', 'active');
        return $this->db->single();
    }

    public function planUsesRecurringBilling($plan): bool {
        if (!$plan) {
            return false;
        }

        $planName = strtolower(trim((string)($plan->PlanName ?? '')));
        if ($planName === 'facility_only') {
            return false;
        }

        return (float)($plan->MonthlyFee ?? 0) > 0;
    }

    // Create a player subscription on registration
    public function createPlayerSubscription($playerId, $planId, $monthlyFee) {
        $this->db->query('INSERT INTO playersubscription 
            (PlayerID, PlanID, StartDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
            VALUES (:player_id, :plan_id, CURDATE(), :status, :monthly_fee, :payment_day, :auto_renewal)');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':plan_id', $planId);
        $this->db->bind(':status', 'active');
        $this->db->bind(':monthly_fee', $monthlyFee);
        $this->db->bind(':payment_day', 1);
        $this->db->bind(':auto_renewal', 1);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }

        return false;
    }

    public function createPendingSubscriptionPayment(int $subscriptionId, $amount, ?string $notes = null): bool {
        $this->db->query('INSERT INTO subscriptionpayment
            (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, DueDate, Notes)
            VALUES (:subscription_id, NULL, :amount, :payment_method, :status, CURDATE(), :notes)');
        $this->db->bind(':subscription_id', $subscriptionId, PDO::PARAM_INT);
        $this->db->bind(':amount', number_format((float)$amount, 2, '.', ''), PDO::PARAM_STR);
        $this->db->bind(':payment_method', 'online', PDO::PARAM_STR);
        $this->db->bind(':status', 'pending', PDO::PARAM_STR);
        $this->db->bind(':notes', $notes, PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function createCompletedSubscriptionPayment(
        int $subscriptionId,
        $amount,
        string $orderId,
        ?string $notes = null
    ): bool {
        $this->db->query('INSERT INTO subscriptionpayment
            (SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, PaymentReference,
             Gateway, GatewayOrderId, DueDate, Notes, PaidAt)
            VALUES (:subscription_id, CURDATE(), :amount, :payment_method, :status, :payment_reference,
             :gateway, :gateway_order_id, CURDATE(), :notes, NOW())');
        $this->db->bind(':subscription_id', $subscriptionId, PDO::PARAM_INT);
        $this->db->bind(':amount', number_format((float)$amount, 2, '.', ''), PDO::PARAM_STR);
        $this->db->bind(':payment_method', 'online', PDO::PARAM_STR);
        $this->db->bind(':status', 'completed', PDO::PARAM_STR);
        $this->db->bind(':payment_reference', $orderId);
        $this->db->bind(':gateway', 'payhere', PDO::PARAM_STR);
        $this->db->bind(':gateway_order_id', $orderId);
        $this->db->bind(':notes', $notes, PDO::PARAM_STR);

        return $this->db->execute();
    }

    public function getActiveSubscriptionForPlayer(int $playerId): ?object {
        $this->db->query(
            'SELECT SubscriptionID, PlanID, Status, StartDate, EndDate
             FROM playersubscription
             WHERE PlayerID = :player_id
               AND Status = :status
             ORDER BY StartDate DESC, SubscriptionID DESC
             LIMIT 1'
        );
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        $this->db->bind(':status', 'active');

        $row = $this->db->single();
        return $row ?: null;
    }

    public function planRequiresCoachAllocation(int $planId): bool {
        $plan = $this->getMembershipPlanById($planId);
        if (!$plan) {
            return false;
        }

        $planName = strtolower(trim((string)($plan->PlanName ?? '')));
        if (in_array($planName, ['general', 'private', 'pro'], true)) {
            return true;
        }

        return (int)($plan->SessionsPerWeek ?? 0) > 0 || (int)($plan->PrivateSessionsIncluded ?? 0) > 0;
    }

    public function getAgeGroupForDateOfBirth(string $dob): string {
        if (empty($dob)) {
            return 'Open';
        }

        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;


        if ($age < 11) return 'Under 11';
        if ($age < 13) return 'Under 13';
        if ($age < 15) return 'Under 15';
        if ($age < 17) return 'Under 17';
        if ($age < 19) return 'Under 19';
        if ($age < 21) return 'Under 21';
        return 'Open';
    }

    public function getBestCoachForSkill(string $ageGroup, string $coachingType): ?object {
        $this->db->query(
            'SELECT csg.CoachID,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
                    cp.Specialization,
                    cp.Experience,
                    csg.CoachingType,
                    csg.AgeGroup,
                    csg.PriorityRank,
                    COUNT(psca.PlayerID) AS CurrentLoad
             FROM coach_skill_age_group_assignment csg
             JOIN coachprofile cp ON cp.CoachID = csg.CoachID
             JOIN user u ON u.UserID = csg.CoachID
             LEFT JOIN player_skill_coach_assignment psca
                    ON psca.CoachID = csg.CoachID
                   AND psca.CoachingType = csg.CoachingType
             WHERE csg.IsActive = 1
               AND u.Status = :status
               AND csg.CoachingType = :ctype
               AND csg.AgeGroup IN (:age_exact, :age_open)
               GROUP BY csg.CoachID, u.FirstName, u.LastName, cp.Specialization, cp.Experience,
                      csg.CoachingType, csg.AgeGroup, csg.PriorityRank
             ORDER BY CASE WHEN csg.AgeGroup = :age_rank THEN 0 ELSE 1 END,
                      CurrentLoad ASC,
                      csg.PriorityRank ASC,
                      cp.Experience DESC,
                      u.FirstName ASC,
                      u.LastName ASC
             LIMIT 1'
        );
        $this->db->bind(':status', 'active');
        $this->db->bind(':ctype', $coachingType);
        $this->db->bind(':age_exact', $ageGroup);
        $this->db->bind(':age_open', 'Open');
        $this->db->bind(':age_rank', $ageGroup);

        $row = $this->db->single();
        return $row ?: null;
    }

    public function replacePlayerSkillCoachAssignments(int $playerId, string $ageGroup, ?int $assignedBy = null): bool {
        $this->db->query('DELETE FROM player_skill_coach_assignment WHERE PlayerID = :pid');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        if (!$this->db->execute()) {
            return false;
        }

        foreach (['batting', 'bowling', 'fielding'] as $coachingType) {
            $coach = $this->getBestCoachForSkill($ageGroup, $coachingType);
            if (!$coach) {
                continue;
            }

            $this->db->query(
                'INSERT INTO player_skill_coach_assignment
                 (PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, Notes)
                 VALUES (:pid, :ctype, :coach, :age, :source, :assigned_by, :notes)'
            );
            $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
            $this->db->bind(':ctype', $coachingType);
            $this->db->bind(':coach', (int)$coach->CoachID, PDO::PARAM_INT);
            $this->db->bind(':age', $ageGroup);
            $this->db->bind(':source', 'auto_registration');
            $this->db->bind(':assigned_by', $assignedBy, $assignedBy === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $this->db->bind(':notes', 'Assigned automatically during registration');

            if (!$this->db->execute()) {
                return false;
            }
        }

        return true;
    }

    public function refreshPlayerProgramTemplateAssignments(int $playerId, ?int $assignedBy = null): bool {
        $this->db->query(
            "DELETE FROM slot_template_player_assignment
             WHERE PlayerID = :pid
               AND AssignmentSource IN ('auto_plan', 'system_refresh')"
        );
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        if (!$this->db->execute()) {
            return false;
        }

        $this->db->query(
            "INSERT INTO slot_template_player_assignment
             (TemplateID, PlayerID, CoachingType, AgeGroup, AssignmentSource, AssignedBy, Notes)
             SELECT st.TemplateID,
                    psca.PlayerID,
                    psca.CoachingType,
                    psca.AgeGroup,
                    'auto_plan',
                    :assigned_by,
                    'Auto-assigned from player skill coach assignment and age group'
             FROM player_skill_coach_assignment psca
             JOIN slot_template st
               ON st.SlotType = 'program'
              AND st.IsActive = 1
              AND LOWER(COALESCE(st.Category, '')) = psca.CoachingType
              AND COALESCE(st.AgeGroup, 'Open') IN (psca.AgeGroup, 'Open')
             WHERE psca.PlayerID = :pid"
        );
        $this->db->bind(':assigned_by', $assignedBy, $assignedBy === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function autoAssignSkillCoachesAndPrograms(int $playerId, int $planId): bool {
        if (!$this->planRequiresCoachAllocation($planId)) {
            return true;
        }

        $this->db->query('SELECT DateOfBirth FROM user WHERE UserID = :pid LIMIT 1');
        $this->db->bind(':pid', $playerId, PDO::PARAM_INT);
        $player = $this->db->single();

        if (!$player || empty($player->DateOfBirth)) {
            return false;
        }

        $ageGroup = $this->getAgeGroupForDateOfBirth($player->DateOfBirth);

        $startedTransaction = false;
        if (method_exists($this->db, 'inTransaction') && method_exists($this->db, 'beginTransaction') && !$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        try {
            if (!$this->replacePlayerSkillCoachAssignments($playerId, $ageGroup)) {
                if ($startedTransaction) {
                    $this->db->rollBack();
                }
                return false;
            }

            if (!$this->refreshPlayerProgramTemplateAssignments($playerId)) {
                if ($startedTransaction) {
                    $this->db->rollBack();
                }
                return false;
            }

            if ($startedTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e) {
            if ($startedTransaction) {
                $this->db->rollBack();
            }
            error_log('autoAssignSkillCoachesAndPrograms failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getPlayerSkillCoachAssignments(int $playerId): array {
        $this->db->query(
            'SELECT psca.CoachingType,
                    psca.AgeGroup,
                    psca.CoachID,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS CoachName
             FROM player_skill_coach_assignment psca
             JOIN user u ON u.UserID = psca.CoachID
             WHERE psca.PlayerID = :player_id
             ORDER BY FIELD(psca.CoachingType, "batting", "bowling", "fielding"), u.FirstName ASC, u.LastName ASC'
        );
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function refreshPlayerAssignmentsOnLogin(int $playerId): array {
        $player = $this->getUserById($playerId);
        if (!$player || ($player->Role ?? '') !== 'Player') {
            return [
                'updated' => false,
                'ageGroupChanged' => false,
                'assignmentsChanged' => false,
                'ageGroup' => null,
                'assignments' => []
            ];
        }

        $subscription = $this->getActiveSubscriptionForPlayer($playerId);
        if (!$subscription) {
            return [
                'updated' => false,
                'ageGroupChanged' => false,
                'assignmentsChanged' => false,
                'ageGroup' => null,
                'assignments' => []
            ];
        }

        $beforeAssignments = $this->getPlayerSkillCoachAssignments($playerId);
        $beforeAgeGroup = $beforeAssignments[0]->AgeGroup ?? null;

        if (!$this->autoAssignSkillCoachesAndPrograms($playerId, (int)$subscription->PlanID)) {
            return [
                'updated' => false,
                'ageGroupChanged' => false,
                'assignmentsChanged' => false,
                'ageGroup' => $beforeAgeGroup,
                'assignments' => $beforeAssignments
            ];
        }

        $afterAssignments = $this->getPlayerSkillCoachAssignments($playerId);
        $afterAgeGroup = $afterAssignments[0]->AgeGroup ?? $this->getAgeGroupForDateOfBirth((string)($player->DateOfBirth ?? ''));

        $toSignature = static function (array $assignments): array {
            $signature = [];

            foreach ($assignments as $assignment) {
                $signature[] = [
                    'type' => (string)($assignment->CoachingType ?? ''),
                    'coachId' => (int)($assignment->CoachID ?? 0),
                    'coachName' => (string)($assignment->CoachName ?? ''),
                    'ageGroup' => (string)($assignment->AgeGroup ?? '')
                ];
            }

            return $signature;
        };

        $beforeSignature = $toSignature($beforeAssignments);
        $afterSignature = $toSignature($afterAssignments);

        $ageGroupChanged = $beforeAgeGroup !== null && $beforeAgeGroup !== ''
            ? $beforeAgeGroup !== $afterAgeGroup
            : !empty($afterAssignments);

        $assignmentsChanged = $beforeSignature !== $afterSignature;

        return [
            'updated' => $ageGroupChanged || $assignmentsChanged,
            'ageGroupChanged' => $ageGroupChanged,
            'assignmentsChanged' => $assignmentsChanged,
            'ageGroup' => $afterAgeGroup,
            'assignments' => $afterAssignments
        ];
    }

    // Create player profile for new registrations
    public function createPlayerProfile($playerId, $data = []) {
        $this->db->query('INSERT INTO PlayerProfile (PlayerID, SchoolInstitution, SubscriptionType) 
                         VALUES (:player_id, :school, :subscription_type)');
        
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':school', $data['school'] ?? null);
        $this->db->bind(':subscription_type', 'basic'); // Default subscription
        
        return $this->db->execute();
    }

    // Create coach profile
    public function createCoachProfile($coachId, $data = []) {
        $this->db->query('INSERT INTO CoachProfile (CoachID, Specialization, Experience, Certifications, IsHeadCoach) 
                         VALUES (:coach_id, :specialization, :experience, :certifications, :is_head_coach)');
        
        $this->db->bind(':coach_id', $coachId);
        $this->db->bind(':specialization', $this->normalizeCoachSpecialization($data['specialization'] ?? 'Batting'));
        $this->db->bind(':experience', $data['experience'] ?? 0);
        $this->db->bind(':certifications', $data['certifications'] ?? null);
        $this->db->bind(':is_head_coach', $data['is_head_coach'] ?? false);
        
        return $this->db->execute();
    }

    private function normalizeCoachSpecialization(?string $specialization): string {
        $allowed = ['Batting', 'Bowling', 'Fielding'];
        $normalized = trim((string) $specialization);

        if ($normalized === '') {
            return 'Batting';
        }

        foreach ($allowed as $allowedSpecialization) {
            if (strcasecmp($normalized, $allowedSpecialization) === 0) {
                return $allowedSpecialization;
            }
        }

        return 'Batting';
    }

    // Create trainer profile
    public function createTrainerProfile($trainerId, $data = []) {
        $this->db->query('INSERT INTO TrainerProfile (TrainerID, Experience, Certifications) 
                         VALUES (:trainer_id, :experience, :certifications)');
        
        $this->db->bind(':trainer_id', $trainerId);
        $this->db->bind(':experience', $data['experience'] ?? 0);
        $this->db->bind(':certifications', $data['certifications'] ?? null);
        
        return $this->db->execute();
    }

    // Create shop employee profile
    public function createShopEmployeeProfile($employeeId, $data = []) {
        $this->db->query('INSERT INTO ShopEmployeeProfile (ShopEmployeeID, Department, HireDate) 
                         VALUES (:employee_id, :department, :hire_date)');
        
        $this->db->bind(':employee_id', $employeeId);
        $this->db->bind(':department', $data['department'] ?? 'General');
        $this->db->bind(':hire_date', $data['hire_date'] ?? date('Y-m-d'));
        
        return $this->db->execute();
    }

    // Create admin profile
    public function createAdminProfile($adminId, $data = []) {
        $this->db->query('INSERT INTO AdminProfile (AdminID, Section, Department, AccessLevel, HireDate) 
                         VALUES (:admin_id, :section, :department, :access_level, :hire_date)');
        
        $this->db->bind(':admin_id', $adminId);
        $this->db->bind(':section', $data['section'] ?? 'General');
       $this->db->bind(':hire_date', $data['hire_date'] ?? date('Y-m-d'));
        
        return $this->db->execute();
    }

    // Create appropriate profile based on role
    public function createRoleProfile($userId, $role, $data = []) {
        switch($role) {
            case 'Player':
                return $this->createPlayerProfile($userId, $data);
            case 'Coach':
                return $this->createCoachProfile($userId, $data);
            case 'Trainer':
                return $this->createTrainerProfile($userId, $data);
            case 'ShopEmployee':
                return $this->createShopEmployeeProfile($userId, $data);
            case 'Admin':
                return $this->createAdminProfile($userId, $data);
            default:
                return false;
        }
    }

    // Get user with profile information
    public function getUserWithProfile($userId) {
        $this->db->query("SELECT u.UserID,
                                u.FirstName,
                                u.LastName,
                                TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
                                u.DateOfBirth, u.PhoneNumber, u.Email,
                                u.Address, u.School, u.Role, u.Username, u.DateJoined, u.Status,
                                u.RequiresPasswordChange, u.PasswordChangeDeadline, u.LastLoginAt,
                                u.LoginAttempts, u.AccountLockedUntil, u.CreatedBy, u.Notes,
                                u.ProfileImage,
                                
                                -- Player Profile fields
                                pp.BattingStyle, pp.BowlingStyle, pp.JerseyNumber, 
                                pp.SubscriptionType, pp.EmergencyContactName, pp.EmergencyContactPhone, 
                                pp.ParentGuardianName, pp.ParentGuardianPhone, 
                                pp.PreviousExperience, pp.MedicalConditions, pp.HowHeardAboutUs,
                                
                                -- Coach Profile fields
                                cp.Specialization as CoachSpecialization, cp.Experience as CoachExperience, 
                                cp.Certifications as CoachCertifications, cp.IsHeadCoach,
                                
                                -- Trainer Profile fields
                                tp.Experience as TrainerExperience, tp.Certifications as TrainerCertifications,
                                
                                -- Shop Employee Profile fields
                                sep.Department as ShopDepartment, sep.HireDate as ShopHireDate
                                
                         FROM User u 
                         LEFT JOIN PlayerProfile pp ON u.UserID = pp.PlayerID 
                         LEFT JOIN CoachProfile cp ON u.UserID = cp.CoachID
                         LEFT JOIN TrainerProfile tp ON u.UserID = tp.TrainerID
                         LEFT JOIN ShopEmployeeProfile sep ON u.UserID = sep.ShopEmployeeID
                         WHERE u.UserID = :user_id");
        
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }

    // Update Player Profile specific fields
    public function updatePlayerProfile($data) {
        // First check if PlayerProfile exists
        $this->db->query('SELECT PlayerID FROM PlayerProfile WHERE PlayerID = :user_id');
        $this->db->bind(':user_id', $data['user_id']);
        $existingProfile = $this->db->single();
        
        if ($existingProfile) {
            // Update existing profile
            $this->db->query('UPDATE PlayerProfile SET 
                             BattingStyle = :batting_style,
                             BowlingStyle = :bowling_style,
                             JerseyNumber = :jersey_number,
                             SubscriptionType = :subscription_type,
                             EmergencyContactName = :emergency_contact_name,
                             EmergencyContactPhone = :emergency_contact_phone,
                             ParentGuardianName = :parent_guardian_name,
                             ParentGuardianPhone = :parent_guardian_phone,
                             SchoolInstitution = :school_institution,
                             PreviousExperience = :previous_experience,
                             MedicalConditions = :medical_conditions,
                             HowHeardAboutUs = :how_heard_about_us
                             WHERE PlayerID = :user_id');
        } else {
            // Create new profile
            $this->db->query('INSERT INTO PlayerProfile (
                             PlayerID, BattingStyle, BowlingStyle, JerseyNumber, SubscriptionType,
                             EmergencyContactName, EmergencyContactPhone, ParentGuardianName, 
                             ParentGuardianPhone, SchoolInstitution, PreviousExperience,
                             MedicalConditions, HowHeardAboutUs
                             ) VALUES (
                             :user_id, :batting_style, :bowling_style, :jersey_number, :subscription_type,
                             :emergency_contact_name, :emergency_contact_phone, :parent_guardian_name,
                             :parent_guardian_phone, :school_institution, :previous_experience,
                             :medical_conditions, :how_heard_about_us
                             )');
        }
        
        // Bind values (same for both INSERT and UPDATE)
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':batting_style', $data['batting_style'] ?? null);
        $this->db->bind(':bowling_style', $data['bowling_style'] ?? null);
        $this->db->bind(':jersey_number', $data['jersey_number'] ?? null);
        $this->db->bind(':subscription_type', $data['subscription_type'] ?? 'basic');
        $this->db->bind(':emergency_contact_name', $data['emergency_contact_name'] ?? null);
        $this->db->bind(':emergency_contact_phone', $data['emergency_contact_phone'] ?? null);
        $this->db->bind(':parent_guardian_name', $data['parent_guardian_name'] ?? null);
        $this->db->bind(':parent_guardian_phone', $data['parent_guardian_phone'] ?? null);
        $this->db->bind(':school_institution', $data['school_institution'] ?? null);
        $this->db->bind(':previous_experience', $data['previous_experience'] ?? null);
        $this->db->bind(':medical_conditions', $data['medical_conditions'] ?? null);
        $this->db->bind(':how_heard_about_us', $data['how_heard_about_us'] ?? null);
        
        return $this->db->execute();
    }

    // Update Coach Profile specific fields
    public function updateCoachProfile($data) {
        $this->db->query('UPDATE CoachProfile SET 
                         Specialization = :specialization,
                         Experience = :experience,
                         Certifications = :certifications
                         WHERE CoachID = :user_id');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':specialization', $this->normalizeCoachSpecialization($data['specialization'] ?? null));
        $this->db->bind(':experience', $data['experience'] ?? 0);
        $this->db->bind(':certifications', $data['certifications'] ?? null);
        
        return $this->db->execute();
    }

    // Update Trainer Profile specific fields
    public function updateTrainerProfile($data) {
        $this->db->query('UPDATE TrainerProfile SET 
                         Experience = :experience,
                         Certifications = :certifications
                         WHERE TrainerID = :user_id');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':experience', $data['experience'] ?? 0);
        $this->db->bind(':certifications', $data['certifications'] ?? null);
        
        return $this->db->execute();
    }

    // Update Shop Employee Profile specific fields
    public function updateShopEmployeeProfile($data) {
        $this->db->query('UPDATE ShopEmployeeProfile SET 
                         Department = :department
                         WHERE ShopEmployeeID = :user_id');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':department', $data['department'] ?? 'General');
        
        return $this->db->execute();
    }

    // Update Admin Profile specific fields
    public function updateAdminProfile($data) {
        $this->db->query('UPDATE AdminProfile SET 
                         AdminLevel = :admin_level,
                         Department = :department,
                         SecurityClearance = :security_clearance
                         WHERE AdminID = :user_id');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':admin_level', $data['admin_level'] ?? 'system_admin');
        $this->db->bind(':department', $data['department'] ?? 'General');
        $this->db->bind(':security_clearance', $data['security_clearance'] ?? 'Level1');
        
        return $this->db->execute();
    }

    // Get user by username and email (for password reset)
    public function getUserByUsernameAndEmail($username, $email) {
        $this->db->query("SELECT User.*, TRIM(CONCAT_WS(CHAR(32), FirstName, LastName)) AS Name FROM User WHERE Username = :username AND Email = :email");
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Update user password
    public function updatePassword($userId, $hashedPassword) {
        $this->db->query('UPDATE User SET PasswordHash = :password WHERE UserID = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':password', $hashedPassword);

        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Create staff member (Admin, Coach, Trainer, ShopEmployee)
    public function createStaff($data) {
        [$firstName, $lastName] = $this->extractNameParts($data);
        $this->db->query('INSERT INTO User (
            FirstName,
            LastName,
            DateOfBirth, 
            PhoneNumber, 
            Email, 
            Address, 
            School, 
            Role, 
            Username, 
            PasswordHash, 
            DateJoined, 
            Status,
            CreatedBy,
            Notes
        ) VALUES (
            :first_name,
            :last_name,
            :date_of_birth, 
            :phone_number, 
            :email, 
            :address, 
            :school, 
            :role, 
            :username, 
            :password_hash, 
            NOW(), 
            :status,
            :created_by,
            :notes
        )');
        
        // Bind values
        $this->db->bind(':first_name', $firstName);
        $this->db->bind(':last_name', $lastName !== '' ? $lastName : null);
        $this->db->bind(':date_of_birth', $data['dateOfBirth']);
        $this->db->bind(':phone_number', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':school', $data['school'] ?? null);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password_hash', $data['passwordHash']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':created_by', $data['createdBy'] ?? null);
        $this->db->bind(':notes', $data['notes'] ?? null);

        // Execute
        try {
            if($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                error_log("Database execution failed during staff creation");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during staff creation: " . $e->getMessage());
            return false;
        }
    }

    public function createPlayer($data) {
        [$firstName, $lastName] = $this->extractNameParts($data);
        // First, insert into User table
        $this->db->query('INSERT INTO User (
            FirstName,
            LastName,
            DateOfBirth, 
            PhoneNumber, 
            Email, 
            Address, 
            Role, 
            Username, 
            PasswordHash, 
            DateJoined, 
            Status,
            CreatedBy
        ) VALUES (
            :first_name,
            :last_name,
            :date_of_birth, 
            :phone_number, 
            :email, 
            :address, 
            :role, 
            :username, 
            :password_hash, 
            NOW(), 
            :status,
            :created_by
        )');
        
        // Bind values
        $this->db->bind(':first_name', $firstName);
        $this->db->bind(':last_name', $lastName !== '' ? $lastName : null);
        $this->db->bind(':date_of_birth', $data['dateOfBirth']);
        $this->db->bind(':phone_number', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':role', 'Player');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password_hash', $data['passwordHash']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':created_by', $data['createdBy'] ?? null);

        // Execute User insert
        try {
            if($this->db->execute()) {
                $userId = $this->db->lastInsertId();
                
                // Clean up any orphaned PlayerProfile record with this ID (from previous failed attempts)
                $this->db->query('DELETE FROM playerprofile WHERE PlayerID = :player_id');
                $this->db->bind(':player_id', $userId);
                $this->db->execute();
                
                // Now insert into PlayerProfile table
                $this->db->query('INSERT INTO playerprofile (
                    PlayerID,
                    JerseyNumber,
                    BattingStyle,
                    BowlingStyle,
                    SubscriptionType
                ) VALUES (
                    :player_id,
                    :jersey_number,
                    :batting_style,
                    :bowling_style,
                    :subscription_type
                )');
                
                $this->db->bind(':player_id', $userId);
                $this->db->bind(':jersey_number', $data['jerseyNumber'] ?? null);
                $this->db->bind(':batting_style', $data['battingStyle'] ?? null);
                $this->db->bind(':bowling_style', $data['bowlingStyle'] ?? null);
                $this->db->bind(':subscription_type', $data['subscriptionType'] ?? 'basic');
                
                if($this->db->execute()) {
                    return $userId;
                } else {
                    $errorInfo = $this->db->getError();
                    error_log("Failed to create Player profile for UserID: $userId - Error: " . print_r($errorInfo, true));
                    
                    // Rollback: Delete both playerprofile and user records
                    $this->db->query('DELETE FROM playerprofile WHERE PlayerID = :player_id');
                    $this->db->bind(':player_id', $userId);
                    $this->db->execute();
                    
                    $this->db->query('DELETE FROM User WHERE UserID = :user_id');
                    $this->db->bind(':user_id', $userId);
                    $this->db->execute();
                    
                    throw new Exception('Failed to create player profile: ' . ($errorInfo[2] ?? 'Unknown database error'));
                }
            } else {
                $errorInfo = $this->db->getError();
                error_log("Database execution failed during player user creation - Error: " . print_r($errorInfo, true));
                throw new Exception('Failed to create user account: ' . ($errorInfo[2] ?? 'Unknown database error'));
            }
        } catch (Exception $e) {
            error_log("Database error during player creation: " . $e->getMessage());
            throw $e;
        }
    }

    public function updatePlayer($data) {
        try {
            [$firstName, $lastName] = $this->extractNameParts($data);
            // Update User table
            $this->db->query('UPDATE User SET 
                FirstName = :first_name,
                LastName = :last_name,
                Email = :email,
                PhoneNumber = :phone,
                Address = :address,
                Status = :status
                WHERE UserID = :user_id');
            
            $this->db->bind(':first_name', $firstName);
            $this->db->bind(':last_name', $lastName !== '' ? $lastName : null);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':user_id', $data['playerId']);
            
            if(!$this->db->execute()) {
                $errorInfo = $this->db->getError();
                error_log("Failed to update User for PlayerID: {$data['playerId']} - Error: " . print_r($errorInfo, true));
                throw new Exception('Failed to update user account: ' . ($errorInfo[2] ?? 'Unknown database error'));
            }
            
            // Update PlayerProfile table
            $this->db->query('UPDATE playerprofile SET 
                JerseyNumber = :jersey_number,
                BattingStyle = :batting_style,
                BowlingStyle = :bowling_style,
                SubscriptionType = :subscription_type
                WHERE PlayerID = :player_id');
            
            $this->db->bind(':jersey_number', $data['jerseyNumber']);
            $this->db->bind(':batting_style', $data['battingStyle']);
            $this->db->bind(':bowling_style', $data['bowlingStyle']);
            $this->db->bind(':subscription_type', $data['subscriptionType']);
            $this->db->bind(':player_id', $data['playerId']);
            
            if(!$this->db->execute()) {
                $errorInfo = $this->db->getError();
                error_log("Failed to update PlayerProfile for PlayerID: {$data['playerId']} - Error: " . print_r($errorInfo, true));
                throw new Exception('Failed to update player profile: ' . ($errorInfo[2] ?? 'Unknown database error'));
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Database error during player update: " . $e->getMessage());
            throw $e;
        }
    }

    // ============= PROFILE IMAGE METHODS =============

    // Update user profile image
    public function updateProfileImage($userId, $imagePath) {
        $this->db->query('UPDATE User SET ProfileImage = :image_path WHERE UserID = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':image_path', $imagePath);
        
        try {
            if($this->db->execute()) {
                return true;
            } else {
                error_log("Database execution failed during profile image update");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during profile image update: " . $e->getMessage());
            return false;
        }
    }

    // Get user profile image path
    public function getProfileImage($userId) {
        $this->db->query('SELECT ProfileImage FROM User WHERE UserID = :user_id');
        $this->db->bind(':user_id', $userId);
        
        $result = $this->db->single();
        return $result ? $result->ProfileImage : null;
    }

    // Delete user profile image (set to NULL)
    public function deleteProfileImage($userId) {
        $this->db->query('UPDATE User SET ProfileImage = NULL WHERE UserID = :user_id');
        $this->db->bind(':user_id', $userId);
        
        try {
            if($this->db->execute()) {
                return true;
            } else {
                error_log("Database execution failed during profile image deletion");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during profile image deletion: " . $e->getMessage());
            return false;
        }
    }

    // Get all staff members (Coach, Trainer, ShopEmployee)
    public function getStaffMembers() {
        $this->db->query("SELECT 
            u.UserID,
            u.FirstName,
            u.LastName,
            TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
            u.Email,
            u.PhoneNumber,
            u.Role,
            u.DateJoined,
            u.Status,
            u.ProfileImage,
            CASE 
                WHEN u.Role = 'Coach' THEN cp.Specialization
                WHEN u.Role = 'Trainer' THEN 'Fitness Training'
                WHEN u.Role = 'ShopEmployee' THEN sep.Department
                ELSE NULL
            END as Department,
            CASE 
                WHEN u.Role = 'Coach' THEN cp.Experience
                WHEN u.Role = 'Trainer' THEN tp.Experience
                ELSE NULL
            END as Experience
        FROM User u
        LEFT JOIN CoachProfile cp ON u.UserID = cp.CoachID AND u.Role = 'Coach'
        LEFT JOIN TrainerProfile tp ON u.UserID = tp.TrainerID AND u.Role = 'Trainer'
        LEFT JOIN ShopEmployeeProfile sep ON u.UserID = sep.ShopEmployeeID AND u.Role = 'ShopEmployee'
        WHERE u.Role IN ('Coach', 'Trainer', 'ShopEmployee')
        ORDER BY u.DateJoined DESC");
        
        $results = $this->db->resultSet();
        return $results;
    }

    // Get staff statistics
    public function getStaffStats() {
        $this->db->query('SELECT 
            COUNT(*) as total_staff,
            SUM(CASE WHEN Role = "Coach" THEN 1 ELSE 0 END) as total_coaches,
            SUM(CASE WHEN Role = "Trainer" THEN 1 ELSE 0 END) as total_trainers,
            SUM(CASE WHEN Role = "ShopEmployee" THEN 1 ELSE 0 END) as total_shop_employees,
            SUM(CASE WHEN Status = "active" THEN 1 ELSE 0 END) as active_staff
        FROM User 
        WHERE Role IN ("Coach", "Trainer", "ShopEmployee")');
        
        $result = $this->db->single();
        return $result;
    }

    // Get all players with their profile information
    public function getAllPlayersWithProfile() {
        $this->db->query("SELECT 
            u.UserID,
            u.FirstName,
            u.LastName,
            TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
            u.DateOfBirth,
            u.PhoneNumber,
            u.Email,
            u.Address,
            u.School,
            u.DateJoined,
            u.Status,
            u.ProfileImage,
            pp.BattingStyle,
            pp.BowlingStyle,
            pp.JerseyNumber,
            pp.SubscriptionType,
            mp.PlanName AS SubscriptionPlanName,
            pp.EmergencyContactName,
            pp.EmergencyContactPhone,
            pp.ParentGuardianName,
            pp.ParentGuardianPhone,
            pp.SchoolInstitution,
            pp.PreviousExperience,
            pp.MedicalConditions,
            pp.HowHeardAboutUs,
            TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) as Age
        FROM user u
        LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
        LEFT JOIN playersubscription ps ON ps.SubscriptionID = (
            SELECT ps2.SubscriptionID
            FROM playersubscription ps2
            WHERE ps2.PlayerID = u.UserID
              AND ps2.Status = 'active'
            ORDER BY ps2.StartDate DESC, ps2.SubscriptionID DESC
            LIMIT 1
        )
        LEFT JOIN membershipplan mp ON mp.PlanID = ps.PlanID
        WHERE u.Role = 'Player'
        ORDER BY u.DateJoined DESC");
        
        return $this->db->resultSet();
    }

    // Get players for report with filters
    public function getPlayersForReport($filters = []) {
        $sql = "SELECT 
            u.UserID,
            u.FirstName,
            u.LastName,
            TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
            u.Email,
            u.PhoneNumber,
            u.DateOfBirth,
            u.Status,
            u.DateJoined,
            pp.BattingStyle,
            pp.BowlingStyle,
            pp.SubscriptionType,
            pp.JerseyNumber,
            TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) as Age
        FROM user u
        LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
        WHERE u.Role = 'Player'";
        
        $conditions = [];
        $params = [];
        
        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $conditions[] = 'u.Status = :status';
            $params[':status'] = $filters['status'];
        }
        
        // Subscription filter
        if (!empty($filters['subscription']) && $filters['subscription'] !== 'all') {
            $conditions[] = 'pp.SubscriptionType = :subscription';
            $params[':subscription'] = $filters['subscription'];
        }
        
        // Batting style filter
        if (!empty($filters['batting']) && $filters['batting'] !== 'all') {
            $conditions[] = 'pp.BattingStyle = :batting';
            $params[':batting'] = $filters['batting'];
        }
        
        // Bowling style filter
        if (!empty($filters['bowling']) && $filters['bowling'] !== 'all') {
            $conditions[] = 'pp.BowlingStyle = :bowling';
            $params[':bowling'] = $filters['bowling'];
        }
        
        // Search filter (name or email)
        if (!empty($filters['search'])) {
            $conditions[] = '(CONCAT_WS(" ", u.FirstName, u.LastName) LIKE :search OR u.Email LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Add conditions to SQL
        if (!empty($conditions)) {
            $sql .= ' AND ' . implode(' AND ', $conditions);
        }
        
        $sql .= ' ORDER BY u.FirstName ASC, u.LastName ASC';
        
        $this->db->query($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }

    // Get player statistics
    public function getPlayerStats() {
        $this->db->query('SELECT 
            COUNT(*) as total_players,
            SUM(CASE WHEN Status = "active" THEN 1 ELSE 0 END) as active_players,
            SUM(CASE WHEN pp.SubscriptionType = "basic" THEN 1 ELSE 0 END) as basic_subscription,
            SUM(CASE WHEN pp.SubscriptionType = "premium" THEN 1 ELSE 0 END) as premium_subscription,
            SUM(CASE WHEN pp.SubscriptionType = "private_only" THEN 1 ELSE 0 END) as private_only_subscription
        FROM user u 
        LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
        WHERE u.Role = "Player"');
        
        $result = $this->db->single();
        return $result;
    }

    // Update staff member information
    public function updateStaff($data) {
        try {
            [$firstName, $lastName] = $this->extractNameParts($data);
            
            // Map role values from form to database values
            $roleMap = [
                'coach' => 'Coach',
                'head_coach' => 'Coach',
                'trainer' => 'Trainer',
                'admin' => 'Admin',
                'shopkeeper' => 'ShopEmployee',
                'shopemployee' => 'ShopEmployee'
            ];
            
            $role = $roleMap[strtolower($data['role'])] ?? $data['role'];
            
            // Update User table
            $this->db->query('UPDATE User SET 
                FirstName = :first_name,
                LastName = :last_name,
                Email = :email,
                PhoneNumber = :phone,
                Address = :address,
                Status = :status,
                Role = :role
                WHERE UserID = :user_id');
            
            $this->db->bind(':first_name', $firstName);
            $this->db->bind(':last_name', $lastName !== '' ? $lastName : null);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':role', $role);
            $this->db->bind(':user_id', $data['user_id']);
            
            if ($this->db->execute()) {
                // If specialization is provided, update role-specific profile
                if (!empty($data['specialization'])) {
                    if ($role === 'Coach') {
                        $this->db->query('UPDATE CoachProfile SET 
                            Specialization = :specialization 
                            WHERE CoachID = :user_id');
                        $this->db->bind(':specialization', $data['specialization']);
                        $this->db->bind(':user_id', $data['user_id']);
                        $this->db->execute();
                    } elseif ($role === 'Trainer') {
                        $this->db->query('UPDATE TrainerProfile SET 
                            Specialization = :specialization 
                            WHERE TrainerID = :user_id');
                        $this->db->bind(':specialization', $data['specialization']);
                        $this->db->bind(':user_id', $data['user_id']);
                        $this->db->execute();
                    }
                }
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error in updateStaff: " . $e->getMessage());
            return false;
        }
    }

    // Delete user account
    public function deleteUser($userId) {
        try {
            // First, delete related records based on role
            $user = $this->getUserById($userId);
            
            if (!$user) {
                return false;
            }
            
            // Delete role-specific profile data
            if ($user->Role === 'Coach') {
                $this->db->query('DELETE FROM CoachProfile WHERE CoachID = :user_id');
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
            } elseif ($user->Role === 'Trainer') {
                $this->db->query('DELETE FROM TrainerProfile WHERE TrainerID = :user_id');
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
            } elseif ($user->Role === 'Player') {
                $this->db->query('DELETE FROM PlayerProfile WHERE PlayerID = :user_id');
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
            } elseif ($user->Role === 'ShopEmployee') {
                $this->db->query('DELETE FROM ShopEmployeeProfile WHERE ShopEmployeeID = :user_id');
                $this->db->bind(':user_id', $userId);
                $this->db->execute();
            }
            
            // Delete from User table
            $this->db->query('DELETE FROM User WHERE UserID = :user_id');
            $this->db->bind(':user_id', $userId);
            
            if ($this->db->execute()) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error in deleteUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get assignments from playercoachassignment table
     * @param int $userId - The user ID to look up
     * @param string $lookupBy - 'coach' to get players for a coach, 'player' to get coaches for a player
     * @return array - Result set of assigned users
     */
    public function getPlayersAssignedToCoach($userId, $lookupBy = 'coach') {
        if ($lookupBy === 'player') {
            $this->db->query('SELECT COUNT(*) AS cnt FROM player_skill_coach_assignment WHERE PlayerID = :userId');
            $this->db->bind(':userId', $userId, PDO::PARAM_INT);
            $newAssignments = $this->db->single();

            if ((int)($newAssignments->cnt ?? 0) > 0) {
                $this->db->query('SELECT 
                    u.UserID as coach_id,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as name,
                    u.Email, u.ProfileImage as image,
                    cp.Specialization as specialization, cp.Experience as experience_years, cp.Certifications,
                    psca.CoachingType as AssignmentType,
                    "active" as AssignmentStatus,
                    psca.AgeGroup,
                    psca.AssignedAt as AssignedDate
                FROM player_skill_coach_assignment psca
                JOIN User u ON psca.CoachID = u.UserID
                JOIN coachprofile cp ON u.UserID = cp.CoachID
                WHERE psca.PlayerID = :userId AND u.Status = "active"
                ORDER BY u.FirstName ASC, u.LastName ASC, psca.CoachingType ASC');
                $this->db->bind(':userId', $userId, PDO::PARAM_INT);
                return $this->db->resultSet();
            }
        } else {
            $this->db->query('SELECT COUNT(*) AS cnt FROM player_skill_coach_assignment WHERE CoachID = :userId');
            $this->db->bind(':userId', $userId, PDO::PARAM_INT);
            $newAssignments = $this->db->single();

            if ((int)($newAssignments->cnt ?? 0) > 0) {
                $this->db->query('SELECT 
                    u.UserID,
                    u.FirstName,
                    u.LastName,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
                    u.Email, u.PhoneNumber, u.DateOfBirth, u.Status,
                    u.ProfileImage, u.Address, u.School,
                    pp.BattingStyle, pp.BowlingStyle, pp.JerseyNumber, pp.SubscriptionType,
                    psca.CoachingType as AssignmentType,
                    psca.AssignedAt as AssignedDate,
                    "active" as AssignmentStatus,
                    psca.AgeGroup as AssignmentNotes
                FROM player_skill_coach_assignment psca
                JOIN User u ON psca.PlayerID = u.UserID
                LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
                WHERE psca.CoachID = :userId AND u.Status = "active"
                ORDER BY u.FirstName ASC, u.LastName ASC, psca.CoachingType ASC');
                $this->db->bind(':userId', $userId, PDO::PARAM_INT);
                return $this->db->resultSet();
            }
        }

        if ($lookupBy === 'player') {
            // Get coaches assigned to this player
            $this->db->query('SELECT 
                u.UserID as coach_id,
                TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as name,
                u.Email, u.ProfileImage as image,
                cp.Specialization as specialization, cp.Experience as experience_years, cp.Certifications,
                pca.AssignmentType, pca.Status as AssignmentStatus
            FROM playercoachassignment pca
            JOIN User u ON pca.CoachID = u.UserID
            JOIN coachprofile cp ON u.UserID = cp.CoachID
            WHERE pca.PlayerID = :userId AND pca.Status = "active" AND u.Status = "active"
            ORDER BY u.FirstName ASC, u.LastName ASC');
        } else {
            // Default: Get players assigned to this coach
            $this->db->query('SELECT 
                u.UserID,
                u.FirstName,
                u.LastName,
                TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS Name,
                u.Email, u.PhoneNumber, u.DateOfBirth, u.Status,
                u.ProfileImage, u.Address, u.School,
                pp.BattingStyle, pp.BowlingStyle, pp.JerseyNumber, pp.SubscriptionType,
                pca.AssignmentType, pca.AssignedDate, pca.Status as AssignmentStatus, pca.Notes as AssignmentNotes
            FROM playercoachassignment pca
            JOIN User u ON pca.PlayerID = u.UserID
            LEFT JOIN playerprofile pp ON u.UserID = pp.PlayerID
            WHERE pca.CoachID = :userId AND pca.Status = "active"
            ORDER BY u.FirstName ASC, u.LastName ASC');
        }
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    public function getCoachAssignedPlayers($coachId) {
        try {
            $this->db->query('SELECT COUNT(*) as count FROM player_skill_coach_assignment WHERE CoachID = :coachId');
            $this->db->bind(':coachId', $coachId, PDO::PARAM_INT);
            $assignmentCount = $this->db->single();

            if ((int)($assignmentCount->count ?? 0) > 0) {
                $this->db->query(
                    "SELECT 
                        COALESCE(p.PlayerID, psca.PlayerID) AS PlayerID,
                        TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as Name,
                        TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as PlayerName,
                        u.UserID,
                        u.Email,
                        u.PhoneNumber,
                                                u.DateOfBirth,
                        u.Status,
                                                COALESCE(p.BattingStyle, 'N/A') AS BattingStyle,
                                                COALESCE(p.BowlingStyle, 'N/A') AS BowlingStyle,
                        GROUP_CONCAT(DISTINCT psca.CoachingType ORDER BY FIELD(psca.CoachingType, 'batting', 'bowling', 'fielding') SEPARATOR ', ') as AssignmentType,
                        GROUP_CONCAT(DISTINCT psca.AgeGroup ORDER BY psca.AgeGroup SEPARATOR ', ') as AssignmentAgeGroups,
                        COUNT(DISTINCT psca.CoachingType) as AssignmentCount,
                        'active' as AssignmentStatus,
                        (SELECT COUNT(*) FROM playertournamentstats WHERE PlayerID = COALESCE(p.PlayerID, psca.PlayerID)) as TournamentCount
                    FROM player_skill_coach_assignment psca
                                        LEFT JOIN playerprofile p ON psca.PlayerID = p.PlayerID
                                        JOIN user u ON psca.PlayerID = u.UserID
                    WHERE psca.CoachID = :coachId
                      AND u.Status = 'active'
                                                                                GROUP BY psca.PlayerID, p.PlayerID, u.UserID, u.FirstName, u.LastName, p.BattingStyle, p.BowlingStyle, u.DateOfBirth, u.Status
                    ORDER BY u.FirstName ASC, u.LastName ASC"
                );
                $this->db->bind(':coachId', $coachId, PDO::PARAM_INT);
                return $this->db->resultSet();
            }

            $this->db->query(
                "SELECT 
                    COALESCE(p.PlayerID, pca.PlayerID) AS PlayerID,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as Name,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as PlayerName,
                    u.UserID,
                    u.Email,
                    u.PhoneNumber,
                                        u.DateOfBirth,
                    u.Status,
                    COALESCE(p.BattingStyle, 'N/A') AS BattingStyle,
                    COALESCE(p.BowlingStyle, 'N/A') AS BowlingStyle,
                    GROUP_CONCAT(DISTINCT pca.AssignmentType ORDER BY pca.AssignmentType SEPARATOR ', ') as AssignmentType,
                    NULL as AssignmentAgeGroups,
                    COUNT(DISTINCT pca.AssignmentType) as AssignmentCount,
                    pca.Status as AssignmentStatus,
                    (SELECT COUNT(*) FROM playertournamentstats WHERE PlayerID = COALESCE(p.PlayerID, pca.PlayerID)) as TournamentCount
                FROM playercoachassignment pca
                LEFT JOIN playerprofile p ON pca.PlayerID = p.PlayerID
                                JOIN user u ON pca.PlayerID = u.UserID
                WHERE pca.CoachID = :coachId
                  AND pca.Status = 'active'
                  AND u.Status = 'active'
                                                                GROUP BY pca.PlayerID, p.PlayerID, u.UserID, u.FirstName, u.LastName, p.BattingStyle, p.BowlingStyle, u.DateOfBirth, pca.Status
                ORDER BY u.FirstName ASC, u.LastName ASC"
            );
            $this->db->bind(':coachId', $coachId, PDO::PARAM_INT);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in getCoachAssignedPlayers: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get notifications for a specific user
     */
    public function getNotificationsByUser($userId) {
        $this->db->query('SELECT 
            NotificationID as id,
            UserID,
            Type as type,
            Title as title,
            Message as message,
            IsRead as is_read,
            CreatedAt,
            CASE 
                WHEN TIMESTAMPDIFF(MINUTE, CreatedAt, NOW()) < 60 THEN CONCAT(TIMESTAMPDIFF(MINUTE, CreatedAt, NOW()), " mins ago")
                WHEN TIMESTAMPDIFF(HOUR, CreatedAt, NOW()) < 24 THEN CONCAT(TIMESTAMPDIFF(HOUR, CreatedAt, NOW()), " hours ago")
                WHEN TIMESTAMPDIFF(DAY, CreatedAt, NOW()) < 7 THEN CONCAT(TIMESTAMPDIFF(DAY, CreatedAt, NOW()), " days ago")
                ELSE DATE_FORMAT(CreatedAt, "%b %d, %Y")
            END as time
        FROM notification 
        WHERE UserID = :userId 
        ORDER BY CreatedAt DESC');
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadNotificationCount($userId) {
        $this->db->query('SELECT COUNT(*) as count FROM notification WHERE UserID = :userId AND IsRead = 0');
        $this->db->bind(':userId', $userId);
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    /**
     * Mark a notification as read
     */
    public function markNotificationRead($notificationId, $userId) {
        $this->db->query('UPDATE notification SET IsRead = 1, ReadAt = NOW() WHERE NotificationID = :id AND UserID = :userId');
        $this->db->bind(':id', $notificationId);
        $this->db->bind(':userId', $userId);
        return $this->db->execute();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllNotificationsRead($userId) {
        $this->db->query('UPDATE notification SET IsRead = 1, ReadAt = NOW() WHERE UserID = :userId AND IsRead = 0');
        $this->db->bind(':userId', $userId);
        return $this->db->execute();
    }

    /**
     * Delete a notification
     */
    public function deleteNotification($notificationId, $userId) {
        $this->db->query('DELETE FROM notification WHERE NotificationID = :id AND UserID = :userId');
        $this->db->bind(':id', $notificationId);
        $this->db->bind(':userId', $userId);
        return $this->db->execute();
    }

    /**
     * Delete all notifications for a user
     */
    public function deleteAllNotifications($userId) {
        $this->db->query('DELETE FROM notification WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        return $this->db->execute();
    }

    /**
     * Get feedback received by a specific user (coach/trainer)
     */
    public function getFeedbackForUser($userId) {
        $this->db->query('SELECT 
            f.FeedbackID, f.Content, f.Rating, f.Category, f.Status, f.CreatedDate,
            TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as FromUserName, u.Email as FromUserEmail
        FROM feedback f
        JOIN User u ON f.FromUserID = u.UserID
        WHERE f.ToUserID = :userId
        ORDER BY f.CreatedDate DESC');
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    // Get all coach profiles with user info
    public function getAllCoachProfiles() {
        $this->db->query("SELECT u.UserID as coach_id, TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as name, u.Email, u.ProfileImage as image,
            cp.Specialization as specialization, cp.Experience as experience_years, cp.Certifications, cp.IsHeadCoach
            FROM User u
            JOIN coachprofile cp ON u.UserID = cp.CoachID
            WHERE u.Role = 'Coach' AND u.Status = 'active'
            ORDER BY u.FirstName, u.LastName");
        return $this->db->resultSet();
    }

    public function getCoachSkillAgeGroupAssignments(): array {
        $this->db->query(
            "SELECT csg.CoachID,
                    TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) AS CoachName,
                    csg.CoachingType,
                    GROUP_CONCAT(csg.AgeGroup ORDER BY csg.PriorityRank ASC SEPARATOR ', ') AS AgeGroups,
                    cp.IsHeadCoach
             FROM coach_skill_age_group_assignment csg
             JOIN user u ON u.UserID = csg.CoachID
             JOIN coachprofile cp ON cp.CoachID = csg.CoachID
             WHERE csg.IsActive = 1
              GROUP BY csg.CoachID, u.FirstName, u.LastName, csg.CoachingType, cp.IsHeadCoach
             ORDER BY cp.IsHeadCoach DESC,
                 u.FirstName ASC,
                 u.LastName ASC,
                      FIELD(csg.CoachingType, 'batting', 'bowling', 'fielding')"
        );

        return $this->db->resultSet();
    }

    public function replaceCoachSkillAgeGroupAssignments(int $coachId, string $coachingType, array $ageGroups, ?int $assignedBy = null): bool {
        $ageGroups = array_values(array_unique(array_filter(array_map('trim', $ageGroups))));

        if (empty($ageGroups)) {
            return false;
        }

        $startedTransaction = false;
        if (method_exists($this->db, 'inTransaction') && method_exists($this->db, 'beginTransaction') && !$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        try {
            $this->db->query(
                'DELETE FROM coach_skill_age_group_assignment
                 WHERE CoachID = :coach_id AND CoachingType = :coaching_type'
            );
            $this->db->bind(':coach_id', $coachId, PDO::PARAM_INT);
            $this->db->bind(':coaching_type', $coachingType);
            if (!$this->db->execute()) {
                throw new RuntimeException('Failed to clear existing coach skill assignments.');
            }

            foreach ($ageGroups as $index => $ageGroup) {
                $this->db->query(
                    'INSERT INTO coach_skill_age_group_assignment
                     (CoachID, CoachingType, AgeGroup, PriorityRank, IsActive, Notes, AssignedBy)
                     VALUES (:coach_id, :coaching_type, :age_group, :priority_rank, 1, :notes, :assigned_by)'
                );
                $this->db->bind(':coach_id', $coachId, PDO::PARAM_INT);
                $this->db->bind(':coaching_type', $coachingType);
                $this->db->bind(':age_group', $ageGroup);
                $this->db->bind(':priority_rank', $index + 1, PDO::PARAM_INT);
                $this->db->bind(':notes', 'Assigned by admin from staff management page');
                $this->db->bind(':assigned_by', $assignedBy, $assignedBy === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

                if (!$this->db->execute()) {
                    throw new RuntimeException('Failed to save coach skill age-group assignment.');
                }
            }

            if ($startedTransaction && method_exists($this->db, 'commit')) {
                $this->db->commit();
            }

            return true;
        } catch (Throwable $e) {
            if ($startedTransaction && method_exists($this->db, 'rollBack')) {
                $this->db->rollBack();
            }
            error_log('Error in replaceCoachSkillAgeGroupAssignments: ' . $e->getMessage());
            return false;
        }
    }

    public function updateHeadCoachDesignation(int $coachId): bool {
        $startedTransaction = false;
        if (method_exists($this->db, 'inTransaction') && method_exists($this->db, 'beginTransaction') && !$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        try {
            $this->db->query('UPDATE coachprofile SET IsHeadCoach = 0');
            if (!$this->db->execute()) {
                throw new RuntimeException('Failed to clear previous head coach.');
            }

            $this->db->query('UPDATE coachprofile SET IsHeadCoach = 1 WHERE CoachID = :coach_id');
            $this->db->bind(':coach_id', $coachId, PDO::PARAM_INT);
            if (!$this->db->execute()) {
                throw new RuntimeException('Failed to set new head coach.');
            }

            if ($startedTransaction && method_exists($this->db, 'commit')) {
                $this->db->commit();
            }

            return true;
        } catch (Throwable $e) {
            if ($startedTransaction && method_exists($this->db, 'rollBack')) {
                $this->db->rollBack();
            }
            error_log('Error in updateHeadCoachDesignation: ' . $e->getMessage());
            return false;
        }
    }

    // Get all trainer profiles with user info
    public function getAllTrainerProfiles() {
        $this->db->query("SELECT u.UserID as trainer_id, TRIM(CONCAT_WS(CHAR(32), u.FirstName, u.LastName)) as name, u.Email, u.ProfileImage as image,
            NULL as specialization, tp.Experience as experience_years, tp.Certifications
            FROM User u
            JOIN trainerprofile tp ON u.UserID = tp.TrainerID
            WHERE u.Role = 'Trainer' AND u.Status = 'active'
            ORDER BY u.FirstName, u.LastName");
        return $this->db->resultSet();
    }
}
?>
