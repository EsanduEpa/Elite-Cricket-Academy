<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<!-- Mobile-specific meta tags -->
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">
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
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/coachbooking" class="nav-link">
                            <i class="fas fa-user-tie"></i>
                            <span>Coach Sessions</span>
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
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
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
                        <h1><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo $data['player']['name']; ?>!</h1>
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
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-baseball-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Batting Average</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['batting_avg']; ?>">0</div>
                       
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Strike Rate</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['strike_rate']; ?>">0</div>
                       
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-target"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Total Runs</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['total_runs']; ?>">0</div>
                        
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bowling-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Wickets Taken</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['total_wickets']; ?>">0</div>
                        
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
                            <h2><i class="fas fa-calendar-check"></i> Upcoming Events</h2>
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
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">Sep 07</div>
                                        <div class="table-cell-secondary">Sunday</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Match vs Central Cricket Club</div>
                                        <div class="table-cell-details">
                                        </div>
                                        <span class="table-badge">Match</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">2:00 PM</div>
                                        <div class="table-cell-secondary">6:00 PM</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">Sep 08</div>
                                        <div class="table-cell-secondary">Monday</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Batting Practice Session</div>
                                        <div class="table-cell-details">
                                        </div>
                                        <span class="table-badge">Training</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">10:00 AM</div>
                                        <div class="table-cell-secondary">12:00 PM</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">Sep 09</div>
                                        <div class="table-cell-secondary">Tuesday</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Fitness Assessment</div>
                                        <div class="table-cell-details">
                                        </div>
                                        <span class="table-badge">Assessment</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">9:00 AM</div>
                                        <div class="table-cell-secondary">11:00 AM</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Row 2: Calendar Section -->
            <div class="calendar-section-wrapper">
                <?php 
                $calendarTitle = 'Training & Match Calendar';
                $calendarIcon = 'fas fa-calendar-alt';
                $calendarId = 'playerCalendar';
                $calendarClass = 'player-calendar';
                include APPROOT . '/views/inc/components/calendar.php'; 
                ?>
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
                            <a href="<?php echo URLROOT; ?>/player/bookings" class="quick-btn">Book</a>
                        </div>

                        <div class="quick-action-card">
                            <div class="quick-action-icon view-performance">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="quick-action-info">
                                <h4>Performance</h4>
                                <p>View detailed statistics</p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/player/performance" class="quick-btn">View</a>
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
                            
                            <!-- Sample payment for demo -->
                            <div class="payment-item">
                                <div class="payment-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>Monthly Membership</h4>
                                    <div class="payment-amount">$150.00</div>
                                    <div class="payment-due">Due: Sep 15, 2025</div>
                                </div>
                                <div class="payment-status status-due">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                            
                            <div class="payment-item">
                                <div class="payment-icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>Equipment Rental</h4>
                                    <div class="payment-amount">$35.00</div>
                                    <div class="payment-due">Due: Sep 12, 2025</div>
                                </div>
                                <div class="payment-status status-upcoming">
                                    <i class="fas fa-clock"></i>
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

    <!-- Pass PHP data to JavaScript -->
    <script>
        // Pass PHP data to JavaScript
        window.dashboardData = {
            todaySchedule: <?php echo json_encode($data['todaySchedule'] ?? [
                ['activity' => 'Morning Training Session', 'time' => '10:00 AM', 'coach' => 'Coach Johnson', 'location' => 'Indoor Nets'],
                ['activity' => 'Fitness Training', 'time' => '2:30 PM', 'coach' => 'Fitness Coach', 'location' => 'Gym Facility'],
                ['activity' => 'Recovery Session', 'time' => '5:00 PM', 'coach' => 'Physiotherapist', 'location' => 'Recovery Room']
            ]); ?>,
            upcomingSchedule: <?php echo json_encode($data['upcomingSchedule'] ?? [
                ['activity' => 'Match vs Central Cricket Club', 'date' => '2024-12-15', 'time' => '2:00 PM'],
                ['activity' => 'Batting Practice Session', 'date' => '2024-12-16', 'time' => '10:00 AM'],
                ['activity' => 'Fitness Assessment', 'date' => '2024-12-17', 'time' => '9:00 AM'],
                ['activity' => 'Team Meeting', 'date' => '2024-12-18', 'time' => '11:00 AM'],
                ['activity' => 'Net Practice', 'date' => '2024-12-19', 'time' => '8:30 AM'],
                ['activity' => 'Bowling Workshop', 'date' => '2024-12-20', 'time' => '3:00 PM'],
                ['activity' => 'Match vs Elite Academy', 'date' => '2024-12-22', 'time' => '1:30 PM']
            ]); ?>,
            upcomingBookings: <?php echo json_encode($data['upcomingBookings'] ?? [
                ['type' => 'Court Booking', 'date' => '2024-12-21', 'time' => '4:00 PM'],
                ['type' => 'Equipment Rental', 'date' => '2024-12-23', 'time' => '10:00 AM']
            ]); ?>,
            currentDate: '<?php echo date('Y-m-d'); ?>',
            currentMonth: <?php echo date('n') - 1; ?>, // JavaScript months are 0-indexed
            currentYear: <?php echo date('Y'); ?>
        };
        
        console.log('Dashboard data loaded:', window.dashboardData);
    </script>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>

</html>

