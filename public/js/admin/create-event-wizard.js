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
        this.addValidationStyles();
    }

    addValidationStyles() {
        // Add CSS for error animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes errorPulse {
                0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0; }
                50% { transform: translate(-50%, -50%) scale(1.05); }
                100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            }
            .form-control.error {
                border-color: #dc3545 !important;
                background-color: #fff5f5 !important;
            }
            .form-control.success {
                border-color: #28a745 !important;
                background-color: #f0fff4 !important;
            }
            .error-message {
                display: none;
                color: #dc3545;
                font-size: 12px;
                margin-top: 5px;
            }
            .error-message.show {
                display: block;
                animation: slideDown 0.3s ease;
            }
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
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
        // Validate current step before proceeding
        if (!this.validateCurrentStep()) {
            console.log('Validation failed, staying on step', this.currentStep);
            return; // Don't proceed if validation fails
        }
        
        console.log('Validation passed, proceeding to next step');
        this.saveCurrentStepData();
        
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.showStep(this.currentStep);
            this.updateProgress();
            
            // Populate summary on last step
            if (this.currentStep === 4) {
                this.populateSummary();
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
        console.log('Event: Showing step:', step);
        
        // Hide all steps explicitly
        document.querySelectorAll('.step-content').forEach((content, index) => {
            content.classList.remove('active');
            content.style.display = 'none';
            console.log(`Event: Hidden step ${index + 1}`);
        });
        
        // Show current step explicitly
        const currentStepElement = document.querySelector(`.step-content[data-step="${step}"]`);
        
        if (currentStepElement) {
            currentStepElement.classList.add('active');
            currentStepElement.style.display = 'block';
            console.log(`Event: Shown step ${step}`);
            
            // Force visibility check
            setTimeout(() => {
                const computedStyle = window.getComputedStyle(currentStepElement);
                console.log(`Event step ${step} display:`, computedStyle.display);
                console.log(`Event step ${step} opacity:`, computedStyle.opacity);
            }, 50);
        } else {
            console.error('Event step element not found for step:', step);
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
        const currentStepElement = document.querySelector(`.step-content[data-step="${this.currentStep}"]`);
        if (!currentStepElement) return false;
        
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalidField = null;

        // Validate all required fields
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });

        // Additional validations based on step
        if (this.currentStep === 2) {
            if (!this.validateDates()) {
                isValid = false;
            }
        }

        // Show alert if validation fails
        if (!isValid) {
            if (firstInvalidField) {
                firstInvalidField.focus();
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Show notification
            this.showValidationError('Please fill all required fields correctly before proceeding.');
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
                this.showError(field, errorElement, 'Please enter a valid phone number (at least 10 digits)');
                return false;
            }
        }

        // Number validation
        if (field.type === 'number' && value) {
            const numValue = parseFloat(value);
            if (isNaN(numValue)) {
                this.showError(field, errorElement, 'Please enter a valid number');
                return false;
            }
            if (numValue < 0) {
                this.showError(field, errorElement, 'Value cannot be negative');
                return false;
            }
            // Check max participants limit
            if (field.id === 'maxParticipants' && numValue > 10000) {
                this.showError(field, errorElement, 'Maximum participants cannot exceed 10,000');
                return false;
            }
            // Check registration fee limit
            if (field.id === 'registrationFee' && numValue > 1000000) {
                this.showError(field, errorElement, 'Registration fee seems too high');
                return false;
            }
        }

        // Text field validation (name, venue, etc.)
        if (field.type === 'text' && value) {
            if (value.length < 3) {
                this.showError(field, errorElement, 'Please enter at least 3 characters');
                return false;
            }
            if (value.length > 200) {
                this.showError(field, errorElement, 'Maximum 200 characters allowed');
                return false;
            }
        }

        // Date validation
        if (field.type === 'date' && value) {
            const selectedDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                this.showError(field, errorElement, 'Date cannot be in the past');
                return false;
            }
        }

        // Time validation
        if (field.type === 'time' && value) {
            const [hours, minutes] = value.split(':').map(Number);
            if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                this.showError(field, errorElement, 'Please enter a valid time');
                return false;
            }
        }

        // Select validation
        if (field.tagName === 'SELECT' && field.hasAttribute('required')) {
            if (!value || value === '') {
                this.showError(field, errorElement, 'Please select an option');
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
        const regStart = document.getElementById('registrationStart').value;
        const regEnd = document.getElementById('registrationEnd').value;

        let isValid = true;

        // Validate start and end dates
        if (startDate && endDate) {
            const start = new Date(startDate + ' ' + (startTime || '00:00'));
            const end = new Date(endDate + ' ' + (endTime || '23:59'));

            if (end <= start) {
                this.showError(document.getElementById('endDate'), 
                              document.getElementById('endDateError'), 
                              'End date/time must be after start date/time');
                isValid = false;
            }

            // Check if event duration is too long (more than 1 year)
            const daysDiff = (end - start) / (1000 * 60 * 60 * 24);
            if (daysDiff > 365) {
                this.showError(document.getElementById('endDate'), 
                              document.getElementById('endDateError'), 
                              'Event duration cannot exceed 1 year');
                isValid = false;
            }
        }

        // Validate same-day event times
        if (startDate && endDate && startDate === endDate && startTime && endTime) {
            if (startTime >= endTime) {
                this.showError(document.getElementById('endTime'), 
                              document.getElementById('endTimeError'), 
                              'End time must be after start time');
                isValid = false;
            }
        }

        // Validate registration dates
        if (regStart && regEnd) {
            const regStartDate = new Date(regStart);
            const regEndDate = new Date(regEnd);

            if (regEndDate <= regStartDate) {
                this.showError(document.getElementById('registrationEnd'), 
                              document.getElementById('registrationEndError'), 
                              'Registration close time must be after open time');
                isValid = false;
            }

            // Registration should close before event starts
            if (startDate && regEnd) {
                const eventStart = new Date(startDate + ' ' + (startTime || '00:00'));
                if (regEndDate > eventStart) {
                    this.showError(document.getElementById('registrationEnd'), 
                                  document.getElementById('registrationEndError'), 
                                  'Registration must close before event starts');
                    isValid = false;
                }
            }
        }

        return isValid;
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

    showValidationError(message) {
        // Create a more prominent error notification
        const notification = document.createElement('div');
        notification.className = 'validation-error-notification';
        notification.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #dc3545;
            color: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(220, 53, 69, 0.4);
            z-index: 10001;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            font-size: 16px;
            animation: errorPulse 0.3s ease;
            max-width: 500px;
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translate(-50%, -50%) scale(0.9)';
            notification.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
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
        console.log('=== MODAL EVENT FORM SUBMISSION STARTED ===');
        console.log('Current step:', this.currentStep);
        
        // Final validation check
        if (!this.validateCurrentStep()) {
            console.error('✗ Final validation failed - form not submitted');
            this.showValidationError('Please review and fill all required fields correctly.');
            return;
        }
        
        console.log('✓ Final validation passed');
        
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('eventWizardForm');
        
        if (!form) {
            console.error('❌ Form not found!');
            alert('Error: Form element not found. Please refresh the page and try again.');
            return;
        }
        
        // Verify form action
        if (!form.action || form.action === '') {
            console.error('❌ Form action is empty!');
            alert('Error: Form action URL is missing. Please contact support.');
            return;
        }
        
        console.log('Form found:', form);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Collect and log form data
        const formData = new FormData(form);
        console.log('Form data being submitted:');
        let fieldCount = 0;
        let emptyRequired = [];
        
        for (let [key, value] of formData.entries()) {
            fieldCount++;
            console.log(`  ${fieldCount}. ${key}: ${value || '(empty)'}`);
            
            // Check if required field is empty
            const field = form.querySelector(`[name="${key}"]`);
            if (field && field.hasAttribute('required') && !value) {
                emptyRequired.push(key);
            }
        }
        
        console.log(`Total fields: ${fieldCount}`);
        
        if (emptyRequired.length > 0) {
            console.error('❌ Required fields are empty:', emptyRequired);
            this.showValidationError('Please fill all required fields:\n- ' + emptyRequired.join('\n- '));
            
            // Re-enable submit button
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Create Event';
            submitBtn.disabled = false;
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<div class="loading"></div> Creating Event...';
        submitBtn.disabled = true;
        console.log('✓ Submit button disabled, showing loading state');

        console.log('✓ Submitting form to server...');
        
        // Try to submit the form
        try {
            form.submit();
            console.log('✓ Form.submit() called successfully');
        } catch (error) {
            console.error('❌ Form submission error:', error);
            alert('Error submitting form: ' + error.message);
            
            // Re-enable submit button
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Create Event';
            submitBtn.disabled = false;
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

// Check for unsaved data
function checkForUnsavedData() {
    const form = document.getElementById('eventWizardForm');
    if (!form) return false;
    
    const formData = new FormData(form);
    let hasData = false;
    
    for (const [key, value] of formData.entries()) {
        if (value && value.trim() !== '') {
            hasData = true;
            break;
        }
    }
    
    return hasData;
}

// Enhanced modal functionality
function setupModalCloseHandlers() {
    const eventModal = document.getElementById('createEventModal');
    
    if (eventModal) {
        // Close on outside click
        eventModal.addEventListener('click', function(e) {
            if (e.target === eventModal) {
                closeCreateEventModal();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && eventModal.classList.contains('show')) {
                closeCreateEventModal();
            }
        });
    }
}

// Make functions globally available for onclick handlers
window.closeCreateEventModal = closeCreateEventModal;
window.openCreateEventModal = openCreateEventModal;

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
    console.log('🔧 create-event-wizard.js DOMContentLoaded fired');
    
    // Setup enhanced modal close handlers
    setupModalCloseHandlers();
    
    // Bind create event button
    const createEventBtn = document.getElementById('createEventBtn');
    console.log('🔘 Create Event button found in wizard JS:', !!createEventBtn);
    
    if (createEventBtn) {
        createEventBtn.addEventListener('click', function(e) {
            console.log('🎯 CREATE EVENT BUTTON CLICKED! (from wizard.js)');
            e.preventDefault();
            e.stopPropagation();
            openCreateEventModal();
        });
        console.log('✅ Event listener attached to Create Event button');
    } else {
        console.error('❌ Create Event button NOT FOUND in wizard JS!');
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
