<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/calendar.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach_calendar.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

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
                                <?php
                                    $sessionMode = strtolower($session['SessionMode'] ?? $session['session_mode'] ?? 'group');
                                    $maxParticipants = (int) ($session['MaxParticipants'] ?? $session['max_participants'] ?? 0);
                                ?>
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
                                            <?php if ($sessionMode === 'group' || $sessionMode === 'normal'): ?>
                                                Auto-assigned group session
                                            <?php else: ?>
                                                Single facility booking
                                            <?php endif; ?>
                                        </div>
                                        <div class="session-detail">
                                            <i class="fas fa-layer-group"></i>
                                            Capacity: <?php echo $maxParticipants > 0 ? $maxParticipants : 20; ?> players
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

function getSessionMode(session) {
    return String(session?.SessionMode || session?.session_mode || 'Group').toLowerCase();
}

function isGroupSession(session) {
    const sessionMode = getSessionMode(session);
    return sessionMode === 'group' || sessionMode === 'normal';
}

function getCapacityText(maxParticipants) {
    const capacity = Number.parseInt(maxParticipants, 10);
    return capacity > 0 ? `Up to ${capacity} players` : 'Capacity not set';
}

function getAllocationText(session) {
    return isGroupSession(session)
        ? 'Auto-assigned to the relevant age group'
        : 'Single facility booking';
}

function getParticipantHeading(session) {
    return isGroupSession(session) ? 'Assigned Players' : 'Players';
}

function getEmptyParticipantText(session) {
    return isGroupSession(session)
        ? 'No players assigned to this session yet'
        : 'No players linked to this booking yet';
}

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
                sessionMode: session.SessionMode,
                facility: session.Facility,
                maxParticipants: session.MaxParticipants,
                participantCount: session.ParticipantCount || 0,
                allocationText: getAllocationText(session)
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
            info.el.title = `${info.event.title}\n${info.event.extendedProps.facility}\n${info.event.extendedProps.allocationText}\n${getCapacityText(info.event.extendedProps.maxParticipants)}`;
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
    const participantHeading = getParticipantHeading(session);
    const emptyParticipantText = getEmptyParticipantText(session);
    
    const html = `
        <div class="session-info-grid">
            <div class="info-item">
                <div class="info-label">Session Type</div>
                <div class="info-value">
                    <span class="session-type-badge ${session.SessionType.toLowerCase()}">${session.SessionType}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Session Mode</div>
                <div class="info-value">${isGroupSession(session) ? 'Group Session' : 'Private Session'}</div>
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
                <div class="info-label">Allocation</div>
                <div class="info-value">${getAllocationText(session)}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Capacity</div>
                <div class="info-value">${getCapacityText(session.MaxParticipants)}</div>
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
            <h4 style="margin-bottom: 16px;">${participantHeading}</h4>
            ${session.participants && session.participants.length > 0 ? 
                session.participants.map(p => `
                    <div class="player-attendance-item">
                        <span class="player-name">${p.Name || (p.FirstName + ' ' + p.LastName)}</span>
                        <span class="attendance-status ${(p.AttendanceStatus || 'pending').toLowerCase()}">
                            ${p.AttendanceStatus || 'Pending'}
                        </span>
                    </div>
                `).join('') : 
                `<p style="text-align: center; color: #999;">${emptyParticipantText}</p>`
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
