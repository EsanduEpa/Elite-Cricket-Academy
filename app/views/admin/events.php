<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Tournaments - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Header -->
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<div class="admin-layout">
    <!-- Left Sidebar Panel -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-user-shield"></i>
                <h3>Admin Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard Overview</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#staff-management" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#player-management" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Player Management</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events & Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#feedback-monitoring" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Feedback Monitoring</span>
                        <span class="badge">12</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#finance-management" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Finance Management</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Admin Profile -->
        <div class="admin-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <span class="admin-name">Admin User</span>
                <span class="admin-role">Super Administrator</span>
            </div>
            <div class="logout-btn">
                <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Events Header -->
        <div class="events-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-alt"></i> Events & Tournaments</h1>
                    <p>Manage cricket academy events, tournaments, and training sessions</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" id="createEventBtn">
                        <i class="fas fa-plus"></i> Create New Event
                    </button>
                    <button class="btn btn-secondary" id="createTournamentBtn">
                        <i class="fas fa-trophy"></i> Create Tournament
                    </button>
                </div>
            </div>
        </div>

        <!-- Event Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon tournament">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['eventStats']['totalEvents']; ?></div>
                    <div class="stat-label">Total Events</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon upcoming">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['eventStats']['upcomingCount']; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon training">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['eventStats']['trainingSessions']; ?></div>
                    <div class="stat-label">Training Sessions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon matches">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['eventStats']['tournaments']; ?></div>
                    <div class="stat-label">Tournaments</div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar"></i> Event Calendar</h2>
                <div class="calendar-controls">
                    <button class="btn-icon" id="prevMonth">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="current-month" id="currentMonth">September 2025</span>
                    <button class="btn-icon" id="nextMonth">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="calendar-container">
                <div id="eventCalendar"></div>
            </div>
        </div>

        <!-- Events Sections -->
        <div class="events-sections">
            <!-- Upcoming Events -->
            <div class="events-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-plus"></i> Upcoming Events</h3>
                    <button class="btn btn-outline" id="viewAllUpcomingBtn">View All</button>
                </div>
                <div class="events-list">
                    <?php foreach ($data['upcomingEvents'] as $event): ?>
                    <div class="event-item" data-event-id="<?php echo $event['id']; ?>">
                        <div class="event-date">
                            <div class="date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                            <div class="date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                        </div>
                        <div class="event-details">
                            <h4 class="event-title"><?php echo $event['title']; ?></h4>
                            <p class="event-description"><?php echo $event['description']; ?></p>
                            <div class="event-meta">
                                <span class="event-type <?php echo $event['event_type']; ?>">
                                    <i class="fas fa-<?php echo $event['event_type'] == 'tournament' ? 'trophy' : ($event['event_type'] == 'training' ? 'dumbbell' : 'users'); ?>"></i>
                                    <?php echo ucfirst($event['event_type']); ?>
                                </span>
                                <span class="event-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $event['location']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="event-actions">
                            <button class="btn-action edit" onclick="editEvent(<?php echo $event['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action delete" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Past Events -->
            <div class="events-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Past Events</h3>
                    <button class="btn btn-outline" id="viewAllPastBtn">View All</button>
                </div>
                <div class="events-list">
                    <?php foreach ($data['pastEvents'] as $event): ?>
                    <div class="event-item past" data-event-id="<?php echo $event['id']; ?>">
                        <div class="event-date">
                            <div class="date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                            <div class="date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                        </div>
                        <div class="event-details">
                            <h4 class="event-title"><?php echo $event['title']; ?></h4>
                            <p class="event-description"><?php echo $event['description']; ?></p>
                            <div class="event-meta">
                                <span class="event-type <?php echo $event['event_type']; ?>">
                                    <i class="fas fa-<?php echo $event['event_type'] == 'tournament' ? 'trophy' : ($event['event_type'] == 'training' ? 'dumbbell' : 'users'); ?>"></i>
                                    <?php echo ucfirst($event['event_type']); ?>
                                </span>
                                <span class="event-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $event['location']; ?>
                                </span>
                                <span class="event-status completed">
                                    <i class="fas fa-check-circle"></i>
                                    Completed
                                </span>
                            </div>
                        </div>
                        <div class="event-actions">
                            <button class="btn-action view" onclick="viewEvent(<?php echo $event['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-action delete" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Event Modals -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Create New Event</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="eventForm" method="POST">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="eventTitle">Event Title</label>
                        <input type="text" id="eventTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="eventType">Event Type</label>
                        <select id="eventType" name="event_type" required>
                            <option value="">Select Type</option>
                            <option value="tournament">Tournament</option>
                            <option value="training">Training Session</option>
                            <option value="match">Match</option>
                            <option value="workshop">Workshop</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="eventDate">Event Date</label>
                        <input type="date" id="eventDate" name="event_date" required>
                    </div>
                    <div class="form-group">
                        <label for="eventLocation">Location</label>
                        <input type="text" id="eventLocation" name="location" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="eventDescription">Description</label>
                    <textarea id="eventDescription" name="description" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Event</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize calendar
    const calendarEl = document.getElementById('eventCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '<?php echo URLROOT; ?>/admin/get_calendar_events',
        eventClick: function(info) {
            viewEventDetails(info.event);
        },
        dateClick: function(info) {
            openCreateModal(info.dateStr);
        },
        eventClassNames: function(arg) {
            return ['event-' + arg.event.extendedProps.type];
        }
    });
    calendar.render();

    // Event handlers
    document.getElementById('createEventBtn').addEventListener('click', () => openCreateModal());
    document.getElementById('createTournamentBtn').addEventListener('click', () => openCreateModal('tournament'));
    document.getElementById('viewAllUpcomingBtn').addEventListener('click', () => viewAllEvents('upcoming'));
    document.getElementById('viewAllPastBtn').addEventListener('click', () => viewAllEvents('past'));
});

function openCreateModal(type = '', date = '') {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    const title = document.getElementById('modalTitle');
    
    title.textContent = 'Create New Event';
    form.action = '<?php echo URLROOT; ?>/admin/create_event';
    form.reset();
    
    if (type === 'tournament') {
        document.getElementById('eventType').value = 'tournament';
    }
    
    if (date) {
        document.getElementById('eventDate').value = date;
    }
    
    modal.style.display = 'block';
}

function editEvent(eventId) {
    // Fetch event details and populate form
    fetch(`<?php echo URLROOT; ?>/admin/get_event/${eventId}`)
        .then(response => response.json())
        .then(event => {
            const modal = document.getElementById('eventModal');
            const form = document.getElementById('eventForm');
            const title = document.getElementById('modalTitle');
            
            title.textContent = 'Edit Event';
            form.action = `<?php echo URLROOT; ?>/admin/edit_event/${eventId}`;
            
            document.getElementById('eventTitle').value = event.title;
            document.getElementById('eventType').value = event.event_type;
            document.getElementById('eventDate').value = event.event_date;
            document.getElementById('eventLocation').value = event.location;
            document.getElementById('eventDescription').value = event.description;
            
            modal.style.display = 'block';
        });
}

function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        window.location.href = `<?php echo URLROOT; ?>/admin/delete_event/${eventId}`;
    }
}

function viewEvent(eventId) {
    window.location.href = `<?php echo URLROOT; ?>/admin/event_details/${eventId}`;
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}

function viewAllEvents(type) {
    window.location.href = `<?php echo URLROOT; ?>/admin/events?filter=${type}`;
}

function viewEventDetails(event) {
    alert(`Event: ${event.title}\nDate: ${event.start}\nType: ${event.extendedProps.type}\nLocation: ${event.extendedProps.location}\nDescription: ${event.extendedProps.description}`);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('eventModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<!-- Admin Events JS -->
<script src="<?php echo URLROOT; ?>/js/admin/events.js"></script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>

</body>
</html>
