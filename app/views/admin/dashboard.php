<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/tournaments.css">

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
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?></div>
                <div class="profile-role">Super Administrator</div>
                <a href="<?php echo URLROOT; ?>/admin/profile" class="action-btn" style="margin-top: 10px;">
                    <i class="fas fa-user-cog"></i> Profile
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
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
<br><br>
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

            <!-- Calendar Section -->
            <?php 
            $calendarTitle = 'Academy Calendar';
            $calendarIcon = 'fas fa-calendar-alt';
            $calendarId = 'adminCalendar';
            include APPROOT . '/views/inc/components/calendar.php'; 
            ?>

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Ensure Chart.js is loaded before proceeding
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');
        window.chartJsLoaded = typeof Chart !== 'undefined';
        console.log('URLROOT for JS files: <?php echo URLROOT; ?>');
    </script>
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/common/tournaments.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/dashboard.js"></script>
    
    <!-- Chart.js Test -->
    <script>
        console.log('Chart.js test script running...');
        console.log('Chart available:', typeof Chart);
        
        // Test if we can find the canvas elements
        setTimeout(function() {
            const staffCanvas = document.getElementById('staffChart');
            console.log('Staff canvas found:', !!staffCanvas);
            if (staffCanvas) {
                console.log('Staff canvas dimensions:', staffCanvas.width, 'x', staffCanvas.height);
                console.log('Staff canvas parent:', staffCanvas.parentElement);
            }
        }, 1000);
    </script>
</body>

</html>
