<?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop.css">

<div class="admin-layout">
    <!-- Shop Sidebar -->
    <div class="admin-sidebar" id="shopSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-store"></i>
                <h3>Shop Manager</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/shop/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/orders" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Order Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/products" class="nav-link">
                        <i class="fas fa-box"></i>
                        <span>Product Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/inventory" class="nav-link">
                        <i class="fas fa-warehouse"></i>
                        <span>Inventory</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="nav-link">
                        <i class="fas fa-tools"></i>
                        <span>Equipment Rentals</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/reviews" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span>Reviews & Feedback</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Facility Management</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Shop Profile -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['user_name']) ? $data['user_name'] : 'Shop Manager'; ?></div>
            <div class="profile-role">Shop Employee</div>
            <a href="<?php echo URLROOT; ?>/shop/profile" class="action-btn" style="margin-top: 10px;">
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
                <h1><i class="fas fa-store"></i> Shop Management Dashboard</h1>
                <p>Manage products, orders, rentals, and facility bookings efficiently</p>
            </div>
            <div class="header-actions">
                <button class="refresh-btn" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <div class="current-time">
                    <i class="fas fa-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
        
        <!-- Key Performance Indicators -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-info">
                        <span class="number"><?php echo $data['stats']['total_orders']; ?></span>
                        <span class="label">Total Orders</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo URLROOT; ?>/shop/orders" class="view-all-link">
                        <i class="fas fa-arrow-right"></i> View All Orders
                    </a>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-info">
                        <span class="number" style="color: #f5576c;"><?php echo $data['stats']['pending_orders']; ?></span>
                        <span class="label">Pending Orders</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo URLROOT; ?>/shop/orders" class="view-all-link">
                        <i class="fas fa-arrow-right"></i> Process Orders
                    </a>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="card-info">
                        <span class="number">₨<?php echo number_format($data['stats']['monthly_revenue']); ?></span>
                        <span class="label">Monthly Revenue</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo URLROOT; ?>/shop/analytics" class="view-all-link">
                        <i class="fas fa-arrow-right"></i> View Analytics
                    </a>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="card-info">
                        <span class="number"><?php echo $data['stats']['total_products']; ?></span>
                        <span class="label">Total Products</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo URLROOT; ?>/shop/products" class="view-all-link">
                        <i class="fas fa-arrow-right"></i> Manage Products
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            </div>
            <div class="quick-actions-grid">
                <a href="<?php echo URLROOT; ?>/shop/products" class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3>Add New Product</h3>
                    <p>Add cricket gear to inventory</p>
                </a>
                
                <a href="<?php echo URLROOT; ?>/shop/facilities" class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Add Facility</h3>
                    <p>Register new facility</p>
                </a>
                
                <a href="<?php echo URLROOT; ?>/shop/orders" class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Process Orders</h3>
                    <p>Handle pending orders</p>
                </a>
                
                <a href="<?php echo URLROOT; ?>/shop/rentals" class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Manage Rentals</h3>
                    <p>Track equipment rentals</p>
                </a>
            </div>
        </div>
        
        <!-- Status Overview Grid -->
        <div class="content-grid">
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h3>
                </div>
                <div class="card-body">
                    <div class="alert-number"><?php echo $data['stats']['low_stock_products']; ?></div>
                    <p class="alert-label">Items Running Low</p>
                    <div class="info-list">
                        <?php if(isset($data['stats']['low_stock_items']) && !empty($data['stats']['low_stock_items'])): ?>
                            <?php foreach($data['stats']['low_stock_items'] as $item): ?>
                                <div class="info-item">
                                    <i class="fas fa-dot-circle"></i>
                                    <span><?php echo $item->Name; ?> (<?php echo $item->StockQuantity; ?> left)</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="info-item">
                                <i class="fas fa-check-circle"></i>
                                <span>All items well stocked</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo URLROOT; ?>/shop/inventory" class="card-action-btn">Manage Inventory</a>
                </div>
            </div>
            
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> Review Management</h3>
                </div>
                <div class="card-body">
                    <div class="alert-number"><?php echo $data['stats']['pending_reviews']; ?></div>
                    <p class="alert-label">Pending Reviews</p>
                    <div class="info-list">
                        <?php if(isset($data['stats']['pending_review_items']) && !empty($data['stats']['pending_review_items'])): ?>
                            <?php foreach($data['stats']['pending_review_items'] as $review): ?>
                                <div class="info-item">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo $review->product_name; ?> - <?php echo $review->Rating; ?> stars</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="info-item">
                                <i class="fas fa-check-circle"></i>
                                <span>No pending reviews</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo URLROOT; ?>/shop/reviews" class="card-action-btn">Moderate Reviews</a>
                </div>
            </div>
            
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-tools"></i> Active Rentals</h3>
                </div>
                <div class="card-body">
                    <div class="alert-number"><?php echo $data['stats']['active_rentals']; ?></div>
                    <p class="alert-label">Equipment Rented</p>
                    <div class="info-list">
                        <?php if(isset($data['stats']['active_rental_items']) && !empty($data['stats']['active_rental_items'])): ?>
                            <?php foreach($data['stats']['active_rental_items'] as $rental): ?>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo $rental->equipment_name; ?> (until <?php echo date('g:i A', strtotime($rental->EndTime)); ?>)</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="info-item">
                                <i class="fas fa-check-circle"></i>
                                <span>No active rentals</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="card-action-btn">Manage Rentals</a>
                </div>
            </div>
        </div>
        
        <!-- Promotions Section - Remove dummy promotions table -->
        
        <!-- Recent Activity -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i> Top Selling Products</h2>
            </div>
            <div class="activity-list">
                <?php if(isset($data['stats']['top_products']) && !empty($data['stats']['top_products'])): ?>
                    <?php foreach($data['stats']['top_products'] as $product): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="activity-details">
                                <h4><?php echo $product->Name; ?></h4>
                                <p><?php echo $product->total_sales; ?> sales | ₨<?php echo number_format($product->total_revenue); ?> revenue</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Sales Data Available</h3>
                        <p>Start processing orders to see analytics</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Update current date/time
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
}

updateDateTime();
setInterval(updateDateTime, 60000);
</script>
<script src="<?php echo URLROOT; ?>/js/admin/admin-dashboard.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>