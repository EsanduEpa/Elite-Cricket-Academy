// Training Schedule JavaScript

// Get current date and time
const now = new Date();
let currentWeekStart = getWeekStart(now);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeTrainingSchedule();
    initializeBookingNavigation();
    initializeDailyView();
    initializeDateNavigation();
    updateCurrentPeriodDisplay();
    updateWeeklyScheduleContent(); // Load initial week content
});

// Date navigation functionality
function initializeDateNavigation() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentWeekStart.setDate(currentWeekStart.getDate() - 7);
            updateCurrentPeriodDisplay();
            updateWeeklyScheduleContent();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentWeekStart.setDate(currentWeekStart.getDate() + 7);
            updateCurrentPeriodDisplay();
            updateWeeklyScheduleContent();
        });
    }
}

function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Monday as week start
    return new Date(d.setDate(diff));
}

function getWeekEnd(weekStart) {
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 6);
    return weekEnd;
}

function formatDate(date) {
    const options = { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    };
    return date.toLocaleDateString('en-US', options);
}

function updateCurrentPeriodDisplay() {
    const currentPeriodElement = document.getElementById('currentPeriod');
    if (currentPeriodElement) {
        const weekEnd = getWeekEnd(currentWeekStart);
        const startDateStr = formatDate(currentWeekStart);
        const endDateStr = formatDate(weekEnd);
        
        // Today's date and time
        const today = new Date();
        const currentTime = today.toLocaleString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        currentPeriodElement.innerHTML = `Week of ${startDateStr} - ${endDateStr}<br><small style="font-size: 0.8em; opacity: 0.8;">Today: ${currentTime}</small>`;
    }
}

// Function to update weekly schedule content when navigating
function updateWeeklyScheduleContent() {
    const daysGrid = document.querySelector('.days-grid');
    if (!daysGrid) return;
    
    const dayColumns = daysGrid.querySelectorAll('.day-column');
    const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    dayColumns.forEach((column, index) => {
        const dayHeader = column.querySelector('.day-header');
        const dayNameSpan = dayHeader.querySelector('.day-name');
        const dayDateSpan = dayHeader.querySelector('.day-date');
        
        // Calculate the date for this day of the week
        const dayDate = new Date(currentWeekStart);
        dayDate.setDate(currentWeekStart.getDate() + index);
        
        // Update day name and date
        dayNameSpan.textContent = dayNames[index];
        dayDateSpan.textContent = formatDateShort(dayDate);
        
        // Update sessions for this week (you can customize this based on your data)
        updateDaySessions(column, dayDate, dayNames[index].toLowerCase());
    });
}

// Helper function for short date format
function formatDateShort(date) {
    const options = { month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Function to update sessions for a specific day
function updateDaySessions(dayColumn, date, dayName) {
    const daySlots = dayColumn.querySelector('.day-slots');
    if (!daySlots) return;
    
    // Generate sessions based on the week offset from current week
    const currentWeek = getWeekStart(new Date());
    const weekOffset = Math.floor((currentWeekStart - currentWeek) / (7 * 24 * 60 * 60 * 1000));
    
    // Get sessions for this day and week
    const sessions = getSessionsForDay(dayName, weekOffset);
    
    // Clear existing sessions
    daySlots.innerHTML = '';
    
    // Add new sessions
    sessions.forEach(session => {
        const sessionElement = document.createElement('div');
        sessionElement.className = `session ${session.type}`;
        sessionElement.setAttribute('data-time', session.time);
        
        sessionElement.innerHTML = `
            <div class="session-content">
                <i class="${session.icon}"></i>
                <span class="session-title">${session.title}</span>
                <span class="session-coach">${session.coach}</span>
            </div>
        `;
        
        daySlots.appendChild(sessionElement);
    });
}

// Function to generate sessions for different weeks
function getSessionsForDay(dayName, weekOffset) {
    // Base sessions for current week (weekOffset = 0)
    const baseSessions = {
        monday: [
            { time: '06:00', type: 'batting', icon: 'fas fa-baseball-ball', title: 'Batting Practice', coach: 'Coach Wilson' },
            { time: '16:00', type: 'fitness', icon: 'fas fa-dumbbell', title: 'Fitness Training', coach: 'Trainer Mike' }
        ],
        tuesday: [
            { time: '07:00', type: 'bowling', icon: 'fas fa-circle', title: 'Bowling Practice', coach: 'Coach Ahmed' }
        ],
        wednesday: [
            { time: '06:00', type: 'fielding', icon: 'fas fa-hand-rock', title: 'Fielding Drills', coach: 'Coach Smith' },
            { time: '17:00', type: 'fitness', icon: 'fas fa-dumbbell', title: 'Strength Training', coach: 'Trainer Sarah' }
        ],
        thursday: [
            { time: '08:00', type: 'batting', icon: 'fas fa-baseball-ball', title: 'Net Practice', coach: 'Coach Wilson' }
        ],
        friday: [
            { time: '09:00', type: 'match', icon: 'fas fa-trophy', title: 'Practice Match', coach: 'All Coaches' }
        ],
        saturday: [
            { time: '', type: 'rest', icon: 'fas fa-bed', title: 'Rest Day', coach: 'Recovery' }
        ],
        sunday: [
            { time: '10:00', type: 'team', icon: 'fas fa-users', title: 'Team Meeting', coach: 'Coach Wilson' }
        ]
    };
    
    // Modify sessions based on week offset
    const sessions = baseSessions[dayName] || [];
    
    if (weekOffset !== 0) {
        // For different weeks, vary the sessions slightly
        return sessions.map(session => {
            const variations = getSessionVariations(session.type, weekOffset);
            return { ...session, ...variations };
        });
    }
    
    return sessions;
}

// Function to get session variations for different weeks
function getSessionVariations(sessionType, weekOffset) {
    const variations = {
        batting: [
            { title: 'Batting Practice', coach: 'Coach Wilson' },
            { title: 'Advanced Batting', coach: 'Coach Ahmed' },
            { title: 'Power Hitting', coach: 'Coach Smith' }
        ],
        fitness: [
            { title: 'Fitness Training', coach: 'Trainer Mike' },
            { title: 'Cardio Workout', coach: 'Trainer Sarah' },
            { title: 'Strength Building', coach: 'Trainer Mike' }
        ],
        bowling: [
            { title: 'Bowling Practice', coach: 'Coach Ahmed' },
            { title: 'Spin Bowling', coach: 'Coach Wilson' },
            { title: 'Fast Bowling', coach: 'Coach Smith' }
        ],
        fielding: [
            { title: 'Fielding Drills', coach: 'Coach Smith' },
            { title: 'Catching Practice', coach: 'Coach Ahmed' },
            { title: 'Ground Fielding', coach: 'Coach Wilson' }
        ]
    };
    
    const typeVariations = variations[sessionType];
    if (typeVariations) {
        const index = Math.abs(weekOffset) % typeVariations.length;
        return typeVariations[index];
    }
    
    return {};
}

function initializeBookingNavigation() {
    // Handle "My Bookings" button click
    const goToBookingsBtn = document.getElementById('goToBookings');
    if (goToBookingsBtn) {
        goToBookingsBtn.addEventListener('click', function() {
            window.location.href = '/Elite/player/bookings';
        });
    }
}

function initializeTrainingSchedule() {
    // View Toggle Functionality
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    const weeklyView = document.getElementById('weeklyView');
    const dailyView = document.getElementById('dailyView');

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active toggle button
            toggleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide views
            if (view === 'week') {
                weeklyView.classList.add('active');
                dailyView.style.display = 'none';
            } else {
                weeklyView.classList.remove('active');
                dailyView.style.display = 'block';
                loadDailyView(currentDay);
            }
        });
    });

    // Today button for jumping to current week
    const todayBtn = document.getElementById('todayBtn');
    if (todayBtn) {
        todayBtn.addEventListener('click', function() {
            currentWeekStart = getWeekStart(new Date());
            updateCurrentPeriodDisplay();
            updateWeeklyScheduleContent();
        });
    }
}

// Daily View Functionality
let currentDay = 'monday';
const dailyScheduleData = {
    monday: {
        name: 'Monday',
        date: 'September 13, 2025',
        sessions: [
            {
                time: '06:00 - 07:30',
                type: 'Batting Practice',
                icon: 'fas fa-baseball-ball',
                coach: 'Coach Wilson',
                location: 'Ground A',
                groupType: 'Group Session',
                status: 'attended'
            },
            {
                time: '16:00 - 17:00',
                type: 'Fitness Training',
                icon: 'fas fa-dumbbell',
                coach: 'Trainer Mike',
                location: 'Gym',
                groupType: 'Personal Training',
                status: 'missed'
            }
        ]
    },
    tuesday: {
        name: 'Tuesday',
        date: 'September 14, 2025',
        sessions: [
            {
                time: '07:00 - 08:30',
                type: 'Bowling Practice',
                icon: 'fas fa-circle',
                coach: 'Coach Ahmed',
                location: 'Ground B',
                groupType: 'Group Session',
                status: 'attended'
            }
        ]
    },
    wednesday: {
        name: 'Wednesday',
        date: 'September 15, 2025',
        sessions: [
            {
                time: '06:00 - 07:00',
                type: 'Fielding Drills',
                icon: 'fas fa-hand-rock',
                coach: 'Coach Smith',
                location: 'Ground A',
                groupType: 'Team Practice',
                status: 'upcoming'
            },
            {
                time: '17:00 - 18:00',
                type: 'Strength Training',
                icon: 'fas fa-dumbbell',
                coach: 'Trainer Sarah',
                location: 'Gym',
                groupType: 'Group Session',
                status: 'upcoming'
            }
        ]
    },
    thursday: {
        name: 'Thursday',
        date: 'September 16, 2025',
        sessions: [
            {
                time: '08:00 - 09:30',
                type: 'Net Practice',
                icon: 'fas fa-baseball-ball',
                coach: 'Coach Wilson',
                location: 'Nets',
                groupType: 'Individual Session',
                status: 'upcoming'
            }
        ]
    },
    friday: {
        name: 'Friday',
        date: 'September 17, 2025',
        sessions: [
            {
                time: '09:00 - 12:00',
                type: 'Practice Match',
                icon: 'fas fa-trophy',
                coach: 'All Coaches',
                location: 'Main Ground',
                groupType: 'Team Match',
                status: 'upcoming'
            }
        ]
    },
    saturday: {
        name: 'Saturday',
        date: 'September 18, 2025',
        sessions: [
            {
                time: 'All Day',
                type: 'Rest Day',
                icon: 'fas fa-bed',
                coach: 'Recovery',
                location: 'Home',
                groupType: 'Rest',
                status: 'rest'
            }
        ]
    },
    sunday: {
        name: 'Sunday',
        date: 'September 19, 2025',
        sessions: [
            {
                time: '10:00 - 11:00',
                type: 'Team Meeting',
                icon: 'fas fa-users',
                coach: 'Coach Wilson',
                location: 'Conference Room',
                groupType: 'Team Meeting',
                status: 'upcoming'
            }
        ]
    }
};

function initializeDailyView() {
    // Day navigation buttons
    const prevDayBtn = document.getElementById('prevDayBtn');
    const nextDayBtn = document.getElementById('nextDayBtn');
    const dayQuickBtns = document.querySelectorAll('.day-quick-btn');

    if (prevDayBtn) {
        prevDayBtn.addEventListener('click', function() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const currentIndex = days.indexOf(currentDay);
            const prevIndex = currentIndex > 0 ? currentIndex - 1 : days.length - 1;
            currentDay = days[prevIndex];
            loadDailyView(currentDay);
        });
    }

    if (nextDayBtn) {
        nextDayBtn.addEventListener('click', function() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const currentIndex = days.indexOf(currentDay);
            const nextIndex = currentIndex < days.length - 1 ? currentIndex + 1 : 0;
            currentDay = days[nextIndex];
            loadDailyView(currentDay);
        });
    }

    // Quick day buttons
    dayQuickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentDay = this.dataset.day;
            loadDailyView(currentDay);
        });
    });

    // Load initial day
    loadDailyView(currentDay);
}

function loadDailyView(day) {
    const dayData = dailyScheduleData[day];
    if (!dayData) return;

    // Update day header
    const currentDayName = document.getElementById('currentDayName');
    const currentDayDate = document.getElementById('currentDayDate');
    const dailySessions = document.getElementById('dailySessions');

    if (currentDayName) currentDayName.textContent = dayData.name;
    if (currentDayDate) currentDayDate.textContent = dayData.date;

    // Update quick day buttons
    const dayQuickBtns = document.querySelectorAll('.day-quick-btn');
    dayQuickBtns.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.day === day);
    });

    // Generate sessions HTML
    if (dailySessions) {
        dailySessions.innerHTML = dayData.sessions.map(session => {
            const statusClass = session.status;
            const statusIcon = getStatusIcon(session.status);
            const statusText = getStatusText(session.status);

            return `
                <div class="session-card ${statusClass}">
                    <div class="session-header">
                        <div class="session-time">
                            <i class="fas fa-clock"></i>
                            <span>${session.time}</span>
                        </div>
                        <div class="attendance-status">
                            <i class="${statusIcon}"></i>
                            <span>${statusText}</span>
                        </div>
                    </div>
                    <div class="session-body">
                        <div class="session-type">
                            <i class="${session.icon}"></i>
                            <span>${session.type}</span>
                        </div>
                        <div class="session-details">
                            <p><i class="fas fa-user"></i> Coach: ${session.coach}</p>
                            <p><i class="fas fa-map-marker-alt"></i> ${session.location}</p>
                            <p><i class="fas fa-users"></i> ${session.groupType}</p>
                        </div>
                    </div>
                    <div class="session-footer">
                        <button class="btn btn-sm btn-outline">View Details</button>
                        ${session.status === 'upcoming' ? '<button class="btn btn-sm btn-primary">Mark Attendance</button>' : ''}
                    </div>
                </div>
            `;
        }).join('');
    }
}

function getStatusIcon(status) {
    switch(status) {
        case 'attended': return 'fas fa-check-circle';
        case 'missed': return 'fas fa-times-circle';
        case 'upcoming': return 'fas fa-clock';
        case 'rest': return 'fas fa-bed';
        default: return 'fas fa-clock';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'attended': return 'Attended';
        case 'missed': return 'Missed';
        case 'upcoming': return 'Upcoming';
        case 'rest': return 'Rest Day';
        default: return 'Scheduled';
    }
}

// ========================================
// CALENDAR FUNCTIONALITY
// ========================================

// Calendar State
let calendarView = 'month';
let calendarMonth = new Date().getMonth();
let calendarYear = new Date().getFullYear();
let calendarDay = new Date().getDate();
let selectedCalendarDate = new Date();
let calendarWeekStart = null;

// Dummy Calendar Events - Training Sessions and Matches
const calendarEvents = [
    // Training Sessions
    {
        id: 1,
        title: 'Batting Practice',
        type: 'training',
        date: '2025-10-23',
        time: '09:00',
        duration: '2 hours',
        coach: 'Coach Michael',
        location: 'Indoor Nets',
        description: 'Focus on cover drives and pull shots'
    },
    {
        id: 2,
        title: 'Bowling Session',
        type: 'training',
        date: '2025-10-25',
        time: '14:00',
        duration: '1.5 hours',
        coach: 'Coach Sarah',
        location: 'Practice Ground',
        description: 'Work on yorkers and slower balls'
    },
    {
        id: 3,
        title: 'Fielding Practice',
        type: 'training',
        date: '2025-10-29',
        time: '10:00',
        duration: '2 hours',
        coach: 'Coach David',
        location: 'Main Ground',
        description: 'Catching drills and ground fielding'
    },
    // Matches
    {
        id: 4,
        title: 'T20 Match vs City Sports Club',
        type: 'match',
        date: '2025-10-26',
        time: '14:00',
        duration: '4 hours',
        opponent: 'City Sports Club',
        location: 'Elite Stadium',
        description: 'T20 Tournament - Quarter Final'
    },
    {
        id: 5,
        title: 'ODI Match vs State Academy',
        type: 'match',
        date: '2025-11-02',
        time: '10:00',
        duration: '8 hours',
        opponent: 'State Academy',
        location: 'State Cricket Ground',
        description: 'Inter-Academy Championship'
    },
    // Fitness Sessions
    {
        id: 6,
        title: 'Fitness Training',
        type: 'fitness',
        date: '2025-10-27',
        time: '07:00',
        duration: '1 hour',
        coach: 'Trainer John',
        location: 'Gym',
        description: 'Cardio and strength training'
    },
    {
        id: 7,
        title: 'Team Practice',
        type: 'training',
        date: '2025-10-30',
        time: '15:00',
        duration: '3 hours',
        coach: 'Head Coach',
        location: 'Main Ground',
        description: 'Full team practice session'
    }
];

// Initialize Calendar when DOM loads
if (document.getElementById('monthView')) {
    initializeCalendar();
}

function initializeCalendar() {
    console.log('Player Calendar initializing...');
    
    // Setup view buttons
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            calendarView = this.getAttribute('data-view');
            renderCalendarView();
        });
    });
    
    // Setup navigation buttons
    const prevBtn = document.getElementById('prevPeriod');
    const nextBtn = document.getElementById('nextPeriod');
    const todayCalBtn = document.getElementById('todayBtn');
    
    if (prevBtn) prevBtn.addEventListener('click', () => navigateCalendarPeriod(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => navigateCalendarPeriod(1));
    if (todayCalBtn) todayCalBtn.addEventListener('click', goToCalendarToday);
    
    // Initial render
    renderCalendarView();
}

// Go to today
function goToCalendarToday() {
    const today = new Date();
    calendarMonth = today.getMonth();
    calendarYear = today.getFullYear();
    calendarDay = today.getDate();
    selectedCalendarDate = new Date(today);
    calendarWeekStart = null;
    renderCalendarView();
}

// Navigate between periods
function navigateCalendarPeriod(direction) {
    if (calendarView === 'month') {
        calendarMonth += direction;
        if (calendarMonth < 0) {
            calendarMonth = 11;
            calendarYear--;
        } else if (calendarMonth > 11) {
            calendarMonth = 0;
            calendarYear++;
        }
    } else if (calendarView === 'week') {
        if (!calendarWeekStart) {
            calendarWeekStart = new Date(selectedCalendarDate);
            calendarWeekStart.setDate(calendarWeekStart.getDate() - calendarWeekStart.getDay());
        }
        calendarWeekStart.setDate(calendarWeekStart.getDate() + (direction * 7));
        selectedCalendarDate = new Date(calendarWeekStart);
        calendarMonth = calendarWeekStart.getMonth();
        calendarYear = calendarWeekStart.getFullYear();
        calendarDay = calendarWeekStart.getDate();
    } else if (calendarView === 'day') {
        selectedCalendarDate.setDate(selectedCalendarDate.getDate() + direction);
        calendarDay = selectedCalendarDate.getDate();
        calendarMonth = selectedCalendarDate.getMonth();
        calendarYear = selectedCalendarDate.getFullYear();
    }
    
    renderCalendarView();
}

// Render current view
function renderCalendarView() {
    const monthView = document.getElementById('monthView');
    const weekView = document.getElementById('weekView');
    const dayView = document.getElementById('dayView');
    
    if (!monthView || !weekView || !dayView) return;
    
    monthView.style.display = 'none';
    weekView.style.display = 'none';
    dayView.style.display = 'none';
    
    if (calendarView === 'month') {
        monthView.style.display = 'grid';
        renderMonthView();
    } else if (calendarView === 'week') {
        weekView.style.display = 'grid';
        renderWeekView();
    } else if (calendarView === 'day') {
        dayView.style.display = 'grid';
        renderDayView();
    }
}

// Render Month View
function renderMonthView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    const periodEl = document.getElementById('currentPeriod');
    if (periodEl) {
        periodEl.textContent = `${monthNames[calendarMonth]} ${calendarYear}`;
    }
    
    const calendarGrid = document.getElementById('monthView');
    if (!calendarGrid) return;
    
    calendarGrid.innerHTML = '';
    
    // Add day headers
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day header';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(calendarYear, calendarMonth, 1).getDay();
    const daysInMonth = new Date(calendarYear, calendarMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(calendarYear, calendarMonth, 0).getDate();
    
    // Add previous month's days
    for (let i = firstDay - 1; i >= 0; i--) {
        const dayCell = createMonthDayCell(daysInPrevMonth - i, true, calendarMonth - 1);
        calendarGrid.appendChild(dayCell);
    }
    
    // Add current month's days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today.getDate() && 
                       calendarMonth === today.getMonth() && 
                       calendarYear === today.getFullYear();
        const dayCell = createMonthDayCell(day, false, calendarMonth, isToday);
        calendarGrid.appendChild(dayCell);
    }
    
    // Add next month's days
    const totalCells = firstDay + daysInMonth;
    const remainingCells = 7 - (totalCells % 7);
    if (remainingCells < 7) {
        for (let day = 1; day <= remainingCells; day++) {
            const dayCell = createMonthDayCell(day, true, calendarMonth + 1);
            calendarGrid.appendChild(dayCell);
        }
    }
}

// Create day cell for month view
function createMonthDayCell(day, isOtherMonth, month, isToday = false) {
    const dayCell = document.createElement('div');
    dayCell.className = 'calendar-day';
    
    if (isOtherMonth) {
        dayCell.classList.add('other-month');
    }
    if (isToday) {
        dayCell.classList.add('today');
    }
    
    // Check if this is the selected date
    const cellDate = new Date(calendarYear, month, day);
    if (!isOtherMonth && 
        cellDate.getDate() === selectedCalendarDate.getDate() && 
        cellDate.getMonth() === selectedCalendarDate.getMonth() && 
        cellDate.getFullYear() === selectedCalendarDate.getFullYear()) {
        dayCell.classList.add('selected');
    }
    
    const dateNumber = document.createElement('div');
    dateNumber.className = 'date-number';
    dateNumber.textContent = day;
    dayCell.appendChild(dateNumber);
    
    // Check for events on this day (show dots only)
    const dateStr = `${calendarYear}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayEvents = calendarEvents.filter(event => event.date === dateStr);
    
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
    
    // Add click handler to select date
    if (!isOtherMonth) {
        dayCell.addEventListener('click', () => {
            selectedCalendarDate = new Date(calendarYear, month, day);
            calendarDay = day;
            renderCalendarView();
        });
    }
    
    return dayCell;
}

// Render Week View
function renderWeekView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Initialize week start if not set
    if (!calendarWeekStart) {
        calendarWeekStart = new Date(selectedCalendarDate);
        calendarWeekStart.setDate(calendarWeekStart.getDate() - calendarWeekStart.getDay());
    }
    
    const weekEnd = new Date(calendarWeekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    const periodEl = document.getElementById('currentPeriod');
    if (periodEl) {
        periodEl.textContent = `${monthNames[calendarWeekStart.getMonth()]} ${calendarWeekStart.getDate()} - ${monthNames[weekEnd.getMonth()]} ${weekEnd.getDate()}, ${calendarWeekStart.getFullYear()}`;
    }
    
    const weekView = document.getElementById('weekView');
    if (!weekView) return;
    
    weekView.innerHTML = '';
    
    // Add empty corner cell
    const corner = document.createElement('div');
    corner.className = 'time-slot';
    corner.textContent = 'Time';
    weekView.appendChild(corner);
    
    // Add day headers
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const today = new Date();
    
    for (let i = 0; i < 7; i++) {
        const dayDate = new Date(calendarWeekStart);
        dayDate.setDate(dayDate.getDate() + i);
        
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        
        const isToday = dayDate.toDateString() === today.toDateString();
        const isSelected = dayDate.toDateString() === selectedCalendarDate.toDateString();
        
        if (isToday) dayHeader.classList.add('today');
        if (isSelected) dayHeader.classList.add('selected');
        
        dayHeader.innerHTML = `<strong>${dayNames[i]}</strong><br>${dayDate.getDate()}`;
        
        // Add click handler to select date and switch to day view
        dayHeader.addEventListener('click', () => {
            selectedCalendarDate = new Date(dayDate);
            calendarDay = selectedCalendarDate.getDate();
            calendarMonth = selectedCalendarDate.getMonth();
            calendarYear = selectedCalendarDate.getFullYear();
            
            // Switch to day view
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            const dayBtn = document.querySelector('.view-btn[data-view="day"]');
            if (dayBtn) {
                dayBtn.classList.add('active');
                calendarView = 'day';
                renderCalendarView();
            }
        });
        
        weekView.appendChild(dayHeader);
    }
    
    // Time slots from 6 AM to 8 PM
    const times = ['6 AM', '8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'];
    
    times.forEach(time => {
        // Time label
        const timeLabel = document.createElement('div');
        timeLabel.className = 'time-slot';
        timeLabel.textContent = time;
        weekView.appendChild(timeLabel);
        
        // Day columns
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(calendarWeekStart);
            dayDate.setDate(dayDate.getDate() + i);
            const dateStr = dayDate.toISOString().split('T')[0];
            
            const dayColumn = document.createElement('div');
            dayColumn.className = 'day-column';
            
            // Filter events for this day and time range
            const dayEvents = calendarEvents.filter(event => {
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

// Render Day View
function renderDayView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
    const currentDate = new Date(selectedCalendarDate);
    const dayOfWeek = currentDate.getDay();
    
    const periodEl = document.getElementById('currentPeriod');
    if (periodEl) {
        periodEl.textContent = `${dayNames[dayOfWeek]}, ${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
    }
    
    const dayView = document.getElementById('dayView');
    if (!dayView) return;
    
    dayView.innerHTML = '';
    
    const dateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
    const dayEvents = calendarEvents.filter(event => event.date === dateStr);
    
    // Sort events by time
    dayEvents.sort((a, b) => a.time.localeCompare(b.time));
    
    // Time slots from 6 AM to 9 PM
    for (let hour = 6; hour <= 21; hour++) {
        const timeLabel = document.createElement('div');
        timeLabel.className = 'time-label';
        const ampm = hour < 12 ? 'AM' : 'PM';
        const displayHour = hour <= 12 ? hour : hour - 12;
        timeLabel.textContent = `${displayHour}:00 ${ampm}`;
        dayView.appendChild(timeLabel);
        
        const timeContent = document.createElement('div');
        timeContent.className = 'time-content';
        
        // Find events in this hour
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
                eventTime.textContent = formatCalendarTime(event.time);
                
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

// Format time from 24h to 12h format
function formatCalendarTime(time) {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
    return `${displayHour}:${minutes} ${ampm}`;
}

console.log('Player Training with Calendar JavaScript loaded successfully');