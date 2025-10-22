<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-requests.css">

    <!-- Coach Dashboard Layout -->
    <div class="coach-layout">
        <!-- Left Sidebar Panel -->
        <div class="coach-sidebar" id="coachSidebar">
            <div class="sidebar-header">
                <div class="coach-logo">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Coach Panel</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                            <i class="fas fa-comments"></i>
                            <span>Communication</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/reports" class="nav-link" data-tooltip="Reports">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Requests</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1>
                            <i class="fas fa-clipboard-list"></i>
                            Requests & Approvals
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Manage player requests and session changes</p>
                    </div>
                    <div class="header-actions">
                        <div class="stats-badge">
                            <span class="badge-label">Pending:</span>
                            <span class="badge-value" id="pendingCount">5</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requests Content -->
            <div class="requests-content">
                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="tab-btn active" data-filter="pending">
                        <i class="fas fa-clock"></i>
                        Pending <span class="count" id="pendingTabCount">5</span>
                    </button>
                    <button class="tab-btn" data-filter="approved">
                        <i class="fas fa-check-circle"></i>
                        Approved <span class="count" id="approvedTabCount">12</span>
                    </button>
                    <button class="tab-btn" data-filter="rejected">
                        <i class="fas fa-times-circle"></i>
                        Rejected <span class="count" id="rejectedTabCount">3</span>
                    </button>
                    <button class="tab-btn" data-filter="all">
                        <i class="fas fa-list"></i>
                        All Requests
                    </button>
                </div>

                <!-- Request Type Filter -->
                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Type:</label>
                        <select id="typeFilter" class="filter-select">
                            <option value="all">All Types</option>
                            <option value="reschedule">Reschedule</option>
                            <option value="cancellation">Cancellation</option>
                            <option value="appointment">Appointment</option>
                            <option value="leave">Leave Request</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Sort By:</label>
                        <select id="sortFilter" class="filter-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="urgent">Urgent First</option>
                        </select>
                    </div>
                    
                    <div class="filter-group search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchRequests" placeholder="Search by player name...">
                    </div>
                </div>

                <!-- Requests List -->
                <div class="requests-list" id="requestsList">
                    <!-- Requests will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div class="modal" id="requestModal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2><i class="fas fa-clipboard-list"></i> Request Details</h2>
                <button class="modal-close" id="closeRequestModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="requestDetails">
                    <!-- Request details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer" id="modalActions">
                <!-- Action buttons will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal" id="approvalModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-check-circle"></i> Approve Request</h2>
                <button class="modal-close" id="closeApprovalModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="approvalForm">
                    <input type="hidden" id="approveRequestId">
                    
                    <div class="form-group">
                        <label for="approvalNotes">Approval Notes (Optional)</label>
                        <textarea id="approvalNotes" rows="4" placeholder="Add any notes or conditions for this approval..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="notifyPlayer" checked>
                            Notify player via email
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelApproval">Cancel</button>
                        <button type="submit" class="btn-success">
                            <i class="fas fa-check"></i>
                            Approve Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal" id="rejectionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-times-circle"></i> Reject Request</h2>
                <button class="modal-close" id="closeRejectionModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm">
                    <input type="hidden" id="rejectRequestId">
                    
                    <div class="form-group">
                        <label for="rejectionReason">Reason for Rejection *</label>
                        <select id="rejectionReason" required>
                            <option value="">Select reason...</option>
                            <option value="scheduling_conflict">Scheduling Conflict</option>
                            <option value="insufficient_notice">Insufficient Notice</option>
                            <option value="session_full">Session Already Full</option>
                            <option value="policy_violation">Policy Violation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rejectionNotes">Additional Notes *</label>
                        <textarea id="rejectionNotes" rows="4" placeholder="Explain why this request is being rejected..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="notifyPlayerReject" checked>
                            Notify player via email
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelRejection">Cancel</button>
                        <button type="submit" class="btn-danger">
                            <i class="fas fa-times"></i>
                            Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach-requests.js"></script>

<script>
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
