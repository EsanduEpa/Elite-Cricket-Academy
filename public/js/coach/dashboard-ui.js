window.filterSessions = function filterSessions() {
    const filterElement = document.getElementById('sessionFilter');
    const filter = filterElement ? filterElement.value : 'all';
    const bookingItems = document.querySelectorAll('.booking-item');

    bookingItems.forEach(function(item) {
        if (filter === 'all') {
            item.style.display = '';
        } else if (filter === 'private' && item.classList.contains('private-session')) {
            item.style.display = '';
        } else if (filter === 'normal' && item.classList.contains('normal-session')) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.__COACH_BASE_URL || '';

    document.querySelectorAll('[data-action="schedule-session"]').forEach(function(button) {
        button.addEventListener('click', function() {
            window.location.href = baseUrl + '/staffslots/calendar';
        });
    });

    document.querySelectorAll('[data-action="view-players"]').forEach(function(button) {
        button.addEventListener('click', function() {
            window.location.href = baseUrl + '/coach/players';
        });
    });

    document.querySelectorAll('[data-action="add-recommendation"]').forEach(function(button) {
        button.addEventListener('click', function() {
            window.location.href = baseUrl + '/coach/tournament-recommendations';
        });
    });

    document.querySelectorAll('[data-action="check-medical"]').forEach(function(button) {
        button.addEventListener('click', function() {
            window.location.href = baseUrl + '/coach/health';
        });
    });
});