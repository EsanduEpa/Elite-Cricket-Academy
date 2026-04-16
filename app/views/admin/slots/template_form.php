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
.stack-layout    { display:flex;flex-direction:column;gap:20px;max-width:800px; }
</style>

<?php
$t = $data['template'];
$isEdit = $t !== null;
$requiredPlanValue = trim((string)($t->RequiredPlanFeature ?? ''));
$slotTypeValue = (string)($t->SlotType ?? '');
$pricePerSessionValue = $t->PricePerSession ?? '0.00';
$ageGroupOptions = ['Under 11', 'Under 13', 'Under 15', 'Under 17', 'Under 19', 'Under 21', 'Open'];
$categoryOptions = ['Batting', 'Bowling', 'Fielding', 'Fitness'];
$requiredPlanOptions = [
    'sessions' => 'Group Sessions',
    'facility_access' => 'Facility Access',
];
?>

<div class="admin-layout">
    <!-- Sidebar -->
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
                <li class="nav-item"><a href="<?php echo URLROOT; ?>/admin/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                <li class="nav-item active"><a href="<?php echo URLROOT; ?>/adminslots/templates" class="nav-link"><i class="fas fa-clock"></i><span>Slot Management</span></a></li>
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
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Back To Templates
                    </a>
                </div>
            </div>
        </div>
        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Private Requests</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>
        <div style="padding:0 25px 40px;">
            <div class="stack-layout">
            <?php if (!empty($data['error'])): ?>
                <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="form-card">
                <input type="hidden" name="form_action" value="save_template">

                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Template Name <span style="color:#e74c3c;">*</span></label>
                        <input type="text" name="TemplateName" required value="<?= htmlspecialchars($t->TemplateName ?? '') ?>" placeholder="e.g. U15 Batting Practice">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Template Code</label>
                        <input type="text" name="temp_code" required value="<?= htmlspecialchars($t->temp_code ?? '') ?>" placeholder="e.g. U15-BAT-PN1">
                        <p class="hint">Enter a unique code manually. This is stored exactly as typed.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Slot Type <span style="color:#e74c3c;">*</span></label>
                        <select name="SlotType" required>
                            <?php if (empty($t->SlotType)): ?>
                            <option value="" disabled selected>— select type —</option>
                            <?php endif; ?>
                            <option value="program"       <?= ($t->SlotType??'') === 'program'        ? 'selected' : '' ?>>Program (recurring group, subscription-covered)</option>
                            <option value="private"       <?= ($t->SlotType??'') === 'private'        ? 'selected' : '' ?>>Private (1-on-1,subscription-covered)</option>
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
                            <option value="1" <?= ($t->DayOfWeek??'') == 1 ? 'selected' : '' ?>>Monday</option>
                            <option value="2" <?= ($t->DayOfWeek??'') == 2 ? 'selected' : '' ?>>Tuesday</option>
                            <option value="3" <?= ($t->DayOfWeek??'') == 3 ? 'selected' : '' ?>>Wednesday</option>
                            <option value="4" <?= ($t->DayOfWeek??'') == 4 ? 'selected' : '' ?>>Thursday</option>
                            <option value="5" <?= ($t->DayOfWeek??'') == 5 ? 'selected' : '' ?>>Friday</option>
                            <option value="6" <?= ($t->DayOfWeek??'') == 6 ? 'selected' : '' ?>>Saturday</option>
                            <option value="7" <?= ($t->DayOfWeek??'') == 7 ? 'selected' : '' ?>>Sunday</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Facility</label>
                        <select name="FacilityID" id="facilityField">
                            <option value="">— Assigned per occurrence —</option>
                            <?php foreach ($data['facilities'] as $f): ?>
                                <option value="<?= $f->FacilityID ?>" data-rate="<?= htmlspecialchars(number_format((float)($f->HourlyRate ?? 0), 2, '.', '')) ?>" <?= ($t->FacilityID??'') == $f->FacilityID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f->Name) ?> (#<?= $f->FacilityID ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Required Plan Feature <span style="color:#e74c3c;">*</span></label>
                        <select name="RequiredPlanFeature" required>
                            <option value="" disabled <?= isset($requiredPlanOptions[$requiredPlanValue]) ? '' : 'selected' ?>>— select required plan feature —</option>
                            <?php foreach ($requiredPlanOptions as $optionValue => $optionLabel): ?>
                                <option value="<?= htmlspecialchars($optionValue) ?>" <?= $requiredPlanValue === $optionValue ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($optionLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="hint">Choose the subscription feature a player must have before booking this template.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Age Group</label>
                        <select name="AgeGroup">
                            <option value="">— Select age group —</option>
                            <?php foreach ($ageGroupOptions as $ageGroup): ?>
                                <option value="<?= htmlspecialchars($ageGroup) ?>" <?= ($t->AgeGroup ?? '') === $ageGroup ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ageGroup) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="Category">
                            <option value="">— Select category —</option>
                            <?php foreach ($categoryOptions as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>" <?= ($t->Category ?? '') === $category ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Max Participants <span style="color:#aaa;font-weight:400;">(facility-only only)</span></label>
                        <input type="number" name="MaxParticipants" id="maxParticipantsField" min="1" max="100" value="<?= htmlspecialchars($t->MaxParticipants ?? '') ?>" <?= $slotTypeValue === 'facility_only' ? '' : 'disabled' ?>>
                        <p class="hint" id="maxParticipantsHint">Only facility-only templates use a participant limit.</p>
                    </div>
                    <div class="form-group" id="pricePerSessionGroup">
                        <label>Price Per Session (Rs.)</label>
                        <input type="number" name="PricePerSession" id="pricePerSessionField" min="0" step="0.01" value="<?= htmlspecialchars((string)$pricePerSessionValue) ?>" <?= $slotTypeValue === 'facility_only' ? 'readonly' : 'disabled' ?>>
                        <p class="hint" id="pricePerSessionHint">Only facility-only templates can carry a direct session price, and it is loaded from the selected facility.</p>
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
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" class="btn-cancel">Back To Templates</a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slotTypeField = document.querySelector('select[name="SlotType"]');
    const facilityField = document.getElementById('facilityField');
    const maxParticipantsField = document.getElementById('maxParticipantsField');
    const maxParticipantsHint = document.getElementById('maxParticipantsHint');
    const pricePerSessionField = document.getElementById('pricePerSessionField');
    const pricePerSessionHint = document.getElementById('pricePerSessionHint');

    if (!slotTypeField || !facilityField || !maxParticipantsField || !maxParticipantsHint || !pricePerSessionField || !pricePerSessionHint) {
        return;
    }

    function getSelectedFacilityRate() {
        const selectedOption = facilityField.options[facilityField.selectedIndex];
        return selectedOption ? (selectedOption.dataset.rate || '0.00') : '0.00';
    }

    function syncMaxParticipantsHelp() {
        if (slotTypeField.value === 'facility_only') {
            maxParticipantsField.disabled = false;
            maxParticipantsField.placeholder = 'Enter facility capacity';
            maxParticipantsHint.textContent = 'Set the maximum group size allowed inside one facility reservation.';
        } else {
            maxParticipantsField.value = '';
            maxParticipantsField.disabled = true;
            maxParticipantsField.placeholder = '';
            maxParticipantsHint.textContent = 'Only facility-only templates use a participant limit.';
        }
    }

    function syncPricePerSession() {
        if (slotTypeField.value === 'facility_only') {
            pricePerSessionField.disabled = false;
            pricePerSessionField.readOnly = true;
            pricePerSessionField.value = getSelectedFacilityRate();
            pricePerSessionHint.textContent = 'This price is pulled from the selected facility hourly rate.';
        } else {
            pricePerSessionField.value = '0.00';
            pricePerSessionField.disabled = true;
            pricePerSessionField.readOnly = false;
            pricePerSessionHint.textContent = 'Only facility-only templates can carry a direct session price, and it is loaded from the selected facility.';
        }
    }

    slotTypeField.addEventListener('change', syncMaxParticipantsHelp);
    slotTypeField.addEventListener('change', syncPricePerSession);
    facilityField.addEventListener('change', syncPricePerSession);
    syncMaxParticipantsHelp();
    syncPricePerSession();
});
</script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
