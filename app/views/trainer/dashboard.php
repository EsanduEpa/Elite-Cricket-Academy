<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'dashboard'; ?>

<!-- Trainer Dashboard Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
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

        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>

        <!-- Profile Section -->
        <div class="trainer-profile">
            <div class="trainer-avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="trainer-name"><?php echo $_SESSION['user_name'] ?? 'Trainer'; ?></div>
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

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-tachometer-alt"></i> Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer'; ?>!</h1>
                    <p>Your training management dashboard - Schedule sessions, track progress, and manage your clients</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="btn btn-training">
                        <i class="fas fa-calendar-plus"></i> New Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/workouts" class="btn btn-performance">
                        <i class="fas fa-dumbbell"></i> Workouts
                    </a>
                    <button class="btn btn-refresh" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i>
                        <span class="current-time"><?php echo date('H:i'); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo (int)($data['today_stats']['active_players'] ?? 0); ?></div>
                <div class="stat-label">Active Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?php echo (int)($data['today_stats']['total_sessions'] ?? 0); ?></div>
                <div class="stat-label">Today's Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?php echo (int)($data['today_stats']['completion_rate'] ?? 0); ?>%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value"><?php echo (int)($data['today_stats']['completed_sessions'] ?? 0); ?></div>
                <div class="stat-label">Completed Sessions</div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="schedule-row">
            <!-- Today's Schedule -->
            <div class="schedule-card today-schedule">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-day"></i> Today's Sessions</h2>
                        <span class="date-display"><?php echo date('M j, Y'); ?></span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Client & Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $todaySessions = $data['today_sessions'] ?? []; ?>
                            <?php if (!empty($todaySessions)): ?>
                                <?php foreach ($todaySessions as $session): ?>
                                    <?php
                                    $start = strtotime($session->StartTime ?? '00:00:00');
                                    $end = strtotime($session->EndTime ?? '00:00:00');
                                    $minutes = ($end > $start) ? (int)(($end - $start) / 60) : 0;
                                    $status = strtolower($session->Status ?? 'upcoming');
                                    $statusClass = in_array($status, ['active', 'completed', 'cancelled', 'upcoming'], true) ? 'status-' . $status : 'status-upcoming';
                                    $participantCount = (int)($session->ParticipantCount ?? 0);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="table-cell-primary"><?php echo date('g:i A', $start); ?></div>
                                            <div class="table-cell-secondary"><?php echo $minutes; ?> minutes</div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?php echo htmlspecialchars($session->Name ?? 'Session'); ?></div>
                                            <div class="table-cell-details">
                                                <i class="fas fa-users"></i>
                                                <?php echo $participantCount; ?> player<?php echo $participantCount !== 1 ? 's' : ''; ?>
                                                - <?php echo htmlspecialchars($session->Location ?? 'Not specified'); ?>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="table-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center;padding:20px;color:#666;">
                                        No sessions scheduled for today.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Session Summary -->
                <div class="session-summary">
                    <?php
                    $activeCount = count(array_filter($todaySessions ?? [], fn($s) => strtolower($s->Status ?? '') === 'active'));
                    $upcomingCount = count(array_filter($todaySessions ?? [], fn($s) => strtolower($s->Status ?? '') === 'upcoming'));
                    $totalMinutes = 0;
                    foreach (($todaySessions ?? []) as $s) {
                        $st = strtotime($s->StartTime ?? '00:00:00');
                        $et = strtotime($s->EndTime ?? '00:00:00');
                        if ($et > $st) {
                            $totalMinutes += (int)(($et - $st) / 60);
                        }
                    }
                    ?>
                    <strong>Today's Summary:</strong>
                    <?php echo count($todaySessions ?? []); ?> sessions scheduled,
                    <?php echo $activeCount; ?> active,
                    <?php echo $upcomingCount; ?> upcoming.
                    Total training time: <?php echo number_format($totalMinutes / 60, 2); ?> hours.
                </div>
            </div>

            <!-- Booking Calendar -->
            <div class="schedule-card calendar-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-calendar-alt"></i> Booking Calendar</h2>
                        <div class="calendar-controls">
                            <button class="nav-btn" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="currentMonth"><?php echo date('F Y'); ?></span>
                            <button class="nav-btn" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="calendar-container">
                        <div class="calendar-legend">
                            <div class="legend-item">
                                <div class="legend-color pending"></div>
                                <span>Pending</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color confirmed"></div>
                                <span>Confirmed</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color completed"></div>
                                <span>Completed</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color cancelled"></div>
                                <span>Cancelled</span>
                            </div>
                        </div>
                        <div class="booking-calendar-grid" id="bookingCalendarGrid">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="schedule-row schedule-row-single">
            <div class="schedule-card activities-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-clipboard-list"></i> Upcoming Activities</h2>
                        <a href="<?php echo URLROOT; ?>/trainer/reports" class="btn-primary">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="activity-list">
                        <?php $upcomingSessions = $data['upcoming_sessions'] ?? []; ?>
                        <?php if (!empty($upcomingSessions)): ?>
                            <?php foreach ($upcomingSessions as $session): ?>
                                <?php
                                $status = strtolower($session->Status ?? 'upcoming');
                                $icon = match ($status) {
                                    'completed' => 'check-circle',
                                    'active' => 'calendar-check',
                                    'cancelled' => 'times-circle',
                                    default => 'clock',
                                };
                                ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-<?php echo $icon; ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Session <?php echo htmlspecialchars(ucfirst($status)); ?></div>
                                        <div class="activity-details"><?php echo htmlspecialchars($session->Name ?? 'Session'); ?> - <?php echo htmlspecialchars($session->Location ?? 'Not specified'); ?></div>
                                        <div class="activity-time"><?php echo date('M j, Y g:i A', strtotime(($session->Date ?? date('Y-m-d')) . ' ' . ($session->StartTime ?? '00:00:00'))); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-title">No upcoming activities</div>
                                    <div class="activity-details">Upcoming sessions will appear here once scheduled.</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Quick Actions -->
        <div class="schedule-card quick-actions-card trainer-quick-actions-bottom">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
            </div>
            <div class="card-content">
                <div class="actions-grid">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="action-btn primary">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Add Session</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/nutrition" class="action-btn secondary">
                        <i class="fas fa-apple-alt"></i>
                        <span>View Nutrition Plans</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="action-btn success">
                        <i class="fas fa-dumbbell"></i>
                        <span>Add Workout Plans</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/medical" class="action-btn warning">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical Records</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

<!-- Scripts -->
<script>
window.trainerDashboardSessions = <?php echo json_encode($data['sessions'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js?v=<?php echo time(); ?>"></script>
<script>
// Dashboard functionality
function refreshDashboard() {
    location.reload();
}

function openNotesModal() {
    alert('Session notes functionality will be implemented');
}

function scheduleFollowup() {
    window.location.href = '<?php echo URLROOT; ?>/trainer/bookings';
}

function viewReports() {
    window.location.href = '<?php echo URLROOT; ?>/trainer/reports';
}

function emergencyProtocol() {
    if(confirm('Are you sure you want to activate emergency protocol?')) {
        alert('Emergency protocol activated. Relevant authorities will be notified.');
    }
}

// Update time every minute
setInterval(function() {
    const timeElement = document.querySelector('.current-time');
    if(timeElement) {
        const now = new Date();
        timeElement.textContent = now.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'});
    }
}, 60000);
</script>
                  