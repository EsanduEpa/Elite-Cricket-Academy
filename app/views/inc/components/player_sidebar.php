<?php
/**
 * Shared Player sidebar.
 *
 * Usage:
 *   $playerActivePage = 'dashboard'|'performance'|'bookings'|'tournaments'|'medical'|'payments'|'shopping';
 *   require APPROOT . '/views/inc/components/player_sidebar.php';
 */

$playerActivePage = $playerActivePage ?? '';

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
                <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('performance'); ?>">
                <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Performance</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('bookings'); ?>">
                <a href="<?php echo URLROOT; ?>/playerslots" class="nav-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('tournaments'); ?>">
                <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                    <i class="fas fa-medal"></i>
                    <span>Tournaments</span>
                </a>
            </li>

            <li class="<?php echo $playerNavItemClass('medical'); ?>">
                <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
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
                <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Shopping</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Simple Profile Section -->
    <div class="profile-section">
        <div style="display:flex; flex-direction:column; align-items:center; width:100%; padding:12px 14px; box-sizing:border-box; gap:8px;">
            <div class="profile-name" style="margin:0; text-align:center; width:100%;">
                <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Player'; ?>
            </div>
            <div style="display:flex; align-items:center; gap:10px; width:100%; justify-content:center;">
                <a href="<?php echo URLROOT; ?>/player/profile" class="profile-avatar" aria-label="Open player profile" style="width:auto; min-width:46px; min-height:46px; margin:0; flex:0 0 46px; padding:0;">
                    <i class="fas fa-user-circle"></i>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin:0; flex:1; padding:8px 12px !important; border-radius:12px !important;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>
