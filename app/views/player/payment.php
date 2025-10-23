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
        <!-- Payment Header -->
        <div class="shopping-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-lock"></i> Secure Payment Portal</h1>
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
                                <div class="field-error" id="error-card-number" style="display:none;color:#d9534f;margin-top:6px;font-size:0.9rem"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM/YY" maxlength="5" id="expiry-date">
                                <div class="field-error" id="error-expiry" style="display:none;color:#d9534f;margin-top:6px;font-size:0.9rem"></div>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" placeholder="123" maxlength="4" id="cvv">
                                <div class="field-error" id="error-cvv" style="display:none;color:#d9534f;margin-top:6px;font-size:0.9rem"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Cardholder Name</label>
                                <input type="text" placeholder="Enter name as on card" id="cardholder-name">
                                <div class="field-error" id="error-cardholder" style="display:none;color:#d9534f;margin-top:6px;font-size:0.9rem"></div>
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
<div id="paymentSuccessModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Payment Successful!</h3>
        </div>
        <div class="modal-body">
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
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="goToOrders()">
                <i class="fas fa-list"></i>
                View Orders
            </button>
            <button class="btn btn-secondary" onclick="continueShopping()">
                <i class="fas fa-shopping-bag"></i>
                Continue Shopping
            </button>
        </div>
    </div>
</div>

<style>
.payment-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    margin-top: 2rem;
    padding: 0 2rem;
}

.payment-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 1.5rem;
}

.payment-card h3 {
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.payment-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.payment-option {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid #eee;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option:hover {
    border-color: #4a90e2;
    background: rgba(74, 144, 226, 0.05);
}

.payment-option input[type="radio"] {
    margin-right: 1rem;
    accent-color: #4a90e2;
}

.payment-option input[type="radio"]:checked + .payment-details {
    color: #4a90e2;
}

.payment-details {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.payment-icon {
    font-size: 1.5rem;
    color: #666;
    width: 2rem;
    text-align: center;
}

.payment-info strong {
    display: block;
    margin-bottom: 0.25rem;
}

.payment-info span {
    color: #666;
    font-size: 0.9rem;
}

.payment-form {
    margin: 1.5rem 0;
}

.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #eee;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.payment-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    padding: 1rem 0;
    border-top: 2px solid #eee;
    margin-top: 1rem;
}

.payment-actions {
    text-align: center;
    margin-top: 2rem;
}

.payment-note {
    margin-top: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.payment-note i {
    color: #4a90e2;
    margin-right: 0.5rem;
}

.success-header {
    text-align: center;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-radius: 15px 15px 0 0;
    margin: -2rem -2rem 1.5rem -2rem;
    padding: 2rem;
}

.success-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.success-message {
    text-align: center;
}

.order-details {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin: 1.5rem 0;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.delivery-info {
    color: #28a745;
    font-weight: 600;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .payment-container {
        grid-template-columns: 1fr;
        padding: 0 1rem;
    }
    
    .form-row {
        flex-direction: column;
    }
}
</style>

<script>
// Load checkout data from localStorage
let checkoutData = JSON.parse(localStorage.getItem('checkoutData')) || null;

// Initialize payment page
document.addEventListener('DOMContentLoaded', function() {
    if (!checkoutData) {
        // Redirect back to cart if no checkout data
        window.location.href = '<?php echo URLROOT; ?>/player/cart';
        return;
    }
    
    loadCheckoutItems();
    setupPaymentForm();
});

// Load checkout items
function loadCheckoutItems() {
    const itemsList = document.getElementById('checkout-items-list');
    const totalSpan = document.getElementById('payment-total');
    const finalAmountSpan = document.getElementById('final-amount');
    
    if (!checkoutData || !checkoutData.items) {
        return;
    }
    
    itemsList.innerHTML = '';
    
    checkoutData.items.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'checkout-item';
        itemElement.innerHTML = `
            <div class="checkout-item-details">
                <h4>${item.Name || item.name}</h4>
                <p>Quantity: ${item.quantity}</p>
                <p>Price: ₹${(item.Price || item.price).toFixed(2)} each</p>
            </div>
            <div class="checkout-item-total">
                ₹${((item.Price || item.price) * item.quantity).toFixed(2)}
            </div>
        `;
        itemsList.appendChild(itemElement);
    });
    
    const total = checkoutData.total.toFixed(2);
    totalSpan.innerHTML = `<strong>₹${total}</strong>`;
    finalAmountSpan.textContent = `₹${total}`;
}

// Setup payment form
function setupPaymentForm() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardForm = document.getElementById('card-form');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'card') {
                cardForm.style.display = 'block';
            } else {
                cardForm.style.display = 'none';
            }
        });
    });
    
    // Format card number input
    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\s/g, '').replace(/[^0-9]/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            this.value = formattedValue;
        });
    }
    
    // Format expiry date input
    const expiryInput = document.getElementById('expiry-date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0,2) + '/' + value.substring(2,4);
            }
            this.value = value;
        });
    }
}

// Helper: show field error
function showFieldError(id, message) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = message;
    el.style.display = message ? 'block' : 'none';
}

// Validate card number (Luhn-lite: length and digits only)
function validateCardNumber(value) {
    const digits = value.replace(/\s/g, '');
    if (!/^[0-9]{13,19}$/.test(digits)) return false;
    return true;
}

// Validate expiry MM/YY and not in the past
function validateExpiry(value) {
    if (!/^(0[1-9]|1[0-2])\/(\d{2})$/.test(value)) return false;
    const parts = value.split('/');
    const month = parseInt(parts[0], 10);
    const year = 2000 + parseInt(parts[1], 10);
    const now = new Date();
    const expiry = new Date(year, month - 1, 1);
    // set to last day of month
    expiry.setMonth(expiry.getMonth() + 1);
    expiry.setDate(0);
    return expiry >= new Date(now.getFullYear(), now.getMonth(), 1);
}

function validateCVV(value) {
    return /^[0-9]{3,4}$/.test(value);
}

function validateCardholder(name) {
    return typeof name === 'string' && name.trim().length >= 2;
}

// Process payment (with validation)
function processPayment() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

    // If payment method is card, validate card form
    if (selectedMethod === 'card') {
        const cardNumber = document.getElementById('card-number').value.trim();
        const expiry = document.getElementById('expiry-date').value.trim();
        const cvv = document.getElementById('cvv').value.trim();
        const cardholder = document.getElementById('cardholder-name').value.trim();

        let valid = true;
        // Reset errors
        showFieldError('error-card-number', '');
        showFieldError('error-expiry', '');
        showFieldError('error-cvv', '');
        showFieldError('error-cardholder', '');

        if (!validateCardNumber(cardNumber)) {
            showFieldError('error-card-number', 'Please enter a valid card number (13-19 digits).');
            valid = false;
        }
        if (!validateExpiry(expiry)) {
            showFieldError('error-expiry', 'Invalid expiry date or card has expired. Use MM/YY.');
            valid = false;
        }
        if (!validateCVV(cvv)) {
            showFieldError('error-cvv', 'Please enter a valid 3 or 4 digit CVV.');
            valid = false;
        }
        if (!validateCardholder(cardholder)) {
            showFieldError('error-cardholder', 'Please enter the name on the card.');
            valid = false;
        }

        if (!valid) {
            // focus first error
            const firstError = document.querySelector('.field-error[style*="display: block"]');
            if (firstError) firstError.previousElementSibling?.focus();
            return;
        }
    }

    // Show loading state
    const paymentBtn = document.getElementById('complete-payment-btn');
    const originalText = paymentBtn.innerHTML;
    paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    paymentBtn.disabled = true;

    // Simulate payment processing
    setTimeout(() => {
        // Generate order ID
        const orderId = 'ECA-' + new Date().getFullYear() + '-' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');

        // Update success modal
        document.getElementById('order-id').textContent = orderId;
        document.getElementById('paid-amount').textContent = `₹${checkoutData.total.toFixed(2)}`;
        document.getElementById('payment-method-used').textContent = getPaymentMethodName(selectedMethod);

        // Clear cart items that were purchased
        if (checkoutData.type === 'multiple') {
            // Remove selected items from cart
            let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
            checkoutData.items.forEach(checkoutItem => {
                cart = cart.filter(cartItem => cartItem.ProductID !== checkoutItem.ProductID);
            });
            localStorage.setItem('shoppingCart', JSON.stringify(cart));
        } else {
            // Remove single item from cart
            let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
            cart = cart.filter(cartItem => cartItem.ProductID !== checkoutData.items[0].ProductID);
            localStorage.setItem('shoppingCart', JSON.stringify(cart));
        }

        // Clear checkout data
        localStorage.removeItem('checkoutData');

        // Show success modal
        document.getElementById('paymentSuccessModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Reset button
        paymentBtn.innerHTML = originalText;
        paymentBtn.disabled = false;

        // After 2.5s redirect to shopping page and hide modal
        setTimeout(() => {
            document.getElementById('paymentSuccessModal').style.display = 'none';
            document.body.style.overflow = '';
            window.location.href = '<?php echo URLROOT; ?>/player/shopping';
        }, 2500);

    }, 1200);
}

// Get payment method display name
function getPaymentMethodName(method) {
    const methods = {
        'card': 'Credit/Debit Card',
        'upi': 'UPI Payment',
        'netbanking': 'Net Banking',
        'wallet': 'Academy Wallet'
    };
    return methods[method] || 'Credit Card';
}

// Go to orders page
function goToOrders() {
    window.location.href = '<?php echo URLROOT; ?>/player/payments';
}

// Continue shopping
function continueShopping() {
    window.location.href = '<?php echo URLROOT; ?>/player/shopping';
}

// Modal close when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = '';
    }
});
</script>

<style>
.checkout-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.checkout-item:last-child {
    border-bottom: none;
}

.checkout-item-details h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.checkout-item-details p {
    margin: 0.25rem 0;
    color: #666;
    font-size: 0.9rem;
}

.checkout-item-total {
    font-weight: bold;
    font-size: 1.1rem;
    color: #4a90e2;
}
</style>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>