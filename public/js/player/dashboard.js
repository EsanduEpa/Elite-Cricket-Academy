// Simplified Player Dashboard JavaScript - Matching Homepage Simplicity
// Basic interactions only, similar to homepage functionality

// Hydrate server-provided dashboard data (replaces inline scripts in the view)
(function hydrateDashboardData() {
    if (window.dashboardData) return;
    const el = document.getElementById('dashboardData');
    if (!el) return;
    try {
        window.dashboardData = JSON.parse(el.textContent || '{}');
    } catch (_err) {
        window.dashboardData = {};
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    initializeSimplePlayerDashboard();
});

// Simple dashboard initialization
function initializeSimplePlayerDashboard() {
    // Simple sidebar toggle
    initializeSimpleSidebar();
    
    // Basic navigation
    initializeSimpleNavigation();
    
    // Simple card animations (like homepage)
    setTimeout(animateCards, 300);
    
    // Simple button interactions
    initializeSimpleButtons();
}

// Simple sidebar functionality (similar to homepage button interactions)
function initializeSimpleSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('playerSidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('sidebar-open');
                }
            }
        });
    }
}

// Simple navigation (similar to homepage anchor scrolling)
function initializeSimpleNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const href = this.getAttribute('href');
            
            // Only handle hash links for internal sections
            if (href && href.startsWith('#')) {
                // Remove active class from all items
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.closest('.nav-item').classList.add('active');
            }
            // Let normal links navigate naturally (like homepage buttons)
        });
    });
}

// Simple card animations (similar to homepage card animations)
function animateCards() {
    const cards = document.querySelectorAll('.stat-card, .schedule-section, .quick-actions, .profile-section');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Simple button interactions (like homepage buttons)
function initializeSimpleButtons() {
    const actionButtons = document.querySelectorAll('.action-btn');
    
    actionButtons.forEach(button => {
        // Add simple hover feedback
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Simple click feedback
        button.addEventListener('click', function() {
            this.style.transform = 'translateY(0)';
            setTimeout(() => {
                this.style.transform = 'translateY(-1px)';
            }, 100);
        });
    });
}

// Simple scroll effects (similar to homepage)
window.addEventListener('scroll', function() {
    const header = document.querySelector('.dashboard-header');
    if (header && window.scrollY > 50) {
        header.style.boxShadow = '0 4px 15px rgba(0,0,0,0.15)';
    } else if (header) {
        header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    }
});

// ========================================
// DASHBOARD CALENDAR FUNCTIONALITY
// ========================================

// Calendar State
let dashCalendarView = 'month';
let dashCalendarMonth = new Date().getMonth();
let dashCalendarYear = new Date().getFullYear();
let dashCalendarDay = new Date().getDate();
let dashSelectedDate = new Date();
let dashWeekStart = null;

// Calendar Events - injected from server via PHP
const dashCalendarEvents = window.dashboardData?.calendarEvents || [];

// Initialize Dashboard Calendar
if (document.getElementById('dashMonthView')) {
    initializeDashboardCalendar();
}

function initializeDashboardCalendar() {
    // Setup view buttons
    const viewButtons = document.querySelectorAll('.compact-calendar .view-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            dashCalendarView = this.getAttribute('data-view');
            renderDashCalendarView();
        });
    });
    
    // Setup navigation buttons
    const prevBtn = document.getElementById('dashPrevPeriod');
    const nextBtn = document.getElementById('dashNextPeriod');
    const todayBtn = document.getElementById('dashTodayBtn');
    
    if (prevBtn) prevBtn.addEventListener('click', () => navigateDashPeriod(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => navigateDashPeriod(1));
    if (todayBtn) todayBtn.addEventListener('click', goToDashToday);
    
    // Initial render
    renderDashCalendarView();
}

function goToDashToday() {
    const today = new Date();
    dashCalendarMonth = today.getMonth();
    dashCalendarYear = today.getFullYear();
    dashCalendarDay = today.getDate();
    dashSelectedDate = new Date(today);
    dashWeekStart = null;
    renderDashCalendarView();
}

function navigateDashPeriod(direction) {
    if (dashCalendarView === 'month') {
        dashCalendarMonth += direction;
        if (dashCalendarMonth < 0) {
            dashCalendarMonth = 11;
            dashCalendarYear--;
        } else if (dashCalendarMonth > 11) {
            dashCalendarMonth = 0;
            dashCalendarYear++;
        }
    } else if (dashCalendarView === 'week') {
        if (!dashWeekStart) {
            dashWeekStart = new Date(dashSelectedDate);
            dashWeekStart.setDate(dashWeekStart.getDate() - dashWeekStart.getDay());
        }
        dashWeekStart.setDate(dashWeekStart.getDate() + (direction * 7));
        dashSelectedDate = new Date(dashWeekStart);
        dashCalendarMonth = dashWeekStart.getMonth();
        dashCalendarYear = dashWeekStart.getFullYear();
        dashCalendarDay = dashWeekStart.getDate();
    } else if (dashCalendarView === 'day') {
        dashSelectedDate.setDate(dashSelectedDate.getDate() + direction);
        dashCalendarDay = dashSelectedDate.getDate();
        dashCalendarMonth = dashSelectedDate.getMonth();
        dashCalendarYear = dashSelectedDate.getFullYear();
    }
    
    renderDashCalendarView();
}

function renderDashCalendarView() {
    const monthView = document.getElementById('dashMonthView');
    const weekView = document.getElementById('dashWeekView');
    const dayView = document.getElementById('dashDayView');
    
    if (!monthView || !weekView || !dayView) return;
    
    monthView.style.display = 'none';
    weekView.style.display = 'none';
    dayView.style.display = 'none';
    
    if (dashCalendarView === 'month') {
        monthView.style.display = 'grid';
        renderDashMonthView();
    } else if (dashCalendarView === 'week') {
        weekView.style.display = 'grid';
        renderDashWeekView();
    } else if (dashCalendarView === 'day') {
        dayView.style.display = 'grid';
        renderDashDayView();
    }
}

function renderDashMonthView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    const periodEl = document.getElementById('dashCurrentPeriod');
    if (periodEl) {
        periodEl.textContent = `${monthNames[dashCalendarMonth]} ${dashCalendarYear}`;
    }
    
    const calendarGrid = document.getElementById('dashMonthView');
    if (!calendarGrid) return;
    
    calendarGrid.innerHTML = '';
    
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day header';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
    });
    
    const firstDay = new Date(dashCalendarYear, dashCalendarMonth, 1).getDay();
    const daysInMonth = new Date(dashCalendarYear, dashCalendarMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(dashCalendarYear, dashCalendarMonth, 0).getDate();
    
    for (let i = firstDay - 1; i >= 0; i--) {
        const dayCell = createDashMonthDayCell(daysInPrevMonth - i, true, dashCalendarMonth - 1);
        calendarGrid.appendChild(dayCell);
    }
    
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today.getDate() && 
                       dashCalendarMonth === today.getMonth() && 
                       dashCalendarYear === today.getFullYear();
        const dayCell = createDashMonthDayCell(day, false, dashCalendarMonth, isToday);
        calendarGrid.appendChild(dayCell);
    }
    
    const totalCells = firstDay + daysInMonth;
    const remainingCells = 7 - (totalCells % 7);
    if (remainingCells < 7) {
        for (let day = 1; day <= remainingCells; day++) {
            const dayCell = createDashMonthDayCell(day, true, dashCalendarMonth + 1);
            calendarGrid.appendChild(dayCell);
        }
    }
}

function createDashMonthDayCell(day, isOtherMonth, month, isToday = false) {
    const dayCell = document.createElement('div');
    dayCell.className = 'calendar-day';
    
    if (isOtherMonth) dayCell.classList.add('other-month');
    if (isToday) dayCell.classList.add('today');
    
    const cellDate = new Date(dashCalendarYear, month, day);
    if (!isOtherMonth && 
        cellDate.getDate() === dashSelectedDate.getDate() && 
        cellDate.getMonth() === dashSelectedDate.getMonth() && 
        cellDate.getFullYear() === dashSelectedDate.getFullYear()) {
        dayCell.classList.add('selected');
    }
    
    const dateNumber = document.createElement('div');
    dateNumber.className = 'date-number';
    dateNumber.textContent = day;
    dayCell.appendChild(dateNumber);
    
    const dateStr = `${dashCalendarYear}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayEvents = dashCalendarEvents.filter(event => event.date === dateStr);
    
    if (dayEvents.length > 0 && !isOtherMonth) {
        const eventIndicator = document.createElement('div');
        eventIndicator.className = 'event-indicator';
        
        dayEvents.forEach(event => {
            const eventDot = document.createElement('span');
            eventDot.className = `event-dot ${event.type}`;
            eventDot.title = event.title;
            eventIndicator.appendChild(eventDot);
        });
        
        dayCell.appendChild(eventIndicator);
    }
    
    if (!isOtherMonth) {
        dayCell.addEventListener('click', () => {
            dashSelectedDate = new Date(dashCalendarYear, month, day);
            dashCalendarDay = day;
            renderDashCalendarView();
        });
    }
    
    return dayCell;
}

function renderDashWeekView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    if (!dashWeekStart) {
        dashWeekStart = new Date(dashSelectedDate);
        dashWeekStart.setDate(dashWeekStart.getDate() - dashWeekStart.getDay());
    }
    
    const weekEnd = new Date(dashWeekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    const periodEl = document.getElementById('dashCurrentPeriod');
    if (periodEl) {
        periodEl.textContent = `${monthNames[dashWeekStart.getMonth()]} ${dashWeekStart.getDate()} - ${monthNames[weekEnd.getMonth()]} ${weekEnd.getDate()}, ${dashWeekStart.getFullYear()}`;
    }
    
    const weekView = document.getElementById('dashWeekView');
    if (!weekView) return;
    
    weekView.innerHTML = '';
    
    const corner = document.createElement('div');
    corner.className = 'time-slot';
    corner.textContent = 'Time';
    weekView.appendChild(corner);
    
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const today = new Date();
    
    for (let i = 0; i < 7; i++) {
        const dayDate = new Date(dashWeekStart);
        dayDate.setDate(dayDate.getDate() + i);
        
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        
        const isToday = dayDate.toDateString() === today.toDateString();
        const isSelected = dayDate.toDateString() === dashSelectedDate.toDateString();
        
        if (isToday) dayHeader.classList.add('today');
        if (isSelected) dayHeader.classList.add('selected');
        
        dayHeader.innerHTML = `<strong>${dayNames[i]}</strong><br>${dayDate.getDate()}`;
        
        dayHeader.addEventListener('click', () => {
            dashSelectedDate = new Date(dayDate);
            dashCalendarDay = dashSelectedDate.getDate();
            dashCalendarMonth = dashSelectedDate.getMonth();
            dashCalendarYear = dashSelectedDate.getFullYear();
            
            document.querySelectorAll('.compact-calendar .view-btn').forEach(b => b.classList.remove('active'));
            const dayBtn = document.querySelector('.compact-calendar .view-btn[data-view="day"]');
            if (dayBtn) {
                dayBtn.classList.add('active');
                dashCalendarView = 'day';
                renderDashCalendarView();
            }
        });
        
        weekView.appendChild(dayHeader);
    }
    
    const times = ['6 AM', '8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'];
    
    times.forEach(time => {
        const timeLabel = document.createElement('div');
        timeLabel.className = 'time-slot';
        timeLabel.textContent = time;
        weekView.appendChild(timeLabel);
        
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(dashWeekStart);
            dayDate.setDate(dayDate.getDate() + i);
            const dateStr = dayDate.toISOString().split('T')[0];
            
            const dayColumn = document.createElement('div');
            dayColumn.className = 'day-column';
            
            const dayEvents = dashCalendarEvents.filter(event => {
                if (event.date !== dateStr) return false;
                const eventHour = parseInt(event.time.split(':')[0]);
                const slotHour = time.includes('AM') ? 
                    (time === '12 PM' ? 12 : parseInt(time)) : 
                    (time === '12 PM' ? 12 : parseInt(time) + 12);
                return eventHour >= slotHour && eventHour < slotHour + 2;
            });
            
            dayEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `week-event ${event.type}`;
                eventDiv.textContent = event.title;
                eventDiv.title = `${event.time} - ${event.title}`;
                dayColumn.appendChild(eventDiv);
            });
            
            weekView.appendChild(dayColumn);
        }
    });
}

function renderDashDayView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
    const currentDate = new Date(dashSelectedDate);
    const dayOfWeek = currentDate.getDay();
    
    const periodEl = document.getElementById('dashCurrentPeriod');
    if (periodEl) {
        periodEl.textContent = `${dayNames[dayOfWeek]}, ${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
    }
    
    const dayView = document.getElementById('dashDayView');
    if (!dayView) return;
    
    dayView.innerHTML = '';
    
    const dateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
    const dayEvents = dashCalendarEvents.filter(event => event.date === dateStr);
    
    dayEvents.sort((a, b) => a.time.localeCompare(b.time));
    
    for (let hour = 6; hour <= 21; hour++) {
        const timeLabel = document.createElement('div');
        timeLabel.className = 'time-label';
        const ampm = hour < 12 ? 'AM' : 'PM';
        const displayHour = hour <= 12 ? hour : hour - 12;
        timeLabel.textContent = `${displayHour}:00 ${ampm}`;
        dayView.appendChild(timeLabel);
        
        const timeContent = document.createElement('div');
        timeContent.className = 'time-content';
        
        const hourEvents = dayEvents.filter(event => {
            const eventHour = parseInt(event.time.split(':')[0]);
            return eventHour === hour;
        });
        
        if (hourEvents.length > 0) {
            hourEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `day-event ${event.type}`;
                
                const eventTime = document.createElement('div');
                eventTime.className = 'day-event-time';
                eventTime.textContent = formatDashTime(event.time);
                
                const eventTitle = document.createElement('div');
                eventTitle.className = 'day-event-title';
                eventTitle.textContent = event.title;
                
                const eventDetails = document.createElement('div');
                eventDetails.className = 'day-event-details';
                eventDetails.textContent = `${event.location} • ${event.duration}`;
                
                eventDiv.appendChild(eventTime);
                eventDiv.appendChild(eventTitle);
                eventDiv.appendChild(eventDetails);
                
                timeContent.appendChild(eventDiv);
            });
        }
        
        dayView.appendChild(timeContent);
    }
}

function formatDashTime(time) {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    return `${displayHour}:${minutes} ${ampm}`;
}

console.log('Player Dashboard with Calendar loaded successfully');

// =========================================================================
// NOTIFICATION BELL — injected on every player page
// =========================================================================
(function initNotificationBell() {
    const URLROOT = window.URLROOT_FACILITY || (window.coachSessionData && window.coachSessionData.urlRoot) || '';

    function getUrlRoot() {
        // Try various globals set by different pages
        return window.URLROOT_FACILITY
            || (window.coachSessionData && window.coachSessionData.urlRoot)
            || (window.dashboardData && window.dashboardData.urlRoot)
            || '';
    }

    function injectBell() {
        if (document.getElementById('notif-bell-widget')) return;
        const widget = document.createElement('div');
        widget.id = 'notif-bell-widget';
        widget.style.cssText = 'position:fixed;top:16px;right:20px;z-index:9000;';
        widget.innerHTML = `
            <button id="notif-bell-btn" style="background:#fff;border:none;border-radius:50%;width:44px;height:44px;cursor:pointer;box-shadow:0 2px 12px rgba(0,0,0,0.15);position:relative;display:flex;align-items:center;justify-content:center;font-size:18px;color:#4A90E2;" title="Notifications">
                <i class="fas fa-bell"></i>
                <span id="notif-badge" style="display:none;position:absolute;top:4px;right:4px;background:#e74c3c;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;line-height:18px;text-align:center;"></span>
            </button>
            <div id="notif-dropdown" style="display:none;position:absolute;top:52px;right:0;width:340px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.18);overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;">
                    <strong style="font-size:15px;color:#2c3e50;">Notifications</strong>
                    <button onclick="markAllNotifsRead()" style="background:none;border:none;color:#4A90E2;cursor:pointer;font-size:12px;">Mark all read</button>
                </div>
                <div id="notif-list" style="max-height:340px;overflow-y:auto;"></div>
            </div>
        `;
        document.body.appendChild(widget);

        document.getElementById('notif-bell-btn').addEventListener('click', function(e) {
            e.stopPropagation();
            const dd = document.getElementById('notif-dropdown');
            const open = dd.style.display === 'block';
            dd.style.display = open ? 'none' : 'block';
            if (!open) loadNotifications();
        });
        document.addEventListener('click', function() {
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.style.display = 'none';
        });
    }

    function loadNotifications() {
        const root = getUrlRoot();
        fetch(root + '/player/notifications')
            .then(r => r.json())
            .then(data => {
                updateBadge(data.unread_count || 0);
                renderNotifications(data.notifications || []);
            })
            .catch(() => {});
    }

    function updateBadge(count) {
        const badge = document.getElementById('notif-badge');
        if (!badge) return;
        if (count > 0) {
            badge.style.display = 'block';
            badge.textContent = count > 9 ? '9+' : count;
        } else {
            badge.style.display = 'none';
        }
    }

    function renderNotifications(items) {
        const list = document.getElementById('notif-list');
        if (!list) return;
        if (!items.length) {
            list.innerHTML = '<div style="padding:24px;text-align:center;color:#999;"><i class="fas fa-bell-slash" style="font-size:32px;margin-bottom:8px;display:block;"></i>No notifications</div>';
            return;
        }
        const icons = { booking_confirmed: 'check-circle', cancellation: 'times-circle', reminder: 'clock' };
        const colors = { booking_confirmed: '#27ae60', cancellation: '#e74c3c', reminder: '#f39c12' };
        list.innerHTML = items.map(n => `
            <div onclick="markOneNotifRead(${n.NotificationID}, this)"
                 style="padding:12px 16px;border-bottom:1px solid #f0f0f0;cursor:pointer;background:${n.IsRead ? '#fff' : '#f0f7ff'};transition:background 0.2s;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <i class="fas fa-${icons[n.Type] || 'info-circle'}" style="color:${colors[n.Type] || '#4A90E2'};margin-top:2px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:${n.IsRead ? '400' : '600'};font-size:13px;color:#2c3e50;">${n.Title}</div>
                        <div style="font-size:12px;color:#666;margin-top:2px;">${n.Message}</div>
                        <div style="font-size:11px;color:#aaa;margin-top:4px;">${timeAgo(n.CreatedAt)}</div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    window.markAllNotifsRead = function() {
        const root = getUrlRoot();
        fetch(root + '/player/mark_notifications_read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: ''
        }).then(r => r.json()).then(d => {
            updateBadge(0);
            document.querySelectorAll('#notif-list > div').forEach(el => {
                el.style.background = '#fff';
                el.querySelector('div > div:first-child')?.style && (el.querySelector('div > div > div')?.style.fontWeight = '400');
            });
        }).catch(() => {});
    };

    window.markOneNotifRead = function(id, el) {
        const root = getUrlRoot();
        fetch(root + '/player/mark_notifications_read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'notification_id=' + id
        }).then(r => r.json()).then(d => {
            el.style.background = '#fff';
            updateBadge(d.unread_count || 0);
        }).catch(() => {});
    };

    function timeAgo(dateStr) {
        const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff/60) + 'm ago';
        if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
        return Math.floor(diff/86400) + 'd ago';
    }

    // Init bell after DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { injectBell(); loadNotifications(); });
    } else {
        injectBell(); loadNotifications();
    }
    // Refresh unread count every 60 seconds
    setInterval(function() {
        const root = getUrlRoot();
        fetch(root + '/player/notifications')
            .then(r => r.json())
            .then(d => updateBadge(d.unread_count || 0))
            .catch(() => {});
    }, 60000);
})();
