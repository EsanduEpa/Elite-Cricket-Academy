document.addEventListener('DOMContentLoaded', function() {
    initializeTrainerSidebar();
    initializeTrainerStatsCards();
});

function initializeTrainerSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const mainContent = document.getElementById('mainContent') || document.querySelector('.main-content');

    if (!sidebarToggle || !sidebar || !mainContent) {
        return;
    }

    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });
}

function initializeTrainerStatsCards() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            card.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            card.style.transform = '';
        });
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const background = type === 'success'
        ? '#16a34a'
        : type === 'error'
            ? '#dc2626'
            : '#2563eb';

    notification.textContent = message;
    notification.style.cssText = [
        'position:fixed',
        'top:20px',
        'right:20px',
        'z-index:10000',
        'padding:12px 16px',
        'border-radius:8px',
        'color:#fff',
        'font-size:14px',
        'font-weight:600',
        `background:${background}`,
        'box-shadow:0 12px 24px rgba(15,23,42,0.18)'
    ].join(';');

    document.body.appendChild(notification);
    window.setTimeout(() => notification.remove(), 2500);
}

function logoutUser() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    window.location.href = '/Elite/login/logout';
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