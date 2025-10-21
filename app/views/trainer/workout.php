<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/injury-reports.css">

<!-- Trainer Layout -->
<div class="trainer-layout">
    <!-- Trainer Sidebar -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-dumbbell"></i>
                <h3>Trainer Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Player Bookings</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                        <i class="fas fa-user-injured"></i>
                        <span>Injury Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Supplement Plans</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Workout Plans</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrition Plans</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Trainer Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer'; ?></div>
            <div class="profile-role">Physical Trainer</div>
            <a href="<?php echo URLROOT; ?>/trainer/profile" class="action-btn" style="margin-top: 10px;">
                <i class="fas fa-user-cog"></i> Profile
            </a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-dumbbell"></i> Workout Plans</h1>
                <p>Create and manage customized workout plans for your players</p>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($data['workout_plans']); ?></span>
                    <span class="stat-label">Total Plans</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">
                        <?php 
                        $activePlans = 0;
                        foreach($data['workout_plans'] as $plan) {
                            if($plan->Status === 'active') {
                                $activePlans++;
                            }
                        }
                        echo $activePlans;
                        ?>
                    </span>
                    <span class="stat-label">Active Plans</span>
                </div>
            </div>
        </div>

        <?php flash('workout_message'); ?>

        <!-- Workout Plans Table -->
        <div class="schedule-card">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-dumbbell"></i> My Workout Plans</h2>
                </div>
                <button class="btn btn-primary" onclick="openAddWorkoutPlanModal()">
                    <i class="fas fa-plus"></i> Add Workout Plan
                </button>
            </div>
            <div class="card-content">
                <?php if (!empty($data['workout_plans'])): ?>
                    <div class="table-responsive">
                        <table class="dashboard-table" id="workoutPlansTable">
                            <thead>
                                <tr>
                                    <th>Created Date</th>
                                    <th>Player Name</th>
                                    <th>Workout Details</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Video URL</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['workout_plans'] as $plan): ?>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary"><?php echo date('M d', strtotime($plan->CreatedDate)); ?></div>
                                        <div class="table-cell-secondary"><?php echo date('Y', strtotime($plan->CreatedDate)); ?></div>
                                    </td>
                                    <td>
                                        <div class="player-info">
                                            <div class="player-avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="player-details">
                                                <div class="table-cell-title"><?php echo htmlspecialchars($plan->player_name ?? 'Unknown Player'); ?></div>
                                                <div class="table-cell-secondary"><?php echo htmlspecialchars($plan->player_email ?? ''); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">
                                            <?php 
                                            $details = htmlspecialchars($plan->WorkoutDetails);
                                            echo strlen($details) > 100 ? substr($details, 0, 100) . '...' : $details; 
                                            ?>
                                        </div>
                                        <?php if(strlen($plan->WorkoutDetails) > 100): ?>
                                            <button class="btn-link" onclick="viewPlanDetails(<?php echo $plan->PlanID; ?>, 'workout')">
                                                <i class="fas fa-expand-alt"></i> View Full
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="table-badge frequency-<?php echo strtolower($plan->Frequency); ?>">
                                            <?php echo ucfirst($plan->Frequency); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $plan->Duration; ?> days</div>
                                    </td>
                                    <td>
                                        <?php if(!empty($plan->VideoUrl)): ?>
                                            <a href="<?php echo htmlspecialchars($plan->VideoUrl); ?>" target="_blank" class="btn-link">
                                                <i class="fas fa-play-circle"></i> Watch Video
                                            </a>
                                        <?php else: ?>
                                            <span class="table-cell-secondary">No video</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="table-badge status-<?php echo strtolower($plan->Status); ?>">
                                            <?php echo ucfirst($plan->Status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-sm btn-primary" onclick="viewPlanDetails(<?php echo $plan->PlanID; ?>, 'workout')" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="btn-sm btn-secondary" onclick="editPlan(<?php echo $plan->PlanID; ?>, 'workout')" title="Edit Plan">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <h3>No Workout Plans Found</h3>
                        <p>You haven't created any workout plans yet. Click the "Add Workout Plan" button above to create your first plan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Workout Plan Modal -->
<div id="addWorkoutPlanModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-dumbbell"></i> Add Workout Plan</h3>
            <span class="close" onclick="closeAddWorkoutPlanModal()">&times;</span>
        </div>
        <form method="POST" action="<?php echo URLROOT; ?>/trainer/addWorkoutPlan">
            <div class="modal-body">
                <div class="form-group">
                    <label for="player_id">Select Player *</label>
                    <select id="player_id" name="player_id" class="form-control" required>
                        <option value="">Choose a player...</option>
                        <?php foreach($data['players'] as $player): ?>
                            <option value="<?php echo $player->UserID; ?>">
                                <?php echo htmlspecialchars($player->name); ?> (<?php echo htmlspecialchars($player->email); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="workout_details">Workout Details *</label>
                    <textarea id="workout_details" name="workout_details" class="form-control" rows="6" required 
                            placeholder="Provide detailed workout plan including exercises, sets, reps, rest periods, instructions, etc..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="frequency">Frequency *</label>
                    <select id="frequency" name="frequency" class="form-control" required>
                        <option value="">Select frequency...</option>
                        <option value="Daily">Daily</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Bi-weekly">Bi-weekly</option>
                        <option value="Custom">Custom</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration (Days) *</label>
                    <input type="number" id="duration" name="duration" class="form-control" required min="1" max="365" 
                           placeholder="Enter duration in days (e.g., 30)">
                </div>
                
                <div class="form-group">
                    <label for="video_url">Video URL (Optional)</label>
                    <input type="url" id="video_url" name="video_url" class="form-control" 
                           placeholder="https://youtube.com/watch?v=... or other video URL">
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Workout Plan Guidelines:</strong>
                    <ul style="margin-top: 5px; margin-bottom: 0;">
                        <li>Include specific exercises with sets and reps</li>
                        <li>Mention rest periods between sets</li>
                        <li>Consider player's fitness level and goals</li>
                        <li>Provide progression guidelines</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddWorkoutPlanModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Plan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Plan Details Modal -->
<div id="viewPlanModal" class="modal" style="display: none;">
    <div class="modal-content large">
        <div class="modal-header">
            <h3><i class="fas fa-eye"></i> Plan Details</h3>
            <span class="close" onclick="closeViewPlanModal()">&times;</span>
        </div>
        <div class="modal-body" id="viewPlanContent">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeViewPlanModal()">Close</button>
        </div>
    </div>
</div>

<script>
    // Add Workout Plan Modal Functions
    function openAddWorkoutPlanModal() {
        document.getElementById('addWorkoutPlanModal').style.display = 'block';
    }

    function closeAddWorkoutPlanModal() {
        document.getElementById('addWorkoutPlanModal').style.display = 'none';
        document.getElementById('addWorkoutPlanModal').querySelector('form').reset();
    }

    // View plan details
    function viewPlanDetails(planId, type) {
        const modal = document.getElementById('viewPlanModal');
        const content = document.getElementById('viewPlanContent');
        
        content.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin"></i> Loading plan details...
            </div>
        `;
        
        modal.style.display = 'block';
        
        // In a real implementation, this would fetch details via AJAX
        setTimeout(() => {
            content.innerHTML = `
                <div class="plan-details">
                    <p><strong>Plan ID:</strong> ${planId}</p>
                    <p><strong>Type:</strong> ${type}</p>
                    <p><em>Full plan details would be loaded here via AJAX...</em></p>
                </div>
            `;
        }, 1000);
    }

    function closeViewPlanModal() {
        document.getElementById('viewPlanModal').style.display = 'none';
    }

    // Edit plan
    function editPlan(planId, type) {
        alert(`Edit ${type} plan ${planId} - Feature coming soon!`);
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const addModal = document.getElementById('addWorkoutPlanModal');
        const viewModal = document.getElementById('viewPlanModal');
        
        if (event.target === addModal) {
            closeAddWorkoutPlanModal();
        }
        if (event.target === viewModal) {
            closeViewPlanModal();
        }
    }

    // Initialize Universal Sidebar for trainer
    document.addEventListener('DOMContentLoaded', function() {
        new UniversalSidebar({
            sidebarId: 'trainerSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent',
            sidebarClass: 'trainer-sidebar'
        });
    });
</script>

<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
</body>
</html>