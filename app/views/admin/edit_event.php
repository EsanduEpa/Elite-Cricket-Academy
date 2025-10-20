<?php
// Debug using error_log instead of HTML comments to avoid contaminating output
if (isset($data['event'])) {
    error_log("Edit Event View - Event keys: " . implode(', ', array_keys($data['event'])));
} else {
    error_log("Edit Event View - NO EVENT DATA");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Elite Cricket Academy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; gap: 15px; }
        .back-btn { background: rgba(255, 255, 255, 0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .back-btn:hover { background: rgba(255, 255, 255, 0.3); transform: translateY(-2px); }
        .form-content { padding: 40px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-section { margin-bottom: 35px; }
        .section-title { font-size: 20px; font-weight: 600; color: #667eea; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0; display: flex; align-items: center; gap: 10px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 500; color: #333; margin-bottom: 8px; font-size: 14px; }
        .form-group label .required { color: #e74c3c; margin-left: 3px; }
        .form-group input, .form-group select, .form-group textarea { padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; transition: all 0.3s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group input[readonly] { background: #f8f9fa; cursor: not-allowed; }
        .form-actions { display: flex; gap: 15px; justify-content: flex-end; padding-top: 30px; border-top: 2px solid #e0e0e0; }
        .btn { padding: 14px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .event-id-badge { background: rgba(255, 255, 255, 0.2); padding: 5px 15px; border-radius: 20px; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
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

    <div class="form-content">
        <?php flash('event_message'); ?>

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
                            <label for="Type">Event Type <span class="required">*</span></label>
                            <?php $type = $event['Type'] ?? ''; ?>
                            <select id="Type" name="Type" required>
                                <option value="">Select Type</option>
                                <?php 
                                    $types = ['Training Camp','Workshop','Seminar','Competition','Tournament','Match','Trial','Meeting','Other'];
                                    foreach($types as $t): ?>
                                        <option value="<?= $t; ?>" <?= ($type === $t) ? 'selected' : ''; ?>><?= $t; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="Category">Category</label>
                            <?php $category = $event['Category'] ?? ''; ?>
                            <select id="Category" name="Category">
                                <option value="">Select Category</option>
                                <?php 
                                    $categories = ['junior','senior','youth','professional','recreational','academy'];
                                    foreach($categories as $c): ?>
                                        <option value="<?= $c; ?>" <?= ($category === $c) ? 'selected' : ''; ?>>
                                            <?= ucfirst($c); ?>
                                        </option>
                                <?php endforeach; ?>
                            </select>
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
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Event data not found.</div>
        <?php endif; ?>
    </div>
</div>

<script>
console.log('Edit Event Page Loaded');

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
console.log('Max Participants:', '<?= htmlspecialchars($event['MaxParticipants'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Registration Fee:', '<?= htmlspecialchars($event['RegistrationFee'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Primary Contact:', '<?= htmlspecialchars($event['PrimaryContact'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Contact Email:', '<?= htmlspecialchars($event['ContactEmail'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
console.log('Contact Phone:', '<?= htmlspecialchars($event['ContactPhone'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>');
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
        
        const startDateTime = new Date(startDateVal + ' ' + startTimeVal);
        const endDateTime = new Date(endDateVal + ' ' + endTimeVal);
        
        if (endDateTime <= startDateTime) {
            e.preventDefault();
            alert('⚠️ End date/time must be after start date/time');
            console.error('Date validation failed: End date must be after start date');
            return false;
        }
        
        console.log('Form validation passed');
    });
}
</script>
</body>
</html>
