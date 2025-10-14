<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Schedule - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/training.css?v=2.0">
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
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link active">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
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
            </nav>

            <!-- Player Profile -->
            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name'] ?? 'Player Name'; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level'] ?? 'Member'; ?> Member</div>
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
            <div class="training-schedule">
                <!-- Page Header -->
                <div class="content-header">
                    <div class="header-title">
                        <h1><i class="fas fa-dumbbell"></i> Training Schedule</h1>
                        <p>Manage your weekly training sessions and track attendance</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="goToBookings">
                            <i class="fas fa-calendar-plus"></i>
                            My Bookings
                        </button>
                    </div>
                </div>

                <!-- View Toggle and Navigation -->
                <div class="training-controls">
                    <div class="view-toggle">
                        <button class="toggle-btn active" data-view="week" id="weekViewBtn">
                            <i class="fas fa-calendar-week"></i> Weekly View
                        </button>
                        <button class="toggle-btn" data-view="day" id="dayViewBtn">
                            <i class="fas fa-calendar-day"></i> Daily View
                        </button>
                    </div>
                    
                    <div class="date-navigation">
                        <button class="nav-btn" id="prevBtn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="current-period" id="currentPeriod">Week of Sep 13 - Sep 19, 2025</span>
                        <button class="nav-btn" id="nextBtn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                   
                </div>

                <!-- Weekly Schedule -->
                <div class="weekly-schedule active" id="weeklyView">
                    <div class="timetable">
                        <div class="time-slots">
                            <div class="time-header">Time</div>
                            <div class="time-slot">06:00</div>
                            <div class="time-slot">07:00</div>
                            <div class="time-slot">08:00</div>
                            <div class="time-slot">09:00</div>
                            <div class="time-slot">10:00</div>
                            <div class="time-slot">15:00</div>
                            <div class="time-slot">16:00</div>
                            <div class="time-slot">17:00</div>
                            <div class="time-slot">18:00</div>
                        </div>

                        <div class="days-grid">
                            <!-- Monday -->
                            <div class="day-column" data-day="monday">
                                <div class="day-header">
                                    <span class="day-name">Monday</span>
                                    <span class="day-date">Sep 13</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session batting" data-time="06:00">
                                        <div class="session-content">
                                            <i class="fas fa-baseball-ball"></i>
                                            <span class="session-title">Batting Practice</span>
                                            <span class="session-coach">Coach Wilson</span>
                                        </div>
                                    </div>
                                    <div class="session fitness" data-time="16:00">
                                        <div class="session-content">
                                            <i class="fas fa-dumbbell"></i>
                                            <span class="session-title">Fitness Training</span>
                                            <span class="session-coach">Trainer Mike</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tuesday -->
                            <div class="day-column" data-day="tuesday">
                                <div class="day-header">
                                    <span class="day-name">Tuesday</span>
                                    <span class="day-date">Sep 14</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session bowling" data-time="07:00">
                                        <div class="session-content">
                                            <i class="fas fa-circle"></i>
                                            <span class="session-title">Bowling Practice</span>
                                            <span class="session-coach">Coach Ahmed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wednesday -->
                            <div class="day-column" data-day="wednesday">
                                <div class="day-header">
                                    <span class="day-name">Wednesday</span>
                                    <span class="day-date">Sep 15</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session fielding" data-time="06:00">
                                        <div class="session-content">
                                            <i class="fas fa-hand-rock"></i>
                                            <span class="session-title">Fielding Drills</span>
                                            <span class="session-coach">Coach Smith</span>
                                        </div>
                                    </div>
                                    <div class="session fitness" data-time="17:00">
                                        <div class="session-content">
                                            <i class="fas fa-dumbbell"></i>
                                            <span class="session-title">Strength Training</span>
                                            <span class="session-coach">Trainer Sarah</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thursday -->
                            <div class="day-column" data-day="thursday">
                                <div class="day-header">
                                    <span class="day-name">Thursday</span>
                                    <span class="day-date">Sep 16</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session batting" data-time="08:00">
                                        <div class="session-content">
                                            <i class="fas fa-baseball-ball"></i>
                                            <span class="session-title">Net Practice</span>
                                            <span class="session-coach">Coach Wilson</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Friday -->
                            <div class="day-column" data-day="friday">
                                <div class="day-header">
                                    <span class="day-name">Friday</span>
                                    <span class="day-date">Sep 17</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session match" data-time="09:00">
                                        <div class="session-content">
                                            <i class="fas fa-trophy"></i>
                                            <span class="session-title">Practice Match</span>
                                            <span class="session-coach">All Coaches</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Saturday -->
                            <div class="day-column" data-day="saturday">
                                <div class="day-header">
                                    <span class="day-name">Saturday</span>
                                    <span class="day-date">Sep 18</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session rest" data-time="">
                                        <div class="session-content">
                                            <i class="fas fa-bed"></i>
                                            <span class="session-title">Rest Day</span>
                                            <span class="session-coach">Recovery</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sunday -->
                            <div class="day-column" data-day="sunday">
                                <div class="day-header">
                                    <span class="day-name">Sunday</span>
                                    <span class="day-date">Sep 19</span>
                                </div>
                                <div class="day-slots">
                                    <div class="session team" data-time="10:00">
                                        <div class="session-content">
                                            <i class="fas fa-users"></i>
                                            <span class="session-title">Team Meeting</span>
                                            <span class="session-coach">Coach Wilson</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Schedule -->
                <div class="daily-schedule" id="dailyView" style="display: none;">
                    <!-- Day Navigation for Daily View -->
                    <div class="day-navigation">
                        <div class="quick-day-buttons">
                            <button class="day-btn" id="prevDayBtn">
                                <i class="fas fa-chevron-left"></i> Previous Day
                            </button>
                            <button class="day-btn" id="nextDayBtn">
                                Next Day <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="day-selector">
                            <button class="day-quick-btn" data-day="monday">Mon</button>
                            <button class="day-quick-btn" data-day="tuesday">Tue</button>
                            <button class="day-quick-btn" data-day="wednesday">Wed</button>
                            <button class="day-quick-btn" data-day="thursday">Thu</button>
                            <button class="day-quick-btn" data-day="friday">Fri</button>
                            <button class="day-quick-btn" data-day="saturday">Sat</button>
                            <button class="day-quick-btn" data-day="sunday">Sun</button>
                        </div>
                    </div>

                    <!-- Single Day Container -->
                    <div class="single-day-container">
                        <div class="day-header-large">
                            <h2 id="currentDayName">Monday</h2>
                            <p id="currentDayDate">September 13, 2025</p>
                        </div>

                        <!-- Sessions for Current Day -->
                        <div class="daily-sessions" id="dailySessions">
                            <!-- Sessions will be dynamically loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="attendance-summary">
                    <h2><i class="fas fa-chart-pie"></i> This Week's Attendance</h2>
                    <div class="attendance-stats">
                        <div class="stat-item">
                            <div class="stat-icon attended">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">8</span>
                                <span class="stat-label">Attended</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon missed">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">2</span>
                                <span class="stat-label">Missed</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon upcoming">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">3</span>
                                <span class="stat-label">Upcoming</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon percentage">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number">80%</span>
                                <span class="stat-label">Attendance Rate</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/training.js"></script>
</body>
</html>
