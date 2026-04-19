// Page-specific staff management behaviour.
// This file keeps staff modals, coach assignment helpers, and AJAX actions out
// of the PHP view so the viva explanation is cleaner.

document.addEventListener('DOMContentLoaded', function() {
    const urlRoot = document.querySelector('.admin-layout')?.dataset.urlroot || '/Elite';
    const coachAssignedPlayers = readJsonScript('coachAssignedPlayersData', {});
    const coachAssignmentsModal = document.getElementById('coachAssignmentsModal');
    const coachPlayersModal = document.getElementById('coachPlayersModal');
    const coachSelect = document.getElementById('assignmentCoachId');
    const skillSelect = document.getElementById('assignmentCoachingType');

    function showModal(modal) {
        if (!modal) return;
        modal.style.display = 'flex';
        modal.classList.add('active');
    }

    function hideModal(modal) {
        if (!modal) return;
        modal.classList.remove('active');
        modal.style.display = 'none';
    }

    function syncCoachSkillSelection(preferredSkill = '') {
        if (!coachSelect || !skillSelect) return;
        const selectedOption = coachSelect.options[coachSelect.selectedIndex];
        const mappedSkill = preferredSkill || selectedOption?.dataset.skill || '';

        if (mappedSkill) {
            skillSelect.value = mappedSkill;
        }
    }

    function openCoachPlayersModal(coachId, coachName) {
        const title = document.getElementById('coachPlayersModalTitle');
        const tbody = document.getElementById('coachPlayersTableBody');
        if (!coachPlayersModal || !title || !tbody) return;

        const players = coachAssignedPlayers[String(coachId)] || coachAssignedPlayers[coachId] || [];
        title.textContent = `${coachName} - Assigned Players`;

        if (!players.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align:center; padding:24px; color:#64748b;">
                        No players are currently assigned to this coach.
                    </td>
                </tr>`;
        } else {
            tbody.innerHTML = players.map(player => `
                <tr>
                    <td>${escapeHtml(player.PlayerName || '-')}</td>
                    <td><span class="role-badge">${escapeHtml(player.AssignmentType || '-')}</span></td>
                    <td>${escapeHtml(player.BattingStyle || '-')}</td>
                    <td>${escapeHtml(player.BowlingStyle || '-')}</td>
                    <td>${escapeHtml(player.TournamentCount ?? 0)}</td>
                    <td><span class="status-badge ${escapeHtml(String(player.AssignmentStatus || 'active').toLowerCase())}">${escapeHtml(player.AssignmentStatus || 'active')}</span></td>
                </tr>`).join('');
        }

        showModal(coachPlayersModal);
    }

    function openViewModal(staffData) {
        setText('viewStaffName', staffData.name);
        setText('viewFullName', staffData.name);
        setText('viewStaffRoleBadge', staffData.role);
        document.getElementById('viewStaffRoleBadge')?.setAttribute('class', 'staff-role-badge role-badge ' + String(staffData.role || '').toLowerCase());
        setText('viewStaffStatusBadge', staffData.status);
        document.getElementById('viewStaffStatusBadge')?.setAttribute('class', 'staff-status status-badge ' + String(staffData.status || '').toLowerCase());
        setText('viewEmail', staffData.email);
        setText('viewPhone', staffData.phone);
        setText('viewJoined', staffData.joined);
        setText('viewRole', staffData.role);
        setText('viewStatus', formatLabel(staffData.status));

        const editFromViewBtn = document.getElementById('editFromViewBtn');
        if (editFromViewBtn) {
            editFromViewBtn.dataset.staffId = staffData.id;
            editFromViewBtn.dataset.staffData = JSON.stringify(staffData);
        }

        showModal(document.getElementById('viewStaffModal'));
    }

    function openEditModal(staffData) {
        const nameParts = String(staffData.name || '').split(' ');
        setValue('editStaffId', staffData.id);
        setValue('editFirstName', nameParts[0] || '');
        setValue('editLastName', nameParts.slice(1).join(' '));
        setValue('editEmail', staffData.email);
        setValue('editPhone', staffData.phone);
        setValue('editRole', String(staffData.role || '').toLowerCase().replace(/\s+/g, '_'));
        setValue('editStatus', String(staffData.status || '').toLowerCase());
        showModal(document.getElementById('editStaffModal'));
    }

    document.getElementById('manageCoachAssignmentsBtn')?.addEventListener('click', () => showModal(coachAssignmentsModal));
    document.getElementById('closeCoachAssignmentsModal')?.addEventListener('click', () => hideModal(coachAssignmentsModal));
    document.getElementById('closeCoachPlayersModal')?.addEventListener('click', () => hideModal(coachPlayersModal));
    coachSelect?.addEventListener('change', () => syncCoachSkillSelection());
    coachAssignmentsModal?.querySelector('.modal-overlay')?.addEventListener('click', () => hideModal(coachAssignmentsModal));
    coachPlayersModal?.querySelector('.modal-overlay')?.addEventListener('click', () => hideModal(coachPlayersModal));

    document.querySelectorAll('.coach-assignment-edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            showModal(coachAssignmentsModal);
            const ageGroups = (this.dataset.ageGroups || '').split(',').map(value => value.trim()).filter(Boolean);

            if (coachSelect) coachSelect.value = this.dataset.coachId || '';
            syncCoachSkillSelection(this.dataset.coachingType || '');

            document.querySelectorAll('.coach-age-group-checkbox').forEach(checkbox => {
                checkbox.checked = ageGroups.includes(checkbox.value);
            });
        });
    });

    document.querySelectorAll('.coach-players-view-btn').forEach(button => {
        button.addEventListener('click', function() {
            openCoachPlayersModal(this.dataset.coachId || '', this.dataset.coachName || 'Coach');
        });
    });

    if (window.location.hash === '#coachAssignmentsModal' || new URLSearchParams(window.location.search).get('open') === 'coach-assignments') {
        showModal(coachAssignmentsModal);
    }

    document.addEventListener('click', function(event) {
        const target = event.target.closest('.action-btn');
        if (!target || !target.dataset.staffId) return;

        const staffId = target.dataset.staffId;
        const staffName = target.dataset.staffName;
        const staffData = {
            id: staffId,
            name: staffName,
            role: target.dataset.staffRole,
            email: target.dataset.staffEmail,
            phone: target.dataset.staffPhone,
            joined: target.dataset.staffJoined,
            status: target.dataset.staffStatus
        };

        if (target.classList.contains('view')) {
            openViewModal(staffData);
        } else if (target.classList.contains('edit')) {
            openEditModal(staffData);
        } else if (target.classList.contains('delete')) {
            deleteStaff(staffId, staffName);
        }
    });

    document.getElementById('editFromViewBtn')?.addEventListener('click', function() {
        const staffData = JSON.parse(this.dataset.staffData || '{}');
        hideModal(document.getElementById('viewStaffModal'));
        openEditModal(staffData);
    });

    document.getElementById('closeViewModal')?.addEventListener('click', () => hideModal(document.getElementById('viewStaffModal')));
    document.getElementById('viewModalOverlay')?.addEventListener('click', () => hideModal(document.getElementById('viewStaffModal')));
    document.getElementById('closeEditModal')?.addEventListener('click', () => hideModal(document.getElementById('editStaffModal')));
    document.getElementById('editModalOverlay')?.addEventListener('click', () => hideModal(document.getElementById('editStaffModal')));
    document.getElementById('cancelEditBtn')?.addEventListener('click', () => hideModal(document.getElementById('editStaffModal')));

    document.getElementById('editStaffForm')?.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const data = {
            staffId: document.getElementById('editStaffId')?.value,
            firstName: formData.get('firstName'),
            lastName: formData.get('lastName'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            role: formData.get('role'),
            status: formData.get('status'),
            specialization: formData.get('specialization'),
            address: formData.get('address')
        };

        fetch(`${urlRoot}/admin/update_staff`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Staff member updated successfully!');
                    location.reload();
                    return;
                }

                alert('Error: ' + (result.message || 'Failed to update staff member'));
            })
            .catch(error => {
                console.error('Staff update request failed:', error);
                alert('An error occurred. Please try again.');
            });
    });

    function deleteStaff(staffId, staffName) {
        if (!confirm(`Are you sure you want to delete ${staffName}?\n\nThis action cannot be undone.`)) {
            return;
        }

        fetch(`${urlRoot}/admin/delete_staff/${staffId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Staff member deleted successfully!');
                    location.reload();
                    return;
                }

                alert('Error: ' + (result.message || 'Failed to delete staff member'));
            })
            .catch(error => {
                console.error('Staff delete request failed:', error);
                alert('An error occurred. Please try again.');
            });
    }
});

function readJsonScript(id, fallback) {
    const node = document.getElementById(id);
    if (!node) return fallback;

    try {
        return JSON.parse(node.textContent || JSON.stringify(fallback));
    } catch (error) {
        console.error(`Unable to parse ${id}:`, error);
        return fallback;
    }
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function setText(id, value) {
    const element = document.getElementById(id);
    if (element) element.textContent = value ?? '';
}

function setValue(id, value) {
    const element = document.getElementById(id);
    if (element) element.value = value ?? '';
}

function formatLabel(value) {
    return String(value || '-')
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, letter => letter.toUpperCase());
}
