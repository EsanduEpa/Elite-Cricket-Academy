<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">

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
                    <h1><i class="fas fa-calendar-plus"></i> Generate Occurrences</h1>
                    <p>Select a template and date range to create calendar occurrences from the recurring schedule.</p>
                </div>
            </div>
        </div>

        <!-- Sub-nav -->
        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Private Requests</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px; max-width:680px;">

            <?php if (!empty($data['notice'])): ?>
                <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['notice']) ?>
                </div>
            <?php endif; ?>

            <?php if ($data['error']): ?>
                <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>

            <?php if ($data['result'] !== null): ?>
                <?php $r = $data['result']; ?>
                <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:16px 20px;border-radius:8px;margin-bottom:24px;">
                    <div style="font-weight:700;font-size:15px;margin-bottom:6px;"><i class="fas fa-check-circle"></i> Generation Complete</div>
                    <div>Inserted: <strong><?= $r['inserted'] ?></strong> &nbsp;|&nbsp; Skipped (already existed): <strong><?= $r['skipped'] ?></strong></div>
                    <?php if (!empty($r['skipped_dates'])): ?>
                        <div style="margin-top:10px;font-size:12px;color:#155724;">
                            <strong>Skipped dates (facility already booked that slot):</strong><br>
                            <?= implode(', ', array_map('htmlspecialchars', $r['skipped_dates'])) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($r['inserted'] > 0): ?>
                        <div style="margin-top:12px;">
                            <a href="<?php echo URLROOT; ?>/adminslots/calendar" style="color:#155724;text-decoration:underline;font-size:13px;">
                                <i class="fas fa-calendar-alt"></i> View Calendar →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['existingOccurrences'])): ?>
                <?php
                $existingOccurrences = $data['existingOccurrences'];
                $latestOccurrence = $existingOccurrences[0];
                $oldestOccurrence = $existingOccurrences[count($existingOccurrences) - 1];
                ?>
                <div style="background:#fff;border-radius:12px;padding:24px 28px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px;">
                    <div style="display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;align-items:flex-start;margin-bottom:14px;">
                        <div>
                            <h3 style="margin:0 0 6px;font-size:18px;color:#2c3e50;"><i class="fas fa-list"></i> Existing Generated Occurrences</h3>
                            <p style="margin:0;font-size:13px;color:#6c757d;">This template already has generated occurrences in the system.</p>
                        </div>
                        <div style="font-size:12px;color:#475467;line-height:1.6;">
                            <div><strong>Total:</strong> <?= count($existingOccurrences) ?></div>
                            <div><strong>Duration:</strong> <?= htmlspecialchars(date('d M Y', strtotime($oldestOccurrence->OccurrenceDate))) ?> to <?= htmlspecialchars(date('d M Y', strtotime($latestOccurrence->OccurrenceDate))) ?></div>
                        </div>
                    </div>

                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="text-align:left;font-size:12px;color:#667085;padding:10px 8px;border-bottom:1px solid #e6ebf0;">Date</th>
                                    <th style="text-align:left;font-size:12px;color:#667085;padding:10px 8px;border-bottom:1px solid #e6ebf0;">Time</th>
                                    <th style="text-align:left;font-size:12px;color:#667085;padding:10px 8px;border-bottom:1px solid #e6ebf0;">Facility</th>
                                    <th style="text-align:left;font-size:12px;color:#667085;padding:10px 8px;border-bottom:1px solid #e6ebf0;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($existingOccurrences, 0, 8) as $occurrence): ?>
                                    <tr>
                                        <td style="font-size:13px;color:#334155;padding:10px 8px;border-bottom:1px solid #eef2f6;"><?= htmlspecialchars(date('d M Y', strtotime($occurrence->OccurrenceDate))) ?></td>
                                        <td style="font-size:13px;color:#334155;padding:10px 8px;border-bottom:1px solid #eef2f6;"><?= htmlspecialchars($occurrence->SlotLabel ?? (($occurrence->StartTime ?? '') . ' - ' . ($occurrence->EndTime ?? ''))) ?></td>
                                        <td style="font-size:13px;color:#334155;padding:10px 8px;border-bottom:1px solid #eef2f6;"><?= htmlspecialchars($occurrence->FacilityName ?? '—') ?></td>
                                        <td style="font-size:13px;color:#334155;padding:10px 8px;border-bottom:1px solid #eef2f6;"><?= htmlspecialchars(ucfirst((string)$occurrence->Status)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (count($existingOccurrences) > 8): ?>
                        <div style="margin-top:10px;font-size:12px;color:#667085;">
                            Showing the latest 8 occurrences.
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif ((int)($data['selectedTemplateId'] ?? 0) > 0): ?>
                <div style="background:#fff;border-radius:12px;padding:20px 24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px;color:#667085;font-size:13px;">
                    <i class="fas fa-info-circle"></i> No generated occurrences exist yet for the selected template.
                </div>
            <?php endif; ?>

            <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <form method="POST">

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-weight:600;font-size:13px;color:#555;margin-bottom:6px;">Template <span style="color:#e74c3c;">*</span></label>
                        <?php if (empty($data['templates'])): ?>
                            <p style="color:#888;font-size:13px;">No active templates found. <a href="<?php echo URLROOT; ?>/adminslots/newtemplate">Create one first.</a></p>
                        <?php else: ?>
                            <select name="template_id" id="templateSelect" required style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;color:#333;">
                                <option value="">— Select a template —</option>
                                <?php foreach ($data['templates'] as $t): ?>
                                    <option value="<?= $t->TemplateID ?>"
                                        <?= ((int)($data['selectedTemplateId'] ?? 0) === (int)$t->TemplateID) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(($t->temp_code ?? ('T' . $t->TemplateID)) . ' — ' . $t->TemplateName) ?>
                                        <?php if ($t->DayOfWeek): ?>
                                            <?php $days = ['','Mon','Tue','Wed','Thu','Fri','Sat','Sun']; ?>
                                            — every <?= $days[(int)$t->DayOfWeek] ?>
                                        <?php else: ?>
                                            — academy event (any day)
                                        <?php endif; ?>
                                        (<?= htmlspecialchars($t->SlotLabel ?? 'No band') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div id="dayOfWeekWarning" style="background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;display:none;">
                        <i class="fas fa-exclamation-triangle"></i> <span id="dayOfWeekText"></span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label style="display:block;font-weight:600;font-size:13px;color:#555;margin-bottom:6px;">From Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" name="from_date" id="fromDate" required
                                   value="<?= htmlspecialchars($_POST['from_date'] ?? '') ?>"
                                   style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;box-sizing:border-box;">
                            <div id="fromDateError" style="color:#e74c3c;font-size:12px;margin-top:4px;display:none;"></div>
                        </div>
                        <div>
                            <label style="display:block;font-weight:600;font-size:13px;color:#555;margin-bottom:6px;">To Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" name="to_date" id="toDate" required
                                   value="<?= htmlspecialchars($_POST['to_date'] ?? '') ?>"
                                   style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;box-sizing:border-box;">
                            <div id="toDateError" style="color:#e74c3c;font-size:12px;margin-top:4px;display:none;"></div>
                        </div>
                    </div>

                    <div style="background:#fff8e1;border-left:4px solid #f39c12;padding:12px 16px;border-radius:4px;font-size:13px;color:#7a5200;margin-bottom:24px;">
                        <strong>How it works:</strong> The system loops every date in the range and creates one occurrence per day that matches the template's day-of-week setting. If a facility conflict exists on a date, that date is skipped and reported — no error, no duplicates.
                    </div>

                    <?php if (!empty($data['templates'])): ?>
                        <button type="submit" style="padding:10px 28px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-cogs"></i> Generate
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script>
// Template day-of-week data
const templateDayOfWeek = <?php
    $templates = $data['templates'] ?? [];
    $dayMap = [];
    foreach ($templates as $t) {
        if ($t->TemplateID) {
            $dayMap[(int)$t->TemplateID] = (int)($t->DayOfWeek ?? 0);
        }
    }
    echo json_encode($dayMap);
?>;

const dayNames = ['Any Day', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('templateSelect');
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    const dayOfWeekWarning = document.getElementById('dayOfWeekWarning');
    const dayOfWeekText = document.getElementById('dayOfWeekText');
    const fromDateError = document.getElementById('fromDateError');
    const toDateError = document.getElementById('toDateError');

    function getSelectedTemplateDay() {
        const templateId = templateSelect.value;
        return templateDayOfWeek[templateId] !== undefined ? templateDayOfWeek[templateId] : null;
    }

    function validateDate(dateString, allowAnyDay = false) {
        if (!dateString) return { valid: true };
        const selectedDay = getSelectedTemplateDay();
        if (selectedDay === null || selectedDay === 0) {
            // Academy event - any day allowed
            return { valid: true };
        }
        const date = new Date(dateString);
        const dayOfWeek = (date.getDay() + 6) % 7 + 1; // Convert JS day (0=Sun) to PHP day (1=Mon)
        const isValid = dayOfWeek === selectedDay;
        return {
            valid: isValid,
            dayOfWeek: dayOfWeek,
            expectedDay: selectedDay,
            dateStr: date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' })
        };
    }

    function updateWarningMessage() {
        const selectedDay = getSelectedTemplateDay();
        const templateId = templateSelect.value;
        
        if (!templateId) {
            dayOfWeekWarning.style.display = 'none';
            return;
        }

        if (selectedDay === null || selectedDay === 0) {
            dayOfWeekWarning.style.display = 'none';
            return;
        }

        dayOfWeekWarning.style.display = 'block';
        dayOfWeekText.innerHTML = `This template is scheduled for <strong>${dayNames[selectedDay]}</strong> only. You can only generate occurrences on dates that fall on ${dayNames[selectedDay]}.`;
    }

    function validateAllDates() {
        let hasErrors = false;
        
        // Validate from date
        if (fromDateInput.value) {
            const validation = validateDate(fromDateInput.value);
            if (!validation.valid) {
                fromDateError.textContent = `Invalid date. ${validation.dateStr} is a ${dayNames[validation.dayOfWeek]}, but this template requires ${dayNames[validation.expectedDay]}.`;
                fromDateError.style.display = 'block';
                hasErrors = true;
            } else {
                fromDateError.style.display = 'none';
            }
        }

        // Validate to date
        if (toDateInput.value) {
            const validation = validateDate(toDateInput.value);
            if (!validation.valid) {
                toDateError.textContent = `Invalid date. ${validation.dateStr} is a ${dayNames[validation.dayOfWeek]}, but this template requires ${dayNames[validation.expectedDay]}.`;
                toDateError.style.display = 'block';
                hasErrors = true;
            } else {
                toDateError.style.display = 'none';
            }
        }

        return !hasErrors;
    }

    // Template selection change
    if (templateSelect) {
        templateSelect.addEventListener('change', function() {
            updateWarningMessage();
            validateAllDates();
            
            const templateId = templateSelect.value;
            const url = new URL('<?php echo URLROOT; ?>/adminslots/generate', window.location.origin);

            if (templateId) {
                url.searchParams.set('template_id', templateId);
            }

            window.location.href = url.toString();
        });
    }

    // Date input validation
    if (fromDateInput) {
        fromDateInput.addEventListener('change', validateAllDates);
        fromDateInput.addEventListener('blur', validateAllDates);
    }

    if (toDateInput) {
        toDateInput.addEventListener('change', validateAllDates);
        toDateInput.addEventListener('blur', validateAllDates);
    }

    // Initialize on page load
    updateWarningMessage();
    validateAllDates();
});
</script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
