<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournament-pages.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach/tournaments-index.css">

<div class="coach-layout">
    <?php $activeCoachNav = 'tournaments'; require APPROOT . '/views/inc/components/coach_sidebar.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>View tournaments and manage squad selection<?php echo $data['is_head_coach'] ? ' as Head Coach' : ''; ?></p>
                </div>
                <div class="header-actions">
                    <a
                        href="<?php echo URLROOT; ?>/coach/tournament-recommendations"
                        id="coachTournamentRecommendationsBtn"
                        class="page-action-btn"
                        aria-label="Open tournament recommendations"
                    >
                        <i class="fas fa-star"></i>
                        Recommendations
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#d1fae5;color:#065f46;"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <?php if (!empty($data['tournaments'])): ?>
        <div class="filter-section">
            <div class="filter-group">
                <label for="coachAgeGroupFilter">Age Group:</label>
                <select id="coachAgeGroupFilter">
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
                <label for="coachStatusFilter">Status:</label>
                <select id="coachStatusFilter">
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
            <button class="btn-reset-filter" onclick="resetCoachFilters()"><i class="fas fa-redo"></i> Reset</button>
        </div>
        <?php endif; ?>

        <div style="padding:20px;">
            <?php if (empty($data['tournaments'])): ?>
                <div style="text-align:center;padding:60px;color:#94a3b8;">
                    <i class="fas fa-trophy" style="font-size:48px;margin-bottom:16px;display:block;opacity:0.3;"></i>
                    <p>No tournaments available yet.</p>
                </div>
            <?php else: ?>
                <div id="coachTournamentCards">
                    <?php foreach ($data['tournaments'] as $t): ?>
                    <div class="t-card <?php echo $t->Status; ?>" data-age-group="<?php echo htmlspecialchars($t->AgeGroup ?? ''); ?>" data-status="<?php echo htmlspecialchars($t->Status ?? ''); ?>">
                    <div>
                        <div style="font-size:17px;font-weight:700;color:#1e293b;margin-bottom:4px;"><?php echo htmlspecialchars($t->Name); ?></div>
                        <div style="font-size:13px;color:#64748b;margin-bottom:8px;">
                            <?php echo htmlspecialchars($t->Format ?? ''); ?> &nbsp;·&nbsp;
                            <?php echo htmlspecialchars($t->AgeGroup ?? ''); ?> &nbsp;·&nbsp;
                            <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : ''; ?>
                            <?php if ($t->Location): ?> &nbsp;·&nbsp; <?php echo htmlspecialchars($t->Location); ?><?php endif; ?>
                        </div>
                        <span class="tournament-status status-<?php echo $t->Status; ?>"><?php echo str_replace('_',' ',$t->Status); ?></span>
                        <?php if ($t->IsTeamAnnounced): ?>&nbsp;<span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;"><i class="fas fa-bullhorn"></i> Team Announced</span><?php endif; ?>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                        <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" style="background:#3b82f6;color:#fff;padding:7px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-eye"></i> View</a>
                        <?php if ($data['is_head_coach'] && ($t->Status ?? '') === 'registration_closed'): ?>
                            <a href="<?php echo URLROOT; ?>/coach/finalize_team/<?php echo $t->TournamentID; ?>" style="background:#16a34a;color:#fff;padding:7px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-users"></i> Finalize Squad</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/tournaments-index.js"></script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
