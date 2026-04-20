// Payment Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializePayments();
});

function initializePayments() {
    // Tab functionality
    initializeTabs();
    
    // Chart initialization
    initializePaymentChart();
    
    // Modal functionality
    initializeModals();
    
    // Filter functionality
    initializeFilters();
    
    // Payment actions
    initializePaymentActions();
    
    // Form handling
    initializeFormHandling();
}

// Tab Management
function initializeTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all tabs and content
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            // Special handling for overview tab to refresh chart
            if (targetTab === 'overview') {
                setTimeout(() => {
                    if (window.paymentChart) {
                        window.paymentChart.resize();
                    }
                }, 100);
            }
        });
    });
}

// Chart Initialization
function initializePaymentChart() {
    const ctx = document.getElementById('paymentChart');
    if (!ctx) return;

    // Chart data - injected from server via PHP
    const serverPaymentData = window.paymentData?.chartData || {};
    const chartData = {
        labels: serverPaymentData.labels || [],
        datasets: [
            {
                label: 'Payments Made',
                data: serverPaymentData.payments || [],
                borderColor: 'rgba(74, 222, 128, 1)',
                backgroundColor: 'rgba(74, 222, 128, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Refunds',
                data: serverPaymentData.refunds || [],
                borderColor: 'rgba(6, 182, 212, 1)',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }
        ]
    };

    const chartConfig = {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)',
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 10,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        borderColor: 'rgba(255, 255, 255, 0.2)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        borderColor: 'rgba(255, 255, 255, 0.2)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.8)',
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    };

    window.paymentChart = new Chart(ctx, chartConfig);
    
    // Period selector functionality
    const periodSelect = document.querySelector('.period-select');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            updateChartData(this.value);
        });
    }
}

function updateChartData(period) {
    // Payment period data - injected from server via PHP
    const periodData = window.paymentData?.periodData?.[period] || window.paymentData?.chartData || {};
    const newData = {
        labels: periodData.labels || [],
        datasets: [
            {
                label: 'Payments Made',
                data: periodData.payments || [],
                borderColor: 'rgba(74, 222, 128, 1)',
                backgroundColor: 'rgba(74, 222, 128, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Refunds',
                data: periodData.refunds || [],
                borderColor: 'rgba(6, 182, 212, 1)',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }
        ]
    };
    
    window.paymentChart.data = newData;
    window.paymentChart.update('active');
}

// Modal Management
function initializeModals() {
    const modal = document.getElementById('paymentModal');
    const modalClose = document.querySelector('.modal-close');
    const cancelPayment = document.getElementById('cancelPayment');
    
    // Close modal functionality
    if (modalClose) {
        modalClose.addEventListener('click', closePaymentModal);
    }
    
    if (cancelPayment) {
        cancelPayment.addEventListener('click', closePaymentModal);
    }
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePaymentModal();
            }
        });
    }
    
    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closePaymentModal();
        }
    });
}

function openPaymentModal(item, amount) {
    const modal = document.getElementById('paymentModal');
    const paymentItem = document.getElementById('paymentItem');
    const paymentAmount = document.getElementById('paymentAmount');
    const paymentTotal = document.getElementById('paymentTotal');
    
    if (paymentItem) paymentItem.textContent = item;
    if (paymentAmount) paymentAmount.textContent = amount;
    if (paymentTotal) paymentTotal.textContent = amount;
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus on the first input
        const firstInput = modal.querySelector('input[type="text"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset form
        const form = modal.querySelector('.payment-form');
        if (form) {
            const inputs = form.querySelectorAll('input[type="text"]');
            inputs.forEach(input => input.value = '');
            
            // Reset radio buttons to first option
            const firstRadio = form.querySelector('input[type="radio"]');
            if (firstRadio) firstRadio.checked = true;
        }
    }
}

// Filter Management
function initializeFilters() {
    const appointmentFilter = document.getElementById('appointmentFilter');
    
    if (appointmentFilter) {
        appointmentFilter.addEventListener('change', function() {
            filterAppointments(this.value);
        });
    }
    
    // Year selector for monthly fees
    const yearSelect = document.querySelector('.year-select');
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            filterMonthlyFees(this.value);
        });
    }
    
    // Refunds filter
    const refundsFilter = document.querySelector('.refunds-section .filter-select');
    if (refundsFilter) {
        refundsFilter.addEventListener('change', function() {
            filterRefunds(this.value);
        });
    }
}

function filterAppointments(filterValue) {
    const appointmentCards = document.querySelectorAll('.appointment-card');
    
    appointmentCards.forEach(card => {
        let shouldShow = true;
        
        switch(filterValue) {
            case 'coach':
                shouldShow = card.querySelector('.instructor-type').textContent.toLowerCase().includes('coach');
                break;
            case 'trainer':
                shouldShow = card.querySelector('.instructor-type').textContent.toLowerCase().includes('trainer');
                break;
            case 'paid':
                shouldShow = card.querySelector('.status-badge.success') !== null;
                break;
            case 'pending':
                shouldShow = card.querySelector('.status-badge.pending') !== null;
                break;
            default: // 'all'
                shouldShow = true;
        }
        
        if (shouldShow) {
            card.style.display = 'block';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 10);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
}

function filterMonthlyFees(year) {
    // In a real app, this would fetch data for the selected year
    console.log('Filtering monthly fees for year:', year);
    
    // Show loading state
    const timelineContent = document.querySelector('.timeline-content');
    if (timelineContent) {
        timelineContent.style.opacity = '0.7';
        setTimeout(() => {
            timelineContent.style.opacity = '1';
        }, 500);
    }
}

function filterRefunds(filterValue) {
    const refundCards = document.querySelectorAll('.refund-card');
    
    refundCards.forEach(card => {
        let shouldShow = true;
        
        switch(filterValue) {
            case 'processed':
                shouldShow = card.querySelector('.status-badge.success') !== null;
                break;
            case 'pending':
                shouldShow = card.querySelector('.status-badge.pending') !== null;
                break;
            case 'cancelled':
                shouldShow = card.querySelector('.status-badge.cancelled') !== null;
                break;
            default: // 'all'
                shouldShow = true;
        }
        
        if (shouldShow) {
            card.style.display = 'block';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 10);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
}

// Payment Actions
function initializePaymentActions() {
    // Pay Now buttons
    const payNowButtons = document.querySelectorAll('.btn-pay');
    payNowButtons.forEach(btn => {
        btn.addEventListener('click', handlePayNow);
    });
    
    // Book Session button
    const bookSessionBtn = document.getElementById('bookNewAppointment');
    if (bookSessionBtn) {
        bookSessionBtn.addEventListener('click', function() {
            // In a real app, this would redirect to booking page
            window.location.href = '/player/bookings';
        });
    }
    
    // View All Payments button
    const viewAllBtn = document.getElementById('viewAllPayments');
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function() {
            console.log('View all payments clicked');
            // Expand the payment list or navigate to a detailed view
        });
    }
    
    // Fee Structure button
    const feeStructureBtn = document.getElementById('viewFeeStructure');
    if (feeStructureBtn) {
        feeStructureBtn.addEventListener('click', function() {
            showFeeStructure();
        });
    }
    
    // Download buttons
    const downloadButtons = document.querySelectorAll('.btn-outline');
    downloadButtons.forEach(btn => {
        if (btn.textContent.includes('Download') || btn.textContent.includes('Receipt')) {
            btn.addEventListener('click', handleDownload);
        }
    });
}

function handlePayNow(e) {
    e.preventDefault();

    const button = e.currentTarget || e.target.closest('.btn-pay');
    if (!button) {
        return;
    }

    const payUrl = button.dataset.payUrl || '/player/payhere_checkout';
    const paymentItem = button.dataset.paymentItem || getPaymentItemFromRow(button) || 'Membership Payment';
    const paymentAmount = parseFloat(button.dataset.paymentAmount || '0');

    if (!paymentAmount || paymentAmount <= 0) {
        showNotification('This payment does not have a valid amount.', 'error');
        return;
    }

    const form = document.getElementById('paymentsPayhereForm');
    if (!form) {
        showNotification('Payment form is missing on this page.', 'error');
        return;
    }

    form.action = payUrl;

    const amountInput = form.querySelector('input[name="cart_total"]');
    const itemsInput = form.querySelector('input[name="cart_items"]');

    if (amountInput) amountInput.value = paymentAmount.toFixed(2);
    if (itemsInput) itemsInput.value = JSON.stringify([{ name: paymentItem, quantity: 1 }]);

    form.submit();
}

function getPaymentItemFromRow(button) {
    const row = button.closest('tr');
    if (!row) {
        return '';
    }

    const title = row.querySelector('.table-cell-title');
    if (title && title.textContent.trim()) {
        return title.textContent.trim();
    }

    const cells = row.querySelectorAll('td');
    if (cells.length > 1) {
        return cells[1].textContent.trim().replace(/\s+/g, ' ');
    }

    return '';
}

function handleDownload(e) {
    e.preventDefault();
    
    const buttonText = e.target.textContent || e.target.closest('button').textContent;
    
    // Simulate download
    console.log('Downloading:', buttonText);
    
    // Show success message
    showNotification('Download started successfully!', 'success');
    
    // In a real app, this would trigger an actual download
    // window.open('/download/invoice/123', '_blank');
}

function showFeeStructure() {
    // Fee structure modal is expected to be server-rendered in PHP if used.
    showNotification('Fee structure details are not available on this page.', 'info');
}

// Form Handling
function initializeFormHandling() {
    const confirmPaymentBtn = document.getElementById('confirmPayment');
    if (confirmPaymentBtn) {
        confirmPaymentBtn.addEventListener('click', handlePaymentSubmission);
    }
    
    // Payment method selection
    const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            togglePaymentFields(this.value);
        });
    });
    
    // Card number formatting
    const cardNumberInput = document.querySelector('input[placeholder*="1234"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', formatCardNumber);
    }
    
    // Expiry date formatting
    const expiryInput = document.querySelector('input[placeholder*="MM/YY"]');
    if (expiryInput) {
        expiryInput.addEventListener('input', formatExpiryDate);
    }
    
    // CVV validation
    const cvvInput = document.querySelector('input[placeholder*="123"]');
    if (cvvInput) {
        cvvInput.addEventListener('input', formatCVV);
    }
}

function togglePaymentFields(method) {
    const cardDetails = document.querySelector('.card-details');
    
    if (cardDetails) {
        if (method === 'card') {
            cardDetails.style.display = 'flex';
        } else {
            cardDetails.style.display = 'none';
        }
    }
}

function formatCardNumber(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    e.target.value = value;
}

function formatExpiryDate(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
}

function formatCVV(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value.substring(0, 3);
}

function handlePaymentSubmission(e) {
    e.preventDefault();
    
    // Validate form
    if (!validatePaymentForm()) {
        return;
    }
    
    // Show loading state
    const btn = e.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;
    
    // Simulate payment processing
    setTimeout(() => {
        // Reset button
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Close modal
        closePaymentModal();
        
        // Show success message
        showNotification('Payment processed successfully!', 'success');
        
        // Update UI to reflect payment
        updatePaymentStatus();
        
    }, 2000);
}

function validatePaymentForm() {
    const form = document.querySelector('.payment-form');
    if (!form) return false;
    
    const paymentMethod = form.querySelector('input[name="paymentMethod"]:checked').value;
    
    if (paymentMethod === 'card') {
        const cardNumber = form.querySelector('input[placeholder*="1234"]').value;
        const expiry = form.querySelector('input[placeholder*="MM/YY"]').value;
        const cvv = form.querySelector('input[placeholder*="123"]').value;
        const name = form.querySelector('input[placeholder*="John Doe"]').value;
        
        if (!cardNumber || cardNumber.replace(/\s/g, '').length < 16) {
            showNotification('Please enter a valid card number', 'error');
            return false;
        }
        
        if (!expiry || expiry.length < 5) {
            showNotification('Please enter a valid expiry date', 'error');
            return false;
        }
        
        if (!cvv || cvv.length < 3) {
            showNotification('Please enter a valid CVV', 'error');
            return false;
        }
        
        if (!name.trim()) {
            showNotification('Please enter cardholder name', 'error');
            return false;
        }
    }
    
    return true;
}

function updatePaymentStatus() {
    // Update pending items to paid status
    const pendingItems = document.querySelectorAll('.status-badge.pending');
    pendingItems.forEach(badge => {
        badge.className = 'status-badge success';
        badge.textContent = 'Paid';
    });
    
    // Update amounts in summary cards
    const pendingCard = document.querySelector('.card-icon.pending').closest('.summary-card');
    if (pendingCard) {
        pendingCard.querySelector('.card-value').textContent = '₹0';
        pendingCard.querySelector('.card-change').innerHTML = '<i class="fas fa-check"></i><span>All cleared!</span>';
        pendingCard.querySelector('.card-change').className = 'card-change positive';
    }
}

// Utility Functions
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4ade80' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        z-index: 10001;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    notification.querySelector('.notification-content').style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    // Tab navigation within modal
    if (e.key === 'Tab') {
        const modal = document.querySelector('.modal.active');
        if (modal) {
            const focusableElements = modal.querySelectorAll('input, button, select');
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }
    }
});

// Auto-save form data (in case user accidentally closes modal)
function autoSaveFormData() {
    const form = document.querySelector('.payment-form');
    if (!form) return;
    
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem('payment_form_data', JSON.stringify(data));
}

function restoreFormData() {
    const savedData = localStorage.getItem('payment_form_data');
    if (!savedData) return;
    
    try {
        const data = JSON.parse(savedData);
        const form = document.querySelector('.payment-form');
        
        if (form) {
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = data[key];
                    if (input.type === 'radio' && input.value === data[key]) {
                        input.checked = true;
                    }
                }
            });
        }
    } catch (e) {
        console.error('Error restoring form data:', e);
    }
}

// Clear saved data when payment is successful
function clearSavedFormData() {
    localStorage.removeItem('payment_form_data');
}

// Initialize form restoration on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(restoreFormData, 500);
});

// Save form data periodically
setInterval(autoSaveFormData, 10000); // Save every 10 seconds

// Scroll to Recent Payments section
function scrollToRecentPayments() {
    const recentPaymentsSection = document.getElementById('recent-payments');
    if (recentPaymentsSection) {
        recentPaymentsSection.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        
        // Add a highlight effect
        recentPaymentsSection.style.transform = 'scale(1.02)';
        recentPaymentsSection.style.boxShadow = '0 8px 30px rgba(74, 144, 226, 0.3)';
        recentPaymentsSection.style.transition = 'all 0.3s ease';
        
        setTimeout(() => {
            recentPaymentsSection.style.transform = 'scale(1)';
            recentPaymentsSection.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
        }, 600);
        
        // Show success message
        showNotification('Showing payment history', 'success');
    }
}