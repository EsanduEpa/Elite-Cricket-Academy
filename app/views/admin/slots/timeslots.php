<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.slots-layout { display:flex; min-height:100vh; }
.slot-badge-active   { background:#d4edda; color:#155724; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600; }
.slot-badge-inactive { background:#f8d7da; color:#721c24; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600; }
.btn-toggle-on  { background:#dc3545; color:#fff; border:none; padding:6px 14px; border-radius:6px; cursor:pointer; font-size:13px; }
.btn-toggle-off { background:#28a745; color:#fff; border:none; padding:6px 14px; border-radius:6px; cursor:pointer; font-size:13px; }
</style>

<div class="admin-layout slots-layout">
    <!-- Sidebar -->
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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
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

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-clock"></i> Time Bands</h1>
                    <p>Master fixed time slots used across all session templates. Toggle active/inactive only — never deleted.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="btn btn-primary" style="text-decoration:none;padding:8px 18px;border-radius:8px;background:#3498db;color:#fff;font-size:14px;">
                        <i class="fas fa-layer-group"></i> Templates
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots" style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Private Requests</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px;">
            <div class="content-card" style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <p style="color:#666;font-size:13px;margin-bottom:20px;">
                    <i class="fas fa-info-circle" style="color:#3498db;"></i>
                    These bands are fixed by design. You can deactivate a band to hide it from future template creation. Existing templates using a deactivated band are unaffected.
                </p>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Band</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Time</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Duration</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Status</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['timeBands'] as $band): ?>
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:12px 16px;font-weight:600;color:#2c3e50;">#<?= $band->SlotID ?></td>
                            <td style="padding:12px 16px;"><?= htmlspecialchars($band->SlotLabel) ?></td>
                            <td style="padding:12px 16px;color:#666;"><?= $band->DurationMinutes ?> min</td>
                            <td style="padding:12px 16px;">
                                <?php if ($band->IsActive): ?>
                                    <span class="slot-badge-active">Active</span>
                                <?php else: ?>
                                    <span class="slot-badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 16px;">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="toggle_band" value="<?= $band->SlotID ?>">
                                    <?php if ($band->IsActive): ?>
                                        <button type="submit" class="btn-toggle-on"><i class="fas fa-toggle-on"></i> Deactivate</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn-toggle-off"><i class="fas fa-toggle-off"></i> Activate</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
