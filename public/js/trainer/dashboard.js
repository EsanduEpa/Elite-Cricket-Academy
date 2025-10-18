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
    }

    console.log('Initializing trainer sidebar...');

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

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('sidebar-open');
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