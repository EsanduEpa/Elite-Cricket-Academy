<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition_crud.css?v=<?php echo time(); ?>">

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

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i><span>Schedule &amp; Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Slot Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i><span>Workout Plans</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/nutrition" class="nav-link">
                        <i class="fas fa-apple-alt"></i><span>Nutrition Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                        <i class="fas fa-user-injured"></i><span>Injury Reports</span>
                    </a>
                </li>
            </ul>
        </nav>

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
                <h1><i class="fas fa-apple-alt"></i> Nutrition Plans</h1>
                <p>Create and manage personalised nutrition plans for your players</p>
            </div>
            <div class="nc-header-actions">
                <a href="<?php echo URLROOT; ?>/nutrition/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> New Plan
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        <?php flash('nutrition_message'); ?>

        <!-- Plans table card -->
        <div class="nc-card">
            <div class="nc-card-header">
                <h2><i class="fas fa-list-ul"></i> All Nutrition Plans
                    <span style="font-weight:400;color:#adb5bd;font-size:.85rem;margin-left:.5rem;">
                        (<?php echo count($data['plans']); ?>)
                    </span>
                </h2>
            </div>

            <?php if (!empty($data['plans'])): ?>
                <div class="nc-table-wrap">
                    <table class="nc-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i> Plan</th>
                                <th><i class="fas fa-users"></i> Assigned To</th>
                                <th><i class="fas fa-hourglass-half"></i> Duration</th>
                                <th><i class="fas fa-sticky-note"></i> Notes</th>
                                <th><i class="fas fa-circle"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['plans'] as $plan): ?>
                                <tr>
                                    <!-- Plan name -->
                                    <td>
                                        <span class="nc-plan-name">
                                            <?php echo htmlspecialchars($plan->PlanName ?? $plan->nutritionPlanName ?? 'Untitled Plan'); ?>
                                        </span>
                                        <?php
                                            $macroSummary = [];
                                            if (isset($plan->ProteinPercentage)) $macroSummary[] = 'P ' . (float)$plan->ProteinPercentage . '%';
                                            if (isset($plan->CarbohydratePercentage)) $macroSummary[] = 'C ' . (float)$plan->CarbohydratePercentage . '%';
                                            if (isset($plan->FatPercentage)) $macroSummary[] = 'F ' . (float)$plan->FatPercentage . '%';
                                            if (isset($plan->RecommendedCalories)) $macroSummary[] = (int)$plan->RecommendedCalories . ' cal';
                                        ?>
                                        <?php if (!empty($macroSummary)): ?>
                                            <span class="plan-description"><?php echo htmlspecialchars(implode(' · ', $macroSummary)); ?></span>
                                        <?php endif; ?>
                                        <span class="nc-plan-id">#<?php echo (int)$plan->PlanID; ?></span>
                                    </td>

                                    <!-- Assigned To -->
                                    <?php $assignmentCount = (int)($plan->assigned_player_count ?? 0); ?>
                                    <td title="<?php echo htmlspecialchars($plan->assigned_player_names ?? ($plan->player_name ?? '')); ?>">
                                        <?php if ($assignmentCount > 1): ?>
                                            <?php echo $assignmentCount; ?> players
                                        <?php elseif ($assignmentCount === 1 || !empty($plan->player_name)) : ?>
                                            <?php echo htmlspecialchars($plan->player_name ?? '1 player'); ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>

                                    <!-- Duration -->
                                    <td><?php echo (int)$plan->Duration; ?> day<?php echo $plan->Duration != 1 ? 's' : ''; ?></td>

                                    <!-- Notes -->
                                    <?php $notesText = (string)($plan->Notes ?? $plan->notes ?? ''); ?>
                                    <td>
                                        <?php if (trim($notesText) === ''): ?>
                                            —
                                        <?php else: ?>
                                            <?php
                                                $notesPreview = mb_strlen($notesText) > 80 ? mb_substr($notesText, 0, 80) . '…' : $notesText;
                                            ?>
                                            <span class="nc-notes-preview" title="<?php echo htmlspecialchars($notesText, ENT_QUOTES); ?>">
                                                <?php echo htmlspecialchars($notesPreview, ENT_QUOTES); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status badge -->
                                    <td>
                                        <?php $s = strtolower($plan->Status ?? 'inactive'); ?>
                                        <span class="nc-badge nc-badge-<?php echo $s; ?>">
                                            <i class="fas fa-<?php echo $s === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                            <?php echo ucfirst($s); ?>
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="actions-col">
                                        <div class="nc-actions">
                                            <!-- Edit -->
                                            <a href="<?php echo URLROOT; ?>/nutrition/edit/<?php echo (int)$plan->PlanID; ?>"
                                               class="btn btn-primary btn-sm"
                                               title="Edit plan">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            <!-- Delete (POST form + JS confirm) -->
                                            <form method="POST"
                                                  action="<?php echo URLROOT; ?>/nutrition/delete/<?php echo (int)$plan->PlanID; ?>"
                                                  onsubmit="return confirmDelete(this)">
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Delete plan">
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
                <!-- Empty state -->
                <div class="nc-empty">
                    <h5>No Nutrition Plans Yet</h5>
                    <p>Start by creating a personalised plan for one of your players.</p>
                    <a href="<?php echo URLROOT; ?>/nutrition/create" class="btn btn-success nc-empty-btn">
                        <i class="fas fa-plus"></i> Create First Plan
                    </a>
                </div>
            <?php endif; ?>

        </div><!-- /.nc-card -->

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<script>
// Sidebar toggle
document.addEventListener('DOMContentLoaded', function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const main    = document.getElementById('mainContent');
    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }
});

// Delete confirmation — called by onsubmit on each delete form
function confirmDelete(form) {
    return confirm('Are you sure you want to permanently delete this nutrition plan?\nThis action cannot be undone.');
}
</script>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
</body>
</html>
