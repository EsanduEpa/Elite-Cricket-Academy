<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_slot_bookings.css?v=<?php echo time(); ?>">

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
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/playerslots" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
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
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn profile-logout-spacing">
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
                <div class="header-actions booking-shortcuts">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="booking-shortcut-btn primary">
                        <i class="fas fa-list-alt"></i> My Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="booking-shortcut-btn">
                        <i class="fas fa-user-tie"></i> Coach
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="booking-shortcut-btn">
                        <i class="fas fa-dumbbell"></i> Trainer
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="booking-shortcut-btn">
                        <i class="fas fa-building"></i> Facilities
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="booking-shortcut-btn">
                        <i class="fas fa-calendar-alt"></i> Calendar
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
                        <i class="fas fa-calendar-alt section-title-icon-upcoming"></i> Upcoming Sessions
                    </div>
                </div>
            </div>
            <div class="card-content">
                <?php if (empty($data['upcoming'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-plus empty-state-icon"></i>
                        <p>No upcoming session bookings.</p>
                        <a href="<?php echo URLROOT; ?>/playerslots/coach" class="empty-state-link">
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
                                <small class="booking-time-meta">
                                    <?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash;
                                    <?php echo date('g:i A', strtotime($b->EndTime)); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($b->TemplateName ?? '—'); ?>
                                <?php if (($b->BookingSource ?? '') === 'system'): ?>
                                    <br><small class="assigned-program-label">Assigned Program</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($b->FacilityName ?? '—'); ?></td>
                            <td><span class="status-badge status-<?php echo $b->Status; ?>"><?php echo ucfirst($b->Status); ?></span></td>
                            <td>
                                <?php if ($b->Status === 'confirmed' && $hoursLeft >= 24): ?>
                                <form method="POST" action="<?php echo URLROOT; ?>/playerslots/cancel" class="js-cancel-booking-form">
                                    <input type="hidden" name="booking_id" value="<?php echo (int)$b->BookingID; ?>">
                                    <button type="submit" class="btn-cancel">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                                <?php elseif ($b->Status === 'confirmed'): ?>
                                <span class="booking-window-closed">
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
                        <i class="fas fa-history section-title-icon-past"></i> Past Sessions
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
                                <small class="booking-time-meta">
                                    <?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash;
                                    <?php echo date('g:i A', strtotime($b->EndTime)); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($b->TemplateName ?? '—'); ?>
                                <?php if (($b->BookingSource ?? '') === 'system'): ?>
                                    <br><small class="assigned-program-label">Assigned Program</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($b->FacilityName ?? '—'); ?></td>
                            <td><span class="status-badge status-<?php echo $b->Status; ?>"><?php echo ucfirst($b->Status); ?></span></td>
                            <td class="booking-source-cell"><?php echo ucfirst(str_replace('_', ' ', $b->BookingSource)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<script src="<?php echo URLROOT; ?>/js/player/player_slot_bookings.js?v=<?php echo time(); ?>"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
