<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
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
                    <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/playerslots" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
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
    <div class="main-content" id="shoppingPage" data-urlroot="<?php echo URLROOT; ?>">
        <!-- Shopping Header -->
        <div class="page-header">
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
            <?php
                $products = $data['products'] ?? [];

                $normKey = function ($value) {
                    return strtolower(trim((string)$value));
                };

                $categoryOptions = [];
                $brandOptions = [];
                if (!empty($products)) {
                    foreach ($products as $p) {
                        if (!empty($p->Category)) {
                            $categoryOptions[(string)$p->Category] = true;
                        }
                        if (!empty($p->Brand)) {
                            $brandOptions[(string)$p->Brand] = true;
                        }
                    }
                }
                ksort($categoryOptions);
                ksort($brandOptions);

                $categoryIconClass = function ($categoryLabel) {
                    $c = strtolower(trim((string)$categoryLabel));
                    if ($c === 'batting') return 'fas fa-baseball-ball';
                    if ($c === 'bowling') return 'fas fa-bullseye';
                    if ($c === 'training') return 'fas fa-dumbbell';
                    if ($c === 'protective') return 'fas fa-shield-alt';
                    return 'fas fa-tag';
                };
            ?>

            <!-- Category Navigation -->
            <div class="page-navigation" id="product-category-navigation">
                <button type="button" class="nav-btn active" data-category="all">
                    <i class="fas fa-th-large"></i>
                    All Categories
                </button>
                <?php foreach (array_keys($categoryOptions) as $category) : ?>
                    <?php $categoryKey = $normKey($category); ?>
                    <button type="button" class="nav-btn" data-category="<?php echo htmlspecialchars($categoryKey, ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="<?php echo htmlspecialchars($categoryIconClass($category), ENT_QUOTES, 'UTF-8'); ?>"></i>
                        <?php echo htmlspecialchars((string)$category, ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="section-header">
                <div>
                    <h2>Cricket Equipment & Gear</h2>
                    <p>Professional-grade cricket equipment for players of all levels</p>
                </div>
                <div class="section-filters">
                   
                    <select class="filter-select" id="brand-filter">
                        <option value="all">All Brands</option>
                        <?php foreach (array_keys($brandOptions) as $brand): ?>
                            <option value="<?php echo htmlspecialchars($normKey($brand), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="filter-select" id="price-filter">
                        <option value="all">All Prices</option>
                        <option value="0-1000">Under Rs. 1,000</option>
                        <option value="1000-5000">Rs. 1,000 - 5,000</option>
                        <option value="5000-10000">Rs. 5,000 - 10,000</option>
                        <option value="10000-25000">Rs. 10,000 - 25,000</option>
                        <option value="25000+">Over Rs. 25,000</option>
                    </select>
                </div>
            </div>
            
            <div class="items-grid" id="products-grid">
                <?php if (!empty($data['products'])): ?>
                    <?php foreach ($data['products'] as $product): ?>
                            <div class="card-item product-card"
                                data-category="<?php echo htmlspecialchars($normKey($product->Category ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                data-brand="<?php echo htmlspecialchars($normKey($product->Brand ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                             data-price="<?php echo $product->Price ?? 0; ?>"
                             data-product-id="<?php echo $product->ProductID; ?>">

                            <div class="card-image">
                                <?php
                                $imagePath = !empty($product->ProductImage)
                                    ? URLROOT . '/' . $product->ProductImage
                                    : 'https://via.placeholder.com/300x200?text=' . urlencode($product->Name ?? 'Product');
                                ?>
                                <img src="<?php echo $imagePath; ?>"
                                     alt="<?php echo htmlspecialchars($product->Name ?? 'Product'); ?>" />
                            </div>

                            <div class="card-body">
                                <?php if (!empty($product->Brand)): ?>
                                    <div class="product-brand"><?php echo htmlspecialchars($product->Brand); ?></div>
                                <?php endif; ?>

                                <h3 class="card-title"><?php echo htmlspecialchars($product->Name ?? 'Unnamed Product'); ?></h3>

                                <p class="card-description">
                                    <?php echo htmlspecialchars(mb_strimwidth((string)($product->Description ?? 'No description available.'), 0, 95, '...')); ?>
                                </p>

                              

                                <?php if (!empty($product->Category)): ?>
                                    <div class="card-tags">
                                        <span class="card-tag"><?php echo htmlspecialchars($product->Category); ?></span>
                                        <?php if (!empty($product->Brand)): ?>
                                            <span class="card-tag"><?php echo htmlspecialchars($product->Brand); ?></span>
                                        <?php endif; ?>
                                        <?php if ($product->StockQuantity > 10): ?>
                                            <span class="card-tag">In Stock</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="price-section">
                                    <span class="price-current">Rs. <?php echo number_format($product->Price ?? 0, 2); ?></span>
                                </div>

                                <div class="stock-summary <?php echo ($product->StockQuantity ?? 0) > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                    <?php if (($product->StockQuantity ?? 0) > 0): ?>
                                        <?php echo (int)$product->StockQuantity; ?> available
                                    <?php else: ?>
                                        Out of stock
                                    <?php endif; ?>
                                </div>

                                

                                <div class="product-actions">
                                    <button class="btn btn-view js-view-product"
                                            data-product-id="<?php echo $product->ProductID; ?>">
                                        View Details
                                    </button>
                                    <?php if ($product->StockQuantity > 0): ?>
                                        <button class="btn btn-cart add-to-cart"
                                                data-product-id="<?php echo $product->ProductID; ?>"
                                                data-name="<?php echo htmlspecialchars($product->Name); ?>"
                                                data-price="<?php echo $product->Price; ?>"
                                                data-image="<?php echo $imagePath; ?>">
                                            Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-cart" disabled>Out of Stock</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-shopping-bag" style="font-size: 48px; color: #ccc; margin-bottom: 1rem;"></i>
                        <h3>No Products Available</h3>
                        <p>Check back soon for new products!</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

          

       
       
                
    </div>
</div>

<!-- Product Details Modal -->
<div id="productDetailsModal" class="modal-overlay" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <div class="header-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="header-text">
                <h3>Product Details</h3>
                <p>Complete product information and specifications</p>
            </div>
            <button class="modal-close-btn" onclick="closeProductDetails()">
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
                                <span class="current-price">Rs. <span id="productDetailPrice">0.00</span></span>
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
                    </div>
                </div>

                <div class="product-specifications">
                    <h4><i class="fas fa-database"></i> Database Fields</h4>
                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-label">Status:</span>
                            <span class="spec-value" id="productDetailStatusText">-</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Added Date:</span>
                            <span class="spec-value" id="productDetailAddedDate">-</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Updated By:</span>
                            <span class="spec-value" id="productDetailUpdatedBy">-</span>
                        </div>
                    </div>
                </div>

                <div class="product-specifications product-record-section">
                    <h4><i class="fas fa-table"></i> Full Product Record</h4>
                    <div id="productRecordGrid" class="record-grid"></div>
                </div>

                <!-- Product Actions -->
                <div class="product-actions-section">
                    <div class="quantity-selector">
                        <label for="productQuantity">Quantity:</label>
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn js-qty-decrease">-</button>
                            <input type="number" id="productQuantity" value="1" min="1" max="10">
                            <button type="button" class="qty-btn js-qty-increase">+</button>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-modal primary js-add-to-cart-details">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn-modal secondary" onclick="buyNowFromDetails()">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </div>
                </div>

                <!-- Additional Product Information -->
                <div class="product-additional-info">
                    <div class="info-tabs">
                        <button class="tab-btn active" onclick="showTab('shipping', this)">Shipping Info</button>
                        <button class="tab-btn" onclick="showTab('warranty', this)">Warranty</button>
                    </div>

                    <div class="tab-content">


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

        <div class="modal-actions">
            <button type="button" class="btn-modal secondary js-close-product-details">
                <i class="fas fa-times"></i> Close
            </button>
            <button type="button" class="btn-modal primary js-add-to-cart-details">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </div>
    </div>
</div>
<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>