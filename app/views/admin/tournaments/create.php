<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
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
            <div class="tourn-alert error" style="margin:0 0 10px;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="tourn-form-wrap tourn-content">
            <form class="tourn-form" method="POST" action="<?php echo URLROOT; ?>/admin/create_tournament" data-mode="create" data-reg-gap="30">

                <div class="form-grid">

                    <div class="form-field full">
                        <label>Tournament Name <span class="required">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. U16 District T20 Cup 2026" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="form-field">
                        <label>Age Group</label>
                        <select name="age_group">
                            <option value="">— Select —</option>
                            <?php foreach (['Under 11' , 'Under 13','Under 15','Under 16','Under 19','Open'] as $ag): ?>
                                <option value="<?php echo $ag; ?>" <?php echo (($_POST['age_group'] ?? '') === $ag) ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Format</label>
                        <select name="format">b
                            <?php foreach (['T20','ODI','Test','Other'] as $fmt): ?>
                                <option value="<?php echo $fmt; ?>" <?php echo (($_POST['format'] ?? 'T20') === $fmt) ? 'selected' : ''; ?>><?php echo $fmt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Tournament Date <span class="required">*</span></label>
                        <input type="date" id="tdate" name="tdate" required value="<?php echo htmlspecialchars($_POST['tdate'] ?? ''); ?>">
                    </div>

                    <div class="form-field">
                        <label>Registration Deadline</label>
                        <input type="date" id="registration_deadline" name="registration_deadline" value="<?php echo htmlspecialchars($_POST['registration_deadline'] ?? ''); ?>">
                        <small id="reg-deadline-hint" style="color:#64748b;">Set a tournament date first.</small>
                    </div>

                    <div class="form-field">
                        <label>Max Players in Squad</label>
                        <input type="number" name="max_players" min="5" max="50" placeholder="e.g. 18" value="<?php echo htmlspecialchars($_POST['max_players'] ?? ''); ?>">
                    </div>

                    <div class="form-field">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="e.g. Colombo Cricket Ground" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                    </div>

                    <div class="form-field">
                        <label>Prize Pool (Rs.)</label>
                        <input type="number" name="prize_pool" min="0" step="0.01" placeholder="0" value="<?php echo htmlspecialchars($_POST['prize_pool'] ?? ''); ?>">
                    </div>

                    <div class="form-field full">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Tournament details visible to players..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-field">
                        <label>Initial Status</label>
                        <select name="status">
                            <option value="created" <?php echo (($_POST['status'] ?? 'created') === 'created') ? 'selected' : ''; ?>>Created (not visible to players)</option>
                            <option value="registration_open" <?php echo (($_POST['status'] ?? '') === 'registration_open') ? 'selected' : ''; ?>>Registration Open (players can apply)</option>
                        </select>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Create Tournament</button>
                    <a href="<?php echo URLROOT; ?>/admin/tournaments" class="btn-form-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/tournament-form.js"></script>
<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
