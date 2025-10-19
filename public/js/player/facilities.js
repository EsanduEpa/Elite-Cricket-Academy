// Facilities Page JavaScript - Facility Booking Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeFacilitiesPage();
});

function initializeFacilitiesPage() {
    console.log('=== INITIALIZING FACILITIES PAGE ===');
    
    initFacilityFiltering();
    initFacilityModals();
    initFacilityBookings();
    setMinimumDates();
    
    console.log('Facilities page initialization complete');
}

// Facility Filtering Functions
function filterFacilitiesByType(type) {
    console.log('Filtering facilities by type:', type);
    
    const facilitiesGrid = document.getElementById('facilities-grid');
    if (!facilitiesGrid) {
        console.log('Facilities grid not found');
        return;
    }
    
    const facilityCards = facilitiesGrid.querySelectorAll('.product-card');
    
    facilityCards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        const cardCapacity = card.getAttribute('data-capacity');
        
        let shouldShow = false;
        
        if (type === 'all') {
            shouldShow = true;
        } else if (type === 'indoor' || type === 'outdoor' || type === 'training') {
            shouldShow = cardType === type;
        } else if (type === 'small' || type === 'medium' || type === 'large') {
            shouldShow = cardCapacity === type;
        }
        
        if (shouldShow) {
            card.style.display = 'block';
            // Add smooth animation
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, 100);
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update navigation buttons
    updateFacilityNavButtons(type);
    
    // Update results count
    updateFacilityResultsCount(type);
}

function updateFacilityNavButtons(activeType) {
    const navBtns = document.querySelectorAll('.facility-nav-btn, .shop-navigation .nav-btn');
    navBtns.forEach(btn => {
        btn.classList.remove('active');
        
        // Check if this button corresponds to the active type
        const btnType = btn.getAttribute('onclick')?.match(/filterFacilitiesByType\('([^']+)'\)/)?.[1];
        if (btnType === activeType) {
            btn.classList.add('active');
        }
    });
}

function updateFacilityResultsCount(type) {
    const facilitiesGrid = document.getElementById('facilities-grid');
    if (!facilitiesGrid) return;
    
    const visibleCards = facilitiesGrid.querySelectorAll('.product-card[style*="display: block"], .product-card:not([style*="display: none"])');
    const count = visibleCards.length;
    
    // Update or create results indicator
    let resultsIndicator = document.getElementById('facility-results-count');
    if (!resultsIndicator) {
        resultsIndicator = document.createElement('div');
        resultsIndicator.id = 'facility-results-count';
        resultsIndicator.style.cssText = `
            text-align: center;
            margin: 1rem 0;
            color: #7f8c8d;
            font-weight: 500;
        `;
        facilitiesGrid.parentNode.insertBefore(resultsIndicator, facilitiesGrid);
    }
    
    const typeText = type === 'all' ? 'All Facilities' : 
                    type === 'indoor' ? 'Indoor Facilities' :
                    type === 'outdoor' ? 'Outdoor Facilities' :
                    type === 'training' ? 'Training Facilities' :
                    type.charAt(0).toUpperCase() + type.slice(1) + ' Capacity';
    resultsIndicator.textContent = `${count} ${typeText} available for booking`;
}

// Advanced Filtering
function initAdvancedFiltering() {
    const typeFilter = document.getElementById('facility-type-filter');
    const capacityFilter = document.getElementById('facility-capacity-filter');
    
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            const selectedType = this.value;
            filterFacilitiesByType(selectedType);
        });
    }
    
    if (capacityFilter) {
        capacityFilter.addEventListener('change', function() {
            const selectedCapacity = this.value;
            filterFacilitiesByCapacity(selectedCapacity);
        });
    }
}

function filterFacilitiesByCapacity(capacity) {
    const facilitiesGrid = document.getElementById('facilities-grid');
    if (!facilitiesGrid) return;
    
    const facilityCards = facilitiesGrid.querySelectorAll('.product-card');
    
    facilityCards.forEach(card => {
        const cardCapacity = card.getAttribute('data-capacity');
        
        if (capacity === 'all' || cardCapacity === capacity) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    updateFacilityResultsCount(capacity);
}

// Facility Modal Functions
function initFacilityModals() {
    // Add event listeners for booking buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('book-facility')) {
            const facilityData = e.target.dataset;
            openFacilityModal(facilityData);
        }
        
        if (e.target.classList.contains('facility-close-btn') || e.target.classList.contains('close-btn')) {
            closeFacilityModal();
        }
        
        if (e.target.id === 'facilityModal') {
            closeFacilityModal();
        }
    });
}

function openFacilityModal(facilityData) {
    let modal = document.getElementById('facilityModal');
    if (!modal) {
        createFacilityModal();
        modal = document.getElementById('facilityModal');
    }
    
    // Populate facility details
    populateFacilityDetails(facilityData);
    
    // Set up price calculation
    setupFacilityPriceCalculation(facilityData);
    
    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Add animation
    const modalContent = modal.querySelector('.facility-modal-content');
    modalContent.style.transform = 'scale(0.7)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
}

function closeFacilityModal() {
    const modal = document.getElementById('facilityModal');
    if (!modal) return;
    
    const modalContent = modal.querySelector('.facility-modal-content');
    modalContent.style.transform = 'scale(0.7)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 200);
}

function createFacilityModal() {
    const modal = document.createElement('div');
    modal.id = 'facilityModal';
    modal.className = 'facility-modal';
    modal.style.display = 'none';
    
    modal.innerHTML = `
        <div class="facility-modal-content" style="transition: all 0.2s ease;">
            <div class="facility-modal-header">
                <h3>Facility Booking</h3>
                <button class="facility-close-btn">&times;</button>
            </div>
            <div class="facility-modal-body">
                <div id="facility-details"></div>
                <form id="facility-form">
                    <div class="facility-form-group">
                        <label for="booking-date">Date:</label>
                        <input type="date" id="booking-date" required>
                    </div>
                    <div class="facility-form-group">
                        <label for="booking-time">Start Time:</label>
                        <select id="booking-time" required>
                            <option value="06:00">6:00 AM</option>
                            <option value="07:00">7:00 AM</option>
                            <option value="08:00">8:00 AM</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="17:00">5:00 PM</option>
                            <option value="18:00">6:00 PM</option>
                            <option value="19:00">7:00 PM</option>
                            <option value="20:00">8:00 PM</option>
                        </select>
                    </div>
                    <div class="facility-form-group">
                        <label for="booking-duration">Duration:</label>
                        <select id="booking-duration" required>
                            <option value="1">1 hour</option>
                            <option value="2">2 hours</option>
                            <option value="3">3 hours</option>
                            <option value="4">4 hours (Half Day)</option>
                            <option value="8">8 hours (Full Day)</option>
                        </select>
                    </div>
                    <div class="facility-form-group">
                        <label for="booking-purpose">Purpose:</label>
                        <select id="booking-purpose" required>
                            <option value="">Select purpose...</option>
                            <option value="training">Training Session</option>
                            <option value="practice">Practice Match</option>
                            <option value="meeting">Team Meeting</option>
                            <option value="event">Special Event</option>
                            <option value="fitness">Fitness Training</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="facility-form-group">
                        <label for="booking-participants">Expected Participants:</label>
                        <input type="number" id="booking-participants" min="1" max="50" value="1" required>
                    </div>
                </form>
                <div class="booking-total">
                    <strong>Total: $<span id="booking-total">0.00</span></strong>
                </div>
            </div>
            <div class="facility-modal-actions">
                <button class="btn-secondary" onclick="closeFacilityModal()">Cancel</button>
                <button class="btn-primary" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function populateFacilityDetails(facilityData) {
    const detailsDiv = document.getElementById('facility-details');
    if (!detailsDiv) return;
    
    detailsDiv.innerHTML = `
        <div class="booking-facility-info">
            <h4>${facilityData.name}</h4>
            <p><strong>Capacity:</strong> ${facilityData.capacity || 'Contact for details'}</p>
            ${facilityData.hourly ? `<p><strong>Hourly Rate:</strong> $${facilityData.hourly}</p>` : ''}
            ${facilityData.half ? `<p><strong>Half Day Rate:</strong> $${facilityData.half}</p>` : ''}
            ${facilityData.full ? `<p><strong>Full Day Rate:</strong> $${facilityData.full}</p>` : ''}
            <p><strong>Available:</strong> 6:00 AM - 10:00 PM daily</p>
        </div>
    `;
}

function setupFacilityPriceCalculation(facilityData) {
    const durationSelect = document.getElementById('booking-duration');
    const totalSpan = document.getElementById('booking-total');
    
    if (!durationSelect || !totalSpan) return;
    
    function updateTotal() {
        updateBookingTotal(facilityData);
    }
    
    durationSelect.addEventListener('change', updateTotal);
    
    // Initial calculation
    updateTotal();
}

function updateBookingTotal(facilityData) {
    const duration = parseInt(document.getElementById('booking-duration').value);
    const totalSpan = document.getElementById('booking-total');
    
    let total = 0;
    
    if (facilityData.hourly) {
        if (duration === 4 && facilityData.half) {
            total = parseFloat(facilityData.half);
        } else if (duration === 8 && facilityData.full) {
            total = parseFloat(facilityData.full);
        } else {
            total = parseFloat(facilityData.hourly) * duration;
        }
    }
    
    // Apply discounts for longer bookings
    if (duration >= 8) {
        total *= 0.9; // 10% discount for full day
    } else if (duration >= 4) {
        total *= 0.95; // 5% discount for half day
    }
    
    totalSpan.textContent = total.toFixed(2);
}

function confirmBooking() {
    const form = document.getElementById('facility-form');
    if (!form) return;
    
    // Validate form
    if (!form.checkValidity()) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Get form data
    const date = document.getElementById('booking-date').value;
    const time = document.getElementById('booking-time').value;
    const duration = document.getElementById('booking-duration').value;
    const purpose = document.getElementById('booking-purpose').value;
    const participants = document.getElementById('booking-participants').value;
    const total = document.getElementById('booking-total').textContent;
    
    // Show confirmation
    showBookingConfirmation({
        date,
        time,
        duration,
        purpose,
        participants,
        total
    });
    
    closeFacilityModal();
}

function showBookingConfirmation(bookingDetails) {
    const timeFormatted = formatTime(bookingDetails.time);
    const durationText = bookingDetails.duration === '1' ? '1 hour' :
                       bookingDetails.duration === '4' ? '4 hours (Half Day)' :
                       bookingDetails.duration === '8' ? '8 hours (Full Day)' :
                       `${bookingDetails.duration} hours`;
    
    alert(`Facility Booking Confirmed!
    
Date: ${bookingDetails.date}
Time: ${timeFormatted}
Duration: ${durationText}
Purpose: ${bookingDetails.purpose}
Participants: ${bookingDetails.participants}
Total: $${bookingDetails.total}

You will receive a confirmation email with access details and facility guidelines.`);
}

function formatTime(time24) {
    const [hours, minutes] = time24.split(':');
    const hour12 = hours % 12 || 12;
    const ampm = hours >= 12 ? 'PM' : 'AM';
    return `${hour12}:${minutes} ${ampm}`;
}

// Facility Booking Calendar Integration
function initFacilityBookings() {
    // Set up availability checking
    const dateInput = document.getElementById('booking-date');
    const timeSelect = document.getElementById('booking-time');
    
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            checkFacilityAvailability(this.value);
        });
    }
}

function checkFacilityAvailability(date) {
    // Mock availability check - in real implementation, this would call the server
    const timeSelect = document.getElementById('booking-time');
    if (!timeSelect) return;
    
    const unavailableTimes = ['09:00', '14:00', '18:00']; // Mock unavailable times
    
    Array.from(timeSelect.options).forEach(option => {
        if (unavailableTimes.includes(option.value)) {
            option.disabled = true;
            option.text = option.text + ' (Unavailable)';
        } else {
            option.disabled = false;
            option.text = option.text.replace(' (Unavailable)', '');
        }
    });
}

// Utility Functions
function setMinimumDates() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        input.min = today;
        
        // Set default to tomorrow if today is weekend
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        if (tomorrow.getDay() === 0 || tomorrow.getDay() === 6) {
            // Skip to next weekday
            while (tomorrow.getDay() === 0 || tomorrow.getDay() === 6) {
                tomorrow.setDate(tomorrow.getDate() + 1);
            }
        }
        
        input.value = tomorrow.toISOString().split('T')[0];
    });
}

function initFacilityFiltering() {
    // Initialize with all facilities showing
    setTimeout(() => {
        filterFacilitiesByType('all');
        initAdvancedFiltering();
    }, 100);
}

// Search functionality
function initFacilitySearch() {
    const searchInput = document.getElementById('facility-search');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const facilityCards = document.querySelectorAll('.product-card');
        
        facilityCards.forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Facility availability calendar (future enhancement)
function showFacilityCalendar(facilityId) {
    // This would show a calendar view of facility availability
    console.log('Showing calendar for facility:', facilityId);
    // Implementation would go here
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .product-card {
        transition: opacity 0.3s ease, transform 0.3s ease;
        animation: fadeIn 0.5s ease;
    }
    
    .facility-modal-content {
        transition: all 0.2s ease;
    }
    
    select option:disabled {
        color: #ccc;
        background-color: #f5f5f5;
    }
`;
document.head.appendChild(style);