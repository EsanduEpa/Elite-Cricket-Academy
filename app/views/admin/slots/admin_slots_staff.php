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
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <a href="<?php echo URLROOT; ?>/adminslots/templates" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;font-weight:600;">
                            <i class="fas fa-arrow-left"></i> Back to Templates
                        </a>
                        <div style="font-size:12px;color:#aaa;">
                            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="color:#3498db;text-decoration:none;">Templates</a>
                            <i class="fas fa-chevron-right" style="font-size:10px;margin:0 6px;"></i>
                            <?= htmlspecialchars($data['template']->TemplateName) ?>
                            <i class="fas fa-chevron-right" style="font-size:10px;margin:0 6px;"></i>
                            Staff
                        </div>
                    </div>
                    <h1><i class="fas fa-users"></i> Staff Assignment</h1>
                    <p><?= htmlspecialchars($data['template']->TemplateName) ?> — <?= htmlspecialchars($data['template']->SlotLabel ?? '') ?>
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
                        <select name="user_id" id="coachList" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                            <?php if (empty($data['coaches'])): ?>
                                <option value="">No coaches available</option>
                            <?php else: ?>
                                <?php foreach ($data['coaches'] as $c): ?>
                                    <option value="<?= $c->UserID ?>"><?= htmlspecialchars($c->Name) ?></option>
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

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Staff Role</label>
                        <select name="staff_role" style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                            <option value="lead">Lead</option>
                            <option value="assistant">Assistant</option>
                        </select>
                    </div>

                    <button type="submit" style="width:100%;padding:10px;background:#9b59b6;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">
                        <i class="fas fa-user-plus"></i> Assign Staff
                    </button>
                </form>
            </div>
        </div>

        <!-- Proceed to Generate -->
        <div style="padding:0 25px 20px;">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="proceed_generate" value="1">
                <button type="submit" style="padding:12px 28px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">
                    <i class="fas fa-arrow-right"></i> Proceed to Generate Occurrences
                </button>
            </form>
            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="margin-left:12px;padding:12px 20px;background:#ecf0f1;color:#333;border-radius:8px;text-decoration:none;font-size:14px;display:inline-block;">
                <i class="fas fa-list"></i> Back to Templates
            </a>
        </div>

        <div style="padding:0 25px 30px;">
            <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <h3 style="margin:0 0 8px;font-size:16px;color:#2c3e50;"><i class="fas fa-list"></i> Current Coach Assignment Matrix</h3>
                    <p style="margin:0;color:#64748b;font-size:13px;">The live coach assignment matrix is managed from Staff Management.</p>
                </div>
                <a href="<?php echo URLROOT; ?>/admin/staff#coachAssignmentsModal" style="padding:10px 18px;background:#ecf0f1;color:#333;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
                    <i class="fas fa-user-tie"></i> Open in Staff Management
                </a>
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
