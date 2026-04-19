<?php
/**
 * Shared Player sidebar.
 *
 * Usage:
 *   $playerActivePage = 'dashboard'|'performance'|'bookings'|'tournaments'|'medical'|'payments'|'shopping';
 *   require APPROOT . '/views/inc/components/player_sidebar.php';
 */

$playerActivePage = $playerActivePage ?? '';

$playerSubscriptionLocked = $playerSubscriptionLocked
    ?? (bool)($data['player']['subscription_locked'] ?? false);

$playerNavLinkExtraClass = static function (string $key) use ($playerSubscriptionLocked): string {
    if (!$playerSubscriptionLocked || $key === 'payments') {
        return '';
    }
    return ' is-disabled';
};

$playerNavLinkExtraAttrs = static function (string $key) use ($playerSubscriptionLocked): string {
    if (!$playerSubscriptionLocked || $key === 'payments') {
        return '';
    }

    return ' aria-disabled="true" tabindex="-1" title="Complete your membership payment to unlock." onclick="return false;"';
};

$playerNavItemClass = static function (string $key) use ($playerActivePage): string {
    return $playerActivePage === $key ? 'nav-item active' : 'nav-item';
};
?>

<!-- Left Sidebar Panel -->
<div class="player-sidebar" id="playerSidebar">
    <div class="sidebar-header">
        <div class="player-logo">
            <i class="fas fa-user-graduate"></i>
            <h3>Player Dashboard</h3>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="<?php echo $playerNavItemClass('dashboard'); ?>">
                <a href="<?php echo URLROOT; ?>/player" class="nav-link<?php echo $playerNavLinkExtraClass('dashboard'); ?>"<?php echo $playerNavLinkExtraAttrs('dashboard'); ?>>
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('performance'); ?>">
                <a href="<?php echo URLROOT; ?>/performance" class="nav-link<?php echo $playerNavLinkExtraClass('performance'); ?>"<?php echo $playerNavLinkExtraAttrs('performance'); ?>>
                    <i class="fas fa-chart-line"></i>
                    <span>Performance</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('bookings'); ?>">
                <a href="<?php echo URLROOT; ?>/playerslots" class="nav-link<?php echo $playerNavLinkExtraClass('bookings'); ?>"<?php echo $playerNavLinkExtraAttrs('bookings'); ?>>
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('tournaments'); ?>">
                <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link<?php echo $playerNavLinkExtraClass('tournaments'); ?>"<?php echo $playerNavLinkExtraAttrs('tournaments'); ?>>
                    <i class="fas fa-medal"></i>
                    <span>Tournaments</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('medical'); ?>">
                <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link<?php echo $playerNavLinkExtraClass('medical'); ?>"<?php echo $playerNavLinkExtraAttrs('medical'); ?>>
                    <i class="fas fa-heartbeat"></i>
                    <span>Medical</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('payments'); ?>">
                <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('shopping'); ?>">
                <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link<?php echo $playerNavLinkExtraClass('shopping'); ?>"<?php echo $playerNavLinkExtraAttrs('shopping'); ?>>
                    <i class="fas fa-shopping-cart"></i>
                    <span>Shopping</span>
                </a>
            </li>
        </ul>
    </nav>

</div>
