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
        <?php $t = $data['tournament']; ?>
        <div class="events-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-edit"></i> Edit Tournament</h1>
                    <p><?php echo htmlspecialchars($t->Name); ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Detail
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
            <form class="tourn-form" method="POST" action="<?php echo URLROOT; ?>/admin/edit_tournament/<?php echo $t->TournamentID; ?>" data-mode="edit" data-reg-gap="30" data-original-tdate="<?php echo htmlspecialchars($t->tdate); ?>">

                <div class="form-grid">

                    <div class="form-field full">
                        <label>Tournament Name <span class="required">*</span></label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($t->Name); ?>">
                    </div>

                    <div class="form-field">
                        <label>Age Group</label>
                        <input type="hidden" name="age_group" value="<?php echo htmlspecialchars($t->AgeGroup ?? ''); ?>">
                        <select disabled title="Age group cannot be changed after creation.">
                            <option value="">— Select —</option>
                            <?php foreach (['Under 11','Under 13','Under 15','Under 16','Under 19','Open'] as $ag): ?>
                                <option value="<?php echo $ag; ?>" <?php echo ($t->AgeGroup === $ag) ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color:#94a3b8;">Age group cannot be changed after creation.</small>
                    </div>

                    <div class="form-field">
                        <label>Format</label>
                        <select name="format">
                            <?php foreach (['T20','ODI','Test','Other'] as $fmt): ?>
                                <option value="<?php echo $fmt; ?>" <?php echo ($t->Format === $fmt) ? 'selected' : ''; ?>><?php echo $fmt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Tournament Date <span class="required">*</span></label>
                        <input type="date" id="tdate" name="tdate" required value="<?php echo htmlspecialchars($t->tdate); ?>">
                    </div>

                    <div class="form-field">
                        <label>Registration Deadline</label>
                        <input type="date" id="registration_deadline" name="registration_deadline" value="<?php echo htmlspecialchars($t->RegistrationDeadline ?? ''); ?>">
                        <small id="reg-deadline-hint" style="color:#64748b;"></small>
                    </div>

                    <div class="form-field">
                        <label>Max Players in Squad</label>
                        <input type="number" name="max_players" min="5" max="50" value="<?php echo htmlspecialchars($t->MaxPlayers ?? ''); ?>">
                    </div>

                    <div class="form-field">
                        <label>Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($t->Location ?? ''); ?>">
                    </div>

                    <div class="form-field">
                        <label>Prize Pool (Rs.)</label>
                        <input type="number" name="prize_pool" min="0" step="0.01" value="<?php echo htmlspecialchars($t->PrizePool ?? '0'); ?>">
                    </div>

                    <div class="form-field full">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo htmlspecialchars($t->Description ?? ''); ?></textarea>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                    <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-form-cancel">Cancel</a>
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
