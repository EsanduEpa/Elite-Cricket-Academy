<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/health.css">

<!-- Coach Dashboard Layout -->
<div class="coach-layout">
    <?php $activeCoachNav = 'health'; require APPROOT . '/views/inc/components/coach_sidebar.php'; ?>

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
    window.__COACH_HEALTH_RECORDS = <?php echo json_encode($data['medicalRecords'] ?? [], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
</script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/health.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
