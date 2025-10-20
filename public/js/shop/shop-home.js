// Elite Cricket Gear Shop - Interactive JavaScript

// ===== GLOBAL VARIABLES =====
let cart = JSON.parse(localStorage.getItem('eliteCricketCart')) || [];
let isCartOpen = false;
let currentTestimonialIndex = 0;
let testimonialInterval;

// ===== DOM CONTENT LOADED =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== ELITE SHOP DEBUG START ===');
    console.log('DOM Content Loaded - Starting initialization');
    
    // Test if we can find the button immediately
    const testButton = document.getElementById('navDropdown');
    console.log('Can we find the button immediately?', testButton);
    
    // List all elements with IDs for debugging
    const allElementsWithIds = document.querySelectorAll('[id]');
    console.log('All elements with IDs:', Array.from(allElementsWithIds).map(el => el.id));
    
    // Test basic button click without our functions
    if (testButton) {
        console.log('Button found! Adding simple test click handler...');
        testButton.addEventListener('click', function() {
            alert('BUTTON CLICKED! Menu should work now.');
            console.log('SIMPLE CLICK TEST WORKED!');
        });
    }
    
    initializeShop();
    setupEventListeners();
    updateCartUI();
    initializeAnimations();
    initializeTestimonialCarousel();
    console.log('Shop initialization complete');
    console.log('=== ELITE SHOP DEBUG END ===');
});

// ===== INITIALIZATION =====
function initializeShop() {
    // Setup sticky header
    setupStickyHeader();
    
    // Setup smooth scrolling
    setupSmoothScrolling();
    
    // Setup intersection observer for animations
    setupScrollReveal();
    
    // Initialize search functionality
    setupSearch();
    
    // Setup mobile menu
    setupMobileMenu();
    
    console.log('Elite Cricket Gear Shop initialized successfully!');
}

// ===== EVENT LISTENERS =====
function setupEventListeners() {
    console.log('Setting up event listeners...');
    
    // Cart functionality
    document.getElementById('cart-btn')?.addEventListener('click', toggleCart);
    document.getElementById('close-cart')?.addEventListener('click', closeCart);
    document.getElementById('cart-overlay')?.addEventListener('click', closeCart);
    
    // Simple Menu Button Test
    const menuButton = document.getElementById('navDropdown');
    console.log('Menu button found:', menuButton);
    
    if (menuButton) {
        // Remove any existing listeners and add a simple one
        menuButton.onclick = function() {
            console.log('MENU BUTTON CLICKED!');
            const dropdown = document.querySelector('.dropdown-menu');
            console.log('Dropdown found:', dropdown);
            
            if (dropdown) {
                // Simple toggle visibility
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                    console.log('Hiding menu');
                } else {
                    dropdown.style.display = 'block';
                    dropdown.style.position = 'absolute';
                    dropdown.style.top = '70px';
                    dropdown.style.left = '0';
                    dropdown.style.background = 'white';
                    dropdown.style.border = '1px solid #ccc';
                    dropdown.style.borderRadius = '8px';
                    dropdown.style.padding = '10px';
                    dropdown.style.zIndex = '9999';
                    dropdown.style.width = '200px';
                    console.log('Showing menu');
                }
            } else {
                console.error('Dropdown menu not found!');
            }
        };
        console.log('Menu button click handler attached');
    } else {
        console.error('Menu button with ID navDropdown not found!');
        // List all buttons for debugging
        const allButtons = document.querySelectorAll('button');
        console.log('All buttons on page:', allButtons);
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const menuButton = document.getElementById('navDropdown');
        const dropdown = document.querySelector('.dropdown-menu');
        
        if (dropdown && menuButton && 
            !menuButton.contains(event.target) && 
            !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
            console.log('Closed menu by clicking outside');
        }
    });
    
    // Category filtering
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', handleCategoryFilter);
    });
    
    // Price range filtering
    const priceSliders = document.querySelectorAll('.price-slider');
    priceSliders.forEach(slider => {
        slider.addEventListener('input', handlePriceFilter);
    });
    
    // Brand and rating filters
    document.querySelectorAll('.filter-checkbox input').forEach(checkbox => {
        checkbox.addEventListener('change', handleFilterChange);
    });
    
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', handleAddToCart);
    });
    
    // Quick view buttons
    document.querySelectorAll('.quick-view-btn').forEach(btn => {
        btn.addEventListener('click', handleQuickView);
    });
    
    // Category cards
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', handleCategoryClick);
    });
    
    // Search functionality
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    
    if (searchInput && searchBtn) {
        searchInput.addEventListener('keypress', handleSearchKeypress);
        searchBtn.addEventListener('click', handleSearch);
    }
    
    // Newsletter form
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', handleNewsletterSubmit);
    }
    
    // Testimonial carousel controls
    const prevBtn = document.getElementById('prev-testimonial');
    const nextBtn = document.getElementById('next-testimonial');
    
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => changeTestimonial(-1));
        nextBtn.addEventListener('click', () => changeTestimonial(1));
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCart();
            closeQuickView();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', handleWindowResize);
}

// ===== HEADER FUNCTIONALITY =====
function setupStickyHeader() {
    const header = document.getElementById('header');
    if (!header) return;
    
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Add scrolled class when scrolling down
        if (scrollTop > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
    });
}

function setupMobileMenu() {
    const mobileToggle = document.getElementById('mobile-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            mobileToggle.classList.toggle('active');
        });
        
        // Close menu when clicking on nav links
        navMenu.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                mobileToggle.classList.remove('active');
            });
        });
    }
}

// ===== SEARCH FUNCTIONALITY =====
function setupSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;
    
    // Add search suggestions functionality
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            showSearchSuggestions(this.value);
        }, 300);
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-box')) {
            hideSearchSuggestions();
        }
    });
}

function handleSearchKeypress(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        handleSearch();
    }
}

function handleSearch() {
    const searchInput = document.getElementById('search-input');
    const searchTerm = searchInput.value.trim();
    
    if (searchTerm) {
        // Redirect to search results page
        window.location.href = `${window.location.origin}/Elite/shop/search?q=${encodeURIComponent(searchTerm)}`;
    }
}

function showSearchSuggestions(query) {
    if (!query || query.length < 2) {
        hideSearchSuggestions();
        return;
    }
    
    // Mock search suggestions - in real app, this would be an API call
    const suggestions = [
        'Cricket Bats',
        'Cricket Balls',
        'Batting Gloves',
        'Cricket Pads',
        'Cricket Helmets',
        'Cricket Shoes'
    ].filter(item => item.toLowerCase().includes(query.toLowerCase()));
    
    if (suggestions.length > 0) {
        displaySearchSuggestions(suggestions);
    } else {
        hideSearchSuggestions();
    }
}

function displaySearchSuggestions(suggestions) {
    let suggestionsContainer = document.getElementById('search-suggestions');
    
    if (!suggestionsContainer) {
        suggestionsContainer = document.createElement('div');
        suggestionsContainer.id = 'search-suggestions';
        suggestionsContainer.className = 'search-suggestions';
        document.querySelector('.search-box').appendChild(suggestionsContainer);
    }
    
    suggestionsContainer.innerHTML = suggestions.map(suggestion => 
        `<div class="suggestion-item" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`
    ).join('');
    
    suggestionsContainer.style.display = 'block';
}

function hideSearchSuggestions() {
    const suggestionsContainer = document.getElementById('search-suggestions');
    if (suggestionsContainer) {
        suggestionsContainer.style.display = 'none';
    }
}

function selectSuggestion(suggestion) {
    const searchInput = document.getElementById('search-input');
    searchInput.value = suggestion;
    hideSearchSuggestions();
    handleSearch();
}

// ===== CART FUNCTIONALITY =====
function toggleCart() {
    if (isCartOpen) {
        closeCart();
    } else {
        openCart();
    }
}

function openCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartOverlay = document.getElementById('cart-overlay');
    
    if (cartSidebar && cartOverlay) {
        cartSidebar.classList.add('open');
        cartOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        isCartOpen = true;
        
        updateCartContent();
    }
}

function closeCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartOverlay = document.getElementById('cart-overlay');
    
    if (cartSidebar && cartOverlay) {
        cartSidebar.classList.remove('open');
        cartOverlay.classList.remove('active');
        document.body.style.overflow = '';
        isCartOpen = false;
    }
}

function handleAddToCart(e) {
    e.preventDefault();
    
    const button = e.currentTarget;
    const productId = button.dataset.productId;
    const productName = button.dataset.productName;
    const productPrice = parseFloat(button.dataset.productPrice);
    
    // Add loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    // Simulate API call delay
    setTimeout(() => {
        addToCart({
            id: productId,
            name: productName,
            price: productPrice,
            image: `product-${productId}.jpg`, // This would come from the product data
            quantity: 1
        });
        
        // Success feedback
        button.innerHTML = '<i class="fas fa-check"></i> Added!';
        button.style.background = 'var(--success)';
        
        // Reset button after delay
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            button.style.background = '';
        }, 2000);
        
        // Show cart animation
        showCartAnimation(button);
        
    }, 500);
}

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += product.quantity;
    } else {
        cart.push(product);
    }
    
    saveCart();
    updateCartUI();
    
    // Show notification
    showNotification(`${product.name} added to cart!`, 'success');
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCart();
    updateCartUI();
    updateCartContent();
    
    showNotification('Item removed from cart', 'info');
}

function updateCartQuantity(productId, newQuantity) {
    const item = cart.find(item => item.id === productId);
    
    if (item) {
        if (newQuantity <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = newQuantity;
            saveCart();
            updateCartUI();
            updateCartContent();
        }
    }
}

function updateCartUI() {
    const cartCount = document.getElementById('cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'block' : 'none';
    }
}

function updateCartContent() {
    const cartContent = document.getElementById('cart-content');
    const cartEmpty = document.getElementById('cart-empty');
    const cartFooter = document.getElementById('cart-footer');
    const cartTotal = document.getElementById('cart-total');
    
    if (!cartContent) return;
    
    if (cart.length === 0) {
        cartEmpty.style.display = 'flex';
        cartFooter.style.display = 'none';
        return;
    }
    
    cartEmpty.style.display = 'none';
    cartFooter.style.display = 'block';
    
    // Generate cart items HTML
    const cartHTML = cart.map(item => `
        <div class="cart-item" data-product-id="${item.id}">
            <div class="cart-item-image">
                <img src="${window.location.origin}/Elite/public/img/products/${item.image}" alt="${item.name}" 
                     onerror="this.src='${window.location.origin}/Elite/public/img/products/placeholder.jpg'">
            </div>
            <div class="cart-item-details">
                <h4 class="cart-item-name">${item.name}</h4>
                <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="updateCartQuantity('${item.id}', ${item.quantity - 1})">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="quantity">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateCartQuantity('${item.id}', ${item.quantity + 1})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <button class="remove-item" onclick="removeFromCart('${item.id}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
    
    cartContent.innerHTML = cartHTML;
    
    // Update total
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    if (cartTotal) {
        cartTotal.textContent = total.toFixed(2);
    }
}

function saveCart() {
    localStorage.setItem('eliteCricketCart', JSON.stringify(cart));
}

function showCartAnimation(button) {
    const cartBtn = document.getElementById('cart-btn');
    const buttonRect = button.getBoundingClientRect();
    const cartRect = cartBtn.getBoundingClientRect();
    
    // Create flying animation element
    const flyingElement = document.createElement('div');
    flyingElement.className = 'cart-animation';
    flyingElement.innerHTML = '<i class="fas fa-shopping-cart"></i>';
    flyingElement.style.cssText = `
        position: fixed;
        left: ${buttonRect.left + buttonRect.width / 2}px;
        top: ${buttonRect.top + buttonRect.height / 2}px;
        z-index: 9999;
        color: var(--primary-color);
        font-size: 20px;
        pointer-events: none;
        transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    `;
    
    document.body.appendChild(flyingElement);
    
    // Animate to cart
    setTimeout(() => {
        flyingElement.style.left = `${cartRect.left + cartRect.width / 2}px`;
        flyingElement.style.top = `${cartRect.top + cartRect.height / 2}px`;
        flyingElement.style.opacity = '0';
        flyingElement.style.transform = 'scale(0.5)';
    }, 50);
    
    // Remove element after animation
    setTimeout(() => {
        document.body.removeChild(flyingElement);
        
        // Add pulse animation to cart button
        cartBtn.classList.add('pulse');
        setTimeout(() => cartBtn.classList.remove('pulse'), 600);
    }, 850);
}

// ===== QUICK VIEW FUNCTIONALITY =====
function handleQuickView(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const productId = e.currentTarget.dataset.productId;
    openQuickView(productId);
}

function openQuickView(productId) {
    const modal = document.getElementById('quick-view-modal');
    const modalContent = document.getElementById('quick-view-content');
    
    if (!modal || !modalContent) return;
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
            <p>Loading product details...</p>
        </div>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Simulate API call to get product details
    setTimeout(() => {
        const productData = getProductData(productId);
        renderQuickView(productData);
    }, 500);
}

function closeQuickView() {
    const modal = document.getElementById('quick-view-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function getProductData(productId) {
    // Mock product data - in real app, this would be an API call
    const products = {
        1: {
            id: 1,
            name: 'Pro Series Cricket Bat',
            brand: 'Elite Pro',
            price: 299.99,
            originalPrice: 349.99,
            rating: 4.8,
            image: 'bat-pro-series.jpg',
            description: 'Professional grade cricket bat made from premium English Willow. Used by international players.',
            features: ['English Willow', 'Professional Grade', 'Balanced Weight', 'Premium Grip'],
            stock: 15
        }
        // Add more products as needed
    };
    
    return products[productId] || products[1]; // Fallback to first product
}

function renderQuickView(product) {
    const modalContent = document.getElementById('quick-view-content');
    
    modalContent.innerHTML = `
        <div class="quick-view-grid">
            <div class="quick-view-image">
                <img src="${window.location.origin}/Elite/public/img/products/${product.image}" 
                     alt="${product.name}"
                     onerror="this.src='${window.location.origin}/Elite/public/img/products/placeholder.jpg'">
            </div>
            <div class="quick-view-details">
                <div class="product-brand">${product.brand}</div>
                <h2 class="product-name">${product.name}</h2>
                <div class="product-rating">
                    ${generateStars(product.rating)}
                    <span class="rating-count">(${Math.floor(Math.random() * 150) + 10})</span>
                </div>
                <div class="product-price">
                    <span class="current-price">$${product.price.toFixed(2)}</span>
                    ${product.originalPrice ? `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>` : ''}
                </div>
                <div class="product-description">
                    <p>${product.description}</p>
                </div>
                <div class="product-features">
                    <h4>Key Features:</h4>
                    <ul>
                        ${product.features.map(feature => `<li>${feature}</li>`).join('')}
                    </ul>
                </div>
                <div class="product-stock ${product.stock <= 5 ? 'low-stock' : ''}">
                    ${product.stock > 0 ? `${product.stock} items in stock` : 'Out of stock'}
                </div>
                <div class="quick-view-actions">
                    <button class="btn btn-primary add-to-cart-btn" 
                            data-product-id="${product.id}"
                            data-product-name="${product.name}"
                            data-product-price="${product.price}"
                            ${product.stock <= 0 ? 'disabled' : ''}>
                        <i class="fas fa-shopping-cart"></i>
                        ${product.stock > 0 ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                    <button class="btn btn-outline" onclick="closeQuickView()">
                        Continue Shopping
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add event listener to the new add to cart button
    const addToCartBtn = modalContent.querySelector('.add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', handleAddToCart);
    }
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fas fa-star ${i <= Math.floor(rating) ? 'filled' : ''}"></i>`;
    }
    return stars;
}

// ===== CATEGORY FUNCTIONALITY =====
function handleCategoryClick(e) {
    const categoryId = e.currentTarget.dataset.categoryId;
    if (categoryId) {
        window.location.href = `${window.location.origin}/Elite/shop/category/${categoryId}`;
    }
}

// ===== TESTIMONIAL CAROUSEL =====
function initializeTestimonialCarousel() {
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    if (testimonialCards.length <= 1) return;
    
    // Start automatic rotation
    startTestimonialRotation();
    
    // Pause on hover
    const carousel = document.querySelector('.testimonials-carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', stopTestimonialRotation);
        carousel.addEventListener('mouseleave', startTestimonialRotation);
    }
}

function changeTestimonial(direction) {
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    const totalTestimonials = testimonialCards.length;
    
    if (totalTestimonials <= 1) return;
    
    currentTestimonialIndex += direction;
    
    if (currentTestimonialIndex >= totalTestimonials) {
        currentTestimonialIndex = 0;
    } else if (currentTestimonialIndex < 0) {
        currentTestimonialIndex = totalTestimonials - 1;
    }
    
    updateTestimonialPosition();
}

function updateTestimonialPosition() {
    const container = document.querySelector('.testimonials-container');
    const cardWidth = 420; // 400px card + 20px gap
    
    if (container) {
        container.style.transform = `translateX(-${currentTestimonialIndex * cardWidth}px)`;
    }
}

function startTestimonialRotation() {
    stopTestimonialRotation();
    testimonialInterval = setInterval(() => {
        changeTestimonial(1);
    }, 5000);
}

function stopTestimonialRotation() {
    if (testimonialInterval) {
        clearInterval(testimonialInterval);
        testimonialInterval = null;
    }
}

// ===== NEWSLETTER FUNCTIONALITY =====
function handleNewsletterSubmit(e) {
    e.preventDefault();
    
    const emailInput = document.getElementById('newsletter-email');
    const email = emailInput.value.trim();
    
    if (!isValidEmail(email)) {
        showNotification('Please enter a valid email address', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Success
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
        emailInput.value = '';
        
        showNotification('Thank you for subscribing to our newsletter!', 'success');
        
        // Reset button
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    }, 1000);
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// ===== SCROLL ANIMATIONS =====
function setupScrollReveal() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    document.querySelectorAll('.product-card, .category-card, .offer-card, .testimonial-card').forEach(el => {
        el.classList.add('reveal');
        observer.observe(el);
    });
}

function initializeAnimations() {
    // Add staggered animations to product cards
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('reveal-up');
    });
    
    // Add staggered animations to category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('reveal-scale');
    });
    
    // Add parallax effect to hero section
    addParallaxEffect();
    
    // Add modern cursor effects
    addCursorEffects();
    
    // Add floating animations
    addFloatingAnimations();
}

// ===== SMOOTH SCROLLING =====
function setupSmoothScrolling() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Global scroll functions
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const headerHeight = 80; // Account for fixed header
        const targetPosition = section.offsetTop - headerHeight;
        
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }
}

// ===== UTILITY FUNCTIONS =====
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        default: return 'info-circle';
    }
}

function handleWindowResize() {
    // Update testimonial carousel on resize
    updateTestimonialPosition();
    
    // Close mobile menu on resize to desktop
    if (window.innerWidth > 768) {
        const navMenu = document.getElementById('nav-menu');
        const mobileToggle = document.getElementById('mobile-toggle');
        
        if (navMenu && mobileToggle) {
            navMenu.classList.remove('active');
            mobileToggle.classList.remove('active');
        }
    }
}

// ===== ERROR HANDLING =====
window.addEventListener('error', function(e) {
    console.error('Elite Cricket Shop Error:', e.error);
    // You could send this to an error tracking service
});

// ===== PERFORMANCE OPTIMIZATIONS =====
// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ===== CLOSE MODAL EVENT LISTENERS =====
document.addEventListener('click', function(e) {
    // Close quick view modal
    if (e.target.id === 'close-quick-view' || e.target.closest('#close-quick-view')) {
        closeQuickView();
    }
    
    // Close quick view when clicking outside
    if (e.target.id === 'quick-view-modal') {
        closeQuickView();
    }
});

// ===== ADDITIONAL CSS CLASSES (Added via JavaScript) =====
const additionalCSS = `
.notification-container {
    position: fixed;
    top: 100px;
    right: 20px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 300px;
    animation: slideInRight 0.3s ease-out;
}

.notification-success { border-left: 4px solid var(--success); }
.notification-error { border-left: 4px solid var(--danger); }
.notification-warning { border-left: 4px solid var(--accent-color); }
.notification-info { border-left: 4px solid var(--primary-color); }

.notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.notification-close {
    background: none;
    border: none;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.notification-close:hover {
    opacity: 1;
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: none;
}

.suggestion-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: var(--light-gray);
}

.suggestion-item:last-child {
    border-bottom: none;
}

.loading-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--medium-gray);
}

.loading-state i {
    margin-bottom: 16px;
}

.quick-view-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.quick-view-image img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 8px;
}

.quick-view-details {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.quick-view-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.quick-view-actions .btn {
    flex: 1;
    justify-content: center;
}

.product-features ul {
    list-style: none;
    padding-left: 0;
}

.product-features li {
    padding: 4px 0;
    padding-left: 20px;
    position: relative;
}

.product-features li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--success);
    font-weight: bold;
}

.product-stock {
    padding: 8px 12px;
    border-radius: 4px;
    background: var(--light-gray);
    font-size: 0.9rem;
}

.product-stock.low-stock {
    background: #fff3cd;
    color: #856404;
}

.cart-item {
    display: flex;
    gap: 12px;
    padding: 16px 0;
    border-bottom: 1px solid var(--light-gray);
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    flex-shrink: 0;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.cart-item-name {
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.3;
}

.cart-item-price {
    font-weight: 600;
    color: var(--primary-color);
}

.cart-item-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.qty-btn {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: var(--transition);
}

.qty-btn:hover {
    background: var(--primary-color);
    color: white;
}

.quantity {
    font-weight: 500;
    min-width: 20px;
    text-align: center;
}

.remove-item {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    flex-shrink: 0;
}

.remove-item:hover {
    background: var(--danger);
    color: white;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Modern Cursor */
.cursor {
    position: fixed;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 50%;
    pointer-events: none;
    z-index: 9999;
    transition: all 0.1s ease;
    opacity: 0.8;
}

.cursor-hover {
    transform: scale(2);
    opacity: 0.6;
}

/* Enhanced Notifications */
.notification-container {
    position: fixed;
    top: 100px;
    right: 20px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notification {
    min-width: 350px;
    padding: 20px;
    border-radius: var(--radius-xl);
    transform: translateX(400px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.notification.notification-show {
    transform: translateX(0);
}

.notification.notification-exit {
    transform: translateX(400px);
    opacity: 0;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.notification-success .notification-icon {
    background: linear-gradient(135deg, var(--success), #16a34a);
    color: white;
}

.notification-error .notification-icon {
    background: linear-gradient(135deg, var(--danger), #dc2626);
    color: white;
}

.notification-warning .notification-icon {
    background: linear-gradient(135deg, var(--warning), #d97706);
    color: white;
}

.notification-info .notification-icon {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
}

.notification-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.notification-title {
    font-weight: var(--font-weight-semibold);
    font-size: 14px;
    color: var(--dark-gray);
}

.notification-message {
    font-size: 13px;
    color: var(--medium-gray);
    line-height: 1.4;
}

.notification-close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: var(--transition);
}

.notification-close:hover {
    background: rgba(0, 0, 0, 0.2);
}

.notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

@media (max-width: 768px) {
    .cursor {
        display: none;
    }
    
    .quick-view-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .notification-container {
        left: 20px;
        right: 20px;
    }
    
    .notification {
        min-width: auto;
        transform: translateY(-100px);
    }
    
    .notification.notification-show {
        transform: translateY(0);
    }
    
    .notification.notification-exit {
        transform: translateY(-100px);
    }
}
`;

// Add the additional CSS to the document
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalCSS;
document.head.appendChild(styleSheet);

// ===== MODERN VISUAL EFFECTS =====
function addParallaxEffect() {
    const hero = document.querySelector('.hero');
    if (!hero) return;
    
    window.addEventListener('scroll', throttle(() => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        if (hero.querySelector('.hero-video')) {
            hero.querySelector('.hero-video').style.transform = `translateY(${rate}px)`;
        }
    }, 10));
}

function addCursorEffects() {
    // Add modern cursor trail effect
    let cursor = document.querySelector('.cursor');
    if (!cursor) {
        cursor = document.createElement('div');
        cursor.className = 'cursor';
        document.body.appendChild(cursor);
    }
    
    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    });
    
    // Add hover effects to interactive elements (excluding menu button)
    document.querySelectorAll('button:not(#navDropdown), .product-card, .category-card, .nav-link').forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursor.classList.add('cursor-hover');
        });
        
        el.addEventListener('mouseleave', () => {
            cursor.classList.remove('cursor-hover');
        });
    });
}

function addFloatingAnimations() {
    // Add floating animation to random elements
    const floatingElements = document.querySelectorAll('.stat-item, .testimonial-card');
    floatingElements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.5}s`;
        el.classList.add('floating-element');
    });
}

// ===== ENHANCED SCROLL EFFECTS =====
function setupScrollReveal() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                
                // Add staggered animations for child elements
                const children = entry.target.querySelectorAll('.product-card, .category-card, .offer-card');
                children.forEach((child, index) => {
                    setTimeout(() => {
                        child.classList.add('revealed');
                    }, index * 100);
                });
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    document.querySelectorAll('.reveal-up, .reveal-scale, .section-modern').forEach(el => {
        observer.observe(el);
    });
}

// ===== MODERN NOTIFICATION SYSTEM =====
function showNotification(message, type = 'info') {
    // Create modern notification element with glassmorphism
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} glass-card`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-${getNotificationIcon(type)}"></i>
            </div>
            <div class="notification-text">
                <span class="notification-title">${getNotificationTitle(type)}</span>
                <span class="notification-message">${message}</span>
            </div>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
        <div class="notification-progress"></div>
    `;
    
    // Add to page with entrance animation
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Trigger entrance animation
    setTimeout(() => {
        notification.classList.add('notification-show');
    }, 10);
    
    // Auto remove with progress bar
    const progressBar = notification.querySelector('.notification-progress');
    progressBar.style.animation = 'progress 5s linear forwards';

    setTimeout(() => {
        notification.classList.remove('notification-show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// ===== NAVIGATION DROPDOWN FUNCTIONALITY =====
function toggleNavDropdown() {
    console.log('toggleNavDropdown called');
    
    const navDropdownContainer = document.querySelector('.left-menu-container .nav-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    const productsSubmenu = document.querySelector('.has-submenu');
    
    console.log('navDropdownContainer found:', navDropdownContainer);
    console.log('dropdownMenu found:', dropdownMenu);
    console.log('productsSubmenu found:', productsSubmenu);
    
    if (navDropdownContainer) {
        const isCurrentlyActive = navDropdownContainer.classList.contains('active');
        navDropdownContainer.classList.toggle('active');
        
        // When opening the menu, also expand the Products submenu automatically
        if (!isCurrentlyActive && productsSubmenu) {
            // Opening the menu - show everything including Products submenu
            productsSubmenu.classList.add('active');
            console.log('Menu opened - automatically expanded Products submenu');
        } else if (isCurrentlyActive && productsSubmenu) {
            // Closing the menu - collapse the Products submenu
            productsSubmenu.classList.remove('active');
            console.log('Menu closed - collapsed Products submenu');
        }
        
        console.log('Toggled dropdown, active:', navDropdownContainer.classList.contains('active'));
    } else {
        console.error('Navigation dropdown container not found');
    }
}

function closeNavDropdown() {
    const navDropdownContainer = document.querySelector('.left-menu-container .nav-dropdown');
    const productsSubmenu = document.querySelector('.has-submenu');
    
    if (navDropdownContainer) {
        navDropdownContainer.classList.remove('active');
    }
    if (productsSubmenu) {
        productsSubmenu.classList.remove('active');
    }
    console.log('Closed dropdown and all submenus');
}

function toggleProductsDropdown() {
    const productsSubmenu = document.querySelector('.has-submenu');
    productsSubmenu.classList.toggle('active');
}

// ===== MODERN SIDEBAR FUNCTIONALITY (Keep for sidebar if needed) =====
function toggleModernSidebar() {
    const sidebar = document.getElementById('modernSidebar');
    const overlay = document.querySelector('.sidebar-overlay') || createSidebarOverlay();
    
    if (sidebar) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        // Prevent body scroll when sidebar is open
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
}

function closeModernSidebar() {
    const sidebar = document.getElementById('modernSidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (sidebar) {
        sidebar.classList.remove('active');
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }
}

// Legacy sidebar functions (kept for compatibility)
function toggleSidebar() {
    toggleModernSidebar();
}

function closeSidebar() {
    closeModernSidebar();
}

function createSidebarOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.addEventListener('click', closeModernSidebar);
    document.body.appendChild(overlay);
    return overlay;
}

// ===== FILTER FUNCTIONALITY =====
function handleCategoryFilter(event) {
    const categoryItem = event.currentTarget;
    const categoryId = categoryItem.dataset.categoryId;
    
    // Toggle active state
    document.querySelectorAll('.category-item').forEach(item => {
        item.classList.remove('active');
    });
    categoryItem.classList.add('active');
    
    // Filter products (placeholder - would connect to backend in real implementation)
    filterProductsByCategory(categoryId);
    
    showNotification(`Filtering by ${categoryItem.querySelector('.category-name').textContent}`, 'info');
}

function handlePriceFilter(event) {
    const slider = event.target;
    const isMin = slider.id === 'priceMin';
    const value = slider.value;
    
    // Update price display
    const priceDisplay = document.getElementById(isMin ? 'minPrice' : 'maxPrice');
    priceDisplay.textContent = value;
    
    // Apply price filter (placeholder)
    const minPrice = document.getElementById('priceMin').value;
    const maxPrice = document.getElementById('priceMax').value;
    
    filterProductsByPrice(minPrice, maxPrice);
}

function handleFilterChange(event) {
    const checkbox = event.target;
    const filterType = checkbox.closest('.sidebar-section').querySelector('h4').textContent.toLowerCase();
    const filterValue = checkbox.value;
    
    if (checkbox.checked) {
        showNotification(`Applied ${filterType} filter: ${filterValue}`, 'success');
    } else {
        showNotification(`Removed ${filterType} filter: ${filterValue}`, 'info');
    }
    
    // Apply filters (placeholder)
    applyFilters();
}

// ===== FILTER LOGIC (Placeholder functions) =====
function filterProductsByCategory(categoryId) {
    // In a real implementation, this would filter products by category
    console.log('Filtering by category:', categoryId);
}

function filterProductsByPrice(minPrice, maxPrice) {
    // In a real implementation, this would filter products by price range
    console.log('Filtering by price range:', minPrice, '-', maxPrice);
}

function applyFilters() {
    // In a real implementation, this would apply all active filters
    console.log('Applying all filters');
}

// ===== ENHANCED PRODUCT INTERACTIONS =====
function addProductHoverEffects() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Initialize enhanced interactions
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(addProductHoverEffects, 100);
});
    
    setTimeout(() => {
        notification.classList.add('notification-exit');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

function getNotificationTitle(type) {
    switch (type) {
        case 'success': return 'Success!';
        case 'error': return 'Error!';
        case 'warning': return 'Warning!';
        default: return 'Info';
    }
}

console.log('Elite Cricket Gear Shop JavaScript loaded successfully with modern effects!');