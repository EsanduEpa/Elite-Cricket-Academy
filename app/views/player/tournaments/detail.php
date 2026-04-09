<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<style>
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px; }
.info-card  { background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.info-card h4 { margin:0 0 12px; font-size:.9rem; color:#888; text-transform:uppercase; letter-spacing:.5px; }
.info-card p  { margin:4px 0; font-size:.93rem; color:#333; }
.section-card { background:#fff; border-radius:10px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:20px; }
.section-card h3 { margin:0 0 16px; font-size:1.05rem; border-bottom:2px solid #f0f0f0; padding-bottom:10px; }
.request-box { padding:20px; border-radius:10px; border:2px solid; }
.request-box.pending  { border-color:#ffc107; background:#fffdf0; }
.request-box.approved { border-color:#28a745; background:#f0fff4; }
.request-box.rejected { border-color:#dc3545; background:#fff5f5; }
.request-box h4 { margin:0 0 8px; }
.request-box p  { margin:4px 0; font-size:.9rem; color:#555; }
table.data-table { width:100%; border-collapse:collapse; font-size:.88rem; }
table.data-table th { background:#f7f8fa; padding:10px 12px; text-align:left; color:#555; font-weight:600; border-bottom:2px solid #e0e0e0; }
table.data-table td { padding:10px 12px; border-bottom:1px solid #f0f0f0; color:#333; }
table.data-table tr:last-child td { border-bottom:none; }
.form-group { margin-bottom:16px; }
.form-group label { display:block; font-weight:600; margin-bottom:6px; color:#333; font-size:.92rem; }
.form-group textarea { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:.93rem; font-family:inherit; box-sizing:border-box; resize:vertical; min-height:100px; }
.form-group textarea:focus { outline:none; border-color:#2e6da4; box-shadow:0 0 0 3px rgba(46,109,164,.1); }
.btn { padding:9px 20px; border-radius:8px; border:none; cursor:pointer; font-size:.9rem; font-weight:600; text-decoration:none; display:inline-block; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.btn-danger    { background:#dc3545; color:#fff; }
.empty-note { color:#888; font-size:.9rem; font-style:italic; }
.team-member-badge { display:inline-block; padding:5px 12px; background:#e8f4fd; border-radius:20px; font-size:.85rem; color:#1a3c5e; margin:4px; font-weight:600; }
</style>

<div class="player-layout">
    <!-- Sidebar -->
    <div class="player-sidebar" id="playerSidebar">
        <div class="sidebar-header">
            <div class="player-logo">
                <i class="fas fa-user-graduate"></i>
                <h3>Player Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/training" class="nav-link"><i class="fas fa-dumbbell"></i><span>Training</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/performance" class="nav-link"><i class="fas fa-chart-line"></i><span>Performance</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/available" class="nav-link"><i class="fas fa-ticket-alt"></i><span>Book Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/playerslots/bookings" class="nav-link"><i class="fas fa-list-alt"></i><span>My Sessions</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link"><i class="fas fa-medal"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/medical" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/payments" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Shopping</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($data['player']['name'] ?? 'Player'); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($data['player']['membership_level'] ?? 'Regular'); ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content">
        <?php $t = $data['tournament']; $myReq = $data['my_request']; ?>

        <div class="dashboard-header" style="padding:20px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h1 style="margin:0;font-size:1.4rem;color:#1a3c5e;"><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($t->Name); ?></h1>
                    <p style="margin:4px 0 0;color:#666;">Tournament Details</p>
                </div>
                <a href="<?php echo URLROOT; ?>/player/tournaments" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tournaments
                </a>
            </div>
        </div>

        <div style="padding:0 24px 24px;">
            <?php flash('tournament_message'); ?>

            <!-- Tournament Info -->
            <div class="info-grid">
                <div class="info-card">
                    <h4>Details</h4>
                    <p><strong>Format:</strong> <?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></p>
                    <p><strong>Age Group:</strong> <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($t->Location ?? 'TBD'); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($t->Status)); ?></p>
                    <?php if ($t->PrizePool): ?>
                    <p><strong>Prize Pool:</strong> LKR <?php echo number_format($t->PrizePool); ?></p>
                    <?php endif; ?>
                </div>
                <div class="info-card">
                    <h4>Dates &amp; Capacity</h4>
                    <p><strong>Tournament Date:</strong> <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></p>
                    <p><strong>Registration Deadline:</strong> <?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></p>
                    <p><strong>Max Players:</strong> <?php echo htmlspecialchars($t->MaxPlayers ?? 'N/A'); ?></p>
                    <p><strong>Team Announced:</strong> <?php echo $t->IsTeamAnnounced ? '<span style="color:#28a745;font-weight:600;">Yes</span>' : 'Not yet'; ?></p>
                </div>
            </div>

            <?php if ($t->Description): ?>
            <div class="section-card">
                <h3>Description</h3>
                <p style="line-height:1.6;color:#444;"><?php echo nl2br(htmlspecialchars($t->Description)); ?></p>
            </div>
            <?php endif; ?>

            <!-- Join Request Block -->
            <div class="section-card">
                <h3><i class="fas fa-paper-plane"></i> Apply for This Tournament</h3>

                <?php if ($myReq): ?>
                    <!-- Already applied -->
                    <div class="request-box" style="border-color:#2e6da4;background:#f0f5ff;">
                        <h4 style="color:#1a3c5e;"><i class="fas fa-check-circle" style="color:#2e6da4;"></i> You have applied</h4>
                        <?php if ($myReq->Message): ?>
                            <p><strong>Your message:</strong> <?php echo htmlspecialchars($myReq->Message); ?></p>
                        <?php endif; ?>
                        <p style="margin-top:8px;font-size:.82rem;color:#777;">Submitted: <?php echo date('d M Y, H:i', strtotime($myReq->RequestedAt)); ?></p>
                        <?php if ($t->Status === 'registration_open'): ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/player/cancel_join_request/<?php echo $myReq->RequestID; ?>"
                              style="margin-top:14px;"
                              onsubmit="return confirm('Withdraw your application?');">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Withdraw Application
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                <?php elseif ($t->Status === 'registration_open'): ?>
                    <!-- Apply form -->
                    <form method="POST" action="<?php echo URLROOT; ?>/player/join_tournament/<?php echo $t->TournamentID; ?>">
                        <div class="form-group">
                            <label for="message">Message (optional)</label>
                            <textarea name="message" id="message" placeholder="Tell us about your experience or why you want to join..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Join Request
                        </button>
                    </form>

                <?php else: ?>
                    <p class="empty-note">Registration is not currently open for this tournament.</p>
                <?php endif; ?>
            </div>

            <!-- Announced Team -->
            <?php if ($t->IsTeamAnnounced && !empty($data['team'])): ?>
            <div class="section-card">
                <h3><i class="fas fa-users"></i> Announced Squad</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Player</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($data['team'] as $member): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($member->Name ?? $member->PlayerName ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($member->RoleInTeam ?? '—'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif ($t->IsTeamAnnounced): ?>
            <div class="section-card">
                <h3><i class="fas fa-users"></i> Announced Squad</h3>
                <p class="empty-note">The team has been announced but details are not yet available.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
