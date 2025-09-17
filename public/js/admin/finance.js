// Finance Management JavaScript

let revenueChart = null;

// Initialize finance charts
function initializeFinanceCharts(monthlyData, revenueCategories) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Prepare data
    const months = Object.keys(monthlyData);
    const revenues = Object.values(monthlyData);
    
    // Create line chart by default
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Revenue (LKR)',
                data: revenues,
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2E7D32',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: LKR ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'LKR ' + (value / 1000) + 'K';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Chart type switcher
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.chart-type-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const chartType = this.getAttribute('data-type');
            updateChartType(chartType, monthlyData, revenueCategories);
        });
    });
}

// Update chart type
function updateChartType(type, monthlyData, revenueCategories) {
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    if (type === 'pie') {
        // Create pie chart for revenue categories
        const categoryLabels = Object.keys(revenueCategories).map(key => 
            key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
        );
        const categoryData = Object.values(revenueCategories).map(cat => cat.amount);
        const categoryColors = ['#2E7D32', '#1976D2', '#F57C00', '#7B1FA2'];
        
        revenueChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: categoryColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const percentage = ((context.parsed / categoryData.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
                                return context.label + ': LKR ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    } else if (type === 'bar') {
        // Create bar chart
        const months = Object.keys(monthlyData);
        const revenues = Object.values(monthlyData);
        
        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Revenue (LKR)',
                    data: revenues,
                    backgroundColor: 'rgba(46, 125, 50, 0.8)',
                    borderColor: '#2E7D32',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: LKR ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + (value / 1000) + 'K';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } else {
        // Create line chart (default)
        const months = Object.keys(monthlyData);
        const revenues = Object.values(monthlyData);
        
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Revenue (LKR)',
                    data: revenues,
                    borderColor: '#2E7D32',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2E7D32',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: LKR ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + (value / 1000) + 'K';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
}

// Initialize filters and search
function initializeFilters() {
    const searchInput = document.getElementById('paymentSearch');
    const typeFilter = document.getElementById('paymentFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        filterPayments();
    });
    
    // Type filter
    typeFilter.addEventListener('change', function() {
        filterPayments();
    });
    
    // Status filter
    statusFilter.addEventListener('change', function() {
        filterPayments();
    });
    
    // Period selector
    const periodSelector = document.getElementById('revenuePeriod');
    periodSelector.addEventListener('change', function() {
        // Here you would normally fetch new data based on the selected period
        console.log('Period changed to:', this.value);
        showNotification('Revenue data updated for ' + this.options[this.selectedIndex].text, 'success');
    });
}

// Filter payments table
function filterPayments() {
    const searchTerm = document.getElementById('paymentSearch').value.toLowerCase();
    const typeFilter = document.getElementById('paymentFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('.payment-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const customerName = row.querySelector('.customer-name').textContent.toLowerCase();
        const paymentId = row.querySelector('.payment-id').textContent.toLowerCase();
        const paymentType = row.getAttribute('data-type');
        const paymentStatus = row.getAttribute('data-status');
        
        const matchesSearch = customerName.includes(searchTerm) || paymentId.includes(searchTerm);
        const matchesType = typeFilter === 'all' || paymentType === typeFilter;
        const matchesStatus = statusFilter === 'all' || paymentStatus === statusFilter;
        
        if (matchesSearch && matchesType && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update pagination info
    const paginationInfo = document.querySelector('.pagination-info');
    if (paginationInfo) {
        paginationInfo.textContent = `Showing 1-${visibleCount} of ${visibleCount} transactions`;
    }
}

// Payment action functions
function viewPayment(paymentId) {
    showNotification(`Viewing details for payment ${paymentId}`, 'info');
    
    // Create and show modal with payment details
    const modal = createPaymentModal(paymentId);
    document.body.appendChild(modal);
    
    // Add close functionality
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('modal-close')) {
            document.body.removeChild(modal);
        }
    });
}

function approvePayment(paymentId) {
    if (confirm(`Are you sure you want to approve payment ${paymentId}?`)) {
        // Find the row and update status
        const row = document.querySelector(`[data-payment-id="${paymentId}"]`);
        if (row) {
            const statusBadge = row.querySelector('.status-badge');
            statusBadge.className = 'status-badge completed';
            statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Completed';
            
            // Remove approve button
            const approveBtn = row.querySelector('.action-btn.approve');
            if (approveBtn) {
                approveBtn.remove();
            }
        }
        
        showNotification(`Payment ${paymentId} has been approved successfully`, 'success');
    }
}

function downloadReceipt(paymentId) {
    showNotification(`Downloading receipt for payment ${paymentId}`, 'info');
    
    // Simulate receipt download
    setTimeout(() => {
        showNotification(`Receipt for payment ${paymentId} downloaded successfully`, 'success');
    }, 1000);
}

// Create payment details modal
function createPaymentModal(paymentId) {
    const modal = document.createElement('div');
    modal.className = 'payment-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Payment Details - ${paymentId}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="payment-details-grid">
                    <div class="detail-group">
                        <label>Payment ID:</label>
                        <span>${paymentId}</span>
                    </div>
                    <div class="detail-group">
                        <label>Type:</label>
                        <span>Equipment Purchase</span>
                    </div>
                    <div class="detail-group">
                        <label>Customer:</label>
                        <span>John Doe</span>
                    </div>
                    <div class="detail-group">
                        <label>Amount:</label>
                        <span>LKR 15,750</span>
                    </div>
                    <div class="detail-group">
                        <label>Date:</label>
                        <span>Sep 16, 2025</span>
                    </div>
                    <div class="detail-group">
                        <label>Payment Method:</label>
                        <span>Credit Card</span>
                    </div>
                    <div class="detail-group">
                        <label>Status:</label>
                        <span class="status-completed">Completed</span>
                    </div>
                    <div class="detail-group">
                        <label>Transaction ID:</label>
                        <span>TXN123456789</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="downloadReceipt('${paymentId}')">
                    <i class="fas fa-download"></i> Download Receipt
                </button>
                <button class="btn btn-secondary modal-close">Close</button>
            </div>
        </div>
    `;
    
    // Add modal styles
    const style = document.createElement('style');
    style.textContent = `
        .payment-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
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
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #333;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .payment-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .detail-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .detail-group label {
            font-weight: 600;
            color: #666;
            font-size: 0.875rem;
        }
        
        .detail-group span {
            color: #333;
            font-size: 1rem;
        }
        
        .status-completed {
            color: #2E7D32;
            font-weight: 600;
        }
        
        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #E0E0E0;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .modal-footer .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .modal-footer .btn-primary {
            background: #2E7D32;
            color: white;
        }
        
        .modal-footer .btn-primary:hover {
            background: #1B5E20;
        }
        
        .modal-footer .btn-secondary {
            background: #F5F5F5;
            color: #333;
        }
        
        .modal-footer .btn-secondary:hover {
            background: #E0E0E0;
        }
    `;
    
    document.head.appendChild(style);
    
    return modal;
}

// Report generation functions
document.addEventListener('DOMContentLoaded', function() {
    const generateReportBtn = document.getElementById('generateReportBtn');
    const exportDataBtn = document.getElementById('exportDataBtn');
    
    generateReportBtn.addEventListener('click', function() {
        showNotification('Generating financial report...', 'info');
        
        setTimeout(() => {
            showNotification('Financial report generated successfully!', 'success');
            // Here you would normally trigger the actual report generation
        }, 2000);
    });
    
    exportDataBtn.addEventListener('click', function() {
        showNotification('Exporting financial data...', 'info');
        
        setTimeout(() => {
            showNotification('Financial data exported to CSV successfully!', 'success');
            // Here you would normally trigger the actual data export
        }, 1500);
    });
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add notification styles
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10001;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 300px;
            max-width: 500px;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid #2E7D32;
        }
        
        .notification-success {
            border-left-color: #2E7D32;
            color: #2E7D32;
        }
        
        .notification-error {
            border-left-color: #D32F2F;
            color: #D32F2F;
        }
        
        .notification-info {
            border-left-color: #1976D2;
            color: #1976D2;
        }
        
        .notification-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            margin-left: auto;
            padding: 0.25rem;
            border-radius: 4px;
        }
        
        .notification-close:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
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
    `;
    
    if (!document.querySelector('style[data-notification-styles]')) {
        style.setAttribute('data-notification-styles', 'true');
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}
