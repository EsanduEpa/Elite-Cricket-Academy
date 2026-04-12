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

                <!-- Filters and Search -->
                <div class="filters-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="playerSearch" placeholder="Search by name, email, or jersey number...">
                    </div>
                    <div class="filter-options">
                        <select id="statusFilter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <select id="subscriptionFilter" class="filter-select">
                            <option value="all">All Subscriptions</option>
                            <option value="premium">Premium</option>
                            <option value="basic">Basic</option>
                            <option value="private_only">Private Only</option>
                        </select>
                        <select id="battingFilter" class="filter-select">
                            <option value="all">All Batting Styles</option>
                            <option value="right">Right-Handed</option>
                            <option value="left">Left-Handed</option>
                        </select>
                        <button class="btn-secondary" id="exportBtn">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Players Table -->
                <div class="staff-table-section">
                    <table class="staff-table" id="playersTable">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Player</th>
                                <th>Jersey #</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Batting Style</th>
                                <th>Subscription</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="playersTableBody">
                            <?php if (!empty($data['players'])): ?>
                                <?php foreach ($data['players'] as $player): ?>
                            <tr>
                                <td><input type="checkbox" class="player-checkbox"></td>
                                <td>
                                    <div class="staff-info">
                                        <h4><?php echo htmlspecialchars($player->Name); ?></h4>
                                        <p><?php echo $player->Age ?? 'N/A'; ?> years old</p>
                                    </div>
                                </td>
                                <td><?php echo $player->JerseyNumber ? '#' . $player->JerseyNumber : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($player->Email); ?></td>
                                <td><?php echo htmlspecialchars($player->PhoneNumber ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($player->BattingStyle): ?>
                                        <span class="role-badge"><?php echo htmlspecialchars($player->BattingStyle); ?></span>
                                    <?php else: ?>
                                        <span class="role-badge">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="role-badge <?php echo strtolower($player->SubscriptionType ?? 'basic'); ?>"><?php echo ucfirst($player->SubscriptionType ?? 'Basic'); ?></span></td>
                                <td><span class="status-badge <?php echo strtolower($player->Status ?? 'active'); ?>"><?php echo ucfirst($player->Status ?? 'active'); ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn view" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($player->Name); ?>"
                                                data-player-email="<?php echo htmlspecialchars($player->Email); ?>"
                                                data-player-phone="<?php echo htmlspecialchars($player->PhoneNumber ?? 'N/A'); ?>"
                                                data-player-address="<?php echo htmlspecialchars($player->Address ?? 'N/A'); ?>"
                                                data-player-jersey="<?php echo $player->JerseyNumber ?? ''; ?>"
                                                data-player-batting="<?php echo htmlspecialchars($player->BattingStyle ?? 'Not set'); ?>"
                                                data-player-bowling="<?php echo htmlspecialchars($player->BowlingStyle ?? 'Not set'); ?>"
                                                data-player-subscription="<?php echo htmlspecialchars($player->SubscriptionType ?? 'basic'); ?>"
                                                data-player-status="<?php echo htmlspecialchars($player->Status ?? 'active'); ?>"
                                                data-player-joined="<?php echo date('M d, Y', strtotime($player->DateJoined)); ?>"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn edit" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($player->Name); ?>"
                                                data-player-email="<?php echo htmlspecialchars($player->Email); ?>"
                                                data-player-phone="<?php echo htmlspecialchars($player->PhoneNumber ?? ''); ?>"
                                                data-player-address="<?php echo htmlspecialchars($player->Address ?? ''); ?>"
                                                data-player-jersey="<?php echo $player->JerseyNumber ?? ''; ?>"
                                                data-player-batting="<?php echo htmlspecialchars($player->BattingStyle ?? ''); ?>"
                                                data-player-bowling="<?php echo htmlspecialchars($player->BowlingStyle ?? ''); ?>"
                                                data-player-subscription="<?php echo htmlspecialchars($player->SubscriptionType ?? 'basic'); ?>"
                                                data-player-status="<?php echo htmlspecialchars($player->Status ?? 'active'); ?>"
                                                title="Edit Player">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn suspend" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($player->Name); ?>"
                                                data-player-status="<?php echo htmlspecialchars($player->Status ?? 'active'); ?>"
                                                title="<?php echo strtolower($player->Status ?? 'active') === 'active' ? 'Suspend Player' : 'Unsuspend Player'; ?>">
                                            <i class="fas fa-<?php echo strtolower($player->Status ?? 'active') === 'active' ? 'user-lock' : 'user-check'; ?>"></i>
                                        </button>
                                        <button class="action-btn delete" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($player->Name); ?>"
                                                title="Delete Player">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-users" style="font-size: 48px; color: #ddd; margin-bottom: 10px;"></i>
                                    <p style="color: #999;">No players found</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="pagination-section">
                        <div class="pagination-info">
                            Showing <span id="showingStart">1</span> to <span id="showingEnd"><?php echo min(10, count($data['players'] ?? [])); ?></span> of <span id="totalPlayers"><?php echo count($data['players'] ?? []); ?></span> players
                        </div>
                        <div class="pagination-controls">
                            <button class="page-btn" id="prevPage">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="page-btn active">1</button>
                            <button class="page-btn">2</button>
                            <button class="page-btn">3</button>
                            <button class="page-btn">4</button>
                            <button class="page-btn">5</button>
                            <button class="page-btn" id="nextPage">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Player Action Button -->
                <div class="add-staff-action">
                    <button class="btn btn-primary btn-large" id="addPlayerBtn">
                        <i class="fas fa-user-plus"></i> Add New Player
                    </button>
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

    <!-- View Player Modal -->
    <div class="modal" id="viewPlayerModal">
        <div class="modal-overlay" id="viewModalOverlay"></div>
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2><i class="fas fa-user-circle"></i> Player Details</h2>
                <button class="modal-close" id="closeViewModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="staff-details-container">
                    <div class="staff-header-section">
                        <div class="staff-avatar-large">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="staff-header-info">
                            <h3 id="viewPlayerName">-</h3>
                            <p class="staff-role-badge" id="viewPlayerJerseyBadge">-</p>
                            <p class="staff-status" id="viewPlayerStatusBadge">-</p>
                        </div>
                    </div>

                    <div class="details-grid">
                        <div class="detail-section">
                            <h4><i class="fas fa-id-card"></i> Personal Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Full Name:</span>
                                <span class="detail-value" id="viewFullName">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value" id="viewEmail">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value" id="viewPhone">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date Joined:</span>
                                <span class="detail-value" id="viewJoined">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Address:</span>
                                <span class="detail-value" id="viewAddress">-</span>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h4><i class="fas fa-cricket"></i> Cricket Details</h4>
                            <div class="detail-item">
                                <span class="detail-label">Jersey Number:</span>
                                <span class="detail-value" id="viewJerseyNumber">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Batting Style:</span>
                                <span class="detail-value" id="viewBattingStyle">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Bowling Style:</span>
                                <span class="detail-value" id="viewBowlingStyle">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Subscription Type:</span>
                                <span class="detail-value" id="viewSubscription">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value" id="viewStatus">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="var modal = document.getElementById('viewPlayerModal'); modal.classList.remove('active'); modal.style.display = 'none';">
                            Close
                        </button>
                        <button type="button" class="btn-primary" id="editFromViewBtn">
                            <i class="fas fa-edit"></i> Edit Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Player Modal -->
    <div class="modal" id="editPlayerModal">
        <div class="modal-overlay" id="editModalOverlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Player</h2>
                <button class="modal-close" id="closeEditModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPlayerForm">
                    <input type="hidden" id="editPlayerId" name="playerId">
                    
                    <div class="form-section">
                        <h4><i class="fas fa-id-card"></i> Personal Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPlayerFirstName">
                                    <i class="fas fa-user"></i> First Name <span class="required">*</span>
                                </label>
                                <input type="text" id="editPlayerFirstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="editPlayerLastName">
                                    <i class="fas fa-user"></i> Last Name <span class="required">*</span>
                                </label>
                                <input type="text" id="editPlayerLastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPlayerEmail">
                                    <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                                </label>
                                <input type="email" id="editPlayerEmail" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="editPlayerPhone">
                                    <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                                </label>
                                <input type="tel" id="editPlayerPhone" name="phone" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="editPlayerAddress">
                                <i class="fas fa-map-marker-alt"></i> Address
                            </label>
                            <textarea id="editPlayerAddress" name="address" rows="2" 
                                      placeholder="Enter full address"></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-cricket"></i> Cricket Details</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPlayerJersey">
                                    <i class="fas fa-hashtag"></i> Jersey Number
                                </label>
                                <input type="number" id="editPlayerJersey" name="jerseyNumber" min="1" max="999">
                            </div>
                            <div class="form-group">
                                <label for="editPlayerSubscription">
                                    <i class="fas fa-crown"></i> Subscription Type <span class="required">*</span>
                                </label>
                                <select id="editPlayerSubscription" name="subscriptionType" required>
                                    <option value="basic">Basic</option>
                                    <option value="premium">Premium</option>
                                    <option value="private_only">Private Only</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPlayerBatting">
                                    <i class="fas fa-baseball-bat"></i> Batting Style
                                </label>
                                <select id="editPlayerBatting" name="battingStyle">
                                    <option value="">Select Batting Style</option>
                                    <option value="Right-handed">Right-handed</option>
                                    <option value="Left-handed">Left-handed</option>
                                    <option value="Switch-hitter">Switch-hitter</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editPlayerBowling">
                                    <i class="fas fa-bowling-ball"></i> Bowling Style
                                </label>
                                <select id="editPlayerBowling" name="bowlingStyle">
                                    <option value="">Select Bowling Style</option>
                                    <option value="Fast">Fast</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Spin">Spin</option>
                                    <option value="Off-spin">Off-spin</option>
                                    <option value="Leg-spin">Leg-spin</option>
                                    <option value="None">None</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="editPlayerStatus">
                                <i class="fas fa-toggle-on"></i> Status <span class="required">*</span>
                            </label>
                            <select id="editPlayerStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelEditBtn">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Player Wizard Modal -->
    <div class="modal wizard-modal" id="addPlayerModal">
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-content wizard-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Add New Player</h2>
            </div>
            
            <!-- Wizard Progress -->
            <div class="wizard-progress">
                <div class="wizard-step active" data-step="1">
                    <div class="step-circle">1</div>
                    <span class="step-label">Player Information</span>
                </div>
                <div class="wizard-line"></div>
                <div class="wizard-step" data-step="2">
                    <div class="step-circle">2</div>
                    <span class="step-label">Review & Confirm</span>
                </div>
            </div>
            
            <div class="modal-body">
                <form id="addPlayerForm">
                    <!-- Step 1: Player Information -->
                    <div class="wizard-step-content active" data-step="1">
                        <h3><i class="fas fa-user-plus"></i> Player Information</h3>
                        <p class="step-description">Enter all details about the new player</p>
                        
                        <!-- Personal Details -->
                        <div class="form-section">
                            <h4><i class="fas fa-id-card"></i> Personal Details</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="playerFirstName">
                                        <i class="fas fa-user"></i> First Name <span class="required">*</span>
                                    </label>
                                    <input type="text" id="playerFirstName" name="firstName" required 
                                           placeholder="Enter first name">
                                </div>
                                <div class="form-group">
                                    <label for="playerLastName">
                                        <i class="fas fa-user"></i> Last Name <span class="required">*</span>
                                    </label>
                                    <input type="text" id="playerLastName" name="lastName" required 
                                           placeholder="Enter last name">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="playerDOB">
                                        <i class="fas fa-birthday-cake"></i> Date of Birth <span class="required">*</span>
                                    </label>
                                    <input type="date" id="playerDOB" name="dateOfBirth" max="<?php echo date('Y-m-d'); ?>" required>
                                    <small class="form-hint" style="display: block; font-size: 0.8rem; color: #666; margin-top: 0.25rem; font-style: italic;">Player must be at least 5 years old</small>
                                </div>
                                <div class="form-group">
                                    <label for="playerJersey">
                                        <i class="fas fa-hashtag"></i> Jersey Number
                                    </label>
                                    <input type="number" id="playerJersey" name="jerseyNumber" min="1" max="999"
                                           placeholder="e.g., 7">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-section">
                            <h4><i class="fas fa-address-book"></i> Contact Information</h4>
                            <div class="form-group">
                                <label for="playerEmail">
                                    <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                                </label>
                                <input type="email" id="playerEmail" name="email" required
                                       placeholder="player@example.com">
                                <small>This will be used for login and communication</small>
                            </div>

                            <div class="form-group">
                                <label for="playerPhone">
                                    <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                                </label>
                                <input type="tel" id="playerPhone" name="phone" required
                                       placeholder="+94 77 123 4567">
                            </div>

                            <div class="form-group">
                                <label for="playerAddress">
                                    <i class="fas fa-map-marker-alt"></i> Address
                                </label>
                                <textarea id="playerAddress" name="address" rows="2" 
                                          placeholder="Enter full residential address"></textarea>
                            </div>
                        </div>

                        <!-- Login Credentials -->
                        <div class="form-section">
                            <h4><i class="fas fa-key"></i> Login Credentials</h4>
                            <div class="form-group">
                                <label for="playerUsername">
                                    <i class="fas fa-user-circle"></i> Username <span class="required">*</span>
                                </label>
                                <input type="text" id="playerUsername" name="username" required
                                       placeholder="Enter username for login">
                                <small>Username must be unique</small>
                            </div>

                            <div class="info-box">
                                <i class="fas fa-info-circle"></i>
                                <p><strong>Default Password:</strong> The system will set the default password as <code>player123456</code>. The player should change this upon first login.</p>
                            </div>
                        </div>

                        <!-- Cricket Details -->
                        <div class="form-section">
                            <h4><i class="fas fa-cricket"></i> Cricket Details</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="playerBatting">
                                        <i class="fas fa-baseball-bat"></i> Batting Style
                                    </label>
                                    <select id="playerBatting" name="battingStyle">
                                        <option value="">Select Batting Style</option>
                                        <option value="Right-handed">Right-handed</option>
                                        <option value="Left-handed">Left-handed</option>
                                        <option value="Switch-hitter">Switch-hitter</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="playerBowling">
                                        <i class="fas fa-bowling-ball"></i> Bowling Style
                                    </label>
                                    <select id="playerBowling" name="bowlingStyle">
                                        <option value="">Select Bowling Style</option>
                                        <option value="Fast">Fast</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Spin">Spin</option>
                                        <option value="Off-spin">Off-spin</option>
                                        <option value="Leg-spin">Leg-spin</option>
                                        <option value="None">None</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="playerSubscription">
                                    <i class="fas fa-crown"></i> Subscription Type <span class="required">*</span>
                                </label>
                                <select id="playerSubscription" name="subscriptionType" required>
                                    <option value="">Select Subscription</option>
                                    <option value="basic">Basic</option>
                                    <option value="premium">Premium</option>
                                    <option value="private_only">Private Only</option>
                                </select>
                                <small>Select the subscription plan for this player</small>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Review & Confirm -->
                    <div class="wizard-step-content" data-step="2">
                        <h3><i class="fas fa-check-circle"></i> Review & Confirm</h3>
                        <p class="step-description">Review all information before adding</p>
                        
                        <div class="review-section">
                            <h4><i class="fas fa-user"></i> Personal Information</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Full Name:</span>
                                    <span class="review-value" id="reviewPlayerFullName">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Date of Birth:</span>
                                    <span class="review-value" id="reviewPlayerDOB">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Jersey Number:</span>
                                    <span class="review-value" id="reviewPlayerJersey">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <h4><i class="fas fa-address-book"></i> Contact Information</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Email:</span>
                                    <span class="review-value" id="reviewPlayerEmail">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Phone:</span>
                                    <span class="review-value" id="reviewPlayerPhone">-</span>
                                </div>
                                <div class="review-item full-width">
                                    <span class="review-label">Address:</span>
                                    <span class="review-value" id="reviewPlayerAddress">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <h4><i class="fas fa-key"></i> Login Credentials</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Username:</span>
                                    <span class="review-value" id="reviewPlayerUsername">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Default Password:</span>
                                    <span class="review-value"><code>player123456</code></span>
                                </div>
                            </div>
                        </div>

                        <div class="review-section">
                            <h4><i class="fas fa-cricket"></i> Cricket Details</h4>
                            <div class="review-grid">
                                <div class="review-item">
                                    <span class="review-label">Batting Style:</span>
                                    <span class="review-value" id="reviewPlayerBatting">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Bowling Style:</span>
                                    <span class="review-value" id="reviewPlayerBowling">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-label">Subscription:</span>
                                    <span class="review-value" id="reviewPlayerSubscription">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" id="sendPlayerEmail" name="sendEmail" checked>
                                <span>Send account credentials via email</span>
                            </label>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <p>The default password <code>player123456</code> will be set. The player should change this password upon first login. If "Send via email" is checked, the credentials will be emailed to the player.</p>
                        </div>
                    </div>

                    <div class="wizard-footer">
                        <button type="button" class="btn-secondary" id="playerWizardPrevBtn" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn-secondary" id="playerCancelBtn">Cancel</button>
                        <button type="button" class="btn-primary" id="playerWizardNextBtn">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-primary" id="playerWizardSubmitBtn" style="display: none;">
                            <i class="fas fa-save"></i> Add Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript -->
    <script>
        const URLROOT = '<?php echo URLROOT; ?>';
    </script>
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/players-management.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 Player Management - Initializing button handlers...');

            // Add Player Button Handler
            const addPlayerBtn = document.getElementById('addPlayerBtn');
            const addPlayerModal = document.getElementById('addPlayerModal');
            
            if (addPlayerBtn) {
                addPlayerBtn.addEventListener('click', function() {
                    console.log('🎯 Add Player button clicked!');
                    
                    if (addPlayerModal) {
                        addPlayerModal.classList.add('active');
                        addPlayerModal.style.display = 'flex';
                        
                        // Reset form
                        const form = document.getElementById('addPlayerForm');
                        if (form) form.reset();
                        
                        // Go to step 1
                        goToPlayerWizardStep(1);
                        
                        console.log('✅ Modal opened successfully!');
                    } else {
                        console.error('❌ Modal not found!');
                    }
                });
            }
            
            // Wizard navigation functions
            let currentPlayerStep = 1;
            
            function goToPlayerWizardStep(step) {
                currentPlayerStep = step;
                
                // Update step content visibility
                document.querySelectorAll('#addPlayerModal .wizard-step-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.querySelector(`#addPlayerModal .wizard-step-content[data-step="${step}"]`).classList.add('active');
                
                // Update progress circles
                document.querySelectorAll('#addPlayerModal .wizard-step').forEach(stepEl => {
                    const stepNum = parseInt(stepEl.dataset.step);
                    if (stepNum < step) {
                        stepEl.classList.add('completed');
                        stepEl.classList.remove('active');
                    } else if (stepNum === step) {
                        stepEl.classList.add('active');
                        stepEl.classList.remove('completed');
                    } else {
                        stepEl.classList.remove('active', 'completed');
                    }
                });
                
                // Update buttons
                const prevBtn = document.getElementById('playerWizardPrevBtn');
                const nextBtn = document.getElementById('playerWizardNextBtn');
                const submitBtn = document.getElementById('playerWizardSubmitBtn');
                
                if (step === 1) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'inline-flex';
                    submitBtn.style.display = 'none';
                } else if (step === 2) {
                    prevBtn.style.display = 'inline-flex';
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'inline-flex';
                    
                    // Update review section
                    updatePlayerReview();
                }
            }
            
            function updatePlayerReview() {
                // Personal Information
                const playerFullName = [
                    document.getElementById('playerFirstName').value,
                    document.getElementById('playerLastName').value
                ].filter(Boolean).join(' ');
                document.getElementById('reviewPlayerFullName').textContent = 
                    playerFullName || '-';
                document.getElementById('reviewPlayerDOB').textContent = 
                    document.getElementById('playerDOB').value || '-';
                document.getElementById('reviewPlayerJersey').textContent = 
                    document.getElementById('playerJersey').value || 'Not assigned';
                
                // Contact Information
                document.getElementById('reviewPlayerEmail').textContent = 
                    document.getElementById('playerEmail').value || '-';
                document.getElementById('reviewPlayerPhone').textContent = 
                    document.getElementById('playerPhone').value || '-';
                document.getElementById('reviewPlayerAddress').textContent = 
                    document.getElementById('playerAddress').value || '-';
                
                // Login Credentials
                document.getElementById('reviewPlayerUsername').textContent = 
                    document.getElementById('playerUsername').value || '-';
                
                // Cricket Details
                const battingSelect = document.getElementById('playerBatting');
                document.getElementById('reviewPlayerBatting').textContent = 
                    battingSelect.options[battingSelect.selectedIndex].text || 'Not set';
                
                const bowlingSelect = document.getElementById('playerBowling');
                document.getElementById('reviewPlayerBowling').textContent = 
                    bowlingSelect.options[bowlingSelect.selectedIndex].text || 'Not set';
                
                const subscriptionSelect = document.getElementById('playerSubscription');
                document.getElementById('reviewPlayerSubscription').textContent = 
                    subscriptionSelect.options[subscriptionSelect.selectedIndex].text || '-';
            }
            
            // Wizard navigation buttons
            document.getElementById('playerWizardNextBtn').addEventListener('click', function() {
                // Validate current step
                const form = document.getElementById('addPlayerForm');
                const currentStepContent = document.querySelector(`#addPlayerModal .wizard-step-content[data-step="${currentPlayerStep}"]`);
                const inputs = currentStepContent.querySelectorAll('input[required], select[required]');
                
                let isValid = true;
                inputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        input.classList.add('error');
                    } else {
                        input.classList.remove('error');
                    }
                });
                
                if (isValid) {
                    goToPlayerWizardStep(currentPlayerStep + 1);
                } else {
                    alert('Please fill in all required fields');
                }
            });
            
            document.getElementById('playerWizardPrevBtn').addEventListener('click', function() {
                goToPlayerWizardStep(currentPlayerStep - 1);
            });
            
            // Close Modal handlers
            const modalOverlay = document.getElementById('modalOverlay');
            const cancelBtn = document.getElementById('playerCancelBtn');
            
            if (modalOverlay) {
                modalOverlay.addEventListener('click', function() {
                    addPlayerModal.classList.remove('active');
                    addPlayerModal.style.display = 'none';
                });
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    addPlayerModal.classList.remove('active');
                    addPlayerModal.style.display = 'none';
                });
            }
            
            // Add Player Form Submission
            const addPlayerForm = document.getElementById('addPlayerForm');
            if (addPlayerForm) {
                addPlayerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData.entries());
                    
                    // Send to backend
                    fetch(`${URLROOT}/admin/add_player`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('✅ Player added successfully!\n\nDefault Password: player123456\n\nThe player should change this password upon first login.');
                            addPlayerModal.classList.remove('active');
                            addPlayerModal.style.display = 'none';
                            location.reload();
                        } else {
                            // Display detailed error message from server
                            const errorMsg = result.message || 'Failed to add player';
                            alert('❌ Failed to Add Player\n\nError Details:\n' + errorMsg + '\n\nPlease check the error and try again.');
                            console.error('Server Error:', result);
                        }
                    })
                    .catch(error => {
                        console.error('Network Error:', error);
                        alert('❌ Network Error\n\nFailed to connect to server.\nError: ' + error.message + '\n\nPlease check your connection and try again.');
                    });
                });
            }
            
            // View Player Modal Functions
            function openViewPlayerModal(data) {
                const modal = document.getElementById('viewPlayerModal');
                document.getElementById('viewPlayerName').textContent = data.name;
                document.getElementById('viewPlayerJerseyBadge').textContent = data.jersey ? `Jersey #${data.jersey}` : 'No Jersey';
                document.getElementById('viewPlayerStatusBadge').textContent = data.status.toUpperCase();
                document.getElementById('viewPlayerStatusBadge').className = 'staff-status ' + data.status.toLowerCase();
                
                document.getElementById('viewFullName').textContent = data.name;
                document.getElementById('viewEmail').textContent = data.email;
                document.getElementById('viewPhone').textContent = data.phone || 'N/A';
                document.getElementById('viewJoined').textContent = data.joined;
                document.getElementById('viewAddress').textContent = data.address || 'N/A';
                
                document.getElementById('viewJerseyNumber').textContent = data.jersey || 'Not Assigned';
                document.getElementById('viewBattingStyle').textContent = data.batting || 'Not Set';
                document.getElementById('viewBowlingStyle').textContent = data.bowling || 'Not Set';
                document.getElementById('viewSubscription').textContent = data.subscription.charAt(0).toUpperCase() + data.subscription.slice(1).replace('_', ' ');
                document.getElementById('viewStatus').textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('active'), 10);
                
                // Store player ID for edit button
                document.getElementById('editFromViewBtn').dataset.playerId = data.id;
                document.getElementById('editFromViewBtn').dataset.playerData = JSON.stringify(data);
            }

            function closeViewPlayerModal() {
                const modal = document.getElementById('viewPlayerModal');
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
            }

            // Edit Player Modal Functions
            function openEditPlayerModal(data) {
                const modal = document.getElementById('editPlayerModal');
                const nameParts = String(data.name || '').trim().split(/\s+/);
                const firstName = nameParts[0] || '';
                const lastName = nameParts.slice(1).join(' ');
                document.getElementById('editPlayerId').value = data.id;
                document.getElementById('editPlayerFirstName').value = firstName;
                document.getElementById('editPlayerLastName').value = lastName;
                document.getElementById('editPlayerEmail').value = data.email;
                document.getElementById('editPlayerPhone').value = data.phone || '';
                document.getElementById('editPlayerAddress').value = data.address || '';
                document.getElementById('editPlayerJersey').value = data.jersey || '';
                document.getElementById('editPlayerBatting').value = data.batting || '';
                document.getElementById('editPlayerBowling').value = data.bowling || '';
                document.getElementById('editPlayerSubscription').value = data.subscription;
                document.getElementById('editPlayerStatus').value = data.status;
                
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('active'), 10);
            }

            function closeEditPlayerModal() {
                const modal = document.getElementById('editPlayerModal');
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
            }

            // Modal Close Button Events
            document.getElementById('closeViewModal').addEventListener('click', closeViewPlayerModal);
            document.getElementById('closeEditModal').addEventListener('click', closeEditPlayerModal);
            document.getElementById('cancelEditBtn').addEventListener('click', closeEditPlayerModal);
            
            // Edit from view button
            document.getElementById('editFromViewBtn').addEventListener('click', function() {
                const data = JSON.parse(this.dataset.playerData);
                closeViewPlayerModal();
                setTimeout(() => openEditPlayerModal(data), 300);
            });

            // Close modals when clicking overlay
            document.getElementById('viewModalOverlay').addEventListener('click', closeViewPlayerModal);
            document.getElementById('editModalOverlay').addEventListener('click', closeEditPlayerModal);

            // Delete Player Modal Functions
            function openDeleteModal(playerId, playerName) {
                document.getElementById('deletePlayerId').value = playerId;
                document.getElementById('deletePlayerName').textContent = playerName;
                document.getElementById('deleteConfirmation').value = '';
                document.getElementById('confirmDeleteBtn').disabled = true;
                
                const modal = document.getElementById('deleteModal');
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('active'), 10);
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteModal');
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
            }

            // Enable delete button only when "DELETE" is typed
            document.getElementById('deleteConfirmation').addEventListener('input', function() {
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                confirmBtn.disabled = this.value.toUpperCase() !== 'DELETE';
            });

            // Delete Form Submission
            document.getElementById('deleteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const playerId = document.getElementById('deletePlayerId').value;
                const confirmation = document.getElementById('deleteConfirmation').value;
                
                if (confirmation.toUpperCase() !== 'DELETE') {
                    alert('Please type DELETE to confirm');
                    return;
                }
                
                console.log('Deleting player:', playerId);
                
                fetch(`${URLROOT}/admin/delete_player`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ playerId: playerId })
                })
                .then(response => {
                    console.log('Delete response status:', response.status);
                    return response.text().then(text => {
                        console.log('Raw delete response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Server returned invalid JSON: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(result => {
                    console.log('Delete result:', result);
                    if (result.success) {
                        alert('✅ Player deleted successfully!');
                        closeDeleteModal();
                        location.reload();
                    } else {
                        alert('❌ Failed to Delete Player\n\n' + (result.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Network Error\n\n' + error.message);
                });
            });

            // Edit Player Form Submission
            document.getElementById('editPlayerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                
                console.log('Sending update request:', data);
                
                fetch(`${URLROOT}/admin/update_player`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    
                    // Clone response to read it twice (once for text, once for json)
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Server returned invalid JSON: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(result => {
                    console.log('Parsed result:', result);
                    if (result.success) {
                        alert('✅ Player updated successfully!');
                        closeEditPlayerModal();
                        location.reload();
                    } else {
                        alert('❌ Failed to Update Player\n\n' + (result.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Network Error\n\n' + error.message);
                });
            });

            // Handle action buttons with event delegation
            document.addEventListener('click', function(e) {
                const target = e.target.closest('.action-btn');
                if (!target) return;
                
                const playerId = target.dataset.playerId;
                const playerName = target.dataset.playerName;
                const playerStatus = target.dataset.playerStatus;
                
                if (target.classList.contains('view')) {
                    // View player details
                    const playerData = {
                        id: playerId,
                        name: playerName,
                        email: target.dataset.playerEmail,
                        phone: target.dataset.playerPhone,
                        address: target.dataset.playerAddress,
                        jersey: target.dataset.playerJersey,
                        batting: target.dataset.playerBatting,
                        bowling: target.dataset.playerBowling,
                        subscription: target.dataset.playerSubscription,
                        status: target.dataset.playerStatus,
                        joined: target.dataset.playerJoined
                    };
                    openViewPlayerModal(playerData);
                    
                } else if (target.classList.contains('edit')) {
                    // Edit player
                    const playerData = {
                        id: playerId,
                        name: playerName,
                        email: target.dataset.playerEmail,
                        phone: target.dataset.playerPhone,
                        address: target.dataset.playerAddress,
                        jersey: target.dataset.playerJersey,
                        batting: target.dataset.playerBatting,
                        bowling: target.dataset.playerBowling,
                        subscription: target.dataset.playerSubscription,
                        status: target.dataset.playerStatus
                    };
                    openEditPlayerModal(playerData);
                    
                } else if (target.classList.contains('suspend')) {
                    // Suspend or unsuspend player
                    if (playerStatus.toLowerCase() === 'active') {
                        if (typeof openSuspendModal === 'function') {
                            openSuspendModal(playerId, playerName);
                        }
                    } else {
                        if (confirm(`Unsuspend ${playerName}?`)) {
                            if (typeof unsuspendPlayer === 'function') {
                                unsuspendPlayer(playerId, playerName);
                            }
                        }
                    }
                    
                } else if (target.classList.contains('delete')) {
                    // Delete player
                    openDeleteModal(playerId, playerName);
                }
            });
        });
    </script>
</body>
</html>
