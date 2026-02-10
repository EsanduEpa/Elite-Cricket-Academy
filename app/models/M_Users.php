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
        // STEP 1: PREPARE SQL INSERT QUERY
        // Uses placeholders (:name) to prevent SQL injection
        // INSERT INTO table (columns) VALUES (placeholders)
        $this->db->query('INSERT INTO User (
            Name,
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
            :name, 
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
        $this->db->bind(':name', $data['fullName']);
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
                error_log("Database execution failed during user registration");
                return false;
            }
        } catch (Exception $e) {
            error_log("Database error during registration: " . $e->getMessage());
            return false;
        }
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
        $this->db->query('SELECT * FROM User WHERE Email = :email OR Username = :email');
        
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
        $this->db->query('SELECT * FROM User WHERE UserID = :id');
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
            u.Name as user_name 
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
        $this->db->query('SELECT * FROM User ORDER BY DateJoined DESC LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }

    // Update user profile
    public function updateUser($data) {
        $this->db->query('UPDATE User SET 
                         Name = :name,
                         Email = :email,
                         PhoneNumber = :phone_number,
                         Address = :address,
                         School = :school,
                         Role = :role,
                         Status = :status
                         WHERE UserID = :user_id');

        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':name', $data['name']);
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
        $this->db->bind(':specialization', $data['specialization'] ?? 'All-rounder');
        $this->db->bind(':experience', $data['experience'] ?? 0);
        $this->db->bind(':certifications', $data['certifications'] ?? null);
        $this->db->bind(':is_head_coach', $data['is_head_coach'] ?? false);
        
        return $this->db->execute();
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
        $this->db->query('SELECT u.UserID, u.Name, u.DateOfBirth, u.PhoneNumber, u.Email, 
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
                         WHERE u.UserID = :user_id');
        
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
        $this->db->bind(':specialization', $data['specialization'] ?? null);
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
        $this->db->query('SELECT * FROM User WHERE Username = :username AND Email = :email');
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
        $this->db->query('INSERT INTO User (
            Name, 
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
            :name, 
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
        $this->db->bind(':name', $data['fullName']);
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
        $this->db->query('SELECT 
            u.UserID,
            u.Name,
            u.Email,
            u.PhoneNumber,
            u.Role,
            u.DateJoined,
            u.Status,
            u.ProfileImage,
            CASE 
                WHEN u.Role = "Coach" THEN cp.Specialization
                WHEN u.Role = "Trainer" THEN "Fitness Training"
                WHEN u.Role = "ShopEmployee" THEN sep.Department
                ELSE NULL
            END as Department,
            CASE 
                WHEN u.Role = "Coach" THEN cp.Experience
                WHEN u.Role = "Trainer" THEN tp.Experience
                ELSE NULL
            END as Experience
        FROM User u
        LEFT JOIN CoachProfile cp ON u.UserID = cp.CoachID AND u.Role = "Coach"
        LEFT JOIN TrainerProfile tp ON u.UserID = tp.TrainerID AND u.Role = "Trainer"
        LEFT JOIN ShopEmployeeProfile sep ON u.UserID = sep.ShopEmployeeID AND u.Role = "ShopEmployee"
        WHERE u.Role IN ("Coach", "Trainer", "ShopEmployee")
        ORDER BY u.DateJoined DESC');
        
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
        $this->db->query('SELECT 
            u.UserID,
            u.Name,
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
        WHERE u.Role = "Player"
        ORDER BY u.DateJoined DESC');
        
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
            // Build the full name
            $fullName = trim($data['first_name'] . ' ' . $data['last_name']);
            
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
                Name = :name,
                Email = :email,
                PhoneNumber = :phone,
                Address = :address,
                Status = :status,
                Role = :role
                WHERE UserID = :user_id');
            
            $this->db->bind(':name', $fullName);
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
}
?> 