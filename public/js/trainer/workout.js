// Workout Plans Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize filter functionality
    initializeFilters();
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        const workoutModal = document.getElementById('workoutModal');
        const deleteModal = document.getElementById('deleteModal');
        const viewModal = document.getElementById('viewModal');
        
        if (event.target === workoutModal) {
            closeModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
        if (event.target === viewModal) {
            closeViewModal();
        }
    };
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTable();
        });
    }
}

// Filter functionality
function initializeFilters() {
    const frequencyFilter = document.getElementById('frequencyFilter');
    if (frequencyFilter) {
        frequencyFilter.addEventListener('change', function() {
            filterTable();
        });
    }
}

// Filter table based on search and filters
function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const frequencyFilter = document.getElementById('frequencyFilter').value;
    const tableRows = document.querySelectorAll('.workout-table tbody tr');
    
    tableRows.forEach(row => {
        if (row.querySelector('.no-data-message')) {
            return; // Skip the no-data row
        }
        
        const planName = row.querySelector('.plan-name strong').textContent.toLowerCase();
        const frequency = row.querySelector('.frequency-badge').textContent.trim();
        
        const matchesSearch = planName.includes(searchTerm);
        const matchesFrequency = !frequencyFilter || frequency === frequencyFilter;
        
        if (matchesSearch && matchesFrequency) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Modal Management
function setWorkoutPlanButtonsBusy() {
    const buttons = document.querySelectorAll('.js-workout-plan-cta');

    buttons.forEach((button) => {
        if (!button.dataset.originalHtml) {
            button.dataset.originalHtml = button.innerHTML;
        }

        button.classList.add('is-opening');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening...';
    });

    window.setTimeout(() => {
        buttons.forEach((button) => {
            button.classList.remove('is-opening');
            button.disabled = false;
            if (button.dataset.originalHtml) {
                button.innerHTML = button.dataset.originalHtml;
            }
        });
    }, 420);
}

function openAddModal() {
    setWorkoutPlanButtonsBusy();
    const modal = document.getElementById('workoutModal');
    const form = document.getElementById('workoutForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    
    // Reset form
    form.reset();
    document.getElementById('planId').value = '';
    
    // Set modal for adding
    modalTitle.innerHTML = '<i class="fas fa-dumbbell"></i> Add New Workout Plan';
    submitBtn.innerHTML = '<i class="fas fa-save"></i>Save Plan';
    form.action = `${window.location.origin}/Elite/trainer/addWorkoutPlan`;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
}

function editPlan(planId, planData) {
    const modal = document.getElementById('workoutModal');
    const form = document.getElementById('workoutForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    
    // Fill form with existing data
    document.getElementById('planId').value = planId;
    document.getElementById('workoutname').value = planData.workoutname || '';
    document.getElementById('frequency').value = planData.frequency || '';
    document.getElementById('duration').value = planData.duration || '';
    document.getElementById('videolink').value = planData.videolink || '';
    document.getElementById('intensity').value = planData.intensity || 'Moderate';
    document.getElementById('notsuitablefor').value = planData.notsuitablefor || '';
    document.getElementById('benefits').value = planData.benefits || '';
    
    // Update character counters after filling the form
    setTimeout(() => {
        updateCharCount('workoutname');
        updateCharCount('benefits');
        updateCharCount('notsuitablefor');
    }, 100);
    
    // Set modal for editing
    modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Workout Plan';
    submitBtn.innerHTML = '<i class="fas fa-save"></i>Update Plan';
    form.action = `${window.location.origin}/Elite/trainer/updateWorkoutPlan`;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeModal() {
    const modal = document.getElementById('workoutModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        document.getElementById('workoutForm').reset();
    }, 300);
}

// Delete Modal Management
function deletePlan(planId, planName) {
    const modal = document.getElementById('deleteModal');
    const planNameSpan = document.getElementById('deletePlanName');
    const planIdInput = document.getElementById('deletePlanId');
    
    planNameSpan.textContent = planName;
    planIdInput.value = planId;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// View Modal Management
function viewPlan(planId) {
    const modal = document.getElementById('viewModal');
    const detailsContainer = document.getElementById('planDetails');
    
    // Show loading state
    detailsContainer.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading plan details...</p>
        </div>
    `;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Find the plan data from the table row
    const row = document.querySelector(`tr[data-plan-id="${planId}"]`);
    if (row) {
        const planName = row.querySelector('.plan-name strong').textContent;
        const frequency = row.querySelector('.frequency-badge').textContent.trim();
        // Safely read duration and created date by class selectors added to server-rendered rows
        const durationEl = row.querySelector('.duration');
        const dateEl = row.querySelector('.table-cell-secondary') || row.querySelector('.date');
        const duration = durationEl ? durationEl.textContent.trim() : '';
        const date = dateEl ? dateEl.textContent.trim() : '';
        const planIdFormatted = row.querySelector('.plan-id').textContent;
        
        // Display plan details
        detailsContainer.innerHTML = `
            <div style="display: grid; gap: 20px; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                    <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-hashtag" style="color: #4A90E2;"></i>Plan ID:
                    </label>
                    <span style="font-family: 'Courier New', monospace; background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                        ${planIdFormatted}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                    <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-dumbbell" style="color: #4A90E2;"></i>Workout Name:
                    </label>
                    <span style="font-weight: 600; color: #1f2937; font-size: 16px;">${planName}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                    <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-calendar-alt" style="color: #4A90E2;"></i>Frequency:
                    </label>
                    <span class="table-badge ${frequency === 'Daily' ? 'status-active' : frequency === 'Weekly' ? 'status-upcoming' : ''}" style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; ${frequency === 'Daily' ? 'background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);' : frequency === 'Weekly' ? 'background: rgba(255, 159, 67, 0.1); color: #ff9f43; border: 1px solid rgba(255, 159, 67, 0.3);' : 'background: rgba(74, 144, 226, 0.1); color: #4A90E2; border: 1px solid rgba(74, 144, 226, 0.3);'}">${frequency}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                    <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-clock" style="color: #4A90E2;"></i>Duration:
                    </label>
                    <span style="font-weight: 600; color: #7c3aed;">${duration}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                    <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-calendar-plus" style="color: #4A90E2;"></i>Created Date:
                    </label>
                    <span style="color: #6b7280; font-size: 14px;">${date}</span>
                </div>
            </div>
            <div style="display: flex; gap: 12px; justify-content: center; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button onclick="prepareEditFromView(${planId}); closeViewModal();" style="background: linear-gradient(135deg, #ff9f43, #ffb74d); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i>Edit Plan
                </button>
                <button onclick="deletePlan(${planId}, '${planName.replace(/'/g, "\\'")}'); closeViewModal();" style="background: linear-gradient(135deg, #ff6b6b, #ff8e8e); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-trash"></i>Delete Plan
                </button>
            </div>
        `;
    }
}

function closeViewModal() {
    const modal = document.getElementById('viewModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Prepare and open the edit modal using data from the table row (used by view modal Edit button)
function prepareEditFromView(planId) {
    const row = document.querySelector(`tr[data-plan-id="${planId}"]`);
    if (!row) return;

    const planName = row.querySelector('.plan-name strong') ? row.querySelector('.plan-name strong').textContent.trim() : '';
    const frequency = row.querySelector('.frequency-badge') ? row.querySelector('.frequency-badge').textContent.trim() : '';
    const durationText = row.querySelector('.duration') ? row.querySelector('.duration').textContent.trim() : '';
    const durationNumeric = parseInt(durationText.replace(/[^0-9]/g, ''), 10) || '';
    const durationDaysText = row.querySelector('.durationdays') ? row.querySelector('.durationdays').textContent.trim() : '';
    const durationDaysNumeric = parseInt(durationDaysText.replace(/[^0-9]/g, ''), 10) || '';
    const videolink = row.querySelector('a') ? row.querySelector('a').getAttribute('href') : '';

    // Construct a planData object similar to what editPlan expects
    const planData = {
        workoutname: planName,
        frequency: frequency,
        duration: durationNumeric,
        durationdays: durationDaysNumeric,
        videolink: videolink,
        intensity: row.querySelector('.intensity-badge') ? row.querySelector('.intensity-badge').textContent.trim() : 'Moderate',
        notsuitablefor: row.querySelector('.notsuitablefor') ? row.querySelector('.notsuitablefor').textContent.trim() : '',
        benefits: row.querySelector('.benefits') ? row.querySelector('.benefits').textContent.trim() : ''
    };

    editPlan(planId, planData);
}

// Character counter function
function updateCharCount(fieldId) {
    const field = document.getElementById(fieldId);
    const counter = document.getElementById(`${fieldId}-counter`);
    
    if (field && counter) {
        const currentLength = field.value.length;
        const maxLength = field.maxLength || 255;
        
        counter.textContent = `${currentLength}/${maxLength} characters`;
        
        // Color coding for character counter
        if (currentLength > maxLength * 0.9) {
            counter.style.color = '#ff6b6b'; // Red when close to limit
        } else if (currentLength > maxLength * 0.7) {
            counter.style.color = '#ff9f43'; // Orange when approaching limit
        } else {
            counter.style.color = '#666'; // Default gray
        }
    }
}

// Real-time field validation
function validateField(field) {
    const fieldId = field.id;
    const value = field.value.trim();
    
    // Remove existing validation classes
    field.style.borderColor = '#ddd';
    
    switch(fieldId) {
        case 'workoutname':
            if (value.length >= 3 && value.length <= 255) {
                field.style.borderColor = '#2ed573'; // Green for valid
            } else if (value.length > 0) {
                field.style.borderColor = '#ff6b6b'; // Red for invalid
            }
            break;
            
        case 'duration':
            const duration = parseInt(value);
            if (duration >= 15 && duration <= 180) {
                field.style.borderColor = '#2ed573';
            } else if (value.length > 0) {
                field.style.borderColor = '#ff6b6b';
            }
            break;
            
        case 'durationdays':
            if (value === '' || (parseInt(value) >= 1 && parseInt(value) <= 365)) {
                field.style.borderColor = value === '' ? '#ddd' : '#2ed573';
            } else {
                field.style.borderColor = '#ff6b6b';
            }
            break;
            
        case 'videolink':
            if (value === '' || value.match(/^https?:\/\/.+/)) {
                field.style.borderColor = value === '' ? '#ddd' : '#2ed573';
            } else {
                field.style.borderColor = '#ff6b6b';
            }
            break;
            
        case 'benefits':
        case 'notsuitablefor':
            if (value.length <= 1000) {
                field.style.borderColor = value.length > 0 ? '#2ed573' : '#ddd';
            } else {
                field.style.borderColor = '#ff6b6b';
            }
            break;
    }
}

// Initialize character counters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update counters for all text fields
    updateCharCount('workoutname');
    updateCharCount('benefits');
    updateCharCount('notsuitablefor');
    
    // Add input event listeners for character counting
    const workoutname = document.getElementById('workoutname');
    const benefits = document.getElementById('benefits');
    const notsuitablefor = document.getElementById('notsuitablefor');
    
    if (workoutname) {
        workoutname.addEventListener('input', () => {
            updateCharCount('workoutname');
            validateField(workoutname);
        });
        workoutname.addEventListener('blur', () => validateField(workoutname));
    }
    if (benefits) {
        benefits.addEventListener('input', () => {
            updateCharCount('benefits');
            validateField(benefits);
        });
        benefits.addEventListener('blur', () => validateField(benefits));
    }
    if (notsuitablefor) {
        notsuitablefor.addEventListener('input', () => {
            updateCharCount('notsuitablefor');
            validateField(notsuitablefor);
        });
        notsuitablefor.addEventListener('blur', () => validateField(notsuitablefor));
    }
    
    // Add validation for other fields
    const duration = document.getElementById('duration');
    const durationDays = document.getElementById('durationdays');
    const videoLink = document.getElementById('videolink');
    
    if (duration) {
        duration.addEventListener('input', () => validateField(duration));
        duration.addEventListener('blur', () => validateField(duration));
    }
    if (durationDays) {
        durationDays.addEventListener('input', () => validateField(durationDays));
        durationDays.addEventListener('blur', () => validateField(durationDays));
    }
    if (videoLink) {
        videoLink.addEventListener('input', () => validateField(videoLink));
        videoLink.addEventListener('blur', () => validateField(videoLink));
    }
});

// Form validation
document.getElementById('workoutForm').addEventListener('submit', function(e) {
    const workoutName = document.getElementById('workoutname').value.trim();
    const frequency = document.getElementById('frequency').value;
    const duration = parseInt(document.getElementById('duration').value);
    const durationDays = document.getElementById('durationdays').value;
    const videoLink = document.getElementById('videolink').value.trim();
    const benefits = document.getElementById('benefits').value.trim();
    const notSuitableFor = document.getElementById('notsuitablefor').value.trim();
    
    // Required fields validation
    if (!workoutName || !frequency || !duration) {
        e.preventDefault();
        showNotification('Please fill in all required fields', 'error');
        return false;
    }
    
    // Workout name validation
    if (workoutName.length < 3 || workoutName.length > 255) {
        e.preventDefault();
        showNotification('Workout name must be between 3 and 255 characters', 'error');
        return false;
    }
    
    // Duration validation (minutes)
    if (isNaN(duration) || duration < 15 || duration > 180) {
        e.preventDefault();
        showNotification('Duration must be between 15 and 180 minutes', 'error');
        return false;
    }
    
    // Duration days validation (optional)
    if (durationDays && (parseInt(durationDays) < 1 || parseInt(durationDays) > 365)) {
        e.preventDefault();
        showNotification('Duration days must be between 1 and 365', 'error');
        return false;
    }
    
    // Video link validation (optional but must be valid URL if provided)
    if (videoLink && !videoLink.match(/^https?:\/\/.+/)) {
        e.preventDefault();
        showNotification('Video link must be a valid URL starting with http:// or https://', 'error');
        return false;
    }
    
    // Benefits validation (optional but max 1000 chars)
    if (benefits.length > 1000) {
        e.preventDefault();
        showNotification('Benefits must not exceed 1000 characters', 'error');
        return false;
    }
    
    // Not suitable for validation (optional but max 1000 chars)
    if (notSuitableFor.length > 1000) {
        e.preventDefault();
        showNotification('Not Suitable For must not exceed 1000 characters', 'error');
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Saving...';
    submitBtn.disabled = true;
    
    // Re-enable button after a delay (in case of form errors)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Notification system
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key to close modals
    if (e.key === 'Escape') {
        closeModal();
        closeDeleteModal();
        closeViewModal();
    }
    
    // Ctrl/Cmd + N to add new plan
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        openAddModal();
    }
});

// Enhance table interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.workout-table tbody tr');
    tableRows.forEach(row => {
        if (!row.querySelector('.no-data-message')) {
            row.addEventListener('click', function(e) {
                // Don't trigger on button clicks
                if (!e.target.closest('.btn-action')) {
                    const planId = this.dataset.planId;
                    if (planId) {
                        viewPlan(planId);
                    }
                }
            });
            
            // Add cursor pointer style
            row.style.cursor = 'pointer';
        }
    });
});

/* ---- Assign to Player ---- */
function openAssignModal(planId, planName) {
    document.getElementById('assignPlanId').value = planId;
    document.getElementById('assignModalSubtitle').textContent = 'Plan: ' + planName;
    document.getElementById('assignPlayerId').value = '';
    document.getElementById('assignEndDate').value  = '';
    document.getElementById('assignFeedback').style.display = 'none';
    document.getElementById('assignModal').style.display = 'flex';
}
function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn  = document.getElementById('assignSubmitBtn');
    const fb   = document.getElementById('assignFeedback');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
    fb.style.display = 'none';

    const fd = new FormData(this);
    fetch(URLROOT + '/trainer/assignPlanToPlayer', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            fb.style.display = 'block';
            if (data.success) {
                fb.style.cssText = 'display:block;padding:10px 15px;border-radius:8px;margin-bottom:15px;font-size:13px;background:rgba(46,213,115,0.1);color:#1a7a3a;border:1px solid rgba(46,213,115,0.4);';
                fb.textContent = data.message;
                setTimeout(function() { closeAssignModal(); location.reload(); }, 1400);
            } else {
                fb.style.cssText = 'display:block;padding:10px 15px;border-radius:8px;margin-bottom:15px;font-size:13px;background:rgba(255,59,48,0.1);color:#c0392b;border:1px solid rgba(255,59,48,0.4);';
                fb.textContent = data.message;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Assign';
            }
        })
        .catch(function() {
            fb.style.cssText = 'display:block;padding:10px 15px;border-radius:8px;margin-bottom:15px;font-size:13px;background:rgba(255,59,48,0.1);color:#c0392b;border:1px solid rgba(255,59,48,0.4);';
            fb.textContent = 'Network error. Please try again.';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Assign';
        });
});

/* ---- View Assigned Players ---- */
function viewAssignedPlayers(planId, planName) {
    document.getElementById('assignedModalTitle').innerHTML = '<i class="fas fa-users" style="color:#4A90E2;"></i> Players — ' + planName;
    document.getElementById('assignedPlayersList').innerHTML = '<p style="color:#666;text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';
    document.getElementById('assignedPlayersModal').style.display = 'flex';

    fetch(URLROOT + '/trainer/getAssignedPlayers?plan_id=' + planId)
        .then(r => r.json())
        .then(function(resp) {
            if (!resp.success || !resp.players.length) {
                document.getElementById('assignedPlayersList').innerHTML =
                    '<p style="color:#999;text-align:center;padding:30px;"><i class="fas fa-users-slash" style="font-size:2rem;display:block;margin-bottom:10px;"></i>No players assigned yet.</p>';
                return;
            }
            const statusColors = { active:'#2ed573', completed:'#4A90E2', paused:'#ff9f43' };
            let html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                + '<thead><tr style="background:#f8fafc;">'
                + '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;">Player</th>'
                + '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;">Assigned</th>'
                + '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;">End Date</th>'
                + '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;">Status</th>'
                + '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;">Assigned By</th>'
                + '<th style="padding:8px 12px;text-align:center;font-weight:600;color:#374151;">Actions</th>'
                + '</tr></thead><tbody>';

            resp.players.forEach(function(p) {
                const sc = statusColors[p.assignment_status] || '#666';
                html += '<tr style="border-bottom:1px solid #f0f0f0;">'
                    + '<td style="padding:10px 12px;"><strong>' + p.player_name + '</strong><br><span style="color:#999;font-size:11px;">' + (p.player_email || '') + '</span></td>'
                    + '<td style="padding:10px 12px;">' + (p.AssignedDate || '-') + '</td>'
                    + '<td style="padding:10px 12px;">' + (p.EndDate || '<span style="color:#999;">open</span>') + '</td>'
                    + '<td style="padding:10px 12px;"><span style="color:' + sc + ';font-weight:600;text-transform:capitalize;">' + (p.assignment_status || '-') + '</span></td>'
                    + '<td style="padding:10px 12px;">' + (p.assigned_by_name || '<span style="color:#999;">-</span>') + '</td>'
                    + '<td style="padding:10px 12px;text-align:center;">';

                if (p.can_manage) {
                    html += '<select onchange="updateAssignStatus(' + planId + ',' + p.PlayerID + ',this.value)" style="padding:4px 8px;border:1px solid #ddd;border-radius:6px;font-size:12px;margin-right:5px;">'
                        + ['active','paused','completed'].map(function(s) {
                            return '<option value="' + s + '"' + (s === p.assignment_status ? ' selected' : '') + '>' + s.charAt(0).toUpperCase() + s.slice(1) + '</option>';
                        }).join('')
                        + '</select>'
                        + '<button onclick="unassignPlayer(' + planId + ',' + p.PlayerID + ')" style="padding:4px 10px;border:none;border-radius:6px;background:rgba(255,59,48,0.1);color:#ff3b30;cursor:pointer;font-size:12px;" title="Remove assignment"><i class="fas fa-times"></i></button>';
                }
                html += '</td></tr>';
            });
            html += '</tbody></table>';
            document.getElementById('assignedPlayersList').innerHTML = html;
        })
        .catch(function() {
            document.getElementById('assignedPlayersList').innerHTML = '<p style="color:#c0392b;text-align:center;padding:20px;">Failed to load players.</p>';
        });
}
function closeAssignedModal() {
    document.getElementById('assignedPlayersModal').style.display = 'none';
}

function updateAssignStatus(planId, playerId, status) {
    const fd = new FormData();
    fd.append('plan_id', planId);
    fd.append('player_id', playerId);
    fd.append('status', status);
    fetch(URLROOT + '/trainer/updateAssignmentStatus', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function(d) { if (!d.success) alert(d.message); });
}

function unassignPlayer(planId, playerId) {
    if (!confirm('Remove this player\'s assignment? This cannot be undone.')) return;
    const fd = new FormData();
    fd.append('plan_id', planId);
    fd.append('player_id', playerId);
    fetch(URLROOT + '/trainer/unassignPlanFromPlayer', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function(d) {
            if (d.success) { location.reload(); }
            else { alert(d.message); }
        });
}

/* ---- Close modals on backdrop click ---- */
document.getElementById('assignModal').addEventListener('click', function(e) { if (e.target === this) closeAssignModal(); });
document.getElementById('assignedPlayersModal').addEventListener('click', function(e) { if (e.target === this) closeAssignedModal(); });