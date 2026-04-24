<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-facilities.css">

<?php
    $facility       = $data['facility'] ?? null;
    $facilityName   = htmlspecialchars($facility->Name ?? 'Facility');
    $facilityId     = (int)($facility->FacilityID ?? 0);
    $location       = htmlspecialchars($facility->Location ?? '');
    $capacity       = (int)($facility->Capacity ?? 0);
    $hourlyRate     = (float)($facility->HourlyRate ?? 0);
    $status         = strtolower($facility->AvailabilityStatus ?? 'available');

    $todayBookings    = $data['todayBookings'] ?? [];
    $upcomingBookings = $data['upcomingBookings'] ?? [];
    $pastBookings     = $data['pastBookings'] ?? [];

    $statusColor = match($status) {
        'occupied'    => '#f39c12',
        'maintenance' => '#e74c3c',
        default       => '#27ae60',
    };

    function renderBookingRow($booking, $showDate = false): void {
        $bookingId  = (int)($booking->BookingID ?? 0);
        $player     = htmlspecialchars($booking->PlayerName ?? 'Unknown');
        $email      = htmlspecialchars($booking->PlayerEmail ?? '');
        $slot       = htmlspecialchars($booking->SlotLabel ?? '');
        $start      = $booking->StartTime ?? '';
        $end        = $booking->EndTime ?? '';
        $startFmt   = $start ? date('g:i A', strtotime($start)) : '—';
        $endFmt     = $end   ? date('g:i A', strtotime($end))   : '—';
        $cost       = (float)($booking->AmountCharged ?? 0);
        $rawStatus  = strtolower($booking->Status ?? 'confirmed');
        $statusLabel = match($rawStatus) {
            'attended'                    => 'Attended',
            'missed', 'not_attended'      => 'Not Attended',
            'confirmed'                   => 'Confirmed',
            default                       => ucwords(str_replace('_', ' ', $rawStatus)),
        };
        $badgeColor = match($rawStatus) {
            'confirmed'                   => '#2563eb',
            'attended'                    => '#16a34a',
            'missed', 'not_attended'      => '#dc2626',
            default                       => '#6b7280',
        };
        $date = $booking->OccurrenceDate ?? '';
        $dateFmt = $date ? date('M d, Y', strtotime($date)) : '—';
        ?>
        <tr>
            <?php if ($showDate): ?>
            <td><span style="font-weight:600;"><?php echo $dateFmt; ?></span></td>
            <?php endif; ?>
            <td>
                <span style="font-weight:600; color:#1e3a5f;">#<?php echo str_pad($bookingId, 5, '0', STR_PAD_LEFT); ?></span>
            </td>
            <td>
                <div style="font-weight:600;"><?php echo $player; ?></div>
                <div style="font-size:0.8rem; color:#6b7280;"><?php echo $email; ?></div>
            </td>
            <td><?php echo $slot ?: ($startFmt . ' – ' . $endFmt); ?></td>
            <td><?php echo $startFmt; ?> – <?php echo $endFmt; ?></td>
            <td>₨ <?php echo number_format($cost, 0); ?></td>
            <td>
                <span style="background:<?php echo $badgeColor; ?>20; color:<?php echo $badgeColor; ?>; padding:3px 10px; border-radius:20px; font-size:0.8rem; font-weight:600; white-space:nowrap;">
                    <?php echo $statusLabel; ?>
                </span>
            </td>
        </tr>
        <?php
    }
?>

<div class="admin-layout">
    <!-- Shop Sidebar -->
    <div class="admin-sidebar" id="shopSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-store"></i>
                <h3>Shop Manager</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/orders" class="nav-link">
                        <i class="fas fa-shopping-cart"></i><span>Order Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/products" class="nav-link">
                        <i class="fas fa-box"></i><span>Product Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="nav-link">
                        <i class="fas fa-tools"></i><span>Equipment Rentals</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/reviews" class="nav-link">
                        <i class="fas fa-star"></i><span>Reviews & Feedback</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                        <i class="fas fa-building"></i><span>Facility Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/counter" class="nav-link">
                        <i class="fas fa-ticket-alt"></i><span>Counter Booking</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($data['user_name'] ?? 'Shop Manager'); ?></div>
            <div class="profile-role">Shop Employee</div>
            <a href="<?php echo URLROOT; ?>/shop/profile" class="action-btn" style="margin-top:10px;">
                <i class="fas fa-user-cog"></i> Profile
            </a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:8px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1 style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-building" style="color:<?php echo $statusColor; ?>;"></i>
                    <?php echo $facilityName; ?>
                </h1>
                <p style="color:#6b7280; margin:4px 0 0;">
                    <?php if ($location): ?>
                        <i class="fas fa-map-marker-alt"></i> <?php echo $location; ?> &nbsp;|&nbsp;
                    <?php endif; ?>
                    <i class="fas fa-users"></i> Capacity: <?php echo $capacity; ?> &nbsp;|&nbsp;
                    <i class="fas fa-rupee-sign"></i> ₨ <?php echo number_format($hourlyRate, 0); ?>/hour &nbsp;|&nbsp;
                    <span style="color:<?php echo $statusColor; ?>; font-weight:600;"><?php echo ucfirst($status); ?></span>
                </p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="<?php echo URLROOT; ?>/shop/facilities" class="action-btn secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin:20px 0;">
            <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-top:4px solid #2563eb; text-align:center;">
                <div style="font-size:2rem; font-weight:700; color:#2563eb;"><?php echo count($todayBookings); ?></div>
                <div style="color:#6b7280; font-size:0.9rem; margin-top:4px;"><i class="fas fa-calendar-day"></i> Today</div>
            </div>
            <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-top:4px solid #16a34a; text-align:center;">
                <div style="font-size:2rem; font-weight:700; color:#16a34a;"><?php echo count($upcomingBookings); ?></div>
                <div style="color:#6b7280; font-size:0.9rem; margin-top:4px;"><i class="fas fa-calendar-check"></i> Upcoming</div>
            </div>
            <div style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-top:4px solid #6b7280; text-align:center;">
                <div style="font-size:2rem; font-weight:700; color:#6b7280;"><?php echo count($pastBookings); ?></div>
                <div style="color:#6b7280; font-size:0.9rem; margin-top:4px;"><i class="fas fa-history"></i> Past</div>
            </div>
        </div>

        <!-- Today's Bookings -->
        <div class="data-table" style="margin-bottom:24px;">
            <div class="table-header">
                <h3><i class="fas fa-calendar-day" style="color:#2563eb;"></i> Today's Bookings</h3>
            </div>
            <div class="table-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Player</th>
                            <th>Slot</th>
                            <th>Time</th>
                            <th>Cost</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($todayBookings)): ?>
                            <?php foreach ($todayBookings as $b): ?>
                                <?php renderBookingRow($b, false); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:28px; color:#9ca3af;">
                                    <i class="fas fa-calendar-day" style="font-size:1.4rem; display:block; margin-bottom:8px;"></i>
                                    No bookings for today
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="data-table" style="margin-bottom:24px;">
            <div class="table-header">
                <h3><i class="fas fa-calendar-check" style="color:#16a34a;"></i> Upcoming Bookings</h3>
            </div>
            <div class="table-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Booking #</th>
                            <th>Player</th>
                            <th>Slot</th>
                            <th>Time</th>
                            <th>Cost</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($upcomingBookings)): ?>
                            <?php foreach ($upcomingBookings as $b): ?>
                                <?php renderBookingRow($b, true); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:28px; color:#9ca3af;">
                                    <i class="fas fa-calendar" style="font-size:1.4rem; display:block; margin-bottom:8px;"></i>
                                    No upcoming bookings
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Past Bookings -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-history" style="color:#6b7280;"></i> Past Bookings</h3>
            </div>
            <div class="table-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Booking #</th>
                            <th>Player</th>
                            <th>Slot</th>
                            <th>Time</th>
                            <th>Cost</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pastBookings)): ?>
                            <?php foreach ($pastBookings as $b): ?>
                                <?php renderBookingRow($b, true); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:28px; color:#9ca3af;">
                                    <i class="fas fa-history" style="font-size:1.4rem; display:block; margin-bottom:8px;"></i>
                                    No past bookings found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.admin-layout -->

<script>
(function () {
    const sidebar = document.getElementById('shopSidebar');
    const toggle  = document.getElementById('sidebarToggle');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    }
})();
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
