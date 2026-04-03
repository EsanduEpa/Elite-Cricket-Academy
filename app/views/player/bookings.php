<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/bookings.css?v=<?php echo time(); ?>">
    
    <div class="player-layout">
        <!-- Simple Sidebar -->
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
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/calendar" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Calendar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Simple Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
                <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Dashboard Header - Using Same Style as Dashboard -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1>Session Bookings</h1>
                        <p>Book sessions with coaches and trainers or view your existing bookings</p>
                    </div>
                    <div class="header-actions">
                        <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="btn btn-training">
                            <i class="fas fa-user-tie"></i>
                            Coach Sessions
                        </a>
                        <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="btn btn-performance">
                            <i class="fas fa-dumbbell"></i>
                            Trainer Sessions
                        </a>
                        <button class="btn btn-calendar" onclick="showBookingHistory()">
                            <i class="fas fa-history"></i>
                            Booking History
                        </button>
                    </div>
                </div>
            </div>

           

            <!-- Quick Booking Section - Small Cards -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-rocket"></i> Quick Booking</h2>
                    <p>Choose your preferred session type to get started</p>
                </div>
                <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <!-- Coach Sessions Card -->
                    <div class="stat-card action-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-title">Coach Sessions</div>
                            <div class="stat-value" style="color: #4A90E2; font-size: 18px; font-weight: 600;">₹2,000+</div>
                            <div class="stat-label" style="font-size: 12px; color: #666;">Expert Cricket Coaching</div>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?php echo URLROOT; ?>/player/coach_sessions" class="action-btn" style="width: 100%; padding: 12px;">
                                <i class="fas fa-calendar-plus"></i> Book Coach Session
                            </a>
                        </div>
                    </div>

                    <!-- Trainer Sessions Card -->
                    <div class="stat-card action-card">
                        <div class="stat-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-title">Trainer Sessions</div>
                            <div class="stat-value" style="color: #4A90E2; font-size: 18px; font-weight: 600;">₹1,500+</div>
                            <div class="stat-label" style="font-size: 12px; color: #666;">Fitness & Conditioning</div>
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?php echo URLROOT; ?>/player/trainer_sessions" class="action-btn" style="width: 100%; padding: 12px;">
                                <i class="fas fa-calendar-plus"></i> Book Trainer Session
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions Overview -->
            <div class="schedule-card upcoming-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-alt"></i> Upcoming private Sessions</h2>
                        <div class="header-filters">
                            <select class="form-control filter-select" id="session-type-filter" onchange="filterSessions()">
                                <option value="all">All Sessions</option>
                                <option value="coach">Coach Sessions</option>
                                <option value="trainer">Trainer Sessions</option>
                            </select>
                            <select class="form-control filter-select" id="status-filter" onchange="filterSessions()">
                                <option value="all">All Status</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Session Details</th>
                                <th>Instructor</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sessions-tbody">
                            <?php if (!empty($data['upcomingBookings'])): ?>
                                <?php foreach ($data['upcomingBookings'] as $booking): 
                                    $type = $booking->booking_type; // 'coach', 'trainer', 'facility'
                                    $status = strtolower($booking->Status);
                                    $dateFormatted = date('M d, Y', strtotime($booking->date));
                                    $startTime = date('g:i A', strtotime($booking->StartTime));
                                    $endTime = date('g:i A', strtotime($booking->EndTime));
                                    
                                    // Icon and label based on type
                                    if ($type === 'coach') {
                                        $icon = 'fa-user-tie';
                                        $typeLabel = 'Coach Session';
                                        $badgeLabel = 'Coach';
                                        $roleLabel = 'Cricket Coach';
                                    } elseif ($type === 'trainer') {
                                        $icon = 'fa-dumbbell';
                                        $typeLabel = 'Trainer Session';
                                        $badgeLabel = 'Trainer';
                                        $roleLabel = 'Fitness Trainer';
                                    } elseif ($type === 'session') {
                                        $icon = 'fa-users';
                                        $typeLabel = 'Group Session';
                                        $badgeLabel = 'Session';
                                        $roleLabel = 'Coach / Trainer';
                                    } else {
                                        $icon = 'fa-building';
                                        $typeLabel = 'Facility Booking';
                                        $badgeLabel = 'Facility';
                                        $roleLabel = 'Facility';
                                    }
                                    
                                    // Status class
                                    $statusClass = 'status-' . $status;
                                    $statusLabel = ucfirst($status);
                                ?>
                                <tr data-type="<?= $type ?>" data-status="<?= $status ?>">
                                    <td>
                                        <div class="table-cell-title"><?= htmlspecialchars($booking->reason ?? $typeLabel) ?></div>
                                        <div class="table-cell-details">
                                            <i class="fas <?= $icon ?>"></i> <?= $typeLabel ?>
                                        </div>
                                        <span class="table-badge"><?= $badgeLabel ?></span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?= htmlspecialchars($booking->practitioner_name) ?></div>
                                        <div class="table-cell-secondary"><?= $roleLabel ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?= $dateFormatted ?></div>
                                        <div class="table-cell-secondary"><?= $startTime ?> - <?= $endTime ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">-</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (in_array($status, ['scheduled', 'confirmed', 'enrolled'])): ?>
                                            <button class="action-btn btn-sm btn-danger" onclick="confirmCancel(<?= $booking->id ?>, '<?= $type ?>')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        <?php elseif ($status === 'pending'): ?>
                                            <button class="action-btn btn-sm" onclick="makePayment(<?= $booking->id ?>)">
                                                <i class="fas fa-credit-card"></i> Pay
                                            </button>
                                        <?php else: ?>
                                            <span class="table-cell-secondary"><?= $statusLabel ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Empty State -->
                    <div class="empty-state" id="empty-state" style="display: <?= empty($data['upcomingBookings']) ? 'block' : 'none' ?>;">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3>No Sessions Found</h3>
                        <p>No upcoming sessions. Book a coach or trainer session to get started!</p>
                        <button class="action-btn" onclick="clearFilters()">
                            <i class="fas fa-filter"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

   

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; padding:32px; max-width:420px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div style="font-size:48px; color:#e74c3c; margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i></div>
            <h3 style="margin:0 0 10px; color:#2c3e50;">Cancel Booking?</h3>
            <p style="color:#666; margin:0 0 8px;">This action cannot be undone.</p>
            <p style="color:#888; font-size:13px; margin:0 0 24px;">
                Cancellations must be made <strong>at least 24 hours</strong> before the session.<br>
                Maximum <strong>3 cancellations per month</strong> allowed.
            </p>
            <div style="display:flex; gap:12px; justify-content:center;">
                <button onclick="closeCancelModal()" style="padding:10px 24px; border:2px solid #ddd; background:#fff; border-radius:8px; cursor:pointer; font-size:14px;">Keep Booking</button>
                <button id="confirmCancelBtn" onclick="doCancel()" style="padding:10px 24px; background:#e74c3c; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600;">Yes, Cancel</button>
            </div>
        </div>
    </div>

    <script>window.URLROOT_FACILITY = '<?php echo URLROOT; ?>';</script>
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/bookings.js"></script>

    <script>
    const CANCEL_URL = '<?php echo URLROOT; ?>/player/cancel_booking';
    let _cancelId = null, _cancelType = null;

    function confirmCancel(id, type) {
        _cancelId = id;
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
        btn.disabled = true;
        btn.textContent = 'Cancelling...';

        fetch(CANCEL_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'booking_id=' + _cancelId + '&booking_type=' + _cancelType
        })
        .then(r => r.json())
        .then(data => {
            closeCancelModal();
            showBookingNotification(data.message || (data.success ? 'Cancelled' : 'Failed'), data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        })
        .catch(() => {
            closeCancelModal();
            showBookingNotification('An error occurred. Please try again.', 'error');
        });
    }

    function showBookingNotification(message, type) {
        const n = document.createElement('div');
        const bg = type === 'success' ? 'linear-gradient(135deg,#27ae60,#2ecc71)' : 'linear-gradient(135deg,#e74c3c,#c0392b)';
        const icon = type === 'success' ? 'check-circle' : 'times-circle';
        n.innerHTML = '<i class="fas fa-' + icon + '"></i> ' + message;
        n.style.cssText = 'position:fixed;top:20px;right:20px;background:' + bg + ';color:#fff;padding:14px 20px;border-radius:10px;z-index:10001;transform:translateX(400px);transition:transform 0.3s ease;max-width:380px;font-size:14px;display:flex;align-items:center;gap:10px;box-shadow:0 8px 25px rgba(0,0,0,0.2);';
        document.body.appendChild(n);
        setTimeout(() => n.style.transform = 'translateX(0)', 50);
        setTimeout(() => { n.style.transform = 'translateX(400px)'; setTimeout(() => n.remove(), 300); }, 3500);
    }

    // Close modal on backdrop click
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });
    </script>

    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>