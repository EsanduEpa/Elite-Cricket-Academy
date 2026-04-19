<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'tournaments'; ?>
<?php
$trainerDisplayName = trim((string) ($_SESSION['user_name'] ?? $_SESSION['username'] ?? ''));
if ($trainerDisplayName === '') {
    $trainerDisplayName = 'Trainer';
}
?>
<style>
.trainer-sidebar .trainer-details {
    display: block !important;
}
.trainer-sidebar.collapsed .trainer-details {
    display: none !important;
}
.tournament-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; padding: 20px 0; }
.tournament-card  { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); overflow: hidden; display: flex; flex-direction: column; }
.card-header-strip { padding: 16px 20px; background: linear-gradient(135deg,#4A90E2,#357ABD); color:#fff; }
.card-header-strip h3 { margin:0 0 4px; font-size:1rem; }
.card-header-strip .badge { font-size:.72rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,.2); }
.card-body { padding:16px 20px; flex:1; }
.card-body p { margin:4px 0; font-size:.88rem; color:#555; }
.card-body p strong { color:#333; }
.card-footer-strip { padding:12px 20px; border-top:1px solid #f0f0f0; display:flex; gap:10px; }
.btn-sm { padding:6px 14px; font-size:.83rem; border-radius:6px; text-decoration:none; border:none; cursor:pointer; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.empty-state { text-align:center; padding:60px 20px; color:#888; }
.empty-state i { font-size:3rem; margin-bottom:16px; display:block; color:#ccc; }
.filter-section { display:flex; gap:16px; margin:20px 0; flex-wrap:wrap; align-items:center; }
.filter-group { display:flex; align-items:center; gap:8px; }
.filter-group label { font-weight:600; color:#333; font-size:.95rem; }
.filter-group select { padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:.88rem; background:#fff; cursor:pointer; min-width:150px; }
.filter-group select:focus { outline:none; border-color:#4A90E2; }
.btn-reset-filter { padding:8px 14px; background:#f0f0f0; border:1px solid #ddd; border-radius:6px; cursor:pointer; font-size:.88rem; color:#333; }
.btn-reset-filter:hover { background:#e0e0e0; }
</style>

<div class="trainer-layout">
    <!-- Sidebar -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar"><i class="fas fa-user-circle"></i></div>
                <div class="trainer-details">
                    <h4><?php echo htmlspecialchars($trainerDisplayName, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>View upcoming tournaments and recommend players</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/my_recommendations" class="btn-sm btn-secondary">
                        <i class="fas fa-list"></i> My Recommendations
                    </a>
                </div>
            </div>
        </div>

        <?php flash('tournament_message'); ?>

        <div class="content-section">
            <!-- Filters -->
            <?php if (!empty($data['tournaments'])): ?>
            <div class="filter-section">
                <div class="filter-group">
                    <label for="trainerAgeGroupFilter">Age Group:</label>
                    <select id="trainerAgeGroupFilter">
                        <option value="">All Age Groups</option>
                        <?php 
                            $ageGroups = [];
                            foreach ($data['tournaments'] as $t) {
                                if (!empty($t->AgeGroup) && !in_array($t->AgeGroup, $ageGroups)) {
                                    $ageGroups[] = $t->AgeGroup;
                                }
                            }
                            sort($ageGroups);
                            foreach ($ageGroups as $ag):
                        ?>
                        <option value="<?php echo htmlspecialchars($ag); ?>"><?php echo htmlspecialchars($ag); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="trainerStatusFilter">Status:</label>
                    <select id="trainerStatusFilter">
                        <option value="">All Statuses</option>
                        <?php 
                            $statuses = [];
                            foreach ($data['tournaments'] as $t) {
                                if (!empty($t->Status) && !in_array($t->Status, $statuses)) {
                                    $statuses[] = $t->Status;
                                }
                            }
                            sort($statuses);
                            foreach ($statuses as $st):
                        ?>
                        <option value="<?php echo htmlspecialchars($st); ?>"><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($st))); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-reset-filter" onclick="resetTrainerFilters()"><i class="fas fa-redo"></i> Reset</button>
            </div>
            <?php endif; ?>
            <?php if (empty($data['tournaments'])): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>No Tournaments Available</h3>
                    <p>There are no open tournaments at the moment.</p>
                </div>
            <?php else: ?>
                <div class="tournament-cards" id="trainerTournamentCards">
                    <?php foreach ($data['tournaments'] as $t): ?>
                        <div class="tournament-card" data-age-group="<?php echo htmlspecialchars($t->AgeGroup ?? ''); ?>" data-status="<?php echo htmlspecialchars($t->Status ?? ''); ?>">
                            <div class="card-header-strip">
                                <h3><?php echo htmlspecialchars($t->Name); ?></h3>
                                <span class="badge"><?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></span>
                            </div>
                            <div class="card-body">
                                <p><strong>Date:</strong> <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></p>
                                <p><strong>Age Group:</strong> <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($t->Location ?? 'TBD'); ?></p>
                                <p><strong>Registration Deadline:</strong>
                                    <?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></p>
                                <p><strong>Status:</strong> <span style="color:#2e6da4;font-weight:600;"><?php echo ucfirst(htmlspecialchars($t->Status)); ?></span></p>
                            </div>
                            <div class="card-footer-strip">
                                <a href="<?php echo URLROOT; ?>/trainer/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="<?php echo URLROOT; ?>/trainer/recommend_player/<?php echo $t->TournamentID; ?>" class="btn-sm btn-primary">
                                    <i class="fas fa-user-plus"></i> Recommend Player
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function applyTrainerFilters() {
    const ageGroupFilter = document.getElementById('trainerAgeGroupFilter').value.toLowerCase();
    const statusFilter = document.getElementById('trainerStatusFilter').value.toLowerCase();
    const cards = document.querySelectorAll('#trainerTournamentCards .tournament-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const cardAgeGroup = card.getAttribute('data-age-group').toLowerCase();
        const cardStatus = card.getAttribute('data-status').toLowerCase();
        
        const matchesAgeGroup = !ageGroupFilter || cardAgeGroup === ageGroupFilter;
        const matchesStatus = !statusFilter || cardStatus === statusFilter;
        
        if (matchesAgeGroup && matchesStatus) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show no results message if needed
    const container = document.getElementById('trainerTournamentCards');
    if (container && visibleCount === 0) {
        let noResults = document.getElementById('trainerNoResults');
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.id = 'trainerNoResults';
            noResults.className = 'empty-state';
            noResults.style.gridColumn = '1 / -1';
            noResults.innerHTML = '<i class="fas fa-search"></i><h3>No Tournaments Found</h3><p>Try adjusting your filters.</p>';
            container.appendChild(noResults);
        }
    } else {
        const noResults = document.getElementById('trainerNoResults');
        if (noResults) noResults.remove();
    }
}

function resetTrainerFilters() {
    document.getElementById('trainerAgeGroupFilter').value = '';
    document.getElementById('trainerStatusFilter').value = '';
    applyTrainerFilters();
}

// Add event listeners
if (document.getElementById('trainerAgeGroupFilter')) {
    document.getElementById('trainerAgeGroupFilter').addEventListener('change', applyTrainerFilters);
    document.getElementById('trainerStatusFilter').addEventListener('change', applyTrainerFilters);
}
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
