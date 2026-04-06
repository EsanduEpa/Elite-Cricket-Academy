<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
<!-- Mobile-specific meta tags -->
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

    <!-- Trainer Layout -->
    <div class="player-layout workout-page">
        <!-- Left Sidebar Panel -->
        <div class="trainer-sidebar" id="trainerSidebar">
            <div class="sidebar-header">
                <div class="trainer-logo">
                    <i class="fas fa-user-tie"></i>
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
                            <span>Schedule & Bookings</span>
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
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Profile Section -->
            <div class="trainer-profile">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-name"><?php echo $_SESSION['username'] ?? 'Trainer'; ?></div>
                <div class="trainer-role">Fitness Trainer</div>
                <div class="profile-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
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
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-dumbbell"></i> Workout Plans Management</h1>
                        <p>Create, manage, and track workout plans for your trainees</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-training plan-cta-btn js-workout-plan-cta" onclick="openAddModal()">
                            <i class="fas fa-plus"></i>Add New Plan
                        </button>
                        <button class="btn btn-refresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            <div class="current-time"><?php echo date('H:i'); ?></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('workout_message'); ?>

            <!-- Workout Plans Table Card -->
            <div class="schedule-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-dumbbell"></i> Your Workout Plans</h2>
                        <div class="table-controls" style="display: flex; gap: 15px; align-items: center;">
                            <div style="position: relative;">
                                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                                <input type="text" id="searchInput" placeholder="Search plans..." style="padding: 8px 12px 8px 35px; border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; background: rgba(255,255,255,0.2); color: white; font-size: 13px;">
                            </div>
                            <select id="frequencyFilter" style="padding: 8px 15px; border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; background: rgba(255,255,255,0.2); color: white; font-size: 13px;">
                                <option value="">All Frequencies</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Bi-weekly">Bi-weekly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Plan ID</th>
                                <th>Workout Name</th>
                                <th>Frequency</th>
                                <th>Intensity</th>
                                <th>Duration (min)</th>
                                <th>Duration (days)</th>
                                <th>Video Link</th>
                                <th>Benefits</th>
                                <th>Not Suitable For</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['workout_plans'])): ?>
                                <?php foreach ($data['workout_plans'] as $plan): ?>
                                    <tr data-plan-id="<?php echo $plan->PlanID; ?>">
                                        <td>
                                            <div class="table-cell-primary plan-id">#<?php echo str_pad($plan->PlanID, 4, '0', STR_PAD_LEFT); ?></div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title plan-name">
                                                <strong><?php echo htmlspecialchars($plan->workoutname); ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="table-badge frequency-badge" style="
                                                <?php 
                                                    $freqColors = [
                                                        'Daily' => 'background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);',
                                                        'Weekly' => 'background: rgba(255, 159, 67, 0.1); color: #ff9f43; border: 1px solid rgba(255, 159, 67, 0.3);',
                                                        'Bi-weekly' => 'background: rgba(74, 144, 226, 0.1); color: #4A90E2; border: 1px solid rgba(74, 144, 226, 0.3);'
                                                    ];
                                                    echo $freqColors[$plan->frequency] ?? $freqColors['Weekly'];
                                                ?>
                                            ">
                                                <?php echo htmlspecialchars($plan->frequency); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($plan->Intensity)): ?>
                                                <span class="table-badge intensity-badge" style="
                                                    <?php 
                                                        $intensityColors = [
                                                            'High' => 'background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 107, 107, 0.3);',
                                                            'Moderate' => 'background: rgba(255, 159, 67, 0.1); color: #ff9f43; border: 1px solid rgba(255, 159, 67, 0.3);',
                                                            'Low' => 'background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);'
                                                        ];
                                                        echo $intensityColors[$plan->Intensity] ?? $intensityColors['Moderate'];
                                                    ?>
                                                ">
                                                    <?php echo htmlspecialchars($plan->Intensity); ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: #999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-cell-primary duration">
                                                <i class="fas fa-clock"></i> <?php echo $plan->Duration; ?> mins
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($plan->durationdays)): ?>
                                                <div class="table-cell-primary durationdays">
                                                    <i class="fas fa-calendar-week"></i> <?php echo $plan->durationdays; ?> days
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($plan->VideoLink)): ?>
                                                <a href="<?php echo htmlspecialchars($plan->VideoLink); ?>" target="_blank" 
                                                   class="table-badge" 
                                                   style="background: rgba(255, 59, 48, 0.1); color: #ff3b30; border: 1px solid rgba(255, 59, 48, 0.3); text-decoration: none; display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; cursor: pointer;">
                                                    <i class="fas fa-video"></i> Watch
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($plan->Benefits)): ?>
                                                <div style="font-size: 12px; color: #666; line-height: 1.4; max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($plan->Benefits, 0, 60)) . (strlen($plan->Benefits) > 60 ? '...' : ''); ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($plan->NotSuitableFor)): ?>
                                                <div style="font-size: 12px; color: #ff6b6b; line-height: 1.4; max-width: 200px;">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <?php echo htmlspecialchars(substr($plan->NotSuitableFor, 0, 60)) . (strlen($plan->NotSuitableFor) > 60 ? '...' : ''); ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-cell-secondary">
                                                <i class="fas fa-calendar-plus"></i>
                                                <?php echo date('M d, Y', strtotime($plan->CreatedDate)); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="profile-actions">
                                                <button class="profile-btn" onclick="viewPlan(<?php echo $plan->PlanID; ?>)" title="View Details" style="background: rgba(46, 213, 115, 0.1); color: #2ed573; border-color: rgba(46, 213, 115, 0.3);">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="profile-btn" onclick='editPlan(<?php echo $plan->PlanID; ?>, <?php echo htmlspecialchars(json_encode([
                                                    'workoutname' => $plan->workoutname,
                                                    'frequency' => $plan->frequency,
                                                    'duration' => $plan->Duration,
                                                    'durationdays' => $plan->durationdays ?? '',
                                                    'videolink' => $plan->VideoLink ?? '',
                                                    'intensity' => $plan->Intensity ?? 'Moderate',
                                                    'notsuitablefor' => $plan->NotSuitableFor ?? '',
                                                    'benefits' => $plan->Benefits ?? ''
                                                ]), ENT_QUOTES, 'UTF-8'); ?>)' title="Edit Plan" style="background: rgba(255, 159, 67, 0.1); color: #ff9f43; border-color: rgba(255, 159, 67, 0.3);">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="profile-btn" onclick="deletePlan(<?php echo $plan->PlanID; ?>, '<?php echo addslashes($plan->workoutname); ?>')" title="Delete Plan" style="background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border-color: rgba(255, 107, 107, 0.3);">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 40px 20px;">
                                        <div style="color: #666; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                            <i class="fas fa-dumbbell" style="font-size: 3rem; color: #4A90E2; margin-bottom: 15px;"></i>
                                            <h3 style="color: #4A90E2; margin-bottom: 8px;">No workout plans found</h3>
                                            <p style="margin-bottom: 20px;">Start by creating your first workout plan!</p>
                                            <button class="btn btn-training plan-cta-btn plan-cta-secondary js-workout-plan-cta" onclick="openAddModal()">
                                                <i class="fas fa-plus"></i>Add Workout Plan
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Workout Plan Modal -->
    <div id="workoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2);">
                <h2 id="modalTitle"><i class="fas fa-dumbbell"></i> Add New Workout Plan</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="workoutForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="planId" name="plan_id">
                    <input type="hidden" name="trainer_id" value="<?php echo $_SESSION['user_id'] ?? 10; ?>">
                    
                    <div class="form-group">
                        <label for="workoutname" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                            <i class="fas fa-dumbbell" style="color: #4A90E2; margin-right: 8px;"></i>
                            Workout Name <span style="color: #ff6b6b;">*</span>
                        </label>
                        <input type="text" id="workoutname" name="workoutname" required 
                               minlength="3" maxlength="255"
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s ease;"
                               placeholder="Enter workout plan name (3-255 characters)">
                        <small style="color: #666; font-size: 11px; display: block; margin-top: 4px;">
                            <span id="workoutname-counter">0</span>/255 characters
                        </small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="frequency" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-calendar-alt" style="color: #4A90E2; margin-right: 8px;"></i>
                                Frequency <span style="color: #ff6b6b;">*</span>
                            </label>
                            <select id="frequency" name="frequency" required 
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: white;">
                                <option value="">Select Frequency</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Bi-weekly">Bi-weekly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="duration" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-clock" style="color: #4A90E2; margin-right: 8px;"></i>
                                Duration (minutes) <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="number" id="duration" name="duration" min="15" max="180" required 
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                   placeholder="15-180 minutes">
                        <small style="color: #666; font-size: 11px; display: block; margin-top: 4px;">
                            Session length per workout
                        </small>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 15px;">
                        <div class="form-group">
                            <label for="durationdays" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-calendar-week" style="color: #4A90E2; margin-right: 8px;"></i>
                                Duration (days)
                            </label>
                            <input type="number" id="durationdays" name="durationdays" min="1" max="365" 
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                   placeholder="1-365 days">
                            <small style="color: #666; font-size: 11px; display: block; margin-top: 4px;">
                                Total program duration
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="intensity" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-tachometer-alt" style="color: #4A90E2; margin-right: 8px;"></i>
                                Intensity Level
                            </label>
                            <select id="intensity" name="intensity" 
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: white;">
                                <option value="Low">Low</option>
                                <option value="Moderate" selected>Moderate</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="videolink" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-video" style="color: #4A90E2; margin-right: 8px;"></i>
                                Video Link
                            </label>
                            <input type="url" id="videolink" name="videolink"
                                   pattern="https?://.+"
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                   placeholder="https://example.com/video">
                            <small style="color: #666; font-size: 11px; display: block; margin-top: 4px;">
                                Must start with http:// or https://
                            </small>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="benefits" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                            <i class="fas fa-star" style="color: #4A90E2; margin-right: 8px;"></i>
                            Key Benefits
                        </label>
                        <textarea id="benefits" name="benefits" rows="3" maxlength="1000"
                                  style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;"
                                  placeholder="List the key benefits of this workout (max 1000 characters)" oninput="updateCharCount('benefits')"></textarea>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                            <small style="color: #666; font-size: 11px;">Health and fitness benefits</small>
                            <span id="benefits-counter" style="color: #666; font-size: 11px; font-weight: 500;">0/1000 characters</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="notsuitablefor" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                            <i class="fas fa-exclamation-triangle" style="color: #ff6b6b; margin-right: 8px;"></i>
                            Not Suitable For
                        </label>
                        <textarea id="notsuitablefor" name="notsuitablefor" rows="2" maxlength="1000"
                                  style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;"
                                  placeholder="E.g., people with knee injuries, pregnant women (max 1000 characters)" oninput="updateCharCount('notsuitablefor')"></textarea>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                            <small style="color: #666; font-size: 11px;">Contraindications and warnings</small>
                            <span id="notsuitablefor-counter" style="color: #666; font-size: 11px; font-weight: 500;">0/1000 characters</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background: #f8fafc; padding: 20px 25px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-save"></i>Save Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content modal-small">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b, #ff8e8e);">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff6b6b; margin-bottom: 15px;"></i>
                    <p style="margin-bottom: 10px; color: #374151;">Are you sure you want to delete the workout plan "<span id="deletePlanName" style="font-weight: 600; color: #4A90E2;"></span>"?</p>
                    <p style="color: #ff6b6b; font-weight: 600; font-size: 14px;">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; padding: 20px 25px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" action="<?php echo URLROOT; ?>/trainer/deleteWorkoutPlan" style="display: inline;">
                    <input type="hidden" id="deletePlanId" name="plan_id">
                    <button type="submit" style="background: #ff6b6b; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-trash"></i>Delete Plan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Plan Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2);">
                <h2><i class="fas fa-eye"></i> Workout Plan Details</h2>
                <span class="close" onclick="closeViewModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="planDetails">
                    <!-- Plan details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; padding: 20px 25px; display: flex; justify-content: flex-end;">
                <button type="button" onclick="closeViewModal()" style="background: #4A90E2; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/trainer/workout.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
