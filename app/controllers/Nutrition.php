<?php
class Nutrition extends Controller {

    public function __construct() {
        // Dev bypass — mirrors the pattern used in Trainer.php
        if (!isset($_SESSION['user_id'])) {
            // Use an actual Trainer that exists in `trainerprofile` to avoid FK failures.
            $_SESSION['user_id']   = 10;
            $_SESSION['username']  = 'Trainer';
            $_SESSION['user_role'] = 'Trainer';
        }
    }

    private function _predefinedPlans(): array {
        return [
            'High Protein Plan',
            'High Carb (Match Preparation) Plan',
            'Balanced Diet Plan',
            'Weight Loss / Lean Plan',
            'Recovery Plan',
            'Hydration & Light Nutrition Plan',
        ];
    }

    private function _planTemplate(string $planName): string {
        $planName = trim($planName);

        $templates = [
            'High Protein Plan' => "Goal: Support muscle building and strength.\n\nGuidelines:\n- Protein with every meal (lean meats, eggs, dairy, legumes).\n- Balanced carbs around training; choose whole grains.\n- Include healthy fats (nuts, olive oil, avocado).\n\nTiming:\n- Pre-training: carb + protein snack 60–90 min before.\n- Post-training: protein + carbs within 60 min.",
            'High Carb (Match Preparation) Plan' => "Goal: Maximise energy availability for match days.\n\nGuidelines:\n- Increase carbs 24–48h pre-match (rice, pasta, potatoes, fruit).\n- Keep protein moderate; keep fats lower close to match time.\n- Hydrate consistently; include electrolytes if sweating heavily.\n\nTiming:\n- Pre-match meal (3–4h): high carb + moderate protein.\n- Top-up snack (60–90 min): easily digested carbs.",
            'Balanced Diet Plan' => "Goal: Everyday performance for training days.\n\nGuidelines:\n- Plate method: 1/2 vegetables, 1/4 protein, 1/4 carbs.\n- 2–3 fruit servings daily.\n- Hydrate and limit sugary drinks.\n\nTiming:\n- Spread meals evenly across the day.\n- Include a recovery snack after intense sessions.",
            'Weight Loss / Lean Plan' => "Goal: Reduce body fat while maintaining performance.\n\nGuidelines:\n- Prioritise protein and high-fibre foods.\n- Choose lower-calorie carbs and control portions.\n- Avoid late-night high-sugar snacks.\n\nTiming:\n- Protein-forward breakfast.\n- Smart snacks (yogurt, fruit, nuts in small portions).",
            'Recovery Plan' => "Goal: Support recovery after matches or during injury rehab.\n\nGuidelines:\n- Higher protein + micronutrients (iron, calcium, vitamin D).\n- Anti-inflammatory foods (omega-3 sources, colourful vegetables).\n- Prioritise sleep-supportive routine and hydration.\n\nTiming:\n- Recovery meal within 60–90 min post-match/training.",
            'Hydration & Light Nutrition Plan' => "Goal: Maintain hydration and light, easy digestion (hot weather / light training).\n\nGuidelines:\n- Water consistently through the day.\n- Electrolytes on hot days or long sessions.\n- Light meals: fruit, yogurt, soups, simple carbs.\n\nTiming:\n- Small frequent meals and fluids.",
        ];

        return $templates[$planName] ?? "Nutrition Plan: {$planName}";
    }

    // ── GET  nutrition  (index) ──────────────────────────────────────
    public function index() {
        $model = $this->model('M_NutritionPlan');
        $plans = $model->getAllPlans($_SESSION['user_id']);

        $data = [
            'title' => 'Nutrition Plans',
            'plans' => $plans,
        ];

        $this->view('trainer/nutrition_index', $data);
    }

    // ── GET  nutrition/create ────────────────────────────────────────
    public function create() {
        $model   = $this->model('M_NutritionPlan');
        $players = $model->getAllPlayers();
        $groups  = $model->getPlayerGroups();

        $errors = $_SESSION['nutrition_create_errors'] ?? [];
        $old    = $_SESSION['nutrition_create_old']    ?? [];
        unset($_SESSION['nutrition_create_errors'], $_SESSION['nutrition_create_old']);

        $data = [
            'title'   => 'Create Nutrition Plan',
            'players' => $players,
            'groups'  => $groups,
            'plan_options' => $this->_predefinedPlans(),
            'errors'  => $errors,
            'old'     => $old,
        ];

        $this->view('trainer/create_nutrition', $data);
    }

    // ── POST  nutrition/store ────────────────────────────────────────
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('nutrition');
            return;
        }

        [$errors, $fields] = $this->_validate($_POST);

        if (!empty($errors)) {
            $_SESSION['nutrition_create_errors'] = $errors;
            $_SESSION['nutrition_create_old']    = $fields;
            redirect('nutrition/create');
            return;
        }

        $model = $this->model('M_NutritionPlan');

        if (($fields['assignment_mode'] ?? 'individual') === 'group') {
            $fields['player_ids'] = $model->getPlayerIdsByGroup($fields['player_group'] ?? '');
            if (empty($fields['player_ids'])) {
                $_SESSION['nutrition_create_errors'] = ['player_group' => 'No players were found for the selected group.'];
                $_SESSION['nutrition_create_old']    = $fields;
                redirect('nutrition/create');
                return;
            }
        }

        $fields['trainer_id'] = $_SESSION['user_id'];

        if ($model->createPlan($fields)) {
            flash('nutrition_message', 'Nutrition plan created successfully!', 'alert alert-success');
            redirect('nutrition');
        } else {
            flash('nutrition_message', 'Something went wrong. Please try again.', 'alert alert-danger');
            redirect('nutrition/create');
        }
    }

    // ── GET  nutrition/edit/$id ──────────────────────────────────────
    public function edit($id) {
        $id    = (int)$id;
        $model = $this->model('M_NutritionPlan');
        $plan  = $model->getPlanById($id, $_SESSION['user_id']);

        if (!$plan) {
            flash('nutrition_message', 'Plan not found or access denied.', 'alert alert-danger');
            redirect('nutrition');
            return;
        }

        $players = $model->getAllPlayers();
        $groups  = $model->getPlayerGroups();
        $assignedPlayerIds = $model->getAssignedPlayerIds($id);
        $errors  = $_SESSION['nutrition_edit_errors'] ?? [];
        $old     = $_SESSION['nutrition_edit_old']    ?? [];
        unset($_SESSION['nutrition_edit_errors'], $_SESSION['nutrition_edit_old']);

        $data = [
            'title'   => 'Edit Nutrition Plan',
            'plan'    => $plan,
            'players' => $players,
            'groups'  => $groups,
            'assigned_player_ids' => $assignedPlayerIds,
            'plan_options' => $this->_predefinedPlans(),
            'errors'  => $errors,
            'old'     => $old,
        ];

        $this->view('trainer/edit_nutrition', $data);
    }

    // ── POST  nutrition/update/$id ───────────────────────────────────
    public function update($id) {
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('nutrition');
            return;
        }

        [$errors, $fields] = $this->_validate($_POST);

        if (!empty($errors)) {
            $_SESSION['nutrition_edit_errors'] = $errors;
            $_SESSION['nutrition_edit_old']    = $fields;
            redirect('nutrition/edit/' . $id);
            return;
        }

        $model = $this->model('M_NutritionPlan');

        // Ownership check before writing
        if (!$model->getPlanById($id, $_SESSION['user_id'])) {
            flash('nutrition_message', 'Plan not found or access denied.', 'alert alert-danger');
            redirect('nutrition');
            return;
        }

        if (($fields['assignment_mode'] ?? 'individual') === 'group') {
            $fields['player_ids'] = $model->getPlayerIdsByGroup($fields['player_group'] ?? '');
            if (empty($fields['player_ids'])) {
                $_SESSION['nutrition_edit_errors'] = ['player_group' => 'No players were found for the selected group.'];
                $_SESSION['nutrition_edit_old']    = $fields;
                redirect('nutrition/edit/' . $id);
                return;
            }
        }

        $fields['plan_id'] = $id;

        if ($model->updatePlan($fields)) {
            flash('nutrition_message', 'Nutrition plan updated successfully!', 'alert alert-success');
            redirect('nutrition');
        } else {
            flash('nutrition_message', 'Update failed. Please try again.', 'alert alert-danger');
            redirect('nutrition/edit/' . $id);
        }
    }

    // ── POST  nutrition/delete/$id ───────────────────────────────────
    public function delete($id) {
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('nutrition');
            return;
        }

        $model = $this->model('M_NutritionPlan');

        if ($model->deletePlan($id, $_SESSION['user_id'])) {
            flash('nutrition_message', 'Nutrition plan deleted.', 'alert alert-success');
        } else {
            flash('nutrition_message', 'Delete failed or plan not found.', 'alert alert-danger');
        }

        redirect('nutrition');
    }

    // ── Private: shared validation for create + update ───────────────
    // Returns [$errors, $sanitisedFields]
    private function _validate(array $post): array {
        $planName    = trim(htmlspecialchars($post['plan_name']    ?? '', ENT_QUOTES, 'UTF-8'));
        $assignmentMode = trim(strtolower($post['assignment_mode'] ?? 'individual'));
        if (!in_array($assignmentMode, ['individual', 'group'], true)) {
            $assignmentMode = 'individual';
        }

        $rawPlayerIds = $post['player_ids'] ?? [];
        if (!is_array($rawPlayerIds)) {
            $rawPlayerIds = [];
        }
        $playerIds = [];
        foreach ($rawPlayerIds as $playerId) {
            $playerId = (int)$playerId;
            if ($playerId > 0) {
                $playerIds[] = $playerId;
            }
        }
        $playerIds = array_values(array_unique($playerIds));

        $playerGroup = trim($post['player_group'] ?? '');

        $notes      = trim(htmlspecialchars($post['notes'] ?? '', ENT_QUOTES, 'UTF-8'));
        $dietDetails = trim(htmlspecialchars($post['diet_details'] ?? '', ENT_QUOTES, 'UTF-8'));
        $duration    = trim($post['duration']    ?? '');
        $status      = trim($post['status']      ?? 'active');
        $createdDate = trim($post['created_date'] ?? '');

        $errors = [];

        $allowedPlans = $this->_predefinedPlans();
        if ($planName === '') {
            $errors['plan_name'] = 'Please select a plan.';
        } elseif (!in_array($planName, $allowedPlans, true)) {
            $errors['plan_name'] = 'Invalid plan selected.';
        }

        if ($assignmentMode === 'group') {
            if ($playerGroup === '') {
                $errors['player_group'] = 'Please select a player group.';
            }
        } elseif (empty($playerIds)) {
            $errors['player_ids'] = 'Please select at least one player.';
        }

        if ($notes !== '' && strlen($notes) > 1000) {
            $errors['notes'] = 'Notes must be 1000 characters or fewer.';
        }

        if ($dietDetails === '') {
            $dietDetails = $this->_planTemplate($planName);
        }

        if ($duration === '') {
            $errors['duration'] = 'Duration is required.';
        } elseif (!ctype_digit($duration) || (int)$duration <= 0) {
            $errors['duration'] = 'Duration must be a whole number greater than 0.';
        }

        if ($createdDate === '') {
            $errors['created_date'] = 'Created date is required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $createdDate)) {
            $errors['created_date'] = 'Invalid date format.';
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        $fields = [
            'plan_name'    => $planName,
            'assignment_mode' => $assignmentMode,
            'player_ids'   => $playerIds,
            'player_group' => $playerGroup,
            'diet_details' => $dietDetails,
            'notes'        => $notes,
            'duration'     => $duration,
            'status'       => $status,
            'created_date' => $createdDate,
        ];

        // Backwards-compatible single-player field for update queries
        $fields['player_id'] = !empty($playerIds) ? (int)$playerIds[0] : 0;

        return [$errors, $fields];
    }
}
