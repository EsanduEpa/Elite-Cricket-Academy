<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
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
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
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
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-clipboard-list"></i> Trainer Plans & Resources</h1>
                        <p>Access general workout routines, nutrition guides, and supplement recommendations from our trainers</p>
                    </div>
                    <div class="header-actions">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Medical
                        </a>
                        <button class="btn-refresh" onclick="refreshTrainerPlans()">
                            <i class="fas fa-sync-alt"></i>
                            <span class="current-time"><?php echo date('H:i'); ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="tab-btn active" data-tab="all" onclick="switchTab('all')">
                    <i class="fas fa-list"></i> All Plans
                </button>
                <button class="tab-btn" data-tab="workouts" onclick="switchTab('workouts')">
                    <i class="fas fa-dumbbell"></i> General Workouts
                </button>
                <button class="tab-btn" data-tab="nutrition" onclick="switchTab('nutrition')">
                    <i class="fas fa-apple-alt"></i> Nutrition Guides
                </button>
                <button class="tab-btn" data-tab="supplements" onclick="switchTab('supplements')">
                    <i class="fas fa-capsules"></i> Supplement Info
                </button>
            </div>

            <!-- Content Sections -->
            <div id="all-plans" class="tab-content active">
                <!-- General Workout Plans -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-dumbbell"></i> General Workout Plans</h2>
                            <span class="event-count">5 Available</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Workout Plan</th>
                                    <th>Trainer</th>
                                    <th>Level</th>
                                    <th>Duration</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Beginner Cricket Fitness</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-play"></i> Foundation strength & conditioning
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Coach Johnson</div>
                                        <div class="table-cell-secondary">Physical Trainer</div>
                                    </td>
                                    <td>
                                        <span class="table-badge level-beginner">Beginner</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">4 weeks</div>
                                        <div class="table-cell-secondary">3x per week</div>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('workout', 1)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Intermediate Strength Training</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-weight-hanging"></i> Progressive strength building
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Trainer Mike</div>
                                        <div class="table-cell-secondary">Fitness Specialist</div>
                                    </td>
                                    <td>
                                        <span class="table-badge level-intermediate">Intermediate</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">6 weeks</div>
                                        <div class="table-cell-secondary">4x per week</div>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('workout', 2)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Advanced Cricket Performance</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-medal"></i> Elite performance training
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Coach Sarah</div>
                                        <div class="table-cell-secondary">Performance Coach</div>
                                    </td>
                                    <td>
                                        <span class="table-badge level-advanced">Advanced</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">8 weeks</div>
                                        <div class="table-cell-secondary">5x per week</div>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('workout', 3)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- General Nutrition Guides -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-apple-alt"></i> General Nutrition Guides</h2>
                            <span class="event-count">4 Guides</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Nutrition Guide</th>
                                    <th>Nutritionist</th>
                                    <th>Focus</th>
                                    <th>Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Sports Nutrition Basics</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-book"></i> Essential nutrition fundamentals
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Nutritionist Emma</div>
                                        <div class="table-cell-secondary">Sports Nutritionist</div>
                                    </td>
                                    <td>
                                        <span class="table-badge focus-general">General Health</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">Oct 15</div>
                                        <div class="table-cell-secondary">2025</div>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('nutrition', 1)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Pre & Post Training Nutrition</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-clock"></i> Timing your nutrition
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Nutritionist Emma</div>
                                        <div class="table-cell-secondary">Sports Nutritionist</div>
                                    </td>
                                    <td>
                                        <span class="table-badge focus-performance">Performance</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">Oct 12</div>
                                        <div class="table-cell-secondary">2025</div>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('nutrition', 2)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Hydration Guidelines</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-tint"></i> Optimal hydration strategies
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Dr. Wilson</div>
                                        <div class="table-cell-secondary">Sports Medicine</div>
                                    </td>
                                    <td>
                                        <span class="table-badge focus-health">Health</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">Oct 8</div>
                                        <div class="table-cell-secondary">2025</div>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('nutrition', 3)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- General Supplement Information -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-capsules"></i> Supplement Information</h2>
                            <span class="event-count">6 Supplements</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Supplement</th>
                                    <th>Benefits</th>
                                    <th>Recommended Dosage</th>
                                    <th>Safety Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Whey Protein</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-muscle"></i> Muscle recovery & growth
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">Post-workout recovery</div>
                                        <div class="table-cell-secondary">Muscle protein synthesis</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">20-30g</div>
                                        <div class="table-cell-secondary">Post-workout</div>
                                    </td>
                                    <td>
                                        <span class="table-badge safety-high">Very Safe</span>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('supplement', 1)">
                                            <i class="fas fa-info"></i> Info
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Creatine Monohydrate</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-bolt"></i> Power & strength enhancement
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">Increased power output</div>
                                        <div class="table-cell-secondary">Enhanced performance</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">3-5g</div>
                                        <div class="table-cell-secondary">Daily</div>
                                    </td>
                                    <td>
                                        <span class="table-badge safety-high">Very Safe</span>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('supplement', 2)">
                                            <i class="fas fa-info"></i> Info
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Multivitamin</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-pills"></i> General health support
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">Nutritional insurance</div>
                                        <div class="table-cell-secondary">Immune system support</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">1 tablet</div>
                                        <div class="table-cell-secondary">With breakfast</div>
                                    </td>
                                    <td>
                                        <span class="table-badge safety-high">Very Safe</span>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewGeneralPlan('supplement', 3)">
                                            <i class="fas fa-info"></i> Info
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Individual Tab Contents (Hidden by default) -->
            <div id="workouts-content" class="tab-content">
                <!-- Only workout plans will be shown here -->
            </div>

            <div id="nutrition-content" class="tab-content">
                <!-- Only nutrition guides will be shown here -->
            </div>

            <div id="supplements-content" class="tab-content">
                <!-- Only supplement info will be shown here -->
            </div>
        </div>
    </div>

    <!-- General Plan Modal -->
    <div id="generalPlanModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-info-circle"></i> Plan Details</h3>
                <span class="close" onclick="closeModal('generalPlanModal')">&times;</span>
            </div>
            <div class="modal-body" id="generalPlanContent">
                <!-- Plan content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
            
            // Show corresponding content
            if (tabName === 'all') {
                document.getElementById('all-plans').classList.add('active');
            } else {
                // For specific tabs, you could filter the content or show separate sections
                document.getElementById('all-plans').classList.add('active');
                // Add filtering logic here if needed
            }
        }

        // View general plan function
        function viewGeneralPlan(type, planId) {
            const modal = document.getElementById('generalPlanModal');
            const content = document.getElementById('generalPlanContent');
            const title = document.getElementById('modalTitle');
            
            // Sample plan data (in real app, this would be fetched from server)
            const plans = {
                workout: {
                    1: {
                        title: 'Beginner Cricket Fitness',
                        trainer: 'Coach Johnson',
                        level: 'Beginner',
                        duration: '4 weeks',
                        content: `
                            <h4>Program Overview</h4>
                            <p>This foundational program is designed for new cricket players to build basic fitness and strength.</p>
                            
                            <h4>Week 1-2: Foundation</h4>
                            <ul>
                                <li><strong>Day 1:</strong> Basic cardio (20 min) + bodyweight exercises</li>
                                <li><strong>Day 2:</strong> Cricket-specific movements + flexibility</li>
                                <li><strong>Day 3:</strong> Strength training basics + core work</li>
                            </ul>
                            
                            <h4>Week 3-4: Progression</h4>
                            <ul>
                                <li><strong>Day 1:</strong> Cardio intervals (25 min) + resistance exercises</li>
                                <li><strong>Day 2:</strong> Cricket drills + agility training</li>
                                <li><strong>Day 3:</strong> Progressive strength training + stability</li>
                            </ul>
                            
                            <h4>Equipment Needed</h4>
                            <ul>
                                <li>Cricket bat and ball</li>
                                <li>Light dumbbells (5-15 lbs)</li>
                                <li>Exercise mat</li>
                                <li>Resistance bands</li>
                            </ul>
                        `
                    }
                },
                nutrition: {
                    1: {
                        title: 'Sports Nutrition Basics',
                        trainer: 'Nutritionist Emma',
                        focus: 'General Health',
                        content: `
                            <h4>Nutrition Fundamentals for Athletes</h4>
                            <p>Understanding proper nutrition is crucial for athletic performance and recovery.</p>
                            
                            <h4>Macronutrient Guidelines</h4>
                            <ul>
                                <li><strong>Carbohydrates (45-65%):</strong> Primary energy source</li>
                                <li><strong>Proteins (15-25%):</strong> Muscle repair and growth</li>
                                <li><strong>Fats (20-35%):</strong> Essential fatty acids and energy</li>
                            </ul>
                            
                            <h4>Meal Timing</h4>
                            <ul>
                                <li><strong>Pre-workout (2-3 hours):</strong> Complex carbs + moderate protein</li>
                                <li><strong>Post-workout (30 min):</strong> Protein + simple carbs</li>
                                <li><strong>Throughout day:</strong> Balanced meals every 3-4 hours</li>
                            </ul>
                            
                            <h4>Hydration Guidelines</h4>
                            <ul>
                                <li>8-10 glasses of water daily</li>
                                <li>Extra 16-24 oz for every hour of training</li>
                                <li>Monitor urine color for hydration status</li>
                            </ul>
                        `
                    }
                },
                supplement: {
                    1: {
                        title: 'Whey Protein Information',
                        benefits: 'Muscle recovery & growth',
                        dosage: '20-30g post-workout',
                        content: `
                            <h4>What is Whey Protein?</h4>
                            <p>Whey protein is a complete protein derived from milk during cheese production. It contains all essential amino acids needed for muscle protein synthesis.</p>
                            
                            <h4>Benefits</h4>
                            <ul>
                                <li>Rapid muscle recovery after workouts</li>
                                <li>Supports lean muscle mass development</li>
                                <li>High biological value and fast absorption</li>
                                <li>Convenient protein source</li>
                            </ul>
                            
                            <h4>Recommended Usage</h4>
                            <ul>
                                <li><strong>Timing:</strong> Within 30 minutes post-workout</li>
                                <li><strong>Dosage:</strong> 20-30g per serving</li>
                                <li><strong>Mixing:</strong> With water or milk</li>
                                <li><strong>Frequency:</strong> 1-2 servings daily</li>
                            </ul>
                            
                            <h4>Safety Information</h4>
                            <ul>
                                <li>Generally safe for healthy individuals</li>
                                <li>Avoid if lactose intolerant (consider isolate)</li>
                                <li>Stay hydrated when using protein supplements</li>
                                <li>Consult healthcare provider if you have kidney issues</li>
                            </ul>
                        `
                    }
                }
            };
            
            const planData = plans[type] && plans[type][planId];
            if (planData) {
                title.innerHTML = `<i class="fas fa-${type === 'workout' ? 'dumbbell' : type === 'nutrition' ? 'apple-alt' : 'capsules'}"></i> ${planData.title}`;
                content.innerHTML = `
                    <div class="plan-header">
                        <h4>${planData.title}</h4>
                        ${planData.trainer ? `<p><strong>By:</strong> ${planData.trainer}</p>` : ''}
                        ${planData.level ? `<p><strong>Level:</strong> ${planData.level}</p>` : ''}
                        ${planData.duration ? `<p><strong>Duration:</strong> ${planData.duration}</p>` : ''}
                        ${planData.focus ? `<p><strong>Focus:</strong> ${planData.focus}</p>` : ''}
                        ${planData.benefits ? `<p><strong>Benefits:</strong> ${planData.benefits}</p>` : ''}
                        ${planData.dosage ? `<p><strong>Recommended Dosage:</strong> ${planData.dosage}</p>` : ''}
                    </div>
                    <div class="plan-details">
                        ${planData.content}
                    </div>
                `;
            }
            
            modal.style.display = 'block';
        }

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Refresh function
        function refreshTrainerPlans() {
            console.log('Refreshing trainer plans...');
            const refreshBtn = document.querySelector('.btn-refresh i');
            refreshBtn.style.animation = 'spin 1s linear';
            setTimeout(() => {
                refreshBtn.style.animation = '';
            }, 1000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('generalPlanModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
</body>
</html>