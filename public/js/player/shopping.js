// Shopping Page JavaScript - Navigation and Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeShoppingPage();
    initPlayerShoppingProducts();
});

let currentProduct = null;

function getUrlRoot() {
    const page = document.getElementById('shoppingPage');
    return page && page.dataset && page.dataset.urlroot ? page.dataset.urlroot : '';
}

function initPlayerShoppingProducts() {
    // View Details buttons
    document.querySelectorAll('.js-view-product').forEach(btn => {
        btn.addEventListener('click', () => {
            const productId = btn.getAttribute('data-product-id');
            if (productId) {
                viewProductFromDB(productId);
            }
        });
    });

    // Add-to-cart buttons in product cards
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            const name = button.getAttribute('data-name') || 'Product';
            const price = parseFloat(button.getAttribute('data-price') || '0');
            const imageUrl = button.getAttribute('data-image') || '';

            if (!productId) return;
            addToCartNew(productId, name, price, imageUrl, 1);
        });
    });

    // Modal controls
    document.querySelectorAll('.js-close-product-details').forEach(btn => {
        btn.addEventListener('click', closeProductDetails);
    });
    document.querySelectorAll('.js-qty-decrease').forEach(btn => {
        btn.addEventListener('click', () => adjustQuantity(-1));
    });
    document.querySelectorAll('.js-qty-increase').forEach(btn => {
        btn.addEventListener('click', () => adjustQuantity(1));
    });
    document.querySelectorAll('.js-add-to-cart-details').forEach(btn => {
        btn.addEventListener('click', addToCartFromDetails);
    });
    document.querySelectorAll('.js-buy-now-details').forEach(btn => {
        btn.addEventListener('click', buyNowFromDetails);
    });

    // Close modal when clicking backdrop
    window.addEventListener('click', (event) => {
        if (event.target && event.target.id === 'productDetailsModal') {
            closeProductDetails();
        }
    });

    // Ensure cart count is correct on load
    updateCartCount();
}

function initializeShoppingPage() {
    console.log('=== INITIALIZING SHOPPING PAGE ===');
    
    // Wait for DOM to be ready
    setTimeout(() => {
        initSectionNavigation();
        initShoppingCart();
        initRentalFunctionality();
        initFacilityBooking();
        initFilters();
        setMinimumDates();
        
        // Initialize section visibility
        initializeSectionVisibility();
        
        console.log('Shopping page initialization complete');
    }, 100);
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

// Initialize filters for products, rentals, facilities, and orders
function initFilters() {
    // Product filters
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function () {
            updateProductCategoryNavButtons(this.value);
            filterProducts();
        });
    }
    if (brandFilter) {
        brandFilter.addEventListener('change', filterProducts);
    }
    if (priceFilter) {
        priceFilter.addEventListener('change', filterProducts);
    }

    // Rentals-style category navigation buttons (Shopping page)
    initProductCategoryNavigation();
    
    // Rental filters
    const rentalCategoryFilter = document.getElementById('rental-category-filter');
    if (rentalCategoryFilter) {
        rentalCategoryFilter.addEventListener('change', filterRentals);
    }
    
    // Facility filters
    const facilityTypeFilter = document.getElementById('facility-type-filter');
    if (facilityTypeFilter) {
        facilityTypeFilter.addEventListener('change', filterFacilities);
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

function initProductCategoryNavigation() {
    const nav = document.getElementById('product-category-navigation');
    if (!nav) return;

    const buttons = nav.querySelectorAll('.nav-btn[data-category]');
    if (!buttons.length) return;

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const category = (this.dataset && this.dataset.category) ? this.dataset.category : 'all';
            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter) {
                categoryFilter.value = category;
            }
            updateProductCategoryNavButtons(category);
            filterProducts();
        });
    });

    // Initial sync from dropdown if present
    const categoryFilter = document.getElementById('category-filter');
    const initialCategory = categoryFilter ? categoryFilter.value : 'all';
    updateProductCategoryNavButtons(initialCategory);
}

function updateProductCategoryNavButtons(activeCategory) {
    const nav = document.getElementById('product-category-navigation');
    if (!nav) return;

    const normalized = (activeCategory || 'all').toString();
    const buttons = nav.querySelectorAll('.nav-btn[data-category]');
    let anyMatched = false;

    buttons.forEach(btn => {
        const btnCategory = (btn.dataset && btn.dataset.category) ? btn.dataset.category : '';
        const isActive = (btnCategory === normalized);
        btn.classList.toggle('active', isActive);
        if (isActive) anyMatched = true;
    });

    if (!anyMatched) {
        // Fallback to All
        buttons.forEach(btn => {
            const btnCategory = (btn.dataset && btn.dataset.category) ? btn.dataset.category : '';
            btn.classList.toggle('active', btnCategory === 'all');
        });
    }
}

// Filter products based on category, brand, and price
function filterProducts() {
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');

    const activeCategoryButton = document.querySelector('#product-category-navigation .nav-btn.active[data-category]');
    const selectedCategory = categoryFilter ? categoryFilter.value : (activeCategoryButton ? activeCategoryButton.dataset.category : 'all');
    const selectedBrand = brandFilter ? brandFilter.value : 'all';
    const selectedPrice = priceFilter ? priceFilter.value : 'all';

    const grid = document.getElementById('products-grid');
    const productCards = grid ? grid.querySelectorAll('.product-card') : document.querySelectorAll('.product-card');
    
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
            if (selectedPrice.endsWith('+')) {
                const minPrice = parseFloat(selectedPrice.replace('+', ''));
                if (cardPrice < minPrice) {
                    showCard = false;
                }
            } else {
                const priceRange = selectedPrice.split('-');
                if (priceRange.length === 2) {
                    const minPrice = parseFloat(priceRange[0]);
                    const maxPrice = parseFloat(priceRange[1]);
                    if (cardPrice < minPrice || cardPrice > maxPrice) {
                        showCard = false;
                    }
                }
            }
        }
        
        card.style.display = showCard ? '' : 'none';
    });
}

// Filter rental equipment
function filterRentals() {
    const rentalCategoryFilter = document.getElementById('rental-category-filter');
    const selectedCategory = rentalCategoryFilter ? rentalCategoryFilter.value : 'all';
    
    const rentalCards = document.querySelectorAll('.rental-card');
    
    rentalCards.forEach(card => {
        const cardCategory = card.dataset.category;
        
        if (selectedCategory === 'all' || cardCategory === selectedCategory) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Filter facilities
function filterFacilities() {
    const facilityTypeFilter = document.getElementById('facility-type-filter');
    const selectedType = facilityTypeFilter ? facilityTypeFilter.value : 'all';
    
    const facilityCards = document.querySelectorAll('.facility-card');
    
    facilityCards.forEach(card => {
        const cardType = card.dataset.type;
        
        if (selectedType === 'all' || cardType === selectedType) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
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

// DB-backed product details
function viewProductFromDB(productId) {
    const urlRoot = getUrlRoot();
    if (!urlRoot) {
        console.error('URLROOT not found on page');
        return;
    }

    fetch(`${urlRoot}/shop/getProduct?id=${encodeURIComponent(productId)}`)
        .then(response => response.json())
        .then(data => {
            if (!data || !data.success || !data.product) {
                showCartNotification('Product not found');
                return;
            }

            const product = data.product;
            const imagePath = product.ProductImage
                ? `${urlRoot}/${product.ProductImage}`
                : `https://via.placeholder.com/400x300?text=${encodeURIComponent(product.Name || 'Product')}`;

            currentProduct = {
                ProductID: product.ProductID,
                Name: product.Name,
                Price: parseFloat(product.Price || 0),
                StockQuantity: parseInt(product.StockQuantity || 0),
                ProductImageUrl: imagePath,
                SKU: product.SKU,
                Category: product.Category,
                Brand: product.Brand,
                Description: product.Description,
                Weight: product.Weight,
                Dimensions: product.Dimensions,
                Status: product.Status,
                AddedDate: product.AddedDate,
                UpdatedBy: product.UpdatedBy
            };

            const fullRecord = { ...product };

            // Populate modal
            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            const imageEl = document.getElementById('productDetailImage');
            if (imageEl) {
                imageEl.src = imagePath;
                imageEl.alt = product.Name || 'Product';
            }

            setText('productDetailID', product.ProductID ?? '');
            setText('productDetailName', product.Name || '');
            setText('productDetailSKU', product.SKU || 'N/A');
            setText('productDetailCategory', product.Category || '');
            setText('productDetailBrand', product.Brand || 'N/A');
            setText('productDetailPrice', (parseFloat(product.Price || 0)).toFixed(2));
            setText('productDetailStock', parseInt(product.StockQuantity || 0));
            setText('productDetailDescription', product.Description || 'No description available');
            setText('productDetailWeight', product.Weight ? `${product.Weight} kg` : 'Not specified');
            setText('productDetailDimensions', product.Dimensions || 'Not specified');

            const statusText = product.Status ? String(product.Status) : 'active';
            setText('productDetailStatusText', statusText);

            const addedDate = product.AddedDate ? new Date(product.AddedDate) : null;
            setText('productDetailAddedDate', addedDate && !isNaN(addedDate) ? addedDate.toLocaleDateString() : 'N/A');
            setText('productDetailUpdatedBy', product.UpdatedBy !== null && product.UpdatedBy !== undefined && product.UpdatedBy !== '' ? String(product.UpdatedBy) : 'N/A');

            const statusBadge = document.getElementById('productDetailStatus');
            if (statusBadge) {
                statusBadge.textContent = statusText;
                statusBadge.className = 'status-badge status-' + statusText;
            }

            const quantityInput = document.getElementById('productQuantity');
            if (quantityInput) {
                const stock = parseInt(product.StockQuantity || 0);
                quantityInput.max = stock > 0 ? stock : 1;
                quantityInput.value = 1;
            }

            renderProductRecord(fullRecord);
            showTab('shipping', document.querySelector('#productDetailsModal .tab-btn'));

            const modal = document.getElementById('productDetailsModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        })
        .catch(error => {
            console.error('Failed to load product', error);
            showCartNotification('Failed to load product');
        });
}

function closeProductDetails() {
    const modal = document.getElementById('productDetailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
    document.body.style.overflow = '';
    currentProduct = null;
}

function renderProductRecord(product) {
    const recordGrid = document.getElementById('productRecordGrid');
    if (!recordGrid) return;

    const formatLabel = (key) => String(key)
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, char => char.toUpperCase());

    const formatValue = (value) => {
        if (value === null || value === undefined || value === '') return 'N/A';
        if (typeof value === 'boolean') return value ? 'Yes' : 'No';
        return String(value);
    };

    const entries = Object.entries(product);
    recordGrid.innerHTML = entries.map(([key, value]) => {
        const stringValue = formatValue(value);
        const fullWidth = stringValue.length > 90 || /description/i.test(key);

        return `
            <div class="record-item${fullWidth ? ' full-width' : ''}">
                <span class="record-label">${formatLabel(key)}</span>
                <span class="record-value">${escapeHtml(stringValue)}</span>
            </div>
        `;
    }).join('');
}

function showTab(tabName, button = null) {
    const panes = document.querySelectorAll('#productDetailsModal .tab-pane');
    panes.forEach((pane) => {
        pane.classList.toggle('active', pane.id === `${tabName}-tab`);
    });

    const buttons = document.querySelectorAll('#productDetailsModal .tab-btn');
    buttons.forEach((btn) => btn.classList.remove('active'));

    if (button) {
        button.classList.add('active');
    }
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function adjustQuantity(change) {
    const quantityInput = document.getElementById('productQuantity');
    if (!quantityInput) return;

    let newValue = parseInt(quantityInput.value || '1', 10) + change;
    const maxValue = parseInt(quantityInput.max || '1', 10);

    if (newValue < 1) newValue = 1;
    if (!isNaN(maxValue) && newValue > maxValue) newValue = maxValue;

    quantityInput.value = newValue;
}

function addToCartFromDetails() {
    if (!currentProduct) return;

    const quantityInput = document.getElementById('productQuantity');
    const quantity = quantityInput ? parseInt(quantityInput.value || '1', 10) : 1;
    const qty = !isNaN(quantity) && quantity > 0 ? quantity : 1;

    addToCartNew(
        currentProduct.ProductID,
        currentProduct.Name,
        currentProduct.Price,
        currentProduct.ProductImageUrl,
        qty
    );
}

function buyNowFromDetails() {
    addToCartFromDetails();
    closeProductDetails();

    const urlRoot = getUrlRoot();
    if (urlRoot) {
        window.location.href = `${urlRoot}/player/cart`;
    }
}

function closeCartModal() {
    const modal = document.getElementById('cartModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeRentalModal() {
    const modal = document.getElementById('rentalModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeFacilityModal() {
    const modal = document.getElementById('facilityModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function confirmRental() {
    console.log('Rental confirmed');
    closeRentalModal();
    showNotification('Equipment rental confirmed!', 'success');
}

function confirmBooking() {
    console.log('Facility booking confirmed');
    closeFacilityModal();
    showNotification('Facility booking confirmed!', 'success');
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
function switchToSection(sectionName) {
    console.log('=== MANUAL SECTION SWITCH ===');
    console.log('Switching to section:', sectionName);
    
    const sections = document.querySelectorAll('.shop-section');
    const navBtns = document.querySelectorAll('.nav-btn');
    
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
        console.log('✅ Switched to:', sectionName + '-section');
    } else {
        console.log('❌ Section not found:', sectionName + '-section');
    }
    
    // Update nav buttons
    navBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-section') === sectionName) {
            btn.classList.add('active');
        }
    });
}

// Debug function to test all sections (can be called from console)
function testAllSections() {
    const sections = ['products', 'rentals', 'facilities'];
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

// Renamed to avoid conflict with legacy addToCart calls
function addToCartNew(productId, productName, price, imageUrl, quantity = 1) {
    const id = String(productId);
    const qty = parseInt(quantity || 1, 10);
    const safeQty = !isNaN(qty) && qty > 0 ? qty : 1;

    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += safeQty;
    } else {
        cart.push({
            id: id,
            name: productName,
            price: price,
            image: imageUrl,
            quantity: safeQty
        });
    }
    
    localStorage.setItem('shoppingCart', JSON.stringify(cart));
    updateCartCount();
    showCartNotification(productName + ' added to cart!');
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
}

function showCartNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.textContent = message;
    notification.style.cssText = 'position: fixed; top: 100px; right: 20px; background: #4A90E2; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000; animation: slideIn 0.3s ease;';
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.remove();
    }, 3000);
}

function viewCart() {
    alert('Cart functionality: ' + cart.length + ' items in cart. Full cart view coming soon!');
}

// Rental Functionality
function initRentalFunctionality() {
    const durationSelects = document.querySelectorAll('.rental-duration');
    durationSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            calculateRentalTotal();
        });
    });
}

function rentEquipment(equipmentId, equipmentName, dailyRate) {
    const modal = document.createElement('div');
    modal.className = 'rental-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Rent ${equipmentName}</h3>
                <button class="close-modal" type="button" onclick="this.closest('.rental-modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Daily Rate: $${dailyRate}</p>
                <label for="rentalDays">Rental Duration (days):</label>
                <input type="number" id="rentalDays" min="1" value="1" onchange="updateRentalTotal(${dailyRate})">
                <p>Total: $<span id="rentalTotal">${dailyRate}</span></p>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="confirmRental('${equipmentId}', '${equipmentName}', ${dailyRate})">Confirm Rental</button>
                    <button class="btn btn-secondary" onclick="this.closest('.rental-modal').remove()">Cancel</button>
                </div>
            </div>
        </div>
    `;
    
    modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
    
    document.body.appendChild(modal);
}

function updateRentalTotal(dailyRate) {
    const days = document.getElementById('rentalDays').value;
    const total = dailyRate * days;
    document.getElementById('rentalTotal').textContent = total;
}

function confirmRental(equipmentId, equipmentName, dailyRate) {
    const days = document.getElementById('rentalDays').value;
    const total = dailyRate * days;
    
    addToCartNew('rental_' + equipmentId, equipmentName + ' (' + days + ' days)', total, '/Elite/img/equipment-default.jpg');
    
    document.querySelector('.rental-modal').remove();
}

function calculateRentalTotal() {
    // Calculate total for rental form if it exists
    const startDate = document.getElementById('rentalStartDate');
    const endDate = document.getElementById('rentalEndDate');
    const equipmentSelect = document.getElementById('equipmentSelect');
    
    if (startDate && endDate && equipmentSelect && startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        
        if (days > 0) {
            const dailyRate = parseFloat(equipmentSelect.selectedOptions[0].dataset.price) || 0;
            const total = days * dailyRate;
            
            const totalElement = document.getElementById('rentalTotalAmount');
            if (totalElement) {
                totalElement.textContent = total.toFixed(2);
            }
        }
    }
}

// Return equipment function
function returnEquipment(equipmentId, equipmentName) {
    const confirmed = confirm(`Are you sure you want to return "${equipmentName}"?`);
    if (confirmed) {
        // In a real implementation, this would send a request to the server
        alert(`Return request submitted for "${equipmentName}".\n\nOur staff will contact you shortly to arrange pickup.\n\nThank you for using our rental service!`);
        
        // Optionally remove the rental item from the display
        // In a real implementation, you would refresh the rental list from server
        console.log('Return requested for equipment ID:', equipmentId);
    }
}

// Facility Booking Functionality
function initFacilityBooking() {
    const facilitySelects = document.querySelectorAll('.facility-select');
    facilitySelects.forEach(function(select) {
        select.addEventListener('change', function() {
            calculateFacilityTotal();
        });
    });
}

function bookFacility(facilityId, facilityName, hourlyRate) {
    const modal = document.createElement('div');
    modal.className = 'booking-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Book ${facilityName}</h3>
                <button class="close-modal" type="button" onclick="this.closest('.booking-modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Hourly Rate: $${hourlyRate}</p>
                <label for="bookingDate">Date:</label>
                <input type="date" id="bookingDate" required>
                <label for="bookingHours">Duration (hours):</label>
                <input type="number" id="bookingHours" min="1" value="1" onchange="updateBookingTotal(${hourlyRate})">
                <p>Total: $<span id="bookingTotal">${hourlyRate}</span></p>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="confirmBooking('${facilityId}', '${facilityName}', ${hourlyRate})">Confirm Booking</button>
                    <button class="btn btn-secondary" onclick="this.closest('.booking-modal').remove()">Cancel</button>
                </div>
            </div>
        </div>
    `;
    
    modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
    
    document.body.appendChild(modal);
    
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('bookingDate').min = today;
}

function updateBookingTotal(hourlyRate) {
    const hours = document.getElementById('bookingHours').value;
    const total = hourlyRate * hours;
    document.getElementById('bookingTotal').textContent = total;
}

function confirmBooking(facilityId, facilityName, hourlyRate) {
    const date = document.getElementById('bookingDate').value;
    const hours = document.getElementById('bookingHours').value;
    const total = hourlyRate * hours;
    
    if (!date) {
        alert('Please select a booking date.');
        return;
    }
    
    addToCartNew('booking_' + facilityId, facilityName + ' (' + date + ', ' + hours + 'h)', total, '/Elite/img/facility-default.jpg');
    
    document.querySelector('.booking-modal').remove();
}

function calculateFacilityTotal() {
    const facilitySelect = document.getElementById('facilitySelect');
    const hoursInput = document.getElementById('facilityHours');
    
    if (facilitySelect && hoursInput && facilitySelect.value && hoursInput.value) {
        const hourlyRate = parseFloat(facilitySelect.selectedOptions[0].dataset.price) || 0;
        const hours = parseInt(hoursInput.value) || 0;
        const total = hourlyRate * hours;
        
        const totalElement = document.getElementById('facilityTotalAmount');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
        }
    }
}

function showFacilityBooking() {
    // Navigate to facilities section
    showSection('facilities');
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

// Legacy function for direct product ID calls
function addToCart(productIdOrObject, productName, price, imageUrl) {
    // Handle both new object format and legacy direct calls
    if (typeof productIdOrObject === 'object') {
        const item = productIdOrObject;
        addToCart(item.id, item.name, item.price, item.image);
        return;
    }
    
    // Handle legacy calls with productId as number
    if (typeof productIdOrObject === 'number') {
        const productCards = document.querySelectorAll('.product-card');
        const productCard = Array.from(productCards)[productIdOrObject - 1];
        if (productCard) {
            const productId = 'product_' + productIdOrObject;
            const productName = productCard.querySelector('.product-title').textContent;
            const priceText = productCard.querySelector('.product-price').textContent;
            const price = parseFloat(priceText.replace('$', ''));
            const imageUrl = productCard.querySelector('.product-image img') ? productCard.querySelector('.product-image img').src : '/Elite/img/product-default.jpg';
            
            addToCartInternal(productId, productName, price, imageUrl);
        }
        return;
    }
    
    // Standard call with all parameters
    addToCartInternal(productIdOrObject, productName, price, imageUrl);
}

// Internal function that actually handles cart operations
function addToCartInternal(productId, productName, price, imageUrl) {
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            image: imageUrl,
            quantity: 1
        });
    }
    
    localStorage.setItem('shoppingCart', JSON.stringify(cart));
    updateCartCount();
    showCartNotification(productName + ' added to cart!');
}

// Add CSS for modals and animations
const style = document.createElement('style');
style.textContent = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } .modal-content { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 90%; } .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; } .modal-body label { display: block; margin: 10px 0 5px 0; font-weight: 600; } .modal-body input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; } .modal-actions { display: flex; gap: 10px; margin-top: 20px; } .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; } .close-modal:hover { color: #333; }';
document.head.appendChild(style);

// Facility Filtering Functions
function filterFacilitiesByType(type) {
    console.log('Filtering facilities by type:', type);
    
    const facilitiesGrid = document.getElementById('facilities-grid');
    if (!facilitiesGrid) {
        console.log('Facilities grid not found');
        return;
    }
    
    const facilityCards = facilitiesGrid.querySelectorAll('.facility-card');
    
    facilityCards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        
        if (type === 'all' || cardType === type) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update navigation buttons
    const navBtns = document.querySelectorAll('.shop-navigation .nav-btn');
    navBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.onclick && btn.onclick.toString().includes(type)) {
            btn.classList.add('active');
        }
    });
}

// Facility booking functions
function closeFacilityModal() {
    const modal = document.getElementById('facilityModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function confirmBooking() {
    const form = document.getElementById('facility-form');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Show success message
    alert('Facility booking confirmed! You will receive a confirmation email shortly.');
    closeFacilityModal();
}

// Initialize facility booking when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const dateInput = document.getElementById('booking-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }
    
    // Add event listener for facility booking buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('book-facility')) {
            const facilityData = e.target.dataset;
            openFacilityModal(facilityData);
        }
    });
});

function openFacilityModal(facilityData) {
    const modal = document.getElementById('facilityModal');
    const detailsDiv = document.getElementById('facility-details');
    
    if (!modal || !detailsDiv) return;
    
    // Populate facility details
    detailsDiv.innerHTML = `
        <div class="booking-facility-info">
            <h4>${facilityData.name}</h4>
            <p>Capacity: ${facilityData.capacity || 'Not specified'}</p>
            ${facilityData.hourly ? `<p>Hourly Rate: $${facilityData.hourly}</p>` : ''}
            ${facilityData.half ? `<p>Half Day: $${facilityData.half}</p>` : ''}
            ${facilityData.full ? `<p>Full Day: $${facilityData.full}</p>` : ''}
        </div>
    `;
    
    // Set up price calculation
    const durationSelect = document.getElementById('booking-duration');
    const totalSpan = document.getElementById('booking-total');
    
    if (durationSelect && totalSpan) {
        durationSelect.addEventListener('change', function() {
            updateBookingTotal(facilityData);
        });
        
        // Initial calculation
        updateBookingTotal(facilityData);
    }
    
    modal.style.display = 'flex';
}

function updateBookingTotal(facilityData) {
    const duration = document.getElementById('booking-duration').value;
    const totalSpan = document.getElementById('booking-total');
    
    let total = 0;
    
    if (facilityData.hourly) {
        total = parseFloat(facilityData.hourly) * parseInt(duration);
    } else if (duration === '4' && facilityData.half) {
        total = parseFloat(facilityData.half);
    } else if (duration === '8' && facilityData.full) {
        total = parseFloat(facilityData.full);
    }
    
    totalSpan.textContent = total.toFixed(2);
}
