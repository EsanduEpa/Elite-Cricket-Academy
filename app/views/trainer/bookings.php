<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/bookings.css?v=<?php echo time(); ?>">

<!-- Trainer Bookings Layout -->
<div class="trainer-layout">
    <!-- Sidebar -->
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
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Player Bookings</span>
                    </a>
                </li>
                
                
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/events" class="nav-link">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Events & Tournaments</span>
                    </a>
                </li>
                
               
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                        <i class="fas fa-apple-whole"></i>
                        <span>Nutrition Plans</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/fitness" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Fitness Programs</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/medical" class="nav-link">
                        <i class="fas fa-heart-pulse"></i>
                        <span>Medical Records</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Trainer Profile Section -->
        <div class="trainer-profile">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <div class="trainer-name"><?php echo isset($data['trainer_name']) ? $data['trainer_name'] : 'John Trainer'; ?></div>
                <div class="trainer-role">Physical Trainer</div>
            </div>
            <div class="logout-btn">
                <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-calendar-check"></i> Session Bookings</h1>
            <p>Manage player bookings for personal training sessions</p>
            <div class="header-actions">
                <button class="action-btn primary" onclick="addNewBooking()">
                    <i class="fas fa-plus"></i> Add Booking
                </button>
            </div>
        </div>

        <!-- Booking Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Today's Sessions</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">18</div>
                    <div class="stat-label">Hours Booked</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">142</div>
                    <div class="stat-label">Active Clients</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Week Utilization</div>
                </div>
            </div>
        </div>

        <!-- Today's Sessions -->
        <div class="today-sessions-section">
            <div class="schedule-card today-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-day"></i> Today's Sessions</h2>
                        <span class="date-display"><?php echo date('M j, Y'); ?></span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Client & Session</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">6:00 AM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Sarah Mitchell</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Strength Training
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Gym A</div>
                                    <div class="table-cell-secondary">Weight Room</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Active</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon success" onclick="markCompleted(1)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon" onclick="viewNotes(1)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">7:30 AM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Mike Johnson</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-heartbeat"></i> Cardio Training
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Cardio Zone</div>
                                    <div class="table-cell-secondary">Treadmills & Bikes</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Upcoming</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon success" onclick="markCompleted(2)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon" onclick="viewNotes(2)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Emma Davis</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-leaf"></i> Flexibility & Recovery
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Yoga Studio</div>
                                    <div class="table-cell-secondary">Recovery Room</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Planned</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon success" onclick="markCompleted(3)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon" onclick="viewNotes(3)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">1.5 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Alex Rodriguez</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-running"></i> Sports-Specific Training
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Field Area</div>
                                    <div class="table-cell-secondary">Training Ground</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Planned</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon success" onclick="markCompleted(4)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon" onclick="viewNotes(4)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">4:00 PM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Lisa Thompson</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-medkit"></i> Injury Rehabilitation
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">Therapy Room</div>
                                    <div class="table-cell-secondary">Rehabilitation Area</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Planned</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon success" onclick="markCompleted(5)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon" onclick="viewNotes(5)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="upcoming-sessions-section">
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-check"></i> Upcoming Sessions This Week</h2>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client & Session</th>
                                <th>Duration & Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 10</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                    <div class="table-cell-details">8:00 AM</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">James Wilson</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Strength & Conditioning
                                    </div>
                                    <span class="table-badge">Cricket Player</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">1.5 hours</div>
                                    <div class="table-cell-secondary">Gym B - Weight Room</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Confirmed</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon" onclick="editBooking(6)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon danger" onclick="cancelBooking(6)" title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 11</div>
                                    <div class="table-cell-secondary">Wednesday</div>
                                    <div class="table-cell-details">6:30 AM</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Rachel Green</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-running"></i> Agility Training
                                    </div>
                                    <span class="table-badge">Tennis Player</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">1 hour</div>
                                    <div class="table-cell-secondary">Court 2 - Outdoor</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Confirmed</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon" onclick="editBooking(7)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon danger" onclick="cancelBooking(7)" title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 12</div>
                                    <div class="table-cell-secondary">Thursday</div>
                                    <div class="table-cell-details">3:00 PM</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">David Chen</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-medkit"></i> Recovery Session
                                    </div>
                                    <span class="table-badge">Football Player</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">45 minutes</div>
                                    <div class="table-cell-secondary">Recovery Room</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Pending</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon" onclick="editBooking(8)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon danger" onclick="cancelBooking(8)" title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 13</div>
                                    <div class="table-cell-secondary">Friday</div>
                                    <div class="table-cell-details">9:00 AM</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Sophie Martinez</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-heartbeat"></i> Cardio & Endurance
                                    </div>
                                    <span class="table-badge">Soccer Player</span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">2 hours</div>
                                    <div class="table-cell-secondary">Cardio Zone & Track</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Planned</span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-icon" onclick="editBooking(9)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon danger" onclick="cancelBooking(9)" title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Booking Actions -->
        <div class="quick-actions-section">
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h3>Quick Booking</h3>
                    <p>Schedule a new training session</p>
                    <button class="action-btn" onclick="addNewBooking()">Book Session</button>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Check Availability</h3>
                    <p>View your available time slots</p>
                    <button class="action-btn" onclick="checkAvailability()">Check Times</button>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h3>Export Bookings</h3>
                    <p>Download bookings report</p>
                    <button class="action-btn" onclick="exportBookings()">Export</button>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/bookings.js"></script>

<script>
// Bookings page specific JavaScript
function addNewBooking() {
    alert('Add New Booking functionality coming soon!');
}

function markCompleted(bookingId) {
    alert(`Mark booking ${bookingId} as completed - functionality coming soon!`);
}

function viewNotes(bookingId) {
    alert(`View notes for booking ${bookingId} - functionality coming soon!`);
}

function editBooking(bookingId) {
    alert(`Edit booking ${bookingId} - functionality coming soon!`);
}

function cancelBooking(bookingId) {
    if(confirm('Are you sure you want to cancel this booking?')) {
        alert(`Cancel booking ${bookingId} - functionality coming soon!`);
    }
}

function checkAvailability() {
    alert('Check Availability functionality coming soon!');
}

function exportBookings() {
    alert('Export Bookings functionality coming soon!');
}
</script>
</body>
</html>