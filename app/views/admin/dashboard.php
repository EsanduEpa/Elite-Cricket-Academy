<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Include Header -->
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>

    <!-- Admin Dashboard Layout -->
    <div class="admin-layout">
        <!-- Left Sidebar Panel -->
        <div class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <i class="fas fa-user-shield"></i>
                    <h3>Admin Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard Overview</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#staff-management" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>Staff Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#player-management" class="nav-link">
                            <i class="fas fa-user-graduate"></i>
                            <span>Player Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Events & Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#feedback-monitoring" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Feedback Monitoring</span>
                            <span class="badge">12</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Finance Management</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Admin Profile -->
            <div class="admin-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                    <span class="admin-name">Admin User</span>
                    <span class="admin-role">Super Administrator</span>
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
                    <h1><i class="fas fa-tachometer-alt"></i> Elite Cricket Academy - Admin Dashboard</h1>
                    <p>Comprehensive management system for academy operations</p>
                </div>
                <div class="header-actions">
                    <button class="refresh-btn" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <div class="current-time" id="currentTime"></div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card staff-card">
                    <div class="card-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="card-content">
                        <h3>Staff Management</h3>
                        <div class="stats">
                            <div class="stat-item">
                                <span class="number">24</span>
                                <span class="label">Total Staff</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">12</span>
                                <span class="label">Coaches</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">6</span>
                                <span class="label">Trainers</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="staffChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="summary-card player-card">
                    <div class="card-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="card-content">
                        <h3>Player Management</h3>
                        <div class="stats">
                            <div class="stat-item">
                                <span class="number">156</span>
                                <span class="label">Active Players</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">89%</span>
                                <span class="label">Attendance</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">23</span>
                                <span class="label">New Joinings</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="playerChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="summary-card events-card">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3>Events & Tournaments</h3>
                        <div class="stats">
                            <div class="stat-item">
                                <span class="number">8</span>
                                <span class="label">Upcoming Events</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">3</span>
                                <span class="label">Tournaments</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">15</span>
                                <span class="label">This Month</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="eventsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="summary-card feedback-card">
                    <div class="card-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="card-content">
                        <h3>Feedback Monitoring</h3>
                        <div class="stats">
                            <div class="stat-item">
                                <span class="number">12</span>
                                <span class="label">Pending Reviews</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">4.7</span>
                                <span class="label">Avg Rating</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">98%</span>
                                <span class="label">Satisfaction</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="feedbackChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="summary-card finance-card">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <h3>Finance Management</h3>
                        <div class="stats">
                            <div class="stat-item">
                                <span class="number">$45,680</span>
                                <span class="label">Monthly Revenue</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">92%</span>
                                <span class="label">Collection Rate</span>
                            </div>
                            <div class="stat-item">
                                <span class="number">$8,250</span>
                                <span class="label">Outstanding</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics Section -->
            <div class="analytics-section">
                <!-- Revenue & Performance Charts -->
                <div class="analytics-row">
                    <div class="chart-card large">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-area"></i> Monthly Revenue Trend</h3>
                            <div class="chart-controls">
                                <select id="revenueFilter">
                                    <option value="6">Last 6 Months</option>
                                    <option value="12" selected>Last 12 Months</option>
                                    <option value="24">Last 24 Months</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-content">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card medium">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i> Student Distribution</h3>
                        </div>
                        <div class="chart-content">
                            <canvas id="studentDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Activity & Performance Metrics -->
                <div class="analytics-row">
                    <div class="chart-card medium">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-bar"></i> Training Attendance</h3>
                        </div>
                        <div class="chart-content">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card medium">
                        <div class="chart-header">
                            <h3><i class="fas fa-trophy"></i> Performance Metrics</h3>
                        </div>
                        <div class="chart-content">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="activity-section">
                <div class="activity-card">
                    <div class="activity-header">
                        <h3><i class="fas fa-history"></i> Recent Activities</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon registration">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>New player registered:</strong> Sarah Johnson (Age 14)</p>
                                <small>2 hours ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon event">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Tournament scheduled:</strong> Junior Championship 2025</p>
                                <small>4 hours ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon feedback">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>5-star feedback received</strong> from parent of Alex Kumar</p>
                                <small>1 day ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon payment">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Payment received:</strong> $450 from Emma Wilson</p>
                                <small>1 day ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon staff">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>New coach hired:</strong> Michael Roberts (Former State Player)</p>
                                <small>2 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="quick-actions-card">
                    <div class="actions-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <button class="action-btn primary" onclick="openModal('addPlayer')">
                            <i class="fas fa-user-plus"></i>
                            <span>Add New Player</span>
                        </button>
                        <button class="action-btn secondary" onclick="openModal('scheduleEvent')">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Schedule Event</span>
                        </button>
                        <button class="action-btn success" onclick="openModal('generateReport')">
                            <i class="fas fa-file-alt"></i>
                            <span>Generate Report</span>
                        </button>
                        <button class="action-btn warning" onclick="openModal('sendNotification')">
                            <i class="fas fa-bell"></i>
                            <span>Send Notification</span>
                        </button>
                        <button class="action-btn info" onclick="openModal('manageStaff')">
                            <i class="fas fa-users-cog"></i>
                            <span>Manage Staff</span>
                        </button>
                        <button class="action-btn danger" onclick="openModal('reviewFeedback')">
                            <i class="fas fa-comments"></i>
                            <span>Review Feedback</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/admin/dashboard.js"></script>
</body>

</html>
