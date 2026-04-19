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
    const calendarGrid = document.getElementById('bookingCalendarGrid');
    const monthLabel = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    if (!calendarGrid || !monthLabel || !prevMonthBtn || !nextMonthBtn) {
        return;
    }

    const sourceSessions = Array.isArray(window.trainerDashboardSessions)
        ? window.trainerDashboardSessions
        : [];

    const calendarState = {
        current: new Date()
    };

    const render = () => {
        renderBookingCalendarGrid(calendarGrid, monthLabel, calendarState.current, sourceSessions);
    };

    prevMonthBtn.addEventListener('click', function() {
        calendarState.current = new Date(calendarState.current.getFullYear(), calendarState.current.getMonth() - 1, 1);
        render();
    });

    nextMonthBtn.addEventListener('click', function() {
        calendarState.current = new Date(calendarState.current.getFullYear(), calendarState.current.getMonth() + 1, 1);
        render();
    });

    render();
}

function renderBookingCalendarGrid(container, monthLabel, dateCursor, sessions) {
    const year = dateCursor.getFullYear();
    const month = dateCursor.getMonth();
    const today = new Date();

    monthLabel.textContent = dateCursor.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    container.innerHTML = '';

    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(function(name) {
        const headerCell = document.createElement('div');
        headerCell.className = 'calendar-day-header';
        headerCell.textContent = name;
        container.appendChild(headerCell);
    });

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const eventsByDate = buildEventsByDate(sessions);

    for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        empty.className = 'calendar-day empty';
        container.appendChild(empty);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const isoDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEvents = eventsByDate[isoDate] || { pending: 0, confirmed: 0, completed: 0, cancelled: 0, total: 0 };

        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        if (dayEvents.total > 0) {
            dayCell.classList.add('has-events');
        }
        if (
            day === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear()
        ) {
            dayCell.classList.add('today');
        }

        const dayNumber = document.createElement('div');
        dayNumber.className = 'calendar-day-number';
        dayNumber.textContent = String(day);
        dayCell.appendChild(dayNumber);

        if (dayEvents.total > 0) {
            const markers = document.createElement('div');
            markers.className = 'calendar-event-markers';

            ['pending', 'confirmed', 'completed', 'cancelled'].forEach(function(status) {
                if (dayEvents[status] > 0) {
                    const dot = document.createElement('span');
                    dot.className = `calendar-event-dot ${status}`;
                    dot.title = `${dayEvents[status]} ${status} session${dayEvents[status] > 1 ? 's' : ''}`;
                    markers.appendChild(dot);
                }
            });

            const count = document.createElement('div');
            count.className = 'calendar-day-count';
            count.textContent = `${dayEvents.total} session${dayEvents.total > 1 ? 's' : ''}`;

            dayCell.appendChild(markers);
            dayCell.appendChild(count);
        }

        container.appendChild(dayCell);
    }
}

function buildEventsByDate(sessions) {
    const summary = {};

    sessions.forEach(function(session) {
        const dateKey = String(session.Date || session.date || '').slice(0, 10);
        if (!dateKey) {
            return;
        }

        if (!summary[dateKey]) {
            summary[dateKey] = {
                pending: 0,
                confirmed: 0,
                completed: 0,
                cancelled: 0,
                total: 0
            };
        }

        const normalizedStatus = normalizeCalendarStatus(session.Status || session.status || 'upcoming');
        summary[dateKey][normalizedStatus] += 1;
        summary[dateKey].total += 1;
    });

    return summary;
}

function normalizeCalendarStatus(status) {
    const currentStatus = String(status || '').toLowerCase();

    if (currentStatus === 'cancelled' || currentStatus === 'canceled') {
        return 'cancelled';
    }
    if (currentStatus === 'completed' || currentStatus === 'attended') {
        return 'completed';
    }
    if (currentStatus === 'active' || currentStatus === 'confirmed') {
        return 'confirmed';
    }

    return 'pending';
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