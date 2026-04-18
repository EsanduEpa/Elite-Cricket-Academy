<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition_crud.css?v=<?php echo time(); ?>">
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
        <div class="nc-card">
            <div class="nc-card-header">
                <h2><i class="fas fa-list-ul"></i> All Nutrition Plans
                    <span style="font-weight:400;color:#adb5bd;font-size:.85rem;margin-left:.5rem;">
                        (<?php echo count($plans); ?>)
                    </span>
                </h2>
            </div>

            <?php if (!empty($plans)): ?>
                <div class="nc-table-wrap">
                    <table class="nc-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i> Plan Name</th>
                                <th><i class="fas fa-user"></i> Player</th>
                                <th><i class="fas fa-utensils"></i> Diet Details</th>
                                <th><i class="fas fa-capsules"></i> Supplements</th>
                                <th><i class="fas fa-hourglass-half"></i> Duration</th>
                                <th><i class="fas fa-circle"></i> Status</th>
                                <th><i class="fas fa-calendar-alt"></i> Created</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $plan): ?>
                                <tr>
                                    <td>
                                        <span class="nc-plan-name"><?php echo htmlspecialchars($plan->PlanName ?? 'Untitled Plan'); ?></span>
                                        <span class="nc-plan-id">#<?php echo (int)($plan->PlanID ?? 0); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($plan->player_name ?? '—'); ?></td>
                                    <td>
                                        <span class="nc-diet-preview" title="<?php echo htmlspecialchars((string)($plan->DietDetails ?? '')); ?>">
                                            <?php echo htmlspecialchars((string)($plan->DietDetails ?? '')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $supplements = trim((string)($plan->Supplements ?? $plan->supplements ?? '')); ?>
                                        <?php if ($supplements !== ''): ?>
                                            <span class="nc-diet-preview" title="<?php echo htmlspecialchars($supplements); ?>">
                                                <?php echo htmlspecialchars($supplements); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color:#9ca3af;">Not required</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo (int)($plan->Duration ?? 0); ?> day<?php echo ((int)($plan->Duration ?? 0) !== 1) ? 's' : ''; ?></td>
                                    <td>
                                        <?php $s = strtolower(trim((string)($plan->Status ?? 'inactive'))); ?>
                                        <?php if ($s !== 'active' && $s !== 'inactive') { $s = 'inactive'; } ?>
                                        <span class="nc-badge nc-badge-<?php echo $s; ?>">
                                            <i class="fas fa-<?php echo $s === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                            <?php echo ucfirst($s); ?>
                                        </span>
                                    </td>
                                    <td><?php echo !empty($plan->CreatedDate) ? date('M j, Y', strtotime($plan->CreatedDate)) : '—'; ?></td>
                                    <td class="actions-col">
                                        <div class="nc-actions">
                                            <a href="<?php echo URLROOT; ?>/nutrition/edit/<?php echo (int)($plan->PlanID ?? 0); ?>"
                                               class="btn btn-primary btn-sm" title="Edit plan">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST"
                                                  action="<?php echo URLROOT; ?>/nutrition/delete/<?php echo (int)($plan->PlanID ?? 0); ?>"
                                                  onsubmit="return confirmDelete(this)">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete plan">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
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
</div><!-- /.player-layout -->

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
});

function confirmDelete() {
    return confirm('Are you sure you want to permanently delete this nutrition plan?\nThis action cannot be undone.');
}
</script>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<!-- NUTRITION_VIEW_MARKER: trainer/nutrition_index_v2.php -->
</body>
</html>
