<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<body>
    <!-- Player Dashboard Layout -->
    <div class="player-layout">
        <!-- Left Sidebar Panel (Same as other pages) -->
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
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Training Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping & Rental</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link active">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment History</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name']; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level']; ?> Member</div>
                </div>
                <div class="logout-btn">
                    <a href="<?php echo URLROOT; ?>/login/logout" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Payment Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <h1><i class="fas fa-credit-card"></i> Payment History</h1>
                    <p>Track your membership fees, event payments and rental charges</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Make Payment
                    </button>
                </div>
            </div>

            <!-- Upcoming Payments -->
            <?php if(!empty($data['upcomingPayments'])): ?>
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-exclamation-triangle"></i> Upcoming Payments</h2>
                    </div>
                    
                    <div class="cards-grid">
                        <?php foreach($data['upcomingPayments'] as $payment): ?>
                            <div class="info-card" style="border-left: 4px solid #e74c3c;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                    <h3><i class="fas fa-clock"></i> <?php echo $payment['type']; ?></h3>
                                    <span class="status-badge status-due">Due Soon</span>
                                </div>
                                
                                <div style="margin-bottom: 1.5rem;">
                                    <div style="font-size: 1.8rem; font-weight: 700; color: #e74c3c; margin-bottom: 0.5rem;">
                                        <?php echo $payment['amount']; ?>
                                    </div>
                                    <p><i class="fas fa-calendar"></i> Due: <?php echo date('M j, Y', strtotime($payment['due_date'])); ?></p>
                                </div>
                                
                                <button class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Monthly Fee History -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-alt"></i> Monthly Membership Fees</h2>
                    <a href="#" class="view-all-btn">Download Statement</a>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Paid Date</th>
                            <th>Status</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['monthlyFees'] as $fee): ?>
                            <tr>
                                <td><?php echo $fee['month']; ?></td>
                                <td>
                                    <div style="font-weight: 600; color: #4A90E2;"><?php echo $fee['amount']; ?></div>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($fee['paid_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-paid"><?php echo $fee['status']; ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Event & Tournament Fees -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-trophy"></i> Event & Tournament Fees</h2>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['eventFees'] as $eventFee): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo $eventFee['event']; ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #4A90E2;"><?php echo $eventFee['amount']; ?></div>
                                </td>
                                <td>
                                    <?php if($eventFee['status'] == 'Paid'): ?>
                                        <?php echo date('M j, Y', strtotime($eventFee['paid_date'])); ?>
                                    <?php else: ?>
                                        Due: <?php echo date('M j, Y', strtotime($eventFee['due_date'])); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $eventFee['status'] == 'Paid' ? 'status-paid' : 'status-due'; ?>">
                                        <?php echo $eventFee['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($eventFee['status'] == 'Paid'): ?>
                                        <button class="btn btn-outline btn-sm">
                                            <i class="fas fa-download"></i> Receipt
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-credit-card"></i> Pay Now
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payment Summary -->
            <div class="cards-grid">
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-chart-pie"></i> Payment Summary</h2>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #28a745; margin-bottom: 0.5rem;">
                                $1,050
                            </div>
                            <div style="color: #666; text-transform: uppercase; font-size: 0.85rem;">
                                Paid This Year
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #e74c3c; margin-bottom: 0.5rem;">
                                $265
                            </div>
                            <div style="color: #666; text-transform: uppercase; font-size: 0.85rem;">
                                Outstanding
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
                        <div style="font-size: 0.9rem; color: #666; text-align: center;">
                            <i class="fas fa-info-circle"></i>
                            Next payment due: September 30, 2025
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-credit-card"></i> Payment Methods</h2>
                    </div>
                    
                    <div class="info-card" style="margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem; color: #4A90E2;">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                    **** **** **** 4532
                                </div>
                                <div style="color: #666; font-size: 0.9rem;">
                                    Expires 12/27
                                </div>
                            </div>
                            <span class="status-badge status-active">Primary</span>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline" style="width: 100%;">
                        <i class="fas fa-plus"></i> Add Payment Method
                    </button>
                    
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
                        <div style="font-size: 0.9rem; color: #666; text-align: center;">
                            <i class="fas fa-shield-alt"></i>
                            All payments are secured with SSL encryption
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once APPROOT . '/views/inc/components/footer.php'; ?>

    <!-- JavaScript for Dashboard -->
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    
    <style>
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
    </style>
</body>

</html>
