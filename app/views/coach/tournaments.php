<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournaments.css">

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
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
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
                            <i class="fas fa-trophy"></i>
                            Tournaments & Team Recommendations
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Manage tournament participation and player recommendations</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-primary" id="recommendPlayerBtn">
                            <i class="fas fa-user-plus"></i>
                            Recommend Player
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tournament Content -->
            <div class="tournament-content">
                <!-- Tournament Filters -->
                <div class="tournament-filters">
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="statusFilter" class="filter-select">
                            <option value="all">All Tournaments</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Category:</label>
                        <select id="categoryFilter" class="filter-select">
                            <option value="all">All Categories</option>
                            <option value="junior">Junior (U-15)</option>
                            <option value="senior">Senior (U-19)</option>
                            <option value="open">Open</option>
                        </select>
                    </div>
                    <div class="search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="tournamentSearch" placeholder="Search tournaments...">
                    </div>
                </div>

                <!-- Tournaments Grid -->
                <div class="tournaments-grid" id="tournamentsGrid">
                    <!-- Tournaments will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Recommend Player Modal -->
    <div class="modal" id="recommendModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Recommend Player for Tournament</h2>
                <button class="modal-close" id="closeRecommendModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="recommendForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournamentSelect">Select Tournament *</label>
                            <select id="tournamentSelect" required>
                                <option value="">Choose tournament...</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="playerSearch">Search Player *</label>
                            <input type="text" id="playerSearch" placeholder="Type player name...">
                            <div class="player-search-results" id="playerSearchResults"></div>
                        </div>
                    </div>

                    <div class="selected-players" id="selectedPlayers">
                        <!-- Selected players will appear here -->
                    </div>

                    <div class="form-group">
                        <label for="recommendationReason">Recommendation Reason</label>
                        <textarea id="recommendationReason" rows="4" placeholder="Why are you recommending this player?"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelRecommend">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Submit Recommendation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tournament Details Modal -->
    <div class="modal" id="tournamentDetailsModal">
        <div class="modal-content extra-large">
            <div class="modal-header">
                <h2 id="tournamentModalTitle">Tournament Details</h2>
                <button class="modal-close" id="closeTournamentModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="tournamentDetailsBody">
                <!-- Tournament details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Player Stats Modal -->
    <div class="modal" id="playerStatsModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2 id="playerStatsTitle">Player Statistics</h2>
                <button class="modal-close" id="closePlayerStatsModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="playerStatsBody">
                <!-- Player stats will be loaded here -->
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach-tournaments.js"></script>

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
