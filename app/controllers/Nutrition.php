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

    private function _normalizeDecimal($value): ?string {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $value = str_replace(',', '.', $value);
        if (!is_numeric($value)) {
            return null;
        }

        return number_format((float)$value, 2, '.', '');
    }

    private function _normalizeInteger($value): ?int {
        if ($value === null || $value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            return null;
        }

        return (int)$value;
    }

    private function _validateMacroPercentages($protein, $carbs, $fat): array {
        $errors = [];

        foreach ([
            'protein_percentage' => $protein,
            'carbohydrate_percentage' => $carbs,
            'fat_percentage' => $fat,
        ] as $field => $value) {
            if ($value === null) {
                $errors[$field] = 'Please enter a valid percentage.';
            } elseif ((float)$value < 0 || (float)$value > 100) {
                $errors[$field] = 'Percentage values must be between 0 and 100.';
            }
        }

        if ($protein !== null && $carbs !== null && $fat !== null) {
            $total = (float)$protein + (float)$carbs + (float)$fat;
            if (abs($total - 100.0) > 0.01) {
                $errors['fat_percentage'] = 'Protein (' . $protein . '%) + Carbohydrate (' . $carbs . '%) + Fat (' . $fat . '%) = ' . number_format($total, 2) . '%. They must total exactly 100%.';
            }
        }

        return $errors;
    }

    private function _composeDietDetails(array $fields): string {
        $lines = [
            'Plan: ' . ($fields['plan_name'] ?? ''),
            'Protein: ' . ($fields['protein_percentage'] ?? '') . '%',
            'Carbohydrates: ' . ($fields['carbohydrate_percentage'] ?? '') . '%',
            'Fat: ' . ($fields['fat_percentage'] ?? '') . '%',
            'Recommended calories: ' . ($fields['recommended_calories'] ?? ''),
        ];

        if (!empty($fields['description'])) {
            $lines[] = 'Description: ' . $fields['description'];
        }

        return implode("\n", array_filter($lines, static fn($line) => trim((string)$line) !== ''));
    }

    private function _extractDescriptionFromDietDetails($dietDetails): string {
        $dietDetails = (string)$dietDetails;
        if ($dietDetails === '') {
            return '';
        }

        foreach (preg_split('/\r\n|\r|\n/', $dietDetails) as $line) {
            if (stripos($line, 'Description:') === 0) {
                return trim(substr($line, strlen('Description:')));
            }
        }

        return '';
    }

    private function _extractDietDetailsFields($dietDetails): array {
        $result = [
            'plan_name' => '',
            'protein_percentage' => null,
            'carbohydrate_percentage' => null,
            'fat_percentage' => null,
            'recommended_calories' => null,
            'description' => '',
        ];

        $dietDetails = (string)$dietDetails;
        if ($dietDetails === '') {
            return $result;
        }

        foreach (preg_split('/\r\n|\r|\n/', $dietDetails) as $line) {
            $line = trim((string)$line);
            if ($line === '') {
                continue;
            }

            if (stripos($line, 'Plan:') === 0) {
                $result['plan_name'] = trim(substr($line, strlen('Plan:')));
                continue;
            }

            if (stripos($line, 'Protein:') === 0) {
                $value = trim(str_replace('%', '', substr($line, strlen('Protein:'))));
                $result['protein_percentage'] = $this->_normalizeDecimal($value);
                continue;
            }

            if (stripos($line, 'Carbohydrates:') === 0) {
                $value = trim(str_replace('%', '', substr($line, strlen('Carbohydrates:'))));
                $result['carbohydrate_percentage'] = $this->_normalizeDecimal($value);
                continue;
            }

            if (stripos($line, 'Fat:') === 0) {
                $value = trim(str_replace('%', '', substr($line, strlen('Fat:'))));
                $result['fat_percentage'] = $this->_normalizeDecimal($value);
                continue;
            }

            if (stripos($line, 'Recommended calories:') === 0) {
                $value = trim(substr($line, strlen('Recommended calories:')));
                $result['recommended_calories'] = $this->_normalizeInteger($value);
                continue;
            }

            if (stripos($line, 'Description:') === 0) {
                $result['description'] = trim(substr($line, strlen('Description:')));
            }
        }

        return $result;
    }

    // ── GET  nutrition  (index) ──────────────────────────────────────
    public function index() {
        $model = $this->model('M_NutritionPlan');
        $plans = $model->getAllPlans($_SESSION['user_id']);
        $templates = $model->getNutritionTemplates(false);

        $data = [
            'title' => 'Nutrition Plans',
            'plans' => $plans,
            'nutrition_templates' => $templates,
        ];

        $this->view('trainer/nutrition_index_v2', $data);
    }

    // ── GET  nutrition/create ────────────────────────────────────────
    public function create() {
        $model   = $this->model('M_NutritionPlan');
        $players = $model->getAllPlayers();
        $groups  = $model->getPlayerGroups();
        // Include inactive templates too, so previously selected template stays visible on edit.
        $templates = $model->getNutritionTemplates(false);

        $errors = $_SESSION['nutrition_create_errors'] ?? [];
        $old    = $_SESSION['nutrition_create_old']    ?? [];
        unset($_SESSION['nutrition_create_errors'], $_SESSION['nutrition_create_old']);

        $data = [
            'title'   => 'Create Nutrition Plan',
            'players' => $players,
            'groups'  => $groups,
            'nutrition_templates' => $templates,
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

        $model = $this->model('M_NutritionPlan');
        [$errors, $fields] = $this->_validate($_POST, $model);

        if (!empty($errors)) {
            $_SESSION['nutrition_create_errors'] = $errors;
            $_SESSION['nutrition_create_old']    = $fields;
            redirect('nutrition/create');
            return;
        }

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
        // Include inactive templates too so existing assignment remains selectable on edit.
        $templates = $model->getNutritionTemplates(false);

        // Backfill TemplateID for legacy rows where only plan name was stored.
        if (empty($plan->TemplateID)) {
            $planName = html_entity_decode((string)($plan->PlanName ?? $plan->nutritionPlanName ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $matchedTemplate = $model->getNutritionTemplateByName($planName);
            if ($matchedTemplate && !empty($matchedTemplate->TemplateID)) {
                $plan->TemplateID = (int)$matchedTemplate->TemplateID;
            }
        }
        $assignedPlayerIds = $model->getAssignedPlayerIds($id);
        $errors  = $_SESSION['nutrition_edit_errors'] ?? [];
        $old     = $_SESSION['nutrition_edit_old']    ?? [];
        unset($_SESSION['nutrition_edit_errors'], $_SESSION['nutrition_edit_old']);

        $data = [
            'title'   => 'Edit Nutrition Plan',
            'plan'    => $plan,
            'players' => $players,
            'groups'  => $groups,
            'nutrition_templates' => $templates,
            'assigned_player_ids' => $assignedPlayerIds,
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

        $model = $this->model('M_NutritionPlan');
        $existingPlan = $model->getPlanById($id, $_SESSION['user_id']);
        if (!$existingPlan) {
            flash('nutrition_message', 'Plan not found or access denied.', 'alert alert-danger');
            redirect('nutrition');
            return;
        }

        [$errors, $fields] = $this->_validate($_POST, $model, $existingPlan);

        if (!empty($errors)) {
            $_SESSION['nutrition_edit_errors'] = $errors;
            $_SESSION['nutrition_edit_old']    = $fields;
            redirect('nutrition/edit/' . $id);
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

    public function template($templateId = 0) {
        header('Content-Type: application/json');

        $templateId = (int)$templateId;
        if ($templateId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid template selected.']);
            return;
        }

        $model = $this->model('M_NutritionPlan');
        $template = $model->getNutritionTemplateById($templateId);

        if (!$template) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Nutrition template not found.']);
            return;
        }

        echo json_encode([
            'success' => true,
            'template' => [
                'template_id' => (int)($template->TemplateID ?? 0),
                'plan_name' => (string)($template->PlanName ?? ''),
                'protein_percentage' => (string)($template->ProteinPercentage ?? ''),
                'carbohydrate_percentage' => (string)($template->CarbohydratePercentage ?? ''),
                'fat_percentage' => (string)($template->FatPercentage ?? ''),
                'recommended_calories' => (string)($template->RecommendedCalories ?? ''),
                'description' => (string)($template->Description ?? ''),
            ],
        ]);
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
    private function _validate(array $post, $model = null, $existingPlan = null): array {
        $isEdit = $existingPlan !== null;
        $existingDietFields = $existingPlan ? $this->_extractDietDetailsFields($existingPlan->DietDetails ?? '') : [];

        $templateId = (int)($post['template_id'] ?? 0);
        $existingTemplateIdFromPost = (int)($post['existing_template_id'] ?? 0);
        $template = null;
        if ($model && $templateId > 0) {
            $template = $model->getNutritionTemplateById($templateId);
        }

        // If trainer did not change template during edit, preserve the existing template id from form.
        if (!$template && $model && $existingTemplateIdFromPost > 0) {
            $template = $model->getNutritionTemplateById($existingTemplateIdFromPost);
        }

        if (!$template && $existingPlan && !empty($existingPlan->TemplateID)) {
            $template = $model ? $model->getNutritionTemplateById((int)$existingPlan->TemplateID) : null;
        }

        // Fallback by plan name for older rows where TemplateID might be null.
        if (!$template && $model && $existingPlan) {
            $existingPlanName = trim(html_entity_decode((string)($existingPlan->PlanName ?? $existingPlan->nutritionPlanName ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($existingPlanName !== '') {
                $template = $model->getNutritionTemplateByName($existingPlanName);
            }
        }

        $planName = trim(htmlspecialchars($post['plan_name'] ?? '', ENT_QUOTES, 'UTF-8'));
        if ($planName === '' && $existingPlan) {
            $planName = trim((string)($existingPlan->PlanName ?? $existingPlan->nutritionPlanName ?? ''));
            if ($planName === '') {
                $planName = trim((string)($existingDietFields['plan_name'] ?? ''));
            }
        } elseif ($planName === '' && $template) {
            $planName = htmlspecialchars((string)($template->PlanName ?? ''), ENT_QUOTES, 'UTF-8');
        }

        $proteinPercentage = $this->_normalizeDecimal($post['protein_percentage'] ?? null);
        if ($proteinPercentage === null && $existingPlan) {
            $proteinPercentage = $this->_normalizeDecimal($existingPlan->ProteinPercentage ?? null);
            if ($proteinPercentage === null) {
                $proteinPercentage = $existingDietFields['protein_percentage'] ?? null;
            }
        } elseif ($proteinPercentage === null && $template) {
            $proteinPercentage = $this->_normalizeDecimal($template->ProteinPercentage ?? null);
        }

        $carbohydratePercentage = $this->_normalizeDecimal($post['carbohydrate_percentage'] ?? null);
        if ($carbohydratePercentage === null && $existingPlan) {
            $carbohydratePercentage = $this->_normalizeDecimal($existingPlan->CarbohydratePercentage ?? null);
            if ($carbohydratePercentage === null) {
                $carbohydratePercentage = $existingDietFields['carbohydrate_percentage'] ?? null;
            }
        } elseif ($carbohydratePercentage === null && $template) {
            $carbohydratePercentage = $this->_normalizeDecimal($template->CarbohydratePercentage ?? null);
        }

        $fatPercentage = $this->_normalizeDecimal($post['fat_percentage'] ?? null);
        if ($fatPercentage === null && $existingPlan) {
            $fatPercentage = $this->_normalizeDecimal($existingPlan->FatPercentage ?? null);
            if ($fatPercentage === null) {
                $fatPercentage = $existingDietFields['fat_percentage'] ?? null;
            }
        } elseif ($fatPercentage === null && $template) {
            $fatPercentage = $this->_normalizeDecimal($template->FatPercentage ?? null);
        }

        $recommendedCalories = $this->_normalizeInteger($post['recommended_calories'] ?? null);
        if ($recommendedCalories === null && $existingPlan) {
            $recommendedCalories = $this->_normalizeInteger($existingPlan->RecommendedCalories ?? null);
            if ($recommendedCalories === null) {
                $recommendedCalories = $this->_normalizeInteger($existingDietFields['recommended_calories'] ?? null);
            }
        } elseif ($recommendedCalories === null && $template) {
            $recommendedCalories = $this->_normalizeInteger($template->RecommendedCalories ?? null);
        }

        $description = trim(htmlspecialchars($post['description'] ?? '', ENT_QUOTES, 'UTF-8'));
        if ($description === '' && $existingPlan) {
            $existingDescription = (string)($existingPlan->Description ?? $existingPlan->description ?? '');
            if ($existingDescription === '') {
                $existingDescription = (string)($existingDietFields['description'] ?? '');
            }
            if ($existingDescription === '') {
                $existingDescription = $this->_extractDescriptionFromDietDetails($existingPlan->DietDetails ?? '');
            }
            $description = trim(htmlspecialchars($existingDescription, ENT_QUOTES, 'UTF-8'));
        }

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

        $supplements = trim(htmlspecialchars($post['supplements'] ?? '', ENT_QUOTES, 'UTF-8'));
        if ($isEdit && $supplements === '' && $existingPlan) {
            $supplements = trim(htmlspecialchars((string)($existingPlan->Supplements ?? $existingPlan->supplements ?? ''), ENT_QUOTES, 'UTF-8'));
        }

        $notes = trim(htmlspecialchars($post['notes'] ?? '', ENT_QUOTES, 'UTF-8'));
        if ($isEdit && $notes === '' && $existingPlan) {
            $notes = trim(htmlspecialchars((string)($existingPlan->Notes ?? $existingPlan->notes ?? ''), ENT_QUOTES, 'UTF-8'));
        }

        $duration = trim($post['duration'] ?? '');
        if ($isEdit && $duration === '' && $existingPlan) {
            $duration = (string)($existingPlan->Duration ?? '');
        }

        $status = trim($post['status'] ?? 'active');
        if ($isEdit && $status === '' && $existingPlan) {
            $status = (string)($existingPlan->Status ?? 'active');
        }

        $createdDate = trim($post['created_date'] ?? '');
        if ($isEdit && $createdDate === '' && $existingPlan) {
            $createdDate = !empty($existingPlan->CreatedDate) ? date('Y-m-d', strtotime($existingPlan->CreatedDate)) : '';
        }

        $errors = [];

        if ($templateId <= 0 && !$existingPlan) {
            $errors['template_id'] = 'Please select a nutrition template.';
        } elseif ($templateId > 0 && !$template) {
            $errors['template_id'] = 'Selected nutrition template is not available.';
        }

        if ($planName === '') {
            $errors['plan_name'] = 'Plan name is required.';
        } elseif (mb_strlen($planName) > 150) {
            $errors['plan_name'] = 'Plan name must be 150 characters or fewer.';
        }

        foreach ([
            'protein_percentage' => $proteinPercentage,
            'carbohydrate_percentage' => $carbohydratePercentage,
            'fat_percentage' => $fatPercentage,
        ] as $field => $value) {
            if ($value === null) {
                $errors[$field] = 'Please enter a valid percentage.';
            } elseif ((float)$value < 0 || (float)$value > 100) {
                $errors[$field] = 'Percentage values must be between 0 and 100.';
            }
        }

        if ($proteinPercentage !== null && $carbohydratePercentage !== null && $fatPercentage !== null) {
            $total = (float)$proteinPercentage + (float)$carbohydratePercentage + (float)$fatPercentage;
            if (abs($total - 100.0) > 0.01) {
                $errors['fat_percentage'] = 'Protein (' . $proteinPercentage . '%) + Carbohydrate (' . $carbohydratePercentage . '%) + Fat (' . $fatPercentage . '%) = ' . number_format($total, 2) . '%. They must total exactly 100%.';
            }
        }


        if ($recommendedCalories === null) {
            $errors['recommended_calories'] = 'Please enter a valid calorie target.';
        } elseif ($recommendedCalories < 500 || $recommendedCalories > 10000) {
            $errors['recommended_calories'] = 'Calories must be between 500 and 10000.';
        }

        if ($description !== '' && mb_strlen($description) > 2000) {
            $errors['description'] = 'Description must be 2000 characters or fewer.';
        }

        if ($assignmentMode === 'group') {
            if ($playerGroup === '') {
                $errors['player_group'] = 'Please select a player group.';
            }
        } elseif (empty($playerIds)) {
            $errors['player_ids'] = 'Please select at least one player.';
        }

        if ($supplements !== '' && mb_strlen($supplements) > 2000) {
            $errors['supplements'] = 'Supplements must be 2000 characters or fewer.';
        }

        if ($notes !== '' && mb_strlen($notes) > 1000) {
            $errors['notes'] = 'Notes must be 1000 characters or fewer.';
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
            'template_id' => $template
                ? (int)($template->TemplateID ?? $templateId)
                : ($existingTemplateIdFromPost > 0
                    ? $existingTemplateIdFromPost
                    : ($existingPlan ? (int)($existingPlan->TemplateID ?? 0) : $templateId)),
            'plan_name'    => $planName,
            'assignment_mode' => $assignmentMode,
            'player_ids'   => $playerIds,
            'player_group' => $playerGroup,
            'protein_percentage' => $proteinPercentage,
            'carbohydrate_percentage' => $carbohydratePercentage,
            'fat_percentage' => $fatPercentage,
            'recommended_calories' => $recommendedCalories,
            'description' => $description,
            'supplements' => $supplements,
            'diet_details' => $this->_composeDietDetails([
                'plan_name' => $planName,
                'protein_percentage' => $proteinPercentage,
                'carbohydrate_percentage' => $carbohydratePercentage,
                'fat_percentage' => $fatPercentage,
                'recommended_calories' => $recommendedCalories,
                'description' => $description,
            ]),
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
