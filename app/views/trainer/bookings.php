<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
<!-- Mobile-specific meta tags -->
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

    <!-- Trainer Layout -->
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
        <div class="trainer-sidebar" id="trainerSidebar">
            <div class="sidebar-header">
                <div class="trainer-logo">
                    <i class="fas fa-user-tie"></i>
                    <h3>Trainer Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedule & Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Workout Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                            <i class="fas fa-apple-alt"></i>
                            <span>Nutrition Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Profile Section -->
            <div class="trainer-profile">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-name"><?php echo $_SESSION['username'] ?? 'John Trainer'; ?></div>
                <div class="trainer-role">Fitness Trainer</div>
                <div class="profile-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                        <i class="fas fa-user-cog"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-calendar-check"></i> Session Bookings & Management</h1>
                        <p>Manage training sessions, bookings, and assign personalized plans to your trainees</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-training" onclick="addNewBooking()">
                            <i class="fas fa-plus"></i>Add New Session
                        </button>
                        <button class="btn btn-refresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            <div class="current-time"><?php echo date('H:i'); ?></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if(isset($_SESSION['flash_message'])): ?>
                <div class="flash-message <?php echo $_SESSION['flash_type']; ?>">
                    <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                </div>
            <?php endif; ?>

        <!-- Today's Sessions -->
        <div class="schedule-card">
            <div class="controls-bar">
                <div class="view-controls">
                    <button class="view-btn active" data-filter="today">
                        <i class="fas fa-calendar-day"></i> Today's Sessions
                    </button>
                    <span class="date-display"><?php echo date('M j, Y'); ?></span>
                </div>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-user"></i> Client & Session</th>
                            <th><i class="fas fa-map-marker-alt"></i> Location</th>
                            <th><i class="fas fa-chart-line"></i> Status</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="time-info">
                                <div class="time-display">
                                    <span class="time">6:00 AM</span>
                                    <span class="duration">1 hour</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Sarah Mitchell</span>
                                    <span class="session-type"><i class="fas fa-dumbbell"></i> Strength Training</span>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Gym A</span>
                                    <span class="room">Weight Room</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-active">
                                    <i class="fas fa-play-circle"></i> Active
                                </span>
                            </td>
                            <td class="actions-cell">
                                <div class="profile-actions">
                                    <button class="profile-action complete" onclick="markCompleted(1)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="profile-action view" onclick="viewNotes(1)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="time-info">
                                <div class="time-display">
                                    <span class="time">7:30 AM</span>
                                    <span class="duration">1 hour</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Mike Johnson</span>
                                    <span class="session-type"><i class="fas fa-heartbeat"></i> Cardio Training</span>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Cardio Zone</span>
                                    <span class="room">Treadmills & Bikes</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-upcoming">
                                    <i class="fas fa-clock"></i> Upcoming
                                </span>
                            </td>
                            <td class="actions-cell">
                                <div class="profile-actions">
                                    <button class="profile-action complete" onclick="markCompleted(2)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="profile-action view" onclick="viewNotes(2)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="time-info">
                                <div class="time-display">
                                    <span class="time">10:00 AM</span>
                                    <span class="duration">1 hour</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Emma Davis</span>
                                    <span class="session-type"><i class="fas fa-leaf"></i> Flexibility & Recovery</span>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Yoga Studio</span>
                                    <span class="room">Recovery Room</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-planned">
                                    <i class="fas fa-calendar-alt"></i> Planned
                                </span>
                            </td>
                            <td class="actions-cell">
                                <div class="profile-actions">
                                    <button class="profile-action complete" onclick="markCompleted(3)" title="Mark Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="profile-action view" onclick="viewNotes(3)" title="View Notes">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Past Sessions Section -->
        <div class="schedule-card">
            <div class="controls-bar">
                <div class="view-controls">
                    <button class="view-btn active" data-filter="completed">
                        <i class="fas fa-history"></i> Past Sessions
                    </button>
                    <button class="view-btn" data-filter="this-week">
                        <i class="fas fa-calendar-week"></i> This Week
                    </button>
                    <button class="view-btn" data-filter="this-month">
                        <i class="fas fa-calendar-alt"></i> This Month
                    </button>
                </div>
                <div class="search-controls">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="pastSessionsSearch" placeholder="Search past sessions..." />
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="dashboard-table" id="pastSessionsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date & Time</th>
                            <th><i class="fas fa-user"></i> Client Details</th>
                            <th><i class="fas fa-clipboard-list"></i> Session Details</th>
                            <th><i class="fas fa-map-marker-alt"></i> Location</th>
                            <th><i class="fas fa-star"></i> Performance</th>
                            <th><i class="fas fa-tasks"></i> Plan Assignments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample past sessions data -->
                        <tr class="session-row">
                            <td class="date-info">
                                <div class="date-display">
                                    <span class="date">Oct 18</span>
                                    <span class="year">2024</span>
                                    <span class="time">6:00 AM</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Sarah Mitchell</span>
                                    <span class="client-sport">Cricket Player</span>
                                    <span class="client-email">sarah.mitchell@email.com</span>
                                </div>
                            </td>
                            <td class="session-details">
                                <div class="session-info">
                                    <div class="session-title">
                                        <i class="fas fa-dumbbell"></i>
                                        <strong>Strength Training</strong>
                                    </div>
                                    <div class="session-description">
                                        <span>Duration: 1 hour</span>
                                        <span>Focus: Upper body strength, core stability</span>
                                        <span>Equipment: Dumbbells, resistance bands</span>
                                    </div>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Gym A</span>
                                    <span class="room">Weight Room</span>
                                </div>
                            </td>
                            <td class="performance-info">
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-text">Excellent</span>
                                </div>
                            </td>
                            <td class="actions-cell">
                                <div class="plan-actions">
                                    <button class="plan-btn workout" onclick="assignWorkoutPlan('sarah_mitchell')" title="Assign Workout Plan">
                                        <i class="fas fa-dumbbell"></i>
                                        <span>Workout</span>
                                    </button>
                                    <button class="plan-btn nutrition" onclick="assignNutritionPlan('sarah_mitchell')" title="Assign Nutrition Plan">
                                        <i class="fas fa-apple-alt"></i>
                                        <span>Nutrition</span>
                                    </button>
                                    <button class="plan-btn supplement" onclick="assignSupplementPlan('sarah_mitchell')" title="Assign Supplement Plan">
                                        <i class="fas fa-capsules"></i>
                                        <span>Supplements</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="session-row">
                            <td class="date-info">
                                <div class="date-display">
                                    <span class="date">Oct 17</span>
                                    <span class="year">2024</span>
                                    <span class="time">7:30 AM</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Mike Johnson</span>
                                    <span class="client-sport">Football Player</span>
                                    <span class="client-email">mike.johnson@email.com</span>
                                </div>
                            </td>
                            <td class="session-details">
                                <div class="session-info">
                                    <div class="session-title">
                                        <i class="fas fa-heartbeat"></i>
                                        <strong>Cardio Training</strong>
                                    </div>
                                    <div class="session-description">
                                        <span>Duration: 1.5 hours</span>
                                        <span>Focus: Endurance, cardiovascular fitness</span>
                                        <span>Equipment: Treadmill, cycling machine</span>
                                    </div>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Cardio Zone</span>
                                    <span class="room">Fitness Center</span>
                                </div>
                            </td>
                            <td class="performance-info">
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="rating-text">Outstanding</span>
                                </div>
                            </td>
                            <td class="actions-cell">
                                <div class="plan-actions">
                                    <button class="plan-btn workout" onclick="assignWorkoutPlan('mike_johnson')" title="Assign Workout Plan">
                                        <i class="fas fa-dumbbell"></i>
                                        <span>Workout</span>
                                    </button>
                                    <button class="plan-btn nutrition" onclick="assignNutritionPlan('mike_johnson')" title="Assign Nutrition Plan">
                                        <i class="fas fa-apple-alt"></i>
                                        <span>Nutrition</span>
                                    </button>
                                    <button class="plan-btn supplement" onclick="assignSupplementPlan('mike_johnson')" title="Assign Supplement Plan">
                                        <i class="fas fa-capsules"></i>
                                        <span>Supplements</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="session-row">
                            <td class="date-info">
                                <div class="date-display">
                                    <span class="date">Oct 16</span>
                                    <span class="year">2024</span>
                                    <span class="time">10:00 AM</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Emma Davis</span>
                                    <span class="client-sport">Tennis Player</span>
                                    <span class="client-email">emma.davis@email.com</span>
                                </div>
                            </td>
                            <td class="session-details">
                                <div class="session-info">
                                    <div class="session-title">
                                        <i class="fas fa-leaf"></i>
                                        <strong>Flexibility & Recovery</strong>
                                    </div>
                                    <div class="session-description">
                                        <span>Duration: 45 minutes</span>
                                        <span>Focus: Mobility, injury prevention</span>
                                        <span>Equipment: Yoga mats, foam rollers</span>
                                    </div>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Yoga Studio</span>
                                    <span class="room">Recovery Room</span>
                                </div>
                            </td>
                            <td class="performance-info">
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-text">Good</span>
                                </div>
                            </td>
                            <td class="actions-cell">
                                <div class="plan-actions">
                                    <button class="plan-btn workout" onclick="assignWorkoutPlan('emma_davis')" title="Assign Workout Plan">
                                        <i class="fas fa-dumbbell"></i>
                                        <span>Workout</span>
                                    </button>
                                    <button class="plan-btn nutrition" onclick="assignNutritionPlan('emma_davis')" title="Assign Nutrition Plan">
                                        <i class="fas fa-apple-alt"></i>
                                        <span>Nutrition</span>
                                    </button>
                                    <button class="plan-btn supplement" onclick="assignSupplementPlan('emma_davis')" title="Assign Supplement Plan">
                                        <i class="fas fa-capsules"></i>
                                        <span>Supplements</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="session-row">
                            <td class="date-info">
                                <div class="date-display">
                                    <span class="date">Oct 15</span>
                                    <span class="year">2024</span>
                                    <span class="time">2:00 PM</span>
                                </div>
                            </td>
                            <td class="client-info">
                                <div class="player-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="client-details">
                                    <span class="client-name">Alex Rodriguez</span>
                                    <span class="client-sport">Basketball Player</span>
                                    <span class="client-email">alex.rodriguez@email.com</span>
                                </div>
                            </td>
                            <td class="session-details">
                                <div class="session-info">
                                    <div class="session-title">
                                        <i class="fas fa-running"></i>
                                        <strong>Sports-Specific Training</strong>
                                    </div>
                                    <div class="session-description">
                                        <span>Duration: 2 hours</span>
                                        <span>Focus: Agility, sport-specific movements</span>
                                        <span>Equipment: Cones, agility ladder, balls</span>
                                    </div>
                                </div>
                            </td>
                            <td class="location-info">
                                <div class="location-display">
                                    <span class="venue">Field Area</span>
                                    <span class="room">Training Ground</span>
                                </div>
                            </td>
                            <td class="performance-info">
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-text">Very Good</span>
                                </div>
                            </td>
                            <td class="actions-cell">
                                <div class="plan-actions">
                                    <button class="plan-btn workout" onclick="assignWorkoutPlan('alex_rodriguez')" title="Assign Workout Plan">
                                        <i class="fas fa-dumbbell"></i>
                                        <span>Workout</span>
                                    </button>
                                    <button class="plan-btn nutrition" onclick="assignNutritionPlan('alex_rodriguez')" title="Assign Nutrition Plan">
                                        <i class="fas fa-apple-alt"></i>
                                        <span>Nutrition</span>
                                    </button>
                                    <button class="plan-btn supplement" onclick="assignSupplementPlan('alex_rodriguez')" title="Assign Supplement Plan">
                                        <i class="fas fa-capsules"></i>
                                        <span>Supplements</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ।

        </div>
    </div>
</div>

<!-- Assign Workout Plan Modal -->
<div id="assignWorkoutModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-dumbbell"></i>
            </div>
            <div class="header-text">
                <h3>Assign Workout Plan</h3>
                <p>Create a personalized workout plan for your trainee</p>
            </div>
            <button class="modal-close" onclick="closeModal('assignWorkoutModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form class="modal-form" onsubmit="submitWorkoutPlan(event)">
            <div class="modal-body">
                <div class="client-info-display" id="workoutClientInfo">
                    <!-- Client info will be populated here -->
                </div>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="workout_plan_name" class="form-label">
                            <i class="fas fa-tag"></i> Plan Name
                        </label>
                        <input type="text" id="workout_plan_name" name="plan_name" class="form-input" required 
                               placeholder="e.g., Strength Building Program">
                    </div>

                    <div class="form-group">
                        <label for="workout_focus" class="form-label">
                            <i class="fas fa-target"></i> Focus Area
                        </label>
                        <select id="workout_focus" name="focus" class="form-input" required>
                            <option value="">Select focus...</option>
                            <option value="strength">Strength Training</option>
                            <option value="cardio">Cardiovascular</option>
                            <option value="flexibility">Flexibility</option>
                            <option value="sports_specific">Sports-Specific</option>
                            <option value="rehabilitation">Rehabilitation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="workout_duration" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Duration (Weeks)
                        </label>
                        <input type="number" id="workout_duration" name="duration" class="form-input" required 
                               min="1" max="52" placeholder="e.g., 8 weeks">
                    </div>

                    <div class="form-group full-width">
                        <label for="workout_description" class="form-label">
                            <i class="fas fa-clipboard-list"></i> Workout Description
                        </label>
                        <textarea id="workout_description" name="description" class="form-input" rows="6" required 
                                placeholder="Describe the workout plan details, exercises, sets, reps, and progression..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('assignWorkoutModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Assign Workout Plan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Nutrition Plan Modal -->
<div id="assignNutritionModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-apple-alt"></i>
            </div>
            <div class="header-text">
                <h3>Assign Nutrition Plan</h3>
                <p>Create a personalized nutrition plan for your trainee</p>
            </div>
            <button class="modal-close" onclick="closeModal('assignNutritionModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form class="modal-form" onsubmit="submitNutritionPlan(event)">
            <div class="modal-body">
                <div class="client-info-display" id="nutritionClientInfo">
                    <!-- Client info will be populated here -->
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nutrition_type" class="form-label">
                            <i class="fas fa-utensils"></i> Plan Type
                        </label>
                        <select id="nutrition_type" name="plan_type" class="form-input" required>
                            <option value="">Select type...</option>
                            <option value="weight_loss">Weight Loss</option>
                            <option value="muscle_gain">Muscle Gain</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="performance">Performance</option>
                            <option value="recovery">Recovery</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="daily_calories" class="form-label">
                            <i class="fas fa-fire"></i> Daily Calories
                        </label>
                        <input type="number" id="daily_calories" name="daily_calories" class="form-input" required 
                               min="1000" max="5000" placeholder="e.g., 2500">
                    </div>

                    <div class="form-group">
                        <label for="nutrition_duration" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Duration (Days)
                        </label>
                        <input type="number" id="nutrition_duration" name="duration" class="form-input" required 
                               min="1" max="365" placeholder="e.g., 30 days">
                    </div>

                    <div class="form-group full-width">
                        <label for="diet_details" class="form-label">
                            <i class="fas fa-clipboard-list"></i> Diet Details & Instructions
                        </label>
                        <textarea id="diet_details" name="diet_details" class="form-input" rows="6" required 
                                placeholder="Provide detailed nutrition plan including meals, portions, timing, special instructions..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('assignNutritionModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Assign Nutrition Plan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Supplement Plan Modal -->
<div id="assignSupplementModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-capsules"></i>
            </div>
            <div class="header-text">
                <h3>Assign Supplement Plan</h3>
                <p>Create a personalized supplement plan for your trainee</p>
            </div>
            <button class="modal-close" onclick="closeModal('assignSupplementModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form class="modal-form" onsubmit="submitSupplementPlan(event)">
            <div class="modal-body">
                <div class="client-info-display" id="supplementClientInfo">
                    <!-- Client info will be populated here -->
                </div>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="supplement_name" class="form-label">
                            <i class="fas fa-pills"></i> Supplement Name
                        </label>
                        <input type="text" id="supplement_name" name="supplement_name" class="form-input" required 
                               placeholder="e.g., Whey Protein, Creatine, Multivitamin">
                    </div>

                    <div class="form-group">
                        <label for="supplement_type" class="form-label">
                            <i class="fas fa-tags"></i> Type
                        </label>
                        <select id="supplement_type" name="supplement_type" class="form-input" required>
                            <option value="">Select type...</option>
                            <option value="protein">Protein</option>
                            <option value="vitamin">Vitamin</option>
                            <option value="mineral">Mineral</option>
                            <option value="pre_workout">Pre-Workout</option>
                            <option value="post_workout">Post-Workout</option>
                            <option value="recovery">Recovery</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dosage" class="form-label">
                            <i class="fas fa-prescription-bottle"></i> Dosage
                        </label>
                        <input type="text" id="dosage" name="dosage" class="form-input" required 
                               placeholder="e.g., 25g daily, 2 capsules">
                    </div>

                    <div class="form-group">
                        <label for="supplement_duration" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Duration (Days)
                        </label>
                        <input type="number" id="supplement_duration" name="duration" class="form-input" required 
                               min="1" max="365" placeholder="e.g., 60 days">
                    </div>

                    <div class="form-group full-width">
                        <label for="instructions" class="form-label">
                            <i class="fas fa-clipboard-list"></i> Instructions & Notes
                        </label>
                        <textarea id="instructions" name="instructions" class="form-input" rows="6" required 
                                placeholder="Provide detailed instructions for timing, usage, precautions, and any special notes..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('assignSupplementModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Assign Supplement Plan
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize filter functionality
    initializeFilters();
    
    // Initialize modal handlers
    initializeModals();
});

// Search Functionality
function initializeSearch() {
    const searchInput = document.getElementById('pastSessionsSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#pastSessionsTable tbody .session-row');
            
            tableRows.forEach(row => {
                const clientName = row.querySelector('.client-name')?.textContent.toLowerCase() || '';
                const sessionTitle = row.querySelector('.session-title strong')?.textContent.toLowerCase() || '';
                const sessionDescription = row.querySelector('.session-description')?.textContent.toLowerCase() || '';
                
                const matches = clientName.includes(searchTerm) || 
                               sessionTitle.includes(searchTerm) || 
                               sessionDescription.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
            });
        });
    }
}

// Filter Functionality
function initializeFilters() {
    const filterButtons = document.querySelectorAll('.view-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter based on the selected option
            const filter = this.getAttribute('data-filter');
            // Add filter logic here based on your needs
        });
    });
}

// Modal Functions
function initializeModals() {
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input, select, textarea');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form if it exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Plan Assignment Functions
function assignWorkoutPlan(clientId) {
    // Get client info (in real implementation, this would come from database)
    const clientInfo = getClientInfo(clientId);
    
    // Populate client info in modal
    document.getElementById('workoutClientInfo').innerHTML = `
        <div class="client-info-card">
            <div class="client-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="client-details">
                <h4>${clientInfo.name}</h4>
                <p>${clientInfo.sport} • ${clientInfo.email}</p>
                <span class="info-badge">Assigning Workout Plan</span>
            </div>
        </div>
    `;
    
    openModal('assignWorkoutModal');
}

function assignNutritionPlan(clientId) {
    // Get client info
    const clientInfo = getClientInfo(clientId);
    
    // Populate client info in modal
    document.getElementById('nutritionClientInfo').innerHTML = `
        <div class="client-info-card">
            <div class="client-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="client-details">
                <h4>${clientInfo.name}</h4>
                <p>${clientInfo.sport} • ${clientInfo.email}</p>
                <span class="info-badge">Assigning Nutrition Plan</span>
            </div>
        </div>
    `;
    
    openModal('assignNutritionModal');
}

function assignSupplementPlan(clientId) {
    // Get client info
    const clientInfo = getClientInfo(clientId);
    
    // Populate client info in modal
    document.getElementById('supplementClientInfo').innerHTML = `
        <div class="client-info-card">
            <div class="client-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="client-details">
                <h4>${clientInfo.name}</h4>
                <p>${clientInfo.sport} • ${clientInfo.email}</p>
                <span class="info-badge">Assigning Supplement Plan</span>
            </div>
        </div>
    `;
    
    openModal('assignSupplementModal');
}

// Helper function to get client info (mock data)
function getClientInfo(clientId) {
    const clients = {
        'sarah_mitchell': { name: 'Sarah Mitchell', sport: 'Cricket Player', email: 'sarah.mitchell@email.com' },
        'mike_johnson': { name: 'Mike Johnson', sport: 'Football Player', email: 'mike.johnson@email.com' },
        'emma_davis': { name: 'Emma Davis', sport: 'Tennis Player', email: 'emma.davis@email.com' },
        'alex_rodriguez': { name: 'Alex Rodriguez', sport: 'Basketball Player', email: 'alex.rodriguez@email.com' }
    };
    
    return clients[clientId] || { name: 'Unknown Client', sport: 'Athlete', email: 'unknown@email.com' };
}

// Form submission handlers
function submitWorkoutPlan(event) {
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(event.target);
    const planData = {
        plan_name: formData.get('plan_name'),
        focus: formData.get('focus'),
        duration: formData.get('duration'),
        description: formData.get('description')
    };
    
    // In real implementation, send data to server
    console.log('Workout Plan Data:', planData);
    
    // Show success message
    alert('Workout plan assigned successfully!');
    
    // Close modal
    closeModal('assignWorkoutModal');
}

function submitNutritionPlan(event) {
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(event.target);
    const planData = {
        plan_type: formData.get('plan_type'),
        daily_calories: formData.get('daily_calories'),
        duration: formData.get('duration'),
        diet_details: formData.get('diet_details')
    };
    
    // In real implementation, send data to server
    console.log('Nutrition Plan Data:', planData);
    
    // Show success message
    alert('Nutrition plan assigned successfully!');
    
    // Close modal
    closeModal('assignNutritionModal');
}

function submitSupplementPlan(event) {
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(event.target);
    const planData = {
        supplement_name: formData.get('supplement_name'),
        supplement_type: formData.get('supplement_type'),
        dosage: formData.get('dosage'),
        duration: formData.get('duration'),
        instructions: formData.get('instructions')
    };
    
    // In real implementation, send data to server
    console.log('Supplement Plan Data:', planData);
    
    // Show success message
    alert('Supplement plan assigned successfully!');
    
    // Close modal
    closeModal('assignSupplementModal');
}

// Existing session management functions
function addNewBooking() {
    alert('Add New Booking functionality - would open booking modal');
}

function markCompleted(bookingId) {
    if(confirm('Mark this session as completed?')) {
        // In real implementation, update database
        alert(`Session ${bookingId} marked as completed!`);
        
        // Update UI to show completed status
        // location.reload(); // or update DOM directly
    }
}

function viewNotes(bookingId) {
    alert(`View session notes for booking ${bookingId} - functionality coming soon!`);
}

function editBooking(bookingId) {
    alert(`Edit booking ${bookingId} - functionality coming soon!`);
}

function cancelBooking(bookingId) {
    if(confirm('Are you sure you want to cancel this booking?')) {
        alert(`Cancel booking ${bookingId} - functionality coming soon!`);
    }
}

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    }
});
</script>
</body>
</html>