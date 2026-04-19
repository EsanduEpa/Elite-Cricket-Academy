// Page-specific player management behaviour.
// The controller/view provide row data through data attributes; this script opens
// modals and posts add/edit/delete requests to the existing admin endpoints.

document.addEventListener('DOMContentLoaded', function() {
    const urlRoot = document.querySelector('.admin-layout')?.dataset.urlroot || '/Elite';
    const addPlayerBtn = document.getElementById('addPlayerBtn');
    const addPlayerModal = document.getElementById('addPlayerModal');
    let currentPlayerStep = 1;

    function showModal(modal) {
        if (!modal) return;
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);
    }

    function hideModal(modal) {
        if (!modal) return;
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function goToPlayerWizardStep(step) {
        currentPlayerStep = step;

        document.querySelectorAll('#addPlayerModal .wizard-step-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelector(`#addPlayerModal .wizard-step-content[data-step="${step}"]`)?.classList.add('active');

        document.querySelectorAll('#addPlayerModal .wizard-step').forEach(stepEl => {
            const stepNum = parseInt(stepEl.dataset.step, 10);
            stepEl.classList.toggle('completed', stepNum < step);
            stepEl.classList.toggle('active', stepNum === step);
        });

        const prevBtn = document.getElementById('playerWizardPrevBtn');
        const nextBtn = document.getElementById('playerWizardNextBtn');
        const submitBtn = document.getElementById('playerWizardSubmitBtn');

        if (prevBtn) prevBtn.style.display = step === 1 ? 'none' : 'inline-flex';
        if (nextBtn) nextBtn.style.display = step === 1 ? 'inline-flex' : 'none';
        if (submitBtn) submitBtn.style.display = step === 2 ? 'inline-flex' : 'none';

        if (step === 2) {
            updatePlayerReview();
        }
    }

    function updatePlayerReview() {
        const fullName = [
            document.getElementById('playerFirstName')?.value,
            document.getElementById('playerLastName')?.value
        ].filter(Boolean).join(' ');
        const battingSelect = document.getElementById('playerBatting');
        const bowlingSelect = document.getElementById('playerBowling');
        const subscriptionSelect = document.getElementById('playerSubscription');

        setText('reviewPlayerFullName', fullName || '-');
        setText('reviewPlayerDOB', document.getElementById('playerDOB')?.value || '-');
        setText('reviewPlayerJersey', document.getElementById('playerJersey')?.value || 'Not assigned');
        setText('reviewPlayerEmail', document.getElementById('playerEmail')?.value || '-');
        setText('reviewPlayerPhone', document.getElementById('playerPhone')?.value || '-');
        setText('reviewPlayerAddress', document.getElementById('playerAddress')?.value || '-');
        setText('reviewPlayerUsername', document.getElementById('playerUsername')?.value || '-');
        setText('reviewPlayerBatting', battingSelect?.options[battingSelect.selectedIndex]?.text || 'Not set');
        setText('reviewPlayerBowling', bowlingSelect?.options[bowlingSelect.selectedIndex]?.text || 'Not set');
        setText('reviewPlayerSubscription', subscriptionSelect?.options[subscriptionSelect.selectedIndex]?.text || '-');
    }

    function openViewPlayerModal(data) {
        const modal = document.getElementById('viewPlayerModal');
        setText('viewPlayerName', data.name);
        setText('viewPlayerInitials', data.initials || getInitials(data.name));
        setText('viewPlayerJerseyBadge', data.jersey ? `Jersey #${data.jersey}` : 'No Jersey');
        setText('viewPlayerStatusBadge', formatLabel(data.status));
        document.getElementById('viewPlayerStatusBadge')?.setAttribute('class', 'profile-chip status ' + String(data.status || '').toLowerCase());
        setText('viewFullName', data.name);
        setText('viewEmail', data.email);
        setText('viewPhone', data.phone || 'N/A');
        setText('viewJoined', data.joined);
        setText('viewAddress', data.address || 'N/A');
        setText('viewJerseyNumber', data.jersey || 'Not Assigned');
        setText('viewBattingStyle', data.batting || 'Not Set');
        setText('viewBowlingStyle', data.bowling || 'Not Set');
        setText('viewSubscription', formatLabel(data.subscription));
        setText('viewSubscriptionDetail', formatLabel(data.subscription));
        setText('viewStatus', formatLabel(data.status));

        const editFromViewBtn = document.getElementById('editFromViewBtn');
        if (editFromViewBtn) {
            editFromViewBtn.dataset.playerId = data.id;
            editFromViewBtn.dataset.playerData = JSON.stringify(data);
        }

        showModal(modal);
    }

    function openEditPlayerModal(data) {
        const nameParts = String(data.name || '').trim().split(/\s+/);
        const firstName = nameParts[0] || '';
        const lastName = nameParts.slice(1).join(' ');

        setValue('editPlayerId', data.id);
        setValue('editPlayerFirstName', firstName);
        setValue('editPlayerLastName', lastName);
        setValue('editPlayerEmail', data.email);
        setValue('editPlayerPhone', data.phone || '');
        setValue('editPlayerAddress', data.address || '');
        setValue('editPlayerJersey', data.jersey || '');
        setValue('editPlayerBatting', data.batting || '');
        setValue('editPlayerBowling', data.bowling || '');
        setValue('editPlayerSubscription', data.subscription);
        setValue('editPlayerStatus', data.status);
        showModal(document.getElementById('editPlayerModal'));
    }

    function openDeleteModal(playerId, playerName) {
        setValue('deletePlayerId', playerId);
        setText('deletePlayerName', playerName);
        setValue('deleteConfirmation', '');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) confirmBtn.disabled = true;
        showModal(document.getElementById('deleteModal'));
    }

    function closeDeleteModal() {
        hideModal(document.getElementById('deleteModal'));
    }

    addPlayerBtn?.addEventListener('click', function() {
        document.getElementById('addPlayerForm')?.reset();
        goToPlayerWizardStep(1);
        showModal(addPlayerModal);
    });

    document.getElementById('playerWizardNextBtn')?.addEventListener('click', function() {
        const currentStepContent = document.querySelector(`#addPlayerModal .wizard-step-content[data-step="${currentPlayerStep}"]`);
        const inputs = currentStepContent?.querySelectorAll('input[required], select[required]') || [];
        let isValid = true;

        inputs.forEach(input => {
            const missing = !input.value;
            input.classList.toggle('error', missing);
            if (missing) isValid = false;
        });

        if (isValid) {
            goToPlayerWizardStep(currentPlayerStep + 1);
        } else {
            alert('Please fill in all required fields');
        }
    });

    document.getElementById('playerWizardPrevBtn')?.addEventListener('click', function() {
        goToPlayerWizardStep(currentPlayerStep - 1);
    });

    document.getElementById('modalOverlay')?.addEventListener('click', function() {
        hideModal(addPlayerModal);
    });
    document.getElementById('playerCancelBtn')?.addEventListener('click', function() {
        hideModal(addPlayerModal);
    });

    document.getElementById('addPlayerForm')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());

        fetch(`${urlRoot}/admin/add_player`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Player added successfully.\n\nDefault Password: player123456\n\nThe player should change this password upon first login.');
                    hideModal(addPlayerModal);
                    location.reload();
                    return;
                }

                alert('Failed to add player.\n\n' + (result.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Player add request failed:', error);
                alert('Network error while adding player: ' + error.message);
            });
    });

    document.getElementById('closeViewModal')?.addEventListener('click', () => hideModal(document.getElementById('viewPlayerModal')));
    document.getElementById('closeViewFooterBtn')?.addEventListener('click', () => hideModal(document.getElementById('viewPlayerModal')));
    document.getElementById('closeEditModal')?.addEventListener('click', () => hideModal(document.getElementById('editPlayerModal')));
    document.getElementById('cancelEditBtn')?.addEventListener('click', () => hideModal(document.getElementById('editPlayerModal')));
    document.getElementById('viewModalOverlay')?.addEventListener('click', () => hideModal(document.getElementById('viewPlayerModal')));
    document.getElementById('editModalOverlay')?.addEventListener('click', () => hideModal(document.getElementById('editPlayerModal')));

    document.getElementById('editFromViewBtn')?.addEventListener('click', function() {
        const data = JSON.parse(this.dataset.playerData || '{}');
        hideModal(document.getElementById('viewPlayerModal'));
        setTimeout(() => openEditPlayerModal(data), 300);
    });

    document.getElementById('deleteConfirmation')?.addEventListener('input', function() {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) confirmBtn.disabled = this.value.toUpperCase() !== 'DELETE';
    });

    document.getElementById('deleteForm')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const playerId = document.getElementById('deletePlayerId')?.value;
        const confirmation = document.getElementById('deleteConfirmation')?.value || '';

        if (confirmation.toUpperCase() !== 'DELETE') {
            alert('Please type DELETE to confirm');
            return;
        }

        fetch(`${urlRoot}/admin/delete_player`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ playerId })
        })
            .then(parseJsonResponse)
            .then(result => {
                if (result.success) {
                    alert('Player deleted successfully!');
                    closeDeleteModal();
                    location.reload();
                    return;
                }

                alert('Failed to delete player.\n\n' + (result.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Player delete request failed:', error);
                alert('Network error while deleting player: ' + error.message);
            });
    });

    document.getElementById('editPlayerForm')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());

        fetch(`${urlRoot}/admin/update_player`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(parseJsonResponse)
            .then(result => {
                if (result.success) {
                    alert('Player updated successfully!');
                    hideModal(document.getElementById('editPlayerModal'));
                    location.reload();
                    return;
                }

                alert('Failed to update player.\n\n' + (result.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Player update request failed:', error);
                alert('Network error while updating player: ' + error.message);
            });
    });

    document.addEventListener('click', function(event) {
        const target = event.target.closest('.action-btn');
        if (!target || !target.dataset.playerId) return;

        const playerId = target.dataset.playerId;
        const playerName = target.dataset.playerName;
        const playerStatus = target.dataset.playerStatus || '';
        const playerData = {
            id: playerId,
            name: playerName,
            email: target.dataset.playerEmail,
            phone: target.dataset.playerPhone,
            address: target.dataset.playerAddress,
            jersey: target.dataset.playerJersey,
            batting: target.dataset.playerBatting,
            bowling: target.dataset.playerBowling,
            subscription: target.dataset.playerSubscription,
            status: playerStatus,
            joined: target.dataset.playerJoined,
            initials: target.dataset.playerInitials
        };

        if (target.classList.contains('view')) {
            openViewPlayerModal(playerData);
        } else if (target.classList.contains('edit')) {
            openEditPlayerModal(playerData);
        } else if (target.classList.contains('suspend')) {
            if (playerStatus.toLowerCase() === 'active' && typeof openSuspendModal === 'function') {
                openSuspendModal(playerId, playerName);
            } else if (typeof unsuspendPlayer === 'function' && confirm(`Unsuspend ${playerName}?`)) {
                unsuspendPlayer(playerId, playerName);
            }
        } else if (target.classList.contains('delete')) {
            openDeleteModal(playerId, playerName);
        }
    });

    window.openDeleteModal = openDeleteModal;
    window.closeDeleteModal = closeDeleteModal;
});

function parseJsonResponse(response) {
    return response.text().then(text => {
        try {
            return JSON.parse(text);
        } catch (error) {
            throw new Error('Server returned invalid JSON: ' + text.substring(0, 100));
        }
    });
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

function getInitials(name) {
    const parts = String(name || 'Player').trim().split(/\s+/);
    return ((parts[0]?.[0] || 'P') + (parts[1]?.[0] || '')).toUpperCase();
}
