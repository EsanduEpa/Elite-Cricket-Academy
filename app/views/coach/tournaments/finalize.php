<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournament-pages.css">

<div class="coach-layout">
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo"><i class="fas fa-chalkboard-teacher"></i><h3>Coach Panel</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-angle-left"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players"><i class="fas fa-users"></i><span>Players</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link" data-tooltip="Performance"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health &amp; Injury"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication"><i class="fas fa-comments"></i><span>Communication</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests"><i class="fas fa-clipboard-list"></i><span>Requests</span></a></li>
            </ul>
        </nav>
    </div>

    <main class="main-content" id="mainContent">
        <?php $t = $data['tournament']; ?>

        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-users-cog"></i> Finalize Squad — <?php echo htmlspecialchars($t->Name); ?></h1>
                <p>Select players and assign roles. Save as draft or confirm the final squad.</p>
            </div>
            <div class="header-actions" style="padding:0 20px;">
                <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" id="coachTournamentBackBtn" class="page-action-btn" style="background:#64748b;color:#fff;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php
        // Build lookup maps
        $teamMap = [];
        foreach ($data['team'] as $p) { $teamMap[$p->PlayerID] = $p; }

        // Build recommendation score map: playerID => [coachRecs, trainerRecs, suggestedRole]
        $recMap = [];
        foreach ($data['coach_recs'] as $r) {
            $pid = $r->PlayerID;
            if (!isset($recMap[$pid])) $recMap[$pid] = ['coach' => 0, 'trainer' => 0, 'role' => null];
            $recMap[$pid]['coach']++;
            if (!$recMap[$pid]['role']) {
                $captaincy = strtolower(trim((string)($r->Captaincy ?? '')));
                $wk = strtolower(trim((string)($r->WicketKeeper ?? '')));
                $role = strtolower(trim((string)($r->RecommendedRole ?? '')));

                if ($captaincy === 'captain') {
                    $recMap[$pid]['role'] = 'Captain';
                } elseif ($captaincy === 'vice captain') {
                    $recMap[$pid]['role'] = 'Vice-Captain';
                } elseif ($wk === 'yes') {
                    $recMap[$pid]['role'] = 'Wicket-Keeper';
                } elseif ($role === 'batsman') {
                    $recMap[$pid]['role'] = 'Batsman';
                } elseif ($role === 'bowler') {
                    $recMap[$pid]['role'] = 'Bowler';
                } elseif ($role === 'allrounder') {
                    $recMap[$pid]['role'] = 'All-Rounder';
                }
            }
        }
        foreach ($data['trainer_recs'] as $r) {
            $pid = $r->PlayerID;
            if (!isset($recMap[$pid])) $recMap[$pid] = ['coach' => 0, 'trainer' => 0, 'role' => null];
            $recMap[$pid]['trainer']++;
        }

        // Sort join_requests by total recommendation count descending
        $applicants = $data['join_requests'];
        usort($applicants, function($a, $b) use ($recMap) {
            $ra = ($recMap[$a->PlayerID]['coach'] ?? 0) + ($recMap[$a->PlayerID]['trainer'] ?? 0);
            $rb = ($recMap[$b->PlayerID]['coach'] ?? 0) + ($recMap[$b->PlayerID]['trainer'] ?? 0);
            return $rb - $ra;
        });

        // Track applicant IDs so we can show "recommended only" section below
        $applicantIds = [];
        foreach ($applicants as $r) { $applicantIds[$r->PlayerID] = true; }
        ?>

        <div style="padding:20px;">
        <form method="POST" action="<?php echo URLROOT; ?>/coach/save_team_selection/<?php echo $t->TournamentID; ?>">

            <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">
                <div>
                    <div class="panel-card">
                        <div class="panel-hdr" style="display:flex;justify-content:space-between;align-items:center;">
                            <h3><i class="fas fa-users"></i> Select Players</h3>
                            <span style="font-size:12px;color:#64748b;">Checking: <strong id="selectedCount">0</strong> selected<?php if ($t->MaxPlayers): ?> / max <?php echo $t->MaxPlayers; ?><?php endif; ?></span>
                        </div>
                        <div class="section-head">Applicants — sorted by recommendations</div>
                        <?php if (empty($applicants)): ?>
                            <p style="padding:12px 16px;color:#94a3b8;font-size:13px;">No players have applied yet.</p>
                        <?php else: ?>
                        <?php foreach ($applicants as $r):
                            $pid    = $r->PlayerID;
                            $inTeam = isset($teamMap[$pid]);
                            $rec    = $recMap[$pid] ?? ['coach'=>0,'trainer'=>0,'role'=>null];
                            $total  = $rec['coach'] + $rec['trainer'];
                        ?>
                        <div class="player-row <?php echo $inTeam ? 'in-team' : ''; ?>">
                            <input type="checkbox" name="selected[]" value="<?php echo $pid; ?>" id="p<?php echo $pid; ?>">
                            <label for="p<?php echo $pid; ?>" style="cursor:pointer;">
                                <span style="font-weight:600;font-size:14px;"><?php echo htmlspecialchars($r->Name); ?></span>
                                <?php if ($inTeam): ?><span class="already-label"> ✓ In squad</span><?php endif; ?>
                                <br>
                                <span class="recs">
                                    <?php if ($rec['coach']): ?><span class="rec-badge rec-coach"><i class="fas fa-user-tie"></i> <?php echo $rec['coach']; ?> coach rec</span>&nbsp;<?php endif; ?>
                                    <?php if ($rec['trainer']): ?><span class="rec-badge rec-trainer"><i class="fas fa-dumbbell"></i> <?php echo $rec['trainer']; ?> trainer rec</span>&nbsp;<?php endif; ?>
                                    <?php if (!$total): ?><span style="color:#cbd5e1;">No recommendations yet</span><?php endif; ?>
                                </span>
                            </label>
                            <select name="roles[<?php echo $pid; ?>]" style="<?php echo !$inTeam ? 'opacity:.5;' : ''; ?>">
                                <option value="">— Role —</option>
                                <?php foreach (['Captain','Vice-Captain','Wicket-Keeper','Batsman','Bowler','All-Rounder'] as $role): ?>
                                    <option value="<?php echo $role; ?>" <?php echo (($teamMap[$pid]->RoleInTeam ?? $rec['role']) === $role) ? 'selected' : ''; ?>><?php echo $role; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span style="font-size:22px;font-weight:800;color:<?php echo $total >= 2 ? '#16a34a' : ($total === 1 ? '#d97706' : '#e2e8f0'); ?>;text-align:center;"><?php echo $total ?: '—'; ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Recommended but have not applied -->
                        <?php
                        $recOnly = [];
                        foreach ($recMap as $pid => $rec) {
                            if (!isset($applicantIds[$pid])) $recOnly[$pid] = $rec;
                        }
                        ?>
                        <?php if (!empty($recOnly)): ?>
                        <div class="section-head">Recommended — have not applied</div>
                        <?php foreach ($recOnly as $pid => $rec):
                            // Get player name from coach_recs
                            $playerName = '';
                            foreach ($data['coach_recs'] as $cr) { if ($cr->PlayerID == $pid) { $playerName = $cr->PlayerName; break; } }
                            foreach ($data['trainer_recs'] as $tr) { if ($tr->PlayerID == $pid && !$playerName) { $playerName = $tr->PlayerName; break; } }
                            $inTeam = isset($teamMap[$pid]);
                        ?>
                        <div class="player-row <?php echo $inTeam ? 'in-team' : ''; ?>">
                            <input type="checkbox" name="selected[]" value="<?php echo $pid; ?>" id="p<?php echo $pid; ?>">
                            <label for="p<?php echo $pid; ?>" style="cursor:pointer;">
                                <span style="font-weight:600;font-size:14px;"><?php echo htmlspecialchars($playerName); ?></span>
                                <?php if ($inTeam): ?><span class="already-label"> ✓ In draft</span><?php endif; ?>
                                <br>
                                <span class="recs">
                                    <?php if ($rec['coach']): ?><span class="rec-badge rec-coach"><i class="fas fa-user-tie"></i> <?php echo $rec['coach']; ?> coach rec</span>&nbsp;<?php endif; ?>
                                    <?php if ($rec['trainer']): ?><span class="rec-badge rec-trainer"><i class="fas fa-dumbbell"></i> <?php echo $rec['trainer']; ?> trainer rec</span><?php endif; ?>
                                </span>
                            </label>
                            <select name="roles[<?php echo $pid; ?>]">
                                <option value="">— Role —</option>
                                <?php foreach (['Captain','Vice-Captain','Wicket-Keeper','Batsman','Bowler','All-Rounder'] as $role): ?>
                                    <option value="<?php echo $role; ?>" <?php echo (($teamMap[$pid]->RoleInTeam ?? $rec['role']) === $role) ? 'selected' : ''; ?>><?php echo $role; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span style="font-size:22px;font-weight:800;color:#94a3b8;text-align:center;"><?php echo ($rec['coach']+$rec['trainer']) ?: '—'; ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right: actions + current draft summary -->
                <div>
                    <div class="panel-card" style="padding:18px;">
                        <div style="font-weight:700;font-size:14px;color:#1e293b;margin-bottom:14px;"><i class="fas fa-save"></i> Save Options</div>
                        <button type="submit" name="action" value="draft" style="width:100%;padding:10px;background:#f59e0b;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;margin-bottom:8px;font-size:14px;">
                            <i class="fas fa-pencil-alt"></i> Save as Draft
                        </button>
                        <button type="submit" name="confirm" value="1" data-confirm-message="Confirm the squad? This will lock all selections as confirmed." style="width:100%;padding:10px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:14px;">
                            <i class="fas fa-check-circle"></i> Confirm Squad
                        </button>
                        <p style="font-size:12px;color:#94a3b8;margin-top:10px;">Confirming will lock selections. The admin can then publish the team announcement.</p>
                    </div>

                    <?php if (!empty($data['team'])): ?>
                    <div class="panel-card" style="padding:18px;">
                        <div style="font-weight:700;font-size:13px;color:#1e293b;margin-bottom:10px;">Current Draft (<?php echo count($data['team']); ?>)</div>
                        <?php foreach ($data['team'] as $p): ?>
                        <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                            <span><?php echo htmlspecialchars($p->Name); ?></span>
                            <span style="color:#64748b;font-size:12px;"><?php echo $p->RoleInTeam ?? '—'; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach-tournament-pages.js"></script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
