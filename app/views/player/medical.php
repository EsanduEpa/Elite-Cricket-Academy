<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/medical.css">
    
    <div class="player-layout">
        <!-- Simple Sidebar -->
        <div class="player-sidebar" id="playerSidebar">
            <div class="sidebar-header">
                <div class="player-logo">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Player Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Simple Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
                <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

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
                        <button class="quick-btn " onclick="openAddMedicalModal()">
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
                                        <th>At Academy</th>
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
                                            <div class="academy-cell">
                                                <?php if ($record->HappenedAtAcademy == 'yes'): ?>
                                                    <div class="academy-indicator">
                                                        <i class="fas fa-school"></i>
                                                        <span>Yes</span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="no-academy">-</span>
                                                <?php endif; ?>
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
                                                    $verifyStatus = strtolower($record->verifyStatus ?? 'pending');
                                                    $verifyIcon = '';
                                                    $verifyColor = '';
                                                    
                                                    switch($verifyStatus) {
                                                        case 'verified':
                                                            $verifyIcon = 'fa-shield-alt';
                                                            $verifyColor = 'success';
                                                            break;
                                                        case 'rejected':
                                                            $verifyIcon = 'fa-times-circle';
                                                            $verifyColor = 'danger';
                                                            break;
                                                        default:
                                                            $verifyIcon = 'fa-clock';
                                                            $verifyColor = 'warning';
                                                    }
                                                ?>
                                                <span class="status-badge verify-<?php echo $verifyColor; ?>">
                                                    <i class="fas <?php echo $verifyIcon; ?>"></i>
                                                    <?php echo ucfirst($record->verifyStatus ?? 'Pending'); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn btn-update" onclick="openUpdateStatusModal(<?php echo $record->RecordID; ?>, '<?php echo $record->RecoveryStatus; ?>')" title="Update Recovery Status">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Update</span>
                                                </button>
                                                <?php if (strtolower($record->verifyStatus ?? 'pending') === 'rejected'): ?>
                                                    <button class="action-btn btn-delete" onclick="confirmDeleteRecord(<?php echo $record->RecordID; ?>)" title="Delete Record">
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
                                                <button class="action-btn btn-update" onclick="viewWorkoutPlan(<?= $plan->PlanID ?>)">
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
                                                <button class="action-btn btn-update" onclick="viewNutritionPlan(<?= $nplan->PlanID ?>)">
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
            <div class="performance-tables-row">
                <!-- Health Overview - Compact Table -->
                <div class="schedule-card">
                      <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-syringe"></i> Vaccinations & Immunizations</h2>
                        <span class="event-count">Up to Date</span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Vaccination</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Next Due</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="text-center">No vaccination records available</td></tr>
                        </tbody>
                    </table>
                </div>
                    
                </div>

                <!-- Current Supplement Plans -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-capsules"></i> Supplements</h2>
                            <span class="event-count"><?= count($data['supplements'] ?? []) ?> Active</span>
                        </div>
                    </div>
                    <div class="card-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Supplement</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['supplements'])): ?>
                                    <?php foreach ($data['supplements'] as $supp): ?>
                                        <tr>
                                            <td>
                                                <div class="table-cell-title"><?= htmlspecialchars($supp->SupplementPlanName ?? '') ?></div>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-user"></i> <?= htmlspecialchars($supp->trainer_name ?? '') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-cell-primary"><?= htmlspecialchars($supp->Dosage ?? '') ?></div>
                                            </td>
                                            <td>
                                                <div class="table-cell-primary"><?= ($supp->Duration ?? '') ?> days</div>
                                                <div class="table-cell-secondary">Started <?= date('M d', strtotime($supp->CreatedDate ?? 'now')) ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No supplements assigned</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Medical Record Modal -->
    <div id="addMedicalModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #357ABD);">
                <h3 style="margin: 0; color: white; display: flex; align-items: center; gap: 10px; font-size: 20px;">
                    <i class="fas fa-notes-medical"></i> Add Medical Record
                </h3>
                <span class="close" onclick="closeAddMedicalModal()" style="font-size: 30px; color: white; font-weight: 700; cursor: pointer;">&times;</span>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/player/addMedicalRecord" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="injury_date">Injury Date *</label>
                            <input type="date" id="injury_date" name="injury_date" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                            <small class="form-text">When did the injury occur?</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="reported_date">Reported Date *</label>
                            <input type="date" id="reported_date" name="reported_date" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                            <small class="form-text">When are you reporting this?</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Did the injury happen at the academy? *</label>
                        <div style="display: flex; gap: 20px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                                <input type="radio" name="happened_at_academy" value="yes" style="margin-right: 5px;" required>
                                <span>Yes</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                                <input type="radio" name="happened_at_academy" value="no" style="margin-right: 5px;" checked>
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="body_area">Body Area *</label>
                        <select id="body_area" name="body_area" class="form-control" required>
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

                    <div class="form-group">
                        <label for="diagnosis">Diagnosis *</label>
                        <select id="diagnosis" name="diagnosis" class="form-control" required>
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

                    <div class="form-group">
                        <label for="treatment_given">Treatment Given</label>
                        <select id="treatment_given" name="treatment_given" class="form-control">
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

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="rest_days_needed">Estimated Rest Days Needed</label>
                            <input type="number" id="rest_days_needed" name="rest_days_needed" class="form-control" min="0" max="1000" step="1" value="0" placeholder="e.g. 7">
                            <small class="form-text">Number of days rest required (max 1000)</small>
                            <small class="form-text text-danger" id="rest_days_error" style="display: none;"></small>
                        </div>

                        <div class="form-group">
                            <label for="recovery_status">Recovery Status *</label>
                            <select id="recovery_status" name="recovery_status" class="form-control" required>
                                <option value="">Select status...</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="recovering">Recovering</option>
                                <option value="fully_recovered">Fully Recovered</option>
                                <option value="chronic_condition">Chronic Condition</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="diagnosis_receipt">Diagnosis Receipt/Document (Optional)</label>
                        <input type="file" id="diagnosis_receipt" name="diagnosis_receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small class="form-text">Upload medical receipt, prescription, or diagnosis document (JPG, PNG, PDF, DOC - Max 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddMedicalModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Validation Error Styling */
        .text-danger {
            color: #dc3545 !important;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-control.is-invalid,
        .form-control[style*="border-color: rgb(220, 53, 69)"] {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem);
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .form-control.is-valid,
        .form-control[style*="border-color: rgb(40, 167, 69)"] {
            border-color: #28a745 !important;
        }
        
        .form-text {
            font-size: 0.875rem;
            margin-top: 0.25rem;
            color: #6c757d;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }
    </style>

    <!-- Workout Plan Modal -->
    <div id="workoutPlanModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-dumbbell"></i> Workout Plan Details</h3>
                <span class="close" onclick="closeModal('workoutPlanModal')">&times;</span>
            </div>
            <div class="modal-body" id="workoutPlanContent">
                <!-- Workout plan content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Nutrition Plan Modal -->
    <div id="nutritionPlanModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-apple-alt"></i> Nutrition Plan Details</h3>
                <span class="close" onclick="closeModal('nutritionPlanModal')">&times;</span>
            </div>
            <div class="modal-body" id="nutritionPlanContent">
                <!-- Nutrition plan content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Update Medical Record Status Modal -->
    <div id="updateStatusModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Update Recovery Status</h3>
                <span class="close" onclick="closeUpdateStatusModal()">&times;</span>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/player/updateMedicalRecord">
                <div class="modal-body">
                    <input type="hidden" id="update_record_id" name="record_id">
                    
                    <div class="form-group">
                        <label for="update_recovery_status">Recovery Status *</label>
                        <select id="update_recovery_status" name="recovery_status" class="form-control" required>
                            <option value="">Select status...</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="recovering">Recovering</option>
                            <option value="fully_recovered">Fully Recovered</option>
                            <option value="chronic_condition">Chronic Condition</option>
                        </select>
                    </div>

                    <div class="form-help">
                        <p><strong>Status Definitions:</strong></p>
                        <ul>
                            <li><strong>Ongoing:</strong> Condition is still active/symptomatic</li>
                            <li><strong>Recovering:</strong> In the process of healing</li>
                            <li><strong>Fully Recovered:</strong> No symptoms, returned to full activity</li>
                            <li><strong>Chronic Condition:</strong> Long-term condition requiring ongoing management</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeUpdateStatusModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Medical Record Confirmation Modal -->
    <div id="deleteRecordModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-danger"></i> Delete Medical Record</h3>
                <span class="close" onclick="closeDeleteRecordModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
                
                <p>Are you sure you want to delete this medical record?</p>
                <p class="text-muted">This record has been marked as "rejected" by a trainer, which allows deletion. Once deleted, this information will be permanently removed from your medical history.</p>
                
                <input type="hidden" id="delete_record_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteRecordModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteMedicalRecord()">
                    <i class="fas fa-trash"></i> Delete Record
                </button>
            </div>
        </div>
    </div>

    <!-- Pass workout/nutrition plan data from controller to JS (data-only) -->
    <script type="application/json" id="medicalData"><?php echo json_encode([
        'workoutPlans' => $data['workoutPlans'] ?? [],
        'nutritionPlans' => $data['nutritionPlans'] ?? [],
    ], JSON_UNESCAPED_SLASHES); ?></script>

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/medical-page.js"></script>
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>