<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/player-statistics.css">
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
                    
                    <li class="nav-item active">
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
                    
                    <li class="nav-item">
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
            <!-- Back Button -->
            <div class="breadcrumb">
                <a href="<?php echo URLROOT; ?>/admin/players" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Players
                </a>
            </div>

            <!-- Player Profile Header -->
            <div class="player-profile-header">
                <div class="player-header-left">
                    <div class="player-avatar-large">
                        <span>JS</span>
                    </div>
                    <div class="player-info-main">
                        <h1 class="player-name">John Smith</h1>
                        <div class="player-badges-inline">
                            <span class="badge badge-jersey"><i class="fas fa-tshirt"></i> #07</span>
                            <span class="badge badge-premium"><i class="fas fa-crown"></i> Premium</span>
                            <span class="badge badge-role"><i class="fas fa-cricket"></i> All-Rounder</span>
                        </div>
                    </div>
                </div>
                <div class="player-header-right">
                    <div class="player-contact-info">
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div class="info-details">
                                <span class="info-label">Email</span>
                                <span class="info-value">john.smith@elite.com</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div class="info-details">
                                <span class="info-label">Phone</span>
                                <span class="info-value">+1 234 567 8900</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="info-details">
                                <span class="info-label">Joined</span>
                                <span class="info-value">Jan 15, 2024</span>
                            </div>
                        </div>
                    </div>
                    <div class="player-actions">
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn btn-secondary" onclick="exportPlayerReport()">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='<?php echo URLROOT; ?>/admin/players'">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Player Details -->
            <div class="player-details-row">
                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">Batting Style</span>
                        <span class="detail-value">Right-Handed</span>
                    </div>
                </div>
                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-bowling-ball"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">Bowling Style</span>
                        <span class="detail-value">Fast Medium</span>
                    </div>
                </div>
                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">Specialization</span>
                        <span class="detail-value">All-Rounder</span>
                    </div>
                </div>
                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">Player Level</span>
                        <span class="detail-value">Advanced</span>
                    </div>
                </div>
                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">Achievements</span>
                        <span class="detail-value">12 Awards</span>
                    </div>
                </div>
            </div>

            <!-- Key Performance Metrics - 4 Cards in Single Row -->
            <div class="performance-metrics">
                <div class="metric-card metric-purple">
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value">85%</div>
                        <div class="metric-label">Overall Performance</div>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>+5% from last month</span>
                        </div>
                    </div>
                </div>
                
                <div class="metric-card metric-green">
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-cricket"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value">456</div>
                        <div class="metric-label">Total Runs</div>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>+23 this month</span>
                        </div>
                    </div>
                </div>
                
                <div class="metric-card metric-orange">
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-bowling-ball"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value">12</div>
                        <div class="metric-label">Wickets Taken</div>
                        <div class="metric-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>+3 this month</span>
                        </div>
                    </div>
                </div>
                
                <div class="metric-card metric-pink">
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value">95%</div>
                        <div class="metric-label">Training Attendance</div>
                        <div class="metric-trend trend-excellent">
                            <i class="fas fa-star"></i>
                            <span>Excellent</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <!-- Performance Trend Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-area"></i> Performance Trend (Last 6 Months)</h3>
                        <select class="chart-filter">
                            <option>Last 6 Months</option>
                            <option>Last Year</option>
                            <option>All Time</option>
                        </select>
                    </div>
                    <canvas id="performanceChart"></canvas>
                </div>
                
                <!-- Batting Statistics Chart -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Batting Statistics</h3>
                    </div>
                    <canvas id="battingChart"></canvas>
                </div>
            </div>

            <!-- Detailed Statistics Tables -->
            <div class="statistics-grid">
                <!-- Batting Statistics -->
                <div class="stats-table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-cricket"></i> Batting Performance</h3>
                    </div>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Runs</td>
                                <td><strong>456</strong></td>
                                <td>45.6 per match</td>
                            </tr>
                            <tr>
                                <td>Highest Score</td>
                                <td><strong>87*</strong></td>
                                <td>Not Out</td>
                            </tr>
                            <tr>
                                <td>Strike Rate</td>
                                <td><strong>135.2</strong></td>
                                <td>Above Average</td>
                            </tr>
                            <tr>
                                <td>Batting Average</td>
                                <td><strong>42.3</strong></td>
                                <td>Excellent</td>
                            </tr>
                            <tr>
                                <td>Centuries</td>
                                <td><strong>2</strong></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Half-Centuries</td>
                                <td><strong>5</strong></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Boundaries (4s)</td>
                                <td><strong>45</strong></td>
                                <td>4.5 per match</td>
                            </tr>
                            <tr>
                                <td>Sixes</td>
                                <td><strong>18</strong></td>
                                <td>1.8 per match</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bowling Statistics -->
                <div class="stats-table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-bowling-ball"></i> Bowling Performance</h3>
                    </div>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Wickets</td>
                                <td><strong>12</strong></td>
                                <td>1.2 per match</td>
                            </tr>
                            <tr>
                                <td>Best Figures</td>
                                <td><strong>4/25</strong></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Bowling Average</td>
                                <td><strong>23.5</strong></td>
                                <td>Good</td>
                            </tr>
                            <tr>
                                <td>Economy Rate</td>
                                <td><strong>5.8</strong></td>
                                <td>Economical</td>
                            </tr>
                            <tr>
                                <td>Five-Wicket Hauls</td>
                                <td><strong>0</strong></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Maidens</td>
                                <td><strong>3</strong></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Overs Bowled</td>
                                <td><strong>48.2</strong></td>
                                <td>4.8 per match</td>
                            </tr>
                            <tr>
                                <td>Runs Conceded</td>
                                <td><strong>282</strong></td>
                                <td>28.2 per match</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Fielding Statistics -->
                <div class="stats-table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-hands"></i> Fielding Performance</h3>
                    </div>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Catches</td>
                                <td><strong>8</strong></td>
                                <td>0.8 per match</td>
                            </tr>
                            <tr>
                                <td>Run Outs</td>
                                <td><strong>2</strong></td>
                                <td>Direct hits</td>
                            </tr>
                            <tr>
                                <td>Stumpings</td>
                                <td><strong>0</strong></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Dropped Catches</td>
                                <td><strong>1</strong></td>
                                <td>98% success rate</td>
                            </tr>
                            <tr>
                                <td>Field Position</td>
                                <td colspan="2"><strong>Mid-off / Cover</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Training & Attendance -->
                <div class="stats-table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-dumbbell"></i> Training & Attendance</h3>
                    </div>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Training Sessions</td>
                                <td><strong>38/40</strong></td>
                                <td><span class="badge badge-success">95%</span></td>
                            </tr>
                            <tr>
                                <td>Matches Played</td>
                                <td><strong>10</strong></td>
                                <td><span class="badge badge-success">100%</span></td>
                            </tr>
                            <tr>
                                <td>Practice Hours</td>
                                <td><strong>156 hrs</strong></td>
                                <td>Above Average</td>
                            </tr>
                            <tr>
                                <td>Fitness Score</td>
                                <td><strong>88/100</strong></td>
                                <td><span class="badge badge-success">Excellent</span></td>
                            </tr>
                            <tr>
                                <td>Last Assessment</td>
                                <td colspan="2"><strong>Oct 15, 2025</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Match Performance -->
            <div class="recent-matches-section">
                <div class="section-header-fancy">
                    <div class="header-content">
                        <i class="fas fa-history"></i>
                        <h2>Recent Match Performance</h2>
                    </div>
                    <button class="btn-view-all">View All Matches <i class="fas fa-arrow-right"></i></button>
                </div>
                <div class="matches-grid-enhanced">
                    <!-- Match Card 1 - Won -->
                    <div class="match-card-enhanced match-won">
                        <div class="match-ribbon">
                            <i class="fas fa-trophy"></i> WON
                        </div>
                        <div class="match-card-header">
                            <div class="match-title-row">
                                <span class="match-format">T20 Match</span>
                                <span class="match-date"><i class="fas fa-calendar-day"></i> Oct 18, 2025</span>
                            </div>
                            <h3 class="match-teams">Elite Academy <span class="vs">vs</span> City Sports</h3>
                            <div class="match-venue">
                                <i class="fas fa-map-marker-alt"></i> Elite Ground, City Stadium
                            </div>
                        </div>
                        
                        <div class="match-card-body">
                            <div class="performance-summary">
                                <div class="batting-performance">
                                    <div class="perf-icon"><i class="fas fa-cricket"></i></div>
                                    <div class="perf-details">
                                        <span class="perf-label">Batting</span>
                                        <span class="perf-value">45 runs (32 balls)</span>
                                        <span class="perf-extra">Strike Rate: <strong>140.6</strong></span>
                                    </div>
                                </div>
                                <div class="bowling-performance">
                                    <div class="perf-icon"><i class="fas fa-bowling-ball"></i></div>
                                    <div class="perf-details">
                                        <span class="perf-label">Bowling</span>
                                        <span class="perf-value">2 Wickets</span>
                                        <span class="perf-extra">Economy: <strong>6.5</strong></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="match-highlights">
                                <div class="highlight-badge">
                                    <i class="fas fa-star"></i> Man of the Match
                                </div>
                                <div class="highlight-stats">
                                    <span><i class="fas fa-circle"></i> 4 Fours</span>
                                    <span><i class="fas fa-circle"></i> 2 Sixes</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="match-card-footer">
                            <div class="match-result-info">
                                <i class="fas fa-trophy"></i>
                                <span>Won by 23 runs</span>
                            </div>
                            <button class="btn-match-details">View Details <i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Match Card 2 - Won -->
                    <div class="match-card-enhanced match-won">
                        <div class="match-ribbon">
                            <i class="fas fa-trophy"></i> WON
                        </div>
                        <div class="match-card-header">
                            <div class="match-title-row">
                                <span class="match-format">ODI Match</span>
                                <span class="match-date"><i class="fas fa-calendar-day"></i> Oct 12, 2025</span>
                            </div>
                            <h3 class="match-teams">Elite Academy <span class="vs">vs</span> State Academy</h3>
                            <div class="match-venue">
                                <i class="fas fa-map-marker-alt"></i> State Cricket Ground
                            </div>
                        </div>
                        
                        <div class="match-card-body">
                            <div class="performance-summary">
                                <div class="batting-performance">
                                    <div class="perf-icon"><i class="fas fa-cricket"></i></div>
                                    <div class="perf-details">
                                        <span class="perf-label">Batting</span>
                                        <span class="perf-value">87* runs (68 balls)</span>
                                        <span class="perf-extra">Strike Rate: <strong>127.9</strong></span>
                                    </div>
                                </div>
                                <div class="bowling-performance">
                                    <div class="perf-icon"><i class="fas fa-bowling-ball"></i></div>
                                    <div class="perf-details">
                                        <span class="perf-label">Bowling</span>
                                        <span class="perf-value">1 Wicket</span>
                                        <span class="perf-extra">Economy: <strong>5.2</strong></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="match-highlights">
                                <div class="highlight-badge highlight-special">
                                    <i class="fas fa-fire"></i> Career Best
                                </div>
                                <div class="highlight-stats">
                                    <span><i class="fas fa-circle"></i> 8 Fours</span>
                                    <span><i class="fas fa-circle"></i> 3 Sixes</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="match-card-footer">
                            <div class="match-result-info">
                                <i class="fas fa-trophy"></i>
                                <span>Won by 5 wickets</span>
                            </div>
                            <button class="btn-match-details">View Details <i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Match Card 3 - Lost -->
                    <div class="match-card-enhanced match-lost">
                        <div class="match-ribbon ribbon-lost">
                            <i class="fas fa-times-circle"></i> LOST
                        </div>
                        <div class="match-card-header">
                            <div class="match-title-row">
                                <span class="match-format">T20 Match</span>
                                <span class="match-date"><i class="fas fa-calendar-day"></i> Oct 5, 2025</span>
                            </div>
                            <h3 class="match-teams">Elite Academy <span class="vs">vs</span> Regional XI</h3>
                            <div class="match-venue">
                                <i class="fas fa-map-marker-alt"></i> Regional Sports Complex
                            </div>
                        </div>
                        
                        <div class="match-card-body">
                            <div class="performance-summary">
                                <div class="batting-performance">
                                    <div class="perf-icon"><i class="fas fa-cricket"></i></div>
                                    <div class="perf-details">
                                        <span class="perf-label">Batting</span>
                                        <span class="perf-value">28 runs (22 balls)</span>
                                        <span class="perf-extra">Strike Rate: <strong>127.3</strong></span>
                                    </div>
                                </div>
                                <div class="bowling-performance">
                                    <div class="perf-icon"><i class="fas fa-bowling-ball"></i></div>
                                    <div class="perf-details">
                                        <span class="perf-label">Bowling</span>
                                        <span class="perf-value">3 Wickets</span>
                                        <span class="perf-extra">Economy: <strong>7.8</strong></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="match-highlights">
                                <div class="highlight-badge highlight-bowling">
                                    <i class="fas fa-bowling-ball"></i> Best Bowling
                                </div>
                                <div class="highlight-stats">
                                    <span><i class="fas fa-circle"></i> 2 Fours</span>
                                    <span><i class="fas fa-circle"></i> 1 Six</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="match-card-footer">
                            <div class="match-result-info">
                                <i class="fas fa-times-circle"></i>
                                <span>Lost by 12 runs</span>
                            </div>
                            <button class="btn-match-details">View Details <i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/admin/player-statistics.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
