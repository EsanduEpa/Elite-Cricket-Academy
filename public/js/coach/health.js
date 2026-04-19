const coachHealthRecords = window.__COACH_HEALTH_RECORDS || [];

document.addEventListener('DOMContentLoaded', function() {
    const severityFilter = document.getElementById('severityFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (severityFilter) {
        severityFilter.addEventListener('change', filterHealthTable);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterHealthTable);
    }
});

function filterHealthTable() {
    const severityFilter = document.getElementById('severityFilter')?.value || 'all';
    const statusFilter = document.getElementById('statusFilter')?.value || 'all';
    const rows = document.querySelectorAll('.health-table tbody tr');

    rows.forEach(function(row) {
        const severity = row.getAttribute('data-severity') || '';
        const status = row.getAttribute('data-status') || '';
        const severityMatch = severityFilter === 'all' || severity === severityFilter;
        const statusMatch = statusFilter === 'all' || status === statusFilter;

        row.style.display = severityMatch && statusMatch ? '' : 'none';
    });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function viewDetails(id) {
    const record = coachHealthRecords.find(function(item) {
        return Number(item.RecordID) === Number(id);
    });

    if (!record) {
        return;
    }

    const modalBody = document.getElementById('modalBody');
    const modal = document.getElementById('injuryModal');
    const restDays = Number(record.RestDaysNeeded || 0);
    const recoveryStatus = String(record.RecoveryStatus || 'pending').toLowerCase();
    const verifyStatus = String(record.verifyStatus || 'pending').toLowerCase();
    const severity = restDays > 14 ? 'severe' : (restDays < 7 ? 'mild' : 'moderate');
    const injuryDate = record.InjuryDate
        ? new Date(record.InjuryDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : 'Not specified';

    modalBody.innerHTML = `
        <div class="injury-details">
            <div class="detail-row"><strong>Player:</strong><span>${escapeHtml(record.PlayerName || 'Unknown')}</span></div>
            <div class="detail-row"><strong>Diagnosis:</strong><span>${escapeHtml(record.Diagnosis || 'Not specified')}</span></div>
            <div class="detail-row"><strong>Injury Details:</strong><span>${escapeHtml(record.InjuryDetails || 'No details provided')}</span></div>
            <div class="detail-row"><strong>Severity:</strong><span class="severity-badge ${escapeHtml(severity)}">${escapeHtml(severity)}</span></div>
            <div class="detail-row"><strong>Injury Date:</strong><span>${escapeHtml(injuryDate)}</span></div>
            <div class="detail-row"><strong>Recovery Status:</strong><span class="status-badge ${escapeHtml(recoveryStatus)}">${escapeHtml(record.RecoveryStatus || 'Pending')}</span></div>
            <div class="detail-row"><strong>Rest Days Needed:</strong><span>${escapeHtml(restDays)} days</span></div>
            <div class="detail-row"><strong>Treatment Given:</strong><span>${escapeHtml(record.TreatmentGiven || 'Not specified')}</span></div>
            <div class="detail-row"><strong>Happened at Academy:</strong><span>${record.HappenedAtAcademy === 'yes' ? 'Yes' : 'No'}</span></div>
            <div class="detail-row"><strong>Verification Status:</strong><span class="status-badge ${escapeHtml(verifyStatus)}">${escapeHtml(record.verifyStatus || 'Pending')}</span></div>
        </div>
    `;

    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('injuryModal').style.display = 'none';
}

window.viewDetails = viewDetails;
window.closeModal = closeModal;

window.addEventListener('click', function(event) {
    const modal = document.getElementById('injuryModal');
    if (event.target === modal) {
        closeModal();
    }
});
