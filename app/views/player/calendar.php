<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

<div class="player-layout">
    <!-- Sidebar -->
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
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo $data['player']['name'] ?? 'Player'; ?></div>
            <div class="profile-role"><?php echo $data['player']['membership_level'] ?? 'Standard'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-alt"></i> My Session Calendar</h1>
                    <p>All your upcoming bookings in one view</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="btn btn-training">
                        <i class="fas fa-list"></i> My Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="btn btn-training">
                        <i class="fas fa-user-tie"></i> Coach
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="btn btn-training">
                        <i class="fas fa-dumbbell"></i> Trainer
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="btn btn-training">
                        <i class="fas fa-building"></i> Facilities
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="btn btn-performance">
                        <i class="fas fa-calendar-alt"></i> Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div style="display:flex;gap:20px;flex-wrap:wrap;padding:16px 20px;background:#fff;border-radius:10px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <span style="display:flex;align-items:center;gap:6px;font-size:13px;"><span style="width:14px;height:14px;border-radius:3px;background:#4A90E2;display:inline-block;"></span> Coach Session</span>
            <span style="display:flex;align-items:center;gap:6px;font-size:13px;"><span style="width:14px;height:14px;border-radius:3px;background:#27ae60;display:inline-block;"></span> Trainer Session</span>
            <span style="display:flex;align-items:center;gap:6px;font-size:13px;"><span style="width:14px;height:14px;border-radius:3px;background:#9b59b6;display:inline-block;"></span> Group Session</span>
            <span style="display:flex;align-items:center;gap:6px;font-size:13px;"><span style="width:14px;height:14px;border-radius:3px;background:#7c3aed;display:inline-block;"></span> Assigned Program</span>
            <span style="display:flex;align-items:center;gap:6px;font-size:13px;"><span style="width:14px;height:14px;border-radius:3px;background:#e67e22;display:inline-block;"></span> Facility Booking</span>
        </div>

        <!-- Calendar -->
        <div style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div id="player-calendar"></div>
        </div>

        <!-- Event detail popover -->
        <div id="cal-popover" style="display:none;position:fixed;background:#fff;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,0.18);padding:16px 20px;z-index:9500;min-width:240px;max-width:320px;">
            <button onclick="document.getElementById('cal-popover').style.display='none'" style="position:absolute;top:8px;right:12px;background:none;border:none;font-size:18px;cursor:pointer;color:#999;">&times;</button>
            <div id="cal-popover-content"></div>
        </div>
    </div>
</div>

<script>window.URLROOT_FACILITY = '<?php echo URLROOT; ?>';</script>
<script>
window.calendarEvents = <?php echo json_encode($data['events'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calEl = document.getElementById('player-calendar');
    const cal = new FullCalendar.Calendar(calEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,listWeek'
        },
        events: window.calendarEvents || [],
        eventClick: function(info) {
            const e = info.event;
            const props = e.extendedProps;
            const start = e.start ? e.start.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '';
            const end   = e.end   ? e.end.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '';
            document.getElementById('cal-popover-content').innerHTML = `
                <h4 style="margin:0 0 8px;color:#2c3e50;font-size:14px;">${e.title}</h4>
                <p style="margin:0 0 4px;font-size:13px;color:#555;"><i class="fas fa-clock"></i> ${start}${end ? ' – ' + end : ''}</p>
                <p style="margin:0;font-size:13px;"><span style="background:${e.backgroundColor};color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;">${props.type}</span>
                <span style="margin-left:6px;color:#888;font-size:12px;">${props.status}</span></p>
            `;
            const pop = document.getElementById('cal-popover');
            pop.style.display = 'block';
            const rect = info.el.getBoundingClientRect();
            pop.style.top  = Math.min(rect.bottom + 8, window.innerHeight - 160) + 'px';
            pop.style.left = Math.min(rect.left, window.innerWidth - 340) + 'px';
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        },
        height: 'auto',
        nowIndicator: true,
    });
    cal.render();

    // Close popover on outside click
    document.addEventListener('click', function(e) {
        const pop = document.getElementById('cal-popover');
        if (pop && !pop.contains(e.target)) pop.style.display = 'none';
    });
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
