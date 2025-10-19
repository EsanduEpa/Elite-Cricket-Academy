<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/coach-sessions.css">
    
<div class="player-layout">
    <!-- Sidebar -->
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

        <!-- Profile Section -->
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
        <!-- Dashboard Header - Using Same Blue Theme as Dashboard -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/player/bookings">Bookings</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Coach Sessions</span>
                    </div>
                    <h1>Book Coach Sessions</h1>
                    <p>Schedule personalized coaching sessions with our expert cricket coaches</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="btn btn-training">
                        <i class="fas fa-arrow-left"></i>
                        Back to Bookings
                    </a>
                    <button class="btn btn-performance" onclick="showMyCoachBookings()">
                        <i class="fas fa-history"></i>
                        My Coach Bookings
                    </button>
                </div>
            </div>
        </div>


        <!-- Filter Section -->
        <div class="coach-filters">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="coach-filter">Select Coach</label>
                    <select id="coach-filter" onchange="applyCoachFilters()">
                        <option value="">All Coaches</option>
                        <option value="">Coach Dharshana</option>
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>
                <div class="filter-item">
                    <label for="date-filter">Select Date</label>
                    <input type="date" id="date-filter" onchange="applyCoachFilters()" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="filter-item">
                    <label for="session-type-filter">Session Type</label>
                    <select id="session-type-filter" onchange="applyCoachFilters()">
                        <option value="">All Types</option>
                        <option value="Individual">Individual Coaching</option>
                        <option value="Group">Group Coaching</option>
                        <option value="Assessment">Skills Assessment</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="specialization-filter">Specialization</label>
                    <select id="specialization-filter" onchange="applyCoachFilters()">
                        <option value="">All Specializations</option>
                        <option value="Batting">Batting Specialist</option>
                        <option value="Bowling">Bowling Specialist</option>
                        <option value="Wicket-keeping">Wicket-keeping</option>
                        <option value="All-rounder">All-rounder Coaching</option>
                    </select>
                </div>
                <div class="filter-item">
                    <button class="btn btn-secondary" onclick="clearCoachFilters()">
                        <i class="fas fa-refresh"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Coaches Overview -->
        <div class="coaches-overview">
            <h2><i class="fas fa-users"></i> Our Expert Coaches</h2>
            <div class="coaches-grid" id="coaches-grid">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

        <!-- Available Coach Sessions -->
        <div class="available-coach-sessions">
            <div class="section-header">
                <h2><i class="fas fa-calendar-alt"></i> Available Coach Sessions</h2>
                <div class="session-stats">
                    <span class="stat-item">
                        <span class="stat-number" id="total-coach-sessions">0</span>
                        <span class="stat-label">Available Sessions</span>
                    </span>
                    <span class="stat-item">
                        <span class="stat-number" id="filtered-coach-sessions">0</span>
                        <span class="stat-label">Filtered Results</span>
                    </span>
                </div>
            </div>
            
            <div class="coach-sessions-container" id="coach-sessions-container">
                <!-- Will be populated by JavaScript -->
            </div>
            
            <div class="no-sessions" id="no-coach-sessions" style="display: none;">
                <div class="no-sessions-content">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Coach Sessions Available</h3>
                    <p>Try adjusting your filters or check back later for new coaching sessions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coach Session Booking Modal -->


<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/coach-sessions.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>