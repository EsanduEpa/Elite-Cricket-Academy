<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/cart.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">

<div class="player-layout">
    <?php $playerActivePage = 'shopping'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content" id="cartPage" data-urlroot="<?php echo URLROOT; ?>" data-cart-count="<?php echo (int)($data['cartItemCount'] ?? 0); ?>">
        <!-- Cart Header -->
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
                    <button class="btn btn-primary" id="checkout-btn" disabled style="background:#28a745;border-color:#28a745;">
                        <i class="fas fa-shopping-bag"></i>
                        Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- Cart Content -->
        <div class="cart-container">
            <!-- Cart Items Section -->
            <div class="cart-items-section">
                <div class="cart-items-header">
                    <h3>Cart Items (<span id="total-items">0</span>)</h3>
                    <div class="cart-header-actions">
                        <label class="select-all-label">
                            <input type="checkbox" id="select-all-checkbox" checked onchange="toggleSelectAll()">
                            <span>Select All</span>
                        </label>
                        <button class="btn btn-link" id="clear-cart-btn" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>

                <!-- Empty Cart State -->
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

                <!-- Cart Items Table -->
                <div class="cart-table-container">
                    <table class="cart-table" id="cart-items-table">
                        <thead>
                            <tr>
                                <th class="col-select">
                                    <input type="checkbox" id="select-all-table" checked onchange="toggleSelectAll()">
                                </th>
                                <th class="col-product">Product</th>
                                <th class="col-price">Unit Price</th>
                                <th class="col-quantity">Quantity</th>
                                <th class="col-total">Total</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items-list">
                            <!-- Cart items will be populated here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cart Summary Section -->
            <div class="cart-summary-section">
                <div class="cart-summary-card">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">LKR 0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="cart-shipping">LKR 0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span id="cart-tax">LKR 0.00</span>
                    </div>
                    
                    <div class="summary-row discount-row" id="discount-row" style="display: none;">
                        <span>Discount:</span>
                        <span id="cart-discount">-LKR 0.00</span>
                    </div>
                    
                    <hr class="summary-divider">
                    
                    <div class="summary-row total-row">
                        <span><strong>Total:</strong></span>
                        <span id="cart-total"><strong>LKR 0.00</strong></span>
                    </div>

                    <!-- Promo Code Section -->
                    <div class="promo-code-section">
                        <h4>Promo Code</h4>
                        <div class="promo-input-group">
                            <input type="text" id="promo-code" placeholder="Enter promo code" maxlength="20">
                            <button class="btn btn-secondary" onclick="applyPromoCode()">Apply</button>
                        </div>
                        <div id="promo-message" class="promo-message"></div>
                    </div>

                    <!-- PayHere Payment -->
                    <div class="payhere-section">
                        <h4>Payment</h4>
                        <div class="payhere-logo-row">
                            <i class="fas fa-shield-alt"></i>
                            <span>PayHere Secure Checkout</span>
                        </div>
                        <p class="payhere-note">You will be redirected to PayHere to complete your payment securely.</p>
                    </div>

                    <!-- Hidden nonce for CSRF protection -->
                    <input type="hidden" id="payhere_nonce" value="<?php echo htmlspecialchars($_SESSION['payhere_nonce'] ?? ''); ?>">

                    <!-- Checkout Button -->
                    <button class="btn btn-primary btn-large checkout-button" id="main-checkout-btn" onclick="proceedToCheckout()">
                        <i class="fas fa-shopping-bag"></i>
                        Checkout
                    </button>

                    <!-- Security Info -->
                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <span>Your payment information is secure and encrypted</span>
                    </div>
                </div>

                <!-- Recently Viewed -->
                <div class="recently-viewed-section">
                    <h4>Recently Viewed</h4>
                    <div class="recently-viewed-items" id="recently-viewed">
                        <!-- Recently viewed items will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Products -->
        <div class="recommended-section">
            <h3>You might also like</h3>
            <div class="recommended-products" id="recommended-products">
                <!-- Recommended products will be populated here -->
            </div>
        </div>
    </div>
</div>

<!-- Checkout Confirmation Modal -->
<div id="checkoutModal" class="modal app-modal">
    <div class="modal-content modal-lg app-modal__dialog app-modal__dialog--wide">
        <div class="modal-header gradient-header app-modal__header">
            <div class="header-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="header-text">
                <h3>Checkout Confirmation</h3>
                <p>Review your order details before completing purchase</p>
            </div>
            <button type="button" class="modal-close app-modal__close" data-cart-close="checkoutModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body app-modal__body">
            <div class="checkout-summary">
                <h4>Order Details</h4>
                <div id="checkout-items-list" class="checkout-items">
                    <!-- Items will be populated here -->
                </div>
                
                <div class="checkout-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="checkout-subtotal">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span id="checkout-shipping">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Tax:</span>
                        <span id="checkout-tax">$0.00</span>
                    </div>
                    <div class="total-row final-total">
                        <span><strong>Total:</strong></span>
                        <span id="checkout-total"><strong>$0.00</strong></span>
                    </div>
                </div>

                <div class="delivery-info">
                    <h4>Delivery Information</h4>
                    <div class="delivery-options">
                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="standard" checked>
                            <div class="delivery-details">
                                <strong>Standard Delivery (Free)</strong>
                                <span>3-5 business days</span>
                            </div>
                        </label>
                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="express">
                            <div class="delivery-details">
                                <strong>Express Delivery (+$15)</strong>
                                <span>1-2 business days</span>
                            </div>
                        </label>
                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="pickup">
                            <div class="delivery-details">
                                <strong>Academy Pickup (Free)</strong>
                                <span>Available next business day</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer app-modal__footer">
            <button type="button" class="btn btn-secondary" data-cart-close="checkoutModal">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </button>
            <button type="button" class="btn btn-primary" data-cart-action="complete-order">
                <i class="fas fa-check"></i> Complete Order
            </button>
        </div>
    </div>
</div>

<!-- Order Success Modal -->
<div id="orderSuccessModal" class="modal app-modal">
    <div class="modal-content app-modal__dialog app-modal__dialog--compact">
        <div class="modal-header success-header app-modal__header app-modal__header--success">
            <div class="header-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="header-text">
                <h3>Order Placed Successfully!</h3>
                <p>Your order has been confirmed and is being processed</p>
            </div>
        </div>
        
        <div class="modal-body app-modal__body">
            <div class="success-content">
                <div class="order-number">
                    <strong>Order #<span id="order-number">ORD-2024-001</span></strong>
                </div>
                <p>Thank you for your purchase! You will receive an email confirmation shortly.</p>
                
                <div class="next-steps">
                    <h4>What's Next?</h4>
                    <ul>
                        <li>📧 Email confirmation sent to your registered email</li>
                        <li>📦 Order processing begins within 24 hours</li>
                        <li>🚚 Tracking information will be provided</li>
                        <li>📞 Our team will contact you if needed</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="modal-footer app-modal__footer">
            <button type="button" class="btn btn-secondary" data-cart-close="orderSuccessModal">
                <i class="fas fa-home"></i> Back to Dashboard
            </button>
            <button type="button" class="btn btn-primary" data-cart-action="view-order-history">
                <i class="fas fa-history"></i> View Orders
            </button>
        </div>
    </div>
</div>

<!-- Pass cart configuration data from controller to JS (data-only, no behavior) -->
<script type="application/json" id="cartData"><?php echo json_encode([
    'promoDiscounts' => $data['promoDiscounts'] ?? null,
    'recommendedProducts' => $data['recommendedProducts'] ?? null,
    'cartItems' => $data['cartItems'] ?? [],
    'cartItemCount' => (int)($data['cartItemCount'] ?? 0),
    'cartTotal' => (float)($data['cartTotal'] ?? 0),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?></script>

<script src="<?php echo URLROOT; ?>/js/player/cart.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>