<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/rentals.css">

<div class="player-layout" id="rentalCartPage" data-urlroot="<?php echo URLROOT; ?>" data-cart-count="<?php echo (int)($data['rentalCartItemCount'] ?? 0); ?>">
    <?php $playerActivePage = 'shopping'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-shopping-cart"></i> Rental Cart</h1>
                    <p>Review your rental items and proceed to checkout</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/rentals" class="btn btn-rentals">
                        <i class="fas fa-tools"></i>
                        Equipment Rentals
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="btn btn-facilities">
                        <i class="fas fa-shopping-bag"></i>
                        Back to Shop
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/rental_cart" class="btn btn-cart" aria-current="page">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                        <span class="cart-count"><?php echo (int)($data['rentalCartItemCount'] ?? 0); ?></span>
                    </a>
                </div>
            </div>
        </div>

        <?php flash('rental_message'); ?>

        <?php
            $escape = function ($value) {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            };

            $rentalCartItems = $data['rentalCartItems'] ?? [];
        ?>

        <div class="info-section rental-cart-section">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <h3 style="margin:0; text-align:left;">Rental Cart</h3>
                <?php if (!empty($rentalCartItems)) : ?>
                    <form method="POST" action="<?php echo URLROOT; ?>/player/clear_rental_cart" style="margin:0;">
                        <input type="hidden" name="return_to" value="rental_cart">
                        <button type="submit" class="btn btn-facilities" style="padding: 0.45rem 0.9rem;">
                            Clear Cart
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (!empty($rentalCartItems)) : ?>
                <div class="rental-history-table-wrap" style="margin-top: 1rem;">
                    <table class="rental-history-table">
                        <thead>
                            <tr>
                                <th>Equipment</th>
                                <th>Rate</th>
                                <th>Added</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rentalCartItems as $item) : ?>
                                <tr>
                                    <td><?php echo $escape($item->EquipmentName ?? 'Equipment'); ?></td>
                                    <td>LKR <?php echo number_format((float)($item->RentalPrice ?? 0), 2); ?>/day</td>
                                    <td><?php echo !empty($item->AddedDate) ? date('M j, Y', strtotime($item->AddedDate)) : '-'; ?></td>
                                    <td>
                                        <form method="POST" action="<?php echo URLROOT; ?>/player/remove_rental_cart_item" style="display:inline; margin:0;">
                                            <input type="hidden" name="return_to" value="rental_cart">
                                            <input type="hidden" name="cart_id" value="<?php echo (int)($item->CartID ?? 0); ?>">
                                            <button type="submit" class="btn btn-cart" style="padding: 0.45rem 0.9rem;">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="<?php echo URLROOT; ?>/player/checkout_rental_cart" style="margin-top: 1rem;">
                    <input type="hidden" name="return_to" value="rental_cart">

                    <div class="form-group">
                        <label for="rental-cart-start-date">Start Date:</label>
                        <input type="date" id="rental-cart-start-date" name="start_date" required>
                    </div>

                    <div class="form-group">
                        <label for="rental-cart-duration">Rental Duration:</label>
                        <select id="rental-cart-duration" name="duration" required>
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
                        <label for="rental-cart-pickup">Pickup Method:</label>
                        <select id="rental-cart-pickup" name="pickup_method" required>
                            <option value="pickup">Pickup from Academy</option>
                            <option value="delivery">Home Delivery (+Rs. 250)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 0.85rem;">
                        <label style="display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" name="agree_terms" value="1" required>
                            <span>I agree to the rental terms (late fees and damage policy).</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-cart" style="padding: 0.6rem 1.1rem;">
                        Checkout Cart
                    </button>
                </form>
            <?php else : ?>
                <div class="rental-empty-state" style="margin-top: 1rem;">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your rental cart is empty.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/player/rental_cart.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
