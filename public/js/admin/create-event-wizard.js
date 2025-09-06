// Event Creation Wizard JavaScript
class EventWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.formData = {};
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateProgress();
        this.setMinDates();
    }

    bindEvents() {
        // Wizard navigation
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('eventWizardForm');

        if (nextBtn) nextBtn.addEventListener('click', () => this.nextStep());
        if (prevBtn) prevBtn.addEventListener('click', () => this.prevStep());
        if (form) form.addEventListener('submit', (e) => this.submitForm(e));
        
        // Real-time validation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearError(input));
        });
    }

    setMinDates() {
        const today = new Date().toISOString().split('T')[0];
        const now = new Date().toISOString().slice(0, 16);
        
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        const regStart = document.getElementById('registrationStart');
        const regEnd = document.getElementById('registrationEnd');
        
        if (startDate) startDate.min = today;
        if (endDate) endDate.min = today;
        if (regStart) regStart.min = now;
        if (regEnd) regEnd.min = now;
    }

    nextStep() {
        if (this.validateCurrentStep()) {
            this.saveCurrentStepData();
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
                this.updateProgress();
                if (this.currentStep === 4) {
                    this.populateSummary();
                }
            }
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
            this.updateProgress();
        }
    }

    showStep(step) {
        console.log('Showing step:', step);
        
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
            console.log('Removing active from step:', content.getAttribute('data-step'));
        });
        
        // Show current step
        const currentStepElement = document.querySelector(`.step-content[data-step="${step}"]`);
        console.log('Current step element:', currentStepElement);
        
        if (currentStepElement) {
            currentStepElement.classList.add('active');
            console.log('Added active to step:', step);
            
            // Force display check
            setTimeout(() => {
                const computedStyle = window.getComputedStyle(currentStepElement);
                console.log('Step display:', computedStyle.display);
                console.log('Step opacity:', computedStyle.opacity);
            }, 50);
        } else {
            console.error('Step element not found for step:', step);
        }
        
        // Update navigation buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (prevBtn) prevBtn.style.display = step === 1 ? 'none' : 'flex';
        if (nextBtn) nextBtn.style.display = step === this.totalSteps ? 'none' : 'flex';
        if (submitBtn) submitBtn.style.display = step === this.totalSteps ? 'flex' : 'none';
    }

    updateProgress() {
        const progressLine = document.getElementById('progressLine');
        const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        if (progressLine) {
            progressLine.style.width = progress + '%';
        }

        // Update step indicators
        document.querySelectorAll('.step-circle').forEach((circle, index) => {
            const stepNum = index + 1;
            circle.classList.remove('active', 'completed');
            
            if (stepNum < this.currentStep) {
                circle.classList.add('completed');
                circle.innerHTML = '<i class="fas fa-check"></i>';
            } else if (stepNum === this.currentStep) {
                circle.classList.add('active');
                circle.innerHTML = stepNum;
            } else {
                circle.innerHTML = stepNum;
            }
        });

        // Update step labels
        document.querySelectorAll('.step-label').forEach((label, index) => {
            const stepNum = index + 1;
            label.classList.remove('active', 'completed');
            
            if (stepNum < this.currentStep) {
                label.classList.add('completed');
            } else if (stepNum === this.currentStep) {
                label.classList.add('active');
            }
        });
    }

    validateCurrentStep() {
        const currentStepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        if (!currentStepElement) return false;
        
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        // Additional validations
        if (this.currentStep === 2) {
            if (!this.validateDates()) {
                isValid = false;
            }
        }

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const errorElement = document.getElementById(field.id + 'Error');
        
        // Clear previous errors
        field.classList.remove('error', 'success');
        if (errorElement) errorElement.classList.remove('show');

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            this.showError(field, errorElement, 'This field is required');
            return false;
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showError(field, errorElement, 'Please enter a valid email address');
                return false;
            }
        }

        // Phone validation
        if (field.type === 'tel' && value) {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]+$/;
            if (!phoneRegex.test(value) || value.length < 10) {
                this.showError(field, errorElement, 'Please enter a valid phone number');
                return false;
            }
        }

        // Number validation
        if (field.type === 'number' && value) {
            if (isNaN(value) || value < 0) {
                this.showError(field, errorElement, 'Please enter a valid number');
                return false;
            }
        }

        // Success state
        if (value) {
            field.classList.add('success');
        }

        return true;
    }

    validateDates() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (startDate && endDate) {
            const start = new Date(startDate + ' ' + (startTime || '00:00'));
            const end = new Date(endDate + ' ' + (endTime || '23:59'));

            if (end <= start) {
                this.showError(document.getElementById('endDate'), 
                              document.getElementById('endDateError'), 
                              'End date must be after start date');
                return false;
            }
        }

        return true;
    }

    showError(field, errorElement, message) {
        field.classList.add('error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    clearError(field) {
        field.classList.remove('error');
        const errorElement = document.getElementById(field.id + 'Error');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
    }

    saveCurrentStepData() {
        const currentStepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        if (!currentStepElement) return;
        
        const inputs = currentStepElement.querySelectorAll('.form-control');
        
        inputs.forEach(input => {
            this.formData[input.name] = input.value;
        });
    }

    populateSummary() {
        // Basic Details
        this.updateSummaryElement('summaryEventName', 'eventName');
        this.updateSummaryElement('summaryEventType', 'eventType', true);
        this.updateSummaryElement('summaryEventCategory', 'eventCategory', true);
        this.updateSummaryElement('summaryEventVenue', 'eventVenue');
        
        const maxParticipants = document.getElementById('maxParticipants').value;
        document.getElementById('summaryMaxParticipants').textContent = maxParticipants || 'Unlimited';
        
        const regFee = document.getElementById('registrationFee').value;
        document.getElementById('summaryRegistrationFee').textContent = regFee ? 
            'LKR ' + regFee : 'Free';

        // Date & Time
        const startDate = document.getElementById('startDate').value;
        const startTime = document.getElementById('startTime').value;
        const endDate = document.getElementById('endDate').value;
        const endTime = document.getElementById('endTime').value;
        
        document.getElementById('summaryStartDateTime').textContent = 
            startDate && startTime ? this.formatDateTime(startDate, startTime) : '-';
        document.getElementById('summaryEndDateTime').textContent = 
            endDate && endTime ? this.formatDateTime(endDate, endTime) : '-';
        
        const regStart = document.getElementById('registrationStart').value;
        const regEnd = document.getElementById('registrationEnd').value;
        document.getElementById('summaryRegistrationStart').textContent = 
            regStart ? this.formatDateTime(regStart.split('T')[0], regStart.split('T')[1]) : '-';
        document.getElementById('summaryRegistrationEnd').textContent = 
            regEnd ? this.formatDateTime(regEnd.split('T')[0], regEnd.split('T')[1]) : '-';
        
        this.updateSummaryElement('summaryEventStatus', 'eventStatus', true);

        // Contact Details
        this.updateSummaryElement('summaryPrimaryContact', 'primaryContact');
        this.updateSummaryElement('summaryContactEmail', 'contactEmail');
        this.updateSummaryElement('summaryContactPhone', 'contactPhone');
        this.updateSummaryElement('summaryEventCoordinator', 'eventCoordinator', true);

        // Description
        this.updateSummaryElement('summaryEventDescription', 'eventDescription');
    }

    updateSummaryElement(summaryId, inputId, isSelect = false) {
        const summaryElement = document.getElementById(summaryId);
        const inputElement = document.getElementById(inputId);
        
        if (summaryElement && inputElement) {
            if (isSelect) {
                const selectedOption = inputElement.options[inputElement.selectedIndex];
                summaryElement.textContent = selectedOption ? selectedOption.text : '-';
            } else {
                summaryElement.textContent = inputElement.value || '-';
            }
        }
    }

    formatDateTime(date, time) {
        if (!date || !time) return '-';
        const dateObj = new Date(date + ' ' + time);
        return dateObj.toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    submitForm(e) {
        e.preventDefault();
        
        if (this.validateCurrentStep()) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<div class="loading"></div> Creating Event...';
            submitBtn.disabled = true;

            // Simulate form submission - replace with actual form submission
            setTimeout(() => {
                // Reset form and close modal
                this.resetWizard();
                closeCreateEventModal();
                
                // Show success message
                showNotification('Event created successfully!', 'success');
                
                // Refresh events if needed
                if (typeof refreshEvents === 'function') {
                    refreshEvents();
                }
            }, 2000);
        }
    }

    resetWizard() {
        console.log('Resetting wizard...');
        this.currentStep = 1;
        this.formData = {};
        
        // Reset form
        const form = document.getElementById('eventWizardForm');
        if (form) form.reset();
        
        // Reset UI state
        this.showStep(1);
        this.updateProgress();
        
        // Clear errors
        document.querySelectorAll('.form-control').forEach(field => {
            field.classList.remove('error', 'success');
        });
        document.querySelectorAll('.error-message').forEach(error => {
            error.classList.remove('show');
        });
        
        // Reset submit button
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Create Event';
            submitBtn.classList.remove('loading');
        }
        
        console.log('Wizard reset complete');
    }
}

// Modal functions
function openCreateEventModal() {
    console.log('Opening create event modal...');
    const modal = document.getElementById('createEventModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Initialize wizard if not already done
        if (!window.eventWizard) {
            console.log('Initializing new EventWizard...');
            window.eventWizard = new EventWizard();
        } else {
            console.log('Resetting existing EventWizard...');
            window.eventWizard.resetWizard();
        }
        
        // Force show first step
        setTimeout(() => {
            const firstStep = document.querySelector('.step-content[data-step="1"]');
            const allSteps = document.querySelectorAll('.step-content');
            
            console.log('Found steps:', allSteps.length);
            console.log('First step element:', firstStep);
            
            // Remove active from all steps
            allSteps.forEach(step => {
                step.classList.remove('active');
                console.log('Removed active from step:', step.getAttribute('data-step'));
            });
            
            // Add active to first step
            if (firstStep) {
                firstStep.classList.add('active');
                console.log('Added active to first step');
            }
        }, 100);
    } else {
        console.error('Modal element not found!');
    }
}

function closeCreateEventModal() {
    const modal = document.getElementById('createEventModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        // Reset wizard state
        if (window.eventWizard) {
            window.eventWizard.resetWizard();
        }
    }
}

// Notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        ${message}
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#17a2b8'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        animation: slideInRight 0.3s ease;
    `;
    
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Bind create event button
    const createEventBtn = document.getElementById('createEventBtn');
    if (createEventBtn) {
        createEventBtn.addEventListener('click', openCreateEventModal);
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('createEventModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCreateEventModal();
            }
        });
    }
});
