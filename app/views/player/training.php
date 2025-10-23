<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/training.css">
    
    <div class="player-layout">
        <!-- Simple Sidebar -->
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
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item active">
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
        <div class="main-content">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-dumbbell"></i> Training Schedule</h1>
                <p>View your upcoming training sessions and track your progress.</p>
            </div>

            <!-- Weekly Overview -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stat-value">5</div>
                    <div class="stat-label">Sessions This Week</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">12h</div>
                    <div class="stat-label">Total Hours</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">3</div>
                    <div class="stat-label">Completed</div>
                </div>
                
               
            </div>

            <!-- Today's Training and Weekly Schedule - Two Tables Per Row -->
            <div class="performance-tables-row">
                <!-- Today's Training -->
                <div class="schedule-card today-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-day"></i> Today's Training</h2>
                            <span class="date-display"><?php echo date('M j'); ?></span>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Training Activity</th>
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
                                    <div class="table-cell-title">Batting Practice</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Indoor Nets - Coach Johnson
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Upcoming</span>
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
                                        <i class="fas fa-dumbbell"></i> Gym Facility - Cardio & Strength
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Upcoming</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

                <!-- This Week's Schedule -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-week"></i> This Week's Schedule</h2>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Training Activity</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Mon</div>
                                    <div class="table-cell-secondary">Monday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Batting & Fielding Practice</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Indoor Nets
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
                                    <div class="table-cell-primary">Tue</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Fitness Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Gym Facility
                                    </div>
                                    <span class="table-badge">Fitness</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">2:30 PM</div>
                                    <div class="table-cell-secondary">4:00 PM</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Wed</div>
                                    <div class="table-cell-secondary">Wednesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Bowling Practice</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-baseball-ball"></i> Outdoor Pitch
                                    </div>
                                    <span class="table-badge">Training</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">11:30 AM</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Thu</div>
                                    <div class="table-cell-secondary">Thursday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Team Practice</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> Main Ground
                                    </div>
                                    <span class="table-badge">Team</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">3:00 PM</div>
                                    <div class="table-cell-secondary">5:00 PM</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Fri</div>
                                    <div class="table-cell-secondary">Friday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Match Simulation</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-trophy"></i> Main Ground
                                    </div>
                                    <span class="table-badge">Match</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">1:00 PM</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Training & Match Calendar -->
            <div class="calendar-section">
                <div class="calendar-header">
                    <h3><i class="fas fa-calendar-alt"></i> Training & Match Calendar</h3>
                    <div class="calendar-controls">
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="month"><i class="fas fa-calendar"></i> Month</button>
                            <button class="view-btn" data-view="week"><i class="fas fa-calendar-week"></i> Week</button>
                            <button class="view-btn" data-view="day"><i class="fas fa-calendar-day"></i> Day</button>
                        </div>
                        <div class="calendar-nav">
                            <button id="todayBtn" class="calendar-btn today-btn" title="Go to Today"><i class="fas fa-calendar-check"></i></button>
                            <button id="prevPeriod" class="calendar-btn"><i class="fas fa-chevron-left"></i></button>
                            <span id="currentPeriod"></span>
                            <button id="nextPeriod" class="calendar-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
                <div id="calendarContent" class="calendar-content">
                    <div id="monthView" class="calendar-grid"></div>
                    <div id="weekView" class="week-view" style="display: none;"></div>
                    <div id="dayView" class="day-view" style="display: none;"></div>
                </div>
                <div class="calendar-legend">
                    <div class="legend-item"><span class="legend-dot training"></span> Training Sessions</div>
                    <div class="legend-item"><span class="legend-dot match"></span> Matches</div>
                    <div class="legend-item"><span class="legend-dot fitness"></span> Fitness</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>Training Actions</h3>
                <div class="action-buttons">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="action-btn">
                        <i class="fas fa-plus"></i> Book Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/performance" class="action-btn">
                        <i class="fas fa-chart-line"></i> View Progress
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Training notes feature coming soon!')">
                        <i class="fas fa-sticky-note"></i> Training Notes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/training.js"></script>
</body>
</html>