<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/facilities.css">
    
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
                    <a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Book Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="nav-link">
                        <i class="fas fa-list-alt"></i>
                        <span>My Sessions</span>
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
                <li class="nav-item">
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
        <!-- Facility Booking Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-building"></i> Facility Booking</h1>
                    <p>Book our premium facilities for training, practice, matches, and events</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="btn btn-facilities">
                        <i class="fas fa-shopping-bag"></i>
                        Back to Shop
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/rentals" class="btn btn-cart">
                        <i class="fas fa-tools"></i>
                        Equipment Rentals
                    </a>
                </div>
            </div>
        </div>

       

        <!-- Facility Navigation -->
        <div class="page-navigation">
            <button class="nav-btn active" onclick="filterFacilitiesByType('all')">
                <i class="fas fa-th-large"></i>
                All Facilities
            </button>
            <button class="nav-btn" onclick="filterFacilitiesByType('indoor')">
                <i class="fas fa-home"></i>
                Indoor
            </button>
            <button class="nav-btn" onclick="filterFacilitiesByType('outdoor')">
                <i class="fas fa-sun"></i>
                Outdoor
            </button>
            <button class="nav-btn" onclick="filterFacilitiesByType('training')">
                <i class="fas fa-dumbbell"></i>
                Training
            </button>
        </div>

        <!-- Facility Booking Content -->
        <div class="section-header">
            <div>
                <h2>Available Facilities</h2>
                <p>World-class facilities for all your cricket training and event needs</p>
            </div>
            <div class="section-filters">
                <select class="filter-select" id="facility-type-filter">
                    <option value="all">All Facilities</option>
                    <option value="indoor">Indoor Facilities</option>
                    <option value="outdoor">Outdoor Facilities</option>
                    <option value="training">Training Areas</option>
                    <option value="event">Event Spaces</option>
                </select>
                <select class="filter-select" id="facility-capacity-filter">
                    <option value="all">All Capacities</option>
                    <option value="small">1-10 People</option>
                    <option value="medium">11-25 People</option>
                    <option value="large">26+ People</option>
                </select>
            </div>
        </div>
        
        <div class="items-grid" id="facilities-grid">
            <?php
                $facilities = $data['facilities'] ?? [];
                $escape = function ($value) {
                    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                };

                $facilityType = function ($name, $location) {
                    $text = strtolower(((string)$name) . ' ' . ((string)$location));
                    if (strpos($text, 'conference') !== false || strpos($text, 'meeting') !== false) {
                        return 'event';
                    }
                    if (strpos($text, 'indoor') !== false || strpos($text, 'hall') !== false || strpos($text, 'building') !== false) {
                        return 'indoor';
                    }
                    if (strpos($text, 'ground') !== false || strpos($text, 'outdoor') !== false || strpos($text, 'field') !== false) {
                        return 'outdoor';
                    }
                    return 'training';
                };

                $capacityBucket = function ($capacity) {
                    $c = (int)$capacity;
                    if ($c <= 10) return 'small';
                    if ($c <= 25) return 'medium';
                    return 'large';
                };

                $placeholderUrl = function ($label) {
                    return 'https://via.placeholder.com/300x200?text=' . urlencode((string)$label);
                };
            ?>

            <?php if (!empty($facilities)) : ?>
                <?php foreach ($facilities as $facility) : ?>
                    <?php
                        $name = $facility->Name ?? 'Facility';
                        $location = $facility->Location ?? '';
                        $type = $facilityType($name, $location);
                        $bucket = $capacityBucket($facility->Capacity ?? 0);

                        $availability = strtolower((string)($facility->AvailabilityStatus ?? 'available'));
                        $canBook = ($availability === 'available');

                        $hourlyRate = (float)($facility->HourlyRate ?? 0);

                        $dbImage = trim((string)($facility->facilityImage ?? ''));
                        $image = $dbImage !== ''
                            ? (URLROOT . '/' . ltrim($dbImage, '/'))
                            : $placeholderUrl($name);

                        $imageFallback = $placeholderUrl($name);
                    ?>

                    <div class="card-item" data-type="<?php echo $escape($type); ?>" data-capacity="<?php echo $escape($bucket); ?>">
                        <div class="card-image">
                            <img src="<?php echo $escape($image); ?>" alt="<?php echo $escape($name); ?>" onerror="this.src='<?php echo $escape($imageFallback); ?>'" />
                        </div>
                        <div class="card-body">
                            <div class="facility-category"><?php echo $escape(ucfirst($type)); ?> Facility</div>
                            <h3 class="card-title"><?php echo $escape($name); ?></h3>
                            <?php if (!empty($location)) : ?>
                                <p class="card-description"><?php echo $escape($location); ?></p>
                            <?php endif; ?>

                            <div class="facility-details" style="margin: 1rem 0; font-size: 0.9rem; color: #666;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <i class="fas fa-users" style="color: #4A90E2; width: 16px;"></i>
                                    <span>Capacity: <?php echo (int)($facility->Capacity ?? 0); ?></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <i class="fas fa-info-circle" style="color: #4A90E2; width: 16px;"></i>
                                    <span>Status: <?php echo $escape($availability); ?></span>
                                </div>
                            </div>

                            <div class="price-section">
                                <span class="price-current">Rs. <?php echo number_format($hourlyRate, 2); ?>/hour</span>
                            </div>
                            <div class="product-stock">
                                <?php echo $canBook ? '✓ Available for booking' : 'Unavailable'; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-card book-facility" data-facility-id="<?php echo $escape($facility->FacilityID ?? ''); ?>" data-name="<?php echo $escape($name); ?>" data-hourly="<?php echo $escape($hourlyRate); ?>" data-capacity="<?php echo $escape($facility->Capacity ?? ''); ?>" <?php echo $canBook ? '' : 'disabled'; ?>>
                                    Book Facility
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="payment-card" style="grid-column: 1 / -1;">
                    <p>No facilities found in the database.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Booking Information -->
        <div class="info-section">
            <h3>Why Book With Us?</h3>
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Easy Online Booking</h4>
                        <p>Book facilities online with our simple booking system. Choose your date, time, and duration with instant confirmation.</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h4>Flexible Scheduling</h4>
                        <p>Our facilities are available from early morning to late evening. Book for as little as 1 hour or reserve for full days.</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="info-content">
                        <h4>Equipment Included</h4>
                        <p>Basic equipment and facilities maintenance are included in all bookings. Additional equipment available for rent.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>window.URLROOT_FACILITY = '<?php echo URLROOT; ?>';</script>
<script src="<?php echo URLROOT; ?>/js/player/facilities.js"></script>


<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>