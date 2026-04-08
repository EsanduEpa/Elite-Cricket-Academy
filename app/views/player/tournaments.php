<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/tournaments.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/tournaments.css">
    
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
        <div class="player-sidebar" id="playerSidebar">
            <div class="sidebar-header">
                <div class="player-logo">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Player Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item active">                        <a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Book Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="nav-link">
                            <i class="fas fa-list-alt"></i>
                            <span>My Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Simple Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
                <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-medal"></i> Tournaments</h1>
                <p>Participate in exciting cricket tournaments and championships</p>
            </div>

            <!-- Tournament Stats -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Available</div>
                        <div class="stat-value">5</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Enrolled</div>
                        <div class="stat-value">3</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Completed</div>
                        <div class="stat-value">8</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Wins</div>
                        <div class="stat-value">3</div>
                    </div>
                </div>
            </div>

            <!-- Available Tournaments and My Enrollments - Two Tables Per Row -->
            <div class="performance-tables-row">
                <!-- Available Tournaments -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-plus"></i> Available Tournaments</h2>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tournament</th>
                                <th>Prize</th>
                                <th>Fee</th>
                                <th>More Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 25</div>
                                    <div class="table-cell-secondary">Wednesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Elite Cricket Championship</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Main Stadium - Intermediate Level
                                    </div>
                                    <span class="table-badge">Championship</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">$5,000</div>
                                    <div class="table-cell-secondary">16 Teams</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">$150</div>
                                    <div class="table-cell-secondary">2 Days</div>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="showTournamentDetails('tournament_1')">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 05</div>
                                    <div class="table-cell-secondary">Saturday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Masters Cup Tournament</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Championship Ground - Advanced Level
                                    </div>
                                    <span class="table-badge">Masters</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">$10,000</div>
                                    <div class="table-cell-secondary">8 Teams</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">$250</div>
                                    <div class="table-cell-secondary">3 Days</div>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="showTournamentDetails('tournament_2')">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

                <!-- My Enrollments -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-user-check"></i> My Enrollments</h2>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tournament</th>
                                <th>Status</th>
                                <th>Prize</th>
                                <th>More Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 30</div>
                                    <div class="table-cell-secondary">Monday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Youth Cricket League</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Practice Ground - Beginner Level
                                    </div>
                                    <span class="table-badge">Youth</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Enrolled</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Medals</div>
                                    <div class="table-cell-secondary">12 Teams</div>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="showTournamentDetails('tournament_3')">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Past Tournaments -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-history"></i> Past Tournaments</h2>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tournament</th>
                                <th>Result</th>
                                <th>Prize Won</th>
                                <th>More Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Aug 15</div>
                                    <div class="table-cell-secondary">Thursday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Summer Championship</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-trophy"></i> Regional Cricket Ground
                                    </div>
                                    <span class="table-badge">Summer</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-completed">1st Place</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">$2,500</div>
                                    <div class="table-cell-secondary">Champion</div>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="showTournamentDetails('tournament_4')">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Jul 20</div>
                                    <div class="table-cell-secondary">Saturday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Local Cricket Cup</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Community Ground
                                    </div>
                                    <span class="table-badge">Local</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-pending">3rd Place</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">$500</div>
                                    <div class="table-cell-secondary">Bronze</div>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="showTournamentDetails('tournament_5')">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tournament Actions -->
            <!-- Academy Events Table -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar"></i> Academy Events</h2>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['academyEvents'])): ?>
                                <?php foreach ($data['academyEvents'] as $event): ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary">
                                                <?php echo date('M d', strtotime($event['event_date'])); ?>
                                            </div>
                                            <div class="table-cell-secondary">
                                                <?php echo date('l', strtotime($event['event_date'])); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title">
                                                <?php echo htmlspecialchars($event['title']); ?>
                                            </div>
                                            <div class="table-cell-details">
                                                <?php echo htmlspecialchars($event['description']); ?>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="table-badge">
                                                <?php echo htmlspecialchars($event['Type']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="table-badge status-<?php echo strtolower($event['Status']); ?>">
                                                <?php echo htmlspecialchars($event['Status']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <button class="action-btn" onclick="alert('Event details coming soon!')">
                                                <i class="fas fa-info-circle"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; color:#888;">No upcoming academy events found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Tournament Actions -->
            <div class="quick-actions">
                <h3>Tournament Actions</h3>
                <div class="action-buttons">
                    <a href="#" class="action-btn" onclick="alert('Browse tournaments feature coming soon!')">
                        <i class="fas fa-search"></i> Browse Tournaments
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Tournament history feature coming soon!')">
                        <i class="fas fa-history"></i> My History
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Tournament rules feature coming soon!')">
                        <i class="fas fa-book"></i> Tournament Rules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/player/tournaments.js"></script>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
</body>
</html>