<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach  = $data['role'] === 'Coach';
$trainerSidebarActive = 'slots';
$cssFile  = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout   = $isCoach ? 'coach-layout'    : 'trainer-layout';
$sidebar  = $isCoach ? 'coach-sidebar'   : 'trainer-sidebar';
$logo     = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
$post     = $data['post'] ?? [];  // repopulate form on validation error
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/<?= $cssFile ?>.css">
<style>
.form-group       { margin-bottom:20px; }
.form-group label { display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px; }
.form-group input,
.form-group select,
.form-group textarea {
    width:100%;padding:10px 13px;border:1px solid #ced4da;border-radius:7px;
    font-size:14px;color:#333;box-sizing:border-box;
}
.form-group textarea  { height:90px;resize:vertical; }
.form-group small     { display:block;font-size:11px;color:#888;margin-top:4px; }
.form-grid            { display:grid;grid-template-columns:1fr 1fr;gap:20px; }
.alert-error          { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.required-star        { color:#dc3545; }
</style>

<div class="<?= $layout ?>">
    <!-- ── Sidebar ── -->
    <div class="<?= $sidebar ?>" id="staffSidebar">
        <div class="sidebar-header">
            <div class="<?= $isCoach ? 'coach-logo' : 'trainer-info' ?>">
                <?php if ($isCoach): ?>
                    <i class="fas <?= $logo ?>"></i><h3>Coach Panel</h3>
                <?php else: ?>
                    <div class="trainer-avatar"><i class="fas <?= $logo ?>"></i></div>
                    <div class="trainer-details">
                        <h4><?= htmlspecialchars($_SESSION['user_name'] ?? 'Trainer') ?></h4>
                        <p>Physical Trainer</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>

        <?php if ($isCoach): ?>
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard"  class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/sessions"   class="nav-link"><i class="fas fa-calendar-alt"></i><span>Sessions</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players"    class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link"><i class="fas fa-star"></i><span>Recommendations</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/health"     class="nav-link"><i class="fas fa-heartbeat"></i><span>Health &amp; Injury</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link"><i class="fas fa-bell"></i><span>Notifications</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/events"     class="nav-link"><i class="fas fa-calendar"></i><span>Events</span></a></li>
                </ul>
            </nav>
        <?php else: ?>
            <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        <?php endif; ?>

        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ($data['role'] ?? 'Staff'); ?>
                </div>
                <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                    <a href="<?php echo URLROOT; ?>/<?php echo strtolower($data['role'] ?? 'coach'); ?>/profile" class="profile-avatar" aria-label="Open profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Main Content ── -->
    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                        <h1><i class="fas fa-plus-circle"></i> Request Private Session</h1>
                        <p>Submit a session request for admin review and approval.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar"
                       style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">
                        <i class="fas fa-calendar-alt"></i> Back to Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/staffslots/calendar"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-calendar-alt"></i> Calendar
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/past_requests"
               style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">
                <i class="fas fa-history"></i> Past Requests
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/private_session"
               style="padding:7px 16px;border-radius:6px;background:#2e7d32;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">
                <i class="fas fa-plus-circle"></i> Request Private Session
            </a>
        </div>

        <div style="padding:0 25px 40px;max-width:760px;">

            <?php flash('session_message'); ?>
            <?php if ($data['error']): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <!-- Info box -->
            <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:16px 18px;margin-bottom:24px;font-size:13px;color:#1b5e20;">
                <i class="fas fa-info-circle"></i>
                A <strong>private session request</strong> is reviewed by admin before it becomes an actual session.
                If the requested facility is free on that date and time, admin will approve the request and create the session for you.
                You will be assigned as the lead staff member once approved.
            </div>

            <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <form method="POST" action="<?php echo URLROOT; ?>/staffslots/private_session">

                    <div class="form-grid">

                        <!-- Date -->
                        <div class="form-group">
                            <label for="OccurrenceDate">Session Date <span class="required-star">*</span></label>
                            <input type="date" name="OccurrenceDate" id="OccurrenceDate"
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?= htmlspecialchars($post['OccurrenceDate'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- Time Band -->
                        <div class="form-group">
                            <label for="SlotID">Time Band <span class="required-star">*</span></label>
                            <select name="SlotID" id="SlotID" required>
                                <option value="">— Select time band —</option>
                                <?php foreach ($data['timeBands'] as $band): ?>
                                    <option value="<?= (int)$band->SlotID ?>"
                                        <?= ($post['SlotID'] ?? '') == $band->SlotID ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($band->SlotLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Facility -->
                        <div class="form-group">
                            <label for="FacilityID">Facility <span class="required-star">*</span></label>
                            <select name="FacilityID" id="FacilityID" required>
                                <option value="">— Select facility —</option>
                                <?php foreach ($data['facilities'] as $fac): ?>
                                    <option value="<?= (int)$fac->FacilityID ?>"
                                        <?= ($post['FacilityID'] ?? '') == $fac->FacilityID ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($fac->Name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small>A facility must be free for that date + time band (no double-booking).</small>
                        </div>

                        <!-- Max Participants -->
                        <div class="form-group">
                            <label for="MaxParticipants">Max Participants</label>
                            <input type="number" name="MaxParticipants" id="MaxParticipants"
                                   min="1" max="100"
                                   value="<?= htmlspecialchars($post['MaxParticipants'] ?? '10') ?>">
                            <small>Admin will use this as the requested session capacity.</small>
                        </div>

                    </div>

                    <!-- Notes (full width) -->
                    <div class="form-group">
                        <label for="Notes">Notes / Description <span style="color:#888;font-weight:400;">(optional)</span></label>
                        <textarea name="Notes" id="Notes"
                                  placeholder="e.g. Focus on batting drills, bring pads…"><?= htmlspecialchars($post['Notes'] ?? '') ?></textarea>
                    </div>

                    <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
                        <button type="submit"
                                style="padding:11px 28px;background:#2e7d32;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-paper-plane"></i> Send Request
                        </button>
                        <a href="<?php echo URLROOT; ?>/staffslots/calendar"
                           style="padding:11px 20px;background:#ecf0f1;color:#333;border-radius:8px;text-decoration:none;font-size:14px;">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
