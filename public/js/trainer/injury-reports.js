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
        alert('Please select a verification status');
        return;
    }

    if (!recordId) {
        alert('Invalid record ID');
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
                alert('Verification status updated successfully!');

                const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
                if (row) {
                    const badge = row.querySelector('.verify-status-badge');
                    const newStatus = String(data.new_status || verifyStatus).toLowerCase();
                    const label = newStatus ? (newStatus.charAt(0).toUpperCase() + newStatus.slice(1)) : '';
                    if (badge) {
                        badge.className = `table-badge verify-status-badge verify-status-${newStatus}`;
                        badge.textContent = label;
                    }
                }

                closeVerifyModal();
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch((err) => {
            alert('An error occurred while updating the verification status. ' + (err && err.message ? err.message : ''));
        });
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('verifyModal');
    if (event.target === modal) {
        closeVerifyModal();
    }
});
