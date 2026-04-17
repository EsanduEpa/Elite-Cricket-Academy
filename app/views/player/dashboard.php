<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<?php
$todaySchedule = $data['todaySchedule'] ?? [];
$upcomingBookings = $data['upcomingBookings'] ?? [];
$todayDate = date('Y-m-d');
$upcomingCutoffDate = date('Y-m-d', strtotime('+7 days'));

$dashboardFormatDuration = static function ($startTime, $endTime) {
    if (empty($startTime) || empty($endTime)) {
        return 'Time not set';
    }

    $startTimestamp = strtotime($startTime);
    $endTimestamp = strtotime($endTime);

    if ($startTimestamp === false || $endTimestamp === false || $endTimestamp <= $startTimestamp) {
        return date('g:i A', $startTimestamp ?: time()) . ' - ' . date('g:i A', $endTimestamp ?: time());
    }

    $minutes = (int) round(($endTimestamp - $startTimestamp) / 60);
    if ($minutes < 60) {
        return $minutes . ' min';
    }

    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    if ($remainingMinutes === 0) {
        return $hours . ' hour' . ($hours === 1 ? '' : 's');
    }

    return $hours . 'h ' . $remainingMinutes . 'm';
};

$dashboardBadgeClass = static function ($status, $date = null, $startTime = null, $endTime = null) use ($todayDate) {
    $normalizedStatus = strtolower(trim((string) $status));

    if (in_array($normalizedStatus, ['confirmed', 'booked', 'scheduled'], true)) {
        $normalizedStatus = 'upcoming';
    }

    if ($date === $todayDate && !empty($startTime) && !empty($endTime)) {
        $now = time();
        $startTimestamp = strtotime($date . ' ' . $startTime);
        $endTimestamp = strtotime($date . ' ' . $endTime);

        if ($startTimestamp !== false && $endTimestamp !== false) {
            if ($now >= $startTimestamp && $now <= $endTimestamp) {
                $normalizedStatus = 'active';
            } elseif ($now < $startTimestamp && $normalizedStatus === '') {
                $normalizedStatus = 'upcoming';
            } elseif ($now > $endTimestamp) {
                $normalizedStatus = 'completed';
            }
        }
    }

    return match ($normalizedStatus) {
        'active' => 'status-active',
        'upcoming', 'pending', 'confirmed', 'booked', 'scheduled' => 'status-upcoming',
        default => '',
    };
};

$dashboardStatusLabel = static function ($status, $date = null, $startTime = null, $endTime = null) use ($todayDate) {
    $normalizedStatus = strtolower(trim((string) $status));

    if ($date === $todayDate && !empty($startTime) && !empty($endTime)) {
        $now = time();
        $startTimestamp = strtotime($date . ' ' . $startTime);
        $endTimestamp = strtotime($date . ' ' . $endTime);

        if ($startTimestamp !== false && $endTimestamp !== false) {
            if ($now >= $startTimestamp && $now <= $endTimestamp) {
                return 'Active';
            }
            if ($now > $endTimestamp) {
                return 'Completed';
            }
        }
    }

    if (in_array($normalizedStatus, ['confirmed', 'booked', 'scheduled'], true)) {
        return 'Upcoming';
    }

    if ($normalizedStatus === '') {
        return 'Planned';
    }

    return ucwords(str_replace('_', ' ', $normalizedStatus));
};

$futureBookings = array_values(array_filter($upcomingBookings, static function ($booking) use ($todayDate, $upcomingCutoffDate) {
    return !empty($booking->date) && $booking->date > $todayDate && $booking->date <= $upcomingCutoffDate;
}));
?>

    <!-- Player Dashboard Layout -->
    <div class="player-layout">
        <?php $playerActivePage = 'dashboard'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?>!</h1>
                        <p>Your cricket journey dashboard - Track progress, manage bookings, and achieve your goals</p>
                    </div>
                    <div class="header-actions">

                        <button class="btn btn-refresh" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                            <div class="current-time" id="currentTime"></div>
                        </button>
                    </div>
                </div>
            </div>

            

            <!-- Row 1: Today's Schedule and Upcoming Events Side by Side -->
            <div class="schedule-row">
                <div class="schedule-card today-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-day"></i> Today's Schedule</h2>
                            <span class="date-display"><?php echo date('M j, Y'); ?></span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($todaySchedule)): ?>
                                    <?php foreach ($todaySchedule as $scheduleItem): ?>
                                        <?php
                                        $badgeClass = $dashboardBadgeClass(
                                            $scheduleItem->Status ?? '',
                                            $scheduleItem->Date ?? $todayDate,
                                            $scheduleItem->StartTime ?? null,
                                            $scheduleItem->EndTime ?? null
                                        );
                                        $statusLabel = $dashboardStatusLabel(
                                            $scheduleItem->Status ?? '',
                                            $scheduleItem->Date ?? $todayDate,
                                            $scheduleItem->StartTime ?? null,
                                            $scheduleItem->EndTime ?? null
                                        );
                                        $details = trim((string)($scheduleItem->location ?? ''));
                                        if (!empty($scheduleItem->coach)) {
                                            $details .= ($details !== '' ? ' - ' : '') . $scheduleItem->coach;
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="table-cell-primary"><?php echo !empty($scheduleItem->StartTime) ? date('g:i A', strtotime($scheduleItem->StartTime)) : 'TBD'; ?></div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title"><?php echo htmlspecialchars($scheduleItem->activity ?? 'Scheduled session'); ?></div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="table-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align:center; color:#888;">No sessions scheduled for today.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-check"></i> Upcoming Schedule</h2>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Event</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($futureBookings)): ?>
                                    <?php foreach ($futureBookings as $booking): ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo date('M d', strtotime($booking->date)); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <?php echo date('l', strtotime($booking->date)); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title">
                                                    <?php echo htmlspecialchars($booking->reason ?: ucfirst($booking->booking_type ?? 'session')); ?>
                                                </div>
                                                <div class="table-cell-details">
                                                    <?php
                                                    $bookingDetails = [];
                                                    if (!empty($booking->practitioner_name)) {
                                                        $bookingDetails[] = $booking->practitioner_name;
                                                    }
                                                    if (!empty($booking->location)) {
                                                        $bookingDetails[] = $booking->location;
                                                    }
                                                    echo htmlspecialchars(!empty($bookingDetails) ? implode(' | ', $bookingDetails) : 'Academy');
                                                    ?>
                                                </div>
                                                <span class="table-badge status-upcoming"><?php echo htmlspecialchars(ucfirst($booking->booking_type ?? 'session')); ?></span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo !empty($booking->StartTime) ? date('g:i A', strtotime($booking->StartTime)) : 'TBD'; ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <?php echo !empty($booking->EndTime) ? date('g:i A', strtotime($booking->EndTime)) : ''; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align:center; color:#888;">No upcoming schedule items found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Row 2: Quick Actions and Payment Due Side by Side -->
            <div class="action-row">
                <div class="quick-actions-container">
                    <div class="section-header">
                        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    </div>
                    <div class="quick-actions-content">
                        <div class="quick-action-card">
                            <div class="quick-action-icon book-training">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="quick-action-info">
                                <h4>Book Training</h4>
                                <p>Schedule a training session</p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/playerslots/available" class="quick-btn">Book</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon view-performance">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="quick-action-info">
                                <h4>Performance</h4>
                                <p>View detailed statistics</p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/performance" class="quick-btn">View</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon medical-records">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div class="quick-action-info">
                                <h4>Medical</h4>
                                <p>Health records & checkup</p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/player/medical" class="quick-btn">Update</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon equipment-shop">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="quick-action-info">
                                <h4>Equipment</h4>
                                <p>Rent cricket gear</p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/player/shopping" class="quick-btn">Shop</a>
                        </div>
                    </div>
                </div>

                <div class="action-card payment-due">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-credit-card"></i> Payment Due</h2>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php if(!empty($data['paymentsDue'])): ?>
                            <?php foreach($data['paymentsDue'] as $payment): ?>
                                <div class="payment-item">
                                    <div class="payment-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h4><?php echo $payment['type']; ?></h4>
                                        <div class="payment-amount"><?php echo $payment['amount']; ?></div>
                                        <div class="payment-due">Due: <?php echo date('M j, Y', strtotime($payment['due_date'])); ?></div>
                                        <?php if (!empty($payment['message'])): ?>
                                            <div class="payment-copy"><?php echo htmlspecialchars($payment['message']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="payment-status status-due">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-payments">
                                <div class="no-payments-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="no-payments-content">
                                    <h4>All Caught Up!</h4>
                                    <p>No payments due at this time</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="payment-actions">
                            <a href="<?php echo URLROOT; ?>/player/payments" class="pay-btn">
                                <i class="fas fa-credit-card"></i> Make Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- Pass PHP data to JavaScript (data-only) -->
    <script>window.URLROOT_FACILITY = '<?php echo URLROOT; ?>';</script>
    <script type="application/json" id="dashboardData"><?php echo json_encode([
        'todaySchedule' => $data['todaySchedule'] ?? [],
        'upcomingSchedule' => $data['upcomingSchedule'] ?? [],
        'upcomingBookings' => $data['upcomingBookings'] ?? [],
        'calendarEvents' => $data['calendarEvents'] ?? [],
        'currentDate' => date('Y-m-d'),
        'currentMonth' => (int)date('n') - 1,
        'currentYear' => (int)date('Y'),
        'urlRoot' => URLROOT,
    ], JSON_UNESCAPED_SLASHES); ?></script>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>

</html>

