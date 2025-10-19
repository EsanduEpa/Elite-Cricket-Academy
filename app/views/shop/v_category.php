<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-category.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Include main shop header -->
    <?php require_once 'components/shop-header.php'; ?>

    <!-- Category Hero Section -->
    <section class="category-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="<?php echo URLROOT; ?>">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="<?php echo URLROOT; ?>/shop">Shop</a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo $data['category']['name'] ?? 'Category'; ?></span>
            </div>
            <h1 class="category-title"><?php echo $data['category']['name'] ?? 'Products'; ?></h1>
            <p class="category-description"><?php echo $data['category']['description'] ?? 'Browse our premium cricket equipment'; ?></p>
        </div>
    </section>

    <!-- Filters and Products -->
    <section class="products-section">
        <div class="container">
            <div class="products-layout">
                <!-- Filters Sidebar -->
                <aside class="filters-sidebar">
                    <div class="filter-section">
                        <h3>Price Range</h3>
                        <div class="price-range">
                            <input type="range" min="0" max="1000" value="500" id="price-range">
                            <div class="price-labels">
                                <span>$0</span>
                                <span>$1000+</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <h3>Brand</h3>
                        <div class="filter-options">
                            <label><input type="checkbox"> Elite Pro</label>
                            <label><input type="checkbox"> Elite Match</label>
                            <label><input type="checkbox"> Elite Guard</label>
                            <label><input type="checkbox"> Elite Safety</label>
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <h3>Rating</h3>
                        <div class="rating-filters">
                            <label><input type="radio" name="rating"> 4+ Stars</label>
                            <label><input type="radio" name="rating"> 3+ Stars</label>
                            <label><input type="radio" name="rating"> 2+ Stars</label>
                        </div>
                    </div>
                </aside>

                <!-- Products Grid -->
                <main class="products-main">
                    <div class="products-header">
                        <div class="results-info">
                            <span><?php echo count($data['products'] ?? []); ?> products found</span>
                        </div>
                        <div class="sort-options">
                            <select id="sort-select">
                                <option value="featured">Featured</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="rating">Customer Rating</option>
                                <option value="newest">Newest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="products-grid">
                        <?php if(isset($data['products']) && !empty($data['products'])): ?>
                            <?php foreach($data['products'] as $product): ?>
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
                                        <?php if(isset($product['discount']) && $product['discount'] > 0): ?>
                                            <div class="product-badge sale-badge"><?php echo $product['discount']; ?>% OFF</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-brand"><?php echo $product['brand'] ?? 'Elite'; ?></div>
                                        <h3 class="product-name"><?php echo $product['name']; ?></h3>
                                        <div class="product-rating">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= ($product['rating'] ?? 4) ? 'filled' : ''; ?>"></i>
                                            <?php endfor; ?>
                                            <span class="rating-count">(<?php echo rand(10, 150); ?>)</span>
                                        </div>
                                        <div class="product-price">
                                            <span class="current-price">$<?php echo number_format($product['price'] ?? 99.99, 2); ?></span>
                                            <?php if(isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                                <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-primary add-to-cart-btn" 
                                                data-product-id="<?php echo $product['id']; ?>"
                                                data-product-name="<?php echo $product['name']; ?>"
                                                data-product-price="<?php echo $product['price'] ?? 99.99; ?>">
                                            <i class="fas fa-shopping-cart"></i>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-products">
                                <i class="fas fa-box-open"></i>
                                <h3>No products found</h3>
                                <p>Try adjusting your filters or browse other categories.</p>
                                <a href="<?php echo URLROOT; ?>/shop" class="btn btn-primary">Browse All Products</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination">
                        <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn">2</button>
                        <button class="pagination-btn">3</button>
                        <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <!-- Include shop footer -->
    <?php require_once '../inc/components/footer.php'; ?>

    <script src="<?php echo URLROOT; ?>/js/shop/shop-category.js"></script>
</body>
</html>