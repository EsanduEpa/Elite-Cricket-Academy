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
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
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
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link active">
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
            <!-- Performance Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-chart-line"></i> Performance Analytics</h1>
                    <p>Comprehensive analysis of your cricket performance and progress</p>
                </div>
                <div class="header-actions">
                    <select class="btn btn-outline" style="border: 2px solid #4A90E2; margin-right: 1rem;">
                        <option>Last 3 Months</option>
                        <option>Last 6 Months</option>
                        <option>Last Year</option>
                        <option>All Time</option>
                    </select>
                    <button class="btn btn-primary">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </div>

            <!-- Detailed Performance Statistics -->
            <div class="stats-grid">
                <!-- Batting Stats -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-baseball-ball"></i>
                            Batting Average
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['batting']['average']; ?>">0</div>
                    <div class="stat-label">Current Average</div>
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                        <div>Highest Score: <?php echo $data['performanceStats']['batting']['highest_score']; ?></div>
                        <div>Centuries: <?php echo $data['performanceStats']['batting']['centuries']; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-running"></i>
                            Strike Rate
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['batting']['strike_rate']; ?>">0</div>
                    <div class="stat-label">Runs per 100 balls</div>
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                        <div>Half Centuries: <?php echo $data['performanceStats']['batting']['half_centuries']; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-bowling-ball"></i>
                            Bowling Average
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['bowling']['average']; ?>">0</div>
                    <div class="stat-label">Runs per wicket</div>
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #666;">
                        <div>Best: <?php echo $data['performanceStats']['bowling']['best_figures']; ?></div>
                        <div>5 Wickets: <?php echo $data['performanceStats']['bowling']['five_wickets']; ?></div>
                    </div>
                </div>

                <div class="stat-card performance-stat-card">
                    <div class="stat-header">
                        <div class="stat-title">
                            <i class="fas fa-chart-bar"></i>
                            Economy Rate
                        </div>
                    </div>
                    <div class="stat-value" data-target="<?php echo $data['performanceStats']['bowling']['economy_rate']; ?>">0</div>
                    <div class="stat-label">Runs per over</div>
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: rgba(255,255,255,0.8);">
                        <div>4 Wickets: <?php echo $data['performanceStats']['bowling']['four_wickets']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Practice Matches -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-baseball-ball"></i> Recent Practice Matches</h2>
                    <a href="#" class="view-all-btn">View All Matches</a>
                </div>
                
                <div class="cards-grid">
                    <?php foreach($data['practiceMatches'] as $match): ?>
                        <div class="info-card">
                            <div style="display: flex; justify-content: between; align-items: flex-start; margin-bottom: 1rem;">
                                <h3><i class="fas fa-vs"></i> vs <?php echo $match['opponent']; ?></h3>
                                <span class="status-badge <?php echo $match['result'] == 'Win' ? 'status-paid' : 'status-due'; ?>">
                                    <?php echo $match['result']; ?>
                                </span>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-weight: 600; color: #4A90E2; font-size: 1.5rem;">
                                        <?php echo $match['runs']; ?>
                                    </div>
                                    <div style="color: #666; font-size: 0.9rem;">Runs Scored</div>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #4A90E2; font-size: 1.5rem;">
                                        <?php echo $match['wickets']; ?>
                                    </div>
                                    <div style="color: #666; font-size: 0.9rem;">Wickets Taken</div>
                                </div>
                            </div>
                            
                            <p style="color: #666; font-size: 0.9rem;">
                                <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($match['date'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tournament Performance -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-trophy"></i> Tournament Performance</h2>
                    <a href="<?php echo URLROOT; ?>/player/achievements" class="view-all-btn">View Achievements</a>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Position</th>
                            <th>Runs</th>
                            <th>Wickets</th>
                            <th>Performance Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['tournaments'] as $tournament): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo $tournament['name']; ?></div>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $tournament['position'] == '1st Place' ? 'status-paid' : ($tournament['position'] == '2nd Place' ? 'status-pending' : 'status-active'); ?>">
                                        <?php echo $tournament['position']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #4A90E2;"><?php echo $tournament['runs']; ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #4A90E2;"><?php echo $tournament['wickets']; ?></div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <?php 
                                        $rating = $tournament['position'] == '1st Place' ? 5 : ($tournament['position'] == '2nd Place' ? 4 : 3);
                                        for($i = 1; $i <= 5; $i++): 
                                        ?>
                                            <i class="fas fa-star" style="color: <?php echo $i <= $rating ? '#ffc107' : '#e9ecef'; ?>;"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Performance Analysis -->
            <div class="cards-grid">
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-chart-pie"></i> Batting Analysis</h2>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #4A90E2; margin-bottom: 0.5rem;">
                                <?php echo number_format($data['performanceStats']['batting']['total_runs']); ?>
                            </div>
                            <div style="color: #666; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">
                                Total Runs
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #4A90E2; margin-bottom: 0.5rem;">
                                <?php echo $data['performanceStats']['batting']['centuries']; ?>
                            </div>
                            <div style="color: #666; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">
                                Centuries
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress-bars">
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Consistency</span>
                                <span>85%</span>
                            </div>
                            <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: 85%; background: linear-gradient(90deg, #4A90E2, #357ABD); transition: width 1s ease;"></div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Shot Selection</span>
                                <span>92%</span>
                            </div>
                            <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: 92%; background: linear-gradient(90deg, #28a745, #20c997); transition: width 1s ease;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-bullseye"></i> Bowling Analysis</h2>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #4A90E2; margin-bottom: 0.5rem;">
                                <?php echo $data['performanceStats']['bowling']['total_wickets']; ?>
                            </div>
                            <div style="color: #666; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">
                                Total Wickets
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #4A90E2; margin-bottom: 0.5rem;">
                                <?php echo $data['performanceStats']['bowling']['five_wickets']; ?>
                            </div>
                            <div style="color: #666; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">
                                5-Wicket Hauls
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress-bars">
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Line & Length</span>
                                <span>88%</span>
                            </div>
                            <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: 88%; background: linear-gradient(90deg, #4A90E2, #357ABD); transition: width 1s ease;"></div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Variation</span>
                                <span>78%</span>
                            </div>
                            <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: 78%; background: linear-gradient(90deg, #ffc107, #fd7e14); transition: width 1s ease;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
</body>

</html>
