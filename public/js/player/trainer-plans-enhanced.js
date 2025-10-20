// =============================================================================
// TRAINER PLANS PAGE ENHANCED FUNCTIONALITY
// =============================================================================
// Additional JavaScript for trainer-plans.php page to handle:
// - Content views tracking
// - Favorites management
// - Search functionality
// - Modal interactions
// - Filter enhancements
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize enhanced functionality
    initializeContentTracking();
    initializeFavoritesSystem();
    initializeSearchFunctionality();
    initializeModalEnhancements();
    initializeFilterEnhancements();
});

// =============================================================================
// CONTENT VIEW TRACKING
// =============================================================================

let viewStartTimes = {};

function initializeContentTracking() {
    // Track modal views
    const modals = document.querySelectorAll('.plan-modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const contentType = this.dataset.contentType;
            const contentId = this.dataset.contentId;
            viewStartTimes[`${contentType}_${contentId}`] = Date.now();
        });
        
        modal.addEventListener('hide.bs.modal', function() {
            const contentType = this.dataset.contentType;
            const contentId = this.dataset.contentId;
            const startTime = viewStartTimes[`${contentType}_${contentId}`];
            
            if (startTime) {
                const viewDuration = Math.round((Date.now() - startTime) / 1000);
                recordContentView(contentType, contentId, viewDuration);
                delete viewStartTimes[`${contentType}_${contentId}`];
            }
        });
    });
}

function recordContentView(contentType, contentId, viewDuration) {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `record_view=1&content_type=${contentType}&content_id=${contentId}&view_duration=${viewDuration}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`Recorded view: ${contentType} ${contentId} (${viewDuration}s)`);
        }
    })
    .catch(error => console.error('Error recording view:', error));
}

// =============================================================================
// FAVORITES SYSTEM
// =============================================================================

function initializeFavoritesSystem() {
    // Add favorite buttons to each plan card
    const planCards = document.querySelectorAll('.dashboard-card');
    planCards.forEach(card => {
        addFavoriteButton(card);
    });
}

function addFavoriteButton(card) {
    const contentType = card.dataset.contentType;
    const contentId = card.dataset.contentId;
    
    if (!contentType || !contentId) return;
    
    const favoriteBtn = document.createElement('button');
    favoriteBtn.className = 'btn btn-sm btn-outline-warning favorite-btn';
    favoriteBtn.innerHTML = '<i class="fas fa-star"></i>';
    favoriteBtn.title = 'Add to Favorites';
    favoriteBtn.onclick = () => toggleFavorite(contentType, contentId, favoriteBtn);
    
    // Insert favorite button in card header
    const cardHeader = card.querySelector('.card-header') || card.querySelector('.card-body');
    if (cardHeader) {
        cardHeader.style.position = 'relative';
        favoriteBtn.style.position = 'absolute';
        favoriteBtn.style.top = '10px';
        favoriteBtn.style.right = '10px';
        cardHeader.appendChild(favoriteBtn);
    }
}

function toggleFavorite(contentType, contentId, button) {
    const isFavorited = button.classList.contains('btn-warning');
    const action = isFavorited ? 'remove_favorite' : 'add_favorite';
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `${action}=1&content_type=${contentType}&content_id=${contentId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isFavorited) {
                button.classList.remove('btn-warning');
                button.classList.add('btn-outline-warning');
                button.title = 'Add to Favorites';
            } else {
                button.classList.remove('btn-outline-warning');
                button.classList.add('btn-warning');
                button.title = 'Remove from Favorites';
            }
            showToast(isFavorited ? 'Removed from favorites' : 'Added to favorites');
        }
    })
    .catch(error => console.error('Error toggling favorite:', error));
}

// =============================================================================
// SEARCH FUNCTIONALITY
// =============================================================================

function initializeSearchFunctionality() {
    // Add search bar to the page
    const searchContainer = document.createElement('div');
    searchContainer.className = 'search-container mb-4';
    searchContainer.innerHTML = `
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="contentSearch" 
                           placeholder="Search workouts, nutrition guides, supplements...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="searchType">
                    <option value="">All Content</option>
                    <option value="workout">Workouts Only</option>
                    <option value="nutrition">Nutrition Only</option>
                    <option value="supplement">Supplements Only</option>
                </select>
            </div>
        </div>
        <div id="searchResults" class="mt-3" style="display: none;"></div>
    `;
    
    // Insert search container before filter tabs
    const filterTabs = document.querySelector('.filter-tabs');
    if (filterTabs) {
        filterTabs.parentNode.insertBefore(searchContainer, filterTabs);
    }
    
    // Bind search events
    const searchInput = document.getElementById('contentSearch');
    const searchBtn = document.getElementById('searchBtn');
    const searchType = document.getElementById('searchType');
    
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    // Clear search when filter tabs are clicked
    const filterTabButtons = document.querySelectorAll('.filter-tabs .tab-btn');
    filterTabButtons.forEach(btn => {
        btn.addEventListener('click', clearSearch);
    });
}

function performSearch() {
    const keyword = document.getElementById('contentSearch').value.trim();
    const contentType = document.getElementById('searchType').value;
    const searchResults = document.getElementById('searchResults');
    
    if (!keyword) {
        clearSearch();
        return;
    }
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `search=1&keyword=${encodeURIComponent(keyword)}&content_type=${contentType}`
    })
    .then(response => response.json())
    .then(data => {
        displaySearchResults(data);
    })
    .catch(error => {
        console.error('Error searching:', error);
        searchResults.innerHTML = '<div class="alert alert-danger">Search error occurred</div>';
        searchResults.style.display = 'block';
    });
}

function displaySearchResults(results) {
    const searchResults = document.getElementById('searchResults');
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div class="alert alert-info">No results found</div>';
        searchResults.style.display = 'block';
        hideMainContent();
        return;
    }
    
    let html = `<h5>Search Results (${results.length})</h5><div class="row">`;
    
    results.forEach(result => {
        const badgeClass = result.ContentType === 'workout' ? 'success' : 
                          result.ContentType === 'nutrition' ? 'info' : 'warning';
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">${result.Title}</h6>
                            <span class="badge bg-${badgeClass}">${result.ContentType}</span>
                        </div>
                        <p class="card-text small text-muted">${result.Description || result.Benefits || ''}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">by ${result.TrainerName}</small>
                            <button class="btn btn-sm btn-primary" 
                                    onclick="viewContent('${result.ContentType}', ${result.ContentID})">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    html += '<button class="btn btn-secondary mt-3" onclick="clearSearch()">Clear Search</button>';
    
    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
    hideMainContent();
}

function clearSearch() {
    document.getElementById('contentSearch').value = '';
    document.getElementById('searchType').value = '';
    document.getElementById('searchResults').style.display = 'none';
    showMainContent();
}

function hideMainContent() {
    const filterTabs = document.querySelector('.filter-tabs');
    const tabContents = document.querySelectorAll('.tab-content');
    if (filterTabs) filterTabs.style.display = 'none';
    tabContents.forEach(content => content.style.display = 'none');
}

function showMainContent() {
    const filterTabs = document.querySelector('.filter-tabs');
    const tabContents = document.querySelectorAll('.tab-content');
    if (filterTabs) filterTabs.style.display = 'flex';
    tabContents.forEach(content => content.style.display = 'block');
}

function viewContent(contentType, contentId) {
    // Trigger the appropriate modal
    const modalId = `${contentType}Modal${contentId}`;
    const modal = document.getElementById(modalId);
    if (modal) {
        new bootstrap.Modal(modal).show();
    }
}

// =============================================================================
// MODAL ENHANCEMENTS
// =============================================================================

function initializeModalEnhancements() {
    // Add print functionality to modals
    const modals = document.querySelectorAll('.plan-modal');
    modals.forEach(modal => {
        addPrintButton(modal);
        addShareButton(modal);
    });
}

function addPrintButton(modal) {
    const modalFooter = modal.querySelector('.modal-footer');
    if (modalFooter) {
        const printBtn = document.createElement('button');
        printBtn.className = 'btn btn-outline-secondary';
        printBtn.innerHTML = '<i class="fas fa-print"></i> Print';
        printBtn.onclick = () => printModal(modal);
        modalFooter.insertBefore(printBtn, modalFooter.firstChild);
    }
}

function addShareButton(modal) {
    const modalFooter = modal.querySelector('.modal-footer');
    if (modalFooter) {
        const shareBtn = document.createElement('button');
        shareBtn.className = 'btn btn-outline-info';
        shareBtn.innerHTML = '<i class="fas fa-share"></i> Share';
        shareBtn.onclick = () => shareContent(modal);
        modalFooter.insertBefore(shareBtn, modalFooter.firstChild);
    }
}

function printModal(modal) {
    const modalContent = modal.querySelector('.modal-body').innerHTML;
    const modalTitle = modal.querySelector('.modal-title').textContent;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>${modalTitle}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    @media print { .no-print { display: none; } }
                </style>
            </head>
            <body>
                <h2>${modalTitle}</h2>
                ${modalContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function shareContent(modal) {
    const modalTitle = modal.querySelector('.modal-title').textContent;
    if (navigator.share) {
        navigator.share({
            title: modalTitle,
            text: `Check out this ${modalTitle} from Elite Cricket Academy`,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard');
        });
    }
}

// =============================================================================
// FILTER ENHANCEMENTS
// =============================================================================

function initializeFilterEnhancements() {
    // Add count badges to filter tabs
    updateFilterCounts();
    
    // Add level and category filters
    addAdvancedFilters();
}

function updateFilterCounts() {
    const allTab = document.querySelector('[data-tab="all"]');
    const workoutTab = document.querySelector('[data-tab="workouts"]');
    const nutritionTab = document.querySelector('[data-tab="nutrition"]');
    const supplementTab = document.querySelector('[data-tab="supplements"]');
    
    if (allTab) {
        const allCount = document.querySelectorAll('.dashboard-card').length;
        allTab.innerHTML += ` <span class="badge bg-secondary">${allCount}</span>`;
    }
    
    if (workoutTab) {
        const workoutCount = document.querySelectorAll('#workouts .dashboard-card').length;
        workoutTab.innerHTML += ` <span class="badge bg-success">${workoutCount}</span>`;
    }
    
    if (nutritionTab) {
        const nutritionCount = document.querySelectorAll('#nutrition .dashboard-card').length;
        nutritionTab.innerHTML += ` <span class="badge bg-info">${nutritionCount}</span>`;
    }
    
    if (supplementTab) {
        const supplementCount = document.querySelectorAll('#supplements .dashboard-card').length;
        supplementTab.innerHTML += ` <span class="badge bg-warning">${supplementCount}</span>`;
    }
}

function addAdvancedFilters() {
    const filterContainer = document.createElement('div');
    filterContainer.className = 'advanced-filters mb-3';
    filterContainer.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="levelFilter">
                    <option value="">All Levels</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Strength">Strength</option>
                    <option value="Cardio">Cardio</option>
                    <option value="Cricket-Specific">Cricket-Specific</option>
                    <option value="General Health">General Health</option>
                    <option value="Performance">Performance</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="safetyFilter">
                    <option value="">All Safety Levels</option>
                    <option value="Very Safe">Very Safe</option>
                    <option value="Generally Safe">Generally Safe</option>
                    <option value="Use With Caution">Use With Caution</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="clearAllFilters()">
                    Clear Filters
                </button>
            </div>
        </div>
    `;
    
    // Insert after filter tabs
    const filterTabs = document.querySelector('.filter-tabs');
    if (filterTabs) {
        filterTabs.parentNode.insertBefore(filterContainer, filterTabs.nextSibling);
    }
    
    // Bind filter events
    document.getElementById('levelFilter').addEventListener('change', applyAdvancedFilters);
    document.getElementById('categoryFilter').addEventListener('change', applyAdvancedFilters);
    document.getElementById('safetyFilter').addEventListener('change', applyAdvancedFilters);
}

function applyAdvancedFilters() {
    const levelFilter = document.getElementById('levelFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const safetyFilter = document.getElementById('safetyFilter').value;
    
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
        let show = true;
        
        if (levelFilter && !card.textContent.includes(levelFilter)) {
            show = false;
        }
        
        if (categoryFilter && !card.textContent.includes(categoryFilter)) {
            show = false;
        }
        
        if (safetyFilter && !card.textContent.includes(safetyFilter)) {
            show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

function clearAllFilters() {
    document.getElementById('levelFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('safetyFilter').value = '';
    
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
        card.style.display = 'block';
    });
}

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

function showToast(message, type = 'success') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+F or Cmd+F for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.getElementById('contentSearch');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to close modals or clear search
    if (e.key === 'Escape') {
        clearSearch();
    }
});

// Export functions for global access
window.trainerPlansEnhanced = {
    recordContentView,
    toggleFavorite,
    performSearch,
    clearSearch,
    viewContent,
    printModal,
    shareContent,
    showToast
};