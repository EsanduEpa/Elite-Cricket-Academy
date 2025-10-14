<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Mobile-specific meta tags -->
    <meta name="theme-color" content="#2c3e50">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
</head>

<body>
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>
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
                    <!-- PRIMARY FEATURES - High Priority -->
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training Schedule</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                            <?php if(!empty($data['upcomingBookings'])): ?>
                                <span class="badge"><?php echo count($data['upcomingBookings']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                            <?php if(!empty($data['paymentsDue'])): ?>
                                <span class="badge"><?php echo count($data['paymentsDue']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping & Rental</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Player Profile -->
            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name']; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level']; ?> Member</div>
                </div>
                <div class="logout-btn">
                    <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo $data['player']['name']; ?>!</h1>
                    <p>Your performance overview and upcoming activities</p>
                </div>
                <div class="header-actions">
                    <button class="refresh-btn" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <div class="current-time" id="currentTime"></div>
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
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +2.3 this month
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Strike Rate</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['strike_rate']; ?>">0</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +5.8% this month
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-target"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Total Runs</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['total_runs']; ?>">0</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +125 this month
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bowling-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Wickets Taken</div>
                        <div class="stat-value" data-target="<?php echo $data['performanceStats']['total_wickets']; ?>">0</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +8 this month
                        </div>
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
                        <div class="header-accent"></div>
                    </div>
                    <div class="card-content">
                        <div class="schedule-item current">
                            <div class="schedule-time">
                                <div class="time-display">10:00 AM</div>
                                <div class="duration">2 hours</div>
                            </div>
                            <div class="schedule-info">
                                <h4>Morning Training Session</h4>
                                <div class="schedule-meta">
                                    <span><i class="fas fa-map-marker-alt"></i> Indoor Nets</span>
                                    <span><i class="fas fa-user-tie"></i> Coach Johnson</span>
                                </div>
                            </div>
                            <div class="schedule-status status-active">
                                <i class="fas fa-play"></i> Active
                            </div>
                        </div>

                        <div class="schedule-item upcoming">
                            <div class="schedule-time">
                                <div class="time-display">2:30 PM</div>
                                <div class="duration">1.5 hours</div>
                            </div>
                            <div class="schedule-info">
                                <h4>Fitness Training</h4>
                                <div class="schedule-meta">
                                    <span><i class="fas fa-dumbbell"></i> Gym Facility</span>
                                    <span><i class="fas fa-fire"></i> Cardio & Strength</span>
                                </div>
                            </div>
                            <div class="schedule-status status-upcoming">
                                <i class="fas fa-clock"></i> Upcoming
                            </div>
                        </div>

                        <div class="schedule-item planned">
                            <div class="schedule-time">
                                <div class="time-display">5:00 PM</div>
                                <div class="duration">45 min</div>
                            </div>
                            <div class="schedule-info">
                                <h4>Recovery Session</h4>
                                <div class="schedule-meta">
                                    <span><i class="fas fa-spa"></i> Recovery Room</span>
                                    <span><i class="fas fa-leaf"></i> Stretching</span>
                                </div>
                            </div>
                            <div class="schedule-status status-planned">
                                <i class="fas fa-calendar"></i> Planned
                            </div>
                        </div>
                    </div>
                </div>

                <div class="schedule-card upcoming-events">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-check"></i> Upcoming Events</h2>
                            <span class="event-count">5 events this week</span>
                        </div>
                        <div class="header-accent"></div>
                    </div>
                    <div class="card-content">
                        <div class="event-item match-event">
                            <div class="event-date">
                                <div class="day">07</div>
                                <div class="month">SEP</div>
                            </div>
                            <div class="event-info">
                                <h4>Match vs Central Cricket Club</h4>
                                <div class="event-meta">
                                    <span><i class="fas fa-clock"></i> 2:00 PM - 6:00 PM</span>
                                    <span><i class="fas fa-map-marker-alt"></i> Main Ground</span>
                                </div>
                            </div>
                            <div class="event-type type-match">
                                <i class="fas fa-trophy"></i> Match
                            </div>
                        </div>

                        <div class="event-item training-event">
                            <div class="event-date">
                                <div class="day">08</div>
                                <div class="month">SEP</div>
                            </div>
                            <div class="event-info">
                                <h4>Batting Practice Session</h4>
                                <div class="event-meta">
                                    <span><i class="fas fa-clock"></i> 10:00 AM - 12:00 PM</span>
                                    <span><i class="fas fa-map-marker-alt"></i> Indoor Nets</span>
                                </div>
                            </div>
                            <div class="event-type type-training">
                                <i class="fas fa-dumbbell"></i> Training
                            </div>
                        </div>

                        <div class="event-item assessment-event">
                            <div class="event-date">
                                <div class="day">09</div>
                                <div class="month">SEP</div>
                            </div>
                            <div class="event-info">
                                <h4>Fitness Assessment</h4>
                                <div class="event-meta">
                                    <span><i class="fas fa-clock"></i> 9:00 AM - 11:00 AM</span>
                                    <span><i class="fas fa-map-marker-alt"></i> Gym Facility</span>
                                </div>
                            </div>
                            <div class="event-type type-assessment">
                                <i class="fas fa-chart-line"></i> Assessment
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Calendar Section -->
            <div class="calendar-section-wrapper">
                <div class="calendar-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-alt"></i> Training & Match Calendar</h2>
                        </div>
                        <div class="calendar-controls">
                            <div class="calendar-nav">
                                <button class="nav-btn" onclick="previousMonth()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <div class="current-month" id="currentMonth">December 2024</div>
                                <button class="nav-btn" onclick="nextMonth()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="view-toggle">
                                <button class="view-btn active" onclick="setView('month')">Month</button>
                                <button class="view-btn" onclick="setView('week')">Week</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="calendar-content">
                        <div class="calendar-grid">
                            <div class="calendar-weekdays">
                                <div class="weekday">Sun</div>
                                <div class="weekday">Mon</div>
                                <div class="weekday">Tue</div>
                                <div class="weekday">Wed</div>
                                <div class="weekday">Thu</div>
                                <div class="weekday">Fri</div>
                                <div class="weekday">Sat</div>
                            </div>
                            
                            <div class="calendar-days" id="calendarDays">
                                <!-- Calendar days will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 3: Quick Actions and Payment Due Side by Side -->
            <div class="action-row">
                <div class="action-card quick-actions">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                        
                        <div class="header-accent"></div>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions-grid">
                            <div class="quick-action-item">
                                <div class="action-icon book-session">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Book Training</h4>
                                    <p>Schedule a training session</p>
                                </div>
                                <a href="<?php echo URLROOT; ?>/player/bookings" class="action-btn">Book</a>
                            </div>

                            <div class="quick-action-item">
                                <div class="action-icon view-stats">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Performance</h4>
                                    <p>View detailed statistics</p>
                                </div>
                                <a href="<?php echo URLROOT; ?>/player/performance" class="action-btn">View</a>
                            </div>

                            <div class="quick-action-item">
                                <div class="action-icon medical-record">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Medical</h4>
                                    <p>Health records & checkup</p>
                                </div>
                                <a href="<?php echo URLROOT; ?>/player/medical" class="action-btn">Update</a>
                            </div>

                            <div class="quick-action-item">
                                <div class="action-icon shopping">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Equipment</h4>
                                    <p>Rent cricket gear</p>
                                </div>
                                <a href="<?php echo URLROOT; ?>/player/shopping" class="action-btn">Shop</a>
                            </div>
                        </div>
                    </div>
                </div></div>

                <div class="action-card payment-due">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-credit-card"></i> Payment Due</h2>
                        </div>
                        <div class="header-accent"></div>
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

    <!-- Pass Schedule Data to JavaScript -->
    <script>
        // Pass PHP data to JavaScript
        window.dashboardData = {
            todaySchedule: <?php echo json_encode($data['todaySchedule'] ?? []); ?>,
            upcomingSchedule: <?php echo json_encode($data['upcomingSchedule'] ?? []); ?>,
            upcomingBookings: <?php echo json_encode($data['upcomingBookings'] ?? []); ?>,
            currentDate: '<?php echo date('Y-m-d'); ?>',
            currentMonth: <?php echo date('n') - 1; ?>, // JavaScript months are 0-indexed
            currentYear: <?php echo date('Y'); ?>
        };
    </script>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>

</html>

