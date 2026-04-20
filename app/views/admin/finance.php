<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/finance.css">

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
                            <span>Dashboard </span>
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
                    
                 <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link">
                    <i class="fas fa-trophy"></i><span>Tournaments</span></a></li>

                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>Slot Management</span>
                        </a>
                    </li>

                    <li class="nav-item active">
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
            <!-- Finance Header -->
            <div class="finance-header finance-page-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-chart-line"></i> Finance Management</h1>
                        <p>Track revenue, monitor income streams, and manage financial transactions</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-secondary" id="exportDataBtn">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Financial Overview Cards -->
            <div class="finance-overview">
                <div class="overview-card total-revenue">
                    <div class="card-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-value">LKR <?php echo number_format($data['totalRevenue']); ?></div>
                        <div class="card-label">Total Revenue</div>
                        <div class="card-trend <?php echo $data['growthRate'] >= 0 ? 'positive' : 'negative'; ?>">
                            <i class="fas fa-arrow-<?php echo $data['growthRate'] >= 0 ? 'up' : 'down'; ?>"></i>
                            <?php echo abs($data['growthRate']); ?>% vs last month
                        </div>
                    </div>
                </div>

                <div class="overview-card monthly-revenue">
                    <div class="card-icon">
                        <i class="fas fa-calendar-month"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-value">LKR <?php echo number_format($data['monthlyRevenue']); ?></div>
                        <div class="card-label">This Month</div>
                        <div class="card-trend neutral">
                            <i class="fas fa-calendar-check"></i> Current month revenue
                        </div>
                    </div>
                </div>

                <div class="overview-card avg-transaction">
                    <div class="card-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-value">LKR <?php echo number_format($data['dailyAverage']); ?></div>
                        <div class="card-label">Daily Average</div>
                        <div class="card-trend <?php echo $data['growthRate'] >= 0 ? 'positive' : 'negative'; ?>">
                            <i class="fas fa-arrow-<?php echo $data['growthRate'] >= 0 ? 'up' : 'down'; ?>"></i> <?php echo abs($data['growthRate']); ?>% growth
                        </div>
                    </div>
                </div>

                <div class="overview-card pending-payments">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-value"><?php echo $data['pendingCount']; ?></div>
                        <div class="card-label">Pending Payments</div>
                        <div class="card-trend neutral">
                            <i class="fas fa-clock"></i> Requires attention
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown -->
            <div class="revenue-section">
                <div class="section-header">
                    <h2><i class="fas fa-chart-pie"></i> Revenue Breakdown by Category</h2>
                    <div class="section-controls">
                        <select class="period-selector" id="revenuePeriod">
                            <option value="current_month" <?php echo (($data['selectedPeriod'] ?? '') === 'current_month') ? 'selected' : ''; ?>>This Month</option>
                            <option value="last_month" <?php echo (($data['selectedPeriod'] ?? '') === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                            <option value="current_year" <?php echo (($data['selectedPeriod'] ?? 'current_year') === 'current_year') ? 'selected' : ''; ?>>This Year</option>
                            <option value="last_year" <?php echo (($data['selectedPeriod'] ?? '') === 'last_year') ? 'selected' : ''; ?>>Last Year</option>
                            <option value="all_time" <?php echo (($data['selectedPeriod'] ?? '') === 'all_time') ? 'selected' : ''; ?>>All Time</option>
                        </select>
                    </div>
                </div>

                <div class="revenue-grid">
                    <!-- Revenue Categories -->
                    <div class="revenue-categories">
                        <div class="category-card membership">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="category-info">
                                    <h3><?php echo htmlspecialchars($data['revenueCategories']['membership_fees']['label'] ?? 'Membership Fees'); ?></h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['membership_fees']['amount']); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['membership_fees']['percentage']; ?>%</div>
                            </div>
                        </div>

                        <div class="category-card shop-sales">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="category-info">
                                    <h3><?php echo htmlspecialchars($data['revenueCategories']['shop_sales']['label'] ?? 'Shop Sales'); ?></h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['shop_sales']['amount']); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['shop_sales']['percentage']; ?>%</div>
                            </div>
                        </div>

                        <div class="category-card facility-rental">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="category-info">
                                    <h3><?php echo htmlspecialchars($data['revenueCategories']['facility_bookings']['label'] ?? 'Facility & Session Bookings'); ?></h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['facility_bookings']['amount'] ?? 0); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['facility_bookings']['percentage'] ?? 0; ?>%</div>
                            </div>
                        </div>

                        <div class="category-card equipment-rental">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                                <div class="category-info">
                                    <h3><?php echo htmlspecialchars($data['revenueCategories']['equipment_rentals']['label'] ?? 'Equipment Rentals'); ?></h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['equipment_rentals']['amount'] ?? 0); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['equipment_rentals']['percentage'] ?? 0; ?>%</div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Payment Log Section -->
            <div class="payments-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Recent Payments & Transactions</h2>
                    <div class="section-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search transactions..." id="paymentSearch">
                        </div>
                        <select class="filter-select" id="paymentFilter">
                            <option value="all">All Types</option>
                            <option value="membership_fee">Membership Fees</option>
                            <option value="shop_sale">Shop Sales</option>
                            <option value="facility_booking">Facility / Session Bookings</option>
                            <option value="session_payment">Session Payments</option>
                            <option value="equipment_rental">Equipment Rental</option>
                            <option value="return_fee">Return Fees</option>
                        </select>
                        <select class="status-filter" id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="payments-table-container">
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Type</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTableBody">
                            <?php if(!empty($data['recentTransactions'])): ?>
                                <?php foreach ($data['recentTransactions'] as $payment): ?>
                                <tr class="payment-row"
                                    data-payment-id="<?php echo htmlspecialchars($payment['id']); ?>"
                                    data-status="<?php echo htmlspecialchars($payment['status']); ?>"
                                    data-type="<?php echo htmlspecialchars(strtolower(str_replace(' ', '_', $payment['type']))); ?>"
                                    data-amount="<?php echo htmlspecialchars((string)$payment['amount']); ?>"
                                    data-date="<?php echo htmlspecialchars($payment['date']); ?>"
                                    data-method="<?php echo htmlspecialchars($payment['method']); ?>">
                                    <td class="payment-id"><?php echo htmlspecialchars($payment['id']); ?></td>
                                    <td class="payment-type">
                                        <span class="type-badge <?php echo strtolower(str_replace(' ', '-', $payment['type'])); ?>">
                                            <?php echo htmlspecialchars($payment['type']); ?>
                                        </span>
                                    </td>
                                    <td class="customer-name"><?php echo htmlspecialchars($payment['customer']); ?></td>
                                    <td class="payment-amount">LKR <?php echo number_format($payment['amount'], 2); ?></td>
                                    <td class="payment-date"><?php echo date('M d, Y', strtotime($payment['date'])); ?></td>
                                    <td class="payment-method">
                                        <span class="method-badge <?php echo strtolower($payment['method']); ?>">
                                            <i class="fas fa-<?php echo strtolower($payment['method']) === 'card' ? 'credit-card' : (strtolower($payment['method']) === 'cash' ? 'money-bills' : (strtolower($payment['method']) === 'online' ? 'globe' : 'university')); ?>"></i>
                                            <?php echo $payment['method']; ?>
                                        </span>
                                    </td>
                                    <td class="payment-status">
                                        <span class="status-badge <?php echo $payment['status']; ?>">
                                            <i class="fas fa-<?php echo $payment['status'] === 'completed' ? 'check-circle' : ($payment['status'] === 'pending' ? 'clock' : 'times-circle'); ?>"></i>
                                            <?php echo ucfirst($payment['status']); ?>
                                        </span>
                                    </td>
                                    <td class="payment-actions">
                                        <button class="action-btn view" onclick="viewPayment('<?php echo htmlspecialchars($payment['id']); ?>')" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <p>No transactions found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    <div class="pagination-info">
                                    Showing 1-<?php echo count($data['recentTransactions']); ?> of <?php echo count($data['recentTransactions']); ?> transactions
                    </div>
                    <div class="pagination">
                        <button class="page-btn prev" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="page-number active">1</span>
                        <button class="page-btn next" disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Top Performing Items -->
            <div class="performance-section">
                <div class="section-header">
                    <h2><i class="fas fa-trophy"></i> Top Performing Revenue Sources</h2>
                </div>

                <div class="performance-grid">
                    <?php if(!empty($data['topSources'])): ?>
                        <?php foreach ($data['topSources'] as $index => $item): ?>
                        <div class="performance-card rank-<?php echo $index + 1; ?>">
                            <div class="item-info">
                                <div class="item-rank">#<?php echo $index + 1; ?></div>
                                <div class="item-name"><?php echo htmlspecialchars($item['item']); ?></div>
                                <div class="item-category"><?php echo ucfirst(str_replace('_', ' ', $item['category'])); ?></div>
                            </div>
                            <div class="item-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Revenue</span>
                                    <span class="stat-value">LKR <?php echo number_format($item['revenue']); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Quantity</span>
                                    <span class="stat-value"><?php echo $item['quantity']; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #999;">No revenue data available yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/finance.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', initializeFilters);
    </script>
</body>
</html>
