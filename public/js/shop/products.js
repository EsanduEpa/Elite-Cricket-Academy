// Product Management JavaScript - Elite Cricket Academy Shop
// Handles CRUD operations and image uploads for products

let currentProductId = null;
let currentProductImage = null;

// Get base URL from the page
const URLROOT = window.location.origin + '/Elite';

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    setupEventListeners();
});

// Setup all event listeners
function setupEventListeners() {
    // Product form submission
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', handleProductSubmit);
    }
    
    // Product image upload
    const productImages = document.getElementById('productImages');
    if (productImages) {
        productImages.addEventListener('change', handleImagePreview);
    }
    
    // Search functionality
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterProducts(this.value);
        });
    }
    
    // Sort functionality
    const sortFilter = document.getElementById('sortFilter');
    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            sortProducts(this.value);
        });
    }
    
    // Category filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterProductsByCategory(this.dataset.category);
        });
    });
}

// Load all products from database
function loadProducts() {
    fetch(`${URLROOT}/shop/getProducts`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products) {
                renderProductsTable(data.products);
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            showNotification('Failed to load products', 'error');
        });
}

// Render products table
function renderProductsTable(products) {
    const tbody = document.querySelector('#productsTable tbody');
    if (!tbody) return;
    
    if (products.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 3rem;">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                        <h3>No Products Found</h3>
                        <p>Start by adding your first product</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = products.map(product => `
        <tr data-product-id="${product.ProductID}">
            <td>
                <div class="product-image">
                    <img src="${product.ProductImage ? URLROOT + '/' + product.ProductImage : URLROOT + '/images/default-product.png'}" 
                         alt="${product.Name}" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                         onerror="this.src='${URLROOT + '/images/default-product.png'}">
                </div>
            </td>
            <td>
                <div class="table-cell-title">${product.Name}</div>
                <div class="table-cell-details">${product.Description || 'No description'}</div>
            </td>
            <td style="text-align: center;">
                <span class="category-badge category-${product.Category ? product.Category.toLowerCase() : 'other'}">${product.Category || 'Other'}</span>
            </td>
            <td>
                <div class="table-cell-primary">₨ ${parseFloat(product.Price).toLocaleString()}</div>
            </td>
            <td>
                <span class="stock-level ${getStockClass(product.StockQuantity)}">${product.StockQuantity} units</span>
            </td>
            <td>
                <div class="rating">
                    <span class="stars">★★★★☆</span>
                    <small>(4.5)</small>
                </div>
            </td>
            <td style="text-align: center;">
                <span class="table-badge status-${product.Status ? product.Status.toLowerCase() : 'active'}">${product.Status || 'Active'}</span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-small btn-primary" onclick="editProduct(${product.ProductID})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-small btn-secondary" onclick="viewProduct(${product.ProductID})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-small btn-warning" onclick="duplicateProduct(${product.ProductID})">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button class="btn-small btn-danger" onclick="deleteProduct(${product.ProductID})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Get stock level class
function getStockClass(quantity) {
    if (quantity > 20) return 'stock-high';
    if (quantity > 5) return 'stock-medium';
    return 'stock-low';
}

// Handle product form submission
function handleProductSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const url = currentProductId 
        ? `${URLROOT}/shop/updateProductData`
        : `${URLROOT}/shop/addProduct`;
    
    if (currentProductId) {
        formData.append('productId', currentProductId);
    }
    
    // Show loading
    showNotification('Saving product...', 'info');
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showNotification(data.message, 'success');
                
                // If it's a new product and we have an image, upload it
                if (!currentProductId && data.productId && currentProductImage) {
                    uploadProductImage(data.productId, currentProductImage);
                } else if (currentProductId && currentProductImage) {
                    uploadProductImage(currentProductId, currentProductImage);
                } else {
                    closeProductModal();
                    loadProducts(); // Reload products list
                }
            } else {
                showNotification(data.message || 'Failed to save product', 'error');
            }
        } catch (error) {
            console.error('JSON Parse Error:', error);
            console.error('Raw response:', text);
            showNotification('Server returned invalid response', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showNotification('An error occurred while saving the product', 'error');
    });
}

// Handle image preview
function handleImagePreview(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.match('image/(jpeg|jpg|png)')) {
        showNotification('Only JPG, JPEG, and PNG images are allowed', 'error');
        e.target.value = '';
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Image size must be less than 5MB', 'error');
        e.target.value = '';
        return;
    }
    
    currentProductImage = file;
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewArea = document.querySelector('.image-upload-area');
        if (previewArea) {
            previewArea.innerHTML = `
                <img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                <p style="margin-top: 1rem;">Image ready to upload</p>
            `;
        }
    };
    reader.readAsDataURL(file);
}

// Upload product image
function uploadProductImage(productId, imageFile) {
    const formData = new FormData();
    formData.append('productId', productId);
    formData.append('productImage', imageFile);
    
    fetch(`${URLROOT}/shop/uploadProductImage`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product image uploaded successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to upload image', 'warning');
        }
        closeProductModal();
        loadProducts(); // Reload products list
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to upload product image', 'error');
        closeProductModal();
        loadProducts();
    });
}

// Delete product image
function deleteProductImage(productId) {
    if (!confirm('Are you sure you want to delete this product image?')) return;
    
    const formData = new FormData();
    formData.append('productId', productId);
    
    fetch(`${URLROOT}/shop/deleteProductImage`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadProducts();
        } else {
            showNotification(data.message || 'Failed to delete image', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Open add product modal
function openAddProductModal() {
    currentProductId = null;
    currentProductImage = null;
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('productForm').reset();
    
    // Reset image preview
    const previewArea = document.querySelector('.image-upload-area');
    if (previewArea) {
        previewArea.innerHTML = `
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Click to upload product images</p>
            <small>Supports: JPG, PNG (Max 5MB)</small>
        `;
    }
    
    document.getElementById('productModal').style.display = 'block';
}

// Edit product
function editProduct(productId) {
    currentProductId = productId;
    currentProductImage = null;
    document.getElementById('modalTitle').textContent = 'Edit Product';
    
    // Fetch product data
    fetch(`${URLROOT}/shop/getProduct?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.product) {
                const product = data.product;
                
                // Fill form with product data
                document.getElementById('productName').value = product.Name || '';
                document.getElementById('productDescription').value = product.Description || '';
                document.getElementById('productCategory').value = product.Category || '';
                document.getElementById('productBrand').value = product.Brand || '';
                document.getElementById('productPrice').value = product.Price || '';
                document.getElementById('productSKU').value = product.SKU || '';
                document.getElementById('productStock').value = product.StockQuantity || 0;
                document.getElementById('productStatus').value = product.Status || 'active';
                document.getElementById('productWeight').value = product.Weight || '';
                document.getElementById('productDimensions').value = product.Dimensions || '';
                
                // Show current image if exists
                if (product.ProductImage) {
                    const previewArea = document.querySelector('.image-upload-area');
                    if (previewArea) {
                        previewArea.innerHTML = 
                            '<img src="' + URLROOT + '/' + product.ProductImage + '" style="max-width: 100%; max-height: 200px; border-radius: 8px;">' +
                            '<p style="margin-top: 1rem;">Current product image</p>' +
                            '<button type="button" class="btn-delete-small" onclick="deleteProductImage(' + productId + ')">' +
                                '<i class="fas fa-trash"></i> Remove Image' +
                            '</button>';
                    }
                }
                
                document.getElementById('productModal').style.display = 'block';
            } else {
                showNotification('Product not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load product data', 'error');
        });
}

// View product
function viewProduct(productId) {
    // Implement product view modal
    showNotification('Product view coming soon', 'info');
}

// Duplicate product
function duplicateProduct(productId) {
    showNotification('Product duplication coming soon', 'info');
}

// Delete product
function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('productId', productId);
    
    fetch(`${URLROOT}/shop/deleteProductData`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadProducts();
        } else {
            showNotification(data.message || 'Failed to delete product', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while deleting the product', 'error');
    });
}

// Close product modal
function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
    currentProductId = null;
    currentProductImage = null;
}

// Filter functions
function filterProductsByCategory(category) {
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        if (category === 'all') {
            row.style.display = '';
        } else {
            const categoryBadge = row.querySelector('.category-badge');
            if (categoryBadge && categoryBadge.textContent.toLowerCase() === category.toLowerCase()) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function filterProducts(searchTerm) {
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function sortProducts(sortBy) {
    // Implement sorting logic
    showNotification(`Sorting by ${sortBy}`, 'info');
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'error' ? 'times-circle' :
                 type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="fas fa-${icon}"></i>
        </div>
        <div class="notification-content">
            <div class="notification-message">${message}</div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeProductModal();
    }
}
