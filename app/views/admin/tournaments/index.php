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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Overview</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link"><i class="fas fa-users-cog"></i><span>Staff Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/players" class="nav-link"><i class="fas fa-user-graduate"></i><span>Player Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/events" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link"><i class="fas fa-comments"></i><span>Feedback Monitoring</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link"><i class="fas fa-file-alt"></i><span>Reports</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finance Management</span></a></li>
            </ul>
        </nav>
        <div class="admin-profile">
            <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
            <div class="profile-info"><span class="admin-name">Admin User</span><span class="admin-role">Super Administrator</span></div>
            <div class="logout-btn"><a href="<?php echo URLROOT; ?>/login/logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="events-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>Manage cricket tournaments — creation, squad selection, and results</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/create_tournament" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Tournament
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="margin:15px 20px;padding:12px 16px;border-radius:8px;background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="margin:15px 20px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="padding:20px;">
            <?php if (empty($data['tournaments'])): ?>
                <div style="text-align:center;padding:60px;color:#64748b;">
                    <i class="fas fa-trophy" style="font-size:48px;margin-bottom:16px;display:block;opacity:0.3;"></i>
                    <p>No tournaments yet. <a href="<?php echo URLROOT; ?>/admin/create_tournament">Create the first one.</a></p>
                </div>
            <?php else: ?>
                <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.1);">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Tournament</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Age Group</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Format</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Date</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Status</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Team</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#64748b;border-bottom:1px solid #e2e8f0;">Actions</th>
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
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
