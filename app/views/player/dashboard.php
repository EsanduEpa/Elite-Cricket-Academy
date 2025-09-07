<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<body>
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
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping & Rental</span>
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
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment History</span>
                            <?php if(!empty($data['paymentsDue'])): ?>
                                <span class="badge"><?php echo count($data['paymentsDue']); ?></span>
                            <?php endif; ?>
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
                <div class="stat-card performance-stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-bat"></i>
                            Batting Average
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['batting_avg']; ?>">0</div>
                    <div class="stat-label">Season Average</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        +2.3% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-running"></i>
                            Strike Rate
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['strike_rate']; ?>">0</div>
                    <div class="stat-label">Current Strike Rate</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        +5.8% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-target"></i>
                            Total Runs
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['total_runs']; ?>">0</div>
                    <div class="stat-label">Career Runs</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        +125 this month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-bowling-ball"></i>
                            Wickets Taken
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['total_wickets']; ?>">0</div>
                    <div class="stat-label">Career Wickets</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        +8 this month
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Upcoming Events -->
            <div class="dashboard-content-grid">
                <div class="quick-actions-section">
                    <div class="section-header">
                        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    </div>
                    <div class="quick-actions-grid">
                        <div class="quick-action-card book-session">
                            <i class="fas fa-calendar-plus"></i>
                            <h3>Book Training</h3>
                            <p>Schedule a training session</p>
                            <button class="action-btn">Book Now</button>
                        </div>
                        <div class="quick-action-card view-stats">
                            <i class="fas fa-chart-bar"></i>
                            <h3>View Stats</h3>
                            <p>Check detailed performance</p>
                            <button class="action-btn">View Details</button>
                        </div>
                        <div class="quick-action-card medical-record">
                            <i class="fas fa-heartbeat"></i>
                            <h3>Medical Check</h3>
                            <p>Update health records</p>
                            <button class="action-btn">Update</button>
                        </div>
                        <div class="quick-action-card payment">
                            <i class="fas fa-credit-card"></i>
                            <h3>Make Payment</h3>
                            <p>Pay fees or dues</p>
                            <button class="action-btn">Pay Now</button>
                        </div>
                    </div>
                </div>

                <div class="upcoming-events-section">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-check"></i> Upcoming Events</h2>
                        <span class="event-count">5 events this week</span>
                    </div>
                    <div class="events-list">
                        <div class="event-item priority-high">
                            <div class="event-date">
                                <span class="day">07</span>
                                <span class="month">SEP</span>
                            </div>
                            <div class="event-details">
                                <h4>Match vs Central Cricket Club</h4>
                                <p><i class="fas fa-clock"></i> 2:00 PM - 6:00 PM</p>
                                <p><i class="fas fa-map-marker-alt"></i> Main Ground</p>
                            </div>
                            <div class="event-status match-event">Match</div>
                        </div>

                        <div class="event-item priority-medium">
                            <div class="event-date">
                                <span class="day">08</span>
                                <span class="month">SEP</span>
                            </div>
                            <div class="event-details">
                                <h4>Batting Practice Session</h4>
                                <p><i class="fas fa-clock"></i> 10:00 AM - 12:00 PM</p>
                                <p><i class="fas fa-map-marker-alt"></i> Indoor Nets</p>
                            </div>
                            <div class="event-status training-event">Training</div>
                        </div>

                        <div class="event-item priority-medium">
                            <div class="event-date">
                                <span class="day">09</span>
                                <span class="month">SEP</span>
                            </div>
                            <div class="event-details">
                                <h4>Fitness Assessment</h4>
                                <p><i class="fas fa-clock"></i> 9:00 AM - 11:00 AM</p>
                                <p><i class="fas fa-map-marker-alt"></i> Gym Facility</p>
                            </div>
                            <div class="event-status assessment-event">Assessment</div>
                        </div>

                        <div class="event-item priority-low">
                            <div class="event-date">
                                <span class="day">10</span>
                                <span class="month">SEP</span>
                            </div>
                            <div class="event-details">
                                <h4>Team Strategy Meeting</h4>
                                <p><i class="fas fa-clock"></i> 4:00 PM - 5:30 PM</p>
                                <p><i class="fas fa-map-marker-alt"></i> Conference Room</p>
                            </div>
                            <div class="event-status meeting-event">Meeting</div>
                        </div>

                        <div class="event-item priority-medium">
                            <div class="event-date">
                                <span class="day">11</span>
                                <span class="month">SEP</span>
                            </div>
                            <div class="event-details">
                                <h4>Equipment Check & Maintenance</h4>
                                <p><i class="fas fa-clock"></i> 3:00 PM - 4:00 PM</p>
                                <p><i class="fas fa-map-marker-alt"></i> Equipment Room</p>
                            </div>
                            <div class="event-status maintenance-event">Maintenance</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="calendar-section">
                <div class="calendar-header">
                    <h2 class="calendar-title">
                        <i class="fas fa-calendar-alt"></i>
                        Training & Match Schedule
                    </h2>
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
                            <button class="view-btn" onclick="setView('day')">Day</button>
                        </div>
                    </div>
                </div>

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

                <!-- Upcoming Events -->
                <div class="upcoming-events">
                    <h3 class="upcoming-title">
                        <i class="fas fa-clock"></i>
                        Upcoming Events
                    </h3>
                    <div class="event-list">
                        <div class="event-item">
                            <div class="event-date">
                                <div class="day">15</div>
                                <div class="month">DEC</div>
                            </div>
                            <div class="event-details">
                                <div class="event-title">Net Practice Session</div>
                                <div class="event-time">
                                    <i class="fas fa-clock"></i>
                                    9:00 AM - 11:00 AM
                                </div>
                            </div>
                            <div class="event-type training">Training</div>
                        </div>

                        <div class="event-item">
                            <div class="event-date">
                                <div class="day">18</div>
                                <div class="month">DEC</div>
                            </div>
                            <div class="event-details">
                                <div class="event-title">District Championship Match</div>
                                <div class="event-time">
                                    <i class="fas fa-clock"></i>
                                    2:00 PM - 6:00 PM
                                </div>
                            </div>
                            <div class="event-type match">Match</div>
                        </div>

                        <div class="event-item">
                            <div class="event-date">
                                <div class="day">22</div>
                                <div class="month">DEC</div>
                            </div>
                            <div class="event-details">
                                <div class="event-title">Team Strategy Meeting</div>
                                <div class="event-time">
                                    <i class="fas fa-clock"></i>
                                    4:00 PM - 5:30 PM
                                </div>
                            </div>
                            <div class="event-type meeting">Meeting</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-day"></i> Today's Schedule</h2>
                    <a href="<?php echo URLROOT; ?>/player/training" class="view-all-btn">View All</a>
                </div>
                
                <?php if(!empty($data['todaySchedule'])): ?>
                    <div class="cards-grid">
                        <?php foreach($data['todaySchedule'] as $activity): ?>
                            <div class="info-card">
                                <h3><i class="fas fa-clock"></i> <?php echo $activity['time']; ?></h3>
                                <p><strong><?php echo $activity['activity']; ?></strong></p>
                                <p>Location: <?php echo $activity['location']; ?></p>
                                <p>Coach: <?php echo $activity['coach']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">No activities scheduled for today. Enjoy your rest day!</p>
                <?php endif; ?>
            </div>

            <!-- Upcoming Schedule -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-alt"></i> Upcoming Schedule</h2>
                    <a href="<?php echo URLROOT; ?>/player/training" class="view-all-btn">View All</a>
                </div>
                
                <?php if(!empty($data['upcomingSchedule'])): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['upcomingSchedule'] as $schedule): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($schedule['date'])); ?></td>
                                    <td><?php echo $schedule['time']; ?></td>
                                    <td><?php echo $schedule['activity']; ?></td>
                                    <td><?php echo $schedule['location']; ?></td>
                                    <td><span class="status-badge status-active">Confirmed</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">No upcoming activities scheduled.</p>
                <?php endif; ?>
            </div>

            <!-- Dashboard Sections Row -->
            <div class="cards-grid">
                <!-- Upcoming Bookings -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-check"></i> Upcoming Bookings</h2>
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="view-all-btn">View All</a>
                    </div>
                    
                    <?php if(!empty($data['upcomingBookings'])): ?>
                        <?php foreach($data['upcomingBookings'] as $booking): ?>
                            <div class="info-card" style="margin-bottom: 1rem;">
                                <h3><i class="fas fa-user-tie"></i> <?php echo $booking['type']; ?></h3>
                                <p><strong><?php echo date('M j, Y', strtotime($booking['date'])); ?></strong> at <?php echo $booking['time']; ?></p>
                                <p><?php echo isset($booking['coach']) ? 'Coach: ' . $booking['coach'] : 'Facility: ' . $booking['facility']; ?></p>
                                <p>Duration: <?php echo $booking['duration']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #666; text-align: center;">No upcoming bookings</p>
                    <?php endif; ?>
                </div>

                <!-- Rentals Due -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-tools"></i> Rentals Due</h2>
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="view-all-btn">View All</a>
                    </div>
                    
                    <?php if(!empty($data['rentalsDue'])): ?>
                        <?php foreach($data['rentalsDue'] as $rental): ?>
                            <div class="info-card" style="margin-bottom: 1rem;">
                                <h3><i class="fas fa-exclamation-triangle"></i> <?php echo $rental['item']; ?></h3>
                                <p><strong>Due: <?php echo date('M j, Y', strtotime($rental['due_date'])); ?></strong></p>
                                <p>Fee: <?php echo $rental['fee']; ?></p>
                                <span class="status-badge status-due">Due Soon</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #666; text-align: center;">No rentals due</p>
                    <?php endif; ?>
                </div>

                <!-- Payments Due -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-dollar-sign"></i> Payments Due</h2>
                        <a href="<?php echo URLROOT; ?>/player/payments" class="view-all-btn">View All</a>
                    </div>
                    
                    <?php if(!empty($data['paymentsDue'])): ?>
                        <?php foreach($data['paymentsDue'] as $payment): ?>
                            <div class="info-card" style="margin-bottom: 1rem;">
                                <h3><i class="fas fa-credit-card"></i> <?php echo $payment['type']; ?></h3>
                                <p><strong><?php echo $payment['amount']; ?></strong></p>
                                <p>Due: <?php echo date('M j, Y', strtotime($payment['due_date'])); ?></p>
                                <span class="status-badge status-due">Payment Due</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #666; text-align: center;">No payments due</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                
                <div class="quick-actions">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="quick-action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Book Session</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="quick-action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Rent Equipment</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/performance" class="quick-action-btn">
                        <i class="fas fa-chart-line"></i>
                        <span>View Performance</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/payments" class="quick-action-btn">
                        <i class="fas fa-credit-card"></i>
                        <span>Make Payment</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/medical" class="quick-action-btn">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical Records</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/training" class="quick-action-btn">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Training Schedule</span>
                    </a>
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
</body>

</html>
