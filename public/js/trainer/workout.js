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
function openAddModal() {
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

function editPlan(planId, workoutName, frequency, duration) {
    const modal = document.getElementById('workoutModal');
    const form = document.getElementById('workoutForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    
    // Fill form with existing data
    document.getElementById('planId').value = planId;
    document.getElementById('workoutname').value = workoutName;
    document.getElementById('frequency').value = frequency;
    document.getElementById('duration').value = duration;
    
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
        const duration = row.querySelector('.duration').textContent;
        const date = row.querySelector('.date').textContent;
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
                <button onclick="editPlan(${planId}, '${planName.replace(/'/g, "\\'")}', '${frequency}', '${duration.replace(' mins', '')}'); closeViewModal();" style="background: linear-gradient(135deg, #ff9f43, #ffb74d); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
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

// Form validation
document.getElementById('workoutForm').addEventListener('submit', function(e) {
    const workoutName = document.getElementById('workoutname').value.trim();
    const frequency = document.getElementById('frequency').value;
    const duration = document.getElementById('duration').value;
    
    if (!workoutName || !frequency || !duration) {
        e.preventDefault();
        showNotification('Please fill in all required fields', 'error');
        return false;
    }
    
    if (duration < 15 || duration > 180) {
        e.preventDefault();
        showNotification('Duration must be between 15 and 180 minutes', 'error');
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