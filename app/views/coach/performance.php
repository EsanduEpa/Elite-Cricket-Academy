<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">

<!-- Coach Dashboard Layout -->
<div class="coach-layout">
    <?php $activeCoachNav = 'performance'; require APPROOT . '/views/inc/components/coach_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-line"></i> Performance</h1>
                <p>Overall stats of players assigned to you</p>
            </div>
        </div>

        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-top: 8px;">
            <div style="display:flex; gap:8px; align-items:center;">
                <label for="coachPerformanceAgeGroupFilter" style="font-weight:600; color:#333;">Age Group:</label>
                <select id="coachPerformanceAgeGroupFilter" class="coach-table-filter" style="min-width:180px;">
                    <option value="all">All Age Groups</option>
                    <?php foreach (($data['ageGroups'] ?? []) as $ageGroup): ?>
                        <option value="<?php echo htmlspecialchars(strtolower($ageGroup), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($ageGroup); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-container" style="margin-top: 16px;">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>Matches</th>
                        <th>Runs</th>
                        <th>HS</th>
                        <th>Bat Avg</th>
                        <th>Wkts</th>
                        <th>Bowl Avg</th>
                        <th>SR</th>
                        <th>Eco</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['players'])): ?>
                        <?php foreach ($data['players'] as $player): ?>
                            <?php
                                $pid = (int)($player->PlayerID ?? 0);
                                $stats = $data['overallStatsByPlayerId'][$pid] ?? null;
                                $ageGroups = strtolower(trim((string)($player->AssignmentAgeGroups ?? '')));
                                $ageGroups = $ageGroups !== '' ? $ageGroups : 'open';
                                $matches = (int)($stats->MatchesPlayed ?? 0);
                                $runs = (int)($stats->TotalRuns ?? 0);
                                $hs = (int)($stats->HighestScore ?? 0);
                                $batAvg = (float)($stats->BattingAverage ?? $stats->BattingAvg ?? 0);
                                $wkts = (int)($stats->TotalWickets ?? $stats->Wickets ?? 0);
                                $bowlAvg = (float)($stats->BowlingAverage ?? $stats->BowlingAvg ?? 0);
                                $sr = (float)($stats->StrikeRate ?? 0);
                                $eco = (float)($stats->EconomyRate ?? 0);
                            ?>
                            <tr class="coach-performance-row" data-age-groups="<?php echo htmlspecialchars($ageGroups, ENT_QUOTES, 'UTF-8'); ?>">
                                <td><?php echo htmlspecialchars($player->Name ?? $player->PlayerName ?? 'Player'); ?></td>
                                <td><?php echo $matches; ?></td>
                                <td><?php echo $runs; ?></td>
                                <td><?php echo $hs; ?></td>
                                <td><?php echo number_format($batAvg, 2); ?></td>
                                <td><?php echo $wkts; ?></td>
                                <td><?php echo number_format($bowlAvg, 2); ?></td>
                                <td><?php echo number_format($sr, 2); ?></td>
                                <td><?php echo number_format($eco, 2); ?></td>
                                <td>
                                    <a class="action-btn" href="<?php echo URLROOT; ?>/coach/performance_details/<?php echo $pid; ?>" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none;">
                                        <i class="fas fa-search"></i>
                                        More details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align:center; padding: 24px;">No assigned players found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/performance.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
