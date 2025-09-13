// Shopping & Rental Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Shopping cart functionality
    let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

    // Initialize page
    initializeShoppingPage();

    function initializeShoppingPage() {
        updateCartSummary();
        initializeFilters();
        initializeProductActions();
        initializeRentalActions();
        initializeSizeSelectors();
    }

    // Update cart summary in header
    function updateCartSummary() {
        const cartSummary = document.querySelector('.cart-summary');
        if (cartSummary) {
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            const totalPrice = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            
            cartSummary.innerHTML = `
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">${totalItems}</span>
                <span>₹${totalPrice.toFixed(2)}</span>
            `;
        }
    }

    // Category filter functionality
    function initializeFilters() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const productCards = document.querySelectorAll('.product-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const filterCategory = this.getAttribute('data-category');

                // Filter products
                productCards.forEach(card => {
                    const productCategory = card.getAttribute('data-category');
                    
                    if (filterCategory === 'all' || productCategory === filterCategory) {
                        card.style.display = 'block';
                        // Add animation
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 100);
                    } else {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }

    // Product actions (add to cart, wishlist, etc.)
    function initializeProductActions() {
        // Add to cart buttons
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productId = productCard.getAttribute('data-product-id');
                const productName = productCard.querySelector('.product-title').textContent;
                const productPrice = parseFloat(productCard.querySelector('.current-price').textContent.replace('₹', ''));
                const selectedSize = productCard.querySelector('.size-select').value;
                const productImage = productCard.querySelector('.product-image img').src;

                if (!selectedSize) {
                    showNotification('Please select a size', 'warning');
                    return;
                }

                addToCart({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    size: selectedSize,
                    image: productImage,
                    quantity: 1
                });

                showNotification('Added to cart successfully!', 'success');
            });
        });

        // Rent now buttons
        document.querySelectorAll('.btn-rent').forEach(btn => {
            btn.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productName = productCard.querySelector('.product-title').textContent;
                
                showRentalModal(productCard);
            });
        });

        // Wishlist buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            if (btn.querySelector('.fa-heart')) {
                btn.addEventListener('click', function() {
                    const productCard = this.closest('.product-card');
                    const productId = productCard.getAttribute('data-product-id');
                    
                    toggleWishlist(productId, this);
                });
            }
        });
    }

    // Add item to cart
    function addToCart(product) {
        const existingItem = cart.find(item => 
            item.id === product.id && item.size === product.size
        );

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push(product);
        }

        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        updateCartSummary();
    }

    // Toggle wishlist
    function toggleWishlist(productId, button) {
        const isInWishlist = wishlist.includes(productId);
        
        if (isInWishlist) {
            wishlist = wishlist.filter(id => id !== productId);
            button.classList.remove('active');
            showNotification('Removed from wishlist', 'info');
        } else {
            wishlist.push(productId);
            button.classList.add('active');
            showNotification('Added to wishlist', 'success');
        }
        
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
    }

    // Rental actions
    function initializeRentalActions() {
        document.querySelectorAll('.btn-extend').forEach(btn => {
            btn.addEventListener('click', function() {
                const rentalCard = this.closest('.rental-card');
                const itemName = rentalCard.querySelector('.rental-info h4').textContent;
                
                showExtendRentalModal(itemName);
            });
        });

        document.querySelectorAll('.btn-return').forEach(btn => {
            btn.addEventListener('click', function() {
                const rentalCard = this.closest('.rental-card');
                const itemName = rentalCard.querySelector('.rental-info h4').textContent;
                
                showReturnConfirmation(itemName, rentalCard);
            });
        });
    }

    // Size selector functionality
    function initializeSizeSelectors() {
        document.querySelectorAll('.size-select').forEach(select => {
            select.addEventListener('change', function() {
                const productCard = this.closest('.product-card');
                const selectedSize = this.value;
                
                // Update price based on size if needed
                updatePriceBySize(productCard, selectedSize);
            });
        });
    }

    // Update price based on size
    function updatePriceBySize(productCard, size) {
        const basePrice = parseFloat(productCard.getAttribute('data-base-price'));
        let finalPrice = basePrice;
        
        // Size-based price adjustments
        const sizeMultipliers = {
            'XS': 0.9,
            'S': 0.95,
            'M': 1.0,
            'L': 1.05,
            'XL': 1.1,
            'XXL': 1.15
        };
        
        if (sizeMultipliers[size]) {
            finalPrice = basePrice * sizeMultipliers[size];
        }
        
        const priceElement = productCard.querySelector('.current-price');
        if (priceElement) {
            priceElement.textContent = `₹${finalPrice.toFixed(2)}`;
        }
    }

    // Show rental modal
    function showRentalModal(productCard) {
        const productName = productCard.querySelector('.product-title').textContent;
        const productPrice = productCard.querySelector('.current-price').textContent;
        
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Rent ${productName}</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="rental-options">
                        <div class="option">
                            <input type="radio" id="duration1" name="duration" value="7" checked>
                            <label for="duration1">1 Week - ₹${(parseFloat(productPrice.replace('₹', '')) * 0.3).toFixed(2)}</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="duration2" name="duration" value="14">
                            <label for="duration2">2 Weeks - ₹${(parseFloat(productPrice.replace('₹', '')) * 0.5).toFixed(2)}</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="duration3" name="duration" value="30">
                            <label for="duration3">1 Month - ₹${(parseFloat(productPrice.replace('₹', '')) * 0.8).toFixed(2)}</label>
                        </div>
                    </div>
                    <div class="rental-terms">
                        <p><strong>Rental Terms:</strong></p>
                        <ul>
                            <li>Damage or loss will be charged at full product price</li>
                            <li>Late return fees: ₹50 per day</li>
                            <li>Items must be returned in original condition</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="confirmRental()">Confirm Rental</button>
                    <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Modal close functionality
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    // Show extend rental modal
    function showExtendRentalModal(itemName) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Extend Rental - ${itemName}</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="extension-options">
                        <div class="option">
                            <input type="radio" id="extend1" name="extend" value="7" checked>
                            <label for="extend1">1 Week - ₹150</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="extend2" name="extend" value="14">
                            <label for="extend2">2 Weeks - ₹280</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="confirmExtension()">Extend Rental</button>
                    <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
    }

    // Show return confirmation
    function showReturnConfirmation(itemName, rentalCard) {
        if (confirm(`Are you sure you want to return ${itemName}? Please ensure the item is in good condition.`)) {
            // Animate card removal
            rentalCard.style.transition = 'all 0.3s ease';
            rentalCard.style.opacity = '0';
            rentalCard.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                rentalCard.remove();
                showNotification(`${itemName} return initiated successfully!`, 'success');
            }, 300);
        }
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Hide notification
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Global functions for modal actions
    window.confirmRental = function() {
        const modal = document.querySelector('.modal-overlay');
        const selectedDuration = document.querySelector('input[name="duration"]:checked').value;
        
        showNotification(`Rental confirmed for ${selectedDuration} days!`, 'success');
        document.body.removeChild(modal);
    };

    window.confirmExtension = function() {
        const modal = document.querySelector('.modal-overlay');
        const selectedExtension = document.querySelector('input[name="extend"]:checked').value;
        
        showNotification(`Rental extended for ${selectedExtension} days!`, 'success');
        document.body.removeChild(modal);
    };

    window.closeModal = function() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            document.body.removeChild(modal);
        }
    };

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const productName = card.querySelector('.product-title').textContent.toLowerCase();
                const productCategory = card.querySelector('.product-category').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Initialize wishlist status
    wishlist.forEach(productId => {
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (productCard) {
            const wishlistBtn = productCard.querySelector('.action-btn .fa-heart').parentElement;
            wishlistBtn.classList.add('active');
        }
    });

    // Enhanced Purchase History and Quick Links Functionality
    setupPurchaseHistoryFeatures();
    setupQuickLinksInteractivity();
    setupEnhancedNotifications();
});

/**
 * Setup enhanced purchase history timeline features
 */
function setupPurchaseHistoryFeatures() {
    // Animate timeline items on scroll
    const timelineItems = document.querySelectorAll('.timeline-item');
    
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    }, observerOptions);

    timelineItems.forEach(item => {
        observer.observe(item);
    });

    // Setup order action buttons
    setupOrderActionButtons();
}

/**
 * Setup order action button functionality
 */
function setupOrderActionButtons() {
    const actionButtons = document.querySelectorAll('.btn-action');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const action = this.textContent.trim();
            const orderCard = this.closest('.order-card');
            const orderNumber = orderCard.querySelector('.order-number').textContent;
            
            handleOrderAction(action, orderNumber, this);
        });
    });
}

/**
 * Handle different order actions with enhanced UX
 */
function handleOrderAction(action, orderNumber, buttonElement) {
    // Add loading state with animation
    const originalText = buttonElement.innerHTML;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    buttonElement.disabled = true;
    buttonElement.style.cursor = 'not-allowed';

    // Add subtle loading animation to the order card
    const orderCard = buttonElement.closest('.order-card');
    orderCard.style.opacity = '0.7';
    orderCard.style.transform = 'scale(0.98)';

    // Simulate API call with realistic delay
    setTimeout(() => {
        switch(action.toLowerCase()) {
            case 'view details':
                showEnhancedOrderDetails(orderNumber);
                break;
            case 'download invoice':
                downloadInvoiceWithProgress(orderNumber);
                break;
            case 'reorder':
                reorderItemsWithAnimation(orderNumber);
                break;
            case 'track order':
                trackOrderWithModal(orderNumber);
                break;
            case 'cancel order':
                cancelOrderWithConfirmation(orderNumber, buttonElement);
                break;
            default:
                showEnhancedToast(`Action "${action}" processed successfully`, 'success');
        }

        // Restore button and card state
        buttonElement.innerHTML = originalText;
        buttonElement.disabled = false;
        buttonElement.style.cursor = 'pointer';
        orderCard.style.opacity = '1';
        orderCard.style.transform = 'scale(1)';
    }, Math.random() * 1000 + 1000); // Random delay between 1-2 seconds
}

/**
 * Show enhanced order details with animated modal
 */
function showEnhancedOrderDetails(orderNumber) {
    const modalHTML = `
        <div class="enhanced-modal-overlay" id="orderDetailsModal">
            <div class="enhanced-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-receipt"></i> Order Details</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-content">
                    <div class="order-timeline">
                        <div class="timeline-step completed">
                            <div class="step-icon"><i class="fas fa-shopping-cart"></i></div>
                            <div class="step-content">
                                <h4>Order Placed</h4>
                                <p>September 15, 2025 - 10:30 AM</p>
                            </div>
                        </div>
                        <div class="timeline-step completed">
                            <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="step-content">
                                <h4>Payment Confirmed</h4>
                                <p>September 15, 2025 - 10:32 AM</p>
                            </div>
                        </div>
                        <div class="timeline-step completed">
                            <div class="step-icon"><i class="fas fa-box"></i></div>
                            <div class="step-content">
                                <h4>Order Shipped</h4>
                                <p>September 16, 2025 - 2:15 PM</p>
                            </div>
                        </div>
                        <div class="timeline-step completed">
                            <div class="step-icon"><i class="fas fa-truck"></i></div>
                            <div class="step-content">
                                <h4>Delivered</h4>
                                <p>September 18, 2025 - 11:45 AM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    const modal = document.getElementById('orderDetailsModal');
    const closeBtn = modal.querySelector('.modal-close');
    
    // Animate modal entrance
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modal.querySelector('.enhanced-modal').style.transform = 'scale(1)';
    });
    
    closeBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });
}

/**
 * Close modal with animation
 */
function closeModal(modal) {
    modal.style.opacity = '0';
    modal.querySelector('.enhanced-modal').style.transform = 'scale(0.9)';
    setTimeout(() => modal.remove(), 300);
}

/**
 * Download invoice with progress indicator
 */
function downloadInvoiceWithProgress(orderNumber) {
    const progressToast = showProgressToast(`Preparing invoice for ${orderNumber}...`);
    
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            setTimeout(() => {
                updateProgressToast(progressToast, 'Invoice downloaded successfully!', 'success');
                hideProgressToast(progressToast);
            }, 500);
        }
        updateProgressToast(progressToast, `Downloading... ${Math.round(progress)}%`);
    }, 200);
}

/**
 * Reorder items with cart animation
 */
function reorderItemsWithAnimation(orderNumber) {
    showEnhancedToast(`Adding items from ${orderNumber} to your cart...`, 'info');
    
    // Animate cart icon
    const cartSummary = document.querySelector('.cart-summary');
    if (cartSummary) {
        cartSummary.style.transform = 'scale(1.1)';
        cartSummary.style.backgroundColor = '#22c55e';
        
        setTimeout(() => {
            cartSummary.style.transform = 'scale(1)';
            cartSummary.style.backgroundColor = '';
            
            // Update cart count
            const cartCount = cartSummary.querySelector('.cart-count');
            if (cartCount) {
                const currentCount = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = currentCount + 2;
                cartCount.style.animation = 'bounce 0.6s ease';
            }
            
            showEnhancedToast('Items added to cart successfully!', 'success');
        }, 300);
    }
}

/**
 * Track order with detailed modal
 */
function trackOrderWithModal(orderNumber) {
    showEnhancedToast(`Opening tracking details for ${orderNumber}...`, 'info');
    
    const trackingModal = `
        <div class="enhanced-modal-overlay" id="trackingModal">
            <div class="enhanced-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-map-marker-alt"></i> Order Tracking</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-content">
                    <div class="tracking-info">
                        <div class="tracking-number">
                            <strong>Tracking Number:</strong> ECT${orderNumber.replace('ECA-', '')}789
                        </div>
                        <div class="estimated-delivery">
                            <strong>Estimated Delivery:</strong> September 20, 2025
                        </div>
                        <div class="tracking-map">
                            <div class="map-placeholder">
                                <i class="fas fa-map-marked-alt"></i>
                                <p>Interactive tracking map would appear here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', trackingModal);
    
    const modal = document.getElementById('trackingModal');
    const closeBtn = modal.querySelector('.modal-close');
    
    closeBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });
}

/**
 * Setup interactive features for quick links section
 */
function setupQuickLinksInteractivity() {
    const quickLinkCards = document.querySelectorAll('.quick-link-card');
    
    quickLinkCards.forEach(card => {
        // Add click ripple effect
        card.addEventListener('click', function(e) {
            createRippleEffect(e, this);
            
            // Handle navigation based on card type
            const cardTitle = this.querySelector('h3').textContent.trim();
            handleQuickLinkNavigation(cardTitle);
        });

        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.querySelector('.quick-link-icon').style.transform = 'scale(1.1) rotate(5deg)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.querySelector('.quick-link-icon').style.transform = 'scale(1) rotate(0deg)';
        });
    });
}

/**
 * Handle quick link navigation with enhanced feedback
 */
function handleQuickLinkNavigation(cardTitle) {
    switch(cardTitle) {
        case 'Browse Shop':
            showEnhancedToast('Redirecting to shop...', 'info');
            setTimeout(() => window.location.href = '#shop', 800);
            break;
        case 'Rental Center':
            showEnhancedToast('Opening rental center...', 'info');
            setTimeout(() => window.location.href = '#rentals', 800);
            break;
        case 'My Wishlist':
            showEnhancedToast('Loading your wishlist...', 'info');
            setTimeout(() => window.location.href = '#wishlist', 800);
            break;
        case 'Shopping Support':
            openEnhancedSupportChat();
            break;
        default:
            showEnhancedToast(`Opening ${cardTitle}...`, 'info');
    }
}

/**
 * Open enhanced support chat
 */
function openEnhancedSupportChat() {
    showEnhancedToast('Connecting to support agent...', 'info');
    
    setTimeout(() => {
        const chatModal = `
            <div class="enhanced-modal-overlay" id="supportChatModal">
                <div class="enhanced-modal chat-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-comments"></i> Shopping Support</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-content">
                        <div class="chat-area">
                            <div class="support-message">
                                <div class="message-avatar">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="message-content">
                                    <strong>Support Agent</strong>
                                    <p>Hello! How can I help you with your shopping today?</p>
                                </div>
                            </div>
                        </div>
                        <div class="chat-input-area">
                            <input type="text" placeholder="Type your message..." class="chat-input">
                            <button class="send-btn"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatModal);
        
        const modal = document.getElementById('supportChatModal');
        const closeBtn = modal.querySelector('.modal-close');
        
        closeBtn.addEventListener('click', () => closeModal(modal));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    }, 1000);
}

/**
 * Create enhanced ripple effect
 */
function createRippleEffect(event, element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5) 0%, transparent 70%);
        transform: scale(0);
        animation: ripple 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 1000;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 800);
}

/**
 * Setup enhanced notification system
 */
function setupEnhancedNotifications() {
    // Create notification container if it doesn't exist
    if (!document.querySelector('.notification-container')) {
        const container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
}

/**
 * Show enhanced toast notification
 */
function showEnhancedToast(message, type = 'info', duration = 4000) {
    const toastId = `toast-${Date.now()}`;
    const toastHTML = `
        <div class="enhanced-toast toast-${type}" id="${toastId}">
            <div class="toast-icon">
                <i class="fas fa-${getToastIcon(type)}"></i>
            </div>
            <div class="toast-content">
                <span>${message}</span>
            </div>
            <button class="toast-close">&times;</button>
        </div>
    `;
    
    let toastContainer = document.querySelector('.notification-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'notification-container';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toast = document.getElementById(toastId);
    const closeBtn = toast.querySelector('.toast-close');
    
    // Animate entrance
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });
    
    closeBtn.addEventListener('click', () => removeEnhancedToast(toast));
    
    // Auto remove
    setTimeout(() => removeEnhancedToast(toast), duration);
    
    return toast;
}

/**
 * Show progress toast
 */
function showProgressToast(message) {
    const progressToast = showEnhancedToast(message, 'info', 0);
    progressToast.classList.add('progress-toast');
    
    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar';
    progressBar.innerHTML = '<div class="progress-fill"></div>';
    progressToast.querySelector('.toast-content').appendChild(progressBar);
    
    return progressToast;
}

/**
 * Update progress toast
 */
function updateProgressToast(toast, message, type = 'info') {
    const content = toast.querySelector('.toast-content span');
    content.textContent = message;
    
    if (type === 'success') {
        toast.className = `enhanced-toast toast-success progress-toast`;
    }
}

/**
 * Hide progress toast
 */
function hideProgressToast(toast) {
    setTimeout(() => removeEnhancedToast(toast), 2000);
}

/**
 * Get icon for toast type
 */
function getToastIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle', 
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Remove enhanced toast
 */
function removeEnhancedToast(toast) {
    if (toast && toast.parentNode) {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }
}

// Enhanced CSS for all the new features
const enhancedCSS = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        pointer-events: none;
    }
    
    .enhanced-toast {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.9));
        backdrop-filter: blur(20px);
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 320px;
        max-width: 450px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: auto;
        position: relative;
        overflow: hidden;
    }
    
    .enhanced-toast::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
    }
    
    .toast-success::before { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .toast-error::before { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .toast-warning::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .toast-info::before { background: linear-gradient(90deg, #4A90E2, #5B9BD5); }
    
    .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    
    .toast-success .toast-icon { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .toast-error .toast-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .toast-warning .toast-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .toast-info .toast-icon { background: linear-gradient(135deg, #4A90E2, #5B9BD5); }
    
    .toast-content {
        flex: 1;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.4;
    }
    
    .toast-close {
        background: none;
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        color: #64748b;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    
    .toast-close:hover {
        background: rgba(100, 116, 139, 0.1);
        color: #374151;
    }
    
    .progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(229, 231, 235, 0.8);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.8rem;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #4A90E2, #5B9BD5);
        border-radius: 3px;
        width: 0%;
        transition: width 0.3s ease;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .enhanced-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .enhanced-modal {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.95));
        backdrop-filter: blur(30px);
        border-radius: 24px;
        padding: 2.5rem;
        max-width: 600px;
        width: 90%;
        max-height: 85vh;
        overflow-y: auto;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .enhanced-modal-overlay[style*="opacity: 1"] .enhanced-modal {
        transform: scale(1);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(74, 144, 226, 0.1);
    }
    
    .modal-header h3 {
        color: #2c3e50;
        font-weight: 700;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 2rem;
        cursor: pointer;
        color: #64748b;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    
    .modal-close:hover {
        background: rgba(100, 116, 139, 0.1);
        color: #374151;
    }
    
    .order-timeline {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        position: relative;
    }
    
    .order-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #4A90E2, #22c55e);
    }
    
    .timeline-step {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
    }
    
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4A90E2, #5B9BD5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
    }
    
    .timeline-step.completed .step-icon {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }
    
    .step-content h4 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }
    
    .step-content p {
        color: #64748b;
        font-size: 0.9rem;
    }
    
    .tracking-info {
        text-align: center;
    }
    
    .tracking-number, .estimated-delivery {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: rgba(248, 250, 252, 0.7);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .map-placeholder {
        background: linear-gradient(135deg, rgba(74, 144, 226, 0.1), rgba(34, 197, 94, 0.1));
        border-radius: 16px;
        padding: 3rem;
        border: 2px dashed rgba(74, 144, 226, 0.3);
        color: #64748b;
    }
    
    .map-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #4A90E2;
    }
    
    .chat-modal {
        max-width: 500px;
        height: 600px;
        display: flex;
        flex-direction: column;
    }
    
    .chat-area {
        flex: 1;
        padding: 1rem;
        background: rgba(248, 250, 252, 0.5);
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow-y: auto;
    }
    
    .support-message {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .message-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4A90E2, #5B9BD5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    
    .message-content {
        flex: 1;
    }
    
    .message-content strong {
        color: #2c3e50;
        font-weight: 600;
        display: block;
        margin-bottom: 0.3rem;
    }
    
    .message-content p {
        background: rgba(255, 255, 255, 0.8);
        padding: 0.8rem 1rem;
        border-radius: 12px;
        margin: 0;
        color: #374151;
        line-height: 1.4;
    }
    
    .chat-input-area {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .chat-input {
        flex: 1;
        padding: 1rem;
        border: 2px solid rgba(74, 144, 226, 0.2);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .chat-input:focus {
        outline: none;
        border-color: #4A90E2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }
    
    .send-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4A90E2, #5B9BD5);
        border: none;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
    }
`;

// Inject the enhanced CSS
const enhancedStyleSheet = document.createElement('style');
enhancedStyleSheet.textContent = enhancedCSS;
document.head.appendChild(enhancedStyleSheet);