// Rentals Page JavaScript - Equipment Rental Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeRentalsPage();
});

const rentalFilterState = {
    category: 'all',
    status: 'all',
    search: ''
};

function initializeRentalsPage() {
    console.log('=== INITIALIZING RENTALS PAGE ===');
    
    initRentalFiltering();
    initRentalModals();
    initRentalImageFallbacks();
    initRentalSearch();
    initRentalCart();
    setMinimumDates();
    
    console.log('Rentals page initialization complete');
}

// Rental Filtering Functions
function applyRentalFilters() {
    const rentalsGrid = document.getElementById('rentals-grid');
    if (!rentalsGrid) {
        console.log('Rentals grid not found');
        return;
    }

    const rentalCards = rentalsGrid.querySelectorAll('.product-card');
    const searchTerm = (rentalFilterState.search || '').toLowerCase();

    rentalCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category') || '';
        const cardStatus = card.getAttribute('data-status') || '';

        const nameEl = card.querySelector('h3');
        const descriptionEl = card.querySelector('.card-description');
        const name = nameEl ? nameEl.textContent.toLowerCase() : '';
        const description = descriptionEl ? descriptionEl.textContent.toLowerCase() : '';

        const matchesCategory = rentalFilterState.category === 'all' || cardCategory === rentalFilterState.category;
        const matchesStatus = rentalFilterState.status === 'all' || cardStatus === rentalFilterState.status;
        const matchesSearch = !searchTerm || name.includes(searchTerm) || description.includes(searchTerm);

        if (matchesCategory && matchesStatus && matchesSearch) {
            card.style.display = 'block';
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, 50);
        } else {
            card.style.display = 'none';
        }
    });

    updateRentalNavButtons(rentalFilterState.category);
    updateRentalResultsCount();
}

function setRentalCategory(category) {
    rentalFilterState.category = category || 'all';
    applyRentalFilters();
}

function setRentalStatus(status) {
    rentalFilterState.status = status || 'all';
    applyRentalFilters();
}

function updateRentalNavButtons(activeCategory) {
    const navBtns = document.querySelectorAll('.rental-nav-btn');
    navBtns.forEach(btn => {
        btn.classList.remove('active');

        const btnCategory = btn.dataset.category;
        if (btnCategory === activeCategory) {
            btn.classList.add('active');
        }
    });
}

function updateRentalResultsCount() {
    const rentalsGrid = document.getElementById('rentals-grid');
    if (!rentalsGrid) return;
    
    const visibleCards = rentalsGrid.querySelectorAll('.product-card[style*="display: block"], .product-card:not([style*="display: none"])');
    const count = visibleCards.length;
    
    // Update or create results indicator
    let resultsIndicator = document.getElementById('rental-results-count');
    if (!resultsIndicator) {
        resultsIndicator = document.createElement('div');
        resultsIndicator.id = 'rental-results-count';
        resultsIndicator.style.cssText = `
            text-align: center;
            margin: 1rem 0;
            color: #7f8c8d;
            font-weight: 500;
        `;
    }

    const mainContent = document.querySelector('#rentalsPage .main-content');
    if (mainContent) {
        mainContent.appendChild(resultsIndicator);
    } else {
        rentalsGrid.parentNode.appendChild(resultsIndicator);
    }
    
    const categoryText = rentalFilterState.category === 'all'
        ? 'All Equipment'
        : rentalFilterState.category.charAt(0).toUpperCase() + rentalFilterState.category.slice(1);

    const statusText = rentalFilterState.status === 'all'
        ? ''
        : ` (${rentalFilterState.status.charAt(0).toUpperCase() + rentalFilterState.status.slice(1)})`;

    resultsIndicator.textContent = `${count} ${categoryText}${statusText} items available for rental`;
}

// Rental Modal Functions
function initRentalModals() {
    // Add event listeners for rental buttons
    document.addEventListener('click', function(e) {
        const navButton = e.target.closest('.rental-nav-btn');
        if (navButton) {
            setRentalCategory(navButton.dataset.category || 'all');
            return;
        }

        const rentButton = e.target.closest('.rent-equipment');
        if (rentButton) {
            const rentalData = rentButton.dataset;
            openRentalModal(rentalData);
        }

        const cancelButton = e.target.closest('.js-rental-cancel');
        if (cancelButton) {
            closeRentalModal();
            return;
        }

        const confirmButton = e.target.closest('.js-rental-confirm');
        if (confirmButton) {
            confirmRental();
            return;
        }
        
        if (e.target.classList.contains('modal-close-btn')) {
            closeRentalModal();
        }
        
        if (e.target.id === 'rentalModal') {
            closeRentalModal();
        }
    });
}

function openRentalModal(rentalData) {
    const modal = document.getElementById('rentalModal');
    if (!modal) {
        console.error('Rental modal not found in view markup');
        return;
    }
    
    // Populate rental details
    populateRentalDetails(rentalData);
    populateRentalForm(rentalData);
    
    // Set up price calculation
    setupRentalPriceCalculation(rentalData);
    
    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Add animation
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.transform = 'scale(0.7)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
}

function closeRentalModal() {
    const modal = document.getElementById('rentalModal');
    if (!modal) return;
    
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.transform = 'scale(0.7)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 200);
}

function populateRentalDetails(rentalData) {
    const detailsDiv = document.getElementById('rental-details');
    if (!detailsDiv) return;

    const dailyRate = parseFloat(rentalData.rate || '0');
    
    detailsDiv.innerHTML = `
        <div class="rental-equipment-info">
            <h4>${rentalData.name}</h4>
            <p><strong>Condition:</strong> ${rentalData.condition || 'Excellent'}</p>
            <p><strong>Rate:</strong> Rs. ${Number.isFinite(dailyRate) ? dailyRate.toFixed(2) : '0.00'} / day</p>
        </div>
    `;
}

function populateRentalForm(rentalData) {
    const equipmentIdInput = document.getElementById('rental-equipment-id');
    const cartEquipmentIdInput = document.getElementById('rental-cart-equipment-id');
    const quantitySelect = document.getElementById('rental-quantity');
    const startDateInput = document.getElementById('rental-start-date');

    if (equipmentIdInput) {
        equipmentIdInput.value = rentalData.equipmentId || '';
    }

    if (cartEquipmentIdInput) {
        cartEquipmentIdInput.value = rentalData.equipmentId || '';
    }

    if (quantitySelect) {
        const stock = Math.max(1, parseInt(rentalData.stock || '1', 10) || 1);
        const maxQty = Math.min(stock, 10);
        quantitySelect.innerHTML = '';
        for (let qty = 1; qty <= maxQty; qty++) {
            const option = document.createElement('option');
            option.value = String(qty);
            option.textContent = String(qty);
            quantitySelect.appendChild(option);
        }
    }

    if (startDateInput) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.min = today;
        startDateInput.value = today;
    }
}

function setupRentalPriceCalculation(rentalData) {
    const durationSelect = document.getElementById('rental-duration');
    const quantitySelect = document.getElementById('rental-quantity');
    const pickupSelect = document.getElementById('rental-pickup');
    const totalSpan = document.getElementById('rental-total-amount');
    
    if (!durationSelect || !quantitySelect || !pickupSelect || !totalSpan) return;
    
    function updateTotal() {
        calculateRentalTotal(rentalData);
    }
    
    durationSelect.addEventListener('change', updateTotal);
    quantitySelect.addEventListener('change', updateTotal);
    pickupSelect.addEventListener('change', updateTotal);
    
    // Initial calculation
    updateTotal();
}

function calculateRentalTotal(rentalData) {
    const duration = parseInt(document.getElementById('rental-duration').value);
    const quantity = parseInt(document.getElementById('rental-quantity').value);
    const pickup = document.getElementById('rental-pickup').value;
    const totalSpan = document.getElementById('rental-total-amount');
    
    const dailyRate = parseFloat(rentalData.rate || '0');
    let total = (Number.isFinite(dailyRate) ? dailyRate : 0) * duration * quantity;
    
    // Add delivery fee
    if (pickup === 'delivery') {
        total += 250;
    }
    
    // Apply discounts for longer rentals
    if (duration >= 14) {
        total *= 0.9; // 10% discount for 2+ weeks
    } else if (duration >= 7) {
        total *= 0.95; // 5% discount for 1+ week
    }
    
    totalSpan.textContent = total.toFixed(2);
}

function confirmRental() {
    const form = document.getElementById('rental-form');
    if (!form) return;
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const confirmButton = document.querySelector('.js-rental-confirm');
    if (confirmButton) {
        confirmButton.disabled = true;
        confirmButton.textContent = 'Confirming...';
    }

    form.submit();
}

// Rental Cart Functions
function initRentalCart() {
    bindAddToRentalCartForms();
    updateRentalCartCount();
}

let rentalCartCountState = parseInt((document.getElementById('rentalsPage') || {}).dataset?.cartCount || '0', 10) || 0;

function getUrlRoot() {
    const page = document.getElementById('rentalsPage');
    return page && page.dataset && page.dataset.urlroot ? page.dataset.urlroot : '';
}

function getRentalCartEndpoint(path) {
    const root = getUrlRoot();
    return root ? `${root}/player/${path}` : `/player/${path}`;
}

async function postRentalCartAction(path, data) {
    const response = await fetch(getRentalCartEndpoint(path), {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams(data).toString()
    });

    return response.json();
}

function bindAddToRentalCartForms() {
    document.addEventListener('submit', function (e) {
        const form = e.target && e.target.classList ? e.target : null;
        if (!form || !form.classList.contains('js-add-to-rental-cart-form')) return;

        e.preventDefault();

        const equipmentInput = form.querySelector('input[name="equipment_id"]');
        const equipmentId = equipmentInput ? parseInt(equipmentInput.value || '0', 10) : 0;
        const equipmentName = (form.dataset && form.dataset.name) ? form.dataset.name : 'Equipment';

        if (!equipmentId) return;
        addToRentalCartAjax(equipmentId, equipmentName);
    });
}

async function addToRentalCartAjax(equipmentId, equipmentName) {
    try {
        const result = await postRentalCartAction('addToRentalCart', {
            equipment_id: String(equipmentId)
        });

        if (result && result.success) {
            rentalCartCountState = Number(result.cart_count || rentalCartCountState);
            updateRentalCartCount();
            showRentalCartNotification(equipmentName + ' added to cart!');
            return;
        }

        showRentalCartNotification((result && result.message) ? result.message : 'Failed to add item to cart', true);
    } catch (error) {
        console.error('Failed to add rental equipment to cart', error);
        showRentalCartNotification('Failed to add item to cart', true);
    }
}

async function updateRentalCartCount() {
    const cartCountNodes = document.querySelectorAll('.cart-count, #rental-cart-count');
    if (!cartCountNodes.length) return;

    try {
        const response = await fetch(getRentalCartEndpoint('rentalCartSummary'), { credentials: 'same-origin' });
        const payload = await response.json();
        if (payload && payload.success) {
            rentalCartCountState = Number(payload.cart_count || 0);
        }
    } catch (error) {
        console.error('Failed to load rental cart count', error);
    }

    cartCountNodes.forEach((node) => {
        node.textContent = String(rentalCartCountState);
    });
}

function showRentalCartNotification(message, isError = false) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${isError ? '#e74c3c' : '#27ae60'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Utility Functions
function setMinimumDates() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        input.min = today;
        if (!input.value) {
            input.value = today;
        }
    });
}

function initRentalFiltering() {
    const statusFilter = document.getElementById('rental-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function (e) {
            setRentalStatus(e.target.value || 'all');
        });
    }

    // Initialize with all equipment showing
    setTimeout(() => {
        applyRentalFilters();
    }, 100);
}

function initRentalImageFallbacks() {
    document.querySelectorAll('#rentals-grid img[data-fallback-src]').forEach(function (image) {
        image.addEventListener('error', function handleImageError() {
            if (image.src !== image.dataset.fallbackSrc) {
                image.src = image.dataset.fallbackSrc;
            }
            image.removeEventListener('error', handleImageError);
        });
    });
}

// Search functionality
function initRentalSearch() {
    const searchInput = document.getElementById('rental-search');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        rentalFilterState.search = e.target.value || '';
        applyRentalFilters();
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .product-card {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    .modal-content {
        transition: all 0.2s ease;
    }
`;
document.head.appendChild(style);
