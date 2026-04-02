<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">

<style>
.reports-page {
    padding: 20px;
    background: #f5f7fa;
    min-height: 100vh;
}

.reports-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.reports-header h1 {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 700;
}

.reports-header p {
    margin: 0;
    opacity: 0.9;
}

.filters-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filters-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-group label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
}

.filter-group select,
.filter-group input {
    padding: 10px 12px;
    border: 1.5px solid #e1e8ed;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    background: white;
}

.filter-group select:focus,
.filter-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #e9ecef;
    color: #495057;
}

.btn-secondary:hover {
    background: #dee2e6;
}

.players-table-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.table-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.player-count {
    background: #667eea;
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.players-table {
    width: 100%;
    border-collapse: collapse;
}

.players-table thead {
    background: #f8f9fa;
}

.players-table th {
    padding: 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.players-table td {
    padding: 15px;
    border-bottom: 1px solid #f1f3f5;
    color: #495057;
}

.players-table tbody tr:hover {
    background: #f8f9fa;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    color: #dee2e6;
    margin-bottom: 20px;
}

.empty-state p {
    font-size: 16px;
    margin: 10px 0 0 0;
}
</style>

<div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-user-shield"></i>
                <h3>Admin Dashboard</h3>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Players</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Feedback</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/session_slots" class="nav-link">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Session Slots</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Finance</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="admin-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <span class="admin-name"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <span class="admin-role">Administrator</span>
            </div>
            <div class="logout-btn">
                <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content reports-page">
        <!-- Header -->
        <div class="reports-header">
            <h1><i class="fas fa-file-alt"></i> Player Reports</h1>
            <p>Filter and view detailed player information</p>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-title">
                <i class="fas fa-filter"></i> Filter Options
            </div>
            
            <form method="GET" action="<?php echo URLROOT; ?>/admin/reports">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="all" <?php echo $data['filters']['status'] === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="active" <?php echo $data['filters']['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $data['filters']['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="subscription">Subscription</label>
                        <select name="subscription" id="subscription">
                            <option value="all" <?php echo $data['filters']['subscription'] === 'all' ? 'selected' : ''; ?>>All Types</option>
                            <option value="basic" <?php echo $data['filters']['subscription'] === 'basic' ? 'selected' : ''; ?>>Basic</option>
                            <option value="premium" <?php echo $data['filters']['subscription'] === 'premium' ? 'selected' : ''; ?>>Premium</option>
                            <option value="private_only" <?php echo $data['filters']['subscription'] === 'private_only' ? 'selected' : ''; ?>>Private Only</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="batting">Batting Style</label>
                        <select name="batting" id="batting">
                            <option value="all" <?php echo $data['filters']['batting'] === 'all' ? 'selected' : ''; ?>>All Styles</option>
                            <option value="Right-handed" <?php echo $data['filters']['batting'] === 'Right-handed' ? 'selected' : ''; ?>>Right-handed</option>
                            <option value="Left-handed" <?php echo $data['filters']['batting'] === 'Left-handed' ? 'selected' : ''; ?>>Left-handed</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="bowling">Bowling Style</label>
                        <select name="bowling" id="bowling">
                            <option value="all" <?php echo $data['filters']['bowling'] === 'all' ? 'selected' : ''; ?>>All Styles</option>
                            <option value="Fast" <?php echo $data['filters']['bowling'] === 'Fast' ? 'selected' : ''; ?>>Fast</option>
                            <option value="Medium" <?php echo $data['filters']['bowling'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="Spin" <?php echo $data['filters']['bowling'] === 'Spin' ? 'selected' : ''; ?>>Spin</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" placeholder="Name or email..." 
                               value="<?php echo htmlspecialchars($data['filters']['search']); ?>">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Players Table -->
        <div class="players-table-section">
            <div class="table-header">
                <h2>Player List</h2>
                <span class="player-count"><?php echo $data['totalPlayers']; ?> Players</span>
            </div>
            
            <?php if(!empty($data['players'])): ?>
            <table class="players-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>Batting</th>
                        <th>Bowling</th>
                        <th>Subscription</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['players'] as $player): ?>
                    <tr>
                        <td><?php echo $player->UserID; ?></td>
                        <td><strong><?php echo htmlspecialchars($player->Name); ?></strong></td>
                        <td><?php echo htmlspecialchars($player->Email); ?></td>
                        <td><?php echo htmlspecialchars($player->PhoneNumber ?? 'N/A'); ?></td>
                        <td><?php echo $player->Age ?? 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($player->BattingStyle ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($player->BowlingStyle ?? '—'); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $player->SubscriptionType ?? 'basic')); ?></td>
                        <td>
                            <span class="status-badge <?php echo $player->Status; ?>">
                                <?php echo ucfirst($player->Status); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>No players found matching your filters</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
