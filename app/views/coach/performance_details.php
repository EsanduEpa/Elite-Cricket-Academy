<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">

<?php
    $player = $data['player'] ?? null;
    $overall = $data['overall'] ?? null;
    $matchHistory = $data['matchHistory'] ?? [];
    $playerName = $player ? ($player->Name ?? $player->PlayerName ?? 'Player') : 'Player';
    $playerId = (int)($player->PlayerID ?? 0);

    $matches = (int)($overall->MatchesPlayed ?? 0);
    $runs = (int)($overall->TotalRuns ?? 0);
    $hs = (int)($overall->HighestScore ?? 0);
    $batAvg = (float)($overall->BattingAverage ?? $overall->BattingAvg ?? 0);
    $wkts = (int)($overall->TotalWickets ?? $overall->Wickets ?? 0);
    $bowlAvg = (float)($overall->BowlingAverage ?? $overall->BowlingAvg ?? 0);
    $sr = (float)($overall->StrikeRate ?? 0);
    $eco = (float)($overall->EconomyRate ?? 0);
?>

<!-- Coach Dashboard Layout -->
<div class="coach-layout">
    <!-- Left Sidebar Panel -->
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Coach Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Slot Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link" data-tooltip="Performance">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health & Injury</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                        <i class="fas fa-comments"></i>
                        <span>Communication</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Requests</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Coach'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/coach/profile" class="profile-avatar" aria-label="Open coach profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-user"></i> <?php echo htmlspecialchars($playerName); ?></h1>
                <p>Match-wise performance</p>
            </div>
            <div class="header-actions" style="display:flex; gap:10px; align-items:center;">
                <a class="action-btn" href="<?php echo URLROOT; ?>/coach/performance" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none;">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="table-container" style="margin-top: 16px;">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th colspan="8">Overall</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Matches</strong><br><?php echo $matches; ?></td>
                        <td><strong>Runs</strong><br><?php echo $runs; ?></td>
                        <td><strong>Highest</strong><br><?php echo $hs; ?></td>
                        <td><strong>Bat Avg</strong><br><?php echo number_format($batAvg, 2); ?></td>
                        <td><strong>Wickets</strong><br><?php echo $wkts; ?></td>
                        <td><strong>Bowl Avg</strong><br><?php echo number_format($bowlAvg, 2); ?></td>
                        <td><strong>Strike Rate</strong><br><?php echo number_format($sr, 2); ?></td>
                        <td><strong>Economy</strong><br><?php echo number_format($eco, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-container" style="margin-top: 16px;">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Tournament</th>
                        <th>Opponent</th>
                        <th>Venue</th>
                        <th>Batting</th>
                        <th>Bowling</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($matchHistory)): ?>
                        <?php foreach ($matchHistory as $match): ?>
                            <?php
                                $batting = ((int)($match->RunsScored ?? 0)) . ' (' . ((int)($match->BallsFaced ?? 0)) . ')';
                                $bowling = ((float)($match->OversBowled ?? 0)) . ' ov, ' . ((int)($match->WicketsTaken ?? 0)) . ' wk, ' . ((int)($match->RunsConceded ?? 0)) . ' r';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($match->Date ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($match->TournamentName ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($match->OpponentTeam ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($match->Venue ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($batting); ?></td>
                                <td><?php echo htmlspecialchars($bowling); ?></td>
                                <td><?php echo htmlspecialchars($match->Result ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 24px;">No verified match performance found for this player.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
// Sidebar Toggle
(function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const icon = sidebarToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-angle-left');
                icon.classList.toggle('fa-angle-right');
            }
        });
    }
})();
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
