document.addEventListener('DOMContentLoaded', function() {
    const notificationsList = document.getElementById('notificationsList');
    const notificationItems = Array.from(document.querySelectorAll('.notification-item'));
    const filterTabs = document.querySelectorAll('.filter-tab');
    const searchInput = document.getElementById('notificationSearch');
    const sortSelect = document.getElementById('sortSelect');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const compactViewBtn = document.getElementById('compactViewBtn');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const closeDetailsModalBtn = document.getElementById('closeNotificationDetailsModal');
    const actionBtn = document.getElementById('notificationActionBtn');
    const modal = document.getElementById('notificationDetailsModal');

    const appUrlRoot = window.APP_URLROOT || '';

    function updateEmptyState() {
        const visibleItems = notificationItems.filter(function(item) {
            return item.style.display !== 'none';
        });

        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = visibleItems.length === 0 ? 'block' : 'none';
        }
    }

    function setVisibleItems(predicate) {
        notificationItems.forEach(function(item) {
            item.style.display = predicate(item) ? 'grid' : 'none';
        });
        updateEmptyState();
    }

    function applySearchFilter(searchTerm) {
        const normalizedTerm = String(searchTerm || '').toLowerCase();
        setVisibleItems(function(item) {
            const title = item.querySelector('.notification-title')?.textContent.toLowerCase() || '';
            const message = item.querySelector('.notification-message')?.textContent.toLowerCase() || '';
            return title.includes(normalizedTerm) || message.includes(normalizedTerm);
        });
    }

    function applyTypeFilter(filter) {
        const typeMap = {
            sessions: 'session',
            injuries: 'injury',
            tournaments: 'event',
            messages: 'player'
        };

        setVisibleItems(function(item) {
            if (filter === 'all') {
                return true;
            }
            if (filter === 'unread') {
                return item.classList.contains('unread');
            }
            return item.getAttribute('data-type') === typeMap[filter];
        });
    }

    function updateCounts() {
        const unreadCountValue = notificationItems.filter(function(item) {
            return item.classList.contains('unread');
        }).length;

        const unreadCount = document.getElementById('unreadCount');
        const headerCount = document.getElementById('headerNotificationCount');
        if (unreadCount) unreadCount.textContent = '(' + unreadCountValue + ')';
        if (headerCount) headerCount.textContent = '(' + unreadCountValue + ')';
    }

    function markAsRead(id) {
        fetch(appUrlRoot + '/coach/markNotificationRead/' + id, { method: 'POST' })
            .then(function() {
                const item = document.querySelector('.notification-item[data-id="' + id + '"]');
                if (item) {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    const markBtn = item.querySelector('.btn-mark-read');
                    if (markBtn) {
                        markBtn.remove();
                    }
                    const badge = item.querySelector('.badge-unread');
                    if (badge) {
                        badge.remove();
                    }
                }
                updateCounts();
            })
            .catch(function(err) {
                console.error('Error marking notification as read:', err);
            });
    }

    function deleteNotification(id) {
        if (!confirm('Delete this notification?')) {
            return;
        }

        fetch(appUrlRoot + '/coach/deleteNotification/' + id, { method: 'POST' })
            .then(function() {
                const item = document.querySelector('.notification-item[data-id="' + id + '"]');
                if (item) {
                    item.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(function() {
                        item.remove();
                        updateEmptyState();
                        updateCounts();
                    }, 300);
                }
            })
            .catch(function(err) {
                console.error('Error deleting notification:', err);
            });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            applySearchFilter(this.value);
        });
    }

    filterTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            filterTabs.forEach(function(button) {
                button.classList.remove('active');
            });
            this.classList.add('active');
            applyTypeFilter(this.getAttribute('data-filter') || 'all');
        });
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch(appUrlRoot + '/coach/markAllNotificationsRead', { method: 'POST' })
                .then(function() {
                    notificationItems.forEach(function(item) {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        item.querySelector('.btn-mark-read')?.remove();
                        item.querySelector('.badge-unread')?.remove();
                    });
                    updateCounts();
                });
        });
    }

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            if (!confirm('Delete all notifications? This action cannot be undone.')) {
                return;
            }

            notificationItems.forEach(function(item) {
                const id = item.getAttribute('data-id');
                fetch(appUrlRoot + '/coach/deleteNotification/' + id, { method: 'POST' });
                item.remove();
            });
            updateEmptyState();
            updateCounts();
        });
    }

    if (listViewBtn) {
        listViewBtn.addEventListener('click', function() {
            listViewBtn.classList.add('active');
            compactViewBtn?.classList.remove('active');
            notificationsList?.classList.remove('compact-view');
        });
    }

    if (compactViewBtn) {
        compactViewBtn.addEventListener('click', function() {
            compactViewBtn.classList.add('active');
            listViewBtn?.classList.remove('active');
            notificationsList?.classList.add('compact-view');
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            if (!notificationsList) {
                return;
            }

            const items = Array.from(notificationsList.querySelectorAll('.notification-item'));
            items.sort(function(a, b) {
                const dateA = a.querySelector('.notification-time')?.textContent || '';
                const dateB = b.querySelector('.notification-time')?.textContent || '';
                return this.value === 'oldest' ? dateA.localeCompare(dateB) : dateB.localeCompare(dateA);
            }.bind(this));
            items.forEach(function(item) {
                notificationsList.appendChild(item);
            });
        });
    }

    document.querySelectorAll('[data-action="mark-notification-read"]').forEach(function(button) {
        button.addEventListener('click', function() {
            markAsRead(this.getAttribute('data-notification-id'));
        });
    });

    document.querySelectorAll('[data-action="delete-notification"]').forEach(function(button) {
        button.addEventListener('click', function() {
            deleteNotification(this.getAttribute('data-notification-id'));
        });
    });

    closeDetailsBtn?.addEventListener('click', function() {
        modal && (modal.style.display = 'none');
    });

    closeDetailsModalBtn?.addEventListener('click', function() {
        modal && (modal.style.display = 'none');
    });

    actionBtn?.addEventListener('click', function() {
        modal && (modal.style.display = 'none');
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && modal.style.display === 'block') {
            modal.style.display = 'none';
        }
    });

    modal?.addEventListener('click', function(event) {
        if (event.target === this) {
            this.style.display = 'none';
        }
    });

    updateCounts();
    updateEmptyState();
});