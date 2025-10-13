<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/achievements.css">
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
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link active"><i class="fas fa-trophy"></i><span>Achievements</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="content-header">
                <div class="header-title">
                    <h1><i class="fas fa-trophy"></i> Achievements & Awards</h1>
                    <p>Celebrate your cricket milestones, records, and accomplishments</p>
                </div>
             
            </div>

            <!-- Achievement Summary -->
            <div class="achievement-summary">
                <div class="summary-card">
                    <div class="summary-icon trophies">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">15</div>
                        <div class="summary-label">Trophies Won</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon records">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">8</div>
                        <div class="summary-label">Personal Records</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon certificates">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">12</div>
                        <div class="summary-label">Certificates</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon badges">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">24</div>
                        <div class="summary-label">Achievement Badges</div>
                    </div>
                </div>
            </div>

            <!-- Achievement Categories -->
            <div class="achievement-nav">
                <button class="achievement-tab active" data-category="all">
                    <i class="fas fa-th-large"></i>
                    All Achievements
                </button>
                <button class="achievement-tab" data-category="trophies">
                    <i class="fas fa-trophy"></i>
                    Trophies & Awards
                </button>
                <button class="achievement-tab" data-category="records">
                    <i class="fas fa-star"></i>
                    Personal Records
                </button>
                <button class="achievement-tab" data-category="certificates">
                    <i class="fas fa-certificate"></i>
                    Certificates
                </button>
                <button class="achievement-tab" data-category="badges">
                    <i class="fas fa-medal"></i>
                    Skill Badges
                </button>
            </div>

            <!-- Trophy Gallery -->
            <div class="achievement-section active" id="trophies" data-category="trophies">
                <div class="section-header">
                    <h2>Trophy Gallery</h2>
                    <div class="section-filters">
                        <select class="filter-select">
                            <option value="all">All Years</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                </div>

                <div class="trophies-grid">
                    <div class="trophy-card gold">
                        <div class="trophy-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="trophy-content">
                            <h3>Spring Tournament Champion</h3>
                            <div class="trophy-details">
                                <p class="trophy-event">Elite Cricket Academy Spring Championship</p>
                                <p class="trophy-date">May 20, 2025</p>
                                <p class="trophy-achievement">First Place - Outstanding performance with 340 runs</p>
                            </div>
                            <div class="trophy-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Runs Scored</span>
                                    <span class="stat-value">340</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Matches</span>
                                    <span class="stat-value">6</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Average</span>
                                    <span class="stat-value">68.0</span>
                                </div>
                            </div>
                        </div>
                        <div class="trophy-badge">
                            <span class="badge-text">1st Place</span>
                        </div>
                    </div>

                    <div class="trophy-card silver">
                        <div class="trophy-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="trophy-content">
                            <h3>Summer Championship</h3>
                            <div class="trophy-details">
                                <p class="trophy-event">Regional Summer Cricket Championship</p>
                                <p class="trophy-date">August 15, 2025</p>
                                <p class="trophy-achievement">Second Place - Excellent team performance</p>
                            </div>
                            <div class="trophy-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Runs Scored</span>
                                    <span class="stat-value">285</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Matches</span>
                                    <span class="stat-value">8</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Average</span>
                                    <span class="stat-value">47.5</span>
                                </div>
                            </div>
                        </div>
                        <div class="trophy-badge silver">
                            <span class="badge-text">2nd Place</span>
                        </div>
                    </div>

                    <div class="trophy-card bronze">
                        <div class="trophy-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="trophy-content">
                            <h3>District Cup</h3>
                            <div class="trophy-details">
                                <p class="trophy-event">Inter-District Cricket Championship</p>
                                <p class="trophy-date">March 10, 2025</p>
                                <p class="trophy-achievement">Third Place - Best bowling figures</p>
                            </div>
                            <div class="trophy-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Wickets</span>
                                    <span class="stat-value">15</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Matches</span>
                                    <span class="stat-value">5</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Economy</span>
                                    <span class="stat-value">3.2</span>
                                </div>
                            </div>
                        </div>
                        <div class="trophy-badge bronze">
                            <span class="badge-text">3rd Place</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Records -->
            <div class="achievement-section" id="records" data-category="records">
                <div class="section-header">
                    <h2>Personal Records & Milestones</h2>
                </div>

                <div class="records-grid">
                    <div class="record-card batting">
                        <div class="record-icon">
                            <i class="fas fa-baseball-bat"></i>
                        </div>
                        <div class="record-content">
                            <h3>Highest Individual Score</h3>
                            <div class="record-value">156 runs</div>
                            <div class="record-details">
                                <p>vs Thunder Hawks</p>
                                <p>June 15, 2025</p>
                                <p>Match-winning innings with 12 fours and 4 sixes</p>
                            </div>
                        </div>
                        <div class="record-progress">
                            <div class="progress-ring">
                                <svg width="60" height="60">
                                    <circle cx="30" cy="30" r="25" stroke="#e2e8f0" stroke-width="4" fill="none"/>
                                    <circle cx="30" cy="30" r="25" stroke="#4A90E2" stroke-width="4" fill="none" 
                                            stroke-dasharray="157" stroke-dashoffset="31"/>
                                </svg>
                                <div class="progress-text">80%</div>
                            </div>
                        </div>
                    </div>

                    <div class="record-card bowling">
                        <div class="record-icon">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="record-content">
                            <h3>Best Bowling Figures</h3>
                            <div class="record-value">6/25</div>
                            <div class="record-details">
                                <p>vs Storm Eagles</p>
                                <p>May 20, 2025</p>
                                <p>Career-best bowling performance in tournament final</p>
                            </div>
                        </div>
                        <div class="record-progress">
                            <div class="progress-ring">
                                <svg width="60" height="60">
                                    <circle cx="30" cy="30" r="25" stroke="#e2e8f0" stroke-width="4" fill="none"/>
                                    <circle cx="30" cy="30" r="25" stroke="#22c55e" stroke-width="4" fill="none" 
                                            stroke-dasharray="157" stroke-dashoffset="47"/>
                                </svg>
                                <div class="progress-text">70%</div>
                            </div>
                        </div>
                    </div>

                    <div class="record-card fielding">
                        <div class="record-icon">
                            <i class="fas fa-hand-rock"></i>
                        </div>
                        <div class="record-content">
                            <h3>Most Catches in Match</h3>
                            <div class="record-value">5 catches</div>
                            <div class="record-details">
                                <p>vs Lightning Bolts</p>
                                <p>April 8, 2025</p>
                                <p>Outstanding fielding display in crucial match</p>
                            </div>
                        </div>
                        <div class="record-progress">
                            <div class="progress-ring">
                                <svg width="60" height="60">
                                    <circle cx="30" cy="30" r="25" stroke="#e2e8f0" stroke-width="4" fill="none"/>
                                    <circle cx="30" cy="30" r="25" stroke="#f59e0b" stroke-width="4" fill="none" 
                                            stroke-dasharray="157" stroke-dashoffset="63"/>
                                </svg>
                                <div class="progress-text">60%</div>
                            </div>
                        </div>
                    </div>

                    <div class="record-card runs">
                        <div class="record-icon">
                            <i class="fas fa-running"></i>
                        </div>
                        <div class="record-content">
                            <h3>Most Runs in Tournament</h3>
                            <div class="record-value">340 runs</div>
                            <div class="record-details">
                                <p>Spring Championship 2025</p>
                                <p>Tournament MVP</p>
                                <p>Consistent performance throughout the tournament</p>
                            </div>
                        </div>
                        <div class="record-progress">
                            <div class="progress-ring">
                                <svg width="60" height="60">
                                    <circle cx="30" cy="30" r="25" stroke="#e2e8f0" stroke-width="4" fill="none"/>
                                    <circle cx="30" cy="30" r="25" stroke="#8A2BE2" stroke-width="4" fill="none" 
                                            stroke-dasharray="157" stroke-dashoffset="16"/>
                                </svg>
                                <div class="progress-text">90%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificates Section -->
            <div class="achievement-section" id="certificates" data-category="certificates">
                <div class="section-header">
                    <h2>Certificates & Qualifications</h2>
                </div>

                <div class="certificates-grid">
                    <div class="certificate-card">
                        <div class="certificate-ribbon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="certificate-content">
                            <h3>Level 3 Coaching Certificate</h3>
                            <div class="certificate-details">
                                <p class="issued-by">Cricket Board of Sri Lanka</p>
                                <p class="issue-date">March 15, 2025</p>
                                <p class="certificate-id">ID: CC-2025-001847</p>
                            </div>
                            <div class="certificate-actions">
                                <button class="btn btn-outline">
                                    <i class="fas fa-download"></i>
                                    Download
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-share"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="certificate-card">
                        <div class="certificate-ribbon">
                            <i class="fas fa-first-aid"></i>
                        </div>
                        <div class="certificate-content">
                            <h3>Sports First Aid Certificate</h3>
                            <div class="certificate-details">
                                <p class="issued-by">National Health Council</p>
                                <p class="issue-date">November 20, 2024</p>
                                <p class="certificate-id">ID: FA-2024-005632</p>
                            </div>
                            <div class="certificate-actions">
                                <button class="btn btn-outline">
                                    <i class="fas fa-download"></i>
                                    Download
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-share"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="certificate-card">
                        <div class="certificate-ribbon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="certificate-content">
                            <h3>Fitness Training Certificate</h3>
                            <div class="certificate-details">
                                <p class="issued-by">Elite Fitness Academy</p>
                                <p class="issue-date">September 10, 2024</p>
                                <p class="certificate-id">ID: FT-2024-002198</p>
                            </div>
                            <div class="certificate-actions">
                                <button class="btn btn-outline">
                                    <i class="fas fa-download"></i>
                                    Download
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-share"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skill Badges Section -->
            <div class="achievement-section" id="badges" data-category="badges">
                <div class="section-header">
                    <h2>Skill Badges & Recognition</h2>
                </div>

                <div class="badges-grid">
                    <div class="badge-card earned">
                        <div class="badge-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="badge-content">
                            <h3>Rising Star</h3>
                            <p class="badge-description">Achieved exceptional performance in junior tournaments</p>
                            <div class="badge-date">Earned: March 2024</div>
                            <div class="badge-level earned">Level 3</div>
                        </div>
                    </div>

                    <div class="badge-card earned">
                        <div class="badge-icon">
                            <i class="fas fa-cricket-bat"></i>
                        </div>
                        <div class="badge-content">
                            <h3>Batting Expert</h3>
                            <p class="badge-description">Mastered advanced batting techniques</p>
                            <div class="badge-date">Earned: February 2024</div>
                            <div class="badge-level earned">Level 2</div>
                        </div>
                    </div>

                    <div class="badge-card earned">
                        <div class="badge-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="badge-content">
                            <h3>Precision Player</h3>
                            <p class="badge-description">Consistently accurate shot placement</p>
                            <div class="badge-date">Earned: January 2024</div>
                            <div class="badge-level earned">Level 1</div>
                        </div>
                    </div>

                    <div class="badge-card locked">
                        <div class="badge-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="badge-content">
                            <h3>Century Maker</h3>
                            <p class="badge-description">Score a century in competitive match</p>
                            <div class="badge-progress">Progress: 87/100 runs</div>
                            <div class="badge-level locked">Level 4</div>
                        </div>
                    </div>

                    <div class="badge-card locked">
                        <div class="badge-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="badge-content">
                            <h3>Champion</h3>
                            <p class="badge-description">Win a major tournament championship</p>
                            <div class="badge-progress">Not yet achieved</div>
                            <div class="badge-level locked">Level 5</div>
                        </div>
                    </div>
                </div>
            </div>

            
            </div>
        </div>
    </div>
    <!-- End Player Layout -->

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/achievements.js"></script>
</body>
</html>