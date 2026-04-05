<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/nutrition_crud.css?v=<?php echo time(); ?>">

<?php
// Resolve which values to show:
//   After a failed update attempt → use stashed $old values
//   Fresh load → use $plan values from DB
$plan   = $data['plan'];
$old    = $data['old']    ?? [];       // repopulated after failed submit
$errors = $data['errors'] ?? [];

// Helper: return old (post) value if available, otherwise DB value
$val = function(string $k, string $dbCol = '') use ($old, $plan) {
    if (isset($old[$k])) return htmlspecialchars($old[$k], ENT_QUOTES);
    $col = $dbCol ?: $k;
    return htmlspecialchars($plan->$col ?? '', ENT_QUOTES);
};
$err = fn(string $k) => $errors[$k] ?? '';
$cls = fn(string $k) => isset($errors[$k]) ? ' is-invalid' : '';
?>

<div class="player-layout nc-page">

    <!-- ── Sidebar ─────────────────────────────────────────────── -->
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

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                        <i class="fas fa-calendar-check"></i><span>Schedule &amp; Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                        <i class="fas fa-dumbbell"></i><span>Workout Plans</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/nutrition" class="nav-link">
                        <i class="fas fa-apple-alt"></i><span>Nutrition Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                        <i class="fas fa-capsules"></i><span>Supplements</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                        <i class="fas fa-user-injured"></i><span>Injury Reports</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="trainer-profile">
            <div class="trainer-avatar"><i class="fas fa-user-tie"></i></div>
            <div class="trainer-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'John Trainer'); ?></div>
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
    </div>

    <!-- ── Main content ─────────────────────────────────────────── -->
    <div class="main-content" id="mainContent">

        <!-- Page header -->
        <div class="nc-header">
            <div class="nc-header-text">
                <h1><i class="fas fa-edit"></i> Edit Nutrition Plan</h1>
                <p>Update plan #<?php echo (int)$plan->PlanID; ?> —
                   <?php echo htmlspecialchars($plan->PlanName ?? 'Untitled Plan'); ?>
                </p>
            </div>
            <div class="nc-header-actions">
                <a href="<?php echo URLROOT; ?>/nutrition" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Plans
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        <?php flash('nutrition_message'); ?>

        <!-- Form card -->
        <div class="nc-card">
            <div class="nc-card-header">
                <h2>
                    <i class="fas fa-apple-alt"></i>
                    Editing: <?php echo htmlspecialchars($plan->PlanName ?? 'Plan #' . $plan->PlanID); ?>
                </h2>
                <span style="font-size:.8rem;color:#6c757d;">
                    ID #<?php echo (int)$plan->PlanID; ?>
                </span>
            </div>
            <div class="nc-card-body">

                <p class="nc-form-sub">
                    All fields marked <span style="color:#e74c3c">*</span> are required.
                </p>

                <form method="POST"
                      action="<?php echo URLROOT; ?>/nutrition/update/<?php echo (int)$plan->PlanID; ?>"
                      novalidate>

                    <!-- Row 1: Plan Name + Player -->
                    <div class="nc-field-row">

                        <div class="nc-field">
                            <label for="plan_name">
                                <i class="fas fa-tag"></i> Plan Name
                                <span class="req">*</span>
                            </label>
                            <input type="text"
                                   id="plan_name" name="plan_name"
                                   class="form-control<?php echo $cls('plan_name'); ?>"
                                   value="<?php echo $val('plan_name', 'PlanName'); ?>"
                                   placeholder="e.g. Pre-Season High Protein Plan"
                                   maxlength="255">
                            <?php if ($err('plan_name')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('plan_name')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="nc-field">
                            <label for="player_id">
                                <i class="fas fa-user"></i> Player
                                <span class="req">*</span>
                            </label>
                            <?php
                            // Current player: use stashed old value if present, else DB value
                            $currentPlayer = isset($old['player_id'])
                                ? (int)$old['player_id']
                                : (int)($plan->PlayerID ?? 0);
                            ?>
                            <select id="player_id" name="player_id"
                                    class="form-control<?php echo $cls('player_id'); ?>">
                                <option value="">— Select a player —</option>
                                <?php foreach ($data['players'] as $p): ?>
                                    <option value="<?php echo (int)$p->UserID; ?>"
                                        <?php echo ($currentPlayer === (int)$p->UserID) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p->name); ?> — <?php echo htmlspecialchars($p->email); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($err('player_id')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('player_id')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Row 2: Diet Details -->
                    <div class="nc-field-row full">
                        <div class="nc-field">
                            <label for="diet_details">
                                <i class="fas fa-utensils"></i> Diet Details
                                <span class="req">*</span>
                            </label>
                            <textarea id="diet_details" name="diet_details"
                                      class="form-control<?php echo $cls('diet_details'); ?>"
                                      rows="6"
                                      placeholder="Describe meal timing, portions, macros, hydration guidelines..."><?php echo $val('diet_details', 'DietDetails'); ?></textarea>
                            <?php if ($err('diet_details')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('diet_details')); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 3: Duration + Status + Created Date -->
                    <div class="nc-field-row third">

                        <div class="nc-field">
                            <label for="duration">
                                <i class="fas fa-hourglass-half"></i> Duration (days)
                                <span class="req">*</span>
                            </label>
                            <input type="number"
                                   id="duration" name="duration"
                                   class="form-control<?php echo $cls('duration'); ?>"
                                   value="<?php echo $val('duration', 'Duration'); ?>"
                                   min="1" max="365"
                                   placeholder="e.g. 30">
                            <?php if ($err('duration')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('duration')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="nc-field">
                            <label for="status">
                                <i class="fas fa-toggle-on"></i> Status
                            </label>
                            <?php
                            $currentStatus = isset($old['status']) ? $old['status'] : ($plan->Status ?? 'active');
                            ?>
                            <select id="status" name="status" class="form-control">
                                <option value="active"
                                    <?php echo ($currentStatus === 'active') ? 'selected' : ''; ?>>
                                    Active
                                </option>
                                <option value="inactive"
                                    <?php echo ($currentStatus === 'inactive') ? 'selected' : ''; ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>

                        <div class="nc-field">
                            <label for="created_date">
                                <i class="fas fa-calendar-alt"></i> Created Date
                                <span class="req">*</span>
                            </label>
                            <?php
                            // Format DB date to Y-m-d for <input type="date">
                            $dbDate = !empty($plan->CreatedDate)
                                ? date('Y-m-d', strtotime($plan->CreatedDate))
                                : date('Y-m-d');
                            $dateVal = isset($old['created_date'])
                                ? htmlspecialchars($old['created_date'], ENT_QUOTES)
                                : $dbDate;
                            ?>
                            <input type="date"
                                   id="created_date" name="created_date"
                                   class="form-control<?php echo $cls('created_date'); ?>"
                                   value="<?php echo $dateVal; ?>">
                            <?php if ($err('created_date')): ?>
                                <span class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo htmlspecialchars($err('created_date')); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Action buttons -->
                    <div class="nc-form-actions">
                        <!-- Far-left: Return to the old Nutrition page -->
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition"
                           class="btn btn-secondary push-left">
                            <i class="fas fa-undo"></i> Return to Nutrition Page
                        </a>

                        <!-- Right side: Cancel + Submit -->
                        <a href="<?php echo URLROOT; ?>/nutrition"
                           class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Plan
                        </button>
                    </div>

                </form>
            </div><!-- /.nc-card-body -->
        </div><!-- /.nc-card -->

    </div><!-- /.main-content -->
</div><!-- /.player-layout -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const main    = document.getElementById('mainContent');
    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }

    // Client-side validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        let valid = true;

        form.querySelectorAll('.js-error').forEach(el => el.remove());
        form.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

        function addError(id, msg) {
            const field = document.getElementById(id);
            if (!field) return;
            field.classList.add('is-invalid');
            const span = document.createElement('span');
            span.className = 'invalid-feedback js-error';
            span.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            field.parentNode.appendChild(span);
            valid = false;
        }

        const planName    = document.getElementById('plan_name').value.trim();
        const playerId    = document.getElementById('player_id').value;
        const dietDetails = document.getElementById('diet_details').value.trim();
        const duration    = document.getElementById('duration').value.trim();
        const createdDate = document.getElementById('created_date').value.trim();

        if (!planName)                                  addError('plan_name',    'Plan name is required.');
        if (!playerId)                                  addError('player_id',    'Please select a player.');
        if (!dietDetails)                               addError('diet_details', 'Diet details are required.');
        if (!duration || isNaN(duration) || +duration <= 0)
                                                        addError('duration',     'Duration must be a positive number.');
        if (!createdDate)                               addError('created_date', 'Created date is required.');

        if (!valid) e.preventDefault();
    });
});
</script>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
</body>
</html>
