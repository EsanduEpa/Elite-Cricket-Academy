<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop.css">

<div class="admin-layout">
    <!-- Shop Sidebar -->
    <div class="admin-sidebar" id="shopSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-store"></i>
                <h3>Shop Manager</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/shop/orders" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Order Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/products" class="nav-link">
                        <i class="fas fa-box"></i>
                        <span>Product Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/inventory" class="nav-link">
                        <i class="fas fa-warehouse"></i>
                        <span>Inventory</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="nav-link">
                        <i class="fas fa-tools"></i>
                        <span>Equipment Rentals</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/reviews" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span>Reviews & Feedback</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/prescriptions" class="nav-link">
                        <i class="fas fa-prescription-bottle"></i>
                        <span>Prescriptions</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/analytics" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Sales Analytics</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Facility Management</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <h1><i class="fas fa-shopping-cart"></i> Order Management</h1>
            <p>Process orders, manage payments, and track deliveries</p>
        </div>

        <!-- Order Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <div class="card-content">
                    <h3>Total Orders</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">156</span>
                            <span class="label">All Time</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3>Pending Orders</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent">8</span>
                            <span class="label">Need Processing</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45d, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="card-content">
                    <h3>In Transit</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">15</span>
                            <span class="label">Being Delivered</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #6B73FF, #8B83FF);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-content">
                    <h3>Completed</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">133</span>
                            <span class="label">This Month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Filters -->
        <div class="filter-section">
            <div class="filter-tabs">
                <a href="<?php echo URLROOT; ?>/shop/orders" class="filter-tab <?php echo ($data['current_status'] == 'all') ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> All Orders
                </a>
                <a href="<?php echo URLROOT; ?>/shop/orders/pending" class="filter-tab <?php echo ($data['current_status'] == 'pending') ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="<?php echo URLROOT; ?>/shop/orders/processing" class="filter-tab <?php echo ($data['current_status'] == 'processing') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Processing
                </a>
                <a href="<?php echo URLROOT; ?>/shop/orders/completed" class="filter-tab <?php echo ($data['current_status'] == 'completed') ? 'active' : ''; ?>">
                    <i class="fas fa-check"></i> Completed
                </a>
                <a href="<?php echo URLROOT; ?>/shop/orders/cancelled" class="filter-tab <?php echo ($data['current_status'] == 'cancelled') ? 'active' : ''; ?>">
                    <i class="fas fa-times"></i> Cancelled
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Orders List</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search orders..." id="orderSearch">
                    <select class="filter-dropdown" id="paymentFilter">
                        <option value="all">All Payment Methods</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-content">
                <table id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-2025-156</td>
                            <td>
                                <div>
                                    <strong>John Smith</strong><br>
                                    <small>john@example.com</small>
                                </div>
                            </td>
                            <td>Oct 18, 2025<br><small>10:30 AM</small></td>
                            <td>3 items</td>
                            <td>₨ 12,500</td>
                            <td>
                                <span class="status-badge status-card">Card</span>
                            </td>
                            <td>
                                <select class="status-dropdown" onchange="updateOrderStatus(156, this.value)">
                                    <option value="pending" selected>Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewOrder(156)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="printInvoice(156)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="processPayment(156)">
                                        <i class="fas fa-credit-card"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-2025-155</td>
                            <td>
                                <div>
                                    <strong>Sarah Johnson</strong><br>
                                    <small>sarah@example.com</small>
                                </div>
                            </td>
                            <td>Oct 17, 2025<br><small>2:15 PM</small></td>
                            <td>2 items</td>
                            <td>₨ 8,200</td>
                            <td>
                                <span class="status-badge status-cash">Cash</span>
                            </td>
                            <td>
                                <select class="status-dropdown" onchange="updateOrderStatus(155, this.value)">
                                    <option value="pending">Pending</option>
                                    <option value="processing" selected>Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewOrder(155)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="printInvoice(155)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="trackDelivery(155)">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-2025-154</td>
                            <td>
                                <div>
                                    <strong>Mike Wilson</strong><br>
                                    <small>mike@example.com</small>
                                </div>
                            </td>
                            <td>Oct 16, 2025<br><small>4:45 PM</small></td>
                            <td>1 item</td>
                            <td>₨ 4,500</td>
                            <td>
                                <span class="status-badge status-online">Online</span>
                            </td>
                            <td>
                                <select class="status-dropdown" onchange="updateOrderStatus(154, this.value)">
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed" selected>Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewOrder(154)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="printInvoice(154)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn-small btn-info" onclick="requestReview(154)">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Details Modal -->
        <div id="orderModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-shopping-cart"></i> Order Details</h3>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <div class="modal-body" id="orderDetails">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button class="btn btn-primary" onclick="processCurrentOrder()">Process Order</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript -->
<script>
// Filter tabs functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        // Filter orders based on status
        filterOrdersByStatus(this.href.split('/').pop());
    });
});

// Search functionality
document.getElementById('orderSearch').addEventListener('input', function() {
    filterOrders(this.value);
});

// Payment filter
document.getElementById('paymentFilter').addEventListener('change', function() {
    filterOrdersByPayment(this.value);
});

function filterOrdersByStatus(status) {
    console.log('Filtering by status:', status);
    // Implementation would filter the table rows
}

function filterOrders(searchTerm) {
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function filterOrdersByPayment(payment) {
    console.log('Filtering by payment:', payment);
    // Implementation would filter the table rows
}

function updateOrderStatus(orderId, status) {
    console.log(`Updating order ${orderId} to status: ${status}`);
    // Implementation would send AJAX request to update status
    showNotification(`Order #${orderId} status updated to ${status}`, 'success');
}

function viewOrder(orderId) {
    console.log('Viewing order:', orderId);
    // Load order details
    document.getElementById('orderDetails').innerHTML = `
        <div class="order-info">
            <h4>Order #ORD-2025-${orderId}</h4>
            <div class="order-grid">
                <div class="order-section">
                    <h5>Customer Information</h5>
                    <p><strong>Name:</strong> John Smith</p>
                    <p><strong>Email:</strong> john@example.com</p>
                    <p><strong>Phone:</strong> +94771234567</p>
                    <p><strong>Address:</strong> 123 Main St, Colombo</p>
                </div>
                <div class="order-section">
                    <h5>Order Items</h5>
                    <div class="order-items">
                        <div class="order-item">
                            <span>Cricket Bat Pro × 1</span>
                            <span>₨ 8,500</span>
                        </div>
                        <div class="order-item">
                            <span>Batting Gloves × 1</span>
                            <span>₨ 2,500</span>
                        </div>
                        <div class="order-item">
                            <span>Helmet Elite × 1</span>
                            <span>₨ 4,200</span>
                        </div>
                        <div class="order-total">
                            <span><strong>Total: ₨ 15,200</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('orderModal').style.display = 'block';
}

function printInvoice(orderId) {
    console.log('Printing invoice for order:', orderId);
    window.print();
}

function processPayment(orderId) {
    console.log('Processing payment for order:', orderId);
    showNotification(`Payment processed for order #${orderId}`, 'success');
}

function trackDelivery(orderId) {
    console.log('Tracking delivery for order:', orderId);
    showNotification(`Tracking information sent for order #${orderId}`, 'info');
}

function requestReview(orderId) {
    console.log('Requesting review for order:', orderId);
    showNotification(`Review request sent for order #${orderId}`, 'info');
}

function exportOrders() {
    console.log('Exporting orders');
    showNotification('Orders exported successfully', 'success');
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function processCurrentOrder() {
    console.log('Processing current order');
    showNotification('Order processed successfully', 'success');
    closeModal();
}

function showNotification(message, type) {
    // Simple notification system
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4ECDC4' : type === 'error' ? '#FF6B6B' : '#4A90E2'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<style>
.filter-section {
    margin: 2rem 0;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.25);
    padding: 0.5rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.filter-tab {
    flex: 1;
    padding: 12px 20px;
    text-decoration: none;
    color: #666;
    background: transparent;
    border-radius: 10px;
    text-align: center;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.filter-tab:hover {
    background: rgba(74, 144, 226, 0.1);
    color: #4A90E2;
}

.filter-tab.active {
    background: #4A90E2;
    color: white;
}

.status-dropdown {
    padding: 6px 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.5);
    outline: none;
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    margin: 5% auto;
    padding: 0;
    border-radius: 15px;
    width: 80%;
    max-width: 800px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.close {
    color: #999;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #333;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.order-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 1rem;
}

.order-section h5 {
    color: #4A90E2;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.order-items {
    background: rgba(74, 144, 226, 0.1);
    padding: 1rem;
    border-radius: 10px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.order-item:last-child {
    border-bottom: none;
}

.order-total {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #4A90E2;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@media (max-width: 768px) {
    .filter-tabs {
        flex-wrap: wrap;
    }
    
    .order-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 2% auto;
    }
}
</style>

<script src="<?php echo URLROOT; ?>/js/admin/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>