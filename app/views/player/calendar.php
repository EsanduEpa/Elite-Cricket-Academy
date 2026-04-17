<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_calendar.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

<div class="player-layout">
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
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Player'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/player/profile" class="profile-avatar" aria-label="Open player profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-alt"></i> My Session Calendar</h1>
                    <p>All your upcoming bookings in one view</p>
                </div>
                <div class="header-actions booking-shortcuts">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="booking-shortcut-btn">
                        <i class="fas fa-list"></i> My Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="booking-shortcut-btn">
                        <i class="fas fa-user-tie"></i> Coach
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="booking-shortcut-btn">
                        <i class="fas fa-dumbbell"></i> Trainer
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="booking-shortcut-btn">
                        <i class="fas fa-building"></i> Facilities
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="booking-shortcut-btn primary">
                        <i class="fas fa-calendar-alt"></i> Calendar
                    </a>
                </div>
            </div>
        </div>

        <div class="calendar-legend">
            <span class="calendar-legend-item"><span class="calendar-legend-swatch coach"></span> Coach Session</span>
            <span class="calendar-legend-item"><span class="calendar-legend-swatch trainer"></span> Trainer Session</span>
            <span class="calendar-legend-item"><span class="calendar-legend-swatch group"></span> Group Session</span>
            <span class="calendar-legend-item"><span class="calendar-legend-swatch program"></span> Assigned Program</span>
            <span class="calendar-legend-item"><span class="calendar-legend-swatch facility"></span> Facility Booking</span>
        </div>

        <div class="calendar-card">
            <div class="calendar-frame">
                <div id="player-calendar" data-events="<?php echo htmlspecialchars(json_encode($data['events'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"></div>
            </div>
        </div>

        <div id="cal-popover" class="calendar-popover">
            <button id="cal-popover-close" class="calendar-popover-close">&times;</button>
            <div id="cal-popover-content"></div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/player_calendar.js?v=<?php echo time(); ?>"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>