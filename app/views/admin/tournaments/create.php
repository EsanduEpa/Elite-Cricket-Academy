<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">

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
                    <h1><i class="fas fa-plus"></i> Create Tournament</h1>
                    <p>Fill in the details to set up a new tournament</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/tournaments" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:15px 20px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="padding:20px;max-width:760px;">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/create_tournament" style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 1px 4px rgba(0,0,0,.1);">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Tournament Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box;" placeholder="e.g. U16 District T20 Cup 2026" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Age Group</label>
                        <select name="age_group" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                            <option value="">— Select —</option>
                            <?php foreach (['Under 13','Under 15','Under 16','Under 19','Under 21','Open'] as $ag): ?>
                                <option value="<?php echo $ag; ?>" <?php echo (($_POST['age_group'] ?? '') === $ag) ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Format</label>
                        <select name="format" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                            <?php foreach (['T20','ODI','Test','Other'] as $fmt): ?>
                                <option value="<?php echo $fmt; ?>" <?php echo (($_POST['format'] ?? 'T20') === $fmt) ? 'selected' : ''; ?>><?php echo $fmt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Tournament Date <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="tdate" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($_POST['tdate'] ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Registration Deadline</label>
                        <input type="date" name="registration_deadline" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($_POST['registration_deadline'] ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Max Players in Squad</label>
                        <input type="number" name="max_players" min="5" max="50" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" placeholder="e.g. 18" value="<?php echo htmlspecialchars($_POST['max_players'] ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Location</label>
                        <input type="text" name="location" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" placeholder="e.g. Colombo Cricket Ground" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Prize Pool (Rs.)</label>
                        <input type="number" name="prize_pool" min="0" step="0.01" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" placeholder="0" value="<?php echo htmlspecialchars($_POST['prize_pool'] ?? ''); ?>">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Description</label>
                        <textarea name="description" rows="4" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical;box-sizing:border-box;" placeholder="Tournament details visible to players..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Initial Status</label>
                        <select name="status" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                            <option value="created" <?php echo (($_POST['status'] ?? 'created') === 'created') ? 'selected' : ''; ?>>Created (not visible to players)</option>
                            <option value="registration_open" <?php echo (($_POST['status'] ?? '') === 'registration_open') ? 'selected' : ''; ?>>Registration Open (players can apply)</option>
                        </select>
                    </div>

                </div>

                <div style="margin-top:24px;display:flex;gap:12px;">
                    <button type="submit" style="padding:10px 24px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:14px;">
                        <i class="fas fa-save"></i> Create Tournament
                    </button>
                    <a href="<?php echo URLROOT; ?>/admin/tournaments" style="padding:10px 24px;background:#f1f5f9;color:#475569;border-radius:8px;font-weight:600;text-decoration:none;font-size:14px;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
