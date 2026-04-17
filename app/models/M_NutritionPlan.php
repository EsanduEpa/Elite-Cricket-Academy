<?php
class M_NutritionPlan {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    private function normalizeTemplateName(string $value): string {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value);
        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    private function nutritionTemplateFallbacks(): array {
        return [
            (object)[
                'TemplateID' => 1,
                'PlanName' => 'High Protein',
                'ProteinPercentage' => 45,
                'CarbohydratePercentage' => 35,
                'FatPercentage' => 20,
                'RecommendedCalories' => 2400,
                'Description' => 'Supports muscle repair and strength development with a protein-forward meal balance.',
                'SortOrder' => 1,
                'IsActive' => 1,
            ],
            (object)[
                'TemplateID' => 2,
                'PlanName' => 'Low Carb',
                'ProteinPercentage' => 40,
                'CarbohydratePercentage' => 25,
                'FatPercentage' => 35,
                'RecommendedCalories' => 2200,
                'Description' => 'Reduces carbohydrate load while keeping protein high for satiety and recovery.',
                'SortOrder' => 2,
                'IsActive' => 1,
            ],
            (object)[
                'TemplateID' => 3,
                'PlanName' => 'Balanced Diet',
                'ProteinPercentage' => 30,
                'CarbohydratePercentage' => 40,
                'FatPercentage' => 30,
                'RecommendedCalories' => 2300,
                'Description' => 'A balanced everyday plan for training consistency and general performance.',
                'SortOrder' => 3,
                'IsActive' => 1,
            ],
            (object)[
                'TemplateID' => 4,
                'PlanName' => 'Weight Loss / Lean',
                'ProteinPercentage' => 40,
                'CarbohydratePercentage' => 30,
                'FatPercentage' => 30,
                'RecommendedCalories' => 1900,
                'Description' => 'Uses controlled calories with higher protein to protect lean mass.',
                'SortOrder' => 4,
                'IsActive' => 1,
            ],
            (object)[
                'TemplateID' => 5,
                'PlanName' => 'Recovery',
                'ProteinPercentage' => 35,
                'CarbohydratePercentage' => 45,
                'FatPercentage' => 20,
                'RecommendedCalories' => 2500,
                'Description' => 'Prioritises glycogen replenishment and recovery nutrition after training or matches.',
                'SortOrder' => 5,
                'IsActive' => 1,
            ],
            (object)[
                'TemplateID' => 6,
                'PlanName' => 'Hydration & Light Nutrition',
                'ProteinPercentage' => 25,
                'CarbohydratePercentage' => 50,
                'FatPercentage' => 25,
                'RecommendedCalories' => 2000,
                'Description' => 'Keeps meals light and easy to digest while maintaining hydration and energy.',
                'SortOrder' => 6,
                'IsActive' => 1,
            ],
        ];
    }

    public function getNutritionTemplates($activeOnly = true) {
        if (!$this->tableExists('nutrition_plan_templates')) {
            return $this->nutritionTemplateFallbacks();
        }

        $sql = 'SELECT TemplateID, PlanName, ProteinPercentage, CarbohydratePercentage, FatPercentage, RecommendedCalories, Description, SortOrder, IsActive
                FROM nutrition_plan_templates';
        if ($activeOnly) {
            $sql .= ' WHERE IsActive = 1';
        }
        $sql .= ' ORDER BY SortOrder ASC, PlanName ASC';

        $this->db->query($sql);
        $rows = $this->db->resultSet();
        return !empty($rows) ? $rows : $this->nutritionTemplateFallbacks();
    }

    public function getNutritionTemplateById($templateId) {
        $templateId = (int)$templateId;
        if ($templateId <= 0) {
            return null;
        }

        if (!$this->tableExists('nutrition_plan_templates')) {
            foreach ($this->nutritionTemplateFallbacks() as $template) {
                if ((int)$template->TemplateID === $templateId) {
                    return $template;
                }
            }
            return null;
        }

        $this->db->query('SELECT TemplateID, PlanName, ProteinPercentage, CarbohydratePercentage, FatPercentage, RecommendedCalories, Description, SortOrder, IsActive
            FROM nutrition_plan_templates
            WHERE TemplateID = :template_id
            LIMIT 1');
        $this->db->bind(':template_id', $templateId);
        return $this->db->single();
    }

    public function getNutritionTemplateByName($planName) {
        $planName = trim((string)$planName);
        if ($planName === '') {
            return null;
        }

        $normalizedPlanName = $this->normalizeTemplateName($planName);

        if (!$this->tableExists('nutrition_plan_templates')) {
            foreach ($this->nutritionTemplateFallbacks() as $template) {
                $templateName = (string)($template->PlanName ?? '');
                if (strcasecmp($templateName, $planName) === 0 || $this->normalizeTemplateName($templateName) === $normalizedPlanName) {
                    return $template;
                }
            }
            return null;
        }

        $this->db->query('SELECT TemplateID, PlanName, ProteinPercentage, CarbohydratePercentage, FatPercentage, RecommendedCalories, Description, SortOrder, IsActive
            FROM nutrition_plan_templates
            WHERE PlanName = :plan_name
            LIMIT 1');
        $this->db->bind(':plan_name', $planName);
        $exact = $this->db->single();
        if ($exact) {
            return $exact;
        }

        foreach ($this->getNutritionTemplates(false) as $template) {
            $templateName = (string)($template->PlanName ?? '');
            if ($this->normalizeTemplateName($templateName) === $normalizedPlanName) {
                return $template;
            }
        }

        return null;
    }

    private function appendNutritionStructuredColumns(array $data, array &$columns, array &$placeholders, array &$bindings): void {
        if ($this->columnExists('NutritionPlan', 'TemplateID')) {
            $columns[] = 'TemplateID';
            $placeholders[] = ':template_id';
            $bindings[':template_id'] = !empty($data['template_id']) ? (int)$data['template_id'] : null;
        }

        if ($this->columnExists('NutritionPlan', 'ProteinPercentage')) {
            $columns[] = 'ProteinPercentage';
            $placeholders[] = ':protein_percentage';
            $bindings[':protein_percentage'] = $data['protein_percentage'];
        }

        if ($this->columnExists('NutritionPlan', 'CarbohydratePercentage')) {
            $columns[] = 'CarbohydratePercentage';
            $placeholders[] = ':carbohydrate_percentage';
            $bindings[':carbohydrate_percentage'] = $data['carbohydrate_percentage'];
        }

        if ($this->columnExists('NutritionPlan', 'FatPercentage')) {
            $columns[] = 'FatPercentage';
            $placeholders[] = ':fat_percentage';
            $bindings[':fat_percentage'] = $data['fat_percentage'];
        }

        if ($this->columnExists('NutritionPlan', 'RecommendedCalories')) {
            $columns[] = 'RecommendedCalories';
            $placeholders[] = ':recommended_calories';
            $bindings[':recommended_calories'] = $data['recommended_calories'];
        }

        if ($this->columnExists('NutritionPlan', 'Description')) {
            $columns[] = 'Description';
            $placeholders[] = ':description';
            $bindings[':description'] = $data['description'];
        }
    }

    private function appendNutritionStructuredUpdates(array $data, array &$sets, array &$bindings): void {
        if ($this->columnExists('NutritionPlan', 'TemplateID')) {
            $sets[] = 'TemplateID = :template_id';
            $bindings[':template_id'] = !empty($data['template_id']) ? (int)$data['template_id'] : null;
        }

        if ($this->columnExists('NutritionPlan', 'ProteinPercentage')) {
            $sets[] = 'ProteinPercentage = :protein_percentage';
            $bindings[':protein_percentage'] = $data['protein_percentage'];
        }

        if ($this->columnExists('NutritionPlan', 'CarbohydratePercentage')) {
            $sets[] = 'CarbohydratePercentage = :carbohydrate_percentage';
            $bindings[':carbohydrate_percentage'] = $data['carbohydrate_percentage'];
        }

        if ($this->columnExists('NutritionPlan', 'FatPercentage')) {
            $sets[] = 'FatPercentage = :fat_percentage';
            $bindings[':fat_percentage'] = $data['fat_percentage'];
        }

        if ($this->columnExists('NutritionPlan', 'RecommendedCalories')) {
            $sets[] = 'RecommendedCalories = :recommended_calories';
            $bindings[':recommended_calories'] = $data['recommended_calories'];
        }

        if ($this->columnExists('NutritionPlan', 'Description')) {
            $sets[] = 'Description = :description';
            $bindings[':description'] = $data['description'];
        }
    }
    
    // Get all nutrition plans created by a trainer
    public function getNutritionPlansByTrainer($trainerId) {
        $this->db->query('SELECT 
            np.*,
            CONCAT(u.FirstName, \' \', u.LastName) as player_name,
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
            CONCAT(u.FirstName, \' \', u.LastName) as player_name,
            u.email as player_email
            FROM NutritionPlan np 
            LEFT JOIN User u ON np.PlayerID = u.UserID 
            WHERE np.PlanID = :plan_id');
        
        $this->db->bind(':plan_id', $planId);
        
        return $this->db->single();
    }
    
    // Get all active players for dropdown
    public function getAllPlayers() {
        $this->db->query('SELECT u.UserID, CONCAT(u.FirstName, \' \', u.LastName) AS name, u.email, COALESCE(pp.SubscriptionType, "basic") AS group_key
            FROM User u
            LEFT JOIN PlayerProfile pp ON pp.PlayerID = u.UserID
            WHERE u.Role = "Player" AND u.Status = "active"
            ORDER BY u.FirstName');
        
        return $this->db->resultSet();
    }

    public function getPlayerGroups() {
        return [
            (object)['key' => 'all', 'label' => 'All Players', 'description' => 'Assign the plan to every active player.'],
            (object)['key' => 'under_13', 'label' => 'Under 13 Players', 'description' => 'Players younger than 13 years old.'],
            (object)['key' => 'under_15', 'label' => 'Under 15 Players', 'description' => 'Players younger than 15 years old.'],
            (object)['key' => 'under_19', 'label' => 'Under 19 Players', 'description' => 'Players 19 years old or younger.'],
            (object)['key' => 'tournament', 'label' => 'Tournament Players', 'description' => 'Players who have been selected for a tournament squad.'],
        ];
    }

    public function getPlayerIdsByGroup($groupKey) {
        $groupKey = strtolower(trim((string)$groupKey));

        if ($groupKey === 'all') {
            $this->db->query('SELECT u.UserID
                FROM User u
                WHERE u.Role = "Player" AND u.Status = "active"
                ORDER BY u.FirstName');
            $ageLimit = (int)str_replace('under_', '', $groupKey);
            $this->db->query('SELECT u.UserID
                FROM User u
                WHERE u.Role = "Player"
                  AND u.Status = "active"
                  AND u.DateOfBirth IS NOT NULL
                  AND TIMESTAMPDIFF(YEAR, u.DateOfBirth, CURDATE()) < :age_limit
                ORDER BY u.FirstName');
            $this->db->bind(':age_limit', $ageLimit);
            $rows = $this->db->resultSet();
        } elseif ($groupKey === 'tournament') {
            $this->db->query('SELECT DISTINCT u.UserID
                FROM tournamentplayer tp
                INNER JOIN User u ON u.UserID = tp.PlayerID
                WHERE u.Role = "Player" AND u.Status = "active"
                ORDER BY u.FirstName');
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

        $this->appendNutritionStructuredColumns($data, $columns, $placeholders, $bindings);

        if ($this->columnExists('NutritionPlan', 'Notes')) {
            $columns[] = 'Notes';
            $placeholders[] = ':notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        } elseif ($this->columnExists('NutritionPlan', 'notes')) {
            $columns[] = 'notes';
            $placeholders[] = ':notes';
            $bindings[':notes'] = $data['notes'] ?? null;
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

    public function getAssignedPlayerIds($planId): array {
        if (!$this->tableExists('nutritionplan_player')) {
            return [];
        }

        $this->db->query('SELECT PlayerID FROM nutritionplan_player WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', (int)$planId);
        $rows = $this->db->resultSet();
        if (!$rows) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(static function ($row) {
            return isset($row->PlayerID) ? (int)$row->PlayerID : 0;
        }, $rows), static fn($id) => $id > 0)));
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
        if (!$this->tableExists('nutritionplan_player')) {
            $this->db->query('SELECT
                np.*,
                CONCAT(u.FirstName, \' \', u.LastName)  AS player_name,
                u.email AS player_email,
                CASE WHEN np.PlayerID IS NULL OR np.PlayerID = 0 THEN 0 ELSE 1 END AS assigned_player_count,
                COALESCE(CONCAT(u.FirstName, \' \', u.LastName), \"\") AS assigned_player_names,
                COALESCE(u.email, "") AS assigned_player_emails
                FROM NutritionPlan np
                LEFT JOIN User u ON np.PlayerID = u.UserID
                WHERE np.TrainerID = :trainer_id
                ORDER BY np.CreatedDate DESC');
        } else {
            $this->db->query('SELECT
                np.*,
                CONCAT(u.FirstName, \' \', u.LastName)  AS player_name,
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
                        GROUP_CONCAT(DISTINCT CONCAT(assigned_u.FirstName, \' \', assigned_u.LastName) ORDER BY assigned_u.FirstName SEPARATOR ", ") AS assigned_player_names,
                        GROUP_CONCAT(DISTINCT assigned_u.email ORDER BY assigned_u.FirstName SEPARATOR ", ") AS assigned_player_emails
                    FROM nutritionplan_player npp
                    INNER JOIN User assigned_u ON npp.PlayerID = assigned_u.UserID
                    GROUP BY npp.PlanID
                ) a ON a.PlanID = np.PlanID
                WHERE np.TrainerID = :trainer_id
                ORDER BY np.CreatedDate DESC');
        }
        $this->db->bind(':trainer_id', (int)$trainerId);
        return $this->db->resultSet();
    }

    // Return a single plan — only if it belongs to the given trainer (ownership check)
    public function getPlanById($planId, $trainerId) {
        $this->db->query('SELECT
            np.*,
            CONCAT(u.FirstName, \' \', u.LastName)  AS player_name,
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
        $planId = (int)($data['plan_id'] ?? 0);
        if ($planId <= 0) {
            return false;
        }

        $playerIds = $data['player_ids'] ?? [];
        if (!is_array($playerIds)) {
            $playerIds = [];
        }
        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
        $playerIdPrimary = !empty($playerIds) ? (int)$playerIds[0] : (int)($data['player_id'] ?? 0);

        $sets = [];
        $bindings = [':plan_id' => $planId];

        if ($this->columnExists('NutritionPlan', 'PlayerID')) {
            $sets[] = 'PlayerID = :player_id';
            $bindings[':player_id'] = $playerIdPrimary;
        }

        if ($this->columnExists('NutritionPlan', 'PlanName')) {
            $sets[] = 'PlanName = :plan_name';
            $bindings[':plan_name'] = $data['plan_name'] ?? '';
        } elseif ($this->columnExists('NutritionPlan', 'nutritionPlanName')) {
            $sets[] = 'nutritionPlanName = :plan_name';
            $bindings[':plan_name'] = $data['plan_name'] ?? '';
        }

        $this->appendNutritionStructuredUpdates($data, $sets, $bindings);

        if ($this->columnExists('NutritionPlan', 'DietDetails')) {
            $sets[] = 'DietDetails = :diet_details';
            $bindings[':diet_details'] = $data['diet_details'] ?? '';
        }

        if ($this->columnExists('NutritionPlan', 'Notes')) {
            $sets[] = 'Notes = :notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        } elseif ($this->columnExists('NutritionPlan', 'notes')) {
            $sets[] = 'notes = :notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        }

        if ($this->columnExists('NutritionPlan', 'Duration')) {
            $sets[] = 'Duration = :duration';
            $bindings[':duration'] = (int)($data['duration'] ?? 0);
        }

        if ($this->columnExists('NutritionPlan', 'CreatedDate')) {
            $sets[] = 'CreatedDate = :created_date';
            $bindings[':created_date'] = $data['created_date'] ?? date('Y-m-d');
        }

        if ($this->columnExists('NutritionPlan', 'Status')) {
            $sets[] = 'Status = :status';
            $bindings[':status'] = $data['status'] ?? 'active';
        }

        if (empty($sets)) {
            return false;
        }

        $this->db->query('UPDATE NutritionPlan SET ' . implode(', ', $sets) . ' WHERE PlanID = :plan_id');
        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        if (!$this->db->execute()) {
            return false;
        }

        if ($this->tableExists('nutritionplan_player')) {
            if (!$this->replaceAssignedPlayers($planId, $playerIds, $data['created_date'] ?? null)) {
                return false;
            }
        }

        return true;
    }

    private function replaceAssignedPlayers($planId, array $playerIds, $assignedDate = null): bool {
        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
        
        // Delete existing assignments
        $this->db->query('DELETE FROM nutritionplan_player WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', (int)$planId);
        if (!$this->db->execute()) {
            return false;
        }

        // If no players to assign, return success (plan can exist without assigned players)
        if (empty($playerIds)) {
            return true;
        }

        return $this->assignPlayersToPlan($planId, $playerIds, $assignedDate);
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
        $this->db->query('SELECT np.*, CONCAT(u.FirstName, \' \', u.LastName) AS trainer_name 
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
