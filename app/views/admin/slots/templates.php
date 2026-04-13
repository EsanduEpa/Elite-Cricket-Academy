<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.badge-program        { background:#cce5ff;color:#004085;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-private        { background:#fff3cd;color:#856404;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-facility       { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-coach          { background:#e2d9f3;color:#4a1e8c;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-trainer        { background:#fde2b8;color:#7a3d00;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-none           { background:#e9ecef;color:#495057;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-active         { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-inactive       { background:#f8d7da;color:#721c24;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.tbl-action-btn       { padding:5px 12px;border:none;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-block; }
.day-map              { font-size:11px;color:#888; }
</style>

<?php
$dayNames = ['','Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
?>

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
                    <h1><i class="fas fa-layer-group"></i> Session Templates</h1>
                    <p>Define recurring programs, private sessions, and facility-only slots.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/newtemplate" style="padding:9px 20px;border-radius:8px;background:#27ae60;color:#fff;text-decoration:none;font-size:14px;font-weight:600;">
                        <i class="fas fa-plus"></i> New Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px;">
            <?php if (!empty($data['templateNotice'])): ?>
                <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:14px 16px;border-radius:10px;margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['templateNotice']) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($data['templates'])): ?>
                <div style="text-align:center;padding:60px;color:#888;">
                    <i class="fas fa-layer-group" style="font-size:48px;color:#dee2e6;display:block;margin-bottom:16px;"></i>
                    <h3>No templates yet</h3>
                    <p>Create your first session template to get started.</p>
                    <a href="<?php echo URLROOT; ?>/adminslots/newtemplate" style="display:inline-block;margin-top:16px;padding:10px 24px;background:#27ae60;color:#fff;border-radius:8px;text-decoration:none;">
                        <i class="fas fa-plus"></i> Create Template
                    </a>
                </div>
            <?php else: ?>
            <div class="content-card" style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Template</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Type</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Staff</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Time Band</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Day</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Facility</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Max</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Price</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Status</th>
                            <th style="padding:12px 14px;text-align:left;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['templates'] as $t): ?>
                        <tr style="border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                            <td style="padding:12px 14px;">
                                <div style="font-weight:600;color:#2c3e50;"><?= htmlspecialchars($t->TemplateName) ?></div>
                                <?php if ($t->AgeGroup): ?><div style="font-size:11px;color:#888;"><?= htmlspecialchars($t->AgeGroup) ?><?= $t->Category ? ' · ' . htmlspecialchars($t->Category) : '' ?></div><?php endif; ?>
                            </td>
                            <td style="padding:12px 14px;">
                                <?php
                                $tc = ['program'=>'badge-program','private'=>'badge-private','facility_only'=>'badge-facility'];
                                echo '<span class="' . ($tc[$t->SlotType] ?? 'badge-none') . '">' . ucfirst(str_replace('_',' ',$t->SlotType)) . '</span>';
                                ?>
                            </td>
                            <td style="padding:12px 14px;">
                                <?php
                                $sc = ['coach'=>'badge-coach','trainer'=>'badge-trainer','none'=>'badge-none'];
                                echo '<span class="' . ($sc[$t->StaffType] ?? 'badge-none') . '">' . ucfirst($t->StaffType) . '</span>';
                                ?>
                            </td>
                            <td style="padding:12px 14px;font-size:13px;"><?= htmlspecialchars($t->SlotLabel ?? '—') ?></td>
                            <td style="padding:12px 14px;font-size:13px;"><?= $t->DayOfWeek ? $dayNames[(int)$t->DayOfWeek] : '<span style="color:#aaa;">Any</span>' ?></td>
                            <td style="padding:12px 14px;font-size:13px;"><?= htmlspecialchars($t->FacilityName ?? '—') ?></td>
                            <td style="padding:12px 14px;font-size:13px;text-align:center;"><?= $t->MaxParticipants !== null ? (int) $t->MaxParticipants : '<span style="color:#999;">Age-group based</span>' ?></td>
                            <td style="padding:12px 14px;font-size:13px;">
                                <?php if ($t->SlotType === 'facility_only'): ?>
                                    <?= $t->PricePerSession > 0 ? 'Rs. ' . number_format($t->PricePerSession,2) : '<span style="color:#27ae60;">Free</span>' ?>
                                <?php else: ?>
                                    <span style="color:#2980b9;font-weight:600;">Covered</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 14px;">
                                <?php if ($t->IsActive): ?>
                                    <span class="badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 14px;">
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <a href="<?php echo URLROOT; ?>/adminslots/edittemplate/<?= $t->TemplateID ?>" class="tbl-action-btn" style="background:#3498db;color:#fff;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <?php if ($t->SlotType === 'facility_only'): ?>
                                        <a class="tbl-action-btn" style="background:#ccc;color:#fff;pointer-events:none;opacity:0.6;" tabindex="-1" title="Staff assignment not available for facility-only templates">
                                            <i class="fas fa-users"></i> Staff
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo URLROOT; ?>/adminslots/staff/<?= $t->TemplateID ?>" class="tbl-action-btn" style="background:#9b59b6;color:#fff;">
                                            <i class="fas fa-users"></i> Staff
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="toggle_template" value="<?= $t->TemplateID ?>">
                                        <?php if ($t->IsActive): ?>
                                            <button type="submit" class="tbl-action-btn" style="background:#e74c3c;color:#fff;">Deactivate</button>
                                        <?php else: ?>
                                            <button type="submit" class="tbl-action-btn" style="background:#27ae60;color:#fff;">Activate</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
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

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
