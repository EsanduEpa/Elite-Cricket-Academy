<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/injury-reports.css?v=<?php echo time(); ?>">

<!-- Coach Dashboard Layout -->
<div class="coach-layout">
    <!-- Left Sidebar Panel -->
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Coach Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Sessions</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health & Injury</span>
                    </a>
                </li>
                
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                        <i class="fas fa-calendar"></i>
                        <span>Events</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-heartbeat"></i> Player Health & Injury Reports</h1>
                    <p>Review and verify player injury reports and medical records</p>
                </div>
            </div>
        </div>

        <!-- Medical Reports Table -->
        <div class="schedule-card">
            <div class="card-header">
                <div class="header-content">
                    <h2><i class="fas fa-notes-medical"></i> Medical Reports</h2>
                </div>
            </div>
            <div class="card-content">
                <table class="dashboard-table" id="medicalReportsTable">
                    <thead>
                        <tr>
                            <th>Injury Date</th>
                            <th>Player</th>
                            <th>Injury Details</th>
                            <th>Diagnosis</th>
                            <th>At Academy</th>
                            <th>Rest Days</th>
                            <th>Receipt</th>
                            <th>Recovery Status</th>
                            <th>Verify Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['medical_records'])): ?>
                            <?php foreach ($data['medical_records'] as $record): ?>
                                <tr data-record-id="<?php echo $record->RecordID; ?>">
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">
                                            <?php echo date('M d, Y', strtotime($record->InjuryDate)); ?>
                                        </div>
                                        <div class="table-cell-secondary">Reported: <?php echo date('M d', strtotime($record->ReportedDate)); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">
                                            <?php echo htmlspecialchars($record->player_name ?? 'Unknown'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-details">
                                            <?php echo htmlspecialchars($record->InjuryDetails); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-details">
                                            <?php echo htmlspecialchars($record->Diagnosis); ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($record->HappenedAtAcademy == 'yes'): ?>
                                            <span class="table-badge" style="background-color: #ffc107; color: #333;">
                                                <i class="fas fa-school"></i> Yes
                                            </span>
                                        <?php else: ?>
                                            <span class="table-badge" style="background-color: #6c757d; color: white;">
                                                <i class="fas fa-home"></i> No
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary"><?php echo intval($record->RestDaysNeeded); ?></div>
                                        <div class="table-cell-secondary">days</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (!empty($record->DiagnosisReceiptURL)): ?>
                                            <?php 
                                                // Remove 'public/' prefix if exists for correct URL
                                                $receiptPath = str_replace('public/', '', $record->DiagnosisReceiptURL);
                                            ?>
                                            <a href="<?php echo URLROOT . '/' . $receiptPath; ?>" target="_blank" class="btn-sm" style="background: #17a2b8;">
                                                <i class="fas fa-file-alt"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span class="table-cell-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-<?php echo strtolower($record->RecoveryStatus); ?>">
                                            <?php echo htmlspecialchars($record->RecoveryStatus); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge verify-status-<?php echo strtolower($record->verifyStatus); ?>">
                                            <?php echo htmlspecialchars($record->verifyStatus); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="action-btn verify-btn" onclick="openVerifyModal(<?php echo $record->RecordID; ?>, '<?php echo htmlspecialchars($record->player_name ?? 'Unknown'); ?>', '<?php echo addslashes($record->InjuryDetails); ?>')">
                                            <i class="fas fa-check-circle"></i> Verify
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:#888;">No medical reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div id="verifyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Verify Medical Report</h3>
            <span class="close" onclick="closeVerifyModal()">&times;</span>
        </div>
        <div class="modal-body">
            <input type="hidden" id="verify_record_id">
            <div class="form-group">
                <label><strong>Player:</strong></label>
                <p id="verify_player_name"></p>
            </div>
            <div class="form-group">
                <label><strong>Injury Details:</strong></label>
                <p id="verify_injury_details"></p>
            </div>
            <div class="form-group">
                <label for="verify_status">Verification Status:</label>
                <select id="verify_status" class="form-control" required>
                    <option value="">-- Select Status --</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="form-group">
                <label for="verify_comments">Comments (Optional):</label>
                <textarea id="verify_comments" class="form-control" rows="3" placeholder="Add any verification notes..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeVerifyModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="submitVerification()">
                <i class="fas fa-save"></i> Update Status
            </button>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: 1px solid #888;
    width: 600px;
    max-width: 90%;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 20px;
    background-color: #4A90E2;
    color: white;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
}

.close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.close:hover,
.close:focus {
    color: #ddd;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    background-color: #f1f1f1;
    border-radius: 0 0 8px 8px;
    text-align: right;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #4A90E2;
    color: white;
}

.btn-primary:hover {
    background-color: #357ABD;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    margin-right: 10px;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.verify-status-verified {
    background-color: #28a745;
    color: white;
}

.verify-status-rejected {
    background-color: #dc3545;
    color: white;
}

.verify-status-pending {
    background-color: #ffc107;
    color: #333;
}

.action-btn {
    padding: 8px 16px;
    background-color: #4A90E2;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background-color: #357ABD;
}
</style>

<script>
// Open verify modal
function openVerifyModal(recordId, playerName, injuryDetails) {
    document.getElementById('verify_record_id').value = recordId;
    document.getElementById('verify_player_name').textContent = playerName;
    document.getElementById('verify_injury_details').textContent = injuryDetails;
    document.getElementById('verify_status').value = '';
    document.getElementById('verify_comments').value = '';
    document.getElementById('verifyModal').style.display = 'block';
}

// Close verify modal
function closeVerifyModal() {
    document.getElementById('verifyModal').style.display = 'none';
}

// Submit verification
function submitVerification() {
    const recordId = document.getElementById('verify_record_id').value;
    const verifyStatus = document.getElementById('verify_status').value;
    const verifyComments = document.getElementById('verify_comments').value;

    console.log('Submitting verification:', { recordId, verifyStatus, verifyComments });

    if (!verifyStatus) {
        alert('Please select a verification status');
        return;
    }

    if (!recordId) {
        alert('Invalid record ID');
        return;
    }

    // Send AJAX request to coach controller
    fetch('<?php echo URLROOT; ?>/coach/updateVerifyStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `record_id=${recordId}&verify_status=${verifyStatus}&verify_comments=${encodeURIComponent(verifyComments)}`
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Verification status updated successfully!');
            
            // Update the table row
            const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
            if (row) {
                const statusCell = row.querySelector('td:nth-child(6) span');
                if (statusCell) {
                    statusCell.className = `table-badge verify-status-${verifyStatus.toLowerCase()}`;
                    statusCell.textContent = verifyStatus;
                }
            }
            
            closeVerifyModal();
        } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
            console.error('Server error:', data);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('An error occurred while updating the verification status. Check console for details.');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('verifyModal');
    if (event.target == modal) {
        closeVerifyModal();
    }
}

// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                if (mainContent) mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                if (mainContent) mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
