<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-communication.css">

    <!-- Coach Dashboard Layout -->
    <div class="coach-layout">
        <!-- Left Sidebar Panel -->
        <div class="coach-sidebar" id="coachSidebar">
            <div class="sidebar-header">
                <div class="coach-logo">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Coach Panel</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Slot Sessions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>

                    <li class="nav-item">
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                            <i class="fas fa-comments"></i>
                            <span>Communication</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/reports" class="nav-link" data-tooltip="Reports">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Requests</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1>
                            <i class="fas fa-comments"></i>
                            Communication & Feedback
                        </h1>
                        <p class="coach-communication-subtitle">Message players, trainers, and admins</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-primary" id="newMessageBtn">
                            <i class="fas fa-plus"></i>
                            New Message
                        </button>
                        <button class="btn-primary" id="newAnnouncementBtn">
                            <i class="fas fa-bullhorn"></i>
                            Send Announcement
                        </button>
                    </div>
                </div>
            </div>

            <!-- Communication Content -->
            <div class="communication-content">
                <!-- Feedback Received -->
                <div class="communication-card">
                    <div class="communication-card__header">
                        <h2 class="communication-card__title">
                            <i class="fas fa-star"></i>
                            Feedback Received from Players
                        </h2>
                        <p class="communication-card__subtitle">View feedback and ratings from your assigned players</p>
                    </div>

                    <?php if (!empty($data['feedbacks'])): ?>
                        <?php foreach ($data['feedbacks'] as $feedback): ?>
                        <div class="communication-feedback-item">
                            <div class="communication-feedback-item__top">
                                <div class="communication-feedback-item__author">
                                    <div class="communication-avatar">
                                        <?php echo strtoupper(substr($feedback->FromUserName, 0, 2)); ?>
                                    </div>
                                    <div>
                                        <div class="communication-author-name"><?php echo htmlspecialchars($feedback->FromUserName); ?></div>
                                        <div class="communication-author-email"><?php echo htmlspecialchars($feedback->FromUserEmail); ?></div>
                                    </div>
                                </div>
                                <div class="communication-feedback-item__meta">
                                    <div class="communication-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $feedback->Rating ? 'is-filled' : 'is-empty'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <?php
                                    $statusColors = ['pending' => '#f59e0b', 'reviewed' => '#4A90E2', 'resolved' => '#10b981'];
                                    $sColor = $statusColors[$feedback->Status] ?? '#666';
                                    ?>
                                    <span class="communication-status-badge" style="background: <?php echo $sColor; ?>20; color: <?php echo $sColor; ?>;">
                                        <?php echo $feedback->Status; ?>
                                    </span>
                                </div>
                            </div>
                            <p class="communication-feedback-item__message">
                                <?php echo htmlspecialchars($feedback->Content); ?>
                            </p>
                            <div class="communication-feedback-item__footer">
                                <i class="fas fa-clock"></i>
                                <?php echo date('M d, Y', strtotime($feedback->CreatedDate)); ?>
                                <span class="communication-feedback-item__separator">•</span>
                                <i class="fas fa-tag"></i>
                                <?php echo ucfirst($feedback->Category); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="communication-empty-state">
                            <i class="fas fa-comments"></i>
                            <h3>No feedback yet</h3>
                            <p>Feedback from players will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/coach/communication.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
