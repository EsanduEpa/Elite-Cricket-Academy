<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-requests.css">

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
                    
                    <li class="nav-item active">
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
                            <i class="fas fa-clipboard-list"></i>
                            Requests & Approvals
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Manage player requests and session changes</p>
                    </div>
                    <div class="header-actions">
                        <div class="stats-badge">
                            <span class="badge-label">Pending:</span>
                            <span class="badge-value" id="pendingCount"><?php echo $data['pendingCount']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requests Content -->
            <div class="requests-content">
                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="tab-btn active" data-filter="all">
                        <i class="fas fa-list"></i>
                        All <span class="count"><?php echo count($data['feedbacks']); ?></span>
                    </button>
                    <button class="tab-btn" data-filter="pending">
                        <i class="fas fa-clock"></i>
                        Pending <span class="count"><?php echo $data['pendingCount']; ?></span>
                    </button>
                    <button class="tab-btn" data-filter="reviewed">
                        <i class="fas fa-eye"></i>
                        Reviewed <span class="count"><?php echo $data['reviewedCount']; ?></span>
                    </button>
                    <button class="tab-btn" data-filter="resolved">
                        <i class="fas fa-check-circle"></i>
                        Resolved <span class="count"><?php echo $data['resolvedCount']; ?></span>
                    </button>
                </div>

                <!-- Requests List -->
                <div class="requests-list" id="requestsList">
                    <?php if (!empty($data['feedbacks'])): ?>
                        <?php foreach ($data['feedbacks'] as $feedback): ?>
                        <?php
                        $statusColors = ['pending' => '#f59e0b', 'reviewed' => '#4A90E2', 'resolved' => '#10b981'];
                        $sColor = $statusColors[$feedback->Status] ?? '#666';
                        $statusIcons = ['pending' => 'fa-clock', 'reviewed' => 'fa-eye', 'resolved' => 'fa-check-circle'];
                        $sIcon = $statusIcons[$feedback->Status] ?? 'fa-circle';
                        ?>
                        <div class="request-item" data-status="<?php echo $feedback->Status; ?>" style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 4px solid <?php echo $sColor; ?>;">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div style="display: flex; gap: 16px; align-items: start;">
                                    <div style="width: 45px; height: 45px; background: <?php echo $sColor; ?>20; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: <?php echo $sColor; ?>; flex-shrink: 0;">
                                        <i class="fas <?php echo $sIcon; ?>" style="font-size: 18px;"></i>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0 0 4px 0; color: #333; font-size: 16px;">
                                            Feedback from <?php echo htmlspecialchars($feedback->FromUserName); ?>
                                        </h4>
                                        <p style="margin: 0 0 8px 0; color: #666; line-height: 1.5;">
                                            <?php echo htmlspecialchars($feedback->Content); ?>
                                        </p>
                                        <div style="display: flex; gap: 16px; font-size: 12px; color: #999;">
                                            <span><i class="fas fa-calendar" style="margin-right: 4px;"></i><?php echo date('M d, Y', strtotime($feedback->CreatedDate)); ?></span>
                                            <span><i class="fas fa-tag" style="margin-right: 4px;"></i><?php echo ucfirst($feedback->Category); ?></span>
                                            <span>
                                                <i class="fas fa-star" style="margin-right: 4px; color: #f59e0b;"></i>
                                                <?php echo $feedback->Rating; ?>/5
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span style="background: <?php echo $sColor; ?>20; color: <?php echo $sColor; ?>; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; white-space: nowrap;">
                                    <?php echo $feedback->Status; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px; color: #999;">
                            <i class="fas fa-clipboard-check" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                            <h3 style="margin: 0 0 8px 0; color: #666;">No feedback requests</h3>
                            <p style="margin: 0;">Feedback from players will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
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

    // Filter Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const requestItems = document.querySelectorAll('.request-item');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            requestItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-status') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
