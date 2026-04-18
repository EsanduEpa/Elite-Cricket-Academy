<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/players.css">

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
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>

                <li class="nav-item">
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
                    <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                        <i class="fas fa-calendar"></i>
                        <span>Events</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1>
                        <i class="fas fa-users"></i>
                        Player Management
                    </h1>
                    <p class="coach-page-subtitle">View and manage your players</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="coach-stats-grid">
            <div class="coach-stat-card coach-stat-card--blue">
                <div class="coach-stat-card__content">
                    <div>
                        <h3 class="coach-stat-card__value"><?php echo $data['totalPlayers']; ?></h3>
                        <p class="coach-stat-card__label">Total Players</p>
                    </div>
                    <div class="coach-stat-card__icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="coach-stat-card coach-stat-card--green">
                <div class="coach-stat-card__content">
                    <div>
                        <h3 class="coach-stat-card__value"><?php echo $data['activePlayers']; ?></h3>
                        <p class="coach-stat-card__label">Active Players</p>
                    </div>
                    <div class="coach-stat-card__icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            <div class="coach-stat-card coach-stat-card--amber">
                <div class="coach-stat-card__content">
                    <div>
                        <h3 class="coach-stat-card__value"><?php echo $data['inactivePlayers']; ?></h3>
                        <p class="coach-stat-card__label">Inactive</p>
                    </div>
                    <div class="coach-stat-card__icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>

            <div class="coach-stat-card coach-stat-card--violet">
                <div class="coach-stat-card__content">
                    <div>
                        <h3 class="coach-stat-card__value"><?php echo $data['totalPlayers'] > 0 ? $data['totalPlayers'] : 0; ?></h3>
                        <p class="coach-stat-card__label">Assigned to You</p>
                    </div>
                    <div class="coach-stat-card__icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $coachAssignments = $data['coachAssignments'] ?? [];
        $coachProfile = $data['coachProfile'] ?? null;
        ?>
        <div class="coach-assignment-banner">
            <div class="coach-assignment-banner__header">
                <div>
                    <h2 class="coach-assignment-banner__title">Your Assignments</h2>
                    <p class="coach-assignment-banner__subtitle">Skills and age groups mapped to your profile</p>
                </div>
            </div>
            <div class="coach-assignment-badges">
            <?php foreach ($coachAssignments as $assignment): ?>
                <div class="coach-assignment-card">
                    <div class="coach-assignment-label"><?php echo htmlspecialchars($assignment['skill_label']); ?></div>
                    <div class="coach-age-group-list">
                        <?php if (!empty($assignment['age_groups'])): ?>
                            <?php foreach ($assignment['age_groups'] as $ageGroup): ?>
                                <span class="coach-age-group-pill"><?php echo htmlspecialchars($ageGroup); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="coach-age-group-pill">Open</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>

        <!-- Players Table -->
        <div class="coach-table-card">
            <div class="coach-table-card__header">
                <div class="coach-table-card__header-main">
                    <h2 class="coach-table-card__title">
                        <i class="fas fa-users"></i>
                        Player List
                    </h2>
                    <p class="coach-table-card__subtitle">Search, filter, and export the players assigned to you</p>
                </div>

                <div class="coach-table-actions">
                    <input type="text" class="coach-table-search" id="playerSearch" placeholder="Search players...">
                    <select class="coach-table-filter" id="playerStatusFilter">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <select class="coach-table-filter" id="playerAgeGroupFilter">
                        <option value="all">All Age Groups</option>
                        <?php foreach (($data['ageGroups'] ?? []) as $ageGroup): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($ageGroup), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($ageGroup); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="coach-table-action-btn coach-table-action-btn--ghost" id="resetPlayerFiltersBtn">
                        Reset
                    </button>
                    <button type="button" class="coach-table-action-btn coach-table-action-btn--primary" data-action="export-players">
                        <i class="fas fa-download"></i>
                        Export List
                    </button>
                </div>
            </div>
            
            <div class="coach-table-scroll coach-table-scroll--players">
                <table class="coach-data-table" id="playersTable">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Batting Style</th>
                            <th>Bowling Style</th>
                            <th>Assignment</th>
                            <th>Contact</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['players'])): ?>
                            <?php 
                            foreach ($data['players'] as $player): 
                                $playerSearchText = strtolower(trim(implode(' ', array_filter([
                                    $player->Name ?? '',
                                    $player->Email ?? '',
                                    $player->BattingStyle ?? '',
                                    $player->BowlingStyle ?? '',
                                    $player->AssignmentType ?? '',
                                    $player->AssignmentAgeGroups ?? '',
                                    $player->PhoneNumber ?? '',
                                    $player->Status ?? ''
                                ]))));
                                $playerStatus = strtolower(trim((string)($player->Status ?? 'unknown')));
                                $playerAgeGroups = strtolower(trim((string)($player->AssignmentAgeGroups ?? '')));
                            ?>
                            <tr data-player-search="<?php echo htmlspecialchars($playerSearchText, ENT_QUOTES, 'UTF-8'); ?>" data-player-status="<?php echo htmlspecialchars($playerStatus, ENT_QUOTES, 'UTF-8'); ?>" data-player-age-groups="<?php echo htmlspecialchars($playerAgeGroups, ENT_QUOTES, 'UTF-8'); ?>">
                                <td>
                                    <div class="coach-player-meta">
                                        <div class="coach-player-meta__name"><?php echo htmlspecialchars($player->Name); ?></div>
                                        <div class="coach-player-meta__email"><?php echo htmlspecialchars($player->Email); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span style="background: rgba(74, 144, 226, 0.15); color: #4A90E2; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        <?php echo htmlspecialchars($player->BattingStyle ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        <?php echo htmlspecialchars($player->BowlingStyle ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $assignColors = ['regular' => '#4A90E2', 'private' => '#8b5cf6', 'both' => '#f59e0b'];
                                    $assignmentLabel = trim((string)($player->AssignmentType ?? ''));
                                    $assignmentAgeGroups = trim((string)($player->AssignmentAgeGroups ?? ''));
                                    $assignColor = $assignColors[$assignmentLabel] ?? '#666';
                                    ?>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span style="background: rgba(0,0,0,0.05); color: <?php echo $assignColor; ?>; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: capitalize; width: fit-content;">
                                            <?php echo htmlspecialchars($assignmentLabel !== '' ? $assignmentLabel : 'N/A'); ?>
                                        </span>
                                        <?php if ($assignmentAgeGroups !== ''): ?>
                                            <span style="font-size: 12px; color: #666;">
                                                Age groups: <?php echo htmlspecialchars($assignmentAgeGroups); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="color: #666;"><?php echo htmlspecialchars($player->PhoneNumber ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($player->Status == 'active'): ?>
                                        <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>Active
                                        </span>
                                    <?php else: ?>
                                        <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i><?php echo ucfirst($player->Status); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center;">
                                    <div style="color: #999;">
                                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                                        <p style="margin: 0; font-size: 16px;">No players assigned to you yet</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Info -->
            <div class="coach-table-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #666; font-size: 14px;" id="playerCountLabel">
                    Showing <?php echo count($data['players']); ?> player(s) assigned to you
                </div>
            </div>
        </div>

        <!-- Player Achievements Section -->
        <div class="coach-table-card">
            <div class="coach-table-card__header">
                <div class="coach-table-card__header-main">
                    <h2 class="coach-table-card__title">Player Achievements</h2>
                    <p class="coach-table-card__subtitle">View all player achievements and milestones</p>
                </div>
                <div class="coach-table-actions">
                    <button type="button" class="coach-table-action-btn coach-table-action-btn--ghost">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <button type="button" class="coach-table-action-btn coach-table-action-btn--primary">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <div class="coach-table-scroll">
                <table class="coach-data-table">
                    <thead>
                        <tr style="background: rgba(74, 144, 226, 0.05); border-bottom: 2px solid rgba(74, 144, 226, 0.2);">
                            <th>Player</th>
                            <th>Achievement</th>
                            <th>Match</th>
                            <th>Tournament</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['achievements'])): ?>
                            <?php foreach ($data['achievements'] as $achievement): ?>
                                <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 16px;">
                                        <div class="coach-player-meta">
                                            <div class="coach-player-meta__name"><?php echo htmlspecialchars($achievement->PlayerName ?? 'Unknown'); ?></div>
                                            <div class="coach-player-meta__email"><?php echo htmlspecialchars($achievement->PlayerEmail ?? ''); ?></div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px;">
                                        <div style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($achievement->Achievement ?? 'Not specified'); ?></div>
                                    </td>
                                    <td style="padding: 16px; color: #666;">
                                        <?php echo htmlspecialchars($achievement->MatchName ?? 'N/A'); ?>
                                    </td>
                                    <td style="padding: 16px; color: #666;">
                                        <?php echo htmlspecialchars($achievement->Tournament ?? 'N/A'); ?>
                                    </td>
                                    <td style="padding: 16px; color: #666;">
                                        <?php echo date('M d, Y', strtotime($achievement->Date)); ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <?php 
                                            $statusClass = '';
                                            $statusIcon = '';
                                            switch(strtolower($achievement->VerifiedStatus ?? 'pending')) {
                                                case 'verified':
                                                    $statusClass = 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
                                                    $statusIcon = 'fa-check-circle';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
                                                    $statusIcon = 'fa-times-circle';
                                                    break;
                                                default:
                                                    $statusClass = 'background: rgba(245, 158, 11, 0.15); color: #f59e0b;';
                                                    $statusIcon = 'fa-clock';
                                            }
                                        ?>
                                        <span style="<?php echo $statusClass; ?> padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="fas <?php echo $statusIcon; ?>" style="font-size: 10px; margin-right: 6px;"></i><?php echo ucfirst($achievement->VerifiedStatus ?? 'Pending'); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <button type="button" data-action="open-achievement" data-achievement-id="<?php echo (int) $achievement->AchievementID; ?>" style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="padding: 40px; text-align: center;">
                                    <div style="color: #999;">
                                        <i class="fas fa-trophy" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                                        <p style="margin: 0; font-size: 16px;">No achievements recorded yet</p>
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

<!-- Achievement Details Modal -->
<div id="achievementModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="padding: 24px; border-bottom: 1px solid rgba(74, 144, 226, 0.2); display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #4A90E2, #357ABD); color: white;">
            <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
                <i class="fas fa-trophy" style="margin-right: 12px;"></i>Achievement Details
            </h2>
            <button type="button" data-action="close-achievement" style="background: rgba(255,255,255,0.2); border: none; color: white; min-width: 40px; height: 36px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; padding: 0 10px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="achievementModalBody" style="padding: 24px;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
    window.__COACH_PLAYERS_DATA = <?php echo json_encode($data['achievements'] ?? [], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
</script>
<script src="<?php echo URLROOT; ?>/js/coach/players.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
