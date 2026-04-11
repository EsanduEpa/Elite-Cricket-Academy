<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/supplements.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition_crud.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

<?php
$supplementPlans = $data['supplement_plans'] ?? [];
$players = $data['players'] ?? [];
$groups = $data['groups'] ?? [];
$planOptions = $data['plan_options'] ?? [];
$library = $data['supplement_plan_library'] ?? [];
$old = $data['old'] ?? [];
$errors = $data['errors'] ?? [];
$defaultPlanName = array_key_first($library) ?: ($planOptions[0] ?? '');

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
} elseif (!empty($old['player_id'])) {
    $selectedPlayers[] = (int)$old['player_id'];
}
$selectedPlayers = array_values(array_unique($selectedPlayers));

$selectedPlanName = $old['supplement_plan_name'] ?? '';
$selectedNotes = $old['notes'] ?? '';
$selectedDosage = $old['dosage'] ?? '';
$selectedDetails = $old['supplement_details'] ?? '';
$selectedDuration = $old['duration'] ?? '';
$selectedStatus = $old['status'] ?? 'active';
$selectedCreatedDate = $old['created_date'] ?? date('Y-m-d');
$selectedPlanId = (int)($old['plan_id'] ?? 0);

$totalPlans = count($supplementPlans);
$activePlans = 0;
$inactivePlans = 0;
$groupPlans = 0;

$planMap = [];
foreach ($supplementPlans as $plan) {
    $status = strtolower($plan->Status ?? 'active');
    if ($status === 'active') {
        $activePlans++;
    } else {
        $inactivePlans++;
    }

    $assignedCount = (int)($plan->assigned_player_count ?? 0);
    if ($assignedCount > 1) {
        $groupPlans++;
    }

    $assignedIds = [];
    if (!empty($plan->assigned_player_ids)) {
        foreach (explode(',', (string)$plan->assigned_player_ids) as $assignedId) {
            $assignedId = (int)trim($assignedId);
            if ($assignedId > 0) {
                $assignedIds[] = $assignedId;
            }
        }
    }

    $planMap[] = [
        'PlanID' => (int)$plan->PlanID,
        'SupplementPlanName' => (string)($plan->SupplementPlanName ?? ''),
        'SupplementDetails' => (string)($plan->SupplementDetails ?? ''),
        'Dosage' => (string)($plan->Dosage ?? ''),
        'Notes' => (string)($plan->Notes ?? $plan->notes ?? ''),
        'Duration' => (int)($plan->Duration ?? 0),
        'Status' => $status,
        'CreatedDate' => !empty($plan->CreatedDate) ? date('Y-m-d', strtotime($plan->CreatedDate)) : date('Y-m-d'),
        'assigned_player_count' => $assignedCount,
        'assigned_player_ids' => $assignedIds,
        'assigned_player_names' => (string)($plan->assigned_player_names ?? ''),
        'assigned_player_emails' => (string)($plan->assigned_player_emails ?? ''),
    ];
}

$planMapJson = json_encode($planMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
$libraryJson = json_encode($library, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<div class="player-layout supplement-page">
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

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link"><i class="fas fa-calendar-check"></i><span>Schedule & Bookings</span></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link"><i class="fas fa-dumbbell"></i><span>Workout Plans</span></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link"><i class="fas fa-apple-alt"></i><span>Nutrition Plans</span></a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link"><i class="fas fa-capsules"></i><span>Supplements</span></a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link"><i class="fas fa-user-injured"></i><span>Injury Reports</span></a>
                </li>
            </ul>
        </nav>

        <div class="trainer-profile">
            <div class="trainer-avatar"><i class="fas fa-user-tie"></i></div>
            <div class="trainer-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Trainer'); ?></div>
            <div class="trainer-role">Fitness Trainer</div>
            <div class="profile-actions">
                <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile"><i class="fas fa-user-cog"></i></a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <div class="dashboard-header nc-hero">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-capsules"></i> Supplement Plans Management</h1>
                    <p>Create predefined supplement plans, assign them to individual players or groups, and keep notes for custom adjustments.</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-training nc-btn nc-btn-primary js-supplement-cta" onclick="openSupplementPlanModal(null, '<?php echo htmlspecialchars($defaultPlanName, ENT_QUOTES); ?>')">
                        <i class="fas fa-plus"></i>Add New Plan
                    </button>
                    <button class="btn btn-refresh nc-btn nc-btn-refresh" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i>
                        <div class="current-time"><?php echo date('H:i'); ?></div>
                    </button>
                </div>
            </div>
        </div>

        <div class="supplement-stats-grid" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px;">
            <div class="supplement-card" style="margin-bottom:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Total Plans</div>
                        <div style="font-size:30px;font-weight:800;color:#1f2937;"><?php echo (int)$totalPlans; ?></div>
                    </div>
                    <i class="fas fa-box-open" style="font-size:24px;color:#4A90E2;"></i>
                </div>
            </div>
            <div class="supplement-card" style="margin-bottom:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Active</div>
                        <div style="font-size:30px;font-weight:800;color:#16a34a;"><?php echo (int)$activePlans; ?></div>
                    </div>
                    <i class="fas fa-check-circle" style="font-size:24px;color:#16a34a;"></i>
                </div>
            </div>
            <div class="supplement-card" style="margin-bottom:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Group Plans</div>
                        <div style="font-size:30px;font-weight:800;color:#8b5cf6;"><?php echo (int)$groupPlans; ?></div>
                    </div>
                    <i class="fas fa-layer-group" style="font-size:24px;color:#8b5cf6;"></i>
                </div>
            </div>
            <div class="supplement-card" style="margin-bottom:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <div style="font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Needs Review</div>
                        <div style="font-size:30px;font-weight:800;color:#f59e0b;"><?php echo (int)$inactivePlans; ?></div>
                    </div>
                    <i class="fas fa-clock" style="font-size:24px;color:#f59e0b;"></i>
                </div>
            </div>
        </div>

        <?php flash('supplement_message'); ?>

        <div class="schedule-card nc-table-card supplement-table-card">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-capsules"></i> Your Supplement Plans</h2>
                    <div class="table-controls nc-table-controls" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                        <div class="nc-search-wrap" style="position:relative;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="supplementSearch" placeholder="Search plans...">
                        </div>
                        <select id="supplementStatusFilter" class="nc-status-filter">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-content table-container nc-table-shell">
                <table class="dashboard-table nc-nutrition-table" id="supplementTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-capsules"></i> Plan</th>
                            <th><i class="fas fa-users"></i> Assigned To</th>
                            <th><i class="fas fa-hourglass-half"></i> Duration</th>
                            <th><i class="fas fa-sticky-note"></i> Notes</th>
                            <th><i class="fas fa-chart-line"></i> Status</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($supplementPlans)): ?>
                            <?php foreach ($supplementPlans as $plan): ?>
                                <?php
                                $rowId = (int)$plan->PlanID;
                                $rowStatus = strtolower($plan->Status ?? 'active');
                                $assignmentCount = (int)($plan->assigned_player_count ?? 0);
                                $assignedNames = (string)($plan->assigned_player_names ?? '');
                                $assignedEmails = (string)($plan->assigned_player_emails ?? '');
                                $notesText = (string)($plan->Notes ?? $plan->notes ?? '');
                                $planNameText = (string)($plan->SupplementPlanName ?? 'Untitled Plan');
                                ?>
                                <tr class="supplement-row" data-status="<?php echo htmlspecialchars($rowStatus); ?>" data-plan-id="<?php echo $rowId; ?>">
                                    <td class="plan-details nc-plan-cell" data-label="Plan">
                                        <div class="plan-info">
                                            <div class="plan-icon"><i class="fas fa-capsules"></i></div>
                                            <div class="plan-text">
                                                <strong class="nc-plan-name"><?php echo htmlspecialchars($planNameText); ?></strong>
                                                <span class="plan-description">#<?php echo $rowId; ?> | <?php echo htmlspecialchars(date('M d, Y', strtotime($plan->CreatedDate ?? 'now'))); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="player-info nc-player-cell" data-label="Assigned To">
                                        <div class="player-avatar"><i class="fas fa-users"></i></div>
                                        <div class="player-details">
                                            <span class="player-name">
                                                <?php if ($assignmentCount > 1): ?>
                                                    <?php echo (int)$assignmentCount; ?> players
                                                <?php elseif ($assignmentCount === 1): ?>
                                                    <?php echo htmlspecialchars($assignedNames ?: '1 player'); ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </span>
                                            <span class="player-email" title="<?php echo htmlspecialchars($assignedEmails); ?>">
                                                <?php echo htmlspecialchars($assignmentCount > 1 ? $assignedNames : $assignedEmails); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="duration-info nc-duration-cell" data-label="Duration">
                                        <div class="duration-display nc-duration-badge">
                                            <i class="fas fa-hourglass-half"></i>
                                            <span><?php echo (int)($plan->Duration ?? 0); ?> day<?php echo ((int)($plan->Duration ?? 0) !== 1) ? 's' : ''; ?></span>
                                        </div>
                                    </td>
                                    <td class="notes-info nc-notes-cell" data-label="Notes">
                                        <?php if (trim($notesText) === ''): ?>
                                            -
                                        <?php else: ?>
                                            <?php $notesPreview = mb_strlen($notesText) > 80 ? mb_substr($notesText, 0, 80) . '...' : $notesText; ?>
                                            <span class="nc-notes-preview" title="<?php echo htmlspecialchars($notesText, ENT_QUOTES); ?>"><?php echo htmlspecialchars($notesPreview, ENT_QUOTES); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-badge status-<?php echo htmlspecialchars($rowStatus); ?> nc-status-pill">
                                            <i class="fas fa-<?php echo $rowStatus === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                            <?php echo ucfirst($rowStatus); ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell nc-actions-cell" data-label="Actions">
                                        <div class="profile-actions nc-row-actions">
                                            <button type="button" class="profile-action view nc-row-btn nc-row-btn-view" onclick="openSupplementDetailsModal(<?php echo $rowId; ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="profile-action edit nc-row-btn nc-row-btn-edit" onclick="openSupplementPlanModal(<?php echo $rowId; ?>)" title="Edit Plan">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="profile-action delete nc-row-btn nc-row-btn-delete" onclick="deleteSupplementPlan(<?php echo $rowId; ?>)" title="Delete Plan">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-state">
                                <td colspan="6" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-capsules" style="font-size:3rem;color:#4A90E2;margin-bottom:15px;"></i>
                                        <h3>No Supplement Plans Found</h3>
                                        <p>Start by creating your first supplement plan and assign it to players or a group.</p>
                                        <button type="button" class="btn btn-primary nc-empty-btn nc-btn nc-btn-primary" onclick="openSupplementPlanModal(null, '<?php echo htmlspecialchars($defaultPlanName, ENT_QUOTES); ?>')">
                                            <i class="fas fa-plus"></i> Create First Plan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="supplement-card" style="margin-top:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
                <div>
                    <h2 style="margin:0 0 6px 0;font-size:22px;color:#1f2937;"><i class="fas fa-lightbulb"></i> Suggested Plan Library</h2>
                    <p style="margin:0;color:#64748b;">Pick one of these plans, then adjust the notes for the player or group.</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                <?php foreach ($library as $name => $template): ?>
                    <div class="supplement-card" style="margin-bottom:0;border:1px solid rgba(74,144,226,.12);background:linear-gradient(180deg,#fff, #f8fbff);">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;">
                            <div>
                                <div class="supplement-category category-<?php echo htmlspecialchars($template['category'] ?? 'performance'); ?>" style="margin-bottom:10px;">
                                    <?php echo htmlspecialchars(ucfirst($template['category'] ?? 'performance')); ?>
                                </div>
                                <h3 style="margin:0;font-size:18px;color:#1f2937;"><?php echo htmlspecialchars($name); ?></h3>
                            </div>
                            <i class="fas fa-capsules" style="font-size:24px;color:#4A90E2;"></i>
                        </div>
                        <div class="dosage-box">
                            <h5>Suggested Dosage</h5>
                            <p><?php echo htmlspecialchars($template['dosage'] ?? ''); ?></p>
                        </div>
                        <div style="font-size:14px;line-height:1.7;color:#475569;white-space:pre-line;">
                            <?php echo htmlspecialchars($template['details'] ?? ''); ?>
                        </div>
                        <div style="margin-top:14px;">
                            <button type="button" class="btn btn-training nc-btn nc-btn-primary" style="width:100%;justify-content:center;" onclick="openSupplementPlanModal(null, '<?php echo htmlspecialchars($name, ENT_QUOTES); ?>')">
                                <i class="fas fa-plus"></i> Use This Plan
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div id="supplementPlanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2);">
            <h2 id="supplementModalTitle"><i class="fas fa-capsules"></i> Add New Supplement Plan</h2>
            <span class="close" onclick="closeSupplementPlanModal()">&times;</span>
        </div>
        <form id="supplementPlanForm" method="POST" action="<?php echo URLROOT; ?>/trainer/addSupplementPlan">
            <input type="hidden" id="supplement_plan_id" name="plan_id" value="<?php echo $selectedPlanId; ?>">
            <input type="hidden" id="supplement_plan_name" name="supplement_plan_name" value="<?php echo htmlspecialchars($selectedPlanName, ENT_QUOTES); ?>">
            <input type="hidden" id="supplement_details" name="supplement_details" value="<?php echo htmlspecialchars($selectedDetails, ENT_QUOTES); ?>">
            <input type="hidden" id="dosage" name="dosage" value="<?php echo htmlspecialchars($selectedDosage, ENT_QUOTES); ?>">
            <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
                <div class="form-group">
                    <label for="supplement_plan_name_select" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                        <i class="fas fa-tags" style="color:#4A90E2;margin-right:8px;"></i>
                        Select Plan <span style="color:#ff6b6b;">*</span>
                    </label>
                    <select id="supplement_plan_name_select" style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;background:white;">
                        <option value="">Choose a predefined plan...</option>
                        <?php foreach ($planOptions as $option): ?>
                            <option value="<?php echo htmlspecialchars($option, ENT_QUOTES); ?>" <?php echo $selectedPlanName === $option ? 'selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                        <i class="fas fa-tags" style="color:#4A90E2;margin-right:8px;"></i>
                        Selected Plan
                    </label>
                    <div id="selectedTemplateSummary" style="padding:12px 15px;border:1px dashed rgba(74,144,226,.35);border-radius:8px;background:#f8fbff;color:#1f2937;min-height:48px;display:flex;align-items:center;">
                        <?php echo $selectedPlanName ? htmlspecialchars($selectedPlanName) : 'Select a plan from the suggested library above.'; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="assignment_mode" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                        <i class="fas fa-layer-group" style="color:#4A90E2;margin-right:8px;"></i>
                        Assign To <span style="color:#ff6b6b;">*</span>
                    </label>
                    <select id="assignment_mode" name="assignment_mode" required style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;background:white;">
                        <option value="individual" <?php echo $assignmentMode === 'individual' ? 'selected' : ''; ?>>Individual Players</option>
                        <option value="group" <?php echo $assignmentMode === 'group' ? 'selected' : ''; ?>>Player Group</option>
                    </select>
                </div>

                <div class="form-group" id="individualAssignmentPanel">
                    <label style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                        <i class="fas fa-user-friends" style="color:#4A90E2;margin-right:8px;"></i>
                        Select Players <span style="color:#ff6b6b;">*</span>
                    </label>
                    <div class="supplement-picker" id="supplementPicker" style="border:1px solid #dbe6f5;border-radius:12px;padding:14px;background:#f8fbff;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
                            <strong style="color:#1f2937;">Choose one or more players</strong>
                            <span id="selectedPlayersCount" style="font-size:13px;color:#64748b;">0 selected</span>
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                            <button type="button" class="btn btn-secondary btn-sm" id="selectAllPlayersBtn"><i class="fas fa-check-double"></i> Select All</button>
                            <button type="button" class="btn btn-secondary btn-sm" id="clearPlayersBtn"><i class="fas fa-eraser"></i> Clear</button>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:10px;max-height:240px;overflow:auto;padding-right:2px;">
                            <?php foreach ($players as $player): ?>
                                <label style="display:flex;gap:10px;align-items:flex-start;border:1px solid rgba(74,144,226,.12);background:#fff;border-radius:10px;padding:10px 12px;cursor:pointer;">
                                    <input type="checkbox" name="player_ids[]" value="<?php echo (int)$player->UserID; ?>" <?php echo in_array((int)$player->UserID, $selectedPlayers, true) ? 'checked' : ''; ?> style="margin-top:4px;">
                                    <span style="display:flex;flex-direction:column;gap:2px;">
                                        <span style="font-weight:700;color:#1f2937;"><?php echo htmlspecialchars($player->name); ?></span>
                                        <span style="font-size:13px;color:#64748b;"><?php echo htmlspecialchars($player->email); ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="playerIdsError" style="display:<?php echo isset($errors['player_ids']) || isset($errors['player_id']) ? 'block' : 'none'; ?>;">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($errors['player_ids'] ?? $errors['player_id'] ?? ''); ?>
                    </div>
                </div>

                <div class="form-group" id="groupAssignmentPanel" hidden>
                    <label for="player_group" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                        <i class="fas fa-users" style="color:#4A90E2;margin-right:8px;"></i>
                        Player Group <span style="color:#ff6b6b;">*</span>
                    </label>
                    <select id="player_group" name="player_group" style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;background:white;">
                        <option value="">Choose a player group...</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo htmlspecialchars($group->key, ENT_QUOTES); ?>" <?php echo $selectedGroup === $group->key ? 'selected' : ''; ?>><?php echo htmlspecialchars($group->label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small style="display:block;margin-top:8px;color:#64748b;">The selected group will expand into matching active players when saved.</small>
                    <div class="invalid-feedback" id="playerGroupError" style="display:<?php echo isset($errors['player_group']) ? 'block' : 'none'; ?>;">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($errors['player_group'] ?? ''); ?>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label for="duration" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                            <i class="fas fa-hourglass-half" style="color:#4A90E2;margin-right:8px;"></i>
                            Duration (Days) <span style="color:#ff6b6b;">*</span>
                        </label>
                        <input type="number" id="duration" name="duration" min="1" max="365" required value="<?php echo htmlspecialchars((string)$selectedDuration, ENT_QUOTES); ?>" style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;" placeholder="Enter duration in days">
                        <?php if (isset($errors['duration'])): ?>
                            <div class="invalid-feedback" style="display:block;"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['duration']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                            <i class="fas fa-pills" style="color:#4A90E2;margin-right:8px;"></i>
                            Template Dosage
                        </label>
                        <div id="selectedDosageSummary" style="padding:12px 15px;border:1px dashed rgba(74,144,226,.35);border-radius:8px;background:#f8fbff;color:#1f2937;min-height:48px;display:flex;align-items:center;">
                            Dosage is auto-filled from selected plan.
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                        <i class="fas fa-sticky-note" style="color:#4A90E2;margin-right:8px;"></i>
                        Notes (optional)
                    </label>
                    <textarea id="notes" name="notes" rows="4" style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;resize:vertical;" placeholder="Add any custom notes, allergies, timing changes, or player-specific adjustments..."><?php echo htmlspecialchars($selectedNotes, ENT_QUOTES); ?></textarea>
                    <?php if (isset($errors['notes'])): ?>
                        <div class="invalid-feedback" style="display:block;"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['notes']); ?></div>
                    <?php endif; ?>
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;">
                    <div class="form-group">
                        <label for="status" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                            <i class="fas fa-toggle-on" style="color:#4A90E2;margin-right:8px;"></i>
                            Status
                        </label>
                        <select id="status" name="status" style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;background:white;">
                            <option value="active" <?php echo $selectedStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $selectedStatus === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="created_date" style="color:#333;font-weight:600;margin-bottom:8px;display:block;">
                            <i class="fas fa-calendar-alt" style="color:#4A90E2;margin-right:8px;"></i>
                            Created Date <span style="color:#ff6b6b;">*</span>
                        </label>
                        <input type="date" id="created_date" name="created_date" required value="<?php echo htmlspecialchars($selectedCreatedDate, ENT_QUOTES); ?>" style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                        <?php if (isset($errors['created_date'])): ?>
                            <div class="invalid-feedback" style="display:block;"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['created_date']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;padding:20px 25px;display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;">
                <button type="button" onclick="closeSupplementPlanModal()" style="background:#e5e7eb;color:#374151;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:500;">
                    Cancel
                </button>
                <button type="submit" id="supplementSubmitBtn" style="background:linear-gradient(135deg,#4A90E2,#5BA0F2);color:white;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:500;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-save"></i><span>Create Plan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="viewSupplementModal" class="modal">
    <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2);">
            <h2><i class="fas fa-eye"></i> Supplement Plan Details</h2>
            <span class="close" onclick="closeSupplementDetailsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="viewSupplementContent"></div>
        </div>
        <div class="modal-footer" style="background:#f8fafc;padding:20px 25px;display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;">
            <button type="button" onclick="closeSupplementDetailsModal()" style="background:#4A90E2;color:white;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:500;">
                Close
            </button>
        </div>
    </div>
</div>

<form id="deleteSupplementForm" method="POST" style="display:none;"></form>

<script>
    const supplementPlanLibrary = <?php echo $libraryJson ?: '{}'; ?>;
    const supplementPlanMap = Object.fromEntries((<?php echo $planMapJson ?: '[]'; ?>).map((plan) => [String(plan.PlanID), plan]));
    const oldSupplementState = <?php echo json_encode($old, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?: '{}'; ?>;
    const defaultSupplementPlanName = <?php echo json_encode($defaultPlanName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    function setSupplementPlanButtonsBusy() {
        const buttons = document.querySelectorAll('.js-supplement-cta');
        buttons.forEach((button) => {
            if (!button.dataset.originalHtml) {
                button.dataset.originalHtml = button.innerHTML;
            }

            button.classList.add('is-opening');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening...';
        });

        window.setTimeout(() => {
            buttons.forEach((button) => {
                button.classList.remove('is-opening');
                button.disabled = false;
                if (button.dataset.originalHtml) {
                    button.innerHTML = button.dataset.originalHtml;
                }
            });
        }, 420);
    }

    function setAssignmentMode(mode) {
        const individualPanel = document.getElementById('individualAssignmentPanel');
        const groupPanel = document.getElementById('groupAssignmentPanel');
        const picker = document.getElementById('supplementPicker');
        const assignmentMode = document.getElementById('assignment_mode');

        if (assignmentMode) {
            assignmentMode.value = mode;
        }
        if (individualPanel) {
            individualPanel.hidden = mode !== 'individual';
        }
        if (groupPanel) {
            groupPanel.hidden = mode !== 'group';
        }
        if (picker) {
            picker.style.display = mode === 'individual' ? 'block' : 'none';
        }
    }

    function updateSelectedPlayersSummary() {
        const checkboxes = Array.from(document.querySelectorAll('input[name="player_ids[]"]'));
        const selected = checkboxes.filter((checkbox) => checkbox.checked);
        const countLabel = document.getElementById('selectedPlayersCount');

        if (countLabel) {
            countLabel.textContent = selected.length + (selected.length === 1 ? ' player selected' : ' players selected');
        }
    }

    function applyPlanTemplate(planName) {
        const template = supplementPlanLibrary[planName];
        if (!template) {
            return;
        }

        const planNameField = document.getElementById('supplement_plan_name');
        const planNameSelect = document.getElementById('supplement_plan_name_select');
        const detailsField = document.getElementById('supplement_details');
        const dosageField = document.getElementById('dosage');
        const summary = document.getElementById('selectedTemplateSummary');
        const dosageSummary = document.getElementById('selectedDosageSummary');

        if (planNameField) {
            planNameField.value = planName;
        }
        if (planNameSelect) {
            planNameSelect.value = planName;
        }
        if (detailsField) {
            detailsField.value = template.details || '';
        }
        if (dosageField) {
            dosageField.value = template.dosage || '';
        }
        if (summary) {
            summary.textContent = planName;
        }
        if (dosageSummary) {
            dosageSummary.textContent = template.dosage || 'No dosage defined for this plan.';
        }
    }

    function syncPlayerSelection(selectedIds) {
        const selected = new Set((selectedIds || []).map((value) => String(value)));
        document.querySelectorAll('input[name="player_ids[]"]').forEach((checkbox) => {
            checkbox.checked = selected.has(String(checkbox.value));
        });
        updateSelectedPlayersSummary();
    }

    function resetSupplementForm() {
        const form = document.getElementById('supplementPlanForm');
        if (!form) {
            return;
        }
        form.reset();
        document.getElementById('supplement_plan_id').value = '';
        document.getElementById('supplementPlanModal').dataset.mode = 'create';
        document.getElementById('supplementModalTitle').innerHTML = '<i class="fas fa-capsules"></i> Add New Supplement Plan';
        document.getElementById('supplementSubmitBtn').innerHTML = '<i class="fas fa-save"></i><span>Create Plan</span>';
        setAssignmentMode('individual');
        document.querySelectorAll('input[name="player_ids[]"]').forEach((checkbox) => { checkbox.checked = false; });
        const summary = document.getElementById('selectedTemplateSummary');
        if (summary) {
            summary.textContent = defaultSupplementPlanName || 'Select a plan from the suggested library above.';
        }
        const dosageSummary = document.getElementById('selectedDosageSummary');
        if (dosageSummary) {
            dosageSummary.textContent = 'Dosage is auto-filled from selected plan.';
        }

        if (defaultSupplementPlanName) {
            applyPlanTemplate(defaultSupplementPlanName);
        }
    }

    function openSupplementPlanModal(planId = null, forcePlanName = '') {
        setSupplementPlanButtonsBusy();

        const modal = document.getElementById('supplementPlanModal');
        const form = document.getElementById('supplementPlanForm');
        const planNameField = document.getElementById('supplement_plan_name');
        const planNameSelect = document.getElementById('supplement_plan_name_select');
        const planIdField = document.getElementById('supplement_plan_id');
        const title = document.getElementById('supplementModalTitle');
        const submitBtn = document.getElementById('supplementSubmitBtn');
        const mode = planId && supplementPlanMap[String(planId)] ? 'edit' : 'create';
        const plan = planId && supplementPlanMap[String(planId)] ? supplementPlanMap[String(planId)] : null;

        if (mode === 'edit' && plan) {
            form.action = '<?php echo URLROOT; ?>/trainer/updateSupplementPlan/' + plan.PlanID;
            planIdField.value = plan.PlanID;
            title.innerHTML = '<i class="fas fa-edit"></i> Edit Supplement Plan';
            submitBtn.innerHTML = '<i class="fas fa-save"></i><span>Update Plan</span>';

            planNameField.value = plan.SupplementPlanName || defaultSupplementPlanName || '';
            if (planNameSelect) {
                planNameSelect.value = planNameField.value;
            }
            document.getElementById('supplement_details').value = plan.SupplementDetails || '';
            document.getElementById('dosage').value = plan.Dosage || '';
            document.getElementById('notes').value = plan.Notes || '';
            document.getElementById('duration').value = plan.Duration || '';
            document.getElementById('status').value = plan.Status || 'active';
            document.getElementById('created_date').value = plan.CreatedDate || '<?php echo date('Y-m-d'); ?>';
            const summary = document.getElementById('selectedTemplateSummary');
            if (summary) {
                summary.textContent = plan.SupplementPlanName || defaultSupplementPlanName || 'Selected plan';
            }
            const dosageSummary = document.getElementById('selectedDosageSummary');
            if (dosageSummary) {
                dosageSummary.textContent = plan.Dosage || 'No dosage defined for this plan.';
            }

            const selectedIds = Array.isArray(plan.assigned_player_ids) ? plan.assigned_player_ids : [];
            syncPlayerSelection(selectedIds);
            setAssignmentMode(selectedIds.length > 1 ? 'group' : 'individual');
        } else {
            form.action = '<?php echo URLROOT; ?>/trainer/addSupplementPlan';
            planIdField.value = '';
            title.innerHTML = '<i class="fas fa-capsules"></i> Add New Supplement Plan';
            submitBtn.innerHTML = '<i class="fas fa-save"></i><span>Create Plan</span>';
            resetSupplementForm();
            applyPlanTemplate(forcePlanName || defaultSupplementPlanName);
        }

        if (mode === 'create') {
            if (oldSupplementState.assignment_mode) {
                setAssignmentMode(oldSupplementState.assignment_mode);
            }
            if (oldSupplementState.supplement_plan_name) {
                applyPlanTemplate(oldSupplementState.supplement_plan_name);
            }
            const oldPlayers = Array.isArray(oldSupplementState.player_ids) ? oldSupplementState.player_ids : [];
            if (oldPlayers.length > 0) {
                syncPlayerSelection(oldPlayers);
            }
            if (oldSupplementState.player_group) {
                document.getElementById('player_group').value = oldSupplementState.player_group;
            }
            if (oldSupplementState.supplement_details) {
                document.getElementById('supplement_details').value = oldSupplementState.supplement_details;
            }
            if (oldSupplementState.dosage) {
                document.getElementById('dosage').value = oldSupplementState.dosage;
            }
            if (oldSupplementState.notes) {
                document.getElementById('notes').value = oldSupplementState.notes;
            }
            if (oldSupplementState.duration) {
                document.getElementById('duration').value = oldSupplementState.duration;
            }
            if (oldSupplementState.status) {
                document.getElementById('status').value = oldSupplementState.status;
            }
            if (oldSupplementState.created_date) {
                document.getElementById('created_date').value = oldSupplementState.created_date;
            }
        }

        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeSupplementPlanModal() {
        const modal = document.getElementById('supplementPlanModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            resetSupplementForm();
        }, 300);
    }

    function openSupplementDetailsModal(planId) {
        const plan = supplementPlanMap[String(planId)];
        if (!plan) {
            return;
        }

        const modal = document.getElementById('viewSupplementModal');
        const content = document.getElementById('viewSupplementContent');
        const assignments = plan.assigned_player_names || plan.assigned_player_emails || '-';

        content.innerHTML = `
            <div style="display:grid;gap:16px;">
                <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                    <strong>Plan</strong>
                    <span>${escapeHtml(plan.SupplementPlanName || 'Untitled Plan')}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                    <strong>Assigned To</strong>
                    <span>${escapeHtml(assignments)}</span>
                </div>
                <div style="padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                    <strong style="display:block;margin-bottom:8px;">Details</strong>
                    <div style="white-space:pre-line;color:#374151;line-height:1.7;">${escapeHtml(plan.SupplementDetails || '')}</div>
                </div>
                <div style="padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                    <strong style="display:block;margin-bottom:8px;">Duration</strong>
                    <div>${escapeHtml(String(plan.Duration || 0))} day${Number(plan.Duration || 0) === 1 ? '' : 's'}</div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
                    <div style="padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                        <strong style="display:block;margin-bottom:8px;">Status</strong>
                        <div>${escapeHtml((plan.Status || 'active').toString())}</div>
                    </div>
                    <div style="padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                        <strong style="display:block;margin-bottom:8px;">Created Date</strong>
                        <div>${escapeHtml(plan.CreatedDate || '')}</div>
                    </div>
                </div>
                <div style="padding:15px;background:#f8fafc;border-radius:10px;border-left:4px solid #4A90E2;">
                    <strong style="display:block;margin-bottom:8px;">Notes</strong>
                    <div style="white-space:pre-line;color:#374151;line-height:1.7;">${escapeHtml(plan.Notes || '') || 'No notes added.'}</div>
                </div>
            </div>
        `;

        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeSupplementDetailsModal() {
        const modal = document.getElementById('viewSupplementModal');
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    function deleteSupplementPlan(planId) {
        if (!confirm('Are you sure you want to delete this supplement plan?')) {
            return;
        }

        const form = document.getElementById('deleteSupplementForm');
        form.action = '<?php echo URLROOT; ?>/trainer/deleteSupplementPlan/' + planId;
        form.submit();
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('trainerSidebar');
        const mainContent = document.getElementById('mainContent');
        const searchInput = document.getElementById('supplementSearch');
        const statusFilter = document.getElementById('supplementStatusFilter');
        const planNameField = document.getElementById('supplement_plan_name');
        const planNameSelect = document.getElementById('supplement_plan_name_select');
        const assignmentModeField = document.getElementById('assignment_mode');
        const selectAllBtn = document.getElementById('selectAllPlayersBtn');
        const clearBtn = document.getElementById('clearPlayersBtn');
        const playerCheckboxes = Array.from(document.querySelectorAll('input[name="player_ids[]"]'));

        if (sidebarToggle && sidebar && mainContent) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterSupplementTable);
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', filterSupplementTable);
        }

        if (planNameSelect) {
            planNameSelect.addEventListener('change', function () {
                if (this.value) {
                    applyPlanTemplate(this.value);
                }
            });
        }

        if (assignmentModeField) {
            assignmentModeField.addEventListener('change', function () {
                setAssignmentMode(this.value);
            });
        }

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function () {
                playerCheckboxes.forEach((checkbox) => { checkbox.checked = true; });
                updateSelectedPlayersSummary();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                playerCheckboxes.forEach((checkbox) => { checkbox.checked = false; });
                updateSelectedPlayersSummary();
            });
        }

        playerCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateSelectedPlayersSummary);
        });

        setAssignmentMode(assignmentModeField?.value || '<?php echo htmlspecialchars($assignmentMode, ENT_QUOTES); ?>');
        updateSelectedPlayersSummary();

        if (defaultSupplementPlanName) {
            applyPlanTemplate(defaultSupplementPlanName);
        }

        if (Object.keys(oldSupplementState).length > 0 || <?php echo !empty($errors) ? 'true' : 'false'; ?>) {
            openSupplementPlanModal(null, oldSupplementState.supplement_plan_name || '');
        }
    });

    function filterSupplementTable() {
        const searchTerm = document.getElementById('supplementSearch')?.value.toLowerCase() || '';
        const statusFilterValue = document.getElementById('supplementStatusFilter')?.value || 'all';
        const rows = document.querySelectorAll('#supplementTable tbody .supplement-row');

        rows.forEach((row) => {
            const status = row.getAttribute('data-status') || '';
            const text = row.textContent.toLowerCase();
            const matchesSearch = text.includes(searchTerm);
            const matchesStatus = statusFilterValue === 'all' || status === statusFilterValue;
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });

        const visibleRows = Array.from(rows).filter((row) => row.style.display !== 'none');
        const existingNoResults = document.querySelector('.supplement-no-results');
        if (visibleRows.length === 0 && !existingNoResults) {
            const tbody = document.querySelector('#supplementTable tbody');
            if (!tbody) return;
            const row = document.createElement('tr');
            row.className = 'supplement-no-results';
            row.innerHTML = `
                <td colspan="6">
                    <div class="empty-content">
                        <div class="empty-icon"><i class="fas fa-search"></i></div>
                        <h3>No Plans Found</h3>
                        <p>No supplement plans match your current search or filter criteria.</p>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        } else if (visibleRows.length > 0 && existingNoResults) {
            existingNoResults.remove();
        }
    }

    window.onclick = function (event) {
        const createModal = document.getElementById('supplementPlanModal');
        const detailsModal = document.getElementById('viewSupplementModal');
        if (event.target === createModal) {
            closeSupplementPlanModal();
        }
        if (event.target === detailsModal) {
            closeSupplementDetailsModal();
        }
    };
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
