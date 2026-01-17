<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">

<style>
/* Reports Page Enhanced Styles */
.reports-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
    border-radius: 20px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.reports-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(50%, -50%);
}

.reports-header h1 {
    margin: 0 0 12px 0;
    font-size: 36px;
    font-weight: 800;
    position: relative;
    z-index: 1;
}

.reports-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 16px;
    position: relative;
    z-index: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 35px;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.25);
}

.stat-card .icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    margin-bottom: 18px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.stat-card h3 {
    font-size: 42px;
    margin: 12px 0;
    color: #2c3e50;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-card p {
    color: #7f8c8d;
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.content-card {
    background: white;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #f0f0f0;
}

.section-header h2 {
    margin: 0;
    font-size: 26px;
    color: #2c3e50;
    font-weight: 700;
}

.section-header i {
    margin-right: 15px;
    color: #667eea;
    font-size: 28px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 10px;
    font-size: 14px;
    display: flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group label i {
    margin-right: 10px;
    color: #667eea;
    font-size: 16px;
}

.form-group select,
.form-group input {
    padding: 14px 18px;
    border: 2px solid #e8ecef;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: white;
    font-weight: 500;
}

.form-group select:hover,
.form-group input:hover {
    border-color: #d0d7de;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    background: #fafbfd;
}

.btn-generate {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 16px 35px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    margin-top: 10px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-generate:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
}

.btn-generate:active {
    transform: translateY(-1px);
}

.btn-generate:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-generate i {
    font-size: 18px;
}

/* Loading Modal */
.loading-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(44, 62, 80, 0.85);
    backdrop-filter: blur(8px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
}

.loading-modal.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.loading-content {
    background: white;
    padding: 60px 80px;
    border-radius: 20px;
    text-align: center;
    max-width: 450px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.loading-spinner {
    font-size: 70px;
    color: #667eea;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

.loading-content h3 {
    margin: 20px 0 12px 0;
    color: #2c3e50;
    font-size: 24px;
    font-weight: 700;
}

.loading-content p {
    color: #7f8c8d;
    margin: 0;
    font-size: 15px;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Report Modal */
.report-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(44, 62, 80, 0.75);
    backdrop-filter: blur(5px);
    z-index: 10001;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.report-modal.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

.modal-container {
    background: white;
    border-radius: 20px;
    max-width: 1300px;
    width: 100%;
    max-height: 92vh;
    overflow: hidden;
    position: relative;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
    animation: modalSlide 0.4s ease;
    display: flex;
    flex-direction: column;
}

@keyframes modalSlide {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-header {
    padding: 30px 35px;
    border-bottom: 3px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.modal-header h2 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
}

.modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    font-size: 32px;
    cursor: pointer;
    color: white;
    padding: 0;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
    font-weight: 300;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.modal-body {
    padding: 35px;
    overflow-y: auto;
    flex: 1;
}

.report-preview h2 {
    color: #667eea;
    text-align: center;
    margin-bottom: 12px;
    font-size: 32px;
    font-weight: 800;
}

.report-date {
    text-align: center;
    color: #7f8c8d;
    margin-bottom: 35px;
    font-size: 15px;
    line-height: 1.8;
}

.summary-section h3 {
    color: #2c3e50;
    margin-bottom: 25px;
    font-size: 22px;
    font-weight: 700;
    padding-left: 15px;
    border-left: 4px solid #667eea;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 35px;
}

.summary-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    border-radius: 14px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.summary-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
}

.summary-item strong {
    display: block;
    font-size: 12px;
    opacity: 0.95;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.summary-item span {
    font-size: 38px;
    font-weight: 800;
    display: block;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.report-table {
    margin-top: 35px;
}

.report-table h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 22px;
    font-weight: 700;
    padding-left: 15px;
    border-left: 4px solid #667eea;
}

.report-table table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.report-table th {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 18px 20px;
    text-align: left;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.report-table td {
    padding: 18px 20px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 15px;
    color: #2c3e50;
}

.report-table tbody tr {
    transition: all 0.3s ease;
}

.report-table tr:hover td {
    background: #f8f9fd;
    transform: scale(1.01);
}

.report-table tr:last-child td {
    border-bottom: none;
}

.modal-footer {
    padding: 25px 35px;
    border-top: 3px solid #f0f0f0;
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    background: #fafbfd;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 14px;
}

.btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(127, 140, 141, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 14px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .reports-header {
        padding: 30px;
    }
    
    .reports-header h1 {
        font-size: 28px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-container {
        max-width: 95%;
        max-height: 95vh;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
}

@media print {
    .modal-header,
    .modal-footer {
        display: none;
    }
    
    .modal-container {
        box-shadow: none;
        max-height: none;
    }
    
    .report-table {
        page-break-inside: avoid;
    }
}
</style>

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
                    
                    <li class="nav-item active">
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
            <!-- Reports Header -->
            <div class="reports-header">
                <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
                <p>Generate comprehensive reports for your cricket academy operations</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 id="totalReports">-</h3>
                    <p>Total Reports</p>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <h3 id="totalDownloads">-</h3>
                    <p>Downloads</p>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 id="monthReports">-</h3>
                    <p>This Month</p>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>-</h3>
                    <p>Last Generated</p>
                </div>
            </div>

            <!-- Report Generator -->
            <div class="content-card">
                <div class="section-header">
                    <h2><i class="fas fa-plus-circle"></i> Generate New Report</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="reportType">
                            <i class="fas fa-file-alt"></i> Report Type
                        </label>
                        <select id="reportType">
                            <option value="">Select Report Type</option>
                            <option value="event">Event Summary Report</option>
                            <option value="player">Player Performance Report</option>
                            <option value="revenue">Revenue & Finance Report</option>
                            <option value="attendance">Attendance Report</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reportPeriod">
                            <i class="fas fa-calendar"></i> Time Period
                        </label>
                        <select id="reportPeriod">
                            <option value="week">Last Week</option>
                            <option value="month" selected>Last Month</option>
                            <option value="quarter">Last Quarter</option>
                            <option value="year">Last Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div class="form-group" id="customDateStart" style="display: none;">
                        <label for="startDate">
                            <i class="fas fa-calendar-day"></i> Start Date
                        </label>
                        <input type="date" id="startDate">
                    </div>

                    <div class="form-group" id="customDateEnd" style="display: none;">
                        <label for="endDate">
                            <i class="fas fa-calendar-day"></i> End Date
                        </label>
                        <input type="date" id="endDate">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="reportFormat">
                            <i class="fas fa-file-export"></i> Export Format
                        </label>
                        <select id="reportFormat">
                            <option value="pdf">PDF Document</option>
                            <option value="excel">Excel Spreadsheet</option>
                            <option value="csv">CSV File</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn-generate" id="generateBtn">
                            <i class="fas fa-cog fa-spin" style="display:none;" id="generateSpinner"></i>
                            <i class="fas fa-play" id="generateIcon"></i>
                            <span id="generateText">Generate Report</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="loading-modal" id="loadingModal">
        <div class="loading-content">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h3>Generating Report...</h3>
            <p>Please wait while we compile your data</p>
        </div>
    </div>

    <!-- Report Preview Modal -->
    <div class="report-modal" id="reportModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Report Preview</h2>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Report content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="closeModalBtn">Close</button>
                <button class="btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </div>

<script>
// Reports Page JavaScript
(function() {
    'use strict';
    
    console.log('Reports page loaded');

    // Get all elements
    const generateBtn = document.getElementById('generateBtn');
    const reportType = document.getElementById('reportType');
    const reportPeriod = document.getElementById('reportPeriod');
    const reportFormat = document.getElementById('reportFormat');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const customDateStart = document.getElementById('customDateStart');
    const customDateEnd = document.getElementById('customDateEnd');
    const loadingModal = document.getElementById('loadingModal');
    const reportModal = document.getElementById('reportModal');
    const modalBody = document.getElementById('modalBody');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const generateSpinner = document.getElementById('generateSpinner');
    const generateIcon = document.getElementById('generateIcon');
    const generateText = document.getElementById('generateText');

    // Show/hide custom date range
    if (reportPeriod) {
        reportPeriod.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateStart.style.display = 'block';
                customDateEnd.style.display = 'block';
            } else {
                customDateStart.style.display = 'none';
                customDateEnd.style.display = 'none';
            }
        });
    }

    // Generate button click handler
    if (generateBtn) {
        generateBtn.addEventListener('click', function() {
            console.log('Generate button clicked');
            
            const type = reportType.value;
            const period = reportPeriod.value;
            const format = reportFormat.value;

            if (!type) {
                alert('Please select a report type');
                return;
            }

            if (type === 'event') {
                generateEventReport(period, format);
            } else {
                alert('This report type will be available soon!');
            }
        });
    }

    // Generate Event Report
    function generateEventReport(period, format) {
        console.log('Generating event report:', period, format);
        
        // Show loading
        generateSpinner.style.display = 'inline-block';
        generateIcon.style.display = 'none';
        generateText.textContent = 'Generating...';
        generateBtn.disabled = true;
        loadingModal.classList.add('active');

        const formData = new URLSearchParams();
        formData.append('period', period);
        
        if (period === 'custom') {
            formData.append('start_date', startDate.value);
            formData.append('end_date', endDate.value);
        }

        fetch('<?php echo URLROOT; ?>/admin/generate_event_report', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading
            loadingModal.classList.remove('active');
            generateSpinner.style.display = 'none';
            generateIcon.style.display = 'inline-block';
            generateText.textContent = 'Generate Report';
            generateBtn.disabled = false;
            
            if (data.success) {
                displayReport(data);
            } else {
                alert('Error: ' + (data.message || 'Failed to generate report'));
            }
        })
        .catch(error => {
            // Hide loading
            loadingModal.classList.remove('active');
            generateSpinner.style.display = 'none';
            generateIcon.style.display = 'inline-block';
            generateText.textContent = 'Generate Report';
            generateBtn.disabled = false;
            
            console.error('Error:', error);
            alert('Failed to generate report. Please try again.');
        });
    }

    // Display Report in Modal
    function displayReport(data) {
        const html = `
            <div class="report-preview">
                <h2>${data.report_type}</h2>
                <p class="report-date">
                    <strong>Period:</strong> ${data.date_range.from} to ${data.date_range.to}<br>
                    <strong>Generated:</strong> ${data.generated_at}
                </p>

                <div class="summary-section">
                    <h3>Summary Statistics</h3>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <strong>Total Events</strong>
                            <span>${data.summary.total_events}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Completed</strong>
                            <span>${data.summary.completed_events}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Upcoming</strong>
                            <span>${data.summary.upcoming_events}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Cancelled</strong>
                            <span>${data.summary.cancelled_events}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Tournaments</strong>
                            <span>${data.summary.tournaments}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Training Camps</strong>
                            <span>${data.summary.training_camps}</span>
                        </div>
                        <div class="summary-item">
                            <strong>Matches</strong>
                            <span>${data.summary.matches}</span>
                        </div>
                    </div>
                </div>

                ${data.events && data.events.length > 0 ? `
                    <div class="report-table">
                        <h3>Event Details</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.events.map(event => `
                                    <tr>
                                        <td>${event.Name}</td>
                                        <td>${event.Type}</td>
                                        <td>${event.StartDate}</td>
                                        <td>${event.Location || 'N/A'}</td>
                                        <td><span style="text-transform: capitalize;">${event.Status}</span></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                ` : '<p style="text-align: center; color: #7f8c8d; padding: 30px; font-size: 16px;">No events found for this period.</p>'}
            </div>
        `;

        modalBody.innerHTML = html;
        reportModal.classList.add('active');
    }

    // Close modal handlers
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            reportModal.classList.remove('active');
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            reportModal.classList.remove('active');
        });
    }

    // Close on outside click
    if (reportModal) {
        reportModal.addEventListener('click', function(e) {
            if (e.target === reportModal) {
                reportModal.classList.remove('active');
            }
        });
    }

    console.log('Reports page initialized successfully');
})();
</script>

<script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
