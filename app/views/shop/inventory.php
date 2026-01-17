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
                
                <li class="nav-item">
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
                
                <li class="nav-item active">
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
            <h1><i class="fas fa-warehouse"></i> Inventory Management</h1>
            <p>Track stock levels, manage suppliers, and handle stock movements</p>
        </div>

        <!-- Inventory Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="card-content">
                    <h3>Total Stock Value</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">₨ <?php echo number_format($data['stats']['total_stock_value']); ?></span>
                            <span class="label">Current Inventory</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-content">
                    <h3>Low Stock Alerts</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent"><?php echo $data['stats']['low_stock_count']; ?></span>
                            <span class="label">Items Below Minimum</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-content">
                    <h3>In Stock</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo $data['stats']['in_stock_count']; ?></span>
                            <span class="label">Well Stocked Items</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #6B73FF, #8B83FF);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="card-content">
                    <h3>Out of Stock</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo $data['stats']['out_of_stock_count']; ?></span>
                            <span class="label">Need Restock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="action-section">
            <div class="action-cards">
                <div class="action-card" onclick="openStockAdjustmentModal()">
                    <div class="action-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h4>Stock Adjustment</h4>
                    <p>Adjust inventory levels</p>
                </div>
                
                <div class="action-card" onclick="openPurchaseOrderModal()">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>Create Purchase Order</h4>
                    <p>Order from suppliers</p>
                </div>
                
                <div class="action-card" onclick="viewLowStockItems()">
                    <div class="action-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4>Low Stock Alert</h4>
                    <p>Items need restocking</p>
                </div>
                
                <div class="action-card" onclick="generateStockReport()">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Stock Report</h4>
                    <p>Generate inventory report</p>
                </div>
            </div>
        </div>

        <!-- Inventory Filters -->
        <div class="filter-section" style="margin: 2rem 0;">
            <div class="filter-tabs" style="display: flex; gap: 0.5rem; background: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 15px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
                <a href="#" class="filter-tab active" data-status="all" style="flex: 1; padding: 12px 20px; text-decoration: none; color: white; background: #4A90E2; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-th"></i> All Items
                </a>
                <a href="#" class="filter-tab" data-status="in-stock" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-check-circle"></i> In Stock
                </a>
                <a href="#" class="filter-tab" data-status="low-stock" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-exclamation-triangle"></i> Low Stock
                </a>
                <a href="#" class="filter-tab" data-status="out-of-stock" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-times-circle"></i> Out of Stock
                </a>
                <a href="#" class="filter-tab" data-status="reorder" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-shopping-cart"></i> Reorder Point
                </a>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Inventory Items</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search inventory..." id="inventorySearch">
                    <select class="filter-dropdown" id="categoryFilter">
                        <option value="all">All Categories</option>
                        <option value="bats">Cricket Bats</option>
                        <option value="protective">Protective Gear</option>
                        <option value="clothing">Clothing</option>
                        <option value="accessories">Accessories</option>
                        <option value="balls">Cricket Balls</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportInventory()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-content">
                <table id="inventoryTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Min. Stock</th>
                            <th>Unit Cost</th>
                            <th>Total Value</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($data['inventory']) && !empty($data['inventory'])): ?>
                            <?php foreach($data['inventory'] as $item): ?>
                                <?php
                                // Determine stock status
                                $stockClass = 'stock-high';
                                $statusText = 'In Stock';
                                $statusClass = 'status-in-stock';
                                
                                if ($item->StockQuantity == 0) {
                                    $stockClass = 'stock-out';
                                    $statusText = 'Out of Stock';
                                    $statusClass = 'status-out-stock';
                                } elseif ($item->StockQuantity <= 5) {
                                    $stockClass = 'stock-low';
                                    $statusText = 'Low Stock';
                                    $statusClass = 'status-low-stock';
                                } elseif ($item->StockQuantity <= 15) {
                                    $stockClass = 'stock-normal';
                                }
                                
                                $totalValue = $item->Price * $item->StockQuantity;
                                $minStock = max(5, round($item->StockQuantity * 0.2));
                                ?>
                                <tr>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($item->Name); ?></div>
                                        <div class="table-cell-details"><?php echo htmlspecialchars(substr($item->Description ?? '', 0, 40)); ?><?php echo strlen($item->Description ?? '') > 40 ? '...' : ''; ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $item->SKU ?? 'N/A'; ?></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="category-badge category-<?php echo strtolower($item->Category); ?>"><?php echo $item->Category; ?></span>
                                    </td>
                                    <td>
                                        <span class="stock-quantity <?php echo $stockClass; ?>"><?php echo $item->StockQuantity; ?> units</span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $minStock; ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">₨ <?php echo number_format($item->Price, 2); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">₨ <?php echo number_format($totalValue, 2); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo $item->Brand ?? 'N/A'; ?></div>
                                        <div class="table-cell-details">Supplier Info</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-small btn-primary" onclick="adjustStock(<?php echo $item->ProductID; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-small btn-secondary" onclick="viewStockHistory(<?php echo $item->ProductID; ?>)">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <?php if($item->StockQuantity <= 10): ?>
                                                <button class="btn-small btn-danger" onclick="urgentReorder(<?php echo $item->ProductID; ?>)">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-small btn-warning" onclick="reorderItem(<?php echo $item->ProductID; ?>)">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 3rem;">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                                        <h3>No Inventory Items</h3>
                                        <p>Start by adding products to your inventory.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-out-of-stock">Out of Stock</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-danger" onclick="emergencyOrder('JER-004')">
                                        <i class="fas fa-bolt"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="viewStockHistory('JER-004')">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="findAlternativeSupplier('JER-004')">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock Adjustment Modal -->
        <div id="stockAdjustmentModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-edit"></i> Stock Adjustment</h3>
                    <span class="close" onclick="closeStockModal()">&times;</span>
                </div>
                <form id="stockAdjustmentForm" class="modal-body">
                    <div class="form-group">
                        <label for="adjustmentItem">Item</label>
                        <select id="adjustmentItem" name="adjustmentItem" required>
                            <option value="">Select Item</option>
                            <option value="BAT-001">Professional Cricket Bat (BAT-001)</option>
                            <option value="GLV-002">Premium Batting Gloves (GLV-002)</option>
                            <option value="HLM-003">Elite Cricket Helmet (HLM-003)</option>
                            <option value="JER-004">Team Cricket Jersey (JER-004)</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="currentStock">Current Stock</label>
                            <input type="number" id="currentStock" name="currentStock" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="adjustmentType">Adjustment Type</label>
                            <select id="adjustmentType" name="adjustmentType" required>
                                <option value="">Select Type</option>
                                <option value="increase">Increase Stock</option>
                                <option value="decrease">Decrease Stock</option>
                                <option value="set">Set Stock Level</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="adjustmentQuantity">Quantity</label>
                        <input type="number" id="adjustmentQuantity" name="adjustmentQuantity" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="adjustmentReason">Reason</label>
                        <select id="adjustmentReason" name="adjustmentReason" required>
                            <option value="">Select Reason</option>
                            <option value="damaged">Damaged Stock</option>
                            <option value="theft">Theft/Loss</option>
                            <option value="returned">Customer Return</option>
                            <option value="found">Stock Found</option>
                            <option value="recount">Physical Recount</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="adjustmentNotes">Notes (Optional)</label>
                        <textarea id="adjustmentNotes" name="adjustmentNotes" rows="3" placeholder="Additional details about the adjustment..."></textarea>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeStockModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="stockAdjustmentForm">Apply Adjustment</button>
                </div>
            </div>
        </div>

        <!-- Purchase Order Modal -->
        <div id="purchaseOrderModal" class="modal" style="display: none;">
            <div class="modal-content large-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-plus"></i> Create Purchase Order</h3>
                    <span class="close" onclick="closePurchaseOrderModal()">&times;</span>
                </div>
                <form id="purchaseOrderForm" class="modal-body">
                    <div class="form-grid">
                        <div class="form-section">
                            <h4>Supplier Information</h4>
                            <div class="form-group">
                                <label for="supplierId">Supplier</label>
                                <select id="supplierId" name="supplierId" required>
                                    <option value="">Select Supplier</option>
                                    <option value="1">Gray-Nicolls</option>
                                    <option value="2">Kookaburra</option>
                                    <option value="3">MRF</option>
                                    <option value="4">Nike</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="expectedDelivery">Expected Delivery</label>
                                <input type="date" id="expectedDelivery" name="expectedDelivery" required>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4>Order Details</h4>
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <select id="priority" name="priority" required>
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="paymentTerms">Payment Terms</label>
                                <select id="paymentTerms" name="paymentTerms" required>
                                    <option value="net30">Net 30</option>
                                    <option value="net15">Net 15</option>
                                    <option value="immediate">Immediate</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4>Order Items</h4>
                        <div id="orderItemsContainer">
                            <div class="order-item-row">
                                <div class="form-row order-row">
                                    <div class="form-group">
                                        <select name="itemSku[]" required>
                                            <option value="">Select Item</option>
                                            <option value="BAT-001">Professional Cricket Bat</option>
                                            <option value="GLV-002">Premium Batting Gloves</option>
                                            <option value="HLM-003">Elite Cricket Helmet</option>
                                            <option value="JER-004">Team Cricket Jersey</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="quantity[]" placeholder="Quantity" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="number" name="unitCost[]" placeholder="Unit Cost" step="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-danger btn-small" onclick="removeOrderItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="addOrderItem()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePurchaseOrderModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="purchaseOrderForm">Create Purchase Order</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript -->
<script>
// Filter functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterInventoryByStatus(this.dataset.status);
    });
});

// Search functionality
document.getElementById('inventorySearch').addEventListener('input', function() {
    filterInventory(this.value);
});

// Category filter
document.getElementById('categoryFilter').addEventListener('change', function() {
    filterInventoryByCategory(this.value);
});

function filterInventoryByStatus(status) {
    console.log('Filtering by status:', status);
    const rows = document.querySelectorAll('#inventoryTable tbody tr');
    
    rows.forEach(row => {
        const statusCell = row.querySelector('.status-badge');
        if (status === 'all') {
            row.style.display = '';
        } else {
            const statusClass = `status-${status.replace('-', '-')}`;
            if (statusCell && statusCell.classList.contains(statusClass)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function filterInventory(searchTerm) {
    const table = document.getElementById('inventoryTable');
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

function filterInventoryByCategory(category) {
    console.log('Filtering by category:', category);
    // Implementation would filter the table rows
}

// Action functions
function openStockAdjustmentModal() {
    document.getElementById('stockAdjustmentModal').style.display = 'block';
}

function closeStockModal() {
    document.getElementById('stockAdjustmentModal').style.display = 'none';
    document.getElementById('stockAdjustmentForm').reset();
}

function openPurchaseOrderModal() {
    // Set default expected delivery to 7 days from now
    const date = new Date();
    date.setDate(date.getDate() + 7);
    document.getElementById('expectedDelivery').value = date.toISOString().split('T')[0];
    document.getElementById('purchaseOrderModal').style.display = 'block';
}

function closePurchaseOrderModal() {
    document.getElementById('purchaseOrderModal').style.display = 'none';
    document.getElementById('purchaseOrderForm').reset();
}

function viewLowStockItems() {
    console.log('Viewing low stock items');
    // Filter to show only low stock items
    document.querySelector('[data-status="low-stock"]').click();
    showNotification('Showing low stock items', 'info');
}

function generateStockReport() {
    console.log('Generating stock report');
    showNotification('Stock report generated successfully', 'success');
}

// Individual item actions
function adjustStock(sku) {
    console.log('Adjusting stock for:', sku);
    document.getElementById('adjustmentItem').value = sku;
    // Set current stock based on SKU
    const stockData = {
        'BAT-001': 15,
        'GLV-002': 3,
        'HLM-003': 28,
        'JER-004': 0
    };
    document.getElementById('currentStock').value = stockData[sku] || 0;
    openStockAdjustmentModal();
}

function viewStockHistory(sku) {
    console.log('Viewing stock history for:', sku);
    showNotification(`Stock history for ${sku} loaded`, 'info');
}

function reorderItem(sku) {
    console.log('Reordering item:', sku);
    showNotification(`Reorder initiated for ${sku}`, 'success');
}

function urgentReorder(sku) {
    console.log('Urgent reorder for:', sku);
    showNotification(`Urgent reorder placed for ${sku}`, 'warning');
}

function emergencyOrder(sku) {
    console.log('Emergency order for:', sku);
    if (confirm(`Create emergency order for ${sku}? This will be marked as highest priority.`)) {
        showNotification(`Emergency order created for ${sku}`, 'success');
    }
}

function findAlternativeSupplier(sku) {
    console.log('Finding alternative supplier for:', sku);
    showNotification(`Searching for alternative suppliers for ${sku}`, 'info');
}

function checkReorderPoint(sku) {
    console.log('Checking reorder point for:', sku);
    showNotification(`Reorder point analysis for ${sku}`, 'info');
}

// Purchase Order functions
function addOrderItem() {
    const container = document.getElementById('orderItemsContainer');
    const newItem = document.createElement('div');
    newItem.className = 'order-item-row';
    newItem.innerHTML = `
        <div class="form-row order-row">
            <div class="form-group">
                <select name="itemSku[]" required>
                    <option value="">Select Item</option>
                    <option value="BAT-001">Professional Cricket Bat</option>
                    <option value="GLV-002">Premium Batting Gloves</option>
                    <option value="HLM-003">Elite Cricket Helmet</option>
                    <option value="JER-004">Team Cricket Jersey</option>
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="quantity[]" placeholder="Quantity" min="1" required>
            </div>
            <div class="form-group">
                <input type="number" name="unitCost[]" placeholder="Unit Cost" step="0.01" required>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-danger btn-small" onclick="removeOrderItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
}

function removeOrderItem(button) {
    button.closest('.order-item-row').remove();
}

function exportInventory() {
    console.log('Exporting inventory');
    showNotification('Inventory exported successfully', 'success');
}

// Form submissions
document.getElementById('stockAdjustmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    console.log('Stock adjustment:', Object.fromEntries(formData));
    showNotification('Stock adjustment applied successfully', 'success');
    closeStockModal();
});

document.getElementById('purchaseOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    console.log('Purchase order created:', Object.fromEntries(formData));
    showNotification('Purchase order created successfully', 'success');
    closePurchaseOrderModal();
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4ECDC4' : type === 'error' ? '#FF6B6B' : type === 'warning' ? '#FF8A50' : '#4A90E2'};
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
/* Filter tabs styling */
.filter-tab:hover {
    background: rgba(74, 144, 226, 0.1) !important;
    color: #4A90E2 !important;
}

.filter-tab.active {
    background: #4A90E2 !important;
    color: white !important;
}

/* Stock quantity indicators */
.stock-quantity {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
}

.stock-high { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.stock-normal { background: rgba(74, 144, 226, 0.2); color: #1976d2; }
.stock-low { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.stock-out { background: rgba(244, 67, 54, 0.2); color: #c62828; }

/* Status badges for inventory */
.status-in-stock { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.status-low-stock { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.status-out-of-stock { background: rgba(244, 67, 54, 0.2); color: #c62828; }
.status-reorder { background: rgba(156, 39, 176, 0.2); color: #7b1fa2; }

/* Purchase order form styles */
.order-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
    margin-bottom: 1rem;
    padding: 1rem;
    background: rgba(74, 144, 226, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(74, 144, 226, 0.1);
}

.order-item-row {
    margin-bottom: 1rem;
}

#orderItemsContainer {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1rem;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@media (max-width: 768px) {
    .order-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
}
</style>

<script src="<?php echo URLROOT; ?>/js/admin/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>