<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.badge-lead       { background:#cce5ff;color:#004085;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.badge-assistant  { background:#d4edda;color:#155724;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.badge-coach-t    { background:#e2d9f3;color:#4a1e8c;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.badge-trainer-t  { background:#fde2b8;color:#7a3d00;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.alert-error      { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:16px; }
.alert-success    { background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:16px; }
</style>

<div class="admin-layout">
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
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <a href="<?php echo URLROOT; ?>/adminslots/templates" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;font-weight:600;">
                            <i class="fas fa-arrow-left"></i> Back to Templates
                        </a>
                        <div style="font-size:12px;color:#aaa;">
                            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="color:#3498db;text-decoration:none;">Templates</a>
                            <i class="fas fa-chevron-right" style="font-size:10px;margin:0 6px;"></i>
                            <?php if (!empty($data['template']->temp_code)): ?>
                                #<?= htmlspecialchars($data['template']->temp_code) ?>
                                <i class="fas fa-chevron-right" style="font-size:10px;margin:0 6px;"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($data['template']->TemplateName) ?>
                            <i class="fas fa-chevron-right" style="font-size:10px;margin:0 6px;"></i>
                            Staff
                        </div>
                    </div>
                    <h1><i class="fas fa-users"></i> Staff Assignment</h1>
                    <p>
                       <?php if (!empty($data['template']->temp_code)): ?>
                           <strong>#<?= htmlspecialchars($data['template']->temp_code) ?></strong>
                           <span style="color:#adb5bd;">·</span>
                       <?php endif; ?>
                       <?= htmlspecialchars($data['template']->TemplateName) ?> — <?= htmlspecialchars($data['template']->SlotLabel ?? '') ?>
                       <?php if (!empty($data['template']->AgeGroup)): ?>
                           <span style="background:#e8f4fd;color:#0c5460;padding:2px 10px;border-radius:10px;font-size:11px;margin-left:8px;"><?= htmlspecialchars($data['template']->AgeGroup) ?></span>
                       <?php endif; ?>
                    </p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/edittemplate/<?= $data['template']->TemplateID ?>"
                       style="padding:8px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                        <i class="fas fa-edit"></i> Edit Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Private Requests</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px;display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

            <?php if (!empty($data['fromCreate'])): ?>
                <div style="grid-column:1/-1;background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;">
                    <i class="fas fa-check-circle"></i> Template created successfully! Now assign staff members, then proceed to generate occurrences.
                </div>
            <?php endif; ?>

            <!-- Currently Assigned Staff -->
            

            <!-- Add Staff Form -->
            <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:16px;color:#2c3e50;"><i class="fas fa-user-plus"></i> Add Staff Member</h3>

                <?php if (!empty($data['error'])): ?>
                    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
                <?php endif; ?>
                <?php if (!empty($data['success'])): ?>
                    <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['success']) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Staff Type</label>
                        <?php $tst = $data['template']->StaffType; ?>
                        <?php if ($tst === 'none'): ?>
                            <select name="staff_type" id="staffTypeSelect" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;" onchange="updateStaffList()">
                                <option value="coach">Coach</option>
                                <option value="trainer">Trainer</option>
                            </select>
                        <?php else: ?>
                            <input type="hidden" name="staff_type" value="<?= htmlspecialchars($tst) ?>">
                            <input type="text" value="<?= ucfirst($tst) ?>" readonly
                                   style="width:100%;padding:9px 12px;border:1px solid #e0e0e0;border-radius:8px;font-size:14px;background:#f8f9fa;color:#555;cursor:not-allowed;">
                        <?php endif; ?>
                        <p style="font-size:11px;color:#888;margin-top:4px;">
                            Locked to the template's staff type (<?= ucfirst($tst) ?>). Only matching staff can be assigned.
                        </p>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Select Person</label>
                        <?php if (!empty($data['coachFilter']) && (!empty($data['coachFilter']['ageGroup']) || !empty($data['coachFilter']['category']))): ?>
                            <p style="font-size:11px;color:#888;margin:-2px 0 8px;">
                                Coach list filtered by template:
                                <?php if (!empty($data['coachFilter']['ageGroup'])): ?>
                                    Age Group = <strong><?= htmlspecialchars($data['coachFilter']['ageGroup']) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($data['coachFilter']['category'])): ?>
                                    <?php if (!empty($data['coachFilter']['ageGroup'])): ?>·<?php endif; ?>
                                    Category = <strong><?= htmlspecialchars(ucfirst($data['coachFilter']['category'])) ?></strong>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <select name="user_id" id="coachList" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                            <?php if (empty($data['coaches'])): ?>
                                <option value="">No coaches available</option>
                            <?php else: ?>
                                <?php foreach ($data['coaches'] as $c): ?>
                                    <option value="<?= (int)$c->coach_id ?>"><?= htmlspecialchars($c->name ?? $c->Name ?? 'Coach') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <select name="user_id" id="trainerList" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;display:none;">
                            <?php if (empty($data['trainers'])): ?>
                                <option value="">No trainers available</option>
                            <?php else: ?>
                                <?php foreach ($data['trainers'] as $tr): ?>
                                    <option value="<?= $tr->UserID ?>"><?= htmlspecialchars($tr->Name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" style="width:100%;padding:10px;background:#9b59b6;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">
                        <i class="fas fa-user-plus"></i> Assign Staff
                    </button>
                </form>
            </div>

            <!-- Proceed to Generate - Shows on right side after success -->
            <div style="display:flex;flex-direction:column;justify-content:center;gap:12px;<?= !empty($data['success']) ? '' : 'visibility:hidden;' ?>">
                <form method="POST" style="display:block;width:100%;">
                    <input type="hidden" name="proceed_generate" value="1">
                    <button type="submit" style="width:100%;padding:16px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;min-height:56px;">
                        <i class="fas fa-arrow-right"></i> Proceed to Generate Occurrences
                    </button>
                </form>
                <a href="<?php echo URLROOT; ?>/adminslots/templates" style="padding:12px 20px;background:#ecf0f1;color:#333;border-radius:8px;text-decoration:none;font-size:14px;text-align:center;">
                    <i class="fas fa-list"></i> Back to Templates
                </a>
            </div>
        </div>

        <div style="padding:0 25px 30px;">
            <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <div style="margin-bottom:16px;">
                    <h3 style="margin:0 0 8px;font-size:16px;color:#2c3e50;"><i class="fas fa-list"></i> Current Coach Assignment Matrix</h3>
                    <p style="margin:0;color:#64748b;font-size:13px;">Live coach age-group assignments are shown here.</p>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:760px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Coach</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Head Coach</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Skill</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Age Groups</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['coachAssignments'])): ?>
                                <?php foreach ($data['coachAssignments'] as $assignment): ?>
                                    <tr style="border-bottom:1px solid #f0f0f0;">
                                        <td style="padding:10px 14px;font-size:13px;color:#2c3e50;"><?= htmlspecialchars($assignment->CoachName) ?></td>
                                        <td style="padding:10px 14px;font-size:13px;">
                                            <?php if (!empty($assignment->IsHeadCoach)): ?>
                                                <span style="background:#d4edda;color:#155724;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600;">Head Coach</span>
                                            <?php else: ?>
                                                <span style="background:#eef2f7;color:#475467;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600;">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:10px 14px;font-size:13px;">
                                            <span style="background:#e2d9f3;color:#4a1e8c;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600;">
                                                <?= htmlspecialchars(ucfirst($assignment->CoachingType)) ?>
                                            </span>
                                        </td>
                                        <td style="padding:10px 14px;font-size:13px;color:#2c3e50;"><?= htmlspecialchars($assignment->AgeGroups) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:24px;color:#64748b;">No coach age-group assignments have been configured yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const TEMPLATE_STAFF_TYPE = '<?= htmlspecialchars($data['template']->StaffType) ?>';

function updateStaffList() {
    const coachSel   = document.getElementById('coachList');
    const trainerSel = document.getElementById('trainerList');
    if (!coachSel || !trainerSel) return;

    // Determine which type to show: locked by template or chosen in dropdown
    const sel = document.getElementById('staffTypeSelect');
    const type = sel ? sel.value : TEMPLATE_STAFF_TYPE;

    if (type === 'coach') {
        coachSel.style.display   = '';
        coachSel.name            = 'user_id';
        trainerSel.style.display = 'none';
        trainerSel.name          = '_user_id_disabled';
    } else {
        trainerSel.style.display = '';
        trainerSel.name          = 'user_id';
        coachSel.style.display   = 'none';
        coachSel.name            = '_user_id_disabled';
    }
}
// Init on load — shows only the list matching the template's locked type
updateStaffList();
</script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
