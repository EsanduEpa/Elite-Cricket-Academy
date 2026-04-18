document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-notifications-toggle]');
    const dropdown = document.querySelector('[data-notification-dropdown]');
    const list = document.querySelector('[data-notification-list]');
    const badge = document.querySelector('[data-notification-badge]');
    const markAllButton = document.querySelector('[data-notifications-mark-all]');

    if (!toggle || !dropdown || !list) {
        return;
    }

    const listUrl = toggle.dataset.listUrl;
    const markUrl = toggle.dataset.markUrl;
    let hasLoaded = false;
    let isLoading = false;

    const setBadge = function (count) {
        const unreadCount = Number(count || 0);
        if (!badge) {
            return;
        }

        if (unreadCount > 0) {
            badge.textContent = unreadCount > 9 ? '9+' : String(unreadCount);
            badge.classList.add('has-count');
        } else {
            badge.textContent = '';
            badge.classList.remove('has-count');
        }
    };

    const escapeHtml = function (value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const iconForType = function (type) {
        const icons = {
            welcome: 'fa-circle-info',
            payment: 'fa-credit-card',
            session: 'fa-calendar-check',
            reminder: 'fa-clock',
            rental: 'fa-dumbbell',
            warning: 'fa-triangle-exclamation',
            success: 'fa-circle-check',
        };

        return icons[type] || 'fa-bell';
    };

    const renderState = function (message) {
        list.innerHTML = `<div class="notification-dropdown__state">${escapeHtml(message)}</div>`;
    };

    const renderNotifications = function (notifications) {
        if (!notifications.length) {
            renderState('No notifications yet.');
            return;
        }

        list.innerHTML = notifications.map(function (notification) {
            const unreadClass = notification.is_read ? '' : ' is-unread';
            const actionUrl = notification.action_url || '#';
            const actionAttribute = notification.action_url ? `href="${escapeHtml(actionUrl)}"` : 'href="#"';

            return `
                <a ${actionAttribute} class="notification-dropdown__item${unreadClass}" data-notification-id="${Number(notification.id)}">
                    <span class="notification-dropdown__icon">
                        <i class="fas ${iconForType(notification.type)}"></i>
                    </span>
                    <span class="notification-dropdown__content">
                        <strong>${escapeHtml(notification.title)}</strong>
                        <span>${escapeHtml(notification.message)}</span>
                        <small>${escapeHtml(notification.time || 'Recently')}</small>
                    </span>
                </a>
            `;
        }).join('');
    };

    const loadNotifications = async function () {
        if (isLoading || !listUrl) {
            return;
        }

        isLoading = true;
        renderState('Loading notifications...');

        try {
            const response = await fetch(listUrl, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to load notifications.');
            }

            setBadge(data.unread_count);
            renderNotifications(data.notifications || []);
            hasLoaded = true;
        } catch (error) {
            renderState(error.message || 'Unable to load notifications right now.');
        } finally {
            isLoading = false;
        }
    };

    const markRead = async function (notificationId) {
        if (!markUrl) {
            return;
        }

        const body = new URLSearchParams();
        if (notificationId) {
            body.append('notification_id', notificationId);
        }

        try {
            const response = await fetch(markUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                credentials: 'same-origin',
                body: body.toString(),
            });
            const data = await response.json();
            if (data.success) {
                setBadge(data.unread_count);
            }
        } catch (error) {
            console.warn('Notification read update failed', error);
        }
    };

    toggle.addEventListener('click', function (event) {
        event.preventDefault();
        dropdown.classList.toggle('is-open');

        if (dropdown.classList.contains('is-open') && !hasLoaded) {
            loadNotifications();
        }
    });

    if (markAllButton) {
        markAllButton.addEventListener('click', async function () {
            await markRead();
            dropdown.querySelectorAll('.notification-dropdown__item.is-unread').forEach(function (item) {
                item.classList.remove('is-unread');
            });
        });
    }

    list.addEventListener('click', function (event) {
        const item = event.target.closest('[data-notification-id]');
        if (!item) {
            return;
        }

        const notificationId = item.dataset.notificationId;
        if (item.classList.contains('is-unread')) {
            item.classList.remove('is-unread');
            markRead(notificationId);
        }

        if (item.getAttribute('href') === '#') {
            event.preventDefault();
        }
    });

    document.addEventListener('click', function (event) {
        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.remove('is-open');
        }
    });

    loadNotifications();
});
