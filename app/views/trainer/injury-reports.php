<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/injury-reports.css">

<!-- Trainer Layout -->
<div class="trainer-layout">
    <!-- Trainer Sidebar -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-dumbbell"></i>
                <h3>Trainer Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link active">
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
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                        <i class="fas fa-user-injured"></i>
                        <span>Injury Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Supplement plans</span>
                    </a>
                </li>
                
                 <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/nutients" class="nav-link">
                        <i class="fas fa-capsules"></i>
                        <span>Nutrition plans</span>
                    </a>
                </li>
              
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workouts" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Workout plans</span>
                    </a>
                </li>
                
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/reports" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Progress Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Trainer Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer'; ?></div>
            <div class="profile-role">Physical Trainer</div>
            <a href="<?php echo URLROOT; ?>/trainer/profile" class="action-btn" style="margin-top: 10px;">
                <i class="fas fa-user-cog"></i> Profile
            </a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-user-injured"></i> Player Injury Reports</h1>
                <p>View and monitor all player medical records and injury reports for training assessment</p>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($data['medical_records']); ?></span>
                    <span class="stat-label">Total Records</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">
                        <?php 
                        $activeInjuries = 0;
                        foreach($data['medical_records'] as $record) {
                            if(in_array($record->RecoveryStatus, ['ongoing', 'recovering'])) {
                                $activeInjuries++;
                            }
                        }
                        echo $activeInjuries;
                        ?>
                    </span>
                    <span class="stat-label">Active Cases</span>
                </div>
            </div>
        </div>

        <?php flash('injury_message'); ?>

        <!-- Filter and Search Controls -->
        <div class="controls-section">
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="statusFilter">Filter by Status:</label>
                    <select id="statusFilter" class="form-control">
                        <option value="all">All Records</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="recovering">Recovering</option>
                        <option value="recovered">Fully Recovered</option>
                        <option value="chronic">Chronic</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="playerSearch">Search Player:</label>
                    <input type="text" id="playerSearch" class="form-control" placeholder="Search by player name...">
                </div>
                <div class="filter-group">
                    <label for="dateFilter">Filter by Date:</label>
                    <select id="dateFilter" class="form-control">
                        <option value="all">All Time</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">Last 3 Months</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Medical Records Table -->
        <div class="schedule-card">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-notes-medical"></i> All Player Medical Records</h2>
                    <span class="access-badge">
                        <i class="fas fa-eye"></i> Read-Only Access
                    </span>
                </div>
            </div>
            <div class="card-content">
                <?php if (!empty($data['medical_records'])): ?>
                    <div class="table-responsive">
                        <table class="dashboard-table" id="medicalRecordsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Player Name</th>
                                    <th>Injury Details</th>
                                    <th>Diagnosis</th>
                                    <th>Treatment</th>
                                    <th>Recovery Status</th>
                                    <th>Verify Status</th>
                                    <th>Reported By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['medical_records'] as $record): ?>
                                <tr class="medical-record-row" data-status="<?php echo strtolower($record->RecoveryStatus); ?>" data-player="<?php echo strtolower($record->player_name ?? 'unknown'); ?>" data-date="<?php echo $record->ReportedDate; ?>">
                                    <td>
                                        <div class="table-cell-primary"><?php echo date('M d', strtotime($record->ReportedDate)); ?></div>
                                        <div class="table-cell-secondary"><?php echo date('Y', strtotime($record->ReportedDate)); ?></div>
                                    </td>
                                    <td>
                                        <div class="player-info">
                                            <div class="player-avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="player-details">
                                                <div class="table-cell-title"><?php echo htmlspecialchars($record->player_name ?? 'Unknown Player'); ?></div>
                                                <div class="table-cell-secondary">Player ID: <?php echo $record->PlayerID; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="injury-details">
                                            <div class="table-cell-title"><?php echo htmlspecialchars($record->InjuryDetails); ?></div>
                                            <?php if(strlen($record->InjuryDetails) > 50): ?>
                                                <button class="btn-link" onclick="expandDetails(<?php echo $record->RecordID; ?>, 'injury')">
                                                    <i class="fas fa-expand-alt"></i> View Full
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($record->Diagnosis); ?></div>
                                        <?php if(strlen($record->Diagnosis) > 30): ?>
                                            <button class="btn-link" onclick="expandDetails(<?php echo $record->RecordID; ?>, 'diagnosis')">
                                                <i class="fas fa-expand-alt"></i> More
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-cell-secondary">
                                            <?php 
                                            $treatment = $record->TreatmentGiven ?? 'Not specified';
                                            echo htmlspecialchars(strlen($treatment) > 30 ? substr($treatment, 0, 30) . '...' : $treatment); 
                                            ?>
                                        </div>
                                        <?php if(strlen($record->TreatmentGiven ?? '') > 30): ?>
                                            <button class="btn-link" onclick="expandDetails(<?php echo $record->RecordID; ?>, 'treatment')">
                                                <i class="fas fa-expand-alt"></i> More
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="table-badge status-<?php echo strtolower($record->RecoveryStatus); ?>">
                                            <?php echo ucfirst($record->RecoveryStatus); ?>
                                        </span>
                                       
                                    </td>
                                    <td>
                                        <span class="table-badge verify-<?php echo strtolower($record->verifyStatus ?? 'pending'); ?>">
                                            <?php echo ucfirst($record->verifyStatus ?? 'Pending'); ?>
                                        </span>
                                        
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($record->reported_by_name ?? 'Self'); ?></div>
                                        <div class="table-cell-secondary"><?php echo htmlspecialchars($record->reported_by_role ?? 'Player'); ?></div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-sm btn-primary" onclick="viewFullRecord(<?php echo $record->RecordID; ?>)" title="View Full Record">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="btn-sm btn-success" onclick="updateVerifyStatus(<?php echo $record->RecordID; ?>, '<?php echo $record->verifyStatus ?? 'pending'; ?>')" title="Update Verify Status">
                                                <i class="fas fa-check-double"></i> Verify
                                            </button>
                                            
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <h3>No Medical Records Found</h3>
                        <p>There are currently no medical records in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-summary">
            <div class="summary-card">
                <h3><i class="fas fa-chart-pie"></i> Recovery Status Overview</h3>
                <div class="status-breakdown">
                    <?php
                    $statusCounts = [
                        'ongoing' => 0,
                        'recovering' => 0,
                        'recovered' => 0,
                        'chronic' => 0
                    ];
                    
                    foreach($data['medical_records'] as $record) {
                        $status = strtolower($record->RecoveryStatus);
                        if(isset($statusCounts[$status])) {
                            $statusCounts[$status]++;
                        }
                    }
                    ?>
                    
                    <div class="status-item">
                        <span class="status-color ongoing"></span>
                        <span class="status-label">Ongoing</span>
                        <span class="status-count"><?php echo $statusCounts['ongoing']; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-color recovering"></span>
                        <span class="status-label">Recovering</span>
                        <span class="status-count"><?php echo $statusCounts['recovering']; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-color recovered"></span>
                        <span class="status-label">Recovered</span>
                        <span class="status-count"><?php echo $statusCounts['recovered']; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-color chronic"></span>
                        <span class="status-label">Chronic</span>
                        <span class="status-count"><?php echo $statusCounts['chronic']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Full Record Modal -->
<div id="fullRecordModal" class="modal" style="display: none;">
    <div class="modal-content large">
        <div class="modal-header">
            <h3><i class="fas fa-file-medical"></i> Medical Record Details</h3>
            <span class="close" onclick="closeFullRecordModal()">&times;</span>
        </div>
        <div class="modal-body" id="fullRecordContent">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeFullRecordModal()">Close</button>
        </div>
    </div>
</div>

<!-- Update Verify Status Modal -->
<div id="verifyStatusModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-check-double"></i> Update Verification Status</h3>
            <span class="close" onclick="closeVerifyStatusModal()">&times;</span>
        </div>
        <form id="verifyStatusForm">
            <div class="modal-body">
                <input type="hidden" id="verifyRecordId" name="record_id">
                
                <div class="form-group">
                    <label for="verifyStatus">Verification Status *</label>
                    <select id="verifyStatus" name="verify_status" class="form-control" required>
                        <option value="">Select verification status...</option>
                        <option value="pending">Pending Review</option>
                        <option value="verified">Verified - Approved</option>
                        <option value="rejected">Rejected - Needs Review</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="verifyComments">Verification Comments</label>
                    <textarea id="verifyComments" name="verify_comments" class="form-control" rows="3" 
                            placeholder="Add comments about your verification decision (optional)..."></textarea>
                </div>
                
                <div class="form-group">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Verification Guidelines:</strong>
                        <ul style="margin-top: 5px; margin-bottom: 0;">
                            <li><strong>Verified:</strong> Medical record is accurate and complete</li>
                            <li><strong>Pending:</strong> Needs more information or review</li>
                            <li><strong>Rejected:</strong> Contains errors or requires correction</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeVerifyStatusModal()">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-double"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', filterRecords);
    document.getElementById('playerSearch').addEventListener('input', filterRecords);
    document.getElementById('dateFilter').addEventListener('change', filterRecords);

    function filterRecords() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const playerSearch = document.getElementById('playerSearch').value.toLowerCase();
        const dateFilter = document.getElementById('dateFilter').value;
        const rows = document.querySelectorAll('.medical-record-row');

        rows.forEach(row => {
            const status = row.dataset.status;
            const player = row.dataset.player;
            const date = new Date(row.dataset.date);
            const now = new Date();
            
            let showRow = true;

            // Status filter
            if (statusFilter !== 'all' && status !== statusFilter) {
                showRow = false;
            }

            // Player search
            if (playerSearch && !player.includes(playerSearch)) {
                showRow = false;
            }

            // Date filter
            if (dateFilter !== 'all') {
                const timeDiff = now - date;
                const daysDiff = timeDiff / (1000 * 3600 * 24);
                
                switch(dateFilter) {
                    case 'week':
                        if (daysDiff > 7) showRow = false;
                        break;
                    case 'month':
                        if (daysDiff > 30) showRow = false;
                        break;
                    case 'quarter':
                        if (daysDiff > 90) showRow = false;
                        break;
                }
            }

            row.style.display = showRow ? 'table-row' : 'none';
        });
    }

    // View full record
    function viewFullRecord(recordId) {
        // This would fetch full record details via AJAX in a real implementation
        const modal = document.getElementById('fullRecordModal');
        const content = document.getElementById('fullRecordContent');
        
        content.innerHTML = `
            <div class="record-details">
                <p><strong>Record ID:</strong> ${recordId}</p>
                <p><em>Full record details would be loaded here via AJAX...</em></p>
            </div>
        `;
        
        modal.style.display = 'block';
    }

    function closeFullRecordModal() {
        document.getElementById('fullRecordModal').style.display = 'none';
    }

    

    function closeTrainerNoteModal() {
        document.getElementById('trainerNoteModal').style.display = 'none';
        document.getElementById('trainerNoteForm').reset();
    }

    // Update verify status
    function updateVerifyStatus(recordId, currentStatus) {
        document.getElementById('verifyRecordId').value = recordId;
        document.getElementById('verifyStatus').value = currentStatus;
        document.getElementById('verifyStatusModal').style.display = 'block';
    }

    function closeVerifyStatusModal() {
        document.getElementById('verifyStatusModal').style.display = 'none';
        document.getElementById('verifyStatusForm').reset();
    }

    // Expand details
    function expandDetails(recordId, type) {
        alert(`Expanding ${type} details for record ${recordId} - This would show full details in a real implementation`);
    }

    // Handle trainer note form submission
    document.getElementById('trainerNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const recordId = document.getElementById('noteRecordId').value;
        const note = document.getElementById('trainerNote').value;
        const recommendation = document.getElementById('trainingRecommendation').value;
        
        // This would submit via AJAX in a real implementation
        alert(`Trainer note saved for record ${recordId}!\nNote: ${note}\nRecommendation: ${recommendation}`);
        
        closeTrainerNoteModal();
    });

    // Handle verify status form submission
    document.getElementById('verifyStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const recordId = document.getElementById('verifyRecordId').value;
        const verifyStatus = document.getElementById('verifyStatus').value;
        const verifyComments = document.getElementById('verifyComments').value;
        
        // Submit via AJAX
        updateMedicalRecordVerifyStatus(recordId, verifyStatus, verifyComments);
    });

    // AJAX function to update verify status
    function updateMedicalRecordVerifyStatus(recordId, verifyStatus, comments) {
        const formData = new FormData();
        formData.append('record_id', recordId);
        formData.append('verify_status', verifyStatus);
        formData.append('verify_comments', comments);
        
        fetch('<?php echo URLROOT; ?>/trainer/updateVerifyStatus', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the table row
                updateTableVerifyStatus(recordId, verifyStatus);
                closeVerifyStatusModal();
                
                // Show success message
                showNotification('Verification status updated successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to update verification status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while updating the verification status', 'error');
        });
    }

    // Update table row verify status
    function updateTableVerifyStatus(recordId, newStatus) {
        const rows = document.querySelectorAll('.medical-record-row');
        rows.forEach(row => {
            const viewButton = row.querySelector(`button[onclick*="viewFullRecord(${recordId})"]`);
            if (viewButton) {
                const verifyCell = row.cells[6]; // Verify status column (0-indexed)
                
                // Update status badge
                const statusBadge = verifyCell.querySelector('.table-badge');
                statusBadge.className = `table-badge verify-${newStatus.toLowerCase()}`;
                statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                
                // Update status icon
                const statusIndicator = verifyCell.querySelector('.status-indicator');
                let iconClass = '';
                switch(newStatus.toLowerCase()) {
                    case 'pending':
                        iconClass = 'fas fa-clock text-warning';
                        break;
                    case 'verified':
                        iconClass = 'fas fa-check-circle text-success';
                        break;
                    case 'rejected':
                        iconClass = 'fas fa-times-circle text-danger';
                        break;
                }
                statusIndicator.innerHTML = `<i class="${iconClass}"></i>`;
                
                // Update the verify button to reflect new status
                const verifyButton = row.querySelector(`button[onclick*="updateVerifyStatus(${recordId}"]`);
                verifyButton.setAttribute('onclick', `updateVerifyStatus(${recordId}, '${newStatus}')`);
            }
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const fullRecordModal = document.getElementById('fullRecordModal');
        const trainerNoteModal = document.getElementById('trainerNoteModal');
        const verifyStatusModal = document.getElementById('verifyStatusModal');
        
        if (event.target === fullRecordModal) {
            closeFullRecordModal();
        }
        if (event.target === trainerNoteModal) {
            closeTrainerNoteModal();
        }
        if (event.target === verifyStatusModal) {
            closeVerifyStatusModal();
        }
    }

    // Initialize Universal Sidebar for trainer
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the universal sidebar with trainer-specific config
        new UniversalSidebar({
            sidebarId: 'trainerSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent',
            sidebarClass: 'trainer-sidebar'
        });
    });
</script>

<script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
</body>
</html>