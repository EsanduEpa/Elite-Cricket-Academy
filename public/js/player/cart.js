// Cart Page JavaScript

function getCartUrlRoot() {
    const page = document.getElementById('cartPage');
    return page && page.dataset && page.dataset.urlroot ? page.dataset.urlroot : '';
}

function getCartData() {
    const el = document.getElementById('cartData');
    if (!el) return {};

    try {
        return JSON.parse(el.textContent || '{}') || {};
    } catch (e) {
        console.error('Failed to parse cartData JSON', e);
        return {};
    }
}

const cartData = getCartData();

// Shopping cart data and functionality
let cart = Array.isArray(cartData.cartItems) ? cartData.cartItems : [];
let promoDiscounts = cartData.promoDiscounts || {
    'STUDENT10': 0.10,
    'FIRST15': 0.15,
    'CRICKET20': 0.20,
    'ACADEMY25': 0.25
};
let appliedPromo = localStorage.getItem('appliedPromo') || null;
let currentDiscount = 0;

// Product data for recommendations - loaded from server
const sampleProducts = cartData.recommendedProducts || {};

// Initialize cart page
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    loadRecentlyViewed();
    loadRecommendedProducts();
    updateCartSummary();

    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', proceedToCheckout);
    }

    if (appliedPromo) {
        const promoInput = document.getElementById('promo-code');
        if (promoInput) {
            promoInput.value = appliedPromo;
        }
        applyPromoCode();
    }
});

function getCartEndpoint(path) {
    const root = getCartUrlRoot();
    return root ? `${root}/player/${path}` : `/player/${path}`;
}

async function syncCartFromServer() {
    try {
        const response = await fetch(getCartEndpoint('cartItems'), {
            credentials: 'same-origin'
        });
        const payload = await response.json();
        if (payload && payload.success) {
            cart = Array.isArray(payload.items) ? payload.items : [];
            return payload;
        }
    } catch (error) {
        console.error('Failed to sync cart from server', error);
    }
    return null;
}

function getSelectedProductIds(items = getSelectedItems()) {
    return items
        .map((item) => Number(item.ProductID || item.product_id || item.id || 0))
        .filter((id) => id > 0);
}

function getItemQuantity(item) {
    return Number(item && (item.Quantity ?? item.quantity ?? 1)) || 1;
}

function getItemProductId(item) {
    return Number(item && (item.ProductID ?? item.product_id ?? item.id ?? 0)) || 0;
}

async function postCartAction(path, data) {
    const response = await fetch(getCartEndpoint(path), {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams(data).toString()
    });

    return response.json();
}

// Load cart items
function loadCart() {
    const cartItemsList = document.getElementById('cart-items-list');
    const emptyCart = document.getElementById('empty-cart');
    const cartTable = document.getElementById('cart-items-table');
    const totalItemsSpan = document.getElementById('total-items');

    if (!cartItemsList || !emptyCart || !cartTable || !totalItemsSpan) {
        return;
    }

    if (cart.length === 0) {
        emptyCart.style.display = 'block';
        cartTable.style.display = 'none';
        totalItemsSpan.textContent = '0';
        const checkoutBtn = document.getElementById('checkout-btn');
        const mainCheckoutBtn = document.getElementById('main-checkout-btn');
        if (checkoutBtn) checkoutBtn.disabled = true;
        if (mainCheckoutBtn) mainCheckoutBtn.disabled = true;
        return;
    }

    emptyCart.style.display = 'none';
    cartTable.style.display = 'table';

    const totalItems = cart.reduce((sum, item) => sum + getItemQuantity(item), 0);
    totalItemsSpan.textContent = totalItems;

    cartItemsList.innerHTML = '';

    cart.forEach((item, index) => {
        const cartItem = createCartItemElement(item, index);
        cartItemsList.appendChild(cartItem);
    });

    const checkoutBtn = document.getElementById('checkout-btn');
    const mainCheckoutBtn = document.getElementById('main-checkout-btn');
    if (checkoutBtn) checkoutBtn.disabled = false;
    if (mainCheckoutBtn) mainCheckoutBtn.disabled = false;

    updateSelectAllCheckbox();
}

// Create cart item element
function createCartItemElement(item, index) {
    const itemElement = document.createElement('tr');
    itemElement.className = 'cart-item-row';

    const name = item.Name || item.name || 'Item';
    const price = item.Price || item.price || 0;
    const productImage = item.ProductImage || item.productImage || item.image || 'https://via.placeholder.com/52x52?text=Item';

    itemElement.innerHTML = `
        <td class="col-select">
            <input type="checkbox" id="item-${index}" class="item-select-checkbox" checked onchange="updateCheckboxState()">
        </td>
        <td class="col-product">
            <div class="product-info">
                <div class="product-image">
                    <img src="${productImage}" alt="${name}" />
                </div>
                <div class="product-details">
                    <h4 class="product-name">${name}</h4>
                    <p class="product-sku">SKU: ${item.sku || 'N/A'}</p>
                    <p class="product-category">${item.category || 'General'}</p>
                    <div class="product-features">
                        ${item.features ? item.features.slice(0, 2).map(f => `<span class="feature-tag">${f}</span>`).join('') : ''}
                    </div>
                </div>
            </div>
        </td>
        <td class="col-price">
            <span class="price-value">LKR ${Number(price).toFixed(2)}</span>
        </td>
        <td class="col-quantity">
            <div class="quantity-controls">
                <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                <input type="number" value="${getItemQuantity(item)}" min="1" max="10" onchange="updateQuantityDirect(${index}, this.value)">
                <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
            </div>
        </td>
        <td class="col-total">
            <span class="total-value">LKR ${(Number(price) * getItemQuantity(item)).toFixed(2)}</span>
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
        const nextQuantity = getItemQuantity(cart[index]) + change;
        updateCartItem(cart[index], nextQuantity <= 0 ? 0 : nextQuantity);
    }
}

// Update quantity directly
function updateQuantityDirect(index, newQuantity) {
    const qty = parseInt(newQuantity, 10);
    if (cart[index] && qty >= 0) {
        updateCartItem(cart[index], qty);
    }
}

async function updateCartItem(item, quantity) {
    const productId = Number(item.ProductID || item.product_id || item.id || 0);
    if (!productId) return;

    const result = await postCartAction('updateCartItem', {
        product_id: String(productId),
        quantity: String(quantity)
    });

    if (result && result.success) {
        await syncCartFromServer();
        loadCart();
        updateCartSummary();
        updateCartBadge(result.cart_count);
    } else if (result && result.message) {
        showNotification(result.message, 'error');
    }
}

// Remove from cart
function removeFromCart(index) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        const item = cart[index];
        const productId = Number(item && (item.ProductID || item.product_id || item.id || 0));
        if (!productId) return;

        postCartAction('removeCartItem', { product_id: String(productId) })
            .then(async (result) => {
                if (result && result.success) {
                    await syncCartFromServer();
                    loadCart();
                    updateCartSummary();
                    updateCartBadge(result.cart_count);
                    showNotification('Item removed from cart', 'info');
                } else if (result && result.message) {
                    showNotification(result.message, 'error');
                }
            })
            .catch((error) => {
                console.error('Failed to remove cart item', error);
                showNotification('Failed to remove item', 'error');
            });
    }
}

// Clear entire cart
function clearCart() {
    if (cart.length === 0) return;

    if (confirm('Are you sure you want to clear your entire cart?')) {
        postCartAction('clearCart', {})
            .then(async (result) => {
                if (result && result.success) {
                    cart = [];
                    appliedPromo = null;
                    currentDiscount = 0;
                    localStorage.removeItem('appliedPromo');
                    loadCart();
                    updateCartSummary();

                    const promoInput = document.getElementById('promo-code');
                    const promoMessage = document.getElementById('promo-message');
                    if (promoInput) promoInput.value = '';
                    if (promoMessage) promoMessage.textContent = '';

                    updateCartBadge(0);
                    showNotification('Cart cleared', 'info');
                } else if (result && result.message) {
                    showNotification(result.message, 'error');
                }
            })
            .catch((error) => {
                console.error('Failed to clear cart', error);
                showNotification('Failed to clear cart', 'error');
            });
    }
}

// Save for later
function saveForLater() {
    showNotification('Item saved for later (feature in development)', 'info');
}

// Update cart summary
function updateCartSummary() {
    const selectedItems = getSelectedItems();
    const subtotal = selectedItems.reduce((sum, item) => sum + (Number(item.Price || item.price || 0) * getItemQuantity(item)), 0);
    const shipping = subtotal > 100 ? 0 : 15;
    const tax = subtotal * 0.10;
    const discount = subtotal * currentDiscount;
    const total = subtotal + shipping + tax - discount;

    const setText = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    };

    setText('cart-subtotal', `LKR ${subtotal.toFixed(2)}`);
    setText('cart-shipping', shipping === 0 ? 'FREE' : `LKR ${shipping.toFixed(2)}`);
    setText('cart-tax', `LKR ${tax.toFixed(2)}`);
    setText('cart-total', `LKR ${total.toFixed(2)}`);

    const discountRow = document.getElementById('discount-row');
    if (currentDiscount > 0) {
        if (discountRow) discountRow.style.display = 'flex';
        setText('cart-discount', `-LKR ${discount.toFixed(2)}`);
    } else {
        if (discountRow) discountRow.style.display = 'none';
    }

    const checkoutBtn = document.getElementById('checkout-btn');
    const mainCheckoutBtn = document.getElementById('main-checkout-btn');
    const disabled = selectedItems.length === 0;

    if (checkoutBtn) checkoutBtn.disabled = disabled;
    if (mainCheckoutBtn) mainCheckoutBtn.disabled = disabled;
}

// Apply promo code
function applyPromoCode() {
    const promoInput = document.getElementById('promo-code');
    const promoMessage = document.getElementById('promo-message');
    if (!promoInput || !promoMessage) return;

    const promoCode = (promoInput.value || '').toUpperCase();

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

    if (!container) return;

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
                <span class="price">LKR ${Number(item.price || 0).toFixed(2)}</span>
            </div>
            <button class="btn btn-sm" onclick="addToCartFromRecent('${item.id}')">Add to Cart</button>
        `;
        container.appendChild(productElement);
    });
}

// Load recommended products
function loadRecommendedProducts() {
    const container = document.getElementById('recommended-products');
    if (!container) return;

    container.innerHTML = '';

    Object.values(sampleProducts).forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'recommended-item';
        productElement.innerHTML = `
            <img src="${product.image}" alt="${product.name}" />
            <div class="item-info">
                <h5>${product.name}</h5>
                <span class="category">${product.category}</span>
                <span class="price">LKR ${Number(product.price || 0).toFixed(2)}</span>
            </div>
            <button class="btn btn-primary btn-sm" onclick="addRecommendedToCart('${product.id}')">
                <i class="fas fa-plus"></i> Add to Cart
            </button>
        `;
        container.appendChild(productElement);
    });
}

function addToCartFromRecent() {
    showNotification('Add to cart from recent is not implemented yet', 'info');
}

// Add recommended product to cart
function addRecommendedToCart(productId) {
    const product = sampleProducts[productId];
    if (product) {
        postCartAction('addToCart', { product_id: String(product.id), quantity: '1' })
            .then(async (result) => {
                if (result && result.success) {
                    await syncCartFromServer();
                    loadCart();
                    updateCartSummary();
                    updateCartBadge(result.cart_count);
                    showNotification(`${product.name} added to cart!`, 'success');
                } else if (result && result.message) {
                    showNotification(result.message, 'error');
                }
            })
            .catch((error) => {
                console.error('Failed to add recommended product to cart', error);
                showNotification('Failed to add item to cart', 'error');
            });
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

    if (!selectAllCheckbox) return;

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

    if (!selectAllCheckbox) return;

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
            selectedItems.push({ ...item, index: index });
        }
    });
    return selectedItems;
}

// Post cart data to server and redirect to PayHere
function submitToPayhere(items, total) {
    const urlRoot = getCartUrlRoot();
    const action  = urlRoot ? `${urlRoot}/player/payhere_checkout` : '/player/payhere_checkout';

    // Build a temporary form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const addField = (name, value) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = name;
        input.value = value;
        form.appendChild(input);
    };

    addField('cart_items', JSON.stringify(items));
    addField('cart_total', total.toFixed(2));
    addField('selected_product_ids', JSON.stringify(getSelectedProductIds(items)));

    // CSRF-like: include session-based nonce if available
    const nonceEl = document.getElementById('payhere_nonce');
    if (nonceEl) addField('nonce', nonceEl.value);

    document.body.appendChild(form);
    form.submit();
}

// Checkout single item
function checkoutSingleItem(index) {
    if (!cart[index]) {
        showNotification('Item not found!', 'error');
        return;
    }

    const selectedItem = cart[index];
    const total = Number(selectedItem.Price || selectedItem.price || 0) * getItemQuantity(selectedItem);
    submitToPayhere([selectedItem], total);
}

// Proceed to checkout
function proceedToCheckout() {
    const selectedItems = getSelectedItems();

    if (selectedItems.length === 0) {
        showNotification('Please select at least one item to checkout!', 'error');
        return;
    }

    const subtotal = selectedItems.reduce((sum, item) => {
        return sum + Number(item.Price || item.price || 0) * getItemQuantity(item);
    }, 0);
    const shipping = subtotal > 100 ? 0 : 15;
    const tax      = subtotal * 0.10;
    const discount = subtotal * currentDiscount;
    const total    = subtotal + shipping + tax - discount;

    submitToPayhere(selectedItems, total);
}

// Close checkout modal
function closeCheckoutModal() {
    const modal = document.getElementById('checkoutModal');
    if (modal) {
        modal.classList.remove('app-modal--visible');
        modal.setAttribute('aria-hidden', 'true');
    }
    document.body.classList.remove('modal-open');
}

// Complete order
function completeOrder() {
    proceedToCheckout();
}

// Close order success modal
function closeOrderSuccessModal() {
    const modal = document.getElementById('orderSuccessModal');
    if (modal) {
        modal.classList.remove('app-modal--visible');
        modal.setAttribute('aria-hidden', 'true');
    }

    document.body.classList.remove('modal-open');

    const urlRoot = getCartUrlRoot();
    window.location.href = urlRoot ? `${urlRoot}/player` : '/player';
}

// View order history
function viewOrderHistory() {
    closeOrderSuccessModal();

    const urlRoot = getCartUrlRoot();
    window.location.href = urlRoot ? `${urlRoot}/player/orders` : '/player/orders';
}

// Save cart to localStorage
function saveCart() {
    return true;
}

function updateCartBadge(count) {
    const value = Number.isFinite(Number(count)) ? Number(count) : cart.reduce((total, item) => total + Number(item.Quantity || item.quantity || 0), 0);
    document.querySelectorAll('.cart-count, #cartCount').forEach((badge) => {
        badge.textContent = String(value);
    });
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

    setTimeout(() => {
        notification.remove();
    }, 4000);
}

// Modal close handlers
document.addEventListener('click', function(event) {
    const closeTrigger = event.target.closest('[data-cart-close]');
    if (closeTrigger) {
        if (closeTrigger.dataset.cartClose === 'checkoutModal') {
            closeCheckoutModal();
        } else if (closeTrigger.dataset.cartClose === 'orderSuccessModal') {
            closeOrderSuccessModal();
        }
        return;
    }

    const actionTrigger = event.target.closest('[data-cart-action]');
    if (actionTrigger) {
        if (actionTrigger.dataset.cartAction === 'complete-order') {
            completeOrder();
        } else if (actionTrigger.dataset.cartAction === 'view-order-history') {
            viewOrderHistory();
        }
    }
});

window.addEventListener('click', function(event) {
    if (event.target && event.target.classList && event.target.classList.contains('app-modal')) {
        if (event.target.id === 'checkoutModal') {
            closeCheckoutModal();
        } else if (event.target.id === 'orderSuccessModal') {
            closeOrderSuccessModal();
        }
    }
});
