<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_slots.css?v=<?php echo time(); ?>">

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
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/playerslots" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
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
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn profile-logout-spacing">
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
                    <h1><i class="fas fa-ticket-alt"></i> <?php echo htmlspecialchars($data['title'] ?? 'Book a Session'); ?></h1>
                    <p><?php echo htmlspecialchars($data['page_description'] ?? 'Browse available training slots and book your next session.'); ?></p>
                </div>
                <?php $bookingType = $data['booking_type'] ?? null; ?>
                <div class="header-actions booking-shortcuts">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="booking-shortcut-btn">
                        <i class="fas fa-list-alt"></i> My Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="booking-shortcut-btn <?php echo $bookingType === 'coach' ? 'primary' : ''; ?>">
                        <i class="fas fa-user-tie"></i> Coach
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="booking-shortcut-btn <?php echo $bookingType === 'trainer' ? 'primary' : ''; ?>">
                        <i class="fas fa-dumbbell"></i> Trainer
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="booking-shortcut-btn">
                        <i class="fas fa-building"></i> Facilities
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="booking-shortcut-btn">
                        <i class="fas fa-calendar-alt"></i> Calendar
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
                <div class="card-content slots-empty-state">
                    <i class="fas fa-calendar-times slots-empty-state-icon"></i>
                    <p class="slots-empty-state-title">No sessions are currently scheduled for this booking type.</p>
                    <p class="slots-empty-state-copy">Check back soon or contact the academy.</p>
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
                'assigned_program'=> ['icon' => 'fa-user-tie',           'text' => 'Assigned coach session'],
            ];
            ?>
            <div class="slots-grid">
            <?php foreach ($data['occurrences'] as $occ): ?>
                <?php
                $typeClass = 'badge-' . ($occ->SlotType ?? 'program');
                $typeLabel = str_replace('_', ' ', ucfirst($occ->SlotType ?? 'Program'));
                $groupCapacity = $occ->OccMax ?: $occ->TplMax;
                ?>
                <div class="slot-card <?php echo $occ->blocked ? 'blocked' : ''; ?>">
                    <div class="slot-card-toprow">
                        <span class="slot-type-badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span>
                        <?php if (!$occ->blocked): ?>
                            <span class="slots-left">
                                <i class="fas fa-users"></i>
                                <?php if (!empty($groupCapacity)): ?>
                                    Up to <?php echo (int)$groupCapacity; ?> participant<?php echo (int)$groupCapacity !== 1 ? 's' : ''; ?>
                                <?php else: ?>
                                    Private use
                                <?php endif; ?>
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
                    <?php if (!empty($occ->CoachName)): ?>
                    <div class="slot-meta">
                        <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($occ->CoachName); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($occ->SessionMode)): ?>
                    <div class="slot-meta">
                        <i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($occ->SessionMode); ?> Session
                    </div>
                    <?php endif; ?>
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
                        <?php if ($reason === 'assigned_program'): ?>
                            <div style="margin-top:10px;color:#6c757d;font-size:13px;">
                                This session is already assigned to you in your age-group schedule.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/playerslots/book" class="slot-book-form">
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

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
