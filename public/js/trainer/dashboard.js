document.addEventListener('DOMContentLoaded', function() {
    initializeTrainerSidebar();
    initializeTrainerStatsCards();
    initializeBookingCalendar();
});

function initializeTrainerSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const mainContent = document.getElementById('mainContent') || document.querySelector('.main-content');

    if (!sidebarToggle || !sidebar || !mainContent) {
        return;
    }

    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });
}

function initializeTrainerStatsCards() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            card.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            card.style.transform = '';
        });
    });
}

function initializeBookingCalendar() {
    const calendarContainer = document.getElementById('bookingWeekCalendar');
    const weekRangeLabel = document.getElementById('currentWeekRange');
    const prevWeekBtn = document.getElementById('prevWeek');
    const nextWeekBtn = document.getElementById('nextWeek');
    const todayWeekBtn = document.getElementById('todayWeek');

    if (!calendarContainer || !weekRangeLabel || !prevWeekBtn || !nextWeekBtn || !todayWeekBtn) {
        return;
    }

    const sourceSessions = Array.isArray(window.trainerDashboardSessions)
        ? window.trainerDashboardSessions
        : [];

    const now = new Date();
    const calendarState = {
        weekStart: getWeekStartMonday(now)
    };

    const render = () => {
        renderWeeklyBookingCalendar(calendarContainer, weekRangeLabel, calendarState.weekStart, sourceSessions);
    };

    prevWeekBtn.addEventListener('click', function() {
        calendarState.weekStart = new Date(calendarState.weekStart.getFullYear(), calendarState.weekStart.getMonth(), calendarState.weekStart.getDate() - 7);
        render();
    });

    nextWeekBtn.addEventListener('click', function() {
        calendarState.weekStart = new Date(calendarState.weekStart.getFullYear(), calendarState.weekStart.getMonth(), calendarState.weekStart.getDate() + 7);
        render();
    });

    todayWeekBtn.addEventListener('click', function() {
        calendarState.weekStart = getWeekStartMonday(new Date());
        render();
    });

    render();
}

function getWeekStartMonday(date) {
    const cursor = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const day = cursor.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    cursor.setDate(cursor.getDate() + diff);
    return cursor;
}

function renderWeeklyBookingCalendar(container, weekRangeLabel, weekStart, sessions) {
    const weekDates = [];
    for (let i = 0; i < 7; i++) {
        weekDates.push(new Date(weekStart.getFullYear(), weekStart.getMonth(), weekStart.getDate() + i));
    }

    const weekEnd = weekDates[6];
    weekRangeLabel.textContent = `${formatDateForRange(weekStart)} - ${formatDateForRange(weekEnd)}`;

    const byDate = groupSessionsByDate(sessions);
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const todayKey = formatDateKey(new Date());

    let headerHtml = '';
    let bodyHtml = '';

    weekDates.forEach(function(dayDate, idx) {
        const dayKey = formatDateKey(dayDate);
        const isToday = dayKey === todayKey;
        const sessionsForDay = (byDate[dayKey] || []).slice().sort(compareSessionTimes);

        headerHtml += `
            <th>
                <div style="font-weight:700;">${dayNames[idx]}</div>
                <div style="font-size:12px;color:#888;">${formatDayLabel(dayDate)}</div>
            </th>`;

        if (!sessionsForDay.length) {
            bodyHtml += `<td class="cal-cell${isToday ? ' cal-today' : ''}"><div style="color:#ccc;font-size:11px;text-align:center;padding-top:20px;">-</div></td>`;
            return;
        }

        let cardsHtml = '';
        sessionsForDay.forEach(function(session) {
            const statusKey = normalizeStatusBadgeKey(String(session.Status || session.status || 'scheduled').toLowerCase());
            const statusLabel = toStatusLabel(statusKey);
            const isPrivate = isPrivateSession(session);
            const cardClass = statusKey === 'cancelled' ? 'cal-cancelled' : (isPrivate ? 'cal-private' : 'cal-program');
            const occurrenceId = Number(session.SessionID || session.OccurrenceID || 0);
            const detailsHref = occurrenceId > 0
                ? `${String(window.URLROOT || '').replace(/\/$/, '')}/staffslots/occurrence/${occurrenceId}`
                : '#';
            const count = Number(session.ParticipantCount || 0);
            const countLabel = `${count} player${count === 1 ? '' : 's'}`;

            cardsHtml += `
                <a href="${escapeAttr(detailsHref)}" class="cal-card ${cardClass}">
                    <div style="font-weight:700;margin-bottom:2px;">${escapeHtml(session.Name || 'Session')}</div>
                    <div>${escapeHtml(session.SessionType || 'Program Session')}</div>
                    <div style="opacity:.8;">${escapeHtml(session.Location || 'Academy')}</div>
                    <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;margin-top:4px;flex-wrap:wrap;">
                        <div style="font-size:11px;">
                            <i class="fas fa-users" style="font-size:10px;"></i>
                            ${escapeHtml(countLabel)}
                        </div>
                        <span class="cal-status-badge cal-status-${escapeAttr(statusKey)}">${escapeHtml(statusLabel)}</span>
                    </div>
                </a>`;
        });

        bodyHtml += `<td class="cal-cell${isToday ? ' cal-today' : ''}">${cardsHtml}</td>`;
    });

    container.innerHTML = `
        <table class="cal-calendar-table">
            <thead><tr>${headerHtml}</tr></thead>
            <tbody><tr>${bodyHtml}</tr></tbody>
        </table>`;
}

function groupSessionsByDate(sessions) {
    const grouped = {};
    sessions.forEach(function(session) {
        const dateKey = String(session.Date || session.date || '').slice(0, 10);
        if (!dateKey) {
            return;
        }
        if (!grouped[dateKey]) {
            grouped[dateKey] = [];
        }
        grouped[dateKey].push(session);
    });
    return grouped;
}

function compareSessionTimes(a, b) {
    const timeA = String(a.StartTime || a.start_time || '00:00:00');
    const timeB = String(b.StartTime || b.start_time || '00:00:00');
    return timeA.localeCompare(timeB);
}

function formatDateKey(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatDateForRange(date) {
    return `${date.getDate()} ${date.toLocaleDateString('en-US', { month: 'short' })} ${date.getFullYear()}`;
}

function formatDayLabel(date) {
    return `${date.getDate()} ${date.toLocaleDateString('en-US', { month: 'short' })}`;
}

function isPrivateSession(session) {
    const slotType = String(session.SlotType || '').toLowerCase();
    const sessionType = String(session.SessionType || '').toLowerCase();
    const mode = String(session.SessionMode || '').toLowerCase();
    return slotType === 'private' || slotType === 'facility_only' || sessionType.includes('private') || mode === 'individual';
}

function normalizeStatusBadgeKey(status) {
    if (status === 'cancelled' || status === 'canceled') {
        return 'cancelled';
    }
    if (status === 'completed' || status === 'attended' || status === 'missed') {
        return 'completed';
    }
    if (status === 'active' || status === 'confirmed') {
        return 'active';
    }
    if (status === 'upcoming' || status === 'pending' || status === 'scheduled') {
        return status;
    }
    return 'scheduled';
}

function toStatusLabel(statusKey) {
    return statusKey.charAt(0).toUpperCase() + statusKey.slice(1).replace(/_/g, ' ');
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeAttr(value) {
    return escapeHtml(value).replace(/\s+/g, ' ').trim();
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const background = type === 'success'
        ? '#16a34a'
        : type === 'error'
            ? '#dc2626'
            : '#2563eb';

    notification.textContent = message;
    notification.style.cssText = [
        'position:fixed',
        'top:20px',
        'right:20px',
        'z-index:10000',
        'padding:12px 16px',
        'border-radius:8px',
        'color:#fff',
        'font-size:14px',
        'font-weight:600',
        `background:${background}`,
        'box-shadow:0 12px 24px rgba(15,23,42,0.18)'
    ].join(';');

    document.body.appendChild(notification);
    window.setTimeout(() => notification.remove(), 2500);
}

function logoutUser() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    window.location.href = '/Elite/login/logout';
}

function updateClientProgress() {
    // Update progress bars animation
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 200);
    });
}

function updateWeeklySchedule() {
    // This would typically fetch from server
    console.log('Updating weekly schedule...');
}

function updateStatsCards() {
    // Animate stats cards on refresh
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.transform = 'scale(1.02)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 200);
        }, index * 100);
    });
}

// Enhanced session management
function handleSessionAction(action, sessionId) {
    switch(action) {
        case 'edit':
            alert('Edit session feature coming soon!');
            break;
        case 'complete':
            if (confirm('Mark this session as completed?')) {
                // Update session status visually
                const sessionRow = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionRow) {
                    const statusBadge = sessionRow.querySelector('.table-badge');
                    if (statusBadge) {
                        statusBadge.className = 'table-badge status-completed';
                        statusBadge.textContent = 'Completed';
                    }
                }
                
                alert('Session marked as completed!');
                // Here you would make an AJAX call to update the database
                setTimeout(refreshDashboard, 1000);
            }
            break;
        case 'delete':
            if (confirm('Are you sure you want to cancel this session?')) {
                // Update session status visually
                const sessionRow = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionRow) {
                    sessionRow.style.opacity = '0.5';
                    const statusBadge = sessionRow.querySelector('.table-badge');
                    if (statusBadge) {
                        statusBadge.className = 'table-badge status-cancelled';
                        statusBadge.textContent = 'Cancelled';
                    }
                }
                
                alert('Session cancelled!');
                // Here you would make an AJAX call to delete from database
                setTimeout(refreshDashboard, 1000);
            }
            break;
        default:
            console.log('Unknown action:', action);
    }
}

// Table interaction enhancements
function initializeTableFeatures() {
    // Add table row click handlers
    const tableRows = document.querySelectorAll('.dashboard-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger on button clicks
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            
            // Add selection visual feedback
            const allRows = document.querySelectorAll('.dashboard-table tbody tr');
            allRows.forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    
    // Initialize table sorting (basic)
    const tableHeaders = document.querySelectorAll('.dashboard-table th[data-sort]');
    tableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            console.log(`Sorting by: ${sortBy}`);
            // Add sorting logic here
        });
    });
}

// Add CSS for selected row
const style = document.createElement('style');
style.textContent = `
    .dashboard-table tbody tr.selected {
        background: rgba(74, 144, 226, 0.1) !important;
        border-left: 3px solid var(--primary-color);
    }
    
    .dashboard-table th[data-sort] {
        cursor: pointer;
        user-select: none;
    }
    
    .dashboard-table th[data-sort]:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Initialize table features when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeTableFeatures, 500);
});