// Dummy Communication Data
const conversations = [
    {
        id: 1,
        userId: 101,
        userName: 'Ashan Perera',
        userRole: 'player',
        userType: 'Player',
        avatar: 'AP',
        lastMessage: 'Thank you coach! I\'ll practice those techniques.',
        timestamp: new Date(Date.now() - 30 * 60000), // 30 mins ago
        unread: 2,
        online: true,
        messages: [
            { id: 1, sender: 'them', text: 'Hi Coach! I had a question about my batting stance.', time: '2:15 PM' },
            { id: 2, sender: 'me', text: 'Hi Ashan! Sure, what would you like to know?', time: '2:16 PM' },
            { id: 3, sender: 'them', text: 'Should I adjust my grip for shorter balls?', time: '2:17 PM' },
            { id: 4, sender: 'me', text: 'Yes, for shorter balls you need a slightly looser grip to allow for quick adjustments. Let me show you in tomorrow\'s practice.', time: '2:20 PM' },
            { id: 5, sender: 'them', text: 'Thank you coach! I\'ll practice those techniques.', time: '2:25 PM' }
        ]
    },
    {
        id: 2,
        userId: 102,
        userName: 'Nimal Silva',
        userRole: 'player',
        userType: 'Player',
        avatar: 'NS',
        lastMessage: 'Can we reschedule tomorrow\'s session?',
        timestamp: new Date(Date.now() - 2 * 3600000), // 2 hours ago
        unread: 0,
        online: false,
        messages: [
            { id: 1, sender: 'them', text: 'Good evening Coach!', time: '12:30 PM' },
            { id: 2, sender: 'them', text: 'Can we reschedule tomorrow\'s session?', time: '12:31 PM' },
            { id: 3, sender: 'me', text: 'Hi Nimal. What time works better for you?', time: '12:45 PM' }
        ]
    },
    {
        id: 3,
        userId: 103,
        userName: 'Kasun Mendis',
        userRole: 'trainer',
        userType: 'Trainer',
        avatar: 'KM',
        lastMessage: 'Sounds good, let\'s coordinate.',
        timestamp: new Date(Date.now() - 24 * 3600000), // 1 day ago
        unread: 0,
        online: true,
        messages: [
            { id: 1, sender: 'me', text: 'Hi Kasun, can you help with fitness assessment for the junior group?', time: 'Yesterday 3:00 PM' },
            { id: 2, sender: 'them', text: 'Of course! When do you need it done?', time: 'Yesterday 3:15 PM' },
            { id: 3, sender: 'me', text: 'Next week would be perfect.', time: 'Yesterday 3:20 PM' },
            { id: 4, sender: 'them', text: 'Sounds good, let\'s coordinate.', time: 'Yesterday 3:25 PM' }
        ]
    },
    {
        id: 4,
        userId: 104,
        userName: 'Dilani Fernando',
        userRole: 'admin',
        userType: 'Admin',
        avatar: 'DF',
        lastMessage: 'Please submit by Friday EOD.',
        timestamp: new Date(Date.now() - 3 * 24 * 3600000), // 3 days ago
        unread: 1,
        online: false,
        messages: [
            { id: 1, sender: 'them', text: 'Hi, we need your monthly training report.', time: 'Mon 10:00 AM' },
            { id: 2, sender: 'me', text: 'Sure, when do you need it?', time: 'Mon 10:30 AM' },
            { id: 3, sender: 'them', text: 'Please submit by Friday EOD.', time: 'Mon 11:00 AM' }
        ]
    },
    {
        id: 5,
        userId: 105,
        userName: 'Tharindu Jayasinghe',
        userRole: 'player',
        userType: 'Player',
        avatar: 'TJ',
        lastMessage: 'I\'ll be there on time!',
        timestamp: new Date(Date.now() - 5 * 24 * 3600000), // 5 days ago
        unread: 0,
        online: true,
        messages: [
            { id: 1, sender: 'me', text: 'Great performance in last match!', time: 'Last Wed 4:00 PM' },
            { id: 2, sender: 'them', text: 'Thank you coach! Your guidance really helped.', time: 'Last Wed 4:15 PM' },
            { id: 3, sender: 'me', text: 'Keep up the good work. See you at practice tomorrow.', time: 'Last Wed 4:20 PM' },
            { id: 4, sender: 'them', text: 'I\'ll be there on time!', time: 'Last Wed 4:25 PM' }
        ]
    },
    {
        id: 6,
        userId: 106,
        userName: 'Chamara Wickramasinghe',
        userRole: 'player',
        userType: 'Player',
        avatar: 'CW',
        lastMessage: 'My knee is feeling much better now.',
        timestamp: new Date(Date.now() - 7 * 24 * 3600000), // 1 week ago
        unread: 0,
        online: false,
        messages: [
            { id: 1, sender: 'them', text: 'Coach, about my injury recovery...', time: 'Last Mon 2:00 PM' },
            { id: 2, sender: 'me', text: 'How are you feeling?', time: 'Last Mon 2:10 PM' },
            { id: 3, sender: 'them', text: 'My knee is feeling much better now.', time: 'Last Mon 2:15 PM' }
        ]
    }
];

const recipients = {
    player: [
        { id: 101, name: 'Ashan Perera', avatar: 'AP' },
        { id: 102, name: 'Nimal Silva', avatar: 'NS' },
        { id: 105, name: 'Tharindu Jayasinghe', avatar: 'TJ' },
        { id: 106, name: 'Chamara Wickramasinghe', avatar: 'CW' },
        { id: 107, name: 'Sahan De Silva', avatar: 'SD' },
        { id: 108, name: 'Kavinda Rajapaksa', avatar: 'KR' }
    ],
    trainer: [
        { id: 103, name: 'Kasun Mendis', avatar: 'KM' },
        { id: 201, name: 'Ruwan Wijesinghe', avatar: 'RW' },
        { id: 202, name: 'Pradeep Kumar', avatar: 'PK' }
    ],
    admin: [
        { id: 104, name: 'Dilani Fernando', avatar: 'DF' },
        { id: 301, name: 'Sunil Perera', avatar: 'SP' },
        { id: 302, name: 'Malini Jayawardena', avatar: 'MJ' }
    ]
};

let currentFilter = 'all';
let currentConversation = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadConversations();
    setupEventListeners();
});

// Load conversations
function loadConversations() {
    const conversationsList = document.getElementById('conversationsList');
    const filteredConversations = currentFilter === 'all' 
        ? conversations 
        : conversations.filter(c => c.userRole === currentFilter.replace('s', ''));

    conversationsList.innerHTML = filteredConversations.map(conv => `
        <div class="conversation-item ${conv.unread > 0 ? 'unread' : ''}" data-id="${conv.id}">
            <div class="conversation-avatar ${conv.online ? 'online' : ''}">
                ${conv.avatar}
            </div>
            <div class="conversation-info">
                <div class="conversation-header">
                    <span class="conversation-name">${conv.userName}</span>
                    <span class="conversation-time">${formatTime(conv.timestamp)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="conversation-preview">${conv.lastMessage}</div>
                    ${conv.unread > 0 ? `<span class="unread-badge">${conv.unread}</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');

    // Add click listeners
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const convId = parseInt(this.dataset.id);
            openConversation(convId);
        });
    });
}

// Format time
function formatTime(date) {
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days === 1) return 'Yesterday';
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
}

// Open conversation
function openConversation(convId) {
    currentConversation = conversations.find(c => c.id === convId);
    if (!currentConversation) return;

    // Mark as read
    currentConversation.unread = 0;
    loadConversations();

    // Update active state
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
        if (parseInt(item.dataset.id) === convId) {
            item.classList.add('active');
        }
    });

    // Show chat
    document.getElementById('chatEmpty').style.display = 'none';
    document.getElementById('chatActive').style.display = 'flex';

    // Update header
    document.getElementById('chatAvatar').innerHTML = currentConversation.avatar;
    document.getElementById('chatUserName').textContent = currentConversation.userName;
    document.getElementById('chatUserRole').textContent = currentConversation.userType;

    // Load messages
    loadMessages();

    // Update info panel
    updateInfoPanel();
}

// Load messages
function loadMessages() {
    const chatMessages = document.getElementById('chatMessages');
    
    chatMessages.innerHTML = currentConversation.messages.map(msg => `
        <div class="message ${msg.sender === 'me' ? 'sent' : 'received'}">
            <div class="message-avatar">
                ${msg.sender === 'me' ? 'ME' : currentConversation.avatar}
            </div>
            <div class="message-content">
                <div class="message-bubble">
                    <p class="message-text">${msg.text}</p>
                </div>
                <span class="message-time">${msg.time}</span>
            </div>
        </div>
    `).join('');

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Update info panel
function updateInfoPanel() {
    const infoContent = document.getElementById('infoContent');
    
    infoContent.innerHTML = `
        <div class="user-info-section">
            <div class="user-info-avatar">${currentConversation.avatar}</div>
            <h3 class="user-info-name">${currentConversation.userName}</h3>
            <div class="user-info-role">${currentConversation.userType}</div>
        </div>
        
        <div class="info-section">
            <h4>Contact Information</h4>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <span>${currentConversation.userName.toLowerCase().replace(' ', '.')}@elite.lk</span>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <span>+94 77 123 ${1000 + currentConversation.id}</span>
            </div>
            <div class="info-item">
                <i class="fas fa-circle ${currentConversation.online ? 'text-success' : 'text-muted'}"></i>
                <span>${currentConversation.online ? 'Online' : 'Offline'}</span>
            </div>
        </div>
        
        ${currentConversation.userRole === 'player' ? `
        <div class="info-section">
            <h4>Player Details</h4>
            <div class="info-item">
                <i class="fas fa-users"></i>
                <span>Under-19 Group</span>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar"></i>
                <span>Joined: Jan 2024</span>
            </div>
        </div>
        ` : ''}
    `;
}

// Send message
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !currentConversation) return;
    
    const newMessage = {
        id: currentConversation.messages.length + 1,
        sender: 'me',
        text: message,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    };
    
    currentConversation.messages.push(newMessage);
    currentConversation.lastMessage = message;
    currentConversation.timestamp = new Date();
    
    loadMessages();
    loadConversations();
    
    input.value = '';
    input.style.height = 'auto';
}

// Setup event listeners
function setupEventListeners() {
    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            loadConversations();
        });
    });

    // Search conversations
    document.getElementById('searchConversations').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(item => {
            const name = item.querySelector('.conversation-name').textContent.toLowerCase();
            const preview = item.querySelector('.conversation-preview').textContent.toLowerCase();
            item.style.display = (name.includes(search) || preview.includes(search)) ? 'flex' : 'none';
        });
    });

    // Send message button
    document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);

    // Message input
    const messageInput = document.getElementById('messageInput');
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // New message modal
    const newMessageBtn = document.getElementById('newMessageBtn');
    const newMessageModal = document.getElementById('newMessageModal');
    const closeNewMessageModal = document.getElementById('closeNewMessageModal');
    const cancelNewMessage = document.getElementById('cancelNewMessage');

    newMessageBtn.addEventListener('click', () => {
        newMessageModal.classList.add('active');
    });

    closeNewMessageModal.addEventListener('click', () => {
        newMessageModal.classList.remove('active');
    });

    cancelNewMessage.addEventListener('click', () => {
        newMessageModal.classList.remove('active');
    });

    // Recipient type change
    document.getElementById('recipientType').addEventListener('change', function() {
        const type = this.value;
        const recipientSelect = document.getElementById('recipientSelect');
        
        if (type && recipients[type]) {
            recipientSelect.innerHTML = '<option value="">Choose recipient...</option>' +
                recipients[type].map(r => `<option value="${r.id}">${r.name}</option>`).join('');
            recipientSelect.disabled = false;
        } else {
            recipientSelect.innerHTML = '<option value="">Choose recipient...</option>';
            recipientSelect.disabled = true;
        }
    });

    // New message form submit
    document.getElementById('newMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const recipientId = parseInt(document.getElementById('recipientSelect').value);
        const subject = document.getElementById('messageSubject').value;
        const content = document.getElementById('messageContent').value;
        
        // Find recipient
        const type = document.getElementById('recipientType').value;
        const recipient = recipients[type].find(r => r.id === recipientId);
        
        if (recipient) {
            // Create or open conversation
            let conversation = conversations.find(c => c.userId === recipientId);
            
            if (!conversation) {
                conversation = {
                    id: conversations.length + 1,
                    userId: recipientId,
                    userName: recipient.name,
                    userRole: type,
                    userType: type.charAt(0).toUpperCase() + type.slice(1),
                    avatar: recipient.avatar,
                    lastMessage: content.substring(0, 50) + (content.length > 50 ? '...' : ''),
                    timestamp: new Date(),
                    unread: 0,
                    online: false,
                    messages: []
                };
                conversations.unshift(conversation);
            }
            
            conversation.messages.push({
                id: conversation.messages.length + 1,
                sender: 'me',
                text: content,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });
            
            conversation.lastMessage = content.substring(0, 50) + (content.length > 50 ? '...' : '');
            conversation.timestamp = new Date();
            
            loadConversations();
            openConversation(conversation.id);
            
            newMessageModal.classList.remove('active');
            this.reset();
        }
    });

    // Announcement modal
    const newAnnouncementBtn = document.getElementById('newAnnouncementBtn');
    const announcementModal = document.getElementById('announcementModal');
    const closeAnnouncementModal = document.getElementById('closeAnnouncementModal');
    const cancelAnnouncement = document.getElementById('cancelAnnouncement');

    newAnnouncementBtn.addEventListener('click', () => {
        announcementModal.classList.add('active');
    });

    closeAnnouncementModal.addEventListener('click', () => {
        announcementModal.classList.remove('active');
    });

    cancelAnnouncement.addEventListener('click', () => {
        announcementModal.classList.remove('active');
    });

    // Announcement form submit
    document.getElementById('announcementForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const group = document.getElementById('announcementGroup').value;
        const title = document.getElementById('announcementTitle').value;
        const content = document.getElementById('announcementContent').value;
        const urgent = document.getElementById('announcementUrgent').checked;
        
        // Show success message
        alert(`Announcement "${title}" sent to ${group}${urgent ? ' (Urgent)' : ''}`);
        
        announcementModal.classList.remove('active');
        this.reset();
    });

    // Close modals on backdrop click
    [newMessageModal, announcementModal].forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
}
