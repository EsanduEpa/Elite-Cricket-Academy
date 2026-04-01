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
