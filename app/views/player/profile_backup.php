<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/profile.css">

<?php 
// Safety check for user data
if (!isset($data['user']) || !is_object($data['user'])) {
    echo '<div class="alert alert-danger">Error: Unable to load user profile data. Please try again or contact support.</div>';
    exit;
}
?>
    
    <div class="player-layout">
        <!-- Simple Sidebar -->
        <div class="player-sidebar" id="playerSidebar">
            <div class="sidebar-header">
                <div class="player-logo">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Player Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player/profile" class="nav-link">
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
                <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
                <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Profile Header -->
            <div class="profile-header">
                <?php if ($data['formMode'] === 'add'): ?>
                    <h1><i class="fas fa-user-plus"></i> Add Profile Details</h1>
                    <p>Complete your profile to get started with Elite Cricket Academy</p>
                <?php else: ?>
                    <h1><i class="fas fa-user-cog"></i> My Profile</h1>
                    <p>Manage your personal information and account settings</p>
                <?php endif; ?>
            </div>

            <?php flash('profile_message'); ?>

            <!-- Profile Form -->
            <div class="profile-form">
                <?php 
                $formAction = ($data['formMode'] === 'add') ? 'addProfile' : 'updateProfile';
                $buttonText = ($data['formMode'] === 'add') ? 'Add Profile Details' : 'Update Profile';
                $buttonIcon = ($data['formMode'] === 'add') ? 'fas fa-plus' : 'fas fa-save';
                ?>
                <form action="<?php echo URLROOT; ?>/player/<?php echo $formAction; ?>" method="POST" id="profileForm">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i> Basic Information <?php echo ($data['formMode'] === 'add') ? '(Required)' : ''; ?>
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Name ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Email ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number <?php echo ($data['formMode'] === 'add') ? '*' : ''; ?></label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->PhoneNumber ?? ''); ?>"
                                           <?php echo ($data['formMode'] === 'add') ? 'required' : ''; ?>>
                                    <?php if ($data['formMode'] === 'add'): ?>
                                        <small class="form-text text-danger">* Required field</small>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->DateOfBirth ?? ''); ?>" readonly>
                                    <small class="form-text">Contact admin to change date of birth</small>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="address">Address <?php echo ($data['formMode'] === 'add') ? '*' : ''; ?></label>
                                    <textarea id="address" name="address" class="form-control" rows="3" 
                                              <?php echo ($data['formMode'] === 'add') ? 'required' : ''; ?>><?php echo htmlspecialchars($data['user']->Address ?? ''); ?></textarea>
                                    <?php if ($data['formMode'] === 'add'): ?>
                                        <small class="form-text text-danger">* Required field</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="school">School/Institution</label>
                                    <input type="text" id="school" name="school" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->School ?? $data['user']->SchoolInstitution ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <input type="text" id="role" name="role" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->Role ?? ''); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Player-Specific Information (only show for players) -->
                        <?php if (isset($data['user']) && is_object($data['user']) && isset($data['user']->Role) && $data['user']->Role === 'Player'): ?>
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-baseball-ball"></i> Cricket Profile
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="batting_style">Batting Style</label>
                                    <select id="batting_style" name="batting_style" class="form-control">
                                        <option value="">Select batting style</option>
                                        <option value="Right-handed" <?php echo ($data['user']->BattingStyle ?? '') === 'Right-handed' ? 'selected' : ''; ?>>Right-handed</option>
                                        <option value="Left-handed" <?php echo ($data['user']->BattingStyle ?? '') === 'Left-handed' ? 'selected' : ''; ?>>Left-handed</option>
                                        <option value="Switch-hitter" <?php echo ($data['user']->BattingStyle ?? '') === 'Switch-hitter' ? 'selected' : ''; ?>>Switch-hitter</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bowling_style">Bowling Style</label>
                                    <select id="bowling_style" name="bowling_style" class="form-control">
                                        <option value="">Select bowling style</option>
                                        <option value="Fast" <?php echo ($data['user']->BowlingStyle ?? '') === 'Fast' ? 'selected' : ''; ?>>Fast</option>
                                        <option value="Medium" <?php echo ($data['user']->BowlingStyle ?? '') === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                        <option value="Spin" <?php echo ($data['user']->BowlingStyle ?? '') === 'Spin' ? 'selected' : ''; ?>>Spin</option>
                                        <option value="Off-spin" <?php echo ($data['user']->BowlingStyle ?? '') === 'Off-spin' ? 'selected' : ''; ?>>Off-spin</option>
                                        <option value="Leg-spin" <?php echo ($data['user']->BowlingStyle ?? '') === 'Leg-spin' ? 'selected' : ''; ?>>Leg-spin</option>
                                        <option value="None" <?php echo ($data['user']->BowlingStyle ?? '') === 'None' ? 'selected' : ''; ?>>None (Wicket Keeper/Batsman only)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="jersey_number">Jersey Number</label>
                                    <input type="number" id="jersey_number" name="jersey_number" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->JerseyNumber ?? ''); ?>" min="1" max="99">
                                </div>
                                <div class="form-group">
                                    <label for="subscription_type">Subscription Type</label>
                                    <input type="text" id="subscription_type" name="subscription_type" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->SubscriptionType ?? 'Basic'); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-phone-alt"></i> Emergency Contacts
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Emergency Contact Name</label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->EmergencyContactName ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                    <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->EmergencyContactPhone ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="parent_guardian_name">Parent/Guardian Name</label>
                                    <input type="text" id="parent_guardian_name" name="parent_guardian_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->ParentGuardianName ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="parent_guardian_phone">Parent/Guardian Phone</label>
                                    <input type="tel" id="parent_guardian_phone" name="parent_guardian_phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->ParentGuardianPhone ?? ''); ?>">
                                </div>
                            </div>
                            
                            <!-- Additional Player Information (for players only) -->
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="previous_experience">Previous Cricket Experience</label>
                                    <textarea id="previous_experience" name="previous_experience" class="form-control" rows="3"
                                              placeholder="Describe any previous cricket experience, clubs played for, achievements, etc."><?php echo htmlspecialchars($data['user']->PreviousExperience ?? ''); ?></textarea>
                                    <small class="form-text">Optional: Help us understand your cricket background</small>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="medical_conditions">Medical Conditions & Allergies</label>
                                    <textarea id="medical_conditions" name="medical_conditions" class="form-control" rows="3"
                                              placeholder="List any medical conditions, allergies, or medications we should be aware of"><?php echo htmlspecialchars($data['user']->MedicalConditions ?? ''); ?></textarea>
                                    <small class="form-text">Important: This information helps us provide better care and training</small>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="school_institution">School/Institution</label>
                                    <input type="text" id="school_institution" name="school_institution" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->SchoolInstitution ?? $data['user']->School ?? ''); ?>"
                                           placeholder="Current school or educational institution">
                                </div>
                                <div class="form-group">
                                    <label for="how_heard_about_us">How did you hear about us?</label>
                                    <select id="how_heard_about_us" name="how_heard_about_us" class="form-control">
                                        <option value="">Select option</option>
                                        <option value="Friend/Family" <?php echo ($data['user']->HowHeardAboutUs ?? '') === 'Friend/Family' ? 'selected' : ''; ?>>Friend/Family</option>
                                        <option value="Social Media" <?php echo ($data['user']->HowHeardAboutUs ?? '') === 'Social Media' ? 'selected' : ''; ?>>Social Media</option>
                                        <option value="Google Search" <?php echo ($data['user']->HowHeardAboutUs ?? '') === 'Google Search' ? 'selected' : ''; ?>>Google Search</option>
                                        <option value="Advertisement" <?php echo ($data['user']->HowHeardAboutUs ?? '') === 'Advertisement' ? 'selected' : ''; ?>>Advertisement</option>
                                        <option value="School" <?php echo ($data['user']->HowHeardAboutUs ?? '') === 'School' ? 'selected' : ''; ?>>School</option>
                                        <option value="Other" <?php echo ($data['user']->HowHeardAboutUs ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Coach-Specific Information (only show for coaches) -->
                        <?php if (isset($data['user']) && is_object($data['user']) && isset($data['user']->Role) && $data['user']->Role === 'Coach'): ?>
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-chalkboard-teacher"></i> Coaching Profile
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="coach_specialization">Specialization</label>
                                    <select id="coach_specialization" name="specialization" class="form-control">
                                        <option value="">Select specialization</option>
                                        <option value="Batting" <?php echo ($data['user']->CoachSpecialization ?? '') === 'Batting' ? 'selected' : ''; ?>>Batting</option>
                                        <option value="Bowling" <?php echo ($data['user']->CoachSpecialization ?? '') === 'Bowling' ? 'selected' : ''; ?>>Bowling</option>
                                        <option value="All-rounder" <?php echo ($data['user']->CoachSpecialization ?? '') === 'All-rounder' ? 'selected' : ''; ?>>All-rounder</option>
                                        <option value="Wicket-keeping" <?php echo ($data['user']->CoachSpecialization ?? '') === 'Wicket-keeping' ? 'selected' : ''; ?>>Wicket-keeping</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="coach_experience">Years of Experience</label>
                                    <input type="number" id="coach_experience" name="experience" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->CoachExperience ?? '0'); ?>" min="0" max="50">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="coach_certifications">Certifications & Qualifications</label>
                                    <textarea id="coach_certifications" name="certifications" class="form-control" rows="3"
                                              placeholder="List your coaching certifications, qualifications, and achievements"><?php echo htmlspecialchars($data['user']->CoachCertifications ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="is_head_coach">Head Coach Status</label>
                                    <input type="text" id="is_head_coach" name="is_head_coach" class="form-control" 
                                           value="<?php echo ($data['user']->IsHeadCoach ?? false) ? 'Yes' : 'No'; ?>" readonly>
                                    <small class="form-text">Head coach status is assigned by administration</small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Trainer-Specific Information (only show for trainers) -->
                        <?php if (isset($data['user']) && is_object($data['user']) && isset($data['user']->Role) && $data['user']->Role === 'Trainer'): ?>
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-dumbbell"></i> Trainer Profile
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="trainer_experience">Years of Experience</label>
                                    <input type="number" id="trainer_experience" name="experience" class="form-control" 
                                           value="<?php echo htmlspecialchars($data['user']->TrainerExperience ?? '0'); ?>" min="0" max="50">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="trainer_certifications">Certifications & Qualifications</label>
                                    <textarea id="trainer_certifications" name="certifications" class="form-control" rows="3"
                                              placeholder="List your fitness/training certifications, qualifications, and specialties"><?php echo htmlspecialchars($data['user']->TrainerCertifications ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Shop Employee-Specific Information (only show for shop employees) -->
                        <?php if (isset($data['user']) && is_object($data['user']) && isset($data['user']->Role) && $data['user']->Role === 'ShopEmployee'): ?>
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-store"></i> Shop Employee Profile
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shop_department">Department</label>
                                    <select id="shop_department" name="department" class="form-control">
                                        <option value="General" <?php echo ($data['user']->ShopDepartment ?? '') === 'General' ? 'selected' : ''; ?>>General</option>
                                        <option value="Equipment" <?php echo ($data['user']->ShopDepartment ?? '') === 'Equipment' ? 'selected' : ''; ?>>Equipment</option>
                                        <option value="Facility" <?php echo ($data['user']->ShopDepartment ?? '') === 'Facility' ? 'selected' : ''; ?>>Facility</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="shop_hire_date">Hire Date</label>
                                    <input type="text" id="shop_hire_date" name="shop_hire_date" class="form-control" 
                                           value="<?php echo $data['user']->ShopHireDate ? date('M j, Y', strtotime($data['user']->ShopHireDate)) : 'Not Set'; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Admin-Specific Information (only show for admins) -->
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
                                <i class="<?php echo $buttonIcon; ?>"></i> <?php echo $buttonText; ?>
                            </button>
                            <a href="<?php echo URLROOT; ?>/player" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <?php if ($data['formMode'] === 'update'): ?>
                            <button type="button" class="btn btn-danger" onclick="confirmDeactivation()">
                                <i class="fas fa-user-slash"></i> Deactivate Account
                            </button>
                            <?php else: ?>
                            <div class="form-help">
                                <i class="fas fa-info-circle"></i>
                                <small>Complete all required fields to create your profile</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div> <!-- End profile-form -->
            </div>
        </div>
    </div>

    <?php if ($data['formMode'] === 'update'): ?>
    <!-- Deactivation Confirmation Modal -->
    <div id="deactivationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Account Deactivation</h3>
                <span class="close" onclick="closeDeactivationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>Warning:</strong> This action will deactivate your account permanently.</p>
                <p>You will:</p>
                <ul>
                    <li>Lose access to all academy services</li>
                    <li>Be logged out immediately</li>
                    <li>Need to contact administrators to reactivate</li>
                </ul>
                <p>Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="<?php echo URLROOT; ?>/player/deactivateAccount" style="display: inline;">
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

    <?php endif; ?>

    <script src="<?php echo URLROOT; ?>/js/player/profile-backup.js"></script>

    <!-- Include Sidebar JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>

    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>