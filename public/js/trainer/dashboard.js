// Modern Trainer Dashboard JavaScript - Optimized
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing dashboard...');
    
    // Initialize time display
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Initialize with small delay to ensure all elements are ready
    setTimeout(() => {
        try {
            initializeDashboard();
            setupEventListeners();
            initializeAnimations();
            console.log('All dashboard components initialized successfully');
        } catch (error) {
            console.error('Error initializing dashboard:', error);
        }
    }, 100);
});

// Update date and time display
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        dateTimeElement.textContent = now.toLocaleDateString('en-US', options);
    }
}

// Close welcome notification
function closeWelcomeNotification() {
    const welcomeMessage = document.querySelector('.welcome-message');
    if (welcomeMessage) {
        welcomeMessage.style.transform = 'translateY(-20px)';
        welcomeMessage.style.opacity = '0';
        setTimeout(() => {
            welcomeMessage.style.display = 'none';
        }, 300);
    }
}

// Nutrition Assignment Functions
function initializeNutritionAssignments() {
    const newAssignmentBtn = document.getElementById('newAssignmentBtn');
    const newAssignmentForm = document.getElementById('newAssignmentForm');
    
    if (newAssignmentBtn && newAssignmentForm) {
        newAssignmentBtn.addEventListener('click', () => {
            newAssignmentForm.style.display = newAssignmentForm.style.display === 'none' ? 'block' : 'none';
        });
    }
    
    // Initialize inline select change handlers
    const dietSelects = document.querySelectorAll('.diet-plan-select');
    const supplementSelects = document.querySelectorAll('.supplement-plan-select');
    
    dietSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            updatePlanAssignment(e.target.dataset.player, 'diet', e.target.value);
        });
    });
    
    supplementSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            updatePlanAssignment(e.target.dataset.player, 'supplement', e.target.value);
        });
    });
}

function updatePlanAssignment(playerId, planType, planValue) {
    console.log(`Updating ${planType} plan for ${playerId} to ${planValue}`);
    
    // Show success notification
    showNotification(`${planType.charAt(0).toUpperCase() + planType.slice(1)} plan updated successfully!`, 'success');
    
    // Here you would typically make an AJAX call to save the assignment
    // For now, we'll just show a confirmation
}

function saveAssignment() {
    const playerSelect = document.getElementById('playerSelect');
    const dietPlanSelect = document.getElementById('dietPlanSelect');
    const supplementPlanSelect = document.getElementById('supplementPlanSelect');
    const startDate = document.getElementById('startDate');
    const duration = document.getElementById('duration');
    const notes = document.getElementById('notes');
    
    if (!playerSelect.value) {
        showNotification('Please select a player or team', 'error');
        return;
    }
    
    if (dietPlanSelect.value === 'none' && supplementPlanSelect.value === 'none') {
        showNotification('Please select at least one plan (diet or supplement)', 'error');
        return;
    }
    
    const assignmentData = {
        player: playerSelect.value,
        dietPlan: dietPlanSelect.value,
        supplementPlan: supplementPlanSelect.value,
        startDate: startDate.value,
        duration: duration.value,
        notes: notes.value
    };
    
    console.log('Saving assignment:', assignmentData);
    
    // Here you would make an AJAX call to save the assignment
    // For now, we'll simulate success
    showNotification('Assignment saved successfully!', 'success');
    
    // Reset form and hide it
    resetAssignmentForm();
    document.getElementById('newAssignmentForm').style.display = 'none';
    
    // Optionally refresh the assignment table
    // refreshAssignmentTable();
}

function cancelAssignment() {
    resetAssignmentForm();
    document.getElementById('newAssignmentForm').style.display = 'none';
}

function resetAssignmentForm() {
    document.getElementById('playerSelect').value = '';
    document.getElementById('dietPlanSelect').value = 'none';
    document.getElementById('supplementPlanSelect').value = 'none';
    document.getElementById('startDate').value = '2025-10-15';
    document.getElementById('duration').value = '6';
    document.getElementById('notes').value = '';
}

function viewProgress(playerId) {
    console.log('Viewing progress for:', playerId);
    showNotification('Progress view feature coming soon!', 'info');
}

function removeAssignment(playerId) {
    if (confirm('Are you sure you want to remove this assignment?')) {
        console.log('Removing assignment for:', playerId);
        showNotification('Assignment removed successfully!', 'success');
        
        // Here you would make an AJAX call to remove the assignment
        // For now, we'll just show confirmation
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show with animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Workout Assignment Functions
function initializeWorkoutAssignments() {
    const newWorkoutAssignmentBtn = document.getElementById('newWorkoutAssignmentBtn');
    const newWorkoutAssignmentForm = document.getElementById('newWorkoutAssignmentForm');
    
    if (newWorkoutAssignmentBtn && newWorkoutAssignmentForm) {
        newWorkoutAssignmentBtn.addEventListener('click', () => {
            newWorkoutAssignmentForm.style.display = newWorkoutAssignmentForm.style.display === 'none' ? 'block' : 'none';
        });
    }
    
    // Initialize inline select change handlers for workout assignments
    const exerciseSelects = document.querySelectorAll('.exercise-video-select');
    const workoutTypeSelects = document.querySelectorAll('.workout-type-select');
    
    exerciseSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            updateWorkoutAssignment(e.target.dataset.player, 'exercise', e.target.value);
        });
    });
    
    workoutTypeSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            updateWorkoutAssignment(e.target.dataset.player, 'workoutType', e.target.value);
        });
    });
}

function updateWorkoutAssignment(playerId, assignmentType, assignmentValue) {
    console.log(`Updating ${assignmentType} assignment for ${playerId} to ${assignmentValue}`);
    
    // Show success notification
    showNotification(`${assignmentType.charAt(0).toUpperCase() + assignmentType.slice(1)} assignment updated successfully!`, 'success');
    
    // Here you would typically make an AJAX call to save the assignment
    // For now, we'll just show a confirmation
}

function saveWorkoutAssignment() {
    const playerSelect = document.getElementById('workoutPlayerSelect');
    const exerciseSelect = document.getElementById('exerciseSelect');
    const workoutTypeSelect = document.getElementById('workoutTypeSelect');
    const startDate = document.getElementById('workoutStartDate');
    const duration = document.getElementById('workoutDuration');
    const notes = document.getElementById('workoutNotes');
    
    if (!playerSelect.value) {
        showNotification('Please select a player or team', 'error');
        return;
    }
    
    if (exerciseSelect.value === 'none' && workoutTypeSelect.value === 'none') {
        showNotification('Please select at least one assignment (exercise video or workout plan type)', 'error');
        return;
    }
    
    const assignmentData = {
        player: playerSelect.value,
        exercise: exerciseSelect.value,
        workoutType: workoutTypeSelect.value,
        startDate: startDate.value,
        duration: duration.value,
        notes: notes.value
    };
    
    console.log('Saving workout assignment:', assignmentData);
    
    // Here you would make an AJAX call to save the assignment
    // For now, we'll simulate success
    showNotification('Workout assignment saved successfully!', 'success');
    
    // Reset form and hide it
    resetWorkoutAssignmentForm();
    document.getElementById('newWorkoutAssignmentForm').style.display = 'none';
    
    // Optionally refresh the assignment table
    // refreshWorkoutAssignmentTable();
}

function cancelWorkoutAssignment() {
    resetWorkoutAssignmentForm();
    document.getElementById('newWorkoutAssignmentForm').style.display = 'none';
}

function resetWorkoutAssignmentForm() {
    document.getElementById('workoutPlayerSelect').value = '';
    document.getElementById('exerciseSelect').value = 'none';
    document.getElementById('workoutTypeSelect').value = 'none';
    document.getElementById('workoutStartDate').value = '2025-10-15';
    document.getElementById('workoutDuration').value = '4';
    document.getElementById('workoutNotes').value = '';
}

function viewWorkoutProgress(playerId) {
    console.log('Viewing workout progress for:', playerId);
    showNotification('Workout progress view feature coming soon!', 'info');
}

function removeWorkoutAssignment(playerId) {
    if (confirm('Are you sure you want to remove this workout assignment?')) {
        console.log('Removing workout assignment for:', playerId);
        showNotification('Workout assignment removed successfully!', 'success');
        
        // Here you would make an AJAX call to remove the assignment
        // For now, we'll just show confirmation
    }
}

// Logout function
function logoutUser() {
    if (confirm('Are you sure you want to logout?')) {
        // Show logout notification
        showNotification('Logging out...', 'info');
        
        // Clear client-side storage
        sessionStorage.clear();
        localStorage.clear();
        
        // Use server-side logout for proper session cleanup
        window.location.href = '/Elite/pages/logout';
    }
}

// Enhanced dashboard initialization with active navigation
function initializeDashboard() {
    console.log('Initializing dashboard...');
    
    // Initialize active navigation state first
    initializeActiveNavigation();
    
    // Show dashboard section by default
    showSection('dashboard');
    
    // Load sample data
    loadSampleEvents();
    
    // Initialize progress indicators with delay to ensure DOM is ready
    setTimeout(() => {
        animateStatsCards();
    }, 200);
    
    console.log('Dashboard initialized successfully');
}

// Initialize active navigation state
function initializeActiveNavigation() {
    // Get current hash or default to dashboard
    const currentHash = window.location.hash.substring(1) || 'dashboard';
    
    // Find and activate the corresponding nav item
    const navLinks = document.querySelectorAll('.nav-link');
    let foundActive = false;
    
    navLinks.forEach(link => {
        const section = link.getAttribute('data-section');
        const navItem = link.parentElement;
        
        // Remove active from all first
        navItem.classList.remove('active');
        
        if (section === currentHash && !foundActive) {
            // Add active to current
            navItem.classList.add('active');
            foundActive = true;
            
            // Ensure proper styling is applied
            setTimeout(() => {
                link.style.transform = '';
                link.style.opacity = '';
            }, 100);
        }
    });
    
    // If no matching section found, activate dashboard
    if (!foundActive) {
        const dashboardLink = document.querySelector('.nav-link[data-section="dashboard"]');
        if (dashboardLink) {
            dashboardLink.parentElement.classList.add('active');
        }
    }
}

// Enhanced navigation with smooth transitions and better feedback
function setupEventListeners() {
    // Initialize nutrition assignments
    initializeNutritionAssignments();
    
    // Initialize workout assignments
    initializeWorkoutAssignments();
    
    // Sidebar navigation with enhanced feedback and smooth transitions
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionName = this.getAttribute('data-section');
            console.log('Nav link clicked:', sectionName);
            
            // Add immediate visual feedback
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            showSectionWithTransition(sectionName);
            updateActiveNavWithTransition(this);
        });
        
        // Add hover effects
        link.addEventListener('mouseenter', function() {
            if (!this.parentElement.classList.contains('active')) {
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        link.addEventListener('mouseleave', function() {
            if (!this.parentElement.classList.contains('active')) {
                this.style.transform = '';
            }
        });
    });

    // Enhanced calendar navigation
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendarWithTransition();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendarWithTransition();
        });
    }

    // Enhanced action buttons with modern feedback
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const action = this.querySelector('span').textContent;
            
            // Add loading state
            this.style.pointerEvents = 'none';
            this.style.opacity = '0.7';
            
            // Simulate processing
            setTimeout(() => {
                handleQuickAction(action);
                this.style.pointerEvents = 'auto';
                this.style.opacity = '1';
            }, 800);
            
            // Add click animation
            createRippleEffect(this, e);
        });
    });
    
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        
        if (window.innerWidth <= 968 && 
            !sidebar.contains(e.target) && 
            !mobileMenuBtn.contains(e.target) &&
            sidebar.classList.contains('mobile-active')) {
            toggleMobileMenu();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileMenuAndModals();
        }
    });
}

// Modern section transitions - Simplified for better performance
function showSectionWithTransition(sectionName) {
    console.log('Switching to section:', sectionName);
    
    const activeSection = document.querySelector('.content-section.active');
    const targetSection = document.getElementById(`${sectionName}-section`);
    
    if (!targetSection) {
        console.error('Target section not found:', `${sectionName}-section`);
        return;
    }
    
    // Close any open modals when switching sections
    closeAllModals();
    
    // Hide all sections immediately
    const allSections = document.querySelectorAll('.content-section');
    allSections.forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none';
    });
    
    // Show target section immediately
    targetSection.classList.add('active');
    targetSection.style.display = 'block';
    targetSection.style.opacity = '1';
    targetSection.style.transform = 'translateY(0)';
    
    // Initialize specific functionality if needed
    if (sectionName === 'schedules') {
        console.log('Initializing schedules section...');
        setTimeout(() => {
            initializeScheduleCalendar();
        }, 100);
    } else if (sectionName === 'bookings') {
        console.log('Initializing bookings section...');
        if (typeof initializeBookingsSection === 'function') {
            initializeBookingsSection();
        }
    } else if (sectionName === 'schedules') {
        console.log('Initializing schedules section...');
        setTimeout(() => {
            if (typeof initializeSchedulesSection === 'function') {
                initializeSchedulesSection();
            }
        }, 100);
    }
    
    console.log('Section switched successfully to:', sectionName);
}

// Enhanced ripple effect for modern UI
function createRippleEffect(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
        z-index: 1;
    `;
    
    // Add ripple animation CSS if not exists
    if (!document.querySelector('#ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Initialize modern animations and effects
function initializeAnimations() {
    // Animate stats cards on load
    animateStatsCards();
    
    // Add intersection observer for scroll animations
    setupScrollAnimations();
    
    // Initialize particle effects
    createParticleBackground();
}

// Animate statistics cards with counting effect
function animateStatsCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        // Stagger animation
        setTimeout(() => {
            card.style.transform = 'translateY(0)';
            card.style.opacity = '1';
            
            // Animate the number
            const numberElement = card.querySelector('.stat-info h3');
            if (numberElement) {
                animateNumber(numberElement);
            }
        }, index * 150);
    });
}

// Number counting animation
function animateNumber(element) {
    const target = parseInt(element.textContent);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Setup scroll animations with Intersection Observer
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards';
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    const animateElements = document.querySelectorAll('.calendar-section, .session-section, .quick-actions');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        observer.observe(el);
    });
}

// Create subtle particle background effect
function createParticleBackground() {
    const particleContainer = document.createElement('div');
    particleContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
        overflow: hidden;
    `;
    
    document.body.appendChild(particleContainer);
    
    // Create floating particles
    for (let i = 0; i < 20; i++) {
        createParticle(particleContainer);
    }
}

function createParticle(container) {
    const particle = document.createElement('div');
    const size = Math.random() * 4 + 2;
    const x = Math.random() * window.innerWidth;
    const y = Math.random() * window.innerHeight;
    const duration = Math.random() * 20 + 10;
    
    particle.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        left: ${x}px;
        top: ${y}px;
        animation: float ${duration}s infinite linear;
    `;
    
    // Add floating animation if not exists
    if (!document.querySelector('#particle-style')) {
        const style = document.createElement('style');
        style.id = 'particle-style';
        style.textContent = `
            @keyframes float {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100px) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    container.appendChild(particle);
    
    // Remove particle after animation
    setTimeout(() => {
        if (particle.parentNode) {
            particle.remove();
            // Create new particle to maintain count
            createParticle(container);
        }
    }, duration * 1000);
}

// Enhanced calendar generation with smooth transitions
function generateCalendarWithTransition() {
    const calendar = document.getElementById('calendar');
    if (!calendar) return;
    
    // Add transition effect
    calendar.style.opacity = '0.5';
    calendar.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        generateCalendar();
        calendar.style.transition = 'all 0.3s ease';
        calendar.style.opacity = '1';
        calendar.style.transform = 'scale(1)';
    }, 150);
}

// Enhanced notification system
function showModernNotification(message, type = 'info', duration = 4000) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    // Add icon based on type
    const icons = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-triangle',
        'warning': 'fas fa-exclamation-circle',
        'info': 'fas fa-info-circle'
    };
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="${icons[type] || icons.info}" style="font-size: 1.2rem;"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="margin-left: auto; background: none; border: none; color: inherit; cursor: pointer; padding: 0.25rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove with animation
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.4s ease forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 400);
    }, duration);
}

// Enhanced mobile menu toggle
function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    
    if (sidebar && mobileMenuBtn) {
        const isActive = sidebar.classList.contains('mobile-active');
        
        if (isActive) {
            sidebar.classList.remove('mobile-active');
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        } else {
            sidebar.classList.add('mobile-active');
            mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
        }
        
        // Add backdrop
        toggleBackdrop(!isActive);
    }
}

// Backdrop for mobile menu
function toggleBackdrop(show) {
    let backdrop = document.querySelector('.mobile-backdrop');
    
    if (show && !backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'mobile-backdrop';
        backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 999;
            animation: fadeIn 0.3s ease;
        `;
        backdrop.addEventListener('click', toggleMobileMenu);
        document.body.appendChild(backdrop);
    } else if (!show && backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => backdrop.remove(), 300);
    }
}

function closeMobileMenuAndModals() {
    // Close mobile menu
    const sidebar = document.querySelector('.sidebar');
    if (sidebar && sidebar.classList.contains('mobile-active')) {
        toggleMobileMenu();
    }
    
    // Close any open modals
    const modals = document.querySelectorAll('.event-modal');
    modals.forEach(modal => modal.remove());
}

// Enhanced loading states
function addLoadingStates() {
    const cards = document.querySelectorAll('.stat-card, .calendar-section, .session-section');
    
    cards.forEach(card => {
        // Add shimmer effect while loading
        card.style.background = `
            linear-gradient(90deg, 
                rgba(255, 255, 255, 0.1) 25%, 
                rgba(255, 255, 255, 0.3) 50%, 
                rgba(255, 255, 255, 0.1) 75%
            )
        `;
        card.style.backgroundSize = '200% 100%';
        card.style.animation = 'shimmer 2s infinite';
        
        // Add shimmer animation
        if (!document.querySelector('#shimmer-style')) {
            const style = document.createElement('style');
            style.id = 'shimmer-style';
            style.textContent = `
                @keyframes shimmer {
                    0% { background-position: -200% 0; }
                    100% { background-position: 200% 0; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Remove shimmer after content loads
        setTimeout(() => {
            card.style.background = '';
            card.style.animation = '';
        }, 2000);
    });
}

// Enhanced quick action handling
function handleQuickAction(action) {
    const actionMessages = {
        'Add Session': { msg: '🎯 Session creation panel opening...', type: 'info' },
        'New Player': { msg: '👤 Player registration form ready!', type: 'success' },
        'Medical Report': { msg: '📋 Medical records system loading...', type: 'info' },
        'Workout Plan': { msg: '💪 Workout planner initialized!', type: 'success' }
    };
    
    const config = actionMessages[action] || { msg: '✨ Feature coming soon!', type: 'info' };
    showModernNotification(config.msg, config.type);
}

// Add fade-out animations
const fadeOutStyle = document.createElement('style');
fadeOutStyle.textContent = `
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(fadeOutStyle);

// Show specific section (keeping original functionality)
function showSection(sectionName) {
    console.log('Showing section (simple):', sectionName);
    
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none';
        section.style.opacity = '1';
        section.style.transform = 'translateY(0)';
    });
    
    // Show selected section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.add('active');
        targetSection.style.display = 'block';
        targetSection.style.opacity = '1';
        targetSection.style.transform = 'translateY(0)';
        console.log('Section shown successfully:', sectionName);
        
        // Initialize section-specific components
        if (sectionName === 'schedules') {
            setTimeout(() => {
                console.log('Initializing schedule calendar...');
                initializeScheduleCalendar();
            }, 100);
        } else if (sectionName === 'bookings') {
            setTimeout(() => {
                console.log('Initializing bookings...');
                initializeBookings();
            }, 100);
        }
    } else {
        console.error('Target section not found:', `${sectionName}-section`);
    }
}

// Enhanced active navigation with smooth transitions
function updateActiveNavWithTransition(activeLink) {
    // Remove active class from all nav items with transition
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        if (item.classList.contains('active')) {
            // Animate out the current active item
            link.style.transform = 'translateY(-2px) scale(0.98)';
            link.style.opacity = '0.8';
            
            setTimeout(() => {
                item.classList.remove('active');
                link.style.transform = '';
                link.style.opacity = '';
            }, 150);
        } else {
            item.classList.remove('active');
        }
    });
    
    // Add active class to new item with transition
    setTimeout(() => {
        const newActiveItem = activeLink.parentElement;
        newActiveItem.classList.add('active');
        
        // Animate in the new active item
        activeLink.style.transform = 'translateY(-4px) scale(1.02)';
        activeLink.style.opacity = '1';
        
        // Reset transform after animation
        setTimeout(() => {
            activeLink.style.transform = '';
        }, 300);
    }, 150);
}

// Fallback function for backward compatibility
function updateActiveNav(activeLink) {
    updateActiveNavWithTransition(activeLink);
}

// Calendar functionality
let currentDate = new Date();
let events = [];

// Sample events data
function loadSampleEvents() {
    const today = new Date();
    events = [
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            type: 'group-session',
            title: 'Youth Cricket Program',
            time: '09:00 AM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            type: 'private-session',
            title: 'Kumara Silva - Fitness',
            time: '11:30 AM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate()),
            type: 'group-session',
            title: 'Advanced Training',
            time: '02:00 PM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1),
            type: 'tournament',
            title: 'Inter-Academy Match',
            time: '10:00 AM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 2),
            type: 'private-session',
            title: 'Anjali Perera - Recovery',
            time: '03:00 PM'
        },
        {
            date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 5),
            type: 'group-session',
            title: 'Weekend Camp',
            time: '09:00 AM'
        }
    ];
}

// Generate calendar
function generateCalendar() {
    const calendar = document.getElementById('calendar');
    const currentMonthElement = document.getElementById('currentMonth');
    
    if (!calendar || !currentMonthElement) return;
    
    // Clear calendar
    calendar.innerHTML = '';
    
    // Update month display
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    currentMonthElement.textContent = `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        calendar.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    // Generate calendar days
    const today = new Date();
    for (let i = 0; i < 42; i++) {
        const day = new Date(startDate);
        day.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Check if day is in current month
        if (day.getMonth() !== currentDate.getMonth()) {
            dayElement.classList.add('other-month');
        }
        
        // Check if day is today
        if (day.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }
        
        // Add day number
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';
        dayNumber.textContent = day.getDate();
        dayElement.appendChild(dayNumber);
        
        // Add events for this day
        const dayEvents = events.filter(event => 
            event.date.toDateString() === day.toDateString()
        );
        
        if (dayEvents.length > 0) {
            dayElement.classList.add('has-events');
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'events-container';
            
            dayEvents.forEach(event => {
                const eventDot = document.createElement('div');
                eventDot.className = `event-dot ${event.type}`;
                eventDot.title = `${event.title} - ${event.time}`;
                eventsContainer.appendChild(eventDot);
            });
            
            dayElement.appendChild(eventsContainer);
        }
        
        // Add click event
        dayElement.addEventListener('click', () => {
            showDayEvents(day, dayEvents);
        });
        
        calendar.appendChild(dayElement);
    }
}

// Show events for selected day
function showDayEvents(date, dayEvents) {
    const modal = createModernEventModal(date, dayEvents);
    document.body.appendChild(modal);
}

// Create modern event modal
function createModernEventModal(date, dayEvents) {
    const modal = document.createElement('div');
    modal.className = 'event-modal';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'event-modal-content';
    
    const header = document.createElement('div');
    header.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    `;
    
    const title = document.createElement('h3');
    title.textContent = `Events for ${date.toLocaleDateString()}`;
    title.style.cssText = `
        color: white;
        font-size: 1.3rem;
        font-weight: 600;
        background: linear-gradient(135deg, #fff 0%, #e0e7ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    `;
    
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.style.cssText = `
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        color: white;
        transition: all 0.3s ease;
    `;
    closeBtn.addEventListener('click', () => {
        modal.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => document.body.removeChild(modal), 300);
    });
    closeBtn.addEventListener('mouseenter', () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.2)';
        closeBtn.style.transform = 'scale(1.1)';
    });
    closeBtn.addEventListener('mouseleave', () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.1)';
        closeBtn.style.transform = 'scale(1)';
    });
    
    header.appendChild(title);
    header.appendChild(closeBtn);
    modalContent.appendChild(header);
    
    if (dayEvents.length === 0) {
        const noEvents = document.createElement('div');
        noEvents.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: rgba(255, 255, 255, 0.7);">
                <i class="fas fa-calendar-day" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p style="font-size: 1.1rem;">No events scheduled for this day.</p>
            </div>
        `;
        modalContent.appendChild(noEvents);
    } else {
        dayEvents.forEach((event, index) => {
            const eventItem = document.createElement('div');
            eventItem.style.cssText = `
                padding: 1.5rem;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 12px;
                margin-bottom: 1rem;
                border-left: 4px solid ${getEventColor(event.type)};
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
                animation: slideInUp 0.4s ease ${index * 0.1}s both;
            `;
            
            eventItem.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <i class="fas fa-clock" style="color: ${getEventColor(event.type)};"></i>
                    <span style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">${event.time}</span>
                </div>
                <h4 style="margin-bottom: 0.5rem; color: white; font-size: 1.1rem; font-weight: 600;">${event.title}</h4>
                <span style="
                    padding: 0.5rem 1rem;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    background: ${getEventBackgroundColor(event.type)};
                    color: ${getEventColor(event.type)};
                    border: 1px solid ${getEventColor(event.type)}30;
                ">${event.type.replace('-', ' ')}</span>
            `;
            
            eventItem.addEventListener('mouseenter', () => {
                eventItem.style.transform = 'translateY(-2px)';
                eventItem.style.boxShadow = '0 8px 25px rgba(255, 255, 255, 0.15)';
            });
            
            eventItem.addEventListener('mouseleave', () => {
                eventItem.style.transform = 'translateY(0)';
                eventItem.style.boxShadow = 'none';
            });
            
            modalContent.appendChild(eventItem);
        });
    }
    
    modal.appendChild(modalContent);
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => document.body.removeChild(modal), 300);
        }
    });
    
    return modal;
}

// Get event colors
function getEventColor(type) {
    switch (type) {
        case 'group-session': return '#10b981';
        case 'private-session': return '#f59e0b';
        case 'tournament': return '#ef4444';
        default: return '#6b7280';
    }
}

function getEventBackgroundColor(type) {
    switch (type) {
        case 'group-session': return 'rgba(16, 185, 129, 0.2)';
        case 'private-session': return 'rgba(245, 158, 11, 0.2)';
        case 'tournament': return 'rgba(239, 68, 68, 0.2)';
        default: return 'rgba(107, 114, 128, 0.2)';
    }
}

// Override the old notification function with the new one
function showNotification(message, type = 'info') {
    showModernNotification(message, type);
}

// Add slide-in-up animation for modal events
const slideInUpStyle = document.createElement('style');
slideInUpStyle.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(slideInUpStyle);

// Nutrition & Supplements Functionality
function initializeNutritionSection() {
    setupNutritionFilters();
    setupPlanActions();
    initializePlanModals();
}

// Nutrition filter functionality
function setupNutritionFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const planCards = document.querySelectorAll('.plan-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filter plan cards with animation
            planCards.forEach(card => {
                const cardType = card.getAttribute('data-plan-type');
                
                if (filter === 'all' || cardType === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease forwards';
                } else {
                    card.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

// Plan actions (assign, edit, delete, view)
function setupPlanActions() {
    // Assign plan buttons
    const assignBtns = document.querySelectorAll('.assign-btn');
    assignBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const planCard = this.closest('.plan-card');
            const planTitle = planCard.querySelector('.plan-title h3').textContent;
            openAssignmentModal(planTitle);
        });
    });
    
    // View plan buttons
    const viewBtns = document.querySelectorAll('.view-btn');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const planCard = this.closest('.plan-card');
            const planTitle = planCard.querySelector('.plan-title h3').textContent;
            openPlanDetailsModal(planTitle, planCard);
        });
    });
    
    // Edit plan buttons
    const editBtns = document.querySelectorAll('.action-btn-small');
    editBtns.forEach(btn => {
        if (btn.querySelector('.fa-edit')) {
            btn.addEventListener('click', function() {
                const planCard = this.closest('.plan-card');
                const planTitle = planCard.querySelector('.plan-title h3').textContent;
                openEditPlanModal(planTitle, planCard);
            });
        }
        
        if (btn.querySelector('.fa-trash')) {
            btn.addEventListener('click', function() {
                const planCard = this.closest('.plan-card');
                const planTitle = planCard.querySelector('.plan-title h3').textContent;
                confirmDeletePlan(planTitle, planCard);
            });
        }
    });
    
    // Create new plan button
    const createPlanBtn = document.getElementById('createPlanBtn');
    if (createPlanBtn) {
        createPlanBtn.addEventListener('click', function() {
            openCreatePlanModal();
        });
    }
}

// Assignment modal
function openAssignmentModal(planTitle) {
    const modal = createNutritionModal(`
        <h2>Assign Plan: ${planTitle}</h2>
        <div class="assignment-options">
            <div class="assignment-type">
                <h3>Assignment Type</h3>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="assignType" value="individual" checked>
                        <span>Individual Player</span>
                    </label>
                    <label>
                        <input type="radio" name="assignType" value="group">
                        <span>Group</span>
                    </label>
                    <label>
                        <input type="radio" name="assignType" value="team">
                        <span>Entire Team</span>
                    </label>
                </div>
            </div>
            
            <div class="player-selection">
                <h3>Select Players</h3>
                <div class="search-box">
                    <input type="text" placeholder="Search players..." class="search-input">
                    <i class="fas fa-search"></i>
                </div>
                <div class="player-list">
                    <div class="player-item">
                        <input type="checkbox" id="player1">
                        <label for="player1">
                            <div class="player-avatar">KS</div>
                            <span>Kamal Silva</span>
                        </label>
                    </div>
                    <div class="player-item">
                        <input type="checkbox" id="player2">
                        <label for="player2">
                            <div class="player-avatar">AP</div>
                            <span>Anjali Perera</span>
                        </label>
                    </div>
                    <div class="player-item">
                        <input type="checkbox" id="player3">
                        <label for="player3">
                            <div class="player-avatar">DF</div>
                            <span>Dasun Fernando</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="assignment-schedule">
                <h3>Schedule</h3>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-input" value="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="form-group">
                    <label>Duration (weeks)</label>
                    <input type="number" class="form-input" min="1" max="52" value="6">
                </div>
            </div>
        </div>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-primary assign-confirm-btn">Assign Plan</button>
        </div>
    `);
    
    // Handle assignment confirmation
    const assignConfirmBtn = modal.querySelector('.assign-confirm-btn');
    assignConfirmBtn.addEventListener('click', function() {
        const selectedPlayers = modal.querySelectorAll('.player-item input:checked');
        if (selectedPlayers.length > 0) {
            showModernNotification(`Plan "${planTitle}" assigned to ${selectedPlayers.length} player(s)`, 'success');
            closeModal(modal);
        } else {
            showModernNotification('Please select at least one player', 'warning');
        }
    });
}

// Plan details modal
function openPlanDetailsModal(planTitle, planCard) {
    const planType = planCard.getAttribute('data-plan-type');
    const planDescription = planCard.querySelector('.plan-description').textContent;
    
    let detailsContent = '';
    
    if (planType === 'supplement') {
        const scheduleItems = planCard.querySelectorAll('.schedule-item');
        detailsContent = '<div class="supplement-details"><h3>Supplement Schedule</h3>';
        scheduleItems.forEach(item => {
            const timeLabel = item.querySelector('.time-label').textContent;
            const supplementName = item.querySelector('.supplement-name').textContent;
            detailsContent += `
                <div class="detail-row">
                    <span class="time">${timeLabel}</span>
                    <span class="supplement">${supplementName}</span>
                </div>
            `;
        });
        detailsContent += '</div>';
    } else {
        detailsContent = `
            <div class="diet-details">
                <h3>Diet Plan Details</h3>
                <div class="detail-section">
                    <h4>Nutritional Guidelines</h4>
                    <ul>
                        <li>High protein intake: 2.5g per kg body weight</li>
                        <li>Complex carbohydrates: 60% of total calories</li>
                        <li>Healthy fats: 20% of total calories</li>
                        <li>Hydration: 3-4 liters per day</li>
                    </ul>
                </div>
                <div class="detail-section">
                    <h4>Meal Structure</h4>
                    <ul>
                        <li>6 meals per day (3 main, 3 snacks)</li>
                        <li>Pre-workout meal 2-3 hours before training</li>
                        <li>Post-workout meal within 30 minutes</li>
                        <li>Adequate rest period between meals</li>
                    </ul>
                </div>
            </div>
        `;
    }
    
    const modal = createNutritionModal(`
        <h2>${planTitle}</h2>
        <p class="plan-description">${planDescription}</p>
        ${detailsContent}
        <div class="modal-actions">
            <button class="btn-primary close-btn">Close</button>
        </div>
    `);
}

// Create plan modal
function openCreatePlanModal() {
    const modal = createNutritionModal(`
        <h2>Create New Plan</h2>
        <div class="create-plan-form">
            <div class="form-group">
                <label>Plan Type</label>
                <select class="form-input" id="planType">
                    <option value="diet">Diet Plan</option>
                    <option value="supplement">Supplement Plan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Plan Name</label>
                <input type="text" class="form-input" placeholder="Enter plan name...">
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select class="form-input">
                    <option value="general">General</option>
                    <option value="personalized">Personalized</option>
                    <option value="tournament">Tournament</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-input" rows="3" placeholder="Enter plan description..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Duration (weeks)</label>
                <input type="number" class="form-input" min="1" max="52" value="6">
            </div>
        </div>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-primary create-btn">Create Plan</button>
        </div>
    `);
    
    const createBtn = modal.querySelector('.create-btn');
    createBtn.addEventListener('click', function() {
        const planName = modal.querySelector('input[placeholder="Enter plan name..."]').value;
        if (planName.trim()) {
            showModernNotification(`Plan "${planName}" created successfully`, 'success');
            closeModal(modal);
        } else {
            showModernNotification('Please enter a plan name', 'warning');
        }
    });
}

// Edit plan modal
function openEditPlanModal(planTitle, planCard) {
    const planDescription = planCard.querySelector('.plan-description').textContent;
    
    const modal = createNutritionModal(`
        <h2>Edit Plan: ${planTitle}</h2>
        <div class="edit-plan-form">
            <div class="form-group">
                <label>Plan Name</label>
                <input type="text" class="form-input" value="${planTitle}">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-input" rows="3">${planDescription}</textarea>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select class="form-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-primary save-btn">Save Changes</button>
        </div>
    `);
    
    const saveBtn = modal.querySelector('.save-btn');
    saveBtn.addEventListener('click', function() {
        showModernNotification(`Plan "${planTitle}" updated successfully`, 'success');
        closeModal(modal);
    });
}

// Delete confirmation
function confirmDeletePlan(planTitle, planCard) {
    const modal = createNutritionModal(`
        <h2>Delete Plan</h2>
        <p>Are you sure you want to delete the plan "<strong>${planTitle}</strong>"?</p>
        <p class="warning-text">This action cannot be undone and will remove the plan from all assigned players.</p>
        
        <div class="modal-actions">
            <button class="btn-secondary cancel-btn">Cancel</button>
            <button class="btn-danger delete-confirm-btn">Delete Plan</button>
        </div>
    `);
    
    const deleteConfirmBtn = modal.querySelector('.delete-confirm-btn');
    deleteConfirmBtn.addEventListener('click', function() {
        // Add fade out animation to the plan card
        planCard.style.animation = 'fadeOut 0.5s ease forwards';
        setTimeout(() => {
            planCard.remove();
        }, 500);
        
        showModernNotification(`Plan "${planTitle}" deleted successfully`, 'success');
        closeModal(modal);
    });
}

// Create nutrition modal
function createNutritionModal(content) {
    const modal = document.createElement('div');
    modal.className = 'nutrition-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            ${content}
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Animation
    modal.style.animation = 'fadeIn 0.3s ease forwards';
    
    // Close handlers
    const closeBtn = modal.querySelector('.modal-close');
    const cancelBtn = modal.querySelector('.cancel-btn');
    const closeConfirmBtn = modal.querySelector('.close-btn');
    
    [closeBtn, cancelBtn, closeConfirmBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => closeModal(modal));
        }
    });
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    return modal;
}

// Close modal
function closeModal(modal) {
    modal.style.animation = 'fadeOut 0.3s ease forwards';
    setTimeout(() => {
        if (modal.parentNode) {
            document.body.removeChild(modal);
        }
    }, 300);
}

// Initialize nutrition functionality when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize existing functionality
    initializeDashboard();
    generateCalendar();
    setupEventListeners();
    initializeAnimations();
    
    // Initialize nutrition section
    initializeNutritionSection();
});

// Add nutrition modal styles
const nutritionModalStyles = document.createElement('style');
nutritionModalStyles.textContent = `
    .nutrition-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(5px);
    }
    
    .nutrition-modal .modal-content {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
        box-shadow: var(--glass-shadow);
    }
    
    .nutrition-modal .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .nutrition-modal .modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }
    
    .nutrition-modal h2 {
        color: var(--text-primary);
        margin-bottom: 1rem;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .assignment-options,
    .create-plan-form,
    .edit-plan-form {
        margin: 1.5rem 0;
    }
    
    .assignment-type h3,
    .player-selection h3,
    .assignment-schedule h3 {
        color: var(--text-primary);
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    
    .radio-group {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .radio-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        color: var(--text-secondary);
    }
    
    .search-box {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
        backdrop-filter: blur(10px);
    }
    
    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }
    
    .player-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
    }
    
    .player-item {
        padding: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .player-item label {
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        color: var(--text-secondary);
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
        backdrop-filter: blur(10px);
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, var(--error-color), #dc2626);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
    }
    
    .warning-text {
        color: var(--warning-color);
        font-style: italic;
        margin: 1rem 0;
    }
    
    .supplement-details,
    .diet-details {
        margin: 1.5rem 0;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .detail-row .time {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .detail-section {
        margin-bottom: 1.5rem;
    }
    
    .detail-section h4 {
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .detail-section ul {
        color: var(--text-secondary);
        padding-left: 1.5rem;
    }
    
    .detail-section li {
        margin-bottom: 0.25rem;
    }
`;
document.head.appendChild(nutritionModalStyles);

// Medical Records Section JavaScript
function initializeMedicalRecords() {
    console.log('Initializing medical records functionality...');
    
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchMedicalTab(tabName);
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('playerSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterMedicalRecords(this.value);
        });
    }
    
    // Filter functionality
    const injuryTypeFilter = document.getElementById('injuryTypeFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (injuryTypeFilter) {
        injuryTypeFilter.addEventListener('change', function() {
            applyMedicalFilters();
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            applyMedicalFilters();
        });
    }
    
    console.log('Medical records functionality initialized');
}

function switchMedicalTab(tabName) {
    console.log('Switching to medical tab:', tabName);
    
    // Remove active class from all buttons and contents
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Add active class to selected button and content
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    const activeContent = document.getElementById(`${tabName}-tab`);
    
    if (activeButton && activeContent) {
        activeButton.classList.add('active');
        activeContent.classList.add('active');
    }
}

function filterMedicalRecords(searchTerm) {
    const medicalCards = document.querySelectorAll('.medical-card');
    const noRecordsMessage = document.querySelector('.no-records-message');
    let visibleCards = 0;
    
    medicalCards.forEach(card => {
        const playerName = card.querySelector('.player-details h3')?.textContent.toLowerCase() || '';
        const injuryInfo = card.querySelector('.injury-info h4')?.textContent.toLowerCase() || '';
        
        const matchesSearch = searchTerm === '' || 
                             playerName.includes(searchTerm.toLowerCase()) || 
                             injuryInfo.includes(searchTerm.toLowerCase());
        
        if (matchesSearch) {
            card.style.display = 'block';
            visibleCards++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no records message
    if (noRecordsMessage) {
        noRecordsMessage.style.display = visibleCards === 0 ? 'block' : 'none';
    }
}

function applyMedicalFilters() {
    const injuryTypeFilter = document.getElementById('injuryTypeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('playerSearch');
    
    const selectedInjuryType = injuryTypeFilter?.value || 'all';
    const selectedStatus = statusFilter?.value || 'all';
    const searchTerm = searchInput?.value || '';
    
    const medicalCards = document.querySelectorAll('.medical-card');
    const noRecordsMessage = document.querySelector('.no-records-message');
    let visibleCards = 0;
    
    medicalCards.forEach(card => {
        const playerName = card.querySelector('.player-details h3')?.textContent.toLowerCase() || '';
        const injuryInfo = card.querySelector('.injury-info h4')?.textContent.toLowerCase() || '';
        const statusBadge = card.querySelector('.status-badge');
        const cardStatus = statusBadge?.className.includes('in-recovery') ? 'in-recovery' :
                          statusBadge?.className.includes('cleared') ? 'cleared' :
                          statusBadge?.className.includes('restricted') ? 'restricted' :
                          statusBadge?.className.includes('monitoring') ? 'monitoring' : '';
        
        // Check search term
        const matchesSearch = searchTerm === '' || 
                             playerName.includes(searchTerm.toLowerCase()) || 
                             injuryInfo.includes(searchTerm.toLowerCase());
        
        // Check injury type filter
        const matchesInjuryType = selectedInjuryType === 'all' || 
                                 injuryInfo.includes(selectedInjuryType.replace('-', ' '));
        
        // Check status filter
        const matchesStatus = selectedStatus === 'all' || cardStatus === selectedStatus;
        
        if (matchesSearch && matchesInjuryType && matchesStatus) {
            card.style.display = 'block';
            visibleCards++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no records message
    if (noRecordsMessage) {
        noRecordsMessage.style.display = visibleCards === 0 ? 'block' : 'none';
    }
}

function viewMedicalFile(fileId) {
    console.log('Viewing medical file:', fileId);
    
    // Create modal for file viewing (read-only)
    const modal = document.createElement('div');
    modal.className = 'medical-file-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="closeMedicalFileModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-file-medical"></i> Medical File Viewer</h3>
                <button class="modal-close" onclick="closeMedicalFileModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="file-viewer">
                    <div class="read-only-notice">
                        <i class="fas fa-eye"></i>
                        <span>Read-Only Access - Physical Trainer View</span>
                    </div>
                    <div class="file-content">
                        <h4>Medical Report - ${fileId}</h4>
                        <p><strong>Patient:</strong> ${getPatientNameFromFileId(fileId)}</p>
                        <p><strong>Report Type:</strong> ${getReportTypeFromFileId(fileId)}</p>
                        <p><strong>Date:</strong> ${getReportDateFromFileId(fileId)}</p>
                        <hr>
                        <div class="report-details">
                            <h5>Summary:</h5>
                            <p>${getReportSummaryFromFileId(fileId)}</p>
                            <h5>Recommendations:</h5>
                            <ul>
                                ${getReportRecommendationsFromFileId(fileId)}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeMedicalFileModal()">Close</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add modal styles
    const modalStyles = document.createElement('style');
    modalStyles.textContent = `
        .medical-file-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            position: relative;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80%;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--glass-bg);
        }
        
        .modal-header h3 {
            margin: 0;
            color: var(--text-primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .modal-close:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .modal-body {
            padding: 1.5rem;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .read-only-notice {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
            padding: 0.75rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .file-content h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .file-content p {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .file-content hr {
            border: none;
            border-top: 1px solid var(--glass-border);
            margin: 1.5rem 0;
        }
        
        .report-details h5 {
            color: var(--primary-color);
            margin: 1rem 0 0.5rem 0;
            font-weight: 700;
        }
        
        .report-details ul {
            margin: 0;
            padding-left: 1.5rem;
            color: var(--text-primary);
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--glass-border);
            background: var(--glass-bg);
            text-align: right;
        }
        
        .btn-secondary {
            padding: 0.75rem 1.5rem;
            background: var(--glass-bg);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: var(--text-secondary);
            color: white;
        }
    `;
    document.head.appendChild(modalStyles);
}

function closeMedicalFileModal() {
    const modal = document.querySelector('.medical-file-modal');
    if (modal) {
        modal.remove();
    }
}

function getPatientNameFromFileId(fileId) {
    if (fileId.includes('kamal')) return 'Kamal Silva';
    if (fileId.includes('anjali')) return 'Anjali Perera';
    if (fileId.includes('dasun')) return 'Dasun Fernando';
    return 'Unknown Patient';
}

function getReportTypeFromFileId(fileId) {
    if (fileId.includes('mri')) return 'MRI Scan Report';
    if (fileId.includes('xray')) return 'X-Ray Report';
    if (fileId.includes('physio')) return 'Physiotherapy Assessment';
    return 'Medical Report';
}

function getReportDateFromFileId(fileId) {
    if (fileId.includes('kamal')) return 'August 16, 2025';
    if (fileId.includes('anjali')) return 'September 2, 2025';
    if (fileId.includes('dasun')) return 'September 5, 2025';
    return 'Date not available';
}

function getReportSummaryFromFileId(fileId) {
    if (fileId.includes('kamal-mri')) {
        return 'MRI scan confirms Grade 2 hamstring strain in the right leg. Mild edema present. No complete muscle tear detected. Estimated recovery time: 3-4 weeks with proper rehabilitation.';
    }
    if (fileId.includes('anjali-xray')) {
        return 'X-ray shows mild shoulder impingement with no bone abnormalities. Soft tissue inflammation detected. Recommend modified activity and physiotherapy intervention.';
    }
    if (fileId.includes('dasun-physio')) {
        return 'Patient shows excellent recovery from previous knee strain. Full range of motion restored. Strength tests within normal parameters. Cleared for all activities with maintenance exercises.';
    }
    return 'Report summary not available.';
}

function getReportRecommendationsFromFileId(fileId) {
    if (fileId.includes('kamal-mri')) {
        return `
            <li>Continue rest for 1 more week</li>
            <li>Gradual return to light jogging in week 4</li>
            <li>Maintain physiotherapy sessions</li>
            <li>Ice therapy 3 times daily</li>
            <li>Follow-up MRI in 4 weeks</li>
        `;
    }
    if (fileId.includes('anjali-xray')) {
        return `
            <li>Avoid overhead activities for 2 weeks</li>
            <li>Shoulder strengthening exercises daily</li>
            <li>Anti-inflammatory medication as prescribed</li>
            <li>Biomechanics assessment recommended</li>
            <li>Return to bowling gradual and supervised</li>
        `;
    }
    if (fileId.includes('dasun-physio')) {
        return `
            <li>Continue knee strengthening exercises</li>
            <li>Proper warm-up before all activities</li>
            <li>Monitor for any discomfort</li>
            <li>Monthly check-ups for 3 months</li>
            <li>Full participation in training and matches</li>
        `;
    }
    return '<li>No specific recommendations available</li>';
}

// Update the main initialization to include medical records and exercises
const originalShowSection = showSection;
showSection = function(sectionName) {
    originalShowSection(sectionName);
    
    // Initialize medical records when medical section is shown
    if (sectionName === 'medical') {
        setTimeout(() => {
            initializeMedicalRecords();
        }, 100);
    }
    
    // Initialize exercises when workout section is shown
    if (sectionName === 'workout') {
        setTimeout(() => {
            initializeExerciseSection();
        }, 100);
    }
};

// Exercise & Workout Management System
function initializeExerciseSection() {
    console.log('Initializing Exercise & Workout section...');
    
    // Initialize tab navigation
    setupExerciseTabs();
    
    // Initialize filter functionality
    setupExerciseFilters();
    
    // Setup exercise card interactions
    setupExerciseCardInteractions();
    
    // Initialize assignment functionality
    setupAssignmentFunctionality();
    
    console.log('Exercise & Workout section initialized');
}

// Tab Navigation for Exercise Section
function setupExerciseTabs() {
    const tabBtns = document.querySelectorAll('.exercise-tabs .tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabBtns.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

// Filter functionality for exercises and workout plans
function setupExerciseFilters() {
    // Exercise Library Filters
    const exerciseFilterBtns = document.querySelectorAll('#exercise-library .filter-btn');
    exerciseFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.getAttribute('data-filter');
            
            // Update active filter button
            exerciseFilterBtns.forEach(filterBtn => filterBtn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter exercise cards
            filterExerciseCards(filterType);
        });
    });
    
    // Workout Plans Filters
    const planFilterBtns = document.querySelectorAll('#workout-plans .filter-btn');
    planFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.getAttribute('data-filter');
            
            // Update active filter button
            planFilterBtns.forEach(filterBtn => filterBtn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter workout plan cards
            filterWorkoutPlanCards(filterType);
        });
    });
}

// Filter exercise cards based on type (updated for health focus)
function filterExerciseCards(filterType) {
    const exerciseCards = document.querySelectorAll('.exercise-card');
    
    exerciseCards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        
        if (filterType === 'all' || cardType === filterType) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Filter workout plan cards based on medical type
function filterWorkoutPlanCards(filterType) {
    const planCards = document.querySelectorAll('.workout-plan-card');
    
    planCards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        
        if (filterType === 'all' || cardType === filterType) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Setup exercise card interactions
function setupExerciseCardInteractions() {
    // Add hover effects and animations
    const exerciseCards = document.querySelectorAll('.exercise-card, .workout-plan-card');
    
    exerciseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Assignment functionality
function setupAssignmentFunctionality() {
    // Assignment filter dropdown
    const assignmentFilter = document.querySelector('#assignments .filter-select');
    if (assignmentFilter) {
        assignmentFilter.addEventListener('change', function() {
            const filterValue = this.value;
            filterAssignments(filterValue);
        });
    }
}

// Filter assignments table
function filterAssignments(filterType) {
    const assignmentRows = document.querySelectorAll('.assignments-table tbody tr');
    
    assignmentRows.forEach(row => {
        const assignmentTypeElement = row.querySelector('.assignment-type');
        if (assignmentTypeElement) {
            const rowType = assignmentTypeElement.textContent.toLowerCase().trim();
            
            if (filterType === 'all' || rowType === filterType) {
                row.style.display = '';
                row.style.animation = 'fadeIn 0.3s ease';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

// Exercise Modal Functions
function openExerciseModal() {
    showNotification('Opening Add Exercise modal...', 'info');
    // TODO: Implement modal functionality
}

function editExercise(exerciseId) {
    showNotification(`Editing Exercise ID: ${exerciseId}`, 'info');
    // TODO: Implement edit functionality
}

function assignExercise(exerciseId) {
    showNotification(`Assigning Exercise ID: ${exerciseId}`, 'info');
    // TODO: Implement assignment functionality
}

function deleteExercise(exerciseId) {
    if (confirm('Are you sure you want to delete this exercise?')) {
        showNotification(`Deleting Exercise ID: ${exerciseId}`, 'success');
        // TODO: Implement delete functionality
    }
}

// Workout Plan Modal Functions (Updated for Personalized Plans)
function openPersonalizedPlanModal() {
    showNotification('Opening Personalized Workout Plan Creator...', 'info');
    // TODO: Implement personalized plan modal functionality
}

function openWorkoutPlanModal() {
    showNotification('Opening Create Workout Plan modal...', 'info');
    // TODO: Implement modal functionality
}

function viewWorkoutPlan(planId) {
    showNotification(`Viewing Detailed Workout Plan ID: ${planId}`, 'info');
    // TODO: Implement view functionality with exercise details
}

function editWorkoutPlan(planId) {
    showNotification(`Editing Workout Plan ID: ${planId}`, 'info');
    // TODO: Implement edit functionality
}

function assignWorkoutPlan(planId) {
    showNotification(`Assigning Workout Plan ID: ${planId}`, 'info');
    // TODO: Implement assignment functionality
}

function reassignWorkoutPlan(planId) {
    showNotification(`Reassigning Workout Plan ID: ${planId} to new player/group...`, 'info');
    // TODO: Implement reassignment functionality
}

// Assignment Modal Functions (Updated for Medical Focus)
function openMedicalAssignmentModal() {
    showNotification('Opening Medical Assignment modal - Review player medical history...', 'info');
    // TODO: Implement medical assignment modal with medical history integration
}

function viewMedicalAssignment(assignmentId) {
    showNotification(`Viewing Medical History and Assignment Details for ID: ${assignmentId}`, 'info');
    // TODO: Implement medical history view functionality
}

function openAssignmentModal() {
    showNotification('Opening New Assignment modal...', 'info');
    // TODO: Implement modal functionality
}

function viewAssignment(assignmentId) {
    showNotification(`Viewing Assignment ID: ${assignmentId}`, 'info');
    // TODO: Implement view functionality
}

function editAssignment(assignmentId) {
    showNotification(`Editing Assignment ID: ${assignmentId}`, 'info');
    // TODO: Implement edit functionality
}

function deleteAssignment(assignmentId) {
    if (confirm('Are you sure you want to delete this medical assignment?')) {
        showNotification(`Deleting Assignment ID: ${assignmentId}`, 'success');
        // TODO: Implement delete functionality
    }
}

// Utility function for notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        animation: slideInFromRight 0.3s ease;
    `;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutToRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations for notifications
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideInFromRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutToRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(notificationStyles);

// Booking Management Functions
let bookingsData = [
    {
        id: 1,
        playerName: 'John Smith',
        playerTeam: 'Senior Team',
        playerAge: 22,
        playerPhone: '+94 77 123 4567',
        date: '2024-12-15',
        time: '2:00 PM - 3:00 PM',
        serviceType: 'Physio Session',
        reason: 'Injury Recovery Assessment',
        notes: 'Shoulder pain after bowling session. Pain scale 6/10, especially during overhead movements.',
        status: 'pending',
        urgency: 'normal',
        practitioner: 'dr-sarah'
    },
    {
        id: 2,
        playerName: 'Emily Johnson',
        playerTeam: 'Junior Team',
        playerAge: 18,
        playerPhone: '+94 71 987 6543',
        date: '2024-12-15',
        time: '4:00 PM - 5:30 PM',
        serviceType: 'Fitness Assessment',
        reason: 'Monthly Fitness Evaluation',
        notes: 'Standard monthly fitness assessment. Focus on cardiovascular endurance and strength improvements.',
        status: 'confirmed',
        urgency: 'normal',
        practitioner: 'coach-mike'
    },
    {
        id: 3,
        playerName: 'Michael Brown',
        playerTeam: 'Senior Team',
        playerAge: 24,
        playerPhone: '+94 76 555 1234',
        date: '2024-12-16',
        time: '10:00 AM - 11:00 AM',
        serviceType: 'Physio Session',
        reason: 'Knee Injury Follow-up',
        notes: 'Previous ACL injury, week 4 of recovery. Check mobility and pain levels.',
        status: 'confirmed',
        urgency: 'urgent',
        practitioner: 'dr-sarah'
    },
    {
        id: 4,
        playerName: 'Sarah Wilson',
        playerTeam: 'Junior Team',
        playerAge: 19,
        playerPhone: '+94 75 444 9876',
        date: '2024-12-14',
        time: '3:00 PM - 4:00 PM',
        serviceType: 'General Consultation',
        reason: 'Pre-tournament Health Check',
        notes: 'Standard pre-tournament health assessment.',
        status: 'completed',
        urgency: 'normal',
        practitioner: 'trainer-alex',
        sessionSummary: 'Complete health assessment completed. Player cleared for upcoming tournament. No issues found.',
        sessionRating: 'excellent'
    }
];

function initializeBookingsSection() {
    console.log('Initializing bookings section...');
    setupBookingFilters();
    setupBookingViewToggle();
    renderBookings();
    initializeBookingCalendar();
}

function setupBookingFilters() {
    const filterBtns = document.querySelectorAll('#bookings-section .filter-btn');
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

    // Date filter
    const dateFilter = document.getElementById('filterDate');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            filterByDate(this.value);
        });
    }

    // Practitioner filter
    const practitionerFilter = document.getElementById('filterPractitioner');
    if (practitionerFilter) {
        practitionerFilter.addEventListener('change', function() {
            filterByPractitioner(this.value);
        });
    }
}

function setupBookingViewToggle() {
    const toggleBtns = document.querySelectorAll('#bookings-section .toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            toggleBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Switch view
            const view = this.dataset.view;
            switchBookingView(view);
        });
    });
}

function switchBookingView(view) {
    const listView = document.getElementById('bookingListView');
    const calendarView = document.getElementById('bookingCalendarView');
    
    if (view === 'list') {
        listView.classList.add('active');
        calendarView.classList.remove('active');
    } else if (view === 'calendar') {
        listView.classList.remove('active');
        calendarView.classList.add('active');
        renderBookingCalendar();
    }
}

function filterBookings(filter) {
    const bookingCards = document.querySelectorAll('#bookings-section .booking-card');
    
    bookingCards.forEach(card => {
        if (filter === 'all' || card.classList.contains(filter)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterByDate(selectedDate) {
    console.log('Filtering by date:', selectedDate);
}

function filterByPractitioner(practitioner) {
    console.log('Filtering by practitioner:', practitioner);
}

function initializeBookingCalendar() {
    const calendarGrid = document.getElementById('bookingCalendarGrid');
    if (!calendarGrid) return;
    
    renderBookingCalendar();
}

function renderBookingCalendar() {
    const calendarGrid = document.getElementById('bookingCalendarGrid');
    if (!calendarGrid) return;
    
    calendarGrid.innerHTML = '';
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.textContent = day;
        dayHeader.style.cssText = 'background: var(--glass-bg-primary); font-weight: 600; padding: 10px; text-align: center; color: var(--text-primary);';
        calendarGrid.appendChild(dayHeader);
    });
    
    // Generate calendar days
    const today = new Date();
    const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1).getDay();
    
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.style.cssText = 'opacity: 0.3; background: var(--background-color); padding: 10px;';
        calendarGrid.appendChild(emptyDay);
    }
    
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        const dateString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayBookings = bookingsData.filter(booking => booking.date === dateString);
        
        dayElement.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 5px;">${day}</div>
            <div>
                ${dayBookings.map(booking => 
                    `<div style="width: 100%; height: 4px; background: ${getStatusColor(booking.status)}; margin: 1px 0; border-radius: 2px;" title="${booking.serviceType} - ${booking.playerName}"></div>`
                ).join('')}
            </div>
        `;
        
        dayElement.style.cssText = 'background: var(--background-color); padding: 10px 8px; min-height: 80px; cursor: pointer; transition: all 0.3s ease; border: 1px solid transparent;';
        
        if (day === today.getDate()) {
            dayElement.style.border = '2px solid var(--primary-color)';
        }
        
        calendarGrid.appendChild(dayElement);
    }
}

function getStatusColor(status) {
    const colors = {
        pending: 'var(--warning-color)',
        confirmed: 'var(--info-color)',
        completed: 'var(--success-color)',
        cancelled: 'var(--error-color)'
    };
    return colors[status] || 'var(--text-secondary)';
}

// Booking Action Functions
function approveBooking(bookingId) {
    console.log('Approving booking:', bookingId);
    showBookingMessage('Booking approved successfully!', 'success');
}

function rejectBooking(bookingId) {
    if (confirm('Are you sure you want to reject this booking?')) {
        console.log('Rejecting booking:', bookingId);
        showBookingMessage('Booking rejected.', 'error');
    }
}

function rescheduleBooking(bookingId) {
    console.log('Reschedule booking:', bookingId);
    alert('Reschedule functionality would open here');
}

function startSession(bookingId) {
    console.log('Starting session:', bookingId);
    showBookingMessage('Session started successfully!', 'success');
}

function completeSession(bookingId) {
    if (confirm('Mark this session as completed?')) {
        console.log('Completing session:', bookingId);
        showBookingMessage('Session marked as completed!', 'success');
    }
}

function addSessionNotes(bookingId) {
    // Check if we're in the bookings section
    const bookingsSection = document.getElementById('bookings-section');
    const currentSection = document.querySelector('.content-section.active');
    
    if (!bookingsSection || !currentSection || currentSection.id !== 'bookings-section') {
        console.log('Session notes only available in bookings section');
        return;
    }
    
    const modal = document.getElementById('sessionNotesModal');
    if (modal) {
        modal.classList.add('active');
        modal.dataset.bookingId = bookingId;
    }
}

function viewSessionNotes(bookingId) {
    viewBookingDetails(bookingId);
}

function scheduleFollowup(bookingId) {
    console.log('Schedule follow-up for booking:', bookingId);
    alert('Follow-up scheduling would open here');
}

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        console.log('Cancelling booking:', bookingId);
        showBookingMessage('Booking cancelled.', 'error');
    }
}

function viewBookingDetails(bookingId) {
    // Check if we're in the bookings section
    const bookingsSection = document.getElementById('bookings-section');
    const currentSection = document.querySelector('.content-section.active');
    
    if (!bookingsSection || !currentSection || currentSection.id !== 'bookings-section') {
        console.log('Booking details only available in bookings section');
        return;
    }
    
    const booking = bookingsData.find(b => b.id === bookingId);
    if (!booking) return;
    
    const detailsContent = document.getElementById('bookingDetailsContent');
    if (!detailsContent) return;
    
    detailsContent.innerHTML = `
        <div style="display: grid; gap: 25px;">
            <div style="background: var(--glass-bg); padding: 20px; border-radius: 15px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user"></i> Player Information
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div><strong>Name:</strong> ${booking.playerName}</div>
                    <div><strong>Team:</strong> ${booking.playerTeam}</div>
                    <div><strong>Age:</strong> ${booking.playerAge} years</div>
                    <div><strong>Phone:</strong> ${booking.playerPhone}</div>
                </div>
            </div>
            <div style="background: var(--glass-bg); padding: 20px; border-radius: 15px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-calendar-check"></i> Appointment Details
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div><strong>Date & Time:</strong> ${booking.date} at ${booking.time}</div>
                    <div><strong>Service:</strong> ${booking.serviceType}</div>
                    <div><strong>Reason:</strong> ${booking.reason}</div>
                    <div><strong>Status:</strong> <span style="text-transform: capitalize; color: ${getStatusColor(booking.status)};">${booking.status}</span></div>
                </div>
                ${booking.notes ? `<div style="margin-top: 15px;"><strong>Notes:</strong><p style="margin-top: 8px; line-height: 1.5;">${booking.notes}</p></div>` : ''}
                ${booking.sessionSummary ? `<div style="margin-top: 15px;"><strong>Session Summary:</strong><p style="margin-top: 8px; line-height: 1.5;">${booking.sessionSummary}</p></div>` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('bookingDetailsModal').classList.add('active');
}

// Modal Functions - Only work in bookings section
function openAddSlotModal() {
    // Check if we're in the bookings section
    const bookingsSection = document.getElementById('bookings-section');
    const currentSection = document.querySelector('.content-section.active');
    
    if (!bookingsSection || !currentSection || currentSection.id !== 'bookings-section') {
        console.log('Modal only available in bookings section');
        return;
    }
    
    const modal = document.getElementById('addSlotModal');
    if (modal) {
        modal.classList.add('active');
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('slotDate');
        if (dateInput) dateInput.value = today;
    }
}

function closeAddSlotModal() {
    const modal = document.getElementById('addSlotModal');
    if (modal) {
        modal.classList.remove('active');
        const form = document.getElementById('addSlotForm');
        if (form) form.reset();
    }
}

// Close all booking modals
function closeAllModals() {
    const modals = ['addSlotModal', 'bookingDetailsModal', 'sessionNotesModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            // Reset forms if they exist
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    });
}

function closeBookingDetailsModal() {
    const modal = document.getElementById('bookingDetailsModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function closeSessionNotesModal() {
    const modal = document.getElementById('sessionNotesModal');
    if (modal) {
        modal.classList.remove('active');
        const form = document.getElementById('sessionNotesForm');
        if (form) form.reset();
    }
}

function showBookingMessage(message, type = 'success') {
    const messageEl = document.createElement('div');
    messageEl.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'rgba(6, 214, 160, 0.1)' : 'rgba(239, 71, 111, 0.1)'};
        color: ${type === 'success' ? 'var(--success-color)' : 'var(--error-color)'};
        border: 1px solid ${type === 'success' ? 'rgba(6, 214, 160, 0.2)' : 'rgba(239, 71, 111, 0.2)'};
        padding: 15px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        z-index: 1000;
        backdrop-filter: blur(10px);
    `;
    messageEl.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'times'}-circle"></i> ${message}`;
    
    document.body.appendChild(messageEl);
    setTimeout(() => messageEl.remove(), 3000);
}

// Schedule Calendar Functions
function initializeScheduleCalendar() {
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    renderCalendar(currentMonth, currentYear);
    
    // Calendar navigation
    document.getElementById('prevMonth')?.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentMonth, currentYear);
    });
    
    document.getElementById('nextMonth')?.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    });
}

function renderCalendar(month, year) {
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    const daysInWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    const currentMonthElement = document.getElementById('currentMonth');
    if (currentMonthElement) {
        currentMonthElement.textContent = `${monthNames[month]} ${year}`;
    }
    
    const calendarGrid = document.getElementById('scheduleCalendar');
    if (!calendarGrid) return;
    
    let calendarHTML = '';
    
    // Add day headers
    daysInWeek.forEach(day => {
        calendarHTML += `<div class="calendar-header-day">${day}</div>`;
    });
    
    // Add previous month's trailing days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        calendarHTML += `<div class="calendar-day other-month">
            <span class="day-number">${day}</span>
        </div>`;
    }
    
    // Add current month's days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = (today.getDate() === day && today.getMonth() === month && today.getFullYear() === year);
        const events = getEventsForDay(day, month, year);
        
        calendarHTML += `<div class="calendar-day ${isToday ? 'today' : ''}" data-date="${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}">
            <span class="day-number">${day}</span>
            <div class="day-events">
                ${events.map(event => `<div class="event-dot ${event.type}" title="${event.title}"></div>`).join('')}
            </div>
        </div>`;
    }
    
    // Add next month's leading days
    const totalCells = calendarHTML.split('calendar-day').length - 1;
    const remainingCells = 42 - totalCells; // 6 rows * 7 days
    for (let day = 1; day <= remainingCells && totalCells < 35; day++) {
        calendarHTML += `<div class="calendar-day other-month">
            <span class="day-number">${day}</span>
        </div>`;
    }
    
    calendarGrid.innerHTML = calendarHTML;
    
    // Add click events to calendar days with enhanced interactions
    calendarGrid.querySelectorAll('.calendar-day:not(.other-month)').forEach(day => {
        day.addEventListener('click', function() {
            // Remove previous selection
            calendarGrid.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection with animation
            this.classList.add('selected');
            
            // Trigger selection animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            const date = this.dataset.date;
            showScheduleForDate(date);
            updateSidebarForDate(date);
        });
        
        // Add hover effects
        day.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        day.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Add animation to calendar render
    calendarGrid.style.opacity = '0';
    calendarGrid.style.transform = 'translateY(10px)';
    setTimeout(() => {
        calendarGrid.style.transition = 'all 0.3s ease';
        calendarGrid.style.opacity = '1';
        calendarGrid.style.transform = 'translateY(0)';
    }, 50);
}

// Enhanced sidebar update for selected date
function updateSidebarForDate(date) {
    console.log('Updating sidebar for date:', date);
    
    // Update upcoming bookings for selected date
    const upcomingBookingsContainer = document.querySelector('.upcoming-bookings');
    if (upcomingBookingsContainer) {
        const selectedDate = new Date(date);
        const formattedDate = selectedDate.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric'
        });
        
        // Generate sample bookings for the selected date
        const sampleBookings = generateSampleBookingsForDate(selectedDate);
        
        upcomingBookingsContainer.innerHTML = `
            <h4 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1rem;">
                <i class="fas fa-calendar-day"></i> 
                Bookings for ${formattedDate}
            </h4>
            ${sampleBookings.map(booking => `
                <div class="upcoming-booking">
                    <div class="booking-time">
                        <i class="fas fa-clock"></i>
                        ${booking.time}
                    </div>
                    <div class="booking-player">${booking.player}</div>
                    <div class="booking-type">${booking.type}</div>
                </div>
            `).join('')}
        `;
        
        // Add animation to new content
        setTimeout(() => {
            const bookings = upcomingBookingsContainer.querySelectorAll('.upcoming-booking');
            bookings.forEach((booking, index) => {
                booking.style.opacity = '0';
                booking.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    booking.style.transition = 'all 0.3s ease';
                    booking.style.opacity = '1';
                    booking.style.transform = 'translateX(0)';
                }, index * 100);
            });
        }, 50);
    }
}

// Generate sample bookings for a specific date
function generateSampleBookingsForDate(date) {
    const players = ['Alex Johnson', 'Sarah Williams', 'Mike Chen', 'Emma Davis', 'James Wilson'];
    const types = ['Health Assessment', 'Fitness Evaluation', 'Injury Recovery', 'Medical Consultation', 'Physical Therapy'];
    const times = ['09:00 AM', '10:30 AM', '12:00 PM', '02:00 PM', '04:00 PM', '06:00 PM'];
    
    const numBookings = Math.floor(Math.random() * 4) + 1; // 1-4 bookings
    const bookings = [];
    
    for (let i = 0; i < numBookings; i++) {
        bookings.push({
            player: players[Math.floor(Math.random() * players.length)],
            type: types[Math.floor(Math.random() * types.length)],
            time: times[Math.floor(Math.random() * times.length)]
        });
    }
    
    // Sort by time
    bookings.sort((a, b) => {
        const timeA = new Date(`1970/01/01 ${a.time}`);
        const timeB = new Date(`1970/01/01 ${b.time}`);
        return timeA - timeB;
    });
    
    return bookings;
}

// Enhanced animation for stats cards
function animateStatsCards() {
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
            
            // Animate the numbers
            const numberElement = card.querySelector('.stat-number');
            if (numberElement) {
                animateNumber(numberElement);
            }
        }, index * 150);
    });
}

// Animate number counting effect
function animateNumber(element) {
    const finalNumber = parseInt(element.textContent);
    const duration = 1000;
    const increment = finalNumber / (duration / 16); // 60fps
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= finalNumber) {
            current = finalNumber;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

function getEventsForDay(day, month, year) {
    // Sample events - in real app, this would come from API
    const sampleEvents = [
        { day: 9, type: 'training', title: 'Health Assessment' },
        { day: 9, type: 'physio', title: 'Physio Session' },
        { day: 10, type: 'meeting', title: 'Medical Review' },
        { day: 11, type: 'training', title: 'Fitness Evaluation' },
        { day: 12, type: 'physio', title: 'Injury Assessment' },
        { day: 15, type: 'meeting', title: 'Health Consultation' },
        { day: 16, type: 'training', title: 'Physical Screening' }
    ];
    
    return sampleEvents.filter(event => event.day === day);
}

function showScheduleForDate(date) {
    console.log('Showing schedule for date:', date);
    // Implementation would show detailed schedule for selected date
}

// Schedule View Toggle
function initializeScheduleViewToggle() {
    const toggleBtns = document.querySelectorAll('#schedules-section .toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active button
            toggleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide views
            const calendarView = document.getElementById('scheduleCalendarView');
            const listView = document.getElementById('scheduleListView');
            
            if (view === 'calendar') {
                calendarView?.classList.add('active');
                listView?.classList.remove('active');
            } else {
                calendarView?.classList.remove('active');
                listView?.classList.add('active');
            }
        });
    });
}

    initializeScheduleViewToggle();

// Enhanced Calendar functionality for schedules
function initializeScheduleCalendar() {
    const calendarGrid = document.getElementById('scheduleCalendar');
    if (!calendarGrid) return;

    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    // Create calendar header for days of week
    const daysOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
    
    // Clear existing content
    calendarGrid.innerHTML = '';
    
    // Add day headers
    daysOfWeek.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
    });

    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

    // Sample health inspection events
    const healthEvents = {
        5: [
            { type: 'training', title: 'Team Fitness Assessment' },
            { type: 'physio', title: 'Recovery Session' }
        ],
        10: [
            { type: 'meeting', title: 'Medical Review' }
        ],
        12: [
            { type: 'training', title: 'Individual Health Check' }
        ],
        15: [
            { type: 'physio', title: 'Physio Session' },
            { type: 'training', title: 'Fitness Evaluation' }
        ],
        18: [
            { type: 'meeting', title: 'Health Consultation' }
        ],
        22: [
            { type: 'training', title: 'Team Health Review' }
        ],
        25: [
            { type: 'physio', title: 'Injury Assessment' }
        ],
        28: [
            { type: 'meeting', title: 'Medical Conference' },
            { type: 'training', title: 'Fitness Test' }
        ]
    };

    // Add previous month's trailing days
    for (let i = firstDay - 1; i >= 0; i--) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day other-month';
        dayElement.innerHTML = `<div class="calendar-day-number">${daysInPrevMonth - i}</div>`;
        calendarGrid.appendChild(dayElement);
    }

    // Add current month's days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Check if it's today
        if (day === currentDate.getDate() && 
            currentMonth === new Date().getMonth() && 
            currentYear === new Date().getFullYear()) {
            dayElement.classList.add('today');
        }

        const dayNumber = document.createElement('div');
        dayNumber.className = 'calendar-day-number';
        dayNumber.textContent = day;
        dayElement.appendChild(dayNumber);

        // Add events for this day
        if (healthEvents[day]) {
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'events-container';
            
            healthEvents[day].forEach(event => {
                const eventElement = document.createElement('div');
                eventElement.className = `calendar-event ${event.type}`;
                eventElement.textContent = event.title;
                eventElement.title = event.title;
                eventsContainer.appendChild(eventElement);
            });
            
            dayElement.appendChild(eventsContainer);
        }

        // Add click handler
        dayElement.addEventListener('click', () => {
            document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
            dayElement.classList.add('selected');
            // Handle day selection
        });

        calendarGrid.appendChild(dayElement);
    }

    // Add next month's leading days
    const totalCells = calendarGrid.children.length - 7; // Subtract header row
    const remainingCells = 42 - totalCells; // 6 rows × 7 days = 42 cells
    
    for (let day = 1; day <= remainingCells; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day other-month';
        dayElement.innerHTML = `<div class="calendar-day-number">${day}</div>`;
        calendarGrid.appendChild(dayElement);
    }

    // Update month display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const currentMonthElement = document.getElementById('currentMonth');
    if (currentMonthElement) {
        currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }
}

// View toggle functionality
function initializeViewToggle() {
    const toggleButtons = document.querySelectorAll('.toggle-btn');
    const calendarView = document.getElementById('scheduleCalendarView');
    const listView = document.getElementById('scheduleListView');

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const viewType = btn.dataset.view;
            
            // Update button states
            toggleButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show/hide views
            if (viewType === 'calendar') {
                calendarView?.classList.add('active');
                listView?.classList.remove('active');
            } else {
                calendarView?.classList.remove('active');
                listView?.classList.add('active');
            }
        });
    });
}

// Calendar navigation
function initializeCalendarNavigation() {
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            // Navigate to previous month
            console.log('Previous month');
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            // Navigate to next month
            console.log('Next month');
        });
    }
}

// Modal functions for adding sessions and notes
function openAddSessionModal() {
    console.log('Opening add session modal');
    // Add modal functionality here
}

function openAddNoteModal() {
    console.log('Opening add note modal');
    // Add modal functionality here
}
function openAddSessionModal() {
    console.log('Opening add session modal');
    // Implementation for add session modal
}

function openAddNoteModal() {
    console.log('Opening add note modal');
    // Implementation for add note modal
}

// Initialize schedule features when schedules section is active
function initializeSchedulesSection() {
    initializeScheduleCalendar();
    initializeScheduleViewToggle();
}

// Make functions globally available
window.openAddSlotModal = openAddSlotModal;
window.closeAddSlotModal = closeAddSlotModal;
window.closeBookingDetailsModal = closeBookingDetailsModal;
window.closeSessionNotesModal = closeSessionNotesModal;
window.approveBooking = approveBooking;
window.rejectBooking = rejectBooking;
window.rescheduleBooking = rescheduleBooking;
window.startSession = startSession;
window.completeSession = completeSession;
window.addSessionNotes = addSessionNotes;
window.viewSessionNotes = viewSessionNotes;
window.scheduleFollowup = scheduleFollowup;
window.cancelBooking = cancelBooking;
window.viewBookingDetails = viewBookingDetails;
window.openAddSessionModal = openAddSessionModal;
window.openAddNoteModal = openAddNoteModal;
