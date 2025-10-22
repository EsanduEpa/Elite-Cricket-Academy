// Dummy Requests Data
const requests = [
    {
        id: 1,
        playerName: 'Ashan Perera',
        playerId: 101,
        type: 'reschedule',
        status: 'pending',
        urgent: true,
        title: 'Session Reschedule Request',
        description: 'Request to reschedule Saturday batting session due to school examination.',
        currentDate: '2024-10-26',
        currentTime: '10:00 AM',
        requestedDate: '2024-10-28',
        requestedTime: '2:00 PM',
        sessionType: 'Batting Practice',
        reason: 'School examination on the original scheduled date',
        submittedAt: new Date(Date.now() - 2 * 3600000), // 2 hours ago
        notes: ''
    },
    {
        id: 2,
        playerName: 'Nimal Silva',
        playerId: 102,
        type: 'cancellation',
        status: 'pending',
        urgent: false,
        title: 'Session Cancellation Request',
        description: 'Request to cancel Friday fielding session.',
        currentDate: '2024-10-25',
        currentTime: '4:00 PM',
        sessionType: 'Fielding Practice',
        reason: 'Family emergency - need to travel out of town',
        submittedAt: new Date(Date.now() - 5 * 3600000), // 5 hours ago
        notes: ''
    },
    {
        id: 3,
        playerName: 'Tharindu Jayasinghe',
        playerId: 105,
        type: 'appointment',
        status: 'pending',
        urgent: false,
        title: 'Performance Review Appointment',
        description: 'Request for one-on-one performance review meeting.',
        requestedDate: '2024-10-27',
        requestedTime: '11:00 AM',
        duration: '30 minutes',
        topic: 'Discuss batting technique improvements and tournament preparation',
        submittedAt: new Date(Date.now() - 24 * 3600000), // 1 day ago
        notes: ''
    },
    {
        id: 4,
        playerName: 'Kavinda Rajapaksa',
        playerId: 107,
        type: 'leave',
        status: 'pending',
        urgent: false,
        title: 'Leave Request',
        description: 'Request for leave from all training sessions.',
        startDate: '2024-10-30',
        endDate: '2024-11-02',
        days: 4,
        reason: 'Medical treatment for knee injury - physiotherapy sessions',
        medicalCertificate: 'Yes',
        submittedAt: new Date(Date.now() - 2 * 24 * 3600000), // 2 days ago
        notes: ''
    },
    {
        id: 5,
        playerName: 'Sahan De Silva',
        playerId: 108,
        type: 'reschedule',
        status: 'pending',
        urgent: true,
        title: 'Urgent Session Reschedule',
        description: 'Urgent request to move today\'s evening session to morning.',
        currentDate: '2024-10-24',
        currentTime: '6:00 PM',
        requestedDate: '2024-10-24',
        requestedTime: '9:00 AM',
        sessionType: 'Bowling Practice',
        reason: 'Important family commitment in the evening',
        submittedAt: new Date(Date.now() - 30 * 60000), // 30 mins ago
        notes: ''
    },
    {
        id: 6,
        playerName: 'Chamara Wickramasinghe',
        playerId: 106,
        type: 'reschedule',
        status: 'approved',
        urgent: false,
        title: 'Session Reschedule Request',
        description: 'Rescheduled from Oct 20 to Oct 22',
        currentDate: '2024-10-20',
        currentTime: '3:00 PM',
        requestedDate: '2024-10-22',
        requestedTime: '3:00 PM',
        sessionType: 'Fitness Training',
        reason: 'Medical checkup appointment',
        submittedAt: new Date(Date.now() - 4 * 24 * 3600000),
        approvedAt: new Date(Date.now() - 3 * 24 * 3600000),
        approvedBy: 'Coach',
        notes: 'Approved. Make sure to complete the session.'
    },
    {
        id: 7,
        playerName: 'Dinesh Fernando',
        playerId: 109,
        type: 'appointment',
        status: 'approved',
        urgent: false,
        title: 'Technical Review Meeting',
        description: 'Approved for Oct 23 at 2:00 PM',
        requestedDate: '2024-10-23',
        requestedTime: '2:00 PM',
        duration: '45 minutes',
        topic: 'Review bowling action and discuss improvements',
        submittedAt: new Date(Date.now() - 5 * 24 * 3600000),
        approvedAt: new Date(Date.now() - 4 * 24 * 3600000),
        approvedBy: 'Coach',
        notes: 'Bring your match videos.'
    },
    {
        id: 8,
        playerName: 'Lasith Malinga Jr.',
        playerId: 110,
        type: 'cancellation',
        status: 'rejected',
        urgent: false,
        title: 'Session Cancellation Request',
        description: 'Cancellation rejected - insufficient notice',
        currentDate: '2024-10-19',
        currentTime: '10:00 AM',
        sessionType: 'Team Practice',
        reason: 'Personal reasons',
        submittedAt: new Date(Date.now() - 6 * 24 * 3600000),
        rejectedAt: new Date(Date.now() - 5 * 24 * 3600000),
        rejectedBy: 'Coach',
        rejectionReason: 'insufficient_notice',
        notes: 'Request received less than 24 hours before session. Please provide advance notice.'
    }
];

let currentFilter = 'pending';
let currentTypeFilter = 'all';
let currentSort = 'newest';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCounts();
    loadRequests();
    setupEventListeners();
});

// Update counts
function updateCounts() {
    const pending = requests.filter(r => r.status === 'pending').length;
    const approved = requests.filter(r => r.status === 'approved').length;
    const rejected = requests.filter(r => r.status === 'rejected').length;
    
    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('pendingTabCount').textContent = pending;
    document.getElementById('approvedTabCount').textContent = approved;
    document.getElementById('rejectedTabCount').textContent = rejected;
}

// Load requests
function loadRequests() {
    let filteredRequests = requests;
    
    // Filter by status
    if (currentFilter !== 'all') {
        filteredRequests = filteredRequests.filter(r => r.status === currentFilter);
    }
    
    // Filter by type
    if (currentTypeFilter !== 'all') {
        filteredRequests = filteredRequests.filter(r => r.type === currentTypeFilter);
    }
    
    // Sort
    filteredRequests.sort((a, b) => {
        if (currentSort === 'newest') {
            return b.submittedAt - a.submittedAt;
        } else if (currentSort === 'oldest') {
            return a.submittedAt - b.submittedAt;
        } else if (currentSort === 'urgent') {
            if (a.urgent && !b.urgent) return -1;
            if (!a.urgent && b.urgent) return 1;
            return b.submittedAt - a.submittedAt;
        }
        return 0;
    });
    
    const requestsList = document.getElementById('requestsList');
    
    if (filteredRequests.length === 0) {
        requestsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No requests found</h3>
                <p>There are no ${currentFilter !== 'all' ? currentFilter : ''} requests at the moment.</p>
            </div>
        `;
        return;
    }
    
    requestsList.innerHTML = filteredRequests.map(request => `
        <div class="request-card ${request.status} ${request.urgent ? 'urgent' : ''}" data-id="${request.id}">
            <div class="request-header">
                <div class="request-info">
                    <h3>
                        <i class="fas ${getTypeIcon(request.type)}"></i>
                        ${request.title}
                    </h3>
                    <div class="request-meta">
                        <span class="meta-item">
                            <i class="fas fa-user"></i>
                            ${request.playerName}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-clock"></i>
                            ${formatTime(request.submittedAt)}
                        </span>
                    </div>
                </div>
                <div class="request-badges">
                    ${request.urgent ? '<span class="request-badge urgent">Urgent</span>' : ''}
                    <span class="request-badge ${request.status}">${request.status}</span>
                    <span class="type-badge">${request.type}</span>
                </div>
            </div>
            
            <div class="request-body">
                <p>${request.description}</p>
                ${getRequestDetails(request)}
            </div>
            
            <div class="request-actions">
                ${request.status === 'pending' ? `
                    <button class="btn-approve" onclick="showApprovalModal(${request.id})">
                        <i class="fas fa-check"></i>
                        Approve
                    </button>
                    <button class="btn-reject" onclick="showRejectionModal(${request.id})">
                        <i class="fas fa-times"></i>
                        Reject
                    </button>
                ` : ''}
                <button class="btn-view" onclick="viewRequestDetails(${request.id})">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
            </div>
        </div>
    `).join('');
}

// Get type icon
function getTypeIcon(type) {
    const icons = {
        reschedule: 'fa-calendar-alt',
        cancellation: 'fa-times-circle',
        appointment: 'fa-handshake',
        leave: 'fa-plane-departure'
    };
    return icons[type] || 'fa-clipboard';
}

// Get request details
function getRequestDetails(request) {
    const details = [];
    
    if (request.type === 'reschedule') {
        details.push(`
            <div class="request-details">
                <div class="detail-item">
                    <span class="detail-label">Current Schedule</span>
                    <span class="detail-value">${request.currentDate} at ${request.currentTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Requested Schedule</span>
                    <span class="detail-value">${request.requestedDate} at ${request.requestedTime}</span>
                </div>
            </div>
        `);
    } else if (request.type === 'cancellation') {
        details.push(`
            <div class="request-details">
                <div class="detail-item">
                    <span class="detail-label">Session Date</span>
                    <span class="detail-value">${request.currentDate} at ${request.currentTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Session Type</span>
                    <span class="detail-value">${request.sessionType}</span>
                </div>
            </div>
        `);
    } else if (request.type === 'appointment') {
        details.push(`
            <div class="request-details">
                <div class="detail-item">
                    <span class="detail-label">Requested Date & Time</span>
                    <span class="detail-value">${request.requestedDate} at ${request.requestedTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">${request.duration}</span>
                </div>
            </div>
        `);
    } else if (request.type === 'leave') {
        details.push(`
            <div class="request-details">
                <div class="detail-item">
                    <span class="detail-label">Leave Period</span>
                    <span class="detail-value">${request.startDate} to ${request.endDate}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Days</span>
                    <span class="detail-value">${request.days} days</span>
                </div>
            </div>
        `);
    }
    
    return details.join('');
}

// Format time
function formatTime(date) {
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days === 1) return 'Yesterday';
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
}

// View request details
function viewRequestDetails(requestId) {
    const request = requests.find(r => r.id === requestId);
    if (!request) return;
    
    const details = document.getElementById('requestDetails');
    details.innerHTML = `
        <div class="request-detail-section">
            <h3>Request Information</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Player Name</span>
                    <span class="detail-value">${request.playerName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Request Type</span>
                    <span class="detail-value">${request.type.charAt(0).toUpperCase() + request.type.slice(1)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="request-badge ${request.status}">${request.status}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Submitted</span>
                    <span class="detail-value">${request.submittedAt.toLocaleString()}</span>
                </div>
            </div>
        </div>
        
        <div class="request-detail-section">
            <h3>Details</h3>
            <p>${request.description}</p>
            ${getFullRequestDetails(request)}
        </div>
        
        ${request.notes ? `
        <div class="request-detail-section">
            <h3>${request.status === 'rejected' ? 'Rejection' : 'Approval'} Notes</h3>
            <p>${request.notes}</p>
        </div>
        ` : ''}
    `;
    
    const modalActions = document.getElementById('modalActions');
    if (request.status === 'pending') {
        modalActions.innerHTML = `
            <button class="btn-secondary" onclick="closeRequestDetails()">Close</button>
            <button class="btn-reject" onclick="showRejectionModal(${request.id})">
                <i class="fas fa-times"></i> Reject
            </button>
            <button class="btn-success" onclick="showApprovalModal(${request.id})">
                <i class="fas fa-check"></i> Approve
            </button>
        `;
    } else {
        modalActions.innerHTML = `
            <button class="btn-primary" onclick="closeRequestDetails()">Close</button>
        `;
    }
    
    document.getElementById('requestModal').classList.add('active');
}

// Get full request details
function getFullRequestDetails(request) {
    if (request.type === 'reschedule') {
        return `
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Current Date & Time</span>
                    <span class="detail-value">${request.currentDate} at ${request.currentTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Requested Date & Time</span>
                    <span class="detail-value">${request.requestedDate} at ${request.requestedTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Session Type</span>
                    <span class="detail-value">${request.sessionType}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Reason</span>
                    <span class="detail-value">${request.reason}</span>
                </div>
            </div>
        `;
    } else if (request.type === 'cancellation') {
        return `
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Session Date & Time</span>
                    <span class="detail-value">${request.currentDate} at ${request.currentTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Session Type</span>
                    <span class="detail-value">${request.sessionType}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Reason</span>
                    <span class="detail-value">${request.reason}</span>
                </div>
            </div>
        `;
    } else if (request.type === 'appointment') {
        return `
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Requested Date & Time</span>
                    <span class="detail-value">${request.requestedDate} at ${request.requestedTime}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">${request.duration}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Topic</span>
                    <span class="detail-value">${request.topic}</span>
                </div>
            </div>
        `;
    } else if (request.type === 'leave') {
        return `
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Start Date</span>
                    <span class="detail-value">${request.startDate}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">End Date</span>
                    <span class="detail-value">${request.endDate}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Days</span>
                    <span class="detail-value">${request.days} days</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Medical Certificate</span>
                    <span class="detail-value">${request.medicalCertificate}</span>
                </div>
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <span class="detail-label">Reason</span>
                    <span class="detail-value">${request.reason}</span>
                </div>
            </div>
        `;
    }
    return '';
}

// Close request details
function closeRequestDetails() {
    document.getElementById('requestModal').classList.remove('active');
}

// Show approval modal
function showApprovalModal(requestId) {
    document.getElementById('approveRequestId').value = requestId;
    document.getElementById('requestModal').classList.remove('active');
    document.getElementById('approvalModal').classList.add('active');
}

// Show rejection modal
function showRejectionModal(requestId) {
    document.getElementById('rejectRequestId').value = requestId;
    document.getElementById('requestModal').classList.remove('active');
    document.getElementById('rejectionModal').classList.add('active');
}

// Setup event listeners
function setupEventListeners() {
    // Tab filters
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            loadRequests();
        });
    });
    
    // Type filter
    document.getElementById('typeFilter').addEventListener('change', function(e) {
        currentTypeFilter = e.target.value;
        loadRequests();
    });
    
    // Sort filter
    document.getElementById('sortFilter').addEventListener('change', function(e) {
        currentSort = e.target.value;
        loadRequests();
    });
    
    // Search
    document.getElementById('searchRequests').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.request-card').forEach(card => {
            const playerName = card.querySelector('.meta-item .fas.fa-user').parentElement.textContent.toLowerCase();
            card.style.display = playerName.includes(search) ? '' : 'none';
        });
    });
    
    // Modal close buttons
    document.getElementById('closeRequestModal').addEventListener('click', closeRequestDetails);
    document.getElementById('closeApprovalModal').addEventListener('click', () => {
        document.getElementById('approvalModal').classList.remove('active');
    });
    document.getElementById('closeRejectionModal').addEventListener('click', () => {
        document.getElementById('rejectionModal').classList.remove('active');
    });
    document.getElementById('cancelApproval').addEventListener('click', () => {
        document.getElementById('approvalModal').classList.remove('active');
    });
    document.getElementById('cancelRejection').addEventListener('click', () => {
        document.getElementById('rejectionModal').classList.remove('active');
    });
    
    // Approval form
    document.getElementById('approvalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const requestId = parseInt(document.getElementById('approveRequestId').value);
        const notes = document.getElementById('approvalNotes').value;
        const notify = document.getElementById('notifyPlayer').checked;
        
        const request = requests.find(r => r.id === requestId);
        if (request) {
            request.status = 'approved';
            request.notes = notes || 'Request approved.';
            request.approvedAt = new Date();
            request.approvedBy = 'Coach';
            
            updateCounts();
            loadRequests();
            
            alert(`Request #${requestId} approved successfully!${notify ? '\nPlayer will be notified via email.' : ''}`);
        }
        
        document.getElementById('approvalModal').classList.remove('active');
        this.reset();
    });
    
    // Rejection form
    document.getElementById('rejectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const requestId = parseInt(document.getElementById('rejectRequestId').value);
        const reason = document.getElementById('rejectionReason').value;
        const notes = document.getElementById('rejectionNotes').value;
        const notify = document.getElementById('notifyPlayerReject').checked;
        
        const request = requests.find(r => r.id === requestId);
        if (request) {
            request.status = 'rejected';
            request.rejectionReason = reason;
            request.notes = notes;
            request.rejectedAt = new Date();
            request.rejectedBy = 'Coach';
            
            updateCounts();
            loadRequests();
            
            alert(`Request #${requestId} rejected.${notify ? '\nPlayer will be notified via email.' : ''}`);
        }
        
        document.getElementById('rejectionModal').classList.remove('active');
        this.reset();
    });
    
    // Close modals on backdrop click
    [document.getElementById('requestModal'), 
     document.getElementById('approvalModal'), 
     document.getElementById('rejectionModal')].forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
}

// Make functions globally available
window.viewRequestDetails = viewRequestDetails;
window.closeRequestDetails = closeRequestDetails;
window.showApprovalModal = showApprovalModal;
window.showRejectionModal = showRejectionModal;
