<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Tournaments - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/create-event-wizard.css">
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

<!-- Event Creation Wizard Modal -->
<div id="createEventModal" class="create-event-modal">
    <div class="wizard-container">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-calendar-plus"></i>
                Create New Event
            </h2>
            <button class="modal-close" onclick="closeCreateEventModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-line" id="progressLine"></div>
                </div>
                <div class="steps-indicator">
                    <div class="step-indicator">
                        <div class="step-circle active" data-step="1">1</div>
                        <div class="step-label active">Basic Info</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="2">2</div>
                        <div class="step-label">Schedule</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="3">3</div>
                        <div class="step-label">Management</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="4">4</div>
                        <div class="step-label">Confirmation</div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form id="eventWizardForm" action="<?php echo URLROOT; ?>/admin/createEvent" method="POST">
                <div class="wizard-content">
                    <!-- Step 1: Basic Details -->
                    <div class="step-content active" data-step="1">
                        <h2 class="step-title">
                            <i class="fas fa-info-circle step-icon"></i>
                            Event Basic Information
                        </h2>
                        <p class="step-description">Let's start with the fundamental details of your cricket event</p>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="eventName">Event Name <span class="required">*</span></label>
                                <input type="text" id="eventName" name="event_name" class="form-control" 
                                       placeholder="e.g., Annual Cricket Championship" required>
                                <div class="error-message" id="eventNameError">Please enter an event name</div>
                            </div>

                            <div class="form-group">
                                <label for="eventType">Event Type <span class="required">*</span></label>
                                <select id="eventType" name="event_type" class="form-control" required>
                                    <option value="">Select Event Type</option>
                                    <option value="tournament">Tournament</option>
                                    <option value="training_camp">Training Camp</option>
                                    <option value="match">Match/Game</option>
                                    <option value="workshop">Workshop/Clinic</option>
                                    <option value="trial">Trial/Selection</option>
                                    <option value="meeting">Team Meeting</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="error-message" id="eventTypeError">Please select an event type</div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="eventCategory">Category <span class="required">*</span></label>
                                <select id="eventCategory" name="event_category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="junior">Junior (Under 18)</option>
                                    <option value="senior">Senior (18+)</option>
                                    <option value="youth">Youth Development</option>
                                    <option value="professional">Professional</option>
                                    <option value="recreational">Recreational</option>
                                    <option value="academy">Academy Training</option>
                                </select>
                                <div class="error-message" id="eventCategoryError">Please select a category</div>
                            </div>

                            <div class="form-group">
                                <label for="eventVenue">Venue <span class="required">*</span></label>
                                <input type="text" id="eventVenue" name="event_venue" class="form-control" 
                                       placeholder="Enter venue location" required>
                                <div class="error-message" id="eventVenueError">Please enter the venue</div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="maxParticipants">Max Participants</label>
                                <input type="number" id="maxParticipants" name="max_participants" class="form-control" 
                                       placeholder="Leave empty for unlimited" min="1">
                                <div class="error-message" id="maxParticipantsError">Please enter a valid number</div>
                            </div>

                            <div class="form-group">
                                <label for="registrationFee">Registration Fee (LKR)</label>
                                <input type="number" id="registrationFee" name="registration_fee" class="form-control" 
                                       placeholder="0 for free events" min="0" step="0.01">
                                <div class="error-message" id="registrationFeeError">Please enter a valid amount</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventDescription">Event Description</label>
                            <textarea id="eventDescription" name="event_description" class="form-control" 
                                    placeholder="Provide a detailed description of the event, including objectives, activities, and any special information..." 
                                    rows="4"></textarea>
                        </div>
                    </div>

                    <!-- Step 2: Date & Time -->
                    <div class="step-content" data-step="2">
                        <h2 class="step-title">
                            <i class="fas fa-calendar-alt step-icon"></i>
                            Event Scheduling
                        </h2>
                        <p class="step-description">Set the date, time, and duration for your cricket event</p>

                        <div class="datetime-grid">
                            <div class="date-time-group">
                                <h3><i class="fas fa-play-circle"></i> Event Start</h3>
                                <div class="form-group">
                                    <label for="startDate">Start Date <span class="required">*</span></label>
                                    <input type="date" id="startDate" name="start_date" class="form-control" required>
                                    <div class="error-message" id="startDateError">Please select a start date</div>
                                </div>
                                <div class="form-group">
                                    <label for="startTime">Start Time <span class="required">*</span></label>
                                    <input type="time" id="startTime" name="start_time" class="form-control" required>
                                    <div class="error-message" id="startTimeError">Please select a start time</div>
                                </div>
                            </div>

                            <div class="date-time-group">
                                <h3><i class="fas fa-stop-circle"></i> Event End</h3>
                                <div class="form-group">
                                    <label for="endDate">End Date <span class="required">*</span></label>
                                    <input type="date" id="endDate" name="end_date" class="form-control" required>
                                    <div class="error-message" id="endDateError">Please select an end date</div>
                                </div>
                                <div class="form-group">
                                    <label for="endTime">End Time <span class="required">*</span></label>
                                    <input type="time" id="endTime" name="end_time" class="form-control" required>
                                    <div class="error-message" id="endTimeError">Please select an end time</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="registrationStart">Registration Opens</label>
                                <input type="datetime-local" id="registrationStart" name="registration_start" class="form-control">
                                <div class="error-message" id="registrationStartError">Please select a valid date and time</div>
                            </div>

                            <div class="form-group">
                                <label for="registrationEnd">Registration Closes</label>
                                <input type="datetime-local" id="registrationEnd" name="registration_end" class="form-control">
                                <div class="error-message" id="registrationEndError">Please select a valid date and time</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventStatus">Event Status</label>
                            <select id="eventStatus" name="event_status" class="form-control">
                                <option value="upcoming">Upcoming</option>
                                <option value="registration_open">Registration Open</option>
                                <option value="registration_closed">Registration Closed</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 3: In-Charge Details -->
                    <div class="step-content" data-step="3">
                        <h2 class="step-title">
                            <i class="fas fa-users-cog step-icon"></i>
                            Event Management Team
                        </h2>
                        <p class="step-description">Assign responsible persons and contact information for the event</p>

                        <div class="form-group">
                            <label for="primaryContact">Primary Contact Person <span class="required">*</span></label>
                            <input type="text" id="primaryContact" name="primary_contact" class="form-control" 
                                   placeholder="Full name of the main organizer" required>
                            <div class="error-message" id="primaryContactError">Please enter the primary contact person</div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="contactEmail">Contact Email <span class="required">*</span></label>
                                <input type="email" id="contactEmail" name="contact_email" class="form-control" 
                                       placeholder="organizer@elitecricket.com" required>
                                <div class="error-message" id="contactEmailError">Please enter a valid email address</div>
                            </div>

                            <div class="form-group">
                                <label for="contactPhone">Contact Phone <span class="required">*</span></label>
                                <input type="tel" id="contactPhone" name="contact_phone" class="form-control" 
                                       placeholder="+94 77 123 4567" required>
                                <div class="error-message" id="contactPhoneError">Please enter a valid phone number</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="secondaryContact">Secondary Contact Person</label>
                            <input type="text" id="secondaryContact" name="secondary_contact" class="form-control" 
                                   placeholder="Backup organizer (optional)">
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="secondaryEmail">Secondary Email</label>
                                <input type="email" id="secondaryEmail" name="secondary_email" class="form-control" 
                                       placeholder="backup@elitecricket.com">
                            </div>

                            <div class="form-group">
                                <label for="secondaryPhone">Secondary Phone</label>
                                <input type="tel" id="secondaryPhone" name="secondary_phone" class="form-control" 
                                       placeholder="+94 71 987 6543">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventCoordinator">Event Coordinator</label>
                            <select id="eventCoordinator" name="event_coordinator" class="form-control">
                                <option value="">Select Coordinator</option>
                                <option value="john_doe">John Doe (Head Coach)</option>
                                <option value="jane_smith">Jane Smith (Academy Manager)</option>
                                <option value="mike_wilson">Mike Wilson (Senior Coach)</option>
                                <option value="sarah_johnson">Sarah Johnson (Assistant Manager)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="specialRequirements">Special Requirements / Notes</label>
                            <textarea id="specialRequirements" name="special_requirements" class="form-control" 
                                    placeholder="Any special arrangements, equipment needs, dietary requirements, accessibility considerations, etc." 
                                    rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Step 4: Confirmation -->
                    <div class="step-content" data-step="4">
                        <h2 class="step-title">
                            <i class="fas fa-check-circle step-icon"></i>
                            Review & Confirm
                        </h2>
                        <p class="step-description">Please review all the details before creating the event</p>

                        <div class="confirmation-summary">
                            <!-- Basic Details Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-info-circle"></i>
                                    Basic Information
                                </div>
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <div class="summary-label">Event Name</div>
                                        <div class="summary-value" id="summaryEventName">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Event Type</div>
                                        <div class="summary-value" id="summaryEventType">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Category</div>
                                        <div class="summary-value" id="summaryEventCategory">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Venue</div>
                                        <div class="summary-value" id="summaryEventVenue">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Max Participants</div>
                                        <div class="summary-value" id="summaryMaxParticipants">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Registration Fee</div>
                                        <div class="summary-value" id="summaryRegistrationFee">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    Schedule
                                </div>
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <div class="summary-label">Start Date & Time</div>
                                        <div class="summary-value" id="summaryStartDateTime">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">End Date & Time</div>
                                        <div class="summary-value" id="summaryEndDateTime">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Registration Opens</div>
                                        <div class="summary-value" id="summaryRegistrationStart">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Registration Closes</div>
                                        <div class="summary-value" id="summaryRegistrationEnd">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Event Status</div>
                                        <div class="summary-value" id="summaryEventStatus">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-users-cog"></i>
                                    Management Team
                                </div>
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <div class="summary-label">Primary Contact</div>
                                        <div class="summary-value" id="summaryPrimaryContact">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Contact Email</div>
                                        <div class="summary-value" id="summaryContactEmail">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Contact Phone</div>
                                        <div class="summary-value" id="summaryContactPhone">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Event Coordinator</div>
                                        <div class="summary-value" id="summaryEventCoordinator">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-file-alt"></i>
                                    Description
                                </div>
                                <div class="summary-item" style="grid-column: 1 / -1;">
                                    <div class="summary-value" id="summaryEventDescription">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="wizard-navigation">
                    <button type="button" class="nav-btn btn-prev" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                        Previous
                    </button>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="button" class="nav-btn btn-cancel" onclick="closeCreateEventModal()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                        
                        <button type="button" class="nav-btn btn-next" id="nextBtn">
                            Next
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        
                        <button type="submit" class="nav-btn btn-submit" id="submitBtn" style="display: none;">
                            <i class="fas fa-check"></i>
                            Create Event
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== EVENTS PAGE DEBUG ===');
    console.log('URLROOT:', '<?php echo URLROOT; ?>');
    console.log('CSS file path:', '<?php echo URLROOT; ?>/css/admin/create-event-wizard.css');
    console.log('JS file path:', '<?php echo URLROOT; ?>/js/admin/create-event-wizard.js');
    
    // Check if modal exists
    const modal = document.getElementById('createEventModal');
    console.log('Modal element found:', !!modal);
    
    // Check if button exists
    const button = document.getElementById('createEventBtn');
    console.log('Button element found:', !!button);
    
    // Check if openCreateEventModal function exists
    console.log('openCreateEventModal function exists:', typeof openCreateEventModal);
    
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
            openCreateEventModal();
        },
        eventClassNames: function(arg) {
            return ['event-' + arg.event.extendedProps.type];
        }
    });
    calendar.render();

    // Updated event handlers for new wizard modal
    const createEventBtn = document.getElementById('createEventBtn');
    const createTournamentBtn = document.getElementById('createTournamentBtn');
    
    if (createEventBtn) {
        createEventBtn.addEventListener('click', () => {
            console.log('Create Event button clicked!');
            openCreateEventModal();
        });
    } else {
        console.error('Create Event button not found!');
    }
    
    if (createTournamentBtn) {
        createTournamentBtn.addEventListener('click', () => {
            openCreateEventModal();
            // Pre-select tournament in the wizard
            setTimeout(() => {
                const eventTypeSelect = document.getElementById('eventType');
                if (eventTypeSelect) {
                    eventTypeSelect.value = 'tournament';
                }
            }, 100);
        });
    }

    // Other existing event handlers
    const viewAllUpcomingBtn = document.getElementById('viewAllUpcomingBtn');
    const viewAllPastBtn = document.getElementById('viewAllPastBtn');
    
    if (viewAllUpcomingBtn) {
        viewAllUpcomingBtn.addEventListener('click', () => viewAllEvents('upcoming'));
    }
    
    if (viewAllPastBtn) {
        viewAllPastBtn.addEventListener('click', () => viewAllEvents('past'));
    }
    
    // Debug function
    window.debugWizard = function() {
        console.log('=== WIZARD DEBUG INFO ===');
        const modal = document.getElementById('createEventModal');
        const steps = document.querySelectorAll('.step-content');
        const activeSteps = document.querySelectorAll('.step-content.active');
        
        console.log('Modal element:', modal);
        console.log('Modal classes:', modal ? modal.className : 'Not found');
        console.log('Total steps found:', steps.length);
        console.log('Active steps found:', activeSteps.length);
        
        steps.forEach((step, index) => {
            const stepNum = step.getAttribute('data-step');
            const isActive = step.classList.contains('active');
            const display = window.getComputedStyle(step).display;
            const opacity = window.getComputedStyle(step).opacity;
            
            console.log(`Step ${stepNum}: active=${isActive}, display=${display}, opacity=${opacity}`);
        });
        
        if (window.eventWizard) {
            console.log('Current wizard step:', window.eventWizard.currentStep);
        } else {
            console.log('EventWizard not initialized');
        }
        console.log('========================');
    };
    
    // Simple modal test function
    window.testModal = function() {
        const modal = document.getElementById('createEventModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            console.log('Modal test: Showing modal manually');
        } else {
            console.error('Modal test: Modal not found');
        }
    };
    
    // Test button click manually
    window.testButton = function() {
        const btn = document.getElementById('createEventBtn');
        if (btn) {
            btn.click();
            console.log('Button test: Clicked button manually');
        } else {
            console.error('Button test: Button not found');
        }
    };
});

// Legacy function for viewing all events
function viewAllEvents(type) {
    window.location.href = `<?php echo URLROOT; ?>/admin/events?filter=${type}`;
}

function viewEventDetails(event) {
    alert(`Event: ${event.title}\nDate: ${event.start}\nType: ${event.extendedProps.type}\nLocation: ${event.extendedProps.location}\nDescription: ${event.extendedProps.description}`);
}

// Function to refresh events after creation
function refreshEvents() {
    location.reload(); // Simple refresh - can be improved with AJAX
}

// Legacy functions for existing events (can be updated later)
function editEvent(eventId) {
    // Fetch event details and populate form
    fetch(`<?php echo URLROOT; ?>/admin/get_event/${eventId}`)
        .then(response => response.json())
        .then(event => {
            alert('Edit functionality will be updated to use the new wizard format');
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
</script>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<!-- Admin Events JS -->
<script src="<?php echo URLROOT; ?>/js/admin/events.js"></script>
<!-- Create Event Wizard JS -->
<script src="<?php echo URLROOT; ?>/js/admin/create-event-wizard.js"></script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>

</body>
</html>
