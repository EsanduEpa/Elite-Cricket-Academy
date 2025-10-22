<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/create-event-wizard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/events.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/tournaments.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<div class="coach-layout">
    <!-- Left Sidebar Panel -->
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Coach Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
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
                
                <li class="nav-item">
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
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                        <i class="fas fa-calendar"></i>
                        <span>Events</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Coach Profile -->
        <div class="admin-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <span class="admin-name">Coach</span>
                <span class="admin-role">Head Coach</span>
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
                    <p>View cricket academy events, tournaments, and training sessions</p>
                </div>
                <!-- Coach role: No create buttons -->
            </div>
        </div>

        <!-- Event Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon tournament">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Tournaments</h3>
                    <div class="stat-number">3</div>
                    <p class="stat-description">Currently running</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon training">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="stat-content">
                    <h3>Training Sessions</h3>
                    <div class="stat-number">12</div>
                    <p class="stat-description">This week</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon participants">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Participants</h3>
                    <div class="stat-number">48</div>
                    <p class="stat-description">Registered</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon upcoming">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="stat-content">
                    <h3>Upcoming Events</h3>
                    <div class="stat-number">8</div>
                    <p class="stat-description">Next 30 days</p>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-section">
            <div class="calendar-nav">
                <div class="nav-controls">
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
                    <?php 
                    // Sample data for coach view
                    $upcomingEvents = [
                        [
                            'id' => 1,
                            'title' => 'Junior Cricket Championship',
                            'event_date' => '2025-09-15',
                            'event_type' => 'tournament',
                            'description' => 'Annual junior cricket championship for under-16 players',
                            'location' => 'Main Cricket Ground'
                        ],
                        [
                            'id' => 2,
                            'title' => 'Advanced Batting Workshop',
                            'event_date' => '2025-09-12',
                            'event_type' => 'training',
                            'description' => 'Specialized batting techniques workshop by senior coach',
                            'location' => 'Practice Nets Area'
                        ],
                        [
                            'id' => 3,
                            'title' => 'Inter-Academy Friendly Match',
                            'event_date' => '2025-09-18',
                            'event_type' => 'match',
                            'description' => 'Friendly match against City Sports Academy',
                            'location' => 'Stadium Ground'
                        ]
                    ];
                    
                    foreach ($upcomingEvents as $event): ?>
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
                            <!-- Coach role: Only view action -->
                            <button class="btn-action view" onclick="viewEvent(<?php echo $event['id']; ?>)">
                                <i class="fas fa-eye"></i>
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
                    <?php 
                    $pastEvents = [
                        [
                            'id' => 4,
                            'title' => 'Summer Cricket Camp',
                            'event_date' => '2025-08-15',
                            'event_type' => 'training',
                            'description' => 'Intensive 5-day cricket training camp',
                            'location' => 'Academy Grounds'
                        ],
                        [
                            'id' => 5,
                            'title' => 'Regional Tournament Finals',
                            'event_date' => '2025-08-22',
                            'event_type' => 'tournament',
                            'description' => 'Regional championship final match',
                            'location' => 'City Stadium'
                        ]
                    ];
                    
                    foreach ($pastEvents as $event): ?>
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
                            <!-- Coach role: Only view action -->
                            <button class="btn-action view" onclick="viewEvent(<?php echo $event['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<!-- Common Sidebar JS -->
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<!-- Common Tournaments JS -->
<script src="<?php echo URLROOT; ?>/js/common/tournaments.js"></script>
<!-- Coach Events JS -->
<script src="<?php echo URLROOT; ?>/js/coach/events.js"></script>

<script>
// Initialize FullCalendar for Coach view
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('eventCalendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                {
                    title: 'Junior Championship',
                    start: '2025-09-15',
                    className: 'tournament-event'
                },
                {
                    title: 'Batting Workshop',
                    start: '2025-09-12',
                    className: 'training-event'
                },
                {
                    title: 'Friendly Match',
                    start: '2025-09-18',
                    className: 'match-event'
                }
            ],
            eventClick: function(info) {
                viewEvent(info.event.id);
            }
        });
        calendar.render();
    }
});

// View event function for coach
function viewEvent(eventId) {
    alert('View Event Details: ' + eventId);
    // Implement view event details modal
}
</script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>