<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'Elite Cricket Academy'; ?></title>
    <?php
        $faLocalCssPath = dirname(APPROOT) . '/public/vendor/fontawesome/css/all.min.css';
        if (file_exists($faLocalCssPath)) {
            echo '<link rel="stylesheet" href="' . URLROOT . '/vendor/fontawesome/css/all.min.css">';
        } else {
            echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">';
        }
    ?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
</head>
<body>
<?php require_once APPROOT . '/views/inc/components/dev_mode_banner.php'; ?>
<?php
    $isLoggedIn = function_exists('isLoggedIn') ? isLoggedIn() : !empty($_SESSION['user_id']);
    $userRole = function_exists('getUserRole') ? getUserRole() : ($_SESSION['user_role'] ?? 'Guest');

    $notificationUrl = URLROOT . '/player/dashboard';
    $profileUrl = URLROOT . '/player/profile';
    switch ($userRole) {
        case 'Admin':
            $notificationUrl = URLROOT . '/admin/dashboard';
            $profileUrl = URLROOT . '/admin/profile';
            break;
        case 'Coach':
            $notificationUrl = URLROOT . '/coach/notifications';
            $profileUrl = URLROOT . '/coach/profile';
            break;
        case 'Trainer':
            $notificationUrl = URLROOT . '/trainer/dashboard';
            $profileUrl = URLROOT . '/trainer/profile';
            break;
        case 'Shop':
        case 'ShopEmployee':
            $notificationUrl = URLROOT . '/shop/dashboard';
            $profileUrl = URLROOT . '/shop/profile';
            break;
        case 'Player':
        default:
            $notificationUrl = URLROOT . '/player/dashboard';
            $profileUrl = URLROOT . '/player/profile';
            break;
    }
?>

<header class="header">
    <nav class="nav-container">
        <a href="<?php echo URLROOT; ?>" class="logo-link" aria-label="Elite Cricket Academy home">
            <img src="<?php echo URLROOT . '/img/' . rawurlencode('ELITE (1).png'); ?>" alt="Elite Cricket Academy logo" class="logo-mark">
            <span class="logo">Elite Cricket Academy</span>
        </a>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-label="Toggle navigation menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        
        <ul class="nav-menu">
            <li><a href="<?php echo URLROOT; ?>">Home</a></li>
            <li><a href="<?php echo URLROOT; ?>/#programs">Programs</a></li>
            <li><a href="<?php echo URLROOT; ?>/#coaches">Coaches</a></li>
            <li><a href="<?php echo URLROOT; ?>/#facilities">Facilities</a></li>
            <li><a href="<?php echo URLROOT; ?>/#testimonials">Testimonials</a></li>
            <li><a href="<?php echo URLROOT; ?>/#contact">Contact</a></li>
        </ul>
        <div class="nav-buttons">
            <?php if ($isLoggedIn): ?>
                <a href="#" class="nav-icon-btn notification-nav-btn" aria-label="Notifications" title="Notifications" data-notifications-toggle data-list-url="<?php echo URLROOT; ?>/notifications/list" data-mark-url="<?php echo URLROOT; ?>/notifications/mark_read">
                    <i class="fas fa-bell"></i>
                    <span class="notification-dot" data-notification-badge aria-hidden="true"></span>
                </a>
                <div class="notification-dropdown" data-notification-dropdown>
                    <div class="notification-dropdown__header">
                        <div>
                            <h3>Notifications</h3>
                            <p>Latest updates for your account</p>
                        </div>
                        <button type="button" class="notification-dropdown__mark" data-notifications-mark-all>Mark all read</button>
                    </div>
                    <div class="notification-dropdown__body" data-notification-list>
                        <div class="notification-dropdown__state">Click the bell to load notifications.</div>
                    </div>
                </div>
                <a href="<?php echo htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8'); ?>" class="nav-action-btn" aria-label="Profile" title="Profile">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
                <a href="<?php echo URLROOT; ?>/login/logout" class="nav-action-btn nav-action-btn--danger" aria-label="Logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <a href="<?php echo URLROOT; ?>/register" class="enroll-btn">Enroll Now</a>
                <a href="<?php echo URLROOT; ?>/login" class="enroll-btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<?php if ($isLoggedIn): ?>
<script src="<?php echo URLROOT; ?>/js/notifications.js?v=<?php echo time(); ?>" defer></script>
<?php endif; ?>
