<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.cal-cell      { border:1px solid #e0e0e0; vertical-align:top; min-height:120px; width:14.28%; padding:8px; background:#fff; }
.cal-today     { background:#fffde7; }
.cal-date-hdr  { font-size:11px; font-weight:700; color:#888; margin-bottom:6px; }
.cal-card      { border-radius:6px; padding:6px 8px; margin-bottom:5px; font-size:12px; cursor:pointer; }
.cal-program   { background:#cce5ff; color:#004085; border-left:3px solid #004085; }
.cal-private   { background:#fff3cd; color:#856404; border-left:3px solid #856404; }
.cal-facility  { background:#d4edda; color:#155724; border-left:3px solid #155724; }
.cal-cancelled { background:#e9ecef; color:#6c757d; border-left:3px solid #aaa; text-decoration:line-through; }
.cal-adhoc     { background:#f3e5f5; color:#4a1e8c; border-left:3px solid #9b59b6; }
.cal-mismatch  { background:#f8d7da; color:#721c24; border-left:3px solid #c0392b; }
</style>

<?php
$getOccurrenceDisplayCount = static function($occ) {
    return ($occ->SlotType ?? '') === 'program'
        ? (int) ($occ->EligiblePlayerCount ?? 0)
        : (int) ($occ->BookingCount ?? 0);
};

$getOccurrenceCountLabel = static function($occ) use ($getOccurrenceDisplayCount) {
    $count = $getOccurrenceDisplayCount($occ);
    return ($occ->SlotType ?? '') === 'program'
        ? $count . ' eligible player(s)'
        : $count . ' booked';
};
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
                    <h1><i class="fas fa-calendar-alt"></i> Occurrence Calendar</h1>
                    <p>Weekly view of all scheduled, active, and cancelled occurrences. Click any card to view details.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/adhoc" style="padding:9px 18px;border-radius:8px;background:#9b59b6;color:#fff;text-decoration:none;font-size:14px;font-weight:600;">
                        <i class="fas fa-plus"></i> Academy Event
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px;">

            <!-- Week navigation -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
                <a href="<?php echo URLROOT; ?>/adminslots/calendar?start=<?= $data['prevWeek'] ?>"
                   style="padding:7px 16px;background:#ecf0f1;border-radius:6px;text-decoration:none;color:#333;font-size:13px;">
                    <i class="fas fa-chevron-left"></i> Prev Week
                </a>
                <span style="font-weight:700;font-size:15px;color:#2c3e50;">
                    <?= date('j M Y', strtotime($data['from'])) ?> &mdash; <?= date('j M Y', strtotime($data['to'])) ?>
                </span>
                <a href="<?php echo URLROOT; ?>/adminslots/calendar?start=<?= $data['nextWeek'] ?>"
                   style="padding:7px 16px;background:#ecf0f1;border-radius:6px;text-decoration:none;color:#333;font-size:13px;">
                    Next Week <i class="fas fa-chevron-right"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/adminslots/calendar"
                   style="padding:7px 16px;background:#3498db;color:#fff;border-radius:6px;text-decoration:none;font-size:13px;margin-left:auto;">
                    Today
                </a>
            </div>

            <?php if (isset($_GET['generated'])): ?>
                <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
                    <i class="fas fa-check-circle"></i> Successfully generated <strong><?= (int)($_GET['count'] ?? 0) ?></strong> occurrence(s). They are now visible on the calendar below.
                </div>
            <?php endif; ?>

            <!-- Legend -->
            <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;font-size:12px;">
                <span style="background:#cce5ff;color:#004085;padding:3px 10px;border-radius:10px;">Program</span>
                <span style="background:#fff3cd;color:#856404;padding:3px 10px;border-radius:10px;">Private</span>
                <span style="background:#d4edda;color:#155724;padding:3px 10px;border-radius:10px;">Facility Only</span>
                <span style="background:#f3e5f5;color:#4a1e8c;padding:3px 10px;border-radius:10px;">Academy Event</span>
                <span style="background:#e9ecef;color:#6c757d;padding:3px 10px;border-radius:10px;text-decoration:line-through;">Cancelled</span>
            </div>

            <!-- Calendar grid -->
            <div style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <?php
                            $dayNames = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                            for ($i = 0; $i < 7; $i++):
                                $ts   = strtotime("+{$i} days", $data['monTs']);
                                $date = date('Y-m-d', $ts);
                                $isToday = ($date === date('Y-m-d'));
                            ?>
                            <th style="padding:10px 8px;text-align:center;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;<?= $isToday ? 'background:#fffde7;' : '' ?>">
                                <div style="font-weight:700;"><?= $dayNames[$i] ?></div>
                                <div style="font-size:12px;color:#888;"><?= date('j M', $ts) ?></div>
                            </th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            for ($i = 0; $i < 7; $i++):
                                $ts   = strtotime("+{$i} days", $data['monTs']);
                                $date = date('Y-m-d', $ts);
                                $isToday = ($date === date('Y-m-d'));
                                $occs = $data['byDate'][$date] ?? [];
                            ?>
                            <td class="cal-cell<?= $isToday ? ' cal-today' : '' ?>">
                                <?php if (empty($occs)): ?>
                                    <div style="color:#ccc;font-size:11px;text-align:center;padding-top:20px;">—</div>
                                <?php else: ?>
                                    <?php foreach ($occs as $occ):
                                        $occDay = (int) date('N', strtotime($occ->OccurrenceDate));
                                        $templateDay = (int) ($occ->TemplateDayOfWeek ?? 0);
                                        $dayMismatch = $occ->TemplateID && $templateDay > 0 && $occDay !== $templateDay;
                                        if ($occ->Status === 'cancelled') {
                                            $cls = 'cal-cancelled';
                                        } elseif ($occ->TemplateID === null) {
                                            $cls = 'cal-adhoc';
                                        } elseif ($dayMismatch) {
                                            $cls = 'cal-mismatch';
                                        } else {
                                            $map = ['program'=>'cal-program','private'=>'cal-private','facility_only'=>'cal-facility'];
                                            $cls = $map[$occ->SlotType] ?? 'cal-program';
                                        }
                                    ?>
                                    <a href="<?php echo URLROOT; ?>/adminslots/occurrence/<?= $occ->OccurrenceID ?>"
                                       style="display:block;text-decoration:none;">
                                        <div class="cal-card <?= $cls ?>">
                                            <div style="font-weight:700;margin-bottom:2px;">
                                                <?php if (!empty($occ->TemplateCode)): ?>
                                                    <span style="display:block;font-size:10px;letter-spacing:.4px;color:inherit;opacity:.7;">#<?= htmlspecialchars($occ->TemplateCode) ?></span>
                                                <?php endif; ?>
                                                <?= $occ->TemplateName ? htmlspecialchars($occ->TemplateName) : '<em>Academy Event</em>' ?>
                                            </div>
                                            <div><?= htmlspecialchars($occ->SlotLabel ?? '—') ?></div>
                                            <?php if ($occ->FacilityName): ?>
                                                <div style="opacity:.8;"><?= htmlspecialchars($occ->FacilityName) ?></div>
                                            <?php endif; ?>
                                            <?php if ($occ->StaffNames): ?>
                                                <div style="opacity:.75;margin-top:2px;"><i class="fas fa-user" style="font-size:10px;"></i> <?= htmlspecialchars($occ->StaffNames) ?></div>
                                            <?php endif; ?>
                                            <?php if ($dayMismatch): ?>
                                                <div style="margin-top:4px;font-size:11px;font-weight:700;">
                                                    <i class="fas fa-exclamation-triangle"></i> Date does not match template weekday
                                                </div>
                                            <?php endif; ?>
                                            <div style="margin-top:3px;">
                                                <i class="fas fa-users" style="font-size:10px;"></i> <?= htmlspecialchars($getOccurrenceCountLabel($occ)) ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
