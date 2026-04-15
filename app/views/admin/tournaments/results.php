<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.panel-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.1); margin-bottom:20px; overflow:hidden; }
.panel-header { padding:16px 20px; border-bottom:1px solid #f1f5f9; }
.panel-header h3 { margin:0; font-size:15px; color:#1e293b; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:20px; }
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-group label { font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; }
.form-group input, .form-group select, .form-group textarea { padding:9px 12px; border:1px solid #d1d5db; border-radius:7px; font-size:14px; }
.stats-table { width:100%; border-collapse:collapse; font-size:13px; }
.stats-table th { padding:10px 14px; text-align:left; background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#64748b; font-weight:700; font-size:12px; }
.stats-table td { padding:8px 14px; border-bottom:1px solid #f8fafc; }
.stats-table tr:last-child td { border-bottom:none; }
.stats-table input { width:70px; padding:5px 8px; border:1px solid #d1d5db; border-radius:5px; font-size:13px; text-align:center; }
</style>

<div class="admin-layout">
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo"><i class="fas fa-user-shield"></i><h3>Admin Dashboard</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Overview</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link"><i class="fas fa-users-cog"></i><span>Staff Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/players" class="nav-link"><i class="fas fa-user-graduate"></i><span>Player Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/events" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link"><i class="fas fa-comments"></i><span>Feedback Monitoring</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/reports" class="nav-link"><i class="fas fa-file-alt"></i><span>Reports</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finance Management</span></a></li>
            </ul>
        </nav>
        <div class="admin-profile">
            <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
            <div class="profile-info"><span class="admin-name">Admin User</span><span class="admin-role">Super Administrator</span></div>
            <div class="logout-btn"><a href="<?php echo URLROOT; ?>/login/logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a></div>
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
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin:0 20px 10px;padding:12px 16px;border-radius:8px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="padding:20px;">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/enter_results/<?php echo $t->TournamentID; ?>">

                <!-- Tournament-level result -->
                <div class="panel-card">
                    <div class="panel-header"><h3>Tournament Outcome</h3></div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Position / Standing</label>
                            <select name="position" required>
                                <option value="">— Select —</option>
                                <?php foreach (['super 8','super 16','3rd runners up','2nd runners up','1st runners up','champions'] as $pos): ?>
                                    <option value="<?php echo $pos; ?>" <?php echo ($res && $res->Position === $pos) ? 'selected' : ''; ?>><?php echo $pos; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Matches Played</label>
                            <input type="number" name="total_matches_played" min="0" value="<?php echo htmlspecialchars($res->TotalMatchesPlayed ?? '0'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Total Wins</label>
                            <input type="number" name="total_wins" min="0" value="<?php echo htmlspecialchars($res->TotalWins ?? '0'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Total Losses</label>
                            <input type="number" name="total_losses" min="0" value="<?php echo htmlspecialchars($res->TotalLosses ?? '0'); ?>">
                        </div>
                        <div class="form-group">
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
                        <div class="form-group">
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
                        <div class="form-group">
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
                        <div class="form-group" style="grid-column:1/-1;">
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
                        // Build stats lookup by PlayerID
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
                    <div style="background:#fef9c3;border-radius:8px;padding:14px;color:#854d0e;font-size:13px;margin-bottom:20px;">
                        <i class="fas fa-exclamation-triangle"></i> No squad selected yet. Per-player statistics will be available once the team is finalized.
                    </div>
                <?php endif; ?>

                <div style="text-align:right;">
                    <button type="submit" style="padding:12px 32px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Save Results</button>
                </div>

            </form>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
