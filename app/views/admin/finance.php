<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/finance.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    
                    <li class="nav-item">
                        <a href="#player-management" class="nav-link">
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
                            <span class="badge">12</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
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
            <!-- Finance Header -->
            <div class="finance-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-chart-line"></i> Finance Management</h1>
                        <p>Track revenue, monitor income streams, and manage financial transactions</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="generateReportBtn">
                            <i class="fas fa-file-pdf"></i> Generate Report
                        </button>
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
                        <div class="card-trend positive">
                            <i class="fas fa-arrow-up"></i> 12.5% from last month
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
                        <div class="card-trend positive">
                            <i class="fas fa-arrow-up"></i> 8.3% growth
                        </div>
                    </div>
                </div>

                <div class="overview-card avg-transaction">
                    <div class="card-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-value">LKR <?php echo number_format($data['monthlyRevenue'] / 30); ?></div>
                        <div class="card-label">Daily Average</div>
                        <div class="card-trend positive">
                            <i class="fas fa-arrow-up"></i> 5.2% increase
                        </div>
                    </div>
                </div>

                <div class="overview-card pending-payments">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-value"><?php echo count(array_filter($data['recentPayments'], function($payment) { return $payment['status'] === 'pending'; })); ?></div>
                        <div class="card-label">Pending Payments</div>
                        <div class="card-trend neutral">
                            <i class="fas fa-minus"></i> No change
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
                            <option value="current_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="current_year" selected>This Year</option>
                            <option value="last_year">Last Year</option>
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
                                    <h3>Membership Fees</h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['membership_fees']['amount']); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['membership_fees']['percentage']; ?>%</div>
                            </div>
                            <div class="category-details">
                                <div class="detail-item">
                                    <span>Monthly Average</span>
                                    <span>LKR <?php echo number_format($data['revenueCategories']['membership_fees']['monthly']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span>Growth Rate</span>
                                    <span class="positive">+<?php echo $data['revenueCategories']['membership_fees']['growth']; ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div class="category-card shop-sales">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="category-info">
                                    <h3>Equipment Sales</h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['shop_sales']['amount']); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['shop_sales']['percentage']; ?>%</div>
                            </div>
                            <div class="category-details">
                                <div class="detail-item">
                                    <span>Monthly Average</span>
                                    <span>LKR <?php echo number_format($data['revenueCategories']['shop_sales']['monthly']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span>Growth Rate</span>
                                    <span class="positive">+<?php echo $data['revenueCategories']['shop_sales']['growth']; ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div class="category-card facility-rental">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="category-info">
                                    <h3>Facility Rental</h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['facility_rental']['amount']); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['facility_rental']['percentage']; ?>%</div>
                            </div>
                            <div class="category-details">
                                <div class="detail-item">
                                    <span>Monthly Average</span>
                                    <span>LKR <?php echo number_format($data['revenueCategories']['facility_rental']['monthly']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span>Growth Rate</span>
                                    <span class="positive">+<?php echo $data['revenueCategories']['facility_rental']['growth']; ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div class="category-card equipment-rental">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="category-info">
                                    <h3>Equipment Rental</h3>
                                    <div class="category-amount">LKR <?php echo number_format($data['revenueCategories']['equipment_rental']['amount']); ?></div>
                                </div>
                                <div class="category-percentage"><?php echo $data['revenueCategories']['equipment_rental']['percentage']; ?>%</div>
                            </div>
                            <div class="category-details">
                                <div class="detail-item">
                                    <span>Monthly Average</span>
                                    <span>LKR <?php echo number_format($data['revenueCategories']['equipment_rental']['monthly']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span>Growth Rate</span>
                                    <span class="positive">+<?php echo $data['revenueCategories']['equipment_rental']['growth']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="revenue-chart-container">
                        <div class="chart-header">
                            <h3>Revenue Trends</h3>
                            <div class="chart-controls">
                                <button class="chart-type-btn active" data-type="line">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                                <button class="chart-type-btn" data-type="bar">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                                <button class="chart-type-btn" data-type="pie">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="revenueChart"></canvas>
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
                            <option value="equipment_purchase">Equipment Purchase</option>
                            <option value="facility_rental">Facility Rental</option>
                            <option value="equipment_rental">Equipment Rental</option>
                        </select>
                        <select class="status-filter" id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
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
                            <?php foreach ($data['recentPayments'] as $payment): ?>
                            <tr class="payment-row" data-status="<?php echo $payment['status']; ?>" data-type="<?php echo strtolower(str_replace(' ', '_', $payment['type'])); ?>">
                                <td class="payment-id"><?php echo $payment['id']; ?></td>
                                <td class="payment-type">
                                    <span class="type-badge <?php echo strtolower(str_replace(' ', '-', $payment['type'])); ?>">
                                        <?php echo $payment['type']; ?>
                                    </span>
                                </td>
                                <td class="customer-name"><?php echo $payment['customer']; ?></td>
                                <td class="payment-amount">LKR <?php echo number_format($payment['amount']); ?></td>
                                <td class="payment-date"><?php echo date('M d, Y', strtotime($payment['date'])); ?></td>
                                <td class="payment-method">
                                    <span class="method-badge <?php echo strtolower($payment['method']); ?>">
                                        <i class="fas fa-<?php echo $payment['method'] === 'Card' ? 'credit-card' : ($payment['method'] === 'Cash' ? 'money-bills' : ($payment['method'] === 'Online' ? 'globe' : 'university')); ?>"></i>
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
                                    <button class="action-btn view" onclick="viewPayment('<?php echo $payment['id']; ?>')" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                    <button class="action-btn approve" onclick="approvePayment('<?php echo $payment['id']; ?>')" title="Approve Payment">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="action-btn download" onclick="downloadReceipt('<?php echo $payment['id']; ?>')" title="Download Receipt">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing 1-<?php echo count($data['recentPayments']); ?> of <?php echo count($data['recentPayments']); ?> transactions
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
                    <?php foreach ($data['topSellingItems'] as $index => $item): ?>
                    <div class="performance-card rank-<?php echo $index + 1; ?>">
                        <div class="rank-badge">#<?php echo $index + 1; ?></div>
                        <div class="item-info">
                            <div class="item-name"><?php echo $item['item']; ?></div>
                            <div class="item-category"><?php echo ucfirst(str_replace('_', ' ', $item['category'])); ?></div>
                        </div>
                        <div class="item-stats">
                            <div class="stat-item">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">LKR <?php echo number_format($item['revenue']); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Quantity/Sessions</span>
                                <span class="stat-value"><?php echo $item['quantity']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/admin/finance.js"></script>
    
    <script>
        // Initialize charts with data
        const monthlyData = <?php echo json_encode($data['monthlyData']); ?>;
        const revenueCategories = <?php echo json_encode($data['revenueCategories']); ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeFinanceCharts(monthlyData, revenueCategories);
            initializeFilters();
        });
    </script>
</body>
</html>
