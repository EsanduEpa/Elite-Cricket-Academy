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
                            <span class="notification-count" id="headerNotificationCount">(<?php echo $data['unread_count']; ?>)</span>
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
                <!-- Notification Controls Section -->
                <div class="notification-controls">
                    <!-- Search Bar -->
                    <div class="search-wrapper">
                        <input 
                            type="text" 
                            id="notificationSearch" 
                            class="notification-search" 
                            placeholder="Search notifications..." 
                            aria-label="Search notifications"
                        >
                        <i class="fas fa-search search-icon"></i>
                    </div>

                    <!-- Notification Filters -->
                    <div class="notification-filters">
                        <div class="filter-tabs">
                            <button class="filter-tab active" data-filter="all" title="View all notifications">
                                <i class="fas fa-list"></i>
                                <span>All</span> 
                                <span class="tab-count" id="allCount">(<?php echo count($data['notifications']); ?>)</span>
                            </button>
                            <button class="filter-tab" data-filter="unread" title="View unread notifications">
                                <i class="fas fa-envelope"></i>
                                <span>Unread</span> 
                                <span class="tab-count" id="unreadCount">(<?php echo $data['unread_count']; ?>)</span>
                            </button>
                            <button class="filter-tab" data-filter="sessions" title="View session notifications">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Sessions</span>
                            </button>
                            <button class="filter-tab" data-filter="injuries" title="View injury notifications">
                                <i class="fas fa-heartbeat"></i>
                                <span>Injuries</span>
                            </button>
                            <button class="filter-tab" data-filter="tournaments" title="View tournament notifications">
                                <i class="fas fa-trophy"></i>
                                <span>Tournaments</span>
                            </button>
                            <button class="filter-tab" data-filter="messages" title="View messages">
                                <i class="fas fa-comments"></i>
                                <span>Messages</span>
                            </button>
                        </div>
                        
                        <div class="filter-controls">
                            <select id="sortSelect" class="filter-select" aria-label="Sort notifications">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                            </select>
                            <div class="view-toggle">
                                <button class="view-btn active" id="listViewBtn" title="List view">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button class="view-btn" id="compactViewBtn" title="Compact view">
                                    <i class="fas fa-bars"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="notifications-list" id="notificationsList">
                    <?php 
                    if (!empty($data['notifications'])):
                        foreach ($data['notifications'] as $notification): 
                            // Determine icon based on type
                            $iconMap = [
                                'session' => 'calendar-alt',
                                'injury' => 'heartbeat',
                                'event' => 'calendar-check',
                                'player' => 'user',
                                'system' => 'cog',
                                'welcome' => 'hand-peace'
                            ];
                            $icon = $iconMap[$notification->type] ?? 'bell';
                            
                            $readClass = $notification->is_read ? 'read' : 'unread';
                            $priority = $notification->priority ?? 'normal';
                    ?>
                    <div class="notification-item <?php echo $readClass; ?>" data-type="<?php echo $notification->type; ?>" data-id="<?php echo $notification->id; ?>" role="article" tabindex="0">
                        <!-- Unread Indicator Dot -->
                        <?php if (!$notification->is_read): ?>
                        <div class="unread-dot" title="Unread notification"></div>
                        <?php endif; ?>

                        <!-- Icon Section -->
                        <div class="notification-icon-wrapper">
                            <div class="notification-icon <?php echo $notification->type; ?>">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="notification-content-wrapper">
                            <div class="notification-header-top">
                                <h3 class="notification-title"><?php echo htmlspecialchars($notification->title); ?></h3>
                                <div class="notification-meta-badges">
                                    <?php if (!$notification->is_read): ?>
                                    <span class="badge-unread" title="Unread">
                                        <i class="fas fa-envelope"></i> Unread
                                    </span>
                                    <?php endif; ?>
                                    <span class="badge-type <?php echo $notification->type; ?>">
                                        <?php echo ucfirst($notification->type); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <p class="notification-message"><?php echo htmlspecialchars($notification->message); ?></p>
                            
                            <div class="notification-footer">
                                <span class="notification-time" title="<?php echo date('F d, Y H:i', strtotime($notification->created_at ?? 'now')); ?>">
                                    <i class="far fa-clock"></i> <?php echo $notification->time; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Actions Section -->
                        <div class="notification-actions-wrapper">
                            <?php if (!$notification->is_read): ?>
                            <button class="btn-action btn-mark-read" onclick="markAsRead(<?php echo $notification->id; ?>)" title="Mark as read" aria-label="Mark notification as read">
                                <i class="fas fa-check-circle"></i>
                                <span class="tooltip">Mark Read</span>
                            </button>
                            <?php endif; ?>
                            <button class="btn-action btn-delete" onclick="deleteNotification(<?php echo $notification->id; ?>)" title="Delete notification" aria-label="Delete notification">
                                <i class="fas fa-trash-alt"></i>
                                <span class="tooltip">Delete</span>
                            </button>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>

                <!-- Empty State -->
                <div class="empty-notifications" id="emptyState" style="display: <?php echo empty($data['notifications']) ? 'block' : 'none'; ?>;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationsList = document.getElementById('notificationsList');
    const notificationItems = document.querySelectorAll('.notification-item');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const searchInput = document.getElementById('notificationSearch');
    const sortSelect = document.getElementById('sortSelect');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const compactViewBtn = document.getElementById('compactViewBtn');

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

    // Search Functionality
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            notificationItems.forEach(item => {
                const title = item.querySelector('.notification-title')?.textContent.toLowerCase() || '';
                const message = item.querySelector('.notification-message')?.textContent.toLowerCase() || '';
                const matches = title.includes(searchTerm) || message.includes(searchTerm);
                item.style.display = matches ? 'grid' : 'none';
            });
            updateEmptyState();
        });
    }

    // Filter tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            notificationItems.forEach(item => {
                if (filter === 'all') {
                    item.style.display = 'grid';
                } else if (filter === 'unread') {
                    item.style.display = item.classList.contains('unread') ? 'grid' : 'none';
                } else {
                    const type = item.getAttribute('data-type');
                    const typeMap = {
                        'sessions': 'session',
                        'injuries': 'injury',
                        'tournaments': 'event',
                        'messages': 'player'
                    };
                    item.style.display = type === typeMap[filter] ? 'grid' : 'none';
                }
            });
            updateEmptyState();
        });
    });

    // Mark All Read
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch('<?php echo URLROOT; ?>/coach/markAllNotificationsRead', { method: 'POST' })
            .then(() => {
                notificationItems.forEach(item => {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    const markBtn = item.querySelector('.btn-mark-read');
                    if (markBtn) markBtn.remove();
                    const badge = item.querySelector('.badge-unread');
                    if (badge) badge.remove();
                });
                const unreadCount = document.getElementById('unreadCount');
                if (unreadCount) unreadCount.textContent = '(0)';
                const headerCount = document.getElementById('headerNotificationCount');
                if (headerCount) headerCount.textContent = '(0)';
            });
        });
    }

    // Clear All
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            if (confirm('Delete all notifications? This action cannot be undone.')) {
                notificationItems.forEach(item => {
                    const id = item.getAttribute('data-id');
                    fetch('<?php echo URLROOT; ?>/coach/deleteNotification/' + id, { method: 'POST' });
                    item.remove();
                });
                updateEmptyState();
            }
        });
    }

    // View Toggle
    if (listViewBtn) {
        listViewBtn.addEventListener('click', function() {
            listViewBtn.classList.add('active');
            compactViewBtn?.classList.remove('active');
            notificationsList.classList.remove('compact-view');
        });
    }
    if (compactViewBtn) {
        compactViewBtn.addEventListener('click', function() {
            compactViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            notificationsList.classList.add('compact-view');
        });
    }

    // Sort notifications
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            if (!notificationsList) return;
            const items = Array.from(notificationsList.querySelectorAll('.notification-item'));
            items.sort((a, b) => {
                const dateA = a.querySelector('.notification-time')?.textContent || '';
                const dateB = b.querySelector('.notification-time')?.textContent || '';
                if (this.value === 'oldest') {
                    return dateA.localeCompare(dateB);
                }
                return dateB.localeCompare(dateA);
            });
            items.forEach(item => notificationsList.appendChild(item));
        });
    }

    // Update empty state visibility
    function updateEmptyState() {
        const visibleItems = Array.from(notificationItems).filter(item => item.style.display !== 'none');
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = visibleItems.length === 0 ? 'flex' : 'none';
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('notificationDetailsModal');
            if (modal && modal.style.display === 'block') {
                document.getElementById('closeNotificationDetailsModal')?.click();
            }
        }
    });
});

// Mark single notification as read
function markAsRead(id) {
    fetch('<?php echo URLROOT; ?>/coach/markNotificationRead/' + id, { method: 'POST' })
    .then(() => {
        const item = document.querySelector('.notification-item[data-id="' + id + '"]');
        if (item) {
            item.classList.remove('unread');
            item.classList.add('read');
            const markBtn = item.querySelector('.btn-mark-read');
            if (markBtn) {
                markBtn.style.display = 'none';
            }
            const badge = item.querySelector('.badge-unread');
            if (badge) {
                badge.style.display = 'none';
            }
            // Update unread count
            const unreadCount = document.getElementById('unreadCount');
            const currentCount = parseInt(unreadCount?.textContent || 0) - 1;
            if (unreadCount) unreadCount.textContent = '(' + Math.max(0, currentCount) + ')';
        }
    }).catch(err => {
        console.error('Error marking notification as read:', err);
    });
}

// Delete notification
function deleteNotification(id) {
    if (!confirm('Delete this notification?')) return;
    fetch('<?php echo URLROOT; ?>/coach/deleteNotification/' + id, { method: 'POST' })
    .then(() => {
        const item = document.querySelector('.notification-item[data-id="' + id + '"]');
        if (item) {
            item.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => item.remove(), 300);
        }
    })
    .catch(err => {
        console.error('Error deleting notification:', err);
    });
}
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
