function openVerifyModal(recordId, playerName, injuryDetails) {
    document.getElementById('verify_record_id').value = recordId;
    document.getElementById('verify_player_name').textContent = playerName;
    document.getElementById('verify_injury_details').textContent = injuryDetails;
    document.getElementById('verify_status').value = '';
    document.getElementById('verify_comments').value = '';
    document.getElementById('verifyModal').style.display = 'block';
}

function closeVerifyModal() {
    document.getElementById('verifyModal').style.display = 'none';
}

function submitVerification() {
    const recordId = document.getElementById('verify_record_id').value;
    const verifyStatus = document.getElementById('verify_status').value;
    const verifyComments = document.getElementById('verify_comments').value;

    if (!verifyStatus) {
        showNotification('Please select a verification status.', 'warning');
        return;
    }

    if (!recordId) {
        showNotification('Invalid record ID.', 'error');
        return;
    }

    const params = new URLSearchParams();
    params.set('record_id', recordId);
    params.set('verify_status', verifyStatus);
    params.set('verify_comments', verifyComments);

    fetch(window.TRAINER_VERIFY_STATUS_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: params.toString(),
    })
        .then(async (response) => {
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error(text || 'Non-JSON response received');
            }
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            return data;
        })
        .then((data) => {
            if (data.success) {
                showNotification('Verification status updated successfully!', 'success');

                const newStatus = (data.new_status || verifyStatus).toLowerCase();
                const labelMap = { verified: 'Verified', rejected: 'Rejected', pending: 'Pending' };
                const displayLabel = labelMap[newStatus] || newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

                const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(7) span');
                    if (statusCell) {
                        statusCell.className = `table-badge verify-status-badge verify-status-${newStatus}`;
                        statusCell.textContent = displayLabel;
                    }
                }

                closeVerifyModal();
            } else {
                showNotification(data.message || 'Unknown error occurred.', 'error');
            }
        })
        .catch(() => {
            showNotification('An error occurred while updating the verification status.', 'error');
        });
}

function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach((notification) => notification.remove());

    const normalizedType = type === 'warning' ? 'warning' : (type === 'error' ? 'error' : 'success');
    const iconClass = normalizedType === 'success'
        ? 'fa-check-circle'
        : normalizedType === 'warning'
            ? 'fa-exclamation-triangle'
            : 'fa-exclamation-circle';

    const notification = document.createElement('div');
    notification.className = `notification notification-${normalizedType}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${iconClass}"></i>
            <span>${escapeVerificationHtml(message)}</span>
            <button type="button" class="notification-close" aria-label="Dismiss notification">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    const closeButton = notification.querySelector('.notification-close');
    if (closeButton) {
        closeButton.addEventListener('click', function () {
            notification.remove();
        });
    }

    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function escapeVerificationHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('verifyModal');
    if (event.target === modal) {
        closeVerifyModal();
    }
});
