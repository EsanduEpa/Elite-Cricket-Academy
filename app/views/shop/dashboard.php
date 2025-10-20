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
                    <a href="<?php echo URLROOT; ?>/shop/prescriptions" class="nav-link">
                        <i class="fas fa-prescription-bottle"></i>
                        <span>Prescriptions</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/analytics" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Sales Analytics</span>
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


<!-- Simple Profile Section -->
        <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
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

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <h1><i class="fas fa-store"></i> Welcome back, <?php echo isset($data['user_name']) ? $data['user_name'] : 'Shop Manager'; ?>!</h1>
            <p>Shop Management Dashboard - Manage your shop operations efficiently.</p>
            <div class="current-time">
                <i class="fas fa-clock"></i>
                <span id="currentDateTime"></span>
            </div>
        </div>
        
        <!-- Key Performance Indicators -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-content">
                    <h3>Total Orders</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo $data['stats']['total_orders']; ?></span>
                            <span class="label">All Time</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo URLROOT; ?>/shop/orders" class="view-all-btn">View All Orders</a>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3>Pending Orders</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent"><?php echo $data['stats']['pending_orders']; ?></span>
                            <span class="label">Need Action</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo URLROOT; ?>/shop/orders/pending" class="view-all-btn">Process Orders</a>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="card-content">
                    <h3>Monthly Revenue</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">₨ <?php echo number_format($data['stats']['monthly_revenue']); ?></span>
                            <span class="label">This Month</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo URLROOT; ?>/shop/analytics" class="view-all-btn">View Analytics</a>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #6B73FF, #8B83FF);">
                    <i class="fas fa-box"></i>
                </div>
                <div class="card-content">
                    <h3>Products</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo $data['stats']['total_products']; ?></span>
                            <span class="label">Total Items</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo URLROOT; ?>/shop/products" class="view-all-btn">Manage Products</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions-section">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="action-cards">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3>Add New Product</h3>
                    <p>Add cricket equipment, accessories, or merchandise to your inventory</p>
                    <a href="<?php echo URLROOT; ?>/shop/add_product" class="action-btn">Add Product</a>
                </div>
                
               
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-prescription-bottle"></i>
                    </div>
                    <h3>Request Prescription</h3>
                    <p>Request professional recommendations for supplements and nutrition</p>
                    <a href="<?php echo URLROOT; ?>/shop/request_prescription" class="action-btn">Request Now</a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Add Facility</h3>
                    <p>Add new training facilities or update existing facility information</p>
                    <a href="<?php echo URLROOT; ?>/shop/add_facility" class="action-btn">Add Facility</a>
                </div>
            </div>
        </div>
        
        <!-- Status Overview -->
        <div class="status-grid">
            <div class="status-card">
                <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h3>
                <div class="status-content">
                    <div class="status-number"><?php echo $data['stats']['low_stock_products']; ?></div>
                    <div class="status-label">Items Running Low</div>
                    <div class="status-items">
                        <div class="status-item">Cricket Helmets (3 left)</div>
                        <div class="status-item">Batting Gloves (2 left)</div>
                        <div class="status-item">Leg Guards (5 left)</div>
                    </div>
                    <a href="<?php echo URLROOT; ?>/shop/inventory" class="status-action">Restock Items</a>
                </div>
            </div>
            
            <div class="status-card">
                <h3><i class="fas fa-star"></i> Review Management</h3>
                <div class="status-content">
                    <div class="status-number"><?php echo $data['stats']['pending_reviews']; ?></div>
                    <div class="status-label">Pending Reviews</div>
                    <div class="status-items">
                        <div class="status-item">Cricket Bat Pro - 5 stars</div>
                        <div class="status-item">Helmet Elite - 4 stars</div>
                    </div>
                    <a href="<?php echo URLROOT; ?>/shop/reviews" class="status-action">Moderate Reviews</a>
                </div>
            </div>
            
            <div class="status-card">
                <h3><i class="fas fa-tools"></i> Active Rentals</h3>
                <div class="status-content">
                    <div class="status-number"><?php echo $data['stats']['active_rentals']; ?></div>
                    <div class="status-label">Equipment Rented</div>
                    <div class="status-items">
                        <div class="status-item">Bowling Machine A (until 6 PM)</div>
                        <div class="status-item">Practice Nets (until 8 PM)</div>
                    </div>
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="status-action">Manage Rentals</a>
                </div>
            </div>
        </div>
        
        <!-- Calendar Section -->
        <?php 
        $calendarTitle = 'Shop Events & Promotions';
        $calendarIcon = 'fas fa-calendar-alt';
        $calendarId = 'shopCalendar';
        include APPROOT . '/views/inc/components/calendar.php'; 
        ?>
        
        <div class="activity-section">
            <div class="activity-card">
                <div class="activity-header">
                    <h3><i class="fas fa-chart-line"></i> Top Products</h3>
                </div>
                <div class="activity-list">
                    <?php if(isset($data['top_products']) && !empty($data['top_products'])): ?>
                        <?php foreach($data['top_products'] as $product): ?>
                            <div class="activity-item">
                                <div class="activity-content">
                                    <h4><?php echo $product['name']; ?></h4>
                                    <p><?php echo $product['sales']; ?> sales</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <h4>No products available</h4>
                                <p>Start adding products to see analytics</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="quick-actions">
            <div class="actions-card">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript for Dashboard -->
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>