<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/feedback.css">

<div class="admin-layout"
     data-feedback-update-url="<?php echo URLROOT; ?>/admin/updateFeedbackStatus"
     data-feedback-delete-url="<?php echo URLROOT; ?>/admin/deleteFeedback">
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
                        <span>Dashboard </span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Player Management</span>
                    </a>
                </li>
                
             <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link">
                <i class="fas fa-trophy"></i><span>Tournaments</span></a></li>

                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link">
                        <i class="fas fa-clock"></i>
                        <span>Slot Management</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Finances</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Admin Profile -->
        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/admin/profile" class="profile-avatar" aria-label="Open admin profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
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

<script type="application/json" id="feedbackData">
<?php echo json_encode($data['allFeedbacks'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
</script>

<!-- Common Sidebar JS -->
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/feedback.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
