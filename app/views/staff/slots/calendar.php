<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach  = $data['role'] === 'Coach';
$trainerSidebarActive = 'slots';
$cssFile  = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout   = $isCoach ? 'coach-layout'    : 'trainer-layout';
$sidebar  = $isCoach ? 'coach-sidebar'   : 'trainer-sidebar';
$logo     = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
$panelName = $isCoach ? 'Coach Panel'    : 'Trainer Panel';
$dashHref  = $isCoach ? '/coach/dashboard' : '/trainer';

$getOccurrenceDisplayCount = static function($occ) {
    return ($occ->SlotType ?? '') === 'program'
        ? (int) ($occ->EligiblePlayerCount ?? 0)
        : (int) ($occ->BookingCount ?? 0);
};

$getOccurrenceCountLabel = static function($occ) use ($getOccurrenceDisplayCount) {
    $count = $getOccurrenceDisplayCount($occ);
    return ($occ->SlotType ?? '') === 'program'
        ? $count . ' eligible players'
        : $count . ' booked';
};
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/<?= $cssFile ?>.css">
<style>
.cal-calendar-shell {
    background: rgba(255,255,255,0.92);
    border: 1px solid rgba(255,255,255,0.45);
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    overflow: hidden;
}
.cal-calendar-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: rgba(255,255,255,0.92);
}
.cal-calendar-table thead tr {
    background: #f8f9fa;
}
.cal-calendar-table th {
    border-bottom: 2px solid #dee2e6;
}
.cal-cell      { border:1px solid #e0e0e0; vertical-align:top; min-height:120px; width:14.28%; padding:8px; background:rgba(255,255,255,0.96); }
.cal-today     { background:rgba(255,253,231,0.96); }
.cal-card      { border-radius:6px; padding:6px 8px; margin-bottom:5px; font-size:12px; cursor:pointer; text-decoration:none; display:block; }
.cal-program   { background:#cce5ff; color:#004085; border-left:3px solid #004085; }
.cal-private   { background:#e8f5e9; color:#1b5e20; border-left:3px solid #2e7d32; }
.cal-cancelled { background:#e9ecef; color:#6c757d; border-left:3px solid #aaa; text-decoration:line-through !important; }
.cal-status-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:999px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
.cal-status-scheduled { background:#e3f2fd; color:#1565c0; }
.cal-status-active { background:#d4edda; color:#155724; }
.cal-status-completed { background:#e2d9f3; color:#4a1e8c; }
.cal-status-cancelled { background:#f8d7da; color:#721c24; }
.subnav-link   { padding:7px 16px; border-radius:6px; background:#ecf0f1; color:#333; text-decoration:none; font-size:13px; }
.subnav-active { background:#2e7d32; color:#fff; font-weight:600; }
</style>

<div class="<?= $layout ?>">
    <!-- ── Sidebar ── -->
    <div class="<?= $sidebar ?>" id="staffSidebar">
        <div class="sidebar-header">
            <div class="<?= $isCoach ? 'coach-logo' : 'trainer-info' ?>">
                <?php if ($isCoach): ?>
                    <i class="fas <?= $logo ?>"></i><h3><?= $panelName ?></h3>
                <?php else: ?>
                    <div class="trainer-avatar"><i class="fas <?= $logo ?>"></i></div>
                    <div class="trainer-details">
                        <h4><?= htmlspecialchars($_SESSION['user_name'] ?? 'Trainer') ?></h4>
                        <p>Physical Trainer</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>

        <?php if ($isCoach): ?>
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard"  class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players"    class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link"><i class="fas fa-star"></i><span>Recommendations</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health"     class="nav-link"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/events"     class="nav-link"><i class="fas fa-calendar"></i><span>Events</span></a></li>
                </ul>
            </nav>
        <?php else: ?>
            <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        <?php endif; ?>

        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ($data['role'] ?? 'Staff'); ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/<?php echo strtolower($data['role'] ?? 'coach'); ?>/profile" class="profile-avatar" aria-label="Open profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Main Content ── -->
    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-alt"></i> My Slot Sessions</h1>
                    <p>Weekly view of sessions you are assigned to.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/staffslots/private_session"
                       style="padding:9px 18px;border-radius:8px;background:#2e7d32;color:#fff;text-decoration:none;font-size:14px;font-weight:600;">
                        <i class="fas fa-paper-plane"></i> Request Private Session
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="subnav-link subnav-active">
                <i class="fas fa-calendar-alt"></i> Calendar
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/private_session" class="subnav-link">
                <i class="fas fa-paper-plane"></i> Request Private Session
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/past_requests" class="subnav-link">
                <i class="fas fa-history"></i> Past Requests
            </a>
           
        </div>

        <div style="padding:0 25px 40px;">

            <?php flash('session_message'); ?>

            <!-- Week navigation -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                <a href="<?php echo URLROOT; ?>/staffslots/calendar/<?= $data['prevWeek'] ?>"
                   style="padding:7px 16px;background:#ecf0f1;border-radius:6px;text-decoration:none;color:#333;font-size:13px;">
                    <i class="fas fa-chevron-left"></i> Prev Week
                </a>
                <span style="font-weight:700;font-size:15px;color:#2c3e50;">
                    <?= date('j M Y', strtotime($data['from'])) ?> &mdash; <?= date('j M Y', strtotime($data['to'])) ?>
                </span>
                <a href="<?php echo URLROOT; ?>/staffslots/calendar/<?= $data['nextWeek'] ?>"
                   style="padding:7px 16px;background:#ecf0f1;border-radius:6px;text-decoration:none;color:#333;font-size:13px;">
                    Next Week <i class="fas fa-chevron-right"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/staffslots/calendar"
                   style="padding:7px 16px;background:#2e7d32;color:#fff;border-radius:6px;text-decoration:none;font-size:13px;margin-left:auto;">
                    Today
                </a>
            </div>

            <!-- Summary row -->
            <?php
            $totalSessions = 0;
            $totalExpected = 0;
            foreach ($data['byDate'] as $occs) {
                foreach ($occs as $occ) {
                    if ($occ->Status !== 'cancelled') {
                        $totalSessions++;
                        $totalExpected += $getOccurrenceDisplayCount($occ);
                    }
                }
            }
            ?>
            <div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
                <div style="background:#fff;border-radius:10px;padding:14px 22px;box-shadow:0 1px 6px rgba(0,0,0,.07);min-width:140px;">
                    <div style="font-size:22px;font-weight:700;color:#2e7d32;"><?= $totalSessions ?></div>
                    <div style="font-size:12px;color:#888;margin-top:2px;">Sessions this week</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:14px 22px;box-shadow:0 1px 6px rgba(0,0,0,.07);min-width:140px;">
                    <div style="font-size:22px;font-weight:700;color:#004085;"><?= $totalExpected ?></div>
                    <div style="font-size:12px;color:#888;margin-top:2px;">Eligible / booked players</div>
                </div>
            </div>

            <!-- Legend -->
            <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;font-size:12px;">
                <span style="background:#cce5ff;color:#004085;padding:3px 10px;border-radius:10px;">Program</span>
                <span style="background:#e8f5e9;color:#1b5e20;padding:3px 10px;border-radius:10px;">Private</span>
                <span style="background:#e9ecef;color:#6c757d;padding:3px 10px;border-radius:10px;text-decoration:line-through;">Cancelled</span>
            </div>

            <!-- Calendar grid -->
            <div class="cal-calendar-shell">
                <table class="cal-calendar-table">
                    <thead>
                        <tr>
                            <?php
                            $dayNames = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                            for ($i = 0; $i < 7; $i++):
                                $ts      = strtotime("+{$i} days", $data['monTs']);
                                $date    = date('Y-m-d', $ts);
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
                            <?php for ($i = 0; $i < 7; $i++):
                                $ts      = strtotime("+{$i} days", $data['monTs']);
                                $date    = date('Y-m-d', $ts);
                                $isToday = ($date === date('Y-m-d'));
                                $occs    = $data['byDate'][$date] ?? [];
                            ?>
                            <td class="cal-cell<?= $isToday ? ' cal-today' : '' ?>">
                                <?php if (empty($occs)): ?>
                                    <div style="color:#ccc;font-size:11px;text-align:center;padding-top:20px;">—</div>
                                <?php else:
                                    foreach ($occs as $occ):
                                        $statusKey = strtolower((string) ($occ->Status ?? 'scheduled'));
                                        $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
                                        if ($occ->Status === 'cancelled') {
                                            $cls = 'cal-cancelled';
                                        } elseif ($occ->SlotType === 'private' || $occ->SessionName === 'Private Session') {
                                            $cls = 'cal-private';
                                        } else {
                                            $cls = 'cal-program';
                                        }
                                ?>
                                    <a href="<?php echo URLROOT; ?>/staffslots/occurrence/<?= $occ->OccurrenceID ?>"
                                       class="cal-card <?= $cls ?>">
                                        <div style="font-weight:700;margin-bottom:2px;">
                                            <?= htmlspecialchars($occ->SessionName) ?>
                                        </div>
                                        <div><?= htmlspecialchars($occ->SlotLabel ?? '—') ?></div>
                                        <?php if ($occ->FacilityName): ?>
                                            <div style="opacity:.8;"><?= htmlspecialchars($occ->FacilityName) ?></div>
                                        <?php endif; ?>
                                        <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;margin-top:4px;flex-wrap:wrap;">
                                            <div style="font-size:11px;">
                                                <i class="fas fa-users" style="font-size:10px;"></i>
                                                <?= htmlspecialchars($getOccurrenceCountLabel($occ)) ?>
                                            </div>
                                            <span class="cal-status-badge cal-status-<?= htmlspecialchars($statusKey) ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; endif; ?>
                            </td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Attendance Overview -->
            <div id="attendance" style="margin-top:28px;background:rgba(255,255,255,0.9);border:1px solid rgba(255,255,255,0.45);border-radius:18px;padding:24px;box-shadow:0 8px 32px rgba(31,38,135,0.18);">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
                    <div>
                        <h2 style="margin:0;font-size:20px;color:#2c3e50;"><i class="fas fa-chart-area" style="color:#4A90E2;margin-right:8px;"></i>Attendance & Session Participation</h2>
                        <p style="margin-top:6px;color:#666;font-size:13px;">Weekly session totals for the schedule shown above.</p>
                    </div>
                    <div class="controls" style="display:flex;gap:10px;flex-wrap:wrap;">
                        <select id="attendanceTeamSelect" style="padding:10px 14px;border:1px solid #d9dee7;border-radius:10px;background:#fff;min-width:220px;"></select>
                    </div>
                </div>
                <div style="height:320px;">
                    <canvas id="attendanceChart" aria-label="Attendance per session"></canvas>
                </div>
                <div style="margin-top:14px;color:#7a7a7a;font-size:13px;">Use the selector to switch between all sessions, program sessions, and private sessions.</div>
            </div>

        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script>
window.__COACH_DASHBOARD_DATA = <?php echo json_encode($data['attendanceChartData'] ?? new stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/dashboard.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
