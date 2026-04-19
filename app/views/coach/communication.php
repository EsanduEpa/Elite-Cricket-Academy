<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-communication.css">

    <!-- Coach Dashboard Layout -->
    <div class="coach-layout">
        <?php $activeCoachNav = 'communication'; require APPROOT . '/views/inc/components/coach_sidebar.php'; ?>

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
                        <a class="btn-primary" href="<?php echo URLROOT; ?>/coach/requests">
                            <i class="fas fa-clipboard-list"></i>
                            View Requests
                        </a>
                        <a class="btn-primary" href="<?php echo URLROOT; ?>/notifications">
                            <i class="fas fa-bell"></i>
                            Notifications
                        </a>
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

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
