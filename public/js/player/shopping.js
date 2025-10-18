// Shopping Page JavaScript - Navigation and Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeShoppingPage();
});

function initializeShoppingPage() {
    console.log('=== INITIALIZING SHOPPING PAGE ===');
    
    try {
        // Wait for DOM to be ready
        setTimeout(() => {
            try {
                initSectionNavigation();
                initShoppingCart();
                initFilters();
                setMinimumDates();
                
                // Initialize section visibility
                initializeSectionVisibility();
                
                console.log('Shopping page initialization complete');
            } catch (error) {
                console.error('Error during shopping page initialization:', error);
            }
        }, 100);
    } catch (error) {
        console.error('Critical error initializing shopping page:', error);
    }
}

// Initialize section visibility - show products by default, hide others
function initializeSectionVisibility() {
    const sections = document.querySelectorAll('.shop-section');
    
    console.log('Initializing section visibility for', sections.length, 'sections');
    
    sections.forEach(section => {
        console.log('Found section:', section.id);
        
        if (section.id === 'products-section') {
            section.style.display = 'block';
            section.classList.add('active');
            console.log('Set products-section as active');
        } else {
            section.style.display = 'none';
            section.classList.remove('active');
            console.log('Hidden section:', section.id);
        }
    });
    
    // Also ensure the correct nav button is active
    const navBtns = document.querySelectorAll('.nav-btn');
    navBtns.forEach(btn => {
        if (btn.getAttribute('data-section') === 'products') {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Initialize filters for products and orders
function initFilters() {
    // Product filters
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }
    if (brandFilter) {
        brandFilter.addEventListener('change', filterProducts);
    }
    if (priceFilter) {
        priceFilter.addEventListener('change', filterProducts);
    }
    
    // Order filters
    const orderTypeFilter = document.getElementById('order-type-filter');
    const orderStatusFilter = document.getElementById('order-status-filter');
    
    if (orderTypeFilter) {
        orderTypeFilter.addEventListener('change', filterOrders);
    }
    if (orderStatusFilter) {
        orderStatusFilter.addEventListener('change', filterOrders);
    }
}

// Filter products based on category, brand, and price
function filterProducts() {
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');
    
    const selectedCategory = categoryFilter ? categoryFilter.value : 'all';
    const selectedBrand = brandFilter ? brandFilter.value : 'all';
    const selectedPrice = priceFilter ? priceFilter.value : 'all';
    
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const cardCategory = card.dataset.category;
        const cardBrand = card.dataset.brand;
        const cardPrice = parseFloat(card.dataset.price);
        
        let showCard = true;
        
        // Category filter
        if (selectedCategory !== 'all' && cardCategory !== selectedCategory) {
            showCard = false;
        }
        
        // Brand filter
        if (selectedBrand !== 'all' && cardBrand !== selectedBrand) {
            showCard = false;
        }
        
        // Price filter
        if (selectedPrice !== 'all') {
            const priceRange = selectedPrice.split('-');
            if (priceRange.length === 2) {
                const minPrice = parseFloat(priceRange[0]);
                const maxPrice = parseFloat(priceRange[1]);
                if (cardPrice < minPrice || cardPrice > maxPrice) {
                    showCard = false;
                }
            } else if (selectedPrice === '500+') {
                if (cardPrice < 500) {
                    showCard = false;
                }
            }
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}





// Filter orders
function filterOrders() {
    const orderTypeFilter = document.getElementById('order-type-filter');
    const orderStatusFilter = document.getElementById('order-status-filter');
    
    const selectedType = orderTypeFilter ? orderTypeFilter.value : 'all';
    const selectedStatus = orderStatusFilter ? orderStatusFilter.value : 'all';
    
    const orderItems = document.querySelectorAll('.order-item');
    
    orderItems.forEach(item => {
        const itemType = item.dataset.type;
        const itemStatus = item.dataset.status;
        
        let showItem = true;
        
        if (selectedType !== 'all' && itemType !== selectedType) {
            showItem = false;
        }
        
        if (selectedStatus !== 'all' && itemStatus !== selectedStatus) {
            showItem = false;
        }
        
        item.style.display = showItem ? 'block' : 'none';
    });
}

// Global functions for product viewing and cart operations
function viewProduct(productId) {
    console.log('Viewing product:', productId);
    // You can implement a detailed product view modal here
    alert('Product details will be shown here for: ' + productId);
}

function closeCartModal() {
    const modal = document.getElementById('cartModal');
    if (modal) {
        modal.style.display = 'none';
    }
}



function checkout() {
    console.log('Proceeding to checkout');
    closeCartModal();
    showNotification('Redirecting to checkout...', 'info');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `cart-notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Manual section switcher for testing
function switchSection(section) {
    console.log('Switching to section:', section);
    
    // Hide all sections
    const sections = ['products'];
    sections.forEach(sectionName => {
        const sectionElement = document.getElementById(sectionName);
        if (sectionElement) {
            sectionElement.style.display = 'none';
        }
    });
    
    // Show target section
    const targetElement = document.getElementById(section);
    if (targetElement) {
        targetElement.style.display = 'block';
    }
}

// Debug function to test all sections (can be called from console)
function testAllSections() {
    const sections = ['products'];
    sections.forEach((sectionName, index) => {
        setTimeout(() => {
            console.log('Testing section:', sectionName);
            showSection(sectionName);
        }, index * 2000);
    });
}

// Debug function to check current page state
function debugPageState() {
    const sections = document.querySelectorAll('.shop-section');
    const navBtns = document.querySelectorAll('.nav-btn');
    
    console.log('=== SHOPPING PAGE DEBUG ===');
    console.log('Navigation Buttons:', navBtns.length);
    navBtns.forEach((btn, index) => {
        console.log(`  Button ${index + 1}:`, {
            text: btn.textContent.trim(),
            dataSection: btn.getAttribute('data-section'),
            active: btn.classList.contains('active')
        });
    });
    
    console.log('Sections:', sections.length);
    sections.forEach((section, index) => {
        console.log(`  Section ${index + 1}:`, {
            id: section.id,
            display: section.style.display || 'default',
            active: section.classList.contains('active'),
            visible: section.offsetHeight > 0
        });
    });
    
    console.log('=== END DEBUG ===');
}

// Section Navigation Functionality
function initSectionNavigation() {
    const navBtns = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.shop-section');
    
    console.log('=== NAVIGATION INIT ===');
    console.log('Found navigation buttons:', navBtns.length);
    console.log('Found sections:', sections.length);
    
    // List all sections found
    sections.forEach((section, index) => {
        console.log(`Section ${index}: ID = "${section.id}", display = "${section.style.display}"`);
    });
    
    navBtns.forEach((btn, index) => {
        const dataSection = btn.getAttribute('data-section');
        console.log(`Button ${index}: data-section = "${dataSection}", text = "${btn.textContent.trim()}"`);
        
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            const targetSection = this.getAttribute('data-section');
            
            console.log('=== BUTTON CLICKED ===');
            console.log('Clicked button with data-section:', targetSection);
            console.log('Button text:', this.textContent.trim());
            
            // Skip if no data-section attribute
            if (!targetSection) {
                console.log('Button has no data-section attribute, skipping navigation logic');
                return;
            }
            
            // Remove active class from all nav buttons
            navBtns.forEach(navBtn => {
                navBtn.classList.remove('active');
                console.log('Removed active from:', navBtn.textContent.trim());
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            console.log('Added active to:', this.textContent.trim());
            
            // Hide all sections
            sections.forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
                console.log('Hidden section:', section.id);
            });
            
            // Show target section
            const targetSectionElement = document.getElementById(targetSection + '-section');
            console.log('Looking for section with ID:', targetSection + '-section');
            
            if (targetSectionElement) {
                targetSectionElement.style.display = 'block';
                targetSectionElement.classList.add('active');
                
                console.log('✅ Successfully showing section:', targetSection + '-section');
                console.log('Section display style:', targetSectionElement.style.display);
                
                // Smooth scroll to section
                targetSectionElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                console.log('Target section not found:', targetSection + '-section');
            }
        });
    });
}

// Shopping Cart Functionality
let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];

function initShoppingCart() {
    updateCartCount();
}

// Main cart function - handles all cart additions
function addToCart(productId, productName, price, imageUrl) {
    try {
        // Validate input parameters
        if (!productId || !productName || !price) {
            console.error('Missing required parameters for addToCart');
            return false;
        }
        
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: parseFloat(price),
                image: imageUrl || '/Elite/img/product-default.jpg',
                quantity: 1
            });
        }
        
        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        updateCartCount();
        showCartNotification(productName + ' added to cart!');
        return true;
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Error adding item to cart', 'error');
        return false;
    }
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
}

function showCartNotification(message) {
    showNotification(message, 'success');
}

function viewCart() {
    alert('Cart functionality: ' + cart.length + ' items in cart. Full cart view coming soon!');
}















// Utility function to show a specific section
function showSection(sectionName) {
    const navBtns = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.shop-section');
    
    // Remove active class from all nav buttons
    navBtns.forEach(navBtn => navBtn.classList.remove('active'));
    
    // Add active class to target button
    const targetBtn = document.querySelector(`[data-section="${sectionName}"]`);
    if (targetBtn) {
        targetBtn.classList.add('active');
    }
    
    // Hide all sections
    sections.forEach(section => {
        section.style.display = 'none';
        section.classList.remove('active');
    });
    
    // Show target section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.style.display = 'block';
        targetSection.classList.add('active');
        
        // Smooth scroll to section
        targetSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        
        console.log('Showing section:', sectionName);
    } else {
        console.log('Section not found:', sectionName);
    }
}

// Set minimum dates for all date inputs
function setMinimumDates() {
    const today = new Date().toISOString().split('T')[0];
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(function(input) {
        input.min = today;
    });
}

// Product action functions
function addProductToCart(button) {
    const productCard = button.closest('.product-card');
    const productId = productCard.dataset.productId || Math.random().toString(36).substr(2, 9);
    const productName = productCard.querySelector('.product-title').textContent;
    const priceText = productCard.querySelector('.product-price').textContent;
    const price = parseFloat(priceText.replace('$', ''));
    const imageUrl = productCard.querySelector('.product-image img') ? productCard.querySelector('.product-image img').src : '/Elite/img/product-default.jpg';
    
    addToCart(productId, productName, price, imageUrl);
}

// View product details function
function viewProductDetails(productId) {
    // In a real implementation, this would show a detailed product modal
    const productCards = document.querySelectorAll('.product-card');
    const productCard = Array.from(productCards)[productId - 1];
    
    if (productCard) {
        const productName = productCard.querySelector('.product-title').textContent;
        const productDescription = productCard.querySelector('.product-description').textContent;
        const priceText = productCard.querySelector('.product-price').textContent;
        const rating = productCard.querySelector('.rating-text').textContent;
        const features = Array.from(productCard.querySelectorAll('.feature-tag')).map(tag => tag.textContent);
        
        alert(`Product Details:\n\nName: ${productName}\nDescription: ${productDescription}\nPrice: ${priceText}\nRating: ${rating}\nFeatures: ${features.join(', ')}\n\nFull product details modal coming soon!`);
    } else {
        alert('Product details not found.');
    }
}

// Helper function for legacy product ID calls
function addProductToCart(productNumber) {
    try {
        const productCards = document.querySelectorAll('.product-card');
        const productCard = Array.from(productCards)[productNumber - 1];
        
        if (!productCard) {
            console.error('Product card not found for number:', productNumber);
            return false;
        }
        
        const productId = 'product_' + productNumber;
        const titleElement = productCard.querySelector('.product-title');
        const priceElement = productCard.querySelector('.product-price');
        const imageElement = productCard.querySelector('.product-image img');
        
        if (!titleElement || !priceElement) {
            console.error('Required product elements not found');
            return false;
        }
        
        const productName = titleElement.textContent;
        const priceText = priceElement.textContent;
        const price = parseFloat(priceText.replace(/[$,]/g, ''));
        const imageUrl = imageElement ? imageElement.src : '/Elite/img/product-default.jpg';
        
        return addToCart(productId, productName, price, imageUrl);
    } catch (error) {
        console.error('Error adding product to cart:', error);
        return false;
    }
}

// Add CSS for modals and animations
const style = document.createElement('style');
style.textContent = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } .modal-content { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 90%; } .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; } .modal-body label { display: block; margin: 10px 0 5px 0; font-weight: 600; } .modal-body input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; } .modal-actions { display: flex; gap: 10px; margin-top: 20px; } .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; } .close-modal:hover { color: #333; }';
document.head.appendChild(style);

// End of shopping.js - focused on products and orders only
