<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
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

        <!-- Rental Stats -->
        <div class="rental-stats">
            <div class="rental-stat-card">
                <div class="rental-stat-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="rental-stat-content">
                    <div class="rental-stat-number">25+</div>
                    <div class="rental-stat-label">Equipment Items</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Pickup Available</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">$15</div>
                    <div class="stat-label">Starting From</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Insured</div>
                </div>
            </div>
        </div>

        <!-- Rental Navigation -->
        <div class="rental-navigation">
            <button class="rental-nav-btn active" onclick="filterRentalsByCategory('all')">
                <i class="fas fa-th-large"></i>
                All Equipment
            </button>
            <button class="nav-btn" onclick="filterRentalsByCategory('batting')">
                <i class="fas fa-baseball-ball"></i>
                Batting
            </button>
            <button class="nav-btn" onclick="filterRentalsByCategory('bowling')">
                <i class="fas fa-bullseye"></i>
                Bowling
            </button>
            <button class="nav-btn" onclick="filterRentalsByCategory('training')">
                <i class="fas fa-dumbbell"></i>
                Training
            </button>
        </div>

        <!-- Equipment Rentals Content -->
        <div class="section-header">
            <div>
                <h2>Available Equipment</h2>
                <p>Professional-grade equipment for all your cricket needs</p>
            </div>
            <div class="shop-filters">
                <select class="filter-select" id="rental-category-filter">
                    <option value="all">All Equipment</option>
                    <option value="batting">Batting Equipment</option>
                    <option value="bowling">Bowling Equipment</option>
                    <option value="fielding">Fielding Equipment</option>
                    <option value="training">Training Equipment</option>
                </select>
                <select class="filter-select" id="rental-condition-filter">
                    <option value="all">All Conditions</option>
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                </select>
            </div>
        </div>
        
        <div class="products-grid" id="rental-grid">
            <!-- Professional Cricket Bat Set -->
            <div class="product-card" data-category="batting" data-condition="excellent">
                <div class="condition-badge condition-excellent">Excellent</div>
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/rentals/bat-rental-pro.jpg" alt="Professional Bat Rental" onerror="this.src='https://via.placeholder.com/300x200?text=Professional+Bat+Set'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Premium Rental</div>
                    <h3 class="product-title">Professional Cricket Bat Set</h3>
                    <p class="product-description">Premium English willow bat with matching pads, gloves, and helmet for complete protection</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Excellent condition)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">English Willow</span>
                        <span class="feature-tag">Complete Set</span>
                        <span class="feature-tag">Professional Grade</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$25/day</span>
                        <small style="color: #666; margin-left: 10px;">($150/week)</small>
                    </div>
                    <div class="product-stock">✓ Available for pickup</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewRentalDetails('bat-set-pro')">View Details</button>
                        <button class="btn btn-cart rent-equipment" data-equipment="bat-set-pro" data-name="Professional Cricket Bat Set" data-daily="25" data-weekly="150">Rent Now</button>
                    </div>
                </div>
            </div>

            <!-- Automatic Bowling Machine -->
            <div class="product-card" data-category="bowling" data-condition="excellent">
                <div class="condition-badge condition-excellent">Excellent</div>
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/rentals/bowling-machine.jpg" alt="Bowling Machine" onerror="this.src='https://via.placeholder.com/300x200?text=Bowling+Machine'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Professional Equipment</div>
                    <h3 class="product-title">Automatic Bowling Machine</h3>
                    <p class="product-description">Professional bowling machine with variable speed and swing settings for practice sessions</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Excellent condition)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Variable Speed</span>
                        <span class="feature-tag">Swing Control</span>
                        <span class="feature-tag">Remote Control</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$15/hour</span>
                        <small style="color: #666; margin-left: 10px;">($80/day)</small>
                    </div>
                    <div class="product-stock">✓ Available for booking</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewRentalDetails('bowling-machine')">View Details</button>
                        <button class="btn btn-cart rent-equipment" data-equipment="bowling-machine" data-name="Automatic Bowling Machine" data-hourly="15" data-daily="80">Rent Now</button>
                    </div>
                </div>
            </div>

            <!-- Complete Training Kit -->
            <div class="product-card" data-category="training" data-condition="good">
                <div class="condition-badge condition-good">Good</div>
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/rentals/training-kit.jpg" alt="Training Kit" onerror="this.src='https://via.placeholder.com/300x200?text=Training+Kit'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Training Equipment</div>
                    <h3 class="product-title">Complete Training Kit</h3>
                    <p class="product-description">Includes cones, stumps, practice balls, and agility equipment for comprehensive training</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star"></i>
                        <span class="rating-text">(Good condition)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Complete Set</span>
                        <span class="feature-tag">Portable</span>
                        <span class="feature-tag">Multi-Purpose</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$20/day</span>
                        <small style="color: #666; margin-left: 10px;">($120/week)</small>
                    </div>
                    <div class="product-stock">✓ Available for pickup</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewRentalDetails('training-kit')">View Details</button>
                        <button class="btn btn-cart rent-equipment" data-equipment="training-kit" data-name="Complete Training Kit" data-daily="20" data-weekly="120">Rent Now</button>
                    </div>
                </div>
            </div>

            <!-- Junior Cricket Set -->
            <div class="product-card" data-category="batting" data-condition="good">
                <div class="condition-badge condition-good">Good</div>
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/rentals/junior-set.jpg" alt="Junior Cricket Set" onerror="this.src='https://via.placeholder.com/300x200?text=Junior+Cricket+Set'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Youth Equipment</div>
                    <h3 class="product-title">Junior Cricket Set</h3>
                    <p class="product-description">Perfect starter set for young players including lightweight bat, pads, and safety gear</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star"></i>
                        <span class="rating-text">(Good condition)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Youth Size</span>
                        <span class="feature-tag">Lightweight</span>
                        <span class="feature-tag">Safety First</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$15/day</span>
                        <small style="color: #666; margin-left: 10px;">($90/week)</small>
                    </div>
                    <div class="product-stock">✓ Available for pickup</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewRentalDetails('junior-set')">View Details</button>
                        <button class="btn btn-cart rent-equipment" data-equipment="junior-set" data-name="Junior Cricket Set" data-daily="15" data-weekly="90">Rent Now</button>
                    </div>
                </div>
            </div>

            <!-- Wicket Keeping Set -->
            <div class="product-card" data-category="fielding" data-condition="excellent">
                <div class="condition-badge condition-excellent">Excellent</div>
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/rentals/wicket-keeping.jpg" alt="Wicket Keeping Set" onerror="this.src='https://via.placeholder.com/300x200?text=Wicket+Keeping+Set'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Specialist Equipment</div>
                    <h3 class="product-title">Professional Wicket Keeping Set</h3>
                    <p class="product-description">Complete wicket keeper gear including pads, gloves, and inner gloves for maximum protection</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Excellent condition)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Professional Grade</span>
                        <span class="feature-tag">Complete Set</span>
                        <span class="feature-tag">Comfortable Fit</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$22/day</span>
                        <small style="color: #666; margin-left: 10px;">($130/week)</small>
                    </div>
                    <div class="product-stock">✓ Available for pickup</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewRentalDetails('wicket-keeping')">View Details</button>
                        <button class="btn btn-cart rent-equipment" data-equipment="wicket-keeping" data-name="Professional Wicket Keeping Set" data-daily="22" data-weekly="130">Rent Now</button>
                    </div>
                </div>
            </div>

            <!-- Practice Stumps Set -->
            <div class="product-card" data-category="training" data-condition="good">
                <div class="condition-badge condition-good">Good</div>
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/rentals/stumps-set.jpg" alt="Practice Stumps" onerror="this.src='https://via.placeholder.com/300x200?text=Practice+Stumps'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Training Equipment</div>
                    <h3 class="product-title">Practice Stumps & Balls Set</h3>
                    <p class="product-description">Multiple sets of stumps with practice balls for batting and bowling practice sessions</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star"></i>
                        <span class="rating-text">(Good condition)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Multiple Sets</span>
                        <span class="feature-tag">Durable</span>
                        <span class="feature-tag">Easy Setup</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$12/day</span>
                        <small style="color: #666; margin-left: 10px;">($70/week)</small>
                    </div>
                    <div class="product-stock">✓ Available for pickup</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewRentalDetails('stumps-set')">View Details</button>
                        <button class="btn btn-cart rent-equipment" data-equipment="stumps-set" data-name="Practice Stumps & Balls Set" data-daily="12" data-weekly="70">Rent Now</button>
                    </div>
                </div>
            </div>
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

<!-- Equipment Rental Modal -->
<div id="rentalModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Equipment Rental</h3>
            <button class="close-btn" onclick="closeRentalModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="rental-details"></div>
            <form id="rental-form">
                <div class="form-group">
                    <label for="rental-start">Start Date:</label>
                    <input type="date" id="rental-start" required>
                </div>
                <div class="form-group">
                    <label for="rental-end">End Date:</label>
                    <input type="date" id="rental-end" required>
                </div>
                <div class="form-group">
                    <label for="rental-period">Rental Period:</label>
                    <select id="rental-period">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
            </form>
            <div class="rental-total">
                <strong>Total: $<span id="rental-total">0.00</span></strong>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeRentalModal()">Cancel</button>
            <button class="btn btn-primary" onclick="confirmRental()">Confirm Rental</button>
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

<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/rentals.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>