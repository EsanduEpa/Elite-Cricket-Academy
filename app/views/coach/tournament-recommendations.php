<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/tournament-recommendations.css">

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
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>

                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link" data-tooltip="Recommendations">
                        <i class="fas fa-star"></i>
                        <span>Recommendations</span>
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
                        <i class="fas fa-star"></i>
                        Tournament Recommendations
                    </h1>
                    <p class="coach-recommendations-subtitle">Recommend your players for upcoming tournaments</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" id="newRecommendationBtn">
                        <i class="fas fa-plus"></i> New Recommendation
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon stat-icon--pending">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $data['stats']['pending'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-icon--approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $data['stats']['approved'] ?? 0; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-icon--rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $data['stats']['rejected'] ?? 0; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-icon--total">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo count($data['recommendations'] ?? []) ?? 0; ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filter-group">
                <label>Status</label>
                <select id="filterStatus" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Sort By</label>
                <select id="filterSort" class="filter-select">
                    <option value="date_desc">Latest First</option>
                    <option value="date_asc">Oldest First</option>
                    <option value="tournament">Tournament</option>
                    <option value="player">Player Name</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="filterSearch" class="filter-input" placeholder="Search by player, tournament...">
            </div>

            <button class="btn btn-outline" id="clearFiltersBtn">
                <i class="fas fa-undo"></i> Clear
            </button>
        </div>

        <!-- Recommendations List -->
        <div class="recommendations-container">
            <?php if (!empty($data['recommendations'])): ?>
                <div class="recommendations-grid" id="recommendationsGrid">
                    <?php foreach ($data['recommendations'] as $rec): 
                        $statusClass = 'status-' . strtolower($rec->Status ?? 'pending');
                        $statusColor = [
                            'pending' => '#2196F3',
                            'approved' => '#4CAF50',
                            'rejected' => '#f44336'
                        ][$rec->Status ?? 'pending'] ?? '#666';
                    ?>
                    <div class="recommendation-card" data-status="<?php echo $rec->Status ?? 'pending'; ?>" data-tournament="<?php echo htmlspecialchars($rec->TournamentName ?? ''); ?>" data-player="<?php echo htmlspecialchars($rec->PlayerName ?? ''); ?>">
                        <!-- Card Header -->
                        <div class="card-header">
                            <div class="card-title">
                                <h3><?php echo htmlspecialchars($rec->PlayerName ?? 'N/A'); ?></h3>
                                <span class="status-badge <?php echo $statusClass; ?>" style="background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>;">
                                    <?php echo ucfirst($rec->Status ?? 'pending'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-item">
                                    <label>Tournament</label>
                                    <p><?php echo htmlspecialchars($rec->TournamentName ?? 'N/A'); ?></p>
                                </div>
                                <div class="info-item">
                                    <label>Role</label>
                                    <p class="role-badge"><?php echo ucfirst(str_replace('-', ' ', $rec->RecommendedRole ?? 'N/A')); ?></p>
                                </div>
                            </div>

                            <?php if (!empty($rec->Reason)): ?>
                            <div class="info-section">
                                <label>Reason</label>
                                <p><?php echo htmlspecialchars($rec->Reason); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($rec->Comments)): ?>
                            <div class="info-section">
                                <label>Comments</label>
                                <p><?php echo htmlspecialchars($rec->Comments); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($rec->AdminFeedback) && in_array($rec->Status, ['approved', 'rejected'])): ?>
                            <div class="admin-feedback">
                                <label>Admin Feedback</label>
                                <p><?php echo htmlspecialchars($rec->AdminFeedback); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="card-meta">
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M d, Y', strtotime($rec->DateRecommended ?? 'now')); ?>
                                </span>
                                <?php if (!empty($rec->DateReviewed)): ?>
                                <span class="meta-item">
                                    <i class="fas fa-check"></i>
                                    Reviewed <?php echo date('M d, Y', strtotime($rec->DateReviewed)); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="card-actions">
                            <?php if ($rec->Status === 'pending'): ?>
                                <button class="btn btn-sm btn-secondary edit-btn" data-id="<?php echo $rec->RecommendationID; ?>" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $rec->RecommendationID; ?>" title="Delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php else: ?>
                                <div class="status-locked">
                                    <i class="fas fa-lock"></i> <?php echo ucfirst($rec->Status); ?> - Cannot Edit
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Recommendations Yet</h3>
                    <p>Start by recommending your players for upcoming tournaments</p>
                    <button class="btn btn-primary" id="emptyStateBtn">
                        <i class="fas fa-plus"></i> Create First Recommendation
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: New/Edit Recommendation -->
<div class="modal" id="recommendationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">New Recommendation</h2>
            <button class="modal-close" id="modalCloseBtn">&times;</button>
        </div>

        <div class="modal-body">
            <form id="recommendationForm">
                <!-- Tournament Selection -->
                <div class="form-group">
                    <label for="tournamentSelect">Tournament <span class="required">*</span></label>
                    <select id="tournamentSelect" name="tournament" class="form-control" required>
                        <option value="">Select a tournament...</option>
                    </select>
                    <div class="form-error" id="tournamentError"></div>
                </div>

                <!-- Player Selection -->
                <div class="form-group">
                    <label for="playerSelect">Player <span class="required">*</span></label>
                    <select id="playerSelect" name="player" class="form-control" required>
                        <option value="">Select a player...</option>
                    </select>
                    <div class="form-error" id="playerError"></div>
                </div>

                <!-- Role Selection -->
                <div class="form-group">
                    <label for="roleSelect">Recommended Role <span class="required">*</span></label>
                    <select id="roleSelect" name="role" class="form-control" required>
                        <option value="">Select a role...</option>
                        <option value="batsman">Batsman - Primary batting focus</option>
                        <option value="bowler">Bowler - Primary bowling focus</option>
                        <option value="all-rounder">All-rounder - Both batting and bowling</option>
                        <option value="wicket-keeper">Wicket-keeper - Wicket-keeping specialist</option>
                    </select>
                    <div class="form-error" id="roleError"></div>
                </div>

                <!-- Reason -->
                <div class="form-group">
                    <label for="reasonInput">Reason</label>
                    <textarea id="reasonInput" name="reason" class="form-control" rows="3" placeholder="Why do you recommend this player for this role?"></textarea>
                </div>

                <!-- Comments -->
                <div class="form-group">
                    <label for="commentsInput">Additional Comments</label>
                    <textarea id="commentsInput" name="comments" class="form-control" rows="3" placeholder="Any additional comments or notes..."></textarea>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <span id="submitBtnText">Save Recommendation</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" id="modalOverlay"></div>

<!-- Loading Spinner -->
<div class="loading-spinner" id="loadingSpinner">
    <div class="spinner"></div>
    <p>Loading...</p>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script>
window.APP_URLROOT = <?php echo json_encode(URLROOT); ?>;
</script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/tournament-recommendations.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
