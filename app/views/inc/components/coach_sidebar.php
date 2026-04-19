<?php
$activeCoachNav = $activeCoachNav ?? '';
$coachNavItems = [
    'dashboard' => ['url' => 'coach/dashboard', 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
    'sessions' => ['url' => 'staffslots/calendar', 'icon' => 'fas fa-calendar-check', 'label' => 'My Slot Sessions'],
    'players' => ['url' => 'coach/players', 'icon' => 'fas fa-users', 'label' => 'Players'],
    'performance' => ['url' => 'coach/performance', 'icon' => 'fas fa-chart-line', 'label' => 'Performance'],
    'tournaments' => ['url' => 'coach/tournaments', 'icon' => 'fas fa-trophy', 'label' => 'Tournaments'],
    'health' => ['url' => 'coach/health', 'icon' => 'fas fa-heartbeat', 'label' => 'Health & Injury'],
    'communication' => ['url' => 'coach/communication', 'icon' => 'fas fa-comments', 'label' => 'Communication'],
    'requests' => ['url' => 'coach/requests', 'icon' => 'fas fa-clipboard-list', 'label' => 'Requests'],
];
?>
<div class="coach-sidebar" id="coachSidebar">
    <div class="sidebar-header">
        <div class="coach-logo">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>Coach Panel</h3>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle coach sidebar">
            <i class="fas fa-angle-left"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <?php foreach ($coachNavItems as $key => $item): ?>
                <li class="nav-item <?php echo $activeCoachNav === $key ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT . '/' . $item['url']; ?>" class="nav-link" data-tooltip="<?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                        <span><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>
