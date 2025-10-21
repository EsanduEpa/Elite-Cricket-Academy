<?php

class M_Users {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Register user with new table structure
    public function register($data) {
        $this->db->query('INSERT INTO User (Name, DateOfBirth, Address, School, Email, PhoneNumber, Role, Username, PasswordHash, DateJoined) VALUES(:name, :date_of_birth, :address, :school, :email, :phone_number, :role, :username, :password_hash, NOW())');
        
        // Bind values
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

    // Login user
    public function login($email, $password) {
        $this->db->query('SELECT * FROM User WHERE Email = :email OR Username = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->PasswordHash;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }

        return false;
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM User WHERE Email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Find user by username
    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM User WHERE Username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get user by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM User WHERE UserID = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    // Get total users count
    public function getTotalUsers() {
        $this->db->query('SELECT COUNT(*) as count FROM User');
        $result = $this->db->single();
        
        return $result ? $result->count : 25; // Return dummy data if no database
    }

    // Get total users by type (coach, player, trainer, staff)
    public function getTotalUsersByType($type) {
        $this->db->query('SELECT COUNT(*) as count FROM User WHERE Role = :type');
        $this->db->bind(':type', $type);
        $result = $this->db->single();
        
        // Return dummy data if no database results
        if (!$result) {
            switch($type) {
                case 'coach': return 8;
                case 'player': return 15;
                case 'trainer': return 3;
                case 'staff': return 2;
                default: return 0;
            }
        }
        
        return $result->count;
    }

    // Get today's registrations
    public function getTodayRegistrations() {
        $this->db->query('SELECT COUNT(*) as count FROM User WHERE DATE(DateJoined) = CURDATE()');
        $result = $this->db->single();
        
        return $result ? $result->count : 3; // Return dummy data if no database
    }

    // Get recent activities
    public function getRecentActivities($limit = 10) {
        $this->db->query('SELECT al.*, u.Name as user_name 
                         FROM ActivityLog al 
                         LEFT JOIN User u ON al.UserID = u.UserID 
                         ORDER BY al.Timestamp DESC 
                         LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        $results = $this->db->resultSet();
        
        // Return dummy data if no database results
        if (empty($results)) {
            return [
                [
                    'id' => 1,
                    'action' => 'New player registered',
                    'user_name' => 'John Doe',
                    'timestamp' => '2 hours ago',
                    'type' => 'registration',
                    'details' => 'Player joined junior cricket program'
                ],
                [
                    'id' => 2,
                    'action' => 'Training session completed',
                    'user_name' => 'Coach Smith',
                    'timestamp' => '4 hours ago',
                    'type' => 'training',
                    'details' => 'Batting practice session for senior players'
                ],
                [
                    'id' => 3,
                    'action' => 'Event created',
                    'user_name' => 'Admin User',
                    'timestamp' => '1 day ago',
                    'type' => 'event',
                    'details' => 'Junior Cricket Tournament scheduled'
                ],
                [
                    'id' => 4,
                    'action' => 'Feedback submitted',
                    'user_name' => 'Sarah Wilson',
                    'timestamp' => '1 day ago',
                    'type' => 'feedback',
                    'details' => 'Training quality improvement suggestion'
                ],
                [
                    'id' => 5,
                    'action' => 'Profile updated',
                    'user_name' => 'Mike Johnson',
                    'timestamp' => '2 days ago',
                    'type' => 'profile',
                    'details' => 'Contact information updated'
                ]
            ];
        }
        
        return $results;
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
        $this->db->query('INSERT INTO ActivityLog (UserID, Action, Description, IPAddress, UserAgent) 
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
                                sep.Department as ShopDepartment, sep.HireDate as ShopHireDate,
                                
                                -- Admin Profile fields
                                ap.AdminLevel, ap.Department as AdminDepartment, ap.AccessPermissions,
                                ap.LastLoginIP, ap.AccountLocked, ap.LockoutExpiry, ap.TwoFactorEnabled,
                                ap.SecurityClearance, ap.HireDate as AdminHireDate, ap.SessionTimeout
                                
                         FROM User u 
                         LEFT JOIN PlayerProfile pp ON u.UserID = pp.PlayerID 
                         LEFT JOIN CoachProfile cp ON u.UserID = cp.CoachID
                         LEFT JOIN TrainerProfile tp ON u.UserID = tp.TrainerID
                         LEFT JOIN ShopEmployeeProfile sep ON u.UserID = sep.ShopEmployeeID
                         LEFT JOIN AdminProfile ap ON u.UserID = ap.AdminID
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
}
?> 