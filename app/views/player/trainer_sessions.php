<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/trainer-sessions.css">
    
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
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                        <i class="fas fa-medal"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shopping</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
            <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Dashboard Header - Using Same Blue Theme as Dashboard -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/player/bookings">Bookings</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Trainer Sessions</span>
                    </div>
                    <h1>Book Trainer Sessions</h1>
                    <p>Schedule fitness and conditioning sessions with our certified trainers</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="btn btn-training">
                        <i class="fas fa-arrow-left"></i>
                        Back to Bookings
                    </a>
                    <button class="btn btn-performance" onclick="showMyTrainerBookings()">
                        <i class="fas fa-history"></i>
                        My Trainer Bookings
                    </button>
                </div>
            </div>
        </div>

        
        <!-- Filter Section -->
        <div class="trainer-filters">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="trainer-filter">Select Trainer</label>
                    <select id="trainer-filter" onchange="applyTrainerFilters()">
                        <option value="">All Trainers</option>
                        <?php foreach ($data['trainers'] ?? [] as $t): ?>
                            <option value="<?= $t->trainer_id ?>"><?= htmlspecialchars($t->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="trainer-date-filter">Select Date</label>
                    <input type="date" id="trainer-date-filter" onchange="applyTrainerFilters()" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="filter-item">
                    <label for="trainer-session-type-filter">Session Type</label>
                    <select id="trainer-session-type-filter" onchange="applyTrainerFilters()">
                        <option value="">All Types</option>
                        <option value="Personal">Personal Training</option>
                        <option value="Group">Group Training</option>
                        <option value="Assessment">Fitness Assessment</option>
                        <option value="Rehabilitation">Injury Rehabilitation</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="training-focus-filter">Training Focus</label>
                    <select id="training-focus-filter" onchange="applyTrainerFilters()">
                        <option value="">All Focus Areas</option>
                        <option value="Strength">Strength Training</option>
                        <option value="Cardio">Cardiovascular Fitness</option>
                        <option value="Flexibility">Flexibility & Mobility</option>
                        <option value="Sports-Specific">Cricket-Specific Training</option>
                        <option value="Recovery">Recovery & Wellness</option>
                    </select>
                </div>
                <div class="filter-item">
                    <button class="btn btn-secondary" onclick="clearTrainerFilters()">
                        <i class="fas fa-refresh"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Trainers Overview -->
        <div class="trainers-overview">
            <h2><i class="fas fa-users"></i> Our Certified Trainers</h2>
            <div class="trainers-grid" id="trainers-grid">
                <?php if (!empty($data['trainers'])): ?>
                    <?php foreach ($data['trainers'] as $trainer): ?>
                    <div class="coach-card" data-trainer-id="<?= $trainer->trainer_id ?>">
                        <div class="coach-avatar">
                            <?php if (!empty($trainer->image) && $trainer->image !== 'default-profile.jpg'): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/<?= htmlspecialchars($trainer->image) ?>" alt="<?= htmlspecialchars($trainer->name) ?>">
                            <?php else: ?>
                                <i class="fas fa-dumbbell"></i>
                            <?php endif; ?>
                        </div>
                        <div class="coach-info">
                            <h3><?= htmlspecialchars($trainer->name) ?></h3>
                            <p class="coach-experience"><i class="fas fa-clock"></i> <?= $trainer->experience_years ?? 0 ?> years experience</p>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="filterByTrainer(<?= $trainer->trainer_id ?>)">
                            <i class="fas fa-calendar-plus"></i> View Sessions
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #666; grid-column: 1/-1;">
                        <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 15px; color: #ccc;"></i>
                        <h3>No Trainers Available</h3>
                        <p>No trainers are currently available. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Trainer Sessions -->
        <div class="available-trainer-sessions">
            <div class="section-header">
                <h2><i class="fas fa-calendar-alt"></i> Available Trainer Sessions</h2>
                <div class="session-stats">
                    <span class="stat-item">
                        <span class="stat-number" id="total-trainer-sessions"><?= count($data['availableTrainerSlots'] ?? []) ?></span>
                        <span class="stat-label">Available Sessions</span>
                    </span>
                </div>
            </div>

            <div class="trainer-sessions-container" id="trainer-sessions-container">
                <?php if (!empty($data['availableTrainerSlots'])): ?>
                    <?php foreach ($data['availableTrainerSlots'] as $slot): ?>
                    <div class="session-slot-card"
                         data-trainer-id="<?= $slot->trainer_id ?>"
                         data-date="<?= $slot->date ?>">
                        <div class="session-slot-header">
                            <div class="session-type-badge badge-group">
                                <i class="fas fa-dumbbell"></i>
                                <?= htmlspecialchars($slot->session_mode ?? 'Group') ?> Session
                            </div>
                            <span class="session-price">
                                <?= isset($slot->price) && $slot->price ? 'Rs. ' . number_format($slot->price, 2) : 'Free' ?>
                            </span>
                        </div>
                        <div class="session-slot-body">
                            <h3 class="session-title"><?= htmlspecialchars($slot->description ?? 'Training Session') ?></h3>
                            <div class="session-details">
                                <div class="detail-item">
                                    <i class="fas fa-user-cog"></i>
                                    <span><?= htmlspecialchars($slot->trainer_name) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?= date('M d, Y', strtotime($slot->date)) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?= date('g:i A', strtotime($slot->start_time)) ?> - <?= date('g:i A', strtotime($slot->end_time)) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($slot->location ?? 'TBA') ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><?= $slot->current_bookings ?>/<?= $slot->max_participants ?> spots filled</span>
                                </div>
                            </div>
                        </div>
                        <div class="session-slot-footer">
                            <?php $spotsLeft = $slot->max_participants - $slot->current_bookings; ?>
                            <span class="spots-left"><?= $spotsLeft ?> spot<?= $spotsLeft !== 1 ? 's' : '' ?> left</span>
                            <button class="btn btn-primary btn-sm" onclick="bookTrainerSession(<?= $slot->slot_id ?>)">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="no-sessions" id="no-trainer-sessions" style="display: <?= empty($data['availableTrainerSlots']) ? 'block' : 'none' ?>;">
                <div class="no-sessions-content">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Trainer Sessions Available</h3>
                    <p>Try adjusting your filters or check back later for new training sessions.</p>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>

<script>
const URLROOT_TS = '<?php echo URLROOT; ?>';

function bookTrainerSession(slotId) {
    const btn = document.querySelector('[onclick="bookTrainerSession(' + slotId + ')"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking…'; }

    fetch(URLROOT_TS + '/player/trainer_sessions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=book_trainer_session&slot_id=' + slotId
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showTSNotif(res.message || 'Enrolled successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showTSNotif(res.message || 'Booking failed. Please try again.', 'error');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-calendar-check"></i> Book Now'; }
        }
    })
    .catch(() => {
        showTSNotif('Network error. Please try again.', 'error');
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-calendar-check"></i> Book Now'; }
    });
}

function showTSNotif(message, type) {
    const n = document.createElement('div');
    const bg = type === 'success' ? 'linear-gradient(135deg,#27ae60,#2ecc71)' : 'linear-gradient(135deg,#e74c3c,#c0392b)';
    const ico = type === 'success' ? 'check-circle' : 'times-circle';
    n.innerHTML = '<i class="fas fa-' + ico + '"></i> ' + message;
    n.style.cssText = 'position:fixed;top:20px;right:20px;background:' + bg + ';color:#fff;padding:14px 20px;border-radius:10px;z-index:10001;transform:translateX(400px);transition:transform 0.3s ease;max-width:400px;font-size:14px;display:flex;align-items:center;gap:10px;box-shadow:0 8px 25px rgba(0,0,0,.2);';
    document.body.appendChild(n);
    setTimeout(() => n.style.transform = 'translateX(0)', 50);
    setTimeout(() => { n.style.transform = 'translateX(400px)'; setTimeout(() => n.remove(), 300); }, 4000);
}

function filterByTrainer(trainerId) {
    document.querySelectorAll('.session-slot-card').forEach(card => {
        card.style.display = (String(card.dataset.trainerId) === String(trainerId)) ? '' : 'none';
    });
    document.getElementById('no-trainer-sessions').style.display = 'none';
}

window.applyTrainerFilters = function() {
    const tFilter = document.getElementById('trainer-filter')?.value || '';
    const dFilter = document.getElementById('trainer-date-filter')?.value || '';
    let visible = 0;
    document.querySelectorAll('.session-slot-card').forEach(card => {
        let show = true;
        if (tFilter && card.dataset.trainerId !== tFilter) show = false;
        if (dFilter && card.dataset.date !== dFilter) show = false;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-trainer-sessions').style.display = visible === 0 ? 'block' : 'none';
};

window.clearTrainerFilters = function() {
    ['trainer-filter','trainer-date-filter','trainer-session-type-filter','training-focus-filter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    applyTrainerFilters();
};

window.showMyTrainerBookings = function() {
    window.location.href = URLROOT_TS + '/player/bookings';
};
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>