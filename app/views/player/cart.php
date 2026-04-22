<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/cart.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">

<?php
$cartItems = $data['cartItems'] ?? [];
$cartSubtotal = 0.0;
foreach ($cartItems as $cartItem) {
    $cartSubtotal += ((float)($cartItem->Price ?? 0)) * ((int)($cartItem->Quantity ?? 0));
}
$cartShipping = $cartSubtotal > 100 ? 0.0 : ($cartSubtotal > 0 ? 15.0 : 0.0);
$cartTax = $cartSubtotal * 0.10;
$cartGrandTotal = $cartSubtotal + $cartShipping + $cartTax;
?>

<div class="player-layout">
    <?php $playerActivePage = 'shopping'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

    <div class="main-content" id="cartPage" data-urlroot="<?php echo URLROOT; ?>">
        <div class="cart-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
                    <p>Review your selected items and proceed to checkout</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Continue Shopping
                    </a>
                    <button class="btn btn-primary" type="submit" form="cartCheckoutForm" <?php echo empty($cartItems) ? 'disabled' : ''; ?> style="background:#28a745;border-color:#28a745;">
                        <i class="fas fa-shopping-bag"></i>
                        Checkout
                    </button>
                </div>
            </div>
        </div>

        <?php flash('shopping_message'); ?>

        <div class="cart-container">
            <div class="cart-items-section">
                <div class="cart-items-header">
                    <h3>Cart Items (<span id="total-items"><?php echo (int)($data['cartItemCount'] ?? 0); ?></span>)</h3>
                    <div class="cart-header-actions">
                        <?php if (!empty($cartItems)): ?>
                            <form method="POST" action="<?php echo URLROOT; ?>/player/clearCart">
                                <input type="hidden" name="return_to" value="cart">
                                <button class="btn btn-link" id="clear-cart-btn" type="submit">
                                    <i class="fas fa-trash"></i> Clear Cart
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($cartItems)): ?>
                    <div id="empty-cart" class="empty-cart-state">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Your cart is empty</h3>
                        <p>Looks like you haven't added any items to your cart yet.</p>
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i>
                            Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <form id="cartCheckoutForm" method="POST" action="<?php echo URLROOT; ?>/player/payhere_checkout">
                        <input type="hidden" name="nonce" value="<?php echo htmlspecialchars($_SESSION['payhere_nonce'] ?? ''); ?>">
                    </form>

                    <div class="cart-table-container">
                        <table class="cart-table" id="cart-items-table">
                            <thead>
                                <tr>
                                    <th class="col-select">Select</th>
                                    <th class="col-product">Product</th>
                                    <th class="col-price">Unit Price</th>
                                    <th class="col-quantity">Quantity</th>
                                    <th class="col-total">Total</th>
                                    <th class="col-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items-list">
                                <?php foreach ($cartItems as $item): ?>
                                    <?php
                                    $productId = (int)($item->ProductID ?? 0);
                                    $quantity = (int)($item->Quantity ?? 0);
                                    $unitPrice = (float)($item->Price ?? 0);
                                    $lineTotal = $unitPrice * $quantity;
                                    $productImage = !empty($item->ProductImage)
                                        ? URLROOT . '/' . ltrim((string)$item->ProductImage, '/')
                                        : 'https://via.placeholder.com/52x52?text=Item';
                                    ?>
                                    <tr class="cart-item-row">
                                        <td class="col-select">
                                            <input type="checkbox" name="selected_product_ids[]" value="<?php echo $productId; ?>" form="cartCheckoutForm" checked>
                                        </td>
                                        <td class="col-product">
                                            <div class="product-info">
                                                <div class="product-image">
                                                    <img src="<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars((string)($item->Name ?? 'Item')); ?>" />
                                                </div>
                                                <div class="product-details">
                                                    <h4 class="product-name"><?php echo htmlspecialchars((string)($item->Name ?? 'Item')); ?></h4>
                                                    <p class="product-sku">SKU: <?php echo htmlspecialchars((string)($item->SKU ?? 'N/A')); ?></p>
                                                    <p class="product-category"><?php echo htmlspecialchars((string)($item->Category ?? 'General')); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-price">
                                            <span class="price-value">LKR <?php echo number_format($unitPrice, 2); ?></span>
                                        </td>
                                        <td class="col-quantity">
                                            <form method="POST" action="<?php echo URLROOT; ?>/player/updateCartItem" class="quantity-controls">
                                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                                <input type="hidden" name="return_to" value="cart">
                                                <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" max="10">
                                                <button class="btn btn-secondary btn-sm" type="submit">Update</button>
                                            </form>
                                        </td>
                                        <td class="col-total">
                                            <span class="total-value">LKR <?php echo number_format($lineTotal, 2); ?></span>
                                        </td>
                                        <td class="col-actions">
                                            <div class="action-buttons">
                                                <form method="POST" action="<?php echo URLROOT; ?>/player/payhere_checkout">
                                                    <input type="hidden" name="selected_product_ids[]" value="<?php echo $productId; ?>">
                                                    <input type="hidden" name="nonce" value="<?php echo htmlspecialchars($_SESSION['payhere_nonce'] ?? ''); ?>">
                                                    <button class="btn btn-primary btn-sm item-checkout-btn" type="submit">
                                                        <i class="fas fa-credit-card"></i>
                                                        Checkout
                                                    </button>
                                                </form>
                                                <form method="POST" action="<?php echo URLROOT; ?>/player/removeCartItem">
                                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                                    <input type="hidden" name="return_to" value="cart">
                                                    <button class="btn btn-link btn-sm" type="submit" title="Remove Item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cart-summary-section">
                <div class="cart-summary-card">
                    <h3>Order Summary</h3>

                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">LKR <?php echo number_format($cartSubtotal, 2); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="cart-shipping"><?php echo $cartShipping == 0.0 ? 'FREE' : 'LKR ' . number_format($cartShipping, 2); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span id="cart-tax">LKR <?php echo number_format($cartTax, 2); ?></span>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-row total-row">
                        <span><strong>Total:</strong></span>
                        <span id="cart-total"><strong>LKR <?php echo number_format($cartGrandTotal, 2); ?></strong></span>
                    </div>

                    <div class="payhere-section">
                        <h4>Payment</h4>
                        <div class="payhere-logo-row">
                            <i class="fas fa-shield-alt"></i>
                            <span>PayHere Secure Checkout</span>
                        </div>
                        <p class="payhere-note">Selected items will be sent to PayHere for secure payment.</p>
                    </div>

                    <button class="btn btn-primary btn-large checkout-button" type="submit" form="cartCheckoutForm" <?php echo empty($cartItems) ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-bag"></i>
                        Checkout
                    </button>

                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <span>Your payment information is secure and encrypted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
