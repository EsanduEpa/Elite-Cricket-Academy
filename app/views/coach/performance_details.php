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

    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-user"></i> <?php echo htmlspecialchars($playerName); ?></h1>
                <p>Match-wise performance</p>
            </div>
            <div class="header-actions" style="display:flex; gap:10px; align-items:center;">
                <button type="button" class="action-btn primary" id="addPerfBtn">
                    <i class="fas fa-plus"></i> Add Performance
                </button>
                <a class="action-btn secondary" href="<?php echo URLROOT; ?>/coach/performance">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div style="margin-top: 12px;">
            <?php flash('coach_performance_message'); ?>
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
                        <th>Status</th>
                        <th>Verify</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($matchHistory)): ?>
                        <?php foreach ($matchHistory as $match): ?>
                            <?php
                                $batting = ((int)($match->RunsScored ?? 0)) . ' (' . ((int)($match->BallsFaced ?? 0)) . ')';
                                $bowling = ((float)($match->OversBowled ?? 0)) . ' ov, ' . ((int)($match->WicketsTaken ?? 0)) . ' wk, ' . ((int)($match->RunsConceded ?? 0)) . ' r';
                                $status = strtolower(trim((string)($match->VerifiedStatus ?? 'pending')));
                                $statusLabel = ucfirst($status);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($match->Date ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($match->TournamentName ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($match->OpponentTeam ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($match->Venue ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($batting); ?></td>
                                <td><?php echo htmlspecialchars($bowling); ?></td>
                                <td><?php echo htmlspecialchars($match->Result ?? ''); ?></td>
                                <td>
                                    <?php if ($status === 'verified'): ?>
                                        <span style="font-weight:700; color:#1b7a3a;">Verified</span>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <span style="font-weight:700; color:#b91c1c;">Rejected</span>
                                    <?php else: ?>
                                        <span style="font-weight:700; color:#b45309;">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($status === 'pending'): ?>
                                        <div style="display:flex; gap:8px; align-items:center;">
                                            <form method="post" action="<?php echo URLROOT; ?>/coach/updatePerformanceVerifyStatus" style="margin:0;">
                                                <input type="hidden" name="performance_id" value="<?php echo (int)($match->PerformanceID ?? 0); ?>">
                                                <input type="hidden" name="verify_status" value="verified">
                                                <button type="submit" class="action-btn success small">
                                                    Verify
                                                </button>
                                            </form>

                                            <form method="post" action="<?php echo URLROOT; ?>/coach/updatePerformanceVerifyStatus" style="margin:0;">
                                                <input type="hidden" name="performance_id" value="<?php echo (int)($match->PerformanceID ?? 0); ?>">
                                                <input type="hidden" name="verify_status" value="rejected">
                                                <button type="submit" class="action-btn danger small">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#6b7280;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding: 24px;">No match performance found for this player.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Add Performance Modal -->
<div id="addPerfModal" style="display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; margin:16px; box-shadow:0 8px 32px rgba(0,0,0,0.2);">
        <!-- Modal Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 24px 16px; border-bottom:1px solid #e5e7eb;">
            <h2 style="margin:0; font-size:1.15rem; font-weight:700; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-chart-line" style="color:#4A90E2;"></i>
                Add Performance for <?php echo htmlspecialchars($playerName); ?>
            </h2>
            <button type="button" id="addPerfClose" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280; line-height:1;">&times;</button>
        </div>

        <!-- Modal Body -->
        <div style="padding:20px 24px 24px;">
            <form method="POST" action="<?php echo URLROOT; ?>/coach/addPlayerPerformance">
                <input type="hidden" name="player_id" value="<?php echo $playerId; ?>">

                <!-- Match Selection -->
                <div style="margin-bottom:18px;">
                    <label style="display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem;">
                        <i class="fas fa-trophy" style="color:#f59e0b;"></i> Select Match *
                    </label>
                    <select name="match_id" required style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:0.9rem; background:#fff;">
                        <option value="">-- Select a match --</option>
                        <?php foreach (($data['availableMatches'] ?? []) as $m): ?>
                            <option value="<?php echo (int)$m->MatchID; ?>">
                                <?php
                                    $d = !empty($m->Date) ? date('M d, Y', strtotime($m->Date)) : 'Unknown date';
                                    echo htmlspecialchars(implode(' - ', array_filter([
                                        $d,
                                        trim((string)($m->OpponentTeam ?? '')),
                                        trim((string)($m->Venue ?? '')),
                                        trim((string)($m->TournamentName ?? '')),
                                    ])));
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Batting -->
                <fieldset style="border:1px solid #bfdbfe; border-radius:8px; padding:14px 16px; margin-bottom:14px;">
                    <legend style="font-weight:700; font-size:0.85rem; color:#1d4ed8; padding:0 6px;">
                        <i class="fas fa-baseball-ball"></i> Batting Statistics
                    </legend>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Runs Scored</label>
                            <input type="number" name="runs_scored" min="0" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Balls Faced</label>
                            <input type="number" name="balls_faced" min="0" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                    </div>
                </fieldset>

                <!-- Bowling -->
                <fieldset style="border:1px solid #fecaca; border-radius:8px; padding:14px 16px; margin-bottom:14px;">
                    <legend style="font-weight:700; font-size:0.85rem; color:#b91c1c; padding:0 6px;">
                        <i class="fas fa-fire"></i> Bowling Statistics
                    </legend>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Wickets Taken</label>
                            <input type="number" name="wickets_taken" min="0" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Overs Bowled</label>
                            <input type="number" name="overs_bowled" min="0" step="0.1" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Runs Conceded</label>
                            <input type="number" name="runs_conceded" min="0" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                    </div>
                </fieldset>

                <!-- Fielding -->
                <fieldset style="border:1px solid #bbf7d0; border-radius:8px; padding:14px 16px; margin-bottom:14px;">
                    <legend style="font-weight:700; font-size:0.85rem; color:#15803d; padding:0 6px;">
                        <i class="fas fa-hand-paper"></i> Fielding Statistics
                    </legend>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Catches</label>
                            <input type="number" name="catches" min="0" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Stumpings</label>
                            <input type="number" name="stumpings" min="0" value="0" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:0.9rem; box-sizing:border-box;">
                        </div>
                    </div>
                </fieldset>

                <!-- Rating -->
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem;">
                        <i class="fas fa-star" style="color:#f59e0b;"></i> Overall Performance Rating (0–10)
                    </label>
                    <input type="number" name="rating" min="0" max="10" step="0.1" value="0" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:0.9rem; box-sizing:border-box;">
                </div>

                <!-- Actions -->
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" id="addPerfCancel" style="padding:9px 20px; border:1px solid #d1d5db; border-radius:8px; background:#fff; cursor:pointer; font-size:0.9rem;">Cancel</button>
                    <button type="submit" class="action-btn" style="padding:9px 20px;">
                        <i class="fas fa-save"></i> Save Performance
                    </button>
                </div>
            </form>
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

// Add Performance Modal
(function() {
    const modal = document.getElementById('addPerfModal');
    const openBtn = document.getElementById('addPerfBtn');
    const closeBtn = document.getElementById('addPerfClose');
    const cancelBtn = document.getElementById('addPerfCancel');

    function openModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
})();
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
