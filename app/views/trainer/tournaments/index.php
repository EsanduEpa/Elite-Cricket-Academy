<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<style>
.tournament-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; padding: 20px 0; }
.tournament-card  { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); overflow: hidden; display: flex; flex-direction: column; }
.card-header-strip { padding: 16px 20px; background: linear-gradient(135deg,#1a3c5e,#2e6da4); color:#fff; }
.card-header-strip h3 { margin:0 0 4px; font-size:1rem; }
.card-header-strip .badge { font-size:.72rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,.2); }
.card-body { padding:16px 20px; flex:1; }
.card-body p { margin:4px 0; font-size:.88rem; color:#555; }
.card-body p strong { color:#333; }
.card-footer-strip { padding:12px 20px; border-top:1px solid #f0f0f0; display:flex; gap:10px; }
.btn-sm { padding:6px 14px; font-size:.83rem; border-radius:6px; text-decoration:none; border:none; cursor:pointer; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.empty-state { text-align:center; padding:60px 20px; color:#888; }
.empty-state i { font-size:3rem; margin-bottom:16px; display:block; color:#ccc; }
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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/trainer/tournaments" class="nav-link active"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
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
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-trophy"></i> Tournaments</h1>
                    <p>View upcoming tournaments and recommend players</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/my_recommendations" class="btn-sm btn-secondary">
                        <i class="fas fa-list"></i> My Recommendations
                    </a>
                </div>
            </div>
        </div>

        <?php flash('tournament_message'); ?>

        <div class="content-section">
            <?php if (empty($data['tournaments'])): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>No Tournaments Available</h3>
                    <p>There are no open tournaments at the moment.</p>
                </div>
            <?php else: ?>
                <div class="tournament-cards">
                    <?php foreach ($data['tournaments'] as $t): ?>
                        <div class="tournament-card">
                            <div class="card-header-strip">
                                <h3><?php echo htmlspecialchars($t->Name); ?></h3>
                                <span class="badge"><?php echo htmlspecialchars($t->Format ?? 'N/A'); ?></span>
                            </div>
                            <div class="card-body">
                                <p><strong>Date:</strong> <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?></p>
                                <p><strong>Age Group:</strong> <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($t->Location ?? 'TBD'); ?></p>
                                <p><strong>Registration Deadline:</strong>
                                    <?php echo $t->RegistrationDeadline ? date('d M Y', strtotime($t->RegistrationDeadline)) : 'N/A'; ?></p>
                                <p><strong>Status:</strong> <span style="color:#2e6da4;font-weight:600;"><?php echo ucfirst(htmlspecialchars($t->Status)); ?></span></p>
                            </div>
                            <div class="card-footer-strip">
                                <a href="<?php echo URLROOT; ?>/trainer/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="<?php echo URLROOT; ?>/trainer/recommend_player/<?php echo $t->TournamentID; ?>" class="btn-sm btn-primary">
                                    <i class="fas fa-user-plus"></i> Recommend Player
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
