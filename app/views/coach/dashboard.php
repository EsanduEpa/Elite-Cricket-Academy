<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Header (same as v_home.php) -->
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>

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
                        <a href="<?php echo URLROOT; ?>/coach/schedules" class="nav-link" data-tooltip="Schedules">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedules</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/bookings" class="nav-link" data-tooltip="Bookings">
                            <i class="fas fa-bookmark"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/recommendations" class="nav-link" data-tooltip="Recommendations">
                            <i class="fas fa-lightbulb"></i>
                            <span>Recommendations</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/medical" class="nav-link" data-tooltip="Medical Records">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Logout Button -->
            <div class="logout-section">
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-chalkboard-teacher"></i> Coach Dashboard</h1>
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
                                        <h4><?php echo htmlspecialchars($session['player_name']); ?></h4>
                                        <div class="session-meta">
                                            <span class="type-badge <?php echo $session['session_type']; ?>">
                                                <?php echo ucfirst($session['session_type']); ?>
                                            </span>
                                            <span class="facility"><?php echo htmlspecialchars($session['facility']); ?></span>
                                        </div>
                                        <div class="equipment-list">
                                            <i class="fas fa-tools"></i> <?php echo htmlspecialchars($session['equipment']); ?>
                                        </div>
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
                <!-- Calendar Section - Made Smaller -->
                <div class="calendar-section">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-alt"></i> Training Calendar</h2>
                        <div class="calendar-controls">
                            <button class="btn-control" onclick="prevMonth()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="currentMonth">September 2025</span>
                            <button class="btn-control" onclick="nextMonth()">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-container">
                        <div id="trainingCalendar"></div>
                    </div>
                </div>

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
                                        <h3><?php echo htmlspecialchars($booking['player_name']); ?></h3>
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
                                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['facility']); ?>
                                            </span>
                                            <span class="equipment">
                                                <i class="fas fa-tools"></i> <?php echo htmlspecialchars($booking['equipment']); ?>
                                            </span>
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
    <!-- Coach Dashboard JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/coach/dashboard.js"></script>
</body>
</html>
