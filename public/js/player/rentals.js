// Rentals Page JavaScript - Equipment Rental Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeRentalsPage();
});

function initializeRentalsPage() {
    console.log('=== INITIALIZING RENTALS PAGE ===');
    
    initRentalFiltering();
    initRentalModals();
    initRentalCart();
    setMinimumDates();
    
    console.log('Rentals page initialization complete');
}

// Rental Filtering Functions
function filterRentalsByCategory(category) {
    console.log('Filtering rentals by category:', category);
    
    const rentalsGrid = document.getElementById('rentals-grid');
    if (!rentalsGrid) {
        console.log('Rentals grid not found');
        return;
    }
    
    const rentalCards = rentalsGrid.querySelectorAll('.product-card');
    
    rentalCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        
        if (category === 'all' || cardCategory === category) {
            card.style.display = 'block';
            // Add smooth animation
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, 100);
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update navigation buttons
    updateRentalNavButtons(category);
    
    // Update results count
    updateRentalResultsCount(category);
}

function updateRentalNavButtons(activeCategory) {
    const navBtns = document.querySelectorAll('.rental-nav-btn');
    navBtns.forEach(btn => {
        btn.classList.remove('active');
        
        // Check if this button corresponds to the active category
        const btnCategory = btn.getAttribute('onclick')?.match(/filterRentalsByCategory\('([^']+)'\)/)?.[1];
        if (btnCategory === activeCategory) {
            btn.classList.add('active');
        }
    });
}

function updateRentalResultsCount(category) {
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
        rentalsGrid.parentNode.insertBefore(resultsIndicator, rentalsGrid);
    }
    
    const categoryText = category === 'all' ? 'All Equipment' : category.charAt(0).toUpperCase() + category.slice(1);
    resultsIndicator.textContent = `${count} ${categoryText} items available for rental`;
}

// Rental Modal Functions
function initRentalModals() {
    // Add event listeners for rental buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('rent-equipment')) {
            const rentalData = e.target.dataset;
            openRentalModal(rentalData);
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
        createRentalModal();
        return openRentalModal(rentalData);
    }
    
    // Populate rental details
    populateRentalDetails(rentalData);
    
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

function createRentalModal() {
    const modal = document.createElement('div');
    modal.id = 'rentalModal';
    modal.className = 'modal-overlay';
    modal.style.display = 'none';

    modal.innerHTML = `
        <div class="modal-content" style="transition: all 0.2s ease;">
            <div class="modal-header">
                <h3>Equipment Rental</h3>
                <button class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div id="rental-details"></div>
                <form id="rental-form">
                    <div class="form-group">
                        <label for="rental-start-date">Start Date:</label>
                        <input type="date" id="rental-start-date" required>
                    </div>
                    <div class="form-group">
                        <label for="rental-duration">Rental Duration:</label>
                        <select id="rental-duration" required>
                            <option value="1">1 Day</option>
                            <option value="3">3 Days</option>
                            <option value="7">1 Week</option>
                            <option value="14">2 Weeks</option>
                            <option value="30">1 Month</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rental-quantity">Quantity:</label>
                        <select id="rental-quantity" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rental-pickup">Pickup Method:</label>
                        <select id="rental-pickup" required>
                            <option value="pickup">Pickup from Academy</option>
                            <option value="delivery">Home Delivery (+Rs. 5)</option>
                        </select>
                    </div>
                </form>
                <div class="total-display">
                    <strong>Total: Rs. <span id="rental-total-amount">0.00</span></strong>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-modal secondary" onclick="closeRentalModal()">Cancel</button>
                <button class="btn-modal primary" onclick="confirmRental()">Confirm Rental</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
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
        total += 5;
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
        alert('Please fill in all required fields');
        return;
    }
    
    // Get form data
    const startDate = document.getElementById('rental-start-date').value;
    const duration = document.getElementById('rental-duration').value;
    const quantity = document.getElementById('rental-quantity').value;
    const pickup = document.getElementById('rental-pickup').value;
    const total = document.getElementById('rental-total-amount').textContent;
    
    // Show confirmation
    showRentalConfirmation({
        startDate,
        duration,
        quantity,
        pickup,
        total
    });
    
    closeRentalModal();
}

function showRentalConfirmation(rentalDetails) {
    const pickupText = rentalDetails.pickup === 'delivery' ? 'Home Delivery' : 'Academy Pickup';
    const durationText = rentalDetails.duration === '1' ? '1 Day' : 
                        rentalDetails.duration === '7' ? '1 Week' :
                        rentalDetails.duration === '14' ? '2 Weeks' :
                        rentalDetails.duration === '30' ? '1 Month' :
                        `${rentalDetails.duration} Days`;
    
    alert(`Equipment Rental Confirmed!
    
Start Date: ${rentalDetails.startDate}
Duration: ${durationText}
Quantity: ${rentalDetails.quantity}
Pickup: ${pickupText}
Total: Rs. ${rentalDetails.total}

You will receive a confirmation email shortly with pickup/delivery details.`);
}

// Rental Cart Functions
function initRentalCart() {
    updateRentalCartCount();
}

function addToRentalCart(equipmentData) {
    let cart = JSON.parse(localStorage.getItem('rentalCart')) || [];
    
    // Check if equipment already in cart
    const existingItem = cart.find(item => item.id === equipmentData.id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: equipmentData.id,
            name: equipmentData.name,
            rate: equipmentData.rate,
            condition: equipmentData.condition,
            image: equipmentData.image,
            quantity: 1
        });
    }
    
    localStorage.setItem('rentalCart', JSON.stringify(cart));
    updateRentalCartCount();
    showRentalNotification(equipmentData.name + ' added to rental cart!');
}

function updateRentalCartCount() {
    const cart = JSON.parse(localStorage.getItem('rentalCart')) || [];
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const countElement = document.getElementById('rental-cart-count');
    if (countElement) {
        countElement.textContent = count;
        countElement.style.display = count > 0 ? 'block' : 'none';
    }
}

function showRentalNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Utility Functions
function setMinimumDates() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        input.min = today;
    });
}

function initRentalFiltering() {
    // Initialize with all equipment showing
    setTimeout(() => {
        filterRentalsByCategory('all');
    }, 100);
}

// Search functionality
function initRentalSearch() {
    const searchInput = document.getElementById('rental-search');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rentalCards = document.querySelectorAll('.product-card');
        
        rentalCards.forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
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