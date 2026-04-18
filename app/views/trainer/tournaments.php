<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/tournaments-enhanced.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'tournaments'; ?>

<!-- Trainer Tournaments Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Trainer'; ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>View upcoming tournaments and competitions</p>
                </div>
                <div class="header-actions">
                    <button class="btn-performance">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn-training">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Tournaments Content -->
        <div class="tournaments-container">
            <!-- Tournament Statistics Box -->
            <div class="tournament-stats-box glass-card">
                <div class="stats-header">
                    <div class="header-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="header-text">
                        <h2>Tournament Overview</h2>
                        <p>Performance metrics and statistics</p>
                    </div>
                </div>
                <div class="stats-content">
                    <div class="tournament-stats-grid">
                        <div class="tournament-stat-card upcoming-card">
                            <div class="stat-icon-container">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">2</div>
                                <div class="stat-label">Upcoming Events</div>
                                <div class="stat-note">Next month</div>
                            </div>
                        </div>
                        <div class="tournament-stat-card completed-card">
                            <div class="stat-icon-container">
                                <i class="fas fa-flag-checkered"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">5</div>
                                <div class="stat-label">Completed This Year</div>
                                <div class="stat-note positive">+2 from last year</div>
                            </div>
                        </div>
                        <div class="tournament-stat-card winrate-card">
                            <div class="stat-icon-container">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">73%</div>
                                <div class="stat-label">Win Rate</div>
                                <div class="stat-note positive">+8% improvement</div>
                            </div>
                        </div>
                        <div class="tournament-stat-card championship-card">
                            <div class="stat-icon-container">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">3</div>
                                <div class="stat-label">Championships Won</div>
                                <div class="stat-note positive">This year</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Tournaments Box -->
            <div class="upcoming-tournaments-box glass-card">
                <div class="tournaments-header">
                    <div class="header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="header-text">
                        <h2>Upcoming Tournaments</h2>
                        <p>Scheduled competitions and events</p>
                    </div>
                    <button class="enhanced-btn primary-btn" onclick="showTournamentCalendar()">
                        <span class="btn-icon"><i class="fas fa-calendar"></i></span>
                        <span class="btn-text">View Calendar</span>
                        <span class="btn-ripple"></span>
                    </button>
                </div>
                <div class="tournaments-content">
                    <div class="tournament-cards-grid">
                        <div class="enhanced-tournament-card upcoming-tournament">
                            <div class="card-glow-effect"></div>
                            <div class="tournament-status-badge upcoming-badge">
                                <i class="fas fa-clock"></i>
                                <span>Upcoming</span>
                            </div>
                            <div class="tournament-date-display">
                                <div class="date-circle">
                                    <span class="month">Nov</span>
                                    <span class="day">15</span>
                                    <span class="year">2025</span>
                                </div>
                            </div>
                            <div class="tournament-info">
                                <h3 class="tournament-title">Inter-School Cricket Championship</h3>
                                <div class="tournament-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-days"></i>
                                        <span>November 15-17, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Colombo Cricket Club</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>16 Teams Participating</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-trophy"></i>
                                        <span>Senior Division</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tournament-card-actions">
                                <button class="enhanced-action-btn view-btn" onclick="viewTournamentDetails('tournament_1')" title="View Details">
                                    <span class="action-icon"><i class="fas fa-eye"></i></span>
                                    <span class="action-text">View</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                                <button class="enhanced-action-btn prepare-btn" onclick="prepareTeam('tournament_1')" title="Prepare Team">
                                    <span class="action-icon"><i class="fas fa-users"></i></span>
                                    <span class="action-text">Prepare</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                                <button class="enhanced-action-btn download-btn" onclick="downloadTournamentInfo('tournament_1')" title="Download Info">
                                    <span class="action-icon"><i class="fas fa-download"></i></span>
                                    <span class="action-text">Download</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                            </div>
                        </div>

                        <div class="enhanced-tournament-card upcoming-tournament">
                            <div class="card-glow-effect"></div>
                            <div class="tournament-status-badge upcoming-badge">
                                <i class="fas fa-clock"></i>
                                <span>Upcoming</span>
                            </div>
                            <div class="tournament-date-display">
                                <div class="date-circle">
                                    <span class="month">Dec</span>
                                    <span class="day">05</span>
                                    <span class="year">2025</span>
                                </div>
                            </div>
                            <div class="tournament-info">
                                <h3 class="tournament-title">Under-19 District Championship</h3>
                                <div class="tournament-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-days"></i>
                                        <span>December 5-8, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Various Venues</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>12 Teams Participating</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-trophy"></i>
                                        <span>Youth Division</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tournament-card-actions">
                                <button class="enhanced-action-btn view-btn" onclick="viewTournamentDetails('tournament_2')" title="View Details">
                                    <span class="action-icon"><i class="fas fa-eye"></i></span>
                                    <span class="action-text">View</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                                <button class="enhanced-action-btn prepare-btn" onclick="prepareTeam('tournament_2')" title="Prepare Team">
                                    <span class="action-icon"><i class="fas fa-users"></i></span>
                                    <span class="action-text">Prepare</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                                <button class="enhanced-action-btn download-btn" onclick="downloadTournamentInfo('tournament_2')" title="Download Info">
                                    <span class="action-icon"><i class="fas fa-download"></i></span>
                                    <span class="action-text">Download</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tournaments Box -->
            <div class="recent-tournaments-box glass-card">
                <div class="tournaments-header">
                    <div class="header-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="header-text">
                        <h2>Recent Tournaments</h2>
                        <p>Past competition results and achievements</p>
                    </div>
                    <button class="enhanced-btn secondary-btn" onclick="viewAllResults()">
                        <span class="btn-icon"><i class="fas fa-list"></i></span>
                        <span class="btn-text">View All Results</span>
                        <span class="btn-ripple"></span>
                    </button>
                </div>
                <div class="tournaments-content">
                    <div class="tournament-cards-grid">
                        <div class="enhanced-tournament-card completed-tournament runner-up">
                            <div class="card-glow-effect"></div>
                            <div class="achievement-ribbon">
                                <i class="fas fa-medal"></i>
                                <span>2nd Place</span>
                            </div>
                            <div class="tournament-date-display">
                                <div class="date-circle completed">
                                    <span class="month">Sep</span>
                                    <span class="day">20</span>
                                    <span class="year">2025</span>
                                </div>
                            </div>
                            <div class="tournament-info">
                                <h3 class="tournament-title">Elite Cricket League</h3>
                                <div class="tournament-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-days"></i>
                                        <span>September 20-22, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-star"></i>
                                        <span>Best Performance: K. Silva</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Team Rating: 8.5/10</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tournament-card-actions">
                                <button class="enhanced-action-btn report-btn" onclick="viewTournamentReport('tournament_3')" title="View Report">
                                    <span class="action-icon"><i class="fas fa-file-alt"></i></span>
                                    <span class="action-text">Report</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                                <button class="enhanced-action-btn download-btn" onclick="downloadReport('tournament_3')" title="Download Report">
                                    <span class="action-icon"><i class="fas fa-download"></i></span>
                                    <span class="action-text">Download</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                            </div>
                        </div>

                        <div class="enhanced-tournament-card completed-tournament champion">
                            <div class="card-glow-effect"></div>
                            <div class="achievement-ribbon champion-ribbon">
                                <i class="fas fa-crown"></i>
                                <span>Champions</span>
                            </div>
                            <div class="tournament-date-display">
                                <div class="date-circle champion">
                                    <span class="month">Aug</span>
                                    <span class="day">10</span>
                                    <span class="year">2025</span>
                                </div>
                            </div>
                            <div class="tournament-info">
                                <h3 class="tournament-title">Youth Cricket Festival</h3>
                                <div class="tournament-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-days"></i>
                                        <span>August 10-12, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-star"></i>
                                        <span>Best Performance: N. Perera</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Team Rating: 9.2/10</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tournament-card-actions">
                                <button class="enhanced-action-btn report-btn" onclick="viewTournamentReport('tournament_4')" title="View Report">
                                    <span class="action-icon"><i class="fas fa-file-alt"></i></span>
                                    <span class="action-text">Report</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                                <button class="enhanced-action-btn download-btn" onclick="downloadReport('tournament_4')" title="Download Report">
                                    <span class="action-icon"><i class="fas fa-download"></i></span>
                                    <span class="action-text">Download</span>
                                    <span class="btn-shimmer"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script>
// Tournament-specific functions
function showTournamentCalendar() {
    showNotification('Tournament calendar view - Coming soon!', 'info');
}

function viewTournamentDetails(id) {
    showNotification('Tournament details for ' + id + ' - Coming soon!', 'info');
}

function prepareTeam(id) {
    showNotification('Team preparation for ' + id + ' - Coming soon!', 'info');
}

function downloadTournamentInfo(id) {
    showNotification('Downloading tournament info for ' + id + '...', 'info');
}

function viewAllResults() {
    showNotification('All tournament results - Coming soon!', 'info');
}

function viewTournamentReport(id) {
    showNotification('Tournament report for ' + id + ' - Coming soon!', 'info');
}

function downloadReport(id) {
    showNotification('Downloading report for ' + id + '...', 'info');
}
</script>

<!-- Enhanced Tournaments JavaScript -->
<script src="<?php echo URLROOT; ?>/js/trainer/tournaments-enhanced.js?v=<?php echo time(); ?>"></script>
</body>
</html>