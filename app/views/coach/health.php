<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/health.css">

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
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Slot Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link" data-tooltip="Performance">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
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
                    <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                        <i class="fas fa-comments"></i>
                        <span>Communication</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Requests</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1>
                        <i class="fas fa-heartbeat"></i>
                        Health & Injury Monitoring
                    </h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">Monitor player health status and injury reports</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon recovering">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="stat-content">
                    <h3>Currently Injured</h3>
                    <div class="stat-number"><?php echo $data['injuredCount']; ?></div>
                    <p class="stat-description">Players recovering</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon recovered">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Recovered</h3>
                    <div class="stat-number"><?php echo $data['recoveredCount']; ?></div>
                    <p class="stat-description">Back to training</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon severe">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Severe Cases</h3>
                    <div class="stat-number"><?php echo $data['severeCount']; ?></div>
                    <p class="stat-description">Requires attention</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Review</h3>
                    <div class="stat-number"><?php echo $data['pendingCount']; ?></div>
                    <p class="stat-description">Awaiting verification</p>
                </div>
            </div>
        </div>

        <!-- Injury Reports Table -->
        <div class="health-card">
            <div class="card-header">
                <h3><i class="fas fa-notes-medical"></i> Injury Reports</h3>
                <div class="header-actions">
                    <select class="filter-select" id="severityFilter">
                        <option value="all">All Severities</option>
                        <option value="mild">Mild</option>
                        <option value="moderate">Moderate</option>
                        <option value="severe">Severe</option>
                    </select>
                    <select class="filter-select" id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="recovering">Recovering</option>
                        <option value="recovered">Recovered</option>
                    </select>
                </div>
            </div>
            
            <div class="table-container">
                <table class="health-table">
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Injury Type</th>
                            <th>Description</th>
                            <th>Severity</th>
                            <th>Date Reported</th>
                            <th>Status</th>
                            <th>Recovery Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Display real data from PlayerMedicalRecord table
                        if (!empty($data['medicalRecords'])): 
                            foreach ($data['medicalRecords'] as $record): 
                                // Map database status to severity for display
                                $severity = 'moderate'; // default
                                if ($record->RecoveryStatus == 'recovered') {
                                    $severity = 'mild';
                                } elseif ($record->RecoveryStatus == 'chronic') {
                                    $severity = 'severe';
                                } elseif ($record->RestDaysNeeded > 14) {
                                    $severity = 'severe';
                                } elseif ($record->RestDaysNeeded < 7) {
                                    $severity = 'mild';
                                }
                                
                                $statusClass = strtolower($record->RecoveryStatus);
                        ?>
                        <tr data-severity="<?php echo $severity; ?>" data-status="<?php echo $statusClass; ?>">
                            <td>
                                <div class="player-info">
                                    <i class="fas fa-user-circle"></i>
                                    <strong><?php echo htmlspecialchars($record->PlayerName); ?></strong>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($record->Diagnosis ?? 'Not specified'); ?></td>
                            <td class="injury-desc"><?php echo htmlspecialchars($record->InjuryDetails ?? 'No details provided'); ?></td>
                            <td>
                                <span class="severity-badge <?php echo $severity; ?>">
                                    <?php echo ucfirst($severity); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($record->InjuryDate)); ?></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <i class="fas fa-<?php echo $statusClass === 'recovered' ? 'check-circle' : 'spinner'; ?>"></i>
                                    <?php echo ucfirst($record->RecoveryStatus); ?>
                                </span>
                            </td>
                            <td><?php echo $record->RestDaysNeeded > 0 ? $record->RestDaysNeeded . ' days' : 'N/A'; ?></td>
                            <td>
                                <button class="btn-action view" onclick="viewDetails(<?php echo $record->RecordID; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; 
                        else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem;">
                                <div style="color: #999;">
                                    <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                    <p>No medical records found</p>
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

<!-- Injury Details Modal -->
<div class="modal" id="injuryModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-notes-medical"></i> Injury Details</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('severityFilter').addEventListener('change', function() {
    filterTable();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const severityFilter = document.getElementById('severityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.health-table tbody tr');
    
    rows.forEach(row => {
        const severity = row.getAttribute('data-severity');
        const status = row.getAttribute('data-status');
        
        const severityMatch = severityFilter === 'all' || severity === severityFilter;
        const statusMatch = statusFilter === 'all' || status === statusFilter;
        
        if (severityMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// View details function
function viewDetails(id) {
    const records = <?php echo json_encode($data['medicalRecords'] ?? []); ?>;
    const record = records.find(r => r.RecordID === id);
    
    if (record) {
        const modalBody = document.getElementById('modalBody');
        const severity = record.RestDaysNeeded > 14 ? 'severe' : (record.RestDaysNeeded < 7 ? 'mild' : 'moderate');
        
        modalBody.innerHTML = `
            <div class="injury-details">
                <div class="detail-row">
                    <strong>Player:</strong>
                    <span>${record.PlayerName || 'Unknown'}</span>
                </div>
                <div class="detail-row">
                    <strong>Diagnosis:</strong>
                    <span>${record.Diagnosis || 'Not specified'}</span>
                </div>
                <div class="detail-row">
                    <strong>Injury Details:</strong>
                    <span>${record.InjuryDetails || 'No details provided'}</span>
                </div>
                <div class="detail-row">
                    <strong>Severity:</strong>
                    <span class="severity-badge ${severity}">${severity}</span>
                </div>
                <div class="detail-row">
                    <strong>Injury Date:</strong>
                    <span>${new Date(record.InjuryDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                </div>
                <div class="detail-row">
                    <strong>Recovery Status:</strong>
                    <span class="status-badge ${record.RecoveryStatus.toLowerCase()}">${record.RecoveryStatus}</span>
                </div>
                <div class="detail-row">
                    <strong>Rest Days Needed:</strong>
                    <span>${record.RestDaysNeeded || 0} days</span>
                </div>
                <div class="detail-row">
                    <strong>Treatment Given:</strong>
                    <span>${record.TreatmentGiven || 'Not specified'}</span>
                </div>
                <div class="detail-row">
                    <strong>Happened at Academy:</strong>
                    <span>${record.HappenedAtAcademy === 'yes' ? 'Yes' : 'No'}</span>
                </div>
                <div class="detail-row">
                    <strong>Verification Status:</strong>
                    <span class="status-badge ${record.verifyStatus}">${record.verifyStatus}</span>
                </div>
            </div>
        `;
        document.getElementById('injuryModal').style.display = 'flex';
    }
}

function closeModal() {
    document.getElementById('injuryModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('injuryModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
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
