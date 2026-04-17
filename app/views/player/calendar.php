<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_calendar.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

<div class="player-layout">
    <?php $playerActivePage = 'bookings'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

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