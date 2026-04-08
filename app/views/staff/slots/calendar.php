<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach  = $data['role'] === 'Coach';
$cssFile  = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout   = $isCoach ? 'coach-layout'    : 'trainer-layout';
$sidebar  = $isCoach ? 'coach-sidebar'   : 'trainer-sidebar';
$logo     = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
$panelName = $isCoach ? 'Coach Panel'    : 'Trainer Panel';
$dashHref  = $isCoach ? '/coach/dashboard' : '/trainer';
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/<?= $cssFile ?>.css">
<style>
.cal-cell      { border:1px solid #e0e0e0; vertical-align:top; min-height:120px; width:14.28%; padding:8px; background:#fff; }
.cal-today     { background:#fffde7; }
.cal-card      { border-radius:6px; padding:6px 8px; margin-bottom:5px; font-size:12px; cursor:pointer; text-decoration:none; display:block; }
.cal-program   { background:#cce5ff; color:#004085; border-left:3px solid #004085; }
.cal-private   { background:#e8f5e9; color:#1b5e20; border-left:3px solid #2e7d32; }
.cal-cancelled { background:#e9ecef; color:#6c757d; border-left:3px solid #aaa; text-decoration:line-through !important; }
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

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <?php if ($isCoach): ?>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard"  class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/sessions"   class="nav-link"><i class="fas fa-calendar-alt"></i><span>Sessions</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players"    class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link"><i class="fas fa-star"></i><span>Recommendations</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health"     class="nav-link"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link"><i class="fas fa-bell"></i><span>Notifications</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/events"     class="nav-link"><i class="fas fa-calendar"></i><span>Events</span></a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer"           class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/bookings"  class="nav-link"><i class="fas fa-calendar-check"></i><span>Schedule &amp; Bookings</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/workout"   class="nav-link"><i class="fas fa-dumbbell"></i><span>Workout Plans</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link"><i class="fas fa-apple-alt"></i><span>Nutrition Plans</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link"><i class="fas fa-capsules"></i><span>Supplements</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link"><i class="fas fa-user-injured"></i><span>Injury Reports</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?= htmlspecialchars($_SESSION['user_name'] ?? $data['role']) ?></div>
            <div class="profile-role"><?= $data['role'] ?></div>
            <a href="<?php echo URLROOT; ?>/<?= strtolower($data['role']) ?>/profile" class="action-btn" style="margin-top:10px;"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:8px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
                        <i class="fas fa-plus"></i> Add Private Session
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
                <i class="fas fa-plus-circle"></i> Add Private Session
            </a>
        </div>

        <div style="padding:0 25px 40px;">

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
            $totalBooked   = 0;
            foreach ($data['byDate'] as $occs) {
                foreach ($occs as $occ) {
                    if ($occ->Status !== 'cancelled') {
                        $totalSessions++;
                        $totalBooked += (int) $occ->BookingCount;
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
                    <div style="font-size:22px;font-weight:700;color:#004085;"><?= $totalBooked ?></div>
                    <div style="font-size:12px;color:#888;margin-top:2px;">Total players booked</div>
                </div>
            </div>

            <!-- Legend -->
            <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;font-size:12px;">
                <span style="background:#cce5ff;color:#004085;padding:3px 10px;border-radius:10px;">Program</span>
                <span style="background:#e8f5e9;color:#1b5e20;padding:3px 10px;border-radius:10px;">Private</span>
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
                                        <div style="margin-top:3px;">
                                            <i class="fas fa-users" style="font-size:10px;"></i>
                                            <?= (int)$occ->BookingCount ?> / <?= (int)$occ->MaxSlots ?> booked
                                        </div>
                                    </a>
                                <?php endforeach; endif; ?>
                            </td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
