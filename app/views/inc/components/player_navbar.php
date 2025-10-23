<!-- Top Navigation Bar for Player Dashboard -->
<nav class="top-navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="navbar-brand">
                <i class="fas fa-trophy"></i>
                <span class="brand-text">Elite Cricket Academy</span>
                <span class="role-badge">Player</span>
            </div>
        </div>
        
        <div class="navbar-center">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search anything..." id="globalSearch">
            </div>
        </div>
        
        <div class="navbar-right">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="<?php echo URLROOT; ?>/player/cart" class="navbar-icon" title="Shopping Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge-count" id="cartCount">0</span>
                </a>
                
                <a href="<?php echo URLROOT; ?>/player/bookings" class="navbar-icon" title="Bookings">
                    <i class="fas fa-calendar-check"></i>
                    <span class="badge-count" id="bookingCount">0</span>
                </a>
                
                <div class="navbar-icon notification-icon" title="Notifications" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="badge-count" id="notificationCount">3</span>
                </div>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="user-profile-dropdown">
                <div class="profile-trigger" id="profileDropdownBtn">
                    <div class="avatar-circle">
                        <?php if(isset($data['player']['profile_image']) && !empty($data['player']['profile_image'])): ?>
                            <img src="<?php echo URLROOT . '/uploads/profiles/' . $data['player']['profile_image']; ?>" alt="Profile">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></span>
                        <span class="profile-status">Online</span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                
                <div class="dropdown-menu" id="profileDropdownMenu">
                    <a href="<?php echo URLROOT; ?>/player/profile" class="dropdown-item">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/performance" class="dropdown-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/payments" class="dropdown-item">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo URLROOT; ?>/player/profile" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Notification Panel (Slide-in) -->
<div class="notification-panel" id="notificationPanel">
    <div class="notification-header">
        <h3><i class="fas fa-bell"></i> Notifications</h3>
        <button class="close-panel" id="closeNotificationPanel">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="notification-body">
        <div class="notification-item unread">
            <div class="notification-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="notification-content">
                <h4>Booking Confirmed</h4>
                <p>Your net practice session is booked for tomorrow at 10:00 AM</p>
                <span class="notification-time">2 hours ago</span>
            </div>
        </div>
        
        <div class="notification-item unread">
            <div class="notification-icon info">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="notification-content">
                <h4>New Training Plan Available</h4>
                <p>Coach has assigned you a new workout plan</p>
                <span class="notification-time">5 hours ago</span>
            </div>
        </div>
        
        <div class="notification-item">
            <div class="notification-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="notification-content">
                <h4>Payment Reminder</h4>
                <p>Monthly fee payment due in 3 days</p>
                <span class="notification-time">1 day ago</span>
            </div>
        </div>
    </div>
    <div class="notification-footer">
        <a href="<?php echo URLROOT; ?>/player/notifications" class="view-all-btn">View All Notifications</a>
    </div>
</div>

<script>
// Navbar Dropdown and Notification Panel Scripts
document.addEventListener('DOMContentLoaded', function() {
    // Profile Dropdown Toggle
    const profileDropdownBtn = document.getElementById('profileDropdownBtn');
    const profileDropdownMenu = document.getElementById('profileDropdownMenu');
    
    if (profileDropdownBtn && profileDropdownMenu) {
        profileDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdownBtn.contains(e.target)) {
                profileDropdownMenu.classList.remove('show');
            }
        });
    }
    
    // Notification Panel Toggle
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const closeNotificationPanel = document.getElementById('closeNotificationPanel');
    
    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', function() {
            notificationPanel.classList.toggle('show');
        });
        
        if (closeNotificationPanel) {
            closeNotificationPanel.addEventListener('click', function() {
                notificationPanel.classList.remove('show');
            });
        }
        
        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationPanel.contains(e.target) && !notificationBtn.contains(e.target)) {
                notificationPanel.classList.remove('show');
            }
        });
    }
    
    // Global Search Functionality
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    // Redirect to search results page or filter current content
                    console.log('Searching for:', searchTerm);
                    // window.location.href = '<?php echo URLROOT; ?>/player/search?q=' + encodeURIComponent(searchTerm);
                }
            }
        });
    }
});
</script>
