<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css">

<div class="dashboard-container">
    <!-- Dashboard Main Content -->
    <div class="dashboard-main-content">
        <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Trainer Dashboard</h3>
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John Smith'; ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="#dashboard" class="nav-link" data-section="dashboard">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#schedules" class="nav-link" data-section="schedules">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Schedules</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#bookings" class="nav-link" data-section="bookings">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tournaments" class="nav-link" data-section="tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#nutrition" class="nav-link" data-section="nutrition">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrition & Supplements</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#workout" class="nav-link" data-section="workout">
                        <i class="fas fa-dumbbell"></i>
                        <span>Health & Fitness</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#medical" class="nav-link" data-section="medical">
                        <i class="fas fa-heartbeat"></i>
                        <span>Player Medical Records</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?php echo URLROOT; ?>/logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Integrated Header -->
        <div class="integrated-header">
            <div class="header-top">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="admin-info">
                        <i class="fas fa-user-md"></i>
                        <span>Physical Trainer Dashboard</span>
                    </div>
                </div>
                
                <div class="header-right">
                    <button class="refresh-btn" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                    <div class="current-time">
                        <span id="currentDateTime">Wed, Sep 10, 2025, 09:39 AM</span>
                    </div>
                </div>
            </div>
            
            <div class="header-main">
                <div class="welcome-section">
                    <div class="academy-title">
                        <h1><i class="fas fa-heartbeat"></i> Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John Smith'; ?>!</h1>
                        <p>Ready to help athletes achieve their health and fitness goals today</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Overview -->
        <section id="dashboard-section" class="content-section active">

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div class="stat-info">
                        <h3>8</h3>
                        <p>Health Assessments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24</h3>
                        <p>Fitness Reports</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>3</h3>
                        <p>Pending Reviews</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <div class="stat-info">
                        <h3>2</h3>
                        <p>Progress Reports</p>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Calendar Section -->
                <div class="calendar-section">
                    <div class="section-header">
                        <h2>Training Calendar</h2>
                        <div class="calendar-controls">
                            <button class="btn-secondary" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="current-month" id="currentMonth">September 2025</span>
                            <button class="btn-secondary" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-container">
                        <div class="calendar" id="calendar">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <span class="legend-color group-session"></span>
                            <span>Group Sessions</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color private-session"></span>
                            <span>Private Sessions</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color tournament"></span>
                            <span>Tournaments</span>
                        </div>
                    </div>

                    <!-- Recent Activity Section -->
                    <div class="recent-activity-section">
                        <div class="section-header">
                            <h3>Recent Activity</h3>
                            <a href="#" class="view-all-link">View All</a>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-details">
                                    <p><strong>New Player Registered</strong></p>
                                    <p>Kamal Perera joined Youth Cricket Program</p>
                                    <span class="activity-time">2 hours ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="activity-details">
                                    <p><strong>Session Completed</strong></p>
                                    <p>Advanced Training session with 12 players</p>
                                    <span class="activity-time">4 hours ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="activity-details">
                                    <p><strong>Achievement Unlocked</strong></p>
                                    <p>Saman Silva completed 100 training hours</p>
                                    <span class="activity-time">1 day ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="activity-details">
                                    <p><strong>Tournament Update</strong></p>
                                    <p>Inter-School Cricket Championship registration opened</p>
                                    <span class="activity-time">2 days ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="activity-details">
                                    <p><strong>Health Assessment</strong></p>
                                    <p>Fitness evaluation completed for 8 players</p>
                                    <span class="activity-time">3 days ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Sessions Panel -->
                <div class="sessions-panel">
                    <div class="session-section">
                        <h3>Today's Group Sessions</h3>
                        <div class="session-list">
                            <div class="session-item">
                                <div class="session-time">
                                    <i class="fas fa-clock"></i>
                                    <span>09:00 AM - 11:00 AM</span>
                                </div>
                                <div class="session-details">
                                    <h4>Youth Cricket Program</h4>
                                    <p>15 Players • Field A</p>
                                    <span class="session-type group">Group</span>
                                </div>
                            </div>
                            <div class="session-item">
                                <div class="session-time">
                                    <i class="fas fa-clock"></i>
                                    <span>02:00 PM - 04:00 PM</span>
                                </div>
                                <div class="session-details">
                                    <h4>Advanced Training</h4>
                                    <p>12 Players • Indoor Nets</p>
                                    <span class="session-type group">Group</span>
                                </div>
                            </div>
                            <div class="session-item">
                                <div class="session-time">
                                    <i class="fas fa-clock"></i>
                                    <span>05:00 PM - 07:00 PM</span>
                                </div>
                                <div class="session-details">
                                    <h4>Evening Practice</h4>
                                    <p>18 Players • Field B</p>
                                    <span class="session-type group">Group</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="session-section">
                        <h3>Private Sessions</h3>
                        <div class="session-list">
                            <div class="session-item">
                                <div class="session-time">
                                    <i class="fas fa-clock"></i>
                                    <span>11:30 AM - 12:30 PM</span>
                                </div>
                                <div class="session-details">
                                    <h4>Kumara Silva</h4>
                                    <p>Fitness Assessment</p>
                                    <span class="session-type private">Private</span>
                                </div>
                            </div>
                            <div class="session-item">
                                <div class="session-time">
                                    <i class="fas fa-clock"></i>
                                    <span>04:30 PM - 05:30 PM</span>
                                </div>
                                <div class="session-details">
                                    <h4>Anjali Perera</h4>
                                    <p>Strength Training</p>
                                    <span class="session-type private">Private</span>
                                </div>
                            </div>
                            <div class="session-item upcoming">
                                <div class="session-time">
                                    <i class="fas fa-clock"></i>
                                    <span>07:30 PM - 08:30 PM</span>
                                </div>
                                <div class="session-details">
                                    <h4>Dasun Fernando</h4>
                                    <p>Injury Recovery</p>
                                    <span class="session-type private">Private</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="action-buttons">
                    <button class="action-btn blue">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Schedule Session</span>
                    </button>
                    <button class="action-btn gray">
                        <i class="fas fa-users"></i>
                        <span>View Players</span>
                    </button>
                    <button class="action-btn green">
                        <i class="fas fa-lightbulb"></i>
                        <span>Add Recommendation</span>
                    </button>
                    <button class="action-btn orange">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical Check</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Other sections will be added for schedules, bookings, etc. -->
        <!-- Schedules Section with Calendar -->
        <section id="schedules-section" class="content-section">
            <div class="schedules-header">
                <div class="header-content">
                    <div class="title-section">
                        <h1><i class="fas fa-user-md"></i> Health & Fitness Schedules</h1>
                        <p>Manage health assessments, fitness evaluations and physiotherapy sessions</p>
                    </div>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number">6</span>
                            <span class="stat-label">Today's Assessments</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">18</span>
                            <span class="stat-label">This Week</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">3</span>
                            <span class="stat-label">Health Reports Due</span>
                        </div>
                    </div>
                </div>
                <div class="schedules-controls">
                    <div class="view-toggle">
                        <button class="toggle-btn active" data-view="calendar">
                            <i class="fas fa-calendar"></i> Calendar View
                        </button>
                        <button class="toggle-btn" data-view="list">
                            <i class="fas fa-list"></i> List View
                        </button>
                    </div>
                    <button class="add-session-btn" onclick="openAddSessionModal()">
                        <i class="fas fa-plus"></i> Add Assessment
                    </button>
                </div>
            </div>

            <!-- Schedule Calendar -->
            <div class="schedule-container">
                <div class="schedule-main">
                    <!-- Calendar View -->
                    <div class="schedule-calendar-view active" id="scheduleCalendarView">
                        <div class="calendar-header">
                            <div class="calendar-nav">
                                <button class="nav-btn" id="prevMonth">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h3 id="currentMonth">September 2025</h3>
                                <button class="nav-btn" id="nextMonth">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-legend">
                                <div class="legend-item">
                                    <span class="legend-color training"></span>
                                    <span>Health Assessments</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color physio"></span>
                                    <span>Physio Sessions</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color meeting"></span>
                                    <span>Medical Reviews</span>
                                </div>
                            </div>
                        </div>
                        <div class="calendar-grid" id="scheduleCalendar">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>

                    <!-- List View -->
                    <div class="schedule-list-view" id="scheduleListView">
                        <!-- Today's Schedule -->
                        <div class="schedule-section">
                            <h2><i class="fas fa-calendar-day"></i> Today's Health Schedule</h2>
                            <div class="schedule-grid" id="todaySchedule">
                                <!-- Sample Schedule Items -->
                                <div class="schedule-card">
                                    <div class="schedule-header">
                                        <h4>Player Fitness Assessment</h4>
                                        <span class="priority normal">Routine</span>
                                    </div>
                                    <div class="schedule-details">
                                        <div class="detail-item">
                                            <i class="fas fa-clock"></i>
                                            <span>09:00 AM - 11:00 AM</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Medical Center</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-users"></i>
                                            <span>8 Players</span>
                                        </div>
                                    </div>
                                    <div class="schedule-actions">
                                        <button class="action-btn edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="action-btn notes">
                                            <i class="fas fa-file-medical"></i> Medical Notes
                                        </button>
                                    </div>
                                </div>

                                <div class="schedule-card">
                                    <div class="schedule-header">
                                        <h4>Individual Health Check - Mike Johnson</h4>
                                        <span class="priority high">Urgent</span>
                                    </div>
                                    <div class="schedule-details">
                                        <div class="detail-item">
                                            <i class="fas fa-clock"></i>
                                            <span>02:00 PM - 03:00 PM</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Private Consultation Room</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-user"></i>
                                            <span>Private Session</span>
                                        </div>
                                    </div>
                                    <div class="schedule-actions">
                                        <button class="action-btn edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="action-btn notes">
                                            <i class="fas fa-file-medical"></i> Medical Notes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar with upcoming bookings and notes -->
                <div class="schedule-sidebar">
                    <!-- Upcoming Health Assessments -->
                    <div class="sidebar-section">
                        <h3><i class="fas fa-user-md"></i> Upcoming Health Assessments</h3>
                        <div class="upcoming-bookings">
                            <div class="booking-item">
                                <div class="booking-time">
                                    <span class="time">3:00 PM</span>
                                    <span class="date">Today</span>
                                </div>
                                <div class="booking-info">
                                    <h4>Mike Johnson</h4>
                                    <p>Physio Session</p>
                                </div>
                                <div class="booking-status pending">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>

                            <div class="booking-item">
                                <div class="booking-time">
                                    <span class="time">10:00 AM</span>
                                    <span class="date">Tomorrow</span>
                                </div>
                                <div class="booking-info">
                                    <h4>Sarah Williams</h4>
                                    <p>Fitness Assessment</p>
                                </div>
                                <div class="booking-status confirmed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>

                            <div class="booking-item">
                                <div class="booking-time">
                                    <span class="time">2:30 PM</span>
                                    <span class="date">Sep 11</span>
                                </div>
                                <div class="booking-info">
                                    <h4>Team Health Review</h4>
                                    <p>Medical Consultation</p>
                                </div>
                                <div class="booking-status confirmed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Notes -->
                    <div class="sidebar-section">
                        <h3><i class="fas fa-file-medical"></i> Medical Notes</h3>
                        <div class="trainer-notes">
                            <div class="note-item">
                                <div class="note-header">
                                    <span class="note-date">Sep 10, 2025</span>
                                    <button class="note-edit"><i class="fas fa-edit"></i></button>
                                </div>
                                <p>Player fitness assessment results show improvement in cardiovascular endurance across the team.</p>
                            </div>

                            <div class="note-item">
                                <div class="note-header">
                                    <span class="note-date">Sep 9, 2025</span>
                                    <button class="note-edit"><i class="fas fa-edit"></i></button>
                                </div>
                                <p>Injury update: John Smith's shoulder recovery progressing well. Cleared for light training activities.</p>
                            </div>

                            <div class="note-item">
                                <div class="note-header">
                                    <span class="note-date">Sep 8, 2025</span>
                                    <button class="note-edit"><i class="fas fa-edit"></i></button>
                                </div>
                                <p>Recommended nutrition plan adjustments for 3 players based on recent health screenings.</p>
                            </div>
                        </div>

                        <button class="add-note-btn" onclick="openAddNoteModal()">
                            <i class="fas fa-plus"></i> Add Medical Note
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section id="bookings-section" class="content-section">
            <div class="bookings-header">
                <h1><i class="fas fa-calendar-check"></i> Bookings Management</h1>
                <p>Manage player appointments and sessions</p>
                <div class="bookings-controls">
                    <div class="view-toggle">
                        <button class="toggle-btn active" data-view="list">
                            <i class="fas fa-list"></i> List View
                        </button>
                        <button class="toggle-btn" data-view="calendar">
                            <i class="fas fa-calendar"></i> Calendar View
                        </button>
                    </div>
                    <button class="add-slot-btn" onclick="openAddSlotModal()">
                        <i class="fas fa-plus"></i> Add Time Slot
                    </button>
                </div>
            </div>

            <!-- Booking Filters -->
            <div class="booking-filters">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">All Bookings</button>
                    <button class="filter-btn" data-filter="pending">Pending Approval</button>
                    <button class="filter-btn" data-filter="confirmed">Confirmed</button>
                    <button class="filter-btn" data-filter="completed">Completed</button>
                    <button class="filter-btn" data-filter="cancelled">Cancelled</button>
                </div>
                <div class="date-filter">
                    <input type="date" id="filterDate" class="date-input">
                    <select id="filterPractitioner" class="filter-select">
                        <option value="">All Practitioners</option>
                        <option value="dr-sarah">Dr. Sarah Wilson</option>
                        <option value="coach-mike">Coach Mike Johnson</option>
                        <option value="trainer-alex">Trainer Alex Thompson</option>
                    </select>
                </div>
            </div>

            <!-- Booking Views -->
            <div class="booking-views">
                <!-- List View -->
                <div class="booking-list-view active" id="bookingListView">
                    <!-- Today's Bookings -->
                    <div class="booking-section">
                        <h2><i class="fas fa-calendar-day"></i> Today's Appointments</h2>
                        <div class="booking-grid" id="todayBookings">
                            <!-- Sample Today's Booking -->
                            <div class="booking-card pending" data-booking-id="1">
                                <div class="booking-header">
                                    <div class="booking-time">
                                        <i class="fas fa-clock"></i>
                                        <span>2:00 PM - 3:00 PM</span>
                                    </div>
                                    <div class="booking-status pending">
                                        <i class="fas fa-hourglass-half"></i> Pending Approval
                                    </div>
                                </div>
                                <div class="booking-details">
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="player-details">
                                            <h3>John Smith</h3>
                                            <p>Senior Team • 22 years old</p>
                                            <p><i class="fas fa-phone"></i> +94 77 123 4567</p>
                                        </div>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <i class="fas fa-heartbeat"></i>
                                            <span>Physio Session</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-stethoscope"></i>
                                            <span>Injury Recovery Assessment</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span class="urgency normal">Normal Priority</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-notes">
                                    <strong>Patient Notes:</strong> Shoulder pain after bowling session. Pain scale 6/10, especially during overhead movements.
                                </div>
                                <div class="booking-actions">
                                    <button class="action-btn approve" onclick="approveBooking(1)">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="action-btn reject" onclick="rejectBooking(1)">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <button class="action-btn reschedule" onclick="rescheduleBooking(1)">
                                        <i class="fas fa-calendar-alt"></i> Reschedule
                                    </button>
                                    <button class="action-btn details" onclick="viewBookingDetails(1)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Sample Confirmed Booking -->
                            <div class="booking-card confirmed" data-booking-id="2">
                                <div class="booking-header">
                                    <div class="booking-time">
                                        <i class="fas fa-clock"></i>
                                        <span>4:00 PM - 5:30 PM</span>
                                    </div>
                                    <div class="booking-status confirmed">
                                        <i class="fas fa-check-circle"></i> Confirmed
                                    </div>
                                </div>
                                <div class="booking-details">
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="player-details">
                                            <h3>Emily Johnson</h3>
                                            <p>Junior Team • 18 years old</p>
                                            <p><i class="fas fa-phone"></i> +94 71 987 6543</p>
                                        </div>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <i class="fas fa-dumbbell"></i>
                                            <span>Fitness Assessment</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-chart-line"></i>
                                            <span>Monthly Fitness Evaluation</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-flag"></i>
                                            <span class="urgency normal">Normal Priority</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-notes">
                                    <strong>Notes:</strong> Standard monthly fitness assessment. Focus on cardiovascular endurance and strength improvements.
                                </div>
                                <div class="booking-actions">
                                    <button class="action-btn start" onclick="startSession(2)">
                                        <i class="fas fa-play"></i> Start Session
                                    </button>
                                    <button class="action-btn notes" onclick="addSessionNotes(2)">
                                        <i class="fas fa-note-medical"></i> Add Notes
                                    </button>
                                    <button class="action-btn complete" onclick="completeSession(2)">
                                        <i class="fas fa-check-double"></i> Mark Complete
                                    </button>
                                    <button class="action-btn details" onclick="viewBookingDetails(2)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Bookings -->
                    <div class="booking-section">
                        <h2><i class="fas fa-calendar-week"></i> Upcoming Appointments</h2>
                        <div class="booking-grid" id="upcomingBookings">
                            <!-- Sample Upcoming Booking -->
                            <div class="booking-card confirmed" data-booking-id="3">
                                <div class="booking-header">
                                    <div class="booking-time">
                                        <i class="fas fa-calendar"></i>
                                        <span>Tomorrow, 10:00 AM - 11:00 AM</span>
                                    </div>
                                    <div class="booking-status confirmed">
                                        <i class="fas fa-check-circle"></i> Confirmed
                                    </div>
                                </div>
                                <div class="booking-details">
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="player-details">
                                            <h3>Michael Brown</h3>
                                            <p>Senior Team • 24 years old</p>
                                            <p><i class="fas fa-phone"></i> +94 76 555 1234</p>
                                        </div>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <i class="fas fa-heartbeat"></i>
                                            <span>Physio Session</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-bone"></i>
                                            <span>Knee Injury Follow-up</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-exclamation"></i>
                                            <span class="urgency urgent">Urgent</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-notes">
                                    <strong>Follow-up Notes:</strong> Previous ACL injury, week 4 of recovery. Check mobility and pain levels.
                                </div>
                                <div class="booking-actions">
                                    <button class="action-btn reschedule" onclick="rescheduleBooking(3)">
                                        <i class="fas fa-calendar-alt"></i> Reschedule
                                    </button>
                                    <button class="action-btn cancel" onclick="cancelBooking(3)">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <button class="action-btn details" onclick="viewBookingDetails(3)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Sessions -->
                    <div class="booking-section">
                        <h2><i class="fas fa-history"></i> Recent Completed Sessions</h2>
                        <div class="booking-grid" id="completedBookings">
                            <!-- Sample Completed Booking -->
                            <div class="booking-card completed" data-booking-id="4">
                                <div class="booking-header">
                                    <div class="booking-time">
                                        <i class="fas fa-calendar"></i>
                                        <span>Yesterday, 3:00 PM - 4:00 PM</span>
                                    </div>
                                    <div class="booking-status completed">
                                        <i class="fas fa-check"></i> Completed
                                    </div>
                                </div>
                                <div class="booking-details">
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="player-details">
                                            <h3>Sarah Wilson</h3>
                                            <p>Junior Team • 19 years old</p>
                                            <p><i class="fas fa-phone"></i> +94 75 444 9876</p>
                                        </div>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <i class="fas fa-user-md"></i>
                                            <span>General Consultation</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clipboard-check"></i>
                                            <span>Pre-tournament Health Check</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-star"></i>
                                            <span class="rating">Excellent Session</span>
                                        </div>
                                    </div>
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
                        </div>
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

            <!-- Booking Management Modals -->
            <!-- Add Time Slot Modal -->
            <div class="modal-overlay" id="addSlotModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h2><i class="fas fa-plus"></i> Add Available Time Slot</h2>
                        <button class="close-btn" onclick="closeAddSlotModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="addSlotForm" class="add-slot-form">
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="slotDate">Date:</label>
                                    <input type="date" id="slotDate" name="date" required>
                                </div>
                                <div class="field-group">
                                    <label for="slotType">Session Type:</label>
                                    <select id="slotType" name="type" required>
                                        <option value="">Select session type...</option>
                                        <option value="physio">Physio Session (60 min)</option>
                                        <option value="fitness">Fitness Assessment (90 min)</option>
                                        <option value="consultation">General Consultation (30 min)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="startTime">Start Time:</label>
                                    <input type="time" id="startTime" name="startTime" required>
                                </div>
                                <div class="field-group">
                                    <label for="endTime">End Time:</label>
                                    <input type="time" id="endTime" name="endTime" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="practitioner">Assign to Practitioner:</label>
                                    <select id="practitioner" name="practitioner" required>
                                        <option value="">Select practitioner...</option>
                                        <option value="dr-sarah">Dr. Sarah Wilson (Physio)</option>
                                        <option value="coach-mike">Coach Mike Johnson (Fitness)</option>
                                        <option value="trainer-alex">Trainer Alex Thompson (General)</option>
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label for="location">Location:</label>
                                    <select id="location" name="location" required>
                                        <option value="">Select location...</option>
                                        <option value="physio-room">Physiotherapy Room</option>
                                        <option value="fitness-center">Fitness Center</option>
                                        <option value="consultation-room">Consultation Room</option>
                                        <option value="medical-office">Medical Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label for="slotNotes">Notes (Optional):</label>
                                <textarea id="slotNotes" name="notes" rows="3" 
                                    placeholder="Any special notes about this time slot..."></textarea>
                            </div>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="recurring" name="recurring">
                                    <span class="checkmark"></span>
                                    Make this a recurring slot (weekly)
                                </label>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="closeAddSlotModal()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-plus"></i> Add Time Slot
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Booking Details Modal -->
            <div class="modal-overlay" id="bookingDetailsModal">
                <div class="modal-container large">
                    <div class="modal-header">
                        <h2><i class="fas fa-info-circle"></i> Booking Details</h2>
                        <button class="close-btn" onclick="closeBookingDetailsModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <div id="bookingDetailsContent">
                            <!-- Details will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Session Notes Modal -->
            <div class="modal-overlay" id="sessionNotesModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h2><i class="fas fa-notes-medical"></i> Session Notes</h2>
                        <button class="close-btn" onclick="closeSessionNotesModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="sessionNotesForm" class="session-notes-form">
                            <div class="field-group">
                                <label for="sessionSummary">Session Summary:</label>
                                <textarea id="sessionSummary" name="summary" rows="4" 
                                    placeholder="Brief summary of the session..." required></textarea>
                            </div>
                            <div class="field-group">
                                <label for="findings">Key Findings:</label>
                                <textarea id="findings" name="findings" rows="3" 
                                    placeholder="Important findings or observations..."></textarea>
                            </div>
                            <div class="field-group">
                                <label for="recommendations">Recommendations:</label>
                                <textarea id="recommendations" name="recommendations" rows="3" 
                                    placeholder="Treatment recommendations or next steps..."></textarea>
                            </div>
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="followupRequired">Follow-up Required:</label>
                                    <select id="followupRequired" name="followup">
                                        <option value="none">No follow-up needed</option>
                                        <option value="1week">1 week</option>
                                        <option value="2weeks">2 weeks</option>
                                        <option value="1month">1 month</option>
                                        <option value="custom">Custom timeline</option>
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label for="sessionRating">Session Rating:</label>
                                    <select id="sessionRating" name="rating">
                                        <option value="excellent">Excellent Progress</option>
                                        <option value="good">Good Progress</option>
                                        <option value="fair">Fair Progress</option>
                                        <option value="poor">Needs Attention</option>
                                    </select>
                                </div>
                            </div>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="notifyPlayer" name="notifyPlayer" checked>
                                    <span class="checkmark"></span>
                                    Send session summary to player
                                </label>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="closeSessionNotesModal()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-save"></i> Save Notes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section id="tournaments-section" class="content-section">
            <div class="tournaments-header">
                <h1><i class="fas fa-trophy"></i> Upcoming Tournaments</h1>
                <p>View tournaments posted by admin for all players, coaches, and trainers</p>
            </div>

            <!-- Tournament Cards Grid -->
            <div class="tournaments-grid" id="tournamentsGrid">
                
                <!-- Tournament Card 1 -->
                <div class="tournament-card high-priority" data-filter="upcoming this-month requires-attention">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-championship">Championship</span>
                            <span class="priority-badge urgent">High Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Sept 25, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>National Youth Cricket Championship</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> R. Premadasa Stadium, Colombo</p>
                        <p class="tournament-description">Major national tournament requiring intensive physical preparation and health monitoring.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>Complete fitness assessment</li>
                                <li>Injury screening and clearance</li>
                                <li>Nutritional planning for 3-day event</li>
                                <li>Hydration and recovery protocols</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip needs-assessment">John Smith <i class="fas fa-exclamation"></i></span>
                                <span class="player-chip cleared">Mike Johnson <i class="fas fa-check"></i></span>
                                <span class="player-chip needs-assessment">Sarah Williams <i class="fas fa-exclamation"></i></span>
                                <span class="player-chip cleared">David Brown <i class="fas fa-check"></i></span>
                                <span class="player-chip more">+6 more</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-1')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-1')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>

                <!-- Tournament Card 2 -->
                <div class="tournament-card medium-priority" data-filter="upcoming this-month">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-league">League Match</span>
                            <span class="priority-badge medium">Medium Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Oct 2, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>Inter-School Cricket League</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> Sinhalese Sports Club Ground</p>
                        <p class="tournament-description">Regular league match with standard health protocols.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>Basic fitness check</li>
                                <li>Injury status review</li>
                                <li>Match day nutrition plan</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip cleared">Alex Thompson <i class="fas fa-check"></i></span>
                                <span class="player-chip cleared">Emma Davis <i class="fas fa-check"></i></span>
                                <span class="player-chip needs-assessment">Chris Wilson <i class="fas fa-exclamation"></i></span>
                                <span class="player-chip more">+4 more</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-2')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-2')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>

                <!-- Tournament Card 3 -->
                <div class="tournament-card low-priority" data-filter="upcoming">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-friendly">Friendly Match</span>
                            <span class="priority-badge low">Low Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Oct 15, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>Practice Match vs Royal College</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> Academy Ground</p>
                        <p class="tournament-description">Friendly practice match for skill development.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>General fitness verification</li>
                                <li>Basic hydration guidelines</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip cleared">Tom Anderson <i class="fas fa-check"></i></span>
                                <span class="player-chip cleared">Lisa Martinez <i class="fas fa-check"></i></span>
                                <span class="player-chip more">+8 more</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-3')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-3')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>

                <!-- Tournament Card 4 -->
                <div class="tournament-card high-priority" data-filter="upcoming requires-attention">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-championship">Regional Cup</span>
                            <span class="priority-badge urgent">High Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Nov 5, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>Southern Province Cricket Cup</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> Galle International Stadium</p>
                        <p class="tournament-description">Regional championship requiring comprehensive health preparation.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>Comprehensive medical examination</li>
                                <li>Performance fitness testing</li>
                                <li>Travel health protocols</li>
                                <li>Extended tournament nutrition plan</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip pending">Assessments Pending</span>
                                <span class="player-chip more">15 players total</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-4')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-4')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>
            </div>

            <!-- Health Guidelines Section -->
            <div class="health-guidelines">
                <h2><i class="fas fa-clipboard-list"></i> Tournament Health Guidelines</h2>
                <div class="guidelines-grid">
                    <div class="guideline-card">
                        <h3><i class="fas fa-heart"></i> Pre-Tournament Assessment</h3>
                        <ul>
                            <li>Complete physical examination</li>
                            <li>Cardiovascular fitness evaluation</li>
                            <li>Injury risk assessment</li>
                            <li>Nutrition and hydration planning</li>
                        </ul>
                    </div>
                    <div class="guideline-card">
                        <h3><i class="fas fa-running"></i> During Tournament</h3>
                        <ul>
                            <li>Regular hydration monitoring</li>
                            <li>Fatigue level assessment</li>
                            <li>Immediate injury evaluation</li>
                            <li>Performance nutrition support</li>
                        </ul>
                    </div>
                    <div class="guideline-card">
                        <h3><i class="fas fa-bed"></i> Post-Tournament Recovery</h3>
                        <ul>
                            <li>Recovery assessment</li>
                            <li>Injury evaluation and treatment</li>
                            <li>Nutrition for recovery</li>
                            <li>Rest and rehabilitation planning</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="nutrition-section" class="content-section">
            <div class="nutrition-header">
                <h1>Nutrition & Supplements</h1>
                <div class="nutrition-controls">
                    <div class="filter-tabs">
                        <button class="filter-btn active" data-filter="all">All Plans</button>
                        <button class="filter-btn" data-filter="general">General Plans</button>
                        <button class="filter-btn" data-filter="personalized">Personalized Plans</button>
                        <button class="filter-btn" data-filter="supplements">Supplements</button>
                    </div>
                    <button class="btn-primary" id="createPlanBtn">
                        <i class="fas fa-plus"></i>
                        Create New Plan
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="nutrition-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-details">
                        <h3>12</h3>
                        <p>Active Diet Plans</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="stat-details">
                        <h3>8</h3>
                        <p>Supplement Programs</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h3>45</h3>
                        <p>Players Assigned</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-details">
                        <h3>3</h3>
                        <p>Tournament Plans</p>
                    </div>
                </div>
            </div>

            <!-- Diet Plans Grid -->
            <div class="plans-container">
                <h2>Diet Plans</h2>
                <div class="plans-grid" id="dietPlansGrid">
                    <!-- General Diet Plans -->
                    <div class="plan-card general" data-plan-type="general">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Weight Gain Program</h3>
                                <span class="plan-tag general">General</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">High-calorie diet plan for players looking to gain healthy weight and build muscle mass.</p>
                            <div class="plan-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>6 weeks duration</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-fire"></i>
                                    <span>3200+ calories/day</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>12 players assigned</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-plus"></i>
                                Assign to Players
                            </button>
                            <button class="view-btn">View Details</button>
                        </div>
                    </div>

                    <!-- Personalized Diet Plan -->
                    <div class="plan-card personalized" data-plan-type="personalized">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Recovery Diet - Kamal Silva</h3>
                                <span class="plan-tag personalized">Personalized</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">Post-injury recovery diet with anti-inflammatory foods and enhanced protein intake.</p>
                            <div class="plan-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>4 weeks duration</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-heartbeat"></i>
                                    <span>Recovery focused</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span>Individual plan</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-edit"></i>
                                Modify Assignment
                            </button>
                            <button class="view-btn">View Details</button>
                        </div>
                    </div>

                    <!-- Tournament Preparation -->
                    <div class="plan-card general" data-plan-type="general">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Tournament Prep Diet</h3>
                                <span class="plan-tag tournament">Tournament</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">Performance-optimized nutrition plan for major tournaments and competitions.</p>
                            <div class="plan-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>8 weeks duration</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-bolt"></i>
                                    <span>Performance focused</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>18 players assigned</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-plus"></i>
                                Assign to Players
                            </button>
                            <button class="view-btn">View Details</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplement Plans -->
            <div class="supplements-container">
                <h2>Supplement Programs</h2>
                <div class="plans-grid" id="supplementPlansGrid">
                    <!-- Basic Supplement Plan -->
                    <div class="plan-card supplement" data-plan-type="supplement">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Basic Athletic Support</h3>
                                <span class="plan-tag supplement">Supplement</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">Essential supplements for general athletic performance and recovery.</p>
                            <div class="supplement-schedule">
                                <div class="schedule-item">
                                    <span class="time-label">Morning</span>
                                    <span class="supplement-name">Multivitamin (1 tablet)</span>
                                </div>
                                <div class="schedule-item">
                                    <span class="time-label">Pre-Training</span>
                                    <span class="supplement-name">Creatine (5g)</span>
                                </div>
                                <div class="schedule-item">
                                    <span class="time-label">Post-Training</span>
                                    <span class="supplement-name">Whey Protein (25g)</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-plus"></i>
                                Assign to Players
                            </button>
                            <button class="view-btn">View Schedule</button>
                        </div>
                    </div>

                    <!-- Advanced Supplement Plan -->
                    <div class="plan-card supplement" data-plan-type="supplement">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Performance Enhancement</h3>
                                <span class="plan-tag supplement">Supplement</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">Advanced supplement protocol for competitive athletes and performance optimization.</p>
                            <div class="supplement-schedule">
                                <div class="schedule-item">
                                    <span class="time-label">Morning</span>
                                    <span class="supplement-name">BCAA + Beta-Alanine</span>
                                </div>
                                <div class="schedule-item">
                                    <span class="time-label">Pre-Training</span>
                                    <span class="supplement-name">Pre-workout + Citrulline</span>
                                </div>
                                <div class="schedule-item">
                                    <span class="time-label">Post-Training</span>
                                    <span class="supplement-name">Protein + Glutamine</span>
                                </div>
                                <div class="schedule-item">
                                    <span class="time-label">Evening</span>
                                    <span class="supplement-name">ZMA + Fish Oil</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-plus"></i>
                                Assign to Players
                            </button>
                            <button class="view-btn">View Schedule</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Management -->
            <div class="assignment-container">
                <h2>Plan Assignments</h2>
                <div class="assignment-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Player/Group</th>
                                <th>Diet Plan</th>
                                <th>Supplement Plan</th>
                                <th>Start Date</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">KS</div>
                                        <span>Kamal Silva</span>
                                    </div>
                                </td>
                                <td><span class="plan-badge personalized">Recovery Diet</span></td>
                                <td><span class="plan-badge supplement">Basic Athletic</span></td>
                                <td>Aug 15, 2025</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 65%"></div>
                                        <span class="progress-text">65%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Edit Assignment">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">YG</div>
                                        <span>Youth Group (15 players)</span>
                                    </div>
                                </td>
                                <td><span class="plan-badge general">Weight Gain Program</span></td>
                                <td><span class="plan-badge supplement">Basic Athletic</span></td>
                                <td>Sep 1, 2025</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 30%"></div>
                                        <span class="progress-text">30%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Edit Assignment">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">ST</div>
                                        <span>Senior Team (18 players)</span>
                                    </div>
                                </td>
                                <td><span class="plan-badge tournament">Tournament Prep</span></td>
                                <td><span class="plan-badge supplement">Performance Enhancement</span></td>
                                <td>Aug 1, 2025</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 85%"></div>
                                        <span class="progress-text">85%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Edit Assignment">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="workout-section" class="content-section">
            <div class="exercises-header">
                <h1><i class="fas fa-dumbbell"></i> Health & Fitness</h1>
                <p>Manage player health, fitness routines, and medical-based workout assignments for optimal physical condition</p>
            </div>

            <!-- Exercise & Workout Navigation Tabs -->
            <div class="exercise-tabs">
                <button class="tab-btn active" data-tab="exercise-library">
                    <i class="fas fa-video"></i> Exercise Library
                </button>
                <button class="tab-btn" data-tab="workout-plans">
                    <i class="fas fa-clipboard-list"></i> Workout Plans
                </button>
            </div>

            <!-- Exercise Library Tab -->
            <div id="exercise-library" class="tab-content active">
                <div class="section-header">
                    <div class="header-left">
                        <h2>Health & Fitness Exercise Library</h2>
                        <div class="filter-tabs">
                            <button class="filter-btn active" data-filter="all">All Exercises</button>
                            <button class="filter-btn" data-filter="warmup">Warm-up</button>
                            <button class="filter-btn" data-filter="strength">Strength Training</button>
                            <button class="filter-btn" data-filter="endurance">Endurance</button>
                            <button class="filter-btn" data-filter="recovery">Recovery</button>
                        </div>
                    </div>
                    <button class="add-btn" onclick="openExerciseModal()">
                        <i class="fas fa-plus"></i> Add Exercise Video
                    </button>
                </div>

                <div class="exercises-grid">
                    <!-- Warm-up Exercise Cards -->
                    <div class="exercise-card" data-type="warmup">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">15 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Dynamic Warm-up Routine</h3>
                            <p>General warm-up exercises to prepare muscles and joints for training</p>
                            <div class="exercise-meta">
                                <span class="target-area">Full Body</span>
                                <span class="difficulty easy">Beginner</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(1)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(1)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exercise-card" data-type="strength">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">25 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Core Strength Training</h3>
                            <p>Fundamental core strengthening exercises for injury prevention</p>
                            <div class="exercise-meta">
                                <span class="target-area">Core</span>
                                <span class="difficulty medium">Intermediate</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(2)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(2)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exercise-card" data-type="endurance">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">30 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Cardio Endurance Training</h3>
                            <p>Cardiovascular exercises to improve stamina and heart health</p>
                            <div class="exercise-meta">
                                <span class="target-area">Cardiovascular</span>
                                <span class="difficulty medium">Intermediate</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(3)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(3)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exercise-card" data-type="recovery">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">20 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Stretching & Recovery</h3>
                            <p>Cool-down and flexibility exercises for muscle recovery</p>
                            <div class="exercise-meta">
                                <span class="target-area">Full Body</span>
                                <span class="difficulty easy">Beginner</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(4)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(4)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(4)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workout Plans Tab -->
            <div id="workout-plans" class="tab-content">
                <div class="section-header">
                    <div class="header-left">
                        <h2>Personalized Workout Plans</h2>
                        <div class="filter-tabs">
                            <button class="filter-btn active" data-filter="all">All Plans</button>
                            <button class="filter-btn" data-filter="injury-recovery">Injury Recovery</button>
                            <button class="filter-btn" data-filter="tournament">Tournament Prep</button>
                            <button class="filter-btn" data-filter="team-requirement">Team Requirements</button>
                            <button class="filter-btn" data-filter="individual">Individual Plans</button>
                        </div>
                    </div>
                    <button class="add-btn" onclick="openPersonalizedPlanModal()">
                        <i class="fas fa-plus"></i> Create Personalized Plan
                    </button>
                </div>

                <div class="workout-plans-grid">
                    <!-- Personalized Workout Plan Cards -->
                    <div class="workout-plan-card" data-type="injury-recovery">
                        <div class="plan-header">
                            <h3>Shoulder Injury Recovery Plan</h3>
                            <span class="plan-duration">8 weeks</span>
                        </div>
                        <p class="plan-description">Specialized rehabilitation program for shoulder injuries with gradual progression</p>
                        
                        <!-- Plan Details -->
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type injury-recovery">Injury Recovery</span>
                                <span class="plan-intensity low">Low Intensity</span>
                            </div>
                            
                            <!-- Assigned To -->
                            <div class="assigned-to">
                                <h4><i class="fas fa-user"></i> Assigned To:</h4>
                                <p>Shanali Perera</p>
                            </div>
                            
                            <!-- Included Exercises -->
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Shoulder Mobility Exercises (20 min)</li>
                                    <li>Gentle Strength Training (25 min)</li>
                                    <li>Stretching & Recovery (20 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(1)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(1)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(1)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div class="workout-plan-card" data-type="tournament">
                        <div class="plan-header">
                            <h3>Inter-School Championship Prep</h3>
                            <span class="plan-duration">6 weeks</span>
                        </div>
                        <p class="plan-description">Intensive fitness preparation for upcoming Inter-School Championship tournament</p>
                        
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type tournament">Tournament Prep</span>
                                <span class="plan-intensity high">High Intensity</span>
                            </div>
                            
                            <div class="assigned-to">
                                <h4><i class="fas fa-users"></i> Assigned To:</h4>
                                <p>U-19 Senior Team</p>
                            </div>
                            
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Core Strength Training (25 min)</li>
                                    <li>Cardio Endurance Training (30 min)</li>
                                    <li>Power Training Exercises (20 min)</li>
                                    <li>Cool-down & Recovery (15 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(2)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(2)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(2)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div class="workout-plan-card" data-type="team-requirement">
                        <div class="plan-header">
                            <h3>Bowling Team Strength Program</h3>
                            <span class="plan-duration">4 weeks</span>
                        </div>
                        <p class="plan-description">Specialized strength training program requested by bowling coaching staff</p>
                        
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type team-requirement">Team Requirements</span>
                                <span class="plan-intensity medium">Medium Intensity</span>
                            </div>
                            
                            <div class="assigned-to">
                                <h4><i class="fas fa-layer-group"></i> Assigned To:</h4>
                                <p>Group A - Fast Bowlers</p>
                            </div>
                            
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Core Strength Training (25 min)</li>
                                    <li>Lower Body Power Exercises (30 min)</li>
                                    <li>Shoulder Stability Work (20 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(3)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(3)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(3)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div class="workout-plan-card" data-type="individual">
                        <div class="plan-header">
                            <h3>Personal Fitness Enhancement</h3>
                            <span class="plan-duration">5 weeks</span>
                        </div>
                        <p class="plan-description">Individual fitness improvement plan focusing on overall conditioning and stamina</p>
                        
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type individual">Individual Plans</span>
                                <span class="plan-intensity medium">Medium Intensity</span>
                            </div>
                            
                            <div class="assigned-to">
                                <h4><i class="fas fa-user"></i> Assigned To:</h4>
                                <p>Kasun Rajapaksa</p>
                            </div>
                            
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Cardio Endurance Training (30 min)</li>
                                    <li>Functional Strength Training (25 min)</li>
                                    <li>Flexibility & Mobility (20 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(4)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(4)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(4)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="medical-section" class="content-section">
            <div class="medical-header">
                <h1><i class="fas fa-heartbeat"></i> Player Medical Records</h1>
                <p class="access-notice"><i class="fas fa-eye"></i> Read-Only Access - Physical Trainer View</p>
            </div>

            <!-- Search and Filter Controls -->
            <div class="medical-controls">
                <div class="search-filters">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="playerSearch" placeholder="Search by player name..." class="search-input">
                    </div>
                    <div class="filter-group">
                        <select id="injuryTypeFilter" class="filter-select">
                            <option value="all">All Injury Types</option>
                            <option value="shoulder">Shoulder</option>
                            <option value="knee">Knee</option>
                            <option value="back">Back</option>
                            <option value="ankle">Ankle</option>
                            <option value="hamstring">Hamstring</option>
                            <option value="stress-fracture">Stress Fracture</option>
                        </select>
                        <select id="statusFilter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="in-recovery">In Recovery</option>
                            <option value="cleared">Cleared to Play</option>
                            <option value="restricted">Restricted Activity</option>
                            <option value="monitoring">Under Monitoring</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabbed Interface -->
            <div class="medical-tabs">
                <div class="tab-navigation">
                    <button class="tab-btn active" data-tab="current">Current Injuries</button>
                    <button class="tab-btn" data-tab="history">Medical History</button>
                    <button class="tab-btn" data-tab="treatments">Treatments</button>
                    <button class="tab-btn" data-tab="files">Medical Files</button>
                </div>

                <!-- Current Injuries Tab -->
                <div class="tab-content active" id="current-tab">
                    <div class="medical-grid">
                        <!-- Current Injury Card 1 -->
                        <div class="medical-card current-injury">
                            <div class="medical-card-header">
                                <div class="player-info">
                                    <div class="player-avatar">KS</div>
                                    <div class="player-details">
                                        <h3>Kamal Silva</h3>
                                        <p>Right-hand Batsman | Age: 24</p>
                                        <span class="team-badge">Senior Team</span>
                                    </div>
                                </div>
                                <div class="status-badge in-recovery">In Recovery</div>
                            </div>
                            <div class="medical-card-body">
                                <div class="injury-info">
                                    <h4><i class="fas fa-exclamation-triangle"></i> Hamstring Strain - Grade 2</h4>
                                    <p class="injury-date">Injury Date: August 15, 2025</p>
                                    <p class="injury-description">Acute hamstring strain during sprint training. MRI confirms Grade 2 strain.</p>
                                </div>
                                <div class="treatment-plan">
                                    <h5>Current Treatment:</h5>
                                    <ul>
                                        <li>Rest for 3 weeks (Week 3/3)</li>
                                        <li>Physio sessions: Tuesday & Thursday</li>
                                        <li>Ice therapy: 3x daily</li>
                                        <li>Anti-inflammatory medication</li>
                                    </ul>
                                </div>
                                <div class="restrictions">
                                    <h5>Training Restrictions:</h5>
                                    <p><i class="fas fa-ban"></i> No running or sprinting for 1 more week</p>
                                    <p><i class="fas fa-ban"></i> No batting practice until cleared</p>
                                    <p><i class="fas fa-check"></i> Light stretching and upper body exercises allowed</p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Injury Card 2 -->
                        <div class="medical-card current-injury">
                            <div class="medical-card-header">
                                <div class="player-info">
                                    <div class="player-avatar">AP</div>
                                    <div class="player-details">
                                        <h3>Anjali Perera</h3>
                                        <p>Right-arm Fast Bowler | Age: 22</p>
                                        <span class="team-badge">Women's Team</span>
                                    </div>
                                </div>
                                <div class="status-badge restricted">Restricted Activity</div>
                            </div>
                            <div class="medical-card-body">
                                <div class="injury-info">
                                    <h4><i class="fas fa-exclamation-triangle"></i> Shoulder Impingement</h4>
                                    <p class="injury-date">Injury Date: September 1, 2025</p>
                                    <p class="injury-description">Mild shoulder impingement from repetitive bowling action. Early intervention.</p>
                                </div>
                                <div class="treatment-plan">
                                    <h5>Current Treatment:</h5>
                                    <ul>
                                        <li>Modified training regime</li>
                                        <li>Shoulder strengthening exercises</li>
                                        <li>Biomechanics assessment scheduled</li>
                                        <li>Weekly physio check-ups</li>
                                    </ul>
                                </div>
                                <div class="restrictions">
                                    <h5>Training Restrictions:</h5>
                                    <p><i class="fas fa-ban"></i> No bowling for 2 weeks</p>
                                    <p><i class="fas fa-check"></i> Batting practice allowed</p>
                                    <p><i class="fas fa-check"></i> Fielding practice (no throwing)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cleared Player Card -->
                        <div class="medical-card current-injury">
                            <div class="medical-card-header">
                                <div class="player-info">
                                    <div class="player-avatar">DF</div>
                                    <div class="player-details">
                                        <h3>Dasun Fernando</h3>
                                        <p>Wicket Keeper | Age: 26</p>
                                        <span class="team-badge">Senior Team</span>
                                    </div>
                                </div>
                                <div class="status-badge cleared">Cleared to Play</div>
                            </div>
                            <div class="medical-card-body">
                                <div class="injury-info">
                                    <h4><i class="fas fa-check-circle"></i> Recent Knee Injury - Fully Recovered</h4>
                                    <p class="injury-date">Cleared Date: September 5, 2025</p>
                                    <p class="injury-description">Minor knee strain fully recovered. Cleared for all activities.</p>
                                </div>
                                <div class="treatment-plan">
                                    <h5>Maintenance Plan:</h5>
                                    <ul>
                                        <li>Continue knee strengthening exercises</li>
                                        <li>Monitor for any discomfort</li>
                                        <li>Warm-up properly before training</li>
                                    </ul>
                                </div>
                                <div class="restrictions">
                                    <h5>Current Status:</h5>
                                    <p><i class="fas fa-check"></i> Full training participation</p>
                                    <p><i class="fas fa-check"></i> Match available</p>
                                    <p><i class="fas fa-info-circle"></i> Continue monitoring exercises</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical History Tab -->
                <div class="tab-content" id="history-tab">
                    <div class="history-timeline">
                        <div class="timeline-item">
                            <div class="timeline-date">2024</div>
                            <div class="timeline-content">
                                <h4>Kamal Silva - Previous Injuries</h4>
                                <ul>
                                    <li>Ankle sprain (March 2024) - Fully recovered</li>
                                    <li>Lower back strain (June 2024) - Resolved with physio</li>
                                </ul>
                                <p><strong>Allergies:</strong> None reported</p>
                                <p><strong>Chronic Issues:</strong> Mild asthma (well controlled)</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-date">2023</div>
                            <div class="timeline-content">
                                <h4>Anjali Perera - Previous Injuries</h4>
                                <ul>
                                    <li>Finger fracture (August 2023) - Healed completely</li>
                                    <li>Mild concussion (October 2023) - Full recovery</li>
                                </ul>
                                <p><strong>Allergies:</strong> Penicillin allergy</p>
                                <p><strong>Chronic Issues:</strong> None</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Treatments Tab -->
                <div class="tab-content" id="treatments-tab">
                    <div class="treatment-schedule">
                        <h3>Ongoing Treatment Schedule</h3>
                        <div class="schedule-grid">
                            <div class="schedule-card">
                                <div class="schedule-header">
                                    <h4>Kamal Silva - Hamstring Recovery</h4>
                                    <span class="priority high">High Priority</span>
                                </div>
                                <div class="schedule-details">
                                    <p><strong>Next Session:</strong> September 10, 2025 - 2:00 PM</p>
                                    <p><strong>Therapist:</strong> Dr. Sarah Johnson</p>
                                    <p><strong>Treatment Type:</strong> Deep tissue massage + exercises</p>
                                    <p><strong>Duration:</strong> 45 minutes</p>
                                </div>
                            </div>
                            <div class="schedule-card">
                                <div class="schedule-header">
                                    <h4>Anjali Perera - Shoulder Therapy</h4>
                                    <span class="priority medium">Medium Priority</span>
                                </div>
                                <div class="schedule-details">
                                    <p><strong>Next Session:</strong> September 12, 2025 - 10:00 AM</p>
                                    <p><strong>Therapist:</strong> Dr. Mike Chen</p>
                                    <p><strong>Treatment Type:</strong> Shoulder strengthening</p>
                                    <p><strong>Duration:</strong> 30 minutes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Files Tab -->
                <div class="tab-content" id="files-tab">
                    <div class="files-grid">
                        <div class="file-card">
                            <div class="file-icon">
                                <i class="fas fa-file-medical"></i>
                            </div>
                            <div class="file-info">
                                <h4>Kamal Silva - MRI Report</h4>
                                <p>Hamstring strain assessment</p>
                                <span class="file-date">August 16, 2025</span>
                            </div>
                            <div class="file-actions">
                                <button class="btn-view" onclick="viewMedicalFile('kamal-mri-001')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                        <div class="file-card">
                            <div class="file-icon">
                                <i class="fas fa-x-ray"></i>
                            </div>
                            <div class="file-info">
                                <h4>Anjali Perera - X-Ray</h4>
                                <p>Shoulder joint assessment</p>
                                <span class="file-date">September 2, 2025</span>
                            </div>
                            <div class="file-actions">
                                <button class="btn-view" onclick="viewMedicalFile('anjali-xray-001')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                        <div class="file-card">
                            <div class="file-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="file-info">
                                <h4>Dasun Fernando - Physio Report</h4>
                                <p>Knee recovery assessment</p>
                                <span class="file-date">September 5, 2025</span>
                            </div>
                            <div class="file-actions">
                                <button class="btn-view" onclick="viewMedicalFile('dasun-physio-001')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Records Message (Hidden by default) -->
            <div class="no-records-message" style="display: none;">
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No medical records available</h3>
                    <p>No medical records found for the selected criteria.</p>
                </div>
            </div>
        </section>
    </main>
    </div> <!-- End dashboard-main-content -->
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
