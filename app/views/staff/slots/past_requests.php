<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<?php
$isCoach = $data['role'] === 'Coach';
$trainerSidebarActive = 'slots';
$cssFile = $isCoach ? 'coach-dashboard' : 'trainer/dashboard';
$layout = $isCoach ? 'coach-layout' : 'trainer-layout';
$sidebar = $isCoach ? 'coach-sidebar' : 'trainer-sidebar';
$logo = $isCoach ? 'fa-chalkboard-teacher' : 'fa-user-tie';
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/<?= $cssFile ?>.css">
<style>
.subnav-link { padding:7px 16px; border-radius:6px; background:#ecf0f1; color:#333; text-decoration:none; font-size:13px; }
.subnav-active { background:#2e7d32; color:#fff; font-weight:600; }
.requests-table { width:100%; border-collapse:collapse; font-size:13px; }
.requests-table th { padding:10px 14px; text-align:left; color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-weight:600; }
.requests-table td { padding:10px 14px; border-bottom:1px solid #f1f5f9; color:#374151; vertical-align:top; }
.requests-table tr:last-child td { border-bottom:none; }
.request-badge { display:inline-flex; align-items:center; gap:6px; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; text-transform:uppercase; }
.request-pending { background:#fef3c7; color:#92400e; }
.request-approved { background:#dcfce7; color:#166534; }
.request-rejected { background:#fee2e2; color:#991b1b; }
.request-cancelled { background:#e5e7eb; color:#374151; }
.request-blocked { background:#e5e7eb; color:#374151; }
</style>

<div class="<?= $layout ?>">
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
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item active"><a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link"><i class="fas fa-calendar-check"></i><span>My Slot Sessions</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/players" class="nav-link"><i class="fas fa-users"></i><span>Players</span></a></li>
                    <li class="nav-item"><a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link"><i class="fas fa-trophy"></i><span>Tournaments</span></a></li>
                </ul>
            </nav>
        <?php else: ?>
            <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>
        <?php endif; ?>

        <div class="profile-section">
            <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
                <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                    <?php echo htmlspecialchars($_SESSION['user_name'] ?? ($data['role'] ?? 'Staff')); ?>
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

    <main class="main-content" id="mainContent">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-history"></i> Past Requests</h1>
                    <p>History of your private session requests.</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/staffslots/private_session" style="padding:9px 18px;border-radius:8px;background:#2e7d32;color:#fff;text-decoration:none;font-size:14px;font-weight:600;">
                        <i class="fas fa-paper-plane"></i> Request Private Session
                    </a>
                </div>
            </div>
        </div>

        <div style="padding:0 25px 20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="subnav-link">
                <i class="fas fa-calendar-alt"></i> Calendar
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/private_session" class="subnav-link">
                <i class="fas fa-paper-plane"></i> Request Private Session
            </a>
            <a href="<?php echo URLROOT; ?>/staffslots/past_requests" class="subnav-link subnav-active">
                <i class="fas fa-history"></i> Past Requests
            </a>
        </div>

        <div style="padding:0 25px 40px;">
            <?php flash('session_message'); ?>

            <div class="detail-card">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
                    <div>
                        <h2 style="margin:0;font-size:18px;color:#2c3e50;"><i class="fas fa-file-alt"></i> Request History</h2>
                        <p style="margin:6px 0 0;color:#666;font-size:13px;">Submitted, approved, and rejected private session requests.</p>
                    </div>
                    <span style="background:#eef2ff;color:#3730a3;padding:5px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                        <?php echo count($data['requests'] ?? []); ?> total
                    </span>
                </div>

                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Facility</th>
                            <th>Status</th>
                            <th>Review</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['requests'])): ?>
                            <?php foreach ($data['requests'] as $request): ?>
                                <?php $status = strtolower((string) ($request->Status ?? 'pending')); ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:700;">#<?php echo (int) $request->RequestID; ?></div>
                                        <div style="color:#64748b;font-size:12px;"><?php echo !empty($request->CreatedAt) ? date('d M Y, H:i', strtotime($request->CreatedAt)) : '—'; ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;"><?php echo htmlspecialchars($request->SlotLabel ?? 'Private Session'); ?></div>
                                        <div style="color:#64748b;font-size:12px;"><?php echo !empty($request->RequestedDate) ? date('d M Y', strtotime($request->RequestedDate)) : '—'; ?></div>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($request->FacilityName ?? '—'); ?></div>
                                        <div style="color:#64748b;font-size:12px;"><?php echo htmlspecialchars((string) ($request->MaxParticipants ?? '—')); ?> max</div>
                                    </td>
                                    <td>
                                        <span class="request-badge request-<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($status); ?></span>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">Availability: <?php echo htmlspecialchars(ucfirst((string) ($request->AvailabilityStatus ?? 'available'))); ?></div>
                                        <div style="color:#64748b;font-size:12px;"><?php echo htmlspecialchars((string) ($request->ReviewNotes ?? '')); ?></div>
                                    </td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/staffslots/private_session" style="color:#1d4ed8;text-decoration:none;font-weight:600;">New Request</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:#64748b;padding:22px;">No private session requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
</body>
</html>
