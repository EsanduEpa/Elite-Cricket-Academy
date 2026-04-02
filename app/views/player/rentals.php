<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/rentals.css">
    
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
        <!-- Equipment Rentals Header -->
        <div class="rentals-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-tools"></i> Equipment Rentals</h1>
                    <p>High-quality cricket equipment available for daily or weekly rentals</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="btn btn-facilities">
                        <i class="fas fa-shopping-bag"></i>
                        Back to Shop
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="btn btn-cart">
                        <i class="fas fa-calendar-alt"></i>
                        My Bookings
                    </a>
                </div>
            </div>
        </div>

       

        <!-- Rental Navigation -->
        <div class="rental-navigation">
            <button class="rental-nav-btn active" onclick="filterRentalsByCategory('all')">
                <i class="fas fa-th-large"></i>
                All Equipment
            </button>
            <button class="rental-nav-btn" onclick="filterRentalsByCategory('batting')">
                <i class="fas fa-baseball-ball"></i>
                Batting
            </button>
            <button class="rental-nav-btn" onclick="filterRentalsByCategory('bowling')">
                <i class="fas fa-bullseye"></i>
                Bowling
            </button>
            <button class="rental-nav-btn" onclick="filterRentalsByCategory('training')">
                <i class="fas fa-dumbbell"></i>
                Training
            </button>
            <button class="rental-nav-btn" onclick="filterRentalsByCategory('protective')">
                <i class="fas fa-shield-alt"></i>
                Protective
            </button>
            <button class="rental-nav-btn" onclick="filterRentalsByCategory('other')">
                <i class="fas fa-ellipsis-h"></i>
                Other
            </button>
        </div>

        
        
        <div class="products-grid" id="rentals-grid">
            <?php
                $rentals = $data['rentals'] ?? [];
                $escape = function ($value) {
                    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                };

                $categoryKey = function ($category) {
                    $c = strtolower(trim((string)$category));
                    if ($c === 'batting') return 'batting';
                    if ($c === 'bowling') return 'bowling';
                    if ($c === 'training') return 'training';
                    if ($c === 'protective') return 'protective';
                    return 'other';
                };

                $conditionMeta = function ($condition) {
                    $c = strtolower(trim((string)$condition));
                    if ($c === 'new') return ['excellent', 'Excellent'];
                    if ($c === 'good') return ['good', 'Good'];
                    return ['fair', 'Fair'];
                };

                $placeholderUrl = function ($label) {
                    return 'https://via.placeholder.com/300x200?text=' . urlencode((string)$label);
                };
            ?>

            <?php if (!empty($rentals)) : ?>
                <?php foreach ($rentals as $equipment) : ?>
                    <?php
                        $name = $equipment->Name ?? 'Equipment';
                        $category = $equipment->Category ?? 'Other';
                        $categoryFilterKey = $categoryKey($category);

                        $availability = strtolower((string)($equipment->AvailabilityStatus ?? 'available'));
                        $stock = (int)($equipment->Stock ?? 0);
                        $canRent = ($availability === 'available' && $stock > 0);

                        [$conditionClass, $conditionLabel] = $conditionMeta($equipment->EqCondition ?? 'good');

                        $dailyRate = (float)($equipment->RentalPrice ?? 0);

                        $dbImage = trim((string)($equipment->equipmentImage ?? ''));
                        $image = $dbImage !== ''
                            ? (URLROOT . '/' . ltrim($dbImage, '/'))
                            : $placeholderUrl($name);

                        $imageFallback = $placeholderUrl($name);
                    ?>

                    <div class="product-card" data-category="<?php echo $escape($categoryFilterKey); ?>" data-condition="<?php echo $escape($conditionLabel); ?>">
                        <div class="condition-badge condition-<?php echo $escape($conditionClass); ?>"><?php echo $escape($conditionLabel); ?></div>
                        <div class="rental-image">
                            <img src="<?php echo $escape($image); ?>" alt="<?php echo $escape($name); ?>" onerror="this.src='<?php echo $escape($imageFallback); ?>'" />
                        </div>
                        <div class="product-info">
                            <div class="product-brand"><?php echo $escape($category); ?></div>
                            <h3 class="product-title"><?php echo $escape($name); ?></h3>
                            <?php if (!empty($equipment->Description)) : ?>
                                <p class="product-description"><?php echo $escape($equipment->Description); ?></p>
                            <?php endif; ?>
                            <div class="product-price">
                                <span class="price-current">Rs. <?php echo number_format($dailyRate, 2); ?>/day</span>
                            </div>
                            <div class="product-stock">
                                <?php if ($canRent) : ?>
                                    ✓ Available (<?php echo (int)$stock; ?> in stock)
                                <?php else : ?>
                                    Unavailable
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-cart rent-equipment" data-equipment-id="<?php echo $escape($equipment->EquipmentID ?? ''); ?>" data-name="<?php echo $escape($name); ?>" data-condition="<?php echo $escape($conditionLabel); ?>" data-rate="<?php echo $escape($dailyRate); ?>" <?php echo $canRent ? '' : 'disabled'; ?>>
                                    Rent Now
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="payment-card" style="grid-column: 1 / -1;">
                    <p>No equipment found in the database.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rental Information -->
        <div class="rental-info-section">
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="info-content">
                        <h4>Flexible Rental Periods</h4>
                        <p>Rent equipment for as little as 1 day or up to several weeks. Perfect for short practice sessions or extended training camps.</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="info-content">
                        <h4>Free Pickup & Delivery</h4>
                        <p>We offer free pickup and delivery service within 10km of the academy. Convenient scheduling available 7 days a week.</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Insurance Included</h4>
                        <p>All rental equipment comes with comprehensive insurance coverage. No need to worry about accidental damage during use.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>

<style>
.rental-features {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.info-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.info-icon {
    font-size: 2.5rem;
    color: #4A90E2;
    margin-bottom: 1rem;
}

.info-content h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.info-content p {
    color: #7f8c8d;
    line-height: 1.6;
}
</style>

<script src="<?php echo URLROOT; ?>/js/player/rentals.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>