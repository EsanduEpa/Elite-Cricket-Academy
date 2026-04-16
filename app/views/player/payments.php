<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
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
                        <a href="<?php echo URLROOT; ?>/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/playerslots" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
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
                            <?php if (!empty($data['recent_payments'])): ?>
                                <?php foreach ($data['recent_payments'] as $payment): ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary"><?= htmlspecialchars(date('M j', strtotime($payment['date'] ?? ''))) ?></div>
                                            <div class="table-cell-secondary"><?= htmlspecialchars(date('Y', strtotime($payment['date'] ?? ''))) ?></div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?= htmlspecialchars($payment['description'] ?? '') ?></div>
                                            <?php if (!empty($payment['details'])): ?>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-shopping-bag"></i> <?= htmlspecialchars($payment['details']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary payment-amount">Rs. <?= number_format($payment['amount'] ?? 0, 2) ?></div>
                                        </td>
                                        <td>
                                            <div class="payment-method">
                                                <i class="fas fa-<?= ($payment['method_type'] ?? '') === 'bank' ? 'university' : 'credit-card' ?>"></i>
                                                <span><?= htmlspecialchars($payment['method_label'] ?? 'N/A') ?></span>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="table-badge status-<?= htmlspecialchars($payment['status_class'] ?? 'paid') ?>"><?= htmlspecialchars($payment['status'] ?? 'Paid') ?></span>
                                        </td>
                                        <td>
                                            <div class="payment-actions">
                                                <button class="btn btn-view">View</button>
                                                <button class="btn btn-download">Receipt</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-info-circle"></i> No recent payments found.
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                            <?php if (!empty($data['upcoming_payments'])): ?>
                                <?php foreach ($data['upcoming_payments'] as $payment): ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary"><?= htmlspecialchars(date('M j', strtotime($payment['due_date'] ?? ''))) ?></div>
                                            <div class="table-cell-secondary"><?= htmlspecialchars(date('Y', strtotime($payment['due_date'] ?? ''))) ?></div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?= htmlspecialchars($payment['description'] ?? '') ?></div>
                                            <?php if (!empty($payment['details'])): ?>
                                                <div class="table-cell-details">
                                                    <i class="fas fa-clock"></i> <?= htmlspecialchars($payment['details']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-cell-primary payment-amount">Rs. <?= number_format($payment['amount'] ?? 0, 2) ?></div>
                                        </td>
                                        <td>
                                            <div class="payment-method">
                                                <i class="fas fa-<?= ($payment['method_type'] ?? '') === 'bank' ? 'university' : 'credit-card' ?>"></i>
                                                <span><?= htmlspecialchars($payment['method_label'] ?? 'N/A') ?></span>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="table-badge status-<?= htmlspecialchars($payment['status_class'] ?? 'pending') ?>"><?= htmlspecialchars($payment['status'] ?? 'Pending') ?></span>
                                        </td>
                                        <td>
                                            <div class="payment-actions">
                                                <button
                                                    type="button"
                                                    class="btn btn-pay"
                                                    data-pay-url="<?php echo URLROOT; ?>/player/payhere_checkout"
                                                    data-payment-item="Membership Pending"
                                                    data-payment-amount="<?= htmlspecialchars(number_format((float)($payment['amount'] ?? 0), 2, '.', ''), ENT_QUOTES) ?>"
                                                >
                                                    Pay Now
                                                </button>
                                                <button type="button" class="btn btn-view">Details</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-check-circle"></i> No upcoming payments due.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="schedule-section">
                <h3>Payment Methods</h3>
                <?php if (!empty($data['payment_methods'])): ?>
                    <?php foreach ($data['payment_methods'] as $method): ?>
                        <div class="schedule-item">
                            <div class="schedule-time"><i class="fas fa-<?= htmlspecialchars($method['icon'] ?? 'credit-card') ?>" style="color: <?= htmlspecialchars($method['color'] ?? '#4A90E2') ?>; font-size: 20px;"></i></div>
                            <div class="schedule-details">
                                <h4><?= htmlspecialchars($method['name'] ?? '') ?></h4>
                                <p><?= htmlspecialchars($method['details'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="schedule-item">
                        <div class="schedule-time"><i class="fas fa-credit-card" style="color: #4A90E2; font-size: 20px;"></i></div>
                        <div class="schedule-details">
                            <h4>No Payment Methods</h4>
                            <p>No payment methods configured yet.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-pay').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    const payUrl = button.dataset.payUrl;
                    const paymentItem = button.dataset.paymentItem || 'Membership Payment';
                    const paymentAmount = parseFloat(button.dataset.paymentAmount || '0');

                    if (!payUrl || !paymentAmount || paymentAmount <= 0) {
                        return;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = payUrl;
                    form.style.display = 'none';

                    const amountInput = document.createElement('input');
                    amountInput.type = 'hidden';
                    amountInput.name = 'cart_total';
                    amountInput.value = paymentAmount.toFixed(2);

                    const itemsInput = document.createElement('input');
                    itemsInput.type = 'hidden';
                    itemsInput.name = 'cart_items';
                    itemsInput.value = JSON.stringify([{ name: paymentItem, quantity: 1 }]);

                    form.appendChild(amountInput);
                    form.appendChild(itemsInput);
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
</body>
</html>