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

            <!-- Performance Stats - Two Tables Per Row -->
            <div class="performance-tables-row">
                <!-- Batting Performance -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-chart-bar"></i> Batting Performance</h2>
                            <button class="action-btn" style="font-size: 14px; padding: 8px 12px;">
                                <i class="fas fa-sync-alt"></i> Update
                            </button>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Statistic</th>
                                    <th>Value</th>
                                    <th>Rank</th>
                                    <th>Improvement</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-baseball-ball"></i> Batting Average
                                        </div>
                                        <div class="table-cell-details">Runs per dismissal</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">45.2</div>
                                        <div class="table-cell-secondary">runs</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#8</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #28a745; color: white;">+5.2</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-running"></i> Total Runs
                                        </div>
                                        <div class="table-cell-details">Career runs scored</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">1,245</div>
                                        <div class="table-cell-secondary">runs</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#5</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #28a745; color: white;">+185</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-bullseye"></i> Highest Score
                                        </div>
                                        <div class="table-cell-details">Best individual innings</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">85</div>
                                        <div class="table-cell-secondary">not out</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#12</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #17a2b8; color: white;">New</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-percentage"></i> Strike Rate
                                        </div>
                                        <div class="table-cell-details">Runs per 100 balls</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">78%</div>
                                        <div class="table-cell-secondary">per 100 balls</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#6</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #ffc107; color: black;">-2%</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bowling Performance -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-fire"></i> Bowling Performance</h2>
                            <button class="action-btn" style="font-size: 14px; padding: 8px 12px;">
                                <i class="fas fa-sync-alt"></i> Update
                            </button>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Statistic</th>
                                    <th>Value</th>
                                    <th>Rank</th>
                                    <th>Improvement</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-fire"></i> Wickets Taken
                                        </div>
                                        <div class="table-cell-details">Total career wickets</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">15</div>
                                        <div class="table-cell-secondary">wickets</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#4</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #28a745; color: white;">+3</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-chart-bar"></i> Economy Rate
                                        </div>
                                        <div class="table-cell-details">Runs per over</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">3.2</div>
                                        <div class="table-cell-secondary">runs/over</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#3</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #28a745; color: white;">-0.5</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-bullseye"></i> Best Figures
                                        </div>
                                        <div class="table-cell-details">Best bowling in an innings</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">4/25</div>
                                        <div class="table-cell-secondary">wickets/runs</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#2</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #17a2b8; color: white;">New</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">
                                            <i class="fas fa-crosshairs"></i> Bowling Average
                                        </div>
                                        <div class="table-cell-details">Runs per wicket</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">24.8</div>
                                        <div class="table-cell-secondary">runs/wicket</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">#5</div>
                                        <div class="table-cell-secondary">in team</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: #28a745; color: white;">-3.2</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Matches and Achievements - Two Tables Per Row -->
            <div class="performance-tables-row">
                <!-- Recent Matches -->
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
                                        <div class="table-cell-title">vs Team Alpha</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-map-marker-alt"></i> Main Ground
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
                                            <i class="fas fa-map-marker-alt"></i> Practice Ground
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
                                            <i class="fas fa-trophy"></i> Stadium Ground
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

                <!-- Achievements -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-trophy"></i> My Achievements</h2>
                            <button class="action-btn" onclick="showAddAchievementModal()" style="font-size: 14px; padding: 8px 12px;">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Tournament</th>
                                    <th>Achievement</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        <tbody>
                            <?php if (isset($data['achievements']) && !empty($data['achievements'])): ?>
                                <?php foreach ($data['achievements'] as $achievement): ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary"><?php echo date('M d', strtotime($achievement->Date)); ?></div>
                                            <div class="table-cell-secondary"><?php echo date('Y', strtotime($achievement->Date)); ?></div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?php echo htmlspecialchars($achievement->Tournament); ?></div>
                                            <div class="table-cell-details">
                                                <i class="fas fa-baseball-ball"></i> <?php echo htmlspecialchars($achievement->MatchName); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?php echo htmlspecialchars($achievement->Achievement); ?></div>
                                            <?php 
                                            // Determine badge color based on achievement type
                                            $badgeStyle = 'background: #3498db;'; // Default blue
                                            if (stripos($achievement->Achievement, 'century') !== false) {
                                                $badgeStyle = 'background: gold; color: black;';
                                            } elseif (stripos($achievement->Achievement, 'wicket') !== false) {
                                                $badgeStyle = 'background: #e74c3c;';
                                            } elseif (stripos($achievement->Achievement, 'team') !== false) {
                                                $badgeStyle = 'background: #27ae60;';
                                            }
                                            ?>
                                            <span class="table-badge" style="<?php echo $badgeStyle; ?>">Achievement</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php 
                                            $statusClass = 'status-pending';
                                            $statusText = 'Pending';
                                            if ($achievement->VerifiedStatus === 'verified') {
                                                $statusClass = 'status-confirmed';
                                                $statusText = 'Verified';
                                            } elseif ($achievement->VerifiedStatus === 'rejected') {
                                                $statusClass = 'status-cancelled';
                                                $statusText = 'Rejected';
                                            }
                                            ?>
                                            <span class="table-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if ($achievement->VerifiedStatus === 'pending'): ?>
                                                <button class="action-btn" onclick="editAchievement(<?php echo $achievement->AchievementID; ?>)" style="font-size: 12px; padding: 6px 10px;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php elseif ($achievement->VerifiedStatus === 'rejected'): ?>
                                                <button class="action-btn" onclick="viewAchievement(<?php echo $achievement->AchievementID; ?>)" style="font-size: 12px; padding: 6px 8px; margin-right: 5px;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn" onclick="deleteAchievement(<?php echo $achievement->AchievementID; ?>)" style="font-size: 12px; padding: 6px 8px; background: #e74c3c; border-color: #e74c3c;" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="action-btn" onclick="viewAchievement(<?php echo $achievement->AchievementID; ?>)" style="font-size: 12px; padding: 6px 10px;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php elseif (isset($data['achievements']) && empty($data['achievements'])): ?>
                                <!-- Database is working but no achievements found -->
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        <div style="color: #7f8c8d;">
                                            <i class="fas fa-trophy" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                            <h3 style="margin: 10px 0; color: #7f8c8d;">No Achievements Yet</h3>
                                            <p>Start tracking your achievements by clicking "Add" above.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <!-- Database/controller returned no data, show message -->
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        <div style="color: #7f8c8d;">
                                            <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                            <h3 style="margin: 10px 0; color: #7f8c8d;">Unable to Load Achievements</h3>
                                            <p>Please try refreshing the page or contact support.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

    <!-- Achievement Modal -->
    <div id="achievementModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s ease-in-out;">
        <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 3% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 650px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out;">
            <!-- Modal Header -->
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 id="modalTitle" style="margin: 0; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-trophy" style="color: #f1c40f;"></i> Add New Achievement
                </h2>
                <span class="close" onclick="closeAchievementModal()" style="color: #bdc3c7; font-size: 32px; font-weight: bold; cursor: pointer; transition: all 0.3s; padding: 5px; border-radius: 50%;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#bdc3c7';">&times;</span>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" style="padding: 35px;">
                <form id="achievementForm">
                    <input type="hidden" id="achievementId" name="achievement_id">
                    
                    <!-- Loading Indicator -->
                    <div id="formLoading" style="display: none; text-align: center; padding: 20px;">
                        <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <p style="margin-top: 15px; color: #7f8c8d;">Saving achievement...</p>
                    </div>
                    
                    <!-- Form Fields -->
                    <div id="formFields">
                        <div class="form-row" style="display: flex; gap: 25px; margin-bottom: 25px;">
                            <div class="form-group" style="flex: 1;">
                                <label for="achievementDate" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                    <i class="fas fa-calendar" style="color: #3498db; margin-right: 8px;"></i> Achievement Date *
                                </label>
                                <input type="date" id="achievementDate" name="date" required 
                                       style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; transition: all 0.3s; background: #fafafa;"
                                       onfocus="this.style.borderColor='#3498db'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                                       onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none'">
                            </div>
                            
                            <div class="form-group" style="flex: 1;">
                                <label for="tournamentName" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                    <i class="fas fa-medal" style="color: #f39c12; margin-right: 8px;"></i> Tournament *
                                </label>
                                <input type="text" id="tournamentName" name="tournament" required placeholder="e.g., Elite League Championship"
                                       style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; transition: all 0.3s; background: #fafafa;"
                                       onfocus="this.style.borderColor='#3498db'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                                       onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none'">
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="matchName" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-baseball-ball" style="color: #e74c3c; margin-right: 8px;"></i> Match Details *
                            </label>
                            <input type="text" id="matchName" name="match_name" required placeholder="e.g., vs Thunder Warriors (Home Ground)"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; transition: all 0.3s; background: #fafafa;"
                                   onfocus="this.style.borderColor='#3498db'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none'">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 30px;">
                            <label for="achievementText" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-trophy" style="color: #f1c40f; margin-right: 8px;"></i> Achievement Description *
                            </label>
                            <textarea id="achievementText" name="achievement" required rows="4" 
                                      placeholder="Describe your achievement in detail (e.g., Century Maker - Scored 105 runs in 85 balls with 12 fours and 2 sixes)"
                                      style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; resize: vertical; transition: all 0.3s; background: #fafafa; font-family: inherit;"
                                      onfocus="this.style.borderColor='#3498db'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                                      onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none'"></textarea>
                            <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Be specific about your achievement for better verification
                            </small>
                        </div>
                        
                        <div id="verificationStatus" class="form-group" style="margin-bottom: 30px; display: none;">
                            <label for="verifiedStatus" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 8px;"></i> Verification Status
                            </label>
                            <select id="verifiedStatus" name="verified_status" 
                                    style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;">
                                <option value="pending">⏳ Pending Review</option>
                                <option value="verified">✅ Verified</option>
                                <option value="rejected">❌ Rejected</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions" style="display: flex; gap: 15px; justify-content: flex-end; border-top: 1px solid #ecf0f1; padding-top: 25px; margin-top: 20px;">
                        <button type="button" onclick="closeAchievementModal()" 
                                style="padding: 14px 28px; background: #95a5a6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px;"
                                onmouseover="this.style.backgroundColor='#7f8c8d'"
                                onmouseout="this.style.backgroundColor='#95a5a6'">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                                style="padding: 14px 28px; background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(46,204,113,0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(46,204,113,0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(46,204,113,0.3)'">
                            <i class="fas fa-save"></i> <span id="submitText">Save Achievement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CSS Animations -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateY(-50px) scale(0.9); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }
        
        /* Custom scrollbar for modal */
        .modal-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .modal-content::-webkit-scrollbar-thumb {
            background: #bdc3c7;
            border-radius: 4px;
        }
        
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #95a5a6;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .modal-content {
                margin: 5% auto;
                width: 95%;
            }
            
            .form-row {
                flex-direction: column !important;
                gap: 20px !important;
            }
            
            .form-actions {
                flex-direction: column !important;
            }
            
            .form-actions button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        // Modal Management Functions
        function showAddAchievementModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-trophy" style="color: #f1c40f;"></i> Add New Achievement';
            document.getElementById('submitText').textContent = 'Save Achievement';
            document.getElementById('verificationStatus').style.display = 'none';
            document.getElementById('achievementForm').reset();
            document.getElementById('achievementId').value = '';
            
            // Show modal with animation
            const modal = document.getElementById('achievementModal');
            modal.style.display = 'block';
            
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('achievementDate').value = today;
            
            // Focus on first input
            setTimeout(() => {
                document.getElementById('achievementDate').focus();
            }, 300);
        }

        function showEditAchievementModal(achievementData) {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit" style="color: #3498db;"></i> Edit Achievement';
            document.getElementById('submitText').textContent = 'Update Achievement';
            document.getElementById('verificationStatus').style.display = 'block';
            
            // Populate form with existing data
            document.getElementById('achievementId').value = achievementData.AchievementID;
            document.getElementById('achievementDate').value = achievementData.Date;
            document.getElementById('matchName').value = achievementData.MatchName;
            document.getElementById('tournamentName').value = achievementData.Tournament;
            document.getElementById('achievementText').value = achievementData.Achievement;
            document.getElementById('verifiedStatus').value = achievementData.VerifiedStatus;
            
            document.getElementById('achievementModal').style.display = 'block';
        }

        function closeAchievementModal() {
            const modal = document.getElementById('achievementModal');
            modal.style.display = 'none';
            document.getElementById('achievementForm').reset();
            
            // Reset any error states
            const inputs = modal.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.style.borderColor = '#ddd';
                input.style.backgroundColor = '#fafafa';
                input.style.boxShadow = 'none';
            });
        }

        // Enhanced form validation
        function validateForm() {
            const form = document.getElementById('achievementForm');
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e74c3c';
                    field.style.backgroundColor = '#fdf2f2';
                    field.style.boxShadow = '0 0 0 3px rgba(231,76,60,0.1)';
                    
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                    isValid = false;
                } else {
                    field.style.borderColor = '#27ae60';
                    field.style.backgroundColor = '#f8fff8';
                    field.style.boxShadow = '0 0 0 3px rgba(39,174,96,0.1)';
                }
            });

            if (!isValid && firstInvalidField) {
                firstInvalidField.focus();
                showNotification('Please fill in all required fields', 'error');
            }

            return isValid;
        }

        // Show notification function
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 2000;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                transform: translateX(400px);
                transition: transform 0.3s ease-out;
                max-width: 350px;
            `;
            
            // Set colors based on type
            switch(type) {
                case 'success':
                    notification.style.background = 'linear-gradient(135deg, #27ae60, #2ecc71)';
                    notification.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
                    break;
                case 'error':
                    notification.style.background = 'linear-gradient(135deg, #e74c3c, #c0392b)';
                    notification.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                    break;
                default:
                    notification.style.background = 'linear-gradient(135deg, #3498db, #2980b9)';
                    notification.innerHTML = '<i class="fas fa-info-circle"></i> ' + message;
            }
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 4 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }

        // Achievement Action Functions
        function viewAchievement(achievementId) {
            // Show loading
            showNotification('Loading achievement details...', 'info');
            
            // Fetch achievement details and show in a detailed modal
            fetch(`<?php echo URLROOT; ?>/player/getAchievement?id=${achievementId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const achievement = data.achievement;
                        const statusIcon = achievement.VerifiedStatus === 'verified' ? '✅' : 
                                         achievement.VerifiedStatus === 'pending' ? '⏳' : '❌';
                        const statusText = achievement.VerifiedStatus === 'verified' ? 'Verified' : 
                                         achievement.VerifiedStatus === 'pending' ? 'Pending Review' : 'Rejected';
                        
                        const detailsHtml = `
                            <div style="font-family: Arial, sans-serif; line-height: 1.6;">
                                <h3 style="color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-trophy" style="color: #f1c40f;"></i> Achievement Details
                                </h3>
                                
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 15px;">
                                    <p><strong>📅 Date:</strong> ${new Date(achievement.Date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                    <p><strong>🏆 Tournament:</strong> ${achievement.Tournament}</p>
                                    <p><strong>⚾ Match:</strong> ${achievement.MatchName}</p>
                                    <p><strong>🎯 Achievement:</strong> ${achievement.Achievement}</p>
                                    <p><strong>✅ Status:</strong> ${statusIcon} ${statusText}</p>
                                    <p><strong>📝 Submitted:</strong> ${new Date(achievement.CreatedAt).toLocaleDateString()}</p>
                                </div>
                                
                                ${achievement.VerifiedStatus === 'verified' ? 
                                    '<div style="background: #d5f4e6; color: #27ae60; padding: 15px; border-radius: 8px; text-align: center;"><i class="fas fa-medal"></i> <strong>Congratulations! This achievement has been officially verified.</strong></div>' :
                                achievement.VerifiedStatus === 'pending' ?
                                    '<div style="background: #fef9e7; color: #f39c12; padding: 15px; border-radius: 8px; text-align: center;"><i class="fas fa-clock"></i> <strong>This achievement is under review by the coaching staff.</strong></div>' :
                                    '<div style="background: #fadbd8; color: #e74c3c; padding: 15px; border-radius: 8px; text-align: center;"><i class="fas fa-times-circle"></i> <strong>This achievement could not be verified. Please contact your coach for details.</strong></div>'
                                }
                            </div>
                        `;
                        
                        // Create custom modal for viewing
                        const viewModal = document.createElement('div');
                        viewModal.style.cssText = `
                            position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%;
                            background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
                        `;
                        
                        viewModal.innerHTML = `
                            <div style="background: white; border-radius: 12px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                                <div style="padding: 30px;">
                                    ${detailsHtml}
                                    <div style="text-align: center; margin-top: 25px;">
                                        <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                                                style="padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
                                            <i class="fas fa-times"></i> Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Close on background click
                        viewModal.onclick = function(e) {
                            if (e.target === viewModal) {
                                viewModal.remove();
                            }
                        };
                        
                        document.body.appendChild(viewModal);
                        
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to load achievement details. Please try again.', 'error');
                });
        }

        function editAchievement(achievementId) {
            // Show loading
            showNotification('Loading achievement for editing...', 'info');
            
            // Fetch achievement details and open edit modal
            fetch(`<?php echo URLROOT; ?>/player/getAchievement?id=${achievementId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showEditAchievementModal(data.achievement);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to load achievement details. Please try again.', 'error');
                });
        }

        // Enhanced Form Submission Handler
        document.getElementById('achievementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                return;
            }
            
            const formData = new FormData(this);
            const achievementId = document.getElementById('achievementId').value;
            const url = achievementId ? 
                       '<?php echo URLROOT; ?>/player/editAchievement' : 
                       '<?php echo URLROOT; ?>/player/addAchievement';
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const formFields = document.getElementById('formFields');
            const formLoading = document.getElementById('formLoading');
            const originalText = submitText.textContent;
            
            submitBtn.disabled = true;
            formFields.style.display = 'none';
            formLoading.style.display = 'block';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAchievementModal();
                    
                    // Refresh the page after a short delay to show updated achievements
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                    if (data.errors) {
                        showNotification('Validation errors: ' + data.errors.join(', '), 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to save achievement. Please try again.', 'error');
            })
            .finally(() => {
                // Reset form state
                submitBtn.disabled = false;
                formFields.style.display = 'block';
                formLoading.style.display = 'none';
                submitText.textContent = originalText;
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('achievementModal');
            if (event.target === modal) {
                closeAchievementModal();
            }
        }

        // Enhanced keyboard support
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('achievementModal');
            if (modal.style.display === 'block') {
                if (e.key === 'Escape') {
                    closeAchievementModal();
                }
            }
        });

        // Legacy function for backward compatibility
        function addAchievement() {
            showAddAchievementModal();
        }

        // Delete Achievement Function
        function deleteAchievement(achievementId) {
            // Show confirmation dialog
            const userConfirmed = confirm('Are you sure you want to delete this rejected achievement? This action cannot be undone.');
            
            if (!userConfirmed) {
                return;
            }
            
            // Show loading notification
            showNotification('Deleting achievement...', 'info');
            
            // Prepare form data
            const formData = new FormData();
            formData.append('achievement_id', achievementId);
            
            // Send delete request
            fetch('<?php echo URLROOT; ?>/player/deleteAchievement', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Refresh the page to update the table
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to delete achievement. Please try again.', 'error');
            });
        }
    </script>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/performance.js"></script>
</body>
</html>