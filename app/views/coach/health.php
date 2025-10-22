<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/health.css">

<!-- Coach Dashboard Layout -->
<div class="coach-layout">
    <!-- Left Sidebar Panel -->
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Coach Panel</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Sessions</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health & Injury</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                        <i class="fas fa-calendar"></i>
                        <span>Events</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1>
                        <i class="fas fa-heartbeat"></i>
                        Health & Injury Monitoring
                    </h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">Monitor player health status and injury reports</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon recovering">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="stat-content">
                    <h3>Currently Injured</h3>
                    <div class="stat-number">5</div>
                    <p class="stat-description">Players recovering</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon recovered">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Recovered</h3>
                    <div class="stat-number">3</div>
                    <p class="stat-description">Back to training</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon severe">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Severe Cases</h3>
                    <div class="stat-number">2</div>
                    <p class="stat-description">Requires attention</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Review</h3>
                    <div class="stat-number">1</div>
                    <p class="stat-description">Awaiting verification</p>
                </div>
            </div>
        </div>

        <!-- Injury Reports Table -->
        <div class="health-card">
            <div class="card-header">
                <h3><i class="fas fa-notes-medical"></i> Injury Reports</h3>
                <div class="header-actions">
                    <select class="filter-select" id="severityFilter">
                        <option value="all">All Severities</option>
                        <option value="mild">Mild</option>
                        <option value="moderate">Moderate</option>
                        <option value="severe">Severe</option>
                    </select>
                    <select class="filter-select" id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="recovering">Recovering</option>
                        <option value="recovered">Recovered</option>
                    </select>
                </div>
            </div>
            
            <div class="table-container">
                <table class="health-table">
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Injury Type</th>
                            <th>Description</th>
                            <th>Severity</th>
                            <th>Date Reported</th>
                            <th>Status</th>
                            <th>Recovery Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Dummy data - not from database
                        $injuries = [
                            [
                                'id' => 1,
                                'player' => 'Sandun Akalanka',
                                'type' => 'Muscle Strain',
                                'description' => 'Hamstring strain during batting practice',
                                'severity' => 'moderate',
                                'date' => '2025-10-15',
                                'status' => 'recovering',
                                'recovery_date' => '2025-10-25',
                                'treatment' => 'Rest for 5 days, physiotherapy sessions, ice therapy'
                            ],
                            [
                                'id' => 2,
                                'player' => 'Kavindu Perera',
                                'type' => 'Ankle Sprain',
                                'description' => 'Ankle sprain while fielding',
                                'severity' => 'mild',
                                'date' => '2025-10-18',
                                'status' => 'recovering',
                                'recovery_date' => '2025-10-23',
                                'treatment' => 'RICE protocol, ankle support'
                            ],
                            [
                                'id' => 3,
                                'player' => 'Ravindu Silva',
                                'type' => 'Finger Fracture',
                                'description' => 'Finger fracture while catching',
                                'severity' => 'severe',
                                'date' => '2025-09-28',
                                'status' => 'recovered',
                                'recovery_date' => '2025-10-28',
                                'treatment' => 'Splint for 4 weeks, follow-up X-ray'
                            ],
                            [
                                'id' => 4,
                                'player' => 'Tharaka Wickramasinghe',
                                'type' => 'Thigh Bruise',
                                'description' => 'Thigh bruise from impact',
                                'severity' => 'mild',
                                'date' => '2025-10-20',
                                'status' => 'recovering',
                                'recovery_date' => '2025-10-24',
                                'treatment' => 'Ice application, pain relief medication'
                            ],
                            [
                                'id' => 5,
                                'player' => 'Dilshan Nanayakkara',
                                'type' => 'Concussion',
                                'description' => 'Head impact during match',
                                'severity' => 'severe',
                                'date' => '2025-10-12',
                                'status' => 'recovering',
                                'recovery_date' => '2025-10-26',
                                'treatment' => 'Complete rest, concussion protocol, no contact sports'
                            ],
                            [
                                'id' => 6,
                                'player' => 'Nimal Fernando',
                                'type' => 'Back Strain',
                                'description' => 'Lower back strain during bowling',
                                'severity' => 'moderate',
                                'date' => '2025-10-10',
                                'status' => 'recovered',
                                'recovery_date' => '2025-10-20',
                                'treatment' => 'Physiotherapy, core strengthening exercises'
                            ],
                            [
                                'id' => 7,
                                'player' => 'Chamika Jayasinghe',
                                'type' => 'Hand Cut',
                                'description' => 'Deep cut on hand from equipment',
                                'severity' => 'mild',
                                'date' => '2025-10-19',
                                'status' => 'recovering',
                                'recovery_date' => '2025-10-25',
                                'treatment' => 'Cleaned and bandaged, antibiotics prescribed'
                            ],
                            [
                                'id' => 8,
                                'player' => 'Asanka Bandara',
                                'type' => 'Shoulder Dislocation',
                                'description' => 'Shoulder dislocation while diving',
                                'severity' => 'severe',
                                'date' => '2025-09-25',
                                'status' => 'recovered',
                                'recovery_date' => '2025-11-01',
                                'treatment' => 'Shoulder relocated, sling for 3 weeks, physiotherapy'
                            ]
                        ];
                        
                        foreach ($injuries as $injury): 
                            $severityClass = $injury['severity'];
                            $statusClass = $injury['status'];
                        ?>
                        <tr data-severity="<?php echo $severityClass; ?>" data-status="<?php echo $statusClass; ?>">
                            <td>
                                <div class="player-info">
                                    <i class="fas fa-user-circle"></i>
                                    <strong><?php echo $injury['player']; ?></strong>
                                </div>
                            </td>
                            <td><?php echo $injury['type']; ?></td>
                            <td class="injury-desc"><?php echo $injury['description']; ?></td>
                            <td>
                                <span class="severity-badge <?php echo $severityClass; ?>">
                                    <?php echo ucfirst($severityClass); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($injury['date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <i class="fas fa-<?php echo $statusClass === 'recovered' ? 'check-circle' : 'spinner'; ?>"></i>
                                    <?php echo ucfirst($statusClass); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($injury['recovery_date'])); ?></td>
                            <td>
                                <button class="btn-action view" onclick="viewDetails(<?php echo $injury['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Injury Details Modal -->
<div class="modal" id="injuryModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-notes-medical"></i> Injury Details</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('severityFilter').addEventListener('change', function() {
    filterTable();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const severityFilter = document.getElementById('severityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.health-table tbody tr');
    
    rows.forEach(row => {
        const severity = row.getAttribute('data-severity');
        const status = row.getAttribute('data-status');
        
        const severityMatch = severityFilter === 'all' || severity === severityFilter;
        const statusMatch = statusFilter === 'all' || status === statusFilter;
        
        if (severityMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// View details function
function viewDetails(id) {
    const injuries = <?php echo json_encode($injuries); ?>;
    const injury = injuries.find(i => i.id === id);
    
    if (injury) {
        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = `
            <div class="injury-details">
                <div class="detail-row">
                    <strong>Player:</strong>
                    <span>${injury.player}</span>
                </div>
                <div class="detail-row">
                    <strong>Injury Type:</strong>
                    <span>${injury.type}</span>
                </div>
                <div class="detail-row">
                    <strong>Description:</strong>
                    <span>${injury.description}</span>
                </div>
                <div class="detail-row">
                    <strong>Severity:</strong>
                    <span class="severity-badge ${injury.severity}">${injury.severity}</span>
                </div>
                <div class="detail-row">
                    <strong>Date Reported:</strong>
                    <span>${new Date(injury.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                </div>
                <div class="detail-row">
                    <strong>Status:</strong>
                    <span class="status-badge ${injury.status}">${injury.status}</span>
                </div>
                <div class="detail-row">
                    <strong>Expected Recovery:</strong>
                    <span>${new Date(injury.recovery_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                </div>
                <div class="detail-row">
                    <strong>Treatment Plan:</strong>
                    <span>${injury.treatment}</span>
                </div>
            </div>
        `;
        document.getElementById('injuryModal').style.display = 'flex';
    }
}

function closeModal() {
    document.getElementById('injuryModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('injuryModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                if (mainContent) mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                if (mainContent) mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
