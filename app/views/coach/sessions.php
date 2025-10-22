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
                <h2 class="calendar-title" id="calendarTitle">October 2025</h2>
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
                Upcoming Sessions
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
                    <!-- Table rows will be generated by JavaScript -->
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
    currentDate: new Date(2025, 9, 21), // October 21, 2025
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
    
    fetch(`<?php echo URLROOT; ?>/coach/get_sessions_list`)
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
    
    // Sort sessions by date and time
    const sortedSessions = [...calendarState.sessions].sort((a, b) => {
        return new Date(a.date + ' ' + a.startTime) - new Date(b.date + ' ' + b.startTime);
    });
    
    // Filter only upcoming sessions
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const upcomingSessions = sortedSessions.filter(session => {
        const sessionDate = new Date(session.date);
        return sessionDate >= today;
    });
    
    if (upcomingSessions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 16px;"></i>
                    <p style="color: #999; margin: 0;">No upcoming sessions. All sessions are completed!</p>
                </td>
            </tr>
        `;
        return;
    }
    
    upcomingSessions.forEach(session => {
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
                <button class="action-btn action-btn-delete" onclick="deleteSession(${session.id}, event)">
                    <i class="fas fa-trash-alt"></i>
                    Delete
                </button>
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
    calendarState.currentDate = new Date(2025, 9, 21);
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

function openAddSessionModal(date = null) {
    alert(`Add Session Modal\n${date ? 'Pre-selected date: ' + date : 'No date selected'}\n\nModal implementation coming next!`);
}

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
    
    // Filter logic will be implemented when connected to database
    alert(`Filters Applied:\nType: ${type || 'All'}\nMode: ${mode || 'All'}\nStatus: ${status || 'All'}\nSearch: ${search || 'None'}`);
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
            errors.push('⚠️ Please select a session type');
            highlightError(document.querySelector('.session-type-options'));
            isValid = false;
        }
        if (!sessionMode) {
            errors.push('⚠️ Please select a session mode');
            highlightError(document.querySelector('.session-mode-options'));
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
            errors.push('⚠️ Session name is required');
            highlightError(nameField);
            isValid = false;
        } else if (name.length < 3) {
            errors.push('⚠️ Session name must be at least 3 characters');
            highlightError(nameField);
            isValid = false;
        } else if (name.length > 100) {
            errors.push('⚠️ Session name must not exceed 100 characters');
            highlightError(nameField);
            isValid = false;
        }
        
        // Date validation
        if (!date) {
            errors.push('⚠️ Session date is required');
            highlightError(dateField);
            isValid = false;
        } else {
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                errors.push('⚠️ Session date cannot be in the past');
                highlightError(dateField);
                isValid = false;
            }
        }
        
        // Time validation
        if (!startTime) {
            errors.push('⚠️ Start time is required');
            highlightError(startTimeField);
            isValid = false;
        }
        
        if (!endTime) {
            errors.push('⚠️ End time is required');
            highlightError(endTimeField);
            isValid = false;
        }
        
        if (startTime && endTime && startTime >= endTime) {
            errors.push('⚠️ End time must be after start time');
            highlightError(startTimeField);
            highlightError(endTimeField);
            isValid = false;
        }
        
        // Check if session duration is reasonable (at least 30 minutes)
        if (startTime && endTime && startTime < endTime) {
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + endTime);
            const durationMinutes = (end - start) / (1000 * 60);
            
            if (durationMinutes < 30) {
                errors.push('⚠️ Session must be at least 30 minutes long');
                highlightError(startTimeField);
                highlightError(endTimeField);
                isValid = false;
            }
        }
        
        // Location validation
        if (!location) {
            errors.push('⚠️ Location is required');
            highlightError(locationField);
            isValid = false;
        } else if (location.length < 3) {
            errors.push('⚠️ Location must be at least 3 characters');
            highlightError(locationField);
            isValid = false;
        }
        
        // Max participants validation
        if (!maxParticipants || maxParticipants < 1) {
            errors.push('⚠️ Maximum participants must be at least 1');
            highlightError(maxParticipantsField);
            isValid = false;
        } else if (maxParticipants > 100) {
            errors.push('⚠️ Maximum participants cannot exceed 100');
            highlightError(maxParticipantsField);
            isValid = false;
        }
        
        // Price validation (if provided)
        if (price && (isNaN(price) || parseFloat(price) < 0)) {
            errors.push('⚠️ Price must be a valid positive number');
            highlightError(priceField);
            isValid = false;
        } else if (price && parseFloat(price) > 50000) {
            errors.push('⚠️ Price cannot exceed Rs. 50,000');
            highlightError(priceField);
            isValid = false;
        }
    }
    
    if (!isValid) {
        showValidationErrors(errors);
        // Scroll to first error
        const firstError = document.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

// Helper function to highlight error fields
function highlightError(element) {
    if (element) {
        element.classList.add('error');
    }
}

// Helper function to show validation errors
function showValidationErrors(errors) {
    const errorHtml = errors.map(err => `<div style="margin: 5px 0;">${err}</div>`).join('');
    
    const errorModal = document.createElement('div');
    errorModal.className = 'validation-error-popup';
    errorModal.innerHTML = `
        <div class="error-popup-content">
            <div class="error-popup-header">
                <i class="fas fa-exclamation-circle"></i>
                <span>Please fix the following errors:</span>
            </div>
            <div class="error-popup-body">
                ${errorHtml}
            </div>
            <button class="error-popup-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    `;
    
    // Add styling
    errorModal.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10000;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(errorModal);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorModal.parentElement) {
            errorModal.remove();
        }
    }, 5000);
}

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
                this.classList.remove('error');
                this.classList.add('success');
            });
            
            field.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('error');
                    this.classList.add('success');
                } else {
                    this.classList.remove('success');
                }
            });
        }
    });
    
    // Add listeners for radio buttons
    const sessionTypeRadios = document.querySelectorAll('input[name="sessionType"]');
    const sessionModeRadios = document.querySelectorAll('input[name="sessionMode"]');
    
    sessionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.querySelector('.session-type-options');
            if (container) {
                container.classList.remove('error');
            }
        });
    });
    
    sessionModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.querySelector('.session-mode-options');
            if (container) {
                container.classList.remove('error');
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

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
