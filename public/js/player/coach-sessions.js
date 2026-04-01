/**
 * Coach Sessions - Filter Logic, Date Validation & Coach Selection
 */
(function hydrateCoachSessionData() {
    if (window.coachSessionData) return;
    const el = document.getElementById('coachSessionData');
    if (!el) return;
    try {
        window.coachSessionData = JSON.parse(el.textContent || '{}');
    } catch (_err) {
        window.coachSessionData = {};
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    initializeDateValidation();
    initializeFilters();
    initializeCoachCards();
    updateSessionCounts();
});

// =========================================================================
// DATE VALIDATION - Prevent past dates
// =========================================================================
function initializeDateValidation() {
    const dateFilter = document.getElementById('date-filter');
    if (dateFilter) {
        // Set min attribute to today
        const today = new Date().toISOString().split('T')[0];
        dateFilter.setAttribute('min', today);

        // Prevent manual entry of past dates
        dateFilter.addEventListener('change', function() {
            if (this.value && this.value < today) {
                this.value = today;
                showNotification('Cannot select a past date. Date has been reset to today.', 'warning');
            }
        });

        // Block keyboard entry of past dates
        dateFilter.addEventListener('keydown', function(e) {
            // Allow tab, backspace, delete
            if (e.key === 'Tab' || e.key === 'Backspace' || e.key === 'Delete') return;
        });
    }
}

// =========================================================================
// FILTER LOGIC
// =========================================================================
function initializeFilters() {
    // Initial filter application
    applyCoachFilters();
}

window.applyCoachFilters = function() {
    const coachFilter = document.getElementById('coach-filter')?.value || '';
    const dateFilter = document.getElementById('date-filter')?.value || '';
    const sessionTypeFilter = document.getElementById('session-type-filter')?.value || '';
    const specializationFilter = document.getElementById('specialization-filter')?.value || '';

    // Filter coach cards
    filterCoachCards(coachFilter, specializationFilter);

    // Filter available session slots
    filterSessionSlots(coachFilter, dateFilter, sessionTypeFilter, specializationFilter);

    // Update counts
    updateSessionCounts();
};

function filterCoachCards(coachFilter, specializationFilter) {
    const coachCards = document.querySelectorAll('.coach-card');
    let visibleCount = 0;

    coachCards.forEach(card => {
        const coachId = card.getAttribute('data-coach-id');
        const specialization = card.getAttribute('data-specialization')?.toLowerCase() || '';

        let show = true;

        // Filter by coach
        if (coachFilter && coachId !== coachFilter) {
            show = false;
        }

        // Filter by specialization
        if (specializationFilter && specialization !== specializationFilter.toLowerCase()) {
            show = false;
        }

        card.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    // Show/hide no coaches message
    const noCoachesMsg = document.querySelector('.no-coaches-message');
    if (noCoachesMsg) {
        noCoachesMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function filterSessionSlots(coachFilter, dateFilter, sessionTypeFilter, specializationFilter) {
    const sessionCards = document.querySelectorAll('.session-slot-card');
    let visibleCount = 0;

    sessionCards.forEach(card => {
        const cardCoachId = card.getAttribute('data-coach-id') || '';
        const cardDate = card.getAttribute('data-date') || '';
        const cardType = card.getAttribute('data-session-type') || '';
        const cardSpec = card.getAttribute('data-specialization') || '';

        let show = true;

        if (coachFilter && cardCoachId !== coachFilter) show = false;
        if (dateFilter && cardDate !== dateFilter) show = false;
        if (sessionTypeFilter && cardType !== sessionTypeFilter) show = false;
        if (specializationFilter && cardSpec.toLowerCase() !== specializationFilter.toLowerCase()) show = false;

        card.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    // Show/hide no sessions message
    const noSessions = document.getElementById('no-coach-sessions');
    const sessionsContainer = document.getElementById('coach-sessions-container');

    if (noSessions) {
        noSessions.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    if (sessionsContainer) {
        sessionsContainer.style.display = visibleCount > 0 ? '' : 'none';
    }

    // Update filtered count
    const filteredEl = document.getElementById('filtered-coach-sessions');
    if (filteredEl) filteredEl.textContent = visibleCount;
}

function updateSessionCounts() {
    const totalSlots = document.querySelectorAll('.session-slot-card').length;
    const visibleSlots = document.querySelectorAll('.session-slot-card:not([style*="display: none"])').length;

    const totalEl = document.getElementById('total-coach-sessions');
    const filteredEl = document.getElementById('filtered-coach-sessions');

    if (totalEl) totalEl.textContent = totalSlots;
    if (filteredEl) filteredEl.textContent = visibleSlots;
}

window.clearCoachFilters = function() {
    const coachFilter = document.getElementById('coach-filter');
    const dateFilter = document.getElementById('date-filter');
    const sessionTypeFilter = document.getElementById('session-type-filter');
    const specializationFilter = document.getElementById('specialization-filter');

    if (coachFilter) coachFilter.value = '';
    if (dateFilter) dateFilter.value = '';
    if (sessionTypeFilter) sessionTypeFilter.value = '';
    if (specializationFilter) specializationFilter.value = '';

    applyCoachFilters();
    showNotification('Filters cleared', 'success');
};

// =========================================================================
// COACH CARD INTERACTIONS
// =========================================================================
function initializeCoachCards() {
    const cards = document.querySelectorAll('.coach-card');
    cards.forEach((card, index) => {
        // Animate cards on load
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

window.selectCoach = function(coachId) {
    // Set the coach filter dropdown to this coach
    const coachFilter = document.getElementById('coach-filter');
    if (coachFilter) {
        coachFilter.value = coachId;
        applyCoachFilters();
    }

    // Scroll to sessions section
    const sessionsSection = document.querySelector('.available-coach-sessions');
    if (sessionsSection) {
        sessionsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    showNotification('Showing sessions for selected coach', 'info');
};

// =========================================================================
// MY COACH BOOKINGS
// =========================================================================
window.showMyCoachBookings = function() {
    showNotification('Loading your coach bookings...', 'info');
    window.location.href = (window.coachSessionData?.urlRoot || '') + '/player/bookings';
};

// =========================================================================
// BOOK SESSION
// =========================================================================
window.bookSession = function(slotId) {
    if (!confirm('Are you sure you want to book this session?')) return;

    const urlRoot = window.coachSessionData?.urlRoot || '';
    
    fetch(urlRoot + '/player/coachbooking', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=book_session&slot_id=' + slotId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Session booked successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message || 'Failed to book session', 'error');
        }
    })
    .catch(() => {
        showNotification('An error occurred. Please try again.', 'error');
    });
};

// =========================================================================
// NOTIFICATION HELPER
// =========================================================================
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'coach-notification';

    const colors = {
        success: { bg: 'linear-gradient(135deg, #27ae60, #2ecc71)', shadow: 'rgba(46, 204, 113, 0.3)' },
        warning: { bg: 'linear-gradient(135deg, #f39c12, #e67e22)', shadow: 'rgba(243, 156, 18, 0.3)' },
        info: { bg: 'linear-gradient(135deg, #3498db, #2980b9)', shadow: 'rgba(52, 152, 219, 0.3)' },
        error: { bg: 'linear-gradient(135deg, #e74c3c, #c0392b)', shadow: 'rgba(231, 76, 60, 0.3)' }
    };

    const color = colors[type] || colors.info;
    const icons = { success: 'check-circle', warning: 'exclamation-triangle', info: 'info-circle', error: 'times-circle' };

    notification.innerHTML = `<i class="fas fa-${icons[type] || 'info-circle'}"></i> ${message}`;
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background: ${color.bg}; color: white;
        padding: 14px 20px; border-radius: 10px;
        box-shadow: 0 8px 25px ${color.shadow};
        z-index: 10001; transform: translateX(400px);
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        max-width: 380px; font-size: 14px;
        display: flex; align-items: center; gap: 10px;
    `;

    document.body.appendChild(notification);
    setTimeout(() => { notification.style.transform = 'translateX(0)'; }, 100);
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(notification)) document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
