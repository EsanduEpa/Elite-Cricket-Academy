<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'Elite Cricket Academy - Player Dashboard'; ?></title>
    
    <!-- Font Awesome -->
    <?php
        $faLocalCssPath = dirname(APPROOT) . '/public/vendor/fontawesome/css/all.min.css';
        if (file_exists($faLocalCssPath)) {
            echo '<link rel="stylesheet" href="' . URLROOT . '/vendor/fontawesome/css/all.min.css">';
        } else {
            echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">';
        }
    ?>
    
    <!-- Dashboard Base CSS -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
    
    <!-- Page-specific CSS (if provided) -->
    <?php if(isset($data['page_css'])): ?>
        <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/<?php echo $data['page_css']; ?>.css?v=<?php echo time(); ?>">
    <?php endif; ?>
    
    <!-- Mobile-specific meta tags -->
    <meta name="theme-color" content="#2c3e50">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
<?php require_once APPROOT . '/views/inc/components/dev_mode_banner.php'; ?>
