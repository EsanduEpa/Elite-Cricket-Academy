<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/payments.css">
    
    <div class="player-layout">
        <!-- Simple Sidebar -->
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
                            <span>Training</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Simple Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
                <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
                <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Simple Page Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-credit-card"></i> Payment Management</h1>
                <p>View your payment history, manage subscriptions, and handle billing.</p>
            </div>

           
            

            <!-- Subscription Details
            <div class="schedule-section">
                <h3>Current Subscription</h3>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-star" style="color: gold; font-size: 20px;"></i></div>
                    <div class="schedule-details">
                        <h4>Premium Membership</h4>
                        <p><i class="fas fa-calendar"></i> Monthly Plan • <i class="fas fa-dollar-sign"></i> $150/month • <i class="fas fa-check"></i> Full access to all facilities</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-info-circle" style="color: #4A90E2; font-size: 20px;"></i></div>
                    <div class="schedule-details">
                        <h4>Plan Benefits</h4>
                        <p>Unlimited training sessions • Personal coaching • Equipment rental • Tournament participation</p>
                    </div>
                </div>
            </div> -->

            <!-- Recent Payments and Upcoming Payments - Two Tables Per Row -->
            <div class="performance-tables-row">
                <!-- Recent Payments -->
                <div id="recent-payments" class="schedule-card recent-payments">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-history"></i> Recent Payments</h2>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 15</div>
                                    <div class="table-cell-secondary">2025</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Monthly  Fee</div>
                                    
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary payment-amount">$150.00</div>
                                </td>
                                <td>
                                    <div class="payment-method">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Visa ****2341</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-paid">Paid</span>
                                </td>
                                <td>
                                    <div class="payment-actions">
                                        <button class="btn btn-view">View</button>
                                        <button class="btn btn-download">Receipt</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Oct 8</div>
                                    <div class="table-cell-secondary">2025</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Private Coaching Session</div>
                                    
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary payment-amount">$75.00</div>
                                </td>
                                <td>
                                    <div class="payment-method">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Visa ****2341</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-paid">Paid</span>
                                </td>
                                <td>
                                    <div class="payment-actions">
                                        <button class="btn btn-view">View</button>
                                        <button class="btn btn-download">Receipt</button>
                                    </div>
                                </td>
                            </tr>
                           
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Sept 5</div>
                                    <div class="table-cell-secondary">2025</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Equipment Purchase</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-shopping-bag"></i> Cricket bat and protective gear
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary payment-amount">$125.00</div>
                                </td>
                                <td>
                                    <div class="payment-method">
                                        <i class="fas fa-university"></i>
                                        <span>Bank ****7890</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-paid">Paid</span>
                                </td>
                                <td>
                                    <div class="payment-actions">
                                        <button class="btn btn-view">View</button>
                                        <button class="btn btn-download">Receipt</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

                <!-- Upcoming Payments -->
                <div class="schedule-card upcoming-payments">
                    <div class="card-header">
                        <div class="header-content">
                            <h2><i class="fas fa-calendar-plus"></i> Upcoming Payments</h2>
                        </div>
                    </div>
                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Due Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Nov 15</div>
                                    <div class="table-cell-secondary">2025</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Monthly  Fee</div>
                                 
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary payment-amount">$150.00</div>
                                </td>
                                <td>
                                    <div class="payment-method">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Visa ****2341</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-due-soon">Due Soon</span>
                                </td>
                                <td>
                                    <div class="payment-actions">
                                        <button class="btn btn-pay">Pay Now</button>
                                        <button class="btn btn-view">Details</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary">Dec 1</div>
                                    <div class="table-cell-secondary">2025</div>
                                </td>
                                <td>
                                    <div class="table-cell-title">Equipment Rental Fee</div>
                                    <div class="table-cell-details">
                                        <i class="fas fa-clock"></i> Cricket bat rental return
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="table-cell-primary payment-amount">$45.00</div>
                                </td>
                                <td>
                                    <div class="payment-method">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Visa ****2341</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="table-badge status-pending">Pending</span>
                                </td>
                                <td>
                                    <div class="payment-actions">
                                        <button class="btn btn-pay">Pay Now</button>
                                        <button class="btn btn-view">Details</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="schedule-section">
                <h3>Payment Methods</h3>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-credit-card" style="color: #4A90E2; font-size: 20px;"></i></div>
                    <div class="schedule-details">
                        <h4>Visa Credit Card</h4>
                        <p>****2341 • Expires 12/26 • Default payment method • Auto-payment enabled</p>
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time"><i class="fas fa-university" style="color: green; font-size: 20px;"></i></div>
                    <div class="schedule-details">
                        <h4>Bank Account</h4>
                        <p>****7890 • Backup payment method • Available for manual payments</p>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            <div class="quick-actions">
                <h3>Payment Actions</h3>
                <div class="action-buttons">
                    <a href="#" class="action-btn" onclick="alert('Make payment feature coming soon!')">
                        <i class="fas fa-plus"></i> Make Payment
                    </a>
                    <a href="#recent-payments" class="action-btn" onclick="scrollToRecentPayments()">
                        <i class="fas fa-history"></i> View History
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Manage cards feature coming soon!')">
                        <i class="fas fa-credit-card"></i> Manage Cards
                    </a>
                    <a href="#" class="action-btn" onclick="alert('Download invoice feature coming soon!')">
                        <i class="fas fa-download"></i> Download Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/payments.js"></script>
</body>
</html>