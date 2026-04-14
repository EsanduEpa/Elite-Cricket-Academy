<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/tournaments.css">

    <!-- Admin Dashboard Layout -->
    <div class="admin-layout">
        <!-- Left Sidebar Panel -->
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
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard Overview</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>Staff Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                            <i class="fas fa-user-graduate"></i>
                            <span>Player Management</span>
                        </a>
                    </li>
                    
                                   <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>

                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Feedback Monitoring</span>
                            <?php if($data['totalPendingFeedback'] > 0): ?>
                            <span class="badge"><?php echo $data['totalPendingFeedback']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>Slot Management</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Finance Management</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Admin Profile -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?></div>
                <div class="profile-role">Super Administrator</div>
                <a href="<?php echo URLROOT; ?>/admin/profile" class="action-btn" style="margin-top: 10px;">
                    <i class="fas fa-user-cog"></i> Profile
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-tachometer-alt"></i> Elite Cricket Academy - Admin Dashboard</h1>
                    <p>Comprehensive management system for academy operations</p>
                </div>
                <div class="header-actions">
                    <button class="refresh-btn" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <div class="current-time" id="currentTime"></div>
                </div>
            </div>
            <!-- Summary Cards - 4 in a Row with Charts -->
            <div class="summary-cards">
                <div class="summary-card staff-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="card-info">
                            <span class="number"><?php echo $data['totalStaff']; ?></span>
                            <span class="label">Total Staff</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="staffChart"></canvas>
                    </div>
                </div>

                <div class="summary-card events-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="card-info">
                            <span class="number"><?php echo count($data['upcomingEvents']); ?></span>
                            <span class="label">Upcoming Events</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>

                <div class="summary-card feedback-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="card-info">
                            <span class="number"><?php echo $data['totalPendingFeedback']; ?></span>
                            <span class="label">Pending Reviews</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="feedbackChart"></canvas>
                    </div>
                </div>

                <div class="summary-card finance-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="card-info">
                            <span class="number">RS <?php echo number_format($data['monthlyRevenue'] ?? 0); ?></span>
                            <span class="label">Monthly Revenue</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Academy Calendar -->
            <div class="calendar-section" style="margin-top:30px;">
                <style>
                    .slot-cal-cell      { border:1px solid #e0e0e0; vertical-align:top; min-height:160px; width:14.28%; padding:8px; background:#fff; }
                    .slot-cal-outside   { background:#fafafa; color:#999; }
                    .slot-cal-today     { background:#fffde7; }
                    .slot-cal-day-num   { display:flex; align-items:center; justify-content:space-between; font-size:12px; font-weight:700; margin-bottom:8px; }
                    .slot-cal-card      { border-radius:6px; padding:6px 8px; margin-bottom:6px; font-size:12px; cursor:pointer; text-decoration:none; display:block; }
                    .slot-cal-program   { background:#cce5ff; color:#004085; border-left:3px solid #004085; }
                    .slot-cal-private   { background:#fff3cd; color:#856404; border-left:3px solid #856404; }
                    .slot-cal-facility  { background:#d4edda; color:#155724; border-left:3px solid #155724; }
                    .slot-cal-cancelled { background:#e9ecef; color:#6c757d; border-left:3px solid #aaa; text-decoration:line-through; }
                    .slot-cal-adhoc     { background:#f3e5f5; color:#4a1e8c; border-left:3px solid #9b59b6; }
                    .slot-cal-tournament { background:#fff1d6; color:#8a4b00; border-left:3px solid #e67e22; }
                </style>
                <div class="calendar-header">
                    <h3><i class="fas fa-calendar-alt"></i> Academy Calendar</h3>
                    <div class="calendar-controls">
                        <div class="calendar-nav">
                            <a href="<?php echo URLROOT; ?>/admin/dashboard?slot_month=<?= $data['slotPrevMonth'] ?>"
                               class="calendar-btn" title="Previous Month"><i class="fas fa-chevron-left"></i></a>
                            <span style="font-weight:700;font-size:14px;color:#2c3e50;">
                                <?= date('F Y', $data['slotMonthTs']) ?>
                            </span>
                            <a href="<?php echo URLROOT; ?>/admin/dashboard?slot_month=<?= $data['slotNextMonth'] ?>"
                               class="calendar-btn" title="Next Month"><i class="fas fa-chevron-right"></i></a>
                            <a href="<?php echo URLROOT; ?>/admin/dashboard"
                               class="calendar-btn today-btn" title="Current Month" style="margin-left:8px;"><i class="fas fa-calendar-check"></i></a>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:12px;margin:12px 0 16px;flex-wrap:wrap;font-size:12px;">
                    <span style="background:#cce5ff;color:#004085;padding:3px 10px;border-radius:10px;">Program</span>
                    <span style="background:#fff3cd;color:#856404;padding:3px 10px;border-radius:10px;">Private</span>
                    <span style="background:#d4edda;color:#155724;padding:3px 10px;border-radius:10px;">Facility Only</span>
                    <span style="background:#f3e5f5;color:#4a1e8c;padding:3px 10px;border-radius:10px;">Ad-hoc</span>
                    <span style="background:#fff1d6;color:#8a4b00;padding:3px 10px;border-radius:10px;">Tournament</span>
                    <span style="background:#e9ecef;color:#6c757d;padding:3px 10px;border-radius:10px;text-decoration:line-through;">Cancelled</span>
                </div>

                <div style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $slotDayName): ?>
                                <th style="padding:10px 8px;text-align:center;font-size:13px;color:#555;border-bottom:2px solid #dee2e6;">
                                    <div style="font-weight:700;"><?= $slotDayName ?></div>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $slotMonthStartTs = strtotime($data['slotMonthFrom']);
                            $slotMonthEndTs = strtotime($data['slotMonthTo']);
                            $slotMonthStartDow = (int) date('N', $slotMonthStartTs);
                            $slotGridStartTs = strtotime('-' . ($slotMonthStartDow - 1) . ' days', $slotMonthStartTs);
                            $slotGridEndDow = (int) date('N', $slotMonthEndTs);
                            $slotGridEndTs = strtotime('+' . (7 - $slotGridEndDow) . ' days', $slotMonthEndTs);

                            for ($weekTs = $slotGridStartTs; $weekTs <= $slotGridEndTs; $weekTs = strtotime('+7 days', $weekTs)):
                            ?>
                            <tr>
                                <?php for ($si = 0; $si < 7; $si++):
                                    $dayTs = strtotime("+{$si} days", $weekTs);
                                    $dayKey = date('Y-m-d', $dayTs);
                                    $isToday = ($dayKey === date('Y-m-d'));
                                    $isCurrentMonth = (date('Y-m', $dayTs) === date('Y-m', $slotMonthStartTs));
                                    $soccs = $data['slotByDate'][$dayKey] ?? [];
                                    $tournaments = $data['tournamentsByDate'][$dayKey] ?? [];
                                ?>
                                <td class="slot-cal-cell<?= $isToday ? ' slot-cal-today' : '' ?><?= $isCurrentMonth ? '' : ' slot-cal-outside' ?>">
                                    <div class="slot-cal-day-num">
                                        <span><?= date('j', $dayTs) ?></span>
                                        <?php if (!$isCurrentMonth): ?>
                                            <span style="font-weight:600;font-size:10px;opacity:.65;"><?= date('M', $dayTs) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (empty($soccs) && empty($tournaments)): ?>
                                        <div style="color:#ccc;font-size:11px;text-align:center;padding-top:16px;">&mdash;</div>
                                    <?php else: ?>
                                        <?php foreach ($soccs as $socc):
                                            if ($socc->Status === 'cancelled') {
                                                $scls = 'slot-cal-cancelled';
                                            } elseif ($socc->TemplateID === null) {
                                                $scls = 'slot-cal-adhoc';
                                            } else {
                                                $smap = ['program'=>'slot-cal-program','private'=>'slot-cal-private','facility_only'=>'slot-cal-facility'];
                                                $scls = $smap[$socc->SlotType] ?? 'slot-cal-program';
                                            }
                                        ?>
                                        <a href="<?php echo URLROOT; ?>/adminslots/occurrence/<?= $socc->OccurrenceID ?>" class="slot-cal-card <?= $scls ?>">
                                            <div style="font-weight:600;"><?= htmlspecialchars($socc->TemplateName ?? 'Ad-hoc') ?></div>
                                            <div><?= htmlspecialchars($socc->SlotLabel ?? '') ?></div>
                                            <?php if (!empty($socc->FacilityName)): ?>
                                                <div><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> <?= htmlspecialchars($socc->FacilityName) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($socc->StaffNames)): ?>
                                                <div><i class="fas fa-user-tie" style="font-size:10px;"></i> <?= htmlspecialchars($socc->StaffNames) ?></div>
                                            <?php endif; ?>
                                            <div style="margin-top:3px;">
                                                <i class="fas fa-users" style="font-size:10px;"></i> <?= (int)$socc->BookingCount ?> booked
                                            </div>
                                        </a>
                                        <?php endforeach; ?>

                                        <?php foreach ($tournaments as $tournament): ?>
                                        <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?= (int)$tournament->TournamentID ?>" class="slot-cal-card slot-cal-tournament">
                                            <div style="font-weight:600;"><i class="fas fa-trophy"></i> <?= htmlspecialchars($tournament->Name) ?></div>
                                            <div><?= htmlspecialchars(trim(($tournament->AgeGroup ?? '') . (!empty($tournament->Format) ? ' · ' . $tournament->Format : ''))) ?></div>
                                            <?php if (!empty($tournament->Location)): ?>
                                                <div><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> <?= htmlspecialchars($tournament->Location) ?></div>
                                            <?php endif; ?>
                                            <div style="margin-top:3px;">
                                                <i class="fas fa-info-circle" style="font-size:10px;"></i> <?= htmlspecialchars($tournament->Status ?? 'created') ?>
                                            </div>
                                        </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="recent-activity-section">
                <div class="section-header">
                    <h3><i class="fas fa-history"></i> Recent Activities</h3>
                    <div class="activity-filters">
                        <select id="activityTypeFilter" class="filter-select">
                            <option value="all">All Activities</option>
                            <option value="registration">Registration</option>
                            <option value="event">Events</option>
                            <option value="feedback">Feedback</option>
                            <option value="payment">Payments</option>
                            <option value="staff">Staff</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <select id="activityTimeFilter" class="filter-select">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                        <button class="btn-filter-clear" onclick="clearActivityFilters()">
                            <i class="fas fa-redo"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Activity Type</th>
                                <th>Description</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="activityTableBody">
                            <?php if(!empty($data['recentActivities'])): ?>
                                <?php foreach($data['recentActivities'] as $activity): ?>
                                    <tr>
                                        <td>
                                            <span class="activity-badge <?php echo strtolower($activity->action); ?>">
                                                <i class="fas fa-<?php 
                                                    // Map activity action to icon
                                                    $icon = 'info-circle'; // default
                                                    if(stripos($activity->action, 'login') !== false) $icon = 'sign-in-alt';
                                                    elseif(stripos($activity->action, 'register') !== false || stripos($activity->action, 'created') !== false) $icon = 'user-plus';
                                                    elseif(stripos($activity->action, 'update') !== false || stripos($activity->action, 'edit') !== false) $icon = 'edit';
                                                    elseif(stripos($activity->action, 'delete') !== false) $icon = 'trash';
                                                    elseif(stripos($activity->action, 'event') !== false) $icon = 'calendar-alt';
                                                    elseif(stripos($activity->action, 'feedback') !== false) $icon = 'comment';
                                                    echo $icon;
                                                ?>"></i> 
                                                <?php echo htmlspecialchars($activity->action); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($activity->details); ?></td>
                                        <td><?php echo htmlspecialchars($activity->timestamp); ?></td>
                                        <td><span class="status-badge active">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fas fa-info-circle"></i> No recent activities found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions - 3 Buttons Per Row, 2 Rows -->
            <div class="quick-actions-section">
                <div class="section-header">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="quick-actions-grid">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="action-btn primary">
                        <i class="fas fa-user-plus"></i>
                        <span>Add New Player</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/admin/events" class="action-btn secondary">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Schedule Event</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="action-btn success">
                        <i class="fas fa-file-alt"></i>
                        <span>Generate Report</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="action-btn warning">
                        <i class="fas fa-user-tie"></i>
                        <span>Add New Staff</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="action-btn info">
                        <i class="fas fa-chart-line"></i>
                        <span>Manage Finance</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/admin/feedback" class="action-btn danger">
                        <i class="fas fa-comments"></i>
                        <span>Review Feedback</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript for Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Ensure Chart.js is loaded before proceeding
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');
        window.chartJsLoaded = typeof Chart !== 'undefined';
        console.log('URLROOT for JS files: <?php echo URLROOT; ?>');
    </script>
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/common/tournaments.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/dashboard.js"></script>
    
    <!-- Activity Filters JavaScript -->
    <script>
        // Activity Filter Functions
        function filterActivities() {
            const typeFilter = document.getElementById('activityTypeFilter').value;
            const timeFilter = document.getElementById('activityTimeFilter').value;
            const rows = document.querySelectorAll('#activityTableBody tr');
            
            rows.forEach(row => {
                let showRow = true;
                
                // Type filter
                if (typeFilter !== 'all') {
                    const activityType = row.querySelector('.activity-badge').textContent.trim().toLowerCase();
                    if (!activityType.includes(typeFilter.toLowerCase())) {
                        showRow = false;
                    }
                }
                
                // Time filter
                if (timeFilter !== 'all' && showRow) {
                    const dateText = row.cells[2].textContent.trim();
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Parse the date from cell text (expect formats like "Feb 17, 2026" or "2026-02-17")
                    const rowDate = new Date(dateText);
                    if (!isNaN(rowDate.getTime())) {
                        rowDate.setHours(0, 0, 0, 0);
                        if (timeFilter === 'today') {
                            showRow = rowDate.getTime() === today.getTime();
                        } else if (timeFilter === 'week') {
                            const weekAgo = new Date(today);
                            weekAgo.setDate(weekAgo.getDate() - 7);
                            showRow = rowDate >= weekAgo && rowDate <= today;
                        } else if (timeFilter === 'month') {
                            showRow = rowDate.getMonth() === today.getMonth() && rowDate.getFullYear() === today.getFullYear();
                        }
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }
        
        function clearActivityFilters() {
            document.getElementById('activityTypeFilter').value = 'all';
            document.getElementById('activityTimeFilter').value = 'all';
            filterActivities();
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const typeFilter = document.getElementById('activityTypeFilter');
            const timeFilter = document.getElementById('activityTimeFilter');
            
            if (typeFilter) typeFilter.addEventListener('change', filterActivities);
            if (timeFilter) timeFilter.addEventListener('change', filterActivities);
        });
    </script>
    
    <!-- Chart.js Test -->
    <script>
        console.log('Chart.js test script running...');
        console.log('Chart available:', typeof Chart);
        
        // Test if we can find the canvas elements
        setTimeout(function() {
            const staffCanvas = document.getElementById('staffChart');
            console.log('Staff canvas found:', !!staffCanvas);
            if (staffCanvas) {
                console.log('Staff canvas dimensions:', staffCanvas.width, 'x', staffCanvas.height);
                console.log('Staff canvas parent:', staffCanvas.parentElement);
            }
        }, 1000);
    </script>
</body>

</html>
