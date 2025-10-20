<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time() + 20; ?>">

<!-- Trainer Dashboard Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-dumbbell"></i>
                <h3>Trainer Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Player Bookings</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/exercises" class="nav-link">
                        <i class="fas fa-running"></i>
                        <span>Common Exercises</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Supplement Recommendations</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury-reports" class="nav-link">
                        <i class="fas fa-user-injured"></i>
                        <span>Injury Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout-plans" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Individual Workouts</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/schedules" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Training Schedules</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/reports" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Progress Reports</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Trainer Profile Section -->
        <div class="trainer-profile">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <div class="trainer-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer'; ?></div>
                <div class="trainer-role">Physical Trainer</div>
            </div>
            <div class="profile-actions">
                <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                    <i class="fas fa-user-cog"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
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
                <div class="header-text">
                    <h1><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer'; ?>!</h1>
                    <p>Your training management dashboard - Schedule sessions, track progress, and manage your clients</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="btn-training">
                        <i class="fas fa-calendar-plus"></i> New Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/exercises" class="btn-performance">
                        <i class="fas fa-dumbbell"></i> Exercises
                    </a>
                    <button class="btn-refresh" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i>
                        <span class="current-time"><?php echo date('H:i'); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Today's Sessions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Active Clients</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Hours Booked</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">96%</div>
                    <div class="stat-label">Week Utilization</div>
                </div>
            </div>
        </div>

        <!-- Row 1: Today's Schedule and Client Progress Side by Side -->
        <div class="schedule-row">
            <div class="schedule-card today-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-day"></i> Today's Sessions</h2>
                        <span class="date-display"><?php echo date('M j, Y'); ?></span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Client & Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">9:00 AM</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Youth Cricket Program</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> 15 Players - Field A
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Active</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">11:30 AM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Kumara Silva</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Fitness Assessment - Gym
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Upcoming</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Advanced Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> 12 Players - Indoor Nets
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Scheduled</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">4:30 PM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Anjali Perera</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Strength Training - Gym B
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Scheduled</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-chart-line"></i> Client Progress</h2>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Program</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-title">James Wilson</div>
                                    <div class="table-cell-secondary">Cricket Player</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Strength & Conditioning</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Week 4 of 8
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Excellent</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">Sarah Mitchell</div>
                                    <div class="table-cell-secondary">Tennis Player</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Agility Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-running"></i> Week 2 of 6
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Good</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">David Chen</div>
                                    <div class="table-cell-secondary">Football Player</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Recovery Program</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-medkit"></i> Week 6 of 8
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Improving</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 2: Upcoming Sessions This Week -->
        <div class="schedule-row">
            <div class="schedule-card full-width">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-week"></i> This Week's Schedule</h2>
                        <span class="date-display">Sep 9 - Sep 15, 2025</span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Time</th>
                                <th>Participants</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 10</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Advanced Batting Clinic</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-baseball-ball"></i> Batting Technique Focus
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">3 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">8 Players</div>
                                    <div class="table-cell-secondary">Indoor Nets</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Confirmed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 11</div>
                                    <div class="table-cell-secondary">Wednesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Fitness Assessment Day</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-heartbeat"></i> Monthly Progress Review
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">4 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">15 Clients</div>
                                    <div class="table-cell-secondary">Fitness Center</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Scheduled</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 3: Recent Activity and Quick Actions -->
        <div class="schedule-row">
            <div class="schedule-card recent-activity">
                <div class="card-header payment-due">
                    <div class="header-content">
                        <h2><i class="fas fa-bell"></i> Recent Activity</h2>
                    </div>
                </div>
                <div class="card-content">
                    <div class="payment-items">
                        <div class="payment-item">
                            <div class="payment-info">
                                <div class="payment-title">New Client Registration</div>
                                <div class="payment-details">Michael Johnson joined Advanced Program</div>
                            </div>
                            <div class="payment-amount success">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <div class="payment-title">Session Completed</div>
                                <div class="payment-details">Emma Davis - Strength Training</div>
                            </div>
                            <div class="payment-amount">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <div class="payment-title">Program Update</div>
                                <div class="payment-details">Updated workout plan for 3 clients</div>
                            </div>
                            <div class="payment-amount warning">
                                <i class="fas fa-edit"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="schedule-card quick-actions-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    </div>
                </div>
                <div class="card-content">
                    <div class="quick-action-grid">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="quick-action-btn">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Schedule Session</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/exercises" class="quick-action-btn">
                            <i class="fas fa-dumbbell"></i>
                            <span>Manage Exercises</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="quick-action-btn">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/injury-reports" class="quick-action-btn">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js?v=<?php echo time(); ?>"></script>
