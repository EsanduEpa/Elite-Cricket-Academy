<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'Elite Cricket Academy'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
</head>
<body>

<header class="header">
    <nav class="nav-container">
        <div class="logo">Elite Cricket Academy</div>
        
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
            <a href="<?php echo URLROOT; ?>/register" class="enroll-btn">Enroll Now</a>
            <a href="<?php echo URLROOT; ?>/login" class="enroll-btn">Login</a>
        </div>
    </nav>
</header>