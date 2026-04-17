<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/checkout.css">
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
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Player'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/player/profile" class="profile-avatar" aria-label="Open player profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="checkoutPage" data-urlroot="<?php echo URLROOT; ?>" data-cart-count="<?php echo (int)($data['cartItemCount'] ?? 0); ?>">
        <!-- Payment Header -->
        <div class="shopping-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-lock"></i> Secure Checkout</h1>
                    <p>Complete your purchase securely with our encrypted payment system</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/cart" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Cart
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Content -->
        <div class="payment-container">
            <!-- Order Summary -->
            <div class="payment-summary">
                <div class="payment-card">
                    <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                    <div id="checkout-items-list">
                        <!-- Items will be loaded here -->
                    </div>
                    <hr>
                    <div class="payment-total">
                        <span><strong>Total Amount:</strong></span>
                        <span id="payment-total"><strong>₹0.00</strong></span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods">
                <div class="payment-card">
                    <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                    
                    <!-- Payment Options -->
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="card" checked>
                            <div class="payment-details">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-info">
                                    <strong>Credit/Debit Card</strong>
                                    <span>Visa, MasterCard, RuPay</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="upi">
                            <div class="payment-details">
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="payment-info">
                                    <strong>UPI Payment</strong>
                                    <span>PhonePe, Google Pay, Paytm</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="netbanking">
                            <div class="payment-details">
                                <div class="payment-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-info">
                                    <strong>Net Banking</strong>
                                    <span>All major banks supported</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="wallet">
                            <div class="payment-details">
                                <div class="payment-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="payment-info">
                                    <strong>Digital Wallet</strong>
                                    <span>Academy Wallet Balance: ₹1,250.00</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Payment Form -->
                    <div class="payment-form" id="card-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Card Number</label>
                                <input type="text" placeholder="1234 5678 9012 3456" maxlength="19" id="card-number">
                                <div class="field-error" id="error-card-number"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM/YY" maxlength="5" id="expiry-date">
                                <div class="field-error" id="error-expiry"></div>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" placeholder="123" maxlength="4" id="cvv">
                                <div class="field-error" id="error-cvv"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Cardholder Name</label>
                                <input type="text" placeholder="Enter name as on card" id="cardholder-name">
                                <div class="field-error" id="error-cardholder"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <span>Your payment is secured with 256-bit SSL encryption</span>
                    </div>

                    <!-- Payment Buttons -->
                    <div class="payment-actions">
                        <button class="btn btn-primary btn-large" id="complete-payment-btn" onclick="processPayment()">
                            <i class="fas fa-lock"></i>
                            Complete Payment
                        </button>
                        <p class="payment-note">
                            <i class="fas fa-info-circle"></i>
                            You will be charged <span id="final-amount">₹0.00</span> for this order
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Success Modal -->
<div id="paymentSuccessModal" class="modal app-modal" aria-hidden="true">
    <div class="modal-content app-modal__dialog app-modal__dialog--compact">
        <div class="modal-header success-header app-modal__header app-modal__header--success">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Payment Successful!</h3>
        </div>
        <div class="modal-body app-modal__body">
            <div class="success-message">
                <h4>Thank you for your purchase!</h4>
                <p>Your order has been confirmed and will be processed shortly.</p>
                <div class="order-details">
                    <div class="detail-row">
                        <span>Order ID:</span>
                        <span id="order-id">ECA-2024-001</span>
                    </div>
                    <div class="detail-row">
                        <span>Amount Paid:</span>
                        <span id="paid-amount">₹0.00</span>
                    </div>
                    <div class="detail-row">
                        <span>Payment Method:</span>
                        <span id="payment-method-used">Credit Card</span>
                    </div>
                </div>
                <p class="delivery-info">
                    <i class="fas fa-truck"></i>
                    Expected delivery within 3-5 business days
                </p>
            </div>
        </div>
        <div class="modal-footer app-modal__footer">
            <button type="button" class="btn btn-primary" data-checkout-action="go-to-orders">
                <i class="fas fa-list"></i>
                View Orders
            </button>
            <button type="button" class="btn btn-secondary" data-checkout-action="continue-shopping">
                <i class="fas fa-shopping-bag"></i>
                Continue Shopping
            </button>
        </div>
    </div>
</div>
<script type="application/json" id="checkoutData"><?php echo json_encode([
    'cartItems' => $data['cartItems'] ?? [],
    'cartTotal' => (float)($data['cartTotal'] ?? 0),
    'cartItemCount' => (int)($data['cartItemCount'] ?? 0),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?></script>
<script src="<?php echo URLROOT; ?>/js/player/checkout.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>