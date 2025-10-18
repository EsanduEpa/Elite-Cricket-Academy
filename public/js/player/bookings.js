// My Bookings JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeBookingsPage();
});

function initializeBookingsPage() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const bookingSections = document.querySelectorAll('.booking-section');
    const emptyState = document.getElementById('emptyState');

    // Tab switching
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active tab
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide sections
            bookingSections.forEach(section => {
                if (section.id === category) {
                    section.classList.add('active');
                    // Check if section has content
                    const bookingsGrid = section.querySelector('.bookings-grid');
                    if (!bookingsGrid || bookingsGrid.children.length === 0) {
                        emptyState.style.display = 'block';
                    } else {
                        emptyState.style.display = 'none';
                    }
                } else {
                    section.classList.remove('active');
                }
            });
        });
    });

    // Modal functionality
    const newBookingBtn = document.getElementById('newBookingBtn');
    const bookingModal = document.getElementById('newBookingModal');
    const modalClose = document.querySelector('.modal-close');
    const createFirstBookingBtn = document.getElementById('createFirstBooking');

    // Open modal
    function openBookingModal() {
        if (bookingModal) {
            bookingModal.style.display = 'flex';
            bookingModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    // Close modal
    function closeBookingModal() {
        if (bookingModal) {
            bookingModal.style.display = 'none';
            bookingModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if (newBookingBtn) {
        newBookingBtn.addEventListener('click', openBookingModal);
    }

    if (createFirstBookingBtn) {
        createFirstBookingBtn.addEventListener('click', openBookingModal);
    }

    if (modalClose) {
        modalClose.addEventListener('click', closeBookingModal);
    }

    // Close modal when clicking outside
    if (bookingModal) {
        bookingModal.addEventListener('click', function(e) {
            if (e.target === bookingModal) {
                closeBookingModal();
            }
        });
    }

    // Booking option selection
    const optionCards = document.querySelectorAll('.option-card');
    optionCards.forEach(card => {
        card.addEventListener('click', function() {
            const type = this.dataset.type;
            handleBookingTypeSelection(type);
        });
    });

    // Booking action handlers
    const bookingCards = document.querySelectorAll('.booking-card');
    bookingCards.forEach(card => {
        const buttons = card.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                handleBookingAction(this, card);
            });
        });
    });

    // Card click handlers
    bookingCards.forEach(card => {
        card.addEventListener('click', function() {
            showBookingDetails(this);
        });
    });

    // Initialize with upcoming bookings
    document.querySelector('[data-category="upcoming"]').click();
}

// Handle booking type selection
function handleBookingTypeSelection(type) {
    console.log(`Selected booking type: ${type}`);
    
    // Close modal and redirect to booking form based on type
    const bookingModal = document.getElementById('newBookingModal');
    if (bookingModal) {
        bookingModal.style.display = 'none';
        bookingModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Show booking form based on type
    switch(type) {
        case 'coach':
            showCoachBookingForm();
            break;
        case 'trainer':
            showTrainerBookingForm();
            break;
        case 'group':
            showGroupBookingForm();
            break;
    }
}

// Show coach booking form
function showCoachBookingForm() {
    const formModal = createBookingFormModal('coach', 'Coach Appointment', 'fa-user-tie');
    document.body.appendChild(formModal);
    formModal.classList.add('active');
}

// Show trainer booking form
function showTrainerBookingForm() {
    const formModal = createBookingFormModal('trainer', 'Trainer Session', 'fa-dumbbell');
    document.body.appendChild(formModal);
    formModal.classList.add('active');
}

// Show group booking form
function showGroupBookingForm() {
    const formModal = createBookingFormModal('group', 'Group Class', 'fa-users');
    document.body.appendChild(formModal);
    formModal.classList.add('active');
}

// Create booking form modal
function createBookingFormModal(type, title, icon) {
    const modal = document.createElement('div');
    modal.className = 'modal booking-form-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas ${icon}"></i> Book ${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form class="booking-form" data-type="${type}">
                    <div class="form-group">
                        <label>Select ${type === 'group' ? 'Class' : 'Instructor'}</label>
                        <select class="form-control" required>
                            <option value="">Choose...</option>
                            ${getInstructorOptions(type)}
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <select class="form-control" required>
                                <option value="">Select time...</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Session Type</label>
                        <select class="form-control" required>
                            <option value="">Choose session type...</option>
                            ${getSessionTypeOptions(type)}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Additional Notes (Optional)</label>
                        <textarea class="form-control" rows="3" placeholder="Any specific requirements or goals..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline cancel-booking">Cancel</button>
                        <button type="submit" class="btn btn-primary">Book Session</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    // Add form styles
    const formStyles = `
        .booking-form-modal .form-group {
            margin-bottom: 1.5rem;
        }
        .booking-form-modal label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .booking-form-modal .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(74, 144, 226, 0.2);
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .booking-form-modal .form-control:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        .booking-form-modal .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .booking-form-modal .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .booking-form-modal .cancel-booking {
            flex: 1;
        }
        .booking-form-modal .btn-primary {
            flex: 2;
        }
    `;

    const styleSheet = document.createElement('style');
    styleSheet.textContent = formStyles;
    document.head.appendChild(styleSheet);

    // Add event listeners
    const closeBtn = modal.querySelector('.modal-close');
    const cancelBtn = modal.querySelector('.cancel-booking');
    const form = modal.querySelector('.booking-form');

    closeBtn.addEventListener('click', () => {
        modal.classList.remove('active');
        setTimeout(() => {
            document.body.removeChild(modal);
            document.head.removeChild(styleSheet);
        }, 300);
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.remove('active');
        setTimeout(() => {
            document.body.removeChild(modal);
            document.head.removeChild(styleSheet);
        }, 300);
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        handleBookingSubmission(form, modal, styleSheet);
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
            setTimeout(() => {
                document.body.removeChild(modal);
                document.head.removeChild(styleSheet);
            }, 300);
        }
    });

    return modal;
}

// Get instructor options based on type
function getInstructorOptions(type) {
    switch(type) {
        case 'coach':
            return `
                <option value="anderson">Coach Anderson - Senior Cricket Coach</option>
                <option value="wilson">Coach Wilson - Bowling Specialist</option>
                <option value="roberts">Coach Roberts - Batting Expert</option>
            `;
        case 'trainer':
            return `
                <option value="mike">Mike Johnson - Fitness Trainer</option>
                <option value="sarah">Sarah Thompson - Strength Coach</option>
                <option value="david">David Miller - Conditioning Specialist</option>
            `;
        case 'group':
            return `
                <option value="batting-clinic">Batting Masterclass</option>
                <option value="bowling-clinic">Bowling Workshop</option>
                <option value="fielding-clinic">Fielding Excellence</option>
                <option value="match-practice">Practice Match</option>
            `;
    }
}

// Get session type options
function getSessionTypeOptions(type) {
    switch(type) {
        case 'coach':
            return `
                <option value="technique">Technique Review</option>
                <option value="performance">Performance Analysis</option>
                <option value="strategy">Strategy Discussion</option>
                <option value="general">General Coaching</option>
            `;
        case 'trainer':
            return `
                <option value="fitness">General Fitness</option>
                <option value="strength">Strength Training</option>
                <option value="conditioning">Conditioning</option>
                <option value="recovery">Recovery Session</option>
            `;
        case 'group':
            return `
                <option value="beginner">Beginner Level</option>
                <option value="intermediate">Intermediate Level</option>
                <option value="advanced">Advanced Level</option>
                <option value="all-levels">All Levels</option>
            `;
    }
}

// Handle booking submission
function handleBookingSubmission(form, modal, styleSheet) {
    const formData = new FormData(form);
    const bookingType = form.dataset.type;
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success message
        showSuccessMessage('Booking created successfully!');
        
        // Close modal
        modal.classList.remove('active');
        setTimeout(() => {
            document.body.removeChild(modal);
            document.head.removeChild(styleSheet);
        }, 300);
        
        // Refresh bookings (in real app, would reload data)
        setTimeout(() => {
            location.reload(); // Simple refresh for demo
        }, 1500);
        
    }, 2000);
}

// Handle booking actions
function handleBookingAction(button, card) {
    const action = button.textContent.trim();
    
    switch(action) {
        case 'Reschedule':
            handleReschedule(card);
            break;
        case 'Cancel':
            handleCancel(card);
            break;
        case 'Join Session':
        case 'Start Session':
            handleJoinSession(card);
            break;
        case 'View Details':
            showBookingDetails(card);
            break;
        case 'Rate Session':
            handleRateSession(card);
            break;
        case 'Book Again':
            handleBookAgain(card);
            break;
        case 'View Report':
            handleViewReport(card);
            break;
        case 'Rebook':
            handleRebook(card);
            break;
        case 'Refund Status':
            handleRefundStatus(card);
            break;
        case 'Confirmed':
            // Already confirmed, no action needed
            break;
    }
}

// Handle reschedule
function handleReschedule(card) {
    if (confirm('Would you like to reschedule this appointment?')) {
        console.log('Rescheduling appointment...');
        // Show reschedule form
    }
}

// Handle cancel
function handleCancel(card) {
    if (confirm('Are you sure you want to cancel this appointment? Cancellation fees may apply.')) {
        card.style.opacity = '0.5';
        card.style.transform = 'scale(0.95)';
        console.log('Appointment cancelled');
        
        // Update status
        const status = card.querySelector('.booking-status');
        status.className = 'booking-status cancelled';
        status.innerHTML = '<i class="fas fa-times-circle"></i><span>Cancelled</span>';
        
        setTimeout(() => {
            showSuccessMessage('Appointment cancelled successfully');
        }, 500);
    }
}

// Handle join session
function handleJoinSession(card) {
    console.log('Joining session...');
    showSuccessMessage('Redirecting to session...');
    // In real app, would redirect to video call or session page
}

// Handle rate session
function handleRateSession(card) {
    const ratingModal = createRatingModal();
    document.body.appendChild(ratingModal);
    ratingModal.classList.add('active');
}

// Create rating modal
function createRatingModal() {
    const modal = document.createElement('div');
    modal.className = 'modal rating-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-star"></i> Rate Your Session</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="rating-section">
                    <label>Overall Rating</label>
                    <div class="star-rating">
                        <i class="fas fa-star" data-rating="1"></i>
                        <i class="fas fa-star" data-rating="2"></i>
                        <i class="fas fa-star" data-rating="3"></i>
                        <i class="fas fa-star" data-rating="4"></i>
                        <i class="fas fa-star" data-rating="5"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Comments</label>
                    <textarea class="form-control" rows="4" placeholder="Share your experience..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline close-rating">Cancel</button>
                    <button type="button" class="btn btn-primary submit-rating">Submit Rating</button>
                </div>
            </div>
        </div>
    `;

    // Add rating functionality
    const stars = modal.querySelectorAll('.star-rating i');
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(stars, rating);
        });

        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            highlightStars(stars, selectedRating);
        });
    });

    modal.querySelector('.star-rating').addEventListener('mouseleave', function() {
        highlightStars(stars, selectedRating);
    });

    // Close handlers
    modal.querySelector('.modal-close').addEventListener('click', () => {
        modal.classList.remove('active');
        setTimeout(() => document.body.removeChild(modal), 300);
    });

    modal.querySelector('.close-rating').addEventListener('click', () => {
        modal.classList.remove('active');
        setTimeout(() => document.body.removeChild(modal), 300);
    });

    // Submit handler
    modal.querySelector('.submit-rating').addEventListener('click', () => {
        if (selectedRating === 0) {
            alert('Please select a rating');
            return;
        }
        
        showSuccessMessage('Thank you for your feedback!');
        modal.classList.remove('active');
        setTimeout(() => document.body.removeChild(modal), 300);
    });

    return modal;
}

// Highlight stars
function highlightStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.style.color = '#f39c12';
        } else {
            star.style.color = '#ddd';
        }
    });
}

// Show booking details
function showBookingDetails(card) {
    console.log('Showing booking details...');
    // Would show detailed view of the booking
}

// Show success message
function showSuccessMessage(message) {
    const successMsg = document.createElement('div');
    successMsg.className = 'success-message';
    successMsg.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    
    successMsg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(46, 204, 113, 0.3);
        z-index: 10001;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(successMsg);
    
    setTimeout(() => {
        successMsg.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        successMsg.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(successMsg)) {
                document.body.removeChild(successMsg);
            }
        }, 300);
    }, 3000);
}

// Other action handlers (simplified)
function handleBookAgain(card) { console.log('Booking again...'); }
function handleViewReport(card) { console.log('Viewing report...'); }
function handleRebook(card) { console.log('Rebooking...'); }
function handleRefundStatus(card) { console.log('Checking refund status...'); }

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (activeModal) {
            closeBookingModal();
        }
    }
});

// Global functions for HTML onclick events
window.openBookingModal = function() {
    const bookingModal = document.getElementById('bookingModal');
    bookingModal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

window.closeBookingModal = function() {
    const bookingModal = document.getElementById('bookingModal');
    bookingModal.classList.remove('active');
    document.body.style.overflow = '';
    // Reset form
    resetBookingForm();
};

function resetBookingForm() {
    // Reset form steps
    const steps = document.querySelectorAll('.form-step');
    steps.forEach((step, index) => {
        step.classList.toggle('active', index === 0);
    });
    
    // Clear selections
    document.querySelectorAll('.service-card.selected').forEach(card => {
        card.classList.remove('selected');
    });
    
    document.querySelectorAll('.time-slot.selected').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    // Reset form fields
    document.getElementById('bookingForm').reset();
    
    // Reset step navigation
    updateStepNavigation(1);
}

// Step navigation functions
let currentStep = 1;
const totalSteps = 4;

window.nextStep = function() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            updateStepNavigation(currentStep);
        }
    }
};

window.previousStep = function() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        updateStepNavigation(currentStep);
    }
};

function showStep(step) {
    const steps = document.querySelectorAll('.form-step');
    steps.forEach((stepEl, index) => {
        stepEl.classList.toggle('active', index === step - 1);
    });
}

function updateStepNavigation(step) {
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const submitBtn = document.getElementById('submitBooking');
    
    // Show/hide previous button
    prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
    
    // Show/hide next/submit buttons
    if (step === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }
}

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            // Check if a service is selected
            const selectedService = document.querySelector('.service-card.selected');
            if (!selectedService) {
                alert('Please select a service type.');
                return false;
            }
            break;
        case 2:
            // Check if date and time are selected
            const dateInput = document.getElementById('bookingDate');
            const selectedTimeSlot = document.querySelector('.time-slot.selected');
            if (!dateInput.value || !selectedTimeSlot) {
                alert('Please select a date and time slot.');
                return false;
            }
            break;
        case 3:
            // Check if reason is selected
            const reasonSelect = document.getElementById('appointmentReason');
            if (!reasonSelect.value) {
                alert('Please select a reason for your appointment.');
                return false;
            }
            break;
    }
    return true;
}

// Service card selection
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const serviceCards = document.querySelectorAll('.service-card');
        serviceCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selection from other cards
                serviceCards.forEach(c => c.classList.remove('selected'));
                // Add selection to clicked card
                this.classList.add('selected');
                
                // Update summary
                const serviceType = this.querySelector('h4').textContent;
                document.getElementById('summaryService').textContent = serviceType;
            });
        });
        
        // Time slot selection
        const timeSlots = document.querySelectorAll('.time-slot');
        timeSlots.forEach(slot => {
            slot.addEventListener('click', function() {
                if (!this.classList.contains('unavailable')) {
                    // Remove selection from other slots
                    timeSlots.forEach(s => s.classList.remove('selected'));
                    // Add selection to clicked slot
                    this.classList.add('selected');
                    
                    // Update summary
                    document.getElementById('summaryTime').textContent = this.textContent;
                }
            });
        });
        
        // Date input change
        const dateInput = document.getElementById('bookingDate');
        dateInput.addEventListener('change', function() {
            document.getElementById('summaryDate').textContent = this.value;
        });
    }, 500);
});

// Facility Booking Management Functions
function showFacilityBookingFromShopping() {
    // Navigate to shopping page and show facilities section
    window.location.href = '/Elite/player/shopping#facilities';
}

function viewFacilityBookingDetails(bookingId) {
    // Show facility booking details modal
    const facilityBookings = {
        'FB001': {
            id: 'FB001',
            facility: 'Main Cricket Ground',
            date: 'October 20, 2025',
            time: '14:00 - 16:00',
            duration: '2 hours',
            purpose: 'Team Practice Session',
            amount: 'LKR 5,000.00',
            status: 'Confirmed',
            amenities: ['Floodlights', 'Sound System', 'Scoreboard', 'Seating for 50'],
            bookingDate: 'October 15, 2025',
            paymentMethod: 'Credit Card',
            paymentStatus: 'Paid'
        },
        'FB002': {
            id: 'FB002',
            facility: 'Indoor Training Hall',
            date: 'October 25, 2025',
            time: '10:00 - 12:00',
            duration: '2 hours',
            purpose: 'Individual Training',
            amount: 'LKR 4,000.00',
            status: 'Pending',
            amenities: ['Air Conditioning', 'Equipment Storage', 'Changing Rooms'],
            bookingDate: 'October 18, 2025',
            paymentMethod: 'Bank Transfer',
            paymentStatus: 'Pending'
        },
        'FB003': {
            id: 'FB003',
            facility: 'Practice Ground A',
            date: 'October 15, 2025',
            time: '16:00 - 18:00',
            duration: '2 hours',
            purpose: 'Batting Practice',
            amount: 'LKR 3,000.00',
            status: 'Completed',
            amenities: ['Bowling Machine', 'Nets', 'Equipment Storage'],
            bookingDate: 'October 10, 2025',
            paymentMethod: 'Cash',
            paymentStatus: 'Paid'
        }
    };

    const booking = facilityBookings[bookingId];
    if (!booking) return;

    // Create and show modal
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content facility-booking-details">
            <div class="modal-header">
                <h3><i class="fas fa-building"></i> Facility Booking Details</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="booking-detail-grid">
                    <div class="detail-section">
                        <h4>Booking Information</h4>
                        <div class="detail-item">
                            <label>Booking ID:</label>
                            <span>${booking.id}</span>
                        </div>
                        <div class="detail-item">
                            <label>Facility:</label>
                            <span>${booking.facility}</span>
                        </div>
                        <div class="detail-item">
                            <label>Date & Time:</label>
                            <span>${booking.date} • ${booking.time}</span>
                        </div>
                        <div class="detail-item">
                            <label>Duration:</label>
                            <span>${booking.duration}</span>
                        </div>
                        <div class="detail-item">
                            <label>Purpose:</label>
                            <span>${booking.purpose}</span>
                        </div>
                        <div class="detail-item">
                            <label>Status:</label>
                            <span class="status-badge status-${booking.status.toLowerCase()}">${booking.status}</span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Payment Information</h4>
                        <div class="detail-item">
                            <label>Amount:</label>
                            <span class="amount">${booking.amount}</span>
                        </div>
                        <div class="detail-item">
                            <label>Payment Method:</label>
                            <span>${booking.paymentMethod}</span>
                        </div>
                        <div class="detail-item">
                            <label>Payment Status:</label>
                            <span class="status-badge status-${booking.paymentStatus.toLowerCase()}">${booking.paymentStatus}</span>
                        </div>
                        <div class="detail-item">
                            <label>Booking Date:</label>
                            <span>${booking.bookingDate}</span>
                        </div>
                    </div>
                </div>
                
                <div class="amenities-section">
                    <h4>Included Amenities</h4>
                    <div class="amenities-list">
                        ${booking.amenities.map(amenity => `
                            <div class="amenity-item">
                                <i class="fas fa-check"></i>
                                <span>${amenity}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="this.closest('.modal').remove()">
                    <i class="fas fa-times"></i> Close
                </button>
                ${booking.status !== 'Completed' ? `
                    <button class="btn btn-warning" onclick="modifyFacilityBooking('${booking.id}')">
                        <i class="fas fa-edit"></i> Modify Booking
                    </button>
                ` : ''}
                ${booking.status === 'Completed' ? `
                    <button class="btn btn-primary" onclick="rebookFacility('${booking.id}')">
                        <i class="fas fa-redo"></i> Book Again
                    </button>
                ` : ''}
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    modal.style.display = 'flex';
    
    // Add click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function cancelFacilityBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this facility booking? This action cannot be undone.')) {
        // Show loading state
        const card = document.querySelector(`[onclick*="${bookingId}"]`).closest('.facility-booking-card');
        card.style.opacity = '0.5';
        
        // Simulate API call
        setTimeout(() => {
            showSuccessMessage('Facility booking cancelled successfully');
            // Update card to show cancelled status
            const statusElement = card.querySelector('.booking-status');
            statusElement.textContent = 'Cancelled';
            statusElement.className = 'booking-status status-cancelled';
            
            // Update actions
            const actions = card.querySelector('.booking-actions');
            actions.innerHTML = `
                <button class="btn btn-sm btn-secondary" onclick="viewFacilityBookingDetails('${bookingId}')">
                    <i class="fas fa-eye"></i> Details
                </button>
                <button class="btn btn-sm btn-primary" onclick="rebookFacility('${bookingId}')">
                    <i class="fas fa-redo"></i> Rebook
                </button>
            `;
            
            card.style.opacity = '1';
        }, 1500);
    }
}

function modifyFacilityBooking(bookingId) {
    // Navigate to shopping page facilities section with modification parameters
    window.location.href = `/Elite/player/shopping#facilities?modify=${bookingId}`;
}

function rebookFacility(bookingId) {
    // Navigate to shopping page facilities section with rebook parameters
    window.location.href = `/Elite/player/shopping#facilities?rebook=${bookingId}`;
}

function showSuccessMessage(message) {
    // Create success toast
    const toast = document.createElement('div');
    toast.className = 'success-toast';
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show with animation
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Hide and remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Filter Functions for Booking Tables

function filterUpcomingBookings() {
    const filterValue = document.getElementById('upcomingSessionFilter').value;
    const table = document.getElementById('upcomingBookingsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const sessionBadge = row.querySelector('.table-badge:not(.status-confirmed):not(.status-pending):not(.status-completed)');
        
        if (filterValue === 'all' || (sessionBadge && sessionBadge.textContent.trim() === filterValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function filterFacilityBookings() {
    const filterValue = document.getElementById('facilityTypeFilter').value;
    const table = document.getElementById('facilityBookingsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const facilityBadge = row.querySelector('.table-badge:not(.status-confirmed):not(.status-pending):not(.status-completed)');
        
        if (filterValue === 'all' || (facilityBadge && facilityBadge.textContent.trim() === filterValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function filterRecentBookings() {
    const filterValue = document.getElementById('recentSessionFilter').value;
    const table = document.getElementById('recentBookingsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const sessionBadge = row.querySelector('.table-badge:not(.status-confirmed):not(.status-pending):not(.status-completed)');
        
        if (filterValue === 'all' || (sessionBadge && sessionBadge.textContent.trim() === filterValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}