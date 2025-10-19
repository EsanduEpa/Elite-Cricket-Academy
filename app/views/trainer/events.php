<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/create-event-wizard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/events.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/tournaments.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-dumbbell"></i>
                <h3>Trainer Dashboard</h3>
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
                    <a href="<?php echo URLROOT; ?>/trainer/schedules" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Training Schedules</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Session Bookings</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/events" class="nav-link">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Events & Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                        <i class="fas fa-apple-whole"></i>
                        <span>Nutrition Plans</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>workouts</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/medical" class="nav-link">
                        <i class="fas fa-heart-pulse"></i>
                        <span>Medical Records</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Trainer Profile -->
        <div class="admin-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <span class="admin-name">Trainer</span>
                <span class="admin-role">Fitness Trainer</span>
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
                <!-- Trainer role: No create buttons -->
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
                    // Sample data for trainer view
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
                            'title' => 'Fitness Training Workshop',
                            'event_date' => '2025-09-12',
                            'event_type' => 'training',
                            'description' => 'Specialized fitness and conditioning workshop',
                            'location' => 'Fitness Center'
                        ],
                        [
                            'id' => 3,
                            'title' => 'Strength & Conditioning Session',
                            'event_date' => '2025-09-14',
                            'event_type' => 'training',
                            'description' => 'Focus on building core strength and endurance',
                            'location' => 'Gym Area'
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
                            <!-- Trainer role: Only view action -->
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
                            'title' => 'Summer Fitness Boot Camp',
                            'event_date' => '2025-08-15',
                            'event_type' => 'training',
                            'description' => 'Intensive 3-day fitness boot camp for players',
                            'location' => 'Training Ground'
                        ],
                        [
                            'id' => 5,
                            'title' => 'Pre-Tournament Conditioning',
                            'event_date' => '2025-08-20',
                            'event_type' => 'training',
                            'description' => 'Special conditioning session before regional tournament',
                            'location' => 'Fitness Center'
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
                            <!-- Trainer role: Only view action -->
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
<!-- Trainer Dashboard JS -->
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>

<script>
// Initialize FullCalendar for Trainer view
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
                    title: 'Fitness Workshop',
                    start: '2025-09-12',
                    className: 'training-event'
                },
                {
                    title: 'Conditioning Session',
                    start: '2025-09-14',
                    className: 'training-event'
                }
            ],
            eventClick: function(info) {
                viewEvent(info.event.id);
            }
        });
        calendar.render();
    }
});

// View event function for trainer
function viewEvent(eventId) {
    alert('View Event Details: ' + eventId);
    // Implement view event details modal
}
</script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>