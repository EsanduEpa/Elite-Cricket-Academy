<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.tt-wrap { background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:auto; }
.tt-table { width:100%;border-collapse:separate;border-spacing:0;min-width:1100px; }
.tt-table th { background:#f8f9fa;color:#555;font-size:13px;padding:12px 10px;border-bottom:2px solid #dee2e6;border-right:1px solid #eef2f6;text-align:center; }
.tt-table th:first-child { text-align:left;min-width:180px; }
.tt-time { background:#fcfcfd;font-weight:700;color:#2c3e50;vertical-align:top; }
.tt-time span { display:block;font-size:12px;color:#667085;font-weight:500;margin-top:4px; }
.tt-cell { vertical-align:top;min-width:130px;padding:8px;border-right:1px solid #eef2f6;border-bottom:1px solid #eef2f6;background:#fff; }
.tt-cell.today { background:#fffde7; }
.tt-empty { color:#d0d5dd;font-size:11px;text-align:center;padding:18px 0; }
.tt-card { display:block;text-decoration:none;border-radius:8px;padding:8px 9px;margin-bottom:6px;font-size:12px;line-height:1.45;box-shadow:inset 0 0 0 1px rgba(255,255,255,.25); }
.tt-card:last-child { margin-bottom:0; }
.tt-program { background:#dbeafe;color:#1d4ed8;border-left:3px solid #1d4ed8; }
.tt-private { background:#fef3c7;color:#92400e;border-left:3px solid #d97706; }
.tt-facility { background:#dcfce7;color:#166534;border-left:3px solid #16a34a; }
.tt-adhoc { background:#f3e8ff;color:#7c3aed;border-left:3px solid #7c3aed; }
.tt-cancelled { background:#e5e7eb;color:#6b7280;border-left:3px solid #9ca3af;text-decoration:line-through; }
.tt-name { font-weight:700;margin-bottom:2px; }
.tt-meta { opacity:.9; }
.tt-badge-row { display:flex;gap:6px;flex-wrap:wrap;margin:4px 0 6px; }
.tt-badge { display:inline-block;padding:2px 6px;border-radius:999px;font-size:10px;font-weight:700;line-height:1.2;background:rgba(255,255,255,.55); }
</style>

<?php
$getOccurrenceDisplayCount = static function($occurrence) {
    return ($occurrence->SlotType ?? '') === 'program'
        ? (int) ($occurrence->EligiblePlayerCount ?? 0)
        : (int) ($occurrence->BookingCount ?? 0);
};

$getOccurrenceCountLabel = static function($occurrence) use ($getOccurrenceDisplayCount) {
    return ($occurrence->SlotType ?? '') === 'program'
        ? 'Eligible players: ' . $getOccurrenceDisplayCount($occurrence)
        : 'Bookings: ' . $getOccurrenceDisplayCount($occurrence);
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

    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-table"></i> Weekly Timetable</h1>
                    <p>Weekly timetable view with time bands on the vertical axis and occurrence-filled days across the week.</p>
                </div>
            </div>
        </div>

        <div style="padding:0 25px 20px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable?start=<?= $data['prevWeek'] ?>" style="padding:7px 16px;background:#ecf0f1;border-radius:6px;text-decoration:none;color:#333;font-size:13px;"><i class="fas fa-chevron-left"></i> Prev Week</a>
                <span style="font-weight:700;font-size:15px;color:#2c3e50;"><?= date('j M Y', strtotime($data['from'])) ?> - <?= date('j M Y', strtotime($data['to'])) ?></span>
                <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable?start=<?= $data['nextWeek'] ?>" style="padding:7px 16px;background:#ecf0f1;border-radius:6px;text-decoration:none;color:#333;font-size:13px;">Next Week <i class="fas fa-chevron-right"></i></a>
                <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;background:#3498db;color:#fff;border-radius:6px;text-decoration:none;font-size:13px;margin-left:auto;">Today</a>
            </div>

            <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;font-size:12px;">
                <span style="background:#dbeafe;color:#1d4ed8;padding:3px 10px;border-radius:10px;">Program</span>
                <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:10px;">Private</span>
                <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:10px;">Facility Only</span>
                <span style="background:#f3e8ff;color:#7c3aed;padding:3px 10px;border-radius:10px;">Academy Event</span>
                <span style="background:#e5e7eb;color:#6b7280;padding:3px 10px;border-radius:10px;text-decoration:line-through;">Cancelled</span>
            </div>

            <div style="background:#fff;border-radius:12px;padding:12px 16px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:16px;font-size:13px;color:#475467;">
                <i class="fas fa-circle-info"></i> This timetable is rendered with server-side slot and date grouping logic. No external timetable library is used.
            </div>

            <div class="tt-wrap">
                <table class="tt-table">
                    <thead>
                        <tr>
                            <th>Time Band</th>
                            <?php $dayNames = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; ?>
                            <?php for ($i = 0; $i < 7; $i++): ?>
                                <?php $ts = strtotime("+{$i} days", $data['monTs']); $date = date('Y-m-d', $ts); $isToday = ($date === date('Y-m-d')); ?>
                                <th style="<?= $isToday ? 'background:#fffde7;' : '' ?>">
                                    <div style="font-weight:700;"><?= $dayNames[$i] ?></div>
                                    <div style="font-size:12px;color:#888;"><?= date('j M', $ts) ?></div>
                                </th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['timeBands'] as $band): ?>
                            <tr>
                                <th class="tt-time">
                                    <?= htmlspecialchars($band->SlotLabel ?? 'Band ' . $band->SlotID) ?>
                                    <span><?= htmlspecialchars(substr((string)($band->StartTime ?? ''), 0, 5)) ?> - <?= htmlspecialchars(substr((string)($band->EndTime ?? ''), 0, 5)) ?></span>
                                </th>
                                <?php for ($i = 0; $i < 7; $i++): ?>
                                    <?php $ts = strtotime("+{$i} days", $data['monTs']); $date = date('Y-m-d', $ts); $cellOccurrences = $data['grid'][$band->SlotID][$date] ?? []; ?>
                                    <td class="tt-cell<?= $date === date('Y-m-d') ? ' today' : '' ?>">
                                        <?php if (empty($cellOccurrences)): ?>
                                            <div class="tt-empty">-</div>
                                        <?php else: ?>
                                            <?php foreach ($cellOccurrences as $occurrence): ?>
                                                <?php
                                                $slotTypeLabelMap = ['program' => 'Program', 'private' => 'Private', 'facility_only' => 'Facility'];
                                                $slotTypeLabel = $slotTypeLabelMap[$occurrence->SlotType] ?? 'Occurrence';
                                                $statusLabel = ucfirst((string)($occurrence->Status ?? 'scheduled'));
                                                if (($occurrence->Status ?? '') === 'cancelled') {
                                                    $cardClass = 'tt-cancelled';
                                                } elseif (($occurrence->TemplateID ?? null) === null) {
                                                    $cardClass = 'tt-adhoc';
                                                } else {
                                                    $cardMap = ['program' => 'tt-program', 'private' => 'tt-private', 'facility_only' => 'tt-facility'];
                                                    $cardClass = $cardMap[$occurrence->SlotType] ?? 'tt-program';
                                                }
                                                ?>
                                                <a href="<?php echo URLROOT; ?>/adminslots/occurrence/<?= $occurrence->OccurrenceID ?>" class="tt-card <?= $cardClass ?>">
                                                    <?php if (!empty($occurrence->TemplateCode)): ?>
                                                        <div style="font-size:10px;letter-spacing:.4px;opacity:.75;margin-bottom:2px;">#<?= htmlspecialchars($occurrence->TemplateCode) ?></div>
                                                    <?php endif; ?>
                                                    <div class="tt-name"><?= $occurrence->TemplateName ? htmlspecialchars($occurrence->TemplateName) : 'Academy Event' ?></div>
                                                    <div class="tt-badge-row">
                                                        <span class="tt-badge"><?= htmlspecialchars($slotTypeLabel) ?></span>
                                                        <span class="tt-badge"><?= htmlspecialchars($statusLabel) ?></span>
                                                    </div>
                                                    <div class="tt-meta"><strong>Facility:</strong> <?= htmlspecialchars($occurrence->FacilityName ?? 'Not assigned') ?></div>
                                                    <div class="tt-meta"><strong><?= htmlspecialchars($getOccurrenceCountLabel($occurrence)) ?></strong></div>
                                                    <?php if (!empty($occurrence->StaffNames)): ?>
                                                        <div class="tt-meta"><strong>Staff:</strong> <?= htmlspecialchars($occurrence->StaffNames) ?></div>
                                                    <?php endif; ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
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