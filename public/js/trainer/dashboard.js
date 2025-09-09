// Modern Trainer Dashboard JavaScript with Enhanced UX
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard with modern animations
    initializeDashboard();
    generateCalendar();
    setupEventListeners();
    initializeAnimations();
});

// Enhanced dashboard initialization
function initializeDashboard() {
    // Show dashboard section by default
    showSection('dashboard');
    
    // Load sample data
    loadSampleEvents();
    
    // Initialize progress indicators
    animateStatsCards();
    
    // Add loading states
    addLoadingStates();
}

// Modern navigation with smooth transitions
function setupEventListeners() {
    // Sidebar navigation with enhanced feedback
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionName = this.getAttribute('data-section');
            showSectionWithTransition(sectionName);
            updateActiveNav(this);
            
            // Add ripple effect
            createRippleEffect(this, e);
        });
        
        // Add hover sound effect (optional)
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px) scale(1.02)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0) scale(1)';
        });
    });

    // Enhanced calendar navigation
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendarWithTransition();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendarWithTransition();
        });
    }

    // Enhanced action buttons with modern feedback
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const action = this.querySelector('span').textContent;
            
            // Add loading state
            this.style.pointerEvents = 'none';
            this.style.opacity = '0.7';
            
            // Simulate processing
            setTimeout(() => {
                handleQuickAction(action);
                this.style.pointerEvents = 'auto';
                this.style.opacity = '1';
            }, 800);
            
            // Add click animation
            createRippleEffect(this, e);
        });
    });
    
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        
        if (window.innerWidth <= 968 && 
            !sidebar.contains(e.target) && 
            !mobileMenuBtn.contains(e.target) &&
            sidebar.classList.contains('mobile-active')) {
            toggleMobileMenu();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileMenuAndModals();
        }
    });
}

// Modern section transitions
function showSectionWithTransition(sectionName) {
    const activeSection = document.querySelector('.content-section.active');
    const targetSection = document.getElementById(`${sectionName}-section`);
    
    if (activeSection && targetSection && activeSection !== targetSection) {
        // Fade out current section
        activeSection.style.opacity = '0';
        activeSection.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            activeSection.classList.remove('active');
            targetSection.classList.add('active');
            
            // Fade in new section
            targetSection.style.opacity = '0';
            targetSection.style.transform = 'translateY(20px)';
            
            requestAnimationFrame(() => {
                targetSection.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                targetSection.style.opacity = '1';
                targetSection.style.transform = 'translateY(0)';
            });
        }, 200);
    } else if (targetSection) {
        targetSection.classList.add('active');
    }
}

// Enhanced ripple effect for modern UI
function createRippleEffect(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
        z-index: 1;
    `;
    
    // Add ripple animation CSS if not exists
    if (!document.querySelector('#ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Initialize modern animations and effects
function initializeAnimations() {
    // Animate stats cards on load
    animateStatsCards();
    
    // Add intersection observer for scroll animations
    setupScrollAnimations();
    
    // Initialize particle effects
    createParticleBackground();
}

// Animate statistics cards with counting effect
function animateStatsCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        // Stagger animation
        setTimeout(() => {
            card.style.transform = 'translateY(0)';
            card.style.opacity = '1';
            
            // Animate the number
            const numberElement = card.querySelector('.stat-info h3');
            if (numberElement) {
                animateNumber(numberElement);
            }
        }, index * 150);
    });
}

// Number counting animation
function animateNumber(element) {
    const target = parseInt(element.textContent);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Setup scroll animations with Intersection Observer
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards';
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    const animateElements = document.querySelectorAll('.calendar-section, .session-section, .quick-actions');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        observer.observe(el);
    });
}

// Create subtle particle background effect
function createParticleBackground() {
    const particleContainer = document.createElement('div');
    particleContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
        overflow: hidden;
    `;
    
    document.body.appendChild(particleContainer);
    
    // Create floating particles
    for (let i = 0; i < 20; i++) {
        createParticle(particleContainer);
    }
}

function createParticle(container) {
    const particle = document.createElement('div');
    const size = Math.random() * 4 + 2;
    const x = Math.random() * window.innerWidth;
    const y = Math.random() * window.innerHeight;
    const duration = Math.random() * 20 + 10;
    
    particle.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        left: ${x}px;
        top: ${y}px;
        animation: float ${duration}s infinite linear;
    `;
    
    // Add floating animation if not exists
    if (!document.querySelector('#particle-style')) {
        const style = document.createElement('style');
        style.id = 'particle-style';
        style.textContent = `
            @keyframes float {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100px) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    container.appendChild(particle);
    
    // Remove particle after animation
    setTimeout(() => {
        if (particle.parentNode) {
            particle.remove();
            // Create new particle to maintain count
            createParticle(container);
        }
    }, duration * 1000);
}

// Enhanced calendar generation with smooth transitions
function generateCalendarWithTransition() {
    const calendar = document.getElementById('calendar');
    if (!calendar) return;
    
    // Add transition effect
    calendar.style.opacity = '0.5';
    calendar.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        generateCalendar();
        calendar.style.transition = 'all 0.3s ease';
        calendar.style.opacity = '1';
        calendar.style.transform = 'scale(1)';
    }, 150);
}

// Enhanced notification system
function showModernNotification(message, type = 'info', duration = 4000) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    // Add icon based on type
    const icons = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-triangle',
        'warning': 'fas fa-exclamation-circle',
        'info': 'fas fa-info-circle'
    };
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="${icons[type] || icons.info}" style="font-size: 1.2rem;"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="margin-left: auto; background: none; border: none; color: inherit; cursor: pointer; padding: 0.25rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove with animation
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.4s ease forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 400);
    }, duration);
}

// Enhanced mobile menu toggle
function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    
    if (sidebar && mobileMenuBtn) {
        const isActive = sidebar.classList.contains('mobile-active');
        
        if (isActive) {
            sidebar.classList.remove('mobile-active');
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        } else {
            sidebar.classList.add('mobile-active');
            mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
        }
        
        // Add backdrop
        toggleBackdrop(!isActive);
    }
}

// Backdrop for mobile menu
function toggleBackdrop(show) {
    let backdrop = document.querySelector('.mobile-backdrop');
    
    if (show && !backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'mobile-backdrop';
        backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 999;
            animation: fadeIn 0.3s ease;
        `;
        backdrop.addEventListener('click', toggleMobileMenu);
        document.body.appendChild(backdrop);
    } else if (!show && backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => backdrop.remove(), 300);
    }
}

function closeMobileMenuAndModals() {
    // Close mobile menu
    const sidebar = document.querySelector('.sidebar');
    if (sidebar && sidebar.classList.contains('mobile-active')) {
        toggleMobileMenu();
    }
    
    // Close any open modals
    const modals = document.querySelectorAll('.event-modal');
    modals.forEach(modal => modal.remove());
}

// Enhanced loading states
function addLoadingStates() {
    const cards = document.querySelectorAll('.stat-card, .calendar-section, .session-section');
    
    cards.forEach(card => {
        // Add shimmer effect while loading
        card.style.background = `
            linear-gradient(90deg, 
                rgba(255, 255, 255, 0.1) 25%, 
                rgba(255, 255, 255, 0.3) 50%, 
                rgba(255, 255, 255, 0.1) 75%
            )
        `;
        card.style.backgroundSize = '200% 100%';
        card.style.animation = 'shimmer 2s infinite';
        
        // Add shimmer animation
        if (!document.querySelector('#shimmer-style')) {
            const style = document.createElement('style');
            style.id = 'shimmer-style';
            style.textContent = `
                @keyframes shimmer {
                    0% { background-position: -200% 0; }
                    100% { background-position: 200% 0; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Remove shimmer after content loads
        setTimeout(() => {
            card.style.background = '';
            card.style.animation = '';
        }, 2000);
    });
}

// Enhanced quick action handling
function handleQuickAction(action) {
    const actionMessages = {
        'Add Session': { msg: '🎯 Session creation panel opening...', type: 'info' },
        'New Player': { msg: '👤 Player registration form ready!', type: 'success' },
        'Medical Report': { msg: '📋 Medical records system loading...', type: 'info' },
        'Workout Plan': { msg: '💪 Workout planner initialized!', type: 'success' }
    };
    
    const config = actionMessages[action] || { msg: '✨ Feature coming soon!', type: 'info' };
    showModernNotification(config.msg, config.type);
}

// Add fade-out animations
const fadeOutStyle = document.createElement('style');
fadeOutStyle.textContent = `
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(fadeOutStyle);

// Show specific section (keeping original functionality)
function showSection(sectionName) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.add('active');
    }
}

// Update active navigation
function updateActiveNav(activeLink) {
    // Remove active class from all nav items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to parent of clicked link
    activeLink.parentElement.classList.add('active');
}

// Calendar functionality
let currentDate = new Date();
let events = [];

// Sample events data
function loadSampleEvents() {
    const today = new Date();
    events = [
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            type: 'group-session',
            title: 'Youth Cricket Program',
            time: '09:00 AM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            type: 'private-session',
            title: 'Kumara Silva - Fitness',
            time: '11:30 AM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            type: 'group-session',
            title: 'Advanced Training',
            time: '02:00 PM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1),
            type: 'tournament',
            title: 'Inter-Academy Match',
            time: '10:00 AM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 2),
            type: 'private-session',
            title: 'Anjali Perera - Recovery',
            time: '03:00 PM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 5),
            type: 'group-session',
            title: 'Weekend Camp',
            time: '09:00 AM'
        }
    ];
}

// Generate calendar
function generateCalendar() {
    const calendar = document.getElementById('calendar');
    const currentMonthElement = document.getElementById('currentMonth');
    
    if (!calendar || !currentMonthElement) return;
    
    // Clear calendar
    calendar.innerHTML = '';
    
    // Update month display
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    currentMonthElement.textContent = `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        calendar.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    // Generate calendar days
    const today = new Date();
    for (let i = 0; i < 42; i++) {
        const day = new Date(startDate);
        day.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Check if day is in current month
        if (day.getMonth() !== currentDate.getMonth()) {
            dayElement.classList.add('other-month');
        }
        
        // Check if day is today
        if (day.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }
        
        // Add day number
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';
        dayNumber.textContent = day.getDate();
        dayElement.appendChild(dayNumber);
        
        // Add events for this day
        const dayEvents = events.filter(event => 
            event.date.toDateString() === day.toDateString()
        );
        
        if (dayEvents.length > 0) {
            dayElement.classList.add('has-events');
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'events-container';
            
            dayEvents.forEach(event => {
                const eventDot = document.createElement('div');
                eventDot.className = `event-dot ${event.type}`;
                eventDot.title = `${event.title} - ${event.time}`;
                eventsContainer.appendChild(eventDot);
            });
            
            dayElement.appendChild(eventsContainer);
        }
        
        // Add click event
        dayElement.addEventListener('click', () => {
            showDayEvents(day, dayEvents);
        });
        
        calendar.appendChild(dayElement);
    }
}

// Show events for selected day
function showDayEvents(date, dayEvents) {
    const modal = createModernEventModal(date, dayEvents);
    document.body.appendChild(modal);
}

// Create modern event modal
function createModernEventModal(date, dayEvents) {
    const modal = document.createElement('div');
    modal.className = 'event-modal';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'event-modal-content';
    
    const header = document.createElement('div');
    header.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    `;
    
    const title = document.createElement('h3');
    title.textContent = `Events for ${date.toLocaleDateString()}`;
    title.style.cssText = `
        color: white;
        font-size: 1.3rem;
        font-weight: 600;
        background: linear-gradient(135deg, #fff 0%, #e0e7ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    `;
    
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.style.cssText = `
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        color: white;
        transition: all 0.3s ease;
    `;
    closeBtn.addEventListener('click', () => {
        modal.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => document.body.removeChild(modal), 300);
    });
    closeBtn.addEventListener('mouseenter', () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.2)';
        closeBtn.style.transform = 'scale(1.1)';
    });
    closeBtn.addEventListener('mouseleave', () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.1)';
        closeBtn.style.transform = 'scale(1)';
    });
    
    header.appendChild(title);
    header.appendChild(closeBtn);
    modalContent.appendChild(header);
    
    if (dayEvents.length === 0) {
        const noEvents = document.createElement('div');
        noEvents.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: rgba(255, 255, 255, 0.7);">
                <i class="fas fa-calendar-day" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p style="font-size: 1.1rem;">No events scheduled for this day.</p>
            </div>
        `;
        modalContent.appendChild(noEvents);
    } else {
        dayEvents.forEach((event, index) => {
            const eventItem = document.createElement('div');
            eventItem.style.cssText = `
                padding: 1.5rem;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 12px;
                margin-bottom: 1rem;
                border-left: 4px solid ${getEventColor(event.type)};
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
                animation: slideInUp 0.4s ease ${index * 0.1}s both;
            `;
            
            eventItem.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <i class="fas fa-clock" style="color: ${getEventColor(event.type)};"></i>
                    <span style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">${event.time}</span>
                </div>
                <h4 style="margin-bottom: 0.5rem; color: white; font-size: 1.1rem; font-weight: 600;">${event.title}</h4>
                <span style="
                    padding: 0.5rem 1rem;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    background: ${getEventBackgroundColor(event.type)};
                    color: ${getEventColor(event.type)};
                    border: 1px solid ${getEventColor(event.type)}30;
                ">${event.type.replace('-', ' ')}</span>
            `;
            
            eventItem.addEventListener('mouseenter', () => {
                eventItem.style.transform = 'translateY(-2px)';
                eventItem.style.boxShadow = '0 8px 25px rgba(255, 255, 255, 0.15)';
            });
            
            eventItem.addEventListener('mouseleave', () => {
                eventItem.style.transform = 'translateY(0)';
                eventItem.style.boxShadow = 'none';
            });
            
            modalContent.appendChild(eventItem);
        });
    }
    
    modal.appendChild(modalContent);
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => document.body.removeChild(modal), 300);
        }
    });
    
    return modal;
}

// Get event colors
function getEventColor(type) {
    switch (type) {
        case 'group-session': return '#10b981';
        case 'private-session': return '#f59e0b';
        case 'tournament': return '#ef4444';
        default: return '#6b7280';
    }
}

function getEventBackgroundColor(type) {
    switch (type) {
        case 'group-session': return 'rgba(16, 185, 129, 0.2)';
        case 'private-session': return 'rgba(245, 158, 11, 0.2)';
        case 'tournament': return 'rgba(239, 68, 68, 0.2)';
        default: return 'rgba(107, 114, 128, 0.2)';
    }
}

// Override the old notification function with the new one
function showNotification(message, type = 'info') {
    showModernNotification(message, type);
}

// Add slide-in-up animation for modal events
const slideInUpStyle = document.createElement('style');
slideInUpStyle.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(slideInUpStyle);

// Nutrition & Supplements Functionality
function initializeNutritionSection() {
    setupNutritionFilters();
    setupPlanActions();
    initializePlanModals();
}

// Nutrition filter functionality
function setupNutritionFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const planCards = document.querySelectorAll('.plan-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filter plan cards with animation
            planCards.forEach(card => {
                const cardType = card.getAttribute('data-plan-type');
                
                if (filter === 'all' || cardType === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease forwards';
                } else {
                    card.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

// Plan actions (assign, edit, delete, view)
function setupPlanActions() {
    // Assign plan buttons
    const assignBtns = document.querySelectorAll('.assign-btn');
    assignBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const planCard = this.closest('.plan-card');
            const planTitle = planCard.querySelector('.plan-title h3').textContent;
            openAssignmentModal(planTitle);
        });
    });
    
    // View plan buttons
    const viewBtns = document.querySelectorAll('.view-btn');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const planCard = this.closest('.plan-card');
            const planTitle = planCard.querySelector('.plan-title h3').textContent;
            openPlanDetailsModal(planTitle, planCard);
        });
    });
    
    // Edit plan buttons
    const editBtns = document.querySelectorAll('.action-btn-small');
    editBtns.forEach(btn => {
        if (btn.querySelector('.fa-edit')) {
            btn.addEventListener('click', function() {
                const planCard = this.closest('.plan-card');
                const planTitle = planCard.querySelector('.plan-title h3').textContent;
                openEditPlanModal(planTitle, planCard);
            });
        }
        
        if (btn.querySelector('.fa-trash')) {
            btn.addEventListener('click', function() {
                const planCard = this.closest('.plan-card');
                const planTitle = planCard.querySelector('.plan-title h3').textContent;
                confirmDeletePlan(planTitle, planCard);
            });
        }
    });
    
    // Create new plan button
    const createPlanBtn = document.getElementById('createPlanBtn');
    if (createPlanBtn) {
        createPlanBtn.addEventListener('click', function() {
            openCreatePlanModal();
        });
    }
}

// Assignment modal
function openAssignmentModal(planTitle) {
    const modal = createNutritionModal(`
        <h2>Assign Plan: ${planTitle}</h2>
        <div class="assignment-options">
            <div class="assignment-type">
                <h3>Assignment Type</h3>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="assignType" value="individual" checked>
                        <span>Individual Player</span>
                    </label>
                    <label>
                        <input type="radio" name="assignType" value="group">
                        <span>Group</span>
                    </label>
                    <label>
                        <input type="radio" name="assignType" value="team">
                        <span>Entire Team</span>
                    </label>
                </div>
            </div>
            
            <div class="player-selection">
                <h3>Select Players</h3>
                <div class="search-box">
                    <input type="text" placeholder="Search players..." class="search-input">
                    <i class="fas fa-search"></i>
                </div>
                <div class="player-list">
                    <div class="player-item">
                        <input type="checkbox" id="player1">
                        <label for="player1">
                            <div class="player-avatar">KS</div>
                            <span>Kamal Silva</span>
                        </label>
                    </div>
                    <div class="player-item">
                        <input type="checkbox" id="player2">
                        <label for="player2">
                            <div class="player-avatar">AP</div>
                            <span>Anjali Perera</span>
                        </label>
                    </div>
                    <div class="player-item">
                        <input type="checkbox" id="player3">
                        <label for="player3">
                            <div class="player-avatar">DF</div>
                            <span>Dasun Fernando</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="assignment-schedule">
                <h3>Schedule</h3>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-input" value="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="form-group">
                    <label>Duration (weeks)</label>
                    <input type="number" class="form-input" min="1" max="52" value="6">
                </div>
            </div>
        </div>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-primary assign-confirm-btn">Assign Plan</button>
        </div>
    `);
    
    // Handle assignment confirmation
    const assignConfirmBtn = modal.querySelector('.assign-confirm-btn');
    assignConfirmBtn.addEventListener('click', function() {
        const selectedPlayers = modal.querySelectorAll('.player-item input:checked');
        if (selectedPlayers.length > 0) {
            showModernNotification(`Plan "${planTitle}" assigned to ${selectedPlayers.length} player(s)`, 'success');
            closeModal(modal);
        } else {
            showModernNotification('Please select at least one player', 'warning');
        }
    });
}

// Plan details modal
function openPlanDetailsModal(planTitle, planCard) {
    const planType = planCard.getAttribute('data-plan-type');
    const planDescription = planCard.querySelector('.plan-description').textContent;
    
    let detailsContent = '';
    
    if (planType === 'supplement') {
        const scheduleItems = planCard.querySelectorAll('.schedule-item');
        detailsContent = '<div class="supplement-details"><h3>Supplement Schedule</h3>';
        scheduleItems.forEach(item => {
            const timeLabel = item.querySelector('.time-label').textContent;
            const supplementName = item.querySelector('.supplement-name').textContent;
            detailsContent += `
                <div class="detail-row">
                    <span class="time">${timeLabel}</span>
                    <span class="supplement">${supplementName}</span>
                </div>
            `;
        });
        detailsContent += '</div>';
    } else {
        detailsContent = `
            <div class="diet-details">
                <h3>Diet Plan Details</h3>
                <div class="detail-section">
                    <h4>Nutritional Guidelines</h4>
                    <ul>
                        <li>High protein intake: 2.5g per kg body weight</li>
                        <li>Complex carbohydrates: 60% of total calories</li>
                        <li>Healthy fats: 20% of total calories</li>
                        <li>Hydration: 3-4 liters per day</li>
                    </ul>
                </div>
                <div class="detail-section">
                    <h4>Meal Structure</h4>
                    <ul>
                        <li>6 meals per day (3 main, 3 snacks)</li>
                        <li>Pre-workout meal 2-3 hours before training</li>
                        <li>Post-workout meal within 30 minutes</li>
                        <li>Adequate rest period between meals</li>
                    </ul>
                </div>
            </div>
        `;
    }
    
    const modal = createNutritionModal(`
        <h2>${planTitle}</h2>
        <p class="plan-description">${planDescription}</p>
        ${detailsContent}
        <div class="modal-actions">
            <button class="btn-primary close-btn">Close</button>
        </div>
    `);
}

// Create plan modal
function openCreatePlanModal() {
    const modal = createNutritionModal(`
        <h2>Create New Plan</h2>
        <div class="create-plan-form">
            <div class="form-group">
                <label>Plan Type</label>
                <select class="form-input" id="planType">
                    <option value="diet">Diet Plan</option>
                    <option value="supplement">Supplement Plan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Plan Name</label>
                <input type="text" class="form-input" placeholder="Enter plan name...">
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select class="form-input">
                    <option value="general">General</option>
                    <option value="personalized">Personalized</option>
                    <option value="tournament">Tournament</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-input" rows="3" placeholder="Enter plan description..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Duration (weeks)</label>
                <input type="number" class="form-input" min="1" max="52" value="6">
            </div>
        </div>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-primary create-btn">Create Plan</button>
        </div>
    `);
    
    const createBtn = modal.querySelector('.create-btn');
    createBtn.addEventListener('click', function() {
        const planName = modal.querySelector('input[placeholder="Enter plan name..."]').value;
        if (planName.trim()) {
            showModernNotification(`Plan "${planName}" created successfully`, 'success');
            closeModal(modal);
        } else {
            showModernNotification('Please enter a plan name', 'warning');
        }
    });
}

// Edit plan modal
function openEditPlanModal(planTitle, planCard) {
    const planDescription = planCard.querySelector('.plan-description').textContent;
    
    const modal = createNutritionModal(`
        <h2>Edit Plan: ${planTitle}</h2>
        <div class="edit-plan-form">
            <div class="form-group">
                <label>Plan Name</label>
                <input type="text" class="form-input" value="${planTitle}">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-input" rows="3">${planDescription}</textarea>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select class="form-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-primary save-btn">Save Changes</button>
        </div>
    `);
    
    const saveBtn = modal.querySelector('.save-btn');
    saveBtn.addEventListener('click', function() {
        showModernNotification(`Plan "${planTitle}" updated successfully`, 'success');
        closeModal(modal);
    });
}

// Delete confirmation
function confirmDeletePlan(planTitle, planCard) {
    const modal = createNutritionModal(`
        <h2>Delete Plan</h2>
        <p>Are you sure you want to delete the plan "<strong>${planTitle}</strong>"?</p>
        <p class="warning-text">This action cannot be undone and will remove the plan from all assigned players.</p>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-danger delete-confirm-btn">Delete Plan</button>
        </div>
    `);
    
    const deleteConfirmBtn = modal.querySelector('.delete-confirm-btn');
    deleteConfirmBtn.addEventListener('click', function() {
        // Add fade out animation to the plan card
        planCard.style.animation = 'fadeOut 0.5s ease forwards';
        setTimeout(() => {
            planCard.remove();
        }, 500);
        
        showModernNotification(`Plan "${planTitle}" deleted successfully`, 'success');
        closeModal(modal);
    });
}

// Create nutrition modal
function createNutritionModal(content) {
    const modal = document.createElement('div');
    modal.className = 'nutrition-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            ${content}
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Animation
    modal.style.animation = 'fadeIn 0.3s ease forwards';
    
    // Close handlers
    const closeBtn = modal.querySelector('.modal-close');
    const cancelBtn = modal.querySelector('.cancel-btn');
    const closeConfirmBtn = modal.querySelector('.close-btn');
    
    [closeBtn, cancelBtn, closeConfirmBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => closeModal(modal));
        }
    });
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    return modal;
}

// Close modal
function closeModal(modal) {
    modal.style.animation = 'fadeOut 0.3s ease forwards';
    setTimeout(() => {
        if (modal.parentNode) {
            document.body.removeChild(modal);
        }
    }, 300);
}

// Initialize nutrition functionality when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize existing functionality
    initializeDashboard();
    generateCalendar();
    setupEventListeners();
    initializeAnimations();
    
    // Initialize nutrition section
    initializeNutritionSection();
});

// Add nutrition modal styles
const nutritionModalStyles = document.createElement('style');
nutritionModalStyles.textContent = `
    .nutrition-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(5px);
    }
    
    .nutrition-modal .modal-content {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
        box-shadow: var(--glass-shadow);
    }
    
    .nutrition-modal .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .nutrition-modal .modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }
    
    .nutrition-modal h2 {
        color: var(--text-primary);
        margin-bottom: 1rem;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .assignment-options,
    .create-plan-form,
    .edit-plan-form {
        margin: 1.5rem 0;
    }
    
    .assignment-type h3,
    .player-selection h3,
    .assignment-schedule h3 {
        color: var(--text-primary);
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    
    .radio-group {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .radio-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        color: var(--text-secondary);
    }
    
    .search-box {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
        backdrop-filter: blur(10px);
    }
    
    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }
    
    .player-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
    }
    
    .player-item {
        padding: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .player-item label {
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        color: var(--text-secondary);
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
        backdrop-filter: blur(10px);
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, var(--error-color), #dc2626);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
    }
    
    .warning-text {
        color: var(--warning-color);
        font-style: italic;
        margin: 1rem 0;
    }
    
    .supplement-details,
    .diet-details {
        margin: 1.5rem 0;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .detail-row .time {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .detail-section {
        margin-bottom: 1.5rem;
    }
    
    .detail-section h4 {
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .detail-section ul {
        color: var(--text-secondary);
        padding-left: 1.5rem;
    }
    
    .detail-section li {
        margin-bottom: 0.25rem;
    }
`;
document.head.appendChild(nutritionModalStyles);
