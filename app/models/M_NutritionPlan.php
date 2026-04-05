<?php
class M_NutritionPlan {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get all nutrition plans created by a trainer
    public function getNutritionPlansByTrainer($trainerId) {
        $this->db->query('SELECT 
            np.*,
            u.name as player_name,
            u.email as player_email
            FROM NutritionPlan np 
            LEFT JOIN User u ON np.PlayerID = u.UserID 
            WHERE np.TrainerID = :trainer_id 
            ORDER BY np.CreatedDate DESC');
        
        $this->db->bind(':trainer_id', $trainerId);
        
        return $this->db->resultSet();
    }
    
    // Add new nutrition plan
    public function addNutritionPlan($data) {
        $this->db->query('INSERT INTO NutritionPlan (
            TrainerID, 
            PlayerID, 
            DietDetails, 
            Duration, 
            CreatedDate, 
            Status
        ) VALUES (
            :trainer_id, 
            :player_id, 
            :diet_details, 
            :duration, 
            CURDATE(), 
            :status
        )');
        
        // Bind values
        $this->db->bind(':trainer_id', $data['trainer_id']);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':diet_details', $data['diet_details']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Update nutrition plan
    public function updateNutritionPlan($planId, $data) {
        $this->db->query('UPDATE NutritionPlan SET 
            PlayerID = :player_id,
            DietDetails = :diet_details,
            Duration = :duration,
            Status = :status
            WHERE PlanID = :plan_id');
        
        // Bind values
        $this->db->bind(':plan_id', $planId);
        $this->db->bind(':player_id', $data['player_id']);
        $this->db->bind(':diet_details', $data['diet_details']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Delete nutrition plan
    public function deleteNutritionPlan($planId) {
        $this->db->query('DELETE FROM NutritionPlan WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->execute();
    }
    
    // Get single nutrition plan
    public function getNutritionPlan($planId) {
        $this->db->query('SELECT 
            np.*,
            u.name as player_name,
            u.email as player_email
            FROM NutritionPlan np 
            LEFT JOIN User u ON np.PlayerID = u.UserID 
            WHERE np.PlanID = :plan_id');
        
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->single();
    }
    
    // Get all active players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT u.UserID, u.name, u.email, COALESCE(pp.SubscriptionType, "basic") AS group_key
            FROM User u
            LEFT JOIN PlayerProfile pp ON pp.PlayerID = u.UserID
            WHERE u.Role = "Player" AND u.Status = "active"
            ORDER BY u.name');
        
        return $this->db->resultSet();
    }

    public function getPlayerGroups() {
        return [
            (object)['key' => 'all', 'label' => 'All Players', 'description' => 'Assign the plan to every active player.'],
            (object)['key' => 'under_13', 'label' => 'Under 13 Players', 'description' => 'Players younger than 13 years old.'],
            (object)['key' => 'under_15', 'label' => 'Under 15 Players', 'description' => 'Players younger than 15 years old.'],
            (object)['key' => 'under_19', 'label' => 'Under 19 Players', 'description' => 'Players younger than 19 years old.'],
            (object)['key' => 'under_21', 'label' => 'Under 21 Players', 'description' => 'Players younger than 21 years old.'],
            (object)['key' => 'tournament', 'label' => 'Tournament Players', 'description' => 'Players who have been selected for a tournament squad.'],
        ];
    }

    public function getPlayerIdsByGroup($groupKey) {
        $groupKey = strtolower(trim((string)$groupKey));

        if ($groupKey === 'all') {
            $this->db->query('SELECT u.UserID
                FROM User u
                WHERE u.Role = "Player" AND u.Status = "active"
                ORDER BY u.name');
            $rows = $this->db->resultSet();
        } elseif (in_array($groupKey, ['under_13', 'under_15', 'under_19', 'under_21'], true)) {
            $ageLimit = (int)str_replace('under_', '', $groupKey);
            $this->db->query('SELECT u.UserID
                FROM User u
                WHERE u.Role = "Player"
                  AND u.Status = "active"
                  AND u.DateOfBirth IS NOT NULL
                  AND TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < :age_limit
                ORDER BY u.name');
            $this->db->bind(':age_limit', $ageLimit);
            $rows = $this->db->resultSet();
        } elseif ($groupKey === 'tournament') {
            $this->db->query('SELECT DISTINCT u.UserID
                FROM tournamentplayer tp
                INNER JOIN User u ON u.UserID = tp.PlayerID
                WHERE u.Role = "Player" AND u.Status = "active"
                ORDER BY u.name');
            $rows = $this->db->resultSet();
        } else {
            $this->db->query('SELECT u.UserID
                FROM User u
                WHERE 1 = 0');
            $rows = $this->db->resultSet();
        }

        return array_map(static function ($row) {
            return (int)$row->UserID;
        }, $rows ?: []);
    }

    // Create a new nutrition plan (dedicated page form — includes PlanName and explicit CreatedDate)
    public function createPlan($data) {
        $playerIds = $data['player_ids'] ?? [];
        if (!is_array($playerIds)) {
            $playerIds = [];
        }
        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));

        $columns = ['TrainerID', 'DietDetails', 'Duration', 'CreatedDate'];
        $placeholders = [':trainer_id', ':diet_details', ':duration', ':created_date'];
        $bindings = [
            ':trainer_id' => (int)$data['trainer_id'],
            ':diet_details' => $data['diet_details'],
            ':duration' => (int)$data['duration'],
            ':created_date' => $data['created_date'],
        ];

        if ($this->columnExists('NutritionPlan', 'PlanName')) {
            $columns[] = 'PlanName';
            $placeholders[] = ':plan_name';
            $bindings[':plan_name'] = $data['plan_name'];
        } elseif ($this->columnExists('NutritionPlan', 'nutritionPlanName')) {
            $columns[] = 'nutritionPlanName';
            $placeholders[] = ':plan_name';
            $bindings[':plan_name'] = $data['plan_name'];
        }

        if ($this->columnExists('NutritionPlan', 'PlayerID') && !empty($playerIds)) {
            $columns[] = 'PlayerID';
            $placeholders[] = ':player_id';
            $bindings[':player_id'] = $playerIds[0];
        }

        if ($this->columnExists('NutritionPlan', 'Status')) {
            $columns[] = 'Status';
            $placeholders[] = ':status';
            $bindings[':status'] = $data['status'];
        }

        $this->db->query('INSERT INTO NutritionPlan (' . implode(', ', $columns) . ')
            VALUES (' . implode(', ', $placeholders) . ')');

        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        if (!$this->db->execute()) {
            return false;
        }

        $planId = (int)$this->db->lastInsertId();
        if ($planId <= 0) {
            return false;
        }

        if (!$this->assignPlayersToPlan($planId, $playerIds, $data['created_date'] ?? null)) {
            return false;
        }

        return $planId;
    }

    private function assignPlayersToPlan($planId, array $playerIds, $assignedDate = null) {
        if (!$this->tableExists('nutritionplan_player')) {
            return true;
        }

        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
        if (empty($playerIds)) {
            return false;
        }

        $assignedDate = $assignedDate ?: date('Y-m-d');
        foreach ($playerIds as $playerId) {
            $this->db->query('INSERT INTO nutritionplan_player (PlanID, PlayerID, AssignedDate)
                VALUES (:plan_id, :player_id, :assigned_date)');
            $this->db->bind(':plan_id', $planId);
            $this->db->bind(':player_id', $playerId);
            $this->db->bind(':assigned_date', $assignedDate);
            if (!$this->db->execute()) {
                return false;
            }
        }

        return true;
    }

    private function tableExists($tableName) {
        $this->db->query('SHOW TABLES LIKE :table_name');
        $this->db->bind(':table_name', $tableName);
        return (bool)$this->db->single();
    }

    private function columnExists($tableName, $columnName) {
        $this->db->query('SHOW COLUMNS FROM ' . $tableName . ' LIKE :column_name');
        $this->db->bind(':column_name', $columnName);
        return (bool)$this->db->single();
    }

    // ── CRUD helpers used by the Nutrition controller ─────────────

    // Return all plans for a trainer (with player name)
    public function getAllPlans($trainerId) {
        $this->db->query('SELECT
            np.*,
            u.name  AS player_name,
            u.email AS player_email,
            COALESCE(a.assignment_count, 0) AS assigned_player_count,
            COALESCE(a.assigned_player_names, "") AS assigned_player_names,
            COALESCE(a.assigned_player_emails, "") AS assigned_player_emails
            FROM NutritionPlan np
            LEFT JOIN User u ON np.PlayerID = u.UserID
            LEFT JOIN (
                SELECT
                    npp.PlanID,
                    COUNT(DISTINCT npp.PlayerID) AS assignment_count,
                    GROUP_CONCAT(DISTINCT assigned_u.name ORDER BY assigned_u.name SEPARATOR ", ") AS assigned_player_names,
                    GROUP_CONCAT(DISTINCT assigned_u.email ORDER BY assigned_u.name SEPARATOR ", ") AS assigned_player_emails
                FROM nutritionplan_player npp
                INNER JOIN User assigned_u ON npp.PlayerID = assigned_u.UserID
                GROUP BY npp.PlanID
            ) a ON a.PlanID = np.PlanID
            WHERE np.TrainerID = :trainer_id
            ORDER BY np.CreatedDate DESC');
        $this->db->bind(':trainer_id', (int)$trainerId);
        return $this->db->resultSet();
    }

    // Return a single plan — only if it belongs to the given trainer (ownership check)
    public function getPlanById($planId, $trainerId) {
        $this->db->query('SELECT
            np.*,
            u.name  AS player_name,
            u.email AS player_email
            FROM NutritionPlan np
            LEFT JOIN User u ON np.PlayerID = u.UserID
            WHERE np.PlanID    = :plan_id
              AND np.TrainerID = :trainer_id
            LIMIT 1');
        $this->db->bind(':plan_id',    (int)$planId);
        $this->db->bind(':trainer_id', (int)$trainerId);
        return $this->db->single();
    }

    // Update all editable fields of a plan
    public function updatePlan($data) {
        $this->db->query('UPDATE NutritionPlan SET
            PlayerID     = :player_id,
            PlanName     = :plan_name,
            DietDetails  = :diet_details,
            Duration     = :duration,
            CreatedDate  = :created_date,
            Status       = :status
            WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id',     (int)$data['plan_id']);
        $this->db->bind(':player_id',   (int)$data['player_id']);
        $this->db->bind(':plan_name',   $data['plan_name']);
        $this->db->bind(':diet_details',$data['diet_details']);
        $this->db->bind(':duration',    (int)$data['duration']);
        $this->db->bind(':created_date',$data['created_date']);
        $this->db->bind(':status',      $data['status']);
        return $this->db->execute();
    }

    // Delete a plan — only if it belongs to the given trainer
    public function deletePlan($planId, $trainerId) {
        $this->db->query('DELETE FROM NutritionPlan
            WHERE PlanID    = :plan_id
              AND TrainerID = :trainer_id');
        $this->db->bind(':plan_id',    (int)$planId);
        $this->db->bind(':trainer_id', (int)$trainerId);
        return $this->db->execute();
    }

    // Get nutrition plans assigned to a player
    public function getNutritionPlansByPlayer($playerId) {
        $this->db->query('SELECT np.*, u.Name AS trainer_name 
            FROM nutritionplan np 
            JOIN nutritionplan_player npp ON np.PlanID = npp.PlanID
            JOIN user u ON np.TrainerID = u.UserID 
            WHERE npp.PlayerID = :player_id 
            ORDER BY np.CreatedDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }
}
?>