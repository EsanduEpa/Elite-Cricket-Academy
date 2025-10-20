// Events & Tournaments Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeEventPage();
});

function initializeEventPage() {
    // Initialize sidebar functionality (reuse from dashboard)
    initializeSidebar();
    
    // Initialize event handlers
    initializeEventHandlers();
    
    // Initialize modal functionality
    initializeModalHandlers();
    
    // Initialize search and filters
    initializeSearchFilters();
}

function initializeEventHandlers() {
    // Note: Create event buttons are now handled by events.php and create-event-wizard.js
    // Removed conflicting event handlers for createEventBtn and createTournamentBtn
    
    // View all buttons
    const viewAllUpcomingBtn = document.getElementById('viewAllUpcomingBtn');
    const viewAllPastBtn = document.getElementById('viewAllPastBtn');
    
    if (viewAllUpcomingBtn) {
        viewAllUpcomingBtn.addEventListener('click', () => viewAllEvents('upcoming'));
    }
    
    if (viewAllPastBtn) {
        viewAllPastBtn.addEventListener('click', () => viewAllEvents('past'));
    }
    
    // Calendar navigation
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    if (prevMonthBtn && window.eventCalendar) {
        prevMonthBtn.addEventListener('click', () => {
            window.eventCalendar.prev();
            updateCurrentMonthDisplay();
        });
    }
    
    if (nextMonthBtn && window.eventCalendar) {
        nextMonthBtn.addEventListener('click', () => {
            window.eventCalendar.next();
            updateCurrentMonthDisplay();
        });
    }
}

function initializeModalHandlers() {
    const modal = document.getElementById('eventModal');
    const closeBtn = document.querySelector('.modal-close');
    const form = document.getElementById('eventForm');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && modal.style.display === 'block') {
            closeModal();
        }
    });
}

function initializeSearchFilters() {
    // Add search functionality for events
    const searchInput = document.getElementById('eventSearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterEvents, 300));
    }
    
    // Add filter functionality
    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', filterEvents);
    }
}

function openCreateModal(type = '', date = '') {
    // Legacy function - redirecting to new wizard system
    console.log('Legacy openCreateModal called, redirecting to new wizard...');
    
    if (typeof openCreateEventModal === 'function') {
        openCreateEventModal();
        
        // Pre-fill form data if provided
        setTimeout(() => {
            if (type === 'tournament') {
                const eventTypeSelect = document.getElementById('eventType');
                if (eventTypeSelect) {
                    eventTypeSelect.value = 'tournament';
                }
            }
            
            if (date) {
                const startDateInput = document.getElementById('startDate');
                if (startDateInput) {
                    startDateInput.value = date;
                }
            }
        }, 200);
    } else {
        console.error('New wizard system not available, openCreateEventModal function not found');
    }
}


function editEvent(eventId) {
    if (!eventId) {
        alert("⚠️ Invalid event ID");
        return;
    }
    // Redirect to edit page - construct URL from current location
    window.location.href = `${window.location.origin}/Elite/admin/edit_event/${eventId}`;
}



function populateEditForm(event) {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    const title = document.getElementById('modalTitle');
    
    if (!modal || !form || !title) return;
    
    title.textContent = 'Edit Event';
    form.action = `${window.location.origin}/Elite/admin/edit_event/${event.id}`;
    
    // Populate form fields
    const fields = {
        'eventTitle': event.title,
        'eventType': event.event_type,
        'eventDate': event.event_date,
        'eventLocation': event.location,
        'eventDescription': event.description
    };
    
    Object.entries(fields).forEach(([fieldId, value]) => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value || '';
        }
    });
    
    modal.style.display = 'block';
}

function deleteEvent(eventId) {
    // Create custom confirmation modal for better UX
    const confirmed = confirm('Are you sure you want to delete this event? This action cannot be undone.');
    
    if (confirmed) {
        showLoading();
        
        // In a real application, this would be an AJAX request
        window.location.href = `${window.location.origin}/Elite/admin/delete_event/${eventId}`;
    }
}

function viewEvent(eventId) {
    // Redirect to event details page
    window.location.href = `${window.location.origin}/Elite/admin/event_details/${eventId}`;
}

function closeModal() {
    const modal = document.getElementById('eventModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function viewAllEvents(type) {
    // Add filter parameter to URL
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('filter', type);
    window.location.href = currentUrl.toString();
}

function viewEventDetails(event) {
    // Create and show event details popup
    const details = `
        <div class="event-details-popup">
            <h3>${event.title}</h3>
            <p><strong>Date:</strong> ${formatDate(event.start)}</p>
            <p><strong>Type:</strong> ${event.extendedProps.type}</p>
            <p><strong>Location:</strong> ${event.extendedProps.location}</p>
            <p><strong>Description:</strong> ${event.extendedProps.description}</p>
            <div class="popup-actions">
                <button onclick="editEvent(${event.id})" class="btn btn-primary">Edit</button>
                <button onclick="deleteEvent(${event.id})" class="btn btn-danger">Delete</button>
            </div>
        </div>
    `;
    
    // For demo purposes, use alert. In production, use a proper modal
    alert(`Event: ${event.title}\nDate: ${formatDate(event.start)}\nType: ${event.extendedProps.type}\nLocation: ${event.extendedProps.location}\nDescription: ${event.extendedProps.description}`);
}

function handleFormSubmit(event) {
    // Add form validation
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate required fields
    const requiredFields = ['title', 'event_type', 'event_date', 'location', 'description'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const value = formData.get(field);
        if (!value || value.trim() === '') {
            isValid = false;
            const fieldElement = document.getElementById(field === 'title' ? 'eventTitle' : 
                                                      field === 'event_type' ? 'eventType' :
                                                      field === 'event_date' ? 'eventDate' :
                                                      field === 'location' ? 'eventLocation' : 'eventDescription');
            if (fieldElement) {
                fieldElement.style.borderColor = '#FF6B6B';
                fieldElement.focus();
            }
        }
    });
    
    if (!isValid) {
        event.preventDefault();
        showNotification('Please fill in all required fields', 'error');
        return false;
    }
    
    // Show loading state
    showLoading();
    
    // Form will submit normally for demo purposes
    return true;
}

function updateCurrentMonthDisplay() {
    if (window.eventCalendar) {
        const currentDate = window.eventCalendar.getDate();
        const monthElement = document.getElementById('currentMonth');
        if (monthElement) {
            monthElement.textContent = formatMonthYear(currentDate);
        }
    }
}

function filterEvents() {
    const searchTerm = document.getElementById('eventSearch')?.value.toLowerCase() || '';
    const typeFilter = document.getElementById('typeFilter')?.value || '';
    
    const eventItems = document.querySelectorAll('.event-item');
    
    eventItems.forEach(item => {
        const title = item.querySelector('.event-title')?.textContent.toLowerCase() || '';
        const description = item.querySelector('.event-description')?.textContent.toLowerCase() || '';
        const type = item.querySelector('.event-type')?.textContent.toLowerCase() || '';
        
        const matchesSearch = searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm);
        const matchesType = typeFilter === '' || type.includes(typeFilter.toLowerCase());
        
        if (matchesSearch && matchesType) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Utility Functions
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatMonthYear(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showLoading() {
    // Create or show loading indicator
    let loader = document.getElementById('loadingIndicator');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'loadingIndicator';
        loader.innerHTML = `
            <div class="loading-overlay">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Processing...</p>
                </div>
            </div>
        `;
        loader.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3000;
        `;
        document.body.appendChild(loader);
    }
    loader.style.display = 'flex';
}

function hideLoading() {
    const loader = document.getElementById('loadingIndicator');
    if (loader) {
        loader.style.display = 'none';
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="notification-close">×</button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#FF6B6B' : '#4A90E2'};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        z-index: 3000;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Reuse sidebar functionality from dashboard
function initializeSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');

    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '80px';
            } else {
                mainContent.style.marginLeft = '280px';
            }
        });
    }

    // Navigation item active states
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
        });
    });
}
