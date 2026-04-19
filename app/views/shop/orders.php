<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-orders.css">

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
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Facility Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/counter" class="nav-link">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Counter Booking</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Simple Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['user_name']) ? $data['user_name'] : 'Shop Manager'; ?></div>
            <div class="profile-role">Shop Employee</div>
            <a href="<?php echo URLROOT; ?>/shop/profile" class="action-btn" style="margin-top: 10px;">
                <i class="fas fa-user-cog"></i> Profile
            </a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

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
                            <span class="number"><?php echo $data['stats']['total']; ?></span>
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
                            <span class="number urgent"><?php echo $data['stats']['pending']; ?></span>
                            <span class="label">Need Processing</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="card-content">
                    <h3>Processing</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo $data['stats']['processing']; ?></span>
                            <span class="label">Being Processed</span>
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
                            <span class="number"><?php echo $data['stats']['completed']; ?></span>
                            <span class="label">Orders Done</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="data-table">
            <div class="table-header">
                <div class="table-header-main">
                    <h3><i class="fas fa-list"></i> Orders List</h3>
                </div>
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
                <div class="filter-section table-filters">
                    <div class="filter-tabs">
                        <a href="<?php echo URLROOT; ?>/shop/orders/all" class="filter-tab <?php echo $data['current_status'] === 'all' ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i> All Orders
                        </a>
                        <a href="<?php echo URLROOT; ?>/shop/orders/pending" class="filter-tab <?php echo $data['current_status'] === 'pending' ? 'active' : ''; ?>">
                            <i class="fas fa-clock"></i> Pending
                        </a>
                        <a href="<?php echo URLROOT; ?>/shop/orders/processing" class="filter-tab <?php echo $data['current_status'] === 'processing' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i> Processing
                        </a>
                        <a href="<?php echo URLROOT; ?>/shop/orders/completed" class="filter-tab <?php echo $data['current_status'] === 'completed' ? 'active' : ''; ?>">
                            <i class="fas fa-check"></i> Completed
                        </a>
                        <a href="<?php echo URLROOT; ?>/shop/orders/cancelled" class="filter-tab <?php echo $data['current_status'] === 'cancelled' ? 'active' : ''; ?>">
                            <i class="fas fa-times"></i> Cancelled
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="table-content slot-style-table-wrap" style="overflow-x: auto;">
                <table id="ordersTable" class="dashboard-table slot-style-table" style="width: 100%; min-width: 900px;">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Order ID</th>
                            <th style="width: 150px;">Customer</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 70px;">Items</th>
                            <th style="width: 90px;">Total</th>
                            <th style="width: 80px;">Payment</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($data['orders']) && !empty($data['orders'])): ?>
                            <?php foreach($data['orders'] as $order): ?>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary">#ORD-<?php echo str_pad($order->OrderID, 4, '0', STR_PAD_LEFT); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo $order->CustomerName; ?></div>
                                        <div class="table-cell-details"><?php echo $order->Email; ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo date('M d, Y', strtotime($order->OrderDate)); ?></div>
                                        <div class="table-cell-secondary"><?php echo date('g:i A', strtotime($order->OrderDate)); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $order->item_count; ?> item<?php echo $order->item_count > 1 ? 's' : ''; ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">₨ <?php echo number_format($order->TotalAmount); ?></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-<?php echo strtolower($order->PaymentMethod); ?>"><?php echo ucfirst($order->PaymentMethod); ?></span>
                                    </td>
                                    <td>
                                        <span class="table-badge status-<?php echo strtolower($order->Status); ?>"><?php echo ucfirst($order->Status); ?></span>
                                    </td>
                                    <td>
                                        <div class="order-action-buttons">
                                            <button class="order-action-btn btn-view" onclick="viewOrder(<?php echo $order->OrderID; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem;">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                                        <h3>No Orders Found</h3>
                                        <p>There are no orders to display.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
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
// Search functionality
document.getElementById('orderSearch').addEventListener('input', function() {
    filterOrders(this.value);
});

// Payment filter
document.getElementById('paymentFilter').addEventListener('change', function() {
    filterOrdersByPayment(this.value);
});

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
    if (payment === 'all') {
        const rows = document.getElementById('ordersTable').getElementsByTagName('tr');
        for (let i = 1; i < rows.length; i++) {
            rows[i].style.display = '';
        }
        return;
    }
    
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const badge = row.querySelector('.table-badge');
        if (badge && badge.textContent.toLowerCase() === payment.replace('_', ' ')) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function updateOrderStatus(orderId, status) {
    fetch('<?php echo URLROOT; ?>/shop/updateOrderStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            orderId: orderId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Reload page after a short delay to show updated status
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update order status', 'error');
    });
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
.slot-style-table-wrap {
    background: #fff;
    border-radius: 12px;
    padding: 4px 0;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

#ordersTable.slot-style-table {
    width: 100%;
    border-collapse: collapse;
}

#ordersTable.slot-style-table thead tr {
    background: #f8f9fa;
}

#ordersTable.slot-style-table thead th {
    padding: 12px 14px;
    text-align: left;
    font-size: 13px;
    color: #555;
    border-bottom: 2px solid #dee2e6;
}

#ordersTable.slot-style-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

#ordersTable.slot-style-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
}

.data-table .table-header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    grid-template-areas:
        "title actions"
        "filters filters";
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem 1rem;
    border-bottom: 1px solid rgba(74, 144, 226, 0.2);
}

.data-table .table-header-main {
    grid-area: title;
    min-width: 0;
}

.data-table .table-header h3 {
    margin: 0;
}

.data-table .table-actions {
    grid-area: actions;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    justify-self: end;
}

.filter-section {
    margin: 0;
    width: 100%;
}

.table-filters {
    grid-area: filters;
    position: relative;
    z-index: 2;
}

.filter-tabs {
    display: flex;
    gap: 0.8rem;
    flex-wrap: nowrap;
    background: rgba(255, 255, 255, 0.25);
    padding: 0.65rem 0.75rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    width: 100%;
    box-sizing: border-box;
}

.filter-tab {
    flex: 1 1 0;
    min-width: 0;
    padding: 10px 12px;
    text-decoration: none;
    color: #666;
    background: transparent;
    border: 2px solid rgba(74, 144, 226, 0.2);
    border-radius: 25px;
    text-align: center;
    font-weight: 500;
    transition: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    white-space: nowrap;
}

.filter-tab:hover {
    background: rgba(74, 144, 226, 0.1);
    color: #4A90E2;
}

.filter-tab.active {
    background: #4A90E2;
    color: white;
    border-color: #4A90E2;
}

.order-action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.order-action-btn {
    border: none;
    border-radius: 6px;
    color: #fff;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    line-height: 1;
}

.order-action-btn i {
    font-size: 11px;
}

.order-action-btn.btn-view {
    background: #4a90e2;
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
    .data-table .table-header {
        grid-template-columns: 1fr;
        grid-template-areas:
            "title"
            "actions"
            "filters";
        align-items: flex-start;
    }

    .data-table .table-actions {
        width: 100%;
        flex-wrap: wrap;
        justify-self: start;
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