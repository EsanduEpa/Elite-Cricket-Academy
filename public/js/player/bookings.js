// Player Bookings JavaScript

// Global variables
let currentStep = 1;
let maxSteps = 4;
let selectedService = null;
let selectedDate = null;
let selectedTime = null;
let flatpickrInstance = null;

// Sample data for development
const sampleBookings = [
    {
        id: 1,
        type: 'Physio Session',
        date: '2024-12-15',
        time: '2:00 PM - 3:00 PM',
        practitioner: 'Dr. Sarah Wilson',
        reason: 'Injury Recovery Assessment',
        notes: 'Shoulder pain after bowling session',
        status: 'upcoming',
        urgency: 'normal'
    },
    {
        id: 2,
        type: 'Fitness Assessment',
        date: '2024-12-18',
        time: '10:00 AM - 11:30 AM',
        practitioner: 'Coach Mike Johnson',
        reason: 'Monthly Fitness Test',
        notes: 'Standard monthly fitness evaluation',
        status: 'upcoming',
        urgency: 'normal'
    },
    {
        id: 3,
        type: 'Physio Session',
        date: '2024-12-08',
        time: '3:00 PM - 4:00 PM',
        practitioner: 'Dr. Sarah Wilson',
        reason: 'Knee Injury Check',
        notes: 'Follow-up on knee injury',
        status: 'completed',
        sessionFeedback: 'Knee showing good recovery progress. Continue with recommended exercises.',
        urgency: 'normal'
    }
];

const serviceTypes = {
    physio: {
        name: 'Physio Session',
        duration: '60 minutes',
        icon: 'fas fa-heartbeat'
    },
    fitness: {
        name: 'Fitness Assessment',
        duration: '90 minutes',
        icon: 'fas fa-dumbbell'
    },
    consultation: {
        name: 'General Consultation',
        duration: '30 minutes',
        icon: 'fas fa-user-md'
    }
};

// Available time slots (sample data)
const availableSlots = {
    '2024-12-16': ['9:00 AM', '10:30 AM', '2:00 PM', '3:30 PM'],
    '2024-12-17': ['9:00 AM', '11:00 AM', '1:00 PM', '4:00 PM'],
    '2024-12-19': ['8:30 AM', '10:00 AM', '2:30 PM', '4:30 PM'],
    '2024-12-20': ['9:30 AM', '11:30 AM', '1:30 PM', '3:00 PM']
};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeBookings();
    setupEventListeners();
    initializeFlatpickr();
    setupFilters();
    setupViewToggle();
    initializeCalendar();
});

// Initialize bookings display
function initializeBookings() {
    renderBookings();
    updateBookingStats();
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchBookings');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }

    // Modal close listeners
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeBookingModal();
            closeDetailsModal();
        }
    });

    // Service selection
    document.addEventListener('click', function(e) {
        if (e.target.closest('.service-card')) {
            const serviceCard = e.target.closest('.service-card');
            selectService(serviceCard.dataset.service);
        }
    });

    // Time slot selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('time-slot') && !e.target.classList.contains('unavailable')) {
            selectTimeSlot(e.target);
        }
    });

    // Form submission
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', handleBookingSubmission);
    }
}

// Initialize Flatpickr date picker
function initializeFlatpickr() {
    const dateInput = document.getElementById('bookingDate');
    if (dateInput) {
        flatpickrInstance = flatpickr(dateInput, {
            minDate: 'today',
            maxDate: new Date().fp_incr(90), // 90 days from today
            dateFormat: 'Y-m-d',
            onChange: function(selectedDates, dateStr, instance) {
                selectedDate = dateStr;
                loadAvailableTimeSlots(dateStr);
                updateFormNavigation();
            },
            onOpen: function() {
                // Add custom styling if needed
            }
        });
    }
}

// Setup filters
function setupFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter bookings
            const filter = this.dataset.filter;
            filterBookings(filter);
        });
    });
}

// Setup view toggle
function setupViewToggle() {
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            toggleBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Switch view
            const view = this.dataset.view;
            switchView(view);
        });
    });
}

// Switch between list and calendar view
function switchView(view) {
    const listView = document.getElementById('listView');
    const calendarView = document.getElementById('calendarView');
    
    if (view === 'list') {
        listView.classList.add('active');
        calendarView.classList.remove('active');
    } else if (view === 'calendar') {
        listView.classList.remove('active');
        calendarView.classList.add('active');
        renderCalendar();
    }
}

// Render bookings
function renderBookings() {
    const upcomingContainer = document.getElementById('upcomingBookings');
    const pastContainer = document.getElementById('pastBookings');
    
    if (!upcomingContainer || !pastContainer) return;

    // Clear existing content
    upcomingContainer.innerHTML = '';
    pastContainer.innerHTML = '';

    // Separate bookings by status
    const upcomingBookings = sampleBookings.filter(booking => booking.status === 'upcoming');
    const pastBookings = sampleBookings.filter(booking => booking.status === 'completed' || booking.status === 'cancelled');

    // Render upcoming bookings
    if (upcomingBookings.length > 0) {
        upcomingBookings.forEach(booking => {
            upcomingContainer.appendChild(createBookingCard(booking));
        });
    } else {
        upcomingContainer.innerHTML = '<div class="empty-state"><i class="fas fa-calendar-plus"></i><h3>No Upcoming Bookings</h3><p>Book your next appointment to get started</p></div>';
    }

    // Render past bookings
    if (pastBookings.length > 0) {
        pastBookings.forEach(booking => {
            pastContainer.appendChild(createBookingCard(booking));
        });
    } else {
        pastContainer.innerHTML = '<div class="empty-state"><i class="fas fa-history"></i><h3>No Past Bookings</h3><p>Your booking history will appear here</p></div>';
    }
}

// Create booking card element
function createBookingCard(booking) {
    const card = document.createElement('div');
    card.className = `booking-card ${booking.status}`;
    card.dataset.status = booking.status;
    
    const statusIcon = {
        upcoming: 'fas fa-clock',
        completed: 'fas fa-check',
        cancelled: 'fas fa-times'
    };

    const statusText = {
        upcoming: 'Upcoming',
        completed: 'Completed',
        cancelled: 'Cancelled'
    };

    card.innerHTML = `
        <div class="booking-header">
            <div class="booking-type">
                <i class="${getServiceIcon(booking.type)}"></i>
                <span>${booking.type}</span>
            </div>
            <div class="booking-status ${booking.status}">
                <i class="${statusIcon[booking.status]}"></i> ${statusText[booking.status]}
            </div>
        </div>
        <div class="booking-details">
            <div class="booking-info">
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span>${formatDate(booking.date)}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>${booking.time}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-user-md"></i>
                    <span>${booking.practitioner}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-stethoscope"></i>
                    <span>${booking.reason}</span>
                </div>
            </div>
            ${booking.notes ? `
                <div class="booking-notes">
                    <strong>Notes:</strong> ${booking.notes}
                </div>
            ` : ''}
            ${booking.sessionFeedback ? `
                <div class="session-feedback">
                    <strong>Session Notes:</strong> ${booking.sessionFeedback}
                </div>
            ` : ''}
        </div>
        <div class="booking-actions">
            ${getBookingActions(booking)}
        </div>
    `;

    return card;
}

// Get service icon
function getServiceIcon(serviceType) {
    const icons = {
        'Physio Session': 'fas fa-heartbeat',
        'Fitness Assessment': 'fas fa-dumbbell',
        'General Consultation': 'fas fa-user-md'
    };
    return icons[serviceType] || 'fas fa-calendar-check';
}

// Get booking actions based on status
function getBookingActions(booking) {
    if (booking.status === 'upcoming') {
        return `
            <button class="action-btn reschedule" onclick="rescheduleBooking(${booking.id})">
                <i class="fas fa-calendar-alt"></i> Reschedule
            </button>
            <button class="action-btn cancel" onclick="cancelBooking(${booking.id})">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="action-btn details" onclick="viewBookingDetails(${booking.id})">
                <i class="fas fa-eye"></i> Details
            </button>
        `;
    } else if (booking.status === 'completed') {
        return `
            <button class="action-btn rebook" onclick="rebookSession(${booking.id})">
                <i class="fas fa-redo"></i> Book Again
            </button>
            <button class="action-btn details" onclick="viewBookingDetails(${booking.id})">
                <i class="fas fa-eye"></i> Details
            </button>
        `;
    } else {
        return `
            <button class="action-btn rebook" onclick="rebookSession(${booking.id})">
                <i class="fas fa-redo"></i> Book Again
            </button>
            <button class="action-btn details" onclick="viewBookingDetails(${booking.id})">
                <i class="fas fa-eye"></i> Details
            </button>
        `;
    }
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Filter bookings
function filterBookings(filter) {
    const bookingCards = document.querySelectorAll('.booking-card');
    
    bookingCards.forEach(card => {
        if (filter === 'all' || card.dataset.status === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Handle search
function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const bookingCards = document.querySelectorAll('.booking-card');
    
    bookingCards.forEach(card => {
        const content = card.textContent.toLowerCase();
        if (content.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Update booking stats
function updateBookingStats() {
    const upcomingCount = sampleBookings.filter(b => b.status === 'upcoming').length;
    const completedCount = sampleBookings.filter(b => b.status === 'completed').length;
    
    // Update badge in sidebar if exists
    const badge = document.querySelector('.nav-link .badge');
    if (badge && upcomingCount > 0) {
        badge.textContent = upcomingCount;
        badge.style.display = 'inline';
    } else if (badge) {
        badge.style.display = 'none';
    }
}

// Modal functions
function openBookingModal() {
    const modal = document.getElementById('bookingModal');
    if (modal) {
        modal.classList.add('active');
        resetBookingForm();
    }
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    if (modal) {
        modal.classList.remove('active');
        resetBookingForm();
    }
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Booking form functions
function resetBookingForm() {
    currentStep = 1;
    selectedService = null;
    selectedDate = null;
    selectedTime = null;
    
    // Reset form steps
    document.querySelectorAll('.form-step').forEach(step => step.classList.remove('active'));
    document.getElementById('step1').classList.add('active');
    
    // Reset service selection
    document.querySelectorAll('.service-card').forEach(card => card.classList.remove('selected'));
    
    // Reset time slot selection
    document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
    
    // Clear form fields
    const form = document.getElementById('bookingForm');
    if (form) {
        form.reset();
    }
    
    // Reset date picker
    if (flatpickrInstance) {
        flatpickrInstance.clear();
    }
    
    updateFormNavigation();
}

function selectService(serviceType) {
    selectedService = serviceType;
    
    // Update visual selection
    document.querySelectorAll('.service-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelector(`[data-service="${serviceType}"]`).classList.add('selected');
    
    updateFormNavigation();
}

function loadAvailableTimeSlots(date) {
    const timeSlotsContainer = document.getElementById('timeSlots');
    if (!timeSlotsContainer) return;
    
    timeSlotsContainer.innerHTML = '';
    
    const slots = availableSlots[date] || [];
    
    if (slots.length === 0) {
        timeSlotsContainer.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 20px;">No available slots for this date</p>';
        return;
    }
    
    slots.forEach(slot => {
        const slotElement = document.createElement('div');
        slotElement.className = 'time-slot';
        slotElement.textContent = slot;
        slotElement.dataset.time = slot;
        timeSlotsContainer.appendChild(slotElement);
    });
}

function selectTimeSlot(slotElement) {
    selectedTime = slotElement.dataset.time;
    
    // Update visual selection
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    slotElement.classList.add('selected');
    
    updateFormNavigation();
}

function nextStep() {
    if (currentStep < maxSteps && validateCurrentStep()) {
        currentStep++;
        showStep(currentStep);
        updateFormNavigation();
        
        if (currentStep === 4) {
            updateBookingSummary();
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        updateFormNavigation();
    }
}

function showStep(step) {
    document.querySelectorAll('.form-step').forEach(stepEl => stepEl.classList.remove('active'));
    document.getElementById(`step${step}`).classList.add('active');
}

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            return selectedService !== null;
        case 2:
            return selectedDate !== null && selectedTime !== null;
        case 3:
            const reason = document.getElementById('appointmentReason').value;
            return reason !== '';
        case 4:
            const terms = document.getElementById('agreeTerms').checked;
            return terms;
        default:
            return true;
    }
}

function updateFormNavigation() {
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const submitBtn = document.getElementById('submitBooking');
    
    // Show/hide previous button
    if (currentStep === 1) {
        prevBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'inline-flex';
    }
    
    // Show/hide next vs submit button
    if (currentStep === maxSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
        
        // Enable/disable next button based on validation
        if (validateCurrentStep()) {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
        } else {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '0.5';
        }
    }
}

function updateBookingSummary() {
    const serviceInfo = serviceTypes[selectedService];
    
    document.getElementById('summaryService').textContent = serviceInfo.name;
    document.getElementById('summaryDate').textContent = formatDate(selectedDate);
    document.getElementById('summaryTime').textContent = selectedTime;
    document.getElementById('summaryDuration').textContent = serviceInfo.duration;
    
    const reason = document.getElementById('appointmentReason').value;
    const reasonText = document.querySelector(`#appointmentReason option[value="${reason}"]`).textContent;
    document.getElementById('summaryReason').textContent = reasonText;
}

function handleBookingSubmission(e) {
    e.preventDefault();
    
    if (!validateCurrentStep()) {
        return;
    }
    
    // Collect form data
    const formData = {
        service: selectedService,
        date: selectedDate,
        time: selectedTime,
        reason: document.getElementById('appointmentReason').value,
        notes: document.getElementById('appointmentNotes').value,
        urgency: document.getElementById('urgencyLevel').value
    };
    
    // Simulate booking submission
    submitBooking(formData);
}

function submitBooking(formData) {
    // Show loading state
    const submitBtn = document.getElementById('submitBooking');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Create new booking
        const newBooking = {
            id: Date.now(),
            type: serviceTypes[formData.service].name,
            date: formData.date,
            time: formData.time,
            practitioner: 'Will be assigned',
            reason: document.querySelector(`#appointmentReason option[value="${formData.reason}"]`).textContent,
            notes: formData.notes,
            status: 'upcoming',
            urgency: formData.urgency
        };
        
        // Add to bookings
        sampleBookings.unshift(newBooking);
        
        // Refresh display
        renderBookings();
        updateBookingStats();
        
        // Show success message
        showSuccessMessage('Booking confirmed successfully! You will receive a confirmation email shortly.');
        
        // Close modal
        closeBookingModal();
        
        // Reset submit button
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Booking';
        submitBtn.disabled = false;
    }, 2000);
}

function showSuccessMessage(message) {
    // Create and show success message
    const messageEl = document.createElement('div');
    messageEl.className = 'message success';
    messageEl.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    
    // Insert at top of main content
    const mainContent = document.getElementById('mainContent');
    mainContent.insertBefore(messageEl, mainContent.firstChild);
    
    // Remove after 5 seconds
    setTimeout(() => {
        messageEl.remove();
    }, 5000);
}

// Booking action functions
function rescheduleBooking(bookingId) {
    // Find the booking
    const booking = sampleBookings.find(b => b.id === bookingId);
    if (!booking) return;
    
    // Open booking modal with pre-filled data
    openBookingModal();
    // You could pre-fill the form with existing booking data here
    
    console.log('Reschedule booking:', bookingId);
}

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        const bookingIndex = sampleBookings.findIndex(b => b.id === bookingId);
        if (bookingIndex !== -1) {
            sampleBookings[bookingIndex].status = 'cancelled';
            renderBookings();
            updateBookingStats();
            showSuccessMessage('Booking cancelled successfully.');
        }
    }
}

function viewBookingDetails(bookingId) {
    const booking = sampleBookings.find(b => b.id === bookingId);
    if (!booking) return;
    
    const detailsContent = document.getElementById('bookingDetailsContent');
    detailsContent.innerHTML = `
        <div class="booking-details-full">
            <div class="detail-section">
                <h3><i class="${getServiceIcon(booking.type)}"></i> ${booking.type}</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <strong>Date & Time:</strong>
                        <span>${formatDate(booking.date)} at ${booking.time}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Practitioner:</strong>
                        <span>${booking.practitioner}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Reason:</strong>
                        <span>${booking.reason}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Status:</strong>
                        <span class="status-badge ${booking.status}">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Urgency:</strong>
                        <span>${booking.urgency.charAt(0).toUpperCase() + booking.urgency.slice(1)}</span>
                    </div>
                </div>
                ${booking.notes ? `
                    <div class="detail-item full-width">
                        <strong>Notes:</strong>
                        <p>${booking.notes}</p>
                    </div>
                ` : ''}
                ${booking.sessionFeedback ? `
                    <div class="detail-item full-width">
                        <strong>Session Feedback:</strong>
                        <p>${booking.sessionFeedback}</p>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('detailsModal').classList.add('active');
}

function rebookSession(bookingId) {
    const booking = sampleBookings.find(b => b.id === bookingId);
    if (!booking) return;
    
    // Open booking modal and pre-select service
    openBookingModal();
    
    // Pre-select the same service type
    const serviceKey = Object.keys(serviceTypes).find(key => 
        serviceTypes[key].name === booking.type
    );
    if (serviceKey) {
        setTimeout(() => {
            selectService(serviceKey);
        }, 100);
    }
}

// Calendar functions
function initializeCalendar() {
    const calendarGrid = document.getElementById('calendarGrid');
    if (!calendarGrid) return;
    
    renderCalendar();
    
    // Setup calendar navigation
    document.getElementById('prevMonth')?.addEventListener('click', () => {
        // Previous month logic
        renderCalendar();
    });
    
    document.getElementById('nextMonth')?.addEventListener('click', () => {
        // Next month logic
        renderCalendar();
    });
}

function renderCalendar() {
    const calendarGrid = document.getElementById('calendarGrid');
    if (!calendarGrid) return;
    
    // Clear existing calendar
    calendarGrid.innerHTML = '';
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        dayHeader.style.cssText = 'background: var(--glass-bg-primary); font-weight: 600; padding: 10px; text-align: center; color: var(--text-primary);';
        calendarGrid.appendChild(dayHeader);
    });
    
    // Generate calendar days (simplified for demo)
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    
    // Add empty cells for days before month start
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'calendar-day other-month';
        calendarGrid.appendChild(emptyDay);
    }
    
    // Add days of current month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        if (day === today.getDate()) {
            dayElement.classList.add('today');
        }
        
        const dateString = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        // Check for bookings on this date
        const dayBookings = sampleBookings.filter(booking => booking.date === dateString);
        
        dayElement.innerHTML = `
            <div class="day-number">${day}</div>
            <div class="day-events">
                ${dayBookings.map(booking => 
                    `<div class="event-dot ${booking.status}" title="${booking.type}"></div>`
                ).join('')}
            </div>
        `;
        
        calendarGrid.appendChild(dayElement);
    }
}

// Show terms modal
function showTerms() {
    alert('Booking Terms and Conditions:\n\n1. Cancellations must be made at least 24 hours in advance\n2. Late arrivals may result in shortened sessions\n3. Reschedules are subject to availability\n4. Please bring any relevant medical documentation');
}

// Export functions for global access
window.openBookingModal = openBookingModal;
window.closeBookingModal = closeBookingModal;
window.closeDetailsModal = closeDetailsModal;
window.rescheduleBooking = rescheduleBooking;
window.cancelBooking = cancelBooking;
window.viewBookingDetails = viewBookingDetails;
window.rebookSession = rebookSession;
window.nextStep = nextStep;
window.previousStep = previousStep;
window.showTerms = showTerms;
