<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.detail-card  { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px; }
.detail-grid  { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px; }
.detail-label { font-size:12px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px; }
.detail-value { font-size:14px;color:#2c3e50;font-weight:500; }
.badge-program        { background:#cce5ff;color:#004085;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-private        { background:#fff3cd;color:#856404;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-facility       { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-active         { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-inactive       { background:#f8d7da;color:#721c24;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-lead           { background:#cce5ff;color:#004085;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-assistant      { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-substitute     { background:#fff3cd;color:#856404;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.occ-row              { border-bottom:1px solid #eef2f6; }
.btn-header           { padding:9px 18px;border-radius:8px;color:#fff;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:6px;transition:all 0.2s ease; }
.btn-primary          { background:#3498db; }
.btn-primary:hover    { background:#2980b9;box-shadow:0 2px 8px rgba(52,152,219,.3); }
.btn-success          { background:#27ae60; }
.btn-success:hover    { background:#229954;box-shadow:0 2px 8px rgba(39,174,96,.3); }
.btn-purple           { background:#9b59b6; }
.btn-purple:hover     { background:#8e44ad;box-shadow:0 2px 8px rgba(155,89,182,.3); }
.btn-secondary        { background:#ecf0f1;color:#333; }
.btn-secondary:hover  { background:#d5dbdb;box-shadow:0 2px 8px rgba(0,0,0,.1); }
.btn-card             { padding:8px 16px;border-radius:8px;color:#fff;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:6px;transition:all 0.2s ease; }
.btn-link             { padding:6px 12px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:12px;display:inline-flex;align-items:center;gap:4px;transition:all 0.2s ease; }
.btn-link:hover       { background:#2980b9;box-shadow:0 2px 6px rgba(52,152,219,.3); }
</style>

<?php
$template = $data['template'];
$staff = $data['staff'];
$occurrences = $data['occurrences'];
$dayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
$getOccurrenceDisplayCount = static function($occurrence) {
    return ($occurrence->SlotType ?? '') === 'program'
        ? (int) ($occurrence->EligiblePlayerCount ?? 0)
        : (int) ($occurrence->BookingCount ?? 0);
};
?>

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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finances</span></a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-eye"></i> Template Detail</h1>
                    <p>View the full template record, assigned staff, and generated occurrences.</p>
                </div>
                <div class="header-actions" style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="<?php echo URLROOT; ?>/adminslots/edittemplate/<?= (int)$template->TemplateID ?>" class="btn-header btn-primary"><i class="fas fa-edit"></i> Edit</a>
                    <a href="<?php echo URLROOT; ?>/adminslots/staff/<?= (int)$template->TemplateID ?>" class="btn-header btn-purple"><i class="fas fa-users"></i> Staff</a>
                    <a href="<?php echo URLROOT; ?>/adminslots/generate?template_id=<?= (int)$template->TemplateID ?>" class="btn-header btn-success"><i class="fas fa-calendar-plus"></i> Generate</a>
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="btn-header btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
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

        <div style="padding:0 25px 40px;">
            <div class="detail-card">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
                    <div>
                        <div style="font-size:12px;color:#6c757d;font-weight:700;letter-spacing:.4px;margin-bottom:6px;">#<?= htmlspecialchars($template->temp_code ?? '—') ?></div>
                        <h2 style="margin:0;color:#2c3e50;"><?= htmlspecialchars($template->TemplateName) ?></h2>
                        <div style="margin-top:6px;color:#6c757d;font-size:13px;">Template ID <?= (int)$template->TemplateID ?></div>
                    </div>
                    <div>
                        <?php
                        $typeClassMap = ['program' => 'badge-program', 'private' => 'badge-private', 'facility_only' => 'badge-facility'];
                        $typeLabel = ucfirst(str_replace('_', ' ', (string)$template->SlotType));
                        ?>
                        <span class="<?= $typeClassMap[$template->SlotType] ?? 'badge-program' ?>"><?= htmlspecialchars($typeLabel) ?></span>
                        <?php if (!empty($template->IsActive)): ?>
                            <span class="badge-active" style="margin-left:8px;">Active</span>
                        <?php else: ?>
                            <span class="badge-inactive" style="margin-left:8px;">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-grid">
                    <div><div class="detail-label">Template Code</div><div class="detail-value"><?= htmlspecialchars($template->temp_code ?? '—') ?></div></div>
                    <div><div class="detail-label">Template Name</div><div class="detail-value"><?= htmlspecialchars($template->TemplateName ?? '—') ?></div></div>
                    <div><div class="detail-label">Slot Type</div><div class="detail-value"><?= htmlspecialchars($typeLabel) ?></div></div>
                    <div><div class="detail-label">Staff Type</div><div class="detail-value"><?= htmlspecialchars(ucfirst((string)($template->StaffType ?? '—'))) ?></div></div>
                    <div><div class="detail-label">Time Band</div><div class="detail-value"><?= htmlspecialchars($template->SlotLabel ?? '—') ?></div></div>
                    <div><div class="detail-label">Day Of Week</div><div class="detail-value"><?= !empty($template->DayOfWeek) ? htmlspecialchars($dayNames[(int)$template->DayOfWeek] ?? '—') : 'Any day' ?></div></div>
                    <div><div class="detail-label">Facility</div><div class="detail-value"><?= htmlspecialchars($template->FacilityName ?? '—') ?></div></div>
                    <div><div class="detail-label">Age Group</div><div class="detail-value"><?= htmlspecialchars($template->AgeGroup ?? '—') ?></div></div>
                    <div><div class="detail-label">Eligible Players</div><div class="detail-value"><?= !empty($data['eligiblePlayerCount']) ? (int)$data['eligiblePlayerCount'] : '—' ?></div></div>
                    <div><div class="detail-label">Category</div><div class="detail-value"><?= htmlspecialchars($template->Category ?? '—') ?></div></div>
                    <div><div class="detail-label">Max Participants</div><div class="detail-value"><?= htmlspecialchars((string)($template->MaxParticipants ?? '—')) ?></div></div>
                    <div><div class="detail-label">Price Per Session</div><div class="detail-value"><?= isset($template->PricePerSession) ? 'Rs ' . number_format((float)$template->PricePerSession, 2) : '—' ?></div></div>
                    <div><div class="detail-label">Required Plan Feature</div><div class="detail-value"><?= htmlspecialchars($template->RequiredPlanFeature ?? '—') ?></div></div>
                </div>

                <?php if (!empty($template->Description)): ?>
                    <div style="margin-top:18px;">
                        <div class="detail-label">Description</div>
                        <div class="detail-value" style="line-height:1.6;"><?= nl2br(htmlspecialchars($template->Description)) ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-card">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
                    <h3 style="margin:0;color:#2c3e50;"><i class="fas fa-users"></i> Assigned Staff</h3>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <span style="color:#6c757d;font-size:13px;">Total: <?= count($staff) ?></span>
                        <?php if (count($staff) === 0): ?>
                            <a href="<?php echo URLROOT; ?>/adminslots/staff/<?= (int)$template->TemplateID ?>" class="btn-card btn-purple">
                                <i class="fas fa-users"></i> Add Staff
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (empty($staff)): ?>
                    <div style="color:#6c757d;">No staff assigned to this template yet.</div>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Name</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Role</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Staff Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($staff as $member): ?>
                                    <tr class="occ-row">
                                        <td style="padding:10px 12px;"><?= htmlspecialchars($member->UserName ?? '—') ?></td>
                                        <td style="padding:10px 12px;">
                                            <?php
                                            $roleClass = strtolower((string)($member->StaffRole ?? ''));
                                            $roleClass = in_array($roleClass, ['lead', 'assistant', 'substitute'], true) ? 'badge-' . $roleClass : 'badge-substitute';
                                            ?>
                                            <span class="<?= $roleClass ?>"><?= htmlspecialchars(ucfirst((string)($member->StaffRole ?? 'Member'))) ?></span>
                                        </td>
                                        <td style="padding:10px 12px;"><?= htmlspecialchars(ucfirst((string)($member->StaffType ?? '—'))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-card">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
                    <h3 style="margin:0;color:#2c3e50;"><i class="fas fa-calendar-alt"></i> Generated Occurrences</h3>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <span style="color:#6c757d;font-size:13px;">Total: <?= (int)$data['occurrenceCount'] ?></span>
                        <a href="<?php echo URLROOT; ?>/adminslots/generate?template_id=<?= (int)$template->TemplateID ?>" class="btn-card btn-success">
                            <i class="fas fa-calendar-plus"></i> Generate Occurrences
                        </a>
                    </div>
                </div>
                <?php if (empty($occurrences)): ?>
                    <div style="color:#6c757d;">No occurrences have been generated for this template yet.</div>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Date</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Time Band</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Facility</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Count</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Status</th>
                                    <th style="text-align:left;padding:10px 12px;border-bottom:2px solid #dee2e6;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($occurrences as $occurrence): ?>
                                    <tr class="occ-row">
                                        <td style="padding:10px 12px;"><?= htmlspecialchars(date('d M Y', strtotime($occurrence->OccurrenceDate))) ?></td>
                                        <td style="padding:10px 12px;"><?= htmlspecialchars($occurrence->SlotLabel ?? '—') ?></td>
                                        <td style="padding:10px 12px;"><?= htmlspecialchars($occurrence->FacilityName ?? '—') ?></td>
                                        <td style="padding:10px 12px;"><?= $getOccurrenceDisplayCount($occurrence) ?></td>
                                        <td style="padding:10px 12px;"><?= htmlspecialchars(ucfirst((string)($occurrence->Status ?? 'scheduled'))) ?></td>
                                        <td style="padding:10px 12px;">
                                            <a href="<?php echo URLROOT; ?>/adminslots/occurrence/<?= (int)$occurrence->OccurrenceID ?>" class="btn-link">
                                                <i class="fas fa-eye"></i> View
                                            </a>
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
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>