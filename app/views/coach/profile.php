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
                                        <option value="All-rounder" <?php echo (isset($data['user']->CoachSpecialization) && $data['user']->CoachSpecialization == 'All-rounder') ? 'selected' : ''; ?>>⭐ All-rounder</option>
                                        <option value="Wicket-keeping" <?php echo (isset($data['user']->CoachSpecialization) && $data['user']->CoachSpecialization == 'Wicket-keeping') ? 'selected' : ''; ?>>🧤 Wicket-keeping</option>
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
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo URLROOT; ?>/coach/dashboard'">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach/dashboard.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
