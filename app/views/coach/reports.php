<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-reports.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/schedules" class="nav-link" data-tooltip="Schedules">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedules</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                            <i class="fas fa-comments"></i>
                            <span>Communication</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/reports" class="nav-link" data-tooltip="Reports">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Requests</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1>
                            <i class="fas fa-chart-bar"></i>
                            Reports & Analytics
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Performance insights and data visualization</p>
                    </div>
                    <div class="header-actions">
                        <select id="reportPeriod" class="period-select">
                            <option value="week">Last 7 Days</option>
                            <option value="month" selected>Last 30 Days</option>
                            <option value="quarter">Last 3 Months</option>
                            <option value="year">Last Year</option>
                        </select>
                        <button class="btn-primary" id="exportReportBtn">
                            <i class="fas fa-download"></i>
                            Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reports Content -->
            <div class="reports-content">
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="totalPlayers">24</h3>
                            <p>Active Players</p>
                            <span class="trend positive">
                                <i class="fas fa-arrow-up"></i> 12% vs last month
                            </span>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="totalSessions">48</h3>
                            <p>Sessions Completed</p>
                            <span class="trend positive">
                                <i class="fas fa-arrow-up"></i> 8% vs last month
                            </span>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="avgAttendance">87%</h3>
                            <p>Avg Attendance</p>
                            <span class="trend negative">
                                <i class="fas fa-arrow-down"></i> 3% vs last month
                            </span>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="healthScore">92%</h3>
                            <p>Health Score</p>
                            <span class="trend positive">
                                <i class="fas fa-arrow-up"></i> 5% vs last month
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i> Player Performance Trends</h3>
                            <select id="performanceMetric" class="metric-select">
                                <option value="batting">Batting Average</option>
                                <option value="bowling">Bowling Average</option>
                                <option value="fielding">Fielding Score</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-fire"></i> Attendance Heatmap</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 2 -->
                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-heartbeat"></i> Health Summary</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="healthChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-calendar-alt"></i> Session Distribution</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="sessionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Performance Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fas fa-table"></i> Top Performers</h3>
                        <div class="table-actions">
                            <input type="text" id="searchPlayers" placeholder="Search players..." class="search-input">
                            <button class="btn-secondary" id="exportTableBtn">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="table-container">
                        <table id="performersTable">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Player Name</th>
                                    <th>Attendance</th>
                                    <th>Avg Performance</th>
                                    <th>Improvement</th>
                                    <th>Health Status</th>
                                </tr>
                            </thead>
                            <tbody id="performersTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal" id="exportModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-download"></i> Export Report</h2>
                <button class="modal-close" id="closeExportModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="form-group">
                        <label>Report Type *</label>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" name="reportType" value="performance" checked>
                                Performance Analysis
                            </label>
                            <label>
                                <input type="checkbox" name="reportType" value="attendance">
                                Attendance Records
                            </label>
                            <label>
                                <input type="checkbox" name="reportType" value="health">
                                Health Summary
                            </label>
                            <label>
                                <input type="checkbox" name="reportType" value="sessions">
                                Session Statistics
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="exportFormat">Export Format *</label>
                        <select id="exportFormat" required>
                            <option value="pdf">PDF Document</option>
                            <option value="csv">CSV Spreadsheet</option>
                            <option value="excel">Excel Workbook</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="exportPeriod">Time Period *</label>
                        <select id="exportPeriod" required>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="quarter">Last 3 Months</option>
                            <option value="year">Last Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="includeCharts">
                            Include charts and visualizations
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelExport">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-download"></i>
                            Generate & Download
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach-reports.js"></script>

<script>
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
