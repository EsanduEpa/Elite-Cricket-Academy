// Events API Functions for Admin Dashboard
// Handles CRUD operations with live updates (NO external APIs - using PHP session data)

// Global calendar reference (not used after calendar removal, kept for compatibility)
let globalCalendar = null;

// Store events data loaded from PHP
let eventsDataCache = {};

// Edit Event Function - Redirects to dedicated edit page
function editEvent(eventId) {
    console.log('Editing event:', eventId);
    
    // Show loading
    showLoading();
    
    // Redirect to dedicated edit page
    // The PHP controller will load event data and display the edit form
    window.location.href = `${window.location.origin}/Elite/admin/edit_event/${eventId}`;
}

// Populate wizard with event data for editing
function populateWizardForEdit(event) {
    const modal = document.getElementById('createEventModal');
    const form = document.getElementById('eventWizardForm');
    const modalTitle = modal.querySelector('.modal-title');
    
    if (!modal || !form) {
        console.error('Modal or form not found');
        return;
    }
    
    // Change modal title
    modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Event';
    
    // Change form action to edit endpoint
    form.action = `${window.location.origin}/Elite/admin/edit_event/${event.id}`;
    
    // Add hidden input for event ID if it doesn't exist
    let eventIdInput = form.querySelector('input[name="event_id"]');
    if (!eventIdInput) {
        eventIdInput = document.createElement('input');
        eventIdInput.type = 'hidden';
        eventIdInput.name = 'event_id';
        form.appendChild(eventIdInput);
    }
    eventIdInput.value = event.id;
    
    // Extract date and time from datetime fields
    const startDateTime = new Date(event.StartDate);
    const endDateTime = new Date(event.EndDate);
    
    // Format dates as YYYY-MM-DD
    const startDate = startDateTime.toISOString().split('T')[0];
    const endDate = endDateTime.toISOString().split('T')[0];
    
    // Format times as HH:MM
    const startTime = startDateTime.toTimeString().slice(0, 5);
    const endTime = endDateTime.toTimeString().slice(0, 5);
    
    // Populate form fields
    const fieldMappings = {
        'eventName': event.Name,
        'eventType': event.Type,
        'eventCategory': event.Category,
        'eventVenue': event.Location,
        'maxParticipants': event.MaxParticipants || '',
        'registrationFee': event.RegistrationFee || '',
        'eventDescription': event.Description || '',
        'startDate': startDate,
        'startTime': startTime,
        'endDate': endDate,
        'endTime': endTime,
        'registrationStart': event.RegistrationStart ? event.RegistrationStart.replace(' ', 'T') : '',
        'registrationEnd': event.RegistrationEnd ? event.RegistrationEnd.replace(' ', 'T') : '',
        'eventStatus': event.Status || 'upcoming',
        'primaryContact': event.PrimaryContact,
        'contactEmail': event.ContactEmail,
        'contactPhone': event.ContactPhone
    };
    
    // Set field values
    Object.entries(fieldMappings).forEach(([fieldId, value]) => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value;
        } else {
            console.warn(`Field ${fieldId} not found`);
        }
    });
    
    // Update confirmation step with current values
    updateConfirmationStep();
    
    // Open modal
    modal.style.display = 'flex';
    
    // Reset to first step
    resetWizardToStep(1);
}

// Delete Event Function with AJAX
function deleteEvent(eventId) {
    if (!confirm('⚠️ Are you sure you want to delete this event?\n\nThis action cannot be undone.')) {
        return;
    }
    
    console.log('Deleting event:', eventId);
    showLoading();
    
    // Use fetch to delete event
    fetch(`${window.location.origin}/Elite/admin/delete_event/${eventId}`, {
        method: 'GET', // Using GET as the controller expects it
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        hideLoading();
        
        if (response.redirected) {
            // Controller redirected, follow it
            window.location.href = response.url;
        } else {
            // Refresh the page to show updated data
            refreshEventsTable();
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error deleting event:', error);
        alert('❌ Failed to delete event. Please try again.');
    });
}

// View Event Details
function viewEvent(eventId) {
    console.log('Viewing event:', eventId);
    
    // Fetch event details
    fetch(`${window.location.origin}/Elite/admin/get_event/${eventId}`)
        .then(response => response.json())
        .then(event => {
            showEventDetailsModal(event);
        })
        .catch(error => {
            console.error('Error fetching event:', error);
            alert('Failed to load event details');
        });
}

// Show event details in a modal
function showEventDetailsModal(event) {
    const modal = document.createElement('div');
    modal.className = 'event-details-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-alt"></i> ${event.Name}</h2>
                <button class="close-btn" onclick="this.closest('.event-details-modal').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Type:</span>
                        <span class="value">${event.Type}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Category:</span>
                        <span class="value">${event.Category}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Location:</span>
                        <span class="value">${event.Location}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Start Date:</span>
                        <span class="value">${new Date(event.StartDate).toLocaleString()}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">End Date:</span>
                        <span class="value">${new Date(event.EndDate).toLocaleString()}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Status:</span>
                        <span class="value status-${event.Status}">${event.Status}</span>
                    </div>
                    ${event.Description ? `
                    <div class="detail-item full-width">
                        <span class="label">Description:</span>
                        <span class="value">${event.Description}</span>
                    </div>
                    ` : ''}
                    <div class="detail-item">
                        <span class="label">Max Participants:</span>
                        <span class="value">${event.MaxParticipants || 'Unlimited'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Registration Fee:</span>
                        <span class="value">LKR ${event.RegistrationFee || '0.00'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Contact Person:</span>
                        <span class="value">${event.PrimaryContact}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Email:</span>
                        <span class="value">${event.ContactEmail}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Phone:</span>
                        <span class="value">${event.ContactPhone}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="editEvent(${event.EventID}); this.closest('.event-details-modal').remove();">
                    <i class="fas fa-edit"></i> Edit Event
                </button>
                <button class="btn btn-secondary" onclick="this.closest('.event-details-modal').remove()">
                    Close
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Helper function to show loading indicator
function showLoading() {
    let loader = document.getElementById('globalLoader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'globalLoader';
        loader.className = 'global-loader';
        loader.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(loader);
    }
    loader.style.display = 'flex';
}

// Helper function to hide loading indicator
function hideLoading() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.style.display = 'none';
    }
}

console.log('Events API functions loaded');
