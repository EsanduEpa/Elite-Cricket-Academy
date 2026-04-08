<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach  = $data['role'] === 'Coach';
$cssFile  = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout   = $isCoach ? 'coach-layout'    : 'trainer-layout';
$sidebar  = $isCoach ? 'coach-sidebar'   : 'trainer-sidebar';
$logo     = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
$occ      = $data['occurrence'];
$canCancel = in_array($occ->Status, ['scheduled', 'active']);
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
.booking-status-missed    { background:#f8d7da;color:#721c24;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.booking-status-cancelled { background:#e9ecef;color:#6c757d;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600;text-decoration:line-through; }
.detail-card  { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px; }
.detail-grid  { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.detail-label { font-size:12px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px; }
.detail-value { font-size:14px;color:#2c3e50;font-weight:500; }
.alert-error   { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.alert-success { background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.form-group label     { display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px; }
.form-group textarea  { width:100%;padding:10px 13px;border:1px solid #ced4da;border-radius:7px;font-size:14px;color:#333;box-sizing:border-box;height:90px;resize:vertical; }
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
                    <h1><i class="fas fa-calendar-day"></i> Session Detail</h1>
                    <p><?= htmlspecialchars($occ->SessionName) ?> &mdash; <?= date('l, j F Y', strtotime($occ->OccurrenceDate)) ?></p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar/<?= $occ->OccurrenceDate ?>"
                       style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">
                        <i class="fas fa-arrow-left"></i> Back to Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/staffslots/calendar"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-calendar-alt"></i> Calendar
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/private_session"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-plus-circle"></i> Add Private Session
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
                        <div class="detail-label">Bookings</div>
                        <div class="detail-value">
                            <span style="font-size:16px;font-weight:700;color:#2e7d32;"><?= (int)$occ->BookingCount ?></span>
                            <span style="color:#888;font-size:13px;"> / <?= (int)$occ->MaxSlots ?> max</span>
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
            <div class="detail-card">
                <h3 style="margin:0 0 16px;font-size:15px;color:#2c3e50;">
                    <i class="fas fa-users"></i> Attendees
                    <span style="font-size:12px;font-weight:400;color:#888;margin-left:8px;"><?= count($data['bookings']) ?> booked</span>
                </h3>
                <?php if (empty($data['bookings'])): ?>
                    <p style="color:#888;font-size:13px;margin:0;">No players have booked this session yet.</p>
                <?php else: ?>
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">#</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Player</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Email</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Status</th>
                                <th style="padding:9px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Booked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['bookings'] as $i => $b): ?>
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:9px 14px;color:#888;font-size:13px;"><?= $i + 1 ?></td>
                                <td style="padding:9px 14px;font-weight:600;"><?= htmlspecialchars($b->PlayerName) ?></td>
                                <td style="padding:9px 14px;font-size:13px;color:#666;"><?= htmlspecialchars($b->PlayerEmail ?? '—') ?></td>
                                <td style="padding:9px 14px;">
                                    <span class="booking-status-<?= $b->Status ?>"><?= ucfirst($b->Status) ?></span>
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
                    Cancelling will notify all booked players. This action cannot be undone.
                    <?php if ((int)$occ->BookingCount > 0): ?>
                        <strong style="color:#856404;"><?= (int)$occ->BookingCount ?> player(s) currently booked.</strong>
                    <?php endif; ?>
                </p>
                <form method="POST" action="<?php echo URLROOT; ?>/staffslots/occurrence/<?= $occ->OccurrenceID ?>"
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
