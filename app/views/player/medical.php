<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/medical.css">
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
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link active"><i class="fas fa-heartbeat"></i><span>Medical Records</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link"><i class="fas fa-trophy"></i><span>Achievements</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="content-header">
                <div class="header-title">
                    <h1><i class="fas fa-heartbeat"></i> Medical Records</h1>
                    <p>Track your health, fitness assessments, and medical history</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary add-record">
                        <i class="fas fa-plus"></i>
                        Add Medical Record
                    </button>
                </div>
            </div>

            <!-- Health Overview -->
            <div class="health-overview">
                <div class="health-card vital-signs">
                    <div class="card-header">
                        <h3><i class="fas fa-heartbeat"></i> Vital Signs</h3>
                        <span class="last-updated">Last updated: Sep 10, 2025</span>
                    </div>
                    <div class="vitals-grid">
                        <div class="vital-item">
                            <div class="vital-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div class="vital-info">
                                <div class="vital-label">Heart Rate</div>
                                <div class="vital-value">72 <span>bpm</span></div>
                                <div class="vital-status normal">Normal</div>
                            </div>
                        </div>
                        <div class="vital-item">
                            <div class="vital-icon">
                                <i class="fas fa-thermometer-half"></i>
                            </div>
                            <div class="vital-info">
                                <div class="vital-label">Blood Pressure</div>
                                <div class="vital-value">120/80 <span>mmHg</span></div>
                                <div class="vital-status normal">Normal</div>
                            </div>
                        </div>
                        <div class="vital-item">
                            <div class="vital-icon">
                                <i class="fas fa-weight"></i>
                            </div>
                            <div class="vital-info">
                                <div class="vital-label">Weight</div>
                                <div class="vital-value">68 <span>kg</span></div>
                                <div class="vital-status normal">Optimal</div>
                            </div>
                        </div>
                        <div class="vital-item">
                            <div class="vital-icon">
                                <i class="fas fa-ruler-vertical"></i>
                            </div>
                            <div class="vital-info">
                                <div class="vital-label">BMI</div>
                                <div class="vital-value">22.5</div>
                                <div class="vital-status normal">Healthy</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="health-card fitness-metrics">
                    <div class="card-header">
                        <h3><i class="fas fa-dumbbell"></i> Fitness Metrics</h3>
                        <span class="last-updated">Assessed: Sep 5, 2025</span>
                    </div>
                    <div class="fitness-grid">
                        <div class="fitness-item">
                            <div class="fitness-label">VO2 Max</div>
                            <div class="fitness-value">52.3 <span>ml/kg/min</span></div>
                            <div class="fitness-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 85%"></div>
                                </div>
                                <span class="progress-label">Excellent</span>
                            </div>
                        </div>
                        <div class="fitness-item">
                            <div class="fitness-label">Body Fat %</div>
                            <div class="fitness-value">12.8%</div>
                            <div class="fitness-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 78%"></div>
                                </div>
                                <span class="progress-label">Athletic</span>
                            </div>
                        </div>
                        <div class="fitness-item">
                            <div class="fitness-label">Flexibility Score</div>
                            <div class="fitness-value">87/100</div>
                            <div class="fitness-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 87%"></div>
                                </div>
                                <span class="progress-label">Very Good</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Navigation -->
            <div class="medical-nav">
                <button class="nav-tab active" data-section="records">
                    <i class="fas fa-file-medical"></i>
                    Medical Records
                </button>
                <button class="nav-tab" data-section="injuries">
                    <i class="fas fa-band-aid"></i>
                    Injury History
                </button>
                <button class="nav-tab" data-section="assessments">
                    <i class="fas fa-clipboard-check"></i>
                    Fitness Assessments
                </button>
              
            </div>

            <!-- Medical Records Section -->
            <div class="medical-section active" id="records">
                <div class="section-header">
                    <h2>Medical Records</h2>
                    <button class="btn btn-outline filter-btn">
                        <i class="fas fa-filter"></i>
                        Filter Records
                    </button>
                </div>

                <div class="records-timeline">
                    <div class="record-item">
                        <div class="record-date">
                            <div class="date-day">10</div>
                            <div class="date-month">Sep</div>
                            <div class="date-year">2025</div>
                        </div>
                        <div class="record-content">
                            <div class="record-header">
                                <h4>Annual Physical Examination</h4>
                                <span class="record-type routine">Routine Checkup</span>
                            </div>
                            <div class="record-details">
                                <p><strong>Doctor:</strong> Dr. Sarah Johnson</p>
                                <p><strong>Results:</strong> Excellent overall health. No concerns identified.</p>
                                <p><strong>Recommendations:</strong> Continue current fitness regimen. Increase protein intake slightly.</p>
                            </div>
                            <div class="record-actions">
                                <button class="btn-link">
                                    <i class="fas fa-download"></i>
                                    Download Report
                                </button>
                                <button class="btn-link">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="record-item">
                        <div class="record-date">
                            <div class="date-day">28</div>
                            <div class="date-month">Aug</div>
                            <div class="date-year">2025</div>
                        </div>
                        <div class="record-content">
                            <div class="record-header">
                                <h4>Sports Nutrition Consultation</h4>
                                <span class="record-type consultation">Consultation</span>
                            </div>
                            <div class="record-details">
                                <p><strong>Nutritionist:</strong> Maria Rodriguez</p>
                                <p><strong>Focus:</strong> Performance optimization diet plan</p>
                                <p><strong>Plan:</strong> Custom meal plan for training and competition periods.</p>
                            </div>
                            <div class="record-actions">
                                <button class="btn-link">
                                    <i class="fas fa-download"></i>
                                    Download Plan
                                </button>
                                <button class="btn-link">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="record-item">
                        <div class="record-date">
                            <div class="date-day">15</div>
                            <div class="date-month">Aug</div>
                            <div class="date-year">2025</div>
                        </div>
                        <div class="record-content">
                            <div class="record-header">
                                <h4>Blood Work Analysis</h4>
                                <span class="record-type lab">Lab Results</span>
                            </div>
                            <div class="record-details">
                                <p><strong>Lab:</strong> Elite Medical Center</p>
                                <p><strong>Tests:</strong> Complete Blood Count, Metabolic Panel, Vitamin D</p>
                                <p><strong>Results:</strong> All values within normal ranges. Vitamin D slightly low.</p>
                            </div>
                            <div class="record-actions">
                                <button class="btn-link">
                                    <i class="fas fa-download"></i>
                                    Download Results
                                </button>
                                <button class="btn-link">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Injury History Section -->
            <div class="medical-section" id="injuries">
                <div class="section-header">
                    <h2>Injury History & Prevention</h2>
                    <button class="btn btn-outline">
                        <i class="fas fa-plus"></i>
                        Report Injury
                    </button>
                </div>

                <div class="injury-summary">
                    <div class="summary-card">
                        <div class="summary-stat">
                            <div class="stat-number">2</div>
                            <div class="stat-label">Total Injuries</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-stat">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Current Injuries</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-stat">
                            <div class="stat-number">45</div>
                            <div class="stat-label">Days Injury-Free</div>
                        </div>
                    </div>
                </div>

                <div class="injury-history">
                    <div class="injury-card recovered">
                        <div class="injury-icon">
                            <i class="fas fa-running"></i>
                        </div>
                        <div class="injury-info">
                            <h4>Minor Ankle Sprain</h4>
                            <div class="injury-details">
                                <p><strong>Date:</strong> July 29, 2025</p>
                                <p><strong>Cause:</strong> Training accident during fielding practice</p>
                                <p><strong>Recovery:</strong> 14 days</p>
                                <p><strong>Treatment:</strong> Rest, ice, compression, physiotherapy</p>
                            </div>
                        </div>
                        <div class="injury-status">
                            <span class="status recovered">Fully Recovered</span>
                        </div>
                    </div>

                    <div class="injury-card recovered">
                        <div class="injury-icon">
                            <i class="fas fa-hand-paper"></i>
                        </div>
                        <div class="injury-info">
                            <h4>Wrist Strain</h4>
                            <div class="injury-details">
                                <p><strong>Date:</strong> June 12, 2025</p>
                                <p><strong>Cause:</strong> Overuse during batting practice</p>
                                <p><strong>Recovery:</strong> 10 days</p>
                                <p><strong>Treatment:</strong> Rest, anti-inflammatory, stretching</p>
                            </div>
                        </div>
                        <div class="injury-status">
                            <span class="status recovered">Fully Recovered</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fitness Assessments Section -->
            <div class="medical-section" id="assessments">
                <div class="section-header">
                    <h2>Fitness Assessments</h2>
                    <button class="btn btn-outline">
                        <i class="fas fa-calendar-plus"></i>
                        Schedule Assessment
                    </button>
                </div>


                <div class="assessment-history">
                    <div class="assessment-card">
                        <div class="assessment-date">Sep 5, 2025</div>
                        <div class="assessment-content">
                            <h4>Comprehensive Fitness Assessment</h4>
                            <div class="assessment-scores">
                                <div class="score-item">
                                    <span class="score-label">Cardiovascular</span>
                                    <span class="score-value">92/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Strength</span>
                                    <span class="score-value">88/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Flexibility</span>
                                    <span class="score-value">87/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Agility</span>
                                    <span class="score-value">95/100</span>
                                </div>
                            </div>
                            <div class="assessment-recommendations">
                                <p><strong>Recommendations:</strong> Continue current training. Focus on core strength exercises.</p>
                            </div>
                        </div>
                    </div>

                    <div class="assessment-card">
                        <div class="assessment-date">Aug 1, 2025</div>
                        <div class="assessment-content">
                            <h4>Mid-Season Fitness Check</h4>
                            <div class="assessment-scores">
                                <div class="score-item">
                                    <span class="score-label">Cardiovascular</span>
                                    <span class="score-value">89/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Strength</span>
                                    <span class="score-value">85/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Flexibility</span>
                                    <span class="score-value">84/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Agility</span>
                                    <span class="score-value">91/100</span>
                                </div>
                            </div>
                            <div class="assessment-recommendations">
                                <p><strong>Recommendations:</strong> Increase flexibility training. Add plyometric exercises.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           
           
        </div>
    </div>
    <!-- End Player Layout -->

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/medical.js"></script>
</body>
</html>