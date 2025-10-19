<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/bookings.css?v=<?php echo time(); ?>">
    
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
                    <li class="nav-item active">
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
            <!-- Dashboard Header - Using Same Style as Dashboard -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1>Session Bookings</h1>
                        <p>Book sessions with coaches and trainers or view your existing bookings</p>
                    </div>
                    <div class="header-actions">
                        <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="btn btn-training">
                            <i class="fas fa-user-tie"></i>
                            Coach Sessions
                        </a>
                        <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="btn btn-performance">
                            <i class="fas fa-dumbbell"></i>
                            Trainer Sessions
                        </a>
                        <button class="btn btn-calendar" onclick="showBookingHistory()">
                            <i class="fas fa-history"></i>
                            Booking History
                        </button>
                    </div>
                </div>
            </div>

           

            <!-- Quick Actions Section - Dashboard Style -->
            <div class="quick-actions-section">
                <div class="section-header">
                    <h2><i class="fas fa-rocket"></i> Quick Booking</h2>
                    <p>Choose your preferred session type to get started</p>
                </div>
                <div class="quick-actions-grid">
                    <div class="action-card coach-card">
                        <div class="card-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="card-content">
                            <h3>Coach Sessions</h3>
                            <p>Book personalized coaching sessions with our expert cricket coaches</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-cricket-bat-ball"></i> Batting technique improvement</li>
                                <li><i class="fas fa-baseball-ball"></i> Bowling skill development</li>
                                <li><i class="fas fa-users"></i> Individual and group sessions</li>
                                <li><i class="fas fa-trophy"></i> Match preparation coaching</li>
                            </ul>
                        </div>
                        <div class="card-actions">
                            <div class="pricing">Starting from <strong>₹2,000/session</strong></div>
                            <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="action-btn btn-coach">
                                <i class="fas fa-calendar-plus"></i> Book Coach Session
                            </a>
                        </div>
                    </div>

                    <div class="action-card trainer-card">
                        <div class="card-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="card-content">
                            <h3>Trainer Sessions</h3>
                            <p>Book fitness and conditioning sessions with our certified trainers</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-heartbeat"></i> Strength and conditioning</li>
                                <li><i class="fas fa-clipboard-check"></i> Fitness assessments</li>
                                <li><i class="fas fa-shield-alt"></i> Injury prevention training</li>
                                <li><i class="fas fa-running"></i> Cricket-specific fitness</li>
                            </ul>
                        </div>
                        <div class="card-actions">
                            <div class="pricing">Starting from <strong>₹1,500/session</strong></div>
                            <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="action-btn btn-trainer">
                                <i class="fas fa-calendar-plus"></i> Book Trainer Session
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions Overview -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-alt"></i> Upcoming Sessions</h2>
                    <div class="header-filters">
                        <select class="form-control filter-select" id="session-type-filter">
                            <option value="all">All Sessions</option>
                            <option value="coach">Coach Sessions</option>
                            <option value="trainer">Trainer Sessions</option>
                        </select>
                        <select class="form-control filter-select" id="status-filter">
                            <option value="all">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
                
                <div class="sessions-grid">
                    <!-- Coach Session Example -->
                    <div class="session-card coach-session">
                        <div class="session-header">
                            <div class="session-type">
                                <i class="fas fa-user-tie"></i>
                                <span>Coach Session</span>
                            </div>
                            <span class="session-status confirmed">Confirmed</span>
                        </div>
                        <div class="session-details">
                            <h3>Batting Technique Session</h3>
                            <div class="session-info">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span>Coach Johnson</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Oct 20, 2025</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>9:00 AM - 10:00 AM</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Indoor Net 1</span>
                                </div>
                            </div>
                        </div>
                        <div class="session-actions">
                            <button class="btn btn-outline">View Details</button>
                            <button class="btn btn-danger">Cancel</button>
                        </div>
                    </div>

                    <!-- Trainer Session Example -->
                    <div class="session-card trainer-session">
                        <div class="session-header">
                            <div class="session-type">
                                <i class="fas fa-dumbbell"></i>
                                <span>Trainer Session</span>
                            </div>
                            <span class="session-status confirmed">Confirmed</span>
                        </div>
                        <div class="session-details">
                            <h3>Strength & Conditioning</h3>
                            <div class="session-info">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span>Trainer Williams</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Oct 21, 2025</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>6:00 AM - 7:00 AM</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Fitness Center</span>
                                </div>
                            </div>
                        </div>
                        <div class="session-actions">
                            <button class="btn btn-outline">View Details</button>
                            <button class="btn btn-danger">Cancel</button>
                        </div>
                    </div>

                    <!-- Pending Coach Session -->
                    <div class="session-card coach-session">
                        <div class="session-header">
                            <div class="session-type">
                                <i class="fas fa-user-tie"></i>
                                <span>Coach Session</span>
                            </div>
                            <span class="session-status pending">Pending</span>
                        </div>
                        <div class="session-details">
                            <h3>Bowling Technique Session</h3>
                            <div class="session-info">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span>Coach Anderson</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Oct 22, 2025</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>2:00 PM - 3:00 PM</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Practice Ground B</span>
                                </div>
                            </div>
                        </div>
                        <div class="session-actions">
                            <button class="btn btn-outline">View Details</button>
                            <button class="btn btn-warning">Payment Required</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Booking History Modal -->
    <div id="history-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-history"></i> Booking History</h3>
                <span class="close" onclick="closeHistoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="history-tabs">
                    <button class="tab-btn active" onclick="showHistoryTab('all')">All Sessions</button>
                    <button class="tab-btn" onclick="showHistoryTab('coach')">Coach Sessions</button>
                    <button class="tab-btn" onclick="showHistoryTab('trainer')">Trainer Sessions</button>
                </div>
                <div class="history-content" id="history-content">
                    <!-- History content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/bookings.js"></script>
</body>
</html>