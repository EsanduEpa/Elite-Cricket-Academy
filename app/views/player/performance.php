<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/performance.css?v=<?php echo time(); ?>">

    <div class="player-layout">
        <?php $playerActivePage = 'performance'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content" id="performancePage" data-urlroot="<?php echo URLROOT; ?>">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-chart-line"></i> Performance Overview</h1>
                        <p>Track your cricket performance and see your improvement over time.</p>
                    </div>
                    <div class="header-actions">
                    </div>
                </div>
            </div>

            <?php flash('performance_message'); ?>

            <?php
                $performanceSummary = $data['performanceSummary'] ?? [
                    'batting_avg' => 0,
                    'strike_rate' => 0,
                    'total_runs' => 0,
                    'total_wickets' => 0,
                ];
            ?>
            <!-- <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-baseball-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Batting Average</div>
                        <div class="stat-value" data-target="<?php echo $performanceSummary['batting_avg']; ?>">0</div>
                       
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Strike Rate</div>
                        <div class="stat-value" data-target="<?php echo $performanceSummary['strike_rate']; ?>">0</div>
                       
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-target"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Total Runs</div>
                        <div class="stat-value" data-target="<?php echo $performanceSummary['total_runs']; ?>">0</div>
                        
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bowling-ball"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Wickets Taken</div>
                        <div class="stat-value" data-target="<?php echo $performanceSummary['total_wickets']; ?>">0</div>
                        
                    </div>
                </div>
            </div> -->

            <!-- Detailed Batting and Bowling Performance -->
            <div class="performance-tables-row">
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-chart-bar"></i> Batting Performance</h2>
                            <button class="action-btn action-btn-compact" type="button">
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
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-blue">
                                                <?php echo number_format($batting['average'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-running"></i> Total Runs
                                            </div>
                                            <div class="table-cell-details">Career runs scored</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-green">
                                                <?php echo number_format($batting['total_runs']); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-bullseye"></i> Highest Score
                                            </div>
                                            <div class="table-cell-details">Best individual innings</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-orange">
                                                <?php echo $batting['highest_score'] > 0 ? $batting['highest_score'] : 'N/A'; ?>
                                            </div>
                                            <div class="table-cell-secondary">runs</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-percentage"></i> Strike Rate
                                            </div>
                                            <div class="table-cell-details">Runs per 100 balls</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-purple">
                                                <?php echo number_format($batting['strike_rate'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">per 100 balls</div>
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
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary">
                                                <?php echo $batting['centuries']; ?> / <?php echo $batting['half_centuries']; ?>
                                            </div>
                                            <div class="table-cell-secondary">100s / 50s</div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="table-empty-block">
                                            <i class="fas fa-info-circle table-empty-block-icon"></i>
                                            No batting statistics available yet. Add performance data to see your stats.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-fire"></i> Bowling Performance</h2>
                            <button class="action-btn action-btn-compact" type="button">
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['performanceStats']['bowling'])): 
                                    $bowling = $data['performanceStats']['bowling'];
                                    $bowlingEconomyClass = 'performance-metric-blue';
                                    if ($bowling['economy_rate'] <= 6) {
                                        $bowlingEconomyClass = 'performance-metric-green';
                                    } elseif ($bowling['economy_rate'] > 9) {
                                        $bowlingEconomyClass = 'performance-metric-red';
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-fire"></i> Wickets Taken
                                            </div>
                                            <div class="table-cell-details">Total career wickets</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-red">
                                                <?php echo $bowling['total_wickets']; ?>
                                            </div>
                                            <div class="table-cell-secondary">wickets</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-chart-bar"></i> Economy Rate
                                            </div>
                                            <div class="table-cell-details">Runs per over</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric <?php echo $bowlingEconomyClass; ?>">
                                                <?php echo number_format($bowling['economy_rate'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs/over</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-bullseye"></i> Best Figures
                                            </div>
                                            <div class="table-cell-details">Best bowling in an innings</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-gold">
                                                <?php echo htmlspecialchars($bowling['best_figures']); ?>
                                            </div>
                                            <div class="table-cell-secondary">wickets/runs</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="table-cell-title">
                                                <i class="fas fa-crosshairs"></i> Bowling Average
                                            </div>
                                            <div class="table-cell-details">Runs per wicket</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary performance-metric performance-metric-purple">
                                                <?php echo number_format($bowling['average'], 2); ?>
                                            </div>
                                            <div class="table-cell-secondary">runs/wicket</div>
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
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary">
                                                <?php echo $bowling['five_wickets']; ?> / <?php echo $bowling['four_wickets']; ?>
                                            </div>
                                            <div class="table-cell-secondary">5-W / 4-W</div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="table-empty-block">
                                            <i class="fas fa-info-circle table-empty-block-icon"></i>
                                            No bowling statistics available yet. Add performance data to see your stats.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Match Statistics (Batting + Bowling in one table) -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-history"></i> Recent Match Statistics</h2>
                        <span class="badge-info">Last 10 Matches</span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Opponent</th>
                                 <th class="table-cell-center">Result</th>
                                <th class="table-cell-center">Runs</th>
                                <th class="table-cell-center">Balls</th>
                                <th class="table-cell-center">Strike Rt</th>
                               
                                <th class="table-cell-center">Wkts</th>
                                <th class="table-cell-center">Overs</th>
                                <th class="table-cell-center">Conceded</th>
                                <th class="table-cell-center">Econ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $battingStats = $data['battingStats'] ?? [];
                                $bowlingStats = $data['bowlingStats'] ?? [];

                                $mergedRecentStats = [];
                                $buildKey = function(array $stat) {
                                    if (!empty($stat['match_id'])) {
                                        return 'match:' . trim((string)$stat['match_id']);
                                    }
                                    $date = trim((string)($stat['match_date'] ?? ''));
                                    $tournament = strtolower(trim((string)($stat['tournament'] ?? '')));
                                    $opponent = strtolower(trim((string)($stat['opponent'] ?? '')));
                                    $venue = strtolower(trim((string)($stat['venue'] ?? '')));
                                    return $date . '|' . $tournament . '|' . $opponent . '|' . $venue;
                                };

                                foreach ($battingStats as $stat) {
                                    $key = $buildKey($stat);
                                    if (!isset($mergedRecentStats[$key])) {
                                        $mergedRecentStats[$key] = [
                                            'match_date' => $stat['match_date'] ?? null,
                                            'tournament' => $stat['tournament'] ?? null,
                                            'opponent' => $stat['opponent'] ?? null,
                                            'venue' => $stat['venue'] ?? null,
                                            'batting' => null,
                                            'bowling' => null,
                                        ];
                                    }
                                    $mergedRecentStats[$key]['batting'] = $stat;
                                }

                                foreach ($bowlingStats as $stat) {
                                    $key = $buildKey($stat);
                                    if (!isset($mergedRecentStats[$key])) {
                                        $mergedRecentStats[$key] = [
                                            'match_date' => $stat['match_date'] ?? null,
                                            'tournament' => $stat['tournament'] ?? null,
                                            'opponent' => $stat['opponent'] ?? null,
                                            'venue' => $stat['venue'] ?? null,
                                            'batting' => null,
                                            'bowling' => null,
                                        ];
                                    }
                                    $mergedRecentStats[$key]['bowling'] = $stat;
                                }

                                $recentStats = array_values($mergedRecentStats);
                                usort($recentStats, function($a, $b) {
                                    $aTime = strtotime($a['match_date'] ?? '') ?: 0;
                                    $bTime = strtotime($b['match_date'] ?? '') ?: 0;
                                    return $bTime <=> $aTime;
                                });
                                $recentStats = array_slice($recentStats, 0, 10);
                            ?>

                            <?php if (empty($recentStats)): ?>
                                <tr>
                                    <td colspan="10" class="table-empty-row">
                                        <i class="fas fa-info-circle"></i> No recent statistics available yet
                                    </td>
                                </tr>
                            <?php else: ?>

                                <?php foreach ($recentStats as $row): ?>
                                    <?php
                                        $batting = $row['batting'] ?? null;
                                        $bowling = $row['bowling'] ?? null;

                                        $result = $batting['result'] ?? null;
                                        $resultBadgeClass = 'table-badge status-upcoming';
                                        if ($result === 'win') {
                                            $resultBadgeClass = 'table-badge status-active';
                                        } elseif ($result === 'loss') {
                                            $resultBadgeClass = 'table-badge status-cancelled';
                                        }

                                        $economy = $bowling['economy'] ?? null;
                                        $economyClass = 'metric-emphasis-blue';
                                        if ($economy !== null && $economy <= 6) {
                                            $economyClass = 'metric-emphasis-green';
                                        } elseif ($economy !== null && $economy > 9) {
                                            $economyClass = 'metric-emphasis-red';
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-primary">
                                                <?php echo !empty($row['match_date']) ? date('M d, Y', strtotime($row['match_date'])) : '-'; ?>
                                            </div>
                                            <div class="table-cell-secondary">
                                                <?php echo !empty($row['tournament']) ? htmlspecialchars(substr($row['tournament'], 0, 20)) : ''; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title">
                                                <?php echo !empty($row['opponent']) ? htmlspecialchars($row['opponent']) : '-'; ?>
                                            </div>
                                            <div class="table-cell-secondary">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo !empty($row['venue']) ? htmlspecialchars(substr($row['venue'], 0, 25)) : '-'; ?>
                                            </div>
                                        </td>
                                         <td class="table-cell-center">
                                            <?php if (!empty($result)): ?>
                                                <span class="<?php echo $resultBadgeClass; ?>"><?php echo ucfirst($result); ?></span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><strong><?php echo $batting ? (int)$batting['runs'] : '-'; ?></strong></div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo $batting ? (int)$batting['balls'] : '-'; ?></div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo ($batting && isset($batting['strike_rate'])) ? number_format((float)$batting['strike_rate'], 2) : '-'; ?></div>
                                        </td>
                                       
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><strong><?php echo $bowling ? (int)$bowling['wickets'] : '-'; ?></strong></div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo ($bowling && isset($bowling['overs'])) ? number_format((float)$bowling['overs'], 1) : '-'; ?></div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo ($bowling && isset($bowling['runs_conceded'])) ? (int)$bowling['runs_conceded'] : '-'; ?></div>
                                        </td>
                                        <td class="table-cell-center">
                                            <?php if ($economy !== null): ?>
                                                <span class="metric-emphasis <?php echo $economyClass; ?>">
                                                    <?php echo number_format((float)$economy, 2); ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

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
                            <button class="action-btn action-btn-compact" type="button" data-performance-action="add-achievement">
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
                                        <td class="table-cell-center">
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
                                            $achievementBadgeClass = 'achievement-badge-default';
                                            if (stripos($achievement->Achievement, 'century') !== false) {
                                                $achievementBadgeClass = 'achievement-badge-century';
                                            } elseif (stripos($achievement->Achievement, 'wicket') !== false) {
                                                $achievementBadgeClass = 'achievement-badge-wicket';
                                            } elseif (stripos($achievement->Achievement, 'team') !== false) {
                                                $achievementBadgeClass = 'achievement-badge-team';
                                            }
                                            ?>
                                            <span class="table-badge <?php echo $achievementBadgeClass; ?>">Achievement</span>
                                        </td>
                                        <td class="table-cell-center">
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
                                        <td class="table-cell-center achievement-actions-cell">
                                            <?php if ($achievement->VerifiedStatus === 'pending'): ?>
                                                <button
                                                    class="action-btn action-btn-xs"
                                                    type="button"
                                                    data-performance-action="edit-achievement"
                                                    data-achievement-id="<?php echo (int) $achievement->AchievementID; ?>"
                                                    data-date="<?php echo htmlspecialchars((string) ($achievement->Date ?? ''), ENT_QUOTES); ?>"
                                                    data-tournament="<?php echo htmlspecialchars((string) ($achievement->Tournament ?? ''), ENT_QUOTES); ?>"
                                                    data-match-name="<?php echo htmlspecialchars((string) ($achievement->MatchName ?? ''), ENT_QUOTES); ?>"
                                                    data-achievement="<?php echo htmlspecialchars((string) ($achievement->Achievement ?? ''), ENT_QUOTES); ?>"
                                                    data-verified-status="<?php echo htmlspecialchars((string) ($achievement->VerifiedStatus ?? 'pending'), ENT_QUOTES); ?>"
                                                    data-created-at="<?php echo htmlspecialchars((string) ($achievement->CreatedAt ?? ''), ENT_QUOTES); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php elseif ($achievement->VerifiedStatus === 'rejected'): ?>
                                                <button
                                                    class="action-btn action-btn-xs action-btn-spaced"
                                                    type="button"
                                                    data-performance-action="view-achievement"
                                                    data-achievement-id="<?php echo (int) $achievement->AchievementID; ?>"
                                                    data-date="<?php echo htmlspecialchars((string) ($achievement->Date ?? ''), ENT_QUOTES); ?>"
                                                    data-tournament="<?php echo htmlspecialchars((string) ($achievement->Tournament ?? ''), ENT_QUOTES); ?>"
                                                    data-match-name="<?php echo htmlspecialchars((string) ($achievement->MatchName ?? ''), ENT_QUOTES); ?>"
                                                    data-achievement="<?php echo htmlspecialchars((string) ($achievement->Achievement ?? ''), ENT_QUOTES); ?>"
                                                    data-verified-status="<?php echo htmlspecialchars((string) ($achievement->VerifiedStatus ?? 'pending'), ENT_QUOTES); ?>"
                                                    data-created-at="<?php echo htmlspecialchars((string) ($achievement->CreatedAt ?? ''), ENT_QUOTES); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button
                                                    class="action-btn action-btn-xs action-btn-danger"
                                                    type="button"
                                                    data-performance-action="delete-achievement"
                                                    data-achievement-id="<?php echo (int) $achievement->AchievementID; ?>"
                                                    data-tournament="<?php echo htmlspecialchars((string) ($achievement->Tournament ?? ''), ENT_QUOTES); ?>"
                                                    data-achievement-label="<?php echo htmlspecialchars((string) ($achievement->Achievement ?? ''), ENT_QUOTES); ?>"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    class="action-btn action-btn-xs"
                                                    type="button"
                                                    data-performance-action="view-achievement"
                                                    data-achievement-id="<?php echo (int) $achievement->AchievementID; ?>"
                                                    data-date="<?php echo htmlspecialchars((string) ($achievement->Date ?? ''), ENT_QUOTES); ?>"
                                                    data-tournament="<?php echo htmlspecialchars((string) ($achievement->Tournament ?? ''), ENT_QUOTES); ?>"
                                                    data-match-name="<?php echo htmlspecialchars((string) ($achievement->MatchName ?? ''), ENT_QUOTES); ?>"
                                                    data-achievement="<?php echo htmlspecialchars((string) ($achievement->Achievement ?? ''), ENT_QUOTES); ?>"
                                                    data-verified-status="<?php echo htmlspecialchars((string) ($achievement->VerifiedStatus ?? 'pending'), ENT_QUOTES); ?>"
                                                    data-created-at="<?php echo htmlspecialchars((string) ($achievement->CreatedAt ?? ''), ENT_QUOTES); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php elseif (isset($data['achievements']) && empty($data['achievements'])): ?>
                                <!-- Database is working but no achievements found -->
                                <tr>
                                    <td colspan="5" class="table-empty-panel-cell">
                                        <div class="table-empty-panel">
                                            <i class="fas fa-trophy table-empty-panel-icon"></i>
                                            <h3 class="table-empty-panel-title">No Achievements Yet</h3>
                                            <p>Start tracking your achievements by clicking "Add" above.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <!-- Database/controller returned no data, show message -->
                                <tr>
                                    <td colspan="5" class="table-empty-panel-cell">
                                        <div class="table-empty-panel">
                                            <i class="fas fa-exclamation-triangle table-empty-panel-icon"></i>
                                            <h3 class="table-empty-panel-title">Unable to Load Achievements</h3>
                                            <p>Please try refreshing the page or contact support.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Match History -->
            <div class="schedule-card upcoming-schedule" id="matchHistorySection">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-chart-line"></i> Match History</h2>
                        <button class="action-btn action-btn-compact" type="button" data-performance-action="open-performance-modal">
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
                                <?php foreach ($data['playerPerformanceRecords'] as $match): ?>
                                    <tr>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo $match->Date ? date('M d', strtotime($match->Date)) : 'N/A'; ?></div>
                                            <div class="table-cell-secondary"><?php echo $match->Date ? date('l', strtotime($match->Date)) : ''; ?></div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?php echo htmlspecialchars($match->OpponentTeam ?? 'Match'); ?></div>
                                            <div class="table-cell-details"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($match->Venue ?? 'Venue'); ?></div>
                                            <div class="table-cell-details"><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($match->TournamentName ?? 'Match'); ?></div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo $match->RunsScored ?? 0; ?></div>
                                            <div class="table-cell-secondary"><?php echo $match->BallsFaced ?? 0; ?> balls</div>
                                        </td>
                                        <td class="table-cell-center">
                                            <div class="table-cell-primary"><?php echo $match->WicketsTaken ?? 0; ?></div>
                                            <div class="table-cell-secondary"><?php echo $match->RunsConceded ?? 0; ?> runs</div>
                                        </td>
                                        <td class="table-cell-center">
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
                                            <span class="table-badge table-badge-compact <?php echo $statusClass; ?>">
                                                <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                        <td class="table-cell-center achievement-actions-cell">
                                            <button
                                                class="action-btn action-btn-xs action-btn-spaced"
                                                type="button"
                                                data-performance-action="view-match-performance"
                                                data-performance-id="<?php echo (int) $match->PerformanceID; ?>"
                                                data-date="<?php echo htmlspecialchars((string) ($match->Date ?? ''), ENT_QUOTES); ?>"
                                                data-tournament-name="<?php echo htmlspecialchars((string) ($match->TournamentName ?? ''), ENT_QUOTES); ?>"
                                                data-opponent-team="<?php echo htmlspecialchars((string) ($match->OpponentTeam ?? ''), ENT_QUOTES); ?>"
                                                data-venue="<?php echo htmlspecialchars((string) ($match->Venue ?? ''), ENT_QUOTES); ?>"
                                                data-result="<?php echo htmlspecialchars((string) ($match->Result ?? ''), ENT_QUOTES); ?>"
                                                data-runs-scored="<?php echo (int) ($match->RunsScored ?? 0); ?>"
                                                data-balls-faced="<?php echo (int) ($match->BallsFaced ?? 0); ?>"
                                                data-wickets-taken="<?php echo (int) ($match->WicketsTaken ?? 0); ?>"
                                                data-overs-bowled="<?php echo htmlspecialchars((string) ($match->OversBowled ?? 0), ENT_QUOTES); ?>"
                                                data-runs-conceded="<?php echo (int) ($match->RunsConceded ?? 0); ?>"
                                                data-catches="<?php echo (int) ($match->Catches ?? 0); ?>"
                                                data-stumpings="<?php echo (int) ($match->Stumpings ?? 0); ?>"
                                                data-rating="<?php echo htmlspecialchars((string) ($match->Rating ?? 0), ENT_QUOTES); ?>"
                                                data-verified-status="<?php echo htmlspecialchars((string) ($match->VerifiedStatus ?? 'pending'), ENT_QUOTES); ?>"
                                                data-added-by-name="<?php echo htmlspecialchars((string) ($match->AddedByName ?? ''), ENT_QUOTES); ?>"
                                                data-verified-by-name="<?php echo htmlspecialchars((string) ($match->VerifiedByName ?? ''), ENT_QUOTES); ?>"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (!isset($match->VerifiedStatus) || $match->VerifiedStatus === 'pending'): ?>
                                                <button
                                                    class="action-btn action-btn-xs action-btn-spaced"
                                                    type="button"
                                                    data-performance-action="edit-match-performance"
                                                    data-performance-id="<?php echo (int) $match->PerformanceID; ?>"
                                                    data-match-id="<?php echo (int) ($match->MatchID ?? 0); ?>"
                                                    data-runs-scored="<?php echo (int) ($match->RunsScored ?? 0); ?>"
                                                    data-balls-faced="<?php echo (int) ($match->BallsFaced ?? 0); ?>"
                                                    data-wickets-taken="<?php echo (int) ($match->WicketsTaken ?? 0); ?>"
                                                    data-overs-bowled="<?php echo htmlspecialchars((string) ($match->OversBowled ?? 0), ENT_QUOTES); ?>"
                                                    data-runs-conceded="<?php echo (int) ($match->RunsConceded ?? 0); ?>"
                                                    data-catches="<?php echo (int) ($match->Catches ?? 0); ?>"
                                                    data-stumpings="<?php echo (int) ($match->Stumpings ?? 0); ?>"
                                                    data-rating="<?php echo htmlspecialchars((string) ($match->Rating ?? 0), ENT_QUOTES); ?>"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button
                                                    class="action-btn action-btn-xs action-btn-danger"
                                                    type="button"
                                                    data-performance-action="delete-match-performance"
                                                    data-performance-id="<?php echo (int) $match->PerformanceID; ?>"
                                                    data-opponent-team="<?php echo htmlspecialchars((string) ($match->OpponentTeam ?? 'Match'), ENT_QUOTES); ?>"
                                                    data-date="<?php echo htmlspecialchars((string) ($match->Date ?? ''), ENT_QUOTES); ?>"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="table-empty-panel-cell">
                                        <div class="table-empty-panel">
                                            <i class="fas fa-chart-line table-empty-panel-icon"></i>
                                            <h3 class="table-empty-panel-title">No Match History Yet</h3>
                                            <p>Add your match performance by clicking "Add" above.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                    <a href="#" class="action-btn" data-placeholder-message="Goal setting feature coming soon!">
                        <i class="fas fa-target"></i> Set Goals
                    </a>
                    <a href="#" class="action-btn" data-placeholder-message="Detailed reports coming soon!">
                        <i class="fas fa-file-alt"></i> Download Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Statistics Modal -->
    <div id="performanceModal" class="modal app-modal" aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--wide">
            <!-- Modal Header -->
            <div class="modal-header app-modal__header">
                <div class="app-modal__title-wrap">
                    <span class="app-modal__icon">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    <div>
                        <h2 class="app-modal__title">Add Performance Statistics</h2>
                        <p class="app-modal__subtitle">Submit your latest batting, bowling, and fielding figures in the same register-style layout used across player forms.</p>
                    </div>
                </div>
                <button class="close app-modal__close" type="button" data-performance-action="close-performance-modal">&times;</button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body app-modal__body">
                <form id="performanceStatsForm" method="POST" action="<?php echo URLROOT; ?>/performance/addPerformanceStats" class="app-form">
                    <input type="hidden" id="performanceId" name="performance_id">
                    <!-- Match Selection -->
                    <div class="form-group form-group-spaced app-form-group app-form-group--full">
                        <label for="matchSelect" class="app-form-label app-form-label--strong">
                            <i class="fas fa-trophy label-icon-gold app-form-icon app-form-icon--gold"></i> Select Match *
                        </label>
                        <select id="matchSelect" name="match_id" required class="app-form-control app-form-control--lg app-form-select">
                            <option value="">-- Select a match --</option>
                            <?php foreach (($data['availableMatches'] ?? []) as $availableMatch): ?>
                                <option value="<?php echo (int) $availableMatch->MatchID; ?>">
                                    <?php
                                        $matchDateLabel = !empty($availableMatch->Date) ? date('M d, Y', strtotime($availableMatch->Date)) : 'Unknown date';
                                        $matchLabelParts = [
                                            $matchDateLabel,
                                            trim((string) ($availableMatch->OpponentTeam ?? 'Opponent')),
                                            trim((string) ($availableMatch->Venue ?? 'Venue')),
                                            trim((string) ($availableMatch->TournamentName ?? 'Tournament')),
                                        ];
                                        echo htmlspecialchars(implode(' - ', $matchLabelParts));
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Batting Statistics -->
                    <fieldset class="performance-fieldset-blue app-form-section app-form-section--blue">
                        <legend class="performance-fieldset-legend-blue app-form-section__legend app-form-section__legend--blue"><i class="fas fa-baseball-ball"></i> Batting Statistics</legend>
                        <div class="form-row app-form-row">
                            <div class="form-group app-form-group">
                                <label for="runsScored" class="app-form-label">Runs Scored</label>
                                <input type="number" id="runsScored" name="runs_scored" min="0" value="0" class="app-form-control">
                            </div>
                            <div class="form-group app-form-group">
                                <label for="ballsFaced" class="app-form-label">Balls Faced</label>
                                <input type="number" id="ballsFaced" name="balls_faced" min="0" value="0" class="app-form-control">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Bowling Statistics -->
                    <fieldset class="performance-fieldset-red app-form-section app-form-section--red">
                        <legend class="performance-fieldset-legend-red app-form-section__legend app-form-section__legend--red"><i class="fas fa-fire"></i> Bowling Statistics</legend>
                        <div class="form-row app-form-row">
                            <div class="form-group app-form-group">
                                <label for="wicketsTaken" class="app-form-label">Wickets Taken</label>
                                <input type="number" id="wicketsTaken" name="wickets_taken" min="0" value="0" class="app-form-control">
                            </div>
                            <div class="form-group app-form-group">
                                <label for="oversBowled" class="app-form-label">Overs Bowled</label>
                                <input type="number" id="oversBowled" name="overs_bowled" min="0" step="0.1" value="0" class="app-form-control">
                            </div>
                            <div class="form-group app-form-group">
                                <label for="runsConceded" class="app-form-label">Runs Conceded</label>
                                <input type="number" id="runsConceded" name="runs_conceded" min="0" value="0" class="app-form-control">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Fielding Statistics -->
                    <fieldset class="performance-fieldset-green app-form-section app-form-section--green">
                        <legend class="performance-fieldset-legend-green app-form-section__legend app-form-section__legend--green"><i class="fas fa-hand-paper"></i> Fielding Statistics</legend>
                        <div class="form-row app-form-row">
                            <div class="form-group app-form-group">
                                <label for="catches" class="app-form-label">Catches</label>
                                <input type="number" id="catches" name="catches" min="0" value="0" class="app-form-control">
                            </div>
                            <div class="form-group app-form-group">
                                <label for="stumpings" class="app-form-label">Stumpings</label>
                                <input type="number" id="stumpings" name="stumpings" min="0" value="0" class="app-form-control">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Overall Rating -->
                    <div class="form-group form-group-spaced app-form-group app-form-group--full">
                        <label for="performanceRating" class="app-form-label app-form-label--strong">
                            <i class="fas fa-star label-icon-gold app-form-icon app-form-icon--gold"></i> Overall Performance Rating (0-10)
                        </label>
                        <input type="number" id="performanceRating" name="rating" min="0" max="10" step="0.1" value="0" class="app-form-control app-form-control--lg">
                        <small class="app-form-help">
                            <i class="fas fa-info-circle"></i> Rate your overall performance on a scale of 0 to 10
                        </small>
                    </div>

                    <!-- Submit Button -->
                    <div class="performance-submit-section app-form-submit-section">
                        <button type="submit" class="action-btn performance-submit-button">
                            <i class="fas fa-save"></i> <span id="performanceSubmitText">Submit Performance Statistics</span>
                        </button>
                        <p class="performance-submit-note app-form-note">
                            <i class="fas fa-shield-alt"></i> Your performance will be reviewed and verified by coaching staff
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Achievement Modal -->
    <div id="achievementModal" class="modal app-modal" aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--standard">
            <!-- Modal Header -->
            <div class="modal-header app-modal__header performance-modal-header-dark">
                <h2 id="modalTitle" class="app-modal__title">
                    <i class="fas fa-trophy label-icon-gold"></i> Add New Achievement
                </h2>
                <button class="close app-modal__close" type="button" data-performance-action="close-achievement-modal">&times;</button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body app-modal__body">
                <form id="achievementForm" method="POST" action="<?php echo URLROOT; ?>/performance/addAchievement" class="app-form">
                    <input type="hidden" id="achievementId" name="achievement_id">
                    
                    <!-- Loading Indicator -->
                    <div id="formLoading" class="achievement-form-loading">
                        <div class="achievement-spinner"></div>
                        <p class="achievement-form-loading-text">Saving achievement...</p>
                    </div>
                    
                    <!-- Form Fields -->
                    <div id="formFields">
                        <div class="form-row app-form-row">
                            <div class="form-group app-form-group">
                                <label for="achievementDate" class="app-form-label app-form-label--strong">
                                    <i class="fas fa-calendar label-icon-blue app-form-icon app-form-icon--primary"></i> Achievement Date *
                                </label>
                                <input type="date" id="achievementDate" name="date" required class="achievement-form-control app-form-control app-form-control--lg">
                            </div>
                            
                            <div class="form-group app-form-group">
                                <label for="tournamentName" class="app-form-label app-form-label--strong">
                                    <i class="fas fa-medal label-icon-gold app-form-icon app-form-icon--gold"></i> Tournament *
                                </label>
                                <input type="text" id="tournamentName" name="tournament" required placeholder="e.g., Elite League Championship" class="achievement-form-control app-form-control app-form-control--lg">
                            </div>
                        </div>
                        
                        <div class="form-group form-group-spaced app-form-group app-form-group--full">
                            <label for="matchName" class="app-form-label app-form-label--strong">
                                <i class="fas fa-baseball-ball label-icon-red app-form-icon app-form-icon--danger"></i> Match Details *
                            </label>
                            <input type="text" id="matchName" name="match_name" required placeholder="e.g., vs Thunder Warriors (Home Ground)" class="achievement-form-control app-form-control app-form-control--lg">
                        </div>
                        
                        <div class="form-group achievement-text-group app-form-group app-form-group--full">
                            <label for="achievementText" class="app-form-label app-form-label--strong">
                                <i class="fas fa-trophy label-icon-gold app-form-icon app-form-icon--gold"></i> Achievement Description *
                            </label>
                            <textarea id="achievementText" name="achievement" required rows="4" 
                                      placeholder="Describe your achievement in detail (e.g., Century Maker - Scored 105 runs in 85 balls with 12 fours and 2 sixes)"
                                      class="achievement-form-control achievement-textarea app-form-control app-form-control--lg app-form-textarea"></textarea>
                            <small class="app-form-help">
                                <i class="fas fa-info-circle"></i> Be specific about your achievement for better verification
                            </small>
                        </div>
                        
                        <div id="verificationStatus" class="form-group verification-status-group app-form-group app-form-group--full" style="display:none;">
                            <label class="app-form-label app-form-label--strong">
                                <i class="fas fa-check-circle label-icon-green app-form-icon app-form-icon--success"></i> Verification Status
                            </label>
                            <select id="verifiedStatus" name="verified_status" class="app-form-control app-form-control--lg app-form-select">
                                <option value="pending">⏳ Pending Review</option>
                                <option value="verified">✅ Verified</option>
                                <option value="rejected">❌ Rejected</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions app-form-actions">
                        <button type="button" class="achievement-button achievement-button-secondary" data-performance-action="close-achievement-modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                                class="achievement-button achievement-button-primary">
                            <i class="fas fa-save"></i> <span id="submitText">Save Achievement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deletePerformanceModal" class="modal app-modal" aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--compact">
            <div class="modal-header app-modal__header app-modal__header--danger">
                <div class="app-modal__title-wrap">
                    <span class="app-modal__icon"><i class="fas fa-trash"></i></span>
                    <h2 class="app-modal__title">Delete Performance Record</h2>
                </div>
                <button class="close app-modal__close" type="button" data-performance-action="close-delete-performance-modal">&times;</button>
            </div>
            <div class="modal-body app-modal__body">
                <form method="POST" action="<?php echo URLROOT; ?>/performance/deletePerformanceStats" class="app-form">
                    <input type="hidden" id="deletePerformanceId" name="performance_id">
                    <p id="deletePerformanceText">Are you sure you want to delete this performance record?</p>
                    <div class="form-actions app-form-actions">
                        <button type="button" class="achievement-button achievement-button-secondary" data-performance-action="close-delete-performance-modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="achievement-button achievement-button-primary">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Match Performance Details Modal (server-rendered; JS populates + opens/closes) -->
    <div id="detailsModal" class="modal app-modal" aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--wide">
            <div class="modal-header app-modal__header">
                <div class="app-modal__title-wrap">
                    <span class="app-modal__icon"><i class="fas fa-chart-line"></i></span>
                    <div>
                        <h2 id="detailsModalTitle" class="app-modal__title">Match Performance Details</h2>
                        <p id="detailsModalSubtitle" class="app-modal__subtitle">Review the full record for this match.</p>
                    </div>
                </div>
                <button class="close app-modal__close" type="button" data-performance-action="close-details-modal">&times;</button>
            </div>

            <div class="modal-body app-modal__body">
                <div id="detailsGenericMessage" style="display:none;"></div>

                <div id="detailsMatchInfo">
                    <h3><i class="fas fa-info-circle"></i> Match Information</h3>
                    <p><strong>Date:</strong> <span id="detailsDate">-</span></p>
                    <p><strong>Tournament:</strong> <span id="detailsTournament">-</span></p>
                    <p><strong>Opponent:</strong> <span id="detailsOpponent">-</span></p>
                    <p><strong>Venue:</strong> <span id="detailsVenue">-</span></p>
                    <p><strong>Result:</strong> <span id="detailsResult">-</span></p>
                </div>

                <div id="detailsStatsGrid" style="margin-top: 1rem;">
                    <div style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px;">
                        <div>
                            <h4><i class="fas fa-baseball-ball"></i> Batting</h4>
                            <p><strong><span id="detailsRuns">0</span></strong> runs</p>
                            <p><small><span id="detailsBalls">0</span> balls</small></p>
                        </div>
                        <div>
                            <h4><i class="fas fa-fire"></i> Bowling</h4>
                            <p><strong><span id="detailsWickets">0</span></strong> wickets</p>
                            <p><small><span id="detailsOvers">0</span> overs, <span id="detailsConceded">0</span> runs</small></p>
                        </div>
                        <div>
                            <h4><i class="fas fa-hand-paper"></i> Fielding</h4>
                            <p><strong><span id="detailsFieldingTotal">0</span></strong> total</p>
                            <p><small>C: <span id="detailsCatches">0</span> | S: <span id="detailsStumpings">0</span></small></p>
                        </div>
                    </div>
                </div>

                <div id="detailsRating" style="margin-top: 1rem;">
                    <p><strong>Overall Rating:</strong> <span id="detailsRatingValue">0</span>/10</p>
                </div>

                <div id="detailsMeta" style="margin-top: 1rem;">
                    <p><strong>Status:</strong> <span id="detailsStatus">-</span></p>
                    <p id="detailsAddedByRow" style="display:none;"><strong>Added by:</strong> <span id="detailsAddedBy">-</span></p>
                    <p id="detailsVerifiedByRow" style="display:none;"><strong>Verified by:</strong> <span id="detailsVerifiedBy">-</span></p>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteAchievementModal" class="modal app-modal" aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--compact">
            <div class="modal-header app-modal__header app-modal__header--danger">
                <div class="app-modal__title-wrap">
                    <span class="app-modal__icon"><i class="fas fa-trash"></i></span>
                    <h2 class="app-modal__title">Delete Achievement</h2>
                </div>
                <button class="close app-modal__close" type="button" data-performance-action="close-delete-achievement-modal">&times;</button>
            </div>
            <div class="modal-body app-modal__body">
                <form method="POST" action="<?php echo URLROOT; ?>/performance/deleteAchievement" class="app-form">
                    <input type="hidden" id="deleteAchievementId" name="achievement_id">
                    <p id="deleteAchievementText">Are you sure you want to delete this achievement?</p>
                    <div class="form-actions app-form-actions">
                        <button type="button" class="achievement-button achievement-button-secondary" data-performance-action="close-delete-achievement-modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="achievement-button achievement-button-primary">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Achievement Details Modal (server-rendered; JS populates + opens/closes) -->
    <div id="achievementViewModal" class="modal app-modal" aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--standard">
            <div class="modal-header app-modal__header">
                <h2 class="app-modal__title"><i class="fas fa-trophy"></i> Achievement Details</h2>
                <button class="close app-modal__close" type="button" data-performance-action="close-achievement-view-modal">&times;</button>
            </div>
            <div class="modal-body app-modal__body">
                <p><strong>Date:</strong> <span id="achievementViewDate">-</span></p>
                <p><strong>Tournament:</strong> <span id="achievementViewTournament">-</span></p>
                <p><strong>Match:</strong> <span id="achievementViewMatch">-</span></p>
                <p><strong>Achievement:</strong> <span id="achievementViewText">-</span></p>
                <p><strong>Status:</strong> <span id="achievementViewStatus">-</span></p>
                <p><strong>Submitted:</strong> <span id="achievementViewSubmitted">-</span></p>
                <div id="achievementViewBanner" style="margin-top: 1rem;"></div>
            </div>
        </div>
    </div>



    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/performance.js?v=<?php echo time(); ?>"></script>
</body>
</html>
