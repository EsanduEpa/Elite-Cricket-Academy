<?php
$trainerSidebarActive = $trainerSidebarActive ?? '';

$menuItems = [
    [
        'key' => 'dashboard',
        'href' => URLROOT . '/trainer',
        'icon' => 'fas fa-tachometer-alt',
        'label' => 'Dashboard',
    ],
    [
        'key' => 'bookings',
        'href' => URLROOT . '/trainer/bookings',
        'icon' => 'fas fa-calendar-check',
        'label' => 'Player Bookings',
    ],
    [
        'key' => 'slots',
        'href' => URLROOT . '/staffslots/calendar',
        'icon' => 'fas fa-calendar-check',
        'label' => 'My Slot Sessions',
    ],
    [
        'key' => 'tournaments',
        'href' => URLROOT . '/trainer/tournaments',
        'icon' => 'fas fa-trophy',
        'label' => 'Tournaments',
    ],
    [
        'key' => 'nutrition',
        'href' => URLROOT . '/nutrition',
        'icon' => 'fas fa-capsules',
        'label' => 'Nutrition & Supplements',
    ],
    [
        'key' => 'workout',
        'href' => URLROOT . '/trainer/workout',
        'icon' => 'fas fa-dumbbell',
        'label' => 'Workout Plans',
    ],
    [
        'key' => 'medical',
        'href' => URLROOT . '/trainer/medical',
        'icon' => 'fas fa-user-injured',
        'label' => 'Medical Records',
    ],
];
?>
<nav class="sidebar-nav">
    <ul class="nav-menu">
        <?php foreach ($menuItems as $item): ?>
            <?php $isActive = ($trainerSidebarActive === $item['key']); ?>
            <li class="nav-item<?php echo $isActive ? ' active' : ''; ?>">
                <a href="<?php echo $item['href']; ?>" class="nav-link<?php echo $isActive ? ' active' : ''; ?>">
                    <i class="<?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['label']; ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
