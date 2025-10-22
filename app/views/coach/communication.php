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
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Message players, trainers, and admins</p>
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
                <!-- Communication Layout -->
                <div class="communication-layout">
                    <!-- Conversations List -->
                    <div class="conversations-panel">
                        <div class="conversations-header">
                            <h3>Messages</h3>
                            <div class="search-conversations">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchConversations" placeholder="Search conversations...">
                            </div>
                        </div>
                        
                        <div class="conversation-filters">
                            <button class="filter-btn active" data-filter="all">All</button>
                            <button class="filter-btn" data-filter="players">Players</button>
                            <button class="filter-btn" data-filter="trainers">Trainers</button>
                            <button class="filter-btn" data-filter="admins">Admins</button>
                        </div>
                        
                        <div class="conversations-list" id="conversationsList">
                            <!-- Conversations will be loaded here -->
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="chat-panel">
                        <div class="chat-empty" id="chatEmpty">
                            <i class="fas fa-comments"></i>
                            <h3>Select a conversation</h3>
                            <p>Choose a conversation from the list to start messaging</p>
                        </div>
                        
                        <div class="chat-active" id="chatActive" style="display: none;">
                            <div class="chat-header">
                                <div class="chat-user-info">
                                    <div class="user-avatar" id="chatAvatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h4 id="chatUserName">User Name</h4>
                                        <span class="user-role" id="chatUserRole">Role</span>
                                    </div>
                                </div>
                                <div class="chat-actions">
                                    <button class="btn-icon" title="Call">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <button class="btn-icon" title="Video Call">
                                        <i class="fas fa-video"></i>
                                    </button>
                                    <button class="btn-icon" title="More">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="chat-messages" id="chatMessages">
                                <!-- Messages will be loaded here -->
                            </div>
                            
                            <div class="chat-input">
                                <button class="btn-icon" title="Attach File">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <textarea id="messageInput" placeholder="Type your message..." rows="1"></textarea>
                                <button class="btn-send" id="sendMessageBtn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="info-panel" id="infoPanel">
                        <div class="info-header">
                            <h3>Details</h3>
                        </div>
                        <div class="info-content" id="infoContent">
                            <div class="info-empty">
                                <i class="fas fa-info-circle"></i>
                                <p>Select a conversation to view details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Message Modal -->
    <div class="modal" id="newMessageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-envelope"></i> New Message</h2>
                <button class="modal-close" id="closeNewMessageModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="newMessageForm">
                    <div class="form-group">
                        <label for="recipientType">Recipient Type *</label>
                        <select id="recipientType" required>
                            <option value="">Select type...</option>
                            <option value="player">Player</option>
                            <option value="trainer">Trainer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recipientSelect">Select Recipient *</label>
                        <select id="recipientSelect" required>
                            <option value="">Choose recipient...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="messageSubject">Subject</label>
                        <input type="text" id="messageSubject" placeholder="Message subject">
                    </div>

                    <div class="form-group">
                        <label for="messageContent">Message *</label>
                        <textarea id="messageContent" rows="6" placeholder="Type your message..." required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelNewMessage">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div class="modal" id="announcementModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-bullhorn"></i> Send Announcement</h2>
                <button class="modal-close" id="closeAnnouncementModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="announcementForm">
                    <div class="form-group">
                        <label for="announcementGroup">Send To *</label>
                        <select id="announcementGroup" required>
                            <option value="">Select group...</option>
                            <option value="all_players">All Players</option>
                            <option value="junior_group">Junior Group (U-15)</option>
                            <option value="senior_group">Senior Group (U-19)</option>
                            <option value="my_players">My Assigned Players</option>
                            <option value="all_staff">All Staff</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="announcementTitle">Title *</label>
                        <input type="text" id="announcementTitle" placeholder="Announcement title" required>
                    </div>

                    <div class="form-group">
                        <label for="announcementContent">Message *</label>
                        <textarea id="announcementContent" rows="6" placeholder="Announcement message..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="announcementUrgent">
                            Mark as urgent
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelAnnouncement">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-bullhorn"></i>
                            Send Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="<?php echo URLROOT; ?>/js/coach-communication.js"></script>

<script>
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
