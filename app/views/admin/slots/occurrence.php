<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.occ-status-scheduled { background:#cce5ff;color:#004085;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.occ-status-active    { background:#d4edda;color:#155724;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.occ-status-cancelled { background:#f8d7da;color:#721c24;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.occ-status-completed { background:#e2d9f3;color:#4a1e8c;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700; }
.detail-card  { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px; }
.detail-grid  { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.detail-label { font-size:12px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px; }
.detail-value { font-size:14px;color:#2c3e50;font-weight:500; }
.badge-lead,
.badge-substitute,
.badge-assistant { padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600; }
.badge-lead      { background:#cce5ff;color:#004085; }
.badge-assistant { background:#d4edda;color:#155724; }
.badge-substitute{ background:#fff3cd;color:#856404; }
.alert-error     { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.alert-success   { background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
</style>

<?php
$occ  = $data['occurrence'];
$staff = $data['staff'];
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
                    <h1><i class="fas fa-calendar-day"></i> Occurrence Detail</h1>
                    <p>View, cancel, or substitute staff for this single calendar date.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/calendar?start=<?= $occ->OccurrenceDate ?>"
                       style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">
                        <i class="fas fa-arrow-left"></i> Back to Calendar
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
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Ad-hoc Session</a>
        </div>

        <div style="padding:0 25px 40px; max-width:900px;">

            <?php if ($data['error']): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>
            <?php if ($data['success']): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['success']) ?></div>
            <?php endif; ?>

            <!-- ── Occurrence Details ── -->
            <div class="detail-card">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                    <h2 style="margin:0;font-size:18px;color:#2c3e50;">
                        <?= $occ->TemplateName ? htmlspecialchars($occ->TemplateName) : '<em>Ad-hoc Session</em>' ?>
                    </h2>
                    <?php
                    $statusClass = 'occ-status-' . $occ->Status;
                    echo "<span class=\"{$statusClass}\">" . ucfirst($occ->Status) . "</span>";
                    ?>
                </div>

                <div class="detail-grid">
                    <div>
                        <div class="detail-label">Date</div>
                        <div class="detail-value"><?= date('l, j F Y', strtotime($occ->OccurrenceDate)) ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Time Band</div>
                        <div class="detail-value"><?= htmlspecialchars($occ->SlotLabel ?? '—') ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Facility</div>
                        <div class="detail-value"><?= htmlspecialchars($occ->FacilityName ?? '—') ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Slot Type</div>
                        <div class="detail-value"><?= $occ->SlotType ? ucfirst(str_replace('_', ' ', $occ->SlotType)) : '—' ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Bookings</div>
                        <div class="detail-value"><?= (int)$occ->BookingCount ?> player(s) booked</div>
                    </div>
                    <div>
                        <div class="detail-label">Max Participants</div>
                        <div class="detail-value"><?= $occ->MaxParticipants ?? $occ->TemplateMaxParticipants ?? '—' ?></div>
                    </div>
                    <?php if ($occ->Status === 'cancelled' && $occ->CancelReason): ?>
                    <div style="grid-column:1/-1;">
                        <div class="detail-label">Cancellation Reason</div>
                        <div class="detail-value" style="color:#721c24;"><?= htmlspecialchars($occ->CancelReason) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($occ->Notes): ?>
                    <div style="grid-column:1/-1;">
                        <div class="detail-label">Notes</div>
                        <div class="detail-value"><?= htmlspecialchars($occ->Notes) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Staff ── -->
            <div class="detail-card">
                <h3 style="margin:0 0 16px;font-size:15px;color:#2c3e50;"><i class="fas fa-users"></i> Assigned Staff</h3>
                <?php if (empty($staff)): ?>
                    <p style="color:#888;font-size:13px;margin:0;">
                        <?= $occ->TemplateID ? 'No staff assigned to this template yet.' : 'No staff assigned to this ad-hoc occurrence.' ?>
                    </p>
                <?php else: ?>
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Name</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Role</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Type</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Source</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#555;border-bottom:1px solid #dee2e6;">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $s): ?>
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:10px 14px;font-weight:600;"><?= htmlspecialchars($s->UserName) ?></td>
                                <td style="padding:10px 14px;">
                                    <span class="badge-<?= $s->StaffRole ?>"><?= ucfirst($s->StaffRole) ?></span>
                                </td>
                                <td style="padding:10px 14px;font-size:13px;"><?= ucfirst($s->StaffType) ?></td>
                                <td style="padding:10px 14px;font-size:12px;color:#888;"><?= ucfirst($s->Source) ?></td>
                                <td style="padding:10px 14px;font-size:12px;color:#888;">
                                    <?= $s->OverrideReason ? htmlspecialchars($s->OverrideReason) : '—' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- ── Cancel Occurrence ── -->
            <?php if ($occ->Status !== 'cancelled'): ?>
            <div class="detail-card" style="border-left:4px solid #e74c3c;">
                <h3 style="margin:0 0 4px;font-size:15px;color:#e74c3c;"><i class="fas fa-ban"></i> Cancel This Occurrence</h3>
                <p style="color:#888;font-size:13px;margin:0 0 16px;">
                    This cancels only this date — the template and all other occurrences are unaffected.
                    <?php if ($occ->BookingCount > 0): ?>
                        <strong style="color:#e74c3c;"><?= (int)$occ->BookingCount ?> player(s) are booked — Phase 7 will send them notifications.</strong>
                    <?php endif; ?>
                </p>
                <form method="POST">
                    <input type="hidden" name="action_cancel" value="1">
                    <div style="display:flex;gap:12px;align-items:flex-end;">
                        <div style="flex:1;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Reason <span style="color:#e74c3c;">*</span></label>
                            <input type="text" name="cancel_reason" required placeholder="e.g. School holiday, Facility maintenance…"
                                   style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;box-sizing:border-box;">
                        </div>
                        <button type="submit"
                                onclick="return confirm('Cancel this occurrence? This cannot be undone without admin DB access.')"
                                style="padding:9px 20px;background:#e74c3c;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">
                            <i class="fas fa-ban"></i> Cancel Occurrence
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="detail-card" style="border-left:4px solid #aaa;">
                <p style="color:#888;margin:0;font-size:13px;"><i class="fas fa-ban"></i> This occurrence is already cancelled.</p>
            </div>
            <?php endif; ?>

            <!-- ── Substitute Staff ── -->
            <div class="detail-card" style="border-left:4px solid #f39c12;">
                <h3 style="margin:0 0 4px;font-size:15px;color:#e67e22;"><i class="fas fa-user-edit"></i> Substitute Staff (This Date Only)</h3>
                <p style="color:#888;font-size:13px;margin:0 0 16px;">
                    Records a one-day substitution. The template staff assignments are <strong>not</strong> modified.
                </p>
                <form method="POST">
                    <input type="hidden" name="action_substitute" value="1">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Substitute Person <span style="color:#e74c3c;">*</span></label>
                            <select name="sub_user_id" required style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:13px;">
                                <option value="">— Select substitute —</option>
                                <optgroup label="Coaches">
                                    <?php foreach ($data['coaches'] as $c): ?>
                                        <option value="<?= $c->UserID ?>"><?= htmlspecialchars($c->Name) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Trainers">
                                    <?php foreach ($data['trainers'] as $t): ?>
                                        <option value="<?= $t->UserID ?>"><?= htmlspecialchars($t->Name) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Staff Type <span style="color:#e74c3c;">*</span></label>
                            <select name="sub_staff_type" required style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:13px;">
                                <option value="">— Select —</option>
                                <option value="coach">Coach</option>
                                <option value="trainer">Trainer</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Role</label>
                            <select name="sub_staff_role" style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:13px;">
                                <option value="substitute">Substitute</option>
                                <option value="lead">Lead</option>
                                <option value="assistant">Assistant</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Replaces (optional)</label>
                            <select name="replaces_user_id" style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:13px;">
                                <option value="0">— No specific person —</option>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s->UserID ?>"><?= htmlspecialchars($s->UserName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">Reason <span style="color:#e74c3c;">*</span></label>
                        <input type="text" name="sub_reason" required placeholder="e.g. Sick leave, Emergency…"
                               style="width:100%;padding:9px 12px;border:1px solid #ced4da;border-radius:6px;font-size:14px;box-sizing:border-box;">
                    </div>

                    <button type="submit" style="padding:9px 20px;background:#e67e22;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">
                        <i class="fas fa-user-edit"></i> Record Substitute
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
