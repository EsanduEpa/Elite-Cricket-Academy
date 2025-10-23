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
                    <a href="<?php echo URLROOT; ?>/shop/products" class="action-btn">Add Product</a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Add Facility</h3>
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="action-btn">Add Facility</a>
                </div>
            </div>
            
            <div style="flex: 1;"></div>
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
        
        <!-- Promotions Section -->
        <div class="promotions-section" style="background: white; border-radius: 12px; padding: 24px; margin: 24px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #333; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-tags" style="color: #4A90E2;"></i>
                    Shop Events & Promotions
                </h2>
                <button class="action-btn" style="padding: 8px 16px;">
                    <i class="fas fa-plus"></i> Add Promotion
                </button>
            </div>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: rgba(74, 144, 226, 0.1); border-bottom: 2px solid #4A90E2;">
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4A90E2;">Event Name</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4A90E2;">Type</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4A90E2;">Discount</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4A90E2;">Start Date</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4A90E2;">End Date</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600; color: #4A90E2;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 14px;">
                            <div style="font-weight: 600; color: #333;">Cricket Season Sale</div>
                            <div style="font-size: 12px; color: #999;">All cricket equipment</div>
                        </td>
                        <td style="padding: 14px;">
                            <span style="background: rgba(74, 144, 226, 0.15); color: #4A90E2; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Sale</span>
                        </td>
                        <td style="padding: 14px; color: #333; font-weight: 600;">25% OFF</td>
                        <td style="padding: 14px; color: #666;">Dec 1, 2025</td>
                        <td style="padding: 14px; color: #666;">Dec 31, 2025</td>
                        <td style="padding: 14px; text-align: center;">
                            <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Active</span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 14px;">
                            <div style="font-weight: 600; color: #333;">New Year Mega Sale</div>
                            <div style="font-size: 12px; color: #999;">All categories</div>
                        </td>
                        <td style="padding: 14px;">
                            <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Event</span>
                        </td>
                        <td style="padding: 14px; color: #333; font-weight: 600;">40% OFF</td>
                        <td style="padding: 14px; color: #666;">Jan 1, 2026</td>
                        <td style="padding: 14px; color: #666;">Jan 3, 2026</td>
                        <td style="padding: 14px; text-align: center;">
                            <span style="background: rgba(156, 163, 175, 0.15); color: #6b7280; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Upcoming</span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 14px;">
                            <div style="font-weight: 600; color: #333;">Buy 2 Get 1 Free</div>
                            <div style="font-size: 12px; color: #999;">Cricket balls only</div>
                        </td>
                        <td style="padding: 14px;">
                            <span style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Offer</span>
                        </td>
                        <td style="padding: 14px; color: #333; font-weight: 600;">BOGO</td>
                        <td style="padding: 14px; color: #666;">Nov 15, 2025</td>
                        <td style="padding: 14px; color: #666;">Dec 15, 2025</td>
                        <td style="padding: 14px; text-align: center;">
                            <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Active</span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 14px;">
                            <div style="font-weight: 600; color: #333;">Weekend Special</div>
                            <div style="font-size: 12px; color: #999;">Batting gloves & pads</div>
                        </td>
                        <td style="padding: 14px;">
                            <span style="background: rgba(74, 144, 226, 0.15); color: #4A90E2; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Sale</span>
                        </td>
                        <td style="padding: 14px; color: #333; font-weight: 600;">30% OFF</td>
                        <td style="padding: 14px; color: #666;">Every Sat-Sun</td>
                        <td style="padding: 14px; color: #666;">Ongoing</td>
                        <td style="padding: 14px; text-align: center;">
                            <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Active</span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 14px;">
                            <div style="font-weight: 600; color: #333;">Black Friday Deal</div>
                            <div style="font-size: 12px; color: #999;">Store-wide discount</div>
                        </td>
                        <td style="padding: 14px;">
                            <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Flash Sale</span>
                        </td>
                        <td style="padding: 14px; color: #333; font-weight: 600;">50% OFF</td>
                        <td style="padding: 14px; color: #666;">Nov 29, 2025</td>
                        <td style="padding: 14px; color: #666;">Nov 29, 2025</td>
                        <td style="padding: 14px; text-align: center;">
                            <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Expired</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
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