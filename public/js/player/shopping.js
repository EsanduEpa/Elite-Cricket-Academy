/* global document, window */

(function () {
    'use strict';

    let currentProduct = null;

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('app-modal--visible');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('app-modal--visible');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = value === null || value === undefined || value === '' ? 'N/A' : String(value);
        }
    }

    function populateProductModal(trigger) {
        currentProduct = {
            productId: trigger.dataset.productId || '',
            stock: Number(trigger.dataset.stock || 0)
        };

        const image = document.getElementById('productDetailImage');
        if (image) {
            image.src = trigger.dataset.image || '';
            image.alt = trigger.dataset.name || 'Product';
        }

        setText('productDetailID', trigger.dataset.productId || '');
        setText('productDetailName', trigger.dataset.name || '');
        setText('productDetailSKU', trigger.dataset.sku || 'N/A');
        setText('productDetailCategory', trigger.dataset.categoryLabel || 'N/A');
        setText('productDetailBrand', trigger.dataset.brandLabel || 'N/A');
        setText('productDetailPrice', Number(trigger.dataset.price || 0).toFixed(2));
        setText('productDetailStock', trigger.dataset.stock || 0);
        setText('productDetailDescription', trigger.dataset.description || 'No description available');
        setText('productDetailWeight', trigger.dataset.weight ? `${trigger.dataset.weight} kg` : 'Not specified');
        setText('productDetailDimensions', trigger.dataset.dimensions || 'Not specified');

        const statusBadge = document.getElementById('productDetailStatus');
        if (statusBadge) {
            const status = trigger.dataset.status || 'active';
            statusBadge.textContent = status;
            statusBadge.className = `status-badge status-${status}`;
        }

        const quantityInput = document.getElementById('productQuantity');
        if (quantityInput) {
            quantityInput.min = '1';
            quantityInput.max = String(Math.max(1, currentProduct.stock));
            quantityInput.value = '1';
        }

        const addToCartId = document.getElementById('productDetailFormId');
        const buyNowId = document.getElementById('productBuyNowFormId');
        if (addToCartId) addToCartId.value = currentProduct.productId;
        if (buyNowId) buyNowId.value = currentProduct.productId;
        syncProductQuantity();
    }

    function syncProductQuantity() {
        const quantityInput = document.getElementById('productQuantity');
        const addToCartQty = document.getElementById('productDetailFormQuantity');
        const buyNowQty = document.getElementById('productBuyNowQuantity');
        const quantity = quantityInput ? Math.max(1, parseInt(quantityInput.value || '1', 10) || 1) : 1;

        if (quantityInput) {
            const max = parseInt(quantityInput.max || '1', 10) || 1;
            quantityInput.value = String(Math.min(quantity, max));
        }
        if (addToCartQty) addToCartQty.value = quantityInput ? quantityInput.value : String(quantity);
        if (buyNowQty) buyNowQty.value = quantityInput ? quantityInput.value : String(quantity);
    }

    function adjustQuantity(change) {
        const quantityInput = document.getElementById('productQuantity');
        if (!quantityInput) return;

        const currentValue = parseInt(quantityInput.value || '1', 10) || 1;
        const maxValue = parseInt(quantityInput.max || '1', 10) || 1;
        const nextValue = Math.min(maxValue, Math.max(1, currentValue + change));
        quantityInput.value = String(nextValue);
        syncProductQuantity();
    }

    function showTab(tabName, button) {
        document.querySelectorAll('#productDetailsModal .tab-pane').forEach((pane) => {
            pane.classList.toggle('active', pane.id === `${tabName}-tab`);
        });

        document.querySelectorAll('#productDetailsModal .tab-btn').forEach((tabButton) => {
            tabButton.classList.toggle('active', tabButton === button);
        });
    }

    function updateCategoryButtons(activeCategory) {
        const buttons = document.querySelectorAll('#product-category-navigation .nav-btn[data-category]');
        buttons.forEach((button) => {
            button.classList.toggle('active', button.dataset.category === activeCategory);
        });
    }

    function filterProducts() {
        const brandFilter = document.getElementById('brand-filter');
        const priceFilter = document.getElementById('price-filter');
        const activeCategory = document.querySelector('#product-category-navigation .nav-btn.active[data-category]')?.dataset?.category || 'all';

        document.querySelectorAll('.product-card').forEach((card) => {
            const cardCategory = card.dataset.category || '';
            const cardBrand = card.dataset.brand || '';
            const cardPrice = Number(card.dataset.price || 0);
            let visible = true;

            if (activeCategory !== 'all' && cardCategory !== activeCategory) {
                visible = false;
            }

            if (visible && brandFilter && brandFilter.value !== 'all' && cardBrand !== brandFilter.value) {
                visible = false;
            }

            if (visible && priceFilter && priceFilter.value !== 'all') {
                const selected = priceFilter.value;
                if (selected.endsWith('+')) {
                    visible = cardPrice >= Number(selected.replace('+', ''));
                } else {
                    const [minPrice, maxPrice] = selected.split('-').map(Number);
                    visible = cardPrice >= minPrice && cardPrice <= maxPrice;
                }
            }

            card.style.display = visible ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('productDetailsModal');

        document.querySelectorAll('.js-view-product').forEach((button) => {
            button.addEventListener('click', function () {
                populateProductModal(button);
                openModal(modal);
            });
        });

        document.querySelectorAll('.js-close-product-details').forEach((button) => {
            button.addEventListener('click', function () {
                closeModal(modal);
            });
        });

        document.querySelectorAll('.js-qty-decrease').forEach((button) => {
            button.addEventListener('click', function () {
                adjustQuantity(-1);
            });
        });

        document.querySelectorAll('.js-qty-increase').forEach((button) => {
            button.addEventListener('click', function () {
                adjustQuantity(1);
            });
        });

        const quantityInput = document.getElementById('productQuantity');
        if (quantityInput) {
            quantityInput.addEventListener('input', syncProductQuantity);
            quantityInput.addEventListener('change', syncProductQuantity);
        }

        document.querySelectorAll('.js-tab-toggle').forEach((button) => {
            button.addEventListener('click', function () {
                showTab(button.dataset.tab || 'shipping', button);
            });
        });

        document.querySelectorAll('#product-category-navigation .nav-btn[data-category]').forEach((button) => {
            button.addEventListener('click', function () {
                updateCategoryButtons(button.dataset.category || 'all');
                filterProducts();
            });
        });

        const brandFilter = document.getElementById('brand-filter');
        const priceFilter = document.getElementById('price-filter');
        if (brandFilter) brandFilter.addEventListener('change', filterProducts);
        if (priceFilter) priceFilter.addEventListener('change', filterProducts);

        if (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal && modal.classList.contains('app-modal--visible')) {
                closeModal(modal);
            }
        });
    });
})();
