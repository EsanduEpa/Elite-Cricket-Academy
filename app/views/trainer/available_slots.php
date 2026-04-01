<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/bookings.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">

<div class="player-layout">
    <!-- Left Sidebar -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-user-tie"></i>
                <h3>Trainer Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i><span>Schedule &amp; Bookings</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/trainer/available_slots" class="nav-link">
                        <i class="fas fa-calendar-plus"></i><span>Available Slots</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i><span>Workout Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                        <i class="fas fa-apple-alt"></i><span>Nutrition Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i><span>Supplements</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                        <i class="fas fa-user-injured"></i><span>Injury Reports</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="trainer-profile">
            <div class="trainer-avatar"><i class="fas fa-user-tie"></i></div>
            <div class="trainer-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Trainer'); ?></div>
            <div class="trainer-role">Fitness Trainer</div>
            <div class="profile-actions">
                <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                    <i class="fas fa-user-cog"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="padding:24px;">
        <div style="margin-bottom:24px;">
            <h1 style="font-size:24px;font-weight:700;color:#2d3748;">
                <i class="fas fa-calendar-plus" style="color:#4299e1;"></i> Available Slots
            </h1>
            <p style="color:#718096;margin-top:4px;">Claim a Physical Training slot to take ownership and let players enroll</p>
        </div>

        <!-- Filters -->
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
            <input type="date" id="filterDate" onchange="filterSlots()" class="form-control" style="width:170px;" min="<?php echo date('Y-m-d'); ?>">
            <select id="filterMode" onchange="filterSlots()" class="form-control" style="width:150px;">
                <option value="">All Modes</option>
                <option value="Group">Group</option>
                <option value="Private">Private</option>
            </select>
            <button class="btn btn-secondary" onclick="clearFilters()"><i class="fas fa-times"></i> Clear</button>
            <span id="slotCount" style="margin-left:auto;color:#666;font-size:14px;"></span>
        </div>

        <!-- Slots Table -->
        <div class="section-card" style="background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);overflow:hidden;">
            <div class="table-responsive">
                <table class="data-table" id="slotsTable" style="width:100%;border-collapse:collapse;">
                    <thead style="background:#f7fafc;">
                        <tr>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Date</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Day</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Time</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Mode</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Name</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Location</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Max</th>
                            <th style="padding:12px 16px;text-align:left;font-size:13px;color:#4a5568;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['slots'])): ?>
                            <?php foreach ($data['slots'] as $slot): ?>
                            <tr data-date="<?php echo $slot->Date; ?>" data-mode="<?php echo htmlspecialchars($slot->SessionMode); ?>"
                                style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:14px 16px;"><?php echo date('d M Y', strtotime($slot->Date)); ?></td>
                                <td style="padding:14px 16px;"><?php echo date('D', strtotime($slot->Date)); ?></td>
                                <td style="padding:14px 16px;"><?php echo date('h:i A', strtotime($slot->StartTime)); ?> – <?php echo date('h:i A', strtotime($slot->EndTime)); ?></td>
                                <td style="padding:14px 16px;">
                                    <span style="padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;
                                        background:<?php echo $slot->SessionMode === 'Private' ? '#e9d8fd' : '#bee3f8'; ?>;
                                        color:<?php echo $slot->SessionMode === 'Private' ? '#553c9a' : '#2b6cb0'; ?>;">
                                        <?php echo htmlspecialchars($slot->SessionMode); ?>
                                    </span>
                                </td>
                                <td style="padding:14px 16px;font-weight:500;"><?php echo htmlspecialchars($slot->Name); ?></td>
                                <td style="padding:14px 16px;color:#718096;"><?php echo htmlspecialchars($slot->Location ?? '—'); ?></td>
                                <td style="padding:14px 16px;"><?php echo intval($slot->MaxParticipants); ?></td>
                                <td style="padding:14px 16px;">
                                    <button id="claimBtn_<?php echo $slot->SessionID; ?>"
                                        onclick="claimSlot(<?php echo $slot->SessionID; ?>, this)"
                                        style="padding:8px 16px;background:#4299e1;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
                                        <i class="fas fa-hand-pointer"></i> Claim
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align:center;color:#888;padding:40px;">
                                No open Physical Training slots available. Ask an admin to create slots.
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const URLROOT = '<?php echo URLROOT; ?>';

function claimSlot(sessionId, btn) {
    if (!confirm('Claim this slot? You will become responsible for this session.')) return;
    btn.disabled = true;
    btn.textContent = 'Claiming...';

    const fd = new FormData();
    fd.append('session_id', sessionId);

    fetch(URLROOT + '/trainer/claim_slot', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            btn.closest('tr').remove();
            updateCount();
            showNotification('Slot claimed! It now appears in your Bookings.', 'success');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-hand-pointer"></i> Claim';
            showNotification(res.message || 'Failed to claim slot.', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-hand-pointer"></i> Claim';
        showNotification('Network error. Please try again.', 'error');
    });
}

function filterSlots() {
    const date = document.getElementById('filterDate').value;
    const mode = document.getElementById('filterMode').value;
    document.querySelectorAll('#slotsTable tbody tr[data-date]').forEach(row => {
        const show = (!date || row.dataset.date === date)
                  && (!mode || row.dataset.mode === mode);
        row.style.display = show ? '' : 'none';
    });
    updateCount();
}

function clearFilters() {
    document.getElementById('filterDate').value = '';
    document.getElementById('filterMode').value = '';
    filterSlots();
}

function updateCount() {
    const visible = document.querySelectorAll('#slotsTable tbody tr[data-date]:not([style*="none"])').length;
    document.getElementById('slotCount').textContent = visible + ' slot(s) available';
}

function showNotification(msg, type) {
    const n = document.createElement('div');
    n.style.cssText = 'position:fixed;top:20px;right:20px;padding:14px 20px;border-radius:8px;color:#fff;z-index:9999;font-weight:500;';
    n.style.background = type === 'success' ? '#38a169' : '#e53e3e';
    n.textContent = msg;
    document.body.appendChild(n);
    setTimeout(() => n.remove(), 3500);
}

updateCount();
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
