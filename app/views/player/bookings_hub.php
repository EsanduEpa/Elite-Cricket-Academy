<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_bookings_hub.css?v=<?php echo time(); ?>">

<div class="player-layout">
    <div class="player-sidebar" id="playerSidebar">
        <div class="sidebar-header">
            <div class="player-logo">
                <i class="fas fa-user-graduate"></i>
                <h3>Player Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training" class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/performance" class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/playerslots" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($data['player']['name'] ?? 'Player'); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($data['player']['membership_level'] ?? 'Standard'); ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn profile-logout-spacing"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-check"></i> Bookings</h1>
                    <p>Jump straight to the booking type you need and review your session reservations from one place.</p>
                </div>
                <div class="header-actions bookings-header-actions">
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="booking-header-btn coach">
                        <span class="booking-header-icon"><i class="fas fa-user-tie"></i></span>
                        <span class="booking-header-copy">
                            <strong>Coach</strong>
                            <span>Private sessions</span>
                        </span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="booking-header-btn trainer">
                        <span class="booking-header-icon"><i class="fas fa-dumbbell"></i></span>
                        <span class="booking-header-copy">
                            <strong>Trainer</strong>
                            <span>Fitness sessions</span>
                        </span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="booking-header-btn facility">
                        <span class="booking-header-icon"><i class="fas fa-building"></i></span>
                        <span class="booking-header-copy">
                            <strong>Facilities</strong>
                            <span>Reserve spaces</span>
                        </span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="booking-header-btn sessions">
                        <span class="booking-header-icon"><i class="fas fa-list-alt"></i></span>
                        <span class="booking-header-copy">
                            <strong>My Sessions</strong>
                            <span>Your bookings</span>
                        </span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="booking-header-btn sessions">
                        <span class="booking-header-icon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="booking-header-copy">
                            <strong>Calendar</strong>
                            <span>Booking calendar</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="bookings-shell">
            <section class="bookings-overview">
                <div class="bookings-overview-top">
                    <div>
                        <h2>Choose where you want to book next</h2>
                        <p>Use the header buttons just like the shopping page shortcuts. You can move directly to coach sessions, trainer sessions, facility reservations, or your own session list without using a separate navigation block below.</p>
                    </div>
                    <div class="bookings-overview-badge">
                        <i class="fas fa-bolt"></i>
                        <span>Quick booking access</span>
                    </div>
                </div>

                <div class="booking-summary-grid">
                    <div class="booking-summary-card coach">
                        <h3>Coach Sessions</h3>
                        <p>Work with academy coaches on technique, game awareness, and position-specific improvement.</p>
                        <a href="<?php echo URLROOT; ?>/playerslots/coach"><span>Go to coach bookings</span><i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="booking-summary-card trainer">
                        <h3>Trainer Sessions</h3>
                        <p>Reserve strength, conditioning, and performance-focused sessions with trainers.</p>
                        <a href="<?php echo URLROOT; ?>/playerslots/trainer"><span>Go to trainer bookings</span><i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="booking-summary-card facility">
                        <h3>Facility Access</h3>
                        <p>Book nets and other academy spaces when you need focused practice time.</p>
                        <a href="<?php echo URLROOT; ?>/playerslots/facilities"><span>Go to facilities</span><i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>