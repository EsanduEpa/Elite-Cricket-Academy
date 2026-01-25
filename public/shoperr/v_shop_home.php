<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header / Navigation Bar -->
    <header class="header" id="header">
        <nav class="nav-container">
            <div class="logo">Elite Cricket Accessories</div>
            <ul class="nav-menu">
                <li><a href="<?php echo URLROOT; ?>">Home</a></li>
                <li><a href="<?php echo URLROOT; ?>/shop">Shop</a></li>
                <li><a href="<?php echo URLROOT; ?>/#programs">Programs</a></li>
                <li><a href="<?php echo URLROOT; ?>/#coaches">Coaches</a></li>
                <li><a href="<?php echo URLROOT; ?>/#facilities">Facilities</a></li>
                <li><a href="<?php echo URLROOT; ?>/#testimonials">Testimonials</a></li>
                <li><a href="<?php echo URLROOT; ?>/#contact">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="#" class="enroll-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                <a href="<?php echo URLROOT; ?>/login" class="enroll-btn">Login</a>
            </div>
        </nav>
    </header>

    <!-- Left Menu Dropdown -->
    <div class="left-menu-container">
        <div class="nav-dropdown">
            <button class="dropdown-toggle" id="navDropdown" onclick="testMenuClick()">
                <i class="fas fa-bars"></i>
                <span>Menu</span>
            </button>
            
            <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
                <a href="<?php echo URLROOT; ?>" class="dropdown-item">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="<?php echo URLROOT; ?>/shop" class="dropdown-item">
                    <i class="fas fa-store"></i>
                    <span>Shop</span>
                </a>
                <a href="<?php echo URLROOT; ?>/shop/sale" class="dropdown-item">
                    <i class="fas fa-tags"></i>
                    <span>Sale</span>
                </a>
                <a href="<?php echo URLROOT; ?>/shop/products" class="dropdown-item">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="<?php echo URLROOT; ?>/shop/deals" class="dropdown-item">
                    <i class="fas fa-percent"></i>
                    <span>Deals</span>
                </a>
                <a href="<?php echo URLROOT; ?>/shop/contact" class="dropdown-item">
                    <i class="fas fa-envelope"></i>
                    <span>Contact</span>
                </a>
            </div>
        </div>
    </div>

    <script>
    // Simple inline test function
    function testMenuClick() {
        console.log('=== INLINE TEST: Button clicked! ===');
        const dropdown = document.getElementById('dropdownMenu');
        if (dropdown) {
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
                dropdown.style.position = 'absolute';
                dropdown.style.top = '70px';
                dropdown.style.left = '0';
                dropdown.style.background = 'white';
                dropdown.style.border = '2px solid red';
                dropdown.style.padding = '10px';
                dropdown.style.zIndex = '9999';
                dropdown.style.width = '200px';
                console.log('MENU SHOWN!');
                alert('Menu should be visible now!');
            } else {
                dropdown.style.display = 'none';
                console.log('MENU HIDDEN!');
            }
        } else {
            console.error('Dropdown not found!');
            alert('ERROR: Dropdown not found!');
        }
    }
    </script>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <video class="hero-video" autoplay muted loop>
                <source src="<?php echo URLROOT; ?>/img/cricket-hero-video.mp4" type="video/mp4">
                <!-- Fallback image if video doesn't load -->
                <div class="hero-fallback" style="background-image: url('<?php echo URLROOT; ?>/img/hero1.jpg')"></div>
            </video>
        </div>
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="title-line">Play Like the Pros</span>
                    <span class="title-highlight">Get the Best Cricket Gear</span>
                </h1>
                <p class="hero-description">
                    Discover premium cricket equipment trusted by professionals worldwide. 
                    From match-winning bats to protective gear, we've got everything you need to elevate your game.
                </p>
                <div class="hero-actions">
                    <a href="<?php echo URLROOT; ?>/shop/accessories" class="btn btn-primary hero-btn">
                        Shop Now
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <button class="btn btn-secondary hero-btn" onclick="scrollToSection('categories')">
                        Browse Categories
                    </button>
                </div>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Premium Products</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Pro Teams</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Shop Content with Sidebar -->
    <div class="shop-layout">
        <!-- Modern Sidebar -->
        <aside class="modern-sidebar" id="modernSidebar">
            <div class="sidebar-header">
                <button class="sidebar-close" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Home -->
                <div class="nav-item">
                    <a href="<?php echo URLROOT; ?>" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </div>

                <!-- Products with Categories -->
                <div class="nav-item has-submenu">
                    <div class="nav-link submenu-toggle" id="productsToggle">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="submenu" id="productsSubmenu">
                        <?php foreach($data['categories'] as $category): ?>
                            <a href="<?php echo URLROOT; ?>/shop/category/<?php echo $category['id']; ?>" class="submenu-item">
                                <span class="category-icon"><?php echo $category['icon']; ?></span>
                                <span><?php echo $category['name']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sale -->
                <div class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/sale" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Sale</span>
                    </a>
                </div>

                <!-- Logout -->
                <div class="nav-item logout-item">
                    <a href="<?php echo URLROOT; ?>/login/logout" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="shop-main">
            <!-- Featured Products -->
            <section class="featured-products" id="featured">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">Featured Products</h2>
                        <p class="section-subtitle">Top-selling cricket equipment chosen by professionals</p>
                        <button class="mobile-filter-btn" id="mobileFilterBtn">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                    </div>
            <div class="products-grid">
                <?php foreach($data['featured_products'] as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <img src="<?php echo URLROOT; ?>/img/products/<?php echo $product['image']; ?>" 
                                 alt="<?php echo $product['name']; ?>"
                                 onerror="this.src='<?php echo URLROOT; ?>/img/products/placeholder.jpg'">
                            <div class="product-overlay">
                                <button class="overlay-btn quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-eye"></i> Quick View
                                </button>
                            </div>
                            <?php if($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <div class="product-badge sale-badge">
                                    <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                                </div>
                            <?php endif; ?>
                            <?php if($product['stock'] <= 5): ?>
                                <div class="product-badge stock-badge">Low Stock</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-brand"><?php echo $product['brand']; ?></div>
                            <h3 class="product-name"><?php echo $product['name']; ?></h3>
                            <div class="product-rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= floor($product['rating']) ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                                <span class="rating-count">(<?php echo rand(10, 150); ?>)</span>
                            </div>
                            <div class="product-price">
                                <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if($product['original_price']): ?>
                                    <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-primary add-to-cart-btn" 
                                    data-product-id="<?php echo $product['id']; ?>"
                                    data-product-name="<?php echo $product['name']; ?>"
                                    data-product-price="<?php echo $product['price']; ?>">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories" id="categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Find exactly what you need for your cricket game</p>
            </div>
            <div class="categories-grid">
                <?php foreach($data['categories'] as $category): ?>
                    <div class="category-card" data-category-id="<?php echo $category['id']; ?>">
                        <div class="category-image">
                            <img src="<?php echo URLROOT; ?>/img/categories/<?php echo $category['image']; ?>" 
                                 alt="<?php echo $category['name']; ?>"
                                 onerror="this.src='<?php echo URLROOT; ?>/img/products/placeholder.jpg'">
                        </div>
                        <div class="category-content">
                            <div class="category-icon"><?php echo $category['icon']; ?></div>
                            <h3 class="category-name"><?php echo $category['name']; ?></h3>
                            <p class="category-count"><?php echo $category['product_count']; ?> Products</p>
                        </div>
                        <div class="category-overlay">
                            <a href="<?php echo URLROOT; ?>/shop/accessories" class="shop-now-btn">Shop Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Offers / Deals Section -->
    <section class="offers" id="offers">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Special Offers</h2>
                <p class="section-subtitle">Don't miss these amazing deals on premium cricket gear</p>
            </div>
            <div class="offers-grid">
                <?php foreach($data['deals'] as $deal): ?>
                    <div class="offer-card">
                        <div class="offer-image">
                            <img src="<?php echo URLROOT; ?>/img/deals/<?php echo $deal['image']; ?>" 
                                 alt="<?php echo $deal['title']; ?>"
                                 onerror="this.src='<?php echo URLROOT; ?>/img/deals/placeholder.jpg'">
                        </div>
                        <div class="offer-content">
                            <div class="offer-badge"><?php echo $deal['discount']; ?> OFF</div>
                            <h3 class="offer-title"><?php echo $deal['title']; ?></h3>
                            <p class="offer-description"><?php echo $deal['description']; ?></p>
                            <button class="btn btn-primary offer-btn"><?php echo $deal['cta']; ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Customers Say</h2>
                <p class="section-subtitle">Join thousands of satisfied cricketers worldwide</p>
            </div>
            <div class="testimonials-carousel">
                <div class="testimonials-container">
                    <?php foreach($data['testimonials'] as $testimonial): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <div class="testimonial-rating">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'filled' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="testimonial-text">"<?php echo $testimonial['text']; ?>"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-image">
                                    <img src="<?php echo URLROOT; ?>/img/testimonials/<?php echo $testimonial['image']; ?>" 
                                         alt="<?php echo $testimonial['name']; ?>"
                                         onerror="this.src='<?php echo URLROOT; ?>/img/testimonials/placeholder.jpg'">
                                </div>
                                <div class="author-info">
                                    <h4 class="author-name"><?php echo $testimonial['name']; ?></h4>
                                    <p class="author-role"><?php echo $testimonial['role']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-controls">
                    <button class="carousel-btn prev-btn" id="prev-testimonial">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-btn next-btn" id="next-testimonial">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
        </main>
    </div>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2 class="newsletter-title">Stay Updated</h2>
                    <p class="newsletter-description">
                        Get the latest cricket gear updates, exclusive offers, and expert tips delivered to your inbox.
                    </p>
                </div>
                <form class="newsletter-form" id="newsletter-form">
                    <div class="input-group">
                        <input type="email" placeholder="Enter your email address" required id="newsletter-email">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <div class="logo-icon">
                            <i class="fas fa-cricket-bat-ball"></i>
                        </div>
                        <span class="logo-text">Elite Cricket Gear</span>
                    </div>
                    <p class="footer-description">
                        Your trusted partner for premium cricket equipment. 
                        Play like a pro with gear that champions trust.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo URLROOT; ?>">Home</a></li>
                        <li><a href="<?php echo URLROOT; ?>/shop">Shop</a></li>
                        <li><a href="<?php echo URLROOT; ?>/pages/about">About Us</a></li>
                        <li><a href="<?php echo URLROOT; ?>/pages/contact">Contact</a></li>
                        <li><a href="#">Track Order</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Categories</h3>
                    <ul class="footer-links">
                        <?php foreach(array_slice($data['categories'], 0, 5) as $category): ?>
                            <li><a href="<?php echo URLROOT; ?>/shop/category/<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Contact Info</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Cricket Street, Sports City, SC 12345</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@elitecricketgear.com</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p class="copyright">© 2024 Elite Cricket Gear. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Return Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="close-cart" id="close-cart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-content" id="cart-content">
            <div class="cart-empty" id="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <button class="btn btn-primary" onclick="closeCart()">Continue Shopping</button>
            </div>
        </div>
        <div class="cart-footer" id="cart-footer">
            <div class="cart-total">
                <span>Total: $<span id="cart-total">0.00</span></span>
            </div>
            <div class="cart-actions">
                <button class="btn btn-outline" onclick="closeCart()">Continue Shopping</button>
                <button class="btn btn-primary">Checkout</button>
            </div>
        </div>
    </div>

    <!-- Cart Overlay -->
    <div class="cart-overlay" id="cart-overlay"></div>
    
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Quick View Modal -->
    <div class="modal" id="quick-view-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Quick View</h3>
                <button class="close-modal" id="close-quick-view">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="quick-view-content">
                <!-- Quick view content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo URLROOT; ?>/js/shop/shop-home.js"></script>
</body>
</html>