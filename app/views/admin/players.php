<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/players-management.css">

    <!-- Admin Dashboard Layout -->
    <div class="admin-layout" data-urlroot="<?php echo URLROOT; ?>">
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
                            <span>Dashboard </span>
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
                    
                 <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link">
                    <i class="fas fa-trophy"></i><span>Tournaments</span></a></li>

                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>Slot Management</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Finances</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Admin Profile -->
            <div class="profile-section">
                <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                    <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                        <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                        <a href="<?php echo URLROOT; ?>/admin/profile" class="profile-avatar" aria-label="Open admin profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                            <i class="fas fa-user-circle"></i>
                        </a>
                        <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
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
                        <a href="<?php echo URLROOT; ?>/admin/feedback" class="btn-secondary">
                            <i class="fas fa-comments"></i> Feedbacks
                        </a>
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
                        <select id="ageGroupFilter" class="filter-select">
                            <option value="all">All Age Groups</option>
                            <option value="Under 11">Under 11</option>
                            <option value="Under 13">Under 13</option>
                            <option value="Under 15">Under 15</option>
                            <option value="Under 17">Under 17</option>
                            <option value="Under 19">Under 19</option>
                            <option value="Open">Open</option>
                        </select>
                        <select id="subscriptionFilter" class="filter-select">
                            <option value="all">All Subscriptions</option>
                            <option value="general">General</option>
                            <option value="private">Private</option>
                            <option value="pro">Pro</option>
                            <option value="facility-only">Facility Only</option>
                            <option value="premium">Premium</option>
                            <option value="basic">Basic</option>
                            <option value="private-only">Private Only</option>
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
                <div class="staff-table-section player-table-section">
                    <div class="table-topline">
                        <div>
                            <h2>Players Directory</h2>
                            <p>Showing a focused page of players at a time. Use search and filters to narrow the list.</p>
                        </div>
                        <button type="button" class="btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-rotate-left"></i> Reset
                        </button>
                    </div>
                    <table class="staff-table players-table" id="playersTable">
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
                            <?php
                                $subscriptionLabel = $player->SubscriptionPlanName ?? $player->SubscriptionType ?? 'Basic';
                                $subscriptionRaw = $player->SubscriptionType ?? $subscriptionLabel;
                                $normalizeSubscription = function ($value) {
                                    $key = strtolower(trim((string)$value));
                                    $key = str_replace('_', '-', $key);
                                    $key = preg_replace('/[^a-z0-9]+/', '-', $key);
                                    return trim($key, '-');
                                };
                                $subscriptionKeys = array_values(array_unique(array_filter([
                                    $normalizeSubscription($subscriptionLabel),
                                    $normalizeSubscription($subscriptionRaw),
                                ])));
                                $playerName = $player->Name ?? trim(($player->FirstName ?? '') . ' ' . ($player->LastName ?? ''));
                                $nameParts = preg_split('/\s+/', trim($playerName));
                                $initials = strtoupper(substr($nameParts[0] ?? 'P', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                                $statusKey = strtolower($player->Status ?? 'active');
                            ?>
                            <tr
                                data-player-age="<?php echo (int)($player->Age ?? 0); ?>"
                                data-player-subscription="<?php echo htmlspecialchars(implode(' ', $subscriptionKeys)); ?>"
                            >
                                <td><input type="checkbox" class="player-checkbox"></td>
                                <td>
                                    <div class="player-cell">
                                        <div class="player-mini-avatar"><?php echo htmlspecialchars($initials); ?></div>
                                        <div class="staff-info">
                                            <h4><?php echo htmlspecialchars($playerName); ?></h4>
                                            <p><?php echo $player->Age ?? 'N/A'; ?> years old</p>
                                        </div>
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
                                <td>
                                    <?php $subscriptionClass = strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string)$subscriptionLabel)); ?>
                                    <span class="role-badge <?php echo htmlspecialchars($subscriptionClass); ?>"><?php echo htmlspecialchars($subscriptionLabel); ?></span>
                                </td>
                                <td><span class="status-badge status-<?php echo htmlspecialchars($statusKey); ?>"><?php echo ucfirst($player->Status ?? 'active'); ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn view" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($playerName); ?>"
                                                data-player-initials="<?php echo htmlspecialchars($initials); ?>"
                                                data-player-email="<?php echo htmlspecialchars($player->Email); ?>"
                                                data-player-phone="<?php echo htmlspecialchars($player->PhoneNumber ?? 'N/A'); ?>"
                                                data-player-address="<?php echo htmlspecialchars($player->Address ?? 'N/A'); ?>"
                                                data-player-jersey="<?php echo $player->JerseyNumber ?? ''; ?>"
                                                data-player-batting="<?php echo htmlspecialchars($player->BattingStyle ?? 'Not set'); ?>"
                                                data-player-bowling="<?php echo htmlspecialchars($player->BowlingStyle ?? 'Not set'); ?>"
                                                data-player-subscription="<?php echo htmlspecialchars($subscriptionLabel); ?>"
                                                data-player-status="<?php echo htmlspecialchars($player->Status ?? 'active'); ?>"
                                                data-player-joined="<?php echo date('M d, Y', strtotime($player->DateJoined)); ?>"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn edit" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($playerName); ?>"
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
                                                data-player-name="<?php echo htmlspecialchars($playerName); ?>"
                                                data-player-status="<?php echo htmlspecialchars($player->Status ?? 'active'); ?>"
                                                title="<?php echo strtolower($player->Status ?? 'active') === 'active' ? 'Suspend Player' : 'Unsuspend Player'; ?>">
                                            <i class="fas fa-<?php echo strtolower($player->Status ?? 'active') === 'active' ? 'user-lock' : 'user-check'; ?>"></i>
                                        </button>
                                        <button class="action-btn delete" 
                                                data-player-id="<?php echo $player->UserID; ?>"
                                                data-player-name="<?php echo htmlspecialchars($playerName); ?>"
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
                        <div class="pagination-controls" id="playersPagination">
                            <button class="page-btn" id="prevPage">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="page-btn active">1</button>
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
        <div class="modal-content modal-large player-view-modal">
            <div class="modal-header player-view-header">
                <h2><i class="fas fa-user-circle"></i> Player Profile</h2>
                <button class="modal-close" id="closeViewModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="player-profile-view">
                    <div class="player-profile-hero">
                        <div class="player-profile-avatar" id="viewPlayerInitials">P</div>
                        <div class="player-profile-main">
                            <span class="profile-eyebrow">Elite Cricket Academy Player</span>
                            <h3 id="viewPlayerName">-</h3>
                            <div class="player-profile-badges">
                                <span class="profile-chip jersey" id="viewPlayerJerseyBadge">-</span>
                                <span class="profile-chip status" id="viewPlayerStatusBadge">-</span>
                                <span class="profile-chip subscription" id="viewSubscription">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="player-quick-grid">
                        <div class="quick-card">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                            <strong id="viewEmail">-</strong>
                        </div>
                        <div class="quick-card">
                            <i class="fas fa-phone"></i>
                            <span>Phone</span>
                            <strong id="viewPhone">-</strong>
                        </div>
                        <div class="quick-card">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Joined</span>
                            <strong id="viewJoined">-</strong>
                        </div>
                        <div class="quick-card">
                            <i class="fas fa-shirt"></i>
                            <span>Jersey</span>
                            <strong id="viewJerseyNumber">-</strong>
                        </div>
                    </div>

                    <div class="player-detail-panels">
                        <div class="player-detail-panel">
                            <h4><i class="fas fa-id-card"></i> Personal Information</h4>
                            <div class="player-detail-row">
                                <span class="detail-label">Full Name:</span>
                                <span class="detail-value" id="viewFullName">-</span>
                            </div>
                            <div class="player-detail-row">
                                <span class="detail-label">Address:</span>
                                <span class="detail-value" id="viewAddress">-</span>
                            </div>
                            <div class="player-detail-row">
                                <span class="detail-label">Account Status:</span>
                                <span class="detail-value" id="viewStatus">-</span>
                            </div>
                        </div>

                        <div class="player-detail-panel">
                            <h4><i class="fas fa-cricket"></i> Cricket Details</h4>
                            <div class="player-detail-row">
                                <span class="detail-label">Batting Style:</span>
                                <span class="detail-value" id="viewBattingStyle">-</span>
                            </div>
                            <div class="player-detail-row">
                                <span class="detail-label">Bowling Style:</span>
                                <span class="detail-value" id="viewBowlingStyle">-</span>
                            </div>
                            <div class="player-detail-row">
                                <span class="detail-label">Subscription Type:</span>
                                <span class="detail-value" id="viewSubscriptionDetail">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="closeViewFooterBtn">
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
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/players-management.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/players-page.js"></script>
</body>
</html>
