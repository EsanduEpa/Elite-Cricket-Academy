<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/dashboard.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'tournaments'; ?>
<style>
.form-card { background:#fff; border-radius:12px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,.08); max-width:680px; }
.form-group { margin-bottom:20px; }
.form-group label { display:block; font-weight:600; margin-bottom:6px; color:#333; font-size:.92rem; }
.form-group select,
.form-group textarea { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:.93rem; font-family:inherit; box-sizing:border-box; }
.form-group select:focus,
.form-group textarea:focus { outline:none; border-color:#2e6da4; box-shadow:0 0 0 3px rgba(46,109,164,.1); }
.form-group textarea { resize:vertical; min-height:100px; }
.btn { padding:10px 22px; border-radius:8px; border:none; cursor:pointer; font-size:.93rem; font-weight:600; text-decoration:none; display:inline-block; }
.btn-primary   { background:#2e6da4; color:#fff; }
.btn-secondary { background:#6c757d; color:#fff; }
.tournament-info { background:#f0f5ff; border-left:4px solid #2e6da4; border-radius:0 8px 8px 0; padding:14px 18px; margin-bottom:24px; }
.tournament-info h4 { margin:0 0 4px; font-size:.95rem; color:#1a3c5e; }
.tournament-info p  { margin:0; font-size:.85rem; color:#555; }
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
        <?php $t = $data['tournament']; ?>
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-user-plus"></i> Recommend a Player</h1>
                    <p>Submit a player recommendation for this tournament</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <?php flash('tournament_message'); ?>

        <div class="tournament-info">
            <h4><?php echo htmlspecialchars($t->Name); ?></h4>
            <p><?php echo htmlspecialchars($t->Format ?? 'N/A'); ?> &middot;
               <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?> &middot;
               <?php echo htmlspecialchars($t->AgeGroup ?? 'Open'); ?></p>
        </div>

        <?php if (empty($data['players'])): ?>
        <div class="form-card" style="text-align:center;color:#94a3b8;padding:30px;">
            <i class="fas fa-users" style="font-size:2rem;margin-bottom:10px;"></i>
            <p>No players have applied for this tournament yet.</p>
            <a href="<?php echo URLROOT; ?>/trainer/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary" style="margin-top:10px;">Back</a>
        </div>
        <?php else: ?>
        <div class="form-card">
            <form method="POST" action="<?php echo URLROOT; ?>/trainer/recommend_player/<?php echo $t->TournamentID; ?>">
                <div class="form-group">
                    <label for="player_id">Select Player <span style="color:red">*</span></label>
                    <select name="player_id" id="player_id" required>
                        <option value="">-- Choose a player who applied --</option>
                        <?php foreach ($data['players'] as $p): ?>
                            <option value="<?php echo $p->PlayerID; ?>">
                                <?php echo htmlspecialchars($p->Name); ?>
                                (<?php echo $p->CoachRecs + $p->TrainerRecs; ?> rec<?php echo ($p->CoachRecs + $p->TrainerRecs) != 1 ? 's' : ''; ?> so far)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="role">Recommended Role</label>
                    <select name="role" id="role">
                        <option value="">-- Select role (optional) --</option>
                        <option value="Batsman">Batsman</option>
                        <option value="Bowler">Bowler</option>
                        <option value="All-rounder">All-rounder</option>
                        <option value="Wicket Keeper">Wicket Keeper</option>
                        <option value="Captain">Captain</option>
                        <option value="Vice Captain">Vice Captain</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Recommendation <span style="color:red">*</span></label>
                    <textarea name="reason" id="reason" required placeholder="Why are you recommending this player?"></textarea>
                </div>

                <div class="form-group">
                    <label for="comments">Additional Comments</label>
                    <textarea name="comments" id="comments" style="min-height:70px" placeholder="Optional additional notes..."></textarea>
                </div>

                <div style="display:flex;gap:12px;margin-top:10px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Submit Recommendation</button>
                    <a href="<?php echo URLROOT; ?>/trainer/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
