<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-rentals.css">

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
                    <a href="<?php echo URLROOT; ?>/shop/inventory" class="nav-link">
                        <i class="fas fa-warehouse"></i>
                        <span>Inventory</span>
                    </a>
                </li>
                
                <li class="nav-item active">
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
            <h1><i class="fas fa-tools"></i> Equipment Rental Management</h1>
            <p>Manage cricket equipment rentals, track returns, and handle inspections</p>
        </div>

        <!-- Rental Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-content">
                    <h3>Active Rentals</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">24</span>
                            <span class="label">Currently Rented</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-content">
                    <h3>Overdue Returns</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number urgent">3</span>
                            <span class="label">Need Follow-up</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="card-content">
                    <h3>Available Equipment</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">45</span>
                            <span class="label">Ready to Rent</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #6B73FF, #8B83FF);">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="card-content">
                    <h3>Today's Revenue</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">₨ 12,400</span>
                            <span class="label">Rental Income</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="action-section">
            <div class="action-cards">
                <div class="action-card" onclick="openNewRentalModal()">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>New Rental</h4>
                    <p>Process equipment rental</p>
                </div>
                
                <div class="action-card" onclick="openEquipmentModal()">
                    <div class="action-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h4>Add Equipment</h4>
                    <p>Register new equipment</p>
                </div>
                
                <div class="action-card" onclick="processReturns()">
                    <div class="action-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h4>Process Returns</h4>
                    <p>Handle equipment returns</p>
                </div>
                
                <div class="action-card" onclick="generateRentalReport()">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4>Rental Report</h4>
                    <p>Generate analytics</p>
                </div>
            </div>
        </div>

        <!-- Rental Filters -->
        <div class="filter-section" style="margin: 2rem 0;">
            <div class="filter-tabs" style="display: flex; gap: 0.5rem; background: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 15px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
                <a href="#" class="filter-tab active" data-status="all" style="flex: 1; padding: 12px 20px; text-decoration: none; color: white; background: #4A90E2; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-th"></i> All Rentals
                </a>
                <a href="#" class="filter-tab" data-status="active" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-play"></i> Active
                </a>
                <a href="#" class="filter-tab" data-status="overdue" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-clock"></i> Overdue
                </a>
                <a href="#" class="filter-tab" data-status="returned" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-check"></i> Returned
                </a>
                <a href="#" class="filter-tab" data-status="cancelled" style="flex: 1; padding: 12px 20px; text-decoration: none; color: #666; background: transparent; border-radius: 10px; text-align: center; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-times"></i> Cancelled
                </a>
            </div>
        </div>

        <!-- Rentals Table -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Equipment Rentals</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search rentals..." id="rentalSearch">
                    <select class="filter-dropdown" id="categoryFilter">
                        <option value="all">All Categories</option>
                        <option value="batting">Batting</option>
                        <option value="bowling">Bowling</option>
                        <option value="protective">Protective</option>
                        <option value="training">Training</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportRentals()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-content">
                <table id="rentalsTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Rental ID</th>
                            <th>Equipment</th>
                            <th>Player</th>
                            <th>Rental Period</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Return Due</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="table-cell-primary">#REN-2025-156</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Professional Cricket Bat</div>
                                <div class="table-cell-details">Category: Batting | Condition: Good</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Ashen Perera</div>
                                <div class="table-cell-details">ashen@example.com</div>
                            </td>
                            <td>
                                <div class="table-cell-primary">Oct 18, 2025</div>
                                <div class="table-cell-secondary">2 days</div>
                            </td>
                            <td>
                                <div class="table-cell-primary">₨ 800/day</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="table-badge status-active">Active</span>
                            </td>
                            <td>
                                <span class="due-date today">Today 6:00 PM</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewRental(156)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-success" onclick="processReturn(156)">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="extendRental(156)">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="table-cell-primary">#REN-2025-155</div>
                            </td>
                            <td>
                                <div class="table-cell-title">Bowling Machine</div>
                                <div class="table-cell-details">Category: Training | Condition: Good</div>
                            </td>
                            <td>
                                <div>
                                    <strong>Kavinda Silva</strong><br>
                                    <small>kavinda@example.com</small>
                                </div>
                            </td>
                            <td>Oct 16, 2025<br><small>3 days</small></td>
                            <td>₨ 1,500/day</td>
                            <td>
                                <span class="status-badge status-overdue">Overdue</span>
                            </td>
                            <td>
                                <span class="due-date overdue">Oct 17, 6:00 PM</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewRental(155)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="contactPlayer(155)">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="calculateLateFee(155)">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#REN-2025-154</td>
                            <td>
                                <div>
                                    <strong>Cricket Helmet Set</strong><br>
                                    <small>Category: Protective | Condition: New</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>Nimal Fernando</strong><br>
                                    <small>nimal@example.com</small>
                                </div>
                            </td>
                            <td>Oct 15, 2025<br><small>1 day</small></td>
                            <td>₨ 600/day</td>
                            <td>
                                <span class="status-badge status-returned">Returned</span>
                            </td>
                            <td>
                                <span class="due-date completed">Returned on time</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewRental(154)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="printReceipt(154)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn-small btn-info" onclick="viewInspectionReport(154)">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Equipment Inventory Section -->
        <div class="data-table" style="margin-top: 2rem;">
            <div class="table-header">
                <h3><i class="fas fa-tools"></i> Equipment Inventory</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search equipment..." id="equipmentSearch">
                    <select class="filter-dropdown" id="equipmentCategoryFilter">
                        <option value="all">All Categories</option>
                        <option value="batting">Batting</option>
                        <option value="bowling">Bowling</option>
                        <option value="protective">Protective</option>
                        <option value="training">Training</option>
                    </select>
                </div>
            </div>
            
            <div class="table-content">
                <table id="equipmentTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Category</th>
                            <th>Condition</th>
                            <th>Rental Price</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div>
                                    <strong>Professional Cricket Bat #001</strong><br>
                                    <small>Gray-Nicolls | English Willow</small>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge category-batting">Batting</span>
                            </td>
                            <td>
                                <span class="condition-badge condition-good">Good</span>
                            </td>
                            <td>₨ 800/day</td>
                            <td>
                                <span class="equipment-status status-available">Available</span>
                            </td>
                            <td>Storage Room A</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="rentEquipment(1)">
                                        <i class="fas fa-handshake"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="editEquipment(1)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="maintenanceMode(1)">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <strong>Bowling Machine #002</strong><br>
                                    <small>BOLA | Professional Grade</small>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge category-training">Training</span>
                            </td>
                            <td>
                                <span class="condition-badge condition-excellent">Excellent</span>
                            </td>
                            <td>₨ 1,500/day</td>
                            <td>
                                <span class="equipment-status status-rented">Rented</span>
                            </td>
                            <td>Field B</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-info" onclick="trackRental(2)">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="editEquipment(2)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="scheduleInspection(2)">
                                        <i class="fas fa-calendar"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <strong>Cricket Helmet #003</strong><br>
                                    <small>Masuri | Titanium Grille</small>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge category-protective">Protective</span>
                            </td>
                            <td>
                                <span class="condition-badge condition-new">New</span>
                            </td>
                            <td>₨ 600/day</td>
                            <td>
                                <span class="equipment-status status-available">Available</span>
                            </td>
                            <td>Storage Room B</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="rentEquipment(3)">
                                        <i class="fas fa-handshake"></i>
                                    </button>
                                    <button class="btn-small btn-secondary" onclick="editEquipment(3)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-small btn-info" onclick="viewDetails(3)">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Rental Modal -->
        <div id="newRentalModal" class="modal" style="display: none;">
            <div class="modal-content large-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-plus"></i> New Equipment Rental</h3>
                    <span class="close" onclick="closeNewRentalModal()">&times;</span>
                </div>
                <form id="newRentalForm" class="modal-body">
                    <div class="form-grid">
                        <div class="form-section">
                            <h4>Player Information</h4>
                            <div class="form-group">
                                <label for="playerId">Select Player</label>
                                <select id="playerId" name="playerId" required>
                                    <option value="">Choose Player</option>
                                    <option value="1">Ashen Perera - P001</option>
                                    <option value="2">Kavinda Silva - P002</option>
                                    <option value="3">Nimal Fernando - P003</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="playerContact">Contact Number</label>
                                <input type="tel" id="playerContact" name="playerContact" readonly>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4>Rental Details</h4>
                            <div class="form-group">
                                <label for="equipmentId">Equipment</label>
                                <select id="equipmentId" name="equipmentId" required>
                                    <option value="">Select Equipment</option>
                                    <option value="1" data-price="800">Professional Cricket Bat #001 - ₨800/day</option>
                                    <option value="3" data-price="600">Cricket Helmet #003 - ₨600/day</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="rentalDays">Rental Days</label>
                                    <input type="number" id="rentalDays" name="rentalDays" min="1" max="30" value="1" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="returnDate">Return Date</label>
                                    <input type="date" id="returnDate" name="returnDate" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4>Cost Calculation</h4>
                        <div class="cost-breakdown">
                            <div class="cost-item">
                                <span>Daily Rate:</span>
                                <span id="dailyRate">₨ 0</span>
                            </div>
                            <div class="cost-item">
                                <span>Number of Days:</span>
                                <span id="totalDays">1</span>
                            </div>
                            <div class="cost-item">
                                <span>Security Deposit:</span>
                                <span id="securityDeposit">₨ 0</span>
                            </div>
                            <div class="cost-item total">
                                <span><strong>Total Cost:</strong></span>
                                <span id="totalCost"><strong>₨ 0</strong></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4>Additional Information</h4>
                        <div class="form-group">
                            <label for="rentalNotes">Notes (Optional)</label>
                            <textarea id="rentalNotes" name="rentalNotes" rows="3" placeholder="Any special instructions or notes..."></textarea>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeNewRentalModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="newRentalForm">Process Rental</button>
                </div>
            </div>
        </div>

        <!-- Equipment Return Modal -->
        <div id="returnModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-undo"></i> Process Equipment Return</h3>
                    <span class="close" onclick="closeReturnModal()">&times;</span>
                </div>
                <form id="returnForm" class="modal-body">
                    <div class="form-group">
                        <label for="returnRentalId">Rental ID</label>
                        <input type="text" id="returnRentalId" name="returnRentalId" readonly>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="returnDate">Return Date & Time</label>
                            <input type="datetime-local" id="returnDate" name="returnDate" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="equipmentCondition">Equipment Condition</label>
                            <select id="equipmentCondition" name="equipmentCondition" required>
                                <option value="">Assess Condition</option>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" id="lateFeeSection" style="display: none;">
                        <label for="lateFee">Late Fee</label>
                        <input type="number" id="lateFee" name="lateFee" step="0.01" placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="returnNotes">Inspection Notes</label>
                        <textarea id="returnNotes" name="returnNotes" rows="4" placeholder="Document equipment condition, any damage, etc..."></textarea>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeReturnModal()">Cancel</button>
                    <button type="submit" class="btn btn-success" form="returnForm">Complete Return</button>
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
        filterRentalsByStatus(this.dataset.status);
    });
});

// Search functionality
document.getElementById('rentalSearch').addEventListener('input', function() {
    filterRentals(this.value);
});

document.getElementById('equipmentSearch').addEventListener('input', function() {
    filterEquipment(this.value);
});

// Category filter for rentals table
const categoryFilter = document.getElementById('categoryFilter');
if (categoryFilter) {
    categoryFilter.addEventListener('change', function() {
        const category = this.value.toLowerCase();
        const table = document.getElementById('rentalsTable');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (category === 'all') {
                row.style.display = '';
            } else {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(category) ? '' : 'none';
            }
        });
    });
}

// Category filter for equipment table
const equipmentCategoryFilter = document.getElementById('equipmentCategoryFilter');
if (equipmentCategoryFilter) {
    equipmentCategoryFilter.addEventListener('change', function() {
        const category = this.value.toLowerCase();
        const table = document.getElementById('equipmentTable');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (category === 'all') {
                row.style.display = '';
            } else {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(category) ? '' : 'none';
            }
        });
    });
}

// Cost calculation for new rental
document.getElementById('equipmentId').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const price = option.dataset.price || 0;
    document.getElementById('dailyRate').textContent = `₨ ${price}`;
    calculateTotal();
});

document.getElementById('rentalDays').addEventListener('input', function() {
    document.getElementById('totalDays').textContent = this.value;
    calculateTotal();
    
    // Set return date
    const startDate = new Date();
    startDate.setDate(startDate.getDate() + parseInt(this.value));
    document.getElementById('returnDate').value = startDate.toISOString().split('T')[0];
});

function calculateTotal() {
    const dailyRate = parseInt(document.getElementById('dailyRate').textContent.replace(/[^\d]/g, '')) || 0;
    const days = parseInt(document.getElementById('rentalDays').value) || 1;
    const securityDeposit = dailyRate * 0.5; // 50% of daily rate as security
    const total = (dailyRate * days) + securityDeposit;
    
    document.getElementById('securityDeposit').textContent = `₨ ${securityDeposit}`;
    document.getElementById('totalCost').innerHTML = `<strong>₨ ${total}</strong>`;
}

function filterRentalsByStatus(status) {
    const table = document.getElementById('rentalsTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
            return;
        }
        // Check for status badge with matching class
        const badge = row.querySelector('.table-badge, .status-badge');
        if (badge) {
            const hasStatus = badge.classList.contains('status-' + status) || 
                             badge.textContent.trim().toLowerCase() === status;
            row.style.display = hasStatus ? '' : 'none';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterRentals(searchTerm) {
    const table = document.getElementById('rentalsTable');
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

function filterEquipment(searchTerm) {
    const table = document.getElementById('equipmentTable');
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

// Modal functions
function openNewRentalModal() {
    // Set default return date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('returnDate').value = tomorrow.toISOString().split('T')[0];
    document.getElementById('newRentalModal').style.display = 'block';
}

function closeNewRentalModal() {
    document.getElementById('newRentalModal').style.display = 'none';
    document.getElementById('newRentalForm').reset();
}

function openEquipmentModal() {
    showNotification('Equipment management modal will be implemented', 'info');
}

function processReturns() {
    showNotification('Showing overdue rentals for processing', 'info');
    document.querySelector('[data-status="overdue"]').click();
}

function generateRentalReport() {
    showNotification('Generating rental analytics report', 'success');
}

// Rental actions
function viewRental(rentalId) {
    console.log('Viewing rental:', rentalId);
    showNotification(`Viewing rental #${rentalId}`, 'info');
}

function processReturn(rentalId) {
    console.log('Processing return for rental:', rentalId);
    document.getElementById('returnRentalId').value = `#REN-2025-${rentalId}`;
    
    // Set current date and time
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('returnDate').value = now.toISOString().slice(0, 16);
    
    document.getElementById('returnModal').style.display = 'block';
}

function closeReturnModal() {
    document.getElementById('returnModal').style.display = 'none';
    document.getElementById('returnForm').reset();
}

function extendRental(rentalId) {
    console.log('Extending rental:', rentalId);
    showNotification(`Rental #${rentalId} extended for additional day`, 'success');
}

function contactPlayer(rentalId) {
    console.log('Contacting player for rental:', rentalId);
    showNotification(`Reminder sent to player for rental #${rentalId}`, 'info');
}

function calculateLateFee(rentalId) {
    console.log('Calculating late fee for rental:', rentalId);
    showNotification(`Late fee calculated: ₨150 for rental #${rentalId}`, 'warning');
}

function printReceipt(rentalId) {
    console.log('Printing receipt for rental:', rentalId);
    window.print();
}

function viewInspectionReport(rentalId) {
    console.log('Viewing inspection report for rental:', rentalId);
    showNotification(`Inspection report loaded for rental #${rentalId}`, 'info');
}

// Equipment actions
function rentEquipment(equipmentId) {
    console.log('Renting equipment:', equipmentId);
    // Pre-select equipment in new rental modal
    openNewRentalModal();
    document.getElementById('equipmentId').value = equipmentId;
    document.getElementById('equipmentId').dispatchEvent(new Event('change'));
}

function editEquipment(equipmentId) {
    console.log('Editing equipment:', equipmentId);
    showNotification(`Editing equipment #${equipmentId}`, 'info');
}

function maintenanceMode(equipmentId) {
    console.log('Setting maintenance mode for equipment:', equipmentId);
    if (confirm('Put this equipment in maintenance mode?')) {
        showNotification(`Equipment #${equipmentId} set to maintenance mode`, 'warning');
    }
}

function trackRental(equipmentId) {
    console.log('Tracking rental for equipment:', equipmentId);
    showNotification(`Tracking current rental for equipment #${equipmentId}`, 'info');
}

function scheduleInspection(equipmentId) {
    console.log('Scheduling inspection for equipment:', equipmentId);
    showNotification(`Inspection scheduled for equipment #${equipmentId}`, 'info');
}

function viewDetails(equipmentId) {
    console.log('Viewing details for equipment:', equipmentId);
    showNotification(`Equipment details loaded for #${equipmentId}`, 'info');
}

function exportRentals() {
    console.log('Exporting rentals');
    showNotification('Rental data exported successfully', 'success');
}

// Form submissions
document.getElementById('newRentalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    console.log('New rental:', Object.fromEntries(formData));
    showNotification('Equipment rental processed successfully', 'success');
    closeNewRentalModal();
});

document.getElementById('returnForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    console.log('Equipment return:', Object.fromEntries(formData));
    showNotification('Equipment return processed successfully', 'success');
    closeReturnModal();
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

/* Due date indicators */
.due-date {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
}

.due-date.today { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.due-date.overdue { background: rgba(244, 67, 54, 0.2); color: #c62828; }
.due-date.completed { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }

/* Condition badges */
.condition-badge {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
}

.condition-new { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.condition-excellent { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.condition-good { background: rgba(74, 144, 226, 0.2); color: #1976d2; }
.condition-fair { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.condition-poor { background: rgba(244, 67, 54, 0.2); color: #c62828; }

/* Equipment status */
.equipment-status {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-available { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.status-rented { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.status-maintenance { background: rgba(156, 39, 176, 0.2); color: #7b1fa2; }
.status-damaged { background: rgba(244, 67, 54, 0.2); color: #c62828; }

/* Rental status badges */
.status-active { background: rgba(74, 144, 226, 0.2); color: #1976d2; }
.status-overdue { background: rgba(244, 67, 54, 0.2); color: #c62828; }
.status-returned { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.status-cancelled { background: rgba(156, 39, 176, 0.2); color: #7b1fa2; }

/* Cost breakdown */
.cost-breakdown {
    background: rgba(74, 144, 226, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    border: 1px solid rgba(74, 144, 226, 0.1);
}

.cost-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.cost-item:last-child {
    border-bottom: none;
}

.cost-item.total {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #4A90E2;
    font-size: 1.1rem;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="<?php echo URLROOT; ?>/js/admin/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>