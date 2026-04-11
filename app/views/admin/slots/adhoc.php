<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.form-group { margin-bottom:20px; }
.form-group label { display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px; }
.form-group input,
.form-group select,
.form-group textarea {
    width:100%;padding:10px 13px;border:1px solid #ced4da;border-radius:7px;
    font-size:14px;color:#333;box-sizing:border-box;
}
.form-group textarea { height:90px;resize:vertical; }
.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:20px; }
.alert-error { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
</style>

<div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo"><i class="fas fa-user-shield"></i><h3>Admin Dashboard</h3></div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/staff" class="nav-link"><i class="fas fa-users-cog"></i><span>Staff Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/players" class="nav-link"><i class="fas fa-user-graduate"></i><span>Player Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/events" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Events &amp; Tournaments</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/feedback" class="nav-link"><i class="fas fa-comments"></i><span>Feedback</span></a></li>
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/finance" class="nav-link"><i class="fas fa-chart-line"></i><span>Finance</span></a></li>
            </ul>
        </nav>
        <div class="admin-profile">
            <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
            <div class="profile-info"><span class="admin-name">Admin</span><span class="admin-role">Super Administrator</span></div>
            <div class="logout-btn"><a href="<?php echo URLROOT; ?>/login/logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-plus-circle"></i> New Ad-hoc Session</h1>
                    <p>Create a one-off occurrence not tied to any recurring template.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/calendar"
                       style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">
                        <i class="fas fa-calendar-alt"></i> View Calendar
                    </a>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#2c3e50;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Ad-hoc Session</a>
        </div>

        <div style="padding:0 25px 40px; max-width:760px;">

            <?php if ($data['error']): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <!-- Info box -->
            <div style="background:#e8f4fd;border:1px solid #bee5eb;border-radius:8px;padding:16px 18px;margin-bottom:24px;font-size:13px;color:#2c7a7b;">
                <i class="fas fa-info-circle"></i>
                An <strong>ad-hoc session</strong> is a one-off occurrence with no recurring template — ideal for special coaching sessions, trials, makeup sessions, and similar events.
                After creation you will be taken to the occurrence page to assign substitute staff if needed.
            </div>

            <!-- Form card -->
            <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 22px;font-size:15px;color:#2c3e50;"><i class="fas fa-edit"></i> Session Details</h3>

                <form method="POST" id="adhocForm">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="OccurrenceDate">Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" id="OccurrenceDate" name="OccurrenceDate" required
                                   value="<?= htmlspecialchars($_POST['OccurrenceDate'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="SlotID">Time Band <span style="color:#e74c3c;">*</span></label>
                            <select id="SlotID" name="SlotID" required>
                                <option value="">— Select time band —</option>
                                <?php foreach ($data['timeBands'] as $band): ?>
                                    <option value="<?= $band->SlotID ?>"
                                        <?= (isset($_POST['SlotID']) && (int)$_POST['SlotID'] === (int)$band->SlotID) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($band->SlotLabel) ?>
                                        (<?= date('g:ia', strtotime($band->StartTime)) ?>–<?= date('g:ia', strtotime($band->EndTime)) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="FacilityID">Facility <span style="color:#e74c3c;">*</span></label>
                            <select id="FacilityID" name="FacilityID" required>
                                <option value="">— Select facility —</option>
                                <?php foreach ($data['facilities'] as $fac): ?>
                                    <option value="<?= $fac->FacilityID ?>"
                                        <?= (isset($_POST['FacilityID']) && (int)$_POST['FacilityID'] === (int)$fac->FacilityID) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($fac->Name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="MaxParticipants">Max Participants <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                            <input type="number" id="MaxParticipants" name="MaxParticipants" min="1" max="200"
                                   placeholder="Leave blank for no limit"
                                   value="<?= htmlspecialchars($_POST['MaxParticipants'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Notes">Notes <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                        <textarea id="Notes" name="Notes" placeholder="Any additional information about this ad-hoc session…"><?= htmlspecialchars($_POST['Notes'] ?? '') ?></textarea>
                    </div>

                    <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
                        <button type="submit"
                                style="padding:10px 24px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                            <i class="fas fa-plus"></i> Create Ad-hoc Session
                        </button>
                        <a href="<?php echo URLROOT; ?>/adminslots/calendar"
                           style="padding:10px 18px;border-radius:8px;background:#ecf0f1;color:#555;text-decoration:none;font-size:14px;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
