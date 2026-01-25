// Shop Accessories JavaScript - Blue & White Theme
class AccessoriesShop {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('cart')) || [];
        this.products = [];
        this.filteredProducts = [];
        
        this.init();
    }

    init() {
        this.loadProducts();
        this.setupEventListeners();
        this.updateCartUI();
        this.renderProducts();
    }

    loadProducts() {
        // Get products from the page
        const productCards = document.querySelectorAll('.product-card');
        this.products = Array.from(productCards).map(card => {
            const id = parseInt(card.querySelector('.add-to-cart-btn').dataset.productId);
            const name = card.querySelector('.product-name').textContent;
            const brand = card.querySelector('.product-brand').textContent;
            const price = parseFloat(card.querySelector('.current-price').textContent.replace('$', ''));
            const originalPrice = card.querySelector('.original-price') ? 
                parseFloat(card.querySelector('.original-price').textContent.replace('$', '')) : price;
            const rating = parseFloat(card.dataset.rating);
            const category = card.dataset.category;
            const image = card.querySelector('.product-image img').src;
            const features = Array.from(card.querySelectorAll('.feature-tag')).map(tag => tag.textContent);
            const stock = this.getStockFromIndicator(card.querySelector('.stock-indicator').textContent);

            return {
                id, name, brand, price, originalPrice, rating, category, image, features, stock
            };
        });

        this.filteredProducts = [...this.products];
    }

    getStockFromIndicator(stockText) {
        if (stockText.includes('Out of Stock')) return 0;
        if (stockText.includes('Only')) {
            const match = stockText.match(/Only (\d+) left/);
            return match ? parseInt(match[1]) : 0;
        }
        return 20; // Default for "In Stock"
    }

    setupEventListeners() {
        // Cart functionality
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                const productId = parseInt(e.target.closest('.add-to-cart-btn').dataset.productId);
                this.addToCart(productId);
            }

            if (e.target.closest('.cart-icon')) {
                e.preventDefault();
                this.toggleCart();
            }

            if (e.target.closest('.cart-close') || e.target.closest('.cart-overlay')) {
                e.preventDefault();
                this.closeCart();
            }

            if (e.target.closest('.quick-view-btn')) {
                e.preventDefault();
                const productId = parseInt(e.target.closest('.quick-view-btn').dataset.productId);
                this.quickView(productId);
            }
        });

        // Filter functionality
        const sortSelect = document.getElementById('sort-select');
        const popularitySelect = document.getElementById('popularity-select');
        const customerSelect = document.getElementById('customer-select');

        if (sortSelect) {
            sortSelect.addEventListener('change', () => this.applyFilters());
        }

        if (popularitySelect) {
            popularitySelect.addEventListener('change', () => this.applyFilters());
        }

        if (customerSelect) {
            customerSelect.addEventListener('change', () => this.applyFilters());
        }

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchProducts(e.target.value);
            });
        }

        // Pagination
        document.addEventListener('click', (e) => {
            if (e.target.closest('.pagination-number')) {
                e.preventDefault();
                this.changePage(parseInt(e.target.textContent));
            }
        });
    }

    addToCart(productId) {
        const product = this.products.find(p => p.id === productId);
        if (!product) return;

        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push({
                ...product,
                quantity: 1
            });
        }

        this.saveCart();
        this.updateCartUI();
        this.showCartNotification(product);
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCartUI();
    }

    updateQuantity(productId, quantity) {
        const item = this.cart.find(item => item.id === productId);
        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(productId);
            } else {
                item.quantity = quantity;
                this.saveCart();
                this.updateCartUI();
            }
        }
    }

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
    }

    updateCartUI() {
        const cartCount = document.querySelector('.cart-count');
        const cartItems = document.getElementById('cart-items');
        const cartTotal = document.getElementById('cart-total');

        // Update cart count
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
        }

        // Update cart items
        if (cartItems) {
            if (this.cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Your cart is empty</p>
                    </div>
                `;
            } else {
                cartItems.innerHTML = this.cart.map(item => `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-info">
                            <h4>${item.name}</h4>
                            <p class="cart-item-brand">${item.brand}</p>
                            <div class="cart-item-controls">
                                <button class="quantity-btn" onclick="accessoriesShop.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                <span class="quantity">${item.quantity}</span>
                                <button class="quantity-btn" onclick="accessoriesShop.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                        </div>
                        <div class="cart-item-price">
                            <span>$${(item.price * item.quantity).toFixed(2)}</span>
                            <button class="remove-item" onclick="accessoriesShop.removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }
        }

        // Update total
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        if (cartTotal) {
            cartTotal.textContent = `$${total.toFixed(2)}`;
        }
    }

    toggleCart() {
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartSidebar && cartOverlay) {
            cartSidebar.classList.toggle('open');
            cartOverlay.classList.toggle('open');
        }
    }

    closeCart() {
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartSidebar && cartOverlay) {
            cartSidebar.classList.remove('open');
            cartOverlay.classList.remove('open');
        }
    }

    showCartNotification(product) {
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span>${product.name} added to cart!</span>
            </div>
        `;

        // Add styles
        Object.assign(notification.style, {
            position: 'fixed',
            top: '100px',
            right: '20px',
            background: '#10b981',
            color: 'white',
            padding: '1rem 1.5rem',
            borderRadius: '8px',
            zIndex: '3000',
            animation: 'slideInRight 0.3s ease'
        });

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    applyFilters() {
        const sortSelect = document.getElementById('sort-select');
        const popularitySelect = document.getElementById('popularity-select');
        const customerSelect = document.getElementById('customer-select');

        let filtered = [...this.products];

        // Apply rating filter
        if (customerSelect && customerSelect.value) {
            const minRating = this.getRatingFromFilter(customerSelect.value);
            filtered = filtered.filter(product => product.rating >= minRating);
        }

        // Apply sorting
        if (sortSelect && sortSelect.value) {
            filtered = this.sortProducts(filtered, sortSelect.value);
        }

        // Apply popularity filter (simulated)
        if (popularitySelect && popularitySelect.value) {
            filtered = this.filterByPopularity(filtered, popularitySelect.value);
        }

        this.filteredProducts = filtered;
        this.renderProducts();
    }

    getRatingFromFilter(filterValue) {
        switch (filterValue) {
            case '5-star': return 5;
            case '4-star': return 4;
            case '3-star': return 3;
            default: return 0;
        }
    }

    sortProducts(products, sortBy) {
        switch (sortBy) {
            case 'price-low':
                return [...products].sort((a, b) => a.price - b.price);
            case 'price-high':
                return [...products].sort((a, b) => b.price - a.price);
            case 'rating':
                return [...products].sort((a, b) => b.rating - a.rating);
            case 'popularity':
                return [...products].sort((a, b) => b.rating * Math.random() - a.rating * Math.random());
            default:
                return products;
        }
    }

    filterByPopularity(products, popularityType) {
        // Simulate popularity filtering
        switch (popularityType) {
            case 'most-popular':
                return [...products].sort((a, b) => b.rating - a.rating);
            case 'trending':
                return [...products].sort(() => Math.random() - 0.5);
            case 'new-arrivals':
                return [...products].reverse();
            default:
                return products;
        }
    }

    searchProducts(query) {
        if (!query.trim()) {
            this.filteredProducts = [...this.products];
        } else {
            const searchTerm = query.toLowerCase();
            this.filteredProducts = this.products.filter(product => 
                product.name.toLowerCase().includes(searchTerm) ||
                product.brand.toLowerCase().includes(searchTerm) ||
                product.category.toLowerCase().includes(searchTerm) ||
                product.features.some(feature => feature.toLowerCase().includes(searchTerm))
            );
        }
        this.renderProducts();
    }

    renderProducts() {
        const productsGrid = document.getElementById('products-grid');
        if (!productsGrid) return;

        // Show loading state
        productsGrid.classList.add('loading');

        setTimeout(() => {
            if (this.filteredProducts.length === 0) {
                productsGrid.innerHTML = `
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search terms</p>
                    </div>
                `;
            } else {
                productsGrid.innerHTML = this.filteredProducts.map(product => this.renderProductCard(product)).join('');
            }

            productsGrid.classList.remove('loading');
        }, 300);
    }

    renderProductCard(product) {
        const hasDiscount = product.originalPrice > product.price;
        const stockClass = product.stock > 10 ? 'in-stock' : (product.stock > 0 ? 'low-stock' : 'out-of-stock');
        const stockText = product.stock > 10 ? 'In Stock' : 
                         (product.stock > 0 ? `Only ${product.stock} left` : 'Out of Stock');

        return `
            <div class="product-card" data-category="${product.category.toLowerCase()}" 
                 data-price="${product.price}" data-rating="${product.rating}">
                <div class="product-image">
                    <img src="${product.image}" alt="${product.name}" 
                         onerror="this.src='${window.location.origin}/img/placeholder-product.jpg'">
                    <div class="product-overlay">
                        <button class="quick-view-btn" data-product-id="${product.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="add-to-cart-btn" data-product-id="${product.id}" 
                                ${product.stock === 0 ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                    ${hasDiscount ? '<div class="sale-badge">Sale</div>' : ''}
                </div>
                
                <div class="product-info">
                    <div class="product-brand">${product.brand}</div>
                    <h3 class="product-name">${product.name}</h3>
                    
                    <div class="product-rating">
                        <div class="stars">
                            ${this.renderStars(product.rating)}
                        </div>
                        <span class="rating-count">(${Math.floor(Math.random() * 200 + 50)})</span>
                    </div>
                    
                    <div class="product-price">
                        <span class="current-price">$${product.price.toFixed(2)}</span>
                        ${hasDiscount ? `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>` : ''}
                    </div>
                    
                    <div class="product-features">
                        ${product.features.map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
                    </div>
                    
                    <div class="product-stock">
                        <span class="stock-indicator ${stockClass}">${stockText}</span>
                    </div>
                </div>
            </div>
        `;
    }

    renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fas fa-star ${i <= rating ? 'active' : ''}"></i>`;
        }
        return stars;
    }

    quickView(productId) {
        const product = this.products.find(p => p.id === productId);
        if (!product) return;

        // Create modal for quick view
        const modal = document.createElement('div');
        modal.className = 'quick-view-modal';
        modal.innerHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <button class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="modal-body">
                        <div class="modal-image">
                            <img src="${product.image}" alt="${product.name}">
                        </div>
                        <div class="modal-info">
                            <h2>${product.name}</h2>
                            <p class="modal-brand">${product.brand}</p>
                            <div class="modal-rating">
                                <div class="stars">${this.renderStars(product.rating)}</div>
                                <span>(${Math.floor(Math.random() * 200 + 50)} reviews)</span>
                            </div>
                            <div class="modal-price">
                                <span class="current-price">$${product.price.toFixed(2)}</span>
                                ${product.originalPrice > product.price ? 
                                  `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>` : ''}
                            </div>
                            <div class="modal-features">
                                <h4>Features:</h4>
                                <ul>
                                    ${product.features.map(feature => `<li>${feature}</li>`).join('')}
                                </ul>
                            </div>
                            <button class="modal-add-to-cart" onclick="accessoriesShop.addToCart(${product.id}); document.querySelector('.quick-view-modal').remove();">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal styles
        const style = document.createElement('style');
        style.textContent = `
            .quick-view-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                z-index: 4000;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .modal-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .modal-content {
                background: white;
                border-radius: 12px;
                max-width: 800px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                position: relative;
            }
            .modal-close {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                z-index: 10;
            }
            .modal-body {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
                padding: 2rem;
            }
            .modal-image img {
                width: 100%;
                border-radius: 8px;
            }
            .modal-info h2 {
                margin-bottom: 0.5rem;
            }
            .modal-brand {
                color: #64748b;
                margin-bottom: 1rem;
            }
            .modal-rating {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }
            .modal-price {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 2rem;
            }
            .modal-price .current-price {
                font-size: 1.5rem;
                font-weight: bold;
                color: #2563eb;
            }
            .modal-features {
                margin-bottom: 2rem;
            }
            .modal-features h4 {
                margin-bottom: 0.5rem;
            }
            .modal-features ul {
                list-style: none;
                padding: 0;
            }
            .modal-features li {
                padding: 0.25rem 0;
                border-bottom: 1px solid #e2e8f0;
            }
            .modal-add-to-cart {
                width: 100%;
                padding: 1rem;
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }
            @media (max-width: 768px) {
                .modal-body {
                    grid-template-columns: 1fr;
                }
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(modal);

        // Close modal functionality
        modal.querySelector('.modal-close').onclick = () => modal.remove();
        modal.querySelector('.modal-overlay').onclick = (e) => {
            if (e.target === e.currentTarget) modal.remove();
        };
    }

    changePage(pageNumber) {
        // Update pagination UI
        document.querySelectorAll('.pagination-number').forEach(btn => {
            btn.classList.remove('active');
        });
        
        const targetButton = Array.from(document.querySelectorAll('.pagination-number'))
            .find(btn => btn.textContent === pageNumber.toString());
        
        if (targetButton) {
            targetButton.classList.add('active');
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    .cart-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #e2e8f0;
    }
    .cart-item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .cart-item-info {
        flex: 1;
    }
    .cart-item-info h4 {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .cart-item-brand {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .quantity-btn {
        width: 24px;
        height: 24px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .quantity {
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }
    .cart-item-price {
        text-align: right;
    }
    .remove-item {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        margin-top: 0.5rem;
    }
    .no-products {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
    }
    .no-products i {
        font-size: 4rem;
        margin-bottom: 1rem;
        display: block;
    }
`;
document.head.appendChild(style);

// Initialize the accessories shop when the page loads
let accessoriesShop;
document.addEventListener('DOMContentLoaded', () => {
    accessoriesShop = new AccessoriesShop();
});

// Export for global access
window.accessoriesShop = accessoriesShop;