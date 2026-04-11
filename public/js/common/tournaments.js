// Common Tournaments & Events JavaScript - Universal for All Dashboards
// This file provides unified functionality for tournaments and events pages 
// across admin, player, coach, and trainer dashboards

document.addEventListener('DOMContentLoaded', function() {
    console.log('Common Tournaments/Events page loaded');
    
    // Initialize all tournament functionality
    initializeTournaments();
    initializeEventHandlers();
    initializeCalendar();
    initializeFilters();
    initializeModal();
    initializeStats();
});

// Main initialization function
function initializeTournaments() {
    console.log('Initializing tournaments functionality...');
    
    // Add loading animation to cards
    const cards = document.querySelectorAll('.tournament-card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Event handlers for interactive elements
function initializeEventHandlers() {
    // Tournament card hover effects
    const tournamentCards = document.querySelectorAll('.tournament-card');
    tournamentCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Stat card animations
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Button click effects
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

// Calendar integration functionality
function initializeCalendar() {
    const calendarContainer = document.getElementById('calendar');
    if (!calendarContainer) return;
    
    console.log('Initializing calendar...');
    
    // Check if FullCalendar is available
    if (typeof FullCalendar !== 'undefined') {
        const calendar = new FullCalendar.Calendar(calendarContainer, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            themeSystem: 'bootstrap5',
            height: 'auto',
            events: function(fetchInfo, successCallback, failureCallback) {
                // Fetch tournament/event data
                fetchTournamentEvents(fetchInfo, successCallback, failureCallback);
            },
            eventClick: function(info) {
                showEventModal(info.event);
            },
            dateClick: function(info) {
                handleDateClick(info);
            }
        });
        
        calendar.render();
        window.tournamentCalendar = calendar;
    } else {
        console.log('FullCalendar not available, loading fallback calendar');
        createFallbackCalendar();
    }
}

// Filter functionality for tournaments
function initializeFilters() {
    const filterTabs = document.querySelectorAll('.filter-tab, .filter-btn');
    const tournamentCards = document.querySelectorAll('.tournament-card');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter || this.getAttribute('data-filter');
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter tournaments with animation
            filterTournaments(filter, tournamentCards);
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.tournament-search, #tournament-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterTournamentsBySearch(searchTerm, tournamentCards);
        });
    }
}

// Tournament filtering logic
function filterTournaments(filter, cards) {
    cards.forEach((card, index) => {
        const cardType = getCardType(card);
        const shouldShow = filter === 'all' || cardType === filter;
        
        if (shouldShow) {
            showCard(card, index);
        } else {
            hideCard(card);
        }
    });
}

function filterTournamentsBySearch(searchTerm, cards) {
    cards.forEach((card, index) => {
        const cardText = card.textContent.toLowerCase();
        const shouldShow = cardText.includes(searchTerm);
        
        if (shouldShow) {
            showCard(card, index);
        } else {
            hideCard(card);
        }
    });
}

function getCardType(card) {
    if (card.classList.contains('upcoming') || card.querySelector('.status-badge.upcoming')) return 'upcoming';
    if (card.classList.contains('enrolled') || card.querySelector('.status-badge.ongoing')) return 'enrolled';
    if (card.classList.contains('completed') || card.querySelector('.status-badge.completed')) return 'completed';
    return 'all';
}

function showCard(card, index = 0) {
    card.style.display = 'block';
    setTimeout(() => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0) scale(1)';
    }, index * 50);
}

function hideCard(card) {
    card.style.opacity = '0';
    card.style.transform = 'translateY(-20px) scale(0.95)';
    setTimeout(() => {
        card.style.display = 'none';
    }, 300);
}

// Modal functionality for tournament details
function initializeModal() {
    // Create modal if it doesn't exist
    if (!document.getElementById('tournamentModal')) {
        createTournamentModal();
    }
    
    // Add click handlers for view details buttons
    const viewButtons = document.querySelectorAll('.btn-view-details, .view-details');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tournamentId = this.dataset.tournamentId || this.closest('.tournament-card').dataset.tournamentId;
            showTournamentDetails(tournamentId);
        });
    });
    
    // Modal close handlers
    const modal = document.getElementById('tournamentModal');
    if (modal) {
        const closeButtons = modal.querySelectorAll('.modal-close, .close');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', closeTournamentModal);
        });
        
        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTournamentModal();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display !== 'none') {
                closeTournamentModal();
            }
        });
    }
}

// Stats animation and interaction
function initializeStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    // Animate numbers on load
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        animateCounter(stat, 0, finalValue, 1500);
    });
    
    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.stat-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.stat-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
}

// Utility functions
function animateCounter(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = Math.floor(current);
        
        if (current >= end) {
            clearInterval(timer);
            element.textContent = end;
        }
    }, 16);
}

function createTournamentModal() {
    const modalHTML = `
        <div id="tournamentModal" class="tournament-modal" style="display: none;">
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Tournament Details</h2>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="modalContent">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading tournament details...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close">Close</button>
                    <button type="button" class="btn btn-primary" id="modalActionBtn">Take Action</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add modal styles
    const modalStyles = `
        <style>
        .tournament-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #718096;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: #f7fafc;
            color: #2d3748;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-footer {
            padding: 1rem 2rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
        
        .loading-spinner {
            text-align: center;
            padding: 2rem;
            color: #718096;
        }
        
        .loading-spinner i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        </style>
    `;
    
    document.head.insertAdjacentHTML('beforeend', modalStyles);
}

function showTournamentDetails(tournamentId) {
    const modal = document.getElementById('tournamentModal');
    const modalContent = document.getElementById('modalContent');
    
    if (!modal) return;
    
    modal.style.display = 'flex';
    modal.style.opacity = '0';
    
    // Animate modal in
    setTimeout(() => {
        modal.style.transition = 'opacity 0.3s ease';
        modal.style.opacity = '1';
    }, 10);
    
    // Load tournament data (this would typically be an AJAX call)
    loadTournamentData(tournamentId, modalContent);
}

function closeTournamentModal() {
    const modal = document.getElementById('tournamentModal');
    if (!modal) return;
    
    modal.style.transition = 'opacity 0.3s ease';
    modal.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function loadTournamentData(tournamentId, container) {
    // Tournament data - should be fetched from server; uses window.tournamentData as fallback
    const tournaments = window.tournamentData?.events || {};
    const data = tournaments[tournamentId] || {};
    
    setTimeout(() => {
        if (Object.keys(data).length > 0) {
            container.innerHTML = `
                <div class="tournament-details">
                    <h3>${data.title || 'Tournament #' + tournamentId}</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <strong>Date:</strong> ${data.date || 'TBD'}
                        </div>
                        <div class="detail-item">
                            <strong>Location:</strong> ${data.location || 'TBD'}
                        </div>
                        <div class="detail-item">
                            <strong>Teams:</strong> ${data.teams || 'TBD'}
                        </div>
                        <div class="detail-item">
                            <strong>Prize Pool:</strong> ${data.prize || 'TBD'}
                        </div>
                    </div>
                    <div class="tournament-description">
                        <h4>About This Tournament</h4>
                        <p>${data.description || 'Details coming soon.'}</p>
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="tournament-details">
                    <h3>Tournament #${tournamentId}</h3>
                    <p>Tournament details are not available at this time.</p>
                </div>
            `;
        }
    }, 500);
}

// Calendar helper functions
function fetchTournamentEvents(fetchInfo, successCallback, failureCallback) {
    // Tournament events - injected from server via PHP
    const events = window.tournamentData?.calendarEvents || [];
    successCallback(events);
}

function showEventModal(event) {
    console.log('Event clicked:', event.title);
    // Implement event details modal
    showTournamentDetails(event.id);
}

function handleDateClick(info) {
    console.log('Date clicked:', info.dateStr);
    // Implement date click functionality (e.g., create new event)
}

function createFallbackCalendar() {
    const calendarContainer = document.getElementById('calendar');
    if (!calendarContainer) return;
    
    // Upcoming events - injected from server via PHP
    const upcomingEvents = window.tournamentData?.calendarEvents || [];
    let eventsHTML = '';
    if (upcomingEvents.length > 0) {
        eventsHTML = '<ul>' + upcomingEvents.map(e => `<li>${e.title} - ${e.start || ''}</li>`).join('') + '</ul>';
    } else {
        eventsHTML = '<p>No upcoming events at this time.</p>';
    }
    
    calendarContainer.innerHTML = `
        <div class="fallback-calendar">
            <h3>Tournament Calendar</h3>
            <p>Calendar functionality is loading...</p>
            <div class="upcoming-events">
                <h4>Upcoming Events</h4>
                ${eventsHTML}
            </div>
        </div>
    `;
}

// Export functions for external use
window.TournamentUtils = {
    filterTournaments,
    showTournamentDetails,
    closeTournamentModal,
    refreshCalendar: function() {
        if (window.tournamentCalendar) {
            window.tournamentCalendar.refetchEvents();
        }
    }
};

// Add CSS animations
const animationStyles = `
<style>
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.tournament-card, .stat-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', animationStyles);