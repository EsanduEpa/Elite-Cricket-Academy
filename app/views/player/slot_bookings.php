<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<style>
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-confirmed  { background:#d1fae5;color:#065f46; }
    .status-cancelled  { background:#fee2e2;color:#991b1b; }
    .status-attended   { background:#dbeafe;color:#1e40af; }
    .status-missed     { background:#fef3c7;color:#92400e; }
    .status-pending    { background:#f3f4f6;color:#374151; }
    .booking-table { width:100%; border-collapse:collapse; font-size:14px; }
    .booking-table th { text-align:left; padding:10px 14px; background:#f8fafc; color:#475569;
                        font-size:12px; text-transform:uppercase; letter-spacing:.5px;
                        border-bottom:1px solid #e2e8f0; }
    .booking-table td { padding:12px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .booking-table tr:last-child td { border-bottom:none; }
    .booking-table tr:hover td { background:#f8fafc; }
    .btn-cancel {
        padding:6px 14px;
        background:#ef4444;
        color:#fff;
        border:none;
        border-radius:6px;
        font-size:12px;
        font-weight:600;
        cursor:pointer;
        transition:opacity .15s;
    }
    .btn-cancel:hover { opacity:.85; }
    .section-title {
        font-size:18px;
        font-weight:700;
        color:#1e293b;
        margin-bottom:16px;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .schedule-card { margin-bottom:28px; }
    .empty-state { padding:40px; text-align:center; color:#94a3b8; }
    .flash-msg {
        padding:12px 18px;
        border-radius:8px;
        margin-bottom:18px;
        font-size:14px;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .flash-success { background:#dcfce7;color:#166534;border:1px solid #bbf7d0; }
    .flash-error   { background:#fee2e2;color:#991b1b;border:1px solid #fecaca; }
</style>

<div class="player-layout">
    <!-- Sidebar -->
    <div class="player-sidebar" id="playerSidebar">
        <div class="sidebar-header">
            <div class="player-logo">
                <i class="fas fa-user-graduate"></i>
                <h3>Player Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player"                  class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training"         class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/performance"             class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/available"   class="nav-link"><i class="fas fa-ticket-alt"></i><span>Book Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/facilities"   class="nav-link"><i class="fas fa-building"></i><span>Book Facility</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/playerslots/bookings" class="nav-link"><i class="fas fa-list-alt"></i><span>My Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments"      class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical"          class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments"         class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping"         class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($data['player']['name'] ?? 'Player'); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($data['player']['membership_level'] ?? 'Standard'); ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-list-alt"></i> My Session Bookings</h1>
                    <p>View your upcoming and past slot session bookings.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/playerslots/available" class="btn btn-training">
                        <i class="fas fa-plus"></i> Book a Session
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['slot_success'])): ?>
            <div class="flash-msg flash-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['slot_success']); unset($_SESSION['slot_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['slot_error'])): ?>
            <div class="flash-msg flash-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['slot_error']); unset($_SESSION['slot_error']); ?>
            </div>
        <?php endif; ?>

        <!-- ── Upcoming Bookings ── -->
        <div class="schedule-card">
            <div class="card-header">
                <div class="header-content">
                    <div class="section-title">
                        <i class="fas fa-calendar-alt" style="color:#2563eb;"></i> Upcoming Sessions
                    </div>
                </div>
            </div>
            <div class="card-content">
                <?php if (empty($data['upcoming'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-plus" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                        <p>No upcoming session bookings.</p>
                        <a href="<?php echo URLROOT; ?>/playerslots/available" style="color:#2563eb;font-size:14px;margin-top:10px;display:inline-block;">
                            Browse available sessions &rarr;
                        </a>
                    </div>
                <?php else: ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-clock"></i> Time Slot</th>
                            <th><i class="fas fa-clipboard-list"></i> Session</th>
                            <th><i class="fas fa-map-marker-alt"></i> Facility</th>
                            <th><i class="fas fa-circle"></i> Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['upcoming'] as $b): ?>
                        <?php $hoursLeft = (strtotime($b->OccurrenceDate) - time()) / 3600; ?>
                        <tr>
                            <td><strong><?php echo date('D, d M Y', strtotime($b->OccurrenceDate)); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($b->SlotLabel); ?><br>
                                <small style="color:#94a3b8;">
                                    <?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash;
                                    <?php echo date('g:i A', strtotime($b->EndTime)); ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($b->TemplateName ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($b->FacilityName ?? '—'); ?></td>
                            <td><span class="status-badge status-<?php echo $b->Status; ?>"><?php echo ucfirst($b->Status); ?></span></td>
                            <td>
                                <?php if ($b->Status === 'confirmed' && $hoursLeft >= 24): ?>
                                <form method="POST" action="<?php echo URLROOT; ?>/playerslots/cancel"
                                      onsubmit="return confirm('Cancel this session booking?');">
                                    <input type="hidden" name="booking_id" value="<?php echo (int)$b->BookingID; ?>">
                                    <button type="submit" class="btn-cancel">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                                <?php elseif ($b->Status === 'confirmed'): ?>
                                <span style="font-size:12px;color:#94a3b8;">
                                    <i class="fas fa-lock"></i> Window closed
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Past Bookings ── -->
        <div class="schedule-card">
            <div class="card-header">
                <div class="header-content">
                    <div class="section-title">
                        <i class="fas fa-history" style="color:#64748b;"></i> Past Sessions
                    </div>
                </div>
            </div>
            <div class="card-content">
                <?php if (empty($data['past'])): ?>
                    <div class="empty-state">
                        <p>No past session history.</p>
                    </div>
                <?php else: ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-clock"></i> Time Slot</th>
                            <th><i class="fas fa-clipboard-list"></i> Session</th>
                            <th><i class="fas fa-map-marker-alt"></i> Facility</th>
                            <th><i class="fas fa-circle"></i> Status</th>
                            <th><i class="fas fa-tag"></i> Booked Via</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['past'] as $b): ?>
                        <tr>
                            <td><?php echo date('D, d M Y', strtotime($b->OccurrenceDate)); ?></td>
                            <td>
                                <?php echo htmlspecialchars($b->SlotLabel); ?><br>
                                <small style="color:#94a3b8;">
                                    <?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash;
                                    <?php echo date('g:i A', strtotime($b->EndTime)); ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($b->TemplateName ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($b->FacilityName ?? '—'); ?></td>
                            <td><span class="status-badge status-<?php echo $b->Status; ?>"><?php echo ucfirst($b->Status); ?></span></td>
                            <td style="font-size:12px;color:#64748b;"><?php echo ucfirst(str_replace('_', ' ', $b->BookingSource)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<?php require_once APPROOT . '/views/inc/footer.php'; ?>
