<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/cart.css">
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
                    <button class="btn btn-primary" id="checkout-btn" disabled>
                        <i class="fas fa-credit-card"></i>
                        Proceed to Checkout
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
                        <span id="cart-subtotal">₹0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="cart-shipping">₹0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span id="cart-tax">₹0.00</span>
                    </div>
                    
                    <div class="summary-row discount-row" id="discount-row" style="display: none;">
                        <span>Discount:</span>
                        <span id="cart-discount">-₹0.00</span>
                    </div>
                    
                    <hr class="summary-divider">
                    
                    <div class="summary-row total-row">
                        <span><strong>Total:</strong></span>
                        <span id="cart-total"><strong>₹0.00</strong></span>
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

                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <h4>Payment Method</h4>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment" value="card" checked>
                                <div class="payment-info">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Credit/Debit Card</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="paypal">
                                <div class="payment-info">
                                    <i class="fab fa-paypal"></i>
                                    <span>PayPal</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="bank">
                                <div class="payment-info">
                                    <i class="fas fa-university"></i>
                                    <span>Bank Transfer</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="cash">
                                <div class="payment-info">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Cash on Pickup</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <button class="btn btn-primary btn-large checkout-button" id="main-checkout-btn" onclick="proceedToCheckout()">
                        <i class="fas fa-lock"></i>
                        Secure Checkout
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
<div id="checkoutModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="header-text">
                <h3>Checkout Confirmation</h3>
                <p>Review your order details before completing purchase</p>
            </div>
            <button class="modal-close" onclick="closeCheckoutModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
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

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeCheckoutModal()">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </button>
            <button type="button" class="btn btn-primary" onclick="completeOrder()">
                <i class="fas fa-check"></i> Complete Order
            </button>
        </div>
    </div>
</div>

<!-- Order Success Modal -->
<div id="orderSuccessModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success-header">
            <div class="header-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="header-text">
                <h3>Order Placed Successfully!</h3>
                <p>Your order has been confirmed and is being processed</p>
            </div>
        </div>
        
        <div class="modal-body">
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

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeOrderSuccessModal()">
                <i class="fas fa-home"></i> Back to Dashboard
            </button>
            <button type="button" class="btn btn-primary" onclick="viewOrderHistory()">
                <i class="fas fa-history"></i> View Orders
            </button>
        </div>
    </div>
</div>

<script>
// Shopping cart data and functionality
let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
let promoDiscounts = {
    'STUDENT10': 0.10,
    'FIRST15': 0.15,
    'CRICKET20': 0.20,
    'ACADEMY25': 0.25
};
let appliedPromo = localStorage.getItem('appliedPromo') || null;
let currentDiscount = 0;

// Sample product data for recommendations
const sampleProducts = {
    'gloves': {
        id: 'gloves',
        name: 'Pro Cricket Gloves',
        price: 65.00,
        image: 'https://via.placeholder.com/150x100?text=Gloves',
        category: 'Protective'
    },
    'ball': {
        id: 'ball',
        name: 'Leather Cricket Ball',
        price: 25.00,
        image: 'https://via.placeholder.com/150x100?text=Ball',
        category: 'Training'
    },
    'bag': {
        id: 'bag',
        name: 'Equipment Bag',
        price: 85.00,
        image: 'https://via.placeholder.com/150x100?text=Bag',
        category: 'Accessories'
    }
};

// Initialize cart page
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    loadRecentlyViewed();
    loadRecommendedProducts();
    updateCartSummary();
    
    if (appliedPromo) {
        document.getElementById('promo-code').value = appliedPromo;
        applyPromoCode();
    }
});

// Load cart items
function loadCart() {
    const cartItemsList = document.getElementById('cart-items-list');
    const emptyCart = document.getElementById('empty-cart');
    const cartTable = document.getElementById('cart-items-table');
    const totalItemsSpan = document.getElementById('total-items');
    
    if (cart.length === 0) {
        emptyCart.style.display = 'block';
        cartTable.style.display = 'none';
        totalItemsSpan.textContent = '0';
        document.getElementById('checkout-btn').disabled = true;
        document.getElementById('main-checkout-btn').disabled = true;
        return;
    }
    
    emptyCart.style.display = 'none';
    cartTable.style.display = 'table';
    
    let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    totalItemsSpan.textContent = totalItems;
    
    cartItemsList.innerHTML = '';
    
    cart.forEach((item, index) => {
        const cartItem = createCartItemElement(item, index);
        cartItemsList.appendChild(cartItem);
    });
    
    document.getElementById('checkout-btn').disabled = false;
    document.getElementById('main-checkout-btn').disabled = false;
    
    // Update select all checkbox state
    updateSelectAllCheckbox();
}

// Create cart item element
function createCartItemElement(item, index) {
    const itemElement = document.createElement('tr');
    itemElement.className = 'cart-item-row';
    itemElement.innerHTML = `
        <td class="col-select">
            <input type="checkbox" id="item-${index}" class="item-select-checkbox" checked onchange="updateCheckboxState()">
        </td>
        <td class="col-product">
            <div class="product-info">
                <div class="product-image">
                    <img src="${item.image || 'https://via.placeholder.com/80x60?text=Product'}" alt="${item.Name || item.name}" />
                </div>
                <div class="product-details">
                    <h4 class="product-name">${item.Name || item.name}</h4>
                    <p class="product-sku">SKU: ${item.sku || 'N/A'}</p>
                    <p class="product-category">${item.category || 'General'}</p>
                    <div class="product-features">
                        ${item.features ? item.features.slice(0, 2).map(f => `<span class="feature-tag">${f}</span>`).join('') : ''}
                    </div>
                </div>
            </div>
        </td>
        <td class="col-price">
            <span class="price-value">₹${(item.Price || item.price).toFixed(2)}</span>
        </td>
        <td class="col-quantity">
            <div class="quantity-controls">
                <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                <input type="number" value="${item.quantity}" min="1" max="10" onchange="updateQuantityDirect(${index}, this.value)">
                <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
            </div>
        </td>
        <td class="col-total">
            <span class="total-value">₹${((item.Price || item.price) * item.quantity).toFixed(2)}</span>
        </td>
        <td class="col-actions">
            <div class="action-buttons">
                <button class="btn btn-primary btn-sm item-checkout-btn" onclick="checkoutSingleItem(${index})">
                    <i class="fas fa-credit-card"></i>
                    Checkout
                </button>
                <button class="btn btn-link btn-sm" onclick="saveForLater(${index})" title="Save for Later">
                    <i class="fas fa-heart"></i>
                </button>
                <button class="btn btn-link btn-sm" onclick="removeFromCart(${index})" title="Remove Item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    `;
    return itemElement;
}

// Update quantity
function updateQuantity(index, change) {
    if (cart[index]) {
        cart[index].quantity += change;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        saveCart();
        loadCart();
        updateCartSummary();
    }
}

// Update quantity directly
function updateQuantityDirect(index, newQuantity) {
    const qty = parseInt(newQuantity);
    if (cart[index] && qty > 0 && qty <= 10) {
        cart[index].quantity = qty;
        saveCart();
        loadCart();
        updateCartSummary();
    }
}

// Remove from cart
function removeFromCart(index) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        cart.splice(index, 1);
        saveCart();
        loadCart();
        updateCartSummary();
        showNotification('Item removed from cart', 'info');
    }
}

// Clear entire cart
function clearCart() {
    if (cart.length === 0) return;
    
    if (confirm('Are you sure you want to clear your entire cart?')) {
        cart = [];
        appliedPromo = null;
        currentDiscount = 0;
        saveCart();
        localStorage.removeItem('appliedPromo');
        loadCart();
        updateCartSummary();
        document.getElementById('promo-code').value = '';
        document.getElementById('promo-message').textContent = '';
        showNotification('Cart cleared', 'info');
    }
}

// Save for later
function saveForLater(index) {
    // In a real implementation, this would save to a "saved items" list
    showNotification('Item saved for later (feature in development)', 'info');
}

// Update cart summary
function updateCartSummary() {
    // Calculate totals only for selected items
    const selectedItems = getSelectedItems();
    const subtotal = selectedItems.reduce((sum, item) => sum + ((item.Price || item.price) * item.quantity), 0);
    const shipping = subtotal > 100 ? 0 : 15; // Free shipping over ₹100
    const tax = subtotal * 0.10; // 10% tax
    const discount = subtotal * currentDiscount;
    const total = subtotal + shipping + tax - discount;
    
    document.getElementById('cart-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('cart-shipping').textContent = shipping === 0 ? 'FREE' : `₹${shipping.toFixed(2)}`;
    document.getElementById('cart-tax').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('cart-total').textContent = `₹${total.toFixed(2)}`;
    
    if (currentDiscount > 0) {
        document.getElementById('discount-row').style.display = 'flex';
        document.getElementById('cart-discount').textContent = `-₹${discount.toFixed(2)}`;
    } else {
        document.getElementById('discount-row').style.display = 'none';
    }
    
    // Update checkout button state
    const checkoutBtn = document.getElementById('checkout-btn');
    const mainCheckoutBtn = document.getElementById('main-checkout-btn');
    if (selectedItems.length > 0) {
        checkoutBtn.disabled = false;
        if (mainCheckoutBtn) mainCheckoutBtn.disabled = false;
    } else {
        checkoutBtn.disabled = true;
        if (mainCheckoutBtn) mainCheckoutBtn.disabled = true;
    }
}

// Apply promo code
function applyPromoCode() {
    const promoCode = document.getElementById('promo-code').value.toUpperCase();
    const promoMessage = document.getElementById('promo-message');
    
    if (!promoCode) {
        promoMessage.textContent = 'Please enter a promo code';
        promoMessage.className = 'promo-message error';
        return;
    }
    
    if (promoDiscounts[promoCode]) {
        appliedPromo = promoCode;
        currentDiscount = promoDiscounts[promoCode];
        localStorage.setItem('appliedPromo', appliedPromo);
        
        promoMessage.textContent = `Promo code applied! ${Math.round(currentDiscount * 100)}% discount`;
        promoMessage.className = 'promo-message success';
        
        updateCartSummary();
        showNotification(`Promo code applied! ${Math.round(currentDiscount * 100)}% off`, 'success');
    } else {
        promoMessage.textContent = 'Invalid promo code';
        promoMessage.className = 'promo-message error';
    }
}

// Load recently viewed products
function loadRecentlyViewed() {
    const recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed')) || [];
    const container = document.getElementById('recently-viewed');
    
    if (recentlyViewed.length === 0) {
        container.innerHTML = '<p>No recently viewed items</p>';
        return;
    }
    
    container.innerHTML = '';
    recentlyViewed.slice(0, 3).forEach(item => {
        const productElement = document.createElement('div');
        productElement.className = 'recently-viewed-item';
        productElement.innerHTML = `
            <img src="${item.image}" alt="${item.name}" />
            <div class="item-info">
                <h5>${item.name}</h5>
                <span class="price">$${item.price.toFixed(2)}</span>
            </div>
            <button class="btn btn-sm" onclick="addToCartFromRecent('${item.id}')">Add to Cart</button>
        `;
        container.appendChild(productElement);
    });
}

// Load recommended products
function loadRecommendedProducts() {
    const container = document.getElementById('recommended-products');
    container.innerHTML = '';
    
    Object.values(sampleProducts).forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'recommended-item';
        productElement.innerHTML = `
            <img src="${product.image}" alt="${product.name}" />
            <div class="item-info">
                <h5>${product.name}</h5>
                <span class="category">${product.category}</span>
                <span class="price">$${product.price.toFixed(2)}</span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="addRecommendedToCart('${product.id}')">
                <i class="fas fa-plus"></i> Add to Cart
            </button>
        `;
        container.appendChild(productElement);
    });
}

// Add recommended product to cart
function addRecommendedToCart(productId) {
    const product = sampleProducts[productId];
    if (product) {
        const cartItem = {
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            category: product.category,
            quantity: 1
        };
        
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push(cartItem);
        }
        
        saveCart();
        loadCart();
        updateCartSummary();
        showNotification(`${product.name} added to cart!`, 'success');
    }
}

// Update checkbox state and totals
function updateCheckboxState() {
    updateCartSummary();
    updateSelectAllCheckbox();
}

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-select-checkbox');
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateCartSummary();
}

// Update select all checkbox state based on individual checkboxes
function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-select-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.item-select-checkbox:checked');
    
    if (itemCheckboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length === itemCheckboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length > 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
}

// Get selected items for checkout
function getSelectedItems() {
    const selectedItems = [];
    cart.forEach((item, index) => {
        const checkbox = document.getElementById(`item-${index}`);
        if (checkbox && checkbox.checked) {
            selectedItems.push({...item, index: index});
        }
    });
    return selectedItems;
}

// Checkout single item
function checkoutSingleItem(index) {
    if (!cart[index]) {
        showNotification('Item not found!', 'error');
        return;
    }
    
    const selectedItem = cart[index];
    const checkoutData = {
        items: [selectedItem],
        total: (selectedItem.Price || selectedItem.price) * selectedItem.quantity,
        type: 'single'
    };
    
    // Store checkout data in localStorage for payment page
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    
    // Redirect to payment portal
    window.location.href = '<?php echo URLROOT; ?>/player/payment';
}

// Proceed to checkout
function proceedToCheckout() {
    const selectedItems = getSelectedItems();
    
    if (selectedItems.length === 0) {
        showNotification('Please select at least one item to checkout!', 'error');
        return;
    }
    
    // Calculate total for selected items
    let total = 0;
    selectedItems.forEach(item => {
        total += (item.Price || item.price) * item.quantity;
    });
    
    const checkoutData = {
        items: selectedItems,
        total: total,
        type: 'multiple'
    };
    
    // Store checkout data in localStorage for payment page
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    
    // Redirect to payment portal
    window.location.href = '<?php echo URLROOT; ?>/player/payment';
}

// Populate checkout modal
function populateCheckoutModal() {
    const checkoutItemsList = document.getElementById('checkout-items-list');
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 100 ? 0 : 15;
    const tax = subtotal * 0.10;
    const discount = subtotal * currentDiscount;
    const total = subtotal + shipping + tax - discount;
    
    // Populate items
    checkoutItemsList.innerHTML = '';
    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'checkout-item';
        itemElement.innerHTML = `
            <span class="item-name">${item.name} x ${item.quantity}</span>
            <span class="item-price">$${(item.price * item.quantity).toFixed(2)}</span>
        `;
        checkoutItemsList.appendChild(itemElement);
    });
    
    // Populate totals
    document.getElementById('checkout-subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('checkout-shipping').textContent = shipping === 0 ? 'FREE' : `$${shipping.toFixed(2)}`;
    document.getElementById('checkout-tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('checkout-total').textContent = `$${total.toFixed(2)}`;
}

// Close checkout modal
function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Complete order
function completeOrder() {
    // Generate order number
    const orderNumber = 'ORD-' + new Date().getFullYear() + '-' + String(Date.now()).slice(-6);
    
    // Clear cart
    cart = [];
    appliedPromo = null;
    currentDiscount = 0;
    saveCart();
    localStorage.removeItem('appliedPromo');
    
    // Close checkout modal
    closeCheckoutModal();
    
    // Show success modal
    document.getElementById('order-number').textContent = orderNumber;
    document.getElementById('orderSuccessModal').style.display = 'flex';
    
    // Refresh cart display
    loadCart();
    updateCartSummary();
}

// Close order success modal
function closeOrderSuccessModal() {
    document.getElementById('orderSuccessModal').style.display = 'none';
    document.body.style.overflow = '';
    window.location.href = '<?php echo URLROOT; ?>/player';
}

// View order history
function viewOrderHistory() {
    closeOrderSuccessModal();
    window.location.href = '<?php echo URLROOT; ?>/player/orders';
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('shoppingCart', JSON.stringify(cart));
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.remove();
    }, 4000);
}

// Modal close handlers
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        if (event.target.id === 'checkoutModal') {
            closeCheckoutModal();
        } else if (event.target.id === 'orderSuccessModal') {
            closeOrderSuccessModal();
        }
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>