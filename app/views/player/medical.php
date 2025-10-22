<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
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
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
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
        <div class="main-content">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-heartbeat"></i> Medical Records</h1>
                <p>Track your health, fitness assessments, and medical history.</p>
            </div>

            <?php flash('medical_message'); ?>

            <!-- Health Overview - Compact Table -->
            <div class="schedule-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-heartbeat"></i> Health Overview</h2>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-title"><i class="fas fa-weight"></i> Weight</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">75 kg</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Normal</span>
                                </td>
                                <td>
                                    <div class="table-cell-secondary">Oct 10, 2025</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title"><i class="fas fa-ruler-vertical"></i> Height</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">178 cm</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Normal</span>
                                </td>
                                <td>
                                    <div class="table-cell-secondary">Registration</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title"><i class="fas fa-heart"></i> Resting HR</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">68 bpm</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Excellent</span>
                                </td>
                                <td>
                                    <div class="table-cell-secondary">Oct 10, 2025</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title"><i class="fas fa-percentage"></i> Body Fat</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">12%</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Athletic</span>
                                </td>
                                <td>
                                    <div class="table-cell-secondary">Sept 15, 2025</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title"><i class="fas fa-lungs"></i> Lung Capacity</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">4.2 L</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Above Average</span>
                                </td>
                                <td>
                                    <div class="table-cell-secondary">Sept 15, 2025</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Trainer Assigned Plans -->
            <div class="schedule-row">
                <!-- Current Workout Plans -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-dumbbell"></i> Current Workout Plans</h2>
                            <span class="event-count">2 Active</span>
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
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Strength & Conditioning</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-calendar"></i> 3x per week • Started Oct 5
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Coach Johnson</div>
                                        <div class="table-cell-secondary">Physical Trainer</div>
                                    </td>
                                    <td>
                                        <span class="table-badge status-active">Active</span>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewWorkoutPlan(1)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Cricket-Specific Fitness</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-calendar"></i> Daily • Started Oct 1
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Trainer Mike</div>
                                        <div class="table-cell-secondary">Fitness Specialist</div>
                                    </td>
                                    <td>
                                        <span class="table-badge status-active">Active</span>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewWorkoutPlan(2)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Current Nutrition Plans -->
                <div class="schedule-card">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-apple-alt"></i> Nutrition Plans</h2>
                            <span class="event-count">1 Active</span>
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
                                <tr>
                                    <td>
                                        <div class="table-cell-title">Performance Diet Plan</div>
                                        <div class="table-cell-details">
                                            <i class="fas fa-calendar"></i> 30 days • Started Oct 8
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title">Nutritionist Sarah</div>
                                        <div class="table-cell-secondary">Sports Nutritionist</div>
                                    </td>
                                    <td>
                                        <span class="table-badge status-active">Active</span>
                                    </td>
                                    <td>
                                        <button class="btn-sm" onclick="viewNutritionPlan(1)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Current Supplement Plans -->
            <div class="schedule-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-capsules"></i> Supplement Recommendations</h2>
                        <span class="event-count">3 Active</span>
                    </div>
                </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Supplement</th>
                                <th>Dosage</th>
                                <th>Trainer</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell-title">Whey Protein Powder</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-info-circle"></i> Post-workout recovery
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">25g</div>
                                    <div class="table-cell-secondary">2x daily</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Coach Johnson</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">60 days</div>
                                    <div class="table-cell-secondary">Started Oct 1</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Active</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">Creatine Monohydrate</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-info-circle"></i> Strength & power
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">5g</div>
                                    <div class="table-cell-secondary">Daily</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Coach Johnson</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">90 days</div>
                                    <div class="table-cell-secondary">Started Sept 20</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Active</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">Multivitamin Complex</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-info-circle"></i> General health support
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">1 tablet</div>
                                    <div class="table-cell-secondary">Morning</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Nutritionist Sarah</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">30 days</div>
                                    <div class="table-cell-secondary">Started Oct 8</div>
                                </td>
                                <td>
                                    <span class="table-badge status-active">Active</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Medical Records -->
            <div class="schedule-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-notes-medical"></i> Recent Medical Records</h2>
                        <button class="btn btn-primary" onclick="openAddMedicalModal()">
                            <i class="fas fa-plus"></i> Add Record
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <?php if (!empty($data['medicalRecords'])): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Injury Date</th>
                                    <th>Injury Details</th>
                                    <th>Diagnosis</th>
                                    <th>Treatment</th>
                                    <th>At Academy</th>
                                    <th>Rest Days</th>
                                    <th>Receipt</th>
                                    <th>Recovery Status</th>
                                    <th>Verify Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['medicalRecords'] as $record): ?>
                                <tr>
                                    <td>
                                        <div class="table-cell-primary"><?php echo date('M d, Y', strtotime($record->InjuryDate)); ?></div>
                                        <div class="table-cell-secondary">Reported: <?php echo date('M d', strtotime($record->ReportedDate)); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($record->InjuryDetails); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-title"><?php echo htmlspecialchars($record->Diagnosis); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-cell-secondary"><?php echo htmlspecialchars($record->TreatmentGiven ?: 'N/A'); ?></div>
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
                                        <div class="action-buttons">
                                            <button class="btn-sm btn-secondary" onclick="openUpdateStatusModal(<?php echo $record->RecordID; ?>, '<?php echo $record->RecoveryStatus; ?>')">
                                                <i class="fas fa-edit"></i> Update Status
                                            </button>
                                            <?php if (strtolower($record->verifyStatus ?? 'pending') === 'rejected'): ?>
                                                <button class="btn-sm btn-danger" onclick="confirmDeleteRecord(<?php echo $record->RecordID; ?>)" title="Delete Record">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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

           
            <!-- Vaccinations & Immunizations -->
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
                            <tr>
                                <td>
                                    <div class="table-cell-title">COVID-19 Vaccination</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-virus"></i> mRNA Vaccine
                                    </div>
                                </td>
                                <td>
                                    <span class="table-badge status-up-to-date">
                                        <i class="fas fa-check-circle"></i> Up to Date
                                    </span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">March 2025</div>
                                    <div class="table-cell-secondary">Booster received</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">March 2026</div>
                                    <div class="table-cell-secondary">Annual booster</div>
                                </td>
                                <td>
                                    <div class="table-cell-details">
                                        <i class="fas fa-shield-virus medical-status-protective"></i> Fully vaccinated
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="table-cell-title">Tetanus Shot</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-bandage"></i> Tetanus Toxoid
                                    </div>
                                </td>
                                <td>
                                    <span class="table-badge status-up-to-date">
                                        <i class="fas fa-check-circle"></i> Up to Date
                                    </span>
                                </td>
                                <td>
                                    <div class="table-cell-primary">2018</div>
                                    <div class="table-cell-secondary">Standard dose</div>
                                </td>
                                <td>
                                    <div class="table-cell-primary">2028</div>
                                    <div class="table-cell-secondary">10-year cycle</div>
                                </td>
                                <td>
                                    <div class="table-cell-details">
                                        <i class="fas fa-check-circle medical-status-good"></i> No adverse reactions
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Medical Actions -->
            <div class="quick-actions">
                <h3><i class="fas fa-bolt"></i> Quick Medical Actions</h3>
                <div class="action-buttons">
                    <button class="action-btn" onclick="alert('Schedule checkup feature coming soon!')">
                        <i class="fas fa-calendar-plus"></i> Schedule Checkup
                    </button>
                    <button class="action-btn" onclick="alert('Download records feature coming soon!')">
                        <i class="fas fa-download"></i> Download Records
                    </button>
                    <button class="action-btn" onclick="alert('Emergency contacts feature coming soon!')">
                        <i class="fas fa-phone"></i> Emergency Contacts
                    </button>
                    <a href="<?php echo URLROOT; ?>/player/trainerplans" class="action-btn">
                        <i class="fas fa-clipboard-list"></i> View All Trainer Plans
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Medical Record Modal -->
    <div id="addMedicalModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3><i class="fas fa-notes-medical"></i> Add Medical Record</h3>
                <span class="close" onclick="closeAddMedicalModal()">&times;</span>
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
                        <label for="injury_details">Injury/Health Details *</label>
                        <textarea id="injury_details" name="injury_details" class="form-control" rows="3" required 
                                placeholder="Describe the injury, symptoms, or health condition in detail..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="diagnosis">Diagnosis *</label>
                        <textarea id="diagnosis" name="diagnosis" class="form-control" rows="2" required 
                                placeholder="Medical diagnosis or assessment..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="treatment_given">Treatment Given</label>
                        <textarea id="treatment_given" name="treatment_given" class="form-control" rows="2" 
                                placeholder="Treatment provided, medications, therapy, etc..."></textarea>
                    </div>
                    
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="rest_days_needed">Estimated Rest Days Needed</label>
                            <input type="number" id="rest_days_needed" name="rest_days_needed" class="form-control" min="0" value="0" placeholder="e.g. 7">
                            <small class="form-text">Number of days rest required</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="recovery_status">Recovery Status *</label>
                            <select id="recovery_status" name="recovery_status" class="form-control" required>
                                <option value="">Select status...</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="recovering">Recovering</option>
                                <option value="recovered">Fully Recovered</option>
                                <option value="chronic">Chronic Condition</option>
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
                            <option value="recovered">Fully Recovered</option>
                            <option value="chronic">Chronic Condition</option>
                        </select>
                    </div>
                    
                    <div class="form-help">
                        <p><strong>Status Definitions:</strong></p>
                        <ul>
                            <li><strong>Ongoing:</strong> Condition is still active/symptomatic</li>
                            <li><strong>Recovering:</strong> In the process of healing</li>
                            <li><strong>Fully Recovered:</strong> No symptoms, returned to full activity</li>
                            <li><strong>Chronic:</strong> Long-term condition requiring ongoing management</li>
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

    <script>
        // Add Medical Record Modal Functions
        function openAddMedicalModal() {
            const modal = document.getElementById('addMedicalModal');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Set default date to today
            const dateInput = document.getElementById('reported_date');
            if (!dateInput.value) {
                dateInput.value = new Date().toISOString().split('T')[0];
            }
        }
        
        function closeAddMedicalModal() {
            const modal = document.getElementById('addMedicalModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Reset form
            const form = modal.querySelector('form');
            form.reset();
        }
        
        function viewMedicalRecord(recordId) {
            // This would show detailed view of the medical record
            alert('Viewing medical record #' + recordId);
        }
        
        // View workout plan function
        function viewWorkoutPlan(planId) {
            const modal = document.getElementById('workoutPlanModal');
            const content = document.getElementById('workoutPlanContent');
            
            // Sample workout plan content (in real app, this would be fetched from server)
            const workoutPlans = {
                1: {
                    title: 'Strength & Conditioning',
                    trainer: 'Coach Johnson',
                    frequency: '3x per week',
                    duration: '45-60 minutes',
                    details: `
                        <h4>Week 1-2: Foundation Phase</h4>
                        <ul>
                            <li>Warm-up: 10 minutes dynamic stretching</li>
                            <li>Squats: 3 sets x 12 reps</li>
                            <li>Push-ups: 3 sets x 15 reps</li>
                            <li>Planks: 3 sets x 30 seconds</li>
                            <li>Pull-ups: 3 sets x 8 reps</li>
                            <li>Cool-down: 10 minutes static stretching</li>
                        </ul>
                        <h4>Week 3-4: Progression Phase</h4>
                        <ul>
                            <li>Warm-up: 10 minutes dynamic stretching</li>
                            <li>Weighted squats: 4 sets x 10 reps</li>
                            <li>Bench press: 4 sets x 12 reps</li>
                            <li>Planks: 4 sets x 45 seconds</li>
                            <li>Chin-ups: 4 sets x 10 reps</li>
                            <li>Cool-down: 10 minutes static stretching</li>
                        </ul>
                    `
                },
                2: {
                    title: 'Cricket-Specific Fitness',
                    trainer: 'Trainer Mike',
                    frequency: 'Daily',
                    duration: '30-45 minutes',
                    details: `
                        <h4>Daily Routine</h4>
                        <ul>
                            <li>Cricket-specific warm-up: 10 minutes</li>
                            <li>Agility ladder drills: 15 minutes</li>
                            <li>Batting stance practice: 10 minutes</li>
                            <li>Fielding position drills: 15 minutes</li>
                            <li>Cool-down stretches: 10 minutes</li>
                        </ul>
                        <h4>Focus Areas</h4>
                        <ul>
                            <li>Hand-eye coordination</li>
                            <li>Reaction time improvement</li>
                            <li>Cricket-specific movements</li>
                            <li>Endurance building</li>
                        </ul>
                    `
                }
            };
            
            const plan = workoutPlans[planId];
            if (plan) {
                content.innerHTML = `
                    <div class="plan-header">
                        <h4>${plan.title}</h4>
                        <p><strong>Trainer:</strong> ${plan.trainer}</p>
                        <p><strong>Frequency:</strong> ${plan.frequency}</p>
                        <p><strong>Duration:</strong> ${plan.duration}</p>
                    </div>
                    <div class="plan-details">
                        ${plan.details}
                    </div>
                `;
            }
            
            modal.style.display = 'block';
        }

        // View nutrition plan function
        function viewNutritionPlan(planId) {
            const modal = document.getElementById('nutritionPlanModal');
            const content = document.getElementById('nutritionPlanContent');
            
            // Sample nutrition plan content
            const nutritionPlans = {
                1: {
                    title: 'Performance Diet Plan',
                    trainer: 'Nutritionist Sarah',
                    duration: '30 days',
                    details: `
                        <h4>Daily Meal Plan</h4>
                        <div class="meal-plan">
                            <h5>Breakfast (7:00 AM)</h5>
                            <ul>
                                <li>Oatmeal with berries and nuts</li>
                                <li>Greek yogurt</li>
                                <li>Green tea</li>
                            </ul>
                            
                            <h5>Mid-Morning Snack (10:00 AM)</h5>
                            <ul>
                                <li>Banana with almond butter</li>
                                <li>Water (500ml)</li>
                            </ul>
                            
                            <h5>Lunch (1:00 PM)</h5>
                            <ul>
                                <li>Grilled chicken breast</li>
                                <li>Brown rice</li>
                                <li>Steamed vegetables</li>
                                <li>Water (500ml)</li>
                            </ul>
                            
                            <h5>Pre-Training Snack (3:30 PM)</h5>
                            <ul>
                                <li>Apple with honey</li>
                                <li>Sports drink</li>
                            </ul>
                            
                            <h5>Post-Training (6:00 PM)</h5>
                            <ul>
                                <li>Protein shake</li>
                                <li>Banana</li>
                            </ul>
                            
                            <h5>Dinner (8:00 PM)</h5>
                            <ul>
                                <li>Grilled fish or lean meat</li>
                                <li>Quinoa or sweet potato</li>
                                <li>Green salad</li>
                                <li>Water (500ml)</li>
                            </ul>
                        </div>
                        
                        <h4>Key Guidelines</h4>
                        <ul>
                            <li>Drink at least 3 liters of water daily</li>
                            <li>Eat every 3-4 hours</li>
                            <li>Include protein in every meal</li>
                            <li>Avoid processed foods and sugary drinks</li>
                            <li>Time carbohydrate intake around training sessions</li>
                        </ul>
                    `
                }
            };
            
            const plan = nutritionPlans[planId];
            if (plan) {
                content.innerHTML = `
                    <div class="plan-header">
                        <h4>${plan.title}</h4>
                        <p><strong>Nutritionist:</strong> ${plan.trainer}</p>
                        <p><strong>Duration:</strong> ${plan.duration}</p>
                    </div>
                    <div class="plan-details">
                        ${plan.details}
                    </div>
                `;
            }
            
            modal.style.display = 'block';
        }

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Update Status Modal Functions
        function openUpdateStatusModal(recordId, currentStatus) {
            const modal = document.getElementById('updateStatusModal');
            const recordIdInput = document.getElementById('update_record_id');
            const statusSelect = document.getElementById('update_recovery_status');
            
            // Set the record ID
            recordIdInput.value = recordId;
            
            // Set the current status as selected
            statusSelect.value = currentStatus;
            
            // Show modal
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeUpdateStatusModal() {
            const modal = document.getElementById('updateStatusModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Reset form
            const form = modal.querySelector('form');
            form.reset();
        }

        // Delete Record Modal Functions
        function confirmDeleteRecord(recordId) {
            const modal = document.getElementById('deleteRecordModal');
            const recordIdInput = document.getElementById('delete_record_id');
            
            // Set the record ID
            recordIdInput.value = recordId;
            
            // Show modal
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeDeleteRecordModal() {
            const modal = document.getElementById('deleteRecordModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        function deleteMedicalRecord() {
            const recordId = document.getElementById('delete_record_id').value;
            
            if (!recordId) {
                alert('Error: No record ID found');
                return;
            }

            // Create form data
            const formData = new FormData();
            formData.append('record_id', recordId);

            // Send delete request
            fetch('<?php echo URLROOT; ?>/player/deleteMedicalRecord', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and reload page
                    alert('Medical record deleted successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete medical record');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the record');
            })
            .finally(() => {
                closeDeleteRecordModal();
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const addMedicalModal = document.getElementById('addMedicalModal');
            const workoutModal = document.getElementById('workoutPlanModal');
            const nutritionModal = document.getElementById('nutritionPlanModal');
            const updateStatusModal = document.getElementById('updateStatusModal');
            const deleteRecordModal = document.getElementById('deleteRecordModal');
            
            if (event.target === addMedicalModal) {
                closeAddMedicalModal();
            }
            if (event.target === workoutModal) {
                workoutModal.style.display = 'none';
            }
            if (event.target === nutritionModal) {
                nutritionModal.style.display = 'none';
            }
            if (event.target === updateStatusModal) {
                closeUpdateStatusModal();
            }
            if (event.target === deleteRecordModal) {
                closeDeleteRecordModal();
            }
        }
    </script>

    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/medical.js"></script>
</body>
</html>