<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-reviews.css">
<?php
$reviews = $data['reviews'] ?? [];
$reviewCount = count($reviews);
$averageRating = $reviewCount
    ? array_sum(array_map(fn($review) => (float)($review->Rating ?? 0), $reviews)) / $reviewCount
    : 0;
$pendingCount = count(array_filter($reviews, fn($review) => strtolower((string)($review->Status ?? '')) === 'pending'));
$positiveCount = count(array_filter($reviews, fn($review) => (float)($review->Rating ?? 0) >= 4));
$positivePercent = $reviewCount ? round(($positiveCount / $reviewCount) * 100) : 0;
$ratingDistribution = [];
for ($rating = 5; $rating >= 1; $rating--) {
    $count = count(array_filter($reviews, fn($review) => (int)round((float)($review->Rating ?? 0)) === $rating));
    $ratingDistribution[] = [
        'stars' => $rating,
        'count' => $count,
        'percentage' => $reviewCount ? round(($count / $reviewCount) * 100) : 0,
    ];
}
$renderStars = function ($rating) {
    $full = max(0, min(5, (int)round((float)$rating)));
    return str_repeat('<i class="fas fa-star"></i>', $full) . str_repeat('<i class="far fa-star"></i>', 5 - $full);
};
?>

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
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/rentals" class="nav-link">
                        <i class="fas fa-tools"></i>
                        <span>Equipment Rentals</span>
                    </a>
                </li>
                
                <li class="nav-item active">
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
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Shop Manager'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/shop/profile" class="profile-avatar" aria-label="Open shop profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <h1><i class="fas fa-star"></i> Reviews & Feedback Management</h1>
            <p>Monitor customer reviews, respond to feedback, and manage ratings</p>
        </div>

        <!-- Review Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FFD700, #FFA500);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="card-content">
                    <h3>Average Rating</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo number_format($averageRating, 1); ?></span>
                            <span class="label">Out of 5.0</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="card-content">
                    <h3>Total Reviews</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo (int)$reviewCount; ?></span>
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
                    <h3>Pending Reviews</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent"><?php echo (int)$pendingCount; ?></span>
                            <span class="label">Need Response</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="card-content">
                    <h3>Positive Reviews</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo (int)$positivePercent; ?>%</span>
                            <span class="label">4-5 Stars</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div style="background: white; border-radius: 12px; padding: 24px; margin: 24px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-chart-bar" style="color: #4A90E2;"></i>
                Rating Distribution
            </h2>
            <div style="display: grid; gap: 12px;">
                <?php 
                $ratings = $ratingDistribution;
                foreach ($ratings as $rating): ?>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="min-width: 80px; display: flex; align-items: center; gap: 6px;">
                            <span style="font-weight: 600; color: #333;"><?php echo $rating['stars']; ?></span>
                            <i class="fas fa-star" style="color: #FFD700; font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; background: #e0e0e0; height: 24px; border-radius: 12px; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #FFD700, #FFA500); height: 100%; width: <?php echo $rating['percentage']; ?>%; transition: width 0.5s ease;"></div>
                        </div>
                        <div style="min-width: 80px; text-align: right;">
                            <span style="font-weight: 600; color: #666;"><?php echo $rating['count']; ?></span>
                            <span style="color: #999; font-size: 12px;"> (<?php echo $rating['percentage']; ?>%)</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="data-table">
            <div class="table-header">
                <div class="table-header-main">
                    <h3><i class="fas fa-list"></i> Customer Reviews</h3>
                </div>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search reviews..." id="reviewSearch">
                    <select class="filter-dropdown" id="productFilter">
                        <option value="all">All Products</option>
                        <?php foreach (array_unique(array_filter(array_map(fn($review) => (string)($review->product_name ?? ''), $reviews))) as $productName): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($productName), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($productName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" onclick="exportReviews()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <div class="filter-section table-filters">
                    <div class="filter-tabs">
                        <a href="#" class="filter-tab active" data-rating="all">
                            <i class="fas fa-th"></i> All Reviews
                        </a>
                        <a href="#" class="filter-tab" data-rating="5">
                            <i class="fas fa-star"></i> 5 Stars
                        </a>
                        <a href="#" class="filter-tab" data-rating="4">
                            <i class="fas fa-star"></i> 4 Stars
                        </a>
                        <a href="#" class="filter-tab" data-rating="pending">
                            <i class="fas fa-clock"></i> Pending
                        </a>
                        <a href="#" class="filter-tab" data-rating="negative">
                            <i class="fas fa-exclamation-triangle"></i> Low Rated
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="table-content slot-style-table-wrap" style="overflow-x: auto;">
                <table id="reviewsTable" class="dashboard-table slot-style-table" style="min-width: 1280px; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Review ID</th>
                            <th style="width: 150px;">Customer</th>
                            <th style="width: 200px;">Product</th>
                            <th style="width: 90px;">Rating</th>
                            <th style="width: 350px;">Review</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <?php
                                    $reviewId = (int)($review->ReviewID ?? 0);
                                    $ratingValue = (float)($review->Rating ?? 0);
                                    $status = strtolower((string)($review->Status ?? 'pending'));
                                    $statusColor = $status === 'approved' ? '#10b981' : ($status === 'rejected' || $status === 'flagged' ? '#ef4444' : '#f59e0b');
                                    $statusBg = $status === 'approved' ? 'rgba(16, 185, 129, 0.15)' : ($status === 'rejected' || $status === 'flagged' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(245, 158, 11, 0.15)');
                                    $reviewText = (string)($review->Comment ?? $review->ReviewText ?? $review->Content ?? '');
                                    $reviewDate = $review->ReviewDate ?? $review->CreatedAt ?? '';
                                ?>
                                <tr data-review-id="<?php echo $reviewId; ?>"
                                    data-rating="<?php echo htmlspecialchars((string)$ratingValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-status="<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-product="<?php echo htmlspecialchars(strtolower((string)($review->product_name ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                                    <td><div class="table-cell-primary">#REV-<?php echo $reviewId; ?></div></td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($review->customer_name ?? 'Unknown Customer'); ?></div>
                                        <div class="table-cell-details"><?php echo htmlspecialchars($review->Email ?? ''); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($review->product_name ?? 'Product'); ?></div>
                                        <div class="table-cell-details">Product review</div>
                                    </td>
                                    <td>
                                        <div style="color: #FFD700; font-size: 16px;"><?php echo $renderStars($ratingValue); ?></div>
                                        <div style="font-size: 12px; color: #666;"><?php echo number_format($ratingValue, 1); ?></div>
                                    </td>
                                    <td>
                                        <div style="color: #333; font-size: 14px; line-height: 1.4;">
                                            "<?php echo htmlspecialchars($reviewText !== '' ? $reviewText : 'No written review provided.'); ?>"
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $reviewDate ? date('M d, Y', strtotime($reviewDate)) : '-'; ?></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge" style="background: <?php echo $statusBg; ?>; color: <?php echo $statusColor; ?>;"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                                    </td>
                                    <td>
                                        <div class="review-action-buttons">
                                            <button class="review-action-btn btn-view" onclick="viewReview(<?php echo $reviewId; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 2rem; color:#7f8c8d;">
                                    No product reviews found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Review Details Modal -->
<div id="reviewModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeReviewModal()"></div>
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-star"></i> Review Details</h2>
            <button class="modal-close" onclick="closeReviewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="reviewDetails">
            <!-- Review details will be loaded here -->
        </div>
        
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeReviewModal()">Close</button>
            <button class="btn btn-primary" onclick="saveResponse()">Send Response</button>
        </div>
    </div>
</div>

<style>
.slot-style-table-wrap {
    background: #fff;
    border-radius: 12px;
    padding: 4px 0;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

#reviewsTable.slot-style-table {
    width: 100%;
    border-collapse: collapse;
}

#reviewsTable.slot-style-table thead tr {
    background: #f8f9fa;
}

#reviewsTable.slot-style-table thead th {
    background: #f8f9fa;
    padding: 12px 14px;
    text-align: left;
    font-size: 13px;
    color: #555;
    border-bottom: 2px solid #dee2e6;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

#reviewsTable.slot-style-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

#reviewsTable.slot-style-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
}

.review-action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.review-action-btn {
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

.review-action-btn i {
    font-size: 11px;
}

.review-action-btn.btn-view {
    background: #4a90e2;
}

/* Reviews table action bar layout and compact export button */
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

.data-table .table-actions .search-box,
.data-table .table-actions .filter-dropdown {
    height: 32px;
    margin: 0;
}

.data-table .table-actions .btn.btn-primary {
    height: 32px;
    padding: 0.4rem 0.85rem;
    font-size: 0.95rem;
    line-height: 1;
    border-radius: 8px;
    box-shadow: none;
    transition: none;
    transform: none;
    margin: 0;
}

.data-table .table-actions .btn.btn-primary:hover,
.data-table .table-actions .btn.btn-primary:focus,
.data-table .table-actions .btn.btn-primary:active {
    transform: none;
    box-shadow: none;
}

/* Filter tabs styling */
.filter-section {
    margin-bottom: 0;
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
    font-size: 0.95rem;
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
}

.modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #4A90E2, #357ABD);
    color: white;
    border-radius: 15px 15px 0 0;
}

.modal-header h2 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
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

    .data-table .table-actions .btn.btn-primary {
        margin-left: 0;
    }
}
</style>

<script>
// Filter tabs functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterReviews(this.dataset.rating);
    });
});

// Search functionality
document.getElementById('reviewSearch').addEventListener('input', function() {
    searchReviews(this.value);
});

// Product filter
const productFilter = document.getElementById('productFilter');
if (productFilter) {
    productFilter.addEventListener('change', function() {
        const product = this.value.toLowerCase();
        const table = document.getElementById('reviewsTable');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (product === 'all' || !product) {
                row.style.display = '';
            } else {
                const productText = row.dataset.product || '';
                row.style.display = productText.includes(product) ? '' : 'none';
            }
        });
    });
}

function filterReviews(rating) {
    const table = document.getElementById('reviewsTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (rating === 'all') {
            row.style.display = '';
            return;
        }
        
        // Get the rating value from the row
        const ratingValue = row.dataset.rating || '0';
        const statusText = row.dataset.status || '';
        
        if (rating === 'pending') {
            row.style.display = statusText.includes('pending') ? '' : 'none';
        } else if (rating === 'negative') {
            // Show ratings 1-2
            const numRating = parseFloat(ratingValue) || 0;
            row.style.display = numRating <= 2 ? '' : 'none';
        } else {
            // Numeric rating filter (4, 5)
            row.style.display = ratingValue.includes(rating + '.0') || ratingValue.trim() === rating ? '' : 'none';
        }
    });
}

function searchReviews(searchTerm) {
    const table = document.getElementById('reviewsTable');
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

function viewReview(reviewId) {
    const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
    if (!row) {
        showNotification('Review details could not be found', 'error');
        return;
    }
    const cells = row.querySelectorAll('td');
    const customer = cells[1]?.querySelector('.table-cell-title')?.textContent.trim() || '-';
    const email = cells[1]?.querySelector('.table-cell-details')?.textContent.trim() || '-';
    const product = cells[2]?.querySelector('.table-cell-title')?.textContent.trim() || '-';
    const rating = row.dataset.rating || '0';
    const reviewText = cells[4]?.textContent.trim() || 'No written review provided.';
    document.getElementById('reviewDetails').innerHTML = `
        <div style="padding: 20px;">
            <h3 style="color: #333; margin-bottom: 20px;">Review #REV-${reviewId}</h3>
            <div style="background: rgba(74, 144, 226, 0.05); padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h4 style="color: #4A90E2; margin-bottom: 10px;">Customer Information</h4>
                <p><strong>Name:</strong> ${escapeHtml(customer)}</p>
                <p><strong>Email:</strong> ${escapeHtml(email)}</p>
                <p><strong>Product:</strong> ${escapeHtml(product)}</p>
            </div>
            <div style="background: rgba(74, 144, 226, 0.05); padding: 20px; border-radius: 10px;">
                <h4 style="color: #4A90E2; margin-bottom: 10px;">Review Content</h4>
                <div style="color: #FFD700; margin-bottom: 10px;">
                    ${renderStars(Number(rating))}
                    <span style="color: #333; margin-left: 10px; font-weight: 600;">${escapeHtml(Number(rating).toFixed(1))}</span>
                </div>
                <p style="line-height: 1.6; color: #666;">
                    ${escapeHtml(reviewText)}
                </p>
            </div>
            <div style="margin-top: 20px;">
                <label style="display: block; margin-bottom: 10px; color: #333; font-weight: 600;">Your Response:</label>
                <textarea style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; min-height: 100px; font-family: inherit;" placeholder="Type your response here..."></textarea>
            </div>
        </div>
    `;
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

function approveReview(reviewId) {
    console.log('Approving review:', reviewId);
    showNotification('Review approved successfully', 'success');
}

function rejectReview(reviewId) {
    if (confirm('Are you sure you want to reject this review?')) {
        console.log('Rejecting review:', reviewId);
        showNotification('Review rejected', 'info');
    }
}

function respondToReview(reviewId) {
    viewReview(reviewId);
}

function contactCustomer(reviewId) {
    console.log('Contacting customer for review:', reviewId);
    showNotification('Customer contact initiated', 'info');
}

function saveResponse() {
    showNotification('Response storage is not enabled in the current database, so this is saved as a demo response only.', 'info');
    closeReviewModal();
}

function exportReviews() {
    const rows = Array.from(document.querySelectorAll('#reviewsTable tbody tr'))
        .filter(row => row.style.display !== 'none')
        .map(row => Array.from(row.children).slice(0, 7).map(cell => `"${cell.textContent.trim().replace(/"/g, '""')}"`).join(','));
    const csv = ['Review ID,Customer,Product,Rating,Review,Date,Status', ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'shop_reviews.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
    showNotification('Reviews exported successfully', 'success');
}

function renderStars(rating) {
    const full = Math.max(0, Math.min(5, Math.round(Number(rating) || 0)));
    return '<i class="fas fa-star"></i>'.repeat(full) + '<i class="far fa-star"></i>'.repeat(5 - full);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
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

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
