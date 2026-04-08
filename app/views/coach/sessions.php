<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/sessions.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/session-wizard.css">

<!-- Coach Dashboard Layout -->
<div class="coach-layout">
    <!-- Left Sidebar Panel -->
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Coach Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Slot Sessions</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link" data-tooltip="Recommendations">
                        <i class="fas fa-star"></i>
                        <span>Recommendations</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health & Injury</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                        <i class="fas fa-calendar"></i>
                        <span>Events</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1>
                        <i class="fas fa-calendar-alt"></i>
                        Session & Schedule Management
                    </h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">Manage your coaching sessions and schedules</p>
                </div>
                <div class="header-actions">
                    <button class="btn-primary" onclick="openAddSessionModal()">
                        <i class="fas fa-plus"></i>
                        Add New Session
                    </button>
                    <button class="btn-secondary" onclick="refreshCalendar()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <div class="stat-value" id="todaySessions">0</div>
            <div class="stat-label">Today's Sessions</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
            <div class="stat-value" id="weekSessions">0</div>
            <div class="stat-label">This Week</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value" id="totalParticipants">0</div>
            <div class="stat-label">Total Participants</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon purple">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-value" id="avgAttendance">0%</div>
            <div class="stat-label">Average Attendance</div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-group">
                <label>Session Type</label>
                <select id="filterType" onchange="applyFilters()">
                    <option value="">All Types</option>
                    <option value="Coaching">Coaching</option>
                    <option value="Physical Training">Physical Training</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Session Mode</label>
                <select id="filterMode" onchange="applyFilters()">
                    <option value="">All Modes</option>
                    <option value="Group">Group</option>
                    <option value="Private">Private</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Status</label>
                <select id="filterStatus" onchange="applyFilters()">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="filterSearch" placeholder="Search sessions..." onkeyup="applyFilters()">
            </div>
        </div>
    </div>

    <!-- View Tabs & Calendar -->
    <div class="view-tabs">
        <button class="tab-btn active" onclick="switchView('calendar')">
            <i class="fas fa-calendar"></i>
            Calendar
        </button>
        <button class="tab-btn" onclick="switchView('list')">
            <i class="fas fa-list"></i>
            List View
        </button>
    </div>

    <!-- Calendar Container -->
    <div class="calendar-container" id="calendarView">
        <!-- Calendar Header -->
        <div class="calendar-header">
            <div class="calendar-nav">
                <button onclick="previousPeriod()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="calendar-title" id="calendarTitle"><?php echo date('F Y'); ?></h2>
                <button onclick="nextPeriod()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button onclick="goToToday()" style="margin-left: 8px; width: auto; padding: 0 16px;">
                    Today
                </button>
            </div>

            <div class="calendar-view-switcher">
                <button class="view-btn active" onclick="changeCalendarView('month')" data-view="month">
                    Month
                </button>
                <button class="view-btn" onclick="changeCalendarView('week')" data-view="week">
                    Week
                </button>
                <button class="view-btn" onclick="changeCalendarView('day')" data-view="day">
                    Day
                </button>
            </div>
        </div>

        <!-- Month View -->
        <div id="monthView" class="calendar-view-content">
            <div class="calendar-month" id="calendarGrid">
                <!-- Day headers -->
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>
                <!-- Days will be generated by JavaScript -->
            </div>
        </div>

        <!-- Week View -->
        <div id="weekView" class="calendar-view-content" style="display: none;">
            <div class="calendar-week" id="weekGrid">
                <!-- Week grid will be generated by JavaScript -->
            </div>
        </div>

        <!-- Day View -->
        <div id="dayView" class="calendar-view-content" style="display: none;">
            <div class="calendar-day-view" id="dayGrid">
                <!-- Day view will be generated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- List View -->
    <div class="calendar-container" id="listView" style="display: none;">
        <div class="calendar-list" id="listContainer">
            <!-- List items will be generated by JavaScript -->
        </div>
    </div>

    <!-- Upcoming Sessions Table -->
    <div class="sessions-table-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-list"></i>
                Practice Sessions
            </h2>
            <p>All scheduled sessions with quick actions</p>
        </div>

        <div class="table-container">
            <table class="sessions-table" id="sessionsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Session Name</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sessionsTableBody">
                <?php if (!empty($data['sessions'])): ?>
                    <?php foreach ($data['sessions'] as $session): ?>
                        <?php
                            $statusClass = match($session->Status ?? 'active') {
                                'active'    => 'status-active',
                                'cancelled' => 'status-cancelled',
                                'completed' => 'status-completed',
                                default     => 'status-active',
                            };
                            $playerCount = count($session->players ?? []);
                        ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($session->Date)) ?></td>
                            <td><strong><?= htmlspecialchars($session->Name) ?></strong></td>
                            <td><?= htmlspecialchars($session->SessionType) ?></td>
                            <td><?= htmlspecialchars($session->SessionMode) ?></td>
                            <td><?= date('g:i A', strtotime($session->StartTime)) ?> – <?= date('g:i A', strtotime($session->EndTime)) ?></td>
                            <td><?= htmlspecialchars($session->Location ?? '—') ?></td>
                            <td>
                                <span style="font-weight:600;"><?= $playerCount ?> / <?= $session->MaxParticipants ?></span>
                                <?php if ($playerCount > 0): ?>
                                    <button onclick="togglePlayers(<?= $session->SessionID ?>)" style="background:none;border:none;color:#4A90E2;cursor:pointer;font-size:12px;margin-left:6px;">
                                        <i class="fas fa-chevron-down" id="icon-<?= $session->SessionID ?>"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= ucfirst($session->Status ?? 'active') ?>
                                </span>
                            </td>
                        </tr>
                        <?php if ($playerCount > 0): ?>
                        <tr id="players-<?= $session->SessionID ?>" style="display:none; background:#f8faff;">
                            <td colspan="8" style="padding:10px 20px;">
                                <strong style="font-size:13px; color:#2c3e50;">Enrolled Players:</strong>
                                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;">
                                    <?php foreach ($session->players as $player): ?>
                                        <span style="background:#e8f0fe; color:#2c3e50; padding:4px 10px; border-radius:20px; font-size:12px;">
                                            <i class="fas fa-user" style="color:#4A90E2; margin-right:4px;"></i>
                                            <?= htmlspecialchars($player->PlayerName ?? $player->Name ?? 'Player') ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:40px; color:#999;">
                            <i class="fas fa-calendar-times" style="font-size:36px; margin-bottom:12px; display:block; color:#ddd;"></i>
                            No sessions yet. Sessions are scheduled by the admin and will appear here.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    </div> <!-- End main-content -->
</div> <!-- End coach-layout -->

<!-- Session Wizard Modal -->
<div class="modal-overlay" id="sessionWizardModal">
    <div class="wizard-modal">
        <!-- Wizard Header -->
        <div class="wizard-header">
            <div class="wizard-header-content">
                <div class="wizard-title">
                    <i class="fas fa-calendar-plus"></i>
                    <div>
                        <h2>Create New Session</h2>
                        <p>Follow the steps to create your session</p>
                    </div>
                </div>
                <button class="close-wizard" onclick="closeWizard()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="wizard-progress">
            <div class="progress-steps">
                <div class="progress-line" style="width: 0%"></div>
                
                <div class="step-item active">
                    <div class="step-circle">
                        <span>1</span>
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Session Type</div>
                </div>
                
                <div class="step-item">
                    <div class="step-circle">
                        <span>2</span>
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Schedule & Details</div>
                </div>
                
                <div class="step-item">
                    <div class="step-circle">
                        <span>3</span>
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Review</div>
                </div>
            </div>
        </div>

        <!-- Wizard Form -->
        <form id="wizardForm">
            <div class="wizard-body">
                
                <!-- Step 1: Session Type -->
                <div class="wizard-step active" id="step1">
                    <div class="step-header">
                        <h3>
                            <i class="fas fa-clipboard-list"></i>
                            Select Session Type
                        </h3>
                        <p>Choose the type and mode for your session</p>
                    </div>

                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-dumbbell"></i>
                            Session Type <span class="required">*</span>
                        </label>
                        
                        <div class="session-type-cards">
                            <div class="type-card coaching" onclick="selectTypeCard('Coaching')">
                                <input type="radio" name="sessionType" value="Coaching" id="typeCoaching">
                                <label for="typeCoaching">
                                    <div class="type-card-icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <h4>Coaching Session</h4>
                                    <p>Technical cricket training and skill development</p>
                                </label>
                            </div>
                            
                            <div class="type-card physical-training" onclick="selectTypeCard('Physical Training')">
                                <input type="radio" name="sessionType" value="Physical Training" id="typePhysical">
                                <label for="typePhysical">
                                    <div class="type-card-icon">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <h4>Physical Training</h4>
                                    <p>Fitness, strength, and conditioning exercises</p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-users"></i>
                            Session Mode <span class="required">*</span>
                        </label>
                        
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="sessionMode" value="Group" id="modeGroup" checked>
                                <label for="modeGroup">
                                    <i class="fas fa-users"></i>
                                    Group Session
                                </label>
                            </div>
                            
                            <div class="radio-option">
                                <input type="radio" name="sessionMode" value="Private" id="modePrivate">
                                <label for="modePrivate">
                                    <i class="fas fa-user"></i>
                                    Private Session
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Schedule & Details -->
                <div class="wizard-step" id="step2">
                    <div class="step-header">
                        <h3>
                            <i class="fas fa-calendar-alt"></i>
                            Schedule & Details
                        </h3>
                        <p>Set the date, time, location, and session details</p>
                    </div>

                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-heading"></i>
                            Session Name <span class="required">*</span>
                        </label>
                        <input type="text" id="sessionName" name="sessionName" 
                               placeholder="e.g., Batting Techniques, Fitness Training">
                        <div class="form-help">Give your session a descriptive name</div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar"></i>
                                Session Date <span class="required">*</span>
                            </label>
                            <input type="date" id="sessionDate" name="sessionDate" 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Start Time <span class="required">*</span>
                            </label>
                            <input type="time" id="startTime" name="startTime">
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                End Time <span class="required">*</span>
                            </label>
                            <input type="time" id="endTime" name="endTime">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-map-marker-alt"></i>
                                Location <span class="required">*</span>
                            </label>
                            <input type="text" id="location" name="location" 
                                   placeholder="e.g., Main Ground, Indoor Nets">
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-users"></i>
                                Max Participants <span class="required">*</span>
                            </label>
                            <input type="number" id="maxParticipants" name="maxParticipants" 
                                   value="10" min="1" max="50">
                            <div class="form-help">Maximum number of players</div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-dollar-sign"></i>
                                Price Per Session
                            </label>
                            <input type="number" id="pricePerSession" name="pricePerSession" 
                                   value="0.00" min="0" step="0.01" placeholder="0.00">
                            <div class="form-help">0 for group sessions (monthly subscription)</div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <div class="checkbox-option">
                            <input type="checkbox" id="isRecurring" name="isRecurring" checked>
                            <label for="isRecurring">
                                <i class="fas fa-redo"></i>
                                This is a recurring regular session
                            </label>
                        </div>
                        <div class="form-help">Uncheck for one-time special sessions</div>
                    </div>
                </div>

                <!-- Step 3: Review & Confirm -->
                <div class="wizard-step" id="step3">
                    <div class="step-header">
                        <h3>
                            <i class="fas fa-check-circle"></i>
                            Review & Confirm
                        </h3>
                        <p>Please review the session details before creating</p>
                    </div>

                    <div class="summary-grid">
                        <!-- Session Type Info -->
                        <div class="summary-card">
                            <h4>
                                <i class="fas fa-clipboard-list"></i>
                                Session Information
                            </h4>
                            <div class="summary-row">
                                <span class="summary-label">Type</span>
                                <span class="summary-value" id="summaryType">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Mode</span>
                                <span class="summary-value" id="summaryMode">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Session Name</span>
                                <span class="summary-value" id="summaryName">-</span>
                            </div>
                        </div>

                        <!-- Schedule Info -->
                        <div class="summary-card">
                            <h4>
                                <i class="fas fa-calendar-alt"></i>
                                Schedule
                            </h4>
                            <div class="summary-row">
                                <span class="summary-label">Date</span>
                                <span class="summary-value" id="summaryDate">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Time</span>
                                <span class="summary-value" id="summaryTime">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Location</span>
                                <span class="summary-value" id="summaryLocation">-</span>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="summary-card">
                            <h4>
                                <i class="fas fa-info-circle"></i>
                                Additional Details
                            </h4>
                            <div class="summary-row">
                                <span class="summary-label">Max Participants</span>
                                <span class="summary-value" id="summaryMaxParticipants">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Price</span>
                                <span class="summary-value" id="summaryPrice">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Recurring</span>
                                <span class="summary-value" id="summaryRecurring">-</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <!-- Wizard Footer -->
        <div class="wizard-footer">
            <div>
                Step <strong id="currentStepNum">1</strong> of <strong>3</strong>
            </div>
            
            <div class="wizard-actions">
                <button type="button" class="wizard-btn wizard-btn-secondary" id="prevBtn" 
                        onclick="prevStep()" disabled>
                    <i class="fas fa-arrow-left"></i>
                    Previous
                </button>
                
                <button type="button" class="wizard-btn wizard-btn-primary" id="nextBtn" 
                        onclick="nextStep()">
                    Next
                    <i class="fas fa-arrow-right"></i>
                </button>
                
                <button type="button" class="wizard-btn wizard-btn-success" id="submitBtn" 
                        onclick="submitSession()" style="display: none;">
                    <i class="fas fa-check"></i>
                    Create Session
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Calendar JavaScript -->
<script>
// ================================================
// CALENDAR CONFIGURATION
// ================================================

const calendarState = {
    currentDate: new Date(), // Use current date
    viewMode: 'month', // month, week, day
    displayMode: 'calendar', // calendar, list
    sessions: [] // Will be populated from database
};

// Note: Sample data removed - now loading from database via API

// ================================================
// INITIALIZATION
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Load sessions from database
    loadSessionsFromDatabase();
});

/**
 * Load sessions from database via API
 */
function loadSessionsFromDatabase() {
    console.log('Loading sessions from database...');
    
    fetch(`<?php echo URLROOT; ?>/coach/get_sessions_list?scope=all`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            
            if (data.success && data.sessions) {
                // Map database fields to frontend format
                calendarState.sessions = data.sessions.map(session => ({
                    id: parseInt(session.SessionID),
                    name: session.Name,
                    sessionType: session.SessionType,
                    sessionMode: session.SessionMode,
                    date: session.Date, // Already in YYYY-MM-DD format from MySQL
                    startTime: session.StartTime,
                    endTime: session.EndTime,
                    location: session.Location || 'TBA',
                    maxParticipants: parseInt(session.MaxParticipants) || 0,
                    currentParticipants: parseInt(session.ParticipantCount) || 0,
                    status: session.Status,
                    price: parseFloat(session.PricePerSession) || 0,
                    isRecurring: session.IsRecurring == 1
                }));
                
                console.log('✅ Loaded sessions:', calendarState.sessions);
                console.log(`📊 Total: ${calendarState.sessions.length} session(s)`);
                
                // Initialize views
                updateStatistics();
                renderCalendar();
                renderSessionsTable();
                
                // Show success notification if sessions loaded
                if (calendarState.sessions.length > 0) {
                    showNotification(`✅ Loaded ${calendarState.sessions.length} session(s)`, 'success');
                }
            } else {
                console.warn('No sessions found or API error:', data);
                calendarState.sessions = [];
                updateStatistics();
                renderCalendar();
                renderSessionsTable();
            }
        })
        .catch(error => {
            console.error('Error loading sessions:', error);
            showNotification('⚠️ Failed to load sessions from database', 'error');
            
            // Initialize with empty state
            calendarState.sessions = [];
            updateStatistics();
            renderCalendar();
            renderSessionsTable();
        });
}

// ================================================
// STATISTICS
// ================================================

function updateStatistics() {
    const today = new Date(2025, 9, 21);
    const todayStr = formatDate(today);
    
    // Today's sessions
    const todaySessions = calendarState.sessions.filter(s => s.date === todayStr && s.status === 'active');
    document.getElementById('todaySessions').textContent = todaySessions.length;
    
    // This week's sessions
    const weekStart = getWeekStart(today);
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    const weekSessions = calendarState.sessions.filter(s => {
        const sessionDate = new Date(s.date);
        return sessionDate >= weekStart && sessionDate <= weekEnd && s.status === 'active';
    });
    document.getElementById('weekSessions').textContent = weekSessions.length;
    
    // Total participants
    const totalParticipants = calendarState.sessions.reduce((sum, s) => sum + s.currentParticipants, 0);
    document.getElementById('totalParticipants').textContent = totalParticipants;
    
    // Average attendance
    const avgAttendance = calendarState.sessions.length > 0 
        ? Math.round((totalParticipants / calendarState.sessions.reduce((sum, s) => sum + s.maxParticipants, 0)) * 100)
        : 0;
    document.getElementById('avgAttendance').textContent = avgAttendance + '%';
}

// ================================================
// CALENDAR RENDERING
// ================================================

function renderCalendar() {
    if (calendarState.viewMode === 'month') {
        renderMonthView();
    } else if (calendarState.viewMode === 'week') {
        renderWeekView();
    } else if (calendarState.viewMode === 'day') {
        renderDayView();
    }
    updateCalendarTitle();
}

function renderMonthView() {
    const grid = document.getElementById('calendarGrid');
    
    // Keep day headers, clear the rest
    while (grid.children.length > 7) {
        grid.removeChild(grid.lastChild);
    }
    
    const year = calendarState.currentDate.getFullYear();
    const month = calendarState.currentDate.getMonth();
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    // Get previous month's last days
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    
    // Render previous month's trailing days
    for (let i = startingDayOfWeek - 1; i >= 0; i--) {
        const day = prevMonthLastDay - i;
        const dayEl = createDayElement(day, true, year, month - 1);
        grid.appendChild(dayEl);
    }
    
    // Render current month's days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayEl = createDayElement(day, false, year, month);
        grid.appendChild(dayEl);
    }
    
    // Render next month's leading days
    const totalCells = grid.children.length - 7; // Subtract header row
    const remainingCells = 42 - totalCells; // 6 weeks * 7 days
    for (let day = 1; day <= remainingCells; day++) {
        const dayEl = createDayElement(day, true, year, month + 1);
        grid.appendChild(dayEl);
    }
}

function createDayElement(day, isOtherMonth, year, month) {
    const dayEl = document.createElement('div');
    dayEl.className = 'calendar-day';
    
    if (isOtherMonth) {
        dayEl.classList.add('other-month');
    }
    
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const today = formatDate(new Date(2025, 9, 21));
    
    if (dateStr === today) {
        dayEl.classList.add('today');
    }
    
    // Day number
    const dayNumber = document.createElement('div');
    dayNumber.className = 'day-number';
    dayNumber.textContent = day;
    dayEl.appendChild(dayNumber);
    
    // Events container
    const eventsContainer = document.createElement('div');
    eventsContainer.className = 'calendar-events';
    
    // Get sessions for this day
    const daySessions = calendarState.sessions.filter(s => s.date === dateStr);
    
    if (daySessions.length > 0) {
        dayEl.classList.add('has-sessions');
        
        daySessions.slice(0, 3).forEach(session => {
            const eventEl = document.createElement('div');
            eventEl.className = `calendar-event ${session.sessionType.toLowerCase().replace(' ', '-')}`;
            eventEl.innerHTML = `
                <div class="event-time">${formatTime(session.startTime)}</div>
                <div>${session.name}</div>
            `;
            eventEl.onclick = (e) => {
                e.stopPropagation();
                viewSessionDetails(session);
            };
            eventsContainer.appendChild(eventEl);
        });
        
        if (daySessions.length > 3) {
            const moreEl = document.createElement('div');
            moreEl.className = 'calendar-event';
            moreEl.style.background = '#718096';
            moreEl.textContent = `+${daySessions.length - 3} more`;
            eventsContainer.appendChild(moreEl);
        }
    }
    
    dayEl.appendChild(eventsContainer);
    
    // Click handler for day
    dayEl.onclick = () => {
        if (!isOtherMonth) {
            openAddSessionModal(dateStr);
        }
    };
    
    return dayEl;
}

function renderWeekView() {
    const grid = document.getElementById('weekGrid');
    grid.innerHTML = '';
    
    // Create header row
    const weekStart = getWeekStart(calendarState.currentDate);
    
    // Time column header
    const timeHeader = document.createElement('div');
    timeHeader.className = 'week-time-label';
    timeHeader.textContent = 'Time';
    grid.appendChild(timeHeader);
    
    // Day headers
    for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(date.getDate() + i);
        
        const dayHeader = document.createElement('div');
        dayHeader.className = 'week-day-header';
        dayHeader.textContent = `${getDayName(date.getDay())} ${date.getDate()}`;
        grid.appendChild(dayHeader);
    }
    
    grid.innerHTML += '<div style="grid-column: 1 / -1; height: 1px; background: #e2e8f0;"></div>';
    
    // Placeholder message
    const placeholder = document.createElement('div');
    placeholder.style.gridColumn = '1 / -1';
    placeholder.style.padding = '40px';
    placeholder.style.textAlign = 'center';
    placeholder.style.color = '#718096';
    placeholder.innerHTML = '<i class="fas fa-calendar-week" style="font-size: 48px; margin-bottom: 16px; display: block;"></i><p>Week view coming soon</p>';
    grid.appendChild(placeholder);
}

function renderDayView() {
    const grid = document.getElementById('dayGrid');
    grid.innerHTML = '';
    
    // Placeholder message
    const placeholder = document.createElement('div');
    placeholder.style.padding = '40px';
    placeholder.style.textAlign = 'center';
    placeholder.style.color = '#718096';
    placeholder.innerHTML = '<i class="fas fa-calendar-day" style="font-size: 48px; margin-bottom: 16px; display: block;"></i><p>Day view coming soon</p>';
    grid.appendChild(placeholder);
}

function renderListView() {
    const container = document.getElementById('listContainer');
    container.innerHTML = '';
    
    if (calendarState.sessions.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No Sessions Found</h3>
                <p>Start by creating your first session</p>
                <button class="btn-primary" onclick="openAddSessionModal()">
                    <i class="fas fa-plus"></i>
                    Add Session
                </button>
            </div>
        `;
        return;
    }
    
    // Sort sessions by date
    const sortedSessions = [...calendarState.sessions].sort((a, b) => {
        return new Date(a.date + ' ' + a.startTime) - new Date(b.date + ' ' + b.startTime);
    });
    
    sortedSessions.forEach(session => {
        const card = createListCard(session);
        container.appendChild(card);
    });
}

function createListCard(session) {
    const card = document.createElement('div');
    card.className = 'list-session-card';
    
    const sessionDate = new Date(session.date);
    
    card.innerHTML = `
        <div class="session-date-badge">
            <div class="session-day">${sessionDate.getDate()}</div>
            <div class="session-month">${getMonthName(sessionDate.getMonth())}</div>
        </div>
        
        <div class="session-details">
            <div class="session-title">${session.name}</div>
            <div class="session-meta">
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    ${formatTime(session.startTime)} - ${formatTime(session.endTime)}
                </div>
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    ${session.location}
                </div>
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    ${session.currentParticipants}/${session.maxParticipants} participants
                </div>
                <div class="meta-item">
                    <i class="fas fa-users-cog"></i>
                    ${session.sessionMode}
                </div>
            </div>
        </div>
        
        <div class="session-type-badge ${session.sessionType.toLowerCase().replace(' ', '-')}">
            ${session.sessionType}
        </div>
    `;
    
    card.onclick = () => viewSessionDetails(session);
    
    return card;
}

// ================================================
// SESSIONS TABLE
// ================================================

function renderSessionsTable() {
    const tbody = document.getElementById('sessionsTableBody');
    tbody.innerHTML = '';
    
    if (calendarState.sessions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                    <p style="color: #999; margin: 0;">No sessions scheduled yet. Create your first session!</p>
                </td>
            </tr>
        `;
        return;
    }
    
    // Sort sessions by date and time (show ALL sessions)
    const sortedSessions = [...calendarState.sessions].sort((a, b) => {
        return new Date(a.date + ' ' + a.startTime) - new Date(b.date + ' ' + b.startTime);
    });

    if (sortedSessions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                    <p style="color: #999; margin: 0;">No sessions found. Create your first session!</p>
                </td>
            </tr>
        `;
        return;
    }

    // Render all sessions (past and future)
    sortedSessions.forEach(session => {
        const row = createTableRow(session);
        tbody.appendChild(row);
    });
}

function createTableRow(session) {
    const tr = document.createElement('tr');
    
    const sessionDate = new Date(session.date);
    const formattedDate = sessionDate.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
    
    const typeClass = session.sessionType.toLowerCase().replace(' ', '-');
    const modeClass = session.sessionMode.toLowerCase();
    
    // Check if session is in the past (can be deleted)
    const sessionEndDateTime = new Date(`${session.date} ${session.endTime}`);
    const now = new Date();
    const canDelete = sessionEndDateTime < now;
    
    // Build delete button HTML conditionally
    const deleteButtonHTML = canDelete ? `
        <button class="action-btn action-btn-delete" onclick="deleteSession(${session.id}, event)">
            <i class="fas fa-trash-alt"></i>
            Delete
        </button>
    ` : `
        <button class="action-btn action-btn-delete disabled" 
                title="Only past sessions can be deleted" 
                style="opacity: 0.5; cursor: not-allowed;" 
                disabled>
            <i class="fas fa-trash-alt"></i>
            Delete
        </button>
    `;
    
    tr.innerHTML = `
        <td class="table-date">${formattedDate}</td>
        <td class="table-session-name">${session.name}</td>
        <td><span class="table-badge ${typeClass}">${session.sessionType}</span></td>
        <td><span class="table-badge ${modeClass}">${session.sessionMode}</span></td>
        <td class="table-time">${formatTime(session.startTime)} - ${formatTime(session.endTime)}</td>
        <td class="table-location">${session.location}</td>
        <td class="table-participants">
            <span class="current">${session.currentParticipants}</span>/<span class="max">${session.maxParticipants}</span>
        </td>
        <td>
            <div class="table-actions">
                <button class="action-btn action-btn-edit" onclick="editSession(${session.id})">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                ${deleteButtonHTML}
            </div>
        </td>
    `;
    
    return tr;
}

function editSession(sessionId) {
    const session = calendarState.sessions.find(s => s.id === sessionId);
    if (!session) {
        showNotification('❌ Session not found', 'error');
        return;
    }
    
    console.log('Editing session:', session);
    console.log('Session data:', {
        name: session.name,
        date: session.date,
        startTime: session.startTime,
        endTime: session.endTime,
        location: session.location
    });
    
    // Set wizard to edit mode BEFORE opening
    wizardState.isEditMode = true;
    wizardState.editSessionId = sessionId;
    
    // Pre-fill form data with session values
    wizardState.formData = {
        sessionType: session.sessionType,
        sessionMode: session.sessionMode,
        name: session.name,
        date: session.date,
        startTime: session.startTime,
        endTime: session.endTime,
        location: session.location,
        maxParticipants: session.maxParticipants,
        pricePerSession: session.price.toFixed(2),
        isRecurring: session.isRecurring
    };
    
    // Open wizard
    openAddSessionModal();
    
    // Wait for modal to be visible, then populate
    setTimeout(() => {
        // Update wizard title
        const wizardTitle = document.querySelector('#sessionWizardModal h2');
        if (wizardTitle) {
            wizardTitle.textContent = 'Edit Session';
        }
        
        // Update submit button text in Step 3
        updateWizardButtonText();
        
        // Make Step 1 fields READ-ONLY (Session Type & Mode cannot be changed)
        const typeRadios = document.querySelectorAll('input[name="sessionType"]');
        const modeRadios = document.querySelectorAll('input[name="sessionMode"]');
        const typeCards = document.querySelectorAll('.type-card');
        
        typeRadios.forEach(radio => {
            radio.disabled = true;
            if (radio.closest('.type-card')) {
                radio.closest('.type-card').style.opacity = '0.6';
                radio.closest('.type-card').style.cursor = 'not-allowed';
                radio.closest('.type-card').onclick = null;
            }
        });
        
        modeRadios.forEach(radio => {
            radio.disabled = true;
            if (radio.closest('.radio-option')) {
                radio.closest('.radio-option').style.opacity = '0.6';
                radio.closest('.radio-option').style.cursor = 'not-allowed';
            }
        });
        
        // Add read-only indicator to Step 1
        const step1Header = document.querySelector('#step1 .step-header p');
        if (step1Header) {
            step1Header.innerHTML = '<span style="color: #f59e0b; font-weight: 600;">⚠️ Session Type and Mode cannot be changed</span>';
        }
        
        // Populate Step 1 (Type & Mode)
        if (session.sessionType) {
            const typeRadio = document.querySelector(`input[name="sessionType"][value="${session.sessionType}"]`);

            if (typeRadio) {
                typeRadio.checked = true;
                selectTypeCard(session.sessionType);
            }
        }
        if (session.sessionMode) {
            const modeRadio = document.querySelector(`input[name="sessionMode"][value="${session.sessionMode}"]`);
            if (modeRadio) {
                modeRadio.checked = true;
            }
        }
        
        // Populate Step 2 (Details)
        const nameField = document.getElementById('sessionName');
        const dateField = document.getElementById('sessionDate');
        const startTimeField = document.getElementById('startTime');
        const endTimeField = document.getElementById('endTime');
        const locationField = document.getElementById('location');
        
        if (nameField) nameField.value = session.name || '';
        if (dateField) dateField.value = session.date || '';
        if (startTimeField) startTimeField.value = session.startTime || '';
        if (endTimeField) endTimeField.value = session.endTime || '';
        if (locationField) locationField.value = session.location || '';
        
        console.log('Populated fields:', {
            name: nameField?.value,
            date: dateField?.value,
            startTime: startTimeField?.value,
            endTime: endTimeField?.value,
            location: locationField?.value
        });
        
        // Populate Step 3 (Settings)
        const maxParticipantsField = document.getElementById('maxParticipants');
        const priceField = document.getElementById('pricePerSession');
        const recurringField = document.getElementById('isRecurring');
        
        if (maxParticipantsField) maxParticipantsField.value = session.maxParticipants || 10;
        if (priceField) priceField.value = session.price || 0;
        if (recurringField) recurringField.checked = session.isRecurring;
        
        // Update the summary preview if on step 3
        if (wizardState.currentStep === 3) {
            updateSummaryPreview();
        }
    }, 200);
}

// Helper function to update wizard button text based on mode
function updateWizardButtonText() {
    // Find the submit button in step 3
    const step3 = document.querySelector('[data-step="3"]');
    if (step3) {
        const submitButton = step3.querySelector('.btn-primary');
        if (submitButton) {
            submitButton.textContent = wizardState.isEditMode ? 'Update Session' : 'Create Session';
        }
    }
}

function deleteSession(sessionId, event) {
    const session = calendarState.sessions.find(s => s.id === sessionId);
    if (session) {
        // Check if session is in the past
        const sessionEndDateTime = new Date(`${session.date} ${session.endTime}`);
        const now = new Date();
        
        if (sessionEndDateTime > now) {
            showNotification('❌ Cannot delete upcoming sessions. Only past sessions can be deleted.', 'error');
            return;
        }
        
        if (confirm(`Are you sure you want to delete "${session.name}"?\n\nThis action cannot be undone.`)) {
            // Get the button element - check if event is passed or use the clicked element
            let deleteBtn = null;
            if (event && event.target) {
                deleteBtn = event.target.closest('.action-btn-delete');
            }
            
            const originalText = deleteBtn ? deleteBtn.innerHTML : '';
            if (deleteBtn) {
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            
            console.log('Deleting session:', sessionId);
            
            // Send delete request to backend
            fetch(`<?php echo URLROOT; ?>/coach/delete_session/${sessionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ sessionId: sessionId })
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Delete response:', data);
                if (data.success) {
                    // Reload sessions from database
                    showNotification('✅ Session deleted successfully!', 'success');
                    loadSessionsFromDatabase();
                } else {
                    // Show error message
                    showNotification('❌ ' + (data.message || 'Failed to delete session'), 'error');
                    if (deleteBtn) {
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('Error deleting session:', error);
                showNotification('❌ Network error: ' + error.message, 'error');
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalText;
                }
            });
        }
    }
}

// Notification helper function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ================================================
// NAVIGATION
// ================================================

function previousPeriod() {
    if (calendarState.viewMode === 'month') {
        calendarState.currentDate.setMonth(calendarState.currentDate.getMonth() - 1);
    } else if (calendarState.viewMode === 'week') {
        calendarState.currentDate.setDate(calendarState.currentDate.getDate() - 7);
    } else {
        calendarState.currentDate.setDate(calendarState.currentDate.getDate() - 1);
    }
    renderCalendar();
}

function nextPeriod() {
    if (calendarState.viewMode === 'month') {
        calendarState.currentDate.setMonth(calendarState.currentDate.getMonth() + 1);
    } else if (calendarState.viewMode === 'week') {
        calendarState.currentDate.setDate(calendarState.currentDate.getDate() + 7);
    } else {
        calendarState.currentDate.setDate(calendarState.currentDate.getDate() + 1);
    }
    renderCalendar();
}

function goToToday() {
    calendarState.currentDate = new Date();
    renderCalendar();
}

function changeCalendarView(view) {
    calendarState.viewMode = view;
    
    // Update button states
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.view === view) {
            btn.classList.add('active');
        }
    });
    
    // Show/hide views
    document.getElementById('monthView').style.display = view === 'month' ? 'block' : 'none';
    document.getElementById('weekView').style.display = view === 'week' ? 'block' : 'none';
    document.getElementById('dayView').style.display = view === 'day' ? 'block' : 'none';
    
    renderCalendar();
}

function switchView(view) {
    calendarState.displayMode = view;
    
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Show/hide containers
    document.getElementById('calendarView').style.display = view === 'calendar' ? 'block' : 'none';
    document.getElementById('listView').style.display = view === 'list' ? 'block' : 'none';
    
    if (view === 'list') {
        renderListView();
    }
}

function updateCalendarTitle() {
    const titleEl = document.getElementById('calendarTitle');
    
    if (calendarState.viewMode === 'month') {
        titleEl.textContent = `${getMonthName(calendarState.currentDate.getMonth())} ${calendarState.currentDate.getFullYear()}`;
    } else if (calendarState.viewMode === 'week') {
        const weekStart = getWeekStart(calendarState.currentDate);
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekEnd.getDate() + 6);
        titleEl.textContent = `${getMonthName(weekStart.getMonth())} ${weekStart.getDate()} - ${getMonthName(weekEnd.getMonth())} ${weekEnd.getDate()}, ${weekStart.getFullYear()}`;
    } else {
        titleEl.textContent = `${getMonthName(calendarState.currentDate.getMonth())} ${calendarState.currentDate.getDate()}, ${calendarState.currentDate.getFullYear()}`;
    }
}

// ================================================
// UTILITY FUNCTIONS
// ================================================

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatTime(timeStr) {
    const [hours, minutes] = timeStr.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

function getMonthName(month) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
    return months[month];
}

function getDayName(day) {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    return days[day];
}

function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day;
    return new Date(d.setDate(diff));
}

// ================================================
// MODAL FUNCTIONS (Placeholders)
// ================================================

function viewSessionDetails(session) {
    alert(`Session Details\n\nName: ${session.name}\nType: ${session.sessionType}\nMode: ${session.sessionMode}\nDate: ${session.date}\nTime: ${formatTime(session.startTime)} - ${formatTime(session.endTime)}\nLocation: ${session.location}\nParticipants: ${session.currentParticipants}/${session.maxParticipants}\n\nDetailed modal coming next!`);
}

function refreshCalendar() {
    renderCalendar();
    updateStatistics();
    alert('Calendar refreshed!');
}

function applyFilters() {
    const type = document.getElementById('filterType').value;
    const mode = document.getElementById('filterMode').value;
    const status = document.getElementById('filterStatus').value;
    const search = document.getElementById('filterSearch').value.toLowerCase();
    
    // Filter the calendar sessions
    const filtered = calendarState.sessions.filter(session => {
        if (type && session.sessionType !== type) return false;
        if (mode && session.sessionMode !== mode) return false;
        if (status && session.status !== status) return false;
        if (search && !session.name.toLowerCase().includes(search) && !session.location.toLowerCase().includes(search)) return false;
        return true;
    });
    
    // Re-render table with filtered sessions
    const tbody = document.getElementById('sessionsTableBody');
    if (tbody) {
        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:20px; color:#999;">No sessions match filters</td></tr>';
        } else {
            tbody.innerHTML = filtered.map(session => `
                <tr>
                    <td><strong>${session.name}</strong></td>
                    <td><span class="badge">${session.sessionType}</span></td>
                    <td><span class="badge">${session.sessionMode}</span></td>
                    <td>${session.date}</td>
                    <td>${formatTime(session.startTime)} - ${formatTime(session.endTime)}</td>
                    <td><span class="status-badge status-${session.status}">${session.status}</span></td>
                    <td>${session.currentParticipants}/${session.maxParticipants}</td>
                </tr>
            `).join('');
        }
    }
}

// ================================================
// SESSION WIZARD
// ================================================

let wizardState = {
    currentStep: 1,
    totalSteps: 3,
    formData: {},
    isEditMode: false,
    editSessionId: null
};

function openAddSessionModal() {
    const modal = document.getElementById('sessionWizardModal');
    modal.classList.add('active');
    
    // Only reset if not in edit mode
    if (!wizardState.isEditMode) {
        resetWizard();
        
        // Ensure all fields are enabled for new session creation
        setTimeout(() => {
            // Re-enable Session Type radio buttons
            const typeRadios = document.querySelectorAll('input[name="sessionType"]');
            const typeCards = document.querySelectorAll('.type-card');
            
            typeRadios.forEach(radio => {
                radio.disabled = false;
                if (radio.closest('.type-card')) {
                    radio.closest('.type-card').style.opacity = '1';
                    radio.closest('.type-card').style.cursor = 'pointer';
                    // Restore onclick handler
                    const card = radio.closest('.type-card');
                    card.onclick = function() {
                        selectTypeCard(radio.value);
                    };
                }
            });
            
            // Re-enable Session Mode radio buttons
            const modeRadios = document.querySelectorAll('input[name="sessionMode"]');
            modeRadios.forEach(radio => {
                radio.disabled = false;
                if (radio.closest('.radio-option')) {
                    radio.closest('.radio-option').style.opacity = '1';
                    radio.closest('.radio-option').style.cursor = 'pointer';
                }
            });
            
            // Reset Step 1 header text
            const step1Header = document.querySelector('#step1 .step-header p');
            if (step1Header) {
                step1Header.textContent = 'Choose the type and mode for your session';
            }
        }, 50);
    }
}

function closeWizard() {
    const modal = document.getElementById('sessionWizardModal');
    modal.classList.remove('active');
    setTimeout(() => {
        resetWizard();
        // Reset edit mode flags
        wizardState.isEditMode = false;
        wizardState.editSessionId = null;
        
        // Reset wizard title and button
        document.querySelector('#sessionWizardModal h2').textContent = 'Create New Session';
        const submitBtn = document.querySelector('.wizard-footer .btn-primary');
        if (submitBtn) {
            submitBtn.textContent = 'Create Session';
        }
        
        // Re-enable all fields when closing
        const typeRadios = document.querySelectorAll('input[name="sessionType"]');
        typeRadios.forEach(radio => {
            radio.disabled = false;
            if (radio.closest('.type-card')) {
                radio.closest('.type-card').style.opacity = '1';
                radio.closest('.type-card').style.cursor = 'pointer';
                const card = radio.closest('.type-card');
                card.onclick = function() {
                    selectTypeCard(radio.value);
                };
            }
        });
        
        const modeRadios = document.querySelectorAll('input[name="sessionMode"]');
        modeRadios.forEach(radio => {
            radio.disabled = false;
            if (radio.closest('.radio-option')) {
                radio.closest('.radio-option').style.opacity = '1';
                radio.closest('.radio-option').style.cursor = 'pointer';
            }
        });
        
        // Reset Step 1 header text
        const step1Header = document.querySelector('#step1 .step-header p');
        if (step1Header) {
            step1Header.textContent = 'Choose the type and mode for your session';
        }
    }, 300);
}

function resetWizard() {
    wizardState.currentStep = 1;
    
    // Only reset formData if not in edit mode
    if (!wizardState.isEditMode) {
        wizardState.formData = {};
    }
    
    // Reset all form fields
    document.getElementById('wizardForm').reset();
    
    // Show first step
    showStep(1);
    
    // Reset progress
    updateProgress();
}

function nextStep() {
    if (validateStep(wizardState.currentStep)) {
        saveStepData(wizardState.currentStep);
        
        if (wizardState.currentStep < wizardState.totalSteps) {
            wizardState.currentStep++;
            showStep(wizardState.currentStep);
            updateProgress();
        }
    }
}

function prevStep() {
    if (wizardState.currentStep > 1) {
        wizardState.currentStep--;
        showStep(wizardState.currentStep);
        updateProgress();
    }
}

function showStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show current step
    document.getElementById(`step${stepNumber}`).classList.add('active');
    
    // Update buttons
    updateButtons();
    
    // If showing summary (step 3), populate it and update button text
    if (stepNumber === 3) {
        populateSummary();
        updateWizardButtonText();
    }
}

function updateProgress() {
    // Update step circles
    document.querySelectorAll('.step-item').forEach((item, index) => {
        const stepNum = index + 1;
        item.classList.remove('active', 'completed');
        
        if (stepNum < wizardState.currentStep) {
            item.classList.add('completed');
        } else if (stepNum === wizardState.currentStep) {
            item.classList.add('active');
        }
    });
    
    // Update progress line
    const progressPercentage = ((wizardState.currentStep - 1) / (wizardState.totalSteps - 1)) * 100;
    const progressLine = document.querySelector('.progress-line');
    progressLine.style.width = `calc(${progressPercentage}% - 100px)`;
}

function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const currentStepNum = document.getElementById('currentStepNum');
    
    // Update step counter
    currentStepNum.textContent = wizardState.currentStep;
    
    // Prev button
    prevBtn.disabled = wizardState.currentStep === 1;
    
    // Next/Submit buttons
    if (wizardState.currentStep === wizardState.totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }
}

function validateStep(stepNumber) {
    let isValid = true;
    let errors = [];
    
    // Clear previous error styling
    document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    
    if (stepNumber === 1) {
        const sessionType = document.querySelector('input[name="sessionType"]:checked');
        const sessionMode = document.querySelector('input[name="sessionMode"]:checked');
        
        if (!sessionType) {
            const typeContainer = document.querySelector('.session-type-cards');
            highlightError(typeContainer, 'Please select a session type');
            isValid = false;
        }
        if (!sessionMode) {
            const modeContainer = document.querySelector('.radio-group');
            highlightError(modeContainer, 'Please select a session mode');
            isValid = false;
        }
    } else if (stepNumber === 2) {
        const nameField = document.getElementById('sessionName');
        const dateField = document.getElementById('sessionDate');
        const startTimeField = document.getElementById('startTime');
        const endTimeField = document.getElementById('endTime');
        const locationField = document.getElementById('location');
        const maxParticipantsField = document.getElementById('maxParticipants');
        const priceField = document.getElementById('pricePerSession');
        
        const name = nameField.value.trim();
        const date = dateField.value;
        const startTime = startTimeField.value;
        const endTime = endTimeField.value;
        const location = locationField.value.trim();
        const maxParticipants = maxParticipantsField.value;
        const price = priceField.value;
        
        // Session name validation
        if (!name) {
            highlightError(nameField, 'Session name is required');
            isValid = false;
        } else if (name.length < 3) {
            highlightError(nameField, 'Session name must be at least 3 characters');
            isValid = false;
        } else if (name.length > 100) {
            highlightError(nameField, 'Session name must not exceed 100 characters');
            isValid = false;
        }
        
        // Date validation
        if (!date) {
            highlightError(dateField, 'Session date is required');
            isValid = false;
        } else {
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                highlightError(dateField, 'Session date cannot be in the past');
                isValid = false;
            }
        }
        
        // Time validation
        if (!startTime) {
            highlightError(startTimeField, 'Start time is required');
            isValid = false;
        }
        
        if (!endTime) {
            highlightError(endTimeField, 'End time is required');
            isValid = false;
        }
        
        if (startTime && endTime && startTime >= endTime) {
            highlightError(startTimeField, 'End time must be after start time');
            highlightError(endTimeField, 'End time must be after start time');
            isValid = false;
        }
        
        // Check if session duration is reasonable (at least 30 minutes)
        if (startTime && endTime && startTime < endTime) {
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + endTime);
            const durationMinutes = (end - start) / (1000 * 60);
            
            if (durationMinutes < 30) {
                highlightError(startTimeField, 'Session must be at least 30 minutes long');
                highlightError(endTimeField, 'Session must be at least 30 minutes long');
                isValid = false;
            }
        }
        
        // Location validation
        if (!location) {
            highlightError(locationField, 'Location is required');
            isValid = false;
        } else if (location.length < 3) {
            highlightError(locationField, 'Location must be at least 3 characters');
            isValid = false;
        }
        
        // Max participants validation
        if (!maxParticipants || maxParticipants < 1) {
            highlightError(maxParticipantsField, 'Maximum participants must be at least 1');
            isValid = false;
        } else if (maxParticipants > 100) {
            highlightError(maxParticipantsField, 'Maximum participants cannot exceed 100');
            isValid = false;
        }
        
        // Price validation (if provided)
        if (price && (isNaN(price) || parseFloat(price) < 0)) {
            highlightError(priceField, 'Price must be a valid positive number');
            isValid = false;
        } else if (price && parseFloat(price) > 50000) {
            highlightError(priceField, 'Price cannot exceed Rs. 50,000');
            isValid = false;
        }
    }
    
    if (!isValid) {
        // Scroll to first error
        const firstError = document.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

// Helper function to show validation errors under fields
function showValidationErrors(errors) {
    // Don't show popup - errors are already shown under fields via highlightError
    // Just log for debugging
    console.log('Validation errors:', errors);
}

// Enhanced highlightError to show message under field
function highlightError(element, message) {
    if (element) {
        element.classList.add('error');
        element.style.borderColor = '#ef4444';
        
        // Remove existing error message if any
        const existingError = element.parentElement.querySelector('.field-error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message under the field if message is provided
        if (message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error-message';
            errorDiv.style.color = '#ef4444';
            errorDiv.style.fontSize = '0.85rem';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.style.display = 'flex';
            errorDiv.style.alignItems = 'center';
            errorDiv.style.gap = '0.25rem';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            element.parentElement.appendChild(errorDiv);
        }
    }
}

// Form submission handler
function saveStepData(stepNumber) {
    if (stepNumber === 1) {
        wizardState.formData.sessionType = document.querySelector('input[name="sessionType"]:checked').value;
        wizardState.formData.sessionMode = document.querySelector('input[name="sessionMode"]:checked').value;
    } else if (stepNumber === 2) {
        wizardState.formData.name = document.getElementById('sessionName').value;
        wizardState.formData.date = document.getElementById('sessionDate').value;
        wizardState.formData.startTime = document.getElementById('startTime').value;
        wizardState.formData.endTime = document.getElementById('endTime').value;
        wizardState.formData.location = document.getElementById('location').value;
        wizardState.formData.maxParticipants = document.getElementById('maxParticipants').value;
        wizardState.formData.pricePerSession = document.getElementById('pricePerSession').value || '0.00';
        wizardState.formData.isRecurring = document.getElementById('isRecurring').checked;
    }
}

function populateSummary() {
    // Update session type
    const sessionType = wizardState.formData.sessionType;
    document.getElementById('summaryType').innerHTML = 
        `<span class="summary-badge ${sessionType.toLowerCase().replace(' ', '-')}">${sessionType}</span>`;
    
    document.getElementById('summaryMode').innerHTML = 
        `<span class="summary-badge ${wizardState.formData.sessionMode.toLowerCase()}">${wizardState.formData.sessionMode}</span>`;
    
    // Update schedule info
    const date = new Date(wizardState.formData.date);
    document.getElementById('summaryDate').textContent = date.toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    
    document.getElementById('summaryTime').textContent = 
        `${formatTime(wizardState.formData.startTime)} - ${formatTime(wizardState.formData.endTime)}`;
    
    document.getElementById('summaryLocation').textContent = wizardState.formData.location;
    
    // Update additional details
    document.getElementById('summaryMaxParticipants').textContent = wizardState.formData.maxParticipants;
    document.getElementById('summaryPrice').textContent = 
        wizardState.formData.pricePerSession === '0.00' || wizardState.formData.pricePerSession === '0' 
            ? 'Free (Group Session)' 
            : `Rs. ${wizardState.formData.pricePerSession}`;
    document.getElementById('summaryRecurring').textContent = 
        wizardState.formData.isRecurring ? 'Yes (Regular Session)' : 'No (One-time Session)';
    
    document.getElementById('summaryName').textContent = wizardState.formData.name;
}

function submitSession() {
    // Save final step data
    saveStepData(wizardState.currentStep);
    
    // Create session object matching database schema
    const sessionData = {
        SessionType: wizardState.formData.sessionType,
        SessionMode: wizardState.formData.sessionMode,
        Name: wizardState.formData.name,
        Date: wizardState.formData.date,
        StartTime: wizardState.formData.startTime,
        EndTime: wizardState.formData.endTime,
        Location: wizardState.formData.location,
        Status: 'active',
        MaxParticipants: parseInt(wizardState.formData.maxParticipants),
        PricePerSession: parseFloat(wizardState.formData.pricePerSession),
        IsRecurring: wizardState.formData.isRecurring
    };
    
    console.log('Session Data to be saved:', sessionData);
    
    // Determine if we're creating or updating
    const isUpdate = wizardState.isEditMode;
    const endpoint = isUpdate 
        ? `<?php echo URLROOT; ?>/coach/edit_session/${wizardState.editSessionId}` 
        : '<?php echo URLROOT; ?>/coach/create_session';
    const actionText = isUpdate ? 'update' : 'create';
    
    console.log(`${isUpdate ? 'Updating' : 'Creating'} session...`);
    
    // Send to backend API
    fetch(endpoint, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(sessionData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Show success message
            const successMsg = isUpdate ? '✅ Session Updated Successfully!' : '✅ Session Created Successfully!';
            showNotification(successMsg, 'success');
            
            // Close wizard
            closeWizard();
            
            // Reload sessions from database to get the latest data
            loadSessionsFromDatabase();
        } else {
            // Show error message
            showNotification('❌ Error: ' + (data.message || `Failed to ${actionText} session`), 'error');
        }
    })
    .catch(error => {
        console.error(`Error ${actionText}ing session:`, error);
        showNotification(`❌ Failed to ${actionText} session: ` + error.message, 'error');
    });
}

function selectTypeCard(type) {
    // Remove selected class from all cards
    document.querySelectorAll('.type-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    const selectedCard = document.querySelector(`.type-card.${type.toLowerCase().replace(' ', '-')}`);
    selectedCard.classList.add('selected');
    
    // Check the radio button
    document.querySelector(`input[value="${type}"]`).checked = true;
}

// Close wizard when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('sessionWizardModal');
    if (e.target === modal) {
        closeWizard();
    }
});

// Add real-time validation - clear errors when user starts typing
document.addEventListener('DOMContentLoaded', function() {
    // Get all input fields
    const formFields = [
        'sessionName',
        'sessionDate',
        'startTime',
        'endTime',
        'location',
        'maxParticipants',
        'pricePerSession'
    ];
    
    // Add event listeners to clear errors on input
    formFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                // Clear error styling
                this.classList.remove('error');
                this.style.borderColor = '';
                
                // Remove error message below field
                const errorMsg = this.parentElement.querySelector('.field-error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        }
    });
    
    // Add listeners for radio buttons
    const sessionTypeRadios = document.querySelectorAll('input[name="sessionType"]');
    const sessionModeRadios = document.querySelectorAll('input[name="sessionMode"]');
    
    sessionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.querySelector('.session-type-cards');
            if (container) {
                container.classList.remove('error');
                container.style.borderColor = '';
                
                // Remove error message
                const errorMsg = container.parentElement.querySelector('.field-error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
    
    sessionModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.querySelector('.radio-group');
            if (container) {
                container.classList.remove('error');
                container.style.borderColor = '';
                
                // Remove error message
                const errorMsg = container.parentElement.querySelector('.field-error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
});

// ================================================
// SIDEBAR TOGGLE
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<script>
function togglePlayers(sessionId) {
    const row  = document.getElementById('players-' + sessionId);
    const icon = document.getElementById('icon-' + sessionId);
    if (!row) return;
    const open = row.style.display === 'table-row';
    row.style.display  = open ? 'none' : 'table-row';
    icon.className     = open ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
}
</script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
