<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition_crud.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'nutrition'; ?>

<?php
// Resolve which values to show:
//   After a failed update attempt → use stashed $old values
//   Fresh load → use $plan values from DB
$plan   = $data['plan'];
$old    = $data['old']    ?? [];       // repopulated after failed submit
$errors = $data['errors'] ?? [];
$templates = $data['nutrition_templates'] ?? [];

// Helper: return old (post) value if available, otherwise DB value
$val = function(string $k, string $dbCol = '') use ($old, $plan) {
    if (isset($old[$k])) return htmlspecialchars(html_entity_decode((string)$old[$k], ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES);
    $col = $dbCol;
    if (!$col) {
        // Special case: plan_name can map to PlanName or nutritionPlanName
        if ($k === 'plan_name') {
            $col = isset($plan->PlanName) ? 'PlanName' : 'nutritionPlanName';
        } else {
            $col = $k;
        }
    }
    return htmlspecialchars(html_entity_decode((string)($plan->$col ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES);
};
$err = fn(string $k) => $errors[$k] ?? '';
$cls = fn(string $k) => isset($errors[$k]) ? ' is-invalid' : '';

$assignmentMode = $old['assignment_mode'] ?? 'individual';
$selectedGroup = $old['player_group'] ?? '';
$selectedPlayers = [];
if (!empty($old['player_ids']) && is_array($old['player_ids'])) {
    foreach ($old['player_ids'] as $playerId) {
        $playerId = (int)$playerId;
        if ($playerId > 0) {
            $selectedPlayers[] = $playerId;
        }
    }
} elseif (!empty($data['assigned_player_ids']) && is_array($data['assigned_player_ids'])) {
    foreach ($data['assigned_player_ids'] as $playerId) {
        $playerId = (int)$playerId;
        if ($playerId > 0) {
            $selectedPlayers[] = $playerId;
        }
    }
} elseif (!empty($plan->PlayerID)) {
    $selectedPlayers[] = (int)$plan->PlayerID;
}
$selectedPlayers = array_values(array_unique($selectedPlayers));
?>

<div class="player-layout nc-page">

    <!-- ── Sidebar ─────────────────────────────────────────────── -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-user-tie"></i>
                <h3>Trainer Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>

        <div class="trainer-profile">
            <div class="trainer-avatar"><i class="fas fa-user-tie"></i></div>
            <div class="trainer-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'John Trainer'); ?></div>
            <div class="trainer-role">Fitness Trainer</div>
            <div class="profile-actions">
                <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                    <i class="fas fa-user-cog"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Main content ─────────────────────────────────────────── -->
    <div class="main-content" id="mainContent">

        <!-- Page header -->
        <div class="nc-header">
            <div class="nc-header-text">
                <h1><i class="fas fa-edit"></i> Edit Nutrition Plan</h1>
                <p>Update plan #<?php echo (int)$plan->PlanID; ?> —
                         <?php echo htmlspecialchars($plan->PlanName ?? $plan->nutritionPlanName ?? 'Untitled Plan'); ?>
                </p>
            </div>
            <div class="nc-header-actions">
                <a href="<?php echo URLROOT; ?>/nutrition" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Plans
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        <?php flash('nutrition_message'); ?>

        <!-- Form card -->
        <div class="nc-card">
            <div class="nc-card-header">
                <h2>
                    <i class="fas fa-apple-alt"></i>
                    Editing: <?php echo htmlspecialchars($plan->PlanName ?? $plan->nutritionPlanName ?? ('Plan #' . $plan->PlanID)); ?>
                </h2>
                <span style="font-size:.8rem;color:#6c757d;">
                    ID #<?php echo (int)$plan->PlanID; ?>
                </span>
            </div>
            <div class="nc-card-body">

                <p class="nc-form-sub">
                    All fields marked <span style="color:#e74c3c">*</span> are required.
                </p>

                <form method="POST"
                      action="<?php echo URLROOT; ?>/nutrition/update/<?php echo (int)$plan->PlanID; ?>"
                      data-template-endpoint="<?php echo URLROOT; ?>/nutrition/template/"
                      novalidate>

                    <!-- Row 1: Template + Plan Name -->
                    <div class="nc-field-row">

                        <div class="nc-field">
                            <label for="template_id">
                                <i class="fas fa-layer-group"></i> Template
                                <span class="req">*</span>
                            </label>
                            <?php
                                $currentTemplateId = (int)($old['template_id'] ?? ($plan->TemplateID ?? 0));
                                if ($currentTemplateId <= 0) {
                                    $currentPlanName = trim(html_entity_decode((string)($plan->PlanName ?? $plan->nutritionPlanName ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                    foreach ($templates as $template) {
                                        $templateName = trim(html_entity_decode((string)$template->PlanName, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                        if (strcasecmp($templateName, $currentPlanName) === 0) {
                                            $currentTemplateId = (int)$template->TemplateID;
                                            break;
                                        }
                                    }
                                }

                                if ($currentTemplateId <= 0) {
                                    $planProtein = isset($plan->ProteinPercentage) ? (float)$plan->ProteinPercentage : null;
                                    $planCarbs = isset($plan->CarbohydratePercentage) ? (float)$plan->CarbohydratePercentage : null;
                                    $planFat = isset($plan->FatPercentage) ? (float)$plan->FatPercentage : null;
                                    $planCalories = isset($plan->RecommendedCalories) ? (int)$plan->RecommendedCalories : null;

                                    foreach ($templates as $template) {
                                        $templateProtein = isset($template->ProteinPercentage) ? (float)$template->ProteinPercentage : null;
                                        $templateCarbs = isset($template->CarbohydratePercentage) ? (float)$template->CarbohydratePercentage : null;
                                        $templateFat = isset($template->FatPercentage) ? (float)$template->FatPercentage : null;
                                        $templateCalories = isset($template->RecommendedCalories) ? (int)$template->RecommendedCalories : null;

                                        if (
                                            $planProtein !== null && $planCarbs !== null && $planFat !== null && $planCalories !== null &&
                                            abs($templateProtein - $planProtein) < 0.01 &&
                                            abs($templateCarbs - $planCarbs) < 0.01 &&
                                            abs($templateFat - $planFat) < 0.01 &&
                                            $templateCalories === $planCalories
                                        ) {
                                            $currentTemplateId = (int)$template->TemplateID;
                                            break;
                                        }
                                    }
                                }

                                $hasCurrentTemplateOption = false;
                                foreach ($templates as $template) {
                                    if ((int)$template->TemplateID === $currentTemplateId) {
                                        $hasCurrentTemplateOption = true;
                                        break;
                                    }
                                }
                            ?>
                            <input type="hidden" name="existing_template_id" value="<?php echo (int)$currentTemplateId; ?>">
                            <select id="template_id" name="template_id" class="form-control<?php echo $cls('template_id'); ?>" data-template-select>
                                <option value="">— Select a predefined template —</option>
                                <?php if ($currentTemplateId > 0 && !$hasCurrentTemplateOption): ?>
                                    <option value="<?php echo (int)$currentTemplateId; ?>" selected>
                                        Current Template (Unavailable)
                                    </option>
                                <?php endif; ?>
                                <?php foreach ($templates as $template): ?>
                                    <option value="<?php echo (int)$template->TemplateID; ?>" <?php echo $currentTemplateId === (int)$template->TemplateID ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($template->PlanName); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($err('template_id')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('template_id')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="nc-field">
                            <label for="plan_name">
                                <i class="fas fa-tag"></i> Plan Name
                                <span class="req">*</span>
                            </label>
                            <input type="text"
                                   id="plan_name"
                                   name="plan_name"
                                   class="form-control<?php echo $cls('plan_name'); ?>"
                                   value="<?php echo $val('plan_name'); ?>"
                                   maxlength="150"
                                   placeholder="e.g. Tournament Recovery Plan">
                            <?php if ($err('plan_name')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('plan_name')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="nc-field">
                            <label for="assignment_mode">
                                <i class="fas fa-layer-group"></i> Assign To
                                <span class="req">*</span>
                            </label>
                            <select id="assignment_mode" name="assignment_mode" class="form-control<?php echo $cls('assignment_mode'); ?>">
                                <option value="individual" <?php echo $assignmentMode === 'individual' ? 'selected' : ''; ?>>Individual Players</option>
                                <option value="group" <?php echo $assignmentMode === 'group' ? 'selected' : ''; ?>>Player Group</option>
                            </select>
                            <?php if ($err('assignment_mode')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('assignment_mode')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Row 2: Macro breakdown -->
                    <div class="nc-field-row third">
                        <div class="nc-field">
                            <label for="protein_percentage">
                                <i class="fas fa-drumstick-bite"></i> Protein %
                                <span class="req">*</span>
                            </label>
                            <input type="number" id="protein_percentage" name="protein_percentage" class="form-control<?php echo $cls('protein_percentage'); ?>" value="<?php echo $val('protein_percentage', 'ProteinPercentage'); ?>" min="0" max="100" step="0.01" placeholder="40">
                            <?php if ($err('protein_percentage')): ?>
                                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err('protein_percentage')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="nc-field">
                            <label for="carbohydrate_percentage">
                                <i class="fas fa-bread-slice"></i> Carbohydrate %
                                <span class="req">*</span>
                            </label>
                            <input type="number" id="carbohydrate_percentage" name="carbohydrate_percentage" class="form-control<?php echo $cls('carbohydrate_percentage'); ?>" value="<?php echo $val('carbohydrate_percentage', 'CarbohydratePercentage'); ?>" min="0" max="100" step="0.01" placeholder="35">
                            <?php if ($err('carbohydrate_percentage')): ?>
                                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err('carbohydrate_percentage')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="nc-field">
                            <label for="fat_percentage">
                                <i class="fas fa-oil-can"></i> Fat %
                                <span class="req">*</span>
                            </label>
                            <input type="number" id="fat_percentage" name="fat_percentage" class="form-control<?php echo $cls('fat_percentage'); ?>" value="<?php echo $val('fat_percentage', 'FatPercentage'); ?>" min="0" max="100" step="0.01" placeholder="25">
                            <?php if ($err('fat_percentage')): ?>
                                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err('fat_percentage')); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 3: Calories + Description -->
                    <div class="nc-field-row full">
                        <div class="nc-field">
                            <label for="recommended_calories">
                                <i class="fas fa-fire"></i> Recommended Calories
                                <span class="req">*</span>
                            </label>
                            <input type="number" id="recommended_calories" name="recommended_calories" class="form-control<?php echo $cls('recommended_calories'); ?>" value="<?php echo $val('recommended_calories', 'RecommendedCalories'); ?>" min="500" max="10000" step="1" placeholder="2400">
                            <?php if ($err('recommended_calories')): ?>
                                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err('recommended_calories')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="nc-field">
                            <label for="description">
                                <i class="fas fa-align-left"></i> Description / Guidelines
                            </label>
                            <textarea id="description" name="description" class="form-control<?php echo $cls('description'); ?>" rows="4" placeholder="Add notes or guidelines for this customized plan."><?php echo $val('description', 'Description'); ?></textarea>
                            <?php if ($err('description')): ?>
                                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err('description')); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                        <div class="nc-field-row full">
                            <div class="nc-field">
                                <label for="supplements">
                                    <i class="fas fa-capsules"></i> Supplements (optional)
                                </label>
                                <textarea id="supplements" name="supplements"
                                          class="form-control<?php echo $cls('supplements'); ?>"
                                          rows="4"
                                          placeholder="Add supplement guidance only if necessary (e.g., whey protein 1 scoop after training, electrolytes after intense sessions)."><?php echo $val('supplements', 'Supplements'); ?></textarea>
                                <small class="nc-help-text">Leave this empty unless supplements are required for the player or group.</small>
                                <?php if ($err('supplements')): ?>
                                    <span class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo htmlspecialchars($err('supplements')); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                    <!-- Row 4: Assignment Target -->
                    <div class="nc-field-row full nc-assignment-grid">
                        <div class="nc-field">
                            <label>
                                <i class="fas fa-user-friends"></i> Players / Group
                                <span class="req">*</span>
                            </label>

                            <div class="nc-assignment-mode-panel" id="individualAssignmentPanel" data-mode-panel="individual">
                                <div class="nc-player-picker <?php echo $assignmentMode === 'individual' ? 'is-active' : ''; ?><?php echo $cls('player_ids') || $cls('player_id') ? ' is-invalid' : ''; ?>" id="playerPicker">
                                    <button type="button" class="nc-picker-trigger" id="playerPickerTrigger" aria-expanded="false" aria-controls="playerPickerPanel">
                                        <span class="nc-picker-trigger-label">Select players</span>
                                        <span class="nc-picker-trigger-summary" id="playerPickerSummary">No players selected</span>
                                        <i class="fas fa-chevron-down nc-picker-caret"></i>
                                    </button>
                                    <div class="nc-picker-panel" id="playerPickerPanel" hidden>
                                        <div class="nc-picker-panel-head">
                                            <div>
                                                <strong>Choose players</strong>
                                                <span>Pick one or more players for this plan.</span>
                                            </div>
                                            <span class="nc-player-count" id="selectedPlayersCount">0 selected</span>
                                        </div>
                                        <div class="nc-picker-toolbar">
                                            <button type="button" class="btn btn-secondary btn-sm" id="selectAllPlayersBtn">
                                                <i class="fas fa-check-double"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" id="clearPlayersBtn">
                                                <i class="fas fa-eraser"></i> Clear
                                            </button>
                                        </div>
                                        <div class="nc-picker-options">
                                            <?php foreach ($data['players'] as $p): ?>
                                                <label class="nc-picker-option">
                                                    <input type="checkbox"
                                                           name="player_ids[]"
                                                           value="<?php echo (int)$p->UserID; ?>"
                                                           <?php echo in_array((int)$p->UserID, $selectedPlayers, true) ? 'checked' : ''; ?>>
                                                    <span class="nc-picker-option-copy">
                                                        <span class="nc-picker-option-name"><?php echo htmlspecialchars($p->name); ?></span>
                                                        <span class="nc-picker-option-email"><?php echo htmlspecialchars($p->email); ?></span>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($err('player_ids') || $err('player_id')): ?>
                                    <span class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo htmlspecialchars($err('player_ids') ?: $err('player_id')); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="nc-assignment-mode-panel" id="groupAssignmentPanel" data-mode-panel="group" hidden>
                                <select id="player_group" name="player_group" class="form-control<?php echo $cls('player_group'); ?>">
                                    <option value="">— Select a player group —</option>
                                    <?php foreach (($data['groups'] ?? []) as $group): ?>
                                        <option value="<?php echo htmlspecialchars($group->key); ?>" <?php echo $selectedGroup === $group->key ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($group->label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="nc-help-text">The selected group will be expanded into matching active players when saved.</small>
                                <?php if ($err('player_group')): ?>
                                    <span class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo htmlspecialchars($err('player_group')); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Notes -->
                    <div class="nc-field-row full">
                        <div class="nc-field">
                            <label for="notes">
                                <i class="fas fa-sticky-note"></i> Notes (optional)
                            </label>
                            <?php $notesVal = $old['notes'] ?? ($plan->Notes ?? $plan->notes ?? ''); ?>
                            <textarea id="notes" name="notes"
                                      class="form-control<?php echo $cls('notes'); ?>"
                                      rows="5"
                                      placeholder="Add any custom notes for this player/group (e.g., allergies, match-day adjustments, portion changes)."><?php echo htmlspecialchars($notesVal, ENT_QUOTES); ?></textarea>
                            <small class="nc-help-text">Choose a predefined plan above and use Notes for personalisation.</small>
                            <?php if ($err('notes')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('notes')); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 6: Duration + Status + Created Date -->
                    <div class="nc-field-row third">

                        <div class="nc-field">
                            <label for="duration">
                                <i class="fas fa-hourglass-half"></i> Duration (days)
                                <span class="req">*</span>
                            </label>
                            <input type="number"
                                   id="duration" name="duration"
                                   class="form-control<?php echo $cls('duration'); ?>"
                                   value="<?php echo $val('duration', 'Duration'); ?>"
                                   min="1" max="365"
                                   placeholder="e.g. 30">
                            <?php if ($err('duration')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('duration')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="nc-field">
                            <label for="status">
                                <i class="fas fa-toggle-on"></i> Status
                            </label>
                            <?php
                            $currentStatus = isset($old['status']) ? $old['status'] : ($plan->Status ?? 'active');
                            ?>
                            <select id="status" name="status" class="form-control">
                                <option value="active"
                                    <?php echo ($currentStatus === 'active') ? 'selected' : ''; ?>>
                                    Active
                                </option>
                                <option value="inactive"
                                    <?php echo ($currentStatus === 'inactive') ? 'selected' : ''; ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>

                        <div class="nc-field">
                            <label for="created_date">
                                <i class="fas fa-calendar-alt"></i> Created Date
                                <span class="req">*</span>
                            </label>
                            <?php
                            // Format DB date to Y-m-d for <input type="date">
                            $dbDate = !empty($plan->CreatedDate)
                                ? date('Y-m-d', strtotime($plan->CreatedDate))
                                : date('Y-m-d');
                            $dateVal = isset($old['created_date'])
                                ? htmlspecialchars($old['created_date'], ENT_QUOTES)
                                : $dbDate;
                            ?>
                            <input type="date"
                                   id="created_date" name="created_date"
                                   class="form-control<?php echo $cls('created_date'); ?>"
                                   value="<?php echo $dateVal; ?>">
                            <?php if ($err('created_date')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('created_date')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Action buttons -->
                    <div class="nc-form-actions">
                        <!-- Far-left: Return to the old Nutrition page -->
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition"
                           class="btn btn-secondary push-left">
                            <i class="fas fa-undo"></i> Return to Nutrition Page
                        </a>

                        <!-- Right side: Cancel + Submit -->
                        <a href="<?php echo URLROOT; ?>/nutrition"
                           class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Plan
                        </button>
                    </div>

                </form>
            </div><!-- /.nc-card-body -->
        </div><!-- /.nc-card -->

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const main    = document.getElementById('mainContent');
    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }

    const form = document.querySelector('form');
    const templateSelect = document.getElementById('template_id');
    const templateEndpoint = form ? (form.dataset.templateEndpoint || '') : '';
    const assignmentMode = document.getElementById('assignment_mode');
    const individualPanel = document.getElementById('individualAssignmentPanel');
    const groupPanel = document.getElementById('groupAssignmentPanel');
    const picker = document.getElementById('playerPicker');
    const pickerTrigger = document.getElementById('playerPickerTrigger');
    const pickerPanel = document.getElementById('playerPickerPanel');
    const countLabel = document.getElementById('selectedPlayersCount');
    const summaryLabel = document.getElementById('playerPickerSummary');
    const playerCheckboxes = Array.from(document.querySelectorAll('input[name="player_ids[]"]'));
    const selectAllBtn = document.getElementById('selectAllPlayersBtn');
    const clearBtn = document.getElementById('clearPlayersBtn');
    const nutritionFieldIds = [
        'plan_name',
        'protein_percentage',
        'carbohydrate_percentage',
        'fat_percentage',
        'recommended_calories',
        'description',
    ];

    function setPickerOpen(isOpen) {
        if (!pickerPanel || !pickerTrigger || !picker) return;
        pickerPanel.hidden = !isOpen;
        pickerTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        picker.classList.toggle('is-open', isOpen);
    }

    function updatePlayerCount() {
        const selected = playerCheckboxes.filter(input => input.checked);
        const selectedCount = selected.length;

        if (countLabel) {
            countLabel.textContent = selectedCount + (selectedCount === 1 ? ' player selected' : ' players selected');
        }

        if (summaryLabel) {
            summaryLabel.textContent = selectedCount === 0
                ? 'No players selected'
                : selectedCount === 1
                    ? selected[0].closest('.nc-picker-option').querySelector('.nc-picker-option-name').textContent + ' selected'
                    : selectedCount + ' players selected';
        }

        if (picker) {
            picker.classList.toggle('has-selection', selectedCount > 0);
        }
    }

    function clearPickerError() {
        if (!picker) return;
        picker.classList.remove('is-invalid');
        picker.querySelectorAll('.nc-picker-inline-error').forEach(function (node) {
            node.remove();
        });
    }

    function setPickerError(message) {
        if (!picker) return;
        clearPickerError();
        picker.classList.add('is-invalid');
        const error = document.createElement('span');
        error.className = 'invalid-feedback js-error nc-picker-inline-error';
        error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        picker.appendChild(error);
    }

    function clearNutritionFields() {
        nutritionFieldIds.forEach(function (id) {
            const field = document.getElementById(id);
            if (field) {
                field.value = '';
            }
        });
    }

    function populateTemplate(template) {
        if (!template) {
            clearNutritionFields();
            return;
        }

        const mappings = {
            plan_name: template.plan_name || '',
            protein_percentage: template.protein_percentage || '',
            carbohydrate_percentage: template.carbohydrate_percentage || '',
            fat_percentage: template.fat_percentage || '',
            recommended_calories: template.recommended_calories || '',
            description: template.description || '',
        };

        Object.keys(mappings).forEach(function (id) {
            const field = document.getElementById(id);
            if (field) {
                field.value = mappings[id];
            }
        });
    }

    async function loadSelectedTemplate(templateId) {
        // On edit, keep current values when no template is selected.
        if (!templateEndpoint || !templateId) {
            return;
        }

        const response = await fetch(templateEndpoint + encodeURIComponent(templateId));
        const payload = await response.json();
        if (!response.ok || !payload.success || !payload.template) {
            throw new Error(payload.message || 'Unable to load nutrition template.');
        }

        populateTemplate(payload.template);
    }

    function syncAssignmentMode() {
        const mode = assignmentMode ? assignmentMode.value : 'individual';
        if (individualPanel) individualPanel.hidden = mode !== 'individual';
        if (groupPanel) groupPanel.hidden = mode !== 'group';
        if (picker) picker.classList.toggle('is-active', mode === 'individual');
    }

    if (assignmentMode) {
        assignmentMode.addEventListener('change', syncAssignmentMode);
        syncAssignmentMode();
    }

    if (templateSelect) {
        templateSelect.addEventListener('change', function () {
            loadSelectedTemplate(this.value).catch(function (error) {
                console.error(error);
            });
        });
    }

    if (pickerTrigger && pickerPanel) {
        pickerTrigger.addEventListener('click', function () {
            setPickerOpen(pickerPanel.hidden);
        });

        document.addEventListener('click', function (event) {
            if (picker && !picker.contains(event.target)) {
                setPickerOpen(false);
            }
        });
    }

    playerCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updatePlayerCount);
    });
    updatePlayerCount();

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function () {
            playerCheckboxes.forEach(function (checkbox) {
                checkbox.checked = true;
            });
            updatePlayerCount();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            playerCheckboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            updatePlayerCount();
        });
    }

    // Client-side validation mirrors server-side rules for instant feedback
    form.addEventListener('submit', function (e) {
        let valid = true;

        // Clear previous JS-injected errors
        form.querySelectorAll('.js-error').forEach(el => el.remove());
        form.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
        clearPickerError();

        function addError(id, msg) {
            const field = document.getElementById(id);
            if (!field) return;
            field.classList.add('is-invalid');
            const span = document.createElement('span');
            span.className = 'invalid-feedback js-error';
            span.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            field.parentNode.appendChild(span);
            valid = false;
        }

        const templateId  = templateSelect ? templateSelect.value.trim() : '';
        const planName    = document.getElementById('plan_name').value.trim();
        const mode        = assignmentMode ? assignmentMode.value : 'individual';
        const groupValue  = document.getElementById('player_group') ? document.getElementById('player_group').value : '';
        const selectedPlayers = playerCheckboxes.filter(input => input.checked).map(input => input.value).filter(Boolean);
        const notes       = document.getElementById('notes') ? document.getElementById('notes').value.trim() : '';
            const supplements = document.getElementById('supplements') ? document.getElementById('supplements').value.trim() : '';
        const protein     = document.getElementById('protein_percentage').value.trim();
        const carbs       = document.getElementById('carbohydrate_percentage').value.trim();
        const fat         = document.getElementById('fat_percentage').value.trim();
        const calories    = document.getElementById('recommended_calories').value.trim();
        const description = document.getElementById('description').value.trim();
        const duration    = document.getElementById('duration').value.trim();
        const createdDate = document.getElementById('created_date').value.trim();

        // Template selection is optional on edit; keep existing template/value set when not chosen.
        if (!planName) addError('plan_name', 'Plan name is required.');
        if (!protein || isNaN(protein) || +protein < 0 || +protein > 100) addError('protein_percentage', 'Enter a valid percentage between 0 and 100.');
        if (!carbs || isNaN(carbs) || +carbs < 0 || +carbs > 100) addError('carbohydrate_percentage', 'Enter a valid percentage between 0 and 100.');
        if (!fat || isNaN(fat) || +fat < 0 || +fat > 100) addError('fat_percentage', 'Enter a valid percentage between 0 and 100.');
        if (protein && carbs && fat && !isNaN(protein) && !isNaN(carbs) && !isNaN(fat)) {
            const total = (+protein) + (+carbs) + (+fat);
            if (Math.abs(total - 100) > 0.01) {
                addError('fat_percentage', 'Protein (' + protein + '%) + Carbohydrate (' + carbs + '%) + Fat (' + fat + '%) = ' + total.toFixed(2) + '%. They must total exactly 100%.');
            }
        }
        if (!calories || isNaN(calories) || +calories < 500 || +calories > 10000) addError('recommended_calories', 'Calories must be between 500 and 10000.');
        if (description.length > 2000) addError('description', 'Description must be 2000 characters or fewer.');
            if (supplements.length > 2000) addError('supplements', 'Supplements must be 2000 characters or fewer.');
        if (mode === 'group') {
            if (!groupValue) addError('player_group', 'Please select a player group.');
        } else if (!selectedPlayers.length) {
            setPickerError('Please select at least one player.');
            valid = false;
        }

        if (notes && notes.length > 1000) addError('notes', 'Notes must be 1000 characters or fewer.');

        if (!duration || isNaN(duration) || +duration <= 0) {
            addError('duration', 'Duration must be a positive number.');
        }
        if (!createdDate) addError('created_date', 'Created date is required.');

        if (!valid) e.preventDefault();
    });
});
</script>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
</body>
</html>
