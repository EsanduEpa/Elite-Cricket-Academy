<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/players-management.css">

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
                    <li class="nav-item">
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
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                            <i class="fas fa-user-graduate"></i>
                            <span>Player Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Events & Tournaments</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Feedback Monitoring</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Finance Management</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Admin Profile -->
            <div class="admin-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
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
        
        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-user-graduate"></i> Player Management</h1>
                        <p>Monitor player statistics, performance, and account status</p>
                    </div>
                    <div class="header-actions">
                        <div class="current-time" id="currentTime"></div>
                    </div>
                </div>
            </div>

            <!-- Player Management Content -->
            <div class="content-wrapper">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $data['stats']->total_players ?? 0; ?></h3>
                            <p>Total Players</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #06d6a0 0%, #118ab2 100%);">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $data['stats']->active_players ?? 0; ?></h3>
                            <p>Active Players</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $data['stats']->premium_subscription ?? 0; ?></h3>
                            <p>Premium Members</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $data['stats']->basic_subscription ?? 0; ?></h3>
                            <p>Basic Members</p>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search Section -->
                <div class="filter-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="playerSearch" placeholder="Search by name, email, or jersey number...">
                    </div>
                    
                    <div class="filter-controls">
                        <select id="statusFilter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        
                        <select id="subscriptionFilter" class="filter-select">
                            <option value="all">All Subscriptions</option>
                            <option value="premium">Premium</option>
                            <option value="standard">Standard</option>
                            <option value="trial">Trial</option>
                        </select>
                        
                        <select id="battingFilter" class="filter-select">
                            <option value="all">All Batting Styles</option>
                            <option value="right-handed">Right-Handed</option>
                            <option value="left-handed">Left-Handed</option>
                        </select>
                        
                        <button class="btn-reset" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Players Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-table"></i> Players List</h2>
                        <div class="table-actions">
                            <button class="btn-export" onclick="exportPlayers()">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    
                    <table class="data-table" id="playersTable">
                        <thead>
                            <tr>
                                <th>Player ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Batting Style</th>
                                <th>Subscription</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['players'])): ?>
                                <?php foreach ($data['players'] as $index => $player): 
                                    // Get player initials for avatar
                                    $nameParts = explode(' ', $player->Name);
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                                    
                                    // Format subscription badge class
                                    $subClass = 'badge-' . strtolower($player->SubscriptionType ?? 'basic');
                                    
                                    // Format status badge class
                                    $statusClass = 'status-' . strtolower($player->Status ?? 'active');
                                ?>
                                <tr data-player-id="<?php echo $player->UserID; ?>">
                                    <td>#PLR<?php echo str_pad($player->UserID, 3, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <div class="player-info">
                                            <div class="player-avatar"><?php echo $initials; ?></div>
                                            <span><?php echo htmlspecialchars($player->Name); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($player->Email); ?></td>
                                    <td>
                                        <?php if ($player->BattingStyle): ?>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($player->BattingStyle); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?php echo $subClass; ?>"><?php echo ucfirst($player->SubscriptionType ?? 'basic'); ?></span></td>
                                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($player->Status ?? 'active'); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="window.location.href='<?php echo URLROOT; ?>/admin/player_statistics/<?php echo $player->UserID; ?>'" title="View Statistics">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                            <?php if (strtolower($player->Status) === 'active'): ?>
                                                <button class="btn-action btn-suspend" onclick="openSuspendModal(<?php echo $player->UserID; ?>, '<?php echo htmlspecialchars($player->Name, ENT_QUOTES); ?>')" title="Suspend Player">
                                                    <i class="fas fa-user-lock"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action btn-unsuspend" onclick="unsuspendPlayer(<?php echo $player->UserID; ?>, '<?php echo htmlspecialchars($player->Name, ENT_QUOTES); ?>')" title="Unsuspend Player">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn-action btn-delete" onclick="openDeleteModal(<?php echo $player->UserID; ?>, '<?php echo htmlspecialchars($player->Name, ENT_QUOTES); ?>')" title="Remove Player">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">
                                        <div style="color: #999;">
                                            <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                                            <p style="margin: 0; font-size: 16px;">No players found in the system</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="pagination">
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">4</button>
                        <button class="page-btn">5</button>
                        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspend Player Modal -->
    <div class="modal" id="suspendModal" style="display: none;">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-lock"></i> Suspend Player Account</h2>
                <button class="modal-close" onclick="closeSuspendModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="warning-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>You are about to suspend <strong id="suspendPlayerName"></strong>. The player will not be able to login during the suspension period.</p>
                </div>
                
                <form id="suspendForm">
                    <input type="hidden" id="suspendPlayerId">
                    
                    <div class="form-group">
                        <label for="suspensionDuration">Suspension Duration</label>
                        <select id="suspensionDuration" class="form-control">
                            <option value="24">24 Hours</option>
                            <option value="48">48 Hours</option>
                            <option value="72">3 Days</option>
                            <option value="168">1 Week</option>
                            <option value="336">2 Weeks</option>
                            <option value="720">1 Month</option>
                            <option value="custom">Custom Duration</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="customDurationGroup" style="display: none;">
                        <label for="customHours">Custom Hours</label>
                        <input type="number" id="customHours" class="form-control" min="1" placeholder="Enter hours">
                    </div>
                    
                    <div class="form-group">
                        <label for="suspensionReason">Reason for Suspension <span class="required">*</span></label>
                        <textarea id="suspensionReason" class="form-control" rows="4" required placeholder="Provide a detailed reason for suspension..."></textarea>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeSuspendModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-user-lock"></i> Suspend Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Player Modal -->
    <div class="modal" id="deleteModal" style="display: none;">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-trash"></i> Remove Player</h2>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="danger-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Are you sure you want to remove <strong id="deletePlayerName"></strong>?</p>
                    <p class="warning-text">This action cannot be undone. All player data, statistics, and records will be permanently deleted.</p>
                </div>
                
                <form id="deleteForm">
                    <input type="hidden" id="deletePlayerId">
                    
                    <div class="form-group">
                        <label for="deleteConfirmation">Type "DELETE" to confirm</label>
                        <input type="text" id="deleteConfirmation" class="form-control" placeholder="Type DELETE" required>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                            <i class="fas fa-trash"></i> Delete Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/admin/players-management.js"></script>
<script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
