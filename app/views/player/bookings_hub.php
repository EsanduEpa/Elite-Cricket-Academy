<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<style>
    .bookings-grid {
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));
        gap:20px;
        margin-top:24px;
    }
    .booking-card {
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:24px;
        box-shadow:0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .booking-card i {
        width:52px;
        height:52px;
        border-radius:14px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        margin-bottom:14px;
        font-size:22px;
        color:#fff;
    }
    .booking-card.coach i { background:linear-gradient(135deg,#2563eb,#1d4ed8); }
    .booking-card.trainer i { background:linear-gradient(135deg,#16a34a,#15803d); }
    .booking-card.facility i { background:linear-gradient(135deg,#ea580c,#c2410c); }
    .booking-card h3 { margin:0 0 8px; color:#0f172a; font-size:20px; }
    .booking-card p { margin:0 0 16px; color:#64748b; line-height:1.5; }
    .booking-card a {
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:10px 16px;
        border-radius:10px;
        color:#fff;
        text-decoration:none;
        font-weight:600;
        transition:transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }
    .booking-card a:hover {
        transform:translateY(-1px);
        filter:brightness(1.02);
    }
    .booking-card.coach a {
        background:linear-gradient(135deg,#60a5fa,#2563eb);
        box-shadow:0 10px 22px rgba(37, 99, 235, 0.22);
    }
    .booking-card.trainer a {
        background:linear-gradient(135deg,#4ade80,#16a34a);
        box-shadow:0 10px 22px rgba(22, 163, 74, 0.22);
    }
    .booking-card.facility a {
        background:linear-gradient(135deg,#fb923c,#ea580c);
        box-shadow:0 10px 22px rgba(234, 88, 12, 0.22);
    }
</style>

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
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-check"></i> Bookings</h1>
                    <p>Choose the booking area you want to manage.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="btn btn-training"><i class="fas fa-list-alt"></i> My Bookings</a>
                </div>
            </div>
        </div>

        <div class="bookings-grid">
            <div class="booking-card coach">
                <i class="fas fa-user-tie"></i>
                <h3>Coach Bookings</h3>
                <p>See available coach-led private sessions and make a booking.</p>
                <a href="<?php echo URLROOT; ?>/playerslots/coach"><span>Open Coach Bookings</span><i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="booking-card trainer">
                <i class="fas fa-dumbbell"></i>
                <h3>Trainer Bookings</h3>
                <p>Browse trainer-led sessions and reserve your preferred time slot.</p>
                <a href="<?php echo URLROOT; ?>/playerslots/trainer"><span>Open Trainer Bookings</span><i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="booking-card facility">
                <i class="fas fa-building"></i>
                <h3>Facilities</h3>
                <p>Book practice nets and other facilities available for player use.</p>
                <a href="<?php echo URLROOT; ?>/playerslots/facilities"><span>Open Facilities</span><i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>