<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<!-- FullCalendar removed for coach dashboard -->
<style>
.stat-card.clickable {
    transition: all 0.3s ease;
    position: relative;
}
.stat-card.clickable:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}
.stat-action-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.7;
    transition: opacity 0.3s ease;
}
.stat-card.clickable:hover .stat-action-icon {
    opacity: 1;
}

/* Bookings Modal Styles */
.bookings-modal {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bookings-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    cursor: pointer;
}
.bookings-modal__dialog {
    position: relative;
    z-index: 1001;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
}
.bookings-modal__header {
    padding: 20px 24px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.bookings-modal__header h2 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1a3c5e;
}
.bookings-modal__close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bookings-modal__close:hover {
    color: #d32f2f;
}
.bookings-modal__body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}
.bookings-list-modal {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.booking-item-modal {
    padding: 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #f9f9f9;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    transition: all 0.3s ease;
}
.booking-item-modal:hover {
    background: #f5f5f5;
    border-color: #4A90E2;
}
.booking-item-modal__content {
    flex: 1;
}
.booking-item-modal__header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.booking-item-modal__header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
}
.session-type-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.session-type-badge.private {
    background: #e3f2fd;
    color: #1e40af;
}
.session-type-badge.normal {
    background: #e8f5e9;
    color: #2e7d32;
}
.booking-item-modal__info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    font-size: 0.9rem;
}
.info-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.info-label {
    font-weight: 600;
    color: #666;
    min-width: 90px;
}
.info-value {
    color: #333;
}
.booking-item-modal__actions {
    display: flex;
    gap: 8px;
}
.btn-cancel-booking {
    padding: 8px 14px;
    border-radius: 6px;
    border: none;
    background: #ffebee;
    color: #d32f2f;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    white-space: nowrap;
}
.btn-cancel-booking:hover {
    background: #d32f2f;
    color: white;
}
.btn-cancel-booking:disabled {
    background: #e0e0e0;
    color: #999;
    cursor: not-allowed;
}
.no-bookings-message {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}
.no-bookings-message i {
    font-size: 3rem;
    margin-bottom: 12px;
    display: block;
    opacity: 0.3;
}

/* Cancel Confirmation Modal */
.cancel-confirmation-modal {
    position: fixed;
    inset: 0;
    z-index: 1002;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cancel-confirmation-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    cursor: pointer;
}
.cancel-confirmation-modal__dialog {
    position: relative;
    z-index: 1003;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 500px;
    padding: 24px;
}
.cancel-confirmation-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.cancel-confirmation-modal__header h2 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a3c5e;
}
.cancel-confirmation-modal__close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0;
}
.cancel-confirmation-modal__body {
    margin-bottom: 20px;
}
.session-name-display {
    font-weight: 600;
    font-size: 1.1rem;
    color: #333;
    margin: 0 0 8px 0;
}
.session-info-display {
    color: #666;
    font-size: 0.95rem;
    margin: 0;
}
.cancel-warning {
    padding: 12px;
    border-radius: 6px;
    background: #ffebee;
    color: #d32f2f;
    font-size: 0.9rem;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}
.cancel-confirmation-modal__footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}
.btn-secondary,
.btn-danger {
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}
.btn-secondary {
    background: #f0f0f0;
    color: #333;
}
.btn-secondary:hover {
    background: #e0e0e0;
}
.btn-danger {
    background: #d32f2f;
    color: white;
}
.btn-danger:hover {
    background: #b71c1c;
}
.btn-danger:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.booking-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: stretch;
}
.booking-actions .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 44px;
    min-height: 40px;
    border-radius: 12px;
    border: 1px solid rgba(31, 111, 235, 0.12);
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.booking-actions .btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(15, 23, 42, 0.12);
}
.booking-actions .btn-action.view {
    background: #eef6ff;
    color: #0f5fc2;
}
.booking-actions .btn-action.edit {
    background: #f3fff7;
    color: #12804a;
}

.past-sessions-card {
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 22px;
    overflow: hidden;
    background:
        radial-gradient(circle at top right, rgba(59, 130, 246, 0.10), transparent 34%),
        linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
}
.past-sessions-card .card-header {
    margin-bottom: 0;
    padding: 20px 24px;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
}
.past-sessions-card .card-header .header-content {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.past-sessions-card .card-header h2 {
    color: var(--text-primary);
}
.past-sessions-card .card-header h2 i {
    color: var(--primary-color);
}
.past-sessions-card .card-content {
    padding: 18px 20px 20px;
}
.past-sessions-table-wrap {
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.18);
    background: rgba(255, 255, 255, 0.92);
}
.past-sessions-card .dashboard-table {
    margin: 0;
}
.past-session-date {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    min-width: 112px;
    border-radius: 16px;
    background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
    border: 1px solid rgba(96, 165, 250, 0.22);
}
.past-session-name {
    font-weight: 700;
    color: #0f172a;
}
.past-session-location {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 5px;
    color: #475569;
}
.past-session-count {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    min-width: 72px;
}
.past-session-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #eef6ff;
    color: #0f5fc2;
    border: 1px solid rgba(15, 95, 194, 0.14);
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.past-session-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(15, 23, 42, 0.12);
}
</style>

    <!-- Coach Dashboard Layout -->
    <div class="coach-layout">
        <!-- Left Sidebar Panel -->
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
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Slot Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link" data-tooltip="Performance">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                            <i class="fas fa-comments"></i>
                            <span>Communication</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Requests</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-chalkboard-teacher"></i> Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Coach'; ?>!</h1>
                    <p>Manage your training sessions, players, and schedules</p>
                </div>
                <div class="header-actions">
                    <div class="coach-type-badge <?php echo strtolower(str_replace(' ', '-', $data['coachType'])); ?>">
                        <i class="fas fa-medal"></i>
                        <?php echo htmlspecialchars($data['coachType']); ?>
                    </div>
                    <div class="current-time" id="currentTime"></div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon today">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['todaySessions']; ?></div>
                        <div class="stat-label">Today's Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon private">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['privateSessions']; ?></div>
                        <div class="stat-label">Private Sessions</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon normal">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['normalSessions']; ?></div>
                        <div class="stat-label">Group Sessions</div>
                    </div>
                </div>
                <div class="stat-card clickable" onclick="openBookingsModal()" style="cursor:pointer;" title="View all upcoming bookings">
                    <div class="stat-icon total">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $data['totalSessions']; ?></div>
                        <div class="stat-label">Total Sessions</div>
                    </div>
                    <div class="stat-action-icon">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <!-- Top Row: Today's Sessions + Upcoming Bookings -->
            <div class="top-row">
                <!-- Today's Sessions Overview -->
                <div class="todays-sessions">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-day"></i> Today's Training Sessions</h2>
                    <div class="session-summary">
                        <span class="private-count"><?php echo $data['privateSessions']; ?> Private</span>
                        <span class="normal-count"><?php echo $data['normalSessions']; ?> Group</span>
                    </div>
                </div>
                <div class="sessions-timeline">
                    <?php 
                    $todayBookings = array_filter($data['upcomingBookings'], function($booking) {
                        return date('Y-m-d', strtotime($booking['date'])) === date('Y-m-d');
                    });
                    ?>
                    <?php if (!empty($todayBookings)): ?>
                        <?php foreach ($todayBookings as $session): ?>
                            <div class="session-timeline-item <?php echo $session['session_type']; ?>">
                                <div class="timeline-marker"></div>
                                <div class="session-card">
                                    <div class="session-time"><?php echo $session['time']; ?></div>
                                    <div class="session-content">
                                        <h4><?php echo htmlspecialchars($session['session_name']); ?></h4>
                                        <p class="participants"><i class="fas fa-users"></i> <?php echo htmlspecialchars($session['player_name']); ?></p>
                                        <div class="session-meta">
                                            <span class="type-badge <?php echo $session['session_type']; ?>">
                                                <?php echo ($session['session_type'] === 'private') ? 'Private' : 'Group'; ?>
                                            </span>
                                            <?php $todayStatusMeta = $session['status_meta'] ?? ['key' => 'scheduled', 'label' => 'Scheduled']; ?>
                                            <span class="status-badge <?php echo htmlspecialchars($todayStatusMeta['key']); ?>">
                                                <i class="fas fa-info-circle"></i>
                                                <?php echo htmlspecialchars($todayStatusMeta['label']); ?>
                                            </span>
                                            <span class="facility"><?php echo htmlspecialchars($session['facility'] ?? 'TBA'); ?></span>
                                        </div>
                                        <?php if (!empty($session['attendance_enabled'])): ?>
                                        <div class="attendance-action-row">
                                            <button type="button"
                                                    class="attendance-list-btn"
                                                    data-session-id="<?php echo (int) $session['id']; ?>"
                                                    data-session-name="<?php echo htmlspecialchars($session['session_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fas fa-clipboard-list"></i>
                                                <?php echo htmlspecialchars($session['attendance_label']); ?>
                                            </button>
                                        </div>
                                        <?php else: ?>
                                        <div class="attendance-action-row">
                                            <button type="button" class="attendance-list-btn" disabled style="opacity:.55;cursor:not-allowed;">
                                                <i class="fas fa-lock"></i>
                                                <?php echo htmlspecialchars($session['attendance_note'] ?? 'Available after session ends'); ?>
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($session['equipment'])): ?>
                                        <div class="equipment-list">
                                            <i class="fas fa-tools"></i> <?php echo htmlspecialchars($session['equipment']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-sessions-today">
                            <i class="fas fa-calendar-check"></i>
                            <p>No sessions scheduled for today</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

                <!-- Upcoming Bookings Section -->
                <div class="bookings-section">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> Upcoming Bookings</h2>
                        <div class="filter-controls">
                            <select id="sessionFilter" onchange="filterSessions()">
                                <option value="all">All Sessions</option>
                                <option value="private">Private Sessions</option>
                                <option value="normal">Group Sessions</option>
                            </select>
                        </div>
                    </div>
                    <div class="bookings-list">
                        <?php if (!empty($data['upcomingBookings'])): ?>
                            <?php foreach ($data['upcomingBookings'] as $booking): ?>
                                <div class="booking-item <?php echo $booking['session_type']; ?>-session">
                                    <div class="booking-time">
                                        <div class="time"><?php echo $booking['time']; ?></div>
                                        <div class="date"><?php echo date('M d', strtotime($booking['date'])); ?></div>
                                    </div>
                                    <div class="booking-details">
                                        <h3><?php echo htmlspecialchars($booking['session_name']); ?></h3>
                                        <p class="participants-list"><i class="fas fa-users"></i> <?php echo htmlspecialchars($booking['player_name']); ?></p>
                                        <div class="session-info">
                                            <span class="session-type <?php echo $booking['session_type']; ?>">
                                                <i class="fas <?php echo $booking['session_type'] === 'private' ? 'fa-user' : 'fa-users'; ?>"></i>
                                                <?php echo ($booking['session_type'] === 'private') ? 'Private' : 'Group'; ?>
                                            </span>
                                            <?php $bookingStatusMeta = $booking['status_meta'] ?? ['key' => 'scheduled', 'label' => 'Scheduled']; ?>
                                            <span class="status-badge <?php echo htmlspecialchars($bookingStatusMeta['key']); ?>">
                                                <i class="fas fa-info-circle"></i>
                                                <?php echo htmlspecialchars($bookingStatusMeta['label']); ?>
                                            </span>
                                            <span class="duration">
                                                <i class="fas fa-clock"></i> <?php echo $booking['duration']; ?>
                                            </span>
                                        </div>
                                        <div class="facility-info">
                                            <span class="facility">
                                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['facility'] ?? 'TBA'); ?>
                                            </span>
                                            <?php if (!empty($booking['equipment'])): ?>
                                            <span class="equipment">
                                                <i class="fas fa-tools"></i> <?php echo htmlspecialchars($booking['equipment']); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <a href="<?php echo URLROOT; ?>/coach/occurrence/<?php echo (int) ($booking['id'] ?? 0); ?>" class="btn-action view" title="View session details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/coach/occurrence/<?php echo (int) ($booking['id'] ?? 0); ?>" class="btn-action edit" title="Update booking status">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-bookings">
                                <i class="fas fa-calendar-times"></i>
                                <h3>No upcoming bookings</h3>
                                <p>Schedule new sessions to see them here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div> <!-- end top-row -->

                <!-- Past Sessions Section -->
                <div class="schedule-card past-sessions-card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-history"></i> Past Sessions</h2>
                            <span class="table-badge status-completed"><?php echo count($data['pastSessions'] ?? []); ?> records</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="past-sessions-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Session</th>
                                    <th>Type</th>
                                    <th>Participants</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['pastSessions'])): ?>
                                    <?php foreach ($data['pastSessions'] as $session): ?>
                                        <?php $sessionStatus = strtolower((string) ($session->Status ?? 'completed')); ?>
                                        <tr>
                                            <td class="table-cell-center">
                                                <div class="past-session-date">
                                                    <div class="table-cell-primary"><?php echo !empty($session->Date) ? date('M d, Y', strtotime($session->Date)) : 'N/A'; ?></div>
                                                    <div class="table-cell-secondary"><?php echo !empty($session->StartTime) ? date('H:i', strtotime($session->StartTime)) : ''; ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title past-session-name"><?php echo htmlspecialchars($session->Name ?? 'Session'); ?></div>
                                                <div class="table-cell-details past-session-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($session->Facility ?? 'Academy'); ?></div>
                                            </td>
                                            <td class="table-cell-center">
                                                <span class="table-badge <?php echo strtolower((string) ($session->SessionMode ?? 'group')) === 'private' ? 'status-confirmed' : 'status-pending'; ?>">
                                                    <?php echo htmlspecialchars($session->SessionMode ?? 'Group'); ?>
                                                </span>
                                            </td>
                                            <td class="table-cell-center">
                                                <div class="past-session-count">
                                                    <div class="table-cell-primary"><?php echo (int) ($session->ParticipantCount ?? 0); ?></div>
                                                    <div class="table-cell-secondary"><?php echo (int) ($session->MaxParticipants ?? 0); ?> max</div>
                                                </div>
                                            </td>
                                            <td class="table-cell-center">
                                                <span class="table-badge status-<?php echo htmlspecialchars($sessionStatus); ?>">
                                                    <?php echo htmlspecialchars(ucfirst($sessionStatus)); ?>
                                                </span>
                                            </td>
                                            <td class="table-cell-center">
                                                <a href="<?php echo URLROOT; ?>/coach/occurrence/<?php echo (int) ($session->SessionID ?? 0); ?>" class="past-session-action" title="View session">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="table-empty-panel-cell">
                                            <div class="table-empty-panel">
                                                <i class="fas fa-history table-empty-panel-icon"></i>
                                                <h3 class="table-empty-panel-title">No Past Sessions</h3>
                                                <p>Completed and cancelled sessions will appear here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="bottom-section">
                <div class="quick-actions-card">
                    <div class="section-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <button class="action-btn primary" type="button" data-action="schedule-session">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Schedule Session</span>
                        </button>
                        <button class="action-btn secondary" type="button" data-action="view-players">
                            <i class="fas fa-users"></i>
                            <span>View Players</span>
                        </button>
                        <button class="action-btn success" type="button" data-action="add-recommendation">
                            <i class="fas fa-lightbulb"></i>
                            <span>Add Recommendation</span>
                        </button>
                        <button class="action-btn warning" type="button" data-action="check-medical">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Check</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="attendance-modal" id="attendanceModal" aria-hidden="true">
        <div class="attendance-modal__backdrop" data-action="close-attendance"></div>
        <div class="attendance-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="attendanceModalTitle">
            <div class="attendance-modal__header">
                <div>
                    <h3 id="attendanceModalTitle">Eligible Players</h3>
                    <p id="attendanceModalSubtitle">Select the players who attended the session.</p>
                </div>
                <button type="button" class="attendance-modal__close" data-action="close-attendance" aria-label="Close attendance panel">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="attendance-modal__body">
                <input type="hidden" id="attendanceSessionId" value="">
                <div id="attendanceRosterList" class="attendance-roster-list"></div>
            </div>
            <div class="attendance-modal__footer">
                <div class="attendance-modal__hint">Checked players are marked as present. Unchecked players are saved as absent.</div>
                <div class="attendance-modal__actions">
                    <button type="button" class="attendance-modal__secondary" data-action="close-attendance">Cancel</button>
                    <button type="button" class="attendance-modal__primary" id="saveAttendanceBtn">Save Attendance</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Modal -->
    <div class="bookings-modal" id="bookingsModal" style="display:none;">
        <div class="bookings-modal__backdrop" onclick="closeBookingsModal()"></div>
        <div class="bookings-modal__dialog">
            <div class="bookings-modal__header">
                <h2>Upcoming Bookings</h2>
                <button class="bookings-modal__close" onclick="closeBookingsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bookings-modal__body">
                <div id="bookingsList" class="bookings-list-modal">
                    <?php if (!empty($data['upcomingBookings'])): ?>
                        <?php foreach ($data['upcomingBookings'] as $booking): ?>
                            <?php
                                $sessionStartAt = strtotime(($booking['date'] ?? '') . ' ' . ($booking['start_time'] ?? ''));
                                $nowTimestamp = time();
                                $hoursUntilSession = $sessionStartAt ? (($sessionStartAt - $nowTimestamp) / 3600) : null;
                                $isPastSession = $hoursUntilSession !== null && $hoursUntilSession <= 0;
                                $isWithinCancelWindow = $hoursUntilSession !== null && $hoursUntilSession > 0 && $hoursUntilSession <= 48;
                                $isCancelUnavailable = $isPastSession || $isWithinCancelWindow;
                                $cancelLabel = $isPastSession ? 'Session Passed' : ($isWithinCancelWindow ? 'Too Soon' : 'Cancel');
                            ?>
                            <div class="booking-item-modal" data-booking-id="<?php echo (int)$booking['id']; ?>" data-booking-date="<?php echo htmlspecialchars($booking['date']); ?>" data-booking-time="<?php echo htmlspecialchars($booking['start_time']); ?>">
                                <div class="booking-item-modal__content">
                                    <div class="booking-item-modal__header">
                                        <h3><?php echo htmlspecialchars($booking['session_name']); ?></h3>
                                        <span class="session-type-badge <?php echo $booking['session_type']; ?>">
                                            <?php echo ($booking['session_type'] === 'private') ? 'Private' : 'Group'; ?>
                                        </span>
                                    </div>
                                    <div class="booking-item-modal__info">
                                        <div class="info-row">
                                            <span class="info-label"><i class="fas fa-calendar"></i> Date:</span>
                                            <span class="info-value"><?php echo date('M d, Y', strtotime($booking['date'])); ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label"><i class="fas fa-clock"></i> Time:</span>
                                            <span class="info-value"><?php echo $booking['time']; ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label"><i class="fas fa-hourglass-half"></i> Duration:</span>
                                            <span class="info-value"><?php echo htmlspecialchars($booking['duration']); ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label"><i class="fas fa-map-marker-alt"></i> Location:</span>
                                            <span class="info-value"><?php echo htmlspecialchars($booking['facility']); ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label"><i class="fas fa-users"></i> Participants:</span>
                                            <span class="info-value"><?php echo htmlspecialchars($booking['player_name']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-item-modal__actions">
                                    <button class="btn-cancel-booking" <?php echo $isCancelUnavailable ? 'disabled' : ''; ?> onclick="cancelBooking(<?php echo (int)$booking['id']; ?>, '<?php echo htmlspecialchars($booking['date']); ?>', '<?php echo htmlspecialchars($booking['start_time']); ?>', '<?php echo htmlspecialchars($booking['session_name']); ?>')">
                                        <i class="fas fa-ban"></i> <?php echo htmlspecialchars($cancelLabel); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-bookings-message">
                            <i class="fas fa-calendar-times"></i>
                            <p>No upcoming bookings</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="cancel-confirmation-modal" id="cancelConfirmationModal" style="display:none;">
        <div class="cancel-confirmation-modal__backdrop" onclick="closeCancelConfirmation()"></div>
        <div class="cancel-confirmation-modal__dialog">
            <div class="cancel-confirmation-modal__header">
                <h2>Cancel Session</h2>
                <button class="cancel-confirmation-modal__close" onclick="closeCancelConfirmation()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cancel-confirmation-modal__body">
                <p id="cancelSessionName" class="session-name-display"></p>
                <p id="cancelSessionInfo" class="session-info-display"></p>
                <p id="cancelWarning" class="cancel-warning" style="display:none; color:#d32f2f; margin-top:12px;">
                    <i class="fas fa-exclamation-triangle"></i> Cannot cancel within 48 hours of session time
                </p>
                <div class="form-group" id="reasonGroup">
                    <label for="cancellationReason">Reason for cancellation:</label>
                    <textarea id="cancellationReason" placeholder="Enter reason (optional)" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ddd; resize:vertical; min-height:80px;"></textarea>
                </div>
            </div>
            <div class="cancel-confirmation-modal__footer">
                <button class="btn-secondary" onclick="closeCancelConfirmation()">Keep Session</button>
                <button class="btn-danger" id="confirmCancelBtn" onclick="confirmCancelSession()">
                    <i class="fas fa-check"></i> Confirm Cancellation
                </button>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <script>
        window.__COACH_DASHBOARD_DATA = <?php echo json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
        window.__COACH_DASHBOARD_ENDPOINTS = {
            roster: '<?php echo URLROOT; ?>/coach/get_session_roster/',
            saveAttendance: '<?php echo URLROOT; ?>/coach/save_session_attendance'
        };
        window.__COACH_BASE_URL = '<?php echo URLROOT; ?>';
    </script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Coach Dashboard JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/coach/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/coach/dashboard-ui.js"></script>

    <script>
    // Bookings Modal Functions
    function openBookingsModal() {
        document.getElementById('bookingsModal').style.display = 'flex';
    }

    function closeBookingsModal() {
        document.getElementById('bookingsModal').style.display = 'none';
    }

    let pendingCancelData = {
        bookingId: null,
        date: null,
        startTime: null,
        sessionName: null
    };

    function calculateHoursUntilSession(bookingDate, startTime) {
        const now = new Date();
        const sessionDateTime = new Date(bookingDate + 'T' + startTime);
        const hoursUntil = (sessionDateTime - now) / (1000 * 60 * 60);
        return hoursUntil;
    }

    function canCancelSession(bookingDate, startTime) {
        const hoursUntil = calculateHoursUntilSession(bookingDate, startTime);
        return hoursUntil > 48; // Can cancel only if more than 48 hours away and not in the past
    }

    function getCancellationState(bookingDate, startTime) {
        const hoursUntil = calculateHoursUntilSession(bookingDate, startTime);

        if (isNaN(hoursUntil)) {
            return 'invalid';
        }

        if (hoursUntil <= 0) {
            return 'past';
        }

        if (hoursUntil <= 48) {
            return 'within_window';
        }

        return 'allowed';
    }

    function cancelBooking(bookingId, date, startTime, sessionName) {
        const cancellationState = getCancellationState(date, startTime);
        
        // Store pending cancel data
        pendingCancelData = {
            bookingId: bookingId,
            date: date,
            startTime: startTime,
            sessionName: sessionName
        };

        // Update modal content
        const formattedDate = new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const timeStr = startTime ? startTime.substring(0, 5) : 'TBD';
        
        document.getElementById('cancelSessionName').textContent = 'Session: ' + sessionName;
        document.getElementById('cancelSessionInfo').textContent = formattedDate + ' at ' + timeStr;

        const cancelBtn = document.getElementById('confirmCancelBtn');
        const reasonGroup = document.getElementById('reasonGroup');
        const cancelWarning = document.getElementById('cancelWarning');
        const cancelWarningText = cancellationState === 'past'
            ? 'This session has already passed and cannot be cancelled.'
            : 'Cannot cancel within 48 hours of session time';
        
        if (cancellationState !== 'allowed') {
            // Cannot cancel - show warning
            cancelWarning.style.display = 'block';
            cancelWarning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + cancelWarningText;
            reasonGroup.style.display = 'none';
            cancelBtn.disabled = true;
            cancelBtn.textContent = cancellationState === 'past'
                ? '✗ Session Passed'
                : '✗ Cannot Cancel (Within 48 Hours)';
        } else {
            // Can cancel - show form
            cancelWarning.style.display = 'none';
            reasonGroup.style.display = 'block';
            cancelBtn.disabled = false;
            cancelBtn.textContent = '✓ Confirm Cancellation';
            document.getElementById('cancellationReason').value = '';
        }

        // Close bookings modal and open confirmation modal
        closeBookingsModal();
        document.getElementById('cancelConfirmationModal').style.display = 'flex';
    }

    function closeCancelConfirmation() {
        document.getElementById('cancelConfirmationModal').style.display = 'none';
        pendingCancelData = { bookingId: null, date: null, startTime: null, sessionName: null };
    }

    function confirmCancelSession() {
        const reason = document.getElementById('cancellationReason').value;
        const cancellationState = getCancellationState(pendingCancelData.date, pendingCancelData.startTime);
        
        if (!pendingCancelData.bookingId) {
            alert('Error: No booking selected');
            return;
        }

        if (cancellationState !== 'allowed') {
            alert(cancellationState === 'past'
                ? 'This session has already passed and cannot be cancelled.'
                : 'This session cannot be cancelled within 48 hours of the start time.');
            return;
        }

        // Send AJAX request to cancel the session
        fetch('<?php echo URLROOT; ?>/coach/cancel_session/' + encodeURIComponent(pendingCancelData.bookingId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'reason=' + encodeURIComponent(reason)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Session cancelled successfully');
                closeCancelConfirmation();
                // Reload the page or update the bookings list
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to cancel session'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling session');
        });
    }

    // Close modals on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (document.getElementById('bookingsModal').style.display !== 'none') {
                closeBookingsModal();
            }
            if (document.getElementById('cancelConfirmationModal').style.display !== 'none') {
                closeCancelConfirmation();
            }
        }
    });
    </script>
</body>
</html>
