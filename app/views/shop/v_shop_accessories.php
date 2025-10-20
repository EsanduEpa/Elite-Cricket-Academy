<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?> - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-accessories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo URLROOT; ?>">
                        <img src="<?php echo URLROOT; ?>/img/logo.png" alt="Elite Cricket Academy" class="logo-img">
                        <span class="logo-text">Elite Cricket Academy</span>
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul class="nav-links">
                        <li><a href="<?php echo URLROOT; ?>/shop">Cricket Gear</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">New Arrivals</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">Bats</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">Balls</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">Gloves</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop/accessories" class="active">Accessories</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">Sale</a></li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <div class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </div>
                    <div class="user-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="<?php echo URLROOT; ?>">Home</a>
                <span class="breadcrumb-separator">/</span>
                <a href="<?php echo URLROOT; ?>/shop">Shop</a>
                <span class="breadcrumb-separator">/</span>
                <span class="current">Accessories</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Accessories</h1>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="filter-left">
                    <div class="filter-group">
                        <label for="sort-select">Sort by Price:</label>
                        <select id="sort-select" class="filter-select">
                            <option value="">Select Option</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Highest Rated</option>
                            <option value="popularity">Most Popular</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="popularity-select">Sort by Popularity:</label>
                        <select id="popularity-select" class="filter-select">
                            <option value="">Select Option</option>
                            <option value="most-popular">Most Popular</option>
                            <option value="trending">Trending</option>
                            <option value="new-arrivals">New Arrivals</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="customer-select">Sort by Customer Ratings:</label>
                        <select id="customer-select" class="filter-select">
                            <option value="">Select Option</option>
                            <option value="5-star">5 Stars</option>
                            <option value="4-star">4+ Stars</option>
                            <option value="3-star">3+ Stars</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-right">
                    <button class="filter-btn">
                        <i class="fas fa-filter"></i>
                        Filters
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-section">
                <div class="products-grid" id="products-grid">
                    <?php foreach($data['accessories'] as $product): ?>
                        <div class="product-card" data-category="<?php echo strtolower($product['category']); ?>" data-price="<?php echo $product['price']; ?>" data-rating="<?php echo $product['rating']; ?>">
                            <div class="product-image">
                                <img src="<?php echo URLROOT; ?>/img/products/<?php echo $product['image']; ?>" 
                                     alt="<?php echo $product['name']; ?>" 
                                     onerror="this.src='<?php echo URLROOT; ?>/img/placeholder-product.jpg'">
                                <div class="product-overlay">
                                    <button class="quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                                <?php if(isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <div class="sale-badge">
                                        Sale
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-brand"><?php echo $product['brand']; ?></div>
                                <h3 class="product-name"><?php echo $product['name']; ?></h3>
                                
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $product['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-count">(<?php echo $product['reviews']; ?>)</span>
                                </div>
                                
                                <div class="product-price">
                                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if(isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                        <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-features">
                                    <?php foreach($product['features'] as $feature): ?>
                                        <span class="feature-tag"><?php echo $feature; ?></span>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="product-stock">
                                    <span class="stock-indicator <?php echo $product['stock'] > 10 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock'); ?>">
                                        <?php 
                                            if($product['stock'] > 10) echo 'In Stock';
                                            elseif($product['stock'] > 0) echo 'Only ' . $product['stock'] . ' left';
                                            else echo 'Out of Stock';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="pagination-btn prev" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="pagination-numbers">
                    <button class="pagination-number active">1</button>
                    <button class="pagination-number">2</button>
                    <button class="pagination-number">3</button>
                    <span class="pagination-dots">...</span>
                    <button class="pagination-number">6</button>
                </div>
                <button class="pagination-btn next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </main>

    <!-- Shopping Cart Sidebar -->
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="cart-close" id="cart-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="cart-content">
            <div class="cart-items" id="cart-items">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                </div>
            </div>
            
            <div class="cart-footer">
                <div class="cart-total">
                    <span class="total-label">Total: </span>
                    <span class="total-amount" id="cart-total">$0.00</span>
                </div>
                <button class="checkout-btn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <!-- Cart Overlay -->
    <div class="cart-overlay" id="cart-overlay"></div>

    <script src="<?php echo URLROOT; ?>/js/shop/shop-accessories.js"></script>
</body>
</html>