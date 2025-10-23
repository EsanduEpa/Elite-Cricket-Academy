<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
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

           

            <!-- Quick Booking Section - Small Cards -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-rocket"></i> Quick Booking</h2>
                    <p>Choose your preferred session type to get started</p>
                </div>
                <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <!-- Coach Sessions Card -->
                    <div class="stat-card action-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-title">Coach Sessions</div>
                            <div class="stat-value" style="color: #4A90E2; font-size: 18px; font-weight: 600;">₹2,000+</div>
                            <div class="stat-label" style="font-size: 12px; color: #666;">Expert Cricket Coaching</div>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="action-btn" style="width: 100%; padding: 12px;">
                                <i class="fas fa-calendar-plus"></i> Book Coach Session
                            </a>
                        </div>
                    </div>

                    <!-- Trainer Sessions Card -->
                    <div class="stat-card action-card">
                        <div class="stat-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-title">Trainer Sessions</div>
                            <div class="stat-value" style="color: #4A90E2; font-size: 18px; font-weight: 600;">₹1,500+</div>
                            <div class="stat-label" style="font-size: 12px; color: #666;">Fitness & Conditioning</div>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="action-btn" style="width: 100%; padding: 12px;">
                                <i class="fas fa-calendar-plus"></i> Book Trainer Session
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions Overview -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-alt"></i> Upcoming private Sessions</h2>
                        <div class="header-filters">
                            <select class="form-control filter-select" id="session-type-filter" onchange="filterSessions()">
                                <option value="all">All Sessions</option>
                                <option value="coach">Coach Sessions</option>
                                <option value="trainer">Trainer Sessions</option>
                            </select>
                            <select class="form-control filter-select" id="status-filter" onchange="filterSessions()">
                                <option value="all">All Status</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Session Details</th>
                                <th>Instructor</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sessions-tbody">
                            <!-- Coach Session Example -->
                            <tr data-type="coach" data-status="confirmed">
                                <td>
                                    <div class="table-cell-title">Batting Technique Session</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user-tie"></i> Coach Session
                                    </div>
                                    <span class="table-badge">Coach</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Coach Johnson</div>
                                    <div class="table-cell-secondary">Cricket Coach</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Oct 20, 2025</div>
                                    <div class="table-cell-secondary">9:00 AM - 10:00 AM</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Indoor Net 1</div>
                                    <div class="table-cell-secondary">Premium Facility</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn btn-sm" onclick="viewSession(1)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>

                            <!-- Trainer Session Example -->
                            <tr data-type="trainer" data-status="confirmed">
                                <td>
                                    <div class="table-cell-title">Strength & Conditioning</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Trainer Session
                                    </div>
                                    <span class="table-badge">Trainer</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Trainer Williams</div>
                                    <div class="table-cell-secondary">Fitness Trainer</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Oct 21, 2025</div>
                                    <div class="table-cell-secondary">6:00 AM - 7:00 AM</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Fitness Center</div>
                                    <div class="table-cell-secondary">Gym Facility</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn btn-sm" onclick="viewSession(2)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>

                            <!-- Pending Coach Session -->
                            <tr data-type="coach" data-status="pending">
                                <td>
                                    <div class="table-cell-title">Bowling Technique Session</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user-tie"></i> Coach Session
                                    </div>
                                    <span class="table-badge">Coach</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Coach Anderson</div>
                                    <div class="table-cell-secondary">Cricket Coach</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Oct 22, 2025</div>
                                    <div class="table-cell-secondary">2:00 PM - 3:00 PM</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Practice Ground B</div>
                                    <div class="table-cell-secondary">Outdoor Field</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-pending">Pending</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="action-btn btn-sm" onclick="makePayment(3)">
                                        <i class="fas fa-credit-card"></i> Pay
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Empty State -->
                    <div class="empty-state" id="empty-state" style="display: none;">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3>No Sessions Found</h3>
                        <p>No sessions match your current filter criteria.</p>
                        <button class="action-btn" onclick="clearFilters()">
                            <i class="fas fa-filter"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

   

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/bookings.js"></script>
</body>
</html>