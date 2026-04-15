<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-reports.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/reports.js"></script>

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
                        <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Slot Sessions</span>
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
                        <p class="coach-reports-subtitle">Performance insights and data visualization</p>
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
                        <div class="card-icon card-icon--players">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="totalPlayers"><?php echo $data['totalPlayers']; ?></h3>
                            <p>Assigned Players</p>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-icon card-icon--sessions">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="totalSessions"><?php echo $data['totalSessions']; ?></h3>
                            <p>Total Sessions</p>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-icon card-icon--attendance">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="avgAttendance"><?php echo $data['activeSessions']; ?></h3>
                            <p>Active Sessions</p>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-icon card-icon--medical">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="card-info">
                            <h3 id="healthScore"><?php echo $data['totalMedical']; ?></h3>
                            <p>Medical Records</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="charts-row">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i> Session Type Breakdown</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-bar"></i> Session Status</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Session List -->
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fas fa-table"></i> Session Details</h3>
                    </div>
                    <div class="table-container">
                        <table id="performersTable">
                            <thead>
                                <tr>
                                    <th>Session Name</th>
                                    <th>Type</th>
                                    <th>Mode</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="performersTableBody">
                                <?php if (!empty($data['sessions'])): ?>
                                    <?php foreach ($data['sessions'] as $session): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($session->Name); ?></td>
                                        <td><?php echo htmlspecialchars($session->SessionType); ?></td>
                                        <td><?php echo htmlspecialchars($session->SessionMode); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($session->Date)); ?></td>
                                        <td><?php echo date('h:i A', strtotime($session->StartTime)); ?> - <?php echo date('h:i A', strtotime($session->EndTime)); ?></td>
                                        <td><?php echo htmlspecialchars($session->Location ?? 'TBA'); ?></td>
                                        <td>
                                            <?php 
                                            $statusColors = ['active' => '#10b981', 'completed' => '#4A90E2', 'cancelled' => '#ef4444'];
                                            $color = $statusColors[$session->Status] ?? '#666';
                                            ?>
                                            <span class="coach-report-status" style="color: <?php echo $color; ?>;">
                                                <?php echo $session->Status; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No sessions found</td></tr>
                                <?php endif; ?>
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

<script>
window.coachReportsData = <?php echo json_encode([
    'privateSessions' => (int) $data['privateSessions'],
    'groupSessions' => (int) $data['groupSessions'],
    'activeSessions' => (int) $data['activeSessions'],
    'completedSessions' => (int) $data['completedSessions'],
    'totalSessions' => (int) $data['totalSessions']
]); ?>;
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
