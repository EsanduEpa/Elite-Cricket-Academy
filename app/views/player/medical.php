<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/medical.css">
    
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
                    <li class="nav-item active">
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
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-heartbeat"></i> Medical Records</h1>
                <p>Track your health, fitness assessments, and medical history.</p>
            </div>

            <!-- Health Overview -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-value">Excellent</div>
                    <div class="stat-label">Overall Health</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-weight"></i>
                    </div>
                    <div class="stat-value">75kg</div>
                    <div class="stat-label">Weight</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-ruler-vertical"></i>
                    </div>
                    <div class="stat-value">178cm</div>
                    <div class="stat-label">Height</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value">Oct 10</div>
                    <div class="stat-label">Last Checkup</div>
                </div>
            </div>

            <!-- Recent Medical Records -->
            <div class="schedule-section">
                <h3>Recent Medical Records</h3>
                
                <div class="schedule-item">
                    <div class="schedule-time">Oct 10</div>
                    <div class="schedule-details">
                        <h4>Annual Health Checkup</h4>
                        <p><i class="fas fa-check-circle" style="color: green;"></i> All clear • <i class="fas fa-user-md"></i> Dr. Smith • General health assessment completed</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">Sept 15</div>
                    <div class="schedule-details">
                        <h4>Fitness Assessment</h4>
                        <p><i class="fas fa-chart-line"></i> Fitness Level: Excellent • <i class="fas fa-dumbbell"></i> Trainer Johnson • Cardiovascular and strength tests</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">Aug 22</div>
                    <div class="schedule-details">
                        <h4>Injury Prevention Screening</h4>
                        <p><i class="fas fa-shield-alt"></i> No risk factors identified • <i class="fas fa-user-md"></i> Sports Medicine Specialist • Biomechanical analysis</p>
                    </div>
                </div>
            </div>

            <!-- Fitness Metrics -->
            <div class="schedule-section">
                <h3>Current Fitness Metrics</h3>
                
                <div class="stats-overview" style="margin-bottom: 20px;">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-value">68 bpm</div>
                        <div class="stat-label">Resting Heart Rate</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-lungs"></i>
                        </div>
                        <div class="stat-value">4.2L</div>
                        <div class="stat-label">Lung Capacity</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-value">12%</div>
                        <div class="stat-label">Body Fat</div>
                    </div>
                </div>
            </div>

            <!-- Medical History -->
            <div class="schedule-section">
                <h3>Medical History</h3>
                
                <div class="schedule-item">
                    <div class="schedule-time">2024</div>
                    <div class="schedule-details">
                        <h4>Minor Ankle Sprain</h4>
                        <p><i class="fas fa-bandage"></i> Fully recovered • Rehabilitation completed • No long-term effects</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">2023</div>
                    <div class="schedule-details">
                        <h4>Fitness Program Start</h4>
                        <p><i class="fas fa-play"></i> Began structured fitness program • Baseline measurements taken • Regular monitoring established</p>
                    </div>
                </div>
            </div>

            <!-- Vaccinations & Immunizations -->
            <div class="schedule-section">
                <h3>Vaccinations & Immunizations</h3>
                
                <div class="schedule-item">
                    <div class="schedule-time">✓</div>
                    <div class="schedule-details">
                        <h4>COVID-19 Vaccination</h4>
                        <p>Fully vaccinated • Booster received • Last updated: March 2025</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">✓</div>
                    <div class="schedule-details">
                        <h4>Tetanus Shot</h4>
                        <p>Up to date • Valid until 2028 • No adverse reactions reported</p>
                    </div>
                </div>
            </div>

            <!-- Medical Actions -->
            <div class="quick-actions">
                <h3>Medical Actions</h3>
                <div class="action-buttons">
                    <a href="#" class="action-btn" onclick="alert('Schedule checkup feature coming soon!')">
                        <i class="fas fa-calendar-plus"></i> Schedule Checkup
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Download records feature coming soon!')">
                        <i class="fas fa-download"></i> Download Records
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Emergency contacts feature coming soon!')">
                        <i class="fas fa-phone"></i> Emergency Contacts
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Health tips feature coming soon!')">
                        <i class="fas fa-lightbulb"></i> Health Tips
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/medical.js"></script>
</body>
</html>