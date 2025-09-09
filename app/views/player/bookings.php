<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/bookings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>
    
    <!-- Player Dashboard Layout -->
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
        <div class="player-sidebar" id="playerSidebar">
            <div class="sidebar-header">
                <div class="player-logo">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Player Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link active">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                            <?php if(!empty($data['upcomingBookings'])): ?>
                                <span class="badge"><?php echo count($data['upcomingBookings']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Player Profile -->
            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name'] ?? 'Player Name'; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level'] ?? 'Member'; ?> Member</div>
                </div>
                <div class="logout-btn">
                    <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Booking Header -->
            <div class="booking-header">
                <div class="header-content">
                    <h1><i class="fas fa-calendar-check"></i> My Bookings</h1>
                    <p>Book appointments with our professional physio and trainers</p>
                </div>
                <div class="header-actions">
                    <button class="new-booking-btn" onclick="openBookingModal()">
                        <i class="fas fa-plus"></i> New Booking
                    </button>
                    <div class="view-toggle">
                        <button class="toggle-btn active" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                        <button class="toggle-btn" data-view="calendar">
                            <i class="fas fa-calendar"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Booking Filters -->
            <div class="booking-filters">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">All Bookings</button>
                    <button class="filter-btn" data-filter="upcoming">Upcoming</button>
                    <button class="filter-btn" data-filter="completed">Completed</button>
                    <button class="filter-btn" data-filter="cancelled">Cancelled</button>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search bookings..." id="searchBookings">
                </div>
            </div>

            <!-- Booking Views -->
            <div class="booking-views">
                <!-- List View -->
                <div class="booking-list-view active" id="listView">
                    <div class="bookings-grid" id="bookingsGrid">
                        <!-- Upcoming Bookings -->
                        <div class="booking-section">
                            <h2><i class="fas fa-clock"></i> Upcoming Bookings</h2>
                            <div class="booking-cards" id="upcomingBookings">
                                <!-- Sample Upcoming Booking -->
                                <div class="booking-card upcoming" data-status="upcoming">
                                    <div class="booking-header">
                                        <div class="booking-type">
                                            <i class="fas fa-heartbeat"></i>
                                            <span>Physio Session</span>
                                        </div>
                                        <div class="booking-status upcoming">
                                            <i class="fas fa-clock"></i> Upcoming
                                        </div>
                                    </div>
                                    <div class="booking-details">
                                        <div class="booking-info">
                                            <div class="info-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>December 15, 2024</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-clock"></i>
                                                <span>2:00 PM - 3:00 PM</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-md"></i>
                                                <span>Dr. Sarah Wilson</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-stethoscope"></i>
                                                <span>Injury Recovery Assessment</span>
                                            </div>
                                        </div>
                                        <div class="booking-notes">
                                            <strong>Notes:</strong> Shoulder pain after bowling session
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <button class="action-btn reschedule" onclick="rescheduleBooking(1)">
                                            <i class="fas fa-calendar-alt"></i> Reschedule
                                        </button>
                                        <button class="action-btn cancel" onclick="cancelBooking(1)">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button class="action-btn details" onclick="viewBookingDetails(1)">
                                            <i class="fas fa-eye"></i> Details
                                        </button>
                                    </div>
                                </div>

                                <!-- Another Sample Booking -->
                                <div class="booking-card upcoming" data-status="upcoming">
                                    <div class="booking-header">
                                        <div class="booking-type">
                                            <i class="fas fa-dumbbell"></i>
                                            <span>Fitness Assessment</span>
                                        </div>
                                        <div class="booking-status upcoming">
                                            <i class="fas fa-clock"></i> Upcoming
                                        </div>
                                    </div>
                                    <div class="booking-details">
                                        <div class="booking-info">
                                            <div class="info-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>December 18, 2024</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-clock"></i>
                                                <span>10:00 AM - 11:30 AM</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-md"></i>
                                                <span>Coach Mike Johnson</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-chart-line"></i>
                                                <span>Monthly Fitness Test</span>
                                            </div>
                                        </div>
                                        <div class="booking-notes">
                                            <strong>Notes:</strong> Standard monthly fitness evaluation
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <button class="action-btn reschedule" onclick="rescheduleBooking(2)">
                                            <i class="fas fa-calendar-alt"></i> Reschedule
                                        </button>
                                        <button class="action-btn cancel" onclick="cancelBooking(2)">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button class="action-btn details" onclick="viewBookingDetails(2)">
                                            <i class="fas fa-eye"></i> Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Past Bookings -->
                        <div class="booking-section">
                            <h2><i class="fas fa-history"></i> Past Bookings</h2>
                            <div class="booking-cards" id="pastBookings">
                                <!-- Sample Past Booking -->
                                <div class="booking-card completed" data-status="completed">
                                    <div class="booking-header">
                                        <div class="booking-type">
                                            <i class="fas fa-heartbeat"></i>
                                            <span>Physio Session</span>
                                        </div>
                                        <div class="booking-status completed">
                                            <i class="fas fa-check"></i> Completed
                                        </div>
                                    </div>
                                    <div class="booking-details">
                                        <div class="booking-info">
                                            <div class="info-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>December 8, 2024</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-clock"></i>
                                                <span>3:00 PM - 4:00 PM</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-md"></i>
                                                <span>Dr. Sarah Wilson</span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-stethoscope"></i>
                                                <span>Knee Injury Check</span>
                                            </div>
                                        </div>
                                        <div class="session-feedback">
                                            <strong>Session Notes:</strong> Knee showing good recovery progress. Continue with recommended exercises.
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <button class="action-btn rebook" onclick="rebookSession(3)">
                                            <i class="fas fa-redo"></i> Book Again
                                        </button>
                                        <button class="action-btn details" onclick="viewBookingDetails(3)">
                                            <i class="fas fa-eye"></i> Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="booking-calendar-view" id="calendarView">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <div class="calendar-navigation">
                                <button class="nav-btn" id="prevMonth">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h3 id="currentMonth">December 2024</h3>
                                <button class="nav-btn" id="nextMonth">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-legend">
                                <div class="legend-item">
                                    <div class="legend-color upcoming"></div>
                                    <span>Upcoming</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color completed"></div>
                                    <span>Completed</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color cancelled"></div>
                                    <span>Cancelled</span>
                                </div>
                            </div>
                        </div>
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Booking Modal -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-plus"></i> Book New Appointment</h2>
                <button class="close-btn" onclick="closeBookingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="bookingForm" class="booking-form">
                    <!-- Step 1: Service Selection -->
                    <div class="form-step active" id="step1">
                        <h3>Select Service Type</h3>
                        <div class="service-options">
                            <div class="service-card" data-service="physio">
                                <div class="service-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="service-info">
                                    <h4>Physio Session</h4>
                                    <p>Injury assessment, recovery planning, and treatment</p>
                                    <span class="duration">60 minutes</span>
                                </div>
                            </div>
                            <div class="service-card" data-service="fitness">
                                <div class="service-icon">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                                <div class="service-info">
                                    <h4>Fitness Assessment</h4>
                                    <p>Comprehensive fitness evaluation and planning</p>
                                    <span class="duration">90 minutes</span>
                                </div>
                            </div>
                            <div class="service-card" data-service="consultation">
                                <div class="service-icon">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="service-info">
                                    <h4>General Consultation</h4>
                                    <p>Health consultation and advice</p>
                                    <span class="duration">30 minutes</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Date & Time Selection -->
                    <div class="form-step" id="step2">
                        <h3>Select Date & Time</h3>
                        <div class="datetime-selection">
                            <div class="date-picker-container">
                                <label>Select Date:</label>
                                <input type="text" id="bookingDate" class="date-input" placeholder="Choose date..." readonly>
                            </div>
                            <div class="time-slots-container">
                                <label>Available Time Slots:</label>
                                <div class="time-slots" id="timeSlots">
                                    <!-- Time slots will be populated based on selected date -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Details & Notes -->
                    <div class="form-step" id="step3">
                        <h3>Appointment Details</h3>
                        <div class="form-fields">
                            <div class="field-group">
                                <label for="appointmentReason">Reason for Appointment:</label>
                                <select id="appointmentReason" name="reason" required>
                                    <option value="">Select reason...</option>
                                    <option value="injury_assessment">Injury Assessment</option>
                                    <option value="recovery_session">Recovery Session</option>
                                    <option value="fitness_test">Fitness Test</option>
                                    <option value="routine_checkup">Routine Checkup</option>
                                    <option value="pain_management">Pain Management</option>
                                    <option value="performance_optimization">Performance Optimization</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label for="appointmentNotes">Additional Notes (Optional):</label>
                                <textarea id="appointmentNotes" name="notes" rows="4" 
                                    placeholder="Please provide any additional details about your condition, specific areas of concern, or any relevant information for the appointment..."></textarea>
                            </div>
                            <div class="field-group">
                                <label for="urgencyLevel">Urgency Level:</label>
                                <select id="urgencyLevel" name="urgency">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Confirmation -->
                    <div class="form-step" id="step4">
                        <h3>Confirm Your Booking</h3>
                        <div class="booking-summary">
                            <div class="summary-item">
                                <strong>Service:</strong>
                                <span id="summaryService">-</span>
                            </div>
                            <div class="summary-item">
                                <strong>Date:</strong>
                                <span id="summaryDate">-</span>
                            </div>
                            <div class="summary-item">
                                <strong>Time:</strong>
                                <span id="summaryTime">-</span>
                            </div>
                            <div class="summary-item">
                                <strong>Duration:</strong>
                                <span id="summaryDuration">-</span>
                            </div>
                            <div class="summary-item">
                                <strong>Practitioner:</strong>
                                <span id="summaryPractitioner">Will be assigned</span>
                            </div>
                            <div class="summary-item">
                                <strong>Reason:</strong>
                                <span id="summaryReason">-</span>
                            </div>
                        </div>
                        <div class="booking-terms">
                            <label class="checkbox-label">
                                <input type="checkbox" id="agreeTerms" required>
                                <span class="checkmark"></span>
                                I agree to the <a href="#" onclick="showTerms()">booking terms and conditions</a>
                            </label>
                        </div>
                    </div>

                    <!-- Form Navigation -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn prev" id="prevStep" onclick="previousStep()" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="nav-btn next" id="nextStep" onclick="nextStep()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="submit-btn" id="submitBooking" style="display: none;">
                            <i class="fas fa-check"></i> Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Booking Details</h2>
                <button class="close-btn" onclick="closeDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div id="bookingDetailsContent">
                    <!-- Details will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/bookings.js"></script>
</body>
</html>
