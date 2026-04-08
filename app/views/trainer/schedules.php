<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/schedules-enhanced.css?v=<?php echo time(); ?>">

<!-- Trainer Schedules Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Trainer'; ?></h4>
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
                    <a href="<?php echo URLROOT; ?>/trainer/dashboard" class="nav-link">
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
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Slot Sessions</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/schedules" class="nav-link active">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Training Schedules</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/tournaments" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Nutrition & Supplements</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Workout Plans</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/medical" class="nav-link">
                        <i class="fas fa-user-injured"></i>
                        <span>Medical Records</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-alt"></i> Training Schedules</h1>
                    <p>Manage training schedules and recurring sessions</p>
                </div>
                <div class="header-actions">
                    <button class="btn-training">
                        <i class="fas fa-plus"></i> New Schedule
                    </button>
                    <button class="btn-performance">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Schedules Content -->
        <div class="schedules-container">
            <!-- Today's Schedule Box -->
            <div class="schedules-glass-card">
                <div class="schedules-header">
                    <div class="schedules-header-left">
                        <div class="schedules-header-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="schedules-header-content">
                            <h2>Today's Schedule</h2>
                            <p>Current day training sessions and activities</p>
                        </div>
                    </div>
                    <div class="schedules-date-display"><?php echo date('M j, Y'); ?></div>
                </div>
                <div class="schedules-table-container">
                    <table class="schedules-enhanced-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-clock"></i> Time</th>
                                <th><i class="fas fa-dumbbell"></i> Session Type</th>
                                <th><i class="fas fa-users"></i> Participants</th>
                                <th><i class="fas fa-map-marker-alt"></i> Location</th>
                                <th><i class="fas fa-flag"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="schedules-time-display">
                                        <div class="schedules-time-primary">9:00 AM</div>
                                        <div class="schedules-time-secondary">2 hours</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge youth-session">
                                        <i class="fas fa-graduation-cap"></i>
                                        Youth Training
                                    </div>
                                </td>
                                <td>15 Players</td>
                                <td>Field A</td>
                                <td>
                                    <span class="schedules-status-badge confirmed">
                                        <i class="fas fa-check"></i>
                                        Confirmed
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editSchedule(1)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action view" onclick="viewSchedule(1)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-time-display">
                                        <div class="schedules-time-primary">11:00 AM</div>
                                        <div class="schedules-time-secondary">1 hour</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge private-session">
                                        <i class="fas fa-user"></i>
                                        Private Session
                                    </div>
                                </td>
                                <td>Kumara Silva</td>
                                <td>Gym</td>
                                <td>
                                    <span class="schedules-status-badge confirmed">
                                        <i class="fas fa-check"></i>
                                        Confirmed
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editSchedule(2)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action view" onclick="viewSchedule(2)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-time-display">
                                        <div class="schedules-time-primary">2:00 PM</div>
                                        <div class="schedules-time-secondary">2 hours</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge team-session">
                                        <i class="fas fa-users"></i>
                                        Team Practice
                                    </div>
                                </td>
                                <td>12 Players</td>
                                <td>Main Field</td>
                                <td>
                                    <span class="schedules-status-badge pending">
                                        <i class="fas fa-clock"></i>
                                        Pending
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editSchedule(3)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action view" onclick="viewSchedule(3)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-time-display">
                                        <div class="schedules-time-primary">4:00 PM</div>
                                        <div class="schedules-time-secondary">1 hour</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge fitness-session">
                                        <i class="fas fa-dumbbell"></i>
                                        Fitness Training
                                    </div>
                                </td>
                                <td>8 Players</td>
                                <td>Fitness Center</td>
                                <td>
                                    <span class="schedules-status-badge confirmed">
                                        <i class="fas fa-check"></i>
                                        Confirmed
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editSchedule(4)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action view" onclick="viewSchedule(4)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Weekly Schedule Overview Box -->
            <div class="schedules-glass-card">
                <div class="schedules-header">
                    <div class="schedules-header-left">
                        <div class="schedules-header-icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="schedules-header-content">
                            <h2>This Week's Overview</h2>
                            <p>Complete weekly schedule summary</p>
                        </div>
                    </div>
                    <div class="schedules-week-navigation">
                        <button class="schedules-nav-btn" onclick="previousWeek()" title="Previous Week">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="schedules-week-display">Oct 20 - Oct 26, 2025</span>
                        <button class="schedules-nav-btn" onclick="nextWeek()" title="Next Week">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="schedules-table-container">
                    <table class="schedules-enhanced-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-day"></i> Day</th>
                                <th><i class="fas fa-list"></i> Sessions</th>
                                <th><i class="fas fa-users"></i> Participants</th>
                                <th><i class="fas fa-clock"></i> Hours</th>
                                <th><i class="fas fa-flag"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Monday</div>
                                    <div class="schedules-table-cell-secondary">Oct 20</div>
                                </td>
                                <td>4 Sessions</td>
                                <td>35 Players</td>
                                <td>6 Hours</td>
                                <td>
                                    <span class="schedules-status-badge complete">
                                        <i class="fas fa-check-circle"></i>
                                        Complete
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Tuesday</div>
                                    <div class="schedules-table-cell-secondary">Oct 21</div>
                                </td>
                                <td>3 Sessions</td>
                                <td>28 Players</td>
                                <td>5 Hours</td>
                                <td>
                                    <span class="schedules-status-badge scheduled">
                                        <i class="fas fa-calendar-check"></i>
                                        Scheduled
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Wednesday</div>
                                    <div class="schedules-table-cell-secondary">Oct 22</div>
                                </td>
                                <td>5 Sessions</td>
                                <td>42 Players</td>
                                <td>7 Hours</td>
                                <td>
                                    <span class="schedules-status-badge scheduled">
                                        <i class="fas fa-calendar-check"></i>
                                        Scheduled
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Thursday</div>
                                    <div class="schedules-table-cell-secondary">Oct 23</div>
                                </td>
                                <td>3 Sessions</td>
                                <td>25 Players</td>
                                <td>4.5 Hours</td>
                                <td>
                                    <span class="schedules-status-badge planning">
                                        <i class="fas fa-clipboard-list"></i>
                                        Planning
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Friday</div>
                                    <div class="schedules-table-cell-secondary">Oct 24</div>
                                </td>
                                <td>4 Sessions</td>
                                <td>38 Players</td>
                                <td>6.5 Hours</td>
                                <td>
                                    <span class="schedules-status-badge planning">
                                        <i class="fas fa-clipboard-list"></i>
                                        Planning
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Saturday</div>
                                    <div class="schedules-table-cell-secondary">Oct 25</div>
                                </td>
                                <td>6 Sessions</td>
                                <td>55 Players</td>
                                <td>8 Hours</td>
                                <td>
                                    <span class="schedules-status-badge scheduled">
                                        <i class="fas fa-calendar-check"></i>
                                        Scheduled
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-primary">Sunday</div>
                                    <div class="schedules-table-cell-secondary">Oct 26</div>
                                </td>
                                <td>2 Sessions</td>
                                <td>20 Players</td>
                                <td>3 Hours</td>
                                <td>
                                    <span class="schedules-status-badge rest">
                                        <i class="fas fa-bed"></i>
                                        Rest Day
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recurring Schedules Box -->
            <div class="schedules-glass-card">
                <div class="schedules-header">
                    <div class="schedules-header-left">
                        <div class="schedules-header-icon">
                            <i class="fas fa-repeat"></i>
                        </div>
                        <div class="schedules-header-content">
                            <h2>Recurring Schedules</h2>
                            <p>Manage repeating training sessions and programs</p>
                        </div>
                    </div>
                    <button class="schedules-enhanced-btn schedules-btn-primary" onclick="showAddRecurringForm()">
                        <i class="fas fa-plus"></i>
                        <span>Add Recurring</span>
                    </button>
                </div>
                <div class="schedules-table-container">
                    <table class="schedules-enhanced-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-file-alt"></i> Schedule Name</th>
                                <th><i class="fas fa-dumbbell"></i> Session Type</th>
                                <th><i class="fas fa-calendar"></i> Days</th>
                                <th><i class="fas fa-clock"></i> Time</th>
                                <th><i class="fas fa-users"></i> Participants</th>
                                <th><i class="fas fa-map-marker-alt"></i> Location</th>
                                <th><i class="fas fa-flag"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-title">Youth Training Program</div>
                                    <div class="schedules-table-cell-details">Regular youth development sessions</div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge youth-session">
                                        <i class="fas fa-graduation-cap"></i>
                                        Youth Training
                                    </div>
                                </td>
                                <td>Mon, Wed, Fri</td>
                                <td>9:00 AM - 11:00 AM</td>
                                <td>15-18 Players</td>
                                <td>Field A</td>
                                <td>
                                    <span class="schedules-status-badge active">
                                        <i class="fas fa-play"></i>
                                        Active
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editRecurring(1)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action pause" onclick="pauseRecurring(1)" title="Pause Schedule">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                        <button class="schedules-btn-action delete" onclick="deleteRecurring(1)" title="Delete Schedule">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-title">Advanced Team Practice</div>
                                    <div class="schedules-table-cell-details">Senior team practice sessions</div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge team-session">
                                        <i class="fas fa-users"></i>
                                        Team Practice
                                    </div>
                                </td>
                                <td>Tue, Thu, Sat</td>
                                <td>2:00 PM - 4:00 PM</td>
                                <td>12-15 Players</td>
                                <td>Main Field</td>
                                <td>
                                    <span class="schedules-status-badge active">
                                        <i class="fas fa-play"></i>
                                        Active
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editRecurring(2)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action pause" onclick="pauseRecurring(2)" title="Pause Schedule">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                        <button class="schedules-btn-action delete" onclick="deleteRecurring(2)" title="Delete Schedule">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-title">Fitness Training Sessions</div>
                                    <div class="schedules-table-cell-details">Strength and conditioning program</div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge fitness-session">
                                        <i class="fas fa-dumbbell"></i>
                                        Fitness Training
                                    </div>
                                </td>
                                <td>Mon, Wed, Fri</td>
                                <td>4:00 PM - 5:00 PM</td>
                                <td>8-10 Players</td>
                                <td>Fitness Center</td>
                                <td>
                                    <span class="schedules-status-badge active">
                                        <i class="fas fa-play"></i>
                                        Active
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editRecurring(3)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action pause" onclick="pauseRecurring(3)" title="Pause Schedule">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                        <button class="schedules-btn-action delete" onclick="deleteRecurring(3)" title="Delete Schedule">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="schedules-table-cell-title">Private Coaching Slots</div>
                                    <div class="schedules-table-cell-details">Individual player development</div>
                                </td>
                                <td>
                                    <div class="schedules-session-badge private-session">
                                        <i class="fas fa-user"></i>
                                        Private Session
                                    </div>
                                </td>
                                <td>Daily</td>
                                <td>11:00 AM - 12:00 PM</td>
                                <td>1 Player</td>
                                <td>Various</td>
                                <td>
                                    <span class="schedules-status-badge paused">
                                        <i class="fas fa-pause"></i>
                                        Paused
                                    </span>
                                </td>
                                <td>
                                    <div class="schedules-action-buttons">
                                        <button class="schedules-btn-action edit" onclick="editRecurring(4)" title="Edit Schedule">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="schedules-btn-action play" onclick="resumeRecurring(4)" title="Resume Schedule">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="schedules-btn-action delete" onclick="deleteRecurring(4)" title="Delete Schedule">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Schedule Statistics Box -->
            <div class="schedules-glass-card">
                <div class="schedules-header">
                    <div class="schedules-header-left">
                        <div class="schedules-header-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="schedules-header-content">
                            <h2>Schedule Statistics</h2>
                            <p>Weekly performance and activity metrics</p>
                        </div>
                    </div>
                </div>
                <div class="schedules-stats-grid">
                    <div class="schedules-stat-card stat-sessions">
                        <div class="schedules-stat-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="schedules-stat-info">
                            <div class="schedules-stat-number">27</div>
                            <div class="schedules-stat-label">Total Sessions This Week</div>
                            <div class="schedules-stat-change positive">+3 from last week</div>
                        </div>
                    </div>
                    <div class="schedules-stat-card stat-participants">
                        <div class="schedules-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="schedules-stat-info">
                            <div class="schedules-stat-number">243</div>
                            <div class="schedules-stat-label">Total Participants</div>
                            <div class="schedules-stat-change positive">+15 from last week</div>
                        </div>
                    </div>
                    <div class="schedules-stat-card stat-hours">
                        <div class="schedules-stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="schedules-stat-info">
                            <div class="schedules-stat-number">40.5</div>
                            <div class="schedules-stat-label">Total Training Hours</div>
                            <div class="schedules-stat-change neutral">Same as last week</div>
                        </div>
                    </div>
                    <div class="schedules-stat-card stat-attendance">
                        <div class="schedules-stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="schedules-stat-info">
                            <div class="schedules-stat-number">92%</div>
                            <div class="schedules-stat-label">Attendance Rate</div>
                            <div class="schedules-stat-change positive">+2% from last week</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Box -->
            <div class="schedules-glass-card">
                <div class="schedules-header">
                    <div class="schedules-header-left">
                        <div class="schedules-header-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="schedules-header-content">
                            <h2>Quick Actions</h2>
                            <p>Common schedule management tasks</p>
                        </div>
                    </div>
                </div>
                <div class="schedules-quick-actions">
                    <button class="schedules-action-btn" onclick="addNewSession()">
                        <div class="schedules-action-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span class="schedules-action-text">Add Session</span>
                    </button>
                    <button class="schedules-action-btn" onclick="copyLastWeek()">
                        <div class="schedules-action-icon">
                            <i class="fas fa-copy"></i>
                        </div>
                        <span class="schedules-action-text">Copy Last Week</span>
                    </button>
                    <button class="schedules-action-btn" onclick="viewCalendar()">
                        <div class="schedules-action-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <span class="schedules-action-text">Calendar View</span>
                    </button>
                    <button class="schedules-action-btn" onclick="exportSchedule()">
                        <div class="schedules-action-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <span class="schedules-action-text">Export Schedule</span>
                    </button>
                    <button class="schedules-action-btn" onclick="printSchedule()">
                        <div class="schedules-action-icon">
                            <i class="fas fa-print"></i>
                        </div>
                        <span class="schedules-action-text">Print Schedule</span>
                    </button>
                    <button class="schedules-action-btn" onclick="sendNotifications()">
                        <div class="schedules-action-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <span class="schedules-action-text">Send Notifications</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script>
// Schedule-specific functions
function editSchedule(id) {
    showNotification('Edit schedule ' + id + ' - Coming soon!', 'info');
}

function viewSchedule(id) {
    showNotification('View schedule details ' + id + ' - Coming soon!', 'info');
}

function previousWeek() {
    showNotification('Loading previous week...', 'info');
}

function nextWeek() {
    showNotification('Loading next week...', 'info');
}

function showAddRecurringForm() {
    showNotification('Add recurring schedule form - Coming soon!', 'info');
}

function editRecurring(id) {
    showNotification('Edit recurring schedule ' + id + ' - Coming soon!', 'info');
}

function pauseRecurring(id) {
    if (confirm('Are you sure you want to pause this recurring schedule?')) {
        showNotification('Recurring schedule ' + id + ' paused', 'success');
    }
}

function resumeRecurring(id) {
    if (confirm('Are you sure you want to resume this recurring schedule?')) {
        showNotification('Recurring schedule ' + id + ' resumed', 'success');
    }
}

function deleteRecurring(id) {
    if (confirm('Are you sure you want to delete this recurring schedule? This action cannot be undone.')) {
        showNotification('Recurring schedule ' + id + ' deleted', 'success');
    }
}

function addNewSession() {
    showNotification('Add new session form - Coming soon!', 'info');
}

function copyLastWeek() {
    if (confirm('Copy last week\'s schedule to this week?')) {
        showNotification('Last week\'s schedule copied successfully!', 'success');
    }
}

function viewCalendar() {
    showNotification('Calendar view - Coming soon!', 'info');
}

function exportSchedule() {
    showNotification('Exporting schedule to PDF...', 'info');
}

function printSchedule() {
    window.print();
}

function sendNotifications() {
    if (confirm('Send schedule notifications to all participants?')) {
        showNotification('Notifications sent successfully!', 'success');
    }
}
</script>
</body>
</html>