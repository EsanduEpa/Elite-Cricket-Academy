<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">

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
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                  
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Book Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="nav-link">
                            <i class="fas fa-list-alt"></i>
                            <span>My Sessions</span>
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

            <!-- Performance Statistics -->
            <?php
                $performanceStats = $data['performanceStats'] ?? [
                    'batting_avg' => 0,
                    'strike_rate' => 0,
                    'total_runs' => 0,
                    'total_wickets' => 0,
                ];
            ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-baseball-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Batting Average</div>
                        <div class="stat-value" data-target="<?php echo $performanceStats['batting_avg']; ?>">0</div>
                       
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Strike Rate</div>
                        <div class="stat-value" data-target="<?php echo $performanceStats['strike_rate']; ?>">0</div>
                       
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-target"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Total Runs</div>
                        <div class="stat-value" data-target="<?php echo $performanceStats['total_runs']; ?>">0</div>
                        
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bowling-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Wickets Taken</div>
                        <div class="stat-value" data-target="<?php echo $performanceStats['total_wickets']; ?>">0</div>
                        
                    </div>
                </div>
            </div>

            <!-- Batting and Bowling Statistics Tables -->
            <div class="schedule-row">
                <!-- Batting Statistics Table -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-baseball-ball"></i> Recent Batting Statistics</h2>
                            <span class="badge-info">Last 10 Matches</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Opponent</th>
                                    <th>Runs</th>
                                    <th>Balls</th>
                                    <th>Strike Rate</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['battingStats'])): ?>
                                    <?php foreach ($data['battingStats'] as $stat): ?>
                                        <tr>
                                            <td>
                                                <div class="table-cell-primary">
                                                    <?php echo date('M d, Y', strtotime($stat['match_date'])); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <?php echo htmlspecialchars(substr($stat['tournament'], 0, 20)); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title">
                                                    <?php echo htmlspecialchars($stat['opponent']); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(substr($stat['venue'], 0, 25)); ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <strong><?php echo $stat['runs']; ?></strong>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo $stat['balls']; ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo number_format($stat['strike_rate'], 2); ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php 
                                                $badgeClass = 'table-badge ';
                                                if ($stat['result'] == 'win') {
                                                    $badgeClass .= 'status-active';
                                                } elseif ($stat['result'] == 'loss') {
                                                    $badgeClass .= 'status-cancelled';
                                                } else {
                                                    $badgeClass .= 'status-upcoming';
                                                }
                                                ?>
                                                <span class="<?php echo $badgeClass; ?>"><?php echo ucfirst($stat['result']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #888; padding: 20px;">
                                            <i class="fas fa-info-circle"></i> No batting statistics available yet
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bowling Statistics Table -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-bowling-ball"></i> Recent Bowling Statistics</h2>
                            <span class="badge-info">Last 10 Matches</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Opponent</th>
                                    <th>Wickets</th>
                                    <th>Overs</th>
                                    <th>Runs</th>
                                    <th>Economy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['bowlingStats'])): ?>
                                    <?php foreach ($data['bowlingStats'] as $stat): ?>
                                        <tr>
                                            <td>
                                                <div class="table-cell-primary">
                                                    <?php echo date('M d, Y', strtotime($stat['match_date'])); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <?php echo htmlspecialchars(substr($stat['tournament'], 0, 20)); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title">
                                                    <?php echo htmlspecialchars($stat['opponent']); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(substr($stat['venue'], 0, 25)); ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <strong><?php echo $stat['wickets']; ?></strong>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo number_format($stat['overs'], 1); ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo $stat['runs_conceded']; ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php 
                                                    $economy = $stat['economy'];
                                                    $economyColor = '#4A90E2';
                                                    if ($economy <= 6) {
                                                        $economyColor = '#27ae60'; // Good economy
                                                    } elseif ($economy > 9) {
                                                        $economyColor = '#e74c3c'; // Poor economy
                                                    }
                                                    ?>
                                                    <span style="color: <?php echo $economyColor; ?>; font-weight: bold;">
                                                        <?php echo number_format($economy, 2); ?>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #888; padding: 20px;">
                                            <i class="fas fa-info-circle"></i> No bowling statistics available yet
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
                                <tr>
                                    <td>
                                        <div class="table-cell-primary">10:00 AM</div>
                                        <div class="table-cell-secondary">2 hours</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Morning Training Session</div>
                                        <div class="table-cell-details">
                                             Indoor Nets - Coach Johnson
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-active">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary">2:30 PM</div>
                                        <div class="table-cell-secondary">1.5 hours</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Fitness Training</div>
                                        <div class="table-cell-details">
                                             Gym Facility - Cardio & Strength
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-upcoming">Upcoming</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary">5:00 PM</div>
                                        <div class="table-cell-secondary">45 min</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Recovery Session</div>
                                        <div class="table-cell-details">
                                            Recovery Room - Stretching
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge">Planned</span>
                                    </td>
                                </tr>
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
                                <?php if (!empty($data['coachSessions'])): ?>
                                    <?php foreach ($data['coachSessions'] as $session): ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo date('M d', strtotime($session->Date)); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <?php echo date('l', strtotime($session->Date)); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title">
                                                    <?php echo htmlspecialchars($session->Name); ?>
                                                </div>
                                                <div class="table-cell-details">
                                                    Coach: <?php echo htmlspecialchars($session->CoachName); ?> | <?php echo htmlspecialchars($session->Location); ?>
                                                </div>
                                                <span class="table-badge status-upcoming">Coach Session</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary">
                                                    <?php echo date('g:i A', strtotime($session->StartTime)); ?>
                                                </div>
                                                <div class="table-cell-secondary">
                                                    <?php echo date('g:i A', strtotime($session->EndTime)); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align:center; color:#888;">No upcoming coach sessions found.</td>
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

