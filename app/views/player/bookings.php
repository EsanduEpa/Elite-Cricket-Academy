<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/bookings.css">
    
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
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-calendar"></i> My Bookings</h1>
                <p>Manage your training session bookings and view upcoming reservations.</p>
            </div>

            <!-- Booking Stats -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value">8</div>
                    <div class="stat-label">Active Bookings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">24h</div>
                    <div class="stat-label">Total Hours</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="stat-value">45</div>
                    <div class="stat-label">Past Sessions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-value">4.9</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>

            <!-- Quick Booking -->
            <div class="quick-actions">
                <h3>Quick Booking</h3>
                <div class="action-buttons">
                    <a href="#" class="action-btn" onclick="alert('Book net practice feature coming soon!')">
                        <i class="fas fa-baseball-ball"></i> Book Net Practice
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Book fitness session feature coming soon!')">
                        <i class="fas fa-dumbbell"></i> Book Fitness Session
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Book coaching session feature coming soon!')">
                        <i class="fas fa-user-tie"></i> Book Coaching
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Book ground facility feature coming soon!')">
                        <i class="fas fa-futbol"></i> Book Ground
                    </a>
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-alt"></i> Upcoming Bookings</h2>
                        <div class="filter-section">
                            <select id="upcomingSessionFilter" class="filter-dropdown" onchange="filterUpcomingBookings()">
                                <option value="all">All Session Types</option>
                                <option value="Training">Training</option>
                                <option value="Fitness">Fitness</option>
                                <option value="Private">Private</option>
                                <option value="Team">Team</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table" id="upcomingBookingsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 17</div>
                                    <div class="table-cell-secondary">Thursday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Net Practice Session</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-map-marker-alt"></i> Indoor Nets - Coach Johnson
                                    </div>
                                    <span class="table-badge">Training</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">12:00 PM</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 18</div>
                                    <div class="table-cell-secondary">Friday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Fitness Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Gym Facility - Strength & Cardio
                                    </div>
                                    <span class="table-badge">Fitness</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">2:30 PM</div>
                                    <div class="table-cell-secondary">4:00 PM</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 20</div>
                                    <div class="table-cell-secondary">Sunday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Private Coaching</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user-tie"></i> Outdoor Pitch - Coach Williams
                                    </div>
                                    <span class="table-badge">Private</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">9:00 AM</div>
                                    <div class="table-cell-secondary">10:30 AM</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 22</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Ground Practice</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> Main Ground - Team Practice
                                    </div>
                                    <span class="table-badge">Team</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">3:00 PM</div>
                                    <div class="table-cell-secondary">5:00 PM</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Facility Bookings -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-building"></i> Facility Bookings</h2>
                        <div class="filter-section">
                            <select id="facilityTypeFilter" class="filter-dropdown" onchange="filterFacilityBookings()">
                                <option value="all">All Facility Types</option>
                                <option value="Ground">Ground</option>
                                <option value="Indoor">Indoor</option>
                                <option value="Practice">Practice</option>
                            </select>
                        </div>
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="action-btn" onclick="showFacilityBookingFromShopping()" style="font-size: 14px; padding: 8px 12px;">
                            <i class="fas fa-plus"></i> Book Facility
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table" id="facilityBookingsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Facility</th>
                                <th>Time</th>
                                <th>Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 20</div>
                                    <div class="table-cell-secondary">Sunday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Main Cricket Ground</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> Team Practice Session
                                    </div>
                                    <span class="table-badge">Ground</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">4:00 PM</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">LKR 5,000</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-confirmed">Confirmed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 25</div>
                                    <div class="table-cell-secondary">Friday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Indoor Training Hall</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Individual Training
                                    </div>
                                    <span class="table-badge">Indoor</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">12:00 PM</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">LKR 4,000</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-pending">Pending</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 15</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Practice Ground A</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-baseball-ball"></i> Batting Practice
                                    </div>
                                    <span class="table-badge">Practice</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">4:00 PM</div>
                                    <div class="table-cell-secondary">6:00 PM</div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">LKR 3,000</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-completed">Completed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-history"></i> Recent Bookings</h2>
                        <div class="filter-section">
                            <select id="recentSessionFilter" class="filter-dropdown" onchange="filterRecentBookings()">
                                <option value="all">All Session Types</option>
                                <option value="Training">Training</option>
                                <option value="Fitness">Fitness</option>
                                <option value="Private">Private</option>
                                <option value="Team">Team</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table" id="recentBookingsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Rating</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 15</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Net Practice Session</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-comment"></i> Great batting practice session
                                    </div>
                                    <span class="table-badge">Training</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">5.0</div>
                                    <div class="table-cell-secondary">⭐⭐⭐⭐⭐</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-completed">Completed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 13</div>
                                    <div class="table-cell-secondary">Sunday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Fitness Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-comment"></i> Excellent workout intensity
                                    </div>
                                    <span class="table-badge">Fitness</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">5.0</div>
                                    <div class="table-cell-secondary">⭐⭐⭐⭐⭐</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-completed">Completed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 11</div>
                                    <div class="table-cell-secondary">Friday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Private Coaching</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-comment"></i> Improved batting technique
                                    </div>
                                    <span class="table-badge">Private</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">4.0</div>
                                    <div class="table-cell-secondary">⭐⭐⭐⭐</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-completed">Completed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Booking Actions -->
            <div class="quick-actions">
                <h3>Booking Management</h3>
                <div class="action-buttons">
                    <a href="#" class="action-btn" onclick="alert('View all bookings feature coming soon!')">
                        <i class="fas fa-list"></i> View All Bookings
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Booking history feature coming soon!')">
                        <i class="fas fa-history"></i> Booking History
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Cancel booking feature coming soon!')">
                        <i class="fas fa-times"></i> Cancel Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/bookings.js"></script>
</body>
</html>