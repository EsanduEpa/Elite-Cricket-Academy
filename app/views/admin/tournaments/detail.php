<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/tournaments.css">

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

        <?php
        $pendingJoinRequestCount = 0;
        if (!empty($data['join_requests'])) {
            foreach ($data['join_requests'] as $jr) {
                if (($jr->Status ?? '') === 'pending') {
                    $pendingJoinRequestCount++;
                }
            }
        }
        ?>

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

        <div class="detail-layout">

            <!-- Left: tournament info + status controls -->
            <div>
                <div class="panel-card">
                    <div class="meta-panel">
                        <div class="meta-item">
                            <div class="meta-label">Location</div>
                            <div class="meta-value"><?php echo htmlspecialchars($t->Location ?? '—'); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Registration Deadline</div>
                            <div class="meta-value"><?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : '—'; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Max Players</div>
                            <div class="meta-value"><?php echo $t->MaxPlayers ?? '—'; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Prize Pool</div>
                            <div class="meta-value">Rs. <?php echo number_format($t->PrizePool ?? 0, 2); ?></div>
                        </div>
                        <?php if ($t->Description): ?>
                        <div class="meta-item">
                            <div class="meta-label">Description</div>
                            <div class="meta-value" style="color:#64748b;font-size:13px;"><?php echo htmlspecialchars($t->Description); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Advance -->
                <?php if ($t->Status !== 'cancelled' && $t->Status !== 'completed'): ?>
                <div class="panel-card">
                    <div class="status-advance-panel">
                        <div class="panel-title">Advance Status</div>
                        <?php if (!empty($data['status_options'])): ?>
                            <form method="POST" action="<?php echo URLROOT; ?>/admin/update_tournament_status/<?php echo $t->TournamentID; ?>">
                                <select name="status" required>
                                    <option value="">Select status</option>
                                    <?php foreach ($data['status_options'] as $statusOption): ?>
                                        <option value="<?php echo $statusOption; ?>"><?php echo ucwords(str_replace('_', ' ', $statusOption)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="hint">The list includes every valid forward status from the current state.</div>
                                <button type="submit" class="btn-apply-status">Apply Status</button>
                            </form>
                        <?php else: ?>
                            <div style="font-size:13px;color:#64748b;">No further status changes are available right now.</div>
                        <?php endif; ?>

                        <?php
                            $daysLeft = $t->tdate
                                ? (int) ceil((strtotime($t->tdate) - time()) / 86400)
                                : null;
                        ?>
                        <?php if ($daysLeft !== null && $daysLeft > 40): ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/admin/cancel_tournament/<?php echo $t->TournamentID; ?>" style="margin-top:8px;" onsubmit="return confirm('Cancel this tournament? All pending join requests will be rejected.');">
                            <input type="text" name="cancel_reason" required placeholder="Cancellation reason..." class="cancel-reason-input">
                            <button type="submit" class="btn-cancel-tournament">Cancel Tournament</button>
                        </form>
                        <?php else: ?>
                        <div style="margin-top:8px;background:#fef9c3;border:1px solid #fde68a;border-radius:7px;padding:10px 12px;font-size:12px;color:#854d0e;">
                            <i class="fas fa-lock"></i> Cancellation is locked — tournament is within <strong>40 days</strong><?php echo $daysLeft !== null ? " ($daysLeft day" . ($daysLeft === 1 ? '' : 's') . " remaining)" : ''; ?>.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($t->Status === 'cancelled' && $t->CancelReason): ?>
                <div class="cancel-reason-box">
                    <strong>Cancelled:</strong> <?php echo htmlspecialchars($t->CancelReason); ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: tabbed panels -->
            <div>
                <!-- Tab buttons -->
                <div class="tab-bar">
                    <button class="tab-btn active" onclick="showTab('join_requests',this)">
                        Join Requests <span class="tab-count pending"><?php echo (int)$pendingJoinRequestCount; ?></span>
                    </button>
                    <button class="tab-btn" onclick="showTab('coach_recs',this)">Coach Recs <span class="tab-count"><?php echo count($data['coach_recs']); ?></span></button>
                    <button class="tab-btn" onclick="showTab('trainer_recs',this)">Trainer Recs <span class="tab-count"><?php echo count($data['trainer_recs']); ?></span></button>
                    <button class="tab-btn" onclick="showTab('team',this)">Team <span class="tab-count"><?php echo count($data['team']); ?></span></button>
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
                            <thead><tr><th>Player</th><th>Message</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($data['join_requests'] as $r): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($r->Name); ?></strong><br><small style="color:#94a3b8;"><?php echo htmlspecialchars($r->Email ?? ''); ?></small></td>
                                <td style="max-width:220px;"><small><?php echo htmlspecialchars($r->Message ?? '—'); ?></small></td>
                                <td><span class="badge-<?php echo $r->Status; ?>"><?php echo strtoupper($r->Status); ?></span></td>
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
                            <thead><tr><th>Coach</th><th>Player</th><th>Role</th><th>Captaincy</th><th>Wicket Keeper</th><th>Reason</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($data['coach_recs'] as $r): ?>
                            <?php
                                $role = strtolower(trim((string)($r->RecommendedRole ?? '')));
                                $roleLabel = match ($role) {
                                    'batsman' => 'Batsman',
                                    'bowler' => 'Bowler',
                                    'allrounder' => 'All-rounder',
                                    default => ($role !== '' ? ucfirst(str_replace(['-', '_'], ' ', $role)) : '—'),
                                };

                                $captaincy = strtolower(trim((string)($r->Captaincy ?? 'team member')));
                                $captaincyLabel = match ($captaincy) {
                                    'captain' => 'Captain',
                                    'vice captain' => 'Vice Captain',
                                    default => 'Team Member',
                                };

                                $wk = strtolower(trim((string)($r->WicketKeeper ?? 'no')));
                                $wkLabel = $wk === 'yes' ? 'Yes' : 'No';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r->CoachName ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($r->PlayerName ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($roleLabel); ?></td>
                                <td><?php echo htmlspecialchars($captaincyLabel); ?></td>
                                <td><?php echo htmlspecialchars($wkLabel); ?></td>
                                <td><small><?php echo htmlspecialchars($r->Reason ?? '—'); ?></small></td>
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
                                <td><small><?php echo htmlspecialchars($r->Comments ?? '—'); ?></small></td>
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
                            <span class="badge-approved"><i class="fas fa-bullhorn"></i> Publicly Announced</span>
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
                                <td><?php echo $p->SelectionStatus === 'confirmed' ? '<span class="badge-approved">Confirmed</span>' : '<span class="badge-pending">Draft</span>'; ?></td>
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
                    <div class="result-summary">
                        <?php $res = $data['result']; ?>
                        <div>
                            <div class="result-label">Position</div>
                            <div class="result-value"><?php echo htmlspecialchars($res->Position); ?></div>
                        </div>
                        <div>
                            <div class="result-label">Record</div>
                            <div style="font-size:13px;color:#64748b;">Matches: <?php echo (int)($res->TotalMatchesPlayed ?? 0); ?>, Wins: <?php echo (int)($res->TotalWins ?? 0); ?>, Losses: <?php echo (int)($res->TotalLosses ?? 0); ?></div>
                            <div style="font-size:13px;color:#64748b;margin-top:4px;">Best Batsman: <?php echo htmlspecialchars($res->BestBatsmanName ?? '—'); ?> | Best Bowler: <?php echo htmlspecialchars($res->BestBowlerName ?? '—'); ?></div>
                        </div>
                        <div>
                            <div class="result-label">Man of Tournament</div>
                            <div class="meta-value"><?php echo $res->ManFirstName ? htmlspecialchars($res->ManFirstName . ' ' . $res->ManLastName) : '—'; ?></div>
                        </div>
                        <?php if ($res->SummaryNotes): ?>
                        <div style="grid-column:1/-1;">
                            <div class="result-label">Notes</div>
                            <div style="font-size:13px;color:#64748b;"><?php echo htmlspecialchars($res->SummaryNotes); ?></div>
                        </div>
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
</script>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
