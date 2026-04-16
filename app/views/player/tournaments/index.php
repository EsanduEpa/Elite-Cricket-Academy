<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<style>
.tournament-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:20px; padding:20px 0; }
.tournament-card  { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; display:flex; flex-direction:column; }
.card-header-strip { padding:16px 20px; background:linear-gradient(135deg,#4A90E2,#357ABD); color:#fff; }
.card-header-strip h3 { margin:0 0 4px; font-size:1rem; }
.card-header-strip .fmt-badge { font-size:.72rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,.2); }
.card-body { padding:16px 20px; flex:1; }
.card-body p { margin:4px 0; font-size:.88rem; color:#555; }
.card-body p strong { color:#333; }
.card-footer-strip { padding:12px 20px; border-top:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
.btn-sm { padding:6px 14px; font-size:.83rem; border-radius:6px; text-decoration:none; border:none; cursor:pointer; display:inline-block; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.status-pill { padding:4px 12px; border-radius:20px; font-size:.78rem; font-weight:700; }
.pill-applied  { background:#dbeafe; color:#1e40af; }
.pill-ineligible { background:#fff7ed; color:#9a3412; }
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

<div class="player-layout">
    <!-- Sidebar -->
    <div class="player-sidebar" id="playerSidebar">
        <div class="sidebar-header">
            <div class="player-logo">
                <i class="fas fa-user-graduate"></i>
                <h3>Player Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training" class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/performance" class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots" class="nav-link"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($data['player']['name'] ?? 'Player'); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($data['player']['membership_level'] ?? 'Regular'); ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>View upcoming tournaments and submit your join requests</p>
                </div>
                <div class="header-actions">
                </div>
            </div>
        </div>

        <?php flash('tournament_message'); ?>

        <div style="padding:0 24px 24px;">
            <!-- Filters -->
            <div class="filter-section">
                <div class="filter-group">
                    <label for="ageGroupFilter">Age Group:</label>
                    <select id="ageGroupFilter">
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
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter">
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
                <button class="btn-reset-filter" onclick="resetFilters()"><i class="fas fa-redo"></i> Reset</button>
            </div>
            <?php if (empty($data['tournaments'])): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>No Tournaments Available</h3>
                    <p>There are no open tournaments at the moment. Check back later.</p>
                </div>
            <?php else: ?>
                <div class="tournament-cards" id="tournamentCards">
                    <?php foreach ($data['tournaments'] as $t): ?>
                        <?php $myReq = $data['my_requests'][$t->TournamentID] ?? null; ?>
                        <?php $eligibility = $data['eligibility'][$t->TournamentID] ?? ['eligible' => true, 'message' => '']; ?>
                        <div class="tournament-card" data-age-group="<?php echo htmlspecialchars($t->AgeGroup ?? ''); ?>" data-status="<?php echo htmlspecialchars($t->Status ?? ''); ?>">
                            <div class="card-header-strip">
                                <h3><?php echo htmlspecialchars($t->Name); ?></h3>
                                <span class="fmt-badge"><?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></span>
                            </div>
                            <div class="card-body">
                                <p><strong>Date:</strong> <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></p>
                                <p><strong>Age Group:</strong> <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
                                <p><strong>Your eligibility:</strong> <?php echo $eligibility['eligible'] ? 'Eligible' : 'Not eligible'; ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($t->Location ?? 'TBD'); ?></p>
                                <p><strong>Deadline:</strong> <?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></p>
                                <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($t->Status)); ?></p>
                            </div>
                            <div class="card-footer-strip">
                                <a href="<?php echo URLROOT; ?>/player/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <?php if ($myReq): ?>
                                    <span class="status-pill pill-applied">
                                        <i class="fas fa-check-circle"></i> Applied
                                    </span>
                                <?php elseif ($t->Status === 'registration_open' && !$eligibility['eligible']): ?>
                                    <span class="status-pill pill-ineligible">
                                        <i class="fas fa-ban"></i> Not Eligible
                                    </span>
                                <?php elseif ($t->Status === 'registration_open'): ?>
                                    <a href="<?php echo URLROOT; ?>/player/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-primary">
                                        <i class="fas fa-paper-plane"></i> Apply
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const ageGroupFilter = document.getElementById('ageGroupFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const cards = document.querySelectorAll('.tournament-card');
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
    const container = document.getElementById('tournamentCards');
    if (container && visibleCount === 0) {
        let noResults = document.getElementById('noResultsMessage');
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.id = 'noResultsMessage';
            noResults.className = 'empty-state';
            noResults.style.gridColumn = '1 / -1';
            noResults.innerHTML = '<i class="fas fa-search"></i><h3>No Tournaments Found</h3><p>Try adjusting your filters.</p>';
            container.appendChild(noResults);
        }
    } else {
        const noResults = document.getElementById('noResultsMessage');
        if (noResults) noResults.remove();
    }
}

function resetFilters() {
    document.getElementById('ageGroupFilter').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

// Add event listeners
document.getElementById('ageGroupFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
