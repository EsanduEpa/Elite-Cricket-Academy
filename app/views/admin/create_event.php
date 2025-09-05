<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Include Header -->
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<div class="admin-layout">
    <!-- Left Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-cricket-ball-bat"></i>
                <h3>Elite Cricket</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="admin-info">
                <div class="admin-name">Admin Panel</div>
                <div class="admin-role">System Administrator</div>
            </div>
            <button class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/events" class="nav-link active">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Events & Tournaments</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/users" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/coaches" class="nav-link">
                    <i class="fas fa-user-tie"></i>
                    <span>Coaches</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a></li>
                <li><a href="<?php echo URLROOT; ?>/admin/settings" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Create Event Header -->
        <div class="create-event-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-plus-circle"></i> Create New Event</h1>
                    <p>Add a new cricket academy event, tournament, or training session</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/events" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Events
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Event Form -->
        <div class="create-event-form">
            <form action="<?php echo URLROOT; ?>/admin/create_event" method="POST" id="createEventForm">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                        <p>Enter the fundamental details of your event</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="title">Event Title <span class="required">*</span></label>
                            <input type="text" id="title" name="title" required 
                                   placeholder="Enter event title (e.g., Junior Cricket Championship)">
                            <div class="form-help">Choose a clear, descriptive title for your event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_type">Event Type <span class="required">*</span></label>
                            <select id="event_type" name="event_type" required>
                                <option value="">Select Event Type</option>
                                <option value="tournament">🏆 Tournament</option>
                                <option value="training">🏋️ Training Session</option>
                                <option value="match">⚾ Match</option>
                                <option value="workshop">📚 Workshop</option>
                                <option value="camp">🏕️ Cricket Camp</option>
                                <option value="clinic">🩺 Skills Clinic</option>
                            </select>
                            <div class="form-help">Select the type of event you're organizing</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_date">Event Date <span class="required">*</span></label>
                            <input type="date" id="event_date" name="event_date" required>
                            <div class="form-help">Choose the date when the event will take place</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_time">Event Time</label>
                            <input type="time" id="event_time" name="event_time">
                            <div class="form-help">Specify the start time (optional)</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location <span class="required">*</span></label>
                            <input type="text" id="location" name="location" required 
                                   placeholder="e.g., Main Cricket Ground, Practice Nets">
                            <div class="form-help">Where will the event be held?</div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Event Description <span class="required">*</span></label>
                            <textarea id="description" name="description" rows="4" required 
                                      placeholder="Provide a detailed description of the event, including objectives, activities, and any special requirements..."></textarea>
                            <div class="form-help">Describe what participants can expect from this event</div>
                        </div>
                    </div>
                </div>

                <!-- Event Details Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-cogs"></i> Event Details</h3>
                        <p>Additional information and settings</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="duration">Duration (hours)</label>
                            <input type="number" id="duration" name="duration" min="0.5" max="24" step="0.5" 
                                   placeholder="e.g., 2.5">
                            <div class="form-help">Expected duration of the event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="max_participants">Max Participants</label>
                            <input type="number" id="max_participants" name="max_participants" min="1" 
                                   placeholder="e.g., 30">
                            <div class="form-help">Maximum number of participants allowed</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="age_group">Age Group</label>
                            <select id="age_group" name="age_group">
                                <option value="">Select Age Group</option>
                                <option value="under-12">Under 12</option>
                                <option value="under-14">Under 14</option>
                                <option value="under-16">Under 16</option>
                                <option value="under-18">Under 18</option>
                                <option value="under-21">Under 21</option>
                                <option value="senior">Senior (21+)</option>
                                <option value="all-ages">All Ages</option>
                            </select>
                            <div class="form-help">Target age group for this event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="skill_level">Skill Level</label>
                            <select id="skill_level" name="skill_level">
                                <option value="">Select Skill Level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="all-levels">All Levels</option>
                            </select>
                            <div class="form-help">Required skill level for participants</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="registration_fee">Registration Fee ($)</label>
                            <input type="number" id="registration_fee" name="registration_fee" min="0" step="0.01" 
                                   placeholder="0.00">
                            <div class="form-help">Fee to participate (0 for free events)</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person" 
                                   placeholder="e.g., Coach Smith">
                            <div class="form-help">Who should participants contact for questions?</div>
                        </div>
                    </div>
                </div>

                <!-- Requirements & Equipment Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-clipboard-list"></i> Requirements & Equipment</h3>
                        <p>What participants need to know and bring</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="requirements">Requirements</label>
                            <textarea id="requirements" name="requirements" rows="3" 
                                      placeholder="List any specific requirements, prerequisites, or conditions for participation..."></textarea>
                            <div class="form-help">Any special requirements or conditions</div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="equipment_needed">Equipment Needed</label>
                            <textarea id="equipment_needed" name="equipment_needed" rows="3" 
                                      placeholder="List equipment participants should bring (bat, pads, helmet, etc.)..."></textarea>
                            <div class="form-help">Equipment participants need to bring</div>
                        </div>
                    </div>
                </div>

                <!-- Status and Visibility -->
                <div class="form-section">
                    <div class="section-header">
                        <h3><i class="fas fa-eye"></i> Status & Visibility</h3>
                        <p>Control how and when this event appears</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="status">Event Status</label>
                            <select id="status" name="status">
                                <option value="upcoming">📅 Upcoming</option>
                                <option value="registration-open">✅ Registration Open</option>
                                <option value="registration-closed">🚫 Registration Closed</option>
                                <option value="cancelled">❌ Cancelled</option>
                                <option value="completed">✔️ Completed</option>
                            </select>
                            <div class="form-help">Current status of the event</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="visibility">Visibility</label>
                            <select id="visibility" name="visibility">
                                <option value="public">🌐 Public (Everyone can see)</option>
                                <option value="members-only">👥 Members Only</option>
                                <option value="private">🔒 Private (Invite Only)</option>
                                <option value="draft">📝 Draft (Not Published)</option>
                            </select>
                            <div class="form-help">Who can see this event</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-outline" id="saveDraftBtn">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Event
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/admin/events.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form functionality
    initializeCreateEventForm();
    
    // Initialize sidebar
    initializeSidebar();
});

function initializeCreateEventForm() {
    const form = document.getElementById('createEventForm');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    
    // Set minimum date to today
    const eventDateInput = document.getElementById('event_date');
    if (eventDateInput) {
        const today = new Date().toISOString().split('T')[0];
        eventDateInput.min = today;
    }
    
    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Event...';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Save as draft functionality
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            document.getElementById('status').value = 'draft';
            form.submit();
        });
    }
    
    // Auto-save functionality (optional)
    const formInputs = form.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Save to localStorage as draft
            saveFormDraft();
        });
    });
    
    // Load saved draft
    loadFormDraft();
}

function validateForm() {
    const requiredFields = ['title', 'event_type', 'event_date', 'location', 'description'];
    let isValid = true;
    let firstErrorField = null;
    
    // Clear previous error states
    document.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('error');
    });
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && (!field.value || field.value.trim() === '')) {
            isValid = false;
            const formGroup = field.closest('.form-group');
            if (formGroup) {
                formGroup.classList.add('error');
                if (!firstErrorField) {
                    firstErrorField = field;
                }
            }
        }
    });
    
    // Date validation
    const eventDate = document.getElementById('event_date');
    if (eventDate && eventDate.value) {
        const selectedDate = new Date(eventDate.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            isValid = false;
            const formGroup = eventDate.closest('.form-group');
            if (formGroup) {
                formGroup.classList.add('error');
                if (!firstErrorField) {
                    firstErrorField = eventDate;
                }
            }
            showNotification('Event date cannot be in the past', 'error');
        }
    }
    
    // Focus on first error field
    if (!isValid && firstErrorField) {
        firstErrorField.focus();
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        showNotification('Please fill in all required fields correctly', 'error');
    }
    
    return isValid;
}

function saveFormDraft() {
    const formData = new FormData(document.getElementById('createEventForm'));
    const draftData = {};
    
    for (let [key, value] of formData.entries()) {
        draftData[key] = value;
    }
    
    localStorage.setItem('eventFormDraft', JSON.stringify(draftData));
}

function loadFormDraft() {
    const savedDraft = localStorage.getItem('eventFormDraft');
    if (savedDraft) {
        try {
            const draftData = JSON.parse(savedDraft);
            
            Object.entries(draftData).forEach(([key, value]) => {
                const field = document.getElementById(key);
                if (field && value) {
                    field.value = value;
                }
            });
            
            // Show notification about loaded draft
            showNotification('Draft loaded from previous session', 'info');
        } catch (error) {
            console.error('Error loading draft:', error);
        }
    }
}

function clearFormDraft() {
    localStorage.removeItem('eventFormDraft');
}

// Clear draft when form is successfully submitted
window.addEventListener('beforeunload', function() {
    // Only clear if form was submitted successfully
    if (document.querySelector('.btn[disabled]')) {
        clearFormDraft();
    }
});
</script>

<style>
/* Create Event Form Specific Styles */
.create-event-header {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

.create-event-form {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid rgba(74, 144, 226, 0.2);
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-header {
    margin-bottom: 25px;
}

.section-header h3 {
    color: #4A90E2;
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.section-header p {
    color: #666;
    font-size: 1rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    color: #333;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.required {
    color: #FF6B6B;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid rgba(74, 144, 226, 0.2);
    border-radius: 12px;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #4A90E2;
    background: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.form-group.error input,
.form-group.error select,
.form-group.error textarea {
    border-color: #FF6B6B;
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
}

.form-help {
    font-size: 0.85rem;
    color: #666;
    margin-top: 5px;
    font-style: italic;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(74, 144, 226, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .create-event-form {
        padding: 25px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>

</body>
</html>
