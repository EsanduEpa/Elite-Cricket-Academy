<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-reviews.css">

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
                <li class="nav-item">
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
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="nav-link">
                        <i class="fas fa-tools"></i>
                        <span>Equipment Rentals</span>
                    </a>
                </li>
                
                <li class="nav-item active">
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
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/counter" class="nav-link">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Counter Booking</span>
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
            <h1><i class="fas fa-star"></i> Reviews & Feedback Management</h1>
            <p>Monitor customer reviews, respond to feedback, and manage ratings</p>
        </div>

        <!-- Review Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FFD700, #FFA500);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="card-content">
                    <h3>Average Rating</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">4.6</span>
                            <span class="label">Out of 5.0</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="card-content">
                    <h3>Total Reviews</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">248</span>
                            <span class="label">All Time</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3>Pending Reviews</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent">5</span>
                            <span class="label">Need Response</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="card-content">
                    <h3>Positive Reviews</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">92%</span>
                            <span class="label">4-5 Stars</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div style="background: white; border-radius: 12px; padding: 24px; margin: 24px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-chart-bar" style="color: #4A90E2;"></i>
                Rating Distribution
            </h2>
            <div style="display: grid; gap: 12px;">
                <?php 
                $ratings = [
                    ['stars' => 5, 'count' => 156, 'percentage' => 63],
                    ['stars' => 4, 'count' => 72, 'percentage' => 29],
                    ['stars' => 3, 'count' => 12, 'percentage' => 5],
                    ['stars' => 2, 'count' => 5, 'percentage' => 2],
                    ['stars' => 1, 'count' => 3, 'percentage' => 1]
                ];
                foreach ($ratings as $rating): ?>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="min-width: 80px; display: flex; align-items: center; gap: 6px;">
                            <span style="font-weight: 600; color: #333;"><?php echo $rating['stars']; ?></span>
                            <i class="fas fa-star" style="color: #FFD700; font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; background: #e0e0e0; height: 24px; border-radius: 12px; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #FFD700, #FFA500); height: 100%; width: <?php echo $rating['percentage']; ?>%; transition: width 0.5s ease;"></div>
                        </div>
                        <div style="min-width: 80px; text-align: right;">
                            <span style="font-weight: 600; color: #666;"><?php echo $rating['count']; ?></span>
                            <span style="color: #999; font-size: 12px;"> (<?php echo $rating['percentage']; ?>%)</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Review Filters -->
        <div class="filter-section" style="margin: 2rem 0;">
            <div class="filter-tabs" style="display: flex; gap: 0.5rem; background: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 15px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
                <a href="#" class="filter-tab active" data-rating="all" style="flex: 1; padding: 12px 20px; text-decoration: none; color: white; background: #4A90E2; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-th"></i> All Reviews
                </a>
                <a href="#" class="filter-tab" data-rating="5" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-star"></i> 5 Stars
                </a>
                <a href="#" class="filter-tab" data-rating="4" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-star"></i> 4 Stars
                </a>
                <a href="#" class="filter-tab" data-rating="pending" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="#" class="filter-tab" data-rating="negative" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-exclamation-triangle"></i> Low Rated
                </a>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Customer Reviews</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search reviews..." id="reviewSearch">
                    <select class="filter-dropdown" id="productFilter">
                        <option value="all">All Products</option>
                        <option value="bats">Cricket Bats</option>
                        <option value="balls">Cricket Balls</option>
                        <option value="protective">Protective Gear</option>
                        <option value="clothing">Clothing</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportReviews()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-content" style="overflow-x: auto;">
                <table id="reviewsTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Review ID</th>
                            <th style="width: 150px;">Customer</th>
                            <th style="width: 200px;">Product</th>
                            <th style="width: 90px;">Rating</th>
                            <th style="width: 350px;">Review</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Review 1 -->
                        <tr>
                            <td><div class="table-cell-primary">#REV-248</div></td>
                            <td>
                                <div class="table-cell-title">Kasun Silva</div>
                                <div class="table-cell-details">kasun@email.com</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Professional Cricket Bat</div>
                                <div class="table-cell-details">English Willow</div>
                            </td>
                            <td>
                                <div style="color: #FFD700; font-size: 16px;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <div style="font-size: 12px; color: #666;">5.0</div>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                    "Excellent bat! The balance and pick-up are perfect. Great quality willow. Highly recommend for serious players."
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 20, 2025</div>
                                <div class="table-cell-secondary">2 days ago</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">Approved</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewReview(248)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="respondToReview(248)">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Review 2 -->
                        <tr>
                            <td><div class="table-cell-primary">#REV-247</div></td>
                            <td>
                                <div class="table-cell-title">Nimal Perera</div>
                                <div class="table-cell-details">nimal@email.com</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Cricket Helmet Elite</div>
                                <div class="table-cell-details">Safety Gear</div>
                            </td>
                            <td>
                                <div style="color: #FFD700; font-size: 16px;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <div style="font-size: 12px; color: #666;">4.0</div>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                    "Good quality helmet. Comfortable fit and provides excellent protection. Only downside is it's a bit heavy."
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 19, 2025</div>
                                <div class="table-cell-secondary">3 days ago</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">Pending</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewReview(247)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="approveReview(247)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="rejectReview(247)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Review 3 -->
                        <tr>
                            <td><div class="table-cell-primary">#REV-246</div></td>
                            <td>
                                <div class="table-cell-title">Amila Fernando</div>
                                <div class="table-cell-details">amila@email.com</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Batting Gloves Pro</div>
                                <div class="table-cell-details">Premium Quality</div>
                            </td>
                            <td>
                                <div style="color: #FFD700; font-size: 16px;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <div style="font-size: 12px; color: #666;">5.0</div>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                    "Best gloves I've ever used! Great grip, comfortable padding, and excellent durability. Worth every rupee!"
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 18, 2025</div>
                                <div class="table-cell-secondary">4 days ago</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">Approved</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewReview(246)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="respondToReview(246)">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Review 4 -->
                        <tr>
                            <td><div class="table-cell-primary">#REV-245</div></td>
                            <td>
                                <div class="table-cell-title">Ruwan Jayasinghe</div>
                                <div class="table-cell-details">ruwan@email.com</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Cricket Ball - Red Leather</div>
                                <div class="table-cell-details">Match Quality</div>
                            </td>
                            <td>
                                <div style="color: #FFD700; font-size: 16px;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <div style="font-size: 12px; color: #666;">3.0</div>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                    "Average quality. The ball loses shine quickly. Expected better quality for the price."
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 17, 2025</div>
                                <div class="table-cell-secondary">5 days ago</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">Pending</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewReview(245)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="approveReview(245)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="rejectReview(245)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Review 5 -->
                        <tr>
                            <td><div class="table-cell-primary">#REV-244</div></td>
                            <td>
                                <div class="table-cell-title">Sachini Wijesinghe</div>
                                <div class="table-cell-details">sachini@email.com</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Cricket Shoes Pro</div>
                                <div class="table-cell-details">All-Weather</div>
                            </td>
                            <td>
                                <div style="color: #FFD700; font-size: 16px;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <div style="font-size: 12px; color: #666;">4.5</div>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                    "Excellent shoes! Great grip on the field and very comfortable. Sizing is accurate. Fast delivery too!"
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 16, 2025</div>
                                <div class="table-cell-secondary">6 days ago</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">Approved</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewReview(244)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="respondToReview(244)">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Review 6 -->
                        <tr>
                            <td><div class="table-cell-primary">#REV-243</div></td>
                            <td>
                                <div class="table-cell-title">Dinesh Kumar</div>
                                <div class="table-cell-details">dinesh@email.com</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Leg Guards Premium</div>
                                <div class="table-cell-details">Professional Grade</div>
                            </td>
                            <td>
                                <div style="color: #FFD700; font-size: 16px;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <div style="font-size: 12px; color: #666;">2.0</div>
                            </td>
                            <td>
                                <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                    "Not satisfied. Straps broke after just 2 uses. Poor quality for the price. Would not recommend."
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 15, 2025</div>
                                <div class="table-cell-secondary">7 days ago</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">Flagged</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewReview(243)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="contactCustomer(243)">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="respondToReview(243)">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Review Details Modal -->
<div id="reviewModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeReviewModal()"></div>
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-star"></i> Review Details</h2>
            <button class="modal-close" onclick="closeReviewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="reviewDetails">
            <!-- Review details will be loaded here -->
        </div>
        
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeReviewModal()">Close</button>
            <button class="btn btn-primary" onclick="saveResponse()">Send Response</button>
        </div>
    </div>
</div>

<style>
/* Filter tabs styling */
.filter-tab:hover {
    background: rgba(74, 144, 226, 0.1) !important;
    color: #4A90E2 !important;
}

.filter-tab.active {
    background: #4A90E2 !important;
    color: white !important;
}

/* Reviews table action bar alignment and compact export button */
.data-table .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem 1rem;
    border-bottom: 1px solid rgba(74, 144, 226, 0.2);
}

.data-table .table-header h3 {
    margin: 0;
}

.data-table .table-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.data-table .table-actions .search-box,
.data-table .table-actions .filter-dropdown {
    height: 32px;
    margin: 0;
}

.data-table .table-actions .btn.btn-primary {
    height: 32px;
    padding: 0.4rem 0.85rem;
    font-size: 0.95rem;
    line-height: 1;
    border-radius: 8px;
    box-shadow: none;
    transition: none;
    transform: none;
    margin: 0;
}

.data-table .table-actions .btn.btn-primary:hover,
.data-table .table-actions .btn.btn-primary:focus,
.data-table .table-actions .btn.btn-primary:active {
    transform: none;
    box-shadow: none;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #4A90E2, #357ABD);
    color: white;
    border-radius: 15px 15px 0 0;
}

.modal-header h2 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .filter-tabs {
        flex-wrap: wrap;
    }

    .data-table .table-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .data-table .table-actions {
        width: 100%;
        flex-wrap: wrap;
    }

    .data-table .table-actions .btn.btn-primary {
        margin-left: 0;
    }
}
</style>

<script>
// Filter tabs functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterReviews(this.dataset.rating);
    });
});

// Search functionality
document.getElementById('reviewSearch').addEventListener('input', function() {
    searchReviews(this.value);
});

// Product filter
const productFilter = document.getElementById('productFilter');
if (productFilter) {
    productFilter.addEventListener('change', function() {
        const product = this.value.toLowerCase();
        const table = document.getElementById('reviewsTable');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (product === 'all' || !product) {
                row.style.display = '';
            } else {
                const productCell = row.querySelectorAll('td')[2];
                const productText = productCell ? productCell.textContent.trim().toLowerCase() : '';
                row.style.display = productText.includes(product) ? '' : 'none';
            }
        });
    });
}

function filterReviews(rating) {
    const table = document.getElementById('reviewsTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (rating === 'all') {
            row.style.display = '';
            return;
        }
        
        // Get the rating value from the row
        const ratingText = row.querySelectorAll('td')[3];
        if (!ratingText) { row.style.display = 'none'; return; }
        
        const ratingValue = ratingText.textContent.trim();
        const statusCell = row.querySelectorAll('td')[6];
        const statusText = statusCell ? statusCell.textContent.trim().toLowerCase() : '';
        
        if (rating === 'pending') {
            row.style.display = statusText.includes('pending') ? '' : 'none';
        } else if (rating === 'negative') {
            // Show ratings 1-2
            const numRating = parseFloat(ratingValue) || 0;
            row.style.display = numRating <= 2 ? '' : 'none';
        } else {
            // Numeric rating filter (4, 5)
            row.style.display = ratingValue.includes(rating + '.0') || ratingValue.trim() === rating ? '' : 'none';
        }
    });
}

function searchReviews(searchTerm) {
    const table = document.getElementById('reviewsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function viewReview(reviewId) {
    console.log('Viewing review:', reviewId);
    document.getElementById('reviewDetails').innerHTML = `
        <div style="padding: 20px;">
            <h3 style="color: #333; margin-bottom: 20px;">Review #REV-${reviewId}</h3>
            <div style="background: rgba(74, 144, 226, 0.05); padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h4 style="color: #4A90E2; margin-bottom: 10px;">Customer Information</h4>
                <p><strong>Name:</strong> Kasun Silva</p>
                <p><strong>Email:</strong> kasun@email.com</p>
                <p><strong>Product:</strong> Professional Cricket Bat</p>
            </div>
            <div style="background: rgba(74, 144, 226, 0.05); padding: 20px; border-radius: 10px;">
                <h4 style="color: #4A90E2; margin-bottom: 10px;">Review Content</h4>
                <div style="color: #FFD700; margin-bottom: 10px;">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <span style="color: #333; margin-left: 10px; font-weight: 600;">5.0</span>
                </div>
                <p style="line-height: 1.6; color: #666;">
                    "Excellent bat! The balance and pick-up are perfect. Great quality willow. Highly recommend for serious players."
                </p>
            </div>
            <div style="margin-top: 20px;">
                <label style="display: block; margin-bottom: 10px; color: #333; font-weight: 600;">Your Response:</label>
                <textarea style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; min-height: 100px; font-family: inherit;" placeholder="Type your response here..."></textarea>
            </div>
        </div>
    `;
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

function approveReview(reviewId) {
    console.log('Approving review:', reviewId);
    showNotification('Review approved successfully', 'success');
}

function rejectReview(reviewId) {
    if (confirm('Are you sure you want to reject this review?')) {
        console.log('Rejecting review:', reviewId);
        showNotification('Review rejected', 'info');
    }
}

function respondToReview(reviewId) {
    viewReview(reviewId);
}

function contactCustomer(reviewId) {
    console.log('Contacting customer for review:', reviewId);
    showNotification('Customer contact initiated', 'info');
}

function saveResponse() {
    console.log('Saving response');
    showNotification('Response sent successfully', 'success');
    closeReviewModal();
}

function exportReviews() {
    console.log('Exporting reviews');
    showNotification('Reviews exported successfully', 'success');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4ECDC4' : type === 'error' ? '#FF6B6B' : '#4A90E2'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<script src="<?php echo URLROOT; ?>/js/admin/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
