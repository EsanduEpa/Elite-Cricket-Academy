// Simplified Bookings Page JavaScript - Matches Current PHP Structure
document.addEventListener('DOMContentLoaded', function() {
    initializeBookingsPage();
});

function initializeBookingsPage() {
    // Initialize filter functionality
    initializeTableFilters();
    
    // Initialize modals
    initializeModals();
    
    // Initialize card animations
    initializeCardAnimations();
}

// Table filtering functionality
function initializeTableFilters() {
    const typeFilter = document.getElementById('session-type-filter');
    const statusFilter = document.getElementById('status-filter');
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterSessions);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterSessions);
    }
}

// Enhanced filter function - works with current PHP structure
window.filterSessions = function() {
    const typeFilter = document.getElementById('session-type-filter');
    const statusFilter = document.getElementById('status-filter');
    const tbody = document.getElementById('sessions-tbody');
    const emptyState = document.getElementById('empty-state');
    
    if (!typeFilter || !statusFilter || !tbody) return;
    
    const selectedType = typeFilter.value;
    const selectedStatus = statusFilter.value;
    const rows = tbody.querySelectorAll('tr'); // Use tr instead of .session-row
    
    let visibleRows = 0;
    
    rows.forEach(row => {
        const rowType = row.getAttribute('data-type');
        const rowStatus = row.getAttribute('data-status');
        
        const typeMatch = selectedType === 'all' || rowType === selectedType;
        const statusMatch = selectedStatus === 'all' || rowStatus === selectedStatus;
        
        if (typeMatch && statusMatch) {
            row.style.display = '';
            visibleRows++;
            // Add fade in animation
            row.style.opacity = '0';
            setTimeout(() => {
                row.style.opacity = '1';
            }, 100 * visibleRows);
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    if (emptyState) {
        emptyState.style.display = visibleRows === 0 ? 'block' : 'none';
    }
};

// Clear filters function
window.clearFilters = function() {
    const typeFilter = document.getElementById('session-type-filter');
    const statusFilter = document.getElementById('status-filter');
    
    if (typeFilter) typeFilter.value = 'all';
    if (statusFilter) statusFilter.value = 'all';
    
    filterSessions();
};

// Session action functions - simplified for current structure
window.viewSession = function(sessionId) {
    showSuccessMessage('Opening session details...');
    // In a real application, this would show session details
};

window.cancelSession = function(sessionId) {
    if (confirm('Are you sure you want to cancel this session? Cancellation fees may apply.')) {
        showSuccessMessage('Session cancelled successfully');
        // In a real application, this would make an API call to cancel
    }
};

window.makePayment = function(sessionId) {
    showSuccessMessage('Redirecting to payment page...');
    // In a real application, this would redirect to payment processing
};

// Initialize modals functionality
function initializeModals() {
    // Booking History Modal
    const historyModal = document.getElementById('history-modal');
    if (historyModal) {
        // Close modal when clicking outside
        historyModal.addEventListener('click', function(e) {
            if (e.target === historyModal) {
                closeHistoryModal();
            }
        });
    }
}

// Simple card animations
function initializeCardAnimations() {
    setTimeout(() => {
        const cards = document.querySelectorAll('.stat-card, .dashboard-table tr');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }, 300);
}

// Global functions for HTML onclick events
window.showBookingHistory = function() {
    const historyModal = document.getElementById('history-modal');
    if (historyModal) {
        historyModal.style.display = 'flex';
        loadBookingHistory();
    }
};

window.closeHistoryModal = function() {
    const historyModal = document.getElementById('history-modal');
    if (historyModal) {
        historyModal.style.display = 'none';
    }
};

window.showHistoryTab = function(tabType) {
    // Remove active class from all tabs
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Add active class to clicked tab
    event.target.classList.add('active');
    
    // Load content for the selected tab
    loadHistoryContent(tabType);
};

function loadBookingHistory() {
    const historyContent = document.getElementById('history-content');
    if (!historyContent) return;
    
    // Mock history data
    const mockHistory = [
        {
            id: 1001,
            type: 'Coach Session',
            instructor: 'Coach Johnson',
            date: '2025-10-15',
            time: '09:00 AM',
            status: 'Completed',
            amount: '₹2,500',
            rating: 5
        },
        {
            id: 1002,
            type: 'Trainer Session',
            instructor: 'Trainer Mike',
            date: '2025-10-12',
            time: '06:00 AM',
            status: 'Completed',
            amount: '₹2,000',
            rating: 4
        },
        {
            id: 1003,
            type: 'Coach Session',
            instructor: 'Coach Anderson',
            date: '2025-10-10',
            time: '02:00 PM',
            status: 'Cancelled',
            amount: '₹1,800',
            refund: '₹1,350'
        }
    ];
    
    let historyHTML = '<div class="history-list">';
    
    mockHistory.forEach(session => {
        historyHTML += `
            <div class="history-item">
                <div class="history-header">
                    <div class="history-type">
                        <i class="fas fa-${session.type.includes('Coach') ? 'user-tie' : 'dumbbell'}"></i>
                        <span>${session.type}</span>
                    </div>
                    <span class="history-status ${session.status.toLowerCase()}">${session.status}</span>
                </div>
                <div class="history-details">
                    <div class="history-info">
                        <div><i class="fas fa-user"></i> ${session.instructor}</div>
                        <div><i class="fas fa-calendar"></i> ${session.date}</div>
                        <div><i class="fas fa-clock"></i> ${session.time}</div>
                        <div><i class="fas fa-money-bill"></i> ${session.amount}</div>
                    </div>
                    ${session.rating ? `
                        <div class="history-rating">
                            <span>Rating: </span>
                            ${'★'.repeat(session.rating)}${'☆'.repeat(5-session.rating)}
                        </div>
                    ` : ''}
                    ${session.refund ? `
                        <div class="history-refund">
                            Refunded: ${session.refund}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    historyHTML += '</div>';
    historyContent.innerHTML = historyHTML;
}

function loadHistoryContent(tabType) {
    // Filter the history based on the tab type
    loadBookingHistory(); // For now, just reload all history
}

// Enhanced success message function
function showSuccessMessage(message) {
    const successMsg = document.createElement('div');
    successMsg.className = 'success-message';
    successMsg.innerHTML = `
        <div class="success-content">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
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
        transform: translateX(400px);
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        max-width: 350px;
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