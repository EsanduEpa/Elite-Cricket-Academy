/* global document, window, localStorage */

(function () {
    'use strict';

    function showCheckoutModal(modal) {
        if (!modal) return;
        modal.classList.add('app-modal--visible');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }

    function hideCheckoutModal(modal) {
        if (!modal) return;
        modal.classList.remove('app-modal--visible');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    function getCheckoutUrlRoot() {
        const page = document.getElementById('checkoutPage');
        return (page && page.dataset && page.dataset.urlroot) ? page.dataset.urlroot : '';
    }

    function safeJsonParse(value) {
        try {
            return JSON.parse(value);
        } catch (_err) {
            return null;
        }
    }

    let checkoutData = null;

    function initCheckoutPage() {
        checkoutData = safeJsonParse(localStorage.getItem('checkoutData'));

        if (!checkoutData) {
            window.location.href = getCheckoutUrlRoot() + '/player/cart';
            return;
        }

        loadCheckoutItems();
        setupPaymentForm();
    }

    function loadCheckoutItems() {
        const itemsList = document.getElementById('checkout-items-list');
        const totalSpan = document.getElementById('payment-total');
        const finalAmountSpan = document.getElementById('final-amount');

        if (!itemsList || !totalSpan || !finalAmountSpan) return;
        if (!checkoutData || !Array.isArray(checkoutData.items)) return;

        itemsList.innerHTML = '';

        checkoutData.items.forEach((item) => {
            const name = item && (item.Name || item.name) ? (item.Name || item.name) : 'Item';
            const quantity = Number(item && item.quantity ? item.quantity : 0);
            const unitPrice = Number(item && (item.Price ?? item.price) ? (item.Price ?? item.price) : 0);

            const itemElement = document.createElement('div');
            itemElement.className = 'checkout-item';
            itemElement.innerHTML = `
                <div class="checkout-item-details">
                    <h4>${name}</h4>
                    <p>Quantity: ${quantity}</p>
                    <p>Price: ₹${unitPrice.toFixed(2)} each</p>
                </div>
                <div class="checkout-item-total">
                    ₹${(unitPrice * quantity).toFixed(2)}
                </div>
            `;

            itemsList.appendChild(itemElement);
        });

        const total = Number(checkoutData.total || 0).toFixed(2);
        totalSpan.innerHTML = `<strong>₹${total}</strong>`;
        finalAmountSpan.textContent = `₹${total}`;
    }

    function setupPaymentForm() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const cardForm = document.getElementById('card-form');

        paymentMethods.forEach((method) => {
            method.addEventListener('change', function () {
                if (!cardForm) return;
                cardForm.style.display = (this.value === 'card') ? 'block' : 'none';
            });
        });

        const cardNumberInput = document.getElementById('card-number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function () {
                const raw = String(this.value || '').replace(/\s/g, '').replace(/[^0-9]/g, '');
                const formatted = raw.match(/.{1,4}/g)?.join(' ') || raw;
                this.value = formatted;
            });
        }

        const expiryInput = document.getElementById('expiry-date');
        if (expiryInput) {
            expiryInput.addEventListener('input', function () {
                let value = String(this.value || '').replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                this.value = value;
            });
        }
    }

    function showFieldError(id, message) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = message;
        el.style.display = message ? 'block' : 'none';
    }

    function validateCardNumber(value) {
        const digits = String(value || '').replace(/\s/g, '');
        return /^[0-9]{13,19}$/.test(digits);
    }

    function validateExpiry(value) {
        const str = String(value || '');
        if (!/^(0[1-9]|1[0-2])\/(\d{2})$/.test(str)) return false;

        const parts = str.split('/');
        const month = parseInt(parts[0], 10);
        const year = 2000 + parseInt(parts[1], 10);

        const now = new Date();
        const expiry = new Date(year, month - 1, 1);
        expiry.setMonth(expiry.getMonth() + 1);
        expiry.setDate(0);

        return expiry >= new Date(now.getFullYear(), now.getMonth(), 1);
    }

    function validateCVV(value) {
        return /^[0-9]{3,4}$/.test(String(value || ''));
    }

    function validateCardholder(name) {
        return typeof name === 'string' && name.trim().length >= 2;
    }

    function getPaymentMethodName(method) {
        const methods = {
            card: 'Credit/Debit Card',
            upi: 'UPI Payment',
            netbanking: 'Net Banking',
            wallet: 'Academy Wallet'
        };

        return methods[method] || 'Credit Card';
    }

    function processPayment() {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        const selectedMethod = checked ? checked.value : 'card';

        if (selectedMethod === 'card') {
            const cardNumber = String(document.getElementById('card-number')?.value || '').trim();
            const expiry = String(document.getElementById('expiry-date')?.value || '').trim();
            const cvv = String(document.getElementById('cvv')?.value || '').trim();
            const cardholder = String(document.getElementById('cardholder-name')?.value || '').trim();

            let valid = true;
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
                const firstError = document.querySelector('.field-error[style*="display: block"]');
                if (firstError) firstError.previousElementSibling?.focus();
                return;
            }
        }

        const paymentBtn = document.getElementById('complete-payment-btn');
        if (!paymentBtn) return;

        const originalText = paymentBtn.innerHTML;
        paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        paymentBtn.disabled = true;

        window.setTimeout(() => {
            const orderId = 'ECA-' + new Date().getFullYear() + '-' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');

            document.getElementById('order-id').textContent = orderId;
            document.getElementById('paid-amount').textContent = `₹${Number(checkoutData?.total || 0).toFixed(2)}`;
            document.getElementById('payment-method-used').textContent = getPaymentMethodName(selectedMethod);

            let cart = safeJsonParse(localStorage.getItem('shoppingCart')) || [];

            if (checkoutData?.type === 'multiple') {
                (checkoutData.items || []).forEach((checkoutItem) => {
                    cart = cart.filter((cartItem) => String(cartItem.ProductID) !== String(checkoutItem.ProductID));
                });
            } else {
                const firstId = checkoutData?.items?.[0]?.ProductID;
                cart = cart.filter((cartItem) => String(cartItem.ProductID) !== String(firstId));
            }

            localStorage.setItem('shoppingCart', JSON.stringify(cart));
            localStorage.removeItem('checkoutData');

            const modal = document.getElementById('paymentSuccessModal');
            if (modal) {
                showCheckoutModal(modal);
            }

            paymentBtn.innerHTML = originalText;
            paymentBtn.disabled = false;

            window.setTimeout(() => {
                if (modal) {
                    hideCheckoutModal(modal);
                }

                window.location.href = getCheckoutUrlRoot() + '/player/shopping';
            }, 2500);
        }, 1200);
    }

    function goToOrders() {
        window.location.href = getCheckoutUrlRoot() + '/player/payments';
    }

    function continueShopping() {
        window.location.href = getCheckoutUrlRoot() + '/player/shopping';
    }

    window.processPayment = processPayment;
    window.goToOrders = goToOrders;
    window.continueShopping = continueShopping;

    document.addEventListener('click', function (event) {
        const actionTrigger = event.target.closest('[data-checkout-action]');
        if (actionTrigger) {
            if (actionTrigger.dataset.checkoutAction === 'go-to-orders') {
                goToOrders();
            } else if (actionTrigger.dataset.checkoutAction === 'continue-shopping') {
                continueShopping();
            }
            return;
        }

        const modal = document.getElementById('paymentSuccessModal');
        if (modal && event.target === modal) {
            hideCheckoutModal(modal);
        }
    });

    document.addEventListener('DOMContentLoaded', initCheckoutPage);
})();
