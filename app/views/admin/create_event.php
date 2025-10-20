<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
    <style>
        /* Event Creation Wizard Styles */
        .event-wizard-container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .wizard-header {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .wizard-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .wizard-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        /* Progress Bar */
        .progress-container {
            padding: 30px;
            background: rgba(74, 144, 226, 0.05);
        }

        .progress-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-bottom: 20px;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 25px;
            right: 25px;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            z-index: 1;
        }

        .progress-line {
            position: absolute;
            top: 20px;
            left: 25px;
            height: 4px;
            background: linear-gradient(90deg, #4A90E2, #357ABD);
            border-radius: 2px;
            z-index: 2;
            width: 0%;
            transition: width 0.5s ease;
        }

        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 3;
            background: white;
            padding: 5px;
            border-radius: 50px;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            border: 3px solid #e9ecef;
            background: white;
            color: #6c757d;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .step-circle.active {
            border-color: #4A90E2;
            background: #4A90E2;
            color: white;
            transform: scale(1.1);
        }

        .step-circle.completed {
            border-color: #28a745;
            background: #28a745;
            color: white;
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-align: center;
            min-width: 80px;
        }

        .step-label.active {
            color: #4A90E2;
        }

        .step-label.completed {
            color: #28a745;
        }

        /* Form Steps */
        .wizard-content {
            padding: 40px;
            min-height: 500px;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .step-description {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .form-group label .required {
            color: #e74c3c;
            margin-left: 3px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
        }

        .form-control:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
            background: white;
        }

        .form-control.error {
            border-color: #e74c3c;
        }

        .form-control.success {
            border-color: #28a745;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            cursor: pointer;
        }

        /* Date Time Inputs */
        .datetime-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .date-time-group {
            background: rgba(74, 144, 226, 0.05);
            padding: 20px;
            border-radius: 15px;
            border: 2px solid rgba(74, 144, 226, 0.1);
        }

        /* Confirmation Summary */
        .confirmation-summary {
            background: rgba(74, 144, 226, 0.05);
            border-radius: 15px;
            padding: 25px;
            border: 2px solid rgba(74, 144, 226, 0.1);
        }

        .summary-section {
            margin-bottom: 25px;
        }

        .summary-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4A90E2;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #4A90E2;
        }

        .summary-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        /* Navigation Buttons */
        .wizard-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 40px;
            background: rgba(74, 144, 226, 0.05);
            border-top: 1px solid rgba(74, 144, 226, 0.1);
        }

        .nav-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 120px;
            justify-content: center;
        }

        .btn-prev {
            background: #6c757d;
            color: white;
        }

        .btn-prev:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .btn-next {
            background: #4A90E2;
            color: white;
        }

        .btn-next:hover {
            background: #357abd;
            transform: translateY(-1px);
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #218838, #1ba085);
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Date Time Grid */
        .datetime-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
        }

        .date-time-group {
            background: rgba(74, 144, 226, 0.03);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(74, 144, 226, 0.1);
        }

        .date-time-group h3 {
            margin: 0 0 15px 0;
            color: #4A90E2;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Loading Animation */
        .loading {
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .wizard-container {
                margin: 10px;
                padding: 0;
            }

            .step-container {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .datetime-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .wizard-navigation {
                padding: 20px;
                flex-direction: column;
                gap: 15px;
            }

            .wizard-navigation > div {
                width: 100%;
                justify-content: center;
            }

            .nav-btn {
                width: 100%;
            }

            .progress-container {
                padding: 15px 20px;
            }

            .step-indicator {
                padding: 3px;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .step-label {
                font-size: 11px;
            }
        }

        .nav-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-prev {
            background: #6c757d;
            color: white;
        }

        .btn-prev:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-next {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Step Icons */
        .step-icon {
            font-size: 2rem;
            color: #4A90E2;
            margin-right: 10px;
        }

        /* Error Messages */
        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        /* Loading States */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .event-wizard-container {
                margin: 20px;
                border-radius: 15px;
            }

            .wizard-content {
                padding: 30px 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .datetime-grid {
                grid-template-columns: 1fr;
            }

            .progress-bar {
                flex-wrap: wrap;
                gap: 10px;
            }

            .step-indicator {
                margin-bottom: 10px;
            }

            .wizard-navigation {
                padding: 20px;
            }

            .nav-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>


<div class="admin-layout">
    <!-- Left Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-shield-alt"></i>
                <h3>Admin Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/events" class="nav-link active">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Events</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/users" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/settings" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a></li>
            </ul>
        </nav>

        <div class="logout-section">
            <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main-content">
        <div class="event-wizard-container">
            <!-- Wizard Header -->
            <div class="wizard-header">
                <h1><i class="fas fa-calendar-plus"></i> Create New Event</h1>
                <p>Follow these steps to create a comprehensive cricket academy event</p>
            </div>

            <!-- Flash Messages -->
            <?php flash('event_message'); ?>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="step-indicator">
                        <div class="step-circle active" data-step="1">1</div>
                        <div class="step-label active">Basic Details</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="2">2</div>
                        <div class="step-label">Date & Time</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="3">3</div>
                        <div class="step-label">In-Charge Details</div>
                    </div>
                    <div class="step-indicator">
                        <div class="step-circle" data-step="4">4</div>
                        <div class="step-label">Confirmation</div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form id="eventForm" action="<?php echo URLROOT; ?>/admin/create_event" method="POST">
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
                        <button type="button" class="nav-btn" style="background: #6c757d;" onclick="window.location.href='<?php echo URLROOT; ?>/admin/events'">
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
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/dashboard.js"></script>
<script>
// Event Creation Wizard JavaScript
class EventWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.formData = {};
        this.init();
    }

    init() {
        this.form = document.getElementById('eventForm');
        this.bindEvents();
        this.updateProgress();
        this.setMinDates();
    }

    bindEvents() {
        document.getElementById('nextBtn').addEventListener('click', () => this.nextStep());
        document.getElementById('prevBtn').addEventListener('click', () => this.prevStep());
        this.form.addEventListener('submit', (e) => this.submitForm(e));
        
        // Real-time validation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearError(input));
        });
    }

    setMinDates() {
        const today = new Date().toISOString().split('T')[0];
        const now = new Date().toISOString().slice(0, 16);
        
        document.getElementById('startDate').min = today;
        document.getElementById('endDate').min = today;
        document.getElementById('registrationStart').min = now;
        document.getElementById('registrationEnd').min = now;
    }

    nextStep() {
        if (this.validateCurrentStep()) {
            this.saveCurrentStepData();
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
                this.updateProgress();
                if (this.currentStep === 4) {
                    this.populateSummary();
                }
            }
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
            this.updateProgress();
        }
    }

    showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Show current step
        document.querySelector(`[data-step="${step}"]`).classList.add('active');
        
        // Update navigation buttons
        document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'flex';
        document.getElementById('nextBtn').style.display = step === this.totalSteps ? 'none' : 'flex';
        document.getElementById('submitBtn').style.display = step === this.totalSteps ? 'flex' : 'none';
    }

    updateProgress() {
        const progressLine = document.getElementById('progressLine');
        const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        progressLine.style.width = progress + '%';

        // Update step indicators
        document.querySelectorAll('.step-circle').forEach((circle, index) => {
            const stepNum = index + 1;
            circle.classList.remove('active', 'completed');
            
            if (stepNum < this.currentStep) {
                circle.classList.add('completed');
                circle.innerHTML = '<i class="fas fa-check"></i>';
            } else if (stepNum === this.currentStep) {
                circle.classList.add('active');
                circle.innerHTML = stepNum;
            } else {
                circle.innerHTML = stepNum;
            }
        });

        // Update step labels
        document.querySelectorAll('.step-label').forEach((label, index) => {
            const stepNum = index + 1;
            label.classList.remove('active', 'completed');
            
            if (stepNum < this.currentStep) {
                label.classList.add('completed');
            } else if (stepNum === this.currentStep) {
                label.classList.add('active');
            }
        });
    }

    validateCurrentStep() {
        console.log(`Validating step ${this.currentStep}...`);
        const currentStepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        let invalidFields = [];

        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
                invalidFields.push(field.name || field.id);
            }
        });

        // Additional validations
        if (this.currentStep === 2) {
            if (!this.validateDates()) {
                isValid = false;
                invalidFields.push('date validation');
            }
        }

        if (isValid) {
            console.log(`✓ Step ${this.currentStep} validation passed`);
        } else {
            console.error(`✗ Step ${this.currentStep} validation failed. Invalid fields:`, invalidFields);
        }

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        const errorElement = document.getElementById(field.id + 'Error');
        
        // Clear previous errors
        field.classList.remove('error', 'success');
        if (errorElement) errorElement.classList.remove('show');

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            this.showError(field, errorElement, 'This field is required');
            return false;
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showError(field, errorElement, 'Please enter a valid email address');
                return false;
            }
        }

        // Phone validation
        if (field.type === 'tel' && value) {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]+$/;
            if (!phoneRegex.test(value) || value.length < 10) {
                this.showError(field, errorElement, 'Please enter a valid phone number');
                return false;
            }
        }

        // Number validation
        if (field.type === 'number' && value) {
            if (isNaN(value) || value < 0) {
                this.showError(field, errorElement, 'Please enter a valid number');
                return false;
            }
        }

        // Success state
        if (value) {
            field.classList.add('success');
        }

        return true;
    }

    validateDates() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (startDate && endDate) {
            const start = new Date(startDate + ' ' + startTime);
            const end = new Date(endDate + ' ' + endTime);

            if (end <= start) {
                this.showError(document.getElementById('endDate'), 
                              document.getElementById('endDateError'), 
                              'End date must be after start date');
                return false;
            }
        }

        return true;
    }

    showError(field, errorElement, message) {
        field.classList.add('error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    clearError(field) {
        field.classList.remove('error');
        const errorElement = document.getElementById(field.id + 'Error');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
    }

    saveCurrentStepData() {
        const currentStepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        const inputs = currentStepElement.querySelectorAll('.form-control');
        
        inputs.forEach(input => {
            this.formData[input.name] = input.value;
        });
    }

    populateSummary() {
        // Basic Details
        document.getElementById('summaryEventName').textContent = document.getElementById('eventName').value || '-';
        document.getElementById('summaryEventType').textContent = this.getSelectText('eventType') || '-';
        document.getElementById('summaryEventCategory').textContent = this.getSelectText('eventCategory') || '-';
        document.getElementById('summaryEventVenue').textContent = document.getElementById('eventVenue').value || '-';
        document.getElementById('summaryMaxParticipants').textContent = document.getElementById('maxParticipants').value || 'Unlimited';
        document.getElementById('summaryRegistrationFee').textContent = document.getElementById('registrationFee').value ? 
            'LKR ' + document.getElementById('registrationFee').value : 'Free';

        // Date & Time
        const startDate = document.getElementById('startDate').value;
        const startTime = document.getElementById('startTime').value;
        const endDate = document.getElementById('endDate').value;
        const endTime = document.getElementById('endTime').value;
        
        document.getElementById('summaryStartDateTime').textContent = 
            startDate && startTime ? this.formatDateTime(startDate, startTime) : '-';
        document.getElementById('summaryEndDateTime').textContent = 
            endDate && endTime ? this.formatDateTime(endDate, endTime) : '-';
        
        const regStart = document.getElementById('registrationStart').value;
        const regEnd = document.getElementById('registrationEnd').value;
        document.getElementById('summaryRegistrationStart').textContent = 
            regStart ? this.formatDateTime(regStart.split('T')[0], regStart.split('T')[1]) : '-';
        document.getElementById('summaryRegistrationEnd').textContent = 
            regEnd ? this.formatDateTime(regEnd.split('T')[0], regEnd.split('T')[1]) : '-';
        
        document.getElementById('summaryEventStatus').textContent = this.getSelectText('eventStatus') || '-';

        // Contact Details
        document.getElementById('summaryPrimaryContact').textContent = document.getElementById('primaryContact').value || '-';
        document.getElementById('summaryContactEmail').textContent = document.getElementById('contactEmail').value || '-';
        document.getElementById('summaryContactPhone').textContent = document.getElementById('contactPhone').value || '-';
        document.getElementById('summaryEventCoordinator').textContent = this.getSelectText('eventCoordinator') || '-';

        // Description
        document.getElementById('summaryEventDescription').textContent = document.getElementById('eventDescription').value || '-';
    }

    getSelectText(elementId) {
        const select = document.getElementById(elementId);
        return select.options[select.selectedIndex]?.text;
    }

    formatDateTime(date, time) {
        if (!date || !time) return '-';
        const dateObj = new Date(date + ' ' + time);
        return dateObj.toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    submitForm(e) {
        e.preventDefault();
        console.log('=== EVENT FORM SUBMISSION STARTED ===');
        console.log('Form action:', this.form.action);
        console.log('Form method:', this.form.method);
        
        if (this.validateCurrentStep()) {
            console.log('✓ Validation passed');
            
            // Collect all form data
            const formData = new FormData(this.form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<div class="loading"></div> Creating Event...';
            submitBtn.disabled = true;
            console.log('✓ Submit button disabled, showing loading state');

            console.log('✓ Submitting form to:', this.form.action);
            // Actually submit the form to the server
            this.form.submit();
        } else {
            console.error('✗ Validation failed - form not submitted');
        }
    }
}

// Initialize the wizard when page loads
document.addEventListener('DOMContentLoaded', () => {
    new EventWizard();
});
</script>
                                    <option value="workshop">Workshop</option>
                                    <option value="match">Practice Match</option>
                                    <option value="ceremony">Ceremony</option>
                                    <option value="selection">Selection Trial</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="error-message" id="eventTypeError">Please select an event type</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventDescription">Event Description <span class="required">*</span></label>
                            <textarea id="eventDescription" name="event_description" class="form-control" 
                                    placeholder="Provide a detailed description of the event, its objectives, and what participants can expect..." 
                                    rows="4" required></textarea>
                            <div class="error-message" id="eventDescriptionError">Please provide an event description</div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="eventCategory">Category <span class="required">*</span></label>
                                <select id="eventCategory" name="event_category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="under_12">Under 12</option>
                                    <option value="under_15">Under 15</option>
                                    <option value="under_18">Under 18</option>
                                    <option value="senior">Senior (18+)</option>
                                    <option value="all_ages">All Ages</option>
                                    <option value="coaches">Coaches</option>
                                </select>
                                <div class="error-message" id="eventCategoryError">Please select a category</div>
                            </div>

                            <div class="form-group">
                                <label for="maxParticipants">Maximum Participants</label>
                                <input type="number" id="maxParticipants" name="max_participants" class="form-control" 
                                       placeholder="e.g., 50" min="1" max="1000">
                                <div class="error-message" id="maxParticipantsError">Please enter a valid number</div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="eventVenue">Venue <span class="required">*</span></label>
                                <input type="text" id="eventVenue" name="event_venue" class="form-control" 
                                       placeholder="e.g., Main Cricket Ground" required>
                                <div class="error-message" id="eventVenueError">Please enter the venue</div>
                            </div>

                            <div class="form-group">
                                <label for="registrationFee">Registration Fee (LKR)</label>
                                <input type="number" id="registrationFee" name="registration_fee" class="form-control" 
                                       placeholder="0.00" min="0" step="0.01">
                                <div class="error-message" id="registrationFeeError">Please enter a valid amount</div>
                            </div>
                        </div>
                    </div>
                    <span>Events & Tournaments</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/users" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/coaches" class="nav-link">
                    <i class="fas fa-user-tie"></i>
                    <span>Coaches</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/settings" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Create Event Header -->
        <div class="create-event-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-plus-circle"></i> Create New Event</h1>
                    <p>Add a new cricket academy event, tournament, or training session</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Events
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Event Form -->
        <div class="create-event-form">
            <form action="<?php echo URLROOT; ?>/admin/create_event" method="POST" id="createEventForm">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                        <p>Enter the fundamental details of your event</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="title">Event Title <span class="required">*</span></label>
                            <input type="text" id="title" name="title" required 
                                   placeholder="Enter event title (e.g., Junior Cricket Championship)">
                            <div class="form-help">Choose a clear, descriptive title for your event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_type">Event Type <span class="required">*</span></label>
                            <select id="event_type" name="event_type" required>
                                <option value="">Select Event Type</option>
                                <option value="tournament">🏆 Tournament</option>
                                <option value="training">🏋️ Training Session</option>
                                <option value="match">⚾ Match</option>
                                <option value="workshop">📚 Workshop</option>
                                <option value="camp">🏕️ Cricket Camp</option>
                                <option value="clinic">🩺 Skills Clinic</option>
                            </select>
                            <div class="form-help">Select the type of event you're organizing</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_date">Event Date <span class="required">*</span></label>
                            <input type="date" id="event_date" name="event_date" required>
                            <div class="form-help">Choose the date when the event will take place</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_time">Event Time</label>
                            <input type="time" id="event_time" name="event_time">
                            <div class="form-help">Specify the start time (optional)</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location <span class="required">*</span></label>
                            <input type="text" id="location" name="location" required 
                                   placeholder="e.g., Main Cricket Ground, Practice Nets">
                            <div class="form-help">Where will the event be held?</div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Event Description <span class="required">*</span></label>
                            <textarea id="description" name="description" rows="4" required 
                                      placeholder="Provide a detailed description of the event, including objectives, activities, and any special requirements..."></textarea>
                            <div class="form-help">Describe what participants can expect from this event</div>
                        </div>
                    </div>
                </div>

                <!-- Event Details Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-cogs"></i> Event Details</h3>
                        <p>Additional information and settings</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="duration">Duration (hours)</label>
                            <input type="number" id="duration" name="duration" min="0.5" max="24" step="0.5" 
                                   placeholder="e.g., 2.5">
                            <div class="form-help">Expected duration of the event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="max_participants">Max Participants</label>
                            <input type="number" id="max_participants" name="max_participants" min="1" 
                                   placeholder="e.g., 30">
                            <div class="form-help">Maximum number of participants allowed</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="age_group">Age Group</label>
                            <select id="age_group" name="age_group">
                                <option value="">Select Age Group</option>
                                <option value="under-12">Under 12</option>
                                <option value="under-14">Under 14</option>
                                <option value="under-16">Under 16</option>
                                <option value="under-18">Under 18</option>
                                <option value="under-21">Under 21</option>
                                <option value="senior">Senior (21+)</option>
                                <option value="all-ages">All Ages</option>
                            </select>
                            <div class="form-help">Target age group for this event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="skill_level">Skill Level</label>
                            <select id="skill_level" name="skill_level">
                                <option value="">Select Skill Level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="all-levels">All Levels</option>
                            </select>
                            <div class="form-help">Required skill level for participants</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="registration_fee">Registration Fee ($)</label>
                            <input type="number" id="registration_fee" name="registration_fee" min="0" step="0.01" 
                                   placeholder="0.00">
                            <div class="form-help">Fee to participate (0 for free events)</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person" 
                                   placeholder="e.g., Coach Smith">
                            <div class="form-help">Who should participants contact for questions?</div>
                        </div>
                    </div>
                </div>

                <!-- Requirements & Equipment Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-clipboard-list"></i> Requirements & Equipment</h3>
                        <p>What participants need to know and bring</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="requirements">Requirements</label>
                            <textarea id="requirements" name="requirements" rows="3" 
                                      placeholder="List any specific requirements, prerequisites, or conditions for participation..."></textarea>
                            <div class="form-help">Any special requirements or conditions</div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="equipment_needed">Equipment Needed</label>
                            <textarea id="equipment_needed" name="equipment_needed" rows="3" 
                                      placeholder="List equipment participants should bring (bat, pads, helmet, etc.)..."></textarea>
                            <div class="form-help">Equipment participants need to bring</div>
                        </div>
                    </div>
                </div>

                <!-- Status and Visibility -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-eye"></i> Status & Visibility</h3>
                        <p>Control how and when this event appears</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="status">Event Status</label>
                            <select id="status" name="status">
                                <option value="upcoming">📅 Upcoming</option>
                                <option value="registration-open">✅ Registration Open</option>
                                <option value="registration-closed">🚫 Registration Closed</option>
                                <option value="cancelled">❌ Cancelled</option>
                                <option value="completed">✔️ Completed</option>
                            </select>
                            <div class="form-help">Current status of the event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="visibility">Visibility</label>
                            <select id="visibility" name="visibility">
                                <option value="public">🌐 Public (Everyone can see)</option>
                                <option value="members-only">👥 Members Only</option>
                                <option value="private">🔒 Private (Invite Only)</option>
                                <option value="draft">📝 Draft (Not Published)</option>
                            </select>
                            <div class="form-help">Who can see this event</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-outline" id="saveDraftBtn">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Event
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/admin/events.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form functionality
    initializeCreateEventForm();
    
    // Initialize sidebar
    initializeSidebar();
});

function initializeCreateEventForm() {
    const form = document.getElementById('createEventForm');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    
    // Set minimum date to today
    const eventDateInput = document.getElementById('event_date');
    if (eventDateInput) {
        const today = new Date().toISOString().split('T')[0];
        eventDateInput.min = today;
    }
    
    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Event...';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Save as draft functionality
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            document.getElementById('status').value = 'draft';
            form.submit();
        });
    }
    
    // Auto-save functionality (optional)
    const formInputs = form.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Save to localStorage as draft
            saveFormDraft();
        });
    });
    
    // Load saved draft
    loadFormDraft();
}

function validateForm() {
    const requiredFields = ['title', 'event_type', 'event_date', 'location', 'description'];
    let isValid = true;
    let firstErrorField = null;
    
    // Clear previous error states
    document.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('error');
    });
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && (!field.value || field.value.trim() === '')) {
            isValid = false;
            const formGroup = field.closest('.form-group');
            if (formGroup) {
                formGroup.classList.add('error');
                if (!firstErrorField) {
                    firstErrorField = field;
                }
            }
        }
    });
    
    // Date validation
    const eventDate = document.getElementById('event_date');
    if (eventDate && eventDate.value) {
        const selectedDate = new Date(eventDate.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            isValid = false;
            const formGroup = eventDate.closest('.form-group');
            if (formGroup) {
                formGroup.classList.add('error');
                if (!firstErrorField) {
                    firstErrorField = eventDate;
                }
            }
            showNotification('Event date cannot be in the past', 'error');
        }
    }
    
    // Focus on first error field
    if (!isValid && firstErrorField) {
        firstErrorField.focus();
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        showNotification('Please fill in all required fields correctly', 'error');
    }
    
    return isValid;
}

function saveFormDraft() {
    const formData = new FormData(document.getElementById('createEventForm'));
    const draftData = {};
    
    for (let [key, value] of formData.entries()) {
        draftData[key] = value;
    }
    
    localStorage.setItem('eventFormDraft', JSON.stringify(draftData));
}

function loadFormDraft() {
    const savedDraft = localStorage.getItem('eventFormDraft');
    if (savedDraft) {
        try {
            const draftData = JSON.parse(savedDraft);
            
            Object.entries(draftData).forEach(([key, value]) => {
                const field = document.getElementById(key);
                if (field && value) {
                    field.value = value;
                }
            });
            
            // Show notification about loaded draft
            showNotification('Draft loaded from previous session', 'info');
        } catch (error) {
            console.error('Error loading draft:', error);
        }
    }
}

function clearFormDraft() {
    localStorage.removeItem('eventFormDraft');
}

// Clear draft when form is successfully submitted
window.addEventListener('beforeunload', function() {
    // Only clear if form was submitted successfully
    if (document.querySelector('.btn[disabled]')) {
        clearFormDraft();
    }
});
</script>

<style>
/* Create Event Form Specific Styles */
.create-event-header {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

.create-event-form {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid rgba(74, 144, 226, 0.2);
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-header {
    margin-bottom: 25px;
}

.section-header h3 {
    color: #4A90E2;
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.section-header p {
    color: #666;
    font-size: 1rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    color: #333;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.required {
    color: #FF6B6B;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid rgba(74, 144, 226, 0.2);
    border-radius: 12px;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #4A90E2;
    background: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.form-group.error input,
.form-group.error select,
.form-group.error textarea {
    border-color: #FF6B6B;
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
}

.form-help {
    font-size: 0.85rem;
    color: #666;
    margin-top: 5px;
    font-style: italic;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(74, 144, 226, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .create-event-form {
        padding: 25px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Auto-dismiss flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
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

<?php require APPROOT . '/views/inc/components/footer.php'; ?>

</body>
</html>
