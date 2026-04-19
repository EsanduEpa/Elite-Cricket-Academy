<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<style>
.tournament-table-wrap { padding:20px 0; overflow-x:auto; }
.tournament-table { width:100%; border-collapse:separate; border-spacing:0; background:#fff; border:1px solid #eee; border-radius:12px; overflow:hidden; }
.tournament-table thead th { text-align:left; font-size:.86rem; letter-spacing:.01em; color:#333; background:#f7f7f7; padding:12px 14px; border-bottom:1px solid #eee; white-space:nowrap; }
.tournament-table tbody td { padding:12px 14px; border-bottom:1px solid #f0f0f0; font-size:.9rem; color:#555; vertical-align:middle; }
.tournament-table tbody tr:last-child td { border-bottom:none; }
.tournament-name { font-weight:700; color:#333; }
.tournament-sub { display:block; font-size:.8rem; color:#777; margin-top:2px; }
.table-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
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
    <?php $playerActivePage = 'tournaments'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

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
                <div class="tournament-table-wrap">
                    <table class="tournament-table">
                        <thead>
                            <tr>
                                <th>Tournament</th>
                                <th>Date</th>
                                <th>Age Group</th>
                                
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Your Eligibility</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tournamentRows">
                            <?php foreach ($data['tournaments'] as $t): ?>
                                <?php $myReq = $data['my_requests'][$t->TournamentID] ?? null; ?>
                                <?php $eligibility = $data['eligibility'][$t->TournamentID] ?? ['eligible' => true, 'message' => '']; ?>
                                <tr class="tournament-row" data-age-group="<?php echo htmlspecialchars($t->AgeGroup ?? ''); ?>" data-status="<?php echo htmlspecialchars($t->Status ?? ''); ?>">
                                    <td>
                                        <span class="tournament-name"><?php echo htmlspecialchars($t->Name); ?></span>
                                        <span class="tournament-sub">Format: <?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></span>
                                    </td>
                                    <td><?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></td>
                                    <td><?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></td>
                                    <td><?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($t->Status ?? ''))); ?></td>
                                    <td><?php echo $eligibility['eligible'] ? 'Eligible' : 'Not eligible'; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?php echo URLROOT; ?>/player/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-secondary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php if ($myReq): ?>
                                                <span class="status-pill pill-applied">
                                                    <i class="fas fa-check-circle"></i> Applied
                                                </span>
                                            <?php elseif (($t->Status ?? '') === 'registration_open' && !$eligibility['eligible']): ?>
                                                <span class="status-pill pill-ineligible">
                                                    <i class="fas fa-ban"></i> Not Eligible
                                                </span>
                                            <?php elseif (($t->Status ?? '') === 'registration_open'): ?>
                                                <a href="<?php echo URLROOT; ?>/player/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-primary">
                                                    <i class="fas fa-paper-plane"></i> Apply
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <tr id="noResultsRow" style="display:none;">
                                <td colspan="8">
                                    <div class="empty-state" style="padding:40px 20px;">
                                        <i class="fas fa-search"></i>
                                        <h3>No Tournaments Found</h3>
                                        <p>Try adjusting your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const ageGroupFilter = document.getElementById('ageGroupFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('.tournament-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowAgeGroup = (row.getAttribute('data-age-group') || '').toLowerCase();
        const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
        
        const matchesAgeGroup = !ageGroupFilter || rowAgeGroup === ageGroupFilter;
        const matchesStatus = !statusFilter || rowStatus === statusFilter;
        
        if (matchesAgeGroup && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noResultsRow = document.getElementById('noResultsRow');
    if (noResultsRow) {
        noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
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
