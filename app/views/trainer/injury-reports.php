<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/injury-reports.css?v=<?php echo time(); ?>">
<style>
.trainer-sidebar .trainer-details {
    display: block !important;
}
.trainer-sidebar.collapsed .trainer-details {
    display: none !important;
}
</style>
<?php $trainerSidebarActive = 'medical'; ?>
<?php
$trainerDisplayName = trim((string) ($_SESSION['user_name'] ?? $_SESSION['username'] ?? ''));
if ($trainerDisplayName === '') {
    $trainerDisplayName = 'Trainer';
}
?>

<!-- Trainer Dashboard Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo htmlspecialchars($trainerDisplayName, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>

        <!-- Trainer Profile Section -->
        <div class="trainer-profile">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <div class="trainer-name"><?php echo htmlspecialchars($trainerDisplayName, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="trainer-role">Physical Trainer</div>
            </div>
            <div class="profile-actions">
                <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                    <i class="fas fa-user-cog"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-user-injured"></i> Injury Reports</h1>
                    <p>Review and verify player injury reports</p>
                </div>
            </div>
        </div>

        <?php flash('injury_message'); ?>

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
                            <th>Receipt</th>
                            <th>Recovery Status</th>
                            <th>Verify Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['medical_records'])): ?>
                            <?php foreach ($data['medical_records'] as $record): ?>
                                <?php
                                    $verifyStatusRaw = $record->verifyStatus ?? ($record->VerifiedStatus ?? 'pending');
                                    $verifyStatus = strtolower(trim((string)$verifyStatusRaw));
                                    if (in_array($verifyStatus, ['1', 'yes', 'true'], true) || $verifyStatus === 'approved') {
                                        $verifyStatus = 'verified';
                                    } elseif (in_array($verifyStatus, ['0', 'no', 'false', ''], true)) {
                                        $verifyStatus = 'pending';
                                    }
                                    if (!in_array($verifyStatus, ['pending', 'verified', 'rejected'], true)) {
                                        $verifyStatus = 'pending';
                                    }
                                    $verifyStatusLabel = ucfirst($verifyStatus);
                                    $receiptPath = !empty($record->DiagnosisReceiptURL)
                                        ? str_replace('public/', '', $record->DiagnosisReceiptURL)
                                        : '';
                                    $injuryDetails = trim((string)($record->bodyarea ?? ''));
                                    $diagnosis = trim((string)($record->Diagnosis ?? ''));
                                    if ($diagnosis !== '') {
                                        $injuryDetails .= ($injuryDetails !== '' ? ' - ' : '') . $diagnosis;
                                    }
                                ?>
                                <tr data-record-id="<?php echo (int)$record->RecordID; ?>">
                                    <td style="text-align: center;">
                                        <div class="table-cell-primary">
                                            <?php echo date('M d, Y', strtotime($record->InjuryDate)); ?>
                                        </div>
                                        <div class="table-cell-secondary">Reported: <?php echo date('M d', strtotime($record->ReportedDate)); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">
                                            <?php echo htmlspecialchars((string)($record->player_name ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-details">
                                            <strong><?php echo htmlspecialchars((string)($record->bodyarea ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-details">
                                            <?php echo htmlspecialchars((string)($record->Diagnosis ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($receiptPath !== ''): ?>
                                            <a href="<?php echo URLROOT . '/' . $receiptPath; ?>" target="_blank" class="btn-sm" style="background: #17a2b8;">
                                                <i class="fas fa-file-alt"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span class="table-cell-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge status-<?php echo strtolower((string)$record->RecoveryStatus); ?>">
                                            <?php echo htmlspecialchars((string)$record->RecoveryStatus, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="table-badge verify-status-badge verify-status-<?php echo $verifyStatus; ?>">
                                            <?php echo htmlspecialchars($verifyStatusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button
                                            class="action-btn verify-btn"
                                            type="button"
                                            data-record-id="<?php echo (int)$record->RecordID; ?>"
                                            data-player-name="<?php echo htmlspecialchars((string)($record->player_name ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-injury-details="<?php echo htmlspecialchars($injuryDetails, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-verify-status="<?php echo htmlspecialchars($verifyStatus, ENT_QUOTES, 'UTF-8'); ?>"
                                            onclick="openVerifyModal(this)"
                                        >
                                            <i class="fas fa-check-circle"></i> Verify
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center; color:#888;">No medical reports found.</td>
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
        <form method="POST" action="<?php echo URLROOT; ?>/trainer/updateVerifyStatus">
            <div class="modal-body">
                <input type="hidden" id="verify_record_id" name="record_id">
                <div class="form-group">
                    <label><strong>Player:</strong></label>
                    <p id="verify_player_name"></p>
                </div>
                <div class="form-group">
                    <label><strong>Body Area &amp; Diagnosis:</strong></label>
                    <p id="verify_injury_details"></p>
                </div>
                <div class="form-group">
                    <label for="verify_status">Verification Status:</label>
                    <select id="verify_status" name="verify_status" class="form-control" required>
                        <option value="">-- Select Status --</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="verify_comments">Comments (Optional):</label>
                    <textarea id="verify_comments" name="verify_comments" class="form-control" rows="3" placeholder="Add any verification notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeVerifyModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/trainer/injury-reports.js?v=<?php echo time(); ?>"></script>

</body>
</html>
