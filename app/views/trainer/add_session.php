<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/bookings.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/add-session.css?v=<?php echo time(); ?>">
<?php $trainerSidebarActive = 'bookings'; ?>

<!-- Trainer Layout -->
<div class="player-layout">

    <!-- Sidebar -->
    <div class="trainer-sidebar" id="trainerSidebar">
        <div class="sidebar-header">
            <div class="trainer-logo">
                <i class="fas fa-user-tie"></i>
                <h3>Trainer Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <?php require APPROOT . '/views/inc/components/trainer_sidebar_menu.php'; ?>

        <div class="trainer-profile">
            <div class="trainer-avatar"><i class="fas fa-user-tie"></i></div>
            <div class="trainer-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Trainer', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="trainer-role">Fitness Trainer</div>
            <div class="profile-actions">
                <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                    <i class="fas fa-user-cog"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div><!-- /sidebar -->

    <!-- Main Content -->
    <div class="main-content" id="mainContent">

        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-plus"></i> Add New Session</h1>
                    <p>Schedule a new training session for your client</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="asf-btn asf-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Return to Bookings
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash message -->
        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash-message <?php echo htmlspecialchars($_SESSION['flash_type'] ?? 'success', ENT_QUOTES, 'UTF-8'); ?>">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8');
                  unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="asf-card">

            <!-- Card Header -->
            <div class="asf-card-header">
                <div class="asf-card-title">
                    <div class="asf-card-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h2>Session Details</h2>
                        <p>Fill in all required fields to schedule the session</p>
                    </div>
                </div>
                <span class="asf-required-note">
                    <span class="asf-req-star">*</span> Required fields
                </span>
            </div>

            <?php
            function asf_old($field, $oldData, $default = '') {
                return htmlspecialchars($oldData[$field] ?? $default, ENT_QUOTES, 'UTF-8');
            }
            function asf_err($field, $errors) {
                return htmlspecialchars($errors[$field] ?? '', ENT_QUOTES, 'UTF-8');
            }
            function asf_cls($field, $errors) {
                return isset($errors[$field]) ? ' asf-input-error' : '';
            }

            $errors  = $data['errors']  ?? [];
            $oldData = $data['oldData'] ?? [];
            ?>

            <!-- Server-side error banner -->
            <?php if (!empty($errors)): ?>
            <div class="asf-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Please fix the highlighted errors below before submitting.</span>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST"
                  action="<?php echo URLROOT; ?>/trainer/addSession"
                  id="addSessionForm"
                  onsubmit="return validateForm(event)"
                  novalidate
                  class="asf-form">

                <!-- Row 1: Session Title (full width) -->
                <div class="asf-field asf-field-full">
                    <label for="session_title" class="asf-label">
                        <i class="fas fa-heading"></i> Session Title
                        <span class="asf-req-star">*</span>
                    </label>
                    <input type="text"
                           id="session_title" name="session_title"
                           class="asf-input<?php echo asf_cls('session_title', $errors); ?>"
                           placeholder="e.g., Strength &amp; Conditioning Training"
                           maxlength="100"
                           value="<?php echo asf_old('session_title', $oldData); ?>">
                    <span class="asf-error-msg" id="err_session_title">
                        <?php echo asf_err('session_title', $errors); ?>
                    </span>
                </div>

                <!-- Row 2: Client Name (full width) -->
                <div class="asf-field asf-field-full">
                    <label for="session_client" class="asf-label">
                        <i class="fas fa-user"></i> Client Name
                        <span class="asf-req-star">*</span>
                    </label>
                    <input type="text"
                           id="session_client" name="session_client"
                           class="asf-input<?php echo asf_cls('session_client', $errors); ?>"
                           placeholder="e.g., John Smith"
                           maxlength="100"
                           value="<?php echo asf_old('session_client', $oldData); ?>">
                    <span class="asf-error-msg" id="err_session_client">
                        <?php echo asf_err('session_client', $errors); ?>
                    </span>
                </div>

                <!-- Row 3: Date + Time Slot (two columns) -->
                <div class="asf-row-2col">

                    <div class="asf-field">
                        <label for="session_date" class="asf-label">
                            <i class="fas fa-calendar-alt"></i> Date
                            <span class="asf-req-star">*</span>
                        </label>
                        <input type="date"
                               id="session_date" name="session_date"
                               class="asf-input<?php echo asf_cls('session_date', $errors); ?>"
                               min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo asf_old('session_date', $oldData); ?>">
                        <span class="asf-error-msg" id="err_session_date">
                            <?php echo asf_err('session_date', $errors); ?>
                        </span>
                    </div>

                    <div class="asf-field">
                        <label for="session_time_slot" class="asf-label">
                            <i class="fas fa-clock"></i> Time Slot
                            <span class="asf-req-star">*</span>
                        </label>
                        <?php
                        $timeSlots = [
                            '06:00-07:00' => '6:00 AM – 7:00 AM',
                            '07:00-08:00' => '7:00 AM – 8:00 AM',
                            '07:30-08:30' => '7:30 AM – 8:30 AM',
                            '08:00-09:00' => '8:00 AM – 9:00 AM',
                            '09:00-10:00' => '9:00 AM – 10:00 AM',
                            '10:00-11:00' => '10:00 AM – 11:00 AM',
                            '11:00-12:00' => '11:00 AM – 12:00 PM',
                            '12:00-13:00' => '12:00 PM – 1:00 PM',
                            '13:00-14:00' => '1:00 PM – 2:00 PM',
                            '14:00-15:00' => '2:00 PM – 3:00 PM',
                            '15:00-16:00' => '3:00 PM – 4:00 PM',
                            '16:00-17:00' => '4:00 PM – 5:00 PM',
                            '17:00-18:00' => '5:00 PM – 6:00 PM',
                            '18:00-19:00' => '6:00 PM – 7:00 PM',
                        ];
                        $oldSlot = asf_old('session_time_slot', $oldData);
                        ?>
                        <select id="session_time_slot" name="session_time_slot"
                                class="asf-input asf-select<?php echo asf_cls('session_time_slot', $errors); ?>">
                            <option value="">-- Select a time slot --</option>
                            <?php foreach ($timeSlots as $val => $label): ?>
                            <option value="<?php echo $val; ?>"
                                    <?php echo ($oldSlot === $val) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="asf-error-msg" id="err_session_time_slot">
                            <?php echo asf_err('session_time_slot', $errors); ?>
                        </span>
                    </div>

                </div><!-- /.asf-row-2col -->

                <!-- Row 4: Location (full width) -->
                <div class="asf-field asf-field-full">
                    <label for="session_location" class="asf-label">
                        <i class="fas fa-map-marker-alt"></i> Location
                        <span class="asf-req-star">*</span>
                    </label>
                    <?php
                    $locations = [
                        'Gym A - Weight Room'            => 'Gym A – Weight Room',
                        'Cardio Zone - Fitness Center'   => 'Cardio Zone – Fitness Center',
                        'Yoga Studio - Recovery Room'    => 'Yoga Studio – Recovery Room',
                        'Field Area - Training Ground'   => 'Field Area – Training Ground',
                        'Indoor Court - Sports Hall'     => 'Indoor Court – Sports Hall',
                        'Cricket Ground - Main Oval'     => 'Cricket Ground – Main Oval',
                        'Swimming Pool - Aquatic Center' => 'Swimming Pool – Aquatic Center',
                        'Conference Room - Meeting Room' => 'Conference Room – Meeting Room',
                    ];
                    $oldLoc = asf_old('session_location', $oldData);
                    ?>
                    <select id="session_location" name="session_location"
                            class="asf-input asf-select<?php echo asf_cls('session_location', $errors); ?>">
                        <option value="">-- Select a location --</option>
                        <?php foreach ($locations as $val => $label): ?>
                        <option value="<?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo ($oldLoc === $val) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="asf-error-msg" id="err_session_location">
                        <?php echo asf_err('session_location', $errors); ?>
                    </span>
                </div>

                <!-- Row 5: Description (full width, larger textarea) -->
                <div class="asf-field asf-field-full">
                    <label for="session_description" class="asf-label">
                        <i class="fas fa-clipboard-list"></i> Description
                        <span class="asf-req-star">*</span>
                    </label>
                    <textarea id="session_description" name="session_description"
                              class="asf-input asf-textarea<?php echo asf_cls('session_description', $errors); ?>"
                              rows="6"
                              placeholder="Describe session goals, exercises, equipment needed, and any special notes..."
                              maxlength="1000"><?php echo asf_old('session_description', $oldData); ?></textarea>
                    <div class="asf-textarea-meta">
                        <span class="asf-error-msg" id="err_session_description">
                            <?php echo asf_err('session_description', $errors); ?>
                        </span>
                        <span class="asf-char-hint">Max 1000 characters</span>
                    </div>
                </div>

                <!-- Row 6: Status (full width, styled as badge-select) -->
                <div class="asf-field asf-field-full">
                    <label for="session_status" class="asf-label">
                        <i class="fas fa-circle-notch"></i> Status
                        <span class="asf-req-star">*</span>
                    </label>
                    <div class="asf-status-group" id="statusGroup">
                        <?php
                        $statuses = [
                            'active'      => ['label' => 'Active',    'icon' => 'fa-play-circle',    'cls' => 'status-active'],
                            'upcoming'    => ['label' => 'Upcoming',  'icon' => 'fa-clock',          'cls' => 'status-upcoming'],
                            'planned'     => ['label' => 'Planned',   'icon' => 'fa-calendar-alt',   'cls' => 'status-planned'],
                            'completed'   => ['label' => 'Completed', 'icon' => 'fa-check-circle',   'cls' => 'status-completed'],
                        ];
                        $oldStatus = asf_old('session_status', $oldData);
                        foreach ($statuses as $val => $s):
                        ?>
                        <label class="asf-status-option">
                            <input type="radio"
                                   name="session_status"
                                   value="<?php echo $val; ?>"
                                   <?php echo ($oldStatus === $val) ? 'checked' : ''; ?>>
                            <span class="asf-status-badge status-badge <?php echo $s['cls']; ?>">
                                <i class="fas <?php echo $s['icon']; ?>"></i>
                                <?php echo $s['label']; ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <span class="asf-error-msg" id="err_session_status">
                        <?php echo asf_err('session_status', $errors); ?>
                    </span>
                </div>

                <!-- Form Actions -->
                <div class="asf-form-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="asf-btn asf-btn-danger">
                        <i class="fas fa-ban"></i> Cancel Booking
                    </a>
                    <div class="asf-actions-right">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="asf-btn asf-btn-secondary">
                            <i class="fas fa-arrow-left"></i> Return to Bookings
                        </a>
                        <button type="submit" class="asf-btn asf-btn-primary">
                            <i class="fas fa-calendar-plus"></i> Add Session
                        </button>
                    </div>
                </div>

            </form>
        </div><!-- /.asf-card -->

    </div><!-- /main-content -->
</div><!-- /player-layout -->

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

<script>
// ── Client-side validation (logic unchanged) ──────────────────────────────

function validateForm(event) {
    clearErrors();

    let ok = true;
    const today = new Date(); today.setHours(0, 0, 0, 0);

    // ① Session Title
    const title = document.getElementById('session_title').value.trim();
    if (!title) {
        setError('session_title', 'Session title is required.');
        ok = false;
    } else if (title.length < 3 || title.length > 100) {
        setError('session_title', 'Title must be between 3 and 100 characters.');
        ok = false;
    }

    // ② Client Name
    const client = document.getElementById('session_client').value.trim();
    if (!client) {
        setError('session_client', 'Client name is required.');
        ok = false;
    } else if (!/^[a-zA-Z\s'\-\.]+$/.test(client)) {
        setError('session_client', 'Client name must contain letters only.');
        ok = false;
    }

    // ③ Date
    const dateVal = document.getElementById('session_date').value;
    if (!dateVal) {
        setError('session_date', 'Session date is required.');
        ok = false;
    } else if (new Date(dateVal + 'T00:00:00') < today) {
        setError('session_date', 'Date cannot be in the past.');
        ok = false;
    }

    // ④ Time Slot
    const validSlots = [
        '06:00-07:00','07:00-08:00','07:30-08:30','08:00-09:00',
        '09:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00',
        '13:00-14:00','14:00-15:00','15:00-16:00','16:00-17:00',
        '17:00-18:00','18:00-19:00'
    ];
    const slot = document.getElementById('session_time_slot').value;
    if (!slot) {
        setError('session_time_slot', 'Please select a time slot.');
        ok = false;
    } else if (!validSlots.includes(slot)) {
        setError('session_time_slot', 'Invalid time slot selected.');
        ok = false;
    }

    // ⑤ Location
    const validLocs = [
        'Gym A - Weight Room','Cardio Zone - Fitness Center',
        'Yoga Studio - Recovery Room','Field Area - Training Ground',
        'Indoor Court - Sports Hall','Cricket Ground - Main Oval',
        'Swimming Pool - Aquatic Center','Conference Room - Meeting Room'
    ];
    const loc = document.getElementById('session_location').value;
    if (!loc) {
        setError('session_location', 'Please select a location.');
        ok = false;
    } else if (!validLocs.includes(loc)) {
        setError('session_location', 'Invalid location selected.');
        ok = false;
    }

    // ⑥ Description
    const desc = document.getElementById('session_description').value.trim();
    if (!desc) {
        setError('session_description', 'Description is required.');
        ok = false;
    } else if (desc.length < 10) {
        setError('session_description', 'Description must be at least 10 characters.');
        ok = false;
    }

    // ⑦ Status (radio)
    const st = document.querySelector('#addSessionForm input[name="session_status"]:checked');
    const validStatuses = ['active','upcoming','planned','completed'];
    if (!st || !validStatuses.includes(st.value)) {
        setError('session_status', 'Please select a status.');
        ok = false;
    }

    if (!ok) {
        const first = document.querySelector('#addSessionForm .asf-input-error');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return ok;
}

function setError(fieldId, msg) {
    // Input / select / textarea
    const field = document.getElementById(fieldId);
    if (field) field.classList.add('asf-input-error');
    // Radio group
    if (fieldId === 'session_status') {
        document.getElementById('statusGroup')?.classList.add('asf-input-error');
    }
    const errEl = document.getElementById('err_' + fieldId);
    if (errEl) errEl.textContent = msg;
}

function clearErrors() {
    document.querySelectorAll('#addSessionForm .asf-input')
            .forEach(el => el.classList.remove('asf-input-error'));
    document.getElementById('statusGroup')?.classList.remove('asf-input-error');
    document.querySelectorAll('#addSessionForm .asf-error-msg')
            .forEach(el => el.textContent = '');
}

// Sidebar toggle
document.addEventListener('DOMContentLoaded', function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const main    = document.getElementById('mainContent');
    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }
});
</script>
