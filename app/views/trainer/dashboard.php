<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css">

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Trainer Dashboard</h3>
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John Trainer'; ?></h4>
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
                        <span>Workout Recommendations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#medical" class="nav-link" data-section="medical">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical Records</span>
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
        <!-- Dashboard Overview -->
        <section id="dashboard-section" class="content-section active">
            <div class="dashboard-header">
                <h1>Welcome back, John!</h1>
                <p>Here's what's happening with your training schedule today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3>8</h3>
                        <p>Today's Sessions</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24</h3>
                        <p>Active Players</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>3</h3>
                        <p>Private Sessions</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <h3>2</h3>
                        <p>Upcoming Tournaments</p>
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
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <button class="action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Session</span>
                    </button>
                    <button class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>New Player</span>
                    </button>
                    <button class="action-btn">
                        <i class="fas fa-file-medical"></i>
                        <span>Medical Report</span>
                    </button>
                    <button class="action-btn">
                        <i class="fas fa-dumbbell"></i>
                        <span>Workout Plan</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Other sections will be added for schedules, bookings, etc. -->
        <section id="schedules-section" class="content-section">
            <h1>Schedules Management</h1>
            <p>Schedule management features coming soon...</p>
        </section>

        <section id="bookings-section" class="content-section">
            <h1>Bookings Management</h1>
            <p>Booking management features coming soon...</p>
        </section>

        <section id="tournaments-section" class="content-section">
            <h1>Tournaments</h1>
            <p>Tournament management features coming soon...</p>
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
            <h1>Workout Recommendations</h1>
            <p>Workout recommendation features coming soon...</p>
        </section>

        <section id="medical-section" class="content-section">
            <h1>Medical Records</h1>
            <p>Medical records management features coming soon...</p>
        </section>
    </main>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
