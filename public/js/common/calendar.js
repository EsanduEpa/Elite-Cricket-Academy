/**
 * Common Calendar Component
 * Reusable calendar functionality for all dashboards
 */

class CommonCalendar {
    constructor(options = {}) {
        this.containerId = options.containerId || 'calendar';
        this.eventsUrl = options.eventsUrl || '';
        this.calendarType = options.calendarType || 'general';
        this.userRole = options.userRole || 'user';
        this.calendar = null;
        this.defaultEvents = options.defaultEvents || [];
        
        // Calendar configuration
        this.config = {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 'auto',
            contentHeight: 400,
            aspectRatio: 1.8,
            eventDisplay: 'block',
            dayMaxEvents: 3,
            moreLinkClick: 'popover',
            eventClick: this.handleEventClick.bind(this),
            dateClick: this.handleDateClick.bind(this),
            eventClassNames: this.getEventClassNames.bind(this),
            eventDidMount: this.styleEvent.bind(this),
            loading: this.handleLoading.bind(this),
            ...options.config
        };
        
        this.initialize();
    }
    
    initialize() {
        const calendarEl = document.getElementById(this.containerId);
        if (!calendarEl) {
            console.error(`Calendar container #${this.containerId} not found`);
            return;
        }
        
        // Set up events source
        if (this.eventsUrl) {
            this.config.events = this.eventsUrl;
        } else {
            this.config.events = this.getDefaultEvents();
        }
        
        // Initialize FullCalendar
        this.calendar = new FullCalendar.Calendar(calendarEl, this.config);
        this.calendar.render();
        
        // Set up additional event handlers
        this.setupEventHandlers();
        
        console.log(`Calendar initialized for ${this.calendarType} (${this.userRole})`);
    }
    
    setupEventHandlers() {
        // Navigation button handlers
        const prevBtn = document.getElementById('prevMonth');
        const nextBtn = document.getElementById('nextMonth');
        const todayBtn = document.getElementById('todayBtn');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.calendar.prev();
                this.updateCurrentMonth();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.calendar.next();
                this.updateCurrentMonth();
            });
        }
        
        if (todayBtn) {
            todayBtn.addEventListener('click', () => {
                this.calendar.today();
                this.updateCurrentMonth();
            });
        }
        
        // Update month display initially
        this.updateCurrentMonth();
    }
    
    updateCurrentMonth() {
        const currentMonthEl = document.getElementById('currentMonth');
        if (currentMonthEl && this.calendar) {
            const currentDate = this.calendar.getDate();
            const monthYear = currentDate.toLocaleDateString('en-US', { 
                month: 'long', 
                year: 'numeric' 
            });
            currentMonthEl.textContent = monthYear;
        }
    }
    
    handleEventClick(info) {
        const event = info.event;
        console.log('Event clicked:', event.title);
        
        // Role-based event handling
        switch (this.userRole) {
            case 'admin':
                this.openAdminEventModal(event);
                break;
            case 'coach':
                this.openCoachEventModal(event);
                break;
            case 'trainer':
                this.openTrainerEventModal(event);
                break;
            case 'player':
                this.openPlayerEventModal(event);
                break;
            default:
                this.openDefaultEventModal(event);
        }
    }
    
    handleDateClick(info) {
        console.log('Date clicked:', info.dateStr);
        
        // Only allow adding events for admin role
        if (this.userRole === 'admin') {
            this.openCreateEventModal(info.date);
        } else {
            // For other roles, just show date info
            this.showDateInfo(info.date);
        }
    }
    
    getEventClassNames(arg) {
        const event = arg.event;
        const type = event.extendedProps.type || 'general';
        const priority = event.extendedProps.priority || 'normal';
        
        return [
            `event-${type}`,
            `priority-${priority}`,
            `calendar-${this.calendarType}`
        ];
    }
    
    styleEvent(arg) {
        const event = arg.event;
        const element = arg.el;
        
        // Add custom styling based on event properties
        if (event.extendedProps.status === 'cancelled') {
            element.style.opacity = '0.5';
            element.style.textDecoration = 'line-through';
        }
        
        if (event.extendedProps.isUrgent) {
            element.style.border = '2px solid #dc2626';
            element.style.boxShadow = '0 0 8px rgba(220, 38, 38, 0.3)';
        }
    }
    
    handleLoading(isLoading) {
        const loadingEl = document.getElementById('calendarLoading');
        if (loadingEl) {
            loadingEl.style.display = isLoading ? 'block' : 'none';
        }
    }
    
    // Default events for when no API is available
    getDefaultEvents() {
        const baseEvents = [
            {
                id: '1',
                title: 'Training Session',
                start: new Date().toISOString().split('T')[0] + 'T09:00:00',
                end: new Date().toISOString().split('T')[0] + 'T11:00:00',
                type: 'training',
                extendedProps: {
                    type: 'training',
                    priority: 'high',
                    description: 'Morning training session'
                }
            },
            {
                id: '2',
                title: 'Team Meeting',
                start: new Date(Date.now() + 86400000).toISOString().split('T')[0] + 'T14:00:00',
                type: 'meeting',
                extendedProps: {
                    type: 'meeting',
                    priority: 'normal',
                    description: 'Weekly team meeting'
                }
            },
            {
                id: '3',
                title: 'Championship Match',
                start: new Date(Date.now() + 86400000 * 3).toISOString().split('T')[0] + 'T15:30:00',
                type: 'match',
                extendedProps: {
                    type: 'match',
                    priority: 'high',
                    description: 'Important championship match',
                    isUrgent: true
                }
            }
        ];
        
        return [...baseEvents, ...this.defaultEvents];
    }
    
    // Role-specific event modal handlers
    openAdminEventModal(event) {
        if (typeof openEventDetails === 'function') {
            openEventDetails(event);
        } else {
            this.showEventDetails(event);
        }
    }
    
    openCoachEventModal(event) {
        this.showEventDetails(event, 'coach');
    }
    
    openTrainerEventModal(event) {
        this.showEventDetails(event, 'trainer');
    }
    
    openPlayerEventModal(event) {
        this.showEventDetails(event, 'player');
    }
    
    openDefaultEventModal(event) {
        this.showEventDetails(event);
    }
    
    openCreateEventModal(date) {
        if (typeof openCreateEventModal === 'function') {
            openCreateEventModal(date);
        } else {
            console.log('Create event modal not available for this role');
        }
    }
    
    showDateInfo(date) {
        const dateStr = date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        alert(`Selected date: ${dateStr}`);
    }
    
    showEventDetails(event, role = null) {
        const details = `
Event: ${event.title}
Date: ${event.start.toLocaleDateString()}
Time: ${event.start.toLocaleTimeString()}
${event.extendedProps.description ? 'Description: ' + event.extendedProps.description : ''}
${role ? 'View: ' + role : ''}
        `.trim();
        
        alert(details);
    }
    
    // Public methods for external use
    addEvent(eventData) {
        if (this.calendar) {
            this.calendar.addEvent(eventData);
        }
    }
    
    removeEvent(eventId) {
        if (this.calendar) {
            const event = this.calendar.getEventById(eventId);
            if (event) {
                event.remove();
            }
        }
    }
    
    updateEvent(eventId, updates) {
        if (this.calendar) {
            const event = this.calendar.getEventById(eventId);
            if (event) {
                event.setProp('title', updates.title || event.title);
                if (updates.start) event.setStart(updates.start);
                if (updates.end) event.setEnd(updates.end);
                if (updates.extendedProps) {
                    Object.assign(event.extendedProps, updates.extendedProps);
                }
            }
        }
    }
    
    refreshEvents() {
        if (this.calendar) {
            this.calendar.refetchEvents();
        }
    }
    
    changeView(viewName) {
        if (this.calendar) {
            this.calendar.changeView(viewName);
        }
    }
    
    goToDate(date) {
        if (this.calendar) {
            this.calendar.gotoDate(date);
        }
    }
    
    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}

// Utility function to create calendar with default settings
function createDashboardCalendar(options = {}) {
    // Detect user role from URL or global variable
    const currentPath = window.location.pathname;
    let userRole = 'user';
    
    if (currentPath.includes('/admin/')) {
        userRole = 'admin';
    } else if (currentPath.includes('/coach/')) {
        userRole = 'coach';
    } else if (currentPath.includes('/trainer/')) {
        userRole = 'trainer';
    } else if (currentPath.includes('/player/')) {
        userRole = 'player';
    }
    
    const defaultOptions = {
        containerId: 'eventCalendar',
        userRole: userRole,
        calendarType: 'dashboard',
        eventsUrl: `${window.location.origin}${currentPath.split('/').slice(0, -1).join('/')}/get_calendar_events`
    };
    
    return new CommonCalendar({ ...defaultOptions, ...options });
}

// Auto-initialize calendar if container exists
document.addEventListener('DOMContentLoaded', function() {
    const calendarContainer = document.getElementById('eventCalendar');
    if (calendarContainer && !window.calendarInstance) {
        window.calendarInstance = createDashboardCalendar();
    }
});

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { CommonCalendar, createDashboardCalendar };
}