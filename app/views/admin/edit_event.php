<?php
// Debug using error_log instead of HTML comments to avoid contaminating output
if (isset($data['event'])) {
    error_log("Edit Event View - Event keys: " . implode(', ', array_keys($data['event'])));
} else {
    error_log("Edit Event View - NO EVENT DATA");
}
?>
<<<<<<< HEAD
<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
    /* Edit Event Form Specific Styles */
=======
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Elite Cricket Academy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Edit Event Form Specific Styles */
>>>>>>> admin
    .edit-event-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-header {
        background: linear-gradient(135deg, rgba(74, 144, 226, 0.95) 0%, rgba(53, 122, 189, 0.9) 100%);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(74, 144, 226, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .event-id-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .back-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        font-weight: 600;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .form-content {
        padding: 2.5rem;
    }
    
    .form-section {
        margin-bottom: 2.5rem;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #4A90E2;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid rgba(74, 144, 226, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .section-title i {
        font-size: 1.5rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .form-group label .required {
        color: #e74c3c;
        margin-left: 3px;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s;
        background: #f8f9fa;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #4A90E2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        background: white;
    }
    
    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }
    
    .form-group input[readonly] {
        background: #e9ecef;
        cursor: not-allowed;
        color: #6c757d;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 2rem;
        border-top: 3px solid rgba(74, 144, 226, 0.1);
        margin-top: 2rem;
    }
    
    .btn {
        padding: 14px 30px;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2);
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
    }
    
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 2px solid #c3e6cb;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 2px solid #f5c6cb;
    }
    
    .alert i {
        font-size: 1.2rem;
    }
</style>
<<<<<<< HEAD
=======
    </style>
</head>
<body>

>>>>>>> admin

<!-- Admin Dashboard Layout -->
<div class="admin-layout">
    <!-- Left Sidebar Panel -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-user-shield"></i>
                <h3>Admin Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard Overview</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/players" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Player Management</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events & Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Feedback Monitoring</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Finance Management</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Admin Profile -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?></div>
            <div class="profile-role">Super Administrator</div>
            <a href="<?php echo URLROOT; ?>/admin/profile" class="action-btn" style="margin-top: 10px;">
                <i class="fas fa-user-cog"></i> Profile
            </a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 8px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <div class="edit-event-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Edit Event
                    <?php if (!empty($data['event']['EventID'])): ?>
                        <span class="event-id-badge">ID: <?= htmlspecialchars($data['event']['EventID'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </h1>
                <a href="<?= URLROOT; ?>/admin/events" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-content">
        <?php flash('event_message'); ?>

        <!-- Info Box -->
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Edit Permissions</h3>
            <p><span class="readonly-indicator">🔒 READ-ONLY:</span> Event Type, Category (cannot be changed after creation)</p>
            <p><span class="editable-indicator">✏️ EDITABLE:</span> Event Name, Description, Status, Dates, Location, Registration Details, Contact Info</p>
        </div>

        <?php if (!empty($data['event'])): ?>
            <?php 
                $event = $data['event'];
                
                // Normalize data: map lowercase aliases to proper uppercase column names
                // This handles both getEventById() (proper keys) and getUpcomingEvents() (aliased keys)
                $normalized = [
                    'EventID' => $event['EventID'] ?? $event['id'] ?? '',
                    'Name' => $event['Name'] ?? $event['title'] ?? '',
                    'Type' => $event['Type'] ?? ($event['event_type'] ? ucwords(str_replace('_', ' ', $event['event_type'])) : ''),
                    'Category' => $event['Category'] ?? '',
                    'Description' => $event['Description'] ?? $event['description'] ?? '',
                    'StartDate' => $event['StartDate'] ?? $event['event_date'] ?? '',
                    'EndDate' => $event['EndDate'] ?? '',
                    'Location' => $event['Location'] ?? $event['location'] ?? '',
                    'Status' => $event['Status'] ?? $event['status'] ?? '',
                    'RegistrationStart' => $event['RegistrationStart'] ?? '',
                    'RegistrationEnd' => $event['RegistrationEnd'] ?? '',
                    'MaxParticipants' => $event['MaxParticipants'] ?? '',
                    'RegistrationFee' => $event['RegistrationFee'] ?? '',
                    'PrimaryContact' => $event['PrimaryContact'] ?? '',
                    'ContactEmail' => $event['ContactEmail'] ?? '',
                    'ContactPhone' => $event['ContactPhone'] ?? ''
                ];
                
                // Use normalized data for consistency
                $event = $normalized;
                
                // Format dates for form inputs
                $startDate = !empty($event['StartDate']) ? date('Y-m-d', strtotime($event['StartDate'])) : '';
                $startTime = !empty($event['StartDate']) ? date('H:i', strtotime($event['StartDate'])) : '';
                $endDate   = !empty($event['EndDate']) ? date('Y-m-d', strtotime($event['EndDate'])) : '';
                $endTime   = !empty($event['EndDate']) ? date('H:i', strtotime($event['EndDate'])) : '';
                $regStart  = !empty($event['RegistrationStart']) ? date('Y-m-d\TH:i', strtotime($event['RegistrationStart'])) : '';
                $regEnd    = !empty($event['RegistrationEnd']) ? date('Y-m-d\TH:i', strtotime($event['RegistrationEnd'])) : '';
            ?>

            <form action="<?= URLROOT; ?>/admin/edit_event/<?= htmlspecialchars($event['EventID'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" method="POST" id="editEventForm">
                
                <!-- Event ID -->
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-hashtag"></i> Event ID</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="EventID">Event ID</label>
                            <input type="text" id="EventID" value="<?= htmlspecialchars($event['EventID'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                        </div>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="Name">Event Name <span class="required">*</span></label>
                            <input type="text" id="Name" name="Name" value="<?= htmlspecialchars($event['Name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Type">Event Type <span class="required">*</span> <span style="background:#fbbf24;color:#78350f;padding:2px 8px;border-radius:4px;font-size:11px;margin-left:5px;">READ ONLY</span></label>
                            <?php $type = $event['Type'] ?? ''; ?>
                            <input type="text" id="Type" name="Type" value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>" readonly style="background: #f8f9fa; cursor: not-allowed;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="Category">Category <span style="background:#fbbf24;color:#78350f;padding:2px 8px;border-radius:4px;font-size:11px;margin-left:5px;">READ ONLY</span></label>
                            <?php $category = $event['Category'] ?? ''; ?>
                            <input type="text" id="Category" name="Category" value="<?= htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?>" readonly style="background: #f8f9fa; cursor: not-allowed;">
                        </div>

                        <div class="form-group">
                            <label for="Status">Status <span class="required">*</span></label>
                            <?php $status = $event['Status'] ?? ''; ?>
                            <select id="Status" name="Status" required>
                                <?php 
                                    $statuses = [
                                        'upcoming' => 'Upcoming',
                                        'registration_open' => 'Registration Open',
                                        'registration_closed' => 'Registration Closed',
                                        'ongoing' => 'Ongoing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled'
                                    ];
                                    foreach($statuses as $key => $label): ?>
                                        <option value="<?= $key; ?>" <?= ($status === $key) ? 'selected' : ''; ?>>
                                            <?= $label; ?>
                                        </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="Description">Description</label>
                            <textarea id="Description" name="Description"><?= htmlspecialchars($event['Description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-calendar"></i> Date & Time</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="StartDate">Start Date <span class="required">*</span></label>
                            <input type="date" id="StartDate_date" name="StartDate_date" value="<?= $startDate; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="StartTime">Start Time <span class="required">*</span></label>
                            <input type="time" id="StartTime" name="StartTime" value="<?= $startTime; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="EndDate">End Date <span class="required">*</span></label>
                            <input type="date" id="EndDate_date" name="EndDate_date" value="<?= $endDate; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="EndTime">End Time <span class="required">*</span></label>
                            <input type="time" id="EndTime" name="EndTime" value="<?= $endTime; ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-map-marker-alt"></i> Location</div>
                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="Location">Venue Location <span class="required">*</span></label>
                            <input type="text" id="Location" name="Location" value="<?= htmlspecialchars($event['Location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Registration Details -->
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-user-plus"></i> Registration Details</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="RegistrationStart">Registration Start</label>
                            <input type="datetime-local" id="RegistrationStart" name="RegistrationStart" value="<?= $regStart; ?>">
                        </div>
                        <div class="form-group">
                            <label for="RegistrationEnd">Registration End</label>
                            <input type="datetime-local" id="RegistrationEnd" name="RegistrationEnd" value="<?= $regEnd; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="MaxParticipants">Max Participants</label>
                            <input type="number" id="MaxParticipants" name="MaxParticipants" value="<?= htmlspecialchars($event['MaxParticipants'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" min="1">
                        </div>
                        <div class="form-group">
                            <label for="RegistrationFee">Registration Fee (LKR)</label>
                            <input type="number" id="RegistrationFee" name="RegistrationFee" value="<?= htmlspecialchars($event['RegistrationFee'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-address-book"></i> Contact Information</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="PrimaryContact">Primary Contact <span class="required">*</span></label>
                            <input type="text" id="PrimaryContact" name="PrimaryContact" value="<?= htmlspecialchars($event['PrimaryContact'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ContactPhone">Contact Phone <span class="required">*</span></label>
                            <input type="tel" id="ContactPhone" name="ContactPhone" value="<?= htmlspecialchars($event['ContactPhone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="ContactEmail">Contact Email <span class="required">*</span></label>
                            <input type="email" id="ContactEmail" name="ContactEmail" value="<?= htmlspecialchars($event['ContactEmail'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?= URLROOT; ?>/admin/events" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Event
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                Event data not found.
            </div>
        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Edit Event Page Loaded');

// Sidebar toggle functionality
const sidebarToggle = document.getElementById('sidebarToggle');
const adminSidebar = document.getElementById('adminSidebar');
const mainContent = document.getElementById('mainContent');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
        adminSidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });
}

// Log event data to console
<?php if (!empty($data['event'])): ?>
console.log('Event Data:', <?= json_encode($data['event']); ?>);
console.log('Event ID:', '<?= htmlspecialchars($event['EventID'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Event Name:', '<?= htmlspecialchars($event['Name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Event Type:', '<?= htmlspecialchars($event['Type'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Category:', '<?= htmlspecialchars($event['Category'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Status:', '<?= htmlspecialchars($event['Status'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Location:', '<?= htmlspecialchars($event['Location'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Start Date:', '<?= htmlspecialchars($event['StartDate'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('End Date:', '<?= htmlspecialchars($event['EndDate'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
<?php else: ?>
console.error('No event data available');
<?php endif; ?>

// Form validation
const form = document.getElementById('editEventForm');
if (form) {
    form.addEventListener('submit', function(e) {
        console.log('Form submitted');
        
        // Validate dates
        const startDateVal = document.getElementById('StartDate_date').value;
        const startTimeVal = document.getElementById('StartTime').value;
        const endDateVal = document.getElementById('EndDate_date').value;
        const endTimeVal = document.getElementById('EndTime').value;
        
        if (startDateVal && startTimeVal && endDateVal && endTimeVal) {
            const startDateTime = new Date(startDateVal + ' ' + startTimeVal);
            const endDateTime = new Date(endDateVal + ' ' + endTimeVal);
            
            if (endDateTime <= startDateTime) {
                e.preventDefault();
                alert('⚠️ End date/time must be after start date/time');
                console.error('Date validation failed: End date must be after start date');
                return false;
            }
        }
        
        console.log('Form validation passed');
    });
}
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
