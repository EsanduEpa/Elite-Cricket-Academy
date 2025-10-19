<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time() + 20; ?>">

<!-- Trainer Dashboard Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John Smith'; ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Player Bookings</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/exercises" class="nav-link">
                        <i class="fas fa-running"></i>
                        <span>Common Exercises</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Supplement Recommendations</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury-reports" class="nav-link">
                        <i class="fas fa-user-injured"></i>
                        <span>Injury Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout-plans" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Individual Workouts</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/schedules" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Training Schedules</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/reports" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Progress Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

        <!-- Trainer Profile Section -->
        <div class="trainer-profile">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <div class="trainer-name"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John Trainer'; ?></div>
                <div class="trainer-role">Physical Trainer</div>
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
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John'; ?>!</h1>
                    <p>Your training management dashboard - Schedule sessions, track progress, and manage your clients</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="btn-training">
                        <i class="fas fa-calendar-plus"></i> New Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/exercises" class="btn-performance">
                        <i class="fas fa-dumbbell"></i> Exercises
                    </a>
                    <button class="btn-refresh" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i>
                        <span class="current-time"><?php echo date('H:i'); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Today's Sessions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Active Clients</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Hours Booked</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">96%</div>
                    <div class="stat-label">Week Utilization</div>
                </div>
            </div>
        </div>

        <!-- Row 1: Today's Schedule and Client Progress Side by Side -->
        <div class="schedule-row">
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
                                <th>Client & Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">9:00 AM</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Youth Cricket Program</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> 15 Players - Field A
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Active</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">11:30 AM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Kumara Silva</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Fitness Assessment - Gym
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Upcoming</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">2 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Advanced Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-users"></i> 12 Players - Indoor Nets
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge">Scheduled</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-primary">4:30 PM</div>
                                    <div class="table-cell-secondary">1 hour</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Anjali Perera</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-user"></i> Strength Training - Gym B
                                    </div>
                                </div>
                                <div class="session-summary">
                                    <strong>Session Summary:</strong> Complete health assessment completed. Player cleared for upcoming tournament. No issues found.
                                </div>
                                <div class="booking-actions">
                                    <button class="action-btn view-notes" onclick="viewSessionNotes(4)">
                                        <i class="fas fa-notes-medical"></i> View Notes
                                    </button>
                                    <button class="action-btn followup" onclick="scheduleFollowup(4)">
                                        <i class="fas fa-calendar-plus"></i> Schedule Follow-up
                                    </button>
                                    <button class="action-btn details" onclick="viewBookingDetails(4)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="booking-calendar-view" id="bookingCalendarView">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <div class="calendar-navigation">
                                <button class="nav-btn" id="prevBookingMonth">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h3 id="currentBookingMonth">December 2024</h3>
                                <button class="nav-btn" id="nextBookingMonth">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-legend">
                                <div class="legend-item">
                                    <div class="legend-color pending"></div>
                                    <span>Pending</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color confirmed"></div>
                                    <span>Confirmed</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color completed"></div>
                                    <span>Completed</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color cancelled"></div>
                                    <span>Cancelled</span>
                                </div>
                            </div>
                        </div>
                        <div class="booking-calendar-grid" id="bookingCalendarGrid">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Management Modals -->
            <!-- Add Time Slot Modal -->
            <div class="modal-overlay" id="addSlotModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h2><i class="fas fa-plus"></i> Add Available Time Slot</h2>
                        <button class="close-btn" onclick="closeAddSlotModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="addSlotForm" class="add-slot-form">
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="slotDate">Date:</label>
                                    <input type="date" id="slotDate" name="date" required>
                                </div>
                                <div class="field-group">
                                    <label for="slotType">Session Type:</label>
                                    <select id="slotType" name="type" required>
                                        <option value="">Select session type...</option>
                                        <option value="physio">Physio Session (60 min)</option>
                                        <option value="fitness">Fitness Assessment (90 min)</option>
                                        <option value="consultation">General Consultation (30 min)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="startTime">Start Time:</label>
                                    <input type="time" id="startTime" name="startTime" required>
                                </div>
                                <div class="field-group">
                                    <label for="endTime">End Time:</label>
                                    <input type="time" id="endTime" name="endTime" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="practitioner">Assign to Practitioner:</label>
                                    <select id="practitioner" name="practitioner" required>
                                        <option value="">Select practitioner...</option>
                                        <option value="dr-sarah">Dr. Sarah Wilson (Physio)</option>
                                        <option value="coach-mike">Coach Mike Johnson (Fitness)</option>
                                        <option value="trainer-alex">Trainer Alex Thompson (General)</option>
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label for="location">Location:</label>
                                    <select id="location" name="location" required>
                                        <option value="">Select location...</option>
                                        <option value="physio-room">Physiotherapy Room</option>
                                        <option value="fitness-center">Fitness Center</option>
                                        <option value="consultation-room">Consultation Room</option>
                                        <option value="medical-office">Medical Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label for="slotNotes">Notes (Optional):</label>
                                <textarea id="slotNotes" name="notes" rows="3" 
                                    placeholder="Any special notes about this time slot..."></textarea>
                            </div>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="recurring" name="recurring">
                                    <span class="checkmark"></span>
                                    Make this a recurring slot (weekly)
                                </label>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="closeAddSlotModal()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-plus"></i> Add Time Slot
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Booking Details Modal -->
            <div class="modal-overlay" id="bookingDetailsModal">
                <div class="modal-container large">
                    <div class="modal-header">
                        <h2><i class="fas fa-info-circle"></i> Booking Details</h2>
                        <button class="close-btn" onclick="closeBookingDetailsModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <div id="bookingDetailsContent">
                            <!-- Details will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Session Notes Modal -->
            <div class="modal-overlay" id="sessionNotesModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h2><i class="fas fa-notes-medical"></i> Session Notes</h2>
                        <button class="close-btn" onclick="closeSessionNotesModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="sessionNotesForm" class="session-notes-form">
                            <div class="field-group">
                                <label for="sessionSummary">Session Summary:</label>
                                <textarea id="sessionSummary" name="summary" rows="4" 
                                    placeholder="Brief summary of the session..." required></textarea>
                            </div>
                            <div class="field-group">
                                <label for="findings">Key Findings:</label>
                                <textarea id="findings" name="findings" rows="3" 
                                    placeholder="Important findings or observations..."></textarea>
                            </div>
                            <div class="field-group">
                                <label for="recommendations">Recommendations:</label>
                                <textarea id="recommendations" name="recommendations" rows="3" 
                                    placeholder="Treatment recommendations or next steps..."></textarea>
                            </div>
                            <div class="form-row">
                                <div class="field-group">
                                    <label for="followupRequired">Follow-up Required:</label>
                                    <select id="followupRequired" name="followup">
                                        <option value="none">No follow-up needed</option>
                                        <option value="1week">1 week</option>
                                        <option value="2weeks">2 weeks</option>
                                        <option value="1month">1 month</option>
                                        <option value="custom">Custom timeline</option>
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label for="sessionRating">Session Rating:</label>
                                    <select id="sessionRating" name="rating">
                                        <option value="excellent">Excellent Progress</option>
                                        <option value="good">Good Progress</option>
                                        <option value="fair">Fair Progress</option>
                                        <option value="poor">Needs Attention</option>
                                    </select>
                                </div>
                            </div>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="notifyPlayer" name="notifyPlayer" checked>
                                    <span class="checkmark"></span>
                                    Send session summary to player
                                </label>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" onclick="closeSessionNotesModal()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-save"></i> Save Notes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section id="tournaments-section" class="content-section">
            <div class="tournaments-header">
                <h1><i class="fas fa-trophy"></i> Upcoming Tournaments</h1>
                <p>View tournaments posted by admin for all players, coaches, and trainers</p>
            </div>

            <!-- Tournament Cards Grid -->
            <div class="tournaments-grid" id="tournamentsGrid">
                
                <!-- Tournament Card 1 -->
                <div class="tournament-card high-priority" data-filter="upcoming this-month requires-attention">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-championship">Championship</span>
                            <span class="priority-badge urgent">High Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Sept 25, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>National Youth Cricket Championship</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> R. Premadasa Stadium, Colombo</p>
                        <p class="tournament-description">Major national tournament requiring intensive physical preparation and health monitoring.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>Complete fitness assessment</li>
                                <li>Injury screening and clearance</li>
                                <li>Nutritional planning for 3-day event</li>
                                <li>Hydration and recovery protocols</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip needs-assessment">John Smith <i class="fas fa-exclamation"></i></span>
                                <span class="player-chip cleared">Mike Johnson <i class="fas fa-check"></i></span>
                                <span class="player-chip needs-assessment">Sarah Williams <i class="fas fa-exclamation"></i></span>
                                <span class="player-chip cleared">David Brown <i class="fas fa-check"></i></span>
                                <span class="player-chip more">+6 more</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-1')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-1')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>

                <!-- Tournament Card 2 -->
                <div class="tournament-card medium-priority" data-filter="upcoming this-month">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-league">League Match</span>
                            <span class="priority-badge medium">Medium Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Oct 2, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>Inter-School Cricket League</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> Sinhalese Sports Club Ground</p>
                        <p class="tournament-description">Regular league match with standard health protocols.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>Basic fitness check</li>
                                <li>Injury status review</li>
                                <li>Match day nutrition plan</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip cleared">Alex Thompson <i class="fas fa-check"></i></span>
                                <span class="player-chip cleared">Emma Davis <i class="fas fa-check"></i></span>
                                <span class="player-chip needs-assessment">Chris Wilson <i class="fas fa-exclamation"></i></span>
                                <span class="player-chip more">+4 more</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-2')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-2')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>

                <!-- Tournament Card 3 -->
                <div class="tournament-card low-priority" data-filter="upcoming">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-friendly">Friendly Match</span>
                            <span class="priority-badge low">Low Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Oct 15, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>Practice Match vs Royal College</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> Academy Ground</p>
                        <p class="tournament-description">Friendly practice match for skill development.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>General fitness verification</li>
                                <li>Basic hydration guidelines</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip cleared">Tom Anderson <i class="fas fa-check"></i></span>
                                <span class="player-chip cleared">Lisa Martinez <i class="fas fa-check"></i></span>
                                <span class="player-chip more">+8 more</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-3')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-3')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>

                <!-- Tournament Card 4 -->
                <div class="tournament-card high-priority" data-filter="upcoming requires-attention">
                    <div class="tournament-header">
                        <div class="tournament-type">
                            <span class="badge badge-championship">Regional Cup</span>
                            <span class="priority-badge urgent">High Priority</span>
                        </div>
                        <div class="tournament-date">
                            <i class="fas fa-calendar"></i>
                            <span>Nov 5, 2025</span>
                        </div>
                    </div>
                    
                    <div class="tournament-content">
                        <h3>Southern Province Cricket Cup</h3>
                        <p class="tournament-venue"><i class="fas fa-map-marker-alt"></i> Galle International Stadium</p>
                        <p class="tournament-description">Regional championship requiring comprehensive health preparation.</p>
                        
                        <div class="tournament-requirements">
                            <h4>Health Requirements:</h4>
                            <ul>
                                <li>Comprehensive medical examination</li>
                                <li>Performance fitness testing</li>
                                <li>Travel health protocols</li>
                                <li>Extended tournament nutrition plan</li>
                            </ul>
                        </div>
                        
                        <div class="players-status">
                            <h4>Players Status:</h4>
                            <div class="player-chips">
                                <span class="player-chip pending">Assessments Pending</span>
                                <span class="player-chip more">15 players total</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tournament-actions">
                        <button class="btn-secondary" onclick="viewTournamentDetails('tournament-4')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn-primary" onclick="manageTournamentHealth('tournament-4')">
                            <i class="fas fa-heartbeat"></i> Manage Health Plans
                        </button>
                    </div>
                </div>
            </div>

            <!-- Health Guidelines Section -->
            <div class="health-guidelines">
                <h2><i class="fas fa-clipboard-list"></i> Tournament Health Guidelines</h2>
                <div class="guidelines-grid">
                    <div class="guideline-card">
                        <h3><i class="fas fa-heart"></i> Pre-Tournament Assessment</h3>
                        <ul>
                            <li>Complete physical examination</li>
                            <li>Cardiovascular fitness evaluation</li>
                            <li>Injury risk assessment</li>
                            <li>Nutrition and hydration planning</li>
                        </ul>
                    </div>
                    <div class="guideline-card">
                        <h3><i class="fas fa-running"></i> During Tournament</h3>
                        <ul>
                            <li>Regular hydration monitoring</li>
                            <li>Fatigue level assessment</li>
                            <li>Immediate injury evaluation</li>
                            <li>Performance nutrition support</li>
                        </ul>
                    </div>
                    <div class="guideline-card">
                        <h3><i class="fas fa-bed"></i> Post-Tournament Recovery</h3>
                        <ul>
                            <li>Recovery assessment</li>
                            <li>Injury evaluation and treatment</li>
                            <li>Nutrition for recovery</li>
                            <li>Rest and rehabilitation planning</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="nutrition-section" class="content-section">
            <div class="nutrition-header">
                <h1>Nutrition & Supplements</h1>
                <div class="nutrition-controls">
                    <div class="filter-tabs">
                        <button class="filter-btn active" data-filter="all">All Plans</button>
                        <button class="filter-btn" data-filter="general">General Plans</button>
                        <button class="filter-btn" data-filter="personalized">Personalized Plans</button>
                        <button class="filter-btn" data-filter="supplements">Supplements</button>
                    </div>
                    <button class="btn-primary" id="createPlanBtn">
                        <i class="fas fa-plus"></i>
                        Create New Plan
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="nutrition-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-details">
                        <h3>12</h3>
                        <p>Active Diet Plans</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="stat-details">
                        <h3>8</h3>
                        <p>Supplement Programs</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h3>45</h3>
                        <p>Players Assigned</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-details">
                        <h3>3</h3>
                        <p>Tournament Plans</p>
                    </div>
                </div>
            </div>

            <!-- Diet Plans Grid -->
            <div class="plans-container">
                <h2>Diet Plans</h2>
                <div class="plans-grid" id="dietPlansGrid">
                    <!-- General Diet Plans -->
                    <div class="plan-card general" data-plan-type="general">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Weight Gain Program</h3>
                                <span class="plan-tag general">General</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">High-calorie diet plan for players looking to gain healthy weight and build muscle mass.</p>
                            <div class="plan-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>6 weeks duration</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-fire"></i>
                                    <span>3200+ calories/day</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>12 players assigned</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-plus"></i>
                                Assign to Players
                            </button>
                            <button class="view-btn">View Details</button>
                        </div>
                    </div>

                    <!-- Personalized Diet Plan - REMOVED -->

                    <!-- Tournament Preparation -->
                    <div class="plan-card general" data-plan-type="general">
                        <div class="plan-header">
                            <div class="plan-title">
                                <h3>Tournament Prep Diet</h3>
                                <span class="plan-tag tournament">Tournament</span>
                            </div>
                            <div class="plan-actions">
                                <button class="action-btn-small" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small" title="Delete Plan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">Performance-optimized nutrition plan for major tournaments and competitions.</p>
                            <div class="plan-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>8 weeks duration</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-bolt"></i>
                                    <span>Performance focused</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>18 players assigned</span>
                                </div>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <button class="assign-btn">
                                <i class="fas fa-user-plus"></i>
                                Assign to Players
                            </button>
                            <button class="view-btn">View Details</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-chart-line"></i> Client Progress</h2>
                    </div>
                </div>
            </div>

            <!-- Assignment Management -->
            <div class="assignment-container">
                <div class="assignment-header">
                    <h2>Plan Assignments</h2>
                    <button class="btn-primary" id="newAssignmentBtn">
                        <i class="fas fa-plus"></i>
                        New Assignment
                    </button>
                </div>

                <!-- New Assignment Form -->
                <div class="new-assignment-form" id="newAssignmentForm" style="display: none;">
                    <div class="form-card">
                        <h3><i class="fas fa-user-plus"></i> Assign Plans to Player/Team</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="playerSelect">Player/Team</label>
                                <select id="playerSelect" class="form-select">
                                    <option value="">Select Player/Team</option>
                                    <option value="kamal-silva">Kamal Silva</option>
                                    <option value="sarah-fernando">Sarah Fernando</option>
                                    <option value="michael-perera">Michael Perera</option>
                                    <option value="youth-group">Youth Group (15 players)</option>
                                    <option value="senior-team">Senior Team (18 players)</option>
                                    <option value="junior-team">Junior Team (12 players)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="dietPlanSelect">Diet Plan</label>
                                <select id="dietPlanSelect" class="form-select">
                                    <option value="none">None</option>
                                    <option value="weight-gain">Weight Gain Program</option>
                                    <option value="tournament-prep">Tournament Prep Diet</option>
                                    <option value="maintenance">Maintenance Diet</option>
                                    <option value="cutting">Cutting Program</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="supplementPlanSelect">Supplement Plan</label>
                                <select id="supplementPlanSelect" class="form-select">
                                    <option value="none">None</option>
                                    <option value="basic-athletic">Basic Athletic Support</option>
                                    <option value="performance-enhancement">Performance Enhancement</option>
                                    <option value="recovery-focused">Recovery Focused</option>
                                    <option value="pre-competition">Pre-Competition</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="startDate">Start Date</label>
                                <input type="date" id="startDate" class="form-input" value="2025-10-15">
                            </div>
                            
                            <div class="form-group">
                                <label for="duration">Duration (weeks)</label>
                                <select id="duration" class="form-select">
                                    <option value="2">2 weeks</option>
                                    <option value="4">4 weeks</option>
                                    <option value="6" selected>6 weeks</option>
                                    <option value="8">8 weeks</option>
                                    <option value="12">12 weeks</option>
                                    <option value="ongoing">Ongoing</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <textarea id="notes" class="form-textarea" placeholder="Special instructions or notes..."></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="cancelAssignment()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn-primary" onclick="saveAssignment()">
                                <i class="fas fa-save"></i> Save Assignment
                            </button>
                        </div>
                    </div>
                </div>

                <div class="assignment-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Program</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-title">James Wilson</div>
                                    <div class="table-cell-secondary">Cricket Player</div>
                                </td>
                                <td>
                                    <select class="inline-select diet-plan-select" data-player="kamal-silva">
                                        <option value="none">None</option>
                                        <option value="weight-gain" selected>Weight Gain Program</option>
                                        <option value="tournament-prep">Tournament Prep Diet</option>
                                        <option value="maintenance">Maintenance Diet</option>
                                        <option value="cutting">Cutting Program</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select supplement-plan-select" data-player="kamal-silva">
                                        <option value="none">None</option>
                                        <option value="basic-athletic" selected>Basic Athletic Support</option>
                                        <option value="performance-enhancement">Performance Enhancement</option>
                                        <option value="recovery-focused">Recovery Focused</option>
                                        <option value="pre-competition">Pre-Competition</option>
                                    </select>
                                </td>
                                <td>Aug 15, 2025</td>
                                <td>
                                    <div class="table-cell-title">Strength & Conditioning</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-dumbbell"></i> Week 4 of 8
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewProgress('kamal-silva')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeAssignment('kamal-silva')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">Sarah Mitchell</div>
                                    <div class="table-cell-secondary">Tennis Player</div>
                                </td>
                                <td>
                                    <select class="inline-select diet-plan-select" data-player="youth-group">
                                        <option value="none">None</option>
                                        <option value="weight-gain" selected>Weight Gain Program</option>
                                        <option value="tournament-prep">Tournament Prep Diet</option>
                                        <option value="maintenance">Maintenance Diet</option>
                                        <option value="cutting">Cutting Program</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select supplement-plan-select" data-player="youth-group">
                                        <option value="none">None</option>
                                        <option value="basic-athletic" selected>Basic Athletic Support</option>
                                        <option value="performance-enhancement">Performance Enhancement</option>
                                        <option value="recovery-focused">Recovery Focused</option>
                                        <option value="pre-competition">Pre-Competition</option>
                                    </select>
                                </td>
                                <td>Sep 1, 2025</td>
                                <td>
                                    <div class="table-cell-title">Agility Training</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-running"></i> Week 2 of 6
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewProgress('youth-group')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeAssignment('youth-group')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">David Chen</div>
                                    <div class="table-cell-secondary">Football Player</div>
                                </td>
                                <td>
                                    <select class="inline-select diet-plan-select" data-player="senior-team">
                                        <option value="none">None</option>
                                        <option value="weight-gain">Weight Gain Program</option>
                                        <option value="tournament-prep" selected>Tournament Prep Diet</option>
                                        <option value="maintenance">Maintenance Diet</option>
                                        <option value="cutting">Cutting Program</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select supplement-plan-select" data-player="senior-team">
                                        <option value="none">None</option>
                                        <option value="basic-athletic">Basic Athletic Support</option>
                                        <option value="performance-enhancement" selected>Performance Enhancement</option>
                                        <option value="recovery-focused">Recovery Focused</option>
                                        <option value="pre-competition">Pre-Competition</option>
                                    </select>
                                </td>
                                <td>Aug 1, 2025</td>
                                <td>
                                    <div class="table-cell-title">Recovery Program</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-medkit"></i> Week 6 of 8
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewProgress('senior-team')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeAssignment('senior-team')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="workout-section" class="content-section">
            <div class="exercises-header">
                <h1><i class="fas fa-dumbbell"></i> Health & Fitness</h1>
                <p>Manage player health, fitness routines, and medical-based workout assignments for optimal physical condition</p>
            </div>

            <!-- Exercise & Workout Navigation Tabs -->
            <div class="exercise-tabs">
                <button class="tab-btn active" data-tab="exercise-library">
                    <i class="fas fa-video"></i> Exercise Library
                </button>
                <button class="tab-btn" data-tab="workout-plans">
                    <i class="fas fa-clipboard-list"></i> Workout Plans
                </button>
            </div>

            <!-- Exercise Library Tab -->
            <div id="exercise-library" class="tab-content active">
                <div class="section-header">
                    <div class="header-left">
                        <h2>Health & Fitness Exercise Library</h2>
                        <div class="filter-tabs">
                            <button class="filter-btn active" data-filter="all">All Exercises</button>
                            <button class="filter-btn" data-filter="warmup">Warm-up</button>
                            <button class="filter-btn" data-filter="strength">Strength Training</button>
                            <button class="filter-btn" data-filter="endurance">Endurance</button>
                            <button class="filter-btn" data-filter="recovery">Recovery</button>
                        </div>
                    </div>
                    <button class="add-btn" onclick="openExerciseModal()">
                        <i class="fas fa-plus"></i> Add Exercise Video
                    </button>
                </div>

                <div class="exercises-grid">
                    <!-- Warm-up Exercise Cards -->
                    <div class="exercise-card" data-type="warmup">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">15 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Dynamic Warm-up Routine</h3>
                            <p>General warm-up exercises to prepare muscles and joints for training</p>
                            <div class="exercise-meta">
                                <span class="target-area">Full Body</span>
                                <span class="difficulty easy">Beginner</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(1)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(1)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exercise-card" data-type="strength">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">25 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Core Strength Training</h3>
                            <p>Fundamental core strengthening exercises for injury prevention</p>
                            <div class="exercise-meta">
                                <span class="target-area">Core</span>
                                <span class="difficulty medium">Intermediate</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(2)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(2)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exercise-card" data-type="endurance">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">30 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Cardio Endurance Training</h3>
                            <p>Cardiovascular exercises to improve stamina and heart health</p>
                            <div class="exercise-meta">
                                <span class="target-area">Cardiovascular</span>
                                <span class="difficulty medium">Intermediate</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(3)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(3)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="exercise-card" data-type="recovery">
                        <div class="exercise-thumbnail">
                            <i class="fas fa-play-circle"></i>
                            <div class="exercise-duration">20 min</div>
                        </div>
                        <div class="exercise-info">
                            <h3>Stretching & Recovery</h3>
                            <p>Cool-down and flexibility exercises for muscle recovery</p>
                            <div class="exercise-meta">
                                <span class="target-area">Full Body</span>
                                <span class="difficulty easy">Beginner</span>
                                <span class="exercise-type general">General Use</span>
                            </div>
                            <div class="exercise-actions">
                                <button class="btn-edit" onclick="editExercise(4)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-assign" onclick="assignExercise(4)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteExercise(4)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workout Plans Tab -->
            <div id="workout-plans" class="tab-content">
                <div class="section-header">
                    <div class="header-left">
                        <h2>Personalized Workout Plans</h2>
                        <div class="filter-tabs">
                            <button class="filter-btn active" data-filter="all">All Plans</button>
                            <button class="filter-btn" data-filter="injury-recovery">Injury Recovery</button>
                            <button class="filter-btn" data-filter="tournament">Tournament Prep</button>
                            <button class="filter-btn" data-filter="team-requirement">Team Requirements</button>
                            <button class="filter-btn" data-filter="individual">Individual Plans</button>
                        </div>
                    </div>
                    <button class="add-btn" onclick="openPersonalizedPlanModal()">
                        <i class="fas fa-plus"></i> Create Personalized Plan
                    </button>
                </div>

                <div class="workout-plans-grid">
                    <!-- Personalized Workout Plan Cards -->
                    <div class="workout-plan-card" data-type="injury-recovery">
                        <div class="plan-header">
                            <h3>Shoulder Injury Recovery Plan</h3>
                            <span class="plan-duration">8 weeks</span>
                        </div>
                        <p class="plan-description">Specialized rehabilitation program for shoulder injuries with gradual progression</p>
                        
                        <!-- Plan Details -->
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type injury-recovery">Injury Recovery</span>
                                <span class="plan-intensity low">Low Intensity</span>
                            </div>
                            
                            <!-- Assigned To -->
                            <div class="assigned-to">
                                <h4><i class="fas fa-user"></i> Assigned To:</h4>
                                <p>Shanali Perera</p>
                            </div>
                            
                            <!-- Included Exercises -->
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Shoulder Mobility Exercises (20 min)</li>
                                    <li>Gentle Strength Training (25 min)</li>
                                    <li>Stretching & Recovery (20 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(1)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(1)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(1)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div class="workout-plan-card" data-type="tournament">
                        <div class="plan-header">
                            <h3>Inter-School Championship Prep</h3>
                            <span class="plan-duration">6 weeks</span>
                        </div>
                        <p class="plan-description">Intensive fitness preparation for upcoming Inter-School Championship tournament</p>
                        
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type tournament">Tournament Prep</span>
                                <span class="plan-intensity high">High Intensity</span>
                            </div>
                            
                            <div class="assigned-to">
                                <h4><i class="fas fa-users"></i> Assigned To:</h4>
                                <p>U-19 Senior Team</p>
                            </div>
                            
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Core Strength Training (25 min)</li>
                                    <li>Cardio Endurance Training (30 min)</li>
                                    <li>Power Training Exercises (20 min)</li>
                                    <li>Cool-down & Recovery (15 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(2)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(2)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(2)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div class="workout-plan-card" data-type="team-requirement">
                        <div class="plan-header">
                            <h3>Bowling Team Strength Program</h3>
                            <span class="plan-duration">4 weeks</span>
                        </div>
                        <p class="plan-description">Specialized strength training program requested by bowling coaching staff</p>
                        
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type team-requirement">Team Requirements</span>
                                <span class="plan-intensity medium">Medium Intensity</span>
                            </div>
                            
                            <div class="assigned-to">
                                <h4><i class="fas fa-layer-group"></i> Assigned To:</h4>
                                <p>Group A - Fast Bowlers</p>
                            </div>
                            
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Core Strength Training (25 min)</li>
                                    <li>Lower Body Power Exercises (30 min)</li>
                                    <li>Shoulder Stability Work (20 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(3)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(3)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(3)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>

                    <div class="workout-plan-card" data-type="individual">
                        <div class="plan-header">
                            <h3>Personal Fitness Enhancement</h3>
                            <span class="plan-duration">5 weeks</span>
                        </div>
                        <p class="plan-description">Individual fitness improvement plan focusing on overall conditioning and stamina</p>
                        
                        <div class="plan-details">
                            <div class="plan-meta">
                                <span class="plan-type individual">Individual Plans</span>
                                <span class="plan-intensity medium">Medium Intensity</span>
                            </div>
                            
                            <div class="assigned-to">
                                <h4><i class="fas fa-user"></i> Assigned To:</h4>
                                <p>Kasun Rajapaksa</p>
                            </div>
                            
                            <div class="included-exercises">
                                <h4><i class="fas fa-list"></i> Included Exercises:</h4>
                                <ul class="exercise-list">
                                    <li>Dynamic Warm-up Routine (15 min)</li>
                                    <li>Cardio Endurance Training (30 min)</li>
                                    <li>Functional Strength Training (25 min)</li>
                                    <li>Flexibility & Mobility (20 min)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="plan-actions">
                            <button class="btn-view" onclick="viewWorkoutPlan(4)">
                                <i class="fas fa-eye"></i> View Full Plan
                            </button>
                            <button class="btn-edit" onclick="editWorkoutPlan(4)">
                                <i class="fas fa-edit"></i> Edit Plan
                            </button>
                            <button class="btn-assign" onclick="reassignWorkoutPlan(4)">
                                <i class="fas fa-user-plus"></i> Reassign
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workout Assignment Management -->
            <div class="workout-assignment-container">
                <div class="assignment-header">
                    <h2><i class="fas fa-clipboard-list"></i> Workout Plan Assignments</h2>
                    <button class="btn-primary" id="newWorkoutAssignmentBtn">
                        <i class="fas fa-plus"></i>
                        New Assignment
                    </button>
                </div>

                <!-- New Workout Assignment Form -->
                <div class="new-assignment-form" id="newWorkoutAssignmentForm" style="display: none;">
                    <div class="form-card">
                        <h3><i class="fas fa-user-plus"></i> Assign Workout Plan to Player/Team</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="workoutPlayerSelect">Player/Team</label>
                                <select id="workoutPlayerSelect" class="form-select">
                                    <option value="">Select Player/Team</option>
                                    <option value="kamal-silva">Kamal Silva</option>
                                    <option value="sarah-fernando">Sarah Fernando</option>
                                    <option value="michael-perera">Michael Perera</option>
                                    <option value="shanali-perera">Shanali Perera</option>
                                    <option value="kasun-rajapaksa">Kasun Rajapaksa</option>
                                    <option value="youth-group">Youth Group (15 players)</option>
                                    <option value="senior-team">Senior Team (18 players)</option>
                                    <option value="junior-team">Junior Team (12 players)</option>
                                    <option value="fast-bowlers">Fast Bowlers Group</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="exerciseSelect">Exercise Videos</label>
                                <select id="exerciseSelect" class="form-select">
                                    <option value="none">None</option>
                                    <option value="dynamic-warmup">Dynamic Warm-up Routine (15 min)</option>
                                    <option value="core-strength">Core Strength Training (25 min)</option>
                                    <option value="cardio-endurance">Cardio Endurance Training (30 min)</option>
                                    <option value="stretching-recovery">Stretching & Recovery (20 min)</option>
                                    <option value="shoulder-mobility">Shoulder Mobility Exercises (20 min)</option>
                                    <option value="power-training">Power Training Exercises (20 min)</option>
                                    <option value="lower-body-power">Lower Body Power Exercises (30 min)</option>
                                    <option value="shoulder-stability">Shoulder Stability Work (20 min)</option>
                                    <option value="functional-strength">Functional Strength Training (25 min)</option>
                                    <option value="flexibility-mobility">Flexibility & Mobility (20 min)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="workoutTypeSelect">Workout Plan Type</label>
                                <select id="workoutTypeSelect" class="form-select">
                                    <option value="none">None</option>
                                    <option value="injury-recovery">Injury Recovery Plan</option>
                                    <option value="tournament-prep">Tournament Preparation</option>
                                    <option value="team-requirement">Team Requirements</option>
                                    <option value="individual-fitness">Individual Fitness</option>
                                    <option value="strength-training">Strength Training</option>
                                    <option value="endurance-building">Endurance Building</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="workoutStartDate">Start Date</label>
                                <input type="date" id="workoutStartDate" class="form-input" value="2025-10-15">
                            </div>
                            
                            <div class="form-group">
                                <label for="workoutDuration">Duration (weeks)</label>
                                <select id="workoutDuration" class="form-select">
                                    <option value="2">2 weeks</option>
                                    <option value="4" selected>4 weeks</option>
                                    <option value="6">6 weeks</option>
                                    <option value="8">8 weeks</option>
                                    <option value="12">12 weeks</option>
                                    <option value="ongoing">Ongoing</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="workoutNotes">Notes (Optional)</label>
                                <textarea id="workoutNotes" class="form-textarea" placeholder="Special instructions, modifications, or notes..."></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="cancelWorkoutAssignment()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn-primary" onclick="saveWorkoutAssignment()">
                                <i class="fas fa-save"></i> Save Assignment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Workout Assignment Table -->
                <div class="assignment-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Player/Group</th>
                                <th>Exercise Video</th>
                                <th>Workout Plan Type</th>
                                <th>Start Date</th>
                                <th>Duration</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">SP</div>
                                        <span>Shanali Perera</span>
                                    </div>
                                </td>
                                <td>
                                    <select class="inline-select exercise-video-select" data-player="shanali-perera">
                                        <option value="none">None</option>
                                        <option value="dynamic-warmup">Dynamic Warm-up Routine</option>
                                        <option value="core-strength">Core Strength Training</option>
                                        <option value="cardio-endurance">Cardio Endurance Training</option>
                                        <option value="stretching-recovery">Stretching & Recovery</option>
                                        <option value="shoulder-mobility" selected>Shoulder Mobility Exercises</option>
                                        <option value="power-training">Power Training Exercises</option>
                                        <option value="lower-body-power">Lower Body Power Exercises</option>
                                        <option value="shoulder-stability">Shoulder Stability Work</option>
                                        <option value="functional-strength">Functional Strength Training</option>
                                        <option value="flexibility-mobility">Flexibility & Mobility</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select workout-type-select" data-player="shanali-perera">
                                        <option value="none">None</option>
                                        <option value="injury-recovery" selected>Injury Recovery</option>
                                        <option value="tournament-prep">Tournament Prep</option>
                                        <option value="team-requirement">Team Requirements</option>
                                        <option value="individual-fitness">Individual Fitness</option>
                                        <option value="strength-training">Strength Training</option>
                                        <option value="endurance-building">Endurance Building</option>
                                    </select>
                                </td>
                                <td>Sep 20, 2025</td>
                                <td>8 weeks</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 45%"></div>
                                        <span class="progress-text">45%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewWorkoutProgress('shanali-perera')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeWorkoutAssignment('shanali-perera')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">ST</div>
                                        <span>Senior Team (18 players)</span>
                                    </div>
                                </td>
                                <td>
                                    <select class="inline-select exercise-video-select" data-player="senior-team">
                                        <option value="none">None</option>
                                        <option value="dynamic-warmup" selected>Dynamic Warm-up Routine</option>
                                        <option value="core-strength">Core Strength Training</option>
                                        <option value="cardio-endurance">Cardio Endurance Training</option>
                                        <option value="stretching-recovery">Stretching & Recovery</option>
                                        <option value="shoulder-mobility">Shoulder Mobility Exercises</option>
                                        <option value="power-training">Power Training Exercises</option>
                                        <option value="lower-body-power">Lower Body Power Exercises</option>
                                        <option value="shoulder-stability">Shoulder Stability Work</option>
                                        <option value="functional-strength">Functional Strength Training</option>
                                        <option value="flexibility-mobility">Flexibility & Mobility</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select workout-type-select" data-player="senior-team">
                                        <option value="none">None</option>
                                        <option value="injury-recovery">Injury Recovery</option>
                                        <option value="tournament-prep" selected>Tournament Prep</option>
                                        <option value="team-requirement">Team Requirements</option>
                                        <option value="individual-fitness">Individual Fitness</option>
                                        <option value="strength-training">Strength Training</option>
                                        <option value="endurance-building">Endurance Building</option>
                                    </select>
                                </td>
                                <td>Oct 1, 2025</td>
                                <td>6 weeks</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 25%"></div>
                                        <span class="progress-text">25%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewWorkoutProgress('senior-team')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeWorkoutAssignment('senior-team')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">FB</div>
                                        <span>Fast Bowlers Group</span>
                                    </div>
                                </td>
                                <td>
                                    <select class="inline-select exercise-video-select" data-player="fast-bowlers">
                                        <option value="none">None</option>
                                        <option value="dynamic-warmup">Dynamic Warm-up Routine</option>
                                        <option value="core-strength" selected>Core Strength Training</option>
                                        <option value="cardio-endurance">Cardio Endurance Training</option>
                                        <option value="stretching-recovery">Stretching & Recovery</option>
                                        <option value="shoulder-mobility">Shoulder Mobility Exercises</option>
                                        <option value="power-training">Power Training Exercises</option>
                                        <option value="lower-body-power">Lower Body Power Exercises</option>
                                        <option value="shoulder-stability">Shoulder Stability Work</option>
                                        <option value="functional-strength">Functional Strength Training</option>
                                        <option value="flexibility-mobility">Flexibility & Mobility</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select workout-type-select" data-player="fast-bowlers">
                                        <option value="none">None</option>
                                        <option value="injury-recovery">Injury Recovery</option>
                                        <option value="tournament-prep">Tournament Prep</option>
                                        <option value="team-requirement" selected>Team Requirements</option>
                                        <option value="individual-fitness">Individual Fitness</option>
                                        <option value="strength-training">Strength Training</option>
                                        <option value="endurance-building">Endurance Building</option>
                                    </select>
                                </td>
                                <td>Sep 15, 2025</td>
                                <td>4 weeks</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 75%"></div>
                                        <span class="progress-text">75%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewWorkoutProgress('fast-bowlers')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeWorkoutAssignment('fast-bowlers')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">KR</div>
                                        <span>Kasun Rajapaksa</span>
                                    </div>
                                </td>
                                <td>
                                    <select class="inline-select exercise-video-select" data-player="kasun-rajapaksa">
                                        <option value="none">None</option>
                                        <option value="dynamic-warmup">Dynamic Warm-up Routine</option>
                                        <option value="core-strength">Core Strength Training</option>
                                        <option value="cardio-endurance" selected>Cardio Endurance Training</option>
                                        <option value="stretching-recovery">Stretching & Recovery</option>
                                        <option value="shoulder-mobility">Shoulder Mobility Exercises</option>
                                        <option value="power-training">Power Training Exercises</option>
                                        <option value="lower-body-power">Lower Body Power Exercises</option>
                                        <option value="shoulder-stability">Shoulder Stability Work</option>
                                        <option value="functional-strength">Functional Strength Training</option>
                                        <option value="flexibility-mobility">Flexibility & Mobility</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="inline-select workout-type-select" data-player="kasun-rajapaksa">
                                        <option value="none">None</option>
                                        <option value="injury-recovery">Injury Recovery</option>
                                        <option value="tournament-prep">Tournament Prep</option>
                                        <option value="team-requirement">Team Requirements</option>
                                        <option value="individual-fitness" selected>Individual Fitness</option>
                                        <option value="strength-training">Strength Training</option>
                                        <option value="endurance-building">Endurance Building</option>
                                    </select>
                                </td>
                                <td>Oct 5, 2025</td>
                                <td>5 weeks</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 15%"></div>
                                        <span class="progress-text">15%</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="action-btn-small" title="View Progress" onclick="viewWorkoutProgress('kasun-rajapaksa')">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                    <button class="action-btn-small" title="Remove Assignment" onclick="removeWorkoutAssignment('kasun-rajapaksa')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="medical-section" class="content-section">
            <div class="medical-header">
                <h1><i class="fas fa-heartbeat"></i> Player Medical Records</h1>
                <p class="access-notice"><i class="fas fa-eye"></i> Read-Only Access - Physical Trainer View</p>
            </div>

        <!-- Row 2: Upcoming Sessions This Week -->
        <div class="schedule-row">
            <div class="schedule-card full-width">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-week"></i> This Week's Schedule</h2>
                        <span class="date-display">Sep 9 - Sep 15, 2025</span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Time</th>
                                <th>Participants</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 10</div>
                                    <div class="table-cell-secondary">Tuesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Advanced Batting Clinic</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-baseball-ball"></i> Batting Technique Focus
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">10:00 AM</div>
                                    <div class="table-cell-secondary">3 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">8 Players</div>
                                    <div class="table-cell-secondary">Indoor Nets</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-active">Confirmed</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sep 11</div>
                                    <div class="table-cell-secondary">Wednesday</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Fitness Assessment Day</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-heartbeat"></i> Monthly Progress Review
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">2:00 PM</div>
                                    <div class="table-cell-secondary">4 hours</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">15 Clients</div>
                                    <div class="table-cell-secondary">Fitness Center</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-upcoming">Scheduled</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 3: Recent Activity and Quick Actions -->
        <div class="schedule-row">
            <div class="schedule-card recent-activity">
                <div class="card-header payment-due">
                    <div class="header-content">
                        <h2><i class="fas fa-bell"></i> Recent Activity</h2>
                    </div>
                </div>
                <div class="card-content">
                    <div class="payment-items">
                        <div class="payment-item">
                            <div class="payment-info">
                                <div class="payment-title">New Client Registration</div>
                                <div class="payment-details">Michael Johnson joined Advanced Program</div>
                            </div>
                            <div class="payment-amount success">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <div class="payment-title">Session Completed</div>
                                <div class="payment-details">Emma Davis - Strength Training</div>
                            </div>
                            <div class="payment-amount">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <div class="payment-title">Program Update</div>
                                <div class="payment-details">Updated workout plan for 3 clients</div>
                            </div>
                            <div class="payment-amount warning">
                                <i class="fas fa-edit"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="schedule-card quick-actions-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    </div>
                </div>
                <div class="card-content">
                    <div class="quick-action-grid">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="quick-action-btn">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Schedule Session</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/exercises" class="quick-action-btn">
                            <i class="fas fa-dumbbell"></i>
                            <span>Manage Exercises</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="quick-action-btn">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/trainer/injury-reports" class="quick-action-btn">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js?v=<?php echo time(); ?>"></script>
