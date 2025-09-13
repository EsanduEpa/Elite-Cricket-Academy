<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping & Rental - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>
    
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
        <div class="player-sidebar" id="playerSidebar">
            <div class="sidebar-header">
                <div class="player-logo">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Player Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training" class="nav-link"><i class="fas fa-dumbbell"></i><span>Training Schedule</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link"><i class="fas fa-calendar-alt"></i><span>My Bookings</span><span class="badge">2</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/performance" class="nav-link"><i class="fas fa-chart-bar"></i><span>Performance</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span><span class="badge">2</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link active"><i class="fas fa-shopping-cart"></i><span>Shopping & Rental</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical Records</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link"><i class="fas fa-trophy"></i><span>Achievements</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="content-header">
                <div class="header-title">
                    <h1><i class="fas fa-shopping-cart"></i> Shopping & Rental</h1>
                    <p>Browse and purchase cricket equipment, gear, and rental items</p>
                </div>
                <div class="header-actions">
                    <div class="cart-summary">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">3</span>
                        <span class="cart-total">LKR 12,450</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon purchase">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">24</div>
                        <div class="stat-label">Items Purchased</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rental">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Active Rentals</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon savings">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">LKR 15,600</div>
                        <div class="stat-label">Total Savings</div>
                    </div>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="quick-links-section">
                <div class="section-header">
                    <h2><i class="fas fa-lightning-bolt"></i> Quick Actions</h2>
                    <p>Rapid access to popular shopping features</p>
                </div>
                
                <div class="quick-links-grid">
                    <div class="quick-link-card shop-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Browse Shop</h3>
                            <p>Explore our complete equipment catalog</p>
                            <span class="link-arrow"><i class="fas fa-arrow-right"></i></span>
                        </div>
                        <div class="quick-link-overlay"></div>
                    </div>

                    <div class="quick-link-card rental-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Rental Center</h3>
                            <p>Rent equipment for short-term use</p>
                            <span class="link-arrow"><i class="fas fa-arrow-right"></i></span>
                        </div>
                        <div class="quick-link-overlay"></div>
                    </div>

                    <div class="quick-link-card wishlist-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>My Wishlist</h3>
                            <p>Items you want to purchase later</p>
                            <span class="badge-count">5</span>
                            <span class="link-arrow"><i class="fas fa-arrow-right"></i></span>
                        </div>
                        <div class="quick-link-overlay"></div>
                    </div>

                    <div class="quick-link-card support-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="quick-link-content">
                            <h3>Shopping Support</h3>
                            <p>Get help with orders and returns</p>
                            <span class="link-arrow"><i class="fas fa-arrow-right"></i></span>
                        </div>
                        <div class="quick-link-overlay"></div>
                    </div>
                </div>
            </div>

            <!-- Purchase History Section -->
            <div class="purchase-history">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Purchase History</h2>
                    <p>Track your previous orders and purchases</p>
                    <div class="header-controls">
                        <select class="filter-select">
                            <option value="all">All Orders</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button class="btn btn-outline export-btn">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                
                <div class="purchase-timeline">
                    <div class="purchase-item">
                        <div class="purchase-date">
                            <div class="date-badge">
                                <span class="day">08</span>
                                <span class="month">Sep</span>
                            </div>
                        </div>
                        <div class="purchase-details">
                            <div class="purchase-card">
                                <div class="purchase-header">
                                    <div class="order-info">
                                        <h3>Order #ECA-2025-089</h3>
                                        <span class="order-status completed">Delivered</span>
                                    </div>
                                    <div class="order-total">LKR 8,750</div>
                                </div>
                                <div class="purchase-items">
                                    <div class="item-row">
                                        <img src="<?php echo URLROOT; ?>/img/bat.jpeg" alt="Cricket Bat">
                                        <div class="item-details">
                                            <h4>Professional Cricket Bat - Kashmir Willow</h4>
                                            <p>Size: Standard | Weight: 1.2kg</p>
                                        </div>
                                        <div class="item-price">LKR 6,500</div>
                                    </div>
                                    <div class="item-row">
                                        <img src="<?php echo URLROOT; ?>/img/prog2.jpg" alt="Cricket Gloves">
                                        <div class="item-details">
                                            <h4>Premium Batting Gloves</h4>
                                            <p>Size: Large | Material: Leather</p>
                                        </div>
                                        <div class="item-price">LKR 2,250</div>
                                    </div>
                                </div>
                                <div class="purchase-actions">
                                    <button class="btn btn-outline">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-outline">
                                        <i class="fas fa-download"></i> Invoice
                                    </button>
                                    <button class="btn btn-primary">
                                        <i class="fas fa-redo"></i> Reorder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="purchase-item">
                        <div class="purchase-date">
                            <div class="date-badge">
                                <span class="day">25</span>
                                <span class="month">Aug</span>
                            </div>
                        </div>
                        <div class="purchase-details">
                            <div class="purchase-card">
                                <div class="purchase-header">
                                    <div class="order-info">
                                        <h3>Order #ECA-2025-074</h3>
                                        <span class="order-status completed">Delivered</span>
                                    </div>
                                    <div class="order-total">LKR 15,200</div>
                                </div>
                                <div class="purchase-items">
                                    <div class="item-row">
                                        <img src="<?php echo URLROOT; ?>/img/prog1.jpeg" alt="Cricket Helmet">
                                        <div class="item-details">
                                            <h4>Professional Cricket Helmet</h4>
                                            <p>Size: Medium | Type: Full Face Protection</p>
                                        </div>
                                        <div class="item-price">LKR 8,900</div>
                                    </div>
                                    <div class="item-row">
                                        <img src="<?php echo URLROOT; ?>/img/prog3.webp" alt="Cricket Pads">
                                        <div class="item-details">
                                            <h4>Elite Batting Pads</h4>
                                            <p>Size: Adult | Material: Premium Cane</p>
                                        </div>
                                        <div class="item-price">LKR 6,300</div>
                                    </div>
                                </div>
                                <div class="purchase-actions">
                                    <button class="btn btn-outline">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-outline">
                                        <i class="fas fa-download"></i> Invoice
                                    </button>
                                    <button class="btn btn-primary">
                                        <i class="fas fa-redo"></i> Reorder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="purchase-item">
                        <div class="purchase-date">
                            <div class="date-badge">
                                <span class="day">12</span>
                                <span class="month">Aug</span>
                            </div>
                        </div>
                        <div class="purchase-details">
                            <div class="purchase-card">
                                <div class="purchase-header">
                                    <div class="order-info">
                                        <h3>Order #ECA-2025-063</h3>
                                        <span class="order-status pending">Processing</span>
                                    </div>
                                    <div class="order-total">LKR 4,850</div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Rentals Section -->
            <div class="current-rentals">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-check"></i> Current Rentals</h2>
                    <p>Manage your active equipment rentals</p>
                </div>
                
                <div class="rentals-grid">
                    <div class="rental-card">
                        <div class="rental-item">
                            <img src="<?php echo URLROOT; ?>/img/bat.jpeg" alt="Rented Bat">
                            <div class="rental-info">
                                <h4>Professional Cricket Bat</h4>
                                <p class="rental-period">Rented: Sep 1 - Sep 15, 2025</p>
                            </div>
                        </div>
                        <div class="rental-status">
                            <span class="status active">Active</span>
                            <div class="days-left">5 days left</div>
                        </div>
                        <div class="rental-actions">
                            <button class="btn btn-outline extend-rental">
                                <i class="fas fa-plus"></i>
                                Extend
                            </button>
                            <button class="btn btn-primary return-item">
                                <i class="fas fa-undo"></i>
                                Return
                            </button>
                        </div>
                    </div>

                    <div class="rental-card">
                        <div class="rental-item">
                            <img src="<?php echo URLROOT; ?>/img/prog1.jpeg" alt="Rented Helmet">
                            <div class="rental-info">
                                <h4>Premium Cricket Helmet</h4>
                                <p class="rental-period">Rented: Aug 28 - Sep 18, 2025</p>
                            </div>
                        </div>
                        <div class="rental-status">
                            <span class="status active">Active</span>
                            <div class="days-left">9 days left</div>
                        </div>
                        <div class="rental-actions">
                            <button class="btn btn-outline extend-rental">
                                <i class="fas fa-plus"></i>
                                Extend
                            </button>
                            <button class="btn btn-primary return-item">
                                <i class="fas fa-undo"></i>
                                Return
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Player Layout -->

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>
</body>
</html>