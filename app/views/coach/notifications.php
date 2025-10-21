<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-notifications.css">

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
                        <a href="<?php echo URLROOT; ?>/coach/schedules" class="nav-link" data-tooltip="Schedules">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedules</span>
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
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
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
                            <i class="fas fa-bell"></i>
                            Notifications
                            <span class="notification-count" id="headerNotificationCount">(0)</span>
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Stay updated with important alerts and messages</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-secondary" id="markAllReadBtn">
                            <i class="fas fa-check-double"></i>
                            Mark All Read
                        </button>
                        <button class="btn-secondary" id="clearAllBtn">
                            <i class="fas fa-trash-alt"></i>
                            Clear All
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifications Content -->
            <div class="notifications-content">
                <!-- Notification Filters -->
                <div class="notification-filters">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">
                            <i class="fas fa-list"></i>
                            All <span class="tab-count" id="allCount">(0)</span>
                        </button>
                        <button class="filter-tab" data-filter="unread">
                            <i class="fas fa-envelope"></i>
                            Unread <span class="tab-count" id="unreadCount">(0)</span>
                        </button>
                        <button class="filter-tab" data-filter="sessions">
                            <i class="fas fa-calendar"></i>
                            Sessions
                        </button>
                        <button class="filter-tab" data-filter="injuries">
                            <i class="fas fa-heartbeat"></i>
                            Injuries
                        </button>
                        <button class="filter-tab" data-filter="tournaments">
                            <i class="fas fa-trophy"></i>
                            Tournaments
                        </button>
                        <button class="filter-tab" data-filter="messages">
                            <i class="fas fa-comments"></i>
                            Messages
                        </button>
                    </div>
                    
                    <div class="filter-actions">
                        <select id="sortSelect" class="filter-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="notifications-list" id="notificationsList">
                    <!-- Notifications will be loaded here via JavaScript -->
                </div>

                <!-- Empty State -->
                <div class="empty-notifications" id="emptyState" style="display: none;">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No notifications</h3>
                    <p>You're all caught up!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Details Modal -->
    <div class="modal" id="notificationDetailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="notificationDetailsTitle">Notification Details</h2>
                <button class="modal-close" id="closeNotificationDetailsModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="notificationDetailsBody">
                <!-- Notification details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="closeDetailsBtn">Close</button>
                <button class="btn-primary" id="notificationActionBtn" style="display: none;">
                    <i class="fas fa-arrow-right"></i>
                    View Details
                </button>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach-notifications.js"></script>

<script>
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
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
