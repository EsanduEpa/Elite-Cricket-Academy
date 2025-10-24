<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
<!-- Mobile-specific meta tags -->
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

    <!-- Trainer Layout -->
    <div class="player-layout">
        <!-- Left Sidebar Panel -->
        <div class="trainer-sidebar" id="trainerSidebar">
            <div class="sidebar-header">
                <div class="trainer-logo">
                    <i class="fas fa-user-tie"></i>
                    <h3>Trainer Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedule & Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Workout Plans</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                            <i class="fas fa-apple-alt"></i>
                            <span>Nutrition Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Profile Section -->
            <div class="trainer-profile">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-name"><?php echo $_SESSION['username'] ?? 'John Trainer'; ?></div>
                <div class="trainer-role">Fitness Trainer</div>
                <div class="profile-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                        <i class="fas fa-user-cog"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-apple-alt"></i> Nutrition Plans Management</h1>
                        <p>Create and manage customized nutrition plans for your trainees</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-training" onclick="openAddNutritionPlanModal()">
                            <i class="fas fa-plus"></i>Add New Plan
                        </button>
                        <button class="btn btn-refresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            <div class="current-time"><?php echo date('H:i'); ?></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('nutrition_message'); ?>

            <!-- Nutrition Plans Section -->
            <div class="schedule-card">
                <!-- Controls Bar -->
                <div class="controls-bar">
                    <div class="view-controls">
                        <button class="view-btn active" data-filter="all">
                            <i class="fas fa-th-list"></i> All Plans
                        </button>
                        <button class="view-btn" data-filter="active">
                            <i class="fas fa-check-circle"></i> Active
                        </button>
                        <button class="view-btn" data-filter="inactive">
                            <i class="fas fa-pause-circle"></i> Inactive
                        </button>
                    </div>
                    <div class="search-controls">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="nutritionSearch" placeholder="Search nutrition plans or players..." />
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="table-container">
                    <table class="dashboard-table" id="nutritionTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-apple-alt"></i> Plan Details</th>
                                <th><i class="fas fa-user"></i> Player</th>
                                <th><i class="fas fa-utensils"></i> Diet Details</th>
                                <th><i class="fas fa-calendar"></i> Duration</th>
                                <th><i class="fas fa-chart-line"></i> Status</th>
                                <th><i class="fas fa-clock"></i> Created</th>
                                <th><i class="fas fa-cogs"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['nutrition_plans'])): ?>
                                <?php foreach ($data['nutrition_plans'] as $plan): ?>
                                    <tr class="nutrition-row" data-status="<?php echo strtolower($plan->Status); ?>">
                                        <td class="plan-details">
                                            <div class="plan-info">
                                                <div class="plan-icon">
                                                    <i class="fas fa-apple-alt"></i>
                                                </div>
                                                <div class="plan-text">
                                                    <strong>Nutrition Plan #<?php echo $plan->PlanID; ?></strong>
                                                    <span class="plan-description">Custom Nutrition Plan</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="player-info">
                                            <div class="player-avatar">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <div class="player-details">
                                                <span class="player-name"><?php echo htmlspecialchars($plan->player_name ?? 'Unknown Player'); ?></span>
                                                <span class="player-email"><?php echo htmlspecialchars($plan->player_email ?? ''); ?></span>
                                            </div>
                                        </td>
                                        <td class="diet-details">
                                            <div class="diet-preview">
                                                <?php 
                                                $details = htmlspecialchars($plan->DietDetails);
                                                echo strlen($details) > 80 ? substr($details, 0, 80) . '...' : $details; 
                                                ?>
                                                <?php if(strlen($plan->DietDetails) > 80): ?>
                                                    <button class="view-more-btn" onclick="viewPlanDetails(<?php echo $plan->PlanID; ?>, 'nutrition')">
                                                        <i class="fas fa-expand-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="duration-info">
                                            <div class="duration-display">
                                                <i class="fas fa-hourglass-half"></i>
                                                <span><?php echo $plan->Duration; ?> days</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($plan->Status); ?>">
                                                <i class="fas fa-<?php echo $plan->Status === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                                <?php echo ucfirst($plan->Status); ?>
                                            </span>
                                        </td>
                                        <td class="date-info">
                                            <div class="date-display">
                                                <span class="date"><?php echo date('M j', strtotime($plan->CreatedDate)); ?></span>
                                                <span class="year"><?php echo date('Y', strtotime($plan->CreatedDate)); ?></span>
                                            </div>
                                        </td>
                                        <td class="actions-cell">
                                            <div class="profile-actions">
                                                <button class="profile-action view" onclick="viewPlanDetails(<?php echo $plan->PlanID; ?>, 'nutrition')" title="View Plan Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="profile-action edit" onclick="editPlan(<?php echo $plan->PlanID; ?>, 'nutrition')" title="Edit Plan">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="profile-action delete" onclick="deletePlan(<?php echo $plan->PlanID; ?>, 'nutrition')" title="Delete Plan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="empty-state">
                                    <td colspan="7">
                                        <div class="empty-content">
                                            <div class="empty-icon">
                                                <i class="fas fa-apple-alt"></i>
                                            </div>
                                            <h3>No Nutrition Plans Found</h3>
                                            <p>Start by creating your first nutrition plan for your trainees</p>
                                            <button class="btn btn-primary" onclick="openAddNutritionPlanModal()">
                                                <i class="fas fa-plus"></i> Create First Plan
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>

<!-- Add Nutrition Plan Modal -->
<div id="addNutritionPlanModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-apple-alt"></i>
            </div>
            <div class="header-text">
                <h3>Create Nutrition Plan</h3>
                <p>Design a personalized nutrition plan for your trainee</p>
            </div>
            <button class="modal-close" onclick="closeModal('addNutritionPlanModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="<?php echo URLROOT; ?>/trainer/addNutritionPlan" class="modal-form">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="player_id" class="form-label">
                            <i class="fas fa-user"></i> Select Player
                        </label>
                        <select id="player_id" name="player_id" class="form-input" required>
                            <option value="">Choose a player...</option>
                            <?php if(isset($data['players'])): ?>
                                <?php foreach($data['players'] as $player): ?>
                                    <option value="<?php echo $player->UserID; ?>">
                                        <?php echo htmlspecialchars($player->name); ?> - <?php echo htmlspecialchars($player->email); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="diet_details" class="form-label">
                            <i class="fas fa-utensils"></i> Diet Details & Instructions
                        </label>
                        <textarea id="diet_details" name="diet_details" class="form-input" rows="8" required 
                                placeholder="Provide comprehensive nutrition plan details:&#13;&#10;• Meal timing and portions&#13;&#10;• Macronutrient breakdown&#13;&#10;• Special dietary requirements&#13;&#10;• Hydration guidelines&#13;&#10;• Pre/post workout nutrition"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="duration" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Duration (Days)
                        </label>
                        <input type="number" id="duration" name="duration" class="form-input" required 
                               min="1" max="365" placeholder="e.g., 30 days">
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">
                            <i class="fas fa-toggle-on"></i> Plan Status
                        </label>
                        <select id="status" name="status" class="form-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="info-content">
                        <h4>Nutrition Plan Best Practices</h4>
                        <ul>
                            <li>Consider the player's training intensity and schedule</li>
                            <li>Include specific meal timing relative to workouts</li>
                            <li>Account for individual dietary preferences and restrictions</li>
                            <li>Provide clear portion sizes and measurement guidelines</li>
                            <li>Include hydration recommendations and timing</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addNutritionPlanModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Nutrition Plan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Plan Details Modal -->
<div id="viewPlanModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="header-text">
                <h3>Nutrition Plan Details</h3>
                <p>Complete nutrition plan information and guidelines</p>
            </div>
            <button class="modal-close" onclick="closeModal('viewPlanModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div id="viewPlanContent" class="plan-details-content">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('viewPlanModal')">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div id="editPlanModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header gradient-header">
            <div class="header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div class="header-text">
                <h3>Edit Nutrition Plan</h3>
                <p>Update the nutrition plan details and guidelines</p>
            </div>
            <button class="modal-close" onclick="closeModal('editPlanModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editPlanForm" class="modal-form">
            <div class="modal-body">
                <div id="editPlanContent" class="form-grid">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editPlanModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize filter functionality
    initializeFilters();
    
    // Initialize modal handlers
    initializeModals();
});

// Search Functionality
function initializeSearch() {
    const searchInput = document.getElementById('nutritionSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#nutritionTable tbody .nutrition-row');
            
            tableRows.forEach(row => {
                const playerName = row.querySelector('.player-name')?.textContent.toLowerCase() || '';
                const dietDetails = row.querySelector('.diet-preview')?.textContent.toLowerCase() || '';
                const planText = row.querySelector('.plan-text strong')?.textContent.toLowerCase() || '';
                
                const matches = playerName.includes(searchTerm) || 
                               dietDetails.includes(searchTerm) || 
                               planText.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
            });
            
            updateEmptyState();
        });
    }
}

// Filter Functionality
function initializeFilters() {
    const filterButtons = document.querySelectorAll('.view-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter table rows
            const filter = this.getAttribute('data-filter');
            filterNutritionPlans(filter);
        });
    });
}

function filterNutritionPlans(filter) {
    const tableRows = document.querySelectorAll('#nutritionTable tbody .nutrition-row');
    
    tableRows.forEach(row => {
        const status = row.getAttribute('data-status');
        
        if (filter === 'all') {
            row.style.display = '';
        } else {
            row.style.display = status === filter ? '' : 'none';
        }
    });
    
    updateEmptyState();
}

function updateEmptyState() {
    const visibleRows = document.querySelectorAll('#nutritionTable tbody .nutrition-row[style=""], #nutritionTable tbody .nutrition-row:not([style*="none"])');
    const emptyState = document.querySelector('.empty-state');
    
    if (visibleRows.length === 0 && !emptyState) {
        // Show no results message
        const tbody = document.querySelector('#nutritionTable tbody');
        const noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results';
        noResultsRow.innerHTML = `
            <td colspan="7">
                <div class="empty-content">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>No Plans Found</h3>
                    <p>No nutrition plans match your current search or filter criteria</p>
                </div>
            </td>
        `;
        tbody.appendChild(noResultsRow);
    } else if (visibleRows.length > 0) {
        // Remove no results message if it exists
        const noResults = document.querySelector('.no-results');
        if (noResults) {
            noResults.remove();
        }
    }
}

// Modal Functions
function initializeModals() {
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
}

function openAddNutritionPlanModal() {
    openModal('addNutritionPlanModal');
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input, select, textarea');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form if it exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Plan Management Functions
function viewPlanDetails(planId, type) {
    const modal = document.getElementById('viewPlanModal');
    const content = document.getElementById('viewPlanContent');
    
    content.innerHTML = `
        <div class="loading-state">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p>Loading nutrition plan details...</p>
        </div>
    `;
    
    openModal('viewPlanModal');
    
    // Simulate API call - replace with actual AJAX request
    setTimeout(() => {
        content.innerHTML = `
            <div class="plan-overview">
                <div class="plan-header">
                    <div class="plan-icon">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                    <div class="plan-title">
                        <h4>Nutrition Plan #${planId}</h4>
                        <span class="plan-type">Custom Nutrition Plan</span>
                    </div>
                </div>
                
                <div class="plan-info-grid">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <span class="label">Player:</span>
                        <span class="value">John Doe</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span class="label">Duration:</span>
                        <span class="value">30 days</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-chart-line"></i>
                        <span class="label">Status:</span>
                        <span class="value status-active">Active</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span class="label">Created:</span>
                        <span class="value">${new Date().toLocaleDateString()}</span>
                    </div>
                </div>
                
                <div class="plan-details">
                    <h5><i class="fas fa-utensils"></i> Diet Details</h5>
                    <div class="details-content">
                        <p><em>Detailed nutrition plan information would be displayed here from the database...</em></p>
                        <p>This would include meal timing, portions, macronutrient breakdown, and specific dietary guidelines.</p>
                    </div>
                </div>
            </div>
        `;
    }, 800);
}

function editPlan(planId, type) {
    const modal = document.getElementById('editPlanModal');
    const content = document.getElementById('editPlanContent');
    
    content.innerHTML = `
        <div class="loading-state">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p>Loading plan data for editing...</p>
        </div>
    `;
    
    openModal('editPlanModal');
    
    // Simulate loading plan data - replace with actual AJAX request
    setTimeout(() => {
        content.innerHTML = `
            <div class="form-group full-width">
                <label for="edit_player_id" class="form-label">
                    <i class="fas fa-user"></i> Player
                </label>
                <select id="edit_player_id" name="player_id" class="form-input" required>
                    <option value="1" selected>John Doe - john@example.com</option>
                    <!-- Add more players here -->
                </select>
            </div>

            <div class="form-group full-width">
                <label for="edit_diet_details" class="form-label">
                    <i class="fas fa-utensils"></i> Diet Details
                </label>
                <textarea id="edit_diet_details" name="diet_details" class="form-input" rows="8" required>Sample nutrition plan details...</textarea>
            </div>

            <div class="form-group">
                <label for="edit_duration" class="form-label">
                    <i class="fas fa-calendar-alt"></i> Duration (Days)
                </label>
                <input type="number" id="edit_duration" name="duration" class="form-input" value="30" required min="1" max="365">
            </div>

            <div class="form-group">
                <label for="edit_status" class="form-label">
                    <i class="fas fa-toggle-on"></i> Status
                </label>
                <select id="edit_status" name="status" class="form-input">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        `;
    }, 800);
}

function deletePlan(planId, type) {
    if (confirm(`Are you sure you want to delete this ${type} plan? This action cannot be undone.`)) {
        // In a real implementation, this would make an AJAX request to delete the plan
        alert(`${type} plan ${planId} would be deleted (not implemented yet)`);
        
        // Optionally refresh the page or remove the row from the table
        // location.reload();
    }
}

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    }
});
</script>

   
</script>

<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
</body>
</html>