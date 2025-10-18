<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/performance.css">
    
    <div class="player-layout">
        <!-- Simple Sidebar -->
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
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
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
                <h1><i class="fas fa-chart-line"></i> Performance Overview</h1>
                <p>Track your cricket performance and see your improvement over time.</p>
            </div>

            <!-- Performance Stats -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-baseball-ball"></i>
                    </div>
                    <div class="stat-value">45.2</div>
                    <div class="stat-label">Batting Average</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="stat-value">1,245</div>
                    <div class="stat-label">Total Runs</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="stat-value">85</div>
                    <div class="stat-label">Highest Score</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-value">78%</div>
                    <div class="stat-label">Strike Rate</div>
                </div>
            </div>

            <!-- Recent Performance -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-chart-line"></i> Recent Matches</h2>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Match</th>
                                <th>Runs</th>
                                <th>Wickets</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 14</div>
                                    <div class="table-cell-secondary">Monday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Match vs Team Alpha</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Main Ground - Strike Rate: 130%
                                    </div>
                                    <span class="table-badge">Match</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">85</div>
                                    <div class="table-cell-secondary">65 balls</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">2</div>
                                    <div class="table-cell-secondary">32 runs</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 10</div>
                                    <div class="table-cell-secondary">Thursday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Practice Match</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Practice Ground - Strike Rate: 110%
                                    </div>
                                    <span class="table-badge">Practice</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">42</div>
                                    <div class="table-cell-secondary">38 balls</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">1</div>
                                    <div class="table-cell-secondary">28 runs</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 6</div>
                                    <div class="table-cell-secondary">Sunday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Tournament Semi-Final</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-trophy"></i> Stadium Ground - Strike Rate: 122%
                                    </div>
                                    <span class="table-badge">Tournament</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">67</div>
                                    <div class="table-cell-secondary">55 balls</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">3</div>
                                    <div class="table-cell-secondary">45 runs</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bowling Performance -->
            <div class="schedule-section">
                <h3>Bowling Performance</h3>
                
                <div class="stats-overview" style="margin-bottom: 20px;">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="stat-value">15</div>
                        <div class="stat-label">Wickets Taken</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="stat-value">3.2</div>
                        <div class="stat-label">Economy Rate</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="stat-value">4/25</div>
                        <div class="stat-label">Best Figures</div>
                    </div>
                </div>
            </div>

            <!-- Training Progress -->
            <div class="schedule-section">
                <h3>Training Progress</h3>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-check-circle" style="color: green;"></i></div>
                    <div class="schedule-details">
                        <h4>Batting Improvement</h4>
                        <p>Average improved from 38.5 to 45.2 in last month • Keep up the great work!</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-arrow-up" style="color: #4A90E2;"></i></div>
                    <div class="schedule-details">
                        <h4>Fitness Level</h4>
                        <p>Completed 28 training sessions • Fitness score increased by 15%</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-star" style="color: gold;"></i></div>
                    <div class="schedule-details">
                        <h4>Goals Achievement</h4>
                        <p>Achieved 3 out of 4 monthly goals • 85% completion rate</p>
                    </div>
                </div>
            </div>

            <!-- Achievements -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-trophy"></i> My Achievements</h2>
                        <button class="action-btn" onclick="addAchievement()" style="font-size: 14px; padding: 8px 12px;">
                            <i class="fas fa-plus"></i> Add Achievement
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Match</th>
                                <th>Tournament</th>
                                <th>Achievement</th>
                                <th>Picture</th>
                                <th>Verified Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 15</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">vs Team Alpha</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Main Ground
                                    </div>
                                    <span class="table-badge">Match</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Elite League</div>
                                    <div class="table-cell-secondary">Regular Season</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Century Maker</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-trophy"></i> Scored 100+ runs in single match
                                    </div>
                                    <span class="table-badge" style="background: gold; color: black;">Batting</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="viewPicture('century_oct15.jpg')" style="font-size: 12px; padding: 6px 10px;">
                                        <i class="fas fa-image"></i> View
                                    </button>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Verified</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 12</div>
                                    <div class="table-cell-secondary">Saturday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">vs City Warriors</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Stadium Ground
                                    </div>
                                    <span class="table-badge">Match</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Championship</div>
                                    <div class="table-cell-secondary">Quarter Final</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Hot Streak</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-fire"></i> 5 consecutive match wins
                                    </div>
                                    <span class="table-badge" style="background: #ff6b6b;">Team</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="viewPicture('hot_streak_oct12.jpg')" style="font-size: 12px; padding: 6px 10px;">
                                        <i class="fas fa-image"></i> View
                                    </button>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Verified</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 08</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">vs Thunder Bolts</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Practice Ground
                                    </div>
                                    <span class="table-badge">Practice</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Local Cup</div>
                                    <div class="table-cell-secondary">Group Stage</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Perfect Aim</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-bullseye"></i> Hit 3 sixes in a row
                                    </div>
                                    <span class="table-badge">Batting</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">No Image</div>
                                    <div class="table-cell-secondary">Pending</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-pending">Under Review</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 15</div>
                                    <div class="table-cell-secondary">Sunday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">vs Royal Kings</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Central Stadium
                                    </div>
                                    <span class="table-badge">Tournament</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Summer Cup</div>
                                    <div class="table-cell-secondary">Final</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">First Century</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-star"></i> First 100 runs in career
                                    </div>
                                    <span class="table-badge" style="background: gold; color: black;">Milestone</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="viewPicture('first_century_sep15.jpg')" style="font-size: 12px; padding: 6px 10px;">
                                        <i class="fas fa-image"></i> View
                                    </button>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Verified</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Aug 22</div>
                                    <div class="table-cell-secondary">Thursday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">vs Eagles United</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Home Ground
                                    </div>
                                    <span class="table-badge">League</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Regular League</div>
                                    <div class="table-cell-secondary">Season Match</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Team Player</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> 25th team match played
                                    </div>
                                    <span class="table-badge" style="background: green;">Team</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn" onclick="viewPicture('team_player_aug22.jpg')" style="font-size: 12px; padding: 6px 10px;">
                                        <i class="fas fa-image"></i> View
                                    </button>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Verified</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>Performance Actions</h3>
                <div class="action-buttons">
                    <a href="<?php echo URLROOT; ?>/player/training" class="action-btn">
                        <i class="fas fa-dumbbell"></i> View Training
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Goal setting feature coming soon!')">
                        <i class="fas fa-target"></i> Set Goals
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Detailed reports coming soon!')">
                        <i class="fas fa-file-alt"></i> Download Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addAchievement() {
            // Simple form for adding achievements
            const achievementData = {
                date: prompt("Enter achievement date (YYYY-MM-DD):"),
                match: prompt("Enter match details (vs Opponent):"),
                tournament: prompt("Enter tournament name:"),
                achievement: prompt("Enter achievement name:"),
                description: prompt("Enter achievement description:")
            };
            
            if (achievementData.date && achievementData.match && achievementData.achievement) {
                // In a real application, this would send data to the server
                alert(`Achievement "${achievementData.achievement}" submitted for verification!\n\nDetails:\nDate: ${achievementData.date}\nMatch: ${achievementData.match}\nTournament: ${achievementData.tournament}\nDescription: ${achievementData.description}\n\nStatus: Pending verification by coaching staff.\n\nNote: You can upload a picture after submission through the admin panel.`);
            } else {
                alert("Please fill in all required fields (Date, Match, Achievement).");
            }
        }

        function viewPicture(imageName) {
            // In a real application, this would open the actual image
            alert(`Viewing achievement picture: ${imageName}\n\nIn a full implementation, this would display the actual achievement photo in a modal or new window.`);
        }
    </script>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/performance.js"></script>
</body>
</html>