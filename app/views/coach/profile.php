<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/profile.css">

<?php 
// Safety check for user data
if (!isset($data['user']) || !is_object($data['user'])) {
    echo '<div class="alert alert-danger">Error: Unable to load user profile data. Please try again or contact support.</div>';
    exit;
}
?>
    
    <div class="coach-layout">
        <!-- Simple Sidebar -->
        <div class="coach-sidebar" id="coachSidebar">
            <div class="sidebar-header">
                <div class="coach-logo">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Coach Dashboard</h3>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Slot Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Fitness</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Communication</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/reports" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/profile" class="nav-link">
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
                <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Coach'; ?></div>
                <div class="profile-role">Coach</div>
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
                <p>Manage your personal information and coaching details</p>
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
                <form action="<?php echo URLROOT; ?>/coach/updateProfile" method="POST" id="profileForm">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i> Basic Information
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName"><i class="fas fa-user"></i> First Name</label>
                                    <input type="text" id="firstName" name="firstName" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->FirstName ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="lastName"><i class="fas fa-user"></i> Last Name</label>
                                    <input type="text" id="lastName" name="lastName" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->LastName ?? ''); ?>" required>
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
                                           value="<?php echo htmlspecialchars($data['user']->DateOfBirth ?? ''); ?>"
                                           max="<?php echo date('Y-m-d'); ?>">
                                    <small class="form-hint" style="display: block; font-size: 0.8rem; color: #666; margin-top: 0.25rem; font-style: italic;">Must be at least 16 years old</small>
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                                <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($data['user']->Address ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Coaching Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-chalkboard-teacher"></i> Coaching Information
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="specialization"><i class="fas fa-medal"></i> Specialization</label>
                                    <select id="specialization" name="specialization" class="form-control">
                                        <option value="">Select Specialization</option>
                                        <option value="Batting" <?php echo (isset($data['user']->CoachSpecialization) && $data['user']->CoachSpecialization == 'Batting') ? 'selected' : ''; ?>>🏏 Batting</option>
                                        <option value="Bowling" <?php echo (isset($data['user']->CoachSpecialization) && $data['user']->CoachSpecialization == 'Bowling') ? 'selected' : ''; ?>>🎯 Bowling</option>
                                        <option value="Fielding" <?php echo (isset($data['user']->CoachSpecialization) && $data['user']->CoachSpecialization == 'Fielding') ? 'selected' : ''; ?>>🧤 Fielding</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="experience"><i class="fas fa-chart-line"></i> Years of Experience</label>
                                    <input type="number" id="experience" name="experience" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->CoachExperience ?? ''); ?>" min="0" placeholder="e.g., 5">
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="certifications"><i class="fas fa-certificate"></i> Certifications</label>
                                <textarea id="certifications" name="certifications" class="form-control" rows="3" 
                                          placeholder="List your coaching certifications..."><?php echo htmlspecialchars($data['user']->CoachCertifications ?? ''); ?></textarea>
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
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/profile-image.js"></script>
<script>
// Date of Birth Validation
function validateDateOfBirth(dateOfBirth) {
    if (!dateOfBirth) return null;
    
    const birthDate = new Date(dateOfBirth);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    const adjustedAge = (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) 
        ? age - 1 : age;
    
    if (birthDate > today) {
        return 'Date of birth cannot be in the future';
    } else if (adjustedAge < 16) {
        return 'You must be at least 16 years old';
    } else if (adjustedAge > 100) {
        return 'Please enter a valid date of birth';
    }
    
    return null;
}

// Add real-time validation to date of birth field
document.addEventListener('DOMContentLoaded', function() {
    const dobField = document.getElementById('dateOfBirth');
    if (dobField) {
        dobField.addEventListener('change', function() {
            const error = validateDateOfBirth(this.value);
            const existingError = this.parentElement.querySelector('.dob-error');
            
            if (error) {
                this.style.borderColor = '#ef4444';
                if (!existingError) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'dob-error';
                    errorDiv.style.color = '#ef4444';
                    errorDiv.style.fontSize = '0.85rem';
                    errorDiv.style.marginTop = '0.25rem';
                    errorDiv.textContent = error;
                    this.parentElement.appendChild(errorDiv);
                } else {
                    existingError.textContent = error;
                }
            } else {
                this.style.borderColor = '';
                if (existingError) {
                    existingError.remove();
                }
            }
        });
        
        // Validate on form submission
        const profileForm = dobField.closest('form');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const error = validateDateOfBirth(dobField.value);
                if (error) {
                    e.preventDefault();
                    alert(error);
                    dobField.focus();
                }
            });
        }
    }
});
</script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
