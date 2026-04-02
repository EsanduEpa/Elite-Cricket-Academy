<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/trainer-plans.css">
    
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
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player/trainer-plans" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Trainer Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-running"></i>
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
        <div class="main-content">
            <div class="dashboard-header">
                <div>
                    <h1><i class="fas fa-dumbbell"></i> Trainer Plans</h1>
                    <p>Explore workout, nutrition, and supplement plans from our expert trainers</p>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="tab-btn active" onclick="showTab('workout')">
                    <i class="fas fa-running"></i>
                    Workout Plans
                    <span style="background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 5px;">
                        <?php echo count($data['workoutPlans']); ?>
                    </span>
                </button>
                <button class="tab-btn" onclick="showTab('nutrition')">
                    <i class="fas fa-apple-alt"></i>
                    Nutrition Guides
                    <span style="background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 5px;">
                        <?php echo count($data['nutritionGuides']); ?>
                    </span>
                </button>
                <button class="tab-btn" onclick="showTab('supplement')">
                    <i class="fas fa-pills"></i>
                    Supplement Info
                    <span style="background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 5px;">
                        <?php echo count($data['supplementInfo']); ?>
                    </span>
                </button>
            </div>

            <!-- Workout Plans Tab -->
            <div id="workout-tab" class="tab-content active">
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                    <?php if (!empty($data['workoutPlans'])): ?>
                        <?php foreach ($data['workoutPlans'] as $plan): ?>
                            <div class="stat-card" style="cursor: pointer; transition: all 0.3s ease;" onclick="viewWorkoutPlan(<?php echo $plan->PlanID; ?>)">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-title" style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">
                                        <?php echo htmlspecialchars($plan->workoutname); ?>
                                    </div>
                                    <div style="display: flex; gap: 8px; margin: 8px 0; flex-wrap: wrap;">
                                        <span class="table-badge" style="background: rgba(74, 144, 226, 0.1); color: #4A90E2; border: 1px solid rgba(74, 144, 226, 0.3); padding: 4px 10px; border-radius: 12px; font-size: 11px;">
                                            <i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($plan->frequency); ?>
                                        </span>
                                        <?php if (!empty($plan->Intensity)): ?>
                                            <span class="table-badge" style="
                                                <?php 
                                                    $intensityColors = [
                                                        'High' => 'background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 107, 107, 0.3);',
                                                        'Moderate' => 'background: rgba(255, 159, 67, 0.1); color: #ff9f43; border: 1px solid rgba(255, 159, 67, 0.3);',
                                                        'Low' => 'background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);'
                                                    ];
                                                    echo $intensityColors[$plan->Intensity] ?? $intensityColors['Moderate'];
                                                ?>
                                                padding: 4px 10px; border-radius: 12px; font-size: 11px;">
                                                <i class="fas fa-fire"></i> <?php echo htmlspecialchars($plan->Intensity); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="stat-value" style="font-size: 32px; color: #667eea; margin: 10px 0;">
                                        <?php echo htmlspecialchars($plan->Duration); ?> <span style="font-size: 16px;">min</span>
                                    </div>
                                    <?php if (!empty($plan->durationdays)): ?>
                                        <div style="font-size: 12px; color: #666; margin: 5px 0;">
                                            <i class="fas fa-calendar-week"></i> <?php echo htmlspecialchars($plan->durationdays); ?> days program
                                        </div>
                                    <?php endif; ?>
                                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.1);">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-user-tie" style="color: #4A90E2;"></i>
                                            <span style="font-size: 13px; font-weight: 600; color: #333;">
                                                <?php echo htmlspecialchars($plan->trainer_name ?? 'Elite Trainer'); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if (!empty($plan->Benefits)): ?>
                                        <div style="margin-top: 10px; font-size: 12px; color: #666; line-height: 1.4;">
                                            <i class="fas fa-star" style="color: #f39c12;"></i>
                                            <?php echo htmlspecialchars(substr($plan->Benefits, 0, 80)) . (strlen($plan->Benefits) > 80 ? '...' : ''); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                            <i class="fas fa-dumbbell" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                            <h3 style="color: #666; margin-bottom: 10px;">No Workout Plans Available</h3>
                            <p style="color: #999;">Check back later for new training programs from our trainers</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nutrition Guides Tab -->
            <div id="nutrition-tab" class="tab-content">
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                    <?php foreach ($data['nutritionGuides'] as $guide): ?>
                        <div class="stat-card" style="cursor: pointer; transition: all 0.3s ease;" onclick="viewNutritionGuide(<?php echo $guide['id']; ?>)">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                                <i class="fas fa-apple-alt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-title" style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">
                                    <?php echo htmlspecialchars($guide['title']); ?>
                                </div>
                                <div style="display: flex; gap: 8px; margin: 8px 0;">
                                    <span class="table-badge focus-<?php echo strtolower(str_replace(' ', '-', $guide['category'])); ?>" style="padding: 4px 10px; border-radius: 12px; font-size: 11px;">
                                        <?php echo htmlspecialchars($guide['category']); ?>
                                    </span>
                                    <span class="table-badge" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6; border: 1px solid rgba(155, 89, 182, 0.3); padding: 4px 10px; border-radius: 12px; font-size: 11px;">
                                        <?php echo htmlspecialchars($guide['target_audience']); ?>
                                    </span>
                                </div>
                                <div style="font-size: 13px; color: #666; margin: 10px 0; line-height: 1.5;">
                                    <?php echo htmlspecialchars($guide['description']); ?>
                                </div>
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.1);">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user-md" style="color: #2ecc71;"></i>
                                        <span style="font-size: 13px; font-weight: 600; color: #333;">
                                            <?php echo htmlspecialchars($guide['trainer_name']); ?>
                                        </span>
                                    </div>
                                    <div style="font-size: 11px; color: #999; margin-top: 3px; margin-left: 24px;">
                                        <?php echo htmlspecialchars($guide['trainer_specialization']); ?>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; display: flex; align-items: center; gap: 12px; font-size: 12px; color: #999;">
                                    <span><i class="fas fa-eye"></i> <?php echo $guide['view_count']; ?> views</span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($guide['created_date'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Supplement Info Tab -->
            <div id="supplement-tab" class="tab-content">
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                    <?php foreach ($data['supplementInfo'] as $supplement): ?>
                        <div class="stat-card" style="cursor: pointer; transition: all 0.3s ease;" onclick="viewSupplementInfo(<?php echo $supplement['id']; ?>)">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-title" style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">
                                    <?php echo htmlspecialchars($supplement['supplement_name']); ?>
                                </div>
                                <div style="display: flex; gap: 8px; margin: 8px 0;">
                                    <span class="table-badge" style="background: rgba(142, 68, 173, 0.1); color: #8e44ad; border: 1px solid rgba(142, 68, 173, 0.3); padding: 4px 10px; border-radius: 12px; font-size: 11px;">
                                        <?php echo htmlspecialchars($supplement['category']); ?>
                                    </span>
                                    <span class="table-badge safety-<?php echo strtolower(str_replace(' ', '-', $supplement['safety_rating'])); ?>" style="padding: 4px 10px; border-radius: 12px; font-size: 11px;">
                                        <i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars($supplement['safety_rating']); ?>
                                    </span>
                                </div>
                                <div style="font-size: 13px; color: #666; margin: 10px 0; line-height: 1.5;">
                                    <?php echo htmlspecialchars($supplement['description']); ?>
                                </div>
                                <div style="background: rgba(142, 68, 173, 0.05); padding: 10px; border-radius: 8px; margin: 10px 0; border-left: 3px solid #8e44ad;">
                                    <div style="font-size: 11px; color: #666; font-weight: 600; margin-bottom: 3px;">RECOMMENDED DOSAGE</div>
                                    <div style="font-size: 13px; color: #333; font-weight: 500;">
                                        <?php echo htmlspecialchars($supplement['recommended_dosage']); ?>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.1);">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user-tie" style="color: #8e44ad;"></i>
                                        <span style="font-size: 13px; font-weight: 600; color: #333;">
                                            <?php echo htmlspecialchars($supplement['trainer_name']); ?>
                                        </span>
                                    </div>
                                    <div style="font-size: 11px; color: #999; margin-top: 3px; margin-left: 24px;">
                                        <?php echo htmlspecialchars($supplement['trainer_specialization']); ?>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; display: flex; align-items: center; gap: 12px; font-size: 12px; color: #999;">
                                    <span><i class="fas fa-eye"></i> <?php echo $supplement['view_count']; ?> views</span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($supplement['created_date'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Workout Plan Detail Modal -->
    <div id="workoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h2 id="modalTitle"><i class="fas fa-dumbbell"></i> Workout Plan Details</h2>
                <span class="close" onclick="closeModal('workoutModal')">&times;</span>
            </div>
            <div class="modal-body" id="workoutDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Nutrition Guide Detail Modal -->
    <div id="nutritionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2ecc71, #27ae60);">
                <h2 id="nutritionModalTitle"><i class="fas fa-apple-alt"></i> Nutrition Guide</h2>
                <span class="close" onclick="closeModal('nutritionModal')">&times;</span>
            </div>
            <div class="modal-body" id="nutritionDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Supplement Detail Modal -->
    <div id="supplementModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                <h2 id="supplementModalTitle"><i class="fas fa-pills"></i> Supplement Information</h2>
                <span class="close" onclick="closeModal('supplementModal')">&times;</span>
            </div>
            <div class="modal-body" id="supplementDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <script type="application/json" id="trainerPlansData"><?php echo json_encode([
        'workoutPlans' => $data['workoutPlans'],
        'nutritionGuides' => $data['nutritionGuides'],
        'supplementInfo' => $data['supplementInfo'],
    ], JSON_UNESCAPED_SLASHES); ?></script>
    <script src="<?php echo URLROOT; ?>/js/player/trainer-plans.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
