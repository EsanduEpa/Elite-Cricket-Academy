// Player Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Generate events from PHP data now that DOM is loaded
    sampleEvents = generateEventsFromPHPData();
    
    initializeDashboard();
    updateCurrentTime();
    animateCounters();
    initializeSidebar();
    initializeMobileFeatures();
    highlightActiveNavLink();
    initializeCalendar();
});

// Mobile-specific initialization
function initializeMobileFeatures() {
    // Prevent zoom on double tap for iOS
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function (event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
    
    // Improve scrolling on mobile
    if ('ontouchstart' in window) {
        document.body.style.webkitOverflowScrolling = 'touch';
    }
    
    // Handle mobile sidebar behavior
    const sidebar = document.getElementById('playerSidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth <= 1024 && sidebar && mainContent) {
        // Close sidebar when clicking outside on mobile
        mainContent.addEventListener('touchstart', function(e) {
            if (sidebar.classList.contains('sidebar-open')) {
                sidebar.classList.remove('sidebar-open');
            }
        });
        
        // Prevent body scroll when sidebar is open
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                document.body.style.overflow = sidebar.classList.contains('sidebar-open') ? '' : 'hidden';
            });
        }
    }
    
    // Add touch feedback to interactive elements
    const touchElements = document.querySelectorAll('.nav-link, .stat-card, .quick-action-btn, .btn');
    touchElements.forEach(element => {
        element.addEventListener('touchstart', function() {
            this.classList.add('touch-active');
        });
        
        element.addEventListener('touchend', function() {
            setTimeout(() => this.classList.remove('touch-active'), 150);
        });
    });
}

// Calendar functionality - initialize with PHP data if available
let currentDate = new Date();
let currentMonth = (typeof window.dashboardData !== 'undefined' && window.dashboardData.currentMonth !== undefined) 
    ? window.dashboardData.currentMonth 
    : currentDate.getMonth();
let currentYear = (typeof window.dashboardData !== 'undefined' && window.dashboardData.currentYear !== undefined) 
    ? window.dashboardData.currentYear 
    : currentDate.getFullYear();

const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Get current date and generate events from PHP data
const today = new Date();

// Function to format date as YYYY-MM-DD
function formatDate(date) {
    return date.getFullYear() + '-' + 
           String(date.getMonth() + 1).padStart(2, '0') + '-' + 
           String(date.getDate()).padStart(2, '0');
}

// Function to get event type from activity name
function getEventType(activity) {
    const activityLower = activity.toLowerCase();
    if (activityLower.includes('practice') || activityLower.includes('training')) return 'training';
    if (activityLower.includes('match') || activityLower.includes('tournament')) return 'match';
    if (activityLower.includes('meeting') || activityLower.includes('review')) return 'meeting';
    if (activityLower.includes('fitness') || activityLower.includes('gym')) return 'fitness';
    if (activityLower.includes('assessment') || activityLower.includes('test')) return 'assessment';
    if (activityLower.includes('selection')) return 'selection';
    return 'event';
}

// Generate events from PHP data
function generateEventsFromPHPData() {
    const sampleEvents = {};
    
    // Check if dashboard data is available
    if (typeof window.dashboardData !== 'undefined') {
        console.log('Loading events from PHP data:', window.dashboardData);
        
        // Add today's schedule
        if (window.dashboardData.todaySchedule && window.dashboardData.todaySchedule.length > 0) {
            const todayKey = window.dashboardData.currentDate;
            sampleEvents[todayKey] = window.dashboardData.todaySchedule.map(activity => ({
                title: activity.activity,
                type: getEventType(activity.activity),
                time: activity.time
            }));
            console.log(`Added ${sampleEvents[todayKey].length} events for today (${todayKey})`);
        }
        
        // Add upcoming schedule
        if (window.dashboardData.upcomingSchedule && window.dashboardData.upcomingSchedule.length > 0) {
            window.dashboardData.upcomingSchedule.forEach(schedule => {
                if (!sampleEvents[schedule.date]) {
                    sampleEvents[schedule.date] = [];
                }
                sampleEvents[schedule.date].push({
                    title: schedule.activity,
                    type: getEventType(schedule.activity),
                    time: schedule.time
                });
            });
            console.log(`Added ${window.dashboardData.upcomingSchedule.length} upcoming schedule events`);
        }
        
        // Add upcoming bookings
        if (window.dashboardData.upcomingBookings && window.dashboardData.upcomingBookings.length > 0) {
            window.dashboardData.upcomingBookings.forEach(booking => {
                if (!sampleEvents[booking.date]) {
                    sampleEvents[booking.date] = [];
                }
                sampleEvents[booking.date].push({
                    title: booking.type,
                    type: 'booking',
                    time: booking.time
                });
            });
            console.log(`Added ${window.dashboardData.upcomingBookings.length} booking events`);
        }
    } else {
        // Fallback to sample data if PHP data not available
        const todayKey = formatDate(today);
        sampleEvents[todayKey] = [
            { title: 'Morning Practice', type: 'training', time: '7:00 AM' },
            { title: 'Fitness Session', type: 'fitness', time: '4:00 PM' }
        ];
        
        // Add some sample upcoming events
        for (let i = 1; i <= 7; i++) {
            const eventDate = new Date(today);
            eventDate.setDate(today.getDate() + i);
            const eventKey = formatDate(eventDate);
            
            const sampleActivities = [
                { title: 'Team Meeting', type: 'meeting', time: '10:00 AM' },
                { title: 'Net Practice', type: 'training', time: '9:00 AM' },
                { title: 'Match vs Central CC', type: 'match', time: '2:00 PM' },
                { title: 'Bowling Practice', type: 'training', time: '8:00 AM' },
                { title: 'Fitness Assessment', type: 'assessment', time: '3:00 PM' },
                { title: 'Team Selection', type: 'selection', time: '11:00 AM' },
                { title: 'Strategy Review', type: 'meeting', time: '4:00 PM' }
            ];
            
            sampleEvents[eventKey] = [sampleActivities[i - 1]];
        }
    }
    
    return sampleEvents;
}

// Generate events using PHP data
let sampleEvents = {};

function initializeCalendar() {
    generateCalendar(currentMonth, currentYear);
    updateMonthDisplay();
}

function generateCalendar(month, year) {
    const calendarDays = document.getElementById('calendarDays');
    if (!calendarDays) return;
    
    calendarDays.innerHTML = '';
    
    // First day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    // Add previous month's trailing days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const dayElement = createDayElement(day, true, year, month - 1);
        calendarDays.appendChild(dayElement);
    }
    
    // Add current month's days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = createDayElement(day, false, year, month);
        calendarDays.appendChild(dayElement);
    }
    
    // Add next month's leading days
    const totalCells = calendarDays.children.length;
    const remainingCells = 42 - totalCells; // 6 weeks * 7 days
    for (let day = 1; day <= remainingCells; day++) {
        const dayElement = createDayElement(day, true, year, month + 1);
        calendarDays.appendChild(dayElement);
    }
}

function createDayElement(day, isOtherMonth, year, month) {
    const dayElement = document.createElement('div');
    dayElement.className = 'calendar-day';
    
    if (isOtherMonth) {
        dayElement.classList.add('other-month');
    }
    
    // Check if it's today
    const today = new Date();
    if (!isOtherMonth && 
        day === today.getDate() && 
        month === today.getMonth() && 
        year === today.getFullYear()) {
        dayElement.classList.add('today');
    }
    
    const dayNumber = document.createElement('div');
    dayNumber.className = 'day-number';
    dayNumber.textContent = day;
    dayElement.appendChild(dayNumber);
    
    // Add events if any
    const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    if (sampleEvents[dateKey]) {
        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'calendar-events';
        
        sampleEvents[dateKey].forEach(event => {
            const eventElement = document.createElement('div');
            eventElement.className = `calendar-event ${event.type}`;
            eventElement.textContent = event.title;
            eventElement.title = `${event.title} - ${event.time}`;
            eventsContainer.appendChild(eventElement);
        });
        
        dayElement.appendChild(eventsContainer);
    }
    
    return dayElement;
}

function updateMonthDisplay() {
    const monthDisplay = document.getElementById('currentMonth');
    if (monthDisplay) {
        monthDisplay.textContent = `${months[currentMonth]} ${currentYear}`;
    }
}

function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
    updateMonthDisplay();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
    updateMonthDisplay();
}

function setView(view) {
    // Update active button
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // For now, just show alert - can be expanded later
    if (view !== 'month') {
        alert(`${view.charAt(0).toUpperCase() + view.slice(1)} view coming soon!`);
        // Reset to month view
        document.querySelector('.view-btn').classList.add('active');
        event.target.classList.remove('active');
    }
}

// Global functions for calendar navigation (called from HTML)
window.previousMonth = previousMonth;
window.nextMonth = nextMonth;
window.setView = setView;

function initializeDashboard() {
    // Initialize dashboard functionality
    console.log('Player Dashboard initialized');
    
    // Add smooth scrolling
    addSmoothScrolling();
    
    // Add hover effects to cards
    enhanceCardInteractions();
    
    // Initialize tooltips if needed
    initializeTooltips();
    
    // Color code activity cards
    colorCodeActivityCards();
    
    // Add activity type badges to schedule table
    addActivityTypeBadges();
}

function colorCodeActivityCards() {
    const infoCards = document.querySelectorAll('.info-card');
    
    infoCards.forEach(card => {
        const activityText = card.textContent.toLowerCase();
        let color = '#4A90E2'; // default blue
        
        if (activityText.includes('practice') || activityText.includes('training')) {
            color = '#27ae60'; // green
        } else if (activityText.includes('match') || activityText.includes('tournament')) {
            color = '#e74c3c'; // red
        } else if (activityText.includes('fitness') || activityText.includes('gym')) {
            color = '#9b59b6'; // purple
        } else if (activityText.includes('meeting') || activityText.includes('review')) {
            color = '#f39c12'; // orange
        } else if (activityText.includes('assessment') || activityText.includes('test')) {
            color = '#34495e'; // dark gray
        } else if (activityText.includes('selection')) {
            color = '#f1c40f'; // yellow
        }
        
        // Apply the color to the left border and icon
        card.style.borderLeftColor = color;
        const icon = card.querySelector('h3 i');
        if (icon) {
            icon.style.color = color;
        }
    });
}

function addActivityTypeBadges() {
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    
    tableRows.forEach(row => {
        const activityCell = row.querySelector('td:nth-child(3)'); // Activity column
        if (activityCell) {
            const activityText = activityCell.textContent.toLowerCase();
            let badgeClass = 'activity-badge';
            let badgeText = 'Event';
            let badgeColor = '#4A90E2';
            
            if (activityText.includes('practice') || activityText.includes('training')) {
                badgeClass += ' practice';
                badgeText = 'Training';
                badgeColor = '#27ae60';
            } else if (activityText.includes('match') || activityText.includes('tournament')) {
                badgeClass += ' match';
                badgeText = 'Match';
                badgeColor = '#e74c3c';
            } else if (activityText.includes('fitness') || activityText.includes('gym')) {
                badgeClass += ' fitness';
                badgeText = 'Fitness';
                badgeColor = '#9b59b6';
            } else if (activityText.includes('meeting') || activityText.includes('review')) {
                badgeClass += ' meeting';
                badgeText = 'Meeting';
                badgeColor = '#f39c12';
            } else if (activityText.includes('assessment') || activityText.includes('test')) {
                badgeClass += ' assessment';
                badgeText = 'Assessment';
                badgeColor = '#34495e';
            } else if (activityText.includes('selection')) {
                badgeClass += ' selection';
                badgeText = 'Selection';
                badgeColor = '#f1c40f';
            }
            
            // Add badge after activity text
            const badge = document.createElement('span');
            badge.className = badgeClass;
            badge.textContent = badgeText;
            badge.style.cssText = `
                background: ${badgeColor};
                color: white;
                padding: 0.2rem 0.5rem;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 600;
                margin-left: 0.5rem;
                display: inline-block;
            `;
            
            activityCell.appendChild(badge);
        }
    });
}

function updateCurrentTime() {
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: true,
                hour: '2-digit',
                minute: '2-digit'
            });
            timeElement.textContent = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000); // Update every second
    }
}

function animateCounters() {
    const statValues = document.querySelectorAll('.stat-value[data-target]');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const target = parseFloat(element.getAttribute('data-target'));
                
                animateValue(element, 0, target, 1500);
                element.parentElement.classList.add('counter-animate');
                
                // Stop observing after animation
                observer.unobserve(element);
            }
        });
    }, observerOptions);
    
    statValues.forEach(element => {
        observer.observe(element);
    });
}

function animateValue(element, start, end, duration) {
    const startTime = Date.now();
    const isDecimal = end % 1 !== 0;
    
    function update() {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function (easeOutCubic)
        const easedProgress = 1 - Math.pow(1 - progress, 3);
        
        const current = start + (end - start) * easedProgress;
        
        if (isDecimal) {
            element.textContent = current.toFixed(2);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

function initializeSidebar() {
    const sidebar = document.getElementById('playerSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-open');
            
            // Close sidebar when clicking outside on mobile
            if (sidebar.classList.contains('sidebar-open')) {
                document.addEventListener('click', closeSidebarOnClickOutside);
            } else {
                document.removeEventListener('click', closeSidebarOnClickOutside);
            }
        });
    }
    
    function closeSidebarOnClickOutside(event) {
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
            sidebar.classList.remove('sidebar-open');
            document.removeEventListener('click', closeSidebarOnClickOutside);
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('sidebar-open');
            document.removeEventListener('click', closeSidebarOnClickOutside);
        }
    });
}

function highlightActiveNavLink() {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPath = window.location.pathname;
    
    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        
        if (linkPath === currentPath || 
            (currentPath.includes('/player') && linkPath === '/player' && currentPath === '/player')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

function addSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function enhanceCardInteractions() {
    const cards = document.querySelectorAll('.stat-card, .info-card, .quick-action-btn');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add click animation to quick action buttons
    const quickActions = document.querySelectorAll('.quick-action-btn');
    quickActions.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                background: rgba(74, 144, 226, 0.3);
                border-radius: 50%;
                transform: translate(${x}px, ${y}px) scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

function initializeTooltips() {
    const elementsWithTooltip = document.querySelectorAll('[title]');
    
    elementsWithTooltip.forEach(element => {
        const title = element.getAttribute('title');
        element.removeAttribute('title');
        
        element.addEventListener('mouseenter', function(e) {
            showTooltip(e, title);
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(event, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        pointer-events: none;
        z-index: 1000;
        white-space: nowrap;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = tooltip.getBoundingClientRect();
    tooltip.style.left = (event.clientX - rect.width / 2) + 'px';
    tooltip.style.top = (event.clientY - rect.height - 10) + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.custom-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function refreshDashboard() {
    const refreshBtn = document.querySelector('.refresh-btn');
    const originalText = refreshBtn.innerHTML;
    
    refreshBtn.innerHTML = '<div class="loading"></div> Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate refresh delay
    setTimeout(() => {
        refreshBtn.innerHTML = originalText;
        refreshBtn.disabled = false;
        
        // Re-animate counters
        animateCounters();
        
        // Show success message
        showNotification('Dashboard refreshed successfully!', 'success');
    }, 2000);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#17a2b8'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        max-width: 400px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    // Add close functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.style.animation = 'slideOutRight 0.3s ease-out forwards';
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease-out forwards';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes ripple {
        to {
            transform: translate(var(--x), var(--y)) scale(4);
            opacity: 0;
        }
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
        opacity: 0.8;
        transition: opacity 0.3s;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
`;
document.head.appendChild(style);

// Utility functions for other pages
window.PlayerDashboard = {
    animateCounters,
    showNotification,
    highlightActiveNavLink,
    initializeSidebar
};

// Handle page visibility change to pause/resume animations
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Page is now hidden, pause animations
        const style = document.createElement('style');
        style.id = 'pause-animations';
        style.textContent = '*, *::before, *::after { animation-play-state: paused !important; }';
        document.head.appendChild(style);
    } else {
        // Page is now visible, resume animations
        const pauseStyle = document.getElementById('pause-animations');
        if (pauseStyle) {
            pauseStyle.remove();
        }
    }
});

// Quick Actions functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeQuickActions();
    animateEventCards();
});

function initializeQuickActions() {
    // Book Training Session
    const bookSessionBtns = document.querySelectorAll('.quick-action-card.book-session .action-btn');
    bookSessionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            showNotification('Redirecting to training booking...', 'info');
            setTimeout(() => {
                window.location.href = '/Elite/player/training';
            }, 1000);
        });
    });

    // View Stats
    const viewStatsBtns = document.querySelectorAll('.quick-action-card.view-stats .action-btn');
    viewStatsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            showNotification('Loading detailed performance stats...', 'info');
            setTimeout(() => {
                window.location.href = '/Elite/player/performance';
            }, 1000);
        });
    });

    // Medical Records
    const medicalBtns = document.querySelectorAll('.quick-action-card.medical-record .action-btn');
    medicalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            showNotification('Opening medical records...', 'info');
            setTimeout(() => {
                window.location.href = '/Elite/player/medical';
            }, 1000);
        });
    });

    // Payment
    const paymentBtns = document.querySelectorAll('.quick-action-card.payment .action-btn');
    paymentBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            showNotification('Redirecting to payment portal...', 'success');
            setTimeout(() => {
                window.location.href = '/Elite/player/payments';
            }, 1000);
        });
    });
}

function animateEventCards() {
    const eventItems = document.querySelectorAll('.event-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    });

    eventItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'all 0.6s ease';
        observer.observe(item);
    });
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;

    // Styles for notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

function getNotificationColor(type) {
    switch(type) {
        case 'success': return 'linear-gradient(45deg, #28a745, #20c997)';
        case 'error': return 'linear-gradient(45deg, #dc3545, #c82333)';
        case 'warning': return 'linear-gradient(45deg, #ffc107, #fd7e14)';
        default: return 'linear-gradient(45deg, #4A90E2, #357ABD)';
    }
}

// Performance monitoring
if (window.performance) {
    window.addEventListener('load', function() {
        setTimeout(function() {
            const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
            console.log(`Player Dashboard loaded in ${loadTime}ms`);
        }, 0);
    });
}

// Navigation toggle function for collapsible menu
function toggleMoreOptions(event) {
    event.preventDefault();
    
    const expandableItem = event.target.closest('.nav-expandable');
    const submenu = document.getElementById('moreOptions');
    const arrow = expandableItem.querySelector('.nav-arrow');
    
    // Toggle expanded state
    expandableItem.classList.toggle('expanded');
    submenu.classList.toggle('show');
    
    // Update aria attributes for accessibility
    const isExpanded = expandableItem.classList.contains('expanded');
    event.target.setAttribute('aria-expanded', isExpanded);
}

// Make toggle function globally available
window.toggleMoreOptions = toggleMoreOptions;
