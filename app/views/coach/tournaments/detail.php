<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournament-pages.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">

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
        <div class="dashboard-header" style="display:flex;justify-content:space-between;gap:16px;align-items:center;">
            <div class="header-content" style="flex:1;">
                <div>
                    <h1><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($t->Name); ?></h1>
                    <p>
                        <span class="tournament-status status-<?php echo $t->Status; ?>"><?php echo str_replace('_',' ',$t->Status); ?></span>
                        &nbsp; <?php echo htmlspecialchars($t->Format ?? ''); ?> &nbsp;·&nbsp; <?php echo htmlspecialchars($t->AgeGroup ?? ''); ?> &nbsp;·&nbsp; <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : ''; ?>
                    </p>
                </div>
            </div>
            <div class="header-actions" style="position:relative;z-index:2;display:flex;gap:8px;flex-shrink:0;">
                <a href="<?php echo URLROOT; ?>/coach/tournaments" id="coachTournamentBackBtn" class="page-action-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Tournaments
                </a>
                <?php if ($data['is_head_coach'] && in_array($t->Status, ['registration_open','registration_closed'])): ?>
                    <a href="<?php echo URLROOT; ?>/coach/finalize_team/<?php echo $t->TournamentID; ?>" class="page-action-btn" style="color:#16a34a;">
                        <i class="fas fa-users-cog"></i>
                        Finalize Squad
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#d1fae5;color:#065f46;"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div style="padding:20px;display:grid;grid-template-columns:280px 1fr;gap:16px;align-items:start;">

            <!-- Info panel -->
            <div>
                <div class="panel-card" style="padding:18px;">
                    <?php foreach ([
                        'Location'             => $t->Location ?? '—',
                        'Reg. Deadline'        => $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : '—',
                        'Max Players'          => $t->MaxPlayers ?? '—',
                        'Prize Pool'           => 'Rs. ' . number_format($t->PrizePool ?? 0, 2),
                    ] as $label => $val): ?>
                    <div style="margin-bottom:12px;">
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px;"><?php echo $label; ?></div>
                        <div style="font-size:14px;color:#374151;"><?php echo htmlspecialchars((string)$val); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($t->IsTeamAnnounced): ?>
                        <div style="margin-top:8px;padding:8px 12px;background:#dbeafe;border-radius:7px;color:#1e40af;font-size:13px;font-weight:700;"><i class="fas fa-bullhorn"></i> Team has been announced</div>
                    <?php endif; ?>
                </div>

                <?php if ($data['is_head_coach'] && !in_array($t->Status, ['completed', 'cancelled'], true)): ?>
                <div class="panel-card" style="padding:18px;">
                    <div style="font-weight:700;margin-bottom:12px;color:#374151;">Advance Status</div>
                    <?php if (!empty($data['status_options'])): ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/coach/update_tournament_status/<?php echo $t->TournamentID; ?>">
                            <select name="status" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box;background:#fff;color:#374151;margin-bottom:8px;">
                                <option value="">Select status</option>
                                <?php foreach ($data['status_options'] as $statusOption): ?>
                                    <option value="<?php echo htmlspecialchars($statusOption); ?>"><?php echo ucwords(str_replace('_', ' ', $statusOption)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div style="font-size:12px;color:#64748b;margin-bottom:8px;">Only forward status changes are available from the current state.</div>
                            <button type="submit" style="width:100%;padding:10px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                                Apply Status
                            </button>
                        </form>
                    <?php else: ?>
                        <div style="font-size:13px;color:#64748b;">No further status changes are available right now.</div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Result block -->
                <?php if ($data['result']): ?>
                <div class="panel-card" style="padding:18px;">
                    <?php $res = $data['result']; ?>
                        <div style="font-weight:800;font-size:15px;color:#065f46;margin-bottom:10px;"><i class="fas fa-medal"></i> Result</div>
                        <div style="margin-bottom:8px;"><div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Position</div><div style="font-size:20px;font-weight:900;color:#1e293b;"><?php echo htmlspecialchars($res->Position); ?></div></div>
                        <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Matches / Wins / Losses</div><div><?php echo htmlspecialchars((string)($res->TotalMatchesPlayed ?? 0)); ?> / <?php echo htmlspecialchars((string)($res->TotalWins ?? 0)); ?> / <?php echo htmlspecialchars((string)($res->TotalLosses ?? 0)); ?></div></div>
                        <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Best Batsman / Bowler</div><div><?php echo htmlspecialchars($res->BestBatsmanName ?? '—'); ?> / <?php echo htmlspecialchars($res->BestBowlerName ?? '—'); ?></div></div>
                        <?php if ($res->ManName): ?><div style="grid-column:1/-1;margin-top:8px;font-size:13px;"><strong>Man of Tournament:</strong> <?php echo htmlspecialchars($res->ManName); ?></div><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right panels -->
            <div>
                <!-- My recommendations -->
                <div class="panel-card">
                    <div class="panel-hdr">
                        <h3><i class="fas fa-star" style="color:#f59e0b;"></i> Recommendations for this Tournament</h3>
                        <a href="<?php echo URLROOT; ?>/coach/recommend_players/<?php echo $t->TournamentID; ?>" style="background:#3b82f6;color:#fff;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">+ Add</a>
                    </div>
                    <?php if (empty($data['my_recs'])): ?>
                        <p style="padding:16px;color:#94a3b8;text-align:center;font-size:13px;">No recommendations yet for this tournament.</p>
                    <?php else: ?>
                    <table class="data-table">
                        <thead><tr><th>Player</th><th>Role</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($data['my_recs'] as $r): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($r->PlayerName ?? ''); ?></strong></td>
                            <td><?php echo htmlspecialchars($r->RecommendedRole ?? '—'); ?></td>
                            <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- Join requests summary -->
                <div class="panel-card">
                    <div class="panel-hdr"><h3><i class="fas fa-hand-paper"></i> Player Join Requests (<?php echo count($data['join_requests']); ?>)</h3></div>
                    <?php if (empty($data['join_requests'])): ?>
                        <p style="padding:16px;color:#94a3b8;text-align:center;font-size:13px;">No join requests yet.</p>
                    <?php else: ?>
                    <table class="data-table">
                        <thead><tr><th>Player</th><th>Status</th><th>Coach Recs</th><th>Trainer Recs</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php foreach ($data['join_requests'] as $r): ?>
                        <?php $hasCoachRec = (int)($r->CoachRecs ?? 0) > 0; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r->Name); ?></td>
                            <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
                            <td><?php echo $r->CoachRecs; ?></td>
                            <td><?php echo $r->TrainerRecs; ?></td>
                            <td>
                                <?php if ($hasCoachRec): ?>
                                    <span class="page-action-btn page-action-btn--disabled" aria-disabled="true" title="Coach recommendation already exists">Recommended</span>
                                <?php else: ?>
                                    <a href="<?php echo URLROOT; ?>/coach/recommend_players/<?php echo $t->TournamentID; ?>?playerId=<?php echo (int)$r->PlayerID; ?>" class="page-action-btn">
                                        <i class="fas fa-plus"></i> Add
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- Team -->
                <div class="panel-card">
                    <div class="panel-hdr"><h3><i class="fas fa-users"></i> Current Squad (<?php echo count($data['team']); ?>)</h3></div>
                    <?php if (empty($data['team'])): ?>
                        <p style="padding:16px;color:#94a3b8;text-align:center;font-size:13px;">
                            <?php echo $data['is_head_coach'] ? 'No squad selected yet. Use "Finalize Squad" to build it.' : 'Squad not yet selected.'; ?>
                        </p>
                    <?php else: ?>
                    <table class="data-table">
                        <thead><tr><th>Player</th><th>Role</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($data['team'] as $p): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p->Name); ?></strong></td>
                            <td><?php echo htmlspecialchars($p->RoleInTeam ?? '—'); ?></td>
                            <td><?php echo $p->SelectionStatus === 'confirmed' ? '<span class="badge-confirmed">Confirmed</span>' : '<span style="background:#fef3c7;color:#92400e;padding:2px 7px;border-radius:8px;font-size:11px;font-weight:700;">Draft</span>'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- Matches -->
                <div class="panel-card">
                    <div class="panel-hdr" style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
                        <h3><i class="fas fa-cricket"></i> Matches (<?php echo count($data['matches'] ?? []); ?>)</h3>
                        <?php if (!empty($data['is_head_coach'])): ?>
                            <button type="button" class="page-action-btn" onclick="openMatchModal()" style="background:#3b82f6;color:#fff;border:0;cursor:pointer;">
                                <i class="fas fa-plus"></i> Add Match
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($data['matches'])): ?>
                        <p style="padding:16px;color:#94a3b8;text-align:center;font-size:13px;">No matches added for this tournament yet.</p>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Match</th>
                                    <th>Opponent</th>
                                    <th>Result</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['matches'] as $m): ?>
                                    <tr>
                                        <td><?php echo !empty($m->Date) ? date('d M Y', strtotime($m->Date)) : '—'; ?></td>
                                        <td><strong><?php echo htmlspecialchars($m->Name ?? 'Match'); ?></strong></td>
                                        <td><?php echo htmlspecialchars($m->OpponentTeam ?? '—'); ?></td>
                                        <td><span class="badge-<?php echo htmlspecialchars($m->Result ?? 'pending'); ?>"><?php echo strtoupper((string)($m->Result ?? 'pending')); ?></span></td>
                                        <td>
                                            <?php
                                                $our = ($m->OurRuns !== null || $m->OurWickets !== null) ? ((int)($m->OurRuns ?? 0) . '/' . (int)($m->OurWickets ?? 0)) : '—';
                                                $opp = ($m->OpponentRuns !== null || $m->OpponentWickets !== null) ? ((int)($m->OpponentRuns ?? 0) . '/' . (int)($m->OpponentWickets ?? 0)) : '—';
                                                echo htmlspecialchars($our . ' vs ' . $opp);
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <?php if (!empty($data['is_head_coach'])): ?>
                <!-- Head coach review: coach recommendations -->
                <div class="panel-card">
                    <div class="panel-hdr"><h3><i class="fas fa-user-tie"></i> Coach Recommendations (<?php echo count($data['coach_recs'] ?? []); ?>)</h3></div>
                    <?php if (empty($data['coach_recs'])): ?>
                        <p style="padding:16px;color:#94a3b8;text-align:center;font-size:13px;">No coach recommendations yet.</p>
                    <?php else: ?>
                    <table class="data-table">
                        <thead><tr><th>Coach</th><th>Player</th><th>Role</th><th>Reason</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php foreach (($data['coach_recs'] ?? []) as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r->CoachName ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($r->PlayerName ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($r->RecommendedRole ?? '—'); ?></td>
                                <td style="max-width:220px;"><small><?php echo htmlspecialchars($r->Reason ?? '—'); ?></small></td>
                                <td><span class="badge-<?php echo htmlspecialchars($r->Status ?? 'pending'); ?>"><?php echo strtoupper((string)($r->Status ?? 'pending')); ?></span></td>
                                <td>
                                    <?php if (strcasecmp((string)($r->Status ?? ''), 'pending') === 0): ?>
                                        <form method="POST" action="<?php echo URLROOT; ?>/coach/approve_coach_recommendation/<?php echo (int)$t->TournamentID; ?>/<?php echo (int)($r->RecommendationID ?? 0); ?>" style="display:inline;">
                                            <button type="submit" class="page-action-btn" style="background:#16a34a;color:#fff;border:0;cursor:pointer;">Approve</button>
                                        </form>
                                        <form method="POST" action="<?php echo URLROOT; ?>/coach/reject_coach_recommendation/<?php echo (int)$t->TournamentID; ?>/<?php echo (int)($r->RecommendationID ?? 0); ?>" style="display:inline;">
                                            <button type="submit" class="page-action-btn" style="background:#ef4444;color:#fff;border:0;cursor:pointer;">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#94a3b8;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- Head coach review: trainer recommendations -->
                <div class="panel-card">
                    <div class="panel-hdr"><h3><i class="fas fa-dumbbell"></i> Trainer Recommendations (<?php echo count($data['trainer_recs'] ?? []); ?>)</h3></div>
                    <?php if (empty($data['trainer_recs'])): ?>
                        <p style="padding:16px;color:#94a3b8;text-align:center;font-size:13px;">No trainer recommendations yet.</p>
                    <?php else: ?>
                    <table class="data-table">
                        <thead><tr><th>Trainer</th><th>Player</th><th>Fitness</th><th>Comments</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php foreach (($data['trainer_recs'] ?? []) as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r->TrainerName ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($r->PlayerName ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($r->FitnessRecommended ?? '—'); ?></td>
                                <td style="max-width:220px;"><small><?php echo htmlspecialchars($r->Comments ?? '—'); ?></small></td>
                                <td><span class="badge-<?php echo htmlspecialchars($r->Status ?? 'pending'); ?>"><?php echo strtoupper((string)($r->Status ?? 'pending')); ?></span></td>
                                <td>
                                    <?php if (strcasecmp((string)($r->Status ?? ''), 'pending') === 0): ?>
                                        <form method="POST" action="<?php echo URLROOT; ?>/coach/approve_trainer_recommendation/<?php echo (int)$t->TournamentID; ?>/<?php echo (int)($r->RecommendationID ?? 0); ?>" style="display:inline;">
                                            <button type="submit" class="page-action-btn" style="background:#16a34a;color:#fff;border:0;cursor:pointer;">Approve</button>
                                        </form>
                                        <form method="POST" action="<?php echo URLROOT; ?>/coach/reject_trainer_recommendation/<?php echo (int)$t->TournamentID; ?>/<?php echo (int)($r->RecommendationID ?? 0); ?>" style="display:inline;">
                                            <button type="submit" class="page-action-btn" style="background:#ef4444;color:#fff;border:0;cursor:pointer;">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#94a3b8;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php if (!empty($data['is_head_coach'])): ?>
    <div class="modal" id="matchModal" style="display:none;">
        <div class="modal-content" style="max-width:720px;">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> Add Match</h3>
                <button class="close" type="button" onclick="closeMatchModal()" aria-label="Close add match modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo URLROOT; ?>/coach/add_match/<?php echo (int)$t->TournamentID; ?>">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Match Name</label>
                            <input name="name" type="text" required placeholder="e.g. League Match 1" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>

                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Date</label>
                            <input name="match_date" type="date" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Venue</label>
                            <input name="venue" type="text" placeholder="e.g. Main Ground" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>

                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Opponent Team</label>
                            <input name="opponent_team" type="text" required placeholder="e.g. Thunder Warriors" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>

                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Result</label>
                            <select name="result" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;">
                                <?php foreach (['pending','win','loss','tie','draw','no-result','abandoned'] as $opt): ?>
                                    <option value="<?php echo $opt; ?>"><?php echo strtoupper(str_replace('-', ' ', $opt)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">DLS</label>
                            <label style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;">
                                <input type="checkbox" name="is_dls" value="1" />
                                <span style="font-size:13px;color:#374151;">Applied</span>
                            </label>
                        </div>

                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Margin Value</label>
                            <input name="margin_value" type="number" min="0" placeholder="e.g. 5" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Margin Type</label>
                            <select name="margin_type" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;">
                                <option value="">—</option>
                                <?php foreach (['runs','wickets','super over','DLS','boundaries','forfeit'] as $opt): ?>
                                    <option value="<?php echo $opt; ?>"><?php echo strtoupper($opt); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Our Runs</label>
                            <input name="our_runs" type="number" min="0" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Our Wickets</label>
                            <input name="our_wickets" type="number" min="0" max="10" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>

                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Opponent Runs</label>
                            <input name="opponent_runs" type="number" min="0" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Opponent Wickets</label>
                            <input name="opponent_wickets" type="number" min="0" max="10" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;" />
                        </div>

                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Summary Notes</label>
                            <textarea name="summary_notes" rows="3" placeholder="Optional notes" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;resize:vertical;"></textarea>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:14px;">
                        <button type="button" class="page-action-btn" onclick="closeMatchModal()" style="background:#e2e8f0;color:#0f172a;border:0;cursor:pointer;">Cancel</button>
                        <button type="submit" class="page-action-btn" style="background:#16a34a;color:#fff;border:0;cursor:pointer;">Save Match</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openMatchModal() {
            var modal = document.getElementById('matchModal');
            if (!modal) return;

            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
        function closeMatchModal() {
            var modal = document.getElementById('matchModal');
            if (!modal) return;

            modal.classList.remove('show');
            document.body.classList.remove('modal-open');

            // Allow transition to play before fully hiding
            window.setTimeout(function() {
                modal.style.display = 'none';
            }, 320);
        }

        // Close when clicking outside modal content
        (function() {
            var modal = document.getElementById('matchModal');
            if (!modal) return;
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeMatchModal();
            });
        })();
    </script>
<?php endif; ?>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require APPROOT . '/views/inc/components/footer.php'; ?>
