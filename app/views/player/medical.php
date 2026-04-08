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
                        <a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Book Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="nav-link">
                            <i class="fas fa-list-alt"></i>
                            <span>My Sessions</span>
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
                                                <button class="action-btn btn-update" onclick="openUpdateStatusModal(
                                                    <?php echo $record->RecordID; ?>,
                                                    '<?php echo htmlspecialchars($record->RecoveryStatus, ENT_QUOTES); ?>',
                                                    '<?php echo strtolower($record->verifyStatus ?? 'pending'); ?>',
                                                    '<?php echo htmlspecialchars($record->Diagnosis, ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($record->TreatmentGiven ?? '', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($record->bodyarea ?? '', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($record->InjuryDate ?? '', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($record->ReportedDate ?? '', ENT_QUOTES); ?>',
                                                    '<?php echo htmlspecialchars($record->HappenedAtAcademy ?? 'no', ENT_QUOTES); ?>',
                                                    <?php echo intval($record->RestDaysNeeded); ?>
                                                )" title="Update Record">
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
    <div id="addMedicalModal" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s ease-in-out; overflow-y: auto;">
        <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 3% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 750px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out; max-height: 85vh; overflow-y: auto;">
            <!-- Modal Header -->
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-notes-medical" style="color: #fff;"></i> Add Medical Record
                </h2>
                <span class="close" onclick="closeAddMedicalModal()" style="color: #fff; font-size: 32px; font-weight: bold; cursor: pointer; transition: all 0.3s; padding: 5px; border-radius: 50%; opacity: 0.8; line-height: 1;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.opacity='1';" onmouseout="this.style.backgroundColor='transparent'; this.style.opacity='0.8';">&times;</span>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="<?php echo URLROOT; ?>/player/addMedicalRecord" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 35px;">

                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 25px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label for="injury_date" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-calendar-alt" style="color: #4A90E2; margin-right: 8px;"></i> Injury Date *
                            </label>
                            <input type="date" id="injury_date" name="injury_date" required max="<?php echo date('Y-m-d'); ?>"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                            <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;">When did the injury occur?</small>
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label for="reported_date" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-calendar-check" style="color: #4A90E2; margin-right: 8px;"></i> Reported Date *
                            </label>
                            <input type="date" id="reported_date" name="reported_date" required max="<?php echo date('Y-m-d'); ?>"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                            <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;">When are you reporting this?</small>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-hospital" style="color: #4A90E2; margin-right: 8px;"></i> Did the injury happen at the academy? *
                        </label>
                        <div style="display: flex; gap: 25px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: #2c3e50; font-size: 14px;">
                                <input type="radio" name="happened_at_academy" value="yes" required> Yes
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: #2c3e50; font-size: 14px;">
                                <input type="radio" name="happened_at_academy" value="no" checked> No
                            </label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="body_area" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-user-injured" style="color: #e74c3c; margin-right: 8px;"></i> Body Area *
                        </label>
                        <select id="body_area" name="body_area" required
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
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

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="diagnosis" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-stethoscope" style="color: #e74c3c; margin-right: 8px;"></i> Diagnosis *
                        </label>
                        <select id="diagnosis" name="diagnosis" required
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
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

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="treatment_given" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-hand-holding-medical" style="color: #27ae60; margin-right: 8px;"></i> Treatment Given
                        </label>
                        <select id="treatment_given" name="treatment_given"
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
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

                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 25px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label for="rest_days_needed" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-bed" style="color: #f39c12; margin-right: 8px;"></i> Estimated Rest Days Needed
                            </label>
                            <input type="number" id="rest_days_needed" name="rest_days_needed" min="0" max="1000" step="1" value="0" placeholder="e.g. 7"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                            <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;">Number of days rest required (max 1000)</small>
                            <small id="rest_days_error" style="color: #e74c3c; font-size: 12px; margin-top: 3px; display: none;"></small>
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label for="recovery_status" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-heartbeat" style="color: #e74c3c; margin-right: 8px;"></i> Recovery Status *
                            </label>
                            <select id="recovery_status" name="recovery_status" required
                                    style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                    onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                    onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                                <option value="">Select status...</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="recovering">Recovering</option>
                                <option value="fully_recovered">Fully Recovered</option>
                                <option value="chronic_condition">Chronic Condition</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="diagnosis_receipt" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-file-medical" style="color: #4A90E2; margin-right: 8px;"></i> Diagnosis Receipt/Document (Optional)
                        </label>
                        <input type="file" id="diagnosis_receipt" name="diagnosis_receipt" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                               style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: #fafafa; box-sizing: border-box;">
                        <small style="color: #7f8c8d; font-size: 12px; margin-top: 5px; display: block;"><i class="fas fa-info-circle"></i> Upload medical receipt, prescription, or diagnosis document (JPG, PNG, PDF, DOC - Max 5MB)</small>
                    </div>

                    <div style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #ecf0f1; display: flex; gap: 15px; justify-content: flex-end;">
                        <button type="button" onclick="closeAddMedicalModal()"
                                style="padding: 14px 28px; background: #95a5a6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px;"
                                onmouseover="this.style.backgroundColor='#7f8c8d';"
                                onmouseout="this.style.backgroundColor='#95a5a6';">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit"
                                style="padding: 14px 28px; background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(74,144,226,0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(74,144,226,0.4)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(74,144,226,0.3)';">
                            <i class="fas fa-save"></i> Save Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

    <!-- Update Medical Record Modal — Full edit (pending) -->
    <div id="editFullRecordModal" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s ease-in-out; overflow-y: auto;">
        <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 3% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 750px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out; max-height: 85vh; overflow-y: auto;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-edit" style="color: #fff;"></i> Edit Medical Record
                </h2>
                <span class="close" onclick="closeEditFullRecordModal()" style="color: #fff; font-size: 32px; font-weight: bold; cursor: pointer; transition: all 0.3s; padding: 5px; border-radius: 50%; opacity: 0.8; line-height: 1;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.opacity='1';" onmouseout="this.style.backgroundColor='transparent'; this.style.opacity='0.8';">&times;</span>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/player/fullUpdateMedicalRecord">
                <div class="modal-body" style="padding: 35px;">
                    <input type="hidden" id="edit_record_id" name="record_id">

                    <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                        <div style="flex: 1;">
                            <label for="edit_injury_date" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-calendar-alt" style="color: #4A90E2; margin-right: 8px;"></i> Injury Date *
                            </label>
                            <input type="date" id="edit_injury_date" name="injury_date" required max="<?php echo date('Y-m-d'); ?>"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                        </div>
                        <div style="flex: 1;">
                            <label for="edit_reported_date" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-calendar-check" style="color: #4A90E2; margin-right: 8px;"></i> Reported Date *
                            </label>
                            <input type="date" id="edit_reported_date" name="reported_date" required max="<?php echo date('Y-m-d'); ?>"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-hospital" style="color: #4A90E2; margin-right: 8px;"></i> Did the injury happen at the academy? *
                        </label>
                        <div style="display: flex; gap: 25px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: #2c3e50; font-size: 14px;">
                                <input type="radio" id="edit_academy_yes" name="happened_at_academy" value="yes"> Yes
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: #2c3e50; font-size: 14px;">
                                <input type="radio" id="edit_academy_no" name="happened_at_academy" value="no"> No
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label for="edit_body_area" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-user-injured" style="color: #e74c3c; margin-right: 8px;"></i> Body Area *
                        </label>
                        <select id="edit_body_area" name="body_area" required
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
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

                    <div style="margin-bottom: 25px;">
                        <label for="edit_diagnosis" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-stethoscope" style="color: #e74c3c; margin-right: 8px;"></i> Diagnosis *
                        </label>
                        <select id="edit_diagnosis" name="diagnosis" required
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
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

                    <div style="margin-bottom: 25px;">
                        <label for="edit_treatment" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-hand-holding-medical" style="color: #27ae60; margin-right: 8px;"></i> Treatment Given
                        </label>
                        <select id="edit_treatment" name="treatment_given"
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
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

                    <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                        <div style="flex: 1;">
                            <label for="edit_rest_days" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-bed" style="color: #f39c12; margin-right: 8px;"></i> Estimated Rest Days Needed
                            </label>
                            <input type="number" id="edit_rest_days" name="rest_days_needed" min="0" max="1000" step="1" value="0"
                                   style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                   onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                        </div>
                        <div style="flex: 1;">
                            <label for="edit_recovery_status" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                                <i class="fas fa-heartbeat" style="color: #e74c3c; margin-right: 8px;"></i> Recovery Status *
                            </label>
                            <select id="edit_recovery_status" name="recovery_status" required
                                    style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                    onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                    onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                                <option value="">Select status...</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="recovering">Recovering</option>
                                <option value="fully_recovered">Fully Recovered</option>
                                <option value="chronic_condition">Chronic Condition</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #ecf0f1; display: flex; gap: 15px; justify-content: flex-end;">
                        <button type="button" onclick="closeEditFullRecordModal()"
                                style="padding: 14px 28px; background: #95a5a6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px;"
                                onmouseover="this.style.backgroundColor='#7f8c8d';"
                                onmouseout="this.style.backgroundColor='#95a5a6';">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit"
                                style="padding: 14px 28px; background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(74,144,226,0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(74,144,226,0.4)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(74,144,226,0.3)';">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Medical Record Modal — Recovery status only (verified) -->
    <div id="updateStatusModal" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s ease-in-out; overflow-y: auto;">
        <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 5% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 520px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideIn 0.3s ease-out;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-heartbeat" style="color: #fff;"></i> Update Recovery Status
                </h2>
                <span class="close" onclick="closeUpdateStatusModal()" style="color: #fff; font-size: 32px; font-weight: bold; cursor: pointer; transition: all 0.3s; padding: 5px; border-radius: 50%; opacity: 0.8; line-height: 1;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.opacity='1';" onmouseout="this.style.backgroundColor='transparent'; this.style.opacity='0.8';">&times;</span>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/player/updateMedicalRecord">
                <div class="modal-body" style="padding: 35px;">
                    <input type="hidden" id="update_record_id" name="record_id">

                    <div style="margin-bottom: 25px;">
                        <label for="update_recovery_status" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2c3e50; font-size: 14px;">
                            <i class="fas fa-heartbeat" style="color: #e74c3c; margin-right: 8px;"></i> Recovery Status *
                        </label>
                        <select id="update_recovery_status" name="recovery_status" required
                                style="width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: #fafafa;"
                                onfocus="this.style.borderColor='#4A90E2'; this.style.backgroundColor='white'; this.style.boxShadow='0 0 0 3px rgba(74,144,226,0.1)';"
                                onblur="this.style.borderColor='#ddd'; this.style.backgroundColor='#fafafa'; this.style.boxShadow='none';">
                            <option value="">Select status...</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="recovering">Recovering</option>
                            <option value="fully_recovered">Fully Recovered</option>
                            <option value="chronic_condition">Chronic Condition</option>
                        </select>
                    </div>

                    <div style="background: #f0f7ff; border-left: 4px solid #4A90E2; border-radius: 6px; padding: 16px; margin-bottom: 10px; font-size: 13px; color: #2c3e50;">
                        <p style="margin: 0 0 8px 0; font-weight: 600;"><i class="fas fa-info-circle" style="color: #4A90E2; margin-right: 6px;"></i> Status Definitions</p>
                        <ul style="margin: 0; padding-left: 18px; line-height: 1.8;">
                            <li><strong>Ongoing:</strong> Condition is still active/symptomatic</li>
                            <li><strong>Recovering:</strong> In the process of healing</li>
                            <li><strong>Fully Recovered:</strong> No symptoms, returned to full activity</li>
                            <li><strong>Chronic Condition:</strong> Long-term condition requiring ongoing management</li>
                        </ul>
                    </div>

                    <div style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #ecf0f1; display: flex; gap: 15px; justify-content: flex-end;">
                        <button type="button" onclick="closeUpdateStatusModal()"
                                style="padding: 14px 28px; background: #95a5a6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px;"
                                onmouseover="this.style.backgroundColor='#7f8c8d';"
                                onmouseout="this.style.backgroundColor='#95a5a6';">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit"
                                style="padding: 14px 28px; background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(74,144,226,0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(74,144,226,0.4)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(74,144,226,0.3)';">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
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