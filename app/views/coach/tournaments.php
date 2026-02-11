<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournaments.css">

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
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
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
                            <i class="fas fa-trophy"></i>
                            Tournaments & Team Recommendations
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Manage tournament participation and player recommendations</p>
                    </div>
                    <div class="header-actions">
                    </div>
                </div>
            </div>

            <!-- Tournament Content -->
            <div class="tournament-content">
                <!-- Upcoming Events -->
                <div style="margin-bottom: 30px;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
                        <i class="fas fa-calendar-alt" style="color: #4A90E2; margin-right: 8px;"></i>Upcoming Events
                    </h3>
                    <?php if (!empty($data['upcoming_events'])): ?>
                    <div class="tournaments-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                        <?php foreach ($data['upcoming_events'] as $event): 
                            $statusColors = ['upcoming' => '#4A90E2', 'ongoing' => '#10b981', 'completed' => '#6b7280'];
                            $eColor = $statusColors[$event['Status']] ?? '#4A90E2';
                        ?>
                        <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-top: 4px solid <?php echo $eColor; ?>;">
                            <div style="padding: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                    <h4 style="margin: 0; color: #333; font-size: 16px;"><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <span style="background: <?php echo $eColor; ?>15; color: <?php echo $eColor; ?>; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase;"><?php echo $event['Status']; ?></span>
                                </div>
                                <?php if (!empty($event['description'])): ?>
                                <p style="margin: 0 0 12px 0; color: #666; font-size: 13px; line-height: 1.5;"><?php echo htmlspecialchars(substr($event['description'], 0, 120)); ?><?php echo strlen($event['description']) > 120 ? '...' : ''; ?></p>
                                <?php endif; ?>
                                <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: #888;">
                                    <span><i class="fas fa-calendar" style="margin-right: 4px;"></i><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                    <?php if (!empty($event['location'])): ?>
                                    <span><i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i><?php echo htmlspecialchars($event['location']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($event['event_type'])): ?>
                                    <span><i class="fas fa-tag" style="margin-right: 4px;"></i><?php echo htmlspecialchars(ucfirst($event['event_type'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; color: #999;">
                        <i class="fas fa-calendar-times" style="font-size: 36px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p style="margin: 0;">No upcoming events scheduled</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Past Events -->
                <div>
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
                        <i class="fas fa-history" style="color: #6b7280; margin-right: 8px;"></i>Past Events
                    </h3>
                    <?php if (!empty($data['past_events'])): ?>
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Event</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Date</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Location</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['past_events'] as $event): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 16px; font-weight: 500; color: #333;"><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td style="padding: 14px 16px; color: #666;"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td style="padding: 14px 16px; color: #666;"><?php echo htmlspecialchars($event['location'] ?? 'N/A'); ?></td>
                                    <td style="padding: 14px 16px; color: #666;"><?php echo htmlspecialchars(ucfirst($event['event_type'] ?? 'N/A')); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; color: #999;">
                        <i class="fas fa-archive" style="font-size: 36px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p style="margin: 0;">No past events found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- No modals needed - events are view-only for coaches -->

<script>
// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
