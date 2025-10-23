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
                                <label for="productName">Product Name <span class="required">*</span></label>
                                <input type="text" id="productName" name="name" required maxlength="255">
                                <span class="field-error" id="nameError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="productDescription">Description</label>
                                <textarea id="productDescription" name="description" rows="3" maxlength="1000"></textarea>
                                <span class="field-error" id="descriptionError"></span>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productCategory">Category <span class="required">*</span></label>
                                    <select id="productCategory" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Batting">Cricket Bats</option>
                                        <option value="Protective">Protective Gear</option>
                                        <option value="Merchandise">Clothing</option>
                                        <option value="Accessories">Accessories</option>
                                        <option value="Bowling">Cricket Balls</option>
                                        <option value="Training">Training Equipment</option>
                                    </select>
                                    <span class="field-error" id="categoryError"></span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="productBrand">Brand</label>
                                    <input type="text" id="productBrand" name="brand" maxlength="100">
                                    <span class="field-error" id="brandError"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4>Pricing & Inventory</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productPrice">Price (₨) <span class="required">*</span></label>
                                    <input type="number" id="productPrice" name="price" step="0.01" min="0.01" required>
                                    <span class="field-error" id="priceError"></span>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productStock">Stock Quantity <span class="required">*</span></label>
                                    <input type="number" id="productStock" name="stock" min="0" required>
                                    <span class="field-error" id="stockError"></span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="productStatus">Status</label>
                                    <select id="productStatus" name="status">
                                        <option value="active">Active</option>
                                        <option value="discontinued">Discontinued</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productWeight">Weight (kg)</label>
                                    <input type="number" id="productWeight" name="weight" step="0.001">
                                </div>
                                
                                <div class="form-group">
                                    <label for="productDimensions">Dimensions</label>
                                    <input type="text" id="productDimensions" name="dimensions" placeholder="L x W x H">
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

.form-group label .required {
    color: #e74c3c;
    margin-left: 3px;
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

.form-group input.error,
.form-group select.error,
.form-group textarea.error {
    border-color: #e74c3c;
    background: rgba(231, 76, 60, 0.05) !important;
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.form-group input.error:focus,
.form-group select.error:focus,
.form-group textarea.error:focus {
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.field-error {
    display: block;
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    min-height: 1.2em;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
    background: rgba(231, 76, 60, 0.1);
    border-radius: 5px;
    border-left: 3px solid #e74c3c;
}

/* Filter Tabs Styles */
.filter-section {
    margin-bottom: 2rem;
}

.filter-tabs {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    background: rgba(255, 255, 255, 0.2);
    padding: 1rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.filter-tab {
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.3);
    border: 2px solid rgba(74, 144, 226, 0.2);
    border-radius: 25px;
    color: #333;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    cursor: pointer;
    backdrop-filter: blur(5px);
}

.filter-tab:hover {
    background: rgba(74, 144, 226, 0.15);
    border-color: #4A90E2;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 144, 226, 0.2);
}

.filter-tab.active {
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    color: white;
    border-color: #4A90E2;
    box-shadow: 0 5px 20px rgba(74, 144, 226, 0.3);
}

.filter-tab.active i {
    color: white;
}

.filter-tab i {
    font-size: 1rem;
    color: #4A90E2;
    transition: all 0.3s ease;
}

/* Read-only field styles */
.form-group input[readonly],
.form-group select[disabled] {
    background: #e9ecef !important;
    cursor: not-allowed !important;
    color: #6c757d !important;
    opacity: 0.7;
    border-color: rgba(108, 117, 125, 0.3) !important;
}

.form-group label.readonly-label {
    color: #6c757d;
}

.form-group label.readonly-label::after {
    content: " 🔒";
    font-size: 0.9em;
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
<script src="<?php echo URLROOT; ?>/js/shop/products.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>