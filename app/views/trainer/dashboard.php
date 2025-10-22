<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">

<!-- Trainer Dashboard Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer'; ?></h4>
                    <p>Physical Trainer</p>
                </div>
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
                        <span>Schedule & Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Workout Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrition Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Supplements</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                        <i class="fas fa-user-injured"></i>
                        <span>Injury Reports</span>
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
        
        <div class="sidebar-footer">
            <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
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
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="btn btn-training">
                        <i class="fas fa-calendar-plus"></i> New Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/workouts" class="btn btn-performance">
                        <i class="fas fa-dumbbell"></i> Workouts
                    </a>
                    <button class="btn btn-refresh" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i>
                        <span class="current-time"><?php echo date('H:i'); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">28</div>
                <div class="stat-label">Active Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-label">Today's Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">94%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value">18</div>
                <div class="stat-label">Progress Goals</div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="schedule-row">
            <!-- Today's Schedule -->
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
                                    <div class="table-cell-secondary">90 minutes</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Senior Cricket Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> 15 Players - Main Field
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Active</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">11:00 AM</div>
                                    <div class="table-cell-secondary">60 minutes</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Kumara Silva</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Personal Training - Gym
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Upcoming</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">120 minutes</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Youth Development</div>
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
                                    <div class="table-cell-secondary">45 minutes</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Anjali Perera</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Fitness Assessment - Gym B
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Scheduled</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Session Summary -->
                <div class="session-summary">
                    <strong>Today's Summary:</strong> 4 sessions scheduled, 1 active, 3 upcoming. Total training time: 5.25 hours.
                </div>
            </div>

            <!-- Booking Calendar -->
            <div class="schedule-card calendar-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-alt"></i> Booking Calendar</h2>
                        <div class="calendar-controls">
                            <button class="nav-btn" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="currentMonth"><?php echo date('F Y'); ?></span>
                            <button class="nav-btn" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="calendar-container">
                        <div class="calendar-legend">
                            <div class="legend-item">
                                <div class="legend-color pending"></div>
                                <span>Pending</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color confirmed"></div>
                                <span>Confirmed</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color completed"></div>
                                <span>Completed</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color cancelled"></div>
                                <span>Cancelled</span>
                            </div>
                        </div>
                        <div class="booking-calendar-grid" id="bookingCalendarGrid">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Quick Actions and Assignments -->
        <div class="schedule-row">
            <!-- Quick Actions -->
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
                        <a href="<?php echo URLROOT; ?>/trainer/workouts" class="quick-action-btn">
                            <i class="fas fa-dumbbell"></i>
                            <span>Create Workout</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="quick-action-btn">
                            <i class="fas fa-apple-alt"></i>
                            <span>Nutrition Plan</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="quick-action-btn">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Report</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="quick-action-btn">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/reports" class="quick-action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities & Assignments -->
            <div class="schedule-card activities-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-clipboard-list"></i> Recent Activities</h2>
                        <a href="<?php echo URLROOT; ?>/trainer/reports" class="btn-primary">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Workout plan completed</div>
                                <div class="activity-details">Kumara Silva - Upper Body Strength</div>
                                <div class="activity-time">2 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Session scheduled</div>
                                <div class="activity-details">Youth Team - Tomorrow 10:00 AM</div>
                                <div class="activity-time">4 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-injured"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Injury report submitted</div>
                                <div class="activity-details">Anjali Perera - Minor muscle strain</div>
                                <div class="activity-time">1 day ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Progress updated</div>
                                <div class="activity-details">Senior Team - Monthly assessment</div>
                                <div class="activity-time">2 days ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="booking-actions">
            <button class="action-btn view-notes" onclick="openNotesModal()">
                <i class="fas fa-notes-medical"></i> Session Notes
            </button>
            <button class="action-btn followup" onclick="scheduleFollowup()">
                <i class="fas fa-calendar-plus"></i> Schedule Follow-up
            </button>
            <button class="action-btn details" onclick="viewReports()">
                <i class="fas fa-chart-bar"></i> View Reports
            </button>
            <button class="action-btn emergency" onclick="emergencyProtocol()">
                <i class="fas fa-exclamation-triangle"></i> Emergency
            </button>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js?v=<?php echo time(); ?>"></script>
<script>
// Dashboard functionality
function refreshDashboard() {
    location.reload();
}

function openNotesModal() {
    alert('Session notes functionality will be implemented');
}

function scheduleFollowup() {
    window.location.href = '<?php echo URLROOT; ?>/trainer/bookings';
}

function viewReports() {
    window.location.href = '<?php echo URLROOT; ?>/trainer/reports';
}

function emergencyProtocol() {
    if(confirm('Are you sure you want to activate emergency protocol?')) {
        alert('Emergency protocol activated. Relevant authorities will be notified.');
    }
}

// Update time every minute
setInterval(function() {
    const timeElement = document.querySelector('.current-time');
    if(timeElement) {
        const now = new Date();
        timeElement.textContent = now.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'});
    }
}, 60000);
</script>
                    <a href="<?php echo URLROOT; ?>/trainer/nutients" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Nutrition plans</span>
                    </a>
                </li>
              
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workouts" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Workout plans</span>
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
        
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
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

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">24</div>
                <div class="stat-label">Active Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value">8</div>
                <div class="stat-label">Today's Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">92%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value">15</div>
                <div class="stat-label">Progress Goals</div>
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
                
                <div class="session-summary">
                    <strong>Session Summary:</strong> Complete health assessment completed. Player cleared for upcoming tournament. No issues found.
                </div>
                <div class="booking-actions">
                    <button class="action-btn view-notes" onclick="viewSessionNotes(4)">
                        <i class="fas fa-notes-medical"></i> View Notes
                    </button>
                    <button class="action-btn followup" onclick="scheduleFollowup(4)">
                        <i class="fas fa-calendar-plus"></i> Schedule Follow-up
                    </button>
                    <button class="action-btn details" onclick="viewBookingDetails(4)">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                </div>
            </div>

                <!-- Calendar View -->
                <div class="booking-calendar-view" id="bookingCalendarView">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <div class="calendar-navigation">
                                <button class="nav-btn" id="prevBookingMonth">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h3 id="currentBookingMonth">December 2024</h3>
                                <button class="nav-btn" id="nextBookingMonth">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-legend">
                                <div class="legend-item">
                                    <div class="legend-color pending"></div>
                                    <span>Pending</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color confirmed"></div>
                                    <span>Confirmed</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color completed"></div>
                                    <span>Completed</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color cancelled"></div>
                                    <span>Cancelled</span>
                                </div>
                            </div>
                        </div>
                        <div class="booking-calendar-grid" id="bookingCalendarGrid">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Management -->
            <div class="assignment-container">
                <div class="assignment-header">
                    <h2><i class="fas fa-clipboard-list"></i> Plan Assignments</h2>
                    <button class="btn-primary" id="newAssignmentBtn">
                        <i class="fas fa-plus"></i>
                        New Assignment
                    </button>
                </div>
                <div class="card-content">
                    <div class="assignment-placeholder">
                        <p>No assignments available. Click "New Assignment" to create one.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Quick Actions -->
        <div class="schedule-row">
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
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="quick-action-btn">
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
