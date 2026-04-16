<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<style>
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px; }
.info-card  { background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.info-card h4 { margin:0 0 12px; font-size:.9rem; color:#888; text-transform:uppercase; letter-spacing:.5px; }
.info-card p  { margin:4px 0; font-size:.93rem; color:#333; }
.section-card { background:#fff; border-radius:10px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:20px; }
.section-card h3 { margin:0 0 16px; font-size:1.05rem; border-bottom:2px solid #f0f0f0; padding-bottom:10px; }
table.data-table { width:100%; border-collapse:collapse; font-size:.88rem; }
table.data-table th { background:#f7f8fa; padding:10px 12px; text-align:left; color:#555; font-weight:600; border-bottom:2px solid #e0e0e0; }
table.data-table td { padding:10px 12px; border-bottom:1px solid #f0f0f0; color:#333; }
table.data-table tr:last-child td { border-bottom:none; }
.status-badge { padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
.status-pending  { background:#fff3cd; color:#856404; }
.status-reviewed { background:#cfe2ff; color:#084298; }
.status-confirmed { background:#d1e7dd; color:#0a3622; }
.status-rejected  { background:#f8d7da; color:#842029; }
.btn-sm { padding:7px 16px; font-size:.83rem; border-radius:6px; text-decoration:none; border:none; cursor:pointer; display:inline-block; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.empty-note { color:#888; font-size:.9rem; font-style:italic; }
</style>

<div class="trainer-layout">
    <!-- Sidebar -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-info">
                <div class="trainer-avatar"><i class="fas fa-user-circle"></i></div>
                <div class="trainer-details">
                    <h4><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Trainer'; ?></h4>
                    <p>Physical Trainer</p>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link"><i class="fas fa-calendar-check"></i><span>Player Bookings</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/schedules" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Training Schedules</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/my_recommendations" class="nav-link"><i class="fas fa-star"></i><span>My Recommendations</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link"><i class="fas fa-capsules"></i><span>Nutrition &amp; Supplements</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link"><i class="fas fa-dumbbell"></i><span>Workout Plans</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/medical" class="nav-link"><i class="fas fa-user-injured"></i><span>Medical Records</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content" id="mainContent">
        <?php $t = $data['tournament']; ?>
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($t->Name); ?></h1>
                    <p>Tournament Details</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/recommend_player/<?php echo $t->TournamentID; ?>" class="btn-sm btn-primary">
                        <i class="fas fa-user-plus"></i> Recommend Player
                    </a>
                    <a href="<?php echo URLROOT; ?>/trainer/tournaments" class="btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <?php flash('tournament_message'); ?>

        <!-- Tournament Info -->
        <div class="info-grid">
            <div class="info-card">
                <h4>Details</h4>
                <p><strong>Format:</strong> <?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></p>
                <p><strong>Age Group:</strong> <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($t->Location ?? 'TBD'); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($t->Status)); ?></p>
            </div>
            <div class="info-card">
                <h4>Dates &amp; Capacity</h4>
                <p><strong>Tournament Date:</strong> <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></p>
                <p><strong>Registration Deadline:</strong> <?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></p>
                <p><strong>Max Players:</strong> <?php echo htmlspecialchars($t->MaxPlayers ?? 'N/A'); ?></p>
                <p><strong>Prize Pool:</strong> <?php echo $t->PrizePool ? 'LKR ' . number_format($t->PrizePool) : 'N/A'; ?></p>
            </div>
        </div>

        <?php if ($t->Description): ?>
        <div class="section-card">
            <h3>Description</h3>
            <p style="line-height:1.6"><?php echo nl2br(htmlspecialchars($t->Description)); ?></p>
        </div>
        <?php endif; ?>

        <!-- My Recommendations for This Tournament -->
        <div class="section-card">
            <h3><i class="fas fa-star"></i> My Recommendations for This Tournament</h3>
            <?php $recs = $data['my_recs_for_tournament']; ?>
            <?php if (empty($recs)): ?>
                <p class="empty-note">You haven't recommended any players for this tournament yet.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Recommended Role</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recs as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r->PlayerName); ?></td>
                            <td><?php echo htmlspecialchars($r->RecommendedRole ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($r->Reason ?? '—'); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($r->Status); ?>"><?php echo ucfirst($r->Status); ?></span></td>
                            <td><?php echo date('d M Y', strtotime($r->DateRecommended)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Announced Team -->
        <?php if (in_array(strtolower((string)($t->Status ?? '')), ['team_announced', 'ongoing', 'completed'], true) || $t->IsTeamAnnounced): ?>
        <div class="section-card">
            <h3><i class="fas fa-users"></i> Squad</h3>
            <table class="data-table">
                <thead><tr><th>Player</th><th>Role</th></tr></thead>
                <tbody>
                    <?php if (!empty($data['team'])): ?>
                    <?php foreach ($data['team'] as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member->Name ?? $member->PlayerName ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($member->RoleInTeam ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align:center;color:#64748b;padding:18px;">The squad is visible for this tournament state, but no players are listed yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
