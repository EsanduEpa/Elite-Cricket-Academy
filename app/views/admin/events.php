<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/create-event-wizard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/events.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/tournaments.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

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
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
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
                    <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Feedback Monitoring</span>
                        <span class="badge">12</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
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

        <!-- Flash Messages -->
        <?php flash('event_message'); ?>

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
            <div class="events-table-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-plus"></i> Upcoming Events</h2>
                    <div class="event-filters">
                        <select id="eventTypeFilter" class="filter-select">
                            <option value="all">All Types</option>
                            <option value="tournament">Tournament</option>
                            <option value="training">Training</option>
                            <option value="match">Match</option>
                            <option value="meeting">Meeting</option>
                        </select>
                        <select id="eventDateFilter" class="filter-select">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                        <button class="btn btn-outline" id="viewAllUpcomingBtn">View All</button>
                    </div>
                </div>
                <div class="events-table-wrapper">
                    <table class="events-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event Title</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['upcomingEvents'] as $event): ?>
                            <tr data-event-id="<?php echo $event['id']; ?>">
                                <td class="date-cell">
                                    <div class="table-date">
                                        <div class="date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                        <div class="date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                                        <div class="date-year"><?php echo date('Y', strtotime($event['event_date'])); ?></div>
                                    </div>
                                </td>
                                <td class="title-cell">
                                    <strong><?php echo $event['title']; ?></strong>
                                </td>
                                <td>
                                    <span class="event-type-badge <?php echo $event['event_type']; ?>">
                                        <i class="fas fa-<?php echo $event['event_type'] == 'tournament' ? 'trophy' : ($event['event_type'] == 'training' ? 'dumbbell' : 'users'); ?>"></i>
                                        <?php echo ucfirst($event['event_type']); ?>
                                    </span>
                                </td>
                                <td class="location-cell">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $event['location']; ?>
                                </td>
                                <td class="description-cell">
                                    <?php echo $event['description']; ?>
                                </td>
                                <td class="actions-cell">
                                    <button 
    class="btn-action-table edit" 
    onclick="editEvent(<?php echo isset($event['EventID']) ? (int)$event['EventID'] : (isset($event['id']) ? (int)$event['id'] : 0); ?>)" 
    title="Edit"
>
    <i class="fas fa-edit"></i>
</button>
                                    <button class="btn-action-table delete" onclick="deleteEvent(<?php echo $event['id']; ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Past Events -->
            <div class="events-table-section">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Past Events</h2>
                    <button class="btn btn-outline" id="viewAllPastBtn">View All</button>
                </div>
                <div class="events-table-wrapper">
                    <table class="events-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event Title</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['pastEvents'] as $event): ?>
                            <tr class="past-event" data-event-id="<?php echo $event['id']; ?>">
                                <td class="date-cell">
                                    <div class="table-date">
                                        <div class="date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                        <div class="date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                                        <div class="date-year"><?php echo date('Y', strtotime($event['event_date'])); ?></div>
                                    </div>
                                </td>
                                <td class="title-cell">
                                    <strong><?php echo $event['title']; ?></strong>
                                </td>
                                <td>
                                    <span class="event-type-badge <?php echo $event['event_type']; ?>">
                                        <i class="fas fa-<?php echo $event['event_type'] == 'tournament' ? 'trophy' : ($event['event_type'] == 'training' ? 'dumbbell' : 'users'); ?>"></i>
                                        <?php echo ucfirst($event['event_type']); ?>
                                    </span>
                                </td>
                                <td class="location-cell">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $event['location']; ?>
                                </td>
                                <td class="description-cell">
                                    <?php echo $event['description']; ?>
                                </td>
                                <td>
                                    <span class="event-status-badge completed">
                                        <i class="fas fa-check-circle"></i>
                                        Completed
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button class="btn-action-table view" onclick="viewEvent(<?php echo $event['id']; ?>)" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action-table delete" onclick="deleteEvent(<?php echo $event['id']; ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
            <form id="eventWizardForm" action="<?php echo URLROOT; ?>/admin/create_event" method="POST">
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
                                    <option value="Tournament">Tournament</option>
                                    <option value="Training Camp">Training Camp</option>
                                    <option value="Match">Match</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Competition">Competition</option>
                                    <option value="Trial">Trial</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Other">Other</option>
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

<!-- Tournament Creation Wizard Modal -->
<div id="createTournamentModal" class="create-event-modal">
    <div class="wizard-container">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-trophy"></i>
                Create Cricket Tournament
            </h2>
        </div>

        <div class="modal-body">
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-line" id="tournamentProgressLine"></div>
                </div>
                <div class="steps-indicator">
                    <div class="step-indicator">
                        <div class="step-circle active" data-step="1">1</div>
                        <div class="step-label active">Basic Info</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="2">2</div>
                        <div class="step-label">Schedule & Venue</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="3">3</div>
                        <div class="step-label">Rules & Supervisors</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="4">4</div>
                        <div class="step-label">Confirmation</div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form id="tournamentWizardForm" action="<?php echo URLROOT; ?>/admin/createTournament" method="POST">
                <div class="wizard-content">
                    <!-- Step 1: Basic Information -->
                    <div class="tournament-step-content active" data-step="1">
                        <h2 class="step-title">
                            <i class="fas fa-info-circle step-icon"></i>
                            Tournament Basic Information
                        </h2>
                        <p class="step-description">Let's start with the fundamental details of your cricket tournament</p>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tournamentName">Tournament Name <span class="required">*</span></label>
                                <input type="text" id="tournamentName" name="tournament_name" class="form-control" 
                                       placeholder="e.g., Elite Cricket Championship 2025" required>
                                <div class="error-message" id="tournamentNameError">Please enter a tournament name</div>
                            </div>

                            <div class="form-group">
                                <label for="tournamentType">Tournament Format <span class="required">*</span></label>
                                <select id="tournamentType" name="tournament_type" class="form-control" required>
                                    <option value="">Select Format</option>
                                    <option value="knockout">Knockout/Elimination</option>
                                    <option value="round_robin">Round Robin</option>
                                    <option value="league">League Format</option>
                                    <option value="t20">T20 Tournament</option>
                                    <option value="odi">ODI Tournament</option>
                                    <option value="test">Test Match Series</option>
                                    <option value="mixed">Mixed Format</option>
                                </select>
                                <div class="error-message" id="tournamentTypeError">Please select a tournament format</div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="numberOfTeams">Number of Teams <span class="required">*</span></label>
                                <select id="numberOfTeams" name="number_of_teams" class="form-control" required>
                                    <option value="">Select Number</option>
                                    <option value="4">4 Teams</option>
                                    <option value="6">6 Teams</option>
                                    <option value="8">8 Teams</option>
                                    <option value="10">10 Teams</option>
                                    <option value="12">12 Teams</option>
                                    <option value="16">16 Teams</option>
                                    <option value="20">20 Teams</option>
                                    <option value="custom">Custom Number</option>
                                </select>
                                <div class="error-message" id="numberOfTeamsError">Please select number of teams</div>
                            </div>

                            <div class="form-group" id="customTeamsGroup" style="display: none;">
                                <label for="customTeamsNumber">Custom Number of Teams</label>
                                <input type="number" id="customTeamsNumber" name="custom_teams_number" class="form-control" 
                                       placeholder="Enter number" min="2" max="50">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="registrationFee">Team Registration Fee (LKR)</label>
                                <input type="number" id="registrationFee" name="registration_fee" class="form-control" 
                                       placeholder="0 for free tournaments" min="0" step="0.01">
                                <div class="error-message" id="registrationFeeError">Please enter a valid amount</div>
                            </div>

                            <div class="form-group">
                                <label for="entryDeadline">Team Registration Deadline</label>
                                <input type="datetime-local" id="entryDeadline" name="entry_deadline" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <h3><i class="fas fa-money-bill-wave"></i> Prize Structure</h3>
                            <div class="prize-grid">
                                <div class="form-group">
                                    <label for="firstPrize">1st Place Prize (LKR)</label>
                                    <input type="number" id="firstPrize" name="first_prize" class="form-control" 
                                           placeholder="50000" min="0" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="secondPrize">2nd Place Prize (LKR)</label>
                                    <input type="number" id="secondPrize" name="second_prize" class="form-control" 
                                           placeholder="25000" min="0" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="thirdPrize">3rd Place Prize (LKR)</label>
                                    <input type="number" id="thirdPrize" name="third_prize" class="form-control" 
                                           placeholder="10000" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tournamentDescription">Tournament Description</label>
                            <textarea id="tournamentDescription" name="tournament_description" class="form-control" 
                                    placeholder="Provide details about the tournament, rules overview, objectives, and any special information..." 
                                    rows="4"></textarea>
                        </div>
                    </div>

                    <!-- Step 2: Schedule & Venue -->
                    <div class="tournament-step-content" data-step="2">
                        <h2 class="step-title">
                            <i class="fas fa-calendar-alt step-icon"></i>
                            Tournament Schedule & Venues
                        </h2>
                        <p class="step-description">Set the dates, match schedule, and venue details for the tournament</p>

                        <div class="datetime-grid">
                            <div class="date-time-group">
                                <h3><i class="fas fa-play-circle"></i> Tournament Period</h3>
                                <div class="form-group">
                                    <label for="tournamentStartDate">Tournament Start Date <span class="required">*</span></label>
                                    <input type="date" id="tournamentStartDate" name="start_date" class="form-control" required>
                                    <div class="error-message" id="tournamentStartDateError">Please select a start date</div>
                                </div>
                                <div class="form-group">
                                    <label for="tournamentEndDate">Tournament End Date <span class="required">*</span></label>
                                    <input type="date" id="tournamentEndDate" name="end_date" class="form-control" required>
                                    <div class="error-message" id="tournamentEndDateError">Please select an end date</div>
                                </div>
                            </div>

                            <div class="date-time-group">
                                <h3><i class="fas fa-clock"></i> Match Timing</h3>
                                <div class="form-group">
                                    <label for="matchStartTime">Daily Match Start Time</label>
                                    <input type="time" id="matchStartTime" name="match_start_time" class="form-control" value="09:00">
                                </div>
                                <div class="form-group">
                                    <label for="matchEndTime">Daily Match End Time</label>
                                    <input type="time" id="matchEndTime" name="match_end_time" class="form-control" value="17:00">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <h3><i class="fas fa-map-marker-alt"></i> Venue Information</h3>
                            <div class="venue-grid">
                                <div class="form-group">
                                    <label for="primaryVenue">Primary Venue <span class="required">*</span></label>
                                    <input type="text" id="primaryVenue" name="primary_venue" class="form-control" 
                                           placeholder="Elite Cricket Academy Ground" required>
                                    <div class="error-message" id="primaryVenueError">Please enter the primary venue</div>
                                </div>
                                <div class="form-group">
                                    <label for="secondaryVenue">Secondary Venue (if any)</label>
                                    <input type="text" id="secondaryVenue" name="secondary_venue" class="form-control" 
                                           placeholder="Additional ground for multiple matches">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="venueAddress">Venue Address</label>
                            <textarea id="venueAddress" name="venue_address" class="form-control" 
                                    placeholder="Complete address with landmarks for teams and spectators..." rows="3"></textarea>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="matchesPerDay">Matches Per Day</label>
                                <select id="matchesPerDay" name="matches_per_day" class="form-control">
                                    <option value="1">1 Match</option>
                                    <option value="2" selected>2 Matches</option>
                                    <option value="3">3 Matches</option>
                                    <option value="4">4 Matches</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="restDays">Rest Days Between Rounds</label>
                                <select id="restDays" name="rest_days" class="form-control">
                                    <option value="0">No Rest Days</option>
                                    <option value="1" selected>1 Day</option>
                                    <option value="2">2 Days</option>
                                    <option value="3">3 Days</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Rules & Supervisors -->
                    <div class="tournament-step-content" data-step="3">
                        <h2 class="step-title">
                            <i class="fas fa-gavel step-icon"></i>
                            Rules & Supervisors
                        </h2>
                        <p class="step-description">Define tournament rules, regulations, and assign supervisory staff</p>

                        <div class="form-group">
                            <h3><i class="fas fa-book"></i> Tournament Rules & Regulations</h3>
                            <textarea id="tournamentRules" name="tournament_rules" class="form-control" 
                                    placeholder="Enter detailed rules and regulations for the tournament:
- Match format (overs, innings, etc.)
- Team composition rules
- Player eligibility criteria
- Equipment regulations
- Conduct and disciplinary rules
- Weather and pitch conditions protocols
- Scoring and result determination
- Appeals and dispute resolution
- Any special tournament-specific rules..." 
                                    rows="8"></textarea>
                        </div>

                        <div class="form-group">
                            <h3><i class="fas fa-users-cog"></i> Tournament Management</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="tournamentDirector">Tournament Director <span class="required">*</span></label>
                                    <select id="tournamentDirector" name="tournament_director" class="form-control" required>
                                        <option value="">Select Director</option>
                                        <option value="john_doe">John Doe (Head Coach)</option>
                                        <option value="jane_smith">Jane Smith (Academy Manager)</option>
                                        <option value="mike_wilson">Mike Wilson (Senior Coach)</option>
                                        <option value="sarah_johnson">Sarah Johnson (Sports Manager)</option>
                                    </select>
                                    <div class="error-message" id="tournamentDirectorError">Please select a tournament director</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="headUmpire">Head Umpire</label>
                                    <select id="headUmpire" name="head_umpire" class="form-control">
                                        <option value="">Select Head Umpire</option>
                                        <option value="umpire_1">David Thompson (Level 3 Umpire)</option>
                                        <option value="umpire_2">Robert Kumar (Level 2 Umpire)</option>
                                        <option value="umpire_3">Michael Silva (Level 3 Umpire)</option>
                                        <option value="external">External Umpire (TBD)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <h3><i class="fas fa-clipboard-list"></i> Match Officials</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="scorers">Official Scorers</label>
                                    <textarea id="scorers" name="scorers" class="form-control" 
                                            placeholder="List designated scorers:
- Primary Scorer: Name
- Secondary Scorer: Name
- Backup Scorers: Names..." rows="4"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="groundStaff">Ground Staff Coordinator</label>
                                    <select id="groundStaff" name="ground_staff" class="form-control">
                                        <option value="">Select Coordinator</option>
                                        <option value="staff_1">Alex Fernando (Ground Manager)</option>
                                        <option value="staff_2">Priya Patel (Facilities Manager)</option>
                                        <option value="staff_3">James Wilson (Maintenance Head)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="emergencyContact">Emergency Contact Information</label>
                            <textarea id="emergencyContact" name="emergency_contact" class="form-control" 
                                    placeholder="Emergency contacts during tournament:
- Medical Emergency: Dr. Name - Phone
- Security: Contact Name - Phone
- Tournament Organizer: Name - Phone
- Venue Emergency: Contact - Phone..." rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="specialInstructions">Special Instructions & Notes</label>
                            <textarea id="specialInstructions" name="special_instructions" class="form-control" 
                                    placeholder="Any additional instructions:
- Spectator guidelines
- Media coverage arrangements
- Catering arrangements
- Transportation details
- Equipment requirements
- Weather contingency plans..." rows="4"></textarea>
                        </div>
                    </div>

                    <!-- Step 4: Confirmation -->
                    <div class="tournament-step-content" data-step="4">
                        <h2 class="step-title">
                            <i class="fas fa-check-circle step-icon"></i>
                            Review & Confirm Tournament
                        </h2>
                        <p class="step-description">Please review all tournament details before creating</p>

                        <div class="confirmation-summary">
                            <!-- Basic Details Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-trophy"></i>
                                    Tournament Information
                                </div>
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <div class="summary-label">Tournament Name</div>
                                        <div class="summary-value" id="summaryTournamentName">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Format</div>
                                        <div class="summary-value" id="summaryTournamentType">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Number of Teams</div>
                                        <div class="summary-value" id="summaryNumberOfTeams">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Registration Fee</div>
                                        <div class="summary-value" id="summaryTournamentRegistrationFee">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">First Prize</div>
                                        <div class="summary-value" id="summaryFirstPrize">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Registration Deadline</div>
                                        <div class="summary-value" id="summaryEntryDeadline">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    Schedule & Venue
                                </div>
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <div class="summary-label">Tournament Period</div>
                                        <div class="summary-value" id="summaryTournamentPeriod">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Match Timing</div>
                                        <div class="summary-value" id="summaryMatchTiming">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Primary Venue</div>
                                        <div class="summary-value" id="summaryPrimaryVenue">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Secondary Venue</div>
                                        <div class="summary-value" id="summarySecondaryVenue">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Matches Per Day</div>
                                        <div class="summary-value" id="summaryMatchesPerDay">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Rest Days</div>
                                        <div class="summary-value" id="summaryRestDays">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Management Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-users-cog"></i>
                                    Tournament Management
                                </div>
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <div class="summary-label">Tournament Director</div>
                                        <div class="summary-value" id="summaryTournamentDirector">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Head Umpire</div>
                                        <div class="summary-value" id="summaryHeadUmpire">-</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-label">Ground Staff Coordinator</div>
                                        <div class="summary-value" id="summaryGroundStaff">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rules Summary -->
                            <div class="summary-section">
                                <div class="summary-title">
                                    <i class="fas fa-gavel"></i>
                                    Rules & Description
                                </div>
                                <div class="summary-item" style="grid-column: 1 / -1;">
                                    <div class="summary-label">Tournament Description</div>
                                    <div class="summary-value" id="summaryTournamentDescription">-</div>
                                </div>
                                <div class="summary-item" style="grid-column: 1 / -1;">
                                    <div class="summary-label">Rules & Regulations</div>
                                    <div class="summary-value" id="summaryTournamentRules">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="wizard-navigation">
                    <button type="button" class="nav-btn btn-prev" id="tournamentPrevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                        Previous
                    </button>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="button" class="nav-btn btn-cancel" onclick="closeTournamentModal()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                        
                        <button type="button" class="nav-btn btn-next" id="tournamentNextBtn">
                            Next
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        
                        <button type="submit" class="nav-btn btn-submit" id="tournamentSubmitBtn" style="display: none;">
                            <i class="fas fa-trophy"></i>
                            Create Tournament
                        </button>
                    </div>
                </div>
            </form>
        </div>
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
            openCreateEventModal();
        },
        eventClassNames: function(arg) {
            return ['event-' + arg.event.extendedProps.type];
        }
    });
    calendar.render();

    // Note: Create Event button handler is now in create-event-wizard.js to avoid timing issues
    // Note: Create Tournament button handler is in create-tournament-wizard.js
    
    // Other existing event handlers
    const viewAllUpcomingBtn = document.getElementById('viewAllUpcomingBtn');
    const viewAllPastBtn = document.getElementById('viewAllPastBtn');
    
    if (viewAllUpcomingBtn) {
        viewAllUpcomingBtn.addEventListener('click', () => viewAllEvents('upcoming'));
    }
    
    if (viewAllPastBtn) {
        viewAllPastBtn.addEventListener('click', () => viewAllEvents('past'));
    }
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

// Note: editEvent() function is defined in events.js


function editEvent(eventId) {
    if (!eventId || eventId === 0) {
        console.error("⚠️ Invalid event ID passed to editEvent()");
        alert("Invalid event ID. Please refresh the page and try again.");
        return;
    }

    // Redirect to the edit page
    window.location.href = `<?php echo URLROOT; ?>/admin/edit_event/${eventId}`;
}


function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        window.location.href = `<?php echo URLROOT; ?>/admin/delete_event/${eventId}`;
    }
}

function viewEvent(eventId) {
    window.location.href = `<?php echo URLROOT; ?>/admin/event_details/${eventId}`;
}

// Event Filter Functions
function filterEvents() {
    const typeFilter = document.getElementById('eventTypeFilter').value;
    const dateFilter = document.getElementById('eventDateFilter').value;
    const rows = document.querySelectorAll('.events-table tbody tr');
    
    rows.forEach(row => {
        let showRow = true;
        
        // Type filter
        if (typeFilter !== 'all') {
            const eventType = row.querySelector('.event-type-badge')?.textContent.trim().toLowerCase();
            if (eventType && !eventType.includes(typeFilter.toLowerCase())) {
                showRow = false;
            }
        }
        
        // Date filter (simplified - would need actual dates in production)
        if (dateFilter !== 'all' && showRow) {
            const dateText = row.querySelector('.date-cell')?.textContent || '';
            const today = new Date();
            
            if (dateFilter === 'today' && !dateText.includes(today.getDate().toString())) {
                showRow = false;
            } else if (dateFilter === 'week') {
                // Filter for this week
                showRow = true; // Simplified
            } else if (dateFilter === 'month') {
                // Filter for this month
                showRow = true; // Simplified
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Add event listeners for filters
document.addEventListener('DOMContentLoaded', function() {
    const typeFilter = document.getElementById('eventTypeFilter');
    const dateFilter = document.getElementById('eventDateFilter');
    
    if (typeFilter) typeFilter.addEventListener('change', filterEvents);
    if (dateFilter) dateFilter.addEventListener('change', filterEvents);
    
    // Auto-dismiss flash messages after 5 seconds
    const flashMessage = document.getElementById('msg-flash');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.classList.add('alert-fade-out');
            setTimeout(() => {
                flashMessage.remove();
            }, 500);
        }, 5000);
    }
});
</script>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<!-- Common Sidebar JS -->
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<!-- Common Tournaments JS -->
<script src="<?php echo URLROOT; ?>/js/common/tournaments.js"></script>
<!-- Admin Events JS -->
<script src="<?php echo URLROOT; ?>/js/admin/events.js"></script>
<!-- Create Event Wizard JS -->
<script src="<?php echo URLROOT; ?>/js/admin/create-event-wizard.js"></script>
<!-- Create Tournament Wizard JS -->
<script src="<?php echo URLROOT; ?>/js/admin/create-tournament-wizard.js"></script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>

</body>
</html>
