<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<?php
$todaySchedule = $data['todaySchedule'] ?? [];
$upcomingBookings = $data['upcomingBookings'] ?? [];
$todayDate = date('Y-m-d');

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

$futureBookings = array_values(array_filter($upcomingBookings, static function ($booking) use ($todayDate) {
    return !empty($booking->date) && $booking->date > $todayDate;
}));
?>

    <!-- Player Dashboard Layout -->
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
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
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                   
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
               
                  
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/playerslots" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Bookings</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Simple Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
                <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
                <a href="<?php echo URLROOT; ?>/player/profile" class="action-btn" style="margin-top: 10px;">
                    <i class="fas fa-user-cog"></i> Profile
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

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

            <!-- Row 2: Calendar Section -->
            <div class="calendar-section-wrapper">
                <div class="calendar-section compact-calendar">
                    <div class="calendar-header">
                        <h3><i class="fas fa-calendar-alt"></i> Training & Match Calendar</h3>
                        <div class="calendar-controls">
                            <div class="view-toggle">
                                <button class="view-btn active" data-view="month"><i class="fas fa-calendar"></i> Month</button>
                                <button class="view-btn" data-view="week"><i class="fas fa-calendar-week"></i> Week</button>
                                <button class="view-btn" data-view="day"><i class="fas fa-calendar-day"></i> Day</button>
                            </div>
                            <div class="calendar-nav">
                                <button id="dashTodayBtn" class="calendar-btn today-btn" title="Go to Today"><i class="fas fa-calendar-check"></i></button>
                                <button id="dashPrevPeriod" class="calendar-btn"><i class="fas fa-chevron-left"></i></button>
                                <span id="dashCurrentPeriod"></span>
                                <button id="dashNextPeriod" class="calendar-btn"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="dashCalendarContent" class="calendar-content">
                        <div id="dashMonthView" class="calendar-grid"></div>
                        <div id="dashWeekView" class="week-view" style="display: none;"></div>
                        <div id="dashDayView" class="day-view" style="display: none;"></div>
                    </div>
                    <div class="calendar-legend">
                        <div class="legend-item"><span class="legend-dot training"></span> Training Sessions</div>
                        <div class="legend-item"><span class="legend-dot match"></span> Matches</div>
                        <div class="legend-item"><span class="legend-dot fitness"></span> Fitness</div>
                    </div>
                </div>
            </div>
            <!-- Row 3: Quick Actions and Payment Due Side by Side -->
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

