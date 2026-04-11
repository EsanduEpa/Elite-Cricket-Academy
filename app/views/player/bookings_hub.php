<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<style>
    .bookings-shell {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .bookings-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: flex-end;
    }
    .booking-header-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 14px;
        text-decoration: none;
        color: #fff;
        border: none;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        font-weight: 700;
    }
    .booking-header-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
        filter: brightness(1.03);
    }
    .booking-header-btn.coach { background: linear-gradient(135deg, #60a5fa, #2563eb); }
    .booking-header-btn.trainer { background: linear-gradient(135deg, #4ade80, #16a34a); }
    .booking-header-btn.facility { background: linear-gradient(135deg, #fb923c, #ea580c); }
    .booking-header-btn.sessions { background: linear-gradient(135deg, #a78bfa, #7c3aed); }
    .booking-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        font-size: 15px;
        flex-shrink: 0;
    }
    .booking-header-copy strong {
        display: block;
        font-size: 15px;
        line-height: 1.25;
    }
    .booking-header-copy span {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.84);
        line-height: 1.4;
    }
    .bookings-overview {
        background: linear-gradient(135deg, #f8fbff, #ffffff 55%, #fff7ed);
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    }
    .bookings-overview-top {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-start;
        margin-bottom: 22px;
    }
    .bookings-overview-top h2 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 24px;
    }
    .bookings-overview-top p {
        margin: 0;
        max-width: 760px;
        color: #475569;
        line-height: 1.65;
    }
    .bookings-overview-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        font-weight: 600;
        white-space: nowrap;
    }
    .booking-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }
    .booking-summary-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
    }
    .booking-summary-card h3 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 18px;
    }
    .booking-summary-card p {
        margin: 0 0 16px;
        color: #64748b;
        line-height: 1.55;
    }
    .booking-summary-card a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-weight: 700;
    }
    .booking-summary-card.coach a { color: #2563eb; }
    .booking-summary-card.trainer a { color: #16a34a; }
    .booking-summary-card.facility a { color: #ea580c; }
    @media (max-width: 1100px) {
        .booking-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .bookings-overview-top {
            flex-direction: column;
            align-items: stretch;
        }
        .bookings-header-actions {
            justify-content: flex-start;
        }
    }
    @media (max-width: 700px) {
        .booking-summary-grid {
            grid-template-columns: 1fr;
        }
        .bookings-overview {
            padding: 22px;
        }
        .booking-header-btn {
            width: 100%;
        }
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