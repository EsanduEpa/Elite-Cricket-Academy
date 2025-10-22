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
                        <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>Staff Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
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
                        <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Feedback Monitoring</span>
                            <span class="badge">12</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
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
            <!-- Summary Cards - 4 in a Row with Charts -->
            <div class="summary-cards">
                <div class="summary-card staff-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="card-info">
                            <span class="number">24</span>
                            <span class="label">Total Staff</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="staffChart"></canvas>
                    </div>
                </div>

                <div class="summary-card events-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="card-info">
                            <span class="number">8</span>
                            <span class="label">Upcoming Events</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>

                <div class="summary-card feedback-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="card-info">
                            <span class="number">12</span>
                            <span class="label">Pending Reviews</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="feedbackChart"></canvas>
                    </div>
                </div>

                <div class="summary-card finance-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="card-info">
                            <span class="number">RS 100,000</span>
                            <span class="label">Monthly Revenue</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Academy Calendar - Custom JS Implementation -->
            <div class="calendar-section">
                <div class="calendar-header">
                    <h3><i class="fas fa-calendar-alt"></i> Academy Calendar</h3>
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
                    <div class="legend-item"><span class="legend-dot event"></span> Events</div>
                    <div class="legend-item"><span class="legend-dot coaching"></span> Coaching Sessions</div>
                    <div class="legend-item"><span class="legend-dot tournament"></span> Tournaments</div>
                    <div class="legend-item"><span class="legend-dot meeting"></span> Meetings</div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="recent-activity-section">
                <div class="section-header">
                    <h3><i class="fas fa-history"></i> Recent Activities</h3>
                    <div class="activity-filters">
                        <select id="activityTypeFilter" class="filter-select">
                            <option value="all">All Activities</option>
                            <option value="registration">Registration</option>
                            <option value="event">Events</option>
                            <option value="feedback">Feedback</option>
                            <option value="payment">Payments</option>
                            <option value="staff">Staff</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <select id="activityTimeFilter" class="filter-select">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                        <button class="btn-filter-clear" onclick="clearActivityFilters()">
                            <i class="fas fa-redo"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Activity Type</th>
                                <th>Description</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="activityTableBody">
                            <tr>
                                <td><span class="activity-badge registration"><i class="fas fa-user-plus"></i> Registration</span></td>
                                <td>New player registered: Sarah Johnson (Age 14)</td>
                                <td>Oct 19, 2025 - 10:30 AM</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(1)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge event"><i class="fas fa-calendar-plus"></i> Event</span></td>
                                <td>Tournament scheduled: Junior Championship 2025</td>
                                <td>Oct 19, 2025 - 08:15 AM</td>
                                <td><span class="status-badge scheduled">Scheduled</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(2)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge feedback"><i class="fas fa-star"></i> Feedback</span></td>
                                <td>5-star feedback received from parent of Alex Kumar</td>
                                <td>Oct 18, 2025 - 04:45 PM</td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(3)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge payment"><i class="fas fa-credit-card"></i> Payment</span></td>
                                <td>Payment received: $450 from Emma Wilson</td>
                                <td>Oct 18, 2025 - 02:20 PM</td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(4)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge staff"><i class="fas fa-user-tie"></i> Staff</span></td>
                                <td>New coach hired: Michael Roberts (Former State Player)</td>
                                <td>Oct 17, 2025 - 09:00 AM</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(5)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge training"><i class="fas fa-dumbbell"></i> Training</span></td>
                                <td>Advanced batting session completed: 15 participants</td>
                                <td>Oct 16, 2025 - 05:30 PM</td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(6)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge maintenance"><i class="fas fa-tools"></i> Maintenance</span></td>
                                <td>Equipment maintenance completed for Ground A</td>
                                <td>Oct 16, 2025 - 11:00 AM</td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(7)">Show More</button></td>
                            </tr>
                            <tr>
                                <td><span class="activity-badge registration"><i class="fas fa-user-plus"></i> Registration</span></td>
                                <td>New player registered: David Chen (Age 12)</td>
                                <td>Oct 15, 2025 - 03:15 PM</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td><button class="btn-show-more" onclick="showActivityDetails(8)">Show More</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions - 3 Buttons Per Row, 2 Rows -->
            <div class="quick-actions-section">
                <div class="section-header">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="quick-actions-grid">
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
    
    <!-- Activity Filters JavaScript -->
    <script>
        // Activity Filter Functions
        function filterActivities() {
            const typeFilter = document.getElementById('activityTypeFilter').value;
            const timeFilter = document.getElementById('activityTimeFilter').value;
            const rows = document.querySelectorAll('#activityTableBody tr');
            
            rows.forEach(row => {
                let showRow = true;
                
                // Type filter
                if (typeFilter !== 'all') {
                    const activityType = row.querySelector('.activity-badge').textContent.trim().toLowerCase();
                    if (!activityType.includes(typeFilter.toLowerCase())) {
                        showRow = false;
                    }
                }
                
                // Time filter (simplified - would need actual dates in production)
                if (timeFilter !== 'all' && showRow) {
                    const dateText = row.cells[2].textContent;
                    const today = new Date();
                    
                    if (timeFilter === 'today' && !dateText.includes('Oct 19')) {
                        showRow = false;
                    } else if (timeFilter === 'week' && !dateText.includes('Oct 1')) {
                        showRow = false;
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }
        
        function clearActivityFilters() {
            document.getElementById('activityTypeFilter').value = 'all';
            document.getElementById('activityTimeFilter').value = 'all';
            filterActivities();
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const typeFilter = document.getElementById('activityTypeFilter');
            const timeFilter = document.getElementById('activityTimeFilter');
            
            if (typeFilter) typeFilter.addEventListener('change', filterActivities);
            if (timeFilter) timeFilter.addEventListener('change', filterActivities);
        });
    </script>
    
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
