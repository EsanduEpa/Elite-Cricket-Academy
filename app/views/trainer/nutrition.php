<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<!-- Mobile-specific meta tags -->
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

<?php
$nutritionPlans = $data['nutrition_plans'] ?? ($data['plans'] ?? []);
$totalPlans = count($nutritionPlans);
$activePlans = 0;
$inactivePlans = 0;
$groupPlans = 0;
foreach ($nutritionPlans as $plan) {
    if (strtolower($plan->Status ?? 'active') === 'active') {
        $activePlans++;
    } else {
        $inactivePlans++;
    }

    if ((int)($plan->assigned_player_count ?? 0) > 1) {
        $groupPlans++;
    }
}
?>

    <!-- Trainer Layout -->
    <div class="player-layout nutrition-page nc-page">
        <!-- Left Sidebar Panel -->
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
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedule & Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Workout Plans</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                            <i class="fas fa-apple-alt"></i>
                            <span>Nutrition Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Profile Section -->
            <div class="trainer-profile">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-name"><?php echo $_SESSION['username'] ?? 'John Trainer'; ?></div>
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

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header nc-hero">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-apple-alt"></i> Nutrition Plans Management</h1>
                        <p>Create and manage customized nutrition plans for your trainees</p>
                    </div>
                    <div class="header-actions">
                        <a href="<?php echo URLROOT; ?>/nutrition/create" class="btn btn-training nc-btn nc-btn-primary">
                            <i class="fas fa-plus"></i>Add New Plan
                        </a>
                        <button class="btn btn-refresh nc-btn nc-btn-refresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            <div class="current-time"><?php echo date('H:i'); ?></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('nutrition_message'); ?>

            <!-- Nutrition Plans Table Card -->
            <div class="schedule-card nc-table-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-apple-alt"></i> Your Nutrition Plans</h2>
                        <div class="table-controls nc-table-controls">
                            <div class="nc-search-wrap">
                                <i class="fas fa-search"></i>
                                <input type="text" id="nutritionSearch" placeholder="Search plans...">
                            </div>
                            <select id="statusFilter" class="nc-status-filter">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="card-content table-container nc-table-shell">
                    <table class="dashboard-table nc-nutrition-table" id="nutritionTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-apple-alt"></i> Plan</th>
                                <th><i class="fas fa-users"></i> Assigned To</th>
                                <th><i class="fas fa-calendar"></i> Duration</th>
                                <th><i class="fas fa-sticky-note"></i> Notes</th>
                                <th><i class="fas fa-chart-line"></i> Status</th>
                                <th><i class="fas fa-cogs"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($nutritionPlans)): ?>
                                <?php foreach ($nutritionPlans as $plan): ?>
                                    <?php $rowStatus = strtolower($plan->Status ?? 'inactive'); ?>
                                    <tr class="nutrition-row" data-status="<?php echo htmlspecialchars($rowStatus); ?>">
                                        <td class="plan-details nc-plan-cell" data-label="Plan">
                                            <div class="plan-info">
                                                <div class="plan-icon">
                                                    <i class="fas fa-apple-alt"></i>
                                                </div>
                                                <div class="plan-text">
                                                    <strong class="nc-plan-name">
                                                        <?php echo htmlspecialchars($plan->PlanName ?? $plan->nutritionPlanName ?? ('Plan #' . (int)$plan->PlanID)); ?>
                                                    </strong>
                                                    <span class="plan-description">#<?php echo (int)$plan->PlanID; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="player-info nc-player-cell" data-label="Assigned To">
                                            <div class="player-avatar">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <?php $assignmentCount = (int)($plan->assigned_player_count ?? 0); ?>
                                            <div class="player-details">
                                                <span class="player-name">
                                                    <?php if ($assignmentCount > 1): ?>
                                                        <?php echo $assignmentCount; ?> players
                                                    <?php elseif ($assignmentCount === 1 || !empty($plan->player_name)) : ?>
                                                        <?php echo htmlspecialchars($plan->player_name ?? '1 player'); ?>
                                                    <?php else: ?>
                                                        —
                                                    <?php endif; ?>
                                                </span>
                                                <span class="player-email" title="<?php echo htmlspecialchars($plan->assigned_player_names ?? ($plan->player_email ?? '')); ?>">
                                                    <?php if ($assignmentCount > 1): ?>
                                                        <?php echo htmlspecialchars($plan->assigned_player_names ?? ''); ?>
                                                    <?php elseif ($assignmentCount === 1): ?>
                                                        <?php echo htmlspecialchars($plan->player_email ?? ''); ?>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($plan->player_email ?? ''); ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="duration-info nc-duration-cell" data-label="Duration">
                                            <div class="duration-display nc-duration-badge">
                                                <i class="fas fa-hourglass-half"></i>
                                                <span>
                                                    <?php echo (int)($plan->Duration ?? 0); ?> day<?php echo ((int)($plan->Duration ?? 0) !== 1) ? 's' : ''; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="notes-info nc-notes-cell" data-label="Notes">
                                            <?php $notesText = (string)($plan->Notes ?? $plan->notes ?? ''); ?>
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
                                        <td data-label="Status">
                                            <span class="status-badge status-<?php echo htmlspecialchars($rowStatus); ?> nc-status-pill">
                                                <i class="fas fa-<?php echo $rowStatus === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                                <?php echo ucfirst($rowStatus); ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell nc-actions-cell" data-label="Actions">
                                            <div class="profile-actions nc-row-actions">
                                                <a class="profile-action edit nc-row-btn nc-row-btn-edit"
                                                   href="<?php echo URLROOT; ?>/nutrition/edit/<?php echo (int)$plan->PlanID; ?>"
                                                   title="Edit Plan">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                      action="<?php echo URLROOT; ?>/nutrition/delete/<?php echo (int)$plan->PlanID; ?>"
                                                      onsubmit="return confirm('Delete this nutrition plan? This action cannot be undone.');">
                                                    <button class="profile-action delete nc-row-btn nc-row-btn-delete" type="submit" title="Delete Plan">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="empty-state">
                                    <td colspan="6" class="empty-state">
                                        <div class="empty-content">
                                            <h3>No Nutrition Plans Found</h3>
                                            <p>Start by creating a nutrition plan and assign it to players or a group.</p>
                                            <a href="<?php echo URLROOT; ?>/nutrition/create" class="btn btn-primary nc-empty-btn nc-btn nc-btn-primary">
                                                <i class="fas fa-plus"></i> Create First Plan
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>
<script src="<?php echo URLROOT; ?>/js/trainer/nutrition.js"></script>
</body>
</html>
