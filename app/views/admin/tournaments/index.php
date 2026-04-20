<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-list-tools.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/tournaments.css">

<div class="admin-layout">
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo"><i class="fas fa-user-shield"></i><h3>Admin Dashboard</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard </span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link"><i class="fas fa-users-cog"></i><span>Staff Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/players" class="nav-link"><i class="fas fa-user-graduate"></i><span>Player Management</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finances</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/admin/profile" class="profile-avatar" aria-label="Open admin profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>Manage cricket tournaments </p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/create_tournament" style="padding:9px 18px;border-radius:8px;background:#3498db;color:#fff;text-decoration:none;font-size:14px;">
                        <i class="fas fa-plus"></i> Create Tournament
                    </a>
                </div>
            </div>
        </div>

        <div class="tourn-index-content">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="tourn-alert success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="tourn-alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($data['tournaments'])): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <p>No tournaments yet. <a href="<?php echo URLROOT; ?>/admin/create_tournament" style="color:#3498db;text-decoration:none;">Create the first one.</a></p>
                </div>
            <?php else: ?>
                <div class="admin-list-shell" data-admin-list data-page-size="10">
                    <div class="admin-list-toolbar">
                        <div class="admin-list-search">
                            <i class="fas fa-search"></i>
                            <input type="text" data-list-filter="search" placeholder="Search tournament, location, format...">
                        </div>
                        <select data-list-filter="status">
                            <option value="all">All Status</option>
                            <option value="created">Created</option>
                            <option value="registration_open">Registration Open</option>
                            <option value="registration_closed">Registration Closed</option>
                            <option value="team_announced">Team Announced</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select data-list-filter="team">
                            <option value="all">All Team States</option>
                            <option value="announced">Team Announced</option>
                            <option value="not-announced">Not Announced</option>
                        </select>
                        <select data-list-filter="period">
                            <option value="all">All Dates</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="past">Past</option>
                        </select>
                        <select data-list-filter="format">
                            <option value="all">All Formats</option>
                            <?php
                                $formats = array_values(array_unique(array_filter(array_map(fn($t) => trim((string)($t->Format ?? '')), $data['tournaments']))));
                                sort($formats);
                                foreach ($formats as $format):
                            ?>
                                <option value="<?php echo htmlspecialchars(strtolower($format)); ?>"><?php echo htmlspecialchars($format); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-list-table-wrap">
                    <table class="admin-compact-table">
                        <thead>
                            <tr>
                                <th>Tournament</th>
                                <th>Age Group</th>
                                <th>Format</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Team</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody data-list-body>
                            <?php foreach ($data['tournaments'] as $t): ?>
                            <?php
                                $dateValue = !empty($t->tdate) ? date('Y-m-d', strtotime($t->tdate)) : '';
                                $period = ($dateValue && $dateValue < date('Y-m-d')) ? 'past' : 'upcoming';
                                $teamState = !empty($t->IsTeamAnnounced) ? 'announced' : 'not-announced';
                                $searchText = trim(($t->Name ?? '') . ' ' . ($t->Location ?? '') . ' ' . ($t->AgeGroup ?? '') . ' ' . ($t->Format ?? '') . ' ' . ($t->Status ?? ''));
                            ?>
                            <tr data-list-row
                                data-search="<?php echo htmlspecialchars(strtolower($searchText)); ?>"
                                data-filter-status="<?php echo htmlspecialchars(strtolower($t->Status ?? '')); ?>"
                                data-filter-team="<?php echo htmlspecialchars($teamState); ?>"
                                data-filter-period="<?php echo htmlspecialchars($period); ?>"
                                data-filter-format="<?php echo htmlspecialchars(strtolower((string)($t->Format ?? ''))); ?>">
                                <td>
                                    <span class="admin-list-primary"><?php echo htmlspecialchars($t->Name); ?></span>
                                    <span class="admin-list-muted"><?php echo htmlspecialchars($t->Location ?? '—'); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($t->AgeGroup ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($t->Format ?? '—'); ?></td>
                                <td><?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : '—'; ?></td>
                                <td>
                                    <span class="tournament-status status-<?php echo htmlspecialchars($t->Status); ?>">
                                        <?php echo str_replace('_', ' ', $t->Status); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($t->IsTeamAnnounced): ?>
                                        <span style="color:#16a34a;"><i class="fas fa-check-circle"></i> Announced</span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8;">Not announced</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="admin-list-actions">
                                    <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?php echo $t->TournamentID; ?>" class="admin-list-action view" title="View"><i class="fas fa-eye"></i></a>
                                    <?php if (!in_array($t->Status, ['ongoing','completed','cancelled'])): ?>
                                        <a href="<?php echo URLROOT; ?>/admin/edit_tournament/<?php echo $t->TournamentID; ?>" class="admin-list-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                                    <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="admin-list-empty-row" data-list-empty-row style="display:none;">
                                <td colspan="7"><i class="fas fa-filter"></i> No tournaments match the selected filters.</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="admin-list-footer">
                        <div class="admin-list-count" data-list-count></div>
                        <div class="admin-list-pagination" data-list-pagination></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/admin-list-tools.js"></script>
<?php require APPROOT . '/views/inc/components/footer.php'; ?>
