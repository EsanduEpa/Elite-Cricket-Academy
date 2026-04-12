<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">

<div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo"><i class="fas fa-user-shield"></i><h3>Admin Dashboard</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link"><i class="fas fa-users-cog"></i><span>Staff Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/players" class="nav-link"><i class="fas fa-user-graduate"></i><span>Player Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/events" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Events &amp; Tournaments</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link"><i class="fas fa-comments"></i><span>Feedback</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finance</span></a></li>
            </ul>
        </nav>
        <div class="admin-profile">
            <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
            <div class="profile-info"><span class="admin-name">Admin</span><span class="admin-role">Super Administrator</span></div>
            <div class="logout-btn"><a href="<?php echo URLROOT; ?>/login/logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-plus"></i> Generate Occurrences</h1>
                    <p>Select a template and date range to create calendar occurrences from the recurring schedule.</p>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Ad-hoc Session</a>
        </div>

        <div style="padding:0 25px 40px; max-width:680px;">

            <?php if (!empty($data['notice'])): ?>
                <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['notice']) ?>
                </div>
            <?php endif; ?>

            <?php if ($data['error']): ?>
                <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>

            <?php if ($data['result'] !== null): ?>
                <?php $r = $data['result']; ?>
                <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:16px 20px;border-radius:8px;margin-bottom:24px;">
                    <div style="font-weight:700;font-size:15px;margin-bottom:6px;"><i class="fas fa-check-circle"></i> Generation Complete</div>
                    <div>Inserted: <strong><?= $r['inserted'] ?></strong> &nbsp;|&nbsp; Skipped (already existed): <strong><?= $r['skipped'] ?></strong></div>
                    <?php if (!empty($r['skipped_dates'])): ?>
                        <div style="margin-top:10px;font-size:12px;color:#155724;">
                            <strong>Skipped dates (facility already booked that slot):</strong><br>
                            <?= implode(', ', array_map('htmlspecialchars', $r['skipped_dates'])) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($r['inserted'] > 0): ?>
                        <div style="margin-top:12px;">
                            <a href="<?php echo URLROOT; ?>/adminslots/calendar" style="color:#155724;text-decoration:underline;font-size:13px;">
                                <i class="fas fa-calendar-alt"></i> View Calendar →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <form method="POST">

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-weight:600;font-size:13px;color:#555;margin-bottom:6px;">Template <span style="color:#e74c3c;">*</span></label>
                        <?php if (empty($data['templates'])): ?>
                            <p style="color:#888;font-size:13px;">No active templates found. <a href="<?php echo URLROOT; ?>/adminslots/newtemplate">Create one first.</a></p>
                        <?php else: ?>
                            <select name="template_id" required style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;color:#333;">
                                <option value="">— Select a template —</option>
                                <?php foreach ($data['templates'] as $t): ?>
                                    <option value="<?= $t->TemplateID ?>"
                                        <?= ((int)($data['selectedTemplateId'] ?? 0) === (int)$t->TemplateID) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t->TemplateName) ?>
                                        <?php if ($t->DayOfWeek): ?>
                                            <?php $days = ['','Mon','Tue','Wed','Thu','Fri','Sat','Sun']; ?>
                                            — every <?= $days[(int)$t->DayOfWeek] ?>
                                        <?php else: ?>
                                            — ad-hoc (any day)
                                        <?php endif; ?>
                                        (<?= htmlspecialchars($t->SlotLabel ?? 'No band') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label style="display:block;font-weight:600;font-size:13px;color:#555;margin-bottom:6px;">From Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" name="from_date" required
                                   value="<?= htmlspecialchars($_POST['from_date'] ?? '') ?>"
                                   style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-weight:600;font-size:13px;color:#555;margin-bottom:6px;">To Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" name="to_date" required
                                   value="<?= htmlspecialchars($_POST['to_date'] ?? '') ?>"
                                   style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;box-sizing:border-box;">
                        </div>
                    </div>

                    <div style="background:#fff8e1;border-left:4px solid #f39c12;padding:12px 16px;border-radius:4px;font-size:13px;color:#7a5200;margin-bottom:24px;">
                        <strong>How it works:</strong> The system loops every date in the range and creates one occurrence per day that matches the template's day-of-week setting. If a facility conflict exists on a date, that date is skipped and reported — no error, no duplicates.
                    </div>

                    <?php if (!empty($data['templates'])): ?>
                        <button type="submit" style="padding:10px 28px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-cogs"></i> Generate
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
