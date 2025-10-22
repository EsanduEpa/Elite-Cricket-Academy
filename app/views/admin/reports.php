<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/reports.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <li class="nav-item">
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
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Finance Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
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
                    <div class="header-text">
                        <h1><i class="fas fa-file-alt"></i> Reports & Analytics</h1>
                        <p>Generate comprehensive reports on performance, revenue, and events</p>
                    </div>
                    <div class="header-actions">
                        <div class="current-time" id="currentTime"></div>
                    </div>
                </div>
            </div>

            <!-- Reports Content -->
            <div class="content-wrapper">
                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>48</h3>
                            <p>Reports Generated</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #06d6a0 0%, #118ab2 100%);">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="stat-info">
                            <h3>156</h3>
                            <p>Total Downloads</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="stat-info">
                            <h3>12</h3>
                            <p>This Month</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Today</h3>
                            <p>Last Generated</p>
                        </div>
                    </div>
                </div>

                <!-- Report Generator Section -->
                <div class="report-generator">
                    <div class="section-header">
                        <h2><i class="fas fa-plus-circle"></i> Generate New Report</h2>
                    </div>
                    
                    <div class="generator-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="reportType">
                                    <i class="fas fa-file-alt"></i> Report Type
                                </label>
                                <select id="reportType" class="filter-select">
                                    <option value="">Select Report Type</option>
                                    <option value="player">Player Performance Report</option>
                                    <option value="revenue">Revenue & Finance Report</option>
                                    <option value="event">Event & Tournament Report</option>
                                    <option value="attendance">Attendance Report</option>
                                    <option value="subscription">Subscription Report</option>
                                    <option value="comprehensive">Comprehensive Report</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="reportPeriod">
                                    <i class="fas fa-calendar-alt"></i> Time Period
                                </label>
                                <select id="reportPeriod" class="filter-select">
                                    <option value="week">Last Week</option>
                                    <option value="month" selected>Last Month</option>
                                    <option value="quarter">Last Quarter</option>
                                    <option value="year">Last Year</option>
                                    <option value="custom">Custom Date Range</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="customDateGroup" style="display: none;">
                                <label for="startDate">
                                    <i class="fas fa-calendar"></i> Start Date
                                </label>
                                <input type="date" id="startDate" class="form-control">
                            </div>
                            
                            <div class="form-group" id="customDateGroup2" style="display: none;">
                                <label for="endDate">
                                    <i class="fas fa-calendar"></i> End Date
                                </label>
                                <input type="date" id="endDate" class="form-control">
                            </div>
                        </div>
                        
                        <!-- Player Report Options -->
                        <div class="report-options" id="playerOptions" style="display: none;">
                            <h4>Player Report Options</h4>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Include Batting Statistics
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Include Bowling Statistics
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Include Fielding Statistics
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Include Training Attendance
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Include Performance Graphs
                                </label>
                            </div>
                        </div>
                        
                        <!-- Revenue Report Options -->
                        <div class="report-options" id="revenueOptions" style="display: none;">
                            <h4>Revenue Report Options</h4>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Subscription Revenue
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Event Registration Fees
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Equipment Sales
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Facility Rentals
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Revenue Comparison Charts
                                </label>
                            </div>
                        </div>
                        
                        <!-- Event Report Options -->
                        <div class="report-options" id="eventOptions" style="display: none;">
                            <h4>Event Report Options</h4>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Event Participation Stats
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Registration Details
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Event Revenue
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Attendance Records
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Success Metrics
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="reportFormat">
                                    <i class="fas fa-file-export"></i> Export Format
                                </label>
                                <select id="reportFormat" class="form-control">
                                    <option value="pdf">PDF Document</option>
                                    <option value="excel">Excel Spreadsheet</option>
                                    <option value="csv">CSV File</option>
                                    <option value="html">HTML Page</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="btn btn-primary btn-generate" onclick="generateReport()">
                                    <i class="fas fa-file-export"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Templates -->
                <div class="report-templates">
                    <div class="section-header">
                        <h2><i class="fas fa-layer-group"></i> Quick Report Templates</h2>
                    </div>
                    
                    <div class="templates-grid">
                        <div class="template-card" onclick="generateTemplateReport('monthly-performance')">
                            <div class="template-icon purple">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Monthly Performance</h3>
                            <p>Overall academy performance for the current month</p>
                            <button class="btn-template">
                                <i class="fas fa-download"></i> Generate
                            </button>
                        </div>
                        
                        <div class="template-card" onclick="generateTemplateReport('player-rankings')">
                            <div class="template-icon blue">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3>Player Rankings</h3>
                            <p>Top performers based on statistics</p>
                            <button class="btn-template">
                                <i class="fas fa-download"></i> Generate
                            </button>
                        </div>
                        
                        <div class="template-card" onclick="generateTemplateReport('revenue-summary')">
                            <div class="template-icon green">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3>Revenue Summary</h3>
                            <p>Financial overview and revenue breakdown</p>
                            <button class="btn-template">
                                <i class="fas fa-download"></i> Generate
                            </button>
                        </div>
                        
                        <div class="template-card" onclick="generateTemplateReport('event-summary')">
                            <div class="template-icon orange">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h3>Event Summary</h3>
                            <p>Completed and upcoming events report</p>
                            <button class="btn-template">
                                <i class="fas fa-download"></i> Generate
                            </button>
                        </div>
                        
                        <div class="template-card" onclick="generateTemplateReport('attendance-report')">
                            <div class="template-icon pink">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h3>Attendance Report</h3>
                            <p>Training and match attendance records</p>
                            <button class="btn-template">
                                <i class="fas fa-download"></i> Generate
                            </button>
                        </div>
                        
                        <div class="template-card" onclick="generateTemplateReport('comprehensive')">
                            <div class="template-icon indigo">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h3>Comprehensive Report</h3>
                            <p>Complete academy overview with all metrics</p>
                            <button class="btn-template">
                                <i class="fas fa-download"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="recent-reports">
                    <div class="section-header">
                        <h2><i class="fas fa-history"></i> Recent Reports</h2>
                        <button class="btn-secondary" onclick="viewAllReports()">
                            <i class="fas fa-list"></i> View All
                        </button>
                    </div>
                    
                    <div class="reports-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Report Name</th>
                                    <th>Type</th>
                                    <th>Period</th>
                                    <th>Generated</th>
                                    <th>Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="report-name">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>Player Performance October 2025</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-player">Player Report</span></td>
                                    <td>Oct 1 - Oct 22, 2025</td>
                                    <td>Today, 10:30 AM</td>
                                    <td>2.4 MB</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewReport(1)" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-download" onclick="downloadReport(1)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn-action btn-share" onclick="shareReport(1)" title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <div class="report-name">
                                            <i class="fas fa-file-excel"></i>
                                            <span>Revenue Report Q3 2025</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-revenue">Revenue Report</span></td>
                                    <td>Jul - Sep 2025</td>
                                    <td>Oct 20, 2025</td>
                                    <td>1.8 MB</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewReport(2)" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-download" onclick="downloadReport(2)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn-action btn-share" onclick="shareReport(2)" title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <div class="report-name">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>Event Participation Report</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-event">Event Report</span></td>
                                    <td>Sep 2025</td>
                                    <td>Oct 15, 2025</td>
                                    <td>3.1 MB</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewReport(3)" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-download" onclick="downloadReport(3)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn-action btn-share" onclick="shareReport(3)" title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <div class="report-name">
                                            <i class="fas fa-file-alt"></i>
                                            <span>Training Attendance September</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-attendance">Attendance</span></td>
                                    <td>Sep 1 - Sep 30, 2025</td>
                                    <td>Oct 10, 2025</td>
                                    <td>856 KB</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewReport(4)" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-download" onclick="downloadReport(4)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn-action btn-share" onclick="shareReport(4)" title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <div class="report-name">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>Comprehensive Academy Report</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-comprehensive">Comprehensive</span></td>
                                    <td>Aug 2025</td>
                                    <td>Sep 5, 2025</td>
                                    <td>5.2 MB</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewReport(5)" title="View Report">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-download" onclick="downloadReport(5)" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn-action btn-share" onclick="shareReport(5)" title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Report Analytics -->
                <div class="report-analytics">
                    <div class="section-header">
                        <h2><i class="fas fa-chart-bar"></i> Report Analytics</h2>
                    </div>
                    
                    <div class="analytics-grid">
                        <div class="analytics-chart">
                            <h3>Reports Generated by Type</h3>
                            <canvas id="reportTypeChart"></canvas>
                        </div>
                        
                        <div class="analytics-chart">
                            <h3>Report Generation Trend</h3>
                            <canvas id="reportTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal" id="loadingModal" style="display: none;">
        <div class="modal-overlay"></div>
        <div class="modal-content modal-loading">
            <div class="loading-animation">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h3>Generating Report...</h3>
            <p>Please wait while we compile your report</p>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <p class="progress-text" id="progressText">0%</p>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/admin/reports.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
