<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/bookings.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>
    
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
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
                            <span>Training Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link active">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
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
                            <span>Shopping & Rental</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Profile Section at Bottom -->
                <div class="sidebar-profile">
                    <a href="<?php echo URLROOT; ?>/player/profile" class="profile-link">
                        <div class="profile-avatar">
                            <img src="<?php echo URLROOT; ?>/img/default-avatar.jpg" alt="Profile" id="profileAvatar">
                        </div>
                        <div class="profile-info">
                            <span class="profile-name">John Doe</span>
                            <span class="profile-role">Player</span>
                        </div>
                        <i class="fas fa-cog profile-settings"></i>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="content-header">
                <div class="header-title">
                    <h1><i class="fas fa-calendar-check"></i> My Bookings</h1>
                    <p>Manage your trainer and coach appointments</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" id="newBookingBtn">
                        <i class="fas fa-plus"></i> New Booking
                    </button>
                </div>
            </div>

            <!-- Booking Categories -->
            <div class="booking-categories">
                <div class="category-tabs">
                    <button class="tab-btn active" data-category="upcoming">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Upcoming</span>
                        <span class="badge">3</span>
                    </button>
                    <button class="tab-btn" data-category="past">
                        <i class="fas fa-history"></i>
                        <span>Past Bookings</span>
                        <span class="badge">12</span>
                    </button>
                    <button class="tab-btn" data-category="cancelled">
                        <i class="fas fa-times-circle"></i>
                        <span>Cancelled</span>
                        <span class="badge">2</span>
                    </button>
                </div>
            </div>

            <!-- Booking Content -->
            <div class="booking-content">
                <!-- Upcoming Bookings -->
                <div class="booking-section active" id="upcoming">
                    <div class="bookings-grid">
                        <!-- Upcoming Coach Appointment -->
                        <div class="booking-card coach-booking upcoming">
                            <div class="booking-header">
                                <div class="booking-type">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Coach Appointment</span>
                                </div>
                                <div class="booking-status upcoming">
                                    <i class="fas fa-clock"></i>
                                    <span>Upcoming</span>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="instructor-info">
                                    <img src="<?php echo URLROOT; ?>/img/coach1.jpg" alt="Coach" class="instructor-avatar">
                                    <div class="instructor-details">
                                        <h3>Coach Anderson</h3>
                                        <p>Senior Cricket Coach</p>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>4.9</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="session-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>September 15, 2025</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span>10:00 AM - 11:00 AM</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Ground A</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Batting Technique Review</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-footer">
                                <button class="btn btn-outline">
                                    <i class="fas fa-edit"></i> Reschedule
                                </button>
                                <button class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-video"></i> Join Session
                                </button>
                            </div>
                        </div>

                        <!-- Upcoming Trainer Appointment -->
                        <div class="booking-card trainer-booking upcoming">
                            <div class="booking-header">
                                <div class="booking-type">
                                    <i class="fas fa-dumbbell"></i>
                                    <span>Trainer Appointment</span>
                                </div>
                                <div class="booking-status upcoming">
                                    <i class="fas fa-clock"></i>
                                    <span>Tomorrow</span>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="instructor-info">
                                    <img src="<?php echo URLROOT; ?>/img/default-avatar.jpg" alt="Trainer" class="instructor-avatar">
                                    <div class="instructor-details">
                                        <h3>Mike Johnson</h3>
                                        <p>Fitness Trainer</p>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span>4.7</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="session-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>September 14, 2025</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span>6:00 AM - 7:00 AM</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Fitness Center</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Strength Training</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-footer">
                                <button class="btn btn-outline">
                                    <i class="fas fa-edit"></i> Reschedule
                                </button>
                                <button class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-play"></i> Start Session
                                </button>
                            </div>
                        </div>

                        <!-- Upcoming Group Session -->
                        <div class="booking-card group-booking upcoming">
                            <div class="booking-header">
                                <div class="booking-type">
                                    <i class="fas fa-users"></i>
                                    <span>Group Session</span>
                                </div>
                                <div class="booking-status upcoming">
                                    <i class="fas fa-clock"></i>
                                    <span>This Week</span>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="instructor-info">
                                    <img src="<?php echo URLROOT; ?>/img/coach2.jpg" alt="Coach" class="instructor-avatar">
                                    <div class="instructor-details">
                                        <h3>Coach Wilson</h3>
                                        <p>Bowling Specialist</p>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>5.0</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="session-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>September 18, 2025</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span>4:00 PM - 6:00 PM</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Practice Ground</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Bowling Masterclass</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-footer">
                                <button class="btn btn-outline">
                                    <i class="fas fa-info-circle"></i> View Details
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-check"></i> Confirmed
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Past Bookings -->
                <div class="booking-section" id="past">
                    <div class="bookings-grid">
                        <div class="booking-card coach-booking completed">
                            <div class="booking-header">
                                <div class="booking-type">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Coach Appointment</span>
                                </div>
                                <div class="booking-status completed">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Completed</span>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="instructor-info">
                                    <img src="<?php echo URLROOT; ?>/img/coach1.jpg" alt="Coach" class="instructor-avatar">
                                    <div class="instructor-details">
                                        <h3>Coach Anderson</h3>
                                        <p>Senior Cricket Coach</p>
                                    </div>
                                </div>
                                
                                <div class="session-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>September 10, 2025</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span>2:00 PM - 3:00 PM</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Performance Review</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-footer">
                                <button class="btn btn-outline">
                                    <i class="fas fa-star"></i> Rate Session
                                </button>
                                <button class="btn btn-outline">
                                    <i class="fas fa-redo"></i> Book Again
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-file-alt"></i> View Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancelled Bookings -->
                <div class="booking-section" id="cancelled">
                    <div class="bookings-grid">
                        <div class="booking-card trainer-booking cancelled">
                            <div class="booking-header">
                                <div class="booking-type">
                                    <i class="fas fa-dumbbell"></i>
                                    <span>Trainer Appointment</span>
                                </div>
                                <div class="booking-status cancelled">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Cancelled</span>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="instructor-info">
                                    <img src="<?php echo URLROOT; ?>/img/default-avatar.jpg" alt="Trainer" class="instructor-avatar">
                                    <div class="instructor-details">
                                        <h3>Sarah Thompson</h3>
                                        <p>Fitness Trainer</p>
                                    </div>
                                </div>
                                
                                <div class="session-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>September 8, 2025</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span>7:00 AM - 8:00 AM</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Cancelled by trainer</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-footer">
                                <button class="btn btn-outline">
                                    <i class="fas fa-redo"></i> Rebook
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-receipt"></i> Refund Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <i class="fas fa-calendar-times"></i>
                <h3>No bookings found</h3>
                <p>You haven't made any bookings in this category yet.</p>
                <button class="btn btn-primary" id="createFirstBooking">
                    <i class="fas fa-plus"></i> Make Your First Booking
                </button>
            </div>
        </div>
        <!-- End Main Content -->
    </div>
    <!-- End Player Layout -->

  

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/bookings.js"></script>
</body>
</html>
