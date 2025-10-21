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
                
                <li class="nav-item active">
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
            <h1><i class="fas fa-box"></i> Product Management</h1>
            <p>Manage cricket equipment, clothing, and accessories</p>
        </div>

        <!-- Product Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="card-content">
                    <h3>Total Products</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">248</span>
                            <span class="label">Active Products</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-content">
                    <h3>Low Stock</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent">12</span>
                            <span class="label">Need Restock</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="card-content">
                    <h3>Top Rated</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">28</span>
                            <span class="label">5 Star Products</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #6B73FF, #8B83FF);">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="card-content">
                    <h3>New This Month</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">15</span>
                            <span class="label">Added Products</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Categories Filter -->
        <div class="filter-section">
            <div class="filter-tabs">
                <a href="#" class="filter-tab active" data-category="all">
                    <i class="fas fa-th"></i> All Categories
                </a>
                <a href="#" class="filter-tab" data-category="bats">
                    <i class="fas fa-baseball-bat"></i> Bats
                </a>
                <a href="#" class="filter-tab" data-category="protective">
                    <i class="fas fa-shield-alt"></i> Protective Gear
                </a>
                <a href="#" class="filter-tab" data-category="clothing">
                    <i class="fas fa-tshirt"></i> Clothing
                </a>
                <a href="#" class="filter-tab" data-category="accessories">
                    <i class="fas fa-cog"></i> Accessories
                </a>
                <a href="#" class="filter-tab" data-category="balls">
                    <i class="fas fa-circle"></i> Balls
                </a>
            </div>
        </div>

        <!-- Product Actions & Search -->
        <div class="action-section">
            <div class="action-cards">
                <div class="action-card" onclick="openAddProductModal()">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>Add New Product</h4>
                    <p>Create new product listing</p>
                </div>
                
                <div class="action-card" onclick="importProducts()">
                    <div class="action-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h4>Import Products</h4>
                    <p>Bulk product upload</p>
                </div>
                
                <div class="action-card" onclick="manageCategories()">
                    <div class="action-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h4>Manage Categories</h4>
                    <p>Edit product categories</p>
                </div>
                
                <div class="action-card" onclick="bulkPriceUpdate()">
                    <div class="action-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h4>Price Updates</h4>
                    <p>Bulk pricing changes</p>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Products Catalog</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search products..." id="productSearch">
                    <select class="filter-dropdown" id="sortFilter">
                        <option value="name">Sort by Name</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="stock">Stock Level</option>
                        <option value="rating">Rating</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportProducts()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-content">
                <table id="productsTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="product-image">
                                    <img src="<?php echo URLROOT; ?>/img/products/bat-pro.jpg" alt="Cricket Bat" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-title">Professional Cricket Bat</div>
                                <div class="table-cell-details">Grade A English Willow</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="category-badge category-bats">Bats</span>
                            </td>
                            <td>
                                <div class="table-cell-primary">₨ 25,000</div>
                            </td>
                            <td>
                                <span class="stock-level stock-medium">15 units</span>
                            </td>
                            <td>
                                <div class="rating">
                                    <span class="stars">★★★★★</span>
                                    <small>(4.8)</small>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge status-active">Active</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="editProduct(1)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="viewProduct(1)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="duplicateProduct(1)">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="deleteProduct(1)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="product-image">
                                    <img src="<?php echo URLROOT; ?>/img/products/gloves.jpg" alt="Batting Gloves" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-title">Premium Batting Gloves</div>
                                <div class="table-cell-details">Leather palm with ventilation</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="category-badge category-protective">Protective</span>
                            </td>
                            <td>
                                <div class="table-cell-primary">₨ 8,500</div>
                            </td>
                            <td>
                                <span class="stock-level stock-low">3 units</span>
                            </td>
                            <td>
                                <div class="rating">
                                    <span class="stars">★★★★☆</span>
                                    <small>(4.2)</small>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge status-active">Active</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="editProduct(2)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="viewProduct(2)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="duplicateProduct(2)">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="deleteProduct(2)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="product-image">
                                    <img src="<?php echo URLROOT; ?>/img/products/helmet.jpg" alt="Cricket Helmet" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-title">Elite Cricket Helmet</div>
                                <div class="table-cell-details">Titanium grille with comfort padding</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="category-badge category-protective">Protective</span>
                            </td>
                            <td>
                                <div class="table-cell-primary">₨ 15,200</div>
                            </td>
                            <td>
                                <span class="stock-level stock-high">28 units</span>
                            </td>
                            <td>
                                <div class="rating">
                                    <span class="stars">★★★★★</span>
                                    <small>(4.9)</small>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge status-active">Active</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="editProduct(3)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="viewProduct(3)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="duplicateProduct(3)">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="deleteProduct(3)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="product-image">
                                    <img src="<?php echo URLROOT; ?>/img/products/jersey.jpg" alt="Team Jersey" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>Team Cricket Jersey</strong><br>
                                    <small>Moisture-wicking fabric</small>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge category-clothing">Clothing</span>
                            </td>
                            <td>₨ 4,200</td>
                            <td>
                                <span class="stock-level stock-high">45 units</span>
                            </td>
                            <td>
                                <div class="rating">
                                    <span class="stars">★★★★☆</span>
                                    <small>(4.3)</small>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-active">Active</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="editProduct(4)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="viewProduct(4)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="duplicateProduct(4)">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="deleteProduct(4)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div id="productModal" class="modal" style="display: none;">
            <div class="modal-content large-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-plus"></i> <span id="modalTitle">Add New Product</span></h3>
                    <span class="close" onclick="closeProductModal()">&times;</span>
                </div>
                <form id="productForm" class="modal-body">
                    <div class="form-grid">
                        <div class="form-section">
                            <h4>Basic Information</h4>
                            <div class="form-group">
                                <label for="productName">Product Name</label>
                                <input type="text" id="productName" name="productName" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="productDescription">Description</label>
                                <textarea id="productDescription" name="productDescription" rows="3"></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productCategory">Category</label>
                                    <select id="productCategory" name="productCategory" required>
                                        <option value="">Select Category</option>
                                        <option value="bats">Cricket Bats</option>
                                        <option value="protective">Protective Gear</option>
                                        <option value="clothing">Clothing</option>
                                        <option value="accessories">Accessories</option>
                                        <option value="balls">Cricket Balls</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="productBrand">Brand</label>
                                    <input type="text" id="productBrand" name="productBrand">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4>Pricing & Inventory</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productPrice">Price (₨)</label>
                                    <input type="number" id="productPrice" name="productPrice" step="0.01" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="productCost">Cost Price (₨)</label>
                                    <input type="number" id="productCost" name="productCost" step="0.01">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productStock">Stock Quantity</label>
                                    <input type="number" id="productStock" name="productStock" min="0" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="productMinStock">Minimum Stock Alert</label>
                                    <input type="number" id="productMinStock" name="productMinStock" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4>Product Images</h4>
                        <div class="image-upload-area" onclick="document.getElementById('productImages').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload product images</p>
                            <small>Supports: JPG, PNG, GIF (Max 5MB each)</small>
                        </div>
                        <input type="file" id="productImages" name="productImages[]" multiple accept="image/*" style="display: none;">
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="productForm">Save Product</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript -->
<script>
let currentProductId = null;

// Category filter functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterProductsByCategory(this.dataset.category);
    });
});

// Search functionality
document.getElementById('productSearch').addEventListener('input', function() {
    filterProducts(this.value);
});

// Sort functionality
document.getElementById('sortFilter').addEventListener('change', function() {
    sortProducts(this.value);
});

function filterProductsByCategory(category) {
    console.log('Filtering by category:', category);
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        if (category === 'all') {
            row.style.display = '';
        } else {
            const categoryCell = row.querySelector('.category-badge');
            const categoryClass = `category-${category}`;
            if (categoryCell && categoryCell.classList.contains(categoryClass)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function filterProducts(searchTerm) {
    const table = document.getElementById('productsTable');
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

function sortProducts(sortBy) {
    console.log('Sorting by:', sortBy);
    // Implementation would sort the table rows
    showNotification(`Products sorted by ${sortBy}`, 'info');
}

// Product management functions
function openAddProductModal() {
    currentProductId = null;
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('productForm').reset();
    document.getElementById('productModal').style.display = 'block';
}

function editProduct(productId) {
    currentProductId = productId;
    document.getElementById('modalTitle').textContent = 'Edit Product';
    // Load product data
    document.getElementById('productName').value = 'Professional Cricket Bat';
    document.getElementById('productDescription').value = 'Grade A English Willow cricket bat';
    document.getElementById('productCategory').value = 'bats';
    document.getElementById('productBrand').value = 'Gray-Nicolls';
    document.getElementById('productPrice').value = '25000';
    document.getElementById('productCost').value = '18000';
    document.getElementById('productStock').value = '15';
    document.getElementById('productMinStock').value = '5';
    
    document.getElementById('productModal').style.display = 'block';
}

function viewProduct(productId) {
    console.log('Viewing product:', productId);
    // Implementation would show product details
    showNotification(`Viewing product #${productId}`, 'info');
}

function duplicateProduct(productId) {
    console.log('Duplicating product:', productId);
    showNotification(`Product #${productId} duplicated successfully`, 'success');
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        console.log('Deleting product:', productId);
        showNotification(`Product #${productId} deleted successfully`, 'success');
        // Remove row from table
        event.target.closest('tr').remove();
    }
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
    currentProductId = null;
}

// Action card functions
function importProducts() {
    console.log('Import products');
    showNotification('Product import feature will be available soon', 'info');
}

function manageCategories() {
    console.log('Manage categories');
    showNotification('Category management opened', 'info');
}

function bulkPriceUpdate() {
    console.log('Bulk price update');
    showNotification('Bulk price update feature will be available soon', 'info');
}

function exportProducts() {
    console.log('Exporting products');
    showNotification('Products exported successfully', 'success');
}

// Form submission
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = currentProductId ? 'update' : 'create';
    
    console.log(`${action} product:`, Object.fromEntries(formData));
    
    showNotification(`Product ${action}d successfully`, 'success');
    closeProductModal();
    
    // If it's a new product, add row to table
    if (!currentProductId) {
        // Add new row logic here
    }
});

// Image upload preview
document.getElementById('productImages').addEventListener('change', function(e) {
    const files = e.target.files;
    console.log('Selected files:', files.length);
    showNotification(`${files.length} image(s) selected`, 'info');
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
/* Category badges */
.category-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.category-bats { background: rgba(255, 193, 7, 0.2); color: #b8860b; }
.category-protective { background: rgba(220, 53, 69, 0.2); color: #c82333; }
.category-clothing { background: rgba(40, 167, 69, 0.2); color: #1e7e34; }
.category-accessories { background: rgba(108, 117, 125, 0.2); color: #495057; }
.category-balls { background: rgba(255, 87, 34, 0.2); color: #d84315; }

/* Stock level indicators */
.stock-level {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
}

.stock-high { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.stock-medium { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.stock-low { background: rgba(244, 67, 54, 0.2); color: #c62828; }

/* Rating display */
.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stars {
    color: #ffc107;
    font-size: 1rem;
}

/* Product image */
.product-image img {
    border: 2px solid rgba(255, 255, 255, 0.3);
}

/* Large modal for product form */
.large-modal {
    max-width: 900px;
    width: 90%;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.form-section {
    background: rgba(74, 144, 226, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    border: 1px solid rgba(74, 144, 226, 0.1);
}

.form-section h4 {
    color: #4A90E2;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.8);
    outline: none;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

/* Image upload area */
.image-upload-area {
    border: 2px dashed rgba(74, 144, 226, 0.3);
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(74, 144, 226, 0.02);
}

.image-upload-area:hover {
    border-color: #4A90E2;
    background: rgba(74, 144, 226, 0.05);
}

.image-upload-area i {
    font-size: 3rem;
    color: #4A90E2;
    margin-bottom: 1rem;
}

.image-upload-area p {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-weight: 500;
}

.image-upload-area small {
    color: #666;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .large-modal {
        width: 95%;
        max-width: none;
    }
}
</style>

<script src="<?php echo URLROOT; ?>/js/admin/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>