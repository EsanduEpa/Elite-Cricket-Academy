<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/rentals.css">
    
<div class="player-layout" id="rentalsPage" data-urlroot="<?php echo URLROOT; ?>" data-cart-count="<?php echo (int)($data['rentalCartItemCount'] ?? 0); ?>">
    <?php $playerActivePage = 'shopping'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Equipment Rentals Header -->
        <div class="page-header">
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

                    <a href="<?php echo URLROOT; ?>/player/rental_cart" class="btn btn-cart" id="rental-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                        <span class="cart-count" id="rental-cart-count"><?php echo (int)($data['rentalCartItemCount'] ?? 0); ?></span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="btn btn-cart">
                        <i class="fas fa-calendar-alt"></i>
                        My Bookings
                    </a>
                </div>
            </div>
        </div>

        <?php flash('rental_message'); ?>

        <div class="rental-toolbar">
            <div class="rental-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" id="rental-search" placeholder="Search equipment by name or description">
            </div>

            <select id="rental-status-filter" class="filter-select" aria-label="Filter by availability status">
                <option value="all">All Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>

        <!-- Rental Navigation -->
        <div class="page-navigation">
            <button class="nav-btn rental-nav-btn active" data-category="all" type="button">
                <i class="fas fa-th-large"></i>
                All Equipment
            </button>
            <button class="nav-btn rental-nav-btn" data-category="batting" type="button">
                <i class="fas fa-baseball-ball"></i>
                Batting
            </button>
            <button class="nav-btn rental-nav-btn" data-category="bowling" type="button">
                <i class="fas fa-bullseye"></i>
                Bowling
            </button>
            <button class="nav-btn rental-nav-btn" data-category="training" type="button">
                <i class="fas fa-dumbbell"></i>
                Training
            </button>
            <button class="nav-btn rental-nav-btn" data-category="protective" type="button">
                <i class="fas fa-shield-alt"></i>
                Protective
            </button>
            <button class="nav-btn rental-nav-btn" data-category="other" type="button">
                <i class="fas fa-ellipsis-h"></i>
                Other
            </button>
        </div>

        <?php
            $escape = function ($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            };
        ?>

        <div class="items-grid" id="rentals-grid">
            <?php
                $rentals = $data['rentals'] ?? [];
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

                    <div class="card-item product-card" data-category="<?php echo $escape($categoryFilterKey); ?>" data-condition="<?php echo $escape($conditionLabel); ?>" data-status="<?php echo $canRent ? 'available' : 'unavailable'; ?>">
                        <!--<div class="condition-badge condition-<?php echo $escape($conditionClass); ?>"><?php echo $escape($conditionLabel); ?></div> -->
                        <div class="card-image">
                            <img src="<?php echo $escape($image); ?>" alt="<?php echo $escape($name); ?>" data-fallback-src="<?php echo $escape($imageFallback); ?>" />
                        </div>
                        <div class="card-body">
                            <div class="equipment-category"><?php echo $escape($category); ?></div>
                            <h3 class="card-title"><?php echo $escape($name); ?></h3>
                            <?php if (!empty($equipment->Description)) : ?>
                                <p class="card-description"><?php echo $escape($equipment->Description); ?></p>
                            <?php endif; ?>
                            <div class="price-section">
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
                                <button class="btn btn-card rent-equipment" data-equipment-id="<?php echo $escape($equipment->EquipmentID ?? ''); ?>" data-name="<?php echo $escape($name); ?>" data-condition="<?php echo $escape($conditionLabel); ?>" data-rate="<?php echo $escape($dailyRate); ?>" data-stock="<?php echo (int)$stock; ?>" <?php echo $canRent ? '' : 'disabled'; ?>>
                                    Rent Now
                                </button>

                                <form class="js-add-to-rental-cart-form" method="POST" action="<?php echo URLROOT; ?>/player/add_rental_cart_item" style="margin:0;" data-name="<?php echo $escape($name); ?>">
                                    <input type="hidden" name="return_to" value="rentals">
                                    <input type="hidden" name="equipment_id" value="<?php echo (int)($equipment->EquipmentID ?? 0); ?>">
                                    <button type="submit" class="btn btn-cart" title="Add to Cart" <?php echo $canRent ? '' : 'disabled'; ?>>
                                        <i class="fas fa-cart-plus" aria-hidden="true"></i>
                                        Add
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="payment-card payment-card-fullspan">
                    <p>No equipment found in the database.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-section my-rentals-section">
            <h3>My Rentals</h3>
            <?php $myRentals = $data['myRentals'] ?? []; ?>
            <?php if (!empty($myRentals)) : ?>
                <div class="rental-history-table-wrap">
                    <table class="rental-history-table">
                        <thead>
                            <tr>
                                <th>Rental ID</th>
                                <th>Equipment</th>
                                <th>Start</th>
                                <th>Return By</th>
                                <th>Status</th>
                                <th>Return Fee</th>
                                <th>Payment</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myRentals as $rental) : ?>
                                <?php
                                    $statusRaw = strtolower((string)($rental->Status ?? 'active'));
                                    $endTimeRaw = (string)($rental->EndTime ?? '');
                                    $isLate = false;
                                    if ($endTimeRaw !== '' && in_array($statusRaw, ['active','overdue'], true)) {
                                        try {
                                            $isLate = (new DateTime($endTimeRaw)) < new DateTime('today');
                                        } catch (Exception $e) {
                                            $isLate = false;
                                        }
                                    }
                                    $statusForBadge = $statusRaw;
                                    if ($statusForBadge === 'active' && $isLate) {
                                        $statusForBadge = 'overdue';
                                    }

                                    $returnPay = (float)($rental->ReturnTotalPay ?? 0);
                                    $returnPaymentStatus = strtolower((string)($rental->ReturnPaymentStatus ?? ''));
                                    $hasPendingReturnFee = ($returnPay > 0 && $returnPaymentStatus === 'pending' && !empty($rental->ReturnID));
                                ?>
                                <tr>
                                    <td>#<?php echo (int)($rental->RentalID ?? 0); ?></td>
                                    <td><?php echo $escape($rental->EquipmentName ?? 'Equipment'); ?></td>
                                    <td><?php echo !empty($rental->StartTime) ? date('M j, Y', strtotime($rental->StartTime)) : '-'; ?></td>
                                    <td><?php echo !empty($rental->EndTime) ? date('M j, Y', strtotime($rental->EndTime)) : '-'; ?></td>
                                    <td>
                                        <span class="rental-status rental-status-<?php echo $escape($statusForBadge); ?>">
                                            <?php echo $escape(ucfirst($statusForBadge)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($rental->ReturnID)) : ?>
                                            LKR <?php echo number_format($returnPay, 2); ?>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($hasPendingReturnFee) : ?>
                                            <form method="POST" action="<?php echo URLROOT; ?>/player/pay_return_fee" style="display:inline;">
                                                <input type="hidden" name="return_id" value="<?php echo (int)$rental->ReturnID; ?>">
                                                <button type="submit" class="btn btn-cart" style="padding: 0.45rem 0.9rem;">
                                                    Pay Now
                                                </button>
                                            </form>
                                        <?php elseif (!empty($rental->ReturnID) && $returnPaymentStatus !== '') : ?>
                                            <?php echo $escape(ucfirst(str_replace('_', ' ', $returnPaymentStatus))); ?>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>LKR <?php echo number_format((float)($rental->TotalCost ?? 0), 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="rental-empty-state">
                    <i class="fas fa-tools"></i>
                    <p>You do not have any equipment rentals yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Rental Modal (kept in view so form fields exist in markup) -->
<div id="rentalModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="transition: all 0.2s ease;">
        <div class="modal-header">
            <h3>Equipment Rental</h3>
            <button class="modal-close-btn" type="button">&times;</button>
        </div>
        <div class="modal-body">
            <div id="rental-details"></div>

            <form id="rental-cart-form" method="POST" action="<?php echo URLROOT; ?>/player/add_rental_cart_item">
                <input type="hidden" name="return_to" value="rentals">
                <input type="hidden" id="rental-cart-equipment-id" name="equipment_id" value="">
            </form>

            <form id="rental-form" method="POST" action="<?php echo URLROOT; ?>/player/confirm_rental">
                <input type="hidden" id="rental-equipment-id" name="equipment_id" value="">

                <div class="form-group">
                    <label for="rental-start-date">Start Date:</label>
                    <input type="date" id="rental-start-date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="rental-duration">Rental Duration:</label>
                    <select id="rental-duration" name="duration" required>
                        <option value="1">1 Day</option>
                        <option value="2">2 Days</option>
                        <option value="3">3 Days</option>
                        <option value="4">4 Days</option>
                        <option value="5">5 Days</option>
                        <option value="6">6 Days</option>
                        <option value="7">7 Days</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rental-quantity">Quantity:</label>
                    <select id="rental-quantity" name="quantity" required>
                        <option value="1">1</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rental-pickup">Pickup Method:</label>
                    <select id="rental-pickup" name="pickup_method" required>
                        <option value="pickup">Pickup from Academy</option>
                        <option value="delivery">Home Delivery (+Rs. 250)</option>
                    </select>
                </div>

                <div class="total-display">
                    <strong>Total: Rs. <span id="rental-total-amount">0.00</span></strong>
                </div>

                <div class="rental-criteria" role="note" aria-label="Rental rules">
                    <div class="rental-criteria-title">Important</div>
                    <ul>
                        <li>Rental duration is limited to a maximum of 7 days.</li>
                        <li>Late fees apply if returned after the due date (Day 1: Rs. 750, Day 2: Rs. 1,000, Day 3: Rs. 1,500; increases further with each late day).</li>
                        <li>Damage policy: Slight damage → no fee; Moderate damage → equipment value price × 0.5; High damage → equipment value price × 0.8.</li>
                    </ul>
                </div>

                <div class="form-group" style="margin-top: 0.85rem;">
                    <label style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="agree_terms" value="1" required>
                        <span>I agree to the rental terms (late fees and damage policy).</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-actions">
            <button class="btn-modal secondary js-rental-cancel" type="button">Cancel</button>
            <button class="btn-modal secondary" type="submit" form="rental-cart-form">Add to Cart</button>
            <button class="btn-modal primary js-rental-confirm" type="button">Confirm Rental</button>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/player/rentals.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
