function getAppBaseUrl() {
    if (typeof URLROOT === 'string' && URLROOT.trim() !== '') {
        return URLROOT.replace(/\/$/, '');
    }

    return `${window.location.origin}/Elite`;
}

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeFilters();

    window.onclick = function(event) {
        const workoutModal = document.getElementById('workoutModal');
        const deleteModal = document.getElementById('deleteModal');
        const viewModal = document.getElementById('viewModal');
        const assignModal = document.getElementById('assignModal');

        if (event.target === workoutModal) {
            closeModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
        if (event.target === viewModal) {
            closeViewModal();
        }
        if (event.target === assignModal) {
            closeAssignModal();
        }

        document.querySelectorAll('.assigned-players-modal').forEach(function(modal) {
            if (event.target === modal) {
                closeAssignedModal(modal.id);
            }
        });
    };

    updateCharCount('workoutname');
    updateCharCount('benefits');
});

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
}

function initializeFilters() {
    const frequencyFilter = document.getElementById('frequencyFilter');
    if (frequencyFilter) {
        frequencyFilter.addEventListener('change', filterTable);
    }
}

function filterTable() {
    const searchInput = document.getElementById('searchInput');
    const frequencyFilter = document.getElementById('frequencyFilter');
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const frequency = frequencyFilter ? frequencyFilter.value : '';

    document.querySelectorAll('.dashboard-table tbody tr').forEach(function(row) {
        const planNameElement = row.querySelector('.plan-name strong');
        if (!planNameElement) {
            return;
        }

        const planName = planNameElement.textContent.toLowerCase();
        const rowFrequency = (row.dataset.frequency || '').trim();
        const matchesSearch = planName.includes(searchTerm);
        const matchesFrequency = !frequency || rowFrequency === frequency;

        row.style.display = matchesSearch && matchesFrequency ? '' : 'none';
    });
}

function setWorkoutPlanButtonsBusy() {
    const buttons = document.querySelectorAll('.js-workout-plan-cta');

    buttons.forEach(function(button) {
        if (!button.dataset.originalHtml) {
            button.dataset.originalHtml = button.innerHTML;
        }

        button.classList.add('is-opening');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening...';
    });

    window.setTimeout(function() {
        buttons.forEach(function(button) {
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

    form.reset();
    document.getElementById('planId').value = '';
    document.getElementById('status').value = 'active';
    document.getElementById('intensity').value = 'Moderate';
    document.getElementById('notsuitablefor').value = 'None (General)';

    modalTitle.innerHTML = '<i class="fas fa-dumbbell"></i> Add New Workout Plan';
    submitBtn.innerHTML = '<i class="fas fa-save"></i>Save Plan';
    form.action = `${getAppBaseUrl()}/trainer/addWorkoutPlan`;

    updateCharCount('workoutname');
    updateCharCount('benefits');

    modal.style.display = 'block';
    setTimeout(function() {
        modal.classList.add('show');
    }, 10);
}

function editPlan(planId, planData) {
    const modal = document.getElementById('workoutModal');
    const form = document.getElementById('workoutForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');

    document.getElementById('planId').value = planId;
    document.getElementById('workoutname').value = planData.workoutname || '';
    document.getElementById('frequency').value = planData.frequency || '';
    document.getElementById('duration').value = planData.duration || '';
    document.getElementById('videolink').value = planData.videolink || '';
    document.getElementById('intensity').value = planData.intensity || 'Moderate';
    document.getElementById('notsuitablefor').value = planData.notsuitablefor || 'None (General)';
    document.getElementById('benefits').value = planData.benefits || '';
    document.getElementById('status').value = planData.status || 'active';

    modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Workout Plan';
    submitBtn.innerHTML = '<i class="fas fa-save"></i>Update Plan';
    form.action = `${getAppBaseUrl()}/trainer/updateWorkoutPlan`;

    updateCharCount('workoutname');
    updateCharCount('benefits');

    modal.style.display = 'block';
    setTimeout(function() {
        modal.classList.add('show');
    }, 10);
}

function openEditFromButton(button) {
    if (!button) {
        return;
    }

    const planId = parseInt(button.getAttribute('data-plan-id') || '0', 10);
    const planRaw = button.getAttribute('data-plan') || '{}';
    let planData = {};

    try {
        planData = JSON.parse(planRaw);
    } catch (error) {
        console.error('Failed to parse workout plan data:', error);
        return;
    }

    if (!planId) {
        return;
    }

    editPlan(planId, planData);
}

function closeModal() {
    const modal = document.getElementById('workoutModal');
    modal.classList.remove('show');
    setTimeout(function() {
        modal.style.display = 'none';
        document.getElementById('workoutForm').reset();
    }, 300);
}

function deletePlan(planId, planName) {
    const modal = document.getElementById('deleteModal');
    document.getElementById('deletePlanName').textContent = planName;
    document.getElementById('deletePlanId').value = planId;

    modal.style.display = 'block';
    setTimeout(function() {
        modal.classList.add('show');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    setTimeout(function() {
        modal.style.display = 'none';
    }, 300);
}

function viewPlan(planId) {
    const modal = document.getElementById('viewModal');
    const detailsContainer = document.getElementById('planDetails');
    const row = document.querySelector(`tr[data-plan-id="${planId}"]`);

    if (!row) {
        detailsContainer.innerHTML = '<p style="color:#c0392b;">Could not load plan details.</p>';
        modal.style.display = 'block';
        setTimeout(function() {
            modal.classList.add('show');
        }, 10);
        return;
    }

    const editButton = row.querySelector('.btn-edit');
    let planData = {};
    if (editButton) {
        try {
            planData = JSON.parse(editButton.getAttribute('data-plan') || '{}');
        } catch (error) {
            planData = {};
        }
    }

    const planName = row.querySelector('.plan-name strong')?.textContent || '';
    const status = row.querySelector('.status-badge')?.textContent.trim() || '-';
    const duration = row.querySelector('.duration')?.textContent.trim() || '-';
    const createdDate = row.dataset.created || '-';
    const assignedText = row.querySelector('.assigned-players-btn')?.textContent.replace(/\s+/g, ' ').trim() || '-';
    const trainer = row.querySelector('.workout-trainer-text')?.textContent.trim() || '-';

    detailsContainer.innerHTML = `
        <div style="display:grid;gap:16px;">
            <div style="padding:15px;background:#f8fafc;border-radius:8px;border-left:4px solid #4A90E2;">
                <strong style="display:block;color:#374151;margin-bottom:6px;">Workout Name</strong>
                <span>${escapeHtml(planName)}</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Trainer</strong><span>${escapeHtml(trainer)}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Status</strong><span>${escapeHtml(status)}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Frequency</strong><span>${escapeHtml(planData.frequency || row.dataset.frequency || '-')}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Duration</strong><span>${escapeHtml(duration)}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Intensity</strong><span>${escapeHtml(planData.intensity || '-')}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Created</strong><span>${escapeHtml(createdDate)}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Assigned Players</strong><span>${escapeHtml(assignedText)}</span></div>
                <div style="padding:15px;background:#f8fafc;border-radius:8px;"><strong style="display:block;color:#374151;margin-bottom:6px;">Video Link</strong><span>${planData.videolink ? `<a href="${escapeAttribute(planData.videolink)}" target="_blank" rel="noopener noreferrer">${escapeHtml(planData.videolink)}</a>` : '-'}</span></div>
            </div>
            <div style="padding:15px;background:#f8fafc;border-radius:8px;">
                <strong style="display:block;color:#374151;margin-bottom:6px;">Not Suitable For</strong>
                <span>${escapeHtml(planData.notsuitablefor || 'None (General)')}</span>
            </div>
            <div style="padding:15px;background:#f8fafc;border-radius:8px;">
                <strong style="display:block;color:#374151;margin-bottom:6px;">Key Benefits</strong>
                <span>${escapeHtml(planData.benefits || '-')}</span>
            </div>
        </div>
    `;

    modal.style.display = 'block';
    setTimeout(function() {
        modal.classList.add('show');
    }, 10);
}

function closeViewModal() {
    const modal = document.getElementById('viewModal');
    modal.classList.remove('show');
    setTimeout(function() {
        modal.style.display = 'none';
    }, 300);
}

function openAssignModal(planId, planName) {
    document.getElementById('assignPlanId').value = planId;
    document.getElementById('assignModalSubtitle').textContent = `Plan: ${planName}`;
    document.getElementById('assignPlayerId').value = '';
    document.getElementById('assignEndDate').value = '';
    document.getElementById('assignModal').style.display = 'flex';
}

function closeAssignModal() {
    const modal = document.getElementById('assignModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function openAssignedModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeAssignedModal(modalId) {
    if (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
        return;
    }

    document.querySelectorAll('.assigned-players-modal').forEach(function(modal) {
        modal.style.display = 'none';
    });
}

function updateCharCount(fieldId) {
    const field = document.getElementById(fieldId);
    const counter = document.getElementById(`${fieldId}-counter`);

    if (!field || !counter) {
        return;
    }

    const length = field.value.length;
    if (fieldId === 'workoutname') {
        counter.textContent = String(length);
    } else {
        counter.textContent = `${length}/1000 characters`;
    }
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeAttribute(value) {
    return escapeHtml(value);
}
