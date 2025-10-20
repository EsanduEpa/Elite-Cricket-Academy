<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/profile.css">

<?php 
// Safety check for user data
if (!isset($data['user']) || !is_object($data['user'])) {
    echo '<div class="alert alert-danger">Error: Unable to load user profile data. Please try again or contact support.</div>';
    exit;
}
?>
    
    <div class="admin-layout">
        <!-- Simple Sidebar -->
        <div class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <i class="fas fa-user-shield"></i>
                    <h3>Admin Dashboard</h3>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Events Management</span>
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
                    <span class="admin-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?></span>
                    <span class="admin-role">Administrator</span>
                </div>
                <div class="profile-actions">
                    <a href="<?php echo URLROOT; ?>/admin/profile" class="profile-btn" title="Profile">
                        <i class="fas fa-user-cog"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Profile Header -->
            <div class="profile-header">
                <h1><i class="fas fa-user-cog"></i> Admin Profile</h1>
                <p>Manage your administrative account information and settings</p>
            </div>

            <?php flash('profile_message'); ?>

            <!-- Profile Form -->
            <div class="profile-form">
                <form action="<?php echo URLROOT; ?>/admin/updateProfile" method="POST" id="profileForm">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i> Basic Information
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Name ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Email ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->PhoneNumber ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->DateOfBirth ?? ''); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($data['user']->Address ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="school">School/Institution</label>
                                    <input type="text" id="school" name="school" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->School ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <input type="text" id="role" name="role" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Role ?? ''); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Admin-Specific Information -->
                        <?php if (isset($data['user']) && is_object($data['user']) && isset($data['user']->Role) && $data['user']->Role === 'Admin'): ?>
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user-shield"></i> Admin Profile
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="admin_level">Admin Level</label>
                                    <input type="text" id="admin_level" name="admin_level" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->AdminLevel ?? 'system_admin'); ?>" readonly>
                                    <small class="form-text">Admin level is assigned by super administrators</small>
                                </div>
                                <div class="form-group">
                                    <label for="admin_department">Department</label>
                                    <select id="admin_department" name="department" class="form-control">
                                        <option value="General" <?php echo ($data['user']->AdminDepartment ?? '') === 'General' ? 'selected' : ''; ?>>General</option>
                                        <option value="Management" <?php echo ($data['user']->AdminDepartment ?? '') === 'Management' ? 'selected' : ''; ?>>Management</option>
                                        <option value="Operations" <?php echo ($data['user']->AdminDepartment ?? '') === 'Operations' ? 'selected' : ''; ?>>Operations</option>
                                        <option value="Finance" <?php echo ($data['user']->AdminDepartment ?? '') === 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                        <option value="Technical" <?php echo ($data['user']->AdminDepartment ?? '') === 'Technical' ? 'selected' : ''; ?>>Technical</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="admin_security_clearance">Security Clearance</label>
                                    <input type="text" id="admin_security_clearance" name="admin_security_clearance" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->SecurityClearance ?? 'Level1'); ?>" readonly>
                                    <small class="form-text">Security clearance is managed by system administrators</small>
                                </div>
                                <div class="form-group">
                                    <label for="admin_hire_date">Hire Date</label>
                                    <input type="text" id="admin_hire_date" name="admin_hire_date" class="form-control" 
                                           value="<?php echo $data['user']->AdminHireDate ? date('M j, Y', strtotime($data['user']->AdminHireDate)) : 'Not Set'; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Account Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-shield-alt"></i> Account Information
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Username ?? ''); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="status">Account Status</label>
                                    <input type="text" id="status" name="status" class="form-control" 
                                           value="<?php echo ucfirst($data['user']->Status ?? 'active'); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_joined">Date Joined</label>
                                    <input type="text" id="date_joined" name="date_joined" class="form-control" 
                                           value="<?php echo date('M j, Y', strtotime($data['user']->DateJoined ?? 'now')); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="last_login">Last Login</label>
                                    <input type="text" id="last_login" name="last_login" class="form-control" 
                                           value="<?php echo $data['user']->LastLoginAt ? date('M j, Y g:i A', strtotime($data['user']->LastLoginAt)) : 'Never'; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDeactivation()">
                                <i class="fas fa-user-slash"></i> Deactivate Account
                            </button>
                        </div>
                    </form>
                </div> <!-- End profile-form -->
            </div>
        </div>
    </div>

    <!-- Deactivation Confirmation Modal -->
    <div id="deactivationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Account Deactivation</h3>
                <span class="close" onclick="closeDeactivationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>Warning:</strong> This action will deactivate your admin account permanently.</p>
                <p>You will:</p>
                <ul>
                    <li>Lose access to all admin services</li>
                    <li>Be logged out immediately</li>
                    <li>Need to contact super administrators to reactivate</li>
                </ul>
                <p>Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="<?php echo URLROOT; ?>/admin/deactivateAccount" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-slash"></i> Yes, Deactivate My Account
                    </button>
                </form>
                <button type="button" class="btn btn-secondary" onclick="closeDeactivationModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmDeactivation() {
            document.getElementById('deactivationModal').style.display = 'block';
        }

        function closeDeactivationModal() {
            document.getElementById('deactivationModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deactivationModal');
            if (event.target === modal) {
                closeDeactivationModal();
            }
        }
    </script>

    <!-- Include Sidebar JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>