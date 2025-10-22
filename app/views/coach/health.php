<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-health.css">

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
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/schedules" class="nav-link" data-tooltip="Schedules">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedules</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1>
                            <i class="fas fa-heartbeat"></i>
                            Health & Injury Monitoring
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Track player health status and injury recovery</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-primary" id="addInjuryReportBtn">
                            <i class="fas fa-plus-circle"></i>
                            Add Injury Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Health Content -->
            <div class="health-content">
                <!-- Health Statistics Cards -->
                <div class="health-stats-grid">
                    <div class="stat-card active-injuries">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="activeInjuriesCount">0</h3>
                            <p>Active Injuries</p>
                        </div>
                    </div>
                    
                    <div class="stat-card recovering">
                        <div class="stat-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="recoveringCount">0</h3>
                            <p>In Recovery</p>
                        </div>
                    </div>
                    
                    <div class="stat-card cleared">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="clearedCount">0</h3>
                            <p>Cleared</p>
                        </div>
                    </div>
                    
                    <div class="stat-card fitness-evaluation">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="evaluationCount">0</h3>
                            <p>Pending Evaluation</p>
                        </div>
                    </div>
                </div>

                <!-- Health Filters -->
                <div class="health-filters">
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="statusFilter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="injured">Injured</option>
                            <option value="recovering">Recovering</option>
                            <option value="cleared">Cleared</option>
                            <option value="under_observation">Under Observation</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Severity:</label>
                        <select id="severityFilter" class="filter-select">
                            <option value="all">All Severity</option>
                            <option value="critical">Critical</option>
                            <option value="moderate">Moderate</option>
                            <option value="minor">Minor</option>
                        </select>
                    </div>
                    <div class="search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="playerSearch" placeholder="Search players...">
                    </div>
                </div>

                <!-- Health Records Table -->
                <div class="health-records-container">
                    <table class="health-records-table" id="healthRecordsTable">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Injury Type</th>
                                <th>Date Reported</th>
                                <th>Status</th>
                                <th>Severity</th>
                                <th>Recovery Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="healthRecordsBody">
                            <!-- Records will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    

    
    

<script src="<?php echo URLROOT; ?>/js/coach-health.js"></script>

<script>
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
