// Tournament Creation Wizard JavaScript
class TournamentWizard {
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
        this.handleCustomTeams();
    }

    bindEvents() {
        // Wizard navigation
        const nextBtn = document.getElementById('tournamentNextBtn');
        const prevBtn = document.getElementById('tournamentPrevBtn');
        const submitBtn = document.getElementById('tournamentSubmitBtn');
        const form = document.getElementById('tournamentWizardForm');

        if (nextBtn) nextBtn.addEventListener('click', () => this.nextStep());
        if (prevBtn) prevBtn.addEventListener('click', () => this.prevStep());
        if (form) form.addEventListener('submit', (e) => this.submitForm(e));
        
        // Real-time validation
        document.querySelectorAll('#tournamentWizardForm .form-control').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearError(input));
        });

        // Custom teams handling
        const numberOfTeamsSelect = document.getElementById('numberOfTeams');
        if (numberOfTeamsSelect) {
            numberOfTeamsSelect.addEventListener('change', () => this.handleCustomTeams());
        }
    }

    setMinDates() {
        const today = new Date().toISOString().split('T')[0];
        const now = new Date().toISOString().slice(0, 16);
        
        const startDate = document.getElementById('tournamentStartDate');
        const endDate = document.getElementById('tournamentEndDate');
        const entryDeadline = document.getElementById('entryDeadline');
        
        if (startDate) startDate.min = today;
        if (endDate) endDate.min = today;
        if (entryDeadline) entryDeadline.min = now;
    }

    handleCustomTeams() {
        const numberOfTeamsSelect = document.getElementById('numberOfTeams');
        const customTeamsGroup = document.getElementById('customTeamsGroup');
        
        if (numberOfTeamsSelect && customTeamsGroup) {
            if (numberOfTeamsSelect.value === 'custom') {
                customTeamsGroup.style.display = 'block';
            } else {
                customTeamsGroup.style.display = 'none';
            }
        }
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
        console.log('Tournament: Showing step:', step);
        
        // Hide all steps explicitly
        document.querySelectorAll('.tournament-step-content').forEach((content, index) => {
            content.classList.remove('active');
            content.style.display = 'none';
            console.log(`Tournament: Hidden step ${index + 1}`);
        });
        
        // Show current step explicitly
        const currentStepElement = document.querySelector(`.tournament-step-content[data-step="${step}"]`);
        
        if (currentStepElement) {
            currentStepElement.classList.add('active');
            currentStepElement.style.display = 'block';
            console.log(`Tournament: Shown step ${step}`);
            
            // Force visibility check
            setTimeout(() => {
                const computedStyle = window.getComputedStyle(currentStepElement);
                console.log(`Tournament step ${step} display:`, computedStyle.display);
                console.log(`Tournament step ${step} opacity:`, computedStyle.opacity);
            }, 50);
        } else {
            console.error('Tournament step element not found for step:', step);
        }
        
        // Update navigation buttons
        const prevBtn = document.getElementById('tournamentPrevBtn');
        const nextBtn = document.getElementById('tournamentNextBtn');
        const submitBtn = document.getElementById('tournamentSubmitBtn');
        
        if (prevBtn) prevBtn.style.display = step === 1 ? 'none' : 'flex';
        if (nextBtn) nextBtn.style.display = step === this.totalSteps ? 'none' : 'flex';
        if (submitBtn) submitBtn.style.display = step === this.totalSteps ? 'flex' : 'none';
    }

    updateProgress() {
        const progressLine = document.getElementById('tournamentProgressLine');
        const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        if (progressLine) {
            progressLine.style.width = progress + '%';
        }

        // Update step indicators
        document.querySelectorAll('#createTournamentModal .step-circle').forEach((circle, index) => {
            const stepNum = index + 1;
            const label = circle.parentNode.querySelector('.step-label');
            
            circle.classList.remove('active', 'completed');
            label.classList.remove('active', 'completed');
            
            if (stepNum < this.currentStep) {
                circle.classList.add('completed');
                label.classList.add('completed');
                circle.innerHTML = '<i class="fas fa-check"></i>';
            } else if (stepNum === this.currentStep) {
                circle.classList.add('active');
                label.classList.add('active');
                circle.textContent = stepNum;
            } else {
                circle.textContent = stepNum;
            }
        });
    }

    validateCurrentStep() {
        let isValid = true;
        const currentStepElement = document.querySelector(`.tournament-step-content[data-step="${this.currentStep}"]`);
        
        if (!currentStepElement) return false;
        
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';
        
        // Clear previous errors
        this.clearError(field);
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }
        
        // Specific field validations
        switch (fieldName) {
            case 'tournament_name':
                if (value && value.length < 3) {
                    isValid = false;
                    errorMessage = 'Tournament name must be at least 3 characters';
                }
                break;
                
            case 'end_date':
                const startDate = document.getElementById('tournamentStartDate').value;
                if (startDate && value && new Date(value) <= new Date(startDate)) {
                    isValid = false;
                    errorMessage = 'End date must be after start date';
                }
                break;
                
            case 'custom_teams_number':
                const numberOfTeamsSelect = document.getElementById('numberOfTeams');
                if (numberOfTeamsSelect && numberOfTeamsSelect.value === 'custom') {
                    if (!value || value < 2 || value > 50) {
                        isValid = false;
                        errorMessage = 'Number of teams must be between 2 and 50';
                    }
                }
                break;
        }
        
        // Show error if validation failed
        if (!isValid) {
            this.showError(field, errorMessage);
        } else {
            this.showSuccess(field);
        }
        
        return isValid;
    }

    showError(field, message) {
        field.classList.add('error');
        field.classList.remove('success');
        
        const errorElement = document.getElementById(field.id + 'Error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    showSuccess(field) {
        field.classList.add('success');
        field.classList.remove('error');
        
        const errorElement = document.getElementById(field.id + 'Error');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
    }

    clearError(field) {
        field.classList.remove('error', 'success');
        
        const errorElement = document.getElementById(field.id + 'Error');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
    }

    saveCurrentStepData() {
        const currentStepElement = document.querySelector(`.tournament-step-content[data-step="${this.currentStep}"]`);
        if (!currentStepElement) return;
        
        const formFields = currentStepElement.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            this.formData[field.name] = field.value;
        });
    }

    populateSummary() {
        // Tournament Information
        document.getElementById('summaryTournamentName').textContent = 
            document.getElementById('tournamentName').value || '-';
        document.getElementById('summaryTournamentType').textContent = 
            this.getSelectText('tournamentType') || '-';
        
        // Number of teams
        const numberOfTeams = document.getElementById('numberOfTeams').value;
        const customNumber = document.getElementById('customTeamsNumber').value;
        const teamsText = numberOfTeams === 'custom' ? customNumber : numberOfTeams;
        document.getElementById('summaryNumberOfTeams').textContent = teamsText || '-';
        
        // Registration fee
        const regFee = document.getElementById('registrationFee').value;
        document.getElementById('summaryTournamentRegistrationFee').textContent = 
            regFee ? `LKR ${regFee}` : 'Free';
        
        // Prizes
        const firstPrize = document.getElementById('firstPrize').value;
        document.getElementById('summaryFirstPrize').textContent = 
            firstPrize ? `LKR ${firstPrize}` : '-';
        
        // Entry deadline
        const deadline = document.getElementById('entryDeadline').value;
        document.getElementById('summaryEntryDeadline').textContent = 
            deadline ? new Date(deadline).toLocaleString() : '-';
        
        // Tournament period
        const startDate = document.getElementById('tournamentStartDate').value;
        const endDate = document.getElementById('tournamentEndDate').value;
        const period = (startDate && endDate) ? `${startDate} to ${endDate}` : '-';
        document.getElementById('summaryTournamentPeriod').textContent = period;
        
        // Match timing
        const startTime = document.getElementById('matchStartTime').value;
        const endTime = document.getElementById('matchEndTime').value;
        const timing = (startTime && endTime) ? `${startTime} - ${endTime}` : '-';
        document.getElementById('summaryMatchTiming').textContent = timing;
        
        // Venues
        document.getElementById('summaryPrimaryVenue').textContent = 
            document.getElementById('primaryVenue').value || '-';
        document.getElementById('summarySecondaryVenue').textContent = 
            document.getElementById('secondaryVenue').value || 'Not specified';
        
        // Matches per day and rest days
        document.getElementById('summaryMatchesPerDay').textContent = 
            document.getElementById('matchesPerDay').value || '-';
        document.getElementById('summaryRestDays').textContent = 
            document.getElementById('restDays').value || '-';
        
        // Management
        document.getElementById('summaryTournamentDirector').textContent = 
            this.getSelectText('tournamentDirector') || '-';
        document.getElementById('summaryHeadUmpire').textContent = 
            this.getSelectText('headUmpire') || 'Not assigned';
        document.getElementById('summaryGroundStaff').textContent = 
            this.getSelectText('groundStaff') || 'Not assigned';
        
        // Description and rules
        document.getElementById('summaryTournamentDescription').textContent = 
            document.getElementById('tournamentDescription').value || '-';
        document.getElementById('summaryTournamentRules').textContent = 
            document.getElementById('tournamentRules').value || 'Standard tournament rules apply';
    }

    getSelectText(elementId) {
        const select = document.getElementById(elementId);
        if (select && select.selectedIndex > 0) {
            return select.options[select.selectedIndex].text;
        }
        return '';
    }

    submitForm(e) {
        e.preventDefault();
        
        if (!this.validateCurrentStep()) {
            return;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('tournamentSubmitBtn');
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Tournament...';
        }
        
        // Collect all form data
        const formData = new FormData(document.getElementById('tournamentWizardForm'));
        
        // Add custom teams number if applicable
        const numberOfTeams = document.getElementById('numberOfTeams').value;
        if (numberOfTeams === 'custom') {
            formData.set('number_of_teams', document.getElementById('customTeamsNumber').value);
        }
        
        // Submit form
        fetch(document.getElementById('tournamentWizardForm').action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Tournament created successfully!');
                closeTournamentModal();
                // Refresh page or update calendar
                if (typeof refreshEvents === 'function') {
                    refreshEvents();
                } else {
                    location.reload();
                }
            } else {
                throw new Error(data.message || 'Failed to create tournament');
            }
        })
        .catch(error => {
            console.error('Error creating tournament:', error);
            alert('Error creating tournament: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            if (submitBtn) {
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="fas fa-trophy"></i> Create Tournament';
            }
        });
    }

    resetWizard() {
        this.currentStep = 1;
        this.formData = {};
        
        // Reset form
        const form = document.getElementById('tournamentWizardForm');
        if (form) form.reset();
        
        // Reset UI state
        this.showStep(1);
        this.updateProgress();
        
        // Clear errors
        document.querySelectorAll('#tournamentWizardForm .form-control').forEach(field => {
            field.classList.remove('error', 'success');
        });
        document.querySelectorAll('#tournamentWizardForm .error-message').forEach(error => {
            error.classList.remove('show');
        });
        
        // Reset custom teams
        this.handleCustomTeams();
        
        console.log('Tournament wizard reset complete');
    }
}

// Modal functions
function openTournamentModal() {
    console.log('Opening tournament modal...');
    const modal = document.getElementById('createTournamentModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Initialize wizard if not already done
        if (!window.tournamentWizard) {
            console.log('Initializing new TournamentWizard...');
            window.tournamentWizard = new TournamentWizard();
        } else {
            console.log('Resetting existing TournamentWizard...');
            window.tournamentWizard.resetWizard();
        }
        
        // Force show first step and hide all others
        setTimeout(() => {
            const allSteps = document.querySelectorAll('.tournament-step-content');
            const firstStep = document.querySelector('.tournament-step-content[data-step="1"]');
            
            console.log('Found tournament steps:', allSteps.length);
            console.log('First step element:', firstStep);
            
            // Hide all steps first
            allSteps.forEach((step, index) => {
                step.classList.remove('active');
                step.style.display = 'none';
                console.log(`Tournament step ${index + 1} hidden`);
            });
            
            // Show only first step
            if (firstStep) {
                firstStep.classList.add('active');
                firstStep.style.display = 'block';
                console.log('Tournament first step shown');
            }
            
            // Reset progress to step 1
            if (window.tournamentWizard) {
                window.tournamentWizard.currentStep = 1;
                window.tournamentWizard.updateProgress();
            }
        }, 100);
    } else {
        console.error('Tournament modal element not found!');
    }
}

function closeTournamentModal() {
    const modal = document.getElementById('createTournamentModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        // Reset wizard state
        if (window.tournamentWizard) {
            window.tournamentWizard.resetWizard();
        }
    }
}

// Check for unsaved tournament data
function checkForUnsavedTournamentData() {
    const form = document.getElementById('tournamentWizardForm');
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
function setupTournamentModalCloseHandlers() {
    const tournamentModal = document.getElementById('createTournamentModal');
    
    if (tournamentModal) {
        // Close on outside click
        tournamentModal.addEventListener('click', function(e) {
            if (e.target === tournamentModal) {
                closeTournamentModal();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && tournamentModal.classList.contains('show')) {
                closeTournamentModal();
            }
        });
    }
}

// Make functions globally available for onclick handlers
window.closeTournamentModal = closeTournamentModal;
window.openTournamentModal = openTournamentModal;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Setup enhanced modal close handlers
    setupTournamentModalCloseHandlers();
    
    // Ensure create tournament button is handled
    const createTournamentBtn = document.getElementById('createTournamentBtn');
    if (createTournamentBtn) {
        // Remove any existing event listeners to avoid conflicts
        createTournamentBtn.replaceWith(createTournamentBtn.cloneNode(true));
        const newBtn = document.getElementById('createTournamentBtn');
        
        newBtn.addEventListener('click', () => {
            console.log('Create Tournament button clicked!');
            openTournamentModal();
        });
    }
});
