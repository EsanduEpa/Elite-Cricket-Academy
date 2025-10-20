<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/calendar.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    /* Session Management Specific Styles */
    .sessions-container {
        padding: 20px;
        min-height: calc(100vh - 80px);
    }

    .sessions-header {
        background: linear-gradient(135deg, rgba(74, 144, 226, 0.95) 0%, rgba(53, 122, 189, 0.9) 100%);
        color: white;
        padding: 32px;
        border-radius: 20px;
        margin-bottom: 32px;
        box-shadow: 0 10px 30px rgba(74, 144, 226, 0.3);
        position: relative;
        overflow: hidden;
    }

    .sessions-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: pulse 15s ease-in-out infinite;
    }

    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-content h1 {
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-actions {
        display: flex;
        gap: 15px;
    }

    .btn-add-session {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 24px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-add-session:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Quick Stats Cards */
    .session-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(74, 144, 226, 0.4);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: linear-gradient(45deg, #4A90E2, #5BA0F2);
        color: white;
        box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
        margin-bottom: 16px;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Calendar and Sessions Grid */
    .sessions-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    .calendar-section, .upcoming-sessions {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 16px;
        border-bottom: 2px solid rgba(74, 144, 226, 0.2);
    }

    .section-title i {
        color: #4A90E2;
    }

    /* Calendar Styling */
    .fc {
        background: white;
        border-radius: 12px;
        padding: 15px;
    }

    .fc-event {
        border: none;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
    }

    .fc-event.batting {
        background: linear-gradient(135deg, #4A90E2, #5BA0F2);
    }

    .fc-event.bowling {
        background: linear-gradient(135deg, #10b981, #14b894);
    }

    .fc-event.strategy {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    .fc-event.fitness {
        background: linear-gradient(135deg, #ef4444, #f87171);
    }

    /* Upcoming Sessions List */
    .session-item {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        border-left: 4px solid #4A90E2;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .session-item:hover {
        transform: translateX(8px);
        box-shadow: 0 8px 24px rgba(74, 144, 226, 0.2);
    }

    .session-time {
        font-size: 13px;
        font-weight: 600;
        color: #4A90E2;
        margin-bottom: 8px;
    }

    .session-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
    }

    .session-details {
        display: flex;
        gap: 16px;
        font-size: 13px;
        color: #666;
        margin-top: 8px;
    }

    .session-detail {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .session-type-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .session-type-badge.batting {
        background: rgba(74, 144, 226, 0.15);
        color: #4A90E2;
    }

    .session-type-badge.bowling {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .session-type-badge.strategy {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .session-type-badge.fitness {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease;
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        background: linear-gradient(135deg, #4A90E2, #357ABD);
        color: white;
        padding: 24px 32px;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        font-size: 24px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 32px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group label .required {
        color: #ef4444;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #4A90E2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .modal-footer {
        padding: 24px 32px;
        border-top: 2px solid #e5e7eb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4A90E2, #357ABD);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(74, 144, 226, 0.4);
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #666;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    /* Session Details Modal */
    .session-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .info-item {
        padding: 16px;
        background: #f9fafb;
        border-radius: 10px;
    }

    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .attendance-list {
        margin-top: 24px;
    }

    .player-attendance-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .player-name {
        font-weight: 600;
        color: #333;
    }

    .attendance-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .attendance-status.present {
        background: #d1fae5;
        color: #065f46;
    }

    .attendance-status.absent {
        background: #fee2e2;
        color: #991b1b;
    }

    .attendance-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    /* Action Buttons */
    .session-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
    }

    .btn-reschedule {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #ef4444, #f87171);
        color: white;
    }

    .btn-notify {
        background: linear-gradient(135deg, #10b981, #14b894);
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .sessions-content {
            grid-template-columns: 1fr;
        }

        .session-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .session-stats {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .header-content {
            flex-direction: column;
            gap: 20px;
        }
    }
</style>

<div class="coach-layout">
    <?php require_once APPROOT . '/views/inc/components/coach_sidebar.php'; ?>

    <div class="main-content">
        <div class="sessions-container">
            <!-- Header -->
            <div class="sessions-header">
                <div class="header-content">
                    <h1>
                        <i class="fas fa-calendar-alt"></i>
                        Session & Schedule Management
                    </h1>
                    <div class="header-actions">
                        <button class="btn-add-session" onclick="openAddSessionModal()">
                            <i class="fas fa-plus"></i>
                            Add Session
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('session_message'); ?>

            <!-- Quick Stats -->
            <div class="session-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value"><?php echo $data['stats']['todaySessions'] ?? 0; ?></div>
                    <div class="stat-label">Today's Sessions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?php echo $data['stats']['upcomingSessions'] ?? 0; ?></div>
                    <div class="stat-label">Upcoming Sessions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo $data['stats']['totalPlayers'] ?? 0; ?></div>
                    <div class="stat-label">Active Players</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-value"><?php echo $data['stats']['avgAttendance'] ?? 0; ?>%</div>
                    <div class="stat-label">Avg Attendance</div>
                </div>
            </div>

            <!-- Calendar and Upcoming Sessions -->
            <div class="sessions-content">
                <!-- Calendar Section -->
                <div class="calendar-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar"></i>
                        Session Calendar
                    </h3>
                    <div id="sessionCalendar"></div>
                </div>

                <!-- Upcoming Sessions -->
                <div class="upcoming-sessions">
                    <h3 class="section-title">
                        <i class="fas fa-list"></i>
                        Upcoming Sessions
                    </h3>
                    <div id="upcomingSessionsList">
                        <?php if (!empty($data['upcomingSessions'])): ?>
                            <?php foreach ($data['upcomingSessions'] as $session): ?>
                                <div class="session-item" onclick="viewSessionDetails(<?php echo $session['SessionID']; ?>)">
                                    <div class="session-time">
                                        <i class="far fa-clock"></i>
                                        <?php echo date('M d, Y - h:i A', strtotime($session['StartTime'])); ?>
                                    </div>
                                    <div class="session-title"><?php echo htmlspecialchars($session['Title'] ?? 'Training Session', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="session-type-badge <?php echo strtolower($session['SessionType'] ?? 'batting'); ?>">
                                        <?php echo htmlspecialchars($session['SessionType'] ?? 'Batting', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <div class="session-details">
                                        <div class="session-detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($session['Facility'] ?? 'Main Ground', ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <div class="session-detail">
                                            <i class="fas fa-users"></i>
                                            <?php echo $session['ParticipantCount'] ?? 0; ?>/<?php echo $session['MaxParticipants'] ?? 20; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                                <p>No upcoming sessions scheduled</p>
                                <button class="btn btn-primary" onclick="openAddSessionModal()" style="margin-top: 16px;">
                                    <i class="fas fa-plus"></i> Schedule First Session
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Session Modal -->
<div id="addSessionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Session</h2>
            <button class="modal-close" onclick="closeAddSessionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addSessionForm" action="<?php echo URLROOT; ?>/coach/create_session" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label>Session Title <span class="required">*</span></label>
                    <input type="text" name="title" placeholder="e.g., Advanced Batting Practice" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Session Type <span class="required">*</span></label>
                        <select name="session_type" required>
                            <option value="">Select Type</option>
                            <option value="Batting">Batting</option>
                            <option value="Bowling">Bowling</option>
                            <option value="Strategy">Strategy</option>
                            <option value="Fitness">Fitness</option>
                            <option value="Fielding">Fielding</option>
                            <option value="Match">Match Practice</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Facility <span class="required">*</span></label>
                        <select name="facility" required>
                            <option value="">Select Facility</option>
                            <option value="Main Ground">Main Ground</option>
                            <option value="Practice Nets">Practice Nets</option>
                            <option value="Indoor Arena">Indoor Arena</option>
                            <option value="Fitness Center">Fitness Center</option>
                            <option value="Strategy Room">Strategy Room</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <input type="date" name="session_date" required>
                    </div>

                    <div class="form-group">
                        <label>Start Time <span class="required">*</span></label>
                        <input type="time" name="start_time" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>End Time <span class="required">*</span></label>
                        <input type="time" name="end_time" required>
                    </div>

                    <div class="form-group">
                        <label>Max Participants <span class="required">*</span></label>
                        <input type="number" name="max_participants" min="1" max="50" value="20" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Add session details, focus areas, or special instructions..." rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="notify_players" value="1" checked>
                        Automatically notify enrolled players
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddSessionModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Create Session
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Session Details Modal -->
<div id="sessionDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-info-circle"></i> Session Details</h2>
            <button class="modal-close" onclick="closeSessionDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="sessionDetailsContent">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
// Global variables
let calendar;
const sessions = <?php echo json_encode($data['allSessions'] ?? []); ?>;

// Initialize calendar when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    setupFormValidation();
});

// Initialize FullCalendar
function initializeCalendar() {
    const calendarEl = document.getElementById('sessionCalendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: sessions.map(session => ({
            id: session.SessionID,
            title: session.Title || session.SessionType,
            start: session.StartTime,
            end: session.EndTime,
            backgroundColor: getSessionColor(session.SessionType),
            borderColor: getSessionColor(session.SessionType),
            extendedProps: {
                sessionType: session.SessionType,
                facility: session.Facility,
                maxParticipants: session.MaxParticipants,
                participantCount: session.ParticipantCount || 0
            }
        })),
        eventClick: function(info) {
            viewSessionDetails(info.event.id);
        },
        dateClick: function(info) {
            openAddSessionModal(info.dateStr);
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.title = `${info.event.title}\n${info.event.extendedProps.facility}\nParticipants: ${info.event.extendedProps.participantCount}/${info.event.extendedProps.maxParticipants}`;
        }
    });
    
    calendar.render();
}

// Get session color based on type
function getSessionColor(type) {
    const colors = {
        'Batting': '#4A90E2',
        'Bowling': '#10b981',
        'Strategy': '#f59e0b',
        'Fitness': '#ef4444',
        'Fielding': '#8b5cf6',
        'Match': '#06b6d4'
    };
    return colors[type] || '#4A90E2';
}

// Modal Functions
function openAddSessionModal(date = null) {
    const modal = document.getElementById('addSessionModal');
    modal.classList.add('active');
    
    // Pre-fill date if provided
    if (date) {
        document.querySelector('input[name="session_date"]').value = date;
    } else {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="session_date"]').min = today;
    }
}

function closeAddSessionModal() {
    const modal = document.getElementById('addSessionModal');
    modal.classList.remove('active');
    document.getElementById('addSessionForm').reset();
}

function viewSessionDetails(sessionId) {
    const modal = document.getElementById('sessionDetailsModal');
    const content = document.getElementById('sessionDetailsContent');
    
    // Show loading
    content.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #4A90E2;"></i></div>';
    modal.classList.add('active');
    
    // Fetch session details
    fetch(`<?php echo URLROOT; ?>/coach/get_session_details/${sessionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySessionDetails(data.session);
            } else {
                content.innerHTML = '<div style="text-align: center; padding: 40px; color: #ef4444;">Error loading session details</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div style="text-align: center; padding: 40px; color: #ef4444;">Error loading session details</div>';
        });
}

function displaySessionDetails(session) {
    const content = document.getElementById('sessionDetailsContent');
    
    const html = `
        <div class="session-info-grid">
            <div class="info-item">
                <div class="info-label">Session Type</div>
                <div class="info-value">
                    <span class="session-type-badge ${session.SessionType.toLowerCase()}">${session.SessionType}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Facility</div>
                <div class="info-value">${session.Facility}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Date & Time</div>
                <div class="info-value">${new Date(session.StartTime).toLocaleString()}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Duration</div>
                <div class="info-value">${calculateDuration(session.StartTime, session.EndTime)}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Participants</div>
                <div class="info-value">${session.ParticipantCount || 0}/${session.MaxParticipants}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">${session.Status || 'Scheduled'}</div>
            </div>
        </div>
        
        ${session.Description ? `
        <div class="info-item" style="margin-top: 20px;">
            <div class="info-label">Description</div>
            <div class="info-value">${session.Description}</div>
        </div>
        ` : ''}
        
        <div class="attendance-list">
            <h4 style="margin-bottom: 16px;">Participant List</h4>
            ${session.participants && session.participants.length > 0 ? 
                session.participants.map(p => `
                    <div class="player-attendance-item">
                        <span class="player-name">${p.Name}</span>
                        <span class="attendance-status ${(p.AttendanceStatus || 'pending').toLowerCase()}">
                            ${p.AttendanceStatus || 'Pending'}
                        </span>
                    </div>
                `).join('') : 
                '<p style="text-align: center; color: #999;">No participants enrolled yet</p>'
            }
        </div>
        
        <div class="session-actions">
            <button class="btn btn-reschedule" onclick="rescheduleSession(${session.SessionID})">
                <i class="fas fa-calendar-alt"></i> Reschedule
            </button>
            <button class="btn btn-notify" onclick="notifyParticipants(${session.SessionID})">
                <i class="fas fa-bell"></i> Notify Participants
            </button>
            <button class="btn btn-cancel" onclick="cancelSession(${session.SessionID})">
                <i class="fas fa-times-circle"></i> Cancel Session
            </button>
        </div>
    `;
    
    content.innerHTML = html;
}

function closeSessionDetailsModal() {
    const modal = document.getElementById('sessionDetailsModal');
    modal.classList.remove('active');
}

// Helper Functions
function calculateDuration(start, end) {
    const startTime = new Date(start);
    const endTime = new Date(end);
    const diff = endTime - startTime;
    const hours = Math.floor(diff / 3600000);
    const minutes = Math.floor((diff % 3600000) / 60000);
    return `${hours}h ${minutes}m`;
}

function rescheduleSession(sessionId) {
    if (confirm('Are you sure you want to reschedule this session? Participants will be notified.')) {
        // Implement reschedule logic
        window.location.href = `<?php echo URLROOT; ?>/coach/reschedule_session/${sessionId}`;
    }
}

function notifyParticipants(sessionId) {
    if (confirm('Send notification to all enrolled participants?')) {
        fetch(`<?php echo URLROOT; ?>/coach/notify_participants/${sessionId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Notifications sent successfully!');
            } else {
                alert('❌ Failed to send notifications');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error sending notifications');
        });
    }
}

function cancelSession(sessionId) {
    if (confirm('⚠️ Are you sure you want to cancel this session? This action cannot be undone. All participants will be notified.')) {
        window.location.href = `<?php echo URLROOT; ?>/coach/cancel_session/${sessionId}`;
    }
}

function setupFormValidation() {
    const form = document.getElementById('addSessionForm');
    form.addEventListener('submit', function(e) {
        const startTime = document.querySelector('input[name="start_time"]').value;
        const endTime = document.querySelector('input[name="end_time"]').value;
        
        if (startTime >= endTime) {
            e.preventDefault();
            alert('⚠️ End time must be after start time!');
            return false;
        }
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
