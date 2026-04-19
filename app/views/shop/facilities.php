<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/shop/shop-facilities.css">

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
                
                                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/reviews" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span>Reviews & Feedback</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/shop/facilities" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Facility Management</span>
                    </a>
                </li>                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/shop/counter" class="nav-link">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Counter Booking</span>
                    </a>
                </li>            </ul>
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
            <h1><i class="fas fa-building"></i> Facility Management</h1>
            <p>Manage cricket facilities, bookings, and maintenance schedules</p>
        </div>

        <!-- Facility Statistics -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4A90E2, #5BA0F2);">
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-content">
                    <h3>Total Facilities</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo isset($data['totalFacilities']) ? (int)$data['totalFacilities'] : 0; ?></span>
                            <span class="label">Available Facilities</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #4ECDC4, #5EDDD4);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-content">
                    <h3>Today's Bookings</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number"><?php echo isset($data['todaysBookingCount']) ? (int)$data['todaysBookingCount'] : 0; ?></span>
                            <span class="label">Active Sessions</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(45deg, #FF8A50, #FFB366);">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="card-content">
                    <h3>Maintenance</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number <?php echo ((int)$data['facilitiesInMaintenance'] > 0) ? 'urgent' : ''; ?>"><?php echo isset($data['facilitiesInMaintenance']) ? (int)$data['facilitiesInMaintenance'] : 0; ?></span>
                            <span class="label">Needs Attention</span>
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
                            <span class="number">₨ <?php echo isset($data['todaysRevenue']) ? number_format($data['todaysRevenue'], 0) : 0; ?></span>
                            <span class="label">Facility Bookings</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="action-section">
            <div class="action-cards">
                <div class="action-card" onclick="openNewBookingModal()">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>New Booking</h4>
                    <p>Book facility for player</p>
                </div>
                
                <div class="action-card" onclick="openFacilityModal()">
                    <div class="action-icon">
                        <i class="fas fa-building-plus"></i>
                    </div>
                    <h4>Add Facility</h4>
                    <p>Register new facility</p>
                </div>
                
                <div class="action-card" onclick="viewSchedule()">
                    <div class="action-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <h4>View Schedule</h4>
                    <p>Today's facility schedule</p>
                </div>
                
                <div class="action-card" onclick="generateFacilityReport()">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Usage Report</h4>
                    <p>Facility analytics</p>
                </div>
            </div>
        </div>

        <!-- Facility Status Grid -->
        <div class="facility-grid">
            <h3><i class="fas fa-th-large"></i> Facility Status Overview</h3>
            <div class="grid-container">
                <?php
                $facilities = $data['facilities'] ?? [];
                if (!empty($facilities)) {
                    foreach ($facilities as $facility) {
                        $id = $facility->FacilityID ?? '';
                        $name = $facility->Name ?? 'Unknown Facility';
                        $capacity = $facility->Capacity ?? 0;
                        $status = strtolower($facility->AvailabilityStatus ?? 'available');
                        $rate = $facility->HourlyRate ?? 0;

                        // Determine status class
                        $statusClass = 'available';
                        $statusDisplay = 'Available';
                        if ($status === 'occupied') {
                            $statusClass = 'occupied';
                            $statusDisplay = 'Occupied';
                        } elseif ($status === 'maintenance') {
                            $statusClass = 'maintenance';
                            $statusDisplay = 'Maintenance';
                        }
                ?>
                <div class="facility-card <?php echo $statusClass; ?>" onclick="viewFacilityDetails(<?php echo $id; ?>)">
                    <div class="facility-header">
                        <h4><?php echo htmlspecialchars($name); ?></h4>
                        <span class="facility-status status-<?php echo $statusClass; ?>"><?php echo $statusDisplay; ?></span>
                    </div>
                    <div class="facility-info">
                        <p><i class="fas fa-users"></i> Capacity: <?php echo (int)$capacity; ?> players</p>
                        <p><i class="fas fa-rupee-sign"></i> ₨ <?php echo number_format((float)$rate, 0); ?>/hour</p>
                        <?php if (!empty($facility->Location)): ?>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($facility->Location); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="facility-actions">
                        <?php if ($status === 'available'): ?>
                            <button class="btn-small btn-primary" onclick="quickBook(<?php echo $id; ?>, event)">
                                <i class="fas fa-calendar-plus"></i> Book Now
                            </button>
                        <?php elseif ($status === 'occupied'): ?>
                            <button class="btn-small btn-secondary" onclick="viewBooking(<?php echo $id; ?>, event)">
                                <i class="fas fa-eye"></i> View Booking
                            </button>
                        <?php else: ?>
                            <button class="btn-small btn-warning" onclick="scheduleMaintenanceEnd(<?php echo $id; ?>, event)">
                                <i class="fas fa-calendar-check"></i> Schedule End
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                    }
                } else {
                ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                    <p style="color: #7f8c8d;">No facilities found in the system.</p>
                </div>
                <?php
                }
                ?>
            </div>
        </div>

        <!-- Facility Bookings Table -->
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-calendar-alt"></i> Today's Facility Bookings</h3>
                <div class="table-actions">
                    <input type="text" class="search-box" placeholder="Search bookings..." id="bookingSearch">
                    <select class="filter-dropdown" id="facilityFilter">
                        <option value="all">All Facilities</option>
                        <option value="nets">Practice Nets</option>
                        <option value="ground">Main Ground</option>
                        <option value="indoor">Indoor Facilities</option>
                        <option value="gym">Gymnasium</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportBookings()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-content">
                <table id="bookingsTable" class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Facility</th>
                            <th>Player</th>
                            <th>Time Slot</th>
                            <th>Duration</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bookings = $data['todaysBookings'] ?? [];
                        if (!empty($bookings)) {
                            foreach ($bookings as $booking) {
                                $bookingId = $booking->FacilityBookingID ?? '';
                                $facilityName = $booking->facility_name ?? 'Unknown Facility';
                                $playerName = $booking->player_name ?? 'Unknown Player';
                                $playerEmail = $booking->player_email ?? 'N/A';
                                $startTime = $booking->StartTime ?? '';
                                $endTime = $booking->EndTime ?? '';
                                $totalCost = (float)($booking->TotalCost ?? 0);

                                // Format start and end times
                                $startDisplay = date('g:i A', strtotime($startTime));
                                $endDisplay = date('g:i A', strtotime($endTime));

                                // Calculate duration in hours
                                $start = strtotime($startTime);
                                $end = strtotime($endTime);
                                $durationHours = ($end - $start) / 3600;
                                $durationLabel = $durationHours == 1 ? '1 hour' : $durationHours . ' hours';
                        ?>
                        <tr>
                            <td>
                                <div class="table-cell-primary">#FB-<?php echo str_pad($bookingId, 6, '0', STR_PAD_LEFT); ?></div>
                            </td>
                            <td>
                                <div class="table-cell-title"><?php echo htmlspecialchars($facilityName); ?></div>
                                <div class="table-cell-details"><?php echo htmlspecialchars($booking->Location ?? 'N/A'); ?></div>
                            </td>
                            <td>
                                <div class="table-cell-title"><?php echo htmlspecialchars($playerName); ?></div>
                                <div class="table-cell-details"><?php echo htmlspecialchars($playerEmail); ?></div>
                            </td>
                            <td>
                                <div class="table-cell-primary"><?php echo $startDisplay; ?> - <?php echo $endDisplay; ?></div>
                            </td>
                            <td>
                                <div class="table-cell-primary"><?php echo $durationLabel; ?></div>
                            </td>
                            <td>
                                <div class="table-cell-primary">₨ <?php echo number_format($totalCost, 0); ?></div>
                            </td>
                            <td style="text-align: center;">
                                <?php
                                $rawStatus = strtolower((string)($booking->Status ?? 'confirmed'));
                                $statusClass = match ($rawStatus) {
                                    'confirmed' => 'status-confirmed',
                                    'attended', 'completed' => 'status-completed',
                                    'missed', 'not_attended' => 'status-cancelled',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-active',
                                };
                                $statusLabel = match ($rawStatus) {
                                    'attended', 'completed' => 'Completed',
                                    'missed', 'not_attended' => 'Not Attended',
                                    default => ucwords(str_replace('_', ' ', $rawStatus)),
                                };
                                ?>
                                <span class="table-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-primary" onclick="viewBookingDetails(<?php echo $bookingId; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-small btn-warning" onclick="extendBooking(<?php echo $bookingId; ?>)">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button class="btn-small btn-danger" onclick="cancelBooking(<?php echo $bookingId; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox"></i> No facility bookings for today
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Booking Modal -->
        <div id="newBookingModal" class="modal" style="display: none;">
            <div class="modal-content large-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-calendar-plus"></i> New Facility Booking</h3>
                    <span class="close" onclick="closeNewBookingModal()">&times;</span>
                </div>
                <form id="newBookingForm" class="modal-body">
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
                            
                            <div class="form-group">
                                <label for="groupSize">Group Size</label>
                                <input type="number" id="groupSize" name="groupSize" min="1" max="25" value="1" required>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4>Booking Details</h4>
                            <div class="form-group">
                                <label for="facilityId">Facility</label>
                                <select id="facilityId" name="facilityId" required>
                                    <option value="">Select Facility</option>
                                    <option value="1" data-rate="2000" data-capacity="8">Practice Net 1 - ₨2,000/hour</option>
                                    <option value="3" data-rate="3500" data-capacity="4">Bowling Machine Area - ₨3,500/hour</option>
                                    <option value="5" data-rate="2500" data-capacity="15">Indoor Training Hall - ₨2,500/hour</option>
                                    <option value="6" data-rate="4000" data-capacity="20">Gymnasium - ₨4,000/hour</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bookingDate">Date</label>
                                    <input type="date" id="bookingDate" name="bookingDate" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bookingDuration">Duration (hours)</label>
                                    <select id="bookingDuration" name="bookingDuration" required>
                                        <option value="0.5">30 minutes</option>
                                        <option value="1" selected>1 hour</option>
                                        <option value="1.5">1.5 hours</option>
                                        <option value="2">2 hours</option>
                                        <option value="3">3 hours</option>
                                        <option value="4">4 hours</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="startTime">Start Time</label>
                                    <input type="time" id="startTime" name="startTime" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="endTime">End Time</label>
                                    <input type="time" id="endTime" name="endTime" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4>Cost Calculation</h4>
                        <div class="cost-breakdown">
                            <div class="cost-item">
                                <span>Hourly Rate:</span>
                                <span id="hourlyRate">₨ 0</span>
                            </div>
                            <div class="cost-item">
                                <span>Duration:</span>
                                <span id="totalDuration">1 hour</span>
                            </div>
                            <div class="cost-item">
                                <span>Base Cost:</span>
                                <span id="baseCost">₨ 0</span>
                            </div>
                            <div class="cost-item" id="groupDiscountSection" style="display: none;">
                                <span>Group Discount (10%):</span>
                                <span id="groupDiscount">₨ 0</span>
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
                            <label for="bookingNotes">Special Requirements (Optional)</label>
                            <textarea id="bookingNotes" name="bookingNotes" rows="3" placeholder="Any special equipment needs, setup requirements, etc..."></textarea>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeNewBookingModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="newBookingForm">Confirm Booking</button>
                </div>
            </div>
        </div>

        <!-- Add Facility Modal -->
        <div id="addFacilityModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-building-plus"></i> Add New Facility</h3>
                    <span class="close" onclick="closeFacilityModal()">&times;</span>
                </div>
                <form id="addFacilityForm" class="modal-body">
                    <div class="form-group">
                        <label for="facilityName">Facility Name</label>
                        <input type="text" id="facilityName" name="facilityName" required placeholder="e.g., Practice Net 3">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="facilityLocation">Location</label>
                            <input type="text" id="facilityLocation" name="facilityLocation" placeholder="e.g., East Wing">
                        </div>
                        
                        <div class="form-group">
                            <label for="facilityCapacity">Capacity</label>
                            <input type="number" id="facilityCapacity" name="facilityCapacity" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="facilityRate">Hourly Rate (₨)</label>
                        <input type="number" id="facilityRate" name="facilityRate" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="facilityDescription">Description</label>
                        <textarea id="facilityDescription" name="facilityDescription" rows="3" placeholder="Facility features, equipment included, etc..."></textarea>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeFacilityModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="addFacilityForm">Add Facility</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript -->
<script>
// Auto-calculate end time and costs
document.getElementById('startTime').addEventListener('change', calculateEndTime);
document.getElementById('bookingDuration').addEventListener('change', function() {
    calculateEndTime();
    calculateCost();
    document.getElementById('totalDuration').textContent = `${this.value} hour${this.value != 1 ? 's' : ''}`;
});

document.getElementById('facilityId').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const rate = option.dataset.rate || 0;
    const capacity = option.dataset.capacity || 0;
    
    document.getElementById('hourlyRate').textContent = `₨ ${rate}`;
    document.getElementById('groupSize').max = capacity;
    calculateCost();
});

document.getElementById('groupSize').addEventListener('input', function() {
    calculateCost();
    
    // Show group discount for 5+ people
    if (parseInt(this.value) >= 5) {
        document.getElementById('groupDiscountSection').style.display = 'flex';
    } else {
        document.getElementById('groupDiscountSection').style.display = 'none';
    }
});

function calculateEndTime() {
    const startTime = document.getElementById('startTime').value;
    const duration = parseFloat(document.getElementById('bookingDuration').value);
    
    if (startTime && duration) {
        const [hours, minutes] = startTime.split(':').map(Number);
        const endDate = new Date();
        endDate.setHours(hours, minutes + (duration * 60));
        
        const endTimeString = endDate.toTimeString().substr(0, 5);
        document.getElementById('endTime').value = endTimeString;
    }
}

function calculateCost() {
    const rate = parseInt(document.getElementById('hourlyRate').textContent.replace(/[^\d]/g, '')) || 0;
    const duration = parseFloat(document.getElementById('bookingDuration').value) || 1;
    const groupSize = parseInt(document.getElementById('groupSize').value) || 1;
    
    const baseCost = rate * duration;
    let discount = 0;
    
    // Apply group discount for 5+ people
    if (groupSize >= 5) {
        discount = baseCost * 0.1;
        document.getElementById('groupDiscount').textContent = `₨ ${discount}`;
    }
    
    const totalCost = baseCost - discount;
    
    document.getElementById('baseCost').textContent = `₨ ${baseCost}`;
    document.getElementById('totalCost').innerHTML = `<strong>₨ ${totalCost}</strong>`;
}

// Set today's date as default
document.getElementById('bookingDate').value = new Date().toISOString().split('T')[0];

// Modal functions
function openNewBookingModal() {
    document.getElementById('newBookingModal').style.display = 'block';
}

function closeNewBookingModal() {
    document.getElementById('newBookingModal').style.display = 'none';
    document.getElementById('newBookingForm').reset();
}

function openFacilityModal() {
    document.getElementById('addFacilityModal').style.display = 'block';
}

function closeFacilityModal() {
    document.getElementById('addFacilityModal').style.display = 'none';
    document.getElementById('addFacilityForm').reset();
}

// Quick actions
function viewSchedule() {
    showNotification('Opening today\'s facility schedule', 'info');
}

function generateFacilityReport() {
    showNotification('Generating facility usage report', 'success');
}

// Facility grid actions
function viewFacilityDetails(facilityId) {
    console.log('Viewing facility details:', facilityId);
    showNotification(`Viewing details for facility #${facilityId}`, 'info');
}

function quickBook(facilityId, event) {
    event.stopPropagation();
    console.log('Quick booking for facility:', facilityId);
    openNewBookingModal();
    document.getElementById('facilityId').value = facilityId;
    document.getElementById('facilityId').dispatchEvent(new Event('change'));
}

function viewBooking(facilityId, event) {
    event.stopPropagation();
    console.log('Viewing current booking for facility:', facilityId);
    showNotification(`Viewing current booking for facility #${facilityId}`, 'info');
}

function scheduleMaintenanceEnd(facilityId, event) {
    event.stopPropagation();
    console.log('Scheduling maintenance end for facility:', facilityId);
    showNotification(`Maintenance end scheduled for facility #${facilityId}`, 'success');
}

// Booking table actions
function viewBookingDetails(bookingId) {
    console.log('Viewing booking details:', bookingId);
    showNotification(`Viewing booking details #${bookingId}`, 'info');
}

function extendBooking(bookingId) {
    console.log('Extending booking:', bookingId);
    if (confirm('Extend this booking by 30 minutes?')) {
        showNotification(`Booking #${bookingId} extended`, 'success');
    }
}

function cancelBooking(bookingId) {
    console.log('Cancelling booking:', bookingId);
    if (confirm('Are you sure you want to cancel this booking?')) {
        showNotification(`Booking #${bookingId} cancelled`, 'warning');
    }
}

function checkInPlayer(bookingId) {
    console.log('Checking in player for booking:', bookingId);
    showNotification(`Player checked in for booking #${bookingId}`, 'success');
}

function editBooking(bookingId) {
    console.log('Editing booking:', bookingId);
    showNotification(`Opening edit form for booking #${bookingId}`, 'info');
}

function viewGroupDetails(bookingId) {
    console.log('Viewing group details for booking:', bookingId);
    showNotification(`Viewing group training details #${bookingId}`, 'info');
}

function addPlayerToGroup(bookingId) {
    console.log('Adding player to group booking:', bookingId);
    showNotification(`Adding player to group booking #${bookingId}`, 'info');
}

function exportBookings() {
    console.log('Exporting bookings');
    showNotification('Booking data exported successfully', 'success');
}

// Search functionality
document.getElementById('bookingSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('bookingsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Facility filter
const facilityFilter = document.getElementById('facilityFilter');
if (facilityFilter) {
    facilityFilter.addEventListener('change', function() {
        const facility = this.value.toLowerCase();
        const table = document.getElementById('bookingsTable');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (facility === 'all') {
                row.style.display = '';
            } else {
                const facilityCell = row.querySelectorAll('td')[1];
                const facilityText = facilityCell ? facilityCell.textContent.toLowerCase() : '';
                row.style.display = facilityText.includes(facility) ? '' : 'none';
            }
        });
    });
}

// Form submissions
document.getElementById('newBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    console.log('New booking:', Object.fromEntries(formData));
    showNotification('Facility booking confirmed successfully', 'success');
    closeNewBookingModal();
});

document.getElementById('addFacilityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    console.log('New facility:', Object.fromEntries(formData));
    showNotification('New facility added successfully', 'success');
    closeFacilityModal();
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
/* Facility Grid Styles */
.facility-grid {
    margin: 2rem 0;
}

.facility-grid h3 {
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.facility-card {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 15px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.facility-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.facility-card.available {
    border-left: 4px solid #4ECDC4;
}

.facility-card.occupied {
    border-left: 4px solid #FF8A50;
}

.facility-card.maintenance {
    border-left: 4px solid #FF6B6B;
}

.facility-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.facility-header h4 {
    margin: 0;
    color: #333;
    font-size: 1.2rem;
}

.facility-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-available {
    background: rgba(78, 205, 196, 0.2);
    color: #4ECDC4;
}

.status-occupied {
    background: rgba(255, 138, 80, 0.2);
    color: #FF8A50;
}

.status-maintenance {
    background: rgba(255, 107, 107, 0.2);
    color: #FF6B6B;
}

.facility-info {
    margin: 1rem 0;
}

.facility-info p {
    margin: 0.5rem 0;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.facility-info i {
    width: 16px;
    color: #4A90E2;
}

.facility-actions {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

/* Booking Status Badges */
.booking-status {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-active { background: rgba(74, 144, 226, 0.2); color: #1976d2; }
.status-confirmed { background: rgba(76, 175, 80, 0.2); color: #2e7d32; }
.status-cancelled { background: rgba(244, 67, 54, 0.2); color: #c62828; }
.status-completed { background: rgba(156, 39, 176, 0.2); color: #7b1fa2; }

/* Cost breakdown (reused from rentals) */
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
    .grid-container {
        grid-template-columns: 1fr;
    }
    
    .facility-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>