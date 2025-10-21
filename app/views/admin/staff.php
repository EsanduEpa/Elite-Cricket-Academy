<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/staff-management.css">

    <!-- Admin Dashboard Layout -->
    <!-- Admin Dashboard Layout -->
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
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>Staff Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#player-management" class="nav-link">
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
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Feedback Monitoring</span>
                            <span class="badge">12</span>
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
        
        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-users-cog"></i> Staff Management</h1>
                        <p>Manage coaches, trainers, and administrative staff</p>
                    </div>
                    <div class="header-actions">
                        <div class="current-time" id="currentTime"></div>
                    </div>
                </div>
            </div>

            <!-- Staff Management Content -->
            <div class="content-wrapper">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card coaches">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-details">
                            <h3>12</h3>
                            <p>Total Coaches</p>
                            <span class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 2 this month
                            </span>
                        </div>
                    </div>

                    <div class="stat-card trainers">
                        <div class="stat-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="stat-details">
                            <h3>5</h3>
                            <p>Trainers</p>
                            <span class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 1 this month
                            </span>
                        </div>
                    </div>

                    <div class="stat-card admins">
                        <div class="stat-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-details">
                            <h3>3</h3>
                            <p>Administrators</p>
                            <span class="stat-change neutral">
                                <i class="fas fa-minus"></i> No change
                            </span>
                        </div>
                    </div>

                    <div class="stat-card shopkeepers">
                        <div class="stat-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-details">
                            <h3>2</h3>
                            <p>Shop Staff</p>
                            <span class="stat-change neutral">
                                <i class="fas fa-minus"></i> No change
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Role Requests Section -->
                <div class="role-requests-section" id="roleRequestsSection" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-user-clock"></i> Pending Role Requests</h2>
                        <span class="badge-count" id="requestCount">0</span>
                    </div>
                    <div class="role-requests-table-wrapper">
                        <table class="role-requests-table" id="roleRequestsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Requested Role</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="roleRequestsTableBody">
                                <!-- Role request rows will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="filters-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="staffSearch" placeholder="Search by name, email, or role...">
                    </div>
                    <div class="filter-options">
                        <select id="roleFilter" class="filter-select">
                            <option value="all">All Roles</option>
                            <option value="coach">Coaches</option>
                            <option value="head_coach">Head Coaches</option>
                            <option value="trainer">Trainers</option>
                            <option value="admin">Administrators</option>
                            <option value="shopkeeper">Shop Staff</option>
                        </select>
                        <select id="statusFilter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button class="btn-secondary" id="exportBtn">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Staff Table -->
                <div class="staff-table-section">
                    <table class="staff-table" id="staffTable">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Staff Member</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="staffTableBody">
                            <!-- Staff rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-section">
                    <div class="pagination-info">
                        Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalStaff">22</span> staff members
                    </div>
                    <div class="pagination-controls">
                        <button class="pagination-btn" id="prevPage">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn">2</button>
                        <button class="pagination-btn">3</button>
                        <button class="pagination-btn" id="nextPage">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Add Staff Action Button -->
                <div class="add-staff-action">
                    <button class="btn btn-primary btn-large" id="addStaffBtn">
                        <i class="fas fa-user-plus"></i> Add New Staff Member
                    </button>
                    <p class="action-hint">Click here to add a new coach, trainer, or staff member to the academy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Staff Wizard Modal -->
    <div class="modal wizard-modal" id="addStaffModal">
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-content wizard-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Add New Staff Member</h2>
            </div>
            
            <!-- Wizard Progress -->
            <div class="wizard-progress">
                <div class="wizard-step active" data-step="1">
                    <div class="step-circle">1</div>
                    <span class="step-label">Staff Information</span>
                </div>
                <div class="wizard-line"></div>
                <div class="wizard-step" data-step="2">
                    <div class="step-circle">2</div>
                    <span class="step-label">Review & Confirm</span>
                </div>
            </div>
            
            <div class="modal-body">
                <form id="addStaffForm">
                    <!-- Step 1: Staff Information (Combined) -->
                    <div class="wizard-step-content active" data-step="1">
                        <h3><i class="fas fa-user-plus"></i> Staff Information</h3>
                        <p class="step-description">Enter all details about the new staff member</p>
                        
                        <!-- Personal Details -->
                        <div class="form-section">
                            <h4><i class="fas fa-id-card"></i> Personal Details</h4>
                            <div class="form-group">
                                <label for="fullName">
                                    <i class="fas fa-user"></i> Full Name <span class="required">*</span>
                                </label>
                                <input type="text" id="fullName" name="fullName" required 
                                       placeholder="Enter full name">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dateOfBirth">
                                        <i class="fas fa-birthday-cake"></i> Date of Birth <span class="required">*</span>
                                    </label>
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                                </div>
                                <div class="form-group">
                                    <label for="school">
                                        <i class="fas fa-school"></i> School/Institution
                                    </label>
                                    <input type="text" id="school" name="school" 
                                           placeholder="e.g., Royal College, University of Colombo">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-section">
                            <h4><i class="fas fa-address-book"></i> Contact Information</h4>
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       placeholder="staff@example.com">
                                <small>This will be used for login and communication</small>
                            </div>

                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" required
                                       placeholder="+94 77 123 4567">
                            </div>

                            <div class="form-group">
                                <label for="address">
                                    <i class="fas fa-map-marker-alt"></i> Address
                                </label>
                                <textarea id="address" name="address" rows="2" 
                                          placeholder="Enter full residential address"></textarea>
                            </div>
                        </div>

                        <!-- Login Credentials -->
                        <div class="form-section">
                            <h4><i class="fas fa-key"></i> Login Credentials</h4>
                            <div class="form-group">
                                <label for="username">
                                    <i class="fas fa-user-circle"></i> Username <span class="required">*</span>
                                </label>
                                <input type="text" id="username" name="username" required
                                       placeholder="Enter username for login">
                                <small>Username must be unique</small>
                            </div>

                            <div class="info-box">
                                <i class="fas fa-info-circle"></i>
                                <p><strong>Default Password:</strong> The system will set the default password as <code>staff123456</code>. The staff member should change this upon first login.</p>
                            </div>
                        </div>

                        <!-- Role & Position -->
                        <div class="form-section">
                            <h4><i class="fas fa-user-tag"></i> Role & Position</h4>
                            <div class="form-group">
                                <label for="role">
                                    <i class="fas fa-user-tag"></i> Role <span class="required">*</span>
                                </label>
                                <select id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="Coach">Coach</option>
                                    <option value="Trainer">Trainer</option>
                                    <option value="Admin">Administrator</option>
                                    <option value="ShopEmployee">Shop Employee</option>
                                </select>
                                <small>Select the primary role for this staff member</small>
                            </div>

                            <div class="form-group">
                                <label for="notes">
                                    <i class="fas fa-sticky-note"></i> Notes (Optional)
                                </label>
                                <textarea id="notes" name="notes" rows="2"
                                          placeholder="Any additional notes about this staff member"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Review & Confirm -->
                    <div class="wizard-step-content" data-step="2">
                        <h3><i class="fas fa-check-circle"></i> Review & Confirm</h3>
                        <p class="step-description">Review all information before adding</p>
                        
                        <div class="review-section">
                            <h4><i class="fas fa-user"></i> Personal Information</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Full Name:</span>
                                    <span class="review-value" id="reviewFullName">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Date of Birth:</span>
                                    <span class="review-value" id="reviewDOB">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">School/Institution:</span>
                                    <span class="review-value" id="reviewSchool">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <h4><i class="fas fa-address-book"></i> Contact Information</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Email:</span>
                                    <span class="review-value" id="reviewEmail">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Phone:</span>
                                    <span class="review-value" id="reviewPhone">-</span>
                                </div>
                                <div class="review-item full-width">
                                    <span class="review-label">Address:</span>
                                    <span class="review-value" id="reviewAddress">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <h4><i class="fas fa-key"></i> Login Credentials</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Username:</span>
                                    <span class="review-value" id="reviewUsername">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Default Password:</span>
                                    <span class="review-value"><code>staff123456</code></span>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <h4><i class="fas fa-briefcase"></i> Role & Position</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Role:</span>
                                    <span class="review-value" id="reviewRole">-</span>
                                </div>
                                <div class="review-item full-width">
                                    <span class="review-label">Notes:</span>
                                    <span class="review-value" id="reviewNotes">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" id="sendEmail" name="sendEmail" checked>
                                <span>Send account credentials via email</span>
                            </label>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <p>The default password <code>staff123456</code> will be set. The staff member should change this password upon first login. If "Send via email" is checked, the credentials will be emailed to the staff member.</p>
                        </div>
                    </div>

                    <div class="wizard-footer">
                        <button type="button" class="btn-secondary" id="wizardPrevBtn" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="button" class="btn-primary" id="wizardNextBtn">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-primary" id="wizardSubmitBtn" style="display: none;">
                            <i class="fas fa-save"></i> Add Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal" id="editStaffModal">
        <div class="modal-overlay" id="editModalOverlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Staff Member</h2>
                <button class="modal-close" id="closeEditModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editStaffForm">
                    <input type="hidden" id="editStaffId" name="staffId">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editFirstName">
                                <i class="fas fa-user"></i> First Name <span class="required">*</span>
                            </label>
                            <input type="text" id="editFirstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="editLastName">
                                <i class="fas fa-user"></i> Last Name <span class="required">*</span>
                            </label>
                            <input type="text" id="editLastName" name="lastName" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="editEmail">
                                <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                            </label>
                            <input type="email" id="editEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="editPhone">
                                <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" id="editPhone" name="phone" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="editRole">
                                <i class="fas fa-user-tag"></i> Role <span class="required">*</span>
                            </label>
                            <select id="editRole" name="role" required>
                                <option value="">Select Role</option>
                                <option value="coach">Coach</option>
                                <option value="head_coach">Head Coach</option>
                                <option value="trainer">Trainer</option>
                                <option value="admin">Administrator</option>
                                <option value="shopkeeper">Shop Staff</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editStatus">
                                <i class="fas fa-toggle-on"></i> Status <span class="required">*</span>
                            </label>
                            <select id="editStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editSpecialization">
                            <i class="fas fa-certificate"></i> Specialization
                        </label>
                        <input type="text" id="editSpecialization" name="specialization" 
                               placeholder="e.g., Batting Coach, Bowling Coach, Fitness Trainer">
                    </div>

                    <div class="form-group">
                        <label for="editAddress">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <textarea id="editAddress" name="address" rows="3" 
                                  placeholder="Enter full address"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelEditBtn">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteStaffModal">
        <div class="modal-overlay" id="deleteModalOverlay"></div>
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
                <button class="modal-close" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove <strong id="deleteStaffName"></strong> from the system?</p>
                <p class="warning-text">This action cannot be undone. The staff member will lose access to their account.</p>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelDeleteBtn">Cancel</button>
                    <button type="button" class="btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash"></i> Delete Staff Member
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript for Staff Management -->
    <script>
        // Define URLROOT for JavaScript
        const URLROOT = '<?php echo URLROOT; ?>';
    </script>
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/staff-management.js"></script>
    
    <script>
        // Button click handler - similar to events.php
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 Staff Management - Initializing button handlers...');
            
            const addStaffBtn = document.getElementById('addStaffBtn');
            const modal = document.getElementById('addStaffModal');
            
            if (addStaffBtn) {
                addStaffBtn.addEventListener('click', function() {
                    console.log('🎯 Add Staff button clicked!');
                    
                    if (modal) {
                        modal.classList.add('active');
                        modal.style.display = 'flex';
                        
                        // Reset form
                        const form = document.getElementById('addStaffForm');
                        if (form) form.reset();
                        
                        // Set join date to today
                        const joinDateInput = document.getElementById('joinDate');
                        if (joinDateInput) joinDateInput.valueAsDate = new Date();
                        
                        // Go to step 1
                        if (typeof goToWizardStep === 'function') {
                            goToWizardStep(1);
                        }
                        
                        console.log('✅ Modal opened successfully!');
                    } else {
                        console.error('❌ Modal not found!');
                    }
                });
                console.log('✅ Button handler attached!');
            } else {
                console.error('❌ Add Staff button not found!');
            }
        });
    </script>
</body>
</html>
