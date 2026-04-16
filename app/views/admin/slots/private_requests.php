<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin-dashboard.css">
<style>
.req-card { background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px; }
.req-table { width:100%;border-collapse:collapse; }
.req-table th,
.req-table td { padding:10px 12px;border-bottom:1px solid #eef2f6;vertical-align:top; }
.req-table th { background:#f8f9fa;text-align:left;font-size:12px;color:#555; }
.badge-pending { background:#fff3cd;color:#856404;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-approved { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-rejected { background:#f8d7da;color:#721c24;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-available { background:#d4edda;color:#155724;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.badge-blocked { background:#f8d7da;color:#721c24;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600; }
.req-btn { padding:7px 12px;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600; }
.req-approve { background:#2e7d32;color:#fff; }
.req-reject { background:#c0392b;color:#fff; }
.alert-error { background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
.alert-success { background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:20px; }
</style>

<?php $requests = $data['requests']; ?>

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

    <div class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-envelope-open-text"></i> Private Session Requests</h1>
                    <p>Review coach and trainer requests before creating the session.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/adminslots/templates" style="padding:9px 18px;border-radius:8px;background:#ecf0f1;color:#333;text-decoration:none;font-size:14px;">Back to Templates</a>
                </div>
            </div>
        </div>

        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#3498db;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Private Requests</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px;">
            <?php flash('private_session_request'); ?>
            <?php if ($data['error']): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="req-card">
                <div style="overflow-x:auto;">
                    <table class="req-table">
                        <thead>
                            <tr>
                                <th>Requester</th>
                                <th>Date</th>
                                <th>Time Band</th>
                                <th>Facility</th>
                                <th>Capacity</th>
                                <th>Availability</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="9" style="text-align:center;padding:24px;color:#6c757d;">No private session requests.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($request->RequesterName ?? '—') ?></strong><br>
                                            <small style="color:#6c757d;">#<?= (int)$request->RequestID ?> · <?= htmlspecialchars(ucfirst((string)($request->RequesterRole ?? '')) ) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars(date('d M Y', strtotime($request->RequestedDate))) ?></td>
                                        <td><?= htmlspecialchars($request->SlotLabel ?? '—') ?></td>
                                        <td><?= htmlspecialchars($request->FacilityName ?? '—') ?></td>
                                        <td><?= (int)($request->MaxParticipants ?? 10) ?></td>
                                        <td>
                                            <?php
                                            $availability = strtolower((string)($request->AvailabilityStatus ?? 'blocked'));
                                            if ($availability === 'available') {
                                                echo '<span class="badge-available">Coach free + facility free</span>';
                                            } else {
                                                echo '<span class="badge-blocked">Conflict found</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= nl2br(htmlspecialchars($request->Notes ?? '—')) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'badge-' . strtolower((string)$request->Status);
                                            echo '<span class="' . $statusClass . '">' . ucfirst((string)$request->Status) . '</span>';
                                            if (!empty($request->ApprovedOccurrenceID)) {
                                                echo '<div style="margin-top:6px;"><a href="' . URLROOT . '/adminslots/occurrence/' . (int)$request->ApprovedOccurrenceID . '">View session</a></div>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($request->Status === 'pending'): ?>
                                                <form method="POST" action="<?php echo URLROOT; ?>/adminslots/private_requests" style="display:flex;gap:8px;flex-wrap:wrap;min-width:180px;">
                                                    <input type="hidden" name="request_id" value="<?= (int)$request->RequestID ?>">
                                                    <button type="submit" name="request_action" value="approve" class="req-btn req-approve">Approve</button>
                                                    <button type="submit" name="request_action" value="reject" class="req-btn req-reject">Reject</button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color:#6c757d;font-size:12px;">Reviewed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>