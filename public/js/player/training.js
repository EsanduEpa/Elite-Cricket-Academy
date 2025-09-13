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