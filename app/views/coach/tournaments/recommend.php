<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournament-pages.css">

<div class="coach-layout">
    <div class="coach-sidebar" id="coachSidebar">
        <div class="sidebar-header">
            <div class="coach-logo"><i class="fas fa-chalkboard-teacher"></i><h3>Coach Panel</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-angle-left"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players" class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
            </ul>
        </nav>
        <div class="profile-section">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Coach'); ?></div>
            <div class="profile-role">Cricket Coach<?php echo $data['is_head_coach'] ? ' · Head Coach' : ''; ?></div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top:8px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <main class="main-content" id="mainContent">
        <?php $t = $data['tournament']; ?>
        <div class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-user-plus"></i> Recommend a Player</h1>
                <p>Recommend applicants for <strong><?php echo htmlspecialchars($t->Name); ?></strong></p>
            </div>
            <div style="display:flex;gap:8px;padding:0 20px;">
                <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tournament
                </a>
            </div>
        </div>

        <div style="padding:20px;max-width:760px;">

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="tournament-info">
                <h4><?php echo htmlspecialchars($t->Name); ?></h4>
                <p>
                    <?php echo htmlspecialchars($t->Format ?? 'N/A'); ?> &middot;
                    <?php echo $t->tdate ? date('d M Y', strtotime($t->tdate)) : 'TBD'; ?> &middot;
                    <?php echo htmlspecialchars($t->Location ?? ''); ?>
                </p>
            </div>

            <?php if (empty($data['players'])): ?>
                <div class="form-card" style="text-align:center;color:#94a3b8;">
                    <i class="fas fa-users" style="font-size:2rem;margin-bottom:10px;"></i>
                    <p>No players have applied for this tournament yet.</p>
                </div>
            <?php else: ?>

            <!-- Build a quick lookup of already-recommended player IDs -->
            <?php
            $alreadyRecommended = [];
            foreach ($data['my_recs'] as $r) {
                $alreadyRecommended[$r->PlayerID] = $r;
            }
            ?>

            <div class="form-card">
                <h3 style="margin:0 0 18px;font-size:15px;color:#1e293b;"><i class="fas fa-paper-plane" style="color:#3b82f6;"></i> Submit a Recommendation</h3>

                <form method="POST" action="<?php echo URLROOT; ?>/coach/save_recommendation">
                    <input type="hidden" name="tournamentId" value="<?php echo $t->TournamentID; ?>">

                    <div class="form-group">
                        <label for="playerId">Select Player <span style="color:red">*</span></label>
                        <select name="playerId" id="playerId" required>
                            <option value="">-- Choose a player who applied --</option>
                            <?php foreach ($data['players'] as $p): ?>
                                <?php $alreadyRec = $alreadyRecommended[$p->PlayerID] ?? null; ?>
                                <option value="<?php echo $p->PlayerID; ?>" <?php echo $alreadyRec ? 'disabled' : ''; ?>>
                                    <?php echo htmlspecialchars($p->Name); ?>
                                    <?php if ($alreadyRec): ?>
                                        — already recommended as <?php echo htmlspecialchars($alreadyRec->RecommendedRole ?? ''); ?>
                                    <?php else: ?>
                                        (<?php echo $p->CoachRecs + $p->TrainerRecs; ?> rec<?php echo ($p->CoachRecs + $p->TrainerRecs) != 1 ? 's' : ''; ?> so far)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recommendedRole">Recommended Role <span style="color:red">*</span></label>
                        <select name="recommendedRole" id="recommendedRole" required>
                            <option value="">-- Select role --</option>
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
                        <textarea name="reason" id="reason" required placeholder="Why are you recommending this player for this tournament?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="comments">Additional Comments</label>
                        <textarea name="comments" id="comments" style="min-height:70px;" placeholder="Optional additional notes..."></textarea>
                    </div>

                    <div style="display:flex;gap:12px;margin-top:4px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Submit Recommendation</button>
                        <a href="<?php echo URLROOT; ?>/coach/tournament_detail/<?php echo $t->TournamentID; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <!-- Applicants overview -->
            <div class="form-card">
                <h3 style="margin:0 0 14px;font-size:15px;color:#1e293b;"><i class="fas fa-clipboard-list" style="color:#64748b;"></i> All Applicants</h3>
                <table class="recs-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Coach Recs</th>
                            <th>Trainer Recs</th>
                            <th>My Rec</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['players'] as $p): ?>
                        <?php $myRec = $alreadyRecommended[$p->PlayerID] ?? null; ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p->Name); ?></strong><br><span style="font-size:11px;color:#94a3b8;"><?php echo htmlspecialchars($p->Email ?? ''); ?></span></td>
                            <td><?php echo $p->CoachRecs; ?></td>
                            <td><?php echo $p->TrainerRecs; ?></td>
                            <td>
                                <?php if ($myRec): ?>
                                    <span class="rec-badge"><i class="fas fa-star"></i> <?php echo htmlspecialchars($myRec->RecommendedRole ?? 'Recommended'); ?></span>
                                <?php else: ?>
                                    <span style="color:#94a3b8;font-size:12px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
