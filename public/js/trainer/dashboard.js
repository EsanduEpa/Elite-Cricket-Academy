// Trainer Dashboard JavaScript - Elite Cricket Academy
// Simple trainer dashboard functionality

document.addEventListener('DOMContentLoaded', function() {
    console.log('Trainer Dashboard loaded');
    
    // Use simplified sidebar approach like player dashboard
    initializeTrainerSidebar();

    // Initialize basic dashboard functionality
    initializeTrainerDashboard();
    initializeStatsCards();
    initializeNotifications();
    initializeCalendar();
});

// Simple trainer sidebar functionality (based on player dashboard approach)
function initializeTrainerSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const mainContent = document.querySelector('.main-content');

    if (!sidebar || !sidebarToggle || !mainContent) {
        console.error('Sidebar elements not found:', {
            sidebar: !!sidebar, 
            toggle: !!sidebarToggle, 
            mainContent: !!mainContent
        });
        return;
    }}

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

    // Desktop toggle functionality
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Sidebar toggle clicked');
        
        if (window.innerWidth > 1024) {
            // Desktop mode - collapse/expand
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '80px';
            } else {
                mainContent.style.marginLeft = '280px';
            }
        } else {
            // Mobile mode - open/close
            sidebar.classList.toggle('sidebar-open');
        }
    });

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
        }
    });

    // Handle navigation active states
    const navLinks = sidebar.querySelectorAll('.nav-link');
    console.log('Found nav links:', navLinks.length);
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            console.log('Nav link clicked:', this.getAttribute('href'));
            
            // Remove active from all nav links
            navLinks.forEach(l => l.classList.remove('active'));
            // Add active to clicked link
            this.classList.add('active');
            
            // Close mobile sidebar after navigation
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('sidebar-open');
            }
        });
    });
    
    // Set current page as active based on URL
    const currentPath = window.location.pathname;
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
            link.classList.add('active');
        }
    });

    console.log('Trainer sidebar initialized successfully');
}

function initializeTrainerDashboard() {
    // Handle section switching for trainer dashboard
    const sectionLinks = document.querySelectorAll('[data-section]');
    const contentSections = document.querySelectorAll('.content-section');
    
    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const sectionName = this.getAttribute('data-section');
            
            // Update active link
            sectionLinks.forEach(l => l.parentElement.classList.remove('active'));
            this.parentElement.classList.add('active');
            
            // Show corresponding section
            contentSections.forEach(section => {
                section.classList.remove('active');
            });
            
            const targetSection = document.getElementById(`${sectionName}-section`);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });
}

function initializeStatsCards() {
    // Add simple hover animations to stats cards
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 32px rgba(31, 38, 135, 0.37)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 8px 32px rgba(31, 38, 135, 0.37)';
        });
    });
}

function initializeNotifications() {
    // Handle trainer notifications
    const notificationBells = document.querySelectorAll('.notification-bell');
    
    notificationBells.forEach(bell => {
        bell.addEventListener('click', function() {
            console.log('Trainer notification clicked');
            alert('Notifications feature coming soon!');
        });
    });
}

function initializeCalendar() {
    // Initialize calendar if available
    if (typeof CommonCalendar !== 'undefined') {
        const calendar = new CommonCalendar('trainer');
        console.log('Calendar initialized');
    }
}

// Dashboard refresh function
function refreshDashboard() {
    console.log('Refreshing trainer dashboard...');
    
    // Add visual feedback
    const refreshButtons = document.querySelectorAll('.refresh-btn, [onclick*="refreshDashboard"]');
    refreshButtons.forEach(btn => {
        const icon = btn.querySelector('i');
        if (icon) {
            icon.style.animation = 'spin 1s linear';
            setTimeout(() => {
                icon.style.animation = '';
            }, 1000);
        }
    });
    
    // Refresh dashboard data (simulate AJAX call)
    setTimeout(() => {
        updateDashboardTables();
        console.log('Dashboard refreshed successfully');
    }, 500);
}

// Update dashboard tables with fresh data
function updateDashboardTables() {
    // Update today's sessions
    updateTodaysSessions();
    
    // Update client progress
    updateClientProgress();
    
    // Update weekly schedule
    updateWeeklySchedule();
    
    // Update stats cards
    updateStatsCards();
}

function updateTodaysSessions() {
    // This would typically fetch from server
    console.log('Updating today\'s sessions...');
    
    // Add visual feedback to table
    const todaysTable = document.querySelector('#todays-sessions .dashboard-table');
    if (todaysTable) {
        todaysTable.style.opacity = '0.7';
        setTimeout(() => {
            todaysTable.style.opacity = '1';
        }, 300);
    }
}

function updateClientProgress() {
    // Update progress bars animation
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 200);
    });
}

function updateWeeklySchedule() {
    // This would typically fetch from server
    console.log('Updating weekly schedule...');
}

function updateStatsCards() {
    // Animate stats cards on refresh
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.transform = 'scale(1.02)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 200);
        }, index * 100);
    });
}

// Enhanced session management
function handleSessionAction(action, sessionId) {
    switch(action) {
        case 'edit':
            alert('Edit session feature coming soon!');
            break;
        case 'complete':
            if (confirm('Mark this session as completed?')) {
                // Update session status visually
                const sessionRow = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionRow) {
                    const statusBadge = sessionRow.querySelector('.table-badge');
                    if (statusBadge) {
                        statusBadge.className = 'table-badge status-completed';
                        statusBadge.textContent = 'Completed';
                    }
                }
                
                alert('Session marked as completed!');
                // Here you would make an AJAX call to update the database
                setTimeout(refreshDashboard, 1000);
            }
            break;
        case 'delete':
            if (confirm('Are you sure you want to cancel this session?')) {
                // Update session status visually
                const sessionRow = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionRow) {
                    sessionRow.style.opacity = '0.5';
                    const statusBadge = sessionRow.querySelector('.table-badge');
                    if (statusBadge) {
                        statusBadge.className = 'table-badge status-cancelled';
                        statusBadge.textContent = 'Cancelled';
                    }
                }
                
                alert('Session cancelled!');
                // Here you would make an AJAX call to delete from database
                setTimeout(refreshDashboard, 1000);
            }
            break;
        default:
            console.log('Unknown action:', action);
    }
}

// Table interaction enhancements
function initializeTableFeatures() {
    // Add table row click handlers
    const tableRows = document.querySelectorAll('.dashboard-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger on button clicks
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            
            // Add selection visual feedback
            const allRows = document.querySelectorAll('.dashboard-table tbody tr');
            allRows.forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    
    // Initialize table sorting (basic)
    const tableHeaders = document.querySelectorAll('.dashboard-table th[data-sort]');
    tableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            console.log(`Sorting by: ${sortBy}`);
            // Add sorting logic here
        });
    });
}

// Add CSS for selected row
const style = document.createElement('style');
style.textContent = `
    .dashboard-table tbody tr.selected {
        background: rgba(74, 144, 226, 0.1) !important;
        border-left: 3px solid var(--primary-color);
    }
    
    .dashboard-table th[data-sort] {
        cursor: pointer;
        user-select: none;
    }
    
    .dashboard-table th[data-sort]:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Initialize table features when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeTableFeatures, 500);
});