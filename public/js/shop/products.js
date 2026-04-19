// Product Management JavaScript - Elite Cricket Academy Shop
// Handles CRUD operations and image uploads for products

let currentProductId = null;
let currentProductImage = null;

// Get base URL from the page so the script still works if the project folder changes.
const URLROOT = document.querySelector('.admin-layout')?.dataset.urlroot || (window.location.origin + '/Elite');
let activeCategoryFilter = 'all';
let activeSearchTerm = '';

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    setupEventListeners();
    setupRealtimeValidation();
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
        <tr data-product-id="${product.ProductID}"
            data-search="${escapeAttribute([product.Name, product.Description, product.Brand, product.Category].filter(Boolean).join(' ').toLowerCase())}"
            data-category="${escapeAttribute(String(product.Category || '').toLowerCase())}">
            <td>
                <div class="product-image">
                    <img src="${product.ProductImage ? URLROOT + '/' + escapeAttribute(product.ProductImage) : URLROOT + '/images/default-product.png'}"
                         alt="${escapeAttribute(product.Name || 'Product')}"
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                         onerror="this.src='${URLROOT + '/images/default-product.png'}">
                </div>
            </td>
            <td>
                <div class="table-cell-title">${escapeHtml(product.Name || 'Product')}</div>
                <div class="table-cell-details">${escapeHtml(product.Description || 'No description')}</div>
            </td>
            <td style="text-align: center;">
                <span class="category-badge category-${escapeAttribute(product.Category ? product.Category.toLowerCase() : 'other')}">${escapeHtml(product.Category || 'Other')}</span>
            </td>
            <td>
                <div class="table-cell-primary">₨ ${parseFloat(product.Price).toLocaleString()}</div>
            </td>
            <td>
                <span class="stock-level ${getStockClass(product.StockQuantity)}">${product.StockQuantity} units</span>
            </td>
            <td>
                <div class="rating">
                    <span class="stars">${renderStars(product.avg_rating || 0)}</span>
                    <small>(${Number(product.avg_rating || 0).toFixed(1)})</small>
                </div>
            </td>
            <td style="text-align: center;">
                <span class="table-badge status-${product.Status ? product.Status.toLowerCase() : 'active'}">${product.Status || 'Active'}</span>
            </td>
            <td>
                <div class="catalog-action-buttons">
                    <button class="catalog-action-btn btn-edit" onclick="editProduct(${product.ProductID})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="catalog-action-btn btn-view" onclick="viewProduct(${product.ProductID})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="catalog-action-btn btn-delete" onclick="deleteProduct(${product.ProductID})">
                        <i class="fas fa-trash"></i> Delete
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

function renderStars(rating) {
    const rounded = Math.max(0, Math.min(5, Math.round(Number(rating) || 0)));
    return '★'.repeat(rounded) + '☆'.repeat(5 - rounded);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeAttribute(value) {
    return escapeHtml(value).replace(/`/g, '&#096;');
}

// Handle product form submission
function handleProductSubmit(e) {
    e.preventDefault();
    
    // Clear all previous errors
    clearAllErrors();
    
    // Validate form
    if (!validateProductForm()) {
        showNotification('Please fix the errors before submitting', 'error');
        return;
    }
    
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

// Validate product form
function validateProductForm() {
    let isValid = true;
    
    // Product Name validation
    const name = document.getElementById('productName').value.trim();
    if (!name) {
        showFieldError('productName', 'nameError', 'Product name is required');
        isValid = false;
    } else if (name.length < 3) {
        showFieldError('productName', 'nameError', 'Product name must be at least 3 characters');
        isValid = false;
    } else if (name.length > 255) {
        showFieldError('productName', 'nameError', 'Product name is too long (max 255 characters)');
        isValid = false;
    }
    
    // Description validation
    const description = document.getElementById('productDescription').value.trim();
    if (description.length > 1000) {
        showFieldError('productDescription', 'descriptionError', 'Description is too long (max 1000 characters)');
        isValid = false;
    }
    
    // Category validation
    const category = document.getElementById('productCategory').value;
    if (!category) {
        showFieldError('productCategory', 'categoryError', 'Please select a category');
        isValid = false;
    }
    
    // Brand validation
    const brand = document.getElementById('productBrand').value.trim();
    if (brand.length > 100) {
        showFieldError('productBrand', 'brandError', 'Brand name is too long (max 100 characters)');
        isValid = false;
    }
    
    // Price validation
    const price = parseFloat(document.getElementById('productPrice').value);
    if (!price || price <= 0) {
        showFieldError('productPrice', 'priceError', 'Price must be greater than 0');
        isValid = false;
    } else if (price > 1000000) {
        showFieldError('productPrice', 'priceError', 'Price is too high (max ₨1,000,000)');
        isValid = false;
    }
    
    // Stock validation
    const stock = parseInt(document.getElementById('productStock').value);
    if (stock === '' || stock < 0) {
        showFieldError('productStock', 'stockError', 'Stock quantity cannot be negative');
        isValid = false;
    } else if (stock > 10000) {
        showFieldError('productStock', 'stockError', 'Stock quantity is too high (max 10,000)');
        isValid = false;
    }
    
    return isValid;
}

// Show field error
function showFieldError(fieldId, errorId, message) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById(errorId);
    
    if (field) {
        field.classList.add('error');
        // Scroll to first error
        if (document.querySelectorAll('.error').length === 1) {
            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    if (errorSpan) {
        errorSpan.textContent = '⚠ ' + message;
        errorSpan.style.display = 'block';
        errorSpan.style.color = '#e74c3c';
        errorSpan.style.fontWeight = '500';
    }
}

// Clear all errors
function clearAllErrors() {
    // Remove error class from all inputs
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
        field.classList.remove('error');
    });
    
    // Clear all error messages
    document.querySelectorAll('.field-error').forEach(errorSpan => {
        errorSpan.textContent = '';
        errorSpan.style.display = 'none';
    });
}

// Clear error on input
function setupRealtimeValidation() {
    const fields = ['productName', 'productDescription', 'productCategory', 'productBrand', 'productPrice', 'productStock'];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.classList.remove('error');
                const errorId = fieldId.replace('product', '').charAt(0).toLowerCase() + fieldId.replace('product', '').slice(1) + 'Error';
                const errorSpan = document.getElementById(errorId);
                if (errorSpan) {
                    errorSpan.textContent = '';
                }
            });
        }
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
    document.querySelectorAll('#productForm input, #productForm select, #productForm textarea').forEach(field => {
        field.disabled = false;
    });
    const submitButton = document.querySelector('button[form="productForm"]');
    if (submitButton) submitButton.style.display = '';
    
    // Clear all errors
    clearAllErrors();
    
    // Enable all fields for new product (remove read-only from category)
    const categoryField = document.getElementById('productCategory');
    const categoryLabel = document.querySelector('label[for="productCategory"]');
    if (categoryField) {
        categoryField.removeAttribute('disabled');
        if (categoryLabel) {
            categoryLabel.classList.remove('readonly-label');
        }
    }
    
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
    document.querySelectorAll('#productForm input, #productForm select, #productForm textarea').forEach(field => {
        field.disabled = false;
    });
    const submitButton = document.querySelector('button[form="productForm"]');
    if (submitButton) submitButton.style.display = '';
    
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
                document.getElementById('productStock').value = product.StockQuantity || 0;
                document.getElementById('productStatus').value = product.Status || 'active';
                document.getElementById('productWeight').value = product.Weight || '';
                document.getElementById('productDimensions').value = product.Dimensions || '';
                
                // Clear any previous errors
                clearAllErrors();
                
                // Make category read-only when editing
                // Use a hidden input to preserve the category value since disabled fields don't submit
                const categoryField = document.getElementById('productCategory');
                const categoryLabel = document.querySelector('label[for="productCategory"]');
                if (categoryField) {
                    // Remove any existing hidden category input
                    const existingHidden = document.getElementById('hiddenCategory');
                    if (existingHidden) {
                        existingHidden.remove();
                    }
                    
                    // Create hidden input to store category value
                    const hiddenCategory = document.createElement('input');
                    hiddenCategory.type = 'hidden';
                    hiddenCategory.id = 'hiddenCategory';
                    hiddenCategory.name = 'category';
                    hiddenCategory.value = product.Category || '';
                    categoryField.parentNode.appendChild(hiddenCategory);
                    
                    // Disable the visible select (for UI only)
                    categoryField.setAttribute('disabled', 'disabled');
                    categoryField.removeAttribute('name'); // Remove name so it doesn't override hidden field
                    
                    if (categoryLabel) {
                        categoryLabel.classList.add('readonly-label');
                    }
                }
                
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
    currentProductId = null;
    currentProductImage = null;
    fetch(`${URLROOT}/shop/getProduct?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.product) {
                showNotification(data.message || 'Product not found', 'error');
                return;
            }

            const product = data.product;
            document.getElementById('modalTitle').textContent = 'View Product';
            document.getElementById('productName').value = product.Name || '';
            document.getElementById('productDescription').value = product.Description || '';
            document.getElementById('productCategory').value = product.Category || '';
            document.getElementById('productBrand').value = product.Brand || '';
            document.getElementById('productPrice').value = product.Price || '';
            document.getElementById('productStock').value = product.StockQuantity || 0;
            document.getElementById('productStatus').value = product.Status || 'active';
            document.getElementById('productWeight').value = product.Weight || '';
            document.getElementById('productDimensions').value = product.Dimensions || '';

            clearAllErrors();
            document.querySelectorAll('#productForm input, #productForm select, #productForm textarea').forEach(field => {
                field.disabled = true;
            });
            const submitButton = document.querySelector('button[form="productForm"]');
            if (submitButton) submitButton.style.display = 'none';

            const previewArea = document.querySelector('.image-upload-area');
            if (previewArea) {
                previewArea.innerHTML = product.ProductImage
                    ? `<img src="${URLROOT}/${escapeAttribute(product.ProductImage)}" style="max-width: 100%; max-height: 200px; border-radius: 8px;"><p style="margin-top: 1rem;">Current product image</p>`
                    : '<p>No product image uploaded.</p>';
            }

            document.getElementById('productModal').style.display = 'block';
        })
        .catch(() => showNotification('Failed to load product data', 'error'));
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
    document.querySelectorAll('#productForm input, #productForm select, #productForm textarea').forEach(field => {
        field.disabled = false;
    });
    const submitButton = document.querySelector('button[form="productForm"]');
    if (submitButton) submitButton.style.display = '';
    
    // Clear errors
    clearAllErrors();
    
    // Reset read-only states and clean up hidden fields
    const categoryField = document.getElementById('productCategory');
    const categoryLabel = document.querySelector('label[for="productCategory"]');
    const hiddenCategory = document.getElementById('hiddenCategory');
    
    if (categoryField) {
        categoryField.removeAttribute('disabled');
        categoryField.setAttribute('name', 'category'); // Restore name attribute
        if (categoryLabel) {
            categoryLabel.classList.remove('readonly-label');
        }
    }
    
    // Remove hidden category field if exists
    if (hiddenCategory) {
        hiddenCategory.remove();
    }
}

// Filter functions
function filterProductsByCategory(category) {
    activeCategoryFilter = String(category || 'all').toLowerCase();
    applyProductFilters();
}

function filterProducts(searchTerm) {
    activeSearchTerm = String(searchTerm || '').toLowerCase();
    applyProductFilters();
}

function applyProductFilters() {
    const rows = document.querySelectorAll('#productsTable tbody tr');

    rows.forEach(row => {
        const category = row.dataset.category || '';
        const searchText = row.dataset.search || row.textContent.toLowerCase();
        const matchesCategory = activeCategoryFilter === 'all' || category === activeCategoryFilter;
        const matchesSearch = !activeSearchTerm || searchText.includes(activeSearchTerm);
        row.style.display = matchesCategory && matchesSearch ? '' : 'none';
    });
}

function sortProducts(sortBy) {
    const tbody = document.querySelector('#productsTable tbody');
    if (!tbody) return;
    
    const rows = Array.from(tbody.querySelectorAll('tr'));
    if (rows.length <= 1) return;
    
    rows.sort((a, b) => {
        if (sortBy === 'name' || sortBy === 'name-asc' || sortBy === 'name-desc') {
            const nameA = (a.querySelector('.table-cell-title')?.textContent || '').toLowerCase();
            const nameB = (b.querySelector('.table-cell-title')?.textContent || '').toLowerCase();
            return sortBy === 'name-desc' ? nameB.localeCompare(nameA) : nameA.localeCompare(nameB);
        } else if (sortBy === 'price_low' || sortBy === 'price_high' || sortBy === 'price-low' || sortBy === 'price-high') {
            const priceA = parseFloat((a.querySelector('.table-cell-primary')?.textContent || '0').replace(/[^\d.]/g, '')) || 0;
            const priceB = parseFloat((b.querySelector('.table-cell-primary')?.textContent || '0').replace(/[^\d.]/g, '')) || 0;
            return (sortBy === 'price_low' || sortBy === 'price-low') ? priceA - priceB : priceB - priceA;
        } else if (sortBy === 'stock' || sortBy === 'stock-low' || sortBy === 'stock-high') {
            const stockA = parseInt((a.querySelector('.stock-level')?.textContent || '0').replace(/[^\d]/g, '')) || 0;
            const stockB = parseInt((b.querySelector('.stock-level')?.textContent || '0').replace(/[^\d]/g, '')) || 0;
            return sortBy === 'stock-high' ? stockB - stockA : stockA - stockB;
        } else if (sortBy === 'rating') {
            const ratingA = parseFloat((a.querySelector('.rating small')?.textContent || '0').replace(/[^\d.]/g, '')) || 0;
            const ratingB = parseFloat((b.querySelector('.rating small')?.textContent || '0').replace(/[^\d.]/g, '')) || 0;
            return ratingB - ratingA;
        } else if (sortBy === 'newest') {
            return 0; // Keep original order (newest from DB)
        }
        return 0;
    });
    
    rows.forEach(row => tbody.appendChild(row));
    showNotification(`Sorted by ${sortBy}`, 'info');
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
            <div class="notification-message">${escapeHtml(message)}</div>
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
