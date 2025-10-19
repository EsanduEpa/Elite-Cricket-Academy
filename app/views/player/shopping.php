<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
    
<div class="player-layout">
    <!-- Sidebar -->
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
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                        <i class="fas fa-medal"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shopping</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
            <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Shopping Header -->
        <div class="shopping-header">
            <div class="header-content">
                <div class="header-text">
                    <h1>Elite Cricket Academy Shop</h1>
                    <p>Premium cricket equipment, rentals, and facility bookings</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/rentals" class="btn btn-rentals">
                        <i class="fas fa-tools"></i>
                        Equipment Rentals
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/facilities" class="btn btn-facilities">
                        <i class="fas fa-building"></i>
                        Facility Booking
                    </a>
                    <button class="btn btn-cart" id="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                        <span class="cart-count" id="cart-count">0</span>
                    </button>
                </div>
            </div>
        </div>


        

        <!-- Products Section -->
        <div id="products-section" class="shop-section active">
            <div class="section-header">
                <div>
                    <h2>Cricket Equipment & Gear</h2>
                    <p>Professional-grade cricket equipment for players of all levels</p>
                </div>
                <div class="shop-filters">
                    <select class="filter-select" id="category-filter">
                        <option value="all">All Categories</option>
                        <option value="bats">Cricket Bats</option>
                        <option value="protective">Protective Gear</option>
                        <option value="footwear">Footwear</option>
                        <option value="clothing">Clothing</option>
                        <option value="accessories">Accessories</option>
                    </select>
                    <select class="filter-select" id="brand-filter">
                        <option value="all">All Brands</option>
                        <option value="gray-nicolls">Gray-Nicolls</option>
                        <option value="kookaburra">Kookaburra</option>
                        <option value="new-balance">New Balance</option>
                        <option value="gunn-moore">Gunn & Moore</option>
                    </select>
                    <select class="filter-select" id="price-filter">
                        <option value="all">All Prices</option>
                        <option value="0-50">Under $50</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100-250">$100 - $250</option>
                        <option value="250-500">$250 - $500</option>
                        <option value="500+">Over $500</option>
                    </select>
                </div>
            </div>
            
            <div class="products-grid" id="products-grid">
                <!-- Cricket Bats -->
                <div class="product-card" data-category="bats" data-brand="gray-nicolls" data-price="450">
                    <div class="discount-badge">15% OFF</div>
                    <div class="product-image">
                        <img src="<?php echo URLROOT; ?>/img/products/bat-pro.jpg" alt="Professional Cricket Bat" onerror="this.src='https://via.placeholder.com/300x200?text=Cricket+Bat'" />
                    </div>
                    <div class="product-info">
                        <div class="product-brand">Gray-Nicolls</div>
                        <h3 class="product-title">Powerbow 6X Pro Cricket Bat</h3>
                        <p class="product-description">Premium English willow bat with advanced edge profile and massive hitting zone</p>
                        <div class="product-rating">
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <span class="rating-text">(24 reviews)</span>
                        </div>
                        <div class="product-features">
                            <span class="feature-tag">English Willow</span>
                            <span class="feature-tag">Professional Grade</span>
                            <span class="feature-tag">Lightweight</span>
                        </div>
                        <div class="product-price">
                            <span class="price-original">$529.99</span>
                            <span class="price-discounted">$450.00</span>
                        </div>
                        <div class="product-stock">✓ In Stock (3 available)</div>
                        <div class="product-actions">
                            <button class="btn btn-view" onclick="viewProduct('bat-pro')">View Details</button>
                            <button class="btn btn-cart add-to-cart" data-product="bat-pro" data-name="Powerbow 6X Pro Cricket Bat" data-price="450" data-image="<?php echo URLROOT; ?>/img/products/bat-pro.jpg">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <div class="product-card" data-category="bats" data-brand="kookaburra" data-price="320">
                    <div class="product-image">
                        <img src="<?php echo URLROOT; ?>/img/products/bat-kahuna.jpg" alt="Kahuna Cricket Bat" onerror="this.src='https://via.placeholder.com/300x200?text=Cricket+Bat'" />
                    </div>
                    <div class="product-info">
                        <div class="product-brand">Kookaburra</div>
                        <h3 class="product-title">Kahuna 4.0 Cricket Bat</h3>
                        <p class="product-description">Premium Kashmir willow bat with enhanced sweet spot and exceptional balance</p>
                        <div class="product-rating">
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-text">(18 reviews)</span>
                        </div>
                        <div class="product-features">
                            <span class="feature-tag">Kashmir Willow</span>
                            <span class="feature-tag">Balanced</span>
                            <span class="feature-tag">Youth Friendly</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">$320.00</span>
                        </div>
                        <div class="product-stock">✓ In Stock (5 available)</div>
                        <div class="product-actions">
                            <button class="btn btn-view" onclick="viewProduct('bat-kahuna')">View Details</button>
                            <button class="btn btn-cart add-to-cart" data-product="bat-kahuna" data-name="Kahuna 4.0 Cricket Bat" data-price="320" data-image="<?php echo URLROOT; ?>/img/products/bat-kahuna.jpg">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <!-- Protective Gear -->
                <div class="product-card" data-category="protective" data-brand="kookaburra" data-price="180">
                    <div class="product-image">
                        <img src="<?php echo URLROOT; ?>/img/products/pads-pro.jpg" alt="Cricket Pads" onerror="this.src='https://via.placeholder.com/300x200?text=Cricket+Pads'" />
                    </div>
                    <div class="product-info">
                        <div class="product-brand">Kookaburra</div>
                        <h3 class="product-title">Pro 2.0 Batting Pads</h3>
                        <p class="product-description">Lightweight batting pads with superior protection and comfort for long innings</p>
                        <div class="product-rating">
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-text">(15 reviews)</span>
                        </div>
                        <div class="product-features">
                            <span class="feature-tag">Lightweight</span>
                            <span class="feature-tag">Adjustable</span>
                            <span class="feature-tag">High Protection</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">$180.00</span>
                        </div>
                        <div class="product-stock">✓ In Stock (8 available)</div>
                        <div class="product-actions">
                            <button class="btn btn-view" onclick="viewProduct('pads-pro')">View Details</button>
                            <button class="btn btn-cart add-to-cart" data-product="pads-pro" data-name="Pro 2.0 Batting Pads" data-price="180" data-image="<?php echo URLROOT; ?>/img/products/pads-pro.jpg">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <div class="product-card" data-category="protective" data-brand="gray-nicolls" data-price="85">
                    <div class="product-image">
                        <img src="<?php echo URLROOT; ?>/img/products/helmet-atomic.jpg" alt="Cricket Helmet" onerror="this.src='https://via.placeholder.com/300x200?text=Cricket+Helmet'" />
                    </div>
                    <div class="product-info">
                        <div class="product-brand">Gray-Nicolls</div>
                        <h3 class="product-title">Atomic Cricket Helmet</h3>
                        <p class="product-description">Advanced protection helmet with titanium grille and superior ventilation system</p>
                        <div class="product-rating">
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <span class="rating-text">(32 reviews)</span>
                        </div>
                        <div class="product-features">
                            <span class="feature-tag">Titanium Grille</span>
                            <span class="feature-tag">Ventilated</span>
                            <span class="feature-tag">Adjustable</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">$85.00</span>
                        </div>
                        <div class="product-stock">✓ In Stock (12 available)</div>
                        <div class="product-actions">
                            <button class="btn btn-view" onclick="viewProduct('helmet-atomic')">View Details</button>
                            <button class="btn btn-cart add-to-cart" data-product="helmet-atomic" data-name="Atomic Cricket Helmet" data-price="85" data-image="<?php echo URLROOT; ?>/img/products/helmet-atomic.jpg">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <!-- Footwear -->
                <div class="product-card" data-category="footwear" data-brand="new-balance" data-price="160">
                    <div class="discount-badge">20% OFF</div>
                    <div class="product-image">
                        <img src="<?php echo URLROOT; ?>/img/products/spikes-tc.jpg" alt="Cricket Spikes" onerror="this.src='https://via.placeholder.com/300x200?text=Cricket+Spikes'" />
                    </div>
                    <div class="product-info">
                        <div class="product-brand">New Balance</div>
                        <h3 class="product-title">TC 4040v5 Cricket Spikes</h3>
                        <p class="product-description">Professional cricket spikes with superior grip and all-day comfort</p>
                        <div class="product-rating">
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-text">(27 reviews)</span>
                        </div>
                        <div class="product-features">
                            <span class="feature-tag">Metal Spikes</span>
                            <span class="feature-tag">Breathable</span>
                            <span class="feature-tag">Lightweight</span>
                        </div>
                        <div class="product-price">
                            <span class="price-original">$200.00</span>
                            <span class="price-discounted">$160.00</span>
                        </div>
                        <div class="product-stock">✓ In Stock (6 available)</div>
                        <div class="product-actions">
                            <button class="btn btn-view" onclick="viewProduct('spikes-tc')">View Details</button>
                            <button class="btn btn-cart add-to-cart" data-product="spikes-tc" data-name="TC 4040v5 Cricket Spikes" data-price="160" data-image="<?php echo URLROOT; ?>/img/products/spikes-tc.jpg">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <!-- Clothing -->
                <div class="product-card" data-category="clothing" data-brand="new-balance" data-price="45">
                    <div class="product-image">
                        <img src="<?php echo URLROOT; ?>/img/products/jersey-team.jpg" alt="Team Jersey" onerror="this.src='https://via.placeholder.com/300x200?text=Team+Jersey'" />
                    </div>
                    <div class="product-info">
                        <div class="product-brand">New Balance</div>
                        <h3 class="product-title">Elite Academy Team Jersey</h3>
                        <p class="product-description">Official team jersey with moisture-wicking fabric and professional fit</p>
                        <div class="product-rating">
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star active"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-text">(41 reviews)</span>
                        </div>
                        <div class="product-features">
                            <span class="feature-tag">Moisture-Wicking</span>
                            <span class="feature-tag">Breathable</span>
                            <span class="feature-tag">Official Design</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">$45.00</span>
                        </div>
                        <div class="product-stock">✓ In Stock (20 available)</div>
                        <div class="product-actions">
                            <button class="btn btn-view" onclick="viewProduct('jersey-team')">View Details</button>
                            <button class="btn btn-cart add-to-cart" data-product="jersey-team" data-name="Elite Academy Team Jersey" data-price="45" data-image="<?php echo URLROOT; ?>/img/products/jersey-team.jpg">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

          

       
       
                
    </div>
</div>

<!-- Shopping Cart Modal -->
<div id="cartModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Shopping Cart</h3>
            <button class="close-btn" onclick="closeCartModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="cart-items"></div>
            <div class="cart-total">
                <strong>Total: $<span id="cart-total">0.00</span></strong>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeCartModal()">Continue Shopping</button>
            <button class="btn btn-primary" onclick="checkout()">Checkout</button>
        </div>
    </div>
</div>





<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>