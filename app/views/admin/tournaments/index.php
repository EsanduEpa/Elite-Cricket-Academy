<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.tournament-status { display:inline-block; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600; text-transform:uppercase; }
.status-created { background:#e2e8f0; color:#475569; }
.status-registration_open { background:#dcfce7; color:#166534; }
.status-registration_closed { background:#fef9c3; color:#854d0e; }
.status-team_announced { background:#dbeafe; color:#1e40af; }
.status-ongoing { background:#fde68a; color:#92400e; }
.status-completed { background:#d1fae5; color:#065f46; }
.status-cancelled { background:#fee2e2; color:#991b1b; }
</style>

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
                    <p>Manage cricket tournaments — creation, squad selection, and results</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/create_tournament" style="padding:9px 18px;border-radius:8px;background:#3498db;color:#fff;text-decoration:none;font-size:14px;">
                        <i class="fas fa-plus"></i> Create Tournament
                    </a>
                </div>
            </div>
        </div>

        <div style="padding:0 25px 40px;">
            <?php if (isset($_SESSION['success'])): ?>
                <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($data['tournaments'])): ?>
                <div style="background:#fff;border-radius:12px;padding:60px;text-align:center;color:#64748b;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                    <i class="fas fa-trophy" style="font-size:48px;margin-bottom:16px;display:block;opacity:0.3;"></i>
                    <p>No tournaments yet. <a href="<?php echo URLROOT; ?>/admin/create_tournament" style="color:#3498db;text-decoration:none;">Create the first one.</a></p>
                </div>
            <?php else: ?>
                <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Tournament</th>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Age Group</th>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Format</th>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Date</th>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Status</th>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Team</th>
                                <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;font-weight:600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['tournaments'] as $t): ?>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px 16px;">
                                    <strong><?php echo htmlspecialchars($t->Name); ?></strong><br>
                                    <small style="color:#94a3b8;"><?php echo htmlspecialchars($t->Location ?? '—'); ?></small>
                                </td>
                                <td style="padding:12px 16px;"><?php echo htmlspecialchars($t->AgeGroup ?? '—'); ?></td>
                                <td style="padding:12px 16px;"><?php echo htmlspecialchars($t->Format ?? '—'); ?></td>
                                <td style="padding:12px 16px;"><?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : '—'; ?></td>
                                <td style="padding:12px 16px;">
                                    <span class="tournament-status status-<?php echo $t->Status; ?>">
                                        <?php echo str_replace('_', ' ', $t->Status); ?>
                                    </span>
                                </td>
                                <td style="padding:12px 16px;">
                                    <?php if ($t->IsTeamAnnounced): ?>
                                        <span style="color:#16a34a;"><i class="fas fa-check-circle"></i> Announced</span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8;">Not announced</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?php echo $t->TournamentID; ?>" style="color:#3b82f6;margin-right:8px;" title="View"><i class="fas fa-eye"></i></a>
                                    <?php if (!in_array($t->Status, ['ongoing','completed','cancelled'])): ?>
                                        <a href="<?php echo URLROOT; ?>/admin/edit_tournament/<?php echo $t->TournamentID; ?>" style="color:#f59e0b;margin-right:8px;" title="Edit"><i class="fas fa-edit"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
