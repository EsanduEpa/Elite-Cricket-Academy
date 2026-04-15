<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<style>
.tournament-status { display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; text-transform:uppercase; }
.status-created{background:#e2e8f0;color:#475569;} .status-registration_open{background:#dcfce7;color:#166534;}
.status-registration_closed{background:#fef9c3;color:#854d0e;} .status-team_announced{background:#dbeafe;color:#1e40af;}
.status-ongoing{background:#fde68a;color:#92400e;} .status-completed{background:#d1fae5;color:#065f46;} .status-cancelled{background:#fee2e2;color:#991b1b;}
.panel-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); margin-bottom:16px; overflow:hidden; }
.panel-hdr { padding:14px 18px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.panel-hdr h3 { margin:0; font-size:14px; color:#1e293b; font-weight:700; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table th { padding:9px 14px; text-align:left; color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-weight:600; font-size:12px; }
.data-table td { padding:9px 14px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.badge-pending{background:#fef3c7;color:#92400e;padding:2px 7px;border-radius:8px;font-size:11px;font-weight:700;}
.badge-approved{background:#dcfce7;color:#166534;padding:2px 7px;border-radius:8px;font-size:11px;font-weight:700;}
.badge-rejected{background:#fee2e2;color:#991b1b;padding:2px 7px;border-radius:8px;font-size:11px;font-weight:700;}
.badge-confirmed{background:#dbeafe;color:#1e40af;padding:2px 7px;border-radius:8px;font-size:11px;font-weight:700;}
</style>

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
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link"><i class="fas fa-star"></i><span>Recommendations</span></a></li>
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
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($t->Name); ?></h1>
                <p>
                    <span class="tournament-status status-<?php echo $t->Status; ?>"><?php echo str_replace('_',' ',$t->Status); ?></span>
                    &nbsp; <?php echo htmlspecialchars($t->Format ?? ''); ?> &nbsp;·&nbsp; <?php echo htmlspecialchars($t->AgeGroup ?? ''); ?> &nbsp;·&nbsp; <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : ''; ?>
                </p>
            </div>
            <div style="display:flex;gap:8px;padding:0 20px;">
                <a href="<?php echo URLROOT; ?>/coach/tournaments" style="background:#64748b;color:#fff;padding:8px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-arrow-left"></i> Back</a>
                <?php if ($data['is_head_coach'] && in_array($t->Status, ['registration_open','registration_closed'])): ?>
                    <a href="<?php echo URLROOT; ?>/coach/finalize_team/<?php echo $t->TournamentID; ?>" style="background:#16a34a;color:#fff;padding:8px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-users-cog"></i> Finalize Squad</a>
                <?php endif; ?>
                <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" style="background:#3b82f6;color:#fff;padding:8px 16px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"><i class="fas fa-star"></i> Recommend Players</a>
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
                        <h3><i class="fas fa-star" style="color:#f59e0b;"></i> My Recommendations for this Tournament</h3>
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
                        <thead><tr><th>Player</th><th>Status</th><th>Coach Recs</th><th>Trainer Recs</th></tr></thead>
                        <tbody>
                        <?php foreach ($data['join_requests'] as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r->Name); ?></td>
                            <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
                            <td><?php echo $r->CoachRecs; ?></td>
                            <td><?php echo $r->TrainerRecs; ?></td>
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
