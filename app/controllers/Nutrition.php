<?php
class Nutrition extends Controller {

    public function __construct() {
        // Dev bypass — mirrors the pattern used in Trainer.php
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id']   = 1;
            $_SESSION['username']  = 'John Trainer';
            $_SESSION['user_role'] = 'Trainer';
        }
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

        $errors = $_SESSION['nutrition_create_errors'] ?? [];
        $old    = $_SESSION['nutrition_create_old']    ?? [];
        unset($_SESSION['nutrition_create_errors'], $_SESSION['nutrition_create_old']);

        $data = [
            'title'   => 'Create Nutrition Plan',
            'players' => $players,
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

        $fields['trainer_id'] = $_SESSION['user_id'];
        $model = $this->model('M_NutritionPlan');

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
        $errors  = $_SESSION['nutrition_edit_errors'] ?? [];
        $old     = $_SESSION['nutrition_edit_old']    ?? [];
        unset($_SESSION['nutrition_edit_errors'], $_SESSION['nutrition_edit_old']);

        $data = [
            'title'   => 'Edit Nutrition Plan',
            'plan'    => $plan,
            'players' => $players,
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
        $playerId    = (int)($post['player_id']    ?? 0);
        $dietDetails = trim(htmlspecialchars($post['diet_details'] ?? '', ENT_QUOTES, 'UTF-8'));
        $duration    = trim($post['duration']    ?? '');
        $status      = trim($post['status']      ?? 'active');
        $createdDate = trim($post['created_date'] ?? '');

        $errors = [];

        if ($planName === '') {
            $errors['plan_name'] = 'Plan name is required.';
        } elseif (strlen($planName) > 255) {
            $errors['plan_name'] = 'Plan name must be 255 characters or fewer.';
        }

        if ($playerId === 0) {
            $errors['player_id'] = 'Please select a player.';
        }

        if ($dietDetails === '') {
            $errors['diet_details'] = 'Diet details are required.';
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
            'player_id'    => $playerId,
            'diet_details' => $dietDetails,
            'duration'     => $duration,
            'status'       => $status,
            'created_date' => $createdDate,
        ];

        return [$errors, $fields];
    }
}
