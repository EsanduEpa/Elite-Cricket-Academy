<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$counterBookingStatusOptions = [
    'confirmed' => 'Confirmed',
    'completed' => 'Completed',
    'not_attended' => 'Not Attended',
];
?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-dashboard.css">
<style>
    .counter-form-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: 13px; font-weight: 600; color: #374151; }
    .form-group input, .form-group select {
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #111827;
        background: #fff;
        transition: border-color .15s;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .player-search-results {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-height: 220px;
        overflow-y: auto;
        background: #fff;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
    .player-result-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background .12s;
    }
    .player-result-item:last-child { border-bottom: none; }
    .player-result-item:hover { background: #f0f7ff; }
    .player-result-item .player-name { font-weight: 600; color: #1e293b; }
    .player-result-item .player-meta { color: #64748b; font-size: 12px; }
    #selectedPlayerBadge {
        display: none;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: #065f46;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .slot-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .slot-table th {
        text-align: left; padding: 10px 14px;
        background: #f8fafc; color: #475569;
        font-size: 12px; text-transform: uppercase; letter-spacing: .5px;
        border-bottom: 1px solid #e2e8f0;
    }
    .slot-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .slot-table tr:last-child td { border-bottom: none; }
    .slot-table tr:hover td { background: #f8fafc; }
    .badge-type {
        display: inline-block; padding: 3px 10px;
        border-radius: 20px; font-size: 11px; font-weight: 600;
        text-transform: uppercase;
    }
    .badge-private       { background: #fef3c7; color: #92400e; }
    .badge-facility_only { background: #d1fae5; color: #065f46; }
    .btn-select-slot {
        padding: 6px 14px;
        background: #2563eb; color: #fff;
        border: none; border-radius: 6px;
        font-size: 12px; font-weight: 600;
        cursor: pointer; transition: opacity .15s;
    }
    .btn-select-slot:hover { opacity: .85; }
    .btn-select-slot.selected { background: #16a34a; }
    .btn-book-submit {
        padding: 11px 28px;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff; border: none; border-radius: 8px;
        font-size: 15px; font-weight: 600; cursor: pointer;
        transition: opacity .15s;
    }
    .btn-book-submit:hover { opacity: .88; }
    .flash-msg {
        padding: 12px 18px; border-radius: 8px; margin-bottom: 18px;
        font-size: 14px; display: flex; align-items: center; gap: 8px;
    }
    .flash-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .flash-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .section-title { font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .spots-tag { font-size: 12px; font-weight: 600; }
    .spots-ok   { color: #16a34a; }
    .spots-low  { color: #ea580c; }
    .spots-full { color: #dc2626; }
    .status-form { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .status-select {
        min-width:150px; padding:7px 10px; border:1px solid #d1d5db; border-radius:8px;
        font-size:12px; background:#fff; color:#111827;
    }
    .status-save-btn {
        padding:7px 12px; border:none; border-radius:8px; background:#2563eb; color:#fff;
        font-size:12px; font-weight:700; cursor:pointer;
    }
    .status-save-btn:hover { opacity:.9; }
    .status-locked { font-size:12px; color:#6b7280; font-weight:600; }
</style>

<div class="admin-layout">
    <!-- Sidebar (mirrors shop/dashboard.php) -->
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
                        <i class="fas fa-star"></i><span>Reviews &amp; Feedback</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                        <i class="fas fa-building"></i><span>Facility Management</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/shop/counter" class="nav-link">
                        <i class="fas fa-ticket-alt"></i><span>Counter Booking</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Shop Manager'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/shop/profile" class="profile-avatar" aria-label="Open shop profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-ticket-alt"></i> Counter Slot Booking</h1>
                <p>Book facility and private sessions for walk-in players.</p>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['counter_success'])): ?>
            <div class="flash-msg flash-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['counter_success']); unset($_SESSION['counter_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['counter_error'])): ?>
            <div class="flash-msg flash-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($_SESSION['counter_error']); unset($_SESSION['counter_error']); ?>
            </div>
        <?php endif; ?>

        <!-- ── Booking Form ── -->
        <div class="counter-form-card">
            <div class="section-title">
                <i class="fas fa-user-plus" style="color:#2563eb;"></i> New Counter Booking
            </div>

            <form method="POST" action="<?php echo URLROOT; ?>/shop/slotbook" id="counterForm">
                <input type="hidden" name="occurrence_id" id="selectedOccurrenceId" value="">
                <input type="hidden" name="player_id"     id="selectedPlayerId"     value="">
                <input type="hidden" name="amount"        id="selectedAmount"       value="0">

                <!-- Player Search -->
                <div class="form-group">
                    <label><i class="fas fa-search"></i> Search Player (name, email, or ID)</label>
                    <input type="text" id="playerSearch" placeholder="Start typing to search..."
                           autocomplete="off">
                    <div class="player-search-results" id="playerResults"></div>
                    <div id="selectedPlayerBadge" style="display:none;">
                        <i class="fas fa-user-check"></i>
                        <span id="selectedPlayerLabel"></span>
                        <button type="button" onclick="clearPlayer()" style="margin-left:auto;background:none;border:none;color:#991b1b;cursor:pointer;font-size:12px;">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Session Selection notice -->
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> Selected Session</label>
                    <div id="selectedSlotBadge" style="background:#f8fafc;border:1px solid #e2e8f0;
                         border-radius:8px;padding:10px 14px;font-size:13px;color:#94a3b8;">
                        No session selected — click <strong>Select</strong> in the table below.
                    </div>
                </div>

                <button type="submit" class="btn-book-submit" id="submitBtn" disabled>
                    <i class="fas fa-check-circle"></i> Confirm Booking (Cash)
                </button>
                <div id="submitHint" style="font-size:12px;color:#64748b;margin-top:8px;padding:8px 12px;
                     background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;">
                    <i class="fas fa-info-circle" style="color:#3b82f6;"></i>
                    &nbsp;To confirm: <strong>search &amp; click a player</strong> above, then <strong>click Select</strong> on a session below.
                </div>
            </form>
        </div>

        <!-- ── Available Sessions ── -->
        <div class="counter-form-card">
            <div class="section-title">
                <i class="fas fa-list-alt" style="color:#16a34a;"></i> Available Sessions
                <span style="font-size:12px;font-weight:400;color:#94a3b8;margin-left:8px;">
                    Facility &amp; Private slots only
                </span>
            </div>

            <?php if (empty($data['slots'])): ?>
                <div style="padding:30px;text-align:center;color:#94a3b8;">
                    <i class="fas fa-calendar-times" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                    <p>No eligible sessions scheduled. Ask the admin to create facility or private slots.</p>
                </div>
            <?php else: ?>
            <table class="slot-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar"></i> Date</th>
                        <th><i class="fas fa-clock"></i> Time</th>
                        <th><i class="fas fa-clipboard"></i> Session</th>
                        <th><i class="fas fa-map-marker-alt"></i> Facility</th>
                        <th><i class="fas fa-tag"></i> Type</th>
                        <th><i class="fas fa-users"></i> Spots</th>
                        <th><i class="fas fa-sterling-sign"></i> Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data['slots'] as $slot): ?>
                    <?php
                    $max     = $slot->OccMax ?: $slot->TplMax;
                    $left    = max(0, $max - $slot->BookedCount);
                    $spotsClass = $left === 0 ? 'spots-full' : ($left <= 3 ? 'spots-low' : 'spots-ok');
                    ?>
                    <tr data-occ="<?php echo (int)$slot->OccurrenceID; ?>"
                        data-label="<?php echo htmlspecialchars(date('D d M', strtotime($slot->OccurrenceDate)) . ' · ' . $slot->SlotLabel . ' · ' . ($slot->FacilityName ?? $slot->TemplateName)); ?>"
                        data-amount="<?php echo $slot->PricePerSession; ?>">
                        <td><strong><?php echo date('D, d M Y', strtotime($slot->OccurrenceDate)); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($slot->SlotLabel); ?><br>
                            <small style="color:#94a3b8;">
                                <?php echo date('g:i A', strtotime($slot->StartTime)); ?> &ndash;
                                <?php echo date('g:i A', strtotime($slot->EndTime)); ?>
                            </small>
                        </td>
                        <td><?php echo htmlspecialchars($slot->TemplateName); ?></td>
                        <td><?php echo htmlspecialchars($slot->FacilityName ?? '—'); ?></td>
                        <td>
                            <span class="badge-type badge-<?php echo $slot->SlotType; ?>">
                                <?php echo str_replace('_', ' ', ucfirst($slot->SlotType)); ?>
                            </span>
                        </td>
                        <td>
                            <span class="spots-tag <?php echo $spotsClass; ?>">
                                <?php echo $left; ?>/<?php echo $max; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $slot->PricePerSession > 0
                                ? 'LKR ' . number_format($slot->PricePerSession, 2)
                                : '<span style="color:#94a3b8;">—</span>'; ?>
                        </td>
                        <td>
                            <?php if ($left > 0): ?>
                                <button type="button" class="btn-select-slot"
                                        onclick="selectSlot(this)">
                                    Select
                                </button>
                            <?php else: ?>
                                <span style="font-size:12px;color:#dc2626;font-weight:600;">Full</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="counter-form-card">
            <div class="section-title">
                <i class="fas fa-clipboard-check" style="color:#7c3aed;"></i> Facility Booking Status Updates
                <span style="font-size:12px;font-weight:400;color:#94a3b8;margin-left:8px;">
                    Shop employees can update facility-only slot bookings only
                </span>
            </div>

            <?php if (empty($data['facilityBookings'])): ?>
                <div style="padding:20px 0;color:#94a3b8;">
                    No recent facility-only slot bookings were found.
                </div>
            <?php else: ?>
                <table class="slot-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> Booking</th>
                            <th><i class="fas fa-user"></i> Player</th>
                            <th><i class="fas fa-map-marker-alt"></i> Facility</th>
                            <th><i class="fas fa-clock"></i> Session Time</th>
                            <th><i class="fas fa-edit"></i> Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['facilityBookings'] as $booking): ?>
                            <?php
                            $rawStatus = strtolower((string) ($booking->Status ?? 'confirmed'));
                            $selectedStatus = $rawStatus;
                            if ($rawStatus === 'attended') {
                                $selectedStatus = 'completed';
                            } elseif ($rawStatus === 'missed') {
                                $selectedStatus = 'not_attended';
                            }
                            ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo (int) $booking->BookingID; ?></strong><br>
                                    <small style="color:#94a3b8;">
                                        <?php echo date('d M Y', strtotime($booking->CreatedAt)); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($booking->PlayerName); ?><br>
                                    <small style="color:#94a3b8;"><?php echo htmlspecialchars($booking->PlayerEmail ?? '—'); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($booking->FacilityName ?? $booking->TemplateName ?? 'Facility Booking'); ?>
                                </td>
                                <td>
                                    <strong><?php echo date('D, d M Y', strtotime($booking->OccurrenceDate)); ?></strong><br>
                                    <small style="color:#94a3b8;">
                                        <?php echo htmlspecialchars($booking->SlotLabel); ?>
                                        · <?php echo date('g:i A', strtotime($booking->StartTime)); ?> - <?php echo date('g:i A', strtotime($booking->EndTime)); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($rawStatus === 'cancelled'): ?>
                                        <span class="status-locked">Cancelled booking</span>
                                    <?php else: ?>
                                        <form method="POST" action="<?php echo URLROOT; ?>/shop/updateFacilityBookingStatus" class="status-form">
                                            <input type="hidden" name="booking_id" value="<?php echo (int) $booking->BookingID; ?>">
                                            <select name="booking_status" class="status-select">
                                                <?php foreach ($counterBookingStatusOptions as $optionValue => $optionLabel): ?>
                                                    <option value="<?php echo htmlspecialchars($optionValue); ?>" <?php echo $selectedStatus === $optionValue ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($optionLabel); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="status-save-btn">Save</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.admin-layout -->

<script>
// ── Player search ──────────────────────────────────────────────
const searchInput   = document.getElementById('playerSearch');
const resultsBox    = document.getElementById('playerResults');
const playerBadge   = document.getElementById('selectedPlayerBadge');
const playerLabel   = document.getElementById('selectedPlayerLabel');
const playerIdInput = document.getElementById('selectedPlayerId');
let searchTimer;

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    const term = this.value.trim();
    if (term.length < 2) { resultsBox.style.display = 'none'; return; }
    searchTimer = setTimeout(() => doSearch(term), 300);
});

async function doSearch(term) {
    const formData = new FormData();
    formData.append('term', term);
    const resp = await fetch('<?php echo URLROOT; ?>/shop/searchplayer', {
        method: 'POST', body: formData
    });
    const json = await resp.json();
    resultsBox.innerHTML = '';
    if (!json.success || !json.players.length) {
        resultsBox.innerHTML = '<div class="player-result-item"><span class="player-meta">No players found.</span></div>';
        resultsBox.style.display = 'block';
        return;
    }
    json.players.forEach(p => {
        const item = document.createElement('div');
        item.className = 'player-result-item';
        item.innerHTML = `<i class="fas fa-user" style="color:#2563eb;"></i>
            <div>
                <div class="player-name">${escHtml(p.Name)}</div>
                <div class="player-meta">${escHtml(p.Email)} &middot; #${p.UserID}</div>
            </div>`;
        item.onclick = () => selectPlayer(p);
        resultsBox.appendChild(item);
    });
    resultsBox.style.display = 'block';
}

function selectPlayer(p) {
    playerIdInput.value = p.UserID;
    playerLabel.textContent = `${p.Name}  (${p.Email})  — ID #${p.UserID}`;
    playerBadge.style.display = 'flex';
    resultsBox.style.display  = 'none';
    searchInput.value = '';
    checkSubmitReady();
}

function clearPlayer() {
    playerIdInput.value       = '';
    playerBadge.style.display = 'none';
    checkSubmitReady();
}

// ── Slot selection ─────────────────────────────────────────────
let activeSlotBtn = null;

function selectSlot(btn) {
    if (activeSlotBtn) activeSlotBtn.classList.remove('selected');
    activeSlotBtn = btn;
    btn.classList.add('selected');

    const row    = btn.closest('tr');
    const occId  = row.dataset.occ;
    const label  = row.dataset.label;
    const amount = row.dataset.amount;

    document.getElementById('selectedOccurrenceId').value = occId;
    document.getElementById('selectedAmount').value       = amount;

    const badge = document.getElementById('selectedSlotBadge');
    badge.style.color      = '#065f46';
    badge.style.background = '#ecfdf5';
    badge.style.border     = '1px solid #a7f3d0';
    badge.innerHTML = `<i class="fas fa-check-circle"></i> ${escHtml(label)}`;

    checkSubmitReady();
}

function checkSubmitReady() {
    const pid = document.getElementById('selectedPlayerId').value;
    const oid = document.getElementById('selectedOccurrenceId').value;
    const ready = !!(pid && oid);
    document.getElementById('submitBtn').disabled = !ready;
    document.getElementById('submitHint').style.display = ready ? 'none' : 'block';
}
checkSubmitReady(); // show hint on page load

// ── Helpers ────────────────────────────────────────────────────
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('click', function(e) {
    if (!resultsBox.contains(e.target) && e.target !== searchInput) {
        resultsBox.style.display = 'none';
    }
});
</script>

<?php require_once APPROOT . '/views/inc/footer.php'; ?>
