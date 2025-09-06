// Player Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    updateCurrentTime();
    animateCounters();
    initializeSidebar();
    highlightActiveNavLink();
    initializeCalendar();
});

// Calendar functionality
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

const sampleEvents = {
    '2024-12-15': [
        { title: 'Net Practice', type: 'training', time: '9:00 AM' }
    ],
    '2024-12-18': [
        { title: 'Championship Match', type: 'match', time: '2:00 PM' }
    ],
    '2024-12-22': [
        { title: 'Team Meeting', type: 'meeting', time: '4:00 PM' }
    ],
    '2024-12-25': [
        { title: 'Holiday Training', type: 'training', time: '10:00 AM' }
    ]
};

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

// Performance monitoring
if (window.performance) {
    window.addEventListener('load', function() {
        setTimeout(function() {
            const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
            console.log(`Player Dashboard loaded in ${loadTime}ms`);
        }, 0);
    });
}
