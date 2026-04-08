<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<style>
    /* ── Slots grid ── */
    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding-top: 8px;
    }
    .slot-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e8ecf0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        transition: transform .15s, box-shadow .15s;
        position: relative;
    }
    .slot-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.10); }
    .slot-card.blocked { background: #f6f7f9; opacity: .85; }
    .slot-type-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .badge-program       { background:#dbeafe; color:#1e40af; }
    .badge-private       { background:#fef3c7; color:#92400e; }
    .badge-facility_only { background:#d1fae5; color:#065f46; }
    .slot-date { font-size: 18px; font-weight: 700; color: #1e293b; }
    .slot-time { font-size: 14px; color: #475569; }
    .slot-meta { font-size: 13px; color: #64748b; }
    .slot-meta i { width: 16px; }
    .slots-left { font-size: 12px; font-weight: 600; color: #16a34a; }
    .slots-left.low  { color: #ea580c; }
    .slots-left.none { color: #dc2626; }
    .block-reason {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .block-reason.already_booked  { background:#dcfce7; color:#166534; }
    .block-reason.active_injury   { background:#fee2e2; color:#991b1b; }
    .block-reason.full            { background:#fef9c3; color:#854d0e; }
    .block-reason.no_subscription,
    .block-reason.plan_mismatch   { background:#ede9fe; color:#4c1d95; }
    .btn-book {
        margin-top: auto;
        padding: 10px 0;
        background: linear-gradient(135deg,#2563eb,#1d4ed8);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-book:hover { opacity: .88; }
    .price-tag { font-size: 13px; color: #475569; }
    .price-tag strong { color: #1e293b; }
    .flash-msg {
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .flash-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .flash-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
</style>

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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player"                  class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training"         class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/performance"             class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link"><i class="fas fa-ticket-alt"></i><span>Book Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/bookings"    class="nav-link"><i class="fas fa-list-alt"></i><span>My Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments"      class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical"          class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments"         class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping"         class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($data['player']['name'] ?? 'Player'); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($data['player']['membership_level'] ?? 'Standard'); ?> Member</div>
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
                    <h1><i class="fas fa-ticket-alt"></i> Book a Session</h1>
                    <p>Browse available training slots and book your next session.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="btn btn-training">
                        <i class="fas fa-list-alt"></i> My Session Bookings
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['slot_success'])): ?>
            <div class="flash-msg flash-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['slot_success']); unset($_SESSION['slot_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['slot_error'])): ?>
            <div class="flash-msg flash-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['slot_error']); unset($_SESSION['slot_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Sessions grid -->
        <?php if (empty($data['occurrences'])): ?>
            <div class="schedule-card">
                <div class="card-content" style="padding:40px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-calendar-times" style="font-size:48px;margin-bottom:16px;display:block;"></i>
                    <p style="font-size:16px;">No sessions are currently scheduled.</p>
                    <p style="font-size:13px;margin-top:8px;">Check back soon or contact the academy.</p>
                </div>
            </div>
        <?php else: ?>
            <?php
            $blockLabels = [
                'already_booked'  => ['icon' => 'fa-check-circle',      'text' => 'Already booked'],
                'active_injury'   => ['icon' => 'fa-band-aid',           'text' => 'Blocked — active medical flag'],
                'full'            => ['icon' => 'fa-users-slash',        'text' => 'Session full'],
                'no_subscription' => ['icon' => 'fa-lock',               'text' => 'Subscription required'],
                'plan_mismatch'   => ['icon' => 'fa-lock',               'text' => 'Not included in your plan'],
            ];
            ?>
            <div class="slots-grid">
            <?php foreach ($data['occurrences'] as $occ): ?>
                <?php
                $typeClass = 'badge-' . ($occ->SlotType ?? 'program');
                $typeLabel = str_replace('_', ' ', ucfirst($occ->SlotType ?? 'Program'));
                $maxSpots  = $occ->OccMax ?: $occ->TplMax;
                $spotsLeft = isset($occ->spotsLeft) ? (int)$occ->spotsLeft : max(0, $maxSpots - $occ->BookedCount);
                $spotsClass = $spotsLeft === 0 ? 'none' : ($spotsLeft <= 3 ? 'low' : '');
                ?>
                <div class="slot-card <?php echo $occ->blocked ? 'blocked' : ''; ?>">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <span class="slot-type-badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span>
                        <?php if (!$occ->blocked): ?>
                            <span class="slots-left <?php echo $spotsClass; ?>">
                                <i class="fas fa-users"></i> <?php echo $spotsLeft; ?> spot<?php echo $spotsLeft !== 1 ? 's' : ''; ?> left
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="slot-date">
                        <?php echo date('D, d M Y', strtotime($occ->OccurrenceDate)); ?>
                    </div>
                    <div class="slot-time">
                        <i class="fas fa-clock"></i>
                        <?php echo $occ->SlotLabel; ?> &mdash;
                        <?php echo date('g:i A', strtotime($occ->StartTime)); ?> &ndash;
                        <?php echo date('g:i A', strtotime($occ->EndTime)); ?>
                    </div>

                    <div class="slot-meta">
                        <i class="fas fa-clipboard-list"></i> <?php echo htmlspecialchars($occ->TemplateName); ?>
                    </div>
                    <?php if (!empty($occ->FacilityName)): ?>
                    <div class="slot-meta">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($occ->FacilityName); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($occ->PricePerSession > 0): ?>
                    <div class="price-tag">
                        <i class="fas fa-tag"></i> <strong>LKR <?php echo number_format($occ->PricePerSession, 2); ?></strong>
                    </div>
                    <?php endif; ?>

                    <?php if ($occ->blocked): ?>
                        <?php
                        $reason = $occ->blockReason ?? 'unavailable';
                        $label  = $blockLabels[$reason] ?? ['icon' => 'fa-ban', 'text' => 'Unavailable'];
                        ?>
                        <div class="block-reason <?php echo htmlspecialchars($reason); ?>">
                            <i class="fas <?php echo $label['icon']; ?>"></i>
                            <?php echo $label['text']; ?>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/playerslots/book" style="margin-top:4px;">
                            <input type="hidden" name="occurrence_id" value="<?php echo (int)$occ->OccurrenceID; ?>">
                            <button type="submit" class="btn-book">
                                <i class="fas fa-check"></i> Book This Session
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<?php require_once APPROOT . '/views/inc/footer.php'; ?>
