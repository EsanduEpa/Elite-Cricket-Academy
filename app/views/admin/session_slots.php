<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/session-slots.css">

<div class="admin-layout">
    <!-- Left Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-user-shield"></i>
                <h3>Admin Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                        <i class="fas fa-users-cog"></i><span>Staff Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                        <i class="fas fa-user-graduate"></i><span>Player Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                        <i class="fas fa-calendar-alt"></i><span>Events &amp; Tournaments</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/admin/session_slots" class="nav-link">
                        <i class="fas fa-calendar-plus"></i><span>Session Slots</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                        <i class="fas fa-comments"></i><span>Feedback Monitoring</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                        <i class="fas fa-file-alt"></i><span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                        <i class="fas fa-chart-line"></i><span>Finance Management</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="admin-profile">
            <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
            <div class="profile-info">
                <span class="admin-name">Admin User</span>
                <span class="admin-role">Super Administrator</span>
            </div>
            <div class="logout-btn">
                <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content session-slots-page" id="mainContent">

        <!-- Page Header -->
        <div class="feedback-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-plus"></i> Session Slot Management</h1>
                    <p>Create time slots for coaches and trainers to claim and conduct sessions</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openCreateSlotModal()">
                        <i class="fas fa-plus"></i> Create Slot
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-calendar-plus"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $data['openCount']; ?></div>
                    <div class="stat-label">Open Slots</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo count($data['recentActive']); ?></div>
                    <div class="stat-label">Claimed Sessions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-list"></i></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo count($data['slots']) + count($data['recentActive']); ?></div>
                    <div class="stat-label">Total Slots</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <select id="filterType" onchange="filterTable()" class="form-control" style="width:180px;">
                <option value="">All Types</option>
                <option value="Coaching">Coaching</option>
                <option value="Physical Training">Physical Training</option>
            </select>
            <select id="filterMode" onchange="filterTable()" class="form-control" style="width:150px;">
                <option value="">All Modes</option>
                <option value="Group">Group</option>
                <option value="Private">Private</option>
            </select>
            <input type="date" id="filterDate" onchange="filterTable()" class="form-control" style="width:170px;" placeholder="Filter by date">
            <button class="btn btn-secondary" onclick="clearFilters()"><i class="fas fa-times"></i> Clear</button>
        </div>

        <!-- Open Slots Table -->
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fas fa-calendar-plus"></i> Open Slots (Unclaimed)</h2>
            </div>
            <div class="table-responsive">
                <table class="data-table" id="openSlotsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Max</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['slots'])): ?>
                            <?php foreach ($data['slots'] as $slot): ?>
                            <tr data-type="<?php echo htmlspecialchars($slot->SessionType); ?>"
                                data-mode="<?php echo htmlspecialchars($slot->SessionMode); ?>"
                                data-date="<?php echo htmlspecialchars($slot->Date); ?>">
                                <td><?php echo date('d M Y', strtotime($slot->Date)); ?></td>
                                <td><?php echo date('D', strtotime($slot->Date)); ?></td>
                                <td><?php echo date('h:i A', strtotime($slot->StartTime)); ?> – <?php echo date('h:i A', strtotime($slot->EndTime)); ?></td>
                                <td>
                                    <span class="badge <?php echo $slot->SessionType === 'Coaching' ? 'badge-blue' : 'badge-green'; ?>">
                                        <?php echo htmlspecialchars($slot->SessionType); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($slot->SessionMode); ?></td>
                                <td><?php echo htmlspecialchars($slot->Name); ?></td>
                                <td><?php echo htmlspecialchars($slot->Location ?? '—'); ?></td>
                                <td><?php echo intval($slot->MaxParticipants); ?></td>
                                <td>
                                    <button class="btn btn-danger btn-sm" onclick="deleteSlot(<?php echo $slot->SessionID; ?>, this)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="emptyRow">
                                <td colspan="9" class="empty-state">
                                    No open slots. Click "Create Slot" to add one.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Claimed Sessions Table -->
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fas fa-calendar-check"></i> Claimed Sessions (Active)</h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Coach / Trainer</th>
                            <th>Enrolled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['recentActive'])): ?>
                            <?php foreach ($data['recentActive'] as $s): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($s->Date)); ?></td>
                                <td><?php echo date('h:i A', strtotime($s->StartTime)); ?> – <?php echo date('h:i A', strtotime($s->EndTime)); ?></td>
                                <td>
                                    <span class="badge <?php echo $s->SessionType === 'Coaching' ? 'badge-blue' : 'badge-green'; ?>">
                                        <?php echo htmlspecialchars($s->SessionType); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($s->SessionMode); ?></td>
                                <td><?php echo htmlspecialchars($s->Name); ?></td>
                                <td><?php echo htmlspecialchars($s->Location ?? '—'); ?></td>
                                <td><?php echo isset($s->ParticipantCount) ? intval($s->ParticipantCount) . ' / ' . intval($s->MaxParticipants) : '—'; ?></td>
                                <td><?php echo intval($s->ParticipantCount ?? 0); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    No claimed sessions yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- Create Slot Modal -->
<div id="createSlotModal" class="create-slot-modal">
    <div class="create-slot-modal__card">
        <button type="button" class="create-slot-modal__close" onclick="closeCreateSlotModal()">&times;</button>
        <h2 class="create-slot-modal__title"><i class="fas fa-calendar-plus"></i> Create Session Slot</h2>

        <form id="createSlotForm">
            <div class="create-slot-modal__grid">
                <div class="slot-field">
                    <label>Session Type *</label>
                    <select name="session_type" id="sl_type" class="form-control" required>
                        <option value="Coaching">Coaching</option>
                        <option value="Physical Training">Physical Training</option>
                    </select>
                </div>
                <div class="slot-field">
                    <label>Mode *</label>
                    <select name="session_mode" class="form-control" required>
                        <option value="Group">Group</option>
                        <option value="Private">Private</option>
                    </select>
                </div>
                <div class="slot-field slot-field--full">
                    <label>Slot Name / Description *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. U15 Batting Practice" required>
                </div>
                <div class="slot-field">
                    <label>Date *</label>
                    <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="slot-field">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Main Ground">
                </div>
                <div class="slot-field">
                    <label>Start Time *</label>
                    <input type="time" name="start_time" id="sl_start" class="form-control" required>
                </div>
                <div class="slot-field">
                    <label>End Time *</label>
                    <input type="time" name="end_time" id="sl_end" class="form-control" required>
                </div>
                <div class="slot-field">
                    <label>Max Participants</label>
                    <input type="number" name="max_participants" class="form-control" value="10" min="1" max="100">
                </div>
                <div class="slot-field">
                    <label>Price per Session (Rs.)</label>
                    <input type="number" name="price" class="form-control" value="0" min="0" step="0.01">
                </div>
                <div class="slot-field slot-field--full slot-field__checkbox">
                    <input type="checkbox" name="is_recurring" id="sl_recurring" checked>
                    <label for="sl_recurring">Recurring session</label>
                </div>
            </div>

            <div id="slotFormError" class="slot-form-error"></div>

            <div class="create-slot-modal__actions">
                <button type="button" class="btn btn-secondary" onclick="closeCreateSlotModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="slotSubmitBtn">
                    <i class="fas fa-plus"></i> Create Slot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const URLROOT = '<?php echo URLROOT; ?>';

function openCreateSlotModal() {
    document.getElementById('createSlotModal').style.display = 'flex';
}
function closeCreateSlotModal() {
    document.getElementById('createSlotModal').style.display = 'none';
    document.getElementById('createSlotForm').reset();
    document.getElementById('slotFormError').style.display = 'none';
}

document.getElementById('createSlotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('slotSubmitBtn');
    const errEl = document.getElementById('slotFormError');
    errEl.style.display = 'none';

    const start = document.getElementById('sl_start').value;
    const end   = document.getElementById('sl_end').value;
    if (start && end && end <= start) {
        errEl.textContent = 'End time must be after start time.';
        errEl.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

    fetch(URLROOT + '/admin/create_session_slot', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeCreateSlotModal();
            location.reload();
        } else {
            errEl.textContent = res.message || 'Failed to create slot.';
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Create Slot';
        }
    })
    .catch(() => {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Create Slot';
    });
});

function deleteSlot(slotId, btn) {
    if (!confirm('Delete this slot? This cannot be undone.')) return;
    btn.disabled = true;

    fetch(URLROOT + '/admin/delete_slot/' + slotId, { method: 'POST' })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            btn.closest('tr').remove();
            // Show empty row if table is empty
            const tbody = document.querySelector('#openSlotsTable tbody');
            if (!tbody.querySelector('tr')) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="9" class="empty-state">No open slots.</td>';
                tbody.appendChild(tr);
            }
        } else {
            alert(res.message || 'Failed to delete slot.');
            btn.disabled = false;
        }
    });
}

function filterTable() {
    const type = document.getElementById('filterType').value;
    const mode = document.getElementById('filterMode').value;
    const date = document.getElementById('filterDate').value;
    document.querySelectorAll('#openSlotsTable tbody tr[data-type]').forEach(row => {
        const show = (!type || row.dataset.type === type)
                  && (!mode || row.dataset.mode === mode)
                  && (!date || row.dataset.date === date);
        row.style.display = show ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('filterType').value = '';
    document.getElementById('filterMode').value = '';
    document.getElementById('filterDate').value = '';
    filterTable();
}
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
