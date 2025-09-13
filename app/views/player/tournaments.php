<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournaments - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/tournaments.css">
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
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training" class="nav-link"><i class="fas fa-dumbbell"></i><span>Training Schedule</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link"><i class="fas fa-calendar-alt"></i><span>My Bookings</span><span class="badge">2</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/performance" class="nav-link"><i class="fas fa-chart-bar"></i><span>Performance</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span><span class="badge">2</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping & Rental</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical Records</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link"><i class="fas fa-trophy"></i><span>Achievements</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link active"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-medal"></i> Tournaments</h1>
                <p>Participate in exciting cricket tournaments and championships</p>
            </div>

            <!-- Tournament Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon upcoming">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Upcoming Tournaments</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon enrolled">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Enrolled</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon completed">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon wins">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Tournament Wins</div>
                    </div>
                </div>
            </div>

            <!-- Tournament Filters -->
            <div class="filter-container">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <i class="fas fa-th"></i> All Tournaments
                    </button>
                    <button class="filter-tab" data-filter="upcoming">
                        <i class="fas fa-calendar-plus"></i> Upcoming
                    </button>
                    <button class="filter-tab" data-filter="enrolled">
                        <i class="fas fa-user-check"></i> My Enrollments
                    </button>
                    <button class="filter-tab" data-filter="completed">
                        <i class="fas fa-history"></i> Past Tournaments
                    </button>
                </div>
            </div>

            <!-- Tournaments Grid -->
            <div class="tournaments-grid">
                <!-- Upcoming Tournaments -->
                <div class="tournament-card upcoming" data-tournament-id="1">
                    <div class="tournament-header">
                        <div class="tournament-badge upcoming">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Upcoming</span>
                        </div>
                        <div class="tournament-level">
                            <i class="fas fa-star"></i>
                            <span>Intermediate</span>
                        </div>
                    </div>
                    
                    <div class="tournament-image">
                        <img src="<?php echo URLROOT; ?>/img/cricket-camps.jpg" alt="Tournament" class="tournament-img">
                        <div class="tournament-overlay">
                            <div class="tournament-date">
                                <div class="date-day">25</div>
                                <div class="date-month">Sep</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3 class="tournament-title">Elite Cricket Championship</h3>
                        <p class="tournament-description">Annual championship featuring teams from all levels. Compete for the ultimate cricket trophy.</p>
                        
                        <div class="tournament-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Main Stadium</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>16 Teams</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>2 Days</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-trophy"></i>
                                <span>$5,000 Prize</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-footer">
                        <div class="registration-fee">
                            <span class="fee-label">Registration Fee:</span>
                            <span class="fee-amount">$150</span>
                        </div>
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Enroll Now
                        </button>
                    </div>
                </div>

                <!-- Enrolled Tournament -->
                <div class="tournament-card enrolled" data-tournament-id="2">
                    <div class="tournament-header">
                        <div class="tournament-badge enrolled">
                            <i class="fas fa-user-check"></i>
                            <span>Enrolled</span>
                        </div>
                        <div class="tournament-level">
                            <i class="fas fa-star"></i>
                            <span>Beginner</span>
                        </div>
                    </div>
                    
                    <div class="tournament-image">
                        <img src="<?php echo URLROOT; ?>/img/outdoor.jpg" alt="Tournament" class="tournament-img">
                        <div class="tournament-overlay">
                            <div class="tournament-date">
                                <div class="date-day">30</div>
                                <div class="date-month">Sep</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3 class="tournament-title">Youth Cricket League</h3>
                        <p class="tournament-description">Perfect for young players to showcase their skills in a competitive environment.</p>
                        
                        <div class="tournament-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Practice Ground</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>12 Teams</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>1 Day</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-trophy"></i>
                                <span>Medals & Trophies</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-footer">
                        <div class="enrollment-status">
                            <i class="fas fa-check-circle"></i>
                            <span>Successfully Enrolled</span>
                        </div>
                        <button class="btn btn-outline">
                            <i class="fas fa-info-circle"></i> View Details
                        </button>
                    </div>
                </div>

                <!-- Another Upcoming Tournament -->
                <div class="tournament-card upcoming" data-tournament-id="3">
                    <div class="tournament-header">
                        <div class="tournament-badge upcoming">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Upcoming</span>
                        </div>
                        <div class="tournament-level">
                            <i class="fas fa-star"></i>
                            <span>Advanced</span>
                        </div>
                    </div>
                    
                    <div class="tournament-image">
                        <img src="<?php echo URLROOT; ?>/img/nets.jpg" alt="Tournament" class="tournament-img">
                        <div class="tournament-overlay">
                            <div class="tournament-date">
                                <div class="date-day">05</div>
                                <div class="date-month">Oct</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3 class="tournament-title">Masters Cup Tournament</h3>
                        <p class="tournament-description">Elite level tournament for experienced players seeking the ultimate challenge.</p>
                        
                        <div class="tournament-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Championship Ground</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>8 Teams</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>3 Days</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-trophy"></i>
                                <span>$10,000 Prize</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-footer">
                        <div class="registration-fee">
                            <span class="fee-label">Registration Fee:</span>
                            <span class="fee-amount">$250</span>
                        </div>
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Enroll Now
                        </button>
                    </div>
                </div>

                <!-- Past Tournament -->
                <div class="tournament-card completed" data-tournament-id="4">
                    <div class="tournament-header">
                        <div class="tournament-badge completed">
                            <i class="fas fa-check-circle"></i>
                            <span>Completed</span>
                        </div>
                        <div class="tournament-result winner">
                            <i class="fas fa-trophy"></i>
                            <span>Winner</span>
                        </div>
                    </div>
                    
                    <div class="tournament-image">
                        <img src="<?php echo URLROOT; ?>/img/hero1.jpg" alt="Tournament" class="tournament-img">
                        <div class="tournament-overlay">
                            <div class="tournament-date">
                                <div class="date-day">15</div>
                                <div class="date-month">Aug</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3 class="tournament-title">Summer Cricket Festival</h3>
                        <p class="tournament-description">A fantastic summer tournament celebrating the spirit of cricket with great competition.</p>
                        
                        <div class="tournament-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Main Stadium</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>20 Teams</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>4 Days</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-trophy"></i>
                                <span>Champion</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-footer">
                        <div class="tournament-achievement">
                            <i class="fas fa-medal"></i>
                            <span>1st Place - Champion</span>
                        </div>
                        <button class="btn btn-outline">
                            <i class="fas fa-download"></i> Certificate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tournament Details Modal -->
    <div class="modal" id="tournamentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-medal"></i> Tournament Details</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="tournamentDetails">
                    <!-- Tournament details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Player Layout -->

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/tournaments.js"></script>
</body>
</html>