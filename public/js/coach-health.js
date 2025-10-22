// Coach Health & Injury Monitoring Page - JavaScript with Dummy Data

// Dummy Data
const healthRecordsData = [
    {
        id: 1,
        playerId: 1,
        playerName: 'Alex Smith',
        injuryType: 'muscle_strain',
        injuryDescription: 'Hamstring strain during training session',
        affectedArea: 'Right hamstring',
        dateReported: '2025-10-15',
        severity: 'moderate',
        status: 'recovering',
        recoveryProgress: 65,
        estimatedRecovery: 14,
        daysInRecovery: 6,
        restrictions: 'No heavy running for 2 weeks. Light exercises only.',
        requiresPhysio: true,
        medicalNotes: [
            { date: '2025-10-15', note: 'Initial assessment. Recommended rest and ice therapy.' },
            { date: '2025-10-18', note: 'Progress check. Swelling reduced. Started light stretching.' },
            { date: '2025-10-21', note: 'Good progress. Can resume light jogging next week.' }
        ],
        assignedPhysio: 'Dr. Sarah Johnson'
    },
    {
        id: 2,
        playerId: 2,
        playerName: 'Emma Davis',
        injuryType: 'sprain',
        injuryDescription: 'Ankle sprain during match',
        affectedArea: 'Left ankle',
        dateReported: '2025-10-10',
        severity: 'minor',
        status: 'under_observation',
        recoveryProgress: 85,
        estimatedRecovery: 10,
        daysInRecovery: 11,
        restrictions: 'Avoid lateral movements. Wear ankle support.',
        requiresPhysio: true,
        medicalNotes: [
            { date: '2025-10-10', note: 'Mild ankle sprain. RICE protocol initiated.' },
            { date: '2025-10-14', note: 'Significant improvement. Can walk without pain.' },
            { date: '2025-10-20', note: 'Almost fully recovered. Cleared for light training.' }
        ],
        assignedPhysio: 'Dr. Sarah Johnson'
    },
    {
        id: 3,
        playerId: 3,
        playerName: 'Sarah Wilson',
        injuryType: 'fitness_evaluation',
        injuryDescription: 'Routine fitness evaluation before tournament',
        affectedArea: 'N/A',
        dateReported: '2025-10-18',
        severity: 'minor',
        status: 'cleared',
        recoveryProgress: 100,
        estimatedRecovery: 0,
        daysInRecovery: 3,
        restrictions: 'None',
        requiresPhysio: false,
        medicalNotes: [
            { date: '2025-10-18', note: 'Comprehensive fitness evaluation conducted.' },
            { date: '2025-10-19', note: 'All tests passed. Excellent physical condition.' },
            { date: '2025-10-21', note: 'Cleared for all activities including tournament participation.' }
        ],
        assignedPhysio: 'Dr. Michael Chen'
    },
    {
        id: 4,
        playerId: 4,
        playerName: 'James Brown',
        injuryType: 'muscle_strain',
        injuryDescription: 'Minor shoulder strain from bowling',
        affectedArea: 'Right shoulder',
        dateReported: '2025-10-12',
        severity: 'minor',
        status: 'recovering',
        recoveryProgress: 75,
        estimatedRecovery: 7,
        daysInRecovery: 9,
        restrictions: 'No bowling for 1 week. Light batting practice only.',
        requiresPhysio: true,
        medicalNotes: [
            { date: '2025-10-12', note: 'Mild rotator cuff strain. Rest recommended.' },
            { date: '2025-10-16', note: 'Pain reduced. Starting physiotherapy exercises.' },
            { date: '2025-10-20', note: 'Good response to treatment. Can resume light activities.' }
        ],
        assignedPhysio: 'Dr. Sarah Johnson'
    },
    {
        id: 5,
        playerId: 5,
        playerName: 'Emily Clark',
        injuryType: 'ligament',
        injuryDescription: 'Minor knee ligament strain',
        affectedArea: 'Left knee',
        dateReported: '2025-10-08',
        severity: 'moderate',
        status: 'injured',
        recoveryProgress: 40,
        estimatedRecovery: 21,
        daysInRecovery: 13,
        restrictions: 'No running or jumping. Swimming exercises only.',
        requiresPhysio: true,
        medicalNotes: [
            { date: '2025-10-08', note: 'MCL strain diagnosed. MRI scheduled.' },
            { date: '2025-10-10', note: 'MRI shows Grade 1 strain. Conservative treatment plan.' },
            { date: '2025-10-15', note: 'Slow but steady progress. Continue physiotherapy.' }
        ],
        assignedPhysio: 'Dr. Michael Chen'
    },
    {
        id: 6,
        playerId: 6,
        playerName: 'Michael Lee',
        injuryType: 'other',
        injuryDescription: 'Heat exhaustion during training',
        affectedArea: 'General',
        dateReported: '2025-10-19',
        severity: 'minor',
        status: 'under_observation',
        recoveryProgress: 90,
        estimatedRecovery: 3,
        daysInRecovery: 2,
        restrictions: 'Proper hydration. Avoid peak sun hours for training.',
        requiresPhysio: false,
        medicalNotes: [
            { date: '2025-10-19', note: 'Mild heat exhaustion. Given fluids and rest.' },
            { date: '2025-10-20', note: 'Fully recovered. Education on hydration importance provided.' }
        ],
        assignedPhysio: null
    }
];

const playersForSelection = [
    { id: 1, name: 'Alex Smith', currentStatus: 'recovering' },
    { id: 2, name: 'Emma Davis', currentStatus: 'under_observation' },
    { id: 3, name: 'Sarah Wilson', currentStatus: 'cleared' },
    { id: 4, name: 'James Brown', currentStatus: 'recovering' },
    { id: 5, name: 'Emily Clark', currentStatus: 'injured' },
    { id: 6, name: 'Michael Lee', currentStatus: 'under_observation' },
    { id: 7, name: 'David Martinez', currentStatus: null },
    { id: 8, name: 'Sophie Anderson', currentStatus: null },
    { id: 9, name: 'Ryan Thompson', currentStatus: null },
    { id: 10, name: 'Olivia White', currentStatus: null }
];

let currentFilter = { status: 'all', severity: 'all', search: '' };

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
    loadHealthRecords();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Filter listeners
    document.getElementById('statusFilter').addEventListener('change', function(e) {
        currentFilter.status = e.target.value;
        loadHealthRecords();
    });

    document.getElementById('severityFilter').addEventListener('change', function(e) {
        currentFilter.severity = e.target.value;
        loadHealthRecords();
    });

    document.getElementById('playerSearch').addEventListener('input', function(e) {
        currentFilter.search = e.target.value.toLowerCase();
        loadHealthRecords();
    });

    // Add injury report button
    document.getElementById('addInjuryReportBtn').addEventListener('click', openInjuryReportModal);
    document.getElementById('closeInjuryModal').addEventListener('click', closeInjuryReportModal);
    document.getElementById('cancelInjuryReport').addEventListener('click', closeInjuryReportModal);

    // Health details modal
    document.getElementById('closeHealthDetailsModal').addEventListener('click', closeHealthDetailsModal);

    // Update recovery modal
    document.getElementById('closeUpdateRecoveryModal').addEventListener('click', closeUpdateRecoveryModal);
    document.getElementById('cancelUpdateRecovery').addEventListener('click', closeUpdateRecoveryModal);

    // Forms
    document.getElementById('injuryReportForm').addEventListener('submit', handleInjuryReportSubmit);
    document.getElementById('updateRecoveryForm').addEventListener('submit', handleUpdateRecoverySubmit);

    // Recovery progress slider
    document.getElementById('recoveryProgress').addEventListener('input', function(e) {
        document.getElementById('progressValue').textContent = e.target.value;
    });
}

// Update statistics
function updateStatistics() {
    const activeInjuries = healthRecordsData.filter(r => r.status === 'injured').length;
    const recovering = healthRecordsData.filter(r => r.status === 'recovering').length;
    const cleared = healthRecordsData.filter(r => r.status === 'cleared').length;
    const evaluation = healthRecordsData.filter(r => r.injuryType === 'fitness_evaluation' && r.status !== 'cleared').length;

    document.getElementById('activeInjuriesCount').textContent = activeInjuries;
    document.getElementById('recoveringCount').textContent = recovering;
    document.getElementById('clearedCount').textContent = cleared;
    document.getElementById('evaluationCount').textContent = evaluation;
}

// Load and display health records
function loadHealthRecords() {
    const tbody = document.getElementById('healthRecordsBody');
    
    let filtered = healthRecordsData.filter(record => {
        const matchesStatus = currentFilter.status === 'all' || record.status === currentFilter.status;
        const matchesSeverity = currentFilter.severity === 'all' || record.severity === currentFilter.severity;
        const matchesSearch = !currentFilter.search || 
            record.playerName.toLowerCase().includes(currentFilter.search) ||
            record.injuryType.toLowerCase().includes(currentFilter.search);
        
        return matchesStatus && matchesSeverity && matchesSearch;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="no-records">
                    <i class="fas fa-heartbeat"></i>
                    <p>No health records found</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filtered.map(record => createHealthRecordRow(record)).join('');
}

// Create health record row
function createHealthRecordRow(record) {
    const statusClass = record.status.replace('_', '-');
    const severityClass = record.severity;
    
    const statusIcon = {
        injured: 'fa-exclamation-circle',
        recovering: 'fa-sync-alt',
        cleared: 'fa-check-circle',
        under_observation: 'fa-eye'
    }[record.status];

    return `
        <tr class="health-record-row" onclick="viewHealthDetails(${record.id})">
            <td>
                <div class="player-cell">
                    <i class="fas fa-user-circle"></i>
                    <span>${record.playerName}</span>
                </div>
            </td>
            <td>${formatInjuryType(record.injuryType)}</td>
            <td>${formatDate(record.dateReported)}</td>
            <td>
                <span class="status-badge ${statusClass}">
                    <i class="fas ${statusIcon}"></i>
                    ${formatStatus(record.status)}
                </span>
            </td>
            <td>
                <span class="severity-badge ${severityClass}">
                    ${record.severity.charAt(0).toUpperCase() + record.severity.slice(1)}
                </span>
            </td>
            <td>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: ${record.recoveryProgress}%"></div>
                    <span class="progress-text">${record.recoveryProgress}%</span>
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action view" onclick="event.stopPropagation(); viewHealthDetails(${record.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${record.status !== 'cleared' ? `
                    <button class="btn-action edit" onclick="event.stopPropagation(); openUpdateRecoveryModal(${record.id})" title="Update Progress">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `;
}

// View health record details
function viewHealthDetails(recordId) {
    const record = healthRecordsData.find(r => r.id === recordId);
    if (!record) return;

    const modal = document.getElementById('healthDetailsModal');
    const title = document.getElementById('healthDetailsTitle');
    const body = document.getElementById('healthDetailsBody');

    title.textContent = `${record.playerName} - Health Record`;

    body.innerHTML = `
        <div class="health-details-content">
            <div class="details-header">
                <div class="player-info-large">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <h3>${record.playerName}</h3>
                        <span class="status-badge ${record.status.replace('_', '-')}">
                            ${formatStatus(record.status)}
                        </span>
                        <span class="severity-badge ${record.severity}">
                            ${record.severity.charAt(0).toUpperCase() + record.severity.slice(1)} Severity
                        </span>
                    </div>
                </div>
                <div class="recovery-progress-large">
                    <div class="circular-progress">
                        <svg viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#e0e0e0" stroke-width="10"></circle>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#4CAF50" stroke-width="10" 
                                stroke-dasharray="${2 * Math.PI * 45}" 
                                stroke-dashoffset="${2 * Math.PI * 45 * (1 - record.recoveryProgress / 100)}"
                                transform="rotate(-90 50 50)"></circle>
                        </svg>
                        <div class="progress-text">${record.recoveryProgress}%</div>
                    </div>
                    <p>Recovery Progress</p>
                </div>
            </div>

            <div class="details-section">
                <h4><i class="fas fa-info-circle"></i> Injury Information</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Injury Type:</label>
                        <span>${formatInjuryType(record.injuryType)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Affected Area:</label>
                        <span>${record.affectedArea}</span>
                    </div>
                    <div class="detail-item">
                        <label>Date Reported:</label>
                        <span>${formatDate(record.dateReported)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Days in Recovery:</label>
                        <span>${record.daysInRecovery} days</span>
                    </div>
                    <div class="detail-item">
                        <label>Estimated Recovery:</label>
                        <span>${record.estimatedRecovery} days</span>
                    </div>
                    <div class="detail-item">
                        <label>Requires Physiotherapy:</label>
                        <span>${record.requiresPhysio ? 'Yes' : 'No'}</span>
                    </div>
                    ${record.assignedPhysio ? `
                    <div class="detail-item">
                        <label>Assigned Physio:</label>
                        <span>${record.assignedPhysio}</span>
                    </div>
                    ` : ''}
                </div>
                <div class="detail-full">
                    <label>Description:</label>
                    <p>${record.injuryDescription}</p>
                </div>
                ${record.restrictions ? `
                <div class="detail-full restrictions">
                    <label><i class="fas fa-exclamation-triangle"></i> Restrictions:</label>
                    <p>${record.restrictions}</p>
                </div>
                ` : ''}
            </div>

            ${record.medicalNotes && record.medicalNotes.length > 0 ? `
            <div class="details-section">
                <h4><i class="fas fa-notes-medical"></i> Medical Notes</h4>
                <div class="medical-notes-timeline">
                    ${record.medicalNotes.map(note => `
                        <div class="note-item">
                            <div class="note-date">${formatDate(note.date)}</div>
                            <div class="note-content">${note.note}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            <div class="details-actions">
                ${record.status !== 'cleared' ? `
                <button class="btn-primary" onclick="openUpdateRecoveryModal(${record.id})">
                    <i class="fas fa-sync-alt"></i>
                    Update Recovery Progress
                </button>
                ` : ''}
                <button class="btn-secondary" onclick="sendRecommendationToTrainer(${record.id})">
                    <i class="fas fa-paper-plane"></i>
                    Send to Trainer
                </button>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

// Close health details modal
function closeHealthDetailsModal() {
    document.getElementById('healthDetailsModal').classList.remove('active');
}

// Open injury report modal
function openInjuryReportModal() {
    const modal = document.getElementById('injuryReportModal');
    const select = document.getElementById('playerSelect');
    
    // Populate player select
    select.innerHTML = '<option value="">Choose player...</option>' + 
        playersForSelection.map(p => 
            `<option value="${p.id}">${p.name}</option>`
        ).join('');
    
    document.getElementById('injuryReportForm').reset();
    document.getElementById('injuryDate').valueAsDate = new Date();
    
    modal.classList.add('active');
}

// Close injury report modal
function closeInjuryReportModal() {
    document.getElementById('injuryReportModal').classList.remove('active');
}

// Handle injury report submit
function handleInjuryReportSubmit(e) {
    e.preventDefault();
    
    const playerId = parseInt(document.getElementById('playerSelect').value);
    const player = playersForSelection.find(p => p.id === playerId);
    
    const newRecord = {
        id: healthRecordsData.length + 1,
        playerId: playerId,
        playerName: player.name,
        injuryType: document.getElementById('injuryType').value,
        injuryDescription: document.getElementById('injuryDescription').value,
        affectedArea: document.getElementById('affectedArea').value,
        dateReported: document.getElementById('injuryDate').value,
        severity: document.getElementById('severity').value,
        status: 'injured',
        recoveryProgress: 0,
        estimatedRecovery: parseInt(document.getElementById('estimatedRecovery').value) || 0,
        daysInRecovery: 0,
        restrictions: document.getElementById('restrictions').value,
        requiresPhysio: document.getElementById('requiresPhysio').value === 'yes',
        medicalNotes: [{
            date: document.getElementById('injuryDate').value,
            note: 'Initial injury report filed.'
        }],
        assignedPhysio: null
    };

    healthRecordsData.unshift(newRecord);
    
    alert('Injury report submitted successfully!');
    updateStatistics();
    loadHealthRecords();
    closeInjuryReportModal();
}

// Open update recovery modal
function openUpdateRecoveryModal(recordId) {
    const record = healthRecordsData.find(r => r.id === recordId);
    if (!record) return;

    closeHealthDetailsModal(); // Close details modal if open

    const modal = document.getElementById('updateRecoveryModal');
    document.getElementById('updateRecordId').value = recordId;
    document.getElementById('recoveryStatus').value = record.status;
    document.getElementById('recoveryProgress').value = record.recoveryProgress;
    document.getElementById('progressValue').textContent = record.recoveryProgress;
    document.getElementById('recoveryNotes').value = '';
    
    modal.classList.add('active');
}

// Close update recovery modal
function closeUpdateRecoveryModal() {
    document.getElementById('updateRecoveryModal').classList.remove('active');
}

// Handle update recovery submit
function handleUpdateRecoverySubmit(e) {
    e.preventDefault();
    
    const recordId = parseInt(document.getElementById('updateRecordId').value);
    const record = healthRecordsData.find(r => r.id === recordId);
    
    if (!record) return;

    record.status = document.getElementById('recoveryStatus').value;
    record.recoveryProgress = parseInt(document.getElementById('recoveryProgress').value);
    
    const notes = document.getElementById('recoveryNotes').value;
    if (notes) {
        record.medicalNotes.push({
            date: new Date().toISOString().split('T')[0],
            note: notes
        });
    }

    alert('Recovery progress updated successfully!');
    updateStatistics();
    loadHealthRecords();
    closeUpdateRecoveryModal();
}

// Send recommendation to trainer
function sendRecommendationToTrainer(recordId) {
    const record = healthRecordsData.find(r => r.id === recordId);
    if (!record) return;

    alert(`Recommendation for ${record.playerName} sent to physical trainer successfully!`);
    closeHealthDetailsModal();
}

// Helper functions
function formatInjuryType(type) {
    return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function formatStatus(status) {
    return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
