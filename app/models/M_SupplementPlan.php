<?php
class M_SupplementPlan {
    private $db;
    private $supplementTable;
    private $userTable;
    
    public function __construct() {
        $this->db = new Database;
        $this->supplementTable = $this->resolveExistingTableName(['SupplementPlan', 'supplementplan']) ?? 'SupplementPlan';
        $this->userTable = $this->resolveExistingTableName(['User', 'user']) ?? 'User';
        $this->ensureSupplementCrudColumns();
    }

    public function getSupplementPlansByTrainer($trainerId) {
        return $this->getAllPlans($trainerId);
    }

    public function getAllPlans($trainerId) {
        $supplementTable = $this->supplementTable;
        $userTable = $this->userTable;

        if ($this->tableExists('supplement_player')) {
            $this->db->query('SELECT 
                sp.*, 
                COALESCE(a.assignment_count, 0) AS assigned_player_count,
                COALESCE(a.assigned_player_ids, "") AS assigned_player_ids,
                COALESCE(a.assigned_player_names, "") AS assigned_player_names,
                COALESCE(a.assigned_player_emails, "") AS assigned_player_emails
                FROM ' . $supplementTable . ' sp
                LEFT JOIN (
                    SELECT
                        spl.PlanID,
                        COUNT(DISTINCT spl.PlayerID) AS assignment_count,
                        GROUP_CONCAT(DISTINCT spl.PlayerID ORDER BY assigned_u.FirstName SEPARATOR ",") AS assigned_player_ids,
                        GROUP_CONCAT(DISTINCT CONCAT(assigned_u.FirstName, \' \', assigned_u.LastName) ORDER BY assigned_u.FirstName SEPARATOR ", ") AS assigned_player_names,
                        GROUP_CONCAT(DISTINCT assigned_u.email ORDER BY assigned_u.FirstName SEPARATOR ", ") AS assigned_player_emails
                    FROM supplement_player spl
                    INNER JOIN ' . $userTable . ' assigned_u ON spl.PlayerID = assigned_u.UserID
                    GROUP BY spl.PlanID
                ) a ON a.PlanID = sp.PlanID
                WHERE sp.TrainerID = :trainer_id
                ORDER BY sp.CreatedDate DESC');
        } else {
            $this->db->query('SELECT 
                sp.*, 
                CONCAT(u.FirstName, \' \', u.LastName) as player_name,
                u.email as player_email,
                CASE WHEN sp.PlayerID IS NULL OR sp.PlayerID = 0 THEN 0 ELSE 1 END AS assigned_player_count,
                COALESCE(CONCAT(u.FirstName, \' \', u.LastName), "") AS assigned_player_names,
                COALESCE(u.email, "") AS assigned_player_emails
                FROM ' . $supplementTable . ' sp 
                LEFT JOIN ' . $userTable . ' u ON sp.PlayerID = u.UserID 
                WHERE sp.TrainerID = :trainer_id 
                ORDER BY sp.CreatedDate DESC');
        }

        $this->db->bind(':trainer_id', (int)$trainerId);
        return $this->db->resultSet();
    }

    public function createPlan(array $data) {
        $playerIds = $this->normalisePlayerIds($data);
        $trainerId = (int)($data['trainer_id'] ?? 0);

        if (!$this->ensureSupplementForeignKeyProfiles($trainerId, $playerIds)) {
            return false;
        }

        $columns = ['TrainerID', 'SupplementDetails', 'Dosage', 'Duration', 'CreatedDate'];
        $placeholders = [':trainer_id', ':supplement_details', ':dosage', ':duration', ':created_date'];
        $bindings = [
            ':trainer_id' => $trainerId,
            ':supplement_details' => $data['supplement_details'] ?? '',
            ':dosage' => $data['dosage'] ?? '',
            ':duration' => (int)($data['duration'] ?? 0),
            ':created_date' => $data['created_date'] ?? date('Y-m-d'),
        ];

        if ($this->columnExists($this->supplementTable, 'SupplementPlanName')) {
            $columns[] = 'SupplementPlanName';
            $placeholders[] = ':supplement_plan_name';
            $bindings[':supplement_plan_name'] = $data['supplement_plan_name'] ?? '';
        }

        if ($this->columnExists($this->supplementTable, 'Notes')) {
            $columns[] = 'Notes';
            $placeholders[] = ':notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        } elseif ($this->columnExists($this->supplementTable, 'notes')) {
            $columns[] = 'notes';
            $placeholders[] = ':notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        }

        if ($this->columnExists($this->supplementTable, 'Status')) {
            $columns[] = 'Status';
            $placeholders[] = ':status';
            $bindings[':status'] = $data['status'] ?? 'active';
        } elseif ($this->columnExists($this->supplementTable, 'status')) {
            $columns[] = 'status';
            $placeholders[] = ':status';
            $bindings[':status'] = $data['status'] ?? 'active';
        }

        if ($this->columnExists($this->supplementTable, 'PlayerID') && !empty($playerIds)) {
            $columns[] = 'PlayerID';
            $placeholders[] = ':player_id';
            $bindings[':player_id'] = $playerIds[0];
        }

        $this->db->query('INSERT INTO ' . $this->supplementTable . ' (' . implode(', ', $columns) . ')
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

    public function addSupplementPlan($data) {
        return $this->createPlan($data);
    }

    public function getPlanById($planId, $trainerId = null) {
        $supplementTable = $this->supplementTable;
        $userTable = $this->userTable;

        if ($this->tableExists('supplement_player')) {
            $query = 'SELECT 
                sp.*, 
                COALESCE(a.assignment_count, 0) AS assigned_player_count,
                COALESCE(a.assigned_player_ids, "") AS assigned_player_ids,
                COALESCE(a.assigned_player_names, "") AS assigned_player_names,
                COALESCE(a.assigned_player_emails, "") AS assigned_player_emails
                FROM ' . $supplementTable . ' sp
                LEFT JOIN (
                    SELECT
                        spl.PlanID,
                        COUNT(DISTINCT spl.PlayerID) AS assignment_count,
                        GROUP_CONCAT(DISTINCT spl.PlayerID ORDER BY assigned_u.FirstName SEPARATOR ",") AS assigned_player_ids,
                        GROUP_CONCAT(DISTINCT CONCAT(assigned_u.FirstName, \' \', assigned_u.LastName) ORDER BY assigned_u.FirstName SEPARATOR ", ") AS assigned_player_names,
                        GROUP_CONCAT(DISTINCT assigned_u.email ORDER BY assigned_u.FirstName SEPARATOR ", ") AS assigned_player_emails
                    FROM supplement_player spl
                    INNER JOIN ' . $userTable . ' assigned_u ON spl.PlayerID = assigned_u.UserID
                    GROUP BY spl.PlanID
                ) a ON a.PlanID = sp.PlanID
                WHERE sp.PlanID = :plan_id';
        } else {
            $query = 'SELECT 
                sp.*, 
                CONCAT(u.FirstName, \' \', u.LastName) as player_name,
                u.email as player_email
                FROM ' . $supplementTable . ' sp 
                LEFT JOIN ' . $userTable . ' u ON sp.PlayerID = u.UserID 
                WHERE sp.PlanID = :plan_id';
        }

        if ($trainerId !== null) {
            $query .= ' AND sp.TrainerID = :trainer_id';
        }

        $this->db->query($query);
        $this->db->bind(':plan_id', (int)$planId);
        if ($trainerId !== null) {
            $this->db->bind(':trainer_id', (int)$trainerId);
        }

        return $this->db->single();
    }

    public function getSupplementPlan($planId) {
        return $this->getPlanById($planId);
    }

    public function updateSupplementPlan($planId, $data) {
        $data['plan_id'] = $planId;
        return $this->updatePlan($data);
    }

    public function updatePlan(array $data) {
        $planId = (int)($data['plan_id'] ?? 0);
        if ($planId <= 0) {
            return false;
        }

        $playerIds = $this->normalisePlayerIds($data);
        $playerIdPrimary = !empty($playerIds) ? $playerIds[0] : (int)($data['player_id'] ?? 0);

        $sets = [];
        $bindings = [':plan_id' => $planId];

        if ($this->columnExists($this->supplementTable, 'PlayerID')) {
            $sets[] = 'PlayerID = :player_id';
            $bindings[':player_id'] = $playerIdPrimary;
        }

        if ($this->columnExists($this->supplementTable, 'SupplementPlanName')) {
            $sets[] = 'SupplementPlanName = :supplement_plan_name';
            $bindings[':supplement_plan_name'] = $data['supplement_plan_name'] ?? '';
        }

        if ($this->columnExists($this->supplementTable, 'SupplementDetails')) {
            $sets[] = 'SupplementDetails = :supplement_details';
            $bindings[':supplement_details'] = $data['supplement_details'] ?? '';
        }

        if ($this->columnExists($this->supplementTable, 'Notes')) {
            $sets[] = 'Notes = :notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        } elseif ($this->columnExists($this->supplementTable, 'notes')) {
            $sets[] = 'notes = :notes';
            $bindings[':notes'] = $data['notes'] ?? null;
        }

        if ($this->columnExists($this->supplementTable, 'Dosage')) {
            $sets[] = 'Dosage = :dosage';
            $bindings[':dosage'] = $data['dosage'] ?? '';
        }

        if ($this->columnExists($this->supplementTable, 'Duration')) {
            $sets[] = 'Duration = :duration';
            $bindings[':duration'] = (int)($data['duration'] ?? 0);
        }

        if ($this->columnExists($this->supplementTable, 'CreatedDate')) {
            $sets[] = 'CreatedDate = :created_date';
            $bindings[':created_date'] = $data['created_date'] ?? date('Y-m-d');
        }

        if ($this->columnExists($this->supplementTable, 'Status')) {
            $sets[] = 'Status = :status';
            $bindings[':status'] = $data['status'] ?? 'active';
        } elseif ($this->columnExists($this->supplementTable, 'status')) {
            $sets[] = 'status = :status';
            $bindings[':status'] = $data['status'] ?? 'active';
        }

        if (empty($sets)) {
            return false;
        }

        $this->db->query('UPDATE ' . $this->supplementTable . ' SET ' . implode(', ', $sets) . ' WHERE PlanID = :plan_id');
        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        if (!$this->db->execute()) {
            return false;
        }

        if ($this->tableExists('supplement_player')) {
            if (!$this->ensurePlayerProfiles($playerIds)) {
                return false;
            }

            if (!$this->replaceAssignedPlayers($planId, $playerIds, $data['created_date'] ?? null)) {
                return false;
            }
        }

        return true;
    }

    public function deleteSupplementPlan($planId) {
        return $this->deletePlan($planId);
    }

    public function deletePlan($planId, $trainerId = null) {
        $query = 'DELETE FROM ' . $this->supplementTable . ' WHERE PlanID = :plan_id';
        if ($trainerId !== null) {
            $query .= ' AND TrainerID = :trainer_id';
        }

        $this->db->query($query);
        $this->db->bind(':plan_id', (int)$planId);
        if ($trainerId !== null) {
            $this->db->bind(':trainer_id', (int)$trainerId);
        }

        return $this->db->execute();
    }

    public function getAllPlayers() {
        $this->db->query('SELECT UserID, name, email FROM ' . $this->userTable . ' WHERE Role = "Player" AND Status = "active" ORDER BY name');
        return $this->db->resultSet();
    }

    public function getPlayerGroups() {
        return [
            (object)['key' => 'all', 'label' => 'All Players', 'description' => 'Assign the plan to every active player.'],
            (object)['key' => 'under_13', 'label' => 'Under 13 Players', 'description' => 'Players younger than 13 years old.'],
            (object)['key' => 'under_15', 'label' => 'Under 15 Players', 'description' => 'Players younger than 15 years old.'],
            (object)['key' => 'under_19', 'label' => 'Under 19 Players', 'description' => 'Players 19 years old or younger.'],
            (object)['key' => 'under_21', 'label' => 'Under 21 Players', 'description' => 'Players 21 years old or younger.'],
            (object)['key' => 'tournament', 'label' => 'Tournament Players', 'description' => 'Players selected for a tournament squad.'],
        ];
    }

    public function getPlayerIdsByGroup($groupKey) {
        $groupKey = strtolower(trim((string)$groupKey));

        if ($groupKey === 'all') {
            $this->db->query('SELECT UserID FROM ' . $this->userTable . ' WHERE Role = "Player" AND Status = "active" ORDER BY FirstName');
            $rows = $this->db->resultSet();
        } elseif (in_array($groupKey, ['under_13', 'under_15', 'under_19', 'under_21'], true)) {
            $ageLimit = (int)str_replace('under_', '', $groupKey);
            $this->db->query('SELECT UserID
                FROM ' . $this->userTable . '
                WHERE Role = "Player"
                  AND Status = "active"
                  AND DateOfBirth IS NOT NULL
                  AND TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) < :age_limit
                ORDER BY FirstName');
            $this->db->bind(':age_limit', $ageLimit);
            $rows = $this->db->resultSet();
        } elseif ($groupKey === 'tournament') {
            $this->db->query('SELECT DISTINCT u.UserID
                FROM tournamentplayer tp
                INNER JOIN ' . $this->userTable . ' u ON u.UserID = tp.PlayerID
                WHERE u.Role = "Player" AND u.Status = "active"
                ORDER BY u.FirstName');
            $rows = $this->db->resultSet();
        } else {
            $this->db->query('SELECT UserID FROM User WHERE 1 = 0');
            $rows = $this->db->resultSet();
        }

        return array_map(static function ($row) {
            return (int)$row->UserID;
        }, $rows ?: []);
    }

    public function getAssignedPlayerIds($planId): array {
        if (!$this->tableExists('supplement_player')) {
            return [];
        }

        $this->db->query('SELECT PlayerID FROM supplement_player WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', (int)$planId);
        $rows = $this->db->resultSet();
        if (!$rows) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(static function ($row) {
            return isset($row->PlayerID) ? (int)$row->PlayerID : 0;
        }, $rows), static fn($id) => $id > 0)));
    }

    public function getSupplementPlansByPlayer($playerId) {
        $this->db->query('SELECT sp.*, CONCAT(u.FirstName, \' \', u.LastName) AS trainer_name 
            FROM supplementplan sp 
            JOIN supplement_player spp ON sp.PlanID = spp.PlanID
            JOIN user u ON sp.TrainerID = u.UserID 
            WHERE spp.PlayerID = :player_id 
            ORDER BY sp.CreatedDate DESC');
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    private function normalisePlayerIds(array $data): array {
        $playerIds = $data['player_ids'] ?? [];
        if (!is_array($playerIds)) {
            $playerIds = [];
        }

        if (empty($playerIds) && !empty($data['player_id'])) {
            $playerIds = [(int)$data['player_id']];
        }

        if (empty($playerIds) && !empty($data['player_group']) && ($data['assignment_mode'] ?? 'individual') === 'group') {
            $playerIds = $this->getPlayerIdsByGroup($data['player_group']);
        }

        return array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
    }

    private function assignPlayersToPlan($planId, array $playerIds, $assignedDate = null) {
        if (!$this->tableExists('supplement_player')) {
            return true;
        }

        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
        if (empty($playerIds)) {
            return false;
        }

        $assignedDate = $assignedDate ?: date('Y-m-d');
        foreach ($playerIds as $playerId) {
            $this->db->query('INSERT INTO supplement_player (PlanID, PlayerID, AssignedDate)
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

    private function replaceAssignedPlayers($planId, array $playerIds, $assignedDate = null): bool {
        $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
        if (empty($playerIds)) {
            return false;
        }

        $this->db->query('DELETE FROM supplement_player WHERE PlanID = :plan_id');
        $this->db->bind(':plan_id', (int)$planId);
        if (!$this->db->execute()) {
            return false;
        }

        return $this->assignPlayersToPlan($planId, $playerIds, $assignedDate);
    }

    private function tableExists($tableName) {
        $this->db->query('SHOW TABLES LIKE :table_name');
        $this->db->bind(':table_name', $tableName);
        return (bool)$this->db->single();
    }

    private function resolveExistingTableName(array $candidates): ?string {
        foreach ($candidates as $candidate) {
            if ($this->tableExists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function ensureSupplementCrudColumns(): void {
        $table = $this->supplementTable;
        if (!$this->tableExists($table)) {
            return;
        }

        if (!$this->columnExists($table, 'Notes') && !$this->columnExists($table, 'notes')) {
            $this->db->query('ALTER TABLE ' . $table . ' ADD COLUMN Notes TEXT NULL AFTER SupplementDetails');
            $this->db->execute();
        }

        if (!$this->columnExists($table, 'Status') && !$this->columnExists($table, 'status')) {
            $this->db->query("ALTER TABLE {$table} ADD COLUMN Status ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER Duration");
            $this->db->execute();
        }
    }

    private function ensureSupplementForeignKeyProfiles(int $trainerId, array $playerIds): bool {
        if (!$this->ensureTrainerProfile($trainerId)) {
            return false;
        }

        return $this->ensurePlayerProfiles($playerIds);
    }

    private function ensureTrainerProfile(int $trainerId): bool {
        if ($trainerId <= 0) {
            return false;
        }

        if (!$this->tableExists('trainerprofile')) {
            return true;
        }

        $this->db->query('INSERT INTO trainerprofile (TrainerID)
            SELECT :trainer_id
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1 FROM trainerprofile WHERE TrainerID = :trainer_id_check
            )');
        $this->db->bind(':trainer_id', $trainerId);
        $this->db->bind(':trainer_id_check', $trainerId);
        return $this->db->execute();
    }

    private function ensurePlayerProfiles(array $playerIds): bool {
        if (empty($playerIds)) {
            return true;
        }

        if (!$this->tableExists('playerprofile')) {
            return true;
        }

        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            if ($playerId <= 0) {
                continue;
            }

            $this->db->query('INSERT INTO playerprofile (PlayerID)
                SELECT :player_id
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM playerprofile WHERE PlayerID = :player_id_check
                )');
            $this->db->bind(':player_id', $playerId);
            $this->db->bind(':player_id_check', $playerId);
            if (!$this->db->execute()) {
                return false;
            }
        }

        return true;
    }

    private function columnExists($tableName, $columnName) {
        $this->db->query('SHOW COLUMNS FROM ' . $tableName . ' LIKE :column_name');
        $this->db->bind(':column_name', $columnName);
        return (bool)$this->db->single();
    }
}
?>