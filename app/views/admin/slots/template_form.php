<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.form-card      { background:#fff;border-radius:12px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.08);max-width:800px; }
.form-row       { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px; }
.form-row.full  { grid-template-columns:1fr; }
.form-group label { display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px; }
.form-group input,
.form-group select,
.form-group textarea { width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;box-sizing:border-box; }
.form-group textarea  { resize:vertical;min-height:80px; }
.form-group input:focus,
.form-group select:focus { outline:none;border-color:#3498db;box-shadow:0 0 0 3px rgba(52,152,219,.15); }
.hint { font-size:11px;color:#888;margin-top:4px; }
.btn-save { padding:10px 28px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer; }
.btn-cancel { padding:10px 20px;background:#ecf0f1;color:#333;border:none;border-radius:8px;font-size:15px;cursor:pointer;text-decoration:none;display:inline-block; }
</style>

<?php $t = $data['template']; $isEdit = $t !== null; ?>

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
                    <div style="font-size:12px;color:#aaa;margin-bottom:4px;">
                        <a href="<?php echo URLROOT; ?>/adminslots/templates" style="color:#3498db;text-decoration:none;">Templates</a>
                        <i class="fas fa-chevron-right" style="font-size:10px;margin:0 6px;"></i>
                        <?= $isEdit ? 'Edit' : 'New Template' ?>
                    </div>
                    <h1><i class="fas fa-layer-group"></i> <?= $isEdit ? 'Edit Template' : 'New Session Template' ?></h1>
                    <p>Define a repeating session program, private session offer, or facility-only slot.</p>
                </div>
            </div>
        </div>

        <div style="padding:0 25px 40px;">
            <form method="POST" class="form-card">

                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Template Name <span style="color:#e74c3c;">*</span></label>
                        <input type="text" name="TemplateName" required value="<?= htmlspecialchars($t->TemplateName ?? '') ?>" placeholder="e.g. U15 Batting Practice">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Slot Type <span style="color:#e74c3c;">*</span></label>
                        <select name="SlotType" required>
                            <option value="program"       <?= ($t->SlotType??'') === 'program'        ? 'selected' : '' ?>>Program (recurring group, subscription-covered)</option>
                            <option value="private"       <?= ($t->SlotType??'') === 'private'        ? 'selected' : '' ?>>Private (1-on-1, paid on request)</option>
                            <option value="facility_only" <?= ($t->SlotType??'') === 'facility_only'  ? 'selected' : '' ?>>Facility Only (no staff, self-book)</option>
                        </select>
                        <p class="hint">Program = admin enrolls players. Private = player/admin requests. Facility = player self-books net/bowling machine.</p>
                    </div>
                    <div class="form-group">
                        <label>Staff Type <span style="color:#e74c3c;">*</span></label>
                        <select name="StaffType" required>
                            <option value="coach"   <?= ($t->StaffType??'') === 'coach'   ? 'selected' : '' ?>>Coach</option>
                            <option value="trainer" <?= ($t->StaffType??'') === 'trainer' ? 'selected' : '' ?>>Trainer</option>
                            <option value="none"    <?= ($t->StaffType??'') === 'none'    ? 'selected' : '' ?>>None (facility only)</option>
                        </select>
                        <p class="hint">Controls which staff can be assigned and which post-session log is prompted.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Time Band <span style="color:#e74c3c;">*</span></label>
                        <select name="SlotID" required>
                            <?php foreach ($data['timeBands'] as $band): ?>
                                <option value="<?= $band->SlotID ?>" <?= ($t->SlotID??0) == $band->SlotID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($band->SlotLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Day of Week</label>
                        <select name="DayOfWeek">
                            <option value="">— No fixed day (ad-hoc / private) —</option>
                            <option value="1" <?= ($t->DayOfWeek??'') == 1 ? 'selected' : '' ?>>Monday</option>
                            <option value="2" <?= ($t->DayOfWeek??'') == 2 ? 'selected' : '' ?>>Tuesday</option>
                            <option value="3" <?= ($t->DayOfWeek??'') == 3 ? 'selected' : '' ?>>Wednesday</option>
                            <option value="4" <?= ($t->DayOfWeek??'') == 4 ? 'selected' : '' ?>>Thursday</option>
                            <option value="5" <?= ($t->DayOfWeek??'') == 5 ? 'selected' : '' ?>>Friday</option>
                            <option value="6" <?= ($t->DayOfWeek??'') == 6 ? 'selected' : '' ?>>Saturday</option>
                            <option value="7" <?= ($t->DayOfWeek??'') == 7 ? 'selected' : '' ?>>Sunday</option>
                        </select>
                        <p class="hint">Leave blank for private or ad-hoc sessions.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Facility</label>
                        <select name="FacilityID">
                            <option value="">— Assigned per occurrence —</option>
                            <?php foreach ($data['facilities'] as $f): ?>
                                <option value="<?= $f->FacilityID ?>" <?= ($t->FacilityID??'') == $f->FacilityID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f->Name) ?> (#<?= $f->FacilityID ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Required Plan Feature</label>
                        <select name="RequiredPlanFeature">
                            <option value="none"               <?= ($t->RequiredPlanFeature??'') === 'none'               ? 'selected' : '' ?>>None (open to all)</option>
                            <option value="sessions"           <?= ($t->RequiredPlanFeature??'') === 'sessions'           ? 'selected' : '' ?>>Sessions (SessionsPerWeek > 0)</option>
                            <option value="private_sessions"   <?= ($t->RequiredPlanFeature??'') === 'private_sessions'   ? 'selected' : '' ?>>Private Sessions included</option>
                            <option value="facility_access"    <?= ($t->RequiredPlanFeature??'') === 'facility_access'    ? 'selected' : '' ?>>Facility Access included</option>
                        </select>
                        <p class="hint">Checked against the player's membership plan at booking time.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Age Group</label>
                        <input type="text" name="AgeGroup" value="<?= htmlspecialchars($t->AgeGroup ?? '') ?>" placeholder="e.g. Under 15, Under 19, Open">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="Category" value="<?= htmlspecialchars($t->Category ?? '') ?>" placeholder="e.g. Batting, Bowling, Fitness">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Max Participants <span style="color:#e74c3c;">*</span></label>
                        <input type="number" name="MaxParticipants" min="1" max="100" required value="<?= $t->MaxParticipants ?? 10 ?>">
                    </div>
                    <div class="form-group">
                        <label>Price Per Session (Rs.)</label>
                        <input type="number" name="PricePerSession" min="0" step="0.01" value="<?= $t->PricePerSession ?? '0.00' ?>">
                        <p class="hint">Enter 0 for subscription-covered sessions.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Recurrence Start <span style="color:#e74c3c;">*</span></label>
                        <input type="date" name="RecurrenceStart" required value="<?= $t->RecurrenceStart ?? date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Recurrence End</label>
                        <input type="date" name="RecurrenceEnd" value="<?= $t->RecurrenceEnd ?? '' ?>">
                        <p class="hint">Leave blank for an open-ended season.</p>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="Description" placeholder="Optional description of what this session covers..."><?= htmlspecialchars($t->Description ?? '') ?></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> <?= $isEdit ? 'Save Changes' : 'Create Template' ?></button>
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
