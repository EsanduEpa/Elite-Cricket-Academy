<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/training.css">
    
    <div class="player-layout">
        <?php $playerActivePage = ''; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-dumbbell"></i> Training Schedule</h1>
                <p>View your upcoming training sessions and track your progress.</p>
            </div>

            <!-- Weekly Overview -->
            <?php
            $upcomingAll = $data['upcomingSessions'] ?? [];
            $todayAll    = $data['todaySessions']    ?? [];
            // Sessions this week = today + upcoming within 7 days
            $weekSessions = count($todayAll) + count(array_filter($upcomingAll, function($s) {
                return strtotime($s->Date) <= strtotime('+7 days');
            }));
            // Total upcoming (not past)
            $totalUpcoming = count($todayAll) + count($upcomingAll);
            ?>
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stat-value"><?php echo $weekSessions; ?></div>
                    <div class="stat-label">Sessions This Week</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalUpcoming; ?></div>
                    <div class="stat-label">Upcoming Sessions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-value"><?php echo count($todayAll); ?></div>
                    <div class="stat-label">Today's Sessions</div>
                </div>
            </div>

            <!-- Today's Training and Weekly Schedule - Two Tables Per Row -->
            <div class="performance-tables-row">
                <!-- Today's Training -->
                <div class="schedule-card today-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-day"></i> Today's Training</h2>
                            <span class="date-display"><?php echo date('M j'); ?></span>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Training Activity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['todaySessions'])): ?>
                                <?php foreach ($data['todaySessions'] as $s): ?>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary"><?php echo date('g:i A', strtotime($s->start_time)); ?></div>
                                        <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($s->end_time)); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($s->activity); ?></div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($s->location ?? ''); ?>
                                            <?php if (!empty($s->coach)): ?> &mdash; <?php echo htmlspecialchars($s->coach); ?><?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-upcoming">Upcoming</span>
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
            </div>

                <!-- This Week's Schedule -->
                <div class="schedule-card upcoming-schedule">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-week"></i> This Week's Schedule</h2>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Training Activity</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['upcomingSessions'])): ?>
                                <?php foreach ($data['upcomingSessions'] as $s): ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary"><?php echo date('D', strtotime($s->Date)); ?></div>
                                        <div class="table-cell-secondary"><?php echo date('M j', strtotime($s->Date)); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($s->Name); ?></div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($s->Location ?? ''); ?>
                                            <?php if (!empty($s->CoachName)): ?> &mdash; <?php echo htmlspecialchars($s->CoachName); ?><?php endif; ?>
                                        </div>
                                        <span class="table-badge"><?php echo htmlspecialchars($s->SessionType ?? 'Session'); ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary"><?php echo date('g:i A', strtotime($s->StartTime)); ?></div>
                                        <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($s->EndTime)); ?></div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center;padding:20px;color:#666;">
                                        No upcoming sessions this week.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Training & Match Calendar -->
            <div class="calendar-section">
                <div class="calendar-header">
                    <h3><i class="fas fa-calendar-alt"></i> Training & Match Calendar</h3>
                    <div class="calendar-controls">
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="month"><i class="fas fa-calendar"></i> Month</button>
                            <button class="view-btn" data-view="week"><i class="fas fa-calendar-week"></i> Week</button>
                            <button class="view-btn" data-view="day"><i class="fas fa-calendar-day"></i> Day</button>
                        </div>
                        <div class="calendar-nav">
                            <button id="todayBtn" class="calendar-btn today-btn" title="Go to Today"><i class="fas fa-calendar-check"></i></button>
                            <button id="prevPeriod" class="calendar-btn"><i class="fas fa-chevron-left"></i></button>
                            <span id="currentPeriod"></span>
                            <button id="nextPeriod" class="calendar-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
                <div id="calendarContent" class="calendar-content">
                    <div id="monthView" class="calendar-grid"></div>
                    <div id="weekView" class="week-view" style="display: none;"></div>
                    <div id="dayView" class="day-view" style="display: none;"></div>
                </div>
                <div class="calendar-legend">
                    <div class="legend-item"><span class="legend-dot training"></span> Training Sessions</div>
                    <div class="legend-item"><span class="legend-dot match"></span> Matches</div>
                    <div class="legend-item"><span class="legend-dot fitness"></span> Fitness</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>Training Actions</h3>
                <div class="action-buttons">
                    <a href="<?php echo URLROOT; ?>/playerslots/available" class="action-btn">
                        <i class="fas fa-plus"></i> Book Session
                    </a>
                    <a href="<?php echo URLROOT; ?>/performance" class="action-btn">
                        <i class="fas fa-chart-line"></i> View Progress
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Training notes feature coming soon!')">
                        <i class="fas fa-sticky-note"></i> Training Notes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/training.js"></script>
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>