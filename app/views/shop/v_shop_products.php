<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-products.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="shop-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo URLROOT; ?>">
                        <i class="fas fa-cricket-bat"></i>
                        <span>Elite Cricket</span>
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo URLROOT; ?>">Home</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop" class="active">Shop</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop/categories">Categories</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop/deals">Deals</a></li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Search products..." id="searchInput">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    <button class="cart-btn" id="cartBtn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="shop-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Premium Cricket Equipment</h1>
                <p>Discover our complete range of professional cricket gear</p>
                <div class="breadcrumb">
                    <a href="<?php echo URLROOT; ?>">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Shop</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-header">
                <h2>Cricket Equipment</h2>
                <div class="view-toggle">
                    <button class="grid-view active" data-view="grid">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="list-view" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            
            <div class="filters-bar">
                <div class="filter-group">
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php foreach($data['categories'] as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="priceFilter">
                        <option value="">All Prices</option>
                        <option value="0-50">Under $50</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100-200">$100 - $200</option>
                        <option value="200-500">$200 - $500</option>
                        <option value="500+">$500+</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="sortFilter">
                        <option value="name">Sort by Name</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
                
                <button class="clear-filters" id="clearFilters">
                    <i class="fas fa-times"></i>
                    Clear Filters
                </button>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="products-section">
        <div class="container">
            <div class="products-grid" id="productsGrid">
                <?php foreach($data['all_products'] as $product): ?>
                    <div class="product-card" 
                         data-category="<?php echo $product['category_id']; ?>"
                         data-price="<?php echo $product['price']; ?>"
                         data-rating="<?php echo $product['rating']; ?>">
                        
                        <div class="product-image">
                            <img src="<?php echo URLROOT; ?>/img/products/<?php echo $product['image']; ?>" 
                                 alt="<?php echo $product['name']; ?>"
                                 onerror="this.src='<?php echo URLROOT; ?>/img/products/placeholder.jpg'">
                            
                            <?php if(isset($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <div class="sale-badge">
                                    <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-actions">
                                <button class="quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-category">
                                <?php echo $product['category']; ?>
                            </div>
                            
                            <h3 class="product-name">
                                <a href="<?php echo URLROOT; ?>/shop/product/<?php echo $product['id']; ?>">
                                    <?php echo $product['name']; ?>
                                </a>
                            </h3>
                            
                            <div class="product-rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $product['rating'] ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                                <span class="rating-count">(<?php echo $product['review_count'] ?? 0; ?>)</span>
                            </div>
                            
                            <div class="product-price">
                                <?php if(isset($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                    <span class="sale-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                    <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-features">
                                <?php if(isset($product['features']) && is_array($product['features'])): ?>
                                    <?php foreach(array_slice($product['features'], 0, 2) as $feature): ?>
                                        <span class="feature-tag"><?php echo $feature; ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <button class="add-to-cart-btn" 
                                    data-product-id="<?php echo $product['id']; ?>"
                                    data-product-name="<?php echo $product['name']; ?>"
                                    data-product-price="<?php echo $product['sale_price'] ?? $product['price']; ?>">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Load More Button -->
            <div class="load-more-section">
                <button class="load-more-btn" id="loadMoreBtn">
                    <i class="fas fa-plus"></i>
                    Load More Products
                </button>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="featured-categories">
        <div class="container">
            <h2>Shop by Category</h2>
            <div class="categories-grid">
                <?php foreach($data['categories'] as $category): ?>
                    <a href="<?php echo URLROOT; ?>/shop/category/<?php echo $category['id']; ?>" class="category-card">
                        <div class="category-icon">
                            <i class="<?php echo $category['icon']; ?>"></i>
                        </div>
                        <h3><?php echo $category['name']; ?></h3>
                        <p><?php echo $category['product_count'] ?? 0; ?> items</p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Get the latest deals and cricket gear updates delivered to your inbox</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="shop-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Elite Cricket</h3>
                    <p>Your trusted partner for premium cricket equipment and gear.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo URLROOT; ?>">Home</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">Shop</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop/categories">Categories</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop/deals">Deals</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Customer Service</h4>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Size Guide</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                        <p><i class="fas fa-envelope"></i> info@elitecricket.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> 123 Cricket Street, Sports City</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Elite Cricket. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="close-cart" id="closeCart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-content" id="cartContent">
            <!-- Cart items will be loaded here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                Total: $<span id="cartTotal">0.00</span>
            </div>
            <button class="checkout-btn">
                <i class="fas fa-credit-card"></i>
                Checkout
            </button>
        </div>
    </div>
    
    <div class="cart-overlay" id="cartOverlay"></div>

    <!-- JavaScript -->
    <script src="<?php echo URLROOT; ?>/js/shop/shop-products.js"></script>
</body>
</html>
</pre>