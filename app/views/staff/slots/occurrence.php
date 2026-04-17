<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach  = $data['role'] === 'Coach';
$cssFile  = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout   = $isCoach ? 'coach-layout'    : 'trainer-layout';
$sidebar  = $isCoach ? 'coach-sidebar'   : 'trainer-sidebar';
$logo     = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
$detailBaseUrl = $isCoach
    ? URLROOT . '/coach/occurrence/' . (int) ($data['occurrence']->OccurrenceID ?? 0)
    : URLROOT . '/staffslots/occurrence/' . (int) ($data['occurrence']->OccurrenceID ?? 0);
$calendarUrl = $isCoach
    ? URLROOT . '/staffslots/calendar/' . ($data['occurrence']->OccurrenceDate ?? '')
    : URLROOT . '/staffslots/calendar/' . ($data['occurrence']->OccurrenceDate ?? '');
$pastRequestsUrl = URLROOT . '/staffslots/past_requests';
$privateSessionUrl = URLROOT . '/staffslots/private_session';
$occ      = $data['occurrence'];
$canCancel = in_array($occ->Status, ['scheduled', 'active']);
$isProgramSession = ($occ->SlotType ?? '') === 'program';
$affectedPlayerCount = $isProgramSession ? (int) ($occ->EligiblePlayerCount ?? 0) : count($data['bookings']);
$playerCountLabel = $isProgramSession ? 'Eligible Players' : 'Bookings';
$playerListHeading = $isProgramSession ? 'Eligible Players' : 'Attendees';
$playerListSummary = $isProgramSession ? $affectedPlayerCount . ' eligible' : $affectedPlayerCount . ' booked';
$emptyPlayerText = $isProgramSession
    ? 'No eligible players are currently assigned to this program.'
    : 'No players have booked this session yet.';
$cancellationAudience = $isProgramSession ? 'assigned players' : 'booked players';
$occurrenceEndTimestamp = (!empty($occ->OccurrenceDate) && !empty($occ->EndTime))
    ? strtotime($occ->OccurrenceDate . ' ' . $occ->EndTime)
    : false;
$canCoachUpdatePastOccurrenceStatus = $isCoach && $occurrenceEndTimestamp !== false && $occurrenceEndTimestamp <= time();
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
.occ-status-scheduled { background:#cce5ff;color:#004085;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.occ-status-active    { background:#d4edda;color:#155724;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.occ-status-cancelled { background:#f8d7da;color:#721c24;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.occ-status-completed { background:#e2d9f3;color:#4a1e8c;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.booking-status-confirmed { background:#d4edda;color:#155724;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-pending   { background:#fff3cd;color:#856404;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-attended  { background:#cce5ff;color:#004085;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-completed { background:#cce5ff;color:#004085;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-missed    { background:#f8d7da;color:#721c24;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-not-attended { background:#f8d7da;color:#721c24;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-cancelled { background:#e9ecef;color:#6c757d;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600;text-decoration:line-through; }
.detail-card  { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px; }
.detail-grid  { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.detail-label { font-size:12px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px; }
.detail-value { font-size:14px;color:#2c3e50;font-weight:500; }
.alert-error   { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.alert-success { background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.form-group label     { display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px; }
.form-group textarea  { width:100%;padding:10px 13px;border:1px solid #ced4da;border-radius:7px;font-size:14px;color:#333;box-sizing:border-box;height:90px;resize:vertical; }
.booking-process { display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:14px 0 0; }
.booking-process-step { display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;font-size:12px;font-weight:700; }
.booking-process-step--confirmed { background:#d4edda;color:#155724; }
.booking-process-step--completed { background:#cce5ff;color:#004085; }
.booking-process-step--not-attended { background:#f8d7da;color:#721c24; }
.booking-process-step--cancelled { background:#e9ecef;color:#6c757d; }
.booking-process-arrow { color:#97a6b5;font-size:12px;font-weight:700; }
.booking-status-form { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
.booking-status-select {
    min-width:150px;padding:7px 10px;border:1px solid #ced4da;border-radius:8px;
    font-size:12px;color:#2c3e50;background:#fff;
}
.booking-status-save {
    padding:7px 12px;border:none;border-radius:8px;background:#1f6feb;color:#fff;
    font-size:12px;font-weight:700;cursor:pointer;
}
.booking-status-save:hover { background:#1558b0; }
.booking-status-locked { font-size:12px;color:#6c757d;font-weight:600; }
</style>

<div class="<?= $layout ?>">
    <!-- ── Sidebar ── -->
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
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link"><i class="fas fa-user-injured"></i><span>Injury Reports</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>

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
                    <h1><i class="fas fa-calendar-day"></i> Session Detail</h1>
                    <p><?= htmlspecialchars($occ->SessionName) ?> &mdash; <?= date('l, j F Y', strtotime($occ->OccurrenceDate)) ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?= htmlspecialchars($calendarUrl) ?>"
                       style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">
                        <i class="fas fa-arrow-left"></i> Back to Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?= htmlspecialchars($calendarUrl) ?>"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-calendar-alt"></i> Calendar
            </a>
            <a href="<?= htmlspecialchars($pastRequestsUrl) ?>"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-history"></i> Past Requests
            </a>
            <a href="<?= htmlspecialchars($privateSessionUrl) ?>"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-paper-plane"></i> Request Private Session
            </a>
        </div>

        <div style="padding:0 25px 40px;max-width:860px;">

            <?php if ($data['error']): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>
            <?php if ($data['success']): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['success']) ?></div>
            <?php endif; ?>

            <!-- ── Session Details ── -->
            <div class="detail-card">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
                    <h2 style="margin:0;font-size:18px;color:#2c3e50;">
                        <?= htmlspecialchars($occ->SessionName) ?>
                    </h2>
                    <?php
                    $sc = 'occ-status-' . $occ->Status;
                    echo "<span class=\"{$sc}\">" . ucfirst($occ->Status) . "</span>";
                    ?>
                    <?php if ($occ->SessionName === 'Private Session'): ?>
                        <span style="background:#e8f5e9;color:#1b5e20;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700;">
                            <i class="fas fa-user-lock"></i> Private
                        </span>
                    <?php endif; ?>
                </div>

                <div class="detail-grid">
                    <div>
                        <div class="detail-label">Date</div>
                        <div class="detail-value"><?= date('l, j F Y', strtotime($occ->OccurrenceDate)) ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Time Band</div>
                        <div class="detail-value"><?= htmlspecialchars($occ->SlotLabel ?? '—') ?>
                            <?php if ($occ->StartTime && $occ->EndTime): ?>
                                <span style="color:#888;font-size:12px;"> (<?= substr($occ->StartTime,0,5) ?>–<?= substr($occ->EndTime,0,5) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="detail-label">Facility</div>
                        <div class="detail-value"><?= htmlspecialchars($occ->FacilityName ?? '—') ?></div>
                    </div>
                    <div>
                        <div class="detail-label"><?= htmlspecialchars($playerCountLabel) ?></div>
                        <div class="detail-value">
                            <span style="font-size:16px;font-weight:700;color:#2e7d32;">
                                <?= $isProgramSession ? (int)$occ->EligiblePlayerCount : (int)$occ->BookingCount ?>
                            </span>
                            <?php if (!$isProgramSession && $occ->MaxSlots !== null): ?>
                                <span style="color:#888;font-size:13px;"> / <?= (int)$occ->MaxSlots ?> max</span>
                            <?php elseif ($isProgramSession && !empty($occ->AgeGroup)): ?>
                                <span style="color:#888;font-size:13px;"> for <?= htmlspecialchars($occ->AgeGroup) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($occ->Status === 'cancelled' && $occ->CancelReason): ?>
                    <div style="grid-column:1/-1;">
                        <div class="detail-label">Cancellation Reason</div>
                        <div class="detail-value" style="color:#721c24;"><?= htmlspecialchars($occ->CancelReason) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($occ->Notes): ?>
                    <div style="grid-column:1/-1;">
                        <div class="detail-label">Notes</div>
                        <div class="detail-value"><?= nl2br(htmlspecialchars($occ->Notes)) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Attendees ── -->
            <?php if ($canCoachUpdatePastOccurrenceStatus): ?>
            <div class="detail-card" style="border-top:3px solid #1f6feb;">
                <h3 style="margin:0 0 10px;font-size:15px;color:#1f4f9c;"><i class="fas fa-flag-checkered"></i> Update Occurrence Status</h3>
                <p style="font-size:13px;color:#666;margin:0 0 16px;">
                    This changes the overall status of this past session occurrence.
                </p>
                <form method="POST" action="<?= htmlspecialchars($detailBaseUrl) ?>">
                    <input type="hidden" name="action_update_occurrence_status" value="1">
                    <div style="display:grid;grid-template-columns:minmax(220px,280px) 1fr;gap:16px;align-items:end;">
                        <div>
                            <label for="occurrence_status" style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Occurrence Status</label>
                            <select name="occurrence_status" id="occurrence_status" style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:8px;font-size:13px;">
                                <option value="completed" <?= strtolower((string) ($occ->Status ?? '')) === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= strtolower((string) ($occ->Status ?? '')) === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label for="occurrence_status_reason" style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Reason if cancelled</label>
                            <input type="text"
                                   name="occurrence_status_reason"
                                   id="occurrence_status_reason"
                                   value="<?= htmlspecialchars((string) ($occ->CancelReason ?? '')) ?>"
                                   placeholder="Required only when status is cancelled"
                                   style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:8px;font-size:13px;box-sizing:border-box;">
                        </div>
                    </div>
                    <div style="margin-top:14px;">
                        <button type="submit" class="booking-status-save" style="padding:10px 18px;">
                            <i class="fas fa-save"></i> Save Occurrence Status
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <div class="detail-card">
                <h3 style="margin:0 0 16px;font-size:15px;color:#2c3e50;">
                    <i class="fas fa-users"></i> <?= htmlspecialchars($playerListHeading) ?>
                    <span style="font-size:12px;font-weight:400;color:#888;margin-left:8px;"><?= htmlspecialchars($playerListSummary) ?></span>
                </h3>
                <div style="margin:0 0 18px;font-size:13px;color:#5f6f7f;line-height:1.6;">
                    Booking status process:
                    <div class="booking-process">
                        <span class="booking-process-step booking-process-step--confirmed"><i class="fas fa-calendar-check"></i> Confirmed</span>
                        <span class="booking-process-arrow"><i class="fas fa-arrow-right"></i></span>
                        <span class="booking-process-step booking-process-step--completed"><i class="fas fa-check-circle"></i> Completed</span>
                        <span class="booking-process-arrow">or</span>
                        <span class="booking-process-step booking-process-step--not-attended"><i class="fas fa-user-times"></i> Not Attended</span>
                        <span class="booking-process-arrow">or</span>
                        <span class="booking-process-step booking-process-step--cancelled"><i class="fas fa-ban"></i> Cancelled</span>
                    </div>
                </div>
                <?php if (empty($data['bookings'])): ?>
                    <p style="color:#888;font-size:13px;margin:0;"><?= htmlspecialchars($emptyPlayerText) ?></p>
                <?php else: ?>
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">#</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Player</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Email</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Status</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Update Status</th>
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
                            ?>
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:9px 14px;color:#888;font-size:13px;"><?= $i + 1 ?></td>
                                <td style="padding:9px 14px;font-weight:600;"><?= htmlspecialchars($b->PlayerName) ?></td>
                                <td style="padding:9px 14px;font-size:13px;color:#666;"><?= htmlspecialchars($b->PlayerEmail ?? '—') ?></td>
                                <td style="padding:9px 14px;">
                                    <span class="booking-status-<?= htmlspecialchars($statusInfo['class']) ?>"><?= htmlspecialchars($statusInfo['label']) ?></span>
                                </td>
                                <td style="padding:9px 14px;">
                                    <?php if ($rawBookingStatus === 'cancelled'): ?>
                                        <span class="booking-status-locked">Cancelled booking</span>
                                    <?php else: ?>
                                        <form method="POST" action="<?= htmlspecialchars($detailBaseUrl) ?>" class="booking-status-form">
                                            <input type="hidden" name="action_update_booking" value="1">
                                            <input type="hidden" name="booking_id" value="<?= (int) $b->BookingID ?>">
                                            <select name="booking_status" class="booking-status-select">
                                                <?php foreach ($manualStatusOptions as $optionValue => $optionLabel): ?>
                                                    <option value="<?= htmlspecialchars($optionValue) ?>" <?= $selectedManualStatus === $optionValue ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($optionLabel) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="booking-status-save">Save</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:9px 14px;font-size:12px;color:#888;">
                                    <?= date('j M Y, H:i', strtotime($b->CreatedAt)) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- ── Cancel Session ── -->
            <?php if ($canCancel): ?>
            <div class="detail-card" style="border-top:3px solid #dc3545;">
                <h3 style="margin:0 0 12px;font-size:15px;color:#dc3545;"><i class="fas fa-ban"></i> Cancel This Session</h3>
                <p style="font-size:13px;color:#666;margin-bottom:16px;">
                    Cancelling will notify all <?= htmlspecialchars($cancellationAudience) ?>. This action cannot be undone.
                    <?php if ($affectedPlayerCount > 0): ?>
                        <strong style="color:#856404;"><?= $affectedPlayerCount ?> player(s) currently affected.</strong>
                    <?php endif; ?>
                </p>
                <form method="POST" action="<?= htmlspecialchars($detailBaseUrl) ?>"
                      onsubmit="return confirm('Are you sure you want to cancel this session?');">
                    <input type="hidden" name="action_cancel" value="1">
                    <div class="form-group">
                        <label for="cancel_reason">Reason for cancellation <span style="color:#dc3545;">*</span></label>
                        <textarea name="cancel_reason" id="cancel_reason" placeholder="e.g. Facility unavailable, coach sick leave…" required></textarea>
                    </div>
                    <button type="submit"
                            style="padding:10px 24px;background:#dc3545;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                        <i class="fas fa-ban"></i> Confirm Cancellation
                    </button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
