<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/feedback.css">

<div class="admin-layout">
    <!-- Left Sidebar Panel -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-user-shield"></i>
                <h3>Admin Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard Overview</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Player Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events & Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Feedback Monitoring</span>
                        <span class="badge"><?php echo $data['feedbackStats']['pending']; ?></span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Finance Management</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Admin Profile -->
        <div class="admin-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <span class="admin-name">Admin User</span>
                <span class="admin-role">Super Administrator</span>
            </div>
            <div class="logout-btn">
                <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Feedback Header -->
        <div class="feedback-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-comments"></i> Feedback Monitoring</h1>
                    <p>Monitor and respond to user feedback, suggestions, and concerns</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" id="exportFeedbackBtn">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                    <button class="btn btn-primary" id="feedbackSettingsBtn">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Feedback Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['feedbackStats']['total']; ?></div>
                    <div class="stat-label">Total Feedback</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['feedbackStats']['pending']; ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon progress">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['feedbackStats']['inProgress']; ?></div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon resolved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['feedbackStats']['resolved']; ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Bar -->
        <div class="quick-stats-bar">
            <div class="quick-stat">
                <i class="fas fa-exclamation-circle"></i>
                <span class="stat-value"><?php echo $data['feedbackStats']['highPriority']; ?></span>
                <span class="stat-text">High Priority</span>
            </div>
            <div class="quick-stat">
                <i class="fas fa-calendar-day"></i>
                <span class="stat-value"><?php echo $data['feedbackStats']['todayCount']; ?></span>
                <span class="stat-text">Today</span>
            </div>
            <div class="quick-stat">
                <i class="fas fa-hourglass-half"></i>
                <span class="stat-value"><?php echo $data['feedbackStats']['avgResponseTime']; ?></span>
                <span class="stat-text">Avg Response</span>
            </div>
            <div class="quick-stat">
                <i class="fas fa-smile"></i>
                <span class="stat-value"><?php echo $data['feedbackStats']['satisfactionRate']; ?>%</span>
                <span class="stat-text">Satisfaction</span>
            </div>
        </div>

        <!-- Filter and Search Section -->
        <div class="filter-section">
            <div class="filter-tabs">
                <button class="filter-tab active" data-status="all">
                    <i class="fas fa-list"></i> All Feedback
                    <span class="tab-count"><?php echo $data['feedbackStats']['total']; ?></span>
                </button>
                <button class="filter-tab" data-status="pending">
                    <i class="fas fa-clock"></i> Pending
                    <span class="tab-count"><?php echo $data['feedbackStats']['pending']; ?></span>
                </button>
                <button class="filter-tab" data-status="in_progress">
                    <i class="fas fa-spinner"></i> In Progress
                    <span class="tab-count"><?php echo $data['feedbackStats']['inProgress']; ?></span>
                </button>
                <button class="filter-tab" data-status="resolved">
                    <i class="fas fa-check-circle"></i> Resolved
                    <span class="tab-count"><?php echo $data['feedbackStats']['resolved']; ?></span>
                </button>
            </div>
            
            <div class="filter-controls">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="feedbackSearch" placeholder="Search feedback...">
                </div>
                
               
                
                <select class="filter-select" id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="training">Training</option>
                    <option value="facilities">Facilities</option>
                    <option value="equipment">Equipment</option>
                    <option value="events">Events</option>
                    <option value="services">Services</option>
                    <option value="other">Other</option>
                </select>
                
                <button class="btn btn-outline" id="clearFiltersBtn">
                    <i class="fas fa-redo"></i> Clear Filters
                </button>
            </div>
        </div>

        <!-- Feedback Table -->
        <div class="feedback-table-section">
            <div class="table-header">
                <h2><i class="fas fa-inbox"></i> Feedback Messages</h2>
                <div class="table-actions">
                    <button class="btn btn-sm" id="bulkActionBtn">
                        <i class="fas fa-tasks"></i> Bulk Actions
                    </button>
                </div>
            </div>
            
            <div class="feedback-table-wrapper">
                <table class="feedback-table" id="feedbackTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th width="200">Submitted By</th>
                            <th>Subject</th>
                            <th width="120">Status</th>
                            <th width="140">Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['allFeedbacks'] as $feedback): ?>
                        <tr class="feedback-row" data-status="<?php echo $feedback['status']; ?>" data-priority="<?php echo $feedback['priority']; ?>" data-category="<?php echo $feedback['category']; ?>" data-feedback-id="<?php echo $feedback['id']; ?>">
                            <td>
                                <input type="checkbox" class="feedback-checkbox" value="<?php echo $feedback['id']; ?>">
                            </td>
                            <td class="user-cell">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name"><?php echo $feedback['user_name']; ?></div>
                                        <div class="user-email"><?php echo $feedback['user_email'] ?? 'N/A'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="subject-cell">
                                <div class="subject-text"><?php echo $feedback['subject']; ?></div>
                                <div class="message-preview"><?php echo substr($feedback['message'], 0, 50) . '...'; ?></div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $feedback['status']; ?>">
                                    <?php 
                                        $statusIcons = [
                                            'pending' => 'fa-clock',
                                            'in_progress' => 'fa-spinner',
                                            'resolved' => 'fa-check-circle'
                                        ];
                                        $statusText = str_replace('_', ' ', $feedback['status']);
                                        echo '<i class="fas ' . $statusIcons[$feedback['status']] . '"></i> ';
                                        echo ucfirst($statusText); 
                                    ?>
                                </span>
                            </td>
                            <td class="date-cell">
                                <div class="date-info">
                                    <div class="date-text"><?php echo date('M d, Y', strtotime($feedback['created_at'])); ?></div>
                                    <div class="time-text"><?php echo date('h:i A', strtotime($feedback['created_at'])); ?></div>
                                </div>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-action-table view" onclick="viewFeedback(<?php echo $feedback['id']; ?>)" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action-table edit" onclick="respondFeedback(<?php echo $feedback['id']; ?>)" title="Respond">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button class="btn-action-table delete" onclick="deleteFeedback(<?php echo $feedback['id']; ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing <strong>1</strong> to <strong><?php echo count($data['allFeedbacks']); ?></strong> of <strong><?php echo $data['feedbackStats']['total']; ?></strong> entries
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Feedback Detail Modal -->
<div id="feedbackDetailModal" class="feedback-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-comment-alt"></i> Feedback Details</h2>
            <button class="modal-close" onclick="closeFeedbackModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="feedback-detail-content">
                <!-- User Info Section -->
                <div class="detail-section">
                    <h3><i class="fas fa-user"></i> Submitted By</h3>
                    <div class="user-detail-card">
                        <div class="user-avatar-large">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-detail-info">
                            <div class="detail-name" id="modalUserName">Loading...</div>
                            <div class="detail-email" id="modalUserEmail">Loading...</div>
                            <div class="detail-date" id="modalDate">Loading...</div>
                        </div>
                    </div>
                </div>
                
                <!-- Feedback Info Section -->
                <div class="detail-section">
                    <h3><i class="fas fa-info-circle"></i> Feedback Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Category:</span>
                            <span class="info-value" id="modalCategory">Loading...</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Priority:</span>
                            <span class="info-value" id="modalPriority">Loading...</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status:</span>
                            <span class="info-value" id="modalStatus">Loading...</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Feedback ID:</span>
                            <span class="info-value" id="modalFeedbackId">Loading...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Subject Section -->
                <div class="detail-section">
                    <h3><i class="fas fa-heading"></i> Subject</h3>
                    <div class="subject-box" id="modalSubject">
                        Loading...
                    </div>
                </div>
                
                <!-- Message Section -->
                <div class="detail-section">
                    <h3><i class="fas fa-comment-dots"></i> Message</h3>
                    <div class="message-box" id="modalMessage">
                        Loading...
                    </div>
                </div>
                
                <!-- Response Section -->
                <div class="detail-section" id="responseSection" style="display: none;">
                    <h3><i class="fas fa-reply"></i> Admin Response</h3>
                    <div class="response-box" id="modalResponse">
                        No response yet
                    </div>
                    <div class="response-date" id="modalResolvedDate"></div>
                </div>
                
                <!-- Action Section -->
                <div class="detail-section">
                    <h3><i class="fas fa-tasks"></i> Response & Actions</h3>
                    <div class="response-form">
                        <textarea id="responseText" class="response-textarea" placeholder="Type your response here..." rows="4"></textarea>
                        
                        <div class="action-buttons">
                            <div class="status-actions">
                                <button class="btn btn-warning" onclick="updateFeedbackStatus('in_progress')">
                                    <i class="fas fa-spinner"></i> Mark In Progress
                                </button>
                                <button class="btn btn-success" onclick="updateFeedbackStatus('resolved')">
                                    <i class="fas fa-check"></i> Mark Resolved
                                </button>
                            </div>
                            <button class="btn btn-danger" onclick="deleteFeedbackFromModal()">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store current feedback ID
let currentFeedbackId = null;

// View feedback details
function viewFeedback(feedbackId) {
    currentFeedbackId = feedbackId;
    const row = document.querySelector(`tr[data-feedback-id="${feedbackId}"]`);
    
    if (row) {
        // Extract data from row
        const userInfo = row.querySelector('.user-details');
        const userName = userInfo.querySelector('.user-name').textContent;
        const userEmail = userInfo.querySelector('.user-email').textContent;
        
        const subject = row.querySelector('.subject-text').textContent;
        const category = row.querySelector('.category-badge').textContent.trim();
        const priority = row.querySelector('.priority-badge').textContent.trim();
        const status = row.querySelector('.status-badge').textContent.trim();
        const dateInfo = row.querySelector('.date-info');
        const date = dateInfo.querySelector('.date-text').textContent;
        const time = dateInfo.querySelector('.time-text').textContent;
        
        // Populate modal
        document.getElementById('modalUserName').textContent = userName;
        document.getElementById('modalUserEmail').textContent = userEmail;
        document.getElementById('modalDate').textContent = `${date} at ${time}`;
        document.getElementById('modalCategory').textContent = category;
        document.getElementById('modalPriority').textContent = priority;
        document.getElementById('modalStatus').textContent = status;
        document.getElementById('modalFeedbackId').textContent = `#${feedbackId}`;
        document.getElementById('modalSubject').textContent = subject;
        
        // Get full message (would normally come from AJAX call)
        const feedbackData = <?php echo json_encode($data['allFeedbacks']); ?>;
        const feedback = feedbackData.find(f => f.id == feedbackId);
        
        if (feedback) {
            document.getElementById('modalMessage').textContent = feedback.message;
            
            // Show response if exists
            if (feedback.admin_response) {
                document.getElementById('responseSection').style.display = 'block';
                document.getElementById('modalResponse').textContent = feedback.admin_response;
                if (feedback.resolved_at) {
                    document.getElementById('modalResolvedDate').textContent = 
                        'Responded on ' + new Date(feedback.resolved_at).toLocaleString();
                }
            } else {
                document.getElementById('responseSection').style.display = 'none';
            }
        }
        
        // Show modal
        document.getElementById('feedbackDetailModal').classList.add('active');
    }
}

function respondFeedback(feedbackId) {
    viewFeedback(feedbackId);
    document.getElementById('responseText').focus();
}

function closeFeedbackModal() {
    document.getElementById('feedbackDetailModal').classList.remove('active');
    currentFeedbackId = null;
}

function updateFeedbackStatus(status) {
    if (!currentFeedbackId) return;
    
    const response = document.getElementById('responseText').value;
    
    if (confirm(`Are you sure you want to mark this feedback as ${status}?`)) {
        // In real implementation, make AJAX call
        console.log('Updating feedback', currentFeedbackId, 'to status:', status, 'with response:', response);
        
        // Show success message
        alert('Feedback status updated successfully!');
        
        // Close modal and reload
        closeFeedbackModal();
        location.reload();
    }
}

function deleteFeedback(feedbackId) {
    if (confirm('Are you sure you want to delete this feedback? This action cannot be undone.')) {
        // In real implementation, make AJAX call
        console.log('Deleting feedback:', feedbackId);
        alert('Feedback deleted successfully!');
        location.reload();
    }
}

function deleteFeedbackFromModal() {
    if (currentFeedbackId) {
        deleteFeedback(currentFeedbackId);
    }
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Tab filtering
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const status = this.getAttribute('data-status');
            filterFeedback(status);
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('feedbackSearch');
    searchInput.addEventListener('input', function() {
        searchFeedback(this.value);
    });
    
    // Priority filter
    const priorityFilter = document.getElementById('priorityFilter');
    priorityFilter.addEventListener('change', function() {
        applyFilters();
    });
    
    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    categoryFilter.addEventListener('change', function() {
        applyFilters();
    });
    
    // Clear filters
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('feedbackSearch').value = '';
        document.getElementById('priorityFilter').value = 'all';
        document.getElementById('categoryFilter').value = 'all';
        filterTabs[0].click();
    });
    
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.feedback-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
});

function filterFeedback(status) {
    const rows = document.querySelectorAll('.feedback-row');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            if (row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function searchFeedback(query) {
    const rows = document.querySelectorAll('.feedback-row');
    const lowerQuery = query.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(lowerQuery)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function applyFilters() {
    const priority = document.getElementById('priorityFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const rows = document.querySelectorAll('.feedback-row');
    
    rows.forEach(row => {
        let showRow = true;
        
        if (priority !== 'all' && row.getAttribute('data-priority') !== priority) {
            showRow = false;
        }
        
        if (category !== 'all' && row.getAttribute('data-category') !== category) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Close modal on outside click
window.addEventListener('click', function(event) {
    const modal = document.getElementById('feedbackDetailModal');
    if (event.target === modal) {
        closeFeedbackModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeFeedbackModal();
    }
});
</script>

<!-- Common Sidebar JS -->
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
