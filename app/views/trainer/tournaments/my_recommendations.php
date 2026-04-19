<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'tournaments'; ?>
<style>
.section-card { background:#fff; border-radius:10px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:20px; }
table.data-table { width:100%; border-collapse:collapse; font-size:.88rem; }
table.data-table th { background:#f7f8fa; padding:10px 12px; text-align:left; color:#555; font-weight:600; border-bottom:2px solid #e0e0e0; }
table.data-table td { padding:10px 12px; border-bottom:1px solid #f0f0f0; color:#333; }
table.data-table tr:last-child td { border-bottom:none; }
.status-badge { padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
.status-pending  { background:#fff3cd; color:#856404; }
.status-reviewed { background:#cfe2ff; color:#084298; }
.status-confirmed { background:#d1e7dd; color:#0a3622; }
.status-rejected  { background:#f8d7da; color:#842029; }
.empty-state { text-align:center; padding:60px 20px; color:#888; }
.empty-state i { font-size:3rem; margin-bottom:16px; display:block; color:#ccc; }
.btn-sm { padding:6px 14px; font-size:.83rem; border-radius:6px; text-decoration:none; display:inline-block; }
.btn-primary { background:#2e6da4; color:#fff; }
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
        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="logoutUser()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-list"></i> My Recommendations</h1>
                    <p>All player recommendations you have submitted across tournaments</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/tournaments" class="btn-sm btn-primary">
                        <i class="fas fa-arrow-left"></i> All Tournaments
                    </a>
                </div>
            </div>
        </div>

        <?php flash('tournament_message'); ?>

        <div class="section-card">
            <?php if (empty($data['recs'])): ?>
                <div class="empty-state">
                    <i class="fas fa-star"></i>
                    <h3>No Recommendations Yet</h3>
                    <p>You haven't recommended any players for tournaments yet.</p>
                    <a href="<?php echo URLROOT; ?>/trainer/tournaments" class="btn-sm btn-primary" style="margin-top:12px;">Browse Tournaments</a>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Date</th>
                            <th>Player</th>
                            <th>Fitness Recommended</th>
                            <th>Comments</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['recs'] as $r): ?>
                        <tr>
                            <td>
                                <a href="<?php echo URLROOT; ?>/trainer/tournament_detail/<?php echo $r->TournamentID; ?>"
                                   style="color:#2e6da4; text-decoration:none; font-weight:600;">
                                    <?php echo htmlspecialchars($r->TournamentName); ?>
                                </a>
                            </td>
                            <td><?php echo $r->tdate ? date('d M Y', strtotime($r->tdate)) : 'TBD'; ?></td>
                            <td><?php echo htmlspecialchars($r->PlayerName ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($r->FitnessRecommended ?? 'No'); ?></td>
                            <td style="max-width:200px;"><?php echo htmlspecialchars($r->Comments ?? '—'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($r->Status); ?>">
                                    <?php echo ucfirst($r->Status); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($r->DateRecommended)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
