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
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link">
                        <i class="fas fa-clock"></i>
                        <span>Slot Management</span>
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
                    <div class="stat-number"><?php echo $data['feedbackStats']['reviewed']; ?></div>
                    <div class="stat-label">Reviewed</div>
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
                <i class="fas fa-star"></i>
                <span class="stat-value"><?php 
                    $avgRating = 0;
                    $ratedCount = 0;
                    foreach($data['allFeedbacks'] as $f) {
                        if(isset($f['rating']) && $f['rating'] > 0) {
                            $avgRating += $f['rating'];
                            $ratedCount++;
                        }
                    }
                    echo $ratedCount > 0 ? number_format($avgRating / $ratedCount, 1) : 'N/A';
                ?></span>
                <span class="stat-text">Avg Rating</span>
            </div>
            <div class="quick-stat">
                <i class="fas fa-comments"></i>
                <span class="stat-value"><?php 
                    $byCategory = array_count_values(array_column($data['allFeedbacks'], 'subject'));
                    echo !empty($byCategory) ? max($byCategory) : 0;
                ?></span>
                <span class="stat-text">Most Common</span>
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
                <button class="filter-tab" data-status="reviewed">
                    <i class="fas fa-spinner"></i> Reviewed
                    <span class="tab-count"><?php echo $data['feedbackStats']['reviewed']; ?></span>
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
                    <option value="coach">Coach</option>
                    <option value="trainer">Trainer</option>
                    <option value="facility">Facility</option>
                    <option value="equipment">Equipment</option>
                    <option value="shop">Shop</option>
                    <option value="general">General</option>
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
                            <th width="150">User</th>
                            <th width="120">Category</th>
                            <th>Message</th>
                            <th width="100">Rating</th>
                            <th width="100">Status</th>
                            <th width="120">Date</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['allFeedbacks'])): ?>
                            <?php foreach ($data['allFeedbacks'] as $feedback): ?>
                        <tr class="feedback-row" data-status="<?php echo $feedback['status']; ?>" data-priority="<?php echo $feedback['priority']; ?>" data-category="<?php echo $feedback['subject'] ?? 'general'; ?>" data-feedback-id="<?php echo $feedback['id']; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($feedback['user_name'] ?? 'Unknown'); ?></strong>
                            </td>
                            <td>
                                <span class="category-badge"><?php echo ucfirst($feedback['subject'] ?? 'General'); ?></span>
                            </td>
                            <td>
                                <div class="message-preview"><?php echo htmlspecialchars(substr($feedback['message'], 0, 100)) . (strlen($feedback['message']) > 100 ? '...' : ''); ?></div>
                            </td>
                            <td style="text-align: center;">
                                <?php if($feedback['rating']): ?>
                                <span class="rating-inline">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'filled' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <?php else: ?>
                                <span style="color: #999;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $feedback['status']; ?>">
                                    <?php echo ucfirst($feedback['status']); ?>
                                </span>
                            </td>
                            <td class="date-cell">
                                <small><?php echo date('M d, Y', strtotime($feedback['created_at'])); ?></small>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-action-table view" onclick="viewFeedback(<?php echo $feedback['id']; ?>)" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action-table delete" onclick="deleteFeedback(<?php echo $feedback['id']; ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 60px 20px;">
                                <i class="fas fa-inbox" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
                                <p style="font-size: 18px; color: #666; margin: 0;">No feedback found</p>
                                <p style="font-size: 14px; color: #999; margin: 10px 0 0 0;">Feedback from users will appear here</p>
                            </td>
                        </tr>
                        <?php endif; ?>
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
                                <button class="btn btn-info" onclick="updateFeedbackStatus('reviewed')">
                                    <i class="fas fa-eye"></i> Mark as Reviewed
                                </button>
                                <button class="btn btn-success" onclick="updateFeedbackStatus('resolved')">
                                    <i class="fas fa-check"></i> Mark as Resolved
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
        // Extract data from row cells matching actual table structure
        const cells = row.querySelectorAll('td');
        const userName = cells[0] ? cells[0].querySelector('strong')?.textContent || 'Unknown' : 'Unknown';
        const category = row.querySelector('.category-badge')?.textContent.trim() || 'General';
        const status = row.querySelector('.status-badge')?.textContent.trim() || 'Pending';
        const dateCell = row.querySelector('.date-cell small');
        const date = dateCell ? dateCell.textContent : '';
        
        // Populate modal
        document.getElementById('modalUserName').textContent = userName;
        const modalEmail = document.getElementById('modalUserEmail');
        if (modalEmail) modalEmail.textContent = '';
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalCategory').textContent = category;
        const modalPriority = document.getElementById('modalPriority');
        if (modalPriority) modalPriority.textContent = row.getAttribute('data-priority') || 'Normal';
        document.getElementById('modalStatus').textContent = status;
        document.getElementById('modalFeedbackId').textContent = `#${feedbackId}`;
        const modalSubject = document.getElementById('modalSubject');
        if (modalSubject) modalSubject.textContent = category;
        
        // Get full message (would normally come from AJAX call)
        const feedbackData = <?php echo json_encode($data['allFeedbacks']); ?>;
        const feedback = feedbackData.find(f => f.id == feedbackId);
        
        if (feedback) {
            document.getElementById('modalMessage').textContent = feedback.message;
            
            // Hide response section since we don't store responses in database
            const responseSection = document.getElementById('responseSection');
            if (responseSection) {
                responseSection.style.display = 'none';
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
    
    const response = document.getElementById('responseText') ? document.getElementById('responseText').value : '';
    
    if (confirm(`Are you sure you want to mark this feedback as ${status}?`)) {
        // Make AJAX call to update status
        fetch('<?php echo URLROOT; ?>/admin/updateFeedbackStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `feedback_id=${currentFeedbackId}&status=${status}&response=${encodeURIComponent(response)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Feedback status updated successfully!');
                closeFeedbackModal();
                location.reload();
            } else {
                alert('Error updating feedback: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error updating feedback');
        });
    }
}

function deleteFeedback(feedbackId) {
    if (confirm('Are you sure you want to delete this feedback? This action cannot be undone.')) {
        // Make AJAX call to delete feedback
        fetch('<?php echo URLROOT; ?>/admin/deleteFeedback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `feedback_id=${feedbackId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Feedback deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting feedback: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error deleting feedback');
        });
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
    
    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    // Clear filters
    const clearBtn = document.getElementById('clearFiltersBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            document.getElementById('feedbackSearch').value = '';
            if (categoryFilter) categoryFilter.value = 'all';
            filterTabs[0].click();
        });
    }
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
    const categoryEl = document.getElementById('categoryFilter');
    const category = categoryEl ? categoryEl.value : 'all';
    const rows = document.querySelectorAll('.feedback-row');
    
    rows.forEach(row => {
        let showRow = true;
        
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
