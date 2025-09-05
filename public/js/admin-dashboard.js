// Academy Management Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
    
    // Initialize sidebar
    initializeSidebar();
    
    // Auto-refresh dashboard every 5 minutes
    setInterval(refreshDashboard, 300000);
});

// Initialize sidebar functionality
function initializeSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainContent = document.getElementById('mainContent');
    
    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Mobile menu toggle
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Initialize dropdown menus
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.nav-item');
            
            // Close other dropdowns
            document.querySelectorAll('.nav-item.dropdown').forEach(item => {
                if (item !== dropdown) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('active');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.nav-item.dropdown.active').forEach(item => {
                item.classList.remove('active');
            });
        }
    });
    
    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            !e.target.closest('.admin-sidebar') && 
            !e.target.closest('.mobile-menu-toggle') &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
    
    // Handle menu item clicks for demo
    const menuLinks = document.querySelectorAll('.nav-link, .dropdown-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Check if it's a placeholder link (starts with #)
            if (href === '#' || href.includes('/admin/') && href !== window.location.pathname) {
                e.preventDefault();
                
                // Get the menu name from the link text
                const menuName = this.textContent.trim();
                const icon = this.querySelector('i') ? this.querySelector('i').className : '';
                
                showMenuDemo(menuName, icon);
            }
        });
    });
}

// Show demo for menu items
function showMenuDemo(menuName, iconClass) {
    const demoMessages = {
        'Staff Management': 'Staff Management interface will allow you to:\n• View all coaches, trainers, and sales staff\n• Add new staff members\n• Edit staff profiles and credentials\n• Manage staff schedules',
        'Coaches': 'Coaches management will show:\n• List of all coaches\n• Coaching certifications\n• Performance ratings\n• Assigned teams and players',
        'Physical Trainers': 'Physical Trainers section will display:\n• Trainer profiles and specializations\n• Training programs\n• Client assignments\n• Fitness assessments',
        'Sales Staff': 'Sales Staff management includes:\n• Sales team members\n• Performance metrics\n• Lead assignments\n• Commission tracking',
        'Add Staff': 'Add Staff form will include:\n• Personal information\n• Role selection (Coach/Trainer/Sales)\n• Qualification details\n• Contact information',
        'Player Management': 'Player Management features:\n• Complete player database\n• Performance tracking\n• Training history\n• Parent/guardian information',
        'All Players': 'Players list will show:\n• Active and inactive players\n• Age groups and skill levels\n• Training attendance\n• Performance statistics',
        'Performance': 'Performance tracking includes:\n• Individual player statistics\n• Progress reports\n• Skill development charts\n• Comparative analysis',
        'Suspended': 'Suspended players section:\n• List of suspended players\n• Suspension reasons and duration\n• Reactivation process\n• Appeal status',
        'Events & Tournaments': 'Events management covers:\n• Upcoming tournaments\n• Training sessions\n• Match scheduling\n• Registration management',
        'All Events': 'Events calendar will display:\n• Chronological event list\n• Event details and participants\n• Status tracking\n• Results recording',
        'Tournaments': 'Tournament management:\n• Tournament brackets\n• Team registrations\n• Match schedules\n• Prize distributions',
        'Training Sessions': 'Training session management:\n• Session schedules\n• Attendance tracking\n• Coach assignments\n• Skill focus areas',
        'Create Event': 'Event creation form:\n• Event details and description\n• Date and venue selection\n• Participant requirements\n• Registration settings',
        'Feedback Monitoring': 'Feedback system features:\n• Pending feedback queue\n• Priority-based sorting\n• Response tracking\n• Resolution status',
        'Reports & Analytics': 'Comprehensive reporting:\n• Performance analytics\n• Revenue reports\n• Attendance statistics\n• Custom report generation',
        'Player Performance': 'Performance reports include:\n• Individual progress charts\n• Skill development tracking\n• Comparative analysis\n• Improvement recommendations',
        'Revenue Reports': 'Financial reporting covers:\n• Monthly/yearly revenue\n• Payment tracking\n• Fee collection status\n• Expense management',
        'Attendance': 'Attendance analytics:\n• Training session attendance\n• Event participation rates\n• Individual attendance patterns\n• Absence tracking',
        'Event Reports': 'Event analytics include:\n• Event success metrics\n• Participant feedback\n• Cost analysis\n• ROI calculations',
        'Facility Management': 'Facility management includes:\n• Ground maintenance schedules\n• Equipment inventory\n• Booking management\n• Safety inspections',
        'Equipment Inventory': 'Inventory system features:\n• Equipment catalog\n• Stock levels and alerts\n• Maintenance schedules\n• Purchase tracking',
        'Activity Logs': 'System activity tracking:\n• User actions and timestamps\n• System events\n• Security logs\n• Audit trails',
        'System Settings': 'System configuration options:\n• General academy settings\n• User permissions\n• Notification preferences\n• System maintenance'
    };
    
    const message = demoMessages[menuName] || `${menuName} interface is ready for implementation!`;
    showNotification(message, 'info');
}

// Initialize dashboard functionality
function initializeDashboard() {
    // Add smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add loading states for action buttons
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('loading')) {
                showLoadingState(this);
            }
        });
    });
    
    // Initialize tooltips for icons
    initializeTooltips();
    
    // Add keyboard shortcuts
    initializeKeyboardShortcuts();
}

// Refresh dashboard data
async function refreshDashboard() {
    const refreshBtn = document.querySelector('.refresh-btn');
    const lastUpdatedSpan = document.getElementById('lastUpdated');
    
    // Show loading state
    if (refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    try {
        const response = await fetch(`${window.location.origin}/Elite/admin/refresh`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.success) {
                // Update dashboard counts
                updateDashboardCounts(data.data);
                
                // Update last updated timestamp
                if (lastUpdatedSpan) {
                    lastUpdatedSpan.textContent = new Date().toLocaleString();
                }
                
                // Show success notification
                showNotification('Dashboard refreshed successfully!', 'success');
            } else {
                showNotification('Failed to refresh dashboard data', 'error');
            }
        } else {
            throw new Error('Network response was not ok');
        }
    } catch (error) {
        console.error('Refresh error:', error);
        showNotification('Unable to refresh dashboard data', 'error');
    } finally {
        // Reset refresh button
        if (refreshBtn) {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
        }
    }
}

// Update dashboard counts with new data
function updateDashboardCounts(data) {
    // Update today's stats
    const todayRegistrations = document.getElementById('todayRegistrations');
    const todayEvents = document.getElementById('todayEvents');
    const todayFeedback = document.getElementById('todayFeedback');
    
    if (todayRegistrations && data.todayStats) {
        animateCountUp(todayRegistrations, parseInt(data.todayStats.newRegistrations));
    }
    if (todayEvents && data.todayStats) {
        animateCountUp(todayEvents, parseInt(data.todayStats.activeEvents));
    }
    if (todayFeedback && data.todayStats) {
        animateCountUp(todayFeedback, parseInt(data.todayStats.feedbackReceived));
    }
    
    // Update pending feedback count
    const pendingCount = document.querySelector('.pending-count');
    if (pendingCount && data.totalPendingFeedback) {
        pendingCount.textContent = data.totalPendingFeedback;
        
        // Add pulse effect if count increased
        pendingCount.style.animation = 'pulse 0.5s ease-in-out';
        setTimeout(() => {
            pendingCount.style.animation = '';
        }, 500);
    }
}

// Animate count up effect
function animateCountUp(element, targetValue) {
    const currentValue = parseInt(element.textContent) || 0;
    const increment = targetValue > currentValue ? 1 : -1;
    const steps = Math.abs(targetValue - currentValue);
    const stepDuration = Math.min(1000 / steps, 50);
    
    let current = currentValue;
    const timer = setInterval(() => {
        current += increment;
        element.textContent = current;
        
        if (current === targetValue) {
            clearInterval(timer);
        }
    }, stepDuration);
}

// Show loading state on buttons
function showLoadingState(button) {
    const originalText = button.innerHTML;
    button.classList.add('loading');
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    button.disabled = true;
    
    // Simulate loading for demo purposes
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('loading');
        button.disabled = false;
    }, 2000);
}

// Show notification messages
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add styles if not already added
    if (!document.getElementById('notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                border-radius: 8px;
                padding: 1rem;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: 400px;
                z-index: 10000;
                animation: slideInRight 0.3s ease-out;
            }
            
            .notification-success { border-left: 4px solid #28a745; }
            .notification-error { border-left: 4px solid #dc3545; }
            .notification-info { border-left: 4px solid #007bff; }
            
            .notification-content {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .notification-content i {
                font-size: 1.1rem;
            }
            
            .notification-success i { color: #28a745; }
            .notification-error i { color: #dc3545; }
            .notification-info i { color: #007bff; }
            
            .notification-close {
                background: none;
                border: none;
                font-size: 1rem;
                cursor: pointer;
                padding: 0.25rem;
                color: #666;
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
        `;
        document.head.appendChild(styles);
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Initialize tooltips
function initializeTooltips() {
    // Simple tooltip implementation
    document.querySelectorAll('[title]').forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const element = event.target;
    const title = element.getAttribute('title');
    
    if (title) {
        element.removeAttribute('title');
        element.setAttribute('data-title', title);
        
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = title;
        tooltip.style.cssText = `
            position: absolute;
            background: #333;
            color: white;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            z-index: 10001;
            pointer-events: none;
            white-space: nowrap;
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        
        element.tooltipElement = tooltip;
    }
}

function hideTooltip(event) {
    const element = event.target;
    const title = element.getAttribute('data-title');
    
    if (title) {
        element.setAttribute('title', title);
        element.removeAttribute('data-title');
    }
    
    if (element.tooltipElement) {
        element.tooltipElement.remove();
        element.tooltipElement = null;
    }
}

// Initialize keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(event) {
        // Ctrl/Cmd + R: Refresh dashboard
        if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
            event.preventDefault();
            refreshDashboard();
        }
        
        // Ctrl/Cmd + U: Go to users
        if ((event.ctrlKey || event.metaKey) && event.key === 'u') {
            event.preventDefault();
            window.location.href = `${window.location.origin}/Elite/admin/users`;
        }
        
        // Ctrl/Cmd + E: Go to events
        if ((event.ctrlKey || event.metaKey) && event.key === 'e') {
            event.preventDefault();
            window.location.href = `${window.location.origin}/Elite/admin/events`;
        }
    });
}

// Modal functions (interface demonstration)
function openModal(modalId) {
    const modalMessages = {
        'addUserModal': 'Add User interface will allow you to:\n• Add coaches with certifications\n• Register new players\n• Add physical trainers\n• Add sales employees',
        'addEventModal': 'Add Event interface will allow you to:\n• Create tournaments\n• Schedule training sessions\n• Organize friendly matches\n• Set venue and timing'
    };
    
    const message = modalMessages[modalId] || 'Modal functionality ready for implementation!';
    showNotification(message, 'info');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Chart functions (for future analytics)
function initializeCharts() {
    // Placeholder for chart initialization
    console.log('Chart initialization will be implemented with future analytics features');
}

// Export functions for use in other scripts
window.dashboardUtils = {
    refreshDashboard,
    showNotification,
    openModal,
    closeModal,
    showLoadingState
};
