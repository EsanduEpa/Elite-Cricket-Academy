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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players" class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health" class="nav-link"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link"><i class="fas fa-bell"></i><span>Notifications</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/events" class="nav-link"><i class="fas fa-calendar"></i><span>Events</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Coach'); ?></div>
            <div class="profile-role">Cricket Coach<?php echo $data['is_head_coach'] ? ' · Head Coach' : ''; ?></div>
            <a href="<?php echo URLROOT; ?>/coach/profile" class="action-btn" style="margin-top:10px;"><i class="fas fa-user-cog"></i> Profile</a>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:8px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
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
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
