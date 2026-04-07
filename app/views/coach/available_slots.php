<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/available-slots.css">

<div class="coach-layout">
    <!-- Left Sidebar -->
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Coach Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                        <i class="fas fa-calendar-alt"></i><span>Sessions</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/available_slots" class="nav-link" data-tooltip="Available Slots">
                        <i class="fas fa-calendar-check"></i><span>Available Slots</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i><span>Players</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i><span>Tournaments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link" data-tooltip="Recommendations">
                        <i class="fas fa-star"></i>
                        <span>Recommendations</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                        <i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                        <i class="fas fa-bell"></i><span>Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                        <i class="fas fa-calendar"></i><span>Events</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content coach-available-slots-page available-slots-page">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-calendar-check"></i> Available Slots</h1>
                    <p style="margin:0;opacity:0.9;font-size:14px;">Claim a session slot to take ownership and let players enroll</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <input type="date" id="filterDate" onchange="filterSlots()" class="form-control filter-control" min="<?php echo date('Y-m-d'); ?>">
            <select id="filterMode" onchange="filterSlots()" class="form-control filter-control">
                <option value="">All Modes</option>
                <option value="Group">Group</option>
                <option value="Private">Private</option>
            </select>
            <button class="btn btn-secondary" onclick="clearFilters()"><i class="fas fa-times"></i> Clear</button>
            <span id="slotCount" class="slot-count"></span>
        </div>

        <!-- Slots Table -->
        <div class="section-card slots-section">
            <div class="table-responsive">
                <table class="data-table" id="slotsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Mode</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Max Spots</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['slots'])): ?>
                            <?php foreach ($data['slots'] as $slot): ?>
                            <tr data-date="<?php echo $slot->Date; ?>" data-mode="<?php echo htmlspecialchars($slot->SessionMode); ?>">
                                <td><?php echo date('d M Y', strtotime($slot->Date)); ?></td>
                                <td><?php echo date('D', strtotime($slot->Date)); ?></td>
                                <td><?php echo date('h:i A', strtotime($slot->StartTime)); ?> – <?php echo date('h:i A', strtotime($slot->EndTime)); ?></td>
                                <td>
                                    <span class="badge <?php echo $slot->SessionMode === 'Private' ? 'badge-purple' : 'badge-blue'; ?>">
                                        <?php echo htmlspecialchars($slot->SessionMode); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($slot->Name); ?></td>
                                <td><?php echo htmlspecialchars($slot->Location ?? '—'); ?></td>
                                <td><?php echo intval($slot->MaxParticipants); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm"
                                        id="claimBtn_<?php echo $slot->SessionID; ?>"
                                        onclick="claimSlot(<?php echo $slot->SessionID; ?>, this)">
                                        <i class="fas fa-hand-pointer"></i> Claim
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="empty-state">
                                No open coaching slots available. Ask an admin to create slots.
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
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Claiming...';

    const fd = new FormData();
    fd.append('session_id', sessionId);

    fetch(URLROOT + '/coach/claim_slot', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            btn.closest('tr').remove();
            updateCount();
            showNotification('Slot claimed! It now appears in your Sessions.', 'success');
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
