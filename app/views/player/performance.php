<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/performance.css?v=<?php echo time(); ?>">

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
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                      <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link"><i class="fas fa-ticket-alt"></i><span>Book Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/facilities"   class="nav-link"><i class="fas fa-building"></i><span>Book Facility</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/bookings"    class="nav-link"><i class="fas fa-list-alt"></i><span>My Sessions</span></a></li>
               
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
        <div class="main-content" id="performancePage" data-urlroot="<?php echo URLROOT; ?>">
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
                                    <th style="width: 20%;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['performanceStats']['batting'])): 
                                    $batting = $data['performanceStats']['batting'];
                                ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-baseball-ball"></i> Batting Average
                                            </div>
                                            <div class="table-cell-details">Runs per dismissal</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary" style="font-size: 20px; color: #4A90E2;">
                                                <?php echo number_format($batting['average'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Career Average</div>
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
                                            <div class="table-cell-primary" style="font-size: 20px; color: #27ae60;">
                                                <?php echo number_format($batting['total_runs']); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">All Matches</div>
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
                                            <div class="table-cell-primary" style="font-size: 20px; color: #e67e22;">
                                                <?php echo $batting['highest_score'] > 0 ? $batting['highest_score'] : 'N/A'; ?>
                                            </div>
                                            <div class="table-cell-secondary">runs</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Personal Best</div>
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
                                            <div class="table-cell-primary" style="font-size: 20px; color: #9b59b6;">
                                                <?php echo number_format($batting['strike_rate'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">per 100 balls</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Career Strike Rate</div>
                                        </td>
                                    </tr>
                                    <?php if ($batting['centuries'] > 0 || $batting['half_centuries'] > 0): ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-star"></i> Milestones
                                            </div>
                                            <div class="table-cell-details">Centuries and half-centuries</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary">
                                                <?php echo $batting['centuries']; ?> / <?php echo $batting['half_centuries']; ?>
                                            </div>
                                            <div class="table-cell-secondary">100s / 50s</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Career Milestones</div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 40px; color: #888;">
                                            <i class="fas fa-info-circle" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                                            No batting statistics available yet. Add performance data to see your stats.
                                        </td>
                                    </tr>
                                <?php endif; ?>
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
                                    <th style="width: 20%;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['performanceStats']['bowling'])): 
                                    $bowling = $data['performanceStats']['bowling'];
                                ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-fire"></i> Wickets Taken
                                            </div>
                                            <div class="table-cell-details">Total career wickets</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary" style="font-size: 20px; color: #e74c3c;">
                                                <?php echo $bowling['total_wickets']; ?>
                                            </div>
                                            <div class="table-cell-secondary">wickets</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Career Total</div>
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
                                            <div class="table-cell-primary" style="font-size: 20px; color: <?php echo $bowling['economy_rate'] <= 6 ? '#27ae60' : ($bowling['economy_rate'] > 9 ? '#e74c3c' : '#4A90E2'); ?>;">
                                                <?php echo number_format($bowling['economy_rate'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs/over</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">
                                                <?php 
                                                if ($bowling['economy_rate'] <= 6) echo 'Excellent';
                                                elseif ($bowling['economy_rate'] <= 8) echo 'Good';
                                                elseif ($bowling['economy_rate'] <= 10) echo 'Average';
                                                else echo 'Needs Work';
                                                ?>
                                            </div>
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
                                            <div class="table-cell-primary" style="font-size: 20px; color: #f39c12;">
                                                <?php echo htmlspecialchars($bowling['best_figures']); ?>
                                            </div>
                                            <div class="table-cell-secondary">wickets/runs</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Personal Best</div>
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
                                            <div class="table-cell-primary" style="font-size: 20px; color: #9b59b6;">
                                                <?php echo number_format($bowling['average'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs/wicket</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Career Average</div>
                                        </td>
                                    </tr>
                                    <?php if ($bowling['five_wickets'] > 0 || $bowling['four_wickets'] > 0): ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-star"></i> Milestones
                                            </div>
                                            <div class="table-cell-details">5-wicket and 4-wicket hauls</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary">
                                                <?php echo $bowling['five_wickets']; ?> / <?php echo $bowling['four_wickets']; ?>
                                            </div>
                                            <div class="table-cell-secondary">5-W / 4-W</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-secondary">Career Hauls</div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 40px; color: #888;">
                                            <i class="fas fa-info-circle" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                                            No bowling statistics available yet. Add performance data to see your stats.
                                        </td>
                                    </tr>
                                <?php endif; ?>
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
                            <button class="action-btn" onclick="openPerformanceModal()" style="font-size: 14px; padding: 8px 12px;">
                                <i class="fas fa-plus"></i> Add
                            </button>
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
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['playerPerformanceRecords']) && !empty($data['playerPerformanceRecords'])): ?>
                                    <?php
                                    $recentMatches = array_slice($data['playerPerformanceRecords'], 0, 5); // Show only 5 most recent
                                    foreach ($recentMatches as $match):
                                    ?>
                                        <tr style="background-color: <?php 
                                            $result = strtolower($match->Result ?? '');
                                            if (in_array($result, ['won', 'win', 'w'])) echo 'rgba(39, 174, 96, 0.1)';
                                            elseif (in_array($result, ['lost', 'loss', 'lose', 'l'])) echo 'rgba(231, 76, 60, 0.1)';
                                            elseif (in_array($result, ['draw', 'd', 'tie', 'tied'])) echo 'rgba(52, 152, 219, 0.1)';
                                            else echo 'rgba(241, 196, 15, 0.1)'; // pending/default
                                        ?>;">
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary"><?php echo $match->Date ? date('M d', strtotime($match->Date)) : 'N/A'; ?></div>
                                                <div class="table-cell-secondary"><?php echo $match->Date ? date('l', strtotime($match->Date)) : ''; ?></div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title">
                                                    <?php echo htmlspecialchars($match->OpponentTeam ?? 'Match'); ?>
                                                </div>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($match->Venue ?? 'Venue'); ?>
                                                </div>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-trophy"></i> <?php echo htmlspecialchars($match->TournamentName ?? 'Match'); ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary"><?php echo $match->RunsScored ?? 0; ?></div>
                                                <div class="table-cell-secondary"><?php echo $match->BallsFaced ?? 0; ?> balls</div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="table-cell-primary"><?php echo $match->WicketsTaken ?? 0; ?></div>
                                                <div class="table-cell-secondary"><?php echo $match->RunsConceded ?? 0; ?> runs</div>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php 
                                                $statusClass = 'status-pending';
                                                $statusText = 'Pending';
                                                $statusIcon = 'fa-clock';
                                                if (isset($match->VerifiedStatus)) {
                                                    if ($match->VerifiedStatus === 'verified') {
                                                        $statusClass = 'status-confirmed';
                                                        $statusText = 'Verified';
                                                        $statusIcon = 'fa-check-circle';
                                                    } elseif ($match->VerifiedStatus === 'rejected') {
                                                        $statusClass = 'status-cancelled';
                                                        $statusText = 'Rejected';
                                                        $statusIcon = 'fa-times-circle';
                                                    }
                                                }
                                                ?>
                                                <span class="table-badge <?php echo $statusClass; ?>" style="font-size: 11px;">
                                                    <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <button class="action-btn" onclick="viewMatchDetails(<?php echo $match->PerformanceID; ?>)" 
                                                        style="font-size: 12px; padding: 6px 10px; margin-right: 5px;" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if (!isset($match->VerifiedStatus) || $match->VerifiedStatus === 'pending'): ?>
                                                    <button class="action-btn" onclick="editMatchPerformance(<?php echo $match->PerformanceID; ?>)" 
                                                            style="font-size: 12px; padding: 6px 10px; margin-right: 5px;" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="action-btn" onclick="deleteMatchPerformance(<?php echo $match->PerformanceID; ?>)" 
                                                            style="font-size: 12px; padding: 6px 10px; background: #e74c3c; border-color: #e74c3c;" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px;">
                                            <div style="color: #7f8c8d;">
                                                <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                                <h3 style="margin: 10px 0; color: #7f8c8d;">No Recent Matches</h3>
                                                <p>Add your match performance by clicking "Add" above.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
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

    <!-- Performance Statistics Modal -->
    <div id="performanceModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s ease-in-out;">
        <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 3% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 750px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out; max-height: 85vh; overflow-y: auto;">
            <!-- Modal Header -->
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-chart-bar" style="color: #fff;"></i> Add Performance Statistics
                </h2>
                <span class="close" onclick="closePerformanceModal()" style="color: #fff; font-size: 32px; font-weight: bold; cursor: pointer; transition: all 0.3s; padding: 5px; border-radius: 50%; opacity: 0.8;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.opacity='1';" onmouseout="this.style.backgroundColor='transparent'; this.style.opacity='0.8';">&times;</span>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" style="padding: 35px;">
                <form id="performanceStatsForm">
                    <!-- Match Selection -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="matchSelect" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-trophy" style="color: #f39c12; margin-right: 8px;"></i> Select Match *
                        </label>
                        <select id="matchSelect" name="match_id" required 
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;">
                            <option value="">-- Select a match --</option>
                        </select>
                    </div>

                    <!-- Batting Statistics -->
                    <fieldset style="border: 2px solid #3498db; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                        <legend style="padding: 0 10px; font-weight: 600; color: #3498db;"><i class="fas fa-baseball-ball"></i> Batting Statistics</legend>
                        <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="form-group" style="flex: 1;">
                                <label for="runsScored" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Runs Scored</label>
                                <input type="number" id="runsScored" name="runs_scored" min="0" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="ballsFaced" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Balls Faced</label>
                                <input type="number" id="ballsFaced" name="balls_faced" min="0" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Bowling Statistics -->
                    <fieldset style="border: 2px solid #e74c3c; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                        <legend style="padding: 0 10px; font-weight: 600; color: #e74c3c;"><i class="fas fa-fire"></i> Bowling Statistics</legend>
                        <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="form-group" style="flex: 1;">
                                <label for="wicketsTaken" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Wickets Taken</label>
                                <input type="number" id="wicketsTaken" name="wickets_taken" min="0" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="oversBowled" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Overs Bowled</label>
                                <input type="number" id="oversBowled" name="overs_bowled" min="0" step="0.1" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="runsConceded" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Runs Conceded</label>
                                <input type="number" id="runsConceded" name="runs_conceded" min="0" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Fielding Statistics -->
                    <fieldset style="border: 2px solid #27ae60; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                        <legend style="padding: 0 10px; font-weight: 600; color: #27ae60;"><i class="fas fa-hand-paper"></i> Fielding Statistics</legend>
                        <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="form-group" style="flex: 1;">
                                <label for="catches" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Catches</label>
                                <input type="number" id="catches" name="catches" min="0" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="stumpings" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Stumpings</label>
                                <input type="number" id="stumpings" name="stumpings" min="0" value="0" 
                                       style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Overall Rating -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="performanceRating" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-star" style="color: #f1c40f; margin-right: 8px;"></i> Overall Performance Rating (0-10)
                        </label>
                        <input type="number" id="performanceRating" name="rating" min="0" max="10" step="0.1" value="0" 
                               style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;">
                        <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;">
                            <i class="fas fa-info-circle"></i> Rate your overall performance on a scale of 0 to 10
                        </small>
                    </div>

                    <!-- Submit Button -->
                    <div style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #ecf0f1;">
                        <button type="submit" class="action-btn" style="width: 100%; padding: 16px; font-size: 16px; font-weight: 600;">
                            <i class="fas fa-save"></i> Submit Performance Statistics
                        </button>
                        <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #7f8c8d;">
                            <i class="fas fa-shield-alt"></i> Your performance will be reviewed and verified by coaching staff
                        </p>
                    </div>
                </form>
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



    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/performance.js"></script>
</body>
</html>