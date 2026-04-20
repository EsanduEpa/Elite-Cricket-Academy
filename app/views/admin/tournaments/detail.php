<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.tournament-status { display:inline-block; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600; text-transform:uppercase; }
.status-created { background:#e2e8f0; color:#475569; }
.status-registration_open { background:#dcfce7; color:#166534; }
.status-registration_closed { background:#fef9c3; color:#854d0e; }
.status-team_announced { background:#dbeafe; color:#1e40af; }
.status-ongoing { background:#fde68a; color:#92400e; }
.status-completed { background:#d1fae5; color:#065f46; }
.status-cancelled { background:#fee2e2; color:#991b1b; }
.panel-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.1); margin-bottom:20px; overflow:hidden; }
.panel-header { padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.panel-header h3 { margin:0; font-size:15px; color:#1e293b; }
.panel-body { padding:0; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table th { padding:10px 16px; text-align:left; color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-weight:600; }
.data-table td { padding:10px 16px; border-bottom:1px solid #f8fafc; color:#374151; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.badge-pending { background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:8px; font-size:11px; font-weight:700; }
.badge-approved { background:#dcfce7; color:#166534; padding:2px 8px; border-radius:8px; font-size:11px; font-weight:700; }
.badge-rejected { background:#fee2e2; color:#991b1b; padding:2px 8px; font-size:11px; font-weight:700; border-radius:8px; }
.tab-btn { padding:8px 18px; border:none; border-radius:6px; background:#f1f5f9; color:#64748b; font-weight:600; cursor:pointer; font-size:13px; }
.tab-btn.active { background:#3b82f6; color:#fff; }
</style>

<div class="admin-layout">
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo"><i class="fas fa-user-shield"></i><h3>Admin Dashboard</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard </span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link"><i class="fas fa-users-cog"></i><span>Staff Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/players" class="nav-link"><i class="fas fa-user-graduate"></i><span>Player Management</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finances</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/admin/profile" class="profile-avatar" aria-label="Open admin profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <?php $t = $data['tournament']; ?>

        <!-- Header -->
        <div class="events-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($t->Name); ?></h1>
                    <p>
                        <span class="tournament-status status-<?php echo $t->Status; ?>"><?php echo str_replace('_',' ',$t->Status); ?></span>
                        &nbsp; <?php echo htmlspecialchars($t->AgeGroup ?? ''); ?> &nbsp;•&nbsp; <?php echo htmlspecialchars($t->Format ?? ''); ?> &nbsp;•&nbsp; <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : ''; ?>
                    </p>
                </div>
                <div class="header-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="<?php echo URLROOT; ?>/admin/tournaments" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    <?php if (!in_array($t->Status, ['ongoing','completed','cancelled'])): ?>
                        <a href="<?php echo URLROOT; ?>/admin/edit_tournament/<?php echo $t->TournamentID; ?>" class="btn btn-secondary"><i class="fas fa-edit"></i> Edit</a>
                    <?php endif; ?>
                    <?php if (in_array($t->Status, ['ongoing','completed'])): ?>
                        <a href="<?php echo URLROOT; ?>/admin/enter_results/<?php echo $t->TournamentID; ?>" class="btn btn-primary"><i class="fas fa-medal"></i> Results</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="padding:20px;display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">

            <!-- Left: tournament info + status controls -->
            <div>
                <div class="panel-card" style="padding:20px;">
                    <div style="margin-bottom:14px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Location</div>
                        <div><?php echo htmlspecialchars($t->Location ?? '—'); ?></div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Registration Deadline</div>
                        <div><?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : '—'; ?></div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Max Players</div>
                        <div><?php echo $t->MaxPlayers ?? '—'; ?></div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Prize Pool</div>
                        <div>Rs. <?php echo number_format($t->PrizePool ?? 0, 2); ?></div>
                    </div>
                    <?php if ($t->Description): ?>
                    <div style="margin-bottom:14px;">
                        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Description</div>
                        <div style="font-size:13px;color:#64748b;"><?php echo htmlspecialchars($t->Description); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Status Advance -->
                <?php if ($t->Status !== 'cancelled' && $t->Status !== 'completed'): ?>
                <div class="panel-card" style="padding:20px;">
                    <div style="font-weight:700;margin-bottom:12px;color:#374151;">Advance Status</div>
                    <?php if (!empty($data['status_options'])): ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/admin/update_tournament_status/<?php echo $t->TournamentID; ?>">
                            <select name="status" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box;background:#fff;color:#374151;margin-bottom:8px;">
                                <option value="">Select status</option>
                                <?php foreach ($data['status_options'] as $statusOption): ?>
                                    <option value="<?php echo $statusOption; ?>"><?php echo ucwords(str_replace('_', ' ', $statusOption)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div style="font-size:12px;color:#64748b;margin-bottom:8px;">The list includes every valid forward status from the current state.</div>
                            <button type="submit" style="width:100%;padding:10px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                                Apply Status
                            </button>
                        </form>
                    <?php else: ?>
                        <div style="font-size:13px;color:#64748b;">No further status changes are available right now.</div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo URLROOT; ?>/admin/cancel_tournament/<?php echo $t->TournamentID; ?>" style="margin-top:8px;" onsubmit="return confirm('Cancel this tournament? All pending join requests will be rejected.');">
                        <input type="text" name="cancel_reason" required placeholder="Cancellation reason..." style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;margin-bottom:6px;">
                        <button type="submit" style="width:100%;padding:8px;background:#ef4444;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px;">
                            Cancel Tournament
                        </button>
                    </form>
                </div>
                <?php endif; ?>
                <?php if ($t->Status === 'cancelled' && $t->CancelReason): ?>
                <div style="background:#fee2e2;border-radius:8px;padding:14px;color:#991b1b;font-size:13px;">
                    <strong>Cancelled:</strong> <?php echo htmlspecialchars($t->CancelReason); ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: tabbed panels -->
            <div>
                <!-- Tab buttons -->
                <div style="display:flex;gap:8px;margin-bottom:16px;">
                    <button class="tab-btn active" onclick="showTab('join_requests',this)">
                        Join Requests <span style="background:#ef4444;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;"><?php echo count(array_filter($data['join_requests'], fn($r) => $r->Status === 'pending')); ?></span>
                    </button>
                    <button class="tab-btn" onclick="showTab('coach_recs',this)">Coach Recs <span style="background:#64748b;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;"><?php echo count($data['coach_recs']); ?></span></button>
                    <button class="tab-btn" onclick="showTab('trainer_recs',this)">Trainer Recs <span style="background:#64748b;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;"><?php echo count($data['trainer_recs']); ?></span></button>
                    <button class="tab-btn" onclick="showTab('team',this)">Team <span style="background:#64748b;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;"><?php echo count($data['team']); ?></span></button>
                    <?php if ($data['result']): ?>
                        <button class="tab-btn" onclick="showTab('results',this)">Results</button>
                    <?php endif; ?>
                </div>

                <!-- Join Requests -->
                <div id="tab-join_requests" class="panel-card">
                    <div class="panel-header"><h3><i class="fas fa-hand-paper"></i> Player Join Requests</h3></div>
                    <div class="panel-body">
                        <?php if (empty($data['join_requests'])): ?>
                            <p style="padding:20px;color:#94a3b8;text-align:center;">No join requests yet.</p>
                        <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Player</th><th>Message</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php foreach ($data['join_requests'] as $r): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($r->Name); ?></strong><br><small style="color:#94a3b8;"><?php echo htmlspecialchars($r->Email ?? ''); ?></small></td>
                                <td style="max-width:220px;"><small><?php echo htmlspecialchars($r->Message ?? '—'); ?></small></td>
                                <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
                                <td>
                                    <?php if ($r->Status === 'pending'): ?>
                                    <button onclick="reviewRequest(<?php echo $r->RequestID; ?>,'approved')" style="background:#16a34a;color:#fff;border:none;border-radius:5px;padding:4px 10px;cursor:pointer;font-size:12px;margin-right:4px;">Approve</button>
                                    <button onclick="reviewRequest(<?php echo $r->RequestID; ?>,'rejected')" style="background:#ef4444;color:#fff;border:none;border-radius:5px;padding:4px 10px;cursor:pointer;font-size:12px;">Reject</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Coach Recommendations -->
                <div id="tab-coach_recs" class="panel-card" style="display:none;">
                    <div class="panel-header"><h3><i class="fas fa-user-tie"></i> Coach Recommendations</h3></div>
                    <div class="panel-body">
                        <?php if (empty($data['coach_recs'])): ?>
                            <p style="padding:20px;color:#94a3b8;text-align:center;">No coach recommendations yet.</p>
                        <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Coach</th><th>Player</th><th>Role</th><th>Reason</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($data['coach_recs'] as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r->CoachName ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($r->PlayerName ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($r->RecommendedRole ?? '—'); ?></td>
                                <td style="max-width:200px;"><small><?php echo htmlspecialchars($r->Reason ?? '—'); ?></small></td>
                                <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Trainer Recommendations -->
                <div id="tab-trainer_recs" class="panel-card" style="display:none;">
                    <div class="panel-header"><h3><i class="fas fa-dumbbell"></i> Trainer Recommendations</h3></div>
                    <div class="panel-body">
                        <?php if (empty($data['trainer_recs'])): ?>
                            <p style="padding:20px;color:#94a3b8;text-align:center;">No trainer recommendations yet.</p>
                        <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Trainer</th><th>Player</th><th>Fitness Recommended</th><th>Comments</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($data['trainer_recs'] as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r->TrainerName ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($r->PlayerName ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($r->FitnessRecommended ?? 'No'); ?></td>
                                <td style="max-width:200px;"><small><?php echo htmlspecialchars($r->Comments ?? '—'); ?></small></td>
                                <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Team -->
                <div id="tab-team" class="panel-card" style="display:none;">
                    <div class="panel-header">
                        <h3><i class="fas fa-users"></i> Selected Squad (<?php echo count($data['team']); ?>)</h3>
                        <?php if ($t->IsTeamAnnounced): ?>
                            <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:10px;font-size:12px;font-weight:700;"><i class="fas fa-bullhorn"></i> Publicly Announced</span>
                        <?php endif; ?>
                    </div>
                    <div class="panel-body">
                        <?php if (empty($data['team'])): ?>
                            <p style="padding:20px;color:#94a3b8;text-align:center;">No players selected yet. Head coach finalizes the squad.</p>
                        <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Player</th><th>Role</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($data['team'] as $p): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p->Name); ?></strong></td>
                                <td><?php echo htmlspecialchars($p->RoleInTeam ?? '—'); ?></td>
                                <td><?php echo $p->SelectionStatus === 'confirmed' ? '<span style="color:#16a34a;font-weight:700;">Confirmed</span>' : '<span style="color:#f59e0b;font-weight:700;">Draft</span>'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Results -->
                <?php if ($data['result']): ?>
                <div id="tab-results" class="panel-card" style="display:none;">
                    <div class="panel-header"><h3><i class="fas fa-medal"></i> Tournament Result</h3></div>
                    <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <?php $res = $data['result']; ?>
                        <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Position</div><div style="font-size:18px;font-weight:800;color:#1e293b;"><?php echo htmlspecialchars($res->Position); ?></div></div>
                            <div style="font-size:13px;color:#64748b;">Matches: <?php echo htmlspecialchars((string)($res->TotalMatchesPlayed ?? 0)); ?>, Wins: <?php echo htmlspecialchars((string)($res->TotalWins ?? 0)); ?>, Losses: <?php echo htmlspecialchars((string)($res->TotalLosses ?? 0)); ?></div>
                            <div style="font-size:13px;color:#64748b;margin-top:6px;">Best Batsman: <?php echo htmlspecialchars($res->BestBatsmanName ?? '—'); ?> | Best Bowler: <?php echo htmlspecialchars($res->BestBowlerName ?? '—'); ?></div>
                            <div><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Man of Tournament</div><div><?php echo $res->ManFirstName ? htmlspecialchars($res->ManFirstName . ' ' . $res->ManLastName) : '—'; ?></div></div>
                        <?php if ($res->SummaryNotes): ?>
                        <div style="grid-column:1/-1;"><div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Notes</div><div style="font-size:13px;color:#64748b;"><?php echo htmlspecialchars($res->SummaryNotes); ?></div></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>
</div>

<script>
function showTab(id, btn) {
    document.querySelectorAll('[id^="tab-"]').forEach(el => el.style.display = 'none');
    document.querySelector('#tab-' + id).style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function reviewRequest(requestId, action) {
    const label = action === 'approved' ? 'approve' : 'reject';
    if (!confirm('Are you sure you want to ' + label + ' this join request?')) return;
    fetch('<?php echo URLROOT; ?>/admin/' + (action === 'approved' ? 'approve' : 'reject') + '_join_request/' + requestId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'notes='
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert(d.message); });
}
</script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
