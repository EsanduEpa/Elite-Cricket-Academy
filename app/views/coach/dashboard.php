<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <!-- Coach Dashboard Layout -->
    <div class="coach-layout">
        <!-- Left Sidebar Panel -->
        <div class="coach-sidebar" id="coachSidebar">
            <div class="sidebar-header">
                <div class="coach-logo">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Coach Panel</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/schedules" class="nav-link" data-tooltip="Schedules">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedules</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Coach'; ?></div>
                <div class="profile-role">Cricket Coach</div>
                <a href="<?php echo URLROOT; ?>/coach/profile" class="action-btn" style="margin-top: 10px;">
                    <i class="fas fa-user-cog"></i> Profile
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-chalkboard-teacher"></i> Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Coach'; ?>!</h1>
                    <p>Manage your training sessions, players, and schedules</p>
                </div>
                <div class="header-actions">
                    <div class="coach-type-badge <?php echo strtolower(str_replace(' ', '-', $data['coachType'])); ?>">
                        <i class="fas fa-medal"></i>
                        <?php echo htmlspecialchars($data['coachType']); ?>
                    </div>
                    <div class="current-time" id="currentTime"></div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon today">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['todaySessions']; ?></div>
                        <div class="stat-label">Today's Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon private">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['privateSessions']; ?></div>
                        <div class="stat-label">Private Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon normal">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['normalSessions']; ?></div>
                        <div class="stat-label">Group Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['totalSessions']; ?></div>
                        <div class="stat-label">Total Sessions</div>
                    </div>
                </div>
            </div>

            <!-- Today's Sessions Overview - Moved to Top -->
            <div class="todays-sessions">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-day"></i> Today's Training Sessions</h2>
                    <div class="session-summary">
                        <span class="private-count"><?php echo $data['privateSessions']; ?> Private</span>
                        <span class="normal-count"><?php echo $data['normalSessions']; ?> Group</span>
                    </div>
                </div>
                <div class="sessions-timeline">
                    <?php 
                    $todayBookings = array_filter($data['upcomingBookings'], function($booking) {
                        return date('Y-m-d', strtotime($booking['date'])) === date('Y-m-d');
                    });
                    ?>
                    <?php if (!empty($todayBookings)): ?>
                        <?php foreach ($todayBookings as $session): ?>
                            <div class="session-timeline-item <?php echo $session['session_type']; ?>">
                                <div class="timeline-marker"></div>
                                <div class="session-card">
                                    <div class="session-time"><?php echo $session['time']; ?></div>
                                    <div class="session-content">
                                        <h4><?php echo htmlspecialchars($session['session_name']); ?></h4>
                                        <p class="participants"><i class="fas fa-users"></i> <?php echo htmlspecialchars($session['player_name']); ?></p>
                                        <div class="session-meta">
                                            <span class="type-badge <?php echo $session['session_type']; ?>">
                                                <?php echo ucfirst($session['session_type']); ?>
                                            </span>
                                            <span class="facility"><?php echo htmlspecialchars($session['facility'] ?? 'TBA'); ?></span>
                                        </div>
                                        <?php if (!empty($session['equipment'])): ?>
                                        <div class="equipment-list">
                                            <i class="fas fa-tools"></i> <?php echo htmlspecialchars($session['equipment']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-sessions-today">
                            <i class="fas fa-calendar-check"></i>
                            <p>No sessions scheduled for today</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Secondary Content Grid for Calendar and Bookings -->
            <div class="secondary-content-grid">
                <!-- Calendar Section -->
                <?php 
                $calendarTitle = 'Training Calendar';
                $calendarIcon = 'fas fa-calendar-alt';
                $calendarId = 'trainingCalendar';
                include APPROOT . '/views/inc/components/calendar.php'; 
                ?>

                <!-- Upcoming Bookings Section -->
                <div class="bookings-section">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> Upcoming Bookings</h2>
                        <div class="filter-controls">
                            <select id="sessionFilter" onchange="filterSessions()">
                                <option value="all">All Sessions</option>
                                <option value="private">Private Sessions</option>
                                <option value="normal">Group Sessions</option>
                            </select>
                        </div>
                    </div>
                    <div class="bookings-list">
                        <?php if (!empty($data['upcomingBookings'])): ?>
                            <?php foreach ($data['upcomingBookings'] as $booking): ?>
                                <div class="booking-item <?php echo $booking['session_type']; ?>-session">
                                    <div class="booking-time">
                                        <div class="time"><?php echo $booking['time']; ?></div>
                                        <div class="date"><?php echo date('M d', strtotime($booking['date'])); ?></div>
                                    </div>
                                    <div class="booking-details">
                                        <h3><?php echo htmlspecialchars($booking['session_name']); ?></h3>
                                        <p class="participants-list"><i class="fas fa-users"></i> <?php echo htmlspecialchars($booking['player_name']); ?></p>
                                        <div class="session-info">
                                            <span class="session-type <?php echo $booking['session_type']; ?>">
                                                <i class="fas <?php echo $booking['session_type'] === 'private' ? 'fa-user' : 'fa-users'; ?>"></i>
                                                <?php echo ucfirst($booking['session_type']); ?>
                                            </span>
                                            <span class="duration">
                                                <i class="fas fa-clock"></i> <?php echo $booking['duration']; ?>
                                            </span>
                                        </div>
                                        <div class="facility-info">
                                            <span class="facility">
                                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['facility'] ?? 'TBA'); ?>
                                            </span>
                                            <?php if (!empty($booking['equipment'])): ?>
                                            <span class="equipment">
                                                <i class="fas fa-tools"></i> <?php echo htmlspecialchars($booking['equipment']); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <button class="btn-action view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action edit" title="Edit Booking">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-bookings">
                                <i class="fas fa-calendar-times"></i>
                                <h3>No upcoming bookings</h3>
                                <p>Schedule new sessions to see them here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Charts & Analytics Section -->
            <div class="analytics-section">
                <div class="section-header full-width">
                    <h2><i class="fas fa-chart-area"></i> Analytics & Player Insights</h2>
                    <p class="muted">Interactive, data-driven charts help you monitor player progress, attendance trends, and health status. Use the filters to focus on specific players, date ranges, or teams.</p>
                </div>

                <div class="analytics-grid">
                    <!-- Player Performance Chart -->
                    <div class="analytics-card" id="performance">
                        <div class="card-header">
                            <h3>Player Performance</h3>
                            <div class="controls">
                                <select id="performancePlayerSelect"></select>
                                <select id="performanceRangeSelect">
                                    <option value="30">Last 30 days</option>
                                    <option value="90">Last 90 days</option>
                                    <option value="365">Last 12 months</option>
                                </select>
                                <button class="btn small" id="performanceToggleType">Toggle Line/Bar</button>
                            </div>
                        </div>
                        <div class="card-body chart-container">
                            <canvas id="performanceChart" aria-label="Player performance over time"></canvas>
                        </div>
                        <div class="card-footer muted">Tip: Click a legend item to show/hide series. Hover for exact values.</div>
                    </div>

                    <!-- Attendance Chart -->
                    <div class="analytics-card" id="sessions">
                        <div class="card-header">
                            <h3>Attendance & Session Participation</h3>
                            <div class="controls">
                                <select id="attendanceTeamSelect"></select>
                                <select id="attendanceRangeSelect">
                                    <option value="30">Last 30 days</option>
                                    <option value="90">Last 90 days</option>
                                    <option value="365">Last 12 months</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-container">
                            <canvas id="attendanceChart" aria-label="Attendance per session"></canvas>
                        </div>
                        <div class="card-footer muted">Monthly totals shown as bars. Use filters to compare squads or individuals.</div>
                    </div>

                    <!-- Health Status Pie Chart -->
                    <div class="analytics-card" id="health">
                        <div class="card-header">
                            <h3>Health & Injury Overview</h3>
                            <div class="controls">
                                <select id="healthFilterSelect">
                                    <option value="all">All Players</option>
                                    <option value="fit">Fit</option>
                                    <option value="under_observation">Under Observation</option>
                                    <option value="injured">Injured</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-container">
                            <canvas id="healthChart" aria-label="Health status distribution"></canvas>
                        </div>
                        <div class="card-footer muted">Track injury load and clearance. Click segments to filter player lists.</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="bottom-section">
                <div class="quick-actions-card">
                    <div class="section-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <button class="action-btn primary" onclick="scheduleSession()">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Schedule Session</span>
                        </button>
                        <button class="action-btn secondary" onclick="viewPlayers()">
                            <i class="fas fa-users"></i>
                            <span>View Players</span>
                        </button>
                        <button class="action-btn success" onclick="addRecommendation()">
                            <i class="fas fa-lightbulb"></i>
                            <span>Add Recommendation</span>
                        </button>
                        <button class="action-btn warning" onclick="checkMedical()">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Check</span>
                        </button>
                    </div>
                </div>

                <div class="player-summary-card">
                    <div class="section-header">
                        <h3><i class="fas fa-chart-bar"></i> Player Performance Overview</h3>
                    </div>
                    <div class="player-stats">
                        <?php if (!empty($data['playerProfiles'])): ?>
                            <?php foreach (array_slice($data['playerProfiles'], 0, 3) as $player): ?>
                                <div class="player-stat-item">
                                    <div class="player-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="player-info">
                                        <h4><?php echo htmlspecialchars($player['name']); ?></h4>
                                        <span class="position"><?php echo htmlspecialchars($player['position']); ?></span>
                                    </div>
                                    <div class="performance-rating">
                                        <div class="rating-circle">
                                            <span><?php echo $player['performance_rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        // Expose server-side dashboard data to client-side scripts
        window.__COACH_DASHBOARD_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
    </script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Coach Dashboard JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/coach/dashboard.js"></script>
</body>
</html>
