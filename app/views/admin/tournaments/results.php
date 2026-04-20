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
        <?php $t = $data['tournament']; $res = $data['result']; $team = $data['team']; ?>

        <div class="events-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-medal"></i> Enter Results — <?php echo htmlspecialchars($t->Name); ?></h1>
                    <p>Record the final result and individual player statistics.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/admin/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Detail</a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="tourn-alert success tourn-content" style="margin-bottom:0;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="tourn-alert error tourn-content" style="margin-bottom:0;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="tourn-content">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/enter_results/<?php echo $t->TournamentID; ?>">

                <!-- Tournament-level result -->
                <div class="panel-card">
                    <div class="panel-header"><h3>Tournament Outcome</h3></div>
                    <div class="form-grid" style="padding:20px;">
                        <div class="form-field">
                            <label>Position / Standing</label>
                            <select name="position" required>
                                <option value="">— Select —</option>
                                <?php foreach (['super 8','super 16','3rd runners up','2nd runners up','1st runners up','champions'] as $pos): ?>
                                    <option value="<?php echo $pos; ?>" <?php echo ($res && $res->Position === $pos) ? 'selected' : ''; ?>><?php echo $pos; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Total Matches Played</label>
                            <input type="number" name="total_matches_played" min="0" value="<?php echo htmlspecialchars($res->TotalMatchesPlayed ?? '0'); ?>">
                        </div>
                        <div class="form-field">
                            <label>Total Wins</label>
                            <input type="number" name="total_wins" min="0" value="<?php echo htmlspecialchars($res->TotalWins ?? '0'); ?>">
                        </div>
                        <div class="form-field">
                            <label>Total Losses</label>
                            <input type="number" name="total_losses" min="0" value="<?php echo htmlspecialchars($res->TotalLosses ?? '0'); ?>">
                        </div>
                        <div class="form-field">
                            <label>Man of the Tournament</label>
                            <select name="man_of_tournament">
                                <option value="">— None —</option>
                                <?php foreach ($team as $p): ?>
                                    <option value="<?php echo $p->PlayerID; ?>" <?php echo ($res && $res->ManOfTournament == $p->PlayerID) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p->Name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Best Batsman</label>
                            <select name="best_batsman">
                                <option value="">— None —</option>
                                <?php foreach ($team as $p): ?>
                                    <option value="<?php echo $p->PlayerID; ?>" <?php echo ($res && $res->BestBatsman == $p->PlayerID) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p->Name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Best Bowler</label>
                            <select name="best_bowler">
                                <option value="">— None —</option>
                                <?php foreach ($team as $p): ?>
                                    <option value="<?php echo $p->PlayerID; ?>" <?php echo ($res && $res->BestBowler == $p->PlayerID) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p->Name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-field full">
                            <label>Summary Notes</label>
                            <textarea name="summary_notes" rows="3" placeholder="Overall tournament summary, highlights..."><?php echo htmlspecialchars($res->SummaryNotes ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Per-player stats -->
                <?php if (!empty($team)): ?>
                <div class="panel-card">
                    <div class="panel-header"><h3>Player Statistics</h3></div>
                    <div style="overflow-x:auto;">
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Matches</th>
                                <th>Runs</th>
                                <th>Wickets</th>
                                <th>Bat Avg</th>
                                <th>Bowl Avg</th>
                                <th>Strike Rate</th>
                                <th>Economy</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $statsMap = [];
                        foreach ($data['stats'] as $s) {
                            $statsMap[$s->PlayerID] = $s;
                        }
                        foreach ($team as $p):
                            $pid = $p->PlayerID;
                            $s = $statsMap[$pid] ?? null;
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p->Name); ?></strong><br><small style="color:#94a3b8;"><?php echo htmlspecialchars($p->RoleInTeam ?? ''); ?></small></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][matches]" min="0" value="<?php echo $s->MatchesPlayed ?? ''; ?>" placeholder="0"></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][runs]" min="0" value="<?php echo $s->TotalRuns ?? ''; ?>" placeholder="0"></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][wickets]" min="0" value="<?php echo $s->TotalWickets ?? ''; ?>" placeholder="0"></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][bat_avg]" step="0.01" min="0" value="<?php echo $s->BattingAverage ?? ''; ?>" placeholder="0.00"></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][bowl_avg]" step="0.01" min="0" value="<?php echo $s->BowlingAverage ?? ''; ?>" placeholder="0.00"></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][sr]" step="0.01" min="0" value="<?php echo $s->StrikeRate ?? ''; ?>" placeholder="0.00"></td>
                            <td><input type="number" name="player_stats[<?php echo $pid; ?>][er]" step="0.01" min="0" value="<?php echo $s->EconomyRate ?? ''; ?>" placeholder="0.00"></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <?php else: ?>
                    <div class="tourn-alert warning">
                        <i class="fas fa-exclamation-triangle"></i> No squad selected yet. Per-player statistics will be available once the team is finalized.
                    </div>
                <?php endif; ?>

                <div style="text-align:right;">
                    <button type="submit" class="btn-save" style="padding:12px 32px;font-size:15px;"><i class="fas fa-save"></i> Save Results</button>
                </div>

            </form>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
