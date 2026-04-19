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

    fetch(window.TRAINER_VERIFY_STATUS_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `record_id=${recordId}&verify_status=${verifyStatus}&verify_comments=${encodeURIComponent(verifyComments)}`,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert('Verification status updated successfully!');

                const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(9) span');
                    if (statusCell) {
                        statusCell.className = `table-badge verify-status-${verifyStatus.toLowerCase()}`;
                        statusCell.textContent = verifyStatus;
                    }
                }

                closeVerifyModal();
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(() => {
            alert('An error occurred while updating the verification status.');
        });
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('verifyModal');
    if (event.target === modal) {
        closeVerifyModal();
    }
});
