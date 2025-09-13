<!DOCTYPE html>
<html lang="en">
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Analytics - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/performance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>
    
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
                            <span>Training Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link active">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                      <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping & Rental</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Player Profile -->
            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name'] ?? 'Player Name'; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level'] ?? 'Member'; ?> Member</div>
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
            <div class="performance-dashboard">
                <!-- Header Section -->
                <div class="content-header">
                    <div class="header-title">
                        <h1><i class="fas fa-chart-line"></i> Performance Analytics</h1>
                        <p>Track your cricket performance, statistics, and improvement over time</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="exportDataBtn">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>

                <!-- Overview Stats -->
                <div class="overview-section">
                    <h2><i class="fas fa-tachometer-alt"></i> Performance Overview</h2>
                    <div class="stats-grid">
                        <div class="stat-card batting">
                            <div class="stat-icon">
                                <i class="fas fa-baseball-ball"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Batting Average</h3>
                                <div class="stat-value">42.5</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+5.2% from last month</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card bowling">
                            <div class="stat-icon">
                                <i class="fas fa-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Bowling Average</h3>
                                <div class="stat-value">24.8</div>
                                <div class="stat-change negative">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>-2.1% from last month</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card fielding">
                            <div class="stat-icon">
                                <i class="fas fa-hand-rock"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Fielding %</h3>
                                <div class="stat-value">89.3%</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+3.1% from last month</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card matches">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Matches Played</h3>
                                <div class="stat-value">18</div>
                                <div class="stat-change neutral">
                                    <i class="fas fa-minus"></i>
                                    <span>This season</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Charts -->
                <div class="charts-section">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i> Performance Trends</h3>
                            <div class="time-range-selector">
                                <button class="range-btn active" data-range="week">Week</button>
                                <button class="range-btn" data-range="month">Month</button>
                                <button class="range-btn" data-range="season">Season</button>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Practice Matches Section -->
                <div class="matches-section">
                    <div class="section-header">
                        <h2><i class="fas fa-trophy"></i> Recent Practice Matches</h2>
                        <button class="btn btn-outline" id="addMatchBtn">
                            <i class="fas fa-plus"></i> Add Match
                        </button>
                    </div>

                    <div class="matches-grid">
                        <!-- Practice Match Cards -->
                        <div class="match-card">
                            <div class="match-header">
                                <div class="match-type">
                                    <i class="fas fa-users"></i>
                                    <span>Practice Match</span>
                                </div>
                                <div class="match-date">Dec 10, 2024</div>
                            </div>
                            <div class="match-details">
                                <div class="teams">
                                    <span class="team-name">Team A</span>
                                    <span class="vs">vs</span>
                                    <span class="team-name">Team B</span>
                                </div>
                                <div class="score">165/8 vs 142/10</div>
                                <div class="result win">Won by 23 runs</div>
                            </div>
                            <div class="performance-summary">
                                <div class="performance-item">
                                    <span class="performance-label">Batting:</span>
                                    <span class="performance-value">34* (28 balls)</span>
                                </div>
                                <div class="performance-item">
                                    <span class="performance-label">Bowling:</span>
                                    <span class="performance-value">2/15 (4 overs)</span>
                                </div>
                            </div>
                            <div class="match-actions">
                                <button class="action-btn view" onclick="viewMatchDetails(1)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="action-btn edit" onclick="editMatch(1)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>

                        <!-- Another Match Card -->
                        <div class="match-card">
                            <div class="match-header">
                                <div class="match-type">
                                    <i class="fas fa-trophy"></i>
                                    <span>Tournament</span>
                                </div>
                                <div class="match-date">Dec 8, 2024</div>
                            </div>
                            <div class="match-details">
                                <div class="teams">
                                    <span class="team-name">Elite CA</span>
                                    <span class="vs">vs</span>
                                    <span class="team-name">City Club</span>
                                </div>
                                <div class="score">198/6 vs 201/4</div>
                                <div class="result loss">Lost by 6 wickets</div>
                            </div>
                            <div class="performance-summary">
                                <div class="performance-item">
                                    <span class="performance-label">Batting:</span>
                                    <span class="performance-value">67 (45 balls)</span>
                                </div>
                                <div class="performance-item">
                                    <span class="performance-label">Bowling:</span>
                                    <span class="performance-value">1/28 (4 overs)</span>
                                </div>
                            </div>
                            <div class="match-actions">
                                <button class="action-btn view" onclick="viewMatchDetails(2)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="action-btn edit" onclick="editMatch(2)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>

                        <!-- To Be Updated Match -->
                        <div class="match-card to-update">
                            <div class="match-header">
                                <div class="match-type">
                                    <i class="fas fa-clock"></i>
                                    <span>To Be Updated</span>
                                </div>
                                <div class="match-date">Dec 12, 2024</div>
                            </div>
                            <div class="match-details">
                                <div class="teams">
                                    <span class="team-name">Elite CA</span>
                                    <span class="vs">vs</span>
                                    <span class="team-name">Sports Club</span>
                                </div>
                                <div class="pending-update">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Performance data pending</span>
                                </div>
                            </div>
                            <div class="match-actions">
                                <button class="action-btn update primary" onclick="updateMatch(3)">
                                    <i class="fas fa-plus"></i> Add Performance
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tournaments Section -->
                <div class="tournaments-section">
                    <div class="section-header">
                        <h2><i class="fas fa-trophy"></i> Tournament Performances</h2>
                    </div>

                    <div class="tournaments-grid">
                        <div class="tournament-card">
                            <div class="tournament-header">
                                <div class="tournament-logo">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="tournament-info">
                                    <h4>District Championship</h4>
                                    <p>September 2024</p>
                                </div>
                                <div class="tournament-status">
                                    <span class="status-badge completed">Completed</span>
                                </div>
                            </div>
                            <div class="tournament-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Position:</span>
                                    <span class="stat-value">2nd Place</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Matches:</span>
                                    <span class="stat-value">5 played</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Best Score:</span>
                                    <span class="stat-value">85*</span>
                                </div>
                            </div>
                        </div>

                        <div class="tournament-card">
                            <div class="tournament-header">
                                <div class="tournament-logo">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="tournament-info">
                                    <h4>Inter-Academy Cup</h4>
                                    <p>November 2024</p>
                                </div>
                                <div class="tournament-status">
                                    <span class="status-badge ongoing">Ongoing</span>
                                </div>
                            </div>
                            <div class="tournament-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Position:</span>
                                    <span class="stat-value">Quarter Finals</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Matches:</span>
                                    <span class="stat-value">3 played</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Best Score:</span>
                                    <span class="stat-value">67</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/performance.js"></script>
</body>
</html>