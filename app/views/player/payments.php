<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - Elite Cricket Academy</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/payments.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php require_once APPROOT . '/views/inc/components/header.php'; ?>
    
    <div class="player-layout">
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
                        <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link active">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping & Rental</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/achievements" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                            <i class="fas fa-medal"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Player Profile -->
            <div class="player-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="player-name"><?php echo $data['player']['name'] ?? 'Player Name'; ?></div>
                    <div class="player-role"><?php echo $data['player']['membership_level'] ?? 'Member'; ?> Member</div>
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
            <div class="payments-dashboard">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-left">
                <h1><i class="fas fa-credit-card"></i> Payment Management</h1>
                <p>Track your payments, fees, and billing history</p>
            </div>
            <div class="header-right">
                <div class="balance-card">
                    <div class="balance-info">
                        <span class="balance-label">Account Balance</span>
                        <span class="balance-amount">₹2,450</span>
                    </div>
                    <div class="balance-status credit">
                        <i class="fas fa-arrow-up"></i>
                        <span>Credit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Summary Cards -->
    <div class="payment-summary">
        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-icon total">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="card-content">
                    <span class="card-value">₹12,500</span>
                    <span class="card-label">Total Paid This Month</span>
                    <div class="card-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+15% from last month</span>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <span class="card-value">₹3,200</span>
                    <span class="card-label">Pending Payments</span>
                    <div class="card-change negative">
                        <i class="fas fa-arrow-down"></i>
                        <span>2 pending payments</span>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon refunded">
                    <i class="fas fa-undo"></i>
                </div>
                <div class="card-content">
                    <span class="card-value">₹1,600</span>
                    <span class="card-label">Refunds Received</span>
                    <div class="card-change neutral">
                        <i class="fas fa-check-circle"></i>
                        <span>Last refund: 2 days ago</span>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon upcoming">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-content">
                    <span class="card-value">₹2,800</span>
                    <span class="card-label">Upcoming Payments</span>
                    <div class="card-change neutral">
                        <i class="fas fa-info-circle"></i>
                        <span>Due in next 7 days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Section -->
    <div class="payment-history">
        <div class="section-header">
            <h2><i class="fas fa-history"></i> Recent Transactions</h2>
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All</button>
                <button class="filter-tab" data-filter="paid">Paid</button>
                <button class="filter-tab" data-filter="pending">Pending</button>
                <button class="filter-tab" data-filter="refunded">Refunded</button>
            </div>
        </div>

        <div class="appointments-grid">
            <div class="appointment-card refunded">
                            <div class="appointment-header">
                                <div class="instructor-info">
                                    <img src="<?php echo URLROOT; ?>/img/coach3.webp" alt="Coach Wilson" class="instructor-avatar">
                                    <div class="instructor-details">
                                        <h4>Coach Wilson</h4>
                                        <span class="instructor-type">Batting Coach</span>
                                    </div>
                                </div>
                                <div class="appointment-status">
                                    <span class="status-badge refund">Refunded</span>
                                </div>
                            </div>
                            <div class="appointment-body">
                                <div class="appointment-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Nov 25, 2024</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span>10:00 AM - 11:00 AM</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Cancelled by Coach</span>
                                    </div>
                                </div>
                                <div class="appointment-payment">
                                    <span class="payment-label">Refund Amount</span>
                                    <span class="payment-amount refund">+₹800</span>
                                </div>
                            </div>
                            <div class="appointment-footer">
                                <button class="btn btn-outline btn-sm">
                                    <i class="fas fa-receipt"></i>
                                    View Refund
                                </button>
                                <button class="btn btn-outline btn-sm">
                                    <i class="fas fa-redo"></i>
                                    Rebook Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Fees Tab -->
            <div class="tab-content" id="monthly-fees">
                <div class="monthly-fees-section">
                    <div class="section-header">
                        <h3>Monthly Fee Payments</h3>
                        <div class="header-actions">
                            <button class="btn btn-outline" id="viewFeeStructure">
                                <i class="fas fa-file-alt"></i>
                                Fee Structure
                            </button>
                            <button class="btn btn-primary" id="payMonthlyFee">
                                <i class="fas fa-credit-card"></i>
                                Pay Current Month
                            </button>
                        </div>
                    </div>

                    <div class="fee-timeline">
                        <div class="timeline-header">
                            <h4>Payment History</h4>
                            <select class="year-select">
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>

                        <div class="timeline-content">
                            <div class="month-fee-card current">
                                <div class="month-header">
                                    <div class="month-info">
                                        <h5>December 2024</h5>
                                        <span class="month-status">Current Month</span>
                                    </div>
                                    <div class="fee-amount">
                                        <span class="amount">₹5,000</span>
                                        <span class="status pending">Due: Dec 15</span>
                                    </div>
                                </div>
                                <div class="fee-breakdown">
                                    <div class="breakdown-item">
                                        <span>Academy Fee</span>
                                        <span>₹4,000</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span>Equipment Usage</span>
                                        <span>₹500</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span>Ground Maintenance</span>
                                        <span>₹300</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span>GST (18%)</span>
                                        <span>₹200</span>
                                    </div>
                                </div>
                                <div class="fee-actions">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-credit-card"></i>
                                        Pay Now
                                    </button>
                                    <button class="btn btn-outline">
                                        <i class="fas fa-download"></i>
                                        Download Bill
                                    </button>
                                </div>
                            </div>

                            <div class="month-fee-card paid">
                                <div class="month-header">
                                    <div class="month-info">
                                        <h5>November 2024</h5>
                                        <span class="month-status">Paid</span>
                                    </div>
                                    <div class="fee-amount">
                                        <span class="amount">₹5,000</span>
                                        <span class="status paid">Paid: Nov 1</span>
                                    </div>
                                </div>
                                <div class="fee-breakdown">
                                    <div class="breakdown-item">
                                        <span>Academy Fee</span>
                                        <span>₹4,000</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span>Equipment Usage</span>
                                        <span>₹500</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span>Ground Maintenance</span>
                                        <span>₹300</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span>GST (18%)</span>
                                        <span>₹200</span>
                                    </div>
                                </div>
                                <div class="fee-actions">
                                    <button class="btn btn-outline">
                                        <i class="fas fa-receipt"></i>
                                        View Receipt
                                    </button>
                                    <button class="btn btn-outline">
                                        <i class="fas fa-download"></i>
                                        Download Invoice
                                    </button>
                                </div>
                            </div>

                            <div class="month-fee-card paid">
                                <div class="month-header">
                                    <div class="month-info">
                                        <h5>October 2024</h5>
                                        <span class="month-status">Paid</span>
                                    </div>
                                    <div class="fee-amount">
                                        <span class="amount">₹5,000</span>
                                        <span class="status paid">Paid: Oct 1</span>
                                    </div>
                                </div>
                                <div class="fee-actions">
                                    <button class="btn btn-outline">
                                        <i class="fas fa-receipt"></i>
                                        View Receipt
                                    </button>
                                    <button class="btn btn-outline">
                                        <i class="fas fa-download"></i>
                                        Download Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refunds Tab -->
            <div class="tab-content" id="refunds">
                <div class="refunds-section">
                    <div class="section-header">
                        <h3>Refunds & Credits</h3>
                        <div class="header-actions">
                            <select class="filter-select">
                                <option value="all">All Refunds</option>
                                <option value="processed">Processed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="refunds-list">
                        <div class="refund-card">
                            <div class="refund-header">
                                <div class="refund-info">
                                    <h4>Session Cancellation Refund</h4>
                                    <span class="refund-reason">Coach Wilson - Nov 25, 2024</span>
                                </div>
                                <div class="refund-amount success">
                                    <span class="amount">+₹800</span>
                                    <span class="status-badge success">Processed</span>
                                </div>
                            </div>
                            <div class="refund-body">
                                <div class="refund-details">
                                    <div class="detail-item">
                                        <span class="label">Original Payment:</span>
                                        <span class="value">₹800</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Refund Amount:</span>
                                        <span class="value">₹800</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Processing Fee:</span>
                                        <span class="value">₹0</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Refund Date:</span>
                                        <span class="value">Nov 25, 2024</span>
                                    </div>
                                </div>
                                <div class="refund-timeline">
                                    <div class="timeline-step completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Refund Requested</span>
                                    </div>
                                    <div class="timeline-step completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Admin Approved</span>
                                    </div>
                                    <div class="timeline-step completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Amount Credited</span>
                                    </div>
                                </div>
                            </div>
                            <div class="refund-footer">
                                <button class="btn btn-outline btn-sm">
                                    <i class="fas fa-receipt"></i>
                                    View Receipt
                                </button>
                            </div>
                        </div>

                        <div class="refund-card">
                            <div class="refund-header">
                                <div class="refund-info">
                                    <h4>Equipment Rental Return</h4>
                                    <span class="refund-reason">Batting Pads - Oct 15, 2024</span>
                                </div>
                                <div class="refund-amount success">
                                    <span class="amount">+₹500</span>
                                    <span class="status-badge success">Processed</span>
                                </div>
                            </div>
                            <div class="refund-body">
                                <div class="refund-details">
                                    <div class="detail-item">
                                        <span class="label">Security Deposit:</span>
                                        <span class="value">₹500</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Damage Deduction:</span>
                                        <span class="value">₹0</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Refund Amount:</span>
                                        <span class="value">₹500</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Refund Date:</span>
                                        <span class="value">Oct 16, 2024</span>
                                    </div>
                                </div>
                                <div class="refund-timeline">
                                    <div class="timeline-step completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Equipment Returned</span>
                                    </div>
                                    <div class="timeline-step completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Condition Verified</span>
                                    </div>
                                    <div class="timeline-step completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Deposit Refunded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="refund-footer">
                                <button class="btn btn-outline btn-sm">
                                    <i class="fas fa-receipt"></i>
                                    View Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Tab -->
            <div class="tab-content" id="pending">
                <div class="pending-section">
                    <div class="section-header">
                        <h3>Pending Payments</h3>
                        <div class="header-actions">
                            <div class="total-pending">
                                <span class="pending-label">Total Pending:</span>
                                <span class="pending-amount">₹3,200</span>
                            </div>
                            <button class="btn btn-primary" id="payAllPending">
                                <i class="fas fa-credit-card"></i>
                                Pay All
                            </button>
                        </div>
                    </div>

                    <div class="pending-list">
                        <div class="pending-item urgent">
                            <div class="pending-header">
                                <div class="pending-info">
                                    <h4>Monthly Academy Fee</h4>
                                    <span class="pending-details">December 2024</span>
                                </div>
                                <div class="pending-amount">
                                    <span class="amount">₹5,000</span>
                                    <span class="due-badge urgent">Due in 3 days</span>
                                </div>
                            </div>
                            <div class="pending-body">
                                <div class="payment-breakdown">
                                    <div class="breakdown-row">
                                        <span>Base Fee:</span>
                                        <span>₹4,000</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>Additional Services:</span>
                                        <span>₹800</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>GST (18%):</span>
                                        <span>₹200</span>
                                    </div>
                                </div>
                                <div class="late-fee-notice">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Late fee of ₹200 will be applied after Dec 15</span>
                                </div>
                            </div>
                            <div class="pending-actions">
                                <button class="btn btn-primary">
                                    <i class="fas fa-credit-card"></i>
                                    Pay Now
                                </button>
                                <button class="btn btn-outline">
                                    <i class="fas fa-calendar"></i>
                                    Request Extension
                                </button>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-header">
                                <div class="pending-info">
                                    <h4>Equipment Rental Fee</h4>
                                    <span class="pending-details">Batting Gloves - Weekly Rental</span>
                                </div>
                                <div class="pending-amount">
                                    <span class="amount">₹1,200</span>
                                    <span class="due-badge">Due: Dec 15</span>
                                </div>
                            </div>
                            <div class="pending-body">
                                <div class="payment-breakdown">
                                    <div class="breakdown-row">
                                        <span>Rental Fee (2 weeks):</span>
                                        <span>₹1,000</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>GST (18%):</span>
                                        <span>₹180</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>Service Charge:</span>
                                        <span>₹20</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pending-actions">
                                <button class="btn btn-primary">
                                    <i class="fas fa-credit-card"></i>
                                    Pay Now
                                </button>
                                <button class="btn btn-outline">
                                    <i class="fas fa-undo"></i>
                                    Return Equipment
                                </button>
                            </div>
                        </div>

                        <div class="pending-item">
                            <div class="pending-header">
                                <div class="pending-info">
                                    <h4>Tournament Registration</h4>
                                    <span class="pending-details">Inter-Academy Championship 2024</span>
                                </div>
                                <div class="pending-amount">
                                    <span class="amount">₹2,000</span>
                                    <span class="due-badge">Due: Dec 20</span>
                                </div>
                            </div>
                            <div class="pending-body">
                                <div class="payment-breakdown">
                                    <div class="breakdown-row">
                                        <span>Registration Fee:</span>
                                        <span>₹1,500</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>Kit Fee:</span>
                                        <span>₹300</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>Processing Fee:</span>
                                        <span>₹200</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pending-actions">
                                <button class="btn btn-primary">
                                    <i class="fas fa-credit-card"></i>
                                    Pay Now
                                </button>
                                <button class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Cancel Registration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Make Payment</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="payment-form">
                <div class="payment-summary">
                    <h4>Payment Summary</h4>
                    <div class="summary-item">
                        <span>Item:</span>
                        <span id="paymentItem">Monthly Academy Fee</span>
                    </div>
                    <div class="summary-item">
                        <span>Amount:</span>
                        <span id="paymentAmount">₹5,000</span>
                    </div>
                    <div class="summary-item total">
                        <span>Total:</span>
                        <span id="paymentTotal">₹5,000</span>
                    </div>
                </div>

                <div class="payment-methods">
                    <h4>Payment Method</h4>
                    <div class="method-options">
                        <label class="method-option">
                            <input type="radio" name="paymentMethod" value="card" checked>
                            <div class="option-content">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit/Debit Card</span>
                            </div>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="paymentMethod" value="upi">
                            <div class="option-content">
                                <i class="fab fa-google-pay"></i>
                                <span>UPI</span>
                            </div>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="paymentMethod" value="netbanking">
                            <div class="option-content">
                                <i class="fas fa-university"></i>
                                <span>Net Banking</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="card-details">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" placeholder="123" maxlength="3">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cardholder Name</label>
                        <input type="text" placeholder="John Doe">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelPayment">Cancel</button>
            <button class="btn btn-primary" id="confirmPayment">
                <i class="fas fa-lock"></i>
                Pay Securely
            </button>
        </div>
    </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
    <script src="<?php echo URLROOT; ?>/js/player/payments.js"></script>
</body>
</html>
