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
            <div style="margin:15px 20px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="padding:20px;max-width:760px;">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/edit_tournament/<?php echo $t->TournamentID; ?>" style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 1px 4px rgba(0,0,0,.1);">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Tournament Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;box-sizing:border-box;" value="<?php echo htmlspecialchars($t->Name); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Age Group</label>
                        <select name="age_group" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                            <option value="">— Select —</option>
                            <?php foreach (['Under 13','Under 15','Under 16','Under 19','Under 21','Open'] as $ag): ?>
                                <option value="<?php echo $ag; ?>" <?php echo ($t->AgeGroup === $ag) ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Format</label>
                        <select name="format" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                            <?php foreach (['T20','ODI','Test','Other'] as $fmt): ?>
                                <option value="<?php echo $fmt; ?>" <?php echo ($t->Format === $fmt) ? 'selected' : ''; ?>><?php echo $fmt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Tournament Date <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="tdate" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($t->tdate); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Registration Deadline</label>
                        <input type="date" name="registration_deadline" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($t->RegistrationDeadline ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Max Players in Squad</label>
                        <input type="number" name="max_players" min="5" max="50" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($t->MaxPlayers ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Location</label>
                        <input type="text" name="location" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($t->Location ?? ''); ?>">
                    </div>

                    <div>
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Prize Pool (Rs.)</label>
                        <input type="number" name="prize_pool" min="0" step="0.01" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" value="<?php echo htmlspecialchars($t->PrizePool ?? '0'); ?>">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-weight:600;margin-bottom:6px;color:#374151;">Description</label>
                        <textarea name="description" rows="4" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical;box-sizing:border-box;"><?php echo htmlspecialchars($t->Description ?? ''); ?></textarea>
                    </div>

                </div>

                <div style="margin-top:24px;display:flex;gap:12px;">
                    <button type="submit" style="padding:10px 24px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:14px;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?php echo $t->TournamentID; ?>" style="padding:10px 24px;background:#f1f5f9;color:#475569;border-radius:8px;font-weight:600;text-decoration:none;font-size:14px;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
