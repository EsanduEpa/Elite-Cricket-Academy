<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/performance.css?v=<?php echo time(); ?>">

<div class="player-layout">
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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training" class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/performance" class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>

        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
            <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content" id="performancePage" data-urlroot="<?php echo URLROOT; ?>">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-history"></i> Match History</h1>
                    <p>Review your submitted and verified match performance records.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/performance" class="action-btn">
                        <i class="fas fa-arrow-left"></i> Back to Performance
                    </a>
                </div>
            </div>
        </div>

        <div class="schedule-card upcoming-schedule">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-chart-line"></i> Match History</h2>
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
                            <?php foreach ($data['playerPerformanceRecords'] as $match): ?>
                                <tr style="background-color: <?php 
                                    $result = strtolower($match->Result ?? '');
                                    if (in_array($result, ['won', 'win', 'w'])) echo 'rgba(39, 174, 96, 0.1)';
                                    elseif (in_array($result, ['lost', 'loss', 'lose', 'l'])) echo 'rgba(231, 76, 60, 0.1)';
                                    elseif (in_array($result, ['draw', 'd', 'tie', 'tied'])) echo 'rgba(52, 152, 219, 0.1)';
                                    else echo 'rgba(241, 196, 15, 0.1)';
                                ?>;">
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary"><?php echo $match->Date ? date('M d', strtotime($match->Date)) : 'N/A'; ?></div>
                                        <div class="table-cell-secondary"><?php echo $match->Date ? date('l', strtotime($match->Date)) : ''; ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($match->OpponentTeam ?? 'Match'); ?></div>
                                        <div class="table-cell-details"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($match->Venue ?? 'Venue'); ?></div>
                                        <div class="table-cell-details"><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($match->TournamentName ?? 'Match'); ?></div>
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
                                        <button class="action-btn" onclick="viewMatchDetails(<?php echo $match->PerformanceID; ?>)" style="font-size: 12px; padding: 6px 10px; margin-right: 5px;" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (!isset($match->VerifiedStatus) || $match->VerifiedStatus === 'pending'): ?>
                                            <button class="action-btn" onclick="editMatchPerformance(<?php echo $match->PerformanceID; ?>)" style="font-size: 12px; padding: 6px 10px; margin-right: 5px;" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn" onclick="deleteMatchPerformance(<?php echo $match->PerformanceID; ?>)" style="font-size: 12px; padding: 6px 10px; background: #e74c3c; border-color: #e74c3c;" title="Delete">
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
                                        <h3 style="margin: 10px 0; color: #7f8c8d;">No Match History Yet</h3>
                                        <p>Add your match performance by clicking "Add" above.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="performanceModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s ease-in-out;">
    <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 3% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 750px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out; max-height: 85vh; overflow-y: auto;">
        <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-chart-bar" style="color: #fff;"></i> Add Performance Statistics
            </h2>
            <span class="close" onclick="closePerformanceModal()" style="color: #fff; font-size: 32px; font-weight: bold; cursor: pointer; transition: all 0.3s; padding: 5px; border-radius: 50%; opacity: 0.8;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.opacity='1';" onmouseout="this.style.backgroundColor='transparent'; this.style.opacity='0.8';">&times;</span>
        </div>

        <div class="modal-body" style="padding: 35px;">
            <form id="performanceStatsForm">
                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="matchSelect" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                        <i class="fas fa-trophy" style="color: #f39c12; margin-right: 8px;"></i> Select Match *
                    </label>
                    <select id="matchSelect" name="match_id" required style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;">
                        <option value="">-- Select a match --</option>
                    </select>
                </div>

                <fieldset style="border: 2px solid #3498db; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #3498db;"><i class="fas fa-baseball-ball"></i> Batting Statistics</legend>
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="runsScored" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Runs Scored</label>
                            <input type="number" id="runsScored" name="runs_scored" min="0" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="ballsFaced" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Balls Faced</label>
                            <input type="number" id="ballsFaced" name="balls_faced" min="0" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                    </div>
                </fieldset>

                <fieldset style="border: 2px solid #e74c3c; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #e74c3c;"><i class="fas fa-fire"></i> Bowling Statistics</legend>
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="wicketsTaken" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Wickets Taken</label>
                            <input type="number" id="wicketsTaken" name="wickets_taken" min="0" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="oversBowled" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Overs Bowled</label>
                            <input type="number" id="oversBowled" name="overs_bowled" min="0" step="0.1" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="runsConceded" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Runs Conceded</label>
                            <input type="number" id="runsConceded" name="runs_conceded" min="0" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                    </div>
                </fieldset>

                <fieldset style="border: 2px solid #27ae60; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #27ae60;"><i class="fas fa-hand-paper"></i> Fielding Statistics</legend>
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="catches" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Catches</label>
                            <input type="number" id="catches" name="catches" min="0" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="stumpings" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; font-size: 13px;">Stumpings</label>
                            <input type="number" id="stumpings" name="stumpings" min="0" value="0" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa;">
                        </div>
                    </div>
                </fieldset>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="performanceRating" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                        <i class="fas fa-star" style="color: #f1c40f; margin-right: 8px;"></i> Overall Performance Rating (0-10)
                    </label>
                    <input type="number" id="performanceRating" name="rating" min="0" max="10" step="0.1" value="0" style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;">
                    <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;">
                        <i class="fas fa-info-circle"></i> Rate your overall performance on a scale of 0 to 10
                    </small>
                </div>

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

<script src="<?php echo URLROOT; ?>/js/player/performance.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>