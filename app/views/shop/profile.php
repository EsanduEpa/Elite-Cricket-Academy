<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/profile.css">

<?php 
// Safety check for user data
if (!isset($data['user']) || !is_object($data['user'])) {
    echo '<div class="alert alert-danger">Error: Unable to load user profile data. Please try again or contact support.</div>';
    exit;
}
?>
    
    <div class="shop-layout">
        <!-- Simple Sidebar -->
        <div class="shop-sidebar" id="shopSidebar">
            <div class="sidebar-header">
                <div class="shop-logo">
                    <i class="fas fa-store"></i>
                    <h3>Shop Dashboard</h3>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/shop/dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/shop/products" class="nav-link">
                            <i class="fas fa-boxes"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/shop/inventory" class="nav-link">
                            <i class="fas fa-warehouse"></i>
                            <span>Inventory</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/shop/orders" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/shop/rentals" class="nav-link">
                            <i class="fas fa-handshake"></i>
                            <span>Rentals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                            <i class="fas fa-building"></i>
                            <span>Facilities</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/shop/profile" class="nav-link">
                            <i class="fas fa-user-cog"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Simple Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($data['user_name']) ? $data['user_name'] : 'Shop Employee'; ?></div>
                <div class="profile-role">Shop Manager</div>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Profile Header -->
            <div class="profile-header">
                <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                <p>Manage your personal information and employment details</p>
            </div>

            <?php flash('profile_message'); ?>

            <!-- Profile Image Section -->
            <div class="profile-image-section">
                <div class="image-container">
                    <div class="profile-image-wrapper" title="Click to upload new photo">
                        <?php 
                        $profileImage = $data['user']->ProfileImage ?? null;
                        $imageUrl = $profileImage ? URLROOT . '/' . $profileImage : URLROOT . '/images/default-avatar.png';
                        ?>
                        <img src="<?php echo $imageUrl; ?>" 
                             alt="Profile Image" 
                             id="profileImagePreview" 
                             class="profile-image"
                             onerror="this.src='<?php echo URLROOT; ?>/images/default-avatar.png'">
                        <div class="image-overlay">
                            <i class="fas fa-camera"></i>
                            <span>Click to Upload</span>
                        </div>
                    </div>
                    <div class="image-actions">
                        <input type="file" 
                               id="profileImageInput" 
                               accept="image/jpeg,image/jpg,image/png" 
                               style="display: none;">
                        <?php if ($profileImage): ?>
                        <button type="button" id="deleteImageBtn" class="btn-delete-small">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <?php endif; ?>
                    </div>
                    <div id="imageValidationInfo" class="image-info" style="display: none;">
                        <p><i class="fas fa-info-circle"></i> Accepted formats: JPG, JPEG, PNG</p>
                        <p><i class="fas fa-exclamation-circle"></i> Maximum size: 2MB</p>
                    </div>
                    <div id="uploadMessage" class="upload-message"></div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
                <form action="<?php echo URLROOT; ?>/shop/updateProfile" method="POST" id="profileForm">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i> Basic Information
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Name ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Email ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->PhoneNumber ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dateOfBirth"><i class="fas fa-calendar"></i> Date of Birth</label>
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->DateOfBirth ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                                <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($data['user']->Address ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Employment Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-briefcase"></i> Employment Information
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="department"><i class="fas fa-briefcase"></i> Department</label>
                                    <select id="department" name="department" class="form-control">
                                        <option value="Equipment" <?php echo (isset($data['user']->ShopDepartment) && $data['user']->ShopDepartment == 'Equipment') ? 'selected' : ''; ?>>🏏 Equipment</option>
                                        <option value="Facility" <?php echo (isset($data['user']->ShopDepartment) && $data['user']->ShopDepartment == 'Facility') ? 'selected' : ''; ?>>🏟️ Facility</option>
                                        <option value="General" <?php echo (isset($data['user']->ShopDepartment) && $data['user']->ShopDepartment == 'General') ? 'selected' : ''; ?>>📦 General</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-check"></i> Hire Date</label>
                                    <div class="info-text">
                                        <?php echo isset($data['user']->ShopHireDate) ? date('F d, Y', strtotime($data['user']->ShopHireDate)) : 'N/A'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i> Account Status
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Account Status</label>
                                    <div class="status-badge <?php echo (isset($data['user']->Status) && $data['user']->Status == 'active') ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo htmlspecialchars($data['user']->Status ?? 'Active'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Date Joined</label>
                                    <div class="info-text">
                                        <?php echo isset($data['user']->DateJoined) ? date('F d, Y', strtotime($data['user']->DateJoined)) : 'N/A'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo URLROOT; ?>/shop/dashboard'">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/shop/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/profile-image.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
