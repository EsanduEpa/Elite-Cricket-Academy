// Trainer Events JavaScript - Elite Cricket Academy

document.addEventListener('DOMContentLoaded', function() {
    initializeTrainerEvents();
    initializeSidebar();
});

// Trainer Events Initialization
function initializeTrainerEvents() {
    // Calendar navigation
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    const currentMonthSpan = document.getElementById('currentMonth');
    
    if (prevBtn && nextBtn && currentMonthSpan) {
        let currentDate = new Date();
        
        prevBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendarDisplay(currentDate, currentMonthSpan);
        });
        
        nextBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendarDisplay(currentDate, currentMonthSpan);
        });
    }
    
    // View All buttons
    const viewAllUpcomingBtn = document.getElementById('viewAllUpcomingBtn');
    const viewAllPastBtn = document.getElementById('viewAllPastBtn');
    
    if (viewAllUpcomingBtn) {
        viewAllUpcomingBtn.addEventListener('click', function() {
            showAllEvents('upcoming');
        });
    }
    
    if (viewAllPastBtn) {
        viewAllPastBtn.addEventListener('click', function() {
            showAllEvents('past');
        });
    }
    
    // Event item interactions
    initializeEventInteractions();
    
    // Trainer-specific features
    initializeTrainingFocus();
    
    console.log('Trainer Events page initialized');
}

// Update calendar display
function updateCalendarDisplay(date, displayElement) {
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    displayElement.textContent = `${months[date.getMonth()]} ${date.getFullYear()}`;
}

// Show all events modal
function showAllEvents(type) {
    alert(`Showing all ${type} events - Feature coming soon!`);
    // TODO: Implement modal to show all events with trainer focus
}

// Initialize event interactions
function initializeEventInteractions() {
    const eventItems = document.querySelectorAll('.event-item');
    
    eventItems.forEach(item => {
        // Add hover effects
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
        
        // Click to view details
        item.addEventListener('click', function(e) {
            if (!e.target.closest('.event-actions')) {
                const eventId = this.dataset.eventId;
                if (eventId) {
                    viewEvent(eventId);
                }
            }
        });
        
        // Highlight training events for trainer
        if (item.querySelector('.event-type.training')) {
            item.classList.add('trainer-relevant');
        }
    });
}

// Initialize training-focused features
function initializeTrainingFocus() {
    // Add special highlighting for training events
    const trainingEvents = document.querySelectorAll('.event-type.training');
    
    trainingEvents.forEach(eventType => {
        const eventItem = eventType.closest('.event-item');
        if (eventItem) {
            eventItem.style.borderLeft = '4px solid #4ECDC4';
            eventItem.setAttribute('title', 'Training Event - Trainer Focus');
        }
    });
    
    // Add training statistics
    addTrainingStatistics();
}

// Add training-specific statistics
function addTrainingStatistics() {
    const trainingEvents = document.querySelectorAll('.event-type.training').length;
    const totalEvents = document.querySelectorAll('.event-item').length;
    
    console.log(`Training Events: ${trainingEvents}/${totalEvents}`);
    
    // Could add a trainer-specific stats widget here
}

// View event details (trainer has read-only access with training focus)
function viewEvent(eventId) {
    // Create modal for viewing event details
    const modal = createEventModal(eventId);
    document.body.appendChild(modal);
    modal.style.display = 'block';
}

// Create event details modal with trainer focus
function createEventModal(eventId) {
    const modal = document.createElement('div');
    modal.className = 'event-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-dumbbell"></i> Event Details - Trainer View</h3>
                <button class="modal-close" onclick="closeModal(this)">&times;</button>
            </div>
            <div class="modal-body">
                <div class="event-detail-card">
                    <h4>Event Information</h4>
                    <p><strong>Event ID:</strong> ${eventId}</p>
                    <p><strong>Status:</strong> Trainer View (Read Only)</p>
                    <p><i class="fas fa-info-circle"></i> Event details with training focus would be loaded here</p>
                </div>
                <div class="trainer-notes-card">
                    <h4><i class="fas fa-clipboard-list"></i> Training Notes</h4>
                    <p>Training-specific information and requirements for this event</p>
                    <ul>
                        <li>Fitness requirements</li>
                        <li>Equipment needed</li>
                        <li>Player preparation notes</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal(this)">Close</button>
            </div>
        </div>
    `;
    
    // Add modal styles
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: none;
    `;
    
    return modal;
}

// Close modal
function closeModal(element) {
    const modal = element.closest('.event-modal');
    if (modal) {
        modal.remove();
    }
}

// Sidebar functionality for trainer
function initializeSidebar() {
    const sidebar = document.getElementById('trainerSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');

    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '80px';
                sidebar.style.width = '80px';
            } else {
                mainContent.style.marginLeft = '280px';
                sidebar.style.width = '280px';
            }
        });
    }
}

// Export functions for global access
window.viewEvent = viewEvent;
window.closeModal = closeModal;