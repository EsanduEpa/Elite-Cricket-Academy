<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-list-tools.css">

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
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Private Requests</a>
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
            <div class="admin-list-shell" data-admin-list data-page-size="10">
                <div class="admin-list-toolbar">
                    <div class="admin-list-search">
                        <i class="fas fa-search"></i>
                        <input type="text" data-list-filter="search" placeholder="Search template, code, facility...">
                    </div>
                    <select data-list-filter="type">
                        <option value="all">All Types</option>
                        <option value="program">Program</option>
                        <option value="private">Private</option>
                        <option value="facility_only">Facility Only</option>
                    </select>
                    <select data-list-filter="day">
                        <option value="all">All Days</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo htmlspecialchars($dayNames[$i]); ?></option>
                        <?php endfor; ?>
                        <option value="any">Any Day</option>
                    </select>
                    <select data-list-filter="status">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <select data-list-filter="facility">
                        <option value="all">All Facilities</option>
                        <?php
                            $facilities = array_values(array_unique(array_filter(array_map(fn($t) => trim((string)($t->FacilityName ?? '')), $data['templates']))));
                            sort($facilities);
                            foreach ($facilities as $facility):
                        ?>
                            <option value="<?php echo htmlspecialchars(strtolower($facility)); ?>"><?php echo htmlspecialchars($facility); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                </div>
                <div class="admin-list-table-wrap">
                <table class="admin-compact-table">
                    <thead>
                        <tr>
                            <th>Template</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Time Band</th>
                            <th>Day</th>
                            <th>Facility</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody data-list-body>
                        <?php foreach ($data['templates'] as $t): ?>
                        <?php
                            $dayKey = $t->DayOfWeek ? (string)(int)$t->DayOfWeek : 'any';
                            $statusKey = $t->IsActive ? 'active' : 'inactive';
                            $facilityKey = strtolower(trim((string)($t->FacilityName ?? '')));
                            $searchText = trim(($t->TemplateName ?? '') . ' ' . ($t->temp_code ?? '') . ' ' . ($t->SlotType ?? '') . ' ' . ($t->SlotLabel ?? '') . ' ' . ($t->FacilityName ?? '') . ' ' . ($t->AgeGroup ?? '') . ' ' . ($t->Category ?? ''));
                        ?>
                        <tr data-list-row
                            data-search="<?= htmlspecialchars(strtolower($searchText)) ?>"
                            data-filter-type="<?= htmlspecialchars(strtolower((string)$t->SlotType)) ?>"
                            data-filter-day="<?= htmlspecialchars($dayKey) ?>"
                            data-filter-status="<?= htmlspecialchars($statusKey) ?>"
                            data-filter-facility="<?= htmlspecialchars($facilityKey) ?>">
                            <td>
                                <span class="admin-list-primary"><?= htmlspecialchars($t->TemplateName) ?></span>
                                <?php if ($t->AgeGroup): ?><span class="admin-list-muted"><?= htmlspecialchars($t->AgeGroup) ?><?= $t->Category ? ' · ' . htmlspecialchars($t->Category) : '' ?></span><?php endif; ?>
                            </td>
                            <td style="font-weight:700;letter-spacing:.2px;">
                                <?= htmlspecialchars($t->temp_code ?? '—') ?>
                            </td>
                            <td>
                                <span class="admin-pill pill-<?= htmlspecialchars($t->SlotType ?: 'neutral') ?>">
                                    <?= htmlspecialchars(ucfirst(str_replace('_',' ', $t->SlotType))) ?>
                                </span>
                            </td>
                            
                            <td><?= htmlspecialchars($t->SlotLabel ?? '—') ?></td>
                            <td><?= $t->DayOfWeek ? $dayNames[(int)$t->DayOfWeek] : '<span style="color:#aaa;">Any</span>' ?></td>
                            <td><?= htmlspecialchars($t->FacilityName ?? '—') ?></td>
                           
                            <td>
                                <?php if ($t->IsActive): ?>
                                    <span class="admin-pill pill-active">Active</span>
                                <?php else: ?>
                                    <span class="admin-pill pill-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-list-actions">
                                    <a href="<?php echo URLROOT; ?>/adminslots/template_detail/<?= $t->TemplateID ?>" class="admin-list-action view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo URLROOT; ?>/adminslots/edittemplate/<?= $t->TemplateID ?>" class="admin-list-action edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($t->SlotType === 'facility_only'): ?>
                                        <a class="admin-list-action disabled" tabindex="-1" title="Staff assignment not available for facility-only templates">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo URLROOT; ?>/adminslots/staff/<?= $t->TemplateID ?>" class="admin-list-action staff" title="Assign Staff">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="toggle_template" value="<?= $t->TemplateID ?>">
                                        <?php if ($t->IsActive): ?>
                                            <button type="submit" class="admin-list-reset" style="background:#fee2e2;color:#991b1b;">Deactivate</button>
                                        <?php else: ?>
                                            <button type="submit" class="admin-list-reset" style="background:#dcfce7;color:#166534;">Activate</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="admin-list-empty-row" data-list-empty-row style="display:none;">
                            <td colspan="8"><i class="fas fa-filter"></i> No templates match the selected filters.</td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <div class="admin-list-footer">
                    <div class="admin-list-count" data-list-count></div>
                    <div class="admin-list-pagination" data-list-pagination></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/admin-list-tools.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
