<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/medical.css?v=<?php echo time(); ?>">
    
    <div class="player-layout">
        <?php $playerActivePage = 'medical'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content" id="medicalPage" data-urlroot="<?php echo URLROOT; ?>">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-heartbeat"></i> Medical Records</h1>
                <p>Track your health, fitness assessments, and medical history.</p>
            </div>

            <?php flash('medical_message'); ?>

           


            <div class="schedule-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-notes-medical"></i> Recent Medical Records</h2>
                        <button type="button" class="quick-btn" data-medical-action="open-add-modal">
                            <i class="fas fa-plus"></i> Add Record
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <?php if (!empty($data['medicalRecords'])): ?>
                        <div class="table-responsive">
                            <table class="dashboard-table medical-records-table">
                                <thead>
                                    <tr>
                                        <th>Injury Details</th>
                                        <th>Body Area</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment</th>
                                        <th>Recovery Status</th>
                                        <th>Receipt</th>
                                        <th>Verification</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['medicalRecords'] as $record): ?>
                                    <tr>
                                        <td>
                                            <div class="injury-info">
                                                <div class="injury-date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span><?php echo date('M d, Y', strtotime($record->InjuryDate)); ?></span>
                                                </div>
                                                <div class="reported-date">
                                                    <small>Reported: <?php echo date('M d, Y', strtotime($record->ReportedDate)); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="body-area-cell">
                                                <span class="body-area-text"><?php echo htmlspecialchars($record->bodyarea ?? 'Not specified'); ?></span>
                                            </div>
                                        </td>
                                       
                                        <td>
                                            <div class="diagnosis-cell">
                                                <div class="diagnosis-text">
                                                    <?php echo htmlspecialchars($record->Diagnosis); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="treatment-cell">
                                                <?php if (!empty($record->TreatmentGiven)): ?>
                                                    <div class="treatment-text">
                                                        <?php echo htmlspecialchars($record->TreatmentGiven); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="no-treatment">No treatment specified</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="recovery-status-cell">
                                                <div class="recovery-info">
                                                    <?php 
                                                        $recoveryStatus = strtolower($record->RecoveryStatus);
                                                        $statusIcon = '';
                                                        $statusColor = '';
                                                        
                                                        switch($recoveryStatus) {
                                                            case 'recovered':
                                                                $statusIcon = 'fa-check-circle';
                                                                $statusColor = 'success';
                                                                break;
                                                            case 'recovering':
                                                            case 'ongoing':
                                                                $statusIcon = 'fa-clock';
                                                                $statusColor = 'warning';
                                                                break;
                                                            case 'chronic':
                                                                $statusIcon = 'fa-exclamation-triangle';
                                                                $statusColor = 'danger';
                                                                break;
                                                            default:
                                                                $statusIcon = 'fa-question-circle';
                                                                $statusColor = 'secondary';
                                                        }
                                                    ?>
                                                    <span class="status-badge status-<?php echo $statusColor; ?>">
                                                        <i class="fas <?php echo $statusIcon; ?>"></i>
                                                        <?php echo ucwords(str_replace('_', ' ', $record->RecoveryStatus)); ?>
                                                    </span>
                                                    <div class="rest-days-info">
                                                        <i class="fas fa-clock"></i>
                                                        <span><?php echo intval($record->RestDaysNeeded); ?> days rest</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="receipt-cell">
                                                <?php if (!empty($record->DiagnosisReceiptURL)): ?>
                                                    <?php 
                                                        $receiptPath = str_replace('public/', '', $record->DiagnosisReceiptURL);
                                                    ?>
                                                    <a href="<?php echo URLROOT . '/' . $receiptPath; ?>" target="_blank" class="receipt-btn">
                                                        <i class="fas fa-file-medical"></i> View Receipt
                                                    </a>
                                                <?php else: ?>
                                                    <span class="no-receipt">No receipt</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="verification-cell">
                                                <?php 
                                                    $verifyStatusRaw = $record->verifyStatus
                                                        ?? ($record->VerifiedStatus ?? 'pending');
                                                    $verifyStatus = strtolower(trim((string)$verifyStatusRaw));
                                                    if (in_array($verifyStatus, ['1', 'yes', 'true'], true)) {
                                                        $verifyStatus = 'verified';
                                                    } elseif ($verifyStatus === 'approved') {
                                                        $verifyStatus = 'verified';
                                                    } elseif (in_array($verifyStatus, ['0', 'no', 'false', ''], true)) {
                                                        $verifyStatus = 'pending';
                                                    }
                                                    $verifyIcon = '';
                                                    $verifyColor = '';
                                                    $verifyLabel = 'Pending';
                                                    
                                                    switch($verifyStatus) {
                                                        case 'verified':
                                                            $verifyIcon = 'fa-shield-alt';
                                                            $verifyColor = 'success';
                                                            $verifyLabel = 'Verified';
                                                            break;
                                                        case 'rejected':
                                                            $verifyIcon = 'fa-times-circle';
                                                            $verifyColor = 'danger';
                                                            $verifyLabel = 'Rejected';
                                                            break;
                                                        default:
                                                            $verifyIcon = 'fa-clock';
                                                            $verifyColor = 'warning';
                                                            $verifyLabel = 'Pending';
                                                    }
                                                ?>
                                                <span class="status-badge verify-<?php echo $verifyColor; ?>">
                                                    <i class="fas <?php echo $verifyIcon; ?>"></i>
                                                    <?php echo $verifyLabel; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button
                                                    type="button"
                                                    class="action-btn btn-update"
                                                    data-medical-action="open-update-modal"
                                                    data-record-id="<?php echo $record->RecordID; ?>"
                                                    data-recovery-status="<?php echo htmlspecialchars($record->RecoveryStatus, ENT_QUOTES); ?>"
                                                    title="Update Record">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Update</span>
                                                </button>
                                                <?php if (strtolower($record->verifyStatus ?? 'pending') === 'rejected'): ?>
                                                    <button
                                                        type="button"
                                                        class="action-btn btn-delete"
                                                        data-medical-action="confirm-delete"
                                                        data-record-id="<?php echo $record->RecordID; ?>"
                                                        title="Delete Record">
                                                        <i class="fas fa-trash-alt"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                <?php endif; ?>
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
                            <h3>No Medical Records</h3>
                            <p>You haven't added any medical records yet. Click the "Add Record" button above to create your first medical record.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Trainer Assigned Plans -->
            <div class="performance-tables-row">
                <!-- Current Workout Plans -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-dumbbell"></i> Workout Plans</h2>
                            <span class="event-count"><?= count($data['workoutPlans'] ?? []) ?> Active</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Trainer</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['workoutPlans'])): ?>
                                    <?php foreach ($data['workoutPlans'] as $plan): ?>
                                        <tr>
                                            <td>
                                                <div class="table-cell-title"><?= htmlspecialchars($plan->workoutname ?? '') ?></div>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-calendar"></i> <?= htmlspecialchars($plan->frequency ?? '') ?> • <?= ($plan->Duration ?? '') ?> days
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title"><?= htmlspecialchars($plan->trainer_name ?? '') ?></div>
                                            </td>
                                            <td>
                                                <span class="table-badge status-active">Active</span>
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="action-btn btn-update"
                                                    data-medical-action="open-modal"
                                                    data-modal-id="workoutPlanModal_<?php echo (int)($plan->PlanID ?? 0); ?>">
                                                    <i class="fas fa-eye"></i>
                                                    <span>View</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">No workout plans assigned</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Current Nutrition Plans -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-apple-alt"></i> Nutrition Plans</h2>
                            <span class="event-count"><?= count($data['nutritionPlans'] ?? []) ?> Active</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Trainer</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['nutritionPlans'])): ?>
                                    <?php foreach ($data['nutritionPlans'] as $nplan): ?>
                                        <tr>
                                            <td>
                                                <div class="table-cell-title"><?= htmlspecialchars($nplan->nutritionPlanName ?? '') ?></div>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-calendar"></i> <?= ($nplan->Duration ?? '') ?> days • Started <?= date('M d', strtotime($nplan->CreatedDate ?? 'now')) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-title"><?= htmlspecialchars($nplan->trainer_name ?? '') ?></div>
                                            </td>
                                            <td>
                                                <span class="table-badge status-active">Active</span>
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="action-btn btn-update"
                                                    data-medical-action="open-modal"
                                                    data-modal-id="nutritionPlanModal_<?php echo (int)($nplan->PlanID ?? 0); ?>">
                                                    <i class="fas fa-eye"></i>
                                                    <span>View</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">No nutrition plans assigned</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

             <!-- Health Overview and Supplements - Two Tables Per Row -->
            
        </div>
    </div>

    <!-- Add Medical Record Modal -->
    <div id="addMedicalModal" class="modal app-modal medical-modal" data-medical-modal aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--wide medical-modal-content medical-modal-content--wide">
            <div class="modal-header app-modal__header medical-modal-header">
                <h2 class="medical-modal-title app-modal__title">
                    <i class="fas fa-notes-medical medical-modal-title-icon"></i> Add Medical Record
                </h2>
                <button type="button" class="close medical-modal-close app-modal__close" data-medical-close="addMedicalModal" aria-label="Close add medical record modal">&times;</button>
            </div>

            <form method="POST" action="<?php echo URLROOT; ?>/player/addMedicalRecord" enctype="multipart/form-data" class="medical-modal-form app-form">
                <div class="modal-body app-modal__body medical-modal-body">
                    <div class="medical-form-row app-form-row">
                        <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                            <label for="injury_date" class="app-form-label app-form-label--strong">
                                <i class="fas fa-calendar-alt medical-field-icon medical-field-icon--primary app-form-icon app-form-icon--primary"></i> Injury Date *
                            </label>
                            <input type="date" id="injury_date" name="injury_date" class="form-control app-form-control app-form-control--lg" required max="<?php echo date('Y-m-d'); ?>">
                            <small class="medical-help-text app-form-help">When did the injury occur?</small>
                        </div>
                        <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                            <label for="reported_date" class="app-form-label app-form-label--strong">
                                <i class="fas fa-calendar-check medical-field-icon medical-field-icon--primary app-form-icon app-form-icon--primary"></i> Reported Date *
                            </label>
                            <input type="date" id="reported_date" name="reported_date" class="form-control app-form-control app-form-control--lg" required max="<?php echo date('Y-m-d'); ?>">
                            <small class="medical-help-text app-form-help">When are you reporting this?</small>
                        </div>
                    </div>

                    <div class="medical-form-row app-form-row">
                    <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                        <label class="app-form-label app-form-label--strong">
                            <i class="fas fa-hospital medical-field-icon medical-field-icon--primary app-form-icon app-form-icon--primary"></i> Did the injury happen at the academy? *
                        </label>
                        <div class="medical-radio-group app-form-radio-group">
                            <label class="medical-radio-option">
                                <input type="radio" name="happened_at_academy" value="yes" required> Yes
                            </label>
                            <label class="medical-radio-option">
                                <input type="radio" name="happened_at_academy" value="no" checked> No
                            </label>
                        </div>
                    </div>

                    <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                        <label for="body_area" class="app-form-label app-form-label--strong">
                            <i class="fas fa-user-injured medical-field-icon medical-field-icon--danger app-form-icon app-form-icon--danger"></i> Body Area *
                        </label>
                        <select id="body_area" name="body_area" class="form-control app-form-control app-form-control--lg app-form-select" required>
                            <option value="">Select body area...</option>
                            <option value="Head/Face">Head/Face</option>
                            <option value="Neck">Neck</option>
                            <option value="Shoulder">Shoulder</option>
                            <option value="Arm/Elbow">Arm/Elbow</option>
                            <option value="Hand/Wrist">Hand/Wrist</option>
                            <option value="Chest/Back">Chest/Back</option>
                            <option value="Hip/Groin">Hip/Groin</option>
                            <option value="Thigh">Thigh</option>
                            <option value="Knee">Knee</option>
                            <option value="Lower Leg">Lower Leg</option>
                            <option value="Ankle/Foot">Ankle/Foot</option>
                        </select>
                    </div>
                    </div>

                    

                    <div class="medical-form-row app-form-row">
                    <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                        <label for="diagnosis" class="app-form-label app-form-label--strong">
                            <i class="fas fa-stethoscope medical-field-icon medical-field-icon--danger app-form-icon app-form-icon--danger"></i> Diagnosis *
                        </label>
                        <select id="diagnosis" name="diagnosis" class="form-control app-form-control app-form-control--lg app-form-select" required>
                            <option value="">Select diagnosis...</option>
                            <option value="Sprain">Sprain</option>
                            <option value="Strain">Strain</option>
                            <option value="Fracture">Fracture</option>
                            <option value="Dislocation">Dislocation</option>
                            <option value="Concussion">Concussion</option>
                            <option value="Tear">Tear</option>
                            <option value="Laceration">Laceration</option>
                            <option value="Overuse/Inflammation">Overuse/Inflammation</option>
                            <option value="Illness">Illness</option>
                        </select>
                    </div>

                    <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                        <label for="treatment_given" class="app-form-label app-form-label--strong">
                            <i class="fas fa-hand-holding-medical medical-field-icon medical-field-icon--success app-form-icon app-form-icon--success"></i> Treatment Given
                        </label>
                        <select id="treatment_given" name="treatment_given" class="form-control app-form-control app-form-control--lg app-form-select">
                            <option value="">Select treatment...</option>
                            <option value="RICE Procedure">RICE Procedure</option>
                            <option value="First Aid/Wound Care">First Aid/Wound Care</option>
                            <option value="Physiotherapy">Physiotherapy</option>
                            <option value="Medication">Medication</option>
                            <option value="Referral to Specialist">Referral to Specialist</option>
                            <option value="Surgery">Surgery</option>
                            <option value="Observation">Observation</option>
                        </select>
                    </div>
                    </div>

                    <div class="medical-form-row app-form-row">
                        <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                            <label for="rest_days_needed" class="app-form-label app-form-label--strong">
                                <i class="fas fa-bed medical-field-icon medical-field-icon--warning app-form-icon app-form-icon--warning"></i> Estimated Rest Days Needed
                            </label>
                            <input type="number" id="rest_days_needed" name="rest_days_needed" class="form-control app-form-control app-form-control--lg" min="0" max="1000" step="1" value="0" placeholder="e.g. 7">
                            <small class="medical-help-text app-form-help">Number of days rest required (max 1000)</small>
                            <small id="rest_days_error" class="medical-error-text app-form-error" aria-live="polite"></small>
                        </div>
                        <div class="form-group medical-form-group medical-form-group--tight app-form-group">
                            <label for="recovery_status" class="app-form-label app-form-label--strong">
                                <i class="fas fa-heartbeat medical-field-icon medical-field-icon--danger app-form-icon app-form-icon--danger"></i> Recovery Status *
                            </label>
                            <select id="recovery_status" name="recovery_status" class="form-control app-form-control app-form-control--lg app-form-select" required>
                                <option value="">Select status...</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="recovering">Recovering</option>
                                <option value="fully_recovered">Fully Recovered</option>
                                <option value="chronic_condition">Chronic Condition</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group medical-form-group medical-form-group--full app-form-group app-form-group--full">
                        <label for="diagnosis_receipt" class="app-form-label app-form-label--strong">
                            <i class="fas fa-file-medical medical-field-icon medical-field-icon--primary app-form-icon app-form-icon--primary"></i> Diagnosis Receipt/Document (Optional)
                        </label>
                        <input type="file" id="diagnosis_receipt" name="diagnosis_receipt" class="form-control medical-file-input app-form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small class="medical-help-text app-form-help"><i class="fas fa-info-circle"></i> Upload medical receipt, prescription, or diagnosis document (JPG, PNG, PDF, DOC - Max 5MB)</small>
                    </div>

                    <div class="medical-form-actions app-form-actions">
                        <button type="button" class="medical-btn medical-btn--secondary" data-medical-close="addMedicalModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="medical-btn medical-btn--primary">
                            <i class="fas fa-save"></i> Save Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
        $medicalText = function ($value, $fallback = 'Not provided') {
            $text = trim((string)($value ?? ''));
            return $text !== '' ? $text : $fallback;
        };

        $medicalDate = function ($value) {
            $raw = trim((string)($value ?? ''));
            if ($raw === '') return 'Not provided';
            $ts = strtotime($raw);
            if ($ts === false) return htmlspecialchars($raw);
            return date('M d, Y', $ts);
        };

        $medicalMultiline = function ($value, $fallback) {
            $raw = trim((string)($value ?? ''));
            if ($raw === '') {
                return '<p class="plan-empty-text">' . htmlspecialchars($fallback) . '</p>';
            }
            return '<p>' . nl2br(htmlspecialchars($raw)) . '</p>';
        };
    ?>

    <!-- Workout Plan Modals (server-rendered; JS only opens/closes) -->
    <?php if (!empty($data['workoutPlans'])): ?>
        <?php foreach ($data['workoutPlans'] as $plan): ?>
            <?php $modalId = 'workoutPlanModal_' . (int)($plan->PlanID ?? 0); ?>
            <div id="<?php echo htmlspecialchars($modalId); ?>" class="modal app-modal medical-modal" data-medical-modal aria-hidden="true">
                <div class="modal-content app-modal__dialog app-modal__dialog--standard medical-modal-content medical-modal-content--standard">
                    <div class="modal-header app-modal__header medical-modal-header">
                        <h3 class="medical-modal-title app-modal__title"><i class="fas fa-dumbbell"></i> Workout Plan Details</h3>
                        <button type="button" class="close medical-modal-close app-modal__close" data-medical-close="<?php echo htmlspecialchars($modalId); ?>" aria-label="Close workout plan modal">&times;</button>
                    </div>
                    <div class="modal-body app-modal__body medical-modal-body">
                        <div class="plan-header">
                            <h4><?php echo htmlspecialchars($medicalText($plan->workoutname ?? null, 'Workout plan')); ?></h4>
                            <p><strong>Trainer:</strong> <?php echo htmlspecialchars($medicalText($plan->trainer_name ?? null, 'Not assigned')); ?></p>
                            <p><strong>Frequency:</strong> <?php echo htmlspecialchars($medicalText($plan->frequency ?? null)); ?></p>
                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($medicalText($plan->Duration ?? null)); ?> days</p>
                        </div>

                        <div class="plan-meta-grid">
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Intensity</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalText($plan->Intensity ?? null)); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Status</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalText($plan->assignment_status ?? ($plan->Status ?? null), 'active')); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Assigned Date</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalDate($plan->AssignedDate ?? null)); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">End Date</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalDate($plan->EndDate ?? null)); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Assigned By</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalText($plan->assigned_by_name ?? null)); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Not Suitable For</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalText($plan->NotSuitableFor ?? null)); ?></span>
                            </div>
                        </div>

                        <section class="plan-section">
                            <h5>Benefits</h5>
                            <div class="plan-rich-text">
                                <?php echo $medicalMultiline($plan->Benefits ?? null, 'No benefits have been added for this workout yet.'); ?>
                            </div>
                        </section>

                        <section class="plan-section">
                            <h5>Video Demonstration</h5>
                            <div class="plan-rich-text">
                                <?php if (!empty($plan->VideoLink)): ?>
                                    <p><a class="plan-link" href="<?php echo htmlspecialchars((string)$plan->VideoLink); ?>" target="_blank" rel="noopener noreferrer">Open workout video</a></p>
                                <?php else: ?>
                                    <p class="plan-empty-text">No video link has been provided for this workout.</p>
                                <?php endif; ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Nutrition Plan Modals (server-rendered; JS only opens/closes) -->
    <?php if (!empty($data['nutritionPlans'])): ?>
        <?php foreach ($data['nutritionPlans'] as $nplan): ?>
            <?php $modalId = 'nutritionPlanModal_' . (int)($nplan->PlanID ?? 0); ?>
            <div id="<?php echo htmlspecialchars($modalId); ?>" class="modal app-modal medical-modal" data-medical-modal aria-hidden="true">
                <div class="modal-content app-modal__dialog app-modal__dialog--standard medical-modal-content medical-modal-content--standard">
                    <div class="modal-header app-modal__header medical-modal-header">
                        <h3 class="medical-modal-title app-modal__title"><i class="fas fa-apple-alt"></i> Nutrition Plan Details</h3>
                        <button type="button" class="close medical-modal-close app-modal__close" data-medical-close="<?php echo htmlspecialchars($modalId); ?>" aria-label="Close nutrition plan modal">&times;</button>
                    </div>
                    <div class="modal-body app-modal__body medical-modal-body">
                        <div class="plan-header">
                            <h4><?php echo htmlspecialchars($medicalText($nplan->nutritionPlanName ?? null, 'Nutrition plan')); ?></h4>
                            <p><strong>Nutritionist:</strong> <?php echo htmlspecialchars($medicalText($nplan->trainer_name ?? null, 'Not assigned')); ?></p>
                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($medicalText($nplan->Duration ?? null)); ?> days</p>
                        </div>

                        <div class="plan-meta-grid">
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Status</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalText($nplan->Status ?? null, 'active')); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Created</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalDate($nplan->CreatedDate ?? null)); ?></span>
                            </div>
                            <div class="plan-meta-item">
                                <span class="plan-meta-label">Plan ID</span>
                                <span class="plan-meta-value"><?php echo htmlspecialchars($medicalText($nplan->PlanID ?? null)); ?></span>
                            </div>
                        </div>

                        <section class="plan-section">
                            <h5>Diet Details</h5>
                            <div class="plan-rich-text">
                                <?php echo $medicalMultiline($nplan->DietDetails ?? null, 'No diet details are available for this plan.'); ?>
                            </div>
                        </section>

                        <section class="plan-section">
                            <h5>Trainer Notes</h5>
                            <div class="plan-rich-text">
                                <?php echo $medicalMultiline($nplan->Notes ?? null, 'No custom notes were added to this nutrition plan.'); ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Update Medical Record Modal — Recovery status only (verified) -->
    <div id="updateStatusModal" class="modal app-modal medical-modal" data-medical-modal aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--compact medical-modal-content medical-modal-content--compact">
            <div class="modal-header app-modal__header medical-modal-header">
                <h2 class="medical-modal-title app-modal__title">
                    <i class="fas fa-heartbeat medical-modal-title-icon"></i> Update Recovery Status
                </h2>
                <button type="button" class="close medical-modal-close app-modal__close" data-medical-close="updateStatusModal" aria-label="Close update status modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/player/updateMedicalRecord" class="medical-modal-form app-form">
                <div class="modal-body app-modal__body medical-modal-body">
                    <input type="hidden" id="update_record_id" name="record_id">

                    <div class="form-group medical-form-group app-form-group app-form-group--full">
                        <label for="update_recovery_status" class="app-form-label app-form-label--strong">
                            <i class="fas fa-heartbeat medical-field-icon medical-field-icon--danger app-form-icon app-form-icon--danger"></i> Recovery Status *
                        </label>
                        <select id="update_recovery_status" name="recovery_status" class="form-control app-form-control app-form-control--lg app-form-select" required>
                            <option value="">Select status...</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="recovering">Recovering</option>
                            <option value="fully_recovered">Fully Recovered</option>
                            <option value="chronic_condition">Chronic Condition</option>
                        </select>
                    </div>

                    <div class="medical-info-panel">
                        <p class="medical-info-title"><i class="fas fa-info-circle medical-field-icon medical-field-icon--primary"></i> Status Definitions</p>
                        <ul class="medical-info-list">
                            <li><strong>Ongoing:</strong> Condition is still active/symptomatic</li>
                            <li><strong>Recovering:</strong> In the process of healing</li>
                            <li><strong>Fully Recovered:</strong> No symptoms, returned to full activity</li>
                            <li><strong>Chronic Condition:</strong> Long-term condition requiring ongoing management</li>
                        </ul>
                    </div>

                    <div class="medical-form-actions app-form-actions">
                        <button type="button" class="medical-btn medical-btn--secondary" data-medical-close="updateStatusModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="medical-btn medical-btn--primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Medical Record Confirmation Modal -->
    <div id="deleteRecordModal" class="modal app-modal medical-modal" data-medical-modal aria-hidden="true">
        <div class="modal-content app-modal__dialog app-modal__dialog--compact medical-modal-content medical-modal-content--compact">
            <div class="modal-header app-modal__header medical-modal-header">
                <h3 class="medical-modal-title app-modal__title"><i class="fas fa-exclamation-triangle text-danger"></i> Delete Medical Record</h3>
                <button type="button" class="close medical-modal-close app-modal__close" data-medical-close="deleteRecordModal" aria-label="Close delete record modal">&times;</button>
            </div>
            <form id="deleteMedicalForm" method="POST" action="<?php echo URLROOT; ?>/player/deleteMedicalRecord" class="medical-modal-form">
                <div class="modal-body app-modal__body medical-modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>

                    <p>Are you sure you want to delete this medical record?</p>
                    <p class="text-muted">This record has been marked as "rejected" by a trainer, which allows deletion. Once deleted, this information will be permanently removed from your medical history.</p>

                    <input type="hidden" id="delete_record_id" name="record_id" value="">
                </div>
                <div class="modal-footer app-modal__footer medical-modal-footer">
                    <button type="button" class="btn btn-secondary" data-medical-close="deleteRecordModal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/medical.js?v=<?php echo time(); ?>"></script>
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>