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
        <div class="dashboard-header recommendations-page-header">
            <div class="header-content">
                <h1><i class="fas fa-user-plus"></i> Recommend a Player</h1>
                <p>Recommend applicants for <strong><?php echo htmlspecialchars($t->Name); ?></strong></p>
            </div>
            <div class="header-actions" style="padding:0 20px;">
                <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" id="coachTournamentBackBtn" class="page-action-btn">
                    <i class="fas fa-arrow-left"></i> Back to Tournament
                </a>
            </div>
        </div>

        <div class="recommendations-shell">

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="tournament-info">
                <h4><?php echo htmlspecialchars($t->Name); ?></h4>
                <p>
                    <?php echo htmlspecialchars($t->Format ?? 'N/A'); ?> &middot;
                    <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?> &middot;
                    <?php echo htmlspecialchars($t->Location ?? ''); ?>
                </p>
            </div>

            <?php if (empty($data['players'])): ?>
                <div class="form-card" style="text-align:center;color:#94a3b8;">
                    <i class="fas fa-users" style="font-size:2rem;margin-bottom:10px;"></i>
                    <p>No players have applied for this tournament yet.</p>
                </div>
            <?php else: ?>

            <!-- Build a quick lookup of already-recommended player IDs -->
            <?php
            $alreadyRecommended = [];
            foreach ($data['my_recs'] as $r) {
                $alreadyRecommended[$r->PlayerID] = $r;
            }
            ?>

            <div class="form-card">
                <h3 style="margin:0 0 18px;font-size:15px;color:#1e293b;"><i class="fas fa-paper-plane" style="color:#3b82f6;"></i> Submit a Recommendation</h3>

                <form method="POST" action="<?php echo URLROOT; ?>/coach/save_recommendation">
                    <input type="hidden" name="tournamentId" value="<?php echo $t->TournamentID; ?>">



                    
                    <div class="form-group">
                        <label for="playerId">Select Player <span style="color:red">*</span></label>
                        <select name="playerId" id="playerId" required>
                            <option value="">-- Choose a player who applied --</option>
                            <?php foreach ($data['players'] as $p): ?>
                                <?php $alreadyRec = $alreadyRecommended[$p->PlayerID] ?? null; ?>
                                <?php
                                    $alreadyRecRole = 'Recommended';
                                    if ($alreadyRec && isset($alreadyRec->RecommendedRole)) {
                                        $alreadyRecRole = trim((string)$alreadyRec->RecommendedRole);
                                        if ($alreadyRecRole === '' || strtolower($alreadyRecRole) === 'null') {
                                            $alreadyRecRole = 'Recommended';
                                        }
                                    }
                                ?>
                                <option value="<?php echo $p->PlayerID; ?>" <?php echo $alreadyRec ? 'disabled' : ''; ?> <?php echo ((int)($data['selected_player_id'] ?? 0) === (int)$p->PlayerID) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p->Name); ?>
                                    <?php if ($alreadyRec): ?>
                                        — already recommended as <?php echo htmlspecialchars($alreadyRecRole); ?>
                                    <?php else: ?>
                                        (<?php echo $p->CoachRecs + $p->TrainerRecs; ?> rec<?php echo ($p->CoachRecs + $p->TrainerRecs) != 1 ? 's' : ''; ?> so far)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recommendedRole">Recommended Role <span style="color:red">*</span></label>
                        <select name="recommendedRole" id="recommendedRole" required>
                            <option value="">-- Select role --</option>
                            <option value="Batsman">Batsman</option>
                            <option value="Bowler">Bowler</option>
                            <option value="All-rounder">All-rounder</option>
                            <option value="Wicket Keeper">Wicket Keeper</option>
                            <option value="Captain">Captain</option>
                            <option value="Vice Captain">Vice Captain</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason for Recommendation <span style="color:red">*</span></label>
                        <textarea name="reason" id="reason" required placeholder="Why are you recommending this player for this tournament?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="comments">Additional Comments</label>
                        <textarea name="comments" id="comments" style="min-height:70px;" placeholder="Optional additional notes..."></textarea>
                    </div>

                    <div style="display:flex;gap:12px;margin-top:4px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Submit Recommendation</button>
                        <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <!-- Applicants overview -->
            <div class="form-card">
                <h3 style="margin:0 0 14px;font-size:15px;color:#1e293b;"><i class="fas fa-clipboard-list" style="color:#64748b;"></i> All Applicants</h3>
                <table class="recs-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Coach Recs</th>
                            <th>Trainer Recs</th>
                            <th>Performance</th>
                            <th>Details</th>
                            <th>My Rec</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['players'] as $p): ?>
                        <?php $myRec = $alreadyRecommended[$p->PlayerID] ?? null; ?>
                        <?php
                            $myRecRole = 'Recommended';
                            if ($myRec && isset($myRec->RecommendedRole)) {
                                $myRecRole = trim((string)$myRec->RecommendedRole);
                                if ($myRecRole === '' || strtolower($myRecRole) === 'null') {
                                    $myRecRole = 'Recommended';
                                }
                            }
                        ?>
                        <?php $perf = $p->PerformanceSummary ?? ['overall' => null, 'latest_match' => null]; ?>
                        <?php $overall = $perf['overall'] ?? null; ?>
                        <?php $latestMatch = $perf['latest_match'] ?? null; ?>
                        <?php $matches = $p->PerformanceMatches ?? []; ?>
                        <?php $detailsRowId = 'performance-details-' . (int)$p->PlayerID; ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p->Name); ?></strong><br><span style="font-size:11px;color:#94a3b8;"><?php echo htmlspecialchars($p->Email ?? ''); ?></span></td>
                            <td><?php echo $p->CoachRecs; ?></td>
                            <td><?php echo $p->TrainerRecs; ?></td>
                            <td>
                                <div class="player-performance-summary">
                                    <div class="player-performance-grid">
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">Matches</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? (int)($overall->MatchesPlayed ?? 0) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">Runs</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? (int)($overall->TotalRuns ?? 0) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">Bat Avg</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? htmlspecialchars(number_format((float)($overall->BattingAverage ?? 0), 2)) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">Wkts</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? (int)($overall->TotalWickets ?? 0) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">Bowl Avg</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? htmlspecialchars(number_format((float)($overall->BowlingAverage ?? 0), 2)) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">SR</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? htmlspecialchars(number_format((float)($overall->StrikeRate ?? 0), 2)) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">HS</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? htmlspecialchars((string)($overall->HighestScore ?? 0)) : '—'; ?></span>
                                        </div>
                                        <div class="player-performance-stat">
                                            <span class="player-performance-stat__label">Eco</span>
                                            <span class="player-performance-stat__value"><?php echo $overall ? htmlspecialchars(number_format((float)($overall->EconomyRate ?? 0), 2)) : '—'; ?></span>
                                        </div>
                                    </div>
                                    <div class="player-performance-note">
                                        <?php if ($latestMatch): ?>
                                            <span class="player-performance-tag<?php echo (($latestMatch->VerifiedStatus ?? '') !== 'verified') ? ' player-performance-tag--pending' : ''; ?>">
                                                <i class="fas fa-<?php echo (($latestMatch->VerifiedStatus ?? '') === 'verified') ? 'check-circle' : 'clock'; ?>"></i>
                                                <?php echo (($latestMatch->VerifiedStatus ?? '') === 'verified') ? 'Verified' : 'Pending'; ?>
                                            </span>
                                            <div style="margin-top:6px;">
                                                Latest: <?php echo !empty($latestMatch->Date) ? date('d M Y', strtotime($latestMatch->Date)) : 'N/A'; ?> ·
                                                <?php echo htmlspecialchars($latestMatch->OpponentTeam ?? 'Match'); ?> ·
                                                R <?php echo (int)($latestMatch->RunsScored ?? 0); ?> / W <?php echo (int)($latestMatch->WicketsTaken ?? 0); ?>
                                            </div>
                                        <?php else: ?>
                                            No match performance recorded yet.
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="performance-toggle-cell">
                                <button
                                    type="button"
                                    class="performance-toggle-btn"
                                    data-performance-toggle="<?php echo $detailsRowId; ?>"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-chart-bar"></i>
                                    Details
                                </button>
                            </td>
                            <td>
                                <?php if ($myRec): ?>
                                    <span class="rec-badge"><i class="fas fa-star"></i> <?php echo htmlspecialchars($myRecRole); ?></span>
                                <?php else: ?>
                                    <span style="color:#94a3b8;font-size:12px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr id="<?php echo $detailsRowId; ?>" class="performance-details-row" hidden>
                            <td colspan="6">
                                <div class="performance-details-panel">
                                    <div class="performance-details-panel__title">
                                        <h4><i class="fas fa-table"></i> Match-wise Performance</h4>
                                        <div class="performance-details-panel__summary">
                                            <?php echo $overall ? (int)($overall->MatchesPlayed ?? 0) : '—'; ?> matches ·
                                            <?php echo $overall ? (int)($overall->TotalRuns ?? 0) : '—'; ?> runs ·
                                            <?php echo $overall ? (int)($overall->TotalWickets ?? 0) : '—'; ?> wickets
                                        </div>
                                    </div>

                                    <?php if (!empty($matches)): ?>
                                        <table class="performance-match-table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Match</th>
                                                    <th>Runs</th>
                                                    <th>Balls</th>
                                                    <th>Wkts</th>
                                                    <th>Overs</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($matches as $match): ?>
                                                    <?php
                                                        $verifiedStatus = strtolower((string)($match->VerifiedStatus ?? 'pending'));
                                                        $statusClass = 'performance-status--other';
                                                        if ($verifiedStatus === 'verified') {
                                                            $statusClass = 'performance-status--verified';
                                                        } elseif ($verifiedStatus === 'pending') {
                                                            $statusClass = 'performance-status--pending';
                                                        } elseif ($verifiedStatus === 'rejected') {
                                                            $statusClass = 'performance-status--rejected';
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo !empty($match->Date) ? date('d M Y', strtotime($match->Date)) : 'N/A'; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($match->OpponentTeam ?? 'Match'); ?></strong><br>
                                                            <span style="color:#64748b;">Tourn.: <?php echo htmlspecialchars($match->TournamentName ?? 'N/A'); ?></span>
                                                        </td>
                                                        <td><?php echo (int)($match->RunsScored ?? 0); ?></td>
                                                        <td><?php echo (int)($match->BallsFaced ?? 0); ?></td>
                                                        <td><?php echo (int)($match->WicketsTaken ?? 0); ?></td>
                                                        <td><?php echo htmlspecialchars((string)($match->OversBowled ?? '0')); ?></td>
                                                        <td>
                                                            <span class="performance-status <?php echo $statusClass; ?>">
                                                                <i class="fas <?php echo $verifiedStatus === 'verified' ? 'fa-check-circle' : ($verifiedStatus === 'rejected' ? 'fa-times-circle' : 'fa-clock'); ?>"></i>
                                                                <?php echo ucfirst($verifiedStatus); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <div class="player-performance-note">No match-wise performance records available.</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach-tournament-pages.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
