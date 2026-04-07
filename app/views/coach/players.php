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
                    <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Sessions</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link" data-tooltip="Recommendations">
                        <i class="fas fa-star"></i>
                        <span>Recommendations</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health & Injury</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
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
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">View and manage your players</p>
                </div>
                <div class="header-actions">
                    <button class="btn-primary">
                        <i class="fas fa-file-export"></i>
                        Export List
                    </button>
                    <button class="btn-secondary">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?php echo $data['totalPlayers']; ?></h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Total Players</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?php echo $data['activePlayers']; ?></h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Active Players</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-check" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?php echo $data['inactivePlayers']; ?></h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Inactive</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-clock" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;"><?php echo $data['totalPlayers'] > 0 ? $data['totalPlayers'] : 0; ?></h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Assigned to You</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Players Table -->
        <div class="table-container" style="background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid rgba(74, 144, 226, 0.2);">
                <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">
                    <i class="fas fa-users" style="color: #4A90E2; margin-right: 10px;"></i>
                    Player List
                </h2>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: rgba(74, 144, 226, 0.1);">
                        <tr>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Player</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Batting Style</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Bowling Style</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Assignment</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Contact</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['players'])): ?>
                            <?php 
                            $colors = ['#4A90E2', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4'];
                            $i = 0;
                            foreach ($data['players'] as $player): 
                                $initials = strtoupper(substr($player->Name, 0, 2));
                                $color = $colors[$i % count($colors)];
                                $i++;
                            ?>
                            <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 16px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, <?php echo $color; ?>, <?php echo $color; ?>cc); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;"><?php echo $initials; ?></div>
                                        <div>
                                            <div style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($player->Name); ?></div>
                                            <div style="font-size: 12px; color: #999;"><?php echo htmlspecialchars($player->Email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px;">
                                    <span style="background: rgba(74, 144, 226, 0.15); color: #4A90E2; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        <?php echo htmlspecialchars($player->BattingStyle ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td style="padding: 16px;">
                                    <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        <?php echo htmlspecialchars($player->BowlingStyle ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td style="padding: 16px;">
                                    <?php 
                                    $assignColors = ['regular' => '#4A90E2', 'private' => '#8b5cf6', 'both' => '#f59e0b'];
                                    $assignColor = $assignColors[$player->AssignmentType] ?? '#666';
                                    ?>
                                    <span style="background: rgba(0,0,0,0.05); color: <?php echo $assignColor; ?>; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: capitalize;">
                                        <?php echo htmlspecialchars($player->AssignmentType); ?>
                                    </span>
                                </td>
                                <td style="padding: 16px; color: #666;"><?php echo htmlspecialchars($player->PhoneNumber ?? 'N/A'); ?></td>
                                <td style="padding: 16px;">
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
            <div style="padding: 20px; border-top: 1px solid rgba(74, 144, 226, 0.2); display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #666; font-size: 14px;">
                    Showing <?php echo count($data['players']); ?> player(s) assigned to you
                </div>
            </div>
        </div>

        <!-- Player Achievements Section -->
        <div class="table-container" style="background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; margin-top: 30px;">
            <div style="padding: 24px; border-bottom: 1px solid rgba(74, 144, 226, 0.2);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="margin: 0; color: #333; font-size: 24px; font-weight: 700;">Player Achievements</h2>
                        <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">View all player achievements and milestones</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-filter" style="margin-right: 8px;"></i>Filter
                        </button>
                        <button style="background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-download" style="margin-right: 8px;"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(74, 144, 226, 0.05); border-bottom: 2px solid rgba(74, 144, 226, 0.2);">
                            <th style="padding: 16px; text-align: left; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Player</th>
                            <th style="padding: 16px; text-align: left; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Achievement</th>
                            <th style="padding: 16px; text-align: left; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Match</th>
                            <th style="padding: 16px; text-align: left; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Tournament</th>
                            <th style="padding: 16px; text-align: left; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Date</th>
                            <th style="padding: 16px; text-align: left; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Status</th>
                            <th style="padding: 16px; text-align: center; font-weight: 700; color: #4A90E2; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['achievements'])): ?>
                            <?php foreach ($data['achievements'] as $achievement): ?>
                                <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 16px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                                                <?php echo strtoupper(substr($achievement->PlayerName ?? 'NA', 0, 2)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($achievement->PlayerName ?? 'Unknown'); ?></div>
                                                <div style="font-size: 12px; color: #999;"><?php echo htmlspecialchars($achievement->PlayerEmail ?? ''); ?></div>
                                            </div>
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
                                        <button onclick="viewAchievementDetails(<?php echo $achievement->AchievementID; ?>)" style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
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
            <button onclick="closeAchievementModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="achievementModalBody" style="padding: 24px;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                if (mainContent) {
                    mainContent.style.marginLeft = '80px';
                }
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                if (mainContent) {
                    mainContent.style.marginLeft = '280px';
                }
            }
        });
    }
});

// Achievement modal functions
function viewAchievementDetails(id) {
    const achievements = <?php echo json_encode($data['achievements'] ?? []); ?>;
    const achievement = achievements.find(a => a.AchievementID === id);
    
    if (achievement) {
        const modalBody = document.getElementById('achievementModalBody');
        
        let statusClass = '';
        let statusIcon = '';
        switch((achievement.VerifiedStatus || 'pending').toLowerCase()) {
            case 'verified':
                statusClass = 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
                statusIcon = 'fa-check-circle';
                break;
            case 'rejected':
                statusClass = 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
                statusIcon = 'fa-times-circle';
                break;
            default:
                statusClass = 'background: rgba(245, 158, 11, 0.15); color: #f59e0b;';
                statusIcon = 'fa-clock';
        }
        
        modalBody.innerHTML = `
            <div class="achievement-details">
                <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(74, 144, 226, 0.2);">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 24px;">
                            ${achievement.PlayerName ? achievement.PlayerName.substring(0, 2).toUpperCase() : 'NA'}
                        </div>
                        <div>
                            <h3 style="margin: 0; color: #333; font-size: 20px;">${achievement.PlayerName || 'Unknown Player'}</h3>
                            <p style="margin: 4px 0 0 0; color: #666;">${achievement.PlayerEmail || ''}</p>
                            <p style="margin: 4px 0 0 0; color: #666;">${achievement.PlayerContact || ''}</p>
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; gap: 16px;">
                    <div style="padding: 16px; background: rgba(74, 144, 226, 0.05); border-radius: 8px;">
                        <strong style="color: #4A90E2; display: block; margin-bottom: 8px;">Achievement</strong>
                        <span style="color: #333; font-size: 18px; font-weight: 600;">${achievement.Achievement || 'Not specified'}</span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <strong style="color: #666; display: block; margin-bottom: 4px;">Match Name</strong>
                            <span style="color: #333;">${achievement.MatchName || 'N/A'}</span>
                        </div>
                        <div>
                            <strong style="color: #666; display: block; margin-bottom: 4px;">Tournament</strong>
                            <span style="color: #333;">${achievement.Tournament || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <strong style="color: #666; display: block; margin-bottom: 4px;">Date</strong>
                            <span style="color: #333;">${new Date(achievement.Date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                        </div>
                        <div>
                            <strong style="color: #666; display: block; margin-bottom: 4px;">Verification Status</strong>
                            <span style="${statusClass} padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                <i class="fas ${statusIcon}" style="margin-right: 6px;"></i>${(achievement.VerifiedStatus || 'Pending').charAt(0).toUpperCase() + (achievement.VerifiedStatus || 'Pending').slice(1)}
                            </span>
                        </div>
                    </div>
                    
                    ${achievement.Description ? `
                        <div style="padding: 16px; background: rgba(74, 144, 226, 0.05); border-radius: 8px;">
                            <strong style="color: #4A90E2; display: block; margin-bottom: 8px;">Description</strong>
                            <p style="margin: 0; color: #666; line-height: 1.6;">${achievement.Description}</p>
                        </div>
                    ` : ''}
                    
                    <div style="padding: 12px; background: rgba(139, 92, 246, 0.05); border-radius: 8px; border-left: 4px solid #8b5cf6;">
                        <div style="font-size: 12px; color: #8b5cf6; margin-bottom: 4px;">
                            <i class="fas fa-clock" style="margin-right: 6px;"></i>Created
                        </div>
                        <div style="color: #666; font-size: 13px;">
                            ${new Date(achievement.CreatedAt).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('achievementModal').style.display = 'flex';
    }
}

function closeAchievementModal() {
    document.getElementById('achievementModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('achievementModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAchievementModal();
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
