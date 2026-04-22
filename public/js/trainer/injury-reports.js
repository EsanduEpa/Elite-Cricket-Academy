function openVerifyModal(button) {
    if (!button) {
        return;
    }

    document.getElementById('verify_record_id').value = button.dataset.recordId || '';
    document.getElementById('verify_player_name').textContent = button.dataset.playerName || '';
    document.getElementById('verify_injury_details').textContent = button.dataset.injuryDetails || '';
    document.getElementById('verify_status').value = button.dataset.verifyStatus || '';
    document.getElementById('verify_comments').value = '';
    document.getElementById('verifyModal').style.display = 'block';
}

function closeVerifyModal() {
    const modal = document.getElementById('verifyModal');
    if (!modal) {
        return;
    }

    modal.style.display = 'none';
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('verifyModal');
    if (event.target === modal) {
        closeVerifyModal();
    }
});
