<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
    
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
                    <a href="<?php echo URLROOT; ?>/player/cart" class="btn btn-cart" id="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
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
                            <span class="price-original">₹529.99</span>
                            <span class="price-discounted">₹450.00</span>
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
                            <span class="price-current">₹320.00</span>
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
                            <span class="price-current">₹180.00</span>
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
                            <span class="price-current">₹85.00</span>
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
                            <span class="price-original">₹200.00</span>
                            <span class="price-discounted">₹160.00</span>
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
                            <span class="price-current">₹45.00</span>
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

<!-- Product Details Modal -->
<div id="productDetailsModal" class="modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="header-text">
                <h3>Product Details</h3>
                <p>Complete product information and specifications</p>
            </div>
            <button class="modal-close" onclick="closeProductDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="product-details-container">
                <!-- Product Image and Basic Info -->
                <div class="product-main-info">
                    <div class="product-image-large">
                        <img id="productDetailImage" src="" alt="Product Image" />
                        <div class="image-badges">
                            <span id="productDetailStatus" class="status-badge"></span>
                        </div>
                    </div>
                    
                    <div class="product-basic-info">
                        <div class="product-header">
                            <h2 id="productDetailName">Product Name</h2>
                            <div class="product-meta">
                                <span class="product-id">ID: #<span id="productDetailID">001</span></span>
                                <span class="product-sku">SKU: <span id="productDetailSKU">SKU-001</span></span>
                            </div>
                        </div>
                        
                        <div class="product-category-brand">
                            <span class="category-badge" id="productDetailCategory">Category</span>
                            <span class="brand-badge" id="productDetailBrand">Brand</span>
                        </div>
                        
                        <div class="product-pricing">
                            <div class="price-info">
                                <span class="current-price">$<span id="productDetailPrice">0.00</span></span>
                                <span class="price-label">Current Price</span>
                            </div>
                            <div class="stock-info">
                                <span class="stock-quantity" id="productDetailStock">0</span>
                                <span class="stock-label">Units Available</span>
                            </div>
                        </div>
                        
                        <div class="product-description">
                            <h4>Product Description</h4>
                            <p id="productDetailDescription">Product description will be displayed here...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product Specifications -->
                <div class="product-specifications">
                    <h4><i class="fas fa-cog"></i> Product Specifications</h4>
                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-label">Weight:</span>
                            <span class="spec-value" id="productDetailWeight">-</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Dimensions:</span>
                            <span class="spec-value" id="productDetailDimensions">-</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Status:</span>
                            <span class="spec-value" id="productDetailStatusText">Active</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Added Date:</span>
                            <span class="spec-value" id="productDetailAddedDate">-</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Last Updated:</span>
                            <span class="spec-value" id="productDetailUpdatedBy">-</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Product ID:</span>
                            <span class="spec-value" id="productDetailProductID">-</span>
                        </div>
                    </div>
                </div>
                
                <!-- Product Actions -->
                <div class="product-actions-section">
                    <div class="quantity-selector">
                        <label for="productQuantity">Quantity:</label>
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn" onclick="adjustQuantity(-1)">-</button>
                            <input type="number" id="productQuantity" value="1" min="1" max="10">
                            <button type="button" class="qty-btn" onclick="adjustQuantity(1)">+</button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn btn-primary btn-large" onclick="addToCartFromDetails()">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn btn-secondary btn-large" onclick="buyNowFromDetails()">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </div>
                </div>
                
                <!-- Additional Product Information -->
                <div class="product-additional-info">
                    <div class="info-tabs">
                        <button class="tab-btn active" onclick="showTab('features')">Features</button>
                        <button class="tab-btn" onclick="showTab('shipping')">Shipping Info</button>
                        <button class="tab-btn" onclick="showTab('warranty')">Warranty</button>
                    </div>
                    
                    <div class="tab-content">
                        <div id="features-tab" class="tab-pane active">
                            <h5>Product Features</h5>
                            <ul id="productFeaturesList">
                                <li>High-quality materials</li>
                                <li>Professional grade equipment</li>
                                <li>Suitable for all skill levels</li>
                                <li>Tested and approved by professionals</li>
                            </ul>
                        </div>
                        
                        <div id="shipping-tab" class="tab-pane">
                            <h5>Shipping Information</h5>
                            <p>Free shipping on orders over $100. Standard delivery takes 3-5 business days.</p>
                            <ul>
                                <li>Express shipping available</li>
                                <li>Same-day delivery for local area</li>
                                <li>Secure packaging guaranteed</li>
                            </ul>
                        </div>
                        
                        <div id="warranty-tab" class="tab-pane">
                            <h5>Warranty & Returns</h5>
                            <p>30-day return policy with full refund. Manufacturer warranty included.</p>
                            <ul>
                                <li>1-year manufacturer warranty</li>
                                <li>Free returns within 30 days</li>
                                <li>Expert support included</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeProductDetails()">
                <i class="fas fa-times"></i> Close
            </button>
            <button type="button" class="btn btn-primary" onclick="addToCartFromDetails()">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </div>
    </div>
</div>





<script>
// Product data with complete database fields
const products = {
    'bat-pro': {
        ProductID: 1,
        Name: 'Powerbow 6X Pro Cricket Bat',
        Description: 'Premium English willow bat with advanced edge profile and massive hitting zone. Handcrafted by expert craftsmen with years of experience. Features superior balance and exceptional pick-up for professional players.',
        Category: 'Batting',
        Brand: 'Gray-Nicolls',
        Price: 450.00,
        StockQuantity: 3,
        Status: 'active',
        SKU: 'GN-PB6X-PRO-001',
        Weight: 1.180, // kg
        Dimensions: '96cm x 10.8cm x 6.5cm',
        AddedDate: '2024-09-15 10:30:00',
        UpdatedBy: 'Shop Manager - John Smith',
        image: '<?php echo URLROOT; ?>/img/products/bat-pro.jpg',
        features: [
            'Premium English Willow Construction',
            'Advanced Edge Profile Technology',
            'Massive Sweet Spot',
            'Professional Grade Balance',
            'Hand-Selected Grains',
            'Superior Pick-up Performance'
        ]
    },
    'bat-kahuna': {
        ProductID: 2,
        Name: 'Kahuna 4.0 Cricket Bat',
        Description: 'Premium Kashmir willow bat with enhanced sweet spot and exceptional balance. Perfect for intermediate to advanced players looking for consistent performance and reliable stroke play.',
        Category: 'Batting',
        Brand: 'Kookaburra',
        Price: 320.00,
        StockQuantity: 5,
        Status: 'active',
        SKU: 'KOO-KAH-4.0-002',
        Weight: 1.150, // kg
        Dimensions: '95cm x 10.5cm x 6.2cm',
        AddedDate: '2024-09-10 14:15:00',
        UpdatedBy: 'Shop Assistant - Sarah Johnson',
        image: '<?php echo URLROOT; ?>/img/products/bat-kahuna.jpg',
        features: [
            'Premium Kashmir Willow',
            'Enhanced Sweet Spot',
            'Exceptional Balance',
            'Youth Friendly Design',
            'Reliable Performance',
            'Comfortable Grip'
        ]
    },
    'pads-pro': {
        ProductID: 3,
        Name: 'Pro 2.0 Batting Pads',
        Description: 'Lightweight batting pads with superior protection and comfort for long innings. Advanced foam padding with ventilation channels for enhanced breathability during extended play.',
        Category: 'Protective',
        Brand: 'Kookaburra',
        Price: 180.00,
        StockQuantity: 8,
        Status: 'active',
        SKU: 'KOO-PRO-PADS-003',
        Weight: 0.850, // kg
        Dimensions: '70cm x 20cm x 15cm',
        AddedDate: '2024-09-05 11:45:00',
        UpdatedBy: 'Shop Manager - John Smith',
        image: '<?php echo URLROOT; ?>/img/products/pads-pro.jpg',
        features: [
            'Lightweight Construction',
            'Superior Protection',
            'Adjustable Straps',
            'High-Density Foam',
            'Ventilation Channels',
            'Extended Wear Comfort'
        ]
    },
    'helmet-atomic': {
        ProductID: 4,
        Name: 'Atomic Cricket Helmet',
        Description: 'Advanced protection helmet with titanium grille and superior ventilation system. Meets all international safety standards with lightweight design for maximum comfort.',
        Category: 'Protective',
        Brand: 'Gray-Nicolls',
        Price: 85.00,
        StockQuantity: 12,
        Status: 'active',
        SKU: 'GN-ATOMIC-HLM-004',
        Weight: 0.650, // kg
        Dimensions: '28cm x 25cm x 22cm',
        AddedDate: '2024-08-30 09:20:00',
        UpdatedBy: 'Shop Assistant - Mike Wilson',
        image: '<?php echo URLROOT; ?>/img/products/helmet-atomic.jpg',
        features: [
            'Titanium Grille Protection',
            'Superior Ventilation',
            'Adjustable Fit System',
            'Lightweight Design',
            'Safety Certified',
            'Comfortable Padding'
        ]
    },
    'spikes-tc': {
        ProductID: 5,
        Name: 'TC 4040v5 Cricket Spikes',
        Description: 'Professional cricket spikes with superior grip and all-day comfort. Advanced sole technology with metal spikes for optimal traction on all cricket surfaces.',
        Category: 'Training',
        Brand: 'New Balance',
        Price: 160.00,
        StockQuantity: 6,
        Status: 'active',
        SKU: 'NB-TC4040-SPK-005',
        Weight: 0.420, // kg (per shoe)
        Dimensions: '30cm x 12cm x 10cm',
        AddedDate: '2024-09-12 16:30:00',
        UpdatedBy: 'Shop Assistant - Sarah Johnson',
        image: '<?php echo URLROOT; ?>/img/products/spikes-tc.jpg',
        features: [
            'Metal Spike Technology',
            'Superior Grip',
            'Breathable Materials',
            'Lightweight Construction',
            'All-Day Comfort',
            'Professional Grade'
        ]
    },
    'jersey-team': {
        ProductID: 6,
        Name: 'Elite Academy Team Jersey',
        Description: 'Official team jersey with moisture-wicking fabric and professional fit. Designed for optimal performance with breathable materials and official academy branding.',
        Category: 'Merchandise',
        Brand: 'New Balance',
        Price: 45.00,
        StockQuantity: 20,
        Status: 'active',
        SKU: 'NB-ELITE-JER-006',
        Weight: 0.180, // kg
        Dimensions: 'Various Sizes Available',
        AddedDate: '2024-08-25 12:00:00',
        UpdatedBy: 'Shop Manager - John Smith',
        image: '<?php echo URLROOT; ?>/img/products/jersey-team.jpg',
        features: [
            'Moisture-Wicking Fabric',
            'Breathable Material',
            'Official Academy Design',
            'Professional Fit',
            'Multiple Sizes',
            'Durable Construction'
        ]
    }
};

// Shopping cart functionality
let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
let currentProduct = null;

// Product Details Modal Functions
function viewProduct(productId) {
    const product = products[productId];
    if (!product) {
        alert('Product not found!');
        return;
    }
    
    currentProduct = product;
    
    // Populate modal with product data
    document.getElementById('productDetailImage').src = product.image;
    document.getElementById('productDetailID').textContent = product.ProductID;
    document.getElementById('productDetailName').textContent = product.Name;
    document.getElementById('productDetailSKU').textContent = product.SKU;
    document.getElementById('productDetailCategory').textContent = product.Category;
    document.getElementById('productDetailBrand').textContent = product.Brand;
    document.getElementById('productDetailPrice').textContent = product.Price.toFixed(2);
    document.getElementById('productDetailStock').textContent = product.StockQuantity;
    document.getElementById('productDetailDescription').textContent = product.Description;
    document.getElementById('productDetailWeight').textContent = product.Weight ? product.Weight + ' kg' : 'Not specified';
    document.getElementById('productDetailDimensions').textContent = product.Dimensions || 'Not specified';
    document.getElementById('productDetailStatusText').textContent = product.Status.charAt(0).toUpperCase() + product.Status.slice(1);
    document.getElementById('productDetailAddedDate').textContent = new Date(product.AddedDate).toLocaleDateString();
    document.getElementById('productDetailUpdatedBy').textContent = product.UpdatedBy || 'System';
    document.getElementById('productDetailProductID').textContent = product.ProductID;
    
    // Set status badge
    const statusBadge = document.getElementById('productDetailStatus');
    statusBadge.textContent = product.Status.charAt(0).toUpperCase() + product.Status.slice(1);
    statusBadge.className = `status-badge status-${product.Status}`;
    
    // Update quantity max based on stock
    const quantityInput = document.getElementById('productQuantity');
    quantityInput.max = product.StockQuantity;
    quantityInput.value = 1;
    
    // Update features list
    const featuresList = document.getElementById('productFeaturesList');
    featuresList.innerHTML = '';
    product.features.forEach(feature => {
        const li = document.createElement('li');
        li.textContent = feature;
        featuresList.appendChild(li);
    });
    
    // Show modal
    document.getElementById('productDetailsModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeProductDetails() {
    document.getElementById('productDetailsModal').style.display = 'none';
    document.body.style.overflow = '';
    currentProduct = null;
}

// Quantity adjustment functions
function adjustQuantity(change) {
    const quantityInput = document.getElementById('productQuantity');
    let newValue = parseInt(quantityInput.value) + change;
    
    if (newValue < 1) newValue = 1;
    if (newValue > parseInt(quantityInput.max)) newValue = parseInt(quantityInput.max);
    
    quantityInput.value = newValue;
}

// Add to cart from details modal
function addToCartFromDetails() {
    if (!currentProduct) return;
    
    const quantity = parseInt(document.getElementById('productQuantity').value);
    addToCart(currentProduct, quantity);
    
    // Show success message
    showNotification(`${currentProduct.Name} added to cart!`, 'success');
}

// Buy now from details modal
function buyNowFromDetails() {
    if (!currentProduct) return;
    
    const quantity = parseInt(document.getElementById('productQuantity').value);
    addToCart(currentProduct, quantity);
    closeProductDetails();
    
    // Redirect to cart page
    window.location.href = '<?php echo URLROOT; ?>/player/cart';
}

// Tab functionality for additional product info
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('active');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

// Enhanced add to cart function
function addToCart(product, quantity = 1) {
    const existingItem = cart.find(item => item.ProductID === product.ProductID);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            ProductID: product.ProductID,
            Name: product.Name,
            Price: product.Price,
            image: product.image,
            quantity: quantity
        });
    }
    
    // Save to localStorage
    localStorage.setItem('shoppingCart', JSON.stringify(cart));
    updateCartCount();
}

// Update cart display
// Update cart count
function updateCartCount() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('cart-count').textContent = count;
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Modal close when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        if (event.target.id === 'productDetailsModal') {
            closeProductDetails();
        }
    }
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Add event listeners for existing add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product');
            const product = products[productId];
            if (product) {
                addToCart(product);
                showNotification(`${product.Name} added to cart!`, 'success');
            }
        });
    });
});
</script>

<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>