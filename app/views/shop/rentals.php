<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-rentals.css">

<?php
    $escape = function ($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    };

    $rentals = $data['rentals'] ?? [];
    $returns = $data['returns'] ?? [];
    $stats = $data['rental_stats'] ?? [];

    $fmtMoney = function ($amount) {
        return number_format((float)$amount, 2);
    };

    $fmtDate = function ($value, $format) {
        if (empty($value)) return '-';
        try {
            return (new DateTime((string)$value))->format($format);
        } catch (Exception $e) {
            return '-';
        }
    };

    $rentalLabel = function ($rentalId, $rentalDate) use ($fmtDate) {
        $year = $fmtDate($rentalDate, 'Y');
        if ($year === '-') {
            $year = date('Y');
        }
        return '#REN-' . $year . '-' . (int)$rentalId;
    };

    $durationDays = function ($start, $end) {
        if (empty($start) || empty($end)) return '-';
        try {
            $startDt = new DateTime((string)$start);
            $endDt = new DateTime((string)$end);
            $days = (int)$startDt->diff($endDt)->format('%a');
            return max(1, $days);
        } catch (Exception $e) {
            return '-';
        }
    };

    $now = new DateTime();
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

        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['user_name']) ? $escape($data['user_name']) : 'Shop Manager'; ?></div>
            <div class="profile-role">Shop Employee</div>
            <a href="<?php echo URLROOT; ?>/shop/profile" class="action-btn" style="margin-top: 10px;">
                <i class="fas fa-user-cog"></i> Profile
            </a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <h1><i class="fas fa-tools"></i> Equipment Rental Management</h1>
            <p>Manage equipment rentals, returns, and inspections</p>
        </div>

        <?php flash('shop_rental_message'); ?>

        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-content">
                    <h3>Active Rentals</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo (int)($stats['active_rentals'] ?? 0); ?></span>
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
                            <span class="number urgent"><?php echo (int)($stats['overdue_rentals'] ?? 0); ?></span>
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
                            <span class="number"><?php echo (int)($stats['available_equipment'] ?? 0); ?></span>
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
                            <span class="number">₨ <?php echo $fmtMoney((float)($stats['todays_revenue'] ?? 0)); ?></span>
                            <span class="label">Rental Income</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        <?php if (!empty($rentals)) : ?>
                            <?php foreach ($rentals as $rental) : ?>
                                <?php
                                    $rid = (int)($rental->RentalID ?? 0);
                                    $status = strtolower((string)($rental->Status ?? 'active'));
                                    $endTime = (string)($rental->EndTime ?? '');
                                    $isOverdue = false;
                                    if ($endTime !== '' && in_array($status, ['active','overdue'], true)) {
                                        try {
                                            $isOverdue = (new DateTime($endTime)) < $now;
                                        } catch (Exception $e) {
                                            $isOverdue = false;
                                        }
                                    }
                                    $statusClass = $status;
                                    if ($status === 'active' && $isOverdue) {
                                        $statusClass = 'overdue';
                                    }
                                ?>
                                <tr data-rental-id="<?php echo $rid; ?>">
                                    <td>
                                        <div class="table-cell-primary"><?php echo $escape($rentalLabel($rid, $rental->RentalDate ?? '')); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo $escape($rental->equipment_name ?? 'Equipment'); ?></div>
                                        <div class="table-cell-details">Category: <?php echo $escape($rental->Category ?? '-'); ?> | Condition: <?php echo $escape($rental->EqCondition ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo $escape($rental->renter_name ?? 'Player'); ?></div>
                                        <div class="table-cell-details"><?php echo $escape($rental->renter_email ?? ''); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $escape($fmtDate($rental->StartTime ?? '', 'M j, Y')); ?></div>
                                        <div class="table-cell-secondary"><?php echo $escape($durationDays($rental->StartTime ?? '', $rental->EndTime ?? '')); ?> days</div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">₨ <?php echo $fmtMoney($rental->TotalCost ?? 0); ?></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-<?php echo $escape($statusClass); ?>"><?php echo $escape(ucfirst($statusClass)); ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($rental->EndTime)) : ?>
                                            <span class="due-date <?php echo $isOverdue ? 'overdue' : 'today'; ?>"><?php echo $escape($fmtDate($rental->EndTime, 'M j, g:i A')); ?></span>
                                        <?php else : ?>
                                            <span class="due-date">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (!in_array($status, ['returned','cancelled'], true)) : ?>
                                                <button class="btn-small btn-success js-set-return" type="button" data-rental-id="<?php echo $rid; ?>" title="Add Return">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            <?php else : ?>
                                                <span style="color:#7f8c8d; font-size:0.9rem;">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 1.25rem; color:#7f8c8d;">No rentals found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="data-table" style="margin-top: 2rem;">
            <div class="table-header">
                <h3><i class="fas fa-clipboard-check"></i> Equipment Returns</h3>
                <div class="table-actions">
                    <button class="btn btn-primary" type="button" id="openReturnModalBtn">
                        <i class="fas fa-undo"></i> Add Return
                    </button>
                </div>
            </div>

            <div class="table-content">
                <table id="returnsTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Return ID</th>
                            <th>Rental</th>
                            <th>Equipment</th>
                            <th>Player</th>
                            <th>Returned At</th>
                            <th>Damage</th>
                            <th>Late</th>
                            <th>Total Pay</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($returns)) : ?>
                            <?php foreach ($returns as $ret) : ?>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary">#RET-<?php echo (int)($ret->ReturnID ?? 0); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $escape($rentalLabel((int)($ret->RentalID ?? 0), $ret->RentalDate ?? '')); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo $escape($ret->equipment_name ?? 'Equipment'); ?></div>
                                        <div class="table-cell-details">Category: <?php echo $escape($ret->Category ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo $escape($ret->renter_name ?? 'Player'); ?></div>
                                        <div class="table-cell-details"><?php echo $escape($ret->renter_email ?? ''); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $escape($fmtDate($ret->ReturnedAt ?? '', 'M j, Y g:i A')); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo $escape(str_replace('_', ' ', (string)($ret->DamageStatus ?? 'not_damaged'))); ?></div>
                                        <div class="table-cell-details">₨ <?php echo $fmtMoney($ret->DamageFee ?? 0); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary"><?php echo (int)($ret->DaysLate ?? 0); ?> days</div>
                                        <div class="table-cell-details">₨ <?php echo $fmtMoney($ret->LateFee ?? 0); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">₨ <?php echo $fmtMoney($ret->TotalReturnPay ?? ((float)($ret->LateFee ?? 0) + (float)($ret->DamageFee ?? 0))); ?></div>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php
                                            $payStatus = strtolower((string)($ret->PaymentStatus ?? 'not_required'));
                                            $payBadgeClass = 'status-inactive';
                                            if ($payStatus === 'pending') $payBadgeClass = 'status-pending';
                                            elseif ($payStatus === 'paid') $payBadgeClass = 'status-completed';
                                            elseif ($payStatus === 'refunded') $payBadgeClass = 'status-cancelled';
                                            elseif ($payStatus === 'not_required') $payBadgeClass = 'status-inactive';
                                        ?>
                                        <span class="table-badge <?php echo $escape($payBadgeClass); ?>"><?php echo $escape(ucfirst(str_replace('_', ' ', $payStatus))); ?></span>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="table-badge status-<?php echo $escape((string)($ret->ReturnStatus ?? 'received')); ?>"><?php echo $escape(ucfirst((string)($ret->ReturnStatus ?? 'received'))); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="10" style="text-align:center; padding: 1.25rem; color:#7f8c8d;">No return records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Return Equipment Modal (hidden until opened) -->
<div id="returnModal" class="modal" style="display: none;">
    <div class="modal-content large-modal" style="max-width: 950px;">
        <div class="modal-header">
            <h3><i class="fas fa-undo"></i> Add Return Record</h3>
            <span class="close" id="closeReturnModalBtn">&times;</span>
        </div>

        <form method="POST" action="<?php echo URLROOT; ?>/shop/add_equipment_return" id="equipmentReturnForm" class="modal-body">
            <div class="form-row">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label>Return ID</label>
                    <input type="text" value="Auto" readonly>
                </div>

                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label>Created At</label>
                    <input type="text" value="Auto" readonly>
                </div>

                <div class="form-group" style="flex: 1; min-width: 220px;">
                    <label>Inspected By</label>
                    <input type="text" value="<?php echo $escape($data['user_name'] ?? 'Shop Manager'); ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1; min-width: 240px;">
                    <label for="return_rental_id">Rental</label>
                    <select id="return_rental_id" name="rental_id" required>
                        <option value="">Select a rental...</option>
                        <?php foreach ($rentals as $rental) : ?>
                            <?php
                                $status = strtolower((string)($rental->Status ?? 'active'));
                                if (in_array($status, ['returned','cancelled'], true)) {
                                    continue;
                                }
                                $rid = (int)($rental->RentalID ?? 0);
                            ?>
                            <option value="<?php echo $rid; ?>" data-end-time="<?php echo $escape((string)($rental->EndTime ?? '')); ?>" data-value-price="<?php echo $escape((string)($rental->equipment_value_price ?? '0')); ?>">
                                <?php echo $escape($rentalLabel($rid, $rental->RentalDate ?? '')); ?> — <?php echo $escape($rental->equipment_name ?? 'Equipment'); ?> (<?php echo $escape($rental->renter_name ?? 'Player'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 240px;">
                    <label for="returned_at">Returned At</label>
                    <input type="datetime-local" id="returned_at" name="returned_at" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="return_status">Return Status</label>
                    <select id="return_status" name="return_status" required>
                        <option value="received">Received</option>
                        <option value="inspected">Inspected</option>
                        <option value="completed" selected>Completed</option>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="damage_status">Damage Status</label>
                    <select id="damage_status" name="damage_status" required>
                        <option value="not_damaged" selected>Not Damaged</option>
                        <option value="slight">Slight</option>
                        <option value="moderate">Moderate</option>
                        <option value="high">High</option>
                    </select>
                    <small style="display:block; margin-top: 6px; color:#7f8c8d;">
                        Damage fee is calculated automatically based on equipment value price.
                    </small>
                </div>

                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="" selected>Auto</option>
                        <option value="not_required">Not Required</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                    </select>
                    <small style="display:block; margin-top: 6px; color:#7f8c8d;">
                        If left as Auto, it becomes Pending only when fees apply.
                    </small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label>Damage Fee</label>
                    <input type="number" id="preview_damage_fee" value="0.00" step="0.01" min="0" readonly>
                </div>
                <div class="form-group" style="flex: 1; min-width: 180px;">
                    <label>Days Late</label>
                    <input type="number" id="preview_days_late" value="0" min="0" readonly>
                </div>
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label>Late Fee</label>
                    <input type="number" id="preview_late_fee" value="0.00" step="0.01" min="0" readonly>
                </div>
                <div class="form-group" style="flex: 1; min-width: 220px;">
                    <label>Total Return Pay</label>
                    <input type="number" id="preview_total_return_pay" value="0.00" step="0.01" min="0" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Inspection notes, damage details, etc..."></textarea>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelReturnModalBtn">Cancel</button>
            <button type="submit" class="btn btn-success" form="equipmentReturnForm">
                <i class="fas fa-save"></i> Save Return
            </button>
        </div>
    </div>
</div>

<script>
// Filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterRentalsByStatus(this.dataset.status);
    });
});

// Search
const rentalSearch = document.getElementById('rentalSearch');
if (rentalSearch) {
    rentalSearch.addEventListener('input', function() {
        filterRentals(this.value);
    });
}

// Category filter
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

function filterRentalsByStatus(status) {
    const table = document.getElementById('rentalsTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
            return;
        }
        const badge = row.querySelector('.table-badge');
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
    if (!table) return;
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm.toLowerCase()) ? '' : 'none';
    }
}

// Return modal
(function initReturnModal() {
    const modal = document.getElementById('returnModal');
    const openBtn = document.getElementById('openReturnModalBtn');
    const closeBtn = document.getElementById('closeReturnModalBtn');
    const cancelBtn = document.getElementById('cancelReturnModalBtn');
    const returnedAt = document.getElementById('returned_at');
    const rentalSelect = document.getElementById('return_rental_id');
    const damageStatusSelect = document.getElementById('damage_status');
    const paymentStatusSelect = document.getElementById('payment_status');

    const previewDamageFee = document.getElementById('preview_damage_fee');
    const previewDaysLate = document.getElementById('preview_days_late');
    const previewLateFee = document.getElementById('preview_late_fee');
    const previewTotalReturnPay = document.getElementById('preview_total_return_pay');

    if (!modal) return;

    function setReturnedAtDefault() {
        if (!returnedAt) return;
        if (returnedAt.value) return;
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        returnedAt.value = now.toISOString().slice(0, 16);
    }

    function openModal(preselectRentalId) {
        modal.style.display = 'block';
        setReturnedAtDefault();

        if (rentalSelect && preselectRentalId) {
            rentalSelect.value = String(preselectRentalId);
        }

        if (rentalSelect) {
            rentalSelect.focus();
        }

        updateReturnPreview();
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function computeLateFee(daysLate) {
        const d = Number(daysLate) || 0;
        if (d <= 0) return 0;
        return 750 + (125 * d * (d - 1));
    }

    function updateReturnPreview() {
        if (!rentalSelect) return;

        const selected = rentalSelect.options[rentalSelect.selectedIndex];
        if (!selected) return;

        const endTimeRaw = (selected.dataset && selected.dataset.endTime) ? String(selected.dataset.endTime) : '';
        const valuePriceRaw = (selected.dataset && selected.dataset.valuePrice) ? String(selected.dataset.valuePrice) : '0';

        const returnedAtVal = returnedAt && returnedAt.value ? returnedAt.value : '';
        const damageStatus = damageStatusSelect && damageStatusSelect.value ? damageStatusSelect.value : 'not_damaged';

        const valuePrice = parseFloat(valuePriceRaw) || 0;

        let damageFee = 0;
        if (damageStatus === 'moderate') {
            damageFee = valuePrice * 0.5;
        } else if (damageStatus === 'high') {
            damageFee = valuePrice * 0.8;
        } else {
            damageFee = 0;
        }
        damageFee = Math.max(0, Math.round(damageFee * 100) / 100);

        let daysLate = 0;
        if (endTimeRaw && returnedAtVal) {
            const endAsIso = endTimeRaw.includes('T') ? endTimeRaw : endTimeRaw.replace(' ', 'T');
            const end = new Date(endAsIso);
            const ret = new Date(returnedAtVal);
            if (!Number.isNaN(end.getTime()) && !Number.isNaN(ret.getTime()) && ret > end) {
                const diffMs = ret.getTime() - end.getTime();
                daysLate = Math.ceil(diffMs / 86400000);
            }
        }

        const lateFee = Math.max(0, Math.round(computeLateFee(daysLate) * 100) / 100);
        const totalPay = Math.max(0, Math.round((damageFee + lateFee) * 100) / 100);

        if (previewDamageFee) previewDamageFee.value = damageFee.toFixed(2);
        if (previewDaysLate) previewDaysLate.value = String(daysLate);
        if (previewLateFee) previewLateFee.value = lateFee.toFixed(2);
        if (previewTotalReturnPay) previewTotalReturnPay.value = totalPay.toFixed(2);

        if (paymentStatusSelect && paymentStatusSelect.value === '') {
            // keep Auto selected; no forced changes
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', function () {
            openModal();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.querySelectorAll('.js-set-return').forEach(btn => {
        btn.addEventListener('click', function () {
            openModal(this.dataset.rentalId);
        });
    });

    if (rentalSelect) {
        rentalSelect.addEventListener('change', updateReturnPreview);
    }
    if (returnedAt) {
        returnedAt.addEventListener('change', updateReturnPreview);
    }
    if (damageStatusSelect) {
        damageStatusSelect.addEventListener('change', updateReturnPreview);
    }
})();
 </script>

<style>
.filter-tab:hover {
    background: rgba(74, 144, 226, 0.1) !important;
    color: #4A90E2 !important;
}

.filter-tab.active {
    background: #4A90E2 !important;
    color: white !important;
}

.due-date {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
}

.due-date.today { background: rgba(255, 193, 7, 0.2); color: #f57f17; }
.due-date.overdue { background: rgba(244, 67, 54, 0.2); color: #c62828; }

.status-active { background: rgba(74, 144, 226, 0.2); color: #1976d2; }
.status-overdue { background: rgba(244, 67, 54, 0.2); color: #c62828; }
.status-returned { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.status-cancelled { background: rgba(156, 39, 176, 0.2); color: #7b1fa2; }
</style>

<script src="<?php echo URLROOT; ?>/js/admin/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
