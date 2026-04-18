<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">

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

// Age group filter
(function() {
    const filter = document.getElementById('coachPerformanceAgeGroupFilter');
    const rows = Array.from(document.querySelectorAll('.coach-performance-row'));
    if (!filter || rows.length === 0) return;

    const applyFilter = () => {
        const selected = (filter.value || 'all').toLowerCase();
        rows.forEach((row) => {
            const raw = (row.getAttribute('data-age-groups') || '').toLowerCase();
            if (selected === 'all') {
                row.style.display = '';
                return;
            }
            const groups = raw.split(',').map(s => s.trim()).filter(Boolean);
            row.style.display = groups.includes(selected) ? '' : 'none';
        });
    };

    filter.addEventListener('change', applyFilter);
    applyFilter();
})();
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
