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
                    <h1><i class="fas fa-plus-circle"></i> New Academy Event</h1>
                    <p>Create a one-off academy event such as a summer camp, trial, workshop, or special training camp.</p>
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
        <div style="padding:0 25px 20px; display:flex; gap:10px;">
            <a href="<?php echo URLROOT; ?>/adminslots/timeslots"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Time Bands</a>
            <a href="<?php echo URLROOT; ?>/adminslots/templates"  style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Templates</a>
            <a href="<?php echo URLROOT; ?>/adminslots/private_requests" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Private Requests</a>
            <a href="<?php echo URLROOT; ?>/adminslots/generate"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Generate Occurrences</a>
            <a href="<?php echo URLROOT; ?>/adminslots/weeklytimetable" style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Weekly Timetable</a>
            <a href="<?php echo URLROOT; ?>/adminslots/calendar"   style="padding:7px 16px;border-radius:6px;background:#ecf0f1;color:#333;text-decoration:none;font-size:13px;">Calendar</a>
            <a href="<?php echo URLROOT; ?>/adminslots/adhoc"      style="padding:7px 16px;border-radius:6px;background:#2c3e50;color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Academy Event</a>
        </div>

        <div style="padding:0 25px 40px; max-width:760px;">

            <?php if ($data['error']): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <!-- Info box -->
            <div style="background:#e8f4fd;border:1px solid #bee5eb;border-radius:8px;padding:16px 18px;margin-bottom:24px;font-size:13px;color:#2c7a7b;">
                <i class="fas fa-info-circle"></i>
                This form creates an <strong>academy event</strong> in the Event table, not a recurring slot occurrence.
                Use it for summer camps, trials, workshops, and other one-off academy activities.
            </div>

            <!-- Form card -->
            <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 22px;font-size:15px;color:#2c3e50;"><i class="fas fa-edit"></i> Event Details</h3>

                <form method="POST" id="adhocForm">

                    <div class="form-group">
                        <label for="event_name">Event Name <span style="color:#e74c3c;">*</span></label>
                        <input type="text" id="event_name" name="event_name" required placeholder="e.g. Elite Summer Camp"
                               value="<?= htmlspecialchars($_POST['event_name'] ?? '') ?>">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="event_type">Event Type <span style="color:#e74c3c;">*</span></label>
                            <select id="event_type" name="event_type" required>
                                <option value="">— Select event type —</option>
                                <?php
                                $eventTypes = ['Training Camp', 'Workshop', 'Seminar', 'Competition', 'Tournament', 'Match', 'Trial', 'Meeting', 'Other'];
                                foreach ($eventTypes as $eventType):
                                ?>
                                    <option value="<?= htmlspecialchars($eventType) ?>" <?= (($_POST['event_type'] ?? '') === $eventType) ? 'selected' : '' ?>><?= htmlspecialchars($eventType) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" value="Academy" readonly style="background:#f8f9fa;cursor:not-allowed;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="event_venue">Venue / Location <span style="color:#e74c3c;">*</span></label>
                        <input type="text" id="event_venue" name="event_venue" required placeholder="e.g. Main Ground"
                               value="<?= htmlspecialchars($_POST['event_venue'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="event_description">Description <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                        <textarea id="event_description" name="event_description" placeholder="Short summary of the academy event…"><?= htmlspecialchars($_POST['event_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="start_date">Start Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="start_time">Start Time <span style="color:#e74c3c;">*</span></label>
                            <input type="time" id="start_time" name="start_time" required
                                   value="<?= htmlspecialchars($_POST['start_time'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date <span style="color:#e74c3c;">*</span></label>
                            <input type="date" id="end_date" name="end_date" required
                                   value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="end_time">End Time <span style="color:#e74c3c;">*</span></label>
                            <input type="time" id="end_time" name="end_time" required
                                   value="<?= htmlspecialchars($_POST['end_time'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="registration_start">Registration Start <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                            <input type="datetime-local" id="registration_start" name="registration_start"
                                   value="<?= htmlspecialchars($_POST['registration_start'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="registration_end">Registration End <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                            <input type="datetime-local" id="registration_end" name="registration_end"
                                   value="<?= htmlspecialchars($_POST['registration_end'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="max_participants">Max Participants <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                            <input type="number" id="max_participants" name="max_participants" min="1" max="10000"
                                   placeholder="Leave blank for no limit"
                                   value="<?= htmlspecialchars($_POST['max_participants'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="registration_fee">Registration Fee <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                            <input type="number" id="registration_fee" name="registration_fee" min="0" step="0.01"
                                   placeholder="Leave blank if free"
                                   value="<?= htmlspecialchars($_POST['registration_fee'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="primary_contact">Primary Contact <span style="color:#e74c3c;">*</span></label>
                            <input type="text" id="primary_contact" name="primary_contact" required
                                   value="<?= htmlspecialchars($_POST['primary_contact'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_email">Contact Email <span style="color:#e74c3c;">*</span></label>
                            <input type="email" id="contact_email" name="contact_email" required
                                   value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_phone">Contact Phone <span style="color:#e74c3c;">*</span></label>
                            <input type="tel" id="contact_phone" name="contact_phone" required
                                   value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="event_status">Status</label>
                            <select id="event_status" name="event_status">
                                <option value="upcoming" <?= (($_POST['event_status'] ?? 'upcoming') === 'upcoming') ? 'selected' : '' ?>>Upcoming</option>
                                <option value="registration_open" <?= (($_POST['event_status'] ?? '') === 'registration_open') ? 'selected' : '' ?>>Registration Open</option>
                                <option value="registration_closed" <?= (($_POST['event_status'] ?? '') === 'registration_closed') ? 'selected' : '' ?>>Registration Closed</option>
                                <option value="ongoing" <?= (($_POST['event_status'] ?? '') === 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <p style="margin:0;color:#64748b;font-size:12px;">For summer camps, use <strong>Training Camp</strong> as the event type.</p>
                    </div>

                    <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
                        <button type="submit"
                                style="padding:10px 24px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                            <i class="fas fa-plus"></i> Create Academy Event
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
