<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition_crud.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/table-consistency.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'nutrition'; ?>

<?php
$plans = $data['plans'] ?? [];
$templates = $data['nutrition_templates'] ?? [];

if ($plans instanceof Traversable) {
    $plans = iterator_to_array($plans);
}
if (!is_array($plans)) {
    $plans = [];
}

if ($templates instanceof Traversable) {
    $templates = iterator_to_array($templates);
}
if (!is_array($templates)) {
    $templates = [];
}

$resolvePlanName = static function ($plan): string {
    $name = trim((string)($plan->PlanName ?? $plan->nutritionPlanName ?? ''));
    if ($name !== '') {
        return $name;
    }

    $dietDetails = (string)($plan->DietDetails ?? '');
    if ($dietDetails !== '') {
        foreach (preg_split('/\r\n|\r|\n/', $dietDetails) as $line) {
            $line = trim((string)$line);
            if (stripos($line, 'Plan:') === 0) {
                $parsed = trim(substr($line, strlen('Plan:')));
                if ($parsed !== '') {
                    return $parsed;
                }
            }
        }
    }

    return 'Untitled Plan';
};
?>

<div class="trainer-layout nc-page">

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
            <div class="trainer-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Trainer'); ?></div>
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

        <div class="nc-header">
            <div class="nc-header-text">
                <h1><i class="fas fa-apple-alt"></i> Nutrition Plans</h1>
                <p>Create and manage personalised nutrition plans for your players</p>
            </div>
            <div class="nc-header-actions">
                <a href="<?php echo URLROOT; ?>/nutrition/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> New Plan
                </a>
            </div>
        </div>

        <?php flash('nutrition_message'); ?>

        <!-- Plans table card -->
        <div class="nc-card nc-workout-style-table">
            <div class="nc-card-header">
                <h2><i class="fas fa-list-ul"></i> All Nutrition Plans
                    <span style="font-weight:400;color:#adb5bd;font-size:.85rem;margin-left:.5rem;">
                        (<?php echo count($plans); ?>)
                    </span>
                </h2>
            </div>

            <?php if (!empty($plans)): ?>
                <div class="nc-table-wrap">
                    <table class="nc-table nc-table-workout-look">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> Plan ID</th>
                                <th><i class="fas fa-tag"></i> Plan Name</th>
                                <th><i class="fas fa-users"></i> Assigned Players</th>
                                <th><i class="fas fa-capsules"></i> Supplements</th>
                                <th><i class="fas fa-circle"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $plan): ?>
                                <?php
                                    $planName = $resolvePlanName($plan);
                                    $assignedCount = (int)($plan->assigned_player_count ?? 0);
                                    $assignedNames = array_values(array_filter(array_map('trim', explode(',', (string)($plan->assigned_player_names ?? ''))), static fn($v) => $v !== ''));
                                    $assignedEmails = array_values(array_filter(array_map('trim', explode(',', (string)($plan->assigned_player_emails ?? ''))), static fn($v) => $v !== ''));

                                    // Fallback for older rows where only a single PlayerID/name exists.
                                    if ($assignedCount <= 0 && !empty($plan->player_name)) {
                                        $assignedCount = 1;
                                        $assignedNames = [trim((string)$plan->player_name)];
                                        $fallbackEmail = trim((string)($plan->player_email ?? ''));
                                        if ($fallbackEmail !== '') {
                                            $assignedEmails = [$fallbackEmail];
                                        }
                                    }

                                    $assignedPlayers = [];
                                    foreach ($assignedNames as $idx => $name) {
                                        $assignedPlayers[] = [
                                            'name' => $name,
                                            'email' => $assignedEmails[$idx] ?? '',
                                        ];
                                    }

                                    if ($assignedCount <= 0) {
                                        $assignedCount = count($assignedPlayers);
                                    }

                                    $supplements = trim((string)($plan->Supplements ?? $plan->supplements ?? ''));
                                    $durationText = (int)($plan->Duration ?? 0) . ' day' . (((int)($plan->Duration ?? 0) !== 1) ? 's' : '');
                                    $createdText = !empty($plan->CreatedDate) ? date('M j, Y', strtotime($plan->CreatedDate)) : '—';
                                    $statusText = '';
                                    $statusRaw = strtolower(trim((string)($plan->Status ?? 'inactive')));
                                    if ($statusRaw === 'active' || $statusRaw === 'inactive') {
                                        $statusText = ucfirst($statusRaw);
                                    } else {
                                        $statusText = 'Inactive';
                                    }

                                    $dietDetailsRaw = trim((string)($plan->DietDetails ?? ''));
                                    $descriptionRaw = trim((string)($plan->Description ?? ''));
                                    $notesRaw = trim((string)($plan->Notes ?? ''));
                                    $fullDetails = $descriptionRaw !== '' ? $descriptionRaw : ($dietDetailsRaw !== '' ? $dietDetailsRaw : ($notesRaw !== '' ? $notesRaw : 'No additional details available.'));
                                ?>
                                <tr>
                                    <td><span class="nc-plan-id-strong">#<?php echo (int)($plan->PlanID ?? 0); ?></span></td>
                                    <td>
                                        <span class="nc-plan-name"><?php echo htmlspecialchars($planName); ?></span>
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="nc-assigned-btn"
                                            onclick="openNutritionAssignedPlayersModal(this)"
                                            data-plan-name="<?php echo htmlspecialchars($planName, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-assigned-players="<?php echo htmlspecialchars(json_encode($assignedPlayers), ENT_QUOTES, 'UTF-8'); ?>"
                                        >
                                            <i class="fas fa-users"></i>
                                            <?php echo (int)$assignedCount; ?> player<?php echo ((int)$assignedCount === 1) ? '' : 's'; ?>
                                        </button>
                                    </td>
                                    <td>
                                        <?php if ($supplements !== ''): ?>
                                            <span class="nc-diet-preview" title="<?php echo htmlspecialchars($supplements); ?>">
                                                <?php echo htmlspecialchars($supplements); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color:#9ca3af;">Not required</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $s = strtolower(trim((string)($plan->Status ?? 'inactive'))); ?>
                                        <?php if ($s !== 'active' && $s !== 'inactive') { $s = 'inactive'; } ?>
                                        <span class="nc-badge nc-badge-<?php echo $s; ?>">
                                            <i class="fas fa-<?php echo $s === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                            <?php echo ucfirst($s); ?>
                                        </span>
                                    </td>
                                    <td class="actions-col">
                                        <div class="nc-actions">
                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                title="View full plan details"
                                                onclick="openNutritionPlanViewModal(this)"
                                                data-plan-id="<?php echo (int)($plan->PlanID ?? 0); ?>"
                                                data-plan-name="<?php echo htmlspecialchars($planName, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-supplements="<?php echo htmlspecialchars($supplements !== '' ? $supplements : 'Not required', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-duration="<?php echo htmlspecialchars($durationText, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-status="<?php echo htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-created="<?php echo htmlspecialchars($createdText, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-details="<?php echo htmlspecialchars($fullDetails, ENT_QUOTES, 'UTF-8'); ?>"
                                                data-assigned-count="<?php echo (int)$assignedCount; ?>"
                                            >
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <a href="<?php echo URLROOT; ?>/nutrition/edit/<?php echo (int)($plan->PlanID ?? 0); ?>"
                                               class="btn btn-primary btn-sm" title="Edit plan">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                title="Delete plan"
                                                onclick="openNutritionDeleteModal(<?php echo htmlspecialchars(json_encode(URLROOT . '/nutrition/delete/' . (int)($plan->PlanID ?? 0)), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($planName), ENT_QUOTES, 'UTF-8'); ?>)">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="nc-empty" style="padding: 1.5rem 1.5rem 1.75rem;">
                    <h5 style="margin: 0 0 .35rem; color: #2c3e50;">No Nutrition Plans Yet</h5>
                    <p style="margin: 0 0 1rem; color: #6b7280;">Start by creating a personalised plan for one of your players.</p>
                    <a href="<?php echo URLROOT; ?>/nutrition/create" class="btn btn-success nc-empty-btn">
                        <i class="fas fa-plus"></i> New Plan
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Template plans reference cards (under the table) -->
        <div class="nc-card nc-templates-card">
            <div class="nc-card-header">
                <h2>
                    <i class="fas fa-layer-group"></i>
                    Template Plans (Quick Reference)
                    <span style="font-weight:400;color:#adb5bd;font-size:.85rem;margin-left:.5rem;">
                        (<?php echo count($templates); ?>)
                    </span>
                </h2>
            </div>

            <?php if (!empty($templates)): ?>
                <div class="nc-template-grid">
                    <?php foreach ($templates as $tpl): ?>
                        <div class="nc-template-card">
                            <div class="nc-template-top">
                                <div class="nc-template-title"><?php echo htmlspecialchars($tpl->PlanName ?? 'Template'); ?></div>
                                <div class="nc-template-calories">
                                    <i class="fas fa-fire"></i>
                                    <?php echo htmlspecialchars((string)($tpl->RecommendedCalories ?? '-')); ?> kcal
                                </div>
                            </div>

                            <?php if (!empty($tpl->Description)): ?>
                                <div class="nc-template-desc"><?php echo htmlspecialchars($tpl->Description); ?></div>
                            <?php else: ?>
                                <div class="nc-template-desc" style="color:#9ca3af;">No description available.</div>
                            <?php endif; ?>

                            <div class="nc-template-macros">
                                <span class="nc-macro-pill nc-macro-protein">Protein: <?php echo htmlspecialchars((string)($tpl->ProteinPercentage ?? '-')); ?>%</span>
                                <span class="nc-macro-pill nc-macro-carbs">Carbs: <?php echo htmlspecialchars((string)($tpl->CarbohydratePercentage ?? '-')); ?>%</span>
                                <span class="nc-macro-pill nc-macro-fat">Fat: <?php echo htmlspecialchars((string)($tpl->FatPercentage ?? '-')); ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="padding: 1.25rem 1.4rem; color: #6b7280;">No template plans found.</div>
            <?php endif; ?>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.trainer-layout -->

<div id="nutritionPlanViewModal" class="nc-modal" style="display:none;">
    <div class="nc-modal-panel">
        <div class="nc-modal-header">
            <h3 id="nutritionPlanViewTitle">Nutrition Plan Details</h3>
            <button type="button" class="nc-modal-close" onclick="closeNutritionPlanViewModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="nc-modal-body">
            <div class="nc-plan-view-grid">
                <div><strong>Plan ID:</strong> <span id="nutritionViewPlanId">-</span></div>
                <div><strong>Plan Name:</strong> <span id="nutritionViewPlanName">-</span></div>
                <div><strong>Supplements:</strong> <span id="nutritionViewSupplements">-</span></div>
                <div><strong>Duration:</strong> <span id="nutritionViewDuration">-</span></div>
                <div><strong>Status:</strong> <span id="nutritionViewStatus">-</span></div>
                <div><strong>Created:</strong> <span id="nutritionViewCreated">-</span></div>
                <div><strong>Assigned Players:</strong> <span id="nutritionViewAssigned">-</span></div>
            </div>
            <div class="nc-plan-view-details">
                <strong>Full Plan Details</strong>
                <pre id="nutritionViewDetails">-</pre>
            </div>
        </div>
    </div>
</div>

<div id="nutritionAssignedPlayersModal" class="nc-modal" style="display:none;">
    <div class="nc-modal-panel">
        <div class="nc-modal-header">
            <h3 id="nutritionAssignedPlayersTitle">Assigned Players</h3>
            <button type="button" class="nc-modal-close" onclick="closeNutritionAssignedPlayersModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="nc-modal-body">
            <div class="nc-assigned-table-wrap">
                <table class="nc-assigned-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="nutritionAssignedPlayersBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="nutritionDeleteModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b, #ff8e8e);">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <span class="close" onclick="closeNutritionDeleteModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div style="text-align: center; padding: 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff6b6b; margin-bottom: 15px;"></i>
                <p style="margin-bottom: 10px; color: #374151;">Are you sure you want to delete the nutrition plan "<span id="nutritionDeletePlanName" style="font-weight: 600; color: #4A90E2;"></span>"?</p>
                <p style="color: #ff6b6b; font-weight: 600; font-size: 14px;">This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer" style="background: #f8fafc; padding: 20px 25px; display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="closeNutritionDeleteModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                Cancel
            </button>
            <form id="nutritionDeleteForm" method="POST" style="display: inline;">
                <button type="submit" style="background: #ff6b6b; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-trash"></i>Delete Plan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const main    = document.getElementById('mainContent');

    if (toggle && sidebar && main) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }

    const assignedPlayersModal = document.getElementById('nutritionAssignedPlayersModal');
    const planViewModal = document.getElementById('nutritionPlanViewModal');
    if (assignedPlayersModal) {
        assignedPlayersModal.addEventListener('click', function (event) {
            if (event.target === assignedPlayersModal) {
                closeNutritionAssignedPlayersModal();
            }
        });
    }

    if (planViewModal) {
        planViewModal.addEventListener('click', function (event) {
            if (event.target === planViewModal) {
                closeNutritionPlanViewModal();
            }
        });
    }

    const deleteModal = document.getElementById('nutritionDeleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function (event) {
            if (event.target === deleteModal) {
                closeNutritionDeleteModal();
            }
        });
    }
});

function openNutritionDeleteModal(actionUrl, planName) {
    const modal = document.getElementById('nutritionDeleteModal');
    const form = document.getElementById('nutritionDeleteForm');
    const nameNode = document.getElementById('nutritionDeletePlanName');

    if (!modal || !form || !nameNode) {
        return;
    }

    form.action = actionUrl;
    nameNode.textContent = (planName && String(planName).trim() !== '') ? String(planName) : 'this plan';

    modal.style.display = 'block';
    setTimeout(function () {
        modal.classList.add('show');
    }, 10);
}

function closeNutritionDeleteModal() {
    const modal = document.getElementById('nutritionDeleteModal');
    if (!modal) {
        return;
    }

    modal.classList.remove('show');
    setTimeout(function () {
        modal.style.display = 'none';
    }, 250);
}

function openNutritionAssignedPlayersModal(button) {
    const modal = document.getElementById('nutritionAssignedPlayersModal');
    const title = document.getElementById('nutritionAssignedPlayersTitle');
    const body = document.getElementById('nutritionAssignedPlayersBody');

    if (!modal || !title || !body || !button) {
        return;
    }

    const planName = button.getAttribute('data-plan-name') || 'Nutrition Plan';
    const raw = button.getAttribute('data-assigned-players') || '[]';

    let players = [];
    try {
        players = JSON.parse(raw);
    } catch (e) {
        players = [];
    }

    title.textContent = 'Assigned Players - ' + planName;

    if (!Array.isArray(players) || players.length === 0) {
        body.innerHTML = '<tr><td colspan="2" class="nc-assigned-empty">No players assigned.</td></tr>';
    } else {
        body.innerHTML = players.map((player) => {
            const name = escapeHtml((player && player.name) ? String(player.name) : '-');
            const email = escapeHtml((player && player.email) ? String(player.email) : '-');
            return '<tr><td>' + name + '</td><td>' + email + '</td></tr>';
        }).join('');
    }

    modal.style.display = 'flex';
}

function closeNutritionAssignedPlayersModal() {
    const modal = document.getElementById('nutritionAssignedPlayersModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function openNutritionPlanViewModal(button) {
    const modal = document.getElementById('nutritionPlanViewModal');
    if (!modal || !button) {
        return;
    }

    const setText = function (id, value) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = (value && String(value).trim() !== '') ? String(value) : '-';
        }
    };

    const planName = button.getAttribute('data-plan-name') || 'Nutrition Plan';
    const title = document.getElementById('nutritionPlanViewTitle');
    if (title) {
        title.textContent = 'Nutrition Plan Details - ' + planName;
    }

    setText('nutritionViewPlanId', '#' + (button.getAttribute('data-plan-id') || '-'));
    setText('nutritionViewPlanName', planName);
    setText('nutritionViewSupplements', button.getAttribute('data-supplements') || '-');
    setText('nutritionViewDuration', button.getAttribute('data-duration') || '-');
    setText('nutritionViewStatus', button.getAttribute('data-status') || '-');
    setText('nutritionViewCreated', button.getAttribute('data-created') || '-');

    const assignedCount = parseInt(button.getAttribute('data-assigned-count') || '0', 10);
    setText('nutritionViewAssigned', assignedCount + ' player' + (assignedCount === 1 ? '' : 's'));

    const detailsNode = document.getElementById('nutritionViewDetails');
    if (detailsNode) {
        detailsNode.textContent = button.getAttribute('data-details') || '-';
    }

    modal.style.display = 'flex';
}

function closeNutritionPlanViewModal() {
    const modal = document.getElementById('nutritionPlanViewModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function escapeHtml(value) {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
</script>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<!-- NUTRITION_VIEW_MARKER: trainer/nutrition_index_v2.php -->
</body>
</html>
