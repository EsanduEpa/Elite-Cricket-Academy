<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach  = ($data['role'] ?? '') === 'Coach';
$trainerSidebarActive = 'slots';
$cssFile  = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout   = $isCoach ? 'coach-layout'    : 'trainer-layout';
$sidebar  = $isCoach ? 'coach-sidebar'   : 'trainer-sidebar';
$logo     = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
$occ      = $data['occurrence'];
$occurrenceId = (int) ($occ->OccurrenceID ?? 0);

$calendarUrl = URLROOT . '/staffslots/calendar/' . ($occ->OccurrenceDate ?? '');
$detailUrl   = $isCoach
    ? URLROOT . '/coach/occurrence/' . $occurrenceId
    : URLROOT . '/staffslots/occurrence/' . $occurrenceId;
$attendanceUrl = URLROOT . '/staffslots/attendance/' . $occurrenceId;

$bookingStatusMeta = [
    'confirmed' => ['label' => 'Confirmed', 'class' => 'confirmed'],
    'pending' => ['label' => 'Pending', 'class' => 'pending'],
    'attended' => ['label' => 'Completed', 'class' => 'completed'],
    'completed' => ['label' => 'Completed', 'class' => 'completed'],
    'not_attended' => ['label' => 'Not Attended', 'class' => 'not-attended'],
    'missed' => ['label' => 'Not Attended', 'class' => 'not-attended'],
    'cancelled' => ['label' => 'Cancelled', 'class' => 'cancelled'],
];

$manualStatusOptions = [
    'confirmed' => 'Confirmed',
    'completed' => 'Completed',
    'not_attended' => 'Not Attended',
];
?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/<?= $cssFile ?>.css">
<style>
.detail-card  { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px; }
.detail-grid  { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.detail-label { font-size:12px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px; }
.detail-value { font-size:14px;color:#2c3e50;font-weight:500; }
.alert-error   { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.alert-success { background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.booking-status-confirmed { background:#d4edda;color:#155724;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-pending   { background:#fff3cd;color:#856404;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-attended  { background:#cce5ff;color:#004085;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-completed { background:#cce5ff;color:#004085;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-missed    { background:#f8d7da;color:#721c24;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-not-attended { background:#f8d7da;color:#721c24;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-cancelled { background:#e9ecef;color:#6c757d;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600;text-decoration:line-through; }
.booking-status-select {
    min-width: 170px;
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    font-size: 12px;
    color: #2c3e50;
    background: #fff;
}
.booking-status-save {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    background: #1f6feb;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
}
.booking-status-save:hover { background: #1558b0; }
</style>

<div class="<?= $layout ?>">
    <div class="<?= $sidebar ?>" id="staffSidebar">
        <div class="sidebar-header">
            <div class="<?= $isCoach ? 'coach-logo' : 'trainer-info' ?>">
                <?php if ($isCoach): ?>
                    <i class="fas <?= $logo ?>"></i><h3><?= $isCoach ? 'Coach Panel' : 'Trainer Panel' ?></h3>
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
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/sessions"   class="nav-link"><i class="fas fa-calendar-alt"></i><span>Sessions</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players"    class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link"><i class="fas fa-star"></i><span>Recommendations</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health"     class="nav-link"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
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

    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-clipboard-check"></i> Mark Attendance</h1>
                    <p><?= htmlspecialchars((string) ($occ->SessionName ?? 'Session')) ?> &mdash; <?= !empty($occ->OccurrenceDate) ? date('l, j F Y', strtotime($occ->OccurrenceDate)) : '' ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?= htmlspecialchars($calendarUrl) ?>"
                       style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">
                        <i class="fas fa-arrow-left"></i> Back to Calendar
                    </a>
                </div>
            </div>
        </div>

        <div style="padding:0 25px 20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?= htmlspecialchars($detailUrl) ?>"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-info-circle"></i> Session Detail
            </a>
            <a href="<?= htmlspecialchars($attendanceUrl) ?>"
               style="padding:7px 16px;border-radius:6px;background:#2e7d32;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">
                <i class="fas fa-clipboard-check"></i> Attendance
            </a>
        </div>

        <div style="padding:0 25px 40px;max-width:920px;">

            <?php if (!empty($data['error'])): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars((string) $data['error']) ?></div>
            <?php endif; ?>
            <?php if (!empty($data['success'])): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars((string) $data['success']) ?></div>
            <?php endif; ?>

            <div class="detail-card">
                <div class="detail-grid">
                    <div>
                        <div class="detail-label">Facility</div>
                        <div class="detail-value"><?= htmlspecialchars((string) ($occ->FacilityName ?? '—')) ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Time Band</div>
                        <div class="detail-value"><?= htmlspecialchars((string) ($occ->SlotLabel ?? '—')) ?>
                            <?php if (!empty($occ->StartTime) && !empty($occ->EndTime)): ?>
                                <span style="color:#888;font-size:12px;"> (<?= substr((string)$occ->StartTime,0,5) ?>–<?= substr((string)$occ->EndTime,0,5) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="detail-label">Session Type</div>
                        <div class="detail-value"><?= htmlspecialchars(ucfirst((string) ($occ->SlotType ?? 'program'))) ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Bookings</div>
                        <div class="detail-value"><?= (int) ($occ->BookingCount ?? 0) ?></div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3 style="margin:0 0 16px;font-size:15px;color:#2c3e50;"><i class="fas fa-users"></i> Attendees</h3>

                <?php if (empty($data['bookings'])): ?>
                    <p style="color:#888;font-size:13px;margin:0;">No bookings found for this session.</p>
                <?php else: ?>
                    <form method="POST" action="<?= htmlspecialchars($attendanceUrl) ?>" onsubmit="return confirm('Save attendance updates for this session?');">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">#</th>
                                    <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Player</th>
                                    <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Current Status</th>
                                    <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Set Status</th>
                                    <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Booked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['bookings'] as $i => $b): ?>
                                    <?php
                                    $rawBookingStatus = strtolower((string)($b->Status ?? 'confirmed'));
                                    $statusInfo = $bookingStatusMeta[$rawBookingStatus] ?? ['label' => ucwords(str_replace('_', ' ', $rawBookingStatus)), 'class' => $rawBookingStatus];
                                    $selectedManualStatus = $rawBookingStatus;
                                    if ($rawBookingStatus === 'attended') {
                                        $selectedManualStatus = 'completed';
                                    } elseif ($rawBookingStatus === 'missed') {
                                        $selectedManualStatus = 'not_attended';
                                    }
                                    if (!isset($manualStatusOptions[$selectedManualStatus])) {
                                        $selectedManualStatus = 'confirmed';
                                    }
                                    ?>
                                    <tr style="border-bottom:1px solid #f0f0f0;">
                                        <td style="padding:9px 14px;color:#888;font-size:13px;"><?= $i + 1 ?></td>
                                        <td style="padding:9px 14px;font-weight:600;"><?= htmlspecialchars((string) ($b->PlayerName ?? '—')) ?></td>
                                        <td style="padding:9px 14px;">
                                            <span class="booking-status-<?= htmlspecialchars($statusInfo['class']) ?>"><?= htmlspecialchars($statusInfo['label']) ?></span>
                                        </td>
                                        <td style="padding:9px 14px;">
                                            <?php if ($rawBookingStatus === 'cancelled'): ?>
                                                <span style="font-size:12px;color:#6c757d;font-weight:600;">Cancelled booking</span>
                                            <?php else: ?>
                                                <select class="booking-status-select" name="attendance[<?= (int) ($b->BookingID ?? 0) ?>]">
                                                    <?php foreach ($manualStatusOptions as $optionValue => $optionLabel): ?>
                                                        <option value="<?= htmlspecialchars($optionValue) ?>" <?= $selectedManualStatus === $optionValue ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($optionLabel) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:9px 14px;font-size:12px;color:#888;">
                                            <?= !empty($b->CreatedAt) ? date('j M Y, H:i', strtotime($b->CreatedAt)) : '—' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div style="margin-top:16px;display:flex;justify-content:flex-end;">
                            <button type="submit" class="booking-status-save"><i class="fas fa-save"></i> Save Attendance</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
