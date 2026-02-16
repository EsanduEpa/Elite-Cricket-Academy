// Tournaments Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Tournaments page loaded');
    
    // Initialize filter functionality
    initializeFilters();
    
    // Initialize tournament cards
    initializeTournamentCards();
    
    // Initialize modal functionality
    initializeModal();
});

// Filter functionality
function initializeFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const tournamentCards = document.querySelectorAll('.tournament-card');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter tournaments
            filterTournaments(filter, tournamentCards);
        });
    });
}

function filterTournaments(filter, cards) {
    cards.forEach(card => {
        const cardType = card.classList.contains('upcoming') ? 'upcoming' :
                        card.classList.contains('enrolled') ? 'enrolled' :
                        card.classList.contains('completed') ? 'completed' : 'all';
        
        if (filter === 'all' || cardType === filter) {
            card.style.display = 'block';
            // Add fade-in animation
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        } else {
            card.style.display = 'none';
        }
    });
}

// Tournament card interactions
function initializeTournamentCards() {
    const tournamentCards = document.querySelectorAll('.tournament-card');
    
    tournamentCards.forEach(card => {
        // Add hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
        
        // Add click handlers for buttons
        const enrollBtn = card.querySelector('.btn-primary');
        const detailsBtn = card.querySelector('.btn-outline');
        
        if (enrollBtn && enrollBtn.textContent.includes('Enroll')) {
            enrollBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const tournamentId = card.dataset.tournamentId;
                enrollInTournament(tournamentId);
            });
        }
        
        if (detailsBtn) {
            detailsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const tournamentId = card.dataset.tournamentId;
                showTournamentDetails(tournamentId);
            });
        }
    });
}

// Enrollment functionality
function enrollInTournament(tournamentId) {
    // Show loading state
    showLoadingToast('Processing enrollment...');
    
    // Simulate API call
    setTimeout(() => {
        showSuccessToast('Successfully enrolled in tournament!');
        
        // Update card to enrolled state
        const card = document.querySelector(`[data-tournament-id="${tournamentId}"]`);
        if (card) {
            updateCardToEnrolled(card);
        }
    }, 1500);
}

function updateCardToEnrolled(card) {
    // Update badge
    const badge = card.querySelector('.tournament-badge');
    badge.className = 'tournament-badge enrolled';
    badge.innerHTML = '<i class="fas fa-user-check"></i><span>Enrolled</span>';
    
    // Update footer
    const footer = card.querySelector('.tournament-footer');
    footer.innerHTML = `
        <div class="enrollment-status">
            <i class="fas fa-check-circle"></i>
            <span>Successfully Enrolled</span>
        </div>
        <button class="btn btn-outline">
            <i class="fas fa-info-circle"></i> View Details
        </button>
    `;
    
    // Re-attach event listeners
    const detailsBtn = footer.querySelector('.btn-outline');
    detailsBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const tournamentId = card.dataset.tournamentId;
        showTournamentDetails(tournamentId);
    });
    
    // Add success animation
    card.style.transform = 'scale(1.05)';
    setTimeout(() => {
        card.style.transform = 'scale(1)';
    }, 300);
}

// Modal functionality
function initializeModal() {
    const modal = document.getElementById('tournamentModal');
    const closeBtn = modal.querySelector('.modal-close');
    
    closeBtn.addEventListener('click', function() {
        closeModal();
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}

function showTournamentDetails(tournamentId) {
    const modal = document.getElementById('tournamentModal');
    const detailsContainer = document.getElementById('tournamentDetails');
    
    // Show loading state
    detailsContainer.innerHTML = '<div class="loading-spinner">Loading tournament details...</div>';
    modal.classList.add('active');
    
    // Simulate API call to get tournament details
    setTimeout(() => {
        const tournamentData = getTournamentData(tournamentId);
        renderTournamentDetails(tournamentData, detailsContainer);
    }, 800);
}

function getTournamentData(tournamentId) {
    // Tournament details - injected from server via PHP
    const tournaments = window.tournamentData?.details || {};
    return tournaments[tournamentId] || {};
}

function renderTournamentDetails(data, container) {
    container.innerHTML = `
        <div class="tournament-details-content">
            <div class="tournament-main-info">
                <h4>${data.title}</h4>
                <p class="tournament-desc">${data.description}</p>
                
                <div class="tournament-info-grid">
                    <div class="info-card">
                        <i class="fas fa-calendar"></i>
                        <div>
                            <strong>Date</strong>
                            <span>${data.date}</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Time</strong>
                            <span>${data.time}</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Location</strong>
                            <span>${data.location}</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Teams</strong>
                            <span>${data.teams} Teams</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-trophy"></i>
                        <div>
                            <strong>Prize</strong>
                            <span>${data.prize}</span>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-tag"></i>
                        <div>
                            <strong>Fee</strong>
                            <span>${data.fee}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            ${data.rules ? `
                <div class="tournament-rules">
                    <h5><i class="fas fa-list"></i> Tournament Rules</h5>
                    <ul>
                        ${data.rules.map(rule => `<li>${rule}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
            
            ${data.schedule ? `
                <div class="tournament-schedule">
                    <h5><i class="fas fa-calendar-alt"></i> Schedule</h5>
                    <div class="schedule-list">
                        ${data.schedule.map(item => `
                            <div class="schedule-item">
                                <span class="schedule-time">${item.time}</span>
                                <span class="schedule-event">${item.event}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            <div class="tournament-actions">
                ${data.status === 'enrolled' ? `
                    <button class="btn btn-outline">
                        <i class="fas fa-download"></i> Download Details
                    </button>
                ` : `
                    <button class="btn btn-primary" onclick="enrollInTournament('${data.id}')">
                        <i class="fas fa-user-plus"></i> Enroll Now
                    </button>
                `}
            </div>
        </div>
    `;
}

function closeModal() {
    const modal = document.getElementById('tournamentModal');
    modal.classList.remove('active');
}

// Toast notifications
function showLoadingToast(message) {
    showToast(message, 'loading');
}

function showSuccessToast(message) {
    showToast(message, 'success');
}

function showErrorToast(message) {
    showToast(message, 'error');
}

function showToast(message, type) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'loading' ? 'spinner fa-spin' : type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Auto remove (except loading)
    if (type !== 'loading') {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
}

// Add toast styles dynamically
const toastStyles = `
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        z-index: 10001;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    }
    
    .toast.show {
        transform: translateX(0);
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
        font-weight: 600;
    }
    
    .toast-success {
        border-left: 4px solid #4ade80;
    }
    
    .toast-error {
        border-left: 4px solid #f87171;
    }
    
    .toast-loading {
        border-left: 4px solid #60a5fa;
    }
`;

// Add styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = toastStyles;
document.head.appendChild(styleSheet);