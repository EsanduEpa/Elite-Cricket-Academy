<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/medical-enhanced.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'medical'; ?>

<!-- Trainer Medical Layout -->
<div class="trainer-layout">
    <!-- Left Sidebar Panel -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Trainer'; ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <div class="medical-container">
            <!-- Page Header -->
            <div class="medical-header">
                <h1><i class="fas fa-user-injured"></i> Medical Records Dashboard</h1>
                <p>Comprehensive injury tracking and recovery management for all academy players</p>
                <div class="medical-header-actions">
                    <button class="btn-medical-primary" onclick="showNewInjuryReport()">
                        <i class="fas fa-plus"></i> New Injury Report
                    </button>
                    <button class="btn-medical-secondary" onclick="exportMedicalData()">
                        <i class="fas fa-download"></i> Export Data
                    </button>
                    <button class="btn-medical-secondary">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Medical Statistics -->
            <div class="medical-stats-grid">
                <div class="medical-stat-card">
                    <div class="medical-stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="medical-stat-content">
                        <div class="medical-stat-number">3</div>
                        <div class="medical-stat-label">Active Injuries</div>
                        <div class="medical-stat-change negative">-2 from last month</div>
                    </div>
                </div>
                <div class="medical-stat-card">
                    <div class="medical-stat-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="medical-stat-content">
                        <div class="medical-stat-number">7</div>
                        <div class="medical-stat-label">Recovery Programs</div>
                        <div class="medical-stat-change positive">+1 this week</div>
                    </div>
                </div>
                <div class="medical-stat-card">
                    <div class="medical-stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="medical-stat-content">
                        <div class="medical-stat-number">89%</div>
                        <div class="medical-stat-label">Recovery Rate</div>
                        <div class="medical-stat-change positive">+5% improvement</div>
                    </div>
                </div>
                <div class="medical-stat-card">
                    <div class="medical-stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="medical-stat-content">
                        <div class="medical-stat-number">12</div>
                        <div class="medical-stat-label">Days Average Recovery</div>
                        <div class="medical-stat-change positive">-3 days improvement</div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="medical-content-grid">
                <!-- Active Injuries -->
                <div class="glass-card">
                    <div class="card-header">
                        <h2><i class="fas fa-exclamation-triangle"></i> Active Injuries</h2>
                        <button class="btn-medical-primary" onclick="showNewInjuryReport()">
                            <i class="fas fa-plus"></i> New Report
                        </button>
                    </div>
                    <div class="injury-grid">
                        <div class="injury-card critical">
                            <div class="injury-header">
                                <div class="injury-priority critical">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>Critical</span>
                                </div>
                                <div class="injury-date">Oct 18</div>
                            </div>
                            <div class="injury-content">
                                <div class="player-info">
                                    <div class="player-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="player-details">
                                        <strong>Kumara Silva</strong>
                                        <small>Youth Team</small>
                                    </div>
                                </div>
                                <div class="injury-details">
                                    <h4><i class="fas fa-ankle"></i> Ankle Sprain</h4>
                                    <p>Right ankle injury during practice session</p>
                                    <div class="injury-meta">
                                        <span class="injury-status recovering">
                                            <i class="fas fa-heart"></i> Recovering
                                        </span>
                                        <span class="expected-return">
                                            <i class="fas fa-calendar"></i> Return: Oct 25
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="injury-actions">
                                <button class="btn-action view" onclick="viewInjuryReport('kumara_silva_ankle')" title="View Report">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="updateInjuryReport('kumara_silva_ankle')" title="Update Report">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action schedule" onclick="scheduleCheckup('kumara_silva_ankle')" title="Schedule Checkup">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="injury-card moderate">
                            <div class="injury-header">
                                <div class="injury-priority moderate">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Moderate</span>
                                </div>
                                <div class="injury-date">Oct 15</div>
                            </div>
                            <div class="injury-content">
                                <div class="player-info">
                                    <div class="player-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="player-details">
                                        <strong>Nimal Perera</strong>
                                        <small>Senior Team</small>
                                    </div>
                                </div>
                                <div class="injury-details">
                                    <h4><i class="fas fa-shoulder"></i> Shoulder Strain</h4>
                                    <p>Overuse injury from extensive bowling practice</p>
                                    <div class="injury-meta">
                                        <span class="injury-status treatment">
                                            <i class="fas fa-medkit"></i> Under Treatment
                                        </span>
                                        <span class="expected-return">
                                            <i class="fas fa-calendar"></i> Return: Nov 5
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="injury-actions">
                                <button class="btn-action view" onclick="viewInjuryReport('nimal_perera_shoulder')" title="View Report">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="updateInjuryReport('nimal_perera_shoulder')" title="Update Report">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action schedule" onclick="scheduleCheckup('nimal_perera_shoulder')" title="Schedule Checkup">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="injury-card mild">
                            <div class="injury-header">
                                <div class="injury-priority mild">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Mild</span>
                                </div>
                                <div class="injury-date">Oct 19</div>
                            </div>
                            <div class="injury-content">
                                <div class="player-info">
                                    <div class="player-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="player-details">
                                        <strong>Rashmi Fernando</strong>
                                        <small>Youth Team</small>
                                    </div>
                                </div>
                                <div class="injury-details">
                                    <h4><i class="fas fa-leg"></i> Minor Bruise</h4>
                                    <p>Leg bruise from fielding collision</p>
                                    <div class="injury-meta">
                                        <span class="injury-status mild">
                                            <i class="fas fa-band-aid"></i> Mild Care
                                        </span>
                                        <span class="expected-return">
                                            <i class="fas fa-calendar"></i> Return: Oct 22
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="injury-actions">
                                <button class="btn-action view" onclick="viewInjuryReport('rashmi_fernando_bruise')" title="View Report">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="updateInjuryReport('rashmi_fernando_bruise')" title="Update Report">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action schedule" onclick="scheduleCheckup('rashmi_fernando_bruise')" title="Schedule Checkup">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recovery Progress -->
            <div class="glass-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Recovery Progress</h2>
                    <button class="btn-medical-secondary" onclick="showRecoveryPlans()">
                        <i class="fas fa-plus"></i> New Recovery Plan
                    </button>
                </div>
                    <div class="recovery-grid">
                        <div class="recovery-card">
                            <div class="recovery-header">
                                <div class="player-info">
                                    <div class="player-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="player-details">
                                        <strong>Kumara Silva</strong>
                                        <small>Ankle Recovery Program</small>
                                    </div>
                                </div>
                                <div class="recovery-priority high">
                                    <i class="fas fa-bolt"></i>
                                </div>
                            </div>
                            <div class="recovery-content">
                                <div class="recovery-plan">
                                    <h4><i class="fas fa-heartbeat"></i> Physiotherapy & Rest</h4>
                                    <p>Daily physiotherapy sessions with ice treatment</p>
                                </div>
                                <div class="recovery-progress">
                                    <div class="progress-container">
                                        <div class="progress-bar enhanced">
                                            <div class="progress-fill" style="width: 75%"></div>
                                        </div>
                                        <span class="progress-text">75% Recovered</span>
                                        <small class="progress-detail">Expected return: Oct 25</small>
                                    </div>
                                </div>
                            </div>
                            <div class="recovery-actions">
                                <button class="btn-action view" onclick="viewRecoveryPlan('kumara_silva')" title="View Plan">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="editRecoveryPlan('kumara_silva')" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action check" onclick="markCheckpoint('kumara_silva')" title="Mark Checkpoint">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>

                        <div class="recovery-card">
                            <div class="recovery-header">
                                <div class="player-info">
                                    <div class="player-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="player-details">
                                        <strong>Nimal Perera</strong>
                                        <small>Shoulder Rehabilitation</small>
                                    </div>
                                </div>
                                <div class="recovery-priority medium">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="recovery-content">
                                <div class="recovery-plan">
                                    <h4><i class="fas fa-dumbbell"></i> Strength & Mobility</h4>
                                    <p>Progressive strength building and mobility exercises</p>
                                </div>
                                <div class="recovery-progress">
                                    <div class="progress-container">
                                        <div class="progress-bar enhanced">
                                            <div class="progress-fill" style="width: 40%"></div>
                                        </div>
                                        <span class="progress-text">40% Recovered</span>
                                        <small class="progress-detail">Expected return: Nov 5</small>
                                    </div>
                                </div>
                            </div>
                            <div class="recovery-actions">
                                <button class="btn-action view" onclick="viewRecoveryPlan('nimal_perera')" title="View Plan">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="editRecoveryPlan('nimal_perera')" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action check" onclick="markCheckpoint('nimal_perera')" title="Mark Checkpoint">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>

                        <div class="recovery-card">
                            <div class="recovery-header">
                                <div class="player-info">
                                    <div class="player-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="player-details">
                                        <strong>Rashmi Fernando</strong>
                                        <small>Bruise Healing Program</small>
                                    </div>
                                </div>
                                <div class="recovery-priority low">
                                    <i class="fas fa-leaf"></i>
                                </div>
                            </div>
                            <div class="recovery-content">
                                <div class="recovery-plan">
                                    <h4><i class="fas fa-snowflake"></i> Ice Therapy & Rest</h4>
                                    <p>Regular ice application and activity monitoring</p>
                                </div>
                                <div class="recovery-progress">
                                    <div class="progress-container">
                                        <div class="progress-bar enhanced">
                                            <div class="progress-fill" style="width: 90%"></div>
                                        </div>
                                        <span class="progress-text">90% Recovered</span>
                                        <small class="progress-detail">Expected return: Oct 22</small>
                                    </div>
                                </div>
                            </div>
                            <div class="recovery-actions">
                                <button class="btn-action view" onclick="viewRecoveryPlan('rashmi_fernando')" title="View Plan">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="editRecoveryPlan('rashmi_fernando')" title="Edit Plan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action check" onclick="markCheckpoint('rashmi_fernando')" title="Mark Checkpoint">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History -->
            <div class="glass-card full-width">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Medical History</h2>
                    <div class="filter-controls">
                        <select class="filter-select enhanced">
                            <option value="all">All Players</option>
                            <option value="youth">Youth Team</option>
                            <option value="senior">Senior Team</option>
                        </select>
                        <select class="filter-select enhanced">
                            <option value="all">All Time</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                        <button class="btn-medical-secondary" onclick="exportMedicalHistory()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <table class="medical-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar"></i> Date</th>
                                <th><i class="fas fa-user"></i> Player</th>
                                <th><i class="fas fa-bandage"></i> Injury Type</th>
                                <th><i class="fas fa-exclamation-circle"></i> Severity</th>
                                <th><i class="fas fa-clock"></i> Recovery Time</th>
                                <th><i class="fas fa-flag"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="date-info">
                                        <span class="date">Oct 18, 2025</span>
                                        <small class="time">2:30 PM</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="player-details">
                                            <strong>Kumara Silva</strong>
                                            <small>Youth Team</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="injury-type">
                                        <i class="fas fa-ankle"></i>
                                        <span>Ankle Sprain</span>
                                    </div>
                                </td>
                                <td><span class="severity-badge moderate">Moderate</span></td>
                                <td>
                                    <div class="recovery-time">
                                        <span class="duration">7-10 days</span>
                                        <small class="remaining">3 days left</small>
                                    </div>
                                </td>
                                <td><span class="status-badge recovering">Recovering</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action view" onclick="viewFullReport('report_001')" title="View Report">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action edit" onclick="editMedicalRecord('report_001')" title="Edit Record">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action download" onclick="downloadReport('report_001')" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="date-info">
                                        <span class="date">Oct 15, 2025</span>
                                        <small class="time">10:15 AM</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="player-details">
                                            <strong>Nimal Perera</strong>
                                            <small>Senior Team</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="injury-type">
                                        <i class="fas fa-shoulder"></i>
                                        <span>Shoulder Strain</span>
                                    </div>
                                </td>
                                <td><span class="severity-badge moderate">Moderate</span></td>
                                <td>
                                    <div class="recovery-time">
                                        <span class="duration">2-3 weeks</span>
                                        <small class="remaining">2 weeks left</small>
                                    </div>
                                </td>
                                <td><span class="status-badge treatment">Treatment</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action view" onclick="viewFullReport('report_002')" title="View Report">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action edit" onclick="editMedicalRecord('report_002')" title="Edit Record">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action download" onclick="downloadReport('report_002')" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="date-info">
                                        <span class="date">Oct 19, 2025</span>
                                        <small class="time">4:45 PM</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="player-details">
                                            <strong>Rashmi Fernando</strong>
                                            <small>Youth Team</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="injury-type">
                                        <i class="fas fa-leg"></i>
                                        <span>Minor Bruise</span>
                                    </div>
                                </td>
                                <td><span class="severity-badge mild">Mild</span></td>
                                <td>
                                    <div class="recovery-time">
                                        <span class="duration">2-3 days</span>
                                        <small class="remaining">1 day left</small>
                                    </div>
                                </td>
                                <td><span class="status-badge mild">Mild Care</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action view" onclick="viewFullReport('report_003')" title="View Report">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action edit" onclick="editMedicalRecord('report_003')" title="Edit Record">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action download" onclick="downloadReport('report_003')" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="date-info">
                                        <span class="date">Sep 28, 2025</span>
                                        <small class="time">11:20 AM</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="player-details">
                                            <strong>Saman Wickrama</strong>
                                            <small>Senior Team</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="injury-type">
                                        <i class="fas fa-running"></i>
                                        <span>Hamstring Pull</span>
                                    </div>
                                </td>
                                <td><span class="severity-badge mild">Mild</span></td>
                                <td>
                                    <div class="recovery-time">
                                        <span class="duration">5 days</span>
                                        <small class="completed">Completed</small>
                                    </div>
                                </td>
                                <td><span class="status-badge recovered">Recovered</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action view" onclick="viewFullReport('report_004')" title="View Report">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action archive" onclick="archiveRecord('report_004')" title="Archive">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                        <button class="btn-action download" onclick="downloadReport('report_004')" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>

                            </div> <!-- End medical-content-grid -->
        </div>
    </div>
</div><script src="<?php echo URLROOT; ?>/js/trainer/dashboard.js"></script>
<script>
// Medical-specific functions with enhanced interactions
function showNewInjuryReport() {
    showNotification('New injury report form - Coming soon!', 'info');
}

function viewInjuryReport(injuryId) {
    showNotification('View injury report: ' + injuryId + ' - Coming soon!', 'info');
}

function updateInjuryReport(injuryId) {
    showNotification('Update injury report: ' + injuryId + ' - Coming soon!', 'info');
}

function viewFullReport(reportId) {
    showNotification('View full medical report: ' + reportId + ' - Coming soon!', 'info');
}

function downloadReport(reportId) {
    showNotification('Download report: ' + reportId + ' - Coming soon!', 'info');
}

function exportMedicalData() {
    showNotification('Exporting medical data...', 'info');
}

// Button loading states
function addButtonLoading(button) {
    button.classList.add('btn-loading');
    button.disabled = true;
}

function removeButtonLoading(button) {
    button.classList.remove('btn-loading');
    button.disabled = false;
}

// Enhanced button click handlers
document.addEventListener('DOMContentLoaded', function() {
    // Add click effects to all buttons
    const buttons = document.querySelectorAll('.btn-action, .btn-medical-primary, .btn-medical-secondary');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Simple notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>

<!-- CSS for ripple effect -->
<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.notification {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
}
</style>
</body>
</html>