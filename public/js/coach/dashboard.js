/* ===== COACH DASHBOARD JAVASCRIPT - ELITE CRICKET ACADEMY ===== */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

// Initialize Dashboard
function initializeDashboard() {
    // Update current time
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);

    // Initialize sidebar functionality
    initializeSidebar();

    // Initialize calendar
    initializeCalendar();

    // Initialize quick actions
    initializeQuickActions();

    // Initialize booking filters
    initializeBookingFilters();

    // Initialize animations
    initializeAnimations();

    // Initialize theme system
    initializeTheme();
}

// Time Management
function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    const currentTimeElement = document.querySelector('.current-time');
    if (currentTimeElement) {
        currentTimeElement.textContent = timeString;
    }
}

// Sidebar Functionality
function initializeSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.coach-sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update main content margin
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '80px';
                // Update toggle icon to show expand
                this.innerHTML = '<i class="fas fa-angle-right"></i>';
            } else {
                mainContent.style.marginLeft = '280px';
                // Update toggle icon to show collapse
                this.innerHTML = '<i class="fas fa-angle-left"></i>';
            }
        });
    }

    // Set active navigation item
    const currentPage = window.location.pathname.split('/').pop();
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    navItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        if (link) {
            const href = link.getAttribute('href');
            if (href && href.includes(currentPage)) {
                item.classList.add('active');
            }
        }
    });
    
    // Handle mobile sidebar
    handleMobileSidebar();
}

function handleMobileSidebar() {
    const sidebar = document.querySelector('.coach-sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    // Mobile sidebar toggle
    if (window.innerWidth <= 768) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }
}

// Calendar Functionality
let currentDate = new Date();
let selectedDate = null;

function initializeCalendar() {
    generateCalendar();
    
    // Calendar navigation
    const prevBtn = document.querySelector('.btn-prev');
    const nextBtn = document.querySelector('.btn-next');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar();
        });
    }
}

function generateCalendar() {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    
    const monthDisplay = document.querySelector('#currentMonth');
    if (monthDisplay) {
        monthDisplay.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
    
    const calendarContainer = document.querySelector('.calendar-container');
    if (!calendarContainer) return;
    
    // Clear existing calendar
    const existingCalendar = calendarContainer.querySelector('.mini-calendar');
    if (existingCalendar) {
        existingCalendar.remove();
    }
    
    // Create calendar structure
    const calendar = document.createElement('div');
    calendar.className = 'mini-calendar';
    
    // Calendar header (days of week)
    const headerHTML = `
        <div class="calendar-header">
            <div class="day-header">Sun</div>
            <div class="day-header">Mon</div>
            <div class="day-header">Tue</div>
            <div class="day-header">Wed</div>
            <div class="day-header">Thu</div>
            <div class="day-header">Fri</div>
            <div class="day-header">Sat</div>
        </div>
    `;
    
    calendar.innerHTML = headerHTML;
    
    // Calendar body
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const calendarBody = document.createElement('div');
    calendarBody.className = 'calendar-body';
    
    // Sample booking data for calendar display
    const bookings = getBookingData();
    
    for (let i = 0; i < 42; i++) {
        const cellDate = new Date(startDate);
        cellDate.setDate(startDate.getDate() + i);
        
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        
        if (cellDate.getMonth() !== currentDate.getMonth()) {
            dayCell.classList.add('other-month');
        }
        
        if (isToday(cellDate)) {
            dayCell.classList.add('today');
        }
        
        const dayBookings = bookings.filter(booking => 
            isSameDate(new Date(booking.date), cellDate)
        );
        
        if (dayBookings.length > 0) {
            dayCell.classList.add('has-bookings');
        }
        
        dayCell.innerHTML = `
            <div class="day-number">${cellDate.getDate()}</div>
            ${dayBookings.length > 0 ? `<div class="booking-count">${dayBookings.length}</div>` : ''}
        `;
        
        dayCell.addEventListener('click', function() {
            selectDate(cellDate);
        });
        
        calendarBody.appendChild(dayCell);
    }
    
    calendar.appendChild(calendarBody);
    calendarContainer.appendChild(calendar);
    
    // Add calendar styles
    addCalendarStyles();
}

function addCalendarStyles() {
    if (document.querySelector('#calendar-styles')) return;
    
    const styles = document.createElement('style');
    styles.id = 'calendar-styles';
    styles.textContent = `
        .mini-calendar {
            width: 100%;
        }
        
        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            margin-bottom: 10px;
        }
        
        .day-header {
            text-align: center;
            font-weight: 600;
            color: #4A90E2;
            padding: 8px 4px;
            font-size: 0.9rem;
        }
        
        .calendar-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            padding: 4px;
        }
        
        .calendar-day:hover {
            background: rgba(74, 144, 226, 0.2);
            transform: scale(1.05);
        }
        
        .calendar-day.other-month {
            opacity: 0.3;
        }
        
        .calendar-day.today {
            background: #4A90E2;
            color: white;
        }
        
        .calendar-day.today .day-number {
            color: white;
        }
        
        .calendar-day.has-bookings {
            background: rgba(39, 174, 96, 0.2);
            border: 2px solid #27ae60;
        }
        
        .calendar-day.selected {
            background: #357ABD;
            color: white;
        }
        
        .day-number {
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
        }
        
        .booking-count {
            font-size: 0.7rem;
            background: #27ae60;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
        }
    `;
    document.head.appendChild(styles);
}

function selectDate(date) {
    selectedDate = date;
    
    // Update selected state
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.classList.remove('selected');
    });
    
    event.currentTarget.classList.add('selected');
    
    // Filter bookings for selected date
    filterBookingsByDate(date);
}

function isToday(date) {
    const today = new Date();
    return isSameDate(date, today);
}

function isSameDate(date1, date2) {
    return date1.getDate() === date2.getDate() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getFullYear() === date2.getFullYear();
}

// Booking Management
function initializeBookingFilters() {
    const filterSelect = document.querySelector('#bookingFilter');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            filterBookings(this.value);
        });
    }
}

function filterBookings(filter) {
    const bookings = document.querySelectorAll('.booking-item');
    
    bookings.forEach(booking => {
        const sessionType = booking.querySelector('.session-type');
        const isVisible = filter === 'all' || 
                         (filter === 'private' && sessionType.classList.contains('private')) ||
                         (filter === 'normal' && sessionType.classList.contains('normal'));
        
        booking.style.display = isVisible ? 'flex' : 'none';
    });
}

function filterBookingsByDate(date) {
    const bookings = document.querySelectorAll('.booking-item');
    const dateString = date.toISOString().split('T')[0];
    
    bookings.forEach(booking => {
        const bookingDate = booking.dataset.date;
        booking.style.display = bookingDate === dateString ? 'flex' : 'none';
    });
    
    // Update bookings header
    const bookingsHeader = document.querySelector('.bookings-section .section-header h2');
    if (bookingsHeader) {
        const formattedDate = date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        bookingsHeader.innerHTML = `<i class="fas fa-calendar-alt"></i> Bookings for ${formattedDate}`;
    }
}

// Quick Actions
function initializeQuickActions() {
    const actionButtons = document.querySelectorAll('.action-btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const action = this.dataset.action;
            handleQuickAction(action);
        });
    });
}

function handleQuickAction(action) {
    switch(action) {
        case 'new-session':
            showNewSessionModal();
            break;
        case 'player-profile':
            showPlayerProfileModal();
            break;
        case 'view-schedule':
            window.location.href = '/coach/schedules';
            break;
        case 'reports':
            window.location.href = '/coach/reports';
            break;
        default:
            console.log('Unknown action:', action);
    }
}

function showNewSessionModal() {
    // Create modal for new session
    const modal = createModal('Create New Session', `
        <form id="newSessionForm">
            <div class="form-group">
                <label for="sessionType">Session Type</label>
                <select id="sessionType" required>
                    <option value="">Select Type</option>
                    <option value="private">Private Session</option>
                    <option value="normal">Normal Session</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sessionDate">Date</label>
                <input type="date" id="sessionDate" required>
            </div>
            <div class="form-group">
                <label for="sessionTime">Time</label>
                <input type="time" id="sessionTime" required>
            </div>
            <div class="form-group">
                <label for="playerName">Player Name</label>
                <input type="text" id="playerName" required>
            </div>
            <div class="form-group">
                <label for="facility">Facility</label>
                <select id="facility" required>
                    <option value="">Select Facility</option>
                    <option value="indoor-nets">Indoor Nets</option>
                    <option value="outdoor-ground">Outdoor Ground</option>
                    <option value="practice-pitches">Practice Pitches</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-create">Create Session</button>
            </div>
        </form>
    `);
    
    // Handle form submission
    const form = modal.querySelector('#newSessionForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Add new session logic here
        showNotification('Session created successfully!', 'success');
        closeModal(modal);
    });
}

function showPlayerProfileModal() {
    const modal = createModal('Player Profile Search', `
        <div class="player-search">
            <div class="form-group">
                <label for="playerSearch">Search Player</label>
                <input type="text" id="playerSearch" placeholder="Enter player name...">
            </div>
            <div class="player-results" id="playerResults">
                <!-- Player search results will appear here -->
            </div>
        </div>
    `);
    
    const searchInput = modal.querySelector('#playerSearch');
    searchInput.addEventListener('input', function() {
        searchPlayers(this.value);
    });
}

function searchPlayers(query) {
    // Simulate player search
    const players = [
        { id: 1, name: 'Raj Patel', age: 16, position: 'Batsman' },
        { id: 2, name: 'Amit Singh', age: 15, position: 'Bowler' },
        { id: 3, name: 'Karan Kumar', age: 17, position: 'All-rounder' }
    ];
    
    const filtered = players.filter(player => 
        player.name.toLowerCase().includes(query.toLowerCase())
    );
    
    const resultsContainer = document.querySelector('#playerResults');
    if (resultsContainer) {
        resultsContainer.innerHTML = filtered.map(player => `
            <div class="player-result-item" onclick="viewPlayerProfile(${player.id})">
                <div class="player-avatar">${player.name.charAt(0)}</div>
                <div class="player-info">
                    <h4>${player.name}</h4>
                    <p>Age: ${player.age} | ${player.position}</p>
                </div>
            </div>
        `).join('');
    }
}

function viewPlayerProfile(playerId) {
    // Navigate to player profile
    window.location.href = `/coach/players/${playerId}`;
}

// Modal System
function createModal(title, content) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-content">
                ${content}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add modal styles
    addModalStyles();
    
    // Close modal handlers
    const closeBtn = modal.querySelector('.modal-close');
    const cancelBtn = modal.querySelector('.btn-cancel');
    
    closeBtn.addEventListener('click', () => closeModal(modal));
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => closeModal(modal));
    }
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    return modal;
}

function closeModal(modal) {
    modal.classList.add('fade-out');
    setTimeout(() => {
        if (modal.parentNode) {
            modal.parentNode.removeChild(modal);
        }
    }, 300);
}

function addModalStyles() {
    if (document.querySelector('#modal-styles')) return;
    
    const styles = document.createElement('style');
    styles.id = 'modal-styles';
    styles.textContent = `
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-overlay.fade-out {
            animation: fadeOut 0.3s ease;
        }
        
        .modal-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .modal-header h3 {
            margin: 0;
            color: #333;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }
        
        .modal-content {
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }
        
        .btn-cancel, .btn-create {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
        }
        
        .btn-create {
            background: #4A90E2;
            color: white;
        }
        
        .player-result-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .player-result-item:hover {
            background: rgba(74, 144, 226, 0.1);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
    document.head.appendChild(styles);
}

// Notification System
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Add notification styles
    addNotificationStyles();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeNotification(notification);
    }, 5000);
    
    // Close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => removeNotification(notification));
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

function removeNotification(notification) {
    notification.classList.add('fade-out');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

function addNotificationStyles() {
    if (document.querySelector('#notification-styles')) return;
    
    const styles = document.createElement('style');
    styles.id = 'notification-styles';
    styles.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            max-width: 400px;
            z-index: 3000;
            animation: slideIn 0.3s ease;
        }
        
        .notification.fade-out {
            animation: slideOut 0.3s ease;
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification-success {
            border-left: 4px solid #27ae60;
        }
        
        .notification-error {
            border-left: 4px solid #e74c3c;
        }
        
        .notification-warning {
            border-left: 4px solid #f39c12;
        }
        
        .notification-info {
            border-left: 4px solid #4A90E2;
        }
        
        .notification-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #666;
            cursor: pointer;
            margin-left: 15px;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(styles);
}

// Animation System
function initializeAnimations() {
    // Stagger animations for cards
    const cards = document.querySelectorAll('.stat-card, .booking-item, .session-timeline-item');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in-up');
    });
    
    // Add animation styles
    addAnimationStyles();
}

function addAnimationStyles() {
    if (document.querySelector('#animation-styles')) return;
    
    const styles = document.createElement('style');
    styles.id = 'animation-styles';
    styles.textContent = `
        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(styles);
}

// Theme System
function initializeTheme() {
    // Set theme based on time of day
    const hour = new Date().getHours();
    const body = document.body;
    
    if (hour >= 6 && hour < 12) {
        body.classList.add('morning-theme');
    } else if (hour >= 12 && hour < 18) {
        body.classList.add('afternoon-theme');
    } else {
        body.classList.add('evening-theme');
    }
}

// Data Management
function getBookingData() {
    // Sample booking data for calendar
    return [
        {
            date: new Date().toISOString().split('T')[0],
            type: 'private',
            count: 2
        },
        {
            date: new Date(Date.now() + 86400000).toISOString().split('T')[0],
            type: 'normal',
            count: 1
        },
        {
            date: new Date(Date.now() + 172800000).toISOString().split('T')[0],
            type: 'private',
            count: 3
        }
    ];
}

// Export functions for global access
window.CoachDashboard = {
    showNotification,
    selectDate,
    filterBookings,
    handleQuickAction,
    viewPlayerProfile
};
