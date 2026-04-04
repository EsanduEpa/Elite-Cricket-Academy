<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/bookings.css?v=<?php echo time(); ?>">

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
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link"><i class="fas fa-calendar"></i><span>Bookings</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo $data['player']['name'] ?? 'Player'; ?></div>
            <div class="profile-role"><?php echo $data['player']['membership_level'] ?? 'Regular'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-check"></i> My Bookings</h1>
                    <p>Manage your sessions, view enrollments, and book new training.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="btn btn-training">
                        <i class="fas fa-user-tie"></i> Book Coach Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="btn btn-performance">
                        <i class="fas fa-dumbbell"></i> Book Trainer Session
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <?php
        $enrolled     = $data['enrolled']      ?? [];
        $coachAppts   = $data['coachAppts']    ?? [];
        $trainerAppts = $data['trainerAppts']  ?? [];
        $facilities   = $data['facilities']    ?? [];
        $total        = $data['totalCount']    ?? 0;

        // Count sessions happening this week
        $weekEnd = date('Y-m-d', strtotime('+7 days'));
        $thisWeek = count(array_filter($data['allBookings'] ?? [], fn($b) => $b->date <= $weekEnd));
        ?>
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-value"><?php echo $total; ?></div>
                <div class="stat-label">Total Upcoming</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-value"><?php echo $thisWeek; ?></div>
                <div class="stat-label">This Week</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?php echo count($enrolled); ?></div>
                <div class="stat-label">Group Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                <div class="stat-value"><?php echo count($coachAppts) + count($trainerAppts); ?></div>
                <div class="stat-label">Private Appointments</div>
            </div>
        </div>

        <!-- ===== ENROLLED GROUP SESSIONS ===== -->
        <div class="schedule-card" style="margin-bottom:24px;">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-users"></i> Enrolled Group Sessions</h2>
                    <span style="font-size:13px;color:black;">Sessions claimed by coaches &amp; trainers </span>
                </div>
                <div class="header-actions">
                    <!-- <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="action-btn btn-sm">
                        <i class="fas fa-plus"></i> Browse Coach Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="action-btn btn-sm" style="background:#27ae60;">
                        <i class="fas fa-plus"></i> Browse Trainer Sessions
                    </a> --> 
                </div>
            </div>
            <div class="card-content">
                <?php if (!empty($enrolled)): ?>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-clipboard-list"></i> Session</th>
                            <th><i class="fas fa-user-tie"></i> Coach / Trainer</th>
                            <th><i class="fas fa-calendar"></i> Date &amp; Time</th>
                            <th><i class="fas fa-map-marker-alt"></i> Location</th>
                            <th><i class="fas fa-circle"></i> Status</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolled as $b):
                            $status = strtolower($b->Status);
                            $statusClass = match($status) {
                                'enrolled'  => 'status-active',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                default     => 'status-upcoming',
                            };
                            $statusLabel = match($status) {
                                'enrolled'  => '<i class="fas fa-check-circle"></i> Enrolled',
                                'completed' => '<i class="fas fa-flag-checkered"></i> Completed',
                                'cancelled' => '<i class="fas fa-times-circle"></i> Cancelled',
                                default     => '<i class="fas fa-clock"></i> ' . ucfirst($status),
                            };
                        ?>
                        <tr>
                            <td>
                                <div class="table-cell-title">
                                    <i class="fas fa-users" style="color:#9b59b6;"></i>
                                    <?php echo htmlspecialchars($b->reason ?? 'Training Session'); ?>
                                </div>
                                <span class="table-badge" style="background:#9b59b620;color:#9b59b6;">Group Session</span>
                            </td>
                            <td>
                                <div class="table-cell-primary"><?php echo htmlspecialchars($b->practitioner_name ?? '—'); ?></div>
                            </td>
                            <td>
                                <div class="table-cell-primary"><?php echo date('M d, Y', strtotime($b->date)); ?></div>
                                <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash; <?php echo date('g:i A', strtotime($b->EndTime)); ?></div>
                            </td>
                            <td><div class="table-cell-primary">—</div></td>
                            <td style="text-align:center;">
                                <span class="table-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($status === 'enrolled'): ?>
                                    <button class="action-btn btn-sm btn-danger" onclick="confirmCancel(<?php echo $b->id; ?>, 'session')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php else: ?>
                                    <span style="color:#aaa;font-size:13px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center;padding:40px 20px;color:#666;">
                    <i class="fas fa-calendar-times" style="font-size:3em;opacity:0.3;display:block;margin-bottom:12px;"></i>
                    <p style="margin:0 0 16px;">You have not enrolled in any group sessions yet.</p>
                    <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="action-btn" >
                        <i class="fas fa-user-tie"></i> Browse Coach Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="action-btn" style="background:#27ae60;">
                        <i class="fas fa-dumbbell"></i> Browse Trainer Sessions
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== PRIVATE COACH APPOINTMENTS ===== -->
        




        <div class="schedule-card" style="margin-bottom:24px;">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-user-tie"></i> Private Coach Appointments</h2>
                </div>
            </div>
            <div class="card-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Coach</th>
                            <th>Purpose</th>
                            <th>Date &amp; Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php foreach ($coachAppts as $b):
                            $status = strtolower($b->Status);
                            $statusClass = ($status === 'scheduled' || $status === 'confirmed') ? 'status-active' : 'status-upcoming';
                        ?>
                        <tr>
                            <td>
                                <div class="table-cell-primary"><i class="fas fa-user-tie" style="color:#4A90E2;"></i> <?php echo htmlspecialchars($b->practitioner_name ?? '—'); ?></div>
                                <div class="table-cell-secondary">Cricket Coach</div>
                            </td>
                            <td><div class="table-cell-primary"><?php echo htmlspecialchars($b->reason ?? 'Coaching Session'); ?></div></td>
                            <td>
                                <div class="table-cell-primary"><?php echo date('M d, Y', strtotime($b->date)); ?></div>
                                <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash; <?php echo date('g:i A', strtotime($b->EndTime)); ?></div>
                            </td>
                            <td style="text-align:center;">
                                <span class="table-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td style="text-align:center;">
                                <?php if (in_array($status, ['scheduled', 'confirmed'])): ?>
                                    <button class="action-btn btn-sm btn-danger" onclick="confirmCancel(<?php echo $b->id; ?>, 'coach')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php else: ?>
                                    <span style="color:#aaa;font-size:13px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ===== PRIVATE TRAINER APPOINTMENTS ===== -->
        <?php if (!empty($trainerAppts)): ?>
        <div class="schedule-card" style="margin-bottom:24px;">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-dumbbell"></i> Private Trainer Appointments</h2>
                </div>
            </div>
            <div class="card-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Trainer</th>
                            <th>Purpose</th>
                            <th>Date &amp; Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainerAppts as $b):
                            $status = strtolower($b->Status);
                            $statusClass = ($status === 'scheduled' || $status === 'confirmed') ? 'status-active' : 'status-upcoming';
                        ?>
                        <tr>
                            <td>
                                <div class="table-cell-primary"><i class="fas fa-dumbbell" style="color:#27ae60;"></i> <?php echo htmlspecialchars($b->practitioner_name ?? '—'); ?></div>
                                <div class="table-cell-secondary">Fitness Trainer</div>
                            </td>
                            <td><div class="table-cell-primary"><?php echo htmlspecialchars($b->reason ?? 'Training Session'); ?></div></td>
                            <td>
                                <div class="table-cell-primary"><?php echo date('M d, Y', strtotime($b->date)); ?></div>
                                <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash; <?php echo date('g:i A', strtotime($b->EndTime)); ?></div>
                            </td>
                            <td style="text-align:center;">
                                <span class="table-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td style="text-align:center;">
                                <?php if (in_array($status, ['scheduled', 'confirmed'])): ?>
                                    <button class="action-btn btn-sm btn-danger" onclick="confirmCancel(<?php echo $b->id; ?>, 'trainer')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php else: ?>
                                    <span style="color:#aaa;font-size:13px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ===== FACILITY BOOKINGS ===== -->
        <?php if (!empty($facilities)): ?>
        <div class="schedule-card" style="margin-bottom:24px;">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-building"></i> Facility Bookings</h2>
                </div>
            </div>
            <div class="card-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Date &amp; Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facilities as $b):
                            $status = strtolower($b->Status);
                        ?>
                        <tr>
                            <td>
                                <div class="table-cell-primary"><i class="fas fa-building" style="color:#e67e22;"></i> <?php echo htmlspecialchars($b->reason ?? 'Facility'); ?></div>
                            </td>
                            <td>
                                <div class="table-cell-primary"><?php echo date('M d, Y', strtotime($b->date)); ?></div>
                                <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($b->StartTime)); ?> &ndash; <?php echo date('g:i A', strtotime($b->EndTime)); ?></div>
                            </td>
                            <td style="text-align:center;">
                                <span class="table-badge status-upcoming"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td style="text-align:center;"><span style="color:#aaa;font-size:13px;">—</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Empty state when truly nothing booked -->
        <?php if ($total === 0): ?>
        <div class="schedule-card">
            <div class="card-content" style="text-align:center;padding:60px 20px;">
                <i class="fas fa-calendar-plus" style="font-size:4em;opacity:0.2;display:block;margin-bottom:16px;color:#4A90E2;"></i>
                <h3 style="color:#2c3e50;margin:0 0 8px;">No Bookings Yet</h3>
                <p style="color:#666;margin:0 0 24px;">Browse available sessions below and get started with your training.</p>
                <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="action-btn" style="margin-right:10px;">
                    <i class="fas fa-user-tie"></i> Coach Sessions
                </a>
                <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="action-btn" style="background:#27ae60;">
                    <i class="fas fa-dumbbell"></i> Trainer Sessions
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:32px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="font-size:48px;color:#e74c3c;margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i></div>
        <h3 style="margin:0 0 10px;color:#2c3e50;">Cancel Booking?</h3>
        <p style="color:#666;margin:0 0 8px;">This action cannot be undone.</p>
        <p style="color:#888;font-size:13px;margin:0 0 24px;">
            Cancellations must be made <strong>at least 24 hours</strong> before the session.<br>
            Maximum <strong>3 cancellations per month</strong> allowed.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="closeCancelModal()" style="padding:10px 24px;border:2px solid #ddd;background:#fff;border-radius:8px;cursor:pointer;font-size:14px;">Keep Booking</button>
            <button id="confirmCancelBtn" onclick="doCancel()" style="padding:10px 24px;background:#e74c3c;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;">Yes, Cancel</button>
        </div>
    </div>
</div>

<script>window.URLROOT_FACILITY = '<?php echo URLROOT; ?>';</script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>

<script>
const CANCEL_URL = '<?php echo URLROOT; ?>/player/cancel_booking';
let _cancelId = null, _cancelType = null;

function confirmCancel(id, type) {
    _cancelId   = id;
    _cancelType = type;
    const modal = document.getElementById('cancelModal');
    modal.style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
    _cancelId = null; _cancelType = null;
}

function doCancel() {
    const btn = document.getElementById('confirmCancelBtn');
    btn.disabled    = true;
    btn.textContent = 'Cancelling…';

    fetch(CANCEL_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'booking_id=' + _cancelId + '&booking_type=' + _cancelType
    })
    .then(r => r.json())
    .then(res => {
        closeCancelModal();
        notify(res.message || (res.success ? 'Cancelled' : 'Failed'), res.success ? 'success' : 'error');
        if (res.success) setTimeout(() => location.reload(), 1500);
    })
    .catch(() => {
        closeCancelModal();
        notify('An error occurred. Please try again.', 'error');
    });
}

function notify(message, type) {
    const n   = document.createElement('div');
    const bg  = type === 'success' ? 'linear-gradient(135deg,#27ae60,#2ecc71)' : 'linear-gradient(135deg,#e74c3c,#c0392b)';
    const ico = type === 'success' ? 'check-circle' : 'times-circle';
    n.innerHTML = '<i class="fas fa-' + ico + '"></i> ' + message;
    n.style.cssText = 'position:fixed;top:20px;right:20px;background:' + bg + ';color:#fff;padding:14px 20px;border-radius:10px;z-index:10001;transform:translateX(400px);transition:transform 0.3s ease;max-width:380px;font-size:14px;display:flex;align-items:center;gap:10px;box-shadow:0 8px 25px rgba(0,0,0,0.2);';
    document.body.appendChild(n);
    setTimeout(() => n.style.transform = 'translateX(0)', 50);
    setTimeout(() => { n.style.transform = 'translateX(400px)'; setTimeout(() => n.remove(), 300); }, 3500);
}

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
