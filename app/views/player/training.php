<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<body>
    <!-- Player Dashboard Layout -->
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
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link active">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training Schedule</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping & Rental</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment History</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Player Profile -->
            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name']; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level']; ?> Member</div>
                </div>
                <div class="logout-btn">
                    <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Training Schedule Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-dumbbell"></i> Training Schedule</h1>
                    <p>Your personalized training sessions and coaching schedule</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Request Session
                    </button>
                </div>
            </div>

            <!-- Training Sessions -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-week"></i> Upcoming Training Sessions</h2>
                    <div style="display: flex; gap: 1rem;">
                        <select class="btn btn-outline" style="border: 2px solid #4A90E2;">
                            <option>This Week</option>
                            <option>Next Week</option>
                            <option>This Month</option>
                        </select>
                    </div>
                </div>
                
                <?php if(!empty($data['trainingSessions'])): ?>
                    <div class="cards-grid">
                        <?php foreach($data['trainingSessions'] as $session): ?>
                            <div class="info-card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                    <h3>
                                        <i class="fas fa-<?php echo $session['type'] == 'Batting' ? 'baseball-ball' : ($session['type'] == 'Bowling' ? 'bowling-ball' : 'dumbbell'); ?>"></i> 
                                        <?php echo $session['type']; ?> Training
                                    </h3>
                                    <span class="status-badge status-active">Confirmed</span>
                                </div>
                                
                                <div style="margin-bottom: 1rem;">
                                    <p><i class="fas fa-calendar"></i> <strong><?php echo date('l, M j, Y', strtotime($session['date'])); ?></strong></p>
                                    <p><i class="fas fa-clock"></i> <?php echo $session['time']; ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $session['location']; ?></p>
                                    <p><i class="fas fa-user-tie"></i> <?php echo $session['coach']; ?></p>
                                </div>
                                
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="btn btn-outline btn-sm" style="flex: 1;">
                                        <i class="fas fa-edit"></i> Reschedule
                                    </button>
                                    <button class="btn btn-secondary btn-sm" style="flex: 1;">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: #666;">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>No training sessions scheduled</p>
                        <button class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i> Schedule Training Session
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Training History -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Recent Training History</h2>
                    <a href="#" class="view-all-btn">View All History</a>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Coach</th>
                            <th>Duration</th>
                            <th>Performance</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sep 5, 2025</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-baseball-ball" style="color: #4A90E2;"></i>
                                    Batting
                                </div>
                            </td>
                            <td>Coach Wilson</td>
                            <td>90 minutes</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #e9ecef;"></i>
                                </div>
                            </td>
                            <td>Excellent footwork improvement</td>
                        </tr>
                        <tr>
                            <td>Sep 3, 2025</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-dumbbell" style="color: #4A90E2;"></i>
                                    Fitness
                                </div>
                            </td>
                            <td>Trainer Mike</td>
                            <td>60 minutes</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                </div>
                            </td>
                            <td>Outstanding endurance gains</td>
                        </tr>
                        <tr>
                            <td>Sep 1, 2025</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-bowling-ball" style="color: #4A90E2;"></i>
                                    Bowling
                                </div>
                            </td>
                            <td>Coach Sarah</td>
                            <td>75 minutes</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <i class="fas fa-star" style="color: #e9ecef;"></i>
                                </div>
                            </td>
                            <td>Good line and length control</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Training Programs -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-graduation-cap"></i> Available Training Programs</h2>
                </div>
                
                <div class="cards-grid">
                    <div class="info-card">
                        <h3><i class="fas fa-baseball-ball"></i> Advanced Batting Program</h3>
                        <p>Intensive batting technique improvement with focus on shot selection and timing.</p>
                        <div style="margin: 1rem 0;">
                            <span class="status-badge" style="background-color: #e3f2fd; color: #1976d2;">12 weeks</span>
                            <span class="status-badge" style="background-color: #f3e5f5; color: #7b1fa2; margin-left: 0.5rem;">$450/month</span>
                        </div>
                        <button class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-plus"></i> Enroll Now
                        </button>
                    </div>

                    <div class="info-card">
                        <h3><i class="fas fa-bowling-ball"></i> Fast Bowling Mastery</h3>
                        <p>Develop pace, accuracy and various bowling techniques with professional coaches.</p>
                        <div style="margin: 1rem 0;">
                            <span class="status-badge" style="background-color: #e3f2fd; color: #1976d2;">16 weeks</span>
                            <span class="status-badge" style="background-color: #f3e5f5; color: #7b1fa2; margin-left: 0.5rem;">$500/month</span>
                        </div>
                        <button class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-plus"></i> Enroll Now
                        </button>
                    </div>

                    <div class="info-card">
                        <h3><i class="fas fa-running"></i> Fitness & Conditioning</h3>
                        <p>Complete fitness program designed specifically for cricket players.</p>
                        <div style="margin: 1rem 0;">
                            <span class="status-badge" style="background-color: #e3f2fd; color: #1976d2;">8 weeks</span>
                            <span class="status-badge" style="background-color: #f3e5f5; color: #7b1fa2; margin-left: 0.5rem;">$300/month</span>
                        </div>
                        <button class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-plus"></i> Enroll Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    
    <style>
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
    </style>
</body>

</html>
