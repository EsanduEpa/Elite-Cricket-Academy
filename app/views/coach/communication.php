<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-communication.css">

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
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                            <i class="fas fa-comments"></i>
                            <span>Communication</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/reports" class="nav-link" data-tooltip="Reports">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Requests</span>
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
                            <i class="fas fa-comments"></i>
                            Communication & Feedback
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Message players, trainers, and admins</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-primary" id="newMessageBtn">
                            <i class="fas fa-plus"></i>
                            New Message
                        </button>
                        <button class="btn-primary" id="newAnnouncementBtn">
                            <i class="fas fa-bullhorn"></i>
                            Send Announcement
                        </button>
                    </div>
                </div>
            </div>

            <!-- Communication Content -->
            <div class="communication-content" style="padding: 20px;">
                <!-- Feedback Received -->
                <div style="background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden;">
                    <div style="padding: 24px; border-bottom: 1px solid rgba(74, 144, 226, 0.2);">
                        <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">
                            <i class="fas fa-star" style="color: #f59e0b; margin-right: 10px;"></i>
                            Feedback Received from Players
                        </h2>
                        <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">View feedback and ratings from your assigned players</p>
                    </div>

                    <?php if (!empty($data['feedbacks'])): ?>
                        <?php foreach ($data['feedbacks'] as $feedback): ?>
                        <div style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05); transition: background 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #4A90E2, #357ABD); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                        <?php echo strtoupper(substr($feedback->FromUserName, 0, 2)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #333; font-size: 16px;"><?php echo htmlspecialchars($feedback->FromUserName); ?></div>
                                        <div style="font-size: 12px; color: #999;"><?php echo htmlspecialchars($feedback->FromUserEmail); ?></div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <!-- Rating Stars -->
                                    <div style="color: #f59e0b;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="<?php echo $i <= $feedback->Rating ? 'color: #f59e0b;' : 'color: #ddd;'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <!-- Status Badge -->
                                    <?php
                                    $statusColors = ['pending' => '#f59e0b', 'reviewed' => '#4A90E2', 'resolved' => '#10b981'];
                                    $sColor = $statusColors[$feedback->Status] ?? '#666';
                                    ?>
                                    <span style="background: <?php echo $sColor; ?>20; color: <?php echo $sColor; ?>; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: capitalize;">
                                        <?php echo $feedback->Status; ?>
                                    </span>
                                </div>
                            </div>
                            <p style="margin: 0; color: #555; line-height: 1.6; padding-left: 57px;">
                                <?php echo htmlspecialchars($feedback->Content); ?>
                            </p>
                            <div style="padding-left: 57px; margin-top: 8px; font-size: 12px; color: #999;">
                                <i class="fas fa-clock" style="margin-right: 4px;"></i>
                                <?php echo date('M d, Y', strtotime($feedback->CreatedDate)); ?>
                                <span style="margin: 0 8px;">•</span>
                                <i class="fas fa-tag" style="margin-right: 4px;"></i>
                                <?php echo ucfirst($feedback->Category); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding: 60px; text-align: center; color: #999;">
                            <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                            <h3 style="margin: 0 0 8px 0; color: #666;">No feedback yet</h3>
                            <p style="margin: 0;">Feedback from players will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script>
// Sidebar Toggle Functionality
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
