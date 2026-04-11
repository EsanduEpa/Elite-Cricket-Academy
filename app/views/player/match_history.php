<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css?v=<?php echo time(); ?>">
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
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn profile-logout-spacing">
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
                                <?php
                                    $result = strtolower($match->Result ?? '');
                                    $rowClass = 'match-history-row-other';
                                    if (in_array($result, ['won', 'win', 'w'])) {
                                        $rowClass = 'match-history-row-win';
                                    } elseif (in_array($result, ['lost', 'loss', 'lose', 'l'])) {
                                        $rowClass = 'match-history-row-loss';
                                    } elseif (in_array($result, ['draw', 'd', 'tie', 'tied'])) {
                                        $rowClass = 'match-history-row-draw';
                                    }
                                ?>
                                <tr class="<?php echo $rowClass; ?>">
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
                                        <button class="action-btn action-btn-xs action-btn-spaced" type="button" data-performance-action="view-match-performance" data-performance-id="<?php echo $match->PerformanceID; ?>" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (!isset($match->VerifiedStatus) || $match->VerifiedStatus === 'pending'): ?>
                                            <button class="action-btn action-btn-xs action-btn-spaced" type="button" data-performance-action="edit-match-performance" data-performance-id="<?php echo $match->PerformanceID; ?>" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn action-btn-xs action-btn-danger" type="button" data-performance-action="delete-match-performance" data-performance-id="<?php echo $match->PerformanceID; ?>" title="Delete">
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
    </div>
</div>

<div id="performanceModal" class="modal app-modal" aria-hidden="true">
    <div class="modal-content app-modal__dialog app-modal__dialog--wide">
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

        <div class="modal-body app-modal__body">
            <form id="performanceStatsForm" class="app-form">
                <div class="form-group form-group-spaced app-form-group app-form-group--full">
                    <label for="matchSelect" class="app-form-label app-form-label--strong">
                        <i class="fas fa-trophy app-form-icon app-form-icon--gold"></i> Select Match *
                    </label>
                    <select id="matchSelect" name="match_id" required class="app-form-control app-form-control--lg app-form-select">
                        <option value="">-- Select a match --</option>
                    </select>
                </div>

                <fieldset class="app-form-section app-form-section--blue">
                    <legend class="app-form-section__legend app-form-section__legend--blue"><i class="fas fa-baseball-ball"></i> Batting Statistics</legend>
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

                <fieldset class="app-form-section app-form-section--red">
                    <legend class="app-form-section__legend app-form-section__legend--red"><i class="fas fa-fire"></i> Bowling Statistics</legend>
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

                <fieldset class="app-form-section app-form-section--green">
                    <legend class="app-form-section__legend app-form-section__legend--green"><i class="fas fa-hand-paper"></i> Fielding Statistics</legend>
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

                <div class="form-group form-group-spaced app-form-group app-form-group--full">
                    <label for="performanceRating" class="app-form-label app-form-label--strong">
                        <i class="fas fa-star app-form-icon app-form-icon--gold"></i> Overall Performance Rating (0-10)
                    </label>
                    <input type="number" id="performanceRating" name="rating" min="0" max="10" step="0.1" value="0" class="app-form-control app-form-control--lg">
                    <small class="app-form-help">
                        <i class="fas fa-info-circle"></i> Rate your overall performance on a scale of 0 to 10
                    </small>
                </div>

                <div class="performance-submit-section app-form-submit-section">
                    <button type="submit" class="action-btn performance-submit-button">
                        <i class="fas fa-save"></i> Submit Performance Statistics
                    </button>
                    <p class="performance-submit-note app-form-note">
                        <i class="fas fa-shield-alt"></i> Your performance will be reviewed and verified by coaching staff
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/player/performance.js?v=<?php echo time(); ?>"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>