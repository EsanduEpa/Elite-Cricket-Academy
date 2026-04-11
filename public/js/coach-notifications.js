// Coach Notifications Page - JavaScript with Dummy Data

// Dummy Data
const notificationsData = [
    {
        id: 1,
        type: 'sessions',
        title: 'New Session Booking',
        message: 'Alex Smith has booked a private session for tomorrow at 10:00 AM',
        timestamp: '2025-10-21T09:30:00',
        isRead: false,
        priority: 'normal',
        actionUrl: '/coach/sessions',
        icon: 'fa-calendar-plus'
    },
    {
        id: 2,
        type: 'injuries',
        title: 'Injury Update',
        message: 'Emma Davis has been cleared for full training activities',
        timestamp: '2025-10-21T08:15:00',
        isRead: false,
        priority: 'high',
        actionUrl: '/coach/health',
        icon: 'fa-heartbeat'
    },
    {
        id: 3,
        type: 'sessions',
        title: 'Session Rescheduled',
        message: 'Sarah Wilson has requested to reschedule Friday\'s session to Saturday',
        timestamp: '2025-10-20T16:45:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/sessions',
        icon: 'fa-calendar-alt'
    },
    {
        id: 4,
        type: 'tournaments',
        title: 'Tournament Selection Update',
        message: 'Head Coach has finalized the team for Junior Championship 2025',
        timestamp: '2025-10-20T14:20:00',
        isRead: false,
        priority: 'high',
        actionUrl: '/coach/tournaments',
        icon: 'fa-trophy'
    },
    {
        id: 5,
        type: 'messages',
        title: 'Message from James Brown',
        message: 'Hi Coach, I have a question about my batting technique. Can we discuss after today\'s session?',
        timestamp: '2025-10-20T11:30:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/messages',
        icon: 'fa-comments'
    },
    {
        id: 6,
        type: 'injuries',
        title: 'Injury Report Filed',
        message: 'New injury report filed for Emily Clark - Minor knee ligament strain',
        timestamp: '2025-10-19T15:00:00',
        isRead: true,
        priority: 'high',
        actionUrl: '/coach/health',
        icon: 'fa-exclamation-triangle'
    },
    {
        id: 7,
        type: 'tournaments',
        title: 'Tournament Registration Open',
        message: 'Registration is now open for Summer Cricket League 2025. Deadline: Nov 20',
        timestamp: '2025-10-19T10:00:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/tournaments',
        icon: 'fa-trophy'
    },
    {
        id: 8,
        type: 'sessions',
        title: 'Session Cancelled',
        message: 'Michael Lee has cancelled tomorrow\'s group session due to personal reasons',
        timestamp: '2025-10-18T18:30:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/sessions',
        icon: 'fa-calendar-times'
    },
    {
        id: 9,
        type: 'messages',
        title: 'Message from Head Coach',
        message: 'Please submit your player recommendations for Regional Tournament by end of this week',
        timestamp: '2025-10-18T09:00:00',
        isRead: true,
        priority: 'high',
        actionUrl: '/coach/tournaments',
        icon: 'fa-envelope'
    },
    {
        id: 10,
        type: 'injuries',
        title: 'Recovery Progress Update',
        message: 'Alex Smith\'s recovery progress updated to 65%. Physiotherapy going well.',
        timestamp: '2025-10-17T14:45:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/health',
        icon: 'fa-sync-alt'
    },
    {
        id: 11,
        type: 'sessions',
        title: 'Session Reminder',
        message: 'You have 3 sessions scheduled for tomorrow',
        timestamp: '2025-10-17T08:00:00',
        isRead: true,
        priority: 'low',
        actionUrl: '/coach/sessions',
        icon: 'fa-bell'
    },
    {
        id: 12,
        type: 'tournaments',
        title: 'Tournament Results',
        message: 'Spring Tournament 2025 completed. Our team finished in 2nd place!',
        timestamp: '2025-10-16T17:00:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/tournaments',
        icon: 'fa-medal'
    },
    {
        id: 13,
        type: 'messages',
        title: 'Message from Sophie Anderson',
        message: 'Thank you for the excellent training session today! Looking forward to the next one.',
        timestamp: '2025-10-16T12:30:00',
        isRead: true,
        priority: 'low',
        actionUrl: '/coach/messages',
        icon: 'fa-comment'
    },
    {
        id: 14,
        type: 'injuries',
        title: 'Fitness Evaluation Scheduled',
        message: 'Sarah Wilson\'s fitness evaluation scheduled for October 18th',
        timestamp: '2025-10-15T10:15:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/health',
        icon: 'fa-clipboard-check'
    },
    {
        id: 15,
        type: 'sessions',
        title: 'New Player Registration',
        message: 'A new player, Ryan Thompson, has been assigned to your coaching group',
        timestamp: '2025-10-15T09:00:00',
        isRead: true,
        priority: 'normal',
        actionUrl: '/coach/players',
        icon: 'fa-user-plus'
    }
];

let currentFilter = 'all';
let currentSort = 'newest';
let displayedNotifications = [...notificationsData];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateNotificationCounts();
    loadNotifications();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            loadNotifications();
        });
    });

    // Sort select
    document.getElementById('sortSelect').addEventListener('change', function(e) {
        currentSort = e.target.value;
        loadNotifications();
    });

    // Mark all read button
    document.getElementById('markAllReadBtn').addEventListener('click', markAllAsRead);

    // Clear all button
    document.getElementById('clearAllBtn').addEventListener('click', clearAllNotifications);

    // Notification details modal
    document.getElementById('closeNotificationDetailsModal').addEventListener('click', closeNotificationDetailsModal);
    document.getElementById('closeDetailsBtn').addEventListener('click', closeNotificationDetailsModal);
}

// Update notification counts
function updateNotificationCounts() {
    const allCount = notificationsData.length;
    const unreadCount = notificationsData.filter(n => !n.isRead).length;
    
    document.getElementById('allCount').textContent = `(${allCount})`;
    document.getElementById('unreadCount').textContent = `(${unreadCount})`;
    document.getElementById('headerNotificationCount').textContent = `(${allCount})`;
    document.getElementById('sidebarNotificationBadge').textContent = unreadCount;
    
    if (unreadCount === 0) {
        document.getElementById('sidebarNotificationBadge').style.display = 'none';
    } else {
        document.getElementById('sidebarNotificationBadge').style.display = 'inline-block';
    }
}

// Load and display notifications
function loadNotifications() {
    const container = document.getElementById('notificationsList');
    const emptyState = document.getElementById('emptyState');
    
    // Filter notifications
    let filtered = notificationsData;
    
    if (currentFilter === 'unread') {
        filtered = notificationsData.filter(n => !n.isRead);
    } else if (currentFilter !== 'all') {
        filtered = notificationsData.filter(n => n.type === currentFilter);
    }
    
    // Sort notifications
    filtered.sort((a, b) => {
        const dateA = new Date(a.timestamp);
        const dateB = new Date(b.timestamp);
        return currentSort === 'newest' ? dateB - dateA : dateA - dateB;
    });
    
    displayedNotifications = filtered;
    
    if (filtered.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'flex';
        return;
    }
    
    container.style.display = 'block';
    emptyState.style.display = 'none';
    
    container.innerHTML = filtered.map(notification => createNotificationCard(notification)).join('');
    
    // Add click listeners
    document.querySelectorAll('.notification-card').forEach(card => {
        card.addEventListener('click', function() {
            const notificationId = parseInt(this.dataset.notificationId);
            viewNotificationDetails(notificationId);
        });
    });
    
    // Add mark as read listeners
    document.querySelectorAll('.btn-mark-read').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = parseInt(this.dataset.notificationId);
            markAsRead(notificationId);
        });
    });
    
    // Add delete listeners
    document.querySelectorAll('.btn-delete-notification').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = parseInt(this.dataset.notificationId);
            deleteNotification(notificationId);
        });
    });
}

// Create notification card HTML
function createNotificationCard(notification) {
    const timeAgo = getTimeAgo(notification.timestamp);
    const priorityClass = notification.priority === 'high' ? 'high-priority' : '';
    const unreadClass = !notification.isRead ? 'unread' : '';
    
    return `
        <div class="notification-card ${notification.type} ${unreadClass} ${priorityClass}" data-notification-id="${notification.id}">
            <div class="notification-icon ${notification.type}">
                <i class="fas ${notification.icon}"></i>
            </div>
            
            <div class="notification-content">
                <div class="notification-header">
                    <h4 class="notification-title">${notification.title}</h4>
                    <span class="notification-time">${timeAgo}</span>
                </div>
                <p class="notification-message">${notification.message}</p>
                <div class="notification-meta">
                    <span class="notification-type-badge ${notification.type}">
                        ${formatType(notification.type)}
                    </span>
                    ${notification.priority === 'high' ? '<span class="priority-badge">High Priority</span>' : ''}
                </div>
            </div>
            
            <div class="notification-actions">
                ${!notification.isRead ? `
                <button class="btn-mark-read" data-notification-id="${notification.id}" title="Mark as read">
                    <i class="fas fa-check"></i>
                </button>
                ` : ''}
                <button class="btn-delete-notification" data-notification-id="${notification.id}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            ${!notification.isRead ? '<div class="unread-indicator"></div>' : ''}
        </div>
    `;
}

// View notification details
function viewNotificationDetails(notificationId) {
    const notification = notificationsData.find(n => n.id === notificationId);
    if (!notification) return;
    
    // Mark as read when viewing
    if (!notification.isRead) {
        markAsRead(notificationId, false);
    }
    
    const modal = document.getElementById('notificationDetailsModal');
    const title = document.getElementById('notificationDetailsTitle');
    const body = document.getElementById('notificationDetailsBody');
    const actionBtn = document.getElementById('notificationActionBtn');
    
    title.textContent = notification.title;
    
    body.innerHTML = `
        <div class="notification-details-content">
            <div class="notification-details-header">
                <div class="notification-icon-large ${notification.type}">
                    <i class="fas ${notification.icon}"></i>
                </div>
                <div class="notification-details-info">
                    <span class="notification-type-badge ${notification.type}">
                        ${formatType(notification.type)}
                    </span>
                    ${notification.priority === 'high' ? '<span class="priority-badge">High Priority</span>' : ''}
                    <span class="notification-timestamp">${formatFullDate(notification.timestamp)}</span>
                </div>
            </div>
            
            <div class="notification-details-body">
                <p>${notification.message}</p>
            </div>
        </div>
    `;
    
    if (notification.actionUrl) {
        actionBtn.style.display = 'inline-flex';
        actionBtn.onclick = () => {
            window.location.href = notification.actionUrl;
        };
    } else {
        actionBtn.style.display = 'none';
    }
    
    modal.classList.add('active');
}

// Close notification details modal
function closeNotificationDetailsModal() {
    document.getElementById('notificationDetailsModal').classList.remove('active');
}

// Mark notification as read
function markAsRead(notificationId, reload = true) {
    const notification = notificationsData.find(n => n.id === notificationId);
    if (notification) {
        notification.isRead = true;
        updateNotificationCounts();
        if (reload) {
            loadNotifications();
        }
    }
}

// Mark all as read
function markAllAsRead() {
    const unreadCount = notificationsData.filter(n => !n.isRead).length;
    
    if (unreadCount === 0) {
        alert('All notifications are already marked as read');
        return;
    }
    
    if (confirm(`Mark all ${unreadCount} unread notifications as read?`)) {
        notificationsData.forEach(n => n.isRead = true);
        updateNotificationCounts();
        loadNotifications();
    }
}

// Delete notification
function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const index = notificationsData.findIndex(n => n.id === notificationId);
        if (index > -1) {
            notificationsData.splice(index, 1);
            updateNotificationCounts();
            loadNotifications();
        }
    }
}

// Clear all notifications
function clearAllNotifications() {
    if (notificationsData.length === 0) {
        alert('No notifications to clear');
        return;
    }
    
    if (confirm(`Are you sure you want to delete all ${notificationsData.length} notifications? This action cannot be undone.`)) {
        notificationsData.length = 0;
        updateNotificationCounts();
        loadNotifications();
    }
}

// Helper functions
function formatType(type) {
    const typeMap = {
        sessions: 'Sessions',
        injuries: 'Health & Injury',
        tournaments: 'Tournaments',
        messages: 'Messages'
    };
    return typeMap[type] || type;
}

function getTimeAgo(timestamp) {
    const now = new Date();
    const then = new Date(timestamp);
    const seconds = Math.floor((now - then) / 1000);
    
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)} minutes ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)} days ago`;
    
    return formatDate(timestamp);
}

function formatDate(timestamp) {
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    return new Date(timestamp).toLocaleDateString('en-US', options);
}

function formatFullDate(timestamp) {
    const options = { 
        weekday: 'long',
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(timestamp).toLocaleDateString('en-US', options);
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
