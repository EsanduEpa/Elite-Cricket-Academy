<?php

class M_Users {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Register user with extended fields
    public function register($data) {
        $this->db->query('INSERT INTO users (full_name, date_of_birth, address, email, contact_number, school, username, password, created_at) VALUES(:full_name, :date_of_birth, :address, :email, :contact_number, :school, :username, :password, NOW())');
        
        // Bind values
        $this->db->bind(':full_name', $data['fullName']);
        $this->db->bind(':date_of_birth', $data['dateOfBirth']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':contact_number', $data['contactNumber']);
        $this->db->bind(':school', $data['school']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login user
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email OR username = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }

        return false;
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
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
        $this->db->query('SELECT * FROM users WHERE username = :username');
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
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    // Get total users count
    public function getTotalUsers() {
        $this->db->query('SELECT COUNT(*) as count FROM users');
        $result = $this->db->single();
        
        return $result ? $result->count : 25; // Return dummy data if no database
    }

    // Get total users by type (coach, player, trainer, staff)
    public function getTotalUsersByType($type) {
        $this->db->query('SELECT COUNT(*) as count FROM users WHERE user_type = :type');
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
        $this->db->query('SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()');
        $result = $this->db->single();
        
        return $result ? $result->count : 3; // Return dummy data if no database
    }

    // Get recent activities
    public function getRecentActivities($limit = 10) {
        $this->db->query('SELECT ua.*, u.full_name as user_name 
                         FROM user_activities ua 
                         LEFT JOIN users u ON ua.user_id = u.id 
                         ORDER BY ua.created_at DESC 
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
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }

    // Update user profile
    public function updateUser($data) {
        $this->db->query('UPDATE users SET 
                         full_name = :full_name,
                         email = :email,
                         contact_number = :contact_number,
                         address = :address,
                         user_type = :user_type,
                         status = :status
                         WHERE id = :id');

        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':contact_number', $data['contact_number']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':user_type', $data['user_type']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Suspend user
    public function suspendUser($id, $duration) {
        $suspend_until = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
        
        $this->db->query('UPDATE users SET 
                         status = "suspended",
                         suspended_until = :suspended_until
                         WHERE id = :id');

        $this->db->bind(':id', $id);
        $this->db->bind(':suspended_until', $suspend_until);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Activate user
    public function activateUser($id) {
        $this->db->query('UPDATE users SET 
                         status = "active",
                         suspended_until = NULL
                         WHERE id = :id');

        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?> 