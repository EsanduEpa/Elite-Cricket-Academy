<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<style>
.tournament-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:20px; padding:20px 0; }
.tournament-card  { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; display:flex; flex-direction:column; }
.card-header-strip { padding:16px 20px; background:linear-gradient(135deg,#1a3c5e,#2e6da4); color:#fff; }
.card-header-strip h3 { margin:0 0 4px; font-size:1rem; }
.card-header-strip .fmt-badge { font-size:.72rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,.2); }
.card-body { padding:16px 20px; flex:1; }
.card-body p { margin:4px 0; font-size:.88rem; color:#555; }
.card-body p strong { color:#333; }
.card-footer-strip { padding:12px 20px; border-top:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
.btn-sm { padding:6px 14px; font-size:.83rem; border-radius:6px; text-decoration:none; border:none; cursor:pointer; display:inline-block; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.status-pill { padding:4px 12px; border-radius:20px; font-size:.78rem; font-weight:700; }
.pill-applied  { background:#dbeafe; color:#1e40af; }
.pill-ineligible { background:#fff7ed; color:#9a3412; }
.empty-state { text-align:center; padding:60px 20px; color:#888; }
.empty-state i { font-size:3rem; margin-bottom:16px; display:block; color:#ccc; }
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
        <div class="dashboard-header" style="padding:20px 24px;">
            <h1 style="margin:0;font-size:1.5rem;color:#1a3c5e;"><i class="fas fa-trophy"></i> Tournaments</h1>
            <p style="margin:4px 0 0;color:#666;">View upcoming tournaments and submit your join requests</p>
        </div>

        <?php flash('tournament_message'); ?>

        <div style="padding:0 24px 24px;">
            <?php if (empty($data['tournaments'])): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>No Tournaments Available</h3>
                    <p>There are no open tournaments at the moment. Check back later.</p>
                </div>
            <?php else: ?>
                <div class="tournament-cards">
                    <?php foreach ($data['tournaments'] as $t): ?>
                        <?php $myReq = $data['my_requests'][$t->TournamentID] ?? null; ?>
                        <?php $eligibility = $data['eligibility'][$t->TournamentID] ?? ['eligible' => true, 'message' => '']; ?>
                        <div class="tournament-card">
                            <div class="card-header-strip">
                                <h3><?php echo htmlspecialchars($t->Name); ?></h3>
                                <span class="fmt-badge"><?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></span>
                            </div>
                            <div class="card-body">
                                <p><strong>Date:</strong> <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></p>
                                <p><strong>Age Group:</strong> <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
                                <p><strong>Your eligibility:</strong> <?php echo $eligibility['eligible'] ? 'Eligible' : 'Not eligible'; ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($t->Location ?? 'TBD'); ?></p>
                                <p><strong>Deadline:</strong> <?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></p>
                                <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($t->Status)); ?></p>
                            </div>
                            <div class="card-footer-strip">
                                <a href="<?php echo URLROOT; ?>/player/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <?php if ($myReq): ?>
                                    <span class="status-pill pill-applied">
                                        <i class="fas fa-check-circle"></i> Applied
                                    </span>
                                <?php elseif ($t->Status === 'registration_open' && !$eligibility['eligible']): ?>
                                    <span class="status-pill pill-ineligible">
                                        <i class="fas fa-ban"></i> Not Eligible
                                    </span>
                                <?php elseif ($t->Status === 'registration_open'): ?>
                                    <a href="<?php echo URLROOT; ?>/player/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-primary">
                                        <i class="fas fa-paper-plane"></i> Apply
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
