// ===== SHOP PRODUCTS PAGE JAVASCRIPT =====

// Global Variables
let currentPage = 1;
let isLoading = false;
let cart = JSON.parse(localStorage.getItem('eliteCricketCart')) || [];
let allProducts = [];
let filteredProducts = [];

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeShop();
    setupEventListeners();
    loadProducts();
    updateCartDisplay();
});

// Initialize Shop
function initializeShop() {
    console.log('Elite Cricket Shop initialized');
    
    // Get all products from the page
    allProducts = Array.from(document.querySelectorAll('.product-card')).map(card => ({
        id: card.querySelector('.add-to-cart-btn').dataset.productId,
        name: card.querySelector('.add-to-cart-btn').dataset.productName,
        price: parseFloat(card.querySelector('.add-to-cart-btn').dataset.productPrice),
        category: card.dataset.category,
        rating: parseFloat(card.dataset.rating),
        element: card
    }));
    
    filteredProducts = [...allProducts];
    
    // Setup animations
    setupScrollAnimations();
}

// Setup Event Listeners
function setupEventListeners() {
    // Filter Controls
    document.getElementById('categoryFilter')?.addEventListener('change', applyFilters);
    document.getElementById('priceFilter')?.addEventListener('change', applyFilters);
    document.getElementById('sortFilter')?.addEventListener('change', applySorting);
    document.getElementById('clearFilters')?.addEventListener('click', clearAllFilters);
    
    // View Toggle
    document.querySelectorAll('.view-toggle button').forEach(btn => {
        btn.addEventListener('click', toggleView);
    });
    
    // Search
    document.getElementById('searchInput')?.addEventListener('input', handleSearch);
    
    // Cart Actions
    document.getElementById('cartBtn')?.addEventListener('click', toggleCart);
    document.getElementById('closeCart')?.addEventListener('click', closeCart);
    document.getElementById('cartOverlay')?.addEventListener('click', closeCart);
    
    // Add to Cart Buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', addToCart);
    });
    
    // Quick View Buttons
    document.querySelectorAll('.quick-view-btn').forEach(btn => {
        btn.addEventListener('click', quickView);
    });
    
    // Wishlist Buttons
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', toggleWishlist);
    });
    
    // Load More Button
    document.getElementById('loadMoreBtn')?.addEventListener('click', loadMoreProducts);
    
    // Newsletter Form
    document.getElementById('newsletterForm')?.addEventListener('submit', handleNewsletterSubmit);
    
    // Window Events
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);
}

// Product Filtering
function applyFilters() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    
    filteredProducts = allProducts.filter(product => {
        // Category Filter
        if (categoryFilter && product.category !== categoryFilter) {
            return false;
        }
        
        // Price Filter
        if (priceFilter) {
            const price = product.price;
            switch (priceFilter) {
                case '0-50':
                    if (price > 50) return false;
                    break;
                case '50-100':
                    if (price < 50 || price > 100) return false;
                    break;
                case '100-200':
                    if (price < 100 || price > 200) return false;
                    break;
                case '200-500':
                    if (price < 200 || price > 500) return false;
                    break;
                case '500+':
                    if (price < 500) return false;
                    break;
            }
        }
        
        return true;
    });
    
    updateProductDisplay();
    showNotification(`Showing ${filteredProducts.length} products`, 'info');
}

// Product Sorting
function applySorting() {
    const sortBy = document.getElementById('sortFilter').value;
    
    filteredProducts.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return a.name.localeCompare(b.name);
            case 'price-low':
                return a.price - b.price;
            case 'price-high':
                return b.price - a.price;
            case 'rating':
                return b.rating - a.rating;
            case 'newest':
                // Assuming newer products have higher IDs
                return b.id - a.id;
            default:
                return 0;
        }
    });
    
    updateProductDisplay();
}

// Update Product Display
function updateProductDisplay() {
    const productsGrid = document.getElementById('productsGrid');
    
    // Hide all products first
    allProducts.forEach(product => {
        product.element.style.display = 'none';
    });
    
    // Show filtered products
    filteredProducts.forEach((product, index) => {
        product.element.style.display = 'block';
        product.element.style.order = index;
        
        // Add animation delay
        setTimeout(() => {
            product.element.classList.add('fade-in-up');
        }, index * 50);
    });
    
    // Update load more button
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.style.display = filteredProducts.length > 12 ? 'block' : 'none';
    }
}

// Clear All Filters
function clearAllFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('priceFilter').value = '';
    document.getElementById('sortFilter').value = 'name';
    
    filteredProducts = [...allProducts];
    updateProductDisplay();
    
    showNotification('Filters cleared', 'success');
}

// Search Functionality
function handleSearch(event) {
    const searchTerm = event.target.value.toLowerCase();
    
    if (searchTerm.length === 0) {
        filteredProducts = [...allProducts];
    } else {
        filteredProducts = allProducts.filter(product => 
            product.name.toLowerCase().includes(searchTerm)
        );
    }
    
    updateProductDisplay();
}

// View Toggle
function toggleView(event) {
    const viewType = event.currentTarget.dataset.view;
    const productsGrid = document.getElementById('productsGrid');
    
    // Update button states
    document.querySelectorAll('.view-toggle button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Update grid layout
    if (viewType === 'list') {
        productsGrid.classList.add('list-view');
    } else {
        productsGrid.classList.remove('list-view');
    }
}

// Cart Functionality
function addToCart(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const productId = button.dataset.productId;
    const productName = button.dataset.productName;
    const productPrice = parseFloat(button.dataset.productPrice);
    
    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                image: `product-${productId}.jpg`
            });
        }
        
        saveCart();
        updateCartDisplay();
        
        // Success feedback
        button.innerHTML = '<i class="fas fa-check"></i> Added!';
        button.style.background = 'var(--success)';
        
        // Reset button
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            button.style.background = '';
        }, 2000);
        
        showNotification(`${productName} added to cart!`, 'success');
        
    }, 800);
}

// Quick View
function quickView(event) {
    event.preventDefault();
    const productId = event.currentTarget.dataset.productId;
    
    // Create modal (simplified version)
    showNotification('Quick view feature coming soon!', 'info');
}

// Wishlist Toggle
function toggleWishlist(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const productId = button.dataset.productId;
    
    button.classList.toggle('active');
    
    if (button.classList.contains('active')) {
        button.style.color = 'var(--danger)';
        showNotification('Added to wishlist!', 'success');
    } else {
        button.style.color = '';
        showNotification('Removed from wishlist', 'info');
    }
}

// Cart Management
function toggleCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    
    cartSidebar.classList.toggle('open');
    cartOverlay.classList.toggle('active');
    
    if (cartSidebar.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
        updateCartContent();
    } else {
        document.body.style.overflow = '';
    }
}

function closeCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    
    cartSidebar.classList.remove('open');
    cartOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

function updateCartDisplay() {
    const cartCount = document.querySelector('.cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }
}

function updateCartContent() {
    const cartContent = document.getElementById('cartContent');
    const cartTotal = document.getElementById('cartTotal');
    
    if (!cartContent) return;
    
    if (cart.length === 0) {
        cartContent.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--medium-gray); margin-bottom: 1rem;"></i>
                <p>Your cart is empty</p>
                <button onclick="closeCart()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary-blue); color: white; border: none; border-radius: 0.5rem; cursor: pointer;">Continue Shopping</button>
            </div>
        `;
        return;
    }
    
    let cartHTML = '';
    let total = 0;
    
    cart.forEach(item => {
        total += item.price * item.quantity;
        cartHTML += `
            <div class="cart-item">
                <img src="${URLROOT}/img/products/${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.5rem;">
                <div class="item-details" style="flex: 1; margin-left: 1rem;">
                    <h4 style="margin-bottom: 0.25rem; font-size: 0.9rem;">${item.name}</h4>
                    <p style="color: var(--primary-blue); font-weight: 600;">$${item.price.toFixed(2)}</p>
                </div>
                <div class="item-controls" style="display: flex; align-items: center; gap: 0.5rem;">
                    <button onclick="updateQuantity('${item.id}', ${item.quantity - 1})" style="width: 30px; height: 30px; border: 1px solid var(--light-gray); background: white; border-radius: 0.25rem; cursor: pointer;">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateQuantity('${item.id}', ${item.quantity + 1})" style="width: 30px; height: 30px; border: 1px solid var(--light-gray); background: white; border-radius: 0.25rem; cursor: pointer;">+</button>
                </div>
            </div>
        `;
    });
    
    cartContent.innerHTML = cartHTML;
    
    if (cartTotal) {
        cartTotal.textContent = total.toFixed(2);
    }
}

function updateQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        cart = cart.filter(item => item.id !== productId);
    } else {
        const item = cart.find(item => item.id === productId);
        if (item) {
            item.quantity = newQuantity;
        }
    }
    
    saveCart();
    updateCartDisplay();
    updateCartContent();
}

function saveCart() {
    localStorage.setItem('eliteCricketCart', JSON.stringify(cart));
}

// Load More Products
function loadMoreProducts() {
    if (isLoading) return;
    
    isLoading = true;
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const originalText = loadMoreBtn.innerHTML;
    
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    loadMoreBtn.disabled = true;
    
    // Simulate loading more products
    setTimeout(() => {
        // In a real app, you would make an API call here
        showNotification('All products loaded!', 'info');
        loadMoreBtn.style.display = 'none';
        isLoading = false;
    }, 1500);
}

// Newsletter Submission
function handleNewsletterSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const email = form.querySelector('input[type="email"]').value;
    const submitBtn = form.querySelector('button[type="submit"]');
    
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showNotification('Successfully subscribed to newsletter!', 'success');
        form.reset();
        
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 1000);
}

// Scroll Animations
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe product cards
    document.querySelectorAll('.product-card').forEach(card => {
        observer.observe(card);
    });
}

// Scroll Handler
function handleScroll() {
    // Add scroll-to-top functionality
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Show/hide scroll-to-top button (if you add one)
    const scrollToTopBtn = document.getElementById('scrollToTop');
    if (scrollToTopBtn) {
        if (scrollTop > 300) {
            scrollToTopBtn.style.display = 'block';
        } else {
            scrollToTopBtn.style.display = 'none';
        }
    }
}

// Resize Handler
function handleResize() {
    // Handle responsive adjustments
    const width = window.innerWidth;
    
    if (width <= 768) {
        // Mobile adjustments
        closeCart(); // Close cart on mobile if screen size changes
    }
}

// Load Products (for initial page load)
function loadProducts() {
    // This would typically load products from an API
    // For now, we're using the products already in the DOM
    console.log(`Loaded ${allProducts.length} products`);
}

// Utility Functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--primary-blue)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .cart-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--light-gray);
    }
    
    .empty-cart {
        text-align: center;
        padding: 2rem;
        color: var(--medium-gray);
    }
    
    .list-view .products-grid {
        grid-template-columns: 1fr;
    }
    
    .list-view .product-card {
        display: flex;
        align-items: center;
    }
    
    .list-view .product-image {
        width: 200px;
        height: 150px;
        flex-shrink: 0;
    }
    
    .list-view .product-info {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
`;
document.head.appendChild(style);

console.log('Elite Cricket Shop Products JavaScript loaded successfully!');