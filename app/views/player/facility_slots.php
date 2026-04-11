<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<style>
/* ── Header shortcuts ── */
.booking-shortcuts {
    display:flex;
    flex-wrap:wrap;
    gap:12px;
}
.booking-shortcut-btn {
    padding:12px 20px;
    border-radius:12px;
    border:2px solid rgba(255,255,255,0.28);
    font-weight:600;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:all .3s ease;
    font-size:14px;
    color:#fff;
    background:rgba(255,255,255,0.18);
}
.booking-shortcut-btn:hover {
    transform:translateY(-2px);
    background:rgba(255,255,255,0.28);
}
.booking-shortcut-btn.primary {
    background:rgba(46,204,113,0.9);
    border-color:rgba(46,204,113,0.95);
}
.booking-shortcut-btn.primary:hover {
    background:rgba(46,204,113,1);
}
/* ── Layout ── */
.fac-page { display:flex; flex-direction:column; gap:24px; }

/* ── Filter bar ── */
.filter-bar {
    background:#fff;
    border-radius:12px;
    border:1px solid #e2e8f0;
    padding:18px 22px;
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    align-items:flex-end;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
}
.filter-bar label  { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:5px; }
.filter-bar select,
.filter-bar input[type=date] {
    padding:9px 12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    font-size:14px;
    color:#111827;
    background:#fff;
    min-width:180px;
    transition:border-color .15s;
}
.filter-bar select:focus,
.filter-bar input[type=date]:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
.btn-filter {
    padding:9px 22px;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:opacity .15s;
    height:40px;
    align-self:flex-end;
}
.btn-filter:hover { opacity:.88; }
.btn-clear {
    padding:9px 16px;
    background:#f1f5f9;
    color:#475569;
    border:1px solid #e2e8f0;
    border-radius:8px;
    font-size:14px;
    font-weight:500;
    cursor:pointer;
    text-decoration:none;
    height:40px;
    align-self:flex-end;
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.btn-clear:hover { background:#e2e8f0; color:#1e293b; }

/* ── Facility cards row ── */
.fac-cards {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
    gap:14px;
}
.fac-card {
    background:#fff;
    border-radius:10px;
    border:2px solid #e2e8f0;
    overflow:hidden;
    text-decoration:none;
    color:inherit;
    transition:border-color .15s, box-shadow .15s, transform .12s;
    display:flex;
    flex-direction:column;
}
.fac-card:hover   { border-color:#2563eb; box-shadow:0 4px 16px rgba(37,99,235,.12); transform:translateY(-2px); }
.fac-card.active  { border-color:#2563eb; background:#f0f7ff; }
.fac-card-img {
    width:100%;
    height:100px;
    object-fit:cover;
    background:#e2e8f0;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    color:#94a3b8;
}
.fac-card-img img { width:100%; height:100px; object-fit:cover; display:block; }
.fac-card-body    { padding:12px 14px; }
.fac-card-name    { font-size:14px; font-weight:700; color:#1e293b; margin-bottom:4px; }
.fac-card-meta    { font-size:12px; color:#64748b; }
.fac-card-rate    { font-size:12px; font-weight:600; color:#2563eb; margin-top:4px; }
.fac-avail-dot    {
    display:inline-block;
    width:7px; height:7px;
    border-radius:50%;
    background:#22c55e;
    margin-right:4px;
}
.fac-avail-dot.unavailable { background:#ef4444; }

/* ── Results section ── */
.results-card {
    background:#fff;
    border-radius:12px;
    border:1px solid #e2e8f0;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
    overflow:hidden;
}
.results-header {
    padding:16px 22px;
    border-bottom:1px solid #f1f5f9;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
}
.results-title { font-size:16px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px; }
.results-count { font-size:13px; color:#64748b; }

.slot-table { width:100%; border-collapse:collapse; font-size:14px; }
.slot-table th {
    text-align:left;
    padding:11px 16px;
    background:#f8fafc;
    color:#475569;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.5px;
    border-bottom:1px solid #e2e8f0;
    white-space:nowrap;
}
.slot-table td {
    padding:13px 16px;
    border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
}
.slot-table tr:last-child td { border-bottom:none; }
.slot-table tr:hover td      { background:#fafbfc; }
.slot-table tr.blocked-row td { color:#94a3b8; }

.badge-type {
    display:inline-block;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.3px;
}
.badge-facility_only { background:#d1fae5; color:#065f46; }
.badge-private       { background:#fef3c7; color:#92400e; }

.spots-ok   { color:#16a34a; font-weight:600; font-size:13px; }
.spots-low  { color:#ea580c; font-weight:600; font-size:13px; }
.spots-full { color:#dc2626; font-weight:600; font-size:13px; }

.block-tag {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 10px;
    border-radius:6px;
    font-size:12px;
    font-weight:500;
}
.block-already    { background:#dcfce7; color:#166534; }
.block-injury     { background:#fee2e2; color:#991b1b; }
.block-full       { background:#fef9c3; color:#854d0e; }
.block-plan       { background:#ede9fe; color:#4c1d95; }

.btn-book-row {
    padding:7px 16px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    border:none;
    border-radius:7px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
    white-space:nowrap;
    transition:opacity .15s;
}
.btn-book-row:hover { opacity:.88; }

.empty-state {
    padding:48px 24px;
    text-align:center;
    color:#94a3b8;
}
.empty-state i    { font-size:48px; display:block; margin-bottom:16px; }
.empty-state p    { font-size:15px; margin-bottom:6px; }
.empty-state small { font-size:13px; }

.flash-msg {
    padding:12px 18px;
    border-radius:8px;
    margin-bottom:18px;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:8px;
}
.flash-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.flash-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

/* mobile stack */
@media(max-width:640px) {
    .filter-bar { flex-direction:column; }
    .filter-bar select,
    .filter-bar input[type=date] { width:100%; }
    .fac-cards { grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); }
    .slot-table { display:block; overflow-x:auto; }
}

/* ── Payment Modal ── */
.pay-modal-overlay {
    position:fixed; inset:0;
    background:rgba(15,23,42,.55);
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:16px;
    backdrop-filter:blur(2px);
}
.pay-modal {
    background:#fff;
    border-radius:16px;
    width:100%;
    max-width:440px;
    box-shadow:0 24px 64px rgba(0,0,0,.28);
    overflow:hidden;
    animation:slideUp .22s ease;
}
@keyframes slideUp {
    from { transform:translateY(28px); opacity:0; }
    to   { transform:translateY(0);    opacity:1; }
}
.pay-modal-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:18px 22px;
    background:linear-gradient(135deg,#1e293b,#334155);
    color:#fff;
}
.pay-modal-header h3 { margin:0; font-size:17px; font-weight:700; display:flex; align-items:center; gap:9px; }
.pay-close {
    background:none; border:none; color:#94a3b8;
    font-size:24px; cursor:pointer; line-height:1;
    transition:color .15s; padding:0 2px;
}
.pay-close:hover { color:#fff; }
.pay-summary {
    background:#f8fafc;
    border-bottom:2px solid #e2e8f0;
    padding:14px 22px;
}
.pay-summary-row {
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:13px;
    padding:4px 0;
}
.pay-summary-row span   { color:#64748b; }
.pay-summary-row strong { color:#1e293b; }
.pay-total {
    margin-top:8px;
    padding-top:10px;
    border-top:2px solid #e2e8f0;
}
.pay-total span   { font-weight:700; color:#1e293b; font-size:14px; }
.pay-total strong { font-size:20px; color:#2563eb; font-weight:800; }
#payForm {
    padding:20px 22px 24px;
    display:flex;
    flex-direction:column;
    gap:15px;
}
.pay-field { display:flex; flex-direction:column; gap:5px; }
.pay-field label { font-size:12px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:.4px; }
.pay-field input {
    padding:11px 14px;
    border:1.5px solid #d1d5db;
    border-radius:8px;
    font-size:14px;
    color:#111827;
    background:#fff;
    transition:border-color .15s, box-shadow .15s;
    width:100%;
    box-sizing:border-box;
}
.pay-field input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
.card-num-wrap { position:relative; display:flex; align-items:center; }
.card-num-wrap i { position:absolute; left:13px; color:#94a3b8; font-size:15px; pointer-events:none; }
.card-num-wrap input { padding-left:38px; letter-spacing:.08em; font-family:monospace; font-size:15px; }
.pay-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.btn-pay {
    padding:13px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    border:none;
    border-radius:9px;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:9px;
    transition:opacity .15s, transform .1s;
    margin-top:4px;
    box-shadow:0 4px 14px rgba(37,99,235,.35);
}
.btn-pay:hover  { opacity:.92; transform:translateY(-1px); }
.btn-pay:active { transform:translateY(0); }
.pay-secure-note {
    text-align:center;
    font-size:12px;
    color:#94a3b8;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    margin-top:-4px;
}
.btn-book-pay {
    padding:7px 16px;
    background:linear-gradient(135deg,#0284c7,#0369a1);
    color:#fff;
    border:none;
    border-radius:7px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
    white-space:nowrap;
    transition:opacity .15s;
}
.btn-book-pay:hover { opacity:.88; }
</style>

<div class="player-layout">
    <!-- ── Sidebar ── -->
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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player"                       class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training"              class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/performance"                  class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/playerslots" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments"           class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical"               class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments"              class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping"              class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
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

    <!-- ── Main Content ── -->
    <div class="main-content">

        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-building"></i> Facility Booking</h1>
                    <p>Browse all facilities and book an available slot for your preferred date and time.</p>
                </div>
                <div class="header-actions booking-shortcuts">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="booking-shortcut-btn">
                        <i class="fas fa-list-alt"></i> My Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="booking-shortcut-btn">
                        <i class="fas fa-user-tie"></i> Coach
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="booking-shortcut-btn">
                        <i class="fas fa-dumbbell"></i> Trainer
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="booking-shortcut-btn primary">
                        <i class="fas fa-building"></i> Facilities
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="booking-shortcut-btn">
                        <i class="fas fa-calendar-alt"></i> Calendar
                    </a>
                </div>
            </div>
        </div>

        <?php
        $filter = $data['filter'];
        $hasFilter = $filter['facility'] > 0 || $filter['date'] !== '' || $filter['slot'] > 0;
        ?>

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

        <div class="fac-page">

            <!-- ── Facility Cards ── -->
            <div>
                <p style="font-size:13px;font-weight:600;color:#475569;margin-bottom:12px;text-transform:uppercase;letter-spacing:.5px;">
                    <i class="fas fa-map-marker-alt"></i> &nbsp;All Facilities — click to filter
                </p>
                <div class="fac-cards">
                    <?php foreach ($data['facilities'] as $fac): ?>
                    <?php
                    $isActive = (int)$filter['facility'] === (int)$fac->FacilityID;
                    // Build href: toggle — clicking an active card clears the facility filter
                    $href = URLROOT . '/playerslots/facilities?'
                          . http_build_query([
                              'facility' => $isActive ? 0 : $fac->FacilityID,
                              'date'     => $filter['date'],
                              'slot'     => $filter['slot'],
                          ]);
                    $dotClass = $fac->AvailabilityStatus === 'available' ? '' : 'unavailable';
                    ?>
                    <a href="<?php echo $href; ?>" class="fac-card <?php echo $isActive ? 'active' : ''; ?>">
                        <div class="fac-card-img">
                            <?php if (!empty($fac->facilityImage)): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/facilities/<?php echo htmlspecialchars($fac->facilityImage); ?>"
                                     alt="<?php echo htmlspecialchars($fac->Name); ?>">
                            <?php else: ?>
                                <i class="fas fa-building"></i>
                            <?php endif; ?>
                        </div>
                        <div class="fac-card-body">
                            <div class="fac-card-name"><?php echo htmlspecialchars($fac->Name); ?></div>
                            <div class="fac-card-meta">
                                <span class="fac-avail-dot <?php echo $dotClass; ?>"></span>
                                <?php echo htmlspecialchars($fac->Location ?? '—'); ?>
                            </div>
                            <div class="fac-card-meta">
                                <i class="fas fa-users" style="font-size:11px;"></i>
                                Capacity: <?php echo (int)$fac->Capacity; ?>
                            </div>
                            <?php if ($fac->HourlyRate > 0): ?>
                            <div class="fac-card-rate">
                                <i class="fas fa-tag" style="font-size:11px;"></i>
                                LKR <?php echo number_format($fac->HourlyRate, 2); ?>/hr
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ── Filter Bar ── -->
            <form method="GET" action="<?php echo URLROOT; ?>/playerslots/facilities" class="filter-bar">
                <div>
                    <label for="f_facility"><i class="fas fa-building"></i> Facility</label>
                    <select name="facility" id="f_facility">
                        <option value="0">All Facilities</option>
                        <?php foreach ($data['facilities'] as $fac): ?>
                            <option value="<?php echo (int)$fac->FacilityID; ?>"
                                <?php echo (int)$filter['facility'] === (int)$fac->FacilityID ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fac->Name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="f_date"><i class="fas fa-calendar"></i> Date</label>
                    <input type="date" name="date" id="f_date"
                           min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo htmlspecialchars($filter['date']); ?>">
                </div>
                <div>
                    <label for="f_slot"><i class="fas fa-clock"></i> Time Band</label>
                    <select name="slot" id="f_slot">
                        <option value="0">Any Time</option>
                        <?php foreach ($data['timeBands'] as $tb): ?>
                            <option value="<?php echo (int)$tb->SlotID; ?>"
                                <?php echo (int)$filter['slot'] === (int)$tb->SlotID ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tb->SlotLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Find Slots
                </button>
                <?php if ($hasFilter): ?>
                <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>

            <!-- ── Results ── -->
            <div class="results-card">
                <div class="results-header">
                    <div class="results-title">
                        <i class="fas fa-calendar-check" style="color:#2563eb;"></i>
                        Available Slots
                    </div>
                    <div class="results-count">
                        <?php
                        $total     = count($data['occurrences']);
                        $bookable  = count(array_filter($data['occurrences'], fn($o) => !$o->blocked));
                        ?>
                        <?php if ($total === 0): ?>
                            No slots found
                        <?php else: ?>
                            <?php echo $total; ?> slot<?php echo $total !== 1 ? 's' : ''; ?> found
                            &mdash; <span style="color:#16a34a;font-weight:600;"><?php echo $bookable; ?> bookable</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($data['occurrences'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No facility slots are scheduled for your selection.</p>
                        <?php if ($hasFilter): ?>
                            <small>Try clearing some filters to see more results.</small>
                        <?php else: ?>
                            <small>Contact the academy to request a facility slot or check back later.</small>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                <table class="slot-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-building"></i> Facility</th>
                            <th><i class="fas fa-map-marker-alt"></i> Location</th>
                            <th><i class="fas fa-tag"></i> Type</th>
                            <th><i class="fas fa-users"></i> Group Size</th>
                            <th><i class="fas fa-coins"></i> Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data['occurrences'] as $occ): ?>
                    <?php
                    $groupCapacity = $occ->OccMax ?: $occ->TplMax;
                    ?>
                    <tr class="<?php echo $occ->blocked ? 'blocked-row' : ''; ?>">
                        <td>
                            <strong><?php echo date('D, d M Y', strtotime($occ->OccurrenceDate)); ?></strong>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($occ->SlotLabel); ?><br>
                            <small style="color:#94a3b8;">
                                <?php echo date('g:i A', strtotime($occ->StartTime)); ?> &ndash;
                                <?php echo date('g:i A', strtotime($occ->EndTime)); ?>
                            </small>
                        </td>
                        <td style="font-weight:600;"><?php echo htmlspecialchars($occ->FacilityName); ?></td>
                        <td style="color:#64748b;font-size:13px;">
                            <?php echo htmlspecialchars($occ->FacilityLocation ?? '—'); ?>
                        </td>
                        <td>
                            <span class="badge-type badge-<?php echo htmlspecialchars($occ->SlotType); ?>">
                                <?php echo ucwords(str_replace('_', ' ', $occ->SlotType)); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($groupCapacity)): ?>
                                <span class="spots-ok">
                                    <i class="fas fa-users"></i> Up to <?php echo (int)$groupCapacity; ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#cbd5e1;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:#1e293b;">
                            <?php if ($occ->PricePerSession > 0): ?>
                                <strong>LKR <?php echo number_format($occ->PricePerSession, 2); ?></strong>
                            <?php else: ?>
                                <span style="color:#94a3b8;">Subscription</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($occ->blocked): ?>
                                <?php $r = $occ->blockReason ?? 'unavailable'; ?>
                                <?php if ($r === 'already_booked'): ?>
                                    <span class="block-tag block-already">
                                        <i class="fas fa-check-circle"></i> Booked
                                    </span>
                                <?php elseif ($r === 'active_injury'): ?>
                                    <span class="block-tag block-injury">
                                        <i class="fas fa-band-aid"></i> Medical hold
                                    </span>
                                <?php elseif ($r === 'full'): ?>
                                    <span class="block-tag block-full">
                                        <i class="fas fa-users-slash"></i> Full
                                    </span>
                                <?php elseif (in_array($r, ['no_subscription','plan_mismatch'], true)): ?>
                                    <span class="block-tag block-plan">
                                        <i class="fas fa-lock"></i>
                                        <?php echo $r === 'no_subscription' ? 'No subscription' : 'Plan upgrade needed'; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="block-tag block-full">
                                        <i class="fas fa-ban"></i> Unavailable
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($occ->PricePerSession > 0): ?>
                                    <button type="button" class="btn-book-pay"
                                            onclick="openPayModal(
                                                <?php echo (int)$occ->OccurrenceID; ?>,
                                                '<?php echo addslashes(htmlspecialchars($occ->FacilityName, ENT_QUOTES)); ?>',
                                                '<?php echo date('D, d M Y', strtotime($occ->OccurrenceDate)); ?>',
                                                '<?php echo addslashes(htmlspecialchars($occ->SlotLabel, ENT_QUOTES)); ?>',
                                                <?php echo (float)$occ->PricePerSession; ?>
                                            )">
                                        <i class="fas fa-credit-card"></i> Book &amp; Pay
                                    </button>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo URLROOT; ?>/playerslots/bookfacility">
                                        <input type="hidden" name="occurrence_id" value="<?php echo (int)$occ->OccurrenceID; ?>">
                                        <input type="hidden" name="amount" value="0">
                                        <button type="submit" class="btn-book-row">
                                            <i class="fas fa-check"></i> Book
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div><!-- /.results-card -->

        </div><!-- /.fac-page -->
    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<!-- ── Payment Modal ── -->
<div id="payModal" class="pay-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="payModalTitle">
    <div class="pay-modal">
        <div class="pay-modal-header">
            <h3 id="payModalTitle"><i class="fas fa-lock"></i> Secure Payment</h3>
            <button type="button" class="pay-close" onclick="closePayModal()" aria-label="Close">&times;</button>
        </div>

        <!-- Order summary -->
        <div class="pay-summary">
            <div class="pay-summary-row">
                <span><i class="fas fa-building" style="margin-right:5px;"></i>Facility</span>
                <strong id="pFacility"></strong>
            </div>
            <div class="pay-summary-row">
                <span><i class="fas fa-calendar" style="margin-right:5px;"></i>Date</span>
                <strong id="pDate"></strong>
            </div>
            <div class="pay-summary-row">
                <span><i class="fas fa-clock" style="margin-right:5px;"></i>Time</span>
                <strong id="pTime"></strong>
            </div>
            <div class="pay-summary-row pay-total">
                <span>Amount Due</span>
                <strong id="pTotal"></strong>
            </div>
        </div>

        <!-- Card payment form -->
        <form method="POST" action="<?php echo URLROOT; ?>/playerslots/bookfacility" id="payForm" onsubmit="return validatePayForm()">
            <input type="hidden" name="occurrence_id" id="pOccId">
            <input type="hidden" name="amount"        id="pAmount">

            <div class="pay-field">
                <label for="cardName">Cardholder Name</label>
                <input type="text" id="cardName" name="card_name"
                       placeholder="Name as on card"
                       autocomplete="cc-name" required>
            </div>

            <div class="pay-field">
                <label for="cardNum">Card Number</label>
                <div class="card-num-wrap">
                    <i class="fas fa-credit-card"></i>
                    <input type="text" id="cardNum" name="card_number"
                           placeholder="0000  0000  0000  0000"
                           maxlength="19" autocomplete="cc-number"
                           inputmode="numeric" required>
                </div>
                <div id="cardBrand" style="font-size:12px;color:#64748b;min-height:16px;margin-top:2px;"></div>
            </div>

            <div class="pay-row">
                <div class="pay-field">
                    <label for="cardExp">Expiry</label>
                    <input type="text" id="cardExp" name="card_expiry"
                           placeholder="MM / YY"
                           maxlength="7" autocomplete="cc-exp"
                           inputmode="numeric" required>
                </div>
                <div class="pay-field">
                    <label for="cardCvv">CVV</label>
                    <input type="text" id="cardCvv" name="card_cvv"
                           placeholder="•••"
                           maxlength="4" autocomplete="cc-csc"
                           inputmode="numeric" required>
                </div>
            </div>

            <button type="submit" class="btn-pay">
                <i class="fas fa-lock"></i> Pay &amp; Confirm Booking
            </button>
            <p class="pay-secure-note">
                <i class="fas fa-shield-alt"></i>
                Payments are processed securely. Your card details are not stored.
            </p>
        </form>
    </div>
</div>

<script>
function openPayModal(occId, facility, date, time, amount) {
    document.getElementById('pOccId').value  = occId;
    document.getElementById('pAmount').value = amount;
    document.getElementById('pFacility').textContent = facility;
    document.getElementById('pDate').textContent     = date;
    document.getElementById('pTime').textContent     = time;

    // Format amount: e.g. 1500 → "LKR 1,500.00"
    var formatted = 'LKR ' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('pTotal').textContent = formatted;

    // Clear previous inputs
    document.getElementById('payForm').querySelectorAll('input[type=text]').forEach(function(el) {
        el.value = '';
    });
    document.getElementById('cardBrand').textContent = '';

    document.getElementById('payModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePayModal();
});

// Card number spacing
document.getElementById('cardNum').addEventListener('input', function() {
    var pos = this.selectionStart;
    var raw = this.value.replace(/\D/g, '').substring(0, 16);
    var fmt = raw.replace(/(.{4})/g, '$1  ').trim();
    this.value = fmt;

    // Detect card brand
    var brand = document.getElementById('cardBrand');
    if (/^4/.test(raw))             brand.innerHTML = '<i class="fab fa-cc-visa"></i> Visa';
    else if (/^5[1-5]/.test(raw))   brand.innerHTML = '<i class="fab fa-cc-mastercard"></i> Mastercard';
    else if (/^3[47]/.test(raw))    brand.innerHTML = '<i class="fab fa-cc-amex"></i> Amex';
    else                            brand.textContent = '';
});

// Expiry formatting
document.getElementById('cardExp').addEventListener('input', function() {
    var raw = this.value.replace(/\D/g, '').substring(0, 4);
    if (raw.length >= 3) {
        this.value = raw.substring(0, 2) + ' / ' + raw.substring(2);
    } else {
        this.value = raw;
    }
});

// CVV — digits only
document.getElementById('cardCvv').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').substring(0, 4);
});

function validatePayForm() {
    var num = document.getElementById('cardNum').value.replace(/\s/g, '');
    var exp = document.getElementById('cardExp').value.replace(/\s/g, '');
    var cvv = document.getElementById('cardCvv').value;
    var name = document.getElementById('cardName').value.trim();

    if (!name) { alert('Please enter the cardholder name.'); return false; }
    if (num.length < 13) { alert('Please enter a valid card number.'); return false; }
    if (!/^\d{2}\/\d{2}$/.test(exp)) { alert('Please enter a valid expiry date (MM / YY).'); return false; }
    if (cvv.length < 3) { alert('Please enter a valid CVV.'); return false; }
    return true;
}

// Sync facility card clicks with the dropdown in the filter bar
document.querySelectorAll('.fac-card').forEach(function(card) {
    card.addEventListener('click', function(e) {
        // Let the href handle navigation naturally
    });
});
</script>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>
