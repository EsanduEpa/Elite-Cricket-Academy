<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournament-pages.css">

<div class="coach-layout">
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo"><i class="fas fa-chalkboard-teacher"></i><h3>Coach Panel</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-angle-left"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players"><i class="fas fa-users"></i><span>Players</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health &amp; Injury"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications"><i class="fas fa-bell"></i><span>Notifications</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events"><i class="fas fa-calendar"></i><span>Events</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Coach'); ?></div>
            <div class="profile-role">Cricket Coach<?php echo $data['is_head_coach'] ? ' · Head Coach' : ''; ?></div>
            <a href="<?php echo URLROOT; ?>/coach/profile" class="action-btn" style="margin-top:10px;"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:8px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                <p>View tournaments and manage squad selection<?php echo $data['is_head_coach'] ? ' as Head Coach' : ''; ?></p>
            </div>
            <div style="display:flex;gap:8px;padding:0 20px;align-items:flex-start;">
                <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="page-action-btn">
                    <i class="fas fa-star"></i>
                    Recommendations
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#d1fae5;color:#065f46;"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div style="padding:20px;">
            <?php if (empty($data['tournaments'])): ?>
                <div style="text-align:center;padding:60px;color:#94a3b8;">
                    <i class="fas fa-trophy" style="font-size:48px;margin-bottom:16px;display:block;opacity:0.3;"></i>
                    <p>No tournaments available yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($data['tournaments'] as $t): ?>
                <div class="t-card <?php echo $t->Status; ?>">
                    <div>
                        <div style="font-size:17px;font-weight:700;color:#1e293b;margin-bottom:4px;"><?php echo htmlspecialchars($t->Name); ?></div>
                        <div style="font-size:13px;color:#64748b;margin-bottom:8px;">
                            <?php echo htmlspecialchars($t->Format ?? ''); ?> &nbsp;·&nbsp;
                            <?php echo htmlspecialchars($t->AgeGroup ?? ''); ?> &nbsp;·&nbsp;
                            <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : ''; ?>
                            <?php if ($t->Location): ?> &nbsp;·&nbsp; <?php echo htmlspecialchars($t->Location); ?><?php endif; ?>
                        </div>
                        <span class="tournament-status status-<?php echo $t->Status; ?>"><?php echo str_replace('_',' ',$t->Status); ?></span>
                        <?php if ($t->IsTeamAnnounced): ?>&nbsp;<span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;"><i class="fas fa-bullhorn"></i> Team Announced</span><?php endif; ?>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                        <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" style="background:#3b82f6;color:#fff;padding:7px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-eye"></i> View</a>
                        <?php if ($data['is_head_coach'] && in_array($t->Status, ['registration_open','registration_closed'])): ?>
                            <a href="<?php echo URLROOT; ?>/coach/finalize_team/<?php echo $t->TournamentID; ?>" style="background:#16a34a;color:#fff;padding:7px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-users"></i> Finalize Squad</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
